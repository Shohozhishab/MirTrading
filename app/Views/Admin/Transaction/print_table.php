<?php
// invoice logo (falls back to the shop logo, then to the built-in placeholder)
$invLogo = invoice_logo();
$logoImg = no_image_view('/uploads/schools/' . $invLogo, '/uploads/schools/no_image_logo.jpg', $invLogo);
$currentShopId = isset($id) ? $id : (isset($shopId) ? $shopId : (isset($_SESSION['shopId']) ? $_SESSION['shopId'] : 0));
$shopRow = DB()->table('shops')->where('sch_id', $currentShopId)->get()->getRow();

$st_date = isset($st_date) ? $st_date : '';
$en_date = isset($en_date) ? $en_date : '';
$active_category = isset($active_category) ? $active_category : '';

$transactionList = isset($transaction_data) ? $transaction_data : [];

$totalAmount = 0;
foreach ($transactionList as $row) {
    $totalAmount += (float)$row->amount;
}
?>
<div class="col-xs-12" style="margin-bottom: 20px;">
    <div class="col-xs-6">
        <img src="<?php echo $logoImg; ?>" style="max-height:80px;max-width:220px;" alt="<?php echo !empty($shopRow->name) ? $shopRow->name : 'Logo'; ?>">
    </div>
    <div class="col-xs-6">
        <?php print address(); ?>
    </div>
</div>
<div class="col-xs-12" style="margin-bottom: 10px;">
    <h3 style="margin:0; text-transform: capitalize;">Transaction List <?php echo !empty($active_category) ? '(' . ucfirst($active_category) . ')' : ''; ?></h3>
    <?php if (!empty($st_date) || !empty($en_date)) { ?>
        <div style="font-size: 13px;">Period: <?php echo !empty($st_date) ? $st_date : '...'; ?> to <?php echo !empty($en_date) ? $en_date : '...'; ?></div>
    <?php } ?>
    <table class="table table-bordered table-striped" style="width:auto; display:inline-table; margin-top:5px;" id="TFtable">
        <tr>
            <td>Total Transactions:</td>
            <td><?php echo count($transactionList); ?></td>
            <td>Total Amount:</td>
            <td><?php echo showWithCurrencySymbol($totalAmount); ?></td>
        </tr>
    </table>
</div>
<div class="col-md-12">
    <table class="table table-bordered table-striped text-capitalize">
        <thead>
        <tr>
            <th>No</th>
            <th>Date</th>
            <th>Particulars</th>
            <th>Transaction Type</th>
            <th>Amount</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = 1;
        foreach ($transactionList as $row) {
            $particular = '---';
            if ($row->customer_id != NULL) {
                $particular = 'Customer: ' . get_data_by_id('customer_name', 'customers', 'customer_id', $row->customer_id);
            } elseif ($row->supplier_id != NULL) {
                $particular = 'Supplier: ' . get_data_by_id('name', 'suppliers', 'supplier_id', $row->supplier_id);
            } elseif ($row->loan_pro_id != NULL) {
                $particular = 'Account Head: ' . get_data_by_id('name', 'loan_provider', 'loan_pro_id', $row->loan_pro_id);
            } elseif ($row->bank_to_id != NULL) {
                $particular = 'Fund Transfer: ' . get_data_by_id('name', 'bank', 'bank_id', $row->bank_id);
            } elseif ($row->employee_id != NULL) {
                $particular = 'Employee: ' . get_data_by_id('name', 'employee', 'employee_id', $row->employee_id);
            } elseif ($row->vat_id != NULL) {
                $particular = 'Vat Pay';
            } elseif ($row->account_id != NULL) {
                $particular = get_data_by_id('name', 'account_heads', 'account_id', $row->account_id);
            } else {
                $particular = !empty($row->title) ? $row->title : 'Transaction';
            }
            ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo invoiceDateFormat($row->date); ?></td>
                <td><?php echo $particular; ?></td>
                <td><?php echo $row->trangaction_type; ?></td>
                <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot>
        <tr>
            <th>No</th>
            <th>Date</th>
            <th>Particulars</th>
            <th>Transaction Type</th>
            <th>Amount</th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th>Total</th>
            <th>= <?php echo showWithCurrencySymbol($totalAmount); ?></th>
        </tr>
        </tfoot>
    </table>
</div>

