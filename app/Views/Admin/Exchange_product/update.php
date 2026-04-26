<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Exchange Products <small>Exchange Products List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Exchange Products</li>
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
                <form action="<?= base_url('Admin/Exchange_product/exchangeUpdateAction') ?>" method="post">
                    <div class="box">
                        <div class="box-header">
                            <div class="row">
                                <div class="col-lg-6">
                                    <h3 class="box-title">Exchange Products List</h3>
                                </div>
                                <div class="col-lg-6"></div>
                                <div class="col-lg-12" style="margin-top: 20px;">
                                    <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                                </div>
                            </div>


                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table id="example1" class="table table-bordered table-striped dataTable" role="grid"
                                   aria-describedby="example1_info">
                                <thead>
                                <tr role="row">
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Lot</th>
                                    <th>Lot Add</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($transferItem as $row) {
                                    $table = DB()->table('product_lot_info');
                                    if ($exchangeProduct->type == 'Unconditional') {
                                        $table->where('stock_transfer_item_id', $row->stock_transfer_item_id);
                                    }else {
                                        $table->where('exchange_product_item_id', $row->exchange_product_item_id);
                                    }
                                    $lot = $table->get()->getResult();
                                    $lotId = 0;
                                    ?>
                                    <tr role="row" class="odd">
                                        <td><input type="checkbox" name="prod_id[]" class="datatables" id="checkedProd" value="<?php echo $row->prod_id; ?>" checked hidden="">

                                        <?php echo get_data_by_id('name', 'products', 'prod_id', $row->prod_id) ?></td>
                                        <td><?php echo $row->quantity ?></td>
                                        <td><div id="showId_<?= ($exchangeProduct->type == 'Unconditional')?$row->stock_transfer_item_id:$row->exchange_product_item_id; ?>">
                                                <?php foreach ($lot as $val){ $lotId = $val->product_lot_info_id ?>
                                                    <div class="item " style="display:flex; margin-top:5px;">
                                                        <div style="display:flex;">
                                                            <?php if ($exchangeProduct->type == 'Unconditional') { ?>
                                                                <input type="hidden" class="form-control" name="stock_transfer_item_id[]" value="<?= $row->stock_transfer_item_id?>">
                                                            <?php }else{ ?>
                                                                <input type="hidden" class="form-control" name="exchange_product_item_id[]" value="<?= $row->exchange_product_item_id?>">
                                                            <?php } ?>
                                                            <input type="text" class="form-control" name="number[]" placeholder="Number" value="<?= $val->number?>">
                                                            <input type="date" class="form-control" name="date[]" value="<?= $val->date?>">
                                                            <input type="text" class="form-control" name="comment[]" placeholder="Comment" value="<?= $val->comment?>">
                                                        </div>
                                                        <div>
                                                            <button class="remove-me" onclick="removeRow(this)" style="height: 34px;width: 34px;border: 1px solid #d2d6de;margin-left: 5px;">X</button>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div></td>
                                        <td>
                                            <?php if ($exchangeProduct->type == 'Unconditional') { ?>
                                                <button type="button" onclick="addLot('<?= $row->stock_transfer_item_id?>','showId')"  class="btn btn-info">Add Lot</button>
                                            <?php }else{ ?>
                                                <button type="button" onclick="addLot('<?= $row->exchange_product_item_id?>','showId')"  class="btn btn-info">Add Lot</button>
                                            <?php } ?>

                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                            <div class="col-xs-12" >
                                <div class="form-group col-md-6" >
                                    <div class="form-group">
                                        <p><b>Type:</b> <?= $exchangeProduct->type ?></p>

                                    </div>
                                    <div class="form-group" >
                                        <label for="varchar">Comment </label>
                                        <textarea class="form-control" name="commentMain" required><?= $exchangeProduct->comment ?></textarea>
                                    </div>
                                    <?php if (!empty($status)){ ?>
                                    <div class="form-group" >
                                        <label for="varchar">Status </label>
                                        <select class="form-control" name="status" id="status"  required>
                                            <option value="1" <?= ($status->status == 1)?'selected':''; ?>>Received From Customer</option>
                                            <option value="2" <?= ($status->status == 2)?'selected':''; ?>>Sent to Warehouse</option>
                                            <option value="4" <?= ($status->status == 3)?'selected':''; ?>>Received From Warehouse</option>
                                            <option value="4" <?= ($status->status == 4)?'selected':''; ?>>Complete</option>
                                            <option value="5" <?= ($status->status == 5)?'selected':''; ?>>Canceled with no return</option>
                                        </select>
                                    </div>
                                    <?php } ?>
                                </div>
                                <div class="col-md-12" >
                                    <input type="hidden" name="exchange_pro_id" value="<?= $exchangeProduct->exchange_pro_id ?>">
                                    <button type="submit" class="btn btn-primary">Exchange Update</button>
                                </div>
                            </div>
                        </div>
                    </div>


                </form>


            </div>

        </div>
        <!-- /.row -->

        <div class="modal fade" id="basicModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Basic Modal</h4>
                        <button type="button" style="float: right" class="btn btn-sm btn-info" id="addBtn" onclick="addRow()" aria-hidden="true">Add row</button>
                    </div>
                    <div class="modal-body" >
                        <div id="modalBody">

                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="showCloneId" >
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="cloneDiv('modalBody')">Add</button>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <!-- /.content -->
</div>
<script>
    function addLot(prodctId, showID) {
        let html = `<div class="item template" style="display:flex; margin-top:5px;">
                <div style="display:flex;">
                    <?php if ($exchangeProduct->type == 'Unconditional') { ?>
                        <input type="hidden" class="form-control" name="stock_transfer_item_id[]" id="proId" value="<?= $row->stock_transfer_item_id?>">
                    <?php }else{ ?>
                        <input type="hidden" class="form-control" name="exchange_product_item_id[]" id="proId" value="<?= $row->exchange_product_item_id?>">
                    <?php } ?>
                    <input type="text" class="form-control" name="number[]" placeholder="Number">
                    <input type="date" class="form-control" name="date[]">
                    <input type="text" class="form-control" name="comment[]" placeholder="Comment">
                </div>
                <div>
                    <button class="remove-me" onclick="removeRow(this)" style="height: 34px;width: 34px;border: 1px solid #d2d6de;margin-left: 5px;">X</button>
                </div>
            </div>`;
        $('#modalBody').html(html);

        $('#proId').val(prodctId);
        $('#showCloneId').val(showID+'_'+prodctId);

        $('#basicModal').modal('show');
    }

    function addRow(){
        var prodctId = $('#proId').val();

        var cloned = $('.template').clone();

        cloned.removeClass('template').show();

        // clear only visible inputs
        cloned.find('input[type="text"], input[type="date"]').val('');

        // ✅ set proId here
        cloned.find('input[name="proIdLot[]"]').val(prodctId);

        $('#modalBody').append(cloned);
    }
    function removeRow(row){
        $(row).parent().parent('.item').remove();
    }

    function cloneDiv(cloneId){
        var cloned = $('#'+cloneId).clone(true);

        // ❗ remove the main container ID
        cloned.removeAttr('id');

        // ❗ also remove duplicate IDs inside (important)
        cloned.find('.item').removeClass('template');
        cloned.find('[id]').removeAttr('id');

        var show = $('#showCloneId').val();
        $('#'+show).append(cloned);

        $('#basicModal').modal('hide');
    }

    function typeChangeExchange(el){

        if ($(el).val() === 'Conditional') {

            // disable store
            $('#store_id').prop('disabled', true);
            $('#store_id').prop('required', false);
            $('#store_id').val('');

        } else {

            // enable store
            $('#store_id').prop('disabled', false);
            $('#store_id').prop('required', true);
        }
    }

</script>