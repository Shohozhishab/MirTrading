<?php
// invoice logo (falls back to the shop logo, then to the built-in placeholder)
$invLogo = invoice_logo();
$logoImg = no_image_view('/uploads/schools/' . $invLogo, '/uploads/schools/no_image_logo.jpg', $invLogo);
$shopRow = DB()->table('shops')->where('sch_id', isset($_SESSION['shopId']) ? $_SESSION['shopId'] : 0)->get()->getRow();

$st_date = isset($st_date) ? $st_date : '';
$en_date = isset($en_date) ? $en_date : '';
$customerId = isset($customerId) ? $customerId : '';
$return_sale_data = isset($return_sale_data) ? $return_sale_data : [];

$customerName = !empty($customerId) ? get_data_by_id('customer_name', 'customers', 'customer_id', $customerId) : '';

$totalAmount = 0;
foreach ($return_sale_data as $return) {
    $totalAmount += $return->amount ?? 0;
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
    <h3 style="margin:0; text-transform: capitalize;">Return Sales</h3>
    <?php if (!empty($customerName)) { ?>
        <div style="font-size: 14px; font-weight: bold;">Customer: <?php echo $customerName; ?></div>
    <?php } ?>
    <?php if (!empty($st_date) || !empty($en_date)) { ?>
        <div style="font-size: 13px;">Period: <?php echo !empty($st_date) ? $st_date : '...'; ?> to <?php echo !empty($en_date) ? $en_date : '...'; ?></div>
    <?php } ?>
    <table class="table table-bordered table-striped" style="width:auto; display:inline-table; margin-top:5px;" id="TFtable">
        <tr>
            <td>Total Returns:</td>
            <td><?php echo count($return_sale_data); ?></td>
            <td>Total Amount:</td>
            <td><?php echo showWithCurrencySymbol($totalAmount); ?></td>
        </tr>
    </table>
</div>
<div class="col-md-12" >
    <table class="table table-bordered table-striped text-capitalize">
        <thead>
        <tr>
            <th>No</th>
            <th>Date</th>
            <th>Customer</th>
            <th>Total Amount</th>
        </tr>
        </thead>
        <tbody>
        <?php $i = 1; foreach ($return_sale_data as $return) { ?>
            <tr>
                <td><?php echo $i++ ?></td>
                <td><?php echo invoiceDateFormat($return->createdDtm); ?></td>
                <td><?php
                    if (!empty($return->customer_id)) {
                        echo get_data_by_id('customer_name', 'customers', 'customer_id', $return->customer_id);
                    } else {
                        echo $return->customer_name;
                    }
                    ?></td>
                <td><?php echo showWithCurrencySymbol($return->amount); ?></td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot>
        <tr>
            <th>No</th>
            <th>Date</th>
            <th>Customer</th>
            <th>Total Amount</th>
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

