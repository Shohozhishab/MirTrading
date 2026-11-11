<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="row">
                        <div class="col-lg-12">
                            <h3 class="box-title">Purchase Item</h3>
                        </div>
                        <div class="col-lg-12" style="margin-top: 20px;">
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <form action="<?= site_url('Admin/Purchase/purchaseEdiAction')?>" method="post">
                        <div class="col-md-12">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Product</th>
                                    <th>Quantity </th>
                                    <th>Purchase Price</th>
                                    <th>Sale Price</th>
                                    <th>Total Price</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i=1; foreach ($purchaseItem as $item){
                                    $unit = productIdByDefaultStoreUnit($item->prod_id);
                                    $conversion_factor = get_data_by_id('conversion_factor', 'units', 'units_id', $unit);
                                    if (empty($conversion_factor) || (float)$conversion_factor <= 0) {
                                        $conversion_factor = 1;
                                    }

                                    $salePrice = 0;
                                    if (!empty($item->product_stock_relation_id)) {
                                        $salePrice = get_data_by_id('selling_price', 'product_stock_relation', 'product_stock_relation_id', $item->product_stock_relation_id);
                                    }
                                    if (empty($salePrice)) {
                                        $stockRel = DB()->table('product_stock_relation')->where('product_id', $item->prod_id)->get()->getRow();
                                        if (!empty($stockRel)) {
                                            $salePrice = $stockRel->selling_price;
                                        }
                                    }

                                    // Fetch configured purchase units for this product
                                    $jsonArray = get_data_by_id('purchase_units', 'products', 'prod_id', $item->prod_id);
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
                                            'unit_id' => $u->units_id,
                                            'name' => $u->name,
                                            'symbol' => $u->symbol,
                                            'conversion_factor' => $factor,
                                            'qty' => $val,
                                        ];
                                    }
                                    ?>
                                    <tr>
                                        <td width="80px"><?= $i++ ?></td>
                                        <td>
                                            <?= get_data_by_id('name','products','prod_id',$item->prod_id); ?>
                                            <input type="hidden" name="purchase_item_id[]" value="<?= $item->purchase_item_id ?>">
                                            <input type="hidden" name="prod_id[]" value="<?= $item->prod_id ?>">
                                        </td>
                                        <td>
                                            <input type="hidden" name="qty[]" id="qtyUp_<?= $item->purchase_item_id ?>" value="<?= (float)$item->quantity; ?>">
                                            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                                <?php foreach ($displayUnits as $input){ ?>
                                                    <div style="margin-right: 10px;">
                                                        <label for="int" class="text-capitalize"><?= $input['name'];?></label>
                                                        <input type="number" step="any" min="0" class="form-control" oninput="qtyMakeBaseUnit(this, '<?= $item->purchase_item_id ?>')" data-factor="<?= (float)$input['conversion_factor']?>" style="width: 70px; display: inline-block;" value="<?= (float)$input['qty']; ?>">
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="hidden" name="price[]" id="priceUp_<?= $item->purchase_item_id; ?>" value="<?= (float)$item->purchase_price?>">
                                            <label for="int"><?= showUnitName($unit);?></label>
                                            <input type="number" step="any" min="0" class="form-control" name="priceUnit[]" style="width: 100px; display: inline-block;" oninput="priceMakeBaseUnit(this, '<?= (float)$conversion_factor;?>', '<?= $item->purchase_item_id;?>')" value="<?= (float)unitOrBasePriceByUnitPrice($unit,$item->purchase_price)?>">
                                        </td>
                                        <td>
                                            <input type="hidden" name="salePrice[]" id="salePriceUp_<?= $item->purchase_item_id; ?>" value="<?= (float)$salePrice;?>">
                                            <label for="int"><?= showUnitName($unit);?></label>
                                            <input type="number" step="any" min="0" class="form-control" name="salePriceUnit[]" style="width: 100px; display: inline-block;" oninput="salePriceMakeBaseUnit(this, '<?= (float)$conversion_factor;?>', '<?= $item->purchase_item_id;?>')" value="<?= (float)unitOrBasePriceByUnitPrice($unit,$salePrice)?>" >
                                        </td>
                                        <td><input type="text" class="form-control" name="total_price[]" value="<?= (float)$item->total_price ?>" readonly></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <div class="col-xs-6"></div>
                            <div class="col-xs-6">
                                <div class="col-xs-6">
                                    <label for="int">Total Amount</label>
                                </div>
                                <div class="form-group col-xs-6">
                                    <input type="number" step="any" class="form-control" name="totalPrice"
                                           id="totalPrice" value="<?= (float)$purchase->amount ?>"
                                           readonly>
                                </div>
                                <div class="col-xs-6">
                                    <label for="int">Cash</label>
                                </div>
                                <div class="form-group col-xs-6">
                                    <input type="number" step="any" min="0" onchange="checkShopsBalance(this.value); calculateDueTotal();"
                                           class="cash form-control"
                                           oninput="minusValueCheck(this.value,this); calculateDueTotal();" name="cash"
                                           id="cash" value="<?= (float)$purchase->nagad_paid ?>"><b id="Balance_valid"></b>
                                </div>
                                <div class="col-xs-6">
                                    <label for="int">Bank</label>
                                </div>
                                <div class="form-group col-xs-6">
                                    <select class="form-control" name="bank_id" id="bank_id">
                                        <option value="">Select Bank</option>
                                        <?php echo getTwoValueInOption($purchase->bank_id, 'bank_id', 'name', 'account_no', 'bank'); ?>
                                    </select><br>
                                    <input type="number" step="any" min="0" onchange="checkBankBalance(this.value); calculateDueTotal();"
                                           class="bank form-control"
                                           oninput="minusValueCheck(this.value,this); calculateDueTotal();" name="bank"
                                           id="bank" value="<?= (float)$purchase->bank_paid ?>">
                                    <b id="Bank_valid"></b>
                                </div>
                                <div class="col-xs-6">
                                    <label for="int">Due</label>
                                </div>
                                <div class="form-group col-xs-6">
                                    <input type="hidden" class="form-control" id="totalDue2" readonly value="<?= (float)$purchase->due ?>">
                                    <input type="number" step="any" class="form-control" name="due" id="totaldue" readonly value="<?= (float)$purchase->due ?>">
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <input type="hidden" name="purchase_id" value="<?= $purchase->purchase_id ?>">
                                <!-- Hidden element so that checkShopsBalance from ajaxScript.php never fails with null disabled -->
                                <span id="createBtn" style="display:none;"></span>
                                <button type="submit" class="btn btn-success " style="float: right;" id="dueBtn">Update</button>
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
    function cleanNumber(num) {
        let n = parseFloat(num);
        if (isNaN(n)) return 0;
        return parseFloat(n.toFixed(8));
    }

    function calculateRow(row) {
        let qty = parseFloat($(row).find('input[name="qty[]"]').val()) || 0;
        let price = parseFloat($(row).find('input[name="price[]"]').val()) || 0;

        let total = qty * price;

        $(row).find('input[name="total_price[]"]').val(cleanNumber(total));

        return total;
    }

    function calculateGrandTotal() {
        let grandTotal = 0;

        $('tbody tr').each(function () {
            grandTotal += calculateRow(this);
        });

        $('#totalPrice').val(cleanNumber(grandTotal));
        calculateDueTotal();
    }

    window.calculateDueTotal = function () {
        let total = parseFloat($('#totalPrice').val()) || 0;
        let cash  = parseFloat($('#cash').val()) || 0;
        let bank  = parseFloat($('#bank').val()) || 0;

        let due = total - (cash + bank);

        if (due < 0) {
            $('#dueBtn').prop('disabled', true);
        } else {
            $('#dueBtn').prop('disabled', false);
        }

        $('#totaldue').val(cleanNumber(due));
        $('#totalDue2').val(cleanNumber(due));
    };

    window.qtyMakeBaseUnit = function (elem, id) {
        let td;
        if (typeof elem === 'object' && elem !== null) {
            td = $(elem).closest("td");
        } else {
            td = $("#qtyUp_" + (id || elem)).closest("td");
        }
        let total = 0;
        td.find("input[data-factor]").each(function () {
            let factor = parseFloat($(this).data("factor")) || 0;
            let qty = parseFloat($(this).val()) || 0;

            total += qty * factor;
        });
        td.find('input[name="qty[]"]').val(cleanNumber(total));

        calculateGrandTotal();
    };

    window.priceMakeBaseUnit = function (elem, factor, id) {
        let td;
        let val;
        let key = parseFloat(factor) || 1;
        if (typeof elem === 'object' && elem !== null) {
            td = $(elem).closest("td");
            val = parseFloat($(elem).val()) || 0;
        } else {
            val = parseFloat(elem) || 0;
            td = $("#priceUp_" + (id || factor)).closest("td");
        }
        let total = val / key;

        td.find('input[name="price[]"]').val(cleanNumber(total));

        calculateGrandTotal();
    };

    window.salePriceMakeBaseUnit = function (elem, factor, id) {
        let td;
        let val;
        let key = parseFloat(factor) || 1;
        if (typeof elem === 'object' && elem !== null) {
            td = $(elem).closest("td");
            val = parseFloat($(elem).val()) || 0;
        } else {
            val = parseFloat(elem) || 0;
            td = $("#salePriceUp_" + (id || factor)).closest("td");
        }
        let total = val / key;

        td.find('input[name="salePrice[]"]').val(cleanNumber(total));
    };

    $(document).ready(function () {
        // Recalculate when quantity or price changes
        $(document).on('input change keyup', 'input[name="qty[]"], input[name="price[]"]', function () {
            calculateGrandTotal();
        });

        // Recalculate due when cash or bank changes
        $(document).on('input change keyup', '#cash, #bank', function () {
            calculateDueTotal();
        });

        calculateGrandTotal();
    });

    // Run immediately when loaded into DOM
    calculateGrandTotal();
</script>
