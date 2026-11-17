<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Ledger_employee_ajax extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Ledger_employee';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
        $this->request = \Config\Services::request();
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

            $employee_id = $this->request->getGet('employee_id');
            $st_date = $this->request->getGet('st_date');
            $en_date = $this->request->getGet('en_date');
            $query = [];
            if (!empty($employee_id)) {
                $query = $this->employeeLedgerQuery($employee_id, $st_date, $en_date, $shopId);
            }
            $data['result'] = $query;

            $data['st_date'] = isset($st_date) ? $st_date : '';
            $data['en_date'] = isset($en_date) ? $en_date : '';
            $data['employee_id'] = $employee_id;
            $data['id'] = $shopId;
            $data['shopId'] = $shopId;

            // All Permissions
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Ledger_employee/list', $data);
            } else {
                echo view('no_permission');
            }
        }
    }

    /**
     * @description This method search employee ledger date to date
     * @return void
     */
    public function search_dateTodate()
    {
        $shopId = $this->session->shopId;

        $employee_id = $this->request->getPost('employeeId');
        if (empty($employee_id)) {
            $employee_id = $this->request->getPost('employee_id') ?? $this->request->getGet('employee_id');
        }
        $st_date = $this->request->getPost('st_date') ?? $this->request->getGet('st_date');
        $en_date = $this->request->getPost('en_date') ?? $this->request->getGet('en_date');

        if (empty($employee_id)) {
            print '';
            return;
        }

        $query = $this->employeeLedgerQuery($employee_id, $st_date, $en_date, $shopId);

        print $this->employeeLedgerBox($query, $employee_id, $st_date, $en_date, $shopId);
    }

    /**
     * @description This method search employee ledger print date to date
     * @return void
     */
    public function search_dateTodatePrint()
    {
        $shopId = $this->session->shopId;

        $employee_id = $this->request->getPost('employeeId');
        if (empty($employee_id)) {
            $employee_id = $this->request->getPost('employee_id') ?? $this->request->getGet('employee_id');
        }
        $st_date = $this->request->getPost('st_date') ?? $this->request->getGet('st_date');
        $en_date = $this->request->getPost('en_date') ?? $this->request->getGet('en_date');

        if (empty($employee_id)) {
            print '';
            return;
        }

        $data = $this->employeeLedgerQuery($employee_id, $st_date, $en_date, $shopId);

        $view = '<div class="col-xs-12" style="text-transform: capitalize;">'
            . view('Admin/Ledger_employee/print_table', [
                'result' => $data,
                'employee_id' => $employee_id,
                'st_date' => $st_date,
                'en_date' => $en_date,
                'shopId' => $shopId,
            ])
            . '</div>';

        print $view;
    }

    /**
     * @description Legacy search method preserved for backward compatibility
     * @return void
     */
    public function search_ledger_employee()
    {
        $shopId = $this->session->shopId;
        $employeeId = $this->request->getPost('id');
        if (empty($employeeId)) {
            $employeeId = $this->request->getPost('employeeId') ?? $this->request->getGet('employee_id');
        }
        $st_date = $this->request->getPost('st_date') ?? $this->request->getGet('st_date');
        $en_date = $this->request->getPost('en_date') ?? $this->request->getGet('en_date');

        if (empty($employeeId)) {
            print '';
            return;
        }

        $query = $this->employeeLedgerQuery($employeeId, $st_date, $en_date, $shopId);

        print $this->employeeLedgerBox($query, $employeeId, $st_date, $en_date, $shopId);
    }

    /**
     * @description Legacy search print method
     * @return void
     */
    public function search_ledger_employeePrint()
    {
        $shopId = $this->session->shopId;
        $employeeId = $this->request->getPost('id');
        if (empty($employeeId)) {
            $employeeId = $this->request->getPost('employeeId') ?? $this->request->getGet('employee_id');
        }
        $st_date = $this->request->getPost('st_date') ?? $this->request->getGet('st_date');
        $en_date = $this->request->getPost('en_date') ?? $this->request->getGet('en_date');

        if (empty($employeeId)) {
            print '';
            return;
        }

        $query = $this->employeeLedgerQuery($employeeId, $st_date, $en_date, $shopId);

        $view = '<div class="col-xs-12" style="text-transform: capitalize;">'
            . view('Admin/Ledger_employee/print_table', [
                'result' => $query,
                'employee_id' => $employeeId,
                'st_date' => $st_date,
                'en_date' => $en_date,
                'shopId' => $shopId,
            ])
            . '</div>';

        print $view;
    }

    /**
     * @description Helper to query employee ledger with running balance
     * @param string|int $employee_id
     * @param string $st_date
     * @param string $en_date
     * @param int|null $shopId
     * @return array
     */
    public function employeeLedgerQuery($employee_id, $st_date = '', $en_date = '', $shopId = null)
    {
        if (empty($employee_id)) {
            return [];
        }

        $shopId = $shopId ?? $this->session->shopId;
        $db = DB();

        // 1. Define the window function column
        $mBalanceSubquery = "SUM(
            CASE 
                WHEN LOWER(trangaction_type) LIKE 'dr%' THEN amount 
                WHEN LOWER(trangaction_type) LIKE 'cr%' THEN -amount 
                ELSE 0 
            END
        ) OVER (ORDER BY ledg_emp_id) AS r_balance";

        // 2. Build the inner subquery
        $subquery = $db->table('ledger_employee')
            ->select('ledger_employee.*')
            ->select($mBalanceSubquery, false)
            ->where("employee_id", $employee_id);
        if (!empty($shopId)) {
            $subquery->where("sch_id", $shopId);
        }

        // 3. Query from the subquery derived table
        $table = $db->newQuery()
            ->fromSubquery($subquery, 't')
            ->where("employee_id", $employee_id);
        if (!empty($shopId)) {
            $table->where("sch_id", $shopId);
        }
        if (!empty($st_date) && !empty($en_date)) {
            $table->where('createdDtm >=', $st_date . ' 00:00:00');
            $table->where('createdDtm <=', $en_date . ' 23:59:59');
        } elseif (!empty($st_date)) {
            $table->where('createdDtm >=', $st_date . ' 00:00:00');
        } elseif (!empty($en_date)) {
            $table->where('createdDtm <=', $en_date . ' 23:59:59');
        }

        $table->orderBy('ledg_emp_id', 'ASC');
        return $table->get()->getResult();
    }

    /**
     * @description Builds the interactive box with header, table, and print container
     * @param array $result
     * @param string|int $employee_id
     * @param string $st_date
     * @param string $en_date
     * @param int|null $shopId
     * @return string
     */
    public function employeeLedgerBox($result, $employee_id, $st_date = '', $en_date = '', $shopId = null)
    {
        if (empty($employee_id)) {
            return '';
        }

        $shopId = $shopId ?? $this->session->shopId;
        $name = get_data_by_id('name', 'employee', 'employee_id', $employee_id);
        $balance = get_data_by_id('balance', 'employee', 'employee_id', $employee_id);

        $start_date = !empty($st_date) ? $st_date : 0;
        $end_date = !empty($en_date) ? $en_date : 0;

        $view = '<div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Employee: ' . $name . '</h3>
                        <span class="pull-right">
                            <table class="table table-bordered table-striped" id="TFtable">
                                <tr>
                                    <td>Total Paid:</td>
                                    <td>' . showWithCurrencySymbol(get_total('ledger_employee', 'amount', 'Dr.', 'employee_id', $employee_id, $start_date, $end_date)) . '</td>
                                </tr>
                                <tr>
                                    <td>Total Received:</td>
                                    <td>' . showWithCurrencySymbol(get_total('ledger_employee', 'amount', 'Cr.', 'employee_id', $employee_id, $start_date, $end_date)) . '</td>
                                </tr>
                                <tr>
                                    <td>Balance:</td>
                                    <td>' . showWithCurrencySymbol($balance) . '</td>
                                </tr>
                            </table>
                        </span>
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

        $totalRows = count($result);
        for ($i = 0; $i < $totalRows; $i++) {
            $particulars = ($result[$i]->particulars == NULL) ? "Transaction" : $result[$i]->particulars;
            $amountCr = ($result[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($result[$i]->amount);
            $amountDr = ($result[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($result[$i]->amount);
            $transMemo = !empty($result[$i]->trans_id) ? '<a href="' . site_url('Admin/Transaction/read/' . $result[$i]->trans_id) . '">TRNS_' . $result[$i]->trans_id . '</a>' : '---';

            $view .= '<tr>
                        <td>' . $result[$i]->ledg_emp_id . '</td>
                        <td>' . $result[$i]->createdDtm . '</td>
                        <td>' . $particulars . '</td>
                        <td>' . $transMemo . '</td>
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
                                    <th>= ' . showWithCurrencySymbol(get_total('ledger_employee', 'amount', 'Dr.', 'employee_id', $employee_id, $start_date, $end_date)) . '</th>
                                    <th>= ' . showWithCurrencySymbol(get_total('ledger_employee', 'amount', 'Cr.', 'employee_id', $employee_id, $start_date, $end_date)) . '</th>
                                    <th>' . showWithCurrencySymbol($balance) . '</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>';

        // printable version of the same (filtered) rows
        $view .= '<div id="ajaxEmpLedgPrint" style="display: none;">'
            . view('Admin/Ledger_employee/print_table', [
                'result' => $result,
                'employee_id' => $employee_id,
                'st_date' => $st_date,
                'en_date' => $en_date,
                'shopId' => $shopId,
            ])
            . '</div>';

        return $view;
    }
}
