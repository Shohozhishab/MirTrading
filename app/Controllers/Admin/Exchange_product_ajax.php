<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Exchange_product_ajax extends BaseController
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
            $table = DB()->table('exchange_product');
            $table->where('exchange_product.sch_id', $shopId);
            if (!empty($customer_id)) {
                $table->where('customer_id', $customer_id);
            }
            if (!empty($status)){
                $table->join('exchange_status_info', 'exchange_status_info.exchange_pro_id = exchange_product.exchange_pro_id');
                $table->where('exchange_status_info.status',$status);
            }
            // Apply date filters only if they are present in the request
            if (!empty($st_date) && !empty($en_date)) {
                // Assuming your database column name is 'date'
                $table->where('createdDtm >=', $st_date . ' 00:00:00');
                $table->where('createdDtm <=', $en_date . ' 23:59:59');
            }
            $data['exchangeProduct'] = $table->get()->getResult();

            $data['customerId'] = $customer_id ?? '';
            $data['status'] = $status ?? '';
            $data['st_date'] = isset($st_date)?$st_date:'';
            $data['en_date'] = isset($en_date)?$en_date:'';

            $data['menu'] = view('Admin/menu_stock');
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Exchange_product/list', $data);
            } else {
                echo view('no_permission');
            }
        }
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
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Exchange_product/view', $data);
            } else {
                echo view('no_permission');
            }
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

            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Exchange_product/update', $data);
            } else {
                echo view('no_permission');
            }
        }
    }
}