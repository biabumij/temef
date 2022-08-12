<!DOCTYPE html>
<html>
	<head>
	  <title>DISKONTO</title>
	  
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
					<div style="display: block;font-weight: bold;font-size: 12px;">DISKONTO</div>
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
		
			//DISKONTO
			$diskonto = $this->db->select('pb.tanggal_transaksi, pb.nomor_transaksi, c.coa, sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 168")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->group_by('pdb.id')
			->order_by('pb.tanggal_transaksi','asc')
			->order_by('pb.created_on','asc')
			->get()->result_array();
			//END DISKONTO

			$total_diskonto = 0;

			foreach ($diskonto as $row2){
				$total_diskonto += $row2['total'];
			}

			$total_diskonto_all = $total_diskonto;

			?>
			
			<tr class="table-judul">
				<th width="5%" align="center">NO.</th>
				<th width="15%" align="center">TRANSAKSI</th>
				<th width="15%" align="center">TGL. TRANSAKSI</th>
				<th width="20%" align="center">NO. TRANSAKSI</th>
				<th width="25%" align="center">URAIAN</th>
				<th width="20%" align="center">NILAI</th>
	        </tr>
			<?php foreach ($diskonto as $key => $row): ?>
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
				<th align="right"><?php echo number_format($total_diskonto_all,0,',','.');?></th>
	        </tr>			
	    </table>
		
	</body>
</html>