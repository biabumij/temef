<!DOCTYPE html>
<html>
	<head>
	  <title>LAPORAN EVALUASI BUA</title>
	  
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
					<div style="display: block;font-weight: bold;font-size: 12px;">LAPORAN EVALUASI BUA</div>
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
			<?php
			//PENJUALAN
			$penjualan = $this->db->select('p.nama, pp.client_id, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume, pp.convert_measure as measure')
			->from('pmm_productions pp')
			->join('penerima p', 'pp.client_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("pp.date_production between '$date1' and '$date2'")
			->where("pp.status = 'PUBLISH'")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.client_id")
			->get()->result_array();
			
			$total_volume = 0;
			foreach ($penjualan as $x){
				$total_volume += $x['volume'];
			}
			?>


			<!--RAP BUA -->
			<?php


			$rap_gaji_upah = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa in ('199','200')")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_konsumsi = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 201")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_biaya_sewa_mess = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 215")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_listrik_internet = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 206")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_pengujian_material_laboratorium = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 216")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_keamanan_kebersihan = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 151")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_pengobatan = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 121")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_donasi = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 127")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_bensin_tol_parkir = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 129")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_perjalanan_dinas_penjualan = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 113")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_pakaian_dinas = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 138")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_alat_tulis_kantor = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 149")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_perlengkapan_kantor = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 153")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_beban_kirim = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 145")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_beban_lain_lain = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 146")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_biaya_sewa_kendaraan = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 157")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_thr_bonus = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 202")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_biaya_admin_bank = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 143")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			?>


			<!--PEMAKAIAN BUA -->
			<?php

			$gaji_upah_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in ('199','200')")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$gaji_upah_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in ('199','200')")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$gaji_upah = $gaji_upah_biaya['total'] + $gaji_upah_jurnal['total'];

			$konsumsi_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 201")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$konsumsi_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 201")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$konsumsi = $konsumsi_biaya['total'] + $konsumsi_jurnal['total'];

			$biaya_sewa_mess_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 215")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$biaya_sewa_mess_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 215")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$biaya_sewa_mess = $biaya_sewa_mess_biaya['total'] + $biaya_sewa_mess_jurnal['total'];

			$listrik_internet_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 206")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$listrik_internet_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 206")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$listrik_internet = $listrik_internet_biaya['total'] + $listrik_internet_jurnal['total'];

			$pengujian_material_laboratorium_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 216")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$pengujian_material_laboratorium_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 216")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$pengujian_material_laboratorium = $pengujian_material_laboratorium_biaya['total'] + $pengujian_material_laboratorium_jurnal['total'];

			$keamanan_kebersihan_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 151")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$keamanan_kebersihan_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 151")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$keamanan_kebersihan = $keamanan_kebersihan_biaya['total'] + $keamanan_kebersihan_jurnal['total'];

			$pengobatan_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 121")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$pengobatan_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 121")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$pengobatan = $pengobatan_biaya['total'] + $pengobatan_jurnal['total'];

			$donasi_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 127")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$donasi_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 127")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$donasi = $donasi_biaya['total'] + $donasi_jurnal['total'];

			$bensin_tol_parkir_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 129")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$bensin_tol_parkir_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 129")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$bensin_tol_parkir = $bensin_tol_parkir_biaya['total'] + $bensin_tol_parkir_jurnal['total'];

			$perjalanan_dinas_penjualan_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 113")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$perjalanan_dinas_penjualan_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 113")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$perjalanan_dinas_penjualan = $perjalanan_dinas_penjualan_biaya['total'] + $perjalanan_dinas_penjualan_jurnal['total'];

			$pakaian_dinas_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 138")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$pakaian_dinas_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 138")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$pakaian_dinas = $pakaian_dinas_biaya['total'] + $pakaian_dinas_jurnal['total'];

			$alat_tulis_kantor_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 149")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$alat_tulis_kantor_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 149")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$alat_tulis_kantor = $alat_tulis_kantor_biaya['total'] + $alat_tulis_kantor_jurnal['total'];

			$alat_tulis_kantor_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 149")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$alat_tulis_kantor_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 149")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$alat_tulis_kantor = $alat_tulis_kantor_biaya['total'] + $alat_tulis_kantor_jurnal['total'];

			$perlengkapan_kantor_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 153")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$perlengkapan_kantor_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 153")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$perlengkapan_kantor = $perlengkapan_kantor_biaya['total'] + $perlengkapan_kantor_jurnal['total'];


			$beban_kirim_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 145")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$beban_kirim_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 145")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$beban_kirim = $beban_kirim_biaya['total'] + $beban_kirim_jurnal['total'];

			$beban_lain_lain_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 146")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$beban_lain_lain_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 146")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$beban_lain_lain = $beban_lain_lain_biaya['total'] + $beban_lain_lain_jurnal['total'];

			$biaya_sewa_kendaraan_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 157")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$biaya_sewa_kendaraan_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 157")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$biaya_sewa_kendaraan = $biaya_sewa_kendaraan_biaya['total'] + $biaya_sewa_kendaraan_jurnal['total'];

			$thr_bonus_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 202")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$thr_bonus_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 202")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$thr_bonus = $thr_bonus_biaya['total'] + $thr_bonus_jurnal['total'];

			$biaya_admin_bank_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 143")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$biaya_admin_bank_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 143")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$biaya_admin_bank = $biaya_admin_bank_biaya['total'] + $biaya_admin_bank_jurnal['total'];
			?>

			<?php
			
			$evaluasi_gaji_upah = $rap_gaji_upah['total'] - $gaji_upah;
			$evaluasi_konsumsi = $rap_konsumsi['total'] - $konsumsi;
			$evaluasi_biaya_sewa_mess = $rap_biaya_sewa_mess['total'] - $biaya_sewa_mess;
			$evaluasi_listrik_internet = $rap_listrik_internet['total'] - $listrik_internet;
			$evaluasi_pengujian_material_laboratorium = $rap_pengujian_material_laboratorium['total'] - $pengujian_material_laboratorium;
			$evaluasi_keamanan_kebersihan = $rap_keamanan_kebersihan['total'] - $keamanan_kebersihan;
			$evaluasi_pengobatan = $rap_pengobatan['total'] - $pengobatan;
			$evaluasi_donasi = $rap_donasi['total'] - $donasi;
			$evaluasi_bensin_tol_parkir = $rap_bensin_tol_parkir['total'] - $bensin_tol_parkir;
			$evaluasi_perjalanan_dinas_penjualan = $rap_perjalanan_dinas_penjualan['total'] - $perjalanan_dinas_penjualan;
			$evaluasi_pakaian_dinas = $rap_pakaian_dinas['total'] - $pakaian_dinas;
			$evaluasi_alat_tulis_kantor = $rap_alat_tulis_kantor['total'] -	$alat_tulis_kantor;
			$evaluasi_perlengkapan_kantor = $rap_perlengkapan_kantor['total'] - $perlengkapan_kantor;
			$evaluasi_beban_kirim = $rap_beban_kirim['total'] - $beban_kirim;
			$evaluasi_beban_lain_lain = $rap_beban_lain_lain['total'] - 	$beban_lain_lain;
			$evaluasi_biaya_sewa_kendaraan = $rap_biaya_sewa_kendaraan['total'] - $biaya_sewa_kendaraan;
			$evaluasi_thr_bonus = $rap_thr_bonus['total'] - $thr_bonus;
			$evaluasi_biaya_admin_bank = $rap_biaya_admin_bank['total'] - $biaya_admin_bank;


			$total_rap = $rap_gaji_upah['total'] + $rap_konsumsi['total'] + $rap_biaya_sewa_mess['total'] + $rap_listrik_internet['total'] + $rap_pengujian_material_laboratorium['total'] + $rap_keamanan_kebersihan['total'] + $rap_pengobatan['total'] + $rap_donasi['total'] + $rap_bensin_tol_parkir['total'] + $rap_perjalanan_dinas_penjualan['total'] + $rap_pakaian_dinas['total'] + $rap_alat_tulis_kantor['total'] + $rap_perlengkapan_kantor['total'] + $rap_beban_kirim['total'] + $rap_beban_lain_lain['total'] + $rap_biaya_sewa_kendaraan['total'] + $rap_thr_bonus['total'] + $rap_biaya_admin_bank['total'];
			$total_realisasi = $gaji_upah + $konsumsi + $biaya_sewa_mess + $listrik_internet + $pengujian_material_laboratorium + $keamanan_kebersihan + $pengobatan + $donasi + $bensin_tol_parkir + $perjalanan_dinas_penjualan + $pakaian_dinas + $alat_tulis_kantor + $perlengkapan_kantor + $beban_kirim + $beban_lain_lain + $biaya_sewa_kendaraan + $thr_bonus + $biaya_admin_bank;
			$total_evaluasi = $total_rap - $total_realisasi;

			$volume_rap = $this->db->select('rbd.qty as volume')
			->from('rap_bua rap')
			->from('rap_bua_detail rbd', 'rap.id = rbd.rap_bua_id','left')
			->where("rap.tanggal_rap_bua < '$date2'")
			->get()->row_array();
		
			$volume_rap = round($volume_rap['volume'],2);

			$volume_realisasi = $this->db->select('SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("pp.date_production between '$date1' and '$date2'")
			->where("pp.status = 'PUBLISH'")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.client_id")
			->get()->row_array();

			$volume_realisasi = round($volume_realisasi['volume'],2);

			$volume_evaluasi = $total_evaluasi / 15;

			$total_eveluasi_rap = ($volume_rap!=0)?$total_rap / $volume_rap * 1:0;
			$total_eveluasi_realisasi = ($volume_realisasi!=0)?$total_realisasi / $volume_realisasi * 1:0;
			$total_eveluasi_all = $total_eveluasi_rap - $total_eveluasi_realisasi;

			$evaluasi_1 = $total_eveluasi_rap * $volume_realisasi;
			$evaluasi_2 = $total_realisasi - $evaluasi_1;
			?>
			
			<tr class="table-judul">
				<th width="5%" align="center">NO.</th>
				<th width="35%" align="center">URAIAN</th>
				<th width="20%" align="center">RAP</th>
				<th width="20%" align="center">REALISASI</th>
				<th width="20%" align="center">SISA ANGGARAN</th>
	        </tr>
			<?php
				$styleColorA = $evaluasi_gaji_upah < 0 ? 'color:red' : 'color:black';
				$styleColorB = $evaluasi_konsumsi < 0 ? 'color:red' : 'color:black';
				$styleColorC = $evaluasi_biaya_sewa_mess < 0 ? 'color:red' : 'color:black';
				$styleColorD = $evaluasi_listrik_internet < 0 ? 'color:red' : 'color:black';
				$styleColorE = $evaluasi_pengujian_material_laboratorium < 0 ? 'color:red' : 'color:black';
				$styleColorF = $evaluasi_keamanan_kebersihan < 0 ? 'color:red' : 'color:black';
				$styleColorG = $evaluasi_pengobatan < 0 ? 'color:red' : 'color:black';
				$styleColorH = $evaluasi_donasi < 0 ? 'color:red' : 'color:black';
				$styleColorI = $evaluasi_bensin_tol_parkir < 0 ? 'color:red' : 'color:black';
				$styleColorJ = $evaluasi_perjalanan_dinas_penjualan < 0 ? 'color:red' : 'color:black';
				$styleColorK = $evaluasi_pakaian_dinas < 0 ? 'color:red' : 'color:black';
				$styleColorL = $evaluasi_alat_tulis_kantor < 0 ? 'color:red' : 'color:black';
				$styleColorM = $evaluasi_perlengkapan_kantor < 0 ? 'color:red' : 'color:black';
				$styleColorN = $evaluasi_beban_kirim < 0 ? 'color:red' : 'color:black';
				$styleColorO = $evaluasi_beban_lain_lain < 0 ? 'color:red' : 'color:black';
				$styleColorP = $evaluasi_biaya_sewa_kendaraan < 0 ? 'color:red' : 'color:black';
				$styleColorQ = $evaluasi_thr_bonus < 0 ? 'color:red' : 'color:black';
				$styleColorR = $evaluasi_biaya_admin_bank < 0 ? 'color:red' : 'color:black';
				$styleColorS = $total_evaluasi < 0 ? 'color:red' : 'color:black';
				$styleColorT = $total_eveluasi_all < 0 ? 'color:red' : 'color:black';
			?>
			<tr class="table-baris1">
				<th align="center">1</th>			
				<th align="left">Gaji / Upah</th>
				<th align="right"><?php echo number_format($rap_gaji_upah['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($gaji_upah,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorA ?>"><?php echo number_format($evaluasi_gaji_upah,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">2</th>			
				<th align="left">Konsumsi</th>
				<th align="right"><?php echo number_format($rap_konsumsi['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($konsumsi,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorB ?>"><?php echo number_format($evaluasi_konsumsi,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">3</th>			
				<th align="left">Biaya Sewa - Mess</th>
				<th align="right"><?php echo number_format($rap_biaya_sewa_mess['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($biaya_sewa_mess,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorC ?>"><?php echo number_format($evaluasi_biaya_sewa_mess,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">4</th>			
				<th align="left">Listrik & Internet</th>
				<th align="right"><?php echo number_format($rap_listrik_internet['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($listrik_internet,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorD ?>"><?php echo number_format($evaluasi_listrik_internet,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">5</th>			
				<th align="left">Pengujian Material & Laboratorium</th>
				<th align="right"><?php echo number_format($rap_pengujian_material_laboratorium['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pengujian_material_laboratorium,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorE ?>"><?php echo number_format($evaluasi_pengujian_material_laboratorium,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">6</th>			
				<th align="left">Keamanan & Kebersihan</th>
				<th align="right"><?php echo number_format($rap_keamanan_kebersihan['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($keamanan_kebersihan,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorF ?>"><?php echo number_format($evaluasi_keamanan_kebersihan,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">7</th>			
				<th align="left">Pengobatan</th>
				<th align="right"><?php echo number_format($rap_pengobatan['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pengobatan,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorG ?>"><?php echo number_format($evaluasi_pengobatan,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">8</th>			
				<th align="left">Donasi</th>
				<th align="right"><?php echo number_format($rap_donasi['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($donasi,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorH ?>"><?php echo number_format($evaluasi_donasi,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">9</th>			
				<th align="left">Bensin, Tol dan Parkir - Umum</th>
				<th align="right"><?php echo number_format($rap_bensin_tol_parkir['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($bensin_tol_parkir,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorI ?>"><?php echo number_format($evaluasi_bensin_tol_parkir,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">10</th>			
				<th align="left">Perjalanan Dinas - Penjualan</th>
				<th align="right"><?php echo number_format($rap_perjalanan_dinas_penjualan['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($perjalanan_dinas_penjualan,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorJ ?>"><?php echo number_format($evaluasi_perjalanan_dinas_penjualan,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">11</th>			
				<th align="left">Pakaian Dinas & K3</th>
				<th align="right"><?php echo number_format($rap_pakaian_dinas['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pakaian_dinas,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorK ?>"><?php echo number_format($evaluasi_pakaian_dinas,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">12</th>			
				<th align="left">Alat Tulis Kantor & Printing</th>
				<th align="right"><?php echo number_format($rap_alat_tulis_kantor['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($alat_tulis_kantor,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorL ?>"><?php echo number_format($evaluasi_alat_tulis_kantor,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">13</th>			
				<th align="left">Perlengkapan Kantor</th>
				<th align="right"><?php echo number_format($rap_perlengkapan_kantor['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($perlengkapan_kantor,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorM ?>"><?php echo number_format($evaluasi_perlengkapan_kantor,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">14</th>			
				<th align="left">Beban Kirim</th>
				<th align="right"><?php echo number_format($rap_beban_kirim['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($beban_kirim,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorN ?>"><?php echo number_format($evaluasi_beban_kirim,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">15</th>			
				<th align="left">Beban Lain-Lain</th>
				<th align="right"><?php echo number_format($rap_beban_lain_lain['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($beban_lain_lain,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorO ?>"><?php echo number_format($evaluasi_beban_lain_lain,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">16</th>			
				<th align="left">Biaya Sewa - Kendaraan</th>
				<th align="right"><?php echo number_format($rap_biaya_sewa_kendaraan['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($biaya_sewa_kendaraan,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorP ?>"><?php echo number_format($evaluasi_biaya_sewa_kendaraan,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">17</th>			
				<th align="left">THR & Bonus</th>
				<th align="right"><?php echo number_format($rap_thr_bonus['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($thr_bonus,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorQ ?>"><?php echo number_format($evaluasi_thr_bonus,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">18</th>			
				<th align="left">Biaya Admin Bank</th>
				<th align="right"><?php echo number_format($rap_biaya_admin_bank['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($biaya_admin_bank,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorR ?>"><?php echo number_format($evaluasi_biaya_admin_bank,0,',','.');?></th>
	        </tr>
			<tr class="table-total">
				<th align="center" colspan="2">TOTAL</th>
				<th align="right"><?php echo number_format($total_rap,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_realisasi,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorS ?>"><?php echo number_format($total_evaluasi,0,',','.');?></th>
	        </tr>
			<tr class="table-total">
				<th align="center" colspan="2">VOLUME (M3)</th>
				<th align="right"><?php echo number_format($volume_rap,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_realisasi,2,',','.');?></th>
				<th align="right"><?php echo number_format($volume_evaluasi,0,',','.');?></th>
	        </tr>
			<tr class="table-total">
				<th align="center" colspan="2">EVALUASI</th>
				<th align="right"><?php echo number_format($total_eveluasi_rap,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_eveluasi_realisasi,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorT ?>"><?php echo number_format($total_eveluasi_all,0,',','.');?></th>
	        </tr>
			<tr class="table-total">
				<th align="center" colspan="2"></th>
				<th align="right"><?php echo number_format($evaluasi_1,0,',','.');?></th>
				<th align="right"><?php echo number_format($evaluasi_2,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			
	    </table>
		<br />
		<br />
		<table width="98%" border="0" cellpadding="15">
			<tr >
				<td width="5%"></td>
				<td width="90%">
					<table width="100%" border="0" cellpadding="2">
						<tr>
							<td align="center" >
								Disetujui Oleh
							</td>
							<td align="center" >
								Diperiksa Oleh
							</td>
							<td align="center" >
								Dibuat Oleh
							</td>
						</tr>
						<tr>
							<td align="center" height="40px">
								
							</td>
							<td align="center">
								
							</td>
							<td align="center">
								
							</td>
						</tr>
						<tr>
							<td align="center" >
								<b><u>Gervasius K. Limahekin</u><br />
								Ka. Plant</b>
							</td>
							<td align="center" >
								<b><u>Erika Sinaga</u><br />
								M. Keu & SDM</b>
							</td>
							<td align="center" >
								<b><u>Debi Khania</u><br />
								Kasir</b>
							</td>
						</tr>
					</table>
				</td>
				<td width="5%"></td>
			</tr>
		</table>
		
	</body>
</html>