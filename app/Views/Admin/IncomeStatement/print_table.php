<?php
// invoice logo (falls back to the shop logo, then to the built-in placeholder)
$invLogo = invoice_logo();
$logoImg = no_image_view('/uploads/schools/' . $invLogo, '/uploads/schools/no_image_logo.jpg', $invLogo);
$currentShopId = isset($id) ? $id : (isset($shopId) ? $shopId : (isset($_SESSION['shopId']) ? $_SESSION['shopId'] : 0));
$shopRow = DB()->table('shops')->where('sch_id', $currentShopId)->get()->getRow();

$st_date = isset($st_date) ? $st_date : '';
$en_date = isset($en_date) ? $en_date : '';
$purchaseAmount = isset($purchaseAmount) ? $purchaseAmount : 0;
$sales = isset($sales) ? $sales : 0;
$profit = isset($profit) ? $profit : 0;
$expenses = isset($expenses) ? $expenses : 0;
$profitLoss = isset($profitLoss) ? $profitLoss : ($profit - $expenses);
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
    <h3 style="margin:0; text-transform: capitalize;">Income Statement</h3>
    <?php if (!empty($st_date) || !empty($en_date)) { ?>
        <div style="font-size: 13px;"><strong>Report Period:</strong> <?php echo !empty($st_date) ? invoiceDateFormat($st_date) : '...'; ?> to <?php echo !empty($en_date) ? invoiceDateFormat($en_date) : '...'; ?></div>
    <?php } ?>
    <table class="table table-bordered table-striped" style="width:auto; display:inline-table; margin-top:5px;" id="TFtable">
        <tr>
            <td>Total Sales:</td>
            <td><strong><?php echo showWithCurrencySymbol($sales); ?></strong></td>
            <td>Total Purchase:</td>
            <td><strong><?php echo showWithCurrencySymbol($purchaseAmount); ?></strong></td>
            <td>Net Profit / Loss:</td>
            <td>
                <?php if ($profitLoss >= 0) { ?>
                    <strong style="color: green;"><?php echo showWithCurrencySymbol($profitLoss); ?></strong>
                <?php } else { ?>
                    <strong style="color: red;"><?php echo showWithCurrencySymbol($profitLoss); ?></strong>
                <?php } ?>
            </td>
        </tr>
    </table>
</div>
<div class="col-md-12">
    <div class="row">
        <div class="col-xs-6">
            <h4>Trading Activity</h4>
            <table class="table table-bordered table-striped text-capitalize">
                <thead>
                <tr>
                    <th>Title</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Purchase</td>
                    <td style="text-align: right;"><?php echo showWithCurrencySymbol($purchaseAmount); ?></td>
                </tr>
                <tr>
                    <td>Sales</td>
                    <td style="text-align: right;"><?php echo showWithCurrencySymbol($sales); ?></td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="col-xs-6">
            <h4>Operating Summary</h4>
            <table class="table table-bordered table-striped text-capitalize">
                <thead>
                <tr>
                    <th>Title</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Revenue (Profit + Service Charge + Other Income)</td>
                    <td style="text-align: right;"><?php echo showWithCurrencySymbol($profit); ?></td>
                </tr>
                <tr>
                    <td>Expenses</td>
                    <td style="text-align: right;"><?php echo showWithCurrencySymbol($expenses); ?></td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                    <th>Net Profit / Loss</th>
                    <th style="text-align: right;">
                        <?php if ($profitLoss >= 0) { ?>
                            <span style="color: green; font-weight: bold;"><?php echo showWithCurrencySymbol($profitLoss); ?></span>
                        <?php } else { ?>
                            <span style="color: red; font-weight: bold;"><?php echo showWithCurrencySymbol($profitLoss); ?></span>
                        <?php } ?>
                    </th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

