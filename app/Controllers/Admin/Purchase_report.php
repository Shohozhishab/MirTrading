<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Purchase_report extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Purchase_report';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
        $this->request = \Config\Services::request();
    }

    /**
     * @description This method provides purchase report view
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

            //all suppliers purchase total amount show (start)
            $suppliersTab = DB()->table('suppliers');
            $suppliers = $suppliersTab->where('sch_id', $shopId)->get()->getResult();
            //all suppliers purchase total amount show (end)

            $supplier_id = $this->request->getGet('supplier_id');
            $prod_id = $this->request->getGet('prod_id');
            $st_date = $this->request->getGet('st_date');
            $en_date = $this->request->getGet('en_date');

            $purchaseItem = $this->purchaseReportQuery($supplier_id, $prod_id, $st_date, $en_date, $shopId);

            $data = array(
                'suppliers' => $suppliers,
                'purchaseItem' => $purchaseItem,
                'supplier_id' => $supplier_id ?? '',
                'prod_id' => $prod_id ?? '',
                'st_date' => $st_date ?? '',
                'en_date' => $en_date ?? '',
                'shopId' => $shopId,
            );

            $data['menu'] = view('Admin/menu_report');
            // All Permissions
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Purchase_report/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method handles date-to-date AJAX search for purchase report
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

        $supplier_id = !empty($this->request->getPost('supplier_id')) ? $this->request->getPost('supplier_id') : $this->request->getGet('supplier_id');
        $prod_id = !empty($this->request->getPost('prod_id')) ? $this->request->getPost('prod_id') : $this->request->getGet('prod_id');
        $st_date = !empty($this->request->getPost('st_date')) ? $this->request->getPost('st_date') : $this->request->getGet('st_date');
        $en_date = !empty($this->request->getPost('en_date')) ? $this->request->getPost('en_date') : $this->request->getGet('en_date');

        $purchaseItem = $this->purchaseReportQuery($supplier_id, $prod_id, $st_date, $en_date, $shopId);

        $perm = $this->permission->module_permission_list($role_id, $this->module_name);
        $data = [];
        foreach ($perm as $key => $val) {
            $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
        }

        $data['purchaseItem'] = $purchaseItem;
        $data['supplier_id'] = $supplier_id ?? '';
        $data['prod_id'] = $prod_id ?? '';
        $data['st_date'] = $st_date ?? '';
        $data['en_date'] = $en_date ?? '';
        $data['shopId'] = $shopId;

        return view('Admin/Purchase_report/table_list', $data);
    }

    /**
     * @description This method handles date-to-date print view for purchase report
     * @return string|RedirectResponse
     */
    public function search_dateTodatePrint()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        }

        $shopId = $this->session->shopId;

        $supplier_id = !empty($this->request->getPost('supplier_id')) ? $this->request->getPost('supplier_id') : $this->request->getGet('supplier_id');
        $prod_id = !empty($this->request->getPost('prod_id')) ? $this->request->getPost('prod_id') : $this->request->getGet('prod_id');
        $st_date = !empty($this->request->getPost('st_date')) ? $this->request->getPost('st_date') : $this->request->getGet('st_date');
        $en_date = !empty($this->request->getPost('en_date')) ? $this->request->getPost('en_date') : $this->request->getGet('en_date');

        $purchaseItem = $this->purchaseReportQuery($supplier_id, $prod_id, $st_date, $en_date, $shopId);

        $view = '<div class="col-xs-12" style="text-transform: capitalize;">'
            . view('Admin/Purchase_report/print_table', [
                'purchaseItem' => $purchaseItem,
                'supplier_id' => $supplier_id,
                'prod_id' => $prod_id,
                'st_date' => $st_date,
                'en_date' => $en_date,
                'shopId' => $shopId,
            ])
            . '</div>';

        return $view;
    }

    /**
     * @description Helper to query purchase report records
     * @param string|int $supplier_id
     * @param string|int $prod_id
     * @param string $st_date
     * @param string $en_date
     * @param int|null $shopId
     * @return array
     */
    public function purchaseReportQuery($supplier_id = '', $prod_id = '', $st_date = '', $en_date = '', $shopId = null)
    {
        $shopId = $shopId ?? $this->session->shopId;

        $builder = DB()->table('purchase_item')
            ->select('purchase_item.*, purchase.supplier_id, purchase.date as purchase_date, purchase.sch_id')
            ->join('purchase', 'purchase.purchase_id = purchase_item.purchase_id')
            ->where('purchase.sch_id', $shopId);

        if (!empty($supplier_id)) {
            $builder->where('purchase.supplier_id', $supplier_id);
        }
        if (!empty($prod_id)) {
            $builder->where('purchase_item.prod_id', $prod_id);
        }
        if (!empty($st_date) && !empty($en_date)) {
            $builder->groupStart()
                ->where('purchase.createdDtm >=', $st_date . ' 00:00:00')
                ->where('purchase.createdDtm <=', $en_date . ' 23:59:59')
                ->orGroupStart()
                    ->where('purchase.date >=', $st_date . ' 00:00:00')
                    ->where('purchase.date <=', $en_date . ' 23:59:59')
                ->groupEnd()
            ->groupEnd();
        } elseif (!empty($st_date)) {
            $builder->groupStart()
                ->where('purchase.createdDtm >=', $st_date . ' 00:00:00')
                ->orWhere('purchase.date >=', $st_date . ' 00:00:00')
            ->groupEnd();
        } elseif (!empty($en_date)) {
            $builder->groupStart()
                ->where('purchase.createdDtm <=', $en_date . ' 23:59:59')
                ->orWhere('purchase.date <=', $en_date . ' 23:59:59')
            ->groupEnd();
        }

        return $builder->orderBy('purchase_item.purchase_item_id', 'DESC')->get()->getResult();
    }

    /**
     * @description Legacy search method preserved for backward compatibility
     * @return void
     */
    public function search()
    {
        $shopId = $this->session->shopId;

        $st_date = $this->request->getPost('st_date');
        $en_date = $this->request->getPost('en_date');

        $i = 0;
        $purchaseTab = DB()->table('purchase');
        $purchaseId = $purchaseTab->where('sch_id', $shopId)->get()->getResult();
        $purchaseItem = [];
        foreach ($purchaseId as $value) {
            $purchase_itemTab = DB()->table('purchase_item');
            $query = $purchase_itemTab->where('purchase_id', $value->purchase_id)->where('createdDtm >=', $st_date . ' 00:00:00')->where('createdDtm <=', $en_date . ' 23:59:59')->get()->getResult();
            $purchaseItem[$i] = $query;
            $i++;
        }
        $data['purchase'] = $purchaseItem;
        $data['st_date'] = $st_date;
        $data['en_date'] = $en_date;

        $data['menu'] = view('Admin/menu_report');
        echo view('Admin/header');
        echo view('Admin/sidebar');
        echo view('Admin/Purchase_report/search', $data);
        echo view('Admin/footer');
    }
}
