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
					<div style="display: block;font-weight: bold;font-size: 12px;">PROGNOSA PRODUKSI</div>
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
		
			<!-- RAP TERBARU -->
			<?php
			//VOLUME RAP
			$date_now = date('Y-m-d');
			$date_end = date('2022-12-31');

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
			$total_rap_nilai_2022 = $nilai_jual_all_2022;

			//BIAYA RAP 2022
			$rencana_kerja_biaya_2022_1 = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '2021-12-30' and '2021-12-30'")
			->get()->row_array();

			$rencana_kerja_biaya_2022_2 = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '2021-12-31' and '2021-12-31'")
			->get()->row_array();


			$total_rap_2022_biaya_bahan = $rencana_kerja_biaya_2022_1['biaya_bahan'] + $rencana_kerja_biaya_2022_2['biaya_bahan'];
			$total_rap_2022_biaya_alat = $rencana_kerja_biaya_2022_1['biaya_alat'] + $rencana_kerja_biaya_2022_2['biaya_alat'];
			$total_rap_2022_biaya_overhead = $rencana_kerja_biaya_2022_1['biaya_overhead'] + $rencana_kerja_biaya_2022_2['biaya_overhead'];
			$total_rap_2022_biaya_bank = $rencana_kerja_biaya_2022_1['biaya_bank'] + $rencana_kerja_biaya_2022_2['biaya_bank'];
			$total_biaya_rap_2022_biaya = $total_rap_2022_biaya_bahan + $total_rap_2022_biaya_alat + $total_rap_2022_biaya_overhead + $total_rap_2022_biaya_bank;

			?>
			<!-- RAP TERBARU -->
			
			<!-- AKUMULASI BULAN TERAKHIR -->
			<?php
			$stock_opname = $this->db->select('date')->order_by('date','desc')->limit(1)->get_where('pmm_remaining_materials_cat',array('status'=>'PUBLISH'))->row_array();
			$last_opname =  date('Y-m-d', strtotime($stock_opname['date']));

			$penjualan_akumulasi_produk_a = $this->db->select('p.nama_produk, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('produk p', 'pp.product_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("(pp.date_production <= '$last_opname')")
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
			->where("(pp.date_production <= '$last_opname')")
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
			->where("(pp.date_production <= '$last_opname')")
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
			->where("(pp.date_production <= '$last_opname')")
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
		
			//AKUMULASI BIAYA
			//BAHAN
			$akumulasi = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar as total_nilai_keluar')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi <= '$last_opname')")
			->get()->result_array();

			$total_akumulasi = 0;

			foreach ($akumulasi as $a){
				$total_akumulasi += $a['total_nilai_keluar'];
			}

			$total_bahan_akumulasi = $total_akumulasi;
		
			//ALAT
			$nilai_alat = $this->db->select('SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("(prm.date_receipt <= '$last_opname')")
			->where("p.kategori_produk = '5'")
			->where("po.status in ('PUBLISH','CLOSED')")
			->get()->row_array();

			$akumulasi_bbm = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar_2 as total_nilai_keluar_2')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi <= '$last_opname')")
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
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$last_opname')")
			->get()->row_array();
			$total_insentif_tm = $insentif_tm['total'];

			$biaya_alat_lainnya = 0;
			$biaya_alat_lainnya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("c.id in ('219','505')")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$last_opname')")
			->get()->row_array();
			$biaya_alat_lainnya = $biaya_alat_lainnya['total'];

			$total_alat_akumulasi = $nilai_alat['nilai'] + $total_akumulasi_bbm + $total_insentif_tm + $biaya_alat_lainnya;

			//OVERHEAD
			$overhead_15_akumulasi = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',15)
			->where("c.id <> 168 ") //Biaya Diskonto Bank
			->where("c.id <> 219 ") //Biaya Alat Batching Plant 
			->where("c.id <> 220 ") //Biaya Alat Truck Mixer
			->where("c.id <> 228 ") //Biaya Persiapan
			->where("c.id <> 505 ") //Biaya Oli
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$last_opname')")
			->get()->row_array();

			$overhead_jurnal_15_akumulasi = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',15)
			->where("c.id <> 168 ") //Biaya Diskonto Bank
			->where("c.id <> 219 ") //Biaya Alat Batching Plant 
			->where("c.id <> 220 ") //Biaya Alat Truck Mixer
			->where("c.id <> 228 ") //Biaya Persiapan
			->where("c.id <> 505 ") //Biaya Oli
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$last_opname')")
			->get()->row_array();

			$total_overhead_akumulasi =  $overhead_15_akumulasi['total'] + $overhead_jurnal_15_akumulasi['total'];
			
			//DISKONTO
			$diskonto_akumulasi = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 168")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$last_opname')")
			->get()->row_array();

			$total_diskonto_akumulasi = $diskonto_akumulasi['total'];
			$total_biaya_akumulasi = $total_bahan_akumulasi + $total_alat_akumulasi + $total_overhead_akumulasi + $total_diskonto_akumulasi;
			?>
			<!-- AKUMULASI BULAN TERAKHIR -->

			

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

			$rencana_kerja_biaya_desember = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_desember_awal' and '$date_desember_akhir'")
			->get()->row_array();

			$nilai_jual_125_desember = $volume_desember_produk_a * $rencana_kerja_desember['price_a'];
			$nilai_jual_225_desember = $volume_desember_produk_b * $rencana_kerja_desember['price_b'];
			$nilai_jual_250_desember = $volume_desember_produk_c * $rencana_kerja_desember['price_c'];
			$nilai_jual_250_18_desember = $volume_desember_produk_d * $rencana_kerja_desember['price_d'];
			$nilai_jual_all_desember = $nilai_jual_125_desember + $nilai_jual_225_desember + $nilai_jual_250_desember + $nilai_jual_250_18_desember;

			$total_desember_nilai = $nilai_jual_all_desember;

			//VOLUME
			$rencana_kerja_desember = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_desember_awal' and '$date_desember_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_desember_produk_a = $rencana_kerja_desember['vol_produk_a'];
			$volume_rencana_kerja_desember_produk_b = $rencana_kerja_desember['vol_produk_b'];
			$volume_rencana_kerja_desember_produk_c = $rencana_kerja_desember['vol_produk_c'];
			$volume_rencana_kerja_desember_produk_d = $rencana_kerja_desember['vol_produk_d'];

			//BIAYA
			$rencana_kerja_biaya_desember = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_desember_awal' and '$date_desember_akhir'")
			->get()->row_array();
		
			$total_desember_biaya_bahan = $rencana_kerja_biaya_desember['biaya_bahan'];
			$total_desember_biaya_alat = $rencana_kerja_biaya_desember['biaya_alat'];
			$total_desember_biaya_overhead = $rencana_kerja_biaya_desember['biaya_overhead'];
			$total_desember_biaya_bank = $rencana_kerja_biaya_desember['biaya_bank'];
			$total_biaya_desember_biaya = $total_desember_biaya_bahan + $total_desember_biaya_alat + $total_desember_biaya_overhead + $total_desember_biaya_bank;
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

			$rencana_kerja_biaya_januari = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_januari_akhir'")
			->get()->row_array();

			$nilai_jual_125_januari = $volume_januari_produk_a * $rencana_kerja_januari['price_a'];
			$nilai_jual_225_januari = $volume_januari_produk_b * $rencana_kerja_januari['price_b'];
			$nilai_jual_250_januari = $volume_januari_produk_c * $rencana_kerja_januari['price_c'];
			$nilai_jual_250_18_januari = $volume_januari_produk_d * $rencana_kerja_januari['price_d'];
			$nilai_jual_all_januari = $nilai_jual_125_januari + $nilai_jual_225_januari + $nilai_jual_250_januari + $nilai_jual_250_18_januari;

			$total_januari_nilai = $nilai_jual_all_januari;

			//VOLUME
			$rencana_kerja_januari = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_januari_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_januari_produk_a = $rencana_kerja_januari['vol_produk_a'];
			$volume_rencana_kerja_januari_produk_b = $rencana_kerja_januari['vol_produk_b'];
			$volume_rencana_kerja_januari_produk_c = $rencana_kerja_januari['vol_produk_c'];
			$volume_rencana_kerja_januari_produk_d = $rencana_kerja_januari['vol_produk_d'];

			//BIAYA
			$rencana_kerja_biaya_januari = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_januari_akhir'")
			->get()->row_array();
		
			$total_januari_biaya_bahan = $rencana_kerja_biaya_januari['biaya_bahan'];
			$total_januari_biaya_alat = $rencana_kerja_biaya_januari['biaya_alat'];
			$total_januari_biaya_overhead = $rencana_kerja_biaya_januari['biaya_overhead'];
			$total_januari_biaya_bank = $rencana_kerja_biaya_januari['biaya_bank'];
			$total_biaya_januari_biaya = $total_januari_biaya_bahan + $total_januari_biaya_alat + $total_januari_biaya_overhead + $total_januari_biaya_bank;
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

			$rencana_kerja_biaya_februari = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_februari_awal' and '$date_februari_akhir'")
			->get()->row_array();

			$nilai_jual_125_februari = $volume_februari_produk_a * $rencana_kerja_februari['price_a'];
			$nilai_jual_225_februari = $volume_februari_produk_b * $rencana_kerja_februari['price_b'];
			$nilai_jual_250_februari = $volume_februari_produk_c * $rencana_kerja_februari['price_c'];
			$nilai_jual_250_18_februari = $volume_februari_produk_d * $rencana_kerja_februari['price_d'];
			$nilai_jual_all_februari = $nilai_jual_125_februari + $nilai_jual_225_februari + $nilai_jual_250_februari + $nilai_jual_250_18_februari;

			$total_februari_nilai = $nilai_jual_all_februari;

			//VOLUME
			$rencana_kerja_februari = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_februari_awal' and '$date_februari_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_februari_produk_a = $rencana_kerja_februari['vol_produk_a'];
			$volume_rencana_kerja_februari_produk_b = $rencana_kerja_februari['vol_produk_b'];
			$volume_rencana_kerja_februari_produk_c = $rencana_kerja_februari['vol_produk_c'];
			$volume_rencana_kerja_februari_produk_d = $rencana_kerja_februari['vol_produk_d'];

			//BIAYA
			$rencana_kerja_biaya_februari = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_februari_awal' and '$date_februari_akhir'")
			->get()->row_array();
		
			$total_februari_biaya_bahan = $rencana_kerja_biaya_februari['biaya_bahan'];
			$total_februari_biaya_alat = $rencana_kerja_biaya_februari['biaya_alat'];
			$total_februari_biaya_overhead = $rencana_kerja_biaya_februari['biaya_overhead'];
			$total_februari_biaya_bank = $rencana_kerja_biaya_februari['biaya_bank'];
			$total_biaya_februari_biaya = $total_februari_biaya_bahan + $total_februari_biaya_alat + $total_februari_biaya_overhead + $total_februari_biaya_bank;
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

			$rencana_kerja_biaya_maret = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_maret_awal' and '$date_maret_akhir'")
			->get()->row_array();

			$nilai_jual_125_maret = $volume_maret_produk_a * $rencana_kerja_maret['price_a'];
			$nilai_jual_225_maret = $volume_maret_produk_b * $rencana_kerja_maret['price_b'];
			$nilai_jual_250_maret = $volume_maret_produk_c * $rencana_kerja_maret['price_c'];
			$nilai_jual_250_18_maret = $volume_maret_produk_d * $rencana_kerja_maret['price_d'];
			$nilai_jual_all_maret = $nilai_jual_125_maret + $nilai_jual_225_maret + $nilai_jual_250_maret + $nilai_jual_250_18_maret;

			$total_maret_nilai = $nilai_jual_all_maret;

			//VOLUME
			$rencana_kerja_maret = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_maret_awal' and '$date_maret_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_maret_produk_a = $rencana_kerja_maret['vol_produk_a'];
			$volume_rencana_kerja_maret_produk_b = $rencana_kerja_maret['vol_produk_b'];
			$volume_rencana_kerja_maret_produk_c = $rencana_kerja_maret['vol_produk_c'];
			$volume_rencana_kerja_maret_produk_d = $rencana_kerja_maret['vol_produk_d'];

			//BIAYA
			$rencana_kerja_biaya_maret = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_maret_awal' and '$date_maret_akhir'")
			->get()->row_array();
		
			$total_maret_biaya_bahan = $rencana_kerja_biaya_maret['biaya_bahan'];
			$total_maret_biaya_alat = $rencana_kerja_biaya_maret['biaya_alat'];
			$total_maret_biaya_overhead = $rencana_kerja_biaya_maret['biaya_overhead'];
			$total_maret_biaya_bank = $rencana_kerja_biaya_maret['biaya_bank'];
			$total_biaya_maret_biaya = $total_maret_biaya_bahan + $total_maret_biaya_alat + $total_maret_biaya_overhead + $total_maret_biaya_bank;
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

			$rencana_kerja_biaya_april = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_april_awal' and '$date_april_akhir'")
			->get()->row_array();

			$nilai_jual_125_april = $volume_april_produk_a * $rencana_kerja_april['price_a'];
			$nilai_jual_225_april = $volume_april_produk_b * $rencana_kerja_april['price_b'];
			$nilai_jual_250_april = $volume_april_produk_c * $rencana_kerja_april['price_c'];
			$nilai_jual_250_18_april = $volume_april_produk_d * $rencana_kerja_april['price_d'];
			$nilai_jual_all_april = $nilai_jual_125_april + $nilai_jual_225_april + $nilai_jual_250_april + $nilai_jual_250_18_april;

			$total_april_nilai = $nilai_jual_all_april;

			//VOLUME
			$rencana_kerja_april = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_april_awal' and '$date_april_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_april_produk_a = $rencana_kerja_april['vol_produk_a'];
			$volume_rencana_kerja_april_produk_b = $rencana_kerja_april['vol_produk_b'];
			$volume_rencana_kerja_april_produk_c = $rencana_kerja_april['vol_produk_c'];
			$volume_rencana_kerja_april_produk_d = $rencana_kerja_april['vol_produk_d'];

			//BIAYA
			$rencana_kerja_biaya_april = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_april_awal' and '$date_april_akhir'")
			->get()->row_array();
		
			$total_april_biaya_bahan = $rencana_kerja_biaya_april['biaya_bahan'];
			$total_april_biaya_alat = $rencana_kerja_biaya_april['biaya_alat'];
			$total_april_biaya_overhead = $rencana_kerja_biaya_april['biaya_overhead'];
			$total_april_biaya_bank = $rencana_kerja_biaya_april['biaya_bank'];
			$total_biaya_april_biaya = $total_april_biaya_bahan + $total_april_biaya_alat + $total_april_biaya_overhead + $total_april_biaya_bank;
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

			$rencana_kerja_biaya_mei = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_mei_awal' and '$date_mei_akhir'")
			->get()->row_array();

			$nilai_jual_125_mei = $volume_mei_produk_a * $rencana_kerja_mei['price_a'];
			$nilai_jual_225_mei = $volume_mei_produk_b * $rencana_kerja_mei['price_b'];
			$nilai_jual_250_mei = $volume_mei_produk_c * $rencana_kerja_mei['price_c'];
			$nilai_jual_250_18_mei = $volume_mei_produk_d * $rencana_kerja_mei['price_d'];
			$nilai_jual_all_mei = $nilai_jual_125_mei + $nilai_jual_225_mei + $nilai_jual_250_mei + $nilai_jual_250_18_mei;

			$total_mei_nilai = $nilai_jual_all_mei;

			//VOLUME
			$rencana_kerja_mei = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_mei_awal' and '$date_mei_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_mei_produk_a = $rencana_kerja_mei['vol_produk_a'];
			$volume_rencana_kerja_mei_produk_b = $rencana_kerja_mei['vol_produk_b'];
			$volume_rencana_kerja_mei_produk_c = $rencana_kerja_mei['vol_produk_c'];
			$volume_rencana_kerja_mei_produk_d = $rencana_kerja_mei['vol_produk_d'];

			//BIAYA
			$rencana_kerja_biaya_mei = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_mei_awal' and '$date_mei_akhir'")
			->get()->row_array();
		
			$total_mei_biaya_bahan = $rencana_kerja_biaya_mei['biaya_bahan'];
			$total_mei_biaya_alat = $rencana_kerja_biaya_mei['biaya_alat'];
			$total_mei_biaya_overhead = $rencana_kerja_biaya_mei['biaya_overhead'];
			$total_mei_biaya_bank = $rencana_kerja_biaya_mei['biaya_bank'];
			$total_biaya_mei_biaya = $total_mei_biaya_bahan + $total_mei_biaya_alat + $total_mei_biaya_overhead + $total_mei_biaya_bank;
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

			$rencana_kerja_biaya_juni = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_juni_awal' and '$date_juni_akhir'")
			->get()->row_array();

			$nilai_jual_125_juni = $volume_juni_produk_a * $rencana_kerja_juni['price_a'];
			$nilai_jual_225_juni = $volume_juni_produk_b * $rencana_kerja_juni['price_b'];
			$nilai_jual_250_juni = $volume_juni_produk_c * $rencana_kerja_juni['price_c'];
			$nilai_jual_250_18_juni = $volume_juni_produk_d * $rencana_kerja_juni['price_d'];
			$nilai_jual_all_juni = $nilai_jual_125_juni + $nilai_jual_225_juni + $nilai_jual_250_juni + $nilai_jual_250_18_juni;

			$total_juni_nilai = $nilai_jual_all_juni;

			//VOLUME
			$rencana_kerja_juni = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_juni_awal' and '$date_juni_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_juni_produk_a = $rencana_kerja_juni['vol_produk_a'];
			$volume_rencana_kerja_juni_produk_b = $rencana_kerja_juni['vol_produk_b'];
			$volume_rencana_kerja_juni_produk_c = $rencana_kerja_juni['vol_produk_c'];
			$volume_rencana_kerja_juni_produk_d = $rencana_kerja_juni['vol_produk_d'];

			//BIAYA
			$rencana_kerja_biaya_juni = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_juni_awal' and '$date_juni_akhir'")
			->get()->row_array();
		
			$total_juni_biaya_bahan = $rencana_kerja_biaya_juni['biaya_bahan'];
			$total_juni_biaya_alat = $rencana_kerja_biaya_juni['biaya_alat'];
			$total_juni_biaya_overhead = $rencana_kerja_biaya_juni['biaya_overhead'];
			$total_juni_biaya_bank = $rencana_kerja_biaya_juni['biaya_bank'];
			$total_biaya_juni_biaya = $total_juni_biaya_bahan + $total_juni_biaya_alat + $total_juni_biaya_overhead + $total_juni_biaya_bank;
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

			$rencana_kerja_biaya_juli = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_juli_awal' and '$date_juli_akhir'")
			->get()->row_array();

			$nilai_jual_125_juli = $volume_juli_produk_a * $rencana_kerja_juli['price_a'];
			$nilai_jual_225_juli = $volume_juli_produk_b * $rencana_kerja_juli['price_b'];
			$nilai_jual_250_juli = $volume_juli_produk_c * $rencana_kerja_juli['price_c'];
			$nilai_jual_250_18_juli = $volume_juli_produk_d * $rencana_kerja_juli['price_d'];
			$nilai_jual_all_juli = $nilai_jual_125_juli + $nilai_jual_225_juli + $nilai_jual_250_juli + $nilai_jual_250_18_juli;

			$total_juli_nilai = $nilai_jual_all_juli;

			//VOLUME
			$rencana_kerja_juli = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_juli_awal' and '$date_juli_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_juli_produk_a = $rencana_kerja_juli['vol_produk_a'];
			$volume_rencana_kerja_juli_produk_b = $rencana_kerja_juli['vol_produk_b'];
			$volume_rencana_kerja_juli_produk_c = $rencana_kerja_juli['vol_produk_c'];
			$volume_rencana_kerja_juli_produk_d = $rencana_kerja_juli['vol_produk_d'];

			//BIAYA
			$rencana_kerja_biaya_juli = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_juli_awal' and '$date_juli_akhir'")
			->get()->row_array();
		
			$total_juli_biaya_bahan = $rencana_kerja_biaya_juli['biaya_bahan'];
			$total_juli_biaya_alat = $rencana_kerja_biaya_juli['biaya_alat'];
			$total_juli_biaya_overhead = $rencana_kerja_biaya_juli['biaya_overhead'];
			$total_juli_biaya_bank = $rencana_kerja_biaya_juli['biaya_bank'];
			$total_biaya_juli_biaya = $total_juli_biaya_bahan + $total_juli_biaya_alat + $total_juli_biaya_overhead + $total_juli_biaya_bank;
			?>
			<!-- JULI -->

			<!-- TOTAL -->
			<?php
			$total_all_produk_a = $volume_akumulasi_produk_a + $volume_desember_produk_a + $volume_januari_produk_a + $volume_februari_produk_a + $volume_maret_produk_a + $volume_april_produk_a + $volume_mei_produk_a + $volume_juni_produk_a + $volume_juli_produk_a;
			$total_all_produk_b = $volume_akumulasi_produk_b + $volume_desember_produk_b + $volume_januari_produk_b + $volume_februari_produk_b + $volume_maret_produk_b + $volume_april_produk_b + $volume_mei_produk_b + $volume_juni_produk_b + $volume_juli_produk_b;
			$total_all_produk_c = $volume_akumulasi_produk_c + $volume_desember_produk_c + $volume_januari_produk_c + $volume_februari_produk_c + $volume_maret_produk_c + $volume_april_produk_c + $volume_mei_produk_c + $volume_juni_produk_c + $volume_juli_produk_c;
			$total_all_produk_d = $volume_akumulasi_produk_d + $volume_desember_produk_d+ $volume_januari_produk_d + $volume_februari_produk_d + $volume_maret_produk_d + $volume_april_produk_d + $volume_mei_produk_d + $volume_juni_produk_d + $volume_juli_produk_d;

			$total_all_volume = $total_akumulasi_volume + $total_desember_volume + $total_januari_volume + $total_februari_volume + $total_maret_volume + $total_april_volume + $total_mei_volume + $total_juni_volume + $total_juli_volume;
			$total_all_nilai = $total_akumulasi_nilai + $total_desember_nilai + $total_januari_nilai +  + $total_februari_nilai +  + $total_maret_nilai + $total_april_nilai + $total_mei_nilai + $total_juni_nilai + $total_juli_nilai;

			$total_all_biaya_bahan = $total_bahan_akumulasi + $total_desember_biaya_bahan + $total_januari_biaya_bahan + $total_februari_biaya_bahan + $total_maret_biaya_bahan + $total_april_biaya_bahan + $total_mei_biaya_bahan + $total_juni_biaya_bahan + $total_juli_biaya_bahan;
			$total_all_biaya_alat = $total_alat_akumulasi + $total_desember_biaya_alat + $total_januari_biaya_alat + $total_februari_biaya_alat + $total_maret_biaya_alat + $total_april_biaya_alat + $total_mei_biaya_alat + $total_juni_biaya_alat + $total_juli_biaya_alat;
			$total_all_biaya_overhead = $total_overhead_akumulasi + $total_desember_biaya_overhead + $total_januari_biaya_overhead + $total_februari_biaya_overhead + $total_maret_biaya_overhead + $total_april_biaya_overhead + $total_mei_biaya_overhead + $total_juni_biaya_overhead + $total_juli_biaya_overhead;
			$total_all_biaya_bank = $total_diskonto_akumulasi + $total_desember_biaya_bank + $total_januari_biaya_bank + $total_februari_biaya_bank + $total_maret_biaya_bank + $total_april_biaya_bank + $total_mei_biaya_bank + $total_juni_biaya_bank + $total_juli_biaya_bank;
			
			$total_biaya_all_biaya = $total_all_biaya_bahan + $total_all_biaya_alat + $total_all_biaya_overhead + $total_all_biaya_bank;

			$total_laba_rap_2022 = $total_rap_nilai_2022 - $total_biaya_rap_2022_biaya;
			$total_laba_sd_agustus = $total_akumulasi_nilai - $total_biaya_akumulasi;
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
			
			<?php
			$tanggal = $last_opname;
			$date = date('Y-m-d',strtotime($tanggal));
			?>
			<?php
			function tgl_indo($date){
				$bulan = array (
					1 =>   'Jan',
					'Feb',
					'Mar',
					'Apr',
					'Mei',
					'Jun',
					'Jul',
					'Agu',
					'Sep',
					'Okt',
					'Nov',
					'Des'
				);
				$pecahkan = explode('-', $date);
				
				// variabel pecahkan 0 = tanggal
				// variabel pecahkan 1 = bulan
				// variabel pecahkan 2 = tahun
			
				return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
				
			}
			?>
			<tr class="table-judul">
				<th width="3%" align="center" rowspan="3">&nbsp; <br /><br />NO.</th>
				<th width="14%" align="center" rowspan="3">&nbsp; <br /><br />URAIAN</th>
				<th width="4%" align="center" rowspan="3">&nbsp; <br /><br />SATUAN</th>
				<th width="7%" align="center" rowspan="3">&nbsp; <br /><br />ADEDENDUM RAP</th>
				<th width="7%" align="center" rowspan="2">&nbsp; <br /><br />REALISASI SD.<br><div style="text-transform:uppercase;"><?= tgl_indo(date($date)); ?></div></th>
				<th width="58%" align="center" colspan="8">&nbsp; <br />PROGNOSA</th>
				<th width="7%" align="center" rowspan="3">&nbsp; <br /><br />TOTAL</th>
	        </tr>
			<tr class="table-judul">
				<th align="center">DESEMBER</th>
				<th align="center">JANUARI</th>
				<th align="center">FEBRUARI</th>
				<th align="center">MARET</th>
				<th align="center">APRIL</th>
				<th align="center">MEI</th>
				<th align="center">JUNI</th>
				<th align="center">JULI</th>
	        </tr>
			<tr class="table-judul">
				<th align="center"></th>
				<th align="center">2022</th>
				<th align="center">2023</th>
				<th align="center">2023</th>
				<th align="center">2023</th>
				<th align="center">2023</th>
				<th align="center">2023</th>
				<th align="center">2023</th>
				<th align="center">2023</th>
	        </tr>
			<tr class="table-baris1-bold">
				<th align="left" colspan="14">RENCANA PRODUKSI & PENDAPATAN USAHA</th>
			</tr>
			<tr class="table-baris1">
				<th align="center">1</th>
				<th align="left">Beton K 125 (10±2)</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($volume_rap_2022_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_akumulasi_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_desember_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_januari_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_februari_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_maret_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_april_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_mei_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_juni_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_juli_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_produk_a,2,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center">2</th>
				<th align="left">Beton K 225 (10±2)</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($volume_rap_2022_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_akumulasi_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_desember_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_januari_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_februari_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_maret_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_april_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_mei_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_juni_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_juli_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_produk_b,2,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center">3</th>
				<th align="left">Beton K 250 (10±2)</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($volume_rap_2022_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_akumulasi_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_desember_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_januari_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_februari_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_maret_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_april_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_mei_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_juni_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_juli_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_produk_c,2,',','.');?></th>	
			</tr>
			<tr class="table-baris1">
				<th align="center">4</th>
				<th align="left">Beton K 250 (18±2)</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($volume_rap_2022_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_akumulasi_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_desember_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_januari_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_februari_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_maret_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_april_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_mei_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_juni_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_juli_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_produk_d,2,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="right" colspan="2">TOTAL VOLUME</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($total_rap_volume_2022,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_akumulasi_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_desember_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_januari_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_februari_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_maret_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_april_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_mei_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_juni_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_juli_volume,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_volume,2,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="right" colspan="2">PENDAPATAN USAHA</th>
				<th align="center"></th>
				<th align="right"><?php echo number_format($total_rap_nilai_2022,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_akumulasi_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_desember_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_januari_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_februari_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_maret_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_april_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_mei_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juni_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juli_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_nilai,0,',','.');?></th>
			</tr>
			<tr class="table-baris1-bold">
				<th align="left" colspan="14">BIAYA</th>
			</tr>
			<tr class="table-baris1">
				<th align="center">1</th>
				<th align="left">Bahan</th>
				<th align="center">LS</th>
				<th align="right"><?php echo number_format($total_rap_2022_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_bahan_akumulasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_desember_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_januari_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_februari_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_maret_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_april_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_mei_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juni_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juli_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_biaya_bahan,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center">2</th>
				<th align="left">Alat</th>
				<th align="center">LS</th>
				<th align="right"><?php echo number_format($total_rap_2022_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_alat_akumulasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_desember_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_januari_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_februari_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_maret_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_april_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_mei_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juni_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juli_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_biaya_alat,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center">3</th>
				<th align="left">Overhead</th>
				<th align="center">LS</th>
				<th align="right"><?php echo number_format($total_rap_2022_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_overhead_akumulasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_desember_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_januari_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_februari_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_maret_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_april_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_mei_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juni_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juli_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_biaya_overhead,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center">4</th>
				<th align="left">Biaya Bank</th>
				<th align="center">LS</th>
				<th align="right"><?php echo number_format($total_rap_2022_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_diskonto_akumulasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_desember_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_januari_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_februari_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_maret_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_april_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_mei_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juni_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juli_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_all_biaya_bank,0,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="right" colspan="2">JUMLAH</th>
				<th align="center"></th>
				<th align="right"><?php echo number_format($total_biaya_rap_2022_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_akumulasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_desember_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_januari_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_februari_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_maret_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_april_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_mei_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_juni_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_juli_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_all_biaya,0,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="right" colspan="2">LABA</th>
				<th align="center"></th>
				<th align="right"><?php echo number_format($total_laba_rap_2022,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_sd_agustus,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_desember,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_januari,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_februari,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_maret,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_april,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_mei,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_juni,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_juli,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_laba_all,0,',','.');?></th>
			</tr>
	    </table>
		
	</body>
</html>