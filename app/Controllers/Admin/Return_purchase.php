<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Return_purchase extends BaseController
{

    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Return_purchase';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides return purchase view
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

            $data['return_purchase_data'] = $this->returnPurchaseQuery($supplier_id, $st_date, $en_date, $shopId);

            $data['supplierId'] = isset($supplier_id) ? $supplier_id : '';
            $data['st_date'] = isset($st_date) ? $st_date : '';
            $data['en_date'] = isset($en_date) ? $en_date : '';
            $data['id'] = $shopId;
            $data['shopId'] = $shopId;

            $data['menu'] = view('Admin/menu_purchase', $data);
            // All Permissions
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Return_purchase/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method search return purchase date to date
     * @return void
     */
    public function search_dateTodate()
    {
        $shopId = $this->session->shopId;

        $supplier_id = $this->request->getPost('supplier_id') ?? $this->request->getGet('supplier_id');
        $st_date = $this->request->getPost('st_date') ?? $this->request->getGet('st_date');
        $en_date = $this->request->getPost('en_date') ?? $this->request->getGet('en_date');

        $query = $this->returnPurchaseQuery($supplier_id, $st_date, $en_date, $shopId);

        print $this->returnPurchaseBox($query, $supplier_id, $st_date, $en_date, $shopId);
    }

    /**
     * @description This method search return purchase print date to date
     * @return void
     */
    public function search_dateTodatePrint()
    {
        $shopId = $this->session->shopId;

        $supplier_id = $this->request->getPost('supplier_id') ?? $this->request->getGet('supplier_id');
        $st_date = $this->request->getPost('st_date') ?? $this->request->getGet('st_date');
        $en_date = $this->request->getPost('en_date') ?? $this->request->getGet('en_date');

        $query = $this->returnPurchaseQuery($supplier_id, $st_date, $en_date, $shopId);

        $view = '<div class="col-xs-12" style="text-transform: capitalize;" >'
            . view('Admin/Return_purchase/print_table', [
                'return_purchase_data' => $query,
                'supplierId' => $supplier_id,
                'st_date' => $st_date,
                'en_date' => $en_date,
                'id' => $shopId,
                'shopId' => $shopId
            ])
            . '</div>';

        print $view;
    }

    /**
     * @description This method builds the return purchase query
     * @param string|int $supplier_id
     * @param string $st_date
     * @param string $en_date
     * @param int|null $shopId
     * @return array
     */
    private function returnPurchaseQuery($supplier_id = '', $st_date = '', $en_date = '', $shopId = null)
    {
        $shopId = $shopId ?? $this->session->shopId;
        $table = DB()->table('return_purchase');
        $table->where('return_purchase.sch_id', $shopId);
        $table->where('return_purchase.deleted', null);

        if (!empty($supplier_id)) {
            $table->where('return_purchase.supplier_id', $supplier_id);
        }
        if (!empty($st_date)) {
            $table->where('return_purchase.createdDtm >=', $st_date . ' 00:00:00');
        }
        if (!empty($en_date)) {
            $table->where('return_purchase.createdDtm <=', $en_date . ' 23:59:59');
        }
        $table->orderBy('return_purchase.rtn_purchase_id', 'ASC');

        return $table->get()->getResult();
    }

    /**
     * @description This method builds the return purchase table box and embedded print container
     * @param array $return_purchase_data
     * @param string|int $supplier_id
     * @param string $st_date
     * @param string $en_date
     * @param int|null $shopId
     * @return string
     */
    private function returnPurchaseBox($return_purchase_data, $supplier_id = '', $st_date = '', $en_date = '', $shopId = null)
    {
        $shopId = $shopId ?? $this->session->shopId;
        $read = $this->permission->have_access($this->session->role, $this->module_name, 'read');

        $totalAmount = 0;

        $view = '<table id="example1" class="table table-bordered table-striped text-capitalize" style="margin-top: 20px;">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th>Total Amount</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>';

        $i = 1;
        foreach ($return_purchase_data as $return) {
            $totalAmount += (float)$return->amount;

            $actionBtns = '';
            if (isset($read) && $read == 1) {
                $actionBtns .= '<a href="javascript:void(0)" onclick="showData(\'' . site_url('/Admin/Return_purchase_ajax/view/' . $return->rtn_purchase_id) . '\',\'/Admin/Return_purchase/view/' . $return->rtn_purchase_id . '\')" class="btn btn-warning btn-xs">View</a>';
            }

            $view .= '<tr>
                        <td>' . $i++ . '</td>
                        <td>' . invoiceDateFormat($return->createdDtm) . '</td>
                        <td>' . get_data_by_id('name', 'suppliers', 'supplier_id', $return->supplier_id) . '</td>
                        <td>' . showWithCurrencySymbol($return->amount) . '</td>
                        <td>' . $actionBtns . '</td>
                    </tr>';
        }

        $view .= '</tbody>
                    <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th>Total Amount</th>
                        <th>Action</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>Total</th>
                        <th>= ' . showWithCurrencySymbol($totalAmount) . '</th>
                        <th></th>
                    </tr>
                    </tfoot>
                </table>';

        // printable version of the same (filtered) rows
        $view .= '<div id="ajaxReturnPurchasePrint" style="display: none;">'
            . view('Admin/Return_purchase/print_table', [
                'return_purchase_data' => $return_purchase_data,
                'supplierId' => $supplier_id,
                'st_date' => $st_date,
                'en_date' => $en_date,
                'id' => $shopId,
                'shopId' => $shopId
            ])
            . '</div>';

        return $view;
    }

    /**
     * @description This method purchase search
     * @return void
     */
    public function purchase_search()
    {

        $shopId = $this->session->shopId;
        $purchaseId = $this->request->getPost('purchaseId');

        $purchaseTable = DB()->table('purchase');
        $data['purchase_data'] = $purchaseTable->where('sch_id', $shopId)->where('purchase_id', $purchaseId)->get()->getResult();

        $data['return_status'] = get_return_status_by_purchase_id($purchaseId);

        $data['menu'] = view('Admin/menu_purchase', $data);
        echo view('Admin/header');
        echo view('Admin/sidebar');
        echo view('Admin/Return_purchase/search', $data);
        echo view('Admin/footer');
    }

    /**
     * @description This method provides return view
     * @param int $id
     * @return RedirectResponse|void
     */
    public function return($id)
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;

            $purchase_itemTab = DB()->table('purchase_item');
            $data['purchase_item'] = $purchase_itemTab->where('purchase_id', $id)->get()->getResult();

            $purchaseTab = DB()->table('purchase');
            $data['purchase'] = $purchaseTab->where('purchase_id', $id)->where('sch_id', $shopId)->get()->getRow();

            $data['action'] = site_url('Admin/Return_purchase/create_action');
            $data['purchaseId'] = $id;

            $data['menu'] = view('Admin/menu_purchase', $data);
            // All Permissions
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if ($data['create'] == 1) {
                echo view('Admin/Return_purchase/return', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store return purchase
     * @return RedirectResponse
     */
    public function create_action()
    {
        $userId   = $this->session->userId;
        $shopId   = $this->session->shopId;

        $supplierId   = $this->request->getPost('supplier_id');
        $purchaseId    = $this->request->getPost('purchase_id');

        $productIds              = $this->request->getPost('returnchecked[]');
        $productStockRelationIds = $this->request->getPost('product_stock_relation_id[]');
        $quantities              = $this->request->getPost('quantity[]');
        $purchasePrices          = $this->request->getPost('purchase_price[]');

        $amount     = $this->request->getPost('totalPrice');
        $cashAmount = $this->request->getPost('cash');
        $bankAmount = $this->request->getPost('bank');
        $bankId     = $this->request->getPost('bank_id');
        $dueAmount  = $this->request->getPost('due');

        // Supplier validation
        if (empty($supplierId)) {
            return redirect()->to(site_url('Admin/Return_purchase/return/' . $purchaseId));
        }

        if (empty($amount)) {
            $this->session->setFlashdata('message', '
            <div class="alert alert-danger alert-dismissible" role="alert">
                Please select any product
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>');
            return redirect()->to(site_url('Admin/Return_purchase/return/' . $purchaseId));
        }

        if ($dueAmount < 0) {
            $this->session->setFlashdata('message', '
            <div class="alert alert-danger alert-dismissible" role="alert">
                Please enter a valid due amount
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>');
            return redirect()->to(site_url('Admin/Return_purchase/return/' . $purchaseId));
        }

        DB()->transStart();

        // 1. Insert into return_purchase
        $returnData = [
            'sch_id'       => $shopId,
            'purchase_id'  => $purchaseId,
            'supplier_id'  => $supplierId,
            'amount'       => $amount,
            'nagad_paid'   => $cashAmount,
            'bank_paid'    => $bankAmount,
            'bank_id'      => $bankId,
            'createdBy'    => $userId,
            'createdDtm'   => date('Y-m-d H:i:s'),
        ];


        DB()->table('return_purchase')->insert($returnData);
        $returnId = DB()->insertID();

        // 2. Update shop purchase balance + ledger
        $purchaseBalance    = get_data_by_id('purchase_balance', 'shops', 'sch_id', $shopId);
        $newPurchaseBalance = $purchaseBalance - $amount;

        DB()->table('shops')
            ->where('sch_id', $shopId)
            ->update(['purchase_balance' => $newPurchaseBalance]);

        DB()->table('ledger_purchase')->insert([
            'sch_id'           => $shopId,
            'rtn_purchase_id'  => $returnId,
            'trangaction_type' => 'Cr.',
            'particulars'      => 'return purchase',
            'amount'           => $amount,
            'rest_balance'     => $newPurchaseBalance,
            'createdBy'        => $userId,
            'createdDtm'       => date('Y-m-d H:i:s'),
        ]);


        // stock balance update and ledger create (start)
        $stockBal = get_data_by_id('stockAmount', 'shops', 'sch_id', $shopId);
        $restBalStock = $stockBal - $amount;


        $stockUpdata = array('stockAmount' => $restBalStock);
        $tabshopsSt = DB()->table('shops');
        $tabshopsSt->where('sch_id', $shopId)->update($stockUpdata);


        $stockLedgData = array(
            'sch_id' => $shopId,
            'purchase_id' => $purchaseId,
            'trangaction_type' => 'Cr.',
            'particulars' => 'return purchase amount',
            'amount' => $amount,
            'rest_balance' => $restBalStock,
            'createdBy' => $userId,
            'createdDtm' => date('Y-m-d h:i:s')
        );
        $tabledger_stock = DB()->table('ledger_stock');
        $tabledger_stock->insert($stockLedgData);
        $ledg_stock_id = DB()->insertID();
        // stock balance update and ledger create (end)

        // 3. Process each returned item
        $itemCount = count($productIds);

        for ($i = 0; $i < $itemCount; $i++) {
            $qty   = $quantities[$i];
            $price = $purchasePrices[$i];
            $total = $qty * $price;

            // Insert return item
            DB()->table('return_purchase_item')->insert([
                'sch_id'            => $shopId,
                'rtn_purchase_id'   => $returnId,
                'prod_id'           => $productIds[$i],
                'price'             => $price,
                'quantity'          => $qty,
                'total_price'       => $total,
                'createdBy'         => $userId,
                'createdDtm'        => date('Y-m-d H:i:s'),
            ]);

            // Update product stock quantity (subtract returned qty)
            $stock = DB()->table('product_stock_relation')
                ->where('product_stock_relation_id', $productStockRelationIds[$i])
                ->where('product_id', $productIds[$i])
                ->get()
                ->getRow();

            if ($stock) {
                $newQty = $stock->quantity - $qty;

                DB()->table('product_stock_relation')
                    ->where('product_stock_relation_id', $productStockRelationIds[$i])
                    ->where('product_id', $productIds[$i])
                    ->update(['quantity' => $newQty]);
            }
        }

        // 4. Supplier balance + ledger (existing supplier only)
        if (!empty($supplierId)) {
            $oldSupBalance = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
            $newSupBalance = $oldSupBalance + $amount;

            DB()->table('suppliers')
                ->where('supplier_id', $supplierId)
                ->update([
                    'balance'   => $newSupBalance,
                    'createdBy' => $userId,
                ]);

            DB()->table('ledger_suppliers')->insert([
                'sch_id'           => $shopId,
                'supplier_id'      => $supplierId,
                'rtn_purchase_id'  => $returnId,
                'particulars'      => 'Return Purchase Product',
                'trangaction_type' => 'Dr.',
                'amount'           => $amount,
                'rest_balance'     => $newSupBalance,
                'createdBy'        => $userId,
                'createdDtm'       => date('Y-m-d H:i:s'),
            ]);
        }


        DB()->transComplete();

        $this->session->setFlashdata('message', '
        <div class="alert alert-success alert-dismissible" role="alert">
            Return Product Success
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>');

        return redirect()->to(site_url('Admin/Return_purchase/'));
    }

    public function view($id){
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;

            $data['shopsName'] = get_data_by_id('name', 'shops', 'sch_id', $shopId);

            $returnPurchaseTable = DB()->table('return_purchase');
            $data['returnPurchaseData'] = $returnPurchaseTable->where('rtn_purchase_id', $id)->get()->getRow();

            $returnItem = DB()->table('return_purchase_item');
            $data['purchaseItame'] = $returnItem->where('rtn_purchase_id', $id)->get()->getResult();

            $data['menu'] = view('Admin/menu_purchase', $data);
            // All Permissions
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Return_purchase/view', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

}
