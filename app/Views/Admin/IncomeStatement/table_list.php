<?php
$invLogo = invoice_logo();
$logoImg = no_image_view('/uploads/schools/' . $invLogo, '/uploads/schools/no_image_logo.jpg', $invLogo);

$st_date = isset($st_date) ? $st_date : '';
$en_date = isset($en_date) ? $en_date : '';
$purchaseAmount = isset($purchaseAmount) ? $purchaseAmount : 0;
$sales = isset($sales) ? $sales : 0;
$profit = isset($profit) ? $profit : 0;
$expenses = isset($expenses) ? $expenses : 0;
$profitLoss = isset($profitLoss) ? $profitLoss : ($profit - $expenses);
$month = isset($month) ? $month : '';
$days = isset($days) ? $days : 0;
?>
<div class="col-xs-12">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="box-title">Income Statement</h3>
                </div>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <div class="col-lg-6">
                    <h3>Report (<?php if (!empty($month)) { echo date('F', mktime(0, 0, 0, $month, 1)) . ' (1 Month)'; } elseif (!empty($days)) { echo 'Last ' . $days . ' Days'; } else { echo invoiceDateFormat($st_date) . ' to ' . invoiceDateFormat($en_date); } ?>)</h3>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Purchase</td>
                                <td><?= showWithCurrencySymbol($purchaseAmount); ?></td>
                            </tr>
                            <tr>
                                <td>Sales</td>
                                <td><?= showWithCurrencySymbol($sales); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-6">
                    <h3>Report (<?php if (!empty($month)) { echo date('F', mktime(0, 0, 0, $month, 1)) . ' (1 Month)'; } elseif (!empty($days)) { echo 'Last ' . $days . ' Days'; } else { echo invoiceDateFormat($st_date) . ' to ' . invoiceDateFormat($en_date); } ?>)</h3>
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Title</th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Revenue
                                    <div class="tooltip-container" style="position: relative; display: inline-flex; align-items: center; cursor: pointer;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 20 20" fill="none" style="vertical-align: middle;">
                                            <path d="M10.0013 13.3334V10M10.0013 6.66669H10.0096M18.3346 10C18.3346 14.6024 14.6037 18.3334 10.0013 18.3334C5.39893 18.3334 1.66797 14.6024 1.66797 10C1.66797 5.39765 5.39893 1.66669 10.0013 1.66669C14.6037 1.66669 18.3346 5.39765 18.3346 10Z" stroke="#A7A7A7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                        <span class="tooltip-text">Sales Profit + Service Charge + Others Income</span>
                                    </div>
                                </td>
                                <td><?= showWithCurrencySymbol($profit); ?></td>
                            </tr>
                            <tr>
                                <td>Expenses</td>
                                <td><?= showWithCurrencySymbol($expenses); ?></td>
                            </tr>
                            <tr>
                                <td>Profit/Loss</td>
                                <td>
                                    <?php if ($profitLoss >= 0){ ?>
                                        <span style="color: green;font-weight: bold;"><?= showWithCurrencySymbol($profitLoss)?></span>
                                    <?php }else{ ?>
                                        <span style="color: red;font-weight: bold;"><?= showWithCurrencySymbol($profitLoss)?></span>
                                    <?php } ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-xs-12">
                    <?php if (isset($print) && $print == 1){ ?>
                        <button type="button" onclick="printDiv('incomePrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print "></i> Print Now </button>
                    <?php } ?>
                    <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                        <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('incomePrint','income_statement')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                    <?php } ?>
                    <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                        <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('incomePrint','income_statement')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                    <?php } ?>
                </div>

            </div>
        </div>
        <!-- /.box-body -->

        <div class="col-md-12" id="incomePrint" style="display: none; text-transform: capitalize;">
            <div class="col-xs-12" style="margin-bottom: 20px;">
                <div class="col-xs-6">
                    <img src="<?php echo $logoImg; ?>" style="max-height:80px;max-width:220px;" alt="Logo">
                </div>
                <div class="col-xs-6">
                    <?php print address(); ?>
                </div>
            </div>
            <div class="col-md-12" id="incomePrintCss">
                <div class="row">
                    <div class="col-lg-12">
                        <h3 class="box-title">Income Statement</h3>
                        <h5>Report Of (<?= !empty($st_date) ? invoiceDateFormat($st_date) : ''; ?> To <?= !empty($en_date) ? invoiceDateFormat($en_date) : ''; ?>)</h5>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Purchase</td>
                                    <td><?= showWithCurrencySymbol($purchaseAmount); ?></td>
                                </tr>
                                <tr>
                                    <td>Sales</td>
                                    <td><?= showWithCurrencySymbol($sales); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-12" style="float: right;width: 50%;">
                        <table class="table table-bordered table-striped" style="width: 200%;">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Revenue</td>
                                    <td><?= showWithCurrencySymbol($profit); ?></td>
                                </tr>
                                <tr>
                                    <td>Expenses</td>
                                    <td><?= showWithCurrencySymbol($expenses); ?></td>
                                </tr>
                                <tr>
                                    <td>Profit/Loss</td>
                                    <td><?= showWithCurrencySymbol($profitLoss); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.box -->
