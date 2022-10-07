<!DOCTYPE html>
<html>
	<head>
	  <title>LAPORAN RENCANA KERJA PRODUKSI</title>
	  
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
		<br />
		<br />
		<table width="98%" cellpadding="3">
			<tr>
				<td align="center"  width="100%">
					<div style="display: block;font-weight: bold;font-size: 12px;">LAPORAN RENCANA KERJA PRODUKSI</div>
					<div style="display: block;font-weight: bold;font-size: 11px;">DIVISI BETON  PROYEK BENDUNGAN TEMEF</div>
				    <div style="display: block;font-weight: bold;font-size: 11px;">PT. BIA BUMI JAYENDRA</div>
					<div style="display: block;font-weight: bold;font-size: 12px; text-transform: uppercase;">PERIODE : 2022</div>
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
			$date_agustus = date('2022-08-31');
			$penjualan_akumulasi_produk_a = $this->db->select('p.nama_produk, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('produk p', 'pp.product_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("(pp.date_production <= '$date_agustus')")
			->where("pp.status = 'PUBLISH'")
			->where("pp.product_id = 2")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.product_id")
			->order_by('p.nama_produk','asc')
			->get()->row_array();
			$volume_akumulasi_produk_a = $penjualan_akumulasi_produk_a['volume'];
			$nilai_akumulasi_produk_a = $penjualan_akumulasi_produk_a['price'];

			$penjualan_akumulasi_produk_b = $this->db->select('p.nama_produk, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('produk p', 'pp.product_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("(pp.date_production <= '$date_agustus')")
			->where("pp.status = 'PUBLISH'")
			->where("pp.product_id = 1")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.product_id")
			->order_by('p.nama_produk','asc')
			->get()->row_array();
			$volume_akumulasi_produk_b = $penjualan_akumulasi_produk_b['volume'];
			$nilai_akumulasi_produk_b = $penjualan_akumulasi_produk_b['price'];

			$penjualan_akumulasi_produk_c = $this->db->select('p.nama_produk, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('produk p', 'pp.product_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("(pp.date_production <= '$date_agustus')")
			->where("pp.status = 'PUBLISH'")
			->where("pp.product_id = 3")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.product_id")
			->order_by('p.nama_produk','asc')
			->get()->row_array();
			$volume_akumulasi_produk_c = $penjualan_akumulasi_produk_c['volume'];
			$nilai_akumulasi_produk_c = $penjualan_akumulasi_produk_c['price'];

			$penjualan_akumulasi_produk_d = $this->db->select('p.nama_produk, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('produk p', 'pp.product_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("(pp.date_production <= '$date_agustus')")
			->where("pp.status = 'PUBLISH'")
			->where("pp.product_id = 11")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.product_id")
			->order_by('p.nama_produk','asc')
			->get()->row_array();
			$volume_akumulasi_produk_d = $penjualan_akumulasi_produk_d['volume'];
			$nilai_akumulasi_produk_d = $penjualan_akumulasi_produk_d['price'];


			$total_akumulasi_volume = $volume_akumulasi_produk_a + $volume_akumulasi_produk_b + $volume_akumulasi_produk_c + $volume_akumulasi_produk_d;
			$total_akumulasi_nilai = $nilai_akumulasi_produk_a + $nilai_akumulasi_produk_b + $nilai_akumulasi_produk_c + $nilai_akumulasi_produk_d;
			?>

			<?php
			$date_oktober_awal = date('2022-10-01');
			$date_oktober_akhir = date('2022-10-31');
			$rencana_kerja_oktober = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_oktober_awal' and '$date_oktober_akhir'")
			->get()->row_array();
			$volume_oktober_produk_a = $rencana_kerja_oktober['vol_produk_a'];
			$volume_oktober_produk_b = $rencana_kerja_oktober['vol_produk_b'];
			$volume_oktober_produk_c = $rencana_kerja_oktober['vol_produk_c'];
			$volume_oktober_produk_d = $rencana_kerja_oktober['vol_produk_d'];


			$total_oktober_volume = $volume_oktober_produk_a + $volume_oktober_produk_b + $volume_oktober_produk_c + $volume_oktober_produk_d;
			
			//TOTAL PU
			//K125
			$komposisi_125 = $this->db->select('pk.*')
			->from('pmm_agregat pk')
			->where("pk.mutu_beton = 2")
			->where('pk.status','PUBLISH')
			->order_by('pk.date_agregat','desc')->limit(1)
			->get()->row_array();

			$komposisi_125_produk_a = $komposisi_125['presentase_a'];
			$komposisi_125_produk_b = $komposisi_125['presentase_b'];
			$komposisi_125_produk_c = $komposisi_125['presentase_c'];
			$komposisi_125_produk_d = $komposisi_125['presentase_d'];

			//K225
			$komposisi_225 = $this->db->select('pk.*')
			->from('pmm_agregat pk')
			->where("pk.mutu_beton = 1")
			->where('pk.status','PUBLISH')
			->order_by('pk.date_agregat','desc')->limit(1)
			->get()->row_array();

			$komposisi_225_produk_a = $komposisi_225['presentase_a'];
			$komposisi_225_produk_b = $komposisi_225['presentase_b'];
			$komposisi_225_produk_c = $komposisi_225['presentase_c'];
			$komposisi_225_produk_d = $komposisi_225['presentase_d'];

			//K250
			$komposisi_250 = $this->db->select('pk.*')
			->from('pmm_agregat pk')
			->where("pk.mutu_beton = 3")
			->where('pk.status','PUBLISH')
			->order_by('pk.date_agregat','desc')->limit(1)
			->get()->row_array();

			$komposisi_250_produk_a = $komposisi_250['presentase_a'];
			$komposisi_250_produk_b = $komposisi_250['presentase_b'];
			$komposisi_250_produk_c = $komposisi_250['presentase_c'];
			$komposisi_250_produk_d = $komposisi_250['presentase_d'];

			//K250_18
			$komposisi_250_18 = $this->db->select('pk.*')
			->from('pmm_agregat pk')
			->where("pk.mutu_beton = 11")
			->where('pk.status','PUBLISH')
			->order_by('pk.date_agregat','desc')->limit(1)
			->get()->row_array();

			$komposisi_250_18_produk_a = $komposisi_250_18['presentase_a'];
			$komposisi_250_18_produk_b = $komposisi_250_18['presentase_b'];
			$komposisi_250_18_produk_c = $komposisi_250_18['presentase_c'];
			$komposisi_250_18_produk_d = $komposisi_250_18['presentase_d'];

			$rencana_kerja = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_oktober_awal' and '$date_oktober_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_produk_a = $rencana_kerja['vol_produk_a'];
			$volume_rencana_kerja_produk_b = $rencana_kerja['vol_produk_b'];
			$volume_rencana_kerja_produk_c = $rencana_kerja['vol_produk_c'];
			$volume_rencana_kerja_produk_d = $rencana_kerja['vol_produk_d'];

			//TOTAL K-125
			$total_semen_125 = $komposisi_125_produk_a * $volume_rencana_kerja_produk_a;
			$total_pasir_125 = $komposisi_125_produk_b * $volume_rencana_kerja_produk_a;
			$total_batu1020_125 = $komposisi_125_produk_c * $volume_rencana_kerja_produk_a;
			$total_batu2030_125 = $komposisi_125_produk_d * $volume_rencana_kerja_produk_a;

			$nilai_semen_125 = $total_semen_125 * $komposisi_125['price_a'];
			$nilai_pasir_125 = $total_pasir_125 * $komposisi_125['price_b'];
			$nilai_batu1020_125 = $total_batu1020_125 * $komposisi_125['price_c'];
			$nilai_batu2030_125 = $total_batu2030_125 * $komposisi_125['price_d'];

			$total_125 = $nilai_semen_125 + $nilai_pasir_125 + $nilai_batu1020_125 + $nilai_batu2030_125;

			//TOTAL K-225
			$total_semen_225 = $komposisi_225_produk_a * $volume_rencana_kerja_produk_b;
			$total_pasir_225 = $komposisi_225_produk_b * $volume_rencana_kerja_produk_b;
			$total_batu1020_225 = $komposisi_225_produk_c * $volume_rencana_kerja_produk_b;
			$total_batu2030_225 = $komposisi_225_produk_d * $volume_rencana_kerja_produk_b;

			$nilai_semen_225 = $total_semen_225 * $komposisi_225['price_a'];
			$nilai_pasir_225 = $total_pasir_225 * $komposisi_225['price_b'];
			$nilai_batu1020_225 = $total_batu1020_225 * $komposisi_225['price_c'];
			$nilai_batu2030_225 = $total_batu2030_225 * $komposisi_225['price_d'];

			$total_225 = $nilai_semen_225 + $nilai_pasir_225 + $nilai_batu1020_225 + $nilai_batu2030_225;

			//TOTAL K-250
			$total_semen_250 = $komposisi_250_produk_a * $volume_rencana_kerja_produk_c;
			$total_pasir_250 = $komposisi_250_produk_b * $volume_rencana_kerja_produk_c;
			$total_batu1020_250 = $komposisi_250_produk_c * $volume_rencana_kerja_produk_c;
			$total_batu2030_250 = $komposisi_250_produk_d * $volume_rencana_kerja_produk_c;

			$nilai_semen_250 = $total_semen_250 * $komposisi_250['price_a'];
			$nilai_pasir_250 = $total_pasir_250 * $komposisi_250['price_b'];
			$nilai_batu1020_250 = $total_batu1020_250 * $komposisi_250['price_c'];
			$nilai_batu2030_250 = $total_batu2030_250 * $komposisi_250['price_d'];

			$total_250 = $nilai_semen_250 + $nilai_pasir_250 + $nilai_batu1020_250 + $nilai_batu2030_250;

			//TOTAL K-250_18
			$total_semen_250_18 = $komposisi_250_18_produk_a * $volume_rencana_kerja_produk_d;
			$total_pasir_250_18 = $komposisi_250_18_produk_b * $volume_rencana_kerja_produk_d;
			$total_batu1020_250_18 = $komposisi_250_18_produk_c * $volume_rencana_kerja_produk_d;
			$total_batu2030_250_18 = $komposisi_250_18_produk_d * $volume_rencana_kerja_produk_d;

			$nilai_semen_250_18 = $total_semen_250_18 * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18 = $total_pasir_250_18 * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18 = $total_batu1020_250_18 * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18 = $total_batu2030_250_18 * $komposisi_250_18['price_d'];

			$total_250_18 = $nilai_semen_250_18 + $nilai_pasir_250_18 + $nilai_batu1020_250_18 + $nilai_batu2030_250_18;

			//TOTAL ALL
			$total_all = $total_125 + $total_225 + $total_250 + $total_250_18;
			//END TOTAL PU
			
			$total_oktober_nilai = $total_all;

			$total_oktober_biaya_bahan = $rencana_kerja_oktober['biaya_bahan'];
			$total_oktober_biaya_alat = $rencana_kerja_oktober['biaya_alat'];
			$total_oktober_biaya_overhead = $rencana_kerja_oktober['biaya_overhead'];
			$total_oktober_biaya_bank = $rencana_kerja_oktober['biaya_bank'];
			$total_oktober_biaya_persiapan = $rencana_kerja_oktober['biaya_persiapan'];

			$total_biaya_oktober_biaya = $total_oktober_biaya_bahan + $total_oktober_biaya_alat + $total_oktober_biaya_overhead + $total_oktober_biaya_bank + $total_oktober_biaya_persiapan;
			?>
			
			<?php
			$date_november_awal = date('2022-11-01');
			$date_november_akhir = date('2022-11-30');
			$rencana_kerja_november = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_november_awal' and '$date_november_akhir'")
			->get()->row_array();
			$volume_november_produk_a = $rencana_kerja_november['vol_produk_a'];
			$volume_november_produk_b = $rencana_kerja_november['vol_produk_b'];
			$volume_november_produk_c = $rencana_kerja_november['vol_produk_c'];
			$volume_november_produk_d = $rencana_kerja_november['vol_produk_d'];


			$total_november_volume = $volume_november_produk_a + $volume_november_produk_b + $volume_november_produk_c + $volume_november_produk_d;
			
			//TOTAL PU
			$rencana_kerja_november = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_november_awal' and '$date_november_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_produk_a = $rencana_kerja_november['vol_produk_a'];
			$volume_rencana_kerja_produk_b = $rencana_kerja_november['vol_produk_b'];
			$volume_rencana_kerja_produk_c = $rencana_kerja_november['vol_produk_c'];
			$volume_rencana_kerja_produk_d = $rencana_kerja_november['vol_produk_d'];

			//TOTAL K-125
			$total_semen_125 = $komposisi_125_produk_a * $volume_rencana_kerja_produk_a;
			$total_pasir_125 = $komposisi_125_produk_b * $volume_rencana_kerja_produk_a;
			$total_batu1020_125 = $komposisi_125_produk_c * $volume_rencana_kerja_produk_a;
			$total_batu2030_125 = $komposisi_125_produk_d * $volume_rencana_kerja_produk_a;

			$nilai_semen_125 = $total_semen_125 * $komposisi_125['price_a'];
			$nilai_pasir_125 = $total_pasir_125 * $komposisi_125['price_b'];
			$nilai_batu1020_125 = $total_batu1020_125 * $komposisi_125['price_c'];
			$nilai_batu2030_125 = $total_batu2030_125 * $komposisi_125['price_d'];

			$total_125 = $nilai_semen_125 + $nilai_pasir_125 + $nilai_batu1020_125 + $nilai_batu2030_125;

			//TOTAL K-225
			$total_semen_225 = $komposisi_225_produk_a * $volume_rencana_kerja_produk_b;
			$total_pasir_225 = $komposisi_225_produk_b * $volume_rencana_kerja_produk_b;
			$total_batu1020_225 = $komposisi_225_produk_c * $volume_rencana_kerja_produk_b;
			$total_batu2030_225 = $komposisi_225_produk_d * $volume_rencana_kerja_produk_b;

			$nilai_semen_225 = $total_semen_225 * $komposisi_225['price_a'];
			$nilai_pasir_225 = $total_pasir_225 * $komposisi_225['price_b'];
			$nilai_batu1020_225 = $total_batu1020_225 * $komposisi_225['price_c'];
			$nilai_batu2030_225 = $total_batu2030_225 * $komposisi_225['price_d'];

			$total_225 = $nilai_semen_225 + $nilai_pasir_225 + $nilai_batu1020_225 + $nilai_batu2030_225;

			//TOTAL K-250
			$total_semen_250 = $komposisi_250_produk_a * $volume_rencana_kerja_produk_c;
			$total_pasir_250 = $komposisi_250_produk_b * $volume_rencana_kerja_produk_c;
			$total_batu1020_250 = $komposisi_250_produk_c * $volume_rencana_kerja_produk_c;
			$total_batu2030_250 = $komposisi_250_produk_d * $volume_rencana_kerja_produk_c;

			$nilai_semen_250 = $total_semen_250 * $komposisi_250['price_a'];
			$nilai_pasir_250 = $total_pasir_250 * $komposisi_250['price_b'];
			$nilai_batu1020_250 = $total_batu1020_250 * $komposisi_250['price_c'];
			$nilai_batu2030_250 = $total_batu2030_250 * $komposisi_250['price_d'];

			$total_250 = $nilai_semen_250 + $nilai_pasir_250 + $nilai_batu1020_250 + $nilai_batu2030_250;

			//TOTAL K-250_18
			$total_semen_250_18 = $komposisi_250_18_produk_a * $volume_rencana_kerja_produk_d;
			$total_pasir_250_18 = $komposisi_250_18_produk_b * $volume_rencana_kerja_produk_d;
			$total_batu1020_250_18 = $komposisi_250_18_produk_c * $volume_rencana_kerja_produk_d;
			$total_batu2030_250_18 = $komposisi_250_18_produk_d * $volume_rencana_kerja_produk_d;

			$nilai_semen_250_18 = $total_semen_250_18 * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18 = $total_pasir_250_18 * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18 = $total_batu1020_250_18 * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18 = $total_batu2030_250_18 * $komposisi_250_18['price_d'];

			$total_250_18 = $nilai_semen_250_18 + $nilai_pasir_250_18 + $nilai_batu1020_250_18 + $nilai_batu2030_250_18;

			//TOTAL ALL
			$total_all = $total_125 + $total_225 + $total_250 + $total_250_18;
			//END TOTAL PU

			$total_november_nilai = $total_all;

			$total_november_biaya_bahan = $rencana_kerja_november['biaya_bahan'];
			$total_november_biaya_alat = $rencana_kerja_november['biaya_alat'];
			$total_november_biaya_overhead = $rencana_kerja_november['biaya_overhead'];
			$total_november_biaya_bank = $rencana_kerja_november['biaya_bank'];
			$total_november_biaya_persiapan = $rencana_kerja_november['biaya_persiapan'];

			$total_biaya_november_biaya = $total_november_biaya_bahan + $total_november_biaya_alat + $total_november_biaya_overhead + $total_november_biaya_bank + $total_november_biaya_persiapan;
			?>

			<?php
			$date_desember_awal = date('2022-12-01');
			$date_desember_akhir = date('2022-12-31');
			$rencana_kerja_desember = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_desember_awal' and '$date_desember_akhir'")
			->get()->row_array();
			$volume_desember_produk_a = $rencana_kerja_desember['vol_produk_a'];
			$volume_desember_produk_b = $rencana_kerja_desember['vol_produk_b'];
			$volume_desember_produk_c = $rencana_kerja_desember['vol_produk_c'];
			$volume_desember_produk_d = $rencana_kerja_desember['vol_produk_d'];

			$total_desember_volume = $volume_desember_produk_a + $volume_desember_produk_b + $volume_desember_produk_c + $volume_desember_produk_d;
			
			//TOTAL PU
			$rencana_kerja_desember = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_desember_awal' and '$date_desember_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_produk_a = $rencana_kerja_desember['vol_produk_a'];
			$volume_rencana_kerja_produk_b = $rencana_kerja_desember['vol_produk_b'];
			$volume_rencana_kerja_produk_c = $rencana_kerja_desember['vol_produk_c'];
			$volume_rencana_kerja_produk_d = $rencana_kerja_desember['vol_produk_d'];

			//TOTAL K-125
			$total_semen_125 = $komposisi_125_produk_a * $volume_rencana_kerja_produk_a;
			$total_pasir_125 = $komposisi_125_produk_b * $volume_rencana_kerja_produk_a;
			$total_batu1020_125 = $komposisi_125_produk_c * $volume_rencana_kerja_produk_a;
			$total_batu2030_125 = $komposisi_125_produk_d * $volume_rencana_kerja_produk_a;

			$nilai_semen_125 = $total_semen_125 * $komposisi_125['price_a'];
			$nilai_pasir_125 = $total_pasir_125 * $komposisi_125['price_b'];
			$nilai_batu1020_125 = $total_batu1020_125 * $komposisi_125['price_c'];
			$nilai_batu2030_125 = $total_batu2030_125 * $komposisi_125['price_d'];

			$total_125 = $nilai_semen_125 + $nilai_pasir_125 + $nilai_batu1020_125 + $nilai_batu2030_125;

			//TOTAL K-225
			$total_semen_225 = $komposisi_225_produk_a * $volume_rencana_kerja_produk_b;
			$total_pasir_225 = $komposisi_225_produk_b * $volume_rencana_kerja_produk_b;
			$total_batu1020_225 = $komposisi_225_produk_c * $volume_rencana_kerja_produk_b;
			$total_batu2030_225 = $komposisi_225_produk_d * $volume_rencana_kerja_produk_b;

			$nilai_semen_225 = $total_semen_225 * $komposisi_225['price_a'];
			$nilai_pasir_225 = $total_pasir_225 * $komposisi_225['price_b'];
			$nilai_batu1020_225 = $total_batu1020_225 * $komposisi_225['price_c'];
			$nilai_batu2030_225 = $total_batu2030_225 * $komposisi_225['price_d'];

			$total_225 = $nilai_semen_225 + $nilai_pasir_225 + $nilai_batu1020_225 + $nilai_batu2030_225;

			//TOTAL K-250
			$total_semen_250 = $komposisi_250_produk_a * $volume_rencana_kerja_produk_c;
			$total_pasir_250 = $komposisi_250_produk_b * $volume_rencana_kerja_produk_c;
			$total_batu1020_250 = $komposisi_250_produk_c * $volume_rencana_kerja_produk_c;
			$total_batu2030_250 = $komposisi_250_produk_d * $volume_rencana_kerja_produk_c;

			$nilai_semen_250 = $total_semen_250 * $komposisi_250['price_a'];
			$nilai_pasir_250 = $total_pasir_250 * $komposisi_250['price_b'];
			$nilai_batu1020_250 = $total_batu1020_250 * $komposisi_250['price_c'];
			$nilai_batu2030_250 = $total_batu2030_250 * $komposisi_250['price_d'];

			$total_250 = $nilai_semen_250 + $nilai_pasir_250 + $nilai_batu1020_250 + $nilai_batu2030_250;

			//TOTAL K-250_18
			$total_semen_250_18 = $komposisi_250_18_produk_a * $volume_rencana_kerja_produk_d;
			$total_pasir_250_18 = $komposisi_250_18_produk_b * $volume_rencana_kerja_produk_d;
			$total_batu1020_250_18 = $komposisi_250_18_produk_c * $volume_rencana_kerja_produk_d;
			$total_batu2030_250_18 = $komposisi_250_18_produk_d * $volume_rencana_kerja_produk_d;

			$nilai_semen_250_18 = $total_semen_250_18 * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18 = $total_pasir_250_18 * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18 = $total_batu1020_250_18 * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18 = $total_batu2030_250_18 * $komposisi_250_18['price_d'];

			$total_250_18 = $nilai_semen_250_18 + $nilai_pasir_250_18 + $nilai_batu1020_250_18 + $nilai_batu2030_250_18;

			//TOTAL ALL
			$total_all = $total_125 + $total_225 + $total_250 + $total_250_18;
			//END TOTAL PU

			$total_desember_nilai = $total_all;
			
			$total_all_produk_a = $volume_akumulasi_produk_a + $volume_oktober_produk_a + $volume_november_produk_a + $volume_desember_produk_a;
			$total_all_produk_b = $volume_akumulasi_produk_b + $volume_oktober_produk_b + $volume_november_produk_b + $volume_desember_produk_b;
			$total_all_produk_c = $volume_akumulasi_produk_c + $volume_oktober_produk_c + $volume_november_produk_c + $volume_desember_produk_c;
			$total_all_produk_d = $volume_akumulasi_produk_d + $volume_oktober_produk_d + $volume_november_produk_d + $volume_desember_produk_d;

			$total_all_volume = $total_akumulasi_volume + $total_oktober_volume + $total_november_volume + $total_desember_volume;
			$total_all_nilai = $total_akumulasi_nilai + $total_oktober_nilai + $total_november_nilai + $total_desember_nilai;

			$total_desember_biaya_bahan = $rencana_kerja_desember['biaya_bahan'];
			$total_desember_biaya_alat = $rencana_kerja_desember['biaya_alat'];
			$total_desember_biaya_overhead = $rencana_kerja_desember['biaya_overhead'];
			$total_desember_biaya_bank = $rencana_kerja_desember['biaya_bank'];
			$total_desember_biaya_persiapan = $rencana_kerja_desember['biaya_persiapan'];

			$total_biaya_desember_biaya = $total_desember_biaya_bahan + $total_desember_biaya_alat + $total_desember_biaya_overhead + $total_desember_biaya_bank + $total_desember_biaya_persiapan;
			?>

			<?php
			//BAHAN
			$akumulasi = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar as total_nilai_keluar')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi <= '$date_agustus')")
			->get()->result_array();

			$total_akumulasi = 0;

			foreach ($akumulasi as $a){
				$total_akumulasi += $a['total_nilai_keluar'];
			}

			$total_bahan_akumulasi = $total_akumulasi;
			//END BAHAN
			?>

			<?php
			//ALAT
			$nilai_alat = $this->db->select('SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("(prm.date_receipt <= '$date_agustus')")
			->where("p.kategori_produk = '5'")
			->where("po.status in ('PUBLISH','CLOSED')")
			->get()->row_array();

			$akumulasi_bbm = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar_2 as total_nilai_keluar_2')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi <= '$date_agustus')")
			->get()->result_array();

			$total_akumulasi_bbm = 0;

			foreach ($akumulasi_bbm as $b){
				$total_akumulasi_bbm += $b['total_nilai_keluar_2'];
			}

			$total_nilai_bbm = $total_akumulasi_bbm;

			$total_insentif_tm = 0;

			$insentif_tm = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->where("pdb.akun = 220")
			->where("status = 'PAID'")
			->where("(tanggal_transaksi <= '$date_agustus')")
			->get()->row_array();

			$total_insentif_tm = $insentif_tm['total'];

			$total_alat_akumulasi = $nilai_alat['nilai'] + $total_akumulasi_bbm + $total_insentif_tm;
			//END ALAT
			?>

			<?php
			//OVERHEAD
			$overhead_15_akumulasi = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',15)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$date_agustus')")
			->get()->row_array();

			$overhead_jurnal_15_akumulasi = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',15)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$date_agustus')")
			->get()->row_array();

			$overhead_16_akumulasi = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',16)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$date_agustus')")
			->get()->row_array();

			$overhead_jurnal_16_akumulasi = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',16)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$date_agustus')")
			->get()->row_array();

			$overhead_17_akumulasi = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',17)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$date_agustus')")
			->get()->row_array();

			$overhead_jurnal_17_akumulasi = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',17)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$date_agustus')")
			->get()->row_array();

			$total_overhead_akumulasi =  $overhead_15_akumulasi['total'] + $overhead_jurnal_15_akumulasi['total'] + $overhead_16_akumulasi['total'] + $overhead_jurnal_16_akumulasi['total'] + $overhead_17_akumulasi['total'] + $overhead_jurnal_17_akumulasi['total'];
			?>

			<?php
			//DISKONTO
			$diskonto_akumulasi = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 168")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$date_agustus')")
			->get()->row_array();

			$total_diskonto_akumulasi = $diskonto_akumulasi['total'];
			//DISKONTO
			?>

			<?php
			//PERSIAPAN
			$persiapan_biaya_akumulasi = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 228")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$date_agustus')")
			->get()->row_array();

			$persiapan_jurnal_akumulasi = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 228")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$date_agustus')")
			->get()->row_array();

			$total_persiapan_akumulasi = $persiapan_biaya_akumulasi['total'] + $persiapan_jurnal_akumulasi['total'];

			$total_biaya_akumulasi = $total_bahan_akumulasi + $total_alat_akumulasi + $total_overhead_akumulasi + $total_diskonto_akumulasi + $total_persiapan_akumulasi;
			//END PERSIAPAN

			$total_all_biaya_bahan = $total_bahan_akumulasi + $total_oktober_biaya_bahan + $total_november_biaya_bahan + $total_desember_biaya_bahan;
			$total_all_biaya_alat = $total_alat_akumulasi + $total_oktober_biaya_alat + $total_november_biaya_alat + $total_desember_biaya_alat;
			$total_all_biaya_overhead = $total_overhead_akumulasi + $total_oktober_biaya_overhead + $total_november_biaya_overhead + $total_desember_biaya_overhead;
			$total_all_biaya_bank = $total_diskonto_akumulasi + $total_oktober_biaya_bank + $total_november_biaya_bank + $total_desember_biaya_bank;
			$total_all_biaya_persiapan = $total_persiapan_akumulasi + $total_oktober_biaya_persiapan + $total_november_biaya_persiapan + $total_desember_biaya_persiapan;
			
			$total_biaya_all_biaya = $total_all_biaya_bahan + $total_all_biaya_alat + $total_all_biaya_overhead + $total_all_biaya_bank + $total_all_biaya_persiapan;

			$total_laba_sd_agustus = $total_akumulasi_nilai - $total_biaya_akumulasi;
			$total_laba_oktober = $total_oktober_nilai - $total_biaya_oktober_biaya;
			$total_laba_november = $total_november_nilai - $total_biaya_november_biaya;
			$total_laba_desember = $total_desember_nilai - $total_biaya_desember_biaya;
			$total_laba_all = $total_all_nilai - $total_biaya_all_biaya;
			?>
			
			<tr class="table-judul">
				<th width="5%" align="center" rowspan="2" style="vertical-align:middle">NO.</th>
				<th width="15%" align="center" rowspan="2" style="vertical-align:middle">URAIAN</th>
				<th width="10%" align="center" rowspan="2" style="vertical-align:middle">SATUAN</th>
				<th width="15%" align="center" rowspan="2" style="vertical-align:middle">REALISASI SMT 1-2 SD. AGUSTUS 2022</th>
				<th width="40%" align="center" colspan="3">RENCANA KERJA</th>
				<th width="15%" align="center">TOTAL</th>
	        </tr>
			<tr class="table-judul">
				<th align="center">OKTOBER</th>
				<th align="center">NOVEMBER</th>
				<th align="center">DESEMBER</th>
				<th align="center">SD. DESEMBER 2022</th>
	        </tr>
			<tr class="table-baris1-bold">
				<th align="left" colspan="10">RENCANA PRODUKSI & PENDAPATAN USAHA</th>
			</tr>
			<tr class="table-baris1">
				<th align="center">1</th>
				<th align="left">Beton K 125 (10±2)</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($volume_akumulasi_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_oktober_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_november_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_desember_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_produk_a,2,',','.');?></th>	
			</tr>
			<tr class="table-baris1">
				<th align="center">2</th>
				<th align="left">Beton K 225 (10±2)</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($volume_akumulasi_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_oktober_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_november_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_desember_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_produk_b,2,',','.');?></th>	
			</tr>
			<tr class="table-baris1">
				<th align="center">3</th>
				<th align="left">Beton K 250 (10±2)</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($volume_akumulasi_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_oktober_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_november_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_desember_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_produk_c,2,',','.');?></th>	
			</tr>
			<tr class="table-baris1">
				<th align="center">4</th>
				<th align="left">Beton K 250 (18±2)</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($volume_akumulasi_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_oktober_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_november_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_desember_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_produk_d,2,',','.');?></th>	
			</tr>
			<tr class="table-total">
				<th align="right" colspan="2">TOTAL VOLUME</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($total_akumulasi_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_oktober_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_november_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_desember_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_volume,2,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="right" colspan="2">PENDAPATAN USAHA</th>
				<th align="center"></th>
				<th align="right"><?php echo number_format($total_akumulasi_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_oktober_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_november_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_desember_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_nilai,0,',','.');?></th>
			</tr>
			<tr class="table-baris1-bold">
				<th align="left" colspan="10">BIAYA</th>
			</tr>
			<tr class="table-baris1">
				<th align="center">1</th>
				<th align="left">Bahan</th>
				<th align="center">LS</th>
				<th align="right"><?php echo number_format($total_bahan_akumulasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_oktober_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_november_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_desember_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_biaya_bahan,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center">2</th>
				<th align="left">Alat</th>
				<th align="center">LS</th>
				<th align="right"><?php echo number_format($total_alat_akumulasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_oktober_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_november_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_desember_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_biaya_alat,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center">3</th>
				<th align="left">Overhead</th>
				<th align="center">LS</th>
				<th align="right"><?php echo number_format($total_overhead_akumulasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_oktober_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_november_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_desember_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_biaya_overhead,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center">4</th>
				<th align="left">Biaya Bank</th>
				<th align="center">LS</th>
				<th align="right"><?php echo number_format($total_diskonto_akumulasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_oktober_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_november_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_desember_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_biaya_bank,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center">5</th>
				<th align="left">Persiapan</th>
				<th align="center">LS</th>
				<th align="right"><?php echo number_format($total_persiapan_akumulasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_oktober_biaya_persiapan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_november_biaya_persiapan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_desember_biaya_persiapan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_biaya_persiapan,0,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="right" colspan="2">JUMLAH</th>
				<th align="center"></th>
				<th align="right"><?php echo number_format($total_biaya_akumulasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_oktober_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_november_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_desember_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_all_biaya,0,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="right" colspan="2">LABA</th>
				<th align="center"></th>
				<th align="right"><?php echo number_format($total_laba_sd_agustus,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_oktober,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_november,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_desember,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_all,0,',','.');?></th>
			</tr>
	    </table>
		
	</body>
</html>