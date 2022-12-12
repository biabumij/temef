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
			font-size: 8px;
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
                <th width="15%" align="center">TANGGAL</th>
                <th width="20%" align="center">URAIAN</th>
				<th width="15%" align="center">SATUAN</th>
				<th width="15%" align="center">VOLUME</th>
				<th width="15%" align="center">HARGA SATUAN</th>
				<th width="20%" align="center">NILAI</th>
            </tr>
			<?php
			$awal = date('Y-m-d',strtotime($date1));
			$akhir = date('Y-m-d',strtotime($date2));
			
			$stock_opname_semen = $this->db->select('cat.date, cat.measure as satuan, cat.material_id, cat.notes, cat.display_volume as volume, pp.semen as harsat, (cat.display_volume * pp.semen) as nilai')
			->from('pmm_remaining_materials_cat cat ')
			->join('hpp_bahan_baku pp', 'cat.date = pp.date_hpp', 'left')
			->where("cat.date between '$awal' and '$akhir'")
			->where("cat.material_id = 4")
			->where("cat.status = 'PUBLISH'")
			->group_by('cat.id')
			->order_by('cat.material_id','asc')
			->get()->result_array();

			$nilai_semen = 0;

			foreach ($stock_opname_semen as $x){
				$nilai_semen += $x['nilai'];
			}

			$stock_opname_pasir = $this->db->select('cat.date, cat.measure as satuan, cat.material_id, cat.notes, cat.display_volume as volume, pp.pasir as harsat, (cat.display_volume * pp.pasir) as nilai')
			->from('pmm_remaining_materials_cat cat ')
			->join('hpp_bahan_baku pp', 'cat.date = pp.date_hpp', 'left')
			->where("cat.date between '$awal' and '$akhir'")
			->where("cat.material_id = 5")
			->where("cat.status = 'PUBLISH'")
			->group_by('cat.id')
			->order_by('cat.material_id','asc')
			->get()->result_array();

			$nilai_pasir = 0;

			foreach ($stock_opname_pasir as $x){
				$nilai_pasir += $x['nilai'];
			}

			$stock_opname_batu1020 = $this->db->select('cat.date, cat.measure as satuan, cat.material_id, cat.notes, cat.display_volume as volume, pp.batu1020 as harsat, (cat.display_volume * pp.batu1020) as nilai')
			->from('pmm_remaining_materials_cat cat ')
			->join('hpp_bahan_baku pp', 'cat.date = pp.date_hpp', 'left')
			->where("cat.date between '$awal' and '$akhir'")
			->where("cat.material_id = 6")
			->where("cat.status = 'PUBLISH'")
			->group_by('cat.id')
			->order_by('cat.material_id','asc')
			->get()->result_array();

			$nilai_batu1020 = 0;
			$total = 0;

			foreach ($stock_opname_batu1020 as $x){
				$nilai_batu1020 += $x['nilai'];
			}

			$stock_opname_batu2030 = $this->db->select('cat.date, cat.measure as satuan, cat.material_id, cat.notes, cat.display_volume as volume, pp.batu2030 as harsat, (cat.display_volume * pp.batu2030) as nilai')
			->from('pmm_remaining_materials_cat cat ')
			->join('hpp_bahan_baku pp', 'cat.date = pp.date_hpp', 'left')
			->where("cat.date between '$awal' and '$akhir'")
			->where("cat.material_id = 7")
			->where("cat.status = 'PUBLISH'")
			->group_by('cat.id')
			->order_by('cat.material_id','asc')
			->get()->result_array();

			$nilai_batu2030 = 0;
			$total = 0;

			foreach ($stock_opname_batu2030 as $x){
				$nilai_batu2030 += $x['nilai'];
			}

			$stock_opname_solar = $this->db->select('cat.date, cat.measure as satuan, cat.material_id, cat.notes, cat.display_volume as volume, pp.solar as harsat, (cat.display_volume * pp.solar) as nilai')
			->from('pmm_remaining_materials_cat cat ')
			->join('hpp_bahan_baku pp', 'cat.date = pp.date_hpp', 'left')
			->where("cat.date between '$awal' and '$akhir'")
			->where("cat.material_id = 8")
			->where("cat.status = 'PUBLISH'")
			->group_by('cat.id')
			->order_by('cat.material_id','asc')
			->get()->result_array();

			$nilai_solar = 0;

			foreach ($stock_opname_solar as $x){
				$nilai_solar += $x['nilai'];
			}

			?>
			<?php
			foreach ($stock_opname_semen as $row) : ?>  
			<tr class="table-baris2">
				<td align="center"><?php echo $row['date'] = date('d-m-Y',strtotime($row['date']));;?></td>
				<td align="left">Semen</td>
				<td align="center"><?php echo $this->crud_global->GetField('pmm_measures',array('id'=>$row['satuan']),'measure_name');?></td>
				<td align="right"><?= number_format($row['volume'],2,',','.'); ?></td>
				<td align="right"><?= number_format($row['harsat'],0,',','.'); ?></td>
				<td align="right"><?= number_format($row['nilai'],0,',','.'); ?></td>
			</tr>
			<?php
			endforeach; ?>
			
			<?php
			foreach ($stock_opname_pasir as $row) : ?>  
			<tr class="table-baris2">
				<td align="center"><?php echo $row['date'] = date('d-m-Y',strtotime($row['date']));;?></td>
				<td align="left"><?php echo $this->crud_global->GetField('produk',array('id'=>$row['material_id']),'nama_produk');?></td>
				<td align="center"><?php echo $this->crud_global->GetField('pmm_measures',array('id'=>$row['satuan']),'measure_name');?></td>
				<td align="right"><?= number_format($row['volume'],2,',','.'); ?></td>
				<td align="right"><?= number_format($row['harsat'],0,',','.'); ?></td>
				<td align="right"><?= number_format($row['nilai'],0,',','.'); ?></td>
			</tr>
			<?php
			endforeach; ?>

			<?php
			foreach ($stock_opname_batu1020 as $row) : ?>  
			<tr class="table-baris2">
				<td align="center"><?php echo $row['date'] = date('d-m-Y',strtotime($row['date']));;?></td>
				<td align="left"><?php echo $this->crud_global->GetField('produk',array('id'=>$row['material_id']),'nama_produk');?></td>
				<td align="center"><?php echo $this->crud_global->GetField('pmm_measures',array('id'=>$row['satuan']),'measure_name');?></td>
				<td align="right"><?= number_format($row['volume'],2,',','.'); ?></td>
				<td align="right"><?= number_format($row['harsat'],0,',','.'); ?></td>
				<td align="right"><?= number_format($row['nilai'],0,',','.'); ?></td>
			</tr>
			<?php
			endforeach; ?>

			<?php
			foreach ($stock_opname_batu2030 as $row) : ?>  
			<tr class="table-baris2">
				<td align="center"><?php echo $row['date'] = date('d-m-Y',strtotime($row['date']));;?></td>
				<td align="left"><?php echo $this->crud_global->GetField('produk',array('id'=>$row['material_id']),'nama_produk');?></td>
				<td align="center"><?php echo $this->crud_global->GetField('pmm_measures',array('id'=>$row['satuan']),'measure_name');?></td>
				<td align="right"><?= number_format($row['volume'],2,',','.'); ?></td>
				<td align="right"><?= number_format($row['harsat'],0,',','.'); ?></td>
				<td align="right"><?= number_format($row['nilai'],0,',','.'); ?></td>
			</tr>
			<?php
			endforeach; ?>

			<?php
			foreach ($stock_opname_solar as $row) : ?>  
			<tr class="table-baris2">
				<td align="center"><?php echo $row['date'] = date('d-m-Y',strtotime($row['date']));;?></td>
				<td align="left"><?php echo $this->crud_global->GetField('produk',array('id'=>$row['material_id']),'nama_produk');?></td>
				<td align="center"><?php echo $this->crud_global->GetField('pmm_measures',array('id'=>$row['satuan']),'measure_name');?></td>
				<td align="right"><?= number_format($row['volume'],2,',','.'); ?></td>
				<td align="right"><?= number_format($row['harsat'],0,',','.'); ?></td>
				<td align="right"><?= number_format($row['nilai'],0,',','.'); ?></td>
			</tr>
			<?php
			endforeach; ?>

			<tr class="table-total">
				<td align="right" colspan="5">TOTAL</td>
				<td align="right"><?php echo number_format($nilai_semen + $nilai_pasir + $nilai_batu1020 + $nilai_batu2030 + + $nilai_solar,0,',','.');?></td>
			</tr>
		</table>
		<br />
		<br />
		<table width="98%" border="0" cellpadding="0">
			<tr >
				<td width="10%"></td>
				<td width="80%">
					<table width="100%" border="0" cellpadding="2">
						<tr>
							<td align="center" colspan="2">
								Disetujui Oleh
							</td>
							<td align="center">
								Diperiksa Oleh
							</td>
							<td align="center">
								Dibuat Oleh
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
						</tr>
						<tr>
							<td align="center" >
								<b><u>Gervasius K. Limahekin</u><br />
								Ka. Plant</b>
							</td>
							<td align="center" >
								<b><br />
								Ass. Ka. Plant</b>
							</td>
							<td align="center">
								<b><br />
								M. Teknik</b>
							</td>
							<td align="center">
								<b><u>Agustinus Pakaenoni</u><br />
								Pj. Logistik</b>
							</td>
						</tr>
					</table>
				</td>
				<td width="10%"></td>
			</tr>
		</table>
		
	</body>
</html>