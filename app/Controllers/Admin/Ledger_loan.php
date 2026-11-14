<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Ledger_loan extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Ledger_loan';

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

            $loan_pro_id = $this->request->getGet('loan_pro_id');
            $st_date = $this->request->getGet('st_date');
            $en_date = $this->request->getGet('en_date');
            $query = [];
            if (!empty($loan_pro_id)) {
                $query = $this->loanLedgerQuery($loan_pro_id, $st_date, $en_date);
            }
            $data['result'] = $query;

            $data['st_date'] = isset($st_date)?$st_date:'';
            $data['en_date'] = isset($en_date)?$en_date:'';
            $data['loan_pro_id'] = $loan_pro_id;

            $data['id'] = $shopId;
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
                echo view('Admin/Ledger_loan/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method  search lone provider ledger
     * @return void
     */
    public function search_lonProvLedg()
    {
        $shopId = $this->session->shopId;

        $loan_pro_id = $this->request->getGet('loan_pro_id');
        if (empty($loan_pro_id)) {
            // keep supporting the legacy POST call
            $loan_pro_id = $this->request->getPost('id');
        }
        $st_date = $this->request->getGet('st_date');
        $en_date = $this->request->getGet('en_date');

        $query = [];
        if (!empty($loan_pro_id)) {
            $query = $this->loanLedgerQuery($loan_pro_id, $st_date, $en_date);
        }
        $data['result'] = $query;
        $data['loan_pro_id'] = $loan_pro_id;
        $data['st_date'] = $st_date;
        $data['en_date'] = $en_date;
        $data['id'] = $shopId;

        print $this->loanLedgerBox($data['result'], $loan_pro_id, $st_date, $en_date, $shopId);
    }

    /**
     * @description This method search lone provider ledger print
     * @return void
     */
    public function search_lonProvLedgPrint()
    {
        $lonproviId = $this->request->getPost('id');

        $ledger_loanTb = DB()->table('ledger_loan');
        $data = $ledger_loanTb->where('loan_pro_id', $lonproviId)->orderBy('ledg_loan_id', "DESC")->get()->getResult();

        $logo = (logo_image() == NULL) ? 'no_image.jpg' : logo_image();

        $view = '<div class="col-xs-12" style="text-transform: capitalize;" >
                            <div class="col-xs-12" style="margin-bottom: 20px;   ">
                                <div class="col-xs-6">
                                    <img src="' . base_url() . '/uploads/schools/' . $logo . '" alt="User Image" >
                                </div>
                                <div class="col-xs-6">
                                    ' . address() . '
                                </div>
                            </div>
                    <div class="col-xs-12">
                        <table class="table table-bordered table-striped" >
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Particulars</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>';

        foreach ($data as $row) {

            $particulars = ($row->particulars == NULL) ? "Pay due" : $row->particulars;
            $amountCr = ($row->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($row->amount);
            $amountDr = ($row->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($row->amount);
            $view .= '<tr>
                                    <td>' . bdDateFormat($row->createdDtm) . '</td>
                                    <td>' . $particulars . '</td>
                                    <td>' . $amountDr . '</td>
                                    <td>' . $amountCr . '</td>
                                    <td>' . showWithCurrencySymbol($row->rest_balance) . '</td>
                                </tr>';
        }

        $view .= '</tbody>                            
                        </table>
                        </div>
                    </div>';

        $view .= '<div class="row no-print">
                                <div class="col-xs-12">'
            . anchor(site_url('ledger_loan/print/' . $lonproviId), '<i class="fa fa-print"></i> Print Now', 'target="_blank" class="print_line btn btn-primary pull-right"') .
            '</div>
                              </div>';

        print $view;
    }

    /**
     * @description This method search lone provider ledger date to date
     * @return void
     */
    public function search_dateTodate()
    {
        $shopId = $this->session->shopId;

        $lonproviId = $this->request->getPost('loanProId');
        $st_date = $this->request->getPost('st_date');
        $en_date = $this->request->getPost('en_date');

        if (empty($lonproviId)) {
            print '';
            return;
        }

        $query = $this->loanLedgerQuery($lonproviId, $st_date, $en_date);
        $data['result'] = $query;

        print $this->loanLedgerBox($data['result'], $lonproviId, $st_date, $en_date, $shopId);
    }

    /**
     * @description This method search lone provider ledger print
     * @return void
     */
    public function search_dateTodatePrint()
    {
        $lonproviId = $this->request->getPost('loanProId');
        $st_date = $this->request->getPost('st_date');
        $en_date = $this->request->getPost('en_date');

        if (empty($lonproviId)) {
            print '';
            return;
        }

        $db = DB();
        // 1. Define the window function column
        $mBalanceSubquery = "SUM(
            CASE 
                WHEN LOWER(trangaction_type) LIKE 'dr%' THEN amount 
                WHEN LOWER(trangaction_type) LIKE 'cr%' THEN -amount 
                ELSE 0 
            END
        ) OVER (ORDER BY ledg_loan_id) AS r_balance";
        // 2. Build the inner subquery
        $subquery = $db->table('ledger_loan')
            ->select('ledger_loan.*')
            ->select($mBalanceSubquery, false)
            ->where("loan_pro_id", $lonproviId);
        // 3. Query from the subquery derived table
        $table = $db->newQuery()
            ->fromSubquery($subquery, 't')
            ->where("loan_pro_id", $lonproviId);
        if (!empty($st_date) && !empty($en_date)) {
            $table->where('createdDtm >=', $st_date . ' 00:00:00');
            $table->where('createdDtm <=', $en_date . ' 23:59:59');
        }
        $table->orderBy('ledg_loan_id', 'ASC');
        $data = $table->get()->getResult();

        $logo = (logo_image() == NULL) ? 'no_image.jpg' : logo_image();

        $view = '<div class="col-xs-12" style="text-transform: capitalize;" >'
            . view('Admin/Ledger_loan/print_table', ['result' => $data, 'loan_pro_id' => $lonproviId, 'st_date' => $st_date, 'en_date' => $en_date])
            . '</div>';

        print $view;
    }

    /**
     * @description This method builds the account head ledger query with running balance
     * @param string|int $loan_pro_id
     * @param string $st_date
     * @param string $en_date
     * @return array
     */
    private function loanLedgerQuery($loan_pro_id, $st_date = '', $en_date = '')
    {
        $db = DB();
        // 1. Define the window function column
        $mBalanceSubquery = "SUM(
            CASE 
                WHEN LOWER(trangaction_type) LIKE 'dr%' THEN amount 
                WHEN LOWER(trangaction_type) LIKE 'cr%' THEN -amount 
                ELSE 0 
            END
        ) OVER (ORDER BY ledg_loan_id) AS r_balance";
        // 2. Build the inner subquery
        $subquery = $db->table('ledger_loan')
            ->select('ledger_loan.*')
            ->select($mBalanceSubquery, false) // false prevents CI4 from escaping the raw SQL window function
            ->where("loan_pro_id", $loan_pro_id);
        // 3. Query from the subquery derived table
        $table = $db->newQuery()
            ->fromSubquery($subquery, 't')
            ->where("loan_pro_id", $loan_pro_id);
        if (!empty($st_date) && !empty($en_date)) {
            $table->where('createdDtm >=', $st_date . ' 00:00:00');
            $table->where('createdDtm <=', $en_date . ' 23:59:59');
        }
        $table->orderBy('ledg_loan_id', 'ASC');

        return $table->get()->getResult();
    }

    /**
     * @description This method builds the account head ledger box (header + table)
     * @param array $result
     * @param string|int $loan_pro_id
     * @param string $st_date
     * @param string $en_date
     * @param int $shopId
     * @return string
     */
    private function loanLedgerBox($result, $loan_pro_id, $st_date = '', $en_date = '', $shopId = null)
    {
        if (empty($loan_pro_id)) {
            return '';
        }

        $name = get_data_by_id('name', 'loan_provider', 'loan_pro_id', $loan_pro_id);
        $balance = get_data_by_id('balance', 'loan_provider', 'loan_pro_id', $loan_pro_id);

        $start_date = !empty($st_date) ? $st_date : 0;
        $end_date = !empty($en_date) ? $en_date : 0;

        $view = ' <div class="box" >
                        <div class="box-header">
                            <h3 class="box-title">Account Head: ' . $name . '</h3>
                            <span class="pull-right"><table class="table table-bordered table-striped" id="TFtable"><tr><td>Total Get:</td><td>' . showWithCurrencySymbol(get_total('ledger_loan', 'amount', 'Dr.', 'loan_pro_id', $loan_pro_id, $start_date, $end_date)) . '</td></tr><tr><td>Total Pay:</td><td>' . showWithCurrencySymbol(get_total('ledger_loan', 'amount', 'Cr.', 'loan_pro_id', $loan_pro_id, $start_date, $end_date)) . '</td></tr><tr><td>Balance:</td><td>' . showWithCurrencySymbol($balance) . '</td></tr></table></span>
                        </div>
                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="example1">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Date</th>
                                    <th>Particulars</th>
                                    <th>Trangaction Id</th>
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
                                    <td>' . $result[$i]->ledg_loan_id . '</td>
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
                                    <th>Trangaction Id</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>Total</th>
                                    <th>= ' . showWithCurrencySymbol(get_total('ledger_loan', 'amount', 'Dr.', 'loan_pro_id', $loan_pro_id, $start_date, $end_date)) . '</th>
                                    <th>= ' . showWithCurrencySymbol(get_total('ledger_loan', 'amount', 'Cr.', 'loan_pro_id', $loan_pro_id, $start_date, $end_date)) . '</th>
                                    <th>= ' . showWithCurrencySymbol($balance) . '</th>
                                </tr>
                            </tfoot>
                        </table>
                        </div>
                    </div>';

        // printable version of the same (filtered) rows
        $view .= '<div id="ajaxLedgPrint" style="display: none;">'
            . view('Admin/Ledger_loan/print_table', ['result' => $result, 'loan_pro_id' => $loan_pro_id, 'st_date' => $st_date, 'en_date' => $en_date])
            . '</div>';

        return $view;
    }


}