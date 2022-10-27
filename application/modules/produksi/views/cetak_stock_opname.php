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
                <th width="5%" align="center">NO.</th>
                <th width="15%" align="center">TANGGAL</th>
                <th width="20%" align="center">URAIAN</th>
                <th width="10%" align="center">SATUAN</th>
				<th width="10%" align="center">VOLUME</th>
				<th width="15%" align="center">HARGA SATUAN</th>
				<th width="15%" align="center">NILAI</th>
				<th width="10%" align="center">CATATAN</th>
            </tr>
			<?php
			$awal = date('Y-m-d',strtotime($date1));
			$akhir = date('Y-m-d',strtotime($date2));

			$harga_hpp_bahan_baku = $this->db->select('pp.date_hpp, pp.semen, pp.pasir, pp.batu1020, pp.batu2030, pp.solar')
			->from('hpp_bahan_baku pp')
			->where("(pp.date_hpp between '$awal' and '$akhir')")
			->get()->row_array();

			$stock_opname_semen = $this->db->select('SUM(cat.display_volume) as volume, cat.measure as satuan')
			->from('pmm_remaining_materials_cat cat ')
			->where("cat.date between '$awal' and '$akhir'")
			->where("cat.material_id = 4")
			->where("cat.status = 'PUBLISH'")
			->get()->row_array();

			$stock_opname_pasir = $this->db->select('SUM(cat.display_volume) as volume, cat.measure as satuan')
			->from('pmm_remaining_materials_cat cat ')
			->where("cat.date between '$awal' and '$akhir'")
			->where("cat.material_id = 5")
			->where("cat.status = 'PUBLISH'")
			->get()->row_array();

			$stock_opname_batu1020 = $this->db->select('SUM(cat.display_volume) as volume, cat.measure as satuan')
			->from('pmm_remaining_materials_cat cat ')
			->where("cat.date between '$awal' and '$akhir'")
			->where("cat.material_id = 6")
			->where("cat.status = 'PUBLISH'")
			->get()->row_array();

			$stock_opname_batu2030 = $this->db->select('SUM(cat.display_volume) as volume, cat.measure as satuan')
			->from('pmm_remaining_materials_cat cat ')
			->where("cat.date between '$awal' and '$akhir'")
			->where("cat.material_id = 7")
			->where("cat.status = 'PUBLISH'")
			->get()->row_array();

			$stock_opname_solar = $this->db->select('SUM(cat.display_volume) as volume, cat.measure as satuan')
			->from('pmm_remaining_materials_cat cat ')
			->where("cat.date between '$awal' and '$akhir'")
			->where("cat.material_id = 8")
			->where("cat.status = 'PUBLISH'")
			->get()->row_array();

			$nilai_stok_semen = $stock_opname_semen['volume'] * $harga_hpp_bahan_baku['semen'];
			$nilai_stok_pasir = $stock_opname_pasir['volume'] * $harga_hpp_bahan_baku['pasir'];
			$nilai_stok_batu1020 = $stock_opname_batu1020['volume'] * $harga_hpp_bahan_baku['batu1020'];
			$nilai_stok_batu2030 = $stock_opname_batu2030['volume'] * $harga_hpp_bahan_baku['batu2030'];
			$nilai_stok_solar = $stock_opname_solar['volume'] * $harga_hpp_bahan_baku['solar'];

			$nilai_stok_all = $nilai_stok_semen + $nilai_stok_pasir + $nilai_stok_batu1020 + $nilai_stok_batu2030 + $nilai_stok_solar;

			?>
			<tr class="table-baris2">
				<td align="center">1.</td>
				<td align="center"><?php echo $date2;?></td>
				<td align="left">Semen</td>
				<td align="center"><?= $this->crud_global->GetField('pmm_measures',array('id'=>$stock_opname_semen['satuan']),'measure_name');?></td>
				<td align="right"><?php echo number_format($stock_opname_semen['volume'],2,',','.');?></td>
				<td align="right"><?php echo number_format($harga_hpp_bahan_baku['semen'],0,',','.');?></td>
				<td align="right"><?php echo number_format($nilai_stok_semen,0,',','.');?></td>
				<td align="left"></td>
			</tr>
			<tr class="table-baris2">
				<td align="center">2.</td>
				<td align="center"><?php echo $date2;?></td>
				<td align="left">Pasir</td>
				<td align="center"><?= $this->crud_global->GetField('pmm_measures',array('id'=>$stock_opname_pasir['satuan']),'measure_name');?></td>
				<td align="right"><?php echo number_format($stock_opname_pasir['volume'],2,',','.');?></td>
				<td align="right"><?php echo number_format($harga_hpp_bahan_baku['pasir'],0,',','.');?></td>
				<td align="right"><?php echo number_format($nilai_stok_pasir,0,',','.');?></td>
				<td align="left"></td>
			</tr>
			<tr class="table-baris2">
				<td align="center">3.</td>
				<td align="center"><?php echo $date2;?></td>
				<td align="left">Batu Split 1020</td>
				<td align="center"><?= $this->crud_global->GetField('pmm_measures',array('id'=>$stock_opname_batu1020['satuan']),'measure_name');?></td>
				<td align="right"><?php echo number_format($stock_opname_batu1020['volume'],2,',','.');?></td>
				<td align="right"><?php echo number_format($harga_hpp_bahan_baku['batu1020'],0,',','.');?></td>
				<td align="right"><?php echo number_format($nilai_stok_batu1020,0,',','.');?></td>
				<td align="left"></td>
			</tr>
			<tr class="table-baris2">
				<td align="center">4.</td>
				<td align="center"><?php echo $date2;?></td>
				<td align="left">Batu Split 2030</td>
				<td align="center"><?= $this->crud_global->GetField('pmm_measures',array('id'=>$stock_opname_batu2030['satuan']),'measure_name');?></td>
				<td align="right"><?php echo number_format($stock_opname_batu2030['volume'],2,',','.');?></td>
				<td align="right"><?php echo number_format($harga_hpp_bahan_baku['batu2030'],0,',','.');?></td>
				<td align="right"><?php echo number_format($nilai_stok_batu2030,0,',','.');?></td>
				<td align="left"></td>
			</tr>
			<tr class="table-baris2">
				<td align="center">5.</td>
				<td align="center"><?php echo $date2;?></td>
				<td align="left">BBM Solar</td>
				<td align="center"><?= $this->crud_global->GetField('pmm_measures',array('id'=>$stock_opname_solar['satuan']),'measure_name');?></td>
				<td align="right"><?php echo number_format($stock_opname_solar['volume'],2,',','.');?></td>
				<td align="right"><?php echo number_format($harga_hpp_bahan_baku['solar'],0,',','.');?></td>
				<td align="right"><?php echo number_format($nilai_stok_solar,0,',','.');?></td>
				<td align="left"></td>
			</tr>
			<tr class="table-total">
				<td align="right" colspan="6">TOTAL</td>
				<td align="right"><?php echo number_format($nilai_stok_all,0,',','.');?></td>
				<td align="left"></td>
			</tr>

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