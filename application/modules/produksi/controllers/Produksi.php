<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produksi extends Secure_Controller {

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
	
	public function remaining_material_print()
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

		$this->db->where('status','PUBLISH');
		$this->db->order_by('date','desc');
		$this->db->order_by('id','desc');
		if(!empty($this->input->get('material_id'))){
			$this->db->where('material_id',$this->input->post('material_id'));
		}
		$filter_date = $this->input->get('filter_date');
		if(!empty($filter_date)){
			$arr_date = explode(' - ', $filter_date);
			$this->db->where('date >=',date('Y-m-d',strtotime($arr_date[0])));
			$this->db->where('date <=',date('Y-m-d',strtotime($arr_date[1])));
		}
		$query = $this->db->get('pmm_remaining_materials_cat');
		$data['data'] = $query->result_array();
		$data['custom_date'] = $this->input->get('custom_date');
        $html = $this->load->view('produksi/remaining_material_print',$data,TRUE);

        
        $pdf->SetTitle('Stock Opname');
        $pdf->nsi_html($html);
        $pdf->Output('Stock Opname.pdf', 'I');
	
	}

	
	public function form_komposisi()
	{
		$check = $this->m_admin->check_login();
		if ($check == true) {
			$data['products'] = $this->db->select('*')->get_where('produk', array('status' => 'PUBLISH', 'bahanbaku' => 1))->result_array();
			$data['mutu_beton'] = $this->db->select('*')->get_where('produk', array('status' => 'PUBLISH', 'betonreadymix' => 1))->result_array();
			$data['measures'] = $this->db->select('*')->get_where('pmm_measures', array('status' => 'PUBLISH'))->result_array();
			$data['slump'] = $this->db->select('*')->get_where('pmm_slump', array('status' => 'PUBLISH'))->result_array();
			$this->load->view('produksi/form_komposisi', $data);
		} else {
			redirect('admin');
		}
	}
	
	public function submit_agregat()
	{
		$date_agregat = $this->input->post('date_agregat');
		$jobs_type = $this->input->post('jobs_type');
		$mutu_beton = $this->input->post('mutu_beton');
		$volume = str_replace(',', '.', $this->input->post('volume'));
		$measure = $this->input->post('measure');
		$tes = $this->input->post('tes');
		$measure_a = $this->input->post('measure_a');
		$measure_b = $this->input->post('measure_b');
		$measure_c = $this->input->post('measure_c');
		$measure_d = $this->input->post('measure_d');
		$produk_a = $this->input->post('produk_a');
		$produk_b = $this->input->post('produk_b');
		$produk_c = $this->input->post('produk_c');
		$produk_d = $this->input->post('produk_d');
		$presentase_a = str_replace(',', '.', $this->input->post('presentase_a'));
		$presentase_b = str_replace(',', '.', $this->input->post('presentase_b'));
		$presentase_c = str_replace(',', '.', $this->input->post('presentase_c'));
		$presentase_d = str_replace(',', '.', $this->input->post('presentase_d'));
		$price_a = $this->input->post('price_a');
		$price_b = $this->input->post('price_b');
		$price_c = $this->input->post('price_c');
		$price_d = $this->input->post('price_d');
		$total_a = $this->input->post('total_a');
		$total_b = $this->input->post('total_b');
		$total_c = $this->input->post('total_c');
		$total_d = $this->input->post('total_d');
		$memo = $this->input->post('memo');
		$attach = $this->input->post('files[]');

		$this->db->trans_start(); # Starting Transaction
		$this->db->trans_strict(FALSE); # See Note 01. If you wish can remove as well 

		$arr_insert = array(
			'date_agregat' => date('Y-m-d', strtotime($date_agregat)),	
			'jobs_type' => $jobs_type,
			'mutu_beton' => $mutu_beton,
			'volume' => $volume,
			'measure' => $measure,
			'tes' => $tes,
			'measure_a' => $measure_a,
			'measure_b' => $measure_b,
			'measure_c' => $measure_c,
			'measure_d' => $measure_d,			
			'produk_a' => $produk_a,
			'produk_b' => $produk_b,
			'produk_c' => $produk_c,
			'produk_d' => $produk_d,
			'presentase_a' => $presentase_a,
			'presentase_b' => $presentase_b,
			'presentase_c' => $presentase_c,
			'presentase_d' => $presentase_d,
			'price_a' => $price_a,
			'price_b' => $price_b,
			'price_c' => $price_c,
			'price_d' => $price_d,
			'total_a' => $total_a,
			'total_b' => $total_b,
			'total_c' => $total_c,
			'total_d' => $total_d,
			'memo' => $memo,
			'attach' => $attach,
			'status' => 'PUBLISH',
			'created_by' => $this->session->userdata('admin_id'),
			'created_on' => date('Y-m-d H:i:s')
		);

		if ($this->db->insert('pmm_agregat', $arr_insert)) {
			$agregat_id = $this->db->insert_id();

			$data = [];
			$count = count($_FILES['files']['name']);
			for ($i = 0; $i < $count; $i++) {

				if (!empty($_FILES['files']['name'][$i])) {

					$_FILES['file']['name'] = $_FILES['files']['name'][$i];
					$_FILES['file']['type'] = $_FILES['files']['type'][$i];
					$_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
					$_FILES['file']['error'] = $_FILES['files']['error'][$i];
					$_FILES['file']['size'] = $_FILES['files']['size'][$i];

					$config['upload_path'] = 'uploads/agregat';
					$config['allowed_types'] = 'jpg|jpeg|png|pdf';
					$config['file_name'] = $_FILES['files']['name'][$i];

					$this->load->library('upload', $config);

					if ($this->upload->do_upload('file')) {
						$uploadData = $this->upload->data();
						$filename = $uploadData['file_name'];

						$data['totalFiles'][] = $filename;


						$data[$i] = array(
							'agregat_id' => $agregat_id,
							'lampiran'  => $data['totalFiles'][$i]
						);

						$this->db->insert('pmm_lampiran_agregat', $data[$i]);
						
					} 
				}
			}
		}


		if ($this->db->trans_status() === FALSE) {
			# Something went wrong.
			$this->db->trans_rollback();
			$this->session->set_flashdata('notif_error', 'Gagal membuat Komposisi Agregat !!');
			redirect('produksi/komposisi_agregat');
		} else {
			# Everything is Perfect. 
			# Committing data to the database.
			$this->db->trans_commit();
			$this->session->set_flashdata('notif_success', 'Berhasil membuat Komposisi Agregat  !!');
			redirect('admin/produksi');
		}
	}
	
	public function table_agregat()
	{   
        $data = array();
		$filter_date = $this->input->post('filter_date');
		if(!empty($filter_date)){
			$arr_date = explode(' - ', $filter_date);
			$this->db->where('ag.date_agregat >=',date('Y-m-d',strtotime($arr_date[0])));
			$this->db->where('ag.date_agregat <=',date('Y-m-d',strtotime($arr_date[1])));
		}
        $this->db->select('ag.id, ag.jobs_type, ag.date_agregat, p.nama_produk as mutu_beton, lk.agregat_id, lk.lampiran, ag.status');
		$this->db->join('pmm_lampiran_agregat lk', 'ag.id = lk.agregat_id','left');
		$this->db->join('produk p', 'ag.mutu_beton = p.id','left');	
		$this->db->order_by('ag.date_agregat','desc');		
		$query = $this->db->get('pmm_agregat ag');
		//file_put_contents("D:\\table_aggregat.txt", $this->db->last_query());
		
       if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
                $row['no'] = $key+1;
				$row['jobs_type'] = "<a href=" . base_url('produksi/data_komposisi/' . $row["id"]) . ">" . $row["jobs_type"] . "</a>";
                $row['date_agregat'] = date('d F Y',strtotime($row['date_agregat']));
				$row['mutu_beton'] = "<a href=" . base_url('produksi/cetak_komposisi/' . $row["id"]) .'" target="_blank">'. $row["mutu_beton"] . "</a>";
				$row['lampiran'] = '<a href="' . base_url('uploads/agregat/' . $row['lampiran']) .'" target="_blank">' . $row['lampiran'] . '</a>';           
                $data[] = $row;
            }

        }
        echo json_encode(array('data'=>$data));
    }
	
	public function hapus_komposisi_agregat($id)
    {
        $this->db->trans_start(); # Starting Transaction


        $this->db->delete('pmm_agregat', array('id' => $id));

        if ($this->db->trans_status() === FALSE) {
            # Something went wrong.
            $this->db->trans_rollback();
            $this->session->set_flashdata('notif_error', 'Gagal Menghapus Komposisi Agregat');
            redirect('produksi/data_komposisi_agregat');
        } else {
            # Everything is Perfect. 
            # Committing data to the database.
            $this->db->trans_commit();
            $this->session->set_flashdata('notif_success', 'Berhasil Menghapus Komposisi Agregat');
            redirect("admin/produksi");
        }
    }
	
	public function data_komposisi($id)
	{
		$check = $this->m_admin->check_login();
		if ($check == true) {
			$data['tes'] = '';
			$data['agregat'] = $this->db->get_where("pmm_agregat", ["id" => $id])->row_array();
			$data['lampiran'] = $this->db->get_where("pmm_lampiran_agregat", ["agregat_id" => $id])->result_array();
			$this->load->view('produksi/data_komposisi', $data);
			//file_put_contents("D:\\data_kalibrasi.txt", $this->db->last_query());
		} else {
			redirect('admin');
		}
	}

	public function sunting_komposisi($id)
	{
		$check = $this->m_admin->check_login();
		if ($check == true) {
			$data['tes'] = '';
			$data['agregat'] = $this->db->get_where("pmm_agregat", ["id" => $id])->row_array();
			$data['lampiran'] = $this->db->get_where("pmm_lampiran_agregat", ["agregat_id" => $id])->result_array();
			$this->load->view('produksi/sunting_komposisi', $data);
		} else {
			redirect('admin');
		}
	}

	public function submit_sunting_agregat()
	{

		$this->db->trans_start(); # Starting Transaction
		$this->db->trans_strict(FALSE); #

			$id = $this->input->post('id');

			$arr_update = array(
				'presentase_a' => str_replace(',', '.', $this->input->post('presentase_a')),
				'presentase_b' => str_replace(',', '.', $this->input->post('presentase_b')),
				'presentase_c' => str_replace(',', '.', $this->input->post('presentase_c')),
				'presentase_d' => str_replace(',', '.', $this->input->post('presentase_d')),
				'price_a' => $this->input->post('price_a'),
				'price_b' => $this->input->post('price_b'),
				'price_c' => $this->input->post('price_c'),
				'price_d' => $this->input->post('price_d'),
				'total_a'=> $this->input->post('total_a'),
				'total_b' => $this->input->post('total_b'),
				'total_c' => $this->input->post('total_c'),
				'total_d' => $this->input->post('total_d'),
				'updated_by' => $this->session->userdata('admin_id'),
				'updated_on' => date('Y-m-d H:i:s')
			);

			$this->db->where('id', $id);
			if ($this->db->update('pmm_agregat', $arr_update)) {
				
			}

			if ($this->db->trans_status() === FALSE) {
				# Something went wrong.
				$this->db->trans_rollback();
				$this->session->set_flashdata('notif_error', 'Gagal Memperbaharui Data Data Komposisi !!');
				redirect('produksi/komposisi_agregat/' . $this->input->post('id_penagihan'));
			} else {
				# Everything is Perfect. 
				# Committing data to the database.
				$this->db->trans_commit();
				$this->session->set_flashdata('notif_success', 'Berhasil Memperbaharui Data Data Komposisi !!');
				redirect('admin/produksi/' . $this->input->post('id_penagihan'));
			}
	}
	
	public function cetak_komposisi($id){

		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		$pdf->AddPage('P');

		$data['row'] = $this->db->get_where('pmm_agregat',array('id'=>$id))->row_array();
        $html = $this->load->view('produksi/cetak_komposisi',$data,TRUE);
        $row = $this->db->get_where('pmm_agregat',array('id'=>$id))->row_array();


        
        $pdf->SetTitle($row['jobs_type']);
        $pdf->nsi_html($html);
        $pdf->Output($row['jobs_type'].'.pdf', 'I');
	}

	public function form_hpp_bahan_baku()
	{
		$check = $this->m_admin->check_login();
		if ($check == true) {
			$data['products'] = $this->db->select('*')->get_where('produk', array('status' => 'PUBLISH', 'bahanbaku' => 1))->result_array();
			$this->load->view('produksi/form_hpp_bahan_baku', $data);
		} else {
			redirect('admin');
		}
	}

	public function submit_hpp_bahan_baku()
	{
		$date_hpp = $this->input->post('date_hpp');
		$semen = $this->input->post('semen');
		$pasir = $this->input->post('pasir');
		$batu1020 = $this->input->post('batu1020');
		$batu2030 = $this->input->post('batu2030');
		$solar = $this->input->post('solar');

		$this->db->trans_start(); # Starting Transaction
		$this->db->trans_strict(FALSE); # See Note 01. If you wish can remove as well 

		$arr_insert = array(
			'date_hpp' => date('Y-m-d', strtotime($date_hpp)),
			'semen' => $semen,
			'pasir' => $pasir,
			'batu1020' => $batu1020,
			'batu2030' => $batu2030,
			'solar' => $solar,
			'status' => 'PUBLISH',
			'created_by' => $this->session->userdata('admin_id'),
			'created_on' => date('Y-m-d H:i:s')
		);

		$this->db->insert('hpp_bahan_baku', $arr_insert);

		if ($this->db->trans_status() === FALSE) {
			# Something went wrong.
			$this->db->trans_rollback();
			$this->session->set_flashdata('notif_error', 'Gagal membuat HPP Bahan Baku !!');
			redirect('produksi/hpp_bahan_baku');
		} else {
			# Everything is Perfect. 
			# Committing data to the database.
			$this->db->trans_commit();
			$this->session->set_flashdata('notif_success', 'Berhasil membuat HPP Bahan Baku !!');
			redirect('admin/produksi');
		}
	}

	public function table_hpp_bahan_baku()
	{   
        $data = array();
		$filter_date = $this->input->post('filter_date');
		if(!empty($filter_date)){
			$arr_date = explode(' - ', $filter_date);
			$this->db->where('pp.date_hpp >=',date('Y-m-d',strtotime($arr_date[0])));
			$this->db->where('pp.date_hpp <=',date('Y-m-d',strtotime($arr_date[1])));
		}
        $this->db->select('pp.id, pp.date_hpp, pp.semen, pp.pasir, pp.batu1020, pp.batu2030, pp.solar, pp.status');
		$this->db->order_by('pp.date_hpp','desc');
		$query = $this->db->get('hpp_bahan_baku pp');
		
		//file_put_contents("D:\\table_hpp_bahan_baku.txt", $this->db->last_query());
		
       if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
                $row['no'] = $key+1;
                $row['date_hpp'] = date('d F Y',strtotime($row['date_hpp']));
                $row['semen'] = number_format($row['semen'],0,',','.');
				$row['pasir'] = number_format($row['pasir'],0,',','.');
				$row['batu1020'] = number_format($row['batu1020'],0,',','.');
				$row['batu2030'] = number_format($row['batu2030'],0,',','.');
				$row['solar'] = number_format($row['solar'],0,',','.');
				$row['status'] = $row['status'];
				$row['actions'] = '<a href="javascript:void(0);" onclick="DeleteDataHppBahanBaku('.$row['id'].')" class="btn btn-danger"><i class="fa fa-close"></i> </a>';
                
                $data[] = $row;
            }

        }
        echo json_encode(array('data'=>$data));
    }

	public function delete_hpp_bahan_baku()
	{
		$output['output'] = false;
		$id = $this->input->post('id');
		if(!empty($id)){
			$this->db->delete('hpp_bahan_baku',array('id'=>$id));
			{
				$output['output'] = true;
			}
		}
		echo json_encode($output);
	}

	public function form_akumulasi()
	{
		$check = $this->m_admin->check_login();
		if ($check == true) {
			$data['products'] = $this->db->select('*')->get_where('produk', array('status' => 'PUBLISH', 'bahanbaku' => 1))->result_array();
			$this->load->view('produksi/form_akumulasi_bahan_baku', $data);
		} else {
			redirect('admin');
		}
	}

	public function submit_akumulasi()
	{
		$date_akumulasi = $this->input->post('date_akumulasi');
		$total_nilai_keluar = $this->input->post('total_nilai_keluar');
		$total_nilai_keluar_2 = $this->input->post('total_nilai_keluar_2');

		$this->db->trans_start(); # Starting Transaction
		$this->db->trans_strict(FALSE); # See Note 01. If you wish can remove as well 

		$arr_insert = array(
			'date_akumulasi' => date('Y-m-d', strtotime($date_akumulasi)),
			'total_nilai_keluar' => $total_nilai_keluar,
			'total_nilai_keluar_2' => $total_nilai_keluar_2,
			'status' => 'PUBLISH',
			'created_by' => $this->session->userdata('admin_id'),
			'created_on' => date('Y-m-d H:i:s')
		);

		$this->db->insert('akumulasi', $arr_insert);

		if ($this->db->trans_status() === FALSE) {
			# Something went wrong.
			$this->db->trans_rollback();
			$this->session->set_flashdata('notif_error', 'Gagal membuat Akumulasi Pergerakan Bahan Baku !!');
			redirect('produksi/akumulasi');
		} else {
			# Everything is Perfect. 
			# Committing data to the database.
			$this->db->trans_commit();
			$this->session->set_flashdata('notif_success', 'Berhasil membuat Akumulasi Pergerakan Bahan Baku !!');
			redirect('admin/produksi');
		}
	}

	public function table_akumulasi()
	{   
        $data = array();
		$filter_date = $this->input->post('filter_date');
		if(!empty($filter_date)){
			$arr_date = explode(' - ', $filter_date);
			$this->db->where('pp.date_akumulasi >=',date('Y-m-d',strtotime($arr_date[0])));
			$this->db->where('pp.date_akumulasi <=',date('Y-m-d',strtotime($arr_date[1])));
		}
        $this->db->select('pp.id, pp.date_akumulasi, pp.total_nilai_keluar, pp.total_nilai_keluar_2, pp.status');
		$this->db->order_by('pp.date_akumulasi','desc');
		$query = $this->db->get('akumulasi pp');
		
		//file_put_contents("D:\\table_akumulasi.txt", $this->db->last_query());
		
       if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
                $row['no'] = $key+1;
                $row['date_akumulasi'] = date('d F Y',strtotime($row['date_akumulasi']));
                $row['total_nilai_keluar'] = number_format($row['total_nilai_keluar'],0,',','.');
				$row['total_nilai_keluar_2'] = number_format($row['total_nilai_keluar_2'],0,',','.');
				$row['status'] = $row['status'];
				$row['actions'] = '<a href="javascript:void(0);" onclick="DeleteDataAkumulasi('.$row['id'].')" class="btn btn-danger"><i class="fa fa-close"></i> </a>';
                
                $data[] = $row;
            }

        }
        echo json_encode(array('data'=>$data));
    }

	public function delete_akumulasi()
	{
		$output['output'] = false;
		$id = $this->input->post('id');
		if(!empty($id)){
			$this->db->delete('akumulasi',array('id'=>$id));
			{
				$output['output'] = true;
			}
		}
		echo json_encode($output);
	}

}
?>