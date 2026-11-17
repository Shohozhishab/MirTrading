<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Replace Product <small>List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Replace Product</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="col-xs-12" style="margin-bottom: 15px;">
            <?php echo $menu;?>
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
                                        <select class="form-control" onchange="rolePermission(this.value,'ExchangeProduct')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="ExchangeProduct">
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


            <div class="col-xs-12" >
                <div class="box box-primary">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <h3 class="box-title">Replace Product List</h3>
                            </div>
                            <div class="col-lg-6">
                                <form method="post" action="<?php echo site_url('Admin/Exchange_product/invoice_search') ?>"  >
                                    <div class="col-lg-4 pull-right">
                                        <button style="margin-top: 25px;" class="btn btn-primary " type="submit">Search</button>
                                    </div>
                                    <div class="col-lg-8 pull-right">
                                        <label>Input Invoice ID</label>
                                        <input type="text" class="form-control" name="invoiceId" id="invoiceId"  required>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-12">

                <?php if (isset($filter) && $filter == 1){ ?>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-filter"></i> Filter </h3>
                    </div>
                    <div class="box-body">
                        <form action="<?= base_url('Admin/Exchange_product')?>" method="get" onsubmit="return false;">
                            <div class="row">
                                <div class="col-lg-6" style="margin-bottom: 15px;">
                                    <label>Customer</label><br>
                                    <select class="form-control select2" name="customer" id="customerId" onchange="exchangeProductFilter()" style="width: 100%;">
                                        <option value="">Please Select</option>
                                        <?= getAllListInOptionWithStatus( $customerId, 'customer_id', 'customer_name', 'customers', 'customer_name' );?>
                                    </select>
                                </div>
                                <div class="col-lg-6" style="margin-bottom: 15px;">
                                    <label for="varchar">Conditional Status </label><br>
                                    <select class="form-control select2" name="status" id="status" onchange="exchangeProductFilter()" style="width: 100%;">
                                        <option value="" >Please Select</option>
                                        <option value="1" <?= ($status == '1')?'selected':'';?> >Received From Customer</option>
                                        <option value="2" <?= ($status == '2')?'selected':'';?> >Sent to Warehouse</option>
                                        <option value="3" <?= ($status == '3')?'selected':'';?> >Received From Warehouse</option>
                                        <option value="4" <?= ($status == '4')?'selected':'';?> >Complete</option>
                                        <option value="5" <?= ($status == '5')?'selected':'';?> >Canceled with no return</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" name="st_date" value="<?= $st_date; ?>" id="st_date" onchange="exchangeProductFilter()">
                                </div>
                                <div class="col-md-4">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" name="en_date" value="<?= $en_date; ?>" id="en_date" onchange="exchangeProductFilter()">
                                </div>
                                <div class="col-md-2" style="margin-top: 25px;">
                                    <button type="button" onclick="exchangeProductFilter()" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Filter </button>
                                </div>
                                <div class="col-md-2" style="margin-top: 25px;">
                                    <button type="button" onclick="resetExchangeProductFilter()" class="btn btn-default btn-block"><i class="fa fa-refresh"></i> Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <?php } ?>

                <div id="exchangeProductResult">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>

                            <table id="example1" class="table table-bordered table-striped text-capitalize">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Customer</th>
                                    <th>Quantity</th>
                                    <th>Comment</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $totalQty = 0;
                                $i = 1;
                                foreach ($exchangeProduct as $item) {
                                    $qty = ($item->type == 'Conditional')
                                        ? (get_exchange_conditional_product_qty_by_id($item->exchange_pro_id) ?? 0)
                                        : (get_exchange_unconditional_product_qty_by_id($item->exchange_pro_id) ?? 0);
                                    $totalQty += $qty;
                                ?>
                                    <tr>
                                        <td><?php echo $i++ ?></td>
                                        <td><?php echo globalTimeStamp($item->createdDtm); ?></td>
                                        <td><?php echo $item->type; ?></td>
                                        <td><?php echo get_exchange_status_by_id($item->exchange_pro_id); ?></td>
                                        <td><?php
                                            if(!empty($item->customer_id)){
                                                echo get_data_by_id('customer_name','customers','customer_id',$item->customer_id);
                                            }else{
                                                echo $item->customer_name;
                                            }
                                            ?></td>
                                        <td><?php echo $qty; ?></td>
                                        <td><?php echo $item->comment; ?></td>
                                        <td>
                                            <?php if (isset($read) && $read == 1){ ?>
                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Exchange_product_ajax/view/'.$item->exchange_pro_id); ?>','<?php echo '/Admin/Exchange_product/view/'.$item->exchange_pro_id; ?>')" class="btn btn-primary btn-xs">View</a>
                                            <?php } ?>
                                            <?php if (isset($update) && $update == 1){ ?>
                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Exchange_product_ajax/update/' . $item->exchange_pro_id); ?>','<?php echo '/Admin/Exchange_product/update/' . $item->exchange_pro_id; ?>')" class="btn btn-warning btn-xs">Update</a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>

                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Customer</th>
                                    <th>Quantity</th>
                                    <th>Comment</th>
                                    <th>Action</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>Total</th>
                                    <th>= <?php echo $totalQty; ?></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row no-print" >
                    <div class="col-xs-12">
                        <?php if (isset($print) && $print == 1){ ?>
                        <button onclick="printDiv('ledgPrint')" class="print_line btn btn-primary pull-right" ><i class="fa fa-print "></i> Print Now</button>
                        <?php } ?>
                        <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                        <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('ledgPrint','exchangeProduct')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                        <?php } ?>
                        <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                        <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('ledgPrint','exchangeProduct')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                        <?php } ?>
                    </div>
                </div>

                <div style="display: none;">
                    <div id="ledgPrint">
                        <?php echo view('Admin/Exchange_product/print_table', [
                            'exchangeProduct' => $exchangeProduct,
                            'customerId' => $customerId,
                            'status' => $status,
                            'st_date' => $st_date,
                            'en_date' => $en_date
                        ]); ?>
                    </div>
                </div>

            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
