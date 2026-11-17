<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Products <small>Products List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Products</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="col-xs-12" style="margin-bottom: 15px;">
            <?php echo $menu; ?>
        </div>
        <?php if (isDefaultRole() == true){ ?>
            <div class="row" id="reloadRoleDiv">
                <div class="col-lg-12" >
                    <button class="btn btn-sm btn-info " style="float: right;" onclick="rollPermissionBtn()">Roll Permission</button>
                </div>
                <div class="col-lg-12" id="permissionDiv" style="display: none; margin-top: 20px">
                    <form id="roleUpdateform" action="<?= base_url('Admin/Role/modulePermissionAction')?>" method="post">
                        <div class="box box-primary">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <select class="form-control" onchange="rolePermission(this.value,'Products')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="Products">
                                    </div>
                                    <div class="col-md-12" id="rolView"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php } ?>
        <div class="row" style="margin-top: 20px;">

            <?php if (isset($filter) && $filter == 1){ ?>
                <div class="col-xs-12" style="margin-bottom: 15px;">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-filter"></i> Filter Products</h3>
                        </div>
                        <div class="box-body">
                            <form action="<?= base_url('Admin/Products') ?>" method="get" id="filterForm" onsubmit="return false;">
                                <div class="row">
                                    <div class="form-group col-xs-3">
                                        <label for="int">Category </label>
                                        <select class="form-control" onchange="showSubCategory(this.value); productFilter();" name="category" id="category">
                                            <option value="">Please Select</option>
                                            <?php echo getCatListInOption($category, 'prod_cat_id', 'product_category', 'product_category'); ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-xs-3">
                                        <label for="int">Sub Category </label>
                                        <select class="form-control" name="sub_category" id="subCat" onchange="productFilter()">
                                            <option value="">Please Select</option>
                                            <?= subCategoryListOption($sub_category);?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Start Date</label>
                                        <input type="date" class="form-control" name="st_date" value="<?= $st_date ?? ''; ?>" id="st_date" onchange="productFilter()">
                                    </div>
                                    <div class="col-md-2">
                                        <label>End Date</label>
                                        <input type="date" class="form-control" name="en_date" value="<?= $en_date ?? ''; ?>" id="en_date" onchange="productFilter()">
                                    </div>

                                    <div class="col-md-1" style="margin-top: 25px;">
                                        <button type="button" onclick="productFilter()" class="btn btn-primary btn-block"><i class="fa fa-search"></i></button>
                                    </div>
                                    <div class="col-md-1" style="margin-top: 25px;">
                                        <button type="button" onclick="resetProductFilter()" class="btn btn-default btn-block"><i class="fa fa-refresh"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <div class="col-xs-12">
                <form action="<?php echo base_url('Admin/Products/barcode') ?>" method="post">
                    <div id="productResult">
                        <?php echo view('Admin/Products/table_list', [
                            'products_data' => $products_data,
                            'category' => $category ?? '',
                            'sub_category' => $sub_category ?? '',
                            'st_date' => $st_date ?? '',
                            'en_date' => $en_date ?? '',
                            'update' => $update ?? 0,
                            'print' => $print ?? 0,
                            'download_PDF' => $download_PDF ?? 0,
                            'download_CSV' => $download_CSV ?? 0,
                        ]); ?>
                    </div>
                </form>
            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
