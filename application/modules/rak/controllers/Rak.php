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
			$this->load->view('rak/form_rencana_kerja', $data);
		} else {
			redirect('admin');
		}
	}
	
	public function submit_rencana_kerja()
	{
		$tanggal_rencana_kerja = $this->input->post('tanggal_rencana_kerja');
		$nomor_rencana_kerja = $this->input->post('nomor_rencana_kerja');
		$vol_produk_a =  str_replace('.', '', $this->input->post('vol_produk_a'));
		$vol_produk_a =  str_replace(',', '.', $vol_produk_a);
		$vol_produk_b =  str_replace('.', '', $this->input->post('vol_produk_b'));
		$vol_produk_b =  str_replace(',', '.', $vol_produk_b);
		$vol_produk_c =  str_replace('.', '', $this->input->post('vol_produk_c'));
		$vol_produk_c =  str_replace(',', '.', $vol_produk_c);
		$vol_produk_d =  str_replace('.', '', $this->input->post('vol_produk_d'));
		$vol_produk_d =  str_replace(',', '.', $vol_produk_d);
		$biaya_overhead =  str_replace('.', '', $this->input->post('biaya_overhead'));
		$biaya_bank =  str_replace('.', '', $this->input->post('biaya_bank'));
		$biaya_persiapan =  str_replace('.', '', $this->input->post('biaya_persiapan'));

		$this->db->trans_start(); # Starting Transaction
		$this->db->trans_strict(FALSE); # See Note 01. If you wish can remove as well 

		$arr_insert = array(
			'tanggal_rencana_kerja' =>  date('Y-m-d', strtotime($tanggal_rencana_kerja)),
			'nomor_rencana_kerja' => $nomor_rencana_kerja,
			'vol_produk_a' => $vol_produk_a,
			'vol_produk_b' => $vol_produk_b,
			'vol_produk_c' => $vol_produk_c,
			'vol_produk_d' => $vol_produk_d,
			'biaya_overhead' => $biaya_overhead,
			'biaya_bank' => $biaya_bank,
			'biaya_persiapan' => $biaya_persiapan,
			
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
				$row['tanggal_rencana_kerja'] =  date('d F Y',strtotime($row['tanggal_rencana_kerja']));
				$row['nomor_rencana_kerja'] = "<a href=" . base_url('rak/cetak_rencana_kerja/' . $row["id"]) .'" target="_blank">' . $row["nomor_rencana_kerja"] . "</a>";
				$row['lampiran'] = '<a href="' . base_url('uploads/rak/' . $row['lampiran']) .'" target="_blank">' . $row['lampiran'] . '</a>';  
				$row['actions'] = '<a href="javascript:void(0);" onclick="DeleteData('.$row['id'].')" class="btn btn-danger"><i class="fa fa-close"></i> </a>';
				
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


        
        $pdf->SetTitle($rak['nomor_rencana_kerja']);
        $pdf->nsi_html($html);
        $pdf->Output($rak['nomor_rencana_kerja'].'.pdf', 'I');
	}
	

}
?>