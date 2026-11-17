<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Ledger_bank extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Ledger_bank';

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

            $bank_id = $this->request->getGet('bank_id');
            $st_date = $this->request->getGet('st_date');
            $en_date = $this->request->getGet('en_date');
            $query = [];

            if (!empty($bank_id)) {
                $query = $this->bankLedgerQuery($bank_id, $st_date, $en_date);
            }

            $data['result'] = $query;




            $data['st_date'] = isset($st_date)?$st_date:'';
            $data['en_date'] = isset($en_date)?$en_date:'';
            $data['bank_id'] = $bank_id;

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
                echo view('Admin/Ledger_bank/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method search bank ledger
     * @return void
     */
    public function search_bankLedg()
    {
        $shopId = $this->session->shopId;

        $bank_id = $this->request->getGet('bank_id');
        if (empty($bank_id)) {
            // keep supporting the legacy POST call
            $bank_id = $this->request->getPost('id');
        }
        $st_date = $this->request->getGet('st_date');
        $en_date = $this->request->getGet('en_date');

        $query = [];
        if (!empty($bank_id)) {
            $query = $this->bankLedgerQuery($bank_id, $st_date, $en_date);
        }
        $data['result'] = $query;
        $data['bank_id'] = $bank_id;
        $data['st_date'] = $st_date;
        $data['en_date'] = $en_date;
        $data['id'] = $shopId;

        print $this->bankLedgerBox($data['result'], $bank_id, $st_date, $en_date, $shopId);
    }

    /**
     * @description This method bank ledger print
     * @return void
     */
    public function search_bankLedgPrint()
    {

        $bank_id = $this->request->getPost('id');
        $st_date = $this->request->getGet('st_date');
        $en_date = $this->request->getGet('en_date');

        if (empty($bank_id)) {
            print '';
            return;
        }

        $data = $this->bankLedgerQuery($bank_id, $st_date, $en_date);

        $view = '<div class="col-xs-12" style="text-transform: capitalize;" >'
            . view('Admin/Ledger_bank/print_table', ['result' => $data, 'bank_id' => $bank_id, 'st_date' => $st_date, 'en_date' => $en_date])
            . '</div>';

        print $view;
    }

    /**
     * @description This method search bank ledger date to date
     * @return void
     */
    public function search_dateTodate()
    {
        $shopId = $this->session->shopId;

        $bank_id = $this->request->getPost('bankId');
        $st_date = $this->request->getPost('st_date');
        $en_date = $this->request->getPost('en_date');

        if (empty($bank_id)) {
            print '';
            return;
        }

        $query = $this->bankLedgerQuery($bank_id, $st_date, $en_date);
        $data['result'] = $query;
        $data['bank_id'] = $bank_id;
        $data['st_date'] = $st_date;
        $data['en_date'] = $en_date;
        $data['id'] = $shopId;

        print $this->bankLedgerBox($data['result'], $bank_id, $st_date, $en_date, $shopId);
    }

    /**
     * @description This method bank ledger print
     * @return void
     */
    public function search_dateTodatePrint()
    {
        $bank_id = $this->request->getPost('bankId');
        $st_date = $this->request->getPost('st_date');
        $en_date = $this->request->getPost('en_date');

        if (empty($bank_id)) {
            print '';
            return;
        }

        $data = $this->bankLedgerQuery($bank_id, $st_date, $en_date);

        $view = '<div class="col-xs-12" style="text-transform: capitalize;" >'
            . view('Admin/Ledger_bank/print_table', ['result' => $data, 'bank_id' => $bank_id, 'st_date' => $st_date, 'en_date' => $en_date])
            . '</div>';

        print $view;
    }

    /**
     * @description This method builds the bank ledger query with running balance
     * @param string|int $bank_id
     * @param string $st_date
     * @param string $en_date
     * @return array
     */
    private function bankLedgerQuery($bank_id, $st_date = '', $en_date = '')
    {
        $db = DB();
        // 1. Define the window function column
        $mBalanceSubquery = "SUM(
            CASE 
                WHEN LOWER(trangaction_type) LIKE 'dr%' THEN amount 
                WHEN LOWER(trangaction_type) LIKE 'cr%' THEN -amount 
                ELSE 0 
            END
        ) OVER (ORDER BY ledgBank_id) AS r_balance";
        // 2. Build the inner subquery
        $subquery = $db->table('ledger_bank')
            ->select('ledger_bank.*')
            ->select($mBalanceSubquery, false) // false prevents CI4 from escaping the raw SQL window function
            ->where("bank_id", $bank_id);
        // 3. Query from the subquery derived table
        $table = $db->newQuery()
            ->fromSubquery($subquery, 't')
            ->where("bank_id", $bank_id);
        if (!empty($st_date) && !empty($en_date)) {
            $table->where('createdDtm >=', $st_date . ' 00:00:00');
            $table->where('createdDtm <=', $en_date . ' 23:59:59');
        }
        $table->orderBy('ledgBank_id', 'ASC');

        return $table->get()->getResult();
    }

    /**
     * @description This method builds the bank ledger box (header + table)
     * @param array $result
     * @param string|int $bank_id
     * @param string $st_date
     * @param string $en_date
     * @param int $shopId
     * @return string
     */
    private function bankLedgerBox($result, $bank_id, $st_date = '', $en_date = '', $shopId = null)
    {
        if (empty($bank_id)) {
            return '';
        }

        $name = get_data_by_id('name', 'bank', 'bank_id', $bank_id);
        $account = get_data_by_id('account_no', 'bank', 'bank_id', $bank_id);
        $balance = get_data_by_id('balance', 'bank', 'bank_id', $bank_id);

        $start_date = !empty($st_date) ? $st_date : 0;
        $end_date = !empty($en_date) ? $en_date : 0;

        $view = ' <div class="box" >
                        <div class="box-header">
                            <h3 class="box-title">Bank: ' . $name . ' -- ' . $account . '</h3>
                            <span class="pull-right"><table class="table table-bordered table-striped" id="TFtable"><tr><td>Total Deposit:</td><td>' . showWithCurrencySymbol(get_total('ledger_bank', 'amount', 'Dr.', 'bank_id', $bank_id, $start_date, $end_date)) . '</td></tr><tr><td>Total Withdraw:</td><td>' . showWithCurrencySymbol(get_total('ledger_bank', 'amount', 'Cr.', 'bank_id', $bank_id, $start_date, $end_date)) . '</td></tr><tr><td>Balance:</td><td>' . showWithCurrencySymbol($balance) . '</td></tr></table></span>
                        </div>
                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="example1">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Date</th>
                                    <th>Particulars</th>
                                    <th>Tran Id</th>
                                    <th>purc Id</th>
                                    <th>inv Id</th>
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
            $transId = ($result[$i]->trans_id == NULL) ? "---" : '<a href="'.base_url('Admin/Transaction/read/'.$result[$i]->trans_id).'"> TRNS_'.$result[$i]->trans_id.'</a>';
            $purchaseId = ($result[$i]->purchase_id == NULL) ? "---" : '<a href="'.base_url('Admin/Purchase/view/'.$result[$i]->purchase_id).'"> PURS_'.$result[$i]->purchase_id.'</a>';
            $invoiceId = ($result[$i]->invoice_id == 0) ? "---" : '<a href="'.base_url('Admin/Invoice/view/'.$result[$i]->invoice_id).'"> INV_'.$result[$i]->invoice_id.'</a>';
            $view .= '<tr>
                                    <td>' . $result[$i]->ledgBank_id . '</td>
                                    <td>' . $result[$i]->createdDtm . '</td>
                                    <td>' . $particulars . '</td>
                                    <td>' . $transId . '</td>
                                    <td>' . $purchaseId . '</td>
                                    <td>' . $invoiceId . '</td>
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
                                    <th>Tran Id</th>
                                    <th>purc Id</th>
                                    <th>inv Id</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>Total</th>
                                    <th>= ' . showWithCurrencySymbol(get_total('ledger_bank', 'amount', 'Dr.', 'bank_id', $bank_id, $start_date, $end_date)) . '</th>
                                    <th>= ' . showWithCurrencySymbol(get_total('ledger_bank', 'amount', 'Cr.', 'bank_id', $bank_id, $start_date, $end_date)) . '</th>
                                    <th>= ' . showWithCurrencySymbol($balance) . '</th>
                                </tr>
                            </tfoot>
                        </table>
                        </div>
                    </div>';

        // printable version of the same (filtered) rows
        $view .= '<div id="ajaxBankLedgPrint" style="display: none;">'
            . view('Admin/Ledger_bank/print_table', ['result' => $result, 'bank_id' => $bank_id, 'st_date' => $st_date, 'en_date' => $en_date])
            . '</div>';

        return $view;
    }


}