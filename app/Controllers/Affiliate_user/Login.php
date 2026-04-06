<?php

namespace App\Controllers\Affiliate_user;

use App\Controllers\BaseController;
use App\Models\LicenseModel;
use App\Models\ShopsModel;
use App\Models\UsersModel;
use CodeIgniter\HTTP\RedirectResponse;

class Login extends BaseController
{
    protected $usersModel;
    protected $licenseModel;
    protected $shopsModel;
    protected $validation;
    protected $session;

    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->licenseModel = new LicenseModel();
        $this->shopsModel = new ShopsModel();
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
            print view('Affiliate_user/login');
        } else {
            return redirect()->to(site_url('Admin/Dashboard'));
        }
    }

    /**
     * @description This method provides login action
     * @return RedirectResponse
     */
    public function action()
    {
        $this->validation->setRule('phone', 'Phone', 'required');
        $this->validation->setRule('password', 'Password', 'required|max_length[32]');

        if ($this->validation->withRequest($this->request)->run() == FALSE) {

            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">All field is required <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url());
        } else {

            $phone =  $this->request->getPost('phone');
            $password = $this->request->getPost('password');

            $result = $this->loginMe($phone, $password);

            if (!empty($result)) {

                // Remember me (Remembering the user email and password) Start
                if (!empty($this->request->getPost("remember"))) {

                    setcookie('login_phone', $phone, time() + (86400 * 30), "/");
                    setcookie('login_password', $password, time() + (86400 * 30), "/");

                } else {
                    if (isset($_COOKIE['login_email'])) {
                        setcookie('login_phone', '', 0, "/");
                    }
                    if (isset($_COOKIE['login_password'])) {
                        setcookie('login_password', '', 0, "/");
                    }
                }
                // Remember me (Remembering the user email and password) End



                $sessionArray = array('affiliateUserId' => $result->affiliate_user_id,
                    'name' => $result->name,
                    'isLoggedInAffiliate' => TRUE
                );
                $this->session->set($sessionArray);

                return redirect()->to(site_url('Affiliate_user/dashboard'));


            } else {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Your login detail not match <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url());
            }

        }
    }

    /**
     * @description This method data check
     * @param string $phone
     * @param string $password
     * @return array|object
     */
    private function loginMe($phone, $password)
    {
        $table = DB()->table('affiliate_user');
        $user = $table->where('phone', $phone)->get()->getRow();

        if (!empty($user)) {
            if (SHA1($password) == $user->password) {
                return $user;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    /**
     * @description This method session destroy
     * @return RedirectResponse
     */
    public function logout(){

        unset($_SESSION['affiliateUserId']);
        unset($_SESSION['name']);
        unset($_SESSION['isLoggedInAffiliate']);

        return redirect()->to('/Affiliate_user');
    }


}
