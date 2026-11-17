<?php
// invoice logo (falls back to the shop logo, then to the built-in placeholder)
$invLogo = invoice_logo();
$logoImg = no_image_view('/uploads/schools/' . $invLogo, '/uploads/schools/no_image_logo.jpg', $invLogo);
$currentShopId = isset($id) ? $id : (isset($_SESSION['shopId']) ? $_SESSION['shopId'] : 0);
$shopRow = DB()->table('shops')->where('sch_id', $currentShopId)->get()->getRow();

$st_date = isset($st_date) ? $st_date : '';
$en_date = isset($en_date) ? $en_date : '';

$start_date = !empty($st_date) ? $st_date : 0;
$end_date = !empty($en_date) ? $en_date : 0;

$serviceChargeBalance = isset($shopRow->service_charge) ? $shopRow->service_charge : get_data_by_id('service_charge', 'shops', 'sch_id', $currentShopId);
$rowsData = isset($ledger_service_charge) ? $ledger_service_charge : [];
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
    <h3 style="margin:0; text-transform: capitalize;">Ledger Service Charge</h3>
    <?php if (!empty($st_date) || !empty($en_date)) { ?>
        <div style="font-size: 13px;">Period: <?php echo !empty($st_date) ? $st_date : '...'; ?> to <?php echo !empty($en_date) ? $en_date : '...'; ?></div>
    <?php } ?>
    <table class="table table-bordered table-striped" style="width:auto; display:inline-table; margin-top:5px;" id="TFtable">
        <tr>
            <td>Total Debit:</td>
            <td><?php echo showWithCurrencySymbol(get_total_nogodBalance('ledger_service_charge', 'amount', 'Dr.', $start_date, $end_date)); ?></td>
            <td>Total Credit:</td>
            <td><?php echo showWithCurrencySymbol(get_total_nogodBalance('ledger_service_charge', 'amount', 'Cr.', $start_date, $end_date)); ?></td>
            <td>Balance:</td>
            <td><?php echo showWithCurrencySymbol($serviceChargeBalance); ?></td>
        </tr>
    </table>
</div>
<div class="col-md-12" >
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>Id</th>
            <th>Date</th>
            <th>Particulars</th>
            <th>Service Id</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Balance</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $totalRows = count($rowsData) - 1;
        for ($i = 0; $i <= $totalRows; $i++) {
            $particulars = ($rowsData[$i]->particulars == NULL) ? "Payment" : $rowsData[$i]->particulars;
            $amountCr = ($rowsData[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($rowsData[$i]->amount);
            $amountDr = ($rowsData[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($rowsData[$i]->amount);
            $serviceInv = !empty($rowsData[$i]->service_invoice_id) ? 'SINV_' . $rowsData[$i]->service_invoice_id : '---';
            ?>
            <tr>
                <td><?= $rowsData[$i]->ledger_service_charge_id ?></td>
                <td><?= $rowsData[$i]->createdDtm ?></td>
                <td><?= $particulars ?></td>
                <td><?= $serviceInv ?></td>
                <td><?= $amountDr ?></td>
                <td><?= $amountCr ?></td>
                <td><?= showWithCurrencySymbol($rowsData[$i]->r_balance) ?></td>
            </tr>

        <?php } ?>

        </tbody>
        <tfoot>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th>Total</th>
            <th>= <?php echo showWithCurrencySymbol(get_total_nogodBalance('ledger_service_charge', 'amount', 'Dr.', $start_date, $end_date)); ?></th>
            <th>= <?php echo showWithCurrencySymbol(get_total_nogodBalance('ledger_service_charge', 'amount', 'Cr.', $start_date, $end_date)); ?></th>
            <th>= <?php echo showWithCurrencySymbol($serviceChargeBalance); ?></th>
        </tr>
        </tfoot>
    </table>
</div>

