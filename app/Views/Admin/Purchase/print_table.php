<?php
// invoice logo (falls back to the shop logo, then to the built-in placeholder)
$invLogo = invoice_logo();
$logoImg = no_image_view('/uploads/schools/' . $invLogo, '/uploads/schools/no_image_logo.jpg', $invLogo);
$currentShopId = isset($id) ? $id : (isset($shopId) ? $shopId : (isset($_SESSION['shopId']) ? $_SESSION['shopId'] : 0));
$shopRow = DB()->table('shops')->where('sch_id', $currentShopId)->get()->getRow();

$supplier_id = isset($supplier_id) ? $supplier_id : '';
$st_date = isset($st_date) ? $st_date : '';
$en_date = isset($en_date) ? $en_date : '';

$purchaseList = isset($purchase_data) ? $purchase_data : [];

$totalAmount = 0;
$totalDue = 0;
foreach ($purchaseList as $purchase) {
    $pAmount = !empty($purchase->amount) ? $purchase->amount : get_data_by_id('amount', 'purchase', 'purchase_id', $purchase->purchase_id);
    $pDue = !empty($purchase->due) ? $purchase->due : get_data_by_id('due', 'purchase', 'purchase_id', $purchase->purchase_id);
    $totalAmount += (float)$pAmount;
    $totalDue += (float)$pDue;
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
    <h3 style="margin:0; text-transform: capitalize;">Purchase List</h3>
    <?php if (!empty($supplier_id)) { ?>
        <div style="font-size: 13px;">Supplier: <b><?php echo get_data_by_id('name', 'suppliers', 'supplier_id', $supplier_id); ?></b></div>
    <?php } ?>
    <?php if (!empty($st_date) || !empty($en_date)) { ?>
        <div style="font-size: 13px;">Period: <?php echo !empty($st_date) ? $st_date : '...'; ?> to <?php echo !empty($en_date) ? $en_date : '...'; ?></div>
    <?php } ?>
    <table class="table table-bordered table-striped" style="width:auto; display:inline-table; margin-top:5px;" id="TFtable">
        <tr>
            <td>Total Purchases:</td>
            <td><?php echo count($purchaseList); ?></td>
            <td>Total Amount:</td>
            <td><?php echo showWithCurrencySymbol($totalAmount); ?></td>
            <td>Total Due:</td>
            <td><?php echo showWithCurrencySymbol($totalDue); ?></td>
        </tr>
    </table>
</div>
<div class="col-md-12">
    <table class="table table-bordered table-striped text-capitalize">
        <thead>
        <tr>
            <th>No</th>
            <th>Purchase Date</th>
            <th>Supplier</th>
            <th>Total Amount</th>
            <th>Due</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = 1;
        foreach ($purchaseList as $purchase) {
            $pAmount = !empty($purchase->amount) ? $purchase->amount : get_data_by_id('amount', 'purchase', 'purchase_id', $purchase->purchase_id);
            $pDue = !empty($purchase->due) ? $purchase->due : get_data_by_id('due', 'purchase', 'purchase_id', $purchase->purchase_id);
            ?>
            <tr>
                <td><?php echo $i++ ?></td>
                <td><?php echo invoiceDateFormat($purchase->date) ?></td>
                <td><?php echo get_data_by_id('name', 'suppliers', 'supplier_id', $purchase->supplier_id); ?></td>
                <td><?php echo showWithCurrencySymbol($pAmount); ?></td>
                <td><?php echo showWithCurrencySymbol($pDue); ?></td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot>
        <tr>
            <th>No</th>
            <th>Purchase Date</th>
            <th>Supplier</th>
            <th>Total Amount</th>
            <th>Due</th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th>Total</th>
            <th>= <?php echo showWithCurrencySymbol($totalAmount); ?></th>
            <th>= <?php echo showWithCurrencySymbol($totalDue); ?></th>
        </tr>
        </tfoot>
    </table>
</div>

