<?php
// invoice logo (falls back to the shop logo, then to the built-in placeholder)
$invLogo = invoice_logo();
$logoImg = no_image_view('/uploads/schools/' . $invLogo, '/uploads/schools/no_image_logo.jpg', $invLogo);
$shopRow = DB()->table('shops')->where('sch_id', isset($_SESSION['shopId']) ? $_SESSION['shopId'] : 0)->get()->getRow();

$st_date = isset($st_date) ? $st_date : '';
$en_date = isset($en_date) ? $en_date : '';

$start_date = !empty($st_date) ? $st_date : 0;
$end_date = !empty($en_date) ? $en_date : 0;
$stockBalance = isset($shopRow->stockAmount) ? $shopRow->stockAmount : 0;
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
    <h3 style="margin:0; text-transform: capitalize;">Stock Ledger</h3>
    <?php if (!empty($st_date) || !empty($en_date)) { ?>
        <div style="font-size: 13px;">Period: <?php echo !empty($st_date) ? $st_date : '...'; ?> to <?php echo !empty($en_date) ? $en_date : '...'; ?></div>
    <?php } ?>
    <table class="table table-bordered table-striped" style="width:auto; display:inline-table; margin-top:5px;" id="TFtable">
        <tr>
            <td>Total Debit:</td>
            <td><?php echo showWithCurrencySymbol(get_total_nogodBalance('ledger_stock', 'amount', 'Dr.', $start_date, $end_date)); ?></td>
            <td>Total Credit:</td>
            <td><?php echo showWithCurrencySymbol(get_total_nogodBalance('ledger_stock', 'amount', 'Cr.', $start_date, $end_date)); ?></td>
            <td>Stock Balance:</td>
            <td><?php echo showWithCurrencySymbol($stockBalance); ?></td>
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
            <th>Memo</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Balance</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $totalRows = count($ledger_stock) - 1;
        for ($i = 0; $i <= $totalRows; $i++) {
            $particulars = ($ledger_stock[$i]->particulars == NULL) ? "Payment" : $ledger_stock[$i]->particulars;
            $amountCr = ($ledger_stock[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($ledger_stock[$i]->amount);
            $amountDr = ($ledger_stock[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($ledger_stock[$i]->amount);
            $memo = !empty($ledger_stock[$i]->invoice_id) ? 'INV_' . $ledger_stock[$i]->invoice_id : (!empty($ledger_stock[$i]->purchase_id) ? 'PURS_' . $ledger_stock[$i]->purchase_id : (!empty($ledger_stock[$i]->rtn_sale_id) ? 'RSAL_' . $ledger_stock[$i]->rtn_sale_id : '---'));
            ?>
            <tr>
                <td><?= $ledger_stock[$i]->stock_id ?></td>
                <td><?= $ledger_stock[$i]->createdDtm ?></td>
                <td><?= $particulars ?></td>
                <td><?= $memo ?></td>
                <td><?= $amountDr ?></td>
                <td><?= $amountCr ?></td>
                <td><?= showWithCurrencySymbol($ledger_stock[$i]->r_balance) ?></td>
            </tr>

        <?php } ?>

        </tbody>
        <tfoot>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th>Total</th>
            <th>= <?php echo showWithCurrencySymbol(get_total_nogodBalance('ledger_stock', 'amount', 'Dr.', $start_date, $end_date)); ?></th>
            <th>= <?php echo showWithCurrencySymbol(get_total_nogodBalance('ledger_stock', 'amount', 'Cr.', $start_date, $end_date)); ?></th>
            <th>= <?php echo showWithCurrencySymbol($stockBalance); ?></th>
        </tr>
        </tfoot>
    </table>
</div>

