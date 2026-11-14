<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Capital List <small>Capital List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Capital List</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
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
                                        <select class="form-control" onchange="rolePermission(this.value,'Capital')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="Capital">
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
                <div class="col-xs-12" >
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-filter"></i> Filter </h3>
                        </div>
                        <div class="box-body">
                            <form action="<?= base_url('Admin/Capital/list') ?>" method="get" id="filterForm" onsubmit="return false;">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Start Date</label>
                                        <input type="date" class="form-control" name="st_date" value="<?= $st_date ?? ''; ?>"
                                               id="st_date" onchange="capitalFilter()">
                                    </div>
                                    <div class="col-md-3">
                                        <label>End Date</label>
                                        <input type="date" class="form-control" name="en_date" value="<?= $en_date ?? ''; ?>"
                                               id="en_date" onchange="capitalFilter()">
                                    </div>

                                    <div class="col-md-2" style="margin-top: 25px;">
                                        <button type="button" onclick="capitalFilter()" class="btn btn-primary btn-block"><i class="fa fa-search"></i>
                                            Filter
                                        </button>
                                    </div>
                                    <div class="col-md-2" style="margin-top: 25px;">
                                        <button type="button" onclick="resetCapitalFilter()" class="btn btn-default btn-block"><i
                                                    class="fa fa-refresh"></i> Reset</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="col-xs-12">
                <div id="capitalResult">
                    <?php echo view('Admin/Capital/table_list', [
                        'capital' => $capital ?? [],
                        'st_date' => $st_date ?? '',
                        'en_date' => $en_date ?? '',
                        'print' => $print ?? 0,
                        'download_PDF' => $download_PDF ?? 0,
                        'download_CSV' => $download_CSV ?? 0,
                        'shopId' => $shopId ?? ($this->session->shopId ?? 0),
                    ]); ?>
                </div>

                <div class="col-md-12" id="ledgPrint" style="display: none; text-transform: capitalize;">
                    <?php echo view('Admin/Capital/print_table', [
                        'capital' => $capital ?? [],
                        'st_date' => $st_date ?? '',
                        'en_date' => $en_date ?? '',
                        'shopId' => $shopId ?? ($this->session->shopId ?? 0),
                    ]); ?>
                </div>
            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
