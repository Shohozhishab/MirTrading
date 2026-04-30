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
                <form action="<?= base_url('Admin/Exchange_product/exchangeAction') ?>" method="post">
                    <div class="box">
                        <div class="box-header">
                            <div class="row">
                                <div class="col-lg-6">
                                    <h3 class="box-title">Exchange Products List</h3>
                                </div>
                                <div class="col-lg-6"></div>
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
                                    <th>Lot</th>
                                    <th>Lot Add</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($invoice_item as $row) { $availableReturnQty = get_available_quantity_to_return($invoice->invoice_id,$row->prod_id,$row->quantity); ?>
                                    <tr role="row" class="odd">
                                        <td><input type="checkbox" name="prod_id[]" class="datatables" id="checkedProd" value="<?php echo $row->prod_id; ?>" <?= empty($availableReturnQty)?'disabled':'';?> ></td>

                                        <td><?php echo get_data_by_id('name', 'products', 'prod_id', $row->prod_id) ?></td>
                                        <td><input type="number" class="quantity form-control" id="quantity" name="quantity[]" min="<?= empty($availableReturnQty)?0:1;?>" max="<?= $availableReturnQty ?>" placeholder="Quantity" value="<?=$availableReturnQty ?>"> </td>
                                        <td><div id="showId_<?php echo $row->prod_id ?>"></div></td>
                                        <td>
                                            <?php if (!empty($availableReturnQty)){ ?>
                                            <button type="button" onclick="addLot('<?php echo $row->prod_id ?>','showId')"  class="btn btn-info">Add Lot</button>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                            <div class="col-xs-12" >
                                <?php if (!empty($invoice->customer_id)){ ?>
                                    <input type="hidden" name="customer_id" value="<?= $invoice->customer_id ?>">
                                <?php }else{ ?>
                                    <input type="hidden" name="customer_name" value="<?= $invoice->customer_name ?>">
                                <?php }?>

                                <div class="form-group col-md-6" >
                                    <div class="form-group">
                                        <label for="varchar">Type </label><br>
                                        <label>
                                            <input type="radio" name="type" onclick="typeChangeExchange(this)" value="Unconditional" checked>
                                            Unconditional
                                        </label>

                                        <label>
                                            <input type="radio" name="type" onclick="typeChangeExchange(this)" value="Conditional">
                                            Conditional
                                        </label>
                                    </div>
                                    <div class="form-group" >
                                        <label for="varchar">Comment </label>
                                        <textarea class="form-control" name="commentMain" required></textarea>
                                    </div>

                                    <div class="form-group" >
                                        <label for="varchar">Store </label>
                                        <select class="form-control" name="store_id" id="store_id"  required>
                                            <option value="">Please Select</option>
                                            <?php foreach ($stores as $val){ ?>
                                                <option value="<?= $val->store_id ?>"><?= $val->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12" >
                                    <button type="submit" class="btn btn-primary">Exchange</button>
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
                    <input type="hidden" class="form-control" name="proIdLot[]" id="proId" value="">
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