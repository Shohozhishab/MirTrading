
<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Exchange Product <small>List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Exchange Product</li>
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

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-6">
                                <h3 class="box-title">Exchange Product List</h3>
                            </div>
                            <div class="col-lg-6">
                                <form method="post" action="<?php echo site_url('Admin/Exchange_product/invoice_search') ?>"  >
                                    <div class="col-lg-4 pull-right">
                                        <button style="margin-top: 25px;" class="btn btn-primary " type="submit">search</button>
                                    </div>
                                    <div class="col-lg-8 pull-right">
                                        <label>Input InvoiceId</label>
                                        <input type="text" class="form-control" name="invoiceId" id="invoiceId"  required>
                                    </div>
                                </form>
                            </div>
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>


                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped text-capitalize">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Customer</th>
                                <th>Comment</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1; foreach ($exchangeProduct as $item) { ?>
                                <tr>
                                    <td><?php echo $i++ ?></td>
                                    <td><?php echo globalTimeStamp($item->createdDtm); ?></td>
                                    <td><?php echo $item->type; ?></td>
                                    <td><?php
                                        if(!empty($item->customer_id)){
                                            echo get_data_by_id('customer_name','customers','customer_id',$item->customer_id);
                                        }else{
                                            echo $item->customer_name;
                                        }

                                        ?></td>
                                    <td><?php echo $item->comment; ?></td>
                                    <td>
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Exchange_product_ajax/view/'.$item->exchange_pro_id); ?>','<?php echo '/Admin/Exchange_product/view/'.$item->exchange_pro_id; ?>')" class="btn btn-primary btn-xs">View</a>
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Exchange_product_ajax/update/' . $item->exchange_pro_id); ?>','<?php echo '/Admin/Exchange_product/update/' . $item->exchange_pro_id; ?>')" class="btn btn-warning btn-xs">Update</a>

                                    </td>
                                </tr>
                            <?php } ?>

                            </tbody>
                        </table>
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
