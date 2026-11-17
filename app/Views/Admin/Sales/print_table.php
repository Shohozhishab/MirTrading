<?php
// invoice logo (falls back to the shop logo, then to the built-in placeholder)
$invLogo = invoice_logo();
$logoImg = no_image_view('/uploads/schools/' . $invLogo, '/uploads/schools/no_image_logo.jpg', $invLogo);
$shopRow = DB()->table('shops')->where('sch_id', isset($_SESSION['shopId']) ? $_SESSION['shopId'] : 0)->get()->getRow();

$st_date = isset($st_date) ? $st_date : '';
$en_date = isset($en_date) ? $en_date : '';
$customer_id = isset($customer_id) ? $customer_id : '';
$sales = isset($sales) ? $sales : [];
$profit = isset($profit) ? $profit : 0;

$customerName = !empty($customer_id) ? get_data_by_id('customer_name', 'customers', 'customer_id', $customer_id) : '';

$totalAmount = 0;
$totalDue = 0;
$totalProfit = 0;
foreach ($sales as $val) {
    $invAmount = get_data_by_id('amount', 'invoice', 'invoice_id', $val->invoice_id);
    $invDue = get_data_by_id('due', 'invoice', 'invoice_id', $val->invoice_id);
    $invProfit = get_data_by_id('profit', 'invoice', 'invoice_id', $val->invoice_id);
    $totalAmount += $invAmount;
    $totalDue += $invDue;
    $totalProfit += $invProfit;
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
    <h3 style="margin:0; text-transform: capitalize;">Sales List</h3>
    <?php if (!empty($customerName)) { ?>
        <div style="font-size: 14px; font-weight: bold;">Customer: <?php echo $customerName; ?></div>
    <?php } ?>
    <?php if (!empty($st_date) || !empty($en_date)) { ?>
        <div style="font-size: 13px;">Period: <?php echo !empty($st_date) ? $st_date : '...'; ?> to <?php echo !empty($en_date) ? $en_date : '...'; ?></div>
    <?php } ?>
    <table class="table table-bordered table-striped" style="width:auto; display:inline-table; margin-top:5px;" id="TFtable">
        <tr>
            <td>Total Sales:</td>
            <td><?php echo count($sales); ?></td>
            <td>Total Amount:</td>
            <td><?php echo showWithCurrencySymbol($totalAmount); ?></td>
            <td>Total Due:</td>
            <td><?php echo showWithCurrencySymbol($totalDue); ?></td>
            <?php if (isset($profit) && $profit == 1){ ?>
            <td>Total Profit:</td>
            <td><?php echo showWithCurrencySymbol($totalProfit); ?></td>
            <?php } ?>
        </tr>
    </table>
</div>
<div class="col-md-12">
    <table class="table table-bordered table-striped text-capitalize">
        <thead>
        <tr>
            <th>No</th>
            <th>Date</th>
            <th>Customer</th>
            <th>Invoice Id</th>
            <th>Total Amount</th>
            <th>Total Due</th>
            <?php if (isset($profit) && $profit == 1){ ?>
            <th>Profit</th>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
        <?php $j = 1;
        foreach ($sales as $item) {
            $cus_id = get_data_by_id('customer_id', 'invoice', 'invoice_id', $item->invoice_id);
            $cusName = !empty($cus_id) ? get_data_by_id('customer_name', 'customers', 'customer_id', $cus_id) : get_data_by_id('customer_name', 'invoice', 'invoice_id', $item->invoice_id);
            $profitor = get_data_by_id('profit', 'invoice', 'invoice_id', $item->invoice_id);
            ?>
            <tr>
                <td><?php echo $j++ ?></td>
                <td><?php echo invoiceDateFormat($item->date) ?></td>
                <td><?php echo $cusName ?></td>
                <td><?php echo $item->invoice_id ?></td>
                <td><?php echo showWithCurrencySymbol(get_data_by_id('amount', 'invoice', 'invoice_id', $item->invoice_id)) ?></td>
                <td><?php echo showWithCurrencySymbol(get_data_by_id('due', 'invoice', 'invoice_id', $item->invoice_id)) ?></td>
                <?php if (isset($profit) && $profit == 1){ ?>
                <td><?php echo showWithCurrencySymbol($profitor) ?></td>
                <?php } ?>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot>
        <tr>
            <th>No</th>
            <th>Date</th>
            <th>Customer</th>
            <th>Invoice Id</th>
            <th>Total Amount</th>
            <th>Total Due</th>
            <?php if (isset($profit) && $profit == 1){ ?>
            <th>Profit</th>
            <?php } ?>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th>Total</th>
            <th>= <?php echo showWithCurrencySymbol($totalAmount); ?></th>
            <th>= <?php echo showWithCurrencySymbol($totalDue); ?></th>
            <?php if (isset($profit) && $profit == 1){ ?>
            <th>= <?php echo showWithCurrencySymbol($totalProfit); ?></th>
            <?php } ?>
        </tr>
        </tfoot>
    </table>
</div>

