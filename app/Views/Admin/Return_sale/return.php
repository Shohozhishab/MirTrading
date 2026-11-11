<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Return Product <small>Return Product List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Return Product</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <?php echo $menu;?>
            </div>
            <div class="col-xs-12">
                <form action="<?php echo $action; ?>" method="post" onsubmit="return validateReturnForm()">
                    <div class="box">
                        <div class="box-header">
                            <div class="row">
                                <div class="col-lg-6">
                                    <h3 class="box-title">Return Product List</h3>
                                </div>
                                <div class="col-lg-6"></div>
                            </div>
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                                <div id="errorMsg"></div>
                            </div>

                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table id="example1" class="table table-bordered table-striped dataTable" role="grid"
                                   aria-describedby="example1_info">
                                <thead>
                                <tr role="row">
                                    <th>Select</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Sale Price</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($invoice_item as $key => $row) {   $availableReturnQty = get_available_quantity_to_return($invoiceId,$row->prod_id,$row->quantity); ?>
                                    <tr role="row" class="odd">
                                        <td>
                                            <input type="checkbox" oninput="calculateTotalPrice()" name="returnchecked[<?= $key ?>]" class="datatablesReturn" id="checkedProd_<?= $key ?>" value="<?php echo $row->prod_id; ?>" <?= empty($availableReturnQty)?'disabled':'';?> >
                                            <input type="hidden" name="product_stock_relation_id[<?= $key ?>]" value="<?= $row->product_stock_relation_id;?>">
                                        </td>
                                        <td><?php echo get_data_by_id('name', 'products', 'prod_id', $row->prod_id) ?></td>
                                        <td><input type="number" step="any" class="quantityReturn form-control" oninput="calculateTotalPrice()" id="quantity_<?= $key ?>"
                                                   name="quantity[<?= $key ?>]" placeholder="Quantity" min="<?= empty($availableReturnQty)?0:1;?>" max="<?= (float)$availableReturnQty;?>"
                                                   value="<?= (float)$availableReturnQty ?>"> </td>
                                        <td><input type="text" class="purchase_priceReturn form-control" id="searchColumn_<?= $key ?>"
                                                   name="purchase_price[<?= $key ?>]" value="<?= (float)$row->price ?>" readonly></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>Select</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Sale Price</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Payment</h3>
                        </div>
                        <div class="box-body">

                                <div class="col-xs-12">
                                    <div class="col-xs-12" id="box_form">
                                        <div class="col-xs-7">
                                            <p class="lead">Payment Type:</p>
                                            <img src="<?php print base_url(); ?>/dist/img/credit/cash.jpeg" alt="Cash">
                                            <img src="<?php print base_url(); ?>/dist/img/credit/bank.png" alt="Bank">

                                            <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                                                Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles, weebly ning
                                                heekya handango imeem plugg
                                                dopplr jibjab, movity jajah plickers sifteo edmodo ifttt zimbra.
                                            </p>
                                        </div>
                                        <div class="col-xs-5">
                                            <div class="col-xs-6">
                                                <label for="int">Customer</label>
                                            </div>
                                            <div class="form-group col-xs-6">
                                                <?php foreach ($invoice as $row) {

                                                    if ($row->customer_id != NULL) { ?>

                                                        <input type="text" class="form-control"
                                                               value="<?php echo get_data_by_id('customer_name', 'customers', 'customer_id', $row->customer_id) ?>"
                                                               readonly>
                                                    <?php } else { ?>

                                                        <input type="text" class="form-control"
                                                               value="<?php echo $row->customer_name ?>" readonly>

                                                    <?php }
                                                } ?>
                                            </div>
                                            <?php
                                                $vatper = get_data_by_id('vat', 'invoice', 'invoice_id', $invoiceId);
                                                if (!empty($vatper)){
                                            ?>
                                            <div class="col-xs-6">

                                                <input type="hidden" name="vat" id="vat" value="<?php echo (float)$vatper; ?>">
                                                <label for="int">Vat (<?php echo $vatper; ?>%)</label>
                                            </div>
                                            <div class="form-group col-xs-6">
                                                <input type="text" class="form-control" id="vatAmount" name="vatAmount" value="" readonly>
                                            </div>

                                            <?php
                                                }
                                                $discount = get_data_by_id('entire_sale_discount', 'invoice', 'invoice_id', $invoiceId);
                                                if (!empty($discount)){
                                            ?>
                                            <div class="col-xs-6">

                                                <input type="hidden" name="discount" id="discount" value="<?php echo $discount; ?>">
                                                <label for="int">Discount (<?php echo $discount; ?>%)</label>
                                            </div>
                                            <div class="form-group col-xs-6">
                                                <input type="text" class="form-control" id="discountAmount" name="discountAmount" value="" readonly>
                                            </div>
                                            <?php } ?>


                                            <div class="col-xs-6">
                                                <label for="int">Final Return Amount
                                                    <div class="tooltip-container" style="position: relative; display: inline-flex; align-items: center; cursor: pointer;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 20 20" fill="none" style="vertical-align: middle;">
                                                            <path d="M10.0013 13.3334V10M10.0013 6.66669H10.0096M18.3346 10C18.3346 14.6024 14.6037 18.3334 10.0013 18.3334C5.39893 18.3334 1.66797 14.6024 1.66797 10C1.66797 5.39765 5.39893 1.66669 10.0013 1.66669C14.6037 1.66669 18.3346 5.39765 18.3346 10Z" stroke="#A7A7A7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        </svg>
                                                        <span class="tooltip-text">Product-Discount+Vat</span>
                                                    </div>
                                                </label>
                                                
                                            </div>
                                            <div class="form-group col-xs-6">
                                                <input type="text" class="form-control" name="totalPrice" id="totalAmount" readonly>
                                            </div>

                                            <div class="col-xs-6">
                                                <label for="int">Cash</label>
                                            </div>
                                            <div class="form-group col-xs-6">
                                                <input type="number" step="any" min="0" class="form-control nagod" id="nagod" name="nagod" placeholder="Input Cash Amount" oninput="checkCashBalance(); calculateDue();" onchange="checkCashBalance(); calculateDue();">
                                                <b id="Balance_valid"></b>
                                            </div>

                                            <div class="col-xs-6">
                                                <label for="int">Bank</label>
                                            </div>
                                            <div class="form-group col-xs-6">
                                                <select class="form-control" id="bank_id" name="bank_id" onchange="checkBankId(); calculateDue();">
                                                    <option value="">Select Bank</option>
                                                    <?php echo getTwoValueInOption($bankId ?? '', 'bank_id', 'name', 'account_no', 'bank'); ?>
                                                </select>
                                            </div>

                                            <div class="col-xs-6">
                                                <label for="int">Bank Amount</label>
                                            </div>
                                            <div class="form-group col-xs-6">
                                                <input type="number" step="any" min="0" class="form-control bankAmount" id="bankAmount" name="bankAmount" placeholder="Input Bank Amount" oninput="checkBankId(); calculateDue();" onchange="checkBankId(); calculateDue();">
                                                <b id="Bank_valid"></b>
                                            </div>

                                            <div class="col-xs-6">
                                                <label for="int">Due</label>
                                            </div>
                                            <div class="form-group col-xs-6">
                                                <input type="text" class="form-control" name="due" id="totalDueAmount"
                                                       readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xs-12" id="box_form">
                                        <?php foreach ($invoice as $row) {

                                            if ($row->customer_id != NULL) { ?>

                                                <input type="hidden" name="customer_id"
                                                       value="<?php echo $row->customer_id ?>">
                                            <?php } else { ?>

                                                <input type="hidden" name="customer_name"
                                                       value="<?php echo $row->customer_name ?>">

                                            <?php } ?>
                                            <input type="hidden" name="invoice_id" value="<?php echo $row->invoice_id ?>">
                                        <?php } ?>


                                        <button type="submit" class="btn btn-primary" id="createBtn">Return</button>
                                        <a href="<?php echo site_url('Admin/Return_sale') ?>" class="btn btn-default">Cancel</a>
                                    </div>
                                </div>

                        </div>
                    </div>

                </form>


            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>

<script>
    var shopCash = <?= (float) ($shopCash ?? 0); ?>;
    var bankBalances = <?= json_encode($bankBalances ?? []); ?>;
    var isWalkInCustomer = <?= (!empty($invoice[0]->customer_name) && empty($invoice[0]->customer_id)) ? 'true' : 'false'; ?>;

    function cleanNumber(val) {
        let n = parseFloat(val) || 0;
        return parseFloat(n.toFixed(8));
    }

    function calculateDue() {
        var totalAmount = parseFloat($("#totalAmount").val()) || 0;
        var cashVal     = parseFloat($("#nagod").val()) || 0;
        var bankVal     = parseFloat($("#bankAmount").val()) || 0;

        var due = Math.max(0, totalAmount - (cashVal + bankVal));
        if ($("#totalAmount").val() === "") {
            $("#totalDueAmount").val("");
        } else {
            $("#totalDueAmount").val(cleanNumber(due));
        }
    }

    function checkCashBalance() {
        var cashVal = parseFloat($("#nagod").val()) || 0;
        if (cashVal <= 0) {
            $("#Balance_valid").html("");
            return true;
        }
        if (cashVal > shopCash) {
            $("#Balance_valid").html('<span style="color:red">Balance is too low (Shop Cash: ' + shopCash + ')</span>');
            return false;
        } else {
            $("#Balance_valid").html('<span style="color:green">Balance is ok</span>');
            return true;
        }
    }

    function checkBankId() {
        var bankId  = $("#bank_id").val();
        var bankVal = parseFloat($("#bankAmount").val()) || 0;

        if (bankVal <= 0) {
            $("#Bank_valid").html("");
            return true;
        }
        if (!bankId) {
            $("#Bank_valid").html('<span style="color:red">Please Select Bank</span>');
            return false;
        }
        var avail = bankBalances[bankId] !== undefined ? parseFloat(bankBalances[bankId]) : 0;
        if (bankVal > avail) {
            $("#Bank_valid").html('<span style="color:red">Balance is too low (Available: ' + avail + ')</span>');
            return false;
        } else {
            $("#Bank_valid").html('<span style="color:#238A09">Balance is ok</span>');
            return true;
        }
    }

    function validateReturnForm() {
        let checkedProduct = document.querySelectorAll('.datatablesReturn:checked').length;
        let message = '';

        // Product validation
        if (checkedProduct == 0) {
            message += 'Please select at least one product .<br>';
        }

        // Quantity validation
        let checkedRows = document.querySelectorAll('.datatablesReturn:checked');
        checkedRows.forEach(function (item) {
            let row = item.closest('tr');
            let qty = parseFloat(row.querySelector('.quantityReturn').value) || 0;
            let maxQty = parseFloat(row.querySelector('.quantityReturn').max) || 0;

            if (qty <= 0) {
                message += 'Quantity must be greater than 0 .<br>';
            }

            if (qty > maxQty) {
                message += 'Quantity cannot exceed stock .<br>';
            }
        });

        let totalAmount = parseFloat($("#totalAmount").val()) || 0;
        let cashVal     = parseFloat($("#nagod").val()) || 0;
        let bankVal     = parseFloat($("#bankAmount").val()) || 0;
        let bankId      = $("#bank_id").val();

        if (cashVal > shopCash) {
            message += 'Cash amount exceeds shop cash balance (' + shopCash + ').<br>';
        }

        if (bankVal > 0 && !bankId) {
            message += 'Please Select Bank.<br>';
        } else if (bankVal > 0 && bankId) {
            let availBank = bankBalances[bankId] !== undefined ? parseFloat(bankBalances[bankId]) : 0;
            if (bankVal > availBank) {
                message += 'Bank amount exceeds available bank balance (' + availBank + ').<br>';
            }
        }

        if (cashVal + bankVal > totalAmount + 0.0001) {
            message += 'Total Paid (Cash + Bank) cannot exceed Final Return Amount.<br>';
        }

        if (isWalkInCustomer && totalAmount > 0) {
            let due = totalAmount - (cashVal + bankVal);
            if (Math.abs(due) > 0.0001) {
                message += 'Walk-in / Guest Customer return must be paid in full (Due must be 0.00).<br>';
            }
        }

        // Final validation
        if (message != '') {
            $('#errorMsg').html(
                '<div class="alert alert-danger">' + message + '</div>'
            );
            return false;
        }

        return true;
    }

    function calculateTotalPrice() {
        var totalPrice = 0;

        $("input.datatablesReturn:checked").each(function () {
            var row           = $(this).closest('tr');
            var quantity      = parseFloat(row.find('.quantityReturn').val()) || 0;
            var purchasePrice = parseFloat(row.find('.purchase_priceReturn').val()) || 0;

            totalPrice += quantity * purchasePrice;
        });

        // VAT %
        var vatPercent = parseFloat($("#vat").val()) || 0;

        // Discount %
        var discountPercent = parseFloat($("#discount").val()) || 0;

        // Discount amount
        var discountAmount = totalPrice * discountPercent / 100;

        // Amount after discount
        var afterDiscount = totalPrice - discountAmount;

        // VAT amount
        var vatAmount = afterDiscount * vatPercent / 100;

        // Final total
        var finalAmount = afterDiscount + vatAmount;

        // Set values
        $("#vatAmount").val(vatAmount !== 0 ? cleanNumber(vatAmount) : "");
        $("#discountAmount").val(discountAmount !== 0 ? cleanNumber(discountAmount) : "");

        $("#totalAmount").val(finalAmount !== 0 ? cleanNumber(finalAmount) : "");
        calculateDue();
    }

</script>