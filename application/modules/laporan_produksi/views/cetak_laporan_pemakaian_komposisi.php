<!DOCTYPE html>
<html>
	<head>
	  <title>LAPORAN PEMAKAIAN KOMPOSISI BAHAN BAKU</title>
	  
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
					<div style="display: block;font-weight: bold;font-size: 11px;">LAPORAN PEMAKAIAN KOMPOSISI BAHAN BAKU</div>
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
		
		<?php

		$komposisi = $this->db->select('pp.date_production, pp.no_production, pp.convert_measure, pk.produk_a, pk.produk_b, pk.produk_c, pk.produk_d, pk.measure_a, pk.measure_b, pk.measure_c, pk.measure_d, SUM(pp.display_volume * pk.presentase_a) as volume_a, SUM(pp.display_volume * pk.presentase_b) as volume_b, SUM(pp.display_volume * pk.presentase_c) as volume_c, SUM(pp.display_volume * pk.presentase_d) as volume_d, pk.price_a, pk.price_b, pk.price_c, pk.price_d')
		->from('pmm_productions pp')
		->join('pmm_agregat pk', 'pp.komposisi_id = pk.id','left')
		->where("pp.date_production between '$date1' and '$date2'")
		->where('pp.status','PUBLISH')
		->get()->row_array();

		//file_put_contents("D:\\komposisi.txt", $this->db->last_query());

		$volume_a = $komposisi['volume_a'];
		$volume_b = $komposisi['volume_b'];
		$volume_c = $komposisi['volume_c'];
		$volume_d = $komposisi['volume_d'];

		$price_a = $komposisi['price_a'];
		$price_b = $komposisi['price_b'];
		$price_c = $komposisi['price_c'];
		$price_d = $komposisi['price_d'];

		$nilai_a = $volume_a * $price_a;
		$nilai_b = $volume_b * $price_b;
		$nilai_c = $volume_c * $price_c;
		$nilai_d = $volume_d * $price_d;

		$total_volume_komposisi = $volume_a + $volume_b + $volume_c + $volume_d;
		$total_nilai_komposisi = $nilai_a + $nilai_b + $nilai_c + $nilai_d;

		?>
			
		<tr class="table-judul">
			<th width="5%" align = "center"><br/>NO.</th>
			<th width="25%" align = "center"><br/>URAIAN</th>
			<th width="10%" align = "center"><br/>SATUAN</th>
			<th width="20%" align = "center">VOLUME</th>
			<th width="20%" align = "center">HARGA SATUAN</th>
			<th width="20%" align = "center">NILAI</th>
		</tr>
		<tr class="table-baris1">
			<th align = "center"style="vertical-align:middle">1</th>			
			<th align="left">Semen</th>
			<th align="center">Ton</th>
			<th align="center"><?php echo number_format($volume_a,2,',','.');?></th>
			<th align="right"><?php echo number_format($price_a,0,',','.');?></th>
			<th align="right"><?php echo number_format($nilai_a,0,',','.');?></th>
		</tr>
		<tr class="table-baris1">
			<th align = "center"style="vertical-align:middle">2</th>			
			<th align = "left">Pasir</th>
			<th align = "center">Ton</th>
			<th align="center"><?php echo number_format($volume_b,2,',','.');?></th>
			<th align="right"><?php echo number_format($price_b,0,',','.');?></th>
			<th align="right"><?php echo number_format($nilai_b,0,',','.');?></th>
		</tr>
		<tr class="table-baris1">
			<th align = "center"style="vertical-align:middle">3</th>			
			<th align = "left">Batu Split 1-2</th>
			<th align = "center">Ton</th>
			<th align="center"><?php echo number_format($volume_c,2,',','.');?></th>
			<th align="right"><?php echo number_format($price_c,0,',','.');?></th>
			<th align="right"><?php echo number_format($nilai_c,0,',','.');?></th>
		</tr>
		<tr class="table-baris1">
			<th align = "center"style="vertical-align:middle">4</th>			
			<th align = "left">Batu Split 2-3</th>
			<th align = "center">Ton</th>
			<th align="center"><?php echo number_format($volume_d,2,',','.');?></th>
			<th align="right"><?php echo number_format($price_d,0,',','.');?></th>
			<th align="right"><?php echo number_format($nilai_d,0,',','.');?></th>
		</tr>
		<tr class="table-total">		
			<th align = "right" colspan="5">TOTAL PEMAKAIAN BAHAN</th>
			<th align = "right"><?php echo number_format($total_nilai_komposisi,0,',','.');?></th>
		</tr>
	</table>
		
	</body>
</html>