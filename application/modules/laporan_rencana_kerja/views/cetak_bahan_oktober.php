<!DOCTYPE html>
<html>
	<head>
	  <title>KEBUTUHAN BAHAN BAKU</title>
	  <?= include 'lib.php'; ?>
	  
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
		<br />
		<br />
		<table width="98%" cellpadding="3">
			<tr>
				<td align="center"  width="100%">
					<div style="display: block;font-weight: bold;font-size: 11px;">KEBUTUHAN BAHAN BAKU</div>
					<div style="display: block;font-weight: bold;font-size: 11px;">DIVISI BETON  PROYEK BENDUNGAN TEMEF</div>
				    <div style="display: block;font-weight: bold;font-size: 11px;">PT. BIA BUMI JAYENDRA</div>
					<div style="display: block;font-weight: bold;font-size: 11px; text-transform: uppercase;">PERIODE : <?php echo str_replace($search, $replace, $subject);?></div>
				</td>
			</tr>
		</table>
		<br />
		<br />
		<br />
		<?php
		$data = array();
		
		$arr_date = $this->input->get('filter_date');
		$arr_filter_date = explode(' - ', $arr_date);
		$date1 = '';
		$date2 = '';

		if(count($arr_filter_date) == 2){
			$date1 	= date('Y-m-d',strtotime($arr_filter_date[0]));
			$date2 	= date('Y-m-d',strtotime($arr_filter_date[1]));
			$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]));
		}
		
		?>
		
		<table width="98%" border="0" cellpadding="3" border="0">
		<!-- TOTAL PEMAKAIAN KOMPOSISI -->

		<?php

		//K125
		$komposisi_125 = $this->db->select('pk.*')
		->from('pmm_agregat pk')
		->where("pk.mutu_beton = 2")
		->where('pk.status','PUBLISH')
		->order_by('pk.date_agregat','desc')->limit(1)
		->get()->row_array();

		$komposisi_125_produk_a = $komposisi_125['presentase_a'];
		$komposisi_125_produk_b = $komposisi_125['presentase_b'];
		$komposisi_125_produk_c = $komposisi_125['presentase_c'];
		$komposisi_125_produk_d = $komposisi_125['presentase_d'];

		//K225
		$komposisi_225 = $this->db->select('pk.*')
		->from('pmm_agregat pk')
		->where("pk.mutu_beton = 1")
		->where('pk.status','PUBLISH')
		->order_by('pk.date_agregat','desc')->limit(1)
		->get()->row_array();

		$komposisi_225_produk_a = $komposisi_225['presentase_a'];
		$komposisi_225_produk_b = $komposisi_225['presentase_b'];
		$komposisi_225_produk_c = $komposisi_225['presentase_c'];
		$komposisi_225_produk_d = $komposisi_225['presentase_d'];

		//K250
		$komposisi_250 = $this->db->select('pk.*')
		->from('pmm_agregat pk')
		->where("pk.mutu_beton = 3")
		->where('pk.status','PUBLISH')
		->order_by('pk.date_agregat','desc')->limit(1)
		->get()->row_array();

		$komposisi_250_produk_a = $komposisi_250['presentase_a'];
		$komposisi_250_produk_b = $komposisi_250['presentase_b'];
		$komposisi_250_produk_c = $komposisi_250['presentase_c'];
		$komposisi_250_produk_d = $komposisi_250['presentase_d'];

		//K250_18
		$komposisi_250_18 = $this->db->select('pk.*')
		->from('pmm_agregat pk')
		->where("pk.mutu_beton = 11")
		->where('pk.status','PUBLISH')
		->order_by('pk.date_agregat','desc')->limit(1)
		->get()->row_array();

		$komposisi_250_18_produk_a = $komposisi_250_18['presentase_a'];
		$komposisi_250_18_produk_b = $komposisi_250_18['presentase_b'];
		$komposisi_250_18_produk_c = $komposisi_250_18['presentase_c'];
		$komposisi_250_18_produk_d = $komposisi_250_18['presentase_d'];

		$rencana_kerja = $this->db->select('r.*')
		->from('rak r')
		->where("r.tanggal_rencana_kerja between '$date1' and '$date2'")
		->get()->row_array();
		
		$volume_rencana_kerja_produk_a = $rencana_kerja['vol_produk_a'];
		$volume_rencana_kerja_produk_b = $rencana_kerja['vol_produk_b'];
		$volume_rencana_kerja_produk_c = $rencana_kerja['vol_produk_c'];
		$volume_rencana_kerja_produk_d = $rencana_kerja['vol_produk_d'];

		//TOTAL K-125
		$total_semen_125 = $komposisi_125_produk_a * $volume_rencana_kerja_produk_a;
		$total_pasir_125 = $komposisi_125_produk_b * $volume_rencana_kerja_produk_a;
		$total_batu1020_125 = $komposisi_125_produk_c * $volume_rencana_kerja_produk_a;
		$total_batu2030_125 = $komposisi_125_produk_d * $volume_rencana_kerja_produk_a;

		$nilai_semen_125 = $total_semen_125 * $komposisi_125['price_a'];
		$nilai_pasir_125 = $total_pasir_125 * $komposisi_125['price_b'];
		$nilai_batu1020_125 = $total_batu1020_125 * $komposisi_125['price_c'];
		$nilai_batu2030_125 = $total_batu2030_125 * $komposisi_125['price_d'];

		$total_125 = $nilai_semen_125 + $nilai_pasir_125 + $nilai_batu1020_125 + $nilai_batu2030_125;

		//TOTAL K-225
		$total_semen_225 = $komposisi_225_produk_a * $volume_rencana_kerja_produk_b;
		$total_pasir_225 = $komposisi_225_produk_b * $volume_rencana_kerja_produk_b;
		$total_batu1020_225 = $komposisi_225_produk_c * $volume_rencana_kerja_produk_b;
		$total_batu2030_225 = $komposisi_225_produk_d * $volume_rencana_kerja_produk_b;

		$nilai_semen_225 = $total_semen_225 * $komposisi_225['price_a'];
		$nilai_pasir_225 = $total_pasir_225 * $komposisi_225['price_b'];
		$nilai_batu1020_225 = $total_batu1020_225 * $komposisi_225['price_c'];
		$nilai_batu2030_225 = $total_batu2030_225 * $komposisi_225['price_d'];

		$total_225 = $nilai_semen_225 + $nilai_pasir_225 + $nilai_batu1020_225 + $nilai_batu2030_225;

		//TOTAL K-250
		$total_semen_250 = $komposisi_250_produk_a * $volume_rencana_kerja_produk_c;
		$total_pasir_250 = $komposisi_250_produk_b * $volume_rencana_kerja_produk_c;
		$total_batu1020_250 = $komposisi_250_produk_c * $volume_rencana_kerja_produk_c;
		$total_batu2030_250 = $komposisi_250_produk_d * $volume_rencana_kerja_produk_c;

		$nilai_semen_250 = $total_semen_250 * $komposisi_250['price_a'];
		$nilai_pasir_250 = $total_pasir_250 * $komposisi_250['price_b'];
		$nilai_batu1020_250 = $total_batu1020_250 * $komposisi_250['price_c'];
		$nilai_batu2030_250 = $total_batu2030_250 * $komposisi_250['price_d'];

		$total_250 = $nilai_semen_250 + $nilai_pasir_250 + $nilai_batu1020_250 + $nilai_batu2030_250;

		//TOTAL K-250_18
		$total_semen_250_18 = $komposisi_250_18_produk_a * $volume_rencana_kerja_produk_d;
		$total_pasir_250_18 = $komposisi_250_18_produk_b * $volume_rencana_kerja_produk_d;
		$total_batu1020_250_18 = $komposisi_250_18_produk_c * $volume_rencana_kerja_produk_d;
		$total_batu2030_250_18 = $komposisi_250_18_produk_d * $volume_rencana_kerja_produk_d;

		$nilai_semen_250_18 = $total_semen_250_18 * $komposisi_250_18['price_a'];
		$nilai_pasir_250_18 = $total_pasir_250_18 * $komposisi_250_18['price_b'];
		$nilai_batu1020_250_18 = $total_batu1020_250_18 * $komposisi_250_18['price_c'];
		$nilai_batu2030_250_18 = $total_batu2030_250_18 * $komposisi_250_18['price_d'];

		$total_250_18 = $nilai_semen_250_18 + $nilai_pasir_250_18 + $nilai_batu1020_250_18 + $nilai_batu2030_250_18;

		//TOTAL ALL
		$total_all = $total_125 + $total_225 + $total_250 + $total_250_18;

		?>
			
			
			<tr class="table-judul">
				<th width="5%" align="center" rowspan="2">&nbsp;<br>NO.</th>
				<th width="40%" align="center" rowspan="2">&nbsp;<br>URAIAN</th>
				<th width="15%" align="center" rowspan="2">&nbsp;<br>SATUAN</th>
				<th width="40%" align="center" colspan="3">PEMAKAIAN</th>
	        </tr>
			<tr class="table-judul">
				<th align="center" width="10%">VOLUME</th>
				<th align="center" width="10%">HARGA</th>
				<th align="center" width="20%">NILAI</th>
	        </tr>
			<tr class="table-baris1">
	            <th align="left" colspan="12"><b>KEBUTUHAN BAHAN BAKU K-125 (10±2)</b></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">1</th>	
				<th align="left">Semen</th>
				<th align="center">Ton</th>
				<th align="center"><?php echo number_format($total_semen_125,2,',','.');?></th>
				<th align="right"><?php echo number_format($komposisi_125['price_a'],0,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_semen_125,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">2</th>	
				<th align="left">Pasir</th>
				<th align="center">M3</th>
				<th align="center"><?php echo number_format($total_pasir_125,2,',','.');?></th>
				<th align="right"><?php echo number_format($komposisi_125['price_b'],0,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_pasir_125,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">3</th>	
				<th align="left">Batu Split 10-20</th>
				<th align="center">M3</th>
				<th align="center"><?php echo number_format($total_batu1020_125,2,',','.');?></th>
				<th align="right"><?php echo number_format($komposisi_125['price_c'],0,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_batu1020_125,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">4</th>	
				<th align="left">Batu Split 20-30</th>
				<th align="center">M3</th>
				<th align="center"><?php echo number_format($total_batu2030_125,2,',','.');?></th>
				<th align="right"><?php echo number_format($komposisi_125['price_d'],0,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_batu2030_125,0,',','.');?></th>
	        </tr>
			<tr class="table-total">
	            <th align="right" colspan="5">TOTAL</th>
				<th align="right"><?php echo number_format($total_125,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
	            <th align="left" colspan="12"><b>KEBUTUHAN BAHAN BAKU K-225 (10±2)</b></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">1</th>	
				<th align="left">Semen</th>
				<th align="center">Ton</th>
				<th align="center"><?php echo number_format($total_semen_225,2,',','.');?></th>
				<th align="right"><?php echo number_format($komposisi_225['price_a'],0,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_semen_225,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">2</th>	
				<th align="left">Pasir</th>
				<th align="center">M3</th>
				<th align="center"><?php echo number_format($total_pasir_225,2,',','.');?></th>
				<th align="right"><?php echo number_format($komposisi_225['price_b'],0,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_pasir_225,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">3</th>	
				<th align="left">Batu Split 10-20</th>
				<th align="center">M3</th>
				<th align="center"><?php echo number_format($total_batu1020_225,2,',','.');?></th>
				<th align="right"><?php echo number_format($komposisi_225['price_c'],0,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_batu1020_225,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">4</th>	
				<th align="left">Batu Split 20-30</th>
				<th align="center">M3</th>
				<th align="center"><?php echo number_format($total_batu2030_225,2,',','.');?></th>
				<th align="right"><?php echo number_format($komposisi_225['price_d'],0,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_batu2030_225,0,',','.');?></th>
	        </tr>
			<tr class="table-total">
	            <th align="right" colspan="5">TOTAL</th>
				<th align="right"><?php echo number_format($total_225,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
	            <th align="left" colspan="12"><b>KEBUTUHAN BAHAN BAKU K-250 (10±2)</b></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">1</th>	
				<th align="left">Semen</th>
				<th align="center">Ton</th>
				<th align="center"><?php echo number_format($total_semen_250,2,',','.');?></th>
				<th align="right"><?php echo number_format($komposisi_250['price_a'],0,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_semen_250,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">2</th>	
				<th align="left">Pasir</th>
				<th align="center">M3</th>
				<th align="center"><?php echo number_format($total_pasir_250,2,',','.');?></th>
				<th align="right"><?php echo number_format($komposisi_250['price_b'],0,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_pasir_250,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">3</th>	
				<th align="left">Batu Split 10-20</th>
				<th align="center">M3</th>
				<th align="center"><?php echo number_format($total_batu1020_250,2,',','.');?></th>
				<th align="right"><?php echo number_format($komposisi_250['price_c'],0,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_batu1020_250,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">4</th>	
				<th align="left">Batu Split 20-30</th>
				<th align="center">M3</th>
				<th align="center"><?php echo number_format($total_batu2030_250,2,',','.');?></th>
				<th align="right"><?php echo number_format($komposisi_250['price_d'],0,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_batu2030_250,0,',','.');?></th>
	        </tr>
			<tr class="table-total">
	            <th align="right" colspan="5">TOTAL</th>
				<th align="right"><?php echo number_format($total_250,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
	            <th align="left" colspan="12"><b>KEBUTUHAN BAHAN BAKU K-250 (18±2)</b></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">1</th>	
				<th align="left">Semen</th>
				<th align="center">Ton</th>
				<th align="center"><?php echo number_format($total_semen_250_18,2,',','.');?></th>
				<th align="right"><?php echo number_format($komposisi_250_18['price_a'],0,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_semen_250_18,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">2</th>	
				<th align="left">Pasir</th>
				<th align="center">M3</th>
				<th align="center"><?php echo number_format($total_pasir_250_18,2,',','.');?></th>
				<th align="right"><?php echo number_format($komposisi_250_18['price_b'],0,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_pasir_250_18,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">3</th>	
				<th align="left">Batu Split 10-20</th>
				<th align="center">M3</th>
				<th align="center"><?php echo number_format($total_batu1020_250_18,2,',','.');?></th>
				<th align="right"><?php echo number_format($komposisi_250_18['price_c'],0,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_batu1020_250_18,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center">4</th>	
				<th align="left">Batu Split 20-30</th>
				<th align="center">M3</th>
				<th align="center"><?php echo number_format($total_batu2030_250_18,2,',','.');?></th>
				<th align="right"><?php echo number_format($komposisi_250_18['price_d'],0,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_batu2030_250_18,0,',','.');?></th>
	        </tr>
			<tr class="table-total">
	            <th align="right" colspan="5">TOTAL</th>
				<th align="right"><?php echo number_format($total_250_18,0,',','.');?></th>
	        </tr>
			<tr class="table-total">
	            <th align="right" colspan="5">GRAND TOTAL</th>
				<th align="right"><?php echo number_format($total_all,0,',','.');?></th>
	        </tr>
	    </table>
	</body>
</html>