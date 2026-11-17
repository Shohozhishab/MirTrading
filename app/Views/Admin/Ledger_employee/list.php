<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Employee Ledger <small>Employee Ledger</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Employee Ledger</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="col-xs-12" style="margin-bottom: 15px;">
            <a href="#" onclick="showData('<?php echo site_url('/Admin/Employee_ajax/'); ?>','<?php echo '/Admin/Employee/';?>')" class="btn btn-default">Employee</a>
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
                                        <select class="form-control" onchange="rolePermission(this.value,'Ledger_employee')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="Ledger_employee">
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

            <div class="col-xs-12">
                <?php if (isset($filter) && $filter == 1){ ?>
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-filter"></i> Filter </h3>
                        </div>
                        <div class="box-body">
                            <form action="<?= base_url('Admin/Ledger_employee')?>" method="get" onsubmit="return false;">
                                <div class="col-md-2" >
                                    <label>Employee name</label>
                                    <select class="form-control select2" name="employee_id" onchange="employeeLedgerFilter()" id="employeeId" style="width: 100%;" required>
                                        <option value="">Please Select</option>
                                        <?php echo getAllListInOption($employee_id ?? '', 'employee_id', 'name', 'employee'); ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" name="st_date" value="<?= $st_date ?? ''; ?>" id="st_date" onchange="employeeLedgerFilter()">
                                </div>
                                <div class="col-md-3">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" name="en_date" value="<?= $en_date ?? ''; ?>" id="en_date" onchange="employeeLedgerFilter()">
                                </div>

                                <div class="col-md-2" style="margin-top: 25px;">
                                    <button type="button" onclick="employeeLedgerFilter()" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Filter</button>
                                </div>
                                <div class="col-md-2" style="margin-top: 25px;">
                                    <button type="button" onclick="resetEmployeeLedgerFilter()" class="btn btn-default btn-block"><i class="fa fa-refresh"></i> Reset</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php } ?>

                <?php
                $name = !empty($employee_id) ? get_data_by_id('name', 'employee', 'employee_id', $employee_id) : '';
                $balance = !empty($employee_id) ? get_data_by_id('balance', 'employee', 'employee_id', $employee_id) : 0;
                $start_date = !empty($st_date) ? $st_date : 0;
                $end_date = !empty($en_date) ? $en_date : 0;
                $resultList = isset($result) ? $result : [];
                ?>
                <div id="employeeLedgerResult">
                    <?php if (!empty($employee_id)){ ?>
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Employee: <?= $name ?></h3>
                                <span class="pull-right">
                                    <table class="table table-bordered table-striped" id="TFtable">
                                        <tr>
                                            <td>Total Paid:</td>
                                            <td><?= showWithCurrencySymbol(get_total('ledger_employee', 'amount', 'Dr.', 'employee_id', $employee_id, $start_date, $end_date)) ?></td>
                                        </tr>
                                        <tr>
                                            <td>Total Received:</td>
                                            <td><?= showWithCurrencySymbol(get_total('ledger_employee', 'amount', 'Cr.', 'employee_id', $employee_id, $start_date, $end_date)) ?></td>
                                        </tr>
                                        <tr>
                                            <td>Balance:</td>
                                            <td><?= showWithCurrencySymbol($balance) ?></td>
                                        </tr>
                                    </table>
                                </span>
                            </div>

                            <div class="box-body">
                                <table class="table table-bordered table-striped" id="example1">
                                    <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Date</th>
                                        <th>Particulars</th>
                                        <th>Memo</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                        <th>Balance</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $totalRows = count($resultList);
                                    for ($i = 0; $i < $totalRows; $i++) {
                                        $particulars = ($resultList[$i]->particulars == NULL) ? "Transaction" : $resultList[$i]->particulars;
                                        $amountCr = ($resultList[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($resultList[$i]->amount);
                                        $amountDr = ($resultList[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($resultList[$i]->amount);
                                        $transMemo = !empty($resultList[$i]->trans_id) ? '<a href="' . site_url('Admin/Transaction/read/' . $resultList[$i]->trans_id) . '">TRNS_' . $resultList[$i]->trans_id . '</a>' : '---';
                                        ?>
                                        <tr>
                                            <td><?= $resultList[$i]->ledg_emp_id ?></td>
                                            <td><?= $resultList[$i]->createdDtm ?></td>
                                            <td><?= $particulars ?></td>
                                            <td><?= $transMemo ?></td>
                                            <td><?= $amountDr ?></td>
                                            <td><?= $amountCr ?></td>
                                            <td><?= showWithCurrencySymbol($resultList[$i]->r_balance) ?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>Id</th>
                                        <th>Date</th>
                                        <th>Particulars</th>
                                        <th>Memo</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                        <th>Balance</th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>Total</th>
                                        <th>= <?= showWithCurrencySymbol(get_total('ledger_employee', 'amount', 'Dr.', 'employee_id', $employee_id, $start_date, $end_date)) ?></th>
                                        <th>= <?= showWithCurrencySymbol(get_total('ledger_employee', 'amount', 'Cr.', 'employee_id', $employee_id, $start_date, $end_date)) ?></th>
                                        <th><?= showWithCurrencySymbol($balance) ?></th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <div style="display: none;">
                    <div id="ledgPrint">
                        <?php echo view('Admin/Ledger_employee/print_table', [
                            'result' => $resultList,
                            'employee_id' => $employee_id,
                            'st_date' => $st_date,
                            'en_date' => $en_date,
                            'shopId' => $shopId ?? ($id ?? ($this->session->shopId ?? 0)),
                        ]); ?>
                    </div>
                </div>

                <div class="row no-print">
                    <div class="col-xs-12" style="margin-bottom: 20px;">
                        <?php if (isset($print) && $print == 1){ ?>
                            <button onclick="printDiv('ledgPrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print"></i> Print Now</button>
                        <?php } ?>
                        <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                            <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('ledgPrint','employee')"><i class="fa fa-file-pdf-o"></i> Download PDF </button>
                        <?php } ?>
                        <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                            <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('ledgPrint','employee')"><i class="fa fa-file-excel-o"></i> Download CSV</button>
                        <?php } ?>
                    </div>
                </div>

            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
