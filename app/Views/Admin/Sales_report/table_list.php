<?php
$profitPerm = permission_check('Sales', newSession()->role, 'profit');
if (!isset($profit)) {
    $profit = $profitPerm;
}

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
<div class="box">
    <div class="box-header">
        <div class="row">
            <div class="col-lg-12">
                <div class="col-md-4">
                    <h3 class="box-title">Sale Report List</h3>
                </div>
                <div class="col-md-4 text-center">
                    <?php if (!empty($st_date) && !empty($en_date)){ ?>
                        <small><?= $st_date;?> to <?= $en_date;?></small>
                    <?php } else { echo '<small>All data show</small>'; } ?>
                </div>
                <div class="col-md-4">
                    <?php if (isset($profit) && $profit == 1){ ?>
                        <span class="pull-right"><b style="color: green;">Total Profit: <?php echo showWithCurrencySymbol($totalProfit)?></b></span>
                    <?php } ?>
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
            <?php $i = 0;
            if (isset($sale) && is_array($sale)) {
                foreach ($sale as $val) { ?>
                    <tr>
                        <td width="15px"><?php echo ++$i ?></td>
                        <td><?php echo get_data_by_id('name','products','prod_id',$val->prod_id)?></td>
                        <td><?php echo showWithCurrencySymbol($val->price) ?></td>
                        <td><?php echo $val->quantity ?></td>
                        <td><?php echo showWithCurrencySymbol($val->total_price) ?></td>
                        <td><?php echo $val->discount ?></td>
                        <td><?php echo showWithCurrencySymbol($val->final_price) ?></td>
                        <?php if (isset($profit) && $profit == 1){ ?>
                            <td><?php echo showWithCurrencySymbol($val->profit) ?></td>
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
    <!-- /.box-body -->
    <div class="row no-print">
        <div class="col-xs-12" style="margin-bottom: 20px;">
            <?php if (isset($print) && $print == 1){ ?>
                <button onclick="printDiv('ledgPrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print"></i> Print Now</button>
            <?php } ?>
            <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('ledgPrint','salesReport')"><i class="fa fa-file-pdf-o"></i> Download PDF</button>
            <?php } ?>
            <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('ledgPrint','salesReport')"><i class="fa fa-file-excel-o"></i> Download CSV</button>
            <?php } ?>
        </div>
    </div>
</div>
<!-- /.box -->

<div id="ajaxSalesReportPrint" style="display:none;">
    <?php echo view('Admin/Sales_report/print_table', [
        'sale' => $sale,
        'customer_id' => $customer_id ?? '',
        'prod_id' => $prod_id ?? '',
        'st_date' => $st_date ?? '',
        'en_date' => $en_date ?? '',
        'profit' => $profit,
        'shopId' => $shopId ?? ($this->session->shopId ?? 0),
    ]); ?>
</div>
