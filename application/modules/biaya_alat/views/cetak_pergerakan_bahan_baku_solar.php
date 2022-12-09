<!DOCTYPE html>
<html>
	<head>
	  <title>PERGERAKAN BAHAN BAKU</title>
	  
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
					<div style="display: block;font-weight: bold;font-size: 11px;">LAPORAN PERGERAKAN BAHAN BAKU (SOLAR)</div>
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
		
		<!--- OPENING BALANCE --->
			
		<?php
			
		$date1_ago = date('2020-01-01');
		$date2_ago = date('Y-m-d', strtotime('-1 days', strtotime($date1)));
		$date3_ago = date('Y-m-d', strtotime('-1 months', strtotime($date1)));

		$harga_hpp_bahan_baku = $this->db->select('pp.date_hpp, pp.solar')
		->from('hpp_bahan_baku pp')
		->where("(pp.date_hpp between '$date3_ago' and '$date2_ago')")
		->get()->row_array();

		//PEMBELIAN SOLAR AGO
		$pembelian_solar_ago = $this->db->select('
		p.nama_produk, 
		prm.display_measure as satuan, 
		SUM(prm.display_volume) as volume, 
		SUM(prm.display_price) / SUM(prm.display_volume) as harga, 
		SUM(prm.display_price) as nilai')
		->from('pmm_receipt_material prm')
		->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
		->join('produk p', 'prm.material_id = p.id','left')
		->where("prm.date_receipt between '$date1_ago' and '$date2_ago'")
		->where("prm.material_id = 8")
		->group_by('prm.material_id')
		->get()->row_array();

		$total_volume_pembelian_solar_ago = $pembelian_solar_ago['volume'];
		$total_volume_pembelian_solar_akhir_ago  = $total_volume_pembelian_solar_ago;
		
		$stock_opname_solar_ago = $this->db->select('(cat.display_volume) as volume')
		->from('pmm_remaining_materials_cat cat ')
		->where("(cat.date < '$date1')")
		->where("cat.material_id = 8")
		->where("cat.status = 'PUBLISH'")
		->order_by('cat.date','desc')->limit(1)
		->get()->row_array();

		$total_volume_stock_solar_ago = $stock_opname_solar_ago['volume'];
		
		$volume_opening_balance_solar = round($total_volume_stock_solar_ago,2);
		$harga_opening_balance_solar = $harga_hpp_bahan_baku['solar'];
		$nilai_opening_balance_solar = $volume_opening_balance_solar * $harga_opening_balance_solar ;

		?>

		<!--- NOW --->

		<?php

		//PEMBELIAN SOLAR
		$pembelian_solar = $this->db->select('
		p.nama_produk, 
		prm.display_measure as satuan, 
		SUM(prm.display_volume) as volume, 
		SUM(prm.display_price) / SUM(prm.display_volume) as harga, 
		SUM(prm.display_price) as nilai')
		->from('pmm_receipt_material prm')
		->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
		->join('produk p', 'prm.material_id = p.id','left')
		->where("prm.date_receipt between '$date1' and '$date2'")
		->where("prm.material_id = 8")
		->group_by('prm.material_id')
		->get()->row_array();
		
		$total_volume_pembelian_solar = $pembelian_solar['volume'];
		$total_nilai_pembelian_solar =  $pembelian_solar['nilai'];
		$total_harga_pembelian_solar = ($total_volume_pembelian_solar!=0)?$total_nilai_pembelian_solar / $total_volume_pembelian_solar * 1:0;

		$total_volume_pembelian_solar_akhir  = $volume_opening_balance_solar + $total_volume_pembelian_solar;
		$total_harga_pembelian_solar_akhir = ($nilai_opening_balance_solar + $total_nilai_pembelian_solar) / $total_volume_pembelian_solar_akhir;
		$total_nilai_pembelian_solar_akhir =  $total_volume_pembelian_solar_akhir * $total_harga_pembelian_solar_akhir;			
		
		$stock_opname_solar = $this->db->select('(cat.display_volume) as volume')
		->from('pmm_remaining_materials_cat cat ')
		->where("cat.date between '$date1' and '$date2'")
		->where("cat.material_id = 8")
		->where("cat.status = 'PUBLISH'")
		->order_by('cat.date','desc')->limit(1)
		->get()->row_array();
		
		$hpp_bahan_baku = $this->db->select('pp.date_hpp, pp.solar')
		->from('hpp_bahan_baku pp')
		->where("(pp.date_hpp between '$date1' and '$date2')")
		->order_by('pp.date_hpp','desc')->limit(1)
		->get()->row_array();
		
		$total_volume_stock_solar_akhir = $stock_opname_solar['volume'];
		$price_stock_opname_solar =  $hpp_bahan_baku['solar'];

		$total_volume_pemakaian_solar = $total_volume_pembelian_solar_akhir - $stock_opname_solar['volume'];

		$total_harga_stock_solar_akhir = round($price_stock_opname_solar,0);
		$total_nilai_stock_solar_akhir = $total_volume_stock_solar_akhir * $total_harga_stock_solar_akhir;

		$total_nilai_pemakaian_solar = ($nilai_opening_balance_solar + $total_nilai_pembelian_solar) - $total_nilai_stock_solar_akhir;
		//$total_harga_pemakaian_solar = ($total_volume_pemakaian_solar!=0)?$total_nilai_pemakaian_solar / $total_volume_pemakaian_solar * 1:0;
		$total_harga_pemakaian_solar = $total_harga_stock_solar_akhir;

		//TOTAL
		$total_nilai_pembelian = $total_nilai_pembelian_solar;
		$total_nilai_pemakaian = $total_nilai_pemakaian_solar;
		$total_nilai_akhir = $total_nilai_stock_solar_akhir;

		?>
		
		<tr class="table-judul">
			<th class="garis_kanan" width="11%" align="center" rowspan="2">&nbsp;<br>URAIAN</th>
			<th class="garis_kanan" width="8%" align="center" rowspan="2">&nbsp;<br>SATUAN</th>
			<th class="garis_kanan" width="27%" align="center" colspan="3">MASUK</th>
			<th class="garis_kanan" width="27%" align="center" colspan="3">KELUAR</th>
			<th width="27%" align="center" colspan="3">AKHIR</th>
		</tr>
		<tr class="table-judul">
			<th align="center" width="8%">VOLUME</th>
			<th align="center" width="8%">HARGA</th>
			<th class="garis_kanan" align="center" width="11%">NILAI</th>
			<th align="center" width="8%">VOLUME</th>
			<th align="center" width="8%">HARGA</th>
			<th class="garis_kanan" align="center" width="11%">NILAI</th>
			<th align="center" width="8%">VOLUME</th>
			<th align="center" width="8%">HARGA</th>
			<th align="center" width="11%">NILAI</th>
		</tr>
		<tr class="table-baris1">
			<th align="left" colspan="12"><b>ALAT</b></th>
		</tr>
		<tr class="table-baris1">
			<th align="center" style="vertical-align:middle"><?php echo $date2_ago;?></th>
			<th align="left" colspan="7"><i>Opening Balance</i></th>
			<th align="center"><?php echo number_format($volume_opening_balance_solar,2,',','.');?></th>
			<th align="right"><?php echo number_format($harga_opening_balance_solar,0,',','.');?></th>
			<th align="right"><?php echo number_format($nilai_opening_balance_solar,0,',','.');?></th>
		</tr>
		<tr class="table-baris1">	
			<th align="left">BBM Solar</th>
			<th align="center">Liter</th>
			<th align="center"><?php echo number_format($total_volume_pembelian_solar,2,',','.');?></th>
			<th align="right"><?php echo number_format($total_harga_pembelian_solar,0,',','.');?></th>
			<th align="right"><?php echo number_format($total_nilai_pembelian_solar,0,',','.');?></th>
			<th align="center"><?php echo number_format($total_volume_pemakaian_solar,2,',','.');?></th>
			<th align="right"><?php echo number_format($total_harga_pemakaian_solar,0,',','.');?></th>
			<th align="right"><?php echo number_format($total_nilai_pemakaian_solar,0,',','.');?></th>
			<th align="center"><?php echo number_format($total_volume_stock_solar_akhir,2,',','.');?></th>
			<th align="right"><?php echo number_format($total_harga_stock_solar_akhir,0,',','.');?></th>
			<th align="right"><?php echo number_format($total_nilai_stock_solar_akhir,0,',','.');?></th>
		</tr>
		<tr class="table-total">
			<th align="center" colspan="2">TOTAL</th>
			<th align="center"></th>
			<th align="right"></th>
			<th align="right"><?php echo number_format($total_nilai_pembelian,0,',','.');?></th>
			<th align="center"></th>
			<th align="right"></th>
			<th align="right"><?php echo number_format($total_nilai_pemakaian,0,',','.');?></th>
			<th align="center"></th>
			<th align="right"></th>
			<th align="right"><?php echo number_format($total_nilai_akhir,0,',','.');?></th>
		</tr>

	</table>
	<br />
	<br />
	<br />
	<table width="98%" border="0" cellpadding="0">
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
						<tr class="">
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
								<b><u>Gervasius K. Limahekin</u><br />
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
								<b><u>Agustinus Pakaenoni</u><br />
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