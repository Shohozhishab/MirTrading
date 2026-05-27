
<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Stock Transfer <small>List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Stock Transfer</li>
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
                            <div class="col-lg-4">
                                <h3 class="box-title">Stock Transfer List</h3>
                            </div>
                            <div class="col-lg-8">
                                <form method="post" action="<?php echo site_url('Admin/Stock_transfer/search') ?>"  >
                                    <div class="col-lg-3 ">
                                        <div class="form-group" >
                                            <label for="varchar">Store </label>
                                            <select class="form-control" name="store_id" required>
                                                <option value="">Please Select</option>
                                                <?php foreach ($stores as $val){ ?>
                                                    <option value="<?= $val->store_id ?>"><?= $val->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="varchar">Item </label>
                                            <select class="form-control" name="type" onchange="typeChange(this.value)"  id="typeSelect" >
                                                <option value="">Please Select</option>
                                                <option value="product">Product</option>
                                                <option value="brand">Brand</option>
                                                <option value="category">Category</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group" id="productDiv" style="display:none;">
                                            <label>Product Id</label>
                                            <input type="number" class="form-control" name="prod_id" id="prodId" >
                                        </div>

                                        <div class="form-group" id="brandDiv" style="display:none;">
                                            <label>Brand Id</label>
                                            <select class="form-control" name="brand_id" id="brandId">
                                                <option value="">Please Select</option>
                                                <?php foreach ($brand as $value){?>
                                                    <option value="<?= $value->brand_id?>"><?= $value->name?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="form-group" id="categoryDiv" style="display:none;">
                                            <label>Category Id</label>
                                            <select class="form-control" name="prod_cat_id" id="prodCatId" >
                                                <option value="">Please Select</option>
                                                <?php foreach ($category as $val){?>
                                                <option value="<?= $val->prod_cat_id?>"><?= $val->product_category?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 ">
                                        <button style="margin-top: 25px;" class="btn btn-primary " type="submit">Search</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-lg-12" style="margin-top: 10px;">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>


                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-lg-12">
                            <form action="<?= base_url('Admin/Stock_transfer')?>" method="get">
                                <div class="col-xs-3" style="padding: 17px;">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" name="st_date" value="<?= $st_date; ?>" id="st_date" required>
                                </div>
                                <div class="col-xs-3" style="padding: 17px;">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" name="en_date" value="<?= $en_date; ?>" id="en_date" required>
                                </div>
                                <div class="col-xs-3" style="padding: 18px;">
                                    <button style="margin-top: 22px;" class="btn btn-primary " type="submit">Filter </button>
                                </div>
                            </form>
                        </div>
                        
                        <table id="example1" class="table table-bordered table-striped text-capitalize">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Date</th>
                                <th>From Store</th>
                                <th>To Store</th>
                                <th>Quantity</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1; foreach ($transfer as $item) { ?>
                                <tr>
                                    <td><?php echo $i++ ?></td>
                                    <td><?php echo globalTimeStamp($item->createdDtm); ?></td>
                                    <td><?php echo get_data_by_id('name','stores','store_id',$item->from_stock_id);?></td>
                                    <td><?php echo get_data_by_id('name','stores','store_id',$item->to_stock_id);?></td>
                                    <td><?php echo get_stock_transfer_qty_by_id($item->stock_transfer_id);?></td>
                                    <td>
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Stock_transfer_ajax/view/'.$item->stock_transfer_id); ?>','<?php echo '/Admin/Stock_transfer/view/'.$item->stock_transfer_id; ?>')" class="btn btn-primary btn-xs">View</a>
                                    </td>
                                </tr>
                            <?php } ?>

                            </tbody>
                        </table>

                        <div class="row no-print" >
                            <div class="col-xs-12">
                                <button onclick="printDiv('ledgPrint')" class="print_line btn btn-primary pull-right" ><i class="fa fa-print "></i> Print Now</button>
                                <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('ledgPrint','stockTransfer')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('ledgPrint','stockTransfer')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                            </div>
                        </div>

                        <div class="col-md-12" id="ledgPrint" style="display: none; text-transform: capitalize; " >
                            <div class="col-xs-12" style="margin-bottom: 20px;">
                                <div class="col-xs-6">
                                    <?php if(logo_image() == NULL){ ?>
                                        <img src="<?php echo base_url() ?>/uploads/schools/no_image.jpg" alt="User Image" >
                                    <?php }else{ ?>
                                        <img src="<?php echo base_url(); ?>/uploads/schools/<?php echo logo_image(); ?>" class="" alt="User Image">
                                    <?php } ?>
                                </div>
                                <div class="col-xs-6">
                                    <?php print address(); ?>
                                </div>
                            </div>
                            <div class="col-md-12" >
                                <table class="table table-bordered table-striped text-capitalize">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>From Store</th>
                                        <th>To Store</th>
                                        <th>Quantity</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 1; foreach ($transfer as $item) { ?>
                                        <tr>
                                            <td><?php echo $i++ ?></td>
                                            <td><?php echo globalTimeStamp($item->createdDtm); ?></td>
                                            <td><?php echo get_data_by_id('name','stores','store_id',$item->from_stock_id);?></td>
                                            <td><?php echo get_data_by_id('name','stores','store_id',$item->to_stock_id);?></td>
                                            <td><?php echo get_stock_transfer_qty_by_id($item->stock_transfer_id);?></td>
                                        </tr>
                                    <?php } ?>

                                    </tbody>
                                </table>
                            </div>
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
