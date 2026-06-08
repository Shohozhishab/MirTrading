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
            <div class="col-xs-12" >
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-filter"></i> Filter </h3>
                    </div>
                    <div class="box-body">
                        <form action="<?= base_url('Admin/Affiliate_commission_pay') ?>" method="get">
                            <div class="row">
                                <div class="col-md-2" >
                                    <label for="int">Affiliate User</label>
                                    <select class="form-control select2 select2-hidden-accessible" name="affiliate_user_id" id="affiliate_user_id" style=" width: 100%;" tabindex="-1" aria-hidden="true">
                                        <option selected="selected" value="">Please Select</option>
                                        <?php foreach ($user as $item) { ?>
                                            <option value="<?= $item->affiliate_user_id; ?>" <?= ($affiliate_user_id == $item->affiliate_user_id) ? 'selected' : ''; ?>><?= $item->name; ?></option>
                                        <?php } ?>
                                    </select>

                                </div>
                                <div class="col-md-3">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" name="st_date" value="<?= $st_date; ?>"
                                           id="st_date" >
                                </div>
                                <div class="col-md-3">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" name="en_date" value="<?= $en_date; ?>"
                                           id="en_date" >
                                </div>

                                <div class="col-md-2" style="margin-top: 25px;">
                                    <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search"></i>
                                        Filter
                                    </button>
                                </div>
                                <div class="col-md-2" style="margin-top: 25px;">
                                    <a href="<?= base_url('Admin/Affiliate_commission_pay') ?>" class="btn btn-default btn-block"><i
                                                class="fa fa-refresh"></i> Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="box">
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
                                <?php $start = 1;
                                foreach ($commission as $val) {
                                ?>
                                    <tr>
                                        <td width="80px"><?php echo $start++ ?></td>
                                        <td><?php echo get_data_by_id('name', 'affiliate_user', 'affiliate_user_id', $val->affiliate_user_id); ?></td>
                                        <td><?php echo showWithCurrencySymbol($val->amount) ?></td>
                                        <td><?php echo $val->date ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <div class="row no-print">
                            <div class="col-xs-12">
                                <button onclick="printDiv('ledgPrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print "></i> Print Now</button>
                                <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('ledgPrint','sales')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('ledgPrint','sales')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                            </div>
                        </div>

                        <div class="col-md-12" id="ledgPrint" style="display: none; text-transform: capitalize; ">
                            <div class="col-xs-12" style="margin-bottom: 20px;   ">
                                <div class="col-xs-6">
                                    <?php if (logo_image() == NULL) { ?>
                                        <img src="<?php echo base_url() ?>/uploads/schools/no_image.jpg" alt="User Image">
                                    <?php } else { ?>
                                        <img src="<?php echo base_url(); ?>/uploads/schools/<?php echo logo_image(); ?>" class="" alt="User Image">
                                    <?php } ?>
                                </div>
                                <div class="col-xs-6">
                                    <?php print address(); ?>
                                </div>
                            </div>
                            <div class="col-md-12">

                                <table class="table table-bordered table-striped text-capitalize">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Name</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $start = 1; foreach ($commission as $val) { ?>
                                            <tr>
                                                <td width="80px"><?php echo $start++ ?></td>
                                                <td><?php echo get_data_by_id('name', 'affiliate_user', 'affiliate_user_id', $val->affiliate_user_id); ?></td>
                                                <td><?php echo showWithCurrencySymbol($val->amount) ?></td>
                                                <td><?php echo $val->date ?></td>
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