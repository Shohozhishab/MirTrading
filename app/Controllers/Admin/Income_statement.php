<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Models\SuppliersModel;
use CodeIgniter\HTTP\RedirectResponse;


class Income_statement extends BaseController
{


    protected $suppliersModel;
    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'IncomeStatement';

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

            $st_date = $this->request->getGet('st_date');
            $en_date = $this->request->getGet('en_date');
            $period = $this->request->getGet('period');
            $month = $this->request->getGet('month');

            $data = $this->getIncomeStatementData($st_date, $en_date, $period, $month, $shopId);

            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/IncomeStatement/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description Helper to compute income statement data with filters
     */
    private function getIncomeStatementData($st_date = '', $en_date = '', $period = '', $month = '', $shopId = null)
    {
        if ($shopId === null) {
            $shopId = $this->session->shopId;
        }

        // Handle month filter
        if (!empty($month) && $month >= 1 && $month <= 12) {
            $year = date('Y');
            $st_date = date('Y-m-01', strtotime("$year-$month-01"));
            $en_date = date('Y-m-t', strtotime("$year-$month-01"));
            $days = 0;
            $period = '';
        } elseif (!empty($st_date) && !empty($en_date)) {
            $days = round((strtotime($en_date) - strtotime($st_date)) / 86400) + 1;
        } else {
            $days = !empty($period) ? (int)$period : 30;
            $en_date = date('Y-m-d');
            $st_date = date('Y-m-d', strtotime('-' . $days . ' days'));
        }

        $applyDateFilter = function ($builder, string $column) use ($st_date, $en_date) {
            $builder->where("$column >=", $st_date . ' 00:00:00')
                ->where("$column <=", $en_date . ' 23:59:59');
            return $builder;
        };

        $getSum = function ($row, string $field = 'amount'): float {
            return ($row && $row->$field !== null) ? (float)$row->$field : 0.0;
        };

        // Purchase Amount
        $tablePurchase = DB()->table('purchase')
            ->selectSum('amount')
            ->where('sch_id', $shopId);
        $applyDateFilter($tablePurchase, 'date');
        $purchaseAmount = $getSum($tablePurchase->get()->getRow());

        // Sales Amount
        $tableSale = DB()->table('sales')
            ->selectSum('invoice.final_amount')
            ->join('invoice', 'invoice.invoice_id = sales.invoice_id')
            ->where('sales.sch_id', $shopId);
        $applyDateFilter($tableSale, 'sales.date');
        $sales = $getSum($tableSale->get()->getRow(), 'final_amount');

        // Capital Amount
        $tableCapital = DB()->table('capital')
            ->selectSum('amount')
            ->where('sch_id', $shopId);
        $applyDateFilter($tableCapital, 'createdDtm');
        $capital = $getSum($tableCapital->get()->getRow());

        // Profit + Service Charge + Other Income
        $tableProfit = DB()->table('ledger_profit')
            ->selectSum('amount')
            ->where('sch_id', $shopId);
        $applyDateFilter($tableProfit, 'createdDtm');
        $profit = $getSum($tableProfit->get()->getRow());

        $tableCharge = DB()->table('ledger_service_charge')
            ->selectSum('amount')
            ->where('sch_id', $shopId);
        $applyDateFilter($tableCharge, 'createdDtm');
        $charge = $getSum($tableCharge->get()->getRow());

        $otherTable = DB()->table('accounts')
            ->selectSum('ledger_accounts.amount', 'other_income')
            ->join('accounts_account_type_map', 'accounts_account_type_map.account_id = accounts.account_id')
            ->join('account_type', 'account_type.account_type_id = accounts_account_type_map.account_type_id')
            ->join('ledger_accounts', 'ledger_accounts.account_id = accounts.account_id')
            ->where('accounts.sch_id', $shopId)
            ->where('account_type.type_key', 'other_income');
        $applyDateFilter($otherTable, 'ledger_accounts.createdDtm');
        $otherIncome = $getSum($otherTable->get()->getRow(), 'other_income');

        // Expenses
        $expenseTable = DB()->table('accounts')
            ->selectSum('ledger_accounts.amount', 'expenses')
            ->join('accounts_account_type_map', 'accounts_account_type_map.account_id = accounts.account_id')
            ->join('account_type', 'account_type.account_type_id = accounts_account_type_map.account_type_id')
            ->join('ledger_accounts', 'ledger_accounts.account_id = accounts.account_id')
            ->where('accounts.sch_id', $shopId)
            ->where('account_type.type_key', 'expenses');
        $applyDateFilter($expenseTable, 'ledger_accounts.createdDtm');
        $expenses = $getSum($expenseTable->get()->getRow(), 'expenses');

        $totalProfit = $profit + $charge + $otherIncome;
        $profitLoss = $totalProfit - $expenses;

        return [
            'purchaseAmount' => $purchaseAmount,
            'sales' => $sales,
            'capital' => $capital,
            'profit' => $totalProfit,
            'expenses' => $expenses,
            'profitLoss' => $profitLoss,
            'st_date' => $st_date,
            'en_date' => $en_date,
            'period' => $period,
            'month' => $month,
            'days' => $days,
        ];
    }

    /**
     * @description This method provides income statement table view via AJAX
     * @return string|void
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
        $period = !empty($this->request->getPost('period')) ? $this->request->getPost('period') : $this->request->getGet('period');
        $month = !empty($this->request->getPost('month')) ? $this->request->getPost('month') : $this->request->getGet('month');

        $data = $this->getIncomeStatementData($st_date, $en_date, $period, $month, $shopId);

        $perm = $this->permission->module_permission_list($role_id, $this->module_name);
        foreach ($perm as $key => $val) {
            $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
        }

        return view('Admin/IncomeStatement/table_list', $data);
    }

    /**
     * @description This method provides income statement print view
     * @return string|void
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
        $period = !empty($this->request->getPost('period')) ? $this->request->getPost('period') : $this->request->getGet('period');
        $month = !empty($this->request->getPost('month')) ? $this->request->getPost('month') : $this->request->getGet('month');

        $data = $this->getIncomeStatementData($st_date, $en_date, $period, $month, $shopId);
        $data['shopId'] = $shopId;

        return view('Admin/IncomeStatement/print_table', $data);
    }
}
