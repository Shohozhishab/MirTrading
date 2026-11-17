<?php
// invoice logo (falls back to the shop logo, then to the built-in placeholder)
$invLogo = invoice_logo();
$logoImg = no_image_view('/uploads/schools/' . $invLogo, '/uploads/schools/no_image_logo.jpg', $invLogo);
$shopRow = DB()->table('shops')->where('sch_id', isset($_SESSION['shopId']) ? $_SESSION['shopId'] : 0)->get()->getRow();

$st_date = isset($st_date) ? $st_date : '';
$en_date = isset($en_date) ? $en_date : '';
$transfer = isset($transfer) ? $transfer : [];

$totalQty = 0;
foreach ($transfer as $item) {
    $totalQty += get_stock_transfer_qty_by_id($item->stock_transfer_id) ?? 0;
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
    <h3 style="margin:0; text-transform: capitalize;">Stock Transfer</h3>
    <?php if (!empty($st_date) || !empty($en_date)) { ?>
        <div style="font-size: 13px;">Period: <?php echo !empty($st_date) ? $st_date : '...'; ?> to <?php echo !empty($en_date) ? $en_date : '...'; ?></div>
    <?php } ?>
    <table class="table table-bordered table-striped" style="width:auto; display:inline-table; margin-top:5px;" id="TFtable">
        <tr>
            <td>Total Transfers:</td>
            <td><?php echo count($transfer); ?></td>
            <td>Total Quantity:</td>
            <td><?php echo $totalQty; ?></td>
        </tr>
    </table>
</div>
<div class="col-md-12" >
    <table class="table table-bordered table-striped text-capitalize">
        <thead>
        <tr>
            <th>No</th>
            <th>Date</th>
            <th>From Store</th>
            <th>To Store</th>
            <th>Quantity</th>
        </tr>
        </thead>
        <tbody>
        <?php $i = 1; foreach ($transfer as $item) { ?>
            <tr>
                <td><?php echo $i++ ?></td>
                <td><?php echo globalTimeStamp($item->createdDtm); ?></td>
                <td><?php echo get_data_by_id('name','stores','store_id',$item->from_stock_id);?></td>
                <td><?php echo get_data_by_id('name','stores','store_id',$item->to_stock_id);?></td>
                <td><?php echo get_stock_transfer_qty_by_id($item->stock_transfer_id);?></td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot>
        <tr>
            <th>No</th>
            <th>Date</th>
            <th>From Store</th>
            <th>To Store</th>
            <th>Quantity</th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th>Total</th>
            <th>= <?php echo $totalQty; ?></th>
        </tr>
        </tfoot>
    </table>
</div>

