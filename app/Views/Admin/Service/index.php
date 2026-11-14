<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Service  <small>Service List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Service</li>
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
                                        <select class="form-control" onchange="rolePermission(this.value,'Service')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="Service">
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
                        <h3 class="box-title"><i class="fa fa-filter"></i> Filter </h3>
                    </div>
                    <div class="box-body">
                        <form action="<?= base_url('Admin/Service') ?>" method="get" onsubmit="return false;">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" name="st_date" value="<?= $st_date; ?>"
                                           id="st_date" onchange="serviceFilter()">
                                </div>
                                <div class="col-md-3">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" name="en_date" value="<?= $en_date; ?>"
                                           id="en_date" onchange="serviceFilter()">
                                </div>

                                <div class="col-md-2" style="margin-top: 25px;">
                                    <button type="button" onclick="serviceFilter()" class="btn btn-primary btn-block"><i class="fa fa-search"></i>
                                        Filter
                                    </button>
                                </div>
                                <div class="col-md-2" style="margin-top: 25px;">
                                    <button type="button" onclick="resetServiceFilter()" class="btn btn-default btn-block"><i
                                                class="fa fa-refresh"></i> Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php } ?>
            <?php if (isset($create) && $create == 1){ ?>
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <a href="javascript:void(0)"
                    onclick="showData('<?php echo site_url('/Admin/Service_ajax/create/'); ?>','<?php echo '/Admin/Service/create/'; ?>'),activeTab(this)"
                    class="btn btn-success"><i class="fa fa-plus"></i> Add Service</a>
            </div>
            <?php } ?>
            <div class="col-xs-12">
                <div id="serviceResult">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-lg-12" style="margin-top: 20px;" id="message">
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                        </div>

                        <table id="example1" class="table table-bordered table-striped text-capitalize">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php 
                            $start = 1; 
                            $totalAmount = 0;
                            foreach ($services as $val) {  
                                $invAmount = get_data_by_id('final_amount','service_invoice','service_invoice_id',$val->service_invoice_id);
                                $totalAmount += (float)$invAmount;
                            ?>
                                <tr>
                                    <td width="80px"><?php echo $start++ ?></td>
                                    <td><?php echo $val->title ?></td>
                                    <td><?php echo showServiceName($val->service_type) ?></td>
                                    <td><?php echo showWithCurrencySymbol($invAmount) ?></td>
                                    <td width="180px">
                                        <?php if (isset($read) && $read == 1){ ?>
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Service_invoice_ajax/view/'.$val->service_invoice_id); ?>','<?php echo '/Admin/Service_invoice/view/'.$val->service_invoice_id; ?>')" class="btn btn-warning btn-xs">View</a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>No</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th>Total</th>
                                <th>= <?php echo showWithCurrencySymbol($totalAmount); ?></th>
                                <th></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
                </div>

                <div class="row no-print" >
                    <div class="col-xs-12">
                        <?php if (isset($print) && $print == 1){ ?>
                        <button onclick="printDiv('ledgPrint')" class="print_line btn btn-primary pull-right" ><i class="fa fa-print "></i> Print Now</button>
                        <?php } ?>
                        <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                        <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('ledgPrint','service')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                        <?php } ?>
                        <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                        <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('ledgPrint','service')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                        <?php } ?>
                    </div>
                </div>

                <div style="display: none;">
                    <div id="ledgPrint">
                        <?php echo view('Admin/Service/print_table', [
                            'services' => $services,
                            'st_date' => $st_date,
                            'en_date' => $en_date,
                            'id' => isset($id) ? $id : (isset($shopId) ? $shopId : null)
                        ]); ?>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
