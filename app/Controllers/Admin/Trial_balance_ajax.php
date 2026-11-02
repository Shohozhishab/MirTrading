<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Models\SuppliersModel;
use CodeIgniter\HTTP\RedirectResponse;


class Trial_balance_ajax extends BaseController
{


    protected $suppliersModel;
    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Trial_balance';

    public function __construct()
    {
        $this->suppliersModel = new SuppliersModel();
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides trial balance view
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

            // -------------------------------------------------
            // Shop main data (single query)
            // -------------------------------------------------
            $shop = DB()->table('shops')
                ->where('sch_id', $shopId)
                ->get()
                ->getRow();

            $cash                    = $shop->cash ?? 0;
            $purchaseBalance         = $shop->purchase_balance ?? 0;
            $saleBalance             = $shop->sale_balance ?? 0;
            $expense                 = $shop->expense ?? 0;
            $capital                 = $shop->capital ?? 0;
            $serviceCharge           = $shop->service_charge ?? 0;
            $ledgerExpenseCommission = $shop->ledger_expense_commission ?? 0;

            // Currently forced to 0 (as in original)
            $stockAmount = 0;
            $profit      = 0;

            // -------------------------------------------------
            // Bank
            // -------------------------------------------------
            $bankData = DB()->table('bank')
                ->where('sch_id', $shopId)
                ->get()
                ->getResult();

            $bankCash = DB()->table('bank')
                ->selectSum('balance')
                ->where('sch_id', $shopId)
                ->get()
                ->getRow()
                ->balance ?? 0;

            // -------------------------------------------------
            // Customers (debit / credit)
            // -------------------------------------------------
            $customerCash = DB()->table('customers')
                ->selectSum('balance')
                ->where(['sch_id' => $shopId, 'balance >' => 0])
                ->get()
                ->getRow()
                ->balance ?? 0;

            $custCredit = DB()->table('customers')
                ->selectSum('balance')
                ->where(['sch_id' => $shopId, 'balance <' => 0])
                ->get()
                ->getRow()
                ->balance ?? 0;

            // -------------------------------------------------
            // Loan Provider (debit / credit)
            // -------------------------------------------------
            $loanCash = DB()->table('loan_provider')
                ->selectSum('balance')
                ->where(['sch_id' => $shopId, 'balance >' => 0])
                ->get()
                ->getRow()
                ->balance ?? 0;

            $loanCredit = DB()->table('loan_provider')
                ->selectSum('balance')
                ->where(['sch_id' => $shopId, 'balance <' => 0])
                ->get()
                ->getRow()
                ->balance ?? 0;

            // -------------------------------------------------
            // Suppliers (debit / credit)
            // -------------------------------------------------
            $supplierCash = DB()->table('suppliers')
                ->selectSum('balance')
                ->where(['sch_id' => $shopId, 'balance >' => 0])
                ->get()
                ->getRow()
                ->balance ?? 0;

            $supplierCredit = DB()->table('suppliers')
                ->selectSum('balance')
                ->where(['sch_id' => $shopId, 'balance <' => 0])
                ->get()
                ->getRow()
                ->balance ?? 0;

            // -------------------------------------------------
            // Employee
            // -------------------------------------------------
            $employee = DB()->table('employee')
                ->where('sch_id', $shopId)
                ->get()
                ->getResult();

            $emplBal = DB()->table('employee')
                ->selectSum('balance')
                ->where('sch_id', $shopId)
                ->get()
                ->getRow()
                ->balance ?? 0;

            // -------------------------------------------------
            // Accounts - Assets
            // -------------------------------------------------
            $accountsAssets = DB()->table('accounts')
                ->join('accounts_account_type_map', 'accounts_account_type_map.account_id = accounts.account_id')
                ->join('account_type', 'account_type.account_type_id = accounts_account_type_map.account_type_id')
                ->where('accounts.sch_id', $shopId)
                ->where('account_type.type_key', 'assets')
                ->get()
                ->getResult();

            $assets = DB()->table('accounts')
                ->selectSum('accounts.balance')
                ->join('accounts_account_type_map', 'accounts_account_type_map.account_id = accounts.account_id')
                ->join('account_type', 'account_type.account_type_id = accounts_account_type_map.account_type_id')
                ->where('accounts.sch_id', $shopId)
                ->where('account_type.type_key', 'assets')
                ->get()
                ->getRow()
                ->balance ?? 0;

            // -------------------------------------------------
            // Accounts - Expenses
            // -------------------------------------------------
            $accountsExpenses = DB()->table('accounts')
                ->join('accounts_account_type_map', 'accounts_account_type_map.account_id = accounts.account_id')
                ->join('account_type', 'account_type.account_type_id = accounts_account_type_map.account_type_id')
                ->where('accounts.sch_id', $shopId)
                ->where('account_type.type_key', 'expenses')
                ->get()
                ->getResult();

            $expenses = DB()->table('accounts')
                ->selectSum('accounts.balance')
                ->join('accounts_account_type_map', 'accounts_account_type_map.account_id = accounts.account_id')
                ->join('account_type', 'account_type.account_type_id = accounts_account_type_map.account_type_id')
                ->where('accounts.sch_id', $shopId)
                ->where('account_type.type_key', 'expenses')
                ->get()
                ->getRow()
                ->balance ?? 0;

            // -------------------------------------------------
            // Accounts - Other Income
            // -------------------------------------------------
            $otherIncomeData = DB()->table('accounts')
                ->join('accounts_account_type_map', 'accounts_account_type_map.account_id = accounts.account_id')
                ->join('account_type', 'account_type.account_type_id = accounts_account_type_map.account_type_id')
                ->where('accounts.sch_id', $shopId)
                ->where('account_type.type_key', 'other_income')
                ->get()
                ->getResult();

            $otherIncome = DB()->table('accounts')
                ->selectSum('accounts.balance')
                ->join('accounts_account_type_map', 'accounts_account_type_map.account_id = accounts.account_id')
                ->join('account_type', 'account_type.account_type_id = accounts_account_type_map.account_type_id')
                ->where('accounts.sch_id', $shopId)
                ->where('account_type.type_key', 'other_income')
                ->get()
                ->getRow()
                ->balance ?? 0;

            // -------------------------------------------------
            // VAT
            // -------------------------------------------------
            $vatEarn = DB()->table('vat_register')
                ->where('sch_id', $shopId)
                ->get()
                ->getRow()
                ->balance ?? 0;

            // -------------------------------------------------
            // Final calculations (exactly same as original)
            // -------------------------------------------------
            $totalDue   = $customerCash + $loanCash + $supplierCash + $assets + $expenses;
            $totalDebit = $totalDue + $cash + $bankCash + $stockAmount + $emplBal + $expense + $ledgerExpenseCommission + $purchaseBalance;

            $totalAmo    = $custCredit + $loanCredit + $supplierCredit;
            $totalCredit = $totalAmo + $capital + $profit + $vatEarn + $serviceCharge + $otherIncome + $saleBalance;

            // -------------------------------------------------
            // Full lists for view
            // -------------------------------------------------
            $customerData  = DB()->table('customers')->where('sch_id', $shopId)->get()->getResult();
            $loanProData   = DB()->table('loan_provider')->where('sch_id', $shopId)->get()->getResult();
            $supplierData  = DB()->table('suppliers')->where('sch_id', $shopId)->get()->getResult();

            // -------------------------------------------------
            // Return data (same keys as original)
            // -------------------------------------------------
            $data = [
                'allDue'                   => $totalDue,
                'cash'                     => $cash,
                'bankCash'                 => $bankCash,
                'bankData'                 => $bankData,
                'totalDebit'               => $totalDebit,
                'totalAmo'                 => $totalAmo,
                'vatEarn'                  => $vatEarn,
                'totalCredit'              => $totalCredit,
                'customerData'             => $customerData,
                'loanProData'              => $loanProData,
                'supplierData'             => $supplierData,
                'capitalcr'                => $capital,
                'expensedata'              => $expense,
                'profit'                   => $profit,
                'service_charge'           => $serviceCharge,
                'stockAmount'              => $stockAmount,
                'employee'                 => $employee,
                'accountsAssets'           => $accountsAssets,
                'accountsExpenses'         => $accountsExpenses,
                'ledger_expense_commission'=> $ledgerExpenseCommission,
                'otherIncome'              => $otherIncomeData,
                'saleBalance'              => $saleBalance,
                'purchaseBalance'          => $purchaseBalance,
            ];


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Trial_balance/list', $data);
            } else {
                echo view('no_permission');
            }
        }
    }


}