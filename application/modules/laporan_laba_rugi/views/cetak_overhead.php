<!DOCTYPE html>
<html>
	<head>
	  <title>OVERHEAD</title>
	  
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
					<div style="display: block;font-weight: bold;font-size: 12px;">OVERHEAD</div>
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
			
			<?php
		
			//ALAT
			$nilai_alat = $this->db->select('p.nama_produk, SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("prm.material_id in (12,13,14,15,16,23,24)")
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
			$overhead_biaya = $this->db->select('pb.tanggal_transaksi, pb.nomor_transaksi, c.coa, sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',15)
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->group_by('pdb.id')
			->order_by('pb.tanggal_transaksi','asc')
			->order_by('pb.created_on','asc')
			->get()->result_array();

			$total_overhead_biaya = 0;

			foreach ($overhead_biaya as $row){
				$total_overhead_biaya += $row['total'];
			}

			$total_overhead_biaya_all = $total_overhead_biaya;

			$overhead_jurnal = $this->db->select('pb.tanggal_transaksi, pb.nomor_transaksi, c.coa, sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (199)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->group_by('pdb.id')
			->order_by('pb.tanggal_transaksi','asc')
			->order_by('pb.created_on','asc')
			->get()->result_array();

			$total_overhead_jurnal = 0;

			foreach ($overhead_jurnal as $row2){
				$total_overhead_jurnal += $row2['total'];
			}

			$total_overhead_jurnal_all = $total_overhead_jurnal;
			//END OVERHEAD

			$total_all = $total_overhead_biaya_all + $total_overhead_jurnal_all;

			?>
			
			<tr class="table-judul">
				<th width="5%" align="center">NO.</th>
				<th width="15%" align="center">TRANSAKSI</th>
				<th width="15%" align="center">TGL. TRANSAKSI</th>
				<th width="20%" align="center">NO. TRANSAKSI</th>
				<th width="25%" align="center">URAIAN</th>
				<th width="20%" align="center">NILAI</th>
	        </tr>
			<?php foreach ($overhead_biaya as $key => $row): ?>
			<tr class="table-baris1">
				<th align="center"><?php echo $key + 1;?></th>
				<th align="center">BIAYA</th>
				<th align="center"><?= $row['tanggal_transaksi'] ?></th>
				<th><?= $row['nomor_transaksi'] ?></th>
				<th><?= $row['coa'] ?></th>
				<th align="right"><?= number_format($row['total'],0,',','.') ?></th>
	        </tr>
			<?php endforeach; ?>
			<tr class="table-total">
				<th align="right" colspan="5">TOTAL BIAYA</th>
				<th align="right"><?php echo number_format($total_overhead_biaya_all,0,',','.');?></th>
	        </tr>
			<?php foreach ($overhead_jurnal as $key => $row): ?>
			<tr class="table-baris1">
				<th align="center"><?php echo $key + 1;?></th>
				<th align="center">JURNAL UMUM</th>
				<th align="center"><?= $row2['tanggal_transaksi'] ?></th>
				<th><?= $row2['nomor_transaksi'] ?></th>
				<th><?= $row2['coa'] ?></th>
				<th align="right"><?= number_format($row2['total'],0,',','.') ?></th>
	        </tr>
			<?php endforeach; ?>
			<tr class="table-total">
				<th align="right" colspan="5">TOTAL JURNAL UMUM</th>
				<th align="right"><?php echo number_format($total_overhead_jurnal_all,0,',','.');?></th>
	        </tr>
			<tr class="table-total">
				<th align="right" colspan="5">TOTAL</th>
				<th align="right"><?php echo number_format($total_all,0,',','.');?></th>
	        </tr>
			
			
	    </table>
		
	</body>
</html>