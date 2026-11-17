<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Sales <small>Sales List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Sales</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="col-xs-12" style="margin-bottom: 15px;">
            <?php echo $menu; ?>
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
                                        <select class="form-control" onchange="rolePermission(this.value,'Sales')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="Sales">
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
                        <h3 class="box-title"><i class="fa fa-filter"></i> Filter Sales</h3>
                    </div>
                    <div class="box-body">
                        <form action="<?= base_url('Admin/Sales') ?>" method="get" onsubmit="return false;">
                            <div class="row">
                                <div class="col-lg-2 ">
                                    <label>Customer</label><br>
                                    <select class="form-control select2" name="customer" id="customerId" onchange="salesFilter()" style="width: 100%;">
                                        <option value="">Please Select</option>
                                        <?= getAllListInOptionWithStatus( $customer_id, 'customer_id', 'customer_name', 'customers', 'customer_name' );?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" name="st_date" value="<?= $st_date; ?>"
                                           id="st_date" onchange="salesFilter()">
                                </div>
                                <div class="col-md-3">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" name="en_date" value="<?= $en_date; ?>"
                                           id="en_date" onchange="salesFilter()">
                                </div>

                                <div class="col-md-2" style="margin-top: 25px;">
                                    <button type="button" onclick="salesFilter()" class="btn btn-primary btn-block"><i class="fa fa-search"></i>
                                        Filter
                                    </button>
                                </div>
                                <div class="col-md-2" style="margin-top: 25px;">
                                    <button type="button" onclick="resetSalesFilter()" class="btn btn-default btn-block"><i
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
                   onclick="showData('<?php echo site_url('/Admin/Sales_ajax/create/'); ?>','<?php echo '/Admin/Sales/create/'; ?>')"
                   class="btn  btn-success"><i class="fa fa-plus"></i> Add Sales</a>
            </div>
            <?php } ?>
            <div class="col-xs-12">
                <div id="salesResult">
                    <div class="box">
                        <div class="box-header">
                            <div class="row">
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
                                    <th>Customer</th>
                                    <th>Invoice Id</th>
                                    <th>Total Amount</th>
                                    <th>Total Due</th>
                                    <?php if (isset($profit) && $profit == 1){ ?>
                                    <th>Profit</th>
                                    <?php } ?>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1;
                                $totalAmount = 0;
                                $totalDue = 0;
                                $totalProfit = 0;
                                foreach ($sales as $val) {
                                    $cus_id = get_data_by_id('customer_id', 'invoice', 'invoice_id', $val->invoice_id);
                                    $cusName = !empty($cus_id) ? get_data_by_id('customer_name', 'customers', 'customer_id', $cus_id) : get_data_by_id('customer_name', 'invoice', 'invoice_id', $val->invoice_id);
                                    $profitOr = get_data_by_id('profit', 'invoice', 'invoice_id', $val->invoice_id);
                                    $invAmount = get_data_by_id('amount', 'invoice', 'invoice_id', $val->invoice_id);
                                    $invDue = get_data_by_id('due', 'invoice', 'invoice_id', $val->invoice_id);

                                    $totalAmount += $invAmount;
                                    $totalDue += $invDue;
                                    $totalProfit += $profitOr;
                                    ?>
                                    <tr>
                                        <td><?php echo $i++ ?></td>
                                        <td><?php echo invoiceDateFormat($val->date) ?></td>
                                        <td><?php echo $cusName ?></td>
                                        <td><?php echo $val->invoice_id ?></td>
                                        <td><?php echo showWithCurrencySymbol($invAmount) ?></td>
                                        <td><?php echo showWithCurrencySymbol($invDue) ?></td>
                                        <?php if (isset($profit) && $profit == 1){ ?>
                                        <td><?php echo showWithCurrencySymbol($profitOr) ?></td>
                                        <?php } ?>
                                        <td>
                                            <?php if (isset($transaction_flow) && $transaction_flow == 1){ ?>
                                            <a href="javascript:void(0)"
                                               onclick="showData('<?php echo site_url('/Admin/Sales_ajax/transaction_flow/' . $val->sales_id); ?>','<?php echo '/Admin/Sales/transaction_flow/' . $val->sales_id; ?>')"
                                               class="btn btn-success btn-xs">Transaction Flow </a>
                                            <?php } ?>
                                            <?php if (isset($read) && $read == 1){ ?>
                                            <a href="javascript:void(0)"
                                               onclick="showData('<?php echo site_url('/Admin/Invoice_ajax/view/' . $val->invoice_id); ?>','<?php echo '/Admin/Invoice/view/' . $val->invoice_id; ?>')"
                                               class="btn btn-primary btn-xs">View</a>
                                            <?php } ?>
                                            <?php if (isset($update) && $update == 1){ ?>
                                                <a href="javascript:void(0)" class="btn btn-xs btn-warning"
                                                       onclick="saleEdit('<?= $val->sales_id; ?>')" data-toggle="modal"
                                                       data-target="#modal-default">Edit</a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>

                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Invoice Id</th>
                                    <th>Total Amount</th>
                                    <th>Total Due</th>
                                    <?php if (isset($profit) && $profit == 1){ ?>
                                    <th>Profit</th>
                                    <?php } ?>
                                    <th>Action</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>Total</th>
                                    <th>= <?php echo showWithCurrencySymbol($totalAmount); ?></th>
                                    <th>= <?php echo showWithCurrencySymbol($totalDue); ?></th>
                                    <?php if (isset($profit) && $profit == 1){ ?>
                                    <th>= <?php echo showWithCurrencySymbol($totalProfit); ?></th>
                                    <?php } ?>
                                    <th></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row no-print">
                    <div class="col-xs-12">
                        <?php if (isset($print) && $print == 1){ ?>
                            <button onclick="printDiv('ledgPrint')" class="print_line btn btn-primary pull-right"><i
                                        class="fa fa-print "></i> Print Now
                            </button>
                        <?php } ?>
                        <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                            <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;"
                                    onclick="downloadPDF('ledgPrint','sales')"><i class="fa fa-file-pdf-o "></i>
                                Download PDF
                            </button>
                        <?php } ?>
                        <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                            <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;"
                                    onclick="downloadCSV('ledgPrint','sales')"><i class="fa fa-file-excel-o "></i>
                                Download CSV
                            </button>
                        <?php } ?>
                    </div>
                </div>

                <div style="display: none;">
                    <div id="ledgPrint">
                        <?php echo view('Admin/Sales/print_table', [
                            'sales' => $sales,
                            'customer_id' => $customer_id,
                            'st_date' => $st_date,
                            'en_date' => $en_date,
                            'profit' => isset($profit) ? $profit : 0
                        ]); ?>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>

<div class="modal fade" id="modal-default">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Data</h4>
            </div>
            <div class="modal-body" id="formData">


            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script>
    function saleEdit(salesId) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Sales/salesEdit') ?>",
            data: {id: salesId},
            success: function (data) {
                $('#formData').html(data);
            }
        });
    }
</script>
