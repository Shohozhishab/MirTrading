<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Ledger_suppliers extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Ledger_suppliers';

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

            $supplier_id = $this->request->getGet('supplier_id');
            $st_date = $this->request->getGet('st_date');
            $en_date = $this->request->getGet('en_date');
            $query = [];
            if (!empty($supplier_id)) {
                $query = $this->supplierLedgerQuery($supplier_id, $st_date, $en_date);
            }
            $data['result'] = $query;

            $data['st_date'] = isset($st_date) ? $st_date : '';
            $data['en_date'] = isset($en_date) ? $en_date : '';
            $data['supplier_id'] = $supplier_id;

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
                echo view('Admin/Ledger_suppliers/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method search supplier ledger
     * @return void
     */
    public function search_supplierLedg()
    {
        $shopId = $this->session->shopId;

        $supplier_id = $this->request->getGet('supplier_id');
        if (empty($supplier_id)) {
            // keep supporting legacy POST call
            $supplier_id = $this->request->getPost('id');
        }
        $st_date = $this->request->getGet('st_date');
        $en_date = $this->request->getGet('en_date');

        $query = [];
        if (!empty($supplier_id)) {
            $query = $this->supplierLedgerQuery($supplier_id, $st_date, $en_date);
        }
        $data['result'] = $query;
        $data['supplier_id'] = $supplier_id;
        $data['st_date'] = $st_date;
        $data['en_date'] = $en_date;
        $data['id'] = $shopId;

        print $this->supplierLedgerBox($data['result'], $supplier_id, $st_date, $en_date, $shopId);
    }

    /**
     * @description This method search supplier ledger print
     * @return void
     */
    public function search_supplierLedgPrint()
    {
        $supplier_id = $this->request->getPost('id');
        if (empty($supplier_id)) {
            $supplier_id = $this->request->getGet('supplier_id');
        }
        $st_date = $this->request->getGet('st_date');
        $en_date = $this->request->getGet('en_date');

        if (empty($supplier_id)) {
            print '';
            return;
        }

        $data = $this->supplierLedgerQuery($supplier_id, $st_date, $en_date);

        $view = '<div class="col-xs-12" style="text-transform: capitalize;" >'
            . view('Admin/Ledger_suppliers/print_table', ['result' => $data, 'supplier_id' => $supplier_id, 'st_date' => $st_date, 'en_date' => $en_date])
            . '</div>';

        print $view;
    }

    /**
     * @description This method search supplier ledger date to date
     * @return void
     */
    public function search_dateTodate()
    {
        $shopId = $this->session->shopId;

        $supplier_id = $this->request->getPost('supplierId');
        if (empty($supplier_id)) {
            $supplier_id = $this->request->getPost('supplier_id') ?? $this->request->getGet('supplier_id');
        }
        $st_date = $this->request->getPost('st_date') ?? $this->request->getGet('st_date');
        $en_date = $this->request->getPost('en_date') ?? $this->request->getGet('en_date');

        if (empty($supplier_id)) {
            print '';
            return;
        }

        $query = $this->supplierLedgerQuery($supplier_id, $st_date, $en_date);
        $data['result'] = $query;
        $data['supplier_id'] = $supplier_id;
        $data['st_date'] = $st_date;
        $data['en_date'] = $en_date;
        $data['id'] = $shopId;

        print $this->supplierLedgerBox($data['result'], $supplier_id, $st_date, $en_date, $shopId);
    }



    /**
     * @description This method search supplier ledger print date to date
     * @return void
     */
    public function search_dateTodatePrint()
    {
        $supplier_id = $this->request->getPost('supplierId');
        if (empty($supplier_id)) {
            $supplier_id = $this->request->getPost('supplier_id') ?? $this->request->getGet('supplier_id');
        }
        $st_date = $this->request->getPost('st_date') ?? $this->request->getGet('st_date');
        $en_date = $this->request->getPost('en_date') ?? $this->request->getGet('en_date');

        if (empty($supplier_id)) {
            print '';
            return;
        }

        $data = $this->supplierLedgerQuery($supplier_id, $st_date, $en_date);

        $view = '<div class="col-xs-12" style="text-transform: capitalize;" >'
            . view('Admin/Ledger_suppliers/print_table', ['result' => $data, 'supplier_id' => $supplier_id, 'st_date' => $st_date, 'en_date' => $en_date])
            . '</div>';

        print $view;
    }



    /**
     * @description This method builds the supplier ledger query with running balance
     * @param string|int $supplier_id
     * @param string $st_date
     * @param string $en_date
     * @return array
     */
    private function supplierLedgerQuery($supplier_id, $st_date = '', $en_date = '')
    {
        $db = DB();
        // 1. Define the window function column
        $mBalanceSubquery = "SUM(
            CASE 
                WHEN LOWER(trangaction_type) LIKE 'dr%' THEN amount 
                WHEN LOWER(trangaction_type) LIKE 'cr%' THEN -amount 
                ELSE 0 
            END
        ) OVER (ORDER BY ledg_sup_id) AS r_balance";
        // 2. Build the inner subquery
        $subquery = $db->table('ledger_suppliers')
            ->select('ledger_suppliers.*')
            ->select($mBalanceSubquery, false) // false prevents CI4 from escaping the raw SQL window function
            ->where("supplier_id", $supplier_id);
        // 3. Query from the subquery derived table
        $table = $db->newQuery()
            ->fromSubquery($subquery, 't')
            ->where("supplier_id", $supplier_id);
        if (!empty($st_date) && !empty($en_date)) {
            $table->where('createdDtm >=', $st_date . ' 00:00:00');
            $table->where('createdDtm <=', $en_date . ' 23:59:59');
        }
        $table->orderBy('ledg_sup_id', 'ASC');

        return $table->get()->getResult();
    }

    /**
     * @description This method builds the supplier ledger box (header + table + print container)
     * @param array $result
     * @param string|int $supplier_id
     * @param string $st_date
     * @param string $en_date
     * @param int $shopId
     * @return string
     */
    private function supplierLedgerBox($result, $supplier_id, $st_date = '', $en_date = '', $shopId = null)
    {
        if (empty($supplier_id)) {
            return '';
        }

        $name = get_data_by_id('name', 'suppliers', 'supplier_id', $supplier_id);
        $balance = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplier_id);

        $start_date = !empty($st_date) ? $st_date : 0;
        $end_date = !empty($en_date) ? $en_date : 0;

        $view = ' <div class="box" >
                        <div class="box-header">
                            <h3 class="box-title">Supplier: ' . $name . '</h3>
                            <span class="pull-right"><table class="table table-bordered table-striped" id="TFtable"><tr><td>Total Received:</td><td>' . showWithCurrencySymbol(get_total('ledger_suppliers', 'amount', 'Cr.', 'supplier_id', $supplier_id, $start_date, $end_date)) . '</td></tr><tr><td>Total Paid:</td><td>' . showWithCurrencySymbol(get_total('ledger_suppliers', 'amount', 'Dr.', 'supplier_id', $supplier_id, $start_date, $end_date)) . '</td></tr><tr><td>Due Balance:</td><td>' . showWithCurrencySymbol($balance) . '</td></tr></table></span>
                        </div>
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

        $totalRows = count($result) - 1;
        for ($i = 0; $i <= $totalRows; $i++) {
            $particulars = ($result[$i]->particulars == NULL) ? "Pay due" : $result[$i]->particulars;
            $amountCr = ($result[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($result[$i]->amount);
            $amountDr = ($result[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($result[$i]->amount);

            if (($result[$i]->purchase_id == NULL) && ($result[$i]->trans_id == NULL)) {
                $purchaseId = '---';
            } else {
                $purchaseId = ($result[$i]->purchase_id == NULL) ? '<a href="' . site_url('Admin/Transaction/read/' . $result[$i]->trans_id) . '">TRNS_' . $result[$i]->trans_id . '</a>' : '<a href="' . site_url('Admin/Purchase/view/' . $result[$i]->purchase_id) . '">PURS_' . $result[$i]->purchase_id . '</a>';
            }

            $view .= '<tr>
                                    <td>' . $result[$i]->ledg_sup_id . '</td>
                                    <td>' . $result[$i]->createdDtm . '</td>
                                    <td>' . $particulars . '</td>
                                    <td>' . $purchaseId . '</td>
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
                                    <th>= ' . showWithCurrencySymbol(get_total('ledger_suppliers', 'amount', 'Dr.', 'supplier_id', $supplier_id, $start_date, $end_date)) . '</th>
                                    <th>= ' . showWithCurrencySymbol(get_total('ledger_suppliers', 'amount', 'Cr.', 'supplier_id', $supplier_id, $start_date, $end_date)) . '</th>
                                    <th>' . showWithCurrencySymbol($balance) . '</th>
                                </tr>
                            </tfoot>
                        </table>
                        </div>
                    </div>';

        // printable version of the same (filtered) rows
        $view .= '<div id="ajaxSuppLedgPrint" style="display: none;">'
            . view('Admin/Ledger_suppliers/print_table', ['result' => $result, 'supplier_id' => $supplier_id, 'st_date' => $st_date, 'en_date' => $en_date])
            . '</div>';

        return $view;
    }


}
