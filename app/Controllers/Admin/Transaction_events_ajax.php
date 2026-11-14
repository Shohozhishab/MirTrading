<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Transaction_events_ajax extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'TransactionEvents';

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
            $event_type = $this->request->getGet('event_type');

            $data['result'] = $this->transactionEventsQuery($st_date, $en_date, $event_type, $shopId);
            $data['st_date'] = isset($st_date)?$st_date:'';
            $data['en_date'] = isset($en_date)?$en_date:'';
            $data['event_type'] = isset($event_type)?$event_type:'';

            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Transaction_events/list', $data);
            } else {
                echo view('no_permission');
            }
        }
    }

    /**
     * @description Query transaction events with filters
     */
    private function transactionEventsQuery($st_date = '', $en_date = '', $event_type = '', $shopId = null)
    {
        if ($shopId === null) {
            $shopId = $this->session->shopId;
        }
        $table = DB()->table('transaction_events');
        $table->where('sch_id', $shopId);

        if (!empty($st_date)) {
            $table->where('createdDtm >=', $st_date . ' 00:00:00');
        }
        if (!empty($en_date)) {
            $table->where('createdDtm <=', $en_date . ' 23:59:59');
        }

        if ($event_type == 'transaction') {
            $table->where('trans_id IS NOT NULL')->where('trans_id !=', '');
        } elseif ($event_type == 'sales') {
            $table->where('sales_id IS NOT NULL')->where('sales_id !=', '');
        } elseif ($event_type == 'purchase') {
            $table->where('purchase_id IS NOT NULL')->where('purchase_id !=', '');
        } elseif ($event_type == 'deposit') {
            $table->where('dep_id IS NOT NULL')->where('dep_id !=', '');
        } elseif ($event_type == 'withdraw') {
            $table->where('wthd_id IS NOT NULL')->where('wthd_id !=', '');
        }

        return $table->orderBy('transaction_event_id', 'DESC')->get()->getResult();
    }
}
