<?php
// invoice logo (falls back to the shop logo, then to the built-in placeholder)
$invLogo = invoice_logo();
$logoImg = no_image_view('/uploads/schools/' . $invLogo, '/uploads/schools/no_image_logo.jpg', $invLogo);
$shopRow = DB()->table('shops')->where('sch_id', isset($_SESSION['shopId']) ? $_SESSION['shopId'] : 0)->get()->getRow();

$loan_pro_id = isset($loan_pro_id) ? $loan_pro_id : '';
$st_date = isset($st_date) ? $st_date : '';
$en_date = isset($en_date) ? $en_date : '';

$providerName = !empty($loan_pro_id) ? get_data_by_id('name', 'loan_provider', 'loan_pro_id', $loan_pro_id) : '';
$providerBalance = !empty($loan_pro_id) ? get_data_by_id('balance', 'loan_provider', 'loan_pro_id', $loan_pro_id) : 0;
$start_date = !empty($st_date) ? $st_date : 0;
$end_date = !empty($en_date) ? $en_date : 0;
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
    <h3 style="margin:0; text-transform: capitalize;">Account Head: <?php echo $providerName; ?></h3>
    <?php if (!empty($st_date) || !empty($en_date)) { ?>
        <div style="font-size: 13px;">Period: <?php echo !empty($st_date) ? $st_date : '...'; ?> to <?php echo !empty($en_date) ? $en_date : '...'; ?></div>
    <?php } ?>
    <?php if (!empty($loan_pro_id)) { ?>
        <table class="table table-bordered table-striped" style="width:auto; display:inline-table; margin-top:5px;" id="TFtable">
            <tr>
                <td>Total Get:</td>
                <td><?php echo showWithCurrencySymbol(get_total('ledger_loan', 'amount', 'Dr.', 'loan_pro_id', $loan_pro_id, $start_date, $end_date)); ?></td>
                <td>Total Pay:</td>
                <td><?php echo showWithCurrencySymbol(get_total('ledger_loan', 'amount', 'Cr.', 'loan_pro_id', $loan_pro_id, $start_date, $end_date)); ?></td>
                <td>Balance:</td>
                <td><?php echo showWithCurrencySymbol($providerBalance); ?></td>
            </tr>
        </table>
    <?php } ?>
</div>
<div class="col-md-12" >
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>Id</th>
            <th>Date</th>
            <th>Particulars</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Balance</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $totalRows = count($result) - 1;
        for ($i = 0; $i <= $totalRows; $i++) {
            $particulars = ($result[$i]->particulars == NULL) ? "Pay due" : $result[$i]->particulars;
            $amountCr = ($result[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($result[$i]->amount);
            $amountDr = ($result[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($result[$i]->amount);
            ?>
            <tr>
                <td><?= $result[$i]->ledg_loan_id ?></td>
                <td><?= $result[$i]->createdDtm ?></td>
                <td><?= $particulars ?></td>
                <td><?= $amountDr ?></td>
                <td><?= $amountCr ?></td>
                <td><?= showWithCurrencySymbol($result[$i]->r_balance) ?></td>
            </tr>

        <?php } ?>

        </tbody>

    </table>
</div>
