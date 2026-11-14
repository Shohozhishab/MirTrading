<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Ledger_accounts extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Ledger_accounts';

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
            $account_id = $this->request->getGet('account_id');
            $st_date = $this->request->getGet('st_date');
            $en_date = $this->request->getGet('en_date');
            $query = [];

            if (!empty($account_id)) {
                $query = $this->accountLedgerQuery($account_id, $st_date, $en_date);
            }
            $data['result'] = $query;

            $data['st_date'] = isset($st_date) ? $st_date : '';
            $data['en_date'] = isset($en_date) ? $en_date : '';
            $data['account_id'] = $account_id;

            $data['id'] = $shopId;
            $data['shopId'] = $shopId;
            $data['menu'] = view('Admin/menu_ledger');
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Ledger_accounts/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method search account ledger
     * @return void
     */
    public function search_accountLedg()
    {
        $shopId = $this->session->shopId;

        $account_id = $this->request->getGet('account_id');
        if (empty($account_id)) {
            // keep supporting legacy POST call
            $account_id = $this->request->getPost('id');
        }
        $st_date = $this->request->getGet('st_date');
        $en_date = $this->request->getGet('en_date');

        $query = [];
        if (!empty($account_id)) {
            $query = $this->accountLedgerQuery($account_id, $st_date, $en_date);
        }
        $data['result'] = $query;
        $data['account_id'] = $account_id;
        $data['st_date'] = $st_date;
        $data['en_date'] = $en_date;
        $data['id'] = $shopId;

        print $this->accountLedgerBox($data['result'], $account_id, $st_date, $en_date, $shopId);
    }

    /**
     * @description This method search account ledger print
     * @return void
     */
    public function search_accountLedgPrint()
    {
        $account_id = $this->request->getPost('id');
        $st_date = $this->request->getGet('st_date');
        $en_date = $this->request->getGet('en_date');

        if (empty($account_id)) {
            print '';
            return;
        }

        $data = $this->accountLedgerQuery($account_id, $st_date, $en_date);

        $view = '<div class="col-xs-12" style="text-transform: capitalize;" >'
            . view('Admin/Ledger_accounts/print_table', ['result' => $data, 'account_id' => $account_id, 'st_date' => $st_date, 'en_date' => $en_date])
            . '</div>';

        print $view;
    }

    /**
     * @description This method search account ledger date to date
     * @return void
     */
    public function search_dateTodate()
    {
        $shopId = $this->session->shopId;

        $account_id = $this->request->getPost('accountId') ?? $this->request->getPost('account_id') ?? $this->request->getGet('account_id');
        $st_date = $this->request->getPost('st_date') ?? $this->request->getGet('st_date');
        $en_date = $this->request->getPost('en_date') ?? $this->request->getGet('en_date');

        if (empty($account_id)) {
            print '';
            return;
        }

        $query = $this->accountLedgerQuery($account_id, $st_date, $en_date);
        $data['result'] = $query;
        $data['account_id'] = $account_id;
        $data['st_date'] = $st_date;
        $data['en_date'] = $en_date;
        $data['id'] = $shopId;

        print $this->accountLedgerBox($data['result'], $account_id, $st_date, $en_date, $shopId);
    }

    /**
     * @description This method search account ledger date to date print
     * @return void
     */
    public function search_dateTodatePrint()
    {
        $account_id = $this->request->getPost('accountId') ?? $this->request->getPost('account_id') ?? $this->request->getGet('account_id');
        $st_date = $this->request->getPost('st_date') ?? $this->request->getGet('st_date');
        $en_date = $this->request->getPost('en_date') ?? $this->request->getGet('en_date');

        if (empty($account_id)) {
            print '';
            return;
        }

        $data = $this->accountLedgerQuery($account_id, $st_date, $en_date);

        $view = '<div class="col-xs-12" style="text-transform: capitalize;" >'
            . view('Admin/Ledger_accounts/print_table', ['result' => $data, 'account_id' => $account_id, 'st_date' => $st_date, 'en_date' => $en_date])
            . '</div>';

        print $view;
    }

    /**
     * @description This method builds the account ledger query with running balance
     * @param string|int $account_id
     * @param string $st_date
     * @param string $en_date
     * @return array
     */
    private function accountLedgerQuery($account_id, $st_date = '', $en_date = '')
    {
        $db = DB();
        // 1. Define the window function column
        $mBalanceSubquery = "SUM(
            CASE 
                WHEN LOWER(trangaction_type) LIKE 'dr%' THEN amount 
                WHEN LOWER(trangaction_type) LIKE 'cr%' THEN -amount 
                ELSE 0 
            END
        ) OVER (ORDER BY ledg_account_id) AS r_balance";
        // 2. Build the inner subquery
        $subquery = $db->table('ledger_accounts')
            ->select('ledger_accounts.*')
            ->select($mBalanceSubquery, false)
            ->where("account_id", $account_id); // false prevents CI4 from escaping the raw SQL window function
        // 3. Query from the subquery derived table
        $table = $db->newQuery()
            ->fromSubquery($subquery, 't')
            ->where("account_id", $account_id);
        if (!empty($st_date) && !empty($en_date)) {
            $table->where('createdDtm >=', $st_date . ' 00:00:00');
            $table->where('createdDtm <=', $en_date . ' 23:59:59');
        }
        $table->orderBy('ledg_account_id', 'ASC');

        return $table->get()->getResult();
    }

    /**
     * @description This method builds the account ledger box (header + table)
     * @param array $result
     * @param string|int $account_id
     * @param string $st_date
     * @param string $en_date
     * @param int|null $shopId
     * @return string
     */
    private function accountLedgerBox($result, $account_id, $st_date = '', $en_date = '', $shopId = null)
    {
        if (empty($account_id)) {
            return '';
        }

        $name = get_data_by_id('name', 'accounts', 'account_id', $account_id);
        $phone = get_data_by_id('phone', 'accounts', 'account_id', $account_id);
        $balance = get_data_by_id('balance', 'accounts', 'account_id', $account_id);

        $start_date = !empty($st_date) ? $st_date : 0;
        $end_date = !empty($en_date) ? $en_date : 0;

        $phoneText = !empty($phone) ? ' (' . $phone . ')' : '';

        $view = '<div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Account: ' . $name . $phoneText . '</h3>
                        <span class="pull-right"><table class="table table-bordered table-striped" id="TFtable"><tr><td>Total Debit:</td><td>' . showWithCurrencySymbol(get_total('ledger_accounts', 'amount', 'Dr.', 'account_id', $account_id, $start_date, $end_date)) . '</td></tr><tr><td>Total Credit:</td><td>' . showWithCurrencySymbol(get_total('ledger_accounts', 'amount', 'Cr.', 'account_id', $account_id, $start_date, $end_date)) . '</td></tr><tr><td>Balance:</td><td>' . showWithCurrencySymbol($balance) . '</td></tr></table></span>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="example1">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Date</th>
                                <th>Particulars</th>
                                <th>Transaction Id</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>Balance</th>
                            </tr>
                            </thead>
                            <tbody>';

        $totalRows = count($result) - 1;
        for ($i = 0; $i <= $totalRows; $i++) {
            $particulars = ($result[$i]->particulars == NULL) ? "Pay due" : $result[$i]->particulars;
            $amountCr = ($result[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($result[$i]->amount);
            $amountDr = ($result[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($result[$i]->amount);
            $trnUrl = (!empty($result[$i]->trans_id)) ? '<a href="' . site_url('Admin/Transaction/read/' . $result[$i]->trans_id) . '">TRNS_' . $result[$i]->trans_id . '</a>' : '--';

            $view .= '<tr>
                        <td>' . $result[$i]->ledg_account_id . '</td>
                        <td>' . $result[$i]->createdDtm . '</td>
                        <td>' . $particulars . '</td>
                        <td>' . $trnUrl . '</td>
                        <td>' . $amountDr . '</td>
                        <td>' . $amountCr . '</td>
                        <td>' . showWithCurrencySymbol($result[$i]->r_balance) . '</td>
                    </tr>';
        }

        $view .= '</tbody>
                    <tfoot>
                    <tr>
                        <th>Id</th>
                        <th>Date</th>
                        <th>Particulars</th>
                        <th>Transaction Id</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th>Balance</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Total</th>
                        <th>= ' . showWithCurrencySymbol(get_total('ledger_accounts', 'amount', 'Dr.', 'account_id', $account_id, $start_date, $end_date)) . '</th>
                        <th>= ' . showWithCurrencySymbol(get_total('ledger_accounts', 'amount', 'Cr.', 'account_id', $account_id, $start_date, $end_date)) . '</th>
                        <th>= ' . showWithCurrencySymbol($balance) . '</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>';

        // printable version of the same (filtered) rows
        $view .= '<div id="ajaxAccountLedgPrint" style="display: none;">'
            . view('Admin/Ledger_accounts/print_table', ['result' => $result, 'account_id' => $account_id, 'st_date' => $st_date, 'en_date' => $en_date])
            . '</div>';

        return $view;
    }


}
