<?php
$totalCommissionAmount = 0;
if (isset($commission) && is_array($commission)) {
    foreach ($commission as $val) {
        $totalCommissionAmount += (float)$val->commission_amount;
    }
}
?>
<div class="box">
    <div class="box-body">
        <table id="example1" class="table table-bordered table-striped text-capitalize">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Commission</th>
                    <th>Commission Amount</th>
                    <th>Date</th>
                    <th>Invoice</th>
                </tr>
            </thead>
            <tbody>
            <?php $start = 1;
            if (isset($commission) && is_array($commission)) {
                foreach ($commission as $val) { ?>
                    <tr>
                        <td width="80px"><?php echo $start++; ?></td>
                        <td><?php echo get_data_by_id('name','affiliate_user','affiliate_user_id',$val->affiliate_user_id); ?></td>
                        <td><?php echo $val->commission; ?> %</td>
                        <td><?php echo showWithCurrencySymbol($val->commission_amount); ?></td>
                        <td><?php echo $val->date; ?></td>
                        <td><?php $invoice_id = get_data_by_id('invoice_id','sales','sales_id',$val->sales_id);?>
                            <?php if (isset($read) && $read == 1){ ?>
                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Invoice_ajax/view/'.$invoice_id); ?>','<?php echo '/Admin/Invoice/view/'.$invoice_id; ?>')" class="btn btn-warning btn-xs">View</a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php }
            } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-right">Total:</th>
                    <th><?= showWithCurrencySymbol($totalCommissionAmount); ?></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>

        <div class="row no-print">
            <div class="col-xs-12" style="margin-top: 15px;">
                <?php if (isset($print) && $print == 1){ ?>
                <button onclick="printDiv('ledgPrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print"></i> Print Now</button>
                <?php } ?>
                <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('ledgPrint','affiliateCommission')"><i class="fa fa-file-pdf-o"></i> Download PDF </button>
                <?php } ?>
                <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('ledgPrint','affiliateCommission')"><i class="fa fa-file-excel-o"></i> Download CSV</button>
                <?php } ?>
            </div>
        </div>
    </div>
    <!-- /.box-body -->
</div>
<!-- /.box -->

<div id="ajaxAffiliateCommissionPrint" style="display: none;">
    <?php echo view('Admin/Affiliate_commission/print_table', [
        'commission' => $commission ?? [],
        'affiliate_user_id' => $affiliate_user_id ?? '',
        'st_date' => $st_date ?? '',
        'en_date' => $en_date ?? '',
        'shopId' => $shopId ?? ($this->session->shopId ?? 0),
    ]); ?>
</div>

