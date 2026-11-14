<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Commission  <small>Commission List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Commission</li>
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
            <?php if (isset($filter) && $filter == 1){ ?>
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-filter"></i> Filter </h3>
                    </div>
                    <div class="box-body">
                        <form action="<?= base_url('Admin/Affiliate_commission') ?>" method="get" id="filterForm" onsubmit="return false;">
                            <div class="row">
                                <div class="col-md-2" >
                                    <label for="int">Affiliate User</label>
                                    <select class="form-control select2" name="affiliate_user_id" id="affiliateUserId" onchange="affiliateCommissionFilter()" style="width: 100%;">
                                        <option value="">Please Select</option>
                                        <?php if (isset($user) && is_array($user)) {
                                            foreach ($user as $item){ ?>
                                                <option value="<?= $item->affiliate_user_id;?>" <?= ($affiliate_user_id == $item->affiliate_user_id)?'selected':''; ?> ><?= $item->name;?></option>
                                            <?php }
                                        } ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" name="st_date" value="<?= $st_date ?? ''; ?>" id="st_date" onchange="affiliateCommissionFilter()">
                                </div>
                                <div class="col-md-3">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" name="en_date" value="<?= $en_date ?? ''; ?>" id="en_date" onchange="affiliateCommissionFilter()">
                                </div>

                                <div class="col-md-2" style="margin-top: 25px;">
                                    <button type="button" onclick="affiliateCommissionFilter()" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Filter</button>
                                </div>
                                <div class="col-md-2" style="margin-top: 25px;">
                                    <button type="button" onclick="resetAffiliateCommissionFilter()" class="btn btn-default btn-block"><i class="fa fa-refresh"></i> Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php } ?>

            <div class="col-xs-12">
                <div id="affiliateCommissionResult">
                    <?php echo view('Admin/Affiliate_commission/table_list', [
                        'commission' => $commission ?? [],
                        'affiliate_user_id' => $affiliate_user_id ?? '',
                        'st_date' => $st_date ?? '',
                        'en_date' => $en_date ?? '',
                        'read' => $read ?? 0,
                        'print' => $print ?? 0,
                        'download_PDF' => $download_PDF ?? 0,
                        'download_CSV' => $download_CSV ?? 0,
                        'shopId' => $shopId ?? ($this->session->shopId ?? 0),
                    ]); ?>
                </div>

                <div class="col-md-12" id="ledgPrint" style="display: none; text-transform: capitalize;">
                    <?php echo view('Admin/Affiliate_commission/print_table', [
                        'commission' => $commission ?? [],
                        'affiliate_user_id' => $affiliate_user_id ?? '',
                        'st_date' => $st_date ?? '',
                        'en_date' => $en_date ?? '',
                        'shopId' => $shopId ?? ($this->session->shopId ?? 0),
                    ]); ?>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
