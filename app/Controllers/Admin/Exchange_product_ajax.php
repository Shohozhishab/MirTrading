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
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Exchange_product/view', $data);
            } else {
                echo view('no_permission');
            }
        }
    }

}