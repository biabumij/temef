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
		table tr.table-active4{
            font-weight: bold;
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
				<th align="center" width="10%"><b>Transaksi</b></th>
				<th align="center" width="10%"><b>COA</b></th>
				<th align="center" width="50%"><b>Kategori</b></th>
				<th align="center" width="30%" align="right"><b>Jumlah</b></th>
			</tr>
			<tr class="table-active2">
				<th width="100%" align="left" colspan="5"><b>Biaya Overhead Produksi</b></th>
			</tr>
			<?php
			if(!empty($biaya_langsung_parent)){
				foreach ($biaya_langsung_parent as $key => $bl) {
					?>
					<tr class="table-active4">
						<td width="10%" align="center">BIAYA</td>
						<td width="10%" align="center"><?= $bl['coa_number'] = $this->crud_global->GetField('pmm_coa',array('id'=>$bl['coa_parent']),'coa_number');?></td>
						<td width="50%"><?= $bl['coa'] = $this->crud_global->GetField('pmm_coa',array('id'=>$bl['coa_parent']),'coa');?></td>
						<td width="30%" align="right"></td>
					</tr>
					<?php
				}
			}
			$total_biaya_langsung  = 0;
			if(!empty($biaya_langsung)){
				foreach ($biaya_langsung as $key => $bl) {
					?>
					<tr>	
						<td width="10%" align="center">BIAYA</td>
						<td width="10%" align="center"><?= $bl['coa_number'];?></td>
						<td width="2%"></td>
						<td width="48%"><?= $bl['coa'];?></td>
						<td width="30%" align="right"><?= $this->filter->Rupiah($bl['total']);?></td>
					</tr>
					<?php
					$total_biaya_langsung += $bl['total'];	
				}
			}

			if(!empty($biaya_langsung_jurnal_parent)){
				foreach ($biaya_langsung_jurnal_parent as $key => $blj) {
					?>	
					<tr class="table-active4">
						<td width="10%" align="center">JURNAL</td>
						<td width="10%" align="center"><?= $blj['coa_number'] = $this->crud_global->GetField('pmm_coa',array('id'=>$blj['coa_parent']),'coa_number');?></td>
						<td width="50%"><?= $blj['coa'] = $this->crud_global->GetField('pmm_coa',array('id'=>$blj['coa_parent']),'coa');?></td>
						<td width="30%" align="right"></td>
					</tr>
					<?php				
				}
			}

			$total_biaya_langsung_jurnal  = 0;
			$grand_total_biaya_langsung = $total_biaya_langsung;
				if(!empty($biaya_langsung_jurnal)){
					foreach ($biaya_langsung_jurnal as $key => $blj) {
						?>	
						<tr>
							<td width="10%" align="center">JURNAL</td>
							<td width="10%" align="center"><?= $blj['coa_number'];?></td>
							<td width="2%"></td>
							<td width="48%"><?= $blj['coa'];?></td>
							<td width="30%" align="right"><?= $this->filter->Rupiah($blj['total']);?></td>
						</tr>
						<?php
						$total_biaya_langsung_jurnal += $blj['total'];					
					}
			}
			$total_a = $grand_total_biaya_langsung + $total_biaya_langsung_jurnal;
			?>
			<tr class="active">
				<td width="80%" style="padding-left:20px;"><b>Total Biaya Overhead Produksi</b></td>
				<td width="20%" align="right"><b><?= $this->filter->Rupiah($total_a);?></b></td>
			</tr>
			<tr>
				<th width="100%" colspan="5"></th>
			</tr>
			<tr class="table-active2">
				<th width="100%" align="left" colspan="5"><b>Biaya Umum & Administrasi</b></th>
			</tr>
			<?php
			if(!empty($biaya_parent)){
				foreach ($biaya_parent as $key => $row) {
					?>
					<tr class="table-active4">
						<td width="10%" align="center">BIAYA</td>
						<td width="10%" align="center"><?= $row['coa_number'] = $this->crud_global->GetField('pmm_coa',array('id'=>$row['coa_parent']),'coa_number');?></td>
						<td width="50%"><?= $row['coa'] = $this->crud_global->GetField('pmm_coa',array('id'=>$row['coa_parent']),'coa');?></td>
						<td width="30%" align="right"></td>
					</tr>
					<?php			
				}
			}

			$total_biaya  = 0;
			if(!empty($biaya)){
				foreach ($biaya as $key => $row) {
					?>
					<tr>
						<td width="10%" align="center">BIAYA</td>
						<td width="10%" align="center"><?= $row['coa_number'];?></td>
						<td width="2%"></td>
						<td width="48%"><?= $row['coa'];?></td>
						<td width="30%" align="right"><?= $this->filter->Rupiah($row['total']);?></td>
					</tr>
					<?php
					$total_biaya += $row['total'];				
				}
			}

			if(!empty($biaya_jurnal_parent)){
				foreach ($biaya_jurnal_parent as $key => $row2) {
					?>
					<tr class="table-active4">
						<td width="10%" align="center">JURNAL</td>
						<td width="10%" align="center"><?= $row2['coa_number'] = $this->crud_global->GetField('pmm_coa',array('id'=>$row2['coa_parent']),'coa_number');?></td>
						<td width="50%"><?= $row2['coa'] = $this->crud_global->GetField('pmm_coa',array('id'=>$row2['coa_parent']),'coa');?></td>
						<td width="30%" align="right"></td>
					</tr>
					<?php			
				}
			}

			$total_biaya_jurnal = 0;
			$grand_total_biaya = $total_biaya;
			if(!empty($biaya_jurnal)){
				foreach ($biaya_jurnal as $key => $row2) {
					?>
					<tr>
						<td width="10%" align="center">JURNAL</td>
						<td width="10%" align="center"><?= $row2['coa_number'];?></td>
						<td width="2%"></td>
						<td width="48%"><?= $row2['coa'];?></td>
						<td width="30%" align="right"><?= $this->filter->Rupiah($row2['total']);?></td>
					</tr>
					<?php
					$total_biaya_jurnal += $row2['total'];				
				}
			}
			$total_b = $grand_total_biaya + $total_biaya_jurnal;
			?>
			<tr class="active">
				<td width="80%" style="padding-left:20px;"><b>Total Biaya Umum & Administrasi</b></td>
				<td width="20%" align="right"><b><?= $this->filter->Rupiah($total_b);?></b></td>
			</tr>
			<tr>
				<th width="100%" colspan="5"></th>
			</tr>
			<tr class="table-active2">
				<th width="100%" align="left" colspan="5"><b>Biaya Lain - Lain</b></th>
			</tr>
			<?php
			if(!empty($biaya_lainnya_parent)){
				foreach ($biaya_lainnya_parent as $key => $row) {
					?>
					<tr class="table-active4">
						<td width="10%" align="center">BIAYA</td>
						<td width="10%" align="center"><?= $row['coa_number'] = $this->crud_global->GetField('pmm_coa',array('id'=>$row['coa_parent']),'coa_number');?></td>
						<td width="50%"><?= $row['coa'] = $this->crud_global->GetField('pmm_coa',array('id'=>$row['coa_parent']),'coa');?></td>
						<td width="30%" align="right"></td>
					</tr>
					<?php				
				}
			}

			$total_biaya_lainnya = 0;
			if(!empty($biaya_lainnya)){
				foreach ($biaya_lainnya as $key => $row) {
					?>
					<tr>
						<td width="10%" align="center">BIAYA</td>
						<td width="10%" align="center"><?= $row['coa_number'];?></td>
						<td width="2%"></td>
						<td width="48%"><?= $row['coa'];?></td>
						<td width="30%" align="right"><?= $this->filter->Rupiah($row['total']);?></td>
					</tr>
					<?php
					$total_biaya_lainnya += $row['total'];					
				}
			}

			if(!empty($biaya_lainnya_jurnal_parent)){
				foreach ($biaya_lainnya_jurnal_parent as $key => $row2) {
					?>
					<tr class="table-active4">	
						<td width="10%" align="center">JURNAL</td>
						<td width="10%" align="center"><?= $row2['coa_number'] = $this->crud_global->GetField('pmm_coa',array('id'=>$row2['coa_parent']),'coa_number');?></td>
						<td width="50%"><?= $row2['coa'] = $this->crud_global->GetField('pmm_coa',array('id'=>$row2['coa_parent']),'coa');?></td>
						<td width="30%" align="right"></td>
					</tr>
					<?php				
				}
			}

			$total_biaya_lainnya_jurnal = 0;
			$grand_total_biaya_lainnya = $total_biaya_lainnya;
			if(!empty($biaya_lainnya_jurnal)){
				foreach ($biaya_lainnya_jurnal as $key => $row2) {
					?>
					<tr>
						<td width="10%" align="center">JURNAL</td>
						<td width="10%" align="center"><?= $row2['coa_number'];?></td>
						<td width="2%"></td>
						<td width="48%"><?= $row2['coa'];?></td>
						<td width="30%" align="right"><?= $this->filter->Rupiah($row2['total']);?></td>
					</tr>
					<?php
					$total_biaya_lainnya_jurnal += $row2['total'];					
				}
			}
			$total_c = $grand_total_biaya_lainnya + $total_biaya_lainnya_jurnal;
			?>
			<tr class="active">
				<td width="80%" style="padding-left:20px;"><b>Total Biaya Lain - Lain</b></td>
				<td width="20%" align="right"><b><?= $this->filter->Rupiah($total_c);?></b></td>
			</tr>
			<tr>
			<th width="100%" colspan="5"></th>
			</tr>
			<tr class="table-active2">
			<th width="100%" align="left" colspan="5"><b>Biaya Persiapan</b></th>
			</tr>
			<?php
			if(!empty($biaya_persiapan_parent)){
				foreach ($biaya_persiapan_parent as $key => $row) {
					?>
					<tr class="table-active4">
						<td width="10%" align="center">BIAYA</td>
						<td width="10%" align="center"><?= $row['coa_number'] = $this->crud_global->GetField('pmm_coa',array('id'=>$row['coa_parent']),'coa_number');?></td>
						<td width="50%"><?= $row['coa'] = $this->crud_global->GetField('pmm_coa',array('id'=>$row['coa_parent']),'coa');?></td>
						<td width="30%" align="right"></td>
					</tr>
					<?php					
				}
			}

			$total_biaya_persiapan = 0;
			if(!empty($biaya_persiapan)){
				foreach ($biaya_persiapan as $key => $row) {
					?>
					<tr>
						<td width="10%" align="center">BIAYA</td>
						<td width="10%" align="center"><?= $row['coa_number'];?></td>
						<td width="2%"></td>
						<td width="48%"><?= $row['coa'];?></td>
						<td width="30%" align="right"><?= $this->filter->Rupiah($row['total']);?></td>
					</tr>
					<?php
					$total_biaya_persiapan += $row['total'];					
				}
			}

			if(!empty($biaya_persiapan_jurnal_parent)){
				foreach ($biaya_persiapan_jurnal_parent as $key => $row2) {
					?>
					<tr class="table-active4">
						<td width="10%" align="center">JURNAL</td>
						<td width="10%" align="center"><?= $row2['coa_number'] = $this->crud_global->GetField('pmm_coa',array('id'=>$row2['coa_parent']),'coa_number');?></td>
						<td width="50%"><?= $row2['coa'] = $this->crud_global->GetField('pmm_coa',array('id'=>$row2['coa_parent']),'coa');?></td>
						<td width="30%" align="right"></td>
					</tr>
					<?php					
				}
			}

			$total_biaya_persiapan_jurnal = 0;
			$grand_total_biaya_persiapan = $total_biaya_persiapan;
			if(!empty($biaya_persiapan_jurnal)){
				foreach ($biaya_persiapan_jurnal as $key => $row2) {
					?>
					<tr>
						<td width="10%" align="center">JURNAL</td>
						<td width="10%" align="center"><?= $row2['coa_number'];?></td>
						<td width="2%"></td>
						<td width="48%"><?= $row2['coa'];?></td>
						<td width="30%" align="right"><?= $this->filter->Rupiah($row2['total']);?></td>
					</tr>
					<?php
					$total_biaya_persiapan_jurnal += $row2['total'];					
				}
			}
			$total_d = $grand_total_biaya_persiapan + $total_biaya_persiapan_jurnal;
			?>
			<tr class="active">
				<td width="80%" style="padding-left:20px;"><b>Total Biaya Persiapan</b></td>
				<td width="20%" align="right"><b><?= $this->filter->Rupiah($total_d);?></b></td>
			</tr>
		</table>
		<br />
		<br />
		<table width="98%" border="0" cellpadding="20">
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
								<b><u>Farid F. Fanggi Ello</u><br />
								Kepala Unit Proyek</b>
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