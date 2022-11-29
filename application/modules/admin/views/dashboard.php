<!doctype html>
<html lang="en" class="fixed">
<head>
<?php echo $this->Templates->Header();?>
</head>
<style type="text/css">
    .chart-container{
        position: relative; width:100%;height:350px;background: #fff;
    }
    .highcharts-figure,
    .highcharts-data-table table {
    min-width: 65%;
    max-width: 100%;
    }

    .highcharts-data-table table {
    font-family: Verdana, sans-serif;
    border-collapse: collapse;
    border: 1px solid #ebebeb;
    margin: 10px auto;
    text-align: center;
    width: 100%;
    max-width: 500px;
    }

    .highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
    }

    .highcharts-data-table th {
    font-weight: 600;
    padding: 0.5em;
    }

    .highcharts-data-table td,
    .highcharts-data-table th,
    .highcharts-data-table caption {
    padding: 0.5em;
    }

    .highcharts-data-table thead tr,
    .highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
    }

    .highcharts-data-table tr:hover {
    background: #f1f7ff;
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
    $date_juni_akhir = date('2022-06-30');
    $date_juli_awal = date('2022-07-01');
    $date_juli_akhir = date('2022-07-31');
    $date_agustus_awal = date('2022-08-01');
    $date_agustus_akhir = date('2022-08-31');
    $date_september_awal = date('2022-09-01');
    $date_september_akhir = date('2022-09-30');
    $date_oktober_awal = date('2022-10-01');
    $date_oktober_akhir = date('2022-10-31');
    $date_november_awal = date('2022-11-01');
    $date_november_akhir = date('2022-11-30');
    $date_desember_awal = date('2022-12-01');
    $date_desember_akhir = date('2022-12-31');
    $date_januari23_awal = date('2023-01-01');
    $date_januari23_akhir = date('2023-01-31');
    $date_februari23_awal = date('2023-02-01');
    $date_februari23_akhir = date('2023-02-28');
    $date_maret23_awal = date('2023-03-01');
    $date_maret23_akhir = date('2023-03-31');
    $date_april23_awal = date('2023-04-01');
    $date_april23_akhir = date('2023-04-30');
    $date_mei23_awal = date('2023-05-01');
    $date_mei23_akhir = date('2023-05-31');
    $date_juni23_awal = date('2023-06-01');
    $date_juni23_akhir = date('2023-06-30');
    $date_juli23_awal = date('2023-07-01');
    $date_juli23_akhir = date('2023-07-31');

    $stock_opname = $this->db->select('date')->order_by('date','desc')->limit(1,5)->get_where('pmm_remaining_materials_cat',array('status'=>'PUBLISH'))->row_array();
    $last_opname =  date('Y-m-d', strtotime($stock_opname['date']));
    $penjualan_now = $this->db->select('SUM(pp.display_price) as total')
    ->from('pmm_productions pp')
    ->join('penerima p', 'pp.client_id = p.id','left')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production < '$last_opname'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $rencana_kerja_all = $this->db->select('r.*')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '2022-07-01' and '2023-07-01'")
    ->get()->result_array();

    $nilai_jual_all = 0;
    $vol_produk_a = 0;
    $vol_produk_b = 0;
    $vol_produk_c = 0;
    $vol_produk_d = 0;
    $price_a = 0;
    $price_b = 0;
    $price_c = 0;
    $price_d = 0;

    foreach ($rencana_kerja_all as $x){
        $vol_produk_a += $x['vol_produk_a'];
        $vol_produk_b += $x['vol_produk_b'];
        $vol_produk_c += $x['vol_produk_c'];
        $vol_produk_d += $x['vol_produk_d'];
        $price_a = $x['price_a'];
        $price_b = $x['price_b'];
        $price_c = $x['price_c'];
        $price_d = $x['price_d'];
    }

    $price_produk_a = $vol_produk_a *  $price_a;
    $price_produk_b = $vol_produk_b *  $price_b;
    $price_produk_c = $vol_produk_c *  $price_c;
    $price_produk_d = $vol_produk_d *  $price_d;
    $nilai_jual_all = $price_produk_a + $price_produk_b + $price_produk_c + $price_produk_d;

    $total_kontrak_all = $penjualan_now['total'] + $nilai_jual_all;

    //JUNI
    $penjualan_juni = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_januari_awal' and '$date_juni_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_juni = $penjualan_juni['total'];

    //JULI
    $penjualan_juli = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_juli_awal' and '$date_juli_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_juli =  $total_penjualan_juni + $penjualan_juli['total'];
    $presentase_penjualan_juli = ($total_penjualan_juli / $total_kontrak_all) * 100;
    $net_juli = round($presentase_penjualan_juli,2);
    $net_juli_volume = round($penjualan_juli['volume'],2);

    //AGUSTUS
    $penjualan_agustus = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_agustus_awal' and '$date_agustus_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_agustus = $total_penjualan_juli + $penjualan_agustus['total'];
    $presentase_penjualan_agustus = ($total_penjualan_agustus / $total_kontrak_all) * 100;
    $net_agustus = round($presentase_penjualan_agustus,2);
    $net_agustus_volume = round($penjualan_agustus['volume'],2);

    $penjualan_september = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_september_awal' and '$date_september_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_september = $total_penjualan_agustus + $penjualan_september['total'];
    $presentase_penjualan_september = ($total_penjualan_september / $total_kontrak_all) * 100;
    $net_september = round($presentase_penjualan_september,2);
    $net_september_volume = round($penjualan_september['volume'],2);

    //OKTOBER
    $penjualan_oktober = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_oktober_awal' and '$date_oktober_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_oktober = $total_penjualan_september + $penjualan_oktober['total'];
    $presentase_penjualan_oktober = ($total_penjualan_oktober / $total_kontrak_all) * 100;
    $net_oktober = round($presentase_penjualan_oktober,2);
    $net_oktober_volume = round($penjualan_oktober['volume'],2);

    //NOVEMBER
    $penjualan_november = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_november_awal' and '$date_november_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_november = $total_penjualan_oktober + $penjualan_november['total'];
    $presentase_penjualan_november = ($total_penjualan_november / $total_kontrak_all) * 100;
    $net_november = round($presentase_penjualan_november,2);
    $net_november_volume = round($penjualan_november['volume'],2);
    
    //DESEEMBER
    $penjualan_desember = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_desember_awal' and '$date_desember_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_desember = $total_penjualan_november + $penjualan_desember['total'];
    $presentase_penjualan_desember = ($total_penjualan_desember / $total_kontrak_all) * 100;
    $net_desember = round($presentase_penjualan_desember,2);
    $net_desember_volume = round($penjualan_desember['volume'],2);

    //JANUARI23
    $penjualan_januari23 = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_januari23_awal' and '$date_januari23_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();
    
    $total_penjualan_januari23 = $total_penjualan_desember + $penjualan_januari23['total'];
    $presentase_penjualan_januari23 = ($total_penjualan_januari23 / $total_kontrak_all) * 100;
    $net_januari23 = round($presentase_penjualan_januari23,2);
    $net_januari23_volume = round($penjualan_januari23['volume'],2);

    //FEBRUARI23
    $penjualan_februari23 = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_februari23_awal' and '$date_februari23_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_februari23 = $total_penjualan_januari23 + $penjualan_februari23['total'];
    $presentase_penjualan_februari23 = ($total_penjualan_februari23 / $total_kontrak_all) * 100;
    $net_februari23 = round($presentase_penjualan_februari23,2);
    $net_februari23_volume = round($penjualan_februari23['volume'],2);

    //MARET23
    $penjualan_maret23 = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_maret23_awal' and '$date_maret23_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_maret23 = $total_penjualan_februari23 + $penjualan_maret23['total'];
    $presentase_penjualan_maret23 = ($total_penjualan_maret23 / $total_kontrak_all) * 100;
    $net_maret23 = round($presentase_penjualan_maret23,2);
    $net_maret23_volume = round($penjualan_maret23['volume'],2);

    //APRIL
    $penjualan_april23 = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_april23_awal' and '$date_april23_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_april23 = $total_penjualan_maret23 + $penjualan_april23['total'];
    $presentase_penjualan_april23 = ($total_penjualan_april23 / $total_kontrak_all) * 100;
    $net_april23 = round($presentase_penjualan_april23,2);
    $net_april23_volume = round($penjualan_april23['volume'],2);

    //MEI23
    $penjualan_mei23 = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_mei23_awal' and '$date_mei23_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_mei23 = $total_penjualan_april23 + $penjualan_mei23['total'];
    $presentase_penjualan_mei23 = ($total_penjualan_mei23 / $total_kontrak_all) * 100;
    $net_mei23 = round($presentase_penjualan_mei23,2);
    $net_mei23_volume = round($penjualan_mei23['volume'],2);

    //JUNI23
    $penjualan_juni23 = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_juni23_awal' and '$date_juni23_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_juni23 = $total_penjualan_mei23 + $penjualan_juni23['total'];
    $presentase_penjualan_juni23 = ($total_penjualan_juni23 / $total_kontrak_all) * 100;
    $net_juni23 = round($presentase_penjualan_juni23,2);
    $net_juni23_volume = round($penjualan_juni23['volume'],2);

    //JULI23
    $penjualan_juli23 = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_juli23_awal' and '$date_juli23_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_juli23 = $total_penjualan_juni23 + $penjualan_juli23['total'];
    $presentase_penjualan_juli23 = ($total_penjualan_juli23 / $total_kontrak_all) * 100;
    $net_juli23 = round($presentase_penjualan_juli23,2);
    $net_juli23_volume = round($penjualan_juli23['volume'],2);

    //SISA
    $sisa_realisasi = $total_kontrak_all - $total_penjualan_juli23;
    $presentase_penjualan_sisa = ($total_penjualan_juli23 / $total_kontrak_all) * 100;
    $net_sisa = round($presentase_penjualan_sisa,2);
    ?>

    <?php
    //JUNI
    $rencana_kerja_juni = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_juni_akhir'")
    ->get()->row_array();

    $volume_juni_produk_a = $rencana_kerja_juni['vol_produk_a'];
    $volume_juni_produk_b = $rencana_kerja_juni['vol_produk_b'];
    $volume_juni_produk_c = $rencana_kerja_juni['vol_produk_c'];
    $volume_juni_produk_d = $rencana_kerja_juni['vol_produk_d'];

    $total_juni_volume = $volume_juni_produk_a + $volume_juni_produk_b + $volume_juni_produk_c + $volume_juni_produk_d;
		
    $nilai_jual_125_juni = $volume_juni_produk_a * $rencana_kerja_juni['price_a'];
    $nilai_jual_225_juni = $volume_juni_produk_b * $rencana_kerja_juni['price_b'];
    $nilai_jual_250_juni = $volume_juni_produk_c * $rencana_kerja_juni['price_c'];
    $nilai_jual_250_18_juni = $volume_juni_produk_d * $rencana_kerja_juni['price_d'];
    $nilai_jual_all_juni = $nilai_jual_125_juni + $nilai_jual_225_juni + $nilai_jual_250_juni + $nilai_jual_250_18_juni;
    
    $total_juni_nilai = $nilai_jual_all_juni;

    //JULI
    $rencana_kerja_juli = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_juli_awal' and '$date_juli_akhir'")
    ->get()->row_array();

    $volume_juli_produk_a = $rencana_kerja_juli['vol_produk_a'];
    $volume_juli_produk_b = $rencana_kerja_juli['vol_produk_b'];
    $volume_juli_produk_c = $rencana_kerja_juli['vol_produk_c'];
    $volume_juli_produk_d = $rencana_kerja_juli['vol_produk_d'];

    $total_juli_volume = $volume_juli_produk_a + $volume_juli_produk_b + $volume_juli_produk_c + $volume_juli_produk_d;
		
    $nilai_jual_125_juli = $volume_juli_produk_a * $rencana_kerja_juli['price_a'];
    $nilai_jual_225_juli = $volume_juli_produk_b * $rencana_kerja_juli['price_b'];
    $nilai_jual_250_juli = $volume_juli_produk_c * $rencana_kerja_juli['price_c'];
    $nilai_jual_250_18_juli = $volume_juli_produk_d * $rencana_kerja_juli['price_d'];
    $nilai_jual_all_juli = $total_juni_nilai + $nilai_jual_125_juli + $nilai_jual_225_juli + $nilai_jual_250_juli + $nilai_jual_250_18_juli;
    
    $total_juli_nilai = ($nilai_jual_all_juli / $total_kontrak_all) * 100;;
    $net_juli_rap = round($total_juli_nilai,2);

    //AGUSTUS
    $rencana_kerja_agustus = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_agustus_awal' and '$date_agustus_akhir'")
    ->get()->row_array();

    $volume_agustus_produk_a = $rencana_kerja_agustus['vol_produk_a'];
    $volume_agustus_produk_b = $rencana_kerja_agustus['vol_produk_b'];
    $volume_agustus_produk_c = $rencana_kerja_agustus['vol_produk_c'];
    $volume_agustus_produk_d = $rencana_kerja_agustus['vol_produk_d'];

    $total_agustus_volume = $volume_agustus_produk_a + $volume_agustus_produk_b + $volume_agustus_produk_c + $volume_agustus_produk_d;
		
    $nilai_jual_125_agustus = $volume_agustus_produk_a * $rencana_kerja_agustus['price_a'];
    $nilai_jual_225_agustus = $volume_agustus_produk_b * $rencana_kerja_agustus['price_b'];
    $nilai_jual_250_agustus = $volume_agustus_produk_c * $rencana_kerja_agustus['price_c'];
    $nilai_jual_250_18_agustus = $volume_agustus_produk_d * $rencana_kerja_agustus['price_d'];
    $nilai_jual_all_agustus = $nilai_jual_all_juli + $nilai_jual_125_agustus + $nilai_jual_225_agustus + $nilai_jual_250_agustus + $nilai_jual_250_18_agustus;
    
    $total_agustus_nilai = ($nilai_jual_all_agustus / $total_kontrak_all) * 100;;
    $net_agustus_rap = round($total_agustus_nilai,2);

    //SEPTEMBER
    $rencana_kerja_september = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_september_awal' and '$date_september_akhir'")
    ->get()->row_array();

    $volume_september_produk_a = $rencana_kerja_september['vol_produk_a'];
    $volume_september_produk_b = $rencana_kerja_september['vol_produk_b'];
    $volume_september_produk_c = $rencana_kerja_september['vol_produk_c'];
    $volume_september_produk_d = $rencana_kerja_september['vol_produk_d'];

    $total_september_volume = $volume_september_produk_a + $volume_september_produk_b + $volume_september_produk_c + $volume_september_produk_d;
		
    $nilai_jual_125_september = $volume_september_produk_a * $rencana_kerja_september['price_a'];
    $nilai_jual_225_september = $volume_september_produk_b * $rencana_kerja_september['price_b'];
    $nilai_jual_250_september = $volume_september_produk_c * $rencana_kerja_september['price_c'];
    $nilai_jual_250_18_september = $volume_september_produk_d * $rencana_kerja_september['price_d'];
    $nilai_jual_all_september = $nilai_jual_all_agustus + $nilai_jual_125_september + $nilai_jual_225_september + $nilai_jual_250_september + $nilai_jual_250_18_september;
    
    $total_september_nilai = ($nilai_jual_all_september / $total_kontrak_all) * 100;;
    $net_september_rap = round($total_september_nilai,2);

    //OKTOBER
    $rencana_kerja_oktober = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_oktober_awal' and '$date_oktober_akhir'")
    ->get()->row_array();

    $volume_oktober_produk_a = $rencana_kerja_oktober['vol_produk_a'];
    $volume_oktober_produk_b = $rencana_kerja_oktober['vol_produk_b'];
    $volume_oktober_produk_c = $rencana_kerja_oktober['vol_produk_c'];
    $volume_oktober_produk_d = $rencana_kerja_oktober['vol_produk_d'];

    $total_oktober_volume = $volume_oktober_produk_a + $volume_oktober_produk_b + $volume_oktober_produk_c + $volume_oktober_produk_d;
		
    $nilai_jual_125_oktober = $volume_oktober_produk_a * $rencana_kerja_oktober['price_a'];
    $nilai_jual_225_oktober = $volume_oktober_produk_b * $rencana_kerja_oktober['price_b'];
    $nilai_jual_250_oktober = $volume_oktober_produk_c * $rencana_kerja_oktober['price_c'];
    $nilai_jual_250_18_oktober = $volume_oktober_produk_d * $rencana_kerja_oktober['price_d'];
    $nilai_jual_all_oktober = $nilai_jual_all_september + $nilai_jual_125_oktober + $nilai_jual_225_oktober + $nilai_jual_250_oktober + $nilai_jual_250_18_oktober;
    
    $total_oktober_nilai = ($nilai_jual_all_oktober / $total_kontrak_all) * 100;;
    $net_oktober_rap = round($total_oktober_nilai,2);

    //NOVEMBER
    $rencana_kerja_november = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_november_awal' and '$date_november_akhir'")
    ->get()->row_array();

    $volume_november_produk_a = $rencana_kerja_november['vol_produk_a'];
    $volume_november_produk_b = $rencana_kerja_november['vol_produk_b'];
    $volume_november_produk_c = $rencana_kerja_november['vol_produk_c'];
    $volume_november_produk_d = $rencana_kerja_november['vol_produk_d'];

    $total_november_volume = $volume_november_produk_a + $volume_november_produk_b + $volume_november_produk_c + $volume_november_produk_d;
		
    $nilai_jual_125_november = $volume_november_produk_a * $rencana_kerja_november['price_a'];
    $nilai_jual_225_november = $volume_november_produk_b * $rencana_kerja_november['price_b'];
    $nilai_jual_250_november = $volume_november_produk_c * $rencana_kerja_november['price_c'];
    $nilai_jual_250_18_november = $volume_november_produk_d * $rencana_kerja_november['price_d'];
    $nilai_jual_all_november = $nilai_jual_all_oktober + $nilai_jual_125_november + $nilai_jual_225_november + $nilai_jual_250_november + $nilai_jual_250_18_november;
    
    $total_november_nilai = ($nilai_jual_all_november / $total_kontrak_all) * 100;;
    $net_november_rap = round($total_november_nilai,2);

    //DESEMBER
    $rencana_kerja_desember = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_desember_awal' and '$date_desember_akhir'")
    ->get()->row_array();

    $volume_desember_produk_a = $rencana_kerja_desember['vol_produk_a'];
    $volume_desember_produk_b = $rencana_kerja_desember['vol_produk_b'];
    $volume_desember_produk_c = $rencana_kerja_desember['vol_produk_c'];
    $volume_desember_produk_d = $rencana_kerja_desember['vol_produk_d'];

    $total_desember_volume = $volume_desember_produk_a + $volume_desember_produk_b + $volume_desember_produk_c + $volume_desember_produk_d;
		
    $nilai_jual_125_desember = $volume_desember_produk_a * $rencana_kerja_desember['price_a'];
    $nilai_jual_225_desember = $volume_desember_produk_b * $rencana_kerja_desember['price_b'];
    $nilai_jual_250_desember = $volume_desember_produk_c * $rencana_kerja_desember['price_c'];
    $nilai_jual_250_18_desember = $volume_desember_produk_d * $rencana_kerja_desember['price_d'];
    $nilai_jual_all_desember = $nilai_jual_all_november + $nilai_jual_125_desember + $nilai_jual_225_desember + $nilai_jual_250_desember + $nilai_jual_250_18_desember;
    
    $total_desember_nilai = ($nilai_jual_all_desember / $total_kontrak_all) * 100;;
    $net_desember_rap = round($total_desember_nilai,2);

    //JANUARI23
    $rencana_kerja_januari23 = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
    ->get()->row_array();

    $volume_januari23_produk_a = $rencana_kerja_januari23['vol_produk_a'];
    $volume_januari23_produk_b = $rencana_kerja_januari23['vol_produk_b'];
    $volume_januari23_produk_c = $rencana_kerja_januari23['vol_produk_c'];
    $volume_januari23_produk_d = $rencana_kerja_januari23['vol_produk_d'];

    $total_januari23_volume = $volume_januari23_produk_a + $volume_januari23_produk_b + $volume_januari23_produk_c + $volume_januari23_produk_d;
		
    $nilai_jual_125_januari23 = $volume_januari23_produk_a * $rencana_kerja_januari23['price_a'];
    $nilai_jual_225_januari23 = $volume_januari23_produk_b * $rencana_kerja_januari23['price_b'];
    $nilai_jual_250_januari23 = $volume_januari23_produk_c * $rencana_kerja_januari23['price_c'];
    $nilai_jual_250_18_januari23 = $volume_januari23_produk_d * $rencana_kerja_januari23['price_d'];
    $nilai_jual_all_januari23 = $nilai_jual_all_desember + $nilai_jual_125_januari23 + $nilai_jual_225_januari23 + $nilai_jual_250_januari23 + $nilai_jual_250_18_januari23;
    
    $total_januari23_nilai = ($nilai_jual_all_januari23 / $total_kontrak_all) * 100;;
    $net_januari23_rap = round($total_januari23_nilai,2);

    //FEBRUARI23
    $rencana_kerja_februari23 = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_februari23_awal' and '$date_februari23_akhir'")
    ->get()->row_array();

    $volume_februari23_produk_a = $rencana_kerja_februari23['vol_produk_a'];
    $volume_februari23_produk_b = $rencana_kerja_februari23['vol_produk_b'];
    $volume_februari23_produk_c = $rencana_kerja_februari23['vol_produk_c'];
    $volume_februari23_produk_d = $rencana_kerja_februari23['vol_produk_d'];

    $total_februari23_volume = $volume_februari23_produk_a + $volume_februari23_produk_b + $volume_februari23_produk_c + $volume_februari23_produk_d;
		
    $nilai_jual_125_februari23 = $volume_februari23_produk_a * $rencana_kerja_februari23['price_a'];
    $nilai_jual_225_februari23 = $volume_februari23_produk_b * $rencana_kerja_februari23['price_b'];
    $nilai_jual_250_februari23 = $volume_februari23_produk_c * $rencana_kerja_februari23['price_c'];
    $nilai_jual_250_18_februari23 = $volume_februari23_produk_d * $rencana_kerja_februari23['price_d'];
    $nilai_jual_all_februari23 = $nilai_jual_all_januari23 + $nilai_jual_125_februari23 + $nilai_jual_225_februari23 + $nilai_jual_250_februari23 + $nilai_jual_250_18_februari23;
    
    $total_februari23_nilai = ($nilai_jual_all_februari23 / $total_kontrak_all) * 100;;
    $net_februari23_rap = round($total_februari23_nilai,2);

    //MARET23
    $rencana_kerja_maret23 = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_maret23_awal' and '$date_maret23_akhir'")
    ->get()->row_array();

    $volume_maret23_produk_a = $rencana_kerja_maret23['vol_produk_a'];
    $volume_maret23_produk_b = $rencana_kerja_maret23['vol_produk_b'];
    $volume_maret23_produk_c = $rencana_kerja_maret23['vol_produk_c'];
    $volume_maret23_produk_d = $rencana_kerja_maret23['vol_produk_d'];

    $total_maret23_volume = $volume_maret23_produk_a + $volume_maret23_produk_b + $volume_maret23_produk_c + $volume_maret23_produk_d;
		
    $nilai_jual_125_maret23 = $volume_maret23_produk_a * $rencana_kerja_maret23['price_a'];
    $nilai_jual_225_maret23 = $volume_maret23_produk_b * $rencana_kerja_maret23['price_b'];
    $nilai_jual_250_maret23 = $volume_maret23_produk_c * $rencana_kerja_maret23['price_c'];
    $nilai_jual_250_18_maret23 = $volume_maret23_produk_d * $rencana_kerja_maret23['price_d'];
    $nilai_jual_all_maret23 = $nilai_jual_all_februari23 + $nilai_jual_125_maret23 + $nilai_jual_225_maret23 + $nilai_jual_250_maret23 + $nilai_jual_250_18_maret23;
    
    $total_maret23_nilai = ($nilai_jual_all_maret23 / $total_kontrak_all) * 100;;
    $net_maret23_rap = round($total_maret23_nilai,2);

    //APRIL23
    $rencana_kerja_april23 = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_april23_akhir'")
    ->get()->row_array();

    $volume_april23_produk_a = $rencana_kerja_april23['vol_produk_a'];
    $volume_april23_produk_b = $rencana_kerja_april23['vol_produk_b'];
    $volume_april23_produk_c = $rencana_kerja_april23['vol_produk_c'];
    $volume_april23_produk_d = $rencana_kerja_april23['vol_produk_d'];

    $total_april23_volume = $volume_april23_produk_a + $volume_april23_produk_b + $volume_april23_produk_c + $volume_april23_produk_d;
		
    $nilai_jual_125_april23 = $volume_april23_produk_a * $rencana_kerja_april23['price_a'];
    $nilai_jual_225_april23 = $volume_april23_produk_b * $rencana_kerja_april23['price_b'];
    $nilai_jual_250_april23 = $volume_april23_produk_c * $rencana_kerja_april23['price_c'];
    $nilai_jual_250_18_april23 = $volume_april23_produk_d * $rencana_kerja_april23['price_d'];
    $nilai_jual_all_april23 = $nilai_jual_125_april23 + $nilai_jual_225_april23 + $nilai_jual_250_april23 + $nilai_jual_250_18_april23;
    
    $total_april23_nilai = ($nilai_jual_all_april23 / $total_kontrak_all) * 100;;
    $net_april23_rap = round($total_april23_nilai,2);

    //MEI23
    $rencana_kerja_mei23 = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_mei23_awal' and '$date_mei23_akhir'")
    ->get()->row_array();

    $volume_mei23_produk_a = $rencana_kerja_mei23['vol_produk_a'];
    $volume_mei23_produk_b = $rencana_kerja_mei23['vol_produk_b'];
    $volume_mei23_produk_c = $rencana_kerja_mei23['vol_produk_c'];
    $volume_mei23_produk_d = $rencana_kerja_mei23['vol_produk_d'];

    $total_mei23_volume = $volume_mei23_produk_a + $volume_mei23_produk_b + $volume_mei23_produk_c + $volume_mei23_produk_d;
		
    $nilai_jual_125_mei23 = $volume_mei23_produk_a * $rencana_kerja_mei23['price_a'];
    $nilai_jual_225_mei23 = $volume_mei23_produk_b * $rencana_kerja_mei23['price_b'];
    $nilai_jual_250_mei23 = $volume_mei23_produk_c * $rencana_kerja_mei23['price_c'];
    $nilai_jual_250_18_mei23 = $volume_mei23_produk_d * $rencana_kerja_mei23['price_d'];
    $nilai_jual_all_mei23 = $nilai_jual_all_april23 + $nilai_jual_125_mei23 + $nilai_jual_225_mei23 + $nilai_jual_250_mei23 + $nilai_jual_250_18_mei23;
    
    $total_mei23_nilai = ($nilai_jual_all_mei23 / $total_kontrak_all) * 100;;
    $net_mei23_rap = round($total_mei23_nilai,2);

    //JUNI23
    $rencana_kerja_juni23 = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_juni23_awal' and '$date_juni23_akhir'")
    ->get()->row_array();

    $volume_juni23_produk_a = $rencana_kerja_juni23['vol_produk_a'];
    $volume_juni23_produk_b = $rencana_kerja_juni23['vol_produk_b'];
    $volume_juni23_produk_c = $rencana_kerja_juni23['vol_produk_c'];
    $volume_juni23_produk_d = $rencana_kerja_juni23['vol_produk_d'];

    $total_juni23_volume = $volume_juni23_produk_a + $volume_juni23_produk_b + $volume_juni23_produk_c + $volume_juni23_produk_d;
		
    $nilai_jual_125_juni23 = $volume_juni23_produk_a * $rencana_kerja_juni23['price_a'];
    $nilai_jual_225_juni23 = $volume_juni23_produk_b * $rencana_kerja_juni23['price_b'];
    $nilai_jual_250_juni23 = $volume_juni23_produk_c * $rencana_kerja_juni23['price_c'];
    $nilai_jual_250_18_juni23 = $volume_juni23_produk_d * $rencana_kerja_juni23['price_d'];
    $nilai_jual_all_juni23 =  $nilai_jual_all_mei23 + $nilai_jual_125_juni23 + $nilai_jual_225_juni23 + $nilai_jual_250_juni23 + $nilai_jual_250_18_juni23;
    
    $total_juni23_nilai = ($nilai_jual_all_juni23 / $total_kontrak_all) * 100;;
    $net_juni23_rap = round($total_juni23_nilai,2);

    //JULI23
    $rencana_kerja_juli23 = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_juli23_awal' and '$date_juli23_akhir'")
    ->get()->row_array();

    $volume_juli23_produk_a = $rencana_kerja_juli23['vol_produk_a'];
    $volume_juli23_produk_b = $rencana_kerja_juli23['vol_produk_b'];
    $volume_juli23_produk_c = $rencana_kerja_juli23['vol_produk_c'];
    $volume_juli23_produk_d = $rencana_kerja_juli23['vol_produk_d'];

    $total_juli23_volume = $volume_juli23_produk_a + $volume_juli23_produk_b + $volume_juli23_produk_c + $volume_juli23_produk_d;
		
    $nilai_jual_125_juli23 = $volume_juli23_produk_a * $rencana_kerja_juli23['price_a'];
    $nilai_jual_225_juli23 = $volume_juli23_produk_b * $rencana_kerja_juli23['price_b'];
    $nilai_jual_250_juli23 = $volume_juli23_produk_c * $rencana_kerja_juli23['price_c'];
    $nilai_jual_250_18_juli23 = $volume_juli23_produk_d * $rencana_kerja_juli23['price_d'];
    $nilai_jual_all_juli23 =  $nilai_jual_all_juni23 + $nilai_jual_125_juli23 + $nilai_jual_225_juli23 + $nilai_jual_250_juli23 + $nilai_jual_250_18_juli23;
    
    $total_juli23_nilai = ($nilai_jual_all_juli23 / $total_kontrak_all) * 100;
    $net_juli23_rap = round($total_juli23_nilai,2);

    //SISA
    $sisa = $total_kontrak_all - $nilai_jual_all_juli23;
    $akumulasi_sisa = $nilai_jual_all_juli23 + $sisa;
    $total_sisa_nilai = ($akumulasi_sisa / $total_kontrak_all) * 100;
    $net_sisa_rap = round($total_sisa_nilai,2);
    ?>
    <?php
    $selisih_juli = round($net_juli_rap - $net_juli,2);
    $selisih_agustus = round($net_agustus_rap - $net_agustus,2);
    $selisih_september = round($net_september_rap - $net_september,2);
    $selisih_oktober = round($net_oktober_rap - $net_oktober,2);
    $selisih_november = round($net_november_rap - $net_november,2);
    $selisih_desember = round($net_desember_rap - $net_desember,2);
    $selisih_januari23 = round($net_januari23_rap - $net_januari23,2);
    $selisih_februari23 = round($net_februari23_rap - $net_februari23,2);
    $selisih_maret23 = round($net_maret23_rap - $net_maret23,2);
    $selisih_april23 = round($net_april23_rap - $net_april23,2);
    $selisih_mei23 = round($net_mei23_rap - $net_mei23,2);
    $selisih_juni23 = round($net_juni23_rap - $net_juni23,2);
    $selisih_juli23 = round($net_juli23_rap - $net_juli23,2);
    $selisih_sisa = round($net_sisa_rap - $net_sisa,2);
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
                <div class="col-sm-12">
                    <div class="tomorrow"
                    data-location-id="056826"
                    data-language="ID"
                    data-unit-system="METRIC"
                    data-skin="dark"
                    data-widget-type="upcoming"
                    style="padding-bottom:22px;position:relative;"
                    >
                    <a
                        href="https://www.tomorrow.io/weather-api/"
                        rel="nofollow noopener noreferrer"
                        target="_blank"
                        style="position: absolute; bottom: 0; transform: translateX(-50%); left: 50%;"
                    >
                        <img
                        alt="Powered by the Tomorrow.io Weather API"
                        src="https://weather-website-client.tomorrow.io/img/powered-by.svg"
                        width="250"
                        height="18"
                        />
                    </a>
                    </div>
                    <br />
                </div>
                <div class="col-sm-12">
                    <figure class="highcharts-figure">
                        <div id="container"></div>
                        
                    </figure>
                    <br />
                </div>
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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script type="text/javascript">
	//script untuk membuat grafik, perhatikan setiap komentar agar paham
    $(function () {
        var chart;
        $(document).ready(function() {
            chart = new Highcharts.Chart({
                chart: {
                    renderTo: 'container', //letakan grafik di div id container
                    //Type grafik, anda bisa ganti menjadi area,bar,column dan bar
                    type: 'line',
                    marginRight: 130,
                    marginBottom: 75,
                    backgroundColor: {
                        linearGradient: [0, 0, 500, 500],
                        stops: [
                            [0, 'rgb(200,233,233)'],
                            [1, 'rgb(164,219,232)']
                        ]
                    },
                },
                title: {
                    text: 'REALISASI PRODUKSI',
                    x: -20 //center
                },
                subtitle: {
                    text: 'PT. BIA BUMI JAYENDRA - TEMEF',
                    x: -20
                },
                xAxis: { //X axis menampilkan data bulan
                    categories: ['Jul 22','Agu 22','Sep 22','Okt 22','Nov 22','Des 22','Jan 23','Feb 23','Mar 23','Apr 23','Mei 23','Jun 23','Jul 23','Sisa']
                },
                yAxis: {
                    title: {  //label yAxis
                        text: 'Realisasi + Rencana (Jul 22 + Jul 23) <br /> <?php echo number_format($total_kontrak_all,0,',','.'); ?>'
                    },
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080' //warna dari grafik line
                    }],
                    labels: {
                        format: '{value} %'
                    },
                    min: 0,
                    max: 100,
                    tickInterval: 10,
                },
                tooltip: { 
                //fungsi tooltip, ini opsional, kegunaan dari fungsi ini 
                //akan menampikan data di titik tertentu di grafik saat mouseover
                    formatter: function() {
                            return '<b>'+ this.series.name +'</b><br/>'+ 
                            ''+ 'Presentase' +': '+ this.y + '%<br/>';
                            //''+ 'Vol' +': '+ this.x + '';

                            //'<b>'+ 'Presentase' +': '+ this.y +'%'</b><br/>'+ 
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

                plotOptions: {
                    spline: {
                        lineWidth: 4,
                        states: {
                            hover: {
                                lineWidth: 5
                            }
                        },
                        marker: {
                            enabled: true
                        }
                    }
                },
        
                series: [{  
                    name: 'RAP %',  
                    
                    data: [<?php echo json_encode($net_juli_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_agustus_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_september_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_oktober_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_november_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_desember_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_januari23_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_februari23_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_maret23_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_april23_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_mei23_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_juni23_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_juli23_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_sisa_rap, JSON_NUMERIC_CHECK); ?>],

                    color: '#000000',
                },
                {  
                    name: 'Realisasi %',  
                    
                    data: [<?php echo json_encode($net_juli, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_agustus, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_september, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_oktober, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_november, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_desember, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_januari23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_februari23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_maret23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_april23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_mei23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_juni23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_juli23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_sisa, JSON_NUMERIC_CHECK); ?>],

                    color: '#FF0000',

                    zones: [{
                        value: 25
                    }, {
                        dashStyle: 'dot'
                    }]
                },
                {  
                    name: 'Evaluasi %',  
                    
                    data: [<?php echo json_encode($selisih_juli, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_agustus, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_september, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_oktober, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_november, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_desember, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_januari23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_februari23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_maret23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_april23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_mei23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_juni23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_juli23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_sisa, JSON_NUMERIC_CHECK); ?>],

                    color: '#38761D',

                    visible: false
                },
                ]
            });
        });
        
    });
</script>
<script>
    (function(d, s, id) {
        if (d.getElementById(id)) {
            if (window.__TOMORROW__) {
                window.__TOMORROW__.renderWidget();
            }
            return;
        }
        const fjs = d.getElementsByTagName(s)[0];
        const js = d.createElement(s);
        js.id = id;
        js.src = "https://www.tomorrow.io/v1/widget/sdk/sdk.bundle.min.js";

        fjs.parentNode.insertBefore(js, fjs);
    })(document, 'script', 'tomorrow-sdk');
</script>

</body>
</html>
