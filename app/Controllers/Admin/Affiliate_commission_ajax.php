<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Affiliate_commission_ajax extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'AffiliateUser';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides stock report view
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
            $data['affiliate_user_id'] = $this->request->getGet('affiliate_user_id');
            $data['st_date'] = $this->request->getGet('st_date');
            $data['en_date'] = $this->request->getGet('en_date');


            $userTable = DB()->table('affiliate_user');
            $data['user'] = $userTable->where('sch_id',$shopId)->get()->getResult();

            $table = DB()->table('commission');
            $data['commission'] = $table->where('sch_id',$shopId)->get()->getResult();

            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Affiliate_commission/list', $data);
            } else {
                echo view('no_permission');
            }
        }
    }
}