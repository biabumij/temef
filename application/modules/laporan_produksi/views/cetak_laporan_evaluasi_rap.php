<!DOCTYPE html>
<html>
	<head>
	  <title>LAPORAN EVALUASI RAP</title>
	  
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
					<div style="display: block;font-weight: bold;font-size: 12px;">LAPORAN EVALUASI RAP</div>
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
		
		<table width="98%" border="0" cellpadding="3" border="0">
		
			<!-- RAP -->

			<?php

			$rap = $this->db->select('rap.id, rap.tanggal_rap, rap.nomor_rap, SUM(rap.total_bahan) as total_bahan, SUM(rap.total_alat) as total_alat, SUM(rap.total_overhead) as total_overhead, SUM(rap.total_biaya_admin) as total_biaya_admin, SUM(rap.total_diskonto) as total_diskonto, rap.status')
			->from('rap rap')
			->where('status','PUBLISH')
			->where("rap.tanggal_rap between '$date1' and '$date2'")
			->where('rap.status','PUBLISH')
			->get()->row_array();

			$total_bahan = $rap['total_bahan'];
			$total_alat = $rap['total_alat'];
			$total_overhead = $rap['total_overhead'];
			$total_biaya_admin = $rap['total_biaya_admin'];
			$total_diskonto = $rap['total_diskonto'];

			?>

			<!-- END RAP -->

			<!-- REALISASI -->

			<?php

			$penjualan = $this->db->select('p.nama, pp.client_id, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume, pp.convert_measure as measure')
			->from('pmm_productions pp')
			->join('penerima p', 'pp.client_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("pp.date_production between '$date1' and '$date2'")
			->where("pp.status = 'PUBLISH'")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.client_id")
			->get()->result_array();

			$total_penjualan = 0;
			$total_volume = 0;
			$measure = 0;

			foreach ($penjualan as $x){
				$total_penjualan += $x['price'];
				$total_volume += $x['volume'];
			}

			$total_penjualan_all = 0;
			$total_penjualan_all = $total_penjualan;

			//BAHAN		
			$akumulasi = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar as total_nilai_keluar')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi between '$date1' and '$date2')")
			->get()->result_array();

			$total_akumulasi = 0;

			foreach ($akumulasi as $a){
				$total_akumulasi += $a['total_nilai_keluar'];
			}

			$total_nilai = $total_akumulasi;
			//END BAHAN

			//ALAT
			$nilai_alat = $this->db->select('SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("prm.material_id in (12,13,14,15,16)")
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

			$alat = $nilai_alat['nilai'] + $total_akumulasi_bbm + $total_insentif_tm;
			//END ALAT

			//OVERHEAD
			$overhead = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',15)
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$overhead_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->where("pdb.akun in (199)")
			->where("status = 'PAID'")
			->where("(tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$overhead = $overhead['total'] + $overhead_jurnal['total'];
			//END OVERHEAD

			//DISKONTO
			$diskonto = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 168")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$diskonto = $diskonto['total'];

			$bahan = $total_nilai;
			$alat = $alat;
			$overhead = $overhead;
			$biaya_admin = 0;
			$diskonto = $diskonto;
			//END DISKONTO

			?>
			
			<!-- END REALISASI -->

			<!-- EVALUASI -->

			<?php

			$evaluasi_total_bahan = $total_bahan - $bahan;
			$evaluasi_total_alat = $total_alat - $alat;
			$evaluasi_total_overhead = $total_overhead - $overhead;
			$evaluasi_total_biaya_admin = $total_biaya_admin - $biaya_admin;
			$evaluasi_total_diskonto = $total_diskonto - $diskonto;

			//TOTAL
			$total_rap = $total_bahan + $total_alat + $total_overhead + $total_biaya_admin + $total_diskonto;
			$total_realisasi = $bahan + $alat + $overhead + $biaya_admin + $diskonto;
			$total_evaluasi = $evaluasi_total_bahan + $evaluasi_total_alat + $evaluasi_total_overhead + $evaluasi_total_biaya_admin + $evaluasi_total_diskonto;
			?>


			<!-- END EVALUASI -->
			
			<tr class="table-judul">
				<th width="5%" align="center" >NO.</th>
				<th width="35%" align="center" >URAIAN</th>
				<th width="20%" align="center" >RAP</th>
				<th width="20%" align="center" >REALISASI</th>
				<th width="20%" align="center" >EVALUASI</th>
	        </tr>
			<?php
				$styleColorA = $evaluasi_total_bahan < 0 ? 'color:red' : 'color:black';
				$styleColorB = $evaluasi_total_alat < 0 ? 'color:red' : 'color:black';
				$styleColorC = $evaluasi_total_overhead < 0 ? 'color:red' : 'color:black';
				$styleColorD = $evaluasi_total_biaya_admin < 0 ? 'color:red' : 'color:black';
				$styleColorE = $evaluasi_total_diskonto < 0 ? 'color:red' : 'color:black';
				$styleColorF = $total_evaluasi < 0 ? 'color:red' : 'color:black';
			?>
			<tr class="table-baris1">
				<th align="center" >1.</th>			
				<th align="left" >TOTAL BAHAN</th>
				<th align="right" ><?php echo number_format($total_bahan,0,',','.');?></th>
				<th align="right" ><?php echo number_format($bahan,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorA ?>"><?php echo number_format($evaluasi_total_bahan,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center" >2.</th>			
				<th align="left" >TOTAL ALAT</th>
				<th align="right" ><?php echo number_format($total_alat,0,',','.');?></th>
				<th align="right" ><?php echo number_format($alat,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorB ?>"><?php echo number_format($evaluasi_total_alat,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center" >3.</th>			
				<th align="left" >TOTAL OVERHEAD</th>
				<th align="right" ><?php echo number_format($total_overhead,0,',','.');?></th>
				<th align="right" ><?php echo number_format($overhead,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorC ?>"><?php echo number_format($evaluasi_total_overhead,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center" >4.</th>			
				<th align="left" >TOTAL BIAYA ADMIN</th>
				<th align="right" ><?php echo number_format($total_biaya_admin,0,',','.');?></th>
				<th align="right" ><?php echo number_format($biaya_admin,0,',','.');?></th>
				<th align="right"  style="<?php echo $styleColorD ?>"><?php echo number_format($evaluasi_total_biaya_admin,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center" >5.</th>			
				<th align="left" >TOTAL DISKONTO</th>
				<th align="right" ><?php echo number_format($total_diskonto,0,',','.');?></th>
				<th align="right" ><?php echo number_format($diskonto,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorE ?>"><?php echo number_format($evaluasi_total_diskonto,0,',','.');?></th>
	        </tr>
			<tr class="table-total">		
				<th align="right"  colspan="2">TOTAL</th>
				<th align="right" ><?php echo number_format($total_rap,0,',','.');?></th>
				<th align="right" ><?php echo number_format($total_realisasi,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorF ?>"><?php echo number_format($total_evaluasi,0,',','.');?></th>
	        </tr>
	    </table>
		<br />
		<br />
		<br />
		<table width="98%" border="0" cellpadding="0">
			<tr >
				<td width="10%"></td>
				<td width="80%">
					<table width="100%" border="0" cellpadding="2">
						<tr>
							<td align="center" colspan="2">
								Disetujui Oleh
							</td>
							<td align="center">
								Diperiksa Oleh
							</td>
							<td align="center">
								Dibuat Oleh
							</td>	
						</tr>
						<tr class="">
							<td align="center" height="55px">
								
							</td>
							<td align="center">
								
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
								<b><br />
								Ass. Ka. Plant</b>
							</td>
							<td align="center">
								<b><br />
								M. Teknik</b>
							</td>
							<td align="center">
								<b><u>Agustinus Pakaenoni</u><br />
								Pj. Logistik</b>
							</td>
						</tr>
					</table>
				</td>
				<td width="10%"></td>
			</tr>
		</table>
		
	</body>
</html>