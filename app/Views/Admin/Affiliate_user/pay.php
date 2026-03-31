<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Transaction <small>Transaction Process</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Transaction</li>
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
                            <div class="col-lg-9">
                                <h3 class="box-title">Commission Pay</h3>
                            </div>
                            <div class="col-lg-3">

                            </div>
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <div id="message"></div>
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>


                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">

                        <div >
                            <div class="box-header">

                                <h3 class="box-title">Commission Pay</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <form id="geniusform" action="<?php echo $action; ?>" method="post">
                                            <div class="form-group">
                                                <label for="int">Balance </label>
                                                <input type="text" class="form-control input" name="balance" id="balance" value="<?= $affiliateUser->balance;?>" readonly>
                                                <input type="hidden" class="form-control input" name="affiliate_user_id" value="<?= $affiliateUser->affiliate_user_id;?>" >

                                            </div>

                                            <div class="form-group">
                                                <label for="particulars">Particulars </label>
                                                <textarea class="form-control input" rows="3" name="particulars" id="particulars" placeholder="Particulars" required>Commission Pay</textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="payment_type">Payment
                                                    Type </label>
                                                <select class="form-control input"
                                                        onchange="checkBank(this.value)" required
                                                        name="payment_type">
                                                    <option value="">Please Select</option>
                                                    <option value="1">Chaque/Bank</option>
                                                    <option value="2" selected>Cash</option>
                                                </select>
                                            </div>

                                            <div class="form-group" id="employee">
                                                <label for="int">Amount</label>
                                                <input type="number" step=any class="form-control input"
                                                       name="amount" oninput="minusValueCheck(this.value,this)" id="amount" placeholder="Amount"
                                                       required/>
                                            </div>

                                            <button type="submit" class="btn btn-primary geniusSubmit-btn">Pay</button>
                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Affiliate_user_ajax/'); ?>','<?php echo '/Admin/Affiliate_user/'; ?>')"
                                               class="btn btn-default">Cancel</a>
                                        </form>
                                    </div>
                                    <div class="col-md-8">
                                        <div id="ledger_employee"></div>
                                    </div>
                                </div>
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

