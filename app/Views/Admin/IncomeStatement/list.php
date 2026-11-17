<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Income Statement <small>Income Statement</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Income Statement</li>
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
                                        <select class="form-control" onchange="rolePermission(this.value,'IncomeStatement')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="IncomeStatement">
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
                            <form action="<?= base_url('Admin/Income_statement') ?>" method="get" id="filterForm" onsubmit="return false;">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>Days</label>
                                        <select class="form-control" name="period" id="period" onchange="incomeStatementFilter('period')">
                                            <option value="0">Please Select</option>
                                            <option value="7" <?= ($days == 7) ? 'selected' : '' ?>>Last 7 Days</option>
                                            <option value="30" <?= ($days == 30) ? 'selected' : '' ?>>Last 30 Days</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Months</label>
                                        <select class="form-control" name="month" id="month" onchange="incomeStatementFilter('month')">
                                            <option value="">Please Select</option>
                                            <option value="1"  <?= (isset($month) && $month == 1)  ? 'selected' : '' ?>>January</option>
                                            <option value="2"  <?= (isset($month) && $month == 2)  ? 'selected' : '' ?>>February</option>
                                            <option value="3"  <?= (isset($month) && $month == 3)  ? 'selected' : '' ?>>March</option>
                                            <option value="4"  <?= (isset($month) && $month == 4)  ? 'selected' : '' ?>>April</option>
                                            <option value="5"  <?= (isset($month) && $month == 5)  ? 'selected' : '' ?>>May</option>
                                            <option value="6"  <?= (isset($month) && $month == 6)  ? 'selected' : '' ?>>June</option>
                                            <option value="7"  <?= (isset($month) && $month == 7)  ? 'selected' : '' ?>>July</option>
                                            <option value="8"  <?= (isset($month) && $month == 8)  ? 'selected' : '' ?>>August</option>
                                            <option value="9"  <?= (isset($month) && $month == 9)  ? 'selected' : '' ?>>September</option>
                                            <option value="10" <?= (isset($month) && $month == 10) ? 'selected' : '' ?>>October</option>
                                            <option value="11" <?= (isset($month) && $month == 11) ? 'selected' : '' ?>>November</option>
                                            <option value="12" <?= (isset($month) && $month == 12) ? 'selected' : '' ?>>December</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Start Date</label>
                                        <input type="date" class="form-control" name="st_date" id="st_date" value="<?= $st_date; ?>" onchange="incomeStatementFilter('date')">
                                    </div>
                                    <div class="col-md-2">
                                        <label>End Date</label>
                                        <input type="date" class="form-control" name="en_date" id="en_date" value="<?= $en_date; ?>" onchange="incomeStatementFilter('date')">
                                    </div>

                                    <div class="col-md-2" style="margin-top: 25px;">
                                        <button type="button" onclick="incomeStatementFilter('date')" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Filter</button>
                                    </div>
                                    <div class="col-md-2" style="margin-top: 25px;">
                                        <button type="button" onclick="resetIncomeStatementFilter()" class="btn btn-default btn-block"><i class="fa fa-refresh"></i> Reset</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <div id="incomeStatementResult">
                <?php echo view('Admin/IncomeStatement/table_list', [
                    'purchaseAmount' => $purchaseAmount,
                    'sales' => $sales,
                    'profit' => $profit,
                    'expenses' => $expenses,
                    'profitLoss' => $profitLoss,
                    'st_date' => $st_date ?? '',
                    'en_date' => $en_date ?? '',
                    'month' => $month ?? '',
                    'days' => $days ?? 0,
                    'print' => $print ?? 0,
                    'download_PDF' => $download_PDF ?? 0,
                    'download_CSV' => $download_CSV ?? 0,
                ]); ?>
            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
