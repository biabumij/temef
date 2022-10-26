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

        
        $pdf->SetTitle('Satuan');
        $pdf->nsi_html($html);
        $pdf->Output('satuan.pdf', 'I');
	
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

	//BATAS RUMUS LAMA//

	public function dashboard_evaluasi_bahan($arr_date)
	{
		$data = array();
		
		$arr_date = $this->input->post('filter_date');
		$arr_filter_date = explode(' - ', $arr_date);

		$last_production = $this->db->select('date')->order_by('date','desc')->limit(1)->get_where('pmm_remaining_materials_cat',array('status'=>'PUBLISH'))->row_array();
		$last_production_2 = $this->db->select('date')->order_by('date','desc')->limit(1,1)->get_where('pmm_remaining_materials_cat',array('status'=>'PUBLISH','material_id'=>'4'))->row_array();
		
		$date1 = date('Y-m-d', strtotime('+1 days', strtotime($last_production_2['date'])));
		$date2 =  date('Y-m-d', strtotime($last_production['date']));
		$date1_filter = date('d F Y', strtotime($date1));
		$date2_filter = date('d F Y', strtotime($date2));
		

		if(count($arr_filter_date) == 2){
			$date1 	= date('Y-m-d',strtotime($arr_filter_date[0]));
			$date2 	= date('Y-m-d',strtotime($arr_filter_date[1]));
			$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]));
			$date1_filter = date('d F Y',strtotime($arr_filter_date[0]));
			$date2_filter = date('d F Y',strtotime($arr_filter_date[1]));
		}
		
		?>
		
		<table class="table table-bordered" width="100%">
			<style type="text/css">
				table tr.table-active{
				background-color: #ffb732;
				color: black;
			}
			</style>

			<!-- TOTAL PEMAKAIAN KOMPOSISI -->

			<?php

			$komposisi = $this->db->select('pp.date_production, (pp.display_volume) * pk.presentase_a as volume_a, (pp.display_volume) * pk.presentase_b as volume_b, (pp.display_volume) * pk.presentase_c as volume_c, (pp.display_volume) * pk.presentase_d as volume_d, (pp.display_volume * pk.presentase_a) * pk.price_a as nilai_a, (pp.display_volume * pk.presentase_b) * pk.price_b as nilai_b, (pp.display_volume * pk.presentase_c) * pk.price_c as nilai_c, (pp.display_volume * pk.presentase_d) * pk.price_d as nilai_d')
			->from('pmm_productions pp')
			->join('pmm_agregat pk', 'pp.komposisi_id = pk.id','left')
			->where("pp.date_production between '$date1' and '$date2'")
			->get()->result_array();

			$total_volume_a = 0;
			$total_volume_b = 0;
			$total_volume_c = 0;
			$total_volume_d = 0;

			$total_nilai_a = 0;
			$total_nilai_b = 0;
			$total_nilai_c = 0;
			$total_nilai_d = 0;

			foreach ($komposisi as $x){
				$total_volume_a += $x['volume_a'];
				$total_volume_b += $x['volume_b'];
				$total_volume_c += $x['volume_c'];
				$total_volume_d += $x['volume_d'];
				$total_nilai_a += $x['nilai_a'];
				$total_nilai_b += $x['nilai_b'];
				$total_nilai_c += $x['nilai_c'];
				$total_nilai_d += $x['nilai_d'];
				
			}

			$volume_a = $total_volume_a;
			$volume_b = $total_volume_b;
			$volume_c = $total_volume_c;
			$volume_d = $total_volume_d;

			$nilai_a = $total_nilai_a;
			$nilai_b = $total_nilai_b;
			$nilai_c = $total_nilai_c;
			$nilai_d = $total_nilai_d;

			$price_a = ($total_volume_a!=0)?$total_nilai_a / $total_volume_a * 1:0;
			$price_b = ($total_volume_b!=0)?$total_nilai_a / $total_volume_b * 1:0;
			$price_c = ($total_volume_c!=0)?$total_nilai_a / $total_volume_c * 1:0;
			$price_d = ($total_volume_d!=0)?$total_nilai_a / $total_volume_d * 1:0;

			$total_volume_komposisi = $volume_a + $volume_b + $volume_c + $volume_d;
			$total_nilai_komposisi = $nilai_a + $nilai_b + $nilai_c + $nilai_d;
			
			?>

			<!-- END TOTAL PEMAKAIAN KOMPOSISI -->

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
			$total_harga_pembelian_semen_akhir = ($total_volume_pembelian_semen_akhir!=0)?($nilai_opening_balance_semen + $total_nilai_pembelian_semen) / $total_volume_pembelian_semen_akhir * 1:0;
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
			$total_harga_pembelian_semen_cons_akhir = ($total_volume_pembelian_semen_cons_akhir!=0)?($total_nilai_pembelian_semen_akhir + $total_nilai_pembelian_semen_cons) / $total_volume_pembelian_semen_cons_akhir * 1:0;
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
			$total_harga_pembelian_semen_opc_akhir = ($total_volume_pembelian_semen_opc_akhir!=0)?($total_nilai_pembelian_semen_cons_akhir + $total_nilai_pembelian_semen_opc) / $total_volume_pembelian_semen_opc_akhir * 1:0;
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
			$total_harga_pemakaian_semen = ($total_volume_pemakaian_semen!=0)?$total_nilai_pemakaian_semen / $total_volume_pemakaian_semen * 1:0;

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
			$total_harga_pembelian_pasir_akhir = ($total_volume_pembelian_pasir_akhir!=0)?($nilai_opening_balance_pasir + $total_nilai_pembelian_pasir) / $total_volume_pembelian_pasir_akhir * 1:0;
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
	
			$total_nilai_pembelian = $total_nilai_pembelian_semen_all + $total_nilai_pembelian_pasir + $total_nilai_pembelian_batu1020 + $total_nilai_pembelian_batu2030;
			$total_nilai_pemakaian = $total_nilai_pemakaian_semen + $total_nilai_pemakaian_pasir + $total_nilai_pemakaian_batu1020 + $total_nilai_pemakaian_batu2030;
			$total_nilai_akhir = $total_nilai_stock_semen_akhir + $total_nilai_stock_pasir_akhir + $total_nilai_stock_batu1020_akhir + $total_nilai_stock_batu2030_akhir;

			$total_volume_realisasi = $total_volume_pemakaian_semen + $total_volume_pemakaian_pasir + $total_volume_pemakaian_batu1020 + $total_volume_pemakaian_batu2030;
			$total_nilai_realisasi = $total_nilai_pemakaian_semen + $total_nilai_pemakaian_pasir + $total_nilai_pemakaian_batu1020 + $total_nilai_pemakaian_batu2030;

			$evaluasi_volume_a = round($volume_a - $total_volume_pemakaian_semen,2);
			$evaluasi_volume_b = round($volume_b - $total_volume_pemakaian_pasir,2);
			$evaluasi_volume_c = round($volume_c - $total_volume_pemakaian_batu1020,2);
			$evaluasi_volume_d = round($volume_d - $total_volume_pemakaian_batu2030,2);

			$evaluasi_nilai_a = round($nilai_a - $total_nilai_pemakaian_semen,0);
			$evaluasi_nilai_b = round($nilai_b - $total_nilai_pemakaian_pasir,0);
			$evaluasi_nilai_c = round($nilai_c - $total_nilai_pemakaian_batu1020,0);
			$evaluasi_nilai_d = round($nilai_d - $total_nilai_pemakaian_batu2030,0);

			$total_nilai_evaluasi = round($evaluasi_nilai_a + $evaluasi_nilai_b + $evaluasi_nilai_c + $evaluasi_nilai_d,0);

	        ?>

			<tr class="table-active">
				<th class="text-center" colspan="5" style="text-transform:uppercase">
				<marquee>Evaluasi Pemakaian Bahan Baku
				(<?php
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
				
				$subject = "$date1_filter - $date2_filter";

				echo str_replace($search, $replace, $subject);

				?>)</marquee>
				</th>
			</tr>
			<tr>
				<th width="5%" class="text-center" style='background-color:rgb(0,206,209); color:black'>NO.</th>
				<th class="text-center" style='background-color:rgb(0,206,209); color:black'>URAIAN</th>
				<th class="text-center" style='background-color:rgb(0,206,209); color:black'>SATUAN</th>
				<th class="text-center" style='background-color:rgb(0,206,209); color:black'>VOLUME</th>
				<th class="text-center" style='background-color:rgb(0,206,209); color:black'>NILAI</th>
	        </tr>
			<?php
				$styleColorA = $evaluasi_volume_a < 0 ? 'background-color:red' : 'background-color:none';
				$styleColorB = $evaluasi_volume_b < 0 ? 'background-color:red' : 'background-color:none';
				$styleColorC = $evaluasi_volume_c < 0 ? 'background-color:red' : 'background-color:none';
				$styleColorD = $evaluasi_volume_d < 0 ? 'background-color:red' : 'background-color:none';

				$styleColorAA = $evaluasi_nilai_a < 0 ? 'background-color:red' : 'background-color:none';
				$styleColorBB = $evaluasi_nilai_b < 0 ? 'background-color:red' : 'background-color:none';
				$styleColorCC = $evaluasi_nilai_c < 0 ? 'background-color:red' : 'background-color:none';
				$styleColorDD = $evaluasi_nilai_d < 0 ? 'background-color:red' : 'background-color:none';
				$styleColorEE = $total_nilai_evaluasi < 0 ? 'background-color:red' : 'background-color:none';
			?>
			<tr>
				<th class="text-center" style="vertical-align:middle">1</th>			
				<th class="text-left">Semen</th>
				<th class="text-center">Ton</th>
				<th class="text-right" style="<?php echo $styleColorA ?>"><?php echo number_format($evaluasi_volume_a,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorAA ?>"><?php echo number_format($evaluasi_nilai_a,0,',','.');?></th>
	        </tr>
			<tr>
				<th class="text-center" style="vertical-align:middle">2</th>			
				<th class="text-left">Pasir</th>
				<th class="text-center">M3</th>
				<th class="text-right" style="<?php echo $styleColorB ?>"><?php echo number_format($evaluasi_volume_b,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorBB ?>"><?php echo number_format($evaluasi_nilai_b,0,',','.');?></th>
	        </tr>
			<tr>
				<th class="text-center" style="vertical-align:middle">3</th>			
				<th class="text-left">Batu Split 10-20</th>
				<th class="text-center">M3</th>
				<th class="text-right" style="<?php echo $styleColorC ?>"><?php echo number_format($evaluasi_volume_c,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorCC ?>"><?php echo number_format($evaluasi_nilai_c,0,',','.');?></th>
	        </tr>
			<tr>
				<th class="text-center" style="vertical-align:middle">4</th>			
				<th class="text-left">Batu Split 20-30</th>
				<th class="text-center">M3</th>
				<th class="text-right" style="<?php echo $styleColorD ?>"><?php echo number_format($evaluasi_volume_d,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorDD ?>"><?php echo number_format($evaluasi_nilai_d,0,',','.');?></th>
	        </tr>
			<tr>		
				<th class="text-right" colspan="4">TOTAL</th>
				<th class="text-right" style="<?php echo $styleColorEE ?>"><?php echo number_format($total_nilai_evaluasi,0,',','.');?></th>
	        </tr>
	    </table>
		<?php
	}

	public function laba_rugi($arr_date)
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
				<th class="text-center">SD. <?php echo $filter_date_2 = date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
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
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("p.kategori_produk = '5'")
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
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt between '$date3' and '$date2'")
			->where("p.kategori_produk = '5'")
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
			->where("c.id <> 228 ")
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
			->where("c.id <> 228 ")
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
			->where("c.id <> 228 ")
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
			->where("c.id <> 228 ")
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
			->where("c.id <> 228 ")
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
			->where("c.id <> 228 ")
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
			->where("c.id <> 228 ")
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
			->where("c.id <> 228 ")
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
			->where("c.id <> 228 ")
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
			->where("c.id <> 228 ")
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
			->where("c.id <> 228 ")
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
			->where("c.id <> 228 ")
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

			//PERSIAPAN
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
			//END_PERSIAPAN

			//PERSIAPAN_2
			$persiapan_biaya_2 = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 228")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date3' and '$date2')")
			->get()->row_array();

			$persiapan_jurnal_2 = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 228")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date3' and '$date2')")
			->get()->row_array();

			$persiapan_2 = $persiapan_biaya_2['total'] + $persiapan_jurnal_2['total'];
			//END_PERSIAPAN_2

			$bahan = $total_nilai;
			$alat = $alat;
			$overhead = $overhead;
			$diskonto = $diskonto;
			$persiapan = $persiapan;

			$total_biaya_operasional = $bahan + $alat + $overhead + $diskonto + $persiapan;

			$laba_kotor = $total_penjualan_all - $total_biaya_operasional;

			$laba_sebelum_pajak = $laba_kotor;

			$persentase_laba_sebelum_pajak = ($total_penjualan_all!=0)?($laba_sebelum_pajak / $total_penjualan_all)  * 100:0;

			$bahan_2 = $total_nilai_2;
			$alat_2 = $alat_2;
			$overhead_2 = $overhead_2;
			$diskonto_2 = $diskonto_2;
			$persiapan_2 = $persiapan_2;

			$total_biaya_operasional_2 = $bahan_2 + $alat_2 + $overhead_2 + $diskonto_2 + $persiapan_2;

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
	            <th class="text-center"></th>
				<th class="text-left" colspan="3">Persiapan</th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><a target="_blank" href="<?= base_url("laporan/cetak_persiapan?filter_date=".$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($persiapan,0,',','.');?></a></span>
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
									<span><a target="_blank" href="<?= base_url("laporan/cetak_persiapan?filter_date=".$filter_date_2 = date('d F Y',strtotime($date3)).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($persiapan_2,0,',','.');?></a></span>
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
									<span><?php echo number_format($persentase_laba_sebelum_pajak,2,',','.');?> %</span>
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
									<span><?php echo number_format($persentase_laba_sebelum_pajak_2,2,',','.');?> %</span>
								</th>
							</tr>
					</table>
				</th>
	        </tr>
	    </table>
		<?php
	}

	function buku_besar()
	{
		$data = array();
		$filter_client_id = $this->input->post('filter_client_id');
		$purchase_order_no = $this->input->post('salesPo_id');
		$filter_product = $this->input->post('filter_product');
		$start_date = false;
		$end_date = false;
		$total = 0;
		$jumlah_no_transaksi = 0;
		$jumlah_deskripsi = 0;
		$jumlah_debit = 0;
		$jumlah_kredit = 0;
		$jumlah_saldo = 0;
		$date = $this->input->post('filter_date');
		if(!empty($date)){
			$arr_date = explode(' - ',$date);
			$start_date = date('Y-m-d',strtotime($arr_date[0]));
			$end_date = date('Y-m-d',strtotime($arr_date[1]));
		}

		$this->db->select('c.coa, SUM(t.debit) as jumlah_debit_all, SUM(t.kredit) as jumlah_kredit_all, SUM(t.debit - t.kredit) as jumlah_saldo_all');
        $this->db->join('pmm_coa c','t.akun = c.id','left');
		if(!empty($start_date) && !empty($end_date)){
			$this->db->where('t.tanggal_transaksi >=',$start_date);
            $this->db->where('t.tanggal_transaksi <=',$end_date);
        }
        if(!empty($filter_client_id)){
            $this->db->where('c.coa',$filter_client_id);
        }
        if(!empty($filter_product)){
            $this->db->where_in('pp.product_id',$filter_product);
        }
        if(!empty($purchase_order_no)){
            $this->db->where('pp.salesPo_id',$purchase_order_no);
        }
		
        $this->db->order_by('c.id','asc');
        $this->db->group_by('c.id');
		$query = $this->db->get('transactions t');
		
		$no = 1;
		if($query->num_rows() > 0){

			foreach ($query->result_array() as $key => $sups) {

				$mats = array();
				$materials = $this->pmm_model->GetReceiptMatBukuBesar($sups['coa'],$purchase_order_no,$start_date,$end_date,$filter_product);
				if(!empty($materials)){
					foreach ($materials as $key => $row) {
				
						if ($row['no_trx_1']==0) { $jumlah_no_transaksi = $row['no_trx_1'] .= $row['no_trx_2'] .= $row['no_trx_3'] .= $row['no_trx_4'];} else
						{$jumlah_no_transaksi = $row['no_trx_1'] .= $row['no_trx_2'] .= $row['no_trx_3'] .= $row['no_trx_4'];}

						if ($row['dex_1']==0) { $jumlah_deskripsi = $row['dex_1'] .= $row['dex_2'] .= $row['dex_3'] .= $row['dex_4'];} else
						{$jumlah_deskripsi = $row['dex_1'] .= $row['dex_2'] .= $row['dex_3'] .= $row['dex_4'];}

						if ($row['debit']==0) { $jumlah_debit = $row['debit'];} else
						{$jumlah_debit = $row['debit'];}

						if ($row['kredit']==0) { $jumlah_kredit = $row['kredit'];} else
						{$jumlah_kredit = $row['kredit'];}
						
						if ($jumlah_debit==0) { $jumlah_saldo = $jumlah_saldo + $jumlah_debit - $jumlah_kredit;} else
						{$jumlah_saldo = $jumlah_saldo + $jumlah_debit;}

						$arr['no'] = $key + 1;
						$arr['tanggal'] = $row['tanggal_transaksi'];
						$arr['transaksi'] = $row['transaksi'];
						$arr['nomor_transaksi'] = '<a href="'.base_url().'pmm/biaya/detail_transaction/'.$row['transaction_id'].'" target="_blank">'.$jumlah_no_transaksi.'</a>';
						$arr['deskripsi'] = $jumlah_deskripsi;
						$arr['debit'] = number_format($jumlah_debit,0,',','.');
						$arr['kredit'] = number_format($jumlah_kredit,0,',','.');
						$arr['saldo'] = number_format($jumlah_saldo,0,',','.');
						
						
						$arr['coa'] = $sups['coa'];
						$mats[] = $arr;
					}
					$sups['mats'] = $mats;
					$sups['jumlah_debit_all'] = number_format($sups['jumlah_debit_all'],0,',','.');
					$sups['jumlah_kredit_all'] = number_format($sups['jumlah_kredit_all'],0,',','.');
					$sups['jumlah_saldo_all'] = number_format($sups['jumlah_saldo_all'],0,',','.');
					$total = 0;
					$sups['no'] = $no;
					
					$data[] = $sups;
					$no++;
				}	
				
			}
		}

		echo json_encode(array('data'=>$data,'total_volume'=>number_format($total,0,',','.')));		
	}

	public function cash_flow($arr_date)
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
				table tr.table-active-csf{
					background-color: #F0F0F0;
					font-size: 8px;
					font-weight: bold;
					color: black;
				}
					
				table tr.table-active2-csf{
					background-color: #E8E8E8;
					font-size: 8px;
					font-weight: bold;
				}
					
				table tr.table-active3-csf{
					font-size: 8px;
					background-color: #F0F0F0;
				}
					
				table tr.table-active4-csf{
					background-color: #e69500;
					font-weight: bold;
					font-size: 8px;
					color: black;
				}
				table tr.table-active5-csf{
					background-color: #E8E8E8;
					text-decoration: underline;
					font-size: 8px;
					font-weight: bold;
					color: red;
				}
				table tr.table-activeago1-csf{
					background-color: #ffd966;
					font-weight: bold;
					font-size: 8px;
					color: black;
				}
				table tr.table-activeopening-csf{
					background-color: #2986cc;
					font-weight: bold;
					font-size: 8px;
					color: black;
				}
			</style>

			<!-- RAP 2022 -->
			<?php
			$date_now = date('Y-m-d');
			$date_end = date('2022-12-31');
			$rencana_kerja_2022 = $this->db->select('r.*')
			->from('rak r')
			->order_by('r.tanggal_rencana_kerja','asc')->limit(1)
			->get()->row_array();

			$volume_rap_2022_produk_a = $rencana_kerja_2022['vol_produk_a'];
			$volume_rap_2022_produk_b = $rencana_kerja_2022['vol_produk_b'];
			$volume_rap_2022_produk_c = $rencana_kerja_2022['vol_produk_c'];
			$volume_rap_2022_produk_d = $rencana_kerja_2022['vol_produk_d'];

			$total_rap_volume_2022 = $volume_rap_2022_produk_a + $volume_rap_2022_produk_b + $volume_rap_2022_produk_c + $volume_rap_2022_produk_d;
			
			$harga_jual_125_rap = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("pod.product_id = 2")
			->order_by('po.contract_date','asc')->limit(1)
			->get()->row_array();

			$harga_jual_225_rap = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("pod.product_id = 1")
			->order_by('po.contract_date','asc')->limit(1)
			->get()->row_array();

			$harga_jual_250_rap = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("pod.product_id = 3")
			->order_by('po.contract_date','asc')->limit(1)
			->get()->row_array();

			$harga_jual_250_18_rap = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("pod.product_id = 11")
			->order_by('po.contract_date','asc')->limit(1)
			->get()->row_array();

			$harga_jual_125_now = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("(po.contract_date < '$date_now')")
			->where("pod.product_id = 2")
			->order_by('po.contract_date','desc')->limit(1)
			->get()->row_array();

			$harga_jual_225_now = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("(po.contract_date < '$date_now')")
			->where("pod.product_id = 1")
			->order_by('po.contract_date','desc')->limit(1)
			->get()->row_array();

			$harga_jual_250_now = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("(po.contract_date < '$date_now')")
			->where("pod.product_id = 3")
			->order_by('po.contract_date','desc')->limit(1)
			->get()->row_array();

			$harga_jual_250_18_now = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("(po.contract_date < '$date_now')")
			->where("pod.product_id = 11")
			->order_by('po.contract_date','desc')->limit(1)
			->get()->row_array();

			$nilai_jual_125_2022 = $volume_rap_2022_produk_a * $harga_jual_125_rap['harga_satuan'];
			$nilai_jual_225_2022 = $volume_rap_2022_produk_b * $harga_jual_225_rap['harga_satuan'];
			$nilai_jual_250_2022 = $volume_rap_2022_produk_c * $harga_jual_250_rap['harga_satuan'];
			$nilai_jual_250_18_2022 = $volume_rap_2022_produk_d * $harga_jual_250_18_rap['harga_satuan'];
			$nilai_jual_all_2022 = $nilai_jual_125_2022 + $nilai_jual_225_2022 + $nilai_jual_250_2022 + $nilai_jual_250_18_2022;
			
			$total_rap_nilai_2022 = $nilai_jual_all_2022;

			//KOMPOSISI RAP 2022
			//K125
			$komposisi_125_rap = $this->db->select('pk.*')
			->from('pmm_agregat pk')
			->where("pk.mutu_beton = 2")
			->where('pk.status','PUBLISH')
			->order_by('pk.date_agregat','asc')->limit(1)
			->get()->row_array();

			$komposisi_125_produk_a_rap = $komposisi_125_rap['presentase_a'];
			$komposisi_125_produk_b_rap = $komposisi_125_rap['presentase_b'];
			$komposisi_125_produk_c_rap = $komposisi_125_rap['presentase_c'];
			$komposisi_125_produk_d_rap = $komposisi_125_rap['presentase_d'];

			//K225
			$komposisi_225_rap = $this->db->select('pk.*')
			->from('pmm_agregat pk')
			->where("pk.mutu_beton = 1")
			->where('pk.status','PUBLISH')
			->order_by('pk.date_agregat','asc')->limit(1)
			->get()->row_array();

			$komposisi_225_produk_a_rap = $komposisi_225_rap['presentase_a'];
			$komposisi_225_produk_b_rap = $komposisi_225_rap['presentase_b'];
			$komposisi_225_produk_c_rap = $komposisi_225_rap['presentase_c'];
			$komposisi_225_produk_d_rap = $komposisi_225_rap['presentase_d'];

			//K250
			$komposisi_250_rap = $this->db->select('pk.*')
			->from('pmm_agregat pk')
			->where("pk.mutu_beton = 3")
			->where('pk.status','PUBLISH')
			->order_by('pk.date_agregat','asc')->limit(1)
			->get()->row_array();

			$komposisi_250_produk_a_rap = $komposisi_250_rap['presentase_a'];
			$komposisi_250_produk_b_rap = $komposisi_250_rap['presentase_b'];
			$komposisi_250_produk_c_rap = $komposisi_250_rap['presentase_c'];
			$komposisi_250_produk_d_rap = $komposisi_250_rap['presentase_d'];

			//K250_18
			$komposisi_250_18_rap = $this->db->select('pk.*')
			->from('pmm_agregat pk')
			->where("pk.mutu_beton = 11")
			->where('pk.status','PUBLISH')
			->order_by('pk.date_agregat','asc')->limit(1)
			->get()->row_array();

			$komposisi_250_18_produk_a_rap = $komposisi_250_18_rap['presentase_a'];
			$komposisi_250_18_produk_b_rap = $komposisi_250_18_rap['presentase_b'];
			$komposisi_250_18_produk_c_rap = $komposisi_250_18_rap['presentase_c'];
			$komposisi_250_18_produk_d_rap = $komposisi_250_18_rap['presentase_d'];

			//KOMPOSISI
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

			//TOTAL PEMAKAIAN BAHAN RAP 2022
			//TOTAL K-125
			$total_semen_125_rap_2022 = $komposisi_125_produk_a_rap * $volume_rap_2022_produk_a;
			$total_pasir_125_rap_2022 = $komposisi_125_produk_b_rap * $volume_rap_2022_produk_a;
			$total_batu1020_125_rap_2022 = $komposisi_125_produk_c_rap * $volume_rap_2022_produk_a;
			$total_batu2030_125_rap_2022 = $komposisi_125_produk_d_rap * $volume_rap_2022_produk_a;

			$nilai_semen_125_rap_2022 = $total_semen_125_rap_2022 * $komposisi_125_rap['price_a'];
			$nilai_pasir_125_rap_2022 = $total_pasir_125_rap_2022 * $komposisi_125_rap['price_b'];
			$nilai_batu1020_125_rap_2022 = $total_batu1020_125_rap_2022 * $komposisi_125_rap['price_c'];
			$nilai_batu2030_125_rap_2022 = $total_batu2030_125_rap_2022 * $komposisi_125_rap['price_d'];

			$total_125_rap_2022 = $nilai_semen_125_rap_2022 + $nilai_pasir_125_rap_2022 + $nilai_batu1020_125_rap_2022 + $nilai_batu2030_125_rap_2022;

			//TOTAL K-225
			$total_semen_225_rap_2022 = $komposisi_225_produk_a_rap * $volume_rap_2022_produk_b;
			$total_pasir_225_rap_2022 = $komposisi_225_produk_b_rap * $volume_rap_2022_produk_b;
			$total_batu1020_225_rap_2022 = $komposisi_225_produk_c_rap * $volume_rap_2022_produk_b;
			$total_batu2030_225_rap_2022 = $komposisi_225_produk_d_rap * $volume_rap_2022_produk_b;

			$nilai_semen_225_rap_2022 = $total_semen_225_rap_2022 * $komposisi_225_rap['price_a'];
			$nilai_pasir_225_rap_2022 = $total_pasir_225_rap_2022 * $komposisi_225_rap['price_b'];
			$nilai_batu1020_225_rap_2022 = $total_batu1020_225_rap_2022 * $komposisi_225_rap['price_c'];
			$nilai_batu2030_225_rap_2022 = $total_batu2030_225_rap_2022 * $komposisi_225_rap['price_d'];

			$total_225_rap_2022 = $nilai_semen_225_rap_2022 + $nilai_pasir_225_rap_2022 + $nilai_batu1020_225_rap_2022 + $nilai_batu2030_225_rap_2022;

			//TOTAL K-250
			$total_semen_250_rap_2022 = $komposisi_250_produk_a_rap * $volume_rap_2022_produk_c;
			$total_pasir_250_rap_2022 = $komposisi_250_produk_b_rap * $volume_rap_2022_produk_c;
			$total_batu1020_250_rap_2022 = $komposisi_250_produk_c_rap * $volume_rap_2022_produk_c;
			$total_batu2030_250_rap_2022 = $komposisi_250_produk_d_rap * $volume_rap_2022_produk_c;

			$nilai_semen_250_rap_2022 = $total_semen_250_rap_2022 * $komposisi_250_rap['price_a'];
			$nilai_pasir_250_rap_2022 = $total_pasir_250_rap_2022 * $komposisi_250_rap['price_b'];
			$nilai_batu1020_250_rap_2022 = $total_batu1020_250_rap_2022 * $komposisi_250_rap['price_c'];
			$nilai_batu2030_250_rap_2022 = $total_batu2030_250_rap_2022 * $komposisi_250_rap['price_d'];

			$total_250_rap_2022 = $nilai_semen_250_rap_2022 + $nilai_pasir_250_rap_2022 + $nilai_batu1020_250_rap_2022 + $nilai_batu2030_250_rap_2022;

			//TOTAL K-250_18
			$total_semen_250_18_rap_2022 = $komposisi_250_18_produk_a_rap * $volume_rap_2022_produk_d;
			$total_pasir_250_18_rap_2022 = $komposisi_250_18_produk_b_rap * $volume_rap_2022_produk_d;
			$total_batu1020_250_18_rap_2022 = $komposisi_250_18_produk_c_rap * $volume_rap_2022_produk_d;
			$total_batu2030_250_18_rap_2022 = $komposisi_250_18_produk_d_rap * $volume_rap_2022_produk_d;

			$nilai_semen_250_18_rap_2022 = $total_semen_250_18_rap_2022 * $komposisi_250_18_rap['price_a'];
			$nilai_pasir_250_18_rap_2022 = $total_pasir_250_18_rap_2022 * $komposisi_250_18_rap['price_b'];
			$nilai_batu1020_250_18_rap_2022 = $total_batu1020_250_18_rap_2022 * $komposisi_250_18_rap['price_c'];
			$nilai_batu2030_250_18_rap_2022 = $total_batu2030_250_18_rap_2022 * $komposisi_250_18_rap['price_d'];

			$total_250_18_rap_2022 = $nilai_semen_250_18_rap_2022 + $nilai_pasir_250_18_rap_2022 + $nilai_batu1020_250_18_rap_2022 + $nilai_batu2030_250_18_rap_2022;

			//TOTAL ALL
			$total_bahan_all_rap_2022 = $total_125_rap_2022 + $total_225_rap_2022 + $total_250_rap_2022 + $total_250_18_rap_2022;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_rap_2022 = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_end')")
			->get()->row_array();

			$batching_plant_rap_2022 = $total_rap_volume_2022 * $rap_alat_rap_2022['batching_plant'];
			$truck_mixer_rap_2022 = $total_rap_volume_2022 * $rap_alat_rap_2022['truck_mixer'];
			$wheel_loader_rap_2022 = $total_rap_volume_2022 * $rap_alat_rap_2022['wheel_loader'];
			$bbm_solar_rap_2022 = $total_rap_volume_2022 * $rap_alat_rap_2022['bbm_solar'];
			$biaya_alat_all_rap_2022 = $batching_plant_rap_2022 + $truck_mixer_rap_2022 + $wheel_loader_rap_2022 + $bbm_solar_rap_2022;
		
			$total_rap_2022_biaya_bahan = $total_bahan_all_rap_2022;
			$total_rap_2022_biaya_alat = $biaya_alat_all_rap_2022;
			$total_rap_2022_biaya_overhead = $rencana_kerja_2022['biaya_overhead'];
			$total_rap_2022_biaya_bank = $rencana_kerja_2022['biaya_bank'];
			$total_rap_2022_biaya_persiapan = $rencana_kerja_2022['biaya_persiapan'];

			$total_biaya_rap_2022_biaya = $total_rap_2022_biaya_bahan + $total_rap_2022_biaya_alat + $total_rap_2022_biaya_overhead + $total_rap_2022_biaya_bank + $total_rap_2022_biaya_persiapan;

			?>
			<!-- RAP 2022 -->

			<!-- AKUMULASI NOW -->
			<?php

			$penjualan_now = $this->db->select('SUM(pp.display_price) as total')
			->from('pmm_productions pp')
			->join('penerima p', 'pp.client_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("pp.date_production < '$date_now'")
			->where("pp.status = 'PUBLISH'")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.client_id")
			->get()->row_array();

			//AKUMULASI BAHAN	
			$bahan_now = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar) as total_nilai_keluar')
			->from('akumulasi pp')
			->where("pp.date_akumulasi < '$date_now'")
			->get()->result_array();

			$total_bahan_now = 0;

			foreach ($bahan_now as $a){
				$total_bahan_now += $a['total_nilai_keluar'];
			}
			//AKUMULASI BAHAN

			//AKUMULASI ALAT
			$nilai_alat_now = $this->db->select('SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt < '$date_now'")
			->where("p.kategori_produk = '5'")
			->where("po.status in ('PUBLISH','CLOSED')")
			->get()->row_array();

			$akumulasi_bbm_now = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar_2) as total_nilai_keluar_2')
			->from('akumulasi pp')
			->where("pp.date_akumulasi < '$date_now'")
			->get()->result_array();

			$total_akumulasi_bbm_now = 0;

			foreach ($akumulasi_bbm_now as $b){
				$total_akumulasi_bbm_now += $b['total_nilai_keluar_2'];
			}

			$total_insentif_tm_now = 0;

			$insentif_tm_now = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->where("pdb.akun = 220")
			->where("status = 'PAID'")
			->where("tanggal_transaksi < '$date_now'")
			->get()->row_array();

			$total_insentif_tm_now = $insentif_tm_now['total'];

			$alat_now = $nilai_alat_now['nilai'] + $total_akumulasi_bbm_now + $total_insentif_tm_now;
			//AKUMULASI ALAT

			//TERMIN NOW
			$termin_now = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran < '$date_now'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();

			//OVERHEAD
			$overhead_15_now = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',15)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi < '$date_now'")
			->get()->row_array();

			$overhead_jurnal_15_now = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',15)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi < '$date_now'")
			->get()->row_array();

			$overhead_16_now = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',16)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi < '$date_now'")
			->get()->row_array();

			$overhead_jurnal_16_now = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',16)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi < '$date_now'")
			->get()->row_array();

			$overhead_17_now = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',17)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi < '$date_now'")
			->get()->row_array();

			$overhead_jurnal_17_now = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',17)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi < '$date_now'")
			->get()->row_array();

			$overhead_now =  $overhead_15_now['total'] + $overhead_jurnal_15_now['total'] + $overhead_16_now['total'] + $overhead_jurnal_16_now['total'] + $overhead_17_now['total'] + $overhead_jurnal_17_now['total'];
			//END OVERHEAD

			//DISKONTO
			$diskonto_now = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 168")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi < '$date_now'")
			->get()->row_array();

			$diskonto_now = $diskonto_now['total'];
			//END DISKONTO

			//PERSIAPAN
			$persiapan_biaya_now = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 228")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi < '$date_now'")
			->get()->row_array();

			$persiapan_jurnal_now = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 228")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi < '$date_now'")
			->get()->row_array();

			$persiapan_now = $persiapan_biaya_now['total'] + $persiapan_jurnal_now['total'];
			//END_PERSIAPAN

			//PPN KELUAR
			$ppn_keluar = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran_penagihan_pembelian pm')
			->where("pm.tanggal_pembayaran < '$date_now'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo = 'PPN'")
			->get()->row_array();

			//PPN MASUK
			$ppn_masuk = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran < '$date_now'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo = 'PPN'")
			->get()->row_array();
			?>
			<!-- AKUMULASI NOW -->

			<!-- NOVEMBER -->
			<?php
			$date_november_awal = date('2022-11-01');
			$date_november_akhir = date('2022-11-30');
			$rencana_kerja_november = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_november_awal' and '$date_november_akhir'")
			->get()->row_array();
			$volume_november_produk_a = $rencana_kerja_november['vol_produk_a'];
			$volume_november_produk_b = $rencana_kerja_november['vol_produk_b'];
			$volume_november_produk_c = $rencana_kerja_november['vol_produk_c'];
			$volume_november_produk_d = $rencana_kerja_november['vol_produk_d'];

			$total_november_volume = $volume_november_produk_a + $volume_november_produk_b + $volume_november_produk_c + $volume_november_produk_d;
		
			$nilai_jual_125_november = $volume_november_produk_a * $harga_jual_125_now['harga_satuan'];
			$nilai_jual_225_november = $volume_november_produk_b * $harga_jual_225_now['harga_satuan'];
			$nilai_jual_250_november = $volume_november_produk_c * $harga_jual_250_now['harga_satuan'];
			$nilai_jual_250_18_november = $volume_november_produk_d * $harga_jual_250_18_now['harga_satuan'];
			$nilai_jual_all_november = $nilai_jual_125_november + $nilai_jual_225_november + $nilai_jual_250_november + $nilai_jual_250_18_november;
			
			$total_november_nilai = $nilai_jual_all_november;
			
			$volume_rencana_kerja_november_produk_a = $rencana_kerja_november['vol_produk_a'];
			$volume_rencana_kerja_november_produk_b = $rencana_kerja_november['vol_produk_b'];
			$volume_rencana_kerja_november_produk_c = $rencana_kerja_november['vol_produk_c'];
			$volume_rencana_kerja_november_produk_d = $rencana_kerja_november['vol_produk_d'];

			//TOTAL PEMAKAIAN BAHAN NOVEMBER
			//TOTAL K-125
			$total_semen_125_november = $komposisi_125_produk_a * $volume_rencana_kerja_november_produk_a;
			$total_pasir_125_november = $komposisi_125_produk_b * $volume_rencana_kerja_november_produk_a;
			$total_batu1020_125_november = $komposisi_125_produk_c * $volume_rencana_kerja_november_produk_a;
			$total_batu2030_125_november = $komposisi_125_produk_d * $volume_rencana_kerja_november_produk_a;

			$nilai_semen_125_november = $total_semen_125_november * $komposisi_125['price_a'];
			$nilai_pasir_125_november = $total_pasir_125_november * $komposisi_125['price_b'];
			$nilai_batu1020_125_november = $total_batu1020_125_november * $komposisi_125['price_c'];
			$nilai_batu2030_125_november = $total_batu2030_125_november * $komposisi_125['price_d'];

			$total_125_november = $nilai_semen_125_november + $nilai_pasir_125_november + $nilai_batu1020_125_november + $nilai_batu2030_125_november;

			//TOTAL K-225
			$total_semen_225_november = $komposisi_225_produk_a * $volume_rencana_kerja_november_produk_b;
			$total_pasir_225_november = $komposisi_225_produk_b * $volume_rencana_kerja_november_produk_b;
			$total_batu1020_225_november = $komposisi_225_produk_c * $volume_rencana_kerja_november_produk_b;
			$total_batu2030_225_november = $komposisi_225_produk_d * $volume_rencana_kerja_november_produk_b;

			$nilai_semen_225_november = $total_semen_225_november * $komposisi_225['price_a'];
			$nilai_pasir_225_november = $total_pasir_225_november * $komposisi_225['price_b'];
			$nilai_batu1020_225_november = $total_batu1020_225_november * $komposisi_225['price_c'];
			$nilai_batu2030_225_november = $total_batu2030_225_november * $komposisi_225['price_d'];

			$total_225_november = $nilai_semen_225_november + $nilai_pasir_225_november + $nilai_batu1020_225_november + $nilai_batu2030_225_november;

			//TOTAL K-250
			$total_semen_250_november = $komposisi_250_produk_a * $volume_rencana_kerja_november_produk_c;
			$total_pasir_250_november = $komposisi_250_produk_b * $volume_rencana_kerja_november_produk_c;
			$total_batu1020_250_november = $komposisi_250_produk_c * $volume_rencana_kerja_november_produk_c;
			$total_batu2030_250_november = $komposisi_250_produk_d * $volume_rencana_kerja_november_produk_c;

			$nilai_semen_250_november = $total_semen_250_november * $komposisi_250['price_a'];
			$nilai_pasir_250_november = $total_pasir_250_november * $komposisi_250['price_b'];
			$nilai_batu1020_250_november = $total_batu1020_250_november * $komposisi_250['price_c'];
			$nilai_batu2030_250_november = $total_batu2030_250_november * $komposisi_250['price_d'];

			$total_250_november = $nilai_semen_250_november + $nilai_pasir_250_november + $nilai_batu1020_250_november + $nilai_batu2030_250_november;

			//TOTAL K-250_18
			$total_semen_250_18_november = $komposisi_250_18_produk_a * $volume_rencana_kerja_november_produk_d;
			$total_pasir_250_18_november = $komposisi_250_18_produk_b * $volume_rencana_kerja_november_produk_d;
			$total_batu1020_250_18_november = $komposisi_250_18_produk_c * $volume_rencana_kerja_november_produk_d;
			$total_batu2030_250_18_november = $komposisi_250_18_produk_d * $volume_rencana_kerja_november_produk_d;

			$nilai_semen_250_18_november = $total_semen_250_18_november * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_november = $total_pasir_250_18_november * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_november = $total_batu1020_250_18_november * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_november = $total_batu2030_250_18_november * $komposisi_250_18['price_d'];

			$total_250_18_november = $nilai_semen_250_18_november + $nilai_pasir_250_18_november + $nilai_batu1020_250_18_november + $nilai_batu2030_250_18_november;

			//TOTAL ALL
			$total_bahan_all_november = $total_125_november + $total_225_november + $total_250_november + $total_250_18_november;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_november = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_november_akhir')")
			->get()->row_array();

			$batching_plant_november = $total_november_volume * $rap_alat_november['batching_plant'];
			$truck_mixer_november = $total_november_volume * $rap_alat_november['truck_mixer'];
			$wheel_loader_november = $total_november_volume * $rap_alat_november['wheel_loader'];
			$bbm_solar_november = $total_november_volume * $rap_alat_november['bbm_solar'];
			$biaya_alat_all_november = $batching_plant_november + $truck_mixer_november + $wheel_loader_november + $bbm_solar_november;
		
			$total_november_biaya_bahan = $total_bahan_all_november;
			$total_november_biaya_alat = $biaya_alat_all_november;
			$total_november_biaya_overhead = $rencana_kerja_november['biaya_overhead'];
			$total_november_biaya_bank = $rencana_kerja_november['biaya_bank'];
			$total_november_biaya_persiapan = $rencana_kerja_november['biaya_persiapan'];

			$total_biaya_november_biaya = $total_november_biaya_bahan + $total_november_biaya_alat + $total_november_biaya_overhead + $total_november_biaya_bank + $total_november_biaya_persiapan;
			
			//TERMIN NOVEMBER
			$termin_november = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_november_awal' and '$date_november_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();
			?>
			<!-- NOVEMBER -->

			<!-- DESEMBER -->
			<?php
			$date_desember_awal = date('2022-12-01');
			$date_desember_akhir = date('2022-12-31');
			$rencana_kerja_desember = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_desember_awal' and '$date_desember_akhir'")
			->get()->row_array();
			$volume_desember_produk_a = $rencana_kerja_desember['vol_produk_a'];
			$volume_desember_produk_b = $rencana_kerja_desember['vol_produk_b'];
			$volume_desember_produk_c = $rencana_kerja_desember['vol_produk_c'];
			$volume_desember_produk_d = $rencana_kerja_desember['vol_produk_d'];

			$total_desember_volume = $volume_desember_produk_a + $volume_desember_produk_b + $volume_desember_produk_c + $volume_desember_produk_d;
		
			$nilai_jual_125_desember = $volume_desember_produk_a * $harga_jual_125_now['harga_satuan'];
			$nilai_jual_225_desember = $volume_desember_produk_b * $harga_jual_225_now['harga_satuan'];
			$nilai_jual_250_desember = $volume_desember_produk_c * $harga_jual_250_now['harga_satuan'];
			$nilai_jual_250_18_desember = $volume_desember_produk_d * $harga_jual_250_18_now['harga_satuan'];
			$nilai_jual_all_desember = $nilai_jual_125_desember + $nilai_jual_225_desember + $nilai_jual_250_desember + $nilai_jual_250_18_desember;
			
			$total_desember_nilai = $nilai_jual_all_desember;
			
			$volume_rencana_kerja_desember_produk_a = $rencana_kerja_desember['vol_produk_a'];
			$volume_rencana_kerja_desember_produk_b = $rencana_kerja_desember['vol_produk_b'];
			$volume_rencana_kerja_desember_produk_c = $rencana_kerja_desember['vol_produk_c'];
			$volume_rencana_kerja_desember_produk_d = $rencana_kerja_desember['vol_produk_d'];

			//TOTAL PEMAKAIAN BAHAN DESEMBER
			//TOTAL K-125
			$total_semen_125_desember = $komposisi_125_produk_a * $volume_rencana_kerja_desember_produk_a;
			$total_pasir_125_desember = $komposisi_125_produk_b * $volume_rencana_kerja_desember_produk_a;
			$total_batu1020_125_desember = $komposisi_125_produk_c * $volume_rencana_kerja_desember_produk_a;
			$total_batu2030_125_desember = $komposisi_125_produk_d * $volume_rencana_kerja_desember_produk_a;

			$nilai_semen_125_desember = $total_semen_125_desember * $komposisi_125['price_a'];
			$nilai_pasir_125_desember = $total_pasir_125_desember * $komposisi_125['price_b'];
			$nilai_batu1020_125_desember = $total_batu1020_125_desember * $komposisi_125['price_c'];
			$nilai_batu2030_125_desember = $total_batu2030_125_desember * $komposisi_125['price_d'];

			$total_125_desember = $nilai_semen_125_desember + $nilai_pasir_125_desember + $nilai_batu1020_125_desember + $nilai_batu2030_125_desember;

			//TOTAL K-225
			$total_semen_225_desember = $komposisi_225_produk_a * $volume_rencana_kerja_desember_produk_b;
			$total_pasir_225_desember = $komposisi_225_produk_b * $volume_rencana_kerja_desember_produk_b;
			$total_batu1020_225_desember = $komposisi_225_produk_c * $volume_rencana_kerja_desember_produk_b;
			$total_batu2030_225_desember = $komposisi_225_produk_d * $volume_rencana_kerja_desember_produk_b;

			$nilai_semen_225_desember = $total_semen_225_desember * $komposisi_225['price_a'];
			$nilai_pasir_225_desember = $total_pasir_225_desember * $komposisi_225['price_b'];
			$nilai_batu1020_225_desember = $total_batu1020_225_desember * $komposisi_225['price_c'];
			$nilai_batu2030_225_desember = $total_batu2030_225_desember * $komposisi_225['price_d'];

			$total_225_desember = $nilai_semen_225_desember + $nilai_pasir_225_desember + $nilai_batu1020_225_desember + $nilai_batu2030_225_desember;

			//TOTAL K-250
			$total_semen_250_desember = $komposisi_250_produk_a * $volume_rencana_kerja_desember_produk_c;
			$total_pasir_250_desember = $komposisi_250_produk_b * $volume_rencana_kerja_desember_produk_c;
			$total_batu1020_250_desember = $komposisi_250_produk_c * $volume_rencana_kerja_desember_produk_c;
			$total_batu2030_250_desember = $komposisi_250_produk_d * $volume_rencana_kerja_desember_produk_c;

			$nilai_semen_250_desember = $total_semen_250_desember * $komposisi_250['price_a'];
			$nilai_pasir_250_desember = $total_pasir_250_desember * $komposisi_250['price_b'];
			$nilai_batu1020_250_desember = $total_batu1020_250_desember * $komposisi_250['price_c'];
			$nilai_batu2030_250_desember = $total_batu2030_250_desember * $komposisi_250['price_d'];

			$total_250_desember = $nilai_semen_250_desember + $nilai_pasir_250_desember + $nilai_batu1020_250_desember + $nilai_batu2030_250_desember;

			//TOTAL K-250_18
			$total_semen_250_18_desember = $komposisi_250_18_produk_a * $volume_rencana_kerja_desember_produk_d;
			$total_pasir_250_18_desember = $komposisi_250_18_produk_b * $volume_rencana_kerja_desember_produk_d;
			$total_batu1020_250_18_desember = $komposisi_250_18_produk_c * $volume_rencana_kerja_desember_produk_d;
			$total_batu2030_250_18_desember = $komposisi_250_18_produk_d * $volume_rencana_kerja_desember_produk_d;

			$nilai_semen_250_18_desember = $total_semen_250_18_desember * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_desember = $total_pasir_250_18_desember * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_desember = $total_batu1020_250_18_desember * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_desember = $total_batu2030_250_18_desember * $komposisi_250_18['price_d'];

			$total_250_18_desember = $nilai_semen_250_18_desember + $nilai_pasir_250_18_desember + $nilai_batu1020_250_18_desember + $nilai_batu2030_250_18_desember;

			//TOTAL ALL
			$total_bahan_all_desember = $total_125_desember + $total_225_desember + $total_250_desember + $total_250_18_desember;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_desember = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_desember_akhir')")
			->get()->row_array();

			$batching_plant_desember = $total_desember_volume * $rap_alat_desember['batching_plant'];
			$truck_mixer_desember = $total_desember_volume * $rap_alat_desember['truck_mixer'];
			$wheel_loader_desember = $total_desember_volume * $rap_alat_desember['wheel_loader'];
			$bbm_solar_desember = $total_desember_volume * $rap_alat_desember['bbm_solar'];
			$biaya_alat_all_desember = $batching_plant_desember + $truck_mixer_desember + $wheel_loader_desember + $bbm_solar_desember;
		
			$total_desember_biaya_bahan = $total_bahan_all_desember;
			$total_desember_biaya_alat = $biaya_alat_all_desember;
			$total_desember_biaya_overhead = $rencana_kerja_desember['biaya_overhead'];
			$total_desember_biaya_bank = $rencana_kerja_desember['biaya_bank'];
			$total_desember_biaya_persiapan = $rencana_kerja_desember['biaya_persiapan'];

			$total_biaya_desember_biaya = $total_desember_biaya_bahan + $total_desember_biaya_alat + $total_desember_biaya_overhead + $total_desember_biaya_bank + $total_desember_biaya_persiapan;
			
			//TERMIN DESEMBER
			$termin_desember = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_desember_awal' and '$date_desember_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();
			?>
			<!-- DESEMBER -->

			<!-- JANUARI -->
			<?php
			$date_januari_awal = date('2023-01-01');
			$date_januari_akhir = date('2023-01-31');
			$rencana_kerja_januari = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_januari_akhir'")
			->get()->row_array();
			$volume_januari_produk_a = $rencana_kerja_januari['vol_produk_a'];
			$volume_januari_produk_b = $rencana_kerja_januari['vol_produk_b'];
			$volume_januari_produk_c = $rencana_kerja_januari['vol_produk_c'];
			$volume_januari_produk_d = $rencana_kerja_januari['vol_produk_d'];

			$total_januari_volume = $volume_januari_produk_a + $volume_januari_produk_b + $volume_januari_produk_c + $volume_januari_produk_d;
		
			$nilai_jual_125_januari = $volume_januari_produk_a * $harga_jual_125_now['harga_satuan'];
			$nilai_jual_225_januari = $volume_januari_produk_b * $harga_jual_225_now['harga_satuan'];
			$nilai_jual_250_januari = $volume_januari_produk_c * $harga_jual_250_now['harga_satuan'];
			$nilai_jual_250_18_januari = $volume_januari_produk_d * $harga_jual_250_18_now['harga_satuan'];
			$nilai_jual_all_januari = $nilai_jual_125_januari + $nilai_jual_225_januari + $nilai_jual_250_januari + $nilai_jual_250_18_januari;
			
			$total_januari_nilai = $nilai_jual_all_januari;
			
			$volume_rencana_kerja_januari_produk_a = $rencana_kerja_januari['vol_produk_a'];
			$volume_rencana_kerja_januari_produk_b = $rencana_kerja_januari['vol_produk_b'];
			$volume_rencana_kerja_januari_produk_c = $rencana_kerja_januari['vol_produk_c'];
			$volume_rencana_kerja_januari_produk_d = $rencana_kerja_januari['vol_produk_d'];

			//TOTAL PEMAKAIAN BAHAN JANUARI
			//TOTAL K-125
			$total_semen_125_januari = $komposisi_125_produk_a * $volume_rencana_kerja_januari_produk_a;
			$total_pasir_125_januari = $komposisi_125_produk_b * $volume_rencana_kerja_januari_produk_a;
			$total_batu1020_125_januari = $komposisi_125_produk_c * $volume_rencana_kerja_januari_produk_a;
			$total_batu2030_125_januari = $komposisi_125_produk_d * $volume_rencana_kerja_januari_produk_a;

			$nilai_semen_125_januari = $total_semen_125_januari * $komposisi_125['price_a'];
			$nilai_pasir_125_januari = $total_pasir_125_januari * $komposisi_125['price_b'];
			$nilai_batu1020_125_januari = $total_batu1020_125_januari * $komposisi_125['price_c'];
			$nilai_batu2030_125_januari = $total_batu2030_125_januari * $komposisi_125['price_d'];

			$total_125_januari = $nilai_semen_125_januari + $nilai_pasir_125_januari + $nilai_batu1020_125_januari + $nilai_batu2030_125_januari;

			//TOTAL K-225
			$total_semen_225_januari = $komposisi_225_produk_a * $volume_rencana_kerja_januari_produk_b;
			$total_pasir_225_januari = $komposisi_225_produk_b * $volume_rencana_kerja_januari_produk_b;
			$total_batu1020_225_januari = $komposisi_225_produk_c * $volume_rencana_kerja_januari_produk_b;
			$total_batu2030_225_januari = $komposisi_225_produk_d * $volume_rencana_kerja_januari_produk_b;

			$nilai_semen_225_januari = $total_semen_225_januari * $komposisi_225['price_a'];
			$nilai_pasir_225_januari = $total_pasir_225_januari * $komposisi_225['price_b'];
			$nilai_batu1020_225_januari = $total_batu1020_225_januari * $komposisi_225['price_c'];
			$nilai_batu2030_225_januari = $total_batu2030_225_januari * $komposisi_225['price_d'];

			$total_225_januari = $nilai_semen_225_januari + $nilai_pasir_225_januari + $nilai_batu1020_225_januari + $nilai_batu2030_225_januari;

			//TOTAL K-250
			$total_semen_250_januari = $komposisi_250_produk_a * $volume_rencana_kerja_januari_produk_c;
			$total_pasir_250_januari = $komposisi_250_produk_b * $volume_rencana_kerja_januari_produk_c;
			$total_batu1020_250_januari = $komposisi_250_produk_c * $volume_rencana_kerja_januari_produk_c;
			$total_batu2030_250_januari = $komposisi_250_produk_d * $volume_rencana_kerja_januari_produk_c;

			$nilai_semen_250_januari = $total_semen_250_januari * $komposisi_250['price_a'];
			$nilai_pasir_250_januari = $total_pasir_250_januari * $komposisi_250['price_b'];
			$nilai_batu1020_250_januari = $total_batu1020_250_januari * $komposisi_250['price_c'];
			$nilai_batu2030_250_januari = $total_batu2030_250_januari * $komposisi_250['price_d'];

			$total_250_januari = $nilai_semen_250_januari + $nilai_pasir_250_januari + $nilai_batu1020_250_januari + $nilai_batu2030_250_januari;

			//TOTAL K-250_18
			$total_semen_250_18_januari = $komposisi_250_18_produk_a * $volume_rencana_kerja_januari_produk_d;
			$total_pasir_250_18_januari = $komposisi_250_18_produk_b * $volume_rencana_kerja_januari_produk_d;
			$total_batu1020_250_18_januari = $komposisi_250_18_produk_c * $volume_rencana_kerja_januari_produk_d;
			$total_batu2030_250_18_januari = $komposisi_250_18_produk_d * $volume_rencana_kerja_januari_produk_d;

			$nilai_semen_250_18_januari = $total_semen_250_18_januari * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_januari = $total_pasir_250_18_januari * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_januari = $total_batu1020_250_18_januari * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_januari = $total_batu2030_250_18_januari * $komposisi_250_18['price_d'];

			$total_250_18_januari = $nilai_semen_250_18_januari + $nilai_pasir_250_18_januari + $nilai_batu1020_250_18_januari + $nilai_batu2030_250_18_januari;

			//TOTAL ALL
			$total_bahan_all_januari = $total_125_januari + $total_225_januari + $total_250_januari + $total_250_18_januari;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_januari = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_januari_akhir')")
			->get()->row_array();

			$batching_plant_januari = $total_januari_volume * $rap_alat_januari['batching_plant'];
			$truck_mixer_januari = $total_januari_volume * $rap_alat_januari['truck_mixer'];
			$wheel_loader_januari = $total_januari_volume * $rap_alat_januari['wheel_loader'];
			$bbm_solar_januari = $total_januari_volume * $rap_alat_januari['bbm_solar'];
			$biaya_alat_all_januari = $batching_plant_januari + $truck_mixer_januari + $wheel_loader_januari + $bbm_solar_januari;
		
			$total_januari_biaya_bahan = $total_bahan_all_januari;
			$total_januari_biaya_alat = $biaya_alat_all_januari;
			$total_januari_biaya_overhead = $rencana_kerja_januari['biaya_overhead'];
			$total_januari_biaya_bank = $rencana_kerja_januari['biaya_bank'];
			$total_januari_biaya_persiapan = $rencana_kerja_januari['biaya_persiapan'];

			$total_biaya_januari_biaya = $total_januari_biaya_bahan + $total_januari_biaya_alat + $total_januari_biaya_overhead + $total_januari_biaya_bank + $total_januari_biaya_persiapan;
			
			//TERMIN JANUARI
			$termin_januari = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_januari_awal' and '$date_januari_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();
			?>
			<!-- JANUARI -->

			<!-- FEBRUARI -->
			<?php
			$date_februari_awal = date('2023-02-01');
			$date_februari_akhir = date('2023-02-28');
			$rencana_kerja_februari = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_februari_awal' and '$date_februari_akhir'")
			->get()->row_array();
			$volume_februari_produk_a = $rencana_kerja_februari['vol_produk_a'];
			$volume_februari_produk_b = $rencana_kerja_februari['vol_produk_b'];
			$volume_februari_produk_c = $rencana_kerja_februari['vol_produk_c'];
			$volume_februari_produk_d = $rencana_kerja_februari['vol_produk_d'];

			$total_februari_volume = $volume_februari_produk_a + $volume_februari_produk_b + $volume_februari_produk_c + $volume_februari_produk_d;
		
			$nilai_jual_125_februari = $volume_februari_produk_a * $harga_jual_125_now['harga_satuan'];
			$nilai_jual_225_februari = $volume_februari_produk_b * $harga_jual_225_now['harga_satuan'];
			$nilai_jual_250_februari = $volume_februari_produk_c * $harga_jual_250_now['harga_satuan'];
			$nilai_jual_250_18_februari = $volume_februari_produk_d * $harga_jual_250_18_now['harga_satuan'];
			$nilai_jual_all_februari = $nilai_jual_125_februari + $nilai_jual_225_februari + $nilai_jual_250_februari + $nilai_jual_250_18_februari;
			
			$total_februari_nilai = $nilai_jual_all_februari;
			
			$volume_rencana_kerja_februari_produk_a = $rencana_kerja_februari['vol_produk_a'];
			$volume_rencana_kerja_februari_produk_b = $rencana_kerja_februari['vol_produk_b'];
			$volume_rencana_kerja_februari_produk_c = $rencana_kerja_februari['vol_produk_c'];
			$volume_rencana_kerja_februari_produk_d = $rencana_kerja_februari['vol_produk_d'];

			//TOTAL PEMAKAIAN BAHAN FEBRUARI
			//TOTAL K-125
			$total_semen_125_februari = $komposisi_125_produk_a * $volume_rencana_kerja_februari_produk_a;
			$total_pasir_125_februari = $komposisi_125_produk_b * $volume_rencana_kerja_februari_produk_a;
			$total_batu1020_125_februari = $komposisi_125_produk_c * $volume_rencana_kerja_februari_produk_a;
			$total_batu2030_125_februari = $komposisi_125_produk_d * $volume_rencana_kerja_februari_produk_a;

			$nilai_semen_125_februari = $total_semen_125_februari * $komposisi_125['price_a'];
			$nilai_pasir_125_februari = $total_pasir_125_februari * $komposisi_125['price_b'];
			$nilai_batu1020_125_februari = $total_batu1020_125_februari * $komposisi_125['price_c'];
			$nilai_batu2030_125_februari = $total_batu2030_125_februari * $komposisi_125['price_d'];

			$total_125_februari = $nilai_semen_125_februari + $nilai_pasir_125_februari + $nilai_batu1020_125_februari + $nilai_batu2030_125_februari;

			//TOTAL K-225
			$total_semen_225_februari = $komposisi_225_produk_a * $volume_rencana_kerja_februari_produk_b;
			$total_pasir_225_februari = $komposisi_225_produk_b * $volume_rencana_kerja_februari_produk_b;
			$total_batu1020_225_februari = $komposisi_225_produk_c * $volume_rencana_kerja_februari_produk_b;
			$total_batu2030_225_februari = $komposisi_225_produk_d * $volume_rencana_kerja_februari_produk_b;

			$nilai_semen_225_februari = $total_semen_225_februari * $komposisi_225['price_a'];
			$nilai_pasir_225_februari = $total_pasir_225_februari * $komposisi_225['price_b'];
			$nilai_batu1020_225_februari = $total_batu1020_225_februari * $komposisi_225['price_c'];
			$nilai_batu2030_225_februari = $total_batu2030_225_februari * $komposisi_225['price_d'];

			$total_225_februari = $nilai_semen_225_februari + $nilai_pasir_225_februari + $nilai_batu1020_225_februari + $nilai_batu2030_225_februari;

			//TOTAL K-250
			$total_semen_250_februari = $komposisi_250_produk_a * $volume_rencana_kerja_februari_produk_c;
			$total_pasir_250_februari = $komposisi_250_produk_b * $volume_rencana_kerja_februari_produk_c;
			$total_batu1020_250_februari = $komposisi_250_produk_c * $volume_rencana_kerja_februari_produk_c;
			$total_batu2030_250_februari = $komposisi_250_produk_d * $volume_rencana_kerja_februari_produk_c;

			$nilai_semen_250_februari = $total_semen_250_februari * $komposisi_250['price_a'];
			$nilai_pasir_250_februari = $total_pasir_250_februari * $komposisi_250['price_b'];
			$nilai_batu1020_250_februari = $total_batu1020_250_februari * $komposisi_250['price_c'];
			$nilai_batu2030_250_februari = $total_batu2030_250_februari * $komposisi_250['price_d'];

			$total_250_februari = $nilai_semen_250_februari + $nilai_pasir_250_februari + $nilai_batu1020_250_februari + $nilai_batu2030_250_februari;

			//TOTAL K-250_18
			$total_semen_250_18_februari = $komposisi_250_18_produk_a * $volume_rencana_kerja_februari_produk_d;
			$total_pasir_250_18_februari = $komposisi_250_18_produk_b * $volume_rencana_kerja_februari_produk_d;
			$total_batu1020_250_18_februari = $komposisi_250_18_produk_c * $volume_rencana_kerja_februari_produk_d;
			$total_batu2030_250_18_februari = $komposisi_250_18_produk_d * $volume_rencana_kerja_februari_produk_d;

			$nilai_semen_250_18_februari = $total_semen_250_18_februari * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_februari = $total_pasir_250_18_februari * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_februari = $total_batu1020_250_18_februari * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_februari = $total_batu2030_250_18_februari * $komposisi_250_18['price_d'];

			$total_250_18_februari = $nilai_semen_250_18_februari + $nilai_pasir_250_18_februari + $nilai_batu1020_250_18_februari + $nilai_batu2030_250_18_februari;

			//TOTAL ALL
			$total_bahan_all_februari = $total_125_februari + $total_225_februari + $total_250_februari + $total_250_18_februari;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_februari = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_februari_akhir')")
			->get()->row_array();

			$batching_plant_februari = $total_februari_volume * $rap_alat_februari['batching_plant'];
			$truck_mixer_februari = $total_februari_volume * $rap_alat_februari['truck_mixer'];
			$wheel_loader_februari = $total_februari_volume * $rap_alat_februari['wheel_loader'];
			$bbm_solar_februari = $total_februari_volume * $rap_alat_februari['bbm_solar'];
			$biaya_alat_all_februari = $batching_plant_februari + $truck_mixer_februari + $wheel_loader_februari + $bbm_solar_februari;
		
			$total_februari_biaya_bahan = $total_bahan_all_februari;
			$total_februari_biaya_alat = $biaya_alat_all_februari;
			$total_februari_biaya_overhead = $rencana_kerja_februari['biaya_overhead'];
			$total_februari_biaya_bank = $rencana_kerja_februari['biaya_bank'];
			$total_februari_biaya_persiapan = $rencana_kerja_februari['biaya_persiapan'];

			$total_biaya_februari_biaya = $total_februari_biaya_bahan + $total_februari_biaya_alat + $total_februari_biaya_overhead + $total_februari_biaya_bank + $total_februari_biaya_persiapan;
			
			//TERMIN FEBRUARI
			$termin_februari = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_februari_awal' and '$date_februari_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();
			?>
			<!-- FEBRUARI -->

			<!-- MARET -->
			<?php
			$date_maret_awal = date('2023-03-01');
			$date_maret_akhir = date('2023-03-31');
			$rencana_kerja_maret = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_maret_awal' and '$date_maret_akhir'")
			->get()->row_array();
			$volume_maret_produk_a = $rencana_kerja_maret['vol_produk_a'];
			$volume_maret_produk_b = $rencana_kerja_maret['vol_produk_b'];
			$volume_maret_produk_c = $rencana_kerja_maret['vol_produk_c'];
			$volume_maret_produk_d = $rencana_kerja_maret['vol_produk_d'];

			$total_maret_volume = $volume_maret_produk_a + $volume_maret_produk_b + $volume_maret_produk_c + $volume_maret_produk_d;
		
			$nilai_jual_125_maret = $volume_maret_produk_a * $harga_jual_125_now['harga_satuan'];
			$nilai_jual_225_maret = $volume_maret_produk_b * $harga_jual_225_now['harga_satuan'];
			$nilai_jual_250_maret = $volume_maret_produk_c * $harga_jual_250_now['harga_satuan'];
			$nilai_jual_250_18_maret = $volume_maret_produk_d * $harga_jual_250_18_now['harga_satuan'];
			$nilai_jual_all_maret = $nilai_jual_125_maret + $nilai_jual_225_maret + $nilai_jual_250_maret + $nilai_jual_250_18_maret;
			
			$total_maret_nilai = $nilai_jual_all_maret;
			
			$volume_rencana_kerja_maret_produk_a = $rencana_kerja_maret['vol_produk_a'];
			$volume_rencana_kerja_maret_produk_b = $rencana_kerja_maret['vol_produk_b'];
			$volume_rencana_kerja_maret_produk_c = $rencana_kerja_maret['vol_produk_c'];
			$volume_rencana_kerja_maret_produk_d = $rencana_kerja_maret['vol_produk_d'];

			//TOTAL PEMAKAIAN BAHAN MARET
			//TOTAL K-125
			$total_semen_125_maret = $komposisi_125_produk_a * $volume_rencana_kerja_maret_produk_a;
			$total_pasir_125_maret = $komposisi_125_produk_b * $volume_rencana_kerja_maret_produk_a;
			$total_batu1020_125_maret = $komposisi_125_produk_c * $volume_rencana_kerja_maret_produk_a;
			$total_batu2030_125_maret = $komposisi_125_produk_d * $volume_rencana_kerja_maret_produk_a;

			$nilai_semen_125_maret = $total_semen_125_maret * $komposisi_125['price_a'];
			$nilai_pasir_125_maret = $total_pasir_125_maret * $komposisi_125['price_b'];
			$nilai_batu1020_125_maret = $total_batu1020_125_maret * $komposisi_125['price_c'];
			$nilai_batu2030_125_maret = $total_batu2030_125_maret * $komposisi_125['price_d'];

			$total_125_maret = $nilai_semen_125_maret + $nilai_pasir_125_maret + $nilai_batu1020_125_maret + $nilai_batu2030_125_maret;

			//TOTAL K-225
			$total_semen_225_maret = $komposisi_225_produk_a * $volume_rencana_kerja_maret_produk_b;
			$total_pasir_225_maret = $komposisi_225_produk_b * $volume_rencana_kerja_maret_produk_b;
			$total_batu1020_225_maret = $komposisi_225_produk_c * $volume_rencana_kerja_maret_produk_b;
			$total_batu2030_225_maret = $komposisi_225_produk_d * $volume_rencana_kerja_maret_produk_b;

			$nilai_semen_225_maret = $total_semen_225_maret * $komposisi_225['price_a'];
			$nilai_pasir_225_maret = $total_pasir_225_maret * $komposisi_225['price_b'];
			$nilai_batu1020_225_maret = $total_batu1020_225_maret * $komposisi_225['price_c'];
			$nilai_batu2030_225_maret = $total_batu2030_225_maret * $komposisi_225['price_d'];

			$total_225_maret = $nilai_semen_225_maret + $nilai_pasir_225_maret + $nilai_batu1020_225_maret + $nilai_batu2030_225_maret;

			//TOTAL K-250
			$total_semen_250_maret = $komposisi_250_produk_a * $volume_rencana_kerja_maret_produk_c;
			$total_pasir_250_maret = $komposisi_250_produk_b * $volume_rencana_kerja_maret_produk_c;
			$total_batu1020_250_maret = $komposisi_250_produk_c * $volume_rencana_kerja_maret_produk_c;
			$total_batu2030_250_maret = $komposisi_250_produk_d * $volume_rencana_kerja_maret_produk_c;

			$nilai_semen_250_maret = $total_semen_250_maret * $komposisi_250['price_a'];
			$nilai_pasir_250_maret = $total_pasir_250_maret * $komposisi_250['price_b'];
			$nilai_batu1020_250_maret = $total_batu1020_250_maret * $komposisi_250['price_c'];
			$nilai_batu2030_250_maret = $total_batu2030_250_maret * $komposisi_250['price_d'];

			$total_250_maret = $nilai_semen_250_maret + $nilai_pasir_250_maret + $nilai_batu1020_250_maret + $nilai_batu2030_250_maret;

			//TOTAL K-250_18
			$total_semen_250_18_maret = $komposisi_250_18_produk_a * $volume_rencana_kerja_maret_produk_d;
			$total_pasir_250_18_maret = $komposisi_250_18_produk_b * $volume_rencana_kerja_maret_produk_d;
			$total_batu1020_250_18_maret = $komposisi_250_18_produk_c * $volume_rencana_kerja_maret_produk_d;
			$total_batu2030_250_18_maret = $komposisi_250_18_produk_d * $volume_rencana_kerja_maret_produk_d;

			$nilai_semen_250_18_maret = $total_semen_250_18_maret * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_maret = $total_pasir_250_18_maret * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_maret = $total_batu1020_250_18_maret * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_maret = $total_batu2030_250_18_maret * $komposisi_250_18['price_d'];

			$total_250_18_maret = $nilai_semen_250_18_maret + $nilai_pasir_250_18_maret + $nilai_batu1020_250_18_maret + $nilai_batu2030_250_18_maret;

			//TOTAL ALL
			$total_bahan_all_maret = $total_125_maret + $total_225_maret + $total_250_maret + $total_250_18_maret;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_maret = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_maret_akhir')")
			->get()->row_array();

			$batching_plant_maret = $total_maret_volume * $rap_alat_maret['batching_plant'];
			$truck_mixer_maret = $total_maret_volume * $rap_alat_maret['truck_mixer'];
			$wheel_loader_maret = $total_maret_volume * $rap_alat_maret['wheel_loader'];
			$bbm_solar_maret = $total_maret_volume * $rap_alat_maret['bbm_solar'];
			$biaya_alat_all_maret = $batching_plant_maret + $truck_mixer_maret + $wheel_loader_maret + $bbm_solar_maret;
		
			$total_maret_biaya_bahan = $total_bahan_all_maret;
			$total_maret_biaya_alat = $biaya_alat_all_maret;
			$total_maret_biaya_overhead = $rencana_kerja_maret['biaya_overhead'];
			$total_maret_biaya_bank = $rencana_kerja_maret['biaya_bank'];
			$total_maret_biaya_persiapan = $rencana_kerja_maret['biaya_persiapan'];

			$total_biaya_maret_biaya = $total_maret_biaya_bahan + $total_maret_biaya_alat + $total_maret_biaya_overhead + $total_maret_biaya_bank + $total_maret_biaya_persiapan;
			
			//TERMIN MARET
			$termin_maret = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_maret_awal' and '$date_maret_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();
			?>
			<!-- MARET -->

			<!-- APRIL -->
			<?php
			$date_april_awal = date('2023-04-01');
			$date_april_akhir = date('2023-04-30');
			$rencana_kerja_april = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_april_awal' and '$date_april_akhir'")
			->get()->row_array();
			$volume_april_produk_a = $rencana_kerja_april['vol_produk_a'];
			$volume_april_produk_b = $rencana_kerja_april['vol_produk_b'];
			$volume_april_produk_c = $rencana_kerja_april['vol_produk_c'];
			$volume_april_produk_d = $rencana_kerja_april['vol_produk_d'];

			$total_april_volume = $volume_april_produk_a + $volume_april_produk_b + $volume_april_produk_c + $volume_april_produk_d;
		
			$nilai_jual_125_april = $volume_april_produk_a * $harga_jual_125_now['harga_satuan'];
			$nilai_jual_225_april = $volume_april_produk_b * $harga_jual_225_now['harga_satuan'];
			$nilai_jual_250_april = $volume_april_produk_c * $harga_jual_250_now['harga_satuan'];
			$nilai_jual_250_18_april = $volume_april_produk_d * $harga_jual_250_18_now['harga_satuan'];
			$nilai_jual_all_april = $nilai_jual_125_april + $nilai_jual_225_april + $nilai_jual_250_april + $nilai_jual_250_18_april;
			
			$total_april_nilai = $nilai_jual_all_april;
			
			$volume_rencana_kerja_april_produk_a = $rencana_kerja_april['vol_produk_a'];
			$volume_rencana_kerja_april_produk_b = $rencana_kerja_april['vol_produk_b'];
			$volume_rencana_kerja_april_produk_c = $rencana_kerja_april['vol_produk_c'];
			$volume_rencana_kerja_april_produk_d = $rencana_kerja_april['vol_produk_d'];

			//TOTAL PEMAKAIAN BAHAN APRIL
			//TOTAL K-125
			$total_semen_125_april = $komposisi_125_produk_a * $volume_rencana_kerja_april_produk_a;
			$total_pasir_125_april = $komposisi_125_produk_b * $volume_rencana_kerja_april_produk_a;
			$total_batu1020_125_april = $komposisi_125_produk_c * $volume_rencana_kerja_april_produk_a;
			$total_batu2030_125_april = $komposisi_125_produk_d * $volume_rencana_kerja_april_produk_a;

			$nilai_semen_125_april = $total_semen_125_april * $komposisi_125['price_a'];
			$nilai_pasir_125_april = $total_pasir_125_april * $komposisi_125['price_b'];
			$nilai_batu1020_125_april = $total_batu1020_125_april * $komposisi_125['price_c'];
			$nilai_batu2030_125_april = $total_batu2030_125_april * $komposisi_125['price_d'];

			$total_125_april = $nilai_semen_125_april + $nilai_pasir_125_april + $nilai_batu1020_125_april + $nilai_batu2030_125_april;

			//TOTAL K-225
			$total_semen_225_april = $komposisi_225_produk_a * $volume_rencana_kerja_april_produk_b;
			$total_pasir_225_april = $komposisi_225_produk_b * $volume_rencana_kerja_april_produk_b;
			$total_batu1020_225_april = $komposisi_225_produk_c * $volume_rencana_kerja_april_produk_b;
			$total_batu2030_225_april = $komposisi_225_produk_d * $volume_rencana_kerja_april_produk_b;

			$nilai_semen_225_april = $total_semen_225_april * $komposisi_225['price_a'];
			$nilai_pasir_225_april = $total_pasir_225_april * $komposisi_225['price_b'];
			$nilai_batu1020_225_april = $total_batu1020_225_april * $komposisi_225['price_c'];
			$nilai_batu2030_225_april = $total_batu2030_225_april * $komposisi_225['price_d'];

			$total_225_april = $nilai_semen_225_april + $nilai_pasir_225_april + $nilai_batu1020_225_april + $nilai_batu2030_225_april;

			//TOTAL K-250
			$total_semen_250_april = $komposisi_250_produk_a * $volume_rencana_kerja_april_produk_c;
			$total_pasir_250_april = $komposisi_250_produk_b * $volume_rencana_kerja_april_produk_c;
			$total_batu1020_250_april = $komposisi_250_produk_c * $volume_rencana_kerja_april_produk_c;
			$total_batu2030_250_april = $komposisi_250_produk_d * $volume_rencana_kerja_april_produk_c;

			$nilai_semen_250_april = $total_semen_250_april * $komposisi_250['price_a'];
			$nilai_pasir_250_april = $total_pasir_250_april * $komposisi_250['price_b'];
			$nilai_batu1020_250_april = $total_batu1020_250_april * $komposisi_250['price_c'];
			$nilai_batu2030_250_april = $total_batu2030_250_april * $komposisi_250['price_d'];

			$total_250_april = $nilai_semen_250_april + $nilai_pasir_250_april + $nilai_batu1020_250_april + $nilai_batu2030_250_april;

			//TOTAL K-250_18
			$total_semen_250_18_april = $komposisi_250_18_produk_a * $volume_rencana_kerja_april_produk_d;
			$total_pasir_250_18_april = $komposisi_250_18_produk_b * $volume_rencana_kerja_april_produk_d;
			$total_batu1020_250_18_april = $komposisi_250_18_produk_c * $volume_rencana_kerja_april_produk_d;
			$total_batu2030_250_18_april = $komposisi_250_18_produk_d * $volume_rencana_kerja_april_produk_d;

			$nilai_semen_250_18_april = $total_semen_250_18_april * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_april = $total_pasir_250_18_april * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_april = $total_batu1020_250_18_april * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_april = $total_batu2030_250_18_april * $komposisi_250_18['price_d'];

			$total_250_18_april = $nilai_semen_250_18_april + $nilai_pasir_250_18_april + $nilai_batu1020_250_18_april + $nilai_batu2030_250_18_april;

			//TOTAL ALL
			$total_bahan_all_april = $total_125_april + $total_225_april + $total_250_april + $total_250_18_april;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_april = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_april_akhir')")
			->get()->row_array();

			$batching_plant_april = $total_april_volume * $rap_alat_april['batching_plant'];
			$truck_mixer_april = $total_april_volume * $rap_alat_april['truck_mixer'];
			$wheel_loader_april = $total_april_volume * $rap_alat_april['wheel_loader'];
			$bbm_solar_april = $total_april_volume * $rap_alat_april['bbm_solar'];
			$biaya_alat_all_april = $batching_plant_april + $truck_mixer_april + $wheel_loader_april + $bbm_solar_april;
		
			$total_april_biaya_bahan = $total_bahan_all_april;
			$total_april_biaya_alat = $biaya_alat_all_april;
			$total_april_biaya_overhead = $rencana_kerja_april['biaya_overhead'];
			$total_april_biaya_bank = $rencana_kerja_april['biaya_bank'];
			$total_april_biaya_persiapan = $rencana_kerja_april['biaya_persiapan'];

			$total_biaya_april_biaya = $total_april_biaya_bahan + $total_april_biaya_alat + $total_april_biaya_overhead + $total_april_biaya_bank + $total_april_biaya_persiapan;
			
			//TERMIN APRIL
			$termin_april = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_april_awal' and '$date_april_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();
			?>
			<!-- APRIL -->

			<!-- MEI -->
            <?php
			$date_mei_awal = date('2023-05-01');
			$date_mei_akhir = date('2023-05-31');
			$rencana_kerja_mei = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_mei_awal' and '$date_mei_akhir'")
			->get()->row_array();
			$volume_mei_produk_a = $rencana_kerja_mei['vol_produk_a'];
			$volume_mei_produk_b = $rencana_kerja_mei['vol_produk_b'];
			$volume_mei_produk_c = $rencana_kerja_mei['vol_produk_c'];
			$volume_mei_produk_d = $rencana_kerja_mei['vol_produk_d'];

			$total_mei_volume = $volume_mei_produk_a + $volume_mei_produk_b + $volume_mei_produk_c + $volume_mei_produk_d;
		
			$nilai_jual_125_mei = $volume_mei_produk_a * $harga_jual_125_now['harga_satuan'];
			$nilai_jual_225_mei = $volume_mei_produk_b * $harga_jual_225_now['harga_satuan'];
			$nilai_jual_250_mei = $volume_mei_produk_c * $harga_jual_250_now['harga_satuan'];
			$nilai_jual_250_18_mei = $volume_mei_produk_d * $harga_jual_250_18_now['harga_satuan'];
			$nilai_jual_all_mei = $nilai_jual_125_mei + $nilai_jual_225_mei + $nilai_jual_250_mei + $nilai_jual_250_18_mei;
			
			$total_mei_nilai = $nilai_jual_all_mei;
			
			$volume_rencana_kerja_mei_produk_a = $rencana_kerja_mei['vol_produk_a'];
			$volume_rencana_kerja_mei_produk_b = $rencana_kerja_mei['vol_produk_b'];
			$volume_rencana_kerja_mei_produk_c = $rencana_kerja_mei['vol_produk_c'];
			$volume_rencana_kerja_mei_produk_d = $rencana_kerja_mei['vol_produk_d'];

			//TOTAL PEMAKAIAN BAHAN MEI
			//TOTAL K-125
			$total_semen_125_mei = $komposisi_125_produk_a * $volume_rencana_kerja_mei_produk_a;
			$total_pasir_125_mei = $komposisi_125_produk_b * $volume_rencana_kerja_mei_produk_a;
			$total_batu1020_125_mei = $komposisi_125_produk_c * $volume_rencana_kerja_mei_produk_a;
			$total_batu2030_125_mei = $komposisi_125_produk_d * $volume_rencana_kerja_mei_produk_a;

			$nilai_semen_125_mei = $total_semen_125_mei * $komposisi_125['price_a'];
			$nilai_pasir_125_mei = $total_pasir_125_mei * $komposisi_125['price_b'];
			$nilai_batu1020_125_mei = $total_batu1020_125_mei * $komposisi_125['price_c'];
			$nilai_batu2030_125_mei = $total_batu2030_125_mei * $komposisi_125['price_d'];

			$total_125_mei = $nilai_semen_125_mei + $nilai_pasir_125_mei + $nilai_batu1020_125_mei + $nilai_batu2030_125_mei;

			//TOTAL K-225
			$total_semen_225_mei = $komposisi_225_produk_a * $volume_rencana_kerja_mei_produk_b;
			$total_pasir_225_mei = $komposisi_225_produk_b * $volume_rencana_kerja_mei_produk_b;
			$total_batu1020_225_mei = $komposisi_225_produk_c * $volume_rencana_kerja_mei_produk_b;
			$total_batu2030_225_mei = $komposisi_225_produk_d * $volume_rencana_kerja_mei_produk_b;

			$nilai_semen_225_mei = $total_semen_225_mei * $komposisi_225['price_a'];
			$nilai_pasir_225_mei = $total_pasir_225_mei * $komposisi_225['price_b'];
			$nilai_batu1020_225_mei = $total_batu1020_225_mei * $komposisi_225['price_c'];
			$nilai_batu2030_225_mei = $total_batu2030_225_mei * $komposisi_225['price_d'];

			$total_225_mei = $nilai_semen_225_mei + $nilai_pasir_225_mei + $nilai_batu1020_225_mei + $nilai_batu2030_225_mei;

			//TOTAL K-250
			$total_semen_250_mei = $komposisi_250_produk_a * $volume_rencana_kerja_mei_produk_c;
			$total_pasir_250_mei = $komposisi_250_produk_b * $volume_rencana_kerja_mei_produk_c;
			$total_batu1020_250_mei = $komposisi_250_produk_c * $volume_rencana_kerja_mei_produk_c;
			$total_batu2030_250_mei = $komposisi_250_produk_d * $volume_rencana_kerja_mei_produk_c;

			$nilai_semen_250_mei = $total_semen_250_mei * $komposisi_250['price_a'];
			$nilai_pasir_250_mei = $total_pasir_250_mei * $komposisi_250['price_b'];
			$nilai_batu1020_250_mei = $total_batu1020_250_mei * $komposisi_250['price_c'];
			$nilai_batu2030_250_mei = $total_batu2030_250_mei * $komposisi_250['price_d'];

			$total_250_mei = $nilai_semen_250_mei + $nilai_pasir_250_mei + $nilai_batu1020_250_mei + $nilai_batu2030_250_mei;

			//TOTAL K-250_18
			$total_semen_250_18_mei = $komposisi_250_18_produk_a * $volume_rencana_kerja_mei_produk_d;
			$total_pasir_250_18_mei = $komposisi_250_18_produk_b * $volume_rencana_kerja_mei_produk_d;
			$total_batu1020_250_18_mei = $komposisi_250_18_produk_c * $volume_rencana_kerja_mei_produk_d;
			$total_batu2030_250_18_mei = $komposisi_250_18_produk_d * $volume_rencana_kerja_mei_produk_d;

			$nilai_semen_250_18_mei = $total_semen_250_18_mei * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_mei = $total_pasir_250_18_mei * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_mei = $total_batu1020_250_18_mei * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_mei = $total_batu2030_250_18_mei * $komposisi_250_18['price_d'];

			$total_250_18_mei = $nilai_semen_250_18_mei + $nilai_pasir_250_18_mei + $nilai_batu1020_250_18_mei + $nilai_batu2030_250_18_mei;

			//TOTAL ALL
			$total_bahan_all_mei = $total_125_mei + $total_225_mei + $total_250_mei + $total_250_18_mei;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_mei = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_mei_akhir')")
			->get()->row_array();

			$batching_plant_mei = $total_mei_volume * $rap_alat_mei['batching_plant'];
			$truck_mixer_mei = $total_mei_volume * $rap_alat_mei['truck_mixer'];
			$wheel_loader_mei = $total_mei_volume * $rap_alat_mei['wheel_loader'];
			$bbm_solar_mei = $total_mei_volume * $rap_alat_mei['bbm_solar'];
			$biaya_alat_all_mei = $batching_plant_mei + $truck_mixer_mei + $wheel_loader_mei + $bbm_solar_mei;
		
			$total_mei_biaya_bahan = $total_bahan_all_mei;
			$total_mei_biaya_alat = $biaya_alat_all_mei;
			$total_mei_biaya_overhead = $rencana_kerja_mei['biaya_overhead'];
			$total_mei_biaya_bank = $rencana_kerja_mei['biaya_bank'];
			$total_mei_biaya_persiapan = $rencana_kerja_mei['biaya_persiapan'];

			$total_biaya_mei_biaya = $total_mei_biaya_bahan + $total_mei_biaya_alat + $total_mei_biaya_overhead + $total_mei_biaya_bank + $total_mei_biaya_persiapan;
			
			//TERMIN MEI
			$termin_mei = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_mei_awal' and '$date_mei_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();
			?>
			<!-- MEI -->

			<!-- JUNI -->
            <?php
			$date_juni_awal = date('2023-06-01');
			$date_juni_akhir = date('2023-06-30');
			$rencana_kerja_juni = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_juni_awal' and '$date_juni_akhir'")
			->get()->row_array();
			$volume_juni_produk_a = $rencana_kerja_juni['vol_produk_a'];
			$volume_juni_produk_b = $rencana_kerja_juni['vol_produk_b'];
			$volume_juni_produk_c = $rencana_kerja_juni['vol_produk_c'];
			$volume_juni_produk_d = $rencana_kerja_juni['vol_produk_d'];

			$total_juni_volume = $volume_juni_produk_a + $volume_juni_produk_b + $volume_juni_produk_c + $volume_juni_produk_d;
		
			$nilai_jual_125_juni = $volume_juni_produk_a * $harga_jual_125_now['harga_satuan'];
			$nilai_jual_225_juni = $volume_juni_produk_b * $harga_jual_225_now['harga_satuan'];
			$nilai_jual_250_juni = $volume_juni_produk_c * $harga_jual_250_now['harga_satuan'];
			$nilai_jual_250_18_juni = $volume_juni_produk_d * $harga_jual_250_18_now['harga_satuan'];
			$nilai_jual_all_juni = $nilai_jual_125_juni + $nilai_jual_225_juni + $nilai_jual_250_juni + $nilai_jual_250_18_juni;
			
			$total_juni_nilai = $nilai_jual_all_juni;
			
			$volume_rencana_kerja_juni_produk_a = $rencana_kerja_juni['vol_produk_a'];
			$volume_rencana_kerja_juni_produk_b = $rencana_kerja_juni['vol_produk_b'];
			$volume_rencana_kerja_juni_produk_c = $rencana_kerja_juni['vol_produk_c'];
			$volume_rencana_kerja_juni_produk_d = $rencana_kerja_juni['vol_produk_d'];

			//TOTAL PEMAKAIAN BAHAN JUNI
			//TOTAL K-125
			$total_semen_125_juni = $komposisi_125_produk_a * $volume_rencana_kerja_juni_produk_a;
			$total_pasir_125_juni = $komposisi_125_produk_b * $volume_rencana_kerja_juni_produk_a;
			$total_batu1020_125_juni = $komposisi_125_produk_c * $volume_rencana_kerja_juni_produk_a;
			$total_batu2030_125_juni = $komposisi_125_produk_d * $volume_rencana_kerja_juni_produk_a;

			$nilai_semen_125_juni = $total_semen_125_juni * $komposisi_125['price_a'];
			$nilai_pasir_125_juni = $total_pasir_125_juni * $komposisi_125['price_b'];
			$nilai_batu1020_125_juni = $total_batu1020_125_juni * $komposisi_125['price_c'];
			$nilai_batu2030_125_juni = $total_batu2030_125_juni * $komposisi_125['price_d'];

			$total_125_juni = $nilai_semen_125_juni + $nilai_pasir_125_juni + $nilai_batu1020_125_juni + $nilai_batu2030_125_juni;

			//TOTAL K-225
			$total_semen_225_juni = $komposisi_225_produk_a * $volume_rencana_kerja_juni_produk_b;
			$total_pasir_225_juni = $komposisi_225_produk_b * $volume_rencana_kerja_juni_produk_b;
			$total_batu1020_225_juni = $komposisi_225_produk_c * $volume_rencana_kerja_juni_produk_b;
			$total_batu2030_225_juni = $komposisi_225_produk_d * $volume_rencana_kerja_juni_produk_b;

			$nilai_semen_225_juni = $total_semen_225_juni * $komposisi_225['price_a'];
			$nilai_pasir_225_juni = $total_pasir_225_juni * $komposisi_225['price_b'];
			$nilai_batu1020_225_juni = $total_batu1020_225_juni * $komposisi_225['price_c'];
			$nilai_batu2030_225_juni = $total_batu2030_225_juni * $komposisi_225['price_d'];

			$total_225_juni = $nilai_semen_225_juni + $nilai_pasir_225_juni + $nilai_batu1020_225_juni + $nilai_batu2030_225_juni;

			//TOTAL K-250
			$total_semen_250_juni = $komposisi_250_produk_a * $volume_rencana_kerja_juni_produk_c;
			$total_pasir_250_juni = $komposisi_250_produk_b * $volume_rencana_kerja_juni_produk_c;
			$total_batu1020_250_juni = $komposisi_250_produk_c * $volume_rencana_kerja_juni_produk_c;
			$total_batu2030_250_juni = $komposisi_250_produk_d * $volume_rencana_kerja_juni_produk_c;

			$nilai_semen_250_juni = $total_semen_250_juni * $komposisi_250['price_a'];
			$nilai_pasir_250_juni = $total_pasir_250_juni * $komposisi_250['price_b'];
			$nilai_batu1020_250_juni = $total_batu1020_250_juni * $komposisi_250['price_c'];
			$nilai_batu2030_250_juni = $total_batu2030_250_juni * $komposisi_250['price_d'];

			$total_250_juni = $nilai_semen_250_juni + $nilai_pasir_250_juni + $nilai_batu1020_250_juni + $nilai_batu2030_250_juni;

			//TOTAL K-250_18
			$total_semen_250_18_juni = $komposisi_250_18_produk_a * $volume_rencana_kerja_juni_produk_d;
			$total_pasir_250_18_juni = $komposisi_250_18_produk_b * $volume_rencana_kerja_juni_produk_d;
			$total_batu1020_250_18_juni = $komposisi_250_18_produk_c * $volume_rencana_kerja_juni_produk_d;
			$total_batu2030_250_18_juni = $komposisi_250_18_produk_d * $volume_rencana_kerja_juni_produk_d;

			$nilai_semen_250_18_juni = $total_semen_250_18_juni * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_juni = $total_pasir_250_18_juni * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_juni = $total_batu1020_250_18_juni * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_juni = $total_batu2030_250_18_juni * $komposisi_250_18['price_d'];

			$total_250_18_juni = $nilai_semen_250_18_juni + $nilai_pasir_250_18_juni + $nilai_batu1020_250_18_juni + $nilai_batu2030_250_18_juni;

			//TOTAL ALL
			$total_bahan_all_juni = $total_125_juni + $total_225_juni + $total_250_juni + $total_250_18_juni;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_juni = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_juni_akhir')")
			->get()->row_array();

			$batching_plant_juni = $total_juni_volume * $rap_alat_juni['batching_plant'];
			$truck_mixer_juni = $total_juni_volume * $rap_alat_juni['truck_mixer'];
			$wheel_loader_juni = $total_juni_volume * $rap_alat_juni['wheel_loader'];
			$bbm_solar_juni = $total_juni_volume * $rap_alat_juni['bbm_solar'];
			$biaya_alat_all_juni = $batching_plant_juni + $truck_mixer_juni + $wheel_loader_juni + $bbm_solar_juni;
		
			$total_juni_biaya_bahan = $total_bahan_all_juni;
			$total_juni_biaya_alat = $biaya_alat_all_juni;
			$total_juni_biaya_overhead = $rencana_kerja_juni['biaya_overhead'];
			$total_juni_biaya_bank = $rencana_kerja_juni['biaya_bank'];
			$total_juni_biaya_persiapan = $rencana_kerja_juni['biaya_persiapan'];

			$total_biaya_juni_biaya = $total_juni_biaya_bahan + $total_juni_biaya_alat + $total_juni_biaya_overhead + $total_juni_biaya_bank + $total_juni_biaya_persiapan;
			
			//TERMIN JUNI
			$termin_juni = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_juni_awal' and '$date_juni_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();
			?>
			<!-- JUNI -->

			<!-- JULI -->
            <?php
			$date_juli_awal = date('2023-07-01');
			$date_juli_akhir = date('2023-07-31');
			$rencana_kerja_juli = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_juli_awal' and '$date_juli_akhir'")
			->get()->row_array();
			$volume_juli_produk_a = $rencana_kerja_juli['vol_produk_a'];
			$volume_juli_produk_b = $rencana_kerja_juli['vol_produk_b'];
			$volume_juli_produk_c = $rencana_kerja_juli['vol_produk_c'];
			$volume_juli_produk_d = $rencana_kerja_juli['vol_produk_d'];

			$total_juli_volume = $volume_juli_produk_a + $volume_juli_produk_b + $volume_juli_produk_c + $volume_juli_produk_d;
		
			$nilai_jual_125_juli = $volume_juli_produk_a * $harga_jual_125_now['harga_satuan'];
			$nilai_jual_225_juli = $volume_juli_produk_b * $harga_jual_225_now['harga_satuan'];
			$nilai_jual_250_juli = $volume_juli_produk_c * $harga_jual_250_now['harga_satuan'];
			$nilai_jual_250_18_juli = $volume_juli_produk_d * $harga_jual_250_18_now['harga_satuan'];
			$nilai_jual_all_juli = $nilai_jual_125_juli + $nilai_jual_225_juli + $nilai_jual_250_juli + $nilai_jual_250_18_juli;
			
			$total_juli_nilai = $nilai_jual_all_juli;
			
			$volume_rencana_kerja_juli_produk_a = $rencana_kerja_juli['vol_produk_a'];
			$volume_rencana_kerja_juli_produk_b = $rencana_kerja_juli['vol_produk_b'];
			$volume_rencana_kerja_juli_produk_c = $rencana_kerja_juli['vol_produk_c'];
			$volume_rencana_kerja_juli_produk_d = $rencana_kerja_juli['vol_produk_d'];

			//TOTAL PEMAKAIAN BAHAN JULI
			//TOTAL K-125
			$total_semen_125_juli = $komposisi_125_produk_a * $volume_rencana_kerja_juli_produk_a;
			$total_pasir_125_juli = $komposisi_125_produk_b * $volume_rencana_kerja_juli_produk_a;
			$total_batu1020_125_juli = $komposisi_125_produk_c * $volume_rencana_kerja_juli_produk_a;
			$total_batu2030_125_juli = $komposisi_125_produk_d * $volume_rencana_kerja_juli_produk_a;

			$nilai_semen_125_juli = $total_semen_125_juli * $komposisi_125['price_a'];
			$nilai_pasir_125_juli = $total_pasir_125_juli * $komposisi_125['price_b'];
			$nilai_batu1020_125_juli = $total_batu1020_125_juli * $komposisi_125['price_c'];
			$nilai_batu2030_125_juli = $total_batu2030_125_juli * $komposisi_125['price_d'];

			$total_125_juli = $nilai_semen_125_juli + $nilai_pasir_125_juli + $nilai_batu1020_125_juli + $nilai_batu2030_125_juli;

			//TOTAL K-225
			$total_semen_225_juli = $komposisi_225_produk_a * $volume_rencana_kerja_juli_produk_b;
			$total_pasir_225_juli = $komposisi_225_produk_b * $volume_rencana_kerja_juli_produk_b;
			$total_batu1020_225_juli = $komposisi_225_produk_c * $volume_rencana_kerja_juli_produk_b;
			$total_batu2030_225_juli = $komposisi_225_produk_d * $volume_rencana_kerja_juli_produk_b;

			$nilai_semen_225_juli = $total_semen_225_juli * $komposisi_225['price_a'];
			$nilai_pasir_225_juli = $total_pasir_225_juli * $komposisi_225['price_b'];
			$nilai_batu1020_225_juli = $total_batu1020_225_juli * $komposisi_225['price_c'];
			$nilai_batu2030_225_juli = $total_batu2030_225_juli * $komposisi_225['price_d'];

			$total_225_juli = $nilai_semen_225_juli + $nilai_pasir_225_juli + $nilai_batu1020_225_juli + $nilai_batu2030_225_juli;

			//TOTAL K-250
			$total_semen_250_juli = $komposisi_250_produk_a * $volume_rencana_kerja_juli_produk_c;
			$total_pasir_250_juli = $komposisi_250_produk_b * $volume_rencana_kerja_juli_produk_c;
			$total_batu1020_250_juli = $komposisi_250_produk_c * $volume_rencana_kerja_juli_produk_c;
			$total_batu2030_250_juli = $komposisi_250_produk_d * $volume_rencana_kerja_juli_produk_c;

			$nilai_semen_250_juli = $total_semen_250_juli * $komposisi_250['price_a'];
			$nilai_pasir_250_juli = $total_pasir_250_juli * $komposisi_250['price_b'];
			$nilai_batu1020_250_juli = $total_batu1020_250_juli * $komposisi_250['price_c'];
			$nilai_batu2030_250_juli = $total_batu2030_250_juli * $komposisi_250['price_d'];

			$total_250_juli = $nilai_semen_250_juli + $nilai_pasir_250_juli + $nilai_batu1020_250_juli + $nilai_batu2030_250_juli;

			//TOTAL K-250_18
			$total_semen_250_18_juli = $komposisi_250_18_produk_a * $volume_rencana_kerja_juli_produk_d;
			$total_pasir_250_18_juli = $komposisi_250_18_produk_b * $volume_rencana_kerja_juli_produk_d;
			$total_batu1020_250_18_juli = $komposisi_250_18_produk_c * $volume_rencana_kerja_juli_produk_d;
			$total_batu2030_250_18_juli = $komposisi_250_18_produk_d * $volume_rencana_kerja_juli_produk_d;

			$nilai_semen_250_18_juli = $total_semen_250_18_juli * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_juli = $total_pasir_250_18_juli * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_juli = $total_batu1020_250_18_juli * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_juli = $total_batu2030_250_18_juli * $komposisi_250_18['price_d'];

			$total_250_18_juli = $nilai_semen_250_18_juli + $nilai_pasir_250_18_juli + $nilai_batu1020_250_18_juli + $nilai_batu2030_250_18_juli;

			//TOTAL ALL
			$total_bahan_all_juli = $total_125_juli + $total_225_juli + $total_250_juli + $total_250_18_juli;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_juli = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_juli_akhir')")
			->get()->row_array();

			$batching_plant_juli = $total_juli_volume * $rap_alat_juli['batching_plant'];
			$truck_mixer_juli = $total_juli_volume * $rap_alat_juli['truck_mixer'];
			$wheel_loader_juli = $total_juli_volume * $rap_alat_juli['wheel_loader'];
			$bbm_solar_juli = $total_juli_volume * $rap_alat_juli['bbm_solar'];
			$biaya_alat_all_juli = $batching_plant_juli + $truck_mixer_juli + $wheel_loader_juli + $bbm_solar_juli;
		
			$total_juli_biaya_bahan = $total_bahan_all_juli;
			$total_juli_biaya_alat = $biaya_alat_all_juli;
			$total_juli_biaya_overhead = $rencana_kerja_juli['biaya_overhead'];
			$total_juli_biaya_bank = $rencana_kerja_juli['biaya_bank'];
			$total_juli_biaya_persiapan = $rencana_kerja_juli['biaya_persiapan'];

			$total_biaya_juli_biaya = $total_juli_biaya_bahan + $total_juli_biaya_alat + $total_juli_biaya_overhead + $total_juli_biaya_bank + $total_juli_biaya_persiapan;
			
			//TERMIN JULI
			$termin_juli = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_juli_awal' and '$date_juli_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();
			?>
			<!-- JULI -->

			<tr class="table-active4-csf">
				<th class="text-center" rowspan="2" style="vertical-align:middle">URAIAN</th>
				<th class="text-center">CURRENT</th>
				<th class="text-center">REALISASI</th>
				<th class="text-center">NOVEMBER</th>
				<th class="text-center">DESEMBER</th>
				<th class="text-center">JANUARI</th>
				<th class="text-center">FEBRUARI</th>
				<th class="text-center">MARET</th>
				<th class="text-center">APRIL</th>
				<th class="text-center">MEI</th>
				<th class="text-center">JUNI</th>
				<th class="text-center">JULI</th>
				<th class="text-center" rowspan="2" style="vertical-align:middle">JUMLAH</th>
				<th class="text-center" rowspan="2" style="vertical-align:middle">SISA</th>
	        </tr>
			<tr class="table-active4-csf">
				<th class="text-center">CASH BUDGET</th>
				<th class="text-center">SD. SAAT INI</th>
				<th class="text-center">2022</th>
				<th class="text-center">2022</th>
				<th class="text-center">2023</th>
				<th class="text-center">2023</th>
				<th class="text-center">2023</th>
				<th class="text-center">2023</th>
				<th class="text-center">2023</th>
				<th class="text-center">2023</th>
				<th class="text-center">2023</th>
	        </tr>
			<?php
			$presentase_now = ($penjualan_now['total'] / $total_rap_nilai_2022) * 100;
			$presentase_november = ($total_november_nilai / $total_rap_nilai_2022) * 100;
			$presentase_desember = ($total_desember_nilai / $total_rap_nilai_2022) * 100;
			$presentase_januari = ($total_januari_nilai / $total_rap_nilai_2022) * 100;
			$presentase_februari = ($total_februari_nilai / $total_rap_nilai_2022) * 100;
			$presentase_maret = ($total_maret_nilai / $total_rap_nilai_2022) * 100;
			$presentase_april = ($total_april_nilai / $total_rap_nilai_2022) * 100;
			$presentase_mei = ($total_mei_nilai / $total_rap_nilai_2022) * 100;
			$presentase_juni = ($total_juni_nilai / $total_rap_nilai_2022) * 100;
			$presentase_juli = ($total_juli_nilai / $total_rap_nilai_2022) * 100;

			$presentase_akumulasi_november = $presentase_now + $presentase_november;
			$presentase_akumulasi_desember = $presentase_akumulasi_november + $presentase_desember;
			$presentase_akumulasi_januari = $presentase_akumulasi_desember + $presentase_januari;
			$presentase_akumulasi_februari = $presentase_akumulasi_januari + $presentase_februari;
			$presentase_akumulasi_maret = $presentase_akumulasi_februari + $presentase_maret;
			$presentase_akumulasi_april = $presentase_akumulasi_maret + $presentase_april;
			$presentase_akumulasi_mei = $presentase_akumulasi_april + $presentase_mei;
			$presentase_akumulasi_juni = $presentase_akumulasi_mei + $presentase_juni;
			$presentase_akumulasi_juli = $presentase_akumulasi_juni + $presentase_juli;

			$jumlah_presentase = $presentase_november + $presentase_desember + $presentase_januari + $presentase_februari + $presentase_maret + $presentase_april + $presentase_mei + $presentase_juni + $presentase_juli;
			?>
			<tr class="table-active3-csf">
				<th class="text-left"><u>PRODUKSI (EXCL. PPN)</u></th>
				<th class="text-right">100%</th>
				<th class="text-right"><?php echo number_format($presentase_now,2,',','.');?>%</th>	
				<th class="text-right"><?php echo number_format($presentase_november,2,',','.');?>%</th>	
				<th class="text-right"><?php echo number_format($presentase_desember,2,',','.');?>%</th>
				<th class="text-right"><?php echo number_format($presentase_januari,2,',','.');?>%</th>
				<th class="text-right"><?php echo number_format($presentase_februari,2,',','.');?>%</th>
				<th class="text-right"><?php echo number_format($presentase_maret,2,',','.');?>%</th>
				<th class="text-right"><?php echo number_format($presentase_april,2,',','.');?>%</th>
				<th class="text-right"><?php echo number_format($presentase_mei,2,',','.');?>%</th>
				<th class="text-right"><?php echo number_format($presentase_juni,2,',','.');?>%</th>
				<th class="text-right"><?php echo number_format($presentase_juli,2,',','.');?>%</th>
				<th class="text-right"><?php echo number_format($jumlah_presentase,2,',','.');?>%</th>
				<th class="text-right"><?php echo number_format(100 - $jumlah_presentase,2,',','.');?>%</th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left">AKUMULASI (%)</th>
				<th class="text-right">100%</th>
				<th class="text-right"><?php echo number_format($presentase_now,2,',','.');?>%</th>	
				<th class="text-right"><?php echo number_format($presentase_akumulasi_november,2,',','.');?>%</th>
				<th class="text-right"><?php echo number_format($presentase_akumulasi_desember,2,',','.');?>%</th>
				<th class="text-right"><?php echo number_format($presentase_akumulasi_januari,2,',','.');?>%</th>
				<th class="text-right"><?php echo number_format($presentase_akumulasi_februari,2,',','.');?>%</th>
				<th class="text-right"><?php echo number_format($presentase_akumulasi_maret,2,',','.');?>%</th>
				<th class="text-right"><?php echo number_format($presentase_akumulasi_april,2,',','.');?>%</th>
				<th class="text-right"><?php echo number_format($presentase_akumulasi_mei,2,',','.');?>%</th>
				<th class="text-right"><?php echo number_format($presentase_akumulasi_juni,2,',','.');?>%</th>
				<th class="text-right"><?php echo number_format($presentase_akumulasi_juli,2,',','.');?>%</th>
				<th class="text-right"><?php echo number_format($presentase_akumulasi_juli,2,',','.');?>%</th>
				<th class="text-right"><?php echo number_format(100 - $presentase_akumulasi_juli,2,',','.');?>%</th>
				
			</tr>
			<?php
			$jumlah_produksi = $penjualan_now['total'] + $total_november_nilai + $total_desember_nilai + $total_januari_nilai + $total_februari_nilai + $total_maret_nilai + $total_april_nilai + $total_mei_nilai + $total_juni_nilai + $total_juli_nilai;

			$sisa_produksi = $total_rap_nilai_2022 - $jumlah_produksi;
			?>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;1. PRODUKSI (Rp.)</th>
				<th class="text-right"><?php echo number_format($total_rap_nilai_2022,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($penjualan_now['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_november_nilai,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_desember_nilai,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_januari_nilai,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_februari_nilai,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_maret_nilai,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_april_nilai,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_mei_nilai,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_juni_nilai,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_juli_nilai,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_produksi,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_produksi,0,',','.');?></th>
			</tr>
			<?php
			$akumulasi_penjualan_november = $penjualan_now['total'] + $total_november_nilai;
			$akumulasi_penjualan_desember = $penjualan_now['total'] + $total_november_nilai + $total_desember_nilai;
			$akumulasi_penjualan_januari = $penjualan_now['total'] + $total_november_nilai + $total_desember_nilai + $total_januari_nilai;
			$akumulasi_penjualan_februari = $penjualan_now['total'] + $total_november_nilai + $total_desember_nilai + $total_januari_nilai + $total_februari_nilai;
			$akumulasi_penjualan_maret = $penjualan_now['total'] + $total_november_nilai + $total_desember_nilai + $total_januari_nilai + $total_februari_nilai + $total_maret_nilai;
			$akumulasi_penjualan_april = $penjualan_now['total'] + $total_november_nilai + $total_desember_nilai + $total_januari_nilai + $total_februari_nilai + $total_maret_nilai + $total_april_nilai;
			$akumulasi_penjualan_mei = $penjualan_now['total'] + $total_november_nilai + $total_desember_nilai + $total_januari_nilai + $total_februari_nilai + $total_maret_nilai + $total_april_nilai + $total_mei_nilai;
			$akumulasi_penjualan_juni = $penjualan_now['total'] + $total_november_nilai + $total_desember_nilai + $total_januari_nilai + $total_februari_nilai + $total_maret_nilai + $total_april_nilai + $total_mei_nilai + $total_juni_nilai;
			$akumulasi_penjualan_juli = $penjualan_now['total'] + $total_november_nilai + $total_desember_nilai + $total_januari_nilai + $total_februari_nilai + $total_maret_nilai + $total_april_nilai + $total_mei_nilai + $total_juni_nilai + $total_juli_nilai;
			?>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;2. AKUMULASI (Rp.)</th>
				<th class="text-right"><?php echo number_format($total_rap_nilai_2022,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($penjualan_now['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_penjualan_november,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_penjualan_desember,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_penjualan_januari,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_penjualan_februari,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_penjualan_maret,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_penjualan_april,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_penjualan_mei,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_penjualan_juni,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_penjualan_juli,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_penjualan_juli,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_rap_nilai_2022 - $akumulasi_penjualan_juli,0,',','.');?></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left" colspan="14"><u>PENERIMAAN (EXCL. PPN)</u></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;Uang Muka</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
			</tr>
			<?php
			$termin_november = $rencana_kerja_november['termin'];
			$termin_desember = $rencana_kerja_desember['termin'];
			$termin_januari = $rencana_kerja_januari['termin'];
			$termin_februari = $rencana_kerja_februari['termin'];
			$termin_maret = $rencana_kerja_maret['termin'];
			$termin_april = $rencana_kerja_april['termin'];
			$termin_mei = $rencana_kerja_mei['termin'];
			$termin_juni = $rencana_kerja_juni['termin'];
			$termin_juli = $rencana_kerja_juli['termin'];
			$jumlah_termin = $termin_now['total'] + $termin_november + $termin_desember + $termin_januari + $termin_februari + $termin_maret + $termin_april + $termin_mei + $termin_juni + $termin_juli;
			?>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;Termin / Angsuran</th>
				<th class="text-right"><?php echo number_format($total_rap_nilai_2022,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_now['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_november,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_desember,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_januari,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_februari,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_maret,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_april,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_mei,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_juni,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_juli,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_termin,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_rap_nilai_2022 - $jumlah_termin,0,',','.');?></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;Pengembalian Retensi</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;PPN Keluaran</th>
				<th class="text-right"><?php echo number_format($total_rap_nilai_2022 / 10,0,',','.');?></th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
			</tr>
			<tr class="table-active2-csf">
				<th class="text-left"><i>JUMLAH PENERIMAAN</i></th>
				<th class="text-right"><?php echo number_format($total_rap_nilai_2022 / 10 + $total_rap_nilai_2022,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_now['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_november,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_desember,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_januari,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_februari,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_maret,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_april,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_mei,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_juni,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_juli,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_termin,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_rap_nilai_2022 - $jumlah_termin,0,',','.');?></th>
			</tr>
			<?php
			$akumulasi_termin_november = $termin_now['total'] + $termin_november;
			$akumulasi_termin_desember = $termin_now['total'] + $termin_november + $termin_desember;
			$akumulasi_termin_januari = $termin_now['total'] + $termin_november + $termin_desember + $termin_januari;
			$akumulasi_termin_februari = $termin_now['total'] + $termin_november + $termin_desember + $termin_januari + $termin_februari;
			$akumulasi_termin_maret = $termin_now['total'] + $termin_november + $termin_desember + $termin_januari + $termin_februari + $termin_maret;
			$akumulasi_termin_april = $termin_now['total'] + $termin_november + $termin_desember + $termin_januari + $termin_februari + $termin_maret + $termin_april;
			$akumulasi_termin_mei = $termin_now['total'] + $termin_november + $termin_desember + $termin_januari + $termin_februari + $termin_maret + $termin_april + $termin_mei;
			$akumulasi_termin_juni = $termin_now['total'] + $termin_november + $termin_desember + $termin_januari + $termin_februari + $termin_maret + $termin_april + $termin_mei + $termin_juni;
			$akumulasi_termin_juli = $termin_now['total'] + $termin_november + $termin_desember + $termin_januari + $termin_februari + $termin_maret + $termin_april + $termin_mei + $termin_juni + $termin_juli;
			?>
			
			<tr class="table-active2-csf">
				<th class="text-left"><i>AKUMULASI PENERIMAAN</i></th>
				<th class="text-right"><?php echo number_format($total_rap_nilai_2022,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_now['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_termin_november,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_termin_desember,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_termin_januari,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_termin_februari,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_termin_maret,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_termin_april,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_termin_mei,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_termin_juni,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_termin_juli,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_termin_juli,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_rap_nilai_2022 - $akumulasi_termin_juli,0,',','.');?></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left" colspan="14"><u>PEMAKAIAN BAHAN & ALAT</u></th>
			</tr>
			<?php
			$jumlah_bahan = $total_bahan_now + $total_bahan_all_november + $total_bahan_all_desember + $total_bahan_all_januari + $total_bahan_all_februari + $total_bahan_all_maret + $total_bahan_all_april + $total_bahan_all_mei + $total_bahan_all_juni + $total_bahan_all_juli;
			?>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;1. Bahan</th>
				<th class="text-right">-</th>
				<th class="text-right"><?php echo number_format($total_bahan_now,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_bahan_all_november,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_bahan_all_desember,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_bahan_all_januari,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_bahan_all_februari,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_bahan_all_maret,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_bahan_all_april,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_bahan_all_mei,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_bahan_all_juni,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_bahan_all_juli,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_bahan,0,',','.');?></th>
				<th class="text-right">-</th>
			</tr>
			<?php
			$jumlah_alat = $alat_now + $total_november_biaya_alat + $total_desember_biaya_alat + $total_januari_biaya_alat + $total_februari_biaya_alat + $total_maret_biaya_alat + $total_april_biaya_alat + $total_mei_biaya_alat + $total_juni_biaya_alat + $total_juli_biaya_alat;
			?>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;2. Alat</th>
				<th class="text-right">-</th>
				<th class="text-right"><?php echo number_format($alat_now,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_november_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_desember_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_januari_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_februari_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_maret_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_april_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_mei_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_juni_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_juli_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_alat,0,',','.');?></th>
				<th class="text-right">-</th>
			</tr>
			<tr class="table-active2-csf">
				<th class="text-left"><i>JUMLAH PEMAKAIAN</i></th>
				<th class="text-right">-</th>
				<th class="text-right"><?php echo number_format($total_bahan_now + $alat_now,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_bahan_all_november + $total_november_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_bahan_all_desember + $total_desember_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_bahan_all_januari + $total_januari_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_bahan_all_februari + $total_februari_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_bahan_all_maret + $total_maret_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_bahan_all_april + $total_april_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_bahan_all_mei + $total_mei_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_bahan_all_juni + $total_juni_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_bahan_all_juli + $total_juli_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_bahan + $jumlah_alat,0,',','.');?></th>
				<th class="text-right">-</th>
			</tr>
			<?php
			$akumulasi_pemakaian_november = $total_bahan_now + $alat_now + $total_bahan_all_november + $total_november_biaya_alat;
			$akumulasi_pemakaian_desember = $total_bahan_now + $alat_now + $total_bahan_all_november + $total_november_biaya_alat + $total_bahan_all_desember + $total_desember_biaya_alat;
			$akumulasi_pemakaian_januari = $total_bahan_now + $alat_now + $total_bahan_all_november + $total_november_biaya_alat + $total_bahan_all_desember + $total_desember_biaya_alat + $total_bahan_all_januari + $total_januari_biaya_alat;
			$akumulasi_pemakaian_februari = $total_bahan_now + $alat_now + $total_bahan_all_november + $total_november_biaya_alat + $total_bahan_all_desember + $total_desember_biaya_alat + $total_bahan_all_januari + $total_januari_biaya_alat + $total_bahan_all_februari + $total_februari_biaya_alat;
			$akumulasi_pemakaian_maret = $total_bahan_now + $alat_now + $total_bahan_all_november + $total_november_biaya_alat + $total_bahan_all_desember + $total_desember_biaya_alat + $total_bahan_all_januari + $total_januari_biaya_alat + $total_bahan_all_februari + $total_februari_biaya_alat + $total_bahan_all_maret + $total_maret_biaya_alat;
			$akumulasi_pemakaian_april = $total_bahan_now + $alat_now + $total_bahan_all_november + $total_november_biaya_alat + $total_bahan_all_desember + $total_desember_biaya_alat + $total_bahan_all_januari + $total_januari_biaya_alat + $total_bahan_all_februari + $total_februari_biaya_alat + $total_bahan_all_maret + $total_maret_biaya_alat + $total_bahan_all_april + $total_april_biaya_alat;
			$akumulasi_pemakaian_mei = $total_bahan_now + $alat_now + $total_bahan_all_november + $total_november_biaya_alat + $total_bahan_all_desember + $total_desember_biaya_alat + $total_bahan_all_januari + $total_januari_biaya_alat + $total_bahan_all_februari + $total_februari_biaya_alat + $total_bahan_all_maret + $total_maret_biaya_alat + $total_bahan_all_april + $total_april_biaya_alat + $total_bahan_all_mei + $total_mei_biaya_alat;
			$akumulasi_pemakaian_juni = $total_bahan_now + $alat_now + $total_bahan_all_november + $total_november_biaya_alat + $total_bahan_all_desember + $total_desember_biaya_alat + $total_bahan_all_januari + $total_januari_biaya_alat + $total_bahan_all_februari + $total_februari_biaya_alat + $total_bahan_all_maret + $total_maret_biaya_alat + $total_bahan_all_april + $total_april_biaya_alat + $total_bahan_all_mei + $total_mei_biaya_alat  + $total_bahan_all_juni + $total_juni_biaya_alat;
			$akumulasi_pemakaian_juli = $total_bahan_now + $alat_now + $total_bahan_all_november + $total_november_biaya_alat + $total_bahan_all_desember + $total_desember_biaya_alat + $total_bahan_all_januari + $total_januari_biaya_alat + $total_bahan_all_februari + $total_februari_biaya_alat + $total_bahan_all_maret + $total_maret_biaya_alat + $total_bahan_all_april + $total_april_biaya_alat + $total_bahan_all_mei + $total_mei_biaya_alat  + $total_bahan_all_juni + $total_juni_biaya_alat + $total_bahan_all_juli + $total_juli_biaya_alat;
			?>
			<tr class="table-active2-csf">
				<th class="text-left"><i>AKUMULASI PEMAKAIAN</i></th>
				<th class="text-right">-</th>
				<th class="text-right"><?php echo number_format($total_bahan_now + $alat_now,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_pemakaian_november,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_pemakaian_desember,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_pemakaian_januari,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_pemakaian_februari,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_pemakaian_maret,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_pemakaian_april,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_pemakaian_mei,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_pemakaian_juni,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_pemakaian_juli,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_pemakaian_juli,0,',','.');?></th>
				<th class="text-right">-</th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left" colspan="14"><u>PENGELUARAN (EXCL. PPN)</u></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;1. Biaya Bahan</th>
				<th class="text-right"><?php echo number_format($total_bahan_all_rap_2022,0,',','.');?></th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;2. Biaya Upah</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;3. Biaya Peralatan</th>
				<th class="text-right"><?php echo number_format($total_rap_2022_biaya_alat,0,',','.');?></th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;4. Biaya Subkontraktor</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
			</tr>
			<?php
			$jumlah_biaya_bank = $diskonto_now + $rencana_kerja_november['biaya_bank'] + $rencana_kerja_desember['biaya_bank'] + $rencana_kerja_januari['biaya_bank'] + $rencana_kerja_februari['biaya_bank'] + $rencana_kerja_maret['biaya_bank'] + $rencana_kerja_april['biaya_bank'] + $rencana_kerja_mei['biaya_bank'] + $rencana_kerja_juni['biaya_bank'] + $rencana_kerja_juli['biaya_bank'];
			?>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;5. Biaya Bank</th>
				<th class="text-right"><?php echo number_format($total_rap_2022_biaya_bank,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($diskonto_now,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_november['biaya_bank'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_desember['biaya_bank'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_januari['biaya_bank'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_februari['biaya_bank'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_maret['biaya_bank'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_april['biaya_bank'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_mei['biaya_bank'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_juni['biaya_bank'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_juli['biaya_bank'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_biaya_bank,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_rap_2022_biaya_bank - $jumlah_biaya_bank,0,',','.');?></th>
			</tr>
			<?php
			$jumlah_bua = $overhead_now + $rencana_kerja_november['biaya_overhead'] + $rencana_kerja_desember['biaya_overhead'] + $rencana_kerja_januari['biaya_overhead'] + $rencana_kerja_februari['biaya_overhead'] + $rencana_kerja_maret['biaya_overhead'] + $rencana_kerja_april['biaya_overhead'] + $rencana_kerja_mei['biaya_overhead'] + $rencana_kerja_juni['biaya_overhead'] + $rencana_kerja_juli['biaya_overhead'];
			?>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;6. BAU Proyek</th>
				<th class="text-right"><?php echo number_format($total_rap_2022_biaya_overhead,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($overhead_now,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_november['biaya_overhead'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_desember['biaya_overhead'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_januari['biaya_overhead'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_februari['biaya_overhead'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_maret['biaya_overhead'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_april['biaya_overhead'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_mei['biaya_overhead'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_juni['biaya_overhead'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_juli['biaya_overhead'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_bua,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_rap_2022_biaya_overhead - $jumlah_bua,0,',','.');?></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;7. Rupa - Rupa</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right"></th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
			</tr>
			<?php
			$jumlah_persiapan = $persiapan_now + $rencana_kerja_november['biaya_persiapan'] + $rencana_kerja_desember['biaya_persiapan'] + $rencana_kerja_januari['biaya_persiapan'] + $rencana_kerja_februari['biaya_persiapan'] + $rencana_kerja_maret['biaya_persiapan'] + $rencana_kerja_april['biaya_persiapan'] + $rencana_kerja_mei['biaya_persiapan'] + $rencana_kerja_juni['biaya_persiapan'] + $rencana_kerja_juli['biaya_persiapan'];
			?>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;8. Persiapan</th>
				<th class="text-right"><?php echo number_format($total_rap_2022_biaya_persiapan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($persiapan_now,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_november['biaya_persiapan'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_desember['biaya_persiapan'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_januari['biaya_persiapan'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_februari['biaya_persiapan'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_maret['biaya_persiapan'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_april['biaya_persiapan'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_mei['biaya_persiapan'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_juni['biaya_persiapan'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_juli['biaya_persiapan'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_persiapan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_rap_2022_biaya_persiapan - $jumlah_persiapan,0,',','.');?></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;9. Lain - Lain / Susut Aktiva</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;10. PPN Masukan</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
			</tr>
			<?php
			$total_biaya_bank = $total_rap_2022_biaya_bank - $jumlah_biaya_bank;
			$total_biaya_bua = $total_rap_2022_biaya_overhead - $jumlah_bua;
			$total_biaya_persiapan = $total_rap_2022_biaya_persiapan - $jumlah_persiapan;
			?>
			<tr class="table-active2-csf">
				<th class="text-left"><i>JUMLAH III</i></th>
				<th class="text-right"><?php echo number_format($total_biaya_rap_2022_biaya,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($diskonto_now + $overhead_now + $persiapan_now,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_november['biaya_overhead'] + $rencana_kerja_november['biaya_bank'] + $rencana_kerja_november['biaya_persiapan'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_desember['biaya_overhead'] + $rencana_kerja_desember['biaya_bank'] + $rencana_kerja_desember['biaya_persiapan'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_januari['biaya_overhead'] + $rencana_kerja_januari['biaya_bank'] + $rencana_kerja_januari['biaya_persiapan'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_februari['biaya_overhead'] + $rencana_kerja_februari['biaya_bank'] + $rencana_kerja_februari['biaya_persiapan'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_maret['biaya_overhead'] + $rencana_kerja_maret['biaya_bank'] + $rencana_kerja_maret['biaya_persiapan'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_april['biaya_overhead'] + $rencana_kerja_april['biaya_bank'] + $rencana_kerja_april['biaya_persiapan'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_mei['biaya_overhead'] + $rencana_kerja_mei['biaya_bank'] + $rencana_kerja_mei['biaya_persiapan'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_juni['biaya_overhead'] + $rencana_kerja_juni['biaya_bank'] + $rencana_kerja_juni['biaya_persiapan'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rencana_kerja_juli['biaya_overhead'] + $rencana_kerja_juli['biaya_bank'] + $rencana_kerja_juli['biaya_persiapan'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_biaya_bank + $jumlah_persiapan + $jumlah_bua,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_biaya_bank + $total_biaya_bua + $total_biaya_persiapan,0,',','.');?></th>
			</tr>
			<?php
			$jumlah_penerimaan = $termin_now['total'];
			$jumlah_lll_november = $rencana_kerja_november['biaya_overhead'] + $rencana_kerja_november['biaya_bank'] + $rencana_kerja_november['biaya_persiapan'];
			$jumlah_lll_desember = $rencana_kerja_desember['biaya_overhead'] + $rencana_kerja_desember['biaya_bank'] + $rencana_kerja_desember['biaya_persiapan'];
			$jumlah_lll_januari = $rencana_kerja_januari['biaya_overhead'] + $rencana_kerja_januari['biaya_bank'] + $rencana_kerja_januari['biaya_persiapan'];
			$jumlah_lll_februari = $rencana_kerja_februari['biaya_overhead'] + $rencana_kerja_februari['biaya_bank'] + $rencana_kerja_februari['biaya_persiapan'];
			$jumlah_lll_maret = $rencana_kerja_maret['biaya_overhead'] + $rencana_kerja_maret['biaya_bank'] + $rencana_kerja_maret['biaya_persiapan'];
			$jumlah_lll_april = $rencana_kerja_april['biaya_overhead'] + $rencana_kerja_april['biaya_bank'] + $rencana_kerja_april['biaya_persiapan'];
			$jumlah_lll_mei = $rencana_kerja_mei['biaya_overhead'] + $rencana_kerja_mei['biaya_bank'] + $rencana_kerja_mei['biaya_persiapan'];
			$jumlah_lll_juni = $rencana_kerja_juni['biaya_overhead'] + $rencana_kerja_juni['biaya_bank'] + $rencana_kerja_juni['biaya_persiapan'];
			$jumlah_lll_juli = $rencana_kerja_juli['biaya_overhead'] + $rencana_kerja_juli['biaya_bank'] + $rencana_kerja_juli['biaya_persiapan'];

			$jumlah_penerimaan_total = $total_rap_nilai_2022 - $jumlah_termin;
			?>
			<tr class="table-active2-csf">
				<th class="text-left"><i>POSISI (II - III)</i></th>
				<th class="text-right"><?php echo number_format($total_rap_nilai_2022 / 10 + $total_rap_nilai_2022 - $total_biaya_rap_2022_biaya,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_penerimaan - ($diskonto_now + $overhead_now + $persiapan_now),0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_penerimaan - $jumlah_lll_november,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_penerimaan - $jumlah_lll_desember,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_penerimaan - $jumlah_lll_januari,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_penerimaan - $jumlah_lll_februari,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_penerimaan - $jumlah_lll_maret,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_penerimaan - $jumlah_lll_april,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_penerimaan - $jumlah_lll_mei,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_penerimaan - $jumlah_lll_juni,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_penerimaan - $jumlah_lll_juli,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_termin - ($jumlah_biaya_bank + $jumlah_persiapan + $jumlah_bua),0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_penerimaan_total -  ($total_biaya_bank + $total_biaya_bua + $total_biaya_persiapan),0,',','.');?></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left" colspan="14"><u>PAJAK</u></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;1. Pajak Keluaran</th>
				<th class="text-right"><?php echo number_format($total_rap_nilai_2022 / 10,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($ppn_keluar['total'],0,',','.');?></th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right"><?php echo number_format($ppn_keluar['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format(($total_rap_nilai_2022 / 10) - $ppn_keluar['total'],0,',','.');?></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;2. Pajak Masukan</th>
				<th class="text-right"><?php echo number_format($total_bahan_all_rap_2022 / 10,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($ppn_masuk['total'],0,',','.');?></th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right"><?php echo number_format($ppn_masuk['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format(($total_bahan_all_rap_2022 / 10) - $ppn_masuk['total'],0,',','.');?></th>
			</tr>
			<tr class="table-active2-csf">
				<th class="text-left"><i>JUMLAH V (1-2)</i></th>
				<th class="text-right"><?php echo number_format($total_rap_nilai_2022 / 10 - $total_bahan_all_rap_2022  / 10,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($ppn_keluar['total'] - $ppn_masuk['total'],0,',','.');?></th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right"><?php echo number_format($ppn_keluar['total'] - $ppn_masuk['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format(($total_rap_nilai_2022 / 10 - $total_bahan_all_rap_2022  / 10) - ($ppn_keluar['total'] - $ppn_masuk['total']),0,',','.');?></th>
			</tr>
			<?php
			$posisi_iv = $jumlah_penerimaan - ($diskonto_now + $overhead_now + $persiapan_now);
			$posisi_iv_november = $jumlah_penerimaan - $jumlah_lll_november;
			$posisi_iv_desember = $jumlah_penerimaan - $jumlah_lll_desember;
			$posisi_iv_januari = $jumlah_penerimaan - $jumlah_lll_januari;
			$posisi_iv_februari = $jumlah_penerimaan - $jumlah_lll_februari;
			$posisi_iv_maret = $jumlah_penerimaan - $jumlah_lll_maret;
			$posisi_iv_april = $jumlah_penerimaan - $jumlah_lll_april;
			$posisi_iv_mei = $jumlah_penerimaan - $jumlah_lll_mei;
			$posisi_iv_juni = $jumlah_penerimaan - $jumlah_lll_juni;
			$posisi_iv_juli = $jumlah_penerimaan - $jumlah_lll_juli;

			$posisi_lll = $jumlah_termin - ($jumlah_biaya_bank + $jumlah_persiapan + $jumlah_bua);
			$posisi_ll_sisa = $jumlah_penerimaan_total - ($total_biaya_bank + $total_biaya_bua + $total_biaya_persiapan);
			$posisi_lll_sisa = ($total_rap_nilai_2022 / 10 - $total_bahan_all_rap_2022  / 10) - ($ppn_keluar['total'] - $ppn_masuk['total']);
			?>
			<tr class="table-active2-csf">
				<th class="text-left"><i>POSISI (IV+V)</i></th>
				<th class="text-right"><?php echo number_format(($total_rap_nilai_2022 / 10 + $total_rap_nilai_2022 - $total_biaya_rap_2022_biaya) - ($total_rap_nilai_2022 / 10 - $total_bahan_all_rap_2022  / 10),0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_iv - ($ppn_keluar['total'] - $ppn_masuk['total']),0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_iv_november,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_iv_desember,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_iv_januari,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_iv_februari,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_iv_maret,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_iv_april,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_iv_mei,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_iv_juni,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_iv_juli,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_lll - $ppn_keluar['total'] - $ppn_masuk['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_ll_sisa - $posisi_lll_sisa,0,',','.');?></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left" colspan="14"><u>PINJAMAN</u></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;Penerimaan Pinjaman</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>

			</tr>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;Pengembalian Pinjaman</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
			</tr>
			<tr class="table-active2-csf">
				<th class="text-left"><i>JUMLAH VII</i></th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
			</tr>
			<tr class="table-active2-csf">
				<th class="text-left"><i>POSISI (VI+VII)</i></th>
				<th class="text-right"><?php echo number_format(($total_rap_nilai_2022 / 10 + $total_rap_nilai_2022 - $total_biaya_rap_2022_biaya) - ($total_rap_nilai_2022 / 10 - $total_bahan_all_rap_2022  / 10),0,',','.');?></th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
			</tr>
			<tr class="table-active2-csf">
				<th class="text-left"><i>KAS AWAL</i></th>
				<th class="text-right"><?php echo number_format(($total_rap_nilai_2022 / 10 + $total_rap_nilai_2022 - $total_biaya_rap_2022_biaya) - ($total_rap_nilai_2022 / 10 - $total_bahan_all_rap_2022  / 10),0,',','.');?></th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
			</tr>
			<tr class="table-active2-csf">
				<th class="text-left"><i>KAS AKHIR</i></th>
				<th class="text-right"><?php echo number_format(($total_rap_nilai_2022 / 10 + $total_rap_nilai_2022 - $total_biaya_rap_2022_biaya) - ($total_rap_nilai_2022 / 10 - $total_bahan_all_rap_2022  / 10),0,',','.');?></th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
			</tr>
	    </table>
		<?php
	}

	public function evaluasi_bahan($arr_date)
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

			<!-- TOTAL PEMAKAIAN KOMPOSISI -->

			<?php

			$komposisi = $this->db->select('pp.date_production, (pp.display_volume) * pk.presentase_a as volume_a, (pp.display_volume) * pk.presentase_b as volume_b, (pp.display_volume) * pk.presentase_c as volume_c, (pp.display_volume) * pk.presentase_d as volume_d, (pp.display_volume * pk.presentase_a) * pk.price_a as nilai_a, (pp.display_volume * pk.presentase_b) * pk.price_b as nilai_b, (pp.display_volume * pk.presentase_c) * pk.price_c as nilai_c, (pp.display_volume * pk.presentase_d) * pk.price_d as nilai_d')
			->from('pmm_productions pp')
			->join('pmm_agregat pk', 'pp.komposisi_id = pk.id','left')
			->where("pp.date_production between '$date1' and '$date2'")
			->get()->result_array();

			$total_volume_a = 0;
			$total_volume_b = 0;
			$total_volume_c = 0;
			$total_volume_d = 0;

			$total_nilai_a = 0;
			$total_nilai_b = 0;
			$total_nilai_c = 0;
			$total_nilai_d = 0;

			foreach ($komposisi as $x){
				$total_volume_a += $x['volume_a'];
				$total_volume_b += $x['volume_b'];
				$total_volume_c += $x['volume_c'];
				$total_volume_d += $x['volume_d'];
				$total_nilai_a += $x['nilai_a'];
				$total_nilai_b += $x['nilai_b'];
				$total_nilai_c += $x['nilai_c'];
				$total_nilai_d += $x['nilai_d'];
				
			}

			$volume_a = $total_volume_a;
			$volume_b = $total_volume_b;
			$volume_c = $total_volume_c;
			$volume_d = $total_volume_d;

			$nilai_a = $total_nilai_a;
			$nilai_b = $total_nilai_b;
			$nilai_c = $total_nilai_c;
			$nilai_d = $total_nilai_d;

			$price_a = ($total_volume_a!=0)?$total_nilai_a / $total_volume_a * 1:0;
			$price_b = ($total_volume_b!=0)?$total_nilai_a / $total_volume_b * 1:0;
			$price_c = ($total_volume_c!=0)?$total_nilai_a / $total_volume_c * 1:0;
			$price_d = ($total_volume_d!=0)?$total_nilai_a / $total_volume_d * 1:0;

			$total_volume_komposisi = $volume_a + $volume_b + $volume_c + $volume_d;
			$total_nilai_komposisi = $nilai_a + $nilai_b + $nilai_c + $nilai_d;
			
			?>

			<!-- END TOTAL PEMAKAIAN KOMPOSISI -->

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
			$total_harga_pembelian_semen_akhir = ($total_volume_pembelian_semen_akhir!=0)?($nilai_opening_balance_semen + $total_nilai_pembelian_semen) / $total_volume_pembelian_semen_akhir * 1:0;
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
			$total_harga_pembelian_semen_cons_akhir = ($total_volume_pembelian_semen_cons_akhir!=0)?($total_nilai_pembelian_semen_akhir + $total_nilai_pembelian_semen_cons) / $total_volume_pembelian_semen_cons_akhir * 1:0;
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
			$total_harga_pembelian_semen_opc_akhir = ($total_volume_pembelian_semen_opc_akhir!=0)?($total_nilai_pembelian_semen_cons_akhir + $total_nilai_pembelian_semen_opc) / $total_volume_pembelian_semen_opc_akhir * 1:0;
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
			$total_harga_pemakaian_semen = ($total_volume_pemakaian_semen!=0)?$total_nilai_pemakaian_semen / $total_volume_pemakaian_semen * 1:0;

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
			$total_harga_pembelian_pasir_akhir = ($total_volume_pembelian_pasir_akhir!=0)?($nilai_opening_balance_pasir + $total_nilai_pembelian_pasir) / $total_volume_pembelian_pasir_akhir * 1:0;
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
	
			$total_nilai_pembelian = $total_nilai_pembelian_semen_all + $total_nilai_pembelian_pasir + $total_nilai_pembelian_batu1020 + $total_nilai_pembelian_batu2030;
			$total_nilai_pemakaian = $total_nilai_pemakaian_semen + $total_nilai_pemakaian_pasir + $total_nilai_pemakaian_batu1020 + $total_nilai_pemakaian_batu2030;
			$total_nilai_akhir = $total_nilai_stock_semen_akhir + $total_nilai_stock_pasir_akhir + $total_nilai_stock_batu1020_akhir + $total_nilai_stock_batu2030_akhir;

			$total_volume_realisasi = $total_volume_pemakaian_semen + $total_volume_pemakaian_pasir + $total_volume_pemakaian_batu1020 + $total_volume_pemakaian_batu2030;
			$total_nilai_realisasi = $total_nilai_pemakaian_semen + $total_nilai_pemakaian_pasir + $total_nilai_pemakaian_batu1020 + $total_nilai_pemakaian_batu2030;

			$evaluasi_volume_a = round($volume_a - $total_volume_pemakaian_semen,2);
			$evaluasi_volume_b = round($volume_b - $total_volume_pemakaian_pasir,2);
			$evaluasi_volume_c = round($volume_c - $total_volume_pemakaian_batu1020,2);
			$evaluasi_volume_d = round($volume_d - $total_volume_pemakaian_batu2030,2);

			$evaluasi_nilai_a = round($nilai_a - $total_nilai_pemakaian_semen,0);
			$evaluasi_nilai_b = round($nilai_b - $total_nilai_pemakaian_pasir,0);
			$evaluasi_nilai_c = round($nilai_c - $total_nilai_pemakaian_batu1020,0);
			$evaluasi_nilai_d = round($nilai_d - $total_nilai_pemakaian_batu2030,0);

			$total_nilai_evaluasi = round($evaluasi_nilai_a + $evaluasi_nilai_b + $evaluasi_nilai_c + $evaluasi_nilai_d,0);

	        ?>
			
			<tr class="table-active4">
				<th width="5%" class="text-center" rowspan="2" style="vertical-align:middle">NO.</th>
				<th width="20%" class="text-center" rowspan="2" style="vertical-align:middle">URAIAN</th>
				<th width="15%" class="text-center" rowspan="2" style="vertical-align:middle">SATUAN</th>
				<th width="20%" class="text-center" colspan="2">RAP</th>
				<th width="20%" class="text-center" colspan="2">REALISASI</th>
				<th width="20%" class="text-center" colspan="2">EVALUASI</th>
	        </tr>
			<tr class="table-active4">
				<th width="8%" class="text-center">VOLUME</th>
				<th width="13%" class="text-center">NILAI</th>
				<th width="8%" class="text-center">VOLUME</th>
				<th width="13%" class="text-center">NILAI</th>
				<th width="8%" class="text-center">VOLUME</th>
				<th width="13%" class="text-center">NILAI</th>
	        </tr>
			<?php
				$styleColorA = $evaluasi_volume_a < 0 ? 'color:red' : 'color:black';
				$styleColorB = $evaluasi_volume_b < 0 ? 'color:red' : 'color:black';
				$styleColorC = $evaluasi_volume_c < 0 ? 'color:red' : 'color:black';
				$styleColorD = $evaluasi_volume_d < 0 ? 'color:red' : 'color:black';

				$styleColorAA = $evaluasi_nilai_a < 0 ? 'color:red' : 'color:black';
				$styleColorBB = $evaluasi_nilai_b < 0 ? 'color:red' : 'color:black';
				$styleColorCC = $evaluasi_nilai_c < 0 ? 'color:red' : 'color:black';
				$styleColorDD = $evaluasi_nilai_d < 0 ? 'color:red' : 'color:black';
				$styleColorEE = $total_nilai_evaluasi < 0 ? 'color:red' : 'color:black';
			?>
			<tr class="table-active3">
				<th class="text-center" style="vertical-align:middle">1</th>			
				<th class="text-left">Semen</th>
				<th class="text-center">Ton</th>
				<th class="text-right"><?php echo number_format($volume_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($nilai_a,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_pemakaian_semen,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pemakaian_semen,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorA ?>"><?php echo number_format($evaluasi_volume_a,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorAA ?>"><?php echo number_format($evaluasi_nilai_a,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center" style="vertical-align:middle">2</th>			
				<th class="text-left">Pasir</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($nilai_b,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_pemakaian_pasir,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pemakaian_pasir,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorB ?>"><?php echo number_format($evaluasi_volume_b,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorBB ?>"><?php echo number_format($evaluasi_nilai_b,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center" style="vertical-align:middle">3</th>			
				<th class="text-left">Batu Split 10-20</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($nilai_c,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_pemakaian_batu1020,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pemakaian_batu1020,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorC ?>"><?php echo number_format($evaluasi_volume_c,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorCC ?>"><?php echo number_format($evaluasi_nilai_c,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center" style="vertical-align:middle">4</th>			
				<th class="text-left">Batu Split 20-30</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($nilai_d,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_pemakaian_batu2030,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pemakaian_batu2030,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorD ?>"><?php echo number_format($evaluasi_volume_d,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorDD ?>"><?php echo number_format($evaluasi_nilai_d,0,',','.');?></th>
	        </tr>
			<tr class="table-active5">		
				<th class="text-right" colspan="3">TOTAL</th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($total_nilai_komposisi,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($total_nilai_realisasi,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right" style="<?php echo $styleColorEE ?>"><?php echo number_format($total_nilai_evaluasi,0,',','.');?></th>
	        </tr>
	    </table>
		<?php
	}

	public function evaluasi_alat($arr_date)
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

			<!-- Pemakaian Peralatan -->
			
			<?php
			//Batching Plant
			$pembelian_batching_plant = $this->db->select('
			pn.nama, po.no_po, po.subject, prm.measure, SUM(prm.volume) as volume, SUM(prm.price) / SUM(prm.volume) as harga_satuan, SUM(prm.price) as price')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->join('penerima pn', 'po.supplier_id = pn.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("prm.material_id = '15'")
			->where("po.status in ('PUBLISH','CLOSED')")
			->group_by('prm.harga_satuan')
			->order_by('pn.nama','asc')
			->get()->result_array();

			$total_nilai_batching_plant = 0;
			foreach ($pembelian_batching_plant as $x){
				$total_nilai_batching_plant += $x['price'];
			}

			//Truck Mixer
			$pembelian_truck_mixer = $this->db->select('
			pn.nama, po.no_po, po.subject, prm.measure, SUM(prm.volume) as volume, SUM(prm.price) / SUM(prm.volume) as harga_satuan, SUM(prm.price) as price')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->join('penerima pn', 'po.supplier_id = pn.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("prm.material_id in (12,13,14,23,24,25)")
			->where("po.status in ('PUBLISH','CLOSED')")
			->group_by('prm.harga_satuan')
			->order_by('pn.nama','asc')
			->get()->result_array();

			$total_nilai_truck_mixer = 0;
			foreach ($pembelian_truck_mixer as $x){
				$total_nilai_truck_mixer += $x['price'];
			}

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

			//Wheel Loader
			$pembelian_wheel_loader = $this->db->select('
			pn.nama, po.no_po, po.subject, prm.measure, SUM(prm.volume) as volume, SUM(prm.price) / SUM(prm.volume) as harga_satuan, SUM(prm.price) as price')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->join('penerima pn', 'po.supplier_id = pn.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("prm.material_id = '16'")
			->where("po.status in ('PUBLISH','CLOSED')")
			->group_by('prm.harga_satuan')
			->order_by('pn.nama','asc')
			->get()->result_array();

			$total_nilai_wheel_loader = 0;
			foreach ($pembelian_wheel_loader as $x){
				$total_nilai_wheel_loader += $x['price'];
			}

			//BBM SOLAR
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

			$akumulasi_bbm = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar_2 as total_nilai_keluar_2')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi between '$date1' and '$date2')")
			->get()->result_array();

			$total_akumulasi_bbm = 0;
			foreach ($akumulasi_bbm as $b){
				$total_akumulasi_bbm += $b['total_nilai_keluar_2'];
			}
			
			
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
			
			$total_volume = 0;
			foreach ($penjualan as $x){
				$total_volume += $x['volume'];
			}

			$total_vol_batching_plant = $total_volume;
			$total_vol_truck_mixer = $total_volume;
			$total_vol_wheel_loader = $total_volume;
			$total_vol_bbm_solar = $total_volume;

			$total_pemakaian_vol_batching_plant = $total_vol_batching_plant;
			$total_pemakaian_vol_truck_mixer = $total_vol_truck_mixer;
			$total_pemakaian_vol_wheel_loader = $total_vol_wheel_loader;
			$total_pemakaian_vol_bbm_solar = $total_volume_pemakaian_solar;

			$total_pemakaian_batching_plant = $total_nilai_batching_plant;
			$total_pemakaian_truck_mixer = $total_nilai_truck_mixer + $total_insentif_tm;
			$total_pemakaian_wheel_loader = $total_nilai_wheel_loader;
			$total_pemakaian_bbm_solar = $total_akumulasi_bbm;
			

			?>

			<!-- RAP Alat -->

			<?php

			$rap_alat = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where("rap.tanggal_rap_alat <= '$date2'")
			->where('rap.status','PUBLISH')
			->get()->result_array();

			$total_vol_rap_batching_plant = 0;
			$total_vol_rap_truck_mixer = 0;
			$total_vol_rap_wheel_loader = 0;
			$total_vol_rap_bbm_solar = 0;

			$total_batching_plant = 0;
			$total_truck_mixer = 0;
			$total_wheel_loader = 0;
			$total_bbm_solar = 0;

			foreach ($rap_alat as $x){
				$total_vol_rap_batching_plant += $x['vol_batching_plant'];
				$total_vol_rap_truck_mixer += $x['vol_truck_mixer'];
				$total_vol_rap_wheel_loader += $x['vol_wheel_loader'];
				$total_vol_rap_bbm_solar += $x['vol_bbm_solar'];
				$total_batching_plant += $x['harsat_batching_plant'];
				$total_truck_mixer += $x['harsat_truck_mixer'];
				$total_wheel_loader += $x['harsat_wheel_loader'];
				$total_bbm_solar += $x['harsat_bbm_solar'];
				
			}

			$vol_batching_plant = $total_vol_rap_batching_plant * $total_pemakaian_vol_batching_plant;
			$vol_truck_mixer = $total_vol_rap_truck_mixer * $total_pemakaian_vol_truck_mixer;
			$vol_wheel_loader = $total_vol_rap_wheel_loader * $total_pemakaian_vol_wheel_loader;
			$vol_bbm_solar = $total_vol_rap_bbm_solar * $total_vol_bbm_solar;

			$batching_plant = $total_batching_plant * $vol_batching_plant;
			$truck_mixer = $total_truck_mixer * $vol_truck_mixer;
			$wheel_loader = $total_wheel_loader * $vol_wheel_loader;
			$bbm_solar = $total_bbm_solar * $vol_bbm_solar;

			$harsat_batching_plant = ($vol_batching_plant!=0)?$batching_plant / $vol_batching_plant * 1:0;
			$harsat_truck_mixer = ($vol_truck_mixer!=0)?$truck_mixer / $vol_truck_mixer * 1:0;
			$harsat_wheel_loader = ($wheel_loader!=0)?$wheel_loader / $wheel_loader * 1:0;
			$harsat_bbm_solar = ($vol_bbm_solar!=0)?$bbm_solar / $vol_bbm_solar * 1:0;


			$total_nilai_rap_alat = $batching_plant + $truck_mixer + $wheel_loader + $bbm_solar;
			
			?>

			<!-- Evaluasi -->
			<?php
			$total_vol_evaluasi_batching_plant = ($total_pemakaian_vol_batching_plant!=0)?$vol_batching_plant - $total_pemakaian_vol_batching_plant * 1:0;
			$total_nilai_evaluasi_batching_plant = ($total_pemakaian_batching_plant!=0)?$batching_plant - $total_pemakaian_batching_plant * 1:0;

			$total_vol_evaluasi_truck_mixer = ($total_pemakaian_vol_truck_mixer!=0)?$vol_truck_mixer - $total_pemakaian_vol_truck_mixer * 1:0;
			$total_nilai_evaluasi_truck_mixer = ($total_pemakaian_truck_mixer!=0)?$truck_mixer - $total_pemakaian_truck_mixer * 1:0;

			$total_vol_evaluasi_wheel_loader = ($total_pemakaian_vol_wheel_loader!=0)?$vol_wheel_loader - $total_pemakaian_vol_wheel_loader * 1:0;
			$total_nilai_evaluasi_wheel_loader = ($total_pemakaian_wheel_loader!=0)?$wheel_loader - $total_pemakaian_wheel_loader * 1:0;

			$total_vol_evaluasi_bbm_solar = ($total_pemakaian_vol_bbm_solar!=0)?$vol_bbm_solar - $total_pemakaian_vol_bbm_solar * 1:0;
			$total_nilai_evaluasi_bbm_solar = ($total_pemakaian_bbm_solar!=0)?$bbm_solar - $total_pemakaian_bbm_solar * 1:0;

			$total_nilai_rap_all = $batching_plant + $truck_mixer + $wheel_loader + $bbm_solar;
			$total_nilai_realisasi_all = $total_pemakaian_batching_plant + $total_pemakaian_truck_mixer + $total_pemakaian_wheel_loader + $total_pemakaian_bbm_solar;
			$total_nilai_evaluasi_all = $total_nilai_rap_all - $total_nilai_realisasi_all;
			?>
			
			<tr class="table-active4">
				<th width="5%" class="text-center" rowspan="2" style="vertical-align:middle">NO.</th>
				<th width="20%" class="text-center" rowspan="2" style="vertical-align:middle">URAIAN</th>
				<th width="15%" class="text-center" rowspan="2" style="vertical-align:middle">SATUAN</th>
				<th width="20%" class="text-center" colspan="2">RAP</th>
				<th width="20%" class="text-center" colspan="2">REALISASI</th>
				<th width="20%" class="text-center" colspan="2">EVALUASI</th>
	        </tr>
			<tr class="table-active4">
				<th width="8%" class="text-center">VOLUME</th>
				<th width="12%" class="text-center">NILAI</th>
				<th width="8%" class="text-center">VOLUME</th>
				<th width="12%" class="text-center">NILAI</th>
				<th width="8%" class="text-center">VOLUME</th>
				<th width="12%" class="text-center">NILAI</th>
	        </tr>
			<?php
				$styleColorA = $total_vol_evaluasi_batching_plant < 0 ? 'color:red' : 'color:black';
				$styleColorB = $total_nilai_evaluasi_batching_plant < 0 ? 'color:red' : 'color:black';
				$styleColorC = $total_vol_evaluasi_truck_mixer < 0 ? 'color:red' : 'color:black';
				$styleColorD = $total_nilai_evaluasi_truck_mixer < 0 ? 'color:red' : 'color:black';
				$styleColorE = $total_vol_evaluasi_wheel_loader < 0 ? 'color:red' : 'color:black';
				$styleColorF = $total_nilai_evaluasi_wheel_loader < 0 ? 'color:red' : 'color:black';
				$styleColorG = $total_vol_evaluasi_bbm_solar < 0 ? 'color:red' : 'color:black';
				$styleColorH = $total_nilai_evaluasi_bbm_solar < 0 ? 'color:red' : 'color:black';
				$styleColorI = $total_nilai_evaluasi_all < 0 ? 'color:red' : 'color:black';
			?>
			<tr class="table-active3">
				<th class="text-center">1</th>			
				<th class="text-left">Batching Plant</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($vol_batching_plant,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($batching_plant,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_pemakaian_vol_batching_plant,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_pemakaian_batching_plant,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorA ?>"><?php echo number_format($total_vol_evaluasi_batching_plant,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorB ?>"><?php echo number_format($total_nilai_evaluasi_batching_plant,2,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">2</th>			
				<th class="text-left">Truck Mixer</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($vol_truck_mixer,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($truck_mixer,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_pemakaian_vol_truck_mixer,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_pemakaian_truck_mixer,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorC ?>"><?php echo number_format($total_vol_evaluasi_truck_mixer,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorD ?>"><?php echo number_format($total_nilai_evaluasi_truck_mixer,2,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">3</th>			
				<th class="text-left">Wheel Loader</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($vol_wheel_loader,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($wheel_loader,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_pemakaian_vol_wheel_loader,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_pemakaian_wheel_loader,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorE ?>"><?php echo number_format($total_vol_evaluasi_wheel_loader,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorF ?>"><?php echo number_format($total_nilai_evaluasi_wheel_loader,2,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">4</th>			
				<th class="text-left">BBM Solar</th>
				<th class="text-center">Liter</th>
				<th class="text-right"><?php echo number_format($vol_bbm_solar,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($bbm_solar,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_pemakaian_solar,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_pemakaian_bbm_solar,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorG ?>"><?php echo number_format($total_vol_evaluasi_bbm_solar,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorH ?>"><?php echo number_format($total_nilai_evaluasi_bbm_solar,2,',','.');?></th>
	        </tr>
			<tr class="table-active5">		
				<th class="text-right" colspan="3">Total</th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($total_nilai_rap_all,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($total_nilai_realisasi_all,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right" style="<?php echo $styleColorI ?>"><?php echo number_format($total_nilai_evaluasi_all,0,',','.');?></th>
	        </tr>
	    </table>
		<?php
	}

	public function evaluasi_bua($arr_date)
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
			<!--RAP BUA -->
			<?php


			$rap_gaji_upah = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa in ('199','200')")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_konsumsi = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 201")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_biaya_sewa_mess = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 215")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_listrik_internet = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 206")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_pengujian_material_laboratorium = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 216")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_keamanan_kebersihan = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 151")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_pengobatan = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 121")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_donasi = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 127")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_bensin_tol_parkir = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 129")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_perjalanan_dinas_penjualan = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 113")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_pakaian_dinas = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 138")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_alat_tulis_kantor = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 149")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_perlengkapan_kantor = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 153")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_beban_kirim = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 145")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_beban_lain_lain = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 146")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_biaya_sewa_kendaraan = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 157")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_thr_bonus = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 202")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			$rap_biaya_admin_bank = $this->db->select('rap.*,sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("det.coa = 143")
			->where("rap.tanggal_rap_bua < '$date2'")
			->order_by('rap.tanggal_rap_bua','asc')->limit(1)
			->get()->row_array();

			?>


			<!--PEMAKAIAN BUA -->
			<?php

			$gaji_upah_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in ('199','200')")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$gaji_upah_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in ('199','200')")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$gaji_upah = $gaji_upah_biaya['total'] + $gaji_upah_jurnal['total'];

			$konsumsi_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 201")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$konsumsi_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 201")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$konsumsi = $konsumsi_biaya['total'] + $konsumsi_jurnal['total'];

			$biaya_sewa_mess_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 215")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$biaya_sewa_mess_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 215")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$biaya_sewa_mess = $biaya_sewa_mess_biaya['total'] + $biaya_sewa_mess_jurnal['total'];

			$listrik_internet_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 206")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$listrik_internet_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 206")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$listrik_internet = $listrik_internet_biaya['total'] + $listrik_internet_jurnal['total'];

			$pengujian_material_laboratorium_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 216")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$pengujian_material_laboratorium_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 216")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$pengujian_material_laboratorium = $pengujian_material_laboratorium_biaya['total'] + $pengujian_material_laboratorium_jurnal['total'];

			$keamanan_kebersihan_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 151")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$keamanan_kebersihan_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 151")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$keamanan_kebersihan = $keamanan_kebersihan_biaya['total'] + $keamanan_kebersihan_jurnal['total'];

			$pengobatan_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 121")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$pengobatan_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 121")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$pengobatan = $pengobatan_biaya['total'] + $pengobatan_jurnal['total'];

			$donasi_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 127")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$donasi_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 127")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$donasi = $donasi_biaya['total'] + $donasi_jurnal['total'];

			$bensin_tol_parkir_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 129")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$bensin_tol_parkir_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 129")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$bensin_tol_parkir = $bensin_tol_parkir_biaya['total'] + $bensin_tol_parkir_jurnal['total'];

			$perjalanan_dinas_penjualan_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 113")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$perjalanan_dinas_penjualan_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 113")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$perjalanan_dinas_penjualan = $perjalanan_dinas_penjualan_biaya['total'] + $perjalanan_dinas_penjualan_jurnal['total'];

			$pakaian_dinas_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 138")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$pakaian_dinas_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 138")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$pakaian_dinas = $pakaian_dinas_biaya['total'] + $pakaian_dinas_jurnal['total'];

			$alat_tulis_kantor_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 149")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$alat_tulis_kantor_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 149")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$alat_tulis_kantor = $alat_tulis_kantor_biaya['total'] + $alat_tulis_kantor_jurnal['total'];

			$alat_tulis_kantor_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 149")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$alat_tulis_kantor_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 149")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$alat_tulis_kantor = $alat_tulis_kantor_biaya['total'] + $alat_tulis_kantor_jurnal['total'];

			$perlengkapan_kantor_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 153")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$perlengkapan_kantor_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 153")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$perlengkapan_kantor = $perlengkapan_kantor_biaya['total'] + $perlengkapan_kantor_jurnal['total'];


			$beban_kirim_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 145")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$beban_kirim_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 145")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$beban_kirim = $beban_kirim_biaya['total'] + $beban_kirim_jurnal['total'];

			$beban_lain_lain_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 146")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$beban_lain_lain_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 146")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$beban_lain_lain = $beban_lain_lain_biaya['total'] + $beban_lain_lain_jurnal['total'];

			$biaya_sewa_kendaraan_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 157")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$biaya_sewa_kendaraan_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 157")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$biaya_sewa_kendaraan = $biaya_sewa_kendaraan_biaya['total'] + $biaya_sewa_kendaraan_jurnal['total'];

			$thr_bonus_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 202")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$thr_bonus_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 202")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$thr_bonus = $thr_bonus_biaya['total'] + $thr_bonus_jurnal['total'];

			$biaya_admin_bank_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 143")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$biaya_admin_bank_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 143")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			
			$biaya_admin_bank = $biaya_admin_bank_biaya['total'] + $biaya_admin_bank_jurnal['total'];
			?>

			<?php
			
			$evaluasi_gaji_upah = $rap_gaji_upah['total'] - $gaji_upah;
			$evaluasi_konsumsi = $rap_konsumsi['total'] - $konsumsi;
			$evaluasi_biaya_sewa_mess = $rap_biaya_sewa_mess['total'] - $biaya_sewa_mess;
			$evaluasi_listrik_internet = $rap_listrik_internet['total'] - $listrik_internet;
			$evaluasi_pengujian_material_laboratorium = $rap_pengujian_material_laboratorium['total'] - $pengujian_material_laboratorium;
			$evaluasi_keamanan_kebersihan = $rap_keamanan_kebersihan['total'] - $keamanan_kebersihan;
			$evaluasi_pengobatan = $rap_pengobatan['total'] - $pengobatan;
			$evaluasi_donasi = $rap_donasi['total'] - $donasi;
			$evaluasi_bensin_tol_parkir = $rap_bensin_tol_parkir['total'] - $bensin_tol_parkir;
			$evaluasi_perjalanan_dinas_penjualan = $rap_perjalanan_dinas_penjualan['total'] - $perjalanan_dinas_penjualan;
			$evaluasi_pakaian_dinas = $rap_pakaian_dinas['total'] - $pakaian_dinas;
			$evaluasi_alat_tulis_kantor = $rap_alat_tulis_kantor['total'] -	$alat_tulis_kantor;
			$evaluasi_perlengkapan_kantor = $rap_perlengkapan_kantor['total'] - $perlengkapan_kantor;
			$evaluasi_beban_kirim = $rap_beban_kirim['total'] - $beban_kirim;
			$evaluasi_beban_lain_lain = $rap_beban_lain_lain['total'] - 	$beban_lain_lain;
			$evaluasi_biaya_sewa_kendaraan = $rap_biaya_sewa_kendaraan['total'] - $biaya_sewa_kendaraan;
			$evaluasi_thr_bonus = $rap_thr_bonus['total'] - $thr_bonus;
			$evaluasi_biaya_admin_bank = $rap_biaya_admin_bank['total'] - $biaya_admin_bank;


			$total_rap = $rap_gaji_upah['total'] + $rap_konsumsi['total'] + $rap_biaya_sewa_mess['total'] + $rap_listrik_internet['total'] + $rap_pengujian_material_laboratorium['total'] + $rap_keamanan_kebersihan['total'] + $rap_pengobatan['total'] + $rap_donasi['total'] + $rap_bensin_tol_parkir['total'] + $rap_perjalanan_dinas_penjualan['total'] + $rap_pakaian_dinas['total'] + $rap_alat_tulis_kantor['total'] + $rap_perlengkapan_kantor['total'] + $rap_beban_kirim['total'] + $rap_beban_lain_lain['total'] + $rap_biaya_sewa_kendaraan['total'] + $rap_thr_bonus['total'] + $rap_biaya_admin_bank['total'];
			$total_realisasi = $gaji_upah + $konsumsi + $biaya_sewa_mess + $listrik_internet + $pengujian_material_laboratorium + $keamanan_kebersihan + $pengobatan + $donasi + $bensin_tol_parkir + $perjalanan_dinas_penjualan + $pakaian_dinas + $alat_tulis_kantor + $perlengkapan_kantor + $beban_kirim + $beban_lain_lain + $biaya_sewa_kendaraan + $thr_bonus + $biaya_admin_bank;
			$total_evaluasi = $total_rap - $total_realisasi;

			$volume_rap = $this->db->select('rbd.qty as volume')
			->from('rap_bua rap')
			->from('rap_bua_detail rbd', 'rap.id = rbd.rap_bua_id','left')
			->where("rap.tanggal_rap_bua < '$date2'")
			->get()->row_array();
		
			$volume_rap = round($volume_rap['volume'],2);

			$volume_realisasi = $this->db->select('SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("pp.date_production between '$date1' and '$date2'")
			->where("pp.status = 'PUBLISH'")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.client_id")
			->get()->row_array();

			$volume_realisasi = round($volume_realisasi['volume'],2);

			$volume_evaluasi = $total_evaluasi / 15;

			$total_eveluasi_rap = ($volume_rap!=0)?$total_rap / $volume_rap * 1:0;
			$total_eveluasi_realisasi = ($volume_realisasi!=0)?$total_realisasi / $volume_realisasi * 1:0;
			$total_eveluasi_all = $total_eveluasi_rap - $total_eveluasi_realisasi;

			$evaluasi_1 = $total_eveluasi_rap * $volume_realisasi;
			$evaluasi_2 = $total_realisasi - $evaluasi_1;
			?>
			
			<tr class="table-active4">
				<th width="5%" class="text-center">NO.</th>
				<th width="35%" class="text-center">URAIAN</th>
				<th width="20%" class="text-center">RAP</th>
				<th width="20%" class="text-center">REALISASI</th>
				<th width="20%" class="text-center">SISA ANGGARAN</th>
	        </tr>
			<?php
				$styleColorA = $evaluasi_gaji_upah < 0 ? 'color:red' : 'color:black';
				$styleColorB = $evaluasi_konsumsi < 0 ? 'color:red' : 'color:black';
				$styleColorC = $evaluasi_biaya_sewa_mess < 0 ? 'color:red' : 'color:black';
				$styleColorD = $evaluasi_listrik_internet < 0 ? 'color:red' : 'color:black';
				$styleColorE = $evaluasi_pengujian_material_laboratorium < 0 ? 'color:red' : 'color:black';
				$styleColorF = $evaluasi_keamanan_kebersihan < 0 ? 'color:red' : 'color:black';
				$styleColorG = $evaluasi_pengobatan < 0 ? 'color:red' : 'color:black';
				$styleColorH = $evaluasi_donasi < 0 ? 'color:red' : 'color:black';
				$styleColorI = $evaluasi_bensin_tol_parkir < 0 ? 'color:red' : 'color:black';
				$styleColorJ = $evaluasi_perjalanan_dinas_penjualan < 0 ? 'color:red' : 'color:black';
				$styleColorK = $evaluasi_pakaian_dinas < 0 ? 'color:red' : 'color:black';
				$styleColorL = $evaluasi_alat_tulis_kantor < 0 ? 'color:red' : 'color:black';
				$styleColorM = $evaluasi_perlengkapan_kantor < 0 ? 'color:red' : 'color:black';
				$styleColorN = $evaluasi_beban_kirim < 0 ? 'color:red' : 'color:black';
				$styleColorO = $evaluasi_beban_lain_lain < 0 ? 'color:red' : 'color:black';
				$styleColorP = $evaluasi_biaya_sewa_kendaraan < 0 ? 'color:red' : 'color:black';
				$styleColorQ = $evaluasi_thr_bonus < 0 ? 'color:red' : 'color:black';
				$styleColorR = $evaluasi_biaya_admin_bank < 0 ? 'color:red' : 'color:black';
				$styleColorS = $total_evaluasi < 0 ? 'color:red' : 'color:black';
				$styleColorT = $total_eveluasi_all < 0 ? 'color:red' : 'color:black';
			?>
			<tr class="table-active3">
				<th class="text-center">1</th>			
				<th class="text-left">Gaji / Upah</th>
				<th class="text-right"><?php echo number_format($rap_gaji_upah['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($gaji_upah,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorA ?>"><?php echo number_format($evaluasi_gaji_upah,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">2</th>			
				<th class="text-left">Konsumsi</th>
				<th class="text-right"><?php echo number_format($rap_konsumsi['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($konsumsi,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorB ?>"><?php echo number_format($evaluasi_konsumsi,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">3</th>			
				<th class="text-left">Biaya Sewa - Mess</th>
				<th class="text-right"><?php echo number_format($rap_biaya_sewa_mess['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($biaya_sewa_mess,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorC ?>"><?php echo number_format($evaluasi_biaya_sewa_mess,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">4</th>			
				<th class="text-left">Listrik & Internet</th>
				<th class="text-right"><?php echo number_format($rap_listrik_internet['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($listrik_internet,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorD ?>"><?php echo number_format($evaluasi_listrik_internet,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">5</th>			
				<th class="text-left">Pengujian Material & Laboratorium</th>
				<th class="text-right"><?php echo number_format($rap_pengujian_material_laboratorium['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pengujian_material_laboratorium,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorE ?>"><?php echo number_format($evaluasi_pengujian_material_laboratorium,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">6</th>			
				<th class="text-left">Keamanan & Kebersihan</th>
				<th class="text-right"><?php echo number_format($rap_keamanan_kebersihan['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($keamanan_kebersihan,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorF ?>"><?php echo number_format($evaluasi_keamanan_kebersihan,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">7</th>			
				<th class="text-left">Pengobatan</th>
				<th class="text-right"><?php echo number_format($rap_pengobatan['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pengobatan,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorG ?>"><?php echo number_format($evaluasi_pengobatan,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">8</th>			
				<th class="text-left">Donasi</th>
				<th class="text-right"><?php echo number_format($rap_donasi['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($donasi,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorH ?>"><?php echo number_format($evaluasi_donasi,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">9</th>			
				<th class="text-left">Bensin, Tol dan Parkir - Umum</th>
				<th class="text-right"><?php echo number_format($rap_bensin_tol_parkir['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($bensin_tol_parkir,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorI ?>"><?php echo number_format($evaluasi_bensin_tol_parkir,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">10</th>			
				<th class="text-left">Perjalanan Dinas - Penjualan</th>
				<th class="text-right"><?php echo number_format($rap_perjalanan_dinas_penjualan['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($perjalanan_dinas_penjualan,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorJ ?>"><?php echo number_format($evaluasi_perjalanan_dinas_penjualan,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">11</th>			
				<th class="text-left">Pakaian Dinas & K3</th>
				<th class="text-right"><?php echo number_format($rap_pakaian_dinas['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pakaian_dinas,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorK ?>"><?php echo number_format($evaluasi_pakaian_dinas,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">12</th>			
				<th class="text-left">Alat Tulis Kantor & Printing</th>
				<th class="text-right"><?php echo number_format($rap_alat_tulis_kantor['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($alat_tulis_kantor,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorL ?>"><?php echo number_format($evaluasi_alat_tulis_kantor,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">13</th>			
				<th class="text-left">Perlengkapan Kantor</th>
				<th class="text-right"><?php echo number_format($rap_perlengkapan_kantor['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($perlengkapan_kantor,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorM ?>"><?php echo number_format($evaluasi_perlengkapan_kantor,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">14</th>			
				<th class="text-left">Beban Kirim</th>
				<th class="text-right"><?php echo number_format($rap_beban_kirim['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($beban_kirim,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorN ?>"><?php echo number_format($evaluasi_beban_kirim,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">15</th>			
				<th class="text-left">Beban Lain-Lain</th>
				<th class="text-right"><?php echo number_format($rap_beban_lain_lain['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($beban_lain_lain,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorO ?>"><?php echo number_format($evaluasi_beban_lain_lain,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">16</th>			
				<th class="text-left">Biaya Sewa - Kendaraan</th>
				<th class="text-right"><?php echo number_format($rap_biaya_sewa_kendaraan['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($biaya_sewa_kendaraan,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorP ?>"><?php echo number_format($evaluasi_biaya_sewa_kendaraan,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">17</th>			
				<th class="text-left">THR & Bonus</th>
				<th class="text-right"><?php echo number_format($rap_thr_bonus['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($thr_bonus,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorQ ?>"><?php echo number_format($evaluasi_thr_bonus,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">18</th>			
				<th class="text-left">Biaya Admin Bank</th>
				<th class="text-right"><?php echo number_format($rap_biaya_admin_bank['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($biaya_admin_bank,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorR ?>"><?php echo number_format($evaluasi_biaya_admin_bank,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center" colspan="2">TOTAL</th>
				<th class="text-right"><?php echo number_format($total_rap,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_realisasi,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorS ?>"><?php echo number_format($total_evaluasi,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center" colspan="2">VOLUME (M3)</th>
				<th class="text-right"><?php echo number_format($volume_rap,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_realisasi,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_evaluasi,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center" colspan="2">EVALUASI</th>
				<th class="text-right"><?php echo number_format($total_eveluasi_rap,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_eveluasi_realisasi,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorT ?>"><?php echo number_format($total_eveluasi_all,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center" colspan="2"></th>
				<th class="text-right"><?php echo number_format($evaluasi_1,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($evaluasi_2,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
	    </table>
		<?php
	}

	public function evaluasi_target_produksi($arr_date)
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

			<!-- RAP -->
			<?php
			$date_now = date('Y-m-d');
			$rencana_kerja = $this->db->select('r.*')
			->from('rak r')
			->where("(r.tanggal_rencana_kerja between '$date1' and '$date2')")
			->get()->row_array();

			$volume_rap_produk_a = $rencana_kerja['vol_produk_a'];
			$volume_rap_produk_b = $rencana_kerja['vol_produk_b'];
			$volume_rap_produk_c = $rencana_kerja['vol_produk_c'];
			$volume_rap_produk_d = $rencana_kerja['vol_produk_d'];

			$total_rap_volume = $volume_rap_produk_a + $volume_rap_produk_b + $volume_rap_produk_c + $volume_rap_produk_d;
			
			$harga_jual_125_rap = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("pod.product_id = 2")
			->order_by('po.contract_date','desc')->limit(1)
			->get()->row_array();

			$harga_jual_225_rap = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("pod.product_id = 1")
			->order_by('po.contract_date','desc')->limit(1)
			->get()->row_array();

			$harga_jual_250_rap = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("pod.product_id = 3")
			->order_by('po.contract_date','desc')->limit(1)
			->get()->row_array();

			$harga_jual_250_18_rap = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("pod.product_id = 11")
			->order_by('po.contract_date','desc')->limit(1)
			->get()->row_array();

			$harga_jual_125_now = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("(po.contract_date < '$date_now')")
			->where("pod.product_id = 2")
			->order_by('po.contract_date','desc')->limit(1)
			->get()->row_array();

			$harga_jual_225_now = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("(po.contract_date < '$date_now')")
			->where("pod.product_id = 1")
			->order_by('po.contract_date','desc')->limit(1)
			->get()->row_array();

			$harga_jual_250_now = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("(po.contract_date < '$date_now')")
			->where("pod.product_id = 3")
			->order_by('po.contract_date','desc')->limit(1)
			->get()->row_array();

			$harga_jual_250_18_now = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("(po.contract_date < '$date_now')")
			->where("pod.product_id = 11")
			->order_by('po.contract_date','desc')->limit(1)
			->get()->row_array();

			$nilai_jual_125 = $volume_rap_produk_a * $harga_jual_125_rap['harga_satuan'];
			$nilai_jual_225 = $volume_rap_produk_b * $harga_jual_225_rap['harga_satuan'];
			$nilai_jual_250 = $volume_rap_produk_c * $harga_jual_250_rap['harga_satuan'];
			$nilai_jual_250_18 = $volume_rap_produk_d * $harga_jual_250_18_rap['harga_satuan'];
			$nilai_jual_all = $nilai_jual_125 + $nilai_jual_225 + $nilai_jual_250 + $nilai_jual_250_18;
			
			$total_rap_nilai = $nilai_jual_all;

			//KOMPOSISI RAP
			//K125
			$komposisi_125_rap = $this->db->select('pk.*')
			->from('pmm_agregat pk')
			->where("pk.mutu_beton = 2")
			->where('pk.status','PUBLISH')
			->order_by('pk.date_agregat','desc')->limit(1)
			->get()->row_array();

			$komposisi_125_produk_a_rap = $komposisi_125_rap['presentase_a'];
			$komposisi_125_produk_b_rap = $komposisi_125_rap['presentase_b'];
			$komposisi_125_produk_c_rap = $komposisi_125_rap['presentase_c'];
			$komposisi_125_produk_d_rap = $komposisi_125_rap['presentase_d'];

			//K225
			$komposisi_225_rap = $this->db->select('pk.*')
			->from('pmm_agregat pk')
			->where("pk.mutu_beton = 1")
			->where('pk.status','PUBLISH')
			->order_by('pk.date_agregat','desc')->limit(1)
			->get()->row_array();

			$komposisi_225_produk_a_rap = $komposisi_225_rap['presentase_a'];
			$komposisi_225_produk_b_rap = $komposisi_225_rap['presentase_b'];
			$komposisi_225_produk_c_rap = $komposisi_225_rap['presentase_c'];
			$komposisi_225_produk_d_rap = $komposisi_225_rap['presentase_d'];

			//K250
			$komposisi_250_rap = $this->db->select('pk.*')
			->from('pmm_agregat pk')
			->where("pk.mutu_beton = 3")
			->where('pk.status','PUBLISH')
			->order_by('pk.date_agregat','desc')->limit(1)
			->get()->row_array();

			$komposisi_250_produk_a_rap = $komposisi_250_rap['presentase_a'];
			$komposisi_250_produk_b_rap = $komposisi_250_rap['presentase_b'];
			$komposisi_250_produk_c_rap = $komposisi_250_rap['presentase_c'];
			$komposisi_250_produk_d_rap = $komposisi_250_rap['presentase_d'];

			//K250_18
			$komposisi_250_18_rap = $this->db->select('pk.*')
			->from('pmm_agregat pk')
			->where("pk.mutu_beton = 11")
			->where('pk.status','PUBLISH')
			->order_by('pk.date_agregat','desc')->limit(1)
			->get()->row_array();

			$komposisi_250_18_produk_a_rap = $komposisi_250_18_rap['presentase_a'];
			$komposisi_250_18_produk_b_rap = $komposisi_250_18_rap['presentase_b'];
			$komposisi_250_18_produk_c_rap = $komposisi_250_18_rap['presentase_c'];
			$komposisi_250_18_produk_d_rap = $komposisi_250_18_rap['presentase_d'];

			//KOMPOSISI
			//K125
			$komposisi_125 = $this->db->select('pk.*')
			->from('pmm_agregat pk')
			->where("pk.mutu_beton = 2")
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
			->order_by('pk.date_agregat','desc')->limit(1)
			->get()->row_array();

			$komposisi_250_18_produk_a = $komposisi_250_18['presentase_a'];
			$komposisi_250_18_produk_b = $komposisi_250_18['presentase_b'];
			$komposisi_250_18_produk_c = $komposisi_250_18['presentase_c'];
			$komposisi_250_18_produk_d = $komposisi_250_18['presentase_d'];

			//TOTAL PEMAKAIAN BAHAN RAP
			//TOTAL K-125
			$total_semen_125_rap = $komposisi_125_produk_a_rap * $volume_rap_produk_a;
			$total_pasir_125_rap = $komposisi_125_produk_b_rap * $volume_rap_produk_a;
			$total_batu1020_125_rap = $komposisi_125_produk_c_rap * $volume_rap_produk_a;
			$total_batu2030_125_rap = $komposisi_125_produk_d_rap * $volume_rap_produk_a;

			$nilai_semen_125_rap = $total_semen_125_rap * $komposisi_125_rap['price_a'];
			$nilai_pasir_125_rap = $total_pasir_125_rap * $komposisi_125_rap['price_b'];
			$nilai_batu1020_125_rap = $total_batu1020_125_rap * $komposisi_125_rap['price_c'];
			$nilai_batu2030_125_rap = $total_batu2030_125_rap * $komposisi_125_rap['price_d'];

			$total_125_rap = $nilai_semen_125_rap + $nilai_pasir_125_rap + $nilai_batu1020_125_rap + $nilai_batu2030_125_rap;

			//TOTAL K-225
			$total_semen_225_rap = $komposisi_225_produk_a_rap * $volume_rap_produk_b;
			$total_pasir_225_rap = $komposisi_225_produk_b_rap * $volume_rap_produk_b;
			$total_batu1020_225_rap = $komposisi_225_produk_c_rap * $volume_rap_produk_b;
			$total_batu2030_225_rap = $komposisi_225_produk_d_rap * $volume_rap_produk_b;

			$nilai_semen_225_rap = $total_semen_225_rap * $komposisi_225_rap['price_a'];
			$nilai_pasir_225_rap = $total_pasir_225_rap * $komposisi_225_rap['price_b'];
			$nilai_batu1020_225_rap = $total_batu1020_225_rap * $komposisi_225_rap['price_c'];
			$nilai_batu2030_225_rap = $total_batu2030_225_rap * $komposisi_225_rap['price_d'];

			$total_225_rap = $nilai_semen_225_rap + $nilai_pasir_225_rap + $nilai_batu1020_225_rap + $nilai_batu2030_225_rap;

			//TOTAL K-250
			$total_semen_250_rap = $komposisi_250_produk_a_rap * $volume_rap_produk_c;
			$total_pasir_250_rap = $komposisi_250_produk_b_rap * $volume_rap_produk_c;
			$total_batu1020_250_rap = $komposisi_250_produk_c_rap * $volume_rap_produk_c;
			$total_batu2030_250_rap = $komposisi_250_produk_d_rap * $volume_rap_produk_c;

			$nilai_semen_250_rap = $total_semen_250_rap * $komposisi_250_rap['price_a'];
			$nilai_pasir_250_rap = $total_pasir_250_rap * $komposisi_250_rap['price_b'];
			$nilai_batu1020_250_rap = $total_batu1020_250_rap * $komposisi_250_rap['price_c'];
			$nilai_batu2030_250_rap = $total_batu2030_250_rap * $komposisi_250_rap['price_d'];

			$total_250_rap = $nilai_semen_250_rap + $nilai_pasir_250_rap + $nilai_batu1020_250_rap + $nilai_batu2030_250_rap;

			//TOTAL K-250_18
			$total_semen_250_18_rap = $komposisi_250_18_produk_a_rap * $volume_rap_produk_d;
			$total_pasir_250_18_rap = $komposisi_250_18_produk_b_rap * $volume_rap_produk_d;
			$total_batu1020_250_18_rap = $komposisi_250_18_produk_c_rap * $volume_rap_produk_d;
			$total_batu2030_250_18_rap = $komposisi_250_18_produk_d_rap * $volume_rap_produk_d;

			$nilai_semen_250_18_rap = $total_semen_250_18_rap * $komposisi_250_18_rap['price_a'];
			$nilai_pasir_250_18_rap = $total_pasir_250_18_rap * $komposisi_250_18_rap['price_b'];
			$nilai_batu1020_250_18_rap = $total_batu1020_250_18_rap * $komposisi_250_18_rap['price_c'];
			$nilai_batu2030_250_18_rap = $total_batu2030_250_18_rap * $komposisi_250_18_rap['price_d'];

			$total_250_18_rap = $nilai_semen_250_18_rap + $nilai_pasir_250_18_rap + $nilai_batu1020_250_18_rap + $nilai_batu2030_250_18_rap;

			//TOTAL ALL
			$total_bahan_all_rap = $total_125_rap + $total_225_rap + $total_250_rap + $total_250_18_rap;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_rap = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->get()->row_array();

			$batching_plant_rap = $total_rap_volume * $rap_alat_rap['batching_plant'];
			$truck_mixer_rap = $total_rap_volume * $rap_alat_rap['truck_mixer'];
			$wheel_loader_rap = $total_rap_volume * $rap_alat_rap['wheel_loader'];
			$bbm_solar_rap = $total_rap_volume * $rap_alat_rap['bbm_solar'];
			$biaya_alat_all_rap = $batching_plant_rap + $truck_mixer_rap + $wheel_loader_rap + $bbm_solar_rap;
		
			$total_rap_biaya_bahan = $total_bahan_all_rap;
			$total_rap_biaya_alat = $biaya_alat_all_rap;
			$total_rap_biaya_overhead = $rencana_kerja['biaya_overhead'];
			$total_rap_biaya_bank = $rencana_kerja['biaya_bank'];
			$total_rap_biaya_persiapan = $rencana_kerja['biaya_persiapan'];

			$total_biaya_rap_biaya = $total_rap_biaya_bahan + $total_rap_biaya_alat + $total_rap_biaya_overhead + $total_rap_biaya_bank + $total_rap_biaya_persiapan;

			?>
			<!-- RAP 2022 -->
			
			<!-- REALISASI -->
			<?php
			$penjualan_realisasi_produk_a = $this->db->select('p.nama_produk, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('produk p', 'pp.product_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("(pp.date_production between '$date1' and '$date2')")
			->where("pp.status = 'PUBLISH'")
			->where("pp.product_id = 2")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.product_id")
			->order_by('p.nama_produk','asc')
			->get()->row_array();
			$volume_realisasi_produk_a = $penjualan_realisasi_produk_a['volume'];
			$nilai_realisasi_produk_a = $penjualan_realisasi_produk_a['price'];

			$penjualan_realisasi_produk_b = $this->db->select('p.nama_produk, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('produk p', 'pp.product_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("(pp.date_production between '$date1' and '$date2')")
			->where("pp.status = 'PUBLISH'")
			->where("pp.product_id = 1")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.product_id")
			->order_by('p.nama_produk','asc')
			->get()->row_array();
			$volume_realisasi_produk_b = $penjualan_realisasi_produk_b['volume'];
			$nilai_realisasi_produk_b = $penjualan_realisasi_produk_b['price'];

			$penjualan_realisasi_produk_c = $this->db->select('p.nama_produk, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('produk p', 'pp.product_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("(pp.date_production between '$date1' and '$date2')")
			->where("pp.status = 'PUBLISH'")
			->where("pp.product_id = 3")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.product_id")
			->order_by('p.nama_produk','asc')
			->get()->row_array();
			$volume_realisasi_produk_c = $penjualan_realisasi_produk_c['volume'];
			$nilai_realisasi_produk_c = $penjualan_realisasi_produk_c['price'];

			$penjualan_realisasi_produk_d = $this->db->select('p.nama_produk, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('produk p', 'pp.product_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("(pp.date_production between '$date1' and '$date2')")
			->where("pp.status = 'PUBLISH'")
			->where("pp.product_id = 11")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.product_id")
			->order_by('p.nama_produk','asc')
			->get()->row_array();
			$volume_realisasi_produk_d = $penjualan_realisasi_produk_d['volume'];
			$nilai_realisasi_produk_d = $penjualan_realisasi_produk_d['price'];

			$total_realisasi_volume = $volume_realisasi_produk_a + $volume_realisasi_produk_b + $volume_realisasi_produk_c + $volume_realisasi_produk_d;
			$total_realisasi_nilai = $nilai_realisasi_produk_a + $nilai_realisasi_produk_b + $nilai_realisasi_produk_c + $nilai_realisasi_produk_d;
			?>
			<!-- REALISASI SD. SAAT INI -->

		

			<!-- REALISASI BIAYA -->
			<?php
			//BAHAN
			$akumulasi = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar as total_nilai_keluar')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi between '$date1' and '$date2')")
			->get()->result_array();

			$total_akumulasi = 0;

			foreach ($akumulasi as $a){
				$total_akumulasi += $a['total_nilai_keluar'];
			}

			$total_bahan_akumulasi = $total_akumulasi;
			//END BAHAN
			?>

			<?php
			//ALAT
			$nilai_alat = $this->db->select('SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("(prm.date_receipt between '$date1' and '$date2')")
			->where("p.kategori_produk = '5'")
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

			$total_alat_realisasi = $nilai_alat['nilai'] + $total_akumulasi_bbm + $total_insentif_tm;
			//END ALAT
			?>

			<?php
			//OVERHEAD
			$overhead_15_realisasi = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',15)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$overhead_jurnal_15_realisasi = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',15)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$overhead_16_realisasi = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',16)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$overhead_jurnal_16_realisasi = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',16)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$overhead_17_realisasi = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',17)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$overhead_jurnal_17_realisasi = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',17)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$total_overhead_realisasi =  $overhead_15_realisasi['total'] + $overhead_jurnal_15_realisasi['total'] + $overhead_16_realisasi['total'] + $overhead_jurnal_16_realisasi['total'] + $overhead_17_realisasi['total'] + $overhead_jurnal_17_realisasi['total'];
			?>

			<?php
			//DISKONTO
			$diskonto_realisasi = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 168")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$total_diskonto_realisasi = $diskonto_realisasi['total'];
			//DISKONTO
			?>

			<?php
			//PERSIAPAN
			$persiapan_biaya_realisasi = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 228")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$persiapan_jurnal_realisasi = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 228")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$total_persiapan_realisasi = $persiapan_biaya_realisasi['total'] + $persiapan_jurnal_realisasi['total'];
			$total_biaya_realisasi = $total_bahan_akumulasi + $total_alat_realisasi + $total_overhead_realisasi + $total_diskonto_realisasi + $total_persiapan_realisasi;
			//END PERSIAPAN
			?>
			<!-- REALISASI BIAYA -->

			<!-- SISA -->
			<?php
			$sisa_volume_produk_a = $volume_rap_produk_a - $volume_realisasi_produk_a;
			$sisa_volume_produk_b = $volume_rap_produk_b - $volume_realisasi_produk_b;
			$sisa_volume_produk_c = $volume_rap_produk_c - $volume_realisasi_produk_c;
			$sisa_volume_produk_d = $volume_rap_produk_d - $volume_realisasi_produk_d;

			$total_sisa_volume_all_produk = $sisa_volume_produk_a + $sisa_volume_produk_b + $sisa_volume_produk_c + $sisa_volume_produk_d;
			$total_sisa_nilai_all_produk = $total_rap_nilai - $total_realisasi_nilai;

			$sisa_biaya_bahan = $total_rap_biaya_bahan - $total_bahan_akumulasi;
			$sisa_biaya_alat = $total_rap_biaya_alat - $total_alat_realisasi;
			$sisa_biaya_overhead = $total_rap_biaya_overhead - $total_overhead_realisasi;
			$sisa_biaya_bank = $total_rap_biaya_bank - $total_diskonto_realisasi;
			$sisa_biaya_persiapan = $total_rap_biaya_persiapan - $total_persiapan_realisasi;
			?>
			<!-- SISA -->

			<!-- TOTAL -->
			<?php
			$total_laba_rap = $total_rap_nilai - $total_biaya_rap_biaya;
			$total_laba_realisasi = $total_realisasi_nilai - $total_biaya_realisasi;

			$sisa_biaya_realisasi = $total_biaya_rap_biaya - $total_biaya_realisasi;
			$sisa_laba = $total_laba_rap - $total_laba_realisasi;

			?>
			<!-- TOTAL -->

			<tr class="table-active4">
				<th width="5%" class="text-center">NO.</th>
				<th class="text-center">URAIAN</th>
				<th class="text-center">SATUAN</th>
				<th class="text-center">RAP</th>
				<th class="text-center">REALISASI</th>
				<th class="text-center">EVALUASI</th>
	        </tr>
			<tr class="table-active2">
				<th class="text-left" colspan="6">RENCANA PRODUKSI & PENDAPATAN USAHA</th>
			</tr>
			<tr class="table-active3">
				<th class="text-center">1</th>
				<th class="text-left">Beton K 125 (10±2)</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_rap_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_realisasi_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_volume_produk_a,2,',','.');?></th>
			</tr>
			<tr class="table-active3">
				<th class="text-center">2</th>
				<th class="text-left">Beton K 225 (10±2)</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_rap_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_realisasi_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_volume_produk_b,2,',','.');?></th>
			</tr>
			<tr class="table-active3">
				<th class="text-center">3</th>
				<th class="text-left">Beton K 250 (10±2)</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_rap_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_realisasi_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_volume_produk_c,2,',','.');?></th>
			</tr>
			<tr class="table-active3">
				<th class="text-center">4</th>
				<th class="text-left">Beton K 250 (18±2)</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_rap_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_realisasi_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_volume_produk_d,2,',','.');?></th>
			</tr>
			<tr class="table-active2">
				<th class="text-right" colspan="2">TOTAL VOLUME</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($total_rap_volume,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_realisasi_volume,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_sisa_volume_all_produk,2,',','.');?></th>
			</tr>
			<tr class="table-active2">
				<th class="text-right" colspan="2">PENDAPATAN USAHA</th>
				<th class="text-center"></th>
				<th class="text-right"><?php echo number_format($total_rap_nilai,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_realisasi_nilai,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_sisa_nilai_all_produk,0,',','.');?></th>
			</tr>
			<tr class="table-active2">
				<th class="text-left" colspan="6">BIAYA</th>
			</tr>
			<tr class="table-active3">
				<th class="text-center">1</th>
				<th class="text-left">Bahan</th>
				<th class="text-center">LS</th>
				<th class="text-right"><?php echo number_format($total_rap_biaya_bahan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_bahan_akumulasi,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_biaya_bahan,0,',','.');?></th>
			</tr>
			<tr class="table-active3">
				<th class="text-center">2</th>
				<th class="text-left">Alat</th>
				<th class="text-center">LS</th>
				<th class="text-right"><?php echo number_format($total_rap_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_alat_realisasi,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_biaya_alat,0,',','.');?></th>
			</tr>
			<tr class="table-active3">
				<th class="text-center">3</th>
				<th class="text-left">Overhead</th>
				<th class="text-center">LS</th>
				<th class="text-right"><?php echo number_format($total_rap_biaya_overhead,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_overhead_realisasi,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_biaya_overhead,0,',','.');?></th>
			</tr>
			<tr class="table-active3">
				<th class="text-center">4</th>
				<th class="text-left">Biaya Bank</th>
				<th class="text-center">LS</th>
				<th class="text-right"><?php echo number_format($total_rap_biaya_bank,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_diskonto_realisasi,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_biaya_bank,0,',','.');?></th>
			</tr>
			<tr class="table-active3">
				<th class="text-center">5</th>
				<th class="text-left">Persiapan</th>
				<th class="text-center">LS</th>
				<th class="text-right"><?php echo number_format($total_rap_biaya_persiapan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_persiapan_realisasi,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_biaya_persiapan,0,',','.');?></th>
			</tr>
			<tr class="table-active2">
				<th class="text-right" colspan="2">JUMLAH</th>
				<th class="text-center"></th>
				<th class="text-right"><?php echo number_format($total_biaya_rap_biaya,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_biaya_realisasi,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_biaya_realisasi,0,',','.');?></th>
			</tr>
			<tr class="table-active2">
				<th class="text-right" colspan="2">LABA</th>
				<th class="text-center"></th>
				<th class="text-right"><?php echo number_format($total_laba_rap,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_laba_realisasi,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_laba,0,',','.');?></th>
			</tr>	
	    </table>
		<?php
	}

	public function biaya_bahan($arr_date)
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
	            <th class="text-center" colspan="3"><?php echo $filter_date;?></th>
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
			$total_harga_pembelian_semen_akhir = ($total_volume_pembelian_semen_akhir!=0)?($nilai_opening_balance_semen + $total_nilai_pembelian_semen) / $total_volume_pembelian_semen_akhir * 1:0;
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
			$total_harga_pembelian_semen_cons_akhir = ($total_volume_pembelian_semen_cons_akhir!=0)?($total_nilai_pembelian_semen_akhir + $total_nilai_pembelian_semen_cons) / $total_volume_pembelian_semen_cons_akhir * 1:0;
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
			$total_harga_pembelian_semen_opc_akhir = ($total_volume_pembelian_semen_opc_akhir!=0)?($total_nilai_pembelian_semen_cons_akhir + $total_nilai_pembelian_semen_opc) / $total_volume_pembelian_semen_opc_akhir * 1:0;
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
			$total_harga_pemakaian_semen = ($total_volume_pemakaian_semen!=0)?$total_nilai_pemakaian_semen / $total_volume_pemakaian_semen * 1:0;

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
			$total_harga_pembelian_pasir_akhir = ($total_volume_pembelian_pasir_akhir!=0)?($nilai_opening_balance_pasir + $total_nilai_pembelian_pasir) / $total_volume_pembelian_pasir_akhir * 1:0;
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
				<th width="5%" class="text-center" rowspan="2" style="vertical-align:middle">NO.</th>
				<th width="40%" class="text-center" rowspan="2" style="vertical-align:middle">URAIAN</th>
				<th width="15%" class="text-center" rowspan="2" style="vertical-align:middle">SATUAN</th>
				<th width="40%" class="text-center" colspan="3">PEMAKAIAN</th>
	        </tr>
			<tr class="table-active4">
				<th width="10% class="text-center">VOLUME</th>
				<th width="10% class="text-center">HARGA</th>
				<th width="20% class="text-center">NILAI</th>
	        </tr>
			<tr class="table-active2">
	            <th class="text-left" colspan="12">BAHAN BAKU</th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">1</th>
				<th class="text-left"><i>Semen</i></th>
				<th class="text-center">Ton</th>
				<th class="text-center"><?php echo number_format($total_volume_pemakaian_semen,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pemakaian_semen,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pemakaian_semen,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">2</th>
				<th class="text-left"><i>Pasir</i></th>
				<th class="text-center">M3</th>
				<th class="text-center"><?php echo number_format($total_volume_pemakaian_pasir,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pemakaian_pasir,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pemakaian_pasir,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">3</th>
				<th class="text-left"><i>Batu Split 10-20</i></th>
				<th class="text-center">M3</th>
				<th class="text-center"><?php echo number_format($total_volume_pemakaian_batu1020,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pemakaian_batu1020,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pemakaian_batu1020,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">4</th>
				<th class="text-left"><i>Batu Split 20-30</i></th>
				<th class="text-center">M3</th>
				<th class="text-center"><?php echo number_format($total_volume_pemakaian_batu2030,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pemakaian_batu2030,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pemakaian_batu2030,0,',','.');?></th>
	        </tr>
			<tr class="table-active5">
	            <th class="text-center" colspan="3">TOTAL</th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($total_nilai_pemakaian,0,',','.');?></th>
	        </tr>
	    </table>
		<?php
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
			$total_harga_pembelian_semen_akhir = ($total_volume_pembelian_semen_akhir!=0)?($nilai_opening_balance_semen + $total_nilai_pembelian_semen) / $total_volume_pembelian_semen_akhir * 1:0;
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
			$total_harga_pembelian_semen_cons_akhir = ($total_volume_pembelian_semen_cons_akhir!=0)?($total_nilai_pembelian_semen_akhir + $total_nilai_pembelian_semen_cons) / $total_volume_pembelian_semen_cons_akhir * 1:0;
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
			$total_harga_pembelian_semen_opc_akhir = ($total_volume_pembelian_semen_opc_akhir!=0)?($total_nilai_pembelian_semen_cons_akhir + $total_nilai_pembelian_semen_opc) / $total_volume_pembelian_semen_opc_akhir * 1:0;
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
			$total_harga_pemakaian_semen = ($total_volume_pemakaian_semen!=0)?$total_nilai_pemakaian_semen / $total_volume_pemakaian_semen * 1:0;

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
			$total_harga_pembelian_pasir_akhir = ($total_volume_pembelian_pasir_akhir!=0)?($nilai_opening_balance_pasir + $total_nilai_pembelian_pasir) / $total_volume_pembelian_pasir_akhir * 1:0;
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
	            <th class="text-center" style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
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
	            <th class="text-center" style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
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
	            <th class="text-center" style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
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
	            <th class="text-center" style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
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
	            <th class="text-center" style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
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
	            <th class="text-center" style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
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
				<th class="text-center" style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>			
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
	            <th class="text-center" style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
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
			<th class="text-center" style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>			
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
	            <th class="text-center" style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
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
				<th class="text-center" style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>			
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
	            <th class="text-center" style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
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
				<th class="text-center" style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>			
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
				<th class="text-center" style="vertical-align:middle"><?php echo $date2_ago;?></th>			
				<th class="text-left" colspan="10"><i>Opening Balance</i></th>
				<th class="text-right"><?php echo number_format($total_opening_balance_bahan_baku,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center" style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>		
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
				<th class="text-center" style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
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
				<th class="text-center" style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
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
				<th class="text-center" style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
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

	public function biaya_alat($arr_date)
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
			->join('produk p', 'prm.material_id = p.id','left')
			->join('penerima pn', 'po.supplier_id = pn.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("p.kategori_produk = '5'")
			->where("po.status in ('PUBLISH','CLOSED')")
			->group_by('prm.harga_satuan')
			->order_by('pn.nama','asc')
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
				<th width="55%" class="text-center">URAIAN</th>
				<th width="7%" class="text-center">SATUAN</th>
				<th width="13%" class="text-center">VOLUME</th>
				<th width="15%" class="text-center">HARGA SATUAN</th>
				<th width="10%" class="text-center">NILAI</th>
	        </tr>
			<?php foreach ($pembelian as $x): ?>
			<tr>
				<th class="text-left"><?= $x['subject'] ?></th>
				<th class="text-center"><?= $x['measure'] ?></th>
				<th class="text-right"><?php echo number_format($x['volume'],2,',','.');?></th>
				<th class="text-right"><?php echo number_format($x['harga_satuan'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($x['price'],0,',','.');?></th>
			</tr>
			<?php endforeach; ?>
			<tr>
				<th class="text-left">&bull; BBM Solar</th>
				<th class="text-center"></th>
				<th class="text-right"></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($total_nilai_bbm,0,',','.');?></th>
			</tr>
			<?php foreach ($insentif_tm as $y): ?>
			<tr>
				<th class="text-left" colspan="4">&bull; <?= $y['memo'] ?></th>
				<th class="text-right"><?php echo number_format($y['total'],0,',','.');?></th>
			</tr>
			<?php endforeach; ?>
			<tr class="table-active2">
				<th class="text-center" colspan="4">TOTAL</th>
				<th class="text-right"><?php echo number_format($total_nilai,0,',','.');?></th>
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
	            <th class="text-center" style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
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
				<th class="text-center" style="vertical-align:middle"><?php echo $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>			
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

	public function laporan_rencana_kerja($arr_date)
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
				table tr.table-active-rak{
					background-color: #F0F0F0;
					font-size: 8px;
					font-weight: bold;
					color: black;
				}
					
				table tr.table-active2-rak{
					background-color: #E8E8E8;
					font-size: 8px;
					font-weight: bold;
				}
					
				table tr.table-active3-rak{
					font-size: 8px;
					background-color: #F0F0F0;
				}
					
				table tr.table-active4-rak{
					background-color: #e69500;
					font-weight: bold;
					font-size: 8px;
					color: black;
				}
				table tr.table-active5-rak{
					background-color: #E8E8E8;
					text-decoration: underline;
					font-size: 8px;
					font-weight: bold;
					color: red;
				}
				table tr.table-activeago1-rak{
					background-color: #ffd966;
					font-weight: bold;
					font-size: 8px;
					color: black;
				}
				table tr.table-activeopening-rak{
					background-color: #2986cc;
					font-weight: bold;
					font-size: 8px;
					color: black;
				}
			</style>

			<!-- RAP 2022 -->
			<?php
			$date_now = date('Y-m-d');
			$date_end = date('2022-12-31');
			$rencana_kerja_2022 = $this->db->select('r.*')
			->from('rak r')
			->order_by('r.tanggal_rencana_kerja','asc')->limit(1)
			->get()->row_array();

			$volume_rap_2022_produk_a = $rencana_kerja_2022['vol_produk_a'];
			$volume_rap_2022_produk_b = $rencana_kerja_2022['vol_produk_b'];
			$volume_rap_2022_produk_c = $rencana_kerja_2022['vol_produk_c'];
			$volume_rap_2022_produk_d = $rencana_kerja_2022['vol_produk_d'];

			$total_rap_volume_2022 = $volume_rap_2022_produk_a + $volume_rap_2022_produk_b + $volume_rap_2022_produk_c + $volume_rap_2022_produk_d;
			
			$harga_jual_125_rap = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("pod.product_id = 2")
			->order_by('po.contract_date','asc')->limit(1)
			->get()->row_array();

			$harga_jual_225_rap = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("pod.product_id = 1")
			->order_by('po.contract_date','asc')->limit(1)
			->get()->row_array();

			$harga_jual_250_rap = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("pod.product_id = 3")
			->order_by('po.contract_date','asc')->limit(1)
			->get()->row_array();

			$harga_jual_250_18_rap = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("pod.product_id = 11")
			->order_by('po.contract_date','asc')->limit(1)
			->get()->row_array();

			$harga_jual_125_now = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("(po.contract_date < '$date_now')")
			->where("pod.product_id = 2")
			->order_by('po.contract_date','desc')->limit(1)
			->get()->row_array();

			$harga_jual_225_now = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("(po.contract_date < '$date_now')")
			->where("pod.product_id = 1")
			->order_by('po.contract_date','desc')->limit(1)
			->get()->row_array();

			$harga_jual_250_now = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("(po.contract_date < '$date_now')")
			->where("pod.product_id = 3")
			->order_by('po.contract_date','desc')->limit(1)
			->get()->row_array();

			$harga_jual_250_18_now = $this->db->select('pod.price as harga_satuan')
			->from('pmm_sales_po po')
			->join('pmm_sales_po_detail pod', 'po.id = pod.sales_po_id','left')
			->where("(po.contract_date < '$date_now')")
			->where("pod.product_id = 11")
			->order_by('po.contract_date','desc')->limit(1)
			->get()->row_array();

			$nilai_jual_125_2022 = $volume_rap_2022_produk_a * $harga_jual_125_rap['harga_satuan'];
			$nilai_jual_225_2022 = $volume_rap_2022_produk_b * $harga_jual_225_rap['harga_satuan'];
			$nilai_jual_250_2022 = $volume_rap_2022_produk_c * $harga_jual_250_rap['harga_satuan'];
			$nilai_jual_250_18_2022 = $volume_rap_2022_produk_d * $harga_jual_250_18_rap['harga_satuan'];
			$nilai_jual_all_2022 = $nilai_jual_125_2022 + $nilai_jual_225_2022 + $nilai_jual_250_2022 + $nilai_jual_250_18_2022;
			
			$total_rap_nilai_2022 = $nilai_jual_all_2022;

			//KOMPOSISI RAP 2022
			//K125
			$komposisi_125_rap = $this->db->select('pk.*')
			->from('pmm_agregat pk')
			->where("pk.mutu_beton = 2")
			->where('pk.status','PUBLISH')
			->order_by('pk.date_agregat','asc')->limit(1)
			->get()->row_array();

			$komposisi_125_produk_a_rap = $komposisi_125_rap['presentase_a'];
			$komposisi_125_produk_b_rap = $komposisi_125_rap['presentase_b'];
			$komposisi_125_produk_c_rap = $komposisi_125_rap['presentase_c'];
			$komposisi_125_produk_d_rap = $komposisi_125_rap['presentase_d'];

			//K225
			$komposisi_225_rap = $this->db->select('pk.*')
			->from('pmm_agregat pk')
			->where("pk.mutu_beton = 1")
			->where('pk.status','PUBLISH')
			->order_by('pk.date_agregat','asc')->limit(1)
			->get()->row_array();

			$komposisi_225_produk_a_rap = $komposisi_225_rap['presentase_a'];
			$komposisi_225_produk_b_rap = $komposisi_225_rap['presentase_b'];
			$komposisi_225_produk_c_rap = $komposisi_225_rap['presentase_c'];
			$komposisi_225_produk_d_rap = $komposisi_225_rap['presentase_d'];

			//K250
			$komposisi_250_rap = $this->db->select('pk.*')
			->from('pmm_agregat pk')
			->where("pk.mutu_beton = 3")
			->where('pk.status','PUBLISH')
			->order_by('pk.date_agregat','asc')->limit(1)
			->get()->row_array();

			$komposisi_250_produk_a_rap = $komposisi_250_rap['presentase_a'];
			$komposisi_250_produk_b_rap = $komposisi_250_rap['presentase_b'];
			$komposisi_250_produk_c_rap = $komposisi_250_rap['presentase_c'];
			$komposisi_250_produk_d_rap = $komposisi_250_rap['presentase_d'];

			//K250_18
			$komposisi_250_18_rap = $this->db->select('pk.*')
			->from('pmm_agregat pk')
			->where("pk.mutu_beton = 11")
			->where('pk.status','PUBLISH')
			->order_by('pk.date_agregat','asc')->limit(1)
			->get()->row_array();

			$komposisi_250_18_produk_a_rap = $komposisi_250_18_rap['presentase_a'];
			$komposisi_250_18_produk_b_rap = $komposisi_250_18_rap['presentase_b'];
			$komposisi_250_18_produk_c_rap = $komposisi_250_18_rap['presentase_c'];
			$komposisi_250_18_produk_d_rap = $komposisi_250_18_rap['presentase_d'];

			//KOMPOSISI
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

			//TOTAL PEMAKAIAN BAHAN RAP 2022
			//TOTAL K-125
			$total_semen_125_rap_2022 = $komposisi_125_produk_a_rap * $volume_rap_2022_produk_a;
			$total_pasir_125_rap_2022 = $komposisi_125_produk_b_rap * $volume_rap_2022_produk_a;
			$total_batu1020_125_rap_2022 = $komposisi_125_produk_c_rap * $volume_rap_2022_produk_a;
			$total_batu2030_125_rap_2022 = $komposisi_125_produk_d_rap * $volume_rap_2022_produk_a;

			$nilai_semen_125_rap_2022 = $total_semen_125_rap_2022 * $komposisi_125_rap['price_a'];
			$nilai_pasir_125_rap_2022 = $total_pasir_125_rap_2022 * $komposisi_125_rap['price_b'];
			$nilai_batu1020_125_rap_2022 = $total_batu1020_125_rap_2022 * $komposisi_125_rap['price_c'];
			$nilai_batu2030_125_rap_2022 = $total_batu2030_125_rap_2022 * $komposisi_125_rap['price_d'];

			$total_125_rap_2022 = $nilai_semen_125_rap_2022 + $nilai_pasir_125_rap_2022 + $nilai_batu1020_125_rap_2022 + $nilai_batu2030_125_rap_2022;

			//TOTAL K-225
			$total_semen_225_rap_2022 = $komposisi_225_produk_a_rap * $volume_rap_2022_produk_b;
			$total_pasir_225_rap_2022 = $komposisi_225_produk_b_rap * $volume_rap_2022_produk_b;
			$total_batu1020_225_rap_2022 = $komposisi_225_produk_c_rap * $volume_rap_2022_produk_b;
			$total_batu2030_225_rap_2022 = $komposisi_225_produk_d_rap * $volume_rap_2022_produk_b;

			$nilai_semen_225_rap_2022 = $total_semen_225_rap_2022 * $komposisi_225_rap['price_a'];
			$nilai_pasir_225_rap_2022 = $total_pasir_225_rap_2022 * $komposisi_225_rap['price_b'];
			$nilai_batu1020_225_rap_2022 = $total_batu1020_225_rap_2022 * $komposisi_225_rap['price_c'];
			$nilai_batu2030_225_rap_2022 = $total_batu2030_225_rap_2022 * $komposisi_225_rap['price_d'];

			$total_225_rap_2022 = $nilai_semen_225_rap_2022 + $nilai_pasir_225_rap_2022 + $nilai_batu1020_225_rap_2022 + $nilai_batu2030_225_rap_2022;

			//TOTAL K-250
			$total_semen_250_rap_2022 = $komposisi_250_produk_a_rap * $volume_rap_2022_produk_c;
			$total_pasir_250_rap_2022 = $komposisi_250_produk_b_rap * $volume_rap_2022_produk_c;
			$total_batu1020_250_rap_2022 = $komposisi_250_produk_c_rap * $volume_rap_2022_produk_c;
			$total_batu2030_250_rap_2022 = $komposisi_250_produk_d_rap * $volume_rap_2022_produk_c;

			$nilai_semen_250_rap_2022 = $total_semen_250_rap_2022 * $komposisi_250_rap['price_a'];
			$nilai_pasir_250_rap_2022 = $total_pasir_250_rap_2022 * $komposisi_250_rap['price_b'];
			$nilai_batu1020_250_rap_2022 = $total_batu1020_250_rap_2022 * $komposisi_250_rap['price_c'];
			$nilai_batu2030_250_rap_2022 = $total_batu2030_250_rap_2022 * $komposisi_250_rap['price_d'];

			$total_250_rap_2022 = $nilai_semen_250_rap_2022 + $nilai_pasir_250_rap_2022 + $nilai_batu1020_250_rap_2022 + $nilai_batu2030_250_rap_2022;

			//TOTAL K-250_18
			$total_semen_250_18_rap_2022 = $komposisi_250_18_produk_a_rap * $volume_rap_2022_produk_d;
			$total_pasir_250_18_rap_2022 = $komposisi_250_18_produk_b_rap * $volume_rap_2022_produk_d;
			$total_batu1020_250_18_rap_2022 = $komposisi_250_18_produk_c_rap * $volume_rap_2022_produk_d;
			$total_batu2030_250_18_rap_2022 = $komposisi_250_18_produk_d_rap * $volume_rap_2022_produk_d;

			$nilai_semen_250_18_rap_2022 = $total_semen_250_18_rap_2022 * $komposisi_250_18_rap['price_a'];
			$nilai_pasir_250_18_rap_2022 = $total_pasir_250_18_rap_2022 * $komposisi_250_18_rap['price_b'];
			$nilai_batu1020_250_18_rap_2022 = $total_batu1020_250_18_rap_2022 * $komposisi_250_18_rap['price_c'];
			$nilai_batu2030_250_18_rap_2022 = $total_batu2030_250_18_rap_2022 * $komposisi_250_18_rap['price_d'];

			$total_250_18_rap_2022 = $nilai_semen_250_18_rap_2022 + $nilai_pasir_250_18_rap_2022 + $nilai_batu1020_250_18_rap_2022 + $nilai_batu2030_250_18_rap_2022;

			//TOTAL ALL
			$total_bahan_all_rap_2022 = $total_125_rap_2022 + $total_225_rap_2022 + $total_250_rap_2022 + $total_250_18_rap_2022;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_rap_2022 = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_end')")
			->get()->row_array();

			$batching_plant_rap_2022 = $total_rap_volume_2022 * $rap_alat_rap_2022['batching_plant'];
			$truck_mixer_rap_2022 = $total_rap_volume_2022 * $rap_alat_rap_2022['truck_mixer'];
			$wheel_loader_rap_2022 = $total_rap_volume_2022 * $rap_alat_rap_2022['wheel_loader'];
			$bbm_solar_rap_2022 = $total_rap_volume_2022 * $rap_alat_rap_2022['bbm_solar'];
			$biaya_alat_all_rap_2022 = $batching_plant_rap_2022 + $truck_mixer_rap_2022 + $wheel_loader_rap_2022 + $bbm_solar_rap_2022;
		
			$total_rap_2022_biaya_bahan = $total_bahan_all_rap_2022;
			$total_rap_2022_biaya_alat = $biaya_alat_all_rap_2022;
			$total_rap_2022_biaya_overhead = $rencana_kerja_2022['biaya_overhead'];
			$total_rap_2022_biaya_bank = $rencana_kerja_2022['biaya_bank'];
			$total_rap_2022_biaya_persiapan = $rencana_kerja_2022['biaya_persiapan'];

			$total_biaya_rap_2022_biaya = $total_rap_2022_biaya_bahan + $total_rap_2022_biaya_alat + $total_rap_2022_biaya_overhead + $total_rap_2022_biaya_bank + $total_rap_2022_biaya_persiapan;

			?>
			<!-- RAP 2022 -->
			
			<!-- AKUMULASI SD. SAAT INI -->
			<?php
			$penjualan_akumulasi_produk_a = $this->db->select('p.nama_produk, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('produk p', 'pp.product_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("(pp.date_production <= '$date_now')")
			->where("pp.status = 'PUBLISH'")
			->where("pp.product_id = 2")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.product_id")
			->order_by('p.nama_produk','asc')
			->get()->row_array();
			$volume_akumulasi_produk_a = $penjualan_akumulasi_produk_a['volume'];
			$nilai_akumulasi_produk_a = $penjualan_akumulasi_produk_a['price'];

			$penjualan_akumulasi_produk_b = $this->db->select('p.nama_produk, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('produk p', 'pp.product_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("(pp.date_production <= '$date_now')")
			->where("pp.status = 'PUBLISH'")
			->where("pp.product_id = 1")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.product_id")
			->order_by('p.nama_produk','asc')
			->get()->row_array();
			$volume_akumulasi_produk_b = $penjualan_akumulasi_produk_b['volume'];
			$nilai_akumulasi_produk_b = $penjualan_akumulasi_produk_b['price'];

			$penjualan_akumulasi_produk_c = $this->db->select('p.nama_produk, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('produk p', 'pp.product_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("(pp.date_production <= '$date_now')")
			->where("pp.status = 'PUBLISH'")
			->where("pp.product_id = 3")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.product_id")
			->order_by('p.nama_produk','asc')
			->get()->row_array();
			$volume_akumulasi_produk_c = $penjualan_akumulasi_produk_c['volume'];
			$nilai_akumulasi_produk_c = $penjualan_akumulasi_produk_c['price'];

			$penjualan_akumulasi_produk_d = $this->db->select('p.nama_produk, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('produk p', 'pp.product_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("(pp.date_production <= '$date_now')")
			->where("pp.status = 'PUBLISH'")
			->where("pp.product_id = 11")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.product_id")
			->order_by('p.nama_produk','asc')
			->get()->row_array();
			$volume_akumulasi_produk_d = $penjualan_akumulasi_produk_d['volume'];
			$nilai_akumulasi_produk_d = $penjualan_akumulasi_produk_d['price'];

			$total_akumulasi_volume = $volume_akumulasi_produk_a + $volume_akumulasi_produk_b + $volume_akumulasi_produk_c + $volume_akumulasi_produk_d;
			$total_akumulasi_nilai = $nilai_akumulasi_produk_a + $nilai_akumulasi_produk_b + $nilai_akumulasi_produk_c + $nilai_akumulasi_produk_d;
			?>
			<!-- AKUMULASI SD. SAAT INI -->
			
			<!-- NOVEMBER -->
			<?php
			$date_november_awal = date('2022-11-01');
			$date_november_akhir = date('2022-11-30');
			$rencana_kerja_november = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_november_awal' and '$date_november_akhir'")
			->get()->row_array();
			$volume_november_produk_a = $rencana_kerja_november['vol_produk_a'];
			$volume_november_produk_b = $rencana_kerja_november['vol_produk_b'];
			$volume_november_produk_c = $rencana_kerja_november['vol_produk_c'];
			$volume_november_produk_d = $rencana_kerja_november['vol_produk_d'];

			$total_november_volume = $volume_november_produk_a + $volume_november_produk_b + $volume_november_produk_c + $volume_november_produk_d;

			$nilai_jual_125_november = $volume_november_produk_a * $harga_jual_125_now['harga_satuan'];
			$nilai_jual_225_november = $volume_november_produk_b * $harga_jual_225_now['harga_satuan'];
			$nilai_jual_250_november = $volume_november_produk_c * $harga_jual_250_now['harga_satuan'];
			$nilai_jual_250_18_november = $volume_november_produk_d * $harga_jual_250_18_now['harga_satuan'];
			$nilai_jual_all_november = $nilai_jual_125_november + $nilai_jual_225_november + $nilai_jual_250_november + $nilai_jual_250_18_november;

			$total_november_nilai = $nilai_jual_all_november;

			//TOTAL PEMAKAIAN BAHAN
			$rencana_kerja_november = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_november_awal' and '$date_november_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_november_produk_a = $rencana_kerja_november['vol_produk_a'];
			$volume_rencana_kerja_november_produk_b = $rencana_kerja_november['vol_produk_b'];
			$volume_rencana_kerja_november_produk_c = $rencana_kerja_november['vol_produk_c'];
			$volume_rencana_kerja_november_produk_d = $rencana_kerja_november['vol_produk_d'];

			//TOTAL K-125
			$total_semen_125_november = $komposisi_125_produk_a * $volume_rencana_kerja_november_produk_a;
			$total_pasir_125_november = $komposisi_125_produk_b * $volume_rencana_kerja_november_produk_a;
			$total_batu1020_125_november = $komposisi_125_produk_c * $volume_rencana_kerja_november_produk_a;
			$total_batu2030_125_november = $komposisi_125_produk_d * $volume_rencana_kerja_november_produk_a;

			$nilai_semen_125_november = $total_semen_125_november * $komposisi_125['price_a'];
			$nilai_pasir_125_november = $total_pasir_125_november * $komposisi_125['price_b'];
			$nilai_batu1020_125_november = $total_batu1020_125_november * $komposisi_125['price_c'];
			$nilai_batu2030_125_november = $total_batu2030_125_november * $komposisi_125['price_d'];

			$total_125_november = $nilai_semen_125_november + $nilai_pasir_125_november + $nilai_batu1020_125_november + $nilai_batu2030_125_november;

			//TOTAL K-225
			$total_semen_225_november = $komposisi_225_produk_a * $volume_rencana_kerja_november_produk_b;
			$total_pasir_225_november = $komposisi_225_produk_b * $volume_rencana_kerja_november_produk_b;
			$total_batu1020_225_november = $komposisi_225_produk_c * $volume_rencana_kerja_november_produk_b;
			$total_batu2030_225_november = $komposisi_225_produk_d * $volume_rencana_kerja_november_produk_b;

			$nilai_semen_225_november = $total_semen_225_november * $komposisi_225['price_a'];
			$nilai_pasir_225_november = $total_pasir_225_november * $komposisi_225['price_b'];
			$nilai_batu1020_225_november = $total_batu1020_225_november * $komposisi_225['price_c'];
			$nilai_batu2030_225_november = $total_batu2030_225_november * $komposisi_225['price_d'];

			$total_225_november = $nilai_semen_225_november + $nilai_pasir_225_november + $nilai_batu1020_225_november + $nilai_batu2030_225_november;

			//TOTAL K-250
			$total_semen_250_november = $komposisi_250_produk_a * $volume_rencana_kerja_november_produk_c;
			$total_pasir_250_november = $komposisi_250_produk_b * $volume_rencana_kerja_november_produk_c;
			$total_batu1020_250_november = $komposisi_250_produk_c * $volume_rencana_kerja_november_produk_c;
			$total_batu2030_250_november = $komposisi_250_produk_d * $volume_rencana_kerja_november_produk_c;

			$nilai_semen_250_november = $total_semen_250_november * $komposisi_250['price_a'];
			$nilai_pasir_250_november = $total_pasir_250_november * $komposisi_250['price_b'];
			$nilai_batu1020_250_november = $total_batu1020_250_november * $komposisi_250['price_c'];
			$nilai_batu2030_250_november = $total_batu2030_250_november * $komposisi_250['price_d'];

			$total_250_november = $nilai_semen_250_november + $nilai_pasir_250_november + $nilai_batu1020_250_november + $nilai_batu2030_250_november;

			//TOTAL K-250_18
			$total_semen_250_18_november = $komposisi_250_18_produk_a * $volume_rencana_kerja_november_produk_d;
			$total_pasir_250_18_november = $komposisi_250_18_produk_b * $volume_rencana_kerja_november_produk_d;
			$total_batu1020_250_18_november = $komposisi_250_18_produk_c * $volume_rencana_kerja_november_produk_d;
			$total_batu2030_250_18_november = $komposisi_250_18_produk_d * $volume_rencana_kerja_november_produk_d;

			$nilai_semen_250_18_november = $total_semen_250_18_november  * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_november = $total_pasir_250_18_november  * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_november = $total_batu1020_250_18_november  * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_november = $total_batu2030_250_18_november  * $komposisi_250_18['price_d'];

			$total_250_18_november = $nilai_semen_250_18_november  + $nilai_pasir_250_18_november  + $nilai_batu1020_250_18_november  + $nilai_batu2030_250_18_november;

			//TOTAL ALL
			$total_bahan_all_november = $total_125_november  + $total_225_november  + $total_250_november  + $total_250_18_november;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_november = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_november_akhir')")
			->get()->row_array();

			$batching_plant_november = $total_november_volume * $rap_alat_november['batching_plant'];
			$truck_mixer_november = $total_november_volume * $rap_alat_november['truck_mixer'];
			$wheel_loader_november = $total_november_volume * $rap_alat_november['wheel_loader'];
			$bbm_solar_november = $total_november_volume * $rap_alat_november['bbm_solar'];
			$biaya_alat_all_november = $batching_plant_november + $truck_mixer_november + $wheel_loader_november + $bbm_solar_november;
		
			$total_november_biaya_bahan = $total_bahan_all_november;
			$total_november_biaya_alat = $biaya_alat_all_november;
			$total_november_biaya_overhead = $rencana_kerja_november['biaya_overhead'];
			$total_november_biaya_bank = $rencana_kerja_november['biaya_bank'];
			$total_november_biaya_persiapan = $rencana_kerja_november['biaya_persiapan'];

			$total_biaya_november_biaya = $total_november_biaya_bahan + $total_november_biaya_alat + $total_november_biaya_overhead + $total_november_biaya_bank + $total_november_biaya_persiapan;
			?>
			<!-- NOVEMBER -->

			<!-- DESEMBER -->
			<?php
			$date_desember_awal = date('2022-12-01');
			$date_desember_akhir = date('2022-12-31');
			$rencana_kerja_desember = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_desember_awal' and '$date_desember_akhir'")
			->get()->row_array();
			$volume_desember_produk_a = $rencana_kerja_desember['vol_produk_a'];
			$volume_desember_produk_b = $rencana_kerja_desember['vol_produk_b'];
			$volume_desember_produk_c = $rencana_kerja_desember['vol_produk_c'];
			$volume_desember_produk_d = $rencana_kerja_desember['vol_produk_d'];

			$total_desember_volume = $volume_desember_produk_a + $volume_desember_produk_b + $volume_desember_produk_c + $volume_desember_produk_d;

			$nilai_jual_125_desember = $volume_desember_produk_a * $harga_jual_125_now['harga_satuan'];
			$nilai_jual_225_desember = $volume_desember_produk_b * $harga_jual_225_now['harga_satuan'];
			$nilai_jual_250_desember = $volume_desember_produk_c * $harga_jual_250_now['harga_satuan'];
			$nilai_jual_250_18_desember = $volume_desember_produk_d * $harga_jual_250_18_now['harga_satuan'];
			$nilai_jual_all_desember = $nilai_jual_125_desember + $nilai_jual_225_desember + $nilai_jual_250_desember + $nilai_jual_250_18_desember;

			$total_desember_nilai = $nilai_jual_all_desember;

			//TOTAL PEMAKAIAN BAHAN
			$rencana_kerja_desember = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_desember_awal' and '$date_desember_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_desember_produk_a = $rencana_kerja_desember['vol_produk_a'];
			$volume_rencana_kerja_desember_produk_b = $rencana_kerja_desember['vol_produk_b'];
			$volume_rencana_kerja_desember_produk_c = $rencana_kerja_desember['vol_produk_c'];
			$volume_rencana_kerja_desember_produk_d = $rencana_kerja_desember['vol_produk_d'];

			//TOTAL K-125
			$total_semen_125_desember = $komposisi_125_produk_a * $volume_rencana_kerja_desember_produk_a;
			$total_pasir_125_desember = $komposisi_125_produk_b * $volume_rencana_kerja_desember_produk_a;
			$total_batu1020_125_desember = $komposisi_125_produk_c * $volume_rencana_kerja_desember_produk_a;
			$total_batu2030_125_desember = $komposisi_125_produk_d * $volume_rencana_kerja_desember_produk_a;

			$nilai_semen_125_desember = $total_semen_125_desember * $komposisi_125['price_a'];
			$nilai_pasir_125_desember = $total_pasir_125_desember * $komposisi_125['price_b'];
			$nilai_batu1020_125_desember = $total_batu1020_125_desember * $komposisi_125['price_c'];
			$nilai_batu2030_125_desember = $total_batu2030_125_desember * $komposisi_125['price_d'];

			$total_125_desember = $nilai_semen_125_desember + $nilai_pasir_125_desember + $nilai_batu1020_125_desember + $nilai_batu2030_125_desember;

			//TOTAL K-225
			$total_semen_225_desember = $komposisi_225_produk_a * $volume_rencana_kerja_desember_produk_b;
			$total_pasir_225_desember = $komposisi_225_produk_b * $volume_rencana_kerja_desember_produk_b;
			$total_batu1020_225_desember = $komposisi_225_produk_c * $volume_rencana_kerja_desember_produk_b;
			$total_batu2030_225_desember = $komposisi_225_produk_d * $volume_rencana_kerja_desember_produk_b;

			$nilai_semen_225_desember = $total_semen_225_desember * $komposisi_225['price_a'];
			$nilai_pasir_225_desember = $total_pasir_225_desember * $komposisi_225['price_b'];
			$nilai_batu1020_225_desember = $total_batu1020_225_desember * $komposisi_225['price_c'];
			$nilai_batu2030_225_desember = $total_batu2030_225_desember * $komposisi_225['price_d'];

			$total_225_desember = $nilai_semen_225_desember + $nilai_pasir_225_desember + $nilai_batu1020_225_desember + $nilai_batu2030_225_desember;

			//TOTAL K-250
			$total_semen_250_desember = $komposisi_250_produk_a * $volume_rencana_kerja_desember_produk_c;
			$total_pasir_250_desember = $komposisi_250_produk_b * $volume_rencana_kerja_desember_produk_c;
			$total_batu1020_250_desember = $komposisi_250_produk_c * $volume_rencana_kerja_desember_produk_c;
			$total_batu2030_250_desember = $komposisi_250_produk_d * $volume_rencana_kerja_desember_produk_c;

			$nilai_semen_250_desember = $total_semen_250_desember * $komposisi_250['price_a'];
			$nilai_pasir_250_desember = $total_pasir_250_desember * $komposisi_250['price_b'];
			$nilai_batu1020_250_desember = $total_batu1020_250_desember * $komposisi_250['price_c'];
			$nilai_batu2030_250_desember = $total_batu2030_250_desember * $komposisi_250['price_d'];

			$total_250_desember = $nilai_semen_250_desember + $nilai_pasir_250_desember + $nilai_batu1020_250_desember + $nilai_batu2030_250_desember;

			//TOTAL K-250_18
			$total_semen_250_18_desember = $komposisi_250_18_produk_a * $volume_rencana_kerja_desember_produk_d;
			$total_pasir_250_18_desember = $komposisi_250_18_produk_b * $volume_rencana_kerja_desember_produk_d;
			$total_batu1020_250_18_desember = $komposisi_250_18_produk_c * $volume_rencana_kerja_desember_produk_d;
			$total_batu2030_250_18_desember = $komposisi_250_18_produk_d * $volume_rencana_kerja_desember_produk_d;

			$nilai_semen_250_18_desember = $total_semen_250_18_desember  * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_desember = $total_pasir_250_18_desember  * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_desember = $total_batu1020_250_18_desember  * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_desember = $total_batu2030_250_18_desember  * $komposisi_250_18['price_d'];

			$total_250_18_desember = $nilai_semen_250_18_desember  + $nilai_pasir_250_18_desember  + $nilai_batu1020_250_18_desember  + $nilai_batu2030_250_18_desember;

			//TOTAL ALL
			$total_bahan_all_desember = $total_125_desember  + $total_225_desember  + $total_250_desember  + $total_250_18_desember;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_desember = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_desember_akhir')")
			->get()->row_array();

			$batching_plant_desember = $total_desember_volume * $rap_alat_desember['batching_plant'];
			$truck_mixer_desember = $total_desember_volume * $rap_alat_desember['truck_mixer'];
			$wheel_loader_desember = $total_desember_volume * $rap_alat_desember['wheel_loader'];
			$bbm_solar_desember = $total_desember_volume * $rap_alat_desember['bbm_solar'];
			$biaya_alat_all_desember = $batching_plant_desember + $truck_mixer_desember + $wheel_loader_desember + $bbm_solar_desember;
		
			$total_desember_biaya_bahan = $total_bahan_all_desember;
			$total_desember_biaya_alat = $biaya_alat_all_desember;
			$total_desember_biaya_overhead = $rencana_kerja_desember['biaya_overhead'];
			$total_desember_biaya_bank = $rencana_kerja_desember['biaya_bank'];
			$total_desember_biaya_persiapan = $rencana_kerja_desember['biaya_persiapan'];

			$total_biaya_desember_biaya = $total_desember_biaya_bahan + $total_desember_biaya_alat + $total_desember_biaya_overhead + $total_desember_biaya_bank + $total_desember_biaya_persiapan;
			?>
			<!-- DESEMBER -->

			<!-- JANUARI -->
			<?php
			$date_januari_awal = date('2023-01-01');
			$date_januari_akhir = date('2023-01-31');
			$rencana_kerja_januari = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_januari_akhir'")
			->get()->row_array();
			$volume_januari_produk_a = $rencana_kerja_januari['vol_produk_a'];
			$volume_januari_produk_b = $rencana_kerja_januari['vol_produk_b'];
			$volume_januari_produk_c = $rencana_kerja_januari['vol_produk_c'];
			$volume_januari_produk_d = $rencana_kerja_januari['vol_produk_d'];

			$total_januari_volume = $volume_januari_produk_a + $volume_januari_produk_b + $volume_januari_produk_c + $volume_januari_produk_d;

			$nilai_jual_125_januari = $volume_januari_produk_a * $harga_jual_125_now['harga_satuan'];
			$nilai_jual_225_januari = $volume_januari_produk_b * $harga_jual_225_now['harga_satuan'];
			$nilai_jual_250_januari = $volume_januari_produk_c * $harga_jual_250_now['harga_satuan'];
			$nilai_jual_250_18_januari = $volume_januari_produk_d * $harga_jual_250_18_now['harga_satuan'];
			$nilai_jual_all_januari = $nilai_jual_125_januari + $nilai_jual_225_januari + $nilai_jual_250_januari + $nilai_jual_250_18_januari;

			$total_januari_nilai = $nilai_jual_all_januari;

			//TOTAL PEMAKAIAN BAHAN
			$rencana_kerja_januari = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_januari_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_januari_produk_a = $rencana_kerja_januari['vol_produk_a'];
			$volume_rencana_kerja_januari_produk_b = $rencana_kerja_januari['vol_produk_b'];
			$volume_rencana_kerja_januari_produk_c = $rencana_kerja_januari['vol_produk_c'];
			$volume_rencana_kerja_januari_produk_d = $rencana_kerja_januari['vol_produk_d'];

			//TOTAL K-125
			$total_semen_125_januari = $komposisi_125_produk_a * $volume_rencana_kerja_januari_produk_a;
			$total_pasir_125_januari = $komposisi_125_produk_b * $volume_rencana_kerja_januari_produk_a;
			$total_batu1020_125_januari = $komposisi_125_produk_c * $volume_rencana_kerja_januari_produk_a;
			$total_batu2030_125_januari = $komposisi_125_produk_d * $volume_rencana_kerja_januari_produk_a;

			$nilai_semen_125_januari = $total_semen_125_januari * $komposisi_125['price_a'];
			$nilai_pasir_125_januari = $total_pasir_125_januari * $komposisi_125['price_b'];
			$nilai_batu1020_125_januari = $total_batu1020_125_januari * $komposisi_125['price_c'];
			$nilai_batu2030_125_januari = $total_batu2030_125_januari * $komposisi_125['price_d'];

			$total_125_januari = $nilai_semen_125_januari + $nilai_pasir_125_januari + $nilai_batu1020_125_januari + $nilai_batu2030_125_januari;

			//TOTAL K-225
			$total_semen_225_januari = $komposisi_225_produk_a * $volume_rencana_kerja_januari_produk_b;
			$total_pasir_225_januari = $komposisi_225_produk_b * $volume_rencana_kerja_januari_produk_b;
			$total_batu1020_225_januari = $komposisi_225_produk_c * $volume_rencana_kerja_januari_produk_b;
			$total_batu2030_225_januari = $komposisi_225_produk_d * $volume_rencana_kerja_januari_produk_b;

			$nilai_semen_225_januari = $total_semen_225_januari * $komposisi_225['price_a'];
			$nilai_pasir_225_januari = $total_pasir_225_januari * $komposisi_225['price_b'];
			$nilai_batu1020_225_januari = $total_batu1020_225_januari * $komposisi_225['price_c'];
			$nilai_batu2030_225_januari = $total_batu2030_225_januari * $komposisi_225['price_d'];

			$total_225_januari = $nilai_semen_225_januari + $nilai_pasir_225_januari + $nilai_batu1020_225_januari + $nilai_batu2030_225_januari;

			//TOTAL K-250
			$total_semen_250_januari = $komposisi_250_produk_a * $volume_rencana_kerja_januari_produk_c;
			$total_pasir_250_januari = $komposisi_250_produk_b * $volume_rencana_kerja_januari_produk_c;
			$total_batu1020_250_januari = $komposisi_250_produk_c * $volume_rencana_kerja_januari_produk_c;
			$total_batu2030_250_januari = $komposisi_250_produk_d * $volume_rencana_kerja_januari_produk_c;

			$nilai_semen_250_januari = $total_semen_250_januari * $komposisi_250['price_a'];
			$nilai_pasir_250_januari = $total_pasir_250_januari * $komposisi_250['price_b'];
			$nilai_batu1020_250_januari = $total_batu1020_250_januari * $komposisi_250['price_c'];
			$nilai_batu2030_250_januari = $total_batu2030_250_januari * $komposisi_250['price_d'];

			$total_250_januari = $nilai_semen_250_januari + $nilai_pasir_250_januari + $nilai_batu1020_250_januari + $nilai_batu2030_250_januari;

			//TOTAL K-250_18
			$total_semen_250_18_januari = $komposisi_250_18_produk_a * $volume_rencana_kerja_januari_produk_d;
			$total_pasir_250_18_januari = $komposisi_250_18_produk_b * $volume_rencana_kerja_januari_produk_d;
			$total_batu1020_250_18_januari = $komposisi_250_18_produk_c * $volume_rencana_kerja_januari_produk_d;
			$total_batu2030_250_18_januari = $komposisi_250_18_produk_d * $volume_rencana_kerja_januari_produk_d;

			$nilai_semen_250_18_januari = $total_semen_250_18_januari  * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_januari = $total_pasir_250_18_januari  * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_januari = $total_batu1020_250_18_januari  * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_januari = $total_batu2030_250_18_januari  * $komposisi_250_18['price_d'];

			$total_250_18_januari = $nilai_semen_250_18_januari  + $nilai_pasir_250_18_januari  + $nilai_batu1020_250_18_januari  + $nilai_batu2030_250_18_januari;

			//TOTAL ALL
			$total_bahan_all_januari = $total_125_januari  + $total_225_januari  + $total_250_januari  + $total_250_18_januari;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_januari = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_januari_akhir')")
			->get()->row_array();

			$batching_plant_januari = $total_januari_volume * $rap_alat_januari['batching_plant'];
			$truck_mixer_januari = $total_januari_volume * $rap_alat_januari['truck_mixer'];
			$wheel_loader_januari = $total_januari_volume * $rap_alat_januari['wheel_loader'];
			$bbm_solar_januari = $total_januari_volume * $rap_alat_januari['bbm_solar'];
			$biaya_alat_all_januari = $batching_plant_januari + $truck_mixer_januari + $wheel_loader_januari + $bbm_solar_januari;
		
			$total_januari_biaya_bahan = $total_bahan_all_januari;
			$total_januari_biaya_alat = $biaya_alat_all_januari;
			$total_januari_biaya_overhead = $rencana_kerja_januari['biaya_overhead'];
			$total_januari_biaya_bank = $rencana_kerja_januari['biaya_bank'];
			$total_januari_biaya_persiapan = $rencana_kerja_januari['biaya_persiapan'];

			$total_biaya_januari_biaya = $total_januari_biaya_bahan + $total_januari_biaya_alat + $total_januari_biaya_overhead + $total_januari_biaya_bank + $total_januari_biaya_persiapan;
			?>
			<!-- JANUARI -->

			<!-- FEBRUARI -->
			<?php
			$date_februari_awal = date('2023-02-01');
			$date_februari_akhir = date('2023-02-28');
			$rencana_kerja_februari = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_februari_awal' and '$date_februari_akhir'")
			->get()->row_array();
			$volume_februari_produk_a = $rencana_kerja_februari['vol_produk_a'];
			$volume_februari_produk_b = $rencana_kerja_februari['vol_produk_b'];
			$volume_februari_produk_c = $rencana_kerja_februari['vol_produk_c'];
			$volume_februari_produk_d = $rencana_kerja_februari['vol_produk_d'];

			$total_februari_volume = $volume_februari_produk_a + $volume_februari_produk_b + $volume_februari_produk_c + $volume_februari_produk_d;

			$nilai_jual_125_februari = $volume_februari_produk_a * $harga_jual_125_now['harga_satuan'];
			$nilai_jual_225_februari = $volume_februari_produk_b * $harga_jual_225_now['harga_satuan'];
			$nilai_jual_250_februari = $volume_februari_produk_c * $harga_jual_250_now['harga_satuan'];
			$nilai_jual_250_18_februari = $volume_februari_produk_d * $harga_jual_250_18_now['harga_satuan'];
			$nilai_jual_all_februari = $nilai_jual_125_februari + $nilai_jual_225_februari + $nilai_jual_250_februari + $nilai_jual_250_18_februari;

			$total_februari_nilai = $nilai_jual_all_februari;

			//TOTAL PEMAKAIAN BAHAN
			$rencana_kerja_februari = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_februari_awal' and '$date_februari_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_februari_produk_a = $rencana_kerja_februari['vol_produk_a'];
			$volume_rencana_kerja_februari_produk_b = $rencana_kerja_februari['vol_produk_b'];
			$volume_rencana_kerja_februari_produk_c = $rencana_kerja_februari['vol_produk_c'];
			$volume_rencana_kerja_februari_produk_d = $rencana_kerja_februari['vol_produk_d'];

			//TOTAL K-125
			$total_semen_125_februari = $komposisi_125_produk_a * $volume_rencana_kerja_februari_produk_a;
			$total_pasir_125_februari = $komposisi_125_produk_b * $volume_rencana_kerja_februari_produk_a;
			$total_batu1020_125_februari = $komposisi_125_produk_c * $volume_rencana_kerja_februari_produk_a;
			$total_batu2030_125_februari = $komposisi_125_produk_d * $volume_rencana_kerja_februari_produk_a;

			$nilai_semen_125_februari = $total_semen_125_februari * $komposisi_125['price_a'];
			$nilai_pasir_125_februari = $total_pasir_125_februari * $komposisi_125['price_b'];
			$nilai_batu1020_125_februari = $total_batu1020_125_februari * $komposisi_125['price_c'];
			$nilai_batu2030_125_februari = $total_batu2030_125_februari * $komposisi_125['price_d'];

			$total_125_februari = $nilai_semen_125_februari + $nilai_pasir_125_februari + $nilai_batu1020_125_februari + $nilai_batu2030_125_februari;

			//TOTAL K-225
			$total_semen_225_februari = $komposisi_225_produk_a * $volume_rencana_kerja_februari_produk_b;
			$total_pasir_225_februari = $komposisi_225_produk_b * $volume_rencana_kerja_februari_produk_b;
			$total_batu1020_225_februari = $komposisi_225_produk_c * $volume_rencana_kerja_februari_produk_b;
			$total_batu2030_225_februari = $komposisi_225_produk_d * $volume_rencana_kerja_februari_produk_b;

			$nilai_semen_225_februari = $total_semen_225_februari * $komposisi_225['price_a'];
			$nilai_pasir_225_februari = $total_pasir_225_februari * $komposisi_225['price_b'];
			$nilai_batu1020_225_februari = $total_batu1020_225_februari * $komposisi_225['price_c'];
			$nilai_batu2030_225_februari = $total_batu2030_225_februari * $komposisi_225['price_d'];

			$total_225_februari = $nilai_semen_225_februari + $nilai_pasir_225_februari + $nilai_batu1020_225_februari + $nilai_batu2030_225_februari;

			//TOTAL K-250
			$total_semen_250_februari = $komposisi_250_produk_a * $volume_rencana_kerja_februari_produk_c;
			$total_pasir_250_februari = $komposisi_250_produk_b * $volume_rencana_kerja_februari_produk_c;
			$total_batu1020_250_februari = $komposisi_250_produk_c * $volume_rencana_kerja_februari_produk_c;
			$total_batu2030_250_februari = $komposisi_250_produk_d * $volume_rencana_kerja_februari_produk_c;

			$nilai_semen_250_februari = $total_semen_250_februari * $komposisi_250['price_a'];
			$nilai_pasir_250_februari = $total_pasir_250_februari * $komposisi_250['price_b'];
			$nilai_batu1020_250_februari = $total_batu1020_250_februari * $komposisi_250['price_c'];
			$nilai_batu2030_250_februari = $total_batu2030_250_februari * $komposisi_250['price_d'];

			$total_250_februari = $nilai_semen_250_februari + $nilai_pasir_250_februari + $nilai_batu1020_250_februari + $nilai_batu2030_250_februari;

			//TOTAL K-250_18
			$total_semen_250_18_februari = $komposisi_250_18_produk_a * $volume_rencana_kerja_februari_produk_d;
			$total_pasir_250_18_februari = $komposisi_250_18_produk_b * $volume_rencana_kerja_februari_produk_d;
			$total_batu1020_250_18_februari = $komposisi_250_18_produk_c * $volume_rencana_kerja_februari_produk_d;
			$total_batu2030_250_18_februari = $komposisi_250_18_produk_d * $volume_rencana_kerja_februari_produk_d;

			$nilai_semen_250_18_februari = $total_semen_250_18_februari  * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_februari = $total_pasir_250_18_februari  * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_februari = $total_batu1020_250_18_februari  * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_februari = $total_batu2030_250_18_februari  * $komposisi_250_18['price_d'];

			$total_250_18_februari = $nilai_semen_250_18_februari  + $nilai_pasir_250_18_februari  + $nilai_batu1020_250_18_februari  + $nilai_batu2030_250_18_februari;

			//TOTAL ALL
			$total_bahan_all_februari = $total_125_februari  + $total_225_februari  + $total_250_februari  + $total_250_18_februari;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_februari = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_februari_akhir')")
			->get()->row_array();

			$batching_plant_februari = $total_februari_volume * $rap_alat_februari['batching_plant'];
			$truck_mixer_februari = $total_februari_volume * $rap_alat_februari['truck_mixer'];
			$wheel_loader_februari = $total_februari_volume * $rap_alat_februari['wheel_loader'];
			$bbm_solar_februari = $total_februari_volume * $rap_alat_februari['bbm_solar'];
			$biaya_alat_all_februari = $batching_plant_februari + $truck_mixer_februari + $wheel_loader_februari + $bbm_solar_februari;
		
			$total_februari_biaya_bahan = $total_bahan_all_februari;
			$total_februari_biaya_alat = $biaya_alat_all_februari;
			$total_februari_biaya_overhead = $rencana_kerja_februari['biaya_overhead'];
			$total_februari_biaya_bank = $rencana_kerja_februari['biaya_bank'];
			$total_februari_biaya_persiapan = $rencana_kerja_februari['biaya_persiapan'];

			$total_biaya_februari_biaya = $total_februari_biaya_bahan + $total_februari_biaya_alat + $total_februari_biaya_overhead + $total_februari_biaya_bank + $total_februari_biaya_persiapan;
			?>
			<!-- FEBRUARI -->

			<!-- MARET -->
			<?php
			$date_maret_awal = date('2023-03-01');
			$date_maret_akhir = date('2023-03-31');
			$rencana_kerja_maret = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_maret_awal' and '$date_maret_akhir'")
			->get()->row_array();
			$volume_maret_produk_a = $rencana_kerja_maret['vol_produk_a'];
			$volume_maret_produk_b = $rencana_kerja_maret['vol_produk_b'];
			$volume_maret_produk_c = $rencana_kerja_maret['vol_produk_c'];
			$volume_maret_produk_d = $rencana_kerja_maret['vol_produk_d'];

			$total_maret_volume = $volume_maret_produk_a + $volume_maret_produk_b + $volume_maret_produk_c + $volume_maret_produk_d;

			$nilai_jual_125_maret = $volume_maret_produk_a * $harga_jual_125_now['harga_satuan'];
			$nilai_jual_225_maret = $volume_maret_produk_b * $harga_jual_225_now['harga_satuan'];
			$nilai_jual_250_maret = $volume_maret_produk_c * $harga_jual_250_now['harga_satuan'];
			$nilai_jual_250_18_maret = $volume_maret_produk_d * $harga_jual_250_18_now['harga_satuan'];
			$nilai_jual_all_maret = $nilai_jual_125_maret + $nilai_jual_225_maret + $nilai_jual_250_maret + $nilai_jual_250_18_maret;

			$total_maret_nilai = $nilai_jual_all_maret;

			//TOTAL PEMAKAIAN BAHAN
			$rencana_kerja_maret = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_maret_awal' and '$date_maret_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_maret_produk_a = $rencana_kerja_maret['vol_produk_a'];
			$volume_rencana_kerja_maret_produk_b = $rencana_kerja_maret['vol_produk_b'];
			$volume_rencana_kerja_maret_produk_c = $rencana_kerja_maret['vol_produk_c'];
			$volume_rencana_kerja_maret_produk_d = $rencana_kerja_maret['vol_produk_d'];

			//TOTAL K-125
			$total_semen_125_maret = $komposisi_125_produk_a * $volume_rencana_kerja_maret_produk_a;
			$total_pasir_125_maret = $komposisi_125_produk_b * $volume_rencana_kerja_maret_produk_a;
			$total_batu1020_125_maret = $komposisi_125_produk_c * $volume_rencana_kerja_maret_produk_a;
			$total_batu2030_125_maret = $komposisi_125_produk_d * $volume_rencana_kerja_maret_produk_a;

			$nilai_semen_125_maret = $total_semen_125_maret * $komposisi_125['price_a'];
			$nilai_pasir_125_maret = $total_pasir_125_maret * $komposisi_125['price_b'];
			$nilai_batu1020_125_maret = $total_batu1020_125_maret * $komposisi_125['price_c'];
			$nilai_batu2030_125_maret = $total_batu2030_125_maret * $komposisi_125['price_d'];

			$total_125_maret = $nilai_semen_125_maret + $nilai_pasir_125_maret + $nilai_batu1020_125_maret + $nilai_batu2030_125_maret;

			//TOTAL K-225
			$total_semen_225_maret = $komposisi_225_produk_a * $volume_rencana_kerja_maret_produk_b;
			$total_pasir_225_maret = $komposisi_225_produk_b * $volume_rencana_kerja_maret_produk_b;
			$total_batu1020_225_maret = $komposisi_225_produk_c * $volume_rencana_kerja_maret_produk_b;
			$total_batu2030_225_maret = $komposisi_225_produk_d * $volume_rencana_kerja_maret_produk_b;

			$nilai_semen_225_maret = $total_semen_225_maret * $komposisi_225['price_a'];
			$nilai_pasir_225_maret = $total_pasir_225_maret * $komposisi_225['price_b'];
			$nilai_batu1020_225_maret = $total_batu1020_225_maret * $komposisi_225['price_c'];
			$nilai_batu2030_225_maret = $total_batu2030_225_maret * $komposisi_225['price_d'];

			$total_225_maret = $nilai_semen_225_maret + $nilai_pasir_225_maret + $nilai_batu1020_225_maret + $nilai_batu2030_225_maret;

			//TOTAL K-250
			$total_semen_250_maret = $komposisi_250_produk_a * $volume_rencana_kerja_maret_produk_c;
			$total_pasir_250_maret = $komposisi_250_produk_b * $volume_rencana_kerja_maret_produk_c;
			$total_batu1020_250_maret = $komposisi_250_produk_c * $volume_rencana_kerja_maret_produk_c;
			$total_batu2030_250_maret = $komposisi_250_produk_d * $volume_rencana_kerja_maret_produk_c;

			$nilai_semen_250_maret = $total_semen_250_maret * $komposisi_250['price_a'];
			$nilai_pasir_250_maret = $total_pasir_250_maret * $komposisi_250['price_b'];
			$nilai_batu1020_250_maret = $total_batu1020_250_maret * $komposisi_250['price_c'];
			$nilai_batu2030_250_maret = $total_batu2030_250_maret * $komposisi_250['price_d'];

			$total_250_maret = $nilai_semen_250_maret + $nilai_pasir_250_maret + $nilai_batu1020_250_maret + $nilai_batu2030_250_maret;

			//TOTAL K-250_18
			$total_semen_250_18_maret = $komposisi_250_18_produk_a * $volume_rencana_kerja_maret_produk_d;
			$total_pasir_250_18_maret = $komposisi_250_18_produk_b * $volume_rencana_kerja_maret_produk_d;
			$total_batu1020_250_18_maret = $komposisi_250_18_produk_c * $volume_rencana_kerja_maret_produk_d;
			$total_batu2030_250_18_maret = $komposisi_250_18_produk_d * $volume_rencana_kerja_maret_produk_d;

			$nilai_semen_250_18_maret = $total_semen_250_18_maret  * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_maret = $total_pasir_250_18_maret  * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_maret = $total_batu1020_250_18_maret  * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_maret = $total_batu2030_250_18_maret  * $komposisi_250_18['price_d'];

			$total_250_18_maret = $nilai_semen_250_18_maret  + $nilai_pasir_250_18_maret  + $nilai_batu1020_250_18_maret  + $nilai_batu2030_250_18_maret;

			//TOTAL ALL
			$total_bahan_all_maret = $total_125_maret  + $total_225_maret  + $total_250_maret  + $total_250_18_maret;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_maret = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_maret_akhir')")
			->get()->row_array();

			$batching_plant_maret = $total_maret_volume * $rap_alat_maret['batching_plant'];
			$truck_mixer_maret = $total_maret_volume * $rap_alat_maret['truck_mixer'];
			$wheel_loader_maret = $total_maret_volume * $rap_alat_maret['wheel_loader'];
			$bbm_solar_maret = $total_maret_volume * $rap_alat_maret['bbm_solar'];
			$biaya_alat_all_maret = $batching_plant_maret + $truck_mixer_maret + $wheel_loader_maret + $bbm_solar_maret;
		
			$total_maret_biaya_bahan = $total_bahan_all_maret;
			$total_maret_biaya_alat = $biaya_alat_all_maret;
			$total_maret_biaya_overhead = $rencana_kerja_maret['biaya_overhead'];
			$total_maret_biaya_bank = $rencana_kerja_maret['biaya_bank'];
			$total_maret_biaya_persiapan = $rencana_kerja_maret['biaya_persiapan'];

			$total_biaya_maret_biaya = $total_maret_biaya_bahan + $total_maret_biaya_alat + $total_maret_biaya_overhead + $total_maret_biaya_bank + $total_maret_biaya_persiapan;
			?>
			<!-- MARET -->

			<!-- APRIL -->
			<?php
			$date_april_awal = date('2023-04-01');
			$date_april_akhir = date('2023-04-30');
			$rencana_kerja_april = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_april_awal' and '$date_april_akhir'")
			->get()->row_array();
			$volume_april_produk_a = $rencana_kerja_april['vol_produk_a'];
			$volume_april_produk_b = $rencana_kerja_april['vol_produk_b'];
			$volume_april_produk_c = $rencana_kerja_april['vol_produk_c'];
			$volume_april_produk_d = $rencana_kerja_april['vol_produk_d'];

			$total_april_volume = $volume_april_produk_a + $volume_april_produk_b + $volume_april_produk_c + $volume_april_produk_d;

			$nilai_jual_125_april = $volume_april_produk_a * $harga_jual_125_now['harga_satuan'];
			$nilai_jual_225_april = $volume_april_produk_b * $harga_jual_225_now['harga_satuan'];
			$nilai_jual_250_april = $volume_april_produk_c * $harga_jual_250_now['harga_satuan'];
			$nilai_jual_250_18_april = $volume_april_produk_d * $harga_jual_250_18_now['harga_satuan'];
			$nilai_jual_all_april = $nilai_jual_125_april + $nilai_jual_225_april + $nilai_jual_250_april + $nilai_jual_250_18_april;

			$total_april_nilai = $nilai_jual_all_april;

			//TOTAL PEMAKAIAN BAHAN
			$rencana_kerja_april = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_april_awal' and '$date_april_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_april_produk_a = $rencana_kerja_april['vol_produk_a'];
			$volume_rencana_kerja_april_produk_b = $rencana_kerja_april['vol_produk_b'];
			$volume_rencana_kerja_april_produk_c = $rencana_kerja_april['vol_produk_c'];
			$volume_rencana_kerja_april_produk_d = $rencana_kerja_april['vol_produk_d'];

			//TOTAL K-125
			$total_semen_125_april = $komposisi_125_produk_a * $volume_rencana_kerja_april_produk_a;
			$total_pasir_125_april = $komposisi_125_produk_b * $volume_rencana_kerja_april_produk_a;
			$total_batu1020_125_april = $komposisi_125_produk_c * $volume_rencana_kerja_april_produk_a;
			$total_batu2030_125_april = $komposisi_125_produk_d * $volume_rencana_kerja_april_produk_a;

			$nilai_semen_125_april = $total_semen_125_april * $komposisi_125['price_a'];
			$nilai_pasir_125_april = $total_pasir_125_april * $komposisi_125['price_b'];
			$nilai_batu1020_125_april = $total_batu1020_125_april * $komposisi_125['price_c'];
			$nilai_batu2030_125_april = $total_batu2030_125_april * $komposisi_125['price_d'];

			$total_125_april = $nilai_semen_125_april + $nilai_pasir_125_april + $nilai_batu1020_125_april + $nilai_batu2030_125_april;

			//TOTAL K-225
			$total_semen_225_april = $komposisi_225_produk_a * $volume_rencana_kerja_april_produk_b;
			$total_pasir_225_april = $komposisi_225_produk_b * $volume_rencana_kerja_april_produk_b;
			$total_batu1020_225_april = $komposisi_225_produk_c * $volume_rencana_kerja_april_produk_b;
			$total_batu2030_225_april = $komposisi_225_produk_d * $volume_rencana_kerja_april_produk_b;

			$nilai_semen_225_april = $total_semen_225_april * $komposisi_225['price_a'];
			$nilai_pasir_225_april = $total_pasir_225_april * $komposisi_225['price_b'];
			$nilai_batu1020_225_april = $total_batu1020_225_april * $komposisi_225['price_c'];
			$nilai_batu2030_225_april = $total_batu2030_225_april * $komposisi_225['price_d'];

			$total_225_april = $nilai_semen_225_april + $nilai_pasir_225_april + $nilai_batu1020_225_april + $nilai_batu2030_225_april;

			//TOTAL K-250
			$total_semen_250_april = $komposisi_250_produk_a * $volume_rencana_kerja_april_produk_c;
			$total_pasir_250_april = $komposisi_250_produk_b * $volume_rencana_kerja_april_produk_c;
			$total_batu1020_250_april = $komposisi_250_produk_c * $volume_rencana_kerja_april_produk_c;
			$total_batu2030_250_april = $komposisi_250_produk_d * $volume_rencana_kerja_april_produk_c;

			$nilai_semen_250_april = $total_semen_250_april * $komposisi_250['price_a'];
			$nilai_pasir_250_april = $total_pasir_250_april * $komposisi_250['price_b'];
			$nilai_batu1020_250_april = $total_batu1020_250_april * $komposisi_250['price_c'];
			$nilai_batu2030_250_april = $total_batu2030_250_april * $komposisi_250['price_d'];

			$total_250_april = $nilai_semen_250_april + $nilai_pasir_250_april + $nilai_batu1020_250_april + $nilai_batu2030_250_april;

			//TOTAL K-250_18
			$total_semen_250_18_april = $komposisi_250_18_produk_a * $volume_rencana_kerja_april_produk_d;
			$total_pasir_250_18_april = $komposisi_250_18_produk_b * $volume_rencana_kerja_april_produk_d;
			$total_batu1020_250_18_april = $komposisi_250_18_produk_c * $volume_rencana_kerja_april_produk_d;
			$total_batu2030_250_18_april = $komposisi_250_18_produk_d * $volume_rencana_kerja_april_produk_d;

			$nilai_semen_250_18_april = $total_semen_250_18_april  * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_april = $total_pasir_250_18_april  * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_april = $total_batu1020_250_18_april  * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_april = $total_batu2030_250_18_april  * $komposisi_250_18['price_d'];

			$total_250_18_april = $nilai_semen_250_18_april  + $nilai_pasir_250_18_april  + $nilai_batu1020_250_18_april  + $nilai_batu2030_250_18_april;

			//TOTAL ALL
			$total_bahan_all_april = $total_125_april  + $total_225_april  + $total_250_april  + $total_250_18_april;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_april = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_april_akhir')")
			->get()->row_array();

			$batching_plant_april = $total_april_volume * $rap_alat_april['batching_plant'];
			$truck_mixer_april = $total_april_volume * $rap_alat_april['truck_mixer'];
			$wheel_loader_april = $total_april_volume * $rap_alat_april['wheel_loader'];
			$bbm_solar_april = $total_april_volume * $rap_alat_april['bbm_solar'];
			$biaya_alat_all_april = $batching_plant_april + $truck_mixer_april + $wheel_loader_april + $bbm_solar_april;
		
			$total_april_biaya_bahan = $total_bahan_all_april;
			$total_april_biaya_alat = $biaya_alat_all_april;
			$total_april_biaya_overhead = $rencana_kerja_april['biaya_overhead'];
			$total_april_biaya_bank = $rencana_kerja_april['biaya_bank'];
			$total_april_biaya_persiapan = $rencana_kerja_april['biaya_persiapan'];

			$total_biaya_april_biaya = $total_april_biaya_bahan + $total_april_biaya_alat + $total_april_biaya_overhead + $total_april_biaya_bank + $total_april_biaya_persiapan;
			?>
			<!-- APRIL -->

			<!-- MEI -->
			<?php
			$date_mei_awal = date('2023-05-01');
			$date_mei_akhir = date('2023-05-31');
			$rencana_kerja_mei = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_mei_awal' and '$date_mei_akhir'")
			->get()->row_array();
			$volume_mei_produk_a = $rencana_kerja_mei['vol_produk_a'];
			$volume_mei_produk_b = $rencana_kerja_mei['vol_produk_b'];
			$volume_mei_produk_c = $rencana_kerja_mei['vol_produk_c'];
			$volume_mei_produk_d = $rencana_kerja_mei['vol_produk_d'];

			$total_mei_volume = $volume_mei_produk_a + $volume_mei_produk_b + $volume_mei_produk_c + $volume_mei_produk_d;

			$nilai_jual_125_mei = $volume_mei_produk_a * $harga_jual_125_now['harga_satuan'];
			$nilai_jual_225_mei = $volume_mei_produk_b * $harga_jual_225_now['harga_satuan'];
			$nilai_jual_250_mei = $volume_mei_produk_c * $harga_jual_250_now['harga_satuan'];
			$nilai_jual_250_18_mei = $volume_mei_produk_d * $harga_jual_250_18_now['harga_satuan'];
			$nilai_jual_all_mei = $nilai_jual_125_mei + $nilai_jual_225_mei + $nilai_jual_250_mei + $nilai_jual_250_18_mei;

			$total_mei_nilai = $nilai_jual_all_mei;

			//TOTAL PEMAKAIAN BAHAN
			$rencana_kerja_mei = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_mei_awal' and '$date_mei_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_mei_produk_a = $rencana_kerja_mei['vol_produk_a'];
			$volume_rencana_kerja_mei_produk_b = $rencana_kerja_mei['vol_produk_b'];
			$volume_rencana_kerja_mei_produk_c = $rencana_kerja_mei['vol_produk_c'];
			$volume_rencana_kerja_mei_produk_d = $rencana_kerja_mei['vol_produk_d'];

			//TOTAL K-125
			$total_semen_125_mei = $komposisi_125_produk_a * $volume_rencana_kerja_mei_produk_a;
			$total_pasir_125_mei = $komposisi_125_produk_b * $volume_rencana_kerja_mei_produk_a;
			$total_batu1020_125_mei = $komposisi_125_produk_c * $volume_rencana_kerja_mei_produk_a;
			$total_batu2030_125_mei = $komposisi_125_produk_d * $volume_rencana_kerja_mei_produk_a;

			$nilai_semen_125_mei = $total_semen_125_mei * $komposisi_125['price_a'];
			$nilai_pasir_125_mei = $total_pasir_125_mei * $komposisi_125['price_b'];
			$nilai_batu1020_125_mei = $total_batu1020_125_mei * $komposisi_125['price_c'];
			$nilai_batu2030_125_mei = $total_batu2030_125_mei * $komposisi_125['price_d'];

			$total_125_mei = $nilai_semen_125_mei + $nilai_pasir_125_mei + $nilai_batu1020_125_mei + $nilai_batu2030_125_mei;

			//TOTAL K-225
			$total_semen_225_mei = $komposisi_225_produk_a * $volume_rencana_kerja_mei_produk_b;
			$total_pasir_225_mei = $komposisi_225_produk_b * $volume_rencana_kerja_mei_produk_b;
			$total_batu1020_225_mei = $komposisi_225_produk_c * $volume_rencana_kerja_mei_produk_b;
			$total_batu2030_225_mei = $komposisi_225_produk_d * $volume_rencana_kerja_mei_produk_b;

			$nilai_semen_225_mei = $total_semen_225_mei * $komposisi_225['price_a'];
			$nilai_pasir_225_mei = $total_pasir_225_mei * $komposisi_225['price_b'];
			$nilai_batu1020_225_mei = $total_batu1020_225_mei * $komposisi_225['price_c'];
			$nilai_batu2030_225_mei = $total_batu2030_225_mei * $komposisi_225['price_d'];

			$total_225_mei = $nilai_semen_225_mei + $nilai_pasir_225_mei + $nilai_batu1020_225_mei + $nilai_batu2030_225_mei;

			//TOTAL K-250
			$total_semen_250_mei = $komposisi_250_produk_a * $volume_rencana_kerja_mei_produk_c;
			$total_pasir_250_mei = $komposisi_250_produk_b * $volume_rencana_kerja_mei_produk_c;
			$total_batu1020_250_mei = $komposisi_250_produk_c * $volume_rencana_kerja_mei_produk_c;
			$total_batu2030_250_mei = $komposisi_250_produk_d * $volume_rencana_kerja_mei_produk_c;

			$nilai_semen_250_mei = $total_semen_250_mei * $komposisi_250['price_a'];
			$nilai_pasir_250_mei = $total_pasir_250_mei * $komposisi_250['price_b'];
			$nilai_batu1020_250_mei = $total_batu1020_250_mei * $komposisi_250['price_c'];
			$nilai_batu2030_250_mei = $total_batu2030_250_mei * $komposisi_250['price_d'];

			$total_250_mei = $nilai_semen_250_mei + $nilai_pasir_250_mei + $nilai_batu1020_250_mei + $nilai_batu2030_250_mei;

			//TOTAL K-250_18
			$total_semen_250_18_mei = $komposisi_250_18_produk_a * $volume_rencana_kerja_mei_produk_d;
			$total_pasir_250_18_mei = $komposisi_250_18_produk_b * $volume_rencana_kerja_mei_produk_d;
			$total_batu1020_250_18_mei = $komposisi_250_18_produk_c * $volume_rencana_kerja_mei_produk_d;
			$total_batu2030_250_18_mei = $komposisi_250_18_produk_d * $volume_rencana_kerja_mei_produk_d;

			$nilai_semen_250_18_mei = $total_semen_250_18_mei  * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_mei = $total_pasir_250_18_mei  * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_mei = $total_batu1020_250_18_mei  * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_mei = $total_batu2030_250_18_mei  * $komposisi_250_18['price_d'];

			$total_250_18_mei = $nilai_semen_250_18_mei  + $nilai_pasir_250_18_mei  + $nilai_batu1020_250_18_mei  + $nilai_batu2030_250_18_mei;

			//TOTAL ALL
			$total_bahan_all_mei = $total_125_mei  + $total_225_mei  + $total_250_mei  + $total_250_18_mei;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_mei = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_mei_akhir')")
			->get()->row_array();

			$batching_plant_mei = $total_mei_volume * $rap_alat_mei['batching_plant'];
			$truck_mixer_mei = $total_mei_volume * $rap_alat_mei['truck_mixer'];
			$wheel_loader_mei = $total_mei_volume * $rap_alat_mei['wheel_loader'];
			$bbm_solar_mei = $total_mei_volume * $rap_alat_mei['bbm_solar'];
			$biaya_alat_all_mei = $batching_plant_mei + $truck_mixer_mei + $wheel_loader_mei + $bbm_solar_mei;
		
			$total_mei_biaya_bahan = $total_bahan_all_mei;
			$total_mei_biaya_alat = $biaya_alat_all_mei;
			$total_mei_biaya_overhead = $rencana_kerja_mei['biaya_overhead'];
			$total_mei_biaya_bank = $rencana_kerja_mei['biaya_bank'];
			$total_mei_biaya_persiapan = $rencana_kerja_mei['biaya_persiapan'];

			$total_biaya_mei_biaya = $total_mei_biaya_bahan + $total_mei_biaya_alat + $total_mei_biaya_overhead + $total_mei_biaya_bank + $total_mei_biaya_persiapan;
			?>
			<!-- MEI -->

			<!-- JUNI -->
			<?php
			$date_juni_awal = date('2023-06-01');
			$date_juni_akhir = date('2023-06-30');
			$rencana_kerja_juni = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_juni_awal' and '$date_juni_akhir'")
			->get()->row_array();
			$volume_juni_produk_a = $rencana_kerja_juni['vol_produk_a'];
			$volume_juni_produk_b = $rencana_kerja_juni['vol_produk_b'];
			$volume_juni_produk_c = $rencana_kerja_juni['vol_produk_c'];
			$volume_juni_produk_d = $rencana_kerja_juni['vol_produk_d'];

			$total_juni_volume = $volume_juni_produk_a + $volume_juni_produk_b + $volume_juni_produk_c + $volume_juni_produk_d;

			$nilai_jual_125_juni = $volume_juni_produk_a * $harga_jual_125_now['harga_satuan'];
			$nilai_jual_225_juni = $volume_juni_produk_b * $harga_jual_225_now['harga_satuan'];
			$nilai_jual_250_juni = $volume_juni_produk_c * $harga_jual_250_now['harga_satuan'];
			$nilai_jual_250_18_juni = $volume_juni_produk_d * $harga_jual_250_18_now['harga_satuan'];
			$nilai_jual_all_juni = $nilai_jual_125_juni + $nilai_jual_225_juni + $nilai_jual_250_juni + $nilai_jual_250_18_juni;

			$total_juni_nilai = $nilai_jual_all_juni;

			//TOTAL PEMAKAIAN BAHAN
			$rencana_kerja_juni = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_juni_awal' and '$date_juni_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_juni_produk_a = $rencana_kerja_juni['vol_produk_a'];
			$volume_rencana_kerja_juni_produk_b = $rencana_kerja_juni['vol_produk_b'];
			$volume_rencana_kerja_juni_produk_c = $rencana_kerja_juni['vol_produk_c'];
			$volume_rencana_kerja_juni_produk_d = $rencana_kerja_juni['vol_produk_d'];

			//TOTAL K-125
			$total_semen_125_juni = $komposisi_125_produk_a * $volume_rencana_kerja_juni_produk_a;
			$total_pasir_125_juni = $komposisi_125_produk_b * $volume_rencana_kerja_juni_produk_a;
			$total_batu1020_125_juni = $komposisi_125_produk_c * $volume_rencana_kerja_juni_produk_a;
			$total_batu2030_125_juni = $komposisi_125_produk_d * $volume_rencana_kerja_juni_produk_a;

			$nilai_semen_125_juni = $total_semen_125_juni * $komposisi_125['price_a'];
			$nilai_pasir_125_juni = $total_pasir_125_juni * $komposisi_125['price_b'];
			$nilai_batu1020_125_juni = $total_batu1020_125_juni * $komposisi_125['price_c'];
			$nilai_batu2030_125_juni = $total_batu2030_125_juni * $komposisi_125['price_d'];

			$total_125_juni = $nilai_semen_125_juni + $nilai_pasir_125_juni + $nilai_batu1020_125_juni + $nilai_batu2030_125_juni;

			//TOTAL K-225
			$total_semen_225_juni = $komposisi_225_produk_a * $volume_rencana_kerja_juni_produk_b;
			$total_pasir_225_juni = $komposisi_225_produk_b * $volume_rencana_kerja_juni_produk_b;
			$total_batu1020_225_juni = $komposisi_225_produk_c * $volume_rencana_kerja_juni_produk_b;
			$total_batu2030_225_juni = $komposisi_225_produk_d * $volume_rencana_kerja_juni_produk_b;

			$nilai_semen_225_juni = $total_semen_225_juni * $komposisi_225['price_a'];
			$nilai_pasir_225_juni = $total_pasir_225_juni * $komposisi_225['price_b'];
			$nilai_batu1020_225_juni = $total_batu1020_225_juni * $komposisi_225['price_c'];
			$nilai_batu2030_225_juni = $total_batu2030_225_juni * $komposisi_225['price_d'];

			$total_225_juni = $nilai_semen_225_juni + $nilai_pasir_225_juni + $nilai_batu1020_225_juni + $nilai_batu2030_225_juni;

			//TOTAL K-250
			$total_semen_250_juni = $komposisi_250_produk_a * $volume_rencana_kerja_juni_produk_c;
			$total_pasir_250_juni = $komposisi_250_produk_b * $volume_rencana_kerja_juni_produk_c;
			$total_batu1020_250_juni = $komposisi_250_produk_c * $volume_rencana_kerja_juni_produk_c;
			$total_batu2030_250_juni = $komposisi_250_produk_d * $volume_rencana_kerja_juni_produk_c;

			$nilai_semen_250_juni = $total_semen_250_juni * $komposisi_250['price_a'];
			$nilai_pasir_250_juni = $total_pasir_250_juni * $komposisi_250['price_b'];
			$nilai_batu1020_250_juni = $total_batu1020_250_juni * $komposisi_250['price_c'];
			$nilai_batu2030_250_juni = $total_batu2030_250_juni * $komposisi_250['price_d'];

			$total_250_juni = $nilai_semen_250_juni + $nilai_pasir_250_juni + $nilai_batu1020_250_juni + $nilai_batu2030_250_juni;

			//TOTAL K-250_18
			$total_semen_250_18_juni = $komposisi_250_18_produk_a * $volume_rencana_kerja_juni_produk_d;
			$total_pasir_250_18_juni = $komposisi_250_18_produk_b * $volume_rencana_kerja_juni_produk_d;
			$total_batu1020_250_18_juni = $komposisi_250_18_produk_c * $volume_rencana_kerja_juni_produk_d;
			$total_batu2030_250_18_juni = $komposisi_250_18_produk_d * $volume_rencana_kerja_juni_produk_d;

			$nilai_semen_250_18_juni = $total_semen_250_18_juni  * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_juni = $total_pasir_250_18_juni  * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_juni = $total_batu1020_250_18_juni  * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_juni = $total_batu2030_250_18_juni  * $komposisi_250_18['price_d'];

			$total_250_18_juni = $nilai_semen_250_18_juni  + $nilai_pasir_250_18_juni  + $nilai_batu1020_250_18_juni  + $nilai_batu2030_250_18_juni;

			//TOTAL ALL
			$total_bahan_all_juni = $total_125_juni  + $total_225_juni  + $total_250_juni  + $total_250_18_juni;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_juni = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_juni_akhir')")
			->get()->row_array();

			$batching_plant_juni = $total_juni_volume * $rap_alat_juni['batching_plant'];
			$truck_mixer_juni = $total_juni_volume * $rap_alat_juni['truck_mixer'];
			$wheel_loader_juni = $total_juni_volume * $rap_alat_juni['wheel_loader'];
			$bbm_solar_juni = $total_juni_volume * $rap_alat_juni['bbm_solar'];
			$biaya_alat_all_juni = $batching_plant_juni + $truck_mixer_juni + $wheel_loader_juni + $bbm_solar_juni;
		
			$total_juni_biaya_bahan = $total_bahan_all_juni;
			$total_juni_biaya_alat = $biaya_alat_all_juni;
			$total_juni_biaya_overhead = $rencana_kerja_juni['biaya_overhead'];
			$total_juni_biaya_bank = $rencana_kerja_juni['biaya_bank'];
			$total_juni_biaya_persiapan = $rencana_kerja_juni['biaya_persiapan'];

			$total_biaya_juni_biaya = $total_juni_biaya_bahan + $total_juni_biaya_alat + $total_juni_biaya_overhead + $total_juni_biaya_bank + $total_juni_biaya_persiapan;
			?>
			<!-- JUNI -->

			<!-- JULI -->
			<?php
			$date_juli_awal = date('2023-07-01');
			$date_juli_akhir = date('2023-07-31');
			$rencana_kerja_juli = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_juli_awal' and '$date_juli_akhir'")
			->get()->row_array();
			$volume_juli_produk_a = $rencana_kerja_juli['vol_produk_a'];
			$volume_juli_produk_b = $rencana_kerja_juli['vol_produk_b'];
			$volume_juli_produk_c = $rencana_kerja_juli['vol_produk_c'];
			$volume_juli_produk_d = $rencana_kerja_juli['vol_produk_d'];

			$total_juli_volume = $volume_juli_produk_a + $volume_juli_produk_b + $volume_juli_produk_c + $volume_juli_produk_d;

			$nilai_jual_125_juli = $volume_juli_produk_a * $harga_jual_125_now['harga_satuan'];
			$nilai_jual_225_juli = $volume_juli_produk_b * $harga_jual_225_now['harga_satuan'];
			$nilai_jual_250_juli = $volume_juli_produk_c * $harga_jual_250_now['harga_satuan'];
			$nilai_jual_250_18_juli = $volume_juli_produk_d * $harga_jual_250_18_now['harga_satuan'];
			$nilai_jual_all_juli = $nilai_jual_125_juli + $nilai_jual_225_juli + $nilai_jual_250_juli + $nilai_jual_250_18_juli;

			$total_juli_nilai = $nilai_jual_all_juli;

			//TOTAL PEMAKAIAN BAHAN
			$rencana_kerja_juli = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_juli_awal' and '$date_juli_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_juli_produk_a = $rencana_kerja_juli['vol_produk_a'];
			$volume_rencana_kerja_juli_produk_b = $rencana_kerja_juli['vol_produk_b'];
			$volume_rencana_kerja_juli_produk_c = $rencana_kerja_juli['vol_produk_c'];
			$volume_rencana_kerja_juli_produk_d = $rencana_kerja_juli['vol_produk_d'];

			//TOTAL K-125
			$total_semen_125_juli = $komposisi_125_produk_a * $volume_rencana_kerja_juli_produk_a;
			$total_pasir_125_juli = $komposisi_125_produk_b * $volume_rencana_kerja_juli_produk_a;
			$total_batu1020_125_juli = $komposisi_125_produk_c * $volume_rencana_kerja_juli_produk_a;
			$total_batu2030_125_juli = $komposisi_125_produk_d * $volume_rencana_kerja_juli_produk_a;

			$nilai_semen_125_juli = $total_semen_125_juli * $komposisi_125['price_a'];
			$nilai_pasir_125_juli = $total_pasir_125_juli * $komposisi_125['price_b'];
			$nilai_batu1020_125_juli = $total_batu1020_125_juli * $komposisi_125['price_c'];
			$nilai_batu2030_125_juli = $total_batu2030_125_juli * $komposisi_125['price_d'];

			$total_125_juli = $nilai_semen_125_juli + $nilai_pasir_125_juli + $nilai_batu1020_125_juli + $nilai_batu2030_125_juli;

			//TOTAL K-225
			$total_semen_225_juli = $komposisi_225_produk_a * $volume_rencana_kerja_juli_produk_b;
			$total_pasir_225_juli = $komposisi_225_produk_b * $volume_rencana_kerja_juli_produk_b;
			$total_batu1020_225_juli = $komposisi_225_produk_c * $volume_rencana_kerja_juli_produk_b;
			$total_batu2030_225_juli = $komposisi_225_produk_d * $volume_rencana_kerja_juli_produk_b;

			$nilai_semen_225_juli = $total_semen_225_juli * $komposisi_225['price_a'];
			$nilai_pasir_225_juli = $total_pasir_225_juli * $komposisi_225['price_b'];
			$nilai_batu1020_225_juli = $total_batu1020_225_juli * $komposisi_225['price_c'];
			$nilai_batu2030_225_juli = $total_batu2030_225_juli * $komposisi_225['price_d'];

			$total_225_juli = $nilai_semen_225_juli + $nilai_pasir_225_juli + $nilai_batu1020_225_juli + $nilai_batu2030_225_juli;

			//TOTAL K-250
			$total_semen_250_juli = $komposisi_250_produk_a * $volume_rencana_kerja_juli_produk_c;
			$total_pasir_250_juli = $komposisi_250_produk_b * $volume_rencana_kerja_juli_produk_c;
			$total_batu1020_250_juli = $komposisi_250_produk_c * $volume_rencana_kerja_juli_produk_c;
			$total_batu2030_250_juli = $komposisi_250_produk_d * $volume_rencana_kerja_juli_produk_c;

			$nilai_semen_250_juli = $total_semen_250_juli * $komposisi_250['price_a'];
			$nilai_pasir_250_juli = $total_pasir_250_juli * $komposisi_250['price_b'];
			$nilai_batu1020_250_juli = $total_batu1020_250_juli * $komposisi_250['price_c'];
			$nilai_batu2030_250_juli = $total_batu2030_250_juli * $komposisi_250['price_d'];

			$total_250_juli = $nilai_semen_250_juli + $nilai_pasir_250_juli + $nilai_batu1020_250_juli + $nilai_batu2030_250_juli;

			//TOTAL K-250_18
			$total_semen_250_18_juli = $komposisi_250_18_produk_a * $volume_rencana_kerja_juli_produk_d;
			$total_pasir_250_18_juli = $komposisi_250_18_produk_b * $volume_rencana_kerja_juli_produk_d;
			$total_batu1020_250_18_juli = $komposisi_250_18_produk_c * $volume_rencana_kerja_juli_produk_d;
			$total_batu2030_250_18_juli = $komposisi_250_18_produk_d * $volume_rencana_kerja_juli_produk_d;

			$nilai_semen_250_18_juli = $total_semen_250_18_juli  * $komposisi_250_18['price_a'];
			$nilai_pasir_250_18_juli = $total_pasir_250_18_juli  * $komposisi_250_18['price_b'];
			$nilai_batu1020_250_18_juli = $total_batu1020_250_18_juli  * $komposisi_250_18['price_c'];
			$nilai_batu2030_250_18_juli = $total_batu2030_250_18_juli  * $komposisi_250_18['price_d'];

			$total_250_18_juli = $nilai_semen_250_18_juli  + $nilai_pasir_250_18_juli  + $nilai_batu1020_250_18_juli  + $nilai_batu2030_250_18_juli;

			//TOTAL ALL
			$total_bahan_all_juli = $total_125_juli  + $total_225_juli  + $total_250_juli  + $total_250_18_juli;
			//END TOTAL PEMAKAIAN BAHAN

			//TOTAL PEMAKAIAN ALAT
			$rap_alat_juli = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->where("(rap.tanggal_rap_alat < '$date_juli_akhir')")
			->get()->row_array();

			$batching_plant_juli = $total_juli_volume * $rap_alat_juli['batching_plant'];
			$truck_mixer_juli = $total_juli_volume * $rap_alat_juli['truck_mixer'];
			$wheel_loader_juli = $total_juli_volume * $rap_alat_juli['wheel_loader'];
			$bbm_solar_juli = $total_juli_volume * $rap_alat_juli['bbm_solar'];
			$biaya_alat_all_juli = $batching_plant_juli + $truck_mixer_juli + $wheel_loader_juli + $bbm_solar_juli;
		
			$total_juli_biaya_bahan = $total_bahan_all_juli;
			$total_juli_biaya_alat = $biaya_alat_all_juli;
			$total_juli_biaya_overhead = $rencana_kerja_juli['biaya_overhead'];
			$total_juli_biaya_bank = $rencana_kerja_juli['biaya_bank'];
			$total_juli_biaya_persiapan = $rencana_kerja_juli['biaya_persiapan'];

			$total_biaya_juli_biaya = $total_juli_biaya_bahan + $total_juli_biaya_alat + $total_juli_biaya_overhead + $total_juli_biaya_bank + $total_juli_biaya_persiapan;
			?>
			<!-- JULI -->

			<!-- AKUMULASI BIAYA SD. SAAT INI -->
			<?php
			//BAHAN
			$akumulasi = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar as total_nilai_keluar')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi <= '$date_now')")
			->get()->result_array();

			$total_akumulasi = 0;

			foreach ($akumulasi as $a){
				$total_akumulasi += $a['total_nilai_keluar'];
			}

			$total_bahan_akumulasi = $total_akumulasi;
			//END BAHAN
			?>

			<?php
			//ALAT
			$nilai_alat = $this->db->select('SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("(prm.date_receipt <= '$date_now')")
			->where("p.kategori_produk = '5'")
			->where("po.status in ('PUBLISH','CLOSED')")
			->get()->row_array();

			$akumulasi_bbm = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar_2 as total_nilai_keluar_2')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi <= '$date_now')")
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
			->where("(tanggal_transaksi <= '$date_now')")
			->get()->row_array();

			$total_insentif_tm = $insentif_tm['total'];

			$total_alat_akumulasi = $nilai_alat['nilai'] + $total_akumulasi_bbm + $total_insentif_tm;
			//END ALAT
			?>

			<?php
			//OVERHEAD
			$overhead_15_akumulasi = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',15)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$date_now')")
			->get()->row_array();

			$overhead_jurnal_15_akumulasi = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',15)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$date_now')")
			->get()->row_array();

			$overhead_16_akumulasi = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',16)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$date_now')")
			->get()->row_array();

			$overhead_jurnal_16_akumulasi = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',16)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$date_now')")
			->get()->row_array();

			$overhead_17_akumulasi = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',17)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$date_now')")
			->get()->row_array();

			$overhead_jurnal_17_akumulasi = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',17)
			->where("c.id <> 220 ")
			->where("c.id <> 168 ")
			->where("c.id <> 228 ")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$date_now')")
			->get()->row_array();

			$total_overhead_akumulasi =  $overhead_15_akumulasi['total'] + $overhead_jurnal_15_akumulasi['total'] + $overhead_16_akumulasi['total'] + $overhead_jurnal_16_akumulasi['total'] + $overhead_17_akumulasi['total'] + $overhead_jurnal_17_akumulasi['total'];
			?>

			<?php
			//DISKONTO
			$diskonto_akumulasi = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 168")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$date_now')")
			->get()->row_array();

			$total_diskonto_akumulasi = $diskonto_akumulasi['total'];
			//DISKONTO
			?>

			<?php
			//PERSIAPAN
			$persiapan_biaya_akumulasi = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 228")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$date_now')")
			->get()->row_array();

			$persiapan_jurnal_akumulasi = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 228")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$date_now')")
			->get()->row_array();

			$total_persiapan_akumulasi = $persiapan_biaya_akumulasi['total'] + $persiapan_jurnal_akumulasi['total'];
			$total_biaya_akumulasi = $total_bahan_akumulasi + $total_alat_akumulasi + $total_overhead_akumulasi + $total_diskonto_akumulasi + $total_persiapan_akumulasi;
			//END PERSIAPAN
			?>
			<!-- AKUMULASI BIAYA SD. SAAT INI -->

			<!-- TOTAL -->
			<?php
			$total_all_produk_a = $volume_akumulasi_produk_a + $volume_november_produk_a + $volume_desember_produk_a + $volume_januari_produk_a + $volume_februari_produk_a + $volume_maret_produk_a + $volume_april_produk_a + $volume_mei_produk_a + $volume_juni_produk_a + $volume_juli_produk_a;
			$total_all_produk_b = $volume_akumulasi_produk_b + $volume_november_produk_b + $volume_desember_produk_b + $volume_januari_produk_b + $volume_februari_produk_b + $volume_maret_produk_b + $volume_april_produk_b + $volume_mei_produk_b + $volume_juni_produk_b + $volume_juli_produk_b;
			$total_all_produk_c = $volume_akumulasi_produk_c + $volume_november_produk_c + $volume_desember_produk_c + $volume_januari_produk_c + $volume_februari_produk_c + $volume_maret_produk_c + $volume_april_produk_c + $volume_mei_produk_c + $volume_juni_produk_c + $volume_juli_produk_c;
			$total_all_produk_d = $volume_akumulasi_produk_d + $volume_november_produk_d + $volume_desember_produk_d+ $volume_januari_produk_d + $volume_februari_produk_d + $volume_maret_produk_d + $volume_april_produk_d + $volume_mei_produk_d + $volume_juni_produk_d + $volume_juli_produk_d;

			$total_all_volume = $total_akumulasi_volume + $total_november_volume + $total_desember_volume + $total_januari_volume + $total_februari_volume + $total_maret_volume + $total_april_volume + $total_mei_volume + $total_juni_volume + $total_juli_volume;
			$total_all_nilai = $total_akumulasi_nilai + $total_november_nilai + $total_desember_nilai + $total_januari_nilai +  + $total_februari_nilai +  + $total_maret_nilai + $total_april_nilai + $total_mei_nilai + $total_juni_nilai + $total_juli_nilai;

			$total_all_biaya_bahan = $total_bahan_akumulasi + $total_november_biaya_bahan + $total_desember_biaya_bahan + $total_januari_biaya_bahan + $total_februari_biaya_bahan + $total_maret_biaya_bahan + $total_april_biaya_bahan + $total_mei_biaya_bahan + $total_juni_biaya_bahan + $total_juli_biaya_bahan;
			$total_all_biaya_alat = $total_alat_akumulasi + $total_november_biaya_alat + $total_desember_biaya_alat + $total_januari_biaya_alat + $total_februari_biaya_alat + $total_maret_biaya_alat + $total_april_biaya_alat + $total_mei_biaya_alat + $total_juni_biaya_alat + $total_juli_biaya_alat;
			$total_all_biaya_overhead = $total_overhead_akumulasi + $total_november_biaya_overhead + $total_desember_biaya_overhead + $total_januari_biaya_overhead + $total_februari_biaya_overhead + $total_maret_biaya_overhead + $total_april_biaya_overhead + $total_mei_biaya_overhead + $total_juni_biaya_overhead + $total_juli_biaya_overhead;
			$total_all_biaya_bank = $total_diskonto_akumulasi + $total_november_biaya_bank + $total_desember_biaya_bank + $total_januari_biaya_bank + $total_februari_biaya_bank + $total_maret_biaya_bank + $total_april_biaya_bank + $total_mei_biaya_bank + $total_juni_biaya_bank + $total_juli_biaya_bank;
			$total_all_biaya_persiapan = $total_persiapan_akumulasi + $total_november_biaya_persiapan + $total_desember_biaya_persiapan + $total_januari_biaya_persiapan + $total_februari_biaya_persiapan + $total_maret_biaya_persiapan + $total_april_biaya_persiapan + $total_mei_biaya_persiapan + $total_juni_biaya_persiapan + $total_juli_biaya_persiapan;
			
			$total_biaya_all_biaya = $total_all_biaya_bahan + $total_all_biaya_alat + $total_all_biaya_overhead + $total_all_biaya_bank + $total_all_biaya_persiapan;

			$total_laba_rap_2022 = $total_rap_nilai_2022 - $total_biaya_rap_2022_biaya;
			$total_laba_sd_agustus = $total_akumulasi_nilai - $total_biaya_akumulasi;
			$total_laba_november = $total_november_nilai - $total_biaya_november_biaya;
			$total_laba_desember = $total_desember_nilai - $total_biaya_desember_biaya;
			$total_laba_januari = $total_januari_nilai - $total_biaya_januari_biaya;
			$total_laba_februari = $total_februari_nilai - $total_biaya_februari_biaya;
			$total_laba_maret = $total_maret_nilai - $total_biaya_maret_biaya;
			$total_laba_april = $total_april_nilai - $total_biaya_april_biaya;
			$total_laba_mei = $total_mei_nilai - $total_biaya_mei_biaya;
			$total_laba_juni = $total_juni_nilai - $total_biaya_juni_biaya;
			$total_laba_juli = $total_juli_nilai - $total_biaya_juli_biaya;
			$total_laba_all = $total_all_nilai - $total_biaya_all_biaya;
			?>
			<!-- TOTAL -->

			<!-- SISA -->
			<?php
			$sisa_vol_produk_a = $volume_rap_2022_produk_a - $total_all_produk_a;
			$sisa_vol_produk_b = $volume_rap_2022_produk_b - $total_all_produk_b;
			$sisa_vol_produk_c = $volume_rap_2022_produk_c - $total_all_produk_c;
			$sisa_vol_produk_d = $volume_rap_2022_produk_d - $total_all_produk_d;
			$sisa_all_volume = $total_rap_volume_2022 - $total_all_volume;
			$sisa_all_nilai = $total_rap_nilai_2022 - $total_all_nilai;

			$sisa_all_biaya_bahan = $total_rap_2022_biaya_bahan - $total_all_biaya_bahan;
			$sisa_all_biaya_alat = $total_rap_2022_biaya_alat - $total_all_biaya_alat;
			$sisa_all_biaya_overhead = $total_rap_2022_biaya_overhead - $total_all_biaya_overhead ;
			$sisa_all_biaya_bank = $total_rap_2022_biaya_bank - $total_all_biaya_bank;
			$sisa_all_biaya_persiapan = $total_rap_2022_biaya_persiapan - $total_all_biaya_persiapan;
			$sisa_biaya_all_biaya = $total_biaya_rap_2022_biaya - $total_biaya_all_biaya;
			$x = $total_laba_rap_2022;
			$y = - $total_laba_all;
			$sisa_laba_all = $x - $y;
			?>
			<!-- SISA -->

			<tr class="table-active4-rak">
				<th width="5%" class="text-center" rowspan="3" style="vertical-align:middle">NO.</th>
				<th class="text-center" rowspan="3" style="vertical-align:middle">URAIAN</th>
				<th class="text-center" rowspan="3" style="vertical-align:middle">SATUAN</th>
				<th class="text-center" rowspan="3" style="vertical-align:middle">RAP 2022</th>
				<th class="text-center" rowspan="2" style="vertical-align:middle">REALISASI</th>
				<th class="text-center" colspan="9">RENCANA KERJA</th>
				<th class="text-center" rowspan="3" style="vertical-align:middle">TOTAL</th>
				<th class="text-center" rowspan="3" style="vertical-align:middle">SISA</th>
	        </tr>
			<tr class="table-active4-rak">
				<th class="text-center">NOVEMBER</th>
				<th class="text-center">DESEMBER</th>
				<th class="text-center">JANUARI</th>
				<th class="text-center">FEBRUARI</th>
				<th class="text-center">MARET</th>
				<th class="text-center">APRIL</th>
				<th class="text-center">MEI</th>
				<th class="text-center">JUNI</th>
				<th class="text-center">JULI</th>
	        </tr>
			<tr class="table-active4-rak">
				<th class="text-center">SD. SAAT INI</th>
				<th class="text-center">2022</th>
				<th class="text-center">2022</th>
				<th class="text-center">2023</th>
				<th class="text-center">2023</th>
				<th class="text-center">2023</th>
				<th class="text-center">2023</th>
				<th class="text-center">2023</th>
				<th class="text-center">2023</th>
				<th class="text-center">2023</th>
	        </tr>
			<tr class="table-active2-rak">
				<th class="text-left" colspan="16">RENCANA PRODUKSI & PENDAPATAN USAHA</th>
			</tr>
			<tr class="table-active3-rak">
				<th class="text-center">1</th>
				<th class="text-left">Beton K 125 (10±2)</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_rap_2022_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_akumulasi_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_november_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_desember_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_januari_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_februari_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_maret_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_april_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_mei_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_juni_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_juli_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_all_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_vol_produk_a,2,',','.');?></th>
			</tr>
			<tr class="table-active3-rak">
				<th class="text-center">2</th>
				<th class="text-left">Beton K 225 (10±2)</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_rap_2022_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_akumulasi_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_november_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_desember_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_januari_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_februari_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_maret_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_april_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_mei_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_juni_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_juli_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_all_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_vol_produk_b,2,',','.');?></th>
			</tr>
			<tr class="table-active3-rak">
				<th class="text-center">3</th>
				<th class="text-left">Beton K 250 (10±2)</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_rap_2022_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_akumulasi_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_november_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_desember_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_januari_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_februari_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_maret_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_april_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_mei_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_juni_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_juli_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_all_produk_c,2,',','.');?></th>	
				<th class="text-right"><?php echo number_format($sisa_vol_produk_c,2,',','.');?></th>	
			</tr>
			<tr class="table-active3-rak">
				<th class="text-center">4</th>
				<th class="text-left">Beton K 250 (18±2)</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_rap_2022_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_akumulasi_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_november_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_desember_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_januari_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_februari_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_maret_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_april_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_mei_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_juni_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_juli_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_all_produk_d,2,',','.');?></th>	
				<th class="text-right"><?php echo number_format($sisa_vol_produk_d,2,',','.');?></th>	
			</tr>
			<tr class="table-active2-rak">
				<th class="text-right" colspan="2">TOTAL VOLUME</th>
				<th class="text-center">M3</th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_bahan_rap_2022?filter_date=".$filter_date = date('d F Y',strtotime('2022-01-01')).' - '.date('d F Y',strtotime('2022-12-31'))) ?>"><?php echo number_format($total_rap_volume_2022,2,',','.');?></a></th>
				<th class="text-right"><?php echo number_format($total_akumulasi_volume,2,',','.');?></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_bahan_rap?filter_date=".$filter_date = date('d F Y',strtotime($date_november_awal)).' - '.date('d F Y',strtotime($date_november_akhir))) ?>"><?php echo number_format($total_november_volume,2,',','.');?></a></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_bahan_rap?filter_date=".$filter_date = date('d F Y',strtotime($date_desember_awal)).' - '.date('d F Y',strtotime($date_desember_akhir))) ?>"><?php echo number_format($total_desember_volume,2,',','.');?></a></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_bahan_rap?filter_date=".$filter_date = date('d F Y',strtotime($date_januari_awal)).' - '.date('d F Y',strtotime($date_januari_akhir))) ?>"><?php echo number_format($total_januari_volume,2,',','.');?></a></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_bahan_rap?filter_date=".$filter_date = date('d F Y',strtotime($date_februari_awal)).' - '.date('d F Y',strtotime($date_februari_akhir))) ?>"><?php echo number_format($total_februari_volume,2,',','.');?></a></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_bahan_rap?filter_date=".$filter_date = date('d F Y',strtotime($date_maret_awal)).' - '.date('d F Y',strtotime($date_maret_akhir))) ?>"><?php echo number_format($total_maret_volume,2,',','.');?></a></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_bahan_rap?filter_date=".$filter_date = date('d F Y',strtotime($date_april_awal)).' - '.date('d F Y',strtotime($date_april_akhir))) ?>"><?php echo number_format($total_april_volume,2,',','.');?></a></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_bahan_rap?filter_date=".$filter_date = date('d F Y',strtotime($date_mei_awal)).' - '.date('d F Y',strtotime($date_mei_akhir))) ?>"><?php echo number_format($total_mei_volume,2,',','.');?></a></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_bahan_rap?filter_date=".$filter_date = date('d F Y',strtotime($date_juni_awal)).' - '.date('d F Y',strtotime($date_juni_akhir))) ?>"><?php echo number_format($total_juni_volume,2,',','.');?></a></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_bahan_rap?filter_date=".$filter_date = date('d F Y',strtotime($date_juli_awal)).' - '.date('d F Y',strtotime($date_juli_akhir))) ?>"><?php echo number_format($total_juli_volume,2,',','.');?></a></th>
				<th class="text-right"><?php echo number_format($total_all_volume,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_all_volume,2,',','.');?></th>
			</tr>
			<tr class="table-active2-rak">
				<th class="text-right" colspan="2">PENDAPATAN USAHA</th>
				<th class="text-center"></th>
				<th class="text-right"><?php echo number_format($total_rap_nilai_2022,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_akumulasi_nilai,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_november_nilai,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_desember_nilai,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_januari_nilai,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_februari_nilai,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_maret_nilai,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_april_nilai,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_mei_nilai,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_juni_nilai,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_juli_nilai,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_all_nilai,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_all_nilai,0,',','.');?></th>
			</tr>
			<tr class="table-active2-rak">
				<th class="text-left" colspan="16">BIAYA</th>
			</tr>
			<tr class="table-active3-rak">
				<th class="text-center">1</th>
				<th class="text-left">Bahan</th>
				<th class="text-center">LS</th>
				<th class="text-right"><?php echo number_format($total_rap_2022_biaya_bahan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_bahan_akumulasi,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_november_biaya_bahan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_desember_biaya_bahan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_januari_biaya_bahan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_februari_biaya_bahan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_maret_biaya_bahan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_april_biaya_bahan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_mei_biaya_bahan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_juni_biaya_bahan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_juli_biaya_bahan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_all_biaya_bahan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_all_biaya_bahan,0,',','.');?></th>
			</tr>
			<tr class="table-active3-rak">
				<th class="text-center">2</th>
				<th class="text-left">Alat</th>
				<th class="text-center">LS</th>
				<th class="text-right"><?php echo number_format($total_rap_2022_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_alat_akumulasi,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_november_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_desember_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_januari_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_februari_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_maret_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_april_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_mei_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_juni_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_juli_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_all_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_all_biaya_alat,0,',','.');?></th>
			</tr>
			<tr class="table-active3-rak">
				<th class="text-center">3</th>
				<th class="text-left">Overhead</th>
				<th class="text-center">LS</th>
				<th class="text-right"><?php echo number_format($total_rap_2022_biaya_overhead,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_overhead_akumulasi,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_november_biaya_overhead,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_desember_biaya_overhead,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_januari_biaya_overhead,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_februari_biaya_overhead,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_maret_biaya_overhead,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_april_biaya_overhead,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_mei_biaya_overhead,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_juni_biaya_overhead,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_juli_biaya_overhead,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_all_biaya_overhead,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_all_biaya_overhead,0,',','.');?></th>
			</tr>
			<tr class="table-active3-rak">
				<th class="text-center">4</th>
				<th class="text-left">Biaya Bank</th>
				<th class="text-center">LS</th>
				<th class="text-right"><?php echo number_format($total_rap_2022_biaya_bank,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_diskonto_akumulasi,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_november_biaya_bank,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_desember_biaya_bank,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_januari_biaya_bank,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_februari_biaya_bank,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_maret_biaya_bank,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_april_biaya_bank,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_mei_biaya_bank,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_juni_biaya_bank,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_juli_biaya_bank,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_all_biaya_bank,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_all_biaya_bank,0,',','.');?></th>
			</tr>
			<tr class="table-active3-rak">
				<th class="text-center">5</th>
				<th class="text-left">Persiapan</th>
				<th class="text-center">LS</th>
				<th class="text-right"><?php echo number_format($total_rap_2022_biaya_persiapan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_persiapan_akumulasi,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_november_biaya_persiapan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_desember_biaya_persiapan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_januari_biaya_persiapan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_februari_biaya_persiapan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_maret_biaya_persiapan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_april_biaya_persiapan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_mei_biaya_persiapan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_juni_biaya_persiapan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_juli_biaya_persiapan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_all_biaya_persiapan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_all_biaya_persiapan,0,',','.');?></th>
			</tr>
			<tr class="table-active2-rak">
				<th class="text-right" colspan="2">JUMLAH</th>
				<th class="text-center"></th>
				<th class="text-right"><?php echo number_format($total_biaya_rap_2022_biaya,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_biaya_akumulasi,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_biaya_november_biaya,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_biaya_desember_biaya,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_biaya_januari_biaya,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_biaya_februari_biaya,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_biaya_maret_biaya,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_biaya_april_biaya,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_biaya_mei_biaya,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_biaya_juni_biaya,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_biaya_juli_biaya,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_biaya_all_biaya,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_biaya_all_biaya,0,',','.');?></th>
			</tr>
			<tr class="table-active2-rak">
				<th class="text-right" colspan="2">LABA</th>
				<th class="text-center"></th>
				<th class="text-right"><?php echo number_format($total_laba_rap_2022,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_laba_sd_agustus,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_laba_november,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_laba_desember,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_laba_januari,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_laba_februari,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_laba_maret,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_laba_april,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_laba_mei,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_laba_juni,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_laba_juli,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_laba_all,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_laba_all,0,',','.');?></th>
			</tr>
	    </table>
		<?php
	}


}