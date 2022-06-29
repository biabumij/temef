<!DOCTYPE html>
<html>
	<head>
	  <?= include 'lib.php'; ?>

	  <title>RAP</title>
	  
	  <?php
		$search = array(
		'January',
		'February',
		'March',
		'April',
		'May',
		'Juei',
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

		table tr.table-baris2-bold{
			font-size: 8px;
			background-color: #E8E8E8;
			font-weight: bold;
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
		<table width="100%" border="0" cellpadding="3">
			<tr>
				<td align="center">
					<div style="display: block;font-weight: bold;font-size: 12px;">RAP</div>
				</td>
			</tr>
		</table>
		<br />
		<br />
		<table width="100%" border="0">
			<tr>
				<th width="20%">Tanggal</th>
				<th width="10px">:</th>
				<th width="50%" align="left"><?= convertDateDBtoIndo($rap["tanggal_rap"]); ?></th>
			</tr>
			<tr>
				<th>Nomor</th>
				<th width="10px">:</th>
				<th width="50%" align="left"><?php echo $rap['nomor_rap'];?></th>
			</tr>
		</table>
		<br />
		<br />
		<table cellpadding="5" width="98%">
			<tr class="table-judul">
				<?php
					$total = 0;
					?>
					<?php
					$total = $rap['total_bahan'] + $rap['total_alat'] + $rap['total_overhead'] + $rap['total_biaya_admin'] + $rap['total_diskonto'];
				?>
                <th width="5%" align="center">NO.</th>
                <th width="45%" align="center">URAIAN</th>
				<th width="50%" align="center">TOTAL</th>
            </tr>
			<tr class="table-baris1">
				<td align="center">1.</td>
				<td align="left">TOTAL BAHAN</td>
				<td align="right"><?= number_format($rap['total_bahan'],0,',','.'); ?></td>
			</tr>
			<tr class="table-baris1">
				<td align="center">2.</td>
				<td align="left">TOTAL ALAT</td>
				<td align="right"><?= number_format($rap['total_alat'],0,',','.'); ?></td>
			</tr>
			<tr class="table-baris1">
				<td align="center">3.</td>
				<td align="left">TOTAL OVERHEAD</td>
				<td align="right"><?= number_format($rap['total_overhead'],0,',','.'); ?></td>
			</tr>
			<tr class="table-baris1">
				<td align="center">4.</td>
				<td align="left">TOTAL BIAYA ADMIN</td>
				<td align="right"><?= number_format($rap['total_biaya_admin'],0,',','.'); ?></td>
			</tr>
			<tr class="table-baris1">
				<td align="center">5.</td>
				<td align="left">TOTAL DISKONTO</td>
				<td align="right"><?= number_format($rap['total_diskonto'],0,',','.'); ?></td>
			</tr>
			<tr class="table-total">
				<td align="right" colspan="2">TOTAL</td>
				<td align="right"><?= number_format($total,0,',','.'); ?></td>
			</tr>
		</table>
		<br />
		<br />
		
	</body>
</html>