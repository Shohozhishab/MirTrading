<?php
// invoice logo (falls back to the shop logo, then to the built-in placeholder)
$invLogo = invoice_logo();
$logoImg = no_image_view('/uploads/schools/' . $invLogo, '/uploads/schools/no_image_logo.jpg', $invLogo);
$currentShopId = isset($id) ? $id : (isset($shopId) ? $shopId : (isset($_SESSION['shopId']) ? $_SESSION['shopId'] : 0));
$shopRow = DB()->table('shops')->where('sch_id', $currentShopId)->get()->getRow();

$st_date = isset($st_date) ? $st_date : '';
$en_date = isset($en_date) ? $en_date : '';

$servicesList = isset($services) ? $services : [];

$totalAmount = 0;
foreach ($servicesList as $val) {
    $invAmount = get_data_by_id('final_amount', 'service_invoice', 'service_invoice_id', $val->service_invoice_id);
    $totalAmount += (float)$invAmount;
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
    <h3 style="margin:0; text-transform: capitalize;">Service List</h3>
    <?php if (!empty($st_date) || !empty($en_date)) { ?>
        <div style="font-size: 13px;">Period: <?php echo !empty($st_date) ? $st_date : '...'; ?> to <?php echo !empty($en_date) ? $en_date : '...'; ?></div>
    <?php } ?>
    <table class="table table-bordered table-striped" style="width:auto; display:inline-table; margin-top:5px;" id="TFtable">
        <tr>
            <td>Total Services:</td>
            <td><?php echo count($servicesList); ?></td>
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
            <th>Title</th>
            <th>Type</th>
            <th>Amount</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $start = 1;
        foreach ($servicesList as $val) {
            $invAmount = get_data_by_id('final_amount', 'service_invoice', 'service_invoice_id', $val->service_invoice_id);
            ?>
            <tr>
                <td width="80px"><?php echo $start++ ?></td>
                <td><?php echo $val->title ?></td>
                <td><?php echo showServiceName($val->service_type) ?></td>
                <td><?php echo showWithCurrencySymbol($invAmount) ?></td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot>
        <tr>
            <th>No</th>
            <th>Title</th>
            <th>Type</th>
            <th>Amount</th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th>Total</th>
            <th>= <?php echo showWithCurrencySymbol($totalAmount); ?></th>
        </tr>
        </tfoot>
    </table>
</div>

