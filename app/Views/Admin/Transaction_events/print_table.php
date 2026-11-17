<?php
// invoice logo (falls back to the shop logo, then to the built-in placeholder)
$invLogo = invoice_logo();
$logoImg = no_image_view('/uploads/schools/' . $invLogo, '/uploads/schools/no_image_logo.jpg', $invLogo);
$currentShopId = isset($id) ? $id : (isset($shopId) ? $shopId : (isset($_SESSION['shopId']) ? $_SESSION['shopId'] : 0));
$shopRow = DB()->table('shops')->where('sch_id', $currentShopId)->get()->getRow();

$st_date = isset($st_date) ? $st_date : '';
$en_date = isset($en_date) ? $en_date : '';
$event_type = isset($event_type) ? $event_type : '';

$eventsList = isset($result) ? $result : [];
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
    <h3 style="margin:0; text-transform: capitalize;">Transaction Events</h3>
    <?php if (!empty($event_type)) { ?>
        <div style="font-size: 13px;"><strong>Event Type:</strong> <?php echo ucfirst($event_type); ?></div>
    <?php } ?>
    <?php if (!empty($st_date) || !empty($en_date)) { ?>
        <div style="font-size: 13px;"><strong>Period:</strong> <?php echo !empty($st_date) ? $st_date : '...'; ?> to <?php echo !empty($en_date) ? $en_date : '...'; ?></div>
    <?php } ?>
    <table class="table table-bordered table-striped" style="width:auto; display:inline-table; margin-top:5px;" id="TFtable">
        <tr>
            <td>Total Events:</td>
            <td><?php echo count($eventsList); ?></td>
        </tr>
    </table>
</div>
<div class="col-md-12">
    <table class="table table-bordered table-striped text-capitalize">
        <thead>
        <tr>
            <th>No</th>
            <th>Date</th>
            <th>Event Type</th>
            <th>Reference</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = 1;
        foreach ($eventsList as $val) {
            $eventType = '---';
            $ref = '---';
            if (!empty($val->trans_id)){
                $eventType = 'Transaction';
                $ref = 'Trans #' . $val->trans_id;
            } elseif (!empty($val->sales_id)){
                $eventType = 'Sales';
                $invId = get_data_by_id('invoice_id','sales','sales_id',$val->sales_id);
                $ref = !empty($invId) ? 'Invoice #' . $invId : 'Sales #' . $val->sales_id;
            } elseif (!empty($val->purchase_id)){
                $eventType = 'Purchase';
                $ref = 'Purchase #' . $val->purchase_id;
            } elseif (!empty($val->dep_id)){
                $eventType = 'Bank Deposit';
                $ref = 'Deposit #' . $val->dep_id;
            } elseif (!empty($val->wthd_id)){
                $eventType = 'Bank Withdraw';
                $ref = 'Withdraw #' . $val->wthd_id;
            }
            ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo invoiceDateFormat($val->createdDtm); ?></td>
                <td><?php echo $eventType; ?></td>
                <td><?php echo $ref; ?></td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot>
        <tr>
            <th>No</th>
            <th>Date</th>
            <th>Event Type</th>
            <th>Reference</th>
        </tr>
        <tr>
            <th colspan="2" style="text-align: right;">Total Events:</th>
            <th colspan="2">= <?php echo count($eventsList); ?></th>
        </tr>
        </tfoot>
    </table>
</div>

