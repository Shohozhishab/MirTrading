<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Transaction <small>Transaction List</small></h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Transaction</li>
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
                                        <select class="form-control" onchange="rolePermission(this.value,'Transaction')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="Transaction">
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
                        <h3 class="box-title"><i class="fa fa-filter"></i> Filter Transactions</h3>
                    </div>
                    <div class="box-body">
                        <form action="<?= base_url('Admin/Transaction') ?>" method="get" id="filterForm" onsubmit="return false;">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" name="st_date" value="<?= $st_date; ?>" id="st_date" onchange="transactionFilter()" required>
                                </div>
                                <div class="col-md-3">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" name="en_date" value="<?= $en_date; ?>" id="en_date" onchange="transactionFilter()" required>
                                </div>

                                <input type="hidden" name="category" id="hidden_category" value="<?php echo $active_category ?? ''; ?>">
                                <input type="hidden" name="customer_id" id="hidden_customer_id" value="<?php echo $customer_id_filter ?? ''; ?>">
                                <input type="hidden" name="supplier_id" id="hidden_supplier_id" value="<?php echo $supplier_id_filter ?? ''; ?>">
                                <input type="hidden" name="loan_pro_id" id="hidden_loan_pro_id" value="<?php echo $loan_pro_id_filter ?? ''; ?>">
                                <input type="hidden" name="bank_id" id="hidden_bank_id" value="<?php echo $bank_id_filter ?? ''; ?>">
                                <input type="hidden" name="employee_id" id="hidden_employee_id" value="<?php echo $employee_id_filter ?? ''; ?>">

                                <div class="col-md-2" style="margin-top: 25px;">
                                    <button type="button" onclick="transactionFilter()" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Filter</button>
                                </div>
                                <div class="col-md-2" style="margin-top: 25px;">
                                    <button type="button" onclick="resetTransactionFilter()" class="btn btn-default btn-block"><i class="fa fa-refresh"></i> Reset</button>
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
                   onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/create/'); ?>','<?php echo '/Admin/Transaction/create/'; ?>')"
                   class="btn btn-success"><i class="fa fa-plus"></i> Add Transaction</a>
            </div>
            <?php } ?>
            <div class="col-xs-12">
                <div id="transactionResult">
                    <?php echo view('Admin/Transaction/table_list', [
                        'transaction_data' => $transaction_data,
                        'customers' => $customers,
                        'suppliers' => $suppliers,
                        'loan_providers' => $loan_providers,
                        'banks' => $banks,
                        'employees' => $employees,
                        'st_date' => $st_date ?? '',
                        'en_date' => $en_date ?? '',
                        'active_category' => $active_category ?? '',
                        'customer_id_filter' => $customer_id_filter ?? '',
                        'supplier_id_filter' => $supplier_id_filter ?? '',
                        'loan_pro_id_filter' => $loan_pro_id_filter ?? '',
                        'bank_id_filter' => $bank_id_filter ?? '',
                        'employee_id_filter' => $employee_id_filter ?? '',
                        'transaction_flow' => $transaction_flow ?? 0,
                        'read' => $read ?? 0,
                        'update' => $update ?? 0,
                        'delete' => $delete ?? 0,
                        'print' => $print ?? 0,
                        'download_PDF' => $download_PDF ?? 0,
                        'download_CSV' => $download_CSV ?? 0,
                    ]); ?>
                </div>
            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>


<div class="modal fade" id="modal-default">
    <div class="modal-dialog">
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
    function cusTranEdit(tranId) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/cusDataEdit') ?>",
            data: {
                id: tranId
            },
            success: function(data) {
                $('#formData').html(data);
            }
        });
    }

    function submitForm(formId) {
        $('#' + formId).submit();
    }

    function supplierTranEdit(tranId) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/supplierDataEdit') ?>",
            data: {
                id: tranId
            },
            success: function(data) {
                $('#formData').html(data);
            }
        });
    }

    function accountTranEdit(tranId) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/accountDataEdit') ?>",
            data: {
                id: tranId
            },
            success: function(data) {
                $('#formData').html(data);
            }
        });
    }

    function fundTranEdit(tranId) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/fundDataEdit') ?>",
            data: {
                id: tranId
            },
            success: function(data) {
                $('#formData').html(data);
            }
        });
    }

    function expenseTranEdit(tranId) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/expenseDataEdit') ?>",
            data: {
                id: tranId
            },
            success: function(data) {
                $('#formData').html(data);
            }
        });
    }

    function employeeTranEdit(tranId) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/employeeDataEdit') ?>",
            data: {
                id: tranId
            },
            success: function(data) {
                $('#formData').html(data);
            }
        });
    }

    function otherSalesTranEdit(tranId) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/otherSalesDataEdit') ?>",
            data: {
                id: tranId
            },
            success: function(data) {
                $('#formData').html(data);
            }
        });
    }

    function vatTranEdit(tranId) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/vatDataEdit') ?>",
            data: {
                id: tranId
            },
            success: function(data) {
                $('#formData').html(data);
            }
        });
    }

    function filterByCategory(category, entityId, value) {
        $('#hidden_customer_id').val('');
        $('#hidden_supplier_id').val('');
        $('#hidden_loan_pro_id').val('');
        $('#hidden_bank_id').val('');
        $('#hidden_employee_id').val('');

        $('#hidden_category').val(category);
        if (entityId && value) {
            $('#' + entityId).val(value);
        }
        transactionFilter();
    }

    function assetsTranEdit(tranId) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/assetsDataEdit') ?>",
            data: {
                id: tranId
            },
            success: function(data) {
                $('#formData').html(data);
            }
        });
    }

    function otherIncomeTranEdit(tranId) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/otherIncomeDataEdit') ?>",
            data: {
                id: tranId
            },
            success: function(data) {
                $('#formData').html(data);
            }
        });
    }
</script>
