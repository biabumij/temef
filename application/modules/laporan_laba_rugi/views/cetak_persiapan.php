<!DOCTYPE html>
<html>
	<head>
	  <title>PERSIAPAN</title>
	  
	  <style type="text/css">
	  	table.minimalistBlack {
		  border: 0px solid #000000;
		  width: 100%;
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
            background-color: #b5b5b5;
        }
        table tr.table-active3{
            background-color: #eee;
        }
		hr{
			margin-top:0;
			margin-bottom:30px;
		}
		h3{
			margin-top:0;
		}
		.table-lap tr td, .table-lap tr th{
			border-bottom: 1px solid #000;
		}
	  </style>

	</head>
	<body>
		<table width="98%" border="0" cellpadding="3">
			<tr>
				<td align="center">
					<div style="display: block;font-weight: bold;font-size: 12px;">PERSIAPAN</div>
					<div style="display: block;font-weight: bold;font-size: 12px;"><?= $this->crud_global->GetField('pmm_setting_production',array('id'=>1),'nama_pt');?></div>
				</td>
			</tr>
		</table>
		<br />
		<br />
		<br />
		<table class="table-lap" width="98%" border="0" cellpadding="3">
			<tr class="table-active" style="">
				<td width="80%" colspan="5">
					<div style="display: block;font-weight: bold;font-size: 8px;">PERIODE</div>
				</td>
				<td align="right" width="20%">
					<div style="display: block;font-weight: bold;font-size: 8px;"><?php echo $filter_date;?></div>
				</td>
			</tr>
			<tr class="table-active3">
				<th align="center" width="10%"><b>Tanggal</b></th>
				<th align="center" width="10%"><b>Transaksi</b></th>
				<th align="center" width="50%"><b>Kategori</b></th>
				<th align="center" width="30%" align="right"><b>Jumlah</b></th>
			</tr>
			<tr class="table-active2">
				<th width="100%" align="left" colspan="6"><b>Biaya Lain - Lain</b></th>
			</tr>
			<?php
			$total_biaya_persiapan = 0;
			if(!empty($biaya_persiapan)){
				foreach ($biaya_persiapan as $key => $row) {
					?>
					<tr>
						<td width="10%"><?= $row['tanggal_transaksi'];?></td>
						<td width="10%">BIAYA</td>
						<td width="50%"><?= $row['coa'];?></td>
						<td align="center" width="30%" align="right"><?= $this->filter->Rupiah($row['total']);?></td>
					</tr>
					<?php
					$total_biaya_persiapan += $row['total'];					
				}
			}
			$total_biaya_persiapan_jurnal = 0;
			$grand_total_biaya_persiapan = $total_biaya_persiapan;
			if(!empty($biaya_persiapan_jurnal)){
				foreach ($biaya_persiapan_jurnal as $key => $row2) {
					?>
					<tr>
						<td><?= $row2['tanggal_transaksi'];?></td>
						<td>JURNAL</td>
						<td><?= $row2['coa'];?></td>
						<td align="right"><?= $this->filter->Rupiah($row2['total']);?></td>
					</tr>
					<?php
					$total_biaya_persiapan_jurnal += $row2['total'];					
				}
			}
			$total_d = $grand_total_biaya_persiapan + $total_biaya_persiapan_jurnal;
			?>
			<tr class="active">
				<td width="80%" style="padding-left:20px;">Total Biaya Persiapan</td>
				<td width="20%" align="right"><b><?= $this->filter->Rupiah($total_d);?></b></td>
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
								<b><u>Debi Khania</u><br />
								Pj. Keuangan & SDM</b>
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