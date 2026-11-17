<?php
$totalAmount = 0;
if (isset($capital) && is_array($capital)) {
    foreach ($capital as $val) {
        $totalAmount += (float)$val->amount;
    }
}
?>
<div class="box">
    <div class="box-body">
        <table id="example1" class="table table-bordered table-striped text-capitalize">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
            <?php $start = 1;
            if (isset($capital) && is_array($capital)) {
                foreach ($capital as $val) { ?>
                    <tr>
                        <td width="80px"><?php echo $start++; ?></td>
                        <td><?php echo $val->createdDtm; ?></td>
                        <td><?php echo $val->description; ?></td>
                        <td><?php echo showWithCurrencySymbol($val->amount); ?></td>
                    </tr>
                <?php }
            } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-right">Total:</th>
                    <th><?= showWithCurrencySymbol($totalAmount); ?></th>
                </tr>
            </tfoot>
        </table>

        <div class="row no-print">
            <div class="col-xs-12" style="margin-top: 15px;">
                <?php if (isset($print) && $print == 1){ ?>
                <button onclick="printDiv('ledgPrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print"></i> Print Now</button>
                <?php } ?>
                <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('ledgPrint','capital')"><i class="fa fa-file-pdf-o"></i> Download PDF </button>
                <?php } ?>
                <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('ledgPrint','capital')"><i class="fa fa-file-excel-o"></i> Download CSV</button>
                <?php } ?>
            </div>
        </div>
    </div>
    <!-- /.box-body -->
</div>
<!-- /.box -->

<div id="ajaxCapitalPrint" style="display: none;">
    <?php echo view('Admin/Capital/print_table', [
        'capital' => $capital ?? [],
        'st_date' => $st_date ?? '',
        'en_date' => $en_date ?? '',
        'shopId' => $shopId ?? ($this->session->shopId ?? 0),
    ]); ?>
</div>

