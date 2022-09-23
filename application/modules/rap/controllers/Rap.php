<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rap extends Secure_Controller {

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
	
	public function form_alat()
	{
		$check = $this->m_admin->check_login();
		if ($check == true) {
			$data['products'] =  $this->db->select('*')
			->from('produk p')
			->where("p.status = 'PUBLISH'")
			->where("p.peralatan = 1 ")
			->order_by('nama_produk','asc')
			->get()->result_array();
			$this->load->view('rap/form_alat', $data);
		} else {
			redirect('admin');
		}
	}
	
	public function submit_rap()
	{
		$tanggal_rap = $this->input->post('tanggal_rap');
		$nomor_rap = $this->input->post('nomor_rap');
		$total_bahan = $this->input->post('total_bahan');
		$total_alat = $this->input->post('total_alat');
		$total_overhead = $this->input->post('total_overhead');
		$total_biaya_admin = $this->input->post('total_biaya_admin');
		$total_diskonto = $this->input->post('total_diskonto');

		$this->db->trans_start(); # Starting Transaction
		$this->db->trans_strict(FALSE); # See Note 01. If you wish can remove as well 

		$arr_insert = array(
			'tanggal_rap' =>  date('Y-m-d', strtotime($tanggal_rap)),
			'nomor_rap' => $nomor_rap,
			'total_bahan' => $total_bahan,
			'total_alat' => $total_alat,
			'total_overhead' => $total_overhead,
			'total_biaya_admin' => $total_biaya_admin,
			'total_diskonto' => $total_diskonto,
			
			'status' => 'PUBLISH',
			'created_by' => $this->session->userdata('admin_id'),
			'created_on' => date('Y-m-d H:i:s')
		);
		
		if ($this->db->insert('rap', $arr_insert)) {
			$rap_id = $this->db->insert_id();

			$data = [];
			$count = count($_FILES['files']['name']);
			for ($i = 0; $i < $count; $i++) {

				if (!empty($_FILES['files']['name'][$i])) {

					$_FILES['file']['name'] = $_FILES['files']['name'][$i];
					$_FILES['file']['type'] = $_FILES['files']['type'][$i];
					$_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
					$_FILES['file']['error'] = $_FILES['files']['error'][$i];
					$_FILES['file']['size'] = $_FILES['files']['size'][$i];

					$config['upload_path'] = 'uploads/rap';
					$config['allowed_types'] = 'jpg|jpeg|png|pdf';
					$config['file_name'] = $_FILES['files']['name'][$i];

					$this->load->library('upload', $config);

					if ($this->upload->do_upload('file')) {
						$uploadData = $this->upload->data();
						$filename = $uploadData['file_name'];

						$data['totalFiles'][] = $filename;


						$data[$i] = array(
							'rap_id' => $rap_id,
							'lampiran'  => $data['totalFiles'][$i]
						);

						$this->db->insert('lampiran_rap', $data[$i]);
					}
				}
			}
		}


		if ($this->db->trans_status() === FALSE) {
			# Something went wrong.
			$this->db->trans_rollback();
			$this->session->set_flashdata('notif_error', 'Gagal membuat RAP !!');
			redirect('admin/rap');
		} else {
			# Everything is Perfect. 
			# Committing data to the database.
			$this->db->trans_commit();
			$this->session->set_flashdata('notif_success', 'Berhasil membuat RAP !!');
			redirect('admin/rap');
		}
	}
	
	public function table_rap()
	{   
        $data = array();

        $this->db->select('rap.id, rap.tanggal_rap, rap.nomor_rap, rap.total_bahan, rap.total_alat, rap.total_overhead, rap.total_biaya_admin, rap.total_diskonto, lk.lampiran, rap.status');		
		$this->db->join('lampiran_rap lk', 'rap.id = lk.rap_id','left');
		$this->db->where('status','PUBLISH');
		$this->db->order_by('rap.nomor_rap','desc');
		$this->db->order_by('rap.nomor_rap','desc');			
		$query = $this->db->get('rap rap');
		
		//file_put_contents("D:\\table_rap.txt", $this->db->last_query());
		
       if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
                $row['no'] = $key+1;
				$row['tanggal_rap'] =  date('d F Y',strtotime($row['tanggal_rap']));
				$row['nomor_rap'] = "<a href=" . base_url('rap/cetak_rap/' . $row["id"]) . ">" . $row["nomor_rap"] . "</a>";
				$row['total_bahan'] = number_format($row['total_bahan'],0,',','.');
				$row['total_alat'] = number_format($row['total_alat'],0,',','.');
				$row['total_overhead'] = number_format($row['total_overhead'],0,',','.');
				$row['total_biaya_admin'] = number_format($row['total_biaya_admin'],0,',','.');
				$row['total_diskonto'] = number_format($row['total_diskonto'],0,',','.');
				$row['lampiran'] = '<a href="' . base_url('uploads/rap/' . $row['lampiran']) .'" target="_blank">' . $row['lampiran'] . '</a>';  
				//$row['actions'] = "<a href=" . base_url('rap/hapus_rap/' . $row["id"]) . ">" . "<button class='btn btn-danger'>Hapus</button>" . "</a>";
				$row['actions'] = '<a href="javascript:void(0);" onclick="DeleteData('.$row['id'].')" class="btn btn-danger"><i class="fa fa-close"></i> </a>';
				
                $data[] = $row;
            }

        }
        echo json_encode(array('data'=>$data));
    }

	public function delete_rap()
	{
		$output['output'] = false;
		$id = $this->input->post('id');
		if(!empty($id)){
			$this->db->delete('rap',array('id'=>$id));
			{
				$output['output'] = true;
			}
		}
		echo json_encode($output);
	}
	
	public function cetak_rap($id){

		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
		$pdf->setPrintFooter(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		$pdf->AddPage('P');

		$data['rap'] = $this->db->get_where('rap',array('id'=>$id))->row_array();
        $html = $this->load->view('rap/cetak_rap',$data,TRUE);
        $rap = $this->db->get_where('rap',array('id'=>$id))->row_array();


        
        $pdf->SetTitle($rap['nomor_rap']);
        $pdf->nsi_html($html);
        $pdf->Output($rap['nomor_rap'].'.pdf', 'I');
	}
	

}
?>