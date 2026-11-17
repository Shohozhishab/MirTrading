<?php
// invoice logo (falls back to the shop logo, then to the built-in placeholder)
$invLogo = invoice_logo();
$logoImg = no_image_view('/uploads/schools/' . $invLogo, '/uploads/schools/no_image_logo.jpg', $invLogo);
$currentShopId = isset($shopId) ? $shopId : (isset($_SESSION['shopId']) ? $_SESSION['shopId'] : 0);
$shopRow = DB()->table('shops')->where('sch_id', $currentShopId)->get()->getRow();

$st_date = isset($st_date) ? $st_date : '';
$en_date = isset($en_date) ? $en_date : '';
$supplier_id = isset($supplier_id) ? $supplier_id : '';
$prod_id = isset($prod_id) ? $prod_id : '';

$supplierName = !empty($supplier_id) ? get_data_by_id('name', 'suppliers', 'supplier_id', $supplier_id) : '';
$productName = !empty($prod_id) ? get_data_by_id('name', 'products', 'prod_id', $prod_id) : '';

// Flatten purchase items if nested
$itemsList = [];
$totalQuantity = 0;
$totalAmount = 0;

if (isset($purchaseItem) && is_array($purchaseItem)) {
    foreach ($purchaseItem as $item) {
        if (is_array($item)) {
            foreach ($item as $subItem) {
                $itemsList[] = $subItem;
            }
        } elseif (is_object($item)) {
            $itemsList[] = $item;
        }
    }
}

foreach ($itemsList as $val) {
    $qty = isset($val->quantity) ? (float)$val->quantity : 0;
    $rowTotal = !empty($val->total_price) ? (float)$val->total_price : ($qty * (float)($val->purchase_price ?? 0));
    $totalQuantity += $qty;
    $totalAmount += $rowTotal;
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
    <h3 style="margin:0; text-transform: capitalize;">Purchase Report</h3>
    <?php if (!empty($supplierName)) { ?>
        <div style="font-size: 13px;">Supplier: <b><?php echo $supplierName; ?></b></div>
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
            <td><b><?php echo count($itemsList); ?></b></td>
            <td>Total Quantity:</td>
            <td><b><?php echo $totalQuantity; ?></b></td>
            <td>Total Amount:</td>
            <td><b style="color: green;"><?php echo showWithCurrencySymbol($totalAmount); ?></b></td>
        </tr>
    </table>
</div>

<div class="col-md-12">
    <table class="table table-bordered table-striped text-capitalize">
        <thead>
        <tr>
            <th>No</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Purchase Price</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        <?php $j = 1;
        foreach ($itemsList as $purchase) {
            $qty = isset($purchase->quantity) ? (float)$purchase->quantity : 0;
            $rowTotal = !empty($purchase->total_price) ? (float)$purchase->total_price : ($qty * (float)($purchase->purchase_price ?? 0));
            ?>
            <tr>
                <td width="80px"><?php echo $j++; ?></td>
                <td><?php echo get_data_by_id('name', 'products', 'prod_id', $purchase->prod_id); ?></td>
                <td><?php echo $purchase->quantity; ?></td>
                <td><?php echo showWithCurrencySymbol($purchase->purchase_price); ?></td>
                <td><?php echo showWithCurrencySymbol($rowTotal); ?></td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot>
        <tr>
            <th colspan="2" class="text-right">Total:</th>
            <th><?= $totalQuantity; ?></th>
            <th></th>
            <th><?= showWithCurrencySymbol($totalAmount); ?></th>
        </tr>
        </tfoot>
    </table>
</div>

