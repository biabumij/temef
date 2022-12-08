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
			//$total_harga_pemakaian_semen = ($total_volume_pemakaian_semen!=0)?$total_nilai_pemakaian_semen / $total_volume_pemakaian_semen * 1:0;
			$total_harga_pemakaian_semen = $total_harga_stock_semen_akhir;

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
			
			$hpp_bahan_baku = $this->db->select('pp.date_hpp, pp.pasir')
			->from('hpp_bahan_baku pp')
			->where("(pp.date_hpp between '$date1' and '$date2')")
			->order_by('pp.date_hpp','desc')->limit(1)
			->get()->row_array();

			$total_volume_stock_pasir_akhir = $stock_opname_pasir['volume'];
			$price_stock_opname_pasir =  $hpp_bahan_baku['pasir'];

			$total_volume_pemakaian_pasir = $total_volume_pembelian_pasir_akhir - $stock_opname_pasir['volume'];

			$total_harga_stock_pasir_akhir = round($price_stock_opname_pasir,0);
			$total_nilai_stock_pasir_akhir = $total_volume_stock_pasir_akhir * $total_harga_stock_pasir_akhir;

			$total_nilai_pemakaian_pasir = ($nilai_opening_balance_pasir + $total_nilai_pembelian_pasir) - $total_nilai_stock_pasir_akhir;
			//$total_harga_pemakaian_pasir = ($total_volume_pemakaian_pasir!=0)?$total_nilai_pemakaian_pasir / $total_volume_pemakaian_pasir * 1:0;
			$total_harga_pemakaian_pasir = $total_harga_stock_pasir_akhir;

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

			$hpp_bahan_baku = $this->db->select('pp.date_hpp, pp.batu1020')
			->from('hpp_bahan_baku pp')
			->where("(pp.date_hpp between '$date1' and '$date2')")
			->order_by('pp.date_hpp','desc')->limit(1)
			->get()->row_array();

			$total_volume_stock_batu1020_akhir = $stock_opname_batu1020['volume'];
			$price_stock_opname_batu1020 =  $hpp_bahan_baku['batu1020'];

			$total_volume_pemakaian_batu1020 = $total_volume_pembelian_batu1020_akhir - $stock_opname_batu1020['volume'];

			$total_harga_stock_batu1020_akhir = round($price_stock_opname_batu1020,0);
			$total_nilai_stock_batu1020_akhir = $total_volume_stock_batu1020_akhir * $total_harga_stock_batu1020_akhir;

			$total_nilai_pemakaian_batu1020 = ($nilai_opening_balance_batu1020 + $total_nilai_pembelian_batu1020) - $total_nilai_stock_batu1020_akhir;
			//$total_harga_pemakaian_batu1020 = ($total_volume_pemakaian_batu1020!=0)?$total_nilai_pemakaian_batu1020 / $total_volume_pemakaian_batu1020 * 1:0;
			$total_harga_pemakaian_batu1020 = $total_harga_stock_batu1020_akhir;

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
			
			$hpp_bahan_baku = $this->db->select('pp.date_hpp, pp.batu2030')
			->from('hpp_bahan_baku pp')
			->where("(pp.date_hpp between '$date1' and '$date2')")
			->order_by('pp.date_hpp','desc')->limit(1)
			->get()->row_array();

			$total_volume_stock_batu2030_akhir = $stock_opname_batu2030['volume'];
			$price_stock_opname_batu2030 =  $hpp_bahan_baku['batu2030'];

			$total_volume_pemakaian_batu2030 = $total_volume_pembelian_batu2030_akhir - $stock_opname_batu2030['volume'];

			$total_harga_stock_batu2030_akhir = round($price_stock_opname_batu2030,0);
			$total_nilai_stock_batu2030_akhir = $total_volume_stock_batu2030_akhir * $total_harga_stock_batu2030_akhir;

			$total_nilai_pemakaian_batu2030 = ($nilai_opening_balance_batu2030 + $total_nilai_pembelian_batu2030) - $total_nilai_stock_batu2030_akhir;
			//$total_harga_pemakaian_batu2030 = ($total_volume_pemakaian_batu2030!=0)?$total_nilai_pemakaian_batu2030 / $total_volume_pemakaian_batu2030 * 1:0;
			$total_harga_pemakaian_batu2030 = $total_harga_stock_batu2030_akhir;

			//BAHAN BAKU
			$total_opening_balance_bahan_baku = $nilai_opening_balance_semen + $nilai_opening_balance_pasir + $nilai_opening_balance_batu1020 + $nilai_opening_balance_batu2030;

			//TOTAL
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

			$bahan = $total_nilai;
			$alat = $alat;
			$overhead = $overhead;
			$diskonto = $diskonto;

			$total_biaya_operasional = $bahan + $alat + $overhead + $diskonto;

			$laba_kotor = $total_penjualan_all - $total_biaya_operasional;

			$laba_usaha = $laba_kotor;

			$persentase_laba_sebelum_pajak = ($total_penjualan_all!=0)?($laba_usaha / $total_penjualan_all)  * 100:0;

			$bahan_2 = $total_nilai_2;
			$alat_2 = $alat_2;
			$overhead_2 = $overhead_2;
			$diskonto_2 = $diskonto_2;

			$total_biaya_operasional_2 = $bahan_2 + $alat_2 + $overhead_2 + $diskonto_2;

			$laba_kotor_2 = $total_penjualan_all_2 - $total_biaya_operasional_2;

			$laba_usaha_2 = $laba_kotor_2;

			$persentase_laba_sebelum_pajak_2 = ($total_penjualan_all_2!=0)?($laba_usaha_2 / $total_penjualan_all_2)  * 100:0;

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
									<span><a target="_blank" href="<?= base_url("laporan/cetak_bahan_akumulasi?filter_date=".$filter_date_2 = date('d F Y',strtotime($date3)).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($bahan_2,0,',','.');?></a></span>
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
				$styleColorSebelumPajak = $laba_usaha < 0 ? 'color:red' : 'color:black';
				$styleColorSebelumPajak2 = $laba_usaha_2 < 0 ? 'color:red' : 'color:black';
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
	            <th colspan="4" class="text-left">Laba Usaha</th>
	            <th class="text-right" style="<?php echo $styleColorSebelumPajak ?>">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><?php echo number_format($laba_usaha,0,',','.');?></span>
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
									<span><?php echo number_format($laba_usaha_2,0,',','.');?></span>
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
				
						if ($row['no_trx_1']==0) { $jumlah_no_transaksi = $row['no_trx_1'] .= $row['no_trx_2'];} else
						{$jumlah_no_transaksi = $row['no_trx_1'] .= $row['no_trx_2'];}

						if ($row['dex_1']==0) { $jumlah_deskripsi = $row['dex_1'] .= $row['dex_2'];} else
						{$jumlah_deskripsi = $row['dex_1'] .= $row['dex_2'];}

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

			$rencana_kerja_2022_1 = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '2021-12-30' and '2021-12-30'")
			->get()->row_array();
			
			$rencana_kerja_2022_2 = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '2021-12-31' and '2021-12-31'")
			->get()->row_array();
			

			$volume_rap_2022_produk_a = $rencana_kerja_2022_1['vol_produk_a'] + $rencana_kerja_2022_2['vol_produk_a'];
			$volume_rap_2022_produk_b = $rencana_kerja_2022_1['vol_produk_b'] + $rencana_kerja_2022_2['vol_produk_b'];
			$volume_rap_2022_produk_c = $rencana_kerja_2022_1['vol_produk_c'] + $rencana_kerja_2022_2['vol_produk_c'];
			$volume_rap_2022_produk_d = $rencana_kerja_2022_1['vol_produk_d'] + $rencana_kerja_2022_2['vol_produk_d'];

			$total_rap_volume_2022 = $rencana_kerja_2022_1['vol_produk_a'] + $rencana_kerja_2022_1['vol_produk_b'] + $rencana_kerja_2022_1['vol_produk_c'] + $rencana_kerja_2022_1['vol_produk_d'] + $rencana_kerja_2022_2['vol_produk_a'] + $rencana_kerja_2022_2['vol_produk_b'] + $rencana_kerja_2022_2['vol_produk_c'] + $rencana_kerja_2022_2['vol_produk_d'];
			
			$price_produk_a_1 = $rencana_kerja_2022_1['vol_produk_a'] * $rencana_kerja_2022_1['price_a'];
			$price_produk_b_1 = $rencana_kerja_2022_1['vol_produk_b'] * $rencana_kerja_2022_1['price_b'];
			$price_produk_c_1 = $rencana_kerja_2022_1['vol_produk_c'] * $rencana_kerja_2022_1['price_c'];
			$price_produk_d_1 = $rencana_kerja_2022_1['vol_produk_d'] * $rencana_kerja_2022_1['price_d'];

			$price_produk_a_2 = $rencana_kerja_2022_2['vol_produk_a'] * $rencana_kerja_2022_2['price_a'];
			$price_produk_b_2 = $rencana_kerja_2022_2['vol_produk_b'] * $rencana_kerja_2022_2['price_b'];
			$price_produk_c_2 = $rencana_kerja_2022_2['vol_produk_c'] * $rencana_kerja_2022_2['price_c'];
			$price_produk_d_2 = $rencana_kerja_2022_2['vol_produk_d'] * $rencana_kerja_2022_2['price_d'];

			$nilai_jual_all_2022 = $price_produk_a_1 + $price_produk_b_1 + $price_produk_c_1 + $price_produk_d_1 + $price_produk_a_2 + $price_produk_b_2 + $price_produk_c_2 + $price_produk_d_2;
			$total_rap_nilai_2022 = $nilai_jual_all_2022;

			//BIAYA RAP 2022
			$rencana_kerja_2022_biaya_1 = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '2021-12-30' and '2021-12-30'")
			->get()->row_array();

			$rencana_kerja_2022_biaya_2 = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '2021-12-31' and '2021-12-31'")
			->get()->row_array();
		
			$total_rap_2022_biaya_bahan = $rencana_kerja_2022_biaya_1['biaya_bahan'] + $rencana_kerja_2022_biaya_2['biaya_bahan'];
			$total_rap_2022_biaya_alat = $rencana_kerja_2022_biaya_1['biaya_alat'] + $rencana_kerja_2022_biaya_2['biaya_alat'];
			$total_rap_2022_biaya_overhead = $rencana_kerja_2022_biaya_1['biaya_overhead'] + $rencana_kerja_2022_biaya_2['biaya_overhead'];
			$total_rap_2022_biaya_bank = $rencana_kerja_2022_biaya_1['biaya_bank'] + $rencana_kerja_2022_biaya_2['biaya_bank'];

			$total_biaya_rap_2022_biaya = $total_rap_2022_biaya_bahan + $total_rap_2022_biaya_alat + $total_rap_2022_biaya_overhead + $total_rap_2022_biaya_bank;

			?>
			<!-- RAP 2022 -->

			<!-- AKUMULASI BULAN TERAKHIR -->
			<?php
			$stock_opname = $this->db->select('date')->order_by('date','desc')->limit(1,5)->get_where('pmm_remaining_materials_cat',array('status'=>'PUBLISH'))->row_array();
			$last_opname =  date('Y-m-d', strtotime($stock_opname['date']));
			$penjualan_now = $this->db->select('SUM(pp.display_price) as total')
			->from('pmm_productions pp')
			->join('penerima p', 'pp.client_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("pp.date_production < '$last_opname'")
			->where("pp.status = 'PUBLISH'")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.client_id")
			->get()->row_array();

			//AKUMULASI BAHAN	
			$bahan_now = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar as total_nilai_keluar')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi <= '$last_opname')")
			->get()->result_array();

			$total_bahan_now = 0;

			foreach ($bahan_now as $a){
				$total_bahan_now += $a['total_nilai_keluar'];
			}

			$pembayaran_bahan_now = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran_penagihan_pembelian pm')
			->where("(pm.tanggal_pembayaran <= '$last_opname')")
			->where("pm.status = 'DISETUJUI'")
			->get()->row_array();
			$pembayaran_bahan_now = $pembayaran_bahan_now['total'];

			//AKUMULASI BAHAN

			//AKUMULASI ALAT
			$nilai_alat_now = $this->db->select('SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("(prm.date_receipt <= '$last_opname')")
			->where("p.kategori_produk = '5'")
			->where("po.status in ('PUBLISH','CLOSED')")
			->get()->row_array();

			$akumulasi_bbm_now = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar_2 as total_nilai_keluar_2')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi <= '$last_opname')")
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
			->where("(tanggal_transaksi <= '$last_opname')")
			->get()->row_array();
			$total_insentif_tm_now = $insentif_tm_now['total'];
			$alat_now = $nilai_alat_now['nilai'] + $total_akumulasi_bbm_now + $total_insentif_tm_now;
			//AKUMULASI ALAT

			//TERMIN NOW
			$termin_now = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("(pm.tanggal_pembayaran <= '$last_opname')")
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
			->where("(pb.tanggal_transaksi <= '$last_opname')")
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
			->where("(pb.tanggal_transaksi <= '$last_opname')")
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
			->where("(pb.tanggal_transaksi <= '$last_opname')")
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
			->where("(pb.tanggal_transaksi <= '$last_opname')")
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
			->where("(pb.tanggal_transaksi <= '$last_opname')")
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
			->where("(pb.tanggal_transaksi <= '$last_opname')")
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
			->where("pb.tanggal_transaksi < '$last_opname'")
			->get()->row_array();
			$diskonto_now = $diskonto_now['total'];
			//END DISKONTO

			//PPN KELUAR
			$ppn_masuk_now = $this->db->select('SUM(ppd.tax) as total')
			->from('pmm_penagihan_pembelian_detail ppd')
			->join('pmm_penagihan_pembelian ppp','ppd.penagihan_pembelian_id = ppp.id','left')
			->where("ppp.tanggal_invoice < '$last_opname'")
			->get()->row_array();

			//PPN MASUK
			$ppn_keluar_now = $this->db->select('SUM(ppd.tax) as total')
			->from('pmm_penagihan_penjualan_detail ppd')
			->join('pmm_penagihan_penjualan ppp','ppd.penagihan_id = ppp.id','left')
			->where("ppp.tanggal_invoice < '$last_opname'")
			->get()->row_array();

			//PENERIMAAN PEMINJAMAN
			$penerimaan_penjualan_now = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi < '$last_opname'")
			->get()->row_array();

			//PENGEMBALIAN PEMINJAMAN
			$pengembalian_penjualan_now = $this->db->select('sum(pdb.kredit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi < '$last_opname'")
			->get()->row_array();
			?>
			<!-- AKUMULASI BULAN TERAKHIR -->

			<!-- NOVEMBER -->
			<?php
			$date_november_awal = date('2022-11-01');
			$date_november_akhir = date('2022-11-30');

			$rencana_kerja_november = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_november_awal' and '$date_november_akhir'")
			->get()->row_array();

			$rencana_kerja_november_biaya = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_november_awal' and '$date_november_akhir'")
			->get()->row_array();

			$rencana_kerja_november_biaya_cash_flow = $this->db->select('r.*')
			->from('rak_biaya_cash_flow r')
			->where("r.tanggal_rencana_kerja between '$date_november_awal' and '$date_november_akhir'")
			->get()->row_array();

			$volume_november_produk_a = $rencana_kerja_november['vol_produk_a'];
			$volume_november_produk_b = $rencana_kerja_november['vol_produk_b'];
			$volume_november_produk_c = $rencana_kerja_november['vol_produk_c'];
			$volume_november_produk_d = $rencana_kerja_november['vol_produk_d'];

			$total_november_volume = $volume_november_produk_a + $volume_november_produk_b + $volume_november_produk_c + $volume_november_produk_d;
		
			$nilai_jual_125_november = $volume_november_produk_a * $rencana_kerja_november['price_a'];
			$nilai_jual_225_november = $volume_november_produk_b * $rencana_kerja_november['price_b'];
			$nilai_jual_250_november = $volume_november_produk_c * $rencana_kerja_november['price_c'];
			$nilai_jual_250_18_november = $volume_november_produk_d * $rencana_kerja_november['price_d'];
			$nilai_jual_all_november = $nilai_jual_125_november + $nilai_jual_225_november + $nilai_jual_250_november + $nilai_jual_250_18_november;
			
			$total_november_nilai = $nilai_jual_all_november;
			
			$volume_rencana_kerja_november_produk_a = $rencana_kerja_november['vol_produk_a'];
			$volume_rencana_kerja_november_produk_b = $rencana_kerja_november['vol_produk_b'];
			$volume_rencana_kerja_november_produk_c = $rencana_kerja_november['vol_produk_c'];
			$volume_rencana_kerja_november_produk_d = $rencana_kerja_november['vol_produk_d'];

			$total_november_biaya_bahan_rap = $rencana_kerja_november_biaya['biaya_bahan'];
			$total_november_biaya_alat_rap = $rencana_kerja_november_biaya['biaya_alat'];
			$total_biaya_november_biaya_rap = $total_november_biaya_bahan_rap + $total_november_biaya_alat_rap;

			$total_november_biaya_bahan = $rencana_kerja_november_biaya_cash_flow['biaya_bahan'];
			$total_november_biaya_alat = $rencana_kerja_november_biaya_cash_flow['biaya_alat'];
			$total_november_biaya_overhead = $rencana_kerja_november_biaya_cash_flow['biaya_overhead'];
			$total_november_biaya_bank = $rencana_kerja_november_biaya_cash_flow['biaya_bank'];
			$total_november_biaya_termin = $rencana_kerja_november_biaya_cash_flow['biaya_bank'];
			
			//TERMIN NOVEMBER
			$termin_november = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_november_awal' and '$date_november_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();

			//PENERIMAAN PEMINJAMAN
			$penerimaan_penjualan_november = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_november_awal' and '$date_november_akhir'")
			->get()->row_array();

			//PENGEMBALIAN PEMINJAMAN
			$pengembalian_penjualan_november = $this->db->select('sum(pdb.kredit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_november_awal' and '$date_november_akhir'")
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

			$rencana_kerja_desember_biaya = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_desember_awal' and '$date_desember_akhir'")
			->get()->row_array();

			$rencana_kerja_desember_biaya_cash_flow = $this->db->select('r.*')
			->from('rak_biaya_cash_flow r')
			->where("r.tanggal_rencana_kerja between '$date_desember_awal' and '$date_desember_akhir'")
			->get()->row_array();

			$volume_desember_produk_a = $rencana_kerja_desember['vol_produk_a'];
			$volume_desember_produk_b = $rencana_kerja_desember['vol_produk_b'];
			$volume_desember_produk_c = $rencana_kerja_desember['vol_produk_c'];
			$volume_desember_produk_d = $rencana_kerja_desember['vol_produk_d'];

			$total_desember_volume = $volume_desember_produk_a + $volume_desember_produk_b + $volume_desember_produk_c + $volume_desember_produk_d;
		
			$nilai_jual_125_desember = $volume_desember_produk_a * $rencana_kerja_desember['price_a'];
			$nilai_jual_225_desember = $volume_desember_produk_b * $rencana_kerja_desember['price_b'];
			$nilai_jual_250_desember = $volume_desember_produk_c * $rencana_kerja_desember['price_c'];
			$nilai_jual_250_18_desember = $volume_desember_produk_d * $rencana_kerja_desember['price_d'];
			$nilai_jual_all_desember = $nilai_jual_125_desember + $nilai_jual_225_desember + $nilai_jual_250_desember + $nilai_jual_250_18_desember;
			
			$total_desember_nilai = $nilai_jual_all_desember;
			
			$volume_rencana_kerja_desember_produk_a = $rencana_kerja_desember['vol_produk_a'];
			$volume_rencana_kerja_desember_produk_b = $rencana_kerja_desember['vol_produk_b'];
			$volume_rencana_kerja_desember_produk_c = $rencana_kerja_desember['vol_produk_c'];
			$volume_rencana_kerja_desember_produk_d = $rencana_kerja_desember['vol_produk_d'];
		
			$total_desember_biaya_bahan_rap = $rencana_kerja_desember_biaya['biaya_bahan'];
			$total_desember_biaya_alat_rap = $rencana_kerja_desember_biaya['biaya_alat'];
			$total_biaya_desember_biaya_rap = $total_desember_biaya_bahan_rap + $total_desember_biaya_alat_rap;

			$total_desember_biaya_bahan = $rencana_kerja_desember_biaya_cash_flow['biaya_bahan'];
			$total_desember_biaya_alat = $rencana_kerja_desember_biaya_cash_flow['biaya_alat'];
			$total_desember_biaya_overhead = $rencana_kerja_desember_biaya_cash_flow['biaya_overhead'];
			$total_desember_biaya_bank = $rencana_kerja_desember_biaya_cash_flow['biaya_bank'];
			$total_desember_biaya_termin = $rencana_kerja_desember_biaya_cash_flow['biaya_bank'];
			
			//TERMIN DESEMBER
			$termin_desember = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_desember_awal' and '$date_desember_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();

			//PENERIMAAN PEMINJAMAN
			$penerimaan_penjualan_desember = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_desember_awal' and '$date_desember_akhir'")
			->get()->row_array();

			//PENGEMBALIAN PEMINJAMAN
			$pengembalian_penjualan_desember = $this->db->select('sum(pdb.kredit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_desember_awal' and '$date_desember_akhir'")
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

			$rencana_kerja_januari_biaya = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_januari_akhir'")
			->get()->row_array();

			$rencana_kerja_januari_biaya_cash_flow = $this->db->select('r.*')
			->from('rak_biaya_cash_flow r')
			->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_januari_akhir'")
			->get()->row_array();

			$volume_januari_produk_a = $rencana_kerja_januari['vol_produk_a'];
			$volume_januari_produk_b = $rencana_kerja_januari['vol_produk_b'];
			$volume_januari_produk_c = $rencana_kerja_januari['vol_produk_c'];
			$volume_januari_produk_d = $rencana_kerja_januari['vol_produk_d'];

			$total_januari_volume = $volume_januari_produk_a + $volume_januari_produk_b + $volume_januari_produk_c + $volume_januari_produk_d;
		
			$nilai_jual_125_januari = $volume_januari_produk_a * $rencana_kerja_januari['price_a'];
			$nilai_jual_225_januari = $volume_januari_produk_b * $rencana_kerja_januari['price_b'];
			$nilai_jual_250_januari = $volume_januari_produk_c * $rencana_kerja_januari['price_c'];
			$nilai_jual_250_18_januari = $volume_januari_produk_d * $rencana_kerja_januari['price_d'];
			$nilai_jual_all_januari = $nilai_jual_125_januari + $nilai_jual_225_januari + $nilai_jual_250_januari + $nilai_jual_250_18_januari;
			
			$total_januari_nilai = $nilai_jual_all_januari;
			
			$volume_rencana_kerja_januari_produk_a = $rencana_kerja_januari['vol_produk_a'];
			$volume_rencana_kerja_januari_produk_b = $rencana_kerja_januari['vol_produk_b'];
			$volume_rencana_kerja_januari_produk_c = $rencana_kerja_januari['vol_produk_c'];
			$volume_rencana_kerja_januari_produk_d = $rencana_kerja_januari['vol_produk_d'];
		
			$total_januari_biaya_bahan_rap = $rencana_kerja_januari_biaya['biaya_bahan'];
			$total_januari_biaya_alat_rap = $rencana_kerja_januari_biaya['biaya_alat'];
			$total_biaya_januari_biaya_rap = $total_januari_biaya_bahan_rap + $total_januari_biaya_alat_rap;

			$total_januari_biaya_bahan = $rencana_kerja_januari_biaya_cash_flow['biaya_bahan'];
			$total_januari_biaya_alat = $rencana_kerja_januari_biaya_cash_flow['biaya_alat'];
			$total_januari_biaya_overhead = $rencana_kerja_januari_biaya_cash_flow['biaya_overhead'];
			$total_januari_biaya_bank = $rencana_kerja_januari_biaya_cash_flow['biaya_bank'];
			$total_januari_biaya_termin = $rencana_kerja_januari_biaya_cash_flow['biaya_bank'];
			
			//TERMIN JANUARI
			$termin_januari = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_januari_awal' and '$date_januari_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();

			//PENERIMAAN PEMINJAMAN
			$penerimaan_penjualan_januari = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_januari_awal' and '$date_januari_akhir'")
			->get()->row_array();

			//PENGEMBALIAN PEMINJAMAN
			$pengembalian_penjualan_januari = $this->db->select('sum(pdb.kredit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_januari_awal' and '$date_januari_akhir'")
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

			$rencana_kerja_februari_biaya = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_februari_awal' and '$date_februari_akhir'")
			->get()->row_array();

			$rencana_kerja_februari_biaya_cash_flow = $this->db->select('r.*')
			->from('rak_biaya_cash_flow r')
			->where("r.tanggal_rencana_kerja between '$date_februari_awal' and '$date_februari_akhir'")
			->get()->row_array();

			$volume_februari_produk_a = $rencana_kerja_februari['vol_produk_a'];
			$volume_februari_produk_b = $rencana_kerja_februari['vol_produk_b'];
			$volume_februari_produk_c = $rencana_kerja_februari['vol_produk_c'];
			$volume_februari_produk_d = $rencana_kerja_februari['vol_produk_d'];

			$total_februari_volume = $volume_februari_produk_a + $volume_februari_produk_b + $volume_februari_produk_c + $volume_februari_produk_d;
		
			$nilai_jual_125_februari = $volume_februari_produk_a * $rencana_kerja_februari['price_a'];
			$nilai_jual_225_februari = $volume_februari_produk_b * $rencana_kerja_februari['price_b'];
			$nilai_jual_250_februari = $volume_februari_produk_c * $rencana_kerja_februari['price_c'];
			$nilai_jual_250_18_februari = $volume_februari_produk_d * $rencana_kerja_februari['price_d'];
			$nilai_jual_all_februari = $nilai_jual_125_februari + $nilai_jual_225_februari + $nilai_jual_250_februari + $nilai_jual_250_18_februari;
			
			$total_februari_nilai = $nilai_jual_all_februari;
			
			$volume_rencana_kerja_februari_produk_a = $rencana_kerja_februari['vol_produk_a'];
			$volume_rencana_kerja_februari_produk_b = $rencana_kerja_februari['vol_produk_b'];
			$volume_rencana_kerja_februari_produk_c = $rencana_kerja_februari['vol_produk_c'];
			$volume_rencana_kerja_februari_produk_d = $rencana_kerja_februari['vol_produk_d'];
		
			$total_februari_biaya_bahan_rap = $rencana_kerja_februari_biaya['biaya_bahan'];
			$total_februari_biaya_alat_rap = $rencana_kerja_februari_biaya['biaya_alat'];
			$total_biaya_februari_biaya_rap = $total_februari_biaya_bahan_rap + $total_februari_biaya_alat_rap;

			$total_februari_biaya_bahan = $rencana_kerja_februari_biaya_cash_flow['biaya_bahan'];
			$total_februari_biaya_alat = $rencana_kerja_februari_biaya_cash_flow['biaya_alat'];
			$total_februari_biaya_overhead = $rencana_kerja_februari_biaya_cash_flow['biaya_overhead'];
			$total_februari_biaya_bank = $rencana_kerja_februari_biaya_cash_flow['biaya_bank'];
			$total_februari_biaya_termin = $rencana_kerja_februari_biaya_cash_flow['biaya_bank'];
			
			//TERMIN FEBRUARI
			$termin_februari = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_februari_awal' and '$date_februari_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();

			//PENERIMAAN PEMINJAMAN
			$penerimaan_penjualan_februari = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_februari_awal' and '$date_februari_akhir'")
			->get()->row_array();

			//PENGEMBALIAN PEMINJAMAN
			$pengembalian_penjualan_februari = $this->db->select('sum(pdb.kredit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_februari_awal' and '$date_februari_akhir'")
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

			$rencana_kerja_maret_biaya = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_maret_awal' and '$date_maret_akhir'")
			->get()->row_array();

			$rencana_kerja_maret_biaya_cash_flow = $this->db->select('r.*')
			->from('rak_biaya_cash_flow r')
			->where("r.tanggal_rencana_kerja between '$date_maret_awal' and '$date_maret_akhir'")
			->get()->row_array();

			$volume_maret_produk_a = $rencana_kerja_maret['vol_produk_a'];
			$volume_maret_produk_b = $rencana_kerja_maret['vol_produk_b'];
			$volume_maret_produk_c = $rencana_kerja_maret['vol_produk_c'];
			$volume_maret_produk_d = $rencana_kerja_maret['vol_produk_d'];

			$total_maret_volume = $volume_maret_produk_a + $volume_maret_produk_b + $volume_maret_produk_c + $volume_maret_produk_d;
		
			$nilai_jual_125_maret = $volume_maret_produk_a * $rencana_kerja_maret['price_a'];
			$nilai_jual_225_maret = $volume_maret_produk_b * $rencana_kerja_maret['price_b'];
			$nilai_jual_250_maret = $volume_maret_produk_c * $rencana_kerja_maret['price_c'];
			$nilai_jual_250_18_maret = $volume_maret_produk_d * $rencana_kerja_maret['price_d'];
			$nilai_jual_all_maret = $nilai_jual_125_maret + $nilai_jual_225_maret + $nilai_jual_250_maret + $nilai_jual_250_18_maret;
			
			$total_maret_nilai = $nilai_jual_all_maret;
			
			$volume_rencana_kerja_maret_produk_a = $rencana_kerja_maret['vol_produk_a'];
			$volume_rencana_kerja_maret_produk_b = $rencana_kerja_maret['vol_produk_b'];
			$volume_rencana_kerja_maret_produk_c = $rencana_kerja_maret['vol_produk_c'];
			$volume_rencana_kerja_maret_produk_d = $rencana_kerja_maret['vol_produk_d'];
		
			$total_maret_biaya_bahan_rap = $rencana_kerja_maret_biaya['biaya_bahan'];
			$total_maret_biaya_alat_rap = $rencana_kerja_maret_biaya['biaya_alat'];
			$total_biaya_maret_biaya_rap = $total_maret_biaya_bahan_rap + $total_maret_biaya_alat_rap;

			$total_maret_biaya_bahan = $rencana_kerja_maret_biaya_cash_flow['biaya_bahan'];
			$total_maret_biaya_alat = $rencana_kerja_maret_biaya_cash_flow['biaya_alat'];
			$total_maret_biaya_overhead = $rencana_kerja_maret_biaya_cash_flow['biaya_overhead'];
			$total_maret_biaya_bank = $rencana_kerja_maret_biaya_cash_flow['biaya_bank'];
			$total_maret_biaya_termin = $rencana_kerja_maret_biaya_cash_flow['biaya_bank'];
			
			//TERMIN MARET
			$termin_maret = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_maret_awal' and '$date_maret_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();

			//PENERIMAAN PEMINJAMAN
			$penerimaan_penjualan_maret = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_maret_awal' and '$date_maret_akhir'")
			->get()->row_array();

			//PENGEMBALIAN PEMINJAMAN
			$pengembalian_penjualan_maret = $this->db->select('sum(pdb.kredit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_maret_awal' and '$date_maret_akhir'")
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

			$rencana_kerja_april_biaya = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_april_awal' and '$date_april_akhir'")
			->get()->row_array();

			$rencana_kerja_april_biaya_cash_flow = $this->db->select('r.*')
			->from('rak_biaya_cash_flow r')
			->where("r.tanggal_rencana_kerja between '$date_april_awal' and '$date_april_akhir'")
			->get()->row_array();

			$volume_april_produk_a = $rencana_kerja_april['vol_produk_a'];
			$volume_april_produk_b = $rencana_kerja_april['vol_produk_b'];
			$volume_april_produk_c = $rencana_kerja_april['vol_produk_c'];
			$volume_april_produk_d = $rencana_kerja_april['vol_produk_d'];

			$total_april_volume = $volume_april_produk_a + $volume_april_produk_b + $volume_april_produk_c + $volume_april_produk_d;
		
			$nilai_jual_125_april = $volume_april_produk_a * $rencana_kerja_april['price_a'];
			$nilai_jual_225_april = $volume_april_produk_b * $rencana_kerja_april['price_b'];
			$nilai_jual_250_april = $volume_april_produk_c * $rencana_kerja_april['price_c'];
			$nilai_jual_250_18_april = $volume_april_produk_d * $rencana_kerja_april['price_d'];
			$nilai_jual_all_april = $nilai_jual_125_april + $nilai_jual_225_april + $nilai_jual_250_april + $nilai_jual_250_18_april;
			
			$total_april_nilai = $nilai_jual_all_april;
			
			$volume_rencana_kerja_april_produk_a = $rencana_kerja_april['vol_produk_a'];
			$volume_rencana_kerja_april_produk_b = $rencana_kerja_april['vol_produk_b'];
			$volume_rencana_kerja_april_produk_c = $rencana_kerja_april['vol_produk_c'];
			$volume_rencana_kerja_april_produk_d = $rencana_kerja_april['vol_produk_d'];
		
			$total_april_biaya_bahan_rap = $rencana_kerja_april_biaya['biaya_bahan'];
			$total_april_biaya_alat_rap = $rencana_kerja_april_biaya['biaya_alat'];
			$total_biaya_april_biaya_rap = $total_april_biaya_bahan_rap + $total_april_biaya_alat_rap;

			$total_april_biaya_bahan = $rencana_kerja_april_biaya_cash_flow['biaya_bahan'];
			$total_april_biaya_alat = $rencana_kerja_april_biaya_cash_flow['biaya_alat'];
			$total_april_biaya_overhead = $rencana_kerja_april_biaya_cash_flow['biaya_overhead'];
			$total_april_biaya_bank = $rencana_kerja_april_biaya_cash_flow['biaya_bank'];
			$total_april_biaya_termin = $rencana_kerja_april_biaya_cash_flow['biaya_bank'];
			
			//TERMIN APRIL
			$termin_april = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_april_awal' and '$date_april_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();

			//PENERIMAAN PEMINJAMAN
			$penerimaan_penjualan_april = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_april_awal' and '$date_april_akhir'")
			->get()->row_array();

			//PENGEMBALIAN PEMINJAMAN
			$pengembalian_penjualan_april = $this->db->select('sum(pdb.kredit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_april_awal' and '$date_april_akhir'")
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

			$rencana_kerja_mei_biaya = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_mei_awal' and '$date_mei_akhir'")
			->get()->row_array();

			$rencana_kerja_mei_biaya_cash_flow = $this->db->select('r.*')
			->from('rak_biaya_cash_flow r')
			->where("r.tanggal_rencana_kerja between '$date_april_awal' and '$date_mei_akhir'")
			->get()->row_array();

			$volume_mei_produk_a = $rencana_kerja_mei['vol_produk_a'];
			$volume_mei_produk_b = $rencana_kerja_mei['vol_produk_b'];
			$volume_mei_produk_c = $rencana_kerja_mei['vol_produk_c'];
			$volume_mei_produk_d = $rencana_kerja_mei['vol_produk_d'];

			$total_mei_volume = $volume_mei_produk_a + $volume_mei_produk_b + $volume_mei_produk_c + $volume_mei_produk_d;
		
			$nilai_jual_125_mei = $volume_mei_produk_a * $rencana_kerja_mei['price_a'];
			$nilai_jual_225_mei = $volume_mei_produk_b * $rencana_kerja_mei['price_b'];
			$nilai_jual_250_mei = $volume_mei_produk_c * $rencana_kerja_mei['price_c'];
			$nilai_jual_250_18_mei = $volume_mei_produk_d * $rencana_kerja_mei['price_d'];
			$nilai_jual_all_mei = $nilai_jual_125_mei + $nilai_jual_225_mei + $nilai_jual_250_mei + $nilai_jual_250_18_mei;
			
			$total_mei_nilai = $nilai_jual_all_mei;
			
			$volume_rencana_kerja_mei_produk_a = $rencana_kerja_mei['vol_produk_a'];
			$volume_rencana_kerja_mei_produk_b = $rencana_kerja_mei['vol_produk_b'];
			$volume_rencana_kerja_mei_produk_c = $rencana_kerja_mei['vol_produk_c'];
			$volume_rencana_kerja_mei_produk_d = $rencana_kerja_mei['vol_produk_d'];
		
			$total_mei_biaya_bahan_rap = $rencana_kerja_mei_biaya['biaya_bahan'];
			$total_mei_biaya_alat_rap = $rencana_kerja_mei_biaya['biaya_alat'];
			$total_biaya_mei_biaya_rap = $total_mei_biaya_bahan_rap + $total_mei_biaya_alat_rap;

			$total_mei_biaya_bahan = $rencana_kerja_mei_biaya_cash_flow['biaya_bahan'];
			$total_mei_biaya_alat = $rencana_kerja_mei_biaya_cash_flow['biaya_alat'];
			$total_mei_biaya_overhead = $rencana_kerja_mei_biaya_cash_flow['biaya_overhead'];
			$total_mei_biaya_bank = $rencana_kerja_mei_biaya_cash_flow['biaya_bank'];
			$total_mei_biaya_termin = $rencana_kerja_mei_biaya_cash_flow['biaya_bank'];
			
			//TERMIN MEI
			$termin_mei = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_mei_awal' and '$date_mei_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();

			//PENERIMAAN PEMINJAMAN
			$penerimaan_penjualan_mei = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_mei_awal' and '$date_mei_akhir'")
			->get()->row_array();

			//PENGEMBALIAN PEMINJAMAN
			$pengembalian_penjualan_mei = $this->db->select('sum(pdb.kredit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_mei_awal' and '$date_mei_akhir'")
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

			$rencana_kerja_juni_biaya = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_juni_awal' and '$date_juni_akhir'")
			->get()->row_array();

			$rencana_kerja_juni_biaya_cash_flow = $this->db->select('r.*')
			->from('rak_biaya_cash_flow r')
			->where("r.tanggal_rencana_kerja between '$date_juni_awal' and '$date_juni_akhir'")
			->get()->row_array();

			$volume_juni_produk_a = $rencana_kerja_juni['vol_produk_a'];
			$volume_juni_produk_b = $rencana_kerja_juni['vol_produk_b'];
			$volume_juni_produk_c = $rencana_kerja_juni['vol_produk_c'];
			$volume_juni_produk_d = $rencana_kerja_juni['vol_produk_d'];

			$total_juni_volume = $volume_juni_produk_a + $volume_juni_produk_b + $volume_juni_produk_c + $volume_juni_produk_d;
		
			$nilai_jual_125_juni = $volume_juni_produk_a * $rencana_kerja_juni['price_a'];
			$nilai_jual_225_juni = $volume_juni_produk_b * $rencana_kerja_juni['price_b'];
			$nilai_jual_250_juni = $volume_juni_produk_c * $rencana_kerja_juni['price_c'];
			$nilai_jual_250_18_juni = $volume_juni_produk_d * $rencana_kerja_juni['price_d'];
			$nilai_jual_all_juni = $nilai_jual_125_juni + $nilai_jual_225_juni + $nilai_jual_250_juni + $nilai_jual_250_18_juni;
			
			$total_juni_nilai = $nilai_jual_all_juni;
			
			$volume_rencana_kerja_juni_produk_a = $rencana_kerja_juni['vol_produk_a'];
			$volume_rencana_kerja_juni_produk_b = $rencana_kerja_juni['vol_produk_b'];
			$volume_rencana_kerja_juni_produk_c = $rencana_kerja_juni['vol_produk_c'];
			$volume_rencana_kerja_juni_produk_d = $rencana_kerja_juni['vol_produk_d'];
		
			$total_juni_biaya_bahan_rap = $rencana_kerja_juni_biaya['biaya_bahan'];
			$total_juni_biaya_alat_rap = $rencana_kerja_juni_biaya['biaya_alat'];
			$total_biaya_juni_biaya_rap = $total_juni_biaya_bahan_rap + $total_juni_biaya_alat_rap;

			$total_juni_biaya_bahan = $rencana_kerja_juni_biaya_cash_flow['biaya_bahan'];
			$total_juni_biaya_alat = $rencana_kerja_juni_biaya_cash_flow['biaya_alat'];
			$total_juni_biaya_overhead = $rencana_kerja_juni_biaya_cash_flow['biaya_overhead'];
			$total_juni_biaya_bank = $rencana_kerja_juni_biaya_cash_flow['biaya_bank'];
			$total_juni_biaya_termin = $rencana_kerja_juni_biaya_cash_flow['biaya_bank'];
			
			//TERMIN JUNI
			$termin_juni = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_juni_awal' and '$date_juni_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();

			//PENERIMAAN PEMINJAMAN
			$penerimaan_penjualan_juni = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_juni_awal' and '$date_juni_akhir'")
			->get()->row_array();

			//PENGEMBALIAN PEMINJAMAN
			$pengembalian_penjualan_juni = $this->db->select('sum(pdb.kredit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_juni_awal' and '$date_juni_akhir'")
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

			$rencana_kerja_juli_biaya = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_juli_awal' and '$date_juli_akhir'")
			->get()->row_array();

			$rencana_kerja_juli_biaya_cash_flow = $this->db->select('r.*')
			->from('rak_biaya_cash_flow r')
			->where("r.tanggal_rencana_kerja between '$date_juli_awal' and '$date_juli_akhir'")
			->get()->row_array();

			$volume_juli_produk_a = $rencana_kerja_juli['vol_produk_a'];
			$volume_juli_produk_b = $rencana_kerja_juli['vol_produk_b'];
			$volume_juli_produk_c = $rencana_kerja_juli['vol_produk_c'];
			$volume_juli_produk_d = $rencana_kerja_juli['vol_produk_d'];

			$total_juli_volume = $volume_juli_produk_a + $volume_juli_produk_b + $volume_juli_produk_c + $volume_juli_produk_d;
		
			$nilai_jual_125_juli = $volume_juli_produk_a * $rencana_kerja_juli['price_a'];
			$nilai_jual_225_juli = $volume_juli_produk_b * $rencana_kerja_juli['price_b'];
			$nilai_jual_250_juli = $volume_juli_produk_c * $rencana_kerja_juli['price_c'];
			$nilai_jual_250_18_juli = $volume_juli_produk_d * $rencana_kerja_juli['price_d'];
			$nilai_jual_all_juli = $nilai_jual_125_juli + $nilai_jual_225_juli + $nilai_jual_250_juli + $nilai_jual_250_18_juli;
			
			$total_juli_nilai = $nilai_jual_all_juli;
			
			$volume_rencana_kerja_juli_produk_a = $rencana_kerja_juli['vol_produk_a'];
			$volume_rencana_kerja_juli_produk_b = $rencana_kerja_juli['vol_produk_b'];
			$volume_rencana_kerja_juli_produk_c = $rencana_kerja_juli['vol_produk_c'];
			$volume_rencana_kerja_juli_produk_d = $rencana_kerja_juli['vol_produk_d'];
		
			$total_juli_biaya_bahan_rap = $rencana_kerja_juli_biaya['biaya_bahan'];
			$total_juli_biaya_alat_rap = $rencana_kerja_juli_biaya['biaya_alat'];
			$total_biaya_juli_biaya_rap = $total_juli_biaya_bahan_rap + $total_juli_biaya_alat_rap;

			$total_juli_biaya_bahan = $rencana_kerja_juli_biaya_cash_flow['biaya_bahan'];
			$total_juli_biaya_alat = $rencana_kerja_juli_biaya_cash_flow['biaya_alat'];
			$total_juli_biaya_overhead = $rencana_kerja_juli_biaya_cash_flow['biaya_overhead'];
			$total_juli_biaya_bank = $rencana_kerja_juli_biaya_cash_flow['biaya_bank'];
			$total_juli_biaya_termin = $rencana_kerja_juli_biaya_cash_flow['biaya_bank'];
			
			//TERMIN JULI
			$termin_juli = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_juli_awal' and '$date_juli_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();

			//PENERIMAAN PEMINJAMAN
			$penerimaan_penjualan_juli = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_juli_awal' and '$date_juli_akhir'")
			->get()->row_array();

			//PENGEMBALIAN PEMINJAMAN
			$pengembalian_penjualan_juli = $this->db->select('sum(pdb.kredit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 502")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_juli_awal' and '$date_juli_akhir'")
			->get()->row_array();
			?>
			<!-- JULI -->

			<tr class="table-active4-csf">
				<th class="text-center" rowspan="2" style="vertical-align:middle">URAIAN</th>
				<th class="text-center">CURRENT</th>
				<th class="text-center">REALISASI SD.</th>
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
				<?php
				$tanggal = $last_opname;
				$date = date('Y-m-d',strtotime($tanggal));
				?>
				<?php
				function tgl_indo($date){
					$bulan = array (
						1 =>   'Jan',
						'Feb',
						'Mar',
						'Apr',
						'Mei',
						'Jun',
						'Jul',
						'Agu',
						'Sep',
						'Okt',
						'Nov',
						'Des'
					);
					$pecahkan = explode('-', $date);
					
					// variabel pecahkan 0 = tanggal
					// variabel pecahkan 1 = bulan
					// variabel pecahkan 2 = tahun
				
					return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
					
				}
				?>
				<th class="text-center">CASH BUDGET</th>
				<th class="text-center" style="text-transform:uppercase;"><?= tgl_indo(date($date)); ?></th>
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
			$akumulasi_1 = $total_rap_nilai_2022;
			?>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;2. AKUMULASI (Rp.)</th>
				<th class="text-right"><?php echo number_format($akumulasi_1,0,',','.');?></th>
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
				<th class="text-left" colspan="14"><u>PENERIMAAN (EXCL. PPN)</u> <button id="btnpenerimaan3">Buka</button></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left" id="boxpenerimaan1" style="display:none;">&nbsp;&nbsp;Uang Muka</th>
				<th class="text-right" id="boxpenerimaan2" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan3" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan4" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan5" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan6" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan7" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan8" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan9" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan10" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan11" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan12" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan13" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan14" style="display:none;">-</th>
			</tr>
			<?php
			$termin_november = $rencana_kerja_november_biaya['termin'];
			$termin_desember = $rencana_kerja_desember_biaya['termin'];
			$termin_januari = $rencana_kerja_januari_biaya['termin'];
			$termin_februari = $rencana_kerja_februari_biaya['termin'];
			$termin_maret = $rencana_kerja_maret_biaya['termin'];
			$termin_april = $rencana_kerja_april_biaya['termin'];
			$termin_mei = $rencana_kerja_mei_biaya['termin'];
			$termin_juni = $rencana_kerja_juni_biaya['termin'];
			$termin_juli = $rencana_kerja_juli_biaya['termin'];
			$jumlah_termin = $termin_now['total'] + $termin_november + $termin_desember + $termin_januari + $termin_februari + $termin_maret + $termin_april + $termin_mei + $termin_juni + $termin_juli;
			?>
			<tr class="table-active3-csf">
				<th class="text-left" id="boxpenerimaan15" style="display:none;">&nbsp;&nbsp;Termin / Angsuran</th>
				<th class="text-right" id="boxpenerimaan16" style="display:none;"><?php echo number_format($total_rap_nilai_2022,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan17" style="display:none;"><?php echo number_format($termin_now['total'],0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan18" style="display:none;"><?php echo number_format($termin_november,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan19" style="display:none;"><?php echo number_format($termin_desember,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan20" style="display:none;"><?php echo number_format($termin_januari,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan21" style="display:none;"><?php echo number_format($termin_februari,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan22" style="display:none;"><?php echo number_format($termin_maret,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan23" style="display:none;"><?php echo number_format($termin_april,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan24" style="display:none;"><?php echo number_format($termin_mei,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan25" style="display:none;"><?php echo number_format($termin_juni,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan26" style="display:none;"><?php echo number_format($termin_juli,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan27" style="display:none;"><?php echo number_format($jumlah_termin,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan28" style="display:none;"><?php echo number_format($total_rap_nilai_2022 - $jumlah_termin,0,',','.');?></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left" id="boxpenerimaan29" style="display:none;">&nbsp;&nbsp;Pengembalian Retensi</th>
				<th class="text-right" id="boxpenerimaan30" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan31" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan32" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan33" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan34" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan35" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan36" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan37" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan38" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan39" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan40" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan41" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan42" style="display:none;">-</th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left" id="boxpenerimaan43" style="display:none;">&nbsp;&nbsp;PPN Keluaran</th>
				<th class="text-right" id="boxpenerimaan44" style="display:none;"><?php echo number_format(($total_rap_nilai_2022 * 11) / 100,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan45" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan46" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan47" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan48" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan49" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan50" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan51" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan52" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan53" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan54" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan55" style="display:none;">-</th>
				<th class="text-right" id="boxpenerimaan56" style="display:none;">-</th>
			</tr>
			<tr class="table-active2-csf">
				<th class="text-left" id="boxpenerimaan57" style="display:none;"><i>JUMLAH PENERIMAAN</i></th>
				<th class="text-right" id="boxpenerimaan58" style="display:none;"><?php echo number_format((($total_rap_nilai_2022 * 11) / 100) + $total_rap_nilai_2022,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan59" style="display:none;"><?php echo number_format($termin_now['total'],0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan60" style="display:none;"><?php echo number_format($termin_november,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan61" style="display:none;"><?php echo number_format($termin_desember,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan62" style="display:none;"><?php echo number_format($termin_januari,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan63" style="display:none;"><?php echo number_format($termin_februari,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan64" style="display:none;"><?php echo number_format($termin_maret,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan65" style="display:none;"><?php echo number_format($termin_april,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan66" style="display:none;"><?php echo number_format($termin_mei,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan67" style="display:none;"><?php echo number_format($termin_juni,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan68" style="display:none;"><?php echo number_format($termin_juli,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan69" style="display:none;"><?php echo number_format($jumlah_termin,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan70" style="display:none;"><?php echo number_format($total_rap_nilai_2022 - $jumlah_termin,0,',','.');?></th>
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
			
			$akumulasi_2 = (($total_rap_nilai_2022 * 11) / 100) + $total_rap_nilai_2022;
			?>
			
			<tr class="table-active2-csf">
				<th class="text-left" id="boxpenerimaan71" style="display:none;"><i>AKUMULASI PENERIMAAN</i></th>
				<th class="text-right" id="boxpenerimaan72" style="display:none;"><?php echo number_format($akumulasi_2,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan73" style="display:none;"><?php echo number_format($termin_now['total'],0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan74" style="display:none;"><?php echo number_format($akumulasi_termin_november,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan75" style="display:none;"><?php echo number_format($akumulasi_termin_desember,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan76" style="display:none;"><?php echo number_format($akumulasi_termin_januari,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan77" style="display:none;"><?php echo number_format($akumulasi_termin_februari,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan78" style="display:none;"><?php echo number_format($akumulasi_termin_maret,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan79" style="display:none;"><?php echo number_format($akumulasi_termin_april,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan80" style="display:none;"><?php echo number_format($akumulasi_termin_mei,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan81" style="display:none;"><?php echo number_format($akumulasi_termin_juni,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan82" style="display:none;"><?php echo number_format($akumulasi_termin_juli,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan83" style="display:none;"><?php echo number_format($akumulasi_termin_juli,0,',','.');?></th>
				<th class="text-right" id="boxpenerimaan84" style="display:none;"><?php echo number_format($total_rap_nilai_2022 - $akumulasi_termin_juli,0,',','.');?></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left" colspan="14"><u>PEMAKAIAN BAHAN & ALAT</u> <button id="btnpemakaian3">Buka</button></th>
			</tr>
			<?php
			$jumlah_bahan_rap = $total_bahan_now + $total_november_biaya_bahan_rap + $total_desember_biaya_bahan_rap + $total_januari_biaya_bahan_rap + $total_februari_biaya_bahan_rap + $total_maret_biaya_bahan_rap + $total_april_biaya_bahan_rap + $total_mei_biaya_bahan_rap + $total_juni_biaya_bahan_rap + $total_juli_biaya_bahan_rap;
			$sisa_bahan_rap = $total_rap_2022_biaya_bahan - $jumlah_bahan_rap;
			?>
			<tr class="table-active3-csf">
				<th class="text-left" id="boxpemakaian1" style="display:none;">&nbsp;&nbsp;1. Bahan</th>
				<th class="text-right" id="boxpemakaian2" style="display:none;"><?php echo number_format($total_rap_2022_biaya_bahan,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian3" style="display:none;"><?php echo number_format($total_bahan_now,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian4" style="display:none;"><?php echo number_format($total_november_biaya_bahan_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian5" style="display:none;"><?php echo number_format($total_desember_biaya_bahan_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian6" style="display:none;"><?php echo number_format($total_januari_biaya_bahan_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian7" style="display:none;"><?php echo number_format($total_februari_biaya_bahan_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian8" style="display:none;"><?php echo number_format($total_maret_biaya_bahan_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian9" style="display:none;"><?php echo number_format($total_april_biaya_bahan_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian10" style="display:none;"><?php echo number_format($total_mei_biaya_bahan_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian11" style="display:none;"><?php echo number_format($total_juni_biaya_bahan_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian12" style="display:none;"><?php echo number_format($total_juli_biaya_bahan_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian13" style="display:none;"><?php echo number_format($jumlah_bahan_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian14" style="display:none;"><?php echo number_format($sisa_bahan_rap,0,',','.');?></th>
			</tr>
			<?php
			$jumlah_alat_rap = $alat_now + $total_november_biaya_alat_rap + $total_desember_biaya_alat_rap + $total_januari_biaya_alat_rap + $total_februari_biaya_alat_rap + $total_maret_biaya_alat_rap + $total_april_biaya_alat_rap + $total_mei_biaya_alat_rap + $total_juni_biaya_alat_rap + $total_juli_biaya_alat_rap;
			$sisa_alat_rap = $total_rap_2022_biaya_alat - $jumlah_alat_rap;
			?>
			<tr class="table-active3-csf">
				<th class="text-left" id="boxpemakaian15" style="display:none;">&nbsp;&nbsp;2. Alat</th>
				<th class="text-right" id="boxpemakaian16" style="display:none;"><?php echo number_format($total_rap_2022_biaya_alat,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian17" style="display:none;"><?php echo number_format($alat_now,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian18" style="display:none;"><?php echo number_format($total_november_biaya_alat_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian19" style="display:none;"><?php echo number_format($total_desember_biaya_alat_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian20" style="display:none;"><?php echo number_format($total_januari_biaya_alat_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian21" style="display:none;"><?php echo number_format($total_februari_biaya_alat_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian22" style="display:none;"><?php echo number_format($total_maret_biaya_alat_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian23" style="display:none;"><?php echo number_format($total_april_biaya_alat_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian24" style="display:none;"><?php echo number_format($total_mei_biaya_alat_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian25" style="display:none;"><?php echo number_format($total_juni_biaya_alat_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian26" style="display:none;"><?php echo number_format($total_juli_biaya_alat_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian27" style="display:none;"><?php echo number_format($jumlah_alat_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian28" style="display:none;"><?php echo number_format($sisa_alat_rap,0,',','.');?></th>
			</tr>
			<?php
			$jumlah_pemakaian_rap = $jumlah_bahan_rap + $jumlah_alat_rap;
			$sisa_pemakaian_rap = ($total_rap_2022_biaya_bahan + $total_rap_2022_biaya_alat) - $jumlah_pemakaian_rap;
			?>
			<tr class="table-active2-csf">
				<th class="text-left" id="boxpemakaian29" style="display:none;"><i>JUMLAH PEMAKAIAN</i></th>
				<th class="text-right" id="boxpemakaian30" style="display:none;"><?php echo number_format($total_rap_2022_biaya_bahan + $total_rap_2022_biaya_alat,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian31" style="display:none;"><?php echo number_format($total_bahan_now + $alat_now,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian32" style="display:none;"><?php echo number_format($total_november_biaya_bahan_rap + $total_november_biaya_alat_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian33" style="display:none;"><?php echo number_format($total_desember_biaya_bahan_rap + $total_desember_biaya_alat_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian34" style="display:none;"><?php echo number_format($total_januari_biaya_bahan_rap + $total_januari_biaya_alat_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian35" style="display:none;"><?php echo number_format($total_februari_biaya_bahan_rap + $total_februari_biaya_alat_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian36" style="display:none;"><?php echo number_format($total_maret_biaya_bahan_rap + $total_maret_biaya_alat_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian37" style="display:none;"><?php echo number_format($total_april_biaya_bahan_rap + $total_april_biaya_alat_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian38" style="display:none;"><?php echo number_format($total_mei_biaya_bahan_rap + $total_mei_biaya_alat_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian39" style="display:none;"><?php echo number_format($total_juni_biaya_bahan_rap + $total_juni_biaya_alat_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian40" style="display:none;"><?php echo number_format($total_juli_biaya_bahan_rap + $total_juli_biaya_alat_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian41" style="display:none;"><?php echo number_format($jumlah_pemakaian_rap,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian42" style="display:none;"><?php echo number_format($sisa_pemakaian_rap,0,',','.');?></th>
			</tr>
			<?php
			$akumulasi_pemakaian_rap_bahan_alat = $total_rap_2022_biaya_bahan + $total_rap_2022_biaya_alat;
			$akumulasi_pemakaian_november = $total_bahan_now + $alat_now + $total_november_biaya_bahan_rap + $total_november_biaya_alat_rap;
			$akumulasi_pemakaian_desember = $total_bahan_now + $alat_now + $total_november_biaya_bahan_rap + $total_november_biaya_alat_rap + $total_desember_biaya_bahan_rap + $total_desember_biaya_alat_rap;
			$akumulasi_pemakaian_januari = $total_bahan_now + $alat_now + $total_november_biaya_bahan_rap + $total_november_biaya_alat_rap + $total_desember_biaya_bahan_rap + $total_desember_biaya_alat_rap + $total_januari_biaya_bahan_rap + $total_januari_biaya_alat_rap;
			$akumulasi_pemakaian_februari = $total_bahan_now + $alat_now + $total_november_biaya_bahan_rap + $total_november_biaya_alat_rap + $total_desember_biaya_bahan_rap + $total_desember_biaya_alat_rap + $total_januari_biaya_bahan_rap + $total_januari_biaya_alat_rap + $total_februari_biaya_bahan_rap + $total_februari_biaya_alat_rap;
			$akumulasi_pemakaian_maret = $total_bahan_now + $alat_now + $total_november_biaya_bahan_rap + $total_november_biaya_alat_rap + $total_desember_biaya_bahan_rap + $total_desember_biaya_alat_rap + $total_januari_biaya_bahan_rap + $total_januari_biaya_alat_rap + $total_februari_biaya_bahan_rap + $total_februari_biaya_alat_rap + $total_maret_biaya_bahan_rap + $total_maret_biaya_alat_rap;
			$akumulasi_pemakaian_april = $total_bahan_now + $alat_now + $total_november_biaya_bahan_rap + $total_november_biaya_alat_rap + $total_desember_biaya_bahan_rap + $total_desember_biaya_alat_rap + $total_januari_biaya_bahan_rap + $total_januari_biaya_alat_rap + $total_februari_biaya_bahan_rap + $total_februari_biaya_alat_rap + $total_maret_biaya_bahan_rap + $total_maret_biaya_alat_rap + $total_april_biaya_bahan_rap + $total_april_biaya_alat_rap;
			$akumulasi_pemakaian_mei = $total_bahan_now + $alat_now + $total_november_biaya_bahan_rap + $total_november_biaya_alat_rap + $total_desember_biaya_bahan_rap + $total_desember_biaya_alat_rap + $total_januari_biaya_bahan_rap + $total_januari_biaya_alat_rap + $total_februari_biaya_bahan_rap + $total_februari_biaya_alat_rap + $total_maret_biaya_bahan_rap + $total_maret_biaya_alat_rap + $total_april_biaya_bahan_rap + $total_april_biaya_alat_rap + $total_mei_biaya_bahan_rap + $total_mei_biaya_alat_rap;
			$akumulasi_pemakaian_juni = $total_bahan_now + $alat_now + $total_november_biaya_bahan_rap + $total_november_biaya_alat_rap + $total_desember_biaya_bahan_rap + $total_desember_biaya_alat_rap + $total_januari_biaya_bahan_rap + $total_januari_biaya_alat_rap + $total_februari_biaya_bahan_rap + $total_februari_biaya_alat_rap + $total_maret_biaya_bahan_rap + $total_maret_biaya_alat_rap + $total_april_biaya_bahan_rap + $total_april_biaya_alat_rap + $total_mei_biaya_bahan_rap + $total_mei_biaya_alat_rap  + $total_juni_biaya_bahan_rap + $total_juni_biaya_alat_rap;
			$akumulasi_pemakaian_juli = $total_bahan_now + $alat_now + $total_november_biaya_bahan_rap + $total_november_biaya_alat_rap + $total_desember_biaya_bahan_rap + $total_desember_biaya_alat_rap + $total_januari_biaya_bahan_rap + $total_januari_biaya_alat_rap + $total_februari_biaya_bahan_rap + $total_februari_biaya_alat_rap + $total_maret_biaya_bahan_rap + $total_maret_biaya_alat_rap + $total_april_biaya_bahan_rap + $total_april_biaya_alat_rap + $total_mei_biaya_bahan_rap + $total_mei_biaya_alat_rap  + $total_juni_biaya_bahan_rap + $total_juni_biaya_alat_rap + $total_juli_biaya_bahan_rap + $total_juli_biaya_alat_rap;
			$akumulasi_3 = $akumulasi_2 - $akumulasi_pemakaian_rap_bahan_alat;
			$jumlah_akumulasi = $akumulasi_pemakaian_juli;
			?>
			<tr class="table-active2-csf">
				<th class="text-left" id="boxpemakaian43" style="display:none;"><i>AKUMULASI PEMAKAIAN</i></th>
				<th class="text-right" id="boxpemakaian44" style="display:none;"><?php echo number_format($akumulasi_3,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian45" style="display:none;"><?php echo number_format($total_bahan_now + $alat_now,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian46" style="display:none;"><?php echo number_format($akumulasi_pemakaian_november,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian47" style="display:none;"><?php echo number_format($akumulasi_pemakaian_desember,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian48" style="display:none;"><?php echo number_format($akumulasi_pemakaian_januari,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian49" style="display:none;"><?php echo number_format($akumulasi_pemakaian_februari,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian50" style="display:none;"><?php echo number_format($akumulasi_pemakaian_maret,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian51" style="display:none;"><?php echo number_format($akumulasi_pemakaian_april,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian52" style="display:none;"><?php echo number_format($akumulasi_pemakaian_mei,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian53" style="display:none;"><?php echo number_format($akumulasi_pemakaian_juni,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian54" style="display:none;"><?php echo number_format($akumulasi_pemakaian_juli,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian55" style="display:none;"><?php echo number_format($jumlah_akumulasi,0,',','.');?></th>
				<th class="text-right" id="boxpemakaian56" style="display:none;"><?php echo number_format($sisa_pemakaian_rap,0,',','.');?></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left" colspan="14"><u>PENGELUARAN (EXCL. PPN)</u> <button id="btnpengeluaran3">Buka</th>
			</tr>
			<?php
			$jumlah_biaya_bahan = $total_november_biaya_bahan + $total_desember_biaya_bahan + $total_januari_biaya_bahan + $total_februari_biaya_bahan + $total_maret_biaya_bahan + $total_april_biaya_bahan + $total_mei_biaya_bahan + $total_juni_biaya_bahan + $total_juli_biaya_bahan;
			$sisa_biaya_bahan = $total_rap_2022_biaya_bahan - $jumlah_biaya_bahan;
			?>
			<tr class="table-active3-csf">
				<th class="text-left" id="boxpengeluaran1" style="display:none;">&nbsp;&nbsp;1. Biaya Bahan</th>
				<th class="text-right" id="boxpengeluaran2" style="display:none;"><?php echo number_format($total_rap_2022_biaya_bahan,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran3" style="display:none;"><?php echo number_format($pembayaran_bahan_now,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran4" style="display:none;"><?php echo number_format($total_november_biaya_bahan,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran5" style="display:none;"><?php echo number_format($total_desember_biaya_bahan,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran6" style="display:none;"><?php echo number_format($total_januari_biaya_bahan,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran7" style="display:none;"><?php echo number_format($total_februari_biaya_bahan,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran8" style="display:none;"><?php echo number_format($total_maret_biaya_bahan,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran9" style="display:none;"><?php echo number_format($total_april_biaya_bahan,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran10" style="display:none;"><?php echo number_format($total_mei_biaya_bahan,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran11" style="display:none;"><?php echo number_format($total_juni_biaya_bahan,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran12" style="display:none;"><?php echo number_format($total_juli_biaya_bahan,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran13" style="display:none;"><?php echo number_format($jumlah_biaya_bahan,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran14" style="display:none;"><?php echo number_format($sisa_biaya_bahan,0,',','.');?></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left" id="boxpengeluaran15" style="display:none;">&nbsp;&nbsp;2. Biaya Upah</th>
				<th class="text-right" id="boxpengeluaran16" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran17" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran18" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran19" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran20" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran21" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran22" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran23" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran24" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran25" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran26" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran27" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran28" style="display:none;">-</th>
			</tr>
			<?php
			$jumlah_biaya_alat = $total_november_biaya_alat + $total_desember_biaya_alat + $total_januari_biaya_alat + $total_februari_biaya_alat + $total_maret_biaya_alat + $total_april_biaya_alat + $total_mei_biaya_alat + $total_juni_biaya_alat + $total_juli_biaya_alat;
			$sisa_biaya_alat = $total_rap_2022_biaya_alat - $jumlah_biaya_alat;
			?>
			<tr class="table-active3-csf">
				<th class="text-left" id="boxpengeluaran29" style="display:none;">&nbsp;&nbsp;3. Biaya Peralatan</th>
				<th class="text-right" id="boxpengeluaran30" style="display:none;"><?php echo number_format($total_rap_2022_biaya_alat,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran31" style="display:none;"><?php echo number_format($alat_now,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran32" style="display:none;"><?php echo number_format($total_november_biaya_alat,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran33" style="display:none;"><?php echo number_format($total_desember_biaya_alat,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran34" style="display:none;"><?php echo number_format($total_januari_biaya_alat,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran35" style="display:none;"><?php echo number_format($total_februari_biaya_alat,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran36" style="display:none;"><?php echo number_format($total_maret_biaya_alat,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran37" style="display:none;"><?php echo number_format($total_april_biaya_alat,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran38" style="display:none;"><?php echo number_format($total_mei_biaya_alat,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran39" style="display:none;"><?php echo number_format($total_juni_biaya_alat,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran40" style="display:none;"><?php echo number_format($total_juli_biaya_alat,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran41" style="display:none;"><?php echo number_format($jumlah_biaya_alat,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran42" style="display:none;"><?php echo number_format($sisa_biaya_alat,0,',','.');?></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left" id="boxpengeluaran43" style="display:none;">&nbsp;&nbsp;4. Biaya Subkontraktor</th>
				<th class="text-right" id="boxpengeluaran44" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran45" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran46" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran47" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran48" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran49" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran50" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran51" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran52" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran53" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran54" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran55" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran56" style="display:none;">-</th>
			</tr>
			<?php
			$jumlah_biaya_bank = $total_november_biaya_bank + $total_desember_biaya_bank + $total_januari_biaya_bank + $total_februari_biaya_bank + $total_maret_biaya_bank + $total_april_biaya_bank + $total_mei_biaya_bank + $total_juni_biaya_bank + $total_juli_biaya_bank;
			$sisa_biaya_bank = $total_rap_2022_biaya_bank - $jumlah_biaya_bank;
			?>
			<tr class="table-active3-csf">
				<th class="text-left" id="boxpengeluaran57" style="display:none;">&nbsp;&nbsp;5. Biaya Bank</th>
				<th class="text-right" id="boxpengeluaran58" style="display:none;"><?php echo number_format($total_rap_2022_biaya_bank,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran59" style="display:none;"><?php echo number_format($diskonto_now,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran60" style="display:none;"><?php echo number_format($total_november_biaya_bank,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran61" style="display:none;"><?php echo number_format($total_desember_biaya_bank,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran62" style="display:none;"><?php echo number_format($total_januari_biaya_bank,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran63" style="display:none;"><?php echo number_format($total_februari_biaya_bank,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran64" style="display:none;"><?php echo number_format($total_maret_biaya_bank,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran65" style="display:none;"><?php echo number_format($total_april_biaya_bank,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran66" style="display:none;"><?php echo number_format($total_mei_biaya_bank,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran67" style="display:none;"><?php echo number_format($total_juni_biaya_bank,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran68" style="display:none;"><?php echo number_format($total_juli_biaya_bank,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran69" style="display:none;"><?php echo number_format($jumlah_biaya_bank,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran70" style="display:none;"><?php echo number_format($sisa_biaya_bank,0,',','.');?></th>
			</tr>
			<?php
			$jumlah_biaya_overhead = $total_november_biaya_overhead + $total_desember_biaya_overhead + $total_januari_biaya_overhead + $total_februari_biaya_overhead + $total_maret_biaya_overhead + $total_april_biaya_overhead + $total_mei_biaya_overhead + $total_juni_biaya_overhead + $total_juli_biaya_overhead;
			$sisa_biaya_overhead = $total_rap_2022_biaya_overhead - $jumlah_biaya_overhead;
			?>
			<tr class="table-active3-csf">
				<th class="text-left" id="boxpengeluaran71" style="display:none;">&nbsp;&nbsp;6. BAU Proyek</th>
				<th class="text-right" id="boxpengeluaran72" style="display:none;"><?php echo number_format($total_rap_2022_biaya_overhead,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran73" style="display:none;"><?php echo number_format($overhead_now,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran74" style="display:none;"><?php echo number_format($total_november_biaya_overhead,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran75" style="display:none;"><?php echo number_format($total_desember_biaya_overhead,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran76" style="display:none;"><?php echo number_format($total_januari_biaya_overhead,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran77" style="display:none;"><?php echo number_format($total_februari_biaya_overhead,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran78" style="display:none;"><?php echo number_format($total_maret_biaya_overhead,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran79" style="display:none;"><?php echo number_format($total_april_biaya_overhead,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran80" style="display:none;"><?php echo number_format($total_mei_biaya_overhead,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran81" style="display:none;"><?php echo number_format($total_juni_biaya_overhead,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran82" style="display:none;"><?php echo number_format($total_juli_biaya_overhead,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran83" style="display:none;"><?php echo number_format($jumlah_biaya_overhead,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran84" style="display:none;"><?php echo number_format($sisa_biaya_overhead,0,',','.');?></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left" id="boxpengeluaran85" style="display:none;">&nbsp;&nbsp;7. Rupa - Rupa</th>
				<th class="text-right" id="boxpengeluaran86" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran87" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran88" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran89" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran90" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran91" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran92" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran93" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran94" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran95" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran96" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran97" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran98" style="display:none;">-</th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left" id="boxpengeluaran113" style="display:none;">&nbsp;&nbsp;8. Lain - Lain / Susut Aktiva</th>
				<th class="text-right" id="boxpengeluaran114" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran115" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran116" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran117" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran118" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran119" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran120" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran121" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran122" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran123" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran124" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran125" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran126" style="display:none;">-</th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left" id="boxpengeluaran127" style="display:none;">&nbsp;&nbsp;9. PPN Masukan</th>
				<th class="text-right" id="boxpengeluaran128" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran129" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran130" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran131" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran132" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran133" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran134" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran135" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran136" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran137" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran138" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran139" style="display:none;">-</th>
				<th class="text-right" id="boxpengeluaran140" style="display:none;">-</th>
			</tr>
			<?php
			$jumlah_pengeluaran = $total_rap_2022_biaya_bahan + $total_rap_2022_biaya_alat + $total_rap_2022_biaya_overhead + $total_rap_2022_biaya_bank;
			$jumlah_pengeluaran_akumulasi = $pembayaran_bahan_now + $alat_now + $diskonto_now + $overhead_now;
			$jumlah_pengeluaran_november = $total_november_biaya_bahan + $total_november_biaya_alat + $total_november_biaya_overhead + $total_november_biaya_bank;
			$jumlah_pengeluaran_desember = $total_desember_biaya_bahan + $total_desember_biaya_alat + $total_desember_biaya_overhead + $total_desember_biaya_bank;
			$jumlah_pengeluaran_januari = $total_januari_biaya_bahan + $total_januari_biaya_alat + $total_januari_biaya_overhead + $total_januari_biaya_bank;
			$jumlah_pengeluaran_februari = $total_februari_biaya_bahan + $total_februari_biaya_alat + $total_februari_biaya_overhead + $total_februari_biaya_bank;
			$jumlah_pengeluaran_maret = $total_maret_biaya_bahan + $total_maret_biaya_alat + $total_maret_biaya_overhead + $total_maret_biaya_bank;
			$jumlah_pengeluaran_april = $total_april_biaya_bahan + $total_april_biaya_alat + $total_april_biaya_overhead + $total_april_biaya_bank;
			$jumlah_pengeluaran_mei = $total_mei_biaya_bahan + $total_mei_biaya_alat + $total_mei_biaya_overhead + $total_mei_biaya_bank;
			$jumlah_pengeluaran_juni = $total_juni_biaya_bahan + $total_juni_biaya_alat + $total_juni_biaya_overhead + $total_juni_biaya_bank;
			$jumlah_pengeluaran_juli = $total_juli_biaya_bahan + $total_juli_biaya_alat + $total_juli_biaya_overhead + $total_juli_biaya_bank;
			$total_pengeluaran = $jumlah_pengeluaran_november + $jumlah_pengeluaran_desember + $jumlah_pengeluaran_januari + $jumlah_pengeluaran_februari + $jumlah_pengeluaran_maret + $jumlah_pengeluaran_april + $jumlah_pengeluaran_mei + $jumlah_pengeluaran_juni + $jumlah_pengeluaran_juli;
			$sisa_pengeluaran = $jumlah_pengeluaran - $total_pengeluaran;
			?>
			<tr class="table-active2-csf">
				<th class="text-left" id="boxpengeluaran141" style="display:none;"><i>JUMLAH PENGELUARAN</i></th>
				<th class="text-right" id="boxpengeluaran142" style="display:none;"><?php echo number_format($jumlah_pengeluaran,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran143" style="display:none;"><?php echo number_format($jumlah_pengeluaran_akumulasi,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran144" style="display:none;"><?php echo number_format($jumlah_pengeluaran_november,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran145" style="display:none;"><?php echo number_format($jumlah_pengeluaran_desember,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran146" style="display:none;"><?php echo number_format($jumlah_pengeluaran_januari,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran147" style="display:none;"><?php echo number_format($jumlah_pengeluaran_februari,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran148" style="display:none;"><?php echo number_format($jumlah_pengeluaran_maret,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran149" style="display:none;"><?php echo number_format($jumlah_pengeluaran_april,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran150" style="display:none;"><?php echo number_format($jumlah_pengeluaran_mei,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran151" style="display:none;"><?php echo number_format($jumlah_pengeluaran_juni,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran152" style="display:none;"><?php echo number_format($jumlah_pengeluaran_juli,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran153" style="display:none;"><?php echo number_format($total_pengeluaran,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran154" style="display:none;"><?php echo number_format($sisa_pengeluaran,0,',','.');?></th>
			</tr>
			<?php
			$akumulasi_4 = $akumulasi_2 - $jumlah_pengeluaran;
			$jumlah_akumulasi_now = $akumulasi_4 - $jumlah_pengeluaran_akumulasi;
			$jumlah_akumulasi_november = $jumlah_akumulasi_now - $jumlah_pengeluaran_november;
			$jumlah_akumulasi_desember = $jumlah_akumulasi_november - $jumlah_pengeluaran_desember;
			$jumlah_akumulasi_januari = $jumlah_akumulasi_desember - $jumlah_pengeluaran_januari;
			$jumlah_akumulasi_februari = $jumlah_akumulasi_januari - $jumlah_pengeluaran_februari;
			$jumlah_akumulasi_maret = $jumlah_akumulasi_februari - $jumlah_pengeluaran_januari;
			$jumlah_akumulasi_april = $jumlah_akumulasi_maret - $jumlah_pengeluaran_januari;
			$jumlah_akumulasi_mei = $jumlah_akumulasi_april - $jumlah_pengeluaran_mei;
			$jumlah_akumulasi_juni = $jumlah_akumulasi_mei - $jumlah_pengeluaran_juni;
			$jumlah_akumulasi_juli = $jumlah_akumulasi_juni - $jumlah_pengeluaran_juli;
			$total_akumulasi = $jumlah_akumulasi_juli - $total_pengeluaran;
			$sisa_akumulasi = $akumulasi_4 - $total_akumulasi;
			?>
			<tr class="table-active2-csf">
				<th class="text-left" id="boxpengeluaran155" style="display:none;"><i>AKUMULASI PENGELUARAN</i></th>
				<th class="text-right" id="boxpengeluaran156" style="display:none;"><?php echo number_format($akumulasi_4,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran157" style="display:none;"><?php echo number_format($jumlah_akumulasi_now,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran158" style="display:none;"><?php echo number_format($jumlah_akumulasi_november,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran159" style="display:none;"><?php echo number_format($jumlah_akumulasi_desember,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran160" style="display:none;"><?php echo number_format($jumlah_akumulasi_januari,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran161" style="display:none;"><?php echo number_format($jumlah_akumulasi_februari,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran162" style="display:none;"><?php echo number_format($jumlah_akumulasi_maret,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran163" style="display:none;"><?php echo number_format($jumlah_akumulasi_april,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran164" style="display:none;"><?php echo number_format($jumlah_akumulasi_mei,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran165" style="display:none;"><?php echo number_format($jumlah_akumulasi_juni,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran166" style="display:none;"><?php echo number_format($jumlah_akumulasi_juli,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran167" style="display:none;"><?php echo number_format($total_akumulasi,0,',','.');?></th>
				<th class="text-right" id="boxpengeluaran168" style="display:none;"><?php echo number_format($sisa_akumulasi,0,',','.');?></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left" colspan="14"><u>PAJAK</u> <button id="btnpajak3">Buka</th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left" id="boxpajak1" style="display:none;">&nbsp;&nbsp;1. Pajak Keluaran</th>
				<th class="text-right" id="boxpajak2" style="display:none;"><?php echo number_format((($total_rap_nilai_2022 * 11) / 100),0,',','.');?></th>
				<th class="text-right" id="boxpajak3" style="display:none;"><?php echo number_format($ppn_keluar_now['total'],0,',','.');?></th>
				<th class="text-right" id="boxpajak4" style="display:none;">-</th>
				<th class="text-right" id="boxpajak5" style="display:none;">-</th>
				<th class="text-right" id="boxpajak6" style="display:none;">-</th>
				<th class="text-right" id="boxpajak7" style="display:none;">-</th>
				<th class="text-right" id="boxpajak8" style="display:none;">-</th>
				<th class="text-right" id="boxpajak9" style="display:none;">-</th>
				<th class="text-right" id="boxpajak10" style="display:none;">-</th>
				<th class="text-right" id="boxpajak11" style="display:none;">-</th>
				<th class="text-right" id="boxpajak12" style="display:none;">-</th>
				<th class="text-right" id="boxpajak13" style="display:none;"><?php echo number_format($ppn_keluar_now['total'],0,',','.');?></th>
				<th class="text-right" id="boxpajak14" style="display:none;"><?php echo number_format((($total_rap_nilai_2022 * 11) / 100) - $ppn_keluar_now['total'],0,',','.');?></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left" id="boxpajak15" style="display:none;">&nbsp;&nbsp;2. Pajak Masukan</th>
				<th class="text-right" id="boxpajak16" style="display:none;"><?php echo number_format((($total_rap_2022_biaya_bahan * 11) / 100),0,',','.');?></th>
				<th class="text-right" id="boxpajak17" style="display:none;"><?php echo number_format($ppn_masuk_now['total'],0,',','.');?></th>
				<th class="text-right" id="boxpajak18" style="display:none;">-</th>
				<th class="text-right" id="boxpajak19" style="display:none;">-</th>
				<th class="text-right" id="boxpajak20" style="display:none;">-</th>
				<th class="text-right" id="boxpajak21" style="display:none;">-</th>
				<th class="text-right" id="boxpajak22" style="display:none;">-</th>
				<th class="text-right" id="boxpajak23" style="display:none;">-</th>
				<th class="text-right" id="boxpajak24" style="display:none;">-</th>
				<th class="text-right" id="boxpajak25" style="display:none;">-</th>
				<th class="text-right" id="boxpajak26" style="display:none;">-</th>
				<th class="text-right" id="boxpajak27" style="display:none;"><?php echo number_format($ppn_masuk_now['total'],0,',','.');?></th>
				<th class="text-right" id="boxpajak28" style="display:none;"><?php echo number_format((($total_rap_2022_biaya_bahan * 11) / 100) - $ppn_masuk_now['total'],0,',','.');?></th>
			</tr>
			<tr class="table-active2-csf">
				<th class="text-left" id="boxpajak29" style="display:none;"><i>JUMLAH PAJAK</i></th>
				<th class="text-right" id="boxpajak30" style="display:none;"><?php echo number_format((($total_rap_nilai_2022 * 11) / 100) - (($total_rap_2022_biaya_bahan * 11) / 100),0,',','.');?></th>
				<th class="text-right" id="boxpajak31" style="display:none;"><?php echo number_format($ppn_keluar_now['total'] - $ppn_masuk_now['total'],0,',','.');?></th>
				<th class="text-right" id="boxpajak32" style="display:none;">-</th>
				<th class="text-right" id="boxpajak33" style="display:none;">-</th>
				<th class="text-right" id="boxpajak34" style="display:none;">-</th>
				<th class="text-right" id="boxpajak35" style="display:none;">-</th>
				<th class="text-right" id="boxpajak36" style="display:none;">-</th>
				<th class="text-right" id="boxpajak37" style="display:none;">-</th>
				<th class="text-right" id="boxpajak38" style="display:none;">-</th>
				<th class="text-right" id="boxpajak39" style="display:none;">-</th>
				<th class="text-right" id="boxpajak40" style="display:none;">-</th>
				<th class="text-right" id="boxpajak41" style="display:none;"><?php echo number_format($ppn_keluar_now['total'] - $ppn_masuk_now['total'],0,',','.');?></th>
				<th class="text-right" id="boxpajak42" style="display:none;"><?php echo number_format(($total_rap_nilai_2022 / 10 - $total_rap_2022_biaya_bahan  / 10) - ($ppn_keluar_now['total'] - $ppn_masuk_now['total']),0,',','.');?></th>
			</tr>
			<?php
			$akumulasi_5 = $akumulasi_4 - ((($total_rap_nilai_2022 * 11) / 100) - (($total_rap_2022_biaya_bahan * 11) / 100));
			$total_akumulasi_pajak = $akumulasi_5 - ($ppn_keluar_now['total'] - $ppn_masuk_now['total']);
			$sisa_akumulasi_pajak = $total_akumulasi_pajak - ($ppn_keluar_now['total'] - $ppn_masuk_now['total']);
			?>
			<tr class="table-active2-csf">
				<th class="text-left" id="boxpajak43" style="display:none;"><i>AKUMULASI PAJAK</i></th>
				<th class="text-right" id="boxpajak44" style="display:none;"><?php echo number_format($akumulasi_5,0,',','.');?></th>
				<th class="text-right" id="boxpajak45" style="display:none;"><?php echo number_format($akumulasi_5 - ($ppn_keluar_now['total'] - $ppn_masuk_now['total']),0,',','.');?></th>
				<th class="text-right" id="boxpajak46" style="display:none;">-</th>
				<th class="text-right" id="boxpajak47" style="display:none;">-</th>
				<th class="text-right" id="boxpajak48" style="display:none;">-</th>
				<th class="text-right" id="boxpajak49" style="display:none;">-</th>
				<th class="text-right" id="boxpajak50" style="display:none;">-</th>
				<th class="text-right" id="boxpajak51" style="display:none;">-</th>
				<th class="text-right" id="boxpajak52" style="display:none;">-</th>
				<th class="text-right" id="boxpajak53" style="display:none;">-</th>
				<th class="text-right" id="boxpajak54" style="display:none;">-</th>
				<th class="text-right" id="boxpajak55" style="display:none;"><?php echo number_format($total_akumulasi_pajak,0,',','.');?></th>
				<th class="text-right" id="boxpajak56" style="display:none;"><?php echo number_format($sisa_akumulasi_pajak,0,',','.');?></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-left" colspan="14"><u>PINJAMAN</u> <button id="btnpinjaman3">Buka</th>
			</tr>
            <?php
            $total_penerimaan_penjualan = $penerimaan_penjualan_now['total'] + $penerimaan_penjualan_november['total'] + $penerimaan_penjualan_desember['total'] + $penerimaan_penjualan_januari['total'] + $penerimaan_penjualan_februari['total'] + $penerimaan_penjualan_maret['total'] + $penerimaan_penjualan_april['total'] + $penerimaan_penjualan_mei['total'] + $penerimaan_penjualan_juni['total'] + $penerimaan_penjualan_juli['total'];
            $sisa_penerimaan_penjualan = $penerimaan_penjualan_now['total'] - $total_penerimaan_penjualan;
            ?>
			<tr class="table-active3-csf">
				<th class="text-left" id="boxpinjaman1" style="display:none;">&nbsp;&nbsp;Penerimaan Pinjaman</th>
				<th class="text-right" id="boxpinjaman2" style="display:none;"><?php echo number_format($penerimaan_penjualan_now['total'],0,',','.');?></th>
				<th class="text-right" id="boxpinjaman3" style="display:none;"><?php echo number_format($penerimaan_penjualan_now['total'],0,',','.');?></th>
				<th class="text-right" id="boxpinjaman4" style="display:none;"><?php echo number_format($penerimaan_penjualan_november['total'],0,',','.');?></th>
				<th class="text-right" id="boxpinjaman5" style="display:none;"><?php echo number_format($penerimaan_penjualan_desember['total'],0,',','.');?></th>
				<th class="text-right" id="boxpinjaman6" style="display:none;"><?php echo number_format($penerimaan_penjualan_januari['total'],0,',','.');?></th>
				<th class="text-right" id="boxpinjaman7" style="display:none;"><?php echo number_format($penerimaan_penjualan_februari['total'],0,',','.');?></th>
				<th class="text-right" id="boxpinjaman8" style="display:none;"><?php echo number_format($penerimaan_penjualan_maret['total'],0,',','.');?></th>
				<th class="text-right" id="boxpinjaman9" style="display:none;"><?php echo number_format($penerimaan_penjualan_april['total'],0,',','.');?></th>
				<th class="text-right" id="boxpinjaman10" style="display:none;"><?php echo number_format($penerimaan_penjualan_mei['total'],0,',','.');?></th>
				<th class="text-right" id="boxpinjaman11" style="display:none;"><?php echo number_format($penerimaan_penjualan_juni['total'],0,',','.');?></th>
				<th class="text-right" id="boxpinjaman12" style="display:none;"><?php echo number_format($penerimaan_penjualan_juli['total'],0,',','.');?></th>
				<th class="text-right" id="boxpinjaman13" style="display:none;"><?php echo number_format($total_penerimaan_penjualan,0,',','.');?></th>
				<th class="text-right" id="boxpinjaman14" style="display:none;"><?php echo number_format($sisa_penerimaan_penjualan,0,',','.');?></th>
			</tr>
            <?php
            $total_pengembalian_penjualan = $pengembalian_penjualan_now['total'] + $pengembalian_penjualan_november['total'] + $pengembalian_penjualan_desember['total'] + $pengembalian_penjualan_januari['total'] + $pengembalian_penjualan_februari['total'] + $pengembalian_penjualan_maret['total'] + $pengembalian_penjualan_april['total'] + $pengembalian_penjualan_mei['total'] + $pengembalian_penjualan_juni['total'] + $pengembalian_penjualan_juli['total'];
            $sisa_pengembalian_penjualan = $pengembalian_penjualan_now['total'] - $total_pengembalian_penjualan;
            ?>
			<tr class="table-active3-csf">
				<th class="text-left" id="boxpinjaman15" style="display:none;">&nbsp;&nbsp;Pengembalian Pinjaman</th>
				<th class="text-right" id="boxpinjaman16" style="display:none;"><?php echo number_format($pengembalian_penjualan_now['total'],0,',','.');?></th>
				<th class="text-right" id="boxpinjaman17" style="display:none;"><?php echo number_format($pengembalian_penjualan_now['total'],0,',','.');?></th>
				<th class="text-right" id="boxpinjaman18" style="display:none;"><?php echo number_format($pengembalian_penjualan_november['total'],0,',','.');?></th>
				<th class="text-right" id="boxpinjaman19" style="display:none;"><?php echo number_format($pengembalian_penjualan_desember['total'],0,',','.');?></th>
				<th class="text-right" id="boxpinjaman20" style="display:none;"><?php echo number_format($pengembalian_penjualan_januari['total'],0,',','.');?></th>
				<th class="text-right" id="boxpinjaman21" style="display:none;"><?php echo number_format($pengembalian_penjualan_februari['total'],0,',','.');?></th>
				<th class="text-right" id="boxpinjaman22" style="display:none;"><?php echo number_format($pengembalian_penjualan_maret['total'],0,',','.');?></th>
				<th class="text-right" id="boxpinjaman23" style="display:none;"><?php echo number_format($pengembalian_penjualan_april['total'],0,',','.');?></th>
				<th class="text-right" id="boxpinjaman24" style="display:none;"><?php echo number_format($pengembalian_penjualan_mei['total'],0,',','.');?></th>
				<th class="text-right" id="boxpinjaman25" style="display:none;"><?php echo number_format($pengembalian_penjualan_juni['total'],0,',','.');?></th>
				<th class="text-right" id="boxpinjaman26" style="display:none;"><?php echo number_format($pengembalian_penjualan_juli['total'],0,',','.');?></th>
				<th class="text-right" id="boxpinjaman27" style="display:none;"><?php echo number_format($total_pengembalian_penjualan,0,',','.');?></th>
				<th class="text-right" id="boxpinjaman28" style="display:none;"><?php echo number_format($sisa_pengembalian_penjualan,0,',','.');?></th>
			</tr>
			<?php
			$jumlah_vii_rap = $penerimaan_penjualan_now['total'] + $pengembalian_penjualan_now['total'];
			$jumlah_vii_now = $penerimaan_penjualan_now['total'] + $pengembalian_penjualan_now['total'];
			$jumlah_vii_november = $penerimaan_penjualan_november['total'] + $pengembalian_penjualan_november['total'];
			$jumlah_vii_desember = $penerimaan_penjualan_desember['total'] + $pengembalian_penjualan_desember['total'];
			$jumlah_vii_januari = $penerimaan_penjualan_januari['total'] + $pengembalian_penjualan_januari['total'];
			$jumlah_vii_februari = $penerimaan_penjualan_februari['total'] + $pengembalian_penjualan_februari['total'];
			$jumlah_vii_maret = $penerimaan_penjualan_maret['total'] + $pengembalian_penjualan_maret['total'];
			$jumlah_vii_april = $penerimaan_penjualan_april['total'] + $pengembalian_penjualan_april['total'];
			$jumlah_vii_mei = $penerimaan_penjualan_mei['total'] + $pengembalian_penjualan_mei['total'];
			$jumlah_vii_juni = $penerimaan_penjualan_juni['total'] + $pengembalian_penjualan_juni['total'];
			$jumlah_vii_juli = $penerimaan_penjualan_juli['total'] + $pengembalian_penjualan_juli['total'];
			$total_jumlah_vii = $total_penerimaan_penjualan + $total_pengembalian_penjualan;
			$sisa_jumlah_vii = $total_penerimaan_penjualan - $total_jumlah_vii; 
			?>
			<tr class="table-active2-csf">
				<th class="text-left" id="boxpinjaman29" style="display:none;"><i>JUMLAH PINJAMAN</i></th>
				<th class="text-right" id="boxpinjaman30" style="display:none;"><?php echo number_format($jumlah_vii_rap,0,',','.');?></th>
				<th class="text-right" id="boxpinjaman31" style="display:none;"><?php echo number_format($jumlah_vii_now,0,',','.');?></th>
				<th class="text-right" id="boxpinjaman32" style="display:none;"><?php echo number_format($jumlah_vii_november,0,',','.');?></th>
				<th class="text-right" id="boxpinjaman33" style="display:none;"><?php echo number_format($jumlah_vii_desember,0,',','.');?></th>
				<th class="text-right" id="boxpinjaman34" style="display:none;"><?php echo number_format($jumlah_vii_januari,0,',','.');?></th>
				<th class="text-right" id="boxpinjaman35" style="display:none;"><?php echo number_format($jumlah_vii_februari,0,',','.');?></th>
				<th class="text-right" id="boxpinjaman36" style="display:none;"><?php echo number_format($jumlah_vii_maret,0,',','.');?></th>
				<th class="text-right" id="boxpinjaman37" style="display:none;"><?php echo number_format($jumlah_vii_april,0,',','.');?></th>
				<th class="text-right" id="boxpinjaman38" style="display:none;"><?php echo number_format($jumlah_vii_mei,0,',','.');?></th>
				<th class="text-right" id="boxpinjaman39" style="display:none;"><?php echo number_format($jumlah_vii_juni,0,',','.');?></th>
				<th class="text-right" id="boxpinjaman40" style="display:none;"><?php echo number_format($jumlah_vii_juli,0,',','.');?></th>
				<th class="text-right" id="boxpinjaman41" style="display:none;"><?php echo number_format($total_jumlah_vii,0,',','.');?></th>
				<th class="text-right" id="boxpinjaman42" style="display:none;"><?php echo number_format($sisa_jumlah_vii,0,',','.');?></th>
			</tr>
			<?php
			$akumulasi_6 = $akumulasi_5 - $jumlah_vii_rap;
			$posisi_vi_now = $akumulasi_6 - $jumlah_vii_now;
			$posisi_vi_november = $posisi_vi_now - $jumlah_vii_november;
			$posisi_vi_desember = $posisi_vi_november - $jumlah_vii_desember;
			$posisi_vi_januari = $posisi_vi_desember - $jumlah_vii_januari;
			$posisi_vi_februari = $posisi_vi_januari - $jumlah_vii_februari;
			$posisi_vi_maret = $posisi_vi_februari - $jumlah_vii_maret;
			$posisi_vi_april = $posisi_vi_maret - $jumlah_vii_april;
			$posisi_vi_mei = $posisi_vi_april - $jumlah_vii_mei;
			$posisi_vi_juni = $posisi_vi_mei - $jumlah_vii_juni;
			$posisi_vi_juli = $posisi_vi_juni - $jumlah_vii_juli;
			$posisi_vi_total = $posisi_vi_juli - $total_jumlah_vii;
			$posisi_vi_sisa = $akumulasi_6 - $posisi_vi_total;
			?>
			<tr class="table-active2-csf">
				<th class="text-left" id="boxpinjaman43" style="display:none;"><i>AKUMULASI PINJAMAN</i></th>
				<th class="text-right" id="boxpinjaman44" style="display:none;"><?php echo number_format($akumulasi_6,0,',','.');?></th>
				<th class="text-right" id="boxpinjaman45" style="display:none;"><?php echo number_format($posisi_vi_now,0,',','.');?></th>
				<th class="text-right" id="boxpinjaman46" style="display:none;"><?php echo number_format($posisi_vi_november,0,',','.');?></th>
				<th class="text-right" id="boxpinjaman47" style="display:none;"><?php echo number_format($posisi_vi_desember,0,',','.');?></th>
				<th class="text-right" id="boxpinjaman48" style="display:none;"><?php echo number_format($posisi_vi_januari,0,',','.');?></th>
				<th class="text-right" id="boxpinjaman49" style="display:none;"><?php echo number_format($posisi_vi_februari,0,',','.');?></th>
				<th class="text-right" id="boxpinjaman50" style="display:none;"><?php echo number_format($posisi_vi_maret,0,',','.');?></th>
				<th class="text-right" id="boxpinjaman51" style="display:none;"><?php echo number_format($posisi_vi_april,0,',','.');?></th>
				<th class="text-right" id="boxpinjaman52" style="display:none;"><?php echo number_format($posisi_vi_mei,0,',','.');?></th>
				<th class="text-right" id="boxpinjaman53" style="display:none;"><?php echo number_format($posisi_vi_juni,0,',','.');?></th>
				<th class="text-right" id="boxpinjaman54" style="display:none;"><?php echo number_format($posisi_vi_juli,0,',','.');?></th>
				<th class="text-right" id="boxpinjaman55" style="display:none;"><?php echo number_format($posisi_vi_total,0,',','.');?></th>
				<th class="text-right" id="boxpinjaman56" style="display:none;"><?php echo number_format($posisi_vi_sisa,0,',','.');?></th>
			</tr>
			<tr class="table-active2-csf">
				<th class="text-left"><i>KAS AWAL</i></th>
				<th class="text-right"><?php echo number_format($akumulasi_6,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_vi_now,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_vi_november,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_vi_desember,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_vi_januari,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_vi_februari,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_vi_maret,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_vi_april,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_vi_mei,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_vi_juni,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_vi_juli,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_vi_total,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_vi_sisa,0,',','.');?></th>
			</tr>
			<tr class="table-active2-csf">
				<th class="text-left"><i>KAS AKHIR</i></th>
				<th class="text-right"><?php echo number_format($akumulasi_6,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_vi_now,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_vi_november,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_vi_desember,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_vi_januari,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_vi_februari,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_vi_maret,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_vi_april,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_vi_mei,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_vi_juni,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_vi_juli,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_vi_total,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($posisi_vi_sisa,0,',','.');?></th>
			</tr>
	    </table>
		<script>
			$('#btnpenerimaan3').click(function(){
			$('#boxpenerimaan1').slideToggle();
			$('#boxpenerimaan2').slideToggle();
			$('#boxpenerimaan3').slideToggle();
			$('#boxpenerimaan4').slideToggle();
			$('#boxpenerimaan5').slideToggle();
			$('#boxpenerimaan6').slideToggle();
			$('#boxpenerimaan7').slideToggle();
			$('#boxpenerimaan8').slideToggle();
			$('#boxpenerimaan9').slideToggle();
			$('#boxpenerimaan10').slideToggle();
			$('#boxpenerimaan11').slideToggle();
			$('#boxpenerimaan12').slideToggle();
			$('#boxpenerimaan13').slideToggle();
			$('#boxpenerimaan14').slideToggle();
			$('#boxpenerimaan15').slideToggle();
			$('#boxpenerimaan16').slideToggle();
			$('#boxpenerimaan17').slideToggle();
			$('#boxpenerimaan18').slideToggle();
			$('#boxpenerimaan19').slideToggle();
			$('#boxpenerimaan20').slideToggle();
			$('#boxpenerimaan21').slideToggle();
			$('#boxpenerimaan22').slideToggle();
			$('#boxpenerimaan23').slideToggle();
			$('#boxpenerimaan24').slideToggle();
			$('#boxpenerimaan25').slideToggle();
			$('#boxpenerimaan26').slideToggle();
			$('#boxpenerimaan27').slideToggle();
			$('#boxpenerimaan28').slideToggle();
			$('#boxpenerimaan29').slideToggle();
			$('#boxpenerimaan30').slideToggle();
			$('#boxpenerimaan31').slideToggle();
			$('#boxpenerimaan32').slideToggle();
			$('#boxpenerimaan33').slideToggle();
			$('#boxpenerimaan34').slideToggle();
			$('#boxpenerimaan35').slideToggle();
			$('#boxpenerimaan36').slideToggle();
			$('#boxpenerimaan37').slideToggle();
			$('#boxpenerimaan38').slideToggle();
			$('#boxpenerimaan39').slideToggle();
			$('#boxpenerimaan40').slideToggle();
			$('#boxpenerimaan41').slideToggle();
			$('#boxpenerimaan42').slideToggle();
			$('#boxpenerimaan43').slideToggle();
			$('#boxpenerimaan44').slideToggle();
			$('#boxpenerimaan45').slideToggle();
			$('#boxpenerimaan46').slideToggle();
			$('#boxpenerimaan47').slideToggle();
			$('#boxpenerimaan48').slideToggle();
			$('#boxpenerimaan49').slideToggle();
			$('#boxpenerimaan50').slideToggle();
			$('#boxpenerimaan51').slideToggle();
			$('#boxpenerimaan52').slideToggle();
			$('#boxpenerimaan53').slideToggle();
			$('#boxpenerimaan54').slideToggle();
			$('#boxpenerimaan55').slideToggle();
			$('#boxpenerimaan56').slideToggle();
			$('#boxpenerimaan57').slideToggle();
			$('#boxpenerimaan58').slideToggle();
			$('#boxpenerimaan59').slideToggle();
			$('#boxpenerimaan60').slideToggle();
			$('#boxpenerimaan61').slideToggle();
			$('#boxpenerimaan62').slideToggle();
			$('#boxpenerimaan63').slideToggle();
			$('#boxpenerimaan64').slideToggle();
			$('#boxpenerimaan65').slideToggle();
			$('#boxpenerimaan66').slideToggle();
			$('#boxpenerimaan67').slideToggle();
			$('#boxpenerimaan68').slideToggle();
			$('#boxpenerimaan69').slideToggle();
			$('#boxpenerimaan70').slideToggle();
			$('#boxpenerimaan71').slideToggle();
			$('#boxpenerimaan72').slideToggle();
			$('#boxpenerimaan73').slideToggle();
			$('#boxpenerimaan74').slideToggle();
			$('#boxpenerimaan75').slideToggle();
			$('#boxpenerimaan76').slideToggle();
			$('#boxpenerimaan77').slideToggle();
			$('#boxpenerimaan78').slideToggle();
			$('#boxpenerimaan79').slideToggle();
			$('#boxpenerimaan80').slideToggle();
			$('#boxpenerimaan81').slideToggle();
			$('#boxpenerimaan82').slideToggle();
			$('#boxpenerimaan83').slideToggle();
			$('#boxpenerimaan84').slideToggle();
			});
		</script>
		<script>
			$('#btnpemakaian3').click(function(){
			$('#boxpemakaian1').slideToggle();
			$('#boxpemakaian2').slideToggle();
			$('#boxpemakaian3').slideToggle();
			$('#boxpemakaian4').slideToggle();
			$('#boxpemakaian5').slideToggle();
			$('#boxpemakaian6').slideToggle();
			$('#boxpemakaian7').slideToggle();
			$('#boxpemakaian8').slideToggle();
			$('#boxpemakaian9').slideToggle();
			$('#boxpemakaian10').slideToggle();
			$('#boxpemakaian11').slideToggle();
			$('#boxpemakaian12').slideToggle();
			$('#boxpemakaian13').slideToggle();
			$('#boxpemakaian14').slideToggle();
			$('#boxpemakaian15').slideToggle();
			$('#boxpemakaian16').slideToggle();
			$('#boxpemakaian17').slideToggle();
			$('#boxpemakaian18').slideToggle();
			$('#boxpemakaian19').slideToggle();
			$('#boxpemakaian20').slideToggle();
			$('#boxpemakaian21').slideToggle();
			$('#boxpemakaian22').slideToggle();
			$('#boxpemakaian23').slideToggle();
			$('#boxpemakaian24').slideToggle();
			$('#boxpemakaian25').slideToggle();
			$('#boxpemakaian26').slideToggle();
			$('#boxpemakaian27').slideToggle();
			$('#boxpemakaian28').slideToggle();
			$('#boxpemakaian29').slideToggle();
			$('#boxpemakaian30').slideToggle();
			$('#boxpemakaian31').slideToggle();
			$('#boxpemakaian32').slideToggle();
			$('#boxpemakaian33').slideToggle();
			$('#boxpemakaian34').slideToggle();
			$('#boxpemakaian35').slideToggle();
			$('#boxpemakaian36').slideToggle();
			$('#boxpemakaian37').slideToggle();
			$('#boxpemakaian38').slideToggle();
			$('#boxpemakaian39').slideToggle();
			$('#boxpemakaian40').slideToggle();
			$('#boxpemakaian41').slideToggle();
			$('#boxpemakaian42').slideToggle();
			$('#boxpemakaian43').slideToggle();
			$('#boxpemakaian44').slideToggle();
			$('#boxpemakaian45').slideToggle();
			$('#boxpemakaian46').slideToggle();
			$('#boxpemakaian47').slideToggle();
			$('#boxpemakaian48').slideToggle();
			$('#boxpemakaian49').slideToggle();
			$('#boxpemakaian50').slideToggle();
			$('#boxpemakaian51').slideToggle();
			$('#boxpemakaian52').slideToggle();
			$('#boxpemakaian53').slideToggle();
			$('#boxpemakaian54').slideToggle();
			$('#boxpemakaian55').slideToggle();
			$('#boxpemakaian56').slideToggle();
			});
		</script>
		<script>
			$('#btnpengeluaran3').click(function(){
			$('#boxpengeluaran1').slideToggle();
			$('#boxpengeluaran2').slideToggle();
			$('#boxpengeluaran3').slideToggle();
			$('#boxpengeluaran4').slideToggle();
			$('#boxpengeluaran5').slideToggle();
			$('#boxpengeluaran6').slideToggle();
			$('#boxpengeluaran7').slideToggle();
			$('#boxpengeluaran8').slideToggle();
			$('#boxpengeluaran9').slideToggle();
			$('#boxpengeluaran10').slideToggle();
			$('#boxpengeluaran11').slideToggle();
			$('#boxpengeluaran12').slideToggle();
			$('#boxpengeluaran13').slideToggle();
			$('#boxpengeluaran14').slideToggle();
			$('#boxpengeluaran15').slideToggle();
			$('#boxpengeluaran16').slideToggle();
			$('#boxpengeluaran17').slideToggle();
			$('#boxpengeluaran18').slideToggle();
			$('#boxpengeluaran19').slideToggle();
			$('#boxpengeluaran20').slideToggle();
			$('#boxpengeluaran21').slideToggle();
			$('#boxpengeluaran22').slideToggle();
			$('#boxpengeluaran23').slideToggle();
			$('#boxpengeluaran24').slideToggle();
			$('#boxpengeluaran25').slideToggle();
			$('#boxpengeluaran26').slideToggle();
			$('#boxpengeluaran27').slideToggle();
			$('#boxpengeluaran28').slideToggle();
			$('#boxpengeluaran29').slideToggle();
			$('#boxpengeluaran30').slideToggle();
			$('#boxpengeluaran31').slideToggle();
			$('#boxpengeluaran32').slideToggle();
			$('#boxpengeluaran33').slideToggle();
			$('#boxpengeluaran34').slideToggle();
			$('#boxpengeluaran35').slideToggle();
			$('#boxpengeluaran36').slideToggle();
			$('#boxpengeluaran37').slideToggle();
			$('#boxpengeluaran38').slideToggle();
			$('#boxpengeluaran39').slideToggle();
			$('#boxpengeluaran40').slideToggle();
			$('#boxpengeluaran41').slideToggle();
			$('#boxpengeluaran42').slideToggle();
			$('#boxpengeluaran43').slideToggle();
			$('#boxpengeluaran44').slideToggle();
			$('#boxpengeluaran45').slideToggle();
			$('#boxpengeluaran46').slideToggle();
			$('#boxpengeluaran47').slideToggle();
			$('#boxpengeluaran48').slideToggle();
			$('#boxpengeluaran49').slideToggle();
			$('#boxpengeluaran50').slideToggle();
			$('#boxpengeluaran51').slideToggle();
			$('#boxpengeluaran52').slideToggle();
			$('#boxpengeluaran53').slideToggle();
			$('#boxpengeluaran54').slideToggle();
			$('#boxpengeluaran55').slideToggle();
			$('#boxpengeluaran56').slideToggle();
			$('#boxpengeluaran57').slideToggle();
			$('#boxpengeluaran58').slideToggle();
			$('#boxpengeluaran59').slideToggle();
			$('#boxpengeluaran60').slideToggle();
			$('#boxpengeluaran61').slideToggle();
			$('#boxpengeluaran62').slideToggle();
			$('#boxpengeluaran63').slideToggle();
			$('#boxpengeluaran64').slideToggle();
			$('#boxpengeluaran65').slideToggle();
			$('#boxpengeluaran66').slideToggle();
			$('#boxpengeluaran67').slideToggle();
			$('#boxpengeluaran68').slideToggle();
			$('#boxpengeluaran69').slideToggle();
			$('#boxpengeluaran70').slideToggle();
			$('#boxpengeluaran71').slideToggle();
			$('#boxpengeluaran72').slideToggle();
			$('#boxpengeluaran73').slideToggle();
			$('#boxpengeluaran74').slideToggle();
			$('#boxpengeluaran75').slideToggle();
			$('#boxpengeluaran76').slideToggle();
			$('#boxpengeluaran77').slideToggle();
			$('#boxpengeluaran78').slideToggle();
			$('#boxpengeluaran79').slideToggle();
			$('#boxpengeluaran80').slideToggle();
			$('#boxpengeluaran81').slideToggle();
			$('#boxpengeluaran82').slideToggle();
			$('#boxpengeluaran83').slideToggle();
			$('#boxpengeluaran84').slideToggle();
			$('#boxpengeluaran85').slideToggle();
			$('#boxpengeluaran86').slideToggle();
			$('#boxpengeluaran87').slideToggle();
			$('#boxpengeluaran88').slideToggle();
			$('#boxpengeluaran89').slideToggle();
			$('#boxpengeluaran90').slideToggle();
			$('#boxpengeluaran91').slideToggle();
			$('#boxpengeluaran92').slideToggle();
			$('#boxpengeluaran93').slideToggle();
			$('#boxpengeluaran94').slideToggle();
			$('#boxpengeluaran95').slideToggle();
			$('#boxpengeluaran96').slideToggle();
			$('#boxpengeluaran97').slideToggle();
			$('#boxpengeluaran98').slideToggle();
			$('#boxpengeluaran113').slideToggle();
			$('#boxpengeluaran114').slideToggle();
			$('#boxpengeluaran115').slideToggle();
			$('#boxpengeluaran116').slideToggle();
			$('#boxpengeluaran117').slideToggle();
			$('#boxpengeluaran118').slideToggle();
			$('#boxpengeluaran119').slideToggle();
			$('#boxpengeluaran120').slideToggle();
			$('#boxpengeluaran121').slideToggle();
			$('#boxpengeluaran122').slideToggle();
			$('#boxpengeluaran123').slideToggle();
			$('#boxpengeluaran124').slideToggle();
			$('#boxpengeluaran125').slideToggle();
			$('#boxpengeluaran126').slideToggle();
			$('#boxpengeluaran127').slideToggle();
			$('#boxpengeluaran128').slideToggle();
			$('#boxpengeluaran129').slideToggle();
			$('#boxpengeluaran130').slideToggle();
			$('#boxpengeluaran131').slideToggle();
			$('#boxpengeluaran132').slideToggle();
			$('#boxpengeluaran133').slideToggle();
			$('#boxpengeluaran134').slideToggle();
			$('#boxpengeluaran135').slideToggle();
			$('#boxpengeluaran136').slideToggle();
			$('#boxpengeluaran137').slideToggle();
			$('#boxpengeluaran138').slideToggle();
			$('#boxpengeluaran139').slideToggle();
			$('#boxpengeluaran140').slideToggle();
			$('#boxpengeluaran141').slideToggle();
			$('#boxpengeluaran142').slideToggle();
			$('#boxpengeluaran143').slideToggle();
			$('#boxpengeluaran144').slideToggle();
			$('#boxpengeluaran145').slideToggle();
			$('#boxpengeluaran146').slideToggle();
			$('#boxpengeluaran147').slideToggle();
			$('#boxpengeluaran148').slideToggle();
			$('#boxpengeluaran149').slideToggle();
			$('#boxpengeluaran150').slideToggle();
			$('#boxpengeluaran151').slideToggle();
			$('#boxpengeluaran152').slideToggle();
			$('#boxpengeluaran153').slideToggle();
			$('#boxpengeluaran154').slideToggle();
			$('#boxpengeluaran155').slideToggle();
			$('#boxpengeluaran156').slideToggle();
			$('#boxpengeluaran157').slideToggle();
			$('#boxpengeluaran158').slideToggle();
			$('#boxpengeluaran159').slideToggle();
			$('#boxpengeluaran160').slideToggle();
			$('#boxpengeluaran161').slideToggle();
			$('#boxpengeluaran162').slideToggle();
			$('#boxpengeluaran163').slideToggle();
			$('#boxpengeluaran164').slideToggle();
			$('#boxpengeluaran165').slideToggle();
			$('#boxpengeluaran166').slideToggle();
			$('#boxpengeluaran167').slideToggle();
			$('#boxpengeluaran168').slideToggle();
			});
		</script>
		<script>
			$('#btnpajak3').click(function(){
			$('#boxpajak1').slideToggle();
			$('#boxpajak2').slideToggle();
			$('#boxpajak3').slideToggle();
			$('#boxpajak4').slideToggle();
			$('#boxpajak5').slideToggle();
			$('#boxpajak6').slideToggle();
			$('#boxpajak7').slideToggle();
			$('#boxpajak8').slideToggle();
			$('#boxpajak9').slideToggle();
			$('#boxpajak10').slideToggle();
			$('#boxpajak11').slideToggle();
			$('#boxpajak12').slideToggle();
			$('#boxpajak13').slideToggle();
			$('#boxpajak14').slideToggle();
			$('#boxpajak15').slideToggle();
			$('#boxpajak16').slideToggle();
			$('#boxpajak17').slideToggle();
			$('#boxpajak18').slideToggle();
			$('#boxpajak19').slideToggle();
			$('#boxpajak20').slideToggle();
			$('#boxpajak21').slideToggle();
			$('#boxpajak22').slideToggle();
			$('#boxpajak23').slideToggle();
			$('#boxpajak24').slideToggle();
			$('#boxpajak25').slideToggle();
			$('#boxpajak26').slideToggle();
			$('#boxpajak27').slideToggle();
			$('#boxpajak28').slideToggle();
			$('#boxpajak29').slideToggle();
			$('#boxpajak30').slideToggle();
			$('#boxpajak31').slideToggle();
			$('#boxpajak32').slideToggle();
			$('#boxpajak33').slideToggle();
			$('#boxpajak34').slideToggle();
			$('#boxpajak35').slideToggle();
			$('#boxpajak36').slideToggle();
			$('#boxpajak37').slideToggle();
			$('#boxpajak38').slideToggle();
			$('#boxpajak39').slideToggle();
			$('#boxpajak40').slideToggle();
			$('#boxpajak41').slideToggle();
			$('#boxpajak42').slideToggle();
			$('#boxpajak43').slideToggle();
			$('#boxpajak44').slideToggle();
			$('#boxpajak45').slideToggle();
			$('#boxpajak46').slideToggle();
			$('#boxpajak47').slideToggle();
			$('#boxpajak48').slideToggle();
			$('#boxpajak49').slideToggle();
			$('#boxpajak50').slideToggle();
			$('#boxpajak51').slideToggle();
			$('#boxpajak52').slideToggle();
			$('#boxpajak53').slideToggle();
			$('#boxpajak54').slideToggle();
			$('#boxpajak55').slideToggle();
			$('#boxpajak56').slideToggle();
			});
		</script>
		<script>
			$('#btnpinjaman3').click(function(){
			$('#boxpinjaman1').slideToggle();
			$('#boxpinjaman2').slideToggle();
			$('#boxpinjaman3').slideToggle();
			$('#boxpinjaman4').slideToggle();
			$('#boxpinjaman5').slideToggle();
			$('#boxpinjaman6').slideToggle();
			$('#boxpinjaman7').slideToggle();
			$('#boxpinjaman8').slideToggle();
			$('#boxpinjaman9').slideToggle();
			$('#boxpinjaman10').slideToggle();
			$('#boxpinjaman11').slideToggle();
			$('#boxpinjaman12').slideToggle();
			$('#boxpinjaman13').slideToggle();
			$('#boxpinjaman14').slideToggle();
			$('#boxpinjaman15').slideToggle();
			$('#boxpinjaman16').slideToggle();
			$('#boxpinjaman17').slideToggle();
			$('#boxpinjaman18').slideToggle();
			$('#boxpinjaman19').slideToggle();
			$('#boxpinjaman20').slideToggle();
			$('#boxpinjaman21').slideToggle();
			$('#boxpinjaman22').slideToggle();
			$('#boxpinjaman23').slideToggle();
			$('#boxpinjaman24').slideToggle();
			$('#boxpinjaman25').slideToggle();
			$('#boxpinjaman26').slideToggle();
			$('#boxpinjaman27').slideToggle();
			$('#boxpinjaman28').slideToggle();
			$('#boxpinjaman29').slideToggle();
			$('#boxpinjaman30').slideToggle();
			$('#boxpinjaman31').slideToggle();
			$('#boxpinjaman32').slideToggle();
			$('#boxpinjaman33').slideToggle();
			$('#boxpinjaman34').slideToggle();
			$('#boxpinjaman35').slideToggle();
			$('#boxpinjaman36').slideToggle();
			$('#boxpinjaman37').slideToggle();
			$('#boxpinjaman38').slideToggle();
			$('#boxpinjaman39').slideToggle();
			$('#boxpinjaman40').slideToggle();
			$('#boxpinjaman41').slideToggle();
			$('#boxpinjaman42').slideToggle();
			$('#boxpinjaman43').slideToggle();
			$('#boxpinjaman44').slideToggle();
			$('#boxpinjaman45').slideToggle();
			$('#boxpinjaman46').slideToggle();
			$('#boxpinjaman47').slideToggle();
			$('#boxpinjaman48').slideToggle();
			$('#boxpinjaman49').slideToggle();
			$('#boxpinjaman50').slideToggle();
			$('#boxpinjaman51').slideToggle();
			$('#boxpinjaman52').slideToggle();
			$('#boxpinjaman53').slideToggle();
			$('#boxpinjaman54').slideToggle();
			$('#boxpinjaman55').slideToggle();
			$('#boxpinjaman56').slideToggle();
			});
		</script>
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

			$komposisi = $this->db->select('pp.date_production, (pp.display_volume) * pk.presentase_a as volume_a, (pp.display_volume) * pk.presentase_b as volume_b, (pp.display_volume) * pk.presentase_c as volume_c, (pp.display_volume) * pk.presentase_d as volume_d, pk.price_a, pk.price_b, pk.price_c, pk.price_d,(pp.display_volume * pk.presentase_a) * pk.price_a as nilai_a, (pp.display_volume * pk.presentase_b) * pk.price_b as nilai_b, (pp.display_volume * pk.presentase_c) * pk.price_c as nilai_c, (pp.display_volume * pk.presentase_d) * pk.price_d as nilai_d')
			->from('pmm_productions pp')
			->join('pmm_agregat pk', 'pp.komposisi_id = pk.id','left')
			->where("pp.date_production between '$date1' and '$date2'")
			->get()->result_array();

			$total_volume_a = 0;
			$total_volume_b = 0;
			$total_volume_c = 0;
			$total_volume_d = 0;

			$total_price_a = 0;
			$total_price_b = 0;
			$total_price_c = 0;
			$total_price_d = 0;

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
				$total_price_a = $x['price_a'];
				$total_price_b = $x['price_b'];
				$total_price_c = $x['price_c'];
				$total_price_d = $x['price_d'];
				
			}

			$volume_a = $total_volume_a;
			$volume_b = $total_volume_b;
			$volume_c = $total_volume_c;
			$volume_d = $total_volume_d;

			$nilai_a = $total_nilai_a;
			$nilai_b = $total_nilai_b;
			$nilai_c = $total_nilai_c;
			$nilai_d = $total_nilai_d;

			$price_a = $total_price_a;
			$price_b = $total_price_b;
			$price_c = $total_price_c;
			$price_d = $total_price_d;

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
			//$total_harga_pemakaian_semen = ($total_volume_pemakaian_semen!=0)?$total_nilai_pemakaian_semen / $total_volume_pemakaian_semen * 1:0;
			$total_harga_pemakaian_semen = $total_harga_stock_semen_akhir;

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
			
			$hpp_bahan_baku = $this->db->select('pp.date_hpp, pp.pasir')
			->from('hpp_bahan_baku pp')
			->where("(pp.date_hpp between '$date1' and '$date2')")
			->order_by('pp.date_hpp','desc')->limit(1)
			->get()->row_array();

			$total_volume_stock_pasir_akhir = $stock_opname_pasir['volume'];
			$price_stock_opname_pasir =  $hpp_bahan_baku['pasir'];

			$total_volume_pemakaian_pasir = $total_volume_pembelian_pasir_akhir - $stock_opname_pasir['volume'];

			$total_harga_stock_pasir_akhir = round($price_stock_opname_pasir,0);
			$total_nilai_stock_pasir_akhir = $total_volume_stock_pasir_akhir * $total_harga_stock_pasir_akhir;

			$total_nilai_pemakaian_pasir = ($nilai_opening_balance_pasir + $total_nilai_pembelian_pasir) - $total_nilai_stock_pasir_akhir;
			//$total_harga_pemakaian_pasir = ($total_volume_pemakaian_pasir!=0)?$total_nilai_pemakaian_pasir / $total_volume_pemakaian_pasir * 1:0;
			$total_harga_pemakaian_pasir = $total_harga_stock_pasir_akhir;

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

			$hpp_bahan_baku = $this->db->select('pp.date_hpp, pp.batu1020')
			->from('hpp_bahan_baku pp')
			->where("(pp.date_hpp between '$date1' and '$date2')")
			->order_by('pp.date_hpp','desc')->limit(1)
			->get()->row_array();

			$total_volume_stock_batu1020_akhir = $stock_opname_batu1020['volume'];
			$price_stock_opname_batu1020 =  $hpp_bahan_baku['batu1020'];

			$total_volume_pemakaian_batu1020 = $total_volume_pembelian_batu1020_akhir - $stock_opname_batu1020['volume'];

			$total_harga_stock_batu1020_akhir = round($price_stock_opname_batu1020,0);
			$total_nilai_stock_batu1020_akhir = $total_volume_stock_batu1020_akhir * $total_harga_stock_batu1020_akhir;

			$total_nilai_pemakaian_batu1020 = ($nilai_opening_balance_batu1020 + $total_nilai_pembelian_batu1020) - $total_nilai_stock_batu1020_akhir;
			//$total_harga_pemakaian_batu1020 = ($total_volume_pemakaian_batu1020!=0)?$total_nilai_pemakaian_batu1020 / $total_volume_pemakaian_batu1020 * 1:0;
			$total_harga_pemakaian_batu1020 = $total_harga_stock_batu1020_akhir;

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
			
			$hpp_bahan_baku = $this->db->select('pp.date_hpp, pp.batu2030')
			->from('hpp_bahan_baku pp')
			->where("(pp.date_hpp between '$date1' and '$date2')")
			->order_by('pp.date_hpp','desc')->limit(1)
			->get()->row_array();

			$total_volume_stock_batu2030_akhir = $stock_opname_batu2030['volume'];
			$price_stock_opname_batu2030 =  $hpp_bahan_baku['batu2030'];

			$total_volume_pemakaian_batu2030 = $total_volume_pembelian_batu2030_akhir - $stock_opname_batu2030['volume'];

			$total_harga_stock_batu2030_akhir = round($price_stock_opname_batu2030,0);
			$total_nilai_stock_batu2030_akhir = $total_volume_stock_batu2030_akhir * $total_harga_stock_batu2030_akhir;

			$total_nilai_pemakaian_batu2030 = ($nilai_opening_balance_batu2030 + $total_nilai_pembelian_batu2030) - $total_nilai_stock_batu2030_akhir;
			//$total_harga_pemakaian_batu2030 = ($total_volume_pemakaian_batu2030!=0)?$total_nilai_pemakaian_batu2030 / $total_volume_pemakaian_batu2030 * 1:0;
			$total_harga_pemakaian_batu2030 = $total_harga_stock_batu2030_akhir;

			//BAHAN BAKU
			$total_opening_balance_bahan_baku = $nilai_opening_balance_semen + $nilai_opening_balance_pasir + $nilai_opening_balance_batu1020 + $nilai_opening_balance_batu2030;

			//TOTAL
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
				<th width="20%" class="text-center" colspan="3">KOMPOSISI</th>
				<th width="20%" class="text-center" colspan="3">REALISASI</th>
				<th width="20%" class="text-center" colspan="2">EVALUASI</th>
	        </tr>
			<tr class="table-active4">
				<th class="text-center">VOLUME</th>
				<th class="text-center">HARSAT</th>
				<th class="text-center">NILAI</th>
				<th class="text-center">VOLUME</th>
				<th class="text-center">HARSAT</th>
				<th class="text-center">NILAI</th>
				<th class="text-center">VOLUME</th>
				<th class="text-center">NILAI</th>
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
				<th class="text-right"><?php echo number_format($price_a,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($nilai_a,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_pemakaian_semen,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pemakaian_semen,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pemakaian_semen,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorA ?>"><?php echo number_format($evaluasi_volume_a,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorAA ?>"><?php echo number_format($evaluasi_nilai_a,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center" style="vertical-align:middle">2</th>			
				<th class="text-left">Pasir</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($price_b,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($nilai_b,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_pemakaian_pasir,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pemakaian_pasir,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pemakaian_pasir,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorB ?>"><?php echo number_format($evaluasi_volume_b,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorBB ?>"><?php echo number_format($evaluasi_nilai_b,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center" style="vertical-align:middle">3</th>			
				<th class="text-left">Batu Split 10-20</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($price_c,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($nilai_c,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_pemakaian_batu1020,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pemakaian_batu1020,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pemakaian_batu1020,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorC ?>"><?php echo number_format($evaluasi_volume_c,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorCC ?>"><?php echo number_format($evaluasi_nilai_c,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center" style="vertical-align:middle">4</th>			
				<th class="text-left">Batu Split 20-30</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($price_d,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($nilai_d,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_pemakaian_batu2030,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harga_pemakaian_batu2030,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_pemakaian_batu2030,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorD ?>"><?php echo number_format($evaluasi_volume_d,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorDD ?>"><?php echo number_format($evaluasi_nilai_d,0,',','.');?></th>
	        </tr>
			<tr class="table-active5">		
				<th class="text-right" colspan="3">TOTAL</th>
				<th class="text-right"></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($total_nilai_komposisi,0,',','.');?></th>
				<th class="text-right"></th>
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
			->where("prm.material_id in (12,13,14,23,24,25,26)")
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
			$total_harga_pemakaian_solar = round($total_harga_pembelian_solar_akhir,0);
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
			//VOLUME
			$date_now = date('Y-m-d');
			$rencana_kerja = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
			->from('rak r')
			->where("(r.tanggal_rencana_kerja between '$date1' and '$date2')")
			->get()->row_array();

			$volume_rap_produk_a = $rencana_kerja['vol_produk_a'];
			$volume_rap_produk_b = $rencana_kerja['vol_produk_b'];
			$volume_rap_produk_c = $rencana_kerja['vol_produk_c'];
			$volume_rap_produk_d = $rencana_kerja['vol_produk_d'];

			$total_rap_volume = $volume_rap_produk_a + $volume_rap_produk_b + $volume_rap_produk_c + $volume_rap_produk_d;
			
			$harga_jual_125_rap = $rencana_kerja['price_a'];
			$harga_jual_225_rap = $rencana_kerja['price_b'];
			$harga_jual_250_rap = $rencana_kerja['price_c'];
			$harga_jual_250_18_rap = $rencana_kerja['price_d'];

			$nilai_jual_125 = $volume_rap_produk_a * $harga_jual_125_rap;
			$nilai_jual_225 = $volume_rap_produk_b * $harga_jual_225_rap;
			$nilai_jual_250 = $volume_rap_produk_c * $harga_jual_250_rap;
			$nilai_jual_250_18 = $volume_rap_produk_d * $harga_jual_250_18_rap;
			$nilai_jual_all = $nilai_jual_125 + $nilai_jual_225 + $nilai_jual_250 + $nilai_jual_250_18;
			
			$total_rap_nilai = $nilai_jual_all;

			//BIAYA
			$rencana_kerja_biaya = $this->db->select('SUM(r.biaya_bahan) as biaya_bahan, SUM(r.biaya_alat) as biaya_alat, SUM(r.biaya_overhead) as biaya_overhead, SUM(r.biaya_bank) as biaya_bank')
			->from('rak_biaya r')
			->where("(r.tanggal_rencana_kerja between '$date1' and '$date2')")
			->get()->row_array();
		
			$total_rap_biaya_bahan = $rencana_kerja_biaya['biaya_bahan'];
			$total_rap_biaya_alat = $rencana_kerja_biaya['biaya_alat'];
			$total_rap_biaya_overhead = $rencana_kerja_biaya['biaya_overhead'];
			$total_rap_biaya_bank = $rencana_kerja_biaya['biaya_bank'];

			$total_biaya_rap_biaya = $total_rap_biaya_bahan + $total_rap_biaya_alat + $total_rap_biaya_overhead + $total_rap_biaya_bank;

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
			$sisa_volume_produk_a = $volume_realisasi_produk_a - $volume_rap_produk_a;
			$sisa_volume_produk_b = $volume_realisasi_produk_b - $volume_rap_produk_b;
			$sisa_volume_produk_c = $volume_realisasi_produk_c - $volume_rap_produk_c;
			$sisa_volume_produk_d = $volume_realisasi_produk_d  - $volume_rap_produk_d;

			$total_sisa_volume_all_produk = $sisa_volume_produk_a + $sisa_volume_produk_b + $sisa_volume_produk_c + $sisa_volume_produk_d;
			$total_sisa_nilai_all_produk = $total_realisasi_nilai - $total_rap_nilai;

			$sisa_biaya_bahan = $total_bahan_akumulasi - $total_rap_biaya_bahan;
			$sisa_biaya_alat = $total_alat_realisasi - $total_rap_biaya_alat;
			$sisa_biaya_overhead = $total_overhead_realisasi - $total_rap_biaya_overhead;
			$sisa_biaya_bank = $total_diskonto_realisasi - $total_rap_biaya_bank;
			?>
			<!-- SISA -->

			<!-- TOTAL -->
			<?php
			$total_laba_rap = $total_rap_nilai - $total_biaya_rap_biaya;
			$total_laba_realisasi = $total_realisasi_nilai - $total_biaya_realisasi;

			$sisa_biaya_realisasi = $total_biaya_realisasi - $total_biaya_rap_biaya;
			$sisa_laba = $total_laba_realisasi - $total_laba_rap;

			?>
			<!-- TOTAL -->

			<tr class="table-active4">
				<th width="5%" class="text-center">NO.</th>
				<th class="text-center">URAIAN</th>
				<th class="text-center">SATUAN</th>
				<th class="text-center">RENCANA</th>
				<th class="text-center">REALISASI</th>
				<th class="text-center">EVALUASI</th>
	        </tr>
			<tr class="table-active2">
				<th class="text-left" colspan="6">RENCANA PRODUKSI & PENDAPATAN USAHA</th>
			</tr>
			<?php
				$styleColorA = $sisa_volume_produk_a < 0 ? 'color:red' : 'color:black';
				$styleColorB = $sisa_volume_produk_b < 0 ? 'color:red' : 'color:black';
				$styleColorC = $sisa_volume_produk_c < 0 ? 'color:red' : 'color:black';
				$styleColorD = $sisa_volume_produk_d < 0 ? 'color:red' : 'color:black';
				$styleColorE = $total_sisa_volume_all_produk < 0 ? 'color:red' : 'color:black';
				$styleColorF = $total_sisa_nilai_all_produk < 0 ? 'color:red' : 'color:black';
				$styleColorG = $sisa_biaya_bahan < 0 ? 'color:red' : 'color:black';
				$styleColorH = $sisa_biaya_alat < 0 ? 'color:red' : 'color:black';
				$styleColorI = $sisa_biaya_overhead < 0 ? 'color:red' : 'color:black';
				$styleColorJ = $sisa_biaya_bank < 0 ? 'color:red' : 'color:black';
				$styleColorL = $sisa_biaya_realisasi < 0 ? 'color:red' : 'color:black';
				$styleColorM = $sisa_laba < 0 ? 'color:red' : 'color:black';
			?>
			<tr class="table-active3">
				<th class="text-center">1</th>
				<th class="text-left">Beton K 125 (10±2)</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_rap_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_realisasi_produk_a,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorA ?>"><?php echo number_format($sisa_volume_produk_a,2,',','.');?></th>
			</tr>
			<tr class="table-active3">
				<th class="text-center">2</th>
				<th class="text-left">Beton K 225 (10±2)</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_rap_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_realisasi_produk_b,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorB ?>"><?php echo number_format($sisa_volume_produk_b,2,',','.');?></th>
			</tr>
			<tr class="table-active3">
				<th class="text-center">3</th>
				<th class="text-left">Beton K 250 (10±2)</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_rap_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_realisasi_produk_c,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorC ?>"><?php echo number_format($sisa_volume_produk_c,2,',','.');?></th>
			</tr>
			<tr class="table-active3">
				<th class="text-center">4</th>
				<th class="text-left">Beton K 250 (18±2)</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_rap_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_realisasi_produk_d,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorD ?>"><?php echo number_format($sisa_volume_produk_d,2,',','.');?></th>
			</tr>
			<tr class="table-active2">
				<th class="text-right" colspan="2">TOTAL VOLUME</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($total_rap_volume,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_realisasi_volume,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorE ?>"><?php echo number_format($total_sisa_volume_all_produk,2,',','.');?></th>
			</tr>
			<tr class="table-active2">
				<th class="text-right" colspan="2">PENDAPATAN USAHA</th>
				<th class="text-center"></th>
				<th class="text-right"><?php echo number_format($total_rap_nilai,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_realisasi_nilai,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorF ?>"><?php echo number_format($total_sisa_nilai_all_produk,0,',','.');?></th>
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
				<th class="text-right" style="<?php echo $styleColorG ?>"><?php echo number_format($sisa_biaya_bahan,0,',','.');?></th>
			</tr>
			<tr class="table-active3">
				<th class="text-center">2</th>
				<th class="text-left">Alat</th>
				<th class="text-center">LS</th>
				<th class="text-right"><?php echo number_format($total_rap_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_alat_realisasi,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorH ?>"><?php echo number_format($sisa_biaya_alat,0,',','.');?></th>
			</tr>
			<tr class="table-active3">
				<th class="text-center">3</th>
				<th class="text-left">Overhead</th>
				<th class="text-center">LS</th>
				<th class="text-right"><?php echo number_format($total_rap_biaya_overhead,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_overhead_realisasi,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorI ?>"><?php echo number_format($sisa_biaya_overhead,0,',','.');?></th>
			</tr>
			<tr class="table-active3">
				<th class="text-center">4</th>
				<th class="text-left">Biaya Bank</th>
				<th class="text-center">LS</th>
				<th class="text-right"><?php echo number_format($total_rap_biaya_bank,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_diskonto_realisasi,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorJ ?>"><?php echo number_format($sisa_biaya_bank,0,',','.');?></th>
			</tr>
			<tr class="table-active2">
				<th class="text-right" colspan="2">JUMLAH</th>
				<th class="text-center"></th>
				<th class="text-right"><?php echo number_format($total_biaya_rap_biaya,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_biaya_realisasi,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorL ?>"><?php echo number_format($sisa_biaya_realisasi,0,',','.');?></th>
			</tr>
			<tr class="table-active2">
				<th class="text-right" colspan="2">LABA</th>
				<th class="text-center"></th>
				<th class="text-right"><?php echo number_format($total_laba_rap,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_laba_realisasi,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorM ?>"><?php echo number_format($sisa_laba,0,',','.');?></th>
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
			//$total_harga_pemakaian_semen = ($total_volume_pemakaian_semen!=0)?$total_nilai_pemakaian_semen / $total_volume_pemakaian_semen * 1:0;
			$total_harga_pemakaian_semen = $total_harga_stock_semen_akhir;

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
			
			$hpp_bahan_baku = $this->db->select('pp.date_hpp, pp.pasir')
			->from('hpp_bahan_baku pp')
			->where("(pp.date_hpp between '$date1' and '$date2')")
			->order_by('pp.date_hpp','desc')->limit(1)
			->get()->row_array();

			$total_volume_stock_pasir_akhir = $stock_opname_pasir['volume'];
			$price_stock_opname_pasir =  $hpp_bahan_baku['pasir'];

			$total_volume_pemakaian_pasir = $total_volume_pembelian_pasir_akhir - $stock_opname_pasir['volume'];

			$total_harga_stock_pasir_akhir = round($price_stock_opname_pasir,0);
			$total_nilai_stock_pasir_akhir = $total_volume_stock_pasir_akhir * $total_harga_stock_pasir_akhir;

			$total_nilai_pemakaian_pasir = ($nilai_opening_balance_pasir + $total_nilai_pembelian_pasir) - $total_nilai_stock_pasir_akhir;
			//$total_harga_pemakaian_pasir = ($total_volume_pemakaian_pasir!=0)?$total_nilai_pemakaian_pasir / $total_volume_pemakaian_pasir * 1:0;
			$total_harga_pemakaian_pasir = $total_harga_stock_pasir_akhir;

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

			$hpp_bahan_baku = $this->db->select('pp.date_hpp, pp.batu1020')
			->from('hpp_bahan_baku pp')
			->where("(pp.date_hpp between '$date1' and '$date2')")
			->order_by('pp.date_hpp','desc')->limit(1)
			->get()->row_array();

			$total_volume_stock_batu1020_akhir = $stock_opname_batu1020['volume'];
			$price_stock_opname_batu1020 =  $hpp_bahan_baku['batu1020'];

			$total_volume_pemakaian_batu1020 = $total_volume_pembelian_batu1020_akhir - $stock_opname_batu1020['volume'];

			$total_harga_stock_batu1020_akhir = round($price_stock_opname_batu1020,0);
			$total_nilai_stock_batu1020_akhir = $total_volume_stock_batu1020_akhir * $total_harga_stock_batu1020_akhir;

			$total_nilai_pemakaian_batu1020 = ($nilai_opening_balance_batu1020 + $total_nilai_pembelian_batu1020) - $total_nilai_stock_batu1020_akhir;
			//$total_harga_pemakaian_batu1020 = ($total_volume_pemakaian_batu1020!=0)?$total_nilai_pemakaian_batu1020 / $total_volume_pemakaian_batu1020 * 1:0;
			$total_harga_pemakaian_batu1020 = $total_harga_stock_batu1020_akhir;

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
			
			$hpp_bahan_baku = $this->db->select('pp.date_hpp, pp.batu2030')
			->from('hpp_bahan_baku pp')
			->where("(pp.date_hpp between '$date1' and '$date2')")
			->order_by('pp.date_hpp','desc')->limit(1)
			->get()->row_array();

			$total_volume_stock_batu2030_akhir = $stock_opname_batu2030['volume'];
			$price_stock_opname_batu2030 =  $hpp_bahan_baku['batu2030'];

			$total_volume_pemakaian_batu2030 = $total_volume_pembelian_batu2030_akhir - $stock_opname_batu2030['volume'];

			$total_harga_stock_batu2030_akhir = round($price_stock_opname_batu2030,0);
			$total_nilai_stock_batu2030_akhir = $total_volume_stock_batu2030_akhir * $total_harga_stock_batu2030_akhir;

			$total_nilai_pemakaian_batu2030 = ($nilai_opening_balance_batu2030 + $total_nilai_pembelian_batu2030) - $total_nilai_stock_batu2030_akhir;
			//$total_harga_pemakaian_batu2030 = ($total_volume_pemakaian_batu2030!=0)?$total_nilai_pemakaian_batu2030 / $total_volume_pemakaian_batu2030 * 1:0;
			$total_harga_pemakaian_batu2030 = $total_harga_stock_batu2030_akhir;

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
			//$total_harga_pemakaian_semen = ($total_volume_pemakaian_semen!=0)?$total_nilai_pemakaian_semen / $total_volume_pemakaian_semen * 1:0;
			$total_harga_pemakaian_semen = $total_harga_stock_semen_akhir;

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
			
			$hpp_bahan_baku = $this->db->select('pp.date_hpp, pp.pasir')
			->from('hpp_bahan_baku pp')
			->where("(pp.date_hpp between '$date1' and '$date2')")
			->order_by('pp.date_hpp','desc')->limit(1)
			->get()->row_array();

			$total_volume_stock_pasir_akhir = $stock_opname_pasir['volume'];
			$price_stock_opname_pasir =  $hpp_bahan_baku['pasir'];

			$total_volume_pemakaian_pasir = $total_volume_pembelian_pasir_akhir - $stock_opname_pasir['volume'];

			$total_harga_stock_pasir_akhir = round($price_stock_opname_pasir,0);
			$total_nilai_stock_pasir_akhir = $total_volume_stock_pasir_akhir * $total_harga_stock_pasir_akhir;

			$total_nilai_pemakaian_pasir = ($nilai_opening_balance_pasir + $total_nilai_pembelian_pasir) - $total_nilai_stock_pasir_akhir;
			//$total_harga_pemakaian_pasir = ($total_volume_pemakaian_pasir!=0)?$total_nilai_pemakaian_pasir / $total_volume_pemakaian_pasir * 1:0;
			$total_harga_pemakaian_pasir = $total_harga_stock_pasir_akhir;

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

			$hpp_bahan_baku = $this->db->select('pp.date_hpp, pp.batu1020')
			->from('hpp_bahan_baku pp')
			->where("(pp.date_hpp between '$date1' and '$date2')")
			->order_by('pp.date_hpp','desc')->limit(1)
			->get()->row_array();

			$total_volume_stock_batu1020_akhir = $stock_opname_batu1020['volume'];
			$price_stock_opname_batu1020 =  $hpp_bahan_baku['batu1020'];

			$total_volume_pemakaian_batu1020 = $total_volume_pembelian_batu1020_akhir - $stock_opname_batu1020['volume'];

			$total_harga_stock_batu1020_akhir = round($price_stock_opname_batu1020,0);
			$total_nilai_stock_batu1020_akhir = $total_volume_stock_batu1020_akhir * $total_harga_stock_batu1020_akhir;

			$total_nilai_pemakaian_batu1020 = ($nilai_opening_balance_batu1020 + $total_nilai_pembelian_batu1020) - $total_nilai_stock_batu1020_akhir;
			//$total_harga_pemakaian_batu1020 = ($total_volume_pemakaian_batu1020!=0)?$total_nilai_pemakaian_batu1020 / $total_volume_pemakaian_batu1020 * 1:0;
			$total_harga_pemakaian_batu1020 = $total_harga_stock_batu1020_akhir;

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
			
			$hpp_bahan_baku = $this->db->select('pp.date_hpp, pp.batu2030')
			->from('hpp_bahan_baku pp')
			->where("(pp.date_hpp between '$date1' and '$date2')")
			->order_by('pp.date_hpp','desc')->limit(1)
			->get()->row_array();

			$total_volume_stock_batu2030_akhir = $stock_opname_batu2030['volume'];
			$price_stock_opname_batu2030 =  $hpp_bahan_baku['batu2030'];

			$total_volume_pemakaian_batu2030 = $total_volume_pembelian_batu2030_akhir - $stock_opname_batu2030['volume'];

			$total_harga_stock_batu2030_akhir = round($price_stock_opname_batu2030,0);
			$total_nilai_stock_batu2030_akhir = $total_volume_stock_batu2030_akhir * $total_harga_stock_batu2030_akhir;

			$total_nilai_pemakaian_batu2030 = ($nilai_opening_balance_batu2030 + $total_nilai_pembelian_batu2030) - $total_nilai_stock_batu2030_akhir;
			//$total_harga_pemakaian_batu2030 = ($total_volume_pemakaian_batu2030!=0)?$total_nilai_pemakaian_batu2030 / $total_volume_pemakaian_batu2030 * 1:0;
			$total_harga_pemakaian_batu2030 = $total_harga_stock_batu2030_akhir;

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
				<th class="text-right" style='background-color:red; color:white'><blink><?php echo number_format($total_harga_pembelian_semen_opc_akhir,0,',','.');?></blink></th>
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
				<th class="text-right"><?php echo number_format($total_harga_stock_semen_akhir,0,',','.');?></th>
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
				<th class="text-right" style='background-color:red; color:white'><blink><?php echo number_format($total_harga_pembelian_pasir_akhir,0,',','.');?></blink></th>
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
				<th class="text-right"><?php echo number_format($total_harga_stock_pasir_akhir,0,',','.');?></th>
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
				<th class="text-right" style='background-color:red; color:white'><blink><?php echo number_format($total_harga_pembelian_batu1020_akhir,0,',','.');?></blink></th>
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
				<th class="text-right"><?php echo number_format($total_harga_stock_batu1020_akhir,0,',','.');?></th>
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
				<th class="text-right" style='background-color:red; color:white'><blink><?php echo number_format($total_harga_pembelian_batu2030_akhir,0,',','.');?></blink></th>
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
				<th class="text-right"><?php echo number_format($total_harga_stock_batu2030_akhir,0,',','.');?></th>
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
			$total_harga_pemakaian_solar = round($total_harga_pembelian_solar_akhir,0);
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
				<th class="text-right" style='background-color:red; color:white'><blink><?php echo number_format($total_harga_pembelian_solar_akhir,0,',','.');?></blink></th>
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
				<th class="text-right"><?php echo number_format($total_harga_stock_solar_akhir,0,',','.');?></th>
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

	public function prognosa_produksi($arr_date)
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

			<!-- RAP TERBARU -->
			<?php
			//VOLUME RAP
			$date_now = date('Y-m-d');
			$date_end = date('2022-12-31');

			$rencana_kerja_2022_1 = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '2021-12-30' and '2021-12-30'")
			->get()->row_array();

			$rencana_kerja_2022_2 = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '2021-12-31' and '2021-12-31'")
			->get()->row_array();

			$volume_rap_2022_produk_a = $rencana_kerja_2022_1['vol_produk_a'] + $rencana_kerja_2022_2['vol_produk_a'];
			$volume_rap_2022_produk_b = $rencana_kerja_2022_1['vol_produk_b'] + $rencana_kerja_2022_2['vol_produk_b'];
			$volume_rap_2022_produk_c = $rencana_kerja_2022_1['vol_produk_c'] + $rencana_kerja_2022_2['vol_produk_c'];
			$volume_rap_2022_produk_d = $rencana_kerja_2022_1['vol_produk_d'] + $rencana_kerja_2022_2['vol_produk_d'];
			$total_rap_volume_2022 = $rencana_kerja_2022_1['vol_produk_a'] + $rencana_kerja_2022_1['vol_produk_b'] + $rencana_kerja_2022_1['vol_produk_c'] + $rencana_kerja_2022_1['vol_produk_d'] + $rencana_kerja_2022_2['vol_produk_a'] + $rencana_kerja_2022_2['vol_produk_b'] + $rencana_kerja_2022_2['vol_produk_c'] + $rencana_kerja_2022_2['vol_produk_d'];

			$price_produk_a_1 = $rencana_kerja_2022_1['vol_produk_a'] * $rencana_kerja_2022_1['price_a'];
			$price_produk_b_1 = $rencana_kerja_2022_1['vol_produk_b'] * $rencana_kerja_2022_1['price_b'];
			$price_produk_c_1 = $rencana_kerja_2022_1['vol_produk_c'] * $rencana_kerja_2022_1['price_c'];
			$price_produk_d_1 = $rencana_kerja_2022_1['vol_produk_d'] * $rencana_kerja_2022_1['price_d'];

			$price_produk_a_2 = $rencana_kerja_2022_2['vol_produk_a'] * $rencana_kerja_2022_2['price_a'];
			$price_produk_b_2 = $rencana_kerja_2022_2['vol_produk_b'] * $rencana_kerja_2022_2['price_b'];
			$price_produk_c_2 = $rencana_kerja_2022_2['vol_produk_c'] * $rencana_kerja_2022_2['price_c'];
			$price_produk_d_2 = $rencana_kerja_2022_2['vol_produk_d'] * $rencana_kerja_2022_2['price_d'];

			$nilai_jual_all_2022 = $price_produk_a_1 + $price_produk_b_1 + $price_produk_c_1 + $price_produk_d_1 + $price_produk_a_2 + $price_produk_b_2 + $price_produk_c_2 + $price_produk_d_2;
			$total_rap_nilai_2022 = $nilai_jual_all_2022;

			//BIAYA RAP 2022
			$rencana_kerja_biaya_2022_1 = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '2021-12-30' and '2021-12-30'")
			->get()->row_array();

			$rencana_kerja_biaya_2022_2 = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '2021-12-31' and '2021-12-31'")
			->get()->row_array();


			$total_rap_2022_biaya_bahan = $rencana_kerja_biaya_2022_1['biaya_bahan'] + $rencana_kerja_biaya_2022_2['biaya_bahan'];
			$total_rap_2022_biaya_alat = $rencana_kerja_biaya_2022_1['biaya_alat'] + $rencana_kerja_biaya_2022_2['biaya_alat'];
			$total_rap_2022_biaya_overhead = $rencana_kerja_biaya_2022_1['biaya_overhead'] + $rencana_kerja_biaya_2022_2['biaya_overhead'];
			$total_rap_2022_biaya_bank = $rencana_kerja_biaya_2022_1['biaya_bank'] + $rencana_kerja_biaya_2022_2['biaya_bank'];
			$total_biaya_rap_2022_biaya = $total_rap_2022_biaya_bahan + $total_rap_2022_biaya_alat + $total_rap_2022_biaya_overhead + $total_rap_2022_biaya_bank;

			?>
			<!-- RAP TERBARU -->
			
			<!-- AKUMULASI BULAN TERAKHIR -->
			<?php
			$stock_opname = $this->db->select('date')->order_by('date','desc')->limit(1,5)->get_where('pmm_remaining_materials_cat',array('status'=>'PUBLISH'))->row_array();
			$last_opname =  date('Y-m-d', strtotime($stock_opname['date']));

			$penjualan_akumulasi_produk_a = $this->db->select('p.nama_produk, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('produk p', 'pp.product_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("(pp.date_production <= '$last_opname')")
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
			->where("(pp.date_production <= '$last_opname')")
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
			->where("(pp.date_production <= '$last_opname')")
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
			->where("(pp.date_production <= '$last_opname')")
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
		
			//AKUMULASI BIAYA
			//BAHAN
			$akumulasi = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar as total_nilai_keluar')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi <= '$last_opname')")
			->get()->result_array();

			$total_akumulasi = 0;

			foreach ($akumulasi as $a){
				$total_akumulasi += $a['total_nilai_keluar'];
			}

			$total_bahan_akumulasi = $total_akumulasi;
		
			//ALAT
			$nilai_alat = $this->db->select('SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("(prm.date_receipt <= '$last_opname')")
			->where("p.kategori_produk = '5'")
			->where("po.status in ('PUBLISH','CLOSED')")
			->get()->row_array();

			$akumulasi_bbm = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar_2 as total_nilai_keluar_2')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi <= '$last_opname')")
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
			->where("(tanggal_transaksi <= '$last_opname')")
			->get()->row_array();

			$total_insentif_tm = $insentif_tm['total'];

			$total_alat_akumulasi = $nilai_alat['nilai'] + $total_akumulasi_bbm + $total_insentif_tm;

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
			->where("(pb.tanggal_transaksi <= '$last_opname')")
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
			->where("(pb.tanggal_transaksi <= '$last_opname')")
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
			->where("(pb.tanggal_transaksi <= '$last_opname')")
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
			->where("(pb.tanggal_transaksi <= '$last_opname')")
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
			->where("(pb.tanggal_transaksi <= '$last_opname')")
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
			->where("(pb.tanggal_transaksi <= '$last_opname')")
			->get()->row_array();

			$total_overhead_akumulasi =  $overhead_15_akumulasi['total'] + $overhead_jurnal_15_akumulasi['total'] + $overhead_16_akumulasi['total'] + $overhead_jurnal_16_akumulasi['total'] + $overhead_17_akumulasi['total'] + $overhead_jurnal_17_akumulasi['total'];
			
			//DISKONTO
			$diskonto_akumulasi = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 168")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi <= '$last_opname')")
			->get()->row_array();

			$total_diskonto_akumulasi = $diskonto_akumulasi['total'];
			$total_biaya_akumulasi = $total_bahan_akumulasi + $total_alat_akumulasi + $total_overhead_akumulasi + $total_diskonto_akumulasi;
			?>
			<!-- AKUMULASI BULAN TERAKHIR -->

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

			$rencana_kerja_biaya_november = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_november_awal' and '$date_november_akhir'")
			->get()->row_array();

			$nilai_jual_125_november = $volume_november_produk_a * $rencana_kerja_november['price_a'];
			$nilai_jual_225_november = $volume_november_produk_b * $rencana_kerja_november['price_b'];
			$nilai_jual_250_november = $volume_november_produk_c * $rencana_kerja_november['price_c'];
			$nilai_jual_250_18_november = $volume_november_produk_d * $rencana_kerja_november['price_d'];
			$nilai_jual_all_november = $nilai_jual_125_november + $nilai_jual_225_november + $nilai_jual_250_november + $nilai_jual_250_18_november;

			$total_november_nilai = $nilai_jual_all_november;

			//VOLUME
			$rencana_kerja_november = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_november_awal' and '$date_november_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_november_produk_a = $rencana_kerja_november['vol_produk_a'];
			$volume_rencana_kerja_november_produk_b = $rencana_kerja_november['vol_produk_b'];
			$volume_rencana_kerja_november_produk_c = $rencana_kerja_november['vol_produk_c'];
			$volume_rencana_kerja_november_produk_d = $rencana_kerja_november['vol_produk_d'];

			//BIAYA
			$rencana_kerja_biaya_november = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_november_awal' and '$date_november_akhir'")
			->get()->row_array();
		
			$total_november_biaya_bahan = $rencana_kerja_biaya_november['biaya_bahan'];
			$total_november_biaya_alat = $rencana_kerja_biaya_november['biaya_alat'];
			$total_november_biaya_overhead = $rencana_kerja_biaya_november['biaya_overhead'];
			$total_november_biaya_bank = $rencana_kerja_biaya_november['biaya_bank'];
			$total_biaya_november_biaya = $total_november_biaya_bahan + $total_november_biaya_alat + $total_november_biaya_overhead + $total_november_biaya_bank;
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

			$rencana_kerja_biaya_desember = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_desember_awal' and '$date_desember_akhir'")
			->get()->row_array();

			$nilai_jual_125_desember = $volume_desember_produk_a * $rencana_kerja_desember['price_a'];
			$nilai_jual_225_desember = $volume_desember_produk_b * $rencana_kerja_desember['price_b'];
			$nilai_jual_250_desember = $volume_desember_produk_c * $rencana_kerja_desember['price_c'];
			$nilai_jual_250_18_desember = $volume_desember_produk_d * $rencana_kerja_desember['price_d'];
			$nilai_jual_all_desember = $nilai_jual_125_desember + $nilai_jual_225_desember + $nilai_jual_250_desember + $nilai_jual_250_18_desember;

			$total_desember_nilai = $nilai_jual_all_desember;

			//VOLUME
			$rencana_kerja_desember = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_desember_awal' and '$date_desember_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_desember_produk_a = $rencana_kerja_desember['vol_produk_a'];
			$volume_rencana_kerja_desember_produk_b = $rencana_kerja_desember['vol_produk_b'];
			$volume_rencana_kerja_desember_produk_c = $rencana_kerja_desember['vol_produk_c'];
			$volume_rencana_kerja_desember_produk_d = $rencana_kerja_desember['vol_produk_d'];

			//BIAYA
			$rencana_kerja_biaya_desember = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_desember_awal' and '$date_desember_akhir'")
			->get()->row_array();
		
			$total_desember_biaya_bahan = $rencana_kerja_biaya_desember['biaya_bahan'];
			$total_desember_biaya_alat = $rencana_kerja_biaya_desember['biaya_alat'];
			$total_desember_biaya_overhead = $rencana_kerja_biaya_desember['biaya_overhead'];
			$total_desember_biaya_bank = $rencana_kerja_biaya_desember['biaya_bank'];
			$total_biaya_desember_biaya = $total_desember_biaya_bahan + $total_desember_biaya_alat + $total_desember_biaya_overhead + $total_desember_biaya_bank;
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

			$rencana_kerja_biaya_januari = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_januari_akhir'")
			->get()->row_array();

			$nilai_jual_125_januari = $volume_januari_produk_a * $rencana_kerja_januari['price_a'];
			$nilai_jual_225_januari = $volume_januari_produk_b * $rencana_kerja_januari['price_b'];
			$nilai_jual_250_januari = $volume_januari_produk_c * $rencana_kerja_januari['price_c'];
			$nilai_jual_250_18_januari = $volume_januari_produk_d * $rencana_kerja_januari['price_d'];
			$nilai_jual_all_januari = $nilai_jual_125_januari + $nilai_jual_225_januari + $nilai_jual_250_januari + $nilai_jual_250_18_januari;

			$total_januari_nilai = $nilai_jual_all_januari;

			//VOLUME
			$rencana_kerja_januari = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_januari_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_januari_produk_a = $rencana_kerja_januari['vol_produk_a'];
			$volume_rencana_kerja_januari_produk_b = $rencana_kerja_januari['vol_produk_b'];
			$volume_rencana_kerja_januari_produk_c = $rencana_kerja_januari['vol_produk_c'];
			$volume_rencana_kerja_januari_produk_d = $rencana_kerja_januari['vol_produk_d'];

			//BIAYA
			$rencana_kerja_biaya_januari = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_januari_awal' and '$date_januari_akhir'")
			->get()->row_array();
		
			$total_januari_biaya_bahan = $rencana_kerja_biaya_januari['biaya_bahan'];
			$total_januari_biaya_alat = $rencana_kerja_biaya_januari['biaya_alat'];
			$total_januari_biaya_overhead = $rencana_kerja_biaya_januari['biaya_overhead'];
			$total_januari_biaya_bank = $rencana_kerja_biaya_januari['biaya_bank'];
			$total_biaya_januari_biaya = $total_januari_biaya_bahan + $total_januari_biaya_alat + $total_januari_biaya_overhead + $total_januari_biaya_bank;
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

			$rencana_kerja_biaya_februari = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_februari_awal' and '$date_februari_akhir'")
			->get()->row_array();

			$nilai_jual_125_februari = $volume_februari_produk_a * $rencana_kerja_februari['price_a'];
			$nilai_jual_225_februari = $volume_februari_produk_b * $rencana_kerja_februari['price_b'];
			$nilai_jual_250_februari = $volume_februari_produk_c * $rencana_kerja_februari['price_c'];
			$nilai_jual_250_18_februari = $volume_februari_produk_d * $rencana_kerja_februari['price_d'];
			$nilai_jual_all_februari = $nilai_jual_125_februari + $nilai_jual_225_februari + $nilai_jual_250_februari + $nilai_jual_250_18_februari;

			$total_februari_nilai = $nilai_jual_all_februari;

			//VOLUME
			$rencana_kerja_februari = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_februari_awal' and '$date_februari_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_februari_produk_a = $rencana_kerja_februari['vol_produk_a'];
			$volume_rencana_kerja_februari_produk_b = $rencana_kerja_februari['vol_produk_b'];
			$volume_rencana_kerja_februari_produk_c = $rencana_kerja_februari['vol_produk_c'];
			$volume_rencana_kerja_februari_produk_d = $rencana_kerja_februari['vol_produk_d'];

			//BIAYA
			$rencana_kerja_biaya_februari = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_februari_awal' and '$date_februari_akhir'")
			->get()->row_array();
		
			$total_februari_biaya_bahan = $rencana_kerja_biaya_februari['biaya_bahan'];
			$total_februari_biaya_alat = $rencana_kerja_biaya_februari['biaya_alat'];
			$total_februari_biaya_overhead = $rencana_kerja_biaya_februari['biaya_overhead'];
			$total_februari_biaya_bank = $rencana_kerja_biaya_februari['biaya_bank'];
			$total_biaya_februari_biaya = $total_februari_biaya_bahan + $total_februari_biaya_alat + $total_februari_biaya_overhead + $total_februari_biaya_bank;
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

			$rencana_kerja_biaya_maret = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_maret_awal' and '$date_maret_akhir'")
			->get()->row_array();

			$nilai_jual_125_maret = $volume_maret_produk_a * $rencana_kerja_maret['price_a'];
			$nilai_jual_225_maret = $volume_maret_produk_b * $rencana_kerja_maret['price_b'];
			$nilai_jual_250_maret = $volume_maret_produk_c * $rencana_kerja_maret['price_c'];
			$nilai_jual_250_18_maret = $volume_maret_produk_d * $rencana_kerja_maret['price_d'];
			$nilai_jual_all_maret = $nilai_jual_125_maret + $nilai_jual_225_maret + $nilai_jual_250_maret + $nilai_jual_250_18_maret;

			$total_maret_nilai = $nilai_jual_all_maret;

			//VOLUME
			$rencana_kerja_maret = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_maret_awal' and '$date_maret_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_maret_produk_a = $rencana_kerja_maret['vol_produk_a'];
			$volume_rencana_kerja_maret_produk_b = $rencana_kerja_maret['vol_produk_b'];
			$volume_rencana_kerja_maret_produk_c = $rencana_kerja_maret['vol_produk_c'];
			$volume_rencana_kerja_maret_produk_d = $rencana_kerja_maret['vol_produk_d'];

			//BIAYA
			$rencana_kerja_biaya_maret = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_maret_awal' and '$date_maret_akhir'")
			->get()->row_array();
		
			$total_maret_biaya_bahan = $rencana_kerja_biaya_maret['biaya_bahan'];
			$total_maret_biaya_alat = $rencana_kerja_biaya_maret['biaya_alat'];
			$total_maret_biaya_overhead = $rencana_kerja_biaya_maret['biaya_overhead'];
			$total_maret_biaya_bank = $rencana_kerja_biaya_maret['biaya_bank'];
			$total_biaya_maret_biaya = $total_maret_biaya_bahan + $total_maret_biaya_alat + $total_maret_biaya_overhead + $total_maret_biaya_bank;
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

			$rencana_kerja_biaya_april = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_april_awal' and '$date_april_akhir'")
			->get()->row_array();

			$nilai_jual_125_april = $volume_april_produk_a * $rencana_kerja_april['price_a'];
			$nilai_jual_225_april = $volume_april_produk_b * $rencana_kerja_april['price_b'];
			$nilai_jual_250_april = $volume_april_produk_c * $rencana_kerja_april['price_c'];
			$nilai_jual_250_18_april = $volume_april_produk_d * $rencana_kerja_april['price_d'];
			$nilai_jual_all_april = $nilai_jual_125_april + $nilai_jual_225_april + $nilai_jual_250_april + $nilai_jual_250_18_april;

			$total_april_nilai = $nilai_jual_all_april;

			//VOLUME
			$rencana_kerja_april = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_april_awal' and '$date_april_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_april_produk_a = $rencana_kerja_april['vol_produk_a'];
			$volume_rencana_kerja_april_produk_b = $rencana_kerja_april['vol_produk_b'];
			$volume_rencana_kerja_april_produk_c = $rencana_kerja_april['vol_produk_c'];
			$volume_rencana_kerja_april_produk_d = $rencana_kerja_april['vol_produk_d'];

			//BIAYA
			$rencana_kerja_biaya_april = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_april_awal' and '$date_april_akhir'")
			->get()->row_array();
		
			$total_april_biaya_bahan = $rencana_kerja_biaya_april['biaya_bahan'];
			$total_april_biaya_alat = $rencana_kerja_biaya_april['biaya_alat'];
			$total_april_biaya_overhead = $rencana_kerja_biaya_april['biaya_overhead'];
			$total_april_biaya_bank = $rencana_kerja_biaya_april['biaya_bank'];
			$total_biaya_april_biaya = $total_april_biaya_bahan + $total_april_biaya_alat + $total_april_biaya_overhead + $total_april_biaya_bank;
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

			$rencana_kerja_biaya_mei = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_mei_awal' and '$date_mei_akhir'")
			->get()->row_array();

			$nilai_jual_125_mei = $volume_mei_produk_a * $rencana_kerja_mei['price_a'];
			$nilai_jual_225_mei = $volume_mei_produk_b * $rencana_kerja_mei['price_b'];
			$nilai_jual_250_mei = $volume_mei_produk_c * $rencana_kerja_mei['price_c'];
			$nilai_jual_250_18_mei = $volume_mei_produk_d * $rencana_kerja_mei['price_d'];
			$nilai_jual_all_mei = $nilai_jual_125_mei + $nilai_jual_225_mei + $nilai_jual_250_mei + $nilai_jual_250_18_mei;

			$total_mei_nilai = $nilai_jual_all_mei;

			//VOLUME
			$rencana_kerja_mei = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_mei_awal' and '$date_mei_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_mei_produk_a = $rencana_kerja_mei['vol_produk_a'];
			$volume_rencana_kerja_mei_produk_b = $rencana_kerja_mei['vol_produk_b'];
			$volume_rencana_kerja_mei_produk_c = $rencana_kerja_mei['vol_produk_c'];
			$volume_rencana_kerja_mei_produk_d = $rencana_kerja_mei['vol_produk_d'];

			//BIAYA
			$rencana_kerja_biaya_mei = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_mei_awal' and '$date_mei_akhir'")
			->get()->row_array();
		
			$total_mei_biaya_bahan = $rencana_kerja_biaya_mei['biaya_bahan'];
			$total_mei_biaya_alat = $rencana_kerja_biaya_mei['biaya_alat'];
			$total_mei_biaya_overhead = $rencana_kerja_biaya_mei['biaya_overhead'];
			$total_mei_biaya_bank = $rencana_kerja_biaya_mei['biaya_bank'];
			$total_biaya_mei_biaya = $total_mei_biaya_bahan + $total_mei_biaya_alat + $total_mei_biaya_overhead + $total_mei_biaya_bank;
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

			$rencana_kerja_biaya_juni = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_juni_awal' and '$date_juni_akhir'")
			->get()->row_array();

			$nilai_jual_125_juni = $volume_juni_produk_a * $rencana_kerja_juni['price_a'];
			$nilai_jual_225_juni = $volume_juni_produk_b * $rencana_kerja_juni['price_b'];
			$nilai_jual_250_juni = $volume_juni_produk_c * $rencana_kerja_juni['price_c'];
			$nilai_jual_250_18_juni = $volume_juni_produk_d * $rencana_kerja_juni['price_d'];
			$nilai_jual_all_juni = $nilai_jual_125_juni + $nilai_jual_225_juni + $nilai_jual_250_juni + $nilai_jual_250_18_juni;

			$total_juni_nilai = $nilai_jual_all_juni;

			//VOLUME
			$rencana_kerja_juni = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_juni_awal' and '$date_juni_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_juni_produk_a = $rencana_kerja_juni['vol_produk_a'];
			$volume_rencana_kerja_juni_produk_b = $rencana_kerja_juni['vol_produk_b'];
			$volume_rencana_kerja_juni_produk_c = $rencana_kerja_juni['vol_produk_c'];
			$volume_rencana_kerja_juni_produk_d = $rencana_kerja_juni['vol_produk_d'];

			//BIAYA
			$rencana_kerja_biaya_juni = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_juni_awal' and '$date_juni_akhir'")
			->get()->row_array();
		
			$total_juni_biaya_bahan = $rencana_kerja_biaya_juni['biaya_bahan'];
			$total_juni_biaya_alat = $rencana_kerja_biaya_juni['biaya_alat'];
			$total_juni_biaya_overhead = $rencana_kerja_biaya_juni['biaya_overhead'];
			$total_juni_biaya_bank = $rencana_kerja_biaya_juni['biaya_bank'];
			$total_biaya_juni_biaya = $total_juni_biaya_bahan + $total_juni_biaya_alat + $total_juni_biaya_overhead + $total_juni_biaya_bank;
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

			$rencana_kerja_biaya_juli = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_juli_awal' and '$date_juli_akhir'")
			->get()->row_array();

			$nilai_jual_125_juli = $volume_juli_produk_a * $rencana_kerja_juli['price_a'];
			$nilai_jual_225_juli = $volume_juli_produk_b * $rencana_kerja_juli['price_b'];
			$nilai_jual_250_juli = $volume_juli_produk_c * $rencana_kerja_juli['price_c'];
			$nilai_jual_250_18_juli = $volume_juli_produk_d * $rencana_kerja_juli['price_d'];
			$nilai_jual_all_juli = $nilai_jual_125_juli + $nilai_jual_225_juli + $nilai_jual_250_juli + $nilai_jual_250_18_juli;

			$total_juli_nilai = $nilai_jual_all_juli;

			//VOLUME
			$rencana_kerja_juli = $this->db->select('r.*')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_juli_awal' and '$date_juli_akhir'")
			->get()->row_array();
			
			$volume_rencana_kerja_juli_produk_a = $rencana_kerja_juli['vol_produk_a'];
			$volume_rencana_kerja_juli_produk_b = $rencana_kerja_juli['vol_produk_b'];
			$volume_rencana_kerja_juli_produk_c = $rencana_kerja_juli['vol_produk_c'];
			$volume_rencana_kerja_juli_produk_d = $rencana_kerja_juli['vol_produk_d'];

			//BIAYA
			$rencana_kerja_biaya_juli = $this->db->select('r.*')
			->from('rak_biaya r')
			->where("r.tanggal_rencana_kerja between '$date_juli_awal' and '$date_juli_akhir'")
			->get()->row_array();
		
			$total_juli_biaya_bahan = $rencana_kerja_biaya_juli['biaya_bahan'];
			$total_juli_biaya_alat = $rencana_kerja_biaya_juli['biaya_alat'];
			$total_juli_biaya_overhead = $rencana_kerja_biaya_juli['biaya_overhead'];
			$total_juli_biaya_bank = $rencana_kerja_biaya_juli['biaya_bank'];
			$total_biaya_juli_biaya = $total_juli_biaya_bahan + $total_juli_biaya_alat + $total_juli_biaya_overhead + $total_juli_biaya_bank;
			?>
			<!-- JULI -->

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
			
			$total_biaya_all_biaya = $total_all_biaya_bahan + $total_all_biaya_alat + $total_all_biaya_overhead + $total_all_biaya_bank;

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

			<tr class="table-active4-rak">
				<th width="5%" class="text-center" rowspan="3" style="vertical-align:middle">NO.</th>
				<th class="text-center" rowspan="3" style="vertical-align:middle">URAIAN</th>
				<th class="text-center" rowspan="3" style="vertical-align:middle">SATUAN</th>
				<th class="text-center" rowspan="3" style="vertical-align:middle">ADEDENDUM RAP</th>
				<th class="text-center" rowspan="2" style="vertical-align:middle">REALISASI SD.</th>
				<th class="text-center" colspan="9">PROGNOSA</th>
				<th class="text-center" rowspan="3" style="vertical-align:middle">TOTAL</th>
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
				<?php
				$tanggal = $last_opname;
				$date = date('Y-m-d',strtotime($tanggal));
				?>
				<?php
				function tgl_indo($date){
					$bulan = array (
						1 =>   'Jan',
						'Feb',
						'Mar',
						'Apr',
						'Mei',
						'Jun',
						'Jul',
						'Agu',
						'Sep',
						'Okt',
						'Nov',
						'Des'
					);
					$pecahkan = explode('-', $date);
					
					// variabel pecahkan 0 = tanggal
					// variabel pecahkan 1 = bulan
					// variabel pecahkan 2 = tahun
				
					return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
					
				}
				?>
				<th class="text-center" style="text-transform:uppercase;"><?= tgl_indo(date($date)); ?></th>
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
				<th class="text-left" colspan="15">RENCANA PRODUKSI & PENDAPATAN USAHA</th>
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
			</tr>
			<tr class="table-active2-rak">
				<th class="text-right" colspan="2">TOTAL VOLUME</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($total_rap_volume_2022,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_akumulasi_volume,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_november_volume,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_desember_volume,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_januari_volume,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_februari_volume,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_maret_volume,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_april_volume,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_mei_volume,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_juni_volume,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_juli_volume,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_all_volume,2,',','.');?></th>
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
			</tr>
			<tr class="table-active2-rak">
				<th class="text-left" colspan="15">BIAYA</th>
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
			</tr>
	    </table>
		<?php
	}

	public function rencana_kerja($arr_date)
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
				table tr.table-judul{
					background-color: #e69500;
					font-size: 8px;
					font-weight: bold;
					color: black;
				}
					
				table tr.table-baris{
					background-color: #F0F0F0;
					font-size: 8px;
					font-weight: bold;
				}

				table tr.table-total{
					background-color: #E8E8E8;
					font-size: 8px;
					font-weight: bold;
				}
			</style>

			<?php
			$date_november_awal = date('2022-11-01');
			$date_november_akhir = date('2022-11-30');
			$date_desember_awal = date('2022-12-01');
			$date_desember_akhir = date('2022-12-31');
			$date_januari23_awal = date('2023-01-01');
			$date_januari23_akhir = date('2023-01-31');
			$date_februari23_awal = date('2023-02-01');
			$date_februari23_akhir = date('2023-02-28');
			$date_maret23_awal = date('2023-03-01');
			$date_maret23_akhir = date('2023-03-31');
			$date_april23_awal = date('2023-04-01');
			$date_april23_akhir = date('2023-04-30');
			$date_mei23_awal = date('2023-05-01');
			$date_mei23_akhir = date('2023-05-31');
			$date_juni23_awal = date('2023-06-01');
			$date_juni23_akhir = date('2023-06-30');
			$date_juli23_awal = date('2023-07-01');
			$date_juli23_akhir = date('2023-07-31');

			//NOVEMBER
			$rencana_kerja_november = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_november_awal' and '$date_november_akhir'")
			->get()->row_array();

			$volume_november_produk_a = $rencana_kerja_november['vol_produk_a'];
			$volume_november_produk_b = $rencana_kerja_november['vol_produk_b'];
			$volume_november_produk_c = $rencana_kerja_november['vol_produk_c'];
			$volume_november_produk_d = $rencana_kerja_november['vol_produk_d'];

			$total_november_volume = $volume_november_produk_a + $volume_november_produk_b + $volume_november_produk_c + $volume_november_produk_d;
				
			$nilai_jual_125_november = $volume_november_produk_a * $rencana_kerja_november['price_a'];
			$nilai_jual_225_november = $volume_november_produk_b * $rencana_kerja_november['price_b'];
			$nilai_jual_250_november = $volume_november_produk_c * $rencana_kerja_november['price_c'];
			$nilai_jual_250_18_november = $volume_november_produk_d * $rencana_kerja_november['price_d'];
			$nilai_jual_all_november = $nilai_jual_125_november + $nilai_jual_225_november + $nilai_jual_250_november + $nilai_jual_250_18_november;

			//DESEMBER
			$rencana_kerja_desember = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_desember_awal' and '$date_desember_akhir'")
			->get()->row_array();

			$volume_desember_produk_a = $rencana_kerja_desember['vol_produk_a'];
			$volume_desember_produk_b = $rencana_kerja_desember['vol_produk_b'];
			$volume_desember_produk_c = $rencana_kerja_desember['vol_produk_c'];
			$volume_desember_produk_d = $rencana_kerja_desember['vol_produk_d'];

			$total_desember_volume = $volume_desember_produk_a + $volume_desember_produk_b + $volume_desember_produk_c + $volume_desember_produk_d;
				
			$nilai_jual_125_desember = $volume_desember_produk_a * $rencana_kerja_desember['price_a'];
			$nilai_jual_225_desember = $volume_desember_produk_b * $rencana_kerja_desember['price_b'];
			$nilai_jual_250_desember = $volume_desember_produk_c * $rencana_kerja_desember['price_c'];
			$nilai_jual_250_18_desember = $volume_desember_produk_d * $rencana_kerja_desember['price_d'];
			$nilai_jual_all_desember = $nilai_jual_125_desember + $nilai_jual_225_desember + $nilai_jual_250_desember + $nilai_jual_250_18_desember;

			//JANUARI23
			$rencana_kerja_januari23 = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->row_array();

			$volume_januari23_produk_a = $rencana_kerja_januari23['vol_produk_a'];
			$volume_januari23_produk_b = $rencana_kerja_januari23['vol_produk_b'];
			$volume_januari23_produk_c = $rencana_kerja_januari23['vol_produk_c'];
			$volume_januari23_produk_d = $rencana_kerja_januari23['vol_produk_d'];

			$total_januari23_volume = $volume_januari23_produk_a + $volume_januari23_produk_b + $volume_januari23_produk_c + $volume_januari23_produk_d;
				
			$nilai_jual_125_januari23 = $volume_januari23_produk_a * $rencana_kerja_januari23['price_a'];
			$nilai_jual_225_januari23 = $volume_januari23_produk_b * $rencana_kerja_januari23['price_b'];
			$nilai_jual_250_januari23 = $volume_januari23_produk_c * $rencana_kerja_januari23['price_c'];
			$nilai_jual_250_18_januari23 = $volume_januari23_produk_d * $rencana_kerja_januari23['price_d'];
			$nilai_jual_all_januari23 = $nilai_jual_125_januari23 + $nilai_jual_225_januari23 + $nilai_jual_250_januari23 + $nilai_jual_250_18_januari23;

			//FEBRUARI23
			$rencana_kerja_februari23 = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_februari23_awal' and '$date_februari23_akhir'")
			->get()->row_array();

			$volume_februari23_produk_a = $rencana_kerja_februari23['vol_produk_a'];
			$volume_februari23_produk_b = $rencana_kerja_februari23['vol_produk_b'];
			$volume_februari23_produk_c = $rencana_kerja_februari23['vol_produk_c'];
			$volume_februari23_produk_d = $rencana_kerja_februari23['vol_produk_d'];

			$total_februari23_volume = $volume_februari23_produk_a + $volume_februari23_produk_b + $volume_februari23_produk_c + $volume_februari23_produk_d;
				
			$nilai_jual_125_februari23 = $volume_februari23_produk_a * $rencana_kerja_februari23['price_a'];
			$nilai_jual_225_februari23 = $volume_februari23_produk_b * $rencana_kerja_februari23['price_b'];
			$nilai_jual_250_februari23 = $volume_februari23_produk_c * $rencana_kerja_februari23['price_c'];
			$nilai_jual_250_18_februari23 = $volume_februari23_produk_d * $rencana_kerja_februari23['price_d'];
			$nilai_jual_all_februari23 = $nilai_jual_125_februari23 + $nilai_jual_225_februari23 + $nilai_jual_250_februari23 + $nilai_jual_250_18_februari23;

			//MARET23
			$rencana_kerja_maret23 = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_maret23_awal' and '$date_maret23_akhir'")
			->get()->row_array();

			$volume_maret23_produk_a = $rencana_kerja_maret23['vol_produk_a'];
			$volume_maret23_produk_b = $rencana_kerja_maret23['vol_produk_b'];
			$volume_maret23_produk_c = $rencana_kerja_maret23['vol_produk_c'];
			$volume_maret23_produk_d = $rencana_kerja_maret23['vol_produk_d'];

			$total_maret23_volume = $volume_maret23_produk_a + $volume_maret23_produk_b + $volume_maret23_produk_c + $volume_maret23_produk_d;
				
			$nilai_jual_125_maret23 = $volume_maret23_produk_a * $rencana_kerja_maret23['price_a'];
			$nilai_jual_225_maret23 = $volume_maret23_produk_b * $rencana_kerja_maret23['price_b'];
			$nilai_jual_250_maret23 = $volume_maret23_produk_c * $rencana_kerja_maret23['price_c'];
			$nilai_jual_250_18_maret23 = $volume_maret23_produk_d * $rencana_kerja_maret23['price_d'];
			$nilai_jual_all_maret23 = $nilai_jual_125_maret23 + $nilai_jual_225_maret23 + $nilai_jual_250_maret23 + $nilai_jual_250_18_maret23;

			//APRIL23
			$rencana_kerja_april23 = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_april23_awal' and '$date_april23_akhir'")
			->get()->row_array();

			$volume_april23_produk_a = $rencana_kerja_april23['vol_produk_a'];
			$volume_april23_produk_b = $rencana_kerja_april23['vol_produk_b'];
			$volume_april23_produk_c = $rencana_kerja_april23['vol_produk_c'];
			$volume_april23_produk_d = $rencana_kerja_april23['vol_produk_d'];

			$total_april23_volume = $volume_april23_produk_a + $volume_april23_produk_b + $volume_april23_produk_c + $volume_april23_produk_d;
				
			$nilai_jual_125_april23 = $volume_april23_produk_a * $rencana_kerja_april23['price_a'];
			$nilai_jual_225_april23 = $volume_april23_produk_b * $rencana_kerja_april23['price_b'];
			$nilai_jual_250_april23 = $volume_april23_produk_c * $rencana_kerja_april23['price_c'];
			$nilai_jual_250_18_april23 = $volume_april23_produk_d * $rencana_kerja_april23['price_d'];
			$nilai_jual_all_april23 = $nilai_jual_125_april23 + $nilai_jual_225_april23 + $nilai_jual_250_april23 + $nilai_jual_250_18_april23;

			//MEI23
			$rencana_kerja_mei23 = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_mei23_awal' and '$date_mei23_akhir'")
			->get()->row_array();

			$volume_mei23_produk_a = $rencana_kerja_mei23['vol_produk_a'];
			$volume_mei23_produk_b = $rencana_kerja_mei23['vol_produk_b'];
			$volume_mei23_produk_c = $rencana_kerja_mei23['vol_produk_c'];
			$volume_mei23_produk_d = $rencana_kerja_mei23['vol_produk_d'];

			$total_mei23_volume = $volume_mei23_produk_a + $volume_mei23_produk_b + $volume_mei23_produk_c + $volume_mei23_produk_d;
				
			$nilai_jual_125_mei23 = $volume_mei23_produk_a * $rencana_kerja_mei23['price_a'];
			$nilai_jual_225_mei23 = $volume_mei23_produk_b * $rencana_kerja_mei23['price_b'];
			$nilai_jual_250_mei23 = $volume_mei23_produk_c * $rencana_kerja_mei23['price_c'];
			$nilai_jual_250_18_mei23 = $volume_mei23_produk_d * $rencana_kerja_mei23['price_d'];
			$nilai_jual_all_mei23 = $nilai_jual_125_mei23 + $nilai_jual_225_mei23 + $nilai_jual_250_mei23 + $nilai_jual_250_18_mei23;

			//JUNI23
			$rencana_kerja_juni23 = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_juni23_awal' and '$date_juni23_akhir'")
			->get()->row_array();

			$volume_juni23_produk_a = $rencana_kerja_juni23['vol_produk_a'];
			$volume_juni23_produk_b = $rencana_kerja_juni23['vol_produk_b'];
			$volume_juni23_produk_c = $rencana_kerja_juni23['vol_produk_c'];
			$volume_juni23_produk_d = $rencana_kerja_juni23['vol_produk_d'];

			$total_juni23_volume = $volume_juni23_produk_a + $volume_juni23_produk_b + $volume_juni23_produk_c + $volume_juni23_produk_d;
				
			$nilai_jual_125_juni23 = $volume_juni23_produk_a * $rencana_kerja_juni23['price_a'];
			$nilai_jual_225_juni23 = $volume_juni23_produk_b * $rencana_kerja_juni23['price_b'];
			$nilai_jual_250_juni23 = $volume_juni23_produk_c * $rencana_kerja_juni23['price_c'];
			$nilai_jual_250_18_juni23 = $volume_juni23_produk_d * $rencana_kerja_juni23['price_d'];
			$nilai_jual_all_juni23 = $nilai_jual_125_juni23 + $nilai_jual_225_juni23 + $nilai_jual_250_juni23 + $nilai_jual_250_18_juni23;

			//JULI23
			$rencana_kerja_juli23 = $this->db->select('r.*, SUM(r.vol_produk_a) as vol_produk_a, SUM(r.vol_produk_b) as vol_produk_b, SUM(r.vol_produk_c) as vol_produk_c, SUM(r.vol_produk_d) as vol_produk_d')
			->from('rak r')
			->where("r.tanggal_rencana_kerja between '$date_juli23_awal' and '$date_juli23_akhir'")
			->get()->row_array();

			$volume_juli23_produk_a = $rencana_kerja_juli23['vol_produk_a'];
			$volume_juli23_produk_b = $rencana_kerja_juli23['vol_produk_b'];
			$volume_juli23_produk_c = $rencana_kerja_juli23['vol_produk_c'];
			$volume_juli23_produk_d = $rencana_kerja_juli23['vol_produk_d'];

			$total_juli23_volume = $volume_juli23_produk_a + $volume_juli23_produk_b + $volume_juli23_produk_c + $volume_juli23_produk_d;
				
			$nilai_jual_125_juli23 = $volume_juli23_produk_a * $rencana_kerja_juli23['price_a'];
			$nilai_jual_225_juli23 = $volume_juli23_produk_b * $rencana_kerja_juli23['price_b'];
			$nilai_jual_250_juli23 = $volume_juli23_produk_c * $rencana_kerja_juli23['price_c'];
			$nilai_jual_250_18_juli23 = $volume_juli23_produk_d * $rencana_kerja_juli23['price_d'];
			$nilai_jual_all_juli23 = $nilai_jual_125_juli23 + $nilai_jual_225_juli23 + $nilai_jual_250_juli23 + $nilai_jual_250_18_juli23;
			?>

			
			<?php
			//NOV
			$komposisi_125_nov = $this->db->select('(r.vol_produk_a * pk.presentase_a) as komposisi_semen_125, (vol_produk_a * pk.presentase_b) as komposisi_pasir_125, (vol_produk_a * pk.presentase_c) as komposisi_batu1020_125, (vol_produk_a * pk.presentase_d) as komposisi_batu2030_125')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_125 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_november_awal' and '$date_november_akhir'")
			->get()->result_array();

			$total_volume_semen_125_nov = 0;
			$total_volume_pasir_125_nov = 0;
			$total_volume_batu1020_125_nov = 0;
			$total_volume_batu2030_125_nov = 0;

			foreach ($komposisi_125_nov as $x){
				$total_volume_semen_125_nov = $x['komposisi_semen_125'];
				$total_volume_pasir_125_nov = $x['komposisi_pasir_125'];
				$total_volume_batu1020_125_nov = $x['komposisi_batu1020_125'];
				$total_volume_batu2030_125_nov = $x['komposisi_batu2030_125'];
			}

			$komposisi_225_nov = $this->db->select('(r.vol_produk_b * pk.presentase_a) as komposisi_semen_225, (vol_produk_b * pk.presentase_b) as komposisi_pasir_225, (vol_produk_b * pk.presentase_c) as komposisi_batu1020_225, (vol_produk_b * pk.presentase_d) as komposisi_batu2030_225')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_225 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_november_awal' and '$date_november_akhir'")
			->get()->result_array();

			$total_volume_semen_225_nov = 0;
			$total_volume_pasir_225_nov = 0;
			$total_volume_batu1020_225_nov = 0;
			$total_volume_batu2030_225_nov = 0;

			foreach ($komposisi_225_nov as $x){
				$total_volume_semen_225_nov = $x['komposisi_semen_225'];
				$total_volume_pasir_225_nov = $x['komposisi_pasir_225'];
				$total_volume_batu1020_225_nov = $x['komposisi_batu1020_225'];
				$total_volume_batu2030_225_nov = $x['komposisi_batu2030_225'];
			}

			$komposisi_250_nov = $this->db->select('(r.vol_produk_c * pk.presentase_a) as komposisi_semen_250, (vol_produk_c * pk.presentase_b) as komposisi_pasir_250, (vol_produk_c * pk.presentase_c) as komposisi_batu1020_250, (vol_produk_c * pk.presentase_d) as komposisi_batu2030_250')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_november_awal' and '$date_november_akhir'")
			->get()->result_array();

			$total_volume_semen_250_nov = 0;
			$total_volume_pasir_250_nov = 0;
			$total_volume_batu1020_250_nov = 0;
			$total_volume_batu2030_250_nov = 0;

			foreach ($komposisi_250_nov as $x){
				$total_volume_semen_250_nov = $x['komposisi_semen_250'];
				$total_volume_pasir_250_nov = $x['komposisi_pasir_250'];
				$total_volume_batu1020_250_nov = $x['komposisi_batu1020_250'];
				$total_volume_batu2030_250_nov = $x['komposisi_batu2030_250'];
			}

			$komposisi_250_2_nov = $this->db->select('(r.vol_produk_d * pk.presentase_a) as komposisi_semen_250_2, (vol_produk_d * pk.presentase_b) as komposisi_pasir_250_2, (vol_produk_d * pk.presentase_c) as komposisi_batu1020_250_2, (vol_produk_d * pk.presentase_d) as komposisi_batu2030_250_2')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250_2 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_november_awal' and '$date_november_akhir'")
			->get()->result_array();

			$total_volume_semen_250_2_nov = 0;
			$total_volume_pasir_250_2_nov = 0;
			$total_volume_batu1020_250_2_nov = 0;
			$total_volume_batu2030_250_2_nov = 0;

			foreach ($komposisi_250_2_nov as $x){
				$total_volume_semen_250_2_nov = $x['komposisi_semen_250_2'];
				$total_volume_pasir_250_2_nov = $x['komposisi_pasir_250_2'];
				$total_volume_batu1020_250_2_nov = $x['komposisi_batu1020_250_2'];
				$total_volume_batu2030_250_2_nov = $x['komposisi_batu2030_250_2'];
			}

			$total_volume_semen_nov = $total_volume_semen_125_nov + $total_volume_semen_225_nov + $total_volume_semen_250_nov + $total_volume_semen_250_2_nov;
			$total_volume_pasir_nov = $total_volume_pasir_125_nov + $total_volume_pasir_225_nov + $total_volume_pasir_250_nov + $total_volume_pasir_250_2_nov;
			$total_volume_batu1020_nov = $total_volume_batu1020_125_nov + $total_volume_batu1020_225_nov + $total_volume_batu1020_250_nov + $total_volume_batu1020_250_2_nov;
			$total_volume_batu2030_nov = $total_volume_batu2030_125_nov + $total_volume_batu2030_225_nov + $total_volume_batu2030_250_nov + $total_volume_batu2030_250_2_nov;

			//DES
			$komposisi_125_des = $this->db->select('(r.vol_produk_a * pk.presentase_a) as komposisi_semen_125, (vol_produk_a * pk.presentase_b) as komposisi_pasir_125, (vol_produk_a * pk.presentase_c) as komposisi_batu1020_125, (vol_produk_a * pk.presentase_d) as komposisi_batu2030_125')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_125 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_desember_awal' and '$date_desember_akhir'")
			->get()->result_array();

			$total_volume_semen_125_des = 0;
			$total_volume_pasir_125_des = 0;
			$total_volume_batu1020_125_des = 0;
			$total_volume_batu2030_125_des = 0;

			foreach ($komposisi_125_des as $x){
				$total_volume_semen_125_des = $x['komposisi_semen_125'];
				$total_volume_pasir_125_des = $x['komposisi_pasir_125'];
				$total_volume_batu1020_125_des = $x['komposisi_batu1020_125'];
				$total_volume_batu2030_125_des = $x['komposisi_batu2030_125'];
			}

			$komposisi_225_des = $this->db->select('(r.vol_produk_b * pk.presentase_a) as komposisi_semen_225, (vol_produk_b * pk.presentase_b) as komposisi_pasir_225, (vol_produk_b * pk.presentase_c) as komposisi_batu1020_225, (vol_produk_b * pk.presentase_d) as komposisi_batu2030_225')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_225 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_desember_awal' and '$date_desember_akhir'")
			->get()->result_array();

			$total_volume_semen_225_des = 0;
			$total_volume_pasir_225_des = 0;
			$total_volume_batu1020_225_des = 0;
			$total_volume_batu2030_225_des = 0;

			foreach ($komposisi_225_des as $x){
				$total_volume_semen_225_des = $x['komposisi_semen_225'];
				$total_volume_pasir_225_des = $x['komposisi_pasir_225'];
				$total_volume_batu1020_225_des = $x['komposisi_batu1020_225'];
				$total_volume_batu2030_225_des = $x['komposisi_batu2030_225'];
			}

			$komposisi_250_des = $this->db->select('(r.vol_produk_c * pk.presentase_a) as komposisi_semen_250, (vol_produk_c * pk.presentase_b) as komposisi_pasir_250, (vol_produk_c * pk.presentase_c) as komposisi_batu1020_250, (vol_produk_c * pk.presentase_d) as komposisi_batu2030_250')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_desember_awal' and '$date_desember_akhir'")
			->get()->result_array();

			$total_volume_semen_250_des = 0;
			$total_volume_pasir_250_des = 0;
			$total_volume_batu1020_250_des = 0;
			$total_volume_batu2030_250_des = 0;

			foreach ($komposisi_250_des as $x){
				$total_volume_semen_250_des = $x['komposisi_semen_250'];
				$total_volume_pasir_250_des = $x['komposisi_pasir_250'];
				$total_volume_batu1020_250_des = $x['komposisi_batu1020_250'];
				$total_volume_batu2030_250_des = $x['komposisi_batu2030_250'];
			}

			$komposisi_250_2_des = $this->db->select('(r.vol_produk_d * pk.presentase_a) as komposisi_semen_250_2, (vol_produk_d * pk.presentase_b) as komposisi_pasir_250_2, (vol_produk_d * pk.presentase_c) as komposisi_batu1020_250_2, (vol_produk_d * pk.presentase_d) as komposisi_batu2030_250_2')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250_2 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_desember_awal' and '$date_desember_akhir'")
			->get()->result_array();

			$total_volume_semen_250_2_des = 0;
			$total_volume_pasir_250_2_des = 0;
			$total_volume_batu1020_250_2_des = 0;
			$total_volume_batu2030_250_2_des = 0;

			foreach ($komposisi_250_2_des as $x){
				$total_volume_semen_250_2_des = $x['komposisi_semen_250_2'];
				$total_volume_pasir_250_2_des = $x['komposisi_pasir_250_2'];
				$total_volume_batu1020_250_2_des = $x['komposisi_batu1020_250_2'];
				$total_volume_batu2030_250_2_des = $x['komposisi_batu2030_250_2'];
			}

			$total_volume_semen_des = $total_volume_semen_125_des + $total_volume_semen_225_des + $total_volume_semen_250_des + $total_volume_semen_250_2_des;
			$total_volume_pasir_des = $total_volume_pasir_125_des + $total_volume_pasir_225_des + $total_volume_pasir_250_des + $total_volume_pasir_250_2_des;
			$total_volume_batu1020_des = $total_volume_batu1020_125_des + $total_volume_batu1020_225_des + $total_volume_batu1020_250_des + $total_volume_batu1020_250_2_des;
			$total_volume_batu2030_des = $total_volume_batu2030_125_des + $total_volume_batu2030_225_des + $total_volume_batu2030_250_des + $total_volume_batu2030_250_2_des;

			//JAN23
			$komposisi_125_jan23 = $this->db->select('(r.vol_produk_a * pk.presentase_a) as komposisi_semen_125, (vol_produk_a * pk.presentase_b) as komposisi_pasir_125, (vol_produk_a * pk.presentase_c) as komposisi_batu1020_125, (vol_produk_a * pk.presentase_d) as komposisi_batu2030_125')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_125 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_125_jan23 = 0;
			$total_volume_pasir_125_jan23 = 0;
			$total_volume_batu1020_125_jan23 = 0;
			$total_volume_batu2030_125_jan23 = 0;

			foreach ($komposisi_125_jan23 as $x){
				$total_volume_semen_125_jan23 = $x['komposisi_semen_125'];
				$total_volume_pasir_125_jan23 = $x['komposisi_pasir_125'];
				$total_volume_batu1020_125_jan23 = $x['komposisi_batu1020_125'];
				$total_volume_batu2030_125_jan23 = $x['komposisi_batu2030_125'];
			}

			$komposisi_225_jan23 = $this->db->select('(r.vol_produk_b * pk.presentase_a) as komposisi_semen_225, (vol_produk_b * pk.presentase_b) as komposisi_pasir_225, (vol_produk_b * pk.presentase_c) as komposisi_batu1020_225, (vol_produk_b * pk.presentase_d) as komposisi_batu2030_225')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_225 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_225_jan23 = 0;
			$total_volume_pasir_225_jan23 = 0;
			$total_volume_batu1020_225_jan23 = 0;
			$total_volume_batu2030_225_jan23 = 0;

			foreach ($komposisi_225_jan23 as $x){
				$total_volume_semen_225_jan23 = $x['komposisi_semen_225'];
				$total_volume_pasir_225_jan23 = $x['komposisi_pasir_225'];
				$total_volume_batu1020_225_jan23 = $x['komposisi_batu1020_225'];
				$total_volume_batu2030_225_jan23 = $x['komposisi_batu2030_225'];
			}

			$komposisi_250_jan23 = $this->db->select('(r.vol_produk_c * pk.presentase_a) as komposisi_semen_250, (vol_produk_c * pk.presentase_b) as komposisi_pasir_250, (vol_produk_c * pk.presentase_c) as komposisi_batu1020_250, (vol_produk_c * pk.presentase_d) as komposisi_batu2030_250')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_250_jan23 = 0;
			$total_volume_pasir_250_jan23 = 0;
			$total_volume_batu1020_250_jan23 = 0;
			$total_volume_batu2030_250_jan23 = 0;

			foreach ($komposisi_250_jan23 as $x){
				$total_volume_semen_250_jan23 = $x['komposisi_semen_250'];
				$total_volume_pasir_250_jan23 = $x['komposisi_pasir_250'];
				$total_volume_batu1020_250_jan23 = $x['komposisi_batu1020_250'];
				$total_volume_batu2030_250_jan23 = $x['komposisi_batu2030_250'];
			}

			$komposisi_250_2_jan23 = $this->db->select('(r.vol_produk_d * pk.presentase_a) as komposisi_semen_250_2, (vol_produk_d * pk.presentase_b) as komposisi_pasir_250_2, (vol_produk_d * pk.presentase_c) as komposisi_batu1020_250_2, (vol_produk_d * pk.presentase_d) as komposisi_batu2030_250_2')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250_2 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_250_2_jan23 = 0;
			$total_volume_pasir_250_2_jan23 = 0;
			$total_volume_batu1020_250_2_jan23 = 0;
			$total_volume_batu2030_250_2_jan23 = 0;

			foreach ($komposisi_250_2_jan23 as $x){
				$total_volume_semen_250_2_jan23 = $x['komposisi_semen_250_2'];
				$total_volume_pasir_250_2_jan23 = $x['komposisi_pasir_250_2'];
				$total_volume_batu1020_250_2_jan23 = $x['komposisi_batu1020_250_2'];
				$total_volume_batu2030_250_2_jan23 = $x['komposisi_batu2030_250_2'];
			}

			$total_volume_semen_jan23 = $total_volume_semen_125_jan23 + $total_volume_semen_225_jan23 + $total_volume_semen_250_jan23 + $total_volume_semen_250_2_jan23;
			$total_volume_pasir_jan23 = $total_volume_pasir_125_jan23 + $total_volume_pasir_225_jan23 + $total_volume_pasir_250_jan23 + $total_volume_pasir_250_2_jan23;
			$total_volume_batu1020_jan23 = $total_volume_batu1020_125_jan23 + $total_volume_batu1020_225_jan23 + $total_volume_batu1020_250_jan23 + $total_volume_batu1020_250_2_jan23;
			$total_volume_batu2030_jan23 = $total_volume_batu2030_125_jan23 + $total_volume_batu2030_225_jan23 + $total_volume_batu2030_250_jan23 + $total_volume_batu2030_250_2_jan23;

			//FEB23
			$komposisi_125_feb23 = $this->db->select('(r.vol_produk_a * pk.presentase_a) as komposisi_semen_125, (vol_produk_a * pk.presentase_b) as komposisi_pasir_125, (vol_produk_a * pk.presentase_c) as komposisi_batu1020_125, (vol_produk_a * pk.presentase_d) as komposisi_batu2030_125')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_125 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_125_feb23 = 0;
			$total_volume_pasir_125_feb23 = 0;
			$total_volume_batu1020_125_feb23 = 0;
			$total_volume_batu2030_125_feb23 = 0;

			foreach ($komposisi_125_feb23 as $x){
				$total_volume_semen_125_feb23 = $x['komposisi_semen_125'];
				$total_volume_pasir_125_feb23 = $x['komposisi_pasir_125'];
				$total_volume_batu1020_125_feb23 = $x['komposisi_batu1020_125'];
				$total_volume_batu2030_125_feb23 = $x['komposisi_batu2030_125'];
			}

			$komposisi_225_feb23 = $this->db->select('(r.vol_produk_b * pk.presentase_a) as komposisi_semen_225, (vol_produk_b * pk.presentase_b) as komposisi_pasir_225, (vol_produk_b * pk.presentase_c) as komposisi_batu1020_225, (vol_produk_b * pk.presentase_d) as komposisi_batu2030_225')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_225 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_225_feb23 = 0;
			$total_volume_pasir_225_feb23 = 0;
			$total_volume_batu1020_225_feb23 = 0;
			$total_volume_batu2030_225_feb23 = 0;

			foreach ($komposisi_225_feb23 as $x){
				$total_volume_semen_225_feb23 = $x['komposisi_semen_225'];
				$total_volume_pasir_225_feb23 = $x['komposisi_pasir_225'];
				$total_volume_batu1020_225_feb23 = $x['komposisi_batu1020_225'];
				$total_volume_batu2030_225_feb23 = $x['komposisi_batu2030_225'];
			}

			$komposisi_250_feb23 = $this->db->select('(r.vol_produk_c * pk.presentase_a) as komposisi_semen_250, (vol_produk_c * pk.presentase_b) as komposisi_pasir_250, (vol_produk_c * pk.presentase_c) as komposisi_batu1020_250, (vol_produk_c * pk.presentase_d) as komposisi_batu2030_250')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_250_feb23 = 0;
			$total_volume_pasir_250_feb23 = 0;
			$total_volume_batu1020_250_feb23 = 0;
			$total_volume_batu2030_250_feb23 = 0;

			foreach ($komposisi_250_feb23 as $x){
				$total_volume_semen_250_feb23 = $x['komposisi_semen_250'];
				$total_volume_pasir_250_feb23 = $x['komposisi_pasir_250'];
				$total_volume_batu1020_250_feb23 = $x['komposisi_batu1020_250'];
				$total_volume_batu2030_250_feb23 = $x['komposisi_batu2030_250'];
			}

			$komposisi_250_2_feb23 = $this->db->select('(r.vol_produk_d * pk.presentase_a) as komposisi_semen_250_2, (vol_produk_d * pk.presentase_b) as komposisi_pasir_250_2, (vol_produk_d * pk.presentase_c) as komposisi_batu1020_250_2, (vol_produk_d * pk.presentase_d) as komposisi_batu2030_250_2')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250_2 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_250_2_feb23 = 0;
			$total_volume_pasir_250_2_feb23 = 0;
			$total_volume_batu1020_250_2_feb23 = 0;
			$total_volume_batu2030_250_2_feb23 = 0;

			foreach ($komposisi_250_2_feb23 as $x){
				$total_volume_semen_250_2_feb23 = $x['komposisi_semen_250_2'];
				$total_volume_pasir_250_2_feb23 = $x['komposisi_pasir_250_2'];
				$total_volume_batu1020_250_2_feb23 = $x['komposisi_batu1020_250_2'];
				$total_volume_batu2030_250_2_feb23 = $x['komposisi_batu2030_250_2'];
			}

			$total_volume_semen_feb23 = $total_volume_semen_125_feb23 + $total_volume_semen_225_feb23 + $total_volume_semen_250_feb23 + $total_volume_semen_250_2_feb23;
			$total_volume_pasir_feb23 = $total_volume_pasir_125_feb23 + $total_volume_pasir_225_feb23 + $total_volume_pasir_250_feb23 + $total_volume_pasir_250_2_feb23;
			$total_volume_batu1020_feb23 = $total_volume_batu1020_125_feb23 + $total_volume_batu1020_225_feb23 + $total_volume_batu1020_250_feb23 + $total_volume_batu1020_250_2_feb23;
			$total_volume_batu2030_feb23 = $total_volume_batu2030_125_feb23 + $total_volume_batu2030_225_feb23 + $total_volume_batu2030_250_feb23 + $total_volume_batu2030_250_2_feb23;

			//MAR23
			$komposisi_125_mar23 = $this->db->select('(r.vol_produk_a * pk.presentase_a) as komposisi_semen_125, (vol_produk_a * pk.presentase_b) as komposisi_pasir_125, (vol_produk_a * pk.presentase_c) as komposisi_batu1020_125, (vol_produk_a * pk.presentase_d) as komposisi_batu2030_125')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_125 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_125_mar23 = 0;
			$total_volume_pasir_125_mar23 = 0;
			$total_volume_batu1020_125_mar23 = 0;
			$total_volume_batu2030_125_mar23 = 0;

			foreach ($komposisi_125_mar23 as $x){
				$total_volume_semen_125_mar23 = $x['komposisi_semen_125'];
				$total_volume_pasir_125_mar23 = $x['komposisi_pasir_125'];
				$total_volume_batu1020_125_mar23 = $x['komposisi_batu1020_125'];
				$total_volume_batu2030_125_mar23 = $x['komposisi_batu2030_125'];
			}

			$komposisi_225_mar23 = $this->db->select('(r.vol_produk_b * pk.presentase_a) as komposisi_semen_225, (vol_produk_b * pk.presentase_b) as komposisi_pasir_225, (vol_produk_b * pk.presentase_c) as komposisi_batu1020_225, (vol_produk_b * pk.presentase_d) as komposisi_batu2030_225')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_225 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_225_mar23 = 0;
			$total_volume_pasir_225_mar23 = 0;
			$total_volume_batu1020_225_mar23 = 0;
			$total_volume_batu2030_225_mar23 = 0;

			foreach ($komposisi_225_mar23 as $x){
				$total_volume_semen_225_mar23 = $x['komposisi_semen_225'];
				$total_volume_pasir_225_mar23 = $x['komposisi_pasir_225'];
				$total_volume_batu1020_225_mar23 = $x['komposisi_batu1020_225'];
				$total_volume_batu2030_225_mar23 = $x['komposisi_batu2030_225'];
			}

			$komposisi_250_mar23 = $this->db->select('(r.vol_produk_c * pk.presentase_a) as komposisi_semen_250, (vol_produk_c * pk.presentase_b) as komposisi_pasir_250, (vol_produk_c * pk.presentase_c) as komposisi_batu1020_250, (vol_produk_c * pk.presentase_d) as komposisi_batu2030_250')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_250_mar23 = 0;
			$total_volume_pasir_250_mar23 = 0;
			$total_volume_batu1020_250_mar23 = 0;
			$total_volume_batu2030_250_mar23 = 0;

			foreach ($komposisi_250_mar23 as $x){
				$total_volume_semen_250_mar23 = $x['komposisi_semen_250'];
				$total_volume_pasir_250_mar23 = $x['komposisi_pasir_250'];
				$total_volume_batu1020_250_mar23 = $x['komposisi_batu1020_250'];
				$total_volume_batu2030_250_mar23 = $x['komposisi_batu2030_250'];
			}

			$komposisi_250_2_mar23 = $this->db->select('(r.vol_produk_d * pk.presentase_a) as komposisi_semen_250_2, (vol_produk_d * pk.presentase_b) as komposisi_pasir_250_2, (vol_produk_d * pk.presentase_c) as komposisi_batu1020_250_2, (vol_produk_d * pk.presentase_d) as komposisi_batu2030_250_2')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250_2 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_250_2_mar23 = 0;
			$total_volume_pasir_250_2_mar23 = 0;
			$total_volume_batu1020_250_2_mar23 = 0;
			$total_volume_batu2030_250_2_mar23 = 0;

			foreach ($komposisi_250_2_mar23 as $x){
				$total_volume_semen_250_2_mar23 = $x['komposisi_semen_250_2'];
				$total_volume_pasir_250_2_mar23 = $x['komposisi_pasir_250_2'];
				$total_volume_batu1020_250_2_mar23 = $x['komposisi_batu1020_250_2'];
				$total_volume_batu2030_250_2_mar23 = $x['komposisi_batu2030_250_2'];
			}

			$total_volume_semen_mar23 = $total_volume_semen_125_mar23 + $total_volume_semen_225_mar23 + $total_volume_semen_250_mar23 + $total_volume_semen_250_2_mar23;
			$total_volume_pasir_mar23 = $total_volume_pasir_125_mar23 + $total_volume_pasir_225_mar23 + $total_volume_pasir_250_mar23 + $total_volume_pasir_250_2_mar23;
			$total_volume_batu1020_mar23 = $total_volume_batu1020_125_mar23 + $total_volume_batu1020_225_mar23 + $total_volume_batu1020_250_mar23 + $total_volume_batu1020_250_2_mar23;
			$total_volume_batu2030_mar23 = $total_volume_batu2030_125_mar23 + $total_volume_batu2030_225_mar23 + $total_volume_batu2030_250_mar23 + $total_volume_batu2030_250_2_mar23;

			//APR23
			$komposisi_125_apr23 = $this->db->select('(r.vol_produk_a * pk.presentase_a) as komposisi_semen_125, (vol_produk_a * pk.presentase_b) as komposisi_pasir_125, (vol_produk_a * pk.presentase_c) as komposisi_batu1020_125, (vol_produk_a * pk.presentase_d) as komposisi_batu2030_125')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_125 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_125_apr23 = 0;
			$total_volume_pasir_125_apr23 = 0;
			$total_volume_batu1020_125_apr23 = 0;
			$total_volume_batu2030_125_apr23 = 0;

			foreach ($komposisi_125_apr23 as $x){
				$total_volume_semen_125_apr23 = $x['komposisi_semen_125'];
				$total_volume_pasir_125_apr23 = $x['komposisi_pasir_125'];
				$total_volume_batu1020_125_apr23 = $x['komposisi_batu1020_125'];
				$total_volume_batu2030_125_apr23 = $x['komposisi_batu2030_125'];
			}

			$komposisi_225_apr23 = $this->db->select('(r.vol_produk_b * pk.presentase_a) as komposisi_semen_225, (vol_produk_b * pk.presentase_b) as komposisi_pasir_225, (vol_produk_b * pk.presentase_c) as komposisi_batu1020_225, (vol_produk_b * pk.presentase_d) as komposisi_batu2030_225')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_225 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_225_apr23 = 0;
			$total_volume_pasir_225_apr23 = 0;
			$total_volume_batu1020_225_apr23 = 0;
			$total_volume_batu2030_225_apr23 = 0;

			foreach ($komposisi_225_apr23 as $x){
				$total_volume_semen_225_apr23 = $x['komposisi_semen_225'];
				$total_volume_pasir_225_apr23 = $x['komposisi_pasir_225'];
				$total_volume_batu1020_225_apr23 = $x['komposisi_batu1020_225'];
				$total_volume_batu2030_225_apr23 = $x['komposisi_batu2030_225'];
			}

			$komposisi_250_apr23 = $this->db->select('(r.vol_produk_c * pk.presentase_a) as komposisi_semen_250, (vol_produk_c * pk.presentase_b) as komposisi_pasir_250, (vol_produk_c * pk.presentase_c) as komposisi_batu1020_250, (vol_produk_c * pk.presentase_d) as komposisi_batu2030_250')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_250_apr23 = 0;
			$total_volume_pasir_250_apr23 = 0;
			$total_volume_batu1020_250_apr23 = 0;
			$total_volume_batu2030_250_apr23 = 0;

			foreach ($komposisi_250_apr23 as $x){
				$total_volume_semen_250_apr23 = $x['komposisi_semen_250'];
				$total_volume_pasir_250_apr23 = $x['komposisi_pasir_250'];
				$total_volume_batu1020_250_apr23 = $x['komposisi_batu1020_250'];
				$total_volume_batu2030_250_apr23 = $x['komposisi_batu2030_250'];
			}

			$komposisi_250_2_apr23 = $this->db->select('(r.vol_produk_d * pk.presentase_a) as komposisi_semen_250_2, (vol_produk_d * pk.presentase_b) as komposisi_pasir_250_2, (vol_produk_d * pk.presentase_c) as komposisi_batu1020_250_2, (vol_produk_d * pk.presentase_d) as komposisi_batu2030_250_2')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250_2 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_250_2_apr23 = 0;
			$total_volume_pasir_250_2_apr23 = 0;
			$total_volume_batu1020_250_2_apr23 = 0;
			$total_volume_batu2030_250_2_apr23 = 0;

			foreach ($komposisi_250_2_apr23 as $x){
				$total_volume_semen_250_2_apr23 = $x['komposisi_semen_250_2'];
				$total_volume_pasir_250_2_apr23 = $x['komposisi_pasir_250_2'];
				$total_volume_batu1020_250_2_apr23 = $x['komposisi_batu1020_250_2'];
				$total_volume_batu2030_250_2_apr23 = $x['komposisi_batu2030_250_2'];
			}

			$total_volume_semen_apr23 = $total_volume_semen_125_apr23 + $total_volume_semen_225_apr23 + $total_volume_semen_250_apr23 + $total_volume_semen_250_2_apr23;
			$total_volume_pasir_apr23 = $total_volume_pasir_125_apr23 + $total_volume_pasir_225_apr23 + $total_volume_pasir_250_apr23 + $total_volume_pasir_250_2_apr23;
			$total_volume_batu1020_apr23 = $total_volume_batu1020_125_apr23 + $total_volume_batu1020_225_apr23 + $total_volume_batu1020_250_apr23 + $total_volume_batu1020_250_2_apr23;
			$total_volume_batu2030_apr23 = $total_volume_batu2030_125_apr23 + $total_volume_batu2030_225_apr23 + $total_volume_batu2030_250_apr23 + $total_volume_batu2030_250_2_apr23;

			//MEI23
			$komposisi_125_mei23 = $this->db->select('(r.vol_produk_a * pk.presentase_a) as komposisi_semen_125, (vol_produk_a * pk.presentase_b) as komposisi_pasir_125, (vol_produk_a * pk.presentase_c) as komposisi_batu1020_125, (vol_produk_a * pk.presentase_d) as komposisi_batu2030_125')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_125 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_125_mei23 = 0;
			$total_volume_pasir_125_mei23 = 0;
			$total_volume_batu1020_125_mei23 = 0;
			$total_volume_batu2030_125_mei23 = 0;

			foreach ($komposisi_125_mei23 as $x){
				$total_volume_semen_125_mei23 = $x['komposisi_semen_125'];
				$total_volume_pasir_125_mei23 = $x['komposisi_pasir_125'];
				$total_volume_batu1020_125_mei23 = $x['komposisi_batu1020_125'];
				$total_volume_batu2030_125_mei23 = $x['komposisi_batu2030_125'];
			}

			$komposisi_225_mei23 = $this->db->select('(r.vol_produk_b * pk.presentase_a) as komposisi_semen_225, (vol_produk_b * pk.presentase_b) as komposisi_pasir_225, (vol_produk_b * pk.presentase_c) as komposisi_batu1020_225, (vol_produk_b * pk.presentase_d) as komposisi_batu2030_225')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_225 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_225_mei23 = 0;
			$total_volume_pasir_225_mei23 = 0;
			$total_volume_batu1020_225_mei23 = 0;
			$total_volume_batu2030_225_mei23 = 0;

			foreach ($komposisi_225_mei23 as $x){
				$total_volume_semen_225_mei23 = $x['komposisi_semen_225'];
				$total_volume_pasir_225_mei23 = $x['komposisi_pasir_225'];
				$total_volume_batu1020_225_mei23 = $x['komposisi_batu1020_225'];
				$total_volume_batu2030_225_mei23 = $x['komposisi_batu2030_225'];
			}

			$komposisi_250_mei23 = $this->db->select('(r.vol_produk_c * pk.presentase_a) as komposisi_semen_250, (vol_produk_c * pk.presentase_b) as komposisi_pasir_250, (vol_produk_c * pk.presentase_c) as komposisi_batu1020_250, (vol_produk_c * pk.presentase_d) as komposisi_batu2030_250')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_250_mei23 = 0;
			$total_volume_pasir_250_mei23 = 0;
			$total_volume_batu1020_250_mei23 = 0;
			$total_volume_batu2030_250_mei23 = 0;

			foreach ($komposisi_250_mei23 as $x){
				$total_volume_semen_250_mei23 = $x['komposisi_semen_250'];
				$total_volume_pasir_250_mei23 = $x['komposisi_pasir_250'];
				$total_volume_batu1020_250_mei23 = $x['komposisi_batu1020_250'];
				$total_volume_batu2030_250_mei23 = $x['komposisi_batu2030_250'];
			}

			$komposisi_250_2_mei23 = $this->db->select('(r.vol_produk_d * pk.presentase_a) as komposisi_semen_250_2, (vol_produk_d * pk.presentase_b) as komposisi_pasir_250_2, (vol_produk_d * pk.presentase_c) as komposisi_batu1020_250_2, (vol_produk_d * pk.presentase_d) as komposisi_batu2030_250_2')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250_2 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_250_2_mei23 = 0;
			$total_volume_pasir_250_2_mei23 = 0;
			$total_volume_batu1020_250_2_mei23 = 0;
			$total_volume_batu2030_250_2_mei23 = 0;

			foreach ($komposisi_250_2_mei23 as $x){
				$total_volume_semen_250_2_mei23 = $x['komposisi_semen_250_2'];
				$total_volume_pasir_250_2_mei23 = $x['komposisi_pasir_250_2'];
				$total_volume_batu1020_250_2_mei23 = $x['komposisi_batu1020_250_2'];
				$total_volume_batu2030_250_2_mei23 = $x['komposisi_batu2030_250_2'];
			}

			$total_volume_semen_mei23 = $total_volume_semen_125_mei23 + $total_volume_semen_225_mei23 + $total_volume_semen_250_mei23 + $total_volume_semen_250_2_mei23;
			$total_volume_pasir_mei23 = $total_volume_pasir_125_mei23 + $total_volume_pasir_225_mei23 + $total_volume_pasir_250_mei23 + $total_volume_pasir_250_2_mei23;
			$total_volume_batu1020_mei23 = $total_volume_batu1020_125_mei23 + $total_volume_batu1020_225_mei23 + $total_volume_batu1020_250_mei23 + $total_volume_batu1020_250_2_mei23;
			$total_volume_batu2030_mei23 = $total_volume_batu2030_125_mei23 + $total_volume_batu2030_225_mei23 + $total_volume_batu2030_250_mei23 + $total_volume_batu2030_250_2_mei23;

			//JUN23
			$komposisi_125_jun23 = $this->db->select('(r.vol_produk_a * pk.presentase_a) as komposisi_semen_125, (vol_produk_a * pk.presentase_b) as komposisi_pasir_125, (vol_produk_a * pk.presentase_c) as komposisi_batu1020_125, (vol_produk_a * pk.presentase_d) as komposisi_batu2030_125')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_125 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_125_jun23 = 0;
			$total_volume_pasir_125_jun23 = 0;
			$total_volume_batu1020_125_jun23 = 0;
			$total_volume_batu2030_125_jun23 = 0;

			foreach ($komposisi_125_jun23 as $x){
				$total_volume_semen_125_jun23 = $x['komposisi_semen_125'];
				$total_volume_pasir_125_jun23 = $x['komposisi_pasir_125'];
				$total_volume_batu1020_125_jun23 = $x['komposisi_batu1020_125'];
				$total_volume_batu2030_125_jun23 = $x['komposisi_batu2030_125'];
			}

			$komposisi_225_jun23 = $this->db->select('(r.vol_produk_b * pk.presentase_a) as komposisi_semen_225, (vol_produk_b * pk.presentase_b) as komposisi_pasir_225, (vol_produk_b * pk.presentase_c) as komposisi_batu1020_225, (vol_produk_b * pk.presentase_d) as komposisi_batu2030_225')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_225 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_225_jun23 = 0;
			$total_volume_pasir_225_jun23 = 0;
			$total_volume_batu1020_225_jun23 = 0;
			$total_volume_batu2030_225_jun23 = 0;

			foreach ($komposisi_225_jun23 as $x){
				$total_volume_semen_225_jun23 = $x['komposisi_semen_225'];
				$total_volume_pasir_225_jun23 = $x['komposisi_pasir_225'];
				$total_volume_batu1020_225_jun23 = $x['komposisi_batu1020_225'];
				$total_volume_batu2030_225_jun23 = $x['komposisi_batu2030_225'];
			}

			$komposisi_250_jun23 = $this->db->select('(r.vol_produk_c * pk.presentase_a) as komposisi_semen_250, (vol_produk_c * pk.presentase_b) as komposisi_pasir_250, (vol_produk_c * pk.presentase_c) as komposisi_batu1020_250, (vol_produk_c * pk.presentase_d) as komposisi_batu2030_250')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_250_jun23 = 0;
			$total_volume_pasir_250_jun23 = 0;
			$total_volume_batu1020_250_jun23 = 0;
			$total_volume_batu2030_250_jun23 = 0;

			foreach ($komposisi_250_jun23 as $x){
				$total_volume_semen_250_jun23 = $x['komposisi_semen_250'];
				$total_volume_pasir_250_jun23 = $x['komposisi_pasir_250'];
				$total_volume_batu1020_250_jun23 = $x['komposisi_batu1020_250'];
				$total_volume_batu2030_250_jun23 = $x['komposisi_batu2030_250'];
			}

			$komposisi_250_2_jun23 = $this->db->select('(r.vol_produk_d * pk.presentase_a) as komposisi_semen_250_2, (vol_produk_d * pk.presentase_b) as komposisi_pasir_250_2, (vol_produk_d * pk.presentase_c) as komposisi_batu1020_250_2, (vol_produk_d * pk.presentase_d) as komposisi_batu2030_250_2')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250_2 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_250_2_jun23 = 0;
			$total_volume_pasir_250_2_jun23 = 0;
			$total_volume_batu1020_250_2_jun23 = 0;
			$total_volume_batu2030_250_2_jun23 = 0;

			foreach ($komposisi_250_2_jun23 as $x){
				$total_volume_semen_250_2_jun23 = $x['komposisi_semen_250_2'];
				$total_volume_pasir_250_2_jun23 = $x['komposisi_pasir_250_2'];
				$total_volume_batu1020_250_2_jun23 = $x['komposisi_batu1020_250_2'];
				$total_volume_batu2030_250_2_jun23 = $x['komposisi_batu2030_250_2'];
			}

			$total_volume_semen_jun23 = $total_volume_semen_125_jun23 + $total_volume_semen_225_jun23 + $total_volume_semen_250_jun23 + $total_volume_semen_250_2_jun23;
			$total_volume_pasir_jun23 = $total_volume_pasir_125_jun23 + $total_volume_pasir_225_jun23 + $total_volume_pasir_250_jun23 + $total_volume_pasir_250_2_jun23;
			$total_volume_batu1020_jun23 = $total_volume_batu1020_125_jun23 + $total_volume_batu1020_225_jun23 + $total_volume_batu1020_250_jun23 + $total_volume_batu1020_250_2_jun23;
			$total_volume_batu2030_jun23 = $total_volume_batu2030_125_jun23 + $total_volume_batu2030_225_jun23 + $total_volume_batu2030_250_jun23 + $total_volume_batu2030_250_2_jun23;

			//JUL23
			$komposisi_125_jul23 = $this->db->select('(r.vol_produk_a * pk.presentase_a) as komposisi_semen_125, (vol_produk_a * pk.presentase_b) as komposisi_pasir_125, (vol_produk_a * pk.presentase_c) as komposisi_batu1020_125, (vol_produk_a * pk.presentase_d) as komposisi_batu2030_125')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_125 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_125_jul23 = 0;
			$total_volume_pasir_125_jul23 = 0;
			$total_volume_batu1020_125_jul23 = 0;
			$total_volume_batu2030_125_jul23 = 0;

			foreach ($komposisi_125_jul23 as $x){
				$total_volume_semen_125_jul23 = $x['komposisi_semen_125'];
				$total_volume_pasir_125_jul23 = $x['komposisi_pasir_125'];
				$total_volume_batu1020_125_jul23 = $x['komposisi_batu1020_125'];
				$total_volume_batu2030_125_jul23 = $x['komposisi_batu2030_125'];
			}

			$komposisi_225_jul23 = $this->db->select('(r.vol_produk_b * pk.presentase_a) as komposisi_semen_225, (vol_produk_b * pk.presentase_b) as komposisi_pasir_225, (vol_produk_b * pk.presentase_c) as komposisi_batu1020_225, (vol_produk_b * pk.presentase_d) as komposisi_batu2030_225')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_225 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_225_jul23 = 0;
			$total_volume_pasir_225_jul23 = 0;
			$total_volume_batu1020_225_jul23 = 0;
			$total_volume_batu2030_225_jul23 = 0;

			foreach ($komposisi_225_jul23 as $x){
				$total_volume_semen_225_jul23 = $x['komposisi_semen_225'];
				$total_volume_pasir_225_jul23 = $x['komposisi_pasir_225'];
				$total_volume_batu1020_225_jul23 = $x['komposisi_batu1020_225'];
				$total_volume_batu2030_225_jul23 = $x['komposisi_batu2030_225'];
			}

			$komposisi_250_jul23 = $this->db->select('(r.vol_produk_c * pk.presentase_a) as komposisi_semen_250, (vol_produk_c * pk.presentase_b) as komposisi_pasir_250, (vol_produk_c * pk.presentase_c) as komposisi_batu1020_250, (vol_produk_c * pk.presentase_d) as komposisi_batu2030_250')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_250_jul23 = 0;
			$total_volume_pasir_250_jul23 = 0;
			$total_volume_batu1020_250_jul23 = 0;
			$total_volume_batu2030_250_jul23 = 0;

			foreach ($komposisi_250_jul23 as $x){
				$total_volume_semen_250_jul23 = $x['komposisi_semen_250'];
				$total_volume_pasir_250_jul23 = $x['komposisi_pasir_250'];
				$total_volume_batu1020_250_jul23 = $x['komposisi_batu1020_250'];
				$total_volume_batu2030_250_jul23 = $x['komposisi_batu2030_250'];
			}

			$komposisi_250_2_jul23 = $this->db->select('(r.vol_produk_d * pk.presentase_a) as komposisi_semen_250_2, (vol_produk_d * pk.presentase_b) as komposisi_pasir_250_2, (vol_produk_d * pk.presentase_c) as komposisi_batu1020_250_2, (vol_produk_d * pk.presentase_d) as komposisi_batu2030_250_2')
			->from('rak r')
			->join('pmm_agregat pk', 'r.komposisi_250_2 = pk.id','left')
			->where("r.tanggal_rencana_kerja between '$date_januari23_awal' and '$date_januari23_akhir'")
			->get()->result_array();

			$total_volume_semen_250_2_jul23 = 0;
			$total_volume_pasir_250_2_jul23 = 0;
			$total_volume_batu1020_250_2_jul23 = 0;
			$total_volume_batu2030_250_2_jul23 = 0;

			foreach ($komposisi_250_2_jul23 as $x){
				$total_volume_semen_250_2_jul23 = $x['komposisi_semen_250_2'];
				$total_volume_pasir_250_2_jul23 = $x['komposisi_pasir_250_2'];
				$total_volume_batu1020_250_2_jul23 = $x['komposisi_batu1020_250_2'];
				$total_volume_batu2030_250_2_jul23 = $x['komposisi_batu2030_250_2'];
			}

			$total_volume_semen_jul23 = $total_volume_semen_125_jul23 + $total_volume_semen_225_jul23 + $total_volume_semen_250_jul23 + $total_volume_semen_250_2_jul23;
			$total_volume_pasir_jul23 = $total_volume_pasir_125_jul23 + $total_volume_pasir_225_jul23 + $total_volume_pasir_250_jul23 + $total_volume_pasir_250_2_jul23;
			$total_volume_batu1020_jul23 = $total_volume_batu1020_125_jul23 + $total_volume_batu1020_225_jul23 + $total_volume_batu1020_250_jul23 + $total_volume_batu1020_250_2_jul23;
			$total_volume_batu2030_jul23 = $total_volume_batu2030_125_jul23 + $total_volume_batu2030_225_jul23 + $total_volume_batu2030_250_jul23 + $total_volume_batu2030_250_2_jul23;
			
			//SOLAR
			$rap_solar = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where('rap.status','PUBLISH')
			->order_by('rap.id','desc')->limit(1)
			->get()->row_array();

			$total_volume_solar_nov = $total_november_volume * $rap_solar['vol_bbm_solar'];
			$total_volume_solar_des = $total_desember_volume * $rap_solar['vol_bbm_solar'];
			$total_volume_solar_jan23 = $total_januari23_volume * $rap_solar['vol_bbm_solar'];
			$total_volume_solar_feb23 = $total_januari23_volume * $rap_solar['vol_bbm_solar'];
			$total_volume_solar_mar23 = $total_januari23_volume * $rap_solar['vol_bbm_solar'];
			$total_volume_solar_apr23 = $total_januari23_volume * $rap_solar['vol_bbm_solar'];
			$total_volume_solar_mei23 = $total_januari23_volume * $rap_solar['vol_bbm_solar'];
			$total_volume_solar_jun23 = $total_januari23_volume * $rap_solar['vol_bbm_solar'];
			$total_volume_solar_jul23 = $total_januari23_volume * $rap_solar['vol_bbm_solar'];
			?>

			<tr class="table-judul">
				<th width="5%" class="text-center">NO.</th>
				<th class="text-center">URAIAN</th>
				<th class="text-center">SATUAN</th>
				<th class="text-center">NOVEMBER 2022</th>
				<th class="text-center">DESEMBER 2022</th>
				<th class="text-center">JANUARI 2023</th>
				<th class="text-center">FEBRUARI 2023</th>
				<th class="text-center">MARET 2023</th>
				<th class="text-center">APRIL 2023</th>
				<th class="text-center">MEI 2023</th>
				<th class="text-center">JUNI 2023</th>
				<th class="text-center">JULI 2023</th>
	        </tr>
			<tr class="table-baris">
				<th class="text-center">1</th>
				<th class="text-left">Beton K 125 (10±2)</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_november_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_desember_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_januari23_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_februari23_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_maret23_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_april23_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_mei23_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_juni23_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_juli23_produk_a,2,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th class="text-center">2</th>
				<th class="text-left">Beton K 225 (10±2)</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_november_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_desember_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_januari23_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_februari23_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_maret23_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_april23_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_mei23_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_juni23_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_juli23_produk_b,2,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th class="text-center">3</th>
				<th class="text-left">Beton K 250 (10±2)</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_november_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_desember_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_januari23_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_februari23_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_maret23_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_april23_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_mei23_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_juni23_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_juli23_produk_c,2,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th class="text-center">4</th>
				<th class="text-left">Beton K 250 (18±2)</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_november_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_desember_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_januari23_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_februari23_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_maret23_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_april23_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_mei23_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_juni23_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_juli23_produk_d,2,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th class="text-right" colspan="3">TOTAL VOLUME</th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_kebutuhan_bahan") ?>"><?php echo number_format($total_november_volume,2,',','.');?></a></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_kebutuhan_bahan_2") ?>"><?php echo number_format($total_desember_volume,2,',','.');?></a></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_kebutuhan_bahan_3") ?>"><?php echo number_format($total_januari23_volume,2,',','.');?></a></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_kebutuhan_bahan_4") ?>"><?php echo number_format($total_februari23_volume,2,',','.');?></a></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_kebutuhan_bahan_5") ?>"><?php echo number_format($total_maret23_volume,2,',','.');?></a></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_kebutuhan_bahan_6") ?>"><?php echo number_format($total_april23_volume,2,',','.');?></a></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_kebutuhan_bahan_7") ?>"><?php echo number_format($total_mei23_volume,2,',','.');?></a></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_kebutuhan_bahan_8") ?>"><?php echo number_format($total_juni23_volume,2,',','.');?></a></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_kebutuhan_bahan_9") ?>"><?php echo number_format($total_juli23_volume,2,',','.');?></a></th>
			</tr>
			<tr class="table-total">
				<th class="text-right" colspan="3">PENDAPATAN USAHA</th>
				<th class="text-right"><?php echo number_format($nilai_jual_all_november,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($nilai_jual_all_desember,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($nilai_jual_all_januari23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($nilai_jual_all_februari23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($nilai_jual_all_maret23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($nilai_jual_all_april23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($nilai_jual_all_mei23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($nilai_jual_all_juni23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($nilai_jual_all_juli23,2,',','.');?></th>
			</tr>
			<tr class="table-judul">
				<th width="5%" class="text-center" style="vertical-align:middle">NO.</th>
				<th class="text-center" style="vertical-align:middle">KEBUTUHAN BAHAN</th>
				<th class="text-center" style="vertical-align:middle">SATUAN</th>
				<th class="text-center" style="vertical-align:middle">PENGADAAN</th>
				<th class="text-center" style="vertical-align:middle">PENGADAAN</th>
				<th class="text-center" style="vertical-align:middle">PENGADAAN</th>
				<th class="text-center" style="vertical-align:middle">PENGADAAN</th>
				<th class="text-center" style="vertical-align:middle">PENGADAAN</th>
				<th class="text-center" style="vertical-align:middle">PENGADAAN</th>
				<th class="text-center" style="vertical-align:middle">PENGADAAN</th>
				<th class="text-center" style="vertical-align:middle">PENGADAAN</th>
				<th class="text-center" style="vertical-align:middle">PENGADAAN</th>
	        </tr>
			<tr class="table-baris">
				<th class="text-right" colspan="2">Semen</th>
				<th class="text-center">Ton</th>
				<th class="text-right"><?php echo number_format($total_volume_semen_nov,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_semen_des,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_semen_jan23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_semen_feb23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_semen_mar23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_semen_apr23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_semen_mei23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_semen_jun23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_semen_jul23,2,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th class="text-right" colspan="2">Pasir</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($total_volume_pasir_nov,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_pasir_des,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_pasir_jan23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_pasir_feb23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_pasir_mar23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_pasir_apr23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_pasir_mei23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_pasir_jun23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_pasir_jul23,2,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th class="text-right" colspan="2">Batu Split 10-20</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($total_volume_batu1020_nov,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_batu1020_des,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_batu1020_jan23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_batu1020_feb23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_batu1020_mar23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_batu1020_apr23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_batu1020_mei23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_batu1020_jun23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_batu1020_jul23,2,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th class="text-right" colspan="2">Batu Split 20-30</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($total_volume_batu2030_nov,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_batu2030_des,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_batu2030_jan23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_batu2030_feb23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_batu2030_mar23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_batu2030_apr23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_batu2030_mei23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_batu2030_jun23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_batu2030_jul23,2,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th class="text-right" colspan="2">Solar</th>
				<th class="text-center">Liter</th>
				<th class="text-right"><?php echo number_format($total_volume_solar_nov,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_solar_des,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_solar_jan23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_solar_feb23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_solar_mar23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_solar_apr23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_solar_mei23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_solar_jun23,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_solar_jul23,2,',','.');?></th>
			</tr>
	    </table>
		<?php
	}


}