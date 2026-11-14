<?php
$invLogo = invoice_logo();
$logoImg = no_image_view('/uploads/schools/' . $invLogo, '/uploads/schools/no_image_logo.jpg', $invLogo);

$st_date = isset($st_date) ? $st_date : '';
$en_date = isset($en_date) ? $en_date : '';
$event_type = isset($event_type) ? $event_type : '';
$eventsList = isset($result) ? $result : [];
?>
<div class="box">
    <!-- /.box-header -->
    <div class="box-body">
        <table class="table table-bordered table-striped" id="example1">
            <thead>
            <tr>
                <th>SL</th>
                <th>Date</th>
                <th>Event Type</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $i = 1;
            foreach ($eventsList as $val) {
                $url = ''; $urlFlow = ''; $eventType = '';
                if (!empty($val->trans_id)){
                    $url = base_url('Admin/Transaction/read/'.$val->trans_id);
                    $urlFlow = base_url('Admin/Transaction/transaction_flow/'.$val->trans_id);
                    $eventType = 'Transaction';
                } elseif (!empty($val->sales_id)){
                    $invId = get_data_by_id('invoice_id','sales','sales_id',$val->sales_id);
                    $url = base_url('Admin/Invoice/view/'.$invId);
                    $urlFlow = base_url('Admin/Sales/transaction_flow/'.$val->sales_id);
                    $eventType = 'Sales';
                } elseif (!empty($val->purchase_id)){
                    $url = base_url('Admin/Purchase/view/'.$val->purchase_id);
                    $urlFlow = base_url('Admin/Purchase/transaction_flow/'.$val->purchase_id);
                    $eventType = 'Purchase';
                } elseif (!empty($val->dep_id)){
                    $url = '';
                    $urlFlow = base_url('Admin/Bank_deposit/transaction_flow/'.$val->dep_id);
                    $eventType = 'Bank Deposit';
                } elseif (!empty($val->wthd_id)){
                    $url = '';
                    $urlFlow = base_url('Admin/Bank_withdraw/transaction_flow/'.$val->wthd_id);
                    $eventType = 'Bank Withdraw';
                }
            ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= invoiceDateFormat($val->createdDtm) ?></td>
                    <td><?= $eventType;?></td>
                    <td>
                        <?php if (!empty($urlFlow)){ ?>
                            <a href="<?= $urlFlow;?>" class="btn btn-success btn-xs">Transaction Flow </a>
                        <?php } if (!empty($url)){ ?>
                            <a href="<?= $url;?>" class="btn btn-xs btn-primary">Detail</a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
                <th>SL</th>
                <th>Date</th>
                <th>Event Type</th>
                <th>Action</th>
            </tr>
            <tr>
                <th colspan="2" style="text-align: right;">Total Events:</th>
                <th colspan="2">= <?php echo count($eventsList); ?></th>
            </tr>
            </tfoot>
        </table>

        <div class="row no-print" style="margin-top: 15px;">
            <div class="col-xs-12">
                <button type="button" onclick="printDiv('ledgPrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print"></i> Print Now</button>
                <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('ledgPrint','transaction_events')"><i class="fa fa-file-pdf-o"></i> Download PDF </button>
                <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('ledgPrint','transaction_events')"><i class="fa fa-file-excel-o"></i> Download CSV</button>
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
                <h3 style="margin:0; text-transform: capitalize;">Transaction Events</h3>
                <?php if (!empty($event_type)) { ?>
                    <div style="font-size: 13px;"><strong>Event Type:</strong> <?php echo ucfirst($event_type); ?></div>
                <?php } ?>
                <?php if (!empty($st_date) || !empty($en_date)) { ?>
                    <div style="font-size: 13px;"><strong>Period:</strong> <?php echo !empty($st_date) ? $st_date : '...'; ?> to <?php echo !empty($en_date) ? $en_date : '...'; ?></div>
                <?php } ?>
                <table class="table table-bordered table-striped" style="width:auto; display:inline-table; margin-top:5px;" id="TFtable">
                    <tr>
                        <td>Total Events:</td>
                        <td><?php echo count($eventsList); ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-12">
                <table class="table table-bordered table-striped text-capitalize">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Date</th>
                        <th>Event Type</th>
                        <th>Reference</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $j = 1;
                    foreach ($eventsList as $val) {
                        $pEventType = '---';
                        $pRef = '---';
                        if (!empty($val->trans_id)){
                            $pEventType = 'Transaction';
                            $pRef = 'Trans #' . $val->trans_id;
                        } elseif (!empty($val->sales_id)){
                            $pEventType = 'Sales';
                            $invId = get_data_by_id('invoice_id','sales','sales_id',$val->sales_id);
                            $pRef = !empty($invId) ? 'Invoice #' . $invId : 'Sales #' . $val->sales_id;
                        } elseif (!empty($val->purchase_id)){
                            $pEventType = 'Purchase';
                            $pRef = 'Purchase #' . $val->purchase_id;
                        } elseif (!empty($val->dep_id)){
                            $pEventType = 'Bank Deposit';
                            $pRef = 'Deposit #' . $val->dep_id;
                        } elseif (!empty($val->wthd_id)){
                            $pEventType = 'Bank Withdraw';
                            $pRef = 'Withdraw #' . $val->wthd_id;
                        }
                    ?>
                        <tr>
                            <td><?php echo $j++; ?></td>
                            <td><?php echo invoiceDateFormat($val->createdDtm); ?></td>
                            <td><?php echo $pEventType; ?></td>
                            <td><?php echo $pRef; ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Date</th>
                        <th>Event Type</th>
                        <th>Reference</th>
                    </tr>
                    <tr>
                        <th colspan="2" style="text-align: right;">Total Events:</th>
                        <th colspan="2">= <?php echo count($eventsList); ?></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <!-- /.box-body -->
</div>
<!-- /.box -->
<div id="ajaxTransactionEventsPrint" style="display:none;">
    <?php echo view('Admin/Transaction_events/print_table', [
        'result' => $eventsList,
        'st_date' => $st_date,
        'en_date' => $en_date,
        'event_type' => $event_type,
    ]); ?>
</div>

