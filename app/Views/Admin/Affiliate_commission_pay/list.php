<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Commission Pay <small>Commission Pay List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Bank</li>
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
                                <h3 class="box-title">Commission Pay List</h3>
                            </div>
                            <div class="col-lg-3">
                            </div>
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>

                            <div class="col-lg-12">
                                <form action="<?= base_url('/Admin/Affiliate_commission_pay')?>" method="get">
                                    <div class="col-md-3" style="padding: 20px;">
                                        <label for="int">Affiliate User</label>
                                        <select class="form-control select2 select2-hidden-accessible" name="affiliate_user_id" id="affiliate_user_id" style=" width: 100%;" tabindex="-1" aria-hidden="true">
                                            <option selected="selected" value="">Please Select</option>
                                            <?php foreach ($user as $item){ ?>
                                                <option value="<?= $item->affiliate_user_id;?>" <?= ($affiliate_user_id == $item->affiliate_user_id)?'selected':''; ?> ><?= $item->name;?></option>
                                            <?php } ?>
                                        </select>

                                    </div>
                                    <div class="col-xs-3" style="padding: 17px;">
                                        <label>Start Date</label>
                                        <input type="date" class="form-control" name="st_date" value="<?= $st_date?>" id="st_date" >
                                    </div>
                                    <div class="col-xs-3" style="padding: 17px;">
                                        <label>End Date</label>
                                        <input type="date" class="form-control" name="en_date" id="en_date" value="<?= $en_date?>" >
                                    </div>
                                    <div class="col-xs-3" style="padding: 18px;">
                                        <button class="btn btn-primary " style="margin-top: 25px;" type="submit">Filter </button>
                                    </div>
                                </form>
                            </div>
                        </div>


                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped text-capitalize">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $start=1; foreach ($commission as $val) {
                                ?>
                                <tr>
                                    <td width="80px"><?php echo $start++ ?></td>
                                    <td><?php echo get_data_by_id('name','affiliate_user','affiliate_user_id',$val->affiliate_user_id); ?></td>
                                    <td><?php echo showWithCurrencySymbol($val->amount) ?></td>
                                    <td><?php echo $val->date ?></td>
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
