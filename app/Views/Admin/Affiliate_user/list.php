<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Affiliate User  <small>Affiliate User List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Affiliate User</li>
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
                                        <select class="form-control" onchange="rolePermission(this.value,'AffiliateUser')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="AffiliateUser">
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
                        <div class="row">
                            <div class="col-lg-9">
                                <h3 class="box-title">Affiliate User List</h3>
                            </div>
                            <div class="col-lg-3">
                                <?php if (isset($create) && $create == 1){ ?>
                                <a href="javascript:void(0)"
                                   onclick="showData('<?php echo site_url('/Admin/Affiliate_user_ajax/create/'); ?>','<?php echo '/Admin/Affiliate_user/create/'; ?>')"
                                   class="btn btn-block btn-primary">Add</a>
                                <?php } ?>
                            </div>
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
                                <th>User Id</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Commission</th>
                                <th>Balance</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $start = 1;
                            foreach ($user as $val) {
                                $isDeletable = is_deletable('commission','affiliate_user_id',$val->affiliate_user_id);
                                ?>
                                <tr>
                                    <td width="80px"><?php echo $start++ ?></td>
                                    <td><?php echo $val->affiliate_user_id ?></td>
                                    <td><?php echo $val->name ?></td>
                                    <td><?php echo showWithPhoneNummberCountryCode($val->phone) ?></td>
                                    <td><?php echo $val->commission ?> %</td>
                                    <td><?php echo showWithCurrencySymbol($val->balance) ?></td>
                                    <td>
                                        <a href="<?= base_url('Admin/Affiliate_commission?affiliate_user_id='. $val->affiliate_user_id)?>" class="btn btn-primary btn-xs">Commission List</a>
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Affiliate_user_ajax/commissionPay/' . $val->affiliate_user_id); ?>','<?php echo '/Admin/Affiliate_user/commissionPay/' . $val->affiliate_user_id; ?>')"
                                           class="btn btn-success btn-xs">Commission Pay</a>
                                        <?php if (isset($read) && $read == 1){ ?>
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Affiliate_user_ajax/detail/' . $val->affiliate_user_id); ?>','<?php echo '/Admin/Affiliate_user/detail/' . $val->affiliate_user_id; ?>')"
                                           class="btn btn-info btn-xs">Detail</a>
                                        <?php } ?>
                                        <?php if (isset($update) && $update == 1){ ?>
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Affiliate_user_ajax/update/' . $val->affiliate_user_id); ?>','<?php echo '/Admin/Affiliate_user/update/' . $val->affiliate_user_id; ?>')"
                                           class="btn btn-warning btn-xs">Update</a>
                                        <?php } ?>
                                        <?php if (isset($delete) && $delete == 1){ ?>
                                        <?php if($isDeletable == true){ ?>
                                            <a href="<?php echo site_url('/Admin/Affiliate_user/delete/' . $val->affiliate_user_id); ?>" onclick="return confirm('Are you sure you want to delete this item?');"  class="btn btn-danger btn-xs">Delete</a>
                                        <?php } ?>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>

                            </tbody>
                        </table>

                        <div class="row no-print" >
                            <div class="col-xs-12">
                                <button onclick="printDiv('ledgPrint')" class="print_line btn btn-primary pull-right" ><i class="fa fa-print "></i> Print Now</button>
                                <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('ledgPrint','affiliateUser')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('ledgPrint','affiliateUser')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                            </div>
                        </div>

                        <div class="col-md-12" id="ledgPrint" style="display: none; text-transform: capitalize; " >
                            <div class="col-xs-12" style="margin-bottom: 20px;   ">
                                <div class="col-xs-6">
                                    <?php if(logo_image() == NULL){ ?>
                                        <img src="<?php echo base_url() ?>/uploads/schools/no_image.jpg" alt="User Image" >
                                    <?php }else{ ?>
                                        <img src="<?php echo base_url(); ?>/uploads/schools/<?php echo logo_image(); ?>" class="" alt="User Image">
                                    <?php } ?>
                                </div>
                                <div class="col-xs-6">
                                    <?php print address(); ?>
                                </div>
                            </div>
                            <div class="col-md-12" >
                                <table class="table table-bordered table-striped text-capitalize">
                                    <thead>
                                    <tr>
                                        <th>User Id</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Commission</th>
                                        <th>Balance</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($user as $val) { ?>
                                        <tr>
                                            <td><?php echo $val->affiliate_user_id ?></td>
                                            <td><?php echo $val->name ?></td>
                                            <td><?php echo showWithPhoneNummberCountryCode($val->phone) ?></td>
                                            <td><?php echo $val->commission ?> %</td>
                                            <td><?php echo showWithCurrencySymbol($val->balance) ?></td>
                                        </tr>
                                    <?php } ?>

                                    </tbody>
                                </table>
                            </div>
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
