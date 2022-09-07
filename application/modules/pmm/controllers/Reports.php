<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends CI_Controller {

	public function __construct()
	{
        parent::__construct();
        // Your own constructor code
        $this->load->model(array('admin/m_admin','crud_global','m_themes','pages/m_pages','menu/m_menu','admin_access/m_admin_access','DB_model','member_back/m_member_back','m_member','pmm_model','admin/Templates'));
        $this->load->model('pmm_reports');
        $this->load->library('enkrip');
		$this->load->library('filter');
		$this->load->library('waktu');
		$this->load->library('session');
		$this->m_admin->check_login();
	}	


	function report_production_table()
	{
		$data = array();
		$client_id = $this->input->post('client_id');
		$start_date = false;
		$end_date = false;
		$date = $this->input->post('filter_date');
		if(!empty($date)){
			$arr_date = explode(' - ',$date);
			$start_date = date('Y-m-d',strtotime($arr_date[0]));
			$end_date = date('Y-m-d',strtotime($arr_date[1]));
		}

		$products = $this->db->select('id,product')->get_where('pmm_product',array('status'=>'PUBLISH'))->result_array();

		foreach ($products as $key => $row) {
			$arr['no'] = $key + 1;
			$arr['product'] = $row['product'];
			$arr['planning'] = $this->pmm_model->GetPlanningProd($row['id'],$start_date,$end_date,$client_id);
			$arr['real'] = $this->pmm_model->GetRealProd($row['id'],$start_date,$end_date,$client_id);
			$data[] = $arr;
		}

		echo json_encode(array('data'=>$data));	
	}
	
	public function pergerakan_bahan_baku($arr_date)
	{
		$data = array();
		
		$arr_date = $this->input->post('filter_date');
		$arr_filter_date = explode(' - ', $arr_date);
		$date1 = '';
		$date2 = '';

		if(count($arr_filter_date) == 2){
			$date1 	= date('Y-m-d',strtotime($arr_filter_date[0]));
			$date2 	= date('Y-m-d',strtotime($arr_filter_date[1]));
			$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]));
		}
		
		?>
		
		<table class="table table-bordered" width="100%">
			<style type="text/css">
				table tr.table-active{
					background-color: #F0F0F0;
					font-size: 12px;
					font-weight: bold;
					color: black;
				}
					
				table tr.table-active2{
					background-color: #E8E8E8;
					font-size: 12px;
					font-weight: bold;
				}
					
				table tr.table-active3{
					font-size: 12px;
					background-color: #F0F0F0;
				}
					
				table tr.table-active4{
					background-color: #e69500;
					font-weight: bold;
					font-size: 12px;
					color: black;
				}
				table tr.table-active5{
					background-color: #cccccc;
					font-size: 12px;
					font-weight: bold;
					color: black;
				}
				table tr.table-activeago1{
					background-color: #ffd966;
					font-weight: bold;
					font-size: 12px;
					color: black;
				}
				table tr.table-activeopening{
					background-color: #2986cc;
					font-weight: bold;
					font-size: 12px;
					color: black;
				}
				blink {
				-webkit-animation: 2s linear infinite kedip; /* for Safari 4.0 - 8.0 */
				animation: 2s linear infinite kedip;
				}
				/* for Safari 4.0 - 8.0 */
				@-webkit-keyframes kedip { 
				0% {
					visibility: hidden;
				}
				50% {
					visibility: hidden;
				}
				100% {
					visibility: visible;
				}
				}
				@keyframes kedip {
				0% {
					visibility: hidden;
				}
				50% {
					visibility: hidden;
				}
				100% {
					visibility: visible;
				}
				}
			</style>
	        <tr class="table-active2">
	            <th colspan="3">Periode</th>
	            <th class="text-center" colspan="9"><?php echo $filter_date;?></th>
	        </tr>
			
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
			
			<tr class="table-active4">
				<th width="30%" class="text-center" rowspan="2" style="vertical-align:middle">TANGGAL</th>
				<th width="20%" class="text-center" rowspan="2" style="vertical-align:middle">URAIAN</th>
				<th width="10%" class="text-center" rowspan="2" style="vertical-align:middle">SATUAN</th>
				<th width="20%" class="text-center" colspan="3">MASUK</th>
				<th width="20%" class="text-center" colspan="3">KELUAR</th>
				<th width="20%" class="text-center" colspan="3">AKHIR</th>
	        </tr>
			<tr class="table-active4">
				<th class="text-center">VOLUME</th>
				<th class="text-center">HARGA</th>
				<th class="text-center">NILAI</th>
				<th class="text-center">VOLUME</th>
				<th class="text-center">HARGA</th>
				<th class="text-center">NILAI</th>
				<th class="text-center">VOLUME</th>
				<th class="text-center">HARGA</th>
				<th class="text-center">NILAI</th>
	        </tr>
			<tr class="table-active2">
	            <th class="text-center" colspan="12">SEMEN</th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center" style="vertical-align:middle"><?php echo $date2_ago;?></th>
	            <th class="text-left" colspan="8"><i>Opening Balance</i></th>
				<th class="text-center"><?php echo number_format($volume_opening_balance_semen,2,',','.');?></th>
				<th class="text-center"><?php echo number_format($harga_opening_balance_semen,0,',','.');?></th>
				<th class="text-center"><?php echo number_format($nilai_opening_balance_semen,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
	            <th class="text-center"style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
				<th class="text-left"><i>Pembelian (PCC)</i></th>
				<th class="text-center">Ton</th>
				<th class="text-center"><?php echo number_format($total_volume_pembelian_semen,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pembelian_semen,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pembelian_semen,0,',','.');?></th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"></th>
				<th class="text-center"><?php echo number_format($total_volume_pembelian_semen_akhir,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pembelian_semen_akhir,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pembelian_semen_akhir,0,',','.');?></th>		
	        </tr>
			<tr class="table-active3">
	            <th class="text-center"style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
				<th class="text-left"><i>Jasa Angkut Semen (PCC)</i></th>
				<th class="text-center">Ton</th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($total_nilai_jasa_angkut,0,',','.');?></th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"></th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($total_nilai_jasa_angkut_akhir,0,',','.');?></th>		
	        </tr>
			<tr class="table-active3">
	            <th class="text-center"style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
				<th class="text-left"><i>Pembelian (CONS)</i></th>
				<th class="text-center">Ton</th>
				<th class="text-center"><?php echo number_format($total_volume_pembelian_semen_cons,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pembelian_semen_cons,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pembelian_semen_cons,0,',','.');?></th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"></th>
				<th class="text-center"><?php echo number_format($total_volume_pembelian_semen_cons_akhir,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pembelian_semen_cons_akhir,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pembelian_semen_cons_akhir,0,',','.');?></th>		
	        </tr>
			<tr class="table-active3">
	            <th class="text-center"style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
				<th class="text-left"><i>Jasa Angkut Semen (CONS)</i></th>
				<th class="text-center">Ton</th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($total_nilai_jasa_angkut_cons,0,',','.');?></th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"></th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($total_nilai_jasa_angkut_cons_akhir,0,',','.');?></th>		
	        </tr>
			<tr class="table-active3">
	            <th class="text-center"style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
				<th class="text-left"><i>Pembelian (OPC)</i></th>
				<th class="text-center">Ton</th>
				<th class="text-center"><?php echo number_format($total_volume_pembelian_semen_opc,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pembelian_semen_opc,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pembelian_semen_opc,0,',','.');?></th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"></th>
				<th class="text-center"><?php echo number_format($total_volume_pembelian_semen_opc_akhir,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pembelian_semen_opc_akhir,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pembelian_semen_opc_akhir,0,',','.');?></th>		
	        </tr>
			<tr class="table-active3">
	            <th class="text-center"style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
				<th class="text-left"><i>Jasa Angkut Semen (OPC)</i></th>
				<th class="text-center">Ton</th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($total_nilai_jasa_angkut_opc,0,',','.');?></th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"></th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($total_nilai_jasa_angkut_opc_akhir,0,',','.');?></th>		
	        </tr>
			<tr class="table-active3">
				<th class="text-center"style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>			
				<th class="text-left"><i>Produksi</i></th>
				<th class="text-center">Ton</th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"></th>
				<th class="text-center"><?php echo number_format($total_volume_pemakaian_semen,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pemakaian_semen,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pemakaian_semen,0,',','.');?></th>
				<th class="text-center"><?php echo number_format($total_volume_stock_semen_akhir,2,',','.');?></th>
				<th class="text-right" style='background-color:red; color:white'><blink><?php echo number_format($total_harga_stock_semen_akhir,0,',','.');?></blink></th>
				<th class="text-right"><?php echo number_format($total_nilai_stock_semen_akhir,0,',','.');?></th>
	        </tr>
			<tr class="table-active2">
	            <th class="text-center" colspan="12">PASIR</th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center" style="vertical-align:middle"><?php echo $date2_ago;?></th>
	            <th class="text-left" colspan="8"><i>Opening Balance</i></th>
				<th class="text-center"><?php echo number_format($volume_opening_balance_pasir,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($harga_opening_balance_pasir,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($nilai_opening_balance_pasir,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
	            <th class="text-center"style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
				<th class="text-left"><i>Pembelian</i></th>
				<th class="text-center">M3</th>
				<th class="text-center"><?php echo number_format($total_volume_pembelian_pasir,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pembelian_pasir,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pembelian_pasir,0,',','.');?></th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"></th>
				<th class="text-center"><?php echo number_format($total_volume_pembelian_pasir_akhir,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pembelian_pasir_akhir,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pembelian_pasir_akhir,0,',','.');?></th>		
	        </tr>
			<tr class="table-active3">
			<th class="text-center"style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>			
				<th class="text-left"><i>Produksi</i></th>
				<th class="text-center">M3</th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"></th>
				<th class="text-center"><?php echo number_format($total_volume_pemakaian_pasir,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pemakaian_pasir,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pemakaian_pasir,0,',','.');?></th>
				<th class="text-center"><?php echo number_format($total_volume_stock_pasir_akhir,2,',','.');?></th>
				<th class="text-right" style='background-color:red; color:white'><blink><?php echo number_format($total_harga_stock_pasir_akhir,0,',','.');?></blink></th>
				<th class="text-right"><?php echo number_format($total_nilai_stock_pasir_akhir,0,',','.');?></th>
	        </tr>
			<tr class="table-active2">
	            <th class="text-center" colspan="12">BATU SPLIT 10-20</th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center" style="vertical-align:middle"><?php echo $date2_ago;?></th>
	            <th class="text-left" colspan="8"><i>Opening Balance</i></th>
				<th class="text-center"><?php echo number_format($volume_opening_balance_batu1020,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($harga_opening_balance_batu1020,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($nilai_opening_balance_batu1020,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
	            <th class="text-center"style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
				<th class="text-left"><i>Pembelian</i></th>
				<th class="text-center">M3</th>
				<th class="text-center"><?php echo number_format($total_volume_pembelian_batu1020,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pembelian_batu1020,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pembelian_batu1020,0,',','.');?></th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"></th>
				<th class="text-center"><?php echo number_format($total_volume_pembelian_batu1020_akhir,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pembelian_batu1020_akhir,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pembelian_batu1020_akhir,0,',','.');?></th>		
	        </tr>
			<tr class="table-active3">
				<th class="text-center"style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>			
				<th class="text-left"><i>Produksi</i></th>
				<th class="text-center">M3</th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"></th>
				<th class="text-center"><?php echo number_format($total_volume_pemakaian_batu1020,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pemakaian_batu1020,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pemakaian_batu1020,0,',','.');?></th>
				<th class="text-center"><?php echo number_format($total_volume_stock_batu1020_akhir,2,',','.');?></th>
				<th class="text-right" style='background-color:red; color:white'><blink><?php echo number_format($total_harga_stock_batu1020_akhir,0,',','.');?></blink></th>
				<th class="text-right"><?php echo number_format($total_nilai_stock_batu1020_akhir,0,',','.');?></th>
	        </tr>
			<tr class="table-active2">
	            <th class="text-center" colspan="12">BATU SPLIT 20-30</th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center" style="vertical-align:middle"><?php echo $date2_ago;?></th>
	            <th class="text-left" colspan="8"><i>Opening Balance</i></th>
				<th class="text-center"><?php echo number_format($volume_opening_balance_batu2030,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($harga_opening_balance_batu2030,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($nilai_opening_balance_batu2030,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
	            <th class="text-center"style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
				<th class="text-left"><i>Pembelian</i></th>
				<th class="text-center">M3</th>
				<th class="text-center"><?php echo number_format($total_volume_pembelian_batu2030,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pembelian_batu2030,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pembelian_batu2030,0,',','.');?></th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"></th>
				<th class="text-center"><?php echo number_format($total_volume_pembelian_batu2030_akhir,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pembelian_batu2030_akhir,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pembelian_batu2030_akhir,0,',','.');?></th>		
	        </tr>
			<tr class="table-active3">
				<th class="text-center"style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>			
				<th class="text-left"><i>Produksi</i></th>
				<th class="text-center">M3</th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"></th>
				<th class="text-center"><?php echo number_format($total_volume_pemakaian_batu2030,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pemakaian_batu2030,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pemakaian_batu2030,0,',','.');?></th>
				<th class="text-center"><?php echo number_format($total_volume_stock_batu2030_akhir,2,',','.');?></th>
				<th class="text-right" style='background-color:red; color:white'><blink><?php echo number_format($total_harga_stock_batu2030_akhir,0,',','.');?></blink></th>
				<th class="text-right"><?php echo number_format($total_nilai_stock_batu2030_akhir,0,',','.');?></th>
	        </tr>
			<tr class="table-active2">
	            <th class="text-center" colspan="12">BAHAN BAKU</th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center"style="vertical-align:middle"><?php echo $date2_ago;?></th>			
				<th class="text-left" colspan="10"><i>Opening Balance</i></th>
				<th class="text-right"><?php echo number_format($total_opening_balance_bahan_baku,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center"style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>		
				<th class="text-left"><i>Semen</i></th>
				<th class="text-center">Ton</th>
				<th class="text-center"><?php echo number_format($total_volume_pembelian_semen_all,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pembelian_semen_all,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pembelian_semen_all,0,',','.');?></th>
				<th class="text-center"><?php echo number_format($total_volume_pemakaian_semen,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pemakaian_semen,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pemakaian_semen,0,',','.');?></th>
				<th class="text-center"><?php echo number_format($total_volume_stock_semen_akhir,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_stock_semen_akhir,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_stock_semen_akhir,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center"style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
				<th class="text-left"><i>Pasir</i></th>
				<th class="text-center">M3</th>
				<th class="text-center"><?php echo number_format($total_volume_pembelian_pasir,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pembelian_pasir,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pembelian_pasir,0,',','.');?></th>
				<th class="text-center"><?php echo number_format($total_volume_pemakaian_pasir,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pemakaian_pasir,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pemakaian_pasir,0,',','.');?></th>
				<th class="text-center"><?php echo number_format($total_volume_stock_pasir_akhir,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_stock_pasir_akhir,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_stock_pasir_akhir,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center"style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
				<th class="text-left"><i>Batu Split 10-20</i></th>
				<th class="text-center">M3</th>
				<th class="text-center"><?php echo number_format($total_volume_pembelian_batu1020,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pembelian_batu1020,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pembelian_batu1020,0,',','.');?></th>
				<th class="text-center"><?php echo number_format($total_volume_pemakaian_batu1020,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pemakaian_batu1020,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pemakaian_batu1020,0,',','.');?></th>
				<th class="text-center"><?php echo number_format($total_volume_stock_batu1020_akhir,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_stock_batu1020_akhir,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_stock_batu1020_akhir,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center"style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
				<th class="text-left"><i>Batu Split 20-30</i></th>
				<th class="text-center">M3</th>
				<th class="text-center"><?php echo number_format($total_volume_pembelian_batu2030,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pembelian_batu2030,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pembelian_batu2030,0,',','.');?></th>
				<th class="text-center"><?php echo number_format($total_volume_pemakaian_batu2030,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pemakaian_batu2030,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pemakaian_batu2030,0,',','.');?></th>
				<th class="text-center"><?php echo number_format($total_volume_stock_batu2030_akhir,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_stock_batu2030_akhir,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_stock_batu2030_akhir,0,',','.');?></th>
	        </tr>
			<tr class="table-active5">
	            <th class="text-center" colspan="3">TOTAL</th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($total_nilai_pembelian,0,',','.');?></th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($total_nilai_pemakaian,0,',','.');?></th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($total_nilai_akhir,0,',','.');?></th>
	        </tr>
	    </table>
		<?php
	}

	public function pergerakan_bahan_baku_solar($arr_date)
	{
		$data = array();
		
		$arr_date = $this->input->post('filter_date');
		$arr_filter_date = explode(' - ', $arr_date);
		$date1 = '';
		$date2 = '';

		if(count($arr_filter_date) == 2){
			$date1 	= date('Y-m-d',strtotime($arr_filter_date[0]));
			$date2 	= date('Y-m-d',strtotime($arr_filter_date[1]));
			$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]));
		}
		
		?>
		
		<table class="table table-bordered" width="100%">
			<style type="text/css">
				table tr.table-active{
					background-color: #F0F0F0;
					font-size: 12px;
					font-weight: bold;
					color: black;
				}
					
				table tr.table-active2{
					background-color: #E8E8E8;
					font-size: 12px;
					font-weight: bold;
				}
					
				table tr.table-active3{
					font-size: 12px;
					background-color: #F0F0F0;
				}
					
				table tr.table-active4{
					background-color: #e69500;
					font-weight: bold;
					font-size: 12px;
					color: black;
				}
				table tr.table-active5{
					background-color: #cccccc;
					font-size: 12px;
					font-weight: bold;
					color: black;
				}
				table tr.table-activeago1{
					background-color: #ffd966;
					font-weight: bold;
					font-size: 12px;
					color: black;
				}
				table tr.table-activeopening{
					background-color: #2986cc;
					font-weight: bold;
					font-size: 12px;
					color: black;
				}
				blink {
				-webkit-animation: 2s linear infinite kedip; /* for Safari 4.0 - 8.0 */
				animation: 2s linear infinite kedip;
				}
				/* for Safari 4.0 - 8.0 */
				@-webkit-keyframes kedip { 
				0% {
					visibility: hidden;
				}
				50% {
					visibility: hidden;
				}
				100% {
					visibility: visible;
				}
				}
				@keyframes kedip {
				0% {
					visibility: hidden;
				}
				50% {
					visibility: hidden;
				}
				100% {
					visibility: visible;
				}
				}
			</style>
	        <tr class="table-active2">
	            <th colspan="3">Periode</th>
	            <th class="text-center" colspan="9"><?php echo $filter_date;?></th>
	        </tr>
			
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
			$total_harga_pembelian_solar_akhir = ($total_volume_pembelian_solar_akhir!=0)?($nilai_opening_balance_solar + $total_nilai_pembelian_solar) / $total_volume_pembelian_solar_akhir* 1:0;
			$total_nilai_pembelian_solar_akhir =  $total_volume_pembelian_solar_akhir * $total_harga_pembelian_solar_akhir;			
			
			$stock_opname_solar = $this->db->select('(cat.display_volume) as volume')
			->from('pmm_remaining_materials_cat cat ')
			->where("cat.date between '$date1' and '$date2'")
			->where("cat.material_id = 8")
			->where("cat.status = 'PUBLISH'")
			->order_by('cat.date','desc')->limit(1)
			->get()->row_array();
			
			$total_volume_stock_solar_akhir = $stock_opname_solar['volume'];
			

			$total_volume_pemakaian_solar = $total_volume_pembelian_solar_akhir - $stock_opname_solar['volume'];
			$total_harga_pemakaian_solar = $total_harga_pembelian_solar_akhir;
			$total_nilai_pemakaian_solar = $total_volume_pemakaian_solar * $total_harga_pemakaian_solar;

			$total_harga_stock_solar_akhir = $total_harga_pemakaian_solar;
			$total_nilai_stock_solar_akhir = $total_volume_stock_solar_akhir * $total_harga_stock_solar_akhir;

			//TOTAL
			$total_nilai_pembelian = $total_nilai_pembelian_solar;
			$total_nilai_pemakaian = $total_nilai_pemakaian_solar;
			$total_nilai_akhir = $total_nilai_stock_solar_akhir;

	        ?>
			
			<tr class="table-active4">
				<th width="30%" class="text-center" rowspan="2" style="vertical-align:middle">TANGGAL</th>
				<th width="20%" class="text-center" rowspan="2" style="vertical-align:middle">URAIAN</th>
				<th width="10%" class="text-center" rowspan="2" style="vertical-align:middle">SATUAN</th>
				<th width="20%" class="text-center" colspan="3">MASUK</th>
				<th width="20%" class="text-center" colspan="3">KELUAR</th>
				<th width="20%" class="text-center" colspan="3">AKHIR</th>
	        </tr>
			<tr class="table-active4">
				<th class="text-center">VOLUME</th>
				<th class="text-center">HARGA</th>
				<th class="text-center">NILAI</th>
				<th class="text-center">VOLUME</th>
				<th class="text-center">HARGA</th>
				<th class="text-center">NILAI</th>
				<th class="text-center">VOLUME</th>
				<th class="text-center">HARGA</th>
				<th class="text-center">NILAI</th>
	        </tr>
			<tr class="table-active2">
	            <th class="text-center" colspan="12">SOLAR</th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center" style="vertical-align:middle"><?php echo $date2_ago;?></th>
	            <th class="text-left" colspan="8"><i>Opening Balance</i></th>
				<th class="text-center"><?php echo number_format($volume_opening_balance_solar,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($harga_opening_balance_solar,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($nilai_opening_balance_solar,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
	            <th class="text-center"style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
				<th class="text-left"><i>Pembelian</i></th>
				<th class="text-center">Liter</th>
				<th class="text-center"><?php echo number_format($total_volume_pembelian_solar,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pembelian_solar,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pembelian_solar,0,',','.');?></th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"></th>
				<th class="text-center"><?php echo number_format($total_volume_pembelian_solar_akhir,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pembelian_solar_akhir,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pembelian_solar_akhir,0,',','.');?></th>		
	        </tr>
			<tr class="table-active3">
				<th class="text-center"style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>			
				<th class="text-left"><i>Produksi</i></th>
				<th class="text-center">Liter</th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"></th>
				<th class="text-center"><?php echo number_format($total_volume_pemakaian_solar,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pemakaian_solar,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pemakaian_solar,0,',','.');?></th>
				<th class="text-center"><?php echo number_format($total_volume_stock_solar_akhir,2,',','.');?></th>
				<th class="text-right" style='background-color:red; color:white'><blink><?php echo number_format($total_harga_stock_solar_akhir,0,',','.');?></blink></th>
				<th class="text-right"><?php echo number_format($total_nilai_stock_solar_akhir,0,',','.');?></th>
	        </tr>
			<tr class="table-active5">
	            <th class="text-center" colspan="3">TOTAL</th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($total_nilai_pembelian,0,',','.');?></th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($total_nilai_pemakaian,0,',','.');?></th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($total_nilai_akhir,0,',','.');?></th>
	        </tr>
	    </table>
		<?php
	}
	
	public function nilai_persediaan_barang($arr_date)
	{
		$data = array();
		
		$arr_date = $this->input->post('filter_date');
		$arr_filter_date = explode(' - ', $arr_date);
		$date1 = '';
		$date2 = '';

		if(count($arr_filter_date) == 2){
			$date1 	= date('Y-m-d',strtotime($arr_filter_date[0]));
			$date2 	= date('Y-m-d',strtotime($arr_filter_date[1]));
			$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]));
		}

	    ?>
		
		<table class="table table-bordered" width="100%">
		<style type="text/css">
			table tr.table-active{
				background-color: #F0F0F0;
				font-size: 12px;
				font-weight: bold;
				color: black;
			}
				
			table tr.table-active2{
				background-color: #A9A9A9;
				font-size: 12px;
				font-weight: bold;
			}
				
			table tr.table-active3{
				font-size: 12px;
				background-color: #F0F0F0;
			}
				
			table tr.table-active4{
				background-color: #e69500;
				font-weight: bold;
				font-size: 12px;
				color: black;
			}
			table tr.table-active5{
				background-color: #cccccc;
				font-weight: bold;
				font-size: 12px;
				color: black;
			}
			table tr.table-activeago1{
				background-color: #ffd966;
				font-weight: bold;
				font-size: 12px;
				color: black;
			}
			table tr.table-activeopening{
				background-color: #2986cc;
				font-weight: bold;
				font-size: 12px;
				color: black;
			}
		</style>

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
		
		$stock_opname_batu1020 = $this->db->select('sum(cat.display_volume) as volume')
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

		$total_nilai_pembelian = $total_nilai_pembelian_semen_all + $total_nilai_pembelian_pasir + $total_nilai_pembelian_batu1020 + $total_nilai_pembelian_batu2030;
		$total_nilai_pemakaian = $total_nilai_pemakaian_semen + $total_nilai_pemakaian_pasir + $total_nilai_pemakaian_batu1020 + $total_nilai_pemakaian_batu2030;
		$total_nilai_akhir = $total_nilai_stock_semen_akhir + $total_nilai_stock_pasir_akhir + $total_nilai_stock_batu1020_akhir + $total_nilai_stock_batu2030_akhir;
		
		?>

		<tr class="table-active4">
			<th width="20%" class="text-center" >TANGGAL</th>
			<th width="20%" class="text-center" >URAIAN</th>
			<th width="20%" class="text-center" >STOK BARANG</th>
			<th width="20%" class="text-center" >HARGA SATUAN</th>
			<th width="20%" class="text-center" >TOTAL</th>
		</tr>
		<tr class="table-active3">
			<th class="text-center"><?php echo $date2 = date('d-m-Y',strtotime($date2));?></th>
			<th class="text-left">Semen</th>
			<th class="text-center"><?php echo number_format($total_volume_stock_semen_akhir,2,',','.');?></th>
			<th class="text-right"><?php echo number_format($total_harga_stock_semen_akhir,0,',','.');?></th>
			<th class="text-right"><?php echo number_format($total_nilai_stock_semen_akhir,0,',','.');?></th>
		</tr>
		<tr class="table-active3">
			<th class="text-center"><?php echo $date2 = date('d-m-Y',strtotime($date2));?></th>
			<th class="text-left">Pasir</th>
			<th class="text-center"><?php echo number_format($total_volume_stock_pasir_akhir,2,',','.');?></th>
			<th class="text-right"><?php echo number_format($total_harga_stock_pasir_akhir,0,',','.');?></th>
			<th class="text-right"><?php echo number_format($total_nilai_stock_pasir_akhir,0,',','.');?></th>
		</tr>
		<tr class="table-active3">
			<th class="text-center"><?php echo $date2 = date('d-m-Y',strtotime($date2));?></th>
			<th class="text-left">Batu Split 10-20</th>
			<th class="text-center"><?php echo number_format($total_volume_stock_batu1020_akhir,2,',','.');?></th>
			<th class="text-right"><?php echo number_format($total_harga_stock_batu1020_akhir,0,',','.');?></th>
			<th class="text-right"><?php echo number_format($total_nilai_stock_batu1020_akhir,0,',','.');?></th>
		</tr>
		<tr class="table-active3">
			<th class="text-center"><?php echo $date2 = date('d-m-Y',strtotime($date2));?></th>
			<th class="text-left">Batu Split 20-30</th>
			<th class="text-center"><?php echo number_format($total_volume_stock_batu2030_akhir,2,',','.');?></th>
			<th class="text-right"><?php echo number_format($total_harga_stock_batu2030_akhir,0,',','.');?></th>
			<th class="text-right"><?php echo number_format($total_nilai_stock_batu2030_akhir,0,',','.');?></th>
		</tr>
		<tr class="table-active5">
			<th class="text-right" colspan="4">TOTAL NILAI PERSEDIAAN</th>
			<th class="text-right"><?php echo number_format($total_nilai_akhir,0,',','.');?></th>
		</tr>
	</table>
	<?php
	}

	public function report_production($arr_date)
	{
		$data = array();
		
		$arr_date = $this->input->post('filter_date');
		$arr_filter_date = explode(' - ', $arr_date);
		$date3 = '';
		$date1 = '';
		$date2 = '';

		if(count($arr_filter_date) == 2){
			$date3 	= date('2022-01-01',strtotime($date3));
			$date1 	= date('Y-m-d',strtotime($arr_filter_date[0]));
			$date2 	= date('Y-m-d',strtotime($arr_filter_date[1]));
			$filter_date = date('Y-m-d',strtotime($arr_filter_date[0])).' - '.date('Y-m-d',strtotime($arr_filter_date[1]));
			$filter_date_2 = date('Y-m-d',strtotime($date3)).' - '.date('Y-m-d',strtotime($arr_filter_date[1]));
		}
		
		?>
		
		<table class="table table-bordered" width="100%">
		 <style type="text/css">
			table tr.table-active{
				background-color: #F0F0F0;
				font-size: 12px;
				font-weight: bold;
			}
				
			table tr.table-active2{
				background-color: #A9A9A9;
				font-size: 12px;
				font-weight: bold;
			}
				
			table tr.table-active3{
				font-size: 12px;
			}
				
			table tr.table-active4{
				background-color: #D3D3D3;
				font-weight: bold;
				font-size: 12px;
				color: black;
			}
		 </style>
	        <tr class="table-active4">
	            <th colspan="2">Periode</th>
				<th class="text-center"></th>
				<th class="text-center"></th>
	            <th class="text-center"><?php echo $filter_date = $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
				<th class="text-center"><?php echo $filter_date_2 = date('d/m/Y',strtotime($date3)).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
	        </tr>

			<?php

			//PENJUALAN
			$penjualan = $this->db->select('p.nama, pp.client_id, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume, pp.convert_measure as measure')
			->from('pmm_productions pp')
			->join('penerima p', 'pp.client_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("pp.date_production between '$date1' and '$date2'")
			->where("pp.status = 'PUBLISH'")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.client_id")
			->get()->result_array();
			
			$total_penjualan = 0;
			$total_volume = 0;

			foreach ($penjualan as $x){
				$total_penjualan += $x['price'];
				$total_volume += $x['volume'];
			}

			$total_penjualan_all = 0;
			$total_penjualan_all = $total_penjualan;
			//PENJUALAN

			//PENJUALAN_2
			$penjualan_2 = $this->db->select('p.nama, pp.client_id, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume, pp.convert_measure as measure')
			->from('pmm_productions pp')
			->join('penerima p', 'pp.client_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("pp.date_production between '$date3' and '$date2'")
			->where("pp.status = 'PUBLISH'")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.client_id")
			->get()->result_array();
			
			$total_penjualan_2 = 0;
			$total_volume_2 = 0;

			foreach ($penjualan_2 as $x){
				$total_penjualan_2 += $x['price'];
				$total_volume_2 += $x['volume'];
			}

			$total_penjualan_all_2 = 0;
			$total_penjualan_all_2 = $total_penjualan_2;
			//PENJUALAN_2

			//BAHAN		
			$akumulasi = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar as total_nilai_keluar')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi between '$date1' and '$date2')")
			->get()->result_array();

			$total_akumulasi = 0;

			foreach ($akumulasi as $a){
				$total_akumulasi += $a['total_nilai_keluar'];
			}

			$total_nilai = $total_akumulasi;
			//END BAHAN

			//BAHAN_2		
			$akumulasi_2 = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar as total_nilai_keluar')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi between '$date3' and '$date2')")
			->get()->result_array();

			$total_akumulasi_2 = 0;

			foreach ($akumulasi_2 as $a){
				$total_akumulasi_2 += $a['total_nilai_keluar'];
			}

			$total_nilai_2 = $total_akumulasi_2;
			//END BAHAN_2

			//ALAT
			$nilai_alat = $this->db->select('SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("prm.material_id in (12,13,14,15,16,23,24,25)")
			->where("po.status in ('PUBLISH','CLOSED')")
			->get()->row_array();

			$akumulasi_bbm = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar_2 as total_nilai_keluar_2')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi between '$date1' and '$date2')")
			->get()->result_array();

			$total_akumulasi_bbm = 0;

			foreach ($akumulasi_bbm as $b){
				$total_akumulasi_bbm += $b['total_nilai_keluar_2'];
			}

			$total_nilai_bbm = $total_akumulasi_bbm;

			$total_insentif_tm = 0;

			$insentif_tm = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->where("pdb.akun = 220")
			->where("status = 'PAID'")
			->where("(tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$total_insentif_tm = $insentif_tm['total'];

			$alat = $nilai_alat['nilai'] + $total_akumulasi_bbm + $total_insentif_tm;
			//END ALAT

			//ALAT_2
			$nilai_alat_2 = $this->db->select('SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->where("prm.date_receipt between '$date3' and '$date2'")
			->where("prm.material_id in (12,13,14,15,16,23,24,25)")
			->where("po.status in ('PUBLISH','CLOSED')")
			->get()->row_array();

			$akumulasi_bbm_2 = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar_2 as total_nilai_keluar_2')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi between '$date3' and '$date2')")
			->get()->result_array();

			$total_akumulasi_bbm_2 = 0;

			foreach ($akumulasi_bbm_2 as $b){
				$total_akumulasi_bbm_2 += $b['total_nilai_keluar_2'];
			}

			$total_nilai_bbm_2 = $total_akumulasi_bbm_2;

			$total_insentif_tm_2 = 0;

			$insentif_tm_2 = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->where("pdb.akun = 220")
			->where("status = 'PAID'")
			->where("(tanggal_transaksi between '$date3' and '$date2')")
			->get()->row_array();

			$total_insentif_tm_2 = $insentif_tm_2['total'];

			$alat_2 = $nilai_alat_2['nilai'] + $total_akumulasi_bbm_2 + $total_insentif_tm_2;
			//END_ALAT_2

			//OVERHEAD
			$overhead_15 = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',15)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$overhead_jurnal_15 = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',15)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$overhead_16 = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',16)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$overhead_jurnal_16 = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',16)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$overhead_17 = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',17)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$overhead_jurnal_17 = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',17)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$overhead =  $overhead_15['total'] + $overhead_jurnal_15['total'] + $overhead_16['total'] + $overhead_jurnal_16['total'] + $overhead_17['total'] + $overhead_jurnal_17['total'];
			//END OVERHEAD

			//OVERHEAD_2
			$overhead_15_2 = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',15)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date3' and '$date2')")
			->get()->row_array();

			$overhead_jurnal_15_2 = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',15)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date3' and '$date2')")
			->get()->row_array();

			$overhead_16_2 = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',16)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date3' and '$date2')")
			->get()->row_array();

			$overhead_jurnal_16_2 = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',16)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date3' and '$date2')")
			->get()->row_array();

			$overhead_17_2 = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',17)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date3' and '$date2')")
			->get()->row_array();

			$overhead_jurnal_17_2 = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',17)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date3' and '$date2')")
			->get()->row_array();

			$overhead_2 =  $overhead_15_2['total'] + $overhead_jurnal_15_2['total'] + $overhead_16_2['total'] + $overhead_jurnal_16_2['total'] + $overhead_17_2['total'] + $overhead_jurnal_17_2['total'];
			//END_OVERHEAD_2

			//DISKONTO
			$diskonto = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 168")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$diskonto = $diskonto['total'];
			//END DISKONTO

			//DISKONTO_2
			$diskonto_2 = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 168")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date3' and '$date2')")
			->get()->row_array();

			$diskonto_2 = $diskonto_2['total'];
			//END_DISKONTO_2

			$bahan = $total_nilai;
			$alat = $alat;
			$overhead = $overhead;
			$diskonto = $diskonto;

			$total_biaya_operasional = $bahan + $alat + $overhead + $diskonto;

			$laba_kotor = $total_penjualan_all - $total_biaya_operasional;

			$laba_sebelum_pajak = $laba_kotor;

			$persentase_laba_sebelum_pajak = ($total_penjualan_all!=0)?($laba_sebelum_pajak / $total_penjualan_all)  * 100:0;

			$bahan_2 = $total_nilai_2;
			$alat_2 = $alat_2;
			$overhead_2 = $overhead_2;
			$diskonto_2 = $diskonto_2;

			$total_biaya_operasional_2 = $bahan_2 + $alat_2 + $overhead_2 + $diskonto_2;

			$laba_kotor_2 = $total_penjualan_all_2 - $total_biaya_operasional_2;

			$laba_sebelum_pajak_2 = $laba_kotor_2;

			$persentase_laba_sebelum_pajak_2 = ($total_penjualan_all_2!=0)?($laba_sebelum_pajak_2 / $total_penjualan_all_2)  * 100:0;

	        ?>

			<tr class="table-active">
	            <th width="100%" class="text-left" colspan="6">Pendapatan Penjualan</th>
	        </tr>
			<tr class="table-active3">
	            <th width="10%" class="text-center">4-40000</th>
				<th width="90%" class="text-left" colspan="5">Pendapatan</th>
	        </tr>
			<?php foreach ($penjualan as $x): ?>
			<tr class="table-active3">
	            <th width="10%"></th>
				<th width="30%"><?= $x['nama'] ?></th>
				<th width="10%" class="text-right"><?php echo number_format($x['volume'],2,',','.');?></th>
				<th width="10%" class="text-center"><?= $x['measure'];?></th>
	            <th width="20%" class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><?php echo number_format($x['price'],0,',','.');?></span>
								</th>
							</tr>
					</table>
				</th>
			<?php endforeach; ?>
			<?php foreach ($penjualan_2 as $x): ?>
				<th width="20%" class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><?php echo number_format($x['price'],0,',','.');?></span>
								</th>
							</tr>
					</table>
				</th>
	        </tr>
			<?php endforeach; ?>
			<tr class="table-active3">
				<th class="text-left" colspan="2">Total Pendapatan</th>
				<th class="text-right"><?php echo number_format($total_volume,2,',','.');?></th>
				<th class="text-center">M3</th>
	            <th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><?php echo number_format($total_penjualan_all,0,',','.');?></span>
								</th>
							</tr>
					</table>
				</th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><?php echo number_format($total_penjualan_all_2,0,',','.');?></span>
								</th>
							</tr>
					</table>
				</th>
	        </tr>
			<tr class="table-active3">
				<th colspan="6"></th>
			</tr>
			<tr class="table-active">
				<th class="text-left" colspan="6">Beban Pokok Penjualan</th>
	        </tr>
			<tr class="table-active3">
	            <th class="text-center"></th>
				<th class="text-left" colspan="3">Bahan</th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><a target="_blank" href="<?= base_url("laporan/cetak_bahan?filter_date=".$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($bahan,0,',','.');?></a></span>
								</th>
							</tr>
					</table>
				</th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><a target="_blank" href="<?= base_url("laporan/cetak_bahan?filter_date=".$filter_date_2 = date('d F Y',strtotime($date3)).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($bahan_2,0,',','.');?></a></span>
								</th>
							</tr>
					</table>
				</th>
	        </tr>
			<tr class="table-active3">
	            <th class="text-center"></th>
				<th class="text-left" colspan="3">Alat</th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><a target="_blank" href="<?= base_url("laporan/cetak_alat?filter_date=".$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($alat,0,',','.');?></a></span>
								</th>
							</tr>
					</table>
				</th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><a target="_blank" href="<?= base_url("laporan/cetak_alat?filter_date=".$filter_date_2 = date('d F Y',strtotime($date3)).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($alat_2,0,',','.');?></a></span>
								</th>
							</tr>
					</table>
				</th>
	        </tr>
			<tr class="table-active3">
	            <th class="text-center"></th>
				<th class="text-left" colspan="3">Overhead</th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><a target="_blank" href="<?= base_url("laporan/cetak_overhead?filter_date=".$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($overhead,0,',','.');?></a></span>
								</th>
							</tr>
					</table>
				</th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><a target="_blank" href="<?= base_url("laporan/cetak_overhead?filter_date=".$filter_date_2 = date('d F Y',strtotime($date3)).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($overhead_2,0,',','.');?></a></span>
								</th>
							</tr>
					</table>
				</th>
	        </tr>
			<tr class="table-active3">
	            <th class="text-center"></th>
				<th class="text-left" colspan="3">Diskonto</th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><a target="_blank" href="<?= base_url("laporan/cetak_diskonto?filter_date=".$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($diskonto,0,',','.');?></a></span>
								</th>
							</tr>
					</table>
				</th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><a target="_blank" href="<?= base_url("laporan/cetak_diskonto?filter_date=".$filter_date_2 = date('d F Y',strtotime($date3)).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($diskonto_2,0,',','.');?></a></span>
								</th>
							</tr>
					</table>
				</th>
	        </tr>
			<tr class="table-active3">
				<th class="text-left" colspan="4">Total Beban Pokok Penjualan</th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left"width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><?php echo number_format($total_biaya_operasional,0,',','.');?></span>
								</th>
							</tr>
					</table>				
				</th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left"width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><?php echo number_format($total_biaya_operasional_2,0,',','.');?></span>
								</th>
							</tr>
					</table>				
				</th>
	        </tr>
			<tr class="table-active3">
				<th colspan="6"></th>
			</tr>
			<?php
				$styleColorLabaKotor = $laba_kotor < 0 ? 'color:red' : 'color:black';
				$styleColorLabaKotor2 = $laba_kotor_2 < 0 ? 'color:red' : 'color:black';
				$styleColorSebelumPajak = $laba_sebelum_pajak < 0 ? 'color:red' : 'color:black';
				$styleColorSebelumPajak2 = $laba_sebelum_pajak_2 < 0 ? 'color:red' : 'color:black';
				$styleColorPresentase = $persentase_laba_sebelum_pajak < 0 ? 'color:red' : 'color:black';
				$styleColorPresentase2 = $persentase_laba_sebelum_pajak_2 < 0 ? 'color:red' : 'color:black';
			?>
			<tr class="table-active3">
				<th class="text-left" colspan="4">Laba Kotor</th>
	            <th class="text-right" style="<?php echo $styleColorLabaKotor ?>">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><?php echo number_format($laba_kotor,0,',','.');?></span>
								</th>
							</tr>
					</table>
				</th>
				<th class="text-right" style="<?php echo $styleColorLabaKotor2 ?>">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><?php echo number_format($laba_kotor_2,0,',','.');?></span>
								</th>
							</tr>
					</table>
				</th>
	        </tr>
			<tr class="table-active3">
				<th colspan="6"></th>
			</tr>
			<tr class="table-active">
				<th class="text-left" colspan="4">Biaya Umum & Administrasi</th>
	            <th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span>-</span>
								</th>
							</tr>
					</table>
				</th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span>-</span>
								</th>
							</tr>
					</table>
				</th>
	        </tr>
			<tr class="table-active3">
				<th colspan="6"></th>
			</tr>
			<tr class="table-active3">
	            <th colspan="4" class="text-left">Laba Sebelum Pajak</th>
	            <th class="text-right" style="<?php echo $styleColorSebelumPajak ?>">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><?php echo number_format($laba_sebelum_pajak,0,',','.');?></span>
								</th>
							</tr>
					</table>
				</th>
				<th class="text-right" style="<?php echo $styleColorSebelumPajak2 ?>">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><?php echo number_format($laba_sebelum_pajak_2,0,',','.');?></span>
								</th>
							</tr>
					</table>
				</th>
	        </tr>
			<tr class="table-active3">
	            <th colspan="4" class="text-left">Presentase</th>
	            <th class="text-right" style="<?php echo $styleColorPresentase ?>">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><?php echo number_format($persentase_laba_sebelum_pajak,0,',','.');?> %</span>
								</th>
							</tr>
					</table>
				</th>
				<th class="text-right" style="<?php echo $styleColorPresentase2 ?>">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><?php echo number_format($persentase_laba_sebelum_pajak_2,0,',','.');?> %</span>
								</th>
							</tr>
					</table>
				</th>
	        </tr>
	    </table>
		<?php
	}


	public function revenues()
	{

		
		$arr_date = $this->input->post('filter_date');
		if(empty($filter_date)){
			$filter_date = '-';
		}else {
			$filter_date = $arr_date;
		}
		$alphas = range('A', 'Z');
		$data['alphas'] = $alphas;
		$data['clients'] = $this->db->get_where('pmm_client',array('status'=>'PUBLISH'))->result_array();
		$data['arr_date'] = $arr_date;
		$this->load->view('pmm/ajax/reports/revenues',$data);
	}

	public function receipt_materials()
	{
		
		$arr_date = $this->input->post('filter_date');
		if(empty($arr_date)){
    		$month = date('Y-m');
    		$start_date = date('Y-m',strtotime('- 1month '.$month)).'-27';
    		$end_date = $month.'-26';
    	}else {
    		$dt = explode(' - ', $arr_date);
    		$start_date = date('Y-m-d',strtotime($dt[0]));
    		$end_date = date('Y-m-d',strtotime($dt[1]));
    	}

		$data['filter_date'] = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));
		$data['arr'] =  $this->pmm_reports->ReceiptMaterialTagDetails($arr_date);
		$this->load->view('pmm/ajax/reports/receipt_materials',$data);

	}

	public function receipt_material_detail()
	{
		$id = $this->input->post('id');	
		$arr_date = $this->input->post('filter_date');
		$type = $this->input->post('type');
		$data['type'] = $type;
		$data['arr'] =  $this->pmm_reports->ReceiptMaterialTagDetails($id,$arr_date);
		$data['name'] = $this->input->post('name'); 
		$this->load->view('pmm/ajax/reports/receipt_materials_detail',$data);
	}


	public function material_usage()
	{

		$product_id = $this->input->post('product_id');
		$arr_date = $this->input->post('filter_date');

		if(empty($arr_date)){
    		$month = date('Y-m');
    		$start_date = date('Y-m',strtotime('- 1month '.$month)).'-27';
    		$end_date = $month.'-26';

    		$arr_date_2 = $start_date.' - '.$end_date;

    		$total_revenue_now = $this->pmm_model->getRevenueAll($arr_date_2);
    		$total_revenue_before = $this->pmm_model->getRevenueAll($arr_date_2,true);
    	}else {



    		$dt = explode(' - ', $arr_date);
    		$start_date = date('Y-m-d',strtotime($dt[0]));
    		$end_date = date('Y-m-d',strtotime($dt[1]));

    		$total_revenue_now = $this->pmm_model->getRevenueAll($arr_date);
    		$total_revenue_before = $this->pmm_model->getRevenueAll($arr_date,true);
    	} 

		$data['filter_date'] = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));

		if(!empty($product_id)){
			$data['product'] = $this->crud_global->GetField('pmm_product',array('id'=>$product_id),'product');
			$data['total_production'] = $this->pmm_reports->TotalProductions($product_id,$arr_date);
			$data['arr_compo'] = $this->pmm_reports->MaterialUsageCompoProduct($arr_date,$product_id);
			$this->load->view('pmm/ajax/reports/material_usage_product',$data);
		}else {

			$data['arr'] =  $this->pmm_reports->MaterialUsageReal($arr_date);
			$data['arr_compo'] = $this->pmm_reports->MaterialUsageCompo($arr_date);
			$data['total_revenue_now'] = $total_revenue_now;
			$data['total_revenue_before'] =  $total_revenue_before;
			$this->load->view('pmm/ajax/reports/material_usage',$data);	
		}
		
	}

	public function material_usage_detail()
	{
		$id = $this->input->post('id');	
		$arr_date = $this->input->post('filter_date');
		if(empty($arr_date)){
    		$month = date('Y-m');
    		$start_date = date('Y-m',strtotime('- 1month '.$month)).'-27';
    		$end_date = $month.'-26';
    	}else {
    		$dt = explode(' - ', $arr_date);
    		$start_date = date('Y-m-d',strtotime($dt[0]));
    		$end_date = date('Y-m-d',strtotime($dt[1]));
    	} 

		$type = $this->input->post('type');
		$product_id = $this->input->post('product_id');
		$data['type'] = $type;
		if($type == 'compo' || $type == 'compo_cost' || $type == 'compo_now'){
			$data['arr'] =  $this->pmm_reports->MaterialUsageCompoDetails($id,$arr_date,$product_id);
		}else {
			$data['arr'] =  $this->pmm_reports->MaterialUsageDetails($id,$arr_date);	
		}

		$data['filter_date'] = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));
		$data['product_id'] = $product_id;
		$data['name'] = $this->input->post('name'); 
		$this->load->view('pmm/ajax/reports/material_usage_detail',$data);
	}

	public function material_remaining()
	{
		$arr_date = $this->input->post('filter_date');
		if(empty($arr_date)){
    		$month = date('Y-m');
    		$start_date = date('Y-m',strtotime('- 1month '.$month)).'-27';
    		$end_date = $month.'-26';
    	}else {
    		$dt = explode(' - ', $arr_date);
    		$start_date = date('Y-m-d',strtotime($dt[0]));
    		$end_date = date('Y-m-d',strtotime($dt[1]));
    	} 

    	$date = array($start_date,$end_date);
		$data['filter_date'] = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));

		$data['arr'] =  $this->pmm_reports->MaterialRemainingReal($date);
		$data['arr_compo'] = $this->pmm_reports->MaterialRemainingCompo($date);
		$this->load->view('pmm/ajax/reports/material_remaining',$data);	
		

	}

	public function material_remaining_detail()
	{
		$id = $this->input->post('id');	
		$arr_date = $this->input->post('filter_date');
		if(empty($arr_date)){
    		$month = date('Y-m');
    		$start_date = date('Y-m',strtotime('- 1month '.$month)).'-27';
    		$end_date = $month.'-26';
    	}else {
    		$dt = explode(' - ', $arr_date);
    		$start_date = date('Y-m-d',strtotime($dt[0]));
    		$end_date = date('Y-m-d',strtotime($dt[1]));
    	} 
    	$date = array($start_date,$end_date);
		$type = $this->input->post('type');
		$data['type'] = $type;
		if($type == 'compo'){
			$data['arr'] =  $this->pmm_reports->MaterialRemainingCompoDetails($id,$date);
		}else {
			$data['arr'] =  $this->pmm_reports->MaterialRemainingDetails($id,$arr_date);	
		}

		$data['filter_date'] = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));
		$data['name'] = $this->input->post('name'); 
		$this->load->view('pmm/ajax/reports/material_remaining_detail',$data);
	}

	public function equipments()
	{
		$arr_date = $this->input->post('filter_date');
		$supplier_id = $this->input->post('supplier_id');
		if(empty($arr_date)){
    		$month = date('Y-m');
    		$start_date = date('Y-m',strtotime('- 1month '.$month)).'-27';
    		$end_date = $month.'-26';
    	}else {
    		$dt = explode(' - ', $arr_date);
    		$start_date = date('Y-m-d',strtotime($dt[0]));
    		$end_date = date('Y-m-d',strtotime($dt[1]));
    	}

    	$date = array($start_date,$end_date);
    	$data['filter_date'] = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));
		$data['arr'] =  $this->pmm_reports->EquipmentProd($date);
		$data['equipments'] =  $this->pmm_reports->EquipmentReports($date,$supplier_id);
		$data['solar'] =  $this->pmm_reports->EquipmentUsageReal($date,true);
		$this->load->view('pmm/ajax/reports/equipments',$data);

	}

	public function equipments_detail()
	{
		$id = $this->input->post('id');	
		$arr_date = $this->input->post('filter_date');
		if(empty($arr_date)){
    		$month = date('Y-m');
    		$start_date = date('Y-m',strtotime('- 1month '.$month)).'-27';
    		$end_date = $month.'-26';
    	}else {
    		$dt = explode(' - ', $arr_date);
    		$start_date = date('Y-m-d',strtotime($dt[0]));
    		$end_date = date('Y-m-d',strtotime($dt[1]));
    	}
    	$date = array($start_date,$end_date);
		$supplier_id = $this->input->post('supplier_id');;
		$data['equipments'] = $this->pmm_reports->EquipmentReportsDetails($id,$date,$supplier_id);
		$data['name'] = $this->input->post('name');
		$data['filter_date'] = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));
		$this->load->view('pmm/ajax/reports/equipments_detail',$data);
	}


	public function equipments_data_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		$pdf->AddPage('P');

		$arr_data = array();
		$date = $this->input->get('filter_date');
		$supplier_id = $this->input->get('supplier_id');
		$tool_id = $this->input->get('tool_id');
		if(!empty($date)){
			$arr_date = explode(' - ',$date);
			$start_date = date('Y-m-d',strtotime($arr_date[0]));
			$end_date = date('Y-m-d',strtotime($arr_date[1]));

			$filter_date = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));
			$data['filter_date'] = $filter_date;
			$date = explode(' - ',$start_date.' - '.$end_date);
			$arr_data = $this->pmm_reports->EquipmentsData($date,$supplier_id,$tool_id);

			$data['data'] = $arr_data;
			$data['solar'] =  $this->pmm_reports->EquipmentUsageReal($date);
	        $html = $this->load->view('pmm/equipments_data_print',$data,TRUE);

	        
	        $pdf->SetTitle('Data Alat');
	        $pdf->nsi_html($html);
	        $pdf->Output('Data-Alat.pdf', 'I');

		}else {
			echo 'Please Filter Date First';
		}
		

		
	
	}

	public function revenues_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('P');

		$arr_date = $this->input->get('filter_date');
		if(empty($arr_date)){
			$filter_date = '-';
		}else {
			$arr_filter_date = explode(' - ', $arr_date);
			$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]));
		}
		$data['filter_date'] = $filter_date;
		$alphas = range('A', 'Z');
		$data['alphas'] = $alphas;
		$data['arr_date'] = $arr_date;
		$data['clients'] = $this->db->get_where('pmm_client',array('status'=>'PUBLISH'))->result_array();
        $html = $this->load->view('pmm/revenues_print',$data,TRUE);

        
        $pdf->SetTitle('LAPORAN PENDAPATAN USAHA');
        $pdf->nsi_html($html);
        $pdf->Output('LAPORAN-PENDAPATAN-USAHA.pdf', 'I');
	
	}
	
	public function monitoring_receipt_materials_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('P');

		$arr_date = $this->input->get('filter_date');
		if(empty($arr_date)){
    		$month = date('Y-m');
    		$start_date = date('Y-m',strtotime('- 1month '.$month)).'-27';
    		$end_date = $month.'-26';
    	}else {
    		$dt = explode(' - ', $arr_date);
    		$start_date = date('Y-m-d',strtotime($dt[0]));
    		$end_date = date('Y-m-d',strtotime($dt[1]));
    	}

		$data['filter_date'] = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));
		$data['arr'] =  $this->pmm_reports->ReceiptMaterialTagDetails($arr_date);
        $html = $this->load->view('pmm/monitoring_receipt_materials_print',$data,TRUE);

        
        $pdf->SetTitle('LAPORAN PENERIMAAN BAHAN');
        $pdf->nsi_html($html);
        $pdf->Output('LAPORAN-PENERIMAAN-BAHAN.pdf', 'I');
	
	}

	public function material_usage_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('P');

		$product_id = $this->input->get('product_id');
		$arr_date = $this->input->get('filter_date');
		if(empty($arr_date)){
    		$month = date('Y-m');
    		$start_date = date('Y-m',strtotime('- 1month '.$month)).'-27';
    		$end_date = $month.'-26';

    		$arr_date_2 = $start_date.' - '.$end_date;

    		$total_revenue_now = $this->pmm_model->getRevenueAll($arr_date_2);
    		$total_revenue_before = $this->pmm_model->getRevenueAll($arr_date_2,true);

    	}else {
    		$dt = explode(' - ', $arr_date);
    		$start_date = date('Y-m-d',strtotime($dt[0]));
    		$end_date = date('Y-m-d',strtotime($dt[1]));

    		$total_revenue_now = $this->pmm_model->getRevenueAll($arr_date);
    		$total_revenue_before = $this->pmm_model->getRevenueAll($arr_date,true);
    	}
    	
		$data['filter_date'] = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));
		if(empty($product_id)){
			$data['arr'] =  $this->pmm_reports->MaterialUsageReal($arr_date);
			$data['arr_compo'] = $this->pmm_reports->MaterialUsageCompo($arr_date);
			$data['total_revenue_now'] = $total_revenue_now;
			$data['total_revenue_before'] =  $total_revenue_before;
	        $html = $this->load->view('pmm/material_usage_print',$data,TRUE);
		}else {
			$data['product'] = $this->crud_global->GetField('pmm_product',array('id'=>$product_id),'product');
			$data['total_production'] = $this->pmm_reports->TotalProductions($product_id,$arr_date);
			$data['arr_compo'] = $this->pmm_reports->MaterialUsageCompoProduct($arr_date,$product_id);

			

	        $html = $this->load->view('pmm/material_usage_product_print',$data,TRUE);
		}
		 
        $pdf->SetTitle('LAPORAN PEMAKAIAN MATERIAL');
        $pdf->nsi_html($html);
        $pdf->Output('LAPORAN-PEMAKAIAN-MATERIAL.pdf', 'I');
	
	}

	public function material_remaining_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('P');

		$arr_date = $this->input->get('filter_date');
		if(empty($arr_date)){
    		$month = date('Y-m');
    		$start_date = date('Y-m',strtotime('- 1month '.$month)).'-27';
    		$end_date = $month.'-26';
    	}else {
    		$dt = explode(' - ', $arr_date);
    		$start_date = date('Y-m-d',strtotime($dt[0]));
    		$end_date = date('Y-m-d',strtotime($dt[1]));
    	} 

    	$date = array($start_date,$end_date);
		$data['filter_date'] = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));

		$data['arr'] =  $this->pmm_reports->MaterialRemainingReal($date);
		$data['arr_compo'] = $this->pmm_reports->MaterialRemainingCompo($date);

        $html = $this->load->view('pmm/material_remaining_print',$data,TRUE);

        
        $pdf->SetTitle('Materials Remaining');
        $pdf->nsi_html($html);
        $pdf->Output('Materials-Remaining.pdf', 'I');
	
	}


	public function monitoring_equipments_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('P');

		$arr_date = $this->input->get('filter_date');
		$supplier_id = $this->input->get('supplier_id');
		if(empty($arr_date)){
    		$month = date('Y-m');
    		$start_date = date('Y-m',strtotime('- 1month '.$month)).'-27';
    		$end_date = $month.'-26';
    	}else {
    		$dt = explode(' - ', $arr_date);
    		$start_date = date('Y-m-d',strtotime($dt[0]));
    		$end_date = date('Y-m-d',strtotime($dt[1]));
    	}

    	$date = array($start_date,$end_date);
    	$data['filter_date'] = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));
		$data['arr'] =  $this->pmm_reports->EquipmentProd($date);
		$data['equipments'] =  $this->pmm_reports->EquipmentReports($date,$supplier_id);
		$data['supplier'] = $this->crud_global->GetField('pmm_supplier',array('id'=>$supplier_id),'name');

        $html = $this->load->view('pmm/monitoring_equipments_print',$data,TRUE);

        
        $pdf->SetTitle('LAPORAN PEMAKAIAN ALAT');
        $pdf->nsi_html($html);
        $pdf->Output('LAPORAN-PEMAKAIAN-ALAT.pdf', 'I');
	
	}

	public function general_cost_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->SetTopMargin(0);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('P');

		$arr_date = $this->input->get('filter_date');
		$filter_type = $this->input->get('filter_type');
		if(empty($arr_date)){
    		$data['filter_date'] = '-';
    	}else {
    		$dt = explode(' - ', $arr_date);
    		$start_date = date('Y-m-d',strtotime($dt[0]));
    		$end_date = date('Y-m-d',strtotime($dt[1]));
    		$data['filter_date'] = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));
    	} 

		


		if(!empty($arr_date)){
			$dt = explode(' - ', $arr_date);
    		$start_date = date('Y-m-d',strtotime($dt[0]));
    		$end_date = date('Y-m-d',strtotime($dt[1]));
    		$this->db->where('date >=',$start_date);
    		$this->db->where('date <=',$end_date);	
		}
		if(!empty($filter_type)){
			$this->db->where('type',$filter_type);
		}
		$this->db->order_by('date','desc');
		$this->db->where('status !=','DELETED');
		$arr = $this->db->get_where('pmm_general_cost');
		$data['arr'] =  $arr->result_array();

        $html = $this->load->view('pmm/general_cost_print',$data,TRUE);

        
        $pdf->SetTitle('General Cost');
        $pdf->nsi_html($html);
        $pdf->Output('General-Cost.pdf', 'I');
	
	}


	public function purchase_order_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('P');

		$arr_date = $this->input->get('filter_date');
		if(empty($arr_date)){
			$filter_date = '-';
		}else {
			$arr_filter_date = explode(' - ', $arr_date);
			$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]));
		}
		$data['filter_date'] = $filter_date;
		$data['w_date'] = $arr_date;
		$data['status'] = $this->input->post('status');
		$data['supplier_id'] = $this->input->post('supplier_id');
		$this->db->select('supplier_id');
		$this->db->where('status !=','DELETED');
		if(!empty($data['status'])){
			$this->db->where('supplier_id',$data['status']);
		}
		$this->db->group_by('supplier_id');
		$this->db->order_by('created_on','desc');
		$query = $this->db->get('pmm_purchase_order');

		$data['data'] = $query->result_array();
        $html = $this->load->view('pmm/purchase_order_print',$data,TRUE);

        
        $pdf->SetTitle('Purchase Order');
        $pdf->nsi_html($html);
        $pdf->Output('Purchase-Order.pdf', 'I');
	
	}


	public function product_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('P');

		$arr_data = array();
		$tag_id = $this->input->get('product_id');

		if(!empty($tag_id)){
			$this->db->where('tag_id',$tag_id);	
		}
		$this->db->where('status !=','DELETED');
		$this->db->order_by('product','asc');
		$query = $this->db->get('pmm_product');
		if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
				$name = "'".$row['product']."'";
				$row['no'] = $key+1;
				$row['created_on'] = date('d F Y',strtotime($row['created_on']));
				$contract_price = $this->pmm_model->GetContractPrice($row['contract_price']);
				$row['contract_price'] = number_format($contract_price,2,',','.');
				$row['riel_price'] = number_format($this->pmm_model->GetRielPrice($row['id']),2,',','.');
				$row['composition'] = $this->crud_global->GetField('pmm_composition',array('id'=>$row['composition_id']),'composition_name');
				$row['tag_name'] = $this->crud_global->GetField('pmm_tags',array('id'=>$row['tag_id']),'tag_name');
				$arr_data[] = $row;
			}

		}

		$data['data'] = $arr_data;
        $html = $this->load->view('pmm/product_print',$data,TRUE);

        
        $pdf->SetTitle('Product');
        $pdf->nsi_html($html);
        $pdf->Output('Product.pdf', 'I');
	
	}

	public function product_hpp_print()
	{
		$id = $this->input->get('id');
		$name = $this->input->get('name');
		if(!empty($id)){
			$this->load->library('pdf');
		

			$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
	        $pdf->setPrintHeader(true);
	        $pdf->SetFont('helvetica','',7); 
	        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
			$pdf->setHtmlVSpace($tagvs);
			        $pdf->AddPage('P');

			$arr_data = array();

			$output = $this->pmm_model->GetRielPriceDetail($id);

			$data['data'] = $output;
			$data['name'] = $name;
	        $html = $this->load->view('pmm/product_hpp_print',$data,TRUE);

	        
	        $pdf->SetTitle('Product-HPP');
	        $pdf->nsi_html($html);
	        $pdf->Output('Product-HPP-'.$name.'.pdf', 'I');
		}else {
			echo "Product Not Found";
		}
		
	
	}

	public function materials_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		
		$pdf->AddPage('P');

		$arr_data = array();
		$tag_id = $this->input->get('tag_id');

		$this->db->where('status !=','DELETED');
		if(!empty($tag_id)){
			$this->db->where('tag_id',$tag_id);
		}
		$this->db->order_by('material_name','asc');
		$query = $this->db->get('pmm_materials');
		if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
				$row['no'] = $key+1;
				$row['price'] = number_format($row['price'],2,',','.');
				$row['cost'] = number_format($row['cost'],2,',','.');
				$row['measure'] = $this->crud_global->GetField('pmm_measures',array('id'=>$row['measure']),'measure_name');
 				$row['created_on'] = date('d F Y',strtotime($row['created_on']));
 				$row['tag_name'] = $this->crud_global->GetField('pmm_tags',array('id'=>$row['tag_id']),'tag_name');
				$arr_data[] = $row;
			}

		}

		$data['data'] = $arr_data;
        $html = $this->load->view('pmm/materials_print',$data,TRUE);

        
        $pdf->SetTitle('Materials');
        $pdf->nsi_html($html);
        $pdf->Output('Materials.pdf', 'I');
	
	}

	public function tools_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('P');

		$arr_data = array();
		$this->db->where('status !=','DELETED');
		$this->db->order_by('tool','asc');
		$query = $this->db->get('pmm_tools');

		if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
				$row['no'] = $key+1;
				$name = "'".$row['tool']."'";
				$total_cost = $this->db->select('SUM(cost) as total')->get_where('pmm_tool_detail',array('status'=>'PUBLISH','tool_id'=>$row['id']))->row_array();
				$row['total_cost'] = number_format($total_cost['total'],2,',','.');
				$row['measure'] = $this->crud_global->GetField('pmm_measures',array('id'=>$row['measure_id']),'measure_name');
				$row['tag'] = $this->crud_global->GetField('pmm_tags',array('id'=>$row['tag_id']),'tag_name');
 				$row['created_on'] = date('d F Y',strtotime($row['created_on']));
				$row['actions'] = '<a href="javascript:void(0);" onclick="FormDetail('.$row['id'].','.$name.')" class="btn btn-info"><i class="fa fa-search"></i> Detail</a> <a href="javascript:void(0);" onclick="OpenForm('.$row['id'].')" class="btn btn-primary"><i class="fa fa-edit"></i> </a> <a href="javascript:void(0);" onclick="DeleteData('.$row['id'].')" class="btn btn-danger"><i class="fa fa-close"></i> </a>';
				$arr_data[] = $row;
			}

		}
		$data['data'] = $arr_data;
        $html = $this->load->view('pmm/tools_print',$data,TRUE);

        
        $pdf->SetTitle('Tools');
        $pdf->nsi_html($html);
        $pdf->Output('Tools.pdf', 'I');
	
	}

	public function measures_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('P');

		$arr_data = array();
		$this->db->where('status !=','DELETED');
		$query = $this->db->get('pmm_measures');
		$data['data'] = $query->result_array();
        $html = $this->load->view('pmm/measures_print',$data,TRUE);

        
        $pdf->SetTitle('Measures');
        $pdf->nsi_html($html);
        $pdf->Output('Measures.pdf', 'I');
	
	}

	public function composition_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('P');

		$tag_id = $this->input->get('filter_product');
		$arr_tag = array();
		if(!empty($tag_id)){
			$query_tag = $this->db->select('id')->get_where('pmm_product',array('status'=>'PUBLISH','tag_id'=>$tag_id))->result_array();
			foreach ($query_tag as $pid) {
				$arr_tag[] = $pid['id'];
			}
		}
		$this->db->select('pc.*, pp.product');
		$this->db->where('pc.status !=','DELETED');
		if(!empty($tag_id)){
			$this->db->where_in('product_id',$arr_tag);
		}
		$this->db->join('pmm_product pp','pc.product_id = pp.id','left');
		$this->db->order_by('pc.created_on','desc');
		$query = $this->db->get('pmm_composition pc');
		$data['data'] = $query->result_array();
        $html = $this->load->view('pmm/composition_print',$data,TRUE);

        
        $pdf->SetTitle('Composition');
        $pdf->nsi_html($html);
        $pdf->Output('Composition.pdf', 'I');
	
	}

	public function supplier_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		$pdf->AddPage('P');

		$arr_data = array();
		$this->db->where('status !=','DELETED');
		$this->db->order_by('name','asc');
		$query = $this->db->get('pmm_supplier');
		$data['data'] = $query->result_array();
        $html = $this->load->view('pmm/supplier_print',$data,TRUE);

        
        $pdf->SetTitle('Supplier');
        $pdf->nsi_html($html);
        $pdf->Output('Supplier.pdf', 'I');
	
	}

	public function client_print()
	{
		$arr_data = array();
		$this->db->where('status !=','DELETED');
		$this->db->order_by('client_name','asc');
		$query = $this->db->get('pmm_client');
		$data['data'] = $query->result_array();	
	
		$this->load->library('pdf');
		$this->pdf->setPaper('A4', 'potrait');
		$this->pdf->filename = "laporan-client.pdf";
		$this->pdf->load_view('pmm/client_print', $data);
	
	}
	
	public function slump_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('P');

		$arr_data = array();
		$this->db->where('status !=','DELETED');
		$query = $this->db->get('pmm_slump');
		$data['data'] = $query->result_array();
        $html = $this->load->view('pmm/slump_print',$data,TRUE);

        
        $pdf->SetTitle('Slump');
        $pdf->nsi_html($html);
        $pdf->Output('Slump.pdf', 'I');
	
	}

	public function tags_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('P');

		$arr_data = array();
		$type = $this->input->get('type');
		$this->db->where('status !=','DELETED');
		if(!empty($type)){
			$this->db->where('tag_type',$type);
		}
		$this->db->order_by('tag_name','asc');
		$query = $this->db->get('pmm_tags');

		if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
				$row['no'] = $key+1;
				$price = 0;
				if($row['tag_type'] == 'MATERIAL'){
					$get_price = $this->db->select('AVG(cost) as cost')->get_where('pmm_materials',array('status'=>'PUBLISH','tag_id'=>$row['id']))->row_array();
					if(!empty($get_price)){
						$price = $get_price['cost'];
					}
				}
				$row['price'] = number_format($price,2,',','.');
				$arr_data[] = $row;
			}

		}
		$data['data'] = $arr_data;
        $html = $this->load->view('pmm/tags_print',$data,TRUE);

        
        $pdf->SetTitle('Tags');
        $pdf->nsi_html($html);
        $pdf->Output('Tags.pdf', 'I');
	
	}

	public function production_planning_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		$pdf->AddPage('P');

		$arr_data = array();
		$this->db->where('status !=','DELETED');
		$this->db->order_by('created_on','desc');
		$query = $this->db->get('pmm_schedule');
		if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
				$row['no'] = $key+1;
				$arr_date = explode(' - ', $row['schedule_date']);
				$row['schedule_name'] = $row['schedule_name'];
				$row['client_name'] = $this->crud_global->GetField('pmm_client',array('id'=>$row['client_id']),'client_name');
				$row['schedule_date'] = date('d F Y',strtotime($arr_date[0])).' - '.date('d F Y',strtotime($arr_date[1]));
				$row['created_on'] = date('d F Y',strtotime($row['created_on']));
				$row['week_1'] = $this->pmm_model->TotalSPOWeek($row['id'],1);
				$row['week_2'] = $this->pmm_model->TotalSPOWeek($row['id'],2);
				$row['week_3'] = $this->pmm_model->TotalSPOWeek($row['id'],3);
				$row['week_4'] = $this->pmm_model->TotalSPOWeek($row['id'],4);
				$row['status'] = $this->pmm_model->GetStatus($row['status']);
				
				$arr_data[] = $row;
			}

		}
		$data['data'] = $arr_data;
        $html = $this->load->view('pmm/production_planning_print',$data,TRUE);

        
        $pdf->SetTitle('cetak_poduction_planning');
        $pdf->nsi_html($html);
        $pdf->Output('production_planning.pdf', 'I');
	
	}
	
	public function receipt_matuse_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		$pdf->AddPage('P');

		$arr_data = array();
		$supplier_id = $this->input->get('supplier_id');
		$purchase_order_no = $this->input->get('purchase_order_no');
		$filter_material = $this->input->get('filter_material');
		$start_date = false;
		$end_date = false;
		$total = 0;
		$total_convert = 0;
		$date = $this->input->get('filter_date');
		if(!empty($date)){
			$arr_date = explode(' - ',$date);
			$start_date = date('Y-m-d',strtotime($arr_date[0]));
			$end_date = date('Y-m-d',strtotime($arr_date[1]));
			$filter_date = date('d F Y',strtotime($arr_date[0])).' - '.date('d F Y',strtotime($arr_date[1]));

			
			$data['filter_date'] = $filter_date;

			$arr_filter_mats = array();

			$no = 1;
			$this->db->select('ppo.supplier_id,prm.measure,ps.name,SUM(prm.volume) as total, SUM((prm.cost / prm.convert_value) * prm.display_volume) as total_price, prm.convert_value, SUM(prm.volume * prm.convert_value) as total_convert');
			if(!empty($start_date) && !empty($end_date)){
	            $this->db->where('prm.date_receipt >=',$start_date);
	            $this->db->where('prm.date_receipt <=',$end_date);
	        }
	        if(!empty($supplier_id)){
	            $this->db->where('ppo.supplier_id',$supplier_id);
	        }
	        if(!empty($filter_material)){
	            $this->db->where_in('prm.material_id',$filter_material);
	        }
	        if(!empty($purchase_order_no)){
	            $this->db->where('prm.purchase_order_id',$purchase_order_no);
	        }
			$this->db->where('ps.status','PUBLISH');
			$this->db->join('pmm_supplier ps','ppo.supplier_id = ps.id','left');
			$this->db->join('pmm_receipt_material prm','ppo.id = prm.purchase_order_id');
			$this->db->group_by('ppo.supplier_id');
			$this->db->order_by('ps.name','asc');
			$query = $this->db->get('pmm_purchase_order ppo');
			
			if($query->num_rows() > 0){

				foreach ($query->result_array() as $key => $sups) {

					$mats = array();
					$materials = $this->pmm_model->GetReceiptMatUse($sups['supplier_id'],$purchase_order_no,$start_date,$end_date,$arr_filter_mats);
					if(!empty($materials)){
						foreach ($materials as $key => $row) {
							$arr['no'] = $key + 1;
							$arr['measure'] = $row['measure'];
							$arr['material_name'] = $row['material_name'];
							
							$arr['real'] = number_format($row['total'],2,',','.');
							$arr['convert_value'] = number_format($row['convert_value'],2,',','.');
							$arr['total_convert'] = number_format($row['total_convert'],2,',','.');
							$arr['total_price'] = number_format($row['total_price'],2,',','.');
							$mats[] = $arr;
						}
						$sups['mats'] = $mats;
						$total += $sups['total_price'];
						$total_convert += $sups['total_convert'];
						$sups['no'] =$no;
						$sups['real'] = number_format($sups['total'],2,',','.');
						$sups['convert_value'] = number_format($sups['convert_value'],2,',','.');
						$sups['total_convert'] = number_format($sups['total_convert'],2,',','.');
						$sups['total_price'] = number_format($sups['total_price'],2,',','.');
						$sups['measure'] = '';
						$arr_data[] = $sups;
						$no++;
					}
					
					
				}
			}
			if(!empty($filter_material)){
				$total_convert = number_format($total_convert,0,',','.');
			}else {
				$total_convert = '';
			}

			
			$data['data'] = $arr_data;
			$data['total'] = $total;
			$data['total_convert'] = $total_convert;
	        $html = $this->load->view('pmm/receipt_matuse_report_print',$data,TRUE);

	        
	        $pdf->SetTitle('Penerimaan Bahan');
	        $pdf->nsi_html($html);
	        $pdf->Output('Penerimaan-Bahan.pdf', 'I');
		}else {
			echo 'Please Filter Date First';
		}
	
	}

	public function data_material_usage()
	{
		$supplier_id = $this->input->post('supplier_id');
		$filter_material = $this->input->post('filter_material');
		$start_date = false;
		$end_date = false;
		$total = 0;
		$total_convert = 0;
		$query = array();
		$date = $this->input->post('filter_date');
		if(!empty($date)){
			$arr_date = explode(' - ',$date);
			$start_date = date('Y-m-d',strtotime($arr_date[0]));
			$end_date = date('Y-m-d',strtotime($arr_date[1]));
		}

    	$this->db->where(array(
    		'status'=>'PUBLISH',
    	));
    	if(!empty($filter_material)){
    		$this->db->where('id',$filter_material);
    	}
    	$this->db->order_by('nama_produk','asc');
    	$tags = $this->db->get_where('produk',array('status'=>'PUBLISH','bahanbaku'=>1))->result_array();

    	if(!empty($tags)){
    		?>
	        <table class="table table-center table-bordered table-condensed">
	        	<thead>
	        		<tr >
		        		<th class="text-center">No</th>
		        		<th class="text-center">Bahan</th>
		        		<th class="text-center">Rekanan</th>
		        		<th class="text-center">Satuan</th>
		        		<th class="text-center">Volume</th>
		        		<th class="text-center">Total</th>
		        	</tr>	
	        	</thead>
	        	<tbody>
	        		<?php
	        		$no=1;
	        		$total_total = 0;
	        		foreach ($tags as $tag) {
		    			$now = $this->pmm_reports->SumMaterialUsage($tag['id'],array($start_date,$end_date));

		    			
		    			$measure_name = $this->crud_global->GetField('pmm_measures',array('id'=>$tag['satuan']),'measure_name');
		    			if($now['volume'] > 0){
				        	
				        	?>
				        	<tr class="active" style="font-weight:bold;">
				        		<td class="text-center"><?php echo $no;?></td>
				        		<td colspan="2"><?php echo $tag['nama_produk'];?></td>
				        		<td class="text-center"><?php echo $measure_name;?></td>
				        		<td class="text-right"><?php echo number_format($now['volume'],2,',','.');?></td>
				        		<td class="text-right"><span class="pull-left">Rp. </span><?php echo number_format($now['total'],0,',','.');?></td>
				        	</tr>
				        	<?php
				        	$now_new = $this->pmm_reports->MatUseBySupp($tag['id'],array($start_date,$end_date),$now['volume'],$now['total']);
				        	if(!empty($now_new)){
				        		$no_2 = 1;
				        		foreach ($now_new as $new) {
					        		
					        		?>
					        		<!--<tr>
					        			<td class="text-center"><?= $no.'.'.$no_2;?></td>
					        			<td></td>
					        			<td><?php echo $new['supplier'];?></td>
					        			<td class="text-center"><?php echo $measure_name;?></td>
						        		<td class="text-right"><?php echo number_format($new['volume'],2,',','.');?></td>
						        		<td class="text-right"><span class="pull-left">Rp. </span><?php echo number_format($new['total'],0,',','.');?></td>
					        		</tr>-->
					        		<?php
					        		$no_2 ++;
					        	}
				        	}
				        	
				        	?>
				        	<tr style="height: 20px">
				        		<td colspan="6"></td>
				        	</tr>
				        	<?php

				        	$no++;
				        	$total_total += $now['total'];
					        
		    			}
		    		}
	        		?>
	        		<tr>
	        			<th colspan="5" class="text-right">TOTAL</th>
	        			<th class="text-right"><span class="pull-left">Rp. </span><?php echo number_format($total_total,0,',','.');?></th>
	        		</tr>
	        	</tbody>
	        </table>
	        <?php	
    	}


	}


	public function material_usage_prod_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		$pdf->AddPage('P');

		$arr_data = array();
		$supplier_id = $this->input->get('supplier_id');
		$purchase_order_no = $this->input->get('purchase_order_no');
		$filter_material = $this->input->get('filter_material');
		$start_date = false;
		$end_date = false;
		$total = 0;
		$total_convert = 0;
		$date = $this->input->get('filter_date');
		if(!empty($date)){
			$arr_date = explode(' - ',$date);
			$start_date = date('Y-m-d',strtotime($arr_date[0]));
			$end_date = date('Y-m-d',strtotime($arr_date[1]));
			$filter_date = date('d F Y',strtotime($arr_date[0])).' - '.date('d F Y',strtotime($arr_date[1]));

			
			$data['filter_date'] = $filter_date;

			$no = 1;
	    	$this->db->where(array(
	    		'status'=>'PUBLISH',
				'bahanbaku'=>1,
	    	));
	    	if(!empty($filter_material)){
	    		$this->db->where('id',$filter_material);
	    	}
	    	$this->db->order_by('nama_produk','asc');
	    	$query = $this->db->get('produk');
			
			if($query->num_rows() > 0){

				foreach ($query->result_array() as $key => $tag) {

					$now = $this->pmm_reports->SumMaterialUsage($tag['id'],array($start_date,$end_date));
	    			$measure_name = $this->crud_global->GetField('pmm_measures',array('id'=>$tag['satuan']),'measure_name');
	    			if($now['volume'] > 0){
	    				$tags['tag_name'] = $tag['nama_produk'];
	    				$tags['no'] = $no;
	    				$tags['volume'] = number_format($now['volume'],2,',','.');
	    				$tags['total'] = number_format($now['total'],2,',','.');
	    				$tags['measure'] = $measure_name;

	    				$now_new = $this->pmm_reports->MatUseBySupp($tag['id'],array($start_date,$end_date),$now['volume'],$now['total']);
			        	if(!empty($now_new)){
			        		$no_2 = 1;
			        		$supps = array();
			        		foreach ($now_new as $new) {

			        			$arr_supps['no'] = $no_2;
			        			$arr_supps['supplier'] = $new['supplier'];
			        			$arr_supps['volume'] = number_format($new['volume'],2,',','.');
			        			$arr_supps['total'] = number_format($new['total'],2,',','.');
			        			$supps[] = $arr_supps;
			        			$no_2 ++;
			        		}

			        		$tags['supps'] = $supps;
			        	}

						$arr_data[] = $tags;	
						$total += $now['total'];
	    			}
					$no++;
					
				}
			}

			
			$data['data'] = $arr_data;
			$data['total'] = $total;
			$data['custom_date'] = $this->input->get('custom_date');
	        $html = $this->load->view('produksi/material_usage_prod_print',$data,TRUE);

	        
	        $pdf->SetTitle('pemakaian-material');
	        $pdf->nsi_html($html);
	        $pdf->Output('pemakaian-material', 'I');
		}else {
			echo 'Please Filter Date First';
		}
	
	}

	public function exec()
	{
		
	}

	public function laporan_pemakaian_peralatan($arr_date)
	{
		$data = array();
		
		$arr_date = $this->input->post('filter_date');
		$arr_filter_date = explode(' - ', $arr_date);
		$date1 = '';
		$date2 = '';

		if(count($arr_filter_date) == 2){
			$date1 	= date('Y-m-d',strtotime($arr_filter_date[0]));
			$date2 	= date('Y-m-d',strtotime($arr_filter_date[1]));
			$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]));
		}
		
		?>
		
		<table class="table table-bordered" width="100%">
			<style type="text/css">
				table tr.table-active{
					background-color: #F0F0F0;
					font-size: 12px;
					font-weight: bold;
					color: black;
				}
					
				table tr.table-active2{
					background-color: #E8E8E8;
					font-size: 12px;
					font-weight: bold;
				}
					
				table tr.table-active3{
					font-size: 12px;
					background-color: #F0F0F0;
				}
					
				table tr.table-active4{
					background-color: #e69500;
					font-weight: bold;
					font-size: 12px;
					color: black;
				}
				table tr.table-active5{
					background-color: #E8E8E8;
					text-decoration: underline;
					font-size: 12px;
					font-weight: bold;
					color: red;
				}
				table tr.table-activeago1{
					background-color: #ffd966;
					font-weight: bold;
					font-size: 12px;
					color: black;
				}
				table tr.table-activeopening{
					background-color: #2986cc;
					font-weight: bold;
					font-size: 12px;
					color: black;
				}
			</style>
			
			<?php

			$pembelian = $this->db->select('
			pn.nama, po.no_po, po.subject, prm.measure, SUM(prm.volume) as volume, SUM(prm.price) / SUM(prm.volume) as harga_satuan, SUM(prm.price) as price')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('penerima pn', 'po.supplier_id = pn.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("prm.material_id in (12,13,14,15,16,23,24,25)")
			->where("po.status in ('PUBLISH','CLOSED')")
			->order_by('pn.nama','asc')
			->group_by('prm.harga_satuan')
			->get()->result_array();

			$total_nilai = 0;

			foreach ($pembelian as $x){
				$total_nilai += $x['price'];
			}

			$akumulasi_bbm = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar_2 as total_nilai_keluar_2')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi between '$date1' and '$date2')")
			->get()->result_array();

			$total_akumulasi_bbm = 0;

			foreach ($akumulasi_bbm as $b){
				$total_akumulasi_bbm += $b['total_nilai_keluar_2'];
			}

			$total_nilai_bbm = $total_akumulasi_bbm;

			$total_nilai_all = 0;
			$total_nilai_all = $total_nilai + $total_nilai_bbm;

			$total_insentif_tm = 0;

			$insentif_tm = $this->db->select('pb.memo as memo, sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->where("pdb.akun = 220")
			->where("status = 'PAID'")
			->where("(tanggal_transaksi between '$date1' and '$date2')")
			->group_by('pdb.id')
			->get()->result_array();

			$total_insentif_tm = 0;

			foreach ($insentif_tm as $y){
				$total_insentif_tm += $y['total'];
			}

			$total_insentif_tm_all = 0;
			$total_insentif_tm_all = $total_insentif_tm;

			$total_nilai = $total_nilai_all + $total_insentif_tm_all;

			?>
			
			<tr class="table-active4">
				<th width="35%" class="text-center">REKANAN</th>
				<th width="20%" class="text-center">URAIAN</th>
				<th width="7%" class="text-center">SATUAN</th>
				<th width="13%" class="text-center">VOLUME</th>
				<th width="15%" class="text-center">HARGA SATUAN</th>
				<th width="10%" class="text-center">NILAI</th>
	        </tr>
			<?php foreach ($pembelian as $x): ?>
			<tr>
				<th class="text-left">&bull; <?= $x['nama'] ?></th>
				<th class="text-left"><?= $x['subject'] ?></th>
				<th class="text-center"><?= $x['measure'] ?></th>
				<th class="text-right"><?php echo number_format($x['volume'],2,',','.');?></th>
				<th class="text-right"><?php echo number_format($x['harga_satuan'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($x['price'],0,',','.');?></th>
			</tr>
			<?php endforeach; ?>
			<tr>
				<th class="text-left">&bull; BBM Solar</th>
				<th class="text-left"></th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($total_nilai_bbm,0,',','.');?></th>
			</tr>
			<?php foreach ($insentif_tm as $y): ?>
			<tr>
				<th class="text-left" colspan="5">&bull; <?= $y['memo'] ?></th>
				<th class="text-right"><?php echo number_format($y['total'],0,',','.');?></th>
			</tr>
			<?php endforeach; ?>
			<tr class="table-active2">
				<th class="text-center" colspan="5">TOTAL</th>
				<th class="text-right"><?php echo number_format($total_nilai,0,',','.');?></th>
			</tr>
	    </table>
		<?php
	}

	public function table_evaluasi_rap($arr_date)
	{
		$data = array();
		
		$arr_date = $this->input->post('filter_date');
		$arr_filter_date = explode(' - ', $arr_date);
		$date1 = '';
		$date2 = '';

		if(count($arr_filter_date) == 2){
			$date1 	= date('Y-m-d',strtotime($arr_filter_date[0]));
			$date2 	= date('Y-m-d',strtotime($arr_filter_date[1]));
			$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]));
		}
		
		?>
		
		<table class="table table-bordered" width="100%">
			<style type="text/css">
				table tr.table-active{
					background-color: #F0F0F0;
					font-size: 12px;
					font-weight: bold;
					color: black;
				}
					
				table tr.table-active2{
					background-color: #E8E8E8;
					font-size: 12px;
					font-weight: bold;
				}
					
				table tr.table-active3{
					font-size: 12px;
					background-color: #F0F0F0;
				}
					
				table tr.table-active4{
					background-color: #e69500;
					font-weight: bold;
					font-size: 12px;
					color: black;
				}
				table tr.table-active5{
					background-color: #cccccc;
					font-weight: bold;
					font-size: 12px;
					color: black;
				}
				table tr.table-activeago1{
					background-color: #ffd966;
					font-weight: bold;
					font-size: 12px;
					color: black;
				}
				table tr.table-activeopening{
					background-color: #2986cc;
					font-weight: bold;
					font-size: 12px;
					color: black;
				}
			</style>

			<!-- RAP -->

			<?php

			$rap = $this->db->select('rap.id, rap.tanggal_rap, rap.nomor_rap, SUM(rap.total_bahan) as total_bahan, SUM(rap.total_alat) as total_alat, SUM(rap.total_overhead) as total_overhead, SUM(rap.total_biaya_admin) as total_biaya_admin, SUM(rap.total_diskonto) as total_diskonto, rap.status')
			->from('rap rap')
			->where('status','PUBLISH')
			->where("rap.tanggal_rap between '$date1' and '$date2'")
			->where('rap.status','PUBLISH')
			->get()->row_array();

			$total_bahan = $rap['total_bahan'];
			$total_alat = $rap['total_alat'];
			$total_overhead = $rap['total_overhead'];
			$total_biaya_admin = $rap['total_biaya_admin'];
			$total_diskonto = $rap['total_diskonto'];

			?>

			<!-- END RAP -->

			<!-- REALISASI -->

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

			$total_penjualan = 0;
			$total_volume = 0;
			$measure = 0;

			foreach ($penjualan as $x){
				$total_penjualan += $x['price'];
				$total_volume += $x['volume'];
			}

			$total_penjualan_all = 0;
			$total_penjualan_all = $total_penjualan;

			//BAHAN		
			$akumulasi = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar as total_nilai_keluar')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi between '$date1' and '$date2')")
			->get()->result_array();

			$total_akumulasi = 0;

			foreach ($akumulasi as $a){
				$total_akumulasi += $a['total_nilai_keluar'];
			}

			$total_nilai = $total_akumulasi;
			//END BAHAN

			//ALAT
			$nilai_alat = $this->db->select('SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("prm.material_id in (12,13,14,15,16)")
			->where("po.status in ('PUBLISH','CLOSED')")
			->get()->row_array();

			$akumulasi_bbm = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar_2 as total_nilai_keluar_2')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi between '$date1' and '$date2')")
			->get()->result_array();

			$total_akumulasi_bbm = 0;

			foreach ($akumulasi_bbm as $b){
				$total_akumulasi_bbm += $b['total_nilai_keluar_2'];
			}

			$total_nilai_bbm = $total_akumulasi_bbm;

			$total_insentif_tm = 0;

			$insentif_tm = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->where("pdb.akun = 220")
			->where("status = 'PAID'")
			->where("(tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$total_insentif_tm = $insentif_tm['total'];

			$alat = $nilai_alat['nilai'] + $total_akumulasi_bbm + $total_insentif_tm;
			//END ALAT

			//OVERHEAD
			$overhead = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',15)
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$overhead_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->where("pdb.akun in (199)")
			->where("status = 'PAID'")
			->where("(tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$overhead = $overhead['total'] + $overhead_jurnal['total'];
			//END OVERHEAD

			//DISKONTO
			$diskonto = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 168")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$diskonto = $diskonto['total'];

			$bahan = $total_nilai;
			$alat = $alat;
			$overhead = $overhead;
			$biaya_admin = 0;
			$diskonto = $diskonto;
			//END DISKONTO

			?>
			
			<!-- END REALISASI -->

			<!-- EVALUASI -->

			<?php

			$evaluasi_total_bahan = $total_bahan - $bahan;
			$evaluasi_total_alat = $total_alat - $alat;
			$evaluasi_total_overhead = $total_overhead - $overhead;
			$evaluasi_total_biaya_admin = $total_biaya_admin - $biaya_admin;
			$evaluasi_total_diskonto = $total_diskonto - $diskonto;

			//TOTAL
			$total_rap = $total_bahan + $total_alat + $total_overhead + $total_biaya_admin + $total_diskonto;
			$total_realisasi = $bahan + $alat + $overhead + $biaya_admin + $diskonto;
			$total_evaluasi = $evaluasi_total_bahan + $evaluasi_total_alat + $evaluasi_total_overhead + $evaluasi_total_biaya_admin + $evaluasi_total_diskonto;
			?>


			<!-- END EVALUASI -->
			
			<tr class="table-active4">
				<th width="5%" class="text-center">NO.</th>
				<th width="35%" class="text-center">URAIAN</th>
				<th width="20%" class="text-center">RAP</th>
				<th width="20%" class="text-center">REALISASI</th>
				<th width="20%" class="text-center">EVALUASI</th>
	        </tr>
			<?php
				$styleColorA = $evaluasi_total_bahan < 0 ? 'color:red' : 'color:black';
				$styleColorB = $evaluasi_total_alat < 0 ? 'color:red' : 'color:black';
				$styleColorC = $evaluasi_total_overhead < 0 ? 'color:red' : 'color:black';
				$styleColorD = $evaluasi_total_biaya_admin < 0 ? 'color:red' : 'color:black';
				$styleColorE = $evaluasi_total_diskonto < 0 ? 'color:red' : 'color:black';
				$styleColorF = $total_evaluasi < 0 ? 'color:red' : 'color:black';
			?>
			<tr class="table-active3">
				<th class="text-center">1.</th>			
				<th class="text-left">TOTAL BAHAN</th>
				<th class="text-right"><?php echo number_format($total_bahan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($bahan,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorA ?>"><?php echo number_format($evaluasi_total_bahan,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">2.</th>			
				<th class="text-left">TOTAL ALAT</th>
				<th class="text-right"><?php echo number_format($total_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($alat,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorB ?>"><?php echo number_format($evaluasi_total_alat,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">3.</th>			
				<th class="text-left">TOTAL OVERHEAD</th>
				<th class="text-right"><?php echo number_format($total_overhead,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($overhead,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorC ?>"><?php echo number_format($evaluasi_total_overhead,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">4.</th>			
				<th class="text-left">TOTAL BIAYA ADMIN</th>
				<th class="text-right"><?php echo number_format($total_biaya_admin,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($biaya_admin,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorD ?>"><?php echo number_format($evaluasi_total_biaya_admin,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">5.</th>			
				<th class="text-left">TOTAL DISKONTO</th>
				<th class="text-right"><?php echo number_format($total_diskonto,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($diskonto,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorE ?>"><?php echo number_format($evaluasi_total_diskonto,0,',','.');?></th>
	        </tr>
			<tr class="table-active5">		
				<th class="text-right" colspan="2">TOTAL</th>
				<th class="text-right"><?php echo number_format($total_rap,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_realisasi,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorF ?>"><?php echo number_format($total_evaluasi,0,',','.');?></th>
	        </tr>
	    </table>
		<?php
	}

	public function buku_besar($arr_date)
	{
		$data = array();
		
		$arr_date = $this->input->post('filter_date');
		$arr_filter_date = explode(' - ', $arr_date);
		$date1 = '';
		$date2 = '';

		if(count($arr_filter_date) == 2){
			$date1 	= date('Y-m-d',strtotime($arr_filter_date[0]));
			$date2 	= date('Y-m-d',strtotime($arr_filter_date[1]));
			$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]));
		}
		
		?>
		
		<table class="table table-bordered" width="100%">
		 <style type="text/css">
			table tr.table-active{
				background-color: #F0F0F0;
				font-size: 12px;
				font-weight: bold;
			}
				
			table tr.table-active2{
				background-color: #A9A9A9;
				font-size: 12px;
				font-weight: bold;
			}
				
			table tr.table-active3{
				font-size: 12px;
			}
				
			table tr.table-active4{
				background-color: #D3D3D3;
				font-weight: bold;
				font-size: 12px;
				color: black;
			}
			
		 </style>
	        <tr class="table-active4">
	            <th class="text-center">Periode</th>
	            <th class="text-center" colspan="3"><?php echo $filter_date;?></th>
	        </tr>

			<?php

			//kas
			$transactions = $this->db->select('t.id as transaction_id, t.akun, t.tanggal_transaksi, t.transaksi, b.nomor_transaksi as no_trx_1, j.nomor_transaksi as no_trx_2, tu.nomor_transaksi as no_trx_3, tf.nomor_transaksi as no_trx_4, pdb.deskripsi as dex_1, j.memo as dex_2, tu.memo as dex_3, tf.memo as dex_4, t.debit as debit, t.kredit as kredit')
			->from('transactions t ')
			->join('pmm_biaya b','t.biaya_id = b.id','left')
			->join('pmm_detail_biaya pdb','b.id = pdb.biaya_id','left')
			->join('pmm_jurnal_umum j','t.jurnal_id = j.id','left')
			->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
			->join('pmm_terima_uang tu','t.terima_id = tu.id','left')
			->join('pmm_transfer tf','t.transfer_id = tf.id','left')
			->where("(t.tanggal_transaksi between '$date1' and '$date2')")
			->where('t.akun',1)
			->order_by('t.tanggal_transaksi','asc')
			->order_by('t.id','asc')
			->group_by('t.id')
			->get()->result_array();

			$saldo = 0;
			$jumlah_no_transaksi = 0;
			$jumlah_deskripsi = 0;
			$jumlah_debit = 0;
			$jumlah_kredit = 0;

			$total_kredit_all = 0;
			$total_debit_all = 0;
			$saldo_akhir_all = 0;

			foreach ($transactions as $x){
				$total_debit_all += $x['debit'];
				$total_kredit_all += $x['kredit'];
			}

			$saldo_akhir_all = $total_debit_all - $total_kredit_all;
			//kas

			//bank_kantor_pusat
			$bank_kantor_pusat = $this->db->select('t.id as transaction_id, t.akun, t.tanggal_transaksi, t.transaksi, b.nomor_transaksi as no_trx_1, j.nomor_transaksi as no_trx_2, tu.nomor_transaksi as no_trx_3, tf.nomor_transaksi as no_trx_4, pdb.deskripsi as dex_1, j.memo as dex_2, tu.memo as dex_3, tf.memo as dex_4, t.debit as debit, t.kredit as kredit')
			->from('transactions t ')
			->join('pmm_biaya b','t.biaya_id = b.id','left')
			->join('pmm_detail_biaya pdb','b.id = pdb.biaya_id','left')
			->join('pmm_jurnal_umum j','t.jurnal_id = j.id','left')
			->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
			->join('pmm_terima_uang tu','t.terima_id = tu.id','left')
			->join('pmm_transfer tf','t.transfer_id = tf.id','left')
			->where("(t.tanggal_transaksi between '$date1' and '$date2')")
			->where('t.akun',217)
			->order_by('t.tanggal_transaksi','asc')
			->order_by('t.id','asc')
			->group_by('t.id')
			->get()->result_array();

			$saldo_bank_kantor_pusat = 0;
			$jumlah_no_transaksi_bank_kantor_pusat = 0;
			$jumlah_deskripsi_bank_kantor_pusat = 0;
			$jumlah_debit_bank_kantor_pusat = 0;
			$jumlah_kredit_bank_kantor_pusat = 0;

			$total_kredit_bank_kantor_pusat_all = 0;
			$total_debit_bank_kantor_pusat_all = 0;
			$saldo_akhir_bank_kantor_pusat_all = 0;

			foreach ($bank_kantor_pusat as $x){
				$total_debit_bank_kantor_pusat_all += $x['debit'];
				$total_kredit_bank_kantor_pusat_all += $x['kredit'];
			}

			$saldo_akhir_bank_kantor_pusat_all = $total_debit_bank_kantor_pusat_all - $total_kredit_bank_kantor_pusat_all;
			//bank_kantor_pusat

			//hutang_nindya
			$hutang_nindya = $this->db->select('t.id as transaction_id, t.akun, t.tanggal_transaksi, t.transaksi, b.nomor_transaksi as no_trx_1, j.nomor_transaksi as no_trx_2, tu.nomor_transaksi as no_trx_3, tf.nomor_transaksi as no_trx_4, pdb.deskripsi as dex_1, j.memo as dex_2, tu.memo as dex_3, tf.memo as dex_4, t.debit as debit, t.kredit as kredit')
			->from('transactions t ')
			->join('pmm_biaya b','t.biaya_id = b.id','left')
			->join('pmm_detail_biaya pdb','b.id = pdb.biaya_id','left')
			->join('pmm_jurnal_umum j','t.jurnal_id = j.id','left')
			->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
			->join('pmm_terima_uang tu','t.terima_id = tu.id','left')
			->join('pmm_transfer tf','t.transfer_id = tf.id','left')
			->where("(t.tanggal_transaksi between '$date1' and '$date2')")
			->where('t.akun',223)
			->order_by('t.tanggal_transaksi','asc')
			->order_by('t.id','asc')
			->group_by('t.id')
			->get()->result_array();

			$saldo_hutang_nindya = 0;
			$jumlah_no_transaksi_hutang_nindya = 0;
			$jumlah_deskripsi_hutang_nindya = 0;
			$jumlah_debit_hutang_nindya = 0;
			$jumlah_kredit_hutang_nindya = 0;

			$total_kredit_hutang_nindya_all = 0;
			$total_debit_hutang_nindya_all = 0;
			$saldo_akhir_hutang_nindya_all = 0;

			foreach ($hutang_nindya as $x){
				$total_debit_hutang_nindya_all += $x['debit'];
				$total_kredit_hutang_nindya_all += $x['kredit'];
			}

			$saldo_akhir_hutang_nindya_all = $total_debit_hutang_nindya_all - $total_kredit_hutang_nindya_all;
			//hutang_nindya

			//hutang_sinar
			$hutang_sinar = $this->db->select('t.id as transaction_id, t.akun, t.tanggal_transaksi, t.transaksi, b.nomor_transaksi as no_trx_1, j.nomor_transaksi as no_trx_2, tu.nomor_transaksi as no_trx_3, tf.nomor_transaksi as no_trx_4, pdb.deskripsi as dex_1, j.memo as dex_2, tu.memo as dex_3, tf.memo as dex_4, t.debit as debit, t.kredit as kredit')
			->from('transactions t ')
			->join('pmm_biaya b','t.biaya_id = b.id','left')
			->join('pmm_detail_biaya pdb','b.id = pdb.biaya_id','left')
			->join('pmm_jurnal_umum j','t.jurnal_id = j.id','left')
			->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
			->join('pmm_terima_uang tu','t.terima_id = tu.id','left')
			->join('pmm_transfer tf','t.transfer_id = tf.id','left')
			->where("(t.tanggal_transaksi between '$date1' and '$date2')")
			->where('t.akun',224)
			->order_by('t.tanggal_transaksi','asc')
			->order_by('t.id','asc')
			->group_by('t.id')
			->get()->result_array();

			$saldo_hutang_sinar = 0;
			$jumlah_no_transaksi_hutang_sinar = 0;
			$jumlah_deskripsi_hutang_sinar = 0;
			$jumlah_debit_hutang_sinar = 0;
			$jumlah_kredit_hutang_sinar = 0;

			$total_kredit_hutang_sinar_all = 0;
			$total_debit_hutang_sinar_all = 0;
			$saldo_akhir_hutang_sinar_all = 0;

			foreach ($hutang_sinar as $x){
				$total_debit_hutang_sinar_all += $x['debit'];
				$total_kredit_hutang_sinar_all += $x['kredit'];
			}

			$saldo_akhir_hutang_sinar_all = $total_debit_hutang_sinar_all - $total_kredit_hutang_sinar_all;
			//hutang_sinar

			//hutang_lain_lain
			$hutang_lain_lain = $this->db->select('t.id as transaction_id, t.akun, t.tanggal_transaksi, t.transaksi, b.nomor_transaksi as no_trx_1, j.nomor_transaksi as no_trx_2, tu.nomor_transaksi as no_trx_3, tf.nomor_transaksi as no_trx_4, pdb.deskripsi as dex_1, j.memo as dex_2, tu.memo as dex_3, tf.memo as dex_4, t.debit as debit, t.kredit as kredit')
			->from('transactions t ')
			->join('pmm_biaya b','t.biaya_id = b.id','left')
			->join('pmm_detail_biaya pdb','b.id = pdb.biaya_id','left')
			->join('pmm_jurnal_umum j','t.jurnal_id = j.id','left')
			->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
			->join('pmm_terima_uang tu','t.terima_id = tu.id','left')
			->join('pmm_transfer tf','t.transfer_id = tf.id','left')
			->where("(t.tanggal_transaksi between '$date1' and '$date2')")
			->where('t.akun',67)
			->order_by('t.tanggal_transaksi','asc')
			->order_by('t.id','asc')
			->group_by('t.id')
			->get()->result_array();

			$saldo_hutang_lain_lain = 0;
			$jumlah_no_transaksi_hutang_lain_lain = 0;
			$jumlah_deskripsi_hutang_lain_lain = 0;
			$jumlah_debit_hutang_lain_lain = 0;
			$jumlah_kredit_hutang_lain_lain = 0;

			$total_kredit_hutang_lain_lain_all = 0;
			$total_debit_hutang_lain_lain_all = 0;
			$saldo_akhir_hutang_lain_lain_all = 0;

			foreach ($hutang_lain_lain as $x){
				$total_debit_hutang_lain_lain_all += $x['debit'];
				$total_kredit_hutang_lain_lain_all += $x['kredit'];
			}

			$saldo_akhir_hutang_lain_lain_all = $total_debit_hutang_lain_lain_all - $total_kredit_hutang_lain_lain_all;
			//hutang_lain_lain

			//biaya_alat_truck_mixer
			$biaya_alat_truck_mixer = $this->db->select('t.id as transaction_id, t.akun, t.tanggal_transaksi, t.transaksi, b.nomor_transaksi as no_trx_1, j.nomor_transaksi as no_trx_2, tu.nomor_transaksi as no_trx_3, tf.nomor_transaksi as no_trx_4, pdb.deskripsi as dex_1, j.memo as dex_2, tu.memo as dex_3, tf.memo as dex_4, t.debit as debit, t.kredit as kredit')
			->from('transactions t ')
			->join('pmm_biaya b','t.biaya_id = b.id','left')
			->join('pmm_detail_biaya pdb','b.id = pdb.biaya_id','left')
			->join('pmm_jurnal_umum j','t.jurnal_id = j.id','left')
			->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
			->join('pmm_terima_uang tu','t.terima_id = tu.id','left')
			->join('pmm_transfer tf','t.transfer_id = tf.id','left')
			->where("(t.tanggal_transaksi between '$date1' and '$date2')")
			->where('t.akun',220)
			->order_by('t.tanggal_transaksi','asc')
			->order_by('t.id','asc')
			->group_by('t.id')
			->get()->result_array();

			$saldo_biaya_alat_truck_mixer = 0;
			$jumlah_no_transaksi_biaya_alat_truck_mixer = 0;
			$jumlah_deskripsi_biaya_alat_truck_mixer = 0;
			$jumlah_debit_biaya_alat_truck_mixer = 0;
			$jumlah_kredit_biaya_alat_truck_mixer = 0;

			$total_kredit_biaya_alat_truck_mixer_all = 0;
			$total_debit_biaya_alat_truck_mixer_all = 0;
			$saldo_akhir_biaya_alat_truck_mixer_all = 0;

			foreach ($biaya_alat_truck_mixer as $x){
				$total_debit_biaya_alat_truck_mixer_all += $x['debit'];
				$total_kredit_biaya_alat_truck_mixer_all += $x['kredit'];
			}

			$saldo_akhir_biaya_alat_truck_mixer_all = $total_debit_biaya_alat_truck_mixer_all - $total_kredit_biaya_alat_truck_mixer_all;
			//biaya_alat_truck_mixer

			//gaji
			$gaji = $this->db->select('t.id as transaction_id, t.akun, t.tanggal_transaksi, t.transaksi, b.nomor_transaksi as no_trx_1, j.nomor_transaksi as no_trx_2, tu.nomor_transaksi as no_trx_3, tf.nomor_transaksi as no_trx_4, pdb.deskripsi as dex_1, j.memo as dex_2, tu.memo as dex_3, tf.memo as dex_4, t.debit as debit, t.kredit as kredit')
			->from('transactions t ')
			->join('pmm_biaya b','t.biaya_id = b.id','left')
			->join('pmm_detail_biaya pdb','b.id = pdb.biaya_id','left')
			->join('pmm_jurnal_umum j','t.jurnal_id = j.id','left')
			->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
			->join('pmm_terima_uang tu','t.terima_id = tu.id','left')
			->join('pmm_transfer tf','t.transfer_id = tf.id','left')
			->where("(t.tanggal_transaksi between '$date1' and '$date2')")
			->where('t.akun',199)
			->order_by('t.tanggal_transaksi','asc')
			->order_by('t.id','asc')
			->group_by('t.id')
			->get()->result_array();

			$saldo_gaji = 0;
			$jumlah_no_transaksi_gaji = 0;
			$jumlah_deskripsi_gaji = 0;
			$jumlah_debit_gaji = 0;
			$jumlah_kredit_gaji = 0;

			$total_kredit_gaji_all = 0;
			$total_debit_gaji_all = 0;
			$saldo_akhir_gaji_all = 0;

			foreach ($gaji as $x){
				$total_debit_gaji_all += $x['debit'];
				$total_kredit_gaji_all += $x['kredit'];
			}

			$saldo_akhir_gaji_all = $total_debit_gaji_all - $total_kredit_gaji_all;
			//gaji

			//upah
			$upah = $this->db->select('t.id as transaction_id, t.akun, t.tanggal_transaksi, t.transaksi, b.nomor_transaksi as no_trx_1, j.nomor_transaksi as no_trx_2, tu.nomor_transaksi as no_trx_3, tf.nomor_transaksi as no_trx_4, pdb.deskripsi as dex_1, j.memo as dex_2, tu.memo as dex_3, tf.memo as dex_4, t.debit as debit, t.kredit as kredit')
			->from('transactions t ')
			->join('pmm_biaya b','t.biaya_id = b.id','left')
			->join('pmm_detail_biaya pdb','b.id = pdb.biaya_id','left')
			->join('pmm_jurnal_umum j','t.jurnal_id = j.id','left')
			->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
			->join('pmm_terima_uang tu','t.terima_id = tu.id','left')
			->join('pmm_transfer tf','t.transfer_id = tf.id','left')
			->where("(t.tanggal_transaksi between '$date1' and '$date2')")
			->where('t.akun',200)
			->order_by('t.tanggal_transaksi','asc')
			->order_by('t.id','asc')
			->group_by('t.id')
			->get()->result_array();

			$saldo_upah = 0;
			$jumlah_no_transaksi_upah = 0;
			$jumlah_deskripsi_upah = 0;
			$jumlah_debit_upah = 0;
			$jumlah_kredit_upah = 0;

			$total_kredit_upah_all = 0;
			$total_debit_upah_all = 0;
			$saldo_akhir_upah_all = 0;

			foreach ($upah as $x){
				$total_debit_upah_all += $x['debit'];
				$total_kredit_upah_all += $x['kredit'];
			}

			$saldo_akhir_upah_all = $total_debit_upah_all - $total_kredit_upah_all;
			//upah

			//konsumsi
			$konsumsi = $this->db->select('t.id as transaction_id, t.akun, t.tanggal_transaksi, t.transaksi, b.nomor_transaksi as no_trx_1, j.nomor_transaksi as no_trx_2, tu.nomor_transaksi as no_trx_3, tf.nomor_transaksi as no_trx_4, pdb.deskripsi as dex_1, j.memo as dex_2, tu.memo as dex_3, tf.memo as dex_4, t.debit as debit, t.kredit as kredit')
			->from('transactions t ')
			->join('pmm_biaya b','t.biaya_id = b.id','left')
			->join('pmm_detail_biaya pdb','b.id = pdb.biaya_id','left')
			->join('pmm_jurnal_umum j','t.jurnal_id = j.id','left')
			->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
			->join('pmm_terima_uang tu','t.terima_id = tu.id','left')
			->join('pmm_transfer tf','t.transfer_id = tf.id','left')
			->where("(t.tanggal_transaksi between '$date1' and '$date2')")
			->where('t.akun',201)
			->order_by('t.tanggal_transaksi','asc')
			->order_by('t.id','asc')
			->group_by('t.id')
			->get()->result_array();

			$saldo_konsumsi = 0;
			$jumlah_no_transaksi_konsumsi = 0;
			$jumlah_deskripsi_konsumsi = 0;
			$jumlah_debit_konsumsi = 0;
			$jumlah_kredit_konsumsi = 0;

			$total_kredit_konsumsi_all = 0;
			$total_debit_konsumsi_all = 0;
			$saldo_akhir_konsumsi_all = 0;

			foreach ($konsumsi as $x){
				$total_debit_konsumsi_all += $x['debit'];
				$total_kredit_konsumsi_all += $x['kredit'];
			}

			$saldo_akhir_konsumsi_all = $total_debit_konsumsi_all - $total_kredit_konsumsi_all;
			//konsumsi

			//pengobatan
			$pengobatan = $this->db->select('t.id as transaction_id, t.akun, t.tanggal_transaksi, t.transaksi, b.nomor_transaksi as no_trx_1, j.nomor_transaksi as no_trx_2, tu.nomor_transaksi as no_trx_3, tf.nomor_transaksi as no_trx_4, pdb.deskripsi as dex_1, j.memo as dex_2, tu.memo as dex_3, tf.memo as dex_4, t.debit as debit, t.kredit as kredit')
			->from('transactions t ')
			->join('pmm_biaya b','t.biaya_id = b.id','left')
			->join('pmm_detail_biaya pdb','b.id = pdb.biaya_id','left')
			->join('pmm_jurnal_umum j','t.jurnal_id = j.id','left')
			->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
			->join('pmm_terima_uang tu','t.terima_id = tu.id','left')
			->join('pmm_transfer tf','t.transfer_id = tf.id','left')
			->where("(t.tanggal_transaksi between '$date1' and '$date2')")
			->where('t.akun',121)
			->order_by('t.tanggal_transaksi','asc')
			->order_by('t.id','asc')
			->group_by('t.id')
			->get()->result_array();

			$saldo_pengobatan = 0;
			$jumlah_no_transaksi_pengobatan = 0;
			$jumlah_deskripsi_pengobatan = 0;
			$jumlah_debit_pengobatan = 0;
			$jumlah_kredit_pengobatan = 0;

			$total_kredit_pengobatan_all = 0;
			$total_debit_pengobatan_all = 0;
			$saldo_akhir_pengobatan_all = 0;

			foreach ($pengobatan as $x){
				$total_debit_pengobatan_all += $x['debit'];
				$total_kredit_pengobatan_all += $x['kredit'];
			}

			$saldo_akhir_pengobatan_all = $total_debit_pengobatan_all - $total_kredit_pengobatan_all;
			//pengobatan

			//thr_bonus
			$thr_bonus = $this->db->select('t.id as transaction_id, t.akun, t.tanggal_transaksi, t.transaksi, b.nomor_transaksi as no_trx_1, j.nomor_transaksi as no_trx_2, tu.nomor_transaksi as no_trx_3, tf.nomor_transaksi as no_trx_4, pdb.deskripsi as dex_1, j.memo as dex_2, tu.memo as dex_3, tf.memo as dex_4, t.debit as debit, t.kredit as kredit')
			->from('transactions t ')
			->join('pmm_biaya b','t.biaya_id = b.id','left')
			->join('pmm_detail_biaya pdb','b.id = pdb.biaya_id','left')
			->join('pmm_jurnal_umum j','t.jurnal_id = j.id','left')
			->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
			->join('pmm_terima_uang tu','t.terima_id = tu.id','left')
			->join('pmm_transfer tf','t.transfer_id = tf.id','left')
			->where("(t.tanggal_transaksi between '$date1' and '$date2')")
			->where('t.akun',202)
			->order_by('t.tanggal_transaksi','asc')
			->order_by('t.id','asc')
			->group_by('t.id')
			->get()->result_array();

			$saldo_thr_bonus = 0;
			$jumlah_no_transaksi_thr_bonus = 0;
			$jumlah_deskripsi_thr_bonus = 0;
			$jumlah_debit_thr_bonus = 0;
			$jumlah_kredit_thr_bonus = 0;

			$total_kredit_thr_bonus_all = 0;
			$total_debit_thr_bonus_all = 0;
			$saldo_akhir_thr_bonus_all = 0;

			foreach ($thr_bonus as $x){
				$total_debit_thr_bonus_all += $x['debit'];
				$total_kredit_thr_bonus_all += $x['kredit'];
			}

			$saldo_akhir_thr_bonus_all = $total_debit_thr_bonus_all - $total_kredit_thr_bonus_all;
			//thr_bonus

			//bensin_tol_parkir
			$bensin_tol_parkir = $this->db->select('t.id as transaction_id, t.akun, t.tanggal_transaksi, t.transaksi, b.nomor_transaksi as no_trx_1, j.nomor_transaksi as no_trx_2, tu.nomor_transaksi as no_trx_3, tf.nomor_transaksi as no_trx_4, pdb.deskripsi as dex_1, j.memo as dex_2, tu.memo as dex_3, tf.memo as dex_4, t.debit as debit, t.kredit as kredit')
			->from('transactions t ')
			->join('pmm_biaya b','t.biaya_id = b.id','left')
			->join('pmm_detail_biaya pdb','b.id = pdb.biaya_id','left')
			->join('pmm_jurnal_umum j','t.jurnal_id = j.id','left')
			->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
			->join('pmm_terima_uang tu','t.terima_id = tu.id','left')
			->join('pmm_transfer tf','t.transfer_id = tf.id','left')
			->where("(t.tanggal_transaksi between '$date1' and '$date2')")
			->where('t.akun',129)
			->order_by('t.tanggal_transaksi','asc')
			->order_by('t.id','asc')
			->group_by('t.id')
			->get()->result_array();

			$saldo_bensin_tol_parkir = 0;
			$jumlah_no_transaksi_bensin_tol_parkir = 0;
			$jumlah_deskripsi_bensin_tol_parkir = 0;
			$jumlah_debit_bensin_tol_parkir = 0;
			$jumlah_kredit_bensin_tol_parkir = 0;

			$total_kredit_bensin_tol_parkir_all = 0;
			$total_debit_bensin_tol_parkir_all = 0;
			$saldo_akhir_bensin_tol_parkir_all = 0;

			foreach ($bensin_tol_parkir as $x){
				$total_debit_bensin_tol_parkir_all += $x['debit'];
				$total_kredit_bensin_tol_parkir_all += $x['kredit'];
			}

			$saldo_akhir_bensin_tol_parkir_all = $total_debit_bensin_tol_parkir_all - $total_kredit_bensin_tol_parkir_all;
			//bensin_tol_parkir

			//perjalanan_dinas_penjualan
			$perjalanan_dinas_penjualan = $this->db->select('t.id as transaction_id, t.akun, t.tanggal_transaksi, t.transaksi, b.nomor_transaksi as no_trx_1, j.nomor_transaksi as no_trx_2, tu.nomor_transaksi as no_trx_3, tf.nomor_transaksi as no_trx_4, pdb.deskripsi as dex_1, j.memo as dex_2, tu.memo as dex_3, tf.memo as dex_4, t.debit as debit, t.kredit as kredit')
			->from('transactions t ')
			->join('pmm_biaya b','t.biaya_id = b.id','left')
			->join('pmm_detail_biaya pdb','b.id = pdb.biaya_id','left')
			->join('pmm_jurnal_umum j','t.jurnal_id = j.id','left')
			->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
			->join('pmm_terima_uang tu','t.terima_id = tu.id','left')
			->join('pmm_transfer tf','t.transfer_id = tf.id','left')
			->where("(t.tanggal_transaksi between '$date1' and '$date2')")
			->where('t.akun',113)
			->order_by('t.tanggal_transaksi','asc')
			->order_by('t.id','asc')
			->group_by('t.id')
			->get()->result_array();

			$saldo_perjalanan_dinas_penjualan = 0;
			$jumlah_no_transaksi_perjalanan_dinas_penjualan = 0;
			$jumlah_deskripsi_perjalanan_dinas_penjualan = 0;
			$jumlah_debit_perjalanan_dinas_penjualan = 0;
			$jumlah_kredit_perjalanan_dinas_penjualan = 0;

			$total_kredit_perjalanan_dinas_penjualan_all = 0;
			$total_debit_perjalanan_dinas_penjualan_all = 0;
			$saldo_akhir_perjalanan_dinas_penjualan_all = 0;

			foreach ($perjalanan_dinas_penjualan as $x){
				$total_debit_perjalanan_dinas_penjualan_all += $x['debit'];
				$total_kredit_perjalanan_dinas_penjualan_all += $x['kredit'];
			}

			$saldo_akhir_perjalanan_dinas_penjualan_all = $total_debit_perjalanan_dinas_penjualan_all - $total_kredit_perjalanan_dinas_penjualan_all;
			//perjalanan_dinas_penjualan

			//pakaian_dinas
			$pakaian_dinas = $this->db->select('t.id as transaction_id, t.akun, t.tanggal_transaksi, t.transaksi, b.nomor_transaksi as no_trx_1, j.nomor_transaksi as no_trx_2, tu.nomor_transaksi as no_trx_3, tf.nomor_transaksi as no_trx_4, pdb.deskripsi as dex_1, j.memo as dex_2, tu.memo as dex_3, tf.memo as dex_4, t.debit as debit, t.kredit as kredit')
			->from('transactions t ')
			->join('pmm_biaya b','t.biaya_id = b.id','left')
			->join('pmm_detail_biaya pdb','b.id = pdb.biaya_id','left')
			->join('pmm_jurnal_umum j','t.jurnal_id = j.id','left')
			->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
			->join('pmm_terima_uang tu','t.terima_id = tu.id','left')
			->join('pmm_transfer tf','t.transfer_id = tf.id','left')
			->where("(t.tanggal_transaksi between '$date1' and '$date2')")
			->where('t.akun',138)
			->order_by('t.tanggal_transaksi','asc')
			->order_by('t.id','asc')
			->group_by('t.id')
			->get()->result_array();

			$saldo_pakaian_dinas = 0;
			$jumlah_no_transaksi_pakaian_dinas = 0;
			$jumlah_deskripsi_pakaian_dinas = 0;
			$jumlah_debit_pakaian_dinas = 0;
			$jumlah_kredit_pakaian_dinas = 0;

			$total_kredit_pakaian_dinas_all = 0;
			$total_debit_pakaian_dinas_all = 0;
			$saldo_akhir_pakaian_dinas_all = 0;

			foreach ($pakaian_dinas as $x){
				$total_debit_pakaian_dinas_all += $x['debit'];
				$total_kredit_pakaian_dinas_all += $x['kredit'];
			}

			$saldo_akhir_pakaian_dinas_all = $total_debit_pakaian_dinas_all - $total_kredit_pakaian_dinas_all;
			//pakaian_dinas

			//beban_kirim
			$beban_kirim = $this->db->select('t.id as transaction_id, t.akun, t.tanggal_transaksi, t.transaksi, b.nomor_transaksi as no_trx_1, j.nomor_transaksi as no_trx_2, tu.nomor_transaksi as no_trx_3, tf.nomor_transaksi as no_trx_4, pdb.deskripsi as dex_1, j.memo as dex_2, tu.memo as dex_3, tf.memo as dex_4, t.debit as debit, t.kredit as kredit')
			->from('transactions t ')
			->join('pmm_biaya b','t.biaya_id = b.id','left')
			->join('pmm_detail_biaya pdb','b.id = pdb.biaya_id','left')
			->join('pmm_jurnal_umum j','t.jurnal_id = j.id','left')
			->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
			->join('pmm_terima_uang tu','t.terima_id = tu.id','left')
			->join('pmm_transfer tf','t.transfer_id = tf.id','left')
			->where("(t.tanggal_transaksi between '$date1' and '$date2')")
			->where('t.akun',145)
			->order_by('t.tanggal_transaksi','asc')
			->order_by('t.id','asc')
			->group_by('t.id')
			->get()->result_array();

			$saldo_beban_kirim = 0;
			$jumlah_no_transaksi_beban_kirim = 0;
			$jumlah_deskripsi_beban_kirim = 0;
			$jumlah_debit_beban_kirim = 0;
			$jumlah_kredit_beban_kirim = 0;

			$total_kredit_beban_kirim_all = 0;
			$total_debit_beban_kirim_all = 0;
			$saldo_akhir_beban_kirim_all = 0;

			foreach ($beban_kirim as $x){
				$total_debit_beban_kirim_all += $x['debit'];
				$total_kredit_beban_kirim_all += $x['kredit'];
			}

			$saldo_akhir_beban_kirim_all = $total_debit_beban_kirim_all - $total_kredit_beban_kirim_all;
			//beban_kirim

			//pengujian_material_laboratorium
			$pengujian_material_laboratorium = $this->db->select('t.id as transaction_id, t.akun, t.tanggal_transaksi, t.transaksi, b.nomor_transaksi as no_trx_1, j.nomor_transaksi as no_trx_2, tu.nomor_transaksi as no_trx_3, tf.nomor_transaksi as no_trx_4, pdb.deskripsi as dex_1, j.memo as dex_2, tu.memo as dex_3, tf.memo as dex_4, t.debit as debit, t.kredit as kredit')
			->from('transactions t ')
			->join('pmm_biaya b','t.biaya_id = b.id','left')
			->join('pmm_detail_biaya pdb','b.id = pdb.biaya_id','left')
			->join('pmm_jurnal_umum j','t.jurnal_id = j.id','left')
			->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
			->join('pmm_terima_uang tu','t.terima_id = tu.id','left')
			->join('pmm_transfer tf','t.transfer_id = tf.id','left')
			->where("(t.tanggal_transaksi between '$date1' and '$date2')")
			->where('t.akun',216)
			->order_by('t.tanggal_transaksi','asc')
			->order_by('t.id','asc')
			->group_by('t.id')
			->get()->result_array();

			$saldo_pengujian_material_laboratorium = 0;
			$jumlah_no_transaksi_pengujian_material_laboratorium = 0;
			$jumlah_deskripsi_pengujian_material_laboratorium = 0;
			$jumlah_debit_pengujian_material_laboratorium = 0;
			$jumlah_kredit_pengujian_material_laboratorium = 0;

			$total_kredit_pengujian_material_laboratorium_all = 0;
			$total_debit_pengujian_material_laboratorium_all = 0;
			$saldo_akhir_pengujian_material_laboratorium_all = 0;

			foreach ($pengujian_material_laboratorium as $x){
				$total_debit_pengujian_material_laboratorium_all += $x['debit'];
				$total_kredit_pengujian_material_laboratorium_all += $x['kredit'];
			}

			$saldo_akhir_pengujian_material_laboratorium_all = $total_debit_pengujian_material_laboratorium_all - $total_kredit_pengujian_material_laboratorium_all;
			//pengujian_material_laboratorium

			//listrik_internet
			$listrik_internet = $this->db->select('t.id as transaction_id, t.akun, t.tanggal_transaksi, t.transaksi, b.nomor_transaksi as no_trx_1, j.nomor_transaksi as no_trx_2, tu.nomor_transaksi as no_trx_3, tf.nomor_transaksi as no_trx_4, pdb.deskripsi as dex_1, j.memo as dex_2, tu.memo as dex_3, tf.memo as dex_4, t.debit as debit, t.kredit as kredit')
			->from('transactions t ')
			->join('pmm_biaya b','t.biaya_id = b.id','left')
			->join('pmm_detail_biaya pdb','b.id = pdb.biaya_id','left')
			->join('pmm_jurnal_umum j','t.jurnal_id = j.id','left')
			->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
			->join('pmm_terima_uang tu','t.terima_id = tu.id','left')
			->join('pmm_transfer tf','t.transfer_id = tf.id','left')
			->where("(t.tanggal_transaksi between '$date1' and '$date2')")
			->where('t.akun',206)
			->order_by('t.tanggal_transaksi','asc')
			->order_by('t.id','asc')
			->group_by('t.id')
			->get()->result_array();

			$saldo_listrik_internet = 0;
			$jumlah_no_transaksi_listrik_internet = 0;
			$jumlah_deskripsi_listrik_internet = 0;
			$jumlah_debit_listrik_internet = 0;
			$jumlah_kredit_listrik_internet = 0;

			$total_kredit_listrik_internet_all = 0;
			$total_debit_listrik_internet_all = 0;
			$saldo_akhir_listrik_internet_all = 0;

			foreach ($listrik_internet as $x){
				$total_debit_listrik_internet_all += $x['debit'];
				$total_kredit_listrik_internet_all += $x['kredit'];
			}

			$saldo_akhir_listrik_internet_all = $total_debit_listrik_internet_all - $total_kredit_listrik_internet_all;
			//listrik_internet

			//keamanan_kebersihan
			$keamanan_kebersihan = $this->db->select('t.id as transaction_id, t.akun, t.tanggal_transaksi, t.transaksi, b.nomor_transaksi as no_trx_1, j.nomor_transaksi as no_trx_2, tu.nomor_transaksi as no_trx_3, tf.nomor_transaksi as no_trx_4, pdb.deskripsi as dex_1, j.memo as dex_2, tu.memo as dex_3, tf.memo as dex_4, t.debit as debit, t.kredit as kredit')
			->from('transactions t ')
			->join('pmm_biaya b','t.biaya_id = b.id','left')
			->join('pmm_detail_biaya pdb','b.id = pdb.biaya_id','left')
			->join('pmm_jurnal_umum j','t.jurnal_id = j.id','left')
			->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
			->join('pmm_terima_uang tu','t.terima_id = tu.id','left')
			->join('pmm_transfer tf','t.transfer_id = tf.id','left')
			->where("(t.tanggal_transaksi between '$date1' and '$date2')")
			->where('t.akun',151)
			->order_by('t.tanggal_transaksi','asc')
			->order_by('t.id','asc')
			->group_by('t.id')
			->get()->result_array();

			$saldo_keamanan_kebersihan = 0;
			$jumlah_no_transaksi_keamanan_kebersihan = 0;
			$jumlah_deskripsi_keamanan_kebersihan = 0;
			$jumlah_debit_keamanan_kebersihan = 0;
			$jumlah_kredit_keamanan_kebersihan = 0;

			$total_kredit_keamanan_kebersihan_all = 0;
			$total_debit_keamanan_kebersihan_all = 0;
			$saldo_akhir_keamanan_kebersihan_all = 0;

			foreach ($keamanan_kebersihan as $x){
				$total_debit_keamanan_kebersihan_all += $x['debit'];
				$total_kredit_keamanan_kebersihan_all += $x['kredit'];
			}

			$saldo_akhir_keamanan_kebersihan_all = $total_debit_keamanan_kebersihan_all - $total_kredit_keamanan_kebersihan_all;
			//keamanan_kebersihan

			//perlengkapan_kantor
			$perlengkapan_kantor = $this->db->select('t.id as transaction_id, t.akun, t.tanggal_transaksi, t.transaksi, b.nomor_transaksi as no_trx_1, j.nomor_transaksi as no_trx_2, tu.nomor_transaksi as no_trx_3, tf.nomor_transaksi as no_trx_4, pdb.deskripsi as dex_1, j.memo as dex_2, tu.memo as dex_3, tf.memo as dex_4, t.debit as debit, t.kredit as kredit')
			->from('transactions t ')
			->join('pmm_biaya b','t.biaya_id = b.id','left')
			->join('pmm_detail_biaya pdb','b.id = pdb.biaya_id','left')
			->join('pmm_jurnal_umum j','t.jurnal_id = j.id','left')
			->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
			->join('pmm_terima_uang tu','t.terima_id = tu.id','left')
			->join('pmm_transfer tf','t.transfer_id = tf.id','left')
			->where("(t.tanggal_transaksi between '$date1' and '$date2')")
			->where('t.akun',153)
			->order_by('t.tanggal_transaksi','asc')
			->order_by('t.id','asc')
			->group_by('t.id')
			->get()->result_array();

			$saldo_perlengkapan_kantor = 0;
			$jumlah_no_transaksi_perlengkapan_kantor = 0;
			$jumlah_deskripsi_perlengkapan_kantor = 0;
			$jumlah_debit_perlengkapan_kantor = 0;
			$jumlah_kredit_perlengkapan_kantor = 0;

			$total_kredit_perlengkapan_kantor_all = 0;
			$total_debit_perlengkapan_kantor_all = 0;
			$saldo_akhir_perlengkapan_kantor_all = 0;

			foreach ($perlengkapan_kantor as $x){
				$total_debit_perlengkapan_kantor_all += $x['debit'];
				$total_kredit_perlengkapan_kantor_all += $x['kredit'];
			}

			$saldo_akhir_perlengkapan_kantor_all = $total_debit_perlengkapan_kantor_all - $total_kredit_perlengkapan_kantor_all;
			//perlengkapan_kantor

			//beban_lain_lain
			$beban_lain_lain = $this->db->select('t.id as transaction_id, t.akun, t.tanggal_transaksi, t.transaksi, b.nomor_transaksi as no_trx_1, j.nomor_transaksi as no_trx_2, tu.nomor_transaksi as no_trx_3, tf.nomor_transaksi as no_trx_4, pdb.deskripsi as dex_1, j.memo as dex_2, tu.memo as dex_3, tf.memo as dex_4, t.debit as debit, t.kredit as kredit')
			->from('transactions t ')
			->join('pmm_biaya b','t.biaya_id = b.id','left')
			->join('pmm_detail_biaya pdb','b.id = pdb.biaya_id','left')
			->join('pmm_jurnal_umum j','t.jurnal_id = j.id','left')
			->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
			->join('pmm_terima_uang tu','t.terima_id = tu.id','left')
			->join('pmm_transfer tf','t.transfer_id = tf.id','left')
			->where("(t.tanggal_transaksi between '$date1' and '$date2')")
			->where('t.akun',146)
			->order_by('t.tanggal_transaksi','asc')
			->order_by('t.id','asc')
			->group_by('t.id')
			->get()->result_array();

			$saldo_beban_lain_lain = 0;
			$jumlah_no_transaksi_beban_lain_lain = 0;
			$jumlah_deskripsi_beban_lain_lain = 0;
			$jumlah_debit_beban_lain_lain = 0;
			$jumlah_kredit_beban_lain_lain = 0;

			$total_kredit_beban_lain_lain_all = 0;
			$total_debit_beban_lain_lain_all = 0;
			$saldo_akhir_beban_lain_lain_all = 0;

			foreach ($beban_lain_lain as $x){
				$total_debit_beban_lain_lain_all += $x['debit'];
				$total_kredit_beban_lain_lain_all += $x['kredit'];
			}

			$saldo_akhir_beban_lain_lain_all = $total_debit_beban_lain_lain_all - $total_kredit_beban_lain_lain_all;
			//beban_lain_lain

			//biaya_adm_bank
			$biaya_adm_bank = $this->db->select('t.id as transaction_id, t.akun, t.tanggal_transaksi, t.transaksi, b.nomor_transaksi as no_trx_1, j.nomor_transaksi as no_trx_2, tu.nomor_transaksi as no_trx_3, tf.nomor_transaksi as no_trx_4, pdb.deskripsi as dex_1, j.memo as dex_2, tu.memo as dex_3, tf.memo as dex_4, t.debit as debit, t.kredit as kredit')
			->from('transactions t ')
			->join('pmm_biaya b','t.biaya_id = b.id','left')
			->join('pmm_detail_biaya pdb','b.id = pdb.biaya_id','left')
			->join('pmm_jurnal_umum j','t.jurnal_id = j.id','left')
			->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
			->join('pmm_terima_uang tu','t.terima_id = tu.id','left')
			->join('pmm_transfer tf','t.transfer_id = tf.id','left')
			->where("(t.tanggal_transaksi between '$date1' and '$date2')")
			->where('t.akun',143)
			->order_by('t.tanggal_transaksi','asc')
			->order_by('t.id','asc')
			->group_by('t.id')
			->get()->result_array();

			$saldo_biaya_adm_bank = 0;
			$jumlah_no_transaksi_biaya_adm_bank = 0;
			$jumlah_deskripsi_biaya_adm_bank = 0;
			$jumlah_debit_biaya_adm_bank = 0;
			$jumlah_kredit_biaya_adm_bank = 0;

			$total_kredit_biaya_adm_bank_all = 0;
			$total_debit_biaya_adm_bank_all = 0;
			$saldo_akhir_biaya_adm_bank_all = 0;

			foreach ($biaya_adm_bank as $x){
				$total_debit_biaya_adm_bank_all += $x['debit'];
				$total_kredit_biaya_adm_bank_all += $x['kredit'];
			}

			$saldo_akhir_biaya_adm_bank_all = $total_debit_biaya_adm_bank_all - $total_kredit_biaya_adm_bank_all;
			//biaya_adm_bank

			//biaya_sewa_kendaraan
			$biaya_sewa_kendaraan = $this->db->select('t.id as transaction_id, t.akun, t.tanggal_transaksi, t.transaksi, b.nomor_transaksi as no_trx_1, j.nomor_transaksi as no_trx_2, tu.nomor_transaksi as no_trx_3, tf.nomor_transaksi as no_trx_4, pdb.deskripsi as dex_1, j.memo as dex_2, tu.memo as dex_3, tf.memo as dex_4, t.debit as debit, t.kredit as kredit')
			->from('transactions t ')
			->join('pmm_biaya b','t.biaya_id = b.id','left')
			->join('pmm_detail_biaya pdb','b.id = pdb.biaya_id','left')
			->join('pmm_jurnal_umum j','t.jurnal_id = j.id','left')
			->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
			->join('pmm_terima_uang tu','t.terima_id = tu.id','left')
			->join('pmm_transfer tf','t.transfer_id = tf.id','left')
			->where("(t.tanggal_transaksi between '$date1' and '$date2')")
			->where('t.akun',157)
			->order_by('t.tanggal_transaksi','asc')
			->order_by('t.id','asc')
			->group_by('t.id')
			->get()->result_array();

			$saldo_biaya_sewa_kendaraan = 0;
			$jumlah_no_transaksi_biaya_sewa_kendaraan = 0;
			$jumlah_deskripsi_biaya_sewa_kendaraan = 0;
			$jumlah_debit_biaya_sewa_kendaraan = 0;
			$jumlah_kredit_biaya_sewa_kendaraan = 0;

			$total_kredit_biaya_sewa_kendaraan_all = 0;
			$total_debit_biaya_sewa_kendaraan_all = 0;
			$saldo_akhir_biaya_sewa_kendaraan_all = 0;

			foreach ($biaya_sewa_kendaraan as $x){
				$total_debit_biaya_sewa_kendaraan_all += $x['debit'];
				$total_kredit_biaya_sewa_kendaraan_all += $x['kredit'];
			}

			$saldo_akhir_biaya_sewa_kendaraan_all = $total_debit_biaya_sewa_kendaraan_all - $total_kredit_biaya_sewa_kendaraan_all;
			//biaya_sewa_kendaraan

			//beban_bunga
			$beban_bunga = $this->db->select('t.id as transaction_id, t.akun, t.tanggal_transaksi, t.transaksi, b.nomor_transaksi as no_trx_1, j.nomor_transaksi as no_trx_2, tu.nomor_transaksi as no_trx_3, tf.nomor_transaksi as no_trx_4, pdb.deskripsi as dex_1, j.memo as dex_2, tu.memo as dex_3, tf.memo as dex_4, t.debit as debit, t.kredit as kredit')
			->from('transactions t ')
			->join('pmm_biaya b','t.biaya_id = b.id','left')
			->join('pmm_detail_biaya pdb','b.id = pdb.biaya_id','left')
			->join('pmm_jurnal_umum j','t.jurnal_id = j.id','left')
			->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
			->join('pmm_terima_uang tu','t.terima_id = tu.id','left')
			->join('pmm_transfer tf','t.transfer_id = tf.id','left')
			->where("(t.tanggal_transaksi between '$date1' and '$date2')")
			->where('t.akun',168)
			->order_by('t.tanggal_transaksi','asc')
			->order_by('t.id','asc')
			->group_by('t.id')
			->get()->result_array();

			$saldo_beban_bunga = 0;
			$jumlah_no_transaksi_beban_bunga = 0;
			$jumlah_deskripsi_beban_bunga = 0;
			$jumlah_debit_beban_bunga = 0;
			$jumlah_kredit_beban_bunga = 0;

			$total_kredit_beban_bunga_all = 0;
			$total_debit_beban_bunga_all = 0;
			$saldo_akhir_beban_bunga_all = 0;

			foreach ($beban_bunga as $x){
				$total_debit_beban_bunga_all += $x['debit'];
				$total_kredit_beban_bunga_all += $x['kredit'];
			}

			$saldo_akhir_beban_bunga_all = $total_debit_beban_bunga_all - $total_kredit_beban_bunga_all;
			//beban_bunga

			//bank_kantor_pusat
			$bank_kantor_pusat = $this->db->select('t.id as transaction_id, t.akun, t.tanggal_transaksi, t.transaksi, b.nomor_transaksi as no_trx_1, j.nomor_transaksi as no_trx_2, tu.nomor_transaksi as no_trx_3, tf.nomor_transaksi as no_trx_4, pdb.deskripsi as dex_1, j.memo as dex_2, tu.memo as dex_3, tf.memo as dex_4, t.debit as debit, t.kredit as kredit')
			->from('transactions t ')
			->join('pmm_biaya b','t.biaya_id = b.id','left')
			->join('pmm_detail_biaya pdb','b.id = pdb.biaya_id','left')
			->join('pmm_jurnal_umum j','t.jurnal_id = j.id','left')
			->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
			->join('pmm_terima_uang tu','t.terima_id = tu.id','left')
			->join('pmm_transfer tf','t.transfer_id = tf.id','left')
			->where("(t.tanggal_transaksi between '$date1' and '$date2')")
			->where('t.akun',217)
			->order_by('t.tanggal_transaksi','asc')
			->order_by('t.id','asc')
			->group_by('t.id')
			->get()->result_array();

			$saldo_bank_kantor_pusat = 0;
			$jumlah_no_transaksi_bank_kantor_pusat = 0;
			$jumlah_deskripsi_bank_kantor_pusat = 0;
			$jumlah_debit_bank_kantor_pusat = 0;
			$jumlah_kredit_bank_kantor_pusat = 0;

			$total_kredit_bank_kantor_pusat_all = 0;
			$total_debit_bank_kantor_pusat_all = 0;
			$saldo_akhir_bank_kantor_pusat_all = 0;

			foreach ($bank_kantor_pusat as $x){
				$total_debit_bank_kantor_pusat_all += $x['debit'];
				$total_kredit_bank_kantor_pusat_all += $x['kredit'];
			}

			$saldo_akhir_bank_kantor_pusat_all = $total_debit_bank_kantor_pusat_all - $total_kredit_bank_kantor_pusat_all;
			//bank_kantor_pusat
			
	        ?>

			<tr class="table-active">
	            <th width="55%" class="text-center">Nama Akun</th>
				<th width="15%" class="text-center">Debit</th>
				<th width="15%" class="text-center">Kredit</th>
				<th width="15%" class="text-center">Saldo</th>
	        </tr>
		</table>

		<!-- kas -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="55%" class="text-left"><button class="btn btn-info" type="button" data-toggle="collapse" data-target="#kas" aria-expanded="false" aria-controls="kas">(1-10001) Kas</button></th>
				<th width="15%" class="text-right"><?php echo number_format($total_debit_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($total_kredit_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($saldo_akhir_all,0,',','.');?></th>
	        </tr>
		</table>
		<table class="collapse table table-bordered" id="kas" width="100%">
			<tr class="table-active" width="100%">
				<th width="10%" class="text-center">Tanggal</th>
				<th width="10%" class="text-center">Transaksi</th>
				<th width="20%" class="text-center">Nomor</th>
				<th width="15%" class="text-center">Keterangan</th>
				<th width="15%" class="text-center">Debit</th>
				<th width="15%" class="text-center">Kredit</th>
				<th width="15%" class="text-center">Saldo</th>
	        </tr>
			<?php foreach ($transactions as $key => $x) {
				
				if ($x['no_trx_1']==0) { $jumlah_no_transaksi = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];} else
				{$jumlah_no_transaksi = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];}

				if ($x['dex_1']==0) { $jumlah_deskripsi = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];} else
				{$jumlah_deskripsi = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];}

				if ($x['debit']==0) { $jumlah_debit = $x['debit'];} else
				{$jumlah_debit = $x['debit'];}

				if ($x['kredit']==0) { $jumlah_kredit = $x['kredit'];} else
				{$jumlah_kredit = $x['kredit'];}
				
				if ($jumlah_debit==0) { $saldo = $saldo + $jumlah_debit - $jumlah_kredit;} else
				{$saldo = $saldo + $jumlah_debit;}
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center"><?= $x['transaksi'] ?></th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_transaction/".$x['transaction_id']) ?>"><?= $jumlah_no_transaksi ?></th>
				<th class="text-left"><?= $jumlah_deskripsi ?></th>
				<th class="text-right"><?php echo number_format($jumlah_debit,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_kredit,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($saldo,0,',','.');?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- kas -->

		<!-- bank_kantor_pusat -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="55%" class="text-left"><button class="btn btn-info" type="button" data-toggle="collapse" data-target="#bank_kantor_pusat" aria-expanded="false" aria-controls="bank_kantor_pusat">(1-10002) Bank Kantor Pusat</button></th>
				<th width="15%" class="text-right"><?php echo number_format($total_debit_bank_kantor_pusat_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($total_kredit_bank_kantor_pusat_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($saldo_akhir_bank_kantor_pusat_all,0,',','.');?></th>
	        </tr>
		</table>
		<table class="collapse table table-bordered" id="bank_kantor_pusat" width="100%">
			<tr class="table-active" width="100%">
				<th width="10%" class="text-center">Tanggal</th>
				<th width="10%" class="text-center">Transaksi</th>
				<th width="20%" class="text-center">Nomor</th>
				<th width="15%" class="text-center">Keterangan</th>
				<th width="15%" class="text-center">Debit</th>
				<th width="15%" class="text-center">Kredit</th>
				<th width="15%" class="text-center">Saldo</th>
	        </tr>
			<?php foreach ($bank_kantor_pusat as $key => $x) {
				
				if ($x['no_trx_1']==0) { $jumlah_no_transaksi_bank_kantor_pusat = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];} else
				{$jumlah_no_transaksi_bank_kantor_pusat = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];}

				if ($x['dex_1']==0) { $jumlah_deskripsi_bank_kantor_pusat = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];} else
				{$jumlah_deskripsi_bank_kantor_pusat = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];}

				if ($x['debit']==0) { $jumlah_debit_bank_kantor_pusat = $x['debit'];} else
				{$jumlah_debit_bank_kantor_pusat = $x['debit'];}

				if ($x['kredit']==0) { $jumlah_kredit_bank_kantor_pusat = $x['kredit'];} else
				{$jumlah_kredit_bank_kantor_pusat = $x['kredit'];}
				
				if ($jumlah_debit_bank_kantor_pusat==0) { $saldo_bank_kantor_pusat = $saldo_bank_kantor_pusat + $jumlah_debit_bank_kantor_pusat - $jumlah_kredit_bank_kantor_pusat;} else
				{$saldo_bank_kantor_pusat = $saldo_bank_kantor_pusat + $jumlah_debit_bank_kantor_pusat;}
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center"><?= $x['transaksi'] ?></th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_transaction/".$x['transaction_id']) ?>"><?= $jumlah_no_transaksi_bank_kantor_pusat ?></th>
				<th class="text-left"><?= $jumlah_deskripsi_bank_kantor_pusat ?></th>
				<th class="text-right"><?php echo number_format($jumlah_debit_bank_kantor_pusat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_kredit_bank_kantor_pusat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($saldo_bank_kantor_pusat,0,',','.');?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- bank_kantor_pusat -->

		<!-- hutang_nindya -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="55%" class="text-left"><button class="btn btn-info" type="button" data-toggle="collapse" data-target="#hutang_nindya" aria-expanded="false" aria-controls="hutang_nindya">(2-20114) Hutang PT. Nindya Karya (Persero) Div. Peralatan</button></th>
				<th width="15%" class="text-right"><?php echo number_format($total_debit_hutang_nindya_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($total_kredit_hutang_nindya_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($saldo_akhir_hutang_nindya_all,0,',','.');?></th>
	        </tr>
		</table>
		<table class="collapse table table-bordered" id="hutang_nindya" width="100%">
			<tr class="table-active" width="100%">
				<th width="10%" class="text-center">Tanggal</th>
				<th width="10%" class="text-center">Transaksi</th>
				<th width="20%" class="text-center">Nomor</th>
				<th width="15%" class="text-center">Keterangan</th>
				<th width="15%" class="text-center">Debit</th>
				<th width="15%" class="text-center">Kredit</th>
				<th width="15%" class="text-center">Saldo</th>
	        </tr>
			<?php foreach ($hutang_nindya as $key => $x) {
				
				if ($x['no_trx_1']==0) { $jumlah_no_transaksi_hutang_nindya = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];} else
				{$jumlah_no_transaksi_hutang_nindya = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];}

				if ($x['dex_1']==0) { $jumlah_deskripsi_hutang_nindya = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];} else
				{$jumlah_deskripsi_hutang_nindya = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];}

				if ($x['debit']==0) { $jumlah_debit_hutang_nindya = $x['debit'];} else
				{$jumlah_debit_hutang_nindya = $x['debit'];}

				if ($x['kredit']==0) { $jumlah_kredit_hutang_nindya = $x['kredit'];} else
				{$jumlah_kredit_hutang_nindya = $x['kredit'];}
				
				if ($jumlah_debit_hutang_nindya==0) { $saldo_hutang_nindya = $saldo_hutang_nindya + $jumlah_debit_hutang_nindya - $jumlah_kredit_hutang_nindya;} else
				{$saldo_hutang_nindya = $saldo_hutang_nindya + $jumlah_debit_hutang_nindya;}
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center"><?= $x['transaksi'] ?></th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_transaction/".$x['transaction_id']) ?>"><?= $jumlah_no_transaksi_hutang_nindya ?></th>
				<th class="text-left"><?= $jumlah_deskripsi_hutang_nindya ?></th>
				<th class="text-right"><?php echo number_format($jumlah_debit_hutang_nindya,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_kredit_hutang_nindya,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($saldo_hutang_nindya,0,',','.');?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- hutang_nindya -->

		<!-- hutang_sinar -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="55%" class="text-left"><button class="btn btn-info" type="button" data-toggle="collapse" data-target="#hutang_sinar" aria-expanded="false" aria-controls="hutang_sinar">(2-20116) Hutang PT. Sinar Bangun Mandiri</button></th>
				<th width="15%" class="text-right"><?php echo number_format($total_debit_hutang_sinar_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($total_kredit_hutang_sinar_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($saldo_akhir_hutang_sinar_all,0,',','.');?></th>
	        </tr>
		</table>
		<table class="collapse table table-bordered" id="hutang_sinar" width="100%">
			<tr class="table-active" width="100%">
				<th width="10%" class="text-center">Tanggal</th>
				<th width="10%" class="text-center">Transaksi</th>
				<th width="20%" class="text-center">Nomor</th>
				<th width="15%" class="text-center">Keterangan</th>
				<th width="15%" class="text-center">Debit</th>
				<th width="15%" class="text-center">Kredit</th>
				<th width="15%" class="text-center">Saldo</th>
	        </tr>
			<?php foreach ($hutang_sinar as $key => $x) {
				
				if ($x['no_trx_1']==0) { $jumlah_no_transaksi_hutang_sinar = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];} else
				{$jumlah_no_transaksi_hutang_sinar = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];}

				if ($x['dex_1']==0) { $jumlah_deskripsi_hutang_sinar = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];} else
				{$jumlah_deskripsi_hutang_sinar = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];}

				if ($x['debit']==0) { $jumlah_debit_hutang_sinar = $x['debit'];} else
				{$jumlah_debit_hutang_sinar = $x['debit'];}

				if ($x['kredit']==0) { $jumlah_kredit_hutang_sinar = $x['kredit'];} else
				{$jumlah_kredit_hutang_sinar = $x['kredit'];}
				
				if ($jumlah_debit_hutang_sinar==0) { $saldo_hutang_sinar = $saldo_hutang_sinar + $jumlah_debit_hutang_sinar - $jumlah_kredit_hutang_sinar;} else
				{$saldo_hutang_sinar = $saldo_hutang_sinar + $jumlah_debit_hutang_sinar;}
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center"><?= $x['transaksi'] ?></th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_transaction/".$x['transaction_id']) ?>"><?= $jumlah_no_transaksi_hutang_sinar ?></th>
				<th class="text-left"><?= $jumlah_deskripsi_hutang_sinar ?></th>
				<th class="text-right"><?php echo number_format($jumlah_debit_hutang_sinar,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_kredit_hutang_sinar,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($saldo_hutang_sinar,0,',','.');?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- hutang_sinar -->

		<!-- hutang_lain_lain -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="55%" class="text-left"><button class="btn btn-info" type="button" data-toggle="collapse" data-target="#hutang_lain_lain" aria-expanded="false" aria-controls="hutang_lain_lain">2-20200) Hutang Lain Lain</button></th>
				<th width="15%" class="text-right"><?php echo number_format($total_debit_hutang_lain_lain_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($total_kredit_hutang_lain_lain_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($saldo_akhir_hutang_lain_lain_all,0,',','.');?></th>
	        </tr>
		</table>
		<table class="collapse table table-bordered" id="hutang_lain_lain" width="100%">
			<tr class="table-active" width="100%">
				<th width="10%" class="text-center">Tanggal</th>
				<th width="10%" class="text-center">Transaksi</th>
				<th width="20%" class="text-center">Nomor</th>
				<th width="15%" class="text-center">Keterangan</th>
				<th width="15%" class="text-center">Debit</th>
				<th width="15%" class="text-center">Kredit</th>
				<th width="15%" class="text-center">Saldo</th>
	        </tr>
			<?php foreach ($hutang_lain_lain as $key => $x) {
				
				if ($x['no_trx_1']==0) { $jumlah_no_transaksi_hutang_lain_lain = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];} else
				{$jumlah_no_transaksi_hutang_lain_lain = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];}

				if ($x['dex_1']==0) { $jumlah_deskripsi_hutang_lain_lain = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];} else
				{$jumlah_deskripsi_hutang_lain_lain = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];}

				if ($x['debit']==0) { $jumlah_debit_hutang_lain_lain = $x['debit'];} else
				{$jumlah_debit_hutang_lain_lain = $x['debit'];}

				if ($x['kredit']==0) { $jumlah_kredit_hutang_lain_lain = $x['kredit'];} else
				{$jumlah_kredit_hutang_lain_lain = $x['kredit'];}
				
				if ($jumlah_debit_hutang_lain_lain==0) { $saldo_hutang_lain_lain = $saldo_hutang_lain_lain + $jumlah_debit_hutang_lain_lain - $jumlah_kredit_hutang_lain_lain;} else
				{$saldo_hutang_lain_lain = $saldo_hutang_lain_lain + $jumlah_debit_hutang_lain_lain;}
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center"><?= $x['transaksi'] ?></th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_transaction/".$x['transaction_id']) ?>"><?= $jumlah_no_transaksi_hutang_lain_lain ?></th>
				<th class="text-left"><?= $jumlah_deskripsi_hutang_lain_lain ?></th>
				<th class="text-right"><?php echo number_format($jumlah_debit_hutang_lain_lain,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_kredit_hutang_lain_lain,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($saldo_hutang_lain_lain,0,',','.');?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- hutang_lain_lain -->

		<!-- biaya_alat_truck_mixer -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="55%" class="text-left"><button class="btn btn-info" type="button" data-toggle="collapse" data-target="#biaya_alat_truck_mixer" aria-expanded="false" aria-controls="biaya_alat_truck_mixer">(5-50203) Biaya Alat Truck Mixer</button></th>
				<th width="15%" class="text-right"><?php echo number_format($total_debit_biaya_alat_truck_mixer_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($total_kredit_biaya_alat_truck_mixer_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($saldo_akhir_biaya_alat_truck_mixer_all,0,',','.');?></th>
	        </tr>
		</table>
		<table class="collapse table table-bordered" id="biaya_alat_truck_mixer" width="100%">
			<tr class="table-active" width="100%">
				<th width="10%" class="text-center">Tanggal</th>
				<th width="10%" class="text-center">Transaksi</th>
				<th width="20%" class="text-center">Nomor</th>
				<th width="15%" class="text-center">Keterangan</th>
				<th width="15%" class="text-center">Debit</th>
				<th width="15%" class="text-center">Kredit</th>
				<th width="15%" class="text-center">Saldo</th>
	        </tr>
			<?php foreach ($biaya_alat_truck_mixer as $key => $x) {
				
				if ($x['no_trx_1']==0) { $jumlah_no_transaksi_biaya_alat_truck_mixer = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];} else
				{$jumlah_no_transaksi_biaya_alat_truck_mixer = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];}

				if ($x['dex_1']==0) { $jumlah_deskripsi_biaya_alat_truck_mixer = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];} else
				{$jumlah_deskripsi_biaya_alat_truck_mixer = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];}

				if ($x['debit']==0) { $jumlah_debit_biaya_alat_truck_mixer = $x['debit'];} else
				{$jumlah_debit_biaya_alat_truck_mixer = $x['debit'];}

				if ($x['kredit']==0) { $jumlah_kredit_biaya_alat_truck_mixer = $x['kredit'];} else
				{$jumlah_kredit_biaya_alat_truck_mixer = $x['kredit'];}
				
				if ($jumlah_debit_biaya_alat_truck_mixer==0) { $saldo_biaya_alat_truck_mixer = $saldo_biaya_alat_truck_mixer + $jumlah_debit_biaya_alat_truck_mixer - $jumlah_kredit_biaya_alat_truck_mixer;} else
				{$saldo_biaya_alat_truck_mixer = $saldo_biaya_alat_truck_mixer + $jumlah_debit_biaya_alat_truck_mixer;}
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center"><?= $x['transaksi'] ?></th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_transaction/".$x['transaction_id']) ?>"><?= $jumlah_no_transaksi_biaya_alat_truck_mixer ?></th>
				<th class="text-left"><?= $jumlah_deskripsi_biaya_alat_truck_mixer ?></th>
				<th class="text-right"><?php echo number_format($jumlah_debit_biaya_alat_truck_mixer,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_kredit_biaya_alat_truck_mixer,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($saldo_biaya_alat_truck_mixer,0,',','.');?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- biaya_alat_truck_mixer -->

		<!-- gaji -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="55%" class="text-left"><button class="btn btn-info" type="button" data-toggle="collapse" data-target="#gaji" aria-expanded="false" aria-controls="gaji">(5-50501) Gaji</button></th>
				<th width="15%" class="text-right"><?php echo number_format($total_debit_gaji_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($total_kredit_gaji_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($saldo_akhir_gaji_all,0,',','.');?></th>
	        </tr>
		</table>
		<table class="collapse table table-bordered" id="gaji" width="100%">
			<tr class="table-active" width="100%">
				<th width="10%" class="text-center">Tanggal</th>
				<th width="10%" class="text-center">Transaksi</th>
				<th width="20%" class="text-center">Nomor</th>
				<th width="15%" class="text-center">Keterangan</th>
				<th width="15%" class="text-center">Debit</th>
				<th width="15%" class="text-center">Kredit</th>
				<th width="15%" class="text-center">Saldo</th>
	        </tr>
			<?php foreach ($gaji as $key => $x) {
				
				if ($x['no_trx_1']==0) { $jumlah_no_transaksi_gaji = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];} else
				{$jumlah_no_transaksi_gaji = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];}

				if ($x['dex_1']==0) { $jumlah_deskripsi_gaji = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];} else
				{$jumlah_deskripsi_gaji = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];}

				if ($x['debit']==0) { $jumlah_debit_gaji = $x['debit'];} else
				{$jumlah_debit_gaji = $x['debit'];}

				if ($x['kredit']==0) { $jumlah_kredit_gaji = $x['kredit'];} else
				{$jumlah_kredit_gaji = $x['kredit'];}
				
				if ($jumlah_debit_gaji==0) { $saldo_gaji = $saldo_gaji + $jumlah_debit_gaji - $jumlah_kredit_gaji;} else
				{$saldo_gaji = $saldo_gaji + $jumlah_debit_gaji;}
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center"><?= $x['transaksi'] ?></th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_transaction/".$x['transaction_id']) ?>"><?= $jumlah_no_transaksi_gaji ?></th>
				<th class="text-left"><?= $jumlah_deskripsi_gaji ?></th>
				<th class="text-right"><?php echo number_format($jumlah_debit_gaji,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_kredit_gaji,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($saldo_gaji,0,',','.');?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- gaji -->

		<!-- upah -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="55%" class="text-left"><button class="btn btn-info" type="button" data-toggle="collapse" data-target="#upah" aria-expanded="false" aria-controls="upah">(5-50502) Upah</button></th>
				<th width="15%" class="text-right"><?php echo number_format($total_debit_upah_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($total_kredit_upah_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($saldo_akhir_upah_all,0,',','.');?></th>
	        </tr>
		</table>
		<table class="collapse table table-bordered" id="upah" width="100%">
			<tr class="table-active" width="100%">
				<th width="10%" class="text-center">Tanggal</th>
				<th width="10%" class="text-center">Transaksi</th>
				<th width="20%" class="text-center">Nomor</th>
				<th width="15%" class="text-center">Keterangan</th>
				<th width="15%" class="text-center">Debit</th>
				<th width="15%" class="text-center">Kredit</th>
				<th width="15%" class="text-center">Saldo</th>
	        </tr>
			<?php foreach ($upah as $key => $x) {
				
				if ($x['no_trx_1']==0) { $jumlah_no_transaksi_upah = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];} else
				{$jumlah_no_transaksi_upah = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];}

				if ($x['dex_1']==0) { $jumlah_deskripsi_upah = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];} else
				{$jumlah_deskripsi_upah = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];}

				if ($x['debit']==0) { $jumlah_debit_upah = $x['debit'];} else
				{$jumlah_debit_upah = $x['debit'];}

				if ($x['kredit']==0) { $jumlah_kredit_upah = $x['kredit'];} else
				{$jumlah_kredit_upah = $x['kredit'];}
				
				if ($jumlah_debit_upah==0) { $saldo_upah = $saldo_upah + $jumlah_debit_upah - $jumlah_kredit_upah;} else
				{$saldo_upah = $saldo_upah + $jumlah_debit_upah;}
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center"><?= $x['transaksi'] ?></th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_transaction/".$x['transaction_id']) ?>"><?= $jumlah_no_transaksi_upah ?></th>
				<th class="text-left"><?= $jumlah_deskripsi_upah ?></th>
				<th class="text-right"><?php echo number_format($jumlah_debit_upah,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_kredit_upah,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($saldo_upah,0,',','.');?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- upah -->

		<!-- konsumsi -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="55%" class="text-left"><button class="btn btn-info" type="button" data-toggle="collapse" data-target="#konsumsi" aria-expanded="false" aria-controls="konsumsi">(5-50503) Konsumsi</button></th>
				<th width="15%" class="text-right"><?php echo number_format($total_debit_konsumsi_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($total_kredit_konsumsi_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($saldo_akhir_konsumsi_all,0,',','.');?></th>
	        </tr>
		</table>
		<table class="collapse table table-bordered" id="konsumsi" width="100%">
			<tr class="table-active" width="100%">
				<th width="10%" class="text-center">Tanggal</th>
				<th width="10%" class="text-center">Transaksi</th>
				<th width="20%" class="text-center">Nomor</th>
				<th width="15%" class="text-center">Keterangan</th>
				<th width="15%" class="text-center">Debit</th>
				<th width="15%" class="text-center">Kredit</th>
				<th width="15%" class="text-center">Saldo</th>
	        </tr>
			<?php foreach ($konsumsi as $key => $x) {
				
				if ($x['no_trx_1']==0) { $jumlah_no_transaksi_konsumsi = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];} else
				{$jumlah_no_transaksi_konsumsi = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];}

				if ($x['dex_1']==0) { $jumlah_deskripsi_konsumsi = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];} else
				{$jumlah_deskripsi_konsumsi = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];}

				if ($x['debit']==0) { $jumlah_debit_konsumsi = $x['debit'];} else
				{$jumlah_debit_konsumsi = $x['debit'];}

				if ($x['kredit']==0) { $jumlah_kredit_konsumsi = $x['kredit'];} else
				{$jumlah_kredit_konsumsi = $x['kredit'];}
				
				if ($jumlah_debit_konsumsi==0) { $saldo_konsumsi = $saldo_konsumsi + $jumlah_debit_konsumsi - $jumlah_kredit_konsumsi;} else
				{$saldo_konsumsi = $saldo_konsumsi + $jumlah_debit_konsumsi;}
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center"><?= $x['transaksi'] ?></th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_transaction/".$x['transaction_id']) ?>"><?= $jumlah_no_transaksi_konsumsi ?></th>
				<th class="text-left"><?= $jumlah_deskripsi_konsumsi ?></th>
				<th class="text-right"><?php echo number_format($jumlah_debit_konsumsi,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_kredit_konsumsi,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($saldo_konsumsi,0,',','.');?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- konsumsi -->

		<!-- pengobatan -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="55%" class="text-left"><button class="btn btn-info" type="button" data-toggle="collapse" data-target="#pengobatan" aria-expanded="false" aria-controls="pengobatan">(5-50505) Pengobatan</button></th>
				<th width="15%" class="text-right"><?php echo number_format($total_debit_pengobatan_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($total_kredit_pengobatan_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($saldo_akhir_pengobatan_all,0,',','.');?></th>
	        </tr>
		</table>
		<table class="collapse table table-bordered" id="pengobatan" width="100%">
			<tr class="table-active" width="100%">
				<th width="10%" class="text-center">Tanggal</th>
				<th width="10%" class="text-center">Transaksi</th>
				<th width="20%" class="text-center">Nomor</th>
				<th width="15%" class="text-center">Keterangan</th>
				<th width="15%" class="text-center">Debit</th>
				<th width="15%" class="text-center">Kredit</th>
				<th width="15%" class="text-center">Saldo</th>
	        </tr>
			<?php foreach ($pengobatan as $key => $x) {
				
				if ($x['no_trx_1']==0) { $jumlah_no_transaksi_pengobatan = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];} else
				{$jumlah_no_transaksi_pengobatan = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];}

				if ($x['dex_1']==0) { $jumlah_deskripsi_pengobatan = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];} else
				{$jumlah_deskripsi_pengobatan = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];}

				if ($x['debit']==0) { $jumlah_debit_pengobatan = $x['debit'];} else
				{$jumlah_debit_pengobatan = $x['debit'];}

				if ($x['kredit']==0) { $jumlah_kredit_pengobatan = $x['kredit'];} else
				{$jumlah_kredit_pengobatan = $x['kredit'];}
				
				if ($jumlah_debit_pengobatan==0) { $saldo_pengobatan = $saldo_pengobatan + $jumlah_debit_pengobatan - $jumlah_kredit_pengobatan;} else
				{$saldo_pengobatan = $saldo_pengobatan + $jumlah_debit_pengobatan;}
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center"><?= $x['transaksi'] ?></th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_transaction/".$x['transaction_id']) ?>"><?= $jumlah_no_transaksi_pengobatan ?></th>
				<th class="text-left"><?= $jumlah_deskripsi_pengobatan ?></th>
				<th class="text-right"><?php echo number_format($jumlah_debit_pengobatan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_kredit_pengobatan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($saldo_pengobatan,0,',','.');?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- pengobatan -->

		<!-- thr_bonus -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="55%" class="text-left"><button class="btn btn-info" type="button" data-toggle="collapse" data-target="#thr_bonus" aria-expanded="false" aria-controls="thr_bonus">(5-50506) THR & Bonus</button></th>
				<th width="15%" class="text-right"><?php echo number_format($total_debit_thr_bonus_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($total_kredit_thr_bonus_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($saldo_akhir_thr_bonus_all,0,',','.');?></th>
	        </tr>
		</table>
		<table class="collapse table table-bordered" id="thr_bonus" width="100%">
			<tr class="table-active" width="100%">
				<th width="10%" class="text-center">Tanggal</th>
				<th width="10%" class="text-center">Transaksi</th>
				<th width="20%" class="text-center">Nomor</th>
				<th width="15%" class="text-center">Keterangan</th>
				<th width="15%" class="text-center">Debit</th>
				<th width="15%" class="text-center">Kredit</th>
				<th width="15%" class="text-center">Saldo</th>
	        </tr>
			<?php foreach ($thr_bonus as $key => $x) {
				
				if ($x['no_trx_1']==0) { $jumlah_no_transaksi_thr_bonus = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];} else
				{$jumlah_no_transaksi_thr_bonus = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];}

				if ($x['dex_1']==0) { $jumlah_deskripsi_thr_bonus = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];} else
				{$jumlah_deskripsi_thr_bonus = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];}

				if ($x['debit']==0) { $jumlah_debit_thr_bonus = $x['debit'];} else
				{$jumlah_debit_thr_bonus = $x['debit'];}

				if ($x['kredit']==0) { $jumlah_kredit_thr_bonus = $x['kredit'];} else
				{$jumlah_kredit_thr_bonus = $x['kredit'];}
				
				if ($jumlah_debit_thr_bonus==0) { $saldo_thr_bonus = $saldo_thr_bonus + $jumlah_debit_thr_bonus - $jumlah_kredit_thr_bonus;} else
				{$saldo_thr_bonus = $saldo_thr_bonus + $jumlah_debit_thr_bonus;}
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center"><?= $x['transaksi'] ?></th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_transaction/".$x['transaction_id']) ?>"><?= $jumlah_no_transaksi_thr_bonus ?></th>
				<th class="text-left"><?= $jumlah_deskripsi_thr_bonus ?></th>
				<th class="text-right"><?php echo number_format($jumlah_debit_thr_bonus,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_kredit_thr_bonus,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($saldo_thr_bonus,0,',','.');?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- thr_bonus -->

		<!-- bensin_tol_parkir -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="55%" class="text-left"><button class="btn btn-info" type="button" data-toggle="collapse" data-target="#bensin_tol_parkir" aria-expanded="false" aria-controls="bensin_tol_parkir">(5-50508) Bensin Tol dan Parkir - Umum</button></th>
				<th width="15%" class="text-right"><?php echo number_format($total_debit_bensin_tol_parkir_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($total_kredit_bensin_tol_parkir_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($saldo_akhir_bensin_tol_parkir_all,0,',','.');?></th>
	        </tr>
		</table>
		<table class="collapse table table-bordered" id="bensin_tol_parkir" width="100%">
			<tr class="table-active" width="100%">
				<th width="10%" class="text-center">Tanggal</th>
				<th width="10%" class="text-center">Transaksi</th>
				<th width="20%" class="text-center">Nomor</th>
				<th width="15%" class="text-center">Keterangan</th>
				<th width="15%" class="text-center">Debit</th>
				<th width="15%" class="text-center">Kredit</th>
				<th width="15%" class="text-center">Saldo</th>
	        </tr>
			<?php foreach ($bensin_tol_parkir as $key => $x) {
				
				if ($x['no_trx_1']==0) { $jumlah_no_transaksi_bensin_tol_parkir = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];} else
				{$jumlah_no_transaksi_bensin_tol_parkir = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];}

				if ($x['dex_1']==0) { $jumlah_deskripsi_bensin_tol_parkir = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];} else
				{$jumlah_deskripsi_bensin_tol_parkir = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];}

				if ($x['debit']==0) { $jumlah_debit_bensin_tol_parkir = $x['debit'];} else
				{$jumlah_debit_bensin_tol_parkir = $x['debit'];}

				if ($x['kredit']==0) { $jumlah_kredit_bensin_tol_parkir = $x['kredit'];} else
				{$jumlah_kredit_bensin_tol_parkir = $x['kredit'];}
				
				if ($jumlah_debit_bensin_tol_parkir==0) { $saldo_bensin_tol_parkir = $saldo_bensin_tol_parkir + $jumlah_debit_bensin_tol_parkir - $jumlah_kredit_bensin_tol_parkir;} else
				{$saldo_bensin_tol_parkir = $saldo_bensin_tol_parkir + $jumlah_debit_bensin_tol_parkir;}
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center"><?= $x['transaksi'] ?></th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_transaction/".$x['transaction_id']) ?>"><?= $jumlah_no_transaksi_bensin_tol_parkir ?></th>
				<th class="text-left"><?= $jumlah_deskripsi_bensin_tol_parkir ?></th>
				<th class="text-right"><?php echo number_format($jumlah_debit_bensin_tol_parkir,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_kredit_bensin_tol_parkir,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($saldo_bensin_tol_parkir,0,',','.');?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- bensin_tol_parkir -->

		<!-- perjalanan_dinas_penjualan -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="55%" class="text-left"><button class="btn btn-info" type="button" data-toggle="collapse" data-target="#perjalanan_dinas_penjualan" aria-expanded="false" aria-controls="perjalanan_dinas_penjualan">(5-50509) Perjalanan Dinas - Penjualan</button></th>
				<th width="15%" class="text-right"><?php echo number_format($total_debit_perjalanan_dinas_penjualan_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($total_kredit_perjalanan_dinas_penjualan_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($saldo_akhir_perjalanan_dinas_penjualan_all,0,',','.');?></th>
	        </tr>
		</table>
		<table class="collapse table table-bordered" id="perjalanan_dinas_penjualan" width="100%">
			<tr class="table-active" width="100%">
				<th width="10%" class="text-center">Tanggal</th>
				<th width="10%" class="text-center">Transaksi</th>
				<th width="20%" class="text-center">Nomor</th>
				<th width="15%" class="text-center">Keterangan</th>
				<th width="15%" class="text-center">Debit</th>
				<th width="15%" class="text-center">Kredit</th>
				<th width="15%" class="text-center">Saldo</th>
	        </tr>
			<?php foreach ($perjalanan_dinas_penjualan as $key => $x) {
				
				if ($x['no_trx_1']==0) { $jumlah_no_transaksi_perjalanan_dinas_penjualan = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];} else
				{$jumlah_no_transaksi_perjalanan_dinas_penjualan = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];}

				if ($x['dex_1']==0) { $jumlah_deskripsi_perjalanan_dinas_penjualan = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];} else
				{$jumlah_deskripsi_perjalanan_dinas_penjualan = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];}

				if ($x['debit']==0) { $jumlah_debit_perjalanan_dinas_penjualan = $x['debit'];} else
				{$jumlah_debit_perjalanan_dinas_penjualan = $x['debit'];}

				if ($x['kredit']==0) { $jumlah_kredit_perjalanan_dinas_penjualan = $x['kredit'];} else
				{$jumlah_kredit_perjalanan_dinas_penjualan = $x['kredit'];}
				
				if ($jumlah_debit_perjalanan_dinas_penjualan==0) { $saldo_perjalanan_dinas_penjualan = $saldo_perjalanan_dinas_penjualan + $jumlah_debit_perjalanan_dinas_penjualan - $jumlah_kredit_perjalanan_dinas_penjualan;} else
				{$saldo_perjalanan_dinas_penjualan = $saldo_perjalanan_dinas_penjualan + $jumlah_debit_perjalanan_dinas_penjualan;}
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center"><?= $x['transaksi'] ?></th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_transaction/".$x['transaction_id']) ?>"><?= $jumlah_no_transaksi_perjalanan_dinas_penjualan ?></th>
				<th class="text-left"><?= $jumlah_deskripsi_perjalanan_dinas_penjualan ?></th>
				<th class="text-right"><?php echo number_format($jumlah_debit_perjalanan_dinas_penjualan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_kredit_perjalanan_dinas_penjualan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($saldo_perjalanan_dinas_penjualan,0,',','.');?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- perjalanan_dinas_penjualan -->

		<!-- pakaian_dinas -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="55%" class="text-left"><button class="btn btn-info" type="button" data-toggle="collapse" data-target="#pakaian_dinas" aria-expanded="false" aria-controls="pakaian_dinas">(5-50510) Pakaian Dinas & K3</button></th>
				<th width="15%" class="text-right"><?php echo number_format($total_debit_pakaian_dinas_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($total_kredit_pakaian_dinas_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($saldo_akhir_pakaian_dinas_all,0,',','.');?></th>
	        </tr>
		</table>
		<table class="collapse table table-bordered" id="pakaian_dinas" width="100%">
			<tr class="table-active" width="100%">
				<th width="10%" class="text-center">Tanggal</th>
				<th width="10%" class="text-center">Transaksi</th>
				<th width="20%" class="text-center">Nomor</th>
				<th width="15%" class="text-center">Keterangan</th>
				<th width="15%" class="text-center">Debit</th>
				<th width="15%" class="text-center">Kredit</th>
				<th width="15%" class="text-center">Saldo</th>
	        </tr>
			<?php foreach ($pakaian_dinas as $key => $x) {
				
				if ($x['no_trx_1']==0) { $jumlah_no_transaksi_pakaian_dinas = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];} else
				{$jumlah_no_transaksi_pakaian_dinas = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];}

				if ($x['dex_1']==0) { $jumlah_deskripsi_pakaian_dinas = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];} else
				{$jumlah_deskripsi_pakaian_dinas = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];}

				if ($x['debit']==0) { $jumlah_debit_pakaian_dinas = $x['debit'];} else
				{$jumlah_debit_pakaian_dinas = $x['debit'];}

				if ($x['kredit']==0) { $jumlah_kredit_pakaian_dinas = $x['kredit'];} else
				{$jumlah_kredit_pakaian_dinas = $x['kredit'];}
				
				if ($jumlah_debit_pakaian_dinas==0) { $saldo_pakaian_dinas = $saldo_pakaian_dinas + $jumlah_debit_pakaian_dinas - $jumlah_kredit_pakaian_dinas;} else
				{$saldo_pakaian_dinas = $saldo_pakaian_dinas + $jumlah_debit_pakaian_dinas;}
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center"><?= $x['transaksi'] ?></th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_transaction/".$x['transaction_id']) ?>"><?= $jumlah_no_transaksi_pakaian_dinas ?></th>
				<th class="text-left"><?= $jumlah_deskripsi_pakaian_dinas ?></th>
				<th class="text-right"><?php echo number_format($jumlah_debit_pakaian_dinas,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_kredit_pakaian_dinas,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($saldo_pakaian_dinas,0,',','.');?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- pakaian_dinas -->

		<!-- beban_kirim -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="55%" class="text-left"><button class="btn btn-info" type="button" data-toggle="collapse" data-target="#beban_kirim" aria-expanded="false" aria-controls="beban_kirim">(5-50511) Beban Kirim</button></th>
				<th width="15%" class="text-right"><?php echo number_format($total_debit_beban_kirim_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($total_kredit_beban_kirim_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($saldo_akhir_beban_kirim_all,0,',','.');?></th>
	        </tr>
		</table>
		<table class="collapse table table-bordered" id="beban_kirim" width="100%">
			<tr class="table-active" width="100%">
				<th width="10%" class="text-center">Tanggal</th>
				<th width="10%" class="text-center">Transaksi</th>
				<th width="20%" class="text-center">Nomor</th>
				<th width="15%" class="text-center">Keterangan</th>
				<th width="15%" class="text-center">Debit</th>
				<th width="15%" class="text-center">Kredit</th>
				<th width="15%" class="text-center">Saldo</th>
	        </tr>
			<?php foreach ($beban_kirim as $key => $x) {
				
				if ($x['no_trx_1']==0) { $jumlah_no_transaksi_beban_kirim = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];} else
				{$jumlah_no_transaksi_beban_kirim = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];}

				if ($x['dex_1']==0) { $jumlah_deskripsi_beban_kirim = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];} else
				{$jumlah_deskripsi_beban_kirim = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];}

				if ($x['debit']==0) { $jumlah_debit_beban_kirim = $x['debit'];} else
				{$jumlah_debit_beban_kirim = $x['debit'];}

				if ($x['kredit']==0) { $jumlah_kredit_beban_kirim = $x['kredit'];} else
				{$jumlah_kredit_beban_kirim = $x['kredit'];}
				
				if ($jumlah_debit_beban_kirim==0) { $saldo_beban_kirim = $saldo_beban_kirim + $jumlah_debit_beban_kirim - $jumlah_kredit_beban_kirim;} else
				{$saldo_beban_kirim = $saldo_beban_kirim + $jumlah_debit_beban_kirim;}
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center"><?= $x['transaksi'] ?></th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_transaction/".$x['transaction_id']) ?>"><?= $jumlah_no_transaksi_beban_kirim ?></th>
				<th class="text-left"><?= $jumlah_deskripsi_beban_kirim ?></th>
				<th class="text-right"><?php echo number_format($jumlah_debit_beban_kirim,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_kredit_beban_kirim,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($saldo_beban_kirim,0,',','.');?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- beban_kirim -->

		<!-- pengujian_material_laboratorium -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="55%" class="text-left"><button class="btn btn-info" type="button" data-toggle="collapse" data-target="#pengujian_material_laboratorium" aria-expanded="false" aria-controls="pengujian_material_laboratorium">(5-50512) Pengujian Material & Laboratorium</button></th>
				<th width="15%" class="text-right"><?php echo number_format($total_debit_pengujian_material_laboratorium_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($total_kredit_pengujian_material_laboratorium_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($saldo_akhir_pengujian_material_laboratorium_all,0,',','.');?></th>
	        </tr>
		</table>
		<table class="collapse table table-bordered" id="pengujian_material_laboratorium" width="100%">
			<tr class="table-active" width="100%">
				<th width="10%" class="text-center">Tanggal</th>
				<th width="10%" class="text-center">Transaksi</th>
				<th width="20%" class="text-center">Nomor</th>
				<th width="15%" class="text-center">Keterangan</th>
				<th width="15%" class="text-center">Debit</th>
				<th width="15%" class="text-center">Kredit</th>
				<th width="15%" class="text-center">Saldo</th>
	        </tr>
			<?php foreach ($pengujian_material_laboratorium as $key => $x) {
				
				if ($x['no_trx_1']==0) { $jumlah_no_transaksi_pengujian_material_laboratorium = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];} else
				{$jumlah_no_transaksi_pengujian_material_laboratorium = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];}

				if ($x['dex_1']==0) { $jumlah_deskripsi_pengujian_material_laboratorium = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];} else
				{$jumlah_deskripsi_pengujian_material_laboratorium = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];}

				if ($x['debit']==0) { $jumlah_debit_pengujian_material_laboratorium = $x['debit'];} else
				{$jumlah_debit_pengujian_material_laboratorium = $x['debit'];}

				if ($x['kredit']==0) { $jumlah_kredit_pengujian_material_laboratorium = $x['kredit'];} else
				{$jumlah_kredit_pengujian_material_laboratorium = $x['kredit'];}
				
				if ($jumlah_debit_pengujian_material_laboratorium==0) { $saldo_pengujian_material_laboratorium = $saldo_pengujian_material_laboratorium + $jumlah_debit_pengujian_material_laboratorium - $jumlah_kredit_pengujian_material_laboratorium;} else
				{$saldo_pengujian_material_laboratorium = $saldo_pengujian_material_laboratorium + $jumlah_debit_pengujian_material_laboratorium;}
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center"><?= $x['transaksi'] ?></th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_transaction/".$x['transaction_id']) ?>"><?= $jumlah_no_transaksi_pengujian_material_laboratorium ?></th>
				<th class="text-left"><?= $jumlah_deskripsi_pengujian_material_laboratorium ?></th>
				<th class="text-right"><?php echo number_format($jumlah_debit_pengujian_material_laboratorium,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_kredit_pengujian_material_laboratorium,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($saldo_pengujian_material_laboratorium,0,',','.');?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- pengujian_material_laboratorium -->

		<!-- listrik_internet -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="55%" class="text-left"><button class="btn btn-info" type="button" data-toggle="collapse" data-target="#listrik_internet" aria-expanded="false" aria-controls="listrik_internet">5-50513) Listrik & Internet</button></th>
				<th width="15%" class="text-right"><?php echo number_format($total_debit_listrik_internet_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($total_kredit_listrik_internet_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($saldo_akhir_listrik_internet_all,0,',','.');?></th>
	        </tr>
		</table>
		<table class="collapse table table-bordered" id="listrik_internet" width="100%">
			<tr class="table-active" width="100%">
				<th width="10%" class="text-center">Tanggal</th>
				<th width="10%" class="text-center">Transaksi</th>
				<th width="20%" class="text-center">Nomor</th>
				<th width="15%" class="text-center">Keterangan</th>
				<th width="15%" class="text-center">Debit</th>
				<th width="15%" class="text-center">Kredit</th>
				<th width="15%" class="text-center">Saldo</th>
	        </tr>
			<?php foreach ($listrik_internet as $key => $x) {
				
				if ($x['no_trx_1']==0) { $jumlah_no_transaksi_listrik_internet = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];} else
				{$jumlah_no_transaksi_listrik_internet = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];}

				if ($x['dex_1']==0) { $jumlah_deskripsi_listrik_internet = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];} else
				{$jumlah_deskripsi_listrik_internet = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];}

				if ($x['debit']==0) { $jumlah_debit_listrik_internet = $x['debit'];} else
				{$jumlah_debit_listrik_internet = $x['debit'];}

				if ($x['kredit']==0) { $jumlah_kredit_listrik_internet = $x['kredit'];} else
				{$jumlah_kredit_listrik_internet = $x['kredit'];}
				
				if ($jumlah_debit_listrik_internet==0) { $saldo_listrik_internet = $saldo_listrik_internet + $jumlah_debit_listrik_internet - $jumlah_kredit_listrik_internet;} else
				{$saldo_listrik_internet = $saldo_listrik_internet + $jumlah_debit_listrik_internet;}
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center"><?= $x['transaksi'] ?></th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_transaction/".$x['transaction_id']) ?>"><?= $jumlah_no_transaksi_listrik_internet ?></th>
				<th class="text-left"><?= $jumlah_deskripsi_listrik_internet ?></th>
				<th class="text-right"><?php echo number_format($jumlah_debit_listrik_internet,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_kredit_listrik_internet,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($saldo_listrik_internet,0,',','.');?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- listrik_internet -->

		<!-- keamanan_kebersihan -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="55%" class="text-left"><button class="btn btn-info" type="button" data-toggle="collapse" data-target="#keamanan_kebersihan" aria-expanded="false" aria-controls="keamanan_kebersihan">(5-50515) Keamanan dan Kebersihan</button></button></th>
				<th width="15%" class="text-right"><?php echo number_format($total_debit_keamanan_kebersihan_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($total_kredit_keamanan_kebersihan_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($saldo_akhir_keamanan_kebersihan_all,0,',','.');?></th>
	        </tr>
		</table>
		<table class="collapse table table-bordered" id="keamanan_kebersihan" width="100%">
			<tr class="table-active" width="100%">
				<th width="10%" class="text-center">Tanggal</th>
				<th width="10%" class="text-center">Transaksi</th>
				<th width="20%" class="text-center">Nomor</th>
				<th width="15%" class="text-center">Keterangan</th>
				<th width="15%" class="text-center">Debit</th>
				<th width="15%" class="text-center">Kredit</th>
				<th width="15%" class="text-center">Saldo</th>
	        </tr>
			<?php foreach ($keamanan_kebersihan as $key => $x) {
				
				if ($x['no_trx_1']==0) { $jumlah_no_transaksi_keamanan_kebersihan = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];} else
				{$jumlah_no_transaksi_keamanan_kebersihan = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];}

				if ($x['dex_1']==0) { $jumlah_deskripsi_keamanan_kebersihan = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];} else
				{$jumlah_deskripsi_keamanan_kebersihan = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];}

				if ($x['debit']==0) { $jumlah_debit_keamanan_kebersihan = $x['debit'];} else
				{$jumlah_debit_keamanan_kebersihan = $x['debit'];}

				if ($x['kredit']==0) { $jumlah_kredit_keamanan_kebersihan = $x['kredit'];} else
				{$jumlah_kredit_keamanan_kebersihan = $x['kredit'];}
				
				if ($jumlah_debit_keamanan_kebersihan==0) { $saldo_keamanan_kebersihan = $saldo_keamanan_kebersihan + $jumlah_debit_keamanan_kebersihan - $jumlah_kredit_keamanan_kebersihan;} else
				{$saldo_keamanan_kebersihan = $saldo_keamanan_kebersihan + $jumlah_debit_keamanan_kebersihan;}
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center"><?= $x['transaksi'] ?></th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_transaction/".$x['transaction_id']) ?>"><?= $jumlah_no_transaksi_keamanan_kebersihan ?></th>
				<th class="text-left"><?= $jumlah_deskripsi_keamanan_kebersihan ?></th>
				<th class="text-right"><?php echo number_format($jumlah_debit_keamanan_kebersihan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_kredit_keamanan_kebersihan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($saldo_keamanan_kebersihan,0,',','.');?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- keamanan_kebersihan -->

		<!-- perlengkapan_kantor -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="55%" class="text-left"><button class="btn btn-info" type="button" data-toggle="collapse" data-target="#perlengkapan_kantor" aria-expanded="false" aria-controls="perlengkapan_kantor">(5-50516) Perlengkapan Kantor</button></th>
				<th width="15%" class="text-right"><?php echo number_format($total_debit_perlengkapan_kantor_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($total_kredit_perlengkapan_kantor_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($saldo_akhir_perlengkapan_kantor_all,0,',','.');?></th>
	        </tr>
		</table>
		<table class="collapse table table-bordered" id="perlengkapan_kantor" width="100%">
			<tr class="table-active" width="100%">
				<th width="10%" class="text-center">Tanggal</th>
				<th width="10%" class="text-center">Transaksi</th>
				<th width="20%" class="text-center">Nomor</th>
				<th width="15%" class="text-center">Keterangan</th>
				<th width="15%" class="text-center">Debit</th>
				<th width="15%" class="text-center">Kredit</th>
				<th width="15%" class="text-center">Saldo</th>
	        </tr>
			<?php foreach ($perlengkapan_kantor as $key => $x) {
				
				if ($x['no_trx_1']==0) { $jumlah_no_transaksi_perlengkapan_kantor = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];} else
				{$jumlah_no_transaksi_perlengkapan_kantor = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];}

				if ($x['dex_1']==0) { $jumlah_deskripsi_perlengkapan_kantor = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];} else
				{$jumlah_deskripsi_perlengkapan_kantor = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];}

				if ($x['debit']==0) { $jumlah_debit_perlengkapan_kantor = $x['debit'];} else
				{$jumlah_debit_perlengkapan_kantor = $x['debit'];}

				if ($x['kredit']==0) { $jumlah_kredit_perlengkapan_kantor = $x['kredit'];} else
				{$jumlah_kredit_perlengkapan_kantor = $x['kredit'];}
				
				if ($jumlah_debit_perlengkapan_kantor==0) { $saldo_perlengkapan_kantor = $saldo_perlengkapan_kantor + $jumlah_debit_perlengkapan_kantor - $jumlah_kredit_perlengkapan_kantor;} else
				{$saldo_perlengkapan_kantor = $saldo_perlengkapan_kantor + $jumlah_debit_perlengkapan_kantor;}
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center"><?= $x['transaksi'] ?></th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_transaction/".$x['transaction_id']) ?>"><?= $jumlah_no_transaksi_perlengkapan_kantor ?></th>
				<th class="text-left"><?= $jumlah_deskripsi_perlengkapan_kantor ?></th>
				<th class="text-right"><?php echo number_format($jumlah_debit_perlengkapan_kantor,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_kredit_perlengkapan_kantor,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($saldo_perlengkapan_kantor,0,',','.');?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- perlengkapan_kantor -->

		<!-- beban_lain_lain -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="55%" class="text-left"><button class="btn btn-info" type="button" data-toggle="collapse" data-target="#beban_lain_lain" aria-expanded="false" aria-controls="beban_lain_lain">(5-50517) Beban Lain-Lain</button></th>
				<th width="15%" class="text-right"><?php echo number_format($total_debit_beban_lain_lain_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($total_kredit_beban_lain_lain_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($saldo_akhir_beban_lain_lain_all,0,',','.');?></th>
	        </tr>
		</table>
		<table class="collapse table table-bordered" id="beban_lain_lain" width="100%">
			<tr class="table-active" width="100%">
				<th width="10%" class="text-center">Tanggal</th>
				<th width="10%" class="text-center">Transaksi</th>
				<th width="20%" class="text-center">Nomor</th>
				<th width="15%" class="text-center">Keterangan</th>
				<th width="15%" class="text-center">Debit</th>
				<th width="15%" class="text-center">Kredit</th>
				<th width="15%" class="text-center">Saldo</th>
	        </tr>
			<?php foreach ($beban_lain_lain as $key => $x) {
				
				if ($x['no_trx_1']==0) { $jumlah_no_transaksi_beban_lain_lain = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];} else
				{$jumlah_no_transaksi_beban_lain_lain = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];}

				if ($x['dex_1']==0) { $jumlah_deskripsi_beban_lain_lain = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];} else
				{$jumlah_deskripsi_beban_lain_lain = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];}

				if ($x['debit']==0) { $jumlah_debit_beban_lain_lain = $x['debit'];} else
				{$jumlah_debit_beban_lain_lain = $x['debit'];}

				if ($x['kredit']==0) { $jumlah_kredit_beban_lain_lain = $x['kredit'];} else
				{$jumlah_kredit_beban_lain_lain = $x['kredit'];}
				
				if ($jumlah_debit_beban_lain_lain==0) { $saldo_beban_lain_lain = $saldo_beban_lain_lain + $jumlah_debit_beban_lain_lain - $jumlah_kredit_beban_lain_lain;} else
				{$saldo_beban_lain_lain = $saldo_beban_lain_lain + $jumlah_debit_beban_lain_lain;}
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center"><?= $x['transaksi'] ?></th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_transaction/".$x['transaction_id']) ?>"><?= $jumlah_no_transaksi_beban_lain_lain ?></th>
				<th class="text-left"><?= $jumlah_deskripsi_beban_lain_lain ?></th>
				<th class="text-right"><?php echo number_format($jumlah_debit_beban_lain_lain,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_kredit_beban_lain_lain,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($saldo_beban_lain_lain,0,',','.');?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- beban_lain_lain -->

		<!-- biaya_adm_bank -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="55%" class="text-left"><button class="btn btn-info" type="button" data-toggle="collapse" data-target="#biaya_adm_bank" aria-expanded="false" aria-controls="biaya_adm_bank">(5-50518) Beban Adm Bank</button></th>
				<th width="15%" class="text-right"><?php echo number_format($total_debit_biaya_adm_bank_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($total_kredit_biaya_adm_bank_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($saldo_akhir_biaya_adm_bank_all,0,',','.');?></th>
	        </tr>
		</table>
		<table class="collapse table table-bordered" id="biaya_adm_bank" width="100%">
			<tr class="table-active" width="100%">
				<th width="10%" class="text-center">Tanggal</th>
				<th width="10%" class="text-center">Transaksi</th>
				<th width="20%" class="text-center">Nomor</th>
				<th width="15%" class="text-center">Keterangan</th>
				<th width="15%" class="text-center">Debit</th>
				<th width="15%" class="text-center">Kredit</th>
				<th width="15%" class="text-center">Saldo</th>
	        </tr>
			<?php foreach ($biaya_adm_bank as $key => $x) {
				
				if ($x['no_trx_1']==0) { $jumlah_no_transaksi_biaya_adm_bank = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];} else
				{$jumlah_no_transaksi_biaya_adm_bank = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];}

				if ($x['dex_1']==0) { $jumlah_deskripsi_biaya_adm_bank = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];} else
				{$jumlah_deskripsi_biaya_adm_bank = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];}

				if ($x['debit']==0) { $jumlah_debit_biaya_adm_bank = $x['debit'];} else
				{$jumlah_debit_biaya_adm_bank = $x['debit'];}

				if ($x['kredit']==0) { $jumlah_kredit_biaya_adm_bank = $x['kredit'];} else
				{$jumlah_kredit_biaya_adm_bank = $x['kredit'];}
				
				if ($jumlah_debit_biaya_adm_bank==0) { $saldo_biaya_adm_bank = $saldo_biaya_adm_bank + $jumlah_debit_biaya_adm_bank - $jumlah_kredit_biaya_adm_bank;} else
				{$saldo_biaya_adm_bank = $saldo_biaya_adm_bank + $jumlah_debit_biaya_adm_bank;}
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center"><?= $x['transaksi'] ?></th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_transaction/".$x['transaction_id']) ?>"><?= $jumlah_no_transaksi_biaya_adm_bank ?></th>
				<th class="text-left"><?= $jumlah_deskripsi_biaya_adm_bank ?></th>
				<th class="text-right"><?php echo number_format($jumlah_debit_biaya_adm_bank,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_kredit_biaya_adm_bank,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($saldo_biaya_adm_bank,0,',','.');?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- biaya_adm_bank -->

		<!-- biaya_sewa_kendaraan -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="55%" class="text-left"><button class="btn btn-info" type="button" data-toggle="collapse" data-target="#biaya_sewa_kendaraan" aria-expanded="false" aria-controls="biaya_sewa_kendaraan">(5-50520) Biaya Sewa - Kendaraan</button></th>
				<th width="15%" class="text-right"><?php echo number_format($total_debit_biaya_sewa_kendaraan_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($total_kredit_biaya_sewa_kendaraan_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($saldo_akhir_biaya_sewa_kendaraan_all,0,',','.');?></th>
	        </tr>
		</table>
		<table class="collapse table table-bordered" id="biaya_sewa_kendaraan" width="100%">
			<tr class="table-active" width="100%">
				<th width="10%" class="text-center">Tanggal</th>
				<th width="10%" class="text-center">Transaksi</th>
				<th width="20%" class="text-center">Nomor</th>
				<th width="15%" class="text-center">Keterangan</th>
				<th width="15%" class="text-center">Debit</th>
				<th width="15%" class="text-center">Kredit</th>
				<th width="15%" class="text-center">Saldo</th>
	        </tr>
			<?php foreach ($biaya_sewa_kendaraan as $key => $x) {
				
				if ($x['no_trx_1']==0) { $jumlah_no_transaksi_biaya_sewa_kendaraan = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];} else
				{$jumlah_no_transaksi_biaya_sewa_kendaraan = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];}

				if ($x['dex_1']==0) { $jumlah_deskripsi_biaya_sewa_kendaraan = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];} else
				{$jumlah_deskripsi_biaya_sewa_kendaraan = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];}

				if ($x['debit']==0) { $jumlah_debit_biaya_sewa_kendaraan = $x['debit'];} else
				{$jumlah_debit_biaya_sewa_kendaraan = $x['debit'];}

				if ($x['kredit']==0) { $jumlah_kredit_biaya_sewa_kendaraan = $x['kredit'];} else
				{$jumlah_kredit_biaya_sewa_kendaraan = $x['kredit'];}
				
				if ($jumlah_debit_biaya_sewa_kendaraan==0) { $saldo_biaya_sewa_kendaraan = $saldo_biaya_sewa_kendaraan + $jumlah_debit_biaya_sewa_kendaraan - $jumlah_kredit_biaya_sewa_kendaraan;} else
				{$saldo_biaya_sewa_kendaraan = $saldo_biaya_sewa_kendaraan + $jumlah_debit_biaya_sewa_kendaraan;}
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center"><?= $x['transaksi'] ?></th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_transaction/".$x['transaction_id']) ?>"><?= $jumlah_no_transaksi_biaya_sewa_kendaraan ?></th>
				<th class="text-left"><?= $jumlah_deskripsi_biaya_sewa_kendaraan ?></th>
				<th class="text-right"><?php echo number_format($jumlah_debit_biaya_sewa_kendaraan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_kredit_biaya_sewa_kendaraan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($saldo_biaya_sewa_kendaraan,0,',','.');?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- biaya_sewa_kendaraan -->

		<!-- beban_bunga -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="55%" class="text-left"><button class="btn btn-info" type="button" data-toggle="collapse" data-target="#beban_bunga" aria-expanded="false" aria-controls="beban_bunga">(8-80000) Beban Bunga</button></th>
				<th width="15%" class="text-right"><?php echo number_format($total_debit_beban_bunga_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($total_kredit_beban_bunga_all,0,',','.');?></th>
				<th width="15%" class="text-right"><?php echo number_format($saldo_akhir_beban_bunga_all,0,',','.');?></th>
	        </tr>
		</table>
		<table class="collapse table table-bordered" id="beban_bunga" width="100%">
			<tr class="table-active" width="100%">
				<th width="10%" class="text-center">Tanggal</th>
				<th width="10%" class="text-center">Transaksi</th>
				<th width="20%" class="text-center">Nomor</th>
				<th width="15%" class="text-center">Keterangan</th>
				<th width="15%" class="text-center">Debit</th>
				<th width="15%" class="text-center">Kredit</th>
				<th width="15%" class="text-center">Saldo</th>
	        </tr>
			<?php foreach ($beban_bunga as $key => $x) {
				
				if ($x['no_trx_1']==0) { $jumlah_no_transaksi_beban_bunga = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];} else
				{$jumlah_no_transaksi_beban_bunga = $x['no_trx_1'] .= $x['no_trx_2'] .= $x['no_trx_3'] .= $x['no_trx_4'];}

				if ($x['dex_1']==0) { $jumlah_deskripsi_beban_bunga = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];} else
				{$jumlah_deskripsi_beban_bunga = $x['dex_1'] .= $x['dex_2'] .= $x['dex_3'] .= $x['dex_4'];}

				if ($x['debit']==0) { $jumlah_debit_beban_bunga = $x['debit'];} else
				{$jumlah_debit_beban_bunga = $x['debit'];}

				if ($x['kredit']==0) { $jumlah_kredit_beban_bunga = $x['kredit'];} else
				{$jumlah_kredit_beban_bunga = $x['kredit'];}
				
				if ($jumlah_debit_beban_bunga==0) { $saldo_beban_bunga = $saldo_beban_bunga + $jumlah_debit_beban_bunga - $jumlah_kredit_beban_bunga;} else
				{$saldo_beban_bunga = $saldo_beban_bunga + $jumlah_debit_beban_bunga;}
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center"><?= $x['transaksi'] ?></th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_transaction/".$x['transaction_id']) ?>"><?= $jumlah_no_transaksi_beban_bunga ?></th>
				<th class="text-left"><?= $jumlah_deskripsi_beban_bunga ?></th>
				<th class="text-right"><?php echo number_format($jumlah_debit_beban_bunga,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_kredit_beban_bunga,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($saldo_beban_bunga,0,',','.');?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- beban_bunga -->

		<?php
	}
}