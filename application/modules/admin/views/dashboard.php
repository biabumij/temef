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
    <?php
    $date_now = date('Y-m-d');
    $date_januari_awal = date('2022-01-01');
    $date_januari_akhir = date('2022-01-31');

    $rencana_kerja_2022 = $this->db->select('r.*')
    ->from('rak r')
    ->order_by('r.tanggal_rencana_kerja','asc')->limit(1)
    ->get()->row_array();
    
    $volume_rap_2022_produk_a = $rencana_kerja_2022['vol_produk_a'];
    $volume_rap_2022_produk_b = $rencana_kerja_2022['vol_produk_b'];
    $volume_rap_2022_produk_c = $rencana_kerja_2022['vol_produk_c'];
    $volume_rap_2022_produk_d = $rencana_kerja_2022['vol_produk_d'];

    $total_rap_volume_2022 = $volume_rap_2022_produk_a + $volume_rap_2022_produk_b + $volume_rap_2022_produk_c + $volume_rap_2022_produk_d;
    
    $harga_jual_125_rap = $this->db->select('pod.price as harga_satuan')
    ->from('pmm_sales_po po')
    ->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
    ->where("pod.product_id = 2")
    ->order_by('po.contract_date','asc')->limit(1)
    ->get()->row_array();

    $harga_jual_225_rap = $this->db->select('pod.price as harga_satuan')
    ->from('pmm_sales_po po')
    ->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
    ->where("pod.product_id = 1")
    ->order_by('po.contract_date','asc')->limit(1)
    ->get()->row_array();

    $harga_jual_250_rap = $this->db->select('pod.price as harga_satuan')
    ->from('pmm_sales_po po')
    ->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
    ->where("pod.product_id = 3")
    ->order_by('po.contract_date','asc')->limit(1)
    ->get()->row_array();

    $harga_jual_250_18_rap = $this->db->select('pod.price as harga_satuan')
    ->from('pmm_sales_po po')
    ->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
    ->where("pod.product_id = 11")
    ->order_by('po.contract_date','asc')->limit(1)
    ->get()->row_array();

    $nilai_jual_125_2022 = $volume_rap_2022_produk_a * $harga_jual_125_rap['harga_satuan'];
    $nilai_jual_225_2022 = $volume_rap_2022_produk_b * $harga_jual_225_rap['harga_satuan'];
    $nilai_jual_250_2022 = $volume_rap_2022_produk_c * $harga_jual_250_rap['harga_satuan'];
    $nilai_jual_250_18_2022 = $volume_rap_2022_produk_d * $harga_jual_250_18_rap['harga_satuan'];
    $nilai_jual_all_2022 = $nilai_jual_125_2022 + $nilai_jual_225_2022 + $nilai_jual_250_2022 + $nilai_jual_250_18_2022;
    
    $total_rap_nilai_2022 = $nilai_jual_all_2022;
    $total_kontrak_all = $total_rap_nilai_2022;

    $penjualan_januari = $this->db->select('SUM(pp.display_price) as total')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_januari_awal' and '$date_januari_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();
    
    $total_penjualan_januari = $penjualan_januari['total'];
    $presentase_penjualan_januari = ($total_penjualan_januari / $total_kontrak_all) * 100;
    $net_januari = round($presentase_penjualan_januari,2);

    $date_februari_awal = date('2022-02-01');
    $date_februari_akhir = date('2022-02-28');
    $penjualan_februari = $this->db->select('SUM(pp.display_price) as total')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_januari_awal' and '$date_februari_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_februari = $penjualan_februari['total'];
    $presentase_penjualan_februari = ($total_penjualan_februari / $total_kontrak_all) * 100;
    $net_februari = round($presentase_penjualan_februari,2);

    $date_maret_awal = date('2022-03-01');
    $date_maret_akhir = date('2022-03-31');
    $penjualan_maret = $this->db->select('SUM(pp.display_price) as total')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_januari_awal' and '$date_maret_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_maret = $penjualan_maret['total'];
    $presentase_penjualan_maret = ($total_penjualan_maret / $total_kontrak_all) * 100;
    $net_maret = round($presentase_penjualan_maret,2);

    $date_april_awal = date('2022-04-01');
    $date_april_akhir = date('2022-04-30');
    $penjualan_april = $this->db->select('SUM(pp.display_price) as total')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_januari_awal' and '$date_april_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_april = $penjualan_april['total'];
    $presentase_penjualan_april = ($total_penjualan_april / $total_kontrak_all) * 100;
    $net_april = round($presentase_penjualan_april,2);

    $date_mei_awal = date('2022-05-01');
    $date_mei_akhir = date('2022-05-31');
    $penjualan_mei = $this->db->select('SUM(pp.display_price) as total')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_januari_awal' and '$date_mei_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_mei = $penjualan_mei['total'];
    $presentase_penjualan_mei = ($total_penjualan_mei / $total_kontrak_all) * 100;
    $net_mei = round($presentase_penjualan_mei,2);

    $date_juni_awal = date('2022-06-01');
    $date_juni_akhir = date('2022-06-30');
    $penjualan_juni = $this->db->select('SUM(pp.display_price) as total')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_januari_awal' and '$date_juni_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_juni = $penjualan_juni['total'];
    $presentase_penjualan_juni = ($total_penjualan_juni / $total_kontrak_all) * 100;
    $net_juni = round($presentase_penjualan_juni,2);

    $date_juli_awal = date('2022-07-01');
    $date_juli_akhir = date('2022-07-31');
    $penjualan_juli = $this->db->select('SUM(pp.display_price) as total')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_januari_awal' and '$date_juli_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_juli = $penjualan_juli['total'];
    $presentase_penjualan_juli = ($total_penjualan_juli / $total_kontrak_all) * 100;
    $net_juli = round($presentase_penjualan_juli,2);

    $date_agustus_awal = date('2022-08-01');
    $date_agustus_akhir = date('2022-08-31');
    $penjualan_agustus = $this->db->select('SUM(pp.display_price) as total')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_januari_awal' and '$date_agustus_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_agustus = $penjualan_agustus['total'];
    $presentase_penjualan_agustus = ($total_penjualan_agustus / $total_kontrak_all) * 100;
    $net_agustus = round($presentase_penjualan_agustus,2);

    $date_september_awal = date('2022-09-01');
    $date_september_akhir = date('2022-09-30');
    $penjualan_september = $this->db->select('SUM(pp.display_price) as total')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_januari_awal' and '$date_september_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_september = $penjualan_september['total'];
    $presentase_penjualan_september = ($total_penjualan_september / $total_kontrak_all) * 100;
    $net_september = round($presentase_penjualan_september,2);

    $date_oktober_awal = date('2022-10-01');
    $date_oktober_akhir = date('2022-10-31');
    $penjualan_oktober = $this->db->select('SUM(pp.display_price) as total')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_januari_awal' and '$date_oktober_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_oktober = $penjualan_oktober['total'];
    $presentase_penjualan_oktober = ($total_penjualan_oktober / $total_kontrak_all) * 100;
    $net_oktober = round($presentase_penjualan_oktober,2);
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
                <div id="container" style="min-width: 400px;height: 400px; margin: 0 auto"></div>
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
                                    <h3 class="panel-title">Evaluasi Pemakaian Bahan Baku</h3>
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
            type: 'line',
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
                        ticks: {
                            beginAtZero: true,
                            //min: -1500,
                            //max: 1500
                        },
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
                        return 'Laba Rugi = '+data['datasets'][0]['data_laba'][tooltipItem['index']]+ ' ('+data['datasets'][0]['data'][tooltipItem['index']]+'%)';
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
            url     : "<?php echo site_url('pmm/reports/dashboard_evaluasi_bahan'); ?>/"+Math.random(),
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

