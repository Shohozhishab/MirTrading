<?php
// invoice logo (falls back to the shop logo, then to the built-in placeholder)
$invLogo = invoice_logo();
$logoImg = no_image_view('/uploads/schools/' . $invLogo, '/uploads/schools/no_image_logo.jpg', $invLogo);
$currentShopId = isset($shopId) ? $shopId : (isset($_SESSION['shopId']) ? $_SESSION['shopId'] : 0);
$shopRow = DB()->table('shops')->where('sch_id', $currentShopId)->get()->getRow();

$employee_id = isset($employee_id) ? $employee_id : '';
$st_date = isset($st_date) ? $st_date : '';
$en_date = isset($en_date) ? $en_date : '';

$employeeName = !empty($employee_id) ? get_data_by_id('name', 'employee', 'employee_id', $employee_id) : '';
$employeeBalance = !empty($employee_id) ? get_data_by_id('balance', 'employee', 'employee_id', $employee_id) : 0;
$start_date = !empty($st_date) ? $st_date : 0;
$end_date = !empty($en_date) ? $en_date : 0;
$result = isset($result) ? $result : [];
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
    <h3 style="margin:0; text-transform: capitalize;">Employee: <?php echo $employeeName; ?></h3>
    <?php if (!empty($st_date) || !empty($en_date)) { ?>
        <div style="font-size: 13px;">Period: <?php echo !empty($st_date) ? $st_date : '...'; ?> to <?php echo !empty($en_date) ? $en_date : '...'; ?></div>
    <?php } ?>
    <?php if (!empty($employee_id)) { ?>
        <table class="table table-bordered table-striped" style="width:auto; display:inline-table; margin-top:5px;" id="TFtable">
            <tr>
                <td>Total Paid:</td>
                <td><?php echo showWithCurrencySymbol(get_total('ledger_employee', 'amount', 'Dr.', 'employee_id', $employee_id, $start_date, $end_date)); ?></td>
                <td>Total Received:</td>
                <td><?php echo showWithCurrencySymbol(get_total('ledger_employee', 'amount', 'Cr.', 'employee_id', $employee_id, $start_date, $end_date)); ?></td>
                <td>Balance:</td>
                <td><?php echo showWithCurrencySymbol($employeeBalance); ?></td>
            </tr>
        </table>
    <?php } ?>
</div>
<div class="col-md-12">
    <table class="table table-bordered table-striped text-capitalize">
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
        $totalRows = count($result);
        for ($i = 0; $i < $totalRows; $i++) {
            $particulars = ($result[$i]->particulars == NULL) ? "Transaction" : $result[$i]->particulars;
            $amountCr = ($result[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($result[$i]->amount);
            $amountDr = ($result[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($result[$i]->amount);
            ?>
            <tr>
                <td><?= $result[$i]->ledg_emp_id ?></td>
                <td><?= $result[$i]->createdDtm ?></td>
                <td><?= $particulars ?></td>
                <td><?= $amountDr ?></td>
                <td><?= $amountCr ?></td>
                <td><?= showWithCurrencySymbol($result[$i]->r_balance) ?></td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot>
        <tr>
            <th>Id</th>
            <th>Date</th>
            <th>Particulars</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Balance</th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th>Total</th>
            <th>= <?= showWithCurrencySymbol(get_total('ledger_employee', 'amount', 'Dr.', 'employee_id', $employee_id, $start_date, $end_date)) ?></th>
            <th>= <?= showWithCurrencySymbol(get_total('ledger_employee', 'amount', 'Cr.', 'employee_id', $employee_id, $start_date, $end_date)) ?></th>
            <th><?= showWithCurrencySymbol($employeeBalance) ?></th>
        </tr>
        </tfoot>
    </table>
</div>

