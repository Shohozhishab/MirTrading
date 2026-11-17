<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Ledger extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Ledger';

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
            $customer_id = $this->request->getGet('customer_id');
            $st_date = $this->request->getGet('st_date');
            $en_date = $this->request->getGet('en_date');
            $query = [];
            if (!empty($customer_id)) {
                $query = $this->customerLedgerQuery($customer_id, $st_date, $en_date);
            }

            $data['result'] = $query;

            $data['st_date'] = isset($st_date)?$st_date:'';
            $data['en_date'] = isset($en_date)?$en_date:'';
            $data['customer_id'] = $customer_id;

            $result = [];
            if (!empty($customer_id)) {
                $table = DB()->table('ledger');
                $table->where("customer_id", $customer_id);
                $result = $table->get()->getResult();
            }
            $data['mainResult'] = $result;



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
                echo view('Admin/Ledger/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method search customer ledger
     * @return void
     */
    public function search_customerLedg()
    {
        $shopId = $this->session->shopId;

        $customer_id = $this->request->getGet('customer_id');
        if (empty($customer_id)) {
            // keep supporting the legacy POST call
            $customer_id = $this->request->getPost('id');
        }
        $st_date = $this->request->getGet('st_date');
        $en_date = $this->request->getGet('en_date');

        $query = [];
        if (!empty($customer_id)) {
            $query = $this->customerLedgerQuery($customer_id, $st_date, $en_date);
        }
        $data['result'] = $query;
        $data['customer_id'] = $customer_id;
        $data['st_date'] = $st_date;
        $data['en_date'] = $en_date;
        $data['id'] = $shopId;

        print $this->customerLedgerBox($data['result'], $customer_id, $st_date, $en_date, $shopId);
    }

    /**
     * @description This method search customer ledger print
     * @return void
     */
    public function search_customerLedgPrint()
    {
        $customer_id = $this->request->getPost('id');
        $st_date = $this->request->getGet('st_date');
        $en_date = $this->request->getGet('en_date');

        if (empty($customer_id)) {
            print '';
            return;
        }

        $data = $this->customerLedgerQuery($customer_id, $st_date, $en_date);

        $view = '<div class="col-xs-12" style="text-transform: capitalize;" >'
            . view('Admin/Ledger/print_table', ['result' => $data, 'customer_id' => $customer_id, 'st_date' => $st_date, 'en_date' => $en_date])
            . '</div>';

        print $view;
    }

    /**
     * @description This method search customer ledger date to date
     * @return void
     */
    public function search_dateTodate()
    {
        $shopId = $this->session->shopId;

        $customer_id = $this->request->getPost('customerId');
        $st_date = $this->request->getPost('st_date');
        $en_date = $this->request->getPost('en_date');

        if (empty($customer_id)) {
            print '';
            return;
        }

        $query = $this->customerLedgerQuery($customer_id, $st_date, $en_date);
        $data['result'] = $query;
        $data['customer_id'] = $customer_id;
        $data['st_date'] = $st_date;
        $data['en_date'] = $en_date;
        $data['id'] = $shopId;

        print $this->customerLedgerBox($data['result'], $customer_id, $st_date, $en_date, $shopId);
    }

    /**
     * @description This method search customer ledger print
     * @return void
     */
    public function search_dateTodatePrint()
    {
        $customer_id = $this->request->getPost('customerId');
        $st_date = $this->request->getPost('st_date');
        $en_date = $this->request->getPost('en_date');

        if (empty($customer_id)) {
            print '';
            return;
        }

        $data = $this->customerLedgerQuery($customer_id, $st_date, $en_date);

        $view = '<div class="col-xs-12" style="text-transform: capitalize;" >'
            . view('Admin/Ledger/print_table', ['result' => $data, 'customer_id' => $customer_id, 'st_date' => $st_date, 'en_date' => $en_date])
            . '</div>';

        print $view;
    }

    /**
     * @description This method builds the customer ledger query with running balance
     * @param string|int $customer_id
     * @param string $st_date
     * @param string $en_date
     * @return array
     */
    private function customerLedgerQuery($customer_id, $st_date = '', $en_date = '')
    {
        $db = DB();
        // 1. Define the window function column
        $mBalanceSubquery = "SUM(
            CASE 
                WHEN LOWER(trangaction_type) LIKE 'dr%' THEN amount 
                WHEN LOWER(trangaction_type) LIKE 'cr%' THEN -amount 
                ELSE 0 
            END
        ) OVER (ORDER BY ledg_id) AS r_balance";
        // 2. Build the inner subquery
        $subquery = $db->table('ledger')
            ->select('ledger.*')
            ->select($mBalanceSubquery, false) // false prevents CI4 from escaping the raw SQL window function
            ->where("customer_id", $customer_id);
        // 3. Query from the subquery derived table
        $table = $db->newQuery()
            ->fromSubquery($subquery, 't')
            ->where("customer_id", $customer_id);
        if (!empty($st_date) && !empty($en_date)) {
            $table->where('createdDtm >=', $st_date . ' 00:00:00');
            $table->where('createdDtm <=', $en_date . ' 23:59:59');
        }
        $table->orderBy('ledg_id', 'ASC');

        return $table->get()->getResult();
    }

    /**
     * @description This method builds the customer ledger box (header + table)
     * @param array $result
     * @param string|int $customer_id
     * @param string $st_date
     * @param string $en_date
     * @param int $shopId
     * @return string
     */
    private function customerLedgerBox($result, $customer_id, $st_date = '', $en_date = '', $shopId = null)
    {
        if (empty($customer_id)) {
            return '';
        }

        $name = get_data_by_id('customer_name', 'customers', 'customer_id', $customer_id);
        $balance = get_data_by_id('balance', 'customers', 'customer_id', $customer_id);

        $start_date = !empty($st_date) ? $st_date : 0;
        $end_date = !empty($en_date) ? $en_date : 0;

        $view = ' <div class="box" >
                        <div class="box-header">
                            <h3 class="box-title">Customer: ' . $name . '</h3>
                            <span class="pull-right"><table class="table table-bordered table-striped" id="TFtable"><tr><td> Total Buy:</td><td>' . showWithCurrencySymbol(get_total('ledger', 'amount', 'Dr.', 'customer_id', $customer_id, $start_date, $end_date)) . '</td></tr><tr><td>Total Pay:</td><td>' . showWithCurrencySymbol(get_total('ledger', 'amount', 'Cr.', 'customer_id', $customer_id, $start_date, $end_date)) .
            '</td></tr><tr><td>Last Balance:</td><td>' . showWithCurrencySymbol($balance) . '</td></tr></table></span>
                        </div>
                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="example1">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Date</th>
                                    <th>Particulars</th>
                                    <th>Invoice Id/Memo</th>
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

            if (($result[$i]->invoice_id == NULL) && ($result[$i]->trans_id == NULL)) {
                $invoice_id = '---';
            } else {
                $invoice_id = ($result[$i]->invoice_id == NULL) ? '<a href="' . site_url('Admin/Transaction/moneyReceipt/' . $result[$i]->trans_id) . '">TRNS_' . $result[$i]->trans_id . '</a>' : '<a href="' . site_url('Admin/Invoice/view/' . $result[$i]->invoice_id) . '">INV_' . $result[$i]->invoice_id . '</a>';
            }
            $bankName = led_id_by_bank_name($result[$i]->invoice_id);
            $checkNumber = led_id_by_chaque_number($result[$i]->invoice_id);
            $view .= '<tr>
                                    <td>' . $result[$i]->ledg_id . '</td>
                                    <td>' . $result[$i]->createdDtm . '</td>
                                    <td>' . $particulars . '<br><small>'.$bankName. $checkNumber.'</small></td>
                                    <td>' . $invoice_id . '</td>
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
                                    <th>Invoice Id/Memo</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>Total</th>
                                    <th>= ' . showWithCurrencySymbol(get_total('ledger', 'amount', 'Dr.', 'customer_id', $customer_id, $start_date, $end_date)) . '</th>
                                    <th>= ' . showWithCurrencySymbol(get_total('ledger', 'amount', 'Cr.', 'customer_id', $customer_id, $start_date, $end_date)) . '</th>
                                    <th>= ' . showWithCurrencySymbol($balance) . '</th>
                                </tr>
                            </tfoot>
                        </table>
                        </div>
                    </div>';

        // printable version of the same (filtered) rows
        $view .= '<div id="ajaxCustLedgPrint" style="display: none;">'
            . view('Admin/Ledger/print_table', ['result' => $result, 'customer_id' => $customer_id, 'st_date' => $st_date, 'en_date' => $en_date])
            . '</div>';

        return $view;
    }


}