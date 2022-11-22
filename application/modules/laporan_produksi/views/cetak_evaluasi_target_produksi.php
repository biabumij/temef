<!DOCTYPE html>
<html>
	<head>
	  <title>EVALUASI TARGET PRODUKSI</title>
	  
	  <?php
		$search = array(
		'January',
		'February',
		'March',
		'April',
		'May',
		'June',
		'July',
		'August',
		'September',
		'October',
		'November',
		'December'
		);
		
		$replace = array(
		'Januari',
		'Februari',
		'Maret',
		'April',
		'Mei',
		'Juni',
		'Juli',
		'Agustus',
		'September',
		'Oktober',
		'November',
		'Desember'
		);
		
		$subject = "$filter_date";

		echo str_replace($search, $replace, $subject);

	  ?>
	  
	  <style type="text/css">
		table tr.table-judul{
			background-color: #e69500;
			font-weight: bold;
			font-size: 9px;
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
					<div style="display: block;font-weight: bold;font-size: 12px;">EVALUASI TARGET PRODUKSI</div>
					<div style="display: block;font-weight: bold;font-size: 11px;">DIVISI BETON  PROYEK BENDUNGAN TEMEF</div>
				    <div style="display: block;font-weight: bold;font-size: 11px;">PT. BIA BUMI JAYENDRA</div>
					<div style="display: block;font-weight: bold;font-size: 12px; text-transform: uppercase;">PERIODE : <?php echo str_replace($search, $replace, $subject);?></div>
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
		
		<table class="table table-bordered" width="98%"  cellpadding="2">
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
					font-size: 9px;
					color: black;
				}
			</style>
			<!-- RAP -->
			<?php
			$date_now = date('Y-m-d');
			$rencana_kerja = $this->db->select('SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
			->from('rak r')
			->where("(r.tanggal_rencana_kerja between '$date1' and '$date2')")
			->get()->row_array();

			$rencana_kerja_biaya = $this->db->select('SUM(r.biaya_overhead) as biaya_overhead, SUM(r.biaya_bank) as biaya_bank,  SUM(r.biaya_persiapan) as biaya_persiapan')
			->from('rak_biaya r')
			->where("(r.tanggal_rencana_kerja between '$date1' and '$date2')")
			->get()->row_array();

			$volume_rap_produk_a = $rencana_kerja['vol_produk_a'];
			$volume_rap_produk_b = $rencana_kerja['vol_produk_b'];
			$volume_rap_produk_c = $rencana_kerja['vol_produk_c'];
			$volume_rap_produk_d = $rencana_kerja['vol_produk_d'];

			$total_rap_volume = $volume_rap_produk_a + $volume_rap_produk_b + $volume_rap_produk_c + $volume_rap_produk_d;
			
			$harga_jual_125_rap = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("pod.product_id = 2")
			->order_by('po.contract_date','desc')->limit(1)
			->get()->row_array();

			$harga_jual_225_rap = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("pod.product_id = 1")
			->order_by('po.contract_date','desc')->limit(1)
			->get()->row_array();

			$harga_jual_250_rap = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("pod.product_id = 3")
			->order_by('po.contract_date','desc')->limit(1)
			->get()->row_array();

			$harga_jual_250_18_rap = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("pod.product_id = 11")
			->order_by('po.contract_date','desc')->limit(1)
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

			$nilai_jual_125 = $volume_rap_produk_a * $harga_jual_125_rap['harga_satuan'];
			$nilai_jual_225 = $volume_rap_produk_b * $harga_jual_225_rap['harga_satuan'];
			$nilai_jual_250 = $volume_rap_produk_c * $harga_jual_250_rap['harga_satuan'];
			$nilai_jual_250_18 = $volume_rap_produk_d * $harga_jual_250_18_rap['harga_satuan'];
			$nilai_jual_all = $nilai_jual_125 + $nilai_jual_225 + $nilai_jual_250 + $nilai_jual_250_18;
			
			$total_rap_nilai = $nilai_jual_all;

			//KOMPOSISI RAP
			//K125
			$komposisi_125_rap = $this->db->select('pk.*')
			->from('pmm_agregat pk')
			->where("pk.mutu_beton = 2")
			->where('pk.status','PUBLISH')
			->order_by('pk.date_agregat','desc')->limit(1)
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
			->order_by('pk.date_agregat','desc')->limit(1)
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
			->order_by('pk.date_agregat','desc')->limit(1)
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
			->order_by('pk.date_agregat','desc')->limit(1)
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
			->order_by('pk.date_agregat','desc')->limit(1)
			->get()->row_array();

			$komposisi_250_18_produk_a = $komposisi_250_18['presentase_a'];
			$komposisi_250_18_produk_b = $komposisi_250_18['presentase_b'];
			$komposisi_250_18_produk_c = $komposisi_250_18['presentase_c'];
			$komposisi_250_18_produk_d = $komposisi_250_18['presentase_d'];

			//TOTAL PEMAKAIAN BAHAN RAP
			//TOTAL K-125
			$total_semen_125_rap = $komposisi_125_produk_a_rap * $volume_rap_produk_a;
			$total_pasir_125_rap = $komposisi_125_produk_b_rap * $volume_rap_produk_a;
			$total_batu1020_125_rap = $komposisi_125_produk_c_rap * $volume_rap_produk_a;
			$total_batu2030_125_rap = $komposisi_125_produk_d_rap * $volume_rap_produk_a;

			$nilai_semen_125_rap = $total_semen_125_rap * $komposisi_125_rap['price_a'];
			$nilai_pasir_125_rap = $total_pasir_125_rap * $komposisi_125_rap['price_b'];
			$nilai_batu1020_125_rap = $total_batu1020_125_rap * $komposisi_125_rap['price_c'];
			$nilai_batu2030_125_rap = $total_batu2030_125_rap * $komposisi_125_rap['price_d'];

			$total_125_rap = $nilai_semen_125_rap + $nilai_pasir_125_rap + $nilai_batu1020_125_rap + $nilai_batu2030_125_rap;

			//TOTAL K-225
			$total_semen_225_rap = $komposisi_225_produk_a_rap * $volume_rap_produk_b;
			$total_pasir_225_rap = $komposisi_225_produk_b_rap * $volume_rap_produk_b;
			$total_batu1020_225_rap = $komposisi_225_produk_c_rap * $volume_rap_produk_b;
			$total_batu2030_225_rap = $komposisi_225_produk_d_rap * $volume_rap_produk_b;

			$nilai_semen_225_rap = $total_semen_225_rap * $komposisi_225_rap['price_a'];
			$nilai_pasir_225_rap = $total_pasir_225_rap * $komposisi_225_rap['price_b'];
			$nilai_batu1020_225_rap = $total_batu1020_225_rap * $komposisi_225_rap['price_c'];
			$nilai_batu2030_225_rap = $total_batu2030_225_rap * $komposisi_225_rap['price_d'];

			$total_225_rap = $nilai_semen_225_rap + $nilai_pasir_225_rap + $nilai_batu1020_225_rap + $nilai_batu2030_225_rap;

			//TOTAL K-250
			$total_semen_250_rap = $komposisi_250_produk_a_rap * $volume_rap_produk_c;
			$total_pasir_250_rap = $komposisi_250_produk_b_rap * $volume_rap_produk_c;
			$total_batu1020_250_rap = $komposisi_250_produk_c_rap * $volume_rap_produk_c;
			$total_batu2030_250_rap = $komposisi_250_produk_d_rap * $volume_rap_produk_c;

			$nilai_semen_250_rap = $total_semen_250_rap * $komposisi_250_rap['price_a'];
			$nilai_pasir_250_rap = $total_pasir_250_rap * $komposisi_250_rap['price_b'];
			$nilai_batu1020_250_rap = $total_batu1020_250_rap * $komposisi_250_rap['price_c'];
			$nilai_batu2030_250_rap = $total_batu2030_250_rap * $komposisi_250_rap['price_d'];

			$total_250_rap = $nilai_semen_250_rap + $nilai_pasir_250_rap + $nilai_batu1020_250_rap + $nilai_batu2030_250_rap;

			//TOTAL K-250_18
			$total_semen_250_18_rap = $komposisi_250_18_produk_a_rap * $volume_rap_produk_d;
			$total_pasir_250_18_rap = $komposisi_250_18_produk_b_rap * $volume_rap_produk_d;
			$total_batu1020_250_18_rap = $komposisi_250_18_produk_c_rap * $volume_rap_produk_d;
			$total_batu2030_250_18_rap = $komposisi_250_18_produk_d_rap * $volume_rap_produk_d;

			$nilai_semen_250_18_rap = $total_semen_250_18_rap * $komposisi_250_18_rap['price_a'];
			$nilai_pasir_250_18_rap = $total_pasir_250_18_rap * $komposisi_250_18_rap['price_b'];
			$nilai_batu1020_250_18_rap = $total_batu1020_250_18_rap * $komposisi_250_18_rap['price_c'];
			$nilai_batu2030_250_18_rap = $total_batu2030_250_18_rap * $komposisi_250_18_rap['price_d'];

			$total_250_18_rap = $nilai_semen_250_18_rap + $nilai_pasir_250_18_rap + $nilai_batu1020_250_18_rap + $nilai_batu2030_250_18_rap;

			//TOTAL ALL
			$total_bahan_all_rap = $total_125_rap + $total_225_rap + $total_250_rap + $total_250_18_rap;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_rap = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->get()->row_array();

			$batching_plant_rap = $total_rap_volume * $rap_alat_rap['batching_plant'];
			$truck_mixer_rap = $total_rap_volume * $rap_alat_rap['truck_mixer'];
			$wheel_loader_rap = $total_rap_volume * $rap_alat_rap['wheel_loader'];
			$bbm_solar_rap = $total_rap_volume * $rap_alat_rap['bbm_solar'];
			$biaya_alat_all_rap = $batching_plant_rap + $truck_mixer_rap + $wheel_loader_rap + $bbm_solar_rap;
		
			$total_rap_biaya_bahan = $total_bahan_all_rap;
			$total_rap_biaya_alat = $biaya_alat_all_rap;
			$total_rap_biaya_overhead = $rencana_kerja_biaya['biaya_overhead'];
			$total_rap_biaya_bank = $rencana_kerja_biaya['biaya_bank'];
			$total_rap_biaya_persiapan = $rencana_kerja_biaya['biaya_persiapan'];

			$total_biaya_rap_biaya = $total_rap_biaya_bahan + $total_rap_biaya_alat + $total_rap_biaya_overhead + $total_rap_biaya_bank + $total_rap_biaya_persiapan;

			?>
			<!-- RAP 2022 -->
			
			<!-- REALISASI -->
			<?php
			$penjualan_realisasi_produk_a = $this->db->select('p.nama_produk, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('produk p', 'pp.product_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("(pp.date_production between '$date1' and '$date2')")
			->where("pp.status = 'PUBLISH'")
			->where("pp.product_id = 2")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.product_id")
			->order_by('p.nama_produk','asc')
			->get()->row_array();
			$volume_realisasi_produk_a = $penjualan_realisasi_produk_a['volume'];
			$nilai_realisasi_produk_a = $penjualan_realisasi_produk_a['price'];

			$penjualan_realisasi_produk_b = $this->db->select('p.nama_produk, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('produk p', 'pp.product_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("(pp.date_production between '$date1' and '$date2')")
			->where("pp.status = 'PUBLISH'")
			->where("pp.product_id = 1")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.product_id")
			->order_by('p.nama_produk','asc')
			->get()->row_array();
			$volume_realisasi_produk_b = $penjualan_realisasi_produk_b['volume'];
			$nilai_realisasi_produk_b = $penjualan_realisasi_produk_b['price'];

			$penjualan_realisasi_produk_c = $this->db->select('p.nama_produk, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('produk p', 'pp.product_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("(pp.date_production between '$date1' and '$date2')")
			->where("pp.status = 'PUBLISH'")
			->where("pp.product_id = 3")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.product_id")
			->order_by('p.nama_produk','asc')
			->get()->row_array();
			$volume_realisasi_produk_c = $penjualan_realisasi_produk_c['volume'];
			$nilai_realisasi_produk_c = $penjualan_realisasi_produk_c['price'];

			$penjualan_realisasi_produk_d = $this->db->select('p.nama_produk, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('produk p', 'pp.product_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("(pp.date_production between '$date1' and '$date2')")
			->where("pp.status = 'PUBLISH'")
			->where("pp.product_id = 11")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.product_id")
			->order_by('p.nama_produk','asc')
			->get()->row_array();
			$volume_realisasi_produk_d = $penjualan_realisasi_produk_d['volume'];
			$nilai_realisasi_produk_d = $penjualan_realisasi_produk_d['price'];

			$total_realisasi_volume = $volume_realisasi_produk_a + $volume_realisasi_produk_b + $volume_realisasi_produk_c + $volume_realisasi_produk_d;
			$total_realisasi_nilai = $nilai_realisasi_produk_a + $nilai_realisasi_produk_b + $nilai_realisasi_produk_c + $nilai_realisasi_produk_d;
			?>
			<!-- REALISASI SD. SAAT INI -->

		

			<!-- REALISASI BIAYA -->
			<?php
			//BAHAN
			$akumulasi = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar as total_nilai_keluar')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi between '$date1' and '$date2')")
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
			->where("(prm.date_receipt between '$date1' and '$date2')")
			->where("p.kategori_produk = '5'")
			->where("po.status in ('PUBLISH','CLOSED')")
			->get()->row_array();

			$akumulasi_bbm = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar_2 as total_nilai_keluar_2')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi between '$date1' and '$date2')")
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
			->where("(tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$total_insentif_tm = $insentif_tm['total'];

			$total_alat_realisasi = $nilai_alat['nilai'] + $total_akumulasi_bbm + $total_insentif_tm;
			//END ALAT
			?>

			<?php
			//OVERHEAD
			$overhead_15_realisasi = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',15)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$overhead_jurnal_15_realisasi = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',15)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$overhead_16_realisasi = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',16)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$overhead_jurnal_16_realisasi = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',16)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$overhead_17_realisasi = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',17)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$overhead_jurnal_17_realisasi = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',17)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$total_overhead_realisasi =  $overhead_15_realisasi['total'] + $overhead_jurnal_15_realisasi['total'] + $overhead_16_realisasi['total'] + $overhead_jurnal_16_realisasi['total'] + $overhead_17_realisasi['total'] + $overhead_jurnal_17_realisasi['total'];
			?>

			<?php
			//DISKONTO
			$diskonto_realisasi = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 168")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$total_diskonto_realisasi = $diskonto_realisasi['total'];
			//DISKONTO
			?>

			<?php
			//PERSIAPAN
			$persiapan_biaya_realisasi = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 228")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$persiapan_jurnal_realisasi = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 228")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$total_persiapan_realisasi = $persiapan_biaya_realisasi['total'] + $persiapan_jurnal_realisasi['total'];
			$total_biaya_realisasi = $total_bahan_akumulasi + $total_alat_realisasi + $total_overhead_realisasi + $total_diskonto_realisasi + $total_persiapan_realisasi;
			//END PERSIAPAN
			?>
			<!-- REALISASI BIAYA -->

			<!-- SISA -->
			<?php
			$sisa_volume_produk_a = $volume_rap_produk_a - $volume_realisasi_produk_a;
			$sisa_volume_produk_b = $volume_rap_produk_b - $volume_realisasi_produk_b;
			$sisa_volume_produk_c = $volume_rap_produk_c - $volume_realisasi_produk_c;
			$sisa_volume_produk_d = $volume_rap_produk_d - $volume_realisasi_produk_d;

			$total_sisa_volume_all_produk = $sisa_volume_produk_a + $sisa_volume_produk_b + $sisa_volume_produk_c + $sisa_volume_produk_d;
			$total_sisa_nilai_all_produk = $total_rap_nilai - $total_realisasi_nilai;

			$sisa_biaya_bahan = $total_rap_biaya_bahan - $total_bahan_akumulasi;
			$sisa_biaya_alat = $total_rap_biaya_alat - $total_alat_realisasi;
			$sisa_biaya_overhead = $total_rap_biaya_overhead - $total_overhead_realisasi;
			$sisa_biaya_bank = $total_rap_biaya_bank - $total_diskonto_realisasi;
			$sisa_biaya_persiapan = $total_rap_biaya_persiapan - $total_persiapan_realisasi;
			?>
			<!-- SISA -->

			<!-- TOTAL -->
			<?php
			$total_laba_rap = $total_rap_nilai - $total_biaya_rap_biaya;
			$total_laba_realisasi = $total_realisasi_nilai - $total_biaya_realisasi;

			$sisa_biaya_realisasi = $total_biaya_rap_biaya - $total_biaya_realisasi;
			$sisa_laba = $total_laba_rap - $total_laba_realisasi;

			?>
			<!-- TOTAL -->
			
			<tr class="table-judul">
				<th width="5%" align="center">NO.</th>
				<th width="25%" align="center">URAIAN</th>
				<th width="10%" align="center">SATUAN</th>
				<th width="20%" align="center">RENCANA</th>
				<th width="20%" align="center">REALISASI</th>
				<th width="20%" align="center">EVALUASI</th>
	        </tr>
			<tr class="table-total">
				<th align="left" colspan="6">RENCANA PRODUKSI & PENDAPATAN USAHA</th>
			</tr>
			<tr class="table-baris1">
				<th align="center">1</th>
				<th align="left">Beton K 125 (10±2)</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($volume_rap_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_realisasi_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_volume_produk_a,2,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center">2</th>
				<th align="left">Beton K 225 (10±2)</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($volume_rap_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_realisasi_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_volume_produk_b,2,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center">3</th>
				<th align="left">Beton K 250 (10±2)</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($volume_rap_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_realisasi_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_volume_produk_c,2,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center">4</th>
				<th align="left">Beton K 250 (18±2)</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($volume_rap_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_realisasi_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_volume_produk_d,2,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="right" colspan="2">TOTAL VOLUME</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($total_rap_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_realisasi_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_sisa_volume_all_produk,2,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="right" colspan="2">PENDAPATAN USAHA</th>
				<th align="center"></th>
				<th align="right"><?php echo number_format($total_rap_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_realisasi_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_sisa_nilai_all_produk,0,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="left" colspan="6">BIAYA</th>
			</tr>
			<tr class="table-baris1">
				<th align="center">1</th>
				<th align="left">Bahan</th>
				<th align="center">LS</th>
				<th align="right"><?php echo number_format($total_rap_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_bahan_akumulasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_biaya_bahan,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center">2</th>
				<th align="left">Alat</th>
				<th align="center">LS</th>
				<th align="right"><?php echo number_format($total_rap_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_alat_realisasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_biaya_alat,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center">3</th>
				<th align="left">Overhead</th>
				<th align="center">LS</th>
				<th align="right"><?php echo number_format($total_rap_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_overhead_realisasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_biaya_overhead,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center">4</th>
				<th align="left">Biaya Bank</th>
				<th align="center">LS</th>
				<th align="right"><?php echo number_format($total_rap_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_diskonto_realisasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_biaya_bank,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center">5</th>
				<th align="left">Persiapan</th>
				<th align="center">LS</th>
				<th align="right"><?php echo number_format($total_rap_biaya_persiapan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_persiapan_realisasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_biaya_persiapan,0,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="right" colspan="2">JUMLAH</th>
				<th align="center"></th>
				<th align="right"><?php echo number_format($total_biaya_rap_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_realisasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_biaya_realisasi,0,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="right" colspan="2">LABA</th>
				<th align="center"></th>
				<th align="right"><?php echo number_format($total_laba_rap,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_realisasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_laba,0,',','.');?></th>
			</tr>
			
	    </table>
	</body>
</html>