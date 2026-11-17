<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Models\ShopsModel;
use CodeIgniter\HTTP\RedirectResponse;


class Capital_ajax extends BaseController
{


    protected $shopsModel;
    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Capital';

    public function __construct()
    {
        $this->shopsModel = new ShopsModel();
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
        $this->request = \Config\Services::request();
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
            $data['shopId'] = $shopId;

            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Capital/list', $data);
            } else {
                echo view('no_permission');
            }
        }
    }

    public function list()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;
            $st_date = $this->request->getGet('st_date');
            $en_date = $this->request->getGet('en_date');

            $data['capital'] = $this->capitalQuery($st_date, $en_date, $shopId);
            $data['st_date'] = isset($st_date) ? $st_date : '';
            $data['en_date'] = isset($en_date) ? $en_date : '';
            $data['shopId'] = $shopId;

            // All Permissions
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Capital/index', $data);
            } else {
                echo view('no_permission');
            }
        }
    }

    /**
     * @description This method handles date-to-date AJAX search for capital list
     * @return string|RedirectResponse
     */
    public function search_dateTodate()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        }

        $shopId = $this->session->shopId;

        $st_date = !empty($this->request->getPost('st_date')) ? $this->request->getPost('st_date') : $this->request->getGet('st_date');
        $en_date = !empty($this->request->getPost('en_date')) ? $this->request->getPost('en_date') : $this->request->getGet('en_date');

        $capital = $this->capitalQuery($st_date, $en_date, $shopId);

        $perm = $this->permission->module_permission_list($role_id, $this->module_name);
        $data = [];
        foreach ($perm as $key => $val) {
            $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
        }

        $data['capital'] = $capital;
        $data['st_date'] = $st_date ?? '';
        $data['en_date'] = $en_date ?? '';
        $data['shopId'] = $shopId;

        return view('Admin/Capital/table_list', $data);
    }

    /**
     * @description This method handles date-to-date print view for capital list
     * @return string|RedirectResponse
     */
    public function search_dateTodatePrint()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        }

        $shopId = $this->session->shopId;

        $st_date = !empty($this->request->getPost('st_date')) ? $this->request->getPost('st_date') : $this->request->getGet('st_date');
        $en_date = !empty($this->request->getPost('en_date')) ? $this->request->getPost('en_date') : $this->request->getGet('en_date');

        $capital = $this->capitalQuery($st_date, $en_date, $shopId);

        $view = '<div class="col-xs-12" style="text-transform: capitalize;">'
            . view('Admin/Capital/print_table', [
                'capital' => $capital,
                'st_date' => $st_date,
                'en_date' => $en_date,
                'shopId' => $shopId,
            ])
            . '</div>';

        return $view;
    }

    /**
     * @description Helper to query capital records
     * @param string $st_date
     * @param string $en_date
     * @param int|null $shopId
     * @return array
     */
    public function capitalQuery($st_date = '', $en_date = '', $shopId = null)
    {
        $shopId = $shopId ?? $this->session->shopId;

        $table = DB()->table('capital');
        $table->where('sch_id', $shopId);

        if (!empty($st_date) && !empty($en_date)) {
            $table->where('createdDtm >=', $st_date . ' 00:00:00');
            $table->where('createdDtm <=', $en_date . ' 23:59:59');
        } elseif (!empty($st_date)) {
            $table->where('createdDtm >=', $st_date . ' 00:00:00');
        } elseif (!empty($en_date)) {
            $table->where('createdDtm <=', $en_date . ' 23:59:59');
        }

        return $table->orderBy('capital_id', 'DESC')->get()->getResult();
    }


}