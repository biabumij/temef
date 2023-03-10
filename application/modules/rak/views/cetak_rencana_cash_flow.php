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
					<div style="display: block;font-weight: bold;font-size: 12px;">RENCANA CASH FLOW<br/>
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
                <th width="5%" align="center">NO.</th>
                <th width="35%" align="center">URAIAN</th>
				<th width="60%" align="right">NILAI</th>
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
				<td align="left">Biaya Bank</td>
				<td align="right"><?= number_format($rak['biaya_bank'],0,',','.'); ?></td>
			</tr>
			<tr class="table-baris1">
				<td align="center">4.</td>
				<td align="left">Overhead</td>
				<td align="right"><?= number_format($rak['overhead'],0,',','.'); ?></td>
			</tr>
			<tr class="table-baris1">
				<td align="center">5.</td>
				<td align="left">Termin</td>
				<td align="right"><?= number_format($rak['termin'],0,',','.'); ?></td>
			</tr>
			<tr class="table-baris1">
				<td align="center">6.</td>
				<td align="left">Biaya Persiapan</td>
				<td align="right"><?= number_format($rak['biaya_persiapan'],0,',','.'); ?></td>
			</tr>
		</table>
	</body>
</html>