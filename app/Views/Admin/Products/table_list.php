<?php
$invLogo = invoice_logo();
$logoImg = no_image_view('/uploads/schools/' . $invLogo, '/uploads/schools/no_image_logo.jpg', $invLogo);

$category = isset($category) ? $category : '';
$sub_category = isset($sub_category) ? $sub_category : '';
$st_date = isset($st_date) ? $st_date : '';
$en_date = isset($en_date) ? $en_date : '';

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
<div class="box">
    <div class="box-header">
        <div class="row">
            <div class="col-lg-4">
                <h3 class="box-title">Products List</h3>
            </div>
            <div class="col-lg-8">
                <a href="<?= base_url('Admin/Products/add_existing_product/')?>" class="btn btn-info pull-right btn-xs" style="margin-left:10px;"><i class="fa fa-fw fa-tasks"></i> Add Existing Product</a>
                <a href="<?= base_url('Admin/Products/add_product/')?>" class="btn btn-warning pull-right btn-xs" style="margin-left:10px;"><i class="fa fa-fw fa-tasks"></i> Add Product</a>
                <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Products_short_list_ajax/'); ?>','<?php echo '/Admin/Products_short_list/'; ?>')" class="btn btn-danger pull-right btn-xs" style="margin-left:10px;"><i class="fa fa-fw fa-tasks"></i> Short List</a>

                <button type="submit" class="btn btn-primary pull-right btn-xs"><i class="fa fa-barcode"></i> Barcode Generate</button>
            </div>
            <div class="col-lg-12" style="margin-top: 20px;">
                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
            </div>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <table id="example1" class="table table-bordered table-striped text-capitalize">
            <thead>
            <tr>
                <th>Barcode Qty</th>
                <th>Name</th>
                <th>Quantity</th>
                <th>Purchase Price</th>
                <th>Supplier</th>
                <th>Prod Category</th>
                <th>Purchase Date</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $i = 1;
            foreach ($productsList as $products) {
                $pQty = productIdByQuantity($products->prod_id);
                $pPrice = unitOrBasePriceByUnitPrice($products->unit, $products->purchase_price);
            ?>
                <tr>
                    <td>
                        <input type="number" name="barcodeqty[<?php print $products->prod_id; ?>]" style="width: 40px;" value="0">
                    </td>
                    <td><?php echo $products->name ?></td>
                    <td><?php echo unitOrQtyByUnitQty($products->unit, $pQty) ?> / <?php echo showUnitName($products->unit) ?></td>
                    <td><?php echo showWithCurrencySymbol($pPrice) ?></td>
                    <td><?php echo get_data_by_id('name', 'suppliers', 'supplier_id', $products->supplier_id) ?></td>
                    <td><?php echo get_data_by_id('product_category', 'product_category', 'prod_cat_id', $products->prod_cat_id) ?></td>
                    <td><?php echo !empty($products->purchase_date) ? invoiceDateFormat($products->purchase_date) : '---'; ?></td>
                    <td>
                        <?php if (isset($update) && $update == 1){ ?>
                            <a href="javascript:void(0)"
                               onclick="showData('<?php echo site_url('/Admin/Products_ajax/update/' . $products->prod_id); ?>','<?php echo '/Admin/Products/update/' . $products->prod_id; ?>')"
                               class="btn btn-warning btn-xs">Update</a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
                <th>Barcode Qty</th>
                <th>Name</th>
                <th>Quantity</th>
                <th>Purchase Price</th>
                <th>Supplier</th>
                <th>Prod Category</th>
                <th>Purchase Date</th>
                <th>Action</th>
            </tr>
            <tr>
                <th colspan="2" style="text-align: right;">Total:</th>
                <th>= <?php echo $totalQty; ?></th>
                <th>= <?php echo showWithCurrencySymbol($totalPurchaseValue); ?></th>
                <th colspan="4"></th>
            </tr>
            </tfoot>
        </table>

        <div class="row no-print">
            <div class="col-xs-12">
                <?php if (isset($print) && $print == 1){ ?>
                    <button type="button" onclick="printDiv('ledgPrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print "></i> Print Now</button>
                <?php } ?>
                <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                    <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('ledgPrint','products')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                <?php } ?>
                <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                    <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('ledgPrint','products')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                <?php } ?>
            </div>
        </div>

        <div class="col-md-12" id="ledgPrint" style="display: none; text-transform: capitalize;">
            <div class="col-xs-12" style="margin-bottom: 20px;">
                <div class="col-xs-6">
                    <img src="<?php echo $logoImg; ?>" style="max-height:80px;max-width:220px;" alt="Logo">
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
                    <?php $j = 1;
                    foreach ($productsList as $products) {
                        $pQty = productIdByQuantity($products->prod_id);
                        $pPrice = unitOrBasePriceByUnitPrice($products->unit, $products->purchase_price);
                    ?>
                        <tr>
                            <td><?php echo $j++; ?></td>
                            <td><?php echo $products->name ?></td>
                            <td><?php echo unitOrQtyByUnitQty($products->unit, $pQty) ?> / <?php echo showUnitName($products->unit) ?></td>
                            <td><?php echo showWithCurrencySymbol($pPrice) ?></td>
                            <td><?php echo get_data_by_id('name', 'suppliers', 'supplier_id', $products->supplier_id) ?></td>
                            <td><?php echo get_data_by_id('product_category', 'product_category', 'prod_cat_id', $products->prod_cat_id) ?></td>
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
        </div>
    </div>
    <!-- /.box-body -->
</div>
<!-- /.box -->
<div id="ajaxProductPrint" style="display:none;">
    <?php echo view('Admin/Products/print_table', [
        'products_data' => $productsList,
        'category' => $category,
        'sub_category' => $sub_category,
        'st_date' => $st_date,
        'en_date' => $en_date,
    ]); ?>
</div>

