<!doctype html>
<html lang="en" class="fixed">
<head>
<?php echo $this->Templates->Header();?>
</head>
<style type="text/css">
    .chart-container{
        position: relative; width:100%;height:350px;background: #fff;
    }
</style>
<body>
<div class="wrap">
    
    <?php echo $this->Templates->PageHeader();?>
    
    <?php
    $get_date = $this->input->get('dt');
    if(!empty($get_date)){
        $arr_date = $get_date;
    }else {
         // gmdate('F j, Y', strtotime('first day of january this year'));;
        $arr_date = date("d-m-Y", strtotime('first day of january this year')).' - '.date("d-m-Y", strtotime('last day of december this year'));
    }

    ?>
    <div class="page-body">
        <?php echo $this->Templates->LeftBar();?>
        <div class="content">
            <div class="content-header">
                <div class="leftside-content-header">
                    <ul class="breadcrumbs">
                        <li><i class="fa fa-home" aria-hidden="true"></i><a href="#">Dashboard</a></li>
                    </ul>
                </div>
            </div>
            <div class="content-body">
                <div class="row animated fadeInUp">
                    <div class="col-sm-8">
                        <div class="panel panel-default">
                            <div class="panel-header">
                                
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h4>Laba Rugi</h4>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" name="" id="filter_lost_profit" class="form-control dtpicker" placeholder="Filter">
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div id="wait-1" class="loading-chart">
                                    <div>Please Wait</div>
                                    <div class="fa-3x">
                                      <i class="fa fa-spinner fa-spin"></i>
                                    </div>
                                </div>
                                <div class="col-sm-12" style="padding:0;">
                                    <div id="parent-lost-profit" class="chart-container">
                                        <canvas id="canvas"></canvas>
                                    </div>    
                                </div>
                            </div>
                        </div>
                    </div>

                <div class="col-sm-8">

                    <!-- Laporan Evaluasi -->
                                    
                    <div role="tabpanel" class="tab-pane" id="laporan_evaluasi">
                        <div class="col-sm-15">
                        <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Laporan Evaluasi Pemakaian Bahan Baku</h3>
                                    <a href="laporan_produksi">Kembali</a>
                                </div>
                                <div style="margin: 20px">
                                    <div class="row"> 
                                        <div class="col-sm-4">
                                            <input type="text" id="filter_date_evaluasi" name="filter_date" class="form-control dtpicker"  autocomplete="off" placeholder="Filter By Date">
                                        </div>   
                                    </div>
                                    <br />
                                    <div id="wait" style=" text-align: center; align-content: center; display: none;">	
                                        <div>Please Wait</div>
                                        <div class="fa-3x">
                                            <i class="fa fa-spinner fa-spin"></i>
                                        </div>
                                    </div>				
                                    <div class="table-responsive" id="box-ajax-evaluasi">													
                                    
    
                                    </div>
                                </div>
                        </div>
                        
                        </div>
                    </div>
                </div>


            </div>  
        </div>
       
    </div>
</div>

<?php echo $this->Templates->Footer();?>
<script src="<?php echo base_url();?>assets/back/theme/vendor/toastr/toastr.min.js"></script>
<script src="<?php echo base_url();?>assets/back/theme/vendor/chart-js/chart.min.js"></script>
<script src="<?php echo base_url();?>assets/back/theme/vendor/magnific-popup/jquery.magnific-popup.min.js"></script>
<!-- <script src="<?php echo base_url();?>assets/back/theme/javascripts/examples/dashboard.js"></script> -->

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>

<script src="<?php echo base_url();?>assets/back/theme/vendor/daterangepicker/moment.min.js"></script>
<script src="<?php echo base_url();?>assets/back/theme/vendor/number_format.js"></script>
<script src="<?php echo base_url();?>assets/back/theme/vendor/daterangepicker/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/back/theme/vendor/daterangepicker/daterangepicker.css">
<script type="text/javascript" src="<?php echo base_url();?>assets/back/theme/vendor/chart-js/chart.min.js"></script>
<script type="text/javascript">
    
    $('.dtpicker').daterangepicker({
        autoUpdateInput : false,
        locale: {
            format: 'DD-MM-YYYY'
        },
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });

    

    function LostProfit(CharData)
    {
        var ctx = document.getElementById('canvas').getContext('2d');
        window.myBar = new Chart(ctx, {
            type: 'bar',
            data: CharData,
            options: {
                title: {
                    display: true,
                },
                responsive: true,
                scales: {
                    xAxes: [{
                        stacked: true
                        
                    }],
                    yAxes: [{
                        stacked: true,
                    }]
                },
                legend: {
                    display: true,
                    position : 'bottom'
                },
                responsive: true,
                maintainAspectRatio: false,
                hoverMode: 'index',
                tooltips: {
                    callbacks: {
                    title: function(tooltipItem, data) {
                        return data['labels'][tooltipItem[0]['index']];
                    },
                    beforeLabel : function(tooltipItem, data) {
                        return 'Pendapatan = '+data['datasets'][0]['data_revenue'][tooltipItem['index']];
                    },
                    label: function(tooltipItem, data) {
                        return 'Biaya = '+data['datasets'][0]['data_revenuecost'][tooltipItem['index']];
                    },
                    afterLabel : function(tooltipItem, data) {
                        return 'Laba Rugi = '+data['datasets'][0]['data_laba'][tooltipItem['index']]+ ' ('+data['datasets'][0]['data'][tooltipItem['index']]+' %)';
                    },
                    },
                }
            }
        });

    }


    function getLostProfit()
    {
        $.ajax({
            type    : "POST",
            url     : "<?php echo base_url();?>pmm/db_lost_profit/"+Math.random(),
            dataType : 'json',
            data: {arr_date : $('#filter_lost_profit').val()},
            beforeSend : function(){
                $('#wait-1').show();
            },
            success : function(result){
                $('#canvas').remove();
                $('#parent-lost-profit').append('<canvas id="canvas"></canvas>');
                LostProfit(result);
                $('#wait-1').hide();
            }
        });
    }
    getLostProfit();
    $('#filter_lost_profit').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
            getLostProfit();
    });
</script>

    <!-- Script Evaluasi -->
    <script type="text/javascript">
        $('#filter_date_evaluasi').daterangepicker({
        autoUpdateInput : false,
        showDropdowns: true,
        locale: {
            format: 'DD-MM-YYYY'
        },
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(30, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
        });

        $('#filter_date_evaluasi').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
                TableEvaluasi();
        });


        function TableEvaluasi()
        {
            $('#wait').fadeIn('fast');   
            $.ajax({
                type    : "POST",
                url     : "<?php echo site_url('pmm/productions/table_evaluasi_dashboard'); ?>/"+Math.random(),
                dataType : 'html',
                data: {
                    filter_date : $('#filter_date_evaluasi').val(),
                },
                success : function(result){
                    $('#box-ajax-evaluasi').html(result);
                    $('#wait').fadeOut('fast');
                }
            });
        }

        TableEvaluasi();
    </script>

</body>
</html>
