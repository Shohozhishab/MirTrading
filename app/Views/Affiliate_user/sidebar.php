
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">

            </div>
            <div class="pull-left info">
                <p><?php echo newSession()->sup_name;?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">MAIN NAVIGATION</li>
            <li class="active">
                <a href="<?php echo base_url('Affiliate_user/Dashboard')?>">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="">
                <a href="<?php echo base_url('Affiliate_user/Commission')?>">
                    <i class="fa fa fa-tasks"></i> <span>Commission List</span>
                </a>
            </li>
            <li class="">
                <a href="<?php echo base_url('Affiliate_user/CommissionPay')?>">
                    <i class="fa fa fa-tasks"></i> <span>Commission Pay List</span>
                </a>
            </li>





        </ul>
    </section>
    <!-- /.sidebar -->
</aside>