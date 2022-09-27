<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Productions extends Secure_Controller {

	public function __construct()
	{
        parent::__construct();
        // Your own constructor code
        $this->load->model(array('admin/m_admin','crud_global','m_themes','pages/m_pages','menu/m_menu','admin_access/m_admin_access','DB_model','member_back/m_member_back','m_member','pmm_model','admin/Templates','pmm_finance'));
        $this->load->library('enkrip');
		$this->load->library('filter');
		$this->load->library('waktu');
		$this->load->library('session');
		$this->m_admin->check_login();
	}	

	public function add()
	{	
		$check = $this->m_admin->check_login();
		if($check == true){
			$po_id =  $this->input->get('po_id');
			$data['po_id'] = $po_id;
			$client_id = $this->input->get('client_id');
			$data['client_id'] = $client_id;
			$data['clients'] = $this->db->select('id,nama')->order_by('nama','asc')->get_where('penerima',array('pelanggan'=>1))->result_array();
			$data['komposisi'] = $this->db->select('id, jobs_type')->get_where('pmm_agregat',array('status'=>'PUBLISH'))->result_array();
			$get_data = $this->db->get_where('pmm_sales_po',array('id'=>$po_id,'status'=>'OPEN'))->row_array();
			$data['contract_number'] = $this->db->get_where('pmm_sales_po',array('client_id'=>$get_data['client_id'],'status'=>'OPEN'))->result_array();
			$data['data'] = $get_data;
			$this->load->view('pmm/productions_add',$data);
			
		}else {
			redirect('admin');
		}
	}

	public function table()
	{	
		$data = array();
		$client_id = $this->input->post('client_id');
		$product_id = $this->input->post('product_id');
		$sales_po_id = $this->input->post('salesPo_id');
		$w_date = $this->input->post('filter_date');

		$this->db->select('');
		$this->db->where('status !=','DELETED');
		if (!empty($client_id)) {
			$this->db->where('client_id', $client_id);
		}
		if(!empty($product_id)){
			$this->db->where('product_id',$product_id);
		}
		if(!empty($sales_po_id)){
			$this->db->where('salesPo_id',$sales_po_id);
		}
		if(!empty($w_date)){
			$arr_date = explode(' - ', $w_date);
			$start_date = $arr_date[0];
			$end_date = $arr_date[1];
			$this->db->where('date_production  >=',date('Y-m-d',strtotime($start_date)));	
			$this->db->where('date_production <=',date('Y-m-d',strtotime($end_date)));	
		}
		$this->db->order_by('date_production','desc');
		$this->db->order_by('created_on','desc');
		$query = $this->db->get('pmm_productions');
		if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
				$row['no'] = $key+1;
				$row['status'] = $this->pmm_model->GetStatus($row['status']);
				$row['salesPo_id'] = $this->crud_global->GetField('pmm_sales_po',array('id'=>$row['salesPo_id']),'contract_number');
				$row['product_id'] = $this->crud_global->GetField('produk',array('id'=>$row['product_id']),'nama_produk');
				$row['client_id'] = $this->crud_global->GetField('penerima',array('id'=>$row['client_id']),'nama');
				$row['date_production'] =  date('d F Y',strtotime($row['date_production']));
				$row['volume'] = number_format($row['volume'],2,',','.');
				$row['measure'] = $row['measure'];
				$row['harga_satuan'] = number_format($row['harga_satuan'],0,',','.');
				$row['price'] = number_format($row['price'],0,',','.');
				$row['komposisi_id'] = $this->crud_global->GetField('pmm_agregat',array('id'=>$row['komposisi_id']),'jobs_type');
				$row['surat_jalan'] = '<a href="'.base_url().'uploads/surat_jalan_penjualan/'.$row['surat_jalan'].'" target="_blank">'.$row['surat_jalan'].'</a>';
				$row['actions'] = '<a href="javascript:void(0);" onclick="DeleteData('.$row['id'].')" class="btn btn-danger"><i class="fa fa-close"></i> </a>';
				//$row['actions'] = '<a href="javascript:void(0);" onclick="EditData('.$row['id'].')" class="btn btn-primary"><i class="fa fa-edit"></i> </a><a href="javascript:void(0);" onclick="DeleteData('.$row['id'].')" class="btn btn-danger"><i class="fa fa-close"></i> </a>';
				$data[] = $row;
			}

		}
		echo json_encode(array('data'=>$data));
	}

	public function total_pro()
	{	
		$data = array();
		$client_id = $this->input->post('client_id');
		$product_id = $this->input->post('product_id');
		$w_date = $this->input->post('filter_date');

		$this->db->select('SUM(volume) as total');
		$this->db->where('status !=','DELETED');
		if(!empty($client_id)){
			$this->db->where('client_id',$client_id);
		}
		if(!empty($product_id)){
			$this->db->where('product_id',$product_id);
		}
		if(!empty($w_date)){
			$arr_date = explode(' - ', $w_date);
			$start_date = $arr_date[0];
			$end_date = $arr_date[1];
			$this->db->where('date_production  >=',date('Y-m-d',strtotime($start_date)));	
			$this->db->where('date_production <=',date('Y-m-d',strtotime($end_date)));	
		}
		$this->db->order_by('date_production','desc');
		$query = $this->db->get('pmm_productions');
		if($query->num_rows() > 0){
			$row = $query->row_array();
			$data =  number_format($row['total'],2,',','.');
		}
		echo json_encode(array('data'=>$data));
	}


	function process()
	{
		$output['output'] = false;

		$this->db->trans_start(); # Starting Transaction
		$this->db->trans_strict(FALSE); # See Note 01. If you wish can remove as well 
		$production_id = 0;
		$id = $this->input->post('id');
		$sales_po_id = $this->input->post('po_penjualan');
		$komposisi_id = $this->input->post('komposisi_id');
		$product_id = $this->input->post('product_id');
		$volume = str_replace(',', '.', $this->input->post('volume'));
		$price = $this->pmm_model->GetPriceProductions($sales_po_id,$product_id,$volume);
		$no_production = $this->input->post('no_production');
		
		$surat_jalan = $this->input->post('surat_jalan_val');

		$config['upload_path']          = 'uploads/surat_jalan_penjualan/';
        $config['allowed_types']        = 'jpg|png|jpeg|JPG|PNG|JPEG|pdf';
	   
		$production = $this->db->get_where("pmm_productions",["no_production" => $no_production])->num_rows();

		$this->load->library('upload', $config);
		

		if ($production > 1) {
			$output['output'] = false;
			$output['err'] = 'No. Surat Jalan Telah Terdaftar !!';
		}else{
			if(isset($_FILES["data_lab"])){
				if($_FILES["data_lab"]["error"] == 0) {
					$config['file_name'] = $no_production.'_'.$_FILES["data_lab"]['name'];
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('data_lab'))
					{
							$error = $this->upload->display_errors();
							$file = $error;
							$error_file = true;
					}else{
							$data_file = $this->upload->data();
							$file = $data_file['file_name'];
					}
				}
			}
			
	
			
			if($_FILES["surat_jalan"]["error"] == 0) {
				$config['file_name'] = $no_production.'_'.$_FILES["surat_jalan"]['name'];
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('surat_jalan'))
				{
						$error = $this->upload->display_errors();
						$file = $error;
						$error_file = true;
				}else{
						$data_file = $this->upload->data();
						$surat_jalan = $data_file['file_name'];
				}
			}
	
			$data = array(
				'date_production' => date('Y-m-d',strtotime($this->input->post('date'))),
				'no_production' => $no_production,
				'product_id' => $product_id,
				'tax_id' => $this->input->post('tax_id'),
				'client_id' => $this->input->post('client_id'),
				'no_production' => $this->input->post('no_production'),
				'volume' => $volume,
				'convert_value' => 1,
				'display_volume' => $volume,
				'measure' => $this->input->post('measure'),
				'convert_measure' => $this->input->post('measure'),
				'komposisi_id' => $this->input->post('komposisi_id'),
				'nopol_truck' => $this->input->post('nopol_truck'),
				'driver' => $this->input->post('driver'),
				'lokasi' => $this->input->post('lokasi'),
				'price' => $price,
				'salesPo_id' => $this->input->post('po_penjualan'),
				'status' => 'PUBLISH',
				'status_payment' => 'UNCREATED',
				'surat_jalan' => $surat_jalan,
				'memo' => $this->input->post('memo'),
				'harga_satuan' => $price /  $volume,
				'display_price' => $price,
				'display_harga_satuan' => $price /  $volume,
			);
	
	
			if(empty($id)){
				$data['created_by'] = $this->session->userdata('admin_id');
				$data['created_on'] = date('Y-m-d H:i:s');
				if($this->db->insert('pmm_productions',$data)){
					$production_id = $this->db->insert_id();
					
					// Insert COA
					$coa_description = 'Production Nomor '.$no_production;
					$this->pmm_finance->InsertTransactions(4,$coa_description,$price,0);

	
				}
			}else {
				$data['updated_by'] = $this->session->userdata('admin_id');
				$data['updated_on'] = date('Y-m-d H:i:s');
				$this->db->update('pmm_productions',$data,array('id'=>$id));
	
				$check_pro = $this->db->get_where('pmm_productions',array('id'=>$id,'product_id'=>$product_id))->num_rows();
				if($check_pro == 0){
				}
				
			}
	
			if ($this->db->trans_status() === FALSE) {
				# Something went wrong.
				$this->db->trans_rollback();
				$output['output'] = false;
			} 
			else {
				# Everything is Perfect. 
				# Committing data to the database.
				$this->db->trans_commit();
				$output['id'] = $production_id;
				$output['output'] = true;	
				// $output['no_production'] = $this->pmm_model->ProductionsNo();
			}

			
		}
        

		
		echo json_encode($output);
	}


	public function approve_po()
	{
		$output['output'] = false;
		$id = $this->input->post('id');
		if(!empty($id)){
			$data = array(
				'date_po' => date('Y-m-d',strtotime($this->input->post('date_po'))),
				'subject' => $this->input->post('subject'),
				'date_pkp' => date('Y-m-d',strtotime($this->input->post('date_pkp'))),
				'supplier_id' => $this->input->post('supplier_id'),
				'total' => $this->input->post('total'),
				'approved_by' => $this->session->userdata('admin_id'),
				'approved_on' => date('Y-m-d H:i:s'),
				'status' => 'PUBLISH'
			);
			if($this->db->update('pmm_productions',$data,array('id'=>$id))){
				$output['output'] = true;
				$output['url'] = site_url('admin/productions');
			}
		}
		echo json_encode($output);
	}

	public function get_composition()
	{
		$output['output'] = true;
		$product_id = $this->input->post('product_id');
		if(!empty($product_id)){
			$query = $this->db->select('id, product_id,composition_name as text')->get_where('pmm_composition',array('product_id'=>$product_id,'status'=>'PUBLISH'))->result_array();
			if(!empty($query)){
				$data = array();
				$data[0] = array('id'=>'','text'=>'Pilih Composition');
				foreach ($query as $key => $row) {

					$data[] = array('id'=>$row['id'],'text'=>$row['text']);
				}
				$output['output'] = true;
				$output['data'] = $data;
			}
		}
		echo json_encode($output);
	}

	public function get_po_products()
	{
		$output['output'] = true;
		$id = $this->input->post('id');
		if(!empty($id)){
			$client_id = $this->crud_global->GetField('pmm_sales_po',array('id'=>$id),'client_id');
			$query = $this->db->select('product_id')->get_where('pmm_sales_po_detail',array('sales_po_id'=>$id))->result_array();
			if(!empty($query)){
				$data = array();
				$data[0] = array('id'=>'','text'=>'Pilih Produk');
				foreach ($query as $key => $row) {
					$product_name = $this->crud_global->GetField('produk',array('id'=>$row['product_id']),'nama_produk');
					$data[] = array('id'=>$row['product_id'],'text'=>$product_name);
				}
				$output['products'] = $data;
			}
			$client = array();
			$client_name = $this->crud_global->GetField('penerima',array('id'=>$client_id),'nama');
			$client[0] = array('id'=>$client_id,'text'=>$client_name);
			$output['client'] = $client;
			$output['output'] = true;
		}
		echo json_encode($output);
	}

	public function get_po_penjualan(){

		$response = [
			'output' => true,
			'po' => null
		];

		try {

			$id = $this->input->post('id');

			$this->db->select('psp.id, psp.contract_number, psp.client_id');
			$this->db->from('pmm_sales_po psp');
			$this->db->where('psp.client_id = ' . intval($id));
			$this->db->where('psp.status','OPEN');
			$this->db->group_by('psp.id');
			$query = $this->db->get()->result_array();

			$data = [];
			//$data[0] = ['id'=>'','text'=>'Pilih No. Sales Order'];

			if (!empty($query)){
				foreach ($query as $row){
					$data[] = ['id' => $row['id'], 'text' => $row['contract_number']];
				}
			}

			$response['po'] = $data;

		} catch (Throwable $e){
			$response['output'] = false;
		} finally {
			echo json_encode($response);
		}
			
	}

	public function get_materials(){

		$response = [
			'output' => true,
			'po' => null
		];

		try {

			$id = $this->input->post('id');

			$this->db->select('p.id, p.nama_produk, pspd.measure, pspd.tax_id');
			$this->db->from('produk p ');
			$this->db->join('pmm_sales_po_detail pspd','p.id = pspd.product_id','left');
			$this->db->join('pmm_sales_po psp ','pspd.sales_po_id = psp.id','left');
			$this->db->where("psp.id = " . intval($id));
			$query = $this->db->get()->result_array();

			$data = [];
			//$data[0] = ['id'=>'','text'=>'Pilih Produk'];

			if (!empty($query)){
				foreach ($query as $row){
					$data[] = ['id' => $row['id'], 'text' => $row['nama_produk']];
					$tax_id[] = ['id' => $row['id'], 'text' => $row['tax_id']];
					$measure[] = ['id' => $row['id'], 'text' => $row['measure']];
				}
			}

			$response['products'] = $data;
			$response['measure'] = $measure;
			$response['tax_id'] = $tax_id;

		} catch (Throwable $e){
			$response['output'] = false;
		} finally {
			echo json_encode($response);
		}
			
	}

	public function get_pdf()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        // $pdf->set_header_title('Laporan'
		// $pdf->set_nsi_header(FALSE);
        $pdf->setPrintHeader(false);
        $pdf->SetTopMargin(0);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
        $pdf->AddPage('P');

        $id = $this->uri->segment(4);

		$row = $this->db->get_where('pmm_productions',array('id'=>$id))->row_array();
		$row['product'] = $this->crud_global->GetField('pmm_product',array('id'=>$row['product_id']),'product');
		$row['client'] = $this->crud_global->GetField('pmm_client',array('id'=>$row['client_id']),'client_name');
		$data['row'] = $row;
		$data['id'] = $id;
        $html = $this->load->view('pmm/productions_pdf',$data,TRUE);

        
        $pdf->SetTitle($row['no_production']);
        $pdf->nsi_html($html);
        $pdf->Output($row['no_production'].'.pdf', 'I');
	
	}

	public function delete()
	{
		$output['output'] = false;
		$id = $this->input->post('id');
		
		$this->db->delete('pmm_productions',array('id'=>$id));
		$output['output'] = true;
			
		
		echo json_encode($output);
	}

	public function table_dashboard()
	{
		$data = $this->pmm_model->DashboardProductions();

		echo json_encode(array('data'=>$data));
	}

	
	public function table_dashboard_mu()
	{
		$data = array();
		$arr_date = explode(' - ', $this->input->post('date'));
		$material = $this->input->post('material');

		$this->db->select('pm.material_name,pms.measure_name,pm.id');
		$this->db->join('pmm_measures pms','pm.measure = pms.id','left');
		if(!empty($material)){
			$this->db->where('pm.id',$material);
		}	
		$this->db->group_by('pm.id');
		$query = $this->db->get('pmm_materials pm');
		if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {

				$this->db->select('SUM(pp.volume) as volume,ppm.koef');
				$this->db->join('pmm_productions pp','ppm.production_id = pp.id');
				if(!empty($arr_date)){
					$this->db->where('pp.date_production >=',date('Y-m-d',strtotime($arr_date[0])));
					$this->db->where('pp.date_production <=',date('Y-m-d',strtotime($arr_date[1])));
				}
				$this->db->where('pp.status','PUBLISH');
				$this->db->where('ppm.material_id',$row['id']);
				$get_volume = $this->db->get('pmm_production_material ppm')->row_array();

				$row['no'] = $key+1;
				$row['material_name'] = $row['material_name'].' <b>('.$row['measure_name'].')</b>';
				$total = $get_volume['volume'] * $get_volume['koef'];
				
				$total_pakai = $this->pmm_model->GetTotalSisa($row['id'],$arr_date[0]);
				$row['total'] = number_format($total_pakai - $total,2,',','.');
		  //       $pemakaian = $total_pakai * $row['koef'];
		        // $row['total'] = $pemakaian;
				$data[] = $row;
			}
		}

		echo json_encode(array('data'=>$data,'a'=>$arr_date));
	}


	function table_date()
	{
		$data = array();
		$client_id = $this->input->post('client_id');
		$filter_product = $this->input->post('filter_product');
		$start_date = false;
		$end_date = false;
		$date = $this->input->post('filter_date');
		$date_text = '';
		if(!empty($date)){
			$arr_date = explode(' - ',$date);
			$start_date = date('Y-m-d',strtotime($arr_date[0]));
			$end_date = date('Y-m-d',strtotime($arr_date[1]));

			$date_text = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));
		}

		$arr_filter_prods = array();
		if(!empty($filter_product)){
			$query_mats = $this->db->select('id')->get_where('pmm_product',array('status'=>'PUBLISH','tag_id'=>$filter_product))->result_array();
			foreach ($query_mats as $key => $row) {
				$arr_filter_prods[] = $row['id'];
			}
		}

		// $products = $this->db->select('id,product')->get_where('pmm_product',array('status'=>'PUBLISH'))->result_array();
		$total_real = 0;
		$total_cost = 0;
		$no =1;


		$this->db->select('pc.client_name,pp.client_id, SUM(volume) as total, SUM(price) as cost');
		$this->db->join('pmm_client pc','pp.client_id = pc.id','left');
		if(!empty($start_date) && !empty($end_date)){
            $this->db->where('pp.date_production >=',$start_date);
            $this->db->where('pp.date_production <=',$end_date);
        }
        if(!empty($client_id)){
        	$this->db->where('pp.client_id',$client_id);
        }
        if(!empty($arr_filter_prods)){
        	$this->db->where_in('pp.product_id',$arr_filter_prods);
        }
		$this->db->where('pc.status','PUBLISH');
		$this->db->where('pp.status','PUBLISH');
		$this->db->group_by('pp.client_id');
		$clients = $this->db->get('pmm_productions pp')->result_array();
		if(!empty($clients)){
			foreach ($clients as $key => $row) {

				$this->db->select('SUM(pp.volume) as total, SUM(pp.price) as cost, pc.product');
		        $this->db->join('pmm_product pc','pp.product_id = pc.id','left');
		        if(!empty($start_date) && !empty($end_date)){
		            $this->db->where('pp.date_production >=',$start_date);
		            $this->db->where('pp.date_production <=',$end_date);
		        }
		        if(!empty($client_id)){
		            $this->db->where('pp.client_id',$client_id);
		        }
		        if(!empty($arr_filter_prods)){
		        	$this->db->where_in('pp.product_id',$arr_filter_prods);
		        }
		        $this->db->where('pp.client_id',$row['client_id']);
		        $this->db->where('pp.status','PUBLISH');
		        $this->db->where('pc.status','PUBLISH');
		        $this->db->group_by('pp.product_id');
		        $arr_products = $this->db->get_where('pmm_productions pp')->result_array();


				$arr['no'] = $no;
				$arr['products'] = $arr_products;
				$arr['total'] = $row['total'];
				$arr['cost'] = $row['cost'];
				$arr['client'] = $row['client_name'];
				$total_real += $row['total'];
				$total_cost += $row['cost'];
				$data[] = $arr;
				$no++;
			}
		}

		// foreach ($products as $key => $row) {
		// 	$get_real = $this->pmm_model->GetRealProd($row['id'],$start_date,$end_date,$client_id);
		// 	if($get_real['total'] > 0){
		// 		$arr_clients = $this->pmm_model->GetRealProdByClient($row['id'],$start_date,$end_date,$client_id);
		// 		
		// 	}
			
		// }

		echo json_encode(array('data'=>$data,'date_text'=> $date_text,'total_real'=>$total_real,'total_cost'=>$total_cost));	
	}

	function table_date2()
	{
		$data = array();
		$client_id = $this->input->post('client_id');
		$filter_product = $this->input->post('filter_product');
		$start_date = false;
		$end_date = false;
		$date = $this->input->post('filter_date');
		$date_text = '';
		if(!empty($date)){
			$arr_date = explode(' - ',$date);
			$start_date = date('Y-m-d',strtotime($arr_date[0]));
			$end_date = date('Y-m-d',strtotime($arr_date[1]));

			$date_text = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));
		}
		
		$total_real = 0;
		$total_cost = 0;
		$no =1;


		$this->db->select('pc.nama,pp.client_id, SUM(volume) as total, SUM(price) as cost');
		$this->db->join('penerima pc','pp.client_id = pc.id and pc.pelanggan = 1','left');
		if(!empty($start_date) && !empty($end_date)){
            $this->db->where('pp.date_production >=',$start_date);
            $this->db->where('pp.date_production <=',$end_date);
        }
        if(!empty($client_id)){
        	$this->db->where('pp.client_id',$client_id);
        }
        if(!empty($filter_product)){
        	$this->db->where_in('pp.product_id',$filter_product);
        }
		$this->db->where('pc.status','PUBLISH');
		$this->db->where('pp.status','PUBLISH');
		$this->db->group_by('pp.client_id');
		$clients = $this->db->get('pmm_productions pp')->result_array();	
		
		if(!empty($clients)){
			foreach ($clients as $key => $row) {

				$this->db->select('SUM(pp.volume) as total, SUM(pp.price) / SUM(pp.volume) as price, SUM(pp.volume) * SUM(pp.price) / SUM(pp.volume) as cost, pc.nama_produk as product, pp.measure');
		        $this->db->join('produk pc','pp.product_id = pc.id','left');
				$this->db->join('pmm_sales_po po','pp.salesPo_id = po.id');
				//$this->db->join('pmm_sales_po_detail pod','po.id = pod.sales_po_id');
		        if(!empty($start_date) && !empty($end_date)){
		            $this->db->where('pp.date_production >=',$start_date);
		            $this->db->where('pp.date_production <=',$end_date);
		        }
		        if(!empty($client_id)){
		            $this->db->where('pp.client_id',$client_id);
		        }
		        if(!empty($filter_product)){
		        	$this->db->where_in('pp.product_id',$filter_product);
		        }
		        $this->db->where('pp.client_id',$row['client_id']);
		        $this->db->where('pp.status','PUBLISH');
		        $this->db->where('pc.status','PUBLISH');
		        $this->db->group_by('pp.product_id');
		        $arr_products = $this->db->get_where('pmm_productions pp')->result_array();
				

				$arr['no'] = $no;
				$arr['products'] = $arr_products;
				$arr['total'] = $row['total'];
				$arr['cost'] = $row['cost'];
				$arr['client'] = $row['nama'];
				$total_real += $row['total'];
				$total_cost += $row['cost'];
				$data[] = $arr;
				$no++;
			}
		}


		echo json_encode(array('data'=>$data,'date_text'=> $date_text,'total_real'=>$total_real,'total_cost'=>$total_cost));	
	}


	public function edit_data_detail()
	{
		$id = $this->input->post('id');

		$data = $this->db->get_where('pmm_productions prm',array('prm.id'=>$id))->row_array();
		$data['date_production'] = date('d-m-Y',strtotime($data['date_production']));
		echo json_encode(array('data'=>$data));		
	}

	public function print_pdf()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetTopMargin(5);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('L');

		$w_date = $this->input->get('filter_date');
		$product_id = $this->input->get('product_id');
		$client_id = $this->input->get('client_id');
		$salesPo_id = $this->input->get('salesPo_id');
		$filter_date = false;


		$this->db->select('pp.*,pc.nama,ppr.product');
		if(!empty($client_id)){
			$this->db->where('pp.client_id',$client_id);
		}
		if(!empty($product_id) || $product_id != 0){
			$this->db->where('pp.product_id',$product_id);
		}
		if(!empty($salesPo_id) || $salesPo_id != 0){
			$this->db->where('pp.salesPo_id',$salesPo_id);
		}
		if(!empty($w_date)){
			$arr_date = explode(' - ', $w_date);
			$start_date = $arr_date[0];
			$end_date = $arr_date[1];
			$this->db->where('pp.date_production  >=',date('Y-m-d',strtotime($start_date)));	
			$this->db->where('pp.date_production <=',date('Y-m-d',strtotime($end_date)));	
			$filter_date = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));
		}
		$this->db->join('pmm_product ppr','pp.product_id = ppr.id','left');
		$this->db->join('penerima pc','pp.client_id = pc.id','left');
		$this->db->order_by('pp.date_production','asc');
		$this->db->order_by('pp.created_on','asc');
		$this->db->group_by('pp.id');
		$query = $this->db->get('pmm_productions pp');
		

		$data['data'] = $query->result_array();
		$data['filter_date'] = $filter_date;
        $html = $this->load->view('pmm/productions_print',$data,TRUE);

        
        $pdf->SetTitle('rekap_surat_jalan_penjualan');
        $pdf->nsi_html($html);
        $pdf->Output('rekap_surat_jalan_penjualan.pdf', 'I');
	
	}

	function post_price()
	{
		$this->db->where('product_id !=',5);
		$arr = $this->db->get('pmm_productions');
		foreach ($arr->result_array() as $row) {
			$contract_price = $this->crud_global->GetField('pmm_product',array('id'=>$row['product_id']),'contract_price');
			$price = $row['volume'] * $contract_price;
			$this->db->update('pmm_productions',array('price'=>$price),array('id'=>$row['id']));
		}
	}
	
	//BATAS RUMUS LAMA//
	
	function pengiriman_penjualan()
	{
		$data = array();
		$filter_client_id = $this->input->post('filter_client_id');
		$purchase_order_no = $this->input->post('salesPo_id');
		$filter_product = $this->input->post('filter_product');
		$start_date = false;
		$end_date = false;
		$total_nilai = 0;
		$total_volume = 0;
		$date = $this->input->post('filter_date');
		if(!empty($date)){
			$arr_date = explode(' - ',$date);
			$start_date = date('Y-m-d',strtotime($arr_date[0]));
			$end_date = date('Y-m-d',strtotime($arr_date[1]));
		}

		$this->db->select('ppo.client_id, pp.convert_measure as convert_measure, ps.nama as name, SUM(pp.display_price) / SUM(pp.display_volume) as price, SUM(pp.display_volume) as total, SUM(pp.display_price) as total_price');
		if(!empty($start_date) && !empty($end_date)){
            $this->db->where('pp.date_production >=',$start_date);
            $this->db->where('pp.date_production <=',$end_date);
        }
        if(!empty($filter_client_id)){
            $this->db->where('ppo.client_id',$filter_client_id);
        }
        if(!empty($filter_product)){
            $this->db->where_in('pp.product_id',$filter_product);
        }
        if(!empty($purchase_order_no)){
            $this->db->where('pp.salesPo_id',$purchase_order_no);
        }
		
		
		$this->db->join('penerima ps','ppo.client_id = ps.id','left');
		$this->db->join('pmm_productions pp','ppo.id = pp.salesPo_id','left');
		$this->db->where("ppo.status in ('OPEN','CLOSED')");
		$this->db->where('pp.status','PUBLISH');
		$this->db->group_by('ppo.client_id');
		$query = $this->db->get('pmm_sales_po ppo');
		
		$no = 1;
		if($query->num_rows() > 0){

			foreach ($query->result_array() as $key => $sups) {

				$mats = array();
				$materials = $this->pmm_model->GetPengirimanPenjualan($sups['client_id'],$purchase_order_no,$start_date,$end_date,$filter_product);
				if(!empty($materials)){
					foreach ($materials as $key => $row) {
						$arr['no'] = $key + 1;
						$arr['measure'] = $row['measure'];
						$arr['nama_produk'] = $row['nama_produk'];
						$arr['salesPo_id'] = '<a href="'.base_url().'penjualan/dataSalesPO/'.$row['salesPo_id'].'" target="_blank">'.$row['salesPo_id'] = $this->crud_global->GetField('pmm_sales_po',array('id'=>$row['salesPo_id']),'contract_number').'</a>';
						$arr['real'] = number_format($row['total'],2,',','.');
						$arr['price'] = number_format($row['price'],0,',','.');
						$arr['total_price'] = number_format($row['total_price'],0,',','.');
						
						
						$arr['name'] = $sups['name'];
						$mats[] = $arr;
					}
					$sups['mats'] = $mats;
					$total_volume += $sups['total'];
					$total_nilai += $sups['total_price'];
					$sups['no'] = $no;
					$sups['real'] = number_format($sups['total'],2,',','.');
					$sups['price'] = number_format($sups['price'],0,',','.');
					$sups['total_price'] = number_format($sups['total_price'],0,',','.');
					
					$data[] = $sups;
					$no++;
				}	
				
			}
		}

		echo json_encode(array('data'=>$data,
		'total_volume'=>number_format($total_volume,2,',','.'),
		'total_nilai'=>number_format($total_nilai,0,',','.')
	));		
	}

	public function laporan_piutang($arr_date)
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
			<tr class="table-active4">
				<th width="5%" class="text-center" rowspan="2" style="vertical-align:middle;">NO.</th>
				<th width="30%" class="text-center" rowspan="2" style="vertical-align:middle;">URAIAN</th>
				<th width="10%" class="text-center" rowspan="2" style="vertical-align:middle;">PENERIMAAN</th>
				<th width="10%" class="text-center" rowspan="2" style="vertical-align:middle;">TAGIHAN</th>
				<th width="10%" class="text-center" rowspan="2" style="vertical-align:middle;">TAGIHAN BRUTO</th>
				<th width="10%" class="text-center" rowspan="2" style="vertical-align:middle;">PEMBAYARAN</th>
				<th width="20%" class="text-center" colspan="2">SISA HUTANG</th>
				<th width="5%" class="text-center" rowspan="2" style="vertical-align:middle;">KET.</th>
	        </tr>
			<tr class="table-active4">
				<th class="text-center">PENERIMAAN</th>
				<th class="text-center">INVOICE</th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center">1</th>			
				<th class="text-left" colspan="8">MATERIAL / BAHAN</th>
	        </tr>
			<?php
			$penerima_1 = $this->db->select('nama')
			->from('penerima')
			->where("id = 3")
			->get()->row_array();
			?>
			<tr class="table-active3">
				<th class="text-center"></th>			
				<th class="text-left" colspan="8">1.1 <?= $penerima_1['nama'];?></th>
	        </tr>
			<?php
			$penerimaan_batu1020_alamindah = $this->db->select('SUM(prm.price) as total')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order ppo', 'prm.purchase_order_id = ppo.id','left')
			->where("prm.date_receipt <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppo.supplier_id = 3")
			->where("prm.material_id = 6")
			->get()->row_array();

			$penerimaan_batu2030_alamindah = $this->db->select('SUM(prm.price) as total')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order ppo', 'prm.purchase_order_id = ppo.id','left')
			->where("prm.date_receipt <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppo.supplier_id = 3")
			->where("prm.material_id = 7")
			->get()->row_array();

			$penerimaan_pasir_alamindah = $this->db->select('SUM(prm.price) as total')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order ppo', 'prm.purchase_order_id = ppo.id','left')
			->where("prm.date_receipt <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppo.supplier_id = 3")
			->where("prm.material_id = 5")
			->get()->row_array();

			$penerimaan_jasa_angkut_alamindah = $this->db->select('SUM(prm.price) as total')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order ppo', 'prm.purchase_order_id = ppo.id','left')
			->where("prm.date_receipt <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppo.supplier_id = 3")
			->where("prm.material_id = 22")
			->get()->row_array();

			$jumlah_penerimaan_alamindah = $penerimaan_batu1020_alamindah['total'] + $penerimaan_batu2030_alamindah['total'] + $penerimaan_pasir_alamindah['total'] + $penerimaan_jasa_angkut_alamindah['total'];
			?>

			<?php
			$tagihan_batu1020_alamindah = $this->db->select('SUM(ppd.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 3")
			->where("ppd.material_id = 6")
			->get()->row_array();

			$tagihan_batu2030_alamindah = $this->db->select('SUM(ppd.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 3")
			->where("ppd.material_id = 7")
			->get()->row_array();

			$tagihan_pasir_alamindah = $this->db->select('SUM(ppd.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 3")
			->where("ppd.material_id = 5")
			->get()->row_array();

			$tagihan_jasa_angkut_alamindah = $this->db->select('SUM(ppd.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 3")
			->where("ppd.material_id = 22")
			->get()->row_array();

			$jumlah_tagihan_alamindah = $tagihan_batu1020_alamindah['total'] + $tagihan_batu2030_alamindah['total'] + $tagihan_pasir_alamindah['total'] + $tagihan_jasa_angkut_alamindah['total'];
			?>

			<?php
			$pembayaran_batu1020_alamindah = $this->db->select('SUM(pppp.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_pembayaran_penagihan_pembelian pppp', 'ppp.id = pppp.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 3")
			->where("ppd.material_id = 6")
			->where("pppp.memo <> 'PPN' ")
			->get()->row_array();

			$pembayaran_batu2030_alamindah = $this->db->select('SUM(pppp.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_pembayaran_penagihan_pembelian pppp', 'ppp.id = pppp.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 3")
			->where("ppd.material_id = 7")
			->where("pppp.memo <> 'PPN' ")
			->get()->row_array();

			$pembayaran_pasir_alamindah = $this->db->select('SUM(pppp.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_pembayaran_penagihan_pembelian pppp', 'ppp.id = pppp.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 3")
			->where("ppd.material_id = 5")
			->where("pppp.memo <> 'PPN' ")
			->get()->row_array();

			$pembayaran_jasa_angkut_alamindah = $this->db->select('SUM(pppp.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_pembayaran_penagihan_pembelian pppp', 'ppp.id = pppp.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 3")
			->where("ppd.material_id = 22")
			->where("pppp.memo <> 'PPN' ")
			->get()->row_array();

			$jumlah_pembayaran_alamindah = $pembayaran_batu1020_alamindah['total'] + $pembayaran_batu2030_alamindah['total'] + $pembayaran_pasir_alamindah['total'] + $pembayaran_jasa_angkut_alamindah['total'];
			?>

			<?php
			$tagihan_bruto_batu1020_alamindah = $penerimaan_batu1020_alamindah['total'] - $tagihan_batu1020_alamindah['total'];
			$tagihan_bruto_batu2030_alamindah = $penerimaan_batu2030_alamindah['total'] - $tagihan_batu2030_alamindah['total'];
			$tagihan_bruto_pasir_alamindah = $penerimaan_pasir_alamindah['total'] - $tagihan_pasir_alamindah['total'];
			$tagihan_bruto_jasa_angkut_alamindah = $penerimaan_jasa_angkut_alamindah['total'] - $tagihan_jasa_angkut_alamindah['total'];

			$jumlah_tagihan_bruto_alamindah = $tagihan_bruto_batu1020_alamindah + $tagihan_bruto_batu2030_alamindah +  $tagihan_bruto_pasir_alamindah + $tagihan_bruto_jasa_angkut_alamindah;
			?>

			<?php

			$hutang_penerimaan_batu1020_alamindah = $penerimaan_batu1020_alamindah['total'] - $pembayaran_batu1020_alamindah['total'];
			$hutang_penerimaan_batu2030_alamindah = $penerimaan_batu2030_alamindah['total'] - $pembayaran_batu2030_alamindah['total'];
			$hutang_penerimaan_pasir_alamindah = $penerimaan_pasir_alamindah['total'] - $pembayaran_pasir_alamindah['total'];
			$hutang_penerimaan_jasa_angkut_alamindah = $penerimaan_jasa_angkut_alamindah['total'] - $pembayaran_jasa_angkut_alamindah['total'];

			$jumlah_hutang_penerimaan_alamindah = $hutang_penerimaan_batu1020_alamindah + $hutang_penerimaan_batu2030_alamindah +  $hutang_penerimaan_pasir_alamindah + $hutang_penerimaan_jasa_angkut_alamindah;
			?>

			<?php

			$hutang_tagihan_batu1020_alamindah = $tagihan_batu1020_alamindah['total'] - $pembayaran_batu1020_alamindah['total'];
			$hutang_tagihan_batu2030_alamindah = $tagihan_batu2030_alamindah['total'] - $pembayaran_batu2030_alamindah['total'];
			$hutang_tagihan_pasir_alamindah = $tagihan_pasir_alamindah['total'] - $pembayaran_pasir_alamindah['total'];
			$hutang_tagihan_jasa_angkut_alamindah = $tagihan_jasa_angkut_alamindah['total'] - $pembayaran_jasa_angkut_alamindah['total'];

			$jumlah_hutang_tagihan_alamindah = $hutang_tagihan_batu1020_alamindah + $hutang_tagihan_batu2030_alamindah +  $hutang_tagihan_pasir_alamindah + $hutang_tagihan_jasa_angkut_alamindah;
			?>
			<tr class="table-active3">
				<th class="text-center"></th>			
				<th class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Batu Split 10-20</th>
				<th class="text-right"><?php echo number_format($penerimaan_batu1020_alamindah['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_batu1020_alamindah['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_bruto_batu1020_alamindah,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pembayaran_batu1020_alamindah['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_penerimaan_batu1020_alamindah,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_tagihan_batu1020_alamindah,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center"></th>			
				<th class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Batu Split 20-30</th>
				<th class="text-right"><?php echo number_format($penerimaan_batu2030_alamindah['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_batu2030_alamindah['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_bruto_batu2030_alamindah,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pembayaran_batu2030_alamindah['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_penerimaan_batu2030_alamindah,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_tagihan_batu2030_alamindah,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center"></th>			
				<th class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pasir</th>
				<th class="text-right"><?php echo number_format($penerimaan_pasir_alamindah['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_pasir_alamindah['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_bruto_pasir_alamindah,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pembayaran_pasir_alamindah['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_penerimaan_pasir_alamindah,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_tagihan_pasir_alamindah,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center"></th>			
				<th class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jasa Angkut</th>
				<th class="text-right"><?php echo number_format($penerimaan_jasa_angkut_alamindah['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_jasa_angkut_alamindah['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_bruto_jasa_angkut_alamindah,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pembayaran_jasa_angkut_alamindah['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_penerimaan_jasa_angkut_alamindah,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_tagihan_jasa_angkut_alamindah,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			<tr class="table-active2">
				<th class="text-center"></th>			
				<th class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jumlah</th>
				<th class="text-right"><?php echo number_format($jumlah_penerimaan_alamindah,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_tagihan_alamindah,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_tagihan_bruto_alamindah,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pembayaran_alamindah,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_hutang_penerimaan_alamindah,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_hutang_tagihan_alamindah,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			<?php
			$penerima_2 = $this->db->select('nama')
			->from('penerima')
			->where("id = 4")
			->get()->row_array();
			?>
			<tr class="table-active3">
				<th class="text-center"></th>			
				<th class="text-left" colspan="8">1.2 <?= $penerima_2['nama'];?></th>
	        </tr>
			<?php
			$penerimaan_semen_cons_kupang = $this->db->select('SUM(prm.price) as total')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order ppo', 'prm.purchase_order_id = ppo.id','left')
			->where("prm.date_receipt <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppo.supplier_id = 4")
			->where("prm.material_id = 19")
			->get()->row_array();

			$jumlah_penerimaan_kupang = $penerimaan_semen_cons_kupang['total'];
			?>

			<?php
			$tagihan_semen_cons_kupang = $this->db->select('SUM(ppd.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 4")
			->where("ppd.material_id = 19")
			->get()->row_array();

			$jumlah_tagihan_kupang = $tagihan_semen_cons_kupang['total'];
			?>

			<?php
			$pembayaran_semen_cons_kupang = $this->db->select('SUM(pppp.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_pembayaran_penagihan_pembelian pppp', 'ppp.id = pppp.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 4")
			->where("ppd.material_id = 19")
			->where("pppp.memo <> 'PPN' ")
			->get()->row_array();

			$jumlah_pembayaran_kupang = $pembayaran_semen_cons_kupang['total'];
			?>

			<?php
			$tagihan_bruto_semen_cons_kupang = $tagihan_semen_cons_kupang['total'] - $tagihan_semen_cons_kupang['total'];
			
			$jumlah_tagihan_bruto_kupang = $tagihan_bruto_semen_cons_kupang;
			?>

			<?php
			$hutang_penerimaan_semen_cons_kupang = $penerimaan_semen_cons_kupang['total'] - $pembayaran_semen_cons_kupang['total'];

			$jumlah_hutang_penerimaan_kupang = $hutang_penerimaan_semen_cons_kupang;
			?>

			<?php
			$hutang_tagihan_semen_cons_kupang = $tagihan_semen_cons_kupang['total'] - $pembayaran_semen_cons_kupang['total'];

			$jumlah_hutang_tagihan_kupang = $hutang_tagihan_semen_cons_kupang;
			?>
			<tr class="table-active3">
				<th class="text-center"></th>			
				<th class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Semen Cons</th>
				<th class="text-right"><?php echo number_format($penerimaan_semen_cons_kupang['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_semen_cons_kupang['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_bruto_semen_cons_kupang,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pembayaran_semen_cons_kupang['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_penerimaan_semen_cons_kupang,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_tagihan_semen_cons_kupang,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			<tr class="table-active2">
				<th class="text-center"></th>			
				<th class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jumlah</th>
				<th class="text-right"><?php echo number_format($jumlah_penerimaan_kupang,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_tagihan_kupang,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_tagihan_bruto_kupang,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pembayaran_kupang,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_hutang_penerimaan_kupang,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_hutang_tagihan_kupang,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			<?php
			$penerima_3 = $this->db->select('nama')
			->from('penerima')
			->where("id = 7")
			->get()->row_array();
			?>
			<tr class="table-active3">
				<th class="text-center"></th>			
				<th class="text-left" colspan="8">1.3 <?= $penerima_3['nama'];?></th>
	        </tr>
			<?php
			$penerimaan_solar_langit = $this->db->select('SUM(prm.price) as total')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order ppo', 'prm.purchase_order_id = ppo.id','left')
			->where("prm.date_receipt <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppo.supplier_id = 7")
			->where("prm.material_id = 8")
			->get()->row_array();

			$jumlah_penerimaan_langit = $penerimaan_solar_langit['total'];
			?>

			<?php
			$tagihan_solar_langit = $this->db->select('SUM(ppd.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 7")
			->where("ppd.material_id = 8")
			->get()->row_array();

			$jumlah_tagihan_langit = $tagihan_solar_langit['total'];
			?>

			<?php
			$pembayaran_solar_langit = $this->db->select('SUM(pppp.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_pembayaran_penagihan_pembelian pppp', 'ppp.id = pppp.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 7")
			->where("ppd.material_id = 8")
			->where("pppp.memo <> 'PPN' ")
			->get()->row_array();

			$jumlah_pembayaran_langit = $pembayaran_solar_langit['total'];
			?>

			<?php
			$tagihan_bruto_solar_langit = $penerimaan_solar_langit['total'] - $tagihan_solar_langit['total'];

			$jumlah_tagihan_bruto_langit = $tagihan_bruto_solar_langit;
			?>

			<?php
			$hutang_penerimaan_solar_langit = $penerimaan_solar_langit['total'] - $pembayaran_solar_langit['total'];

			$jumlah_hutang_penerimaan_langit = $hutang_penerimaan_solar_langit;
			?>

			<?php
			$hutang_tagihan_solar_langit = $tagihan_solar_langit['total'] - $pembayaran_solar_langit['total'];

			$jumlah_hutang_tagihan_langit = $hutang_tagihan_solar_langit;
			?>
			<tr class="table-active3">
				<th class="text-center"></th>			
				<th class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Solar</th>
				<th class="text-right"><?php echo number_format($penerimaan_solar_langit['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_solar_langit['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_bruto_solar_langit,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pembayaran_solar_langit['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_penerimaan_solar_langit,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_tagihan_solar_langit,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			<tr class="table-active2">
				<th class="text-center"></th>			
				<th class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jumlah</th>
				<th class="text-right"><?php echo number_format($jumlah_penerimaan_langit,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_tagihan_langit,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_tagihan_bruto_langit,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pembayaran_langit,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_hutang_penerimaan_langit,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_hutang_tagihan_langit,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			<?php
			$penerima_4 = $this->db->select('nama')
			->from('penerima')
			->where("id = 2")
			->get()->row_array();
			?>
			<tr class="table-active3">
				<th class="text-center"></th>			
				<th class="text-left" colspan="8">1.4 <?= $penerima_4['nama'];?></th>
	        </tr>
			<?php
			$penerimaan_semen_pcc_sli = $this->db->select('SUM(prm.price) as total')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order ppo', 'prm.purchase_order_id = ppo.id','left')
			->where("prm.date_receipt <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppo.supplier_id = 2")
			->where("prm.material_id = 4")
			->get()->row_array();

			$penerimaan_semen_opc_sli = $this->db->select('SUM(prm.price) as total')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order ppo', 'prm.purchase_order_id = ppo.id','left')
			->where("prm.date_receipt <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppo.supplier_id = 2")
			->where("prm.material_id = 20")
			->get()->row_array();

			$jumlah_penerimaan_sli = $penerimaan_semen_pcc_sli['total'] + $penerimaan_semen_opc_sli['total'];
			?>

			<?php
			$tagihan_semen_pcc_sli = $this->db->select('SUM(ppd.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 2")
			->where("ppd.material_id = 4")
			->get()->row_array();

			$tagihan_semen_opc_sli = $this->db->select('SUM(ppd.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 2")
			->where("ppd.material_id = 20")
			->get()->row_array();

			$jumlah_tagihan_sli = $tagihan_semen_pcc_sli['total'] + $tagihan_semen_opc_sli['total'];
			?>

			<?php
			$pembayaran_semen_pcc_sli = $this->db->select('SUM(pppp.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_pembayaran_penagihan_pembelian pppp', 'ppp.id = pppp.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 2")
			->where("ppd.material_id = 4")
			->where("pppp.memo <> 'PPN' ")
			->get()->row_array();

			$pembayaran_semen_opc_sli = $this->db->select('SUM(pppp.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_pembayaran_penagihan_pembelian pppp', 'ppp.id = pppp.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 2")
			->where("ppd.material_id = 20")
			->where("pppp.memo <> 'PPN' ")
			->get()->row_array();

			$jumlah_pembayaran_sli = $pembayaran_semen_pcc_sli['total'] + $pembayaran_semen_opc_sli['total'];
			?>

			<?php
			$tagihan_bruto_semen_pcc_sli = $penerimaan_semen_pcc_sli['total'] - $tagihan_semen_pcc_sli['total'];
			$tagihan_bruto_semen_opc_sli = $penerimaan_semen_opc_sli['total'] - $tagihan_semen_opc_sli['total'];

			$jumlah_tagihan_bruto_sli = $tagihan_bruto_semen_pcc_sli - $tagihan_bruto_semen_opc_sli;
			?>

			<?php
			$hutang_penerimaan_semen_pcc_sli = $penerimaan_semen_pcc_sli['total'] - $pembayaran_semen_pcc_sli['total'];
			$hutang_penerimaan_semen_opc_sli = $penerimaan_semen_opc_sli['total'] - $pembayaran_semen_opc_sli['total'];

			$jumlah_hutang_penerimaan_sli = $hutang_penerimaan_semen_pcc_sli - $hutang_penerimaan_semen_opc_sli;
			?>

			<?php
			$hutang_tagihan_semen_pcc_sli = $tagihan_semen_pcc_sli['total'] - $pembayaran_semen_pcc_sli['total'];
			$hutang_tagihan_semen_opc_sli = $tagihan_semen_opc_sli['total'] - $pembayaran_semen_opc_sli['total'];

			$jumlah_hutang_tagihan_sli = $hutang_tagihan_semen_pcc_sli - $hutang_tagihan_semen_opc_sli;
			?>
			<tr class="table-active3">
				<th class="text-center"></th>			
				<th class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Semen (PCC)</th>
				<th class="text-right"><?php echo number_format($penerimaan_semen_pcc_sli['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_semen_pcc_sli['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_bruto_semen_pcc_sli['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pembayaran_semen_pcc_sli['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_penerimaan_semen_pcc_sli,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_tagihan_semen_pcc_sli,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center"></th>			
				<th class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Semen (OPC)</th>
				<th class="text-right"><?php echo number_format($penerimaan_semen_opc_sli['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_semen_opc_sli['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_bruto_semen_opc_sli['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pembayaran_semen_opc_sli['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_penerimaan_semen_opc_sli,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_tagihan_semen_opc_sli,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			<tr class="table-active2">
				<th class="text-center"></th>			
				<th class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jumlah</th>
				<th class="text-right"><?php echo number_format($jumlah_penerimaan_sli,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_tagihan_sli,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_tagihan_bruto_sli,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pembayaran_sli,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_hutang_penerimaan_sli,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_hutang_tagihan_sli,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			<?php
			$penerima_5 = $this->db->select('nama')
			->from('penerima')
			->where("id = 24")
			->get()->row_array();
			?>
			<tr class="table-active3">
				<th class="text-center"></th>			
				<th class="text-left" colspan="8">1.5 <?= $penerima_5['nama'];?></th>
	        </tr>
			<?php
			$penerimaan_solar_teleindo = $this->db->select('SUM(prm.price) as total')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order ppo', 'prm.purchase_order_id = ppo.id','left')
			->where("prm.date_receipt <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppo.supplier_id = 24")
			->where("prm.material_id = 8")
			->get()->row_array();

			$jumlah_penerimaan_teleindo = $penerimaan_solar_teleindo['total'];
			?>

			<?php
			$tagihan_solar_teleindo = $this->db->select('SUM(ppd.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 24")
			->where("ppd.material_id = 8")
			->get()->row_array();

			$jumlah_tagihan_teleindo = $tagihan_solar_teleindo['total'];
			?>

			<?php
			$pembayaran_solar_teleindo = $this->db->select('SUM(pppp.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_pembayaran_penagihan_pembelian pppp', 'ppp.id = pppp.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 24")
			->where("ppd.material_id = 8")
			->where("pppp.memo <> 'PPN' ")
			->get()->row_array();

			$jumlah_pembayaran_teleindo = $pembayaran_solar_teleindo['total'];
			?>

			<?php
			$tagihan_bruto_solar_teleindo = $penerimaan_solar_teleindo['total'] - $tagihan_solar_teleindo['total'];

			$jumlah_tagihan_bruto_teleindo = $tagihan_bruto_solar_teleindo;
			?>

			<?php
			$hutang_penerimaan_solar_teleindo = $penerimaan_solar_teleindo['total'] - $pembayaran_solar_teleindo['total'];

			$jumlah_hutang_penerimaan_teleindo = $hutang_penerimaan_solar_teleindo;
			?>

			<?php
			$hutang_tagihan_solar_teleindo = $tagihan_solar_teleindo['total'] - $pembayaran_solar_teleindo['total'];

			$jumlah_hutang_tagihan_teleindo = $hutang_tagihan_solar_teleindo;
			?>
			<tr class="table-active3">
				<th class="text-center"></th>			
				<th class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Solar</th>
				<th class="text-right"><?php echo number_format($penerimaan_solar_teleindo['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_solar_teleindo['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_bruto_solar_teleindo['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pembayaran_solar_teleindo['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_penerimaan_solar_teleindo,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_tagihan_solar_teleindo,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			<tr class="table-active2">
				<th class="text-center"></th>			
				<th class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jumlah</th>
				<th class="text-right"><?php echo number_format($jumlah_penerimaan_teleindo,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_tagihan_teleindo,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_tagihan_bruto_teleindo,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pembayaran_teleindo,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_hutang_penerimaan_teleindo,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_hutang_tagihan_teleindo,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			<?php
			$jumlah_penerimaan_bahan = $jumlah_penerimaan_alamindah + $jumlah_penerimaan_kupang + $jumlah_penerimaan_langit + $jumlah_penerimaan_sli + $jumlah_penerimaan_teleindo;
			$jumlah_tagihan_bahan = $jumlah_tagihan_alamindah + $jumlah_tagihan_kupang + $jumlah_tagihan_langit + $jumlah_tagihan_sli + $jumlah_tagihan_teleindo;
			$jumlah_tagihan_bruto_bahan = $jumlah_tagihan_bruto_alamindah + $jumlah_tagihan_bruto_kupang + $jumlah_tagihan_bruto_langit + $jumlah_tagihan_bruto_sli + $jumlah_tagihan_bruto_teleindo;
			$jumlah_pembayaran_bahan = $jumlah_pembayaran_alamindah + $jumlah_pembayaran_kupang + $jumlah_pembayaran_langit + $jumlah_pembayaran_sli + $jumlah_pembayaran_teleindo;
			$jumlah_hutang_penerimaan_bahan = $jumlah_hutang_penerimaan_alamindah + $jumlah_hutang_penerimaan_kupang + $jumlah_hutang_penerimaan_langit + $jumlah_hutang_penerimaan_sli + $jumlah_hutang_penerimaan_teleindo;
			$jumlah_hutang_tagihan_bahan = $jumlah_hutang_tagihan_alamindah + $jumlah_hutang_tagihan_kupang + $jumlah_hutang_tagihan_langit + $jumlah_hutang_tagihan_sli + $jumlah_hutang_tagihan_teleindo;
			?>
			<tr class="table-active5">
				<th class="text-center"></th>			
				<th class="text-center">Jumlah Bahan</th>
				<th class="text-right"><?php echo number_format($jumlah_penerimaan_bahan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_tagihan_bahan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_tagihan_bruto_bahan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pembayaran_bahan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_hutang_penerimaan_bahan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_hutang_tagihan_bahan,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			
			<!-- PERALATAN -->
			<tr class="table-active3">
				<th class="text-center">2</th>			
				<th class="text-left" colspan="8">PERALATAN</th>
	        </tr>
			<?php
			$penerima_6 = $this->db->select('nama')
			->from('penerima')
			->where("id = 5")
			->get()->row_array();
			?>
			<tr class="table-active3">
				<th class="text-center"></th>			
				<th class="text-left" colspan="8">2.1 <?= $penerima_6['nama'];?></th>
	        </tr>
			<?php
			$penerimaan_truck_mixer_nindya = $this->db->select('SUM(prm.price) as total')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order ppo', 'prm.purchase_order_id = ppo.id','left')
			->where("prm.date_receipt <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppo.supplier_id = 5")
			->where("prm.material_id in (12,13,14,23,24,25)")
			->get()->row_array();

			$jumlah_penerimaan_nindya = $penerimaan_truck_mixer_nindya['total'];
			?>

			<?php
			$tagihan_truck_mixer_nindya = $this->db->select('SUM(ppd.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 5")
			->where("ppd.material_id in (12,13,14,23,24,25)")
			->get()->row_array();

			$jumlah_tagihan_nindya = $tagihan_truck_mixer_nindya['total'];
			?>

			<?php
			$pembayaran_truck_mixer_nindya = $this->db->select('SUM(pppp.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_pembayaran_penagihan_pembelian pppp', 'ppp.id = pppp.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 5")
			->where("ppd.material_id in (12,13,14,23,24,25)")
			->where("pppp.memo <> 'PPN' ")
			->get()->row_array();

			$jumlah_pembayaran_nindya = $pembayaran_truck_mixer_nindya['total'];
			?>

			<?php

			$tagihan_bruto_truck_mixer_nindya = $penerimaan_truck_mixer_nindya['total'] - $tagihan_truck_mixer_nindya['total'];

			$jumlah_tagihan_bruto_nindya = $tagihan_bruto_truck_mixer_nindya;
			?>

			<?php

			$hutang_penerimaan_truck_mixer_nindya = $penerimaan_truck_mixer_nindya['total'] - $pembayaran_truck_mixer_nindya['total'];

			$jumlah_hutang_penerimaan_nindya = $hutang_penerimaan_truck_mixer_nindya;
			?>

			<?php

			$hutang_tagihan_truck_mixer_nindya = $tagihan_truck_mixer_nindya['total'] - $pembayaran_truck_mixer_nindya['total'];

			$jumlah_hutang_tagihan_nindya = $hutang_tagihan_truck_mixer_nindya;
			?>
			<tr class="table-active3">
				<th class="text-center"></th>			
				<th class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Truck Mixer</th>
				<th class="text-right"><?php echo number_format($penerimaan_truck_mixer_nindya['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_truck_mixer_nindya['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_bruto_truck_mixer_nindya['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pembayaran_truck_mixer_nindya['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_penerimaan_truck_mixer_nindya,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_tagihan_truck_mixer_nindya,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			<tr class="table-active2">
				<th class="text-center"></th>			
				<th class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jumlah</th>
				<th class="text-right"><?php echo number_format($jumlah_penerimaan_nindya,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_tagihan_nindya,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_tagihan_bruto_nindya,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pembayaran_nindya,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_hutang_penerimaan_nindya,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_hutang_tagihan_nindya,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			<?php
			$penerima_7 = $this->db->select('nama')
			->from('penerima')
			->where("id = 6")
			->get()->row_array();
			?>
			<tr class="table-active3">
				<th class="text-center"></th>			
				<th class="text-left" colspan="8">2.2 <?= $penerima_7['nama'];?></th>
	        </tr>
			<?php
			$penerimaan_truck_mixer_sbm = $this->db->select('SUM(prm.price) as total')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order ppo', 'prm.purchase_order_id = ppo.id','left')
			->where("prm.date_receipt <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppo.supplier_id = 6")
			->where("prm.material_id in (12,13,14,23,24,25)")
			->get()->row_array();

			$jumlah_penerimaan_sbm = $penerimaan_truck_mixer_sbm['total'];
			?>

			<?php
			$tagihan_truck_mixer_sbm = $this->db->select('SUM(ppd.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 6")
			->where("ppd.material_id in (12,13,14,23,24,25)")
			->get()->row_array();

			$jumlah_tagihan_sbm = $tagihan_truck_mixer_sbm['total'];
			?>

			<?php
			$pembayaran_truck_mixer_sbm = $this->db->select('SUM(pppp.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_pembayaran_penagihan_pembelian pppp', 'ppp.id = pppp.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 6")
			->where("ppd.material_id in (12,13,14,23,24,25)")
			->where("pppp.memo <> 'PPN' ")
			->get()->row_array();

			$jumlah_pembayaran_sbm = $pembayaran_truck_mixer_sbm['total'];
			?>

			<?php
			$tagihan_bruto_truck_mixer_sbm = $penerimaan_truck_mixer_sbm['total'] - $tagihan_truck_mixer_sbm['total'];

			$jumlah_tagihan_bruto_sbm = $tagihan_bruto_truck_mixer_sbm;
			?>

			<?php
			$hutang_penerimaan_truck_mixer_sbm = $penerimaan_truck_mixer_sbm['total'] - $pembayaran_truck_mixer_sbm['total'];

			$jumlah_hutang_penerimaan_sbm = $hutang_penerimaan_truck_mixer_sbm;
			?>

			<?php
			$hutang_tagihan_truck_mixer_sbm = $tagihan_truck_mixer_sbm['total'] - $pembayaran_truck_mixer_sbm['total'];

			$jumlah_hutang_tagihan_sbm = $hutang_tagihan_truck_mixer_sbm;
			?>
			<tr class="table-active3">
				<th class="text-center"></th>			
				<th class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Truck Mixer</th>
				<th class="text-right"><?php echo number_format($penerimaan_truck_mixer_sbm['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_truck_mixer_sbm['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_bruto_truck_mixer_sbm['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pembayaran_truck_mixer_sbm['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_penerimaan_truck_mixer_sbm,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_tagihan_truck_mixer_sbm,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			<tr class="table-active2">
				<th class="text-center"></th>			
				<th class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jumlah</th>
				<th class="text-right"><?php echo number_format($jumlah_penerimaan_sbm,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_tagihan_sbm,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_tagihan_bruto_sbm,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pembayaran_sbm,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_hutang_penerimaan_sbm,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_hutang_tagihan_sbm,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			<?php
			$penerima_8 = $this->db->select('nama')
			->from('penerima')
			->where("id = 3")
			->get()->row_array();
			?>
			<tr class="table-active3">
				<th class="text-center"></th>			
				<th class="text-left" colspan="8">2.3 <?= $penerima_8['nama'];?></th>
	        </tr>
			<?php
			$penerimaan_batching_plant_alamindah_alat = $this->db->select('SUM(prm.price) as total')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order ppo', 'prm.purchase_order_id = ppo.id','left')
			->where("prm.date_receipt <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppo.supplier_id = 3")
			->where("prm.material_id = 15")
			->get()->row_array();

			$penerimaan_wheel_loader_alamindah_alat = $this->db->select('SUM(prm.price) as total')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order ppo', 'prm.purchase_order_id = ppo.id','left')
			->where("prm.date_receipt <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppo.supplier_id = 3")
			->where("prm.material_id = 16")
			->get()->row_array();

			$jumlah_penerimaan_alamindah_alat = $penerimaan_batching_plant_alamindah_alat['total'] + $penerimaan_wheel_loader_alamindah_alat['total'];
			?>

			<?php
			$tagihan_batching_plant_alamindah_alat = $this->db->select('SUM(ppd.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 3")
			->where("ppd.material_id = 15")
			->get()->row_array();

			$tagihan_wheel_loader_alamindah_alat = $this->db->select('SUM(ppd.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 3")
			->where("ppd.material_id = 16")
			->get()->row_array();

			$jumlah_tagihan_alamindah_alat = $tagihan_batching_plant_alamindah_alat['total'] + $tagihan_wheel_loader_alamindah_alat['total'];
			?>

			<?php
			$pembayaran_batching_plant_alamindah_alat = $this->db->select('SUM(pppp.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_pembayaran_penagihan_pembelian pppp', 'ppp.id = pppp.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 3")
			->where("ppd.material_id = 15")
			->where("pppp.memo <> 'PPN' ")
			->get()->row_array();

			$pembayaran_wheel_loader_alamindah_alat = $this->db->select('SUM(pppp.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_pembayaran_penagihan_pembelian pppp', 'ppp.id = pppp.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 3")
			->where("ppd.material_id = 16")
			->where("pppp.memo <> 'PPN' ")
			->get()->row_array();


			$jumlah_pembayaran_alamindah_alat = $pembayaran_batching_plant_alamindah_alat['total'] + $pembayaran_wheel_loader_alamindah_alat['total'];
			?>

			<?php
			$tagihan_bruto_batching_plant_alamindah_alat = $penerimaan_batching_plant_alamindah_alat['total'] - $tagihan_batching_plant_alamindah_alat['total'];
			$tagihan_bruto_wheel_loader_alamindah_alat = $penerimaan_wheel_loader_alamindah_alat['total'] - $tagihan_wheel_loader_alamindah_alat['total'];

			$jumlah_tagihan_bruto_alamindah_alat = $tagihan_bruto_batching_plant_alamindah_alat + $tagihan_bruto_wheel_loader_alamindah_alat;
			?>

			<?php
			$hutang_penerimaan_batching_plant_alamindah_alat = $penerimaan_batching_plant_alamindah_alat['total'] - $pembayaran_batching_plant_alamindah_alat['total'];
			$hutang_penerimaan_wheel_loader_alamindah_alat = $penerimaan_wheel_loader_alamindah_alat['total'] - $pembayaran_wheel_loader_alamindah_alat['total'];

			$jumlah_hutang_penerimaan_alamindah_alat = $hutang_penerimaan_batching_plant_alamindah_alat + $hutang_penerimaan_wheel_loader_alamindah_alat;
			?>

			<?php
			$hutang_tagihan_batching_plant_alamindah_alat = $tagihan_batching_plant_alamindah_alat['total'] - $pembayaran_batching_plant_alamindah_alat['total'];
			$hutang_tagihan_wheel_loader_alamindah_alat = $tagihan_wheel_loader_alamindah_alat['total'] - $pembayaran_wheel_loader_alamindah_alat['total'];

			$jumlah_hutang_tagihan_alamindah_alat = $hutang_tagihan_batching_plant_alamindah_alat + $hutang_tagihan_wheel_loader_alamindah_alat;
			?>
			<tr class="table-active3">
				<th class="text-center"></th>			
				<th class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Batching Plant</th>
				<th class="text-right"><?php echo number_format($penerimaan_batching_plant_alamindah_alat['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_batching_plant_alamindah_alat['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_bruto_batching_plant_alamindah_alat['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pembayaran_batching_plant_alamindah_alat['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_penerimaan_batching_plant_alamindah_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_tagihan_batching_plant_alamindah_alat,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			<tr class="table-active3">
				<th class="text-center"></th>			
				<th class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Wheel Loader</th>
				<th class="text-right"><?php echo number_format($penerimaan_wheel_loader_alamindah_alat['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_wheel_loader_alamindah_alat['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_bruto_wheel_loader_alamindah_alat['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pembayaran_wheel_loader_alamindah_alat['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_penerimaan_wheel_loader_alamindah_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_tagihan_wheel_loader_alamindah_alat,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			<tr class="table-active2">
				<th class="text-center"></th>			
				<th class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jumlah</th>
				<th class="text-right"><?php echo number_format($jumlah_penerimaan_alamindah_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_tagihan_alamindah_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_tagihan_bruto_alamindah_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pembayaran_alamindah_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_hutang_penerimaan_alamindah_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_hutang_tagihan_alamindah_alat,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			<?php
			$penerima_9 = $this->db->select('nama')
			->from('penerima')
			->where("id = 25")
			->get()->row_array();
			?>
			<tr class="table-active3">
				<th class="text-center"></th>			
				<th class="text-left" colspan="8">2.4 <?= $penerima_9['nama'];?></th>
	        </tr>
			<?php
			$penerimaan_truck_mixer_waskita = $this->db->select('SUM(prm.price) as total')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order ppo', 'prm.purchase_order_id = ppo.id','left')
			->where("prm.date_receipt <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppo.supplier_id = 25")
			->where("prm.material_id in (12,13,14,23,24,25)")
			->get()->row_array();

			$jumlah_penerimaan_waskita = $penerimaan_truck_mixer_waskita['total'];
			?>

			<?php
			$tagihan_truck_mixer_waskita = $this->db->select('SUM(ppd.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 25")
			->where("ppd.material_id in (12,13,14,23,24,25)")
			->get()->row_array();

			$jumlah_tagihan_waskita = $tagihan_truck_mixer_waskita['total'];
			?>

			<?php
			$pembayaran_truck_mixer_waskita = $this->db->select('SUM(pppp.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_pembayaran_penagihan_pembelian pppp', 'ppp.id = pppp.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 2")
			->where("ppd.material_id in (12,13,14,23,24,25)")
			->where("pppp.memo <> 'PPN' ")
			->get()->row_array();

			$jumlah_pembayaran_waskita = $pembayaran_truck_mixer_waskita['total'];
			?>

			<?php
			$tagihan_bruto_truck_mixer_waskita = $penerimaan_truck_mixer_waskita['total'] - $tagihan_truck_mixer_waskita['total'];

			$jumlah_tagihan_bruto_waskita = $tagihan_bruto_truck_mixer_waskita;
			?>

			<?php
			$hutang_penerimaan_truck_mixer_waskita = $penerimaan_truck_mixer_waskita['total'] - $pembayaran_truck_mixer_waskita['total'];

			$jumlah_hutang_penerimaan_waskita = $hutang_penerimaan_truck_mixer_waskita;
			?>

			<?php
			$hutang_tagihan_truck_mixer_waskita = $tagihan_truck_mixer_waskita['total'] - $pembayaran_truck_mixer_waskita['total'];

			$jumlah_hutang_tagihan_waskita = $hutang_tagihan_truck_mixer_waskita;
			?>
			<tr class="table-active3">
				<th class="text-center"></th>			
				<th class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Truck Mixer</th>
				<th class="text-right"><?php echo number_format($penerimaan_truck_mixer_waskita['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_truck_mixer_waskita['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_bruto_truck_mixer_waskita['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pembayaran_truck_mixer_waskita['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_penerimaan_truck_mixer_waskita,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_tagihan_truck_mixer_waskita,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			<tr class="table-active2">
				<th class="text-center"></th>			
				<th class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jumlah</th>
				<th class="text-right"><?php echo number_format($jumlah_penerimaan_waskita,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_tagihan_waskita,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_tagihan_bruto_waskita,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pembayaran_waskita,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_hutang_penerimaan_waskita,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_hutang_tagihan_waskita,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			<?php
			$penerima_10 = $this->db->select('nama')
			->from('penerima')
			->where("id = 2")
			->get()->row_array();
			?>
			<tr class="table-active3">
				<th class="text-center"></th>			
				<th class="text-left" colspan="8">2.5 <?= $penerima_10['nama'];?></th>
	        </tr>
			<?php
			$penerimaan_truck_mixer_sli_alat = $this->db->select('SUM(prm.price) as total')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order ppo', 'prm.purchase_order_id = ppo.id','left')
			->where("prm.date_receipt <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppo.supplier_id = 2")
			->where("prm.material_id in (12,13,14,23,24,25)")
			->get()->row_array();

			$jumlah_penerimaan_sli_alat = $penerimaan_truck_mixer_sli_alat['total'];
			?>

			<?php
			$tagihan_truck_mixer_sli_alat = $this->db->select('SUM(ppd.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 2")
			->where("ppd.material_id in (12,13,14,23,24,25)")
			->get()->row_array();

			$jumlah_tagihan_sli_alat = $tagihan_truck_mixer_sli_alat['total'];
			?>

			<?php
			$pembayaran_truck_mixer_sli_alat = $this->db->select('SUM(pppp.total) as total')
			->from('pmm_penagihan_pembelian ppp')
			->join('pmm_penagihan_pembelian_detail ppd', 'ppp.id = ppd.penagihan_pembelian_id','left')
			->join('pmm_pembayaran_penagihan_pembelian pppp', 'ppp.id = pppp.penagihan_pembelian_id','left')
			->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
			->where("ppp.tanggal_invoice <= '$date2'")
			->where("ppo.status in ('PUBLISH','CLOSED')")
			->where("ppp.supplier_id = 2")
			->where("ppd.material_id in (12,13,14,23,24,25)")
			->where("pppp.memo <> 'PPN' ")
			->get()->row_array();

			$jumlah_pembayaran_sli_alat = $pembayaran_truck_mixer_sli_alat['total'];
			?>

			<?php
			$tagihan_bruto_truck_mixer_sli_alat = $penerimaan_truck_mixer_sli_alat['total'] - $tagihan_truck_mixer_sli_alat['total'];

			$jumlah_tagihan_bruto_sli_alat = $tagihan_bruto_truck_mixer_sli_alat;
			?>

			<?php
			$hutang_penerimaan_truck_mixer_sli_alat = $penerimaan_truck_mixer_sli_alat['total'] - $pembayaran_truck_mixer_sli_alat['total'];

			$jumlah_hutang_penerimaan_sli_alat = $hutang_penerimaan_truck_mixer_sli_alat;
			?>

			<?php
			$hutang_tagihan_truck_mixer_sli_alat = $tagihan_truck_mixer_sli_alat['total'] - $pembayaran_truck_mixer_sli_alat['total'];

			$jumlah_hutang_tagihan_sli_alat = $hutang_tagihan_truck_mixer_sli_alat;
			?>
			<tr class="table-active3">
				<th class="text-center"></th>			
				<th class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Solar</th>
				<th class="text-right"><?php echo number_format($penerimaan_truck_mixer_sli_alat['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_truck_mixer_sli_alat['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($tagihan_bruto_truck_mixer_sli_alat['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pembayaran_truck_mixer_sli_alat['total'],0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_penerimaan_truck_mixer_sli_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($hutang_tagihan_truck_mixer_sli_alat,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			<tr class="table-active2">
				<th class="text-center"></th>			
				<th class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jumlah</th>
				<th class="text-right"><?php echo number_format($jumlah_penerimaan_sli_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_tagihan_sli_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_tagihan_bruto_sli_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pembayaran_sli_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_hutang_penerimaan_sli_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_hutang_tagihan_sli_alat,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			<?php
			$jumlah_penerimaan_alat = $jumlah_penerimaan_nindya + $jumlah_penerimaan_sbm + $jumlah_penerimaan_alamindah_alat + $jumlah_penerimaan_waskita + $jumlah_penerimaan_sli_alat;
			$jumlah_tagihan_alat = $jumlah_tagihan_nindya + $jumlah_tagihan_sbm + $jumlah_tagihan_alamindah_alat + $jumlah_tagihan_waskita + $jumlah_tagihan_sli_alat;
			$jumlah_tagihan_bruto_alat = $jumlah_tagihan_bruto_nindya + $jumlah_tagihan_bruto_sbm + $jumlah_tagihan_bruto_alamindah_alat + $jumlah_tagihan_bruto_waskita + $jumlah_tagihan_bruto_sli_alat;
			$jumlah_pembayaran_alat = $jumlah_pembayaran_nindya + $jumlah_pembayaran_sbm + $jumlah_pembayaran_alamindah_alat + $jumlah_pembayaran_waskita + $jumlah_pembayaran_sli_alat;
			$jumlah_hutang_penerimaan_alat = $jumlah_hutang_penerimaan_nindya + $jumlah_hutang_penerimaan_sbm + $jumlah_hutang_penerimaan_alamindah_alat + $jumlah_hutang_penerimaan_waskita + $jumlah_hutang_penerimaan_sli_alat;
			$jumlah_hutang_tagihan_alat = $jumlah_hutang_tagihan_nindya + $jumlah_hutang_tagihan_sbm + $jumlah_hutang_tagihan_alamindah_alat + $jumlah_hutang_tagihan_waskita + $jumlah_hutang_tagihan_sli_alat;
			?>
			<tr class="table-active5">
				<th class="text-center"></th>			
				<th class="text-center">Jumlah Alat</th>
				<th class="text-right"><?php echo number_format($jumlah_penerimaan_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_tagihan_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_tagihan_bruto_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pembayaran_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_hutang_penerimaan_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_hutang_tagihan_alat,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			<?php
			$total_hutang_penerimaan = $jumlah_penerimaan_bahan + $jumlah_penerimaan_alat;
			$total_hutang_tagihan = $jumlah_tagihan_bahan + $jumlah_tagihan_alat;
			$total_hutang_tagihan_bruto = $jumlah_tagihan_bruto_bahan + $jumlah_tagihan_bruto_alat;
			$total_hutang_pembayaran = $jumlah_pembayaran_bahan + $jumlah_pembayaran_alat;
			$total_hutang_penerimaan_all = $jumlah_hutang_penerimaan_bahan + $jumlah_hutang_penerimaan_alat;
			$total_hutang_tagihan_all = $jumlah_hutang_tagihan_bahan + $jumlah_hutang_tagihan_alat;
			?>
			<tr class="table-active5">
				<th class="text-center"></th>			
				<th class="text-center">Total Hutang</th>
				<th class="text-right"><?php echo number_format($total_hutang_penerimaan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_hutang_tagihan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_hutang_tagihan_bruto,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_hutang_pembayaran,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_hutang_penerimaan_all,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_hutang_tagihan_all,0,',','.');?></th>
				<th class="text-right"></th>
	        </tr>
			
	    </table>
		<?php
	}


}