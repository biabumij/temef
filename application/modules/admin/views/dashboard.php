<!doctype html>
<html lang="en" class="fixed">
<head>
<?php echo $this->Templates->Header();?>
</head>
<style type="text/css">
    .chart-container{
        position: relative; max-width: 100%; height:350px; background: #fff;
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
            // gmdate('F j, Y', strtotime('first day of january this year'));
            $arr_date = date("d-m-Y", strtotime('first day of january this year')).' - '.date("d-m-Y", strtotime('last day of december this year'));
        }
    ?>
    <?php
    $date_now = date('Y-m-d');
    $date_januari_awal = date('2022-01-01');
    $date_januari_akhir = date('2022-01-31');
    $date_februari_awal = date('2022-02-01');
    $date_februari_akhir = date('2022-02-28');
    $date_maret_awal = date('2022-03-01');
    $date_maret_akhir = date('2022-03-31');
    $date_april_awal = date('2022-04-01');
    $date_april_akhir = date('2022-04-30');
    $date_mei_awal = date('2022-05-01');
    $date_mei_akhir = date('2022-05-31');
    $date_juni_awal = date('2022-06-01');
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
    $date_akumulasi_awal = date('2022-01-01');
    $date_akumulasi_akhir = date('2023-07-31');

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

    $rencana_kerja_2022_1 = $this->db->select('r.*')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '2021-12-30' and '2021-12-30'")
    ->get()->row_array();

    $rencana_kerja_2022_2 = $this->db->select('r.*')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '2021-12-31' and '2021-12-31'")
    ->get()->row_array();

    $volume_rap_2022_produk_a = $rencana_kerja_2022_1['vol_produk_a'] + $rencana_kerja_2022_2['vol_produk_a'];
    $volume_rap_2022_produk_b = $rencana_kerja_2022_1['vol_produk_b'] + $rencana_kerja_2022_2['vol_produk_b'];
    $volume_rap_2022_produk_c = $rencana_kerja_2022_1['vol_produk_c'] + $rencana_kerja_2022_2['vol_produk_c'];
    $volume_rap_2022_produk_d = $rencana_kerja_2022_1['vol_produk_d'] + $rencana_kerja_2022_2['vol_produk_d'];
    $total_rap_volume_2022 = $rencana_kerja_2022_1['vol_produk_a'] + $rencana_kerja_2022_1['vol_produk_b'] + $rencana_kerja_2022_1['vol_produk_c'] + $rencana_kerja_2022_1['vol_produk_d'] + $rencana_kerja_2022_2['vol_produk_a'] + $rencana_kerja_2022_2['vol_produk_b'] + $rencana_kerja_2022_2['vol_produk_c'] + $rencana_kerja_2022_2['vol_produk_d'];

    $price_produk_a_1 = $rencana_kerja_2022_1['vol_produk_a'] * $rencana_kerja_2022_1['price_a'];
    $price_produk_b_1 = $rencana_kerja_2022_1['vol_produk_b'] * $rencana_kerja_2022_1['price_b'];
    $price_produk_c_1 = $rencana_kerja_2022_1['vol_produk_c'] * $rencana_kerja_2022_1['price_c'];
    $price_produk_d_1 = $rencana_kerja_2022_1['vol_produk_d'] * $rencana_kerja_2022_1['price_d'];

    $price_produk_a_2 = $rencana_kerja_2022_2['vol_produk_a'] * $rencana_kerja_2022_2['price_a'];
    $price_produk_b_2 = $rencana_kerja_2022_2['vol_produk_b'] * $rencana_kerja_2022_2['price_b'];
    $price_produk_c_2 = $rencana_kerja_2022_2['vol_produk_c'] * $rencana_kerja_2022_2['price_c'];
    $price_produk_d_2 = $rencana_kerja_2022_2['vol_produk_d'] * $rencana_kerja_2022_2['price_d'];

    $nilai_jual_all_2022 = $price_produk_a_1 + $price_produk_b_1 + $price_produk_c_1 + $price_produk_d_1 + $price_produk_a_2 + $price_produk_b_2 + $price_produk_c_2 + $price_produk_d_2;
    $total_kontrak_all = $nilai_jual_all_2022;

    //FEBRUARI
    $penjualan_februari = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_februari_awal' and '$date_februari_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_februari = $penjualan_februari['total'];
    $presentase_penjualan_februari = ($total_penjualan_februari / $total_kontrak_all) * 100;
    $net_februari = round($presentase_penjualan_februari,2);

    //MARET
    $penjualan_maret = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_maret_awal' and '$date_maret_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_maret = $total_penjualan_februari + $penjualan_maret['total'];
    $presentase_penjualan_maret = ($total_penjualan_maret / $total_kontrak_all) * 100;
    $net_maret = round($presentase_penjualan_maret,2);

    //APRIL
    $penjualan_april = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_april_awal' and '$date_april_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_april = $total_penjualan_maret + $penjualan_april['total'];
    $presentase_penjualan_april = ($total_penjualan_april / $total_kontrak_all) * 100;
    $net_april = round($presentase_penjualan_april,2);

    //MEI
    $penjualan_mei = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_mei_awal' and '$date_mei_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_mei = $total_penjualan_april + $penjualan_mei['total'];
    $presentase_penjualan_mei = ($total_penjualan_mei / $total_kontrak_all) * 100;
    $net_mei = round($presentase_penjualan_mei,2);

    //JUNI
    $penjualan_juni = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_juni_awal' and '$date_juni_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_juni = $total_penjualan_mei + $penjualan_juni['total'];
    $presentase_penjualan_juni = ($total_penjualan_juni / $total_kontrak_all) * 100;
    $net_juni = round($presentase_penjualan_juni,2);

    //JULI
    $penjualan_juli = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_juli_awal' and '$date_juli_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_juli = $total_penjualan_juni + $penjualan_juli['total'];
    $presentase_penjualan_juli = ($total_penjualan_juli / $total_kontrak_all) * 100;
    $net_juli = round($presentase_penjualan_juli,2);

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

    //SEPTEMBER
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
    
    //DESEMBER
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

    //APRIL23
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

    //AKUMULASI
    $penjualan_akumulasi = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_akumulasi_awal' and '$date_akumulasi_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_akumulasi = $penjualan_akumulasi['total'];
    $presentase_penjualan_akumulasi = ($total_penjualan_akumulasi / $total_kontrak_all) * 100;
    $net_akumulasi = round($presentase_penjualan_akumulasi,2);

    //SISA
    $sisa_realisasi = $total_kontrak_all - $total_penjualan_juli23;
    $presentase_penjualan_sisa = ($total_penjualan_juli23 / $total_kontrak_all) * 100;
    $net_sisa = round($presentase_penjualan_sisa,2);
    ?>

    <?php
    //FEBRUARI
    $rencana_kerja_februari = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_februari_awal' and '$date_februari_akhir'")
    ->get()->row_array();

    $volume_februari_produk_a = $rencana_kerja_februari['vol_produk_a'];
    $volume_februari_produk_b = $rencana_kerja_februari['vol_produk_b'];
    $volume_februari_produk_c = $rencana_kerja_februari['vol_produk_c'];
    $volume_februari_produk_d = $rencana_kerja_februari['vol_produk_d'];

    $total_februari_volume = $volume_februari_produk_a + $volume_februari_produk_b + $volume_februari_produk_c + $volume_februari_produk_d;
		
    $nilai_jual_125_februari = $volume_februari_produk_a * $rencana_kerja_februari['price_a'];
    $nilai_jual_225_februari = $volume_februari_produk_b * $rencana_kerja_februari['price_b'];
    $nilai_jual_250_februari = $volume_februari_produk_c * $rencana_kerja_februari['price_c'];
    $nilai_jual_250_18_februari = $volume_februari_produk_d * $rencana_kerja_februari['price_d'];
    $nilai_jual_all_februari = $nilai_jual_125_februari + $nilai_jual_225_februari + $nilai_jual_250_februari + $nilai_jual_250_18_februari;
    
    $total_februari_nilai = ($nilai_jual_all_februari / $total_kontrak_all) * 100;
    $net_februari_rap = round($total_februari_nilai,2);

    //MARET
    $rencana_kerja_maret = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_maret_awal' and '$date_maret_akhir'")
    ->get()->row_array();

    $volume_maret_produk_a = $rencana_kerja_maret['vol_produk_a'];
    $volume_maret_produk_b = $rencana_kerja_maret['vol_produk_b'];
    $volume_maret_produk_c = $rencana_kerja_maret['vol_produk_c'];
    $volume_maret_produk_d = $rencana_kerja_maret['vol_produk_d'];

    $total_maret_volume = $volume_maret_produk_a + $volume_maret_produk_b + $volume_maret_produk_c + $volume_maret_produk_d;
		
    $nilai_jual_125_maret = $volume_maret_produk_a * $rencana_kerja_maret['price_a'];
    $nilai_jual_225_maret = $volume_maret_produk_b * $rencana_kerja_maret['price_b'];
    $nilai_jual_250_maret = $volume_maret_produk_c * $rencana_kerja_maret['price_c'];
    $nilai_jual_250_18_maret = $volume_maret_produk_d * $rencana_kerja_maret['price_d'];
    $nilai_jual_all_maret = $nilai_jual_all_februari + $nilai_jual_125_maret + $nilai_jual_225_maret + $nilai_jual_250_maret + $nilai_jual_250_18_maret;
    
    $total_maret_nilai = ($nilai_jual_all_maret / $total_kontrak_all) * 100;
    $net_maret_rap = round($total_maret_nilai,2);

    //APRIL
    $rencana_kerja_april = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_april_awal' and '$date_april_akhir'")
    ->get()->row_array();

    $volume_april_produk_a = $rencana_kerja_april['vol_produk_a'];
    $volume_april_produk_b = $rencana_kerja_april['vol_produk_b'];
    $volume_april_produk_c = $rencana_kerja_april['vol_produk_c'];
    $volume_april_produk_d = $rencana_kerja_april['vol_produk_d'];

    $total_april_volume = $volume_april_produk_a + $volume_april_produk_b + $volume_april_produk_c + $volume_april_produk_d;
		
    $nilai_jual_125_april = $volume_april_produk_a * $rencana_kerja_april['price_a'];
    $nilai_jual_225_april = $volume_april_produk_b * $rencana_kerja_april['price_b'];
    $nilai_jual_250_april = $volume_april_produk_c * $rencana_kerja_april['price_c'];
    $nilai_jual_250_18_april = $volume_april_produk_d * $rencana_kerja_april['price_d'];
    $nilai_jual_all_april = $nilai_jual_all_maret + $nilai_jual_125_april + $nilai_jual_225_april + $nilai_jual_250_april + $nilai_jual_250_18_april;
    
    $total_april_nilai = ($nilai_jual_all_april / $total_kontrak_all) * 100;
    $net_april_rap = round($total_april_nilai,2);

    //MEI
    $rencana_kerja_mei = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_mei_awal' and '$date_mei_akhir'")
    ->get()->row_array();

    $volume_mei_produk_a = $rencana_kerja_mei['vol_produk_a'];
    $volume_mei_produk_b = $rencana_kerja_mei['vol_produk_b'];
    $volume_mei_produk_c = $rencana_kerja_mei['vol_produk_c'];
    $volume_mei_produk_d = $rencana_kerja_mei['vol_produk_d'];

    $total_mei_volume = $volume_mei_produk_a + $volume_mei_produk_b + $volume_mei_produk_c + $volume_mei_produk_d;
		
    $nilai_jual_125_mei = $volume_mei_produk_a * $rencana_kerja_mei['price_a'];
    $nilai_jual_225_mei = $volume_mei_produk_b * $rencana_kerja_mei['price_b'];
    $nilai_jual_250_mei = $volume_mei_produk_c * $rencana_kerja_mei['price_c'];
    $nilai_jual_250_18_mei = $volume_mei_produk_d * $rencana_kerja_mei['price_d'];
    $nilai_jual_all_mei = $nilai_jual_all_april + $nilai_jual_125_mei + $nilai_jual_225_mei + $nilai_jual_250_mei + $nilai_jual_250_18_mei;
    
    $total_mei_nilai = ($nilai_jual_all_mei / $total_kontrak_all) * 100;
    $net_mei_rap = round($total_mei_nilai,2);
    
    //JUNI
    $rencana_kerja_juni = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_juni_awal' and '$date_juni_akhir'")
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
    $nilai_jual_all_juni = $nilai_jual_all_mei + $nilai_jual_125_juni + $nilai_jual_225_juni + $nilai_jual_250_juni + $nilai_jual_250_18_juni;
    
    $total_juni_nilai = ($nilai_jual_all_juni / $total_kontrak_all) * 100;
    $net_juni_rap = round($total_juni_nilai,2);

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
    $nilai_jual_all_juli = $nilai_jual_all_juni + $nilai_jual_125_juli + $nilai_jual_225_juli + $nilai_jual_250_juli + $nilai_jual_250_18_juli;
    
    $total_juli_nilai = ($nilai_jual_all_juli / $total_kontrak_all) * 100;
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
    
    $total_agustus_nilai = ($nilai_jual_all_agustus / $total_kontrak_all) * 100;
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
    
    $total_september_nilai = ($nilai_jual_all_september / $total_kontrak_all) * 100;
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
    
    $total_oktober_nilai = ($nilai_jual_all_oktober / $total_kontrak_all) * 100;
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
    
    $total_november_nilai = ($nilai_jual_all_november / $total_kontrak_all) * 100;
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
    
    $total_desember_nilai = ($nilai_jual_all_desember / $total_kontrak_all) * 100;
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
    
    $total_januari23_nilai = ($nilai_jual_all_januari23 / $total_kontrak_all) * 100;
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
    
    $total_februari23_nilai = ($nilai_jual_all_februari23 / $total_kontrak_all) * 100;
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
    
    $total_maret23_nilai = ($nilai_jual_all_maret23 / $total_kontrak_all) * 100;
    $net_maret23_rap = round($total_maret23_nilai,2);

    //APRIL23
    $rencana_kerja_april23 = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_april23_awal' and '$date_april23_akhir'")
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
    $nilai_jual_all_april23 = $nilai_jual_all_maret23 + $nilai_jual_125_april23 + $nilai_jual_225_april23 + $nilai_jual_250_april23 + $nilai_jual_250_18_april23;
    
    $total_april23_nilai = ($nilai_jual_all_april23 / $total_kontrak_all) * 100;
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
    
    $total_mei23_nilai = ($nilai_jual_all_mei23 / $total_kontrak_all) * 100;
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
    
    $total_juni23_nilai = ($nilai_jual_all_juni23 / $total_kontrak_all) * 100;
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
                    <figure class="highcharts-figure">
                        <div id="container"></div>
                        
                    </figure>
                    <br />
                </div>
                <?php
                //$stock_opname = $this->db->select('date')->order_by('date','desc')->limit(1,5)->get_where('pmm_remaining_materials_cat',array('status'=>'PUBLISH'))->row_array();
                //$stock_opname = $this->db->select('date')->order_by('date','desc')->limit(1,1)->get_where('pmm_remaining_materials_cat',array('status'=>'PUBLISH'))->row_array();
                //$last_opname =  date('Y-m-d', strtotime($stock_opname['date']));
    
                //$date_november_awal = date('Y-m-d', strtotime('+1 days', strtotime($last_opname)));
                //$date_november_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_november_awal)));
                //$november = date('F Y', strtotime('+1 days', strtotime($last_opname)));
    
                //$date_desember_awal = date('Y-m-d', strtotime('+1 days', strtotime($date_november_akhir)));
                //$date_desember_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_desember_awal)));
                //$desember = date('F Y', strtotime('+1 days', strtotime($date_november_akhir)));
    
                //$date_januari23_awal = date('Y-m-d', strtotime('+1 days', strtotime($date_desember_akhir)));
                //$date_januari23_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_januari23_awal)));
                //$januari23 = date('F Y', strtotime('+1 days', strtotime($date_desember_akhir)));

                //FEBRUARI	
                $akumulasi_februari = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar) as total_nilai_keluar')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_februari_awal' and '$date_februari_akhir')")
                ->get()->row_array();

                $nilai_alat_februari = $this->db->select('SUM(prm.display_price) as nilai')
                ->from('pmm_receipt_material prm')
                ->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
                ->join('produk p', 'prm.material_id = p.id','left')
                ->where("prm.date_receipt between '$date_februari_awal' and '$date_februari_akhir'")
                ->where("p.kategori_produk = '5'")
                ->where("po.status in ('PUBLISH','CLOSED')")
                ->get()->row_array();

                $akumulasi_bbm_februari = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar_2) as total_nilai_keluar_2')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_februari_awal' and '$date_februari_akhir')")
                ->get()->row_array();

                $insentif_tm_februari = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun = 220")
                ->where("status = 'PAID'")
                ->where("(tanggal_transaksi between '$date_februari_awal' and '$date_februari_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_februari = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_februari_awal' and '$date_februari_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_jurnal_februari = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_februari_awal' and '$date_februari_akhir')")
                ->get()->row_array();

                $overhead_15_februari = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_februari_awal' and '$date_februari_akhir')")
                ->get()->row_array();

                $overhead_jurnal_15_februari = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_februari_awal' and '$date_februari_akhir')")
                ->get()->row_array();

                $diskonto_februari = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun = 168")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_februari_awal' and '$date_februari_akhir')")
                ->get()->row_array();   

                $cost_februari = $akumulasi_februari['total_nilai_keluar'] + $nilai_alat_februari['nilai'] + $akumulasi_bbm_februari['total_nilai_keluar_2'] + $insentif_tm_februari['total'] + $biaya_alat_lainnya_februari['total'] + $biaya_alat_lainnya_jurnal_februari['total'] + $overhead_15_februari['total'] + $overhead_jurnal_15_februari['total'] + $diskonto_februari['total'];
                $laba_rugi_februari = $penjualan_februari['total'] - $cost_februari;
                $presentase_laba_rugi_februari = ($penjualan_februari['total']!=0)?($laba_rugi_februari / $penjualan_februari['total'])  * 100:0;
                $presentase_laba_rugi_februari_fix = round($presentase_laba_rugi_februari,2);

                //MARET	
                $akumulasi_maret = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar) as total_nilai_keluar')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_maret_awal' and '$date_maret_akhir')")
                ->get()->row_array();

                $nilai_alat_maret = $this->db->select('SUM(prm.display_price) as nilai')
                ->from('pmm_receipt_material prm')
                ->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
                ->join('produk p', 'prm.material_id = p.id','left')
                ->where("prm.date_receipt between '$date_maret_awal' and '$date_maret_akhir'")
                ->where("p.kategori_produk = '5'")
                ->where("po.status in ('PUBLISH','CLOSED')")
                ->get()->row_array();

                $akumulasi_bbm_maret = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar_2) as total_nilai_keluar_2')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_maret_awal' and '$date_maret_akhir')")
                ->get()->row_array();

                $insentif_tm_maret = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun = 220")
                ->where("status = 'PAID'")
                ->where("(tanggal_transaksi between '$date_maret_awal' and '$date_maret_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_maret = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_maret_awal' and '$date_maret_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_jurnal_maret = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_maret_awal' and '$date_maret_akhir')")
                ->get()->row_array();

                $overhead_15_maret = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_maret_awal' and '$date_maret_akhir')")
                ->get()->row_array();

                $overhead_jurnal_15_maret = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_maret_awal' and '$date_maret_akhir')")
                ->get()->row_array();

                $diskonto_maret = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun = 168")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_maret_awal' and '$date_maret_akhir')")
                ->get()->row_array();   

                $cost_maret = $akumulasi_maret['total_nilai_keluar'] + $nilai_alat_maret['nilai'] + $akumulasi_bbm_maret['total_nilai_keluar_2'] + $insentif_tm_maret['total'] + $biaya_alat_lainnya_maret['total'] + $biaya_alat_lainnya_jurnal_maret['total'] + $overhead_15_maret['total'] + $overhead_jurnal_15_maret['total'] + $diskonto_maret['total'];
                $laba_rugi_maret = $penjualan_maret['total'] - $cost_maret;
                $presentase_laba_rugi_maret = ($penjualan_maret['total']!=0)?($laba_rugi_maret / $penjualan_maret['total'])  * 100:0;
                $presentase_laba_rugi_maret_fix = round($presentase_laba_rugi_maret,2);

                //APRIL	
                $akumulasi_april = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar) as total_nilai_keluar')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_april_awal' and '$date_april_akhir')")
                ->get()->row_array();

                $nilai_alat_april = $this->db->select('SUM(prm.display_price) as nilai')
                ->from('pmm_receipt_material prm')
                ->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
                ->join('produk p', 'prm.material_id = p.id','left')
                ->where("prm.date_receipt between '$date_april_awal' and '$date_april_akhir'")
                ->where("p.kategori_produk = '5'")
                ->where("po.status in ('PUBLISH','CLOSED')")
                ->get()->row_array();

                $akumulasi_bbm_april = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar_2) as total_nilai_keluar_2')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_april_awal' and '$date_april_akhir')")
                ->get()->row_array();

                $insentif_tm_april = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun = 220")
                ->where("status = 'PAID'")
                ->where("(tanggal_transaksi between '$date_april_awal' and '$date_april_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_april = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_april_awal' and '$date_april_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_jurnal_april = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_april_awal' and '$date_april_akhir')")
                ->get()->row_array();

                $overhead_15_april = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_april_awal' and '$date_april_akhir')")
                ->get()->row_array();

                $overhead_jurnal_15_april = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_april_awal' and '$date_april_akhir')")
                ->get()->row_array();

                $diskonto_april = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun = 168")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_april_awal' and '$date_april_akhir')")
                ->get()->row_array();   

                $cost_april = $akumulasi_april['total_nilai_keluar'] + $nilai_alat_april['nilai'] + $akumulasi_bbm_april['total_nilai_keluar_2'] + $insentif_tm_april['total'] + $biaya_alat_lainnya_april['total'] + $biaya_alat_lainnya_jurnal_april['total'] + $overhead_15_april['total'] + $overhead_jurnal_15_april['total'] + $diskonto_april['total'];
                $laba_rugi_april = $penjualan_april['total'] - $cost_april;
                $presentase_laba_rugi_april = ($penjualan_april['total']!=0)?($laba_rugi_april / $penjualan_april['total'])  * 100:0;
                $presentase_laba_rugi_april_fix = round($presentase_laba_rugi_april,2);

                //MEI	
                $akumulasi_mei = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar) as total_nilai_keluar')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_mei_awal' and '$date_mei_akhir')")
                ->get()->row_array();

                $nilai_alat_mei = $this->db->select('SUM(prm.display_price) as nilai')
                ->from('pmm_receipt_material prm')
                ->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
                ->join('produk p', 'prm.material_id = p.id','left')
                ->where("prm.date_receipt between '$date_mei_awal' and '$date_mei_akhir'")
                ->where("p.kategori_produk = '5'")
                ->where("po.status in ('PUBLISH','CLOSED')")
                ->get()->row_array();

                $akumulasi_bbm_mei = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar_2) as total_nilai_keluar_2')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_mei_awal' and '$date_mei_akhir')")
                ->get()->row_array();

                $insentif_tm_mei = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun = 220")
                ->where("status = 'PAID'")
                ->where("(tanggal_transaksi between '$date_mei_awal' and '$date_mei_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_mei = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_mei_awal' and '$date_mei_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_jurnal_mei = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_mei_awal' and '$date_mei_akhir')")
                ->get()->row_array();

                $overhead_15_mei = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_mei_awal' and '$date_mei_akhir')")
                ->get()->row_array();

                $overhead_jurnal_15_mei = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_mei_awal' and '$date_mei_akhir')")
                ->get()->row_array();

                $diskonto_mei = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun = 168")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_mei_awal' and '$date_mei_akhir')")
                ->get()->row_array();   

                $cost_mei = $akumulasi_mei['total_nilai_keluar'] + $nilai_alat_mei['nilai'] + $akumulasi_bbm_mei['total_nilai_keluar_2'] + $insentif_tm_mei['total'] + $biaya_alat_lainnya_mei['total'] + $biaya_alat_lainnya_jurnal_mei['total'] + $overhead_15_mei['total'] + $overhead_jurnal_15_mei['total'] + $diskonto_mei['total'];
                $laba_rugi_mei = $penjualan_mei['total'] - $cost_mei;
                $presentase_laba_rugi_mei = ($penjualan_mei['total']!=0)?($laba_rugi_mei / $penjualan_mei['total'])  * 100:0;
                $presentase_laba_rugi_mei_fix = round($presentase_laba_rugi_mei,2);

                //JUNI	
                $akumulasi_juni = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar) as total_nilai_keluar')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_juni_awal' and '$date_juni_akhir')")
                ->get()->row_array();

                $nilai_alat_juni = $this->db->select('SUM(prm.display_price) as nilai')
                ->from('pmm_receipt_material prm')
                ->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
                ->join('produk p', 'prm.material_id = p.id','left')
                ->where("prm.date_receipt between '$date_juni_awal' and '$date_juni_akhir'")
                ->where("p.kategori_produk = '5'")
                ->where("po.status in ('PUBLISH','CLOSED')")
                ->get()->row_array();

                $akumulasi_bbm_juni = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar_2) as total_nilai_keluar_2')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_juni_awal' and '$date_juni_akhir')")
                ->get()->row_array();

                $insentif_tm_juni = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun = 220")
                ->where("status = 'PAID'")
                ->where("(tanggal_transaksi between '$date_juni_awal' and '$date_juni_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_juni = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_juni_awal' and '$date_juni_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_jurnal_juni = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_juni_awal' and '$date_juni_akhir')")
                ->get()->row_array();

                $overhead_15_juni = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_juni_awal' and '$date_juni_akhir')")
                ->get()->row_array();

                $overhead_jurnal_15_juni = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_juni_awal' and '$date_juni_akhir')")
                ->get()->row_array();

                $diskonto_juni = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun = 168")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_juni_awal' and '$date_juni_akhir')")
                ->get()->row_array();   

                $cost_juni = $akumulasi_juni['total_nilai_keluar'] + $nilai_alat_juni['nilai'] + $akumulasi_bbm_juni['total_nilai_keluar_2'] + $insentif_tm_juni['total'] + $biaya_alat_lainnya_juni['total'] + $biaya_alat_lainnya_jurnal_juni['total'] + $overhead_15_juni['total'] + $overhead_jurnal_15_juni['total'] + $diskonto_juni['total'];
                $laba_rugi_juni = $penjualan_juni['total'] - $cost_juni;
                $presentase_laba_rugi_juni = ($penjualan_juni['total']!=0)?($laba_rugi_juni / $penjualan_juni['total'])  * 100:0;
                $presentase_laba_rugi_juni_fix = round($presentase_laba_rugi_juni,2);

                //JULI	
                $akumulasi_juli = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar) as total_nilai_keluar')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_juli_awal' and '$date_juli_akhir')")
                ->get()->row_array();

                $nilai_alat_juli = $this->db->select('SUM(prm.display_price) as nilai')
                ->from('pmm_receipt_material prm')
                ->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
                ->join('produk p', 'prm.material_id = p.id','left')
                ->where("prm.date_receipt between '$date_juli_awal' and '$date_juli_akhir'")
                ->where("p.kategori_produk = '5'")
                ->where("po.status in ('PUBLISH','CLOSED')")
                ->get()->row_array();

                $akumulasi_bbm_juli = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar_2) as total_nilai_keluar_2')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_juli_awal' and '$date_juli_akhir')")
                ->get()->row_array();

                $insentif_tm_juli = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun = 220")
                ->where("status = 'PAID'")
                ->where("(tanggal_transaksi between '$date_juli_awal' and '$date_juli_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_juli = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_juli_awal' and '$date_juli_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_jurnal_juli = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_juli_awal' and '$date_juli_akhir')")
                ->get()->row_array();

                $overhead_15_juli = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_juli_awal' and '$date_juli_akhir')")
                ->get()->row_array();

                $overhead_jurnal_15_juli = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_juli_awal' and '$date_juli_akhir')")
                ->get()->row_array();

                $diskonto_juli = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun = 168")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_juli_awal' and '$date_juli_akhir')")
                ->get()->row_array();   

                $cost_juli = $akumulasi_juli['total_nilai_keluar'] + $nilai_alat_juli['nilai'] + $akumulasi_bbm_juli['total_nilai_keluar_2'] + $insentif_tm_juli['total'] + $biaya_alat_lainnya_juli['total'] + $biaya_alat_lainnya_jurnal_juli['total'] + $overhead_15_juli['total'] + $overhead_jurnal_15_juli['total'] + $diskonto_juli['total'];
                $laba_rugi_juli = $penjualan_juli['total'] - $cost_juli;
                $presentase_laba_rugi_juli = ($penjualan_juli['total']!=0)?($laba_rugi_juli / $penjualan_juli['total'])  * 100:0;
                $presentase_laba_rugi_juli_fix = round($presentase_laba_rugi_juli,2);

                //AGUSTUS	
                $akumulasi_agustus = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar) as total_nilai_keluar')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_agustus_awal' and '$date_agustus_akhir')")
                ->get()->row_array();

                $nilai_alat_agustus = $this->db->select('SUM(prm.display_price) as nilai')
                ->from('pmm_receipt_material prm')
                ->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
                ->join('produk p', 'prm.material_id = p.id','left')
                ->where("prm.date_receipt between '$date_agustus_awal' and '$date_agustus_akhir'")
                ->where("p.kategori_produk = '5'")
                ->where("po.status in ('PUBLISH','CLOSED')")
                ->get()->row_array();

                $akumulasi_bbm_agustus = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar_2) as total_nilai_keluar_2')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_agustus_awal' and '$date_agustus_akhir')")
                ->get()->row_array();

                $insentif_tm_agustus = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun = 220")
                ->where("status = 'PAID'")
                ->where("(tanggal_transaksi between '$date_agustus_awal' and '$date_agustus_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_agustus = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_agustus_awal' and '$date_agustus_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_jurnal_agustus = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_agustus_awal' and '$date_agustus_akhir')")
                ->get()->row_array();

                $overhead_15_agustus = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_agustus_awal' and '$date_agustus_akhir')")
                ->get()->row_array();

                $overhead_jurnal_15_agustus = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_agustus_awal' and '$date_agustus_akhir')")
                ->get()->row_array();

                $diskonto_agustus = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun = 168")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_agustus_awal' and '$date_agustus_akhir')")
                ->get()->row_array();   

                $cost_agustus = $akumulasi_agustus['total_nilai_keluar'] + $nilai_alat_agustus['nilai'] + $akumulasi_bbm_agustus['total_nilai_keluar_2'] + $insentif_tm_agustus['total'] + $biaya_alat_lainnya_agustus['total'] + $biaya_alat_lainnya_jurnal_agustus['total'] + $overhead_15_agustus['total'] + $overhead_jurnal_15_agustus['total'] + $diskonto_agustus['total'];
                $laba_rugi_agustus = $penjualan_agustus['total'] - $cost_agustus;
                $presentase_laba_rugi_agustus = ($penjualan_agustus['total']!=0)?($laba_rugi_agustus / $penjualan_agustus['total'])  * 100:0;
                $presentase_laba_rugi_agustus_fix = round($presentase_laba_rugi_agustus,2);

                //SEPTEMBER	
                $akumulasi_september = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar) as total_nilai_keluar')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_september_awal' and '$date_september_akhir')")
                ->get()->row_array();

                $nilai_alat_september = $this->db->select('SUM(prm.display_price) as nilai')
                ->from('pmm_receipt_material prm')
                ->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
                ->join('produk p', 'prm.material_id = p.id','left')
                ->where("prm.date_receipt between '$date_september_awal' and '$date_september_akhir'")
                ->where("p.kategori_produk = '5'")
                ->where("po.status in ('PUBLISH','CLOSED')")
                ->get()->row_array();

                $akumulasi_bbm_september = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar_2) as total_nilai_keluar_2')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_september_awal' and '$date_september_akhir')")
                ->get()->row_array();

                $insentif_tm_september = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun = 220")
                ->where("status = 'PAID'")
                ->where("(tanggal_transaksi between '$date_september_awal' and '$date_september_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_september = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_september_awal' and '$date_september_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_jurnal_september = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_september_awal' and '$date_september_akhir')")
                ->get()->row_array();

                $overhead_15_september = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_september_awal' and '$date_september_akhir')")
                ->get()->row_array();

                $overhead_jurnal_15_september = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_september_awal' and '$date_september_akhir')")
                ->get()->row_array();

                $diskonto_september = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun = 168")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_september_awal' and '$date_september_akhir')")
                ->get()->row_array();   

                $cost_september = $akumulasi_september['total_nilai_keluar'] + $nilai_alat_september['nilai'] + $akumulasi_bbm_september['total_nilai_keluar_2'] + $insentif_tm_september['total'] + $biaya_alat_lainnya_september['total'] + $biaya_alat_lainnya_jurnal_september['total'] + $overhead_15_september['total'] + $overhead_jurnal_15_september['total'] + $diskonto_september['total'];
                $laba_rugi_september = $penjualan_september['total'] - $cost_september;
                $presentase_laba_rugi_september = ($penjualan_september['total']!=0)?($laba_rugi_september / $penjualan_september['total'])  * 100:0;
                $presentase_laba_rugi_september_fix = round($presentase_laba_rugi_september,2);

                //OKTOBER	
                $akumulasi_oktober = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar) as total_nilai_keluar')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_oktober_awal' and '$date_oktober_akhir')")
                ->get()->row_array();

                $nilai_alat_oktober = $this->db->select('SUM(prm.display_price) as nilai')
                ->from('pmm_receipt_material prm')
                ->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
                ->join('produk p', 'prm.material_id = p.id','left')
                ->where("prm.date_receipt between '$date_oktober_awal' and '$date_oktober_akhir'")
                ->where("p.kategori_produk = '5'")
                ->where("po.status in ('PUBLISH','CLOSED')")
                ->get()->row_array();

                $akumulasi_bbm_oktober = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar_2) as total_nilai_keluar_2')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_oktober_awal' and '$date_oktober_akhir')")
                ->get()->row_array();

                $insentif_tm_oktober = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun = 220")
                ->where("status = 'PAID'")
                ->where("(tanggal_transaksi between '$date_oktober_awal' and '$date_oktober_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_oktober = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_oktober_awal' and '$date_oktober_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_jurnal_oktober = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_oktober_awal' and '$date_oktober_akhir')")
                ->get()->row_array();

                $overhead_15_oktober = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_oktober_awal' and '$date_oktober_akhir')")
                ->get()->row_array();

                $overhead_jurnal_15_oktober = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_oktober_awal' and '$date_oktober_akhir')")
                ->get()->row_array();

                $diskonto_oktober = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun = 168")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_oktober_awal' and '$date_oktober_akhir')")
                ->get()->row_array();   

                $cost_oktober = $akumulasi_oktober['total_nilai_keluar'] + $nilai_alat_oktober['nilai'] + $akumulasi_bbm_oktober['total_nilai_keluar_2'] + $insentif_tm_oktober['total'] + $biaya_alat_lainnya_oktober['total'] + $biaya_alat_lainnya_jurnal_oktober['total'] + $overhead_15_oktober['total'] + $overhead_jurnal_15_oktober['total'] + $diskonto_oktober['total'];
                $laba_rugi_oktober = $penjualan_oktober['total'] - $cost_oktober;
                $presentase_laba_rugi_oktober = ($penjualan_oktober['total']!=0)?($laba_rugi_oktober / $penjualan_oktober['total'])  * 100:0;
                $presentase_laba_rugi_oktober_fix = round($presentase_laba_rugi_oktober,2);

                //NOVEMBER	
                $akumulasi_november = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar) as total_nilai_keluar')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_november_awal' and '$date_november_akhir')")
                ->get()->row_array();

                $nilai_alat_november = $this->db->select('SUM(prm.display_price) as nilai')
                ->from('pmm_receipt_material prm')
                ->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
                ->join('produk p', 'prm.material_id = p.id','left')
                ->where("prm.date_receipt between '$date_november_awal' and '$date_november_akhir'")
                ->where("p.kategori_produk = '5'")
                ->where("po.status in ('PUBLISH','CLOSED')")
                ->get()->row_array();

                $akumulasi_bbm_november = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar_2) as total_nilai_keluar_2')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_november_awal' and '$date_november_akhir')")
                ->get()->row_array();

                $insentif_tm_november = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun = 220")
                ->where("status = 'PAID'")
                ->where("(tanggal_transaksi between '$date_november_awal' and '$date_november_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_november = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_november_awal' and '$date_november_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_jurnal_november = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_november_awal' and '$date_november_akhir')")
                ->get()->row_array();

                $overhead_15_november = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_november_awal' and '$date_november_akhir')")
                ->get()->row_array();

                $overhead_jurnal_15_november = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_november_awal' and '$date_november_akhir')")
                ->get()->row_array();

                $diskonto_november = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun = 168")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_november_awal' and '$date_november_akhir')")
                ->get()->row_array();   

                $cost_november = $akumulasi_november['total_nilai_keluar'] + $nilai_alat_november['nilai'] + $akumulasi_bbm_november['total_nilai_keluar_2'] + $insentif_tm_november['total'] + $biaya_alat_lainnya_november['total'] + $biaya_alat_lainnya_jurnal_november['total'] + $overhead_15_november['total'] + $overhead_jurnal_15_november['total'] + $diskonto_november['total'];
                $laba_rugi_november = $penjualan_november['total'] - $cost_november;
                $presentase_laba_rugi_november = ($penjualan_november['total']!=0)?($laba_rugi_november / $penjualan_november['total'])  * 100:0;
                $presentase_laba_rugi_november_fix = round($presentase_laba_rugi_november,2);

                //DESEMBER	
                $akumulasi_desember = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar) as total_nilai_keluar')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_desember_awal' and '$date_desember_akhir')")
                ->get()->row_array();

                $nilai_alat_desember = $this->db->select('SUM(prm.display_price) as nilai')
                ->from('pmm_receipt_material prm')
                ->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
                ->join('produk p', 'prm.material_id = p.id','left')
                ->where("prm.date_receipt between '$date_desember_awal' and '$date_desember_akhir'")
                ->where("p.kategori_produk = '5'")
                ->where("po.status in ('PUBLISH','CLOSED')")
                ->get()->row_array();

                $akumulasi_bbm_desember = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar_2) as total_nilai_keluar_2')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_desember_awal' and '$date_desember_akhir')")
                ->get()->row_array();

                $insentif_tm_desember = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun = 220")
                ->where("status = 'PAID'")
                ->where("(tanggal_transaksi between '$date_desember_awal' and '$date_desember_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_desember = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_desember_awal' and '$date_desember_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_jurnal_desember = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_desember_awal' and '$date_desember_akhir')")
                ->get()->row_array();

                $overhead_15_desember = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_desember_awal' and '$date_desember_akhir')")
                ->get()->row_array();

                $overhead_jurnal_15_desember = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_desember_awal' and '$date_desember_akhir')")
                ->get()->row_array();

                $diskonto_desember = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun = 168")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_desember_awal' and '$date_desember_akhir')")
                ->get()->row_array();   

                $cost_desember = $akumulasi_desember['total_nilai_keluar'] + $nilai_alat_desember['nilai'] + $akumulasi_bbm_desember['total_nilai_keluar_2'] + $insentif_tm_desember['total'] + $biaya_alat_lainnya_desember['total'] + $biaya_alat_lainnya_jurnal_desember['total'] + $overhead_15_desember['total'] + $overhead_jurnal_15_desember['total'] + $diskonto_desember['total'];
                $laba_rugi_desember = $penjualan_desember['total'] - $cost_desember;
                $presentase_laba_rugi_desember = ($penjualan_desember['total']!=0)?($laba_rugi_desember / $penjualan_desember['total'])  * 100:0;
                $presentase_laba_rugi_desember_fix = round($presentase_laba_rugi_desember,2);

                //JANUARI23	
                $akumulasi_januari23 = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar) as total_nilai_keluar')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_januari23_awal' and '$date_januari23_akhir')")
                ->get()->row_array();

                $nilai_alat_januari23 = $this->db->select('SUM(prm.display_price) as nilai')
                ->from('pmm_receipt_material prm')
                ->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
                ->join('produk p', 'prm.material_id = p.id','left')
                ->where("prm.date_receipt between '$date_januari23_awal' and '$date_januari23_akhir'")
                ->where("p.kategori_produk = '5'")
                ->where("po.status in ('PUBLISH','CLOSED')")
                ->get()->row_array();

                $akumulasi_bbm_januari23 = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar_2) as total_nilai_keluar_2')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_januari23_awal' and '$date_januari23_akhir')")
                ->get()->row_array();

                $insentif_tm_januari23 = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun = 220")
                ->where("status = 'PAID'")
                ->where("(tanggal_transaksi between '$date_januari23_awal' and '$date_januari23_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_januari23 = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_januari23_awal' and '$date_januari23_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_jurnal_januari23 = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_januari23_awal' and '$date_januari23_akhir')")
                ->get()->row_array();

                $overhead_15_januari23 = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_januari23_awal' and '$date_januari23_akhir')")
                ->get()->row_array();

                $overhead_jurnal_15_januari23 = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_januari23_awal' and '$date_januari23_akhir')")
                ->get()->row_array();

                $diskonto_januari23 = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun = 168")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_januari23_awal' and '$date_januari23_akhir')")
                ->get()->row_array();   

                $cost_januari23 = $akumulasi_januari23['total_nilai_keluar'] + $nilai_alat_januari23['nilai'] + $akumulasi_bbm_januari23['total_nilai_keluar_2'] + $insentif_tm_januari23['total'] + $biaya_alat_lainnya_januari23['total'] + $biaya_alat_lainnya_jurnal_januari23['total'] + $overhead_15_januari23['total'] + $overhead_jurnal_15_januari23['total'] + $diskonto_januari23['total'];
                $laba_rugi_januari23 = $penjualan_januari23['total'] - $cost_januari23;
                $presentase_laba_rugi_januari23 = ($penjualan_januari23['total']!=0)?($laba_rugi_januari23 / $penjualan_januari23['total'])  * 100:0;
                $presentase_laba_rugi_januari23_fix = round($presentase_laba_rugi_januari23,2);

                //FEBRUARI23	
                $akumulasi_februari23 = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar) as total_nilai_keluar')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_februari23_awal' and '$date_februari23_akhir')")
                ->get()->row_array();

                $nilai_alat_februari23 = $this->db->select('SUM(prm.display_price) as nilai')
                ->from('pmm_receipt_material prm')
                ->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
                ->join('produk p', 'prm.material_id = p.id','left')
                ->where("prm.date_receipt between '$date_februari23_awal' and '$date_februari23_akhir'")
                ->where("p.kategori_produk = '5'")
                ->where("po.status in ('PUBLISH','CLOSED')")
                ->get()->row_array();

                $akumulasi_bbm_februari23 = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar_2) as total_nilai_keluar_2')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_februari23_awal' and '$date_februari23_akhir')")
                ->get()->row_array();

                $insentif_tm_februari23 = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun = 220")
                ->where("status = 'PAID'")
                ->where("(tanggal_transaksi between '$date_februari23_awal' and '$date_februari23_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_februari23 = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_februari23_awal' and '$date_februari23_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_jurnal_februari23 = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_februari23_awal' and '$date_februari23_akhir')")
                ->get()->row_array();

                $overhead_15_februari23 = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_februari23_awal' and '$date_februari23_akhir')")
                ->get()->row_array();

                $overhead_jurnal_15_februari23 = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_februari23_awal' and '$date_februari23_akhir')")
                ->get()->row_array();

                $diskonto_februari23 = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun = 168")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_februari23_awal' and '$date_februari23_akhir')")
                ->get()->row_array();   

                $cost_februari23 = $akumulasi_februari23['total_nilai_keluar'] + $nilai_alat_februari23['nilai'] + $akumulasi_bbm_februari23['total_nilai_keluar_2'] + $insentif_tm_februari23['total'] + $biaya_alat_lainnya_februari23['total'] + $biaya_alat_lainnya_jurnal_februari23['total'] + $overhead_15_februari23['total'] + $overhead_jurnal_15_februari23['total'] + $diskonto_februari23['total'];
                $laba_rugi_februari23 = $penjualan_februari23['total'] - $cost_februari23;
                $presentase_laba_rugi_februari23 = ($penjualan_februari23['total']!=0)?($laba_rugi_februari23 / $penjualan_februari23['total'])  * 100:0;
                $presentase_laba_rugi_februari23_fix = round($presentase_laba_rugi_februari23,2);

                //MARET23	
                $akumulasi_maret23 = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar) as total_nilai_keluar')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_maret23_awal' and '$date_maret23_akhir')")
                ->get()->row_array();

                $nilai_alat_maret23 = $this->db->select('SUM(prm.display_price) as nilai')
                ->from('pmm_receipt_material prm')
                ->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
                ->join('produk p', 'prm.material_id = p.id','left')
                ->where("prm.date_receipt between '$date_maret23_awal' and '$date_maret23_akhir'")
                ->where("p.kategori_produk = '5'")
                ->where("po.status in ('PUBLISH','CLOSED')")
                ->get()->row_array();

                $akumulasi_bbm_maret23 = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar_2) as total_nilai_keluar_2')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_maret23_awal' and '$date_maret23_akhir')")
                ->get()->row_array();

                $insentif_tm_maret23 = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun = 220")
                ->where("status = 'PAID'")
                ->where("(tanggal_transaksi between '$date_maret23_awal' and '$date_maret23_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_maret23 = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_maret23_awal' and '$date_maret23_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_jurnal_maret23 = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_maret23_awal' and '$date_maret23_akhir')")
                ->get()->row_array();

                $overhead_15_maret23 = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_maret23_awal' and '$date_maret23_akhir')")
                ->get()->row_array();

                $overhead_jurnal_15_maret23 = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_maret23_awal' and '$date_maret23_akhir')")
                ->get()->row_array();

                $diskonto_maret23 = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun = 168")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_maret23_awal' and '$date_maret23_akhir')")
                ->get()->row_array();   

                $cost_maret23 = $akumulasi_maret23['total_nilai_keluar'] + $nilai_alat_maret23['nilai'] + $akumulasi_bbm_maret23['total_nilai_keluar_2'] + $insentif_tm_maret23['total'] + $biaya_alat_lainnya_maret23['total'] + $biaya_alat_lainnya_jurnal_maret23['total'] + $overhead_15_maret23['total'] + $overhead_jurnal_15_maret23['total'] + $diskonto_maret23['total'];
                $laba_rugi_maret23 = $penjualan_maret23['total'] - $cost_maret23;
                $presentase_laba_rugi_maret23 = ($penjualan_maret23['total']!=0)?($laba_rugi_maret23 / $penjualan_maret23['total'])  * 100:0;
                $presentase_laba_rugi_maret23_fix = round($presentase_laba_rugi_maret23,2);

                //APRIL23	
                $akumulasi_april23 = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar) as total_nilai_keluar')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_april23_awal' and '$date_april23_akhir')")
                ->get()->row_array();

                $nilai_alat_april23 = $this->db->select('SUM(prm.display_price) as nilai')
                ->from('pmm_receipt_material prm')
                ->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
                ->join('produk p', 'prm.material_id = p.id','left')
                ->where("prm.date_receipt between '$date_april23_awal' and '$date_april23_akhir'")
                ->where("p.kategori_produk = '5'")
                ->where("po.status in ('PUBLISH','CLOSED')")
                ->get()->row_array();

                $akumulasi_bbm_april23 = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar_2) as total_nilai_keluar_2')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_april23_awal' and '$date_april23_akhir')")
                ->get()->row_array();

                $insentif_tm_april23 = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun = 220")
                ->where("status = 'PAID'")
                ->where("(tanggal_transaksi between '$date_april23_awal' and '$date_april23_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_april23 = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_april23_awal' and '$date_april23_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_jurnal_april23 = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_april23_awal' and '$date_april23_akhir')")
                ->get()->row_array();

                $overhead_15_april23 = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_april23_awal' and '$date_april23_akhir')")
                ->get()->row_array();

                $overhead_jurnal_15_april23 = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_april23_awal' and '$date_april23_akhir')")
                ->get()->row_array();

                $diskonto_april23 = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun = 168")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_april23_awal' and '$date_april23_akhir')")
                ->get()->row_array();   

                $cost_april23 = $akumulasi_april23['total_nilai_keluar'] + $nilai_alat_april23['nilai'] + $akumulasi_bbm_april23['total_nilai_keluar_2'] + $insentif_tm_april23['total'] + $biaya_alat_lainnya_april23['total'] + $biaya_alat_lainnya_jurnal_april23['total'] + $overhead_15_april23['total'] + $overhead_jurnal_15_april23['total'] + $diskonto_april23['total'];
                $laba_rugi_april23 = $penjualan_april23['total'] - $cost_april23;
                $presentase_laba_rugi_april23 = ($penjualan_april23['total']!=0)?($laba_rugi_april23 / $penjualan_april23['total'])  * 100:0;
                $presentase_laba_rugi_april23_fix = round($presentase_laba_rugi_april23,2);

                //MEI23	
                $akumulasi_mei23 = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar) as total_nilai_keluar')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_mei23_awal' and '$date_mei23_akhir')")
                ->get()->row_array();

                $nilai_alat_mei23 = $this->db->select('SUM(prm.display_price) as nilai')
                ->from('pmm_receipt_material prm')
                ->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
                ->join('produk p', 'prm.material_id = p.id','left')
                ->where("prm.date_receipt between '$date_mei23_awal' and '$date_mei23_akhir'")
                ->where("p.kategori_produk = '5'")
                ->where("po.status in ('PUBLISH','CLOSED')")
                ->get()->row_array();

                $akumulasi_bbm_mei23 = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar_2) as total_nilai_keluar_2')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_mei23_awal' and '$date_mei23_akhir')")
                ->get()->row_array();

                $insentif_tm_mei23 = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun = 220")
                ->where("status = 'PAID'")
                ->where("(tanggal_transaksi between '$date_mei23_awal' and '$date_mei23_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_mei23 = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_mei23_awal' and '$date_mei23_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_jurnal_mei23 = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_mei23_awal' and '$date_mei23_akhir')")
                ->get()->row_array();

                $overhead_15_mei23 = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_mei23_awal' and '$date_mei23_akhir')")
                ->get()->row_array();

                $overhead_jurnal_15_mei23 = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_mei23_awal' and '$date_mei23_akhir')")
                ->get()->row_array();

                $diskonto_mei23 = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun = 168")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_mei23_awal' and '$date_mei23_akhir')")
                ->get()->row_array();   

                $cost_mei23 = $akumulasi_mei23['total_nilai_keluar'] + $nilai_alat_mei23['nilai'] + $akumulasi_bbm_mei23['total_nilai_keluar_2'] + $insentif_tm_mei23['total'] + $biaya_alat_lainnya_mei23['total'] + $biaya_alat_lainnya_jurnal_mei23['total'] + $overhead_15_mei23['total'] + $overhead_jurnal_15_mei23['total'] + $diskonto_mei23['total'];
                $laba_rugi_mei23 = $penjualan_mei23['total'] - $cost_mei23;
                $presentase_laba_rugi_mei23 = ($penjualan_mei23['total']!=0)?($laba_rugi_mei23 / $penjualan_mei23['total'])  * 100:0;
                $presentase_laba_rugi_mei23_fix = round($presentase_laba_rugi_mei23,2);

                //JUNI23	
                $akumulasi_juni23 = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar) as total_nilai_keluar')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_juni23_awal' and '$date_juni23_akhir')")
                ->get()->row_array();

                $nilai_alat_juni23 = $this->db->select('SUM(prm.display_price) as nilai')
                ->from('pmm_receipt_material prm')
                ->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
                ->join('produk p', 'prm.material_id = p.id','left')
                ->where("prm.date_receipt between '$date_juni23_awal' and '$date_juni23_akhir'")
                ->where("p.kategori_produk = '5'")
                ->where("po.status in ('PUBLISH','CLOSED')")
                ->get()->row_array();

                $akumulasi_bbm_juni23 = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar_2) as total_nilai_keluar_2')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_juni23_awal' and '$date_juni23_akhir')")
                ->get()->row_array();

                $insentif_tm_juni23 = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun = 220")
                ->where("status = 'PAID'")
                ->where("(tanggal_transaksi between '$date_juni23_awal' and '$date_juni23_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_juni23 = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_juni23_awal' and '$date_juni23_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_jurnal_juni23 = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_juni23_awal' and '$date_juni23_akhir')")
                ->get()->row_array();

                $overhead_15_juni23 = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_juni23_awal' and '$date_juni23_akhir')")
                ->get()->row_array();

                $overhead_jurnal_15_juni23 = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_juni23_awal' and '$date_juni23_akhir')")
                ->get()->row_array();

                $diskonto_juni23 = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun = 168")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_juni23_awal' and '$date_juni23_akhir')")
                ->get()->row_array();   

                $cost_juni23 = $akumulasi_juni23['total_nilai_keluar'] + $nilai_alat_juni23['nilai'] + $akumulasi_bbm_juni23['total_nilai_keluar_2'] + $insentif_tm_juni23['total'] + $biaya_alat_lainnya_juni23['total'] + $biaya_alat_lainnya_jurnal_juni23['total'] + $overhead_15_juni23['total'] + $overhead_jurnal_15_juni23['total'] + $diskonto_juni23['total'];
                $laba_rugi_juni23 = $penjualan_juni23['total'] - $cost_juni23;
                $presentase_laba_rugi_juni23 = ($penjualan_juni23['total']!=0)?($laba_rugi_juni23 / $penjualan_juni23['total'])  * 100:0;
                $presentase_laba_rugi_juni23_fix = round($presentase_laba_rugi_juni23,2);

                //JULI23	
                $akumulasi_juli23 = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar) as total_nilai_keluar')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_juli23_awal' and '$date_juli23_akhir')")
                ->get()->row_array();

                $nilai_alat_juli23 = $this->db->select('SUM(prm.display_price) as nilai')
                ->from('pmm_receipt_material prm')
                ->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
                ->join('produk p', 'prm.material_id = p.id','left')
                ->where("prm.date_receipt between '$date_juli23_awal' and '$date_juli23_akhir'")
                ->where("p.kategori_produk = '5'")
                ->where("po.status in ('PUBLISH','CLOSED')")
                ->get()->row_array();

                $akumulasi_bbm_juli23 = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar_2) as total_nilai_keluar_2')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_juli23_awal' and '$date_juli23_akhir')")
                ->get()->row_array();

                $insentif_tm_juli23 = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun = 220")
                ->where("status = 'PAID'")
                ->where("(tanggal_transaksi between '$date_juli23_awal' and '$date_juli23_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_juli23 = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_juli23_awal' and '$date_juli23_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_jurnal_juli23 = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_juli23_awal' and '$date_juli23_akhir')")
                ->get()->row_array();

                $overhead_15_juli23 = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_juli23_awal' and '$date_juli23_akhir')")
                ->get()->row_array();

                $overhead_jurnal_15_juli23 = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("c.id <> 505 ") //Biaya oli
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_juli23_awal' and '$date_juli23_akhir')")
                ->get()->row_array();

                $diskonto_juli23 = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun = 168")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_juli23_awal' and '$date_juli23_akhir')")
                ->get()->row_array();   

                $cost_juli23 = $akumulasi_juli23['total_nilai_keluar'] + $nilai_alat_juli23['nilai'] + $akumulasi_bbm_juli23['total_nilai_keluar_2'] + $insentif_tm_juli23['total'] + $biaya_alat_lainnya_juli23['total'] + $biaya_alat_lainnya_jurnal_juli23['total'] + $overhead_15_juli23['total'] + $overhead_jurnal_15_juli23['total'] + $diskonto_juli23['total'];
                $laba_rugi_juli23 = $penjualan_juli23['total'] - $cost_juli23;
                $presentase_laba_rugi_juli23 = ($penjualan_juli23['total']!=0)?($laba_rugi_juli23 / $penjualan_juli23['total'])  * 100:0;
                $presentase_laba_rugi_juli23_fix = round($presentase_laba_rugi_juli23,2);

                //AKUMULASI	
                $akumulasi_akumulasi = $this->db->select('pp.date_akumulasi, sum(pp.total_nilai_keluar) as total_nilai_keluar')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_akumulasi_awal' and '$date_akumulasi_akhir')")
                ->get()->row_array();

                $nilai_alat_akumulasi = $this->db->select('SUM(prm.display_price) as nilai')
                ->from('pmm_receipt_material prm')
                ->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
                ->join('produk p', 'prm.material_id = p.id','left')
                ->where("prm.date_receipt between '$date_akumulasi_awal' and '$date_akumulasi_akhir'")
                ->where("p.kategori_produk = '5'")
                ->where("po.status in ('PUBLISH','CLOSED')")
                ->get()->row_array();

                $akumulasi_bbm_akumulasi = $this->db->select('pp.date_akumulasi, sum(pp.total_nilai_keluar_2) as total_nilai_keluar_2')
                ->from('akumulasi pp')
                ->where("(pp.date_akumulasi between '$date_akumulasi_awal' and '$date_akumulasi_akhir')")
                ->get()->row_array();

                $insentif_tm_akumulasi = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun = 220")
                ->where("status = 'PAID'")
                ->where("(tanggal_transaksi between '$date_akumulasi_awal' and '$date_akumulasi_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_akumulasi = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_akumulasi_awal' and '$date_akumulasi_akhir')")
                ->get()->row_array();

                $biaya_alat_lainnya_jurnal_akumulasi = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->where("pdb.akun in ('219','505')")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_akumulasi_awal' and '$date_akumulasi_akhir')")
                ->get()->row_array();

                $overhead_15_akumulasi = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_akumulasi_awal' and '$date_akumulasi_akhir')")
                ->get()->row_array();

                $overhead_jurnal_15_akumulasi = $this->db->select('sum(pdb.debit) as total')
                ->from('pmm_jurnal_umum pb ')
                ->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where('c.coa_category',15)
                ->where("c.id <> 168 ") //biaya diskonto bank
                ->where("c.id <> 219 ") //biaya alat batching plant 
                ->where("c.id <> 220 ") //biaya alat truck mixer
                ->where("c.id <> 228 ") //biaya persiapan
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_akumulasi_awal' and '$date_akumulasi_akhir')")
                ->get()->row_array();

                $diskonto_akumulasi = $this->db->select('sum(pdb.jumlah) as total')
                ->from('pmm_biaya pb ')
                ->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
                ->join('pmm_coa c','pdb.akun = c.id','left')
                ->where("pdb.akun = 168")
                ->where("pb.status = 'PAID'")
                ->where("(pb.tanggal_transaksi between '$date_akumulasi_awal' and '$date_akumulasi_akhir')")
                ->get()->row_array();   

                $cost_akumulasi = $akumulasi_akumulasi['total_nilai_keluar'] + $nilai_alat_akumulasi['nilai'] + $akumulasi_bbm_akumulasi['total_nilai_keluar_2'] + $insentif_tm_akumulasi['total'] + $overhead_15_akumulasi['total'] + $overhead_jurnal_15_akumulasi['total'] + $biaya_alat_lainnya_akumulasi['total'] + $biaya_alat_lainnya_jurnal_akumulasi['total'] + $diskonto_akumulasi['total'];
                $laba_rugi_akumulasi = $penjualan_akumulasi['total'] - $cost_akumulasi;
                $presentase_laba_rugi_akumulasi = ($penjualan_akumulasi['total']!=0)?($laba_rugi_akumulasi / $penjualan_akumulasi['total'])  * 100:0;
                $presentase_laba_rugi_akumulasi_fix = round($presentase_laba_rugi_akumulasi,2);

                ?>
                <div class="col-sm-12">
                    <figure class="highcharts-figure">
                        <div id="container_laba_rugi"></div>
                        
                    </figure>
                    <br />
                </div>
                <!--<div class="col-sm-10">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Laba Rugi</h3>
                        </div>
                        <div style="margin: 20px">
                            <div class="row">
                                <div class="col-sm-4">
                                    <input type="text" name="" id="filter_lost_profit" class="form-control dtpicker" placeholder="Filter">
                                </div>
                            </div>
                            <br />
                            <div id="wait" style=" text-align: center; align-content: center; display: none;">	
                                <div>Please Wait</div>
                                    <div class="fa-3x">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </div>
                            </div>		
                            <div id="parent-lost-profit" class="chart-container">
                                <canvas id="canvas"></canvas>
                            </div>
                        </div>
                    </div>
                </div>-->

                <!-- Laporan Evaluasi -->               
                <div role="tabpanel" class="tab-pane" id="laporan_evaluasi">
                    <div class="col-sm-8">
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
                            [0, 'rgb(159,197,232)'],
                            [1, 'rgb(41,134,204)']
                            //[1, 'rgb(255,255,255)']
                        ]
                    },
                },
                title: {
                    style: {
                        color: '#ffffff',
                        fontWeight: 'bold',
                        fontSize: '14px',
                        fontFamily: 'monospace'
                    },
                    text: 'REALISASI PRODUKSI',
                    x: -20 //center            
                },
                subtitle: {
                    style: {
                        color: '#ffffff',
                        fontWeight: 'bold',
                        fontSize: '14px',
                        fontFamily: 'monospace'
                    },
                    text: 'PT. BIA BUMI JAYENDRA - TEMEF',
                    x: -20
                },
                xAxis: { //X axis menampilkan data bulan
                    labels: {
                        style: {
                            color: '#ffffff'
                        }
                    },
                    categories: ['Feb 22','Mar 22','Apr 22','Mei 22','Jun 22','Jul 22','Agu 22','Sep 22','Okt 22','Nov 22','Des 22','Jan 23','Feb 23','Mar 23','Apr 23','Mei 23','Jun 23','Jul 23','Sisa']
                },
                yAxis: {
                    //title: {  //label yAxis
                        //text: 'RAP <br /><?php echo number_format($total_kontrak_all,0,',','.'); ?>'
                        //text: 'Presentase'
                    //},
                    title: {
                        style: {
                            color: '#ffffff',
                            fontWeight: 'bold',
                            fontSize: '14px',
                            fontFamily: 'monospace'
                        },
                        text: 'Presentase'           
                    },
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080' //warna dari grafik line
                    }],
                    labels: {
                        style: {
                            color: '#ffffff'
                        },
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
                    name: 'Rencana %',  
                    
                    data: [<?php echo json_encode($net_februari_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_maret_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_april_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_mei_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_juni_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_juli_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_agustus_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_september_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_oktober_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_november_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_desember_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_januari23_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_februari23_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_maret23_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_april23_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_mei23_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_juni23_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_juli23_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_sisa_rap, JSON_NUMERIC_CHECK); ?>],

                    color: '#000000',
                },
                {  
                    name: 'Realisasi %',  
                    
                    data: [<?php echo json_encode($net_februari, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_maret, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_april, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_mei, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_juni, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_juli, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_agustus, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_september, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_oktober, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_november, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_desember, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_januari23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_februari23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_maret23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_april23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_mei23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_juni23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_juli23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_sisa, JSON_NUMERIC_CHECK); ?>],

                    color: '#FF0000',

                    zones: [{
                        value: [<?php echo json_encode($net_juli23, JSON_NUMERIC_CHECK); ?>],
                    }, {
                        dashStyle: 'dot'
                    }]
                }
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

