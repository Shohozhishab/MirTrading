<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Ledger_vat extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Ledger_vat';

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

            $data['ledger_vat_data'] = $this->vatLedgerQuery($st_date, $en_date, $shopId);
            $data['ledger_vat'] = $data['ledger_vat_data'];

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
                echo view('Admin/Ledger_vat/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method search vat ledger date to date
     * @return void
     */
    public function search_dateTodate()
    {
        $shopId = $this->session->shopId;

        $st_date = $this->request->getPost('st_date') ?? $this->request->getGet('st_date');
        $en_date = $this->request->getPost('en_date') ?? $this->request->getGet('en_date');

        $query = $this->vatLedgerQuery($st_date, $en_date, $shopId);

        print $this->vatLedgerBox($query, $st_date, $en_date, $shopId);
    }

    /**
     * @description This method search vat ledger print date to date
     * @return void
     */
    public function search_dateTodatePrint()
    {
        $shopId = $this->session->shopId;

        $st_date = $this->request->getPost('st_date') ?? $this->request->getGet('st_date');
        $en_date = $this->request->getPost('en_date') ?? $this->request->getGet('en_date');

        $query = $this->vatLedgerQuery($st_date, $en_date, $shopId);

        $view = '<div class="col-xs-12" style="text-transform: capitalize;" >'
            . view('Admin/Ledger_vat/print_table', ['ledger_vat_data' => $query, 'st_date' => $st_date, 'en_date' => $en_date])
            . '</div>';

        print $view;
    }

    /**
     * @description This method builds the vat ledger query with running balance
     * @param string $st_date
     * @param string $en_date
     * @param int|null $shopId
     * @return array
     */
    private function vatLedgerQuery($st_date = '', $en_date = '', $shopId = null)
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
        ) OVER (ORDER BY ledg_vat_id) AS r_balance";
        // 2. Build the inner subquery
        $subquery = $db->table('ledger_vat')
            ->select('ledger_vat.*')
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
        $table->orderBy('ledg_vat_id', 'ASC');

        return $table->get()->getResult();
    }

    /**
     * @description This method builds the vat ledger box (table + print container)
     * @param array $ledger_vat_data
     * @param string $st_date
     * @param string $en_date
     * @param int|null $shopId
     * @return string
     */
    private function vatLedgerBox($ledger_vat_data, $st_date = '', $en_date = '', $shopId = null)
    {
        $shopId = $shopId ?? $this->session->shopId;
        $vatBalance = get_data_by_id('balance', 'vat_register', 'sch_id', $shopId);

        $start_date = !empty($st_date) ? $st_date : 0;
        $end_date = !empty($en_date) ? $en_date : 0;

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

        $totalRows = count($ledger_vat_data) - 1;
        for ($i = 0; $i <= $totalRows; $i++) {
            $particulars = ($ledger_vat_data[$i]->particulars == NULL) ? "Payment" : $ledger_vat_data[$i]->particulars;
            $amountCr = ($ledger_vat_data[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($ledger_vat_data[$i]->amount);
            $amountDr = ($ledger_vat_data[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($ledger_vat_data[$i]->amount);
            $memo = !empty($ledger_vat_data[$i]->invoice_id) ? '<a href="' . base_url('Admin/Invoice/view/' . $ledger_vat_data[$i]->invoice_id) . '">INV_' . $ledger_vat_data[$i]->invoice_id . '</a>' : (!empty($ledger_vat_data[$i]->trans_id) ? 'TR_' . $ledger_vat_data[$i]->trans_id : '---');

            $view .= '<tr>
                        <td>' . $ledger_vat_data[$i]->ledg_vat_id . '</td>
                        <td>' . $ledger_vat_data[$i]->createdDtm . '</td>
                        <td>' . $particulars . '</td>
                        <td>' . $memo . '</td>
                        <td>' . $amountDr . '</td>
                        <td>' . $amountCr . '</td>
                        <td>' . showWithCurrencySymbol($ledger_vat_data[$i]->r_balance) . '</td>
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
                        <th>= ' . showWithCurrencySymbol(get_total_nogodBalance('ledger_vat', 'amount', 'Dr.', $start_date, $end_date)) . '</th>
                        <th>= ' . showWithCurrencySymbol(get_total_nogodBalance('ledger_vat', 'amount', 'Cr.', $start_date, $end_date)) . '</th>
                        <th>= ' . showWithCurrencySymbol($vatBalance) . '</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>';

        // printable version of the same (filtered) rows
        $view .= '<div id="ajaxVatLedgPrint" style="display: none;">'
            . view('Admin/Ledger_vat/print_table', ['ledger_vat_data' => $ledger_vat_data, 'st_date' => $st_date, 'en_date' => $en_date])
            . '</div>';

        return $view;
    }


}
