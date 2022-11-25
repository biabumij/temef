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
			$rencana_kerja_2022 = $this->db->select('r.*')
			->from('rak r')
			->order_by('r.tanggal_rencana_kerja','asc')->limit(1)
			->get()->row_array();

			$volume_rap_2022_produk_a = $rencana_kerja_2022['vol_produk_a'];
			$volume_rap_2022_produk_b = $rencana_kerja_2022['vol_produk_b'];
			$volume_rap_2022_produk_c = $rencana_kerja_2022['vol_produk_c'];
			$volume_rap_2022_produk_d = $rencana_kerja_2022['vol_produk_d'];

			$total_rap_volume_2022 = $volume_rap_2022_produk_a + $volume_rap_2022_produk_b + $volume_rap_2022_produk_c + $volume_rap_2022_produk_d;
			
			//HARGA JUAL RAP
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

			$nilai_jual_125_2022 = $volume_rap_2022_produk_a * $harga_jual_125_rap['harga_satuan'];
			$nilai_jual_225_2022 = $volume_rap_2022_produk_b * $harga_jual_225_rap['harga_satuan'];
			$nilai_jual_250_2022 = $volume_rap_2022_produk_c * $harga_jual_250_rap['harga_satuan'];
			$nilai_jual_250_18_2022 = $volume_rap_2022_produk_d * $harga_jual_250_18_rap['harga_satuan'];
			$nilai_jual_all_2022 = $nilai_jual_125_2022 + $nilai_jual_225_2022 + $nilai_jual_250_2022 + $nilai_jual_250_18_2022;
			
			$total_rap_nilai_2022 = $nilai_jual_all_2022;

			//BIAYA RAP 2022
			$rencana_kerja_2022_biaya = $this->db->select('r.*')
			->from('rak_biaya r')
			->order_by('r.tanggal_rencana_kerja','asc')->limit(1)
			->get()->row_array();

			$total_bahan_all_rap_2022 = $rencana_kerja_2022_biaya['biaya_bahan'];

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
			$total_rap_2022_biaya_overhead = $rencana_kerja_2022_biaya['biaya_overhead'];
			$total_rap_2022_biaya_bank = $rencana_kerja_2022_biaya['biaya_bank'];

			$total_biaya_rap_2022_biaya = $total_rap_2022_biaya_bahan + $total_rap_2022_biaya_alat + $total_rap_2022_biaya_overhead + $total_rap_2022_biaya_bank;

			?>
			<!-- RAP 2022 -->

			<!-- AKUMULASI BULAN TERAKHIR -->
			<?php
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

			//AKUMULASI BAHAN	
			$bahan_now = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar as total_nilai_keluar')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi <= '$last_opname')")
			->get()->result_array();
			file_put_contents("D:\\bahan_now.txt", $this->db->last_query());

			$total_bahan_now = 0;

			foreach ($bahan_now as $a){
				$total_bahan_now += $a['total_nilai_keluar'];
			}
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

			$alat_now = $nilai_alat_now['nilai'] + $total_akumulasi_bbm_now + $total_insentif_tm_now;
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
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$last_opname')")
			->get()->row_array();

			$overhead_jurnal_15_now = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',15)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$last_opname')")
			->get()->row_array();

			$overhead_16_now = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',16)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$last_opname')")
			->get()->row_array();

			$overhead_jurnal_16_now = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',16)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$last_opname')")
			->get()->row_array();

			$overhead_17_now = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',17)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$last_opname')")
			->get()->row_array();

			$overhead_jurnal_17_now = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',17)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$last_opname')")
			->get()->row_array();

			$overhead_now =  $overhead_15_now['total'] + $overhead_jurnal_15_now['total'] + $overhead_16_now['total'] + $overhead_jurnal_16_now['total'] + $overhead_17_now['total'] + $overhead_jurnal_17_now['total'];
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

			<!--- HARGA NOW -->
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
			?>

			<!-- NOVEMBER -->
			<?php
			$date_november_awal = date('2022-11-01');
			$date_november_akhir = date('2022-11-30');

			$rencana_kerja_november = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_november_awal' and '$date_november_akhir'")
			->get()->row_array();

			$rencana_kerja_november_biaya = $this->db->select('r.*')
			->from('rak_biaya r')
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
			
			$volume_rencana_kerja_november_produk_a = $rencana_kerja_november['vol_produk_a'];
			$volume_rencana_kerja_november_produk_b = $rencana_kerja_november['vol_produk_b'];
			$volume_rencana_kerja_november_produk_c = $rencana_kerja_november['vol_produk_c'];
			$volume_rencana_kerja_november_produk_d = $rencana_kerja_november['vol_produk_d'];

			$total_november_biaya_bahan = $rencana_kerja_november_biaya['biaya_bahan'];
			$total_november_biaya_alat = $rencana_kerja_november_biaya['biaya_alat'];
			$total_november_biaya_overhead = $rencana_kerja_november_biaya['biaya_overhead'];
			$total_november_biaya_bank = $rencana_kerja_november_biaya['biaya_bank'];

			$total_biaya_november_biaya = $total_november_biaya_bahan + $total_november_biaya_alat + $total_november_biaya_overhead + $total_november_biaya_bank;
			
			//TERMIN NOVEMBER
			$termin_november = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_november_awal' and '$date_november_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();

			//PENERIMAAN PEMINJAMAN
			$penerimaan_penjualan_november = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_november_awal' and '$date_november_akhir'")
			->get()->row_array();

			//PENGEMBALIAN PEMINJAMAN
			$pengembalian_penjualan_november = $this->db->select('sum(pdb.kredit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_november_awal' and '$date_november_akhir'")
			->get()->row_array();
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

			$rencana_kerja_desember_biaya = $this->db->select('r.*')
			->from('rak_biaya r')
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
			
			$volume_rencana_kerja_desember_produk_a = $rencana_kerja_desember['vol_produk_a'];
			$volume_rencana_kerja_desember_produk_b = $rencana_kerja_desember['vol_produk_b'];
			$volume_rencana_kerja_desember_produk_c = $rencana_kerja_desember['vol_produk_c'];
			$volume_rencana_kerja_desember_produk_d = $rencana_kerja_desember['vol_produk_d'];
		
			$total_desember_biaya_bahan = $rencana_kerja_desember_biaya['biaya_bahan'];
			$total_desember_biaya_alat = $rencana_kerja_desember_biaya['biaya_alat'];
			$total_desember_biaya_overhead = $rencana_kerja_desember_biaya['biaya_overhead'];
			$total_desember_biaya_bank = $rencana_kerja_desember_biaya['biaya_bank'];

			$total_biaya_desember_biaya = $total_desember_biaya_bahan + $total_desember_biaya_alat + $total_desember_biaya_overhead + $total_desember_biaya_bank;
			
			//TERMIN DESEMBER
			$termin_desember = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_desember_awal' and '$date_desember_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();

			//PENERIMAAN PEMINJAMAN
			$penerimaan_penjualan_desember = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_desember_awal' and '$date_desember_akhir'")
			->get()->row_array();

			//PENGEMBALIAN PEMINJAMAN
			$pengembalian_penjualan_desember = $this->db->select('sum(pdb.kredit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_desember_awal' and '$date_desember_akhir'")
			->get()->row_array();
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

			$rencana_kerja_januari_biaya = $this->db->select('r.*')
			->from('rak_biaya r')
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
			
			$volume_rencana_kerja_januari_produk_a = $rencana_kerja_januari['vol_produk_a'];
			$volume_rencana_kerja_januari_produk_b = $rencana_kerja_januari['vol_produk_b'];
			$volume_rencana_kerja_januari_produk_c = $rencana_kerja_januari['vol_produk_c'];
			$volume_rencana_kerja_januari_produk_d = $rencana_kerja_januari['vol_produk_d'];
		
			$total_januari_biaya_bahan = $rencana_kerja_januari_biaya['biaya_bahan'];
			$total_januari_biaya_alat = $rencana_kerja_januari_biaya['biaya_alat'];
			$total_januari_biaya_overhead = $rencana_kerja_januari_biaya['biaya_overhead'];
			$total_januari_biaya_bank = $rencana_kerja_januari_biaya['biaya_bank'];

			$total_biaya_januari_biaya = $total_januari_biaya_bahan + $total_januari_biaya_alat + $total_januari_biaya_overhead + $total_januari_biaya_bank;
			
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
			
			$volume_rencana_kerja_februari_produk_a = $rencana_kerja_februari['vol_produk_a'];
			$volume_rencana_kerja_februari_produk_b = $rencana_kerja_februari['vol_produk_b'];
			$volume_rencana_kerja_februari_produk_c = $rencana_kerja_februari['vol_produk_c'];
			$volume_rencana_kerja_februari_produk_d = $rencana_kerja_februari['vol_produk_d'];
		
			$total_februari_biaya_bahan = $rencana_kerja_februari_biaya['biaya_bahan'];
			$total_februari_biaya_alat = $rencana_kerja_februari_biaya['biaya_alat'];
			$total_februari_biaya_overhead = $rencana_kerja_februari_biaya['biaya_overhead'];
			$total_februari_biaya_bank = $rencana_kerja_februari_biaya['biaya_bank'];

			$total_biaya_februari_biaya = $total_februari_biaya_bahan + $total_februari_biaya_alat + $total_februari_biaya_overhead + $total_februari_biaya_bank;
			
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
			
			$volume_rencana_kerja_maret_produk_a = $rencana_kerja_maret['vol_produk_a'];
			$volume_rencana_kerja_maret_produk_b = $rencana_kerja_maret['vol_produk_b'];
			$volume_rencana_kerja_maret_produk_c = $rencana_kerja_maret['vol_produk_c'];
			$volume_rencana_kerja_maret_produk_d = $rencana_kerja_maret['vol_produk_d'];
		
			$total_maret_biaya_bahan = $rencana_kerja_maret_biaya['biaya_bahan'];
			$total_maret_biaya_alat = $rencana_kerja_maret_biaya['biaya_alat'];
			$total_maret_biaya_overhead = $rencana_kerja_maret_biaya['biaya_overhead'];
			$total_maret_biaya_bank = $rencana_kerja_maret_biaya['biaya_bank'];

			$total_biaya_maret_biaya = $total_maret_biaya_bahan + $total_maret_biaya_alat + $total_maret_biaya_overhead + $total_maret_biaya_bank;
			
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
			
			$volume_rencana_kerja_april_produk_a = $rencana_kerja_april['vol_produk_a'];
			$volume_rencana_kerja_april_produk_b = $rencana_kerja_april['vol_produk_b'];
			$volume_rencana_kerja_april_produk_c = $rencana_kerja_april['vol_produk_c'];
			$volume_rencana_kerja_april_produk_d = $rencana_kerja_april['vol_produk_d'];
		
			$total_april_biaya_bahan = $rencana_kerja_april_biaya['biaya_bahan'];
			$total_april_biaya_alat = $rencana_kerja_april_biaya['biaya_alat'];
			$total_april_biaya_overhead = $rencana_kerja_april_biaya['biaya_overhead'];
			$total_april_biaya_bank = $rencana_kerja_april_biaya['biaya_bank'];

			$total_biaya_april_biaya = $total_april_biaya_bahan + $total_april_biaya_alat + $total_april_biaya_overhead + $total_april_biaya_bank;
			
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
			
			$volume_rencana_kerja_mei_produk_a = $rencana_kerja_mei['vol_produk_a'];
			$volume_rencana_kerja_mei_produk_b = $rencana_kerja_mei['vol_produk_b'];
			$volume_rencana_kerja_mei_produk_c = $rencana_kerja_mei['vol_produk_c'];
			$volume_rencana_kerja_mei_produk_d = $rencana_kerja_mei['vol_produk_d'];
		
			$total_mei_biaya_bahan = $rencana_kerja_mei_biaya['biaya_bahan'];
			$total_mei_biaya_alat = $rencana_kerja_mei_biaya['biaya_alat'];
			$total_mei_biaya_overhead = $rencana_kerja_mei_biaya['biaya_overhead'];
			$total_mei_biaya_bank = $rencana_kerja_mei_biaya['biaya_bank'];

			$total_biaya_mei_biaya = $total_mei_biaya_bahan + $total_mei_biaya_alat + $total_mei_biaya_overhead + $total_mei_biaya_bank;
			
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
			
			$volume_rencana_kerja_juni_produk_a = $rencana_kerja_juni['vol_produk_a'];
			$volume_rencana_kerja_juni_produk_b = $rencana_kerja_juni['vol_produk_b'];
			$volume_rencana_kerja_juni_produk_c = $rencana_kerja_juni['vol_produk_c'];
			$volume_rencana_kerja_juni_produk_d = $rencana_kerja_juni['vol_produk_d'];
		
			$total_juni_biaya_bahan = $rencana_kerja_juni_biaya['biaya_bahan'];
			$total_juni_biaya_alat = $rencana_kerja_juni_biaya['biaya_alat'];
			$total_juni_biaya_overhead = $rencana_kerja_juni_biaya['biaya_overhead'];
			$total_juni_biaya_bank = $rencana_kerja_juni_biaya['biaya_bank'];

			$total_biaya_juni_biaya = $total_juni_biaya_bahan + $total_juni_biaya_alat + $total_juni_biaya_overhead + $total_juni_biaya_bank;
			
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
			
			$volume_rencana_kerja_juli_produk_a = $rencana_kerja_juli['vol_produk_a'];
			$volume_rencana_kerja_juli_produk_b = $rencana_kerja_juli['vol_produk_b'];
			$volume_rencana_kerja_juli_produk_c = $rencana_kerja_juli['vol_produk_c'];
			$volume_rencana_kerja_juli_produk_d = $rencana_kerja_juli['vol_produk_d'];
		
			$total_juli_biaya_bahan = $rencana_kerja_juli_biaya['biaya_bahan'];
			$total_juli_biaya_alat = $rencana_kerja_juli_biaya['biaya_alat'];
			$total_juli_biaya_overhead = $rencana_kerja_juli_biaya['biaya_overhead'];
			$total_juli_biaya_bank = $rencana_kerja_juli_biaya['biaya_bank'];

			$total_biaya_juli_biaya = $total_juli_biaya_bahan + $total_juli_biaya_alat + $total_juli_biaya_overhead + $total_juli_biaya_bank;
			
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
				<th width="9%" align="center" rowspan="2">&nbsp; <br />URAIAN</th>
				<th width="7%" align="center">CURRENT</th>
				<th width="7%" align="center">REALISASI</th>
				<th width="7%" align="center">NOVEMBER</th>
				<th width="7%" align="center">DESEMBER</th>
				<th width="7%" align="center">JANUARI</th>
				<th width="7%" align="center">FEBRUARI</th>
				<th width="7%" align="center">MARET</th>
				<th width="7%" align="center">APRIL</th>
				<th width="7%" align="center">MEI</th>
				<th width="7%" align="center">JUNI</th>
				<th width="7%" align="center">JULI</th>
				<th width="7%" align="center" rowspan="2">&nbsp; <br />JUMLAH</th>
				<th width="7%" align="center" rowspan="2">&nbsp; <br />SISA</th>
	        </tr>
			<tr class="table-judul">
				<th align="center">CASH BUDGET</th>
				<th align="center">SD. BULAN TERAKHIR</th>
				<th align="center">2022</th>
				<th align="center">2022</th>
				<th align="center">2023</th>
				<th align="center">2023</th>
				<th align="center">2023</th>
				<th align="center">2023</th>
				<th align="center">2023</th>
				<th align="center">2023</th>
				<th align="center">2023</th>
	        </tr>
			<?php
			$presentase_now = ($penjualan_now['total'] / $total_rap_nilai_2022) * 100;
			$presentase_november = ($total_november_nilai / $total_rap_nilai_2022) * 100;
			$presentase_desember = ($total_desember_nilai / $total_rap_nilai_2022) * 100;
			$presentase_januari = ($total_januari_nilai / $total_rap_nilai_2022) * 100;
			$presentase_februari = ($total_februari_nilai / $total_rap_nilai_2022) * 100;
			$presentase_maret = ($total_maret_nilai / $total_rap_nilai_2022) * 100;
			$presentase_april = ($total_april_nilai / $total_rap_nilai_2022) * 100;
			$presentase_mei = ($total_mei_nilai / $total_rap_nilai_2022) * 100;
			$presentase_juni = ($total_juni_nilai / $total_rap_nilai_2022) * 100;
			$presentase_juli = ($total_juli_nilai / $total_rap_nilai_2022) * 100;

			$presentase_akumulasi_november = $presentase_now + $presentase_november;
			$presentase_akumulasi_desember = $presentase_akumulasi_november + $presentase_desember;
			$presentase_akumulasi_januari = $presentase_akumulasi_desember + $presentase_januari;
			$presentase_akumulasi_februari = $presentase_akumulasi_januari + $presentase_februari;
			$presentase_akumulasi_maret = $presentase_akumulasi_februari + $presentase_maret;
			$presentase_akumulasi_april = $presentase_akumulasi_maret + $presentase_april;
			$presentase_akumulasi_mei = $presentase_akumulasi_april + $presentase_mei;
			$presentase_akumulasi_juni = $presentase_akumulasi_mei + $presentase_juni;
			$presentase_akumulasi_juli = $presentase_akumulasi_juni + $presentase_juli;

			$jumlah_presentase = $presentase_november + $presentase_desember + $presentase_januari + $presentase_februari + $presentase_maret + $presentase_april + $presentase_mei + $presentase_juni + $presentase_juli;
			?>
			<tr class="table-baris1">
				<th align="left"><u>PRODUKSI (EXCL. PPN)</u></th>
				<th align="right">100%</th>
				<th align="right"><?php echo number_format($presentase_now,2,',','.');?>%</th>	
				<th align="right"><?php echo number_format($presentase_november,2,',','.');?>%</th>	
				<th align="right"><?php echo number_format($presentase_desember,2,',','.');?>%</th>
				<th align="right"><?php echo number_format($presentase_januari,2,',','.');?>%</th>
				<th align="right"><?php echo number_format($presentase_februari,2,',','.');?>%</th>
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
				<th align="right"><?php echo number_format($presentase_akumulasi_november,2,',','.');?>%</th>
				<th align="right"><?php echo number_format($presentase_akumulasi_desember,2,',','.');?>%</th>
				<th align="right"><?php echo number_format($presentase_akumulasi_januari,2,',','.');?>%</th>
				<th align="right"><?php echo number_format($presentase_akumulasi_februari,2,',','.');?>%</th>
				<th align="right"><?php echo number_format($presentase_akumulasi_maret,2,',','.');?>%</th>
				<th align="right"><?php echo number_format($presentase_akumulasi_april,2,',','.');?>%</th>
				<th align="right"><?php echo number_format($presentase_akumulasi_mei,2,',','.');?>%</th>
				<th align="right"><?php echo number_format($presentase_akumulasi_juni,2,',','.');?>%</th>
				<th align="right"><?php echo number_format($presentase_akumulasi_juli,2,',','.');?>%</th>
				<th align="right"><?php echo number_format($presentase_akumulasi_juli,2,',','.');?>%</th>
				<th align="right"><?php echo number_format(100 - $presentase_akumulasi_juli,2,',','.');?>%</th>
				
			</tr>
			<?php
			$jumlah_produksi = $penjualan_now['total'] + $total_november_nilai + $total_desember_nilai + $total_januari_nilai + $total_februari_nilai + $total_maret_nilai + $total_april_nilai + $total_mei_nilai + $total_juni_nilai + $total_juli_nilai;

			$sisa_produksi = $total_rap_nilai_2022 - $jumlah_produksi;
			?>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;1. PRODUKSI (Rp.)</th>
				<th align="right"><?php echo number_format($total_rap_nilai_2022,0,',','.');?></th>
				<th align="right"><?php echo number_format($penjualan_now['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($total_november_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_desember_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_januari_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_februari_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_maret_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_april_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_mei_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juni_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juli_nilai,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_produksi,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_produksi,0,',','.');?></th>
			</tr>
			<?php
			$akumulasi_penjualan_november = $penjualan_now['total'] + $total_november_nilai;
			$akumulasi_penjualan_desember = $penjualan_now['total'] + $total_november_nilai + $total_desember_nilai;
			$akumulasi_penjualan_januari = $penjualan_now['total'] + $total_november_nilai + $total_desember_nilai + $total_januari_nilai;
			$akumulasi_penjualan_februari = $penjualan_now['total'] + $total_november_nilai + $total_desember_nilai + $total_januari_nilai + $total_februari_nilai;
			$akumulasi_penjualan_maret = $penjualan_now['total'] + $total_november_nilai + $total_desember_nilai + $total_januari_nilai + $total_februari_nilai + $total_maret_nilai;
			$akumulasi_penjualan_april = $penjualan_now['total'] + $total_november_nilai + $total_desember_nilai + $total_januari_nilai + $total_februari_nilai + $total_maret_nilai + $total_april_nilai;
			$akumulasi_penjualan_mei = $penjualan_now['total'] + $total_november_nilai + $total_desember_nilai + $total_januari_nilai + $total_februari_nilai + $total_maret_nilai + $total_april_nilai + $total_mei_nilai;
			$akumulasi_penjualan_juni = $penjualan_now['total'] + $total_november_nilai + $total_desember_nilai + $total_januari_nilai + $total_februari_nilai + $total_maret_nilai + $total_april_nilai + $total_mei_nilai + $total_juni_nilai;
			$akumulasi_penjualan_juli = $penjualan_now['total'] + $total_november_nilai + $total_desember_nilai + $total_januari_nilai + $total_februari_nilai + $total_maret_nilai + $total_april_nilai + $total_mei_nilai + $total_juni_nilai + $total_juli_nilai;
			?>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;2. AKUMULASI (Rp.)</th>
				<th align="right"><?php echo number_format($total_rap_nilai_2022,0,',','.');?></th>
				<th align="right"><?php echo number_format($penjualan_now['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_penjualan_november,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_penjualan_desember,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_penjualan_januari,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_penjualan_februari,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_penjualan_maret,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_penjualan_april,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_penjualan_mei,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_penjualan_juni,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_penjualan_juli,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_penjualan_juli,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_rap_nilai_2022 - $akumulasi_penjualan_juli,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="left" colspan="14"><u>PENERIMAAN (EXCL. PPN)</u></th>
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
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
			</tr>
			<?php
			$termin_november = $rencana_kerja_november_biaya['termin'];
			$termin_desember = $rencana_kerja_desember_biaya['termin'];
			$termin_januari = $rencana_kerja_januari_biaya['termin'];
			$termin_februari = $rencana_kerja_februari_biaya['termin'];
			$termin_maret = $rencana_kerja_maret_biaya['termin'];
			$termin_april = $rencana_kerja_april_biaya['termin'];
			$termin_mei = $rencana_kerja_mei_biaya['termin'];
			$termin_juni = $rencana_kerja_juni_biaya['termin'];
			$termin_juli = $rencana_kerja_juli_biaya['termin'];
			$jumlah_termin = $termin_now['total'] + $termin_november + $termin_desember + $termin_januari + $termin_februari + $termin_maret + $termin_april + $termin_mei + $termin_juni + $termin_juli;
			?>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;Termin / Angsuran</th>
				<th align="right"><?php echo number_format($total_rap_nilai_2022,0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_now['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_november,0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_desember,0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_januari,0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_februari,0,',','.');?></th>
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
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;PPN Keluaran</th>
				<th align="right"><?php echo number_format($total_rap_nilai_2022 / 10,0,',','.');?></th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
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
				<th align="left"><i>JUMLAH PENERIMAAN</i></th>
				<th align="right"><?php echo number_format($total_rap_nilai_2022 / 10 + $total_rap_nilai_2022,0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_now['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_november,0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_desember,0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_januari,0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_februari,0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_maret,0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_april,0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_mei,0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_juni,0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_juli,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_termin,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_rap_nilai_2022 - $jumlah_termin,0,',','.');?></th>
			</tr>
			<?php
			$akumulasi_termin_november = $termin_now['total'] + $termin_november;
			$akumulasi_termin_desember = $termin_now['total'] + $termin_november + $termin_desember;
			$akumulasi_termin_januari = $termin_now['total'] + $termin_november + $termin_desember + $termin_januari;
			$akumulasi_termin_februari = $termin_now['total'] + $termin_november + $termin_desember + $termin_januari + $termin_februari;
			$akumulasi_termin_maret = $termin_now['total'] + $termin_november + $termin_desember + $termin_januari + $termin_februari + $termin_maret;
			$akumulasi_termin_april = $termin_now['total'] + $termin_november + $termin_desember + $termin_januari + $termin_februari + $termin_maret + $termin_april;
			$akumulasi_termin_mei = $termin_now['total'] + $termin_november + $termin_desember + $termin_januari + $termin_februari + $termin_maret + $termin_april + $termin_mei;
			$akumulasi_termin_juni = $termin_now['total'] + $termin_november + $termin_desember + $termin_januari + $termin_februari + $termin_maret + $termin_april + $termin_mei + $termin_juni;
			$akumulasi_termin_juli = $termin_now['total'] + $termin_november + $termin_desember + $termin_januari + $termin_februari + $termin_maret + $termin_april + $termin_mei + $termin_juni + $termin_juli;
			?>
			
			<tr class="table-total">
				<th align="left"><i>AKUMULASI PENERIMAAN</i></th>
				<th align="right"><?php echo number_format($total_rap_nilai_2022,0,',','.');?></th>
				<th align="right"><?php echo number_format($termin_now['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_termin_november,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_termin_desember,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_termin_januari,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_termin_februari,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_termin_maret,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_termin_april,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_termin_mei,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_termin_juni,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_termin_juli,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_termin_juli,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_rap_nilai_2022 - $akumulasi_termin_juli,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="left" colspan="14"><u>PEMAKAIAN BAHAN & ALAT</u></th>
			</tr>
			<?php
			$jumlah_bahan = $total_bahan_now + $total_november_biaya_bahan + $total_desember_biaya_bahan +$total_januari_biaya_bahan + $total_februari_biaya_bahan + $total_maret_biaya_bahan + $total_april_biaya_bahan + $total_mei_biaya_bahan + $total_juni_biaya_bahan + $total_juli_biaya_bahan;
			?>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;1. Bahan</th>
				<th align="right">-</th>
				<th align="right"><?php echo number_format($total_bahan_now,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_november_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_desember_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_januari_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_februari_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_maret_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_april_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_mei_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juni_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juli_biaya_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_bahan,0,',','.');?></th>
				<th align="right">-</th>
			</tr>
			<?php
			$jumlah_alat = $alat_now + $total_november_biaya_alat + $total_desember_biaya_alat + $total_januari_biaya_alat + $total_februari_biaya_alat + $total_maret_biaya_alat + $total_april_biaya_alat + $total_mei_biaya_alat + $total_juni_biaya_alat + $total_juli_biaya_alat;
			?>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;2. Alat</th>
				<th align="right">-</th>
				<th align="right"><?php echo number_format($alat_now,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_november_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_desember_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_januari_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_februari_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_maret_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_april_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_mei_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juni_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juli_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_alat,0,',','.');?></th>
				<th align="right">-</th>
			</tr>
			<tr class="table-total">
				<th align="left"><i>JUMLAH PEMAKAIAN</i></th>
				<th align="right">-</th>
				<th align="right"><?php echo number_format($total_bahan_now + $alat_now,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_november_biaya_bahan + $total_november_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_desember_biaya_bahan + $total_desember_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_januari_biaya_bahan + $total_januari_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_februari_biaya_bahan + $total_februari_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_maret_biaya_bahan + $total_maret_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_april_biaya_bahan + $total_april_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_mei_biaya_bahan + $total_mei_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juni_biaya_bahan + $total_juni_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_juli_biaya_bahan + $total_juli_biaya_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_bahan + $jumlah_alat,0,',','.');?></th>
				<th align="right">-</th>
			</tr>
			<?php
			$akumulasi_pemakaian_november = $total_bahan_now + $alat_now + $total_november_biaya_bahan + $total_november_biaya_alat;
			$akumulasi_pemakaian_desember = $total_bahan_now + $alat_now + $total_november_biaya_bahan + $total_november_biaya_alat + $total_desember_biaya_bahan + $total_desember_biaya_alat;
			$akumulasi_pemakaian_januari = $total_bahan_now + $alat_now + $total_november_biaya_bahan + $total_november_biaya_alat + $total_desember_biaya_bahan + $total_desember_biaya_alat + $total_januari_biaya_bahan + $total_januari_biaya_alat;
			$akumulasi_pemakaian_februari = $total_bahan_now + $alat_now + $total_november_biaya_bahan + $total_november_biaya_alat + $total_desember_biaya_bahan + $total_desember_biaya_alat + $total_januari_biaya_bahan + $total_januari_biaya_alat + $total_februari_biaya_bahan + $total_februari_biaya_alat;
			$akumulasi_pemakaian_maret = $total_bahan_now + $alat_now + $total_november_biaya_bahan + $total_november_biaya_alat + $total_desember_biaya_bahan + $total_desember_biaya_alat + $total_januari_biaya_bahan + $total_januari_biaya_alat + $total_februari_biaya_bahan + $total_februari_biaya_alat + $total_maret_biaya_bahan + $total_maret_biaya_alat;
			$akumulasi_pemakaian_april = $total_bahan_now + $alat_now + $total_november_biaya_bahan + $total_november_biaya_alat + $total_desember_biaya_bahan + $total_desember_biaya_alat + $total_januari_biaya_bahan + $total_januari_biaya_alat + $total_februari_biaya_bahan + $total_februari_biaya_alat + $total_maret_biaya_bahan + $total_maret_biaya_alat + $total_april_biaya_bahan + $total_april_biaya_alat;
			$akumulasi_pemakaian_mei = $total_bahan_now + $alat_now + $total_november_biaya_bahan + $total_november_biaya_alat + $total_desember_biaya_bahan + $total_desember_biaya_alat + $total_januari_biaya_bahan + $total_januari_biaya_alat + $total_februari_biaya_bahan + $total_februari_biaya_alat + $total_maret_biaya_bahan + $total_maret_biaya_alat + $total_april_biaya_bahan + $total_april_biaya_alat + $total_mei_biaya_bahan + $total_mei_biaya_alat;
			$akumulasi_pemakaian_juni = $total_bahan_now + $alat_now + $total_november_biaya_bahan + $total_november_biaya_alat + $total_desember_biaya_bahan + $total_desember_biaya_alat + $total_januari_biaya_bahan + $total_januari_biaya_alat + $total_februari_biaya_bahan + $total_februari_biaya_alat + $total_maret_biaya_bahan + $total_maret_biaya_alat + $total_april_biaya_bahan + $total_april_biaya_alat + $total_mei_biaya_bahan + $total_mei_biaya_alat  + $total_juni_biaya_bahan + $total_juni_biaya_alat;
			$akumulasi_pemakaian_juli = $total_bahan_now + $alat_now + $total_november_biaya_bahan + $total_november_biaya_alat + $total_desember_biaya_bahan + $total_desember_biaya_alat + $total_januari_biaya_bahan + $total_januari_biaya_alat + $total_februari_biaya_bahan + $total_februari_biaya_alat + $total_maret_biaya_bahan + $total_maret_biaya_alat + $total_april_biaya_bahan + $total_april_biaya_alat + $total_mei_biaya_bahan + $total_mei_biaya_alat  + $total_juni_biaya_bahan + $total_juni_biaya_alat + $total_juli_biaya_bahan + $total_juli_biaya_alat;
			?>
			<tr class="table-total">
				<th align="left"><i>AKUMULASI PEMAKAIAN</i></th>
				<th align="right">-</th>
				<th align="right"><?php echo number_format($total_bahan_now + $alat_now,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_pemakaian_november,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_pemakaian_desember,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_pemakaian_januari,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_pemakaian_februari,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_pemakaian_maret,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_pemakaian_april,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_pemakaian_mei,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_pemakaian_juni,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_pemakaian_juli,0,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_pemakaian_juli,0,',','.');?></th>
				<th align="right">-</th>
			</tr>
			<tr class="table-baris1">
				<th align="left" colspan="14"><u>PENGELUARAN (EXCL. PPN)</u></th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;1. Biaya Bahan</th>
				<th align="right"><?php echo number_format($total_bahan_all_rap_2022,0,',','.');?></th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
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
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;3. Biaya Peralatan</th>
				<th align="right"><?php echo number_format($total_rap_2022_biaya_alat,0,',','.');?></th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
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
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
			</tr>
			<?php
			$jumlah_biaya_bank = $diskonto_now + $rencana_kerja_november_biaya['biaya_bank'] + $rencana_kerja_desember_biaya['biaya_bank'] + $rencana_kerja_januari_biaya['biaya_bank'] + $rencana_kerja_februari_biaya['biaya_bank'] + $rencana_kerja_maret_biaya['biaya_bank'] + $rencana_kerja_april_biaya['biaya_bank'] + $rencana_kerja_mei_biaya['biaya_bank'] + $rencana_kerja_juni_biaya['biaya_bank'] + $rencana_kerja_juli_biaya['biaya_bank'];
			?>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;5. Biaya Bank</th>
				<th align="right"><?php echo number_format($total_rap_2022_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($diskonto_now,0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_november_biaya['biaya_bank'],0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_desember_biaya['biaya_bank'],0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_januari_biaya['biaya_bank'],0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_februari_biaya['biaya_bank'],0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_maret_biaya['biaya_bank'],0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_april_biaya['biaya_bank'],0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_mei_biaya['biaya_bank'],0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_juni_biaya['biaya_bank'],0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_juli_biaya['biaya_bank'],0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_biaya_bank,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_rap_2022_biaya_bank - $jumlah_biaya_bank,0,',','.');?></th>
			</tr>
			<?php
			$jumlah_bua = $overhead_now + $rencana_kerja_november_biaya['biaya_overhead'] + $rencana_kerja_desember_biaya['biaya_overhead'] + $rencana_kerja_januari_biaya['biaya_overhead'] + $rencana_kerja_februari_biaya['biaya_overhead'] + $rencana_kerja_maret_biaya['biaya_overhead'] + $rencana_kerja_april_biaya['biaya_overhead'] + $rencana_kerja_mei_biaya['biaya_overhead'] + $rencana_kerja_juni_biaya['biaya_overhead'] + $rencana_kerja_juli_biaya['biaya_overhead'];
			?>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;6. BAU Proyek</th>
				<th align="right"><?php echo number_format($total_rap_2022_biaya_overhead,0,',','.');?></th>
				<th align="right"><?php echo number_format($overhead_now,0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_november_biaya['biaya_overhead'],0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_desember_biaya['biaya_overhead'],0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_januari_biaya['biaya_overhead'],0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_februari_biaya['biaya_overhead'],0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_maret_biaya['biaya_overhead'],0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_april_biaya['biaya_overhead'],0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_mei_biaya['biaya_overhead'],0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_juni_biaya['biaya_overhead'],0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_juli_biaya['biaya_overhead'],0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_bua,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_rap_2022_biaya_overhead - $jumlah_bua,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;7. Rupa - Rupa</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right"></th>
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
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
			</tr>
			<?php
			$total_biaya_bank = $total_rap_2022_biaya_bank - $jumlah_biaya_bank;
			$total_biaya_bua = $total_rap_2022_biaya_overhead - $jumlah_bua;
			?>
			<tr class="table-total">
				<th align="left"><i>JUMLAH III</i></th>
				<th align="right"><?php echo number_format($total_biaya_rap_2022_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($diskonto_now + $overhead_now,0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_november_biaya['biaya_overhead'] + $rencana_kerja_november_biaya['biaya_bank'],0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_desember_biaya['biaya_overhead'] + $rencana_kerja_desember_biaya['biaya_bank'],0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_januari_biaya['biaya_overhead'] + $rencana_kerja_januari_biaya['biaya_bank'],0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_februari_biaya['biaya_overhead'] + $rencana_kerja_februari_biaya['biaya_bank'],0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_maret_biaya['biaya_overhead'] + $rencana_kerja_maret_biaya['biaya_bank'],0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_april_biaya['biaya_overhead'] + $rencana_kerja_april_biaya['biaya_bank'],0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_mei_biaya['biaya_overhead'] + $rencana_kerja_mei_biaya['biaya_bank'],0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_juni_biaya['biaya_overhead'] + $rencana_kerja_juni_biaya['biaya_bank'],0,',','.');?></th>
				<th align="right"><?php echo number_format($rencana_kerja_juli_biaya['biaya_overhead'] + $rencana_kerja_juli_biaya['biaya_bank'],0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_biaya_bank + $jumlah_bua,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_biaya_bank + $total_biaya_bua,0,',','.');?></th>
			</tr>
			<?php
			$jumlah_penerimaan = $termin_now['total'];
			$jumlah_lll_november = $rencana_kerja_november_biaya['biaya_overhead'] + $rencana_kerja_november_biaya['biaya_bank'];
			$jumlah_lll_desember = $rencana_kerja_desember_biaya['biaya_overhead'] + $rencana_kerja_desember_biaya['biaya_bank'];
			$jumlah_lll_januari = $rencana_kerja_januari_biaya['biaya_overhead'] + $rencana_kerja_januari_biaya['biaya_bank'];
			$jumlah_lll_februari = $rencana_kerja_februari_biaya['biaya_overhead'] + $rencana_kerja_februari_biaya['biaya_bank'];
			$jumlah_lll_maret = $rencana_kerja_maret_biaya['biaya_overhead'] + $rencana_kerja_maret_biaya['biaya_bank'];
			$jumlah_lll_april = $rencana_kerja_april_biaya['biaya_overhead'] + $rencana_kerja_april_biaya['biaya_bank'];
			$jumlah_lll_mei = $rencana_kerja_mei_biaya['biaya_overhead'] + $rencana_kerja_mei_biaya['biaya_bank'];
			$jumlah_lll_juni = $rencana_kerja_juni_biaya['biaya_overhead'] + $rencana_kerja_juni_biaya['biaya_bank'];
			$jumlah_lll_juli = $rencana_kerja_juli_biaya['biaya_overhead'] + $rencana_kerja_juli_biaya['biaya_bank'];

			$jumlah_penerimaan_total = $total_rap_nilai_2022 - $jumlah_termin;
			?>
			<tr class="table-total">
				<th align="left"><i>POSISI (II - III)</i></th>
				<th align="right"><?php echo number_format($total_rap_nilai_2022 / 10 + $total_rap_nilai_2022 - $total_biaya_rap_2022_biaya,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_penerimaan - ($diskonto_now + $overhead_now),0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_penerimaan - $jumlah_lll_november,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_penerimaan - $jumlah_lll_desember,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_penerimaan - $jumlah_lll_januari,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_penerimaan - $jumlah_lll_februari,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_penerimaan - $jumlah_lll_maret,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_penerimaan - $jumlah_lll_april,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_penerimaan - $jumlah_lll_mei,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_penerimaan - $jumlah_lll_juni,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_penerimaan - $jumlah_lll_juli,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_termin - ($jumlah_biaya_bank + $jumlah_bua),0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_penerimaan_total -  ($total_biaya_bank + $total_biaya_bua),0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="left" colspan="14"><u>PAJAK</u></th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;1. Pajak Keluaran</th>
				<th align="right"><?php echo number_format($total_rap_nilai_2022 / 10,0,',','.');?></th>
				<th align="right"><?php echo number_format($ppn_keluar_now['total'],0,',','.');?></th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right"><?php echo number_format($ppn_keluar_now['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format(($total_rap_nilai_2022 / 10) - $ppn_keluar_now['total'],0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;2. Pajak Masukan</th>
				<th align="right"><?php echo number_format($total_bahan_all_rap_2022 / 10,0,',','.');?></th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right"><?php echo number_format($ppn_masuk_now['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format(($total_bahan_all_rap_2022 / 10) - $ppn_masuk_now['total'],0,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="left"><i>JUMLAH V (1-2)</i></th>
				<th align="right"><?php echo number_format($total_rap_nilai_2022 / 10 - $total_bahan_all_rap_2022  / 10,0,',','.');?></th>
				<th align="right"><?php echo number_format($ppn_keluar_now['total'] - $ppn_masuk_now['total'],0,',','.');?></th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right">-</th>
				<th align="right"><?php echo number_format($ppn_keluar_now['total'] - $ppn_masuk_now['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format(($total_rap_nilai_2022 / 10 - $total_bahan_all_rap_2022  / 10) - ($ppn_keluar_now['total'] - $ppn_masuk_now['total']),0,',','.');?></th>
			</tr>
			<?php
			$posisi_iv = $jumlah_penerimaan - ($diskonto_now + $overhead_now);
			$posisi_iv_november = $jumlah_penerimaan - $jumlah_lll_november;
			$posisi_iv_desember = $jumlah_penerimaan - $jumlah_lll_desember;
			$posisi_iv_januari = $jumlah_penerimaan - $jumlah_lll_januari;
			$posisi_iv_februari = $jumlah_penerimaan - $jumlah_lll_februari;
			$posisi_iv_maret = $jumlah_penerimaan - $jumlah_lll_maret;
			$posisi_iv_april = $jumlah_penerimaan - $jumlah_lll_april;
			$posisi_iv_mei = $jumlah_penerimaan - $jumlah_lll_mei;
			$posisi_iv_juni = $jumlah_penerimaan - $jumlah_lll_juni;
			$posisi_iv_juli = $jumlah_penerimaan - $jumlah_lll_juli;

			$posisi_lll = $jumlah_termin - ($jumlah_biaya_bank + $jumlah_bua);
			$posisi_ll_sisa = $jumlah_penerimaan_total - ($total_biaya_bank + $total_biaya_bua);
			$posisi_lll_sisa = ($total_rap_nilai_2022 / 10 - $total_bahan_all_rap_2022  / 10) - ($ppn_keluar_now['total'] - $ppn_masuk_now['total']);
			?>
			<tr class="table-total">
				<th align="left"><i>POSISI (IV+V)</i></th>
				<th align="right"><?php echo number_format(($total_rap_nilai_2022 / 10 + $total_rap_nilai_2022 - $total_biaya_rap_2022_biaya) - ($total_rap_nilai_2022 / 10 - $total_bahan_all_rap_2022  / 10),0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_iv - ($ppn_keluar_now['total'] - $ppn_masuk_now['total']),0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_iv_november,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_iv_desember,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_iv_januari,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_iv_februari,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_iv_maret,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_iv_april,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_iv_mei,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_iv_juni,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_iv_juli,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_lll - $ppn_keluar_now['total'] - $ppn_masuk_now['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_ll_sisa - $posisi_lll_sisa,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="left" colspan="14"><u>PINJAMAN</u></th>
			</tr>
			<?php
            $total_penerimaan_penjualan = $penerimaan_penjualan_now['total'] + $penerimaan_penjualan_november['total'] + $penerimaan_penjualan_desember['total'] + $penerimaan_penjualan_januari['total'] + $penerimaan_penjualan_februari['total'] + $penerimaan_penjualan_maret['total'] + $penerimaan_penjualan_april['total'] + $penerimaan_penjualan_mei['total'] + $penerimaan_penjualan_juni['total'] + $penerimaan_penjualan_juli['total'];
            $sisa_penerimaan_penjualan = $penerimaan_penjualan_now['total'] - $total_penerimaan_penjualan;
            ?>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;Penerimaan Pinjaman</th>
				<th align="right"><?php echo number_format($penerimaan_penjualan_now['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($penerimaan_penjualan_now['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($penerimaan_penjualan_november['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($penerimaan_penjualan_desember['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($penerimaan_penjualan_januari['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($penerimaan_penjualan_februari['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($penerimaan_penjualan_maret['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($penerimaan_penjualan_april['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($penerimaan_penjualan_mei['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($penerimaan_penjualan_juni['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($penerimaan_penjualan_juli['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($total_penerimaan_penjualan,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_penerimaan_penjualan,0,',','.');?></th>
			</tr>
			<?php
            $total_pengembalian_penjualan = $pengembalian_penjualan_now['total'] + $pengembalian_penjualan_november['total'] + $pengembalian_penjualan_desember['total'] + $pengembalian_penjualan_januari['total'] + $pengembalian_penjualan_februari['total'] + $pengembalian_penjualan_maret['total'] + $pengembalian_penjualan_april['total'] + $pengembalian_penjualan_mei['total'] + $pengembalian_penjualan_juni['total'] + $pengembalian_penjualan_juli['total'];
            $sisa_pengembalian_penjualan = $pengembalian_penjualan_now['total'] - $total_pengembalian_penjualan;
            ?>
			<tr class="table-baris1">
				<th align="left">&nbsp;&nbsp;Pengembalian Pinjaman</th>
				<th align="right"><?php echo number_format($pengembalian_penjualan_now['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pengembalian_penjualan_now['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pengembalian_penjualan_november['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pengembalian_penjualan_desember['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pengembalian_penjualan_januari['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pengembalian_penjualan_februari['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pengembalian_penjualan_maret['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pengembalian_penjualan_april['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pengembalian_penjualan_mei['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pengembalian_penjualan_juni['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pengembalian_penjualan_juli['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($total_pengembalian_penjualan,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_pengembalian_penjualan,0,',','.');?></th>
			</tr>
			<?php
			$jumlah_vii_rap = $penerimaan_penjualan_now['total'] + $pengembalian_penjualan_now['total'];
			$jumlah_vii_now = $penerimaan_penjualan_now['total'] + $pengembalian_penjualan_now['total'];
			$jumlah_vii_november = $penerimaan_penjualan_november['total'] + $pengembalian_penjualan_november['total'];
			$jumlah_vii_desember = $penerimaan_penjualan_desember['total'] + $pengembalian_penjualan_desember['total'];
			$jumlah_vii_januari = $penerimaan_penjualan_januari['total'] + $pengembalian_penjualan_januari['total'];
			$jumlah_vii_februari = $penerimaan_penjualan_februari['total'] + $pengembalian_penjualan_februari['total'];
			$jumlah_vii_maret = $penerimaan_penjualan_maret['total'] + $pengembalian_penjualan_maret['total'];
			$jumlah_vii_april = $penerimaan_penjualan_april['total'] + $pengembalian_penjualan_april['total'];
			$jumlah_vii_mei = $penerimaan_penjualan_mei['total'] + $pengembalian_penjualan_mei['total'];
			$jumlah_vii_juni = $penerimaan_penjualan_juni['total'] + $pengembalian_penjualan_juni['total'];
			$jumlah_vii_juli = $penerimaan_penjualan_juli['total'] + $pengembalian_penjualan_juli['total'];
			$total_jumlah_vii = $total_penerimaan_penjualan + $total_pengembalian_penjualan;
			$sisa_jumlah_vii = $sisa_penerimaan_penjualan + $sisa_pengembalian_penjualan; 
			?>
			<tr class="table-total">
				<th align="left"><i>JUMLAH VII</i></th>
				<th align="right"><?php echo number_format($jumlah_vii_rap,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vii_now,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vii_november,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vii_desember,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vii_januari,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vii_februari,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vii_maret,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vii_april,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vii_mei,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vii_juni,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vii_juli,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_jumlah_vii,0,',','.');?></th>
				<th align="right"><?php echo number_format($sisa_jumlah_vii,0,',','.');?></th>
			</tr>
			<?php
			$posisi_vi_rap = (($total_rap_nilai_2022 / 10 + $total_rap_nilai_2022 - $total_biaya_rap_2022_biaya) - ($total_rap_nilai_2022 / 10 - $total_bahan_all_rap_2022  / 10)) + $jumlah_vii_now;
			$posisi_vi_now = ($posisi_iv - ($ppn_keluar_now['total'] - $ppn_masuk_now['total'])) + $jumlah_vii_now;
			$posisi_vi_november = $posisi_iv_november + $jumlah_vii_november;
			$posisi_vi_desember = $posisi_iv_desember + $jumlah_vii_desember;
			$posisi_vi_januari = $posisi_iv_januari + $jumlah_vii_januari;
			$posisi_vi_februari = $posisi_iv_februari + $jumlah_vii_februari;
			$posisi_vi_maret = $posisi_iv_maret + $jumlah_vii_maret;
			$posisi_vi_april = $posisi_iv_april + $jumlah_vii_april;
			$posisi_vi_mei = $posisi_iv_mei + $jumlah_vii_mei;
			$posisi_vi_juni = $posisi_iv_juni + $jumlah_vii_juni;
			$posisi_vi_juli = $posisi_iv_juli + $jumlah_vii_juli;
			$posisi_vi_total = ($posisi_lll - $ppn_keluar_now['total'] - $ppn_masuk_now['total']) + $total_jumlah_vii;
			$posisi_vi_sisa = ($posisi_ll_sisa - $posisi_lll_sisa) + $sisa_jumlah_vii;
			?>
			<tr class="table-total">
				<th align="left"><i>POSISI (VI+VII)</i></th>
				<th align="right"><?php echo number_format($posisi_vi_rap,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_now,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_november,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_desember,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_januari,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_februari,0,',','.');?></th>
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
				<th align="right"><?php echo number_format($posisi_vi_rap,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_now,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_november,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_desember,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_januari,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_februari,0,',','.');?></th>
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
				<th align="right"><?php echo number_format($posisi_vi_rap,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_now,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_november,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_desember,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_januari,0,',','.');?></th>
				<th align="right"><?php echo number_format($posisi_vi_februari,0,',','.');?></th>
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