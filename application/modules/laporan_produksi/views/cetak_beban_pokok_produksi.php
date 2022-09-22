<!DOCTYPE html>
<html>
	<head>
	  <title>BEBAN POKOK PRODUKSI</title>
	  
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
		table tr.table-active{
			font-size: 8px;
			font-weight: bold;
			color: black;
		}
			
		table tr.table-active2{
			background-color: #A9A9A9;
			font-size: 8px;
			font-weight: bold;
		}
			
		table tr.table-active3{
			font-size: 8px;
		}
			
		table tr.table-active4{
			background-color: #e69500;
			font-weight: bold;
			font-size: 8px;
			color: black;
		}
		table tr.table-active5{
			background-color: #E8E8E8;
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
					<div style="display: block;font-weight: bold;font-size: 12px;">BEBAN POKOK PRODUKSI</div>
					<div style="display: block;font-weight: bold;font-size: 12px;">DIVISI BETON PROYEK BENDUNGAN TEMEF</div>
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
		
		<table width="98%" border="0" cellpadding="3">
		
			<!-- PENJUALAN -->

			<?php
			$penjualan = $this->db->select('p.nama, pp.client_id, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume, pp.convert_measure as measure')
			->from('pmm_productions pp')
			->join('penerima p', 'pp.client_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("pp.date_production between '$date1' and '$date2'")
			->where("pp.status = 'PUBLISH'")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.client_id")
			->get()->result_array();
			
			$total_volume = 0;
			$total_nilai = 0;

			foreach ($penjualan as $x){
				$total_volume += $x['volume'];
				$total_nilai += $x['price'];
			}

			$total_penjualan_all = 0;
			$total_penjualan_all = $total_volume;
			$total_nilai_penjualan_all = 0;
			$total_nilai_penjualan_all = $total_nilai;
			?>

			<!-- PERGERAKAN BAHAN BAKU -->
			
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
			
			<!-- HPP BIAYA -->

			<?php
			$hpp_semen = $total_nilai_pemakaian_semen / $total_penjualan_all;
			$hpp_pasir = $total_nilai_pemakaian_pasir / $total_penjualan_all;
			$hpp_batu1020 = $total_nilai_pemakaian_batu1020 / $total_penjualan_all;
			$hpp_batu2030 = $total_nilai_pemakaian_batu2030 / $total_penjualan_all;
			$total_hpp_biaya = round($hpp_semen + $hpp_pasir + $hpp_batu1020 + $hpp_batu2030,0);
			
			?>

			<!-- PERSENTASE BIAYA -->

			<?php
			$presentase_semen = ($hpp_semen / $total_hpp_biaya) * 100;
			$presentase_pasir = ($hpp_pasir / $total_hpp_biaya) * 100;
			$presentase_batu1020 = ($hpp_batu1020 / $total_hpp_biaya) * 100;
			$presentase_batu2030 = ($hpp_batu2030  / $total_hpp_biaya) * 100;

			$total_presentase_biaya = 0;
			?>

			<!-- PERALATAN -->

			<?php	
			$batching_plant = $this->db->select('SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("prm.material_id in (15)")
			->where("po.status in ('PUBLISH','CLOSED')")
			->get()->row_array();

			$batching_plant = $batching_plant['nilai'];

			$truck_mixer = $this->db->select('SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("prm.material_id in (12,13,14,23,24,25)")
			->where("po.status in ('PUBLISH','CLOSED')")
			->get()->row_array();

			$insentif_tm = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->where("pdb.akun = 220")
			->where("status = 'PAID'")
			->where("(tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$insentif_tm = $insentif_tm['total'];

			$truck_mixer = $truck_mixer['nilai'] + $insentif_tm;

			$wheel_loader = $this->db->select('SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("prm.material_id = 16")
			->where("po.status in ('PUBLISH','CLOSED')")
			->get()->row_array();

			$wheel_loader = $wheel_loader['nilai'];

			
			$akumulasi_bbm = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar_2 as total_nilai_keluar_2')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi between '$date1' and '$date2')")
			->get()->result_array();

			$total_akumulasi_bbm = 0;

			foreach ($akumulasi_bbm as $b){
				$total_akumulasi_bbm += $b['total_nilai_keluar_2'];
			}

			$total_nilai_bbm = $total_akumulasi_bbm;

			$bbm_solar = $total_nilai_bbm;



			$total_nilai_peralatan = round($batching_plant + $truck_mixer + $wheel_loader + $bbm_solar,0);
			?>

			<!-- HPP PERALATAN-->

			<?php
			$hpp_batching_plant = $batching_plant / $total_penjualan_all;
			$hpp_truck_mixer = $truck_mixer / $total_penjualan_all;
			$hpp_wheel_loader = $wheel_loader / $total_penjualan_all;
			$hpp_bbm_solar = $bbm_solar / $total_penjualan_all;
			$total_hpp_peralatan = round($hpp_batching_plant + $hpp_truck_mixer + $hpp_wheel_loader + $hpp_bbm_solar,0);
			
			?>

			<!-- PERSENTASE PERALATAN -->

			<?php
			$presentase_batching_plant = ($batching_plant / $total_nilai_peralatan) * 100;
			$presentase_truck_mixer = ($truck_mixer / $total_nilai_peralatan) * 100;
			$presentase_wheel_loader = ($wheel_loader / $total_nilai_peralatan) * 100;
			$presentase_bbm_solar = ($bbm_solar  / $total_nilai_peralatan) * 100;

			$total_presentase_peralatan = 0;	
			?>

			<!-- OVERHEAD -->

			<?php
			$overhead_biaya = $this->db->select('c.coa, sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("c.coa_category in ('15','16','17')")
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->group_by('c.id')
			->order_by('c.coa_number','asc')
			->get()->result_array();

			
			$total_overhead_biaya = 0;
			$total_hpp_overhead_biaya = 0;

			foreach ($overhead_biaya as $x){
				$total_overhead_biaya += $x['total'];
				$total_hpp_overhead_biaya += $x['total'] / $total_penjualan_all;
			}

			$total_overhead_biaya_all = 0;
			$total_overhead_biaya_all = $total_overhead_biaya;

			$total_hpp_overhead_biaya_all = 0;
			$total_hpp_overhead_biaya_all = $total_hpp_overhead_biaya;
			

			$overhead_jurnal = $this->db->select('c.coa, sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',15)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->group_by('c.id')
			->order_by('c.coa_number','asc')
			->get()->result_array();

			$total_overhead_jurnal = 0;
			$total_hpp_overhead_jurnal = 0;


			foreach ($overhead_jurnal as $x){
				$total_overhead_jurnal += $x['total'];
				$total_hpp_overhead_jurnal += $x['total'] / $total_penjualan_all;
				
			}

			$total_overhead_jurnal_all = 0;
			$total_overhead_jurnal_all = $total_overhead_jurnal;

			$total_hpp_overhead_jurnal_all = 0;
			$total_hpp_overhead_jurnal_all = $total_hpp_overhead_jurnal;

			$total_overhead_all = $total_overhead_biaya_all + $total_overhead_jurnal_all;
			$total_hpp_overhead_all = $total_hpp_overhead_biaya_all + $total_hpp_overhead_jurnal_all;
			
			?>

			<!-- DISKONTO -->

			<?php
			$diskonto = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 168")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$diskonto = $diskonto['total'];
			$hpp_diskonto = $diskonto / $total_penjualan_all;
			$presentase_diskonto = ($hpp_diskonto / $hpp_diskonto) * 100;
			?>


			<!-- PERSIAPAN -->
			
			<?php
			$persiapan_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 228")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$persiapan_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 228")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$persiapan = $persiapan_biaya['total'] + $persiapan_jurnal['total'];
			$hpp_persiapan = $persiapan / $total_penjualan_all;
			$presentase_persiapan = ($hpp_persiapan / $hpp_persiapan) * 100;
			?>

			<?php
			$total_beban_pokok_produksi = $total_nilai_pemakaian + $total_nilai_peralatan + $total_overhead_all + $diskonto + $persiapan;
			$hpp_beban_pokok_produksi = $total_beban_pokok_produksi / $total_penjualan_all;

			$presentase_total_biaya = ($total_nilai_pemakaian / $total_beban_pokok_produksi) * 100;
			$presentase_total_peralatan = ($total_nilai_peralatan / $total_beban_pokok_produksi) * 100;
			$presentase_total_overhead = ($total_overhead_all / $total_beban_pokok_produksi) * 100;
			$presentase_total_diskonto = ($diskonto / $total_beban_pokok_produksi) * 100;
			$presentase_total_persiapan = ($persiapan / $total_beban_pokok_produksi) * 100;

			$presentase_beban_pokok_produksi = $presentase_total_biaya + $presentase_total_peralatan + $presentase_total_overhead + $presentase_total_diskonto + $presentase_total_persiapan;
			$presentase_bpp_terhadap_pendapatan = ($total_beban_pokok_produksi / $total_nilai_penjualan_all) * 100;
			?>

		<tr class="table-active4">
			<th width="50%" align="center" colspan="3" rowspan="2" style="vertical-align:middle">&nbsp; <br />URAIAN</th>
			<th width="50%" align="center" colspan="3"><?php echo str_replace($search, $replace, $subject);?></th>
		</tr>
		<tr class="table-active4">
			<th width="15%" align="center">%</th>
			<th width="15%" align="center">HPP</th>
			<th width="20%" align="center">Nilai</th>
		</tr>
		<tr class="table-active5">
			<th align="center" colspan="3">VOLUME PRODUKSI (M3)</th>
			<th align="right" colspan="3"><?php echo number_format($total_penjualan_all,2,',','.');?></th>
		</tr>
		<tr class="table-active">
			<th align="left" colspan="3">BIAYA</th>
			<th align="left" colspan="3"></th>
		</tr>
		<tr class="table-active">
			<th align="left">1</th>
			<th align="left" colspan="2">Bahan Baku</th>
			<th align="left" colspan="3"></th>
		</tr>
		<tr class="table-active3">
			<th align="right" colspan="3">Semen</th>
			<th align="right"><?php echo number_format($presentase_semen,2,',','.');?> %</th>
			<th align="right"><?php echo number_format($hpp_semen,0,',','.');?></th>
			<th align="right"><?php echo number_format($total_nilai_pemakaian_semen,0,',','.');?></th>
		</tr>
		<tr class="table-active3">
			<th align="right" colspan="3">Pasir</th>
			<th align="right"><?php echo number_format($presentase_pasir,2,',','.');?> %</th>
			<th align="right"><?php echo number_format($hpp_pasir,0,',','.');?></th>
			<th align="right"><?php echo number_format($total_nilai_pemakaian_pasir,0,',','.');?></th>
		</tr>
		<tr class="table-active3">
			<th align="right" colspan="3">Batu Split 10-20</th>
			<th align="right"><?php echo number_format($presentase_batu1020,2,',','.');?> %</th>
			<th align="right"><?php echo number_format($hpp_batu1020,0,',','.');?></th>
			<th align="right"><?php echo number_format($total_nilai_pemakaian_batu1020,0,',','.');?></th>
		</tr>
		<tr class="table-active3">
			<th align="right" colspan="3">Batu Split 20-30</th>
			<th align="right"><?php echo number_format($presentase_batu2030,2,',','.');?> %</th>
			<th align="right"><?php echo number_format($hpp_batu2030,0,',','.');?></th>
			<th align="right"><?php echo number_format($total_nilai_pemakaian_batu2030,0,',','.');?></th>
		</tr>
		<tr class="table-active5">
			<th align="right" colspan="3">TOTAL</th>
			<th align="right"><?php echo number_format($presentase_total_biaya,2,',','.');?> %</th>
			<th align="right"><?php echo number_format($total_hpp_biaya,0,',','.');?></th>
			<th align="right"><?php echo number_format($total_nilai_pemakaian,0,',','.');?></th>
		</tr>
		<tr class="table-active">
			<th align="left">2</th>
			<th align="left" colspan="2">Peralatan</th>
			<th align="left" colspan="3"></th>
		</tr>
		<tr class="table-active3">
			<th align="right" colspan="3">Batching Plant</th>
			<th align="right"><?php echo number_format($presentase_batching_plant,2,',','.');?> %</th>
			<th align="right"><?php echo number_format($hpp_batching_plant,0,',','.');?></th>
			<th align="right"><?php echo number_format($batching_plant,0,',','.');?></th>
		</tr>
		<tr class="table-active3">
			<th align="right" colspan="3">Truck Mixer</th>
			<th align="right"><?php echo number_format($presentase_truck_mixer,2,',','.');?> %</th>
			<th align="right"><?php echo number_format($hpp_truck_mixer,0,',','.');?></th>
			<th align="right"><?php echo number_format($truck_mixer,0,',','.');?></th>
		</tr>
		<tr class="table-active3">
			<th align="right" colspan="3">Wheel Loader</th>
			<th align="right"><?php echo number_format($presentase_wheel_loader,2,',','.');?> %</th>
			<th align="right"><?php echo number_format($hpp_wheel_loader,0,',','.');?></th>
			<th align="right"><?php echo number_format($wheel_loader,0,',','.');?></th>
		</tr>
		<tr class="table-active3">
			<th align="right" colspan="3">BBM Solar</th>
			<th align="right"><?php echo number_format($presentase_bbm_solar,2,',','.');?> %</th>
			<th align="right"><?php echo number_format($hpp_bbm_solar,0,',','.');?></th>
			<th align="right"><?php echo number_format($bbm_solar,0,',','.');?></th>
		</tr>
		<tr class="table-active5">
			<th align="right" colspan="3">TOTAL</th>
			<th align="right"><?php echo number_format($presentase_total_peralatan,2,',','.');?> %</th>
			<th align="right"><?php echo number_format($total_hpp_peralatan,0,',','.');?></th>
			<th align="right"><?php echo number_format($total_nilai_peralatan,0,',','.');?></th>
		</tr>
		<tr class="table-active">
			<th align="left">3</th>
			<th align="left" colspan="2">Overhead</th>
			<th align="left" colspan="3"></th>
		</tr>
		<?php
		$hpp_overhead_biaya = 0;
		$presentase_overhead_biaya = 0;
		?>
		<?php foreach ($overhead_biaya as $x) {
			$hpp_overhead_biaya = $x['total'] / $total_penjualan_all;
			$presentase_overhead_biaya = ($hpp_overhead_biaya / $total_hpp_overhead_all) * 100;
		?>
		<tr class="table-active3">
			<th align="right" colspan="3"><?= $x['coa'];?> (BIAYA)</th>
			<th align="right"><?php echo number_format($presentase_overhead_biaya,2,',','.');?> %</th>
			<th align="right"><?php echo number_format($hpp_overhead_biaya,0,',','.');?></th>
			<th align="right"><?php echo number_format($x['total'],0,',','.');?></th>
		</tr>
		<?php
		}
		?>
		<?php
		$hpp_overhead_jurnal = 0;
		$presentase_overhead_jurnal = 0;
		?>
		<?php foreach ($overhead_jurnal as $x) {
		$hpp_overhead_jurnal = $x['total'] / $total_penjualan_all;
		$presentase_overhead_jurnal =  ($hpp_overhead_jurnal / $total_hpp_overhead_all) * 100;
		?>
		<tr class="table-active3">
			<th align="right" colspan="3"><?= $x['coa'];?> (JURNAL)</th>
			<th align="right"><?php echo number_format($presentase_overhead_jurnal,2,',','.');?> %</th>
			<th align="right"><?php echo number_format($hpp_overhead_jurnal,0,',','.');?></th>
			<th align="right"><?php echo number_format($x['total'],0,',','.');?></th>
		</tr>
		<?php
		}
		?>
		<tr class="table-active5">
			<th align="right" colspan="3">TOTAL</th>
			<th align="right"><?php echo number_format($presentase_total_overhead,2,',','.');?> %</th>
			<th align="right"><?php echo number_format($total_hpp_overhead_all,0,',','.');?></th>
			<th align="right"><?php echo number_format($total_overhead_all,0,',','.');?></th>
		</tr>
		<tr class="table-active">
			<th align="left">4</th>
			<th align="left" colspan="2">Biaya Bank</th>
			<th align="left" colspan="3"></th>
		</tr>
		<tr class="table-active3">
			<th align="right" colspan="3">Diskonto</th>
			<th align="right"><?php echo number_format($presentase_diskonto,2,',','.');?> %</th>
			<th align="right"><?php echo number_format($hpp_diskonto,0,',','.');?></th>
			<th align="right"><?php echo number_format($diskonto,0,',','.');?></th>
		</tr>
		<tr class="table-active5">
			<th align="right" colspan="3">TOTAL</th>
			<th align="right"><?php echo number_format($presentase_total_diskonto,2,',','.');?> %</th>
			<th align="right"><?php echo number_format($hpp_diskonto,0,',','.');?></th>
			<th align="right"><?php echo number_format($diskonto,0,',','.');?></th>
		</tr>
		<tr class="table-active">
			<th align="left">5</th>
			<th align="left" colspan="2">Persiapan</th>
			<th align="left" colspan="3"></th>
		</tr>
		<tr class="table-active3">
			<th align="right" colspan="3">Persiapan</th>
			<th align="right"><?php echo number_format($presentase_persiapan,2,',','.');?> %</th>
			<th align="right"><?php echo number_format($hpp_persiapan,0,',','.');?></th>
			<th align="right"><?php echo number_format($persiapan,0,',','.');?></th>
		</tr>
		<tr class="table-active5">
			<th align="right" colspan="3">TOTAL</th>
			<th align="right"><?php echo number_format($presentase_total_persiapan,2,',','.');?> %</th>
			<th align="right"><?php echo number_format($hpp_persiapan,0,',','.');?></th>
			<th align="right"><?php echo number_format($persiapan,0,',','.');?></th>
		</tr>
		<tr class="table-active5">
			<th align="right" colspan="3">TOTAL BEBAN POKOK PRODUKSI</th>
			<th align="right"><?php echo number_format($presentase_beban_pokok_produksi,0,',','.');?> %</th>
			<th align="right"><?php echo number_format($hpp_beban_pokok_produksi,0,',','.');?></th>
			<th align="right"><?php echo number_format($total_beban_pokok_produksi,0,',','.');?></th>
		</tr>
		<tr class="table-active5">
			<th align="right" colspan="3"><i>% BPP Terhadap Pendapatan Usaha</i></th>
			<th align="right"></th>
			<th align="right"></th>
			<th align="right"><?php echo number_format($presentase_bpp_terhadap_pendapatan,2,',','.');?> %</th>
		</tr>
	</table>
</body>
</html>