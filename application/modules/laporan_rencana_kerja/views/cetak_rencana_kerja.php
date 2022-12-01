<!DOCTYPE html>
<html>
	<head>
	  <title>LAPORAN RENCANA KERJA PRODUKSI</title>
	  
	  <style type="text/css">
		table tr.table-judul{
			background-color: #e69500;
			font-weight: bold;
			font-size: 9px;
			color: black;
		}
			
		table tr.table-baris1{
			background-color: #F0F0F0;
			font-size: 9px;
		}

		table tr.table-baris1-bold{
			background-color: #F0F0F0;
			font-size: 9px;
			font-weight: bold;
		}
			
		table tr.table-baris2{
			font-size: 9px;
			background-color: #E8E8E8;
		}
			
		table tr.table-total{
			background-color: #cccccc;
			font-weight: bold;
			font-size: 10px;
			color: black;
		}
	  </style>

	</head>
	<body>
		<br />
		<br />
		<table width="98%" cellpadding="3">
			<tr>
				<td align="center"  width="100%">
					<div style="display: block;font-weight: bold;font-size: 12px;">RENCANA KERJA PRODUKSI</div>
					<div style="display: block;font-weight: bold;font-size: 11px;">DIVISI BETON  PROYEK BENDUNGAN TEMEF</div>
				    <div style="display: block;font-weight: bold;font-size: 11px;">PT. BIA BUMI JAYENDRA</div>
					<div style="display: block;font-weight: bold;font-size: 12px; text-transform: uppercase;">PERIODE : 2022 - 2023</div>
				</td>
			</tr>
		</table>
		<br />
		<br />
		<br />
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
			<tr class="table-judul">
				<th width="5%" align="center">NO.</th>
				<th width="20%" align="center">URAIAN</th>
				<th width="15%" align="center">SATUAN</th>
				<th width="20%" align="center"><div style="vertical-align:middle; text-transform:uppercase;"><?php echo $november;?></div></th>
				<th width="20%" align="center"><div style="vertical-align:middle; text-transform:uppercase;"><?php echo $desember;?></div></th>
				<th width="20%" align="center"><div style="vertical-align:middle; text-transform:uppercase;"><?php echo $januari23;?></div></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">1.</th>
				<th align="left">Beton K 125 (10±2)</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($volume_november_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_desember_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_januari23_produk_a,2,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center">2.</th>
				<th align="left">Beton K 225 (10±2)</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($volume_november_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_desember_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_januari23_produk_b,2,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center">3.</th>
				<th align="left">Beton K 250 (10±2)</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($volume_november_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_desember_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_januari23_produk_c,2,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center">4.</th>
				<th align="left">Beton K 250 (18±2)</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($volume_november_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_desember_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_januari23_produk_d,2,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="right" colspan="3">TOTAL VOLUME</th>
				<th align="right"><?php echo number_format($total_november_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_desember_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_januari23_volume,2,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="right" colspan="3">PENDAPATAN USAHA</th>
				<th align="right"><?php echo number_format($nilai_jual_all_november,2,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_jual_all_desember,2,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_jual_all_januari23,2,',','.');?></th>
			</tr>
			<br />
			<br />
			<tr class="table-judul">
				<th width="25%" align="center" colspan="2">KEBUTUHAN BAHAN</th>
				<th width="15%" align="center">SATUAN</th>
				<th width="20%" align="center">PENGADAAN</th>
				<th width="20%" align="center">PENGADAAN</th>
				<th width="20%" align="center">PENGADAAN</th>
	        </tr>
			<tr class="table-baris1">
				<th align="right" colspan="2">Semen</th>
				<th align="center">Ton</th>
				<th align="right"><?php echo number_format($total_volume_semen_nov,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_volume_semen_des,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_volume_semen_jan23,2,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="right" colspan="2">Pasir</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($total_volume_pasir_nov,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_volume_pasir_des,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_volume_pasir_jan23,2,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="right" colspan="2">Batu Split 10-20</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($total_volume_batu1020_nov,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_volume_batu1020_des,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_volume_batu1020_jan23,2,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="right" colspan="2">Batu Split 20-30</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($total_volume_batu2030_nov,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_volume_batu2030_des,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_volume_batu2030_jan23,2,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="right" colspan="2">Solar</th>
				<th align="center">Liter</th>
				<th align="right"><?php echo number_format($total_volume_solar_nov,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_volume_solar_des,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_volume_solar_jan23,2,',','.');?></th>
			</tr>
		</table>
		
	</body>
</html>