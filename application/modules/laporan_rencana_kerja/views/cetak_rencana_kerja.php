<!DOCTYPE html>
<html>
	<head>
	  <title>LAPORAN RENCANA KERJA PRODUKSI</title>
	  
	  <style type="text/css">
		table tr.table-judul{
			background-color: #e69500;
			font-weight: bold;
			font-size: 7px;
			color: black;
		}
			
		table tr.table-baris111{
			background-color: #F0F0F0;
			font-size: 7px;
		}

		table tr.table-baris111-bold{
			background-color: #F0F0F0;
			font-size: 7px;
			font-weight: bold;
		}
			
		table tr.table-baris112{
			font-size: 7px;
			background-color: #E8E8E8;
		}
			
		table tr.table-total{
			background-color: #cccccc;
			font-weight: bold;
			font-size: 7px;
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
			$nilai_jual_all_februari23 = $nilai_jual_125_februari23 + $nilai_jual_225_februari23 + $nilai_jual_250_februari23 + $nilai_jual_250_18_februari23;

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
			$nilai_jual_all_maret23 = $nilai_jual_125_maret23 + $nilai_jual_225_maret23 + $nilai_jual_250_maret23 + $nilai_jual_250_18_maret23;

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
			$nilai_jual_all_april23 = $nilai_jual_125_april23 + $nilai_jual_225_april23 + $nilai_jual_250_april23 + $nilai_jual_250_18_april23;

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
			$nilai_jual_all_mei23 = $nilai_jual_125_mei23 + $nilai_jual_225_mei23 + $nilai_jual_250_mei23 + $nilai_jual_250_18_mei23;

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
			$nilai_jual_all_juni23 = $nilai_jual_125_juni23 + $nilai_jual_225_juni23 + $nilai_jual_250_juni23 + $nilai_jual_250_18_juni23;

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
			$nilai_jual_all_juli23 = $nilai_jual_125_juli23 + $nilai_jual_225_juli23 + $nilai_jual_250_juli23 + $nilai_jual_250_18_juli23;
			?>

			
			<?php
			
			//JAN23
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

			//FEB23
			$komposisi_125_feb23 = $this->db->select('(r.vol_produk_a * pk.presentase_a) as komposisi_semen_125, (vol_produk_a * pk.presentase_b) as komposisi_pasir_125, (vol_produk_a * pk.presentase_c) as komposisi_batu1020_125, (vol_produk_a * pk.presentase_d) as komposisi_batu2030_125')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_125 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_125_feb23 = 0;
			$total_volume_pasir_125_feb23 = 0;
			$total_volume_batu1020_125_feb23 = 0;
			$total_volume_batu2030_125_feb23 = 0;

			foreach ($komposisi_125_feb23 as $x){
				$total_volume_semen_125_feb23 = $x['komposisi_semen_125'];
				$total_volume_pasir_125_feb23 = $x['komposisi_pasir_125'];
				$total_volume_batu1020_125_feb23 = $x['komposisi_batu1020_125'];
				$total_volume_batu2030_125_feb23 = $x['komposisi_batu2030_125'];
			}

			$komposisi_225_feb23 = $this->db->select('(r.vol_produk_b * pk.presentase_a) as komposisi_semen_225, (vol_produk_b * pk.presentase_b) as komposisi_pasir_225, (vol_produk_b * pk.presentase_c) as komposisi_batu1020_225, (vol_produk_b * pk.presentase_d) as komposisi_batu2030_225')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_225 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_225_feb23 = 0;
			$total_volume_pasir_225_feb23 = 0;
			$total_volume_batu1020_225_feb23 = 0;
			$total_volume_batu2030_225_feb23 = 0;

			foreach ($komposisi_225_feb23 as $x){
				$total_volume_semen_225_feb23 = $x['komposisi_semen_225'];
				$total_volume_pasir_225_feb23 = $x['komposisi_pasir_225'];
				$total_volume_batu1020_225_feb23 = $x['komposisi_batu1020_225'];
				$total_volume_batu2030_225_feb23 = $x['komposisi_batu2030_225'];
			}

			$komposisi_250_feb23 = $this->db->select('(r.vol_produk_c * pk.presentase_a) as komposisi_semen_250, (vol_produk_c * pk.presentase_b) as komposisi_pasir_250, (vol_produk_c * pk.presentase_c) as komposisi_batu1020_250, (vol_produk_c * pk.presentase_d) as komposisi_batu2030_250')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_250_feb23 = 0;
			$total_volume_pasir_250_feb23 = 0;
			$total_volume_batu1020_250_feb23 = 0;
			$total_volume_batu2030_250_feb23 = 0;

			foreach ($komposisi_250_feb23 as $x){
				$total_volume_semen_250_feb23 = $x['komposisi_semen_250'];
				$total_volume_pasir_250_feb23 = $x['komposisi_pasir_250'];
				$total_volume_batu1020_250_feb23 = $x['komposisi_batu1020_250'];
				$total_volume_batu2030_250_feb23 = $x['komposisi_batu2030_250'];
			}

			$komposisi_250_2_feb23 = $this->db->select('(r.vol_produk_d * pk.presentase_a) as komposisi_semen_250_2, (vol_produk_d * pk.presentase_b) as komposisi_pasir_250_2, (vol_produk_d * pk.presentase_c) as komposisi_batu1020_250_2, (vol_produk_d * pk.presentase_d) as komposisi_batu2030_250_2')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250_2 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_250_2_feb23 = 0;
			$total_volume_pasir_250_2_feb23 = 0;
			$total_volume_batu1020_250_2_feb23 = 0;
			$total_volume_batu2030_250_2_feb23 = 0;

			foreach ($komposisi_250_2_feb23 as $x){
				$total_volume_semen_250_2_feb23 = $x['komposisi_semen_250_2'];
				$total_volume_pasir_250_2_feb23 = $x['komposisi_pasir_250_2'];
				$total_volume_batu1020_250_2_feb23 = $x['komposisi_batu1020_250_2'];
				$total_volume_batu2030_250_2_feb23 = $x['komposisi_batu2030_250_2'];
			}

			$total_volume_semen_feb23 = $total_volume_semen_125_feb23 + $total_volume_semen_225_feb23 + $total_volume_semen_250_feb23 + $total_volume_semen_250_2_feb23;
			$total_volume_pasir_feb23 = $total_volume_pasir_125_feb23 + $total_volume_pasir_225_feb23 + $total_volume_pasir_250_feb23 + $total_volume_pasir_250_2_feb23;
			$total_volume_batu1020_feb23 = $total_volume_batu1020_125_feb23 + $total_volume_batu1020_225_feb23 + $total_volume_batu1020_250_feb23 + $total_volume_batu1020_250_2_feb23;
			$total_volume_batu2030_feb23 = $total_volume_batu2030_125_feb23 + $total_volume_batu2030_225_feb23 + $total_volume_batu2030_250_feb23 + $total_volume_batu2030_250_2_feb23;

			//MAR23
			$komposisi_125_mar23 = $this->db->select('(r.vol_produk_a * pk.presentase_a) as komposisi_semen_125, (vol_produk_a * pk.presentase_b) as komposisi_pasir_125, (vol_produk_a * pk.presentase_c) as komposisi_batu1020_125, (vol_produk_a * pk.presentase_d) as komposisi_batu2030_125')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_125 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_125_mar23 = 0;
			$total_volume_pasir_125_mar23 = 0;
			$total_volume_batu1020_125_mar23 = 0;
			$total_volume_batu2030_125_mar23 = 0;

			foreach ($komposisi_125_mar23 as $x){
				$total_volume_semen_125_mar23 = $x['komposisi_semen_125'];
				$total_volume_pasir_125_mar23 = $x['komposisi_pasir_125'];
				$total_volume_batu1020_125_mar23 = $x['komposisi_batu1020_125'];
				$total_volume_batu2030_125_mar23 = $x['komposisi_batu2030_125'];
			}

			$komposisi_225_mar23 = $this->db->select('(r.vol_produk_b * pk.presentase_a) as komposisi_semen_225, (vol_produk_b * pk.presentase_b) as komposisi_pasir_225, (vol_produk_b * pk.presentase_c) as komposisi_batu1020_225, (vol_produk_b * pk.presentase_d) as komposisi_batu2030_225')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_225 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_225_mar23 = 0;
			$total_volume_pasir_225_mar23 = 0;
			$total_volume_batu1020_225_mar23 = 0;
			$total_volume_batu2030_225_mar23 = 0;

			foreach ($komposisi_225_mar23 as $x){
				$total_volume_semen_225_mar23 = $x['komposisi_semen_225'];
				$total_volume_pasir_225_mar23 = $x['komposisi_pasir_225'];
				$total_volume_batu1020_225_mar23 = $x['komposisi_batu1020_225'];
				$total_volume_batu2030_225_mar23 = $x['komposisi_batu2030_225'];
			}

			$komposisi_250_mar23 = $this->db->select('(r.vol_produk_c * pk.presentase_a) as komposisi_semen_250, (vol_produk_c * pk.presentase_b) as komposisi_pasir_250, (vol_produk_c * pk.presentase_c) as komposisi_batu1020_250, (vol_produk_c * pk.presentase_d) as komposisi_batu2030_250')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_250_mar23 = 0;
			$total_volume_pasir_250_mar23 = 0;
			$total_volume_batu1020_250_mar23 = 0;
			$total_volume_batu2030_250_mar23 = 0;

			foreach ($komposisi_250_mar23 as $x){
				$total_volume_semen_250_mar23 = $x['komposisi_semen_250'];
				$total_volume_pasir_250_mar23 = $x['komposisi_pasir_250'];
				$total_volume_batu1020_250_mar23 = $x['komposisi_batu1020_250'];
				$total_volume_batu2030_250_mar23 = $x['komposisi_batu2030_250'];
			}

			$komposisi_250_2_mar23 = $this->db->select('(r.vol_produk_d * pk.presentase_a) as komposisi_semen_250_2, (vol_produk_d * pk.presentase_b) as komposisi_pasir_250_2, (vol_produk_d * pk.presentase_c) as komposisi_batu1020_250_2, (vol_produk_d * pk.presentase_d) as komposisi_batu2030_250_2')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250_2 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_250_2_mar23 = 0;
			$total_volume_pasir_250_2_mar23 = 0;
			$total_volume_batu1020_250_2_mar23 = 0;
			$total_volume_batu2030_250_2_mar23 = 0;

			foreach ($komposisi_250_2_mar23 as $x){
				$total_volume_semen_250_2_mar23 = $x['komposisi_semen_250_2'];
				$total_volume_pasir_250_2_mar23 = $x['komposisi_pasir_250_2'];
				$total_volume_batu1020_250_2_mar23 = $x['komposisi_batu1020_250_2'];
				$total_volume_batu2030_250_2_mar23 = $x['komposisi_batu2030_250_2'];
			}

			$total_volume_semen_mar23 = $total_volume_semen_125_mar23 + $total_volume_semen_225_mar23 + $total_volume_semen_250_mar23 + $total_volume_semen_250_2_mar23;
			$total_volume_pasir_mar23 = $total_volume_pasir_125_mar23 + $total_volume_pasir_225_mar23 + $total_volume_pasir_250_mar23 + $total_volume_pasir_250_2_mar23;
			$total_volume_batu1020_mar23 = $total_volume_batu1020_125_mar23 + $total_volume_batu1020_225_mar23 + $total_volume_batu1020_250_mar23 + $total_volume_batu1020_250_2_mar23;
			$total_volume_batu2030_mar23 = $total_volume_batu2030_125_mar23 + $total_volume_batu2030_225_mar23 + $total_volume_batu2030_250_mar23 + $total_volume_batu2030_250_2_mar23;

			//APR23
			$komposisi_125_apr23 = $this->db->select('(r.vol_produk_a * pk.presentase_a) as komposisi_semen_125, (vol_produk_a * pk.presentase_b) as komposisi_pasir_125, (vol_produk_a * pk.presentase_c) as komposisi_batu1020_125, (vol_produk_a * pk.presentase_d) as komposisi_batu2030_125')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_125 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_125_apr23 = 0;
			$total_volume_pasir_125_apr23 = 0;
			$total_volume_batu1020_125_apr23 = 0;
			$total_volume_batu2030_125_apr23 = 0;

			foreach ($komposisi_125_apr23 as $x){
				$total_volume_semen_125_apr23 = $x['komposisi_semen_125'];
				$total_volume_pasir_125_apr23 = $x['komposisi_pasir_125'];
				$total_volume_batu1020_125_apr23 = $x['komposisi_batu1020_125'];
				$total_volume_batu2030_125_apr23 = $x['komposisi_batu2030_125'];
			}

			$komposisi_225_apr23 = $this->db->select('(r.vol_produk_b * pk.presentase_a) as komposisi_semen_225, (vol_produk_b * pk.presentase_b) as komposisi_pasir_225, (vol_produk_b * pk.presentase_c) as komposisi_batu1020_225, (vol_produk_b * pk.presentase_d) as komposisi_batu2030_225')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_225 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_225_apr23 = 0;
			$total_volume_pasir_225_apr23 = 0;
			$total_volume_batu1020_225_apr23 = 0;
			$total_volume_batu2030_225_apr23 = 0;

			foreach ($komposisi_225_apr23 as $x){
				$total_volume_semen_225_apr23 = $x['komposisi_semen_225'];
				$total_volume_pasir_225_apr23 = $x['komposisi_pasir_225'];
				$total_volume_batu1020_225_apr23 = $x['komposisi_batu1020_225'];
				$total_volume_batu2030_225_apr23 = $x['komposisi_batu2030_225'];
			}

			$komposisi_250_apr23 = $this->db->select('(r.vol_produk_c * pk.presentase_a) as komposisi_semen_250, (vol_produk_c * pk.presentase_b) as komposisi_pasir_250, (vol_produk_c * pk.presentase_c) as komposisi_batu1020_250, (vol_produk_c * pk.presentase_d) as komposisi_batu2030_250')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_250_apr23 = 0;
			$total_volume_pasir_250_apr23 = 0;
			$total_volume_batu1020_250_apr23 = 0;
			$total_volume_batu2030_250_apr23 = 0;

			foreach ($komposisi_250_apr23 as $x){
				$total_volume_semen_250_apr23 = $x['komposisi_semen_250'];
				$total_volume_pasir_250_apr23 = $x['komposisi_pasir_250'];
				$total_volume_batu1020_250_apr23 = $x['komposisi_batu1020_250'];
				$total_volume_batu2030_250_apr23 = $x['komposisi_batu2030_250'];
			}

			$komposisi_250_2_apr23 = $this->db->select('(r.vol_produk_d * pk.presentase_a) as komposisi_semen_250_2, (vol_produk_d * pk.presentase_b) as komposisi_pasir_250_2, (vol_produk_d * pk.presentase_c) as komposisi_batu1020_250_2, (vol_produk_d * pk.presentase_d) as komposisi_batu2030_250_2')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250_2 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_250_2_apr23 = 0;
			$total_volume_pasir_250_2_apr23 = 0;
			$total_volume_batu1020_250_2_apr23 = 0;
			$total_volume_batu2030_250_2_apr23 = 0;

			foreach ($komposisi_250_2_apr23 as $x){
				$total_volume_semen_250_2_apr23 = $x['komposisi_semen_250_2'];
				$total_volume_pasir_250_2_apr23 = $x['komposisi_pasir_250_2'];
				$total_volume_batu1020_250_2_apr23 = $x['komposisi_batu1020_250_2'];
				$total_volume_batu2030_250_2_apr23 = $x['komposisi_batu2030_250_2'];
			}

			$total_volume_semen_apr23 = $total_volume_semen_125_apr23 + $total_volume_semen_225_apr23 + $total_volume_semen_250_apr23 + $total_volume_semen_250_2_apr23;
			$total_volume_pasir_apr23 = $total_volume_pasir_125_apr23 + $total_volume_pasir_225_apr23 + $total_volume_pasir_250_apr23 + $total_volume_pasir_250_2_apr23;
			$total_volume_batu1020_apr23 = $total_volume_batu1020_125_apr23 + $total_volume_batu1020_225_apr23 + $total_volume_batu1020_250_apr23 + $total_volume_batu1020_250_2_apr23;
			$total_volume_batu2030_apr23 = $total_volume_batu2030_125_apr23 + $total_volume_batu2030_225_apr23 + $total_volume_batu2030_250_apr23 + $total_volume_batu2030_250_2_apr23;

			//MEI23
			$komposisi_125_mei23 = $this->db->select('(r.vol_produk_a * pk.presentase_a) as komposisi_semen_125, (vol_produk_a * pk.presentase_b) as komposisi_pasir_125, (vol_produk_a * pk.presentase_c) as komposisi_batu1020_125, (vol_produk_a * pk.presentase_d) as komposisi_batu2030_125')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_125 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_125_mei23 = 0;
			$total_volume_pasir_125_mei23 = 0;
			$total_volume_batu1020_125_mei23 = 0;
			$total_volume_batu2030_125_mei23 = 0;

			foreach ($komposisi_125_mei23 as $x){
				$total_volume_semen_125_mei23 = $x['komposisi_semen_125'];
				$total_volume_pasir_125_mei23 = $x['komposisi_pasir_125'];
				$total_volume_batu1020_125_mei23 = $x['komposisi_batu1020_125'];
				$total_volume_batu2030_125_mei23 = $x['komposisi_batu2030_125'];
			}

			$komposisi_225_mei23 = $this->db->select('(r.vol_produk_b * pk.presentase_a) as komposisi_semen_225, (vol_produk_b * pk.presentase_b) as komposisi_pasir_225, (vol_produk_b * pk.presentase_c) as komposisi_batu1020_225, (vol_produk_b * pk.presentase_d) as komposisi_batu2030_225')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_225 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_225_mei23 = 0;
			$total_volume_pasir_225_mei23 = 0;
			$total_volume_batu1020_225_mei23 = 0;
			$total_volume_batu2030_225_mei23 = 0;

			foreach ($komposisi_225_mei23 as $x){
				$total_volume_semen_225_mei23 = $x['komposisi_semen_225'];
				$total_volume_pasir_225_mei23 = $x['komposisi_pasir_225'];
				$total_volume_batu1020_225_mei23 = $x['komposisi_batu1020_225'];
				$total_volume_batu2030_225_mei23 = $x['komposisi_batu2030_225'];
			}

			$komposisi_250_mei23 = $this->db->select('(r.vol_produk_c * pk.presentase_a) as komposisi_semen_250, (vol_produk_c * pk.presentase_b) as komposisi_pasir_250, (vol_produk_c * pk.presentase_c) as komposisi_batu1020_250, (vol_produk_c * pk.presentase_d) as komposisi_batu2030_250')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_250_mei23 = 0;
			$total_volume_pasir_250_mei23 = 0;
			$total_volume_batu1020_250_mei23 = 0;
			$total_volume_batu2030_250_mei23 = 0;

			foreach ($komposisi_250_mei23 as $x){
				$total_volume_semen_250_mei23 = $x['komposisi_semen_250'];
				$total_volume_pasir_250_mei23 = $x['komposisi_pasir_250'];
				$total_volume_batu1020_250_mei23 = $x['komposisi_batu1020_250'];
				$total_volume_batu2030_250_mei23 = $x['komposisi_batu2030_250'];
			}

			$komposisi_250_2_mei23 = $this->db->select('(r.vol_produk_d * pk.presentase_a) as komposisi_semen_250_2, (vol_produk_d * pk.presentase_b) as komposisi_pasir_250_2, (vol_produk_d * pk.presentase_c) as komposisi_batu1020_250_2, (vol_produk_d * pk.presentase_d) as komposisi_batu2030_250_2')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250_2 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_250_2_mei23 = 0;
			$total_volume_pasir_250_2_mei23 = 0;
			$total_volume_batu1020_250_2_mei23 = 0;
			$total_volume_batu2030_250_2_mei23 = 0;

			foreach ($komposisi_250_2_mei23 as $x){
				$total_volume_semen_250_2_mei23 = $x['komposisi_semen_250_2'];
				$total_volume_pasir_250_2_mei23 = $x['komposisi_pasir_250_2'];
				$total_volume_batu1020_250_2_mei23 = $x['komposisi_batu1020_250_2'];
				$total_volume_batu2030_250_2_mei23 = $x['komposisi_batu2030_250_2'];
			}

			$total_volume_semen_mei23 = $total_volume_semen_125_mei23 + $total_volume_semen_225_mei23 + $total_volume_semen_250_mei23 + $total_volume_semen_250_2_mei23;
			$total_volume_pasir_mei23 = $total_volume_pasir_125_mei23 + $total_volume_pasir_225_mei23 + $total_volume_pasir_250_mei23 + $total_volume_pasir_250_2_mei23;
			$total_volume_batu1020_mei23 = $total_volume_batu1020_125_mei23 + $total_volume_batu1020_225_mei23 + $total_volume_batu1020_250_mei23 + $total_volume_batu1020_250_2_mei23;
			$total_volume_batu2030_mei23 = $total_volume_batu2030_125_mei23 + $total_volume_batu2030_225_mei23 + $total_volume_batu2030_250_mei23 + $total_volume_batu2030_250_2_mei23;

			//JUN23
			$komposisi_125_jun23 = $this->db->select('(r.vol_produk_a * pk.presentase_a) as komposisi_semen_125, (vol_produk_a * pk.presentase_b) as komposisi_pasir_125, (vol_produk_a * pk.presentase_c) as komposisi_batu1020_125, (vol_produk_a * pk.presentase_d) as komposisi_batu2030_125')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_125 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_125_jun23 = 0;
			$total_volume_pasir_125_jun23 = 0;
			$total_volume_batu1020_125_jun23 = 0;
			$total_volume_batu2030_125_jun23 = 0;

			foreach ($komposisi_125_jun23 as $x){
				$total_volume_semen_125_jun23 = $x['komposisi_semen_125'];
				$total_volume_pasir_125_jun23 = $x['komposisi_pasir_125'];
				$total_volume_batu1020_125_jun23 = $x['komposisi_batu1020_125'];
				$total_volume_batu2030_125_jun23 = $x['komposisi_batu2030_125'];
			}

			$komposisi_225_jun23 = $this->db->select('(r.vol_produk_b * pk.presentase_a) as komposisi_semen_225, (vol_produk_b * pk.presentase_b) as komposisi_pasir_225, (vol_produk_b * pk.presentase_c) as komposisi_batu1020_225, (vol_produk_b * pk.presentase_d) as komposisi_batu2030_225')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_225 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_225_jun23 = 0;
			$total_volume_pasir_225_jun23 = 0;
			$total_volume_batu1020_225_jun23 = 0;
			$total_volume_batu2030_225_jun23 = 0;

			foreach ($komposisi_225_jun23 as $x){
				$total_volume_semen_225_jun23 = $x['komposisi_semen_225'];
				$total_volume_pasir_225_jun23 = $x['komposisi_pasir_225'];
				$total_volume_batu1020_225_jun23 = $x['komposisi_batu1020_225'];
				$total_volume_batu2030_225_jun23 = $x['komposisi_batu2030_225'];
			}

			$komposisi_250_jun23 = $this->db->select('(r.vol_produk_c * pk.presentase_a) as komposisi_semen_250, (vol_produk_c * pk.presentase_b) as komposisi_pasir_250, (vol_produk_c * pk.presentase_c) as komposisi_batu1020_250, (vol_produk_c * pk.presentase_d) as komposisi_batu2030_250')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_250_jun23 = 0;
			$total_volume_pasir_250_jun23 = 0;
			$total_volume_batu1020_250_jun23 = 0;
			$total_volume_batu2030_250_jun23 = 0;

			foreach ($komposisi_250_jun23 as $x){
				$total_volume_semen_250_jun23 = $x['komposisi_semen_250'];
				$total_volume_pasir_250_jun23 = $x['komposisi_pasir_250'];
				$total_volume_batu1020_250_jun23 = $x['komposisi_batu1020_250'];
				$total_volume_batu2030_250_jun23 = $x['komposisi_batu2030_250'];
			}

			$komposisi_250_2_jun23 = $this->db->select('(r.vol_produk_d * pk.presentase_a) as komposisi_semen_250_2, (vol_produk_d * pk.presentase_b) as komposisi_pasir_250_2, (vol_produk_d * pk.presentase_c) as komposisi_batu1020_250_2, (vol_produk_d * pk.presentase_d) as komposisi_batu2030_250_2')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250_2 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_250_2_jun23 = 0;
			$total_volume_pasir_250_2_jun23 = 0;
			$total_volume_batu1020_250_2_jun23 = 0;
			$total_volume_batu2030_250_2_jun23 = 0;

			foreach ($komposisi_250_2_jun23 as $x){
				$total_volume_semen_250_2_jun23 = $x['komposisi_semen_250_2'];
				$total_volume_pasir_250_2_jun23 = $x['komposisi_pasir_250_2'];
				$total_volume_batu1020_250_2_jun23 = $x['komposisi_batu1020_250_2'];
				$total_volume_batu2030_250_2_jun23 = $x['komposisi_batu2030_250_2'];
			}

			$total_volume_semen_jun23 = $total_volume_semen_125_jun23 + $total_volume_semen_225_jun23 + $total_volume_semen_250_jun23 + $total_volume_semen_250_2_jun23;
			$total_volume_pasir_jun23 = $total_volume_pasir_125_jun23 + $total_volume_pasir_225_jun23 + $total_volume_pasir_250_jun23 + $total_volume_pasir_250_2_jun23;
			$total_volume_batu1020_jun23 = $total_volume_batu1020_125_jun23 + $total_volume_batu1020_225_jun23 + $total_volume_batu1020_250_jun23 + $total_volume_batu1020_250_2_jun23;
			$total_volume_batu2030_jun23 = $total_volume_batu2030_125_jun23 + $total_volume_batu2030_225_jun23 + $total_volume_batu2030_250_jun23 + $total_volume_batu2030_250_2_jun23;

			//JUL23
			$komposisi_125_jul23 = $this->db->select('(r.vol_produk_a * pk.presentase_a) as komposisi_semen_125, (vol_produk_a * pk.presentase_b) as komposisi_pasir_125, (vol_produk_a * pk.presentase_c) as komposisi_batu1020_125, (vol_produk_a * pk.presentase_d) as komposisi_batu2030_125')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_125 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_125_jul23 = 0;
			$total_volume_pasir_125_jul23 = 0;
			$total_volume_batu1020_125_jul23 = 0;
			$total_volume_batu2030_125_jul23 = 0;

			foreach ($komposisi_125_jul23 as $x){
				$total_volume_semen_125_jul23 = $x['komposisi_semen_125'];
				$total_volume_pasir_125_jul23 = $x['komposisi_pasir_125'];
				$total_volume_batu1020_125_jul23 = $x['komposisi_batu1020_125'];
				$total_volume_batu2030_125_jul23 = $x['komposisi_batu2030_125'];
			}

			$komposisi_225_jul23 = $this->db->select('(r.vol_produk_b * pk.presentase_a) as komposisi_semen_225, (vol_produk_b * pk.presentase_b) as komposisi_pasir_225, (vol_produk_b * pk.presentase_c) as komposisi_batu1020_225, (vol_produk_b * pk.presentase_d) as komposisi_batu2030_225')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_225 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_225_jul23 = 0;
			$total_volume_pasir_225_jul23 = 0;
			$total_volume_batu1020_225_jul23 = 0;
			$total_volume_batu2030_225_jul23 = 0;

			foreach ($komposisi_225_jul23 as $x){
				$total_volume_semen_225_jul23 = $x['komposisi_semen_225'];
				$total_volume_pasir_225_jul23 = $x['komposisi_pasir_225'];
				$total_volume_batu1020_225_jul23 = $x['komposisi_batu1020_225'];
				$total_volume_batu2030_225_jul23 = $x['komposisi_batu2030_225'];
			}

			$komposisi_250_jul23 = $this->db->select('(r.vol_produk_c * pk.presentase_a) as komposisi_semen_250, (vol_produk_c * pk.presentase_b) as komposisi_pasir_250, (vol_produk_c * pk.presentase_c) as komposisi_batu1020_250, (vol_produk_c * pk.presentase_d) as komposisi_batu2030_250')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_250_jul23 = 0;
			$total_volume_pasir_250_jul23 = 0;
			$total_volume_batu1020_250_jul23 = 0;
			$total_volume_batu2030_250_jul23 = 0;

			foreach ($komposisi_250_jul23 as $x){
				$total_volume_semen_250_jul23 = $x['komposisi_semen_250'];
				$total_volume_pasir_250_jul23 = $x['komposisi_pasir_250'];
				$total_volume_batu1020_250_jul23 = $x['komposisi_batu1020_250'];
				$total_volume_batu2030_250_jul23 = $x['komposisi_batu2030_250'];
			}

			$komposisi_250_2_jul23 = $this->db->select('(r.vol_produk_d * pk.presentase_a) as komposisi_semen_250_2, (vol_produk_d * pk.presentase_b) as komposisi_pasir_250_2, (vol_produk_d * pk.presentase_c) as komposisi_batu1020_250_2, (vol_produk_d * pk.presentase_d) as komposisi_batu2030_250_2')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250_2 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_250_2_jul23 = 0;
			$total_volume_pasir_250_2_jul23 = 0;
			$total_volume_batu1020_250_2_jul23 = 0;
			$total_volume_batu2030_250_2_jul23 = 0;

			foreach ($komposisi_250_2_jul23 as $x){
				$total_volume_semen_250_2_jul23 = $x['komposisi_semen_250_2'];
				$total_volume_pasir_250_2_jul23 = $x['komposisi_pasir_250_2'];
				$total_volume_batu1020_250_2_jul23 = $x['komposisi_batu1020_250_2'];
				$total_volume_batu2030_250_2_jul23 = $x['komposisi_batu2030_250_2'];
			}

			$total_volume_semen_jul23 = $total_volume_semen_125_jul23 + $total_volume_semen_225_jul23 + $total_volume_semen_250_jul23 + $total_volume_semen_250_2_jul23;
			$total_volume_pasir_jul23 = $total_volume_pasir_125_jul23 + $total_volume_pasir_225_jul23 + $total_volume_pasir_250_jul23 + $total_volume_pasir_250_2_jul23;
			$total_volume_batu1020_jul23 = $total_volume_batu1020_125_jul23 + $total_volume_batu1020_225_jul23 + $total_volume_batu1020_250_jul23 + $total_volume_batu1020_250_2_jul23;
			$total_volume_batu2030_jul23 = $total_volume_batu2030_125_jul23 + $total_volume_batu2030_225_jul23 + $total_volume_batu2030_250_jul23 + $total_volume_batu2030_250_2_jul23;
			
			//SOLAR
			$rap_solar = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->order_by('rap.id','desc')->limit(1)
			->get()->row_array();
			
			$total_volume_solar_jan23 = $total_januari23_volume * $rap_solar['vol_bbm_solar'];
			$total_volume_solar_feb23 = $total_februari23_volume * $rap_solar['vol_bbm_solar'];
			$total_volume_solar_mar23 = $total_maret23_volume * $rap_solar['vol_bbm_solar'];
			$total_volume_solar_apr23 = $total_april23_volume * $rap_solar['vol_bbm_solar'];
			$total_volume_solar_mei23 = $total_mei23_volume * $rap_solar['vol_bbm_solar'];
			$total_volume_solar_jun23 = $total_juni23_volume * $rap_solar['vol_bbm_solar'];
			$total_volume_solar_jul23 = $total_juli23_volume * $rap_solar['vol_bbm_solar'];
			?>

			<tr class="table-judul">
				<th width="5%" align="center">NO.</th>
				<th width="15%" align = "center">URAIAN</th>
				<th width="10%" align = "center">SATUAN</th>
				<th width="10%" align = "center">JANUARI 2023</th>
				<th width="10%" align = "center">FEBRUARI 2023</th>
				<th width="10%" align = "center">MARET 2023</th>
				<th width="10%" align = "center">APRIL 2023</th>
				<th width="10%" align = "center">MEI 2023</th>
				<th width="10%" align = "center">JUNI 2023</th>
				<th width="10%" align = "center">JULI 2023</th>
	        </tr>
			<tr class="table-baris111">
				<th align = "center">1</th>
				<th align = "left">Beton K 125 (10±2)</th>
				<th align = "center">M3</th>
				<th align = "right"><?php echo number_format($volume_januari23_produk_a,2,',','.');?></th>
				<th align = "right"><?php echo number_format($volume_februari23_produk_a,2,',','.');?></th>
				<th align = "right"><?php echo number_format($volume_maret23_produk_a,2,',','.');?></th>
				<th align = "right"><?php echo number_format($volume_april23_produk_a,2,',','.');?></th>
				<th align = "right"><?php echo number_format($volume_mei23_produk_a,2,',','.');?></th>
				<th align = "right"><?php echo number_format($volume_juni23_produk_a,2,',','.');?></th>
				<th align = "right"><?php echo number_format($volume_juli23_produk_a,2,',','.');?></th>
			</tr>
			<tr class="table-baris111">
				<th align = "center">2</th>
				<th align = "left">Beton K 225 (10±2)</th>
				<th align = "center">M3</th>
				<th align = "right"><?php echo number_format($volume_januari23_produk_b,2,',','.');?></th>
				<th align = "right"><?php echo number_format($volume_februari23_produk_b,2,',','.');?></th>
				<th align = "right"><?php echo number_format($volume_maret23_produk_b,2,',','.');?></th>
				<th align = "right"><?php echo number_format($volume_april23_produk_b,2,',','.');?></th>
				<th align = "right"><?php echo number_format($volume_mei23_produk_b,2,',','.');?></th>
				<th align = "right"><?php echo number_format($volume_juni23_produk_b,2,',','.');?></th>
				<th align = "right"><?php echo number_format($volume_juli23_produk_b,2,',','.');?></th>
			</tr>
			<tr class="table-baris111">
				<th align = "center">3</th>
				<th align = "left">Beton K 250 (10±2)</th>
				<th align = "center">M3</th>
				<th align = "right"><?php echo number_format($volume_januari23_produk_c,2,',','.');?></th>
				<th align = "right"><?php echo number_format($volume_februari23_produk_c,2,',','.');?></th>
				<th align = "right"><?php echo number_format($volume_maret23_produk_c,2,',','.');?></th>
				<th align = "right"><?php echo number_format($volume_april23_produk_c,2,',','.');?></th>
				<th align = "right"><?php echo number_format($volume_mei23_produk_c,2,',','.');?></th>
				<th align = "right"><?php echo number_format($volume_juni23_produk_c,2,',','.');?></th>
				<th align = "right"><?php echo number_format($volume_juli23_produk_c,2,',','.');?></th>
			</tr>
			<tr class="table-baris111">
				<th align = "center">4</th>
				<th align = "left">Beton K 250 (18±2)</th>
				<th align = "center">M3</th>
				<th align = "right"><?php echo number_format($volume_januari23_produk_d,2,',','.');?></th>
				<th align = "right"><?php echo number_format($volume_februari23_produk_d,2,',','.');?></th>
				<th align = "right"><?php echo number_format($volume_maret23_produk_d,2,',','.');?></th>
				<th align = "right"><?php echo number_format($volume_april23_produk_d,2,',','.');?></th>
				<th align = "right"><?php echo number_format($volume_mei23_produk_d,2,',','.');?></th>
				<th align = "right"><?php echo number_format($volume_juni23_produk_d,2,',','.');?></th>
				<th align = "right"><?php echo number_format($volume_juli23_produk_d,2,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align = "center" colspan="3">TOTAL VOLUME</th>
				<th align = "right"><?php echo number_format($total_januari23_volume,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_februari23_volume,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_maret23_volume,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_april23_volume,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_mei23_volume,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_juni23_volume,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_juli23_volume,2,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align = "center" colspan="3">PENDAPATAN USAHA</th>
				<th align = "right"><?php echo number_format($nilai_jual_all_januari23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($nilai_jual_all_februari23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($nilai_jual_all_maret23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($nilai_jual_all_april23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($nilai_jual_all_mei23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($nilai_jual_all_juni23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($nilai_jual_all_juli23,2,',','.');?></th>
			</tr>
			<tr class="table-judul">
				<th width="5%" align = "center" style="vertical-align:middle">NO.</th>
				<th align = "center" style="vertical-align:middle">KEBUTUHAN BAHAN</th>
				<th align = "center" style="vertical-align:middle">SATUAN</th>
				<th align = "center" style="vertical-align:middle">PENGADAAN</th>
				<th align = "center" style="vertical-align:middle">PENGADAAN</th>
				<th align = "center" style="vertical-align:middle">PENGADAAN</th>
				<th align = "center" style="vertical-align:middle">PENGADAAN</th>
				<th align = "center" style="vertical-align:middle">PENGADAAN</th>
				<th align = "center" style="vertical-align:middle">PENGADAAN</th>
				<th align = "center" style="vertical-align:middle">PENGADAAN</th>
	        </tr>
			<tr class="table-baris111">
				<th align = "left" colspan="2">Semen</th>
				<th align = "center">Ton</th>
				<th align = "right"><?php echo number_format($total_volume_semen_jan23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_semen_feb23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_semen_mar23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_semen_apr23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_semen_mei23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_semen_jun23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_semen_jul23,2,',','.');?></th>
			</tr>
			<tr class="table-baris111">
				<th align = "left" colspan="2">Pasir</th>
				<th align = "center">M3</th>
				<th align = "right"><?php echo number_format($total_volume_pasir_jan23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_pasir_feb23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_pasir_mar23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_pasir_apr23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_pasir_mei23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_pasir_jun23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_pasir_jul23,2,',','.');?></th>
			</tr>
			<tr class="table-baris111">
				<th align = "left" colspan="2">Batu Split 10-20</th>
				<th align = "center">M3</th>
				<th align = "right"><?php echo number_format($total_volume_batu1020_jan23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_batu1020_feb23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_batu1020_mar23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_batu1020_apr23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_batu1020_mei23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_batu1020_jun23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_batu1020_jul23,2,',','.');?></th>
			</tr>
			<tr class="table-baris111">
				<th align = "left" colspan="2">Batu Split 20-30</th>
				<th align = "center">M3</th>
				<th align = "right"><?php echo number_format($total_volume_batu2030_jan23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_batu2030_feb23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_batu2030_mar23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_batu2030_apr23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_batu2030_mei23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_batu2030_jun23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_batu2030_jul23,2,',','.');?></th>
			</tr>
			<tr class="table-baris111">
				<th align = "left" colspan="2">Solar</th>
				<th align = "center">Liter</th>
				<th align = "right"><?php echo number_format($total_volume_solar_jan23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_solar_feb23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_solar_mar23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_solar_apr23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_solar_mei23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_solar_jun23,2,',','.');?></th>
				<th align = "right"><?php echo number_format($total_volume_solar_jul23,2,',','.');?></th>
			</tr>
		</table>
		
	</body>
</html>