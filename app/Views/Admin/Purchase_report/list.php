<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Purchase Report <small>Purchase Report</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Purchase Report</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="col-xs-12" style="margin-bottom: 15px;">
            <?php echo $menu;?>
        </div>
        <?php if (isDefaultRole() == true){ ?>
            <div class="row" id="reloadRoleDiv" style="margin-bottom:20px; ">
                <div class="col-lg-12" >
                    <button class="btn btn-sm btn-info " style="float: right;" onclick="rollPermissionBtn()">Roll Permission</button>
                </div>
                <div class="col-lg-12" id="permissionDiv" style="display: none; margin-top: 20px">
                    <form id="roleUpdateform" action="<?= base_url('Admin/Role/modulePermissionAction')?>" method="post">
                        <div class="box box-primary">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <select class="form-control" onchange="rolePermission(this.value,'Purchase_report')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="Purchase_report">
                                    </div>
                                    <div class="col-md-12" id="rolView"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php } ?>
        <div class="row">

            <?php if (isset($filter) && $filter == 1){ ?>
                <div class="col-xs-12" style="margin-bottom: 15px;">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-filter"></i> Filter Purchase Report</h3>
                        </div>
                        <div class="box-body">
                            <form action="<?= base_url('Admin/Purchase_report') ?>" method="get" id="filterForm" onsubmit="return false;">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Start Date</label>
                                        <input type="date" class="form-control" name="st_date" id="st_date" value="<?= $st_date ?? '';?>" onchange="purchaseReportFilter()">
                                    </div>
                                    <div class="col-md-3">
                                        <label>End Date</label>
                                        <input type="date" class="form-control" name="en_date" id="en_date" value="<?= $en_date ?? '';?>" onchange="purchaseReportFilter()">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Supplier name</label>
                                        <select class="form-control select2" name="supplier_id" id="supplierId" onchange="purchaseReportFilter()" style="width: 100%;">
                                            <option value="">Please Select</option>
                                            <?php echo getAllListInOption($supplier_id ?? '', 'supplier_id', 'name', 'suppliers'); ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Products</label>
                                        <select class="form-control select2" name="prod_id" id="prodId" onchange="purchaseReportFilter()" style="width: 100%;">
                                            <option value="">Please Select</option>
                                            <?php echo getAllListInOption($prod_id ?? '', 'prod_id', 'name', 'products'); ?>
                                        </select>
                                    </div>
                                    <div class="col-md-1" style="margin-top: 25px;">
                                        <button type="button" onclick="purchaseReportFilter()" class="btn btn-primary btn-block"><i class="fa fa-search"></i></button>
                                    </div>
                                    <div class="col-md-1" style="margin-top: 25px;">
                                        <button type="button" onclick="resetPurchaseReportFilter()" class="btn btn-default btn-block"><i class="fa fa-refresh"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <div class="col-lg-8">
                <div id="purchaseReportResult">
                    <?php echo view('Admin/Purchase_report/table_list', [
                        'purchaseItem' => $purchaseItem ?? [],
                        'supplier_id' => $supplier_id ?? '',
                        'prod_id' => $prod_id ?? '',
                        'st_date' => $st_date ?? '',
                        'en_date' => $en_date ?? '',
                        'print' => $print ?? 0,
                        'download_PDF' => $download_PDF ?? 0,
                        'download_CSV' => $download_CSV ?? 0,
                        'shopId' => $shopId ?? ($this->session->shopId ?? 0),
                    ]); ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-fw fa-line-chart"></i> Suppliers Purchase Report List</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="TFtable">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Suppliers All Purchase Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $start = 0;
                            if (isset($suppliers) && is_array($suppliers)) {
                                foreach ($suppliers as $sup) { ?>
                                    <tr>
                                        <td width="80px"><?php echo ++$start; ?></td>
                                        <td><?php echo $sup->name; ?></td>
                                        <td><?php echo suppliersTotalPurchaseAmount($sup->supplier_id); ?></td>
                                    </tr>
                                <?php }
                            } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-12" id="ledgPrint" style="display: none; text-transform: capitalize;">
                <?php echo view('Admin/Purchase_report/print_table', [
                    'purchaseItem' => $purchaseItem ?? [],
                    'supplier_id' => $supplier_id ?? '',
                    'prod_id' => $prod_id ?? '',
                    'st_date' => $st_date ?? '',
                    'en_date' => $en_date ?? '',
                    'shopId' => $shopId ?? ($this->session->shopId ?? 0),
                ]); ?>
            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
