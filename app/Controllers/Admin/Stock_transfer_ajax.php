<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Stock_transfer_ajax extends BaseController
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
            $table = DB()->table('stock_transfer');
            $data['transfer'] = $table->where('sch_id', $shopId)->get()->getResult();

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
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Stock_transfer/list', $data);
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

            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Stock_transfer/view', $data);
            } else {
                echo view('no_permission');
            }
        }
    }

}