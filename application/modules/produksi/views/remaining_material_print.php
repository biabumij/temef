<!DOCTYPE html>
<html>
	<head>
	  <?= include 'lib.php'; ?>
	  <title>SISA BAHAN</title>

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
			font-size: 9px;
			color: black;
		}
	  </style>

	</head>
	<body>
		<table width="98%" border="0" cellpadding="3">
			<tr>
				<td align="center">
					<div style="display: block;font-weight: bold;font-size: 11px;">STOCK OPNAME BAHAN BAKU</div>
					<div style="display: block;font-weight: bold;font-size: 11px;">DIVISI BETON  PROYEK BENDUNGAN TEMEF</div>
				    <div style="display: block;font-weight: bold;font-size: 11px;">PT. BIA BUMI JAYENDRA</div>
					<div style="display: block;font-weight: bold;font-size: 11px; text-transform: uppercase;">PERIODE <?php echo str_replace($search, $replace, $subject);?></div>
				</td>
			</tr>
		</table>
		<br />
		<br />
		<table cellpadding="2" width="98%">
			<tr class="table-judul">
                <th width="5%" align="center">NO.</th>
                <th width="15%" align="center">TANGGAL</th>
                <th width="35%" align="center">URAIAN</th>
				<th width="15%" align="center">VOLUME</th>
                <th width="15%" align="center">SATUAN</th>
				<th width="20%" align="center">CATATAN</th>
            </tr>
            <?php
            
            $total = 0;
            if(!empty($data)){
            	$no=1;
            	foreach ($data as $key => $row) {	
            		$measure = $this->crud_global->GetField('pmm_measures',array('id'=>$row['display_measure']),'measure_name');
            		?>
            		<tr class="table-baris2">
            			<td align="center"><?php echo $key + 1;?></td>
            			<td align="center"><?= convertDateDBtoIndo($row["date"]); ?></td>
            			<td><?php echo $this->crud_global->GetField('produk',array('id'=>$row['material_id']),'nama_produk');?></td>
						<td align="center"><?php echo $measure;?></td>
            			<td align="right"><?php echo  number_format($row['display_volume'],2,',','.');?></td>
						<td align="center"><?php echo $row['notes'];?></td>
            		</tr>
            		<?php

					$no++;
            		$total += $row['total'];
            		
            	}
            }else {
            	?>
            	<tr>
            		<td width=100%" colspan="6" align="center">No Data</td>
            	</tr>
            	<?php
            }
            ?>	
		</table>
		<br />
		<br />
		<table width="98%" border="0" cellpadding="30">
			<tr >
				<td width="5%"></td>
				<td width="90%">
					<table width="100%" border="0" cellpadding="2">
						<tr>
							<td align="center" colspan="2">
								Disetujui Oleh
							</td>
							<td align="center" colspan="2">
								Diperiksa Oleh
							</td>
							<td align="center">
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
							<td align="center">
								
							</td>
							<td align="center">
								
							</td>
						</tr>
						<tr>
							<td align="center" >
								<b><u>Hadi Sucipto</u><br />
								Ka. Plant</b>
							</td>
							<td align="center" >
								<b><br />
								Ass. Ka. Plant</b>
							</td>
							<td align="center">
								<b><br />
								M. Keu & SDM</b>
							</td>
							<td align="center">
								<b><br />
								M. Teknik</b>
							</td>
							<td align="center">
								<b><br />
								Pj. Logistik</b>
							</td>
						</tr>
					</table>
				</td>
				<td width="5%"></td>
			</tr>
		</table>
		
	</body>
</html>