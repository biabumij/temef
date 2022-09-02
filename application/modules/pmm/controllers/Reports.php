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
	            <th colspan="2">Periode</th>
				<th class="text-center"></th>
				<th class="text-center"></th>
	            <th class="text-center"><?php echo $filter_date;?></th>
	        </tr>

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

			$bahan = $total_nilai;
			$alat = $alat;
			$overhead = $overhead;
			$diskonto = $diskonto;

			$total_biaya_operasional = $bahan + $alat + $overhead + $diskonto;

			$laba_kotor = $total_penjualan_all - $total_biaya_operasional;

			$laba_sebelum_pajak = $laba_kotor;

			$persentase_laba_sebelum_pajak = ($total_penjualan_all!=0)?($laba_sebelum_pajak / $total_penjualan_all)  * 100:0;

	        ?>

			<tr class="table-active">
	            <th width="100%" class="text-left" colspan="5">Pendapatan Penjualan</th>
	        </tr>
			<tr class="table-active3">
	            <th width="10%" class="text-center">4-40000</th>
				<th width="90%" class="text-left" colspan="4">Pendapatan</th>
	        </tr>
			<?php foreach ($penjualan as $x): ?>
			<tr class="table-active3">
	            <th width="10%"></th>
				<th width="40%"><?= $x['nama'] ?></th>
				<th width="10%" class="text-right"><?php echo number_format($x['volume'],2,',','.');?></th>
				<th width="10%" class="text-center"><?= $x['measure'];?></th>
	            <th width="30%" class="text-right">
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
	        </tr>
			<tr class="table-active3">
				<th colspan="5"></th>
			</tr>
			<tr class="table-active">
				<th class="text-left" colspan="5">Beban Pokok Penjualan</th>
	        </tr>
			<tr class="table-active3">
	            <th class="text-center"></th>
				<th class="text-left" colspan="3"><a target="_blank" href="<?= base_url("laporan/cetak_bahan?filter_date=".$filter_date) ?>">Bahan</a></th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><?php echo number_format($bahan,0,',','.');?></span>
								</th>
							</tr>
					</table></th>
	        </tr>
			<tr class="table-active3">
	            <th class="text-center"></th>
				<th class="text-left" colspan="3"><a target="_blank" href="<?= base_url("laporan/cetak_alat?filter_date=".$filter_date) ?>">Alat</a></th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><?php echo number_format($alat,0,',','.');?></span>
								</th>
							</tr>
					</table></th>
	        </tr>
			<tr class="table-active3">
	            <th class="text-center"></th>
				<th class="text-left" colspan="3"><a target="_blank" href="<?= base_url("laporan/cetak_overhead?filter_date=".$filter_date) ?>">Overhead</a></th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><?php echo number_format($overhead,0,',','.');?></span>
								</th>
							</tr>
					</table></th>
	        </tr>
			<tr class="table-active3">
	            <th class="text-center"></th>
				<th class="text-left" colspan="3"><a target="_blank" href="<?= base_url("laporan/cetak_diskonto?filter_date=".$filter_date) ?>">Diskonto</a></th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><?php echo number_format($diskonto,0,',','.');?></span>
								</th>
							</tr>
					</table></th>
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
	        </tr>
			<tr class="table-active3">
				<th colspan="5"></th>
			</tr>
			<?php
				$styleColor = $laba_sebelum_pajak < 0 ? 'color:red' : 'color:black';
			?>
			<tr class="table-active3">
				<th class="text-left" colspan="4">Laba Kotor</th>
	            <th class="text-right" style="<?php echo $styleColor ?>">
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
	        </tr>
			<tr class="table-active3">
				<th colspan="5"></th>
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
	        </tr>
			<tr class="table-active3">
				<th colspan="5"></th>
			</tr>
			<tr class="table-active3">
	            <th colspan="4" class="text-left">Laba Sebelum Pajak</th>
	            <th class="text-right" style="<?php echo $styleColor ?>">
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
	        </tr>
			<tr class="table-active3">
	            <th colspan="4" class="text-left">Presentase</th>
	            <th class="text-right" style="<?php echo $styleColor ?>">
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
			->where("prm.material_id in (12,13,14,15,16,23,24)")
			->where("po.status in ('PUBLISH','CLOSED')")
			->group_by('po.supplier_id')
			->order_by('po.supplier_id','asc')
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
	            <th class="text-center" colspan="2">Periode</th>
	            <th class="text-center" colspan="2"><?php echo $filter_date;?></th>
	        </tr>

			<?php

			//kas
			$kas_biaya = $this->db->select('pdb.*, pb.id as biaya_id, pb.tanggal_transaksi, pb.nomor_transaksi')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.id',1)
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->group_by('pdb.id')
			->get()->result_array();

			$total_debit_kas_biaya = 0;
			foreach ($kas_biaya as $x){
				$total_debit_kas_biaya += $x['jumlah'];
			}

			$kas_jurnal = $this->db->select('pdb.*, pb.tanggal_transaksi, pb.id as jurnal_id, pb.nomor_transaksi, pb.total_debit, pb.total_kredit')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.id',1)
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->group_by('pdb.id')
			->get()->result_array();

			$total_debit_kas_jurnal = 0;
			$total_kredit_kas_jurnal = 0;

			foreach ($kas_jurnal as $x){
				$total_debit_kas_jurnal += $x['total_debit'];
				$total_kredit_kas_jurnal += $x['total_kredit'];
			}
			
			$total_debit_kas_all = 0;
			$total_kredit_kas_all = 0;
			$total_debit_kas_all = $total_debit_kas_biaya + $total_debit_kas_jurnal;
			$total_kredit_kas_all = $total_kredit_kas_jurnal;
			//kas

			//bank_kantor_pusat
			$bank_kantor_pusat_biaya = $this->db->select('pdb.*, pb.id as biaya_id, pb.tanggal_transaksi, pb.nomor_transaksi')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.id',217)
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->group_by('pdb.id')
			->get()->result_array();

			$total_debit_bank_kantor_pusat_biaya = 0;
			foreach ($bank_kantor_pusat_biaya as $x){
				$total_debit_bank_kantor_pusat_biaya += $x['jumlah'];
			}

			$bank_kantor_pusat_jurnal = $this->db->select('pdb.*, pb.id as jurnal_id, pb.tanggal_transaksi, pb.nomor_transaksi, pb.total_debit, pb.total_kredit')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.id',217)
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->group_by('pdb.id')
			->get()->result_array();

			$total_debit_bank_kantor_pusat_jurnal = 0;
			$total_kredit_bank_kantor_pusat_jurnal = 0;

			foreach ($bank_kantor_pusat_jurnal as $x){
				$total_debit_bank_kantor_pusat_jurnal += $x['total_debit'];
				$total_kredit_bank_kantor_pusat_jurnal += $x['total_kredit'];
			}
			
			$total_debit_bank_kantor_pusat_all = 0;
			$total_kredit_bank_kantor_pusat_all = 0;
			$total_debit_bank_kantor_pusat_all = $total_debit_bank_kantor_pusat_biaya + $total_debit_bank_kantor_pusat_jurnal;
			$total_kredit_bank_kantor_pusat_all = $total_kredit_bank_kantor_pusat_jurnal;
			//bank_kantor_pusat

			//hutang_lain_lain
			$hutang_lain_lain_biaya = $this->db->select('pdb.*, pb.id as biaya_id, pb.tanggal_transaksi, pb.nomor_transaksi')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.id',67)
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->group_by('pdb.id')
			->get()->result_array();

			$total_debit_hutang_lain_lain_biaya = 0;
			foreach ($hutang_lain_lain_biaya as $x){
				$total_debit_hutang_lain_lain_biaya += $x['jumlah'];
			}

			$hutang_lain_lain_jurnal = $this->db->select('pdb.*, pb.id as jurnal_id, pb.tanggal_transaksi, pb.nomor_transaksi, pb.total_debit, pb.total_kredit')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.id',67)
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->group_by('pdb.id')
			->get()->result_array();

			$total_debit_hutang_lain_lain_jurnal = 0;
			$total_kredit_hutang_lain_lain_jurnal = 0;

			foreach ($hutang_lain_lain_jurnal as $x){
				$total_debit_hutang_lain_lain_jurnal += $x['total_debit'];
				$total_kredit_hutang_lain_lain_jurnal += $x['total_kredit'];
			}
			
			$total_debit_hutang_lain_lain_all = 0;
			$total_kredit_hutang_lain_lain_all = 0;
			$total_debit_hutang_lain_lain_all = $total_debit_hutang_lain_lain_biaya + $total_debit_hutang_lain_lain_jurnal;
			$total_kredit_hutang_lain_lain_all = $total_kredit_hutang_lain_lain_jurnal;
			//hutang_lain_lain

			//perjalanan_dinas_penjualan
			$perjalanan_dinas_penjualan_biaya = $this->db->select('pdb.*, pb.id as biaya_id, pb.tanggal_transaksi, pb.nomor_transaksi')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.id',113)
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->group_by('pdb.id')
			->get()->result_array();

			$total_debit_perjalanan_dinas_penjualan_biaya = 0;
			foreach ($perjalanan_dinas_penjualan_biaya as $x){
				$total_debit_perjalanan_dinas_penjualan_biaya += $x['jumlah'];
			}

			$perjalanan_dinas_penjualan_jurnal = $this->db->select('pdb.*, pb.id as jurnal_id, pb.tanggal_transaksi, pb.nomor_transaksi, pb.total_debit, pb.total_kredit')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.id',113)
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->group_by('pdb.id')
			->get()->result_array();

			$total_debit_perjalanan_dinas_penjualan_jurnal = 0;
			$total_kredit_perjalanan_dinas_penjualan_jurnal = 0;

			foreach ($perjalanan_dinas_penjualan_jurnal as $x){
				$total_debit_perjalanan_dinas_penjualan_jurnal += $x['total_debit'];
				$total_kredit_perjalanan_dinas_penjualan_jurnal += $x['total_kredit'];
			}
			
			$total_debit_perjalanan_dinas_penjualan_all = 0;
			$total_kredit_perjalanan_dinas_penjualan_all = 0;
			$total_debit_perjalanan_dinas_penjualan_all = $total_debit_perjalanan_dinas_penjualan_biaya + $total_debit_perjalanan_dinas_penjualan_jurnal;
			$total_kredit_perjalanan_dinas_penjualan_all = $total_kredit_perjalanan_dinas_penjualan_jurnal;
			//perjalanan_dinas_penjualan

			//pengobatan
			$pengobatan_biaya = $this->db->select('pdb.*, pb.id as biaya_id, pb.tanggal_transaksi, pb.nomor_transaksi')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.id',121)
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->group_by('pdb.id')
			->get()->result_array();

			$total_debit_pengobatan_biaya = 0;
			foreach ($pengobatan_biaya as $x){
				$total_debit_pengobatan_biaya += $x['jumlah'];
			}

			$pengobatan_jurnal = $this->db->select('pdb.*, pb.id as jurnal_id, pb.tanggal_transaksi, pb.nomor_transaksi, pb.total_debit, pb.total_kredit')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.id',121)
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->group_by('pdb.id')
			->get()->result_array();

			$total_debit_pengobatan_jurnal = 0;
			$total_kredit_pengobatan_jurnal = 0;

			foreach ($pengobatan_jurnal as $x){
				$total_debit_pengobatan_jurnal += $x['total_debit'];
				$total_kredit_pengobatan_jurnal += $x['total_kredit'];
			}
			
			$total_debit_pengobatan_all = 0;
			$total_kredit_pengobatan_all = 0;
			$total_debit_pengobatan_all = $total_debit_pengobatan_biaya + $total_debit_pengobatan_jurnal;
			$total_kredit_pengobatan_all = $total_kredit_pengobatan_jurnal;
			//pengobatan

			//bensin_tol_parkir
			$bensin_tol_parkir_biaya = $this->db->select('pdb.*, pb.id as biaya_id, pb.tanggal_transaksi, pb.nomor_transaksi')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.id',129)
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->group_by('pdb.id')
			->get()->result_array();

			$total_debit_bensin_tol_parkir_biaya = 0;
			foreach ($bensin_tol_parkir_biaya as $x){
				$total_debit_bensin_tol_parkir_biaya += $x['jumlah'];
			}

			$bensin_tol_parkir_jurnal = $this->db->select('pdb.*, pb.id as jurnal_id, pb.tanggal_transaksi, pb.nomor_transaksi, pb.total_debit, pb.total_kredit')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.id',129)
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->group_by('pdb.id')
			->get()->result_array();

			$total_debit_bensin_tol_parkir_jurnal = 0;
			$total_kredit_bensin_tol_parkir_jurnal = 0;

			foreach ($bensin_tol_parkir_jurnal as $x){
				$total_debit_bensin_tol_parkir_jurnal += $x['total_debit'];
				$total_kredit_bensin_tol_parkir_jurnal += $x['total_kredit'];
			}
			
			$total_debit_bensin_tol_parkir_all = 0;
			$total_kredit_bensin_tol_parkir_all = 0;
			$total_debit_bensin_tol_parkir_all = $total_debit_bensin_tol_parkir_biaya + $total_debit_bensin_tol_parkir_jurnal;
			$total_kredit_bensin_tol_parkir_all = $total_kredit_bensin_tol_parkir_jurnal;
			//bensin_tol_parkir

			//pakaian_dinas
			$pakaian_dinas_biaya = $this->db->select('pdb.*, pb.id as biaya_id, pb.tanggal_transaksi, pb.nomor_transaksi')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.id',138)
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->group_by('pdb.id')
			->get()->result_array();

			$total_debit_pakaian_dinas_biaya = 0;
			foreach ($pakaian_dinas_biaya as $x){
				$total_debit_pakaian_dinas_biaya += $x['jumlah'];
			}

			$pakaian_dinas_jurnal = $this->db->select('pdb.*, pb.id as jurnal_id, pb.tanggal_transaksi, pb.nomor_transaksi, pb.total_debit, pb.total_kredit')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.id',138)
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->group_by('pdb.id')
			->get()->result_array();

			$total_debit_pakaian_dinas_jurnal = 0;
			$total_kredit_pakaian_dinas_jurnal = 0;

			foreach ($pakaian_dinas_jurnal as $x){
				$total_debit_pakaian_dinas_jurnal += $x['total_debit'];
				$total_kredit_pakaian_dinas_jurnal += $x['total_kredit'];
			}
			
			$total_debit_pakaian_dinas_all = 0;
			$total_kredit_pakaian_dinas_all = 0;
			$total_debit_pakaian_dinas_all = $total_debit_pakaian_dinas_biaya + $total_debit_pakaian_dinas_jurnal;
			$total_kredit_pakaian_dinas_all = $total_kredit_pakaian_dinas_jurnal;
			//pakaian_dinas

			//biaya_adm_bank
			$biaya_adm_bank_biaya = $this->db->select('pdb.*, pb.id as biaya_id, pb.tanggal_transaksi, pb.nomor_transaksi')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.id',143)
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->group_by('pdb.id')
			->get()->result_array();

			$total_debit_biaya_adm_bank_biaya = 0;
			foreach ($biaya_adm_bank_biaya as $x){
				$total_debit_biaya_adm_bank_biaya += $x['jumlah'];
			}

			$biaya_adm_bank_jurnal = $this->db->select('pdb.*, pb.id as jurnal_id, pb.tanggal_transaksi, pb.nomor_transaksi, pb.total_debit, pb.total_kredit')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.id',143)
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->group_by('pdb.id')
			->get()->result_array();

			$total_debit_biaya_adm_bank_jurnal = 0;
			$total_kredit_biaya_adm_bank_jurnal = 0;

			foreach ($biaya_adm_bank_jurnal as $x){
				$total_debit_biaya_adm_bank_jurnal += $x['total_debit'];
				$total_kredit_biaya_adm_bank_jurnal += $x['total_kredit'];
			}
			
			$total_debit_biaya_adm_bank_all = 0;
			$total_kredit_biaya_adm_bank_all = 0;
			$total_debit_biaya_adm_bank_all = $total_debit_biaya_adm_bank_biaya + $total_debit_biaya_adm_bank_jurnal;
			$total_kredit_biaya_adm_bank_all = $total_kredit_biaya_adm_bank_jurnal;
			//biaya_adm_bank

			//beban_lain_lain
			$beban_kirim_biaya = $this->db->select('pdb.*, pb.id as biaya_id, pb.tanggal_transaksi, pb.nomor_transaksi')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.id',145)
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->group_by('pdb.id')
			->get()->result_array();

			$total_debit_beban_kirim_biaya = 0;
			foreach ($beban_kirim_biaya as $x){
				$total_debit_beban_kirim_biaya += $x['jumlah'];
			}

			$beban_kirim_jurnal = $this->db->select('pdb.*, pb.id as jurnal_id, pb.tanggal_transaksi, pb.nomor_transaksi, pb.total_debit, pb.total_kredit')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.id',145)
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->group_by('pdb.id')
			->get()->result_array();

			$total_debit_beban_kirim_jurnal = 0;
			$total_kredit_beban_kirim_jurnal = 0;

			foreach ($beban_kirim_jurnal as $x){
				$total_debit_beban_kirim_jurnal += $x['total_debit'];
				$total_kredit_beban_kirim_jurnal += $x['total_kredit'];
			}
			
			$total_debit_beban_kirim_all = 0;
			$total_kredit_beban_kirim_all = 0;
			$total_debit_beban_kirim_all = $total_debit_beban_kirim_biaya + $total_debit_beban_kirim_jurnal;
			$total_kredit_beban_kirim_all = $total_kredit_beban_kirim_jurnal;
			//beban_kirim

			//beban_lain_lain
			$beban_lain_lain_biaya = $this->db->select('pdb.*, pb.id as biaya_id, pb.tanggal_transaksi, pb.nomor_transaksi')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.id',146)
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->group_by('pdb.id')
			->get()->result_array();

			$total_debit_beban_lain_lain_biaya = 0;
			foreach ($beban_lain_lain_biaya as $x){
				$total_debit_beban_lain_lain_biaya += $x['jumlah'];
			}

			$beban_lain_lain_jurnal = $this->db->select('pdb.*, pb.id as jurnal_id, pb.tanggal_transaksi, pb.nomor_transaksi, pb.total_debit, pb.total_kredit')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.id',146)
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->group_by('pdb.id')
			->get()->result_array();

			$total_debit_beban_lain_lain_jurnal = 0;
			$total_kredit_beban_lain_lain_jurnal = 0;

			foreach ($beban_lain_lain_jurnal as $x){
				$total_debit_beban_lain_lain_jurnal += $x['total_debit'];
				$total_kredit_beban_lain_lain_jurnal += $x['total_kredit'];
			}
			
			$total_debit_beban_lain_lain_all = 0;
			$total_kredit_beban_lain_lain_all = 0;
			$total_debit_beban_lain_lain_all = $total_debit_beban_lain_lain_biaya + $total_debit_beban_lain_lain_jurnal;
			$total_kredit_beban_lain_lain_all = $total_kredit_beban_lain_lain_jurnal;
			//beban_lain_lain

	        ?>

			<tr class="table-active">
	            <th width="40%" class="text-center">Nama Akun</th>
				<th width="20%" class="text-center">Debit</th>
				<th width="20%" class="text-center">Kredit</th>
				<th width="20%" class="text-center">Saldo</th>
	        </tr>
		</table>

		<!-- kas -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="40%" class="text-left"><button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#kas" aria-expanded="false" aria-controls="kas">(1-10001) Kas</button></th>
				<th width="20%" class="text-center"><?php echo number_format($total_debit_kas_all,0,',','.');?></th>
				<th width="20%" class="text-center"><?php echo number_format($total_kredit_kas_all,0,',','.');?></th>
				<th width="20%" class="text-center"></th>
	        </tr>
		</table>
		<table class="collapse table table-bordered" id="kas" width="100%">
			<tr class="table-active">
				<th class="text-center">Tanggal</th>
				<th class="text-center">Transaksi</th>
				<th class="text-center">Nomor</th>
				<th class="text-center">Keterangan</th>
	        </tr>
			<?php foreach ($kas_biaya as $key => $x) {
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center">TRANSAKSI BIAYA</th>
				<th class="text-left"><a target="_blank" href="<a target="_blank" href="<?= base_url("pmm/biaya/detail_biaya/".$x['biaya_id']) ?>"><?= $x['nomor_transaksi'] ?></th>
				<th class="text-left"><?= $x['deskripsi'] ?></th>
	        </tr>
			<?php
			}
			?>

			<?php foreach ($kas_jurnal as $key => $x) {
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center">JURNAL UMUM</th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/jurnal_umum/detailJurnal/".$x['jurnal_id']) ?>"><?= $x['nomor_transaksi'] ?></th>
				<th class="text-left"><?= $x['deskripsi'] ?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- kas -->

		<!-- bank_kantor_pusat -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="40%" class="text-left"><button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#bank_kantor_pusat" aria-expanded="false" aria-controls="bank_kantor_pusat">(1-10002) Bank Kantor Pusat</button></th>
				<th width="20%" class="text-center"><?php echo number_format($total_debit_bank_kantor_pusat_all,0,',','.');?></th>
				<th width="20%" class="text-center"><?php echo number_format($total_kredit_bank_kantor_pusat_all,0,',','.');?></th>
				<th width="20%" class="text-center"></th>
	        </tr>
		</table>	
		<table class="collapse table table-bordered" id="bank_kantor_pusat" width="100%">
			<tr class="table-active">
				<th class="text-center">Tanggal</th>
				<th class="text-center">Transaksi</th>
				<th class="text-center">Nomor</th>
				<th class="text-center">Keterangan</th>
	        </tr>
			<?php foreach ($bank_kantor_pusat_biaya as $key => $x) {
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center">TRANSAKSI BIAYA</th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_biaya/".$x['biaya_id']) ?>"><?= $x['nomor_transaksi'] ?></a></th>
				<th class="text-left"><?= $x['deskripsi'] ?></th>
	        </tr>
			<?php
			}
			?>

			<?php foreach ($bank_kantor_pusat_jurnal as $key => $x) {
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center">JURNAL UMUM</th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/jurnal_umum/detailJurnal/".$x['jurnal_id']) ?>"><?= $x['nomor_transaksi'] ?></a></th>
				<th class="text-left"><?= $x['deskripsi'] ?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- bank_kantor_pusat -->

		<!-- hutang_lain_lain -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="40%" class="text-left"><button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#hutang_lain_lain" aria-expanded="false" aria-controls="hutang_lain_lain">(2-20200) Hutang Lain Lain</button></th>
				<th width="20%" class="text-center"><?php echo number_format($total_debit_hutang_lain_lain_all,0,',','.');?></th>
				<th width="20%" class="text-center"><?php echo number_format($total_kredit_hutang_lain_lain_all,0,',','.');?></th>
				<th width="20%" class="text-center"></th>
	        </tr>
		</table>	
		<table class="collapse table table-bordered" id="hutang_lain_lain" width="100%">
			<tr class="table-active">
				<th class="text-center">Tanggal</th>
				<th class="text-center">Transaksi</th>
				<th class="text-center">Nomor</th>
				<th class="text-center">Keterangan</th>
	        </tr>
			<?php foreach ($hutang_lain_lain_biaya as $key => $x) {
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center">TRANSAKSI BIAYA</th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_biaya/".$x['biaya_id']) ?>"><?= $x['nomor_transaksi'] ?></a></th>
				<th class="text-left"><?= $x['deskripsi'] ?></th>
	        </tr>
			<?php
			}
			?>

			<?php foreach ($hutang_lain_lain_jurnal as $key => $x) {
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center">JURNAL UMUM</th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/jurnal_umum/detailJurnal/".$x['jurnal_id']) ?>"><?= $x['nomor_transaksi'] ?></a></th>
				<th class="text-left"><?= $x['deskripsi'] ?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- hutang_lain_lain -->

		<!-- perjalanan_dinas_penjualan -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="40%" class="text-left"><button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#perjalanan_dinas_penjualan" aria-expanded="false" aria-controls="perjalanan_dinas_penjualan">(5-50509) Perjalanan Dinas - Penjualan</button></th>
				<th width="20%" class="text-center"><?php echo number_format($total_debit_perjalanan_dinas_penjualan_all,0,',','.');?></th>
				<th width="20%" class="text-center"><?php echo number_format($total_kredit_perjalanan_dinas_penjualan_all,0,',','.');?></th>
				<th width="20%" class="text-center"></th>
	        </tr>
		</table>	
		<table class="collapse table table-bordered" id="perjalanan_dinas_penjualan" width="100%">
			<tr class="table-active">
				<th class="text-center">Tanggal</th>
				<th class="text-center">Transaksi</th>
				<th class="text-center">Nomor</th>
				<th class="text-center">Keterangan</th>
	        </tr>
			<?php foreach ($perjalanan_dinas_penjualan_biaya as $key => $x) {
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center">TRANSAKSI BIAYA</th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_biaya/".$x['biaya_id']) ?>"><?= $x['nomor_transaksi'] ?></a></th>
				<th class="text-left"><?= $x['deskripsi'] ?></th>
	        </tr>
			<?php
			}
			?>

			<?php foreach ($perjalanan_dinas_penjualan_jurnal as $key => $x) {
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center">JURNAL UMUM</th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/jurnal_umum/detailJurnal/".$x['jurnal_id']) ?>"><?= $x['nomor_transaksi'] ?></a></th>
				<th class="text-left"><?= $x['deskripsi'] ?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- perjalanan_dinas_penjualan -->

		<!-- pengobatan -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="40%" class="text-left"><button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#pengobatan" aria-expanded="false" aria-controls="pengobatan">(5-50505) Pengobatan</button></th>
				<th width="20%" class="text-center"><?php echo number_format($total_debit_pengobatan_all,0,',','.');?></th>
				<th width="20%" class="text-center"><?php echo number_format($total_kredit_pengobatan_all,0,',','.');?></th>
				<th width="20%" class="text-center"></th>
	        </tr>
		</table>	
		<table class="collapse table table-bordered" id="pengobatan" width="100%">
			<tr class="table-active">
				<th class="text-center">Tanggal</th>
				<th class="text-center">Transaksi</th>
				<th class="text-center">Nomor</th>
				<th class="text-center">Keterangan</th>
	        </tr>
			<?php foreach ($pengobatan_biaya as $key => $x) {
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center">TRANSAKSI BIAYA</th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_biaya/".$x['biaya_id']) ?>"><?= $x['nomor_transaksi'] ?></a></th>
				<th class="text-left"><?= $x['deskripsi'] ?></th>
	        </tr>
			<?php
			}
			?>

			<?php foreach ($pengobatan_jurnal as $key => $x) {
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center">JURNAL UMUM</th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/jurnal_umum/detailJurnal/".$x['jurnal_id']) ?>"><?= $x['nomor_transaksi'] ?></a></th>
				<th class="text-left"><?= $x['deskripsi'] ?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- pengobatan -->

		<!-- bensin_tol_parkir -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="40%" class="text-left"><button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#bensin_tol_parkir" aria-expanded="false" aria-controls="bensin_tol_parkir">(5-50508) Bensin Tol dan Parkir - Umum</button></th>
				<th width="20%" class="text-center"><?php echo number_format($total_debit_bensin_tol_parkir_all,0,',','.');?></th>
				<th width="20%" class="text-center"><?php echo number_format($total_kredit_bensin_tol_parkir_all,0,',','.');?></th>
				<th width="20%" class="text-center"></th>
	        </tr>
		</table>	
		<table class="collapse table table-bordered" id="bensin_tol_parkir" width="100%">
			<tr class="table-active">
				<th class="text-center">Tanggal</th>
				<th class="text-center">Transaksi</th>
				<th class="text-center">Nomor</th>
				<th class="text-center">Keterangan</th>
	        </tr>
			<?php foreach ($bensin_tol_parkir_biaya as $key => $x) {
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center">TRANSAKSI BIAYA</th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_biaya/".$x['biaya_id']) ?>"><?= $x['nomor_transaksi'] ?></a></th>
				<th class="text-left"><?= $x['deskripsi'] ?></th>
	        </tr>
			<?php
			}
			?>

			<?php foreach ($bensin_tol_parkir_jurnal as $key => $x) {
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center">JURNAL UMUM</th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/jurnal_umum/detailJurnal/".$x['jurnal_id']) ?>"><?= $x['nomor_transaksi'] ?></a></th>
				<th class="text-left"><?= $x['deskripsi'] ?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- bensin_tol_parkir -->

		<!-- pakaian_dinas -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="40%" class="text-left"><button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#pakaian_dinas" aria-expanded="false" aria-controls="pakaian_dinas">(5-50510) Pakaian Dinas & K3</button></th>
				<th width="20%" class="text-center"><?php echo number_format($total_debit_pakaian_dinas_all,0,',','.');?></th>
				<th width="20%" class="text-center"><?php echo number_format($total_kredit_pakaian_dinas_all,0,',','.');?></th>
				<th width="20%" class="text-center"></th>
	        </tr>
		</table>	
		<table class="collapse table table-bordered" id="pakaian_dinas" width="100%">
			<tr class="table-active">
				<th class="text-center">Tanggal</th>
				<th class="text-center">Transaksi</th>
				<th class="text-center">Nomor</th>
				<th class="text-center">Keterangan</th>
	        </tr>
			<?php foreach ($pakaian_dinas_biaya as $key => $x) {
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center">TRANSAKSI BIAYA</th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_biaya/".$x['biaya_id']) ?>"><?= $x['nomor_transaksi'] ?></a></th>
				<th class="text-left"><?= $x['deskripsi'] ?></th>
	        </tr>
			<?php
			}
			?>

			<?php foreach ($pakaian_dinas_jurnal as $key => $x) {
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center">JURNAL UMUM</th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/jurnal_umum/detailJurnal/".$x['jurnal_id']) ?>"><?= $x['nomor_transaksi'] ?></a></th>
				<th class="text-left"><?= $x['deskripsi'] ?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- pakaian_dinas -->

		<!-- biaya_adm_bank -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="40%" class="text-left"><button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#biaya_adm_bank" aria-expanded="false" aria-controls="biaya_adm_bank">(5-50518) Beban Adm Bank</button></th>
				<th width="20%" class="text-center"><?php echo number_format($total_debit_biaya_adm_bank_all,0,',','.');?></th>
				<th width="20%" class="text-center"><?php echo number_format($total_kredit_biaya_adm_bank_all,0,',','.');?></th>
				<th width="20%" class="text-center"></th>
	        </tr>
		</table>	
		<table class="collapse table table-bordered" id="biaya_adm_bank" width="100%">
			<tr class="table-active">
				<th class="text-center">Tanggal</th>
				<th class="text-center">Transaksi</th>
				<th class="text-center">Nomor</th>
				<th class="text-center">Keterangan</th>
	        </tr>
			<?php foreach ($biaya_adm_bank_biaya as $key => $x) {
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center">TRANSAKSI BIAYA</th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_biaya/".$x['biaya_id']) ?>"><?= $x['nomor_transaksi'] ?></a></th>
				<th class="text-left"><?= $x['deskripsi'] ?></th>
	        </tr>
			<?php
			}
			?>

			<?php foreach ($biaya_adm_bank_jurnal as $key => $x) {
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center">JURNAL UMUM</th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/jurnal_umum/detailJurnal/".$x['jurnal_id']) ?>"><?= $x['nomor_transaksi'] ?></a></th>
				<th class="text-left"><?= $x['deskripsi'] ?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- biaya_adm_bank -->

		<!-- beban_kirim -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="40%" class="text-left"><button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#beban_kirim" aria-expanded="false" aria-controls="beban_kirim">(5-50511) Beban Kirim </button></th>
				<th width="20%" class="text-center"><?php echo number_format($total_debit_beban_kirim_all,0,',','.');?></th>
				<th width="20%" class="text-center"><?php echo number_format($total_kredit_beban_kirim_all,0,',','.');?></th>
				<th width="20%" class="text-center"></th>
	        </tr>
		</table>	
		<table class="collapse table table-bordered" id="beban_kirim" width="100%">
			<tr class="table-active">
				<th class="text-center">Tanggal</th>
				<th class="text-center">Transaksi</th>
				<th class="text-center">Nomor</th>
				<th class="text-center">Keterangan</th>
	        </tr>
			<?php foreach ($beban_kirim_biaya as $key => $x) {
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center">TRANSAKSI BIAYA</th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_biaya/".$x['biaya_id']) ?>"><?= $x['nomor_transaksi'] ?></a></th>
				<th class="text-left"><?= $x['deskripsi'] ?></th>
	        </tr>
			<?php
			}
			?>

			<?php foreach ($beban_kirim_jurnal as $key => $x) {
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center">JURNAL UMUM</th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/jurnal_umum/detailJurnal/".$x['jurnal_id']) ?>"><?= $x['nomor_transaksi'] ?></a></th>
				<th class="text-left"><?= $x['deskripsi'] ?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- beban_kirim -->

		<!-- beban_lain_lain -->
		<table class="table table-bordered" width="100%">
			<tr class="table-active">
	            <th width="40%" class="text-left"><button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#beban_lain_lain" aria-expanded="false" aria-controls="beban_lain_lain">(5-50517) Beban Lain-Lain </button></th>
				<th width="20%" class="text-center"><?php echo number_format($total_debit_beban_lain_lain_all,0,',','.');?></th>
				<th width="20%" class="text-center"><?php echo number_format($total_kredit_beban_lain_lain_all,0,',','.');?></th>
				<th width="20%" class="text-center"></th>
	        </tr>
		</table>	
		<table class="collapse table table-bordered" id="beban_lain_lain" width="100%">
			<tr class="table-active">
				<th class="text-center">Tanggal</th>
				<th class="text-center">Transaksi</th>
				<th class="text-center">Nomor</th>
				<th class="text-center">Keterangan</th>
	        </tr>
			<?php foreach ($beban_lain_lain_biaya as $key => $x) {
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center">TRANSAKSI BIAYA</th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/biaya/detail_biaya/".$x['biaya_id']) ?>"><?= $x['nomor_transaksi'] ?></a></th>
				<th class="text-left"><?= $x['deskripsi'] ?></th>
	        </tr>
			<?php
			}
			?>

			<?php foreach ($beban_lain_lain_jurnal as $key => $x) {
			?>
			<tr class="table-active3">
				<th class="text-center"><?= date('d-m-Y',strtotime($x['tanggal_transaksi'])); ?></th>
				<th class="text-center">JURNAL UMUM</th>
				<th class="text-left"><a target="_blank" href="<?= base_url("pmm/jurnal_umum/detailJurnal/".$x['jurnal_id']) ?>"><?= $x['nomor_transaksi'] ?></a></th>
				<th class="text-left"><?= $x['deskripsi'] ?></th>
	        </tr>
			<?php
			}
			?>
	    </table>
		<!-- beban_lain_lain -->
		

		<?php
	}
}