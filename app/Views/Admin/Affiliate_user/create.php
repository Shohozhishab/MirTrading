<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Affiliate User <small>Affiliate User Create</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Affiliate User </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Affiliate User Create</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div id="message"></div>
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            <div class="col-lg-6" >
                                <form id="geniusform" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="varchar">Name</label>
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Name" required>
                                        <div class="error"></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="varchar">commission</label>
                                        <input type="number" class="form-control" name="commission" id="commission" placeholder="commission %" required>
                                        <div class="error"></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="varchar">Address</label>
                                        <textarea class="form-control" name="address" id="address"></textarea>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="varchar">Phone</label>
                                        <input type="number" class="form-control" name="phone" id="phone" placeholder="phone" required>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="longtext">Password </label>
                                        <input type="password" class="form-control" name="password" id="password" placeholder="Password" required />
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="longtext">Confirm Password</label>
                                        <input type="password" class="form-control" name="con_password" id="con_password" placeholder="Confirm Password" required>
                                        <div class="error"></div>
                                    </div>


                                    <button type="button" class="btn btn-primary" onclick="affiliateUserValidat()"  >Create</button>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Affiliate_user_ajax/'); ?>','<?php echo '/Admin/Affiliate_user/'; ?>')" class="btn btn-default">Cancel</a>
                                </form>
                            </div>


                            <div class="col-lg-6" style="border-left: 1px solid #cecdcd;">

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