<script src="<?php echo base_url();?>assets/chart/highcharts.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/chart/jquery.min.js"></script>

<script type="text/javascript">
	//2)script untuk membuat grafik, perhatikan setiap komentar agar paham
    $(function () {
        var chart;
        $(document).ready(function() {
            chart = new Highcharts.Chart({
                chart: {
                    renderTo: 'container', //letakan grafik di div id container
                    //Type grafik, anda bisa ganti menjadi area,bar,column dan bar
                    type: 'line',  
                    marginRight: 130,
                    marginBottom: 25
                },
                title: {
                    text: 'Realisasi Produksi',
                    x: -20 //center
                },
                subtitle: {
                    text: 'PT. Bia Bumi Jayendra',
                    x: -20
                },
                xAxis: { //X axis menampilkan data bulan 
                    categories: ['Januari', 'Febuari', 'Maret','April','Mei','Juni','Juli','Agustus','September','Oktober']
                },
                yAxis: {
                    title: {  //label yAxis
                        text: 'Presentase Terhadap Kontrak'
                    },
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080' //warna dari grafik line
                    }]
                },
                tooltip: { 
                //fungsi tooltip, ini opsional, kegunaan dari fungsi ini 
                //akan menampikan data di titik tertentu di grafik saat mouseover
                    formatter: function() {
                            return '<b>'+ this.series.name +'</b><br/>'+ 
                            this.x +': '+ this.y + '%';
                            //'<b>'+ this.x +': '+ this.y +'</b><br/>'+ 
                            //'<b>'+ 'Penjualan' +': '+ this.y +'</b><br/>';
                    }
                },
                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'top',
                    x: -10,
                    y: 100,
                    borderWidth: 0
                },
                //series adalah data yang akan dibuatkan grafiknya,
            
                series: [{  
                    name: 'Realisasi',  
                    
                    data: [<?php echo json_encode($net_januari, JSON_NUMERIC_CHECK); ?>, <?php echo json_encode($net_februari, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_maret, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_april, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_mei, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_juni, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_juli, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_agustus, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_september, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_oktober, JSON_NUMERIC_CHECK); ?>],

                    color: '#000000'
                },
                {  
                    name: 'RAP',  
                    
                    data: [10,20,30,40,50,60,70,80,90,100],

                    color: '#FF0000'
                },
                
                ]
            });
        });
        
    });
		</script>

</body>
</html>
