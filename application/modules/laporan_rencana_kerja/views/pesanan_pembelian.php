<!doctype html>
<html lang="en" class="fixed">
<head>
    <?php echo $this->Templates->Header();?>

    <style type="text/css">
        .table-center th, .table-center td{
            text-align:center;
        }
        .table-warning tr, .table-warning th, .table-warning td{
            border: 1px solid white;
            font-weight:bold;
        }
    </style>
</head>

<body>
    <div class="wrap">
        
        <?php echo $this->Templates->PageHeader();?>

        <div class="page-body">
            <?php echo $this->Templates->LeftBar();?>
            <div class="content" style="padding:0;">
                <div class="content-header">
                    <div class="leftside-content-header">
                        <ul class="breadcrumbs">
                            <li><i class="fa fa-sitemap" aria-hidden="true"></i><a href="<?php echo site_url('admin');?>">Dashboard</a></li>
                            <li>
                                <a href="<?php echo site_url('admin/laporan_rencana_kerja');?>"> <i class="fa fa-calendar" aria-hidden="true"></i> Pesanan Pembelian</a></li>
                            <li><a>Buat Pesanan Pembelian</a></li>
                        </ul>
                    </div>
                </div>
                <div class="row animated fadeInUp">
                    <div class="col-sm-12 col-lg-12">
                        <div class="panel">
                            <div class="panel-header"> 
                                <h3 >Buat Pesanan Pembelian</h3>
                            </div>
                            <div class="panel-content">
                                <div class="table-responsive">
                                    <?php
                                    $date_now = date('Y-m-d');
                        
                                    //BULAN 1
                                    $date_1_awal = date('Y-m-01', (strtotime($date_now)));
                                    $date_1_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_1_awal)));
                                    
                                    $rencana_kerja_1 = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
                                    ->from('rak r')
                                    ->where("r.tanggal_rencana_kerja between '$date_1_awal' and '$date_1_akhir'")
                                    ->get()->row_array();
                        
                                    $volume_1_produk_a = $rencana_kerja_1['vol_produk_a'];
                                    $volume_1_produk_b = $rencana_kerja_1['vol_produk_b'];
                                    $volume_1_produk_c = $rencana_kerja_1['vol_produk_c'];
                                    $volume_1_produk_d = $rencana_kerja_1['vol_produk_d'];
                        
                                    $total_1_volume = $volume_1_produk_a + $volume_1_produk_b + $volume_1_produk_c + $volume_1_produk_d;
                                        
                                    $nilai_jual_125_1 = $volume_1_produk_a * $rencana_kerja_1['price_a'];
                                    $nilai_jual_225_1 = $volume_1_produk_b * $rencana_kerja_1['price_b'];
                                    $nilai_jual_250_1 = $volume_1_produk_c * $rencana_kerja_1['price_c'];
                                    $nilai_jual_250_18_1 = $volume_1_produk_d * $rencana_kerja_1['price_d'];
                                    $nilai_jual_all_1 = $nilai_jual_125_1 + $nilai_jual_225_1 + $nilai_jual_250_1 + $nilai_jual_250_18_1;
                        
                                    //BULAN 2
                                    $date_2_awal = date('Y-m-d', strtotime('+1 days', strtotime($date_1_akhir)));
                                    $date_2_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_2_awal)));
                        
                                    $rencana_kerja_2 = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
                                    ->from('rak r')
                                    ->where("r.tanggal_rencana_kerja between '$date_2_awal' and '$date_2_akhir'")
                                    ->get()->row_array();
                        
                                    $volume_2_produk_a = $rencana_kerja_2['vol_produk_a'];
                                    $volume_2_produk_b = $rencana_kerja_2['vol_produk_b'];
                                    $volume_2_produk_c = $rencana_kerja_2['vol_produk_c'];
                                    $volume_2_produk_d = $rencana_kerja_2['vol_produk_d'];
                        
                                    $total_2_volume = $volume_2_produk_a + $volume_2_produk_b + $volume_2_produk_c + $volume_2_produk_d;
                                        
                                    $nilai_jual_125_2 = $volume_2_produk_a * $rencana_kerja_2['price_a'];
                                    $nilai_jual_225_2 = $volume_2_produk_b * $rencana_kerja_2['price_b'];
                                    $nilai_jual_250_2 = $volume_2_produk_c * $rencana_kerja_2['price_c'];
                                    $nilai_jual_250_18_2 = $volume_2_produk_d * $rencana_kerja_2['price_d'];
                                    $nilai_jual_all_2 = $nilai_jual_125_2 + $nilai_jual_225_2 + $nilai_jual_250_2 + $nilai_jual_250_18_2;
                        
                                    //BULAN 3
                                    $date_3_awal = date('Y-m-d', strtotime('+1 days', strtotime($date_2_akhir)));
                                    $date_3_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_3_awal)));
                        
                                    $rencana_kerja_3 = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
                                    ->from('rak r')
                                    ->where("r.tanggal_rencana_kerja between '$date_3_awal' and '$date_3_akhir'")
                                    ->get()->row_array();
                        
                                    $volume_3_produk_a = $rencana_kerja_3['vol_produk_a'];
                                    $volume_3_produk_b = $rencana_kerja_3['vol_produk_b'];
                                    $volume_3_produk_c = $rencana_kerja_3['vol_produk_c'];
                                    $volume_3_produk_d = $rencana_kerja_3['vol_produk_d'];
                        
                                    $total_3_volume = $volume_3_produk_a + $volume_3_produk_b + $volume_3_produk_c + $volume_3_produk_d;
                                        
                                    $nilai_jual_125_3 = $volume_3_produk_a * $rencana_kerja_3['price_a'];
                                    $nilai_jual_225_3 = $volume_3_produk_b * $rencana_kerja_3['price_b'];
                                    $nilai_jual_250_3 = $volume_3_produk_c * $rencana_kerja_3['price_c'];
                                    $nilai_jual_250_18_3 = $volume_3_produk_d * $rencana_kerja_3['price_d'];
                                    $nilai_jual_all_3 = $nilai_jual_125_3 + $nilai_jual_225_3 + $nilai_jual_250_3 + $nilai_jual_250_18_3;
                        
                                    //BULAN 4
                                    $date_4_awal = date('Y-m-d', strtotime('+1 days', strtotime($date_3_akhir)));
                                    $date_4_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_4_awal)));
                        
                                    $rencana_kerja_4 = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
                                    ->from('rak r')
                                    ->where("r.tanggal_rencana_kerja between '$date_4_awal' and '$date_4_akhir'")
                                    ->get()->row_array();
                        
                                    $volume_4_produk_a = $rencana_kerja_4['vol_produk_a'];
                                    $volume_4_produk_b = $rencana_kerja_4['vol_produk_b'];
                                    $volume_4_produk_c = $rencana_kerja_4['vol_produk_c'];
                                    $volume_4_produk_d = $rencana_kerja_4['vol_produk_d'];
                        
                                    $total_4_volume = $volume_4_produk_a + $volume_4_produk_b + $volume_4_produk_c + $volume_4_produk_d;
                                        
                                    $nilai_jual_125_4 = $volume_4_produk_a * $rencana_kerja_4['price_a'];
                                    $nilai_jual_225_4 = $volume_4_produk_b * $rencana_kerja_4['price_b'];
                                    $nilai_jual_250_4 = $volume_4_produk_c * $rencana_kerja_4['price_c'];
                                    $nilai_jual_250_18_4 = $volume_4_produk_d * $rencana_kerja_4['price_d'];
                                    $nilai_jual_all_4 = $nilai_jual_125_4 + $nilai_jual_225_4 + $nilai_jual_250_4 + $nilai_jual_250_18_4;
                        
                                    //BULAN 5
                                    $date_5_awal = date('Y-m-d', strtotime('+1 days', strtotime($date_4_akhir)));
                                    $date_5_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_5_awal)));
                        
                                    $rencana_kerja_5 = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
                                    ->from('rak r')
                                    ->where("r.tanggal_rencana_kerja between '$date_5_awal' and '$date_5_akhir'")
                                    ->get()->row_array();
                        
                                    $volume_5_produk_a = $rencana_kerja_5['vol_produk_a'];
                                    $volume_5_produk_b = $rencana_kerja_5['vol_produk_b'];
                                    $volume_5_produk_c = $rencana_kerja_5['vol_produk_c'];
                                    $volume_5_produk_d = $rencana_kerja_5['vol_produk_d'];
                        
                                    $total_5_volume = $volume_5_produk_a + $volume_5_produk_b + $volume_5_produk_c + $volume_5_produk_d;
                                        
                                    $nilai_jual_125_5 = $volume_5_produk_a * $rencana_kerja_5['price_a'];
                                    $nilai_jual_225_5 = $volume_5_produk_b * $rencana_kerja_5['price_b'];
                                    $nilai_jual_250_5 = $volume_5_produk_c * $rencana_kerja_5['price_c'];
                                    $nilai_jual_250_18_5 = $volume_5_produk_d * $rencana_kerja_5['price_d'];
                                    $nilai_jual_all_5 = $nilai_jual_125_5 + $nilai_jual_225_5 + $nilai_jual_250_5 + $nilai_jual_250_18_5;
                        
                                    //BULAN 6
                                    $date_6_awal = date('Y-m-d', strtotime('+1 days', strtotime($date_5_akhir)));
                                    $date_6_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_6_awal)));
                        
                                    $rencana_kerja_6 = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
                                    ->from('rak r')
                                    ->where("r.tanggal_rencana_kerja between '$date_6_awal' and '$date_6_akhir'")
                                    ->get()->row_array();
                        
                                    $volume_6_produk_a = $rencana_kerja_6['vol_produk_a'];
                                    $volume_6_produk_b = $rencana_kerja_6['vol_produk_b'];
                                    $volume_6_produk_c = $rencana_kerja_6['vol_produk_c'];
                                    $volume_6_produk_d = $rencana_kerja_6['vol_produk_d'];
                        
                                    $total_6_volume = $volume_6_produk_a + $volume_6_produk_b + $volume_6_produk_c + $volume_6_produk_d;
                                        
                                    $nilai_jual_125_6 = $volume_6_produk_a * $rencana_kerja_6['price_a'];
                                    $nilai_jual_225_6 = $volume_6_produk_b * $rencana_kerja_6['price_b'];
                                    $nilai_jual_250_6 = $volume_6_produk_c * $rencana_kerja_6['price_c'];
                                    $nilai_jual_250_18_6 = $volume_6_produk_d * $rencana_kerja_6['price_d'];
                                    $nilai_jual_all_6 = $nilai_jual_125_6 + $nilai_jual_225_6 + $nilai_jual_250_6 + $nilai_jual_250_18_6;
                                    ?>
                        
                                    
                                    <?php
                                    
                                    //BULAN 1
                                    $komposisi_125_1 = $this->db->select('(r.vol_produk_a * pk.presentase_a) as komposisi_semen_125, (vol_produk_a * pk.presentase_b) as komposisi_pasir_125, (vol_produk_a * pk.presentase_c) as komposisi_batu1020_125, (vol_produk_a * pk.presentase_d) as komposisi_batu2030_125')
                                    ->from('rak r')
                                    ->join('pmm_agregat pk', 'r.komposisi_125 = pk.id','left')
                                    ->where("r.tanggal_rencana_kerja between '$date_1_awal' and '$date_1_akhir'")
                                    ->get()->result_array();
                        
                                    $total_volume_semen_125_1 = 0;
                                    $total_volume_pasir_125_1 = 0;
                                    $total_volume_batu1020_125_1 = 0;
                                    $total_volume_batu2030_125_1 = 0;
                        
                                    foreach ($komposisi_125_1 as $x){
                                        $total_volume_semen_125_1 = $x['komposisi_semen_125'];
                                        $total_volume_pasir_125_1 = $x['komposisi_pasir_125'];
                                        $total_volume_batu1020_125_1 = $x['komposisi_batu1020_125'];
                                        $total_volume_batu2030_125_1 = $x['komposisi_batu2030_125'];
                                    }
                        
                                    $komposisi_225_1 = $this->db->select('(r.vol_produk_b * pk.presentase_a) as komposisi_semen_225, (vol_produk_b * pk.presentase_b) as komposisi_pasir_225, (vol_produk_b * pk.presentase_c) as komposisi_batu1020_225, (vol_produk_b * pk.presentase_d) as komposisi_batu2030_225')
                                    ->from('rak r')
                                    ->join('pmm_agregat pk', 'r.komposisi_225 = pk.id','left')
                                    ->where("r.tanggal_rencana_kerja between '$date_1_awal' and '$date_1_akhir'")
                                    ->get()->result_array();
                        
                                    $total_volume_semen_225_1 = 0;
                                    $total_volume_pasir_225_1 = 0;
                                    $total_volume_batu1020_225_1 = 0;
                                    $total_volume_batu2030_225_1 = 0;
                        
                                    foreach ($komposisi_225_1 as $x){
                                        $total_volume_semen_225_1 = $x['komposisi_semen_225'];
                                        $total_volume_pasir_225_1 = $x['komposisi_pasir_225'];
                                        $total_volume_batu1020_225_1 = $x['komposisi_batu1020_225'];
                                        $total_volume_batu2030_225_1 = $x['komposisi_batu2030_225'];
                                    }
                        
                                    $komposisi_250_1 = $this->db->select('(r.vol_produk_c * pk.presentase_a) as komposisi_semen_250, (vol_produk_c * pk.presentase_b) as komposisi_pasir_250, (vol_produk_c * pk.presentase_c) as komposisi_batu1020_250, (vol_produk_c * pk.presentase_d) as komposisi_batu2030_250')
                                    ->from('rak r')
                                    ->join('pmm_agregat pk', 'r.komposisi_250 = pk.id','left')
                                    ->where("r.tanggal_rencana_kerja between '$date_1_awal' and '$date_1_akhir'")
                                    ->get()->result_array();
                        
                                    $total_volume_semen_250_1 = 0;
                                    $total_volume_pasir_250_1 = 0;
                                    $total_volume_batu1020_250_1 = 0;
                                    $total_volume_batu2030_250_1 = 0;
                        
                                    foreach ($komposisi_250_1 as $x){
                                        $total_volume_semen_250_1 = $x['komposisi_semen_250'];
                                        $total_volume_pasir_250_1 = $x['komposisi_pasir_250'];
                                        $total_volume_batu1020_250_1 = $x['komposisi_batu1020_250'];
                                        $total_volume_batu2030_250_1 = $x['komposisi_batu2030_250'];
                                    }
                        
                                    $komposisi_250_2_1 = $this->db->select('(r.vol_produk_d * pk.presentase_a) as komposisi_semen_250_2, (vol_produk_d * pk.presentase_b) as komposisi_pasir_250_2, (vol_produk_d * pk.presentase_c) as komposisi_batu1020_250_2, (vol_produk_d * pk.presentase_d) as komposisi_batu2030_250_2')
                                    ->from('rak r')
                                    ->join('pmm_agregat pk', 'r.komposisi_250_2 = pk.id','left')
                                    ->where("r.tanggal_rencana_kerja between '$date_1_awal' and '$date_1_akhir'")
                                    ->get()->result_array();
                        
                                    $total_volume_semen_250_2_1 = 0;
                                    $total_volume_pasir_250_2_1 = 0;
                                    $total_volume_batu1020_250_2_1 = 0;
                                    $total_volume_batu2030_250_2_1 = 0;
                        
                                    foreach ($komposisi_250_2_1 as $x){
                                        $total_volume_semen_250_2_1 = $x['komposisi_semen_250_2'];
                                        $total_volume_pasir_250_2_1 = $x['komposisi_pasir_250_2'];
                                        $total_volume_batu1020_250_2_1 = $x['komposisi_batu1020_250_2'];
                                        $total_volume_batu2030_250_2_1 = $x['komposisi_batu2030_250_2'];
                                    }
                        
                                    $total_volume_semen_1 = $total_volume_semen_125_1 + $total_volume_semen_225_1 + $total_volume_semen_250_1 + $total_volume_semen_250_2_1;
                                    $total_volume_pasir_1 = $total_volume_pasir_125_1 + $total_volume_pasir_225_1 + $total_volume_pasir_250_1 + $total_volume_pasir_250_2_1;
                                    $total_volume_batu1020_1 = $total_volume_batu1020_125_1 + $total_volume_batu1020_225_1 + $total_volume_batu1020_250_1 + $total_volume_batu1020_250_2_1;
                                    $total_volume_batu2030_1 = $total_volume_batu2030_125_1 + $total_volume_batu2030_225_1 + $total_volume_batu2030_250_1 + $total_volume_batu2030_250_2_1;
                        
                                    //BULAN 2
                                    $komposisi_125_2 = $this->db->select('(r.vol_produk_a * pk.presentase_a) as komposisi_semen_125, (vol_produk_a * pk.presentase_b) as komposisi_pasir_125, (vol_produk_a * pk.presentase_c) as komposisi_batu1020_125, (vol_produk_a * pk.presentase_d) as komposisi_batu2030_125')
                                    ->from('rak r')
                                    ->join('pmm_agregat pk', 'r.komposisi_125 = pk.id','left')
                                    ->where("r.tanggal_rencana_kerja between '$date_2_awal' and '$date_2_akhir'")
                                    ->get()->result_array();
                        
                                    $total_volume_semen_125_2 = 0;
                                    $total_volume_pasir_125_2 = 0;
                                    $total_volume_batu1020_125_2 = 0;
                                    $total_volume_batu2030_125_2 = 0;
                        
                                    foreach ($komposisi_125_2 as $x){
                                        $total_volume_semen_125_2 = $x['komposisi_semen_125'];
                                        $total_volume_pasir_125_2 = $x['komposisi_pasir_125'];
                                        $total_volume_batu1020_125_2 = $x['komposisi_batu1020_125'];
                                        $total_volume_batu2030_125_2 = $x['komposisi_batu2030_125'];
                                    }
                        
                                    $komposisi_225_2 = $this->db->select('(r.vol_produk_b * pk.presentase_a) as komposisi_semen_225, (vol_produk_b * pk.presentase_b) as komposisi_pasir_225, (vol_produk_b * pk.presentase_c) as komposisi_batu1020_225, (vol_produk_b * pk.presentase_d) as komposisi_batu2030_225')
                                    ->from('rak r')
                                    ->join('pmm_agregat pk', 'r.komposisi_225 = pk.id','left')
                                    ->where("r.tanggal_rencana_kerja between '$date_2_awal' and '$date_2_akhir'")
                                    ->get()->result_array();
                        
                                    $total_volume_semen_225_2 = 0;
                                    $total_volume_pasir_225_2 = 0;
                                    $total_volume_batu1020_225_2 = 0;
                                    $total_volume_batu2030_225_2 = 0;
                        
                                    foreach ($komposisi_225_2 as $x){
                                        $total_volume_semen_225_2 = $x['komposisi_semen_225'];
                                        $total_volume_pasir_225_2 = $x['komposisi_pasir_225'];
                                        $total_volume_batu1020_225_2 = $x['komposisi_batu1020_225'];
                                        $total_volume_batu2030_225_2 = $x['komposisi_batu2030_225'];
                                    }
                        
                                    $komposisi_250_2 = $this->db->select('(r.vol_produk_c * pk.presentase_a) as komposisi_semen_250, (vol_produk_c * pk.presentase_b) as komposisi_pasir_250, (vol_produk_c * pk.presentase_c) as komposisi_batu1020_250, (vol_produk_c * pk.presentase_d) as komposisi_batu2030_250')
                                    ->from('rak r')
                                    ->join('pmm_agregat pk', 'r.komposisi_250 = pk.id','left')
                                    ->where("r.tanggal_rencana_kerja between '$date_2_awal' and '$date_2_akhir'")
                                    ->get()->result_array();
                        
                                    $total_volume_semen_250_2 = 0;
                                    $total_volume_pasir_250_2 = 0;
                                    $total_volume_batu1020_250_2 = 0;
                                    $total_volume_batu2030_250_2 = 0;
                        
                                    foreach ($komposisi_250_2 as $x){
                                        $total_volume_semen_250_2 = $x['komposisi_semen_250'];
                                        $total_volume_pasir_250_2 = $x['komposisi_pasir_250'];
                                        $total_volume_batu1020_250_2 = $x['komposisi_batu1020_250'];
                                        $total_volume_batu2030_250_2 = $x['komposisi_batu2030_250'];
                                    }
                        
                                    $komposisi_250_2_2 = $this->db->select('(r.vol_produk_d * pk.presentase_a) as komposisi_semen_250_2, (vol_produk_d * pk.presentase_b) as komposisi_pasir_250_2, (vol_produk_d * pk.presentase_c) as komposisi_batu1020_250_2, (vol_produk_d * pk.presentase_d) as komposisi_batu2030_250_2')
                                    ->from('rak r')
                                    ->join('pmm_agregat pk', 'r.komposisi_250_2 = pk.id','left')
                                    ->where("r.tanggal_rencana_kerja between '$date_2_awal' and '$date_2_akhir'")
                                    ->get()->result_array();
                        
                                    $total_volume_semen_250_2_2 = 0;
                                    $total_volume_pasir_250_2_2 = 0;
                                    $total_volume_batu1020_250_2_2 = 0;
                                    $total_volume_batu2030_250_2_2 = 0;
                        
                                    foreach ($komposisi_250_2_2 as $x){
                                        $total_volume_semen_250_2_2 = $x['komposisi_semen_250_2'];
                                        $total_volume_pasir_250_2_2 = $x['komposisi_pasir_250_2'];
                                        $total_volume_batu1020_250_2_2 = $x['komposisi_batu1020_250_2'];
                                        $total_volume_batu2030_250_2_2 = $x['komposisi_batu2030_250_2'];
                                    }
                        
                                    $total_volume_semen_2 = $total_volume_semen_125_2 + $total_volume_semen_225_2 + $total_volume_semen_250_2 + $total_volume_semen_250_2_2;
                                    $total_volume_pasir_2 = $total_volume_pasir_125_2 + $total_volume_pasir_225_2 + $total_volume_pasir_250_2 + $total_volume_pasir_250_2_2;
                                    $total_volume_batu1020_2 = $total_volume_batu1020_125_2 + $total_volume_batu1020_225_2 + $total_volume_batu1020_250_2 + $total_volume_batu1020_250_2_2;
                                    $total_volume_batu2030_2 = $total_volume_batu2030_125_2 + $total_volume_batu2030_225_2 + $total_volume_batu2030_250_2 + $total_volume_batu2030_250_2_2;
                        
                                    //BULAN 3
                                    $komposisi_125_3 = $this->db->select('(r.vol_produk_a * pk.presentase_a) as komposisi_semen_125, (vol_produk_a * pk.presentase_b) as komposisi_pasir_125, (vol_produk_a * pk.presentase_c) as komposisi_batu1020_125, (vol_produk_a * pk.presentase_d) as komposisi_batu2030_125')
                                    ->from('rak r')
                                    ->join('pmm_agregat pk', 'r.komposisi_125 = pk.id','left')
                                    ->where("r.tanggal_rencana_kerja between '$date_3_awal' and '$date_3_akhir'")
                                    ->get()->result_array();
                        
                                    $total_volume_semen_125_3 = 0;
                                    $total_volume_pasir_125_3 = 0;
                                    $total_volume_batu1020_125_3 = 0;
                                    $total_volume_batu2030_125_3 = 0;
                        
                                    foreach ($komposisi_125_3 as $x){
                                        $total_volume_semen_125_3 = $x['komposisi_semen_125'];
                                        $total_volume_pasir_125_3 = $x['komposisi_pasir_125'];
                                        $total_volume_batu1020_125_3 = $x['komposisi_batu1020_125'];
                                        $total_volume_batu2030_125_3 = $x['komposisi_batu2030_125'];
                                    }
                        
                                    $komposisi_225_3 = $this->db->select('(r.vol_produk_b * pk.presentase_a) as komposisi_semen_225, (vol_produk_b * pk.presentase_b) as komposisi_pasir_225, (vol_produk_b * pk.presentase_c) as komposisi_batu1020_225, (vol_produk_b * pk.presentase_d) as komposisi_batu2030_225')
                                    ->from('rak r')
                                    ->join('pmm_agregat pk', 'r.komposisi_225 = pk.id','left')
                                    ->where("r.tanggal_rencana_kerja between '$date_3_awal' and '$date_3_akhir'")
                                    ->get()->result_array();
                        
                                    $total_volume_semen_225_3 = 0;
                                    $total_volume_pasir_225_3 = 0;
                                    $total_volume_batu1020_225_3 = 0;
                                    $total_volume_batu2030_225_3 = 0;
                        
                                    foreach ($komposisi_225_3 as $x){
                                        $total_volume_semen_225_3 = $x['komposisi_semen_225'];
                                        $total_volume_pasir_225_3 = $x['komposisi_pasir_225'];
                                        $total_volume_batu1020_225_3 = $x['komposisi_batu1020_225'];
                                        $total_volume_batu2030_225_3 = $x['komposisi_batu2030_225'];
                                    }
                        
                                    $komposisi_250_3 = $this->db->select('(r.vol_produk_c * pk.presentase_a) as komposisi_semen_250, (vol_produk_c * pk.presentase_b) as komposisi_pasir_250, (vol_produk_c * pk.presentase_c) as komposisi_batu1020_250, (vol_produk_c * pk.presentase_d) as komposisi_batu2030_250')
                                    ->from('rak r')
                                    ->join('pmm_agregat pk', 'r.komposisi_250 = pk.id','left')
                                    ->where("r.tanggal_rencana_kerja between '$date_3_awal' and '$date_3_akhir'")
                                    ->get()->result_array();
                        
                                    $total_volume_semen_250_3 = 0;
                                    $total_volume_pasir_250_3 = 0;
                                    $total_volume_batu1020_250_3 = 0;
                                    $total_volume_batu2030_250_3 = 0;
                        
                                    foreach ($komposisi_250_3 as $x){
                                        $total_volume_semen_250_3 = $x['komposisi_semen_250'];
                                        $total_volume_pasir_250_3 = $x['komposisi_pasir_250'];
                                        $total_volume_batu1020_250_3 = $x['komposisi_batu1020_250'];
                                        $total_volume_batu2030_250_3 = $x['komposisi_batu2030_250'];
                                    }
                        
                                    $komposisi_250_2_3 = $this->db->select('(r.vol_produk_d * pk.presentase_a) as komposisi_semen_250_2, (vol_produk_d * pk.presentase_b) as komposisi_pasir_250_2, (vol_produk_d * pk.presentase_c) as komposisi_batu1020_250_2, (vol_produk_d * pk.presentase_d) as komposisi_batu2030_250_2')
                                    ->from('rak r')
                                    ->join('pmm_agregat pk', 'r.komposisi_250_2 = pk.id','left')
                                    ->where("r.tanggal_rencana_kerja between '$date_3_awal' and '$date_3_akhir'")
                                    ->get()->result_array();
                        
                                    $total_volume_semen_250_2_3 = 0;
                                    $total_volume_pasir_250_2_3 = 0;
                                    $total_volume_batu1020_250_2_3 = 0;
                                    $total_volume_batu2030_250_2_3 = 0;
                        
                                    foreach ($komposisi_250_2_3 as $x){
                                        $total_volume_semen_250_2_3 = $x['komposisi_semen_250_2'];
                                        $total_volume_pasir_250_2_3 = $x['komposisi_pasir_250_2'];
                                        $total_volume_batu1020_250_2_3 = $x['komposisi_batu1020_250_2'];
                                        $total_volume_batu2030_250_2_3 = $x['komposisi_batu2030_250_2'];
                                    }
                        
                                    $total_volume_semen_3 = $total_volume_semen_125_3 + $total_volume_semen_225_3 + $total_volume_semen_250_3 + $total_volume_semen_250_2_3;
                                    $total_volume_pasir_3 = $total_volume_pasir_125_3 + $total_volume_pasir_225_3 + $total_volume_pasir_250_3 + $total_volume_pasir_250_2_3;
                                    $total_volume_batu1020_3 = $total_volume_batu1020_125_3 + $total_volume_batu1020_225_3 + $total_volume_batu1020_250_3 + $total_volume_batu1020_250_2_3;
                                    $total_volume_batu2030_3 = $total_volume_batu2030_125_3 + $total_volume_batu2030_225_3 + $total_volume_batu2030_250_3 + $total_volume_batu2030_250_2_3;
                        
                                    //BULAN 4
                                    $komposisi_125_4 = $this->db->select('(r.vol_produk_a * pk.presentase_a) as komposisi_semen_125, (vol_produk_a * pk.presentase_b) as komposisi_pasir_125, (vol_produk_a * pk.presentase_c) as komposisi_batu1020_125, (vol_produk_a * pk.presentase_d) as komposisi_batu2030_125')
                                    ->from('rak r')
                                    ->join('pmm_agregat pk', 'r.komposisi_125 = pk.id','left')
                                    ->where("r.tanggal_rencana_kerja between '$date_4_awal' and '$date_4_akhir'")
                                    ->get()->result_array();
                        
                                    $total_volume_semen_125_4 = 0;
                                    $total_volume_pasir_125_4 = 0;
                                    $total_volume_batu1020_125_4 = 0;
                                    $total_volume_batu2030_125_4 = 0;
                        
                                    foreach ($komposisi_125_4 as $x){
                                        $total_volume_semen_125_4 = $x['komposisi_semen_125'];
                                        $total_volume_pasir_125_4 = $x['komposisi_pasir_125'];
                                        $total_volume_batu1020_125_4 = $x['komposisi_batu1020_125'];
                                        $total_volume_batu2030_125_4 = $x['komposisi_batu2030_125'];
                                    }
                        
                                    $komposisi_225_4 = $this->db->select('(r.vol_produk_b * pk.presentase_a) as komposisi_semen_225, (vol_produk_b * pk.presentase_b) as komposisi_pasir_225, (vol_produk_b * pk.presentase_c) as komposisi_batu1020_225, (vol_produk_b * pk.presentase_d) as komposisi_batu2030_225')
                                    ->from('rak r')
                                    ->join('pmm_agregat pk', 'r.komposisi_225 = pk.id','left')
                                    ->where("r.tanggal_rencana_kerja between '$date_4_awal' and '$date_4_akhir'")
                                    ->get()->result_array();
                        
                                    $total_volume_semen_225_4 = 0;
                                    $total_volume_pasir_225_4 = 0;
                                    $total_volume_batu1020_225_4 = 0;
                                    $total_volume_batu2030_225_4 = 0;
                        
                                    foreach ($komposisi_225_4 as $x){
                                        $total_volume_semen_225_4 = $x['komposisi_semen_225'];
                                        $total_volume_pasir_225_4 = $x['komposisi_pasir_225'];
                                        $total_volume_batu1020_225_4 = $x['komposisi_batu1020_225'];
                                        $total_volume_batu2030_225_4 = $x['komposisi_batu2030_225'];
                                    }
                        
                                    $komposisi_250_4 = $this->db->select('(r.vol_produk_c * pk.presentase_a) as komposisi_semen_250, (vol_produk_c * pk.presentase_b) as komposisi_pasir_250, (vol_produk_c * pk.presentase_c) as komposisi_batu1020_250, (vol_produk_c * pk.presentase_d) as komposisi_batu2030_250')
                                    ->from('rak r')
                                    ->join('pmm_agregat pk', 'r.komposisi_250 = pk.id','left')
                                    ->where("r.tanggal_rencana_kerja between '$date_4_awal' and '$date_4_akhir'")
                                    ->get()->result_array();
                        
                                    $total_volume_semen_250_4 = 0;
                                    $total_volume_pasir_250_4 = 0;
                                    $total_volume_batu1020_250_4 = 0;
                                    $total_volume_batu2030_250_4 = 0;
                        
                                    foreach ($komposisi_250_4 as $x){
                                        $total_volume_semen_250_4 = $x['komposisi_semen_250'];
                                        $total_volume_pasir_250_4 = $x['komposisi_pasir_250'];
                                        $total_volume_batu1020_250_4 = $x['komposisi_batu1020_250'];
                                        $total_volume_batu2030_250_4 = $x['komposisi_batu2030_250'];
                                    }
                        
                                    $komposisi_250_2_4 = $this->db->select('(r.vol_produk_d * pk.presentase_a) as komposisi_semen_250_2, (vol_produk_d * pk.presentase_b) as komposisi_pasir_250_2, (vol_produk_d * pk.presentase_c) as komposisi_batu1020_250_2, (vol_produk_d * pk.presentase_d) as komposisi_batu2030_250_2')
                                    ->from('rak r')
                                    ->join('pmm_agregat pk', 'r.komposisi_250_2 = pk.id','left')
                                    ->where("r.tanggal_rencana_kerja between '$date_4_awal' and '$date_4_akhir'")
                                    ->get()->result_array();
                        
                                    $total_volume_semen_250_2_4 = 0;
                                    $total_volume_pasir_250_2_4 = 0;
                                    $total_volume_batu1020_250_2_4 = 0;
                                    $total_volume_batu2030_250_2_4 = 0;
                        
                                    foreach ($komposisi_250_2_4 as $x){
                                        $total_volume_semen_250_2_4 = $x['komposisi_semen_250_2'];
                                        $total_volume_pasir_250_2_4 = $x['komposisi_pasir_250_2'];
                                        $total_volume_batu1020_250_2_4 = $x['komposisi_batu1020_250_2'];
                                        $total_volume_batu2030_250_2_4 = $x['komposisi_batu2030_250_2'];
                                    }
                        
                                    $total_volume_semen_4 = $total_volume_semen_125_4 + $total_volume_semen_225_4 + $total_volume_semen_250_4 + $total_volume_semen_250_2_4;
                                    $total_volume_pasir_4 = $total_volume_pasir_125_4 + $total_volume_pasir_225_4 + $total_volume_pasir_250_4 + $total_volume_pasir_250_2_4;
                                    $total_volume_batu1020_4 = $total_volume_batu1020_125_4 + $total_volume_batu1020_225_4 + $total_volume_batu1020_250_4 + $total_volume_batu1020_250_2_4;
                                    $total_volume_batu2030_4 = $total_volume_batu2030_125_4 + $total_volume_batu2030_225_4 + $total_volume_batu2030_250_4 + $total_volume_batu2030_250_2_4;
                        
                                    //BULAN 5
                                    $komposisi_125_5 = $this->db->select('(r.vol_produk_a * pk.presentase_a) as komposisi_semen_125, (vol_produk_a * pk.presentase_b) as komposisi_pasir_125, (vol_produk_a * pk.presentase_c) as komposisi_batu1020_125, (vol_produk_a * pk.presentase_d) as komposisi_batu2030_125')
                                    ->from('rak r')
                                    ->join('pmm_agregat pk', 'r.komposisi_125 = pk.id','left')
                                    ->where("r.tanggal_rencana_kerja between '$date_5_awal' and '$date_5_akhir'")
                                    ->get()->result_array();
                        
                                    $total_volume_semen_125_5 = 0;
                                    $total_volume_pasir_125_5 = 0;
                                    $total_volume_batu1020_125_5 = 0;
                                    $total_volume_batu2030_125_5 = 0;
                        
                                    foreach ($komposisi_125_5 as $x){
                                        $total_volume_semen_125_5 = $x['komposisi_semen_125'];
                                        $total_volume_pasir_125_5 = $x['komposisi_pasir_125'];
                                        $total_volume_batu1020_125_5 = $x['komposisi_batu1020_125'];
                                        $total_volume_batu2030_125_5 = $x['komposisi_batu2030_125'];
                                    }
                        
                                    $komposisi_225_5 = $this->db->select('(r.vol_produk_b * pk.presentase_a) as komposisi_semen_225, (vol_produk_b * pk.presentase_b) as komposisi_pasir_225, (vol_produk_b * pk.presentase_c) as komposisi_batu1020_225, (vol_produk_b * pk.presentase_d) as komposisi_batu2030_225')
                                    ->from('rak r')
                                    ->join('pmm_agregat pk', 'r.komposisi_225 = pk.id','left')
                                    ->where("r.tanggal_rencana_kerja between '$date_5_awal' and '$date_5_akhir'")
                                    ->get()->result_array();
                        
                                    $total_volume_semen_225_5 = 0;
                                    $total_volume_pasir_225_5 = 0;
                                    $total_volume_batu1020_225_5 = 0;
                                    $total_volume_batu2030_225_5 = 0;
                        
                                    foreach ($komposisi_225_5 as $x){
                                        $total_volume_semen_225_5 = $x['komposisi_semen_225'];
                                        $total_volume_pasir_225_5 = $x['komposisi_pasir_225'];
                                        $total_volume_batu1020_225_5 = $x['komposisi_batu1020_225'];
                                        $total_volume_batu2030_225_5 = $x['komposisi_batu2030_225'];
                                    }
                        
                                    $komposisi_250_5 = $this->db->select('(r.vol_produk_c * pk.presentase_a) as komposisi_semen_250, (vol_produk_c * pk.presentase_b) as komposisi_pasir_250, (vol_produk_c * pk.presentase_c) as komposisi_batu1020_250, (vol_produk_c * pk.presentase_d) as komposisi_batu2030_250')
                                    ->from('rak r')
                                    ->join('pmm_agregat pk', 'r.komposisi_250 = pk.id','left')
                                    ->where("r.tanggal_rencana_kerja between '$date_5_awal' and '$date_5_akhir'")
                                    ->get()->result_array();
                        
                                    $total_volume_semen_250_5 = 0;
                                    $total_volume_pasir_250_5 = 0;
                                    $total_volume_batu1020_250_5 = 0;
                                    $total_volume_batu2030_250_5 = 0;
                        
                                    foreach ($komposisi_250_5 as $x){
                                        $total_volume_semen_250_5 = $x['komposisi_semen_250'];
                                        $total_volume_pasir_250_5 = $x['komposisi_pasir_250'];
                                        $total_volume_batu1020_250_5 = $x['komposisi_batu1020_250'];
                                        $total_volume_batu2030_250_5 = $x['komposisi_batu2030_250'];
                                    }
                        
                                    $komposisi_250_2_5 = $this->db->select('(r.vol_produk_d * pk.presentase_a) as komposisi_semen_250_2, (vol_produk_d * pk.presentase_b) as komposisi_pasir_250_2, (vol_produk_d * pk.presentase_c) as komposisi_batu1020_250_2, (vol_produk_d * pk.presentase_d) as komposisi_batu2030_250_2')
                                    ->from('rak r')
                                    ->join('pmm_agregat pk', 'r.komposisi_250_2 = pk.id','left')
                                    ->where("r.tanggal_rencana_kerja between '$date_5_awal' and '$date_5_akhir'")
                                    ->get()->result_array();
                        
                                    $total_volume_semen_250_2_5 = 0;
                                    $total_volume_pasir_250_2_5 = 0;
                                    $total_volume_batu1020_250_2_5 = 0;
                                    $total_volume_batu2030_250_2_5 = 0;
                        
                                    foreach ($komposisi_250_2_5 as $x){
                                        $total_volume_semen_250_2_5 = $x['komposisi_semen_250_2'];
                                        $total_volume_pasir_250_2_5 = $x['komposisi_pasir_250_2'];
                                        $total_volume_batu1020_250_2_5 = $x['komposisi_batu1020_250_2'];
                                        $total_volume_batu2030_250_2_5 = $x['komposisi_batu2030_250_2'];
                                    }
                        
                                    $total_volume_semen_5 = $total_volume_semen_125_5 + $total_volume_semen_225_5 + $total_volume_semen_250_5 + $total_volume_semen_250_2_5;
                                    $total_volume_pasir_5 = $total_volume_pasir_125_5 + $total_volume_pasir_225_5 + $total_volume_pasir_250_5 + $total_volume_pasir_250_2_5;
                                    $total_volume_batu1020_5 = $total_volume_batu1020_125_5 + $total_volume_batu1020_225_5 + $total_volume_batu1020_250_5 + $total_volume_batu1020_250_2_5;
                                    $total_volume_batu2030_5 = $total_volume_batu2030_125_5 + $total_volume_batu2030_225_5 + $total_volume_batu2030_250_5 + $total_volume_batu2030_250_2_5;
                        
                                    //BULAN 6
                                    $komposisi_125_6 = $this->db->select('(r.vol_produk_a * pk.presentase_a) as komposisi_semen_125, (vol_produk_a * pk.presentase_b) as komposisi_pasir_125, (vol_produk_a * pk.presentase_c) as komposisi_batu1020_125, (vol_produk_a * pk.presentase_d) as komposisi_batu2030_125')
                                    ->from('rak r')
                                    ->join('pmm_agregat pk', 'r.komposisi_125 = pk.id','left')
                                    ->where("r.tanggal_rencana_kerja between '$date_6_awal' and '$date_6_akhir'")
                                    ->get()->result_array();
                        
                                    $total_volume_semen_125_6 = 0;
                                    $total_volume_pasir_125_6 = 0;
                                    $total_volume_batu1020_125_6 = 0;
                                    $total_volume_batu2030_125_6 = 0;
                        
                                    foreach ($komposisi_125_6 as $x){
                                        $total_volume_semen_125_6 = $x['komposisi_semen_125'];
                                        $total_volume_pasir_125_6 = $x['komposisi_pasir_125'];
                                        $total_volume_batu1020_125_6 = $x['komposisi_batu1020_125'];
                                        $total_volume_batu2030_125_6 = $x['komposisi_batu2030_125'];
                                    }
                        
                                    $komposisi_225_6 = $this->db->select('(r.vol_produk_b * pk.presentase_a) as komposisi_semen_225, (vol_produk_b * pk.presentase_b) as komposisi_pasir_225, (vol_produk_b * pk.presentase_c) as komposisi_batu1020_225, (vol_produk_b * pk.presentase_d) as komposisi_batu2030_225')
                                    ->from('rak r')
                                    ->join('pmm_agregat pk', 'r.komposisi_225 = pk.id','left')
                                    ->where("r.tanggal_rencana_kerja between '$date_6_awal' and '$date_6_akhir'")
                                    ->get()->result_array();
                        
                                    $total_volume_semen_225_6 = 0;
                                    $total_volume_pasir_225_6 = 0;
                                    $total_volume_batu1020_225_6 = 0;
                                    $total_volume_batu2030_225_6 = 0;
                        
                                    foreach ($komposisi_225_6 as $x){
                                        $total_volume_semen_225_6 = $x['komposisi_semen_225'];
                                        $total_volume_pasir_225_6 = $x['komposisi_pasir_225'];
                                        $total_volume_batu1020_225_6 = $x['komposisi_batu1020_225'];
                                        $total_volume_batu2030_225_6 = $x['komposisi_batu2030_225'];
                                    }
                        
                                    $komposisi_250_6 = $this->db->select('(r.vol_produk_c * pk.presentase_a) as komposisi_semen_250, (vol_produk_c * pk.presentase_b) as komposisi_pasir_250, (vol_produk_c * pk.presentase_c) as komposisi_batu1020_250, (vol_produk_c * pk.presentase_d) as komposisi_batu2030_250')
                                    ->from('rak r')
                                    ->join('pmm_agregat pk', 'r.komposisi_250 = pk.id','left')
                                    ->where("r.tanggal_rencana_kerja between '$date_6_awal' and '$date_6_akhir'")
                                    ->get()->result_array();
                        
                                    $total_volume_semen_250_6 = 0;
                                    $total_volume_pasir_250_6 = 0;
                                    $total_volume_batu1020_250_6 = 0;
                                    $total_volume_batu2030_250_6 = 0;
                        
                                    foreach ($komposisi_250_6 as $x){
                                        $total_volume_semen_250_6 = $x['komposisi_semen_250'];
                                        $total_volume_pasir_250_6 = $x['komposisi_pasir_250'];
                                        $total_volume_batu1020_250_6 = $x['komposisi_batu1020_250'];
                                        $total_volume_batu2030_250_6 = $x['komposisi_batu2030_250'];
                                    }
                        
                                    $komposisi_250_2_6 = $this->db->select('(r.vol_produk_d * pk.presentase_a) as komposisi_semen_250_2, (vol_produk_d * pk.presentase_b) as komposisi_pasir_250_2, (vol_produk_d * pk.presentase_c) as komposisi_batu1020_250_2, (vol_produk_d * pk.presentase_d) as komposisi_batu2030_250_2')
                                    ->from('rak r')
                                    ->join('pmm_agregat pk', 'r.komposisi_250_2 = pk.id','left')
                                    ->where("r.tanggal_rencana_kerja between '$date_6_awal' and '$date_6_akhir'")
                                    ->get()->result_array();
                        
                                    $total_volume_semen_250_2_6 = 0;
                                    $total_volume_pasir_250_2_6 = 0;
                                    $total_volume_batu1020_250_2_6 = 0;
                                    $total_volume_batu2030_250_2_6 = 0;
                        
                                    foreach ($komposisi_250_2_6 as $x){
                                        $total_volume_semen_250_2_6 = $x['komposisi_semen_250_2'];
                                        $total_volume_pasir_250_2_6 = $x['komposisi_pasir_250_2'];
                                        $total_volume_batu1020_250_2_6 = $x['komposisi_batu1020_250_2'];
                                        $total_volume_batu2030_250_2_6 = $x['komposisi_batu2030_250_2'];
                                    }
                        
                                    $total_volume_semen_6 = $total_volume_semen_125_6 + $total_volume_semen_225_6 + $total_volume_semen_250_6 + $total_volume_semen_250_2_6;
                                    $total_volume_pasir_6 = $total_volume_pasir_125_6 + $total_volume_pasir_225_6 + $total_volume_pasir_250_6 + $total_volume_pasir_250_2_6;
                                    $total_volume_batu1020_6 = $total_volume_batu1020_125_6 + $total_volume_batu1020_225_6 + $total_volume_batu1020_250_6 + $total_volume_batu1020_250_2_6;
                                    $total_volume_batu2030_6 = $total_volume_batu2030_125_6 + $total_volume_batu2030_225_6 + $total_volume_batu2030_250_6 + $total_volume_batu2030_250_2_6;
                                    
                                    //SOLAR
                                    $rap_solar = $this->db->select('rap.*')
                                    ->from('rap_alat rap')
                                    ->where('rap.status','PUBLISH')
                                    ->order_by('rap.id','desc')->limit(1)
                                    ->get()->row_array();
                                    
                                    $total_volume_solar_1 = $total_1_volume * $rap_solar['vol_bbm_solar'];
                                    $total_volume_solar_2 = $total_2_volume * $rap_solar['vol_bbm_solar'];
                                    $total_volume_solar_3 = $total_3_volume * $rap_solar['vol_bbm_solar'];
                                    $total_volume_solar_4 = $total_4_volume * $rap_solar['vol_bbm_solar'];
                                    $total_volume_solar_5 = $total_5_volume * $rap_solar['vol_bbm_solar'];
                                    $total_volume_solar_6 = $total_6_volume * $rap_solar['vol_bbm_solar'];
                                    ?>

                                    <?php
                                    $kategori  = $this->db->order_by('nama_kategori_produk', 'asc')->select('*')->get_where('kategori_produk', array('status' => 'PUBLISH'))->result_array();
                                    ?>
                                    <form method="POST" action="<?php echo site_url('pembelian/submit_pesanan_pembelian');?>" id="form-po" enctype="multipart/form-data" autocomplete="off">
                                        <table id="table-product" class="table table-bordered table-striped table-condensed table-center">
                                            <tbody>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <label>Subyek</label>
                                                        <input type="text" name="subject" class="form-control" required="" autocomplete="off"/>
                                                    </div>
                                                    <br />
                                                    <br />
                                                    <br />
                                                    <div class="col-sm-3">
                                                        <label>Tanggal Pesanan Pembelian</label>
                                                        <input type="date" name="date_po" class="form-control text-left" required="">
                                                    </div>
                                                    <br />
                                                    <br />
                                                    <br />
                                                    <div class="col-sm-6">
                                                        <label>No. Pesanan Pembelian</label>
                                                        <input type="text" name="no_po" class="form-control text-left" value="<?= $no_po;?>" required="">
                                                    </div>
                                                    <br />
                                                    <br />
                                                    <br />
                                                    <div class="col-sm-6">
                                                        <label>Kategori</label>
                                                        <select name="kategori_id" class="form-control select2" required="" autocomplete="off">
                                                            <option value="">Pilih Kategori</option>
                                                            <?php
                                                            foreach ($kategori as $key => $kat) {
                                                                ?>
                                                                <option value="<?php echo $kat['id'];?>"><?php echo $kat['nama_kategori_produk'];?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <br />
                                                    <br />
                                                    <br />
                                                </div>
                                            </tbody>
                                        </table>
                                        <table class="table-warning table-center" width="40%">
                                            <tr>
                                                <th colspan="4" style="background-color:#404040;color:white;">VOLUME</th>
                                            </tr>
                                            <tr>
                                                <th width="10%" style="background-color:#d63232; color:white;">KEBUTUHAN</th>
                                                <th width="10%" style="background-color:#c1e266;">SISA STOK</th>
                                                <th width="10%" style="background-color:#539ed6; color:white;">PROSES PO</th>
                                                <th width="10%" style="background-color:#cbcbcb;">SISA KEBUTUHAN</th>
                                            </tr>
                                            <tr>
                                                <td class="text-center">
                                                    <input style="background-color:#d63232; color:white;" class="form-control input-sm text-center" value="<?php echo number_format($kebutuhan,2,',','.');?>" readonly=""/>
                                                </td>
                                                <td class="text-center">
                                                    <input style="background-color:#c1e266;" class="form-control input-sm text-center" value="<?php echo number_format($stock_opname['display_volume'],2,',','.');?>" readonly=""/>
                                                </td>
                                                <td class="text-center">
                                                    <input style="background-color:#539ed6; color:white;" class="form-control input-sm text-center" value="<?php echo number_format($purchase_order,2,',','.');?>" readonly=""/>
                                                </td>
                                                <td class="text-center">
                                                    <input style="background-color:#cbcbcb;" class="form-control input-sm text-center" value="<?php echo number_format($kebutuhan - $stock_opname['display_volume'] - $purchase_order,2,',','.');?>" readonly=""/>
                                                </td>
                                            </tr>
                                        </table>
                                        <br />
                                        <table id="table-product" class="table table-bordered table-striped table-condensed table-center">
                                            <thead>
                                                <tr>
                                                    <th width="5%" rowspan="2">NO.</th>
                                                    <th >REKANAN</th>
                                                    <th>PRODUK</th>
                                                    <th>VOLUME</th>
                                                    <th>SATUAN</th>
                                                    <th>HARGA</th>
                                                    <th>NILAI</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <input type="hidden" name="request_no" class="form-control input-sm text-left" value="<?= $request_no;?>"/>
                                                    <input type="hidden" name="measure_id" class="form-control input-sm text-left" value="<?= $details['measure'];?>"/>

                                                    <input type="hidden" name="supplier_id" class="form-control input-sm text-left" value="<?= $row['supplier_id'];?>"/>
                                                    <input type="hidden" name="date_pkp" class="form-control input-sm text-left" value="2021-02-10"/>
                                                    <input type="hidden" name="rekanan" class="form-control input-sm text-left" value="<?= $row['syarat_pembayaran'];?>"/>
                                                    <input type="hidden" name="penawaran_pembelian_id" class="form-control input-sm text-left" value="<?= $row['id'];?>"/>
                                                    <input type="hidden" name="produk" class="form-control input-sm text-left" value="<?= $details['material_id'];?>"/>
                                                    <input type="hidden" name="tax_id" class="form-control input-sm text-left" value="<?= $details['tax_id'];?>"/>
                                                    <input type="hidden" name="pajak_id" class="form-control input-sm text-left" value="<?= $details['pajak_id'];?>"/>
                                                   
                                                    <td class="text-center">1.</td>
                                                    <td class="text-left">
                                                        <input class="form-control input-sm text-center" value="<?= $row['supplier'];?>" readonly=""/>
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-control input-sm text-center" value="<?= $details['material_id'] = $this->crud_global->GetField('produk',array('id'=>$details['material_id']),'nama_produk');?>" readonly=""/>
                                                    </td>
                                                    <td class="text-center">
                                                        <input name="volume" class="form-control numberformat text-center" value="<?php echo number_format($kebutuhan - $stock_opname['display_volume'] - $purchase_order,2,',','.');?>"/>
                                                    </td>
                                                    <td class="text-center">
                                                        <input name="satuan" class="form-control input-sm text-center" value="<?= $details['measure'] = $this->crud_global->GetField('pmm_measures',array('id'=>$details['measure']),'measure_name');?>" readonly=""/>
                                                    </td>
                                                    <td class="text-center">
                                                        <input name="harsat" class="form-control input-sm text-center" value="<?php echo number_format($details['price'],0,',','.');?>" readonly=""/>
                                                    </td>
                                                    <?php
                                                    $a = round($total_volume_semen_1,2);
                                                    $b = $details['price'];
                                                    $nilai = $a * $b
                                                    ?>
                                                    <td class="text-center">
                                                        <input name="nilai" class="form-control input-sm text-center" value="<?php echo number_format($nilai,0,',','.');?>" readonly=""/>
                                                    </td>
                                                </tr>
                                                <?php
                                                if ($details['tax_id'] == 4) {
                                                    $tax_0_1 = true;
                                                }
                                                if ($details['tax_id'] == 3) {
                                                    $tax_ppn_1 = ($nilai * 10) / 100;
                                                }
                                                if ($details['tax_id'] == 5) {
                                                    $tax_pph_1 = ($nilai * 2) / 100;
                                                }
                                                if ($details['tax_id'] == 6) {
                                                    $tax_ppn11_1 = ($nilai * 11) / 100;
                                                }
                                                if ($details['pajak_id'] == 4) {
                                                    $tax_0_2 = true;
                                                }
                                                if ($details['pajak_id'] == 3) {
                                                    $tax_ppn_2 = ($nilai * 10) / 100;
                                                }
                                                if ($details['pajak_id'] == 5) {
                                                    $tax_pph_2 = ($nilai * 2) / 100;
                                                }
                                                if ($details['pajak_id'] == 6) {
                                                    $tax_ppn11_2 = ($nilai * 11) / 100;
                                                }

                                                $ppn_1 = ($tax_ppn_1 - $tax_pph_1 + $tax_ppn11_1);
                                                $ppn_2 = ($tax_ppn_2 - $tax_pph_2 + $tax_ppn11_2);
                                                ?>
                                                <input type="hidden" name="tax" class="form-control input-sm text-left" value="<?= number_format($ppn_1,0,',','.');?>"/>
                                                <input type="hidden" name="pajak" class="form-control input-sm text-left" value="<?= number_format($ppn_2,0,',','.');?>"/>
                                                <input type="hidden" name="total" class="form-control input-sm text-left" value="<?= number_format($nilai,0,',','.');?>"/>
                                            <div>
                                            </tbody>
                                        </table>
                                        <table id="table-product" class="table table-bordered table-striped table-condensed table-center">
                                            <tbody>
                                                <div class="col-sm-8">
                                                    <label>Memo</label>
                                                    <textarea id="about_text" name="memo" class="form-control" data-required="false" rows="20">
<p style="font-size:6;"><b>Syarat &amp; Ketentuan :</b></p>
<p style="font-size:6;">1.&nbsp;Waktu Penyerahan : 2 Februari 2023 s/d 8 Februari 2023</p>
<p style="font-size:6;">2.&nbsp;Tempat Penyerahan : Proyek Bendungan Temef, Desa Konbaki, Kecamatan Polen, KAB. TTS</p>
<p style="font-size:6;">3.&nbsp;Cara Pembayaran : 30 (tiga puluh) hari kerja setelah berkas tagihan dinyatakan lolos verifikasi keuangan PT. Bia Bumi Jayendra, dengan melampirkan</p>
<p style="font-size:6;">&nbsp;&nbsp;&nbsp; dokumen sebagai berikut :</p>
<p style="font-size:6;">&nbsp;&nbsp;&nbsp;3.1 Tagihan</p>
<p style="font-size:6;">&nbsp;&nbsp;&nbsp;3.2 Kwitansi</p>
<p style="font-size:6;">&nbsp;&nbsp;&nbsp;3.3 BAP (Berita Acara Pembayaran)</p>
<p style="font-size:6;">&nbsp;&nbsp;&nbsp;3.4 BAST (Berita Acara Serah Terima) &amp; rekap surat jalan yang ditandatangani oleh pihak pemberi order dan penerima order</p>
<p style="font-size:6;">&nbsp;&nbsp;&nbsp;3.5 Surat Jalan Asli (Nomor PO harus tercantum pada setiap surat jalan)</p>
<p style="font-size:6;">&nbsp;&nbsp;&nbsp;3.6 PO</p>
<p style="font-size:6;">&nbsp;&nbsp;&nbsp;3.7 Faktur Pajak</p>
<p style="font-size:6;">4. Lain-lain :</p>
<p style="font-size:6;">&nbsp;&nbsp;&nbsp;4.1 Barang harus dalam kondisi 100% baik</p>
<p style="font-size:6;">&nbsp;&nbsp;&nbsp;4.2 Barang dikembalikan apabila tidak sesuai dengan pesanan</p>
                                                    </textarea>
                                                </div>
                                                </div>
                                            </tbody>
                                        </table>
                                        <div class="row">
                                        <div class="col-sm-12 text-right">
                                            <a href="<?php echo site_url('admin/pembelian');?>" class="btn btn-danger" style="margin-bottom:0;"><i class="fa fa-close"></i> Batal</a>
                                            <button type="submit" class="btn btn-success"><i class="fa fa-send"></i>  Kirim</button>
                                        </div>
                                        <br />
                                        <br />
                                        <br />
                                    </div>
                                    </form>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    
    <script type="text/javascript">
        var form_control = '';
    </script>
    <?php echo $this->Templates->Footer();?>
    <script src="<?php echo base_url();?>assets/back/theme/vendor/jquery.number.min.js"></script>
    <script src="<?php echo base_url();?>assets/back/theme/vendor/daterangepicker/moment.min.js"></script>
    <script src="<?php echo base_url();?>assets/back/theme/vendor/daterangepicker/daterangepicker.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/back/theme/vendor/daterangepicker/daterangepicker.css">
    <script src="<?php echo base_url();?>assets/back/theme/vendor/bootbox.min.js"></script>

    <script type="text/javascript">
        
    $('input.numberformat').number(true, 2,',','.' );
    $('input.rupiahformat').number(true, 0,',','.' );

    tinymce.init({
    selector: 'textarea#about_text',
    height: 200,
    menubar: false,
    });

    $('#form-po').submit(function(e){
            e.preventDefault();
            var currentForm = this;
            bootbox.confirm({
                message: "Apakah anda yakin untuk proses data ini ?",
                buttons: {
                    confirm: {
                        label: 'Yes',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: 'No',
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                    if(result){
                        currentForm.submit();
                    }
                    
                }
            });
            
        }); 
        </script>
    
</body>
</html>
