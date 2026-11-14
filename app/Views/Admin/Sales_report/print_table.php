<?php
// invoice logo (falls back to the shop logo, then to the built-in placeholder)
$invLogo = invoice_logo();
$logoImg = no_image_view('/uploads/schools/' . $invLogo, '/uploads/schools/no_image_logo.jpg', $invLogo);
$currentShopId = isset($shopId) ? $shopId : (isset($_SESSION['shopId']) ? $_SESSION['shopId'] : 0);
$shopRow = DB()->table('shops')->where('sch_id', $currentShopId)->get()->getRow();

$st_date = isset($st_date) ? $st_date : '';
$en_date = isset($en_date) ? $en_date : '';
$customer_id = isset($customer_id) ? $customer_id : '';
$prod_id = isset($prod_id) ? $prod_id : '';
$sale = isset($sale) ? $sale : [];
$profit = isset($profit) ? $profit : (permission_check('Sales', newSession()->role, 'profit') ?? 0);

$customerName = !empty($customer_id) ? get_data_by_id('customer_name', 'customers', 'customer_id', $customer_id) : '';
$productName = !empty($prod_id) ? get_data_by_id('name', 'products', 'prod_id', $prod_id) : '';

$totalQuantity = 0;
$totalPrice = 0;
$totalDiscount = 0;
$totalFinalPrice = 0;
$totalProfit = 0;

if (isset($sale) && is_array($sale)) {
    foreach ($sale as $val) {
        $totalQuantity += (float)$val->quantity;
        $totalPrice += (float)$val->total_price;
        $totalDiscount += (float)$val->discount;
        $totalFinalPrice += (float)$val->final_price;
        $totalProfit += (float)$val->profit;
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
    <h3 style="margin:0; text-transform: capitalize;">Sale Report</h3>
    <?php if (!empty($customerName)) { ?>
        <div style="font-size: 13px;">Customer: <b><?php echo $customerName; ?></b></div>
    <?php } ?>
    <?php if (!empty($productName)) { ?>
        <div style="font-size: 13px;">Product: <b><?php echo $productName; ?></b></div>
    <?php } ?>
    <?php if (!empty($st_date) || !empty($en_date)) { ?>
        <div style="font-size: 13px;">Period: <?php echo !empty($st_date) ? $st_date : '...'; ?> to <?php echo !empty($en_date) ? $en_date : '...'; ?></div>
    <?php } else { ?>
        <div style="font-size: 13px;">Period: All data show</div>
    <?php } ?>
    <table class="table table-bordered table-striped" style="width:auto; display:inline-table; margin-top:5px;" id="TFtable">
        <tr>
            <td>Total Items:</td>
            <td><b><?php echo count($sale); ?></b></td>
            <td>Total Quantity:</td>
            <td><b><?php echo $totalQuantity; ?></b></td>
            <td>Total Price:</td>
            <td><b><?php echo showWithCurrencySymbol($totalPrice); ?></b></td>
            <td>Total Discount:</td>
            <td><b><?php echo showWithCurrencySymbol($totalDiscount); ?></b></td>
            <td>Final Price:</td>
            <td><b><?php echo showWithCurrencySymbol($totalFinalPrice); ?></b></td>
            <?php if (isset($profit) && $profit == 1){ ?>
            <td>Total Profit:</td>
            <td><b style="color: green;"><?php echo showWithCurrencySymbol($totalProfit); ?></b></td>
            <?php } ?>
        </tr>
    </table>
</div>

<div class="col-md-12">
    <table class="table table-bordered table-striped text-capitalize">
        <thead>
        <tr>
            <th>No</th>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total Price</th>
            <th>Discount</th>
            <th>Final Price</th>
            <?php if (isset($profit) && $profit == 1){ ?>
                <th>Profit</th>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
        <?php $j = 1;
        if (isset($sale) && is_array($sale)) {
            foreach ($sale as $val) { ?>
                <tr>
                    <td width="15px"><?php echo $j++; ?></td>
                    <td><?php echo get_data_by_id('name', 'products', 'prod_id', $val->prod_id); ?></td>
                    <td><?php echo showWithCurrencySymbol($val->price); ?></td>
                    <td><?php echo $val->quantity; ?></td>
                    <td><?php echo showWithCurrencySymbol($val->total_price); ?></td>
                    <td><?php echo $val->discount; ?></td>
                    <td><?php echo showWithCurrencySymbol($val->final_price); ?></td>
                    <?php if (isset($profit) && $profit == 1){ ?>
                        <td><?php echo showWithCurrencySymbol($val->profit); ?></td>
                    <?php } ?>
                </tr>
            <?php }
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <th colspan="3" class="text-right">Total:</th>
            <th><?= $totalQuantity; ?></th>
            <th><?= showWithCurrencySymbol($totalPrice); ?></th>
            <th><?= showWithCurrencySymbol($totalDiscount); ?></th>
            <th><?= showWithCurrencySymbol($totalFinalPrice); ?></th>
            <?php if (isset($profit) && $profit == 1){ ?>
                <th><?= showWithCurrencySymbol($totalProfit); ?></th>
            <?php } ?>
        </tr>
        </tfoot>
    </table>
</div>
