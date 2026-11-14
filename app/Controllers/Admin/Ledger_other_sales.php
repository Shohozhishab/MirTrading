<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Ledger_other_sales extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Ledger_other_sales';

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

            $st_date = $this->request->getGet('st_date');
            $en_date = $this->request->getGet('en_date');

            $data['ledger_data'] = $this->otherSalesLedgerQuery($st_date, $en_date, $shopId);
            $data['ledger_other_sales'] = $data['ledger_data'];

            $data['st_date'] = isset($st_date) ? $st_date : '';
            $data['en_date'] = isset($en_date) ? $en_date : '';
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
                echo view('Admin/Ledger_other_sales/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method search other sales ledger date to date
     * @return void
     */
    public function search_dateTodate()
    {
        $shopId = $this->session->shopId;

        $st_date = $this->request->getPost('st_date') ?? $this->request->getGet('st_date');
        $en_date = $this->request->getPost('en_date') ?? $this->request->getGet('en_date');

        $query = $this->otherSalesLedgerQuery($st_date, $en_date, $shopId);

        print $this->otherSalesLedgerBox($query, $st_date, $en_date, $shopId);
    }

    /**
     * @description This method search other sales ledger print date to date
     * @return void
     */
    public function search_dateTodatePrint()
    {
        $shopId = $this->session->shopId;

        $st_date = $this->request->getPost('st_date') ?? $this->request->getGet('st_date');
        $en_date = $this->request->getPost('en_date') ?? $this->request->getGet('en_date');

        $query = $this->otherSalesLedgerQuery($st_date, $en_date, $shopId);

        $view = '<div class="col-xs-12" style="text-transform: capitalize;" >'
            . view('Admin/Ledger_other_sales/print_table', ['ledger_data' => $query, 'st_date' => $st_date, 'en_date' => $en_date])
            . '</div>';

        print $view;
    }

    /**
     * @description This method builds the other sales ledger query with running balance
     * @param string $st_date
     * @param string $en_date
     * @param int|null $shopId
     * @return array
     */
    private function otherSalesLedgerQuery($st_date = '', $en_date = '', $shopId = null)
    {
        $shopId = $shopId ?? $this->session->shopId;
        $db = DB();
        // 1. Define the window function column
        $mBalanceSubquery = "SUM(
            CASE 
                WHEN LOWER(trangaction_type) LIKE 'dr%' THEN amount 
                WHEN LOWER(trangaction_type) LIKE 'cr%' THEN -amount 
                ELSE 0 
            END
        ) OVER (ORDER BY ledg_oth_sales_id) AS r_balance";
        // 2. Build the inner subquery
        $subquery = $db->table('ledger_other_sales')
            ->select('ledger_other_sales.*')
            ->select($mBalanceSubquery, false)
            ->where('sch_id', $shopId); // false prevents CI4 from escaping the raw SQL window function
        // 3. Query from the subquery derived table
        $table = $db->newQuery()
            ->fromSubquery($subquery, 't')
            ->where('sch_id', $shopId);
        if (!empty($st_date) && !empty($en_date)) {
            $table->where('createdDtm >=', $st_date . ' 00:00:00');
            $table->where('createdDtm <=', $en_date . ' 23:59:59');
        }
        $table->orderBy('ledg_oth_sales_id', 'ASC');

        return $table->get()->getResult();
    }

    /**
     * @description This method builds the other sales ledger box (table + print container)
     * @param array $ledger_data
     * @param string $st_date
     * @param string $en_date
     * @param int|null $shopId
     * @return string
     */
    private function otherSalesLedgerBox($ledger_data, $st_date = '', $en_date = '', $shopId = null)
    {
        $shopId = $shopId ?? $this->session->shopId;

        $start_date = !empty($st_date) ? $st_date : 0;
        $end_date = !empty($en_date) ? $en_date : 0;

        $totalDr = (float) get_total_nogodBalance('ledger_other_sales', 'amount', 'Dr.', $start_date, $end_date);
        $totalCr = (float) get_total_nogodBalance('ledger_other_sales', 'amount', 'Cr.', $start_date, $end_date);
        $netBalance = $totalDr - $totalCr;

        $view = '<div class="box">
                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="example1">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Date</th>
                                <th>Particulars</th>
                                <th>Memo</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>Balance</th>
                            </tr>
                            </thead>
                            <tbody>';

        $totalRows = count($ledger_data) - 1;
        for ($i = 0; $i <= $totalRows; $i++) {
            $particulars = ($ledger_data[$i]->particulars == NULL) ? "Payment" : $ledger_data[$i]->particulars;
            $amountCr = ($ledger_data[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($ledger_data[$i]->amount);
            $amountDr = ($ledger_data[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($ledger_data[$i]->amount);
            $memo = !empty($ledger_data[$i]->trans_id) ? 'TR_' . $ledger_data[$i]->trans_id : (!empty($ledger_data[$i]->chaque_id) ? 'CHQ_' . $ledger_data[$i]->chaque_id : '---');

            $view .= '<tr>
                        <td>' . $ledger_data[$i]->ledg_oth_sales_id . '</td>
                        <td>' . $ledger_data[$i]->createdDtm . '</td>
                        <td>' . $particulars . '</td>
                        <td>' . $memo . '</td>
                        <td>' . $amountDr . '</td>
                        <td>' . $amountCr . '</td>
                        <td>' . showWithCurrencySymbol($ledger_data[$i]->r_balance) . '</td>
                    </tr>';
        }

        $view .= '</tbody>
                    <tfoot>
                    <tr>
                        <th>Id</th>
                        <th>Date</th>
                        <th>Particulars</th>
                        <th>Memo</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th>Balance</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Total</th>
                        <th>= ' . showWithCurrencySymbol($totalDr) . '</th>
                        <th>= ' . showWithCurrencySymbol($totalCr) . '</th>
                        <th>= ' . showWithCurrencySymbol($netBalance) . '</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>';

        // printable version of the same (filtered) rows
        $view .= '<div id="ajaxOtherSalesLedgPrint" style="display: none;">'
            . view('Admin/Ledger_other_sales/print_table', ['ledger_data' => $ledger_data, 'st_date' => $st_date, 'en_date' => $en_date])
            . '</div>';

        return $view;
    }


}
