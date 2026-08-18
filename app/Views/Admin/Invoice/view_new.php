<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Invoice <small>Invoice</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Invoice</li>
        </ol>

        <div class="row">
            <div class="col-xs-12" style="margin-top: 15px;">
                <?php echo $menu; ?>
            </div>
        </div>
    </section>

    <style>

    </style>

    <section class="content">
        <div class="invoice-page ">
            <div class="invoice-header">
                <!-- BILL TO -->
                <div class="bill-to">
                    <p class="invoice-label"> BILL TO </p>
                    <h3> <?= ($invoiceData->customer_id == 0) ? $invoiceData->customer_name : get_data_by_id('customer_name', 'customers', 'customer_id', $invoiceData->customer_id); ?> </h3>
                    <p>
                        <?php if (!empty($invoiceData->customer_id)) { ?>
                            Phone: <?= showWithPhoneNummberCountryCode(get_data_by_id('mobile', 'customers', 'customer_id', $invoiceData->customer_id)) ?>
                            <br>
                            Address: <?= get_data_by_id('address', 'customers', 'customer_id', $invoiceData->customer_id) ?>
                        <?php } ?>
                    </p>
                </div>


                <!-- INVOICE INFORMATION -->
                <div class="invoice-info">

                    <h1>
                        INVOICE
                    </h1>

                    <div class="invoice-row">
                        <span>INVOICE NUMBER</span>
                        <strong>Inv_<?= $invoiceId ?></strong>
                    </div>

                    <div class="invoice-row">
                        <span>DATE OF ISSUE</span>
                        <strong><?= invoiceDateFormat($invoiceData->createdDtm); ?></strong>
                    </div>

                    <div class="invoice-row due-date">
                        <span>DUE DATE</span>
                        <strong><?= invoiceDateFormat($invoiceData->createdDtm); ?></strong>
                    </div>

                    <div class="status-wrapper">
                        <div class="status">
                            <span class="status-dot"></span>
                            Pending
                        </div>
                    </div>

                </div>

            </div>

            <table class="invoice-table text-capitalize">

                <thead>
                <tr>
                    <th>NAME</th>
                    <th>PRICE</th>
                    <th>QTY</th>
                    <th>AMOUNT</th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($invoiceItame as $row) {
                    $catId = get_data_by_id('prod_cat_id', 'products', 'prod_id', $row->prod_id);
                    $parent_pro_cat = get_data_by_id('parent_pro_cat', 'product_category', 'prod_cat_id', $catId);
                    $category = get_data_by_id('product_category', 'product_category', 'prod_cat_id', $parent_pro_cat);
                    $subCategory = get_data_by_id('product_category', 'product_category', 'prod_cat_id', $catId);
                    $productName = get_data_by_id('name', 'products', 'prod_id', $row->prod_id);
                    $unit = productIdByDefaultStoreUnit($row->prod_id);
                    ?>
                    <tr>
                        <td><?= $productName . '<br> <small>(' . $category . ' > ' . $subCategory . ')</small>'; ?></td>
                        <td><?= showWithCurrencySymbol(unitOrBasePriceByUnitPrice($unit, $row->price)); ?></td>
                        <td><?= unitOrQtyByUnitQty($unit, $row->quantity); ?>/<?php echo showUnitName($unit) ?></td>
                        <td><?= showWithCurrencySymbol($row->total_price); ?></td>
                    </tr>
                <?php } ?>


                </tbody>

            </table>

            <div class="totals">

                <div class="total-row">
                    <div class="total-label">
                        Subtotal
                    </div>

                    <div class="total-value">
                        <?= showWithCurrencySymbol($invoiceData->amount); ?>
                        <input type="hidden" id="totalAmount" value="<?= $invoiceData->amount ?>">
                    </div>
                </div>
                <?php if (!empty($invoiceData->entire_sale_discount)) { ?>
                    <div class="total-row">
                        <div class="total-label">
                            Discount (<?= $invoiceData->entire_sale_discount ?>%)
                            <input type="hidden" id="discount" value="<?= $invoiceData->entire_sale_discount ?>">
                        </div>

                        <div class="total-value">
                            <span id="discountAmount"></span>
                        </div>
                    </div>
                <?php } ?>
                <?php if (!empty($invoiceData->vat)) { ?>
                    <div class="total-row">
                        <div class="total-label">
                            Vat (<?= $invoiceData->vat ?>%)
                            <input type="hidden" id="vat" value="<?= $invoiceData->entire_sale_discount ?>">
                        </div>

                        <div class="total-value">
                            <span id="vatAmount"></span>
                        </div>
                    </div>
                <?php } ?>
                <?php if (!empty($invoiceData->final_amount)) { ?>
                    <div class="total-row">
                        <div class="total-label">
                            Subtotal
                        </div>

                        <div class="total-value">
                            <?= showWithCurrencySymbol($invoiceData->final_amount) ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if (!empty($invoiceData->nagad_paid)) { ?>
                    <div class="total-row">
                        <div class="total-label">
                            Cash Pay
                        </div>

                        <div class="total-value">
                            <?= showWithCurrencySymbol($invoiceData->nagad_paid) ?>
                        </div>
                    </div>
                <?php } ?>

                <?php if (!empty($invoiceData->bank_id)) {
                    $bankDetail = getTotalRow('bank', 'bank_id', $invoiceData->bank_id); ?>
                    <div class="total-row">
                        <div class="total-label">
                            Bank Pay<br>
                            <span class="tooltip-text"><?= $bankDetail->name ?>--<?= $bankDetail->account_no ?></span>
                        </div>
                        <div class="total-value">
                            <?= showWithCurrencySymbol($invoiceData->bank_paid) ?>
                        </div>
                    </div>
                <?php } ?>

                <?php if (!empty($invoiceData->chaque_id)) { ?>
                    <div class="total-row">
                        <div class="total-label">
                            Cheque Pay
                        </div>

                        <div class="total-value">
                            <?= showWithCurrencySymbol($invoiceData->chaque_paid) ?>
                        </div>
                    </div>
                <?php } ?>
                <div class="total-row">
                    <div class="total-label">
                        Today Due
                    </div>

                    <div class="total-value">
                        <?= showWithCurrencySymbol($invoiceData->due) ?>
                    </div>
                </div>

                <?php if (!empty($invoiceData->previous_due)) { ?>
                    <div class="total-row">
                        <div class="total-label">
                            Previous <?= ($invoiceData->previous_due > 0) ? 'Due' : 'Balance'; ?>
                        </div>

                        <div class="total-value">
                            <?= showWithCurrencySymbol($invoiceData->previous_due) ?>
                        </div>
                    </div>

                    <div class="total-row">
                        <div class="total-label">
                            Total <?= ($invoiceData->total > 0) ? 'Due' : 'Balance'; ?>
                        </div>

                        <div class="total-value">
                            <?= showWithCurrencySymbol($invoiceData->total) ?>
                        </div>
                    </div>
                <?php } ?>

            </div>

            <div class="balance-due">

                <span class="label">
                    BALANCE DUE
                </span>

                <span class="amount">
                    <?= !empty($invoiceData->previous_due) ? showWithCurrencySymbol($invoiceData->total) : showWithCurrencySymbol($invoiceData->due); ?>
                </span>

            </div>

            <div class="invoice-footer">
                <!-- PAYMENT -->
                <div class="payment-info">

                    <p class="footer-title">
                        PAYMENT INSTRUCTIONS
                    </p>

                    <p>
                        <strong>Cash Pay</strong>,
                        <strong>Bank Pay</strong>,
                        <strong>Cheque Pay</strong>
                    </p>

                </div>
                <!-- SUPPORT -->
                <div class="support-info">

                    <p class="footer-title">
                        CONTACT SUPPORT
                    </p>

                    <p>
                        <?= get_data_by_id('email', 'shops', 'sch_id', $_SESSION['shopId']) ?>
                    </p>

                    <p>
                        <?= showWithPhoneNummberCountryCode(get_data_by_id('mobile', 'shops', 'sch_id', $_SESSION['shopId'])) ?>
                    </p>

                </div>

            </div>
        </div>

        <div class="row no-print">
            <div class="col-xs-12" style="margin-top: 20px;">
                <div class="print_line btn btn-primary pull-right" onclick="print(document);"><i
                            class="fa fa-print"></i> Print Now
                </div>
            </div>
        </div>
    </section>

</div>