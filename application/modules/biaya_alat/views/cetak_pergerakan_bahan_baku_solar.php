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
		 body {
			font-family: helvetica;
		}

		table.table-border-pojok-kiri, th.table-border-pojok-kiri, td.table-border-pojok-kiri {
			border-top: 1px solid black;
			border-bottom: 1px solid black;
			border-right: 1px solid #cccccc;
			border-left: 1px solid black;
		}

		table.table-border-pojok-tengah, th.table-border-pojok-tengah, td.table-border-pojok-tengah {
			border-top: 1px solid black;
			border-bottom: 1px solid black;
			border-right: 1px solid #cccccc;
		}

		table.table-border-pojok-kanan, th.table-border-pojok-kanan, td.table-border-pojok-kanan {
			border-top: 1px solid black;
			border-bottom: 1px solid black;
			border-right: 1px solid black;
		}

		table.table-border-spesial, th.table-border-spesial, td.table-border-spesial {
			border-left: 1px solid black;
			border-right: 1px solid black;
		}

		table.table-border-spesial-kiri, th.table-border-spesial-kiri, td.table-border-spesial-kiri {
			border-left: 1px solid black;
			border-top: 2px solid black;
			border-bottom: 2px solid black;
		}

		table.table-border-spesial-tengah, th.table-border-spesial-tengah, td.table-border-spesial-tengah {
			border-left: 1px solid #cccccc;
			border-right: 1px solid #cccccc;
			border-top: 2px solid black;
			border-bottom: 2px solid black;
		}

		table.table-border-spesial-kanan, th.table-border-spesial-kanan, td.table-border-spesial-kanan {
			border-left: 1px solid #cccccc;
			border-right: 1px solid black;
			border-top: 2px solid black;
			border-bottom: 2px solid black;
		}

		table tr.table-judul{
			border: 1px solid;
			background-color: #e69500;
			font-weight: bold;
			font-size: 7px;
			color: black;
		}
			
		table tr.table-baris1{
			background-color: none;
			font-size: 7px;
		}

		table tr.table-baris1-bold{
			background-color: none;
			font-size: 7px;
			font-weight: bold;
		}
			
		table tr.table-total{
			background-color: #FFFF00;
			font-weight: bold;
			font-size: 7px;
			color: black;
		}

		table tr.table-total2{
			background-color: #eeeeee;
			font-weight: bold;
			font-size: 7px;
			color: black;
		}
	  </style>

	</head>
	<body>
		<div align="center" style="display: block;font-weight: bold;font-size: 11px;">LAPORAN PERGERAKAN BAHAN BAKU (SOLAR)</div>
		<div align="center" style="display: block;font-weight: bold;font-size: 11px;">PROYEK BENDUNGAN TEMEF</div>
		<div align="center" style="display: block;font-weight: bold;font-size: 11px;">PT. BIA BUMI JAYENDRA</div>
		<div align="center" style="display: block;font-weight: bold;font-size: 11px; text-transform: uppercase;">PERIODE : <?php echo str_replace($search, $replace, $subject);?></div>
		<br /><br /><br />
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
			->where("(pp.date_hpp < '$date1')")
			->order_by('pp.date_hpp','desc')->limit(1)
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
			$total_harga_pembelian_solar_akhir = ($total_volume_pembelian_solar_akhir!=0)?($nilai_opening_balance_solar + $total_nilai_pembelian_solar) / $total_volume_pembelian_solar_akhir* 1:0;
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
			<th width="11%" align="center" rowspan="2" class="table-border-pojok-kiri">&nbsp;<br>URAIAN</th>
			<th width="8%" align="center" rowspan="2" class="table-border-pojok-tengah">&nbsp;<br>SATUAN</th>
			<th width="27%" align="center" colspan="3" class="table-border-pojok-tengah">MASUK</th>
			<th width="27%" align="center" colspan="3" class="table-border-pojok-tengah">KELUAR</th>
			<th width="27%" align="center" colspan="3" class="table-border-pojok-kanan">AKHIR</th>
		</tr>
		<tr class="table-judul">
			<th align="center" width="8%" class="table-border-pojok-kiri">VOLUME</th>
			<th align="center" width="8%" class="table-border-pojok-tengah">HARGA</th>
			<th align="center" width="11%" class="table-border-pojok-tengah">NILAI</th>
			<th align="center" width="8%" class="table-border-pojok-tengah">VOLUME</th>
			<th align="center" width="8%" class="table-border-pojok-tengah">HARGA</th>
			<th align="center" width="11%" class="table-border-pojok-tengah">NILAI</th>
			<th align="center" width="8%" class="table-border-pojok-tengah">VOLUME</th>
			<th align="center" width="8%" class="table-border-pojok-tengah">HARGA</th>
			<th align="center" width="11%" class="table-border-pojok-kanan">NILAI</th>
		</tr>
		<tr class="table-baris1">
			<th align="left" colspan="12" class="table-border-spesial"><b>SOLAR</b></th>
		</tr>
		<tr class="table-baris1">
			<th align="center" style="vertical-align:middle" class="table-border-pojok-kiri"><?php echo $date2_ago;?></th>
			<th align="left" colspan="7" class="table-border-pojok-tengah"><i>Opening Balance</i></th>
			<th align="center" class="table-border-pojok-tengah"><?php echo number_format($volume_opening_balance_solar,2,',','.');?></th>
			<th align="right" class="table-border-pojok-tengah"><?php echo number_format($harga_opening_balance_solar,0,',','.');?></th>
			<th align="right" class="table-border-pojok-kanan"><?php echo number_format($nilai_opening_balance_solar,0,',','.');?></th>
		</tr>
		<tr class="table-baris1">	
			<th align="left" class="table-border-pojok-kiri">BBM Solar</th>
			<th align="center" class="table-border-pojok-tengah">Liter</th>
			<th align="center" class="table-border-pojok-tengah"><?php echo number_format($total_volume_pembelian_solar,2,',','.');?></th>
			<th align="right" class="table-border-pojok-tengah"><?php echo number_format($total_harga_pembelian_solar,0,',','.');?></th>
			<th align="right" class="table-border-pojok-tengah"><?php echo number_format($total_nilai_pembelian_solar,0,',','.');?></th>
			<th align="center" class="table-border-pojok-tengah"><?php echo number_format($total_volume_pemakaian_solar,2,',','.');?></th>
			<th align="right" class="table-border-pojok-tengah"><?php echo number_format($total_harga_pemakaian_solar,0,',','.');?></th>
			<th align="right" class="table-border-pojok-tengah"><?php echo number_format($total_nilai_pemakaian_solar,0,',','.');?></th>
			<th align="center" class="table-border-pojok-tengah"><?php echo number_format($total_volume_stock_solar_akhir,2,',','.');?></th>
			<th align="right" class="table-border-pojok-tengah"><?php echo number_format($total_harga_stock_solar_akhir,0,',','.');?></th>
			<th align="right" class="table-border-pojok-kanan"><?php echo number_format($total_nilai_stock_solar_akhir,0,',','.');?></th>
		</tr>
		<tr class="table-total2">
			<th align="center" colspan="2" class="table-border-spesial-kiri">TOTAL</th>
			<th align="center" class="table-border-spesial-tengah"></th>
			<th align="right" class="table-border-spesial-tengah"></th>
			<th align="right" class="table-border-spesial-tengah"><?php echo number_format($total_nilai_pembelian,0,',','.');?></th>
			<th align="center" class="table-border-spesial-tengah"></th>
			<th align="right" class="table-border-spesial-tengah"></th>
			<th align="right" class="table-border-spesial-tengah"><?php echo number_format($total_nilai_pemakaian,0,',','.');?></th>
			<th align="center" class="table-border-spesial-tengah"></th>
			<th align="right" class="table-border-spesial-tengah"></th>
			<th align="right" class="table-border-spesial-kanan"><?php echo number_format($total_nilai_akhir,0,',','.');?></th>
		</tr>

	</table>
	<!--<br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
	<table width="98%" border="0" cellpadding="3">
		<tr>
			<th width="70%">
			</th>
			<th width="30%" align="right" style="margin-top:40px;">
				<table width="98%" border="1" cellpadding="2">
					<tr class="">
						<td align="right" height="50px">
						</td>
						<td align="right">
						</td>
						<td align="right">
						</td>
					</tr>
				</table>
			</th>
		</tr>
	</table>-->
	<br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
	<table width="98%">
			<tr >
				<td width="5%"></td>
				<td width="90%">
					<table width="100%" border="0" cellpadding="2">
						<tr>
							<td align="center">
								Disetujui Oleh
							</td>
							<td align="center">
								Diperiksa Oleh
							</td>
							<td align="center" >
								Dibuat Oleh
							</td>	
						</tr>
						<tr class="">
							<?php
								$create = $this->db->select('id, unit_head, logistik, admin')
								->from('akumulasi')
								->where("(date_akumulasi between '$start_date' and '$end_date')")
								->order_by('id','desc')->limit(1)
								->get()->row_array();

                                $this->db->select('g.admin_group_name, a.admin_ttd');
                                $this->db->join('tbl_admin_group g','a.admin_group_id = g.admin_group_id','left');
                                $this->db->where('a.admin_id',$create['unit_head']);
                                $unit_head = $this->db->get('tbl_admin a')->row_array();

								$this->db->select('g.admin_group_name, a.admin_ttd');
                                $this->db->join('tbl_admin_group g','a.admin_group_id = g.admin_group_id','left');
                                $this->db->where('a.admin_id',$create['logistik']);
                                $logistik = $this->db->get('tbl_admin a')->row_array();

								$this->db->select('g.admin_group_name, a.admin_ttd');
                                $this->db->join('tbl_admin_group g','a.admin_group_id = g.admin_group_id','left');
                                $this->db->where('a.admin_id',$create['admin']);
                                $admin = $this->db->get('tbl_admin a')->row_array();
                            ?>
							<td align="center" height="55px">
								<img src="<?= $unit_head['admin_ttd']?>" width="70px">
							</td>
							<td align="center">
								<img src="<?= $unit_head['admin_ttd']?>" width="70px">
							</td>
							<td align="center">
								<img src="<?= $logistik['admin_ttd']?>" width="70px">
								<img src="<?= $admin['admin_ttd']?>" width="20px">
							</td>
						</tr>
						<tr>
							<td align="center" >
								<b><u><?php echo $this->crud_global->GetField('tbl_admin',array('admin_id'=>$create['unit_head']),'admin_name');?></u><br />
								<?= $unit_head['admin_group_name']?></b>
							</td>
							<td align="center">
							<b><u><?php echo $this->crud_global->GetField('tbl_admin',array('admin_id'=>$create['unit_head']),'admin_name');?></u><br />
								M. Teknik</b>
							</td>
							<td align="center" >
								<b><u><?php echo $this->crud_global->GetField('tbl_admin',array('admin_id'=>$create['logistik']),'admin_name');?></u><br />
								<?= $logistik['admin_group_name']?></b>
							</td>
						</tr>
					</table>
				</td>
				<td width="5%"></td>
			</tr>
		</table>
</body>
</html>