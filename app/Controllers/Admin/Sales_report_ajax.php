<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Sales_report_ajax extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Sales_report';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
        $this->request = \Config\Services::request();
    }

    /**
     * @description This method provides sales report view
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

            //all customer total balance show (start)
            $customersTb = DB()->table('customers');
            $customers = $customersTb->where('sch_id', $shopId)->get()->getResult();
            //all customer total balance show (end)

            //All invoice item show list (start)
            $prod_id = $this->request->getGet('prod_id');
            $customer_id = $this->request->getGet('customer_id');
            $st_date = $this->request->getGet('st_date');
            $en_date = $this->request->getGet('en_date');

            $sale = $this->salesReportQuery($customer_id, $prod_id, $st_date, $en_date, $shopId);
            //All invoice item show list (end)

            $profit = 0;
            foreach ($sale as $item){
                $profit += (float)$item->profit;
            }

            $data = array(
                'customers' => $customers,
                'sale' => $sale,
                'sale2' => $sale,
                'saleprofit' => $profit,
            );
            $data['st_date'] = isset($st_date) ? $st_date : '';
            $data['en_date'] = isset($en_date) ? $en_date : '';
            $data['customer_id'] = isset($customer_id) ? $customer_id : '';
            $data['prod_id'] = isset($prod_id) ? $prod_id : '';
            $data['shopId'] = $shopId;

            $data['menu'] = view('Admin/menu_report');
            // All Permissions
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            $data['profit'] = $this->permission->have_access($role_id, 'Sales', 'profit') ?? permission_check('Sales', $role_id, 'profit');

            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Sales_report/list', $data);
            } else {
                echo view('no_permission');
            }
        }
    }

    /**
     * @description This method handles date-to-date AJAX search for sales report
     * @return string|RedirectResponse
     */
    public function search_dateTodate()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        }

        $shopId = $this->session->shopId;

        $customer_id = !empty($this->request->getPost('customer_id')) ? $this->request->getPost('customer_id') : $this->request->getGet('customer_id');
        $prod_id = !empty($this->request->getPost('prod_id')) ? $this->request->getPost('prod_id') : $this->request->getGet('prod_id');
        $st_date = !empty($this->request->getPost('st_date')) ? $this->request->getPost('st_date') : $this->request->getGet('st_date');
        $en_date = !empty($this->request->getPost('en_date')) ? $this->request->getPost('en_date') : $this->request->getGet('en_date');

        $sale = $this->salesReportQuery($customer_id, $prod_id, $st_date, $en_date, $shopId);

        $saleprofit = 0;
        foreach ($sale as $item) {
            $saleprofit += (float)$item->profit;
        }

        $perm = $this->permission->module_permission_list($role_id, $this->module_name);
        $data = [];
        foreach ($perm as $key => $val) {
            $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
        }

        $data['sale'] = $sale;
        $data['saleprofit'] = $saleprofit;
        $data['st_date'] = $st_date ?? '';
        $data['en_date'] = $en_date ?? '';
        $data['customer_id'] = $customer_id ?? '';
        $data['prod_id'] = $prod_id ?? '';
        $data['shopId'] = $shopId;
        $data['profit'] = $this->permission->have_access($role_id, 'Sales', 'profit') ?? permission_check('Sales', $role_id, 'profit');

        return view('Admin/Sales_report/table_list', $data);
    }

    /**
     * @description This method handles date-to-date print view for sales report
     * @return string|RedirectResponse
     */
    public function search_dateTodatePrint()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        }

        $shopId = $this->session->shopId;
        $role_id = $this->session->role;

        $customer_id = !empty($this->request->getPost('customer_id')) ? $this->request->getPost('customer_id') : $this->request->getGet('customer_id');
        $prod_id = !empty($this->request->getPost('prod_id')) ? $this->request->getPost('prod_id') : $this->request->getGet('prod_id');
        $st_date = !empty($this->request->getPost('st_date')) ? $this->request->getPost('st_date') : $this->request->getGet('st_date');
        $en_date = !empty($this->request->getPost('en_date')) ? $this->request->getPost('en_date') : $this->request->getGet('en_date');

        $sale = $this->salesReportQuery($customer_id, $prod_id, $st_date, $en_date, $shopId);
        $profit = $this->permission->have_access($role_id, 'Sales', 'profit') ?? permission_check('Sales', $role_id, 'profit');

        $view = '<div class="col-xs-12" style="text-transform: capitalize;">'
            . view('Admin/Sales_report/print_table', [
                'sale' => $sale,
                'customer_id' => $customer_id,
                'prod_id' => $prod_id,
                'st_date' => $st_date,
                'en_date' => $en_date,
                'profit' => $profit,
                'shopId' => $shopId,
            ])
            . '</div>';

        return $view;
    }

    /**
     * @description Helper to query sales report records
     * @param string|int $customer_id
     * @param string|int $prod_id
     * @param string $st_date
     * @param string $en_date
     * @param int|null $shopId
     * @return array
     */
    public function salesReportQuery($customer_id = '', $prod_id = '', $st_date = '', $en_date = '', $shopId = null)
    {
        $shopId = $shopId ?? $this->session->shopId;

        $saleTable = DB()->table('invoice_item')
            ->join('invoice', 'invoice.invoice_id = invoice_item.invoice_id')
            ->where('invoice.sch_id', $shopId);

        if (!empty($customer_id)) {
            $saleTable->where('invoice.customer_id', $customer_id);
        }
        if (!empty($prod_id)) {
            $saleTable->where('invoice_item.prod_id', $prod_id);
        }
        if (!empty($st_date) && !empty($en_date)) {
            $saleTable->where('invoice.createdDtm >=', $st_date . ' 00:00:00');
            $saleTable->where('invoice.createdDtm <=', $en_date . ' 23:59:59');
        } elseif (!empty($st_date)) {
            $saleTable->where('invoice.createdDtm >=', $st_date . ' 00:00:00');
        } elseif (!empty($en_date)) {
            $saleTable->where('invoice.createdDtm <=', $en_date . ' 23:59:59');
        }

        return $saleTable->orderBy('invoice_item.inv_item', 'DESC')->get()->getResult();
    }
}
