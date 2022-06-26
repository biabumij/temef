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
	  	body{
	  		font-family: "Open Sans", Arial, sans-serif;
	  	}
	  	table.minimalistBlack {
		  border: 0px solid #000000;
		  width: 98%;
		  text-align: left;
		}
		table.minimalistBlack td, table.minimalistBlack th {
		  border: 1px solid #000000;
		  /*padding: 10px 4px;*/
		}
		table.minimalistBlack tr th {
		  /*font-size: 14px;*/
		  font-weight: bold;
		  color: #000000;
		  text-align: center;
		}
		table tr.table-active{
            background-color: #e69500;
        }
        table tr.table-active2{
            background-color: #cac8c8;
        }
        table tr.table-active3{
            background-color: #cccccc;
        }
        table tr.table-active4{
            background-color: #eee;
        }
        table tr.table-active4 td{
            font-weight: bold;
        }
		hr{
			margin-top:0;
			margin-bottom:30px;
		}
		h3{
			margin-top:0;
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
		<table class="minimalistBlack" cellpadding="2" width="98%">
			<tr class="table-active">
                <th width="5%">NO.</th>
                <th width="15%">TANGGAL</th>
                <th width="20%">URAIAN</th>
				<th width="10%">SATUAN</th>
                <th width="15%">VOLUME</th>
                <th width="15%">HARGA SATUAN</th>
                <th width="20%">NILAI</th>
            </tr>
            <?php
            
            $total = 0;
            if(!empty($data)){
            	$no=1;
            	foreach ($data as $key => $row) {	
            		$measure = $this->crud_global->GetField('pmm_measures',array('id'=>$row['display_measure']),'measure_name');
            		?>
            		<tr class="table-active4">
            			<td align="center"><?php echo $key + 1;?></td>
            			<td align="center"><?= convertDateDBtoIndo($row["date"]); ?></td>
            			<td><?php echo $this->crud_global->GetField('produk',array('id'=>$row['material_id']),'nama_produk');?></td>
						<td align="center"><?php echo $measure;?></td>
            			<td align="right"><?php echo  number_format($row['display_volume'],2,',','.');?></td>
            			<td align="right"><?php echo number_format($row['price'],0,',','.');?></td>
            			<td align="right"><?php echo number_format($row['total'],0,',','.');?></td>
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
            <tr class="table-active3">
            	<th width="80%" align="right" colspan="6">TOTAL</th>
				<th width="20%" align="right"><?php echo number_format($total,0,',','.');?></th>
            </tr>
           
          
		</table>
		<br />
		<br />
		<br />
		<br />
		<br />
		<table width="98%" border="0" cellpadding="0">
			<tr>
				<td width="5%"></td>
				<td width="90%">
					<table width="100%" border="1" cellpadding="2">
						<tr>
							<td align="center" colspan="2">
								<b>Disetujui Oleh</b>
							</td>
							<td align="center" colspan="2">
								<b>Diperiksa Oleh</b>
							</td>
							<td align="center" >
								<b>Dibuat Oleh</b>
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
							<td align="center">
								
							</td>
						</tr>
						<tr>
							<td align="left" >
								<b>Nama : </b><br />
								<b>Jabatan : Ka. Plant</b>
							</td>
							<td align="left" >
								<b>Nama : </b><br />
								<b>Jabatan : Ass. Ka. Plant</b>
							</td>
							<td align="left">
								<b>Nama : </b><br />
								<b>Jabatan : M. Keu & SDM</b>
							</td>
							<td align="left">
								<b>Nama : </b><br />
								<b>Jabatan : M. Teknik</b>
							</td>
							<td align="left">
								<b>Nama : </b><br />
								<b>Jabatan : Pj. Logistik</b>
							</td>
						</tr>
					</table>
				</td>
				<td width="5%"></td>
			</tr>
		</table>
		
	</body>
</html>