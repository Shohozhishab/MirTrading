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
        <div class="row">

            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-9">
                                <h3 class="box-title">Affiliate User List</h3>
                            </div>
                            <div class="col-lg-3">
                                <a href="javascript:void(0)"
                                   onclick="showData('<?php echo site_url('/Admin/Affiliate_user_ajax/create/'); ?>','<?php echo '/Admin/Affiliate_user/create/'; ?>')"
                                   class="btn btn-block btn-primary">Add</a>
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
                                    <td><?php echo $val->commission ?></td>
                                    <td><?php echo showWithCurrencySymbol($val->balance) ?></td>
                                    <td>
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Affiliate_user_ajax/commissionPay/' . $val->affiliate_user_id); ?>','<?php echo '/Admin/Affiliate_user/commissionPay/' . $val->affiliate_user_id; ?>')"
                                           class="btn btn-success btn-xs">Commission Pay</a>
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Affiliate_user_ajax/update/' . $val->affiliate_user_id); ?>','<?php echo '/Admin/Affiliate_user/update/' . $val->affiliate_user_id; ?>')"
                                           class="btn btn-warning btn-xs">Update</a>
                                        <?php if($isDeletable == true){ ?>
                                            <a href="<?php echo site_url('/Admin/Affiliate_user/delete/' . $val->affiliate_user_id); ?>" onclick="return confirm('Are you sure you want to delete this item?');"  class="btn btn-danger btn-xs">Delete</a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>

                            </tbody>
                        </table>
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
