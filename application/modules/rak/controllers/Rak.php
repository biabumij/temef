<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rak extends Secure_Controller {

	public function __construct()
	{
        parent::__construct();
        // Your own constructor code
        $this->load->model(array('admin/m_admin','crud_global','m_themes','pages/m_pages','menu/m_menu','admin_access/m_admin_access','DB_model','member_back/m_member_back','m_member','pmm/pmm_model','admin/Templates','pmm/pmm_finance','produk/m_produk'));
        $this->load->library('enkrip');
		$this->load->library('filter');
		$this->load->library('waktu');
		$this->load->library('session');
    }
	
	public function form_rencana_kerja()
	{
		$check = $this->m_admin->check_login();
		if ($check == true) {
			$data['products'] =  $this->db->select('*')
			->from('produk p')
			->where("p.status = 'PUBLISH'")
			->where("p.betonreadymix = 1 ")
			->order_by('nama_produk','asc')
			->get()->result_array();

			$data['komposisi'] = $this->db->select('id, jobs_type,date_agregat')->order_by('date_agregat','desc')->get_where('pmm_agregat',array('status'=>'PUBLISH'))->result_array();

			$data['semen'] = $this->pmm_model->getMatByPenawaranRencanaKerjaSemen();
			$data['pasir'] = $this->pmm_model->getMatByPenawaranRencanaKerjaPasir();
			$data['batu1020'] = $this->pmm_model->getMatByPenawaranRencanaKerjaBatu1020();
			$data['batu2030'] = $this->pmm_model->getMatByPenawaranRencanaKerjaBatu2030();
			$data['solar'] = $this->pmm_model->getMatByPenawaranRencanaKerjaSolar();
			$this->load->view('rak/form_rencana_kerja', $data);
		} else {
			redirect('admin');
		}
	}
	
	public function submit_rencana_kerja()
	{
		$tanggal_rencana_kerja = $this->input->post('tanggal_rencana_kerja');
		$vol_produk_a =  str_replace('.', '', $this->input->post('vol_produk_a'));
		$vol_produk_a =  str_replace(',', '.', $vol_produk_a);
		$vol_produk_b =  str_replace('.', '', $this->input->post('vol_produk_b'));
		$vol_produk_b =  str_replace(',', '.', $vol_produk_b);
		$vol_produk_c =  str_replace('.', '', $this->input->post('vol_produk_c'));
		$vol_produk_c =  str_replace(',', '.', $vol_produk_c);
		$vol_produk_d =  str_replace('.', '', $this->input->post('vol_produk_d'));
		$vol_produk_d =  str_replace(',', '.', $vol_produk_d);

		$komposisi_125 =  $this->input->post('komposisi_125');
		$komposisi_225 =  $this->input->post('komposisi_225');
		$komposisi_250 =  $this->input->post('komposisi_250');
		$komposisi_250_2 =  $this->input->post('komposisi_250_2');

		$penawaran_id_semen =  $this->input->post('penawaran_id_semen');
		$penawaran_id_pasir =  $this->input->post('penawaran_id_pasir');
		$penawaran_id_batu1020 =  $this->input->post('penawaran_id_batu1020');
		$penawaran_id_batu2030 =  $this->input->post('penawaran_id_batu2030');
		$penawaran_id_solar =  $this->input->post('penawaran_id_solar');

		$harga_semen =  str_replace('.', '', $this->input->post('price_semen'));
		$harga_pasir =  str_replace('.', '', $this->input->post('price_pasir'));
		$harga_batu1020 =  str_replace('.', '', $this->input->post('price_batu1020'));
		$harga_batu2030 =  str_replace('.', '', $this->input->post('price_batu2030'));
		$harga_solar =  str_replace('.', '', $this->input->post('price_solar'));

		$satuan_semen =  $this->input->post('measure_semen');
		$satuan_pasir =  $this->input->post('measure_pasir');
		$satuan_batu1020 =  $this->input->post('measure_batu1020');
		$satuan_batu2030 =  $this->input->post('measure_batu2030');
		$satuan_solar =  $this->input->post('measure_solar');

		$tax_id_semen =  $this->input->post('tax_id_semen');
		$tax_id_pasir =  $this->input->post('tax_id_pasir');
		$tax_id_batu1020 =  $this->input->post('tax_id_batu1020');
		$tax_id_batu2030 =  $this->input->post('tax_id_batu2030');
		$tax_id_solar =  $this->input->post('tax_id_solar');

		$supplier_id_semen =  $this->input->post('supplier_id_semen');
		$supplier_id_pasir =  $this->input->post('supplier_id_pasir');
		$supplier_id_batu1020 =  $this->input->post('supplier_id_batu1020');
		$supplier_id_batu2030 =  $this->input->post('supplier_id_batu2030');
		$supplier_id_solar =  $this->input->post('supplier_id_solar');

		$this->db->trans_start(); # Starting Transaction
		$this->db->trans_strict(FALSE); # See Note 01. If you wish can remove as well 

		$arr_insert = array(
			'tanggal_rencana_kerja' =>  date('Y-m-d', strtotime($tanggal_rencana_kerja)),

			'vol_produk_a' => $vol_produk_a,
			'vol_produk_b' => $vol_produk_b,
			'vol_produk_c' => $vol_produk_c,
			'vol_produk_d' => $vol_produk_d,

			'komposisi_125' => $komposisi_125,
			'komposisi_225' => $komposisi_225,
			'komposisi_250' => $komposisi_250,
			'komposisi_250_2' => $komposisi_250_2,

			'price_a' => 896600,
			'price_b' => 1005000,
			'price_c' => 1179200,
			'price_d' => 1200000,

			'penawaran_id_semen' => $penawaran_id_semen,
			'penawaran_id_pasir' => $penawaran_id_pasir,
			'penawaran_id_batu1020' => $penawaran_id_batu1020,
			'penawaran_id_batu2030' => $penawaran_id_batu2030,
			'penawaran_id_solar' => $penawaran_id_solar,

			'harga_semen' => $harga_semen,
			'harga_pasir' => $harga_pasir,
			'harga_batu1020' => $harga_batu1020,
			'harga_batu2030' => $harga_batu2030,
			'harga_solar' => $harga_solar,

			'satuan_semen' => $satuan_semen,
			'satuan_pasir' => $satuan_pasir,
			'satuan_batu1020' => $satuan_batu1020,
			'satuan_batu2030' => $satuan_batu2030,
			'satuan_solar' => $satuan_solar,

			'tax_id_semen' => $tax_id_semen,
			'tax_id_pasir' => $tax_id_pasir,
			'tax_id_batu1020' => $tax_id_batu1020,
			'tax_id_batu2030' => $tax_id_batu2030,
			'tax_id_solar' => $tax_id_solar,

			'supplier_id_semen' => $supplier_id_semen,
			'supplier_id_pasir' => $supplier_id_pasir,
			'supplier_id_batu1020' => $supplier_id_batu1020,
			'supplier_id_batu2030' => $supplier_id_batu2030,
			'supplier_id_solar' => $supplier_id_solar,
			
			'status' => 'PUBLISH',
			'created_by' => $this->session->userdata('admin_id'),
			'created_on' => date('Y-m-d H:i:s')
		);
		
		if ($this->db->insert('rak', $arr_insert)) {
			$rak_id = $this->db->insert_id();

			if (!file_exists('uploads/rak')) {
			    mkdir('uploads/rak', 0777, true);
			}


			$data = [];
			$count = count($_FILES['files']['name']);
			for ($i = 0; $i < $count; $i++) {

				if (!empty($_FILES['files']['name'][$i])) {

					$_FILES['file']['name'] = $_FILES['files']['name'][$i];
					$_FILES['file']['type'] = $_FILES['files']['type'][$i];
					$_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
					$_FILES['file']['error'] = $_FILES['files']['error'][$i];
					$_FILES['file']['size'] = $_FILES['files']['size'][$i];

					$config['upload_path'] = 'uploads/rak';
					$config['allowed_types'] = 'jpg|jpeg|png|pdf';
					$config['file_name'] = $_FILES['files']['name'][$i];

					$this->load->library('upload', $config);

					if ($this->upload->do_upload('file')) {
						$uploadData = $this->upload->data();
						$filename = $uploadData['file_name'];

						$data['totalFiles'][] = $filename;


						$data[$i] = array(
							'rak_id' => $rak_id,
							'lampiran'  => $data['totalFiles'][$i]
						);

						$this->db->insert('lampiran_rak', $data[$i]);
					}
				}
			}
		}


		if ($this->db->trans_status() === FALSE) {
			# Something went wrong.
			$this->db->trans_rollback();
			$this->session->set_flashdata('notif_error', 'Gagal Membuat Rencana Kerja !!');
			redirect('admin/rencana_kerja');
		} else {
			# Everything is Perfect. 
			# Committing data to the database.
			$this->db->trans_commit();
			$this->session->set_flashdata('notif_success', 'Berhasil Membuat Rencana Kerja !!');
			redirect('admin/rencana_kerja');
		}
	}
	
	public function table_rencana_kerja()
	{   
        $data = array();

        $this->db->select('rak.*, lk.lampiran, rak.status');		
		$this->db->join('lampiran_rak lk', 'rak.id = lk.rak_id','left');
		$this->db->where('rak.status','PUBLISH');
		$this->db->order_by('rak.tanggal_rencana_kerja','desc');			
		$query = $this->db->get('rak rak');
		
       	if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
                $row['no'] = $key+1;
				$row['tanggal_rencana_kerja'] = "<a href=" . base_url('rak/cetak_rencana_kerja/' . $row["id"]) .'" target="_blank">' .  date('d F Y',strtotime($row['tanggal_rencana_kerja'])) . "</a>";
				$row['jumlah'] = number_format($row['vol_produk_a'] + $row['vol_produk_b'] + $row['vol_produk_c'] + $row['vol_produk_d'],2,',','.');
				$row['lampiran'] = '<a href="' . base_url('uploads/rak/' . $row['lampiran']) .'" target="_blank">' . $row['lampiran'] . '</a>';  
				$row['admin_name'] = $this->crud_global->GetField('tbl_admin',array('admin_id'=>$row['created_by']),'admin_name');
                $row['created_on'] = date('d/m/Y H:i:s',strtotime($row['created_on']));
				$row['print'] = '<a href="'.site_url().'rak/cetak_rencana_kerja/'.$row['id'].'" target="_blank" class="btn btn-info"><i class="fa fa-print"></i> </a>';
				
				if($this->session->userdata('admin_group_id') == 1 || $this->session->userdata('admin_group_id') == 4 || $this->session->userdata('admin_group_id') == 5 || $this->session->userdata('admin_group_id') == 6 || $this->session->userdata('admin_group_id') == 10 || $this->session->userdata('admin_group_id') == 15){
				$row['edit'] = '<a href="'.site_url().'rak/sunting_rencana_kerja/'.$row['id'].'" class="btn btn-warning"><i class="fa fa-edit"></i> </a>';
				}else {
					$row['edit'] = '-';
				}

				if($this->session->userdata('admin_group_id') == 1 || $this->session->userdata('admin_group_id') == 4 || $this->session->userdata('admin_group_id') == 5 || $this->session->userdata('admin_group_id') == 6 || $this->session->userdata('admin_group_id') == 10){
				$row['actions'] = '<a href="javascript:void(0);" onclick="DeleteData('.$row['id'].')" class="btn btn-danger"><i class="fa fa-close"></i> </a>';
				}else {
					$row['actions'] = '-';
				}

                $data[] = $row;
            }

        }
        echo json_encode(array('data'=>$data));
    }

	public function delete_rencana_kerja()
	{
		$output['output'] = false;
		$id = $this->input->post('id');
		if(!empty($id)){
			$this->db->delete('rak',array('id'=>$id));
			$this->db->delete('lampiran_rak',array('rak_id'=>$id));
			{
				$output['output'] = true;
			}
		}
		echo json_encode($output);
	}
	
	public function cetak_rencana_kerja($id){

		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
		$pdf->setPrintFooter(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		$pdf->AddPage('P');

		$data['rak'] = $this->db->get_where('rak',array('id'=>$id))->row_array();
        $html = $this->load->view('rak/cetak_rencana_kerja',$data,TRUE);
        $rak = $this->db->get_where('rak',array('id'=>$id))->row_array();

		$pdf->SetTitle('BBJ - Rencana Kerja');
        $pdf->nsi_html($html);
        $pdf->Output('rencana_kerja.pdf', 'I');
	}

	public function sunting_rencana_kerja($id)
	{
		$check = $this->m_admin->check_login();
		if ($check == true) {
			$data['tes'] = '';
			$data['rak'] = $this->db->get_where("rak", ["id" => $id])->row_array();
			$data['lampiran'] = $this->db->get_where("lampiran_rak", ["rak_id" => $id])->result_array();
			$data['komposisi'] = $this->db->select('id, jobs_type,date_agregat')->order_by('date_agregat','desc')->get_where('pmm_agregat',array('status'=>'PUBLISH'))->result_array();
			$data['semen'] = $this->pmm_model->getMatByPenawaranRencanaKerjaSemen();
			$data['pasir'] = $this->pmm_model->getMatByPenawaranRencanaKerjaPasir();
			$data['batu1020'] = $this->pmm_model->getMatByPenawaranRencanaKerjaBatu1020();
			$data['batu2030'] = $this->pmm_model->getMatByPenawaranRencanaKerjaBatu2030();
			$data['solar'] = $this->pmm_model->getMatByPenawaranRencanaKerjaSolar();
			$this->load->view('rak/sunting_rencana_kerja', $data);
		} else {
			redirect('admin');
		}
	}

	public function submit_sunting_rencana_kerja()
	{

		$this->db->trans_start(); # Starting Transaction
		$this->db->trans_strict(FALSE); #

			$id = $this->input->post('id');
			$vol_produk_a =  str_replace('.', '', $this->input->post('vol_produk_a'));
			$vol_produk_a =  str_replace(',', '.', $vol_produk_a);
			$vol_produk_b =  str_replace('.', '', $this->input->post('vol_produk_b'));
			$vol_produk_b =  str_replace(',', '.', $vol_produk_b);
			$vol_produk_c =  str_replace('.', '', $this->input->post('vol_produk_c'));
			$vol_produk_c =  str_replace(',', '.', $vol_produk_c);
			$vol_produk_d =  str_replace('.', '', $this->input->post('vol_produk_d'));
			$vol_produk_d =  str_replace(',', '.', $vol_produk_d);

			$komposisi_125 =  $this->input->post('komposisi_125');
			$komposisi_225 =  $this->input->post('komposisi_225');
			$komposisi_250 =  $this->input->post('komposisi_250');
			$komposisi_250_2 =  $this->input->post('komposisi_250_2');

			$penawaran_id_semen =  $this->input->post('penawaran_id_semen');
			$penawaran_id_pasir =  $this->input->post('penawaran_id_pasir');
			$penawaran_id_batu1020 =  $this->input->post('penawaran_id_batu1020');
			$penawaran_id_batu2030 =  $this->input->post('penawaran_id_batu2030');
			$penawaran_id_solar =  $this->input->post('penawaran_id_solar');

			$harga_semen =  str_replace('.', '', $this->input->post('price_semen'));
			$harga_pasir =  str_replace('.', '', $this->input->post('price_pasir'));
			$harga_batu1020 =  str_replace('.', '', $this->input->post('price_batu1020'));
			$harga_batu2030 =  str_replace('.', '', $this->input->post('price_batu2030'));
			$harga_solar =  str_replace('.', '', $this->input->post('price_solar'));

			$satuan_semen =  $this->input->post('measure_semen');
			$satuan_pasir =  $this->input->post('measure_pasir');
			$satuan_batu1020 =  $this->input->post('measure_batu1020');
			$satuan_batu2030 =  $this->input->post('measure_batu2030');
			$satuan_solar =  $this->input->post('measure_solar');

			$tax_id_semen =  $this->input->post('tax_id_semen');
			$tax_id_pasir =  $this->input->post('tax_id_pasir');
			$tax_id_batu1020 =  $this->input->post('tax_id_batu1020');
			$tax_id_batu2030 =  $this->input->post('tax_id_batu2030');
			$tax_id_solar =  $this->input->post('tax_id_solar');

			$supplier_id_semen =  $this->input->post('supplier_id_semen');
			$supplier_id_pasir =  $this->input->post('supplier_id_pasir');
			$supplier_id_batu1020 =  $this->input->post('supplier_id_batu1020');
			$supplier_id_batu2030 =  $this->input->post('supplier_id_batu2030');
			$supplier_id_solar =  $this->input->post('supplier_id_solar');

			$arr_update = array(
				'vol_produk_a' => $vol_produk_a,
				'vol_produk_b' => $vol_produk_b,
				'vol_produk_c' => $vol_produk_c,
				'vol_produk_d' => $vol_produk_d,

				'komposisi_125' => $komposisi_125,
				'komposisi_225' => $komposisi_225,
				'komposisi_250' => $komposisi_250,
				'komposisi_250_2' => $komposisi_250_2,

				'price_a' => 896600,
				'price_b' => 1005000,
				'price_c' => 1179200,
				'price_d' => 1200000,

				'penawaran_id_semen' => $penawaran_id_semen,
				'penawaran_id_pasir' => $penawaran_id_pasir,
				'penawaran_id_batu1020' => $penawaran_id_batu1020,
				'penawaran_id_batu2030' => $penawaran_id_batu2030,
				'penawaran_id_solar' => $penawaran_id_solar,

				'harga_semen' => $harga_semen,
				'harga_pasir' => $harga_pasir,
				'harga_batu1020' => $harga_batu1020,
				'harga_batu2030' => $harga_batu2030,
				'harga_solar' => $harga_solar,

				'satuan_semen' => $satuan_semen,
				'satuan_pasir' => $satuan_pasir,
				'satuan_batu1020' => $satuan_batu1020,
				'satuan_batu2030' => $satuan_batu2030,
				'satuan_solar' => $satuan_solar,

				'tax_id_semen' => $tax_id_semen,
				'tax_id_pasir' => $tax_id_pasir,
				'tax_id_batu1020' => $tax_id_batu1020,
				'tax_id_batu2030' => $tax_id_batu2030,
				'tax_id_solar' => $tax_id_solar,

				'supplier_id_semen' => $supplier_id_semen,
				'supplier_id_pasir' => $supplier_id_pasir,
				'supplier_id_batu1020' => $supplier_id_batu1020,
				'supplier_id_batu2030' => $supplier_id_batu2030,
				'supplier_id_solar' => $supplier_id_solar,
				
				'status' => 'PUBLISH',
				'updated_by' => $this->session->userdata('admin_id'),
				'updated_on' => date('Y-m-d H:i:s')
			);

			$this->db->where('id', $id);
			if ($this->db->update('rak', $arr_update)) {
				
			}

			if ($this->db->trans_status() === FALSE) {
				# Something went wrong.
				$this->db->trans_rollback();
				$this->session->set_flashdata('notif_error', 'Gagal Memperbaharui Rencana Kerja !!');
				redirect('admin/rencana_kerja');
			} else {
				# Everything is Perfect. 
				# Committing data to the database.
				$this->db->trans_commit();
				$this->session->set_flashdata('notif_success', 'Berhasil Memperbaharui Rencana Kerja !!');
				redirect('admin/rencana_kerja');
			}
	}

	public function form_rencana_kerja_biaya()
	{
		$check = $this->m_admin->check_login();
		if ($check == true) {
			$data['products'] =  $this->db->select('*')
			->from('produk p')
			->where("p.status = 'PUBLISH'")
			->where("p.betonreadymix = 1 ")
			->order_by('nama_produk','asc')
			->get()->result_array();
			$this->load->view('rak/form_rencana_kerja_biaya', $data);
		} else {
			redirect('admin');
		}
	}

	public function submit_rencana_kerja_biaya()
	{
		$tanggal_rencana_kerja = $this->input->post('tanggal_rencana_kerja');
		$biaya_bahan =  str_replace('.', '', $this->input->post('biaya_bahan'));
		$biaya_alat =  str_replace('.', '', $this->input->post('biaya_alat'));

		$this->db->trans_start(); # Starting Transaction
		$this->db->trans_strict(FALSE); # See Note 01. If you wish can remove as well 

		$arr_insert = array(
			'tanggal_rencana_kerja' =>  date('Y-m-d', strtotime($tanggal_rencana_kerja)),
			'biaya_bahan' => $biaya_bahan,
			'biaya_alat' => $biaya_alat,
			'status' => 'PUBLISH',
			'created_by' => $this->session->userdata('admin_id'),
			'created_on' => date('Y-m-d H:i:s')
		);
		
		if ($this->db->insert('rak_biaya', $arr_insert)) {
			$rak_id = $this->db->insert_id();

			if (!file_exists('uploads/rak_biaya')) {
			    mkdir('uploads/rak_biaya', 0777, true);
			}


			$data = [];
			$count = count($_FILES['files']['name']);
			for ($i = 0; $i < $count; $i++) {

				if (!empty($_FILES['files']['name'][$i])) {

					$_FILES['file']['name'] = $_FILES['files']['name'][$i];
					$_FILES['file']['type'] = $_FILES['files']['type'][$i];
					$_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
					$_FILES['file']['error'] = $_FILES['files']['error'][$i];
					$_FILES['file']['size'] = $_FILES['files']['size'][$i];

					$config['upload_path'] = 'uploads/rak_biaya';
					$config['allowed_types'] = 'jpg|jpeg|png|pdf';
					$config['file_name'] = $_FILES['files']['name'][$i];

					$this->load->library('upload', $config);

					if ($this->upload->do_upload('file')) {
						$uploadData = $this->upload->data();
						$filename = $uploadData['file_name'];

						$data['totalFiles'][] = $filename;


						$data[$i] = array(
							'rak_id' => $rak_id,
							'lampiran'  => $data['totalFiles'][$i]
						);

						$this->db->insert('lampiran_rak_biaya', $data[$i]);
					}
				}
			}
		}


		if ($this->db->trans_status() === FALSE) {
			# Something went wrong.
			$this->db->trans_rollback();
			$this->session->set_flashdata('notif_error', 'Gagal Membuat Rencana Kerja !!');
			redirect('admin/rencana_kerja');
		} else {
			# Everything is Perfect. 
			# Committing data to the database.
			$this->db->trans_commit();
			$this->session->set_flashdata('notif_success', 'Berhasil Membuat Rencana Kerja !!');
			redirect('admin/rencana_kerja');
		}
	}

	public function table_rencana_kerja_biaya()
	{   
        $data = array();

        $this->db->select('rak.*, lk.lampiran, rak.status');		
		$this->db->join('lampiran_rak_biaya lk', 'rak.id = lk.rak_id','left');
		$this->db->where('rak.status','PUBLISH');
		$this->db->order_by('rak.tanggal_rencana_kerja','desc');			
		$query = $this->db->get('rak_biaya rak');
		
       	if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
                $row['no'] = $key+1;
				$row['tanggal_rencana_kerja'] = date('d F Y',strtotime($row['tanggal_rencana_kerja']));
				$row['biaya_bahan'] = number_format($row['biaya_bahan'],0,',','.');
				$row['biaya_alat'] = number_format($row['biaya_alat'],0,',','.');
				$row['lampiran'] = '<a href="' . base_url('uploads/rak_biaya/' . $row['lampiran']) .'" target="_blank">' . $row['lampiran'] . '</a>';  
				$row['admin_name'] = $this->crud_global->GetField('tbl_admin',array('admin_id'=>$row['created_by']),'admin_name');
                $row['created_on'] = date('d/m/Y H:i:s',strtotime($row['created_on']));
				$row['print'] = '<a href="'.site_url().'rak/cetak_rencana_kerja_biaya/'.$row['id'].'" target="_blank" class="btn btn-info"><i class="fa fa-print"></i> </a>';
				
				if($this->session->userdata('admin_group_id') == 1 || $this->session->userdata('admin_group_id') == 4 || $this->session->userdata('admin_group_id') == 5 || $this->session->userdata('admin_group_id') == 6 || $this->session->userdata('admin_group_id') == 10 || $this->session->userdata('admin_group_id') == 15){
				$row['edit'] = '<a href="'.site_url().'rak/sunting_rencana_kerja_biaya/'.$row['id'].'" class="btn btn-warning"><i class="fa fa-edit"></i> </a>';
				}else {
					$row['edit'] = '-';
				}

				if($this->session->userdata('admin_group_id') == 1 || $this->session->userdata('admin_group_id') == 4 || $this->session->userdata('admin_group_id') == 5 || $this->session->userdata('admin_group_id') == 6 || $this->session->userdata('admin_group_id') == 10){
				$row['delete'] = '<a href="javascript:void(0);" onclick="DeleteData2('.$row['id'].')" class="btn btn-danger"><i class="fa fa-close"></i> </a>';
				}else {
					$row['delete'] = '-';
				}

                $data[] = $row;
            }

        }
        echo json_encode(array('data'=>$data));
    }

	public function delete_rencana_kerja_biaya()
	{
		$output['output'] = false;
		$id = $this->input->post('id');
		if(!empty($id)){
			$this->db->delete('rak_biaya',array('id'=>$id));
			$this->db->delete('lampiran_rak_biaya',array('rak_id'=>$id));
			{
				$output['output'] = true;
			}
		}
		echo json_encode($output);
	}

	public function cetak_rencana_kerja_biaya($id){

		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
		$pdf->setPrintFooter(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		$pdf->AddPage('P');

		$data['rak'] = $this->db->get_where('rak_biaya',array('id'=>$id))->row_array();
        $html = $this->load->view('rak/cetak_rencana_kerja_biaya',$data,TRUE);
        $rak = $this->db->get_where('rak_biaya',array('id'=>$id))->row_array();

		$pdf->SetTitle('BBJ - Rencana Kerja');
        $pdf->nsi_html($html);
        $pdf->Output('rencana_kerja.pdf', 'I');
	}
	
	public function sunting_rencana_kerja_biaya($id)
	{
		$check = $this->m_admin->check_login();
		if ($check == true) {
			$data['tes'] = '';
			$data['rak'] = $this->db->get_where("rak_biaya", ["id" => $id])->row_array();
			$data['lampiran'] = $this->db->get_where("lampiran_rak_biaya", ["rak_id" => $id])->result_array();
			$this->load->view('rak/sunting_rencana_kerja_biaya', $data);
		} else {
			redirect('admin');
		}
	}

	public function submit_sunting_rencana_biaya()
	{

		$this->db->trans_start(); # Starting Transaction
		$this->db->trans_strict(FALSE); #

			$id = $this->input->post('id');
			$biaya_bahan =  str_replace('.', '', $this->input->post('biaya_bahan'));
			$biaya_alat =  str_replace('.', '', $this->input->post('biaya_alat'));

			$arr_update = array(
				'biaya_bahan' => $biaya_bahan,
				'biaya_alat' => $biaya_alat,
				'status' => 'PUBLISH',
				'updated_by' => $this->session->userdata('admin_id'),
				'updated_on' => date('Y-m-d H:i:s')
			);

			$this->db->where('id', $id);
			if ($this->db->update('rak_biaya', $arr_update)) {
				
			}

			if ($this->db->trans_status() === FALSE) {
				# Something went wrong.
				$this->db->trans_rollback();
				$this->session->set_flashdata('notif_error', 'Gagal Memperbaharui Rencana Kerja !!');
				redirect('admin/rencana_kerja');
			} else {
				# Everything is Perfect. 
				# Committing data to the database.
				$this->db->trans_commit();
				$this->session->set_flashdata('notif_success', 'Berhasil Memperbaharui Rencana Kerja !!');
				redirect('admin/rencana_kerja');
			}
	}

	public function form_rencana_kerja_biaya_cash_flow()
	{
		$check = $this->m_admin->check_login();
		if ($check == true) {
			$data['products'] =  $this->db->select('*')
			->from('produk p')
			->where("p.status = 'PUBLISH'")
			->where("p.betonreadymix = 1 ")
			->order_by('nama_produk','asc')
			->get()->result_array();
			$this->load->view('rak/form_rencana_kerja_biaya_cash_flow', $data);
		} else {
			redirect('admin');
		}
	}

	public function submit_rencana_kerja_biaya_cash_flow()
	{
		$tanggal_rencana_kerja = $this->input->post('tanggal_rencana_kerja');
		$biaya_bank =  str_replace('.', '', $this->input->post('biaya_bank'));
		$biaya_overhead =  str_replace('.', '', $this->input->post('biaya_overhead'));
		$termin =  str_replace('.', '', $this->input->post('termin'));
		$biaya_persiapan =  str_replace('.', '', $this->input->post('biaya_persiapan'));

		$this->db->trans_start(); # Starting Transaction
		$this->db->trans_strict(FALSE); # See Note 01. If you wish can remove as well 

		$arr_insert = array(
			'tanggal_rencana_kerja' =>  date('Y-m-d', strtotime($tanggal_rencana_kerja)),
			'biaya_bank' => $biaya_bank,
			'biaya_overhead' => $biaya_overhead,
			'termin' => $termin,
			'biaya_persiapan' => $biaya_persiapan,
			'status' => 'PUBLISH',
			'created_by' => $this->session->userdata('admin_id'),
			'created_on' => date('Y-m-d H:i:s')
		);
		
		if ($this->db->insert('rak_biaya_cash_flow', $arr_insert)) {
			$rak_id = $this->db->insert_id();

			if (!file_exists('uploads/rak_biaya_cash_flow')) {
			    mkdir('uploads/rak_biaya_cash_flow', 0777, true);
			}


			$data = [];
			$count = count($_FILES['files']['name']);
			for ($i = 0; $i < $count; $i++) {

				if (!empty($_FILES['files']['name'][$i])) {

					$_FILES['file']['name'] = $_FILES['files']['name'][$i];
					$_FILES['file']['type'] = $_FILES['files']['type'][$i];
					$_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
					$_FILES['file']['error'] = $_FILES['files']['error'][$i];
					$_FILES['file']['size'] = $_FILES['files']['size'][$i];

					$config['upload_path'] = 'uploads/rak_biaya_cash_flow';
					$config['allowed_types'] = 'jpg|jpeg|png|pdf';
					$config['file_name'] = $_FILES['files']['name'][$i];

					$this->load->library('upload', $config);

					if ($this->upload->do_upload('file')) {
						$uploadData = $this->upload->data();
						$filename = $uploadData['file_name'];

						$data['totalFiles'][] = $filename;


						$data[$i] = array(
							'rak_id' => $rak_id,
							'lampiran'  => $data['totalFiles'][$i]
						);

						$this->db->insert('lampiran_rak_biaya_cash_flow', $data[$i]);
					}
				}
			}
		}


		if ($this->db->trans_status() === FALSE) {
			# Something went wrong.
			$this->db->trans_rollback();
			$this->session->set_flashdata('notif_error', 'Gagal Membuat Rencana Kerja !!');
			redirect('admin/rencana_kerja_keu');
		} else {
			# Everything is Perfect. 
			# Committing data to the database.
			$this->db->trans_commit();
			$this->session->set_flashdata('notif_success', 'Berhasil Membuat Rencana Kerja !!');
			redirect('admin/rencana_kerja_keu');
		}
	}


	public function table_rencana_kerja_biaya_cash_flow()
	{   
        $data = array();

        $this->db->select('rak.*, lk.lampiran, rak.status');		
		$this->db->join('lampiran_rak_biaya_cash_flow lk', 'rak.id = lk.rak_id','left');
		$this->db->where('rak.status','PUBLISH');
		$this->db->order_by('rak.tanggal_rencana_kerja','desc');			
		$query = $this->db->get('rak_biaya_cash_flow rak');
		
       	if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
                $row['no'] = $key+1;
				$row['tanggal_rencana_kerja'] = date('d F Y',strtotime($row['tanggal_rencana_kerja']));
				$row['biaya_bank'] = number_format($row['biaya_bank'],0,',','.');
				$row['biaya_overhead'] = number_format($row['biaya_overhead'],0,',','.');
				$row['biaya_persiapan'] = number_format($row['biaya_persiapan'],0,',','.');
				$row['termin'] = number_format($row['termin'],0,',','.');
				$row['lampiran'] = '<a href="' . base_url('uploads/rak_biaya_cash_flow/' . $row['lampiran']) .'" target="_blank">' . $row['lampiran'] . '</a>';  
				$row['admin_name'] = $this->crud_global->GetField('tbl_admin',array('admin_id'=>$row['created_by']),'admin_name');
                $row['created_on'] = date('d/m/Y H:i:s',strtotime($row['created_on']));
				$row['print'] = '<a href="'.site_url().'rak/cetak_rencana_kerja_biaya_cash_flow/'.$row['id'].'" target="_blank" class="btn btn-info"><i class="fa fa-print"></i> </a>';
				
				if($this->session->userdata('admin_group_id') == 1 || $this->session->userdata('admin_group_id') == 4 || $this->session->userdata('admin_group_id') == 5 || $this->session->userdata('admin_group_id') == 6 || $this->session->userdata('admin_group_id') == 10 || $this->session->userdata('admin_group_id') == 15){
				$row['edit'] = '<a href="'.site_url().'rak/sunting_rencana_kerja_biaya_cash_flow/'.$row['id'].'" class="btn btn-warning"><i class="fa fa-edit"></i> </a>';
				}else {
					$row['edit'] = '-';
				}

				if($this->session->userdata('admin_group_id') == 1 || $this->session->userdata('admin_group_id') == 4 || $this->session->userdata('admin_group_id') == 5 || $this->session->userdata('admin_group_id') == 6 || $this->session->userdata('admin_group_id') == 10){
				$row['delete'] = '<a href="javascript:void(0);" onclick="DeleteData3('.$row['id'].')" class="btn btn-danger"><i class="fa fa-close"></i> </a>';
				}else {
					$row['delete'] = '-';
				}

                $data[] = $row;
            }

        }
        echo json_encode(array('data'=>$data));
    }

	public function delete_rencana_kerja_biaya_cash_flow()
	{
		$output['output'] = false;
		$id = $this->input->post('id');
		if(!empty($id)){
			$this->db->delete('rak_biaya_cash_flow',array('id'=>$id));
			$this->db->delete('lampiran_rak_biaya_cash_flow',array('rak_id'=>$id));
			{
				$output['output'] = true;
			}
		}
		echo json_encode($output);
	}

	public function cetak_rencana_kerja_biaya_cash_flow($id){

		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
		$pdf->setPrintFooter(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		$pdf->AddPage('P');

		$data['rak'] = $this->db->get_where('rak_biaya_cash_flow',array('id'=>$id))->row_array();
        $html = $this->load->view('rak/cetak_rencana_kerja_biaya_cash_flow',$data,TRUE);
        $rak = $this->db->get_where('rak_biaya_cash_flow',array('id'=>$id))->row_array();

		$pdf->SetTitle('BBJ - Rencana Kerja');
        $pdf->nsi_html($html);
        $pdf->Output('rencana_kerja.pdf', 'I');
	}

	public function sunting_rencana_kerja_biaya_cash_flow($id)
	{
		$check = $this->m_admin->check_login();
		if ($check == true) {
			$data['tes'] = '';
			$data['rak'] = $this->db->get_where("rak_biaya_cash_flow", ["id" => $id])->row_array();
			$data['lampiran'] = $this->db->get_where("lampiran_rak_biaya_cash_flow", ["rak_id" => $id])->result_array();
			$this->load->view('rak/sunting_rencana_kerja_biaya_cash_flow', $data);
		} else {
			redirect('admin');
		}
	}

	public function submit_sunting_rencana_biaya_cash_flow()
	{

		$this->db->trans_start(); # Starting Transaction
		$this->db->trans_strict(FALSE); #

			$id = $this->input->post('id');
			$biaya_overhead =  str_replace('.', '', $this->input->post('biaya_overhead'));
			$biaya_bank =  str_replace('.', '', $this->input->post('biaya_bank'));
			$termin =  str_replace('.', '', $this->input->post('termin'));
			$biaya_persiapan =  str_replace('.', '', $this->input->post('biaya_persiapan'));

			$arr_update = array(
				'biaya_overhead' => $biaya_overhead,
				'biaya_bank' => $biaya_bank,
				'termin' => $termin,
				'biaya_persiapan' => $biaya_persiapan,
				'status' => 'PUBLISH',
				'updated_by' => $this->session->userdata('admin_id'),
				'updated_on' => date('Y-m-d H:i:s')
			);

			$this->db->where('id', $id);
			if ($this->db->update('rak_biaya_cash_flow', $arr_update)) {
				
			}

			if ($this->db->trans_status() === FALSE) {
				# Something went wrong.
				$this->db->trans_rollback();
				$this->session->set_flashdata('notif_error', 'Gagal Memperbaharui Rencana Kerja !!');
				redirect('admin/rencana_kerja_keu');
			} else {
				# Everything is Perfect. 
				# Committing data to the database.
				$this->db->trans_commit();
				$this->session->set_flashdata('notif_success', 'Berhasil Memperbaharui Rencana Kerja !!');
				redirect('admin/rencana_kerja_keu');
			}
	}

}
?>