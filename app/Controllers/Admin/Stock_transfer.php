<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Stock_transfer extends BaseController
{

    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'StockTransfer';

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
            $shopId = $this->session->shopId;

            $st_date = $this->request->getGet('st_date');
            $en_date = $this->request->getGet('en_date');

            $data['transfer'] = $this->stockTransferQuery($st_date, $en_date, $shopId);

            $data['st_date'] = isset($st_date)?$st_date:'';
            $data['en_date'] = isset($en_date)?$en_date:'';
            $data['id'] = $shopId;
            $data['shopId'] = $shopId;

            $storesTab = DB()->table('stores');
            $data['stores'] = $storesTab->where('sch_id', $shopId)->get()->getResult();

            $brandTab = DB()->table('brand');
            $data['brand'] = $brandTab->where('sch_id', $shopId)->get()->getResult();

            $categoryTab = DB()->table('product_category');
            $data['category'] = $categoryTab->where('sch_id', $shopId)->where('parent_pro_cat !=', '0')->get()->getResult();

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
                echo view('Admin/Stock_transfer/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method search stock transfer date to date
     * @return void
     */
    public function search_dateTodate()
    {
        $shopId = $this->session->shopId;

        $st_date = $this->request->getPost('st_date') ?? $this->request->getGet('st_date');
        $en_date = $this->request->getPost('en_date') ?? $this->request->getGet('en_date');

        $query = $this->stockTransferQuery($st_date, $en_date, $shopId);

        print $this->stockTransferBox($query, $st_date, $en_date, $shopId);
    }

    /**
     * @description This method search stock transfer print date to date
     * @return void
     */
    public function search_dateTodatePrint()
    {
        $shopId = $this->session->shopId;

        $st_date = $this->request->getPost('st_date') ?? $this->request->getGet('st_date');
        $en_date = $this->request->getPost('en_date') ?? $this->request->getGet('en_date');

        $query = $this->stockTransferQuery($st_date, $en_date, $shopId);

        $view = '<div class="col-xs-12" style="text-transform: capitalize;" >'
            . view('Admin/Stock_transfer/print_table', ['transfer' => $query, 'st_date' => $st_date, 'en_date' => $en_date])
            . '</div>';

        print $view;
    }

    /**
     * @description This method builds the stock transfer query
     * @param string $st_date
     * @param string $en_date
     * @param int|null $shopId
     * @return array
     */
    private function stockTransferQuery($st_date = '', $en_date = '', $shopId = null)
    {
        $shopId = $shopId ?? $this->session->shopId;
        $table = DB()->table('stock_transfer');
        $table->where('sch_id', $shopId);
        if (!empty($st_date) && !empty($en_date)) {
            $table->where('createdDtm >=', $st_date . ' 00:00:00');
            $table->where('createdDtm <=', $en_date . ' 23:59:59');
        }
        $table->orderBy('stock_transfer_id', 'ASC');

        return $table->get()->getResult();
    }

    /**
     * @description This method builds the stock transfer table box and embedded print container
     * @param array $transfer
     * @param string $st_date
     * @param string $en_date
     * @param int|null $shopId
     * @return string
     */
    private function stockTransferBox($transfer, $st_date = '', $en_date = '', $shopId = null)
    {
        $shopId = $shopId ?? $this->session->shopId;
        $read = $this->permission->have_access($this->session->role, $this->module_name, 'read');

        $totalQty = 0;
        foreach ($transfer as $item) {
            $totalQty += get_stock_transfer_qty_by_id($item->stock_transfer_id) ?? 0;
        }

        $view = '<div class="box">
                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped text-capitalize">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Date</th>
                                <th>From Store</th>
                                <th>To Store</th>
                                <th>Quantity</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>';

        $i = 1;
        foreach ($transfer as $item) {
            $actionBtn = '';
            if (isset($read) && $read == 1) {
                $actionBtn = '<a href="javascript:void(0)" onclick="showData(\'' . site_url('/Admin/Stock_transfer_ajax/view/' . $item->stock_transfer_id) . '\',\'/Admin/Stock_transfer/view/' . $item->stock_transfer_id . '\')" class="btn btn-primary btn-xs">View</a>';
            }

            $qty = get_stock_transfer_qty_by_id($item->stock_transfer_id) ?? 0;

            $view .= '<tr>
                        <td>' . $i++ . '</td>
                        <td>' . globalTimeStamp($item->createdDtm) . '</td>
                        <td>' . get_data_by_id('name', 'stores', 'store_id', $item->from_stock_id) . '</td>
                        <td>' . get_data_by_id('name', 'stores', 'store_id', $item->to_stock_id) . '</td>
                        <td>' . $qty . '</td>
                        <td>' . $actionBtn . '</td>
                    </tr>';
        }

        $view .= '</tbody>
                    <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Date</th>
                        <th>From Store</th>
                        <th>To Store</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Total</th>
                        <th>= ' . $totalQty . '</th>
                        <th></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>';

        // printable version of the same (filtered) rows
        $view .= '<div id="ajaxStockTransferPrint" style="display: none;">'
            . view('Admin/Stock_transfer/print_table', ['transfer' => $transfer, 'st_date' => $st_date, 'en_date' => $en_date])
            . '</div>';

        return $view;
    }

    public function search()
    {

        $shopId = $this->session->shopId;
        $type = $this->request->getPost('type');
        $storeId = $this->request->getPost('store_id');
        $db = DB();
        $result = array();
        $table = $db->table('products');
        $table->join('product_stock_relation', 'product_stock_relation.product_id = products.prod_id')
            ->where('products.sch_id', $shopId)
            ->where('product_stock_relation.store_id', $storeId)
            ->where('product_stock_relation.quantity !=', 0);

        if ($type == 'product') {
            $table->where('products.prod_id', $this->request->getPost('prod_id'));
            $result = $table->get()->getResult();
        } elseif ($type == 'brand') {
            $table->where('products.brand_id', $this->request->getPost('brand_id'));
            $result = $table->get()->getResult();
        } elseif ($type == 'category') {
            $table->where('products.prod_cat_id', $this->request->getPost('prod_cat_id'));
            $result = $table->get()->getResult();
        }else{
            $result = $table->get()->getResult();
        }

        $data['result'] = $result;
        $data['storeId'] = $storeId;

        if (!empty($data['result'])) {
            $storesTab = DB()->table('stores');
            $data['stores'] = $storesTab->where('sch_id', $shopId)->where('store_id !=', $storeId)->get()->getResult();

            $data['menu'] = view('Admin/menu_stock', $data);
            echo view('Admin/header');
            echo view('Admin/sidebar');
            echo view('Admin/Stock_transfer/search', $data);
            echo view('Admin/footer');
        }else{
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert"> Products not found! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Stock_transfer'));
        }
    }

    public function transferAction(){
        $userId = $this->session->userId;
        $shopId = $this->session->shopId;

        $toStockId = $this->request->getPost('store_id');
        $defaultStoreId = $this->request->getPost('from_stock_id');

        $proId = $this->request->getPost('returnchecked[]');
        $quantity = $this->request->getPost('quantity[]');

        // If customer name of id not selected (End)
        // Check if all product IDs are empty
        if (empty($proId)) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert"> Product not selected! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Stock_transfer'));
        }

        $filteredProId = array_filter($proId);
        if (empty($filteredProId)) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert"> Product not selected! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Stock_transfer'));
        }

        $db = DB();
        $db->transStart();
            //stock transfer data insert
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

                    $qty = $quantity[$pid];
                    $db->table('stock_transfer_item')->insert([
                        'sch_id' => $shopId,
                        'stock_transfer_id' => $stockTransferId,
                        'prod_id'  => $pid,
                        'quantity'    => $qty ?? 0,
                        'createdBy'    => $userId,
                    ]);

                    //default Store quantity update
                    $productQty = $db->table('product_stock_relation')->where('store_id',$defaultStoreId)->where('product_id',$pid)->get()->getRow();
                    $upQty = 0;
                    if (!empty($productQty)){
                        $upQty = $productQty->quantity - $qty;
                    }
                    $storeQtyUpdateData = array(
                        'quantity' => $upQty
                    );
                    $db->table('product_stock_relation')->where('store_id',$defaultStoreId)->where('product_id',$pid)->update($storeQtyUpdateData);


                    //new store quantity update
                    $productNewQty = $db->table('product_stock_relation')->where('store_id',$toStockId)->where('product_id',$pid)->get()->getRow();
                    if (!empty($productNewQty)){
                        $newQty = $productNewQty->quantity + $qty;
                        $newQtyData = array(
                            'quantity' => $newQty
                        );
                        $db->table('product_stock_relation')->where('store_id',$toStockId)->where('product_id',$pid)->update($newQtyData);
                    }else{
                        $newQtyData = array(
                            'sch_id' => $shopId,
                            'store_id' => $toStockId,
                            'product_id' => $pid,
                            'quantity' => $qty
                        );
                        $db->table('product_stock_relation')->insert($newQtyData);
                    }
                }
            }

        $db->transComplete();

        if ($db->transStatus() === false) {
            // rollback হয়েছে
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert"> Something went wrong! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        }else {
            $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert"> Transfer data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        }
        return redirect()->to(site_url('Admin/Stock_transfer'));

    }

    public function view($id){
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;

            $table = DB()->table('stock_transfer');
            $data['transfer'] = $table->where('stock_transfer_id', $id)->get()->getRow();

            $tableItem = DB()->table('stock_transfer_item');
            $data['transferItem'] = $tableItem->where('stock_transfer_id', $id)->get()->getResult();

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
                echo view('Admin/Stock_transfer/view', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }
}
