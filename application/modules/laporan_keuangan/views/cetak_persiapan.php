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
					<div style="display: block;font-weight: bold;font-size: 12px;">PERSIAPAN</div>
					<div style="display: block;font-weight: bold;font-size: 12px;"><?= $this->crud_global->GetField('pmm_setting_production',array('id'=>1),'nama_pt');?></div>
					<div style="display: block;font-weight: bold;font-size: 12px;text-transform: uppercase;">PERIODE : <?php echo $filter_date;?></div>
				</td>
			</tr>
		</table>
		<br />
		<br />
		<br />
		<table class="table-lap" width="98%" border="0" cellpadding="3">
			<tr class="table-active">
				<th align="center" width="20%"><b>Kode Akun</b></th>
				<th align="center" width="50%"><b>Nama Akun</b></th>
				<th align="center" width="30%" align="right"><b>Jumlah</b></th>
			</tr>
			<?php
			if(!empty($biaya_persiapan_parent)){
				foreach ($biaya_persiapan_parent as $key => $row) {
					?>
					<tr class="table-active4">
						<td width="20%" align="center"><?= $row['coa_number'] = $this->crud_global->GetField('pmm_coa',array('id'=>$row['coa_parent']),'coa_number');?></td>
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
						<td width="20%" align="center"><?= $row['coa_number'];?></td>
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
						<td width="20%" align="center"><?= $row2['coa_number'] = $this->crud_global->GetField('pmm_coa',array('id'=>$row2['coa_parent']),'coa_number');?></td>
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
						<td width="20%" align="center"><?= $row2['coa_number'];?></td>
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
			<tr class="table-active2">
				<td width="80%" style="padding-left:20px;"><b>Total Biaya Persiapan</b></td>
				<td width="20%" align="right"><b><?= $this->filter->Rupiah($total_d);?></b></td>
			</tr>
		</table>
		<br />
		<br />
		<table width="98%" border="0" cellpadding="15">
			<tr >
				<td width="10%"></td>
				<td width="80%">
					<table width="100%" border="0" cellpadding="2">
						<tr>
							<td align="center" >
							Diperiksa & Disetujui Oleh
							</td>
							<td align="center" >
								Dibuat Oleh
							</td>
						</tr>
						<?php
							$this->db->select('ttd.*');
							$this->db->where("(ttd.date_approval between '$date1' and '$date2')");
							$this->db->where("ttd.approval = 1 ");
							$this->db->order_by('ttd.date_approval','desc')->limit(1);
							$created_group = $this->db->get('ttd_laba_rugi ttd')->row_array();
						?>
						<tr>
							<td align="center" height="40px">
							<?php
								echo '<img src="'.$created_group['ttd_1'].'" width="70"/>';
							?>		
							</td>
							<td align="center">
							<?php
								echo '<img src="'.$created_group['ttd_2'].'" width="70"/>';
							?>
							</td>
						</tr>
						<tr>
							<td align="center" >
								<b><u>Erika Sinaga</u><br />
								M. Keu & SDM</b>
							</td>
							<td align="center" >
								<b><u>Debi Khania</u><br />
								Kasir</b>
							</td>
						</tr>
					</table>
				</td>
				<td width="10%"></td>
			</tr>
		</table>
	</body>
</html>