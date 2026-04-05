<?php

namespace App\Controllers\Affiliate_user;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;

class Commission extends BaseController
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
            $data['commission'] = $tableCommission->where('affiliate_user_id',$affiliateUserId)->get()->getResult();

            echo view('Affiliate_user/header');
            echo view('Affiliate_user/sidebar');
            echo view('Affiliate_user/Commission/index', $data);
            echo view('Affiliate_user/footer');
        }
    }
}