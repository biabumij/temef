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
    $date_januari_akhir = date('2022-01-31');
    $date_januari23_awal = date('2023-01-01');
    $date_januari23_akhir = date('2023-01-31');

    $kontrak = $this->db->select('SUM(pod.total) as total')
    ->from('pmm_sales_po ppo')
    ->join('pmm_sales_po_detail pod', 'ppo.id = pod.sales_po_id','left')
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("ppo.id")
    ->order_by('ppo.contract_date','desc')->limit(1)
    ->get()->row_array();

    $total_kontrak_all = $kontrak['total'];

    $penjualan_januari = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
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
    $net_januari_volume = round($penjualan_januari['volume'],2);

    $date_februari_awal = date('2022-02-01');
    $date_februari_akhir = date('2022-02-28');
    $penjualan_februari = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
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
    $net_februari_volume = round($penjualan_februari['volume'],2);

    $date_maret_awal = date('2022-03-01');
    $date_maret_akhir = date('2022-03-31');
    $penjualan_maret = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
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
    $net_maret_volume = round($penjualan_maret['volume'],2);

    $date_april_awal = date('2022-04-01');
    $date_april_akhir = date('2022-04-30');
    $penjualan_april = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
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
    $net_april_volume = round($penjualan_april['volume'],2);

    $date_mei_awal = date('2022-05-01');
    $date_mei_akhir = date('2022-05-31');
    $penjualan_mei = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
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
    $net_mei_volume = round($penjualan_mei['volume'],2);

    $date_juni_awal = date('2022-06-01');
    $date_juni_akhir = date('2022-06-30');
    $penjualan_juni = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
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
    $net_juni_volume = round($penjualan_juni['volume'],2);

    $date_juli_awal = date('2022-07-01');
    $date_juli_akhir = date('2022-07-31');
    $penjualan_juli = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
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
    $net_juli_volume = round($penjualan_juli['volume'],2);

    $date_agustus_awal = date('2022-08-01');
    $date_agustus_akhir = date('2022-08-31');
    $penjualan_agustus = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
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
    $net_agustus_volume = round($penjualan_agustus['volume'],2);

    $date_september_awal = date('2022-09-01');
    $date_september_akhir = date('2022-09-30');
    $penjualan_september = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
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
    $net_september_volume = round($penjualan_september['volume'],2);

    $date_oktober_awal = date('2022-10-01');
    $date_oktober_akhir = date('2022-10-31');
    $penjualan_oktober = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
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
    $net_oktober_volume = round($penjualan_oktober['volume'],2);

    $date_november_awal = date('2022-11-01');
    $date_november_akhir = date('2022-11-30');
    $penjualan_november = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_januari_awal' and '$date_november_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_november = $penjualan_november['total'];
    $presentase_penjualan_november = ($total_penjualan_november / $total_kontrak_all) * 100;
    $net_november = round($presentase_penjualan_november,2);
    $net_november_volume = round($penjualan_november['volume'],2);

    $date_desember_awal = date('2022-12-01');
    $date_desember_akhir = date('2022-12-31');
    $penjualan_desember = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_januari_awal' and '$date_desember_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_desember = $penjualan_desember['total'];
    $presentase_penjualan_desember = ($total_penjualan_desember / $total_kontrak_all) * 100;
    $net_desember = round($presentase_penjualan_desember,2);
    $net_desember_volume = round($penjualan_desember['volume'],2);

    $penjualan_januari23 = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_januari_awal' and '$date_januari23_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();
    
    $total_penjualan_januari23 = $penjualan_januari23['total'];
    $presentase_penjualan_januari23 = ($total_penjualan_januari23 / $total_kontrak_all) * 100;
    $net_januari23 = round($presentase_penjualan_januari23,2);
    $net_januari23_volume = round($penjualan_januari23['volume'],2);

    $date_februari23_awal = date('2023-02-01');
    $date_februari23_akhir = date('2023-02-28');
    $penjualan_februari23 = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_januari_awal' and '$date_februari23_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_februari23 = $penjualan_februari23['total'];
    $presentase_penjualan_februari23 = ($total_penjualan_februari23 / $total_kontrak_all) * 100;
    $net_februari23 = round($presentase_penjualan_februari23,2);
    $net_februari23_volume = round($penjualan_februari23['volume'],2);

    $date_maret23_awal = date('2023-03-01');
    $date_maret23_akhir = date('2023-03-31');
    $penjualan_maret23 = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_januari_awal' and '$date_maret23_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_maret23 = $penjualan_maret23['total'];
    $presentase_penjualan_maret23 = ($total_penjualan_maret23 / $total_kontrak_all) * 100;
    $net_maret23 = round($presentase_penjualan_maret23,2);
    $net_maret23_volume = round($penjualan_maret23['volume'],2);

    $date_april23_awal = date('2023-04-01');
    $date_april23_akhir = date('2023-04-30');
    $penjualan_april23 = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_januari_awal' and '$date_april23_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_april23 = $penjualan_april23['total'];
    $presentase_penjualan_april23 = ($total_penjualan_april23 / $total_kontrak_all) * 100;
    $net_april23 = round($presentase_penjualan_april23,2);
    $net_april23_volume = round($penjualan_april23['volume'],2);

    $date_mei23_awal = date('2023-05-01');
    $date_mei23_akhir = date('2023-05-31');
    $penjualan_mei23 = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_januari_awal' and '$date_mei23_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_mei23 = $penjualan_mei23['total'];
    $presentase_penjualan_mei23 = ($total_penjualan_mei23 / $total_kontrak_all) * 100;
    $net_mei23 = round($presentase_penjualan_mei23,2);
    $net_mei23_volume = round($penjualan_mei23['volume'],2);

    $date_juni23_awal = date('2023-06-01');
    $date_juni23_akhir = date('2023-06-30');
    $penjualan_juni23 = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_januari_awal' and '$date_juni23_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_juni23 = $penjualan_juni23['total'];
    $presentase_penjualan_juni23 = ($total_penjualan_juni23 / $total_kontrak_all) * 100;
    $net_juni23 = round($presentase_penjualan_juni23,2);
    $net_juni23_volume = round($penjualan_juni23['volume'],2);

    $date_juli23_awal = date('2023-07-01');
    $date_juli23_akhir = date('2023-07-31');
    $penjualan_juli23 = $this->db->select('SUM(pp.display_price) as total, SUM(pp.display_volume) as volume')
    ->from('pmm_productions pp')
    ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
    ->where("pp.date_production between '$date_januari_awal' and '$date_juli23_akhir'")
    ->where("pp.status = 'PUBLISH'")
    ->where("ppo.status in ('OPEN','CLOSED')")
    ->group_by("pp.client_id")
    ->get()->row_array();

    $total_penjualan_juli23 = $penjualan_juli23['total'];
    $presentase_penjualan_juli23 = ($total_penjualan_juli23 / $total_kontrak_all) * 100;
    $net_juli23 = round($presentase_penjualan_juli23,2);
    $net_juli23_volume = round($penjualan_juli23['volume'],2);
    ?>

    <?php
    $harga_jual_125_now = $this->db->select('pod.price as harga_satuan')
    ->from('pmm_sales_po po')
    ->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
    ->where("(po.contract_date < '$date_now')")
    ->where("pod.product_id = 2")
    ->order_by('po.contract_date','desc')->limit(1)
    ->get()->row_array();

    $harga_jual_225_now = $this->db->select('pod.price as harga_satuan')
    ->from('pmm_sales_po po')
    ->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
    ->where("(po.contract_date < '$date_now')")
    ->where("pod.product_id = 1")
    ->order_by('po.contract_date','desc')->limit(1)
    ->get()->row_array();

    $harga_jual_250_now = $this->db->select('pod.price as harga_satuan')
    ->from('pmm_sales_po po')
    ->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
    ->where("(po.contract_date < '$date_now')")
    ->where("pod.product_id = 3")
    ->order_by('po.contract_date','desc')->limit(1)
    ->get()->row_array();

    $harga_jual_250_18_now = $this->db->select('pod.price as harga_satuan')
    ->from('pmm_sales_po po')
    ->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
    ->where("(po.contract_date < '$date_now')")
    ->where("pod.product_id = 11")
    ->order_by('po.contract_date','desc')->limit(1)
    ->get()->row_array();

    //JANUARI
    $rencana_kerja_januari = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_januari_akhir'")
    ->get()->row_array();

    $volume_januari_produk_a = $rencana_kerja_januari['vol_produk_a'];
    $volume_januari_produk_b = $rencana_kerja_januari['vol_produk_b'];
    $volume_januari_produk_c = $rencana_kerja_januari['vol_produk_c'];
    $volume_januari_produk_d = $rencana_kerja_januari['vol_produk_d'];

    $total_januari_volume = $volume_januari_produk_a + $volume_januari_produk_b + $volume_januari_produk_c + $volume_januari_produk_d;
		
    $nilai_jual_125_januari = $volume_januari_produk_a * $harga_jual_125_now['harga_satuan'];
    $nilai_jual_225_januari = $volume_januari_produk_b * $harga_jual_225_now['harga_satuan'];
    $nilai_jual_250_januari = $volume_januari_produk_c * $harga_jual_250_now['harga_satuan'];
    $nilai_jual_250_18_januari = $volume_januari_produk_d * $harga_jual_250_18_now['harga_satuan'];
    $nilai_jual_all_januari = $nilai_jual_125_januari + $nilai_jual_225_januari + $nilai_jual_250_januari + $nilai_jual_250_18_januari;
    
    $total_januari_nilai = ($nilai_jual_all_januari / $total_kontrak_all) * 100;;
    $net_januari_rap = round($total_januari_nilai,2);

    //FEBRUARI
    $rencana_kerja_februari = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_februari_akhir'")
    ->get()->row_array();

    $volume_februari_produk_a = $rencana_kerja_februari['vol_produk_a'];
    $volume_februari_produk_b = $rencana_kerja_februari['vol_produk_b'];
    $volume_februari_produk_c = $rencana_kerja_februari['vol_produk_c'];
    $volume_februari_produk_d = $rencana_kerja_februari['vol_produk_d'];

    $total_februari_volume = $volume_februari_produk_a + $volume_februari_produk_b + $volume_februari_produk_c + $volume_februari_produk_d;
		
    $nilai_jual_125_februari = $volume_februari_produk_a * $harga_jual_125_now['harga_satuan'];
    $nilai_jual_225_februari = $volume_februari_produk_b * $harga_jual_225_now['harga_satuan'];
    $nilai_jual_250_februari = $volume_februari_produk_c * $harga_jual_250_now['harga_satuan'];
    $nilai_jual_250_18_februari = $volume_februari_produk_d * $harga_jual_250_18_now['harga_satuan'];
    $nilai_jual_all_februari = $nilai_jual_125_februari + $nilai_jual_225_februari + $nilai_jual_250_februari + $nilai_jual_250_18_februari;
    
    $total_februari_nilai = ($nilai_jual_all_februari / $total_kontrak_all) * 100;;
    $net_februari_rap = round($total_februari_nilai,2);

    //MARET
    $rencana_kerja_maret = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_maret_akhir'")
    ->get()->row_array();

    $volume_maret_produk_a = $rencana_kerja_maret['vol_produk_a'];
    $volume_maret_produk_b = $rencana_kerja_maret['vol_produk_b'];
    $volume_maret_produk_c = $rencana_kerja_maret['vol_produk_c'];
    $volume_maret_produk_d = $rencana_kerja_maret['vol_produk_d'];

    $total_maret_volume = $volume_maret_produk_a + $volume_maret_produk_b + $volume_maret_produk_c + $volume_maret_produk_d;
		
    $nilai_jual_125_maret = $volume_maret_produk_a * $harga_jual_125_now['harga_satuan'];
    $nilai_jual_225_maret = $volume_maret_produk_b * $harga_jual_225_now['harga_satuan'];
    $nilai_jual_250_maret = $volume_maret_produk_c * $harga_jual_250_now['harga_satuan'];
    $nilai_jual_250_18_maret = $volume_maret_produk_d * $harga_jual_250_18_now['harga_satuan'];
    $nilai_jual_all_maret = $nilai_jual_125_maret + $nilai_jual_225_maret + $nilai_jual_250_maret + $nilai_jual_250_18_maret;
    
    $total_maret_nilai = ($nilai_jual_all_maret / $total_kontrak_all) * 100;;
    $net_maret_rap = round($total_maret_nilai,2);

    //APRIL
    $rencana_kerja_april = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_april_akhir'")
    ->get()->row_array();

    $volume_april_produk_a = $rencana_kerja_april['vol_produk_a'];
    $volume_april_produk_b = $rencana_kerja_april['vol_produk_b'];
    $volume_april_produk_c = $rencana_kerja_april['vol_produk_c'];
    $volume_april_produk_d = $rencana_kerja_april['vol_produk_d'];

    $total_april_volume = $volume_april_produk_a + $volume_april_produk_b + $volume_april_produk_c + $volume_april_produk_d;
		
    $nilai_jual_125_april = $volume_april_produk_a * $harga_jual_125_now['harga_satuan'];
    $nilai_jual_225_april = $volume_april_produk_b * $harga_jual_225_now['harga_satuan'];
    $nilai_jual_250_april = $volume_april_produk_c * $harga_jual_250_now['harga_satuan'];
    $nilai_jual_250_18_april = $volume_april_produk_d * $harga_jual_250_18_now['harga_satuan'];
    $nilai_jual_all_april = $nilai_jual_125_april + $nilai_jual_225_april + $nilai_jual_250_april + $nilai_jual_250_18_april;
    
    $total_april_nilai = ($nilai_jual_all_april / $total_kontrak_all) * 100;;
    $net_april_rap = round($total_april_nilai,2);

    //MEI
    $rencana_kerja_mei = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_mei_akhir'")
    ->get()->row_array();

    $volume_mei_produk_a = $rencana_kerja_mei['vol_produk_a'];
    $volume_mei_produk_b = $rencana_kerja_mei['vol_produk_b'];
    $volume_mei_produk_c = $rencana_kerja_mei['vol_produk_c'];
    $volume_mei_produk_d = $rencana_kerja_mei['vol_produk_d'];

    $total_mei_volume = $volume_mei_produk_a + $volume_mei_produk_b + $volume_mei_produk_c + $volume_mei_produk_d;
		
    $nilai_jual_125_mei = $volume_mei_produk_a * $harga_jual_125_now['harga_satuan'];
    $nilai_jual_225_mei = $volume_mei_produk_b * $harga_jual_225_now['harga_satuan'];
    $nilai_jual_250_mei = $volume_mei_produk_c * $harga_jual_250_now['harga_satuan'];
    $nilai_jual_250_18_mei = $volume_mei_produk_d * $harga_jual_250_18_now['harga_satuan'];
    $nilai_jual_all_mei = $nilai_jual_125_mei + $nilai_jual_225_mei + $nilai_jual_250_mei + $nilai_jual_250_18_mei;
    
    $total_mei_nilai = ($nilai_jual_all_mei / $total_kontrak_all) * 100;;
    $net_mei_rap = round($total_mei_nilai,2);

    //JUNI
    $rencana_kerja_juni = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_juni_akhir'")
    ->get()->row_array();

    $volume_juni_produk_a = $rencana_kerja_juni['vol_produk_a'];
    $volume_juni_produk_b = $rencana_kerja_juni['vol_produk_b'];
    $volume_juni_produk_c = $rencana_kerja_juni['vol_produk_c'];
    $volume_juni_produk_d = $rencana_kerja_juni['vol_produk_d'];

    $total_juni_volume = $volume_juni_produk_a + $volume_juni_produk_b + $volume_juni_produk_c + $volume_juni_produk_d;
		
    $nilai_jual_125_juni = $volume_juni_produk_a * $harga_jual_125_now['harga_satuan'];
    $nilai_jual_225_juni = $volume_juni_produk_b * $harga_jual_225_now['harga_satuan'];
    $nilai_jual_250_juni = $volume_juni_produk_c * $harga_jual_250_now['harga_satuan'];
    $nilai_jual_250_18_juni = $volume_juni_produk_d * $harga_jual_250_18_now['harga_satuan'];
    $nilai_jual_all_juni = $nilai_jual_125_juni + $nilai_jual_225_juni + $nilai_jual_250_juni + $nilai_jual_250_18_juni;
    
    $total_juni_nilai = ($nilai_jual_all_juni / $total_kontrak_all) * 100;;
    $net_juni_rap = round($total_juni_nilai,2);

    //JULI
    $rencana_kerja_juli = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_juli_akhir'")
    ->get()->row_array();

    $volume_juli_produk_a = $rencana_kerja_juli['vol_produk_a'];
    $volume_juli_produk_b = $rencana_kerja_juli['vol_produk_b'];
    $volume_juli_produk_c = $rencana_kerja_juli['vol_produk_c'];
    $volume_juli_produk_d = $rencana_kerja_juli['vol_produk_d'];

    $total_juli_volume = $volume_juli_produk_a + $volume_juli_produk_b + $volume_juli_produk_c + $volume_juli_produk_d;
		
    $nilai_jual_125_juli = $volume_juli_produk_a * $harga_jual_125_now['harga_satuan'];
    $nilai_jual_225_juli = $volume_juli_produk_b * $harga_jual_225_now['harga_satuan'];
    $nilai_jual_250_juli = $volume_juli_produk_c * $harga_jual_250_now['harga_satuan'];
    $nilai_jual_250_18_juli = $volume_juli_produk_d * $harga_jual_250_18_now['harga_satuan'];
    $nilai_jual_all_juli = $nilai_jual_125_juli + $nilai_jual_225_juli + $nilai_jual_250_juli + $nilai_jual_250_18_juli;
    
    $total_juli_nilai = ($nilai_jual_all_juli / $total_kontrak_all) * 100;;
    $net_juli_rap = round($total_juli_nilai,2);

    //AGUSTUS
    $rencana_kerja_agustus = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_agustus_akhir'")
    ->get()->row_array();

    $volume_agustus_produk_a = $rencana_kerja_agustus['vol_produk_a'];
    $volume_agustus_produk_b = $rencana_kerja_agustus['vol_produk_b'];
    $volume_agustus_produk_c = $rencana_kerja_agustus['vol_produk_c'];
    $volume_agustus_produk_d = $rencana_kerja_agustus['vol_produk_d'];

    $total_agustus_volume = $volume_agustus_produk_a + $volume_agustus_produk_b + $volume_agustus_produk_c + $volume_agustus_produk_d;
		
    $nilai_jual_125_agustus = $volume_agustus_produk_a * $harga_jual_125_now['harga_satuan'];
    $nilai_jual_225_agustus = $volume_agustus_produk_b * $harga_jual_225_now['harga_satuan'];
    $nilai_jual_250_agustus = $volume_agustus_produk_c * $harga_jual_250_now['harga_satuan'];
    $nilai_jual_250_18_agustus = $volume_agustus_produk_d * $harga_jual_250_18_now['harga_satuan'];
    $nilai_jual_all_agustus = $nilai_jual_125_agustus + $nilai_jual_225_agustus + $nilai_jual_250_agustus + $nilai_jual_250_18_agustus;
    
    $total_agustus_nilai = ($nilai_jual_all_agustus / $total_kontrak_all) * 100;;
    $net_agustus_rap = round($total_agustus_nilai,2);

    //SEPTEMBER
    $rencana_kerja_september = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_september_akhir'")
    ->get()->row_array();

    $volume_september_produk_a = $rencana_kerja_september['vol_produk_a'];
    $volume_september_produk_b = $rencana_kerja_september['vol_produk_b'];
    $volume_september_produk_c = $rencana_kerja_september['vol_produk_c'];
    $volume_september_produk_d = $rencana_kerja_september['vol_produk_d'];

    $total_september_volume = $volume_september_produk_a + $volume_september_produk_b + $volume_september_produk_c + $volume_september_produk_d;
		
    $nilai_jual_125_september = $volume_september_produk_a * $harga_jual_125_now['harga_satuan'];
    $nilai_jual_225_september = $volume_september_produk_b * $harga_jual_225_now['harga_satuan'];
    $nilai_jual_250_september = $volume_september_produk_c * $harga_jual_250_now['harga_satuan'];
    $nilai_jual_250_18_september = $volume_september_produk_d * $harga_jual_250_18_now['harga_satuan'];
    $nilai_jual_all_september = $nilai_jual_125_september + $nilai_jual_225_september + $nilai_jual_250_september + $nilai_jual_250_18_september;
    
    $total_september_nilai = ($nilai_jual_all_september / $total_kontrak_all) * 100;;
    $net_september_rap = round($total_september_nilai,2);

    //OKTOBER
    $rencana_kerja_oktober = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_oktober_akhir'")
    ->get()->row_array();

    $volume_oktober_produk_a = $rencana_kerja_oktober['vol_produk_a'];
    $volume_oktober_produk_b = $rencana_kerja_oktober['vol_produk_b'];
    $volume_oktober_produk_c = $rencana_kerja_oktober['vol_produk_c'];
    $volume_oktober_produk_d = $rencana_kerja_oktober['vol_produk_d'];

    $total_oktober_volume = $volume_oktober_produk_a + $volume_oktober_produk_b + $volume_oktober_produk_c + $volume_oktober_produk_d;
		
    $nilai_jual_125_oktober = $volume_oktober_produk_a * $harga_jual_125_now['harga_satuan'];
    $nilai_jual_225_oktober = $volume_oktober_produk_b * $harga_jual_225_now['harga_satuan'];
    $nilai_jual_250_oktober = $volume_oktober_produk_c * $harga_jual_250_now['harga_satuan'];
    $nilai_jual_250_18_oktober = $volume_oktober_produk_d * $harga_jual_250_18_now['harga_satuan'];
    $nilai_jual_all_oktober = $nilai_jual_125_oktober + $nilai_jual_225_oktober + $nilai_jual_250_oktober + $nilai_jual_250_18_oktober;
    
    $total_oktober_nilai = ($nilai_jual_all_oktober / $total_kontrak_all) * 100;;
    $net_oktober_rap = round($total_oktober_nilai,2);

    //NOVEMBER
    $rencana_kerja_november = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_november_akhir'")
    ->get()->row_array();

    $volume_november_produk_a = $rencana_kerja_november['vol_produk_a'];
    $volume_november_produk_b = $rencana_kerja_november['vol_produk_b'];
    $volume_november_produk_c = $rencana_kerja_november['vol_produk_c'];
    $volume_november_produk_d = $rencana_kerja_november['vol_produk_d'];

    $total_november_volume = $volume_november_produk_a + $volume_november_produk_b + $volume_november_produk_c + $volume_november_produk_d;
		
    $nilai_jual_125_november = $volume_november_produk_a * $harga_jual_125_now['harga_satuan'];
    $nilai_jual_225_november = $volume_november_produk_b * $harga_jual_225_now['harga_satuan'];
    $nilai_jual_250_november = $volume_november_produk_c * $harga_jual_250_now['harga_satuan'];
    $nilai_jual_250_18_november = $volume_november_produk_d * $harga_jual_250_18_now['harga_satuan'];
    $nilai_jual_all_november = $nilai_jual_125_november + $nilai_jual_225_november + $nilai_jual_250_november + $nilai_jual_250_18_november;
    
    $total_november_nilai = ($nilai_jual_all_november / $total_kontrak_all) * 100;;
    $net_november_rap = round($total_november_nilai,2);

    //DESEMBER
    $rencana_kerja_desember = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_desember_akhir'")
    ->get()->row_array();

    $volume_desember_produk_a = $rencana_kerja_desember['vol_produk_a'];
    $volume_desember_produk_b = $rencana_kerja_desember['vol_produk_b'];
    $volume_desember_produk_c = $rencana_kerja_desember['vol_produk_c'];
    $volume_desember_produk_d = $rencana_kerja_desember['vol_produk_d'];

    $total_desember_volume = $volume_desember_produk_a + $volume_desember_produk_b + $volume_desember_produk_c + $volume_desember_produk_d;
		
    $nilai_jual_125_desember = $volume_desember_produk_a * $harga_jual_125_now['harga_satuan'];
    $nilai_jual_225_desember = $volume_desember_produk_b * $harga_jual_225_now['harga_satuan'];
    $nilai_jual_250_desember = $volume_desember_produk_c * $harga_jual_250_now['harga_satuan'];
    $nilai_jual_250_18_desember = $volume_desember_produk_d * $harga_jual_250_18_now['harga_satuan'];
    $nilai_jual_all_desember = $nilai_jual_125_desember + $nilai_jual_225_desember + $nilai_jual_250_desember + $nilai_jual_250_18_desember;
    
    $total_desember_nilai = ($nilai_jual_all_desember / $total_kontrak_all) * 100;;
    $net_desember_rap = round($total_desember_nilai,2);

    //JANUARI 2023
    $rencana_kerja_januari23 = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_januari23_akhir'")
    ->get()->row_array();

    $volume_januari23_produk_a = $rencana_kerja_januari23['vol_produk_a'];
    $volume_januari23_produk_b = $rencana_kerja_januari23['vol_produk_b'];
    $volume_januari23_produk_c = $rencana_kerja_januari23['vol_produk_c'];
    $volume_januari23_produk_d = $rencana_kerja_januari23['vol_produk_d'];

    $total_januari23_volume = $volume_januari23_produk_a + $volume_januari23_produk_b + $volume_januari23_produk_c + $volume_januari23_produk_d;
		
    $nilai_jual_125_januari23 = $volume_januari23_produk_a * $harga_jual_125_now['harga_satuan'];
    $nilai_jual_225_januari23 = $volume_januari23_produk_b * $harga_jual_225_now['harga_satuan'];
    $nilai_jual_250_januari23 = $volume_januari23_produk_c * $harga_jual_250_now['harga_satuan'];
    $nilai_jual_250_18_januari23 = $volume_januari23_produk_d * $harga_jual_250_18_now['harga_satuan'];
    $nilai_jual_all_januari23 = $nilai_jual_125_januari23 + $nilai_jual_225_januari23 + $nilai_jual_250_januari23 + $nilai_jual_250_18_januari23;
    
    $total_januari23_nilai = ($nilai_jual_all_januari23 / $total_kontrak_all) * 100;;
    $net_januari23_rap = round($total_januari23_nilai,2);

    //FEBRUARI 2023
    $rencana_kerja_februari23 = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_februari23_akhir'")
    ->get()->row_array();

    $volume_februari23_produk_a = $rencana_kerja_februari23['vol_produk_a'];
    $volume_februari23_produk_b = $rencana_kerja_februari23['vol_produk_b'];
    $volume_februari23_produk_c = $rencana_kerja_februari23['vol_produk_c'];
    $volume_februari23_produk_d = $rencana_kerja_februari23['vol_produk_d'];

    $total_februari23_volume = $volume_februari23_produk_a + $volume_februari23_produk_b + $volume_februari23_produk_c + $volume_februari23_produk_d;
		
    $nilai_jual_125_februari23 = $volume_februari23_produk_a * $harga_jual_125_now['harga_satuan'];
    $nilai_jual_225_februari23 = $volume_februari23_produk_b * $harga_jual_225_now['harga_satuan'];
    $nilai_jual_250_februari23 = $volume_februari23_produk_c * $harga_jual_250_now['harga_satuan'];
    $nilai_jual_250_18_februari23 = $volume_februari23_produk_d * $harga_jual_250_18_now['harga_satuan'];
    $nilai_jual_all_februari23 = $nilai_jual_125_februari23 + $nilai_jual_225_februari23 + $nilai_jual_250_februari23 + $nilai_jual_250_18_februari23;
    
    $total_februari23_nilai = ($nilai_jual_all_februari23 / $total_kontrak_all) * 100;;
    $net_februari23_rap = round($total_februari23_nilai,2);

    //MARET 2023
    $rencana_kerja_maret23 = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_maret23_akhir'")
    ->get()->row_array();

    $volume_maret23_produk_a = $rencana_kerja_maret23['vol_produk_a'];
    $volume_maret23_produk_b = $rencana_kerja_maret23['vol_produk_b'];
    $volume_maret23_produk_c = $rencana_kerja_maret23['vol_produk_c'];
    $volume_maret23_produk_d = $rencana_kerja_maret23['vol_produk_d'];

    $total_maret23_volume = $volume_maret23_produk_a + $volume_maret23_produk_b + $volume_maret23_produk_c + $volume_maret23_produk_d;
		
    $nilai_jual_125_maret23 = $volume_maret23_produk_a * $harga_jual_125_now['harga_satuan'];
    $nilai_jual_225_maret23 = $volume_maret23_produk_b * $harga_jual_225_now['harga_satuan'];
    $nilai_jual_250_maret23 = $volume_maret23_produk_c * $harga_jual_250_now['harga_satuan'];
    $nilai_jual_250_18_maret23 = $volume_maret23_produk_d * $harga_jual_250_18_now['harga_satuan'];
    $nilai_jual_all_maret23 = $nilai_jual_125_maret23 + $nilai_jual_225_maret23 + $nilai_jual_250_maret23 + $nilai_jual_250_18_maret23;
    
    $total_maret23_nilai = ($nilai_jual_all_maret23 / $total_kontrak_all) * 100;;
    $net_maret23_rap = round($total_maret23_nilai,2);

    //APRIL 2023
    $rencana_kerja_april23 = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_april23_akhir'")
    ->get()->row_array();

    $volume_april23_produk_a = $rencana_kerja_april23['vol_produk_a'];
    $volume_april23_produk_b = $rencana_kerja_april23['vol_produk_b'];
    $volume_april23_produk_c = $rencana_kerja_april23['vol_produk_c'];
    $volume_april23_produk_d = $rencana_kerja_april23['vol_produk_d'];

    $total_april23_volume = $volume_april23_produk_a + $volume_april23_produk_b + $volume_april23_produk_c + $volume_april23_produk_d;
		
    $nilai_jual_125_april23 = $volume_april23_produk_a * $harga_jual_125_now['harga_satuan'];
    $nilai_jual_225_april23 = $volume_april23_produk_b * $harga_jual_225_now['harga_satuan'];
    $nilai_jual_250_april23 = $volume_april23_produk_c * $harga_jual_250_now['harga_satuan'];
    $nilai_jual_250_18_april23 = $volume_april23_produk_d * $harga_jual_250_18_now['harga_satuan'];
    $nilai_jual_all_april23 = $nilai_jual_125_april23 + $nilai_jual_225_april23 + $nilai_jual_250_april23 + $nilai_jual_250_18_april23;
    
    $total_april23_nilai = ($nilai_jual_all_april23 / $total_kontrak_all) * 100;;
    $net_april23_rap = round($total_april23_nilai,2);

    //MEI 2023
    $rencana_kerja_mei23 = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_mei23_akhir'")
    ->get()->row_array();

    $volume_mei23_produk_a = $rencana_kerja_mei23['vol_produk_a'];
    $volume_mei23_produk_b = $rencana_kerja_mei23['vol_produk_b'];
    $volume_mei23_produk_c = $rencana_kerja_mei23['vol_produk_c'];
    $volume_mei23_produk_d = $rencana_kerja_mei23['vol_produk_d'];

    $total_mei23_volume = $volume_mei23_produk_a + $volume_mei23_produk_b + $volume_mei23_produk_c + $volume_mei23_produk_d;
		
    $nilai_jual_125_mei23 = $volume_mei23_produk_a * $harga_jual_125_now['harga_satuan'];
    $nilai_jual_225_mei23 = $volume_mei23_produk_b * $harga_jual_225_now['harga_satuan'];
    $nilai_jual_250_mei23 = $volume_mei23_produk_c * $harga_jual_250_now['harga_satuan'];
    $nilai_jual_250_18_mei23 = $volume_mei23_produk_d * $harga_jual_250_18_now['harga_satuan'];
    $nilai_jual_all_mei23 = $nilai_jual_125_mei23 + $nilai_jual_225_mei23 + $nilai_jual_250_mei23 + $nilai_jual_250_18_mei23;
    
    $total_mei23_nilai = ($nilai_jual_all_mei23 / $total_kontrak_all) * 100;;
    $net_mei23_rap = round($total_mei23_nilai,2);

    //JUNI 2023
    $rencana_kerja_juni23 = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_juni23_akhir'")
    ->get()->row_array();

    $volume_juni23_produk_a = $rencana_kerja_juni23['vol_produk_a'];
    $volume_juni23_produk_b = $rencana_kerja_juni23['vol_produk_b'];
    $volume_juni23_produk_c = $rencana_kerja_juni23['vol_produk_c'];
    $volume_juni23_produk_d = $rencana_kerja_juni23['vol_produk_d'];

    $total_juni23_volume = $volume_juni23_produk_a + $volume_juni23_produk_b + $volume_juni23_produk_c + $volume_juni23_produk_d;
		
    $nilai_jual_125_juni23 = $volume_juni23_produk_a * $harga_jual_125_now['harga_satuan'];
    $nilai_jual_225_juni23 = $volume_juni23_produk_b * $harga_jual_225_now['harga_satuan'];
    $nilai_jual_250_juni23 = $volume_juni23_produk_c * $harga_jual_250_now['harga_satuan'];
    $nilai_jual_250_18_juni23 = $volume_juni23_produk_d * $harga_jual_250_18_now['harga_satuan'];
    $nilai_jual_all_juni23 = $nilai_jual_125_juni23 + $nilai_jual_225_juni23 + $nilai_jual_250_juni23 + $nilai_jual_250_18_juni23;
    
    $total_juni23_nilai = ($nilai_jual_all_juni23 / $total_kontrak_all) * 100;;
    $net_juni23_rap = round($total_juni23_nilai,2);

    //JULI 2023
    $rencana_kerja_juli23 = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
    ->from('rak r')
    ->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_juli23_akhir'")
    ->get()->row_array();

    $volume_juli23_produk_a = $rencana_kerja_juli23['vol_produk_a'];
    $volume_juli23_produk_b = $rencana_kerja_juli23['vol_produk_b'];
    $volume_juli23_produk_c = $rencana_kerja_juli23['vol_produk_c'];
    $volume_juli23_produk_d = $rencana_kerja_juli23['vol_produk_d'];

    $total_juli23_volume = $volume_juli23_produk_a + $volume_juli23_produk_b + $volume_juli23_produk_c + $volume_juli23_produk_d;
		
    $nilai_jual_125_juli23 = $volume_juli23_produk_a * $harga_jual_125_now['harga_satuan'];
    $nilai_jual_225_juli23 = $volume_juli23_produk_b * $harga_jual_225_now['harga_satuan'];
    $nilai_jual_250_juli23 = $volume_juli23_produk_c * $harga_jual_250_now['harga_satuan'];
    $nilai_jual_250_18_juli23 = $volume_juli23_produk_d * $harga_jual_250_18_now['harga_satuan'];
    $nilai_jual_all_juli23 = $nilai_jual_125_juli23 + $nilai_jual_225_juli23 + $nilai_jual_250_juli23 + $nilai_jual_250_18_juli23;
    
    $total_juli23_nilai = ($nilai_jual_all_juli23 / $total_kontrak_all) * 100;;
    $net_juli23_rap = round($total_juli23_nilai,2);
    ?>
    <?php
    $selisih_januari = round($net_januari_rap - $net_januari,2);
    $selisih_februari = round($net_februari_rap,2);
    $selisih_maret = round($net_maret_rap - $net_maret,2);
    $selisih_april = round($net_april_rap - $net_april,2);
    $selisih_mei = round($net_mei_rap - $net_mei,2);
    $selisih_juni = round($net_juni_rap - $net_juni,2);
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
                <div class="col-sm-16">
                    <figure class="highcharts-figure">
                        <div id="container"></div>
                        <font size="1">
                            <?php
                            $pen_januari = $this->db->select('SUM(pp.display_volume) as volume')
                            ->from('pmm_productions pp')
                            ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
                            ->where("pp.date_production between '$date_januari_awal' and '$date_januari_akhir'")
                            ->where("pp.status = 'PUBLISH'")
                            ->where("ppo.status in ('OPEN','CLOSED')")
                            ->group_by("pp.client_id")
                            ->get()->row_array();
                            
                            $total_pen_januari = $pen_januari['volume'];

                            $pen_februari = $this->db->select('SUM(pp.display_volume) as volume')
                            ->from('pmm_productions pp')
                            ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
                            ->where("pp.date_production between '$date_februari_awal' and '$date_februari_akhir'")
                            ->where("pp.status = 'PUBLISH'")
                            ->where("ppo.status in ('OPEN','CLOSED')")
                            ->group_by("pp.client_id")
                            ->get()->row_array();
                            
                            $total_pen_februari = $pen_februari['volume'];

                            $pen_maret = $this->db->select('SUM(pp.display_volume) as volume')
                            ->from('pmm_productions pp')
                            ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
                            ->where("pp.date_production between '$date_maret_awal' and '$date_maret_akhir'")
                            ->where("pp.status = 'PUBLISH'")
                            ->where("ppo.status in ('OPEN','CLOSED')")
                            ->group_by("pp.client_id")
                            ->get()->row_array();
                            
                            $total_pen_maret = $pen_maret['volume'];

                            $pen_april = $this->db->select('SUM(pp.display_volume) as volume')
                            ->from('pmm_productions pp')
                            ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
                            ->where("pp.date_production between '$date_april_awal' and '$date_april_akhir'")
                            ->where("pp.status = 'PUBLISH'")
                            ->where("ppo.status in ('OPEN','CLOSED')")
                            ->group_by("pp.client_id")
                            ->get()->row_array();
                            
                            $total_pen_april = $pen_april['volume'];

                            $pen_mei = $this->db->select('SUM(pp.display_volume) as volume')
                            ->from('pmm_productions pp')
                            ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
                            ->where("pp.date_production between '$date_mei_awal' and '$date_mei_akhir'")
                            ->where("pp.status = 'PUBLISH'")
                            ->where("ppo.status in ('OPEN','CLOSED')")
                            ->group_by("pp.client_id")
                            ->get()->row_array();
                            
                            $total_pen_mei = $pen_mei['volume'];

                            $pen_juni = $this->db->select('SUM(pp.display_volume) as volume')
                            ->from('pmm_productions pp')
                            ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
                            ->where("pp.date_production between '$date_juni_awal' and '$date_juni_akhir'")
                            ->where("pp.status = 'PUBLISH'")
                            ->where("ppo.status in ('OPEN','CLOSED')")
                            ->group_by("pp.client_id")
                            ->get()->row_array();
                            
                            $total_pen_juni = $pen_juni['volume'];

                            $pen_juli = $this->db->select('SUM(pp.display_volume) as volume')
                            ->from('pmm_productions pp')
                            ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
                            ->where("pp.date_production between '$date_juli_awal' and '$date_juli_akhir'")
                            ->where("pp.status = 'PUBLISH'")
                            ->where("ppo.status in ('OPEN','CLOSED')")
                            ->group_by("pp.client_id")
                            ->get()->row_array();
                            
                            $total_pen_juli = $pen_juli['volume'];

                            $pen_agustus = $this->db->select('SUM(pp.display_volume) as volume')
                            ->from('pmm_productions pp')
                            ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
                            ->where("pp.date_production between '$date_agustus_awal' and '$date_agustus_akhir'")
                            ->where("pp.status = 'PUBLISH'")
                            ->where("ppo.status in ('OPEN','CLOSED')")
                            ->group_by("pp.client_id")
                            ->get()->row_array();
                            
                            $total_pen_agustus = $pen_agustus['volume'];

                            $pen_september = $this->db->select('SUM(pp.display_volume) as volume')
                            ->from('pmm_productions pp')
                            ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
                            ->where("pp.date_production between '$date_september_awal' and '$date_september_akhir'")
                            ->where("pp.status = 'PUBLISH'")
                            ->where("ppo.status in ('OPEN','CLOSED')")
                            ->group_by("pp.client_id")
                            ->get()->row_array();
                            
                            $total_pen_september = $pen_september['volume'];

                            $pen_oktober = $this->db->select('SUM(pp.display_volume) as volume')
                            ->from('pmm_productions pp')
                            ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
                            ->where("pp.date_production between '$date_oktober_awal' and '$date_oktober_akhir'")
                            ->where("pp.status = 'PUBLISH'")
                            ->where("ppo.status in ('OPEN','CLOSED')")
                            ->group_by("pp.client_id")
                            ->get()->row_array();
                            
                            $total_pen_oktober = $pen_oktober['volume'];

                            $pen_november = $this->db->select('SUM(pp.display_volume) as volume')
                            ->from('pmm_productions pp')
                            ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
                            ->where("pp.date_production between '$date_november_awal' and '$date_november_akhir'")
                            ->where("pp.status = 'PUBLISH'")
                            ->where("ppo.status in ('OPEN','CLOSED')")
                            ->group_by("pp.client_id")
                            ->get()->row_array();
                            
                            $total_pen_november = $pen_november['volume'];

                            $pen_desember = $this->db->select('SUM(pp.display_volume) as volume')
                            ->from('pmm_productions pp')
                            ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
                            ->where("pp.date_production between '$date_desember_awal' and '$date_desember_akhir'")
                            ->where("pp.status = 'PUBLISH'")
                            ->where("ppo.status in ('OPEN','CLOSED')")
                            ->group_by("pp.client_id")
                            ->get()->row_array();
                            
                            $total_pen_desember = $pen_desember['volume'];

                            $pen_januari23 = $this->db->select('SUM(pp.display_volume) as volume')
                            ->from('pmm_productions pp')
                            ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
                            ->where("pp.date_production between '$date_januari23_awal' and '$date_januari23_akhir'")
                            ->where("pp.status = 'PUBLISH'")
                            ->where("ppo.status in ('OPEN','CLOSED')")
                            ->group_by("pp.client_id")
                            ->get()->row_array();
                            
                            $total_pen_januari23 = $pen_januari23['volume'];

                            $pen_februari23 = $this->db->select('SUM(pp.display_volume) as volume')
                            ->from('pmm_productions pp')
                            ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
                            ->where("pp.date_production between '$date_februari23_awal' and '$date_februari23_akhir'")
                            ->where("pp.status = 'PUBLISH'")
                            ->where("ppo.status in ('OPEN','CLOSED')")
                            ->group_by("pp.client_id")
                            ->get()->row_array();
                            
                            $total_pen_februari23 = $pen_februari23['volume'];

                            $pen_maret23 = $this->db->select('SUM(pp.display_volume) as volume')
                            ->from('pmm_productions pp')
                            ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
                            ->where("pp.date_production between '$date_maret23_awal' and '$date_maret23_akhir'")
                            ->where("pp.status = 'PUBLISH'")
                            ->where("ppo.status in ('OPEN','CLOSED')")
                            ->group_by("pp.client_id")
                            ->get()->row_array();
                            
                            $total_pen_maret23 = $pen_maret23['volume'];

                            $pen_april23 = $this->db->select('SUM(pp.display_volume) as volume')
                            ->from('pmm_productions pp')
                            ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
                            ->where("pp.date_production between '$date_april23_awal' and '$date_april23_akhir'")
                            ->where("pp.status = 'PUBLISH'")
                            ->where("ppo.status in ('OPEN','CLOSED')")
                            ->group_by("pp.client_id")
                            ->get()->row_array();
                            
                            $total_pen_april23 = $pen_april23['volume'];

                            $pen_mei23 = $this->db->select('SUM(pp.display_volume) as volume')
                            ->from('pmm_productions pp')
                            ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
                            ->where("pp.date_production between '$date_mei23_awal' and '$date_mei23_akhir'")
                            ->where("pp.status = 'PUBLISH'")
                            ->where("ppo.status in ('OPEN','CLOSED')")
                            ->group_by("pp.client_id")
                            ->get()->row_array();
                            
                            $total_pen_mei23 = $pen_mei23['volume'];

                            $pen_juni23 = $this->db->select('SUM(pp.display_volume) as volume')
                            ->from('pmm_productions pp')
                            ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
                            ->where("pp.date_production between '$date_juni23_awal' and '$date_juni23_akhir'")
                            ->where("pp.status = 'PUBLISH'")
                            ->where("ppo.status in ('OPEN','CLOSED')")
                            ->group_by("pp.client_id")
                            ->get()->row_array();
                            
                            $total_pen_juni23 = $pen_juni23['volume'];

                            $pen_juli23 = $this->db->select('SUM(pp.display_volume) as volume')
                            ->from('pmm_productions pp')
                            ->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
                            ->where("pp.date_production between '$date_juli23_awal' and '$date_juli23_akhir'")
                            ->where("pp.status = 'PUBLISH'")
                            ->where("ppo.status in ('OPEN','CLOSED')")
                            ->group_by("pp.client_id")
                            ->get()->row_array();
                            
                            $total_pen_juli23 = $pen_juli23['volume'];
                            ?>
                            <?php
                            //JANUARI
                            $januari = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
                            ->from('rak r')
                            ->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_januari_akhir'")
                            ->get()->row_array();

                            $januari_a = $januari['vol_produk_a'];
                            $januari_b = $januari['vol_produk_b'];
                            $januari_c = $januari['vol_produk_c'];
                            $januari_d = $januari['vol_produk_d'];

                            $januari_total = $januari_a + $januari_b + $januari_c + $januari_d;

                            //FEBRUARI
                            $februari = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
                            ->from('rak r')
                            ->where("r.tanggal_rencana_kerja between '$date_februari_awal' and '$date_februari_akhir'")
                            ->get()->row_array();

                            $februari_a = $februari['vol_produk_a'];
                            $februari_b = $februari['vol_produk_b'];
                            $februari_c = $februari['vol_produk_c'];
                            $februari_d = $februari['vol_produk_d'];

                            $februari_total = $februari_a + $februari_b + $februari_c + $februari_d;

                            //MARET
                            $maret = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
                            ->from('rak r')
                            ->where("r.tanggal_rencana_kerja between '$date_maret_awal' and '$date_maret_akhir'")
                            ->get()->row_array();

                            $maret_a = $maret['vol_produk_a'];
                            $maret_b = $maret['vol_produk_b'];
                            $maret_c = $maret['vol_produk_c'];
                            $maret_d = $maret['vol_produk_d'];

                            $maret_total = $maret_a + $maret_b + $maret_c + $maret_d;

                            //APRIL
                            $april = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
                            ->from('rak r')
                            ->where("r.tanggal_rencana_kerja between '$date_april_awal' and '$date_april_akhir'")
                            ->get()->row_array();

                            $april_a = $april['vol_produk_a'];
                            $april_b = $april['vol_produk_b'];
                            $april_c = $april['vol_produk_c'];
                            $april_d = $april['vol_produk_d'];

                            $april_total = $april_a + $april_b + $april_c + $april_d;

                            //MEI
                            $mei = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
                            ->from('rak r')
                            ->where("r.tanggal_rencana_kerja between '$date_mei_awal' and '$date_mei_akhir'")
                            ->get()->row_array();

                            $mei_a = $mei['vol_produk_a'];
                            $mei_b = $mei['vol_produk_b'];
                            $mei_c = $mei['vol_produk_c'];
                            $mei_d = $mei['vol_produk_d'];

                            $mei_total = $mei_a + $mei_b + $mei_c + $mei_d;

                            //JUNI
                            $juni = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
                            ->from('rak r')
                            ->where("r.tanggal_rencana_kerja between '$date_juni_awal' and '$date_juni_akhir'")
                            ->get()->row_array();

                            $juni_a = $juni['vol_produk_a'];
                            $juni_b = $juni['vol_produk_b'];
                            $juni_c = $juni['vol_produk_c'];
                            $juni_d = $juni['vol_produk_d'];

                            $juni_total = $juni_a + $juni_b + $juni_c + $juni_d;

                            //JULI
                            $juli = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
                            ->from('rak r')
                            ->where("r.tanggal_rencana_kerja between '$date_juli_awal' and '$date_juli_akhir'")
                            ->get()->row_array();

                            $juli_a = $juli['vol_produk_a'];
                            $juli_b = $juli['vol_produk_b'];
                            $juli_c = $juli['vol_produk_c'];
                            $juli_d = $juli['vol_produk_d'];

                            $juli_total = $juli_a + $juli_b + $juli_c + $juli_d;

                            //AGUSTUS
                            $agustus = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
                            ->from('rak r')
                            ->where("r.tanggal_rencana_kerja between '$date_agustus_awal' and '$date_agustus_akhir'")
                            ->get()->row_array();

                            $agustus_a = $agustus['vol_produk_a'];
                            $agustus_b = $agustus['vol_produk_b'];
                            $agustus_c = $agustus['vol_produk_c'];
                            $agustus_d = $agustus['vol_produk_d'];

                            $agustus_total = $agustus_a + $agustus_b + $agustus_c + $agustus_d;

                            //SEPTEMBER
                            $september = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
                            ->from('rak r')
                            ->where("r.tanggal_rencana_kerja between '$date_september_awal' and '$date_september_akhir'")
                            ->get()->row_array();

                            $september_a = $september['vol_produk_a'];
                            $september_b = $september['vol_produk_b'];
                            $september_c = $september['vol_produk_c'];
                            $september_d = $september['vol_produk_d'];

                            $september_total = $september_a + $september_b + $september_c + $september_d;

                            //OKTOBER
                            $oktober = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
                            ->from('rak r')
                            ->where("r.tanggal_rencana_kerja between '$date_oktober_awal' and '$date_oktober_akhir'")
                            ->get()->row_array();

                            $oktober_a = $oktober['vol_produk_a'];
                            $oktober_b = $oktober['vol_produk_b'];
                            $oktober_c = $oktober['vol_produk_c'];
                            $oktober_d = $oktober['vol_produk_d'];

                            $oktober_total = $oktober_a + $oktober_b + $oktober_c + $oktober_d;

                            //NOVEMBER
                            $november = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
                            ->from('rak r')
                            ->where("r.tanggal_rencana_kerja between '$date_november_awal' and '$date_november_akhir'")
                            ->get()->row_array();

                            $november_a = $november['vol_produk_a'];
                            $november_b = $november['vol_produk_b'];
                            $november_c = $november['vol_produk_c'];
                            $november_d = $november['vol_produk_d'];

                            $november_total = $november_a + $november_b + $november_c + $november_d;

                            //DESEMBER
                            $desember = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
                            ->from('rak r')
                            ->where("r.tanggal_rencana_kerja between '$date_desember_awal' and '$date_desember_akhir'")
                            ->get()->row_array();

                            $desember_a = $desember['vol_produk_a'];
                            $desember_b = $desember['vol_produk_b'];
                            $desember_c = $desember['vol_produk_c'];
                            $desember_d = $desember['vol_produk_d'];

                            $desember_total = $desember_a + $desember_b + $desember_c + $desember_d;

                            //JANUARI23
                            $januari23 = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
                            ->from('rak r')
                            ->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
                            ->get()->row_array();

                            $januari23_a = $januari23['vol_produk_a'];
                            $januari23_b = $januari23['vol_produk_b'];
                            $januari23_c = $januari23['vol_produk_c'];
                            $januari23_d = $januari23['vol_produk_d'];

                            $januari23_total = $januari23_a + $januari23_b + $januari23_c + $januari23_d;

                            //FEBRUARI23
                            $februari23 = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
                            ->from('rak r')
                            ->where("r.tanggal_rencana_kerja between '$date_februari23_awal' and '$date_februari23_akhir'")
                            ->get()->row_array();

                            $februari23_a = $februari23['vol_produk_a'];
                            $februari23_b = $februari23['vol_produk_b'];
                            $februari23_c = $februari23['vol_produk_c'];
                            $februari23_d = $februari23['vol_produk_d'];

                            $februari23_total = $februari23_a + $februari23_b + $februari23_c + $februari23_d;

                            //MARET23
                            $maret23 = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
                            ->from('rak r')
                            ->where("r.tanggal_rencana_kerja between '$date_maret23_awal' and '$date_maret23_akhir'")
                            ->get()->row_array();

                            $maret23_a = $maret23['vol_produk_a'];
                            $maret23_b = $maret23['vol_produk_b'];
                            $maret23_c = $maret23['vol_produk_c'];
                            $maret23_d = $maret23['vol_produk_d'];

                            $maret23_total = $maret23_a + $maret23_b + $maret23_c + $maret23_d;

                            //APRIL23
                            $april23 = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
                            ->from('rak r')
                            ->where("r.tanggal_rencana_kerja between '$date_april23_awal' and '$date_april23_akhir'")
                            ->get()->row_array();

                            $april23_a = $april23['vol_produk_a'];
                            $april23_b = $april23['vol_produk_b'];
                            $april23_c = $april23['vol_produk_c'];
                            $april23_d = $april23['vol_produk_d'];

                            $april23_total = $april23_a + $april23_b + $april23_c + $april23_d;

                            //MEI23
                            $mei23 = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
                            ->from('rak r')
                            ->where("r.tanggal_rencana_kerja between '$date_mei23_awal' and '$date_mei23_akhir'")
                            ->get()->row_array();

                            $mei23_a = $mei23['vol_produk_a'];
                            $mei23_b = $mei23['vol_produk_b'];
                            $mei23_c = $mei23['vol_produk_c'];
                            $mei23_d = $mei23['vol_produk_d'];

                            $mei23_total = $mei23_a + $mei23_b + $mei23_c + $mei23_d;

                            //JUNI23
                            $juni23 = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
                            ->from('rak r')
                            ->where("r.tanggal_rencana_kerja between '$date_juni23_awal' and '$date_juni23_akhir'")
                            ->get()->row_array();

                            $juni23_a = $juni23['vol_produk_a'];
                            $juni23_b = $juni23['vol_produk_b'];
                            $juni23_c = $juni23['vol_produk_c'];
                            $juni23_d = $juni23['vol_produk_d'];

                            $juni23_total = $juni23_a + $juni23_b + $juni23_c + $juni23_d;

                            //JULI23
                            $juli23 = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
                            ->from('rak r')
                            ->where("r.tanggal_rencana_kerja between '$date_juli23_awal' and '$date_juli23_akhir'")
                            ->get()->row_array();

                            $juli23_a = $juli23['vol_produk_a'];
                            $juli23_b = $juli23['vol_produk_b'];
                            $juli23_c = $juli23['vol_produk_c'];
                            $juli23_d = $juli23['vol_produk_d'];

                            $juli23_total = $juli23_a + $juli23_b + $juli23_c + $juli23_d;

                            ?>
                            <table style="background-color:#FFFFFF;" border="0" width="100%">
                                <tr style="background-color:#808080; color:white;">
                                    <th class="text-center" width="8%">Keterangan</th>
                                    <th class="text-center">Jan 22</th>
                                    <th class="text-center">Feb 22</th>
                                    <th class="text-center">Mar 22</th>
                                    <th class="text-center">Apr 22</th>
                                    <th class="text-center">Mei 22</th>
                                    <th class="text-center">Jun 22</th>
                                    <th class="text-center">Jul 22</th>
                                    <th class="text-center">Agu 22</th>
                                    <th class="text-center">Sep 22</th>
                                    <th class="text-center">Okt 22</th>
                                    <th class="text-center">Nov 22</th>
                                    <th class="text-center">Des 22</th>
                                    <th class="text-center">Jan 23</th>
                                    <th class="text-center">Feb 23</th>
                                    <th class="text-center">Mar 23</th>
                                    <th class="text-center">Apr 23</th>
                                    <th class="text-center">Mei 23</th>
                                    <th class="text-center">Jun 23</th>
                                    <th class="text-center">Jul 23</th>
                                    <th class="text-center" width="2%"></th>
                                </tr>
                                <tr style="background-color:#000000; color:white;">
                                    <th class="text-right">Vol. RAP</th>
                                    <th class="text-right"><?php echo number_format($januari_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($februari_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($maret_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($april_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($mei_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($juni_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($juli_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($agustus_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($september_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($oktober_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($november_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($desember_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($januari23_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($februari23_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($maret23_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($april23_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($mei23_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($juni23_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($juli23_total,2,',','.');?></th>
                                    <th class="text-right"></th>
                                </tr>
                                <tr style="background-color:#FF0000; color:white;">
                                    <th class="text-right">Vol. Realisasi</th>
                                    <th class="text-right"><?php echo number_format($total_pen_januari,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_februari,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_maret,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_april,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_mei,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_juni,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_juli,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_agustus,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_september,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_oktober,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_november,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_desember,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_januari23,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_februari23,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_maret23,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_april23,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_mei23,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_juni23,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_juli23,2,',','.');?></th>
                                    <th class="text-right"></th>
                                </tr>
                                <tr style="background-color:#38761D; color:white;">
                                    <th class="text-right">Vol. Tdk. Tercapai</th>
                                    <th class="text-right"><?php echo number_format($total_pen_januari - $januari_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_februari - $februari_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_maret - $maret_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_april - $april_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_mei - $mei_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_juni - $juni_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_juli - $juli_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_agustus - $agustus_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_september - $september_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_oktober - $oktober_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_november - $november_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_desember - $desember_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_januari23 - $januari23_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_februari23 - $februari23_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_maret23 - $maret23_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_april23 - $april23_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_mei23 - $mei23_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_juni23 - $juni23_total,2,',','.');?></th>
                                    <th class="text-right"><?php echo number_format($total_pen_juli23 - $juli23_total,2,',','.');?></th>
                                    <th class="text-right"></th>
                                </tr>                                
                            </table>
                        </font> 
                        
                    </figure>
                </div>  
                <br />
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
                            [0, 'rgb(255, 255, 255)'],
                            [1, 'rgb(255, 255, 255)']
                        ]
                    },
                },
                title: {
                    text: 'Realisasi Produksi',
                    x: -20 //center
                },
                subtitle: {
                    text: 'PT. Bia Bumi Jayendra - TEMEF',
                    x: -20
                },
                xAxis: { //X axis menampilkan data bulan
                    categories: ['Jan 22','Feb 22','Mar 22','Apr 22','Mei 22','Jun 22','Jul 22','Agu 22','Sep 22','Okt 22','Nov 22','Des 22','Jan 23','Feb 23','Mar 23','Apr 23','Mei 23','Jun 23','Jul 23']
                },
                yAxis: {
                    title: {  //label yAxis
                        text: 'Presentase Thdp. (Kontrak : <?php echo number_format($total_kontrak_all,0,',','.'); ?>)'
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
                    max: 150,
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
                    
                    data: [<?php echo json_encode($net_januari_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_februari_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_maret_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_april_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_mei_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_juni_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_juli_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_agustus_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_september_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_oktober_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_november_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_desember_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_januari23_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_februari23_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_maret23_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_april23_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_mei23_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_juni23_rap, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_juli23_rap, JSON_NUMERIC_CHECK); ?>],

                    color: '#000000',
                },
                {  
                    name: 'Realisasi %',  
                    
                    data: [<?php echo json_encode($net_januari, JSON_NUMERIC_CHECK); ?>, <?php echo json_encode($net_februari, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_maret, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_april, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_mei, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_juni, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_juli, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_agustus, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_september, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_oktober, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_november, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_desember, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_januari23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_februari23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_maret23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_april23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_mei23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_juni23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($net_juli23, JSON_NUMERIC_CHECK); ?>],

                    color: '#FF0000',

                    zones: [{
                        value: 25
                    }, {
                        dashStyle: 'dot'
                    }]
                },
                {  
                    name: 'Tidak Tercapai %',  
                    
                    data: [<?php echo json_encode($selisih_januari, JSON_NUMERIC_CHECK); ?>, <?php echo json_encode($selisih_februari, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_maret, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_april, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_mei, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_juni, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_juli, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_agustus, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_september, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_oktober, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_november, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_desember, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_januari23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_februari23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_maret23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_april23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_mei23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_juni23, JSON_NUMERIC_CHECK); ?>,<?php echo json_encode($selisih_juli23, JSON_NUMERIC_CHECK); ?>],

                    color: '#38761D',

                    visible: false
                },
                ]
            });
        });
        
    });
		</script>

</body>
</html>
