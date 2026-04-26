
<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Exchange Product <small>View</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Exchange Product</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12 no-print" style="margin-bottom: 15px;">
                <?php echo $menu;?>
            </div>
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-12">
                                <h4>Customer: <?= (!empty($exchangeProduct->customer_id))?get_data_by_id('customer_name','customers','customer_id',$exchangeProduct->customer_id):$exchangeProduct->customer_name;?> </h4>
                                <?php if ($exchangeProduct->type == 'Unconditional'){ ?>
                                <h4>Stock Transfer: <?= get_data_by_id('name','stores','store_id',$transfer->from_stock_id);?>  to <?= get_data_by_id('name','stores','store_id',$transfer->to_stock_id);?></h4>
                                <?php } ?>
                            </div>

                            <div class="col-lg-12" style="margin-top: 20px;">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>


                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <h4>Products</h4>
                        <table class="table table-bordered  text-capitalize" >
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Lot</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1; foreach ($transferItem as $item) {

                                $table = DB()->table('product_lot_info');
                                if ($exchangeProduct->type == 'Unconditional') {
                                    $table->where('stock_transfer_item_id', $item->stock_transfer_item_id);
                                }else {
                                    $table->where('exchange_product_item_id', $item->exchange_product_item_id);
                                }
                                $lot = $table->get()->getResult();


                                ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= get_data_by_id('name','products','prod_id',$item->prod_id);?></td>
                                    <td><?= $item->quantity;?></td>
                                    <td>
                                        <?php foreach ($lot as $val){ ?>
                                        <div style="display: flex;justify-content: space-between;">
                                            <p><?= $val->number;?></p>
                                            <p><?= $val->date;?></p>
                                            <p><?= $val->comment;?></p>
                                        </div>

                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>

                            </tbody>
                        </table>

                        <div class="col-md-6 mt-2">
                            <p><b>Date:</b> <?= globalTimeStamp($exchangeProduct->createdDtm); ?></p>
                            <p><b>Type:</b> <?= $exchangeProduct->type;?> Exchange</p>
                            <p><b>Comment:</b> <?= $exchangeProduct->comment;?></p>
                        </div>
                        <div class="col-xs-12 no-print">
                            <div class="print_line btn btn-primary pull-right" onclick="print(document);"><i class="fa fa-print"></i> Print Now</div>

                        </div>

                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
