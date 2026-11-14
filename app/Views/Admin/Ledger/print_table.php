<?php
// invoice logo (falls back to the shop logo, then to the built-in placeholder)
$invLogo = invoice_logo();
$logoImg = no_image_view('/uploads/schools/' . $invLogo, '/uploads/schools/no_image_logo.jpg', $invLogo);
$shopRow = DB()->table('shops')->where('sch_id', isset($_SESSION['shopId']) ? $_SESSION['shopId'] : 0)->get()->getRow();

$customer_id = isset($customer_id) ? $customer_id : '';
$st_date = isset($st_date) ? $st_date : '';
$en_date = isset($en_date) ? $en_date : '';

$customerName = !empty($customer_id) ? get_data_by_id('customer_name', 'customers', 'customer_id', $customer_id) : '';
$customerBalance = !empty($customer_id) ? get_data_by_id('balance', 'customers', 'customer_id', $customer_id) : 0;
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
    <h3 style="margin:0; text-transform: capitalize;">Customer: <?php echo $customerName; ?></h3>
    <?php if (!empty($st_date) || !empty($en_date)) { ?>
        <div style="font-size: 13px;">Period: <?php echo !empty($st_date) ? $st_date : '...'; ?> to <?php echo !empty($en_date) ? $en_date : '...'; ?></div>
    <?php } ?>
    <?php if (!empty($customer_id)) { ?>
        <table class="table table-bordered table-striped" style="width:auto; display:inline-table; margin-top:5px;" id="TFtable">
            <tr>
                <td>Total Buy:</td>
                <td><?php echo showWithCurrencySymbol(get_total('ledger', 'amount', 'Dr.', 'customer_id', $customer_id, $start_date, $end_date)); ?></td>
                <td>Total Pay:</td>
                <td><?php echo showWithCurrencySymbol(get_total('ledger', 'amount', 'Cr.', 'customer_id', $customer_id, $start_date, $end_date)); ?></td>
                <td>Last Balance:</td>
                <td><?php echo showWithCurrencySymbol($customerBalance); ?></td>
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
            $bankName = led_id_by_bank_name($result[$i]->invoice_id);
            $checkNumber = led_id_by_chaque_number($result[$i]->invoice_id);
            ?>
            <tr>
                <td><?= $result[$i]->ledg_id ?></td>
                <td><?= $result[$i]->createdDtm ?></td>
                <td><?= $particulars ?><br><small><?= $bankName. $checkNumber ?></small></td>
                <td><?= $amountDr ?></td>
                <td><?= $amountCr ?></td>
                <td><?= showWithCurrencySymbol($result[$i]->r_balance) ?></td>
            </tr>

        <?php } ?>

        </tbody>

    </table>
</div>
