<!DOCTYPE html>
<html>
	<head>
	  <title>CASH FLOW</title>
	  
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
					<div style="display: block;font-weight: bold;font-size: 12px;">CASH FLOW</div>
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
			$rencana_kerja_2022_biaya_1 = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '2021-12-30' and '2021-12-30'")
			->get()->row_array();

			$rencana_kerja_2022_biaya_2 = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '2021-12-31' and '2021-12-31'")
			->get()->row_array();
		
			$total_rap_2022_biaya_bahan = $rencana_kerja_2022_biaya_1['biaya_bahan'] + $rencana_kerja_2022_biaya_2['biaya_bahan'];
			$total_rap_2022_biaya_alat = $rencana_kerja_2022_biaya_1['biaya_alat'] + $rencana_kerja_2022_biaya_2['biaya_alat'];
			$total_rap_2022_biaya_overhead = $rencana_kerja_2022_biaya_1['biaya_overhead'] + $rencana_kerja_2022_biaya_2['biaya_overhead'];
			$total_rap_2022_biaya_bank = $rencana_kerja_2022_biaya_1['biaya_bank'] + $rencana_kerja_2022_biaya_2['biaya_bank'];

			$total_biaya_rap_2022_biaya = $total_rap_2022_biaya_bahan + $total_rap_2022_biaya_alat + $total_rap_2022_biaya_overhead + $total_rap_2022_biaya_bank;

			?>
			<!-- RAP 2022 -->

			<!-- AKUMULASI BULAN TERAKHIR -->
			<?php
			$stock_opname = $this->db->select('date')->order_by('date','desc')->limit(1)->get_where('pmm_remaining_materials_cat',array('status'=>'PUBLISH'))->row_array();
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

			//AKUMULASI BAHAN	
			$bahan_now = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar as total_nilai_keluar')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi <= '$last_opname')")
			->get()->result_array();

			$total_bahan_now = 0;

			foreach ($bahan_now as $a){
				$total_bahan_now += $a['total_nilai_keluar'];
			}

			$pembayaran_bahan_now = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran_penagihan_pembelian pm')
			->where("(pm.tanggal_pembayaran <= '$last_opname')")
			->where("pm.status = 'DISETUJUI'")
			->get()->row_array();
			$pembayaran_bahan_now = $pembayaran_bahan_now['total'];

			//AKUMULASI BAHAN

			//AKUMULASI ALAT
			$nilai_alat_now = $this->db->select('SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("(prm.date_receipt <= '$last_opname')")
			->where("p.kategori_produk = '5'")
			->where("po.status in ('PUBLISH','CLOSED')")
			->get()->row_array();

			$akumulasi_bbm_now = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar_2 as total_nilai_keluar_2')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi <= '$last_opname')")
			->get()->result_array();

			$total_akumulasi_bbm_now = 0;

			foreach ($akumulasi_bbm_now as $b){
				$total_akumulasi_bbm_now += $b['total_nilai_keluar_2'];
			}

			$total_insentif_tm_now = 0;
			$insentif_tm_now = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->where("pdb.akun = 220")
			->where("status = 'PAID'")
			->where("(tanggal_transaksi <= '$last_opname')")
			->get()->row_array();
			$total_insentif_tm_now = $insentif_tm_now['total'];

			$biaya_alat_lainnya = 0;
			$biaya_alat_lainnya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in ('219','505')")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$last_opname')")
			->get()->row_array();
			$biaya_alat_lainnya = $biaya_alat_lainnya['total'];

			$biaya_alat_lainnya_jurnal = 0;
			$biaya_alat_lainnya_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->where("pdb.akun in ('219','505')")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$last_opname')")
			->get()->row_array();
			$biaya_alat_lainnya_jurnal = $biaya_alat_lainnya_jurnal['total'];

			$alat_now = $nilai_alat_now['nilai'] + $total_akumulasi_bbm_now + $total_insentif_tm_now + $biaya_alat_lainnya + $biaya_alat_lainnya_jurnal;
			//AKUMULASI ALAT

			//TERMIN NOW
			$termin_now = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("(pm.tanggal_pembayaran <= '$last_opname')")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();

			//OVERHEAD
			$overhead_15_now = $this->db->select('sum(pdb.jumlah) as total')
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

			$overhead_jurnal_15_now = $this->db->select('sum(pdb.debit) as total')
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

			$overhead_now =  $overhead_15_now['total'] + $overhead_jurnal_15_now['total'];
			//END OVERHEAD

			//DISKONTO
			$diskonto_now = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 168")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi < '$last_opname'")
			->get()->row_array();
			$diskonto_now = $diskonto_now['total'];
			//END DISKONTO

			//PPN KELUAR
			$ppn_masuk_now = $this->db->select('SUM(ppd.tax) as total')
			->from('pmm_penagihan_pembelian_detail ppd')
			->join('pmm_penagihan_pembelian ppp','ppd.penagihan_pembelian_id = ppp.id','left')
			->where("ppp.tanggal_invoice < '$last_opname'")
			->get()->row_array();

			//PPN MASUK
			$ppn_keluar_now = $this->db->select('SUM(ppd.tax) as total')
			->from('pmm_penagihan_penjualan_detail ppd')
			->join('pmm_penagihan_penjualan ppp','ppd.penagihan_id = ppp.id','left')
			->where("ppp.tanggal_invoice < '$last_opname'")
			->get()->row_array();

			//PENERIMAAN PEMINJAMAN
			$penerimaan_penjualan_now = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi < '$last_opname'")
			->get()->row_array();

			//PENGEMBALIAN PEMINJAMAN
			$pengembalian_penjualan_now = $this->db->select('sum(pdb.kredit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi < '$last_opname'")
			->get()->row_array();
			?>
			<!-- AKUMULASI BULAN TERAKHIR -->

			<!-- JANUARI -->
			<?php
			$date_januari_awal = date('2023-01-01');
			$date_januari_akhir = date('2023-01-31');

			$rencana_kerja_januari = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_januari_akhir'")
			->get()->row_array();

			$rencana_kerja_januari_biaya = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_januari_akhir'")
			->get()->row_array();

			$rencana_kerja_januari_biaya_cash_flow = $this->db->select('r.*')
			->from('rak_biaya_cash_flow r')
			->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_januari_akhir'")
			->get()->row_array();

			$volume_januari_produk_a = $rencana_kerja_januari['vol_produk_a'];
			$volume_januari_produk_b = $rencana_kerja_januari['vol_produk_b'];
			$volume_januari_produk_c = $rencana_kerja_januari['vol_produk_c'];
			$volume_januari_produk_d = $rencana_kerja_januari['vol_produk_d'];

			$total_januari_volume = $volume_januari_produk_a + $volume_januari_produk_b + $volume_januari_produk_c + $volume_januari_produk_d;
		
			$nilai_jual_125_januari = $volume_januari_produk_a * $rencana_kerja_januari['price_a'];
			$nilai_jual_225_januari = $volume_januari_produk_b * $rencana_kerja_januari['price_b'];
			$nilai_jual_250_januari = $volume_januari_produk_c * $rencana_kerja_januari['price_c'];
			$nilai_jual_250_18_januari = $volume_januari_produk_d * $rencana_kerja_januari['price_d'];
			$nilai_jual_all_januari = $nilai_jual_125_januari + $nilai_jual_225_januari + $nilai_jual_250_januari + $nilai_jual_250_18_januari;
			
			$total_januari_nilai = $nilai_jual_all_januari;
			
			$volume_rencana_kerja_januari_produk_a = $rencana_kerja_januari['vol_produk_a'];
			$volume_rencana_kerja_januari_produk_b = $rencana_kerja_januari['vol_produk_b'];
			$volume_rencana_kerja_januari_produk_c = $rencana_kerja_januari['vol_produk_c'];
			$volume_rencana_kerja_januari_produk_d = $rencana_kerja_januari['vol_produk_d'];
		
			$total_januari_biaya_bahan_rap = $rencana_kerja_januari_biaya['biaya_bahan'];
			$total_januari_biaya_alat_rap = $rencana_kerja_januari_biaya['biaya_alat'];
			$total_biaya_januari_biaya_rap = $total_januari_biaya_bahan_rap + $total_januari_biaya_alat_rap;

			$total_januari_biaya_bahan = $rencana_kerja_januari_biaya_cash_flow['biaya_bahan'];
			$total_januari_biaya_alat = $rencana_kerja_januari_biaya_cash_flow['biaya_alat'];
			$total_januari_biaya_overhead = $rencana_kerja_januari_biaya_cash_flow['biaya_overhead'];
			$total_januari_biaya_bank = $rencana_kerja_januari_biaya_cash_flow['biaya_bank'];
			$total_januari_biaya_termin = $rencana_kerja_januari_biaya_cash_flow['biaya_bank'];
			
			//TERMIN JANUARI
			$termin_januari = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_januari_awal' and '$date_januari_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();

			//PENERIMAAN PEMINJAMAN
			$penerimaan_penjualan_januari = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_januari_awal' and '$date_januari_akhir'")
			->get()->row_array();

			//PENGEMBALIAN PEMINJAMAN
			$pengembalian_penjualan_januari = $this->db->select('sum(pdb.kredit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_januari_awal' and '$date_januari_akhir'")
			->get()->row_array();
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

			$rencana_kerja_februari_biaya = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_februari_awal' and '$date_februari_akhir'")
			->get()->row_array();

			$rencana_kerja_februari_biaya_cash_flow = $this->db->select('r.*')
			->from('rak_biaya_cash_flow r')
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
			
			$total_februari_nilai = $nilai_jual_all_februari;
			
			$volume_rencana_kerja_februari_produk_a = $rencana_kerja_februari['vol_produk_a'];
			$volume_rencana_kerja_februari_produk_b = $rencana_kerja_februari['vol_produk_b'];
			$volume_rencana_kerja_februari_produk_c = $rencana_kerja_februari['vol_produk_c'];
			$volume_rencana_kerja_februari_produk_d = $rencana_kerja_februari['vol_produk_d'];
		
			$total_februari_biaya_bahan_rap = $rencana_kerja_februari_biaya['biaya_bahan'];
			$total_februari_biaya_alat_rap = $rencana_kerja_februari_biaya['biaya_alat'];
			$total_biaya_februari_biaya_rap = $total_februari_biaya_bahan_rap + $total_februari_biaya_alat_rap;

			$total_februari_biaya_bahan = $rencana_kerja_februari_biaya_cash_flow['biaya_bahan'];
			$total_februari_biaya_alat = $rencana_kerja_februari_biaya_cash_flow['biaya_alat'];
			$total_februari_biaya_overhead = $rencana_kerja_februari_biaya_cash_flow['biaya_overhead'];
			$total_februari_biaya_bank = $rencana_kerja_februari_biaya_cash_flow['biaya_bank'];
			$total_februari_biaya_termin = $rencana_kerja_februari_biaya_cash_flow['biaya_bank'];
			
			//TERMIN FEBRUARI
			$termin_februari = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_februari_awal' and '$date_februari_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();

			//PENERIMAAN PEMINJAMAN
			$penerimaan_penjualan_februari = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_februari_awal' and '$date_februari_akhir'")
			->get()->row_array();

			//PENGEMBALIAN PEMINJAMAN
			$pengembalian_penjualan_februari = $this->db->select('sum(pdb.kredit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_februari_awal' and '$date_februari_akhir'")
			->get()->row_array();
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

			$rencana_kerja_maret_biaya = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_maret_awal' and '$date_maret_akhir'")
			->get()->row_array();

			$rencana_kerja_maret_biaya_cash_flow = $this->db->select('r.*')
			->from('rak_biaya_cash_flow r')
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
			$nilai_jual_all_maret = $nilai_jual_125_maret + $nilai_jual_225_maret + $nilai_jual_250_maret + $nilai_jual_250_18_maret;
			
			$total_maret_nilai = $nilai_jual_all_maret;
			
			$volume_rencana_kerja_maret_produk_a = $rencana_kerja_maret['vol_produk_a'];
			$volume_rencana_kerja_maret_produk_b = $rencana_kerja_maret['vol_produk_b'];
			$volume_rencana_kerja_maret_produk_c = $rencana_kerja_maret['vol_produk_c'];
			$volume_rencana_kerja_maret_produk_d = $rencana_kerja_maret['vol_produk_d'];
		
			$total_maret_biaya_bahan_rap = $rencana_kerja_maret_biaya['biaya_bahan'];
			$total_maret_biaya_alat_rap = $rencana_kerja_maret_biaya['biaya_alat'];
			$total_biaya_maret_biaya_rap = $total_maret_biaya_bahan_rap + $total_maret_biaya_alat_rap;

			$total_maret_biaya_bahan = $rencana_kerja_maret_biaya_cash_flow['biaya_bahan'];
			$total_maret_biaya_alat = $rencana_kerja_maret_biaya_cash_flow['biaya_alat'];
			$total_maret_biaya_overhead = $rencana_kerja_maret_biaya_cash_flow['biaya_overhead'];
			$total_maret_biaya_bank = $rencana_kerja_maret_biaya_cash_flow['biaya_bank'];
			$total_maret_biaya_termin = $rencana_kerja_maret_biaya_cash_flow['biaya_bank'];
			
			//TERMIN MARET
			$termin_maret = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_maret_awal' and '$date_maret_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();

			//PENERIMAAN PEMINJAMAN
			$penerimaan_penjualan_maret = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_maret_awal' and '$date_maret_akhir'")
			->get()->row_array();

			//PENGEMBALIAN PEMINJAMAN
			$pengembalian_penjualan_maret = $this->db->select('sum(pdb.kredit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_maret_awal' and '$date_maret_akhir'")
			->get()->row_array();
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

			$rencana_kerja_april_biaya = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_april_awal' and '$date_april_akhir'")
			->get()->row_array();

			$rencana_kerja_april_biaya_cash_flow = $this->db->select('r.*')
			->from('rak_biaya_cash_flow r')
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
			$nilai_jual_all_april = $nilai_jual_125_april + $nilai_jual_225_april + $nilai_jual_250_april + $nilai_jual_250_18_april;
			
			$total_april_nilai = $nilai_jual_all_april;
			
			$volume_rencana_kerja_april_produk_a = $rencana_kerja_april['vol_produk_a'];
			$volume_rencana_kerja_april_produk_b = $rencana_kerja_april['vol_produk_b'];
			$volume_rencana_kerja_april_produk_c = $rencana_kerja_april['vol_produk_c'];
			$volume_rencana_kerja_april_produk_d = $rencana_kerja_april['vol_produk_d'];
		
			$total_april_biaya_bahan_rap = $rencana_kerja_april_biaya['biaya_bahan'];
			$total_april_biaya_alat_rap = $rencana_kerja_april_biaya['biaya_alat'];
			$total_biaya_april_biaya_rap = $total_april_biaya_bahan_rap + $total_april_biaya_alat_rap;

			$total_april_biaya_bahan = $rencana_kerja_april_biaya_cash_flow['biaya_bahan'];
			$total_april_biaya_alat = $rencana_kerja_april_biaya_cash_flow['biaya_alat'];
			$total_april_biaya_overhead = $rencana_kerja_april_biaya_cash_flow['biaya_overhead'];
			$total_april_biaya_bank = $rencana_kerja_april_biaya_cash_flow['biaya_bank'];
			$total_april_biaya_termin = $rencana_kerja_april_biaya_cash_flow['biaya_bank'];
			
			//TERMIN APRIL
			$termin_april = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_april_awal' and '$date_april_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();

			//PENERIMAAN PEMINJAMAN
			$penerimaan_penjualan_april = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_april_awal' and '$date_april_akhir'")
			->get()->row_array();

			//PENGEMBALIAN PEMINJAMAN
			$pengembalian_penjualan_april = $this->db->select('sum(pdb.kredit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_april_awal' and '$date_april_akhir'")
			->get()->row_array();
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

			$rencana_kerja_mei_biaya = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_mei_awal' and '$date_mei_akhir'")
			->get()->row_array();

			$rencana_kerja_mei_biaya_cash_flow = $this->db->select('r.*')
			->from('rak_biaya_cash_flow r')
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
			$nilai_jual_all_mei = $nilai_jual_125_mei + $nilai_jual_225_mei + $nilai_jual_250_mei + $nilai_jual_250_18_mei;
			
			$total_mei_nilai = $nilai_jual_all_mei;
			
			$volume_rencana_kerja_mei_produk_a = $rencana_kerja_mei['vol_produk_a'];
			$volume_rencana_kerja_mei_produk_b = $rencana_kerja_mei['vol_produk_b'];
			$volume_rencana_kerja_mei_produk_c = $rencana_kerja_mei['vol_produk_c'];
			$volume_rencana_kerja_mei_produk_d = $rencana_kerja_mei['vol_produk_d'];
		
			$total_mei_biaya_bahan_rap = $rencana_kerja_mei_biaya['biaya_bahan'];
			$total_mei_biaya_alat_rap = $rencana_kerja_mei_biaya['biaya_alat'];
			$total_biaya_mei_biaya_rap = $total_mei_biaya_bahan_rap + $total_mei_biaya_alat_rap;

			$total_mei_biaya_bahan = $rencana_kerja_mei_biaya_cash_flow['biaya_bahan'];
			$total_mei_biaya_alat = $rencana_kerja_mei_biaya_cash_flow['biaya_alat'];
			$total_mei_biaya_overhead = $rencana_kerja_mei_biaya_cash_flow['biaya_overhead'];
			$total_mei_biaya_bank = $rencana_kerja_mei_biaya_cash_flow['biaya_bank'];
			$total_mei_biaya_termin = $rencana_kerja_mei_biaya_cash_flow['biaya_bank'];
			
			//TERMIN MEI
			$termin_mei = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_mei_awal' and '$date_mei_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();

			//PENERIMAAN PEMINJAMAN
			$penerimaan_penjualan_mei = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_mei_awal' and '$date_mei_akhir'")
			->get()->row_array();

			//PENGEMBALIAN PEMINJAMAN
			$pengembalian_penjualan_mei = $this->db->select('sum(pdb.kredit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_mei_awal' and '$date_mei_akhir'")
			->get()->row_array();
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

			$rencana_kerja_juni_biaya = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_juni_awal' and '$date_juni_akhir'")
			->get()->row_array();

			$rencana_kerja_juni_biaya_cash_flow = $this->db->select('r.*')
			->from('rak_biaya_cash_flow r')
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
			$nilai_jual_all_juni = $nilai_jual_125_juni + $nilai_jual_225_juni + $nilai_jual_250_juni + $nilai_jual_250_18_juni;
			
			$total_juni_nilai = $nilai_jual_all_juni;
			
			$volume_rencana_kerja_juni_produk_a = $rencana_kerja_juni['vol_produk_a'];
			$volume_rencana_kerja_juni_produk_b = $rencana_kerja_juni['vol_produk_b'];
			$volume_rencana_kerja_juni_produk_c = $rencana_kerja_juni['vol_produk_c'];
			$volume_rencana_kerja_juni_produk_d = $rencana_kerja_juni['vol_produk_d'];
		
			$total_juni_biaya_bahan_rap = $rencana_kerja_juni_biaya['biaya_bahan'];
			$total_juni_biaya_alat_rap = $rencana_kerja_juni_biaya['biaya_alat'];
			$total_biaya_juni_biaya_rap = $total_juni_biaya_bahan_rap + $total_juni_biaya_alat_rap;

			$total_juni_biaya_bahan = $rencana_kerja_juni_biaya_cash_flow['biaya_bahan'];
			$total_juni_biaya_alat = $rencana_kerja_juni_biaya_cash_flow['biaya_alat'];
			$total_juni_biaya_overhead = $rencana_kerja_juni_biaya_cash_flow['biaya_overhead'];
			$total_juni_biaya_bank = $rencana_kerja_juni_biaya_cash_flow['biaya_bank'];
			$total_juni_biaya_termin = $rencana_kerja_juni_biaya_cash_flow['biaya_bank'];
			
			//TERMIN JUNI
			$termin_juni = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_juni_awal' and '$date_juni_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();

			//PENERIMAAN PEMINJAMAN
			$penerimaan_penjualan_juni = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_juni_awal' and '$date_juni_akhir'")
			->get()->row_array();

			//PENGEMBALIAN PEMINJAMAN
			$pengembalian_penjualan_juni = $this->db->select('sum(pdb.kredit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_juni_awal' and '$date_juni_akhir'")
			->get()->row_array();
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

			$rencana_kerja_juli_biaya = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_juli_awal' and '$date_juli_akhir'")
			->get()->row_array();

			$rencana_kerja_juli_biaya_cash_flow = $this->db->select('r.*')
			->from('rak_biaya_cash_flow r')
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
			$nilai_jual_all_juli = $nilai_jual_125_juli + $nilai_jual_225_juli + $nilai_jual_250_juli + $nilai_jual_250_18_juli;
			
			$total_juli_nilai = $nilai_jual_all_juli;
			
			$volume_rencana_kerja_juli_produk_a = $rencana_kerja_juli['vol_produk_a'];
			$volume_rencana_kerja_juli_produk_b = $rencana_kerja_juli['vol_produk_b'];
			$volume_rencana_kerja_juli_produk_c = $rencana_kerja_juli['vol_produk_c'];
			$volume_rencana_kerja_juli_produk_d = $rencana_kerja_juli['vol_produk_d'];
		
			$total_juli_biaya_bahan_rap = $rencana_kerja_juli_biaya['biaya_bahan'];
			$total_juli_biaya_alat_rap = $rencana_kerja_juli_biaya['biaya_alat'];
			$total_biaya_juli_biaya_rap = $total_juli_biaya_bahan_rap + $total_juli_biaya_alat_rap;

			$total_juli_biaya_bahan = $rencana_kerja_juli_biaya_cash_flow['biaya_bahan'];
			$total_juli_biaya_alat = $rencana_kerja_juli_biaya_cash_flow['biaya_alat'];
			$total_juli_biaya_overhead = $rencana_kerja_juli_biaya_cash_flow['biaya_overhead'];
			$total_juli_biaya_bank = $rencana_kerja_juli_biaya_cash_flow['biaya_bank'];
			$total_juli_biaya_termin = $rencana_kerja_juli_biaya_cash_flow['biaya_bank'];
			
			//TERMIN JULI
			$termin_juli = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_juli_awal' and '$date_juli_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();

			//PENERIMAAN PEMINJAMAN
			$penerimaan_penjualan_juli = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_juli_awal' and '$date_juli_akhir'")
			->get()->row_array();

			//PENGEMBALIAN PEMINJAMAN
			$pengembalian_penjualan_juli = $this->db->select('sum(pdb.kredit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_juli_awal' and '$date_juli_akhir'")
			->get()->row_array();
			?>
			<!-- JULI -->

			<tr class="table-judul">
				<th width="20%" align="center" rowspan="2">&nbsp; <br />URAIAN</th>
				<th width="16%" align="center">CURRENT</th>
				<th width="8%" align="center">REALISASI SD.</th>
				<th width="8%" align="center">MARET</th>
				<th width="8%" align="center">APRIL</th>
				<th width="8%" align="center">MEI</th>
				<th width="8%" align="center">JUNI</th>
				<th width="8%" align="center">JULI</th>
				<th width="8%" align="center" rowspan="2">&nbsp; <br />JUMLAH</th>
				<th width="8%" align="center" rowspan="2">&nbsp; <br />SISA</th>
	        </tr>
			<tr class="table-judul">
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
				<th align="center">CASH BUDGET</th>
				<th align="center"><div style="text-transform:uppercase;"><?= tgl_indo(date($date)); ?></div></th>
				<th align="center">2023</th>
				<th align="center">2023</th>
				<th align="center">2023</th>
				<th align="center">2023</th>
				<th align="center">2023</th>
	        </tr>
			<?php
			$presentase_now = ($penjualan_now['total'] / $total_rap_nilai_2022) * 100;
			$presentase_maret = ($total_maret_nilai / $total_rap_nilai_2022) * 100;
			$presentase_april = ($total_april_nilai / $total_rap_nilai_2022) * 100;
			$presentase_mei = ($total_mei_nilai / $total_rap_nilai_2022) * 100;
			$presentase_juni = ($total_juni_nilai / $total_rap_nilai_2022) * 100;
			$presentase_juli = ($total_juli_nilai / $total_rap_nilai_2022) * 100;

			$presentase_akumulasi_maret = $presentase_now + $presentase_maret;
			$presentase_akumulasi_april = $presentase_akumulasi_maret + $presentase_april;
			$presentase_akumulasi_mei = $presentase_akumulasi_april + $presentase_mei;
			$presentase_akumulasi_juni = $presentase_akumulasi_mei + $presentase_juni;
			$presentase_akumulasi_juli = $presentase_akumulasi_juni + $presentase_juli;

			$jumlah_presentase = $presentase_maret + $presentase_april + $presentase_mei + $presentase_juni + $presentase_juli;
			?>
			<?php
			$jumlah_produksi = $penjualan_now['total'] + $total_maret_nilai + $total_april_nilai + $total_mei_nilai + $total_juni_nilai + $total_juli_nilai;
			$sisa_produksi = $total_rap_nilai_2022 - $jumlah_produksi;
			?>
			<?php
			$akumulasi_penjualan_maret = $penjualan_now['total'] + $total_maret_nilai;
			$akumulasi_penjualan_april = $penjualan_now['total'] + $total_maret_nilai + $total_april_nilai;
			$akumulasi_penjualan_mei = $penjualan_now['total'] + $total_maret_nilai + $total_april_nilai + $total_mei_nilai;
			$akumulasi_penjualan_juni = $penjualan_now['total'] + $total_maret_nilai + $total_april_nilai + $total_mei_nilai + $total_juni_nilai;
			$akumulasi_penjualan_juli = $penjualan_now['total'] + $total_maret_nilai + $total_april_nilai + $total_mei_nilai + $total_juni_nilai + $total_juli_nilai;
			$akumulasi_1 = $total_rap_nilai_2022;
			?>
			<?php
			$termin_maret = $rencana_kerja_maret_biaya_cash_flow['termin'];
			$termin_april = $rencana_kerja_april_biaya_cash_flow['termin'];
			$termin_mei = $rencana_kerja_mei_biaya_cash_flow['termin'];
			$termin_juni = $rencana_kerja_juni_biaya_cash_flow['termin'];
			$termin_juli = $rencana_kerja_juli_biaya_cash_flow['termin'];
			$jumlah_termin = $termin_now['total'] + $termin_maret + $termin_april + $termin_mei + $termin_juni + $termin_juli;
			?>
			<?php
			$akumulasi_termin_maret = $termin_now['total'] + $termin_maret;
			$akumulasi_termin_april = $termin_now['total'] + $termin_maret + $termin_april;
			$akumulasi_termin_mei = $termin_now['total'] + $termin_maret + $termin_april + $termin_mei;
			$akumulasi_termin_juni = $termin_now['total'] + $termin_maret + $termin_april + $termin_mei + $termin_juni;
			$akumulasi_termin_juli = $termin_now['total'] + $termin_maret + $termin_april + $termin_mei + $termin_juni + $termin_juli;
			
			$akumulasi_2 = (($total_rap_nilai_2022 * 11) / 100) + $total_rap_nilai_2022;
			?>
			<?php
			$jumlah_bahan_rap = $total_bahan_now + $total_maret_biaya_bahan_rap + $total_april_biaya_bahan_rap + $total_mei_biaya_bahan_rap + $total_juni_biaya_bahan_rap + $total_juli_biaya_bahan_rap;
			$sisa_bahan_rap = $total_rap_2022_biaya_bahan - $jumlah_bahan_rap;
			?>
			<?php
			$jumlah_alat_rap = $alat_now + $total_maret_biaya_alat_rap + $total_april_biaya_alat_rap + $total_mei_biaya_alat_rap + $total_juni_biaya_alat_rap + $total_juli_biaya_alat_rap;
			$sisa_alat_rap = $total_rap_2022_biaya_alat - $jumlah_alat_rap;
			?>
			<?php
			$jumlah_pemakaian_rap = $jumlah_bahan_rap + $jumlah_alat_rap;
			$sisa_pemakaian_rap = ($total_rap_2022_biaya_bahan + $total_rap_2022_biaya_alat) - $jumlah_pemakaian_rap;
			?>
			<?php
			$akumulasi_pemakaian_rap_bahan_alat = $total_rap_2022_biaya_bahan + $total_rap_2022_biaya_alat;
			$akumulasi_pemakaian_maret = $total_maret_biaya_bahan_rap + $total_maret_biaya_alat_rap;
			$akumulasi_pemakaian_april = $total_maret_biaya_bahan_rap + $total_maret_biaya_alat_rap + $total_april_biaya_bahan_rap + $total_april_biaya_alat_rap;
			$akumulasi_pemakaian_mei = $total_maret_biaya_bahan_rap + $total_maret_biaya_alat_rap + $total_april_biaya_bahan_rap + $total_april_biaya_alat_rap + $total_mei_biaya_bahan_rap + $total_mei_biaya_alat_rap;
			$akumulasi_pemakaian_juni = $total_maret_biaya_bahan_rap + $total_maret_biaya_alat_rap + $total_april_biaya_bahan_rap + $total_april_biaya_alat_rap + $total_mei_biaya_bahan_rap + $total_mei_biaya_alat_rap  + $total_juni_biaya_bahan_rap + $total_juni_biaya_alat_rap;
			$akumulasi_pemakaian_juli = $total_maret_biaya_bahan_rap + $total_maret_biaya_alat_rap + $total_april_biaya_bahan_rap + $total_april_biaya_alat_rap + $total_mei_biaya_bahan_rap + $total_mei_biaya_alat_rap  + $total_juni_biaya_bahan_rap + $total_juni_biaya_alat_rap + $total_juli_biaya_bahan_rap + $total_juli_biaya_alat_rap;
			$akumulasi_3 = $akumulasi_2 - $akumulasi_pemakaian_rap_bahan_alat;
			$jumlah_akumulasi = $akumulasi_pemakaian_juli;
			?>
			<?php
			$jumlah_biaya_bahan = $total_maret_biaya_bahan + $total_april_biaya_bahan + $total_mei_biaya_bahan + $total_juni_biaya_bahan + $total_juli_biaya_bahan;
			$sisa_biaya_bahan = $total_rap_2022_biaya_bahan - $jumlah_biaya_bahan;
			?>
			<?php
			$jumlah_biaya_alat = $total_maret_biaya_alat + $total_april_biaya_alat + $total_mei_biaya_alat + $total_juni_biaya_alat + $total_juli_biaya_alat;
			$sisa_biaya_alat = $total_rap_2022_biaya_alat - $jumlah_biaya_alat;
			?>
			<?php
			$jumlah_biaya_bank = $total_maret_biaya_bank + $total_april_biaya_bank + $total_mei_biaya_bank + $total_juni_biaya_bank + $total_juli_biaya_bank;
			$sisa_biaya_bank = $total_rap_2022_biaya_bank - $jumlah_biaya_bank;
			?>
			<?php
			$jumlah_pengeluaran = $total_rap_2022_biaya_bahan + $total_rap_2022_biaya_alat + $total_rap_2022_biaya_overhead + $total_rap_2022_biaya_bank;
			$jumlah_pengeluaran_akumulasi = $pembayaran_bahan_now + $alat_now + $diskonto_now + $overhead_now;
			$jumlah_pengeluaran_maret = $total_maret_biaya_bahan + $total_maret_biaya_alat + $total_maret_biaya_overhead + $total_maret_biaya_bank;
			$jumlah_pengeluaran_april = $total_april_biaya_bahan + $total_april_biaya_alat + $total_april_biaya_overhead + $total_april_biaya_bank;
			$jumlah_pengeluaran_mei = $total_mei_biaya_bahan + $total_mei_biaya_alat + $total_mei_biaya_overhead + $total_mei_biaya_bank;
			$jumlah_pengeluaran_juni = $total_juni_biaya_bahan + $total_juni_biaya_alat + $total_juni_biaya_overhead + $total_juni_biaya_bank;
			$jumlah_pengeluaran_juli = $total_juli_biaya_bahan + $total_juli_biaya_alat + $total_juli_biaya_overhead + $total_juli_biaya_bank;
			$total_pengeluaran = $jumlah_pengeluaran_maret + $jumlah_pengeluaran_april + $jumlah_pengeluaran_mei + $jumlah_pengeluaran_juni + $jumlah_pengeluaran_juli;
			$sisa_pengeluaran = $jumlah_pengeluaran - $total_pengeluaran;
			?>
			<?php
			$jumlah_biaya_overhead = $total_maret_biaya_overhead + $total_april_biaya_overhead + $total_mei_biaya_overhead + $total_juni_biaya_overhead + $total_juli_biaya_overhead;
			$sisa_biaya_overhead = $total_rap_2022_biaya_overhead - $jumlah_biaya_overhead;
			?>
			<?php
			$akumulasi_4 = $akumulasi_2 - $jumlah_pengeluaran;
			$jumlah_akumulasi_now = $akumulasi_4 - $jumlah_pengeluaran_akumulasi;
			$jumlah_akumulasi_maret = $jumlah_akumulasi_now - $jumlah_pengeluaran_maret;
			$jumlah_akumulasi_april = $jumlah_akumulasi_maret - $jumlah_pengeluaran_april;
			$jumlah_akumulasi_mei = $jumlah_akumulasi_april - $jumlah_pengeluaran_mei;
			$jumlah_akumulasi_juni = $jumlah_akumulasi_mei - $jumlah_pengeluaran_juni;
			$jumlah_akumulasi_juli = $jumlah_akumulasi_juni - $jumlah_pengeluaran_juli;
			$total_akumulasi = $jumlah_akumulasi_juli - $total_pengeluaran;
			$sisa_akumulasi = $akumulasi_4 - $total_akumulasi;
			?>
			<?php
			$akumulasi_5 = $akumulasi_4 - ((($total_rap_nilai_2022 * 11) / 100) - (($total_rap_2022_biaya_bahan * 11) / 100));
			$total_akumulasi_pajak = $akumulasi_5 - ($ppn_keluar_now['total'] - $ppn_masuk_now['total']);
			$sisa_akumulasi_pajak = $total_akumulasi_pajak - ($ppn_keluar_now['total'] - $ppn_masuk_now['total']);
			?>
            <?php
            $total_penerimaan_penjualan = $penerimaan_penjualan_now['total'] + $penerimaan_penjualan_maret['total'] + $penerimaan_penjualan_april['total'] + $penerimaan_penjualan_mei['total'] + $penerimaan_penjualan_juni['total'] + $penerimaan_penjualan_juli['total'];
            $sisa_penerimaan_penjualan = $penerimaan_penjualan_now['total'] - $total_penerimaan_penjualan;
            ?>
            <?php
            $total_pengembalian_penjualan = $pengembalian_penjualan_now['total'] + $pengembalian_penjualan_maret['total'] + $pengembalian_penjualan_april['total'] + $pengembalian_penjualan_mei['total'] + $pengembalian_penjualan_juni['total'] + $pengembalian_penjualan_juli['total'];
            $sisa_pengembalian_penjualan = $pengembalian_penjualan_now['total'] - $total_pengembalian_penjualan;
            ?>
			<?php
			$jumlah_vii_rap = $penerimaan_penjualan_now['total'] + $pengembalian_penjualan_now['total'];
			$jumlah_vii_now = $penerimaan_penjualan_now['total'] + $pengembalian_penjualan_now['total'];
			$jumlah_vii_maret = $penerimaan_penjualan_maret['total'] + $pengembalian_penjualan_maret['total'];
			$jumlah_vii_april = $penerimaan_penjualan_april['total'] + $pengembalian_penjualan_april['total'];
			$jumlah_vii_mei = $penerimaan_penjualan_mei['total'] + $pengembalian_penjualan_mei['total'];
			$jumlah_vii_juni = $penerimaan_penjualan_juni['total'] + $pengembalian_penjualan_juni['total'];
			$jumlah_vii_juli = $penerimaan_penjualan_juli['total'] + $pengembalian_penjualan_juli['total'];
			$total_jumlah_vii = $total_penerimaan_penjualan + $total_pengembalian_penjualan;
			$sisa_jumlah_vii = $total_penerimaan_penjualan - $total_jumlah_vii; 
			?>
			<?php
			$akumulasi_6 = $akumulasi_5 - $jumlah_vii_rap;
			$posisi_vi_now = $akumulasi_6 - $jumlah_vii_now;
			$posisi_vi_maret = $posisi_vi_now - $jumlah_vii_maret;
			$posisi_vi_april = $posisi_vi_maret - $jumlah_vii_april;
			$posisi_vi_mei = $posisi_vi_april - $jumlah_vii_mei;
			$posisi_vi_juni = $posisi_vi_mei - $jumlah_vii_juni;
			$posisi_vi_juli = $posisi_vi_juni - $jumlah_vii_juli;
			$posisi_vi_total = $posisi_vi_juli - $total_jumlah_vii;
			$posisi_vi_sisa = $akumulasi_6 - $posisi_vi_total;
			?>
			<tr class="table-baris1">
				<th align="left"><u>PRODUKSI (EXCL. PPN)</u></th>
				<th align="right">100%</th>
				<th align="right"><?php echo number_format($presentase_now,2,',','.');?>%</th>
				<th align="right"><?php echo number_format($presentase_maret,2,',','.');?>%</th>
				<th align="right"><?php echo number_format($presentase_april,2,',','.');?>%</th>
				<th align="right"><?php echo number_format($presentase_mei,2,',','.');?>%</th>
				<th align="right"><?php echo number_format($presentase_juni,2,',','.');?>%</th>
				<th align="right"><?php echo number_format($presentase_juli,2,',','.');?>%</th>
				<th align="right"><?php echo number_format($jumlah_presentase,2,',','.');?>%</th>
				<th align="right"><?php echo number_format(100 - $jumlah_presentase,2,',','.');?>%</th>
			</tr>
			<tr class="table-baris1">
				<th align="left">AKUMULASI (%)</th>
				<th align="right">100%</th>
				<th align="right"><?php echo number_format($presentase_now,2,',','.');?>%</th>
				<th align="right"><?php echo number_format($presentase_akumulasi_maret,2,',','.');?>%</th>
				<th align="right"><?php echo number_format($presentase_akumulasi_april,2,',','.');?>%</th>
				<th align="right"><?php echo number_format($presentase_akumulasi_mei,2,',','.');?>%</th>
				<th align="right"><?php echo number_format($presentase_akumulasi_juni,2,',','.');?>%</th>
				<th align="right"><?php echo number_format($presentase_akumulasi_juli,2,',','.');?>%</th>
				<th align="right"><?php echo number_format($presentase_akumulasi_juli,2,',','.');?>%</th>
				<th align="right"><?php echo number_format(100 - $presentase_akumulasi_juli,2,',','.');?>%</th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;1. PRODUKSI (Rp.)</th>
				<th align="right"><?php echo number_format($total_rap_nilai_2022,0,',','.');?></th>
				<th align="right"><?php echo number_format($penjualan_now['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($total_maret_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_april_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_mei_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juni_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juli_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_produksi,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_produksi,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;2. AKUMULASI (Rp.)</th>
				<th align="right"><?php echo number_format($total_rap_nilai_2022,0,',','.');?></th>
				<th align="right"><?php echo number_format($penjualan_now['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_penjualan_maret,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_penjualan_april,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_penjualan_mei,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_penjualan_juni,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_penjualan_juli,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_penjualan_juli,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_rap_nilai_2022 - $akumulasi_penjualan_juli,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="left" colspan="10"><u>PENERIMAAN (EXCL. PPN)</u></th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;Uang Muka</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;Termin / Angsuran</th>
				<th align="right"><?php echo number_format($total_rap_nilai_2022,0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_now['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_maret,0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_april,0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_mei,0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_juni,0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_juli,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_termin,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_rap_nilai_2022 - $jumlah_termin,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;Pengembalian Retensi</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;PPN Keluaran</th>
				<th align="right"><?php echo number_format(($total_rap_nilai_2022 * 11) / 100,0,',','.');?></th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
			</tr>
			<tr class="table-total">
				<th align="left"><i>JUMLAH PENERIMAAN</i></th>
				<th align="right"><?php echo number_format((($total_rap_nilai_2022 * 11) / 100) + $total_rap_nilai_2022,0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_now['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_maret,0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_april,0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_mei,0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_juni,0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_juli,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_termin,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_rap_nilai_2022 - $jumlah_termin,0,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="left"><i>AKUMULASI PENERIMAAN</i></th>
				<th align="right"><?php echo number_format($akumulasi_2,0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_now['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_termin_maret,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_termin_april,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_termin_mei,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_termin_juni,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_termin_juli,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_termin_juli,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_rap_nilai_2022 - $akumulasi_termin_juli,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="left" colspan="10"><u>PEMAKAIAN BAHAN & ALAT</u></th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;1. Bahan</th>
				<th align="right"><?php echo number_format($total_rap_2022_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_bahan_now,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_maret_biaya_bahan_rap,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_april_biaya_bahan_rap,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_mei_biaya_bahan_rap,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juni_biaya_bahan_rap,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juli_biaya_bahan_rap,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_bahan_rap,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_bahan_rap,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;2. Alat</th>
				<th align="right"><?php echo number_format($total_rap_2022_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($alat_now,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_maret_biaya_alat_rap,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_april_biaya_alat_rap,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_mei_biaya_alat_rap,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juni_biaya_alat_rap,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juli_biaya_alat_rap,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_alat_rap,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_alat_rap,0,',','.');?></th>
			</tr>
			<?php
			$jumlah_pemakaian_rap = $jumlah_bahan_rap + $jumlah_alat_rap;
			$sisa_pemakaian_rap = ($total_rap_2022_biaya_bahan + $total_rap_2022_biaya_alat) - $jumlah_pemakaian_rap;
			?>
			<tr class="table-total">
				<th align="left"><i>JUMLAH PEMAKAIAN</i></th>
				<th align="right"><?php echo number_format($total_rap_2022_biaya_bahan + $total_rap_2022_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_bahan_now + $alat_now,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_maret_biaya_bahan_rap + $total_maret_biaya_alat_rap,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_april_biaya_bahan_rap + $total_april_biaya_alat_rap,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_mei_biaya_bahan_rap + $total_mei_biaya_alat_rap,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juni_biaya_bahan_rap + $total_juni_biaya_alat_rap,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juli_biaya_bahan_rap + $total_juli_biaya_alat_rap,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_bahan_rap + $jumlah_alat_rap,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_pemakaian_rap,0,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="left"><i>AKUMULASI PEMAKAIAN</i></th>
				<th align="right"><?php echo number_format($akumulasi_3,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_bahan_now + $alat_now,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_pemakaian_maret,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_pemakaian_april,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_pemakaian_mei,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_pemakaian_juni,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_pemakaian_juli,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_akumulasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_pemakaian_rap,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;1. Biaya Bahan</th>
				<th align="right"><?php echo number_format($total_rap_2022_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($pembayaran_bahan_now,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_maret_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_april_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_mei_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juni_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juli_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_biaya_bahan,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;2. Biaya Upah</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;3. Biaya Peralatan</th>
				<th align="right"><?php echo number_format($total_rap_2022_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($alat_now,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_maret_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_april_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_mei_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juni_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juli_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_biaya_alat,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;4. Biaya Subkontraktor</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;5. Biaya Bank</th>
				<th align="right"><?php echo number_format($total_rap_2022_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($diskonto_now,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_maret_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_april_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_mei_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juni_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juli_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_biaya_bank,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;6. BAU Proyek</th>
				<th align="right"><?php echo number_format($total_rap_2022_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($overhead_now,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_maret_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_april_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_mei_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juni_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juli_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_biaya_overhead,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;7. Rupa - Rupa</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;8. Lain - Lain / Susut Aktiva</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;9. PPN Masukan</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
			</tr>
			<tr class="table-total">
				<th align="left"><i>JUMLAH PENGELUARAN</i></th>
				<th align="right"><?php echo number_format($jumlah_pengeluaran,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_pengeluaran_akumulasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_pengeluaran_maret,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_pengeluaran_april,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_pengeluaran_mei,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_pengeluaran_juni,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_pengeluaran_juli,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_pengeluaran,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_pengeluaran,0,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="left"><i>AKUMULASI PENGELUARAN</i></th>
				<th align="right"><?php echo number_format($akumulasi_4,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_akumulasi_now,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_akumulasi_maret,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_akumulasi_april,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_akumulasi_mei,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_akumulasi_juni,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_akumulasi_juli,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_akumulasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_akumulasi,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="left" colspan="10"><u>PAJAK</u></th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;1. Pajak Keluaran</th>
				<th align="right"><?php echo number_format((($total_rap_nilai_2022 * 11) / 100),0,',','.');?></th>
				<th align="right"><?php echo number_format($ppn_keluar_now['total'],0,',','.');?></th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right"><?php echo number_format($ppn_keluar_now['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format((($total_rap_nilai_2022 * 11) / 100) - $ppn_keluar_now['total'],0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;2. Pajak Masukan</th>
				<th align="right"><?php echo number_format((($total_rap_2022_biaya_bahan * 11) / 100),0,',','.');?></th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right"><?php echo number_format($ppn_masuk_now['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format((($total_rap_2022_biaya_bahan * 11) / 100) - $ppn_masuk_now['total'],0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="left"><i>JUMLAH PAJAK</i></th>
				<th align="right"><?php echo number_format((($total_rap_nilai_2022 * 11) / 100) - (($total_rap_2022_biaya_bahan * 11) / 100),0,',','.');?></th>
				<th align="right"><?php echo number_format($ppn_keluar_now['total'] - $ppn_masuk_now['total'],0,',','.');?></th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right"><?php echo number_format($ppn_keluar_now['total'] - $ppn_masuk_now['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format(($total_rap_nilai_2022 / 10 - $total_rap_2022_biaya_bahan  / 10) - ($ppn_keluar_now['total'] - $ppn_masuk_now['total']),0,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="left"><i>AKUMULASI PAJAK</i></th>
				<th align="right"><?php echo number_format($akumulasi_5,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_5 - ($ppn_keluar_now['total'] - $ppn_masuk_now['total']),0,',','.');?></th>
				<th align="right"></th>
				<th align="right"></th>
				<th align="right"></th>
				<th align="right"></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($total_akumulasi_pajak,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_akumulasi_pajak,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="left" colspan="10"><u>PINJAMAN</u></th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;Penerimaan Pinjaman</th>
				<th align="right"><?php echo number_format($penerimaan_penjualan_now['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($penerimaan_penjualan_now['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($penerimaan_penjualan_maret['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($penerimaan_penjualan_april['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($penerimaan_penjualan_mei['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($penerimaan_penjualan_juni['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($penerimaan_penjualan_juli['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($total_penerimaan_penjualan,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_penerimaan_penjualan,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;Pengembalian Pinjaman</th>
				<th align="right"><?php echo number_format($pengembalian_penjualan_now['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pengembalian_penjualan_now['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pengembalian_penjualan_maret['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pengembalian_penjualan_april['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pengembalian_penjualan_mei['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pengembalian_penjualan_juni['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pengembalian_penjualan_juli['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($total_pengembalian_penjualan,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_pengembalian_penjualan,0,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="left"><i>JUMLAH PINJAMAN</i></th>
				<th align="right"><?php echo number_format($jumlah_vii_rap,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vii_now,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vii_maret,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vii_april,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vii_mei,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vii_juni,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vii_juli,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_jumlah_vii,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_jumlah_vii,0,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="left"><i>AKUMULASI PINJAMAN</i></th>
				<th align="right"><?php echo number_format($akumulasi_6,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_now,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_maret,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_april,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_mei,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_juni,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_juli,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_total,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_sisa,0,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="left"><i>KAS AWAL</i></th>
				<th align="right"><?php echo number_format($akumulasi_6,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_now,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_maret,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_april,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_mei,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_juni,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_juli,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_total,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_sisa,0,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="left"><i>KAS AKHIR</i></th>
				<th align="right"><?php echo number_format($akumulasi_6,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_now,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_maret,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_april,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_mei,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_juni,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_juli,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_total,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_sisa,0,',','.');?></th>
			</tr>
	    </table>
		
	</body>
</html>