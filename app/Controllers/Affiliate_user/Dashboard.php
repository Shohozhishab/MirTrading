<?php

namespace App\Controllers\Affiliate_user;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;

class Dashboard extends BaseController
{
    protected $validation;
    protected $session;

    public function __construct()
    {
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
    }

    /**
     * @description This method provides view
     * @return RedirectResponse|void
     */
    public function index()
    {
        $isLoggedInAffiliate = $this->session->isLoggedInAffiliate;
        if (!isset($isLoggedInAffiliate) || $isLoggedInAffiliate != TRUE) {
            return redirect()->to(site_url('Affiliate_user/login'));
        } else {

            $affiliateUserId = $this->session->affiliateUserId;

            $tableCommission = DB()->table('commission');
            $data['commissionAmount'] = $tableCommission->selectSum('commission_amount')->where('affiliate_user_id',$affiliateUserId)->get()->getRow()->commission_amount;

            $tableCommissionPat = DB()->table('commission_pay');
            $data['payAmount'] = $tableCommissionPat->selectSum('amount')->where('affiliate_user_id',$affiliateUserId)->get()->getRow()->amount;

            $data['totalDue'] = $data['commissionAmount'] - $data['payAmount'];


            echo view('Affiliate_user/header');
            echo view('Affiliate_user/sidebar');
            echo view('Affiliate_user/Dashboard/dashboard', $data);
            echo view('Affiliate_user/footer');
        }
    }
}