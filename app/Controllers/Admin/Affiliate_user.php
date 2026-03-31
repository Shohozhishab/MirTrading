<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Models\SuppliersModel;
use CodeIgniter\HTTP\RedirectResponse;


class Affiliate_user extends BaseController
{


    protected $suppliersModel;
    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'AffiliateUser';

    public function __construct()
    {
        $this->suppliersModel = new SuppliersModel();
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method view
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
            $table = DB()->table('affiliate_user');
            $data['user'] = $table->where('sch_id', $shopId)->orderBy('name', 'ASC')->get()->getResult();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Affiliate_user/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method provides suppliers create view
     * @return RedirectResponse|void
     */
    public function create()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $data['action'] = base_url('Admin/Affiliate_user/create_action');

            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['create'] == 1) {
                echo view('Admin/Affiliate_user/create', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store
     * @return void
     * @throws \ReflectionException
     */
    public function create_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['name'] = $this->request->getPost('name');
        $data['phone'] = $this->request->getPost('phone');
        $data['commission'] = $this->request->getPost('commission');
        $data['address'] = $this->request->getPost('address');
        $data['sch_id'] = $shopId;

        $this->validation->setRules([
            'commission' => ['label' => 'commission', 'rules' => 'required|is_natural_no_zero'],
            'name' => ['label' => 'name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
            'address' => ['label' => 'address', 'rules' => 'required'],
        ]);
        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $phoneUnique = is_unique('affiliate_user', 'phone', $data['phone']);
            if ($phoneUnique == true) {
                $table = DB()->table('affiliate_user');
                if ($table->insert($data)) {
                    print '<div class="alert alert-success alert-dismissible" role="alert"> Crate data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                } else {
                    print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                }
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">Phone number already in use  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }


    }
    /**
     * @description This method update view
     * @param int $id
     * @return RedirectResponse|void
     */
    public function update($id)
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;
            $data['action'] = base_url('Admin/Affiliate_user/update_action');

            $table = DB()->table('affiliate_user');
            $data['affiliateUser'] = $table->where('affiliate_user_id', $id)->where('sch_id', $shopId)->get()->getRow();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['update'] == 1) {
                echo view('Admin/Affiliate_user/update', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method update suppliers
     * @return void
     * @throws \ReflectionException
     */
    public function update_action()
    {
        $userId = $this->session->userId;

        $affiliate_user_id = $this->request->getPost('affiliate_user_id');
        $data['name'] = $this->request->getPost('name');
        $data['phone'] = $this->request->getPost('phone');
        $data['commission'] = $this->request->getPost('commission');
        $data['address'] = $this->request->getPost('address');

        $this->validation->setRules([
            'commission' => ['label' => 'commission', 'rules' => 'required|is_natural_no_zero'],
            'name' => ['label' => 'name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
            'address' => ['label' => 'address', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $phoneUnique = is_unique_update('affiliate_user', 'phone', $data['phone'], 'affiliate_user_id', $affiliate_user_id);
            if ($phoneUnique == true) {
                $table = DB()->table('affiliate_user');
                if ($table->where('affiliate_user_id', $affiliate_user_id)->update($data)) {
                    print '<div class="alert alert-success alert-dismissible" role="alert"> Update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                } else {
                    print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                }
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">Phone number already in use  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }

    /**
     * @param int $affiliate_user_id
     * @return void
     */
    public function delete($affiliate_user_id){
        $table = DB()->table('affiliate_user');
        $table->where('affiliate_user_id',$affiliate_user_id)->delete();

        $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert"> Delete data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        return redirect()->to(site_url('Admin/Affiliate_user'));
    }

    public function commissionPay($affiliate_user_id){
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;
            $data['action'] = base_url('Admin/Affiliate_user/commission_pay_action');

            $table = DB()->table('affiliate_user');
            $data['affiliateUser'] = $table->where('affiliate_user_id', $affiliate_user_id)->where('sch_id', $shopId)->get()->getRow();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['update'] == 1) {
                echo view('Admin/Affiliate_user/pay', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    public function commission_pay_action(){
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $affiliate_user_id = $this->request->getPost('affiliate_user_id');
        $balance = $this->request->getPost('balance');

        $amount = str_replace(',', '', $this->request->getPost('amount'));
        //Supplier Balance

        $restBalance = $balance - $amount;
        //Payment Type
        $paymentType = $this->request->getPost('payment_type');
        //shop data
        $shopBalance = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
        $shopUpdateBalance = $shopBalance - $amount;

        if ($paymentType == 1) {
            $bankId = $this->request->getPost('bank_id');
            if ($bankId) {
                $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $bankId);
                $bankUpData = $bankCash - $amount;
            }
            $availableBalance = checkBankBalance($bankId, $amount);

            if (empty($bankId)) {
                print '<div class="alert alert-danger alert-dismissible" role="alert">Please select a bank <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                die();
            }
        }
        if ($paymentType == 2) {
            $availableBalance = checkNagadBalance($amount);
        }


        if ($amount < 0) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            die();
        }

        if ($availableBalance == true) {

            DB()->transStart();

            //Suppliers Balance Update
            $userRestBalance = array(
                'balance' => $balance - $amount,
            );
            $affiliateUserTable = DB()->table('affiliate_user');
            $affiliateUserTable->where('affiliate_user_id', $affiliate_user_id)->update($userRestBalance);
            //insert log (end)

            //insert data
            $ledgerExpenseCommission = get_data_by_id('ledger_expense_commission', 'shops', 'sch_id', $shopId);
            $newLedgerExpenseCommission = $ledgerExpenseCommission + $amount;
            $shopDataExpense = array(
                'ledger_expense_commission' => $newLedgerExpenseCommission,
            );
            $shopsTab = DB()->table('shops');
            $shopsTab->where('sch_id', $shopId)->update($shopDataExpense);

            $data = array(
                'sch_id' => $shopId,
                'commission_id' => $affiliate_user_id,
                'particulars' => $this->request->getPost('particulars'),
                'trangaction_type' => 'Dr.',
                'amount' => $amount,
                'rest_balance' => $newLedgerExpenseCommission,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledgerTab = DB()->table('ledger_expense_commission');
            $ledgerTab->insert($data);


            //admin transaction
            if ($paymentType == 2) {
                //shop balance update
                $shopData = array(
                    'cash' => $shopUpdateBalance,
                    'updatedBy' => $userId,
                );
                $shopsTab = DB()->table('shops');
                $shopsTab->where('sch_id', $shopId)->update($shopData);

                //insert ledger_nagodan
                $lgNagData = array(
                    'sch_id' => $shopId,
                    'commission_id' => $affiliate_user_id,
                    'trangaction_type' => 'Cr.',
                    'particulars' => $this->request->getPost('particulars'),
                    'amount' => $amount,
                    'rest_balance' => $shopUpdateBalance,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $ledger_nagodanTab = DB()->table('ledger_nagodan');
                $ledger_nagodanTab->insert($lgNagData);

            } else {
                $bankData = array(
                    'balance' => $bankUpData,
                    'updatedBy' => $userId,
                );
                $bankTab = DB()->table('bank');
                $bankTab->where('bank_id', $bankId)->update($bankData);

                //insert ledger_bank
                $lgBankData = array(
                    'sch_id' => $shopId,
                    'bank_id' => $bankId,
                    'commission_id' => $affiliate_user_id,
                    'trangaction_type' => 'Cr.',
                    'particulars' => $this->request->getPost('particulars'),
                    'amount' => $amount,
                    'rest_balance' => $bankUpData,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $ledger_bankTab = DB()->table('ledger_bank');
                $ledger_bankTab->insert($lgBankData);
                //insert log (end)
            }

            DB()->transComplete();

            print '<div class="alert alert-success alert-dismissible" role="alert">Your Commission pay is successful<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

        } else {
            print '<div class="alert alert-danger alert-dismissible" role="alert">Not Enough Balance<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }

    }


}