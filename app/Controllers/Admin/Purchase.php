<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Libraries\Mycart;
use App\Libraries\TransactionLog;
use CodeIgniter\HTTP\RedirectResponse;


class Purchase extends BaseController
{

    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    protected $cart;
    private $module_name = 'Purchase';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
        $this->cart = new Mycart();
        $this->transactionLog = new TransactionLog();
    }

    /**
     * @description This method provides purchase view
     * @return RedirectResponse|void
     */
    public function index()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;

            $supplier_id = $this->request->getGet('supplier_id');
            $st_date = $this->request->getGet('st_date');
            $en_date = $this->request->getGet('en_date');

            //purchase table null value delete (start)
            $purcTable = DB()->table('purchase');
            $purchId = $purcTable->where('sch_id', $shopId)->where('due', NULL)->get()->getResult();
            foreach ($purchId as $value) {
                // purchasa itame fiend count (start)
                $purchase_itemTable = DB()->table('purchase_item');
                $purItem = $purchase_itemTable->where('purchase_id', $value->purchase_id)->countAllResults();
                // purchasa itame fiend count (end)

                //deleted Nul value in purchase (start)
                if ($purItem < 1) {
                    $purcDelTable = DB()->table('purchase');
                    $purcDelTable->where('purchase_id', $value->purchase_id)->delete();
                }
                //deleted Nul value in purchase (end)
            }
            //purchase table null value delete (end)

            $data['purchase_data'] = $this->purchaseQuery($supplier_id, $st_date, $en_date, $shopId);

            $data['supplier_id'] = isset($supplier_id) ? $supplier_id : '';
            $data['st_date'] = isset($st_date) ? $st_date : '';
            $data['en_date'] = isset($en_date) ? $en_date : '';
            $data['id'] = $shopId;
            $data['shopId'] = $shopId;

            $data['menu'] = view('Admin/menu_purchase', $data);
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Purchase/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method search purchase date to date
     * @return void
     */
    public function search_dateTodate()
    {
        $shopId = $this->session->shopId;

        $supplier_id = $this->request->getPost('supplier_id') ?? $this->request->getGet('supplier_id');
        $st_date = $this->request->getPost('st_date') ?? $this->request->getGet('st_date');
        $en_date = $this->request->getPost('en_date') ?? $this->request->getGet('en_date');

        $query = $this->purchaseQuery($supplier_id, $st_date, $en_date, $shopId);

        print $this->purchaseBox($query, $supplier_id, $st_date, $en_date, $shopId);
    }

    /**
     * @description This method search purchase print date to date
     * @return void
     */
    public function search_dateTodatePrint()
    {
        $shopId = $this->session->shopId;

        $supplier_id = $this->request->getPost('supplier_id') ?? $this->request->getGet('supplier_id');
        $st_date = $this->request->getPost('st_date') ?? $this->request->getGet('st_date');
        $en_date = $this->request->getPost('en_date') ?? $this->request->getGet('en_date');

        $query = $this->purchaseQuery($supplier_id, $st_date, $en_date, $shopId);

        $view = '<div class="col-xs-12" style="text-transform: capitalize;" >'
            . view('Admin/Purchase/print_table', [
                'purchase_data' => $query,
                'supplier_id' => $supplier_id,
                'st_date' => $st_date,
                'en_date' => $en_date,
                'id' => $shopId,
                'shopId' => $shopId
            ])
            . '</div>';

        print $view;
    }

    /**
     * @description This method builds the purchase query
     * @param string|int $supplier_id
     * @param string $st_date
     * @param string $en_date
     * @param int|null $shopId
     * @return array
     */
    private function purchaseQuery($supplier_id = '', $st_date = '', $en_date = '', $shopId = null)
    {
        $shopId = $shopId ?? $this->session->shopId;
        $table = DB()->table('purchase');
        $table->where('sch_id', $shopId);
        $table->where('deleted IS NULL');

        if (!empty($supplier_id)) {
            $table->where('supplier_id', $supplier_id);
        }
        if (!empty($st_date)) {
            $table->where('date >=', $st_date . ' 00:00:00');
        }
        if (!empty($en_date)) {
            $table->where('date <=', $en_date . ' 23:59:59');
        }
        $table->orderBy('purchase_id', 'ASC');

        return $table->get()->getResult();
    }

    /**
     * @description This method builds the purchase table box and embedded print container
     * @param array $purchase_data
     * @param string|int $supplier_id
     * @param string $st_date
     * @param string $en_date
     * @param int|null $shopId
     * @return string
     */
    private function purchaseBox($purchase_data, $supplier_id = '', $st_date = '', $en_date = '', $shopId = null)
    {
        $shopId = $shopId ?? $this->session->shopId;
        $transaction_flow = $this->permission->have_access($this->session->role, $this->module_name, 'transaction_flow');
        $read = $this->permission->have_access($this->session->role, $this->module_name, 'read');
        $update = $this->permission->have_access($this->session->role, $this->module_name, 'update');

        $totalAmount = 0;
        $totalDue = 0;

        $view = '<div class="box">
                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped text-capitalize">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Purchase Date</th>
                                <th>Supplier</th>
                                <th>Total Amount</th>
                                <th>Due</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>';

        $i = 1;
        foreach ($purchase_data as $purchase) {
            $pAmount = !empty($purchase->amount) ? $purchase->amount : get_data_by_id('amount', 'purchase', 'purchase_id', $purchase->purchase_id);
            $pDue = !empty($purchase->due) ? $purchase->due : get_data_by_id('due', 'purchase', 'purchase_id', $purchase->purchase_id);
            $totalAmount += (float)$pAmount;
            $totalDue += (float)$pDue;

            $actionBtns = '';
            if (isset($transaction_flow) && $transaction_flow == 1) {
                $actionBtns .= '<a href="javascript:void(0)" onclick="showData(\'' . site_url('/Admin/Purchase_ajax/transaction_flow/' . $purchase->purchase_id) . '\',\'/Admin/Purchase/transaction_flow/' . $purchase->purchase_id . '\')" class="btn btn-success btn-xs">Transaction Flow</a> ';
            }
            if (isset($read) && $read == 1) {
                $actionBtns .= '<a href="javascript:void(0)" onclick="showData(\'' . site_url('/Admin/Purchase_ajax/view/' . $purchase->purchase_id) . '\',\'/Admin/Purchase/view/' . $purchase->purchase_id . '\')" class="btn btn-primary btn-xs">View</a> ';
            }
            if (isset($update) && $update == 1) {
                $actionBtns .= '<a href="javascript:void(0)" class="btn btn-xs btn-warning" onclick="purchaseEdit(\'' . $purchase->purchase_id . '\')" data-toggle="modal" data-target="#modal-default">Edit</a>';
            }

            $view .= '<tr>
                        <td>' . $i++ . '</td>
                        <td>' . invoiceDateFormat($purchase->date) . '</td>
                        <td>' . get_data_by_id('name', 'suppliers', 'supplier_id', $purchase->supplier_id) . '</td>
                        <td>' . showWithCurrencySymbol($pAmount) . '</td>
                        <td>' . showWithCurrencySymbol($pDue) . '</td>
                        <td>' . $actionBtns . '</td>
                    </tr>';
        }

        $view .= '</tbody>
                    <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Purchase Date</th>
                        <th>Supplier</th>
                        <th>Total Amount</th>
                        <th>Due</th>
                        <th>Action</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>Total</th>
                        <th>= ' . showWithCurrencySymbol($totalAmount) . '</th>
                        <th>= ' . showWithCurrencySymbol($totalDue) . '</th>
                        <th></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>';

        // printable version of the same (filtered) rows
        $view .= '<div id="ajaxPurchasePrint" style="display: none;">'
            . view('Admin/Purchase/print_table', [
                'purchase_data' => $purchase_data,
                'supplier_id' => $supplier_id,
                'st_date' => $st_date,
                'en_date' => $en_date,
                'id' => $shopId,
                'shopId' => $shopId
            ])
            . '</div>';

        return $view;
    }

    /**
     * @description This method provides purchase create view
     * @return RedirectResponse|void
     */
    public function create()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $data['action'] = base_url('Admin/Purchase/create_action');


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['create'] == 1) {
                echo view('Admin/Purchase/create', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store purchase
     * @return RedirectResponse
     */
    public function create_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['type_id'] = $this->request->getPost('type_id');
        $data['supplier_id'] = $this->request->getPost('supplier_id');

        $this->validation->setRules([
            'type_id' => ['label' => 'type_id', 'rules' => 'required'],
            'supplier_id' => ['label' => 'supplier_id', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Purchase/create'));
        } else {

            $shopCheck = check_shop('suppliers', 'supplier_id', $data['supplier_id']);
            if ($shopCheck == 1) {

                $datapur = array(
                    'sch_id' => $shopId,
                    'supplier_id' => $data['supplier_id'],
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $purchaseTable = DB()->table('purchase');
                $purchaseTable->insert($datapur);
                $purchaseId = DB()->insertID();

                $purchaseData = array(
                    'supplierId' => $data['supplier_id'],
                    'purchaseId' => $purchaseId,
                );
                $this->session->set($purchaseData);

                if ($data['type_id'] == 1) {
                    return redirect()->to(site_url('Admin/Purchase/new_product'));
                } else {
                    return redirect()->to(site_url('Admin/Purchase/existing_product'));
                }

            } else {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">please select a valid supplier<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Purchase/create'));
            }

        }
    }

    /**
     * @description This method new product
     * @return RedirectResponse|void
     */
    public function new_product()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;

            $data['action'] = base_url('Admin/Purchase/product_create_action');
            if (empty($this->session->purchaseId)) {
                return redirect()->to(site_url('Admin/Purchase/create'));
            }
            if (!empty($this->session->cartType) && $this->session->cartType !== 'purchase') {
                $this->cart->destroy();
            }
            $table = DB()->table('suppliers');
            $data['supplier'] = $table->where('supplier_id',$this->session->supplierId)->get()->getRow();

            $table = DB()->table('unit_set');
            $data['unit_set'] = $table->where('sch_id',$shopId)->get()->getResult();


            $data['supplierId'] = $this->session->supplierId;
            $data['purchaseId'] = $this->session->purchaseId;


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if ($data['create'] == 1) {
                echo view('Admin/Purchase/new_product', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method existing product
     * @return RedirectResponse|void
     */
    public function existing_product()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $data['action'] = base_url('Admin/Purchase/existing_create_action');
            if (empty($this->session->purchaseId)) {
                return redirect()->to(site_url('Admin/Purchase/create'));
            }
            if (!empty($this->session->cartType)) {
                if ($this->session->cartType == 'sale') {
                    $this->cart->destroy();
                }
            }

            $data['supplierId'] = $this->session->supplierId;
            $data['purchaseId'] = $this->session->purchaseId;

            $proTable = DB()->table('products');
            $data['product'] = $proTable->where('supplier_id', $data['supplierId'])->get()->getResult();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if ($data['create'] == 1) {
                echo view('Admin/Purchase/existing_product', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }


    }

    /**
     * @description This method store purchase
     * @return RedirectResponse
     */
    public function existing_create_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $purchaseId = $this->request->getPost('purchase_id');
        $supplierId = $this->request->getPost('supplier_id');
        $sms = $this->request->getPost('sms');

        $returnchecked = $this->request->getPost('returnchecked[]');
        $products = $this->request->getPost('prod_id[]');
        $productQuantity = $this->request->getPost('quantity[]');
        $productPrice = $this->request->getPost('purchase_price[]');
        //payment data
        $totalPrice = str_replace(',', '', $this->request->getPost('totalPrice'));
        $cashAmount = str_replace(',', '', $this->request->getPost('cash'));
        $bankAmount = str_replace(',', '', $this->request->getPost('bank'));
        $dueAmount = str_replace(',', '', $this->request->getPost('due'));
        $bankId = $this->request->getPost('bank_id');

        DB()->transStart();
        if ($totalPrice > 0) {
            //purshase total price calculet to suppliers balance and update suppliers balance or create suppliers ledger (start)
            $supplierCash = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
            $newCash = $supplierCash - $totalPrice;

            $suppData = array(
                'balance' => $newCash,
                'updatedBy' => $userId,
            );
            $tabsuppliers = DB()->table('suppliers');
            $tabsuppliers->where('supplier_id', $supplierId)->update($suppData);

            //create suppliers ledger
            $lgSuplData = array(
                'sch_id' => $shopId,
                'supplier_id' => $supplierId,
                'purchase_id' => $purchaseId,
                'particulars' => 'Purchase Cash Due',
                'trangaction_type' => 'Cr.',
                'amount' => $totalPrice,
                'rest_balance' => $newCash,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $tabledger_suppliers = DB()->table('ledger_suppliers');
            $tabledger_suppliers->insert($lgSuplData);
            $ledg_sup_id = DB()->insertID();
            //purshase total price calculet to suppliers balance and update suppliers balance or create suppliers ledger (end)

            //Insert Purchase Transaction Entries
            $this->purchase_transaction_entries($purchaseId, $ledg_sup_id, 'ledger_suppliers', 'Cr.');

            // purchase balance update and ledger create (start)
            $purchaseBal = get_data_by_id('purchase_balance', 'shops', 'sch_id', $shopId);
            $restBalPurc = $purchaseBal + $totalPrice;


            $purUpdata = array('purchase_balance' => $restBalPurc);
            $tabshops = DB()->table('shops');
            $tabshops->where('sch_id', $shopId)->update($purUpdata);


            $purLedgData = array(
                'sch_id' => $shopId,
                'purchase_id' => $purchaseId,
                'trangaction_type' => 'Dr.',
                'particulars' => 'New purchase amount',
                'amount' => $totalPrice,
                'rest_balance' => $restBalPurc,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $tabledger_purchase = DB()->table('ledger_purchase');
            $tabledger_purchase->insert($purLedgData);
            $ledg_pur_id = DB()->insertID();
            // purchase balance update and ledger create (end)

            //Insert Purchase Transaction Entries
            $this->purchase_transaction_entries($purchaseId, $ledg_pur_id, 'ledger_purchase', 'Dr.');

            // stock balance update and ledger create (start)
            $stockBal = get_data_by_id('stockAmount', 'shops', 'sch_id', $shopId);
            $restBalStock = $stockBal + $totalPrice;


            $stockUpdata = array('stockAmount' => $restBalStock);
            $tabshopsSt = DB()->table('shops');
            $tabshopsSt->where('sch_id', $shopId)->update($stockUpdata);


            $stockLedgData = array(
                'sch_id' => $shopId,
                'purchase_id' => $purchaseId,
                'trangaction_type' => 'Dr.',
                'particulars' => 'New purchase amount',
                'amount' => $totalPrice,
                'rest_balance' => $restBalStock,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $tabledger_stock = DB()->table('ledger_stock');
            $tabledger_stock->insert($stockLedgData);
            $ledg_stock_id = DB()->insertID();
            // stock balance update and ledger create (end)

            //Insert Purchase Transaction Entries
            $this->purchase_transaction_entries($purchaseId, $ledg_stock_id, 'ledger_stock', 'Dr.');


            if ($cashAmount > 0) {

                //purshase pay cash amount calculet to shops cash and update shops cash or create ledger_nagodan statment in ledger_nagodan table (start)
                $shopsCash = get_data_by_id('cash', 'shops', 'sch_id', $shopId);

                if ($shopsCash >= $cashAmount) {
                    $upCahs = $shopsCash - $cashAmount;
                    $shopsData = array(
                        'cash' => $upCahs,
                        'updatedBy' => $userId,
                    );
                    $tabshops = DB()->table('shops');
                    $tabshops->where('sch_id', $shopId)->update($shopsData);

                    //nagodan ledger create
                    $lgNagData = array(
                        'sch_id' => $shopId,
                        'purchase_id' => $purchaseId,
                        'trangaction_type' => 'Cr.',
                        'particulars' => 'Purchase Cash Pay',
                        'amount' => $cashAmount,
                        'rest_balance' => $upCahs,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $tabledger_nagodan = DB()->table('ledger_nagodan');
                    $tabledger_nagodan->insert($lgNagData);
                    $ledg_nagod_id = DB()->insertID();
                    //purshase pay cash amount calculet to shops cash and update shops cash or create ledger_nagodan statment in ledger_nagodan table (end)

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledg_nagod_id, 'ledger_nagodan', 'Cr.');

                    //purshase pay cash amount calculet to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (start)
                    $supplierccCash = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
                    $suppCash = $supplierccCash + $cashAmount;

                    $cashsuppData = array(
                        'balance' => $suppCash,
                        'updatedBy' => $userId,
                    );
                    $tabsuppliers = DB()->table('suppliers');
                    $tabsuppliers->where('supplier_id', $supplierId)->update($cashsuppData);

                    //suppliers ledger create
                    $lgSuplData = array(
                        'sch_id' => $shopId,
                        'supplier_id' => $supplierId,
                        'purchase_id' => $purchaseId,
                        'particulars' => 'Purchase Cash Pay',
                        'trangaction_type' => 'Dr.',
                        'amount' => $cashAmount,
                        'rest_balance' => $suppCash,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $tabledger_suppliers = DB()->table('ledger_suppliers');
                    $tabledger_suppliers->insert($lgSuplData);
                    $ledg_sup_id = DB()->insertID();

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledg_sup_id, 'ledger_suppliers', 'Dr.');
                }
                //purshase pay cash amount calculet to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (end)
            }

            if ($bankAmount > 0) {

                //purshase pay bank amountcalculet to bank balance and update bank balance or create bank ledger in ledger_bank table (start)
                $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $bankId);

                if ($bankCash >= $bankAmount) {

                    $upCahs = $bankCash - $bankAmount;
                    $bankData = array(
                        'balance' => $upCahs,
                        'updatedBy' => $userId,
                    );
                    $tabbank = DB()->table('bank');
                    $tabbank->where('bank_id', $bankId)->update($bankData);

                    //bank ledger create
                    $lgBankData = array(
                        'sch_id' => $shopId,
                        'bank_id' => $bankId,
                        'purchase_id' => $purchaseId,
                        'trangaction_type' => 'Cr.',
                        'particulars' => 'Purchase Bank Pay',
                        'amount' => $bankAmount,
                        'rest_balance' => $upCahs,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $tabledger_bank = DB()->table('ledger_bank');
                    $tabledger_bank->insert($lgBankData);
                    $ledg_bank_id = DB()->insertID();
                    //purshase pay bank amountcalculet to bank balance and update bank balance or create bank ledger in ledger_bank table (end)

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledg_bank_id, 'ledger_bank', 'Cr.');


                    //purshase pay bank amount calculet to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (start)
                    $supplierbbCash = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
                    $suppBaCash = $supplierbbCash + $bankAmount;

                    $banksuppData = array(
                        'balance' => $suppBaCash,
                        'updatedBy' => $userId,
                    );
                    $tabsuppliers = DB()->table('suppliers');
                    $tabsuppliers->where('supplier_id', $supplierId)->update($banksuppData);

                    //suppliers ledger create
                    $lgSuplData = array(
                        'sch_id' => $shopId,
                        'supplier_id' => $supplierId,
                        'purchase_id' => $purchaseId,
                        'particulars' => 'Purchase Bank Pay',
                        'trangaction_type' => 'Dr.',
                        'amount' => $bankAmount,
                        'rest_balance' => $suppBaCash,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $tabledger_suppliers = DB()->table('ledger_suppliers');
                    $tabledger_suppliers->insert($lgSuplData);
                    $ledg_sup_id = DB()->insertID();

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledg_sup_id, 'ledger_suppliers', 'Dr.');
                }
                //purshase pay bank amount calculet to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (end)
            }

            $storeTab = DB()->table('stores');
            $store = $storeTab->where('sch_id', $shopId)->where('is_default', 1)->get()->getRow();
            $storeId = $store->store_id;


            //purchase product insert in product table and purchase item table (start)
            foreach ($returnchecked as $value) {
                $k = array_flip($products);
                $key = $k[$value];

                $product_id = $value;
                $quantity = $productQuantity[$key];
                $price = $productPrice[$key];
                //Update each product quantity and price
                $data = array(
                    'purchase_price' => $price,
                );
                $tabProducts = DB()->table('products');
                $tabProducts->where('prod_id', $product_id)->update($data);

                $stockTable = DB()->table('product_stock_relation');
                $stock = $stockTable->where('store_id',$store->store_id)->where('product_id', $product_id)->get()->getRow();
                $newQunt = $stock->quantity + $quantity;
                $dataQty = array(
                    'quantity' => $newQunt,
                );
                $stockTableUpdate = DB()->table('product_stock_relation');
                $stockTableUpdate->where('store_id',$store->store_id)->where('product_id', $product_id)->update($dataQty);


                //insetr purchase Item in purchase item table
                $total_price = $price * $quantity;
                $purItemData = array(
                    'prod_id' => $product_id,
                    'purchase_id' => $purchaseId,
                    'quantity' => $quantity,
                    'purchase_price' => $price,
                    'total_price' => $total_price,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $tabpurchase_item = DB()->table('purchase_item');
                $tabpurchase_item->insert($purItemData);

            }
            //purchase product insert in product table and purchase item table (end)


            //purchase all pay amount detail Update in purchase table(start)
            $parsData = array(
                'amount' => $totalPrice,
                'nagad_paid' => $cashAmount,
                'bank_paid' => $bankAmount,
                'bank_id' => $bankId,
                'due' => $dueAmount,
                'updatedBy' => $userId,
            );
            $tabpurchase = DB()->table('purchase');
            $tabpurchase->where('purchase_id', $purchaseId)->update($parsData);
            //purchase all pay amount detail Update in purchase table(end)

            // transaction events insert;
            DB()->table('transaction_events')->insert([
                'sch_id' => $shopId,
                'purchase_id'      => $purchaseId,
                'createdDtm'       => date('Y-m-d H:i:s')
            ]);


            if (!empty($sms)) {
                $message = 'Thank you for your purchase.Your purchase amount is-' . $totalPrice;
                $phone = get_data_by_id('phone', 'suppliers', 'supplier_id', $supplierId);
                send_sms($phone, $message);
            }
        } else {
            $this->session->setFlashdata('message', '<div style="margin-top: 12px" class="alert alert-danger" id="message">Invalid Quantity</div>');
            redirect(site_url('purchase'));
        }
        DB()->transComplete();


        $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Create Record Success! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        return redirect()->to(site_url('Admin/Purchase'));
    }

    /**
     * @description This method check sub cat
     * @return void
     */
    public function check_sub_cat()
    {
        $shopId = $this->session->shopId;

        $Id = $this->request->getPost('ID');
        $product_categoryTable = DB()->table('product_category');
        $query = $product_categoryTable->where('parent_pro_cat', $Id)->where('sch_id', $shopId)->where("deleted IS NULL")->get()->getResult();
        $options = '';
        foreach ($query as $row) {

            $options .= '<option value="' . $row->prod_cat_id . '" ';
            $options .= '>' . $row->product_category . '</option>';
        }
        print $options;
    }

    /**
     * @description This method add cart
     * @return void
     */
    public function old_addCart()
    {

        $data['subCatId'] = $this->request->getPost('subCatId');
        $data['category'] = $this->request->getPost('category');
        $data['name'] = $this->request->getPost('name');
        $data['unit'] = $this->request->getPost('unit');
        $data['price'] = $this->request->getPost('price');
        $data['salePrice'] = $this->request->getPost('salePrice');
        $data['qty'] = $this->request->getPost('qty');

        $this->validation->setRules([
            'subCatId' => ['label' => 'subCatId', 'rules' => 'required'],
            'category' => ['label' => 'category', 'rules' => 'required'],
            'name' => ['label' => 'name', 'rules' => 'required'],
            'unit' => ['label' => 'unit', 'rules' => 'required'],
            'price' => ['label' => 'price', 'rules' => 'required'],
            'salePrice' => ['label' => 'salePrice', 'rules' => 'required'],
            'qty' => ['label' => 'qty', 'rules' => 'required|is_natural_no_zero'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            if ($data['qty'] > 0) {
                $i = count($this->cart->contents());
                $data2 = array(
                    'id' => ++$i,
                    'name' => $data['name'],
                    'unit' => $data['unit'],
                    'qty' => $data['qty'],
                    'price' => $data['price'],
                    'salePrice' => $data['salePrice'],
                    'cat_id' => $data['subCatId']
                );

                $this->cart->insert($data2);
                $this->session->set('cartType', 'purchase');

            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">Enter a valid quantity! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }

        }

    }
    public function addCart()
    {
        $categories_id = $this->request->getPost('categories_id');

        $data['subCatId'] = $this->request->getPost('sub_category');
        $data['category'] = $this->request->getPost('category');
        $data['name'] = $this->request->getPost('name');
        $data['price'] = $this->request->getPost('price');
        $data['salePrice'] = $this->request->getPost('selling_price');
        $data['sale_unit'] = $this->request->getPost('sale_unit');
        $data['unit'] = $categories_id;

        $purchase_units_price = $this->request->getPost('purchase_units_price');
        $sell_unit_price = $this->request->getPost('sell_unit_price');



        $this->validation->setRules([
            'subCatId' => ['label' => 'subCatId', 'rules' => 'required'],
            'category' => ['label' => 'category', 'rules' => 'required'],
            'name' => ['label' => 'name', 'rules' => 'required'],
            'unit' => ['label' => 'unit', 'rules' => 'required'],
            'price' => ['label' => 'price', 'rules' => 'required'],
            'salePrice' => ['label' => 'salePrice', 'rules' => 'required'],
            'sale_unit' => ['label' => 'Sale Unit', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $totalQty = 0;

            if (!empty($categories_id)) {
                $query = DB()->table('unit_set')->where('unit_set_id',$categories_id)->get()->getRow();
                $units_id = json_decode($query->purchase_units);

                $unit = array();
                $units = DB()->table('units')->whereIn('units_id', $units_id)->orderBy('conversion_factor', 'DESC')->get()->getResult();
                foreach ($units as $val) {
                    $nameUnit = strtolower(str_replace(' ', '_', $val->name));
                    $unit[$nameUnit] = $this->request->getPost($nameUnit);
                    if (!empty($unit[$nameUnit])) {
                        $totalQty += $val->conversion_factor * $unit[$nameUnit];
                    }
                }
            }

            //purchase price make
            $basePurchasePrice = 0;
            $unitsPur = DB()->table('units')->where('units_id', $purchase_units_price)->get()->getRow();
            if (!empty($unitsPur)){
                $basePurchasePrice = $data['price']/$unitsPur->conversion_factor;
            }
            $purchasePrice = $basePurchasePrice;

            //sale price make
            $baseSalePrice = 0;
            $unitsSale = DB()->table('units')->where('units_id', $sell_unit_price)->get()->getRow();
            if (!empty($unitsSale)){
                $baseSalePrice = $data['salePrice']/$unitsSale->conversion_factor;
            }
            $salePrice = $baseSalePrice;

            if ((!empty($totalQty)) && ($totalQty > 0)) {
                $i = count($this->cart->contents());
                $dataAdd = array(
                    'id' => ++$i,
                    'name' => $data['name'],
                    'unit' => $data['sale_unit'] ,
                    'qty' => $totalQty,
                    'price' => $purchasePrice,
                    'salePrice' => $salePrice,
                    'cat_id' => $data['subCatId'],
                    'unit_set_id' => $categories_id,
                );

                $this->cart->insert($dataAdd);
                $this->session->set('cartType', 'purchase');

            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">Enter a valid quantity! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }

        }

    }

    /**
     * @description This method remove cart
     * @return void
     */
    public function remove_cart()
    {
        $id = $this->request->getPost('id');
        $this->cart->remove($id);
    }

    /**
     * @description This method clear cart
     * @return void
     */
    public function clearCart()
    {
        $this->cart->destroy();
    }

    /**
     * @description This method check shop balance
     * @return int
     */
    public function check_shop_balance()
    {
        $shopId = $this->session->shopId;
        $cash = $this->request->getPost('cash');

        $shopsTable = DB()->table('shops');
        $query = $shopsTable->where('sch_id', $shopId)->get()->getRow();

        $balance = $query->cash;
        if ($cash > $balance) {
            return print '0';
        } else {
            return print '1';
        }
    }

    /**
     * @description This method check bank balance
     * @return int
     */
    public function check_bank_balance()
    {
        $amount = $this->request->getPost('balance');
        $bankId = $this->request->getPost('bank_id');

        $bankBalance = get_data_by_id('balance', 'bank', 'bank_id', $bankId);
        if ($amount > $bankBalance) {
            return print '0';
        } else {
            return print '1';
        }
    }

    /**
     * @description This method store products
     * @return RedirectResponse
     */
    public function old_product_create_action()
    {
        $userId = $this->session->userId;
        $shopId = $this->session->shopId;

        $date = $this->request->getPost('date');
        $purchaseId = $this->request->getPost('purchase_id');
        $supplierId = $this->request->getPost('supplier_id');
        $sms = $this->request->getPost('sms');

        $totalPrice = str_replace(',', '', $this->request->getPost('totalPrice'));
        $cashAmount = str_replace(',', '', $this->request->getPost('cash'));
        $bankAmount = str_replace(',', '', $this->request->getPost('bank'));
        $dueAmount = str_replace(',', '', $this->request->getPost('due'));
        $bankId = $this->request->getPost('bank_id');

        $name = $this->request->getPost('name[]');

        if (!empty($this->cart->contents())) {
            $number = count($name);

            $cashbal = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
            $bankbal = get_data_by_id('balance', 'bank', 'bank_id', $bankId);

            if ($cashbal < $cashAmount) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Not enough balance! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Purchase/new_product'));
            }

            if ($bankbal < $bankAmount) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Not enough balance! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Purchase/new_product'));
            }

            if (!empty($cashAmount) && $cashAmount < 0) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Purchase/new_product'));
            }

            if (!empty($bankAmount) && $bankAmount < 0) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Purchase/new_product'));
            }

            DB()->transStart();

            //purshase total price calculet to suppliers balance and update suppliers balance or create suppliers ledger (start)
            $supplierCash = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
            $newCash = $supplierCash - $totalPrice;

            $suppData = array(
                'balance' => $newCash,
                'updatedBy' => $userId,
            );
            $suppliersTable = DB()->table('suppliers');
            $suppliersTable->where('supplier_id', $supplierId)->update($suppData);

            //insert log (start)
            $this->transactionLog->insert_log_data('suppliers',$supplierId,'',$totalPrice,'','','',$purchaseId);
            //insert log (end)

            //create suppliers ledger
            $lgSuplData = array(
                'sch_id' => $shopId,
                'supplier_id' => $supplierId,
                'purchase_id' => $purchaseId,
                'trangaction_type' => 'Cr.',
                'particulars' => 'Purchase Cash Due',
                'amount' => $totalPrice,
                'rest_balance' => $newCash,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_suppliersTable = DB()->table('ledger_suppliers');
            $ledger_suppliersTable->insert($lgSuplData);
            $ledg_sup_id = DB()->insertID();
            //purshase total price calculet to suppliers balance and update suppliers balance or create suppliers ledger (end)

            //Insert Purchase Transaction Entries
            $this->purchase_transaction_entries($purchaseId, $ledg_sup_id, 'ledger_suppliers', 'Cr.');

            //insert log (start)
            $this->transactionLog->insert_log_data('ledger_suppliers',$ledg_sup_id,'',$totalPrice,'','','',$purchaseId);
            //insert log (end)

            // purchase balance update and ledger create (start)
            $purchaseBal = get_data_by_id('purchase_balance', 'shops', 'sch_id', $shopId);
            $restBalPurc = $purchaseBal + $totalPrice;


            $purUpdata = array('purchase_balance' => $restBalPurc);
            $shopPurBalTable = DB()->table('shops');
            $shopPurBalTable->where('sch_id', $shopId)->update($purUpdata);

            //insert log (start)
            $this->transactionLog->insert_log_data('shops',$shopId,'',$restBalPurc,'','','',$purchaseId,'purchase_balance');
            //insert log (end)

            $purLedgData = array(
                'sch_id' => $shopId,
                'purchase_id' => $purchaseId,
                'trangaction_type' => 'Dr.',
                'particulars' => 'New purchase amount',
                'amount' => $totalPrice,
                'rest_balance' => $restBalPurc,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_purchaseTable = DB()->table('ledger_purchase');
            $ledger_purchaseTable->insert($purLedgData);
            $ledgPurch_id = DB()->insertID();
            // purchase balance update and ledger create (end)

            //Insert Purchase Transaction Entries
            $this->purchase_transaction_entries($purchaseId, $ledgPurch_id, 'ledger_purchase', 'Dr.');

            //insert log (start)
            $this->transactionLog->insert_log_data('ledger_purchase',$ledgPurch_id,'',$totalPrice,'','','',$purchaseId);
            //insert log (end)

            // stock balance update and ledger create (start)
            $stockBal = get_data_by_id('stockAmount', 'shops', 'sch_id', $shopId);
            $restBalStock = $stockBal + $totalPrice;


            $stockUpdata = array('stockAmount' => $restBalStock);
            $shopStoAmTable = DB()->table('shops');
            $shopStoAmTable->where('sch_id', $shopId)->update($stockUpdata);

            //insert log (start)
            $this->transactionLog->insert_log_data('shops',$shopId,'',$totalPrice,'','','',$purchaseId,'stockAmount');
            //insert log (end)

            $stockLedgData = array(
                'sch_id' => $shopId,
                'purchase_id' => $purchaseId,
                'trangaction_type' => 'Dr.',
                'particulars' => 'New purchase amount',
                'amount' => $totalPrice,
                'rest_balance' => $restBalStock,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_stockTable = DB()->table('ledger_stock');
            $ledger_stockTable->insert($stockLedgData);
            $stock_id = DB()->insertID();
            // stock balance update and ledger create (end)

            //Insert Purchase Transaction Entries
            $this->purchase_transaction_entries($purchaseId, $stock_id, 'ledger_stock', 'Dr.');

            //insert log (start)
            $this->transactionLog->insert_log_data('ledger_stock',$stock_id,'',$totalPrice,'','','',$purchaseId);
            //insert log (end)

            if ($cashAmount > 0) {

                //purshase pay cash amount calculet to shops cash and update shops cash or create ledger_nagodan statment in ledger_nagodan table (start)
                $shopsCash = get_data_by_id('cash', 'shops', 'sch_id', $shopId);

                if ($shopsCash >= $cashAmount) {
                    $upCahs = $shopsCash - $cashAmount;
                    $shopsData = array(
                        'cash' => $upCahs,
                        'updatedBy' => $userId,
                    );
                    $shopCasTable = DB()->table('shops');
                    $shopCasTable->where('sch_id', $shopId)->update($shopsData);
                    //insert log (start)
                    $this->transactionLog->insert_log_data('shops',$shopId,'',$cashAmount,'','','',$purchaseId);
                    //insert log (end)

                    //nagodan ledger create
                    $lgNagData = array(
                        'sch_id' => $shopId,
                        'purchase_id' => $purchaseId,
                        'particulars' => 'Purchase Cash Pay',
                        'trangaction_type' => 'Cr.',
                        'amount' => $cashAmount,
                        'rest_balance' => $upCahs,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_nagodanTable = DB()->table('ledger_nagodan');
                    $ledger_nagodanTable->insert($lgNagData);
                    $ledg_nagodan_id = DB()->insertID();
                    //purshase pay cash amount calculet to shops cash and update shops cash or create ledger_nagodan statment in ledger_nagodan table (end)

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledg_nagodan_id, 'ledger_nagodan', 'Cr.');

                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger_nagodan',$ledg_nagodan_id,'',$cashAmount,'','','',$purchaseId);
                    //insert log (end)

                    //purshase pay cash amount calculet to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (start)
                    $supplierccCash = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
                    $suppCash = $supplierccCash + $cashAmount;

                    $cashsuppData = array(
                        'balance' => $suppCash,
                        'updatedBy' => $userId,
                    );
                    $suppliersTab = DB()->table('suppliers');
                    $suppliersTab->where('supplier_id', $supplierId)->update($cashsuppData);
                    //insert log (start)
                    $this->transactionLog->insert_log_data('suppliers',$supplierId,'',$cashAmount,'','','',$purchaseId);
                    //insert log (end)

                    //suppliers ledger create
                    $lgSuplData = array(
                        'sch_id' => $shopId,
                        'supplier_id' => $supplierId,
                        'purchase_id' => $purchaseId,
                        'particulars' => 'Purchase Cash Pay',
                        'trangaction_type' => 'Dr.',
                        'amount' => $cashAmount,
                        'rest_balance' => $suppCash,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_suppliersTab = DB()->table('ledger_suppliers');
                    $ledger_suppliersTab->insert($lgSuplData);
                    $ledg_sup_id = DB()->insertID();

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledg_sup_id, 'ledger_suppliers', 'Dr.');

                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger_suppliers',$ledg_sup_id,'',$cashAmount,'','','',$purchaseId);
                    //insert log (end)
                }
                //purshase pay cash amount calculet to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (end)
            }


            if ($bankAmount > 0) {

                //purshase pay bank amountcalculet to bank balance and update bank balance or create bank ledger in ledger_bank table (start)
                $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $bankId);

                if ($bankCash >= $bankAmount) {

                    $upCahs = $bankCash - $bankAmount;
                    $bankData = array(
                        'balance' => $upCahs,
                        'updatedBy' => $userId,
                    );
                    $bankTable = DB()->table('bank');
                    $bankTable->where('bank_id', $bankId)->update($bankData);
                    //insert log (start)
                    $this->transactionLog->insert_log_data('bank',$bankId,'',$bankAmount,'','','',$purchaseId);
                    //insert log (end)

                    //bank ledger create
                    $lgBankData = array(
                        'sch_id' => $shopId,
                        'bank_id' => $bankId,
                        'purchase_id' => $purchaseId,
                        'trangaction_type' => 'Cr.',
                        'particulars' => 'Purchase Bank Pay',
                        'amount' => $bankAmount,
                        'rest_balance' => $upCahs,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_bankTab = DB()->table('ledger_bank');
                    $ledger_bankTab->insert($lgBankData);
                    $ledgBank_id = DB()->insertID();
                    //purshase pay bank amountcalculet to bank balance and update bank balance or create bank ledger in ledger_bank table (end)

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledgBank_id, 'ledger_bank', 'Cr.');

                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger_bank',$ledgBank_id,'',$bankAmount,'','','',$purchaseId);
                    //insert log (end)

                    //purshase pay bank amount calculet to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (start)
                    $supplierbbCash = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
                    $suppBaCash = $supplierbbCash + $bankAmount;

                    $banksuppData = array(
                        'balance' => $suppBaCash,
                        'updatedBy' => $userId,
                    );
                    $suppliersTab = DB()->table('suppliers');
                    $suppliersTab->where('supplier_id', $supplierId)->update($banksuppData);
                    //insert log (start)
                    $this->transactionLog->insert_log_data('suppliers',$supplierId,'',$bankAmount,'','','',$purchaseId);
                    //insert log (end)

                    //suppliers ledger create
                    $lgSuplData = array(
                        'sch_id' => $shopId,
                        'supplier_id' => $supplierId,
                        'purchase_id' => $purchaseId,
                        'particulars' => 'Purchase Bank Pay',
                        'trangaction_type' => 'Dr.',
                        'amount' => $bankAmount,
                        'rest_balance' => $suppBaCash,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_suppliersTab = DB()->table('ledger_suppliers');
                    $ledger_suppliersTab->insert($lgSuplData);
                    $ledg_sup_id = DB()->insertID();

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledg_sup_id, 'ledger_suppliers', 'Dr.');

                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger_suppliers',$ledg_sup_id,'',$bankAmount,'','','',$purchaseId);
                    //insert log (end)
                }
                //purshase pay bank amount calculet to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (end)

            }


            //purchase product insert in product table and purchase item table (start)
            $storeTab = DB()->table('stores');
            $store = $storeTab->where('sch_id', $shopId)->where('is_default', 1)->get()->getRow();
            $storeId = $store->store_id;

            for ($i = 0; $i < $number; $i++) {
                //insert purchase product
                $data = array(
                    'sch_id' => $shopId,
                    'name' => $name[$i],
                    'unit' => $this->request->getPost('unit[]')[$i],
                    'purchase_price' => $this->request->getPost('purchase_price[]')[$i],
                    'selling_price' => $this->request->getPost('selling_price[]')[$i],
                    'supplier_id' => $supplierId,
                    'prod_cat_id' => $this->request->getPost('prod_cat_id[]')[$i],
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $proTable = DB()->table('products');
                $proTable->insert($data);
                $prodId = DB()->insertID();
                //insert log (start)
                $this->transactionLog->insert_log_data('products',$prodId,'',$data['purchase_price'],'','','',$purchaseId);
                //insert log (end)

                //insert data in table product_stock_relation
                $dataQty = array(
                    'store_id' => $storeId,
                    'product_id' => $prodId,
                    'quantity' => $this->request->getPost('quantity[]')[$i],
                );
                $stockRelationTable = DB()->table('product_stock_relation');
                $stockRelationTable->insert($dataQty);
                //insert data in table product_stock_relation

                //insetr purchase Item in purchase item table
                $purchasePrice = get_data_by_id('purchase_price', 'products', 'prod_id', $prodId);
                $quantity = $this->request->getPost('quantity[]')[$i];

                $total_price = $quantity * $purchasePrice;

                $purchaseData = array(
                    'purchase_id' => $purchaseId,
                    'prod_id' => $prodId,
                    'purchase_price' => $purchasePrice,
                    'quantity' => $quantity,
                    'total_price' => $total_price,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $purchase_itemTab = DB()->table('purchase_item');
                $purchase_itemTab->insert($purchaseData);
                $purchase_item_id = DB()->insertID();
                //insert log (start)
                $this->transactionLog->insert_log_data('purchase_item',$purchase_item_id,'',$total_price,'','','',$purchaseId);
                //insert log (end)
            }
            //purchase product insert in product table and purchase item table (end)


            //purchase all pay amount detail Update in purchase table(start)
            $parsData = array(
                'amount' => $totalPrice,
                'nagad_paid' => $cashAmount,
                'bank_paid' => $bankAmount,
                'bank_id' => $bankId,
                'due' => $dueAmount,
                'date' => $date,
                'updatedBy' => $userId,
            );
            $purchaseTab = DB()->table('purchase');
            $purchaseTab->where('purchase_id', $purchaseId)->update($parsData);
            //purchase all pay amount detail Update in purchase table(end)
            //insert log (start)
            $this->transactionLog->insert_log_data('purchase',$purchaseId,'',$totalPrice,'','','',$purchaseId);
            //insert log (end)

            // transaction events insert;
            DB()->table('transaction_events')->insert([
                'sch_id' => $shopId,
                'purchase_id'      => $purchaseId,
                'createdDtm'       => date('Y-m-d H:i:s')
            ]);

            DB()->transComplete();
            if(!empty($sms)) {
                $message = 'Thank you for your purchase.Your purchase amount is-' . $totalPrice;
                $phone = get_data_by_id('phone', 'suppliers', 'supplier_id', $supplierId);
                send_sms($phone, $message);
            }

            $this->cart->destroy();
            unset($_SESSION['supplier_id']);
            unset($_SESSION['purchaseId']);

            $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Create Record Success<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Purchase'));
        } else {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Your cart is empty! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Purchase/new_product'));
        }

    }

    public function product_create_action()
    {
        $userId = $this->session->userId;
        $shopId = $this->session->shopId;

        $date = $this->request->getPost('date');
        $purchaseId = $this->request->getPost('purchase_id');
        $supplierId = $this->request->getPost('supplier_id');
        $sms = $this->request->getPost('sms');

        $totalPrice = str_replace(',', '', $this->request->getPost('totalPrice'));
        $cashAmount = str_replace(',', '', $this->request->getPost('cash'));
        $bankAmount = str_replace(',', '', $this->request->getPost('bank'));
        $dueAmount = str_replace(',', '', $this->request->getPost('due'));
        $bankId = $this->request->getPost('bank_id');

        $name = $this->request->getPost('name[]');

        if (!empty($this->cart->contents())) {
            $number = count($name);

            $cashbal = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
            $bankbal = get_data_by_id('balance', 'bank', 'bank_id', $bankId);

            if ($cashbal < $cashAmount) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Not enough balance! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Purchase/new_product'));
            }

            if ($bankbal < $bankAmount) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Not enough balance! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Purchase/new_product'));
            }

            if (!empty($cashAmount) && $cashAmount < 0) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Purchase/new_product'));
            }

            if (!empty($bankAmount) && $bankAmount < 0) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Purchase/new_product'));
            }

            DB()->transStart();

            //purshase total price calculet to suppliers balance and update suppliers balance or create suppliers ledger (start)
            $supplierCash = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
            $newCash = $supplierCash - $totalPrice;

            $suppData = array(
                'balance' => $newCash,
                'updatedBy' => $userId,
            );
            $suppliersTable = DB()->table('suppliers');
            $suppliersTable->where('supplier_id', $supplierId)->update($suppData);

            //insert log (start)
            $this->transactionLog->insert_log_data('suppliers',$supplierId,'',$totalPrice,'','','',$purchaseId);
            //insert log (end)

            //create suppliers ledger
            $lgSuplData = array(
                'sch_id' => $shopId,
                'supplier_id' => $supplierId,
                'purchase_id' => $purchaseId,
                'trangaction_type' => 'Cr.',
                'particulars' => 'Purchase Cash Due',
                'amount' => $totalPrice,
                'rest_balance' => $newCash,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_suppliersTable = DB()->table('ledger_suppliers');
            $ledger_suppliersTable->insert($lgSuplData);
            $ledg_sup_id = DB()->insertID();
            //purshase total price calculet to suppliers balance and update suppliers balance or create suppliers ledger (end)

            //Insert Purchase Transaction Entries
            $this->purchase_transaction_entries($purchaseId, $ledg_sup_id, 'ledger_suppliers', 'Cr.');

            //insert log (start)
            $this->transactionLog->insert_log_data('ledger_suppliers',$ledg_sup_id,'',$totalPrice,'','','',$purchaseId);
            //insert log (end)

            // purchase balance update and ledger create (start)
            $purchaseBal = get_data_by_id('purchase_balance', 'shops', 'sch_id', $shopId);
            $restBalPurc = $purchaseBal + $totalPrice;


            $purUpdata = array('purchase_balance' => $restBalPurc);
            $shopPurBalTable = DB()->table('shops');
            $shopPurBalTable->where('sch_id', $shopId)->update($purUpdata);

            //insert log (start)
            $this->transactionLog->insert_log_data('shops',$shopId,'',$totalPrice,'','','',$purchaseId,'purchase_balance');
            //insert log (end)

            $purLedgData = array(
                'sch_id' => $shopId,
                'purchase_id' => $purchaseId,
                'trangaction_type' => 'Dr.',
                'particulars' => 'New purchase amount',
                'amount' => $totalPrice,
                'rest_balance' => $restBalPurc,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_purchaseTable = DB()->table('ledger_purchase');
            $ledger_purchaseTable->insert($purLedgData);
            $ledgPurch_id = DB()->insertID();
            // purchase balance update and ledger create (end)

            //Insert Purchase Transaction Entries
            $this->purchase_transaction_entries($purchaseId, $ledgPurch_id, 'ledger_purchase', 'Dr.');

            //insert log (start)
            $this->transactionLog->insert_log_data('ledger_purchase',$ledgPurch_id,'',$totalPrice,'','','',$purchaseId);
            //insert log (end)

            // stock balance update and ledger create (start)
            $stockBal = get_data_by_id('stockAmount', 'shops', 'sch_id', $shopId);
            $restBalStock = $stockBal + $totalPrice;


            $stockUpdata = array('stockAmount' => $restBalStock);
            $shopStoAmTable = DB()->table('shops');
            $shopStoAmTable->where('sch_id', $shopId)->update($stockUpdata);

            //insert log (start)
            $this->transactionLog->insert_log_data('shops',$shopId,'',$totalPrice,'','','',$purchaseId,'stockAmount');
            //insert log (end)

            $stockLedgData = array(
                'sch_id' => $shopId,
                'purchase_id' => $purchaseId,
                'trangaction_type' => 'Dr.',
                'particulars' => 'New purchase amount',
                'amount' => $totalPrice,
                'rest_balance' => $restBalStock,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_stockTable = DB()->table('ledger_stock');
            $ledger_stockTable->insert($stockLedgData);
            $stock_id = DB()->insertID();
            // stock balance update and ledger create (end)

            //Insert Purchase Transaction Entries
            $this->purchase_transaction_entries($purchaseId, $stock_id, 'ledger_stock', 'Dr.');

            //insert log (start)
            $this->transactionLog->insert_log_data('ledger_stock',$stock_id,'',$totalPrice,'','','',$purchaseId);
            //insert log (end)

            if ($cashAmount > 0) {

                //purshase pay cash amount calculet to shops cash and update shops cash or create ledger_nagodan statment in ledger_nagodan table (start)
                $shopsCash = get_data_by_id('cash', 'shops', 'sch_id', $shopId);

                if ($shopsCash >= $cashAmount) {
                    $upCahs = $shopsCash - $cashAmount;
                    $shopsData = array(
                        'cash' => $upCahs,
                        'updatedBy' => $userId,
                    );
                    $shopCasTable = DB()->table('shops');
                    $shopCasTable->where('sch_id', $shopId)->update($shopsData);
                    //insert log (start)
                    $this->transactionLog->insert_log_data('shops',$shopId,'',$cashAmount,'','','',$purchaseId);
                    //insert log (end)

                    //nagodan ledger create
                    $lgNagData = array(
                        'sch_id' => $shopId,
                        'purchase_id' => $purchaseId,
                        'particulars' => 'Purchase Cash Pay',
                        'trangaction_type' => 'Cr.',
                        'amount' => $cashAmount,
                        'rest_balance' => $upCahs,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_nagodanTable = DB()->table('ledger_nagodan');
                    $ledger_nagodanTable->insert($lgNagData);
                    $ledg_nagodan_id = DB()->insertID();
                    //purshase pay cash amount calculet to shops cash and update shops cash or create ledger_nagodan statment in ledger_nagodan table (end)

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledg_nagodan_id, 'ledger_nagodan', 'Cr.');

                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger_nagodan',$ledg_nagodan_id,'',$cashAmount,'','','',$purchaseId);
                    //insert log (end)

                    //purshase pay cash amount calculet to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (start)
                    $supplierccCash = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
                    $suppCash = $supplierccCash + $cashAmount;

                    $cashsuppData = array(
                        'balance' => $suppCash,
                        'updatedBy' => $userId,
                    );
                    $suppliersTab = DB()->table('suppliers');
                    $suppliersTab->where('supplier_id', $supplierId)->update($cashsuppData);
                    //insert log (start)
                    $this->transactionLog->insert_log_data('suppliers',$supplierId,'',$cashAmount,'','','',$purchaseId);
                    //insert log (end)

                    //suppliers ledger create
                    $lgSuplData = array(
                        'sch_id' => $shopId,
                        'supplier_id' => $supplierId,
                        'purchase_id' => $purchaseId,
                        'particulars' => 'Purchase Cash Pay',
                        'trangaction_type' => 'Dr.',
                        'amount' => $cashAmount,
                        'rest_balance' => $suppCash,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_suppliersTab = DB()->table('ledger_suppliers');
                    $ledger_suppliersTab->insert($lgSuplData);
                    $ledg_sup_id = DB()->insertID();

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledg_sup_id, 'ledger_suppliers', 'Dr.');

                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger_suppliers',$ledg_sup_id,'',$cashAmount,'','','',$purchaseId);
                    //insert log (end)
                }
                //purshase pay cash amount calculet to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (end)
            }


            if ($bankAmount > 0) {

                //purshase pay bank amountcalculet to bank balance and update bank balance or create bank ledger in ledger_bank table (start)
                $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $bankId);

                if ($bankCash >= $bankAmount) {

                    $upCahs = $bankCash - $bankAmount;
                    $bankData = array(
                        'balance' => $upCahs,
                        'updatedBy' => $userId,
                    );
                    $bankTable = DB()->table('bank');
                    $bankTable->where('bank_id', $bankId)->update($bankData);
                    //insert log (start)
                    $this->transactionLog->insert_log_data('bank',$bankId,'',$bankAmount,'','','',$purchaseId);
                    //insert log (end)

                    //bank ledger create
                    $lgBankData = array(
                        'sch_id' => $shopId,
                        'bank_id' => $bankId,
                        'purchase_id' => $purchaseId,
                        'trangaction_type' => 'Cr.',
                        'particulars' => 'Purchase Bank Pay',
                        'amount' => $bankAmount,
                        'rest_balance' => $upCahs,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_bankTab = DB()->table('ledger_bank');
                    $ledger_bankTab->insert($lgBankData);
                    $ledgBank_id = DB()->insertID();
                    //purshase pay bank amountcalculet to bank balance and update bank balance or create bank ledger in ledger_bank table (end)

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledgBank_id, 'ledger_bank', 'Cr.');

                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger_bank',$ledgBank_id,'',$bankAmount,'','','',$purchaseId);
                    //insert log (end)

                    //purshase pay bank amount calculet to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (start)
                    $supplierbbCash = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
                    $suppBaCash = $supplierbbCash + $bankAmount;

                    $banksuppData = array(
                        'balance' => $suppBaCash,
                        'updatedBy' => $userId,
                    );
                    $suppliersTab = DB()->table('suppliers');
                    $suppliersTab->where('supplier_id', $supplierId)->update($banksuppData);
                    //insert log (start)
                    $this->transactionLog->insert_log_data('suppliers',$supplierId,'',$bankAmount,'','','',$purchaseId);
                    //insert log (end)

                    //suppliers ledger create
                    $lgSuplData = array(
                        'sch_id' => $shopId,
                        'supplier_id' => $supplierId,
                        'purchase_id' => $purchaseId,
                        'particulars' => 'Purchase Bank Pay',
                        'trangaction_type' => 'Dr.',
                        'amount' => $bankAmount,
                        'rest_balance' => $suppBaCash,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_suppliersTab = DB()->table('ledger_suppliers');
                    $ledger_suppliersTab->insert($lgSuplData);
                    $ledg_sup_id = DB()->insertID();

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledg_sup_id, 'ledger_suppliers', 'Dr.');

                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger_suppliers',$ledg_sup_id,'',$bankAmount,'','','',$purchaseId);
                    //insert log (end)
                }
                //purshase pay bank amount calculet to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (end)

            }


            //purchase product insert in product table and purchase item table (start)
            $storeTab = DB()->table('stores');
            $store = $storeTab->where('sch_id', $shopId)->where('is_default', 1)->get()->getRow();
            $storeId = $store->store_id;

            for ($i = 0; $i < $number; $i++) {
                $queryUnit = DB()->table('unit_set')->where('unit_set_id',$this->request->getPost('unit_set_id[]')[$i])->get()->getRow();
                //insert purchase product
                $data = array(
                    'sch_id' => $shopId,
                    'name' => $name[$i],
                    'purchase_units' => $queryUnit->purchase_units,
                    'sale_units' => $queryUnit->sell_units,
                    'supplier_id' => $supplierId,
                    'prod_cat_id' => $this->request->getPost('prod_cat_id[]')[$i],
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $proTable = DB()->table('products');
                $proTable->insert($data);
                $prodId = DB()->insertID();
                //insert log (start)
                $this->transactionLog->insert_log_data('products',$prodId,'',$this->request->getPost('purchase_price[]')[$i],'','','',$purchaseId);
                //insert log (end)

                //insert data in table product_stock_relation
                $dataQty = array(
                    'sch_id' => $shopId,
                    'store_id' => $storeId,
                    'product_id' => $prodId,
                    'quantity' => $this->request->getPost('quantity[]')[$i],
                    'unit' => $this->request->getPost('unit[]')[$i],
                    'purchase_price' => $this->request->getPost('purchase_price[]')[$i],
                    'selling_price' => $this->request->getPost('selling_price[]')[$i],
                );
                $stockRelationTable = DB()->table('product_stock_relation');
                $stockRelationTable->insert($dataQty);
                $productStockRelationId = DB()->insertID();
                //insert data in table product_stock_relation

                //insetr purchase Item in purchase item table
                $purchasePrice = $this->request->getPost('purchase_price[]')[$i];
                $quantity = $this->request->getPost('quantity[]')[$i];

                $total_price = $quantity * $purchasePrice;

                $purchaseData = array(
                    'purchase_id' => $purchaseId,
                    'prod_id' => $prodId,
                    'product_stock_relation_id' => $productStockRelationId,
                    'purchase_price' => $purchasePrice,
                    'quantity' => $quantity,
                    'total_price' => $total_price,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $purchase_itemTab = DB()->table('purchase_item');
                $purchase_itemTab->insert($purchaseData);
                $purchase_item_id = DB()->insertID();
                //insert log (start)
                $this->transactionLog->insert_log_data('purchase_item',$purchase_item_id,'',$total_price,'','','',$purchaseId);
                //insert log (end)
            }
            //purchase product insert in product table and purchase item table (end)


            //purchase all pay amount detail Update in purchase table(start)
            $parsData = array(
                'amount' => $totalPrice,
                'nagad_paid' => $cashAmount,
                'bank_paid' => $bankAmount,
                'bank_id' => $bankId,
                'due' => $dueAmount,
                'date' => $date,
                'updatedBy' => $userId,
            );
            $purchaseTab = DB()->table('purchase');
            $purchaseTab->where('purchase_id', $purchaseId)->update($parsData);
            //purchase all pay amount detail Update in purchase table(end)
            //insert log (start)
            $this->transactionLog->insert_log_data('purchase',$purchaseId,'',$totalPrice,'','','',$purchaseId);
            //insert log (end)

            DB()->transComplete();
            if(!empty($sms)) {
                $message = 'Thank you for your purchase.Your purchase amount is-' . $totalPrice;
                $phone = get_data_by_id('phone', 'suppliers', 'supplier_id', $supplierId);
                send_sms($phone, $message);
            }

            $this->cart->destroy();
            unset($_SESSION['supplier_id']);
            unset($_SESSION['purchaseId']);

            $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Create Record Success<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Purchase'));
        } else {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Your cart is empty! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Purchase/new_product'));
        }

    }
    private function purchase_transaction_entries($purchase_id, $ledger_id, $table_name, $transaction_type) {
        DB()->table('transaction_entries')->insert([
            'purchase_id'      => $purchase_id,
            'ledger_id'        => $ledger_id,
            'table_name'       => $table_name,
            'trangaction_type' => $transaction_type,
            'createdDtm'       => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * @description This method provides purchase view
     * @param int $id
     * @return RedirectResponse|void
     */
    public function view($id)
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $purchaseTable = DB()->table('purchase');
            $data['purchase'] = $purchaseTable->where('purchase_id', $id)->get()->getRow();

            $purchase_itemTable = DB()->table('purchase_item');
            $data['purchaseItame'] = $purchase_itemTable->where('purchase_id', $id)->get()->getResult();
            $data['purchaseId'] = $id;

            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['read'] == 1) {
                echo view('Admin/Purchase/view', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    public function purchaseEdit(){
        $purchaseId = $this->request->getPost('id');
        if (empty($purchaseId)) {
            $purchaseId = $this->request->getGet('id');
        }

        $purchaseTable = DB()->table('purchase');
        $data['purchase'] = $purchaseTable->where('purchase_id', $purchaseId)->get()->getRow();

        $purchase_itemTable = DB()->table('purchase_item');
        $data['purchaseItem'] = $purchase_itemTable->where('purchase_id', $purchaseId)->get()->getResult();

        echo view('Admin/Purchase/edit', $data);
    }

    public function purchaseEdiAction(){
        $userId = $this->session->userId;
        $shopId = $this->session->shopId;

        $purchaseId = $this->request->getPost('purchase_id');

        $purchase_item_id = $this->request->getPost('purchase_item_id[]');
        $prod_id = $this->request->getPost('prod_id[]');
        $qty = $this->request->getPost('qty[]');
        $price = $this->request->getPost('price[]');
        $salePrice = $this->request->getPost('salePrice[]');
        $total_price = $this->request->getPost('total_price[]');


        $totalPrice = $this->request->getPost('totalPrice');
        $cashAmount = $this->request->getPost('cash');
        $bankId = $this->request->getPost('bank_id');
        $bankAmount = $this->request->getPost('bank');
        $totalDue = $this->request->getPost('due');


        $oldPurchase = DB()->table('purchase')->where('purchase_id', $purchaseId)->get()->getRow();
        if (empty($oldPurchase)) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger">Purchase not found!</div>');
            return redirect()->to(site_url('Admin/Purchase'));
        }
        $oldAmount  = (float)$oldPurchase->amount;
        $oldNagod   = (float)$oldPurchase->nagad_paid;
        $oldBank    = (float)$oldPurchase->bank_paid;
        $oldBankId  = $oldPurchase->bank_id;
        $oldDue     = (float)$oldPurchase->due;
        $supplierId = !empty($oldPurchase->supplier_id) ? $oldPurchase->supplier_id : 0;

        $totalPrice = (float)$totalPrice;
        $cashAmount = (float)$cashAmount;
        $bankAmount = (float)$bankAmount;
        $totalDue   = (float)$totalDue;

        DB()->transStart();

        // 1. ==================== SUPPLIERS BALANCE & LEDGERS ====================
        if (!empty($supplierId)) {
            $supplierCash = (float)get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
            $newSuppBal   = ($supplierCash + $oldDue) - $totalDue;

            DB()->table('suppliers')->where('supplier_id', $supplierId)->update([
                'balance'   => $newSuppBal,
                'updatedBy' => $userId,
            ]);

            // Supplier Due Ledger ('Purchase Cash Due' - Cr.)
            $dueLedger = DB()->table('ledger_suppliers')
                ->where('purchase_id', $purchaseId)
                ->where('trangaction_type', 'Cr.')
                ->get()->getRow();
            if (!empty($dueLedger)) {
                $newRestBalance = ($dueLedger->rest_balance + $dueLedger->amount) - $totalPrice;
                DB()->table('ledger_suppliers')->where('ledg_sup_id', $dueLedger->ledg_sup_id)->update([
                    'amount'       => $totalPrice,
                    'rest_balance' => $newRestBalance,
                ]);
                $this->suppliers_ledger_rest_balance_update($supplierId, $totalPrice, $dueLedger->ledg_sup_id, $dueLedger->amount);

                $supDueLog = DB()->table('transaction_log')->where(['table_name' => 'ledger_suppliers', 'id' => $dueLedger->ledg_sup_id])->get()->getRow();
                if (!empty($supDueLog)) {
                    $this->transactionLog->transaction_log_data_update($supDueLog->transaction_log_id, $totalPrice);
                }
                $supLog = DB()->table('transaction_log')->where(['table_name' => 'suppliers', 'purchase_id' => $purchaseId])->orderBy('transaction_log_id', 'ASC')->get()->getFirstRow();
                if (!empty($supLog)) {
                    $this->transactionLog->transaction_log_data_update($supLog->transaction_log_id, $totalPrice);
                }
            } elseif ($totalPrice > 0) {
                $lgSuplData = [
                    'sch_id'           => $shopId,
                    'supplier_id'      => $supplierId,
                    'purchase_id'      => $purchaseId,
                    'particulars'      => 'Purchase Cash Due',
                    'trangaction_type' => 'Cr.',
                    'amount'           => $totalPrice,
                    'rest_balance'     => $newSuppBal,
                    'createdBy'        => $userId,
                    'createdDtm'       => date('Y-m-d H:i:s')
                ];
                DB()->table('ledger_suppliers')->insert($lgSuplData);
                $ledg_sup_id = DB()->insertID();
                $this->transactionLog->insert_log_data('ledger_suppliers', $ledg_sup_id, '', $totalPrice, '', '', '', $purchaseId);
                $this->transactionLog->insert_log_data('suppliers', $supplierId, '', $totalPrice, '', '', '', $purchaseId);
            }

            // Supplier Cash Pay Ledger ('Purchase Cash Pay' - Dr.)
            $cashLedger = DB()->table('ledger_suppliers')
                ->where('purchase_id', $purchaseId)
                ->where('trangaction_type', 'Dr.')
                ->like('particulars', 'Cash Pay')
                ->get()->getRow();
            if (!empty($cashLedger)) {
                if ($cashAmount > 0) {
                    $newRestBalance = ($cashLedger->rest_balance - $cashLedger->amount) + $cashAmount;
                    DB()->table('ledger_suppliers')->where('ledg_sup_id', $cashLedger->ledg_sup_id)->update([
                        'amount'       => $cashAmount,
                        'rest_balance' => $newRestBalance,
                    ]);
                    $this->suppliers_ledger_rest_balance_update($supplierId, $cashAmount, $cashLedger->ledg_sup_id, $cashLedger->amount);

                    $cashSupLog = DB()->table('transaction_log')->where(['table_name' => 'ledger_suppliers', 'id' => $cashLedger->ledg_sup_id])->get()->getRow();
                    if (!empty($cashSupLog)) {
                        $this->transactionLog->transaction_log_data_update($cashSupLog->transaction_log_id, $cashAmount);
                    }
                } else {
                    DB()->table('ledger_suppliers')->where('ledg_sup_id', $cashLedger->ledg_sup_id)->delete();
                    DB()->table('transaction_log')->where(['table_name' => 'ledger_suppliers', 'id' => $cashLedger->ledg_sup_id])->delete();
                }
            } elseif ($cashAmount > 0) {
                $lgSuplData = [
                    'sch_id'           => $shopId,
                    'supplier_id'      => $supplierId,
                    'purchase_id'      => $purchaseId,
                    'particulars'      => 'Purchase Cash Pay',
                    'trangaction_type' => 'Dr.',
                    'amount'           => $cashAmount,
                    'rest_balance'     => $newSuppBal,
                    'createdBy'        => $userId,
                    'createdDtm'       => date('Y-m-d H:i:s')
                ];
                DB()->table('ledger_suppliers')->insert($lgSuplData);
                $ledg_sup_id = DB()->insertID();
                $this->transactionLog->insert_log_data('ledger_suppliers', $ledg_sup_id, '', $cashAmount, '', '', '', $purchaseId);
            }

            // Supplier Bank Pay Ledger ('Purchase Bank Pay' - Dr.)
            $bankLedger = DB()->table('ledger_suppliers')
                ->where('purchase_id', $purchaseId)
                ->where('trangaction_type', 'Dr.')
                ->like('particulars', 'Bank Pay')
                ->get()->getRow();
            if (!empty($bankLedger)) {
                if ($bankAmount > 0) {
                    $newRestBalance = ($bankLedger->rest_balance - $bankLedger->amount) + $bankAmount;
                    DB()->table('ledger_suppliers')->where('ledg_sup_id', $bankLedger->ledg_sup_id)->update([
                        'amount'       => $bankAmount,
                        'rest_balance' => $newRestBalance,
                    ]);
                    $this->suppliers_ledger_rest_balance_update($supplierId, $bankAmount, $bankLedger->ledg_sup_id, $bankLedger->amount);

                    $bankSupLog = DB()->table('transaction_log')->where(['table_name' => 'ledger_suppliers', 'id' => $bankLedger->ledg_sup_id])->get()->getRow();
                    if (!empty($bankSupLog)) {
                        $this->transactionLog->transaction_log_data_update($bankSupLog->transaction_log_id, $bankAmount);
                    }
                } else {
                    DB()->table('ledger_suppliers')->where('ledg_sup_id', $bankLedger->ledg_sup_id)->delete();
                    DB()->table('transaction_log')->where(['table_name' => 'ledger_suppliers', 'id' => $bankLedger->ledg_sup_id])->delete();
                }
            } elseif ($bankAmount > 0) {
                $lgSuplData = [
                    'sch_id'           => $shopId,
                    'supplier_id'      => $supplierId,
                    'purchase_id'      => $purchaseId,
                    'particulars'      => 'Purchase Bank Pay',
                    'trangaction_type' => 'Dr.',
                    'amount'           => $bankAmount,
                    'rest_balance'     => $newSuppBal,
                    'createdBy'        => $userId,
                    'createdDtm'       => date('Y-m-d H:i:s')
                ];
                DB()->table('ledger_suppliers')->insert($lgSuplData);
                $ledg_sup_id = DB()->insertID();
                $this->transactionLog->insert_log_data('ledger_suppliers', $ledg_sup_id, '', $bankAmount, '', '', '', $purchaseId);
            }
        }

        // 2. ==================== PURCHASE BALANCE ====================
        $purchaseBal = (float)get_data_by_id('purchase_balance', 'shops', 'sch_id', $shopId);
        $restBalPurc = ($purchaseBal - $oldAmount) + $totalPrice;
        DB()->table('shops')->where('sch_id', $shopId)->update(['purchase_balance' => $restBalPurc]);

        $purchaseBalanceInfo = $this->transactionLog->get_table_name_by_row_purchase_id_by_colum_name('shops', $purchaseId, 'purchase_balance');
        if (!empty($purchaseBalanceInfo)) {
            $this->transactionLog->transaction_log_data_update($purchaseBalanceInfo->transaction_log_id, $totalPrice);
            $this->transactionLog->transaction_edit_log_data_insert('shops', '', '', $this->session->userId, $purchaseBalanceInfo->amount, $totalPrice, '', $purchaseId, 'purchase_balance');
        } elseif ($totalPrice > 0) {
            $this->transactionLog->insert_log_data('shops', $shopId, '', $totalPrice, '', '', '', $purchaseId, 'purchase_balance');
        }

        $ledgerPurchaseInfo = $this->transactionLog->get_table_name_by_row_purchase_id('ledger_purchase', $purchaseId);
        if (!empty($ledgerPurchaseInfo)) {
            $purchaseLedgerBal    = (float)get_data_by_id('rest_balance', 'ledger_purchase', 'ledgPurch_id', $ledgerPurchaseInfo->id);
            $newPurchaseLedgerBal = ($purchaseLedgerBal - (float)$ledgerPurchaseInfo->amount) + $totalPrice;
            DB()->table('ledger_purchase')->where('ledgPurch_id', $ledgerPurchaseInfo->id)->update([
                'amount'       => $totalPrice,
                'rest_balance' => $newPurchaseLedgerBal,
            ]);
            $this->purchase_ledger_rest_balance_update($purchaseId, $totalPrice);
            $this->transactionLog->transaction_log_data_update($ledgerPurchaseInfo->transaction_log_id, $totalPrice);
            $this->transactionLog->transaction_edit_log_data_insert('ledger_purchase', '', '', $this->session->userId, $ledgerPurchaseInfo->amount, $totalPrice, '', $purchaseId, '');
        }

        // 3. ==================== STOCK AMOUNT ====================
        $stockBal     = (float)get_data_by_id('stockAmount', 'shops', 'sch_id', $shopId);
        $restBalStock = ($stockBal - $oldAmount) + $totalPrice;
        DB()->table('shops')->where('sch_id', $shopId)->update(['stockAmount' => $restBalStock]);

        $stockAmountInfo = $this->transactionLog->get_table_name_by_row_purchase_id_by_colum_name('shops', $purchaseId, 'stockAmount');
        if (!empty($stockAmountInfo)) {
            $this->transactionLog->transaction_log_data_update($stockAmountInfo->transaction_log_id, $totalPrice);
            $this->transactionLog->transaction_edit_log_data_insert('shops', '', '', $this->session->userId, $stockAmountInfo->amount, $totalPrice, '', $purchaseId, 'stockAmount');
        } elseif ($totalPrice > 0) {
            $this->transactionLog->insert_log_data('shops', $shopId, '', $totalPrice, '', '', '', $purchaseId, 'stockAmount');
        }

        $ledgerStockInfo = $this->transactionLog->get_table_name_by_row_purchase_id('ledger_stock', $purchaseId);
        if (!empty($ledgerStockInfo)) {
            $ledgerStockBal     = (float)get_data_by_id('rest_balance', 'ledger_stock', 'stock_id', $ledgerStockInfo->id);
            $restLedgerStockBal = ($ledgerStockBal - (float)$ledgerStockInfo->amount) + $totalPrice;
            DB()->table('ledger_stock')->where('stock_id', $ledgerStockInfo->id)->update([
                'amount'       => $totalPrice,
                'rest_balance' => $restLedgerStockBal,
            ]);
            $this->transactionLog->transaction_log_data_update($ledgerStockInfo->transaction_log_id, $totalPrice);
            $this->transactionLog->transaction_edit_log_data_insert('ledger_stock', '', '', $this->session->userId, $ledgerStockInfo->amount, $totalPrice, '', $purchaseId);
            $this->stock_ledger_rest_balance_update($purchaseId, $totalPrice);
        }

        // 4. ==================== CASH (NAGOD) ====================
        $shopsCash = (float)get_data_by_id('cash', 'shops', 'sch_id', $shopId);
        $upCash    = ($shopsCash + $oldNagod) - $cashAmount;
        DB()->table('shops')->where('sch_id', $shopId)->update(['cash' => $upCash]);

        $shopCashLog = DB()->table('transaction_log')->where(['table_name' => 'shops', 'id' => $shopId, 'purchase_id' => $purchaseId])->where('colum_name', null)->get()->getRow();
        if (!empty($shopCashLog)) {
            if ($cashAmount > 0) {
                $this->transactionLog->transaction_log_data_update($shopCashLog->transaction_log_id, $cashAmount);
                $this->transactionLog->transaction_edit_log_data_insert('shops', '', '', $this->session->userId, $shopCashLog->amount, $cashAmount, '', $purchaseId);
            } else {
                DB()->table('transaction_log')->where('transaction_log_id', $shopCashLog->transaction_log_id)->delete();
            }
        } elseif ($cashAmount > 0) {
            $this->transactionLog->insert_log_data('shops', $shopId, '', $cashAmount, '', '', '', $purchaseId);
        }

        $ledgerNagodInfo = DB()->table('ledger_nagodan')->where('purchase_id', $purchaseId)->get()->getRow();
        if (!empty($ledgerNagodInfo)) {
            if ($cashAmount > 0) {
                $ledgerNagodBal = (float)get_data_by_id('rest_balance', 'ledger_nagodan', 'ledg_nagodan_id', $ledgerNagodInfo->ledg_nagodan_id);
                $upRestCash     = ($ledgerNagodBal + (float)$ledgerNagodInfo->amount) - $cashAmount;
                DB()->table('ledger_nagodan')->where('ledg_nagodan_id', $ledgerNagodInfo->ledg_nagodan_id)->update([
                    'amount'       => $cashAmount,
                    'rest_balance' => $upRestCash,
                ]);
                $this->cash_ledger_rest_balance_update($shopId, $cashAmount, $ledgerNagodInfo->ledg_nagodan_id, $ledgerNagodInfo->amount);
                $this->transactionLog->transaction_edit_log_data_insert('ledger_nagodan', '', '', $this->session->userId, $ledgerNagodInfo->amount, $cashAmount, '', $purchaseId);
                $nagLog = DB()->table('transaction_log')->where(['table_name' => 'ledger_nagodan', 'id' => $ledgerNagodInfo->ledg_nagodan_id])->get()->getRow();
                if (!empty($nagLog)) {
                    $this->transactionLog->transaction_log_data_update($nagLog->transaction_log_id, $cashAmount);
                }
            } else {
                DB()->table('ledger_nagodan')->where('ledg_nagodan_id', $ledgerNagodInfo->ledg_nagodan_id)->delete();
                DB()->table('transaction_log')->where(['table_name' => 'ledger_nagodan', 'id' => $ledgerNagodInfo->ledg_nagodan_id])->delete();
            }
        } elseif ($cashAmount > 0) {
            $lgNagData = [
                'sch_id'           => $shopId,
                'purchase_id'      => $purchaseId,
                'trangaction_type' => 'Cr.',
                'particulars'      => 'Purchase Cash Pay',
                'amount'           => $cashAmount,
                'rest_balance'     => $upCash,
                'createdBy'        => $userId,
                'createdDtm'       => date('Y-m-d H:i:s')
            ];
            DB()->table('ledger_nagodan')->insert($lgNagData);
            $ledg_nagodan_id = DB()->insertID();
            $this->transactionLog->insert_log_data('ledger_nagodan', $ledg_nagodan_id, '', $cashAmount, '', '', '', $purchaseId);
        }

        // 5. ==================== BANK ====================
        $targetBankId = !empty($bankId) ? $bankId : $oldBankId;
        if (!empty($targetBankId)) {
            if ($oldBankId == $bankId) {
                $bankCash   = (float)get_data_by_id('balance', 'bank', 'bank_id', $targetBankId);
                $upBankCash = ($bankCash + $oldBank) - $bankAmount;
                DB()->table('bank')->where('bank_id', $targetBankId)->update(['balance' => $upBankCash]);
            } else {
                if (!empty($oldBankId) && $oldBank > 0) {
                    $oldBankCash = (float)get_data_by_id('balance', 'bank', 'bank_id', $oldBankId);
                    DB()->table('bank')->where('bank_id', $oldBankId)->update(['balance' => $oldBankCash + $oldBank]);
                }
                if (!empty($bankId) && $bankAmount > 0) {
                    $newBankCash = (float)get_data_by_id('balance', 'bank', 'bank_id', $bankId);
                    DB()->table('bank')->where('bank_id', $bankId)->update(['balance' => $newBankCash - $bankAmount]);
                }
            }

            $bankInfo = DB()->table('transaction_log')->where(['table_name' => 'bank', 'purchase_id' => $purchaseId])->get()->getRow();
            if (!empty($bankInfo)) {
                if ($bankAmount > 0) {
                    $this->transactionLog->transaction_log_data_update($bankInfo->transaction_log_id, $bankAmount);
                    $this->transactionLog->transaction_edit_log_data_insert('bank', '', '', $this->session->userId, $bankInfo->amount, $bankAmount, '', $purchaseId);
                } else {
                    DB()->table('transaction_log')->where('transaction_log_id', $bankInfo->transaction_log_id)->delete();
                }
            } elseif ($bankAmount > 0) {
                $this->transactionLog->insert_log_data('bank', $targetBankId, '', $bankAmount, '', '', '', $purchaseId);
            }

            $bankLedgerInfo = DB()->table('ledger_bank')->where('purchase_id', $purchaseId)->get()->getRow();
            if (!empty($bankLedgerInfo)) {
                if ($bankAmount > 0) {
                    $bankLedCash = (float)get_data_by_id('rest_balance', 'ledger_bank', 'ledgBank_id', $bankLedgerInfo->ledgBank_id);
                    $upRestCahs  = ($bankLedCash + (float)$bankLedgerInfo->amount) - $bankAmount;
                    DB()->table('ledger_bank')->where('ledgBank_id', $bankLedgerInfo->ledgBank_id)->update([
                        'amount'       => $bankAmount,
                        'bank_id'      => $targetBankId,
                        'rest_balance' => $upRestCahs,
                    ]);
                    $this->bank_ledger_rest_balance_update($targetBankId, $bankAmount, $bankLedgerInfo->ledgBank_id, $bankLedgerInfo->amount);
                    $this->transactionLog->transaction_edit_log_data_insert('ledger_bank', '', '', $this->session->userId, $bankLedgerInfo->amount, $bankAmount, '', $purchaseId);
                    $bLog = DB()->table('transaction_log')->where(['table_name' => 'ledger_bank', 'id' => $bankLedgerInfo->ledgBank_id])->get()->getRow();
                    if (!empty($bLog)) {
                        $this->transactionLog->transaction_log_data_update($bLog->transaction_log_id, $bankAmount);
                    }
                } else {
                    DB()->table('ledger_bank')->where('ledgBank_id', $bankLedgerInfo->ledgBank_id)->delete();
                    DB()->table('transaction_log')->where(['table_name' => 'ledger_bank', 'id' => $bankLedgerInfo->ledgBank_id])->delete();
                }
            } elseif ($bankAmount > 0) {
                $lgBankData = [
                    'sch_id'           => $shopId,
                    'bank_id'          => $targetBankId,
                    'purchase_id'      => $purchaseId,
                    'particulars'      => 'Purchase Bank Pay',
                    'trangaction_type' => 'Cr.',
                    'amount'           => $bankAmount,
                    'rest_balance'     => !empty($upBankCash) ? $upBankCash : 0,
                    'createdBy'        => $userId,
                    'createdDtm'       => date('Y-m-d H:i:s')
                ];
                DB()->table('ledger_bank')->insert($lgBankData);
                $ledgBank_id = DB()->insertID();
                $this->transactionLog->insert_log_data('ledger_bank', $ledgBank_id, '', $bankAmount, '', '', '', $purchaseId);
            }
        }

        $number = !empty($prod_id) && is_array($prod_id) ? count($prod_id) : 0;
        for ($i = 0; $i < $number; $i++) {
            $itemId = (!empty($purchase_item_id) && !empty($purchase_item_id[$i])) ? $purchase_item_id[$i] : null;

            if ($itemId) {
                $purchaseItem = DB()->table('purchase_item')->where('purchase_item_id', $itemId)->get()->getRow();
            } else {
                $purchaseItem = DB()->table('purchase_item')->where('purchase_id', $purchaseId)->where('prod_id', $prod_id[$i])->get()->getRow();
            }

            if (!empty($purchaseItem)) {
                $purchaseDate = null;
                if (!empty($purchaseItem->product_stock_relation_id)) {
                    $purchaseDate = DB()->table('product_stock_relation')->where('product_stock_relation_id', $purchaseItem->product_stock_relation_id)->get()->getRow();
                }

                if (empty($purchaseDate)) {
                    $purchaseDate = DB()->table('product_stock_relation')
                        ->where('sch_id', $shopId)
                        ->where('product_id', $prod_id[$i])
                        ->get()
                        ->getRow();
                    if (!empty($purchaseDate)) {
                        DB()->table('purchase_item')->where('purchase_item_id', $purchaseItem->purchase_item_id)->update(['product_stock_relation_id' => $purchaseDate->product_stock_relation_id]);
                    }
                }

                if (!empty($purchaseDate)) {
                    $oldQty = (float)$purchaseItem->quantity;
                    $newQty = (float)$qty[$i];
                    $currentStock = (float)$purchaseDate->quantity;
                    $updatedStock = ($currentStock - $oldQty) + $newQty;

                    $dataQty = array(
                        'quantity' => $updatedStock,
                        'purchase_price' => $price[$i],
                        'selling_price' => !empty($salePrice[$i]) ? $salePrice[$i] : $purchaseDate->selling_price,
                    );
                    DB()->table('product_stock_relation')->where('product_stock_relation_id', $purchaseDate->product_stock_relation_id)->update($dataQty);
                }

                //update purchase Item in purchase item table
                $purchaseData = array(
                    'purchase_price' => $price[$i],
                    'quantity' => $qty[$i],
                    'total_price' => $total_price[$i],
                    'updatedBy' => $userId,
                );
                DB()->table('purchase_item')->where('purchase_item_id', $purchaseItem->purchase_item_id)->update($purchaseData);

                // Update purchase_item in transaction_log if exists
                $itemLog = DB()->table('transaction_log')
                    ->where('purchase_id', $purchaseId)
                    ->where('table_name', 'purchase_item')
                    ->where('id', $purchaseItem->purchase_item_id)
                    ->get()
                    ->getRow();
                if (!empty($itemLog)) {
                    $this->transactionLog->transaction_log_data_update($itemLog->transaction_log_id, $total_price[$i]);
                }
            }
        }
        //purchase product insert in product table and purchase item table (end)

        // Update purchase in transaction_log if exists
        $purLog = DB()->table('transaction_log')
            ->where('purchase_id', $purchaseId)
            ->where('table_name', 'purchase')
            ->get()
            ->getRow();
        if (!empty($purLog)) {
            $this->transactionLog->transaction_log_data_update($purLog->transaction_log_id, $totalPrice);
        }


        //purchase all pay amount detail Update in purchase table(start)
        $parsData = array(
            'amount' => $totalPrice,
            'nagad_paid' => $cashAmount,
            'bank_paid' => $bankAmount,
            'bank_id' => $bankId,
            'due' => $totalDue,
        );
        $purchaseTab = DB()->table('purchase');
        $purchaseTab->where('purchase_id', $purchaseId)->update($parsData);
        //purchase all pay amount detail Update in purchase table(end)

        DB()->transComplete();

        $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Create Record Success<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        return redirect()->to(site_url('Admin/Purchase'));
    }

    private function bank_ledger_rest_balance_update($bankId,$amount,$ledgBankId,$ledgerAmount)
    {
        // Get a reference to the 'ledger' table using the query builder
        $ledgerTable = DB()->table('ledger_bank');
        $result = $ledgerTable->where('ledgBank_id >', $ledgBankId)->where('bank_id', $bankId)->get()->getResult();

        // Initialize an array to store updated ledger data
        $arrayUpData = [];
        foreach ($result as $val) {
            if ($val->trangaction_type == 'Dr.') {
                $data['ledgBank_id']  = $val->ledgBank_id;
                $data['rest_balance'] = ($val->rest_balance - $ledgerAmount) + $amount;
                array_push($arrayUpData, $data);
            } else {
                $data['ledgBank_id']  = $val->ledgBank_id;
                $data['rest_balance'] = ($val->rest_balance + $ledgerAmount) - $amount;
                array_push($arrayUpData, $data);
            }
        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_bank');
            $table->updateBatch($arrayUpData, 'ledgBank_id');
        }
    }
    private function cash_ledger_rest_balance_update($shopId,$amount,$ledgNagodanId,$ledgerAmount)
    {
        // Get a reference to the 'ledger' table using the query builder
        $ledgerTable = DB()->table('ledger_nagodan');
        $result = $ledgerTable->where('ledg_nagodan_id >', $ledgNagodanId)->where('sch_id', $shopId)->get()->getResult();

        // Initialize an array to store updated ledger data
        $arrayUpData = [];
        foreach ($result as $val) {
            if ($val->trangaction_type == 'Dr.') {
                $data['ledg_nagodan_id']  = $val->ledg_nagodan_id;
                $data['rest_balance'] = ($val->rest_balance - $ledgerAmount) + $amount;
                array_push($arrayUpData, $data);
            } else {
                $data['ledg_nagodan_id']  = $val->ledg_nagodan_id;
                $data['rest_balance'] = ($val->rest_balance + $ledgerAmount) - $amount;
                array_push($arrayUpData, $data);
            }
        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_nagodan');
            $table->updateBatch($arrayUpData, 'ledg_nagodan_id');
        }
    }
    private function suppliers_ledger_rest_balance_update($supplier_id,$amount,$ledg_sup_id,$ledgerAmount)
    {
        // Get a reference to the 'ledger' table using the query builder
        $ledgerTable = DB()->table('ledger_suppliers');
        // Query all ledger entries for the same suppliers where the ledger ID is greater than the current transaction's ID
        $result = $ledgerTable->where('ledg_sup_id >', $ledg_sup_id)->where('supplier_id', $supplier_id)->get()->getResult();

        // Initialize an array to store updated ledger data
        $arrayUpData = [];
        foreach ($result as $val) {
            if ($val->trangaction_type == 'Dr.') {
                $data['ledg_sup_id']  = $val->ledg_sup_id;
                $data['rest_balance'] = ($val->rest_balance - $ledgerAmount) + $amount;
                array_push($arrayUpData, $data);
            } else {
                $data['ledg_sup_id']  = $val->ledg_sup_id;
                $data['rest_balance'] = ($val->rest_balance + $ledgerAmount) - $amount;
                array_push($arrayUpData, $data);
            }
        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_suppliers');
            $table->updateBatch($arrayUpData, 'ledg_sup_id');
        }
    }
    private function purchase_ledger_rest_balance_update($purchaseId,$amount)
    {
        // Get the specific ledger log entry using a helper function that returns table data by $purchaseId
        $ledLog = $this->transactionLog->get_table_name_by_row_purchase_id('ledger_purchase', $purchaseId);

        // Get a reference to the 'ledger' table using the query builder
        $ledgerTable = DB()->table('ledger_purchase');

        // Query all ledger entries is greater than the current transaction's ID
        $result = $ledgerTable->where('ledgPurch_id >', $ledLog->id)->get()->getResult();

        // Initialize an array to store updated ledger data
        $arrayUpData = [];
        foreach ($result as $val) {
            $data['ledgPurch_id']  = $val->ledgPurch_id;
            $data['rest_balance'] = ($val->rest_balance - $ledLog->amount) + $amount;
            array_push($arrayUpData, $data);
        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_purchase');
            $table->updateBatch($arrayUpData, 'ledgPurch_id');
        }
    }
    private function stock_ledger_rest_balance_update($purchaseId,$amount)
    {
        // Get the specific ledger log entry using a helper function that returns table data by $purchaseId
        $ledLog = $this->transactionLog->get_table_name_by_row_purchase_id('ledger_stock', $purchaseId);

        // Get a reference to the 'ledger' table using the query builder
        $ledgerTable = DB()->table('ledger_stock');
        // Query all ledger entries is greater than the current transaction's ID
        $result = $ledgerTable->where('stock_id >', $ledLog->id)->get()->getResult();

        // Initialize an array to store updated ledger data
        $arrayUpData = [];
        foreach ($result as $val) {
            $data['stock_id']  = $val->stock_id;
            $data['rest_balance'] = ($val->rest_balance - $ledLog->amount) + $amount;
            array_push($arrayUpData, $data);
        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_stock');
            $table->updateBatch($arrayUpData, 'stock_id');
        }
    }

    public function transaction_flow($purchase_id){
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;

            $data['flow'] = DB()->table('transaction_entries')
                ->where('purchase_id',$purchase_id)
                ->get()
                ->getResult();

            $data['purchaseData'] = DB()->table('purchase')
                ->join('suppliers', 'suppliers.supplier_id = purchase.supplier_id')
                ->where('purchase.purchase_id', $purchase_id)
                ->get()
                ->getRow();

            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Purchase/transaction_flow', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    public function unitShow(){
        $unit_set_id = $this->request->getPost('unit_set_id');
        $query = DB()->table('unit_set')->where('unit_set_id',$unit_set_id)->get()->getRow();

        $units_id = json_decode($query->purchase_units);
        $data['units'] = DB()->table('units')->whereIn('units_id',$units_id)->orderBy('conversion_factor', 'DESC')->get()->getResult();

        $array = json_decode($query->sell_units);
        $lastKey = array_key_last($array);
        $data['saleUnit'] = DB()->table('units')->where('units_id',$array[$lastKey])->get()->getRow();
        $data['unitsArray'] = DB()->table('units')->where('unit_categories_id',$query->unit_categories_id)->orderBy('conversion_factor', 'DESC')->get()->getResult();

        $data['unitSet'] = $query;
        $data['noPurchase'] = $this->request->getPost('noPurchase');

        echo view('Admin/Purchase/unitShow', $data);
    }

    public function purchase_product(){
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;
            $data['action'] = base_url('Admin/Purchase/purchase_product_action');
            if (!empty($this->session->cartType) && $this->session->cartType !== 'purchaseNew') {
                $this->cart->destroy();
            }
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Purchase/purchase_product', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    public function search_prod()
    {
        $shopId = $this->session->shopId;

        $keyWord = $this->request->getPost("keyWord");

        $storeTab = DB()->table('stores');
        $store = $storeTab->where('sch_id', $shopId)->where('is_default', 1)->get()->getRow();

        $proTable = DB()->table('products');
        $data = $proTable
            ->join('product_stock_relation', 'product_stock_relation.product_id = products.prod_id')
            ->where('products.sch_id', $shopId)
            ->where('product_stock_relation.store_id', $store->store_id)
//            ->where('product_stock_relation.is_default', '1')
            ->groupBy('products.prod_id')
            ->groupStart()
            ->like('products.name', $keyWord)
            ->orLike('products.prod_id', $keyWord)
            ->groupEnd()
            ->get()
            ->getResult();


        $view = '';
        foreach ($data as $sval) {
            $image = ($sval->picture == NULL) ? 'no_image.jpg' : $sval->picture;
            $unit = $sval->unit;
            $qty = $sval->quantity;

            $availQty = unitOrQtyByUnitQty($unit,$qty);
            $purchasePrice = unitOrBasePriceByUnitPrice($unit,$sval->purchase_price);

            $units_id = json_decode($sval->purchase_units);
            $unitsArray = DB()->table('units')->whereIn('units_id',$units_id)->orderBy('conversion_factor', 'DESC')->get()->getResult();

            $view .= '<li>
                        <form action="' . site_url('Admin/Purchase/add_cart') . '" method="post">
                            <div class="col-xs-12" style="padding:15px; border-bottom: 1px solid;color: #d2d6de;" ><a>
                            <div class="col-xs-2">
                                <img class="img-circle" src="' . base_url() . '/uploads/product_image/' . $image . '" width="60" height="60">
                            </div>
                            <div class="col-xs-4"><label for="usr">Name /Price:</label><h4 style="color:black;">' . $sval->name . '/' . showWithCurrencySymbol($purchasePrice) . 'Tk. </h4><p>Available Quantity: '.$availQty.'/'.showUnitName($unit).'</p><input class="form-control" type="hidden" readonly id="name" name="name" value="' . $sval->name . '"><input class="form-control" type="hidden" readonly id="price" name="price" value="' . $sval->purchase_price . '"><input class="form-control" type="hidden" readonly id="sale_price" name="sale_price" value="' . $sval->selling_price . '"><input class="form-control" type="hidden" readonly id="prod_id" name="prod_id" value="' . $sval->prod_id . '"></div>
                            <div class="col-md-5 row">';
            foreach ($unitsArray as $val){
                $view .='<div class="form-group col-xs-6">
                        <label for="int" class="text-capitalize">'. $val->name.' </label>
                        <input type="text" class="form-control" name="'. strtolower(str_replace(' ', '_', $val->name)).'" placeholder="'. $val->name.'" value="" >
                     </div>';
            }
            $view .='</div>';

            $view .='<div class="col-xs-1" >
                        <span>Category:</span><br><h4 style="color:black;">' . get_data_by_id('product_category', 'product_category', 'prod_cat_id', $sval->prod_cat_id) . '</h4>
                                <button  type="subbmit" class="add_cart btn btn-success btn-xs" >Add</button>
                            </div></a></div>
                        </form>
                     </li>';

        }
        echo $view;

    }

    public function add_cart()
    {

        $shopId = $this->session->shopId;

        $proId = $this->request->getPost('prod_id');
        $proName = $this->request->getPost('name');
        $proPrice = $this->request->getPost('price');
        $salePrice = $this->request->getPost('sale_price');


        $qty = 0;
        foreach ($this->cart->contents() as $row) {
            if ($proId == $row['id']) {
                $qty = $row['qty'];
            }
        }

        //unit qty
        $unitIdJson = get_data_by_id('purchase_units', 'products', 'prod_id', $proId);
        $units_id = json_decode($unitIdJson);
        $unitQty = 0;
        $unit = array();
        $units = DB()->table('units')->whereIn('units_id', $units_id)->orderBy('conversion_factor', 'DESC')->get()->getResult();
        foreach ($units as $val) {
            $nameUnit = strtolower(str_replace(' ', '_', $val->name));
            $unit[$nameUnit] = $this->request->getPost($nameUnit);
            if (!empty($unit[$nameUnit])) {
                $unitQty += $val->conversion_factor * $unit[$nameUnit];
            }
        }

        $totalquantity = $unitQty + $qty;

        if ($totalquantity > 0) {
            $data = array(
                'id' => $proId,
                'name' => strval($proName),
                'qty' => $totalquantity,
                'price' => $proPrice,
                'sale_price' => $salePrice
            );
            $this->cart->insert($data);
            $this->session->set('cartType', 'purchaseNew');
        } else {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert"> Invalid Quantity  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Purchase/purchase_product'));
        }
        return redirect()->to(site_url('Admin/Purchase/purchase_product'));
    }

    public function removeCart($id)
    {
        $this->cart->remove($id);
        return redirect()->to(site_url('Admin/Purchase/purchase_product'));
    }
    public function purchase_product_action(){
        $userId = $this->session->userId;
        $shopId = $this->session->shopId;

        $date = $this->request->getPost('date');
        $supplierId = $this->request->getPost('supplier_id');
        $sms = $this->request->getPost('sms');

        $totalPrice = str_replace(',', '', $this->request->getPost('totalPrice'));
        $cashAmount = str_replace(',', '', $this->request->getPost('cash'));
        $bankAmount = str_replace(',', '', $this->request->getPost('bank'));
        $dueAmount = str_replace(',', '', $this->request->getPost('due'));
        $bankId = $this->request->getPost('bank_id');

        $productId = $this->request->getPost('productId[]');

        if (!empty($this->cart->contents())) {
            $number = count($productId);

            $cashbal = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
            $bankbal = get_data_by_id('balance', 'bank', 'bank_id', $bankId);

            if ($cashbal < $cashAmount) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Not enough balance! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Purchase/purchase_product'));
            }

            if ($bankbal < $bankAmount) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Not enough balance! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Purchase/purchase_product'));
            }

            if (!empty($cashAmount) && $cashAmount < 0) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Purchase/purchase_product'));
            }

            if (!empty($bankAmount) && $bankAmount < 0) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Purchase/purchase_product'));
            }
            if (empty($supplierId)) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Supplier is required!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Purchase/purchase_product'));
            }

            DB()->transStart();

            DB()->table('purchase')->insert([
                'sch_id' => $shopId,
                'supplier_id' => $supplierId,
                'date' => $date,
                'createdBy' => $userId,
            ]);
            $purchaseId = DB()->insertID();

            //purshase total price calculet to suppliers balance and update suppliers balance or create suppliers ledger (start)
            $supplierCash = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
            $newCash = $supplierCash - $totalPrice;

            $suppData = array(
                'balance' => $newCash,
                'updatedBy' => $userId,
            );
            $suppliersTable = DB()->table('suppliers');
            $suppliersTable->where('supplier_id', $supplierId)->update($suppData);

            //insert log (start)
            $this->transactionLog->insert_log_data('suppliers',$supplierId,'',$totalPrice,'','','',$purchaseId);
            //insert log (end)

            //create suppliers ledger
            $lgSuplData = array(
                'sch_id' => $shopId,
                'supplier_id' => $supplierId,
                'purchase_id' => $purchaseId,
                'trangaction_type' => 'Cr.',
                'particulars' => 'Purchase Cash Due',
                'amount' => $totalPrice,
                'rest_balance' => $newCash,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_suppliersTable = DB()->table('ledger_suppliers');
            $ledger_suppliersTable->insert($lgSuplData);
            $ledg_sup_id = DB()->insertID();
            //purshase total price calculet to suppliers balance and update suppliers balance or create suppliers ledger (end)

            //Insert Purchase Transaction Entries
            $this->purchase_transaction_entries($purchaseId, $ledg_sup_id, 'ledger_suppliers', 'Cr.');

            //insert log (start)
            $this->transactionLog->insert_log_data('ledger_suppliers',$ledg_sup_id,'',$totalPrice,'','','',$purchaseId);
            //insert log (end)

            // purchase balance update and ledger create (start)
            $purchaseBal = get_data_by_id('purchase_balance', 'shops', 'sch_id', $shopId);
            $restBalPurc = $purchaseBal + $totalPrice;


            $purUpdata = array('purchase_balance' => $restBalPurc);
            $shopPurBalTable = DB()->table('shops');
            $shopPurBalTable->where('sch_id', $shopId)->update($purUpdata);

            //insert log (start)
            $this->transactionLog->insert_log_data('shops',$shopId,'',$totalPrice,'','','',$purchaseId,'purchase_balance');
            //insert log (end)

            $purLedgData = array(
                'sch_id' => $shopId,
                'purchase_id' => $purchaseId,
                'trangaction_type' => 'Dr.',
                'particulars' => 'New purchase amount',
                'amount' => $totalPrice,
                'rest_balance' => $restBalPurc,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_purchaseTable = DB()->table('ledger_purchase');
            $ledger_purchaseTable->insert($purLedgData);
            $ledgPurch_id = DB()->insertID();
            // purchase balance update and ledger create (end)

            //Insert Purchase Transaction Entries
            $this->purchase_transaction_entries($purchaseId, $ledgPurch_id, 'ledger_purchase', 'Dr.');

            //insert log (start)
            $this->transactionLog->insert_log_data('ledger_purchase',$ledgPurch_id,'',$totalPrice,'','','',$purchaseId);
            //insert log (end)

            // stock balance update and ledger create (start)
            $stockBal = get_data_by_id('stockAmount', 'shops', 'sch_id', $shopId);
            $restBalStock = $stockBal + $totalPrice;


            $stockUpdata = array('stockAmount' => $restBalStock);
            $shopStoAmTable = DB()->table('shops');
            $shopStoAmTable->where('sch_id', $shopId)->update($stockUpdata);

            //insert log (start)
            $this->transactionLog->insert_log_data('shops',$shopId,'',$totalPrice,'','','',$purchaseId,'stockAmount');
            //insert log (end)

            $stockLedgData = array(
                'sch_id' => $shopId,
                'purchase_id' => $purchaseId,
                'trangaction_type' => 'Dr.',
                'particulars' => 'New purchase amount',
                'amount' => $totalPrice,
                'rest_balance' => $restBalStock,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_stockTable = DB()->table('ledger_stock');
            $ledger_stockTable->insert($stockLedgData);
            $stock_id = DB()->insertID();
            // stock balance update and ledger create (end)

            //Insert Purchase Transaction Entries
            $this->purchase_transaction_entries($purchaseId, $stock_id, 'ledger_stock', 'Dr.');

            //insert log (start)
            $this->transactionLog->insert_log_data('ledger_stock',$stock_id,'',$totalPrice,'','','',$purchaseId);
            //insert log (end)

            if ($cashAmount > 0) {

                //purchase pay cash amount calculate to shops cash and update shops cash or create ledger_nagodan statement in ledger_nagodan table (start)
                $shopsCash = get_data_by_id('cash', 'shops', 'sch_id', $shopId);

                if ($shopsCash >= $cashAmount) {
                    $upCahs = $shopsCash - $cashAmount;
                    $shopsData = array(
                        'cash' => $upCahs,
                        'updatedBy' => $userId,
                    );
                    $shopCasTable = DB()->table('shops');
                    $shopCasTable->where('sch_id', $shopId)->update($shopsData);
                    //insert log (start)
                    $this->transactionLog->insert_log_data('shops',$shopId,'',$cashAmount,'','','',$purchaseId);
                    //insert log (end)

                    //nagodan ledger create
                    $lgNagData = array(
                        'sch_id' => $shopId,
                        'purchase_id' => $purchaseId,
                        'particulars' => 'Purchase Cash Pay',
                        'trangaction_type' => 'Cr.',
                        'amount' => $cashAmount,
                        'rest_balance' => $upCahs,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_nagodanTable = DB()->table('ledger_nagodan');
                    $ledger_nagodanTable->insert($lgNagData);
                    $ledg_nagodan_id = DB()->insertID();
                    //purshase pay cash amount calculet to shops cash and update shops cash or create ledger_nagodan statment in ledger_nagodan table (end)

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledg_nagodan_id, 'ledger_nagodan', 'Cr.');

                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger_nagodan',$ledg_nagodan_id,'',$cashAmount,'','','',$purchaseId);
                    //insert log (end)

                    //purshase pay cash amount calculet to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (start)
                    $supplierccCash = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
                    $suppCash = $supplierccCash + $cashAmount;

                    $cashsuppData = array(
                        'balance' => $suppCash,
                        'updatedBy' => $userId,
                    );
                    $suppliersTab = DB()->table('suppliers');
                    $suppliersTab->where('supplier_id', $supplierId)->update($cashsuppData);
                    //insert log (start)
                    $this->transactionLog->insert_log_data('suppliers',$supplierId,'',$cashAmount,'','','',$purchaseId);
                    //insert log (end)

                    //suppliers ledger create
                    $lgSuplData = array(
                        'sch_id' => $shopId,
                        'supplier_id' => $supplierId,
                        'purchase_id' => $purchaseId,
                        'particulars' => 'Purchase Cash Pay',
                        'trangaction_type' => 'Dr.',
                        'amount' => $cashAmount,
                        'rest_balance' => $suppCash,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_suppliersTab = DB()->table('ledger_suppliers');
                    $ledger_suppliersTab->insert($lgSuplData);
                    $ledg_sup_id = DB()->insertID();

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledg_sup_id, 'ledger_suppliers', 'Dr.');

                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger_suppliers',$ledg_sup_id,'',$cashAmount,'','','',$purchaseId);
                    //insert log (end)
                }
                //purchase pay cash amount calculate to shops cash and update shops cash or create ledger_nagodan statement in ledger_nagodan table (end)
            }


            if ($bankAmount > 0) {

                //purshase pay bank amount calculet to bank balance and update bank balance or create bank ledger in ledger_bank table (start)
                $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $bankId);

                if ($bankCash >= $bankAmount) {

                    $upCahs = $bankCash - $bankAmount;
                    $bankData = array(
                        'balance' => $upCahs,
                        'updatedBy' => $userId,
                    );
                    $bankTable = DB()->table('bank');
                    $bankTable->where('bank_id', $bankId)->update($bankData);
                    //insert log (start)
                    $this->transactionLog->insert_log_data('bank',$bankId,'',$bankAmount,'','','',$purchaseId);
                    //insert log (end)

                    //bank ledger create
                    $lgBankData = array(
                        'sch_id' => $shopId,
                        'bank_id' => $bankId,
                        'purchase_id' => $purchaseId,
                        'trangaction_type' => 'Cr.',
                        'particulars' => 'Purchase Bank Pay',
                        'amount' => $bankAmount,
                        'rest_balance' => $upCahs,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_bankTab = DB()->table('ledger_bank');
                    $ledger_bankTab->insert($lgBankData);
                    $ledgBank_id = DB()->insertID();
                    //purshase pay bank amountcalculet to bank balance and update bank balance or create bank ledger in ledger_bank table (end)

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledgBank_id, 'ledger_bank', 'Cr.');

                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger_bank',$ledgBank_id,'',$bankAmount,'','','',$purchaseId);
                    //insert log (end)

                    //purshase pay bank amount calculet to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (start)
                    $supplierbbCash = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
                    $suppBaCash = $supplierbbCash + $bankAmount;

                    $banksuppData = array(
                        'balance' => $suppBaCash,
                        'updatedBy' => $userId,
                    );
                    $suppliersTab = DB()->table('suppliers');
                    $suppliersTab->where('supplier_id', $supplierId)->update($banksuppData);
                    //insert log (start)
                    $this->transactionLog->insert_log_data('suppliers',$supplierId,'',$bankAmount,'','','',$purchaseId);
                    //insert log (end)

                    //suppliers ledger create
                    $lgSuplData = array(
                        'sch_id' => $shopId,
                        'supplier_id' => $supplierId,
                        'purchase_id' => $purchaseId,
                        'particulars' => 'Purchase Bank Pay',
                        'trangaction_type' => 'Dr.',
                        'amount' => $bankAmount,
                        'rest_balance' => $suppBaCash,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_suppliersTab = DB()->table('ledger_suppliers');
                    $ledger_suppliersTab->insert($lgSuplData);
                    $ledg_sup_id = DB()->insertID();

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledg_sup_id, 'ledger_suppliers', 'Dr.');

                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger_suppliers',$ledg_sup_id,'',$bankAmount,'','','',$purchaseId);
                    //insert log (end)
                }
                //purshase pay bank amount calculet to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (end)

            }


            //purchase product insert in product table and purchase item table (start)
            $storeTab = DB()->table('stores');
            $store = $storeTab->where('sch_id', $shopId)->where('is_default', 1)->get()->getRow();
            $storeId = $store->store_id;

            for ($i = 0; $i < $number; $i++) {
                $prodId = $productId[$i];
                //insert log (start)
                $purchasePrice = $this->request->getPost('price[]')[$i];
                $sellingPrice = $this->request->getPost('salePrice[]')[$i];
                $quantity = $this->request->getPost('qty[]')[$i];

                $oldStock = DB()->table('product_stock_relation')->where('sch_id',$shopId)->where('store_id',$storeId)->where('product_id',$prodId)->where('purchase_price',$purchasePrice)->where('selling_price',$sellingPrice)->where('is_default','0')->countAllResults();
                $product_stock_relation_id = 0;
                if (empty($oldStock)) {
                    //insert data in table product_stock_relation
                    $dataQty = array(
                        'sch_id' => $shopId,
                        'store_id' => $storeId,
                        'purchase_id' => $purchaseId,
                        'product_id' => $prodId,
                        'supplier_id' => $supplierId,
                        'quantity' => $quantity,
                        'unit' => $this->request->getPost('unit[]')[$i],
                        'purchase_price' => $purchasePrice,
                        'selling_price' => $sellingPrice,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $stockRelationTable = DB()->table('product_stock_relation');
                    $stockRelationTable->insert($dataQty);
                    $product_stock_relation_id = DB()->insertID();
                    //insert data in table product_stock_relation
                }else{
                    $stock = DB()->table('product_stock_relation')->where('sch_id',$shopId)->where('store_id',$storeId)->where('product_id',$prodId)->where('purchase_price',$purchasePrice)->where('selling_price',$sellingPrice)->where('is_default','0')->get()->getRow();
                    $dataQty['quantity'] = $stock->quantity + $quantity;
                    $dataQty['supplier_id'] = $supplierId;
                    DB()->table('product_stock_relation')->where('product_stock_relation_id',$stock->product_stock_relation_id)->update($dataQty);
                    $product_stock_relation_id = $stock->product_stock_relation_id;
                }

                //insert purchase Item in purchase item table
                $total_price = $quantity * $purchasePrice;

                $purchaseData = array(
                    'purchase_id' => $purchaseId,
                    'prod_id' => $prodId,
                    'product_stock_relation_id' => $product_stock_relation_id,
                    'purchase_price' => $purchasePrice,
                    'quantity' => $quantity,
                    'total_price' => $total_price,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $purchase_itemTab = DB()->table('purchase_item');
                $purchase_itemTab->insert($purchaseData);
                $purchase_item_id = DB()->insertID();
                //insert log (start)
                $this->transactionLog->insert_log_data('purchase_item',$purchase_item_id,'',$total_price,'','','',$purchaseId);
                //insert log (end)
            }
            //purchase product insert in product table and purchase item table (end)


            //purchase all pay amount detail Update in purchase table(start)
            $parsData = array(
                'amount' => $totalPrice,
                'nagad_paid' => $cashAmount,
                'bank_paid' => $bankAmount,
                'bank_id' => $bankId,
                'due' => $dueAmount,
            );
            $purchaseTab = DB()->table('purchase');
            $purchaseTab->where('purchase_id', $purchaseId)->update($parsData);
            //purchase all pay amount detail Update in purchase table(end)
            //insert log (start)
            $this->transactionLog->insert_log_data('purchase',$purchaseId,'',$totalPrice,'','','',$purchaseId);
            //insert log (end)

            DB()->transComplete();
            if(!empty($sms)) {
                $message = 'Thank you for your purchase.Your purchase amount is-' . $totalPrice;
                $phone = get_data_by_id('phone', 'suppliers', 'supplier_id', $supplierId);
                send_sms($phone, $message);
            }

            $this->cart->destroy();

            $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Create Record Success<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Purchase'));
        } else {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Your cart is empty! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Purchase/new_product'));
        }
    }
    public function purchase_return_action()
    {
        $userId  = $this->session->userId;
        $shopId  = $this->session->shopId;

        $date        = $this->request->getPost('date');
        $supplierId  = $this->request->getPost('supplier_id');
        $sms         = $this->request->getPost('sms');
        $purchaseId  = $this->request->getPost('purchase_id'); // original purchase to return against

        $totalPrice  = str_replace(',', '', $this->request->getPost('totalPrice'));
        $cashAmount  = str_replace(',', '', $this->request->getPost('cash'));
        $bankAmount  = str_replace(',', '', $this->request->getPost('bank'));
        $dueAmount   = str_replace(',', '', $this->request->getPost('due'));
        $bankId      = $this->request->getPost('bank_id');

        $productId   = $this->request->getPost('productId[]');

        if (empty($purchaseId) || empty($supplierId)) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Purchase & Supplier are required!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Purchase/purchase_return'));
        }

        if (empty($this->cart->contents())) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Your cart is empty!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Purchase/purchase_return'));
        }

        $number = count($productId);

        // Basic validation (same spirit as original)
        if (!empty($cashAmount) && $cashAmount < 0) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Purchase/purchase_return'));
        }
        if (!empty($bankAmount) && $bankAmount < 0) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Purchase/purchase_return'));
        }

        DB()->transStart();

        // -------------------------------------------------
        // 1. Create Purchase Return master record
        // -------------------------------------------------
        DB()->table('purchase_return')->insert([
            'sch_id'       => $shopId,
            'purchase_id'  => $purchaseId,
            'supplier_id'  => $supplierId,
            'date'         => $date,
            'createdBy'    => $userId,
        ]);
        $returnId = DB()->insertID();

        // -------------------------------------------------
        // 2. Reverse Supplier balance (original was -totalPrice)
        //    → now +totalPrice  + Cr → Dr ledger
        // -------------------------------------------------
        $supplierCash = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
        $newCash      = $supplierCash + $totalPrice;   // reverse of original -

        DB()->table('suppliers')
            ->where('supplier_id', $supplierId)
            ->update([
                'balance'   => $newCash,
                'updatedBy' => $userId,
            ]);

        $this->transactionLog->insert_log_data('suppliers', $supplierId, '', $totalPrice, '', '', '', $returnId);

        $lgSuplData = [
            'sch_id'           => $shopId,
            'supplier_id'      => $supplierId,
            'purchase_id'      => $purchaseId,
            'purchase_return_id'=> $returnId,
            'trangaction_type' => 'Dr.',                 // reverse of original Cr.
            'particulars'      => 'Purchase Return',
            'amount'           => $totalPrice,
            'rest_balance'     => $newCash,
            'createdBy'        => $userId,
            'createdDtm'       => date('Y-m-d h:i:s'),
        ];
        DB()->table('ledger_suppliers')->insert($lgSuplData);
        $ledg_sup_id = DB()->insertID();

        $this->purchase_transaction_entries($returnId, $ledg_sup_id, 'ledger_suppliers', 'Dr.');
        $this->transactionLog->insert_log_data('ledger_suppliers', $ledg_sup_id, '', $totalPrice, '', '', '', $returnId);

        // -------------------------------------------------
        // 3. Reverse Purchase Balance (original +totalPrice → now -)
        // -------------------------------------------------
        $purchaseBal  = get_data_by_id('purchase_balance', 'shops', 'sch_id', $shopId);
        $restBalPurc  = $purchaseBal - $totalPrice;

        DB()->table('shops')
            ->where('sch_id', $shopId)
            ->update(['purchase_balance' => $restBalPurc]);

        $this->transactionLog->insert_log_data('shops', $shopId, '', $totalPrice, '', '', '', $returnId, 'purchase_balance');

        $purLedgData = [
            'sch_id'           => $shopId,
            'purchase_id'      => $purchaseId,
            'purchase_return_id'=> $returnId,
            'trangaction_type' => 'Cr.',                 // reverse of original Dr.
            'particulars'      => 'Purchase Return amount',
            'amount'           => $totalPrice,
            'rest_balance'     => $restBalPurc,
            'createdBy'        => $userId,
            'createdDtm'       => date('Y-m-d h:i:s'),
        ];
        DB()->table('ledger_purchase')->insert($purLedgData);
        $ledgPurch_id = DB()->insertID();

        $this->purchase_transaction_entries($returnId, $ledgPurch_id, 'ledger_purchase', 'Cr.');
        $this->transactionLog->insert_log_data('ledger_purchase', $ledgPurch_id, '', $totalPrice, '', '', '', $returnId);

        // -------------------------------------------------
        // 4. Reverse Stock Amount (original +totalPrice → now -)
        // -------------------------------------------------
        $stockBal     = get_data_by_id('stockAmount', 'shops', 'sch_id', $shopId);
        $restBalStock = $stockBal - $totalPrice;

        DB()->table('shops')
            ->where('sch_id', $shopId)
            ->update(['stockAmount' => $restBalStock]);

        $this->transactionLog->insert_log_data('shops', $shopId, '', $totalPrice, '', '', '', $returnId, 'stockAmount');

        $stockLedgData = [
            'sch_id'           => $shopId,
            'purchase_id'      => $purchaseId,
            'purchase_return_id'=> $returnId,
            'trangaction_type' => 'Cr.',                 // reverse of original Dr.
            'particulars'      => 'Purchase Return amount',
            'amount'           => $totalPrice,
            'rest_balance'     => $restBalStock,
            'createdBy'        => $userId,
            'createdDtm'       => date('Y-m-d h:i:s'),
        ];
        DB()->table('ledger_stock')->insert($stockLedgData);
        $stock_id = DB()->insertID();

        $this->purchase_transaction_entries($returnId, $stock_id, 'ledger_stock', 'Cr.');
        $this->transactionLog->insert_log_data('ledger_stock', $stock_id, '', $totalPrice, '', '', '', $returnId);

        // -------------------------------------------------
        // 5. Cash payment reverse (if any)
        //    Original: cash ↓, supplier ↑
        //    Return  : cash ↑, supplier ↓
        // -------------------------------------------------
        if ($cashAmount > 0) {
            $shopsCash = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
            $upCash    = $shopsCash + $cashAmount;   // reverse of original -

            DB()->table('shops')
                ->where('sch_id', $shopId)
                ->update([
                    'cash'      => $upCash,
                    'updatedBy' => $userId,
                ]);

            $this->transactionLog->insert_log_data('shops', $shopId, '', $cashAmount, '', '', '', $returnId);

            // Nagodan ledger (reverse)
            $lgNagData = [
                'sch_id'           => $shopId,
                'purchase_id'      => $purchaseId,
                'purchase_return_id'=> $returnId,
                'particulars'      => 'Purchase Return Cash Receive',
                'trangaction_type' => 'Dr.',             // reverse of original Cr.
                'amount'           => $cashAmount,
                'rest_balance'     => $upCash,
                'createdBy'        => $userId,
                'createdDtm'       => date('Y-m-d h:i:s'),
            ];
            DB()->table('ledger_nagodan')->insert($lgNagData);
            $ledg_nagodan_id = DB()->insertID();

            $this->purchase_transaction_entries($returnId, $ledg_nagodan_id, 'ledger_nagodan', 'Dr.');
            $this->transactionLog->insert_log_data('ledger_nagodan', $ledg_nagodan_id, '', $cashAmount, '', '', '', $returnId);

            // Supplier balance reverse for cash
            $supplierccCash = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
            $suppCash       = $supplierccCash - $cashAmount;  // reverse of original +

            DB()->table('suppliers')
                ->where('supplier_id', $supplierId)
                ->update([
                    'balance'   => $suppCash,
                    'updatedBy' => $userId,
                ]);

            $this->transactionLog->insert_log_data('suppliers', $supplierId, '', $cashAmount, '', '', '', $returnId);

            $lgSuplData = [
                'sch_id'           => $shopId,
                'supplier_id'      => $supplierId,
                'purchase_id'      => $purchaseId,
                'purchase_return_id'=> $returnId,
                'particulars'      => 'Purchase Return Cash Receive',
                'trangaction_type' => 'Cr.',             // reverse of original Dr.
                'amount'           => $cashAmount,
                'rest_balance'     => $suppCash,
                'createdBy'        => $userId,
                'createdDtm'       => date('Y-m-d h:i:s'),
            ];
            DB()->table('ledger_suppliers')->insert($lgSuplData);
            $ledg_sup_id = DB()->insertID();

            $this->purchase_transaction_entries($returnId, $ledg_sup_id, 'ledger_suppliers', 'Cr.');
            $this->transactionLog->insert_log_data('ledger_suppliers', $ledg_sup_id, '', $cashAmount, '', '', '', $returnId);
        }

        // -------------------------------------------------
        // 6. Bank payment reverse (if any)
        // -------------------------------------------------
        if ($bankAmount > 0) {
            $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $bankId);
            $upBank   = $bankCash + $bankAmount;   // reverse of original -

            DB()->table('bank')
                ->where('bank_id', $bankId)
                ->update([
                    'balance'   => $upBank,
                    'updatedBy' => $userId,
                ]);

            $this->transactionLog->insert_log_data('bank', $bankId, '', $bankAmount, '', '', '', $returnId);

            $lgBankData = [
                'sch_id'           => $shopId,
                'bank_id'          => $bankId,
                'purchase_id'      => $purchaseId,
                'purchase_return_id'=> $returnId,
                'trangaction_type' => 'Dr.',             // reverse of original Cr.
                'particulars'      => 'Purchase Return Bank Receive',
                'amount'           => $bankAmount,
                'rest_balance'     => $upBank,
                'createdBy'        => $userId,
                'createdDtm'       => date('Y-m-d h:i:s'),
            ];
            DB()->table('ledger_bank')->insert($lgBankData);
            $ledgBank_id = DB()->insertID();

            $this->purchase_transaction_entries($returnId, $ledgBank_id, 'ledger_bank', 'Dr.');
            $this->transactionLog->insert_log_data('ledger_bank', $ledgBank_id, '', $bankAmount, '', '', '', $returnId);

            // Supplier balance reverse for bank
            $supplierbbCash = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
            $suppBaCash     = $supplierbbCash - $bankAmount;  // reverse of original +

            DB()->table('suppliers')
                ->where('supplier_id', $supplierId)
                ->update([
                    'balance'   => $suppBaCash,
                    'updatedBy' => $userId,
                ]);

            $this->transactionLog->insert_log_data('suppliers', $supplierId, '', $bankAmount, '', '', '', $returnId);

            $lgSuplData = [
                'sch_id'           => $shopId,
                'supplier_id'      => $supplierId,
                'purchase_id'      => $purchaseId,
                'purchase_return_id'=> $returnId,
                'particulars'      => 'Purchase Return Bank Receive',
                'trangaction_type' => 'Cr.',             // reverse of original Dr.
                'amount'           => $bankAmount,
                'rest_balance'     => $suppBaCash,
                'createdBy'        => $userId,
                'createdDtm'       => date('Y-m-d h:i:s'),
            ];
            DB()->table('ledger_suppliers')->insert($lgSuplData);
            $ledg_sup_id = DB()->insertID();

            $this->purchase_transaction_entries($returnId, $ledg_sup_id, 'ledger_suppliers', 'Cr.');
            $this->transactionLog->insert_log_data('ledger_suppliers', $ledg_sup_id, '', $bankAmount, '', '', '', $returnId);
        }

        // -------------------------------------------------
        // 7. Stock / product_stock_relation reverse + purchase_return_item
        // -------------------------------------------------
        $store = DB()->table('stores')
            ->where('sch_id', $shopId)
            ->where('is_default', 1)
            ->get()
            ->getRow();
        $storeId = $store->store_id;

        for ($i = 0; $i < $number; $i++) {
            $prodId        = $productId[$i];
            $purchasePrice = $this->request->getPost('price[]')[$i];
            $sellingPrice  = $this->request->getPost('salePrice[]')[$i];
            $quantity      = $this->request->getPost('qty[]')[$i];

            // Find existing stock row
            $stock = DB()->table('product_stock_relation')
                ->where('sch_id', $shopId)
                ->where('store_id', $storeId)
                ->where('product_id', $prodId)
                ->where('purchase_price', $purchasePrice)
                ->where('selling_price', $sellingPrice)
                ->where('is_default', '0')
                ->get()
                ->getRow();

            if ($stock) {
                $newQty = $stock->quantity - $quantity;
                if ($newQty < 0) $newQty = 0; // safety

                DB()->table('product_stock_relation')
                    ->where('product_stock_relation_id', $stock->product_stock_relation_id)
                    ->update([
                        'quantity'    => $newQty,
                        'supplier_id' => $supplierId,
                    ]);
                $product_stock_relation_id = $stock->product_stock_relation_id;
            } else {
                // Should not happen on return, but keep safe
                $product_stock_relation_id = 0;
            }

            $total_price = $quantity * $purchasePrice;

            // Insert into purchase_return_item
            $returnItemData = [
                'purchase_return_id'         => $returnId,
                'purchase_id'                => $purchaseId,
                'prod_id'                    => $prodId,
                'product_stock_relation_id'  => $product_stock_relation_id,
                'purchase_price'             => $purchasePrice,
                'quantity'                   => $quantity,
                'total_price'                => $total_price,
                'createdBy'                  => $userId,
                'createdDtm'                 => date('Y-m-d h:i:s'),
            ];
            DB()->table('purchase_return_item')->insert($returnItemData);
            $return_item_id = DB()->insertID();

            $this->transactionLog->insert_log_data('purchase_return_item', $return_item_id, '', $total_price, '', '', '', $returnId);
        }

        // -------------------------------------------------
        // 8. Update return master with payment details
        // -------------------------------------------------
        DB()->table('purchase_return')
            ->where('purchase_return_id', $returnId)
            ->update([
                'amount'     => $totalPrice,
                'nagad_paid' => $cashAmount,
                'bank_paid'  => $bankAmount,
                'bank_id'    => $bankId,
                'due'        => $dueAmount,
            ]);

        $this->transactionLog->insert_log_data('purchase_return', $returnId, '', $totalPrice, '', '', '', $returnId);

        DB()->transComplete();

        if (!empty($sms)) {
            $message = 'Thank you. Your purchase return amount is-' . $totalPrice;
            $phone   = get_data_by_id('phone', 'suppliers', 'supplier_id', $supplierId);
            send_sms($phone, $message);
        }

        $this->cart->destroy();

        $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Purchase Return Success<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        return redirect()->to(site_url('Admin/Purchase'));
    }



}