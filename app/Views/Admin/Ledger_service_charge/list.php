<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Ledger Service Charge<small>Ledger Service Charge</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Ledger Service Charge</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="col-xs-12" style="margin-bottom: 15px;">
            <?php echo $menu;?>
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
                                        <select class="form-control" onchange="rolePermission(this.value,'Ledger_service_charge')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="Ledger_service_charge">
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
                            <form action="<?= base_url('Admin/Ledger_service_charge') ?>" method="get" onsubmit="return false;">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Start Date</label>
                                        <input type="date" class="form-control" name="st_date" value="<?= $st_date; ?>"
                                               id="st_date" onchange="serviceChargeLedgerFilter()">
                                    </div>
                                    <div class="col-md-3">
                                        <label>End Date</label>
                                        <input type="date" class="form-control" name="en_date" value="<?= $en_date; ?>"
                                               id="en_date" onchange="serviceChargeLedgerFilter()">
                                    </div>

                                    <div class="col-md-2" style="margin-top: 25px;">
                                        <button type="button" onclick="serviceChargeLedgerFilter()" class="btn btn-primary btn-block"><i class="fa fa-search"></i>
                                            Filter
                                        </button>
                                    </div>
                                    <div class="col-md-2" style="margin-top: 25px;">
                                        <button type="button" onclick="resetServiceChargeLedgerFilter()" class="btn btn-default btn-block"><i
                                                    class="fa fa-refresh"></i> Reset</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php
            $currentShopId = isset($id) ? $id : (isset($_SESSION['shopId']) ? $_SESSION['shopId'] : 0);
            $start_date = !empty($st_date) ? $st_date : 0;
            $end_date = !empty($en_date) ? $en_date : 0;
            $serviceChargeBalance = get_data_by_id('service_charge', 'shops', 'sch_id', $currentShopId);
            $rowsData = isset($ledger_service_charge) ? $ledger_service_charge : [];
            ?>
            <div class="col-xs-12">

                <div id="serviceChargeLedgerResult">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-lg-12" style="margin-top: 20px;">
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                        </div>

                        <table class="table table-bordered table-striped" id="example1">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Date</th>
                                <th>Particulars</th>
                                <th>Service Id</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>Balance</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $totalRows = count($rowsData)-1;
                            for ($i = 0; $i <= $totalRows; $i++) {
                                $particulars = ($rowsData[$i]->particulars == NULL) ? "Payment" : $rowsData[$i]->particulars;
                                $amountCr = ($rowsData[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($rowsData[$i]->amount);
                                $amountDr =($rowsData[$i]->trangaction_type != "Dr.")?"---":showWithCurrencySymbol($rowsData[$i]->amount);
                                $serviceInv = !empty($rowsData[$i]->service_invoice_id) ? '<a href="' . base_url('Admin/Service_invoice/view/' . $rowsData[$i]->service_invoice_id) . '">SINV_' . $rowsData[$i]->service_invoice_id . '</a>' : '---';
                                ?>
                                <tr>
                                    <td><?php echo $rowsData[$i]->ledger_service_charge_id ?></td>
                                    <td><?php echo $rowsData[$i]->createdDtm ?></td>
                                    <td><?php echo $particulars ?></td>
                                    <td><?php echo $serviceInv ?></td>
                                    <td><?php echo $amountDr ?></td>
                                    <td><?php echo $amountCr ?></td>
                                    <td><?php echo showWithCurrencySymbol($rowsData[$i]->r_balance) ?></td>
                                </tr>
                            <?php }?>

                            </tbody>
                            <tfoot>
                            <tr>
                                <th>Id</th>
                                <th>Date</th>
                                <th>Particulars</th>
                                <th>Service Id</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>Balance</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>Total</th>
                                <th>= <?php echo showWithCurrencySymbol(get_total_nogodBalance('ledger_service_charge', 'amount', 'Dr.', $start_date, $end_date)); ?></th>
                                <th>= <?php echo showWithCurrencySymbol(get_total_nogodBalance('ledger_service_charge', 'amount', 'Cr.', $start_date, $end_date)); ?></th>
                                <th>= <?php echo showWithCurrencySymbol($serviceChargeBalance); ?></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                </div>

                <div style="display: none;">
                    <div id="ledgPrint">
                        <?php echo view('Admin/Ledger_service_charge/print_table', ['ledger_service_charge' => $rowsData, 'st_date' => $st_date, 'en_date' => $en_date]); ?>
                    </div>
                </div>

                <div class="row no-print" >
                    <div class="col-xs-12">
                        <?php if (isset($print) && $print == 1){ ?>
                            <button onclick="printDiv('ledgPrint')" class="print_line btn btn-primary pull-right" ><i class="fa fa-print "></i> Print Now</button>
                        <?php } ?>
                        <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                            <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('ledgPrint','service_charge')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                        <?php } ?>
                        <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                            <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('ledgPrint','service_charge')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
