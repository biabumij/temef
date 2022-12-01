<!DOCTYPE html>
<html>
	<head>
	  <title>KEBUTUHAN BAHAN BAKU</title>
	  
	  <style type="text/css">
		table tr.table-judul{
			background-color: #e69500;
			font-weight: bold;
			font-size: 8px;
			color: black;
		}
			
		table tr.table-baris1{
			background-color: #F0F0F0;
			font-size: 8px;
		}

		table tr.table-baris1-bold{
			background-color: #F0F0F0;
			font-size: 8px;
			font-weight: bold;
		}
			
		table tr.table-baris2{
			font-size: 8px;
			background-color: #E8E8E8;
		}
			
		table tr.table-total{
			background-color: #cccccc;
			font-weight: bold;
			font-size: 8px;
			color: black;
		}
	  </style>

	</head>
	<body>
		<?php
		$data = array();
		
		$arr_date = $this->input->get('filter_date');
		$arr_filter_date = explode(' - ', $arr_date);
		$date1 = '';
		$date2 = '';

		if(count($arr_filter_date) == 2){
			$date1 	= date('Y-m-d',strtotime($arr_filter_date[0]));
			$date2 	= date('Y-m-d',strtotime($arr_filter_date[1]));
			$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]));
		}
		
		?>
		
		<table width="98%" border="0" cellpadding="3" border="0">
		<!-- RENCANA KERJA-->
		<?php
		$stock_opname = $this->db->select('date')->order_by('date','desc')->limit(1,5)->get_where('pmm_remaining_materials_cat',array('status'=>'PUBLISH'))->row_array();
		//$stock_opname = $this->db->select('date')->order_by('date','desc')->limit(1,1)->get_where('pmm_remaining_materials_cat',array('status'=>'PUBLISH'))->row_array();
		$last_opname =  date('Y-m-d', strtotime($stock_opname['date']));

		$date_november_awal = date('Y-m-d', strtotime('+1 days', strtotime($last_opname)));
		$date_november_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_november_awal)));
		$november = date('F Y', strtotime('+1 days', strtotime($last_opname)));

		$date_desember_awal = date('Y-m-d', strtotime('+1 days', strtotime($date_november_akhir)));
		$date_desember_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_desember_awal)));
		$desember = date('F Y', strtotime('+1 days', strtotime($date_november_akhir)));

		$date_januari23_awal = date('Y-m-d', strtotime('+1 days', strtotime($date_desember_akhir)));
		$date_januari23_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_januari23_awal)));
		$januari23 = date('F Y', strtotime('+1 days', strtotime($date_desember_akhir)));

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
		$nilai_jual_all_november = $nilai_jual_125_november + $nilai_jual_225_november + $nilai_jual_250_november + $nilai_jual_250_18_november;

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
		$nilai_jual_all_desember = $nilai_jual_125_desember + $nilai_jual_225_desember + $nilai_jual_250_desember + $nilai_jual_250_18_desember;

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
		$nilai_jual_all_januari23 = $nilai_jual_125_januari23 + $nilai_jual_225_januari23 + $nilai_jual_250_januari23 + $nilai_jual_250_18_januari23;
		?>

		
		<?php
		//NOVEMBER
		$komposisi_125_nov = $this->db->select('(r.vol_produk_a * pk.presentase_a) as komposisi_semen_125, (vol_produk_a * pk.presentase_b) as komposisi_pasir_125, (vol_produk_a * pk.presentase_c) as komposisi_batu1020_125, (vol_produk_a * pk.presentase_d) as komposisi_batu2030_125')
		->from('rak r')
		->join('pmm_agregat pk', 'r.komposisi_125 = pk.id','left')
		->where("r.tanggal_rencana_kerja between '$date_november_awal' and '$date_november_akhir'")
		->get()->result_array();

		$total_volume_semen_125_nov = 0;
		$total_volume_pasir_125_nov = 0;
		$total_volume_batu1020_125_nov = 0;
		$total_volume_batu2030_125_nov = 0;

		foreach ($komposisi_125_nov as $x){
			$total_volume_semen_125_nov = $x['komposisi_semen_125'];
			$total_volume_pasir_125_nov = $x['komposisi_pasir_125'];
			$total_volume_batu1020_125_nov = $x['komposisi_batu1020_125'];
			$total_volume_batu2030_125_nov = $x['komposisi_batu2030_125'];
		}

		$komposisi_225_nov = $this->db->select('(r.vol_produk_b * pk.presentase_a) as komposisi_semen_225, (vol_produk_b * pk.presentase_b) as komposisi_pasir_225, (vol_produk_b * pk.presentase_c) as komposisi_batu1020_225, (vol_produk_b * pk.presentase_d) as komposisi_batu2030_225')
		->from('rak r')
		->join('pmm_agregat pk', 'r.komposisi_225 = pk.id','left')
		->where("r.tanggal_rencana_kerja between '$date_november_awal' and '$date_november_akhir'")
		->get()->result_array();

		$total_volume_semen_225_nov = 0;
		$total_volume_pasir_225_nov = 0;
		$total_volume_batu1020_225_nov = 0;
		$total_volume_batu2030_225_nov = 0;

		foreach ($komposisi_225_nov as $x){
			$total_volume_semen_225_nov = $x['komposisi_semen_225'];
			$total_volume_pasir_225_nov = $x['komposisi_pasir_225'];
			$total_volume_batu1020_225_nov = $x['komposisi_batu1020_225'];
			$total_volume_batu2030_225_nov = $x['komposisi_batu2030_225'];
		}

		$komposisi_250_nov = $this->db->select('(r.vol_produk_c * pk.presentase_a) as komposisi_semen_250, (vol_produk_c * pk.presentase_b) as komposisi_pasir_250, (vol_produk_c * pk.presentase_c) as komposisi_batu1020_250, (vol_produk_c * pk.presentase_d) as komposisi_batu2030_250')
		->from('rak r')
		->join('pmm_agregat pk', 'r.komposisi_250 = pk.id','left')
		->where("r.tanggal_rencana_kerja between '$date_november_awal' and '$date_november_akhir'")
		->get()->result_array();

		$total_volume_semen_250_nov = 0;
		$total_volume_pasir_250_nov = 0;
		$total_volume_batu1020_250_nov = 0;
		$total_volume_batu2030_250_nov = 0;

		foreach ($komposisi_250_nov as $x){
			$total_volume_semen_250_nov = $x['komposisi_semen_250'];
			$total_volume_pasir_250_nov = $x['komposisi_pasir_250'];
			$total_volume_batu1020_250_nov = $x['komposisi_batu1020_250'];
			$total_volume_batu2030_250_nov = $x['komposisi_batu2030_250'];
		}

		$komposisi_250_2_nov = $this->db->select('(r.vol_produk_d * pk.presentase_a) as komposisi_semen_250_2, (vol_produk_d * pk.presentase_b) as komposisi_pasir_250_2, (vol_produk_d * pk.presentase_c) as komposisi_batu1020_250_2, (vol_produk_d * pk.presentase_d) as komposisi_batu2030_250_2')
		->from('rak r')
		->join('pmm_agregat pk', 'r.komposisi_250_2 = pk.id','left')
		->where("r.tanggal_rencana_kerja between '$date_november_awal' and '$date_november_akhir'")
		->get()->result_array();

		$total_volume_semen_250_2_nov = 0;
		$total_volume_pasir_250_2_nov = 0;
		$total_volume_batu1020_250_2_nov = 0;
		$total_volume_batu2030_250_2_nov = 0;

		foreach ($komposisi_250_2_nov as $x){
			$total_volume_semen_250_2_nov = $x['komposisi_semen_250_2'];
			$total_volume_pasir_250_2_nov = $x['komposisi_pasir_250_2'];
			$total_volume_batu1020_250_2_nov = $x['komposisi_batu1020_250_2'];
			$total_volume_batu2030_250_2_nov = $x['komposisi_batu2030_250_2'];
		}

		$total_volume_semen_nov = $total_volume_semen_125_nov + $total_volume_semen_225_nov + $total_volume_semen_250_nov + $total_volume_semen_250_2_nov;
		$total_volume_pasir_nov = $total_volume_pasir_125_nov + $total_volume_pasir_225_nov + $total_volume_pasir_250_nov + $total_volume_pasir_250_2_nov;
		$total_volume_batu1020_nov = $total_volume_batu1020_125_nov + $total_volume_batu1020_225_nov + $total_volume_batu1020_250_nov + $total_volume_batu1020_250_2_nov;
		$total_volume_batu2030_nov = $total_volume_batu2030_125_nov + $total_volume_batu2030_225_nov + $total_volume_batu2030_250_nov + $total_volume_batu2030_250_2_nov;

		//DESEMBER
		$komposisi_125_des = $this->db->select('(r.vol_produk_a * pk.presentase_a) as komposisi_semen_125, (vol_produk_a * pk.presentase_b) as komposisi_pasir_125, (vol_produk_a * pk.presentase_c) as komposisi_batu1020_125, (vol_produk_a * pk.presentase_d) as komposisi_batu2030_125')
		->from('rak r')
		->join('pmm_agregat pk', 'r.komposisi_125 = pk.id','left')
		->where("r.tanggal_rencana_kerja between '$date_desember_awal' and '$date_desember_akhir'")
		->get()->result_array();

		$total_volume_semen_125_des = 0;
		$total_volume_pasir_125_des = 0;
		$total_volume_batu1020_125_des = 0;
		$total_volume_batu2030_125_des = 0;

		foreach ($komposisi_125_des as $x){
			$total_volume_semen_125_des = $x['komposisi_semen_125'];
			$total_volume_pasir_125_des = $x['komposisi_pasir_125'];
			$total_volume_batu1020_125_des = $x['komposisi_batu1020_125'];
			$total_volume_batu2030_125_des = $x['komposisi_batu2030_125'];
		}

		$komposisi_225_des = $this->db->select('(r.vol_produk_b * pk.presentase_a) as komposisi_semen_225, (vol_produk_b * pk.presentase_b) as komposisi_pasir_225, (vol_produk_b * pk.presentase_c) as komposisi_batu1020_225, (vol_produk_b * pk.presentase_d) as komposisi_batu2030_225')
		->from('rak r')
		->join('pmm_agregat pk', 'r.komposisi_225 = pk.id','left')
		->where("r.tanggal_rencana_kerja between '$date_desember_awal' and '$date_desember_akhir'")
		->get()->result_array();

		$total_volume_semen_225_des = 0;
		$total_volume_pasir_225_des = 0;
		$total_volume_batu1020_225_des = 0;
		$total_volume_batu2030_225_des = 0;

		foreach ($komposisi_225_des as $x){
			$total_volume_semen_225_des = $x['komposisi_semen_225'];
			$total_volume_pasir_225_des = $x['komposisi_pasir_225'];
			$total_volume_batu1020_225_des = $x['komposisi_batu1020_225'];
			$total_volume_batu2030_225_des = $x['komposisi_batu2030_225'];
		}

		$komposisi_250_des = $this->db->select('(r.vol_produk_c * pk.presentase_a) as komposisi_semen_250, (vol_produk_c * pk.presentase_b) as komposisi_pasir_250, (vol_produk_c * pk.presentase_c) as komposisi_batu1020_250, (vol_produk_c * pk.presentase_d) as komposisi_batu2030_250')
		->from('rak r')
		->join('pmm_agregat pk', 'r.komposisi_250 = pk.id','left')
		->where("r.tanggal_rencana_kerja between '$date_desember_awal' and '$date_desember_akhir'")
		->get()->result_array();

		$total_volume_semen_250_des = 0;
		$total_volume_pasir_250_des = 0;
		$total_volume_batu1020_250_des = 0;
		$total_volume_batu2030_250_des = 0;

		foreach ($komposisi_250_des as $x){
			$total_volume_semen_250_des = $x['komposisi_semen_250'];
			$total_volume_pasir_250_des = $x['komposisi_pasir_250'];
			$total_volume_batu1020_250_des = $x['komposisi_batu1020_250'];
			$total_volume_batu2030_250_des = $x['komposisi_batu2030_250'];
		}

		$komposisi_250_2_des = $this->db->select('(r.vol_produk_d * pk.presentase_a) as komposisi_semen_250_2, (vol_produk_d * pk.presentase_b) as komposisi_pasir_250_2, (vol_produk_d * pk.presentase_c) as komposisi_batu1020_250_2, (vol_produk_d * pk.presentase_d) as komposisi_batu2030_250_2')
		->from('rak r')
		->join('pmm_agregat pk', 'r.komposisi_250_2 = pk.id','left')
		->where("r.tanggal_rencana_kerja between '$date_desember_awal' and '$date_desember_akhir'")
		->get()->result_array();

		$total_volume_semen_250_2_des = 0;
		$total_volume_pasir_250_2_des = 0;
		$total_volume_batu1020_250_2_des = 0;
		$total_volume_batu2030_250_2_des = 0;

		foreach ($komposisi_250_2_des as $x){
			$total_volume_semen_250_2_des = $x['komposisi_semen_250_2'];
			$total_volume_pasir_250_2_des = $x['komposisi_pasir_250_2'];
			$total_volume_batu1020_250_2_des = $x['komposisi_batu1020_250_2'];
			$total_volume_batu2030_250_2_des = $x['komposisi_batu2030_250_2'];
		}

		$total_volume_semen_des = $total_volume_semen_125_des + $total_volume_semen_225_des + $total_volume_semen_250_des + $total_volume_semen_250_2_des;
		$total_volume_pasir_des = $total_volume_pasir_125_des + $total_volume_pasir_225_des + $total_volume_pasir_250_des + $total_volume_pasir_250_2_des;
		$total_volume_batu1020_des = $total_volume_batu1020_125_des + $total_volume_batu1020_225_des + $total_volume_batu1020_250_des + $total_volume_batu1020_250_2_des;
		$total_volume_batu2030_des = $total_volume_batu2030_125_des + $total_volume_batu2030_225_des + $total_volume_batu2030_250_des + $total_volume_batu2030_250_2_des;

		//JANUARI23
		$komposisi_125_jan23 = $this->db->select('(r.vol_produk_a * pk.presentase_a) as komposisi_semen_125, (vol_produk_a * pk.presentase_b) as komposisi_pasir_125, (vol_produk_a * pk.presentase_c) as komposisi_batu1020_125, (vol_produk_a * pk.presentase_d) as komposisi_batu2030_125')
		->from('rak r')
		->join('pmm_agregat pk', 'r.komposisi_125 = pk.id','left')
		->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
		->get()->result_array();

		$total_volume_semen_125_jan23 = 0;
		$total_volume_pasir_125_jan23 = 0;
		$total_volume_batu1020_125_jan23 = 0;
		$total_volume_batu2030_125_jan23 = 0;

		foreach ($komposisi_125_jan23 as $x){
			$total_volume_semen_125_jan23 = $x['komposisi_semen_125'];
			$total_volume_pasir_125_jan23 = $x['komposisi_pasir_125'];
			$total_volume_batu1020_125_jan23 = $x['komposisi_batu1020_125'];
			$total_volume_batu2030_125_jan23 = $x['komposisi_batu2030_125'];
		}

		$komposisi_225_jan23 = $this->db->select('(r.vol_produk_b * pk.presentase_a) as komposisi_semen_225, (vol_produk_b * pk.presentase_b) as komposisi_pasir_225, (vol_produk_b * pk.presentase_c) as komposisi_batu1020_225, (vol_produk_b * pk.presentase_d) as komposisi_batu2030_225')
		->from('rak r')
		->join('pmm_agregat pk', 'r.komposisi_225 = pk.id','left')
		->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
		->get()->result_array();

		$total_volume_semen_225_jan23 = 0;
		$total_volume_pasir_225_jan23 = 0;
		$total_volume_batu1020_225_jan23 = 0;
		$total_volume_batu2030_225_jan23 = 0;

		foreach ($komposisi_225_jan23 as $x){
			$total_volume_semen_225_jan23 = $x['komposisi_semen_225'];
			$total_volume_pasir_225_jan23 = $x['komposisi_pasir_225'];
			$total_volume_batu1020_225_jan23 = $x['komposisi_batu1020_225'];
			$total_volume_batu2030_225_jan23 = $x['komposisi_batu2030_225'];
		}

		$komposisi_250_jan23 = $this->db->select('(r.vol_produk_c * pk.presentase_a) as komposisi_semen_250, (vol_produk_c * pk.presentase_b) as komposisi_pasir_250, (vol_produk_c * pk.presentase_c) as komposisi_batu1020_250, (vol_produk_c * pk.presentase_d) as komposisi_batu2030_250')
		->from('rak r')
		->join('pmm_agregat pk', 'r.komposisi_250 = pk.id','left')
		->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
		->get()->result_array();

		$total_volume_semen_250_jan23 = 0;
		$total_volume_pasir_250_jan23 = 0;
		$total_volume_batu1020_250_jan23 = 0;
		$total_volume_batu2030_250_jan23 = 0;

		foreach ($komposisi_250_jan23 as $x){
			$total_volume_semen_250_jan23 = $x['komposisi_semen_250'];
			$total_volume_pasir_250_jan23 = $x['komposisi_pasir_250'];
			$total_volume_batu1020_250_jan23 = $x['komposisi_batu1020_250'];
			$total_volume_batu2030_250_jan23 = $x['komposisi_batu2030_250'];
		}

		$komposisi_250_2_jan23 = $this->db->select('(r.vol_produk_d * pk.presentase_a) as komposisi_semen_250_2, (vol_produk_d * pk.presentase_b) as komposisi_pasir_250_2, (vol_produk_d * pk.presentase_c) as komposisi_batu1020_250_2, (vol_produk_d * pk.presentase_d) as komposisi_batu2030_250_2')
		->from('rak r')
		->join('pmm_agregat pk', 'r.komposisi_250_2 = pk.id','left')
		->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
		->get()->result_array();

		$total_volume_semen_250_2_jan23 = 0;
		$total_volume_pasir_250_2_jan23 = 0;
		$total_volume_batu1020_250_2_jan23 = 0;
		$total_volume_batu2030_250_2_jan23 = 0;

		foreach ($komposisi_250_2_jan23 as $x){
			$total_volume_semen_250_2_jan23 = $x['komposisi_semen_250_2'];
			$total_volume_pasir_250_2_jan23 = $x['komposisi_pasir_250_2'];
			$total_volume_batu1020_250_2_jan23 = $x['komposisi_batu1020_250_2'];
			$total_volume_batu2030_250_2_jan23 = $x['komposisi_batu2030_250_2'];
		}

		$total_volume_semen_jan23 = $total_volume_semen_125_jan23 + $total_volume_semen_225_jan23 + $total_volume_semen_250_jan23 + $total_volume_semen_250_2_jan23;
		$total_volume_pasir_jan23 = $total_volume_pasir_125_jan23 + $total_volume_pasir_225_jan23 + $total_volume_pasir_250_jan23 + $total_volume_pasir_250_2_jan23;
		$total_volume_batu1020_jan23 = $total_volume_batu1020_125_jan23 + $total_volume_batu1020_225_jan23 + $total_volume_batu1020_250_jan23 + $total_volume_batu1020_250_2_jan23;
		$total_volume_batu2030_jan23 = $total_volume_batu2030_125_jan23 + $total_volume_batu2030_225_jan23 + $total_volume_batu2030_250_jan23 + $total_volume_batu2030_250_2_jan23;
		
		//SOLAR
		$rap_solar = $this->db->select('rap.*')
		->from('rap_alat rap')
		->where('rap.status','PUBLISH')
		->order_by('rap.id','desc')->limit(1)
		->get()->row_array();

		$total_volume_solar_nov = $total_november_volume * $rap_solar['vol_bbm_solar'];
		$total_volume_solar_des = $total_desember_volume * $rap_solar['vol_bbm_solar'];
		$total_volume_solar_jan23 = $total_januari23_volume * $rap_solar['vol_bbm_solar'];
		?>

		<!-- HARGA -->
		<?php
		$nov = $this->db->select('r.*')
		->from('rak r')
		->where("r.tanggal_rencana_kerja between '$date_november_awal' and '$date_november_akhir'")
		->get()->row_array();

		$des = $this->db->select('r.*')
		->from('rak r')
		->where("r.tanggal_rencana_kerja between '$date_desember_awal' and '$date_desember_akhir'")
		->get()->row_array();

		$jan23 = $this->db->select('r.*')
		->from('rak r')
		->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
		->get()->row_array();
		?>
			<tr>
				<td align="center"  width="100%">
					<div style="display: block;font-weight: bold;font-size: 11px;">KEBUTUHAN BAHAN BAKU</div>
					<div style="display: block;font-weight: bold;font-size: 11px;">DIVISI BETON  PROYEK BENDUNGAN TEMEF</div>
				    <div style="display: block;font-weight: bold;font-size: 11px;">PT. BIA BUMI JAYENDRA</div>
					<div style="display: block;font-weight: bold;font-size: 11px; text-transform: uppercase;">PERIODE : <?php echo $januari23;?></div>
				</td>
			</tr>
			<br />
			<br />
			<tr class="table-judul">
				<th width="5%" align="center">NO.</th>
				<th width="15%" align="center">URAIAN</th>
				<th width="20%" align="center">REKANAN</th>
				<th width="15%" align="center">VOLUME</th>
				<th width="15%" align="center">SATUAN</th>
				<th width="15%" align="center">HARGA SATUAN</th>
				<th width="15%" align="center">NILAI</th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">1.</th>	
				<th align="left">Semen</th>
				<th align="left"><?= $this->crud_global->GetField('penerima',array('id'=>$jan23['supplier_id_semen']),'nama');?></th>
				<th align="center"><?php echo number_format($total_volume_semen_jan23,2,',','.');?></th>
				<th align="center">Ton</th>
				<th align="center"><?php echo number_format($jan23['harga_semen'],2,',','.');?></th>
				<th align="right"><?php echo number_format($total_volume_semen_jan23 * $jan23['harga_semen'],2,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">2.</th>	
				<th align="left">Pasir</th>
				<th align="left"><?= $this->crud_global->GetField('penerima',array('id'=>$jan23['supplier_id_pasir']),'nama');?></th>
				<th align="center"><?php echo number_format($total_volume_pasir_jan23,2,',','.');?></th>
				<th align="center">Ton</th>
				<th align="center"><?php echo number_format($jan23['harga_pasir'],2,',','.');?></th>
				<th align="right"><?php echo number_format($total_volume_pasir_jan23 * $jan23['harga_pasir'],2,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">3.</th>	
				<th align="left">Batu Split 10-20</th>
				<th align="left"><?= $this->crud_global->GetField('penerima',array('id'=>$jan23['supplier_id_batu1020']),'nama');?></th>
				<th align="center"><?php echo number_format($total_volume_batu1020_jan23,2,',','.');?></th>
				<th align="center">Ton</th>
				<th align="center"><?php echo number_format($jan23['harga_batu1020'],2,',','.');?></th>
				<th align="right"><?php echo number_format($total_volume_batu1020_jan23 * $jan23['harga_batu1020'],2,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">4.</th>	
				<th align="left">Batu Split 20-30</th>
				<th align="left"><?= $this->crud_global->GetField('penerima',array('id'=>$jan23['supplier_id_batu2030']),'nama');?></th>
				<th align="center"><?php echo number_format($total_volume_batu2030_jan23,2,',','.');?></th>
				<th align="center">Ton</th>
				<th align="center"><?php echo number_format($jan23['harga_batu2030'],2,',','.');?></th>
				<th align="right"><?php echo number_format($total_volume_batu2030_jan23 * $jan23['harga_batu2030'],2,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">5.</th>	
				<th align="left">BBM Solar</th>
				<th align="left"><?= $this->crud_global->GetField('penerima',array('id'=>$jan23['supplier_id_solar']),'nama');?></th>
				<th align="center"><?php echo number_format($total_volume_solar_jan23,2,',','.');?></th>
				<th align="center">Ton</th>
				<th align="center"><?php echo number_format($jan23['harga_solar'],2,',','.');?></th>
				<th align="right"><?php echo number_format($total_volume_solar_jan23 * $jan23['harga_solar'],2,',','.');?></th>
	        </tr>
	    </table>
	</body>
</html>