
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
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="varchar">Store </label>
                                            <select class="form-control" name="type" onchange="typeChange(this.value)"  id="typeSelect" required>
                                                <option value="product">Product</option>
                                                <option value="brand">Brand</option>
                                                <option value="category">Category</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group" id="productDiv">
                                            <label>Product Id</label>
                                            <input type="number" class="form-control" name="prod_id" >
                                        </div>

                                        <div class="form-group" id="brandDiv" style="display:none;">
                                            <label>Brand Id</label>
                                            <select class="form-control" name="brand_id" >
                                                <option value="">Please Select</option>
                                                <?php foreach ($brand as $value){?>
                                                    <option value="<?= $value->brand_id?>"><?= $value->name?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="form-group" id="categoryDiv" style="display:none;">
                                            <label>Category Id</label>
                                            <select class="form-control" name="prod_cat_id"  >
                                                <option value="">Please Select</option>
                                                <?php foreach ($category as $val){?>
                                                <option value="<?= $val->prod_cat_id?>"><?= $val->product_category?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
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
                                    <div class="col-lg-3 ">
                                        <button style="margin-top: 25px;" class="btn btn-primary " type="submit">search</button>
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
                                <th>From Store</th>
                                <th>To Store</th>
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
                                    <td>
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Stock_transfer_ajax/view/'.$item->stock_transfer_id); ?>','<?php echo '/Admin/Stock_transfer/view/'.$item->stock_transfer_id; ?>')" class="btn btn-primary btn-xs">View</a>
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