<script type="text/javascript">
	//script untuk membuat grafik, perhatikan setiap komentar agar paham
    $(function () {
        var chart;
        $(document).ready(function() {
            chart = new Highcharts.Chart({
                chart: {
                    renderTo: 'container_laba_rugi', //letakan grafik di div id container
                    //Type grafik, anda bisa ganti menjadi area,bar,column dan bar
                    type: 'line',
                    marginRight: 130,
                    marginBottom: 75,
                    backgroundColor: {
                        linearGradient: [0, 0, 500, 500],
                        stops: [
                            [0, 'rgb(210,230,165)'],
                            [1, 'rgb(128,185,0)']
                            //[1, 'rgb(255,255,255)']
                        ]
                    },
                },
                title: {
                    style: {
                        color: '#ffffff',
                        fontWeight: 'bold',
                        fontSize: '14px',
                        fontFamily: 'monospace'
                    },
                    text: 'LABA RUGI',
                    x: -20 //center            
                },
                subtitle: {
                    style: {
                        color: '#ffffff',
                        fontWeight: 'bold',
                        fontSize: '14px',
                        fontFamily: 'monospace'
                    },
                    text: 'PT. BIA BUMI JAYENDRA - TEMEF',
                    x: -20
                },
                xAxis: { //X axis menampilkan data bulan
                    labels: {
                        style: {
                            color: '#ffffff'
                        }
                    },
                    categories: ['Feb 22','Mar 22','Apr 22','Mei 22','Jun 22','Jul 22','Agu 22','Sep 22','Okt 22','Nov 22','Des 22','Jan 23','Feb 23','Mar 23','Apr 23','Mei 23','Jun 23','Jul 23','Akumulasi']
                },
                yAxis: {
                    //title: {  //label yAxis
                        //text: 'RAP <br /><?php echo number_format($total_kontrak_all,0,',','.'); ?>'
                        //text: 'Presentase'
                    //},
                    title: {
                        style: {
                            color: '#ffffff',
                            fontWeight: 'bold',
                            fontSize: '14px',
                            fontFamily: 'monospace'
                        },
                        text: 'Presentase'           
                    },
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080' //warna dari grafik line
                    }],
                    labels: {
                        style: {
                            color: '#ffffff'
                        },
                        format: '{value} %'
                    },
                    min: -50,
                    max: 50,
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
                    name: 'Target %',  
                    
                    data: [2.96,2.96,2.96,2.96,2.96,2.96,2.96,2.96,2.96,2.96,2.96,2.96,2.96,2.96,2.96,2.96,2.96,2.96,4.98],

                    color: '#000000',
                },
                {  
                    name: 'Laba Rugi %',  
                    
                    data: [ <?php echo json_encode($presentase_laba_rugi_februari_fix, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($presentase_laba_rugi_maret_fix, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($presentase_laba_rugi_april_fix, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($presentase_laba_rugi_mei_fix, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($presentase_laba_rugi_juni_fix, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($presentase_laba_rugi_juli_fix, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($presentase_laba_rugi_agustus_fix, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($presentase_laba_rugi_september_fix, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($presentase_laba_rugi_oktober_fix, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($presentase_laba_rugi_november_fix, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($presentase_laba_rugi_desember_fix, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($presentase_laba_rugi_januari23_fix, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($presentase_laba_rugi_februari23_fix, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($presentase_laba_rugi_maret23_fix, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($presentase_laba_rugi_april23_fix, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($presentase_laba_rugi_mei23_fix, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($presentase_laba_rugi_juni23_fix, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($presentase_laba_rugi_juli23_fix, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($presentase_laba_rugi_akumulasi_fix, JSON_NUMERIC_CHECK); ?>],

                    color: '#FF0000',

                    zones: [{
                        
                    }, {
                        dashStyle: 'dot'
                    }]
                }
                ]
            });
        });
        
    });
</script>

</body>
</html>
