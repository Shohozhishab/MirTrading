<?php
// invoice logo (falls back to the shop logo, then to the built-in placeholder)
$invLogo = invoice_logo();
$logoImg = no_image_view('/uploads/schools/' . $invLogo, '/uploads/schools/no_image_logo.jpg', $invLogo);
$currentShopId = isset($id) ? $id : (isset($shopId) ? $shopId : (isset($_SESSION['shopId']) ? $_SESSION['shopId'] : 0));
$shopRow = DB()->table('shops')->where('sch_id', $currentShopId)->get()->getRow();

$st_date = isset($st_date) ? $st_date : '';
$en_date = isset($en_date) ? $en_date : '';
$category = isset($category) ? $category : '';
$sub_category = isset($sub_category) ? $sub_category : '';

$productsList = isset($products_data) ? $products_data : [];

$totalQty = 0;
$totalPurchaseValue = 0;
foreach ($productsList as $p) {
    $qty = productIdByQuantity($p->prod_id);
    $price = unitOrBasePriceByUnitPrice($p->unit, $p->purchase_price);
    $totalQty += (float)$qty;
    $totalPurchaseValue += ((float)$qty * (float)$price);
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
    <h3 style="margin:0; text-transform: capitalize;">Products List</h3>
    <?php if (!empty($category)) { ?>
        <div style="font-size: 13px;"><strong>Category:</strong> <?php echo get_data_by_id('product_category', 'product_category', 'prod_cat_id', $category); ?>
            <?php if (!empty($sub_category)) { ?>
                | <strong>Sub Category:</strong> <?php echo get_data_by_id('product_category', 'product_category', 'prod_cat_id', $sub_category); ?>
            <?php } ?>
        </div>
    <?php } ?>
    <?php if (!empty($st_date) || !empty($en_date)) { ?>
        <div style="font-size: 13px;"><strong>Period:</strong> <?php echo !empty($st_date) ? $st_date : '...'; ?> to <?php echo !empty($en_date) ? $en_date : '...'; ?></div>
    <?php } ?>
    <table class="table table-bordered table-striped" style="width:auto; display:inline-table; margin-top:5px;" id="TFtable">
        <tr>
            <td>Total Products:</td>
            <td><?php echo count($productsList); ?></td>
            <td>Total Quantity:</td>
            <td><?php echo $totalQty; ?></td>
            <td>Total Purchase Value:</td>
            <td><?php echo showWithCurrencySymbol($totalPurchaseValue); ?></td>
        </tr>
    </table>
</div>
<div class="col-md-12">
    <table class="table table-bordered table-striped text-capitalize">
        <thead>
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Quantity</th>
            <th>Purchase Price</th>
            <th>Supplier</th>
            <th>Prod Category</th>
            <th>Purchase Date</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = 1;
        foreach ($productsList as $products) {
            $qty = productIdByQuantity($products->prod_id);
            $price = unitOrBasePriceByUnitPrice($products->unit, $products->purchase_price);
            ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo $products->name; ?></td>
                <td><?php echo unitOrQtyByUnitQty($products->unit, $qty); ?> / <?php echo showUnitName($products->unit); ?></td>
                <td><?php echo showWithCurrencySymbol($price); ?></td>
                <td><?php echo get_data_by_id('name', 'suppliers', 'supplier_id', $products->supplier_id); ?></td>
                <td><?php echo get_data_by_id('product_category', 'product_category', 'prod_cat_id', $products->prod_cat_id); ?></td>
                <td><?php echo !empty($products->purchase_date) ? invoiceDateFormat($products->purchase_date) : '---'; ?></td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot>
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Quantity</th>
            <th>Purchase Price</th>
            <th>Supplier</th>
            <th>Prod Category</th>
            <th>Purchase Date</th>
        </tr>
        <tr>
            <th colspan="2" style="text-align: right;">Total:</th>
            <th>= <?php echo $totalQty; ?></th>
            <th>= <?php echo showWithCurrencySymbol($totalPurchaseValue); ?></th>
            <th colspan="3"></th>
        </tr>
        </tfoot>
    </table>
</div>

