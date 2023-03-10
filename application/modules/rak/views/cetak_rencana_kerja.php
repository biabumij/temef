<!DOCTYPE html>
<html>
	<head>
	  <?= include 'lib.php'; ?>

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
		<table width="100%" border="0" cellpadding="3">
			<tr>
				<td align="center">
					<div style="display: block;font-weight: bold;font-size: 12px;">RENCANA KERJA<br/>
					DIVISI BETON PROYEK BENDUNGAN TEMEF<br/>
					PT. BIA BUMI JAYENDRA<br/></div>
				</td>
			</tr>
		</table>
		<br />
		<br />
		<table width="100%" border="0">
			<?php
			$tanggal = $rak['tanggal_rencana_kerja'];
			$date = date('Y-m-d',strtotime($tanggal));
			?>
			<?php
			function tgl_indo($date){
				$bulan = array (
					1 =>   'Januari',
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
				$pecahkan = explode('-', $date);
				
				// variabel pecahkan 0 = tanggal
				// variabel pecahkan 1 = bulan
				// variabel pecahkan 2 = tahun
			
				return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
				
			}
			?>
			<tr>
				<th width="20%">Tanggal</th>
				<th width="10px">:</th>
				<th width="50%" align="left"><?= tgl_indo(date($date)); ?></th>
			</tr>
		</table>
		<br />
		<br />
		<table cellpadding="5" width="98%">
			<tr class="table-judul">
				<?php
					$total = 0;
					?>
					<?php
					$total = $rak['vol_produk_a'] + $rak['vol_produk_b'] + $rak['vol_produk_c'] + $rak['vol_produk_d'];
				?>
                <th width="5%" align="center">NO.</th>
                <th width="35%" align="center">URAIAN</th>
				<th width="30%" align="right">VOLUME</th>
				<th width="30%" align="center">SATUAN</th>
            </tr>
			<tr class="table-baris1">
				<td align="center">1.</td>
				<td align="left">Beton K 125 (10±2)</td>
				<td align="right"><?= number_format($rak['vol_produk_a'],2,',','.'); ?></td>
				<td align="center">M3</td>
			</tr>
			<tr class="table-baris1">
				<td align="center">2.</td>
				<td align="left">Beton K 225 (10±2)</td>
				<td align="right"><?= number_format($rak['vol_produk_b'],2,',','.'); ?></td>
				<td align="center">M3</td>
			</tr>
			<tr class="table-baris1">
				<td align="center">3.</td>
				<td align="left">Beton K 250 (10±2)</td>
				<td align="right"><?= number_format($rak['vol_produk_c'],2,',','.'); ?></td>
				<td align="center">M3</td>
			</tr>
			<tr class="table-baris1">
				<td align="center">4.</td>
				<td align="left">Beton K 250 (18±2)</td>
				<td align="right"><?= number_format($rak['vol_produk_d'],2,',','.'); ?></td>
				<td align="center">M3</td>
			</tr>
			<tr class="table-total">
				<td align="right" colspan="2">TOTAL VOLUME</td>
				<td align="right"><?= number_format($total,2,',','.'); ?></td>
				<td align="center">M3</td>
			</tr>
		</table>
		<br />
		<br />
		<table cellpadding="5" width="98%">
			<tr class="table-judul">
				<th width="5%" align="center">NO.</th>
				<th width="50%" align="center">URAIAN</th>
				<th width="45%" align="right">NILAI</th>
			</tr>
			<tr class="table-baris1">
				<td align="center">1.</td>
				<td align="left">Biaya Bahan</td>
				<td align="right"><?= number_format($rak['biaya_bahan'],0,',','.'); ?></td>
			</tr>
			<tr class="table-baris1">
				<td align="center">2.</td>
				<td align="left">Biaya Alat</td>
				<td align="right"><?= number_format($rak['biaya_alat'],0,',','.'); ?></td>
			</tr>
			<tr class="table-baris1">
				<td align="center">3.</td>
				<td align="left">BUA</td>
				<td align="right"><?= number_format($rak['overhead'],0,',','.'); ?></td>
			</tr>
			<tr class="table-baris1">
				<td align="center">4.</td>
				<td align="left">Biaya Bank</td>
				<td align="right"><?= number_format($rak['biaya_bank'],0,',','.'); ?></td>
			</tr>
		</table>
		<br />
		<br />
		<table cellpadding="5" width="98%">
			<tr class="table-judul">
				<th width="5%" align="center">NO.</th>
				<th width="15%" align="center">URAIAN</th>
				<th width="20%" align="center">REKANAN</th>
				<th width="15%" align="center">VOLUME</th>
				<th width="15%" align="center">SATUAN</th>
				<th width="15%" align="center">HARGA SATUAN</th>
				<th width="15%" align="center">NILAI</th>
			</tr>
			<?php

			$tanggal_rencana_kerja = date('Y-m-d', strtotime($rak['tanggal_rencana_kerja']));
	
			$komposisi_125 = $this->db->select('(r.vol_produk_a * pk.presentase_a) as komposisi_semen_125, (vol_produk_a * pk.presentase_b) as komposisi_pasir_125, (vol_produk_a * pk.presentase_c) as komposisi_batu1020_125, (vol_produk_a * pk.presentase_d) as komposisi_batu2030_125')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_125 = pk.id','left')
			->where("r.tanggal_rencana_kerja = '$tanggal_rencana_kerja'")
			->get()->result_array();

			$total_volume_semen_125 = 0;
			$total_volume_pasir_125 = 0;
			$total_volume_batu1020_125 = 0;
			$total_volume_batu2030_125 = 0;

			foreach ($komposisi_125 as $x){
				$total_volume_semen_125 = $x['komposisi_semen_125'];
				$total_volume_pasir_125 = $x['komposisi_pasir_125'];
				$total_volume_batu1020_125 = $x['komposisi_batu1020_125'];
				$total_volume_batu2030_125 = $x['komposisi_batu2030_125'];
			}

			$komposisi_225 = $this->db->select('(r.vol_produk_b * pk.presentase_a) as komposisi_semen_225, (vol_produk_b * pk.presentase_b) as komposisi_pasir_225, (vol_produk_b * pk.presentase_c) as komposisi_batu1020_225, (vol_produk_b * pk.presentase_d) as komposisi_batu2030_225')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_225 = pk.id','left')
			->where("r.tanggal_rencana_kerja = '$tanggal_rencana_kerja'")
			->get()->result_array();

			$total_volume_semen_225 = 0;
			$total_volume_pasir_225 = 0;
			$total_volume_batu1020_225 = 0;
			$total_volume_batu2030_225 = 0;

			foreach ($komposisi_225 as $x){
				$total_volume_semen_225 = $x['komposisi_semen_225'];
				$total_volume_pasir_225 = $x['komposisi_pasir_225'];
				$total_volume_batu1020_225 = $x['komposisi_batu1020_225'];
				$total_volume_batu2030_225 = $x['komposisi_batu2030_225'];
			}

			$komposisi_250 = $this->db->select('(r.vol_produk_c * pk.presentase_a) as komposisi_semen_250, (vol_produk_c * pk.presentase_b) as komposisi_pasir_250, (vol_produk_c * pk.presentase_c) as komposisi_batu1020_250, (vol_produk_c * pk.presentase_d) as komposisi_batu2030_250')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250 = pk.id','left')
			->where("r.tanggal_rencana_kerja = '$tanggal_rencana_kerja'")
			->get()->result_array();

			$total_volume_semen_250 = 0;
			$total_volume_pasir_250 = 0;
			$total_volume_batu1020_250 = 0;
			$total_volume_batu2030_250 = 0;

			foreach ($komposisi_250_1 as $x){
				$total_volume_semen_250 = $x['komposisi_semen_250'];
				$total_volume_pasir_250 = $x['komposisi_pasir_250'];
				$total_volume_batu1020_250 = $x['komposisi_batu1020_250'];
				$total_volume_batu2030_250 = $x['komposisi_batu2030_250'];
			}

			$komposisi_250_2 = $this->db->select('(r.vol_produk_d * pk.presentase_a) as komposisi_semen_250_2, (vol_produk_d * pk.presentase_b) as komposisi_pasir_250_2, (vol_produk_d * pk.presentase_c) as komposisi_batu1020_250_2, (vol_produk_d * pk.presentase_d) as komposisi_batu2030_250_2')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250_2 = pk.id','left')
			->where("r.tanggal_rencana_kerja = '$tanggal_rencana_kerja'")
			->get()->result_array();

			$total_volume_semen_250_2 = 0;
			$total_volume_pasir_250_2 = 0;
			$total_volume_batu1020_250_2 = 0;
			$total_volume_batu2030_250_2 = 0;

			foreach ($komposisi_250_2 as $x){
				$total_volume_semen_250_2 = $x['komposisi_semen_250_2'];
				$total_volume_pasir_250_2 = $x['komposisi_pasir_250_2'];
				$total_volume_batu1020_250_2 = $x['komposisi_batu1020_250_2'];
				$total_volume_batu2030_250_2 = $x['komposisi_batu2030_250_2'];
			}

			$total_volume_semen = $total_volume_semen_125 + $total_volume_semen_225 + $total_volume_semen_250 + $total_volume_semen_250_2;
			$total_volume_pasir = $total_volume_pasir_125 + $total_volume_pasir_225 + $total_volume_pasir_250 + $total_volume_pasir_250_2;
			$total_volume_batu1020 = $total_volume_batu1020_125 + $total_volume_batu1020_225 + $total_volume_batu1020_250 + $total_volume_batu1020_250_2;
			$total_volume_batu2030 = $total_volume_batu2030_125 + $total_volume_batu2030_225 + $total_volume_batu2030_250 + $total_volume_batu2030_250_2;

			$volume_produksi = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
			->from('rak r')
			->where("r.tanggal_rencana_kerja = '$tanggal_rencana_kerja'")
			->get()->row_array();

			$volume_produksi_produk_a = $volume_produksi['vol_produk_a'];
			$volume_produksi_produk_b = $volume_produksi['vol_produk_b'];
			$volume_produksi_produk_c = $volume_produksi['vol_produk_c'];
			$volume_produksi_produk_d = $volume_produksi['vol_produk_d'];

			$total_produksi_volume = $volume_produksi_produk_a + $volume_produksi_produk_b + $volume_produksi_produk_c + $volume_produksi_produk_d;

			$rap_solar = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->order_by('rap.id','desc')->limit(1)
			->get()->row_array();
			$total_volume_solar = $total_produksi_volume * $rap_solar['vol_bbm_solar'];

			?>
			<tr class="table-baris1">
				<th align="center">1.</th>	
				<th align="left">Semen</th>
				<th align="left"><?= $this->crud_global->GetField('penerima',array('id'=>$rak['supplier_id_semen']),'nama');?></th>
				<th align="center"><?php echo number_format($total_volume_semen,2,',','.');?></th>
				<th align="center">Ton</th>
				<th align="right"><?php echo number_format($rak['harga_semen'],0,',','.');?></th>
				<th align="right"><?php echo number_format($total_volume_semen * $rak['harga_semen'],0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">2.</th>	
				<th align="left">Pasir</th>
				<th align="left"><?= $this->crud_global->GetField('penerima',array('id'=>$rak['supplier_id_pasir']),'nama');?></th>
				<th align="center"><?php echo number_format($total_volume_pasir,2,',','.');?></th>
				<th align="center">Ton</th>
				<th align="right"><?php echo number_format($rak['harga_pasir'],0,',','.');?></th>
				<th align="right"><?php echo number_format($total_volume_pasir * $rak['harga_pasir'],0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">3.</th>	
				<th align="left">Batu Split 10-20</th>
				<th align="left"><?= $this->crud_global->GetField('penerima',array('id'=>$rak['supplier_id_batu1020']),'nama');?></th>
				<th align="center"><?php echo number_format($total_volume_batu1020,2,',','.');?></th>
				<th align="center">Ton</th>
				<th align="right"><?php echo number_format($rak['harga_batu1020'],0,',','.');?></th>
				<th align="right"><?php echo number_format($total_volume_batu1020 * $rak['harga_batu1020'],0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">4.</th>	
				<th align="left">Batu Split 20-30</th>
				<th align="left"><?= $this->crud_global->GetField('penerima',array('id'=>$rak['supplier_id_batu2030']),'nama');?></th>
				<th align="center"><?php echo number_format($total_volume_batu2030,2,',','.');?></th>
				<th align="center">Ton</th>
				<th align="right"><?php echo number_format($rak['harga_batu2030'],0,',','.');?></th>
				<th align="right"><?php echo number_format($total_volume_batu2030 * $rak['harga_batu2030'],0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">5.</th>	
				<th align="left">BBM Solar</th>
				<th align="left"><?= $this->crud_global->GetField('penerima',array('id'=>$rak['supplier_id_solar']),'nama');?></th>
				<th align="center"><?php echo number_format($total_volume_solar,2,',','.');?></th>
				<th align="center">Liter</th>
				<th align="right"><?php echo number_format($rak['harga_solar'],0,',','.');?></th>
				<th align="right"><?php echo number_format($total_volume_solar * $rak['harga_solar'],0,',','.');?></th>
	        </tr>
			<?php
			$total = ($total_volume_semen * $rak['harga_semen']) + ($total_volume_pasir * $rak['harga_pasir']) + ($total_volume_batu1020 * $rak['harga_batu1020']) + ($total_volume_batu2030 * $rak['harga_batu2030']) + ($total_volume_solar * $rak['harga_solar']);
			?>
			<tr class="table-total">	
				<th align="right" colspan="6">TOTAL</th>
				<th align="right"><?php echo number_format($total,0,',','.');?></th>
	        </tr>
		</table>
	</body>
</html>