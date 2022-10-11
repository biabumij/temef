<!DOCTYPE html>
<html>
	<head>
	  <title>LAPORAN RENCANA KERJA PRODUKSI</title>
	  
	  <style type="text/css">
		table tr.table-judul{
			background-color: #e69500;
			font-weight: bold;
			font-size: 6px;
			color: black;
		}
			
		table tr.table-baris1{
			background-color: #F0F0F0;
			font-size: 6px;
		}

		table tr.table-baris1-bold{
			background-color: #F0F0F0;
			font-size: 6px;
			font-weight: bold;
		}
			
		table tr.table-baris2{
			font-size: 6px;
			background-color: #E8E8E8;
		}
			
		table tr.table-total{
			background-color: #cccccc;
			font-weight: bold;
			font-size: 6px;
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
		
		<!-- RAP 2022 -->
		<?php
			$date_now = date('Y-m-d');
			$date_end = date('2022-12-31');
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

			$nilai_jual_125_2022 = $volume_rap_2022_produk_a * $harga_jual_125_rap['harga_satuan'];
			$nilai_jual_225_2022 = $volume_rap_2022_produk_b * $harga_jual_225_rap['harga_satuan'];
			$nilai_jual_250_2022 = $volume_rap_2022_produk_c * $harga_jual_250_rap['harga_satuan'];
			$nilai_jual_250_18_2022 = $volume_rap_2022_produk_d * $harga_jual_250_18_rap['harga_satuan'];
			$nilai_jual_all_2022 = $nilai_jual_125_2022 + $nilai_jual_225_2022 + $nilai_jual_250_2022 + $nilai_jual_250_18_2022;
			
			$total_rap_nilai_2022 = $nilai_jual_all_2022;

			//KOMPOSISI RAP 2022
			//K125
			$komposisi_125_rap = $this->db->select('pk.*')
			->from('pmm_agregat pk')
			->where("pk.mutu_beton = 2")
			->where('pk.status','PUBLISH')
			->order_by('pk.date_agregat','asc')->limit(1)
			->get()->row_array();

			$komposisi_125_produk_a_rap = $komposisi_125_rap['presentase_a'];
			$komposisi_125_produk_b_rap = $komposisi_125_rap['presentase_b'];
			$komposisi_125_produk_c_rap = $komposisi_125_rap['presentase_c'];
			$komposisi_125_produk_d_rap = $komposisi_125_rap['presentase_d'];

			//K225
			$komposisi_225_rap = $this->db->select('pk.*')
			->from('pmm_agregat pk')
			->where("pk.mutu_beton = 1")
			->where('pk.status','PUBLISH')
			->order_by('pk.date_agregat','asc')->limit(1)
			->get()->row_array();

			$komposisi_225_produk_a_rap = $komposisi_225_rap['presentase_a'];
			$komposisi_225_produk_b_rap = $komposisi_225_rap['presentase_b'];
			$komposisi_225_produk_c_rap = $komposisi_225_rap['presentase_c'];
			$komposisi_225_produk_d_rap = $komposisi_225_rap['presentase_d'];

			//K250
			$komposisi_250_rap = $this->db->select('pk.*')
			->from('pmm_agregat pk')
			->where("pk.mutu_beton = 3")
			->where('pk.status','PUBLISH')
			->order_by('pk.date_agregat','asc')->limit(1)
			->get()->row_array();

			$komposisi_250_produk_a_rap = $komposisi_250_rap['presentase_a'];
			$komposisi_250_produk_b_rap = $komposisi_250_rap['presentase_b'];
			$komposisi_250_produk_c_rap = $komposisi_250_rap['presentase_c'];
			$komposisi_250_produk_d_rap = $komposisi_250_rap['presentase_d'];

			//K250_18
			$komposisi_250_18_rap = $this->db->select('pk.*')
			->from('pmm_agregat pk')
			->where("pk.mutu_beton = 11")
			->where('pk.status','PUBLISH')
			->order_by('pk.date_agregat','asc')->limit(1)
			->get()->row_array();

			$komposisi_250_18_produk_a_rap = $komposisi_250_18_rap['presentase_a'];
			$komposisi_250_18_produk_b_rap = $komposisi_250_18_rap['presentase_b'];
			$komposisi_250_18_produk_c_rap = $komposisi_250_18_rap['presentase_c'];
			$komposisi_250_18_produk_d_rap = $komposisi_250_18_rap['presentase_d'];

			//KOMPOSISI
			//K125
			$komposisi_125 = $this->db->select('pk.*')
			->from('pmm_agregat pk')
			->where("pk.mutu_beton = 2")
			->where('pk.status','PUBLISH')
			->order_by('pk.date_agregat','asc')->limit(1)
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
			->order_by('pk.date_agregat','asc')->limit(1)
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
			->order_by('pk.date_agregat','asc')->limit(1)
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
			->order_by('pk.date_agregat','asc')->limit(1)
			->get()->row_array();

			$komposisi_250_18_produk_a = $komposisi_250_18['presentase_a'];
			$komposisi_250_18_produk_b = $komposisi_250_18['presentase_b'];
			$komposisi_250_18_produk_c = $komposisi_250_18['presentase_c'];
			$komposisi_250_18_produk_d = $komposisi_250_18['presentase_d'];

			//TOTAL PEMAKAIAN BAHAN RAP 2022
			//TOTAL K-125
			$total_semen_125_rap_2022 = $komposisi_125_produk_a_rap * $volume_rap_2022_produk_a;
			$total_pasir_125_rap_2022 = $komposisi_125_produk_b_rap * $volume_rap_2022_produk_a;
			$total_batu1020_125_rap_2022 = $komposisi_125_produk_c_rap * $volume_rap_2022_produk_a;
			$total_batu2030_125_rap_2022 = $komposisi_125_produk_d_rap * $volume_rap_2022_produk_a;

			$nilai_semen_125_rap_2022 = $total_semen_125_rap_2022 * $komposisi_125_rap['price_a'];
			$nilai_pasir_125_rap_2022 = $total_pasir_125_rap_2022 * $komposisi_125_rap['price_b'];
			$nilai_batu1020_125_rap_2022 = $total_batu1020_125_rap_2022 * $komposisi_125_rap['price_c'];
			$nilai_batu2030_125_rap_2022 = $total_batu2030_125_rap_2022 * $komposisi_125_rap['price_d'];

			$total_125_rap_2022 = $nilai_semen_125_rap_2022 + $nilai_pasir_125_rap_2022 + $nilai_batu1020_125_rap_2022 + $nilai_batu2030_125_rap_2022;

			//TOTAL K-225
			$total_semen_225_rap_2022 = $komposisi_225_produk_a_rap * $volume_rap_2022_produk_b;
			$total_pasir_225_rap_2022 = $komposisi_225_produk_b_rap * $volume_rap_2022_produk_b;
			$total_batu1020_225_rap_2022 = $komposisi_225_produk_c_rap * $volume_rap_2022_produk_b;
			$total_batu2030_225_rap_2022 = $komposisi_225_produk_d_rap * $volume_rap_2022_produk_b;

			$nilai_semen_225_rap_2022 = $total_semen_225_rap_2022 * $komposisi_225_rap['price_a'];
			$nilai_pasir_225_rap_2022 = $total_pasir_225_rap_2022 * $komposisi_225_rap['price_b'];
			$nilai_batu1020_225_rap_2022 = $total_batu1020_225_rap_2022 * $komposisi_225_rap['price_c'];
			$nilai_batu2030_225_rap_2022 = $total_batu2030_225_rap_2022 * $komposisi_225_rap['price_d'];

			$total_225_rap_2022 = $nilai_semen_225_rap_2022 + $nilai_pasir_225_rap_2022 + $nilai_batu1020_225_rap_2022 + $nilai_batu2030_225_rap_2022;

			//TOTAL K-250
			$total_semen_250_rap_2022 = $komposisi_250_produk_a_rap * $volume_rap_2022_produk_c;
			$total_pasir_250_rap_2022 = $komposisi_250_produk_b_rap * $volume_rap_2022_produk_c;
			$total_batu1020_250_rap_2022 = $komposisi_250_produk_c_rap * $volume_rap_2022_produk_c;
			$total_batu2030_250_rap_2022 = $komposisi_250_produk_d_rap * $volume_rap_2022_produk_c;

			$nilai_semen_250_rap_2022 = $total_semen_250_rap_2022 * $komposisi_250_rap['price_a'];
			$nilai_pasir_250_rap_2022 = $total_pasir_250_rap_2022 * $komposisi_250_rap['price_b'];
			$nilai_batu1020_250_rap_2022 = $total_batu1020_250_rap_2022 * $komposisi_250_rap['price_c'];
			$nilai_batu2030_250_rap_2022 = $total_batu2030_250_rap_2022 * $komposisi_250_rap['price_d'];

			$total_250_rap_2022 = $nilai_semen_250_rap_2022 + $nilai_pasir_250_rap_2022 + $nilai_batu1020_250_rap_2022 + $nilai_batu2030_250_rap_2022;

			//TOTAL K-250_18
			$total_semen_250_18_rap_2022 = $komposisi_250_18_produk_a_rap * $volume_rap_2022_produk_d;
			$total_pasir_250_18_rap_2022 = $komposisi_250_18_produk_b_rap * $volume_rap_2022_produk_d;
			$total_batu1020_250_18_rap_2022 = $komposisi_250_18_produk_c_rap * $volume_rap_2022_produk_d;
			$total_batu2030_250_18_rap_2022 = $komposisi_250_18_produk_d_rap * $volume_rap_2022_produk_d;

			$nilai_semen_250_18_rap_2022 = $total_semen_250_18_rap_2022 * $komposisi_250_18_rap['price_a'];
			$nilai_pasir_250_18_rap_2022 = $total_pasir_250_18_rap_2022 * $komposisi_250_18_rap['price_b'];
			$nilai_batu1020_250_18_rap_2022 = $total_batu1020_250_18_rap_2022 * $komposisi_250_18_rap['price_c'];
			$nilai_batu2030_250_18_rap_2022 = $total_batu2030_250_18_rap_2022 * $komposisi_250_18_rap['price_d'];

			$total_250_18_rap_2022 = $nilai_semen_250_18_rap_2022 + $nilai_pasir_250_18_rap_2022 + $nilai_batu1020_250_18_rap_2022 + $nilai_batu2030_250_18_rap_2022;

			//TOTAL ALL
			$total_bahan_all_rap_2022 = $total_125_rap_2022 + $total_225_rap_2022 + $total_250_rap_2022 + $total_250_18_rap_2022;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_rap_2022 = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_end')")
			->get()->row_array();

			$batching_plant_rap_2022 = $total_rap_volume_2022 * $rap_alat_rap_2022['batching_plant'];
			$truck_mixer_rap_2022 = $total_rap_volume_2022 * $rap_alat_rap_2022['truck_mixer'];
			$wheel_loader_rap_2022 = $total_rap_volume_2022 * $rap_alat_rap_2022['wheel_loader'];
			$bbm_solar_rap_2022 = $total_rap_volume_2022 * $rap_alat_rap_2022['bbm_solar'];
			$biaya_alat_all_rap_2022 = $batching_plant_rap_2022 + $truck_mixer_rap_2022 + $wheel_loader_rap_2022 + $bbm_solar_rap_2022;
		
			$total_rap_2022_biaya_bahan = $total_bahan_all_rap_2022;
			$total_rap_2022_biaya_alat = $biaya_alat_all_rap_2022;
			$total_rap_2022_biaya_overhead = $rencana_kerja_2022['biaya_overhead'];
			$total_rap_2022_biaya_bank = $rencana_kerja_2022['biaya_bank'];
			$total_rap_2022_biaya_persiapan = $rencana_kerja_2022['biaya_persiapan'];

			$total_biaya_rap_2022_biaya = $total_rap_2022_biaya_bahan + $total_rap_2022_biaya_alat + $total_rap_2022_biaya_overhead + $total_rap_2022_biaya_bank + $total_rap_2022_biaya_persiapan;

			?>
			<!-- RAP 2022 -->
			
			<!-- AKUMULASI SD. SAAT INI -->
			<?php
			$penjualan_akumulasi_produk_a = $this->db->select('p.nama_produk, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('produk p', 'pp.product_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("(pp.date_production <= '$date_now')")
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
			->where("(pp.date_production <= '$date_now')")
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
			->where("(pp.date_production <= '$date_now')")
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
			->where("(pp.date_production <= '$date_now')")
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
			<!-- AKUMULASI SD. SAAT INI -->

			<!-- OKTOBER -->
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
		
			$nilai_jual_125_oktober = $volume_oktober_produk_a * $harga_jual_125_now['harga_satuan'];
			$nilai_jual_225_oktober = $volume_oktober_produk_b * $harga_jual_225_now['harga_satuan'];
			$nilai_jual_250_oktober = $volume_oktober_produk_c * $harga_jual_250_now['harga_satuan'];
			$nilai_jual_250_18_oktober = $volume_oktober_produk_d * $harga_jual_250_18_now['harga_satuan'];
			$nilai_jual_all_oktober = $nilai_jual_125_oktober + $nilai_jual_225_oktober + $nilai_jual_250_oktober + $nilai_jual_250_18_oktober;
			
			$total_oktober_nilai = $nilai_jual_all_oktober;

			$rencana_kerja_oktober = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_oktober_awal' and '$date_oktober_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_oktober_produk_a = $rencana_kerja_oktober['vol_produk_a'];
			$volume_rencana_kerja_oktober_produk_b = $rencana_kerja_oktober['vol_produk_b'];
			$volume_rencana_kerja_oktober_produk_c = $rencana_kerja_oktober['vol_produk_c'];
			$volume_rencana_kerja_oktober_produk_d = $rencana_kerja_oktober['vol_produk_d'];

			//TOTAL PEMAKAIAN BAHAN OKTOBER
			//TOTAL K-125
			$total_semen_125_oktober = $komposisi_125_produk_a * $volume_rencana_kerja_oktober_produk_a;
			$total_pasir_125_oktober = $komposisi_125_produk_b * $volume_rencana_kerja_oktober_produk_a;
			$total_batu1020_125_oktober = $komposisi_125_produk_c * $volume_rencana_kerja_oktober_produk_a;
			$total_batu2030_125_oktober = $komposisi_125_produk_d * $volume_rencana_kerja_oktober_produk_a;

			$nilai_semen_125_oktober = $total_semen_125_oktober * $komposisi_125['price_a'];
			$nilai_pasir_125_oktober = $total_pasir_125_oktober * $komposisi_125['price_b'];
			$nilai_batu1020_125_oktober = $total_batu1020_125_oktober * $komposisi_125['price_c'];
			$nilai_batu2030_125_oktober = $total_batu2030_125_oktober * $komposisi_125['price_d'];

			$total_125_oktober = $nilai_semen_125_oktober + $nilai_pasir_125_oktober + $nilai_batu1020_125_oktober + $nilai_batu2030_125_oktober;

			//TOTAL K-225
			$total_semen_225_oktober = $komposisi_225_produk_a * $volume_rencana_kerja_oktober_produk_b;
			$total_pasir_225_oktober = $komposisi_225_produk_b * $volume_rencana_kerja_oktober_produk_b;
			$total_batu1020_225_oktober = $komposisi_225_produk_c * $volume_rencana_kerja_oktober_produk_b;
			$total_batu2030_225_oktober = $komposisi_225_produk_d * $volume_rencana_kerja_oktober_produk_b;

			$nilai_semen_225_oktober = $total_semen_225_oktober * $komposisi_225['price_a'];
			$nilai_pasir_225_oktober = $total_pasir_225_oktober * $komposisi_225['price_b'];
			$nilai_batu1020_225_oktober = $total_batu1020_225_oktober * $komposisi_225['price_c'];
			$nilai_batu2030_225_oktober = $total_batu2030_225_oktober * $komposisi_225['price_d'];

			$total_225_oktober = $nilai_semen_225_oktober + $nilai_pasir_225_oktober + $nilai_batu1020_225_oktober + $nilai_batu2030_225_oktober;

			//TOTAL K-250
			$total_semen_250_oktober = $komposisi_250_produk_a * $volume_rencana_kerja_oktober_produk_c;
			$total_pasir_250_oktober = $komposisi_250_produk_b * $volume_rencana_kerja_oktober_produk_c;
			$total_batu1020_250_oktober = $komposisi_250_produk_c * $volume_rencana_kerja_oktober_produk_c;
			$total_batu2030_250_oktober = $komposisi_250_produk_d * $volume_rencana_kerja_oktober_produk_c;

			$nilai_semen_250_oktober = $total_semen_250_oktober * $komposisi_250['price_a'];
			$nilai_pasir_250_oktober = $total_pasir_250_oktober * $komposisi_250['price_b'];
			$nilai_batu1020_250_oktober = $total_batu1020_250_oktober * $komposisi_250['price_c'];
			$nilai_batu2030_250_oktober = $total_batu2030_250_oktober * $komposisi_250['price_d'];

			$total_250_oktober = $nilai_semen_250_oktober + $nilai_pasir_250_oktober + $nilai_batu1020_250_oktober + $nilai_batu2030_250_oktober;

			//TOTAL K-250_18
			$total_semen_250_18_oktober = $komposisi_250_18_produk_a * $volume_rencana_kerja_oktober_produk_d;
			$total_pasir_250_18_oktober = $komposisi_250_18_produk_b * $volume_rencana_kerja_oktober_produk_d;
			$total_batu1020_250_18_oktober = $komposisi_250_18_produk_c * $volume_rencana_kerja_oktober_produk_d;
			$total_batu2030_250_18_oktober = $komposisi_250_18_produk_d * $volume_rencana_kerja_oktober_produk_d;

			$nilai_semen_250_18_oktober = $total_semen_250_18_oktober * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_oktober = $total_pasir_250_18_oktober * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_oktober = $total_batu1020_250_18_oktober * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_oktober = $total_batu2030_250_18_oktober * $komposisi_250_18['price_d'];

			$total_250_18_oktober = $nilai_semen_250_18_oktober + $nilai_pasir_250_18_oktober + $nilai_batu1020_250_18_oktober + $nilai_batu2030_250_18_oktober;

			//TOTAL ALL
			$total_bahan_all_oktober = $total_125_oktober + $total_225_oktober + $total_250_oktober + $total_250_18_oktober;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_oktober = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_oktober_akhir')")
			->get()->row_array();

			$batching_plant_oktober = $total_oktober_volume * $rap_alat_oktober['batching_plant'];
			$truck_mixer_oktober = $total_oktober_volume * $rap_alat_oktober['truck_mixer'];
			$wheel_loader_oktober = $total_oktober_volume * $rap_alat_oktober['wheel_loader'];
			$bbm_solar_oktober = $total_oktober_volume * $rap_alat_oktober['bbm_solar'];
			$biaya_alat_all_oktober = $batching_plant_oktober + $truck_mixer_oktober + $wheel_loader_oktober + $bbm_solar_oktober;
		
			$total_oktober_biaya_bahan = $total_bahan_all_oktober;
			$total_oktober_biaya_alat = $biaya_alat_all_oktober;
			$total_oktober_biaya_overhead = $rencana_kerja_oktober['biaya_overhead'];
			$total_oktober_biaya_bank = $rencana_kerja_oktober['biaya_bank'];
			$total_oktober_biaya_persiapan = $rencana_kerja_oktober['biaya_persiapan'];

			$total_biaya_oktober_biaya = $total_oktober_biaya_bahan + $total_oktober_biaya_alat + $total_oktober_biaya_overhead + $total_oktober_biaya_bank + $total_oktober_biaya_persiapan;
			?>
			<!-- OKTOBER -->
			
			<!-- NOVEMBER -->
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

			$nilai_jual_125_november = $volume_november_produk_a * $harga_jual_125_now['harga_satuan'];
			$nilai_jual_225_november = $volume_november_produk_b * $harga_jual_225_now['harga_satuan'];
			$nilai_jual_250_november = $volume_november_produk_c * $harga_jual_250_now['harga_satuan'];
			$nilai_jual_250_18_november = $volume_november_produk_d * $harga_jual_250_18_now['harga_satuan'];
			$nilai_jual_all_november = $nilai_jual_125_november + $nilai_jual_225_november + $nilai_jual_250_november + $nilai_jual_250_18_november;

			$total_november_nilai = $nilai_jual_all_november;

			//TOTAL PEMAKAIAN BAHAN
			$rencana_kerja_november = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_november_awal' and '$date_november_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_november_produk_a = $rencana_kerja_november['vol_produk_a'];
			$volume_rencana_kerja_november_produk_b = $rencana_kerja_november['vol_produk_b'];
			$volume_rencana_kerja_november_produk_c = $rencana_kerja_november['vol_produk_c'];
			$volume_rencana_kerja_november_produk_d = $rencana_kerja_november['vol_produk_d'];

			//TOTAL K-125
			$total_semen_125_november = $komposisi_125_produk_a * $volume_rencana_kerja_november_produk_a;
			$total_pasir_125_november = $komposisi_125_produk_b * $volume_rencana_kerja_november_produk_a;
			$total_batu1020_125_november = $komposisi_125_produk_c * $volume_rencana_kerja_november_produk_a;
			$total_batu2030_125_november = $komposisi_125_produk_d * $volume_rencana_kerja_november_produk_a;

			$nilai_semen_125_november = $total_semen_125_november * $komposisi_125['price_a'];
			$nilai_pasir_125_november = $total_pasir_125_november * $komposisi_125['price_b'];
			$nilai_batu1020_125_november = $total_batu1020_125_november * $komposisi_125['price_c'];
			$nilai_batu2030_125_november = $total_batu2030_125_november * $komposisi_125['price_d'];

			$total_125_november = $nilai_semen_125_november + $nilai_pasir_125_november + $nilai_batu1020_125_november + $nilai_batu2030_125_november;

			//TOTAL K-225
			$total_semen_225_november = $komposisi_225_produk_a * $volume_rencana_kerja_november_produk_b;
			$total_pasir_225_november = $komposisi_225_produk_b * $volume_rencana_kerja_november_produk_b;
			$total_batu1020_225_november = $komposisi_225_produk_c * $volume_rencana_kerja_november_produk_b;
			$total_batu2030_225_november = $komposisi_225_produk_d * $volume_rencana_kerja_november_produk_b;

			$nilai_semen_225_november = $total_semen_225_november * $komposisi_225['price_a'];
			$nilai_pasir_225_november = $total_pasir_225_november * $komposisi_225['price_b'];
			$nilai_batu1020_225_november = $total_batu1020_225_november * $komposisi_225['price_c'];
			$nilai_batu2030_225_november = $total_batu2030_225_november * $komposisi_225['price_d'];

			$total_225_november = $nilai_semen_225_november + $nilai_pasir_225_november + $nilai_batu1020_225_november + $nilai_batu2030_225_november;

			//TOTAL K-250
			$total_semen_250_november = $komposisi_250_produk_a * $volume_rencana_kerja_november_produk_c;
			$total_pasir_250_november = $komposisi_250_produk_b * $volume_rencana_kerja_november_produk_c;
			$total_batu1020_250_november = $komposisi_250_produk_c * $volume_rencana_kerja_november_produk_c;
			$total_batu2030_250_november = $komposisi_250_produk_d * $volume_rencana_kerja_november_produk_c;

			$nilai_semen_250_november = $total_semen_250_november * $komposisi_250['price_a'];
			$nilai_pasir_250_november = $total_pasir_250_november * $komposisi_250['price_b'];
			$nilai_batu1020_250_november = $total_batu1020_250_november * $komposisi_250['price_c'];
			$nilai_batu2030_250_november = $total_batu2030_250_november * $komposisi_250['price_d'];

			$total_250_november = $nilai_semen_250_november + $nilai_pasir_250_november + $nilai_batu1020_250_november + $nilai_batu2030_250_november;

			//TOTAL K-250_18
			$total_semen_250_18_november = $komposisi_250_18_produk_a * $volume_rencana_kerja_november_produk_d;
			$total_pasir_250_18_november = $komposisi_250_18_produk_b * $volume_rencana_kerja_november_produk_d;
			$total_batu1020_250_18_november = $komposisi_250_18_produk_c * $volume_rencana_kerja_november_produk_d;
			$total_batu2030_250_18_november = $komposisi_250_18_produk_d * $volume_rencana_kerja_november_produk_d;

			$nilai_semen_250_18_november = $total_semen_250_18_november  * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_november = $total_pasir_250_18_november  * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_november = $total_batu1020_250_18_november  * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_november = $total_batu2030_250_18_november  * $komposisi_250_18['price_d'];

			$total_250_18_november = $nilai_semen_250_18_november  + $nilai_pasir_250_18_november  + $nilai_batu1020_250_18_november  + $nilai_batu2030_250_18_november;

			//TOTAL ALL
			$total_bahan_all_november = $total_125_november  + $total_225_november  + $total_250_november  + $total_250_18_november;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_november = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_november_akhir')")
			->get()->row_array();

			$batching_plant_november = $total_november_volume * $rap_alat_november['batching_plant'];
			$truck_mixer_november = $total_november_volume * $rap_alat_november['truck_mixer'];
			$wheel_loader_november = $total_november_volume * $rap_alat_november['wheel_loader'];
			$bbm_solar_november = $total_november_volume * $rap_alat_november['bbm_solar'];
			$biaya_alat_all_november = $batching_plant_november + $truck_mixer_november + $wheel_loader_november + $bbm_solar_november;
		
			$total_november_biaya_bahan = $total_bahan_all_november;
			$total_november_biaya_alat = $biaya_alat_all_november;
			$total_november_biaya_overhead = $rencana_kerja_november['biaya_overhead'];
			$total_november_biaya_bank = $rencana_kerja_november['biaya_bank'];
			$total_november_biaya_persiapan = $rencana_kerja_november['biaya_persiapan'];

			$total_biaya_november_biaya = $total_november_biaya_bahan + $total_november_biaya_alat + $total_november_biaya_overhead + $total_november_biaya_bank + $total_november_biaya_persiapan;
			?>
			<!-- NOVEMBER -->

			<!-- DESEMBER -->
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

			$nilai_jual_125_desember = $volume_desember_produk_a * $harga_jual_125_now['harga_satuan'];
			$nilai_jual_225_desember = $volume_desember_produk_b * $harga_jual_225_now['harga_satuan'];
			$nilai_jual_250_desember = $volume_desember_produk_c * $harga_jual_250_now['harga_satuan'];
			$nilai_jual_250_18_desember = $volume_desember_produk_d * $harga_jual_250_18_now['harga_satuan'];
			$nilai_jual_all_desember = $nilai_jual_125_desember + $nilai_jual_225_desember + $nilai_jual_250_desember + $nilai_jual_250_18_desember;

			$total_desember_nilai = $nilai_jual_all_desember;

			//TOTAL PEMAKAIAN BAHAN
			$rencana_kerja_desember = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_desember_awal' and '$date_desember_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_desember_produk_a = $rencana_kerja_desember['vol_produk_a'];
			$volume_rencana_kerja_desember_produk_b = $rencana_kerja_desember['vol_produk_b'];
			$volume_rencana_kerja_desember_produk_c = $rencana_kerja_desember['vol_produk_c'];
			$volume_rencana_kerja_desember_produk_d = $rencana_kerja_desember['vol_produk_d'];

			//TOTAL K-125
			$total_semen_125_desember = $komposisi_125_produk_a * $volume_rencana_kerja_desember_produk_a;
			$total_pasir_125_desember = $komposisi_125_produk_b * $volume_rencana_kerja_desember_produk_a;
			$total_batu1020_125_desember = $komposisi_125_produk_c * $volume_rencana_kerja_desember_produk_a;
			$total_batu2030_125_desember = $komposisi_125_produk_d * $volume_rencana_kerja_desember_produk_a;

			$nilai_semen_125_desember = $total_semen_125_desember * $komposisi_125['price_a'];
			$nilai_pasir_125_desember = $total_pasir_125_desember * $komposisi_125['price_b'];
			$nilai_batu1020_125_desember = $total_batu1020_125_desember * $komposisi_125['price_c'];
			$nilai_batu2030_125_desember = $total_batu2030_125_desember * $komposisi_125['price_d'];

			$total_125_desember = $nilai_semen_125_desember + $nilai_pasir_125_desember + $nilai_batu1020_125_desember + $nilai_batu2030_125_desember;

			//TOTAL K-225
			$total_semen_225_desember = $komposisi_225_produk_a * $volume_rencana_kerja_desember_produk_b;
			$total_pasir_225_desember = $komposisi_225_produk_b * $volume_rencana_kerja_desember_produk_b;
			$total_batu1020_225_desember = $komposisi_225_produk_c * $volume_rencana_kerja_desember_produk_b;
			$total_batu2030_225_desember = $komposisi_225_produk_d * $volume_rencana_kerja_desember_produk_b;

			$nilai_semen_225_desember = $total_semen_225_desember * $komposisi_225['price_a'];
			$nilai_pasir_225_desember = $total_pasir_225_desember * $komposisi_225['price_b'];
			$nilai_batu1020_225_desember = $total_batu1020_225_desember * $komposisi_225['price_c'];
			$nilai_batu2030_225_desember = $total_batu2030_225_desember * $komposisi_225['price_d'];

			$total_225_desember = $nilai_semen_225_desember + $nilai_pasir_225_desember + $nilai_batu1020_225_desember + $nilai_batu2030_225_desember;

			//TOTAL K-250
			$total_semen_250_desember = $komposisi_250_produk_a * $volume_rencana_kerja_desember_produk_c;
			$total_pasir_250_desember = $komposisi_250_produk_b * $volume_rencana_kerja_desember_produk_c;
			$total_batu1020_250_desember = $komposisi_250_produk_c * $volume_rencana_kerja_desember_produk_c;
			$total_batu2030_250_desember = $komposisi_250_produk_d * $volume_rencana_kerja_desember_produk_c;

			$nilai_semen_250_desember = $total_semen_250_desember * $komposisi_250['price_a'];
			$nilai_pasir_250_desember = $total_pasir_250_desember * $komposisi_250['price_b'];
			$nilai_batu1020_250_desember = $total_batu1020_250_desember * $komposisi_250['price_c'];
			$nilai_batu2030_250_desember = $total_batu2030_250_desember * $komposisi_250['price_d'];

			$total_250_desember = $nilai_semen_250_desember + $nilai_pasir_250_desember + $nilai_batu1020_250_desember + $nilai_batu2030_250_desember;

			//TOTAL K-250_18
			$total_semen_250_18_desember = $komposisi_250_18_produk_a * $volume_rencana_kerja_desember_produk_d;
			$total_pasir_250_18_desember = $komposisi_250_18_produk_b * $volume_rencana_kerja_desember_produk_d;
			$total_batu1020_250_18_desember = $komposisi_250_18_produk_c * $volume_rencana_kerja_desember_produk_d;
			$total_batu2030_250_18_desember = $komposisi_250_18_produk_d * $volume_rencana_kerja_desember_produk_d;

			$nilai_semen_250_18_desember = $total_semen_250_18_desember  * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_desember = $total_pasir_250_18_desember  * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_desember = $total_batu1020_250_18_desember  * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_desember = $total_batu2030_250_18_desember  * $komposisi_250_18['price_d'];

			$total_250_18_desember = $nilai_semen_250_18_desember  + $nilai_pasir_250_18_desember  + $nilai_batu1020_250_18_desember  + $nilai_batu2030_250_18_desember;

			//TOTAL ALL
			$total_bahan_all_desember = $total_125_desember  + $total_225_desember  + $total_250_desember  + $total_250_18_desember;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_desember = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_desember_akhir')")
			->get()->row_array();

			$batching_plant_desember = $total_desember_volume * $rap_alat_desember['batching_plant'];
			$truck_mixer_desember = $total_desember_volume * $rap_alat_desember['truck_mixer'];
			$wheel_loader_desember = $total_desember_volume * $rap_alat_desember['wheel_loader'];
			$bbm_solar_desember = $total_desember_volume * $rap_alat_desember['bbm_solar'];
			$biaya_alat_all_desember = $batching_plant_desember + $truck_mixer_desember + $wheel_loader_desember + $bbm_solar_desember;
		
			$total_desember_biaya_bahan = $total_bahan_all_desember;
			$total_desember_biaya_alat = $biaya_alat_all_desember;
			$total_desember_biaya_overhead = $rencana_kerja_desember['biaya_overhead'];
			$total_desember_biaya_bank = $rencana_kerja_desember['biaya_bank'];
			$total_desember_biaya_persiapan = $rencana_kerja_desember['biaya_persiapan'];

			$total_biaya_desember_biaya = $total_desember_biaya_bahan + $total_desember_biaya_alat + $total_desember_biaya_overhead + $total_desember_biaya_bank + $total_desember_biaya_persiapan;
			?>
			<!-- DESEMBER -->

			<!-- JANUARI -->
			<?php
			$date_januari_awal = date('2023-01-01');
			$date_januari_akhir = date('2023-01-31');
			$rencana_kerja_januari = $this->db->select('r.*')
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

			$total_januari_nilai = $nilai_jual_all_januari;

			//TOTAL PEMAKAIAN BAHAN
			$rencana_kerja_januari = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_januari_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_januari_produk_a = $rencana_kerja_januari['vol_produk_a'];
			$volume_rencana_kerja_januari_produk_b = $rencana_kerja_januari['vol_produk_b'];
			$volume_rencana_kerja_januari_produk_c = $rencana_kerja_januari['vol_produk_c'];
			$volume_rencana_kerja_januari_produk_d = $rencana_kerja_januari['vol_produk_d'];

			//TOTAL K-125
			$total_semen_125_januari = $komposisi_125_produk_a * $volume_rencana_kerja_januari_produk_a;
			$total_pasir_125_januari = $komposisi_125_produk_b * $volume_rencana_kerja_januari_produk_a;
			$total_batu1020_125_januari = $komposisi_125_produk_c * $volume_rencana_kerja_januari_produk_a;
			$total_batu2030_125_januari = $komposisi_125_produk_d * $volume_rencana_kerja_januari_produk_a;

			$nilai_semen_125_januari = $total_semen_125_januari * $komposisi_125['price_a'];
			$nilai_pasir_125_januari = $total_pasir_125_januari * $komposisi_125['price_b'];
			$nilai_batu1020_125_januari = $total_batu1020_125_januari * $komposisi_125['price_c'];
			$nilai_batu2030_125_januari = $total_batu2030_125_januari * $komposisi_125['price_d'];

			$total_125_januari = $nilai_semen_125_januari + $nilai_pasir_125_januari + $nilai_batu1020_125_januari + $nilai_batu2030_125_januari;

			//TOTAL K-225
			$total_semen_225_januari = $komposisi_225_produk_a * $volume_rencana_kerja_januari_produk_b;
			$total_pasir_225_januari = $komposisi_225_produk_b * $volume_rencana_kerja_januari_produk_b;
			$total_batu1020_225_januari = $komposisi_225_produk_c * $volume_rencana_kerja_januari_produk_b;
			$total_batu2030_225_januari = $komposisi_225_produk_d * $volume_rencana_kerja_januari_produk_b;

			$nilai_semen_225_januari = $total_semen_225_januari * $komposisi_225['price_a'];
			$nilai_pasir_225_januari = $total_pasir_225_januari * $komposisi_225['price_b'];
			$nilai_batu1020_225_januari = $total_batu1020_225_januari * $komposisi_225['price_c'];
			$nilai_batu2030_225_januari = $total_batu2030_225_januari * $komposisi_225['price_d'];

			$total_225_januari = $nilai_semen_225_januari + $nilai_pasir_225_januari + $nilai_batu1020_225_januari + $nilai_batu2030_225_januari;

			//TOTAL K-250
			$total_semen_250_januari = $komposisi_250_produk_a * $volume_rencana_kerja_januari_produk_c;
			$total_pasir_250_januari = $komposisi_250_produk_b * $volume_rencana_kerja_januari_produk_c;
			$total_batu1020_250_januari = $komposisi_250_produk_c * $volume_rencana_kerja_januari_produk_c;
			$total_batu2030_250_januari = $komposisi_250_produk_d * $volume_rencana_kerja_januari_produk_c;

			$nilai_semen_250_januari = $total_semen_250_januari * $komposisi_250['price_a'];
			$nilai_pasir_250_januari = $total_pasir_250_januari * $komposisi_250['price_b'];
			$nilai_batu1020_250_januari = $total_batu1020_250_januari * $komposisi_250['price_c'];
			$nilai_batu2030_250_januari = $total_batu2030_250_januari * $komposisi_250['price_d'];

			$total_250_januari = $nilai_semen_250_januari + $nilai_pasir_250_januari + $nilai_batu1020_250_januari + $nilai_batu2030_250_januari;

			//TOTAL K-250_18
			$total_semen_250_18_januari = $komposisi_250_18_produk_a * $volume_rencana_kerja_januari_produk_d;
			$total_pasir_250_18_januari = $komposisi_250_18_produk_b * $volume_rencana_kerja_januari_produk_d;
			$total_batu1020_250_18_januari = $komposisi_250_18_produk_c * $volume_rencana_kerja_januari_produk_d;
			$total_batu2030_250_18_januari = $komposisi_250_18_produk_d * $volume_rencana_kerja_januari_produk_d;

			$nilai_semen_250_18_januari = $total_semen_250_18_januari  * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_januari = $total_pasir_250_18_januari  * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_januari = $total_batu1020_250_18_januari  * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_januari = $total_batu2030_250_18_januari  * $komposisi_250_18['price_d'];

			$total_250_18_januari = $nilai_semen_250_18_januari  + $nilai_pasir_250_18_januari  + $nilai_batu1020_250_18_januari  + $nilai_batu2030_250_18_januari;

			//TOTAL ALL
			$total_bahan_all_januari = $total_125_januari  + $total_225_januari  + $total_250_januari  + $total_250_18_januari;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_januari = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_januari_akhir')")
			->get()->row_array();

			$batching_plant_januari = $total_januari_volume * $rap_alat_januari['batching_plant'];
			$truck_mixer_januari = $total_januari_volume * $rap_alat_januari['truck_mixer'];
			$wheel_loader_januari = $total_januari_volume * $rap_alat_januari['wheel_loader'];
			$bbm_solar_januari = $total_januari_volume * $rap_alat_januari['bbm_solar'];
			$biaya_alat_all_januari = $batching_plant_januari + $truck_mixer_januari + $wheel_loader_januari + $bbm_solar_januari;
		
			$total_januari_biaya_bahan = $total_bahan_all_januari;
			$total_januari_biaya_alat = $biaya_alat_all_januari;
			$total_januari_biaya_overhead = $rencana_kerja_januari['biaya_overhead'];
			$total_januari_biaya_bank = $rencana_kerja_januari['biaya_bank'];
			$total_januari_biaya_persiapan = $rencana_kerja_januari['biaya_persiapan'];

			$total_biaya_januari_biaya = $total_januari_biaya_bahan + $total_januari_biaya_alat + $total_januari_biaya_overhead + $total_januari_biaya_bank + $total_januari_biaya_persiapan;
			?>
			<!-- JANUARI -->

			<!-- FEBRUARI -->
			<?php
			$date_februari_awal = date('2023-02-01');
			$date_februari_akhir = date('2023-02-28');
			$rencana_kerja_februari = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_februari_awal' and '$date_februari_akhir'")
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

			$total_februari_nilai = $nilai_jual_all_februari;

			//TOTAL PEMAKAIAN BAHAN
			$rencana_kerja_februari = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_februari_awal' and '$date_februari_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_februari_produk_a = $rencana_kerja_februari['vol_produk_a'];
			$volume_rencana_kerja_februari_produk_b = $rencana_kerja_februari['vol_produk_b'];
			$volume_rencana_kerja_februari_produk_c = $rencana_kerja_februari['vol_produk_c'];
			$volume_rencana_kerja_februari_produk_d = $rencana_kerja_februari['vol_produk_d'];

			//TOTAL K-125
			$total_semen_125_februari = $komposisi_125_produk_a * $volume_rencana_kerja_februari_produk_a;
			$total_pasir_125_februari = $komposisi_125_produk_b * $volume_rencana_kerja_februari_produk_a;
			$total_batu1020_125_februari = $komposisi_125_produk_c * $volume_rencana_kerja_februari_produk_a;
			$total_batu2030_125_februari = $komposisi_125_produk_d * $volume_rencana_kerja_februari_produk_a;

			$nilai_semen_125_februari = $total_semen_125_februari * $komposisi_125['price_a'];
			$nilai_pasir_125_februari = $total_pasir_125_februari * $komposisi_125['price_b'];
			$nilai_batu1020_125_februari = $total_batu1020_125_februari * $komposisi_125['price_c'];
			$nilai_batu2030_125_februari = $total_batu2030_125_februari * $komposisi_125['price_d'];

			$total_125_februari = $nilai_semen_125_februari + $nilai_pasir_125_februari + $nilai_batu1020_125_februari + $nilai_batu2030_125_februari;

			//TOTAL K-225
			$total_semen_225_februari = $komposisi_225_produk_a * $volume_rencana_kerja_februari_produk_b;
			$total_pasir_225_februari = $komposisi_225_produk_b * $volume_rencana_kerja_februari_produk_b;
			$total_batu1020_225_februari = $komposisi_225_produk_c * $volume_rencana_kerja_februari_produk_b;
			$total_batu2030_225_februari = $komposisi_225_produk_d * $volume_rencana_kerja_februari_produk_b;

			$nilai_semen_225_februari = $total_semen_225_februari * $komposisi_225['price_a'];
			$nilai_pasir_225_februari = $total_pasir_225_februari * $komposisi_225['price_b'];
			$nilai_batu1020_225_februari = $total_batu1020_225_februari * $komposisi_225['price_c'];
			$nilai_batu2030_225_februari = $total_batu2030_225_februari * $komposisi_225['price_d'];

			$total_225_februari = $nilai_semen_225_februari + $nilai_pasir_225_februari + $nilai_batu1020_225_februari + $nilai_batu2030_225_februari;

			//TOTAL K-250
			$total_semen_250_februari = $komposisi_250_produk_a * $volume_rencana_kerja_februari_produk_c;
			$total_pasir_250_februari = $komposisi_250_produk_b * $volume_rencana_kerja_februari_produk_c;
			$total_batu1020_250_februari = $komposisi_250_produk_c * $volume_rencana_kerja_februari_produk_c;
			$total_batu2030_250_februari = $komposisi_250_produk_d * $volume_rencana_kerja_februari_produk_c;

			$nilai_semen_250_februari = $total_semen_250_februari * $komposisi_250['price_a'];
			$nilai_pasir_250_februari = $total_pasir_250_februari * $komposisi_250['price_b'];
			$nilai_batu1020_250_februari = $total_batu1020_250_februari * $komposisi_250['price_c'];
			$nilai_batu2030_250_februari = $total_batu2030_250_februari * $komposisi_250['price_d'];

			$total_250_februari = $nilai_semen_250_februari + $nilai_pasir_250_februari + $nilai_batu1020_250_februari + $nilai_batu2030_250_februari;

			//TOTAL K-250_18
			$total_semen_250_18_februari = $komposisi_250_18_produk_a * $volume_rencana_kerja_februari_produk_d;
			$total_pasir_250_18_februari = $komposisi_250_18_produk_b * $volume_rencana_kerja_februari_produk_d;
			$total_batu1020_250_18_februari = $komposisi_250_18_produk_c * $volume_rencana_kerja_februari_produk_d;
			$total_batu2030_250_18_februari = $komposisi_250_18_produk_d * $volume_rencana_kerja_februari_produk_d;

			$nilai_semen_250_18_februari = $total_semen_250_18_februari  * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_februari = $total_pasir_250_18_februari  * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_februari = $total_batu1020_250_18_februari  * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_februari = $total_batu2030_250_18_februari  * $komposisi_250_18['price_d'];

			$total_250_18_februari = $nilai_semen_250_18_februari  + $nilai_pasir_250_18_februari  + $nilai_batu1020_250_18_februari  + $nilai_batu2030_250_18_februari;

			//TOTAL ALL
			$total_bahan_all_februari = $total_125_februari  + $total_225_februari  + $total_250_februari  + $total_250_18_februari;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_februari = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_februari_akhir')")
			->get()->row_array();

			$batching_plant_februari = $total_februari_volume * $rap_alat_februari['batching_plant'];
			$truck_mixer_februari = $total_februari_volume * $rap_alat_februari['truck_mixer'];
			$wheel_loader_februari = $total_februari_volume * $rap_alat_februari['wheel_loader'];
			$bbm_solar_februari = $total_februari_volume * $rap_alat_februari['bbm_solar'];
			$biaya_alat_all_februari = $batching_plant_februari + $truck_mixer_februari + $wheel_loader_februari + $bbm_solar_februari;
		
			$total_februari_biaya_bahan = $total_bahan_all_februari;
			$total_februari_biaya_alat = $biaya_alat_all_februari;
			$total_februari_biaya_overhead = $rencana_kerja_februari['biaya_overhead'];
			$total_februari_biaya_bank = $rencana_kerja_februari['biaya_bank'];
			$total_februari_biaya_persiapan = $rencana_kerja_februari['biaya_persiapan'];

			$total_biaya_februari_biaya = $total_februari_biaya_bahan + $total_februari_biaya_alat + $total_februari_biaya_overhead + $total_februari_biaya_bank + $total_februari_biaya_persiapan;
			?>
			<!-- FEBRUARI -->

			<!-- MARET -->
			<?php
			$date_maret_awal = date('2023-03-01');
			$date_maret_akhir = date('2023-03-31');
			$rencana_kerja_maret = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_maret_awal' and '$date_maret_akhir'")
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

			$total_maret_nilai = $nilai_jual_all_maret;

			//TOTAL PEMAKAIAN BAHAN
			$rencana_kerja_maret = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_maret_awal' and '$date_maret_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_maret_produk_a = $rencana_kerja_maret['vol_produk_a'];
			$volume_rencana_kerja_maret_produk_b = $rencana_kerja_maret['vol_produk_b'];
			$volume_rencana_kerja_maret_produk_c = $rencana_kerja_maret['vol_produk_c'];
			$volume_rencana_kerja_maret_produk_d = $rencana_kerja_maret['vol_produk_d'];

			//TOTAL K-125
			$total_semen_125_maret = $komposisi_125_produk_a * $volume_rencana_kerja_maret_produk_a;
			$total_pasir_125_maret = $komposisi_125_produk_b * $volume_rencana_kerja_maret_produk_a;
			$total_batu1020_125_maret = $komposisi_125_produk_c * $volume_rencana_kerja_maret_produk_a;
			$total_batu2030_125_maret = $komposisi_125_produk_d * $volume_rencana_kerja_maret_produk_a;

			$nilai_semen_125_maret = $total_semen_125_maret * $komposisi_125['price_a'];
			$nilai_pasir_125_maret = $total_pasir_125_maret * $komposisi_125['price_b'];
			$nilai_batu1020_125_maret = $total_batu1020_125_maret * $komposisi_125['price_c'];
			$nilai_batu2030_125_maret = $total_batu2030_125_maret * $komposisi_125['price_d'];

			$total_125_maret = $nilai_semen_125_maret + $nilai_pasir_125_maret + $nilai_batu1020_125_maret + $nilai_batu2030_125_maret;

			//TOTAL K-225
			$total_semen_225_maret = $komposisi_225_produk_a * $volume_rencana_kerja_maret_produk_b;
			$total_pasir_225_maret = $komposisi_225_produk_b * $volume_rencana_kerja_maret_produk_b;
			$total_batu1020_225_maret = $komposisi_225_produk_c * $volume_rencana_kerja_maret_produk_b;
			$total_batu2030_225_maret = $komposisi_225_produk_d * $volume_rencana_kerja_maret_produk_b;

			$nilai_semen_225_maret = $total_semen_225_maret * $komposisi_225['price_a'];
			$nilai_pasir_225_maret = $total_pasir_225_maret * $komposisi_225['price_b'];
			$nilai_batu1020_225_maret = $total_batu1020_225_maret * $komposisi_225['price_c'];
			$nilai_batu2030_225_maret = $total_batu2030_225_maret * $komposisi_225['price_d'];

			$total_225_maret = $nilai_semen_225_maret + $nilai_pasir_225_maret + $nilai_batu1020_225_maret + $nilai_batu2030_225_maret;

			//TOTAL K-250
			$total_semen_250_maret = $komposisi_250_produk_a * $volume_rencana_kerja_maret_produk_c;
			$total_pasir_250_maret = $komposisi_250_produk_b * $volume_rencana_kerja_maret_produk_c;
			$total_batu1020_250_maret = $komposisi_250_produk_c * $volume_rencana_kerja_maret_produk_c;
			$total_batu2030_250_maret = $komposisi_250_produk_d * $volume_rencana_kerja_maret_produk_c;

			$nilai_semen_250_maret = $total_semen_250_maret * $komposisi_250['price_a'];
			$nilai_pasir_250_maret = $total_pasir_250_maret * $komposisi_250['price_b'];
			$nilai_batu1020_250_maret = $total_batu1020_250_maret * $komposisi_250['price_c'];
			$nilai_batu2030_250_maret = $total_batu2030_250_maret * $komposisi_250['price_d'];

			$total_250_maret = $nilai_semen_250_maret + $nilai_pasir_250_maret + $nilai_batu1020_250_maret + $nilai_batu2030_250_maret;

			//TOTAL K-250_18
			$total_semen_250_18_maret = $komposisi_250_18_produk_a * $volume_rencana_kerja_maret_produk_d;
			$total_pasir_250_18_maret = $komposisi_250_18_produk_b * $volume_rencana_kerja_maret_produk_d;
			$total_batu1020_250_18_maret = $komposisi_250_18_produk_c * $volume_rencana_kerja_maret_produk_d;
			$total_batu2030_250_18_maret = $komposisi_250_18_produk_d * $volume_rencana_kerja_maret_produk_d;

			$nilai_semen_250_18_maret = $total_semen_250_18_maret  * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_maret = $total_pasir_250_18_maret  * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_maret = $total_batu1020_250_18_maret  * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_maret = $total_batu2030_250_18_maret  * $komposisi_250_18['price_d'];

			$total_250_18_maret = $nilai_semen_250_18_maret  + $nilai_pasir_250_18_maret  + $nilai_batu1020_250_18_maret  + $nilai_batu2030_250_18_maret;

			//TOTAL ALL
			$total_bahan_all_maret = $total_125_maret  + $total_225_maret  + $total_250_maret  + $total_250_18_maret;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_maret = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_maret_akhir')")
			->get()->row_array();

			$batching_plant_maret = $total_maret_volume * $rap_alat_maret['batching_plant'];
			$truck_mixer_maret = $total_maret_volume * $rap_alat_maret['truck_mixer'];
			$wheel_loader_maret = $total_maret_volume * $rap_alat_maret['wheel_loader'];
			$bbm_solar_maret = $total_maret_volume * $rap_alat_maret['bbm_solar'];
			$biaya_alat_all_maret = $batching_plant_maret + $truck_mixer_maret + $wheel_loader_maret + $bbm_solar_maret;
		
			$total_maret_biaya_bahan = $total_bahan_all_maret;
			$total_maret_biaya_alat = $biaya_alat_all_maret;
			$total_maret_biaya_overhead = $rencana_kerja_maret['biaya_overhead'];
			$total_maret_biaya_bank = $rencana_kerja_maret['biaya_bank'];
			$total_maret_biaya_persiapan = $rencana_kerja_maret['biaya_persiapan'];

			$total_biaya_maret_biaya = $total_maret_biaya_bahan + $total_maret_biaya_alat + $total_maret_biaya_overhead + $total_maret_biaya_bank + $total_maret_biaya_persiapan;
			?>
			<!-- MARET -->

			<!-- APRIL -->
			<?php
			$date_april_awal = date('2023-04-01');
			$date_april_akhir = date('2023-04-30');
			$rencana_kerja_april = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_april_awal' and '$date_april_akhir'")
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

			$total_april_nilai = $nilai_jual_all_april;

			//TOTAL PEMAKAIAN BAHAN
			$rencana_kerja_april = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_april_awal' and '$date_april_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_april_produk_a = $rencana_kerja_april['vol_produk_a'];
			$volume_rencana_kerja_april_produk_b = $rencana_kerja_april['vol_produk_b'];
			$volume_rencana_kerja_april_produk_c = $rencana_kerja_april['vol_produk_c'];
			$volume_rencana_kerja_april_produk_d = $rencana_kerja_april['vol_produk_d'];

			//TOTAL K-125
			$total_semen_125_april = $komposisi_125_produk_a * $volume_rencana_kerja_april_produk_a;
			$total_pasir_125_april = $komposisi_125_produk_b * $volume_rencana_kerja_april_produk_a;
			$total_batu1020_125_april = $komposisi_125_produk_c * $volume_rencana_kerja_april_produk_a;
			$total_batu2030_125_april = $komposisi_125_produk_d * $volume_rencana_kerja_april_produk_a;

			$nilai_semen_125_april = $total_semen_125_april * $komposisi_125['price_a'];
			$nilai_pasir_125_april = $total_pasir_125_april * $komposisi_125['price_b'];
			$nilai_batu1020_125_april = $total_batu1020_125_april * $komposisi_125['price_c'];
			$nilai_batu2030_125_april = $total_batu2030_125_april * $komposisi_125['price_d'];

			$total_125_april = $nilai_semen_125_april + $nilai_pasir_125_april + $nilai_batu1020_125_april + $nilai_batu2030_125_april;

			//TOTAL K-225
			$total_semen_225_april = $komposisi_225_produk_a * $volume_rencana_kerja_april_produk_b;
			$total_pasir_225_april = $komposisi_225_produk_b * $volume_rencana_kerja_april_produk_b;
			$total_batu1020_225_april = $komposisi_225_produk_c * $volume_rencana_kerja_april_produk_b;
			$total_batu2030_225_april = $komposisi_225_produk_d * $volume_rencana_kerja_april_produk_b;

			$nilai_semen_225_april = $total_semen_225_april * $komposisi_225['price_a'];
			$nilai_pasir_225_april = $total_pasir_225_april * $komposisi_225['price_b'];
			$nilai_batu1020_225_april = $total_batu1020_225_april * $komposisi_225['price_c'];
			$nilai_batu2030_225_april = $total_batu2030_225_april * $komposisi_225['price_d'];

			$total_225_april = $nilai_semen_225_april + $nilai_pasir_225_april + $nilai_batu1020_225_april + $nilai_batu2030_225_april;

			//TOTAL K-250
			$total_semen_250_april = $komposisi_250_produk_a * $volume_rencana_kerja_april_produk_c;
			$total_pasir_250_april = $komposisi_250_produk_b * $volume_rencana_kerja_april_produk_c;
			$total_batu1020_250_april = $komposisi_250_produk_c * $volume_rencana_kerja_april_produk_c;
			$total_batu2030_250_april = $komposisi_250_produk_d * $volume_rencana_kerja_april_produk_c;

			$nilai_semen_250_april = $total_semen_250_april * $komposisi_250['price_a'];
			$nilai_pasir_250_april = $total_pasir_250_april * $komposisi_250['price_b'];
			$nilai_batu1020_250_april = $total_batu1020_250_april * $komposisi_250['price_c'];
			$nilai_batu2030_250_april = $total_batu2030_250_april * $komposisi_250['price_d'];

			$total_250_april = $nilai_semen_250_april + $nilai_pasir_250_april + $nilai_batu1020_250_april + $nilai_batu2030_250_april;

			//TOTAL K-250_18
			$total_semen_250_18_april = $komposisi_250_18_produk_a * $volume_rencana_kerja_april_produk_d;
			$total_pasir_250_18_april = $komposisi_250_18_produk_b * $volume_rencana_kerja_april_produk_d;
			$total_batu1020_250_18_april = $komposisi_250_18_produk_c * $volume_rencana_kerja_april_produk_d;
			$total_batu2030_250_18_april = $komposisi_250_18_produk_d * $volume_rencana_kerja_april_produk_d;

			$nilai_semen_250_18_april = $total_semen_250_18_april  * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_april = $total_pasir_250_18_april  * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_april = $total_batu1020_250_18_april  * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_april = $total_batu2030_250_18_april  * $komposisi_250_18['price_d'];

			$total_250_18_april = $nilai_semen_250_18_april  + $nilai_pasir_250_18_april  + $nilai_batu1020_250_18_april  + $nilai_batu2030_250_18_april;

			//TOTAL ALL
			$total_bahan_all_april = $total_125_april  + $total_225_april  + $total_250_april  + $total_250_18_april;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_april = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_april_akhir')")
			->get()->row_array();

			$batching_plant_april = $total_april_volume * $rap_alat_april['batching_plant'];
			$truck_mixer_april = $total_april_volume * $rap_alat_april['truck_mixer'];
			$wheel_loader_april = $total_april_volume * $rap_alat_april['wheel_loader'];
			$bbm_solar_april = $total_april_volume * $rap_alat_april['bbm_solar'];
			$biaya_alat_all_april = $batching_plant_april + $truck_mixer_april + $wheel_loader_april + $bbm_solar_april;
		
			$total_april_biaya_bahan = $total_bahan_all_april;
			$total_april_biaya_alat = $biaya_alat_all_april;
			$total_april_biaya_overhead = $rencana_kerja_april['biaya_overhead'];
			$total_april_biaya_bank = $rencana_kerja_april['biaya_bank'];
			$total_april_biaya_persiapan = $rencana_kerja_april['biaya_persiapan'];

			$total_biaya_april_biaya = $total_april_biaya_bahan + $total_april_biaya_alat + $total_april_biaya_overhead + $total_april_biaya_bank + $total_april_biaya_persiapan;
			?>
			<!-- APRIL -->

			<!-- MEI -->
			<?php
			$date_mei_awal = date('2023-05-01');
			$date_mei_akhir = date('2023-05-31');
			$rencana_kerja_mei = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_mei_awal' and '$date_mei_akhir'")
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

			$total_mei_nilai = $nilai_jual_all_mei;

			//TOTAL PEMAKAIAN BAHAN
			$rencana_kerja_mei = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_mei_awal' and '$date_mei_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_mei_produk_a = $rencana_kerja_mei['vol_produk_a'];
			$volume_rencana_kerja_mei_produk_b = $rencana_kerja_mei['vol_produk_b'];
			$volume_rencana_kerja_mei_produk_c = $rencana_kerja_mei['vol_produk_c'];
			$volume_rencana_kerja_mei_produk_d = $rencana_kerja_mei['vol_produk_d'];

			//TOTAL K-125
			$total_semen_125_mei = $komposisi_125_produk_a * $volume_rencana_kerja_mei_produk_a;
			$total_pasir_125_mei = $komposisi_125_produk_b * $volume_rencana_kerja_mei_produk_a;
			$total_batu1020_125_mei = $komposisi_125_produk_c * $volume_rencana_kerja_mei_produk_a;
			$total_batu2030_125_mei = $komposisi_125_produk_d * $volume_rencana_kerja_mei_produk_a;

			$nilai_semen_125_mei = $total_semen_125_mei * $komposisi_125['price_a'];
			$nilai_pasir_125_mei = $total_pasir_125_mei * $komposisi_125['price_b'];
			$nilai_batu1020_125_mei = $total_batu1020_125_mei * $komposisi_125['price_c'];
			$nilai_batu2030_125_mei = $total_batu2030_125_mei * $komposisi_125['price_d'];

			$total_125_mei = $nilai_semen_125_mei + $nilai_pasir_125_mei + $nilai_batu1020_125_mei + $nilai_batu2030_125_mei;

			//TOTAL K-225
			$total_semen_225_mei = $komposisi_225_produk_a * $volume_rencana_kerja_mei_produk_b;
			$total_pasir_225_mei = $komposisi_225_produk_b * $volume_rencana_kerja_mei_produk_b;
			$total_batu1020_225_mei = $komposisi_225_produk_c * $volume_rencana_kerja_mei_produk_b;
			$total_batu2030_225_mei = $komposisi_225_produk_d * $volume_rencana_kerja_mei_produk_b;

			$nilai_semen_225_mei = $total_semen_225_mei * $komposisi_225['price_a'];
			$nilai_pasir_225_mei = $total_pasir_225_mei * $komposisi_225['price_b'];
			$nilai_batu1020_225_mei = $total_batu1020_225_mei * $komposisi_225['price_c'];
			$nilai_batu2030_225_mei = $total_batu2030_225_mei * $komposisi_225['price_d'];

			$total_225_mei = $nilai_semen_225_mei + $nilai_pasir_225_mei + $nilai_batu1020_225_mei + $nilai_batu2030_225_mei;

			//TOTAL K-250
			$total_semen_250_mei = $komposisi_250_produk_a * $volume_rencana_kerja_mei_produk_c;
			$total_pasir_250_mei = $komposisi_250_produk_b * $volume_rencana_kerja_mei_produk_c;
			$total_batu1020_250_mei = $komposisi_250_produk_c * $volume_rencana_kerja_mei_produk_c;
			$total_batu2030_250_mei = $komposisi_250_produk_d * $volume_rencana_kerja_mei_produk_c;

			$nilai_semen_250_mei = $total_semen_250_mei * $komposisi_250['price_a'];
			$nilai_pasir_250_mei = $total_pasir_250_mei * $komposisi_250['price_b'];
			$nilai_batu1020_250_mei = $total_batu1020_250_mei * $komposisi_250['price_c'];
			$nilai_batu2030_250_mei = $total_batu2030_250_mei * $komposisi_250['price_d'];

			$total_250_mei = $nilai_semen_250_mei + $nilai_pasir_250_mei + $nilai_batu1020_250_mei + $nilai_batu2030_250_mei;

			//TOTAL K-250_18
			$total_semen_250_18_mei = $komposisi_250_18_produk_a * $volume_rencana_kerja_mei_produk_d;
			$total_pasir_250_18_mei = $komposisi_250_18_produk_b * $volume_rencana_kerja_mei_produk_d;
			$total_batu1020_250_18_mei = $komposisi_250_18_produk_c * $volume_rencana_kerja_mei_produk_d;
			$total_batu2030_250_18_mei = $komposisi_250_18_produk_d * $volume_rencana_kerja_mei_produk_d;

			$nilai_semen_250_18_mei = $total_semen_250_18_mei  * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_mei = $total_pasir_250_18_mei  * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_mei = $total_batu1020_250_18_mei  * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_mei = $total_batu2030_250_18_mei  * $komposisi_250_18['price_d'];

			$total_250_18_mei = $nilai_semen_250_18_mei  + $nilai_pasir_250_18_mei  + $nilai_batu1020_250_18_mei  + $nilai_batu2030_250_18_mei;

			//TOTAL ALL
			$total_bahan_all_mei = $total_125_mei  + $total_225_mei  + $total_250_mei  + $total_250_18_mei;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_mei = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_mei_akhir')")
			->get()->row_array();

			$batching_plant_mei = $total_mei_volume * $rap_alat_mei['batching_plant'];
			$truck_mixer_mei = $total_mei_volume * $rap_alat_mei['truck_mixer'];
			$wheel_loader_mei = $total_mei_volume * $rap_alat_mei['wheel_loader'];
			$bbm_solar_mei = $total_mei_volume * $rap_alat_mei['bbm_solar'];
			$biaya_alat_all_mei = $batching_plant_mei + $truck_mixer_mei + $wheel_loader_mei + $bbm_solar_mei;
		
			$total_mei_biaya_bahan = $total_bahan_all_mei;
			$total_mei_biaya_alat = $biaya_alat_all_mei;
			$total_mei_biaya_overhead = $rencana_kerja_mei['biaya_overhead'];
			$total_mei_biaya_bank = $rencana_kerja_mei['biaya_bank'];
			$total_mei_biaya_persiapan = $rencana_kerja_mei['biaya_persiapan'];

			$total_biaya_mei_biaya = $total_mei_biaya_bahan + $total_mei_biaya_alat + $total_mei_biaya_overhead + $total_mei_biaya_bank + $total_mei_biaya_persiapan;
			?>
			<!-- MEI -->

			<!-- JUNI -->
			<?php
			$date_juni_awal = date('2023-06-01');
			$date_juni_akhir = date('2023-06-30');
			$rencana_kerja_juni = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_juni_awal' and '$date_juni_akhir'")
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

			$total_juni_nilai = $nilai_jual_all_juni;

			//TOTAL PEMAKAIAN BAHAN
			$rencana_kerja_juni = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_juni_awal' and '$date_juni_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_juni_produk_a = $rencana_kerja_juni['vol_produk_a'];
			$volume_rencana_kerja_juni_produk_b = $rencana_kerja_juni['vol_produk_b'];
			$volume_rencana_kerja_juni_produk_c = $rencana_kerja_juni['vol_produk_c'];
			$volume_rencana_kerja_juni_produk_d = $rencana_kerja_juni['vol_produk_d'];

			//TOTAL K-125
			$total_semen_125_juni = $komposisi_125_produk_a * $volume_rencana_kerja_juni_produk_a;
			$total_pasir_125_juni = $komposisi_125_produk_b * $volume_rencana_kerja_juni_produk_a;
			$total_batu1020_125_juni = $komposisi_125_produk_c * $volume_rencana_kerja_juni_produk_a;
			$total_batu2030_125_juni = $komposisi_125_produk_d * $volume_rencana_kerja_juni_produk_a;

			$nilai_semen_125_juni = $total_semen_125_juni * $komposisi_125['price_a'];
			$nilai_pasir_125_juni = $total_pasir_125_juni * $komposisi_125['price_b'];
			$nilai_batu1020_125_juni = $total_batu1020_125_juni * $komposisi_125['price_c'];
			$nilai_batu2030_125_juni = $total_batu2030_125_juni * $komposisi_125['price_d'];

			$total_125_juni = $nilai_semen_125_juni + $nilai_pasir_125_juni + $nilai_batu1020_125_juni + $nilai_batu2030_125_juni;

			//TOTAL K-225
			$total_semen_225_juni = $komposisi_225_produk_a * $volume_rencana_kerja_juni_produk_b;
			$total_pasir_225_juni = $komposisi_225_produk_b * $volume_rencana_kerja_juni_produk_b;
			$total_batu1020_225_juni = $komposisi_225_produk_c * $volume_rencana_kerja_juni_produk_b;
			$total_batu2030_225_juni = $komposisi_225_produk_d * $volume_rencana_kerja_juni_produk_b;

			$nilai_semen_225_juni = $total_semen_225_juni * $komposisi_225['price_a'];
			$nilai_pasir_225_juni = $total_pasir_225_juni * $komposisi_225['price_b'];
			$nilai_batu1020_225_juni = $total_batu1020_225_juni * $komposisi_225['price_c'];
			$nilai_batu2030_225_juni = $total_batu2030_225_juni * $komposisi_225['price_d'];

			$total_225_juni = $nilai_semen_225_juni + $nilai_pasir_225_juni + $nilai_batu1020_225_juni + $nilai_batu2030_225_juni;

			//TOTAL K-250
			$total_semen_250_juni = $komposisi_250_produk_a * $volume_rencana_kerja_juni_produk_c;
			$total_pasir_250_juni = $komposisi_250_produk_b * $volume_rencana_kerja_juni_produk_c;
			$total_batu1020_250_juni = $komposisi_250_produk_c * $volume_rencana_kerja_juni_produk_c;
			$total_batu2030_250_juni = $komposisi_250_produk_d * $volume_rencana_kerja_juni_produk_c;

			$nilai_semen_250_juni = $total_semen_250_juni * $komposisi_250['price_a'];
			$nilai_pasir_250_juni = $total_pasir_250_juni * $komposisi_250['price_b'];
			$nilai_batu1020_250_juni = $total_batu1020_250_juni * $komposisi_250['price_c'];
			$nilai_batu2030_250_juni = $total_batu2030_250_juni * $komposisi_250['price_d'];

			$total_250_juni = $nilai_semen_250_juni + $nilai_pasir_250_juni + $nilai_batu1020_250_juni + $nilai_batu2030_250_juni;

			//TOTAL K-250_18
			$total_semen_250_18_juni = $komposisi_250_18_produk_a * $volume_rencana_kerja_juni_produk_d;
			$total_pasir_250_18_juni = $komposisi_250_18_produk_b * $volume_rencana_kerja_juni_produk_d;
			$total_batu1020_250_18_juni = $komposisi_250_18_produk_c * $volume_rencana_kerja_juni_produk_d;
			$total_batu2030_250_18_juni = $komposisi_250_18_produk_d * $volume_rencana_kerja_juni_produk_d;

			$nilai_semen_250_18_juni = $total_semen_250_18_juni  * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_juni = $total_pasir_250_18_juni  * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_juni = $total_batu1020_250_18_juni  * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_juni = $total_batu2030_250_18_juni  * $komposisi_250_18['price_d'];

			$total_250_18_juni = $nilai_semen_250_18_juni  + $nilai_pasir_250_18_juni  + $nilai_batu1020_250_18_juni  + $nilai_batu2030_250_18_juni;

			//TOTAL ALL
			$total_bahan_all_juni = $total_125_juni  + $total_225_juni  + $total_250_juni  + $total_250_18_juni;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_juni = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_juni_akhir')")
			->get()->row_array();

			$batching_plant_juni = $total_juni_volume * $rap_alat_juni['batching_plant'];
			$truck_mixer_juni = $total_juni_volume * $rap_alat_juni['truck_mixer'];
			$wheel_loader_juni = $total_juni_volume * $rap_alat_juni['wheel_loader'];
			$bbm_solar_juni = $total_juni_volume * $rap_alat_juni['bbm_solar'];
			$biaya_alat_all_juni = $batching_plant_juni + $truck_mixer_juni + $wheel_loader_juni + $bbm_solar_juni;
		
			$total_juni_biaya_bahan = $total_bahan_all_juni;
			$total_juni_biaya_alat = $biaya_alat_all_juni;
			$total_juni_biaya_overhead = $rencana_kerja_juni['biaya_overhead'];
			$total_juni_biaya_bank = $rencana_kerja_juni['biaya_bank'];
			$total_juni_biaya_persiapan = $rencana_kerja_juni['biaya_persiapan'];

			$total_biaya_juni_biaya = $total_juni_biaya_bahan + $total_juni_biaya_alat + $total_juni_biaya_overhead + $total_juni_biaya_bank + $total_juni_biaya_persiapan;
			?>
			<!-- JUNI -->

			<!-- JULI -->
			<?php
			$date_juli_awal = date('2023-07-01');
			$date_juli_akhir = date('2023-07-31');
			$rencana_kerja_juli = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_juli_awal' and '$date_juli_akhir'")
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

			$total_juli_nilai = $nilai_jual_all_juli;

			//TOTAL PEMAKAIAN BAHAN
			$rencana_kerja_juli = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_juli_awal' and '$date_juli_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_juli_produk_a = $rencana_kerja_juli['vol_produk_a'];
			$volume_rencana_kerja_juli_produk_b = $rencana_kerja_juli['vol_produk_b'];
			$volume_rencana_kerja_juli_produk_c = $rencana_kerja_juli['vol_produk_c'];
			$volume_rencana_kerja_juli_produk_d = $rencana_kerja_juli['vol_produk_d'];

			//TOTAL K-125
			$total_semen_125_juli = $komposisi_125_produk_a * $volume_rencana_kerja_juli_produk_a;
			$total_pasir_125_juli = $komposisi_125_produk_b * $volume_rencana_kerja_juli_produk_a;
			$total_batu1020_125_juli = $komposisi_125_produk_c * $volume_rencana_kerja_juli_produk_a;
			$total_batu2030_125_juli = $komposisi_125_produk_d * $volume_rencana_kerja_juli_produk_a;

			$nilai_semen_125_juli = $total_semen_125_juli * $komposisi_125['price_a'];
			$nilai_pasir_125_juli = $total_pasir_125_juli * $komposisi_125['price_b'];
			$nilai_batu1020_125_juli = $total_batu1020_125_juli * $komposisi_125['price_c'];
			$nilai_batu2030_125_juli = $total_batu2030_125_juli * $komposisi_125['price_d'];

			$total_125_juli = $nilai_semen_125_juli + $nilai_pasir_125_juli + $nilai_batu1020_125_juli + $nilai_batu2030_125_juli;

			//TOTAL K-225
			$total_semen_225_juli = $komposisi_225_produk_a * $volume_rencana_kerja_juli_produk_b;
			$total_pasir_225_juli = $komposisi_225_produk_b * $volume_rencana_kerja_juli_produk_b;
			$total_batu1020_225_juli = $komposisi_225_produk_c * $volume_rencana_kerja_juli_produk_b;
			$total_batu2030_225_juli = $komposisi_225_produk_d * $volume_rencana_kerja_juli_produk_b;

			$nilai_semen_225_juli = $total_semen_225_juli * $komposisi_225['price_a'];
			$nilai_pasir_225_juli = $total_pasir_225_juli * $komposisi_225['price_b'];
			$nilai_batu1020_225_juli = $total_batu1020_225_juli * $komposisi_225['price_c'];
			$nilai_batu2030_225_juli = $total_batu2030_225_juli * $komposisi_225['price_d'];

			$total_225_juli = $nilai_semen_225_juli + $nilai_pasir_225_juli + $nilai_batu1020_225_juli + $nilai_batu2030_225_juli;

			//TOTAL K-250
			$total_semen_250_juli = $komposisi_250_produk_a * $volume_rencana_kerja_juli_produk_c;
			$total_pasir_250_juli = $komposisi_250_produk_b * $volume_rencana_kerja_juli_produk_c;
			$total_batu1020_250_juli = $komposisi_250_produk_c * $volume_rencana_kerja_juli_produk_c;
			$total_batu2030_250_juli = $komposisi_250_produk_d * $volume_rencana_kerja_juli_produk_c;

			$nilai_semen_250_juli = $total_semen_250_juli * $komposisi_250['price_a'];
			$nilai_pasir_250_juli = $total_pasir_250_juli * $komposisi_250['price_b'];
			$nilai_batu1020_250_juli = $total_batu1020_250_juli * $komposisi_250['price_c'];
			$nilai_batu2030_250_juli = $total_batu2030_250_juli * $komposisi_250['price_d'];

			$total_250_juli = $nilai_semen_250_juli + $nilai_pasir_250_juli + $nilai_batu1020_250_juli + $nilai_batu2030_250_juli;

			//TOTAL K-250_18
			$total_semen_250_18_juli = $komposisi_250_18_produk_a * $volume_rencana_kerja_juli_produk_d;
			$total_pasir_250_18_juli = $komposisi_250_18_produk_b * $volume_rencana_kerja_juli_produk_d;
			$total_batu1020_250_18_juli = $komposisi_250_18_produk_c * $volume_rencana_kerja_juli_produk_d;
			$total_batu2030_250_18_juli = $komposisi_250_18_produk_d * $volume_rencana_kerja_juli_produk_d;

			$nilai_semen_250_18_juli = $total_semen_250_18_juli  * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_juli = $total_pasir_250_18_juli  * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_juli = $total_batu1020_250_18_juli  * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_juli = $total_batu2030_250_18_juli  * $komposisi_250_18['price_d'];

			$total_250_18_juli = $nilai_semen_250_18_juli  + $nilai_pasir_250_18_juli  + $nilai_batu1020_250_18_juli  + $nilai_batu2030_250_18_juli;

			//TOTAL ALL
			$total_bahan_all_juli = $total_125_juli  + $total_225_juli  + $total_250_juli  + $total_250_18_juli;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_juli = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_juli_akhir')")
			->get()->row_array();

			$batching_plant_juli = $total_juli_volume * $rap_alat_juli['batching_plant'];
			$truck_mixer_juli = $total_juli_volume * $rap_alat_juli['truck_mixer'];
			$wheel_loader_juli = $total_juli_volume * $rap_alat_juli['wheel_loader'];
			$bbm_solar_juli = $total_juli_volume * $rap_alat_juli['bbm_solar'];
			$biaya_alat_all_juli = $batching_plant_juli + $truck_mixer_juli + $wheel_loader_juli + $bbm_solar_juli;
		
			$total_juli_biaya_bahan = $total_bahan_all_juli;
			$total_juli_biaya_alat = $biaya_alat_all_juli;
			$total_juli_biaya_overhead = $rencana_kerja_juli['biaya_overhead'];
			$total_juli_biaya_bank = $rencana_kerja_juli['biaya_bank'];
			$total_juli_biaya_persiapan = $rencana_kerja_juli['biaya_persiapan'];

			$total_biaya_juli_biaya = $total_juli_biaya_bahan + $total_juli_biaya_alat + $total_juli_biaya_overhead + $total_juli_biaya_bank + $total_juli_biaya_persiapan;
			?>
			<!-- JULI -->
			

			<!-- AKUMULASI BIAYA SD. SAAT INI -->
			<?php
			//BAHAN
			$akumulasi = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar as total_nilai_keluar')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi <= '$date_now')")
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
			->where("(prm.date_receipt <= '$date_now')")
			->where("p.kategori_produk = '5'")
			->where("po.status in ('PUBLISH','CLOSED')")
			->get()->row_array();

			$akumulasi_bbm = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar_2 as total_nilai_keluar_2')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi <= '$date_now')")
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
			->where("(tanggal_transaksi <= '$date_now')")
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
			->where("(pb.tanggal_transaksi <= '$date_now')")
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
			->where("(pb.tanggal_transaksi <= '$date_now')")
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
			->where("(pb.tanggal_transaksi <= '$date_now')")
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
			->where("(pb.tanggal_transaksi <= '$date_now')")
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
			->where("(pb.tanggal_transaksi <= '$date_now')")
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
			->where("(pb.tanggal_transaksi <= '$date_now')")
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
			->where("(pb.tanggal_transaksi <= '$date_now')")
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
			->where("(pb.tanggal_transaksi <= '$date_now')")
			->get()->row_array();

			$persiapan_jurnal_akumulasi = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 228")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$date_now')")
			->get()->row_array();

			$total_persiapan_akumulasi = $persiapan_biaya_akumulasi['total'] + $persiapan_jurnal_akumulasi['total'];
			$total_biaya_akumulasi = $total_bahan_akumulasi + $total_alat_akumulasi + $total_overhead_akumulasi + $total_diskonto_akumulasi + $total_persiapan_akumulasi;
			//END PERSIAPAN
			?>
			<!-- AKUMULASI BIAYA SD. SAAT INI -->


			<!-- TOTAL -->
			<?php
			$total_all_produk_a = $volume_akumulasi_produk_a + $volume_oktober_produk_a + $volume_november_produk_a + $volume_desember_produk_a + $volume_januari_produk_a + $volume_februari_produk_a + $volume_maret_produk_a + $volume_april_produk_a + $volume_mei_produk_a + $volume_juni_produk_a + $volume_juli_produk_a;
			$total_all_produk_b = $volume_akumulasi_produk_b + $volume_oktober_produk_b + $volume_november_produk_b + $volume_desember_produk_b + $volume_januari_produk_b + $volume_februari_produk_b + $volume_maret_produk_b + $volume_april_produk_b + $volume_mei_produk_b + $volume_juni_produk_b + $volume_juli_produk_b;
			$total_all_produk_c = $volume_akumulasi_produk_c + $volume_oktober_produk_c + $volume_november_produk_c + $volume_desember_produk_c + $volume_januari_produk_c + $volume_februari_produk_c + $volume_maret_produk_c + $volume_april_produk_c + $volume_mei_produk_c + $volume_juni_produk_c + $volume_juli_produk_c;
			$total_all_produk_d = $volume_akumulasi_produk_d + $volume_oktober_produk_d + $volume_november_produk_d + $volume_desember_produk_d + $volume_januari_produk_d + $volume_februari_produk_d + $volume_maret_produk_d + $volume_april_produk_d + $volume_mei_produk_d + $volume_juni_produk_d + $volume_juli_produk_d;

			$total_all_volume = $total_akumulasi_volume + $total_oktober_volume + $total_november_volume + $total_desember_volume + $total_januari_volume + $total_februari_volume + $total_maret_volume + $total_april_volume + $total_mei_volume + $total_juni_volume + $total_juli_volume;
			$total_all_nilai = $total_akumulasi_nilai + $total_oktober_nilai + $total_november_nilai + $total_desember_nilai + $total_januari_nilai + $total_februari_nilai + $total_maret_nilai + $total_april_nilai + $total_mei_nilai + $total_juni_nilai + $total_juli_nilai;

			$total_all_biaya_bahan = $total_bahan_akumulasi + $total_oktober_biaya_bahan + $total_november_biaya_bahan + $total_desember_biaya_bahan + $total_januari_biaya_bahan + $total_februari_biaya_bahan + $total_maret_biaya_bahan + $total_april_biaya_bahan + $total_mei_biaya_bahan + $total_juni_biaya_bahan + $total_juli_biaya_bahan;
			$total_all_biaya_alat = $total_alat_akumulasi + $total_oktober_biaya_alat + $total_november_biaya_alat + $total_desember_biaya_alat + $total_januari_biaya_alat + $total_februari_biaya_alat + $total_maret_biaya_alat + $total_april_biaya_alat + $total_mei_biaya_alat + $total_juni_biaya_alat + $total_juli_biaya_alat;
			$total_all_biaya_overhead = $total_overhead_akumulasi + $total_oktober_biaya_overhead + $total_november_biaya_overhead + $total_desember_biaya_overhead + $total_januari_biaya_overhead + $total_februari_biaya_overhead + $total_maret_biaya_overhead + $total_april_biaya_overhead + $total_mei_biaya_overhead + $total_juni_biaya_overhead + $total_juli_biaya_overhead;
			$total_all_biaya_bank = $total_diskonto_akumulasi + $total_oktober_biaya_bank + $total_november_biaya_bank + $total_desember_biaya_bank + $total_januari_biaya_bank + $total_februari_biaya_bank + $total_maret_biaya_bank + $total_april_biaya_bank + $total_mei_biaya_bank + $total_juni_biaya_bank + $total_juli_biaya_bank;
			$total_all_biaya_persiapan = $total_persiapan_akumulasi + $total_oktober_biaya_persiapan + $total_november_biaya_persiapan + $total_desember_biaya_persiapan + $total_januari_biaya_persiapan + $total_februari_biaya_persiapan + $total_maret_biaya_persiapan + $total_april_biaya_persiapan + $total_mei_biaya_persiapan + $total_juni_biaya_persiapan + $total_juli_biaya_persiapan;
			
			$total_biaya_all_biaya = $total_all_biaya_bahan + $total_all_biaya_alat + $total_all_biaya_overhead + $total_all_biaya_bank + $total_all_biaya_persiapan;

			$total_laba_rap_2022 = $total_rap_nilai_2022 - $total_biaya_rap_2022_biaya;
			$total_laba_sd_agustus = $total_akumulasi_nilai - $total_biaya_akumulasi;
			$total_laba_oktober = $total_oktober_nilai - $total_biaya_oktober_biaya;
			$total_laba_november = $total_november_nilai - $total_biaya_november_biaya;
			$total_laba_desember = $total_desember_nilai - $total_biaya_desember_biaya;
			$total_laba_januari = $total_januari_nilai - $total_biaya_januari_biaya;
			$total_laba_februari = $total_februari_nilai - $total_biaya_februari_biaya;
			$total_laba_maret = $total_maret_nilai - $total_biaya_maret_biaya;
			$total_laba_april = $total_april_nilai - $total_biaya_april_biaya;
			$total_laba_mei = $total_mei_nilai - $total_biaya_mei_biaya;
			$total_laba_juni = $total_juni_nilai - $total_biaya_juni_biaya;
			$total_laba_juli = $total_juli_nilai - $total_biaya_juli_biaya;
			$total_laba_all = $total_all_nilai - $total_biaya_all_biaya;
			?>
			<!-- TOTAL -->

			<!-- SISA -->
			<?php
			$sisa_vol_produk_a = $volume_rap_2022_produk_a - $total_all_produk_a;
			$sisa_vol_produk_b = $volume_rap_2022_produk_b - $total_all_produk_b;
			$sisa_vol_produk_c = $volume_rap_2022_produk_c - $total_all_produk_c;
			$sisa_vol_produk_d = $volume_rap_2022_produk_d - $total_all_produk_d;
			$sisa_all_volume = $total_rap_volume_2022 - $total_all_volume;
			$sisa_all_nilai = $total_rap_nilai_2022 - $total_all_nilai;

			$sisa_all_biaya_bahan = $total_rap_2022_biaya_bahan - $total_all_biaya_bahan;
			$sisa_all_biaya_alat = $total_rap_2022_biaya_alat - $total_all_biaya_alat;
			$sisa_all_biaya_overhead = $total_rap_2022_biaya_overhead - $total_all_biaya_overhead ;
			$sisa_all_biaya_bank = $total_rap_2022_biaya_bank - $total_all_biaya_bank;
			$sisa_all_biaya_persiapan = $total_rap_2022_biaya_persiapan - $total_all_biaya_persiapan;
			$sisa_biaya_all_biaya = $total_biaya_rap_2022_biaya - $total_biaya_all_biaya;
			$x = $total_laba_rap_2022;
			$y = - $total_laba_all;
			$sisa_laba_all = $x - $y;
			?>
			<!-- SISA -->
			
			<tr class="table-judul">
				<th width="3%" align="center" rowspan="2">&nbsp; <br />NO.</th>
				<th width="7%" align="center" rowspan="2">&nbsp; <br />URAIAN</th>
				<th width="4%" align="center" rowspan="2">&nbsp; <br />SATUAN</th>
				<th width="7%" align="center" rowspan="2">&nbsp; <br />RAP 2022</th>
				<th width="7%" align="center">REALISASI</th>
				<th width="58%" align="center" colspan="10">&nbsp; <br />RENCANA KERJA</th>
				<th width="7%" align="center">TOTAL</th>
				<th width="7%" align="center" rowspan="2">&nbsp; <br />SISA</th>
	        </tr>
			<tr class="table-judul">
				<th align="center"> SD. SAAT INI</th>
				<th align="center">OKTOBER<br />2022</th>
				<th align="center">NOVEMBER<br />2022</th>
				<th align="center">DESEMBER<br />2022</th>
				<th align="center">JANUARI<br />2023</th>
				<th align="center">FEBRUARI<br />2023</th>
				<th align="center">MARET<br />2023</th>
				<th align="center">APRIL<br />2023</th>
				<th align="center">MEI<br />2023</th>
				<th align="center">JUNI<br />2023</th>
				<th align="center">JULI<br />2023</th>
				<th align="center">SD. JULI<br />2023</th>
	        </tr>
			<tr class="table-baris1-bold">
				<th align="left" colspan="17">RENCANA PRODUKSI & PENDAPATAN USAHA</th>
			</tr>
			<tr class="table-baris1">
				<th align="center">1</th>
				<th align="left">Beton K 125 (10±2)</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($volume_rap_2022_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_akumulasi_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_oktober_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_november_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_desember_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_januari_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_februari_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_maret_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_april_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_mei_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_juni_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_juli_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_produk_a,2,',','.');?></th>	
				<th align="right"><?php echo number_format($sisa_vol_produk_a,2,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center">2</th>
				<th align="left">Beton K 225 (10±2)</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($volume_rap_2022_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_akumulasi_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_oktober_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_november_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_desember_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_januari_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_februari_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_maret_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_april_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_mei_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_juni_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_juli_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_produk_b,2,',','.');?></th>	
				<th align="right"><?php echo number_format($sisa_vol_produk_b,2,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center">3</th>
				<th align="left">Beton K 250 (10±2)</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($volume_rap_2022_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_akumulasi_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_oktober_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_november_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_desember_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_januari_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_februari_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_maret_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_april_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_mei_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_juni_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_juli_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_vol_produk_c,2,',','.');?></th>		
			</tr>
			<tr class="table-baris1">
				<th align="center">4</th>
				<th align="left">Beton K 250 (18±2)</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($volume_rap_2022_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_akumulasi_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_oktober_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_november_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_desember_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_januari_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_februari_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_maret_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_april_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_mei_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_juni_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_juli_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_vol_produk_d,2,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="right" colspan="2">TOTAL VOLUME</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($total_rap_volume_2022,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_akumulasi_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_oktober_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_november_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_desember_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_januari_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_februari_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_maret_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_april_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_mei_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_juni_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_juli_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_all_volume,2,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="right" colspan="2">PENDAPATAN USAHA</th>
				<th align="center"></th>
				<th align="right"><?php echo number_format($total_rap_nilai_2022,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_akumulasi_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_oktober_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_november_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_desember_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_januari_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_februari_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_maret_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_april_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_mei_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juni_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juli_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_all_nilai,0,',','.');?></th>
			</tr>
			<tr class="table-baris1-bold">
				<th align="left" colspan="17">BIAYA</th>
			</tr>
			<tr class="table-baris1">
				<th align="center">1</th>
				<th align="left">Bahan</th>
				<th align="center">LS</th>
				<th align="right"><?php echo number_format($total_rap_2022_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_bahan_akumulasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_oktober_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_november_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_desember_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_januari_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_februari_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_maret_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_april_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_mei_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juni_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juli_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_all_biaya_bahan,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center">2</th>
				<th align="left">Alat</th>
				<th align="center">LS</th>
				<th align="right"><?php echo number_format($total_rap_2022_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_alat_akumulasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_oktober_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_november_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_desember_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_januari_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_februari_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_maret_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_april_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_mei_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juni_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juli_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_all_biaya_alat,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center">3</th>
				<th align="left">Overhead</th>
				<th align="center">LS</th>
				<th align="right"><?php echo number_format($total_rap_2022_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_overhead_akumulasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_oktober_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_november_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_desember_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_januari_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_februari_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_maret_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_april_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_mei_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juni_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juli_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_all_biaya_overhead,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center">4</th>
				<th align="left">Biaya Bank</th>
				<th align="center">LS</th>
				<th align="right"><?php echo number_format($total_rap_2022_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_diskonto_akumulasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_oktober_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_november_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_desember_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_januari_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_februari_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_maret_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_april_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_mei_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juni_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juli_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_all_biaya_bank,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center">5</th>
				<th align="left">Persiapan</th>
				<th align="center">LS</th>
				<th align="right"><?php echo number_format($total_rap_2022_biaya_persiapan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_persiapan_akumulasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_oktober_biaya_persiapan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_november_biaya_persiapan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_desember_biaya_persiapan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_januari_biaya_persiapan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_februari_biaya_persiapan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_maret_biaya_persiapan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_april_biaya_persiapan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_mei_biaya_persiapan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juni_biaya_persiapan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juli_biaya_persiapan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_biaya_persiapan,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_all_biaya_persiapan,0,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="right" colspan="2">JUMLAH</th>
				<th align="center"></th>
				<th align="right"><?php echo number_format($total_biaya_rap_2022_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_akumulasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_oktober_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_november_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_desember_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_januari_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_februari_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_maret_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_april_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_mei_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_juni_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_juli_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_all_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_biaya_all_biaya,0,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="right" colspan="2">LABA</th>
				<th align="center"></th>
				<th align="right"><?php echo number_format($total_laba_rap_2022,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_sd_agustus,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_oktober,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_november,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_desember,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_januari,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_februari,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_maret,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_april,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_mei,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_juni,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_juli,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_all,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_laba_all,0,',','.');?></th>
			</tr>
	    </table>
		
	</body>
</html>