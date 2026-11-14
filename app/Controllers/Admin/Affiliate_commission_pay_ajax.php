<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Affiliate_commission_pay_ajax extends BaseController
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
        $this->request = \Config\Services::request();
    }

    /**
     * @description This method provides affiliate commission pay view
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
            $affiliate_user_id = $this->request->getGet('affiliate_user_id');
            $st_date = $this->request->getGet('st_date');
            $en_date = $this->request->getGet('en_date');

            $userTable = DB()->table('affiliate_user');
            $data['user'] = $userTable->where('sch_id', $shopId)->get()->getResult();

            $data['commission'] = $this->affiliateCommissionPayQuery($affiliate_user_id, $st_date, $en_date, $shopId);
            $data['affiliate_user_id'] = $affiliate_user_id ?? '';
            $data['st_date'] = $st_date ?? '';
            $data['en_date'] = $en_date ?? '';
            $data['shopId'] = $shopId;

            // All Permissions
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Affiliate_commission_pay/list', $data);
            } else {
                echo view('no_permission');
            }
        }
    }

    /**
     * @description This method handles date-to-date AJAX search for affiliate commission pay
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

        $affiliate_user_id = !empty($this->request->getPost('affiliate_user_id')) ? $this->request->getPost('affiliate_user_id') : $this->request->getGet('affiliate_user_id');
        $st_date = !empty($this->request->getPost('st_date')) ? $this->request->getPost('st_date') : $this->request->getGet('st_date');
        $en_date = !empty($this->request->getPost('en_date')) ? $this->request->getPost('en_date') : $this->request->getGet('en_date');

        $commission = $this->affiliateCommissionPayQuery($affiliate_user_id, $st_date, $en_date, $shopId);

        $perm = $this->permission->module_permission_list($role_id, $this->module_name);
        $data = [];
        foreach ($perm as $key => $val) {
            $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
        }

        $data['commission'] = $commission;
        $data['affiliate_user_id'] = $affiliate_user_id ?? '';
        $data['st_date'] = $st_date ?? '';
        $data['en_date'] = $en_date ?? '';
        $data['shopId'] = $shopId;

        return view('Admin/Affiliate_commission_pay/table_list', $data);
    }

    /**
     * @description This method handles date-to-date print view for affiliate commission pay
     * @return string|RedirectResponse
     */
    public function search_dateTodatePrint()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        }

        $shopId = $this->session->shopId;

        $affiliate_user_id = !empty($this->request->getPost('affiliate_user_id')) ? $this->request->getPost('affiliate_user_id') : $this->request->getGet('affiliate_user_id');
        $st_date = !empty($this->request->getPost('st_date')) ? $this->request->getPost('st_date') : $this->request->getGet('st_date');
        $en_date = !empty($this->request->getPost('en_date')) ? $this->request->getPost('en_date') : $this->request->getGet('en_date');

        $commission = $this->affiliateCommissionPayQuery($affiliate_user_id, $st_date, $en_date, $shopId);

        $view = '<div class="col-xs-12" style="text-transform: capitalize;">'
            . view('Admin/Affiliate_commission_pay/print_table', [
                'commission' => $commission,
                'affiliate_user_id' => $affiliate_user_id,
                'st_date' => $st_date,
                'en_date' => $en_date,
                'shopId' => $shopId,
            ])
            . '</div>';

        return $view;
    }

    /**
     * @description Helper to query affiliate commission pay records
     * @param string|int $affiliate_user_id
     * @param string $st_date
     * @param string $en_date
     * @param int|null $shopId
     * @return array
     */
    public function affiliateCommissionPayQuery($affiliate_user_id = '', $st_date = '', $en_date = '', $shopId = null)
    {
        $shopId = $shopId ?? $this->session->shopId;

        $table = DB()->table('commission_pay');
        $table->where('sch_id', $shopId);

        if (!empty($affiliate_user_id)) {
            $table->where('affiliate_user_id', $affiliate_user_id);
        }
        if (!empty($st_date) && !empty($en_date)) {
            $table->where('date >=', $st_date . ' 00:00:00');
            $table->where('date <=', $en_date . ' 23:59:59');
        } elseif (!empty($st_date)) {
            $table->where('date >=', $st_date . ' 00:00:00');
        } elseif (!empty($en_date)) {
            $table->where('date <=', $en_date . ' 23:59:59');
        }

        return $table->orderBy('commission_pay_id', 'DESC')->get()->getResult();
    }
}
