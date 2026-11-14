<?php
$invLogo = invoice_logo();
$logoImg = no_image_view('/uploads/schools/' . $invLogo, '/uploads/schools/no_image_logo.jpg', $invLogo);
$activeCategory = isset($active_category) ? $active_category : '';
$stDate = isset($st_date) ? $st_date : '';
$enDate = isset($en_date) ? $en_date : '';

// Precompute totals for footers
$cusTotal = 0;
$supTotal = 0;
$loanTotal = 0;
$fundTotal = 0;
$expenseTotal = 0;
$otherIncomeTotal = 0;
$employeeTotal = 0;
$vatTotal = 0;
$assetsTotal = 0;

if (isset($transaction_data) && is_array($transaction_data)) {
    foreach ($transaction_data as $r) {
        if ($r->customer_id != NULL) {
            $cusTotal += (float)$r->amount;
        }
        if ($r->supplier_id != NULL) {
            $supTotal += (float)$r->amount;
        }
        if ($r->loan_pro_id != NULL) {
            $loanTotal += (float)$r->amount;
        }
        if ($r->bank_to_id != NULL) {
            $fundTotal += (float)$r->amount;
        }
        if ($r->employee_id != NULL) {
            $employeeTotal += (float)$r->amount;
        }
        if ($r->vat_id != NULL) {
            $vatTotal += (float)$r->amount;
        }
        if ($r->account_id != NULL) {
            $acType = accountIdByType($r->account_id);
            if (!empty($acType)) {
                if ($acType->type_key == 'expenses') {
                    $expenseTotal += (float)$r->amount;
                } elseif ($acType->type_key == 'other_income') {
                    $otherIncomeTotal += (float)$r->amount;
                } elseif ($acType->type_key == 'assets') {
                    $assetsTotal += (float)$r->amount;
                }
            }
        }
    }
}
?>
<div class="box">
    <div class="box-header">
        <div class="row">
            <div class="col-lg-12" style="margin-top: 20px;" id="messageAcc">
                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                <div id="message"></div>
            </div>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="panel with-nav-tabs panel-default" id="nav_panel">
            <ul class="nav nav-tabs" id="ul_border">
                <li class="<?php echo ($activeCategory == 'customer' || empty($activeCategory)) ? 'active' : ''; ?>"><a href="#customer" data-toggle="tab" onclick="$('#hidden_category').val('customer'); updateTransactionUrl();">Customer</a></li>
                <li class="<?php echo ($activeCategory == 'supplier') ? 'active' : ''; ?>"><a href="#supplier" data-toggle="tab" onclick="$('#hidden_category').val('supplier'); updateTransactionUrl();">Supplier</a></li>
                <li class="<?php echo ($activeCategory == 'loan_provider') ? 'active' : ''; ?>"><a href="#loanProvider" data-toggle="tab" onclick="$('#hidden_category').val('loan_provider'); updateTransactionUrl();">Account Head</a></li>
                <li class="<?php echo ($activeCategory == 'fund_transfer') ? 'active' : ''; ?>"><a href="#bank" data-toggle="tab" onclick="$('#hidden_category').val('fund_transfer'); updateTransactionUrl();">Fund Transfer</a></li>
                <li class="<?php echo ($activeCategory == 'expense') ? 'active' : ''; ?>"><a href="#expense" data-toggle="tab" onclick="$('#hidden_category').val('expense'); updateTransactionUrl();">Expense</a></li>
                <li class="<?php echo ($activeCategory == 'othersales') ? 'active' : ''; ?>"><a href="#othersales" data-toggle="tab" onclick="$('#hidden_category').val('othersales'); updateTransactionUrl();">Other Income</a></li>
                <li class="<?php echo ($activeCategory == 'employee') ? 'active' : ''; ?>"><a href="#employeeSalary" data-toggle="tab" onclick="$('#hidden_category').val('employee'); updateTransactionUrl();">Employee Salary</a></li>
                <li class="<?php echo ($activeCategory == 'vat') ? 'active' : ''; ?>"><a href="#vatpay" data-toggle="tab" onclick="$('#hidden_category').val('vat'); updateTransactionUrl();">Vat Pay</a></li>
                <li class="<?php echo ($activeCategory == 'assets') ? 'active' : ''; ?>"><a href="#assets" data-toggle="tab" onclick="$('#hidden_category').val('assets'); updateTransactionUrl();">Assets</a></li>
            </ul>
            <div class="panel-body">
                <div class="tab-content">
                    <!-- Customer Tab -->
                    <div class="tab-pane fade <?php echo ($activeCategory == 'customer' || empty($activeCategory)) ? 'active in' : ''; ?>" id="customer">
                        <div class="box-header">
                            <div class="col-md-3">
                                <h3 class="box-title">Customer Transaction List</h3>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control select2" onchange="filterByCategory('customer', 'hidden_customer_id', $(this).val());">
                                    <option value="">-- All Customers --</option>
                                    <?php foreach ($customers as $cus) { ?>
                                        <option value="<?php echo $cus->customer_id; ?>" <?php echo (($customer_id_filter ?? '') == $cus->customer_id) ? 'selected' : ''; ?>><?php echo $cus->customer_name; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered table-striped transaction" id="customer2">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Transaction Type</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 0;
                                    foreach ($transaction_data as $row) {
                                        if ($row->customer_id != NULL) { ?>
                                            <tr>
                                                <td><?php echo ++$i; ?></td>
                                                <td><?php echo invoiceDateFormat($row->date); ?></td>
                                                <td><?php echo get_data_by_id('customer_name', 'customers', 'customer_id', $row->customer_id); ?></td>
                                                <td><?php echo $row->trangaction_type; ?></td>
                                                <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                <td>
                                                    <?php if (isset($transaction_flow) && $transaction_flow == 1){ ?>
                                                        <a href="javascript:void(0)"
                                                           onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/transaction_flow/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/transaction_flow/' . $row->trans_id; ?>')"
                                                           class="btn btn-success btn-xs">Transaction Flow </a>
                                                    <?php } ?>
                                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/moneyReceipt/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/moneyReceipt/' . $row->trans_id; ?>')"
                                                       class="btn btn-xs btn-info">Money Receipt</a>
                                                    <?php if (isset($update) && $update == 1){ ?>
                                                        <a href="javascript:void(0)" class="btn btn-xs btn-warning" onclick="cusTranEdit('<?= $row->trans_id; ?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
                                                    <?php } ?>
                                                    <?php if (isset($read) && $read == 1){ ?>
                                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $row->trans_id; ?>')"
                                                           class="btn btn-xs btn-success">View</a>
                                                    <?php } ?>
                                                    <?php if (isset($delete) && $delete == 1){ ?>
                                                        <a href="<?php echo site_url('/Admin/Transaction/delete/' . $row->trans_id); ?>" onclick="return confirm('Are you sure you want to delete this item?');" class="btn btn-danger btn-xs">Delete</a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                    <?php }
                                    } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Transaction Type</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" style="text-align: right;">Total:</th>
                                        <th colspan="2">= <?php echo showWithCurrencySymbol($cusTotal); ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="row no-print">
                                <div class="col-xs-12">
                                    <?php if (isset($print) && $print == 1){ ?>
                                        <button onclick="printDiv('cusPrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print "></i> Print Now</button>
                                    <?php } ?>
                                    <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                                        <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('cusPrint','customer')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                    <?php } ?>
                                    <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                                        <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('cusPrint','customer')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="col-md-12" id="cusPrint" style="display: none; text-transform: capitalize; ">
                                <div class="col-xs-12" style="margin-bottom: 20px;">
                                    <div class="col-xs-6">
                                        <img src="<?php echo $logoImg; ?>" style="max-height:80px;max-width:220px;" alt="Logo">
                                    </div>
                                    <div class="col-xs-6">
                                        <?php print address(); ?>
                                    </div>
                                </div>
                                <?php if (!empty($stDate) || !empty($enDate)) { ?>
                                    <div class="col-xs-12" style="margin-bottom:10px; font-size:13px;">
                                        <strong>Period:</strong> <?php echo !empty($stDate) ? $stDate : '...'; ?> to <?php echo !empty($enDate) ? $enDate : '...'; ?>
                                    </div>
                                <?php } ?>
                                <div class="col-md-12">
                                    <table class="table table-bordered table-striped ">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Customer</th>
                                                <th>Transaction Type</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 0;
                                            foreach ($transaction_data as $row) {
                                                if ($row->customer_id != NULL) { ?>
                                                    <tr>
                                                        <td><?php echo ++$i; ?></td>
                                                        <td><?php echo get_data_by_id('customer_name', 'customers', 'customer_id', $row->customer_id); ?></td>
                                                        <td><?php echo $row->trangaction_type; ?></td>
                                                        <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                    </tr>
                                            <?php }
                                            } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" style="text-align: right;">Total:</th>
                                                <th>= <?php echo showWithCurrencySymbol($cusTotal); ?></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Supplier Tab -->
                    <div class="tab-pane fade <?php echo ($activeCategory == 'supplier') ? 'active in' : ''; ?>" id="supplier">
                        <div class="box-header">
                            <div class="col-md-3">
                                <h3 class="box-title">Supplier Transaction</h3>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control select2" onchange="filterByCategory('supplier', 'hidden_supplier_id', $(this).val());">
                                    <option value="">-- All Suppliers --</option>
                                    <?php foreach ($suppliers as $sup) { ?>
                                        <option value="<?php echo $sup->supplier_id; ?>" <?php echo (($supplier_id_filter ?? '') == $sup->supplier_id) ? 'selected' : ''; ?>><?php echo $sup->name; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered table-striped supplier" id="supplier2">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>Supplier</th>
                                        <th>Transaction Type</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 0;
                                    foreach ($transaction_data as $row) {
                                        if ($row->supplier_id != NULL) { ?>
                                            <tr>
                                                <td><?php echo ++$i; ?></td>
                                                <td><?php echo invoiceDateFormat($row->date); ?></td>
                                                <td><?php echo get_data_by_id('name', 'suppliers', 'supplier_id', $row->supplier_id); ?></td>
                                                <td><?php echo $row->trangaction_type; ?></td>
                                                <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                <td>
                                                    <?php if (isset($transaction_flow) && $transaction_flow == 1){ ?>
                                                        <a href="javascript:void(0)"
                                                           onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/transaction_flow/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/transaction_flow/' . $row->trans_id; ?>')"
                                                           class="btn btn-success btn-xs">Transaction Flow </a>
                                                    <?php } ?>
                                                    <?php if (isset($update) && $update == 1){ ?>
                                                        <a href="javascript:void(0)" class="btn btn-xs btn-warning" onclick="supplierTranEdit('<?= $row->trans_id; ?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
                                                    <?php } ?>
                                                    <?php if (isset($read) && $read == 1){ ?>
                                                        <a href="javascript:void(0)"
                                                           onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $row->trans_id; ?>')"
                                                           class="btn btn-xs btn-success">View</a>
                                                    <?php } ?>
                                                    <?php if (isset($delete) && $delete == 1){ ?>
                                                        <a href="<?php echo site_url('/Admin/Transaction/delete/' . $row->trans_id); ?>" onclick="return confirm('Are you sure you want to delete this item?');" class="btn btn-danger btn-xs">Delete</a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                    <?php }
                                    } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>Supplier</th>
                                        <th>Transaction Type</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" style="text-align: right;">Total:</th>
                                        <th colspan="2">= <?php echo showWithCurrencySymbol($supTotal); ?></th>
                                    </tr>
                                </tfoot>
                            </table>

                            <div class="row no-print">
                                <div class="col-xs-12">
                                    <?php if (isset($print) && $print == 1){ ?>
                                        <button onclick="printDiv('supplierPrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print "></i> Print Now</button>
                                    <?php } ?>
                                    <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                                        <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('supplierPrint','supplier')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                    <?php } ?>
                                    <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                                        <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('supplierPrint','supplier')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="col-md-12" id="supplierPrint" style="display: none; text-transform: capitalize; ">
                                <div class="col-xs-12" style="margin-bottom: 20px;">
                                    <div class="col-xs-6">
                                        <img src="<?php echo $logoImg; ?>" style="max-height:80px;max-width:220px;" alt="Logo">
                                    </div>
                                    <div class="col-xs-6">
                                        <?php print address(); ?>
                                    </div>
                                </div>
                                <?php if (!empty($stDate) || !empty($enDate)) { ?>
                                    <div class="col-xs-12" style="margin-bottom:10px; font-size:13px;">
                                        <strong>Period:</strong> <?php echo !empty($stDate) ? $stDate : '...'; ?> to <?php echo !empty($enDate) ? $enDate : '...'; ?>
                                    </div>
                                <?php } ?>
                                <div class="col-md-12">
                                    <table class="table table-bordered table-striped ">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Supplier</th>
                                                <th>Transaction Type</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 0;
                                            foreach ($transaction_data as $row) {
                                                if ($row->supplier_id != NULL) { ?>
                                                    <tr>
                                                        <td><?php echo ++$i; ?></td>
                                                        <td><?php echo get_data_by_id('name', 'suppliers', 'supplier_id', $row->supplier_id); ?></td>
                                                        <td><?php echo $row->trangaction_type; ?></td>
                                                        <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                    </tr>
                                            <?php }
                                            } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" style="text-align: right;">Total:</th>
                                                <th>= <?php echo showWithCurrencySymbol($supTotal); ?></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Head Tab -->
                    <div class="tab-pane fade <?php echo ($activeCategory == 'loan_provider') ? 'active in' : ''; ?>" id="loanProvider">
                        <div class="box-header">
                            <div class="col-md-3"><h3 class="box-title">Account Head Transaction</h3></div>
                            <div class="col-md-3">
                                <select class="form-control select2" onchange="filterByCategory('loan_provider', 'hidden_loan_pro_id', $(this).val());">
                                    <option value="">-- All Account Heads --</option>
                                    <?php foreach ($loan_providers as $lp) { ?>
                                        <option value="<?php echo $lp->loan_pro_id; ?>" <?php echo (($loan_pro_id_filter ?? '') == $lp->loan_pro_id) ? 'selected' : ''; ?>><?php echo $lp->name; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered table-striped loanProvider" id="account2">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>Account Holder</th>
                                        <th>Transaction Type</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 0;
                                    foreach ($transaction_data as $row) {
                                        if ($row->loan_pro_id != NULL) { ?>
                                            <tr>
                                                <td><?php echo ++$i; ?></td>
                                                <td><?php echo invoiceDateFormat($row->date); ?></td>
                                                <td><?php echo get_data_by_id('name', 'loan_provider', 'loan_pro_id', $row->loan_pro_id); ?></td>
                                                <td><?php echo $row->trangaction_type; ?></td>
                                                <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                <td>
                                                    <?php if (isset($transaction_flow) && $transaction_flow == 1){ ?>
                                                        <a href="javascript:void(0)"
                                                           onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/transaction_flow/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/transaction_flow/' . $row->trans_id; ?>')"
                                                           class="btn btn-success btn-xs">Transaction Flow </a>
                                                    <?php } ?>
                                                    <?php if (isset($update) && $update == 1){ ?>
                                                        <a href="javascript:void(0)" class="btn btn-xs btn-warning" onclick="accountTranEdit('<?= $row->trans_id; ?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
                                                    <?php } ?>
                                                    <?php if (isset($read) && $read == 1){ ?>
                                                        <a href="javascript:void(0)"
                                                           onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $row->trans_id; ?>')"
                                                           class="btn btn-xs btn-success">View</a>
                                                    <?php } ?>
                                                    <?php if (isset($delete) && $delete == 1){ ?>
                                                        <a href="<?php echo site_url('/Admin/Transaction/delete/' . $row->trans_id); ?>" onclick="return confirm('Are you sure you want to delete this item?');" class="btn btn-danger btn-xs">Delete</a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                    <?php }
                                    } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>Account Holder</th>
                                        <th>Transaction Type</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" style="text-align: right;">Total:</th>
                                        <th colspan="2">= <?php echo showWithCurrencySymbol($loanTotal); ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="row no-print">
                                <div class="col-xs-12">
                                    <?php if (isset($print) && $print == 1){ ?>
                                        <button onclick="printDiv('accountPrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print "></i> Print Now</button>
                                    <?php } ?>
                                    <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                                        <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('accountPrint','account')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                    <?php } ?>
                                    <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                                        <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('accountPrint','account')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="col-md-12" id="accountPrint" style="display: none; text-transform: capitalize; ">
                                <div class="col-xs-12" style="margin-bottom: 20px;">
                                    <div class="col-xs-6">
                                        <img src="<?php echo $logoImg; ?>" style="max-height:80px;max-width:220px;" alt="Logo">
                                    </div>
                                    <div class="col-xs-6">
                                        <?php print address(); ?>
                                    </div>
                                </div>
                                <?php if (!empty($stDate) || !empty($enDate)) { ?>
                                    <div class="col-xs-12" style="margin-bottom:10px; font-size:13px;">
                                        <strong>Period:</strong> <?php echo !empty($stDate) ? $stDate : '...'; ?> to <?php echo !empty($enDate) ? $enDate : '...'; ?>
                                    </div>
                                <?php } ?>
                                <div class="col-md-12">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Account Holder</th>
                                                <th>Transaction Type</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 0;
                                            foreach ($transaction_data as $row) {
                                                if ($row->loan_pro_id != NULL) { ?>
                                                    <tr>
                                                        <td><?php echo ++$i; ?></td>
                                                        <td><?php echo get_data_by_id('name', 'loan_provider', 'loan_pro_id', $row->loan_pro_id); ?></td>
                                                        <td><?php echo $row->trangaction_type; ?></td>
                                                        <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                    </tr>
                                            <?php }
                                            } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" style="text-align: right;">Total:</th>
                                                <th>= <?php echo showWithCurrencySymbol($loanTotal); ?></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fund Transfer Tab -->
                    <div class="tab-pane fade <?php echo ($activeCategory == 'fund_transfer') ? 'active in' : ''; ?>" id="bank">
                        <div class="box-header">
                            <div class="col-md-3"><h3 class="box-title">Fund Transfer</h3></div>
                            <div class="col-md-3">
                                <select class="form-control select2" onchange="filterByCategory('fund_transfer', 'hidden_bank_id', $(this).val());">
                                    <option value="">-- All Banks --</option>
                                    <?php foreach ($banks as $bank) { ?>
                                        <option value="<?php echo $bank->bank_id; ?>" <?php echo (($bank_id_filter ?? '') == $bank->bank_id) ? 'selected' : ''; ?>><?php echo $bank->name; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered table-striped banktrans" id="fund2">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>Bank</th>
                                        <th>Transaction Type</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 0;
                                    foreach ($transaction_data as $row) {
                                        if ($row->bank_to_id != NULL) { ?>
                                            <tr>
                                                <td><?php echo ++$i; ?></td>
                                                <td><?php echo invoiceDateFormat($row->date); ?></td>
                                                <td><?php echo get_data_by_id('name', 'bank', 'bank_id', $row->bank_id); ?></td>
                                                <td><?php echo $row->trangaction_type; ?></td>
                                                <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                <td>
                                                    <?php if (isset($transaction_flow) && $transaction_flow == 1){ ?>
                                                        <a href="javascript:void(0)"
                                                           onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/transaction_flow/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/transaction_flow/' . $row->trans_id; ?>')"
                                                           class="btn btn-success btn-xs">Transaction Flow </a>
                                                    <?php } ?>
                                                    <?php if (isset($update) && $update == 1){ ?>
                                                        <a href="javascript:void(0)" class="btn btn-xs btn-warning" onclick="fundTranEdit('<?= $row->trans_id; ?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
                                                    <?php } ?>
                                                    <?php if (isset($read) && $read == 1){ ?>
                                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $row->trans_id; ?>')"
                                                           class="btn btn-xs btn-success">View</a>
                                                    <?php } ?>
                                                    <?php if (isset($delete) && $delete == 1){ ?>
                                                        <a href="<?php echo site_url('/Admin/Transaction/delete/' . $row->trans_id); ?>" onclick="return confirm('Are you sure you want to delete this item?');" class="btn btn-danger btn-xs">Delete</a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                    <?php }
                                    } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>Bank</th>
                                        <th>Transaction Type</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" style="text-align: right;">Total:</th>
                                        <th colspan="2">= <?php echo showWithCurrencySymbol($fundTotal); ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="row no-print">
                                <div class="col-xs-12">
                                    <?php if (isset($print) && $print == 1){ ?>
                                        <button onclick="printDiv('fundPrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print "></i> Print Now</button>
                                    <?php } ?>
                                    <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                                        <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('fundPrint','fund')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                    <?php } ?>
                                    <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                                        <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('fundPrint','fund')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="col-md-12" id="fundPrint" style="display: none; text-transform: capitalize; ">
                                <div class="col-xs-12" style="margin-bottom: 20px;">
                                    <div class="col-xs-6">
                                        <img src="<?php echo $logoImg; ?>" style="max-height:80px;max-width:220px;" alt="Logo">
                                    </div>
                                    <div class="col-xs-6">
                                        <?php print address(); ?>
                                    </div>
                                </div>
                                <?php if (!empty($stDate) || !empty($enDate)) { ?>
                                    <div class="col-xs-12" style="margin-bottom:10px; font-size:13px;">
                                        <strong>Period:</strong> <?php echo !empty($stDate) ? $stDate : '...'; ?> to <?php echo !empty($enDate) ? $enDate : '...'; ?>
                                    </div>
                                <?php } ?>
                                <div class="col-md-12">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Bank</th>
                                                <th>Transaction Type</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 0;
                                            foreach ($transaction_data as $row) {
                                                if ($row->bank_to_id != NULL) { ?>
                                                    <tr>
                                                        <td><?php echo ++$i; ?></td>
                                                        <td><?php echo get_data_by_id('name', 'bank', 'bank_id', $row->bank_id); ?></td>
                                                        <td><?php echo $row->trangaction_type; ?></td>
                                                        <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                    </tr>
                                            <?php }
                                            } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" style="text-align: right;">Total:</th>
                                                <th>= <?php echo showWithCurrencySymbol($fundTotal); ?></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Expense Tab -->
                    <div class="tab-pane fade <?php echo ($activeCategory == 'expense') ? 'active in' : ''; ?>" id="expense">
                        <div class="box-header">
                            <h3 class="box-title">Expense Transaction</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered table-striped expense" id="expense2">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>Name</th>
                                        <th>Transaction Type</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 0;
                                    foreach ($transaction_data as $row) {
                                        if ($row->account_id != NULL) {
                                            $accountType = accountIdByType($row->account_id);
                                            if (!empty($accountType)){
                                                if ($accountType->type_key == 'expenses'){ ?>
                                                    <tr>
                                                        <td><?php echo ++$i; ?></td>
                                                        <td><?php echo invoiceDateFormat($row->date); ?></td>
                                                        <td><?php echo get_data_by_id('name', 'accounts', 'account_id', $row->account_id); ?></td>
                                                        <td><?php echo $row->trangaction_type; ?></td>
                                                        <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                        <td>
                                                            <?php if (isset($transaction_flow) && $transaction_flow == 1){ ?>
                                                                <a href="javascript:void(0)"
                                                                   onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/transaction_flow/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/transaction_flow/' . $row->trans_id; ?>')"
                                                                   class="btn btn-success btn-xs">Transaction Flow </a>
                                                            <?php } ?>
                                                            <?php if (isset($update) && $update == 1){ ?>
                                                                <a href="javascript:void(0)" class="btn btn-xs btn-warning" onclick="assetsTranEdit('<?= $row->trans_id; ?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
                                                            <?php } ?>
                                                            <?php if (isset($read) && $read == 1){ ?>
                                                                <a href="javascript:void(0)"
                                                                   onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $row->trans_id; ?>')"
                                                                   class="btn btn-xs btn-success">View</a>
                                                            <?php } ?>
                                                            <?php if (isset($delete) && $delete == 1){ ?>
                                                                <a href="<?php echo site_url('/Admin/Transaction/delete/' . $row->trans_id); ?>" onclick="return confirm('Are you sure you want to delete this item?');" class="btn btn-danger btn-xs">Delete</a>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                        <?php } } }
                                    } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>Name</th>
                                        <th>Transaction Type</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" style="text-align: right;">Total:</th>
                                        <th colspan="2">= <?php echo showWithCurrencySymbol($expenseTotal); ?></th>
                                    </tr>
                                </tfoot>
                            </table>

                            <div class="row no-print">
                                <div class="col-xs-12">
                                    <?php if (isset($print) && $print == 1){ ?>
                                        <button onclick="printDiv('expensePrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print "></i> Print Now</button>
                                    <?php } ?>
                                    <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                                        <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('expensePrint','expense')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                    <?php } ?>
                                    <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                                        <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('expensePrint','expense')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="col-md-12" id="expensePrint" style="display: none; text-transform: capitalize; ">
                                <div class="col-xs-12" style="margin-bottom: 20px;">
                                    <div class="col-xs-6">
                                        <img src="<?php echo $logoImg; ?>" style="max-height:80px;max-width:220px;" alt="Logo">
                                    </div>
                                    <div class="col-xs-6">
                                        <?php print address(); ?>
                                    </div>
                                </div>
                                <?php if (!empty($stDate) || !empty($enDate)) { ?>
                                    <div class="col-xs-12" style="margin-bottom:10px; font-size:13px;">
                                        <strong>Period:</strong> <?php echo !empty($stDate) ? $stDate : '...'; ?> to <?php echo !empty($enDate) ? $enDate : '...'; ?>
                                    </div>
                                <?php } ?>
                                <div class="col-md-12">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Name</th>
                                                <th>Transaction Type</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 0;
                                            foreach ($transaction_data as $row) {
                                                if ($row->account_id != NULL) {
                                                    $accountType = accountIdByType($row->account_id);
                                                    if (!empty($accountType)){
                                                        if ($accountType->type_key == 'expenses'){ ?>
                                                            <tr>
                                                                <td><?php echo ++$i; ?></td>
                                                                <td><?php echo get_data_by_id('name', 'accounts', 'account_id', $row->account_id); ?></td>
                                                                <td><?php echo $row->trangaction_type; ?></td>
                                                                <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                            </tr>
                                            <?php } } } } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" style="text-align: right;">Total:</th>
                                                <th>= <?php echo showWithCurrencySymbol($expenseTotal); ?></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Other Income Tab -->
                    <div class="tab-pane fade <?php echo ($activeCategory == 'othersales') ? 'active in' : ''; ?>" id="othersales">
                        <div class="box-header">
                            <h3 class="box-title">Other Income Transaction</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered table-striped othersales" id="other2">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>Name</th>
                                        <th>Transaction Type</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 0;
                                    foreach ($transaction_data as $row) {
                                        if ($row->account_id != NULL) {
                                            $accountType = accountIdByType($row->account_id);
                                            if (!empty($accountType)){
                                                if ($accountType->type_key == 'other_income'){ ?>
                                                    <tr>
                                                        <td><?php echo ++$i; ?></td>
                                                        <td><?php echo invoiceDateFormat($row->date); ?></td>
                                                        <td><?php echo get_data_by_id('name', 'accounts', 'account_id', $row->account_id); ?></td>
                                                        <td><?php echo $row->trangaction_type; ?></td>
                                                        <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                        <td>
                                                            <?php if (isset($transaction_flow) && $transaction_flow == 1){ ?>
                                                                <a href="javascript:void(0)"
                                                                   onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/transaction_flow/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/transaction_flow/' . $row->trans_id; ?>')"
                                                                   class="btn btn-success btn-xs">Transaction Flow </a>
                                                            <?php } ?>
                                                            <?php if (isset($update) && $update == 1){ ?>
                                                                <a href="javascript:void(0)" class="btn btn-xs btn-warning" onclick="otherIncomeTranEdit('<?= $row->trans_id; ?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
                                                            <?php } ?>
                                                            <?php if (isset($read) && $read == 1){ ?>
                                                                <a href="javascript:void(0)"
                                                                   onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $row->trans_id; ?>')"
                                                                   class="btn btn-xs btn-success">View</a>
                                                            <?php } ?>
                                                            <?php if (isset($delete) && $delete == 1){ ?>
                                                                <a href="<?php echo site_url('/Admin/Transaction/delete/' . $row->trans_id); ?>" onclick="return confirm('Are you sure you want to delete this item?');" class="btn btn-danger btn-xs">Delete</a>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                        <?php } } }
                                    } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>Name</th>
                                        <th>Transaction Type</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" style="text-align: right;">Total:</th>
                                        <th colspan="2">= <?php echo showWithCurrencySymbol($otherIncomeTotal); ?></th>
                                    </tr>
                                </tfoot>
                            </table>

                            <div class="row no-print">
                                <div class="col-xs-12">
                                    <?php if (isset($print) && $print == 1){ ?>
                                        <button onclick="printDiv('otherSalesPrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print "></i> Print Now</button>
                                    <?php } ?>
                                    <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                                        <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('otherSalesPrint','otherSales')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                    <?php } ?>
                                    <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                                        <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('otherSalesPrint','otherSales')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="col-md-12" id="otherSalesPrint" style="display: none; text-transform: capitalize; ">
                                <div class="col-xs-12" style="margin-bottom: 20px;">
                                    <div class="col-xs-6">
                                        <img src="<?php echo $logoImg; ?>" style="max-height:80px;max-width:220px;" alt="Logo">
                                    </div>
                                    <div class="col-xs-6">
                                        <?php print address(); ?>
                                    </div>
                                </div>
                                <?php if (!empty($stDate) || !empty($enDate)) { ?>
                                    <div class="col-xs-12" style="margin-bottom:10px; font-size:13px;">
                                        <strong>Period:</strong> <?php echo !empty($stDate) ? $stDate : '...'; ?> to <?php echo !empty($enDate) ? $enDate : '...'; ?>
                                    </div>
                                <?php } ?>
                                <div class="col-md-12">
                                    <table class="table table-bordered table-striped" >
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Date</th>
                                                <th>Name</th>
                                                <th>Transaction Type</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 0;
                                            foreach ($transaction_data as $row) {
                                                if ($row->account_id != NULL) {
                                                    $accountType = accountIdByType($row->account_id);
                                                    if (!empty($accountType)){
                                                        if ($accountType->type_key == 'other_income'){ ?>
                                                            <tr>
                                                                <td><?php echo ++$i; ?></td>
                                                                <td><?php echo invoiceDateFormat($row->date); ?></td>
                                                                <td><?php echo get_data_by_id('name', 'accounts', 'account_id', $row->account_id); ?></td>
                                                                <td><?php echo $row->trangaction_type; ?></td>
                                                                <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                            </tr>
                                            <?php } } } } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="4" style="text-align: right;">Total:</th>
                                                <th>= <?php echo showWithCurrencySymbol($otherIncomeTotal); ?></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Employee Salary Tab -->
                    <div class="tab-pane fade <?php echo ($activeCategory == 'employee') ? 'active in' : ''; ?>" id="employeeSalary">
                        <div class="box-header">
                            <div class="col-md-3"><h3 class="box-title">Employee Salary Transaction</h3></div>
                            <div class="col-md-3">
                                <select class="form-control select2" onchange="filterByCategory('employee', 'hidden_employee_id', $(this).val());">
                                    <option value="">-- All Employees --</option>
                                    <?php foreach ($employees as $emp) { ?>
                                        <option value="<?php echo $emp->employee_id; ?>" <?php echo (($employee_id_filter ?? '') == $emp->employee_id) ? 'selected' : ''; ?>><?php echo $emp->name; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered table-striped employeeSalary" id="employee2">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>Name</th>
                                        <th>Transaction Type</th>
                                        <th>Salary</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 0;
                                    foreach ($transaction_data as $row) {
                                        if ($row->employee_id != NULL) { ?>
                                            <tr>
                                                <td><?php echo ++$i; ?></td>
                                                <td><?php echo invoiceDateFormat($row->date); ?></td>
                                                <td><?php echo get_data_by_id('name', 'employee', 'employee_id', $row->employee_id); ?></td>
                                                <td><?php echo $row->trangaction_type; ?></td>
                                                <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                <td>
                                                    <?php if (isset($transaction_flow) && $transaction_flow == 1){ ?>
                                                        <a href="javascript:void(0)"
                                                           onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/transaction_flow/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/transaction_flow/' . $row->trans_id; ?>')"
                                                           class="btn btn-success btn-xs">Transaction Flow </a>
                                                    <?php } ?>
                                                    <a href="javascript:void(0)"
                                                       onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/salaryreceipt/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/salaryreceipt/' . $row->trans_id; ?>')"
                                                       class="btn btn-xs btn-info">Salary Receipt</a>
                                                    <?php if (isset($update) && $update == 1){ ?>
                                                        <a href="javascript:void(0)" class="btn btn-xs btn-warning " onclick="employeeTranEdit('<?= $row->trans_id; ?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
                                                    <?php } ?>
                                                    <?php if (isset($read) && $read == 1){ ?>
                                                        <a href="javascript:void(0)"
                                                           onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $row->trans_id; ?>')"
                                                           class="btn btn-xs btn-success">View</a>
                                                    <?php } ?>
                                                    <?php if (isset($delete) && $delete == 1){ ?>
                                                        <a href="<?php echo site_url('/Admin/Transaction/delete/' . $row->trans_id); ?>" onclick="return confirm('Are you sure you want to delete this item?');" class="btn btn-danger btn-xs">Delete</a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                    <?php }
                                    } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>Name</th>
                                        <th>Transaction Type</th>
                                        <th>Salary</th>
                                        <th>Action</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" style="text-align: right;">Total:</th>
                                        <th colspan="2">= <?php echo showWithCurrencySymbol($employeeTotal); ?></th>
                                    </tr>
                                </tfoot>
                            </table>

                            <div class="row no-print">
                                <div class="col-xs-12">
                                    <?php if (isset($print) && $print == 1){ ?>
                                        <button onclick="printDiv('employeePrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print "></i> Print Now</button>
                                    <?php } ?>
                                    <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                                        <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('employeePrint','employee')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                    <?php } ?>
                                    <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                                        <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('employeePrint','employee')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="col-md-12" id="employeePrint" style="display: none; text-transform: capitalize; ">
                                <div class="col-xs-12" style="margin-bottom: 20px;">
                                    <div class="col-xs-6">
                                        <img src="<?php echo $logoImg; ?>" style="max-height:80px;max-width:220px;" alt="Logo">
                                    </div>
                                    <div class="col-xs-6">
                                        <?php print address(); ?>
                                    </div>
                                </div>
                                <?php if (!empty($stDate) || !empty($enDate)) { ?>
                                    <div class="col-xs-12" style="margin-bottom:10px; font-size:13px;">
                                        <strong>Period:</strong> <?php echo !empty($stDate) ? $stDate : '...'; ?> to <?php echo !empty($enDate) ? $enDate : '...'; ?>
                                    </div>
                                <?php } ?>
                                <div class="col-md-12">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Name</th>
                                                <th>Transaction Type</th>
                                                <th>Salary</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 0;
                                            foreach ($transaction_data as $row) {
                                                if ($row->employee_id != NULL) { ?>
                                                    <tr>
                                                        <td><?php echo ++$i; ?></td>
                                                        <td><?php echo get_data_by_id('name', 'employee', 'employee_id', $row->employee_id); ?></td>
                                                        <td><?php echo $row->trangaction_type; ?></td>
                                                        <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                    </tr>
                                            <?php }
                                            } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" style="text-align: right;">Total:</th>
                                                <th>= <?php echo showWithCurrencySymbol($employeeTotal); ?></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vat Pay Tab -->
                    <div class="tab-pane fade <?php echo ($activeCategory == 'vat') ? 'active in' : ''; ?>" id="vatpay">
                        <div class="box-header">
                            <h3 class="box-title">Vat Pay</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered table-striped vatpay" id="vat2">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>Vat Register Name</th>
                                        <th>Transaction Type</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 0;
                                    foreach ($transaction_data as $row) {
                                        if ($row->vat_id != NULL) { ?>
                                            <tr>
                                                <td><?php echo ++$i; ?></td>
                                                <td><?php echo invoiceDateFormat($row->date); ?></td>
                                                <td><?php echo get_data_by_id('name', 'vat_register', 'vat_id', $row->vat_id); ?></td>
                                                <td><?php echo $row->trangaction_type; ?></td>
                                                <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                <td>
                                                    <?php if (isset($transaction_flow) && $transaction_flow == 1){ ?>
                                                        <a href="javascript:void(0)"
                                                           onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/transaction_flow/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/transaction_flow/' . $row->trans_id; ?>')"
                                                           class="btn btn-success btn-xs">Transaction Flow </a>
                                                    <?php } ?>
                                                    <?php if (isset($update) && $update == 1){ ?>
                                                        <a href="javascript:void(0)" class="btn btn-xs btn-warning" onclick="vatTranEdit('<?= $row->trans_id; ?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
                                                    <?php } ?>
                                                    <?php if (isset($read) && $read == 1){ ?>
                                                        <a href="javascript:void(0)"
                                                           onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $row->trans_id; ?>')"
                                                           class="btn btn-xs btn-success">View</a>
                                                    <?php } ?>
                                                    <?php if (isset($delete) && $delete == 1){ ?>
                                                        <a href="<?php echo site_url('/Admin/Transaction/delete/' . $row->trans_id); ?>" onclick="return confirm('Are you sure you want to delete this item?');" class="btn btn-danger btn-xs">Delete</a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                    <?php }
                                    } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>Vat Register Name</th>
                                        <th>Transaction Type</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" style="text-align: right;">Total:</th>
                                        <th colspan="2">= <?php echo showWithCurrencySymbol($vatTotal); ?></th>
                                    </tr>
                                </tfoot>
                            </table>

                            <div class="row no-print">
                                <div class="col-xs-12">
                                    <?php if (isset($print) && $print == 1){ ?>
                                        <button onclick="printDiv('vatPrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print "></i> Print Now</button>
                                    <?php } ?>
                                    <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                                        <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('vatPrint','vat')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                    <?php } ?>
                                    <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                                        <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('vatPrint','vat')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="col-md-12" id="vatPrint" style="display: none; text-transform: capitalize; ">
                                <div class="col-xs-12" style="margin-bottom: 20px;">
                                    <div class="col-xs-6">
                                        <img src="<?php echo $logoImg; ?>" style="max-height:80px;max-width:220px;" alt="Logo">
                                    </div>
                                    <div class="col-xs-6">
                                        <?php print address(); ?>
                                    </div>
                                </div>
                                <?php if (!empty($stDate) || !empty($enDate)) { ?>
                                    <div class="col-xs-12" style="margin-bottom:10px; font-size:13px;">
                                        <strong>Period:</strong> <?php echo !empty($stDate) ? $stDate : '...'; ?> to <?php echo !empty($enDate) ? $enDate : '...'; ?>
                                    </div>
                                <?php } ?>
                                <div class="col-md-12">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Vat Register Name</th>
                                                <th>Transaction Type</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 0;
                                            foreach ($transaction_data as $row) {
                                                if ($row->vat_id != NULL) { ?>
                                                    <tr>
                                                        <td><?php echo ++$i; ?></td>
                                                        <td><?php echo get_data_by_id('name', 'vat_register', 'vat_id', $row->vat_id); ?></td>
                                                        <td><?php echo $row->trangaction_type; ?></td>
                                                        <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                    </tr>
                                            <?php }
                                            } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" style="text-align: right;">Total:</th>
                                                <th>= <?php echo showWithCurrencySymbol($vatTotal); ?></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Assets Tab -->
                    <div class="tab-pane fade <?php echo ($activeCategory == 'assets') ? 'active in' : ''; ?>" id="assets">
                        <div class="box-header">
                            <h3 class="box-title">Assets</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered table-striped asstest" id="assets1">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>Name</th>
                                        <th>Transaction Type</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 0;
                                    foreach ($transaction_data as $row) {
                                        if ($row->account_id != NULL) {
                                            $accountType = accountIdByType($row->account_id);
                                            if (!empty($accountType)){
                                                if ($accountType->type_key == 'assets'){ ?>
                                                    <tr>
                                                        <td><?php echo ++$i; ?></td>
                                                        <td><?php echo invoiceDateFormat($row->date); ?></td>
                                                        <td><?php echo get_data_by_id('name', 'accounts', 'account_id', $row->account_id); ?></td>
                                                        <td><?php echo $row->trangaction_type; ?></td>
                                                        <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                        <td>
                                                            <?php if (isset($transaction_flow) && $transaction_flow == 1){ ?>
                                                                <a href="javascript:void(0)"
                                                                   onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/transaction_flow/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/transaction_flow/' . $row->trans_id; ?>')"
                                                                   class="btn btn-success btn-xs">Transaction Flow </a>
                                                            <?php } ?>
                                                            <?php if (isset($update) && $update == 1){ ?>
                                                                <a href="javascript:void(0)" class="btn btn-xs btn-warning" onclick="assetsTranEdit('<?= $row->trans_id; ?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
                                                            <?php } ?>
                                                            <?php if (isset($read) && $read == 1){ ?>
                                                                <a href="javascript:void(0)"
                                                                   onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $row->trans_id; ?>')"
                                                                   class="btn btn-xs btn-success">View</a>
                                                            <?php } ?>
                                                            <?php if (isset($delete) && $delete == 1){ ?>
                                                                <a href="<?php echo site_url('/Admin/Transaction/delete/' . $row->trans_id); ?>" onclick="return confirm('Are you sure you want to delete this item?');" class="btn btn-danger btn-xs">Delete</a>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                        <?php } } }
                                    } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>Name</th>
                                        <th>Transaction Type</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" style="text-align: right;">Total:</th>
                                        <th colspan="2">= <?php echo showWithCurrencySymbol($assetsTotal); ?></th>
                                    </tr>
                                </tfoot>
                            </table>

                            <div class="row no-print">
                                <div class="col-xs-12">
                                    <?php if (isset($print) && $print == 1){ ?>
                                        <button onclick="printDiv('assetsPrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print "></i> Print Now</button>
                                    <?php } ?>
                                    <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                                        <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('assetsPrint','assets')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                    <?php } ?>
                                    <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                                        <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('assetsPrint','assets')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="col-md-12" id="assetsPrint" style="display: none; text-transform: capitalize; ">
                                <div class="col-xs-12" style="margin-bottom: 20px;">
                                    <div class="col-xs-6">
                                        <img src="<?php echo $logoImg; ?>" style="max-height:80px;max-width:220px;" alt="Logo">
                                    </div>
                                    <div class="col-xs-6">
                                        <?php print address(); ?>
                                    </div>
                                </div>
                                <?php if (!empty($stDate) || !empty($enDate)) { ?>
                                    <div class="col-xs-12" style="margin-bottom:10px; font-size:13px;">
                                        <strong>Period:</strong> <?php echo !empty($stDate) ? $stDate : '...'; ?> to <?php echo !empty($enDate) ? $enDate : '...'; ?>
                                    </div>
                                <?php } ?>
                                <div class="col-md-12">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Name</th>
                                                <th>Transaction Type</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 0;
                                            foreach ($transaction_data as $row) {
                                                if ($row->account_id != NULL) {
                                                    $accountType = accountIdByType($row->account_id);
                                                    if (!empty($accountType)){
                                                        if ($accountType->type_key == 'assets'){ ?>
                                                            <tr>
                                                                <td><?php echo ++$i; ?></td>
                                                                <td><?php echo get_data_by_id('name', 'accounts', 'account_id', $row->account_id); ?></td>
                                                                <td><?php echo $row->trangaction_type; ?></td>
                                                                <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                            </tr>
                                            <?php } } } } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" style="text-align: right;">Total:</th>
                                                <th>= <?php echo showWithCurrencySymbol($assetsTotal); ?></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- /.box-body -->
</div>
<!-- /.box -->

