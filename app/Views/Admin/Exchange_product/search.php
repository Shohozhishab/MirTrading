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
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($invoice_item as $row) { ?>
                                    <tr role="row" class="odd">
                                        <td><input type="checkbox" name="returnchecked[]" class="datatables" id="checkedProd" value="<?php echo $row->prod_id; ?>">
                                            <input type="hidden" name="prod_id[]" value="<?php echo $row->prod_id ?>"></td>
                                        <td><?php echo get_data_by_id('name', 'products', 'prod_id', $row->prod_id) ?></td>
                                        <td><input type="number" class="quantity form-control" id="quantity" name="quantity[]" min="1" max="<?php echo $row->quantity ?>" placeholder="Quantity" value="<?php echo $row->quantity ?>"></td>
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
                                    <label for="varchar">Store </label>
                                    <select class="form-control" name="store_id" required>
                                        <option value="">Please Select</option>
                                        <?php foreach ($stores as $val){ ?>
                                            <option value="<?= $val->store_id ?>"><?= $val->name ?></option>
                                        <?php } ?>
                                    </select>
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

    </section>
    <!-- /.content -->
</div>
