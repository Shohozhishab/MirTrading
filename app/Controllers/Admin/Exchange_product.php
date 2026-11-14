<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Exchange_product extends BaseController
{

    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'ExchangeProduct';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides view
     * @return RedirectResponse|void
     */
    public function index()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $customer_id = $this->request->getGet('customer');
            $status = $this->request->getGet('status');
            $st_date = $this->request->getGet('st_date');
            $en_date = $this->request->getGet('en_date');

            $shopId = $this->session->shopId;
            $data['exchangeProduct'] = $this->exchangeProductQuery($customer_id, $status, $st_date, $en_date, $shopId);

            $data['customerId'] = $customer_id ?? '';
            $data['status'] = $status ?? '';
            $data['st_date'] = isset($st_date) ? $st_date : '';
            $data['en_date'] = isset($en_date) ? $en_date : '';
            $data['id'] = $shopId;
            $data['shopId'] = $shopId;

            $data['menu'] = view('Admin/menu_stock');
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Exchange_product/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method search exchange product date to date
     * @return void
     */
    public function search_dateTodate()
    {
        $shopId = $this->session->shopId;

        $customer_id = $this->request->getPost('customer') ?? $this->request->getGet('customer');
        $status = $this->request->getPost('status') ?? $this->request->getGet('status');
        $st_date = $this->request->getPost('st_date') ?? $this->request->getGet('st_date');
        $en_date = $this->request->getPost('en_date') ?? $this->request->getGet('en_date');

        $query = $this->exchangeProductQuery($customer_id, $status, $st_date, $en_date, $shopId);

        print $this->exchangeProductBox($query, $customer_id, $status, $st_date, $en_date, $shopId);
    }

    /**
     * @description This method search exchange product print date to date
     * @return void
     */
    public function search_dateTodatePrint()
    {
        $shopId = $this->session->shopId;

        $customer_id = $this->request->getPost('customer') ?? $this->request->getGet('customer');
        $status = $this->request->getPost('status') ?? $this->request->getGet('status');
        $st_date = $this->request->getPost('st_date') ?? $this->request->getGet('st_date');
        $en_date = $this->request->getPost('en_date') ?? $this->request->getGet('en_date');

        $query = $this->exchangeProductQuery($customer_id, $status, $st_date, $en_date, $shopId);

        $view = '<div class="col-xs-12" style="text-transform: capitalize;" >'
            . view('Admin/Exchange_product/print_table', [
                'exchangeProduct' => $query,
                'customerId' => $customer_id,
                'status' => $status,
                'st_date' => $st_date,
                'en_date' => $en_date
            ])
            . '</div>';

        print $view;
    }

    /**
     * @description This method builds the exchange product query
     * @param string|int $customer_id
     * @param string $status
     * @param string $st_date
     * @param string $en_date
     * @param int|null $shopId
     * @return array
     */
    private function exchangeProductQuery($customer_id = '', $status = '', $st_date = '', $en_date = '', $shopId = null)
    {
        $shopId = $shopId ?? $this->session->shopId;
        $table = DB()->table('exchange_product');
        $table->select('exchange_product.*');
        $table->where('exchange_product.sch_id', $shopId);
        if (!empty($customer_id)) {
            $table->where('exchange_product.customer_id', $customer_id);
        }
        if (!empty($status)) {
            $table->join('exchange_status_info', 'exchange_status_info.exchange_pro_id = exchange_product.exchange_pro_id');
            $table->where('exchange_status_info.status', $status);
        }
        if (!empty($st_date) && !empty($en_date)) {
            $table->where('exchange_product.createdDtm >=', $st_date . ' 00:00:00');
            $table->where('exchange_product.createdDtm <=', $en_date . ' 23:59:59');
        }
        $table->orderBy('exchange_product.exchange_pro_id', 'ASC');

        return $table->get()->getResult();
    }

    /**
     * @description This method builds the exchange product table box and embedded print container
     * @param array $exchangeProduct
     * @param string|int $customer_id
     * @param string $status
     * @param string $st_date
     * @param string $en_date
     * @param int|null $shopId
     * @return string
     */
    private function exchangeProductBox($exchangeProduct, $customer_id = '', $status = '', $st_date = '', $en_date = '', $shopId = null)
    {
        $shopId = $shopId ?? $this->session->shopId;
        $read = $this->permission->have_access($this->session->role, $this->module_name, 'read');
        $update = $this->permission->have_access($this->session->role, $this->module_name, 'update');

        $totalQty = 0;
        foreach ($exchangeProduct as $item) {
            $qty = ($item->type == 'Conditional')
                ? (get_exchange_conditional_product_qty_by_id($item->exchange_pro_id) ?? 0)
                : (get_exchange_unconditional_product_qty_by_id($item->exchange_pro_id) ?? 0);
            $totalQty += $qty;
        }

        $view = '<div class="box">
                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped text-capitalize">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Customer</th>
                                <th>Quantity</th>
                                <th>Comment</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>';

        $i = 1;
        foreach ($exchangeProduct as $item) {
            $customerName = !empty($item->customer_id)
                ? get_data_by_id('customer_name', 'customers', 'customer_id', $item->customer_id)
                : $item->customer_name;

            $qty = ($item->type == 'Conditional')
                ? get_exchange_conditional_product_qty_by_id($item->exchange_pro_id)
                : get_exchange_unconditional_product_qty_by_id($item->exchange_pro_id);

            $actionBtns = '';
            if (isset($read) && $read == 1) {
                $actionBtns .= '<a href="javascript:void(0)" onclick="showData(\'' . site_url('/Admin/Exchange_product_ajax/view/' . $item->exchange_pro_id) . '\',\'/Admin/Exchange_product/view/' . $item->exchange_pro_id . '\')" class="btn btn-primary btn-xs">View</a> ';
            }
            if (isset($update) && $update == 1) {
                $actionBtns .= '<a href="javascript:void(0)" onclick="showData(\'' . site_url('/Admin/Exchange_product_ajax/update/' . $item->exchange_pro_id) . '\',\'/Admin/Exchange_product/update/' . $item->exchange_pro_id . '\')" class="btn btn-warning btn-xs">Update</a>';
            }

            $view .= '<tr>
                        <td>' . $i++ . '</td>
                        <td>' . globalTimeStamp($item->createdDtm) . '</td>
                        <td>' . $item->type . '</td>
                        <td>' . get_exchange_status_by_id($item->exchange_pro_id) . '</td>
                        <td>' . $customerName . '</td>
                        <td>' . $qty . '</td>
                        <td>' . $item->comment . '</td>
                        <td>' . $actionBtns . '</td>
                    </tr>';
        }

        $view .= '</tbody>
                    <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Customer</th>
                        <th>Quantity</th>
                        <th>Comment</th>
                        <th>Action</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Total</th>
                        <th>= ' . $totalQty . '</th>
                        <th></th>
                        <th></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>';

        // printable version of the same (filtered) rows
        $view .= '<div id="ajaxExchangeProductPrint" style="display: none;">'
            . view('Admin/Exchange_product/print_table', [
                'exchangeProduct' => $exchangeProduct,
                'customerId' => $customer_id,
                'status' => $status,
                'st_date' => $st_date,
                'en_date' => $en_date
            ])
            . '</div>';

        return $view;
    }

    public function invoice_search()
    {

        $shopId = $this->session->shopId;
        $invoiceId = $this->request->getPost('invoiceId');

        $invoice_itemTab = DB()->table('invoice_item');
        $data['invoice_item'] = $invoice_itemTab->where('invoice_id', $invoiceId)->where('sch_id', $shopId)->get()->getResult();

        $invoiceTab = DB()->table('invoice');
        $data['invoice'] = $invoiceTab->where('invoice_id', $invoiceId)->where('sch_id', $shopId)->get()->getRow();


        $return_status = get_return_status_by_invoice_id($invoiceId);

        if ($return_status == 1){
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert"> Invoice Returned! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Exchange_product'));
        }

        if (empty($data['invoice'])){
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert"> Invoice not found! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Exchange_product'));
        }

        $storesTab = DB()->table('stores');
        $data['stores'] = $storesTab->where('sch_id', $shopId)->get()->getResult();

        $data['menu'] = view('Admin/menu_stock', $data);
        echo view('Admin/header');
        echo view('Admin/sidebar');
        echo view('Admin/Exchange_product/search', $data);
        echo view('Admin/footer');
    }

    public function exchangeAction(){
        $userId = $this->session->userId;
        $shopId = $this->session->shopId;

        $toStockId = $this->request->getPost('store_id');

        $customerId = $this->request->getPost('customer_id');
        $customerName = $this->request->getPost('customer_name');

        $proId = $this->request->getPost('prod_id');
        $quantity = $this->request->getPost('quantity');

        // If customer name of id not selected (start)
        if (empty($customerName) && empty($customerId)) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert"> Customer not selected! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Exchange_product'));
        }
        // If customer name of id not selected (End)
        // Check if all product IDs are empty
        if (empty($proId)) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert"> Product not selected! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Exchange_product'));
        }

        $filteredProId = array_filter($proId);
        if (empty($filteredProId)) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert"> Product not selected! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Exchange_product'));
        }

        $proIdLot = $this->request->getPost('proIdLot[]');
        $number = $this->request->getPost('number[]');
        $date = $this->request->getPost('date[]');
        $comment = $this->request->getPost('comment[]');

        $type = $this->request->getPost('type');
        $commentMain = $this->request->getPost('commentMain');

        $db = DB();
        $db->transStart();
            $exchangeData = array(
                'sch_id' => $shopId,
                'customer_id' => $customerId,
                'customer_name' => $customerName,
                'comment' => $commentMain,
                'type' => $type,
            );

            if ($type == 'Conditional'){
                $exchangeData['status'] = 'Processing';
            }else{
                $exchangeData['status'] = 'Complete';
            }

            $db->table('exchange_product')->insert($exchangeData);
            // Get inserted ID
            $exchangeId = $db->insertID();

            if ($type == 'Conditional'){

                $db->table('exchange_status_info')->insert([
                    'sch_id' => $shopId,
                    'exchange_pro_id' => $exchangeId,
                    'status'  => '1',
                    'date'  => date("Y-m-d"),
                ]);

                foreach ($proId as $key => $pid) {
                    if (!empty($pid)) {
                        $db->table('exchange_product_item')->insert([
                            'sch_id' => $shopId,
                            'exchange_pro_id' => $exchangeId,
                            'prod_id'  => $pid,
                            'quantity'    => $quantity[$pid] ?? 0,
                            'createdBy'    => $userId,
                        ]);
                        $exchangeProductItemId = $db->insertID();

                        //product lot number add
                        if (!empty($proIdLot)) {
                            foreach ($proIdLot as $keyLot => $proId) {
                                if ($proId == $pid) {
                                    $db->table('product_lot_info')->insert([
                                        'sch_id' => $shopId,
                                        'exchange_product_item_id' => $exchangeProductItemId,
                                        'number' => $number[$keyLot],
                                        'date' => $date[$keyLot],
                                        'comment' => $comment[$keyLot],
                                    ]);
                                }
                            }
                        }

                    }
                }
            }else {
                //stock transfer data insert
                $store = $db->table('stores')->where('sch_id', $shopId)->where('is_default', 1)->get()->getRow();
                $defaultStoreId = $store->store_id;

                $stockTransferData = array(
                    'sch_id' => $shopId,
                    'from_stock_id' => $defaultStoreId,
                    'to_stock_id' => $toStockId,
                );
                $db->table('stock_transfer')->insert($stockTransferData);
                // Get inserted ID
                $stockTransferId = $db->insertID();

                //exchange product items add
                foreach ($proId as $key => $pid) {
                    if (!empty($pid)) {
                        $db->table('stock_transfer_item')->insert([
                            'sch_id' => $shopId,
                            'stock_transfer_id' => $stockTransferId,
                            'prod_id' => $pid,
                            'quantity' => $quantity[$pid] ?? 0,
                            'createdBy' => $userId,
                        ]);
                        $stockTransferItemId = $db->insertID();

                        //product lot number add
                        if (!empty($proIdLot)) {
                            foreach ($proIdLot as $keyLot => $proId) {
                                if ($proId == $pid) {
                                    $db->table('product_lot_info')->insert([
                                        'sch_id' => $shopId,
                                        'stock_transfer_item_id' => $stockTransferItemId,
                                        'number' => $number[$keyLot],
                                        'date' => $date[$keyLot],
                                        'comment' => $comment[$keyLot],
                                    ]);
                                }
                            }
                        }

                        //default Store quantity update
                        $productQty = $db->table('product_stock_relation')->where('store_id', $defaultStoreId)->where('product_id', $pid)->get()->getRow();
                        $upQty = 0;
                        if (!empty($productQty)) {
                            $upQty = $productQty->quantity - $quantity[$pid];
                        }
                        $storeQtyUpdateData = array(
                            'quantity' => $upQty
                        );
                        $db->table('product_stock_relation')->where('store_id', $defaultStoreId)->where('product_id', $pid)->update($storeQtyUpdateData);


                        //new store quantity update
                        $productNewQty = $db->table('product_stock_relation')->where('store_id', $toStockId)->where('product_id', $pid)->get()->getRow();
                        if (!empty($productNewQty)) {
                            $newQty = $productNewQty->quantity + $quantity[$pid];
                            $newQtyData = array(
                                'quantity' => $newQty
                            );
                            $db->table('product_stock_relation')->where('store_id', $toStockId)->where('product_id', $pid)->update($newQtyData);
                        } else {
                            $newQtyData = array(
                                'sch_id' => $shopId,
                                'store_id' => $toStockId,
                                'product_id' => $pid,
                                'quantity' => $quantity[$pid]
                            );
                            $db->table('product_stock_relation')->insert($newQtyData);
                        }
                    }
                }

                $db->table('exchange_stock_relation')->insert([
                    'sch_id' => $shopId,
                    'exchange_pro_id' => $exchangeId,
                    'stock_transfer_id' => $stockTransferId,
                ]);
            }

        $db->transComplete();

        if ($db->transStatus() === false) {
            // rollback হয়েছে
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert"> Something went wrong! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        }else {
            $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert"> Exchange data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        }
        return redirect()->to(site_url('Admin/Exchange_product'));

    }

    public function view($id){
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;
            $table = DB()->table('exchange_product');
            $data['exchangeProduct'] = $table->where('exchange_pro_id', $id)->get()->getRow();

            $tableStockRelation = DB()->table('exchange_stock_relation');
            $stockRelation = $tableStockRelation->where('exchange_pro_id', $id)->get()->getRow();

            if ($data['exchangeProduct']->type == 'Unconditional' ) {
                $table = DB()->table('stock_transfer');
                $data['transfer'] = $table->where('stock_transfer_id', $stockRelation->stock_transfer_id)->get()->getRow();

                $tableItem = DB()->table('stock_transfer_item');
                $data['transferItem'] = $tableItem->where('stock_transfer_id', $stockRelation->stock_transfer_id)->get()->getResult();
            }else {
                $tableItem = DB()->table('exchange_product_item');
                $data['transferItem'] = $tableItem->where('exchange_pro_id', $data['exchangeProduct']->exchange_pro_id)->get()->getResult();
            }

            $data['menu'] = view('Admin/menu_stock');
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Exchange_product/view', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    public function update($id){
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;
            $table = DB()->table('exchange_product');
            $data['exchangeProduct'] = $table->where('exchange_pro_id', $id)->get()->getRow();

            $tableStockRelation = DB()->table('exchange_stock_relation');
            $stockRelation = $tableStockRelation->where('exchange_pro_id', $id)->get()->getRow();

            if ($data['exchangeProduct']->type == 'Unconditional' ) {
                $table = DB()->table('stock_transfer');
                $data['transfer'] = $table->where('stock_transfer_id', $stockRelation->stock_transfer_id)->get()->getRow();

                $tableItem = DB()->table('stock_transfer_item');
                $data['transferItem'] = $tableItem->where('stock_transfer_id', $stockRelation->stock_transfer_id)->get()->getResult();
            }else {
                $tableItem = DB()->table('exchange_product_item');
                $data['transferItem'] = $tableItem->where('exchange_pro_id', $data['exchangeProduct']->exchange_pro_id)->get()->getResult();
            }

            $statusTab = DB()->table('exchange_status_info');
            $data['status'] = $statusTab->where('exchange_pro_id', $id)->get()->getRow();

            $data['menu'] = view('Admin/menu_stock');
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Exchange_product/update', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    public function exchangeUpdateAction(){
        $shopId = $this->session->shopId;
        $exchange_pro_id = $this->request->getPost('exchange_pro_id');


        $commentMain = $this->request->getPost('commentMain');
        $status = $this->request->getPost('status');


        $exchange_product_item_id = $this->request->getPost('exchange_product_item_id[]');
        $stock_transfer_item_id = $this->request->getPost('stock_transfer_item_id[]');

        $number = $this->request->getPost('number[]');
        $date = $this->request->getPost('date[]');
        $comment = $this->request->getPost('comment[]');


        $product_item_id = $this->request->getPost('product_item_id[]');
        $transfer_item_id = $this->request->getPost('transfer_item_id[]');

        $db = DB();
        $db->transStart();

        //delete lot info
        if (!empty($product_item_id)){
            foreach ($product_item_id as $item) {
                $db->table('product_lot_info')->where('exchange_product_item_id', $item)->delete();
            }
        }
        if (!empty($transfer_item_id)){
            foreach ($transfer_item_id as $item) {
                $db->table('product_lot_info')->where('stock_transfer_item_id', $item)->delete();
            }
        }
        //lot data insert
        if (!empty($exchange_product_item_id)) {
            $data = [];
            foreach ($exchange_product_item_id as $key => $val) {
                $data[] = [
                    'sch_id' => $shopId,
                    'exchange_product_item_id' => $val,
                    'number'                   => $number[$key],
                    'date'                     => $date[$key],
                    'comment'                  => $comment[$key],
                ];
            }
            $db->table('product_lot_info')->insertBatch($data);
        }
        //lot data insert
        if (!empty($stock_transfer_item_id)) {
            $data = [];
            foreach ($stock_transfer_item_id as $key => $val) {
                $data[] = [
                    'sch_id' => $shopId,
                    'stock_transfer_item_id' => $val,
                    'number' => $number[$key],
                    'date' => $date[$key],
                    'comment' => $comment[$key],
                ];
            }
            $db->table('product_lot_info')->insertBatch($data);
        }


        $db->table('exchange_product')->where('exchange_pro_id', $exchange_pro_id)->update(['comment'=>$commentMain]);
        $db->table('exchange_status_info')->where('exchange_pro_id', $exchange_pro_id)->update(['status'=>$status]);


        $db->transComplete();

        if ($db->transStatus() === false) {
            // rollback হয়েছে
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert"> Something went wrong! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        }else {
            $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert"> Exchange data update successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        }
        return redirect()->to(site_url('Admin/Exchange_product/update/'.$exchange_pro_id));
    }

}