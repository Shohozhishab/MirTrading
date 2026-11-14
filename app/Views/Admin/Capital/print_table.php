<?php
// invoice logo (falls back to the shop logo, then to the built-in placeholder)
$invLogo = invoice_logo();
$logoImg = no_image_view('/uploads/schools/' . $invLogo, '/uploads/schools/no_image_logo.jpg', $invLogo);
$currentShopId = isset($shopId) ? $shopId : (isset($_SESSION['shopId']) ? $_SESSION['shopId'] : 0);
$shopRow = DB()->table('shops')->where('sch_id', $currentShopId)->get()->getRow();

$st_date = isset($st_date) ? $st_date : '';
$en_date = isset($en_date) ? $en_date : '';
$capital = isset($capital) ? $capital : [];

$totalAmount = 0;
if (isset($capital) && is_array($capital)) {
    foreach ($capital as $val) {
        $totalAmount += (float)$val->amount;
    }
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
    <h3 style="margin:0; text-transform: capitalize;">Capital List</h3>
    <?php if (!empty($st_date) || !empty($en_date)) { ?>
        <div style="font-size: 13px;">Period: <?php echo !empty($st_date) ? $st_date : '...'; ?> to <?php echo !empty($en_date) ? $en_date : '...'; ?></div>
    <?php } else { ?>
        <div style="font-size: 13px;">Period: All data show</div>
    <?php } ?>
    <table class="table table-bordered table-striped" style="width:auto; display:inline-table; margin-top:5px;" id="TFtable">
        <tr>
            <td>Total Records:</td>
            <td><b><?php echo count($capital); ?></b></td>
            <td>Total Capital Amount:</td>
            <td><b style="color: green;"><?php echo showWithCurrencySymbol($totalAmount); ?></b></td>
        </tr>
    </table>
</div>

<div class="col-md-12">
    <table class="table table-bordered table-striped text-capitalize">
        <thead>
        <tr>
            <th>No</th>
            <th>Date</th>
            <th>Description</th>
            <th>Amount</th>
        </tr>
        </thead>
        <tbody>
        <?php $j = 1;
        if (isset($capital) && is_array($capital)) {
            foreach ($capital as $val) { ?>
                <tr>
                    <td width="80px"><?php echo $j++; ?></td>
                    <td><?php echo $val->createdDtm; ?></td>
                    <td><?php echo $val->description; ?></td>
                    <td><?php echo showWithCurrencySymbol($val->amount); ?></td>
                </tr>
            <?php }
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <th colspan="3" class="text-right">Total:</th>
            <th><?= showWithCurrencySymbol($totalAmount); ?></th>
        </tr>
        </tfoot>
    </table>
</div>

