<!DOCTYPE html>
<html>
	<head>
	  <title>DAFTAR TAGIHAN PEMBELIAN</title>
	  
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
			body {
				font-family: helvetica;
			}

			table.table-border-pojok-kiri, th.table-border-pojok-kiri, td.table-border-pojok-kiri {
				border-top: 1px solid black;
				border-bottom: 1px solid black;
				border-right: 1px solid #cccccc;
				border-left: 1px solid black;
			}

			table.table-border-pojok-tengah, th.table-border-pojok-tengah, td.table-border-pojok-tengah {
				border-top: 1px solid black;
				border-bottom: 1px solid black;
				border-right: 1px solid #cccccc;
			}

			table.table-border-pojok-kanan, th.table-border-pojok-kanan, td.table-border-pojok-kanan {
				border-top: 1px solid black;
				border-bottom: 1px solid black;
				border-right: 1px solid black;
			}

			table.table-border-spesial, th.table-border-spesial, td.table-border-spesial {
				border-left: 1px solid black;
				border-right: 1px solid black;
			}

			table.table-border-spesial-kiri, th.table-border-spesial-kiri, td.table-border-spesial-kiri {
				border-left: 1px solid black;
				border-top: 2px solid black;
				border-bottom: 2px solid black;
			}

			table.table-border-spesial-tengah, th.table-border-spesial-tengah, td.table-border-spesial-tengah {
				border-left: 1px solid #cccccc;
				border-right: 1px solid #cccccc;
				border-top: 2px solid black;
				border-bottom: 2px solid black;
			}

			table.table-border-spesial-kanan, th.table-border-spesial-kanan, td.table-border-spesial-kanan {
				border-left: 1px solid #cccccc;
				border-right: 1px solid black;
				border-top: 2px solid black;
				border-bottom: 2px solid black;
			}

			table tr.table-judul{
				border: 1px solid;
				background-color: #e69500;
				font-weight: bold;
				font-size: 7px;
				color: black;
			}
				
			table tr.table-baris1{
				background-color: none;
				font-size: 7px;
			}

			table tr.table-baris1-bold{
				background-color: none;
				font-size: 7px;
				font-weight: bold;
			}
				
			table tr.table-total{
				background-color: #FFFF00;
				font-weight: bold;
				font-size: 7px;
				color: black;
			}

			table tr.table-total2{
				background-color: #eeeeee;
				font-weight: bold;
				font-size: 7px;
				color: black;
			}
	  </style>

	</head>
	<body>
		<div align="center" style="display: block;font-weight: bold;font-size: 11px;">DAFTAR TAGIHAN PEMBELIAN</div>
		<div align="center" style="display: block;font-weight: bold;font-size: 11px;">DIVISI STONE CRUSHER</div>
		<div align="center" style="display: block;font-weight: bold;font-size: 11px;">PT. BIA BUMI JAYENDRA</div>
		<div align="center" style="display: block;font-weight: bold;font-size: 11px; text-transform: uppercase;">PERIODE : <?php echo str_replace($search, $replace, $subject);?></div>
		<br /><br /><br />
		<table cellpadding="3" width="98%">
			<tr class="table-judul">
				<th align="center" width="5%" rowspan="2" class="table-border-pojok-kiri">&nbsp; <br />NO.</th>
                <th align="center" width="10%" class="table-border-pojok-tengah">REKANAN</th>
				<th align="center" width="20%" rowspan="2" class="table-border-pojok-tengah">&nbsp; <br />NO. INVOICE</th>
				<th align="center" width="10%" rowspan="2" class="table-border-pojok-tengah">&nbsp; <br />VOLUME</th>
				<th align="center" width="10%" rowspan="2" class="table-border-pojok-tengah">&nbsp; <br />SATUAN</th>
				<th align="center" width="15%" rowspan="2" class="table-border-pojok-tengah">&nbsp; <br />TOTAL TAGIHAN</th>
				<th align="center" width="15%" rowspan="2" class="table-border-pojok-tengah">&nbsp; <br />TOTAL PEMBAYARAN</th>
				<th align="center" width="15%" rowspan="2" class="table-border-pojok-kanan">&nbsp; <br />SISA TAGIHAN</th>
            </tr>
			<tr class="table-judul">
				<th align="center" class="table-border-pojok-tengah">TGL. INVOICE</th>
			</tr>
            <?php   
            if(!empty($data)){
            	foreach ($data as $key => $row) {
            		?>
            		<tr class="table-baris1-bold">
						<td align="center" class="table-border-pojok-kiri"><?php echo $key + 1;?></td>
            			<td align="left" colspan="8" class="table-border-pojok-kanan"><?php echo $row['nama'];?></td>
            		</tr>
            		<?php
					$jumlah_total = 0;
					$jumlah_pembayaran = 0;
					$jumlah_sisa = 0;
            		foreach ($row['mats'] as $mat) {
            			?>
            			<tr class="table-baris1">
							<td align="center" class="table-border-pojok-kiri"></td>
	            			<td align="center" class="table-border-pojok-tengah"><?php echo $mat['tanggal_invoice'];?></td>
							<td align="left" class="table-border-pojok-tengah"><?php echo $mat['nomor_invoice'];?></td>
							<td align="right" class="table-border-pojok-tengah"><?php echo $mat['volume'];?></td>
							<td align="center" class="table-border-pojok-tengah"><?php echo $mat['measure'];?></td>
							<td align="right" class="table-border-pojok-tengah"><?php echo $mat['total'];?></td>
							<td align="right" class="table-border-pojok-tengah"><?php echo $mat['pembayaran'];?></td>
							<td align="right" class="table-border-pojok-kanan"><?php echo $mat['sisa'];?></td>
	            		</tr>
            			<?php
						$jumlah_total += str_replace(['.', ','], ['', '.'], $mat['total']);
						$jumlah_pembayaran += str_replace(['.', ','], ['', '.'], $mat['pembayaran']);
						$jumlah_sisa += str_replace(['.', ','], ['', '.'], $mat['sisa']);
					}
					?>
					<tr class="table-baris1-bold">
            			<td align="right" colspan="5" class="table-border-pojok-kiri">JUMLAH</td>
						<td align="right" class="table-border-pojok-tengah"><?php echo number_format($jumlah_total,0,',','.');?></td>
						<td align="right" class="table-border-pojok-tengah"><?php echo number_format($jumlah_pembayaran,0,',','.');?></td>
						<td align="right" class="table-border-pojok-kanan"><?php echo number_format($jumlah_sisa,0,',','.');?></td>
            		</tr>
					<?php
            		}
            }else {
            	?>
            	<tr>
            		<td width="100%" colspan="8" align="center"  class="table-border-spesial">NO DATA</td>
            	</tr>
            	<?php
            }
            ?>
            <tr class="table-total2">
            	<th align="right" colspan="5" class="table-border-spesial-kiri">TOTAL</th>
            	<th align="right" class="table-border-spesial-tengah"><?php echo number_format($total,0,',','.');?></th>
				<th align="right" class="table-border-spesial-tengah"><?php echo number_format($total_2,0,',','.');?></th>
				<th align="right" class="table-border-spesial-kanan"><?php echo number_format($total_3,0,',','.');?></th>
            </tr>
			
		</table>
	</body>
</html>