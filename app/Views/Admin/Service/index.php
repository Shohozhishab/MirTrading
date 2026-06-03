<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Service  <small>Service List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Service</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <?php echo $menu;?>
            </div>

            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-9">
                                <h3 class="box-title">Service List</h3>
                            </div>
                            <div class="col-lg-3">
                                <a href="javascript:void(0)"
                                   onclick="showData('<?php echo site_url('/Admin/Service_ajax/create/'); ?>','<?php echo '/Admin/Service/create/'; ?>'),activeTab(this)"
                                   class="btn btn-block btn-primary">Add</a>
                            </div>
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>


                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-lg-12">
                            <form action="<?= base_url('Admin/Service')?>" method="get">
                                <div class="col-xs-3" style="padding: 17px;">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" name="st_date" value="<?= $st_date; ?>" id="st_date" required>
                                </div>
                                <div class="col-xs-3" style="padding: 17px;">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" name="en_date" value="<?= $en_date; ?>" id="en_date" required>
                                </div>
                                <div class="col-xs-3" style="padding: 18px;">
                                    <button style="margin-top: 22px;" class="btn btn-primary " type="submit">Filter </button>
                                </div>
                            </form>
                        </div>
                        <table id="example1" class="table table-bordered table-striped text-capitalize">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $start=1; foreach ($services as $val) {  ?>
                                <tr>
                                    <td width="80px"><?php echo $start++ ?></td>
                                    <td><?php echo $val->title ?></td>
                                    <td><?php echo showServiceName($val->service_type) ?></td>
                                    <td><?php echo showWithCurrencySymbol(get_data_by_id('final_amount','service_invoice','service_invoice_id',$val->service_invoice_id)) ?></td>
                                    <td width="180px">
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Service_invoice_ajax/view/'.$val->service_invoice_id); ?>','<?php echo '/Admin/Service_invoice/view/'.$val->service_invoice_id; ?>')" class="btn btn-warning btn-xs">View</a>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>

                        <div class="row no-print" >
                            <div class="col-xs-12">
                                <button onclick="printDiv('ledgPrint')" class="print_line btn btn-primary pull-right" ><i class="fa fa-print "></i> Print Now</button>
                                <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('ledgPrint','service')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('ledgPrint','service')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
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
                                        <th>No</th>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $start=1; foreach ($services as $val) {  ?>
                                        <tr>
                                            <td width="80px"><?php echo $start++ ?></td>
                                            <td><?php echo $val->title ?></td>
                                            <td><?php echo showServiceName($val->service_type) ?></td>
                                            <td><?php echo showWithCurrencySymbol(get_data_by_id('final_amount','service_invoice','service_invoice_id',$val->service_invoice_id)) ?></td>
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
