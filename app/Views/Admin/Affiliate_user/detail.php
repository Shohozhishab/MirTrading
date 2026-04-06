<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Detail <small>Detail</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Detail</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <a href="#" onclick="showData('<?php echo site_url('/Admin/Affiliate_user_ajax'); ?>','<?php echo '/Admin/Affiliate_user';?>')"  class="btn btn-default"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to list</a>
            </div>
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-8">
                                <h3 class="box-title">Detail</h3>
                            </div>
                            <div class="col-lg-4">
                                <div style="display: flex; justify-content: space-between;font-size: 15px ">
                                    <b>Total Commission:</b>
                                    <?= showWithCurrencySymbol($commissionAmount);?>
                                </div>
                                <div style="display: flex; justify-content: space-between;font-size: 15px ">
                                    <b>Total Commission Pay:</b>
                                    <?= showWithCurrencySymbol($commissionPayAmount);?>
                                </div>
                                <div style="display: flex; justify-content: space-between;font-size: 15px ">
                                    <b>Total Commission Due:</b>
                                    <?= showWithCurrencySymbol($totalDue);?>
                                </div>
                            </div>
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <div id="message"></div>
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
                                <th>Name</th>
                                <th>Commission</th>
                                <th>Commission Amount</th>
                                <th>Invoice</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $start=1; foreach ($commission as $val) {
                                ?>
                                <tr>
                                    <td width="80px"><?php echo $start++ ?></td>
                                    <td><?php echo get_data_by_id('name','affiliate_user','affiliate_user_id',$val->affiliate_user_id); ?></td>
                                    <td><?php echo $val->commission ?> %</td>
                                    <td><?php echo showWithCurrencySymbol($val->commission_amount) ?></td>
                                    <td><?php $invoice_id = get_data_by_id('invoice_id','sales','sales_id',$val->sales_id);?>
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Invoice_ajax/view/'.$invoice_id); ?>','<?php echo '/Admin/Invoice/view/'.$invoice_id; ?>')" class="btn btn-warning btn-xs">View</a>
                                    </td>
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

