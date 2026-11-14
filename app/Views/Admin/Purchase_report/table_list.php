<?php
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
<div class="box">
    <div class="box-header">
        <div class="row">
            <div class="col-lg-12">
                <div class="col-md-4">
                    <h3 class="box-title">Purchase Report</h3>
                </div>
                <div class="col-md-4 text-center">
                    <?php if (!empty($st_date) && !empty($en_date)){ ?>
                        <small><?= $st_date;?> to <?= $en_date;?></small>
                    <?php } else { echo '<small>All data show</small>'; } ?>
                </div>
                <div class="col-md-4">
                    <span class="pull-right"><b style="color: green;">Total Amount: <?php echo showWithCurrencySymbol($totalAmount)?></b></span>
                </div>
            </div>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <table class="table table-bordered table-striped" id="example1">
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
            <?php $i = 0;
            foreach ($itemsList as $purchase) {
                $qty = isset($purchase->quantity) ? (float)$purchase->quantity : 0;
                $rowTotal = !empty($purchase->total_price) ? (float)$purchase->total_price : ($qty * (float)($purchase->purchase_price ?? 0));
                ?>
                <tr>
                    <td width="80px"><?php echo ++$i; ?></td>
                    <td><?php echo get_data_by_id('name','products','prod_id',$purchase->prod_id); ?></td>
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
    <!-- /.box-body -->
    <div class="row no-print">
        <div class="col-xs-12" style="margin-bottom: 20px;">
            <?php if (isset($print) && $print == 1){ ?>
                <button onclick="printDiv('ledgPrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print"></i> Print Now</button>
            <?php } ?>
            <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('ledgPrint','purchaseReport')"><i class="fa fa-file-pdf-o"></i> Download PDF</button>
            <?php } ?>
            <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('ledgPrint','purchaseReport')"><i class="fa fa-file-excel-o"></i> Download CSV</button>
            <?php } ?>
        </div>
    </div>
</div>
<!-- /.box -->

<div id="ajaxPurchaseReportPrint" style="display:none;">
    <?php echo view('Admin/Purchase_report/print_table', [
        'purchaseItem' => $itemsList,
        'supplier_id' => $supplier_id ?? '',
        'prod_id' => $prod_id ?? '',
        'st_date' => $st_date ?? '',
        'en_date' => $en_date ?? '',
        'shopId' => $shopId ?? ($this->session->shopId ?? 0),
    ]); ?>
</div>

