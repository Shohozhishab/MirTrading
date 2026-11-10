<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Return_sale extends BaseController
{

    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Return_sale';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides return sale view
     * @return RedirectResponse|void
     */
    public function index()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $customer_id = $this->request->getGet('customer');

            $shopId = $this->session->shopId;

            $st_date = $this->request->getGet('st_date');
            $en_date = $this->request->getGet('en_date');

            $table = DB()->table('return_sale');
            $table->where('return_sale.sch_id', $shopId);
            $table->where('return_sale.deleted', null);
            if (!empty($customer_id)) {
                $table->join('invoice', 'invoice.invoice_id = return_sale.invoice_id');
                $table->where('invoice.customer_id', $customer_id);
            }
            if (!empty($st_date) && !empty($en_date)) {
                // Assuming your database column name is 'date'
                $table->where('return_sale.createdDtm >=', $st_date . ' 00:00:00');
                $table->where('return_sale.createdDtm <=', $en_date . ' 23:59:59');
            }
            $data['return_sale_data'] = $table->get()->getResult();

            $data['customerId'] = $customer_id ?? '';
            $data['st_date'] = isset($st_date)?$st_date:'';
            $data['en_date'] = isset($en_date)?$en_date:'';

            $data['menu'] = view('Admin/menu_sales', $data);
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Return_sale/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method invoice search
     * @return void
     */
    public function invoice_search()
    {

        $shopId = $this->session->shopId;
        $invoiceId = $this->request->getPost('invoiceId');

        $invoiceTable = DB()->table('invoice');
        $data['invoice_data'] = $invoiceTable->where('sch_id', $shopId)->where('invoice_id', $invoiceId)->get()->getResult();

        $data['return_status'] = get_return_status_by_invoice_id($invoiceId);

        $data['menu'] = view('Admin/menu_sales', $data);
        echo view('Admin/header');
        echo view('Admin/sidebar');
        echo view('Admin/Return_sale/search', $data);
        echo view('Admin/footer');
    }

    /**
     * @description This method provides return view
     * @param int $id
     * @return RedirectResponse|void
     */
    public function return($id)
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;

            $invoice_itemTab = DB()->table('invoice_item');
            $data['invoice_item'] = $invoice_itemTab->where('invoice_id', $id)->where('sch_id', $shopId)->get()->getResult();

            $invoiceTab = DB()->table('invoice');
            $data['invoice'] = $invoiceTab->where('invoice_id', $id)->where('sch_id', $shopId)->get()->getResult();


            $data['action'] = site_url('Admin/Return_sale/create_action');
            $data['invoiceId'] = $id;

            $data['shopCash'] = (float) get_data_by_id('cash', 'shops', 'sch_id', $shopId);
            $banks = DB()->table('bank')
                ->where('sch_id', $shopId)
                ->where('deleted IS NULL')
                ->get()
                ->getResult();
            $bankBalances = [];
            foreach ($banks as $bank) {
                $bankBalances[$bank->bank_id] = (float) $bank->balance;
            }
            $data['bankBalances'] = $bankBalances;
            $data['bankId'] = '';

            $data['menu'] = view('Admin/menu_sales', $data);
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if ($data['create'] == 1) {
                echo view('Admin/Return_sale/return', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store return sale
     * @return RedirectResponse
     */
    public function create_action()
    {
        $userId   = $this->session->userId;
        $shopId   = $this->session->shopId;

        $customerId   = $this->request->getPost('customer_id');
        $customerName = $this->request->getPost('customer_name');
        $invoiceId    = $this->request->getPost('invoice_id');

        $productIds              = $this->request->getPost('returnchecked');
        $productStockRelationIds = $this->request->getPost('product_stock_relation_id');
        $quantities              = $this->request->getPost('quantity');
        $purchasePrices          = $this->request->getPost('purchase_price');

        $amount     = (float) $this->request->getPost('totalPrice');
        $cashAmount = (float) ($this->request->getPost('nagod') ?? $this->request->getPost('cash') ?? 0);
        $nagod      = $cashAmount;
        $bankAmount = (float) ($this->request->getPost('bankAmount') ?? $this->request->getPost('bank') ?? 0);
        $bankId     = $this->request->getPost('bank_id');
        $dueAmount  = (float) $this->request->getPost('due');
        $totalVat   = empty($this->request->getPost('vatAmount')) ? 0 : (float) $this->request->getPost('vatAmount');
        $totalDiscount = empty($this->request->getPost('discountAmount')) ? 0 : (float) $this->request->getPost('discountAmount');

        // Customer validation
        if (empty($customerName) && empty($customerId)) {
            return redirect()->to(site_url('Admin/Return_sale/return/' . $invoiceId));
        }

        if (empty($productIds) || !is_array($productIds) || $amount <= 0) {
            $this->session->setFlashdata('message', '
            <div class="alert alert-danger alert-dismissible" role="alert">
                Please select any product
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>');
            return redirect()->to(site_url('Admin/Return_sale/return/' . $invoiceId));
        }

        // Clamp negatives to 0
        $cashAmount = max(0, $cashAmount);
        $nagod      = $cashAmount;
        $bankAmount = max(0, $bankAmount);

        if ($bankAmount <= 0) {
            $bankId = null;
        }

        // Server-side Bank selection check
        if ($bankAmount > 0 && empty($bankId)) {
            $this->session->setFlashdata('message', '
            <div class="alert alert-danger alert-dismissible" role="alert">
                Please select a bank
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>');
            return redirect()->to(site_url('Admin/Return_sale/return/' . $invoiceId));
        }

        // Validate Shop Cash balance
        $shopsCash = (float) get_data_by_id('cash', 'shops', 'sch_id', $shopId);
        if ($nagod > $shopsCash) {
            $this->session->setFlashdata('message', '
            <div class="alert alert-danger alert-dismissible" role="alert">
                Not enough cash balance!
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>');
            return redirect()->to(site_url('Admin/Return_sale/return/' . $invoiceId));
        }

        // Validate Bank balance
        if ($bankAmount > 0 && !empty($bankId)) {
            $bankRow = DB()->table('bank')
                ->where('bank_id', $bankId)
                ->where('sch_id', $shopId)
                ->where('deleted IS NULL')
                ->get()
                ->getRow();
            $bankCash = !empty($bankRow) ? (float) $bankRow->balance : 0;
            if (empty($bankRow) || $bankAmount > $bankCash) {
                $this->session->setFlashdata('message', '
                <div class="alert alert-danger alert-dismissible" role="alert">
                    Not enough bank balance!
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>');
                return redirect()->to(site_url('Admin/Return_sale/return/' . $invoiceId));
            }
        }

        // Paid amount cannot exceed Return Amount
        if (round($cashAmount + $bankAmount, 6) > round($amount, 6)) {
            $this->session->setFlashdata('message', '
            <div class="alert alert-danger alert-dismissible" role="alert">
                Total paid amount cannot exceed Final Return Amount!
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>');
            return redirect()->to(site_url('Admin/Return_sale/return/' . $invoiceId));
        }

        $calculatedDue = round($amount - ($cashAmount + $bankAmount), 6);

        if ($dueAmount < 0 || $calculatedDue < -0.0001) {
            $this->session->setFlashdata('message', '
            <div class="alert alert-danger alert-dismissible" role="alert">
                Please enter a valid due amount
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>');
            return redirect()->to(site_url('Admin/Return_sale/return/' . $invoiceId));
        }

        // New / walk-in customer must clear full amount (no due)
        if (!empty($customerName) && empty($customerId) && abs($calculatedDue) > 0.0001) {
            $this->session->setFlashdata('message', '
            <div class="alert alert-danger alert-dismissible" role="alert">
                Please clear due
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>');
            return redirect()->to(site_url('Admin/Return_sale/return/' . $invoiceId));
        }

        DB()->transStart();

        // 1. Insert into return_sale
        $returnData = [
            'sch_id'       => $shopId,
            'invoice_id'   => $invoiceId,
            'amount'       => $amount,
            'rtn_profit'   => 0,
            'nagad_paid'   => $nagod,
            'bank_paid'    => $bankAmount,
            'bank_id'      => $bankId,
            'createdBy'    => $userId,
            'createdDtm'   => date('Y-m-d H:i:s'),
        ];

        if (!empty($customerId)) {
            $returnData['customer_id'] = $customerId;
        } else {
            $returnData['customer_name'] = $customerName;
        }

        DB()->table('return_sale')->insert($returnData);
        $returnId = DB()->insertID();

        // 2. Update shop sale balance + ledger
        $saleBalance     = (float) get_data_by_id('sale_balance', 'shops', 'sch_id', $shopId);
        $newSaleBalance  = $saleBalance + $amount - $totalVat;

        DB()->table('shops')
            ->where('sch_id', $shopId)
            ->update(['sale_balance' => $newSaleBalance]);

        DB()->table('ledger_sales')->insert([
            'sch_id'           => $shopId,
            'rtn_sale_id'      => $returnId,
            'trangaction_type' => 'Dr.',
            'particulars'      => 'return sale',
            'amount'           => $amount,
            'rest_balance'     => $newSaleBalance,
            'createdBy'        => $userId,
            'createdDtm'       => date('Y-m-d H:i:s'),
        ]);

        // 3. Process each returned item
        $totalPurchasePrice = 0;

        foreach ($productIds as $i => $prodId) {
            $qty   = (float) ($quantities[$i] ?? 0);
            $price = (float) ($purchasePrices[$i] ?? 0);
            $relId = $productStockRelationIds[$i] ?? null;
            $total = $qty * $price;

            if ($qty <= 0) {
                continue;
            }

            // Insert return item
            DB()->table('return_sale_item')->insert([
                'sch_id'      => $shopId,
                'rtn_sale_id' => $returnId,
                'prod_id'     => $prodId,
                'price'       => $price,
                'quantity'    => $qty,
                'total_price' => $total,
                'createdBy'   => $userId,
                'createdDtm'  => date('Y-m-d H:i:s'),
            ]);

            // Update product stock quantity
            $stockQuery = DB()->table('product_stock_relation')
                ->where('product_id', $prodId);
            if (!empty($relId)) {
                $stockQuery->where('product_stock_relation_id', $relId);
            }
            $stock = $stockQuery->get()->getRow();

            if ($stock) {
                $newQty = $stock->quantity + $qty;

                DB()->table('product_stock_relation')
                    ->where('product_stock_relation_id', $stock->product_stock_relation_id)
                    ->where('product_id', $prodId)
                    ->update(['quantity' => $newQty]);

                // Calculate total purchase cost
                $totalPurchasePrice += ((float) $stock->purchase_price * $qty);
            }
        }

        // 4. VAT handling
        if (!empty($totalVat)) {
            $vatId      = get_data_by_id('vat_id', 'vat_register', 'sch_id', $shopId);
            $vatBalance = (float) get_data_by_id('balance', 'vat_register', 'sch_id', $shopId);
            $newVatBal  = $vatBalance + $totalVat;

            DB()->table('vat_register')
                ->where('sch_id', $shopId)
                ->update(['balance' => $newVatBal]);

            DB()->table('ledger_vat')->insert([
                'vat_id'           => $vatId,
                'invoice_id'       => $invoiceId,
                'sch_id'           => $shopId,
                'particulars'      => 'Return Sale Vat return',
                'trangaction_type' => 'Dr.',
                'amount'           => $totalVat,
                'rest_balance'     => $newVatBal,
                'createdBy'        => $shopId,
            ]);
        }

        if (!empty($totalDiscount)) {
            $discountBalance = (float) get_data_by_id('discount', 'shops', 'sch_id', $shopId);
            $newDiscountBal  = $discountBalance + $totalDiscount;

            DB()->table('shops')
                ->where('sch_id', $shopId)
                ->update(['discount' => $newDiscountBal]);

            DB()->table('ledger_discount')->insert([
                'invoice_id'       => $invoiceId,
                'sch_id'           => $shopId,
                'particulars'      => 'Return Sale Discount return',
                'trangaction_type' => 'Cr.',
                'amount'           => $totalDiscount,
                'rest_balance'     => $newDiscountBal,
                'createdBy'        => $userId,
            ]);
        }

        // 5. Customer Product Return (Registered Customer only)
        $runningCusBalance = 0;
        if (!empty($customerId)) {
            $oldCusBalance = (float) get_data_by_id('balance', 'customers', 'customer_id', $customerId);
            $runningCusBalance = $oldCusBalance - $amount;

            DB()->table('ledger')->insert([
                'sch_id'           => $shopId,
                'customer_id'      => $customerId,
                'rtn_sale_id'      => $returnId,
                'invoice_id'       => $invoiceId,
                'particulars'      => 'Return Sale Product',
                'trangaction_type' => 'Cr.',
                'amount'           => $amount,
                'rest_balance'     => $runningCusBalance,
                'createdBy'        => $userId,
                'createdDtm'       => date('Y-m-d H:i:s'),
            ]);
        }

        // 6. Cash Refund Processing
        if ($nagod > 0) {
            $currentShopCash = (float) get_data_by_id('cash', 'shops', 'sch_id', $shopId);
            $newShopCash     = $currentShopCash - $nagod;

            DB()->table('shops')
                ->where('sch_id', $shopId)
                ->update([
                    'cash'      => $newShopCash,
                    'updatedBy' => $userId,
                ]);

            DB()->table('ledger_nagodan')->insert([
                'sch_id'           => $shopId,
                'rtn_sale_id'      => $returnId,
                'invoice_id'       => $invoiceId,
                'customer_id'      => !empty($customerId) ? $customerId : null,
                'particulars'      => 'Return Sale Cash Pay',
                'trangaction_type' => 'Cr.',
                'amount'           => $nagod,
                'rest_balance'     => $newShopCash,
                'createdBy'        => $userId,
                'createdDtm'       => date('Y-m-d H:i:s'),
            ]);

            if (!empty($customerId)) {
                $runningCusBalance += $nagod;
                DB()->table('ledger')->insert([
                    'sch_id'           => $shopId,
                    'customer_id'      => $customerId,
                    'rtn_sale_id'      => $returnId,
                    'invoice_id'       => $invoiceId,
                    'particulars'      => 'Return Sale Product cash Pay',
                    'trangaction_type' => 'Dr.',
                    'amount'           => $nagod,
                    'rest_balance'     => $runningCusBalance,
                    'createdBy'        => $userId,
                    'createdDtm'       => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // 7. Bank Refund Processing
        if ($bankAmount > 0 && !empty($bankId)) {
            $currentBankBal = (float) get_data_by_id('balance', 'bank', 'bank_id', $bankId);
            $newBankBal     = $currentBankBal - $bankAmount;

            DB()->table('bank')
                ->where('bank_id', $bankId)
                ->update([
                    'balance'    => $newBankBal,
                    'updatedBy'  => $userId,
                    'updatedDtm' => date('Y-m-d H:i:s'),
                ]);

            DB()->table('ledger_bank')->insert([
                'sch_id'           => $shopId,
                'bank_id'          => $bankId,
                'rtn_sale_id'      => $returnId,
                'invoice_id'       => $invoiceId,
                'particulars'      => 'Return Sale Bank Pay',
                'trangaction_type' => 'Cr.',
                'amount'           => $bankAmount,
                'rest_balance'     => $newBankBal,
                'createdBy'        => $userId,
                'createdDtm'       => date('Y-m-d H:i:s'),
            ]);

            if (!empty($customerId)) {
                $runningCusBalance += $bankAmount;
                DB()->table('ledger')->insert([
                    'sch_id'           => $shopId,
                    'customer_id'      => $customerId,
                    'rtn_sale_id'      => $returnId,
                    'invoice_id'       => $invoiceId,
                    'particulars'      => 'Return Sale Product Bank Pay',
                    'trangaction_type' => 'Dr.',
                    'amount'           => $bankAmount,
                    'rest_balance'     => $runningCusBalance,
                    'createdBy'        => $userId,
                    'createdDtm'       => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // Finalize Customer Balance & Affiliate Commission (Registered Customer only)
        if (!empty($customerId)) {
            DB()->table('customers')
                ->where('customer_id', $customerId)
                ->update([
                    'balance'    => $runningCusBalance,
                    'createdBy'  => $userId,
                    'updatedBy'  => $userId,
                    'updatedDtm' => date('Y-m-d H:i:s'),
                ]);

            // Affiliate commission reverse
            $affiliateUserId = get_data_by_id('affiliate_user_id', 'customers', 'customer_id', $customerId);
            if (!empty($affiliateUserId)) {
                $salesId           = get_data_by_id('sales_id', 'sales', 'invoice_id', $invoiceId);
                $commissionPercent = (float) get_data_by_id('commission', 'commission', 'sales_id', $salesId);
                $oldAffBalance     = (float) get_data_by_id('balance', 'affiliate_user', 'affiliate_user_id', $affiliateUserId);

                $commission    = ($amount * $commissionPercent) / 100;
                $newAffBalance = $oldAffBalance - $commission;

                DB()->table('affiliate_user')
                    ->where('affiliate_user_id', $affiliateUserId)
                    ->update(['balance' => $newAffBalance]);
            }
        }

        // 8. Calculate & update return profit
        $returnProfit = $amount - $totalPurchasePrice - $totalVat;

        DB()->table('return_sale')
            ->where('rtn_sale_id', $returnId)
            ->update(['rtn_profit' => $returnProfit]);

        $shopProfit    = (float) get_data_by_id('profit', 'shops', 'sch_id', $shopId);
        $newShopProfit = $shopProfit + $returnProfit;

        DB()->table('shops')
            ->where('sch_id', $shopId)
            ->update([
                'profit'    => $newShopProfit,
                'updatedBy' => $userId,
            ]);

        DB()->table('ledger_profit')->insert([
            'sch_id'           => $shopId,
            'rtn_sale_id'      => $returnId,
            'trangaction_type' => 'Dr.',
            'particulars'      => 'Return Profit',
            'amount'           => $returnProfit,
            'rest_balance'     => $newShopProfit,
            'createdBy'        => $userId,
            'createdDtm'       => date('Y-m-d H:i:s'),
        ]);

        // 9. Update stock amount + ledger
        $stockAmount    = (float) get_data_by_id('stockAmount', 'shops', 'sch_id', $shopId);
        $newStockAmount = $stockAmount + $totalPurchasePrice;

        DB()->table('shops')
            ->where('sch_id', $shopId)
            ->update(['stockAmount' => $newStockAmount]);

        DB()->table('ledger_stock')->insert([
            'sch_id'           => $shopId,
            'rtn_sale_id'      => $returnId,
            'trangaction_type' => 'Dr.',
            'particulars'      => 'Return sale',
            'amount'           => $totalPurchasePrice,
            'rest_balance'     => $newStockAmount,
            'createdBy'        => $userId,
            'createdDtm'       => date('Y-m-d H:i:s'),
        ]);

        DB()->transComplete();

        $this->session->setFlashdata('message', '
        <div class="alert alert-success alert-dismissible" role="alert">
            Return Product Success
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>');

        return redirect()->to(site_url('Admin/Return_sale/'));
    }

    /**
     * @description This method checks shop cash balance via AJAX
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function check_shop_balance()
    {
        $shopId = $this->session->shopId;
        $cash   = (float) ($this->request->getPost('cash') ?? $this->request->getPost('amount') ?? 0);

        $shopsTable = DB()->table('shops');
        $query      = $shopsTable->where('sch_id', $shopId)->get()->getRow();

        $balance = !empty($query) ? (float) $query->cash : 0;
        return $this->response->setBody($cash > $balance ? '0' : '1');
    }

    /**
     * @description This method checks bank balance via AJAX
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function check_bank_balance()
    {
        $shopId = $this->session->shopId;
        $amount = (float) ($this->request->getPost('balance') ?? $this->request->getPost('amount') ?? 0);
        $bankId = $this->request->getPost('bank_id');

        if (empty($bankId)) {
            return $this->response->setBody('0');
        }

        $bankRow = DB()->table('bank')
            ->where('bank_id', $bankId)
            ->where('sch_id', $shopId)
            ->where('deleted IS NULL')
            ->get()
            ->getRow();

        if (empty($bankRow)) {
            return $this->response->setBody('0');
        }

        $bankBalance = (float) $bankRow->balance;
        return $this->response->setBody($amount > $bankBalance ? '0' : '1');
    }

    public function old_create_action()
    {
        $userId   = $this->session->userId;
        $shopId   = $this->session->shopId;

        $customerId   = $this->request->getPost('customer_id');
        $customerName = $this->request->getPost('customer_name');
        $invoiceId    = $this->request->getPost('invoice_id');

        $productIds              = $this->request->getPost('returnchecked[]');
        $productStockRelationIds = $this->request->getPost('product_stock_relation_id[]');
        $quantities              = $this->request->getPost('quantity[]');
        $purchasePrices          = $this->request->getPost('purchase_price[]');

        $amount     = $this->request->getPost('totalPrice');
        $cashAmount = $this->request->getPost('cash');
        $bankAmount = $this->request->getPost('bank');
        $bankId     = $this->request->getPost('bank_id');
        $dueAmount  = $this->request->getPost('due');
        $totalVat   = empty($this->request->getPost('vatAmount')) ? 0 : $this->request->getPost('vatAmount');
        $totalDiscount   = empty($this->request->getPost('discountAmount')) ? 0 : $this->request->getPost('discountAmount');

        // Customer validation
        if (empty($customerName) && empty($customerId)) {
            return redirect()->to(site_url('Admin/Return_sale/return/' . $invoiceId));
        }

        if (empty($amount)) {
            $this->session->setFlashdata('message', '
            <div class="alert alert-danger alert-dismissible" role="alert">
                Please select any product
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>');
            return redirect()->to(site_url('Admin/Return_sale/return/' . $invoiceId));
        }

        // New customer must clear full amount (no due)
        if (!empty($customerName) && $dueAmount != 0) {
            $this->session->setFlashdata('message', '
            <div class="alert alert-danger alert-dismissible" role="alert">
                Please clear due
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>');
            return redirect()->to(site_url('Admin/Return_sale/return/' . $invoiceId));
        }

        if ($dueAmount < 0) {
            $this->session->setFlashdata('message', '
            <div class="alert alert-danger alert-dismissible" role="alert">
                Please enter a valid due amount
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>');
            return redirect()->to(site_url('Admin/Return_sale/return/' . $invoiceId));
        }

        DB()->transStart();

        // 1. Insert into return_sale
        $returnData = [
            'sch_id'       => $shopId,
            'invoice_id'   => $invoiceId,
            'amount'       => $amount,
            'nagad_paid'   => $cashAmount,
            'bank_paid'    => $bankAmount,
            'bank_id'      => $bankId,
            'createdBy'    => $userId,
            'createdDtm'   => date('Y-m-d H:i:s'),
        ];

        if (!empty($customerId)) {
            $returnData['customer_id'] = $customerId;
        } else {
            $returnData['customer_name'] = $customerName;
        }

        DB()->table('return_sale')->insert($returnData);
        $returnId = DB()->insertID();

        // 2. Update shop sale balance + ledger
        $saleBalance     = get_data_by_id('sale_balance', 'shops', 'sch_id', $shopId);
        $newSaleBalance  = $saleBalance + $amount - $totalVat;

        DB()->table('shops')
            ->where('sch_id', $shopId)
            ->update(['sale_balance' => $newSaleBalance]);

        DB()->table('ledger_sales')->insert([
            'sch_id'           => $shopId,
            'rtn_sale_id'      => $returnId,
            'trangaction_type' => 'Dr.',
            'particulars'      => 'return sale',
            'amount'           => $amount,
            'rest_balance'     => $newSaleBalance,
            'createdBy'        => $userId,
            'createdDtm'       => date('Y-m-d H:i:s'),
        ]);

        // 3. Process each returned item
        $totalPurchasePrice = 0;
        $itemCount = count($productIds);

        for ($i = 0; $i < $itemCount; $i++) {
            $qty   = $quantities[$i];
            $price = $purchasePrices[$i];
            $total = $qty * $price;

            // Insert return item
            DB()->table('return_sale_item')->insert([
                'sch_id'      => $shopId,
                'rtn_sale_id' => $returnId,
                'prod_id'     => $productIds[$i],
                'price'       => $price,
                'quantity'    => $qty,
                'total_price' => $total,
                'createdBy'   => $userId,
                'createdDtm'  => date('Y-m-d H:i:s'),
            ]);

            // Update product stock quantity
            $stock = DB()->table('product_stock_relation')
                ->where('product_stock_relation_id', $productStockRelationIds[$i])
                ->where('product_id', $productIds[$i])
                ->get()
                ->getRow();

            if ($stock) {
                $newQty = $stock->quantity + $qty;

                DB()->table('product_stock_relation')
                    ->where('product_stock_relation_id', $productStockRelationIds[$i])
                    ->where('product_id', $productIds[$i])
                    ->update(['quantity' => $newQty]);

                // Calculate total purchase cost
                $totalPurchasePrice += ($stock->purchase_price * $qty);
            }
        }

        // 4. VAT handling
        if (!empty($totalVat)) {
            $vatId      = get_data_by_id('vat_id', 'vat_register', 'sch_id', $shopId);
            $vatBalance = get_data_by_id('balance', 'vat_register', 'sch_id', $shopId);
            $newVatBal  = $vatBalance + $totalVat;

            DB()->table('vat_register')
                ->where('sch_id', $shopId)
                ->update(['balance' => $newVatBal]);

            DB()->table('ledger_vat')->insert([
                'vat_id'           => $vatId,
                'invoice_id'       => $invoiceId,
                'sch_id'           => $shopId,
                'particulars'      => 'Return Sale Vat return',
                'trangaction_type' => 'Dr.',
                'amount'           => $totalVat,
                'rest_balance'     => $newVatBal,
                'createdBy'        => $shopId,
            ]);
        }

        if (!empty($totalDiscount)) {
            $discountBalance = get_data_by_id('discount', 'shops', 'sch_id', $shopId);
            $newDiscountBal  = $discountBalance + $totalDiscount;

            DB()->table('shops')
                ->where('sch_id', $shopId)
                ->update(['discount' => $newDiscountBal]);

            DB()->table('ledger_discount')->insert([
                'invoice_id'       => $invoiceId,
                'sch_id'           => $shopId,
                'particulars'      => 'Return Sale Discount return',
                'trangaction_type' => 'Cr.',
                'amount'           => $totalDiscount,
                'rest_balance'     => $newDiscountBal,
                'createdBy'        => $userId,
            ]);
        }

        // 5. Customer balance + ledger (existing customer only)
        if (!empty($customerId)) {
            $oldCusBalance = get_data_by_id('balance', 'customers', 'customer_id', $customerId);
            $newCusBalance = $oldCusBalance - $amount;

            DB()->table('customers')
                ->where('customer_id', $customerId)
                ->update([
                    'balance'   => $newCusBalance,
                    'createdBy' => $userId,
                ]);

            DB()->table('ledger')->insert([
                'sch_id'           => $shopId,
                'customer_id'      => $customerId,
                'rtn_sale_id'      => $returnId,
                'particulars'      => 'Return Sale Product',
                'trangaction_type' => 'Cr.',
                'amount'           => $amount,
                'rest_balance'     => $newCusBalance,
                'createdBy'        => $userId,
                'createdDtm'       => date('Y-m-d H:i:s'),
            ]);

            // Affiliate commission reverse
            $affiliateUserId = get_data_by_id('affiliate_user_id', 'customers', 'customer_id', $customerId);
            if (!empty($affiliateUserId)) {
                $salesId          = get_data_by_id('sales_id', 'sales', 'invoice_id', $invoiceId);
                $commissionPercent = get_data_by_id('commission', 'commission', 'sales_id', $salesId);
                $oldAffBalance    = get_data_by_id('balance', 'affiliate_user', 'affiliate_user_id', $affiliateUserId);

                $commission      = ($amount * $commissionPercent) / 100;
                $newAffBalance   = $oldAffBalance - $commission;

                DB()->table('affiliate_user')
                    ->where('affiliate_user_id', $affiliateUserId)
                    ->update(['balance' => $newAffBalance]);
            }
        }

        // 6. Calculate & update return profit
        $returnProfit = $amount - $totalPurchasePrice - $totalVat;

        DB()->table('return_sale')
            ->where('rtn_sale_id', $returnId)
            ->update(['rtn_profit' => $returnProfit]);

        $shopProfit    = get_data_by_id('profit', 'shops', 'sch_id', $shopId);
        $newShopProfit = $shopProfit + $returnProfit;

        DB()->table('shops')
            ->where('sch_id', $shopId)
            ->update([
                'profit'    => $newShopProfit,
                'updatedBy' => $userId,
            ]);

        DB()->table('ledger_profit')->insert([
            'sch_id'           => $shopId,
            'rtn_sale_id'      => $returnId,
            'trangaction_type' => 'Dr.',
            'particulars'      => 'Return Profit',
            'amount'           => $returnProfit,
            'rest_balance'     => $newShopProfit,
            'createdBy'        => $userId,
            'createdDtm'       => date('Y-m-d H:i:s'),
        ]);

        // 7. Update stock amount + ledger
        $stockAmount    = get_data_by_id('stockAmount', 'shops', 'sch_id', $shopId);
        $newStockAmount = $stockAmount + $totalPurchasePrice;

        DB()->table('shops')
            ->where('sch_id', $shopId)
            ->update(['stockAmount' => $newStockAmount]);

        DB()->table('ledger_stock')->insert([
            'sch_id'           => $shopId,
            'rtn_sale_id'      => $returnId,
            'trangaction_type' => 'Dr.',
            'particulars'      => 'Return sale',
            'amount'           => $totalPurchasePrice,
            'rest_balance'     => $newStockAmount,
            'createdBy'        => $userId,
            'createdDtm'       => date('Y-m-d H:i:s'),
        ]);

        DB()->transComplete();

        $this->session->setFlashdata('message', '
        <div class="alert alert-success alert-dismissible" role="alert">
            Return Product Success
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>');

        return redirect()->to(site_url('Admin/Return_sale/'));
    }

    public function view($id){
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;

            $data['shopsName'] = get_data_by_id('name', 'shops', 'sch_id', $shopId);

            $returnSaleTable = DB()->table('return_sale');
            $data['returnSaleData'] = $returnSaleTable->where('rtn_sale_id', $id)->get()->getRow();

            $returnItem = DB()->table('return_sale_item');
            $data['invoiceItame'] = $returnItem->where('rtn_sale_id', $id)->get()->getResult();


            $data['menu'] = view('Admin/menu_sales', $data);
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Return_sale/view', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

}