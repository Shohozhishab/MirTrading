<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Transaction Events <small>Transaction Events</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Transaction Events</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
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
                                        <select class="form-control" onchange="rolePermission(this.value,'TransactionEvents')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="TransactionEvents">
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
            <div class="col-xs-12" >
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-filter"></i> Filter Events</h3>
                    </div>
                    <div class="box-body">
                        <form action="<?= base_url('Admin/Transaction_events') ?>" method="get" id="filterForm" onsubmit="return false;">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Event Type</label>
                                    <select class="form-control" name="event_type" id="eventType" onchange="transactionEventsFilter()">
                                        <option value="">-- All Events --</option>
                                        <option value="transaction" <?= (($event_type ?? '') == 'transaction') ? 'selected' : ''; ?>>Transaction</option>
                                        <option value="sales" <?= (($event_type ?? '') == 'sales') ? 'selected' : ''; ?>>Sales</option>
                                        <option value="purchase" <?= (($event_type ?? '') == 'purchase') ? 'selected' : ''; ?>>Purchase</option>
                                        <option value="deposit" <?= (($event_type ?? '') == 'deposit') ? 'selected' : ''; ?>>Bank Deposit</option>
                                        <option value="withdraw" <?= (($event_type ?? '') == 'withdraw') ? 'selected' : ''; ?>>Bank Withdraw</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" name="st_date" value="<?= $st_date; ?>" id="st_date" onchange="transactionEventsFilter()">
                                </div>
                                <div class="col-md-2">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" name="en_date" value="<?= $en_date; ?>" id="en_date" onchange="transactionEventsFilter()">
                                </div>

                                <div class="col-md-2" style="margin-top: 25px;">
                                    <button type="button" onclick="transactionEventsFilter()" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Filter</button>
                                </div>
                                <div class="col-md-2" style="margin-top: 25px;">
                                    <button type="button" onclick="resetTransactionEventsFilter()" class="btn btn-default btn-block"><i class="fa fa-refresh"></i> Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php } ?>
            <div class="col-xs-12">
                <div id="transactionEventsResult">
                    <?php echo view('Admin/Transaction_events/table_list', [
                        'result' => $result,
                        'st_date' => $st_date ?? '',
                        'en_date' => $en_date ?? '',
                        'event_type' => $event_type ?? '',
                    ]); ?>
                </div>
            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
