<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Capital <small>Capital  Deposit</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Capital </li>
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
        <div class="row">

            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Capital Deposit</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div id="message"></div>
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            <div class="col-lg-6">
                                <form id="geniusform" action="<?php echo base_url('Admin/Capital/deposit') ?>" method="post">
                                    <div class="form-group">
                                        <label for="particulars">Particulars</label>
                                        <textarea class="form-control" rows="3" name="particulars" id="particulars" placeholder="Particulars" required ></textarea>
                                    </div>

                                    <div class="form-group databank" id="chaque">
                                        <label for="int">Amount</label>
                                        <input type="number" class="form-control" name="amount" id="amount" oninput="minusValueCheck(this.value,this)" placeholder="Amount" required />
                                    </div>
                                    <button type="submit" class="btn btn-primary geniusSubmit-btn">Deposit</button>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Settings_ajax/'); ?>','<?php echo '/Admin/Settings/'; ?>')" class="btn btn-default">Cancel</a>
                                </form>
                            </div>
                            <div class="col-lg-6"></div>
                        </div>

                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>