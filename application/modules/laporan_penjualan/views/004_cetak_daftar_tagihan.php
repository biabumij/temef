<!DOCTYPE html>
<html>
	<head>
	  <title>DAFTAR TAGIHAN PENJUALAN</title>
	  
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
		<table width="98%" cellpadding="15">
			<tr class="table-active" style="">
				<td width="100%" align="center">
					<div style="display: block;font-weight: bold;font-size: 11px;">DAFTAR TAGIHAN PENJUALAN</div>
					<div style="display: block;font-weight: bold;font-size: 11px;">DIVISI BETON  PROYEK BENDUNGAN TEMEF</div>
				    <div style="display: block;font-weight: bold;font-size: 11px;">PT. BIA BUMI JAYENDRA</div>
					<div style="display: block;font-weight: bold;font-size: 11px; text-transform: uppercase;">PERIODE <?php echo str_replace($search, $replace, $subject);?></div>
				</td>
			</tr>
		</table>
		<table cellpadding="3" width="98%">
			<tr class="table-judul">
				<th align="center" width="5%" rowspan="2">&nbsp; <br />NO.</th>
                <th align="center" width="10%">PELANGGAN</th>
				<th align="center" width="18%" rowspan="2">&nbsp; <br />NO. INVOICE</th>
				<th align="center" width="15%" rowspan="2">&nbsp; <br />MEMO</th>
				<th align="center" width="8%" rowspan="2">&nbsp; <br />VOLUME</th>
				<th align="center" width="8%" rowspan="2">&nbsp; <br />SATUAN</th>
				<th align="center" width="8%">HARGA</th>
				<th align="center" width="10%">JUMLAH</th>
				<th align="center" width="8%" rowspan="2">&nbsp; <br />PPN</th>
				<th align="center" width="10%" rowspan="2">&nbsp; <br />TOTAL</th>
            </tr>
			<tr class="table-judul">
				<th align="center">TGL. INVOICE</th>
				<th align="center">SATUAN</th>
				<th align="center">TAGIHAN</th>
			</tr>
            <?php   
            if(!empty($data)){
            	foreach ($data as $key => $row) {
            		?>
            		<tr class="table-baris1-bold">
						<td align="center"><?php echo $key + 1;?></td>
            			<td align="left" colspan="9"><?php echo $row['nama'];?></td>
            		</tr>
            		<?php
            		foreach ($row['mats'] as $mat) {
            			?>
            			<tr class="table-baris1">
							<td align="center"></td>
	            			<td align="center"><?php echo $mat['tanggal_invoice'];?></td>
							<td align="left"><?php echo $mat['nomor_invoice'];?></td>
							<td align="left"><?php echo $mat['memo'];?></td>
							<td align="right"><?php echo $mat['qty'];?></td>
							<td align="right"><?php echo $mat['qty'];?></td>
							<td align="center"><?php echo $mat['measure'];?></td>
							<td align="right"><?php echo $mat['jumlah'];?></td>
							<td align="right"><?php echo $mat['ppn'];?></td>
							<td align="right"><?php echo $mat['total_price'];?></td>
	            		</tr>
            			<?php
					}
					?>
					<tr class="table-baris2-bold">
            			<td align="right" colspan="7">JUMLAH</td>
						<td align="right"><?php echo $row['jumlah'];?></td>
						<td align="right"><?php echo $row['ppn'];?></td>
						<td align="right"><?php echo $row['total_price'];?></td>
            		</tr>
					<?php
            		}	
            }else {
            	?>
            	<tr>
            		<td width="100%" colspan="10" align="center">NO DATA</td>
            	</tr>
            	<?php
            }
            ?>
            <tr class="table-total">
            	<th align="right" width="90%"><b>TOTAL</b></th>
            	<th align="right" width="10%"><?php echo number_format($total,0,',','.');?></th>
            </tr>
			
		</table>
	</body>
</html>