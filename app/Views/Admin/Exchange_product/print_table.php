<?php
// invoice logo (falls back to the shop logo, then to the built-in placeholder)
$invLogo = invoice_logo();
$logoImg = no_image_view('/uploads/schools/' . $invLogo, '/uploads/schools/no_image_logo.jpg', $invLogo);
$shopRow = DB()->table('shops')->where('sch_id', isset($_SESSION['shopId']) ? $_SESSION['shopId'] : 0)->get()->getRow();

$st_date = isset($st_date) ? $st_date : '';
$en_date = isset($en_date) ? $en_date : '';
$customerId = isset($customerId) ? $customerId : '';
$status = isset($status) ? $status : '';
$exchangeProduct = isset($exchangeProduct) ? $exchangeProduct : [];

$customerName = !empty($customerId) ? get_data_by_id('customer_name', 'customers', 'customer_id', $customerId) : '';

$totalQty = 0;
foreach ($exchangeProduct as $item) {
    $qty = ($item->type == 'Conditional')
        ? (get_exchange_conditional_product_qty_by_id($item->exchange_pro_id) ?? 0)
        : (get_exchange_unconditional_product_qty_by_id($item->exchange_pro_id) ?? 0);
    $totalQty += $qty;
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
    <h3 style="margin:0; text-transform: capitalize;">Replace Product</h3>
    <?php if (!empty($customerName)) { ?>
        <div style="font-size: 14px; font-weight: bold;">Customer: <?php echo $customerName; ?></div>
    <?php } ?>
    <?php if (!empty($st_date) || !empty($en_date)) { ?>
        <div style="font-size: 13px;">Period: <?php echo !empty($st_date) ? $st_date : '...'; ?> to <?php echo !empty($en_date) ? $en_date : '...'; ?></div>
    <?php } ?>
    <table class="table table-bordered table-striped" style="width:auto; display:inline-table; margin-top:5px;" id="TFtable">
        <tr>
            <td>Total Records:</td>
            <td><?php echo count($exchangeProduct); ?></td>
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
            <th>Type</th>
            <th>Status</th>
            <th>Customer</th>
            <th>Quantity</th>
            <th>Comment</th>
        </tr>
        </thead>
        <tbody>
        <?php $i = 1; foreach ($exchangeProduct as $item) {
            $qty = ($item->type == 'Conditional')
                ? (get_exchange_conditional_product_qty_by_id($item->exchange_pro_id) ?? 0)
                : (get_exchange_unconditional_product_qty_by_id($item->exchange_pro_id) ?? 0);
        ?>
            <tr>
                <td><?php echo $i++ ?></td>
                <td><?php echo globalTimeStamp($item->createdDtm); ?></td>
                <td><?php echo $item->type; ?></td>
                <td><?php echo get_exchange_status_by_id($item->exchange_pro_id); ?></td>
                <td><?php
                    if(!empty($item->customer_id)){
                        echo get_data_by_id('customer_name','customers','customer_id',$item->customer_id);
                    }else{
                        echo $item->customer_name;
                    }
                    ?></td>
                <td><?php echo $qty; ?></td>
                <td><?php echo $item->comment; ?></td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot>
        <tr>
            <th>No</th>
            <th>Date</th>
            <th>Type</th>
            <th>Status</th>
            <th>Customer</th>
            <th>Quantity</th>
            <th>Comment</th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>Total</th>
            <th>= <?php echo $totalQty; ?></th>
            <th></th>
        </tr>
        </tfoot>
    </table>
</div>

