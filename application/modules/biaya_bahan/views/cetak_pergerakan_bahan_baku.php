<!DOCTYPE html>
<html>
	<head>
	  <title>PERGERAKAN BAHAN BAKU</title>
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
					<div style="display: block;font-weight: bold;font-size: 11px;">LAPORAN PERGERAKAN BAHAN BAKU</div>
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
			
			//PEMBELIAN SEMEN AGO
			$pembelian_semen_ago = $this->db->select('
			p.nama_produk, 
			prm.display_measure as satuan, 
			SUM(prm.display_volume) as volume, 
			SUM(prm.display_price) / SUM(prm.display_volume) as harga, 
			SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt between '$date1_ago' and '$date2_ago'")
			->where("prm.material_id = 4")
			->group_by('prm.material_id')
			->get()->row_array();

			$total_volume_pembelian_semen_ago = $pembelian_semen_ago['volume'];
			$total_volume_pembelian_semen_akhir_ago  = $total_volume_pembelian_semen_ago;
			
			$stock_opname_semen_ago = $this->db->select('(cat.display_volume) as volume')
			->from('pmm_remaining_materials_cat cat ')
			->where("(cat.date < '$date1')")
			->where("cat.material_id = 4")
			->where("cat.status = 'PUBLISH'")
			->order_by('cat.date','desc')->limit(1)
			->get()->row_array();

			$total_volume_stock_semen_ago = $stock_opname_semen_ago['volume'];

			$harga_hpp_bahan_baku = $this->db->select('pp.date_hpp, pp.semen, pp.pasir, pp.batu1020, pp.batu2030')
			->from('hpp_bahan_baku pp')
			->where("(pp.date_hpp between '$date3_ago' and '$date2_ago')")
			->get()->row_array();
			
			$volume_opening_balance_semen = round($total_volume_stock_semen_ago,2);
			$harga_opening_balance_semen = $harga_hpp_bahan_baku['semen'];
			$nilai_opening_balance_semen = $volume_opening_balance_semen * $harga_opening_balance_semen;

			//PEMBELIAN PASIR AGO
			$pembelian_pasir_ago = $this->db->select('
			p.nama_produk, 
			prm.display_measure as satuan, 
			SUM(prm.display_volume) as volume, 
			SUM(prm.display_price) / SUM(prm.display_volume) as harga, 
			SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt between '$date1_ago' and '$date2_ago'")
			->where("prm.material_id = 5")
			->group_by('prm.material_id')
			->get()->row_array();

			$total_volume_pembelian_pasir_ago = $pembelian_pasir_ago['volume'];
			$total_volume_pembelian_pasir_akhir_ago  = $total_volume_pembelian_pasir_ago;
			
			$stock_opname_pasir_ago = $this->db->select('(cat.display_volume) as volume')
			->from('pmm_remaining_materials_cat cat ')
			->where("(cat.date < '$date1')")
			->where("cat.material_id = 5")
			->where("cat.status = 'PUBLISH'")
			->order_by('cat.date','desc')->limit(1)
			->get()->row_array();

			$total_volume_stock_pasir_ago = $stock_opname_pasir_ago['volume'];

			$volume_opening_balance_pasir = round($total_volume_stock_pasir_ago,2);
			$harga_opening_balance_pasir = $harga_hpp_bahan_baku['pasir'];
			$nilai_opening_balance_pasir = $volume_opening_balance_pasir * $harga_opening_balance_pasir;

			//PEMBELIAN BATU1020 AGO
			$pembelian_batu1020_ago = $this->db->select('
			p.nama_produk, 
			prm.display_measure as satuan, 
			SUM(prm.display_volume) as volume, 
			SUM(prm.display_price) / SUM(prm.display_volume) as harga, 
			SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt between '$date1_ago' and '$date2_ago'")
			->where("prm.material_id = 6")
			->group_by('prm.material_id')
			->get()->row_array();

			$total_volume_pembelian_batu1020_ago = $pembelian_batu1020_ago['volume'];
			$total_volume_pembelian_batu1020_akhir_ago  = $total_volume_pembelian_batu1020_ago;
			
			$stock_opname_batu1020_ago = $this->db->select('(cat.display_volume) as volume')
			->from('pmm_remaining_materials_cat cat ')
			->where("(cat.date < '$date1')")
			->where("cat.material_id = 6")
			->where("cat.status = 'PUBLISH'")
			->order_by('cat.date','desc')->limit(1)
			->get()->row_array();

			$total_volume_stock_batu1020_ago = $stock_opname_batu1020_ago['volume'];

			$volume_opening_balance_batu1020 = round($total_volume_stock_batu1020_ago,2);
			$harga_opening_balance_batu1020 = $harga_hpp_bahan_baku['batu1020'];
			$nilai_opening_balance_batu1020 = $volume_opening_balance_batu1020 * $harga_opening_balance_batu1020;

			//PEMBELIAN BATU2030 AGO
			$pembelian_batu2030_ago = $this->db->select('
			p.nama_produk, 
			prm.display_measure as satuan, 
			SUM(prm.display_volume) as volume, 
			SUM(prm.display_price) / SUM(prm.display_volume) as harga, 
			SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt between '$date1_ago' and '$date2_ago'")
			->where("prm.material_id = 7")
			->group_by('prm.material_id')
			->get()->row_array();

			$total_volume_pembelian_batu2030_ago = $pembelian_batu2030_ago['volume'];
			$total_volume_pembelian_batu2030_akhir_ago  = $total_volume_pembelian_batu2030_ago;
			
			$stock_opname_batu2030_ago = $this->db->select('(cat.display_volume) as volume')
			->from('pmm_remaining_materials_cat cat ')
			->where("(cat.date < '$date1')")
			->where("cat.material_id = 7")
			->where("cat.status = 'PUBLISH'")
			->order_by('cat.date','desc')->limit(1)
			->get()->row_array();

			$total_volume_stock_batu2030_ago = $stock_opname_batu2030_ago['volume'];
			
			$volume_opening_balance_batu2030 = round($total_volume_stock_batu2030_ago,2);
			$harga_opening_balance_batu2030 = $harga_hpp_bahan_baku['batu2030'];
			$nilai_opening_balance_batu2030 = $volume_opening_balance_batu2030 * $harga_opening_balance_batu2030;

			?>

			<!--- NOW --->

			<?php
			
			//PEMBELIAN SEMEN PCC
			$pembelian_semen = $this->db->select('
			p.nama_produk, 
			prm.display_measure as satuan, 
			SUM(prm.display_volume) as volume, 
			SUM(prm.display_price) / SUM(prm.display_volume) as harga, 
			SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("prm.material_id = 4")
			->group_by('prm.material_id')
			->get()->row_array();
			
			$total_volume_pembelian_semen = $pembelian_semen['volume'];
			$total_nilai_pembelian_semen =  $pembelian_semen['nilai'];
			$total_harga_pembelian_semen = ($total_volume_pembelian_semen!=0)?$total_nilai_pembelian_semen / $total_volume_pembelian_semen * 1:0;

			$total_volume_pembelian_semen_akhir  = $volume_opening_balance_semen + $total_volume_pembelian_semen;
			$total_harga_pembelian_semen_akhir = ($nilai_opening_balance_semen + $total_nilai_pembelian_semen) / $total_volume_pembelian_semen_akhir;
			$total_nilai_pembelian_semen_akhir =  $total_volume_pembelian_semen_akhir * $total_harga_pembelian_semen_akhir;

			$jasa_angkut_semen = $this->db->select('
			p.nama_produk, 
			prm.display_measure as satuan, 
			SUM(prm.display_volume) as volume, 
			SUM(prm.display_price) / SUM(prm.display_volume) as harga, 
			SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("prm.material_id = 18")
			->group_by('prm.material_id')
			->get()->row_array();

			$total_nilai_jasa_angkut = $jasa_angkut_semen['nilai'];
			$total_nilai_jasa_angkut_akhir = $total_nilai_jasa_angkut + $total_nilai_pembelian_semen_akhir;

			//PEMBELIAN SEMEN CONS
			$pembelian_semen_cons = $this->db->select('
			p.nama_produk, 
			prm.display_measure as satuan, 
			SUM(prm.display_volume) as volume, 
			SUM(prm.display_price) / SUM(prm.display_volume) as harga, 
			SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("prm.material_id = 19")
			->group_by('prm.material_id')
			->get()->row_array();

			$total_volume_pembelian_semen_cons = $pembelian_semen_cons['volume'];
			$total_nilai_pembelian_semen_cons =  $pembelian_semen_cons['nilai'];
			$total_harga_pembelian_semen_cons = ($total_volume_pembelian_semen_cons!=0)?$total_nilai_pembelian_semen_cons / $total_volume_pembelian_semen_cons * 1:0;

			$total_volume_pembelian_semen_cons_akhir  = $total_volume_pembelian_semen_akhir + $total_volume_pembelian_semen_cons;
			$total_harga_pembelian_semen_cons_akhir = ($total_nilai_pembelian_semen_akhir + $total_nilai_pembelian_semen_cons) / $total_volume_pembelian_semen_cons_akhir;
			$total_nilai_pembelian_semen_cons_akhir =  $total_volume_pembelian_semen_cons_akhir * $total_harga_pembelian_semen_cons_akhir;

			$jasa_angkut_semen_cons = $this->db->select('
			p.nama_produk, 
			prm.display_measure as satuan, 
			SUM(prm.display_volume) as volume, 
			SUM(prm.display_price) / SUM(prm.display_volume) as harga, 
			SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("prm.material_id = 21")
			->group_by('prm.material_id')
			->get()->row_array();

			$total_nilai_jasa_angkut_cons = $jasa_angkut_semen_cons['nilai'];
			$total_nilai_jasa_angkut_cons_akhir = $total_nilai_jasa_angkut_cons + $total_nilai_pembelian_semen_cons_akhir;

			//PEMBELIAN SEMEN OPC
			$pembelian_semen_opc = $this->db->select('
			p.nama_produk, 
			prm.display_measure as satuan, 
			SUM(prm.display_volume) as volume, 
			SUM(prm.display_price) / SUM(prm.display_volume) as harga, 
			SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("prm.material_id = 20")
			->group_by('prm.material_id')
			->get()->row_array();
		
			$total_volume_pembelian_semen_opc = $pembelian_semen_opc['volume'];
			$total_nilai_pembelian_semen_opc =  $pembelian_semen_opc['nilai'];
			$total_harga_pembelian_semen_opc = ($total_volume_pembelian_semen_opc!=0)?$total_nilai_pembelian_semen_opc / $total_volume_pembelian_semen_opc * 1:0;

			$total_volume_pembelian_semen_opc_akhir  = $total_volume_pembelian_semen_cons_akhir + $total_volume_pembelian_semen_opc;
			$total_harga_pembelian_semen_opc_akhir = ($total_nilai_pembelian_semen_cons_akhir + $total_nilai_pembelian_semen_opc) / $total_volume_pembelian_semen_opc_akhir;
			$total_nilai_pembelian_semen_opc_akhir =  $total_volume_pembelian_semen_opc_akhir * $total_harga_pembelian_semen_opc_akhir;

			$jasa_angkut_semen_opc = $this->db->select('
			p.nama_produk, 
			prm.display_measure as satuan, 
			SUM(prm.display_volume) as volume, 
			SUM(prm.display_price) / SUM(prm.display_volume) as harga, 
			SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("prm.material_id = 22")
			->group_by('prm.material_id')
			->get()->row_array();

			$total_nilai_jasa_angkut_opc = $jasa_angkut_semen_opc['nilai'];
			$total_nilai_jasa_angkut_opc_akhir = $total_nilai_jasa_angkut_opc + $total_nilai_pembelian_semen_opc_akhir;

			$total_volume_pembelian_semen_all = $total_volume_pembelian_semen + $total_volume_pembelian_semen_cons + $total_volume_pembelian_semen_opc;
			$total_nilai_pembelian_semen_all = $total_nilai_pembelian_semen + $total_nilai_pembelian_semen_cons + $total_nilai_pembelian_semen_opc +  $total_nilai_jasa_angkut + $total_nilai_jasa_angkut_cons + $total_nilai_jasa_angkut_opc;
			$total_harga_pembelian_semen_all = ($total_volume_pembelian_semen_all!=0)?$total_nilai_pembelian_semen_all / $total_volume_pembelian_semen_all * 1:0;

			$stock_opname_semen = $this->db->select('(cat.display_volume) as volume, `cat`.`price` as price')
			->from('pmm_remaining_materials_cat cat ')
			->where("cat.date between '$date1' and '$date2'")
			->where("cat.material_id = 4")
			->where("cat.status = 'PUBLISH'")
			->order_by('cat.date','desc')->limit(1)
			->get()->row_array();
			
			$hpp_bahan_baku = $this->db->select('pp.date_hpp, pp.semen')
			->from('hpp_bahan_baku pp')
			->where("(pp.date_hpp between '$date1' and '$date2')")
			->order_by('pp.date_hpp','desc')->limit(1)
			->get()->row_array();
			
			$total_volume_stock_semen_akhir = $stock_opname_semen['volume'];
			$price_stock_opname_semen =  $hpp_bahan_baku['semen'];

			$total_volume_pemakaian_semen = $total_volume_pembelian_semen_opc_akhir - $stock_opname_semen['volume'];

			$total_harga_stock_semen_akhir = round($price_stock_opname_semen,0);
			$total_nilai_stock_semen_akhir = $total_volume_stock_semen_akhir * $total_harga_stock_semen_akhir;

			$total_nilai_pemakaian_semen = ($nilai_opening_balance_semen + $total_nilai_pembelian_semen  + $total_nilai_jasa_angkut + $total_nilai_pembelian_semen_cons + $total_nilai_jasa_angkut_cons + $total_nilai_pembelian_semen_opc + $total_nilai_jasa_angkut_opc) - $total_nilai_stock_semen_akhir;
			$total_harga_pemakaian_semen = $total_nilai_pemakaian_semen / $total_volume_pemakaian_semen;

			//PEMBELIAN PASIR
			$pembelian_pasir = $this->db->select('
			p.nama_produk, 
			prm.display_measure as satuan, 
			SUM(prm.display_volume) as volume, 
			SUM(prm.display_price) / SUM(prm.display_volume) as harga, 
			SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("prm.material_id = 5")
			->group_by('prm.material_id')
			->get()->row_array();
			
			$total_volume_pembelian_pasir = $pembelian_pasir['volume'];
			$total_nilai_pembelian_pasir =  $pembelian_pasir['nilai'];
			$total_harga_pembelian_pasir = ($total_volume_pembelian_pasir!=0)?$total_nilai_pembelian_pasir / $total_volume_pembelian_pasir * 1:0;

			$total_volume_pembelian_pasir_akhir  = $volume_opening_balance_pasir + $total_volume_pembelian_pasir;
			$total_harga_pembelian_pasir_akhir = ($nilai_opening_balance_pasir + $total_nilai_pembelian_pasir) / $total_volume_pembelian_pasir_akhir;
			$total_nilai_pembelian_pasir_akhir =  $total_volume_pembelian_pasir_akhir * $total_harga_pembelian_pasir_akhir;
			
			$stock_opname_pasir = $this->db->select('(cat.display_volume) as volume')
			->from('pmm_remaining_materials_cat cat ')
			->where("cat.date between '$date1' and '$date2'")
			->where("cat.material_id = 5")
			->where("cat.status = 'PUBLISH'")
			->order_by('cat.date','desc')->limit(1)
			->get()->row_array();
			
			$total_volume_stock_pasir_akhir = $stock_opname_pasir['volume'];

			$total_volume_pemakaian_pasir = $total_volume_pembelian_pasir_akhir - $stock_opname_pasir['volume'];
			$total_harga_pemakaian_pasir = round($total_harga_pembelian_pasir_akhir,0);
			$total_nilai_pemakaian_pasir = $total_volume_pemakaian_pasir * $total_harga_pemakaian_pasir;

			$total_harga_stock_pasir_akhir = $total_harga_pemakaian_pasir;
			$total_nilai_stock_pasir_akhir = $total_volume_stock_pasir_akhir * $total_harga_stock_pasir_akhir;


			//PEMBELIAN BATU1020
			$pembelian_batu1020 = $this->db->select('
			p.nama_produk, 
			prm.display_measure as satuan, 
			SUM(prm.display_volume) as volume, 
			SUM(prm.display_price) / SUM(prm.display_volume) as harga, 
			SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("po.status in ('PUBLISH','CLOSED')")
			->where("prm.material_id = 6")
			->group_by('prm.material_id')
			->get()->row_array();
			
			$total_volume_pembelian_batu1020 = $pembelian_batu1020['volume'];
			$total_nilai_pembelian_batu1020 =  $pembelian_batu1020['nilai'];
			$total_harga_pembelian_batu1020 = ($total_volume_pembelian_batu1020!=0)?$total_nilai_pembelian_batu1020 / $total_volume_pembelian_batu1020 * 1:0;

			$total_volume_pembelian_batu1020_akhir  = $volume_opening_balance_batu1020 + $total_volume_pembelian_batu1020;
			$total_harga_pembelian_batu1020_akhir = ($nilai_opening_balance_batu1020 + $total_nilai_pembelian_batu1020) / $total_volume_pembelian_batu1020_akhir;
			$total_nilai_pembelian_batu1020_akhir =  $total_volume_pembelian_batu1020_akhir * $total_harga_pembelian_batu1020_akhir;			
			
			$stock_opname_batu1020 = $this->db->select('(cat.display_volume) as volume')
			->from('pmm_remaining_materials_cat cat ')
			->where("cat.date between '$date1' and '$date2'")
			->where("cat.material_id = 6")
			->where("cat.status = 'PUBLISH'")
			->order_by('cat.date','desc')->limit(1)
			->get()->row_array();
			
			$total_volume_stock_batu1020_akhir = $stock_opname_batu1020['volume'];
			
			$total_volume_pemakaian_batu1020 = $total_volume_pembelian_batu1020_akhir - $stock_opname_batu1020['volume'];
			$total_harga_pemakaian_batu1020 = round($total_harga_pembelian_batu1020_akhir,0);
			$total_nilai_pemakaian_batu1020 = $total_volume_pemakaian_batu1020 * $total_harga_pemakaian_batu1020;

			$total_harga_stock_batu1020_akhir = $total_harga_pemakaian_batu1020;
			$total_nilai_stock_batu1020_akhir = $total_volume_stock_batu1020_akhir * $total_harga_stock_batu1020_akhir;

			//PEMBELIAN BATU2030
			$pembelian_batu2030 = $this->db->select('
			p.nama_produk, 
			prm.display_measure as satuan, 
			SUM(prm.display_volume) as volume, 
			SUM(prm.display_price) / SUM(prm.display_volume) as harga, 
			SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("prm.material_id = 7")
			->group_by('prm.material_id')
			->get()->row_array();
			
			$total_volume_pembelian_batu2030 = $pembelian_batu2030['volume'];
			$total_nilai_pembelian_batu2030 =  $pembelian_batu2030['nilai'];
			$total_harga_pembelian_batu2030 = ($total_volume_pembelian_batu2030!=0)?$total_nilai_pembelian_batu2030 / $total_volume_pembelian_batu2030 * 1:0;

			$total_volume_pembelian_batu2030_akhir  = $volume_opening_balance_batu2030 + $total_volume_pembelian_batu2030;
			$total_harga_pembelian_batu2030_akhir = ($nilai_opening_balance_batu2030 + $total_nilai_pembelian_batu2030) / $total_volume_pembelian_batu2030_akhir;
			$total_nilai_pembelian_batu2030_akhir =  $total_volume_pembelian_batu2030_akhir * $total_harga_pembelian_batu2030_akhir;			
			
			$stock_opname_batu2030 = $this->db->select('(cat.display_volume) as volume')
			->from('pmm_remaining_materials_cat cat ')
			->where("cat.date between '$date1' and '$date2'")
			->where("cat.material_id = 7")
			->where("cat.status = 'PUBLISH'")
			->order_by('cat.date','desc')->limit(1)
			->get()->row_array();
			
			$total_volume_stock_batu2030_akhir = $stock_opname_batu2030['volume'];
			
			$total_volume_pemakaian_batu2030 = $total_volume_pembelian_batu2030_akhir - $stock_opname_batu2030['volume'];
			$total_harga_pemakaian_batu2030 = round($total_harga_pembelian_batu2030_akhir,0);
			$total_nilai_pemakaian_batu2030 = $total_volume_pemakaian_batu2030 * $total_harga_pemakaian_batu2030;

			$total_harga_stock_batu2030_akhir = $total_harga_pemakaian_batu2030;
			$total_nilai_stock_batu2030_akhir = $total_volume_stock_batu2030_akhir * $total_harga_stock_batu2030_akhir;

			//BAHAN BAKU
			$total_opening_balance_bahan_baku = $nilai_opening_balance_semen + $nilai_opening_balance_pasir + $nilai_opening_balance_batu1020 + $nilai_opening_balance_batu2030;

			//TOTAL
			$total_nilai_pembelian = $total_nilai_pembelian_semen_all + $total_nilai_pembelian_pasir + $total_nilai_pembelian_batu1020 + $total_nilai_pembelian_batu2030;
			$total_nilai_pemakaian = $total_nilai_pemakaian_semen + $total_nilai_pemakaian_pasir + $total_nilai_pemakaian_batu1020 + $total_nilai_pemakaian_batu2030;
			$total_nilai_akhir = $total_nilai_stock_semen_akhir + $total_nilai_stock_pasir_akhir + $total_nilai_stock_batu1020_akhir + $total_nilai_stock_batu2030_akhir;

	        ?>
			
			<tr class="table-judul">
				<th width="11%" align="center" rowspan="2">&nbsp;<br>URAIAN</th>
				<th width="8%" align="center" rowspan="2">&nbsp;<br>SATUAN</th>
				<th width="27%" align="center" colspan="3">MASUK</th>
				<th width="27%" align="center" colspan="3">KELUAR</th>
				<th width="27%" align="center" colspan="3">AKHIR</th>
	        </tr>
			<tr class="table-judul">
				<th align="center" width="8%">VOLUME</th>
				<th align="center" width="8%">HARGA</th>
				<th align="center" width="11%">NILAI</th>
				<th align="center" width="8%">VOLUME</th>
				<th align="center" width="8%">HARGA</th>
				<th align="center" width="11%">NILAI</th>
				<th align="center" width="8%">VOLUME</th>
				<th align="center" width="8%">HARGA</th>
				<th align="center" width="11%">NILAI</th>
	        </tr>
			<tr class="table-baris1">
	            <th align="left" colspan="12"><b>BAHAN BAKU</b></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center"style="vertical-align:middle"><?php echo $date2_ago;?></th>			
				<th align="left" colspan="9"><i>Opening Balance</i></th>
				<th align="right"><?php echo number_format($total_opening_balance_bahan_baku,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">	
				<th align="left">Semen</th>
				<th align="center">Ton</th>
				<th align="center"><?php echo number_format($total_volume_pembelian_semen_all,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_harga_pembelian_semen,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_nilai_pembelian_semen_all,0,',','.');?></th>
				<th align="center"><?php echo number_format($total_volume_pemakaian_semen,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_harga_pemakaian_semen,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_nilai_pemakaian_semen,0,',','.');?></th>
				<th align="center"><?php echo number_format($total_volume_stock_semen_akhir,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_harga_stock_semen_akhir,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_nilai_stock_semen_akhir,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="left">Pasir</th>
				<th align="center">M3</th>
				<th align="center"><?php echo number_format($total_volume_pembelian_pasir,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_harga_pembelian_pasir,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_nilai_pembelian_pasir,0,',','.');?></th>
				<th align="center"><?php echo number_format($total_volume_pemakaian_pasir,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_harga_pemakaian_pasir,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_nilai_pemakaian_pasir,0,',','.');?></th>
				<th align="center"><?php echo number_format($total_volume_stock_pasir_akhir,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_harga_stock_pasir_akhir,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_nilai_stock_pasir_akhir,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="left">Batu Split 10-20</th>
				<th align="center">M3</th>
				<th align="center"><?php echo number_format($total_volume_pembelian_batu1020,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_harga_pembelian_batu1020,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_nilai_pembelian_batu1020,0,',','.');?></th>
				<th align="center"><?php echo number_format($total_volume_pemakaian_batu1020,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_harga_pemakaian_batu1020,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_nilai_pemakaian_batu1020,0,',','.');?></th>
				<th align="center"><?php echo number_format($total_volume_stock_batu1020_akhir,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_harga_stock_batu1020_akhir,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_nilai_stock_batu1020_akhir,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="left">Batu Split 20-30</th>
				<th align="center">M3</th>
				<th align="center"><?php echo number_format($total_volume_pembelian_batu2030,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_harga_pembelian_batu2030,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_nilai_pembelian_batu2030,0,',','.');?></th>
				<th align="center"><?php echo number_format($total_volume_pemakaian_batu2030,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_harga_pemakaian_batu2030,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_nilai_pemakaian_batu2030,0,',','.');?></th>
				<th align="center"><?php echo number_format($total_volume_stock_batu2030_akhir,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_harga_stock_batu2030_akhir,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_nilai_stock_batu2030_akhir,0,',','.');?></th>
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