<!DOCTYPE html>
<html>
	<head>
	  <title>LAPORAN PIUTANG</title>
	  
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
		<table width="98%" border="0" cellpadding="15">
			<tr>
				<td width="100%" align="center">
					<div style="display: block;font-weight: bold;font-size: 11px;">LAPORAN PIUTANG</div>
				    <div style="display: block;font-weight: bold;font-size: 11px;">DIVISI BETON  PROYEK BENDUNGAN TEMEF</div>
				    <div style="display: block;font-weight: bold;font-size: 11px;">PT. BIA BUMI JAYENDRA</div>
					<div style="display: block;font-weight: bold;font-size: 11px; text-transform: uppercase;">PERIODE <?php echo str_replace($search, $replace, $subject);?></div>
				</td>
			</tr>
		</table>	
		<table cellpadding="2" width="98%">
			<tr class="table-judul">
				<th width="5%" align="center" rowspan="2">&nbsp; <br />NO.</th>
				<th width="23%" align="center" rowspan="2">&nbsp; <br />REKANAN</th>
				<th width="12%" align="center" rowspan="2">&nbsp; <br />PENJUALAN</th>
				<th width="12%" align="center" rowspan="2">&nbsp; <br />TAGIHAN</th>
				<th width="12%" align="center">TAGIHAN</th>
				<th width="12%" align="center" rowspan="2">&nbsp; <br />PEMBAYARAN</th>
				<th width="24%" align="center"colspan="2">SISA HUTANG</th>
			</tr>
			<tr class="table-judul">
				<th align="center">BRUTO</th>
				<th align="center">PENERIMAAN</th>
				<th align="center">INVOICE</th>
			</tr>			
            <?php   
            if(!empty($data)){
            	foreach ($data as $key => $row) {
            		?>
            		<!--<tr class="table-baris1-bold">
            			<td align="center"><?php echo $key + 1;?></td>
            			<td align="left" colspan="7"><?php echo $row['name'];?></td>
            		</tr>-->
					<?php
					$jumlah_penerimaan = 0;
					$jumlah_tagihan = 0;
					$jumlah_tagihan_bruto = 0;
					$jumlah_pembayaran = 0;
					$jumlah_sisa_piutang_penerimaan = 0;
					$jumlah_sisa_piutang_tagihan = 0;
            		foreach ($row['mats'] as $mat) {
            			?>
						<!--<tr class="table-baris1-bold">
            			<td align="center"></td>
            			<td align="left"></td>
            			<td align="right"><?php echo $mat['penerimaan'];?></td>
						<td align="right"><?php echo $mat['tagihan'];?></td>
						<td align="right"><?php echo $mat['tagihan_bruto'];?></td>
						<td align="right"><?php echo $mat['pembayaran'];?></td>
						<td align="right"><?php echo $mat['sisa_piutang_penerimaan'];?></td>
						<td align="right"><?php echo $mat['sisa_piutang_tagihan'];?></td>
            		</tr>-->

					<?php
					$jumlah_penerimaan += str_replace(['.', ','], ['', '.'], $mat['penerimaan']);
					$jumlah_tagihan += str_replace(['.', ','], ['', '.'], $mat['tagihan']);
					$jumlah_tagihan_bruto += str_replace(['.', ','], ['', '.'], $mat['tagihan_bruto']);
					$jumlah_pembayaran += str_replace(['.', ','], ['', '.'], $mat['pembayaran']);
					$jumlah_sisa_piutang_penerimaan += str_replace(['.', ','], ['', '.'], $mat['sisa_piutang_penerimaan']);
					$jumlah_sisa_piutang_tagihan += str_replace(['.', ','], ['', '.'], $mat['sisa_piutang_tagihan']);
					}	
					?>
					<tr class="table-baris2-bold">
						<td align="right"><?php echo $key + 1;?></td>
						<td align="left"><?php echo $row['name'];?></td>
						<td align="right"><?php echo number_format($jumlah_penerimaan,0,',','.');?></td>
						<td align="right"><?php echo number_format($jumlah_tagihan,0,',','.');?></td>
						<td align="right"><?php echo number_format($jumlah_tagihan_bruto,0,',','.');?></td>
						<td align="right"><?php echo number_format($jumlah_pembayaran,0,',','.');?></td>
						<td align="right"><?php echo number_format($jumlah_sisa_piutang_penerimaan,0,',','.');?></td>
						<td align="right"><?php echo number_format($jumlah_sisa_piutang_tagihan,0,',','.');?></td>
            		</tr>
					<?php
            		}
            }else {
            	?>
            	<tr>
            		<td width="100%" colspan="8" align="center">NO DATA</td>
            	</tr>
            	<?php
            }
            ?>
            <tr class="table-total">
				<th align="right" colspan="2">TOTAL</th>
				<th align="right"><?php echo number_format($total_penerimaan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_tagihan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_tagihan_bruto,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_pembayaran,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_sisa_piutang_penerimaan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_sisa_piutang_tagihan,0,',','.');?></th>
            </tr>   
		</table>
		
	</body>
</html>