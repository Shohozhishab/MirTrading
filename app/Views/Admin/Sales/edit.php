<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="row">
                        <div class="col-lg-12">
                            <h3 class="box-title">Sales Item</h3>
                        </div>
                        <div class="col-lg-12" style="margin-top: 20px;"></div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <form action="<?= site_url('Admin/Sales/salesEdiAction')?>" method="post">
                        <div class="col-md-12">
                            <table id="salesEditTable" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Product</th>
                                    <th>Price </th>
                                    <th>Quantity </th>
                                    <th>Total </th>
                                    <th>Subtotal</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php 
                                $i=0; 
                                foreach ($invoiceItem as $item){
                                    $uniqueId = !empty($item->inv_item) ? $item->inv_item : (++$i);
                                    $catId = get_data_by_id('prod_cat_id','products','prod_id',$item->prod_id);
                                    $parent_pro_cat = get_data_by_id('parent_pro_cat','product_category','prod_cat_id',$catId);
                                    $category = get_data_by_id('product_category','product_category','prod_cat_id',$parent_pro_cat);
                                    $subCategory = get_data_by_id('product_category','product_category','prod_cat_id',$catId);
                                    $productName = get_data_by_id('name','products','prod_id',$item->prod_id);
                                    $unit = productIdByDefaultStoreUnit($item->prod_id);

                                    // Fetch configured sale units for this product
                                    $jsonArray = get_data_by_id('sale_units', 'products', 'prod_id', $item->prod_id);
                                    $unitIds = !empty($jsonArray) ? json_decode($jsonArray, true) : [];
                                    $unitsList = [];
                                    if (!empty($unitIds) && is_array($unitIds)) {
                                        $unitsList = DB()->table('units')
                                            ->whereIn('units_id', $unitIds)
                                            ->orderBy('conversion_factor', 'DESC')
                                            ->get()
                                            ->getResult();
                                    }

                                    if (empty($unitsList) && !empty($unit)) {
                                        $defU = DB()->table('units')->where('units_id', $unit)->get()->getRow();
                                        if (!empty($defU)) {
                                            $unitsList = [$defU];
                                        }
                                    }

                                    if (empty($unitsList)) {
                                        $unitsList = [(object)[
                                            'units_id' => 0,
                                            'name' => 'Qty',
                                            'symbol' => '',
                                            'conversion_factor' => 1
                                        ]];
                                    }

                                    // Break down current quantity across all available units
                                    $remQty = (float)$item->quantity;
                                    $displayUnits = [];
                                    foreach ($unitsList as $u) {
                                        $factor = (float)$u->conversion_factor;
                                        if ($factor <= 0) $factor = 1;
                                        if ($factor > 1) {
                                            $val = floor($remQty / $factor);
                                            $remQty = $remQty - ($val * $factor);
                                        } else {
                                            $val = $remQty;
                                            $remQty = 0;
                                        }
                                        $displayUnits[] = [
                                            'name' => $u->name,
                                            'conversion_factor' => $factor,
                                            'qty' => (float)$val
                                        ];
                                    }
                                    ?>
                                    <tr>
                                        <td><?= $i;?></td>
                                        <td>
                                            <?= $productName.'<br> <small>('.$category.' > '.$subCategory .')</small>'; ?>
                                        </td>
                                        <td>
                                            <input type="hidden" name="prod_id[]" value="<?= $item->prod_id;?>">
                                            <input type="hidden" name="product_stock_relation_id[]" value="<?= $item->product_stock_relation_id;?>">
                                            <input type="hidden" name="inv_item[]" value="<?= $item->inv_item;?>">
                                            <input type="hidden" name="price[]" value="<?= (float)$item->price;?>">
                                            <label for="int" class="text-capitalize"><?= showUnitName($unit)?></label> <?= showWithCurrencySymbol((float)unitOrBasePriceByUnitPrice($unit,$item->price));?>
                                        </td>
                                        <td>
                                            <input type="hidden" name="qty[]" id="qtyUp_<?= $uniqueId; ?>" value="<?= (float)$item->quantity;?>" class="qty">
                                            <div style="display: flex; flex-wrap: wrap; gap: 5px;">
                                                <?php foreach ($displayUnits as $input){ ?>
                                                    <div style="margin-right: 5px;">
                                                        <label class="text-capitalize" style="margin-bottom: 2px; font-size: 12px; display: block;"><?= $input['name'];?></label>
                                                        <input type="number" step="any" min="0" oninput="qtyMakeBaseUnit(this, '<?= $uniqueId ?>')" data-factor="<?= $input['conversion_factor']?>" style="width: 65px;" class="form-control input-sm" value="<?= (float)$input['qty']; ?>">
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="hidden" name="total[]" value="<?= (float)$item->total_price;?>" class="totalVal">
                                            <span class="totalValText"><?= showWithCurrencySymbol((float)$item->total_price);?></span>
                                        </td>
                                        <td>
                                            <input type="hidden" name="discount[]" style="width: 100px;" value="<?= (float)$item->discount;?>" class="discount">
                                            <input type="hidden" name="subTotal[]" value="<?= (float)$item->final_price;?>" class="subTotalVal">
                                            <span class="subTotal"><?= showWithCurrencySymbol((float)$item->final_price);?></span>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-6"></div>
                            <div class="col-md-6" style="background-color: #e8b96f;padding: 10px;">
                                <div class="col-xs-12" style="border:1px dashed #D0D3D8 ;padding-top: 10px;">
                                    <div class="panel with-nav-tabs panel-default nav-tabs-custom" style="background-color: #e8b96f;">
                                        <div class="panel-body">
                                            <div class="tab-content">
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <?php if (!empty($invoice->customer_id)){ ?>
                                                            <input type="hidden" name="customer_id" value="<?= $invoice->customer_id; ?>">
                                                            <p><b>Customer:</b> <?= get_data_by_id('customer_name','customers','customer_id',$invoice->customer_id)?></p>
                                                        <?php }else{ ?>
                                                            <input type="text" class="form-control" name="name" value="<?= $invoice->customer_name; ?>"/>
                                                            <p><b>Customer:</b> <?= $invoice->customer_name; ?></p>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php 
                                    $initialDiscAmount = ((float)$invoice->amount * (float)$invoice->entire_sale_discount) / 100;
                                    $initialVatAmount = (((float)$invoice->amount - $initialDiscAmount) * (float)$invoice->vat) / 100;
                                ?>
                                <div class="col-xs-6" style="border:1px dashed #D0D3D8 ;padding:5px;">
                                    <label>Entire Sale Discount: %</label>
                                    <input type="number" step="any" min="0" class="form-control saleDisc" oninput="minusValueCheck(this.value,this)" name="saleDisc" id="saleDisc" placeholder="Input Discount %" value="<?= (float)$invoice->entire_sale_discount ?>">
                                    <input type="hidden" class="form-control totalamount" id="totalamount" readonly value="<?= (float)$invoice->final_amount ?>">
                                </div>
                                <div class="col-xs-6" style="border:1px dashed #D0D3D8 ;padding:5px;">
                                    <label>Discount Amount</label>
                                    <input type="text" class="form-control" name="saleDiscshow" id="saleDiscshow" placeholder="Discount Amount" value="<?= (float)$initialDiscAmount ?>" readonly>
                                    <input type="hidden" name="granddiscountlast" id="granddiscountlast">
                                </div>
                                <div class="col-xs-12" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                    <div class="col-xs-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                        <label>Vat: %</label>
                                        <input type="number" step="any" min="0" class="form-control vat" oninput="minusValueCheck(this.value,this)" name="vat" id="vat" placeholder="vat %" value="<?= (float)$invoice->vat ?>">
                                        <input type="hidden" class="form-control vatTotallast" name="vatTotallast" id="vatTotallast" readonly value="<?= (float)$invoice->final_amount ?>">
                                    </div>
                                    <div class="col-xs-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                        <label>Vat Amount</label>
                                        <input type="text" onchange="checkBankId()" class="form-control vatAmount" name="vatAmount" id="vatAmount" placeholder="Vat Amount" value="<?= (float)$initialVatAmount ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-xs-12" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                    <div class="col-xs-12" style="padding:5px;">
                                        <label>Grand Total</label>
                                        <input type="hidden" class="form-control" name="grandtotal2" readonly id="grandtotal2" value="<?= (float)$invoice->amount ?>">
                                        <input type="text" class="form-control" name="grandtotal" readonly id="grandtotal" value="<?= (float)$invoice->final_amount ?>">
                                    </div>

                                    <div class="col-xs-12" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                        <label>Payment</label>
                                        <div class="col-xs-12" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                            <label>Cash</label>
                                            <input type="number" step="any" min="0" class="form-control nagod" oninput="minusValueCheck(this.value,this)" name="nagod" id="nagod" placeholder="Input Cash Amount" value="<?= (float)$invoice->nagad_paid?>">
                                        </div>
                                        <div class="col-xs-12" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                            <div class="col-xs-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                                <label>Bank</label>
                                                <select class="form-control" name="bank_id" id="bank_id" onchange="checkBankId()">
                                                    <option value="">Select Bank</option>
                                                    <?php echo getTwoValueInOption($invoice->bank_id, 'bank_id', 'name', 'account_no', 'bank'); ?>
                                                </select>
                                            </div>
                                            <div class="col-xs-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                                <label>Bank Amount</label>
                                                <input type="number" step="any" min="0" onchange="checkBankId()" class="form-control bankAmount"
                                                       name="bankAmount" id="bankAmount" oninput="minusValueCheck(this.value,this); checkBankId();" placeholder="input Bank Amount" value="<?= (float)$invoice->bank_paid?>">
                                                <b id="Bank_valid"></b>
                                            </div>
                                        </div>
                                        <div class="col-xs-12" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                            <div class="col-xs-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                                <label>Cheque No</label>
                                                <input type="text" class="form-control" name="chequeNo" id="chequeNo"
                                                       placeholder="Input Cheque No " value="<?= $invoice->chaque_id?>" oninput="cheque()">
                                            </div>
                                            <div class="col-xs-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                                <label>Cheque Amount</label>
                                                <input type="number" step="any" min="0" onchange="cheque()" class="form-control chequeAmount"
                                                       name="chequeAmount" oninput="minusValueCheck(this.value,this); cheque();" id="chequeAmount" placeholder="Input Cheque Amount " value="<?= (float)$invoice->chaque_paid?>">
                                                <b id="cheque_valid"></b>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-6" style="border:1px dashed #D0D3D8 ;padding:5px;">
                                        <label>Total Amount </label>
                                        <input type="text" class="form-control " name="grandtotallast" readonly
                                               id="grandtotallast" value="<?= (float)$invoice->final_amount ?>">
                                    </div>
                                    <div class="col-xs-6" style="border:1px dashed #D0D3D8; padding:5px;">
                                        <label>Total Due</label>
                                        <input type="number" step="any" class="form-control" name="grandtotaldue" readonly id="grandtotaldue"
                                               value="<?= (float)$invoice->due ?>">
                                    </div>
                                </div>
                                <div class="col-xs-12" style="padding:20px;">
                                    <input type="hidden" name="invoice_id" value="<?= $invoice->invoice_id ?>">
                                    <button style="float: right;" id="dueBtn" type="submit"
                                            class="btn btn-primary">Update Sale</button>
                                    <b id="mess"></b>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
    <!-- /.row -->
</section>
<!-- /.content -->

<script>
    // Helper to clean trailing decimal zeros and keep clean numeric output
    function cleanNumber(n) {
        if (isNaN(n) || n === null || n === undefined) return 0;
        let num = parseFloat(n);
        if (isNaN(num)) return 0;
        return parseFloat(num.toFixed(8));
    }

    window.minusValueCheck = function (value, element) {
        if (parseFloat(value) < 0) {
            $(element).val(0);
        }
    };

    window.checkBankId = function () {
        let bank = $("#bank_id").val();
        let amount = parseFloat($("#bankAmount").val()) || 0;
        if (amount > 0 && !bank) {
            $("#Bank_valid").text("Please select a bank").css("color", "red");
        } else {
            $("#Bank_valid").text("");
        }
        calculate();
    };

    window.cheque = function () {
        let chequeNo = $("#chequeNo").val();
        let amount = parseFloat($("#chequeAmount").val()) || 0;
        if (amount > 0 && !chequeNo) {
            $("#cheque_valid").text("Please enter cheque number").css("color", "red");
        } else {
            $("#cheque_valid").text("");
        }
        calculate();
    };

    window.calculate = function () {
        let grandTotal = 0;

        $("#salesEditTable tbody tr").each(function () {
            let price = parseFloat($(this).find("input[name='price[]']").val()) || 0;
            let qty = parseFloat($(this).find("input[name='qty[]']").val()) || 0;
            let discount = parseFloat($(this).find("input[name='discount[]']").val()) || 0;

            let total = cleanNumber(price * qty);
            $(this).find("input[name='total[]']").val(total);
            $(this).find(".totalValText").text("৳ " + total);

            let subTotal = cleanNumber(total - discount);
            if (subTotal < 0) subTotal = 0;

            $(this).find("input[name='subTotal[]']").val(subTotal);
            $(this).find(".subTotal").text("৳ " + subTotal);

            grandTotal += subTotal;
        });

        grandTotal = cleanNumber(grandTotal);

        let saleDisc = parseFloat($("#saleDisc").val()) || 0;
        let saleDiscAmount = cleanNumber(grandTotal * saleDisc / 100);
        $("#saleDiscshow").val(saleDiscAmount);

        let afterDiscount = cleanNumber(grandTotal - saleDiscAmount);

        let vat = parseFloat($("#vat").val()) || 0;
        let vatAmount = cleanNumber(afterDiscount * vat / 100);
        $("#vatAmount").val(vatAmount);

        let finalTotal = cleanNumber(afterDiscount + vatAmount);

        $("#grandtotal").val(finalTotal);
        $("#grandtotal2").val(grandTotal);
        $("#grandtotallast").val(finalTotal);
        $("#totalamount").val(finalTotal);
        $("#vatTotallast").val(finalTotal);

        let cash = parseFloat($("#nagod").val()) || 0;
        let bank = parseFloat($("#bankAmount").val()) || 0;
        let cheque = parseFloat($("#chequeAmount").val()) || 0;

        let paid = cleanNumber(cash + bank + cheque);
        let due = cleanNumber(finalTotal - paid);

        if (due < 0) {
            due = 0;
            $("#dueBtn").prop("disabled", true);
        } else {
            $("#dueBtn").prop("disabled", false);
        }

        $("#grandtotaldue").val(due);
    };

    // Convert Units -> Base Quantity
    window.qtyMakeBaseUnit = function (elem, printId) {
        let td = elem ? $(elem).closest("td") : $("#qtyUp_" + printId).closest("td");
        let totalQty = 0;

        td.find("input[data-factor]").each(function () {
            let factor = parseFloat($(this).data("factor")) || 1;
            let qty = parseFloat($(this).val()) || 0;
            totalQty += qty * factor;
        });

        totalQty = cleanNumber(totalQty);
        $("#qtyUp_" + printId).val(totalQty);

        calculate();
    };

    $(document).ready(function () {
        $(document).on(
            "keyup change input",
            "#salesEditTable input[name='qty[]'], #salesEditTable input[name='discount[]'], #saleDisc, #vat, #nagod, #bankAmount, #chequeAmount, #bank_id",
            function () {
                calculate();
            }
        );
        calculate();
    });

    // Run calculate immediately in case script is injected into modal
    calculate();
</script>
