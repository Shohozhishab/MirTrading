<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Product Category <small>Product Category Create</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Product Category </li>
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
                        <h3 class="box-title">Product Category Create</h3>
                        <button  data-toggle="modal" data-target="#modal-default" class="btn btn-info " style="float: right;"><i class="fa-solid fa-cloud-arrow-up" aria-hidden="true"></i> CSV Upload</button>
                        <br><br><a href="<?= base_url('uploads/csv/category.csv')?>" style="float: right;">Example</a>

                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div id="message"></div>
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            <div class="col-lg-6" >
                                <h4>Category</h4>
                                <form id="geniusform" action="<?php echo $action; ?>" method="post">
                                    <div class="form-group">
                                        <label for="varchar">Category Name </label>
                                        <input type="text" class="form-control" name="product_category" id="product_category" placeholder="Category Name" required>
                                        <div class="error"></div>
                                    </div>

                                    <button type="submit" class="btn btn-primary"  >Create</button>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Product_category_ajax/'); ?>','<?php echo '/Admin/Product_category/'; ?>')" class="btn btn-default">Cancel</a>
                                </form>
                            </div>


                            <div class="col-lg-6" style="border-left: 1px solid #cecdcd;">
                                <h4>Sub Category</h4>
                                <form id="geniusform" action="<?php echo $action; ?>" method="post">
                                    <div class="form-group" id="reloadimg">
                                        <label for="varchar">Parent Category </label>
                                        <select class="form-control" name="parent_pro_cat" required>
                                            <option value="">Please Select</option>
                                            <?php echo subCategoryListOption('','product_category','product_category'); ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="varchar">Sub Category Name</label>
                                        <input type="text" class="form-control" name="product_category" id="product_category" placeholder="Category Name" required>
                                        <div class="error"></div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary"  >Create</button>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Product_category_ajax/'); ?>','<?php echo '/Admin/Product_category/'; ?>')" class="btn btn-default">Cancel</a>
                                </form>
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
<div class="modal fade" id="modal-default">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">File upload</h4>
            </div>
            <div class="modal-body">

                <form action="<?= base_url('Admin/Product_category/csv_action')?>" method="post" enctype="multipart/form-data" >
                    <div class="form-group">
                        <label for="varchar">CSV File </label>
                        <input type="file" class="form-control" accept=".csv,text/csv" name="file" required />
                    </div>
                    <button type="submit" class="btn btn-primary" >Upload</button>
                </form>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>