</div>

<div class="col-xs-6">
    <div class="box box-danger">
        <div class="box-header with-border">
            <h3 class="box-title">Report Chart</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div style="max-width: 400px; margin: 20px auto;">
                <canvas id="financeDoughnut"></canvas>
            </div>

            <script>
                (function(){
                    var doughnutCanvas = document.getElementById('financeDoughnut');
                    if (doughnutCanvas) {
                        var doughnutCtx = doughnutCanvas.getContext('2d');
                        if (window.financeDoughnutChart) {
                            window.financeDoughnutChart.destroy();
                        }
                        window.financeDoughnutChart = new Chart(doughnutCtx, {
                            type: 'doughnut',
                            data: {
                                labels: ['Sales', 'Purchase', 'Profit', 'Expenses'],
                                datasets: [{
                                    data: [
                                        <?= (float)($sales ?? 0) ?>,
                                        <?= (float)($purchaseAmount ?? 0) ?>,
                                        <?= (float)($profitLoss ?? 0) ?>,
                                        <?= (float)($expenses ?? 0) ?>
                                    ],
                                    backgroundColor: [
                                        '#36A2EB',
                                        '#FF6384',
                                        '#4BC0C0',
                                        '#FF9F40'
                                    ],
                                    borderWidth: 2,
                                    borderColor: '#fff',
                                    hoverOffset: 12
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            padding: 15,
                                            font: { size: 13 }
                                        }
                                    },
                                    title: {
                                        display: true,
                                        text: 'Last <?php if (!empty($month)) { echo date('F', mktime(0, 0, 0, $month, 1)) . ' (1 Month)'; } elseif (!empty($days)) { echo $days . ' Days'; } else { echo invoiceDateFormat($st_date) . ' to ' . invoiceDateFormat($en_date); } ?> Overview',
                                        font: { size: 15 }
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return context.label + ': ' + context.raw.toLocaleString();
                                            }
                                        }
                                    }
                                },
                                cutout: '55%'
                            }
                        });
                    }
                })();
            </script>
        </div>
    </div>
</div>

<div class="col-xs-6">
    <div class="box box-danger">
        <div class="box-header with-border">
            <h3 class="box-title">Report Chart</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div style="max-width: 700px; margin: 20px auto;">
                <canvas id="financeBarChart"></canvas>
            </div>

            <script>
                (function(){
                    var barCanvas = document.getElementById('financeBarChart');
                    if (barCanvas) {
                        var barCtx = barCanvas.getContext('2d');
                        if (window.financeBarChartInst) {
                            window.financeBarChartInst.destroy();
                        }
                        window.financeBarChartInst = new Chart(barCtx, {
                            type: 'bar',
                            data: {
                                labels: ['Sales', 'Purchase', 'Profit', 'Expenses'],
                                datasets: [{
                                    label: 'Amount',
                                    data: [
                                        <?= (float)($sales ?? 0) ?>,
                                        <?= (float)($purchaseAmount ?? 0) ?>,
                                        <?= (float)($profitLoss ?? 0) ?>,
                                        <?= (float)($expenses ?? 0) ?>
                                    ],
                                    backgroundColor: [
                                        'rgba(54, 162, 235, 0.8)',
                                        'rgba(255, 99, 132, 0.8)',
                                        'rgba(75, 192, 192, 0.8)',
                                        'rgba(255, 159, 64, 0.8)'
                                    ],
                                    borderColor: [
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 99, 132, 1)',
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(255, 159, 64, 1)'
                                    ],
                                    borderWidth: 1,
                                    borderRadius: 6
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: { display: false },
                                    title: {
                                        display: true,
                                        text: 'Financial Overview (<?php if (!empty($month)) { echo date('F', mktime(0, 0, 0, $month, 1)) . ' (1 Month)'; } elseif (!empty($days)) { echo $days . ' Days'; } else { echo invoiceDateFormat($st_date) . ' to ' . invoiceDateFormat($en_date); } ?>)',
                                        font: { size: 15 },
                                        padding: { bottom: 15 }
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return context.parsed.y.toLocaleString();
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function(value) {
                                                return value.toLocaleString();
                                            }
                                        }
                                    },
                                    x: {
                                        grid: { display: false }
                                    }
                                }
                            }
                        });
                    }
                })();
            </script>
        </div>
    </div>
</div>

<div id="ajaxIncomeStatementPrint" style="display:none;">
    <?php echo view('Admin/IncomeStatement/print_table', [
        'purchaseAmount' => $purchaseAmount,
        'sales' => $sales,
        'profit' => $profit,
        'expenses' => $expenses,
        'profitLoss' => $profitLoss,
        'st_date' => $st_date,
        'en_date' => $en_date,
    ]); ?>
</div>

