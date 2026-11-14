<?php
// invoice logo (falls back to the shop logo, then to the built-in placeholder)
$invLogo = invoice_logo();
$logoImg = no_image_view('/uploads/schools/' . $invLogo, '/uploads/schools/no_image_logo.jpg', $invLogo);
$currentShopId = isset($shopId) ? $shopId : (isset($_SESSION['shopId']) ? $_SESSION['shopId'] : 0);
$shopRow = DB()->table('shops')->where('sch_id', $currentShopId)->get()->getRow();

$st_date = isset($st_date) ? $st_date : '';
$en_date = isset($en_date) ? $en_date : '';
$ledger_nagodan_data = isset($ledger_nagodan_data) ? $ledger_nagodan_data : [];

$start_date = !empty($st_date) ? $st_date : 0;
$end_date = !empty($en_date) ? $en_date : 0;
$cashBalance = isset($shopRow->cash) ? $shopRow->cash : 0;
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
    <h3 style="margin:0; text-transform: capitalize;">Cash Ledger</h3>
    <?php if (!empty($st_date) || !empty($en_date)) { ?>
        <div style="font-size: 13px;">Period: <?php echo !empty($st_date) ? $st_date : '...'; ?> to <?php echo !empty($en_date) ? $en_date : '...'; ?></div>
    <?php } else { ?>
        <div style="font-size: 13px;">Period: All data show</div>
    <?php } ?>
    <table class="table table-bordered table-striped" style="width:auto; display:inline-table; margin-top:5px;" id="TFtable">
        <tr>
            <td>Total Debit:</td>
            <td><?php echo showWithCurrencySymbol(get_total_nogodBalance('ledger_nagodan', 'amount', 'Dr.', $start_date, $end_date)); ?></td>
            <td>Total Credit:</td>
            <td><?php echo showWithCurrencySymbol(get_total_nogodBalance('ledger_nagodan', 'amount', 'Cr.', $start_date, $end_date)); ?></td>
            <td>Cash Balance:</td>
            <td><?php echo showWithCurrencySymbol($cashBalance); ?></td>
        </tr>
    </table>
</div>
<div class="col-md-12">
    <table class="table table-bordered table-striped text-capitalize">
        <thead>
        <tr>
            <th>Id</th>
            <th>Date</th>
            <th>Particulars</th>
            <th>Trangaction Id</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Balance</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $totalRows = count($ledger_nagodan_data) - 1;
        for ($i = 0; $i <= $totalRows; $i++) {
            $particulars = ($ledger_nagodan_data[$i]->particulars == NULL) ? "Payment" : $ledger_nagodan_data[$i]->particulars;
            $amountCr = ($ledger_nagodan_data[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($ledger_nagodan_data[$i]->amount);
            $amountDr = ($ledger_nagodan_data[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($ledger_nagodan_data[$i]->amount);
            $transId = !empty($ledger_nagodan_data[$i]->trans_id) ? $ledger_nagodan_data[$i]->trans_id : '---';
            ?>
            <tr>
                <td><?= $ledger_nagodan_data[$i]->ledg_nagodan_id ?></td>
                <td><?= $ledger_nagodan_data[$i]->createdDtm ?></td>
                <td><?= $particulars ?></td>
                <td><?= $transId ?></td>
                <td><?= $amountDr ?></td>
                <td><?= $amountCr ?></td>
                <td><?= showWithCurrencySymbol($ledger_nagodan_data[$i]->r_balance) ?></td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th>Total</th>
            <th>= <?php echo showWithCurrencySymbol(get_total_nogodBalance('ledger_nagodan', 'amount', 'Dr.', $start_date, $end_date)); ?></th>
            <th>= <?php echo showWithCurrencySymbol(get_total_nogodBalance('ledger_nagodan', 'amount', 'Cr.', $start_date, $end_date)); ?></th>
            <th>= <?php echo showWithCurrencySymbol($cashBalance); ?></th>
        </tr>
        </tfoot>
    </table>
</div>
