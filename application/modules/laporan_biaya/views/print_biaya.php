<!DOCTYPE html>
<html>
	<head>
	  <title>Laporan Biaya</title>
	  
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
					<div style="display: block;font-weight: bold;font-size: 12px;">LAPORAN BIAYA</div>
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
				<th width="100%" align="left" colspan="6"><b>Biaya Overhead Produksi</b></th>
			</tr>
			<?php
			$total_biaya_langsung  = 0;
			if(!empty($biaya_langsung)){
				foreach ($biaya_langsung as $key => $bl) {
					?>
					<tr>
						<td width="10%"><?= $bl['tanggal_transaksi'];?></td>
						<td width="10%">BIAYA</td>
						<td width="50%"><?= $bl['coa'];?></td>
						<td width="30%" align="right"><?= $this->filter->Rupiah($bl['total']);?></td>
					</tr>
					<?php
					$total_biaya_langsung += $bl['total'];	
				}
			}
			$total_biaya_langsung_jurnal  = 0;
			$grand_total_biaya_langsung = $total_biaya_langsung;
				if(!empty($biaya_langsung_jurnal)){
					foreach ($biaya_langsung_jurnal as $key => $blj) {
						?>	
						<tr>
							<td><?= $blj['tanggal_transaksi'];?></td>
							<td>JURNAL</td>
							<td><?= $blj['coa'];?></td>
							<td align="right"><?= $this->filter->Rupiah($blj['total']);?></td>
						</tr>
						<?php
						$total_biaya_langsung_jurnal += $blj['total'];					
					}
			}
			$total_a = $grand_total_biaya_langsung + $total_biaya_langsung_jurnal;
			?>
			<tr class="active">
				<td width="80%" style="padding-left:20px;">Total Biaya Overhead Produksi</td>
				<td width="20%" align="right"><b><?= $this->filter->Rupiah($total_a);?></b></td>
			</tr>
			<tr>
				<th width="100%" colspan="6"></th>
			</tr>
			<tr class="table-active2">
				<th width="100%" align="left" colspan="6"><b>Biaya Umum & Administrasi</b></th>
			</tr>
			<?php
			$total_biaya  = 0;
			if(!empty($biaya)){
				foreach ($biaya as $key => $row) {
					?>
					<tr>
						<td width="10%"><?= $row['tanggal_transaksi'];?></td>
						<td width="10%">BIAYA</td>
						<td width="50%"><?= $row['coa'];?></td>
						<td align="center" width="30%" align="right"><?= $this->filter->Rupiah($row['total']);?></td>
					</tr>
					<?php
					$total_biaya += $row['total'];				
				}
			}
			$total_biaya_jurnal = 0;
			$grand_total_biaya = $total_biaya;
			if(!empty($biaya_jurnal)){
				foreach ($biaya_jurnal as $key => $row2) {
					?>
					<tr>
						<td><?= $row2['tanggal_transaksi'];?></td>
						<td>JURNAL</td>
						<td><?= $row2['coa'];?></td>
						<td align="right"><?= $this->filter->Rupiah($row2['total']);?></td>
					</tr>
					<?php
					$total_biaya_jurnal += $row2['total'];				
				}
			}
			$total_b = $grand_total_biaya + $total_biaya_jurnal;
			?>
			<tr class="active">
				<td width="80%" style="padding-left:20px;">Total Biaya Umum & Administrasi</td>
				<td width="20%" align="right"><b><?= $this->filter->Rupiah($total_b);?></b></td>
			</tr>
			<tr>
				<th width="100%" colspan="6"></th>
			</tr>
			<tr class="table-active2">
				<th width="100%" align="left" colspan="6"><b>Biaya Lain - Lain</b></th>
			</tr>
			<?php
			$total_biaya_lainnya = 0;
			if(!empty($biaya_lainnya)){
				foreach ($biaya_lainnya as $key => $row) {
					?>
					<tr>
						<td width="10%"><?= $row['tanggal_transaksi'];?></td>
						<td width="10%">BIAYA</td>
						<td width="50%"><?= $row['coa'];?></td>
						<td align="center" width="30%" align="right"><?= $this->filter->Rupiah($row['total']);?></td>
					</tr>
					<?php
					$total_biaya_lainnya += $row['total'];					
				}
			}
			$total_biaya_lainnya_jurnal = 0;
			$grand_total_biaya_lainnya = $total_biaya_lainnya;
			if(!empty($biaya_lainnya_jurnal)){
				foreach ($biaya_lainnya_jurnal as $key => $row2) {
					?>
					<tr>
						<td><?= $row2['tanggal_transaksi'];?></td>
						<td>JURNAL</td>
						<td><?= $row2['coa'];?></td>
						<td align="right"><?= $this->filter->Rupiah($row2['total']);?></td>
					</tr>
					<?php
					$total_biaya_lainnya_jurnal += $row2['total'];					
				}
			}
			$total_c = $grand_total_biaya_lainnya + $total_biaya_lainnya_jurnal;
			?>
			<tr class="active">
				<td width="80%" style="padding-left:20px;">Total Biaya Lain - Lain</td>
				<td width="20%" align="right"><b><?= $this->filter->Rupiah($total_c);?></b></td>
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