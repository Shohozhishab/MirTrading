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
            $shopId = $this->session->shopId;
            $table = DB()->table('exchange_product');
            $data['exchangeProduct'] = $table->where('sch_id', $shopId)->get()->getResult();

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

    public function invoice_search()
    {

        $shopId = $this->session->shopId;
        $invoiceId = $this->request->getPost('invoiceId');

        $invoice_itemTab = DB()->table('invoice_item');
        $data['invoice_item'] = $invoice_itemTab->where('invoice_id', $invoiceId)->where('sch_id', $shopId)->get()->getResult();

        $invoiceTab = DB()->table('invoice');
        $data['invoice'] = $invoiceTab->where('invoice_id', $invoiceId)->where('sch_id', $shopId)->get()->getRow();

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

        $proId = $this->request->getPost('prod_id[]');
        $quantity = $this->request->getPost('quantity[]');

        // If customer name of id not selected (start)
        if (empty($customerName) && empty($customerId)) {
            return redirect()->to(site_url('Admin/Exchange_product'));
        }
        // If customer name of id not selected (End)
        // Check if all product IDs are empty
        $filteredProId = array_filter($proId);
        if (empty($filteredProId)) {
            return redirect()->to(site_url('Admin/Exchange_product'));
        }




        $db = DB();
        $db->transStart();
            $exchangeData = array(
                'sch_id' => $shopId,
                'customer_id' => $customerId,
                'customer_name' => $customerName,
            );
            $db->table('exchange_product')->insert($exchangeData);
            // Get inserted ID
            $exchangeId = $db->insertID();

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
                        'prod_id'  => $pid,
                        'quantity'    => $quantity[$key] ?? 0,
                        'createdBy'    => $userId,
                    ]);

                    //default Store quantity update
                    $productQty = $db->table('product_stock_relation')->where('store_id',$defaultStoreId)->where('product_id',$pid)->get()->getRow();
                    $upQty = 0;
                    if (!empty($productQty)){
                        $upQty = $productQty->quantity - $quantity[$key];
                    }
                    $storeQtyUpdateData = array(
                        'quantity' => $upQty
                    );
                    $db->table('product_stock_relation')->where('store_id',$defaultStoreId)->where('product_id',$pid)->update($storeQtyUpdateData);


                    //new store quantity update
                    $productNewQty = $db->table('product_stock_relation')->where('store_id',$toStockId)->where('product_id',$pid)->get()->getRow();
                    if (!empty($productNewQty)){
                        $newQty = $productNewQty->quantity + $quantity[$key];
                        $newQtyData = array(
                            'quantity' => $newQty
                        );
                        $db->table('product_stock_relation')->where('store_id',$toStockId)->where('product_id',$pid)->update($newQtyData);
                    }else{
                        $newQtyData = array(
                            'store_id' => $toStockId,
                            'product_id' => $pid,
                            'quantity' => $quantity[$key]
                        );
                        $db->table('product_stock_relation')->insert($newQtyData);
                    }
                }
            }

            $db->table('exchange_stock_relation')->insert([
                'exchange_pro_id' => $exchangeId,
                'stock_transfer_id' => $stockTransferId,
            ]);

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

            $table = DB()->table('stock_transfer');
            $data['transfer'] = $table->where('stock_transfer_id', $stockRelation->stock_transfer_id)->get()->getRow();

            $tableItem = DB()->table('stock_transfer_item');
            $data['transferItem'] = $tableItem->where('stock_transfer_id', $stockRelation->stock_transfer_id)->get()->getResult();

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
}