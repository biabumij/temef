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
	
	public function submit_rap_alat()
	{
		$tanggal_rap_alat = $this->input->post('tanggal_rap_alat');
		$nomor_rap_alat = $this->input->post('nomor_rap_alat');
		$batching_plant =  str_replace('.', '', $this->input->post('batching_plant'));
		$truck_mixer =  str_replace('.', '', $this->input->post('truck_mixer'));
		$wheel_loader =  str_replace('.', '', $this->input->post('wheel_loader'));
		$bbm_solar =  str_replace('.', '', $this->input->post('bbm_solar'));

		$this->db->trans_start(); # Starting Transaction
		$this->db->trans_strict(FALSE); # See Note 01. If you wish can remove as well 

		$arr_insert = array(
			'tanggal_rap_alat' =>  date('Y-m-d', strtotime($tanggal_rap_alat)),
			'nomor_rap_alat' => $nomor_rap_alat,
			'batching_plant' => $batching_plant,
			'truck_mixer' => $truck_mixer,
			'wheel_loader' => $wheel_loader,
			'bbm_solar' => $bbm_solar,
			
			'status' => 'PUBLISH',
			'created_by' => $this->session->userdata('admin_id'),
			'created_on' => date('Y-m-d H:i:s')
		);
		
		if ($this->db->insert('rap_alat', $arr_insert)) {
			$rap_id = $this->db->insert_id();

			if (!file_exists('uploads/rap_alat')) {
			    mkdir('uploads/rap_alat', 0777, true);
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

					$config['upload_path'] = 'uploads/rap_alat';
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

						$this->db->insert('lampiran_rap_alat', $data[$i]);
					}
				}
			}
		}


		if ($this->db->trans_status() === FALSE) {
			# Something went wrong.
			$this->db->trans_rollback();
			$this->session->set_flashdata('notif_error', 'Gagal membuat RAP Alat !!');
			redirect('admin/rap');
		} else {
			# Everything is Perfect. 
			# Committing data to the database.
			$this->db->trans_commit();
			$this->session->set_flashdata('notif_success', 'Berhasil membuat RAP Alat !!');
			redirect('admin/rap');
		}
	}
	
	public function table_rap_alat()
	{   
        $data = array();

        $this->db->select('rap.*, lk.lampiran, rap.status');		
		$this->db->join('lampiran_rap_alat lk', 'rap.id = lk.rap_id','left');
		$this->db->where('rap.status','PUBLISH');
		$this->db->order_by('rap.nomor_rap_alat','desc');
		$this->db->order_by('rap.nomor_rap_alat','desc');			
		$query = $this->db->get('rap_alat rap');
		
       if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
                $row['no'] = $key+1;
				$row['tanggal_rap_alat'] =  date('d F Y',strtotime($row['tanggal_rap_alat']));
				$row['nomor_rap_alat'] = "<a href=" . base_url('rap/cetak_rap_alat/' . $row["id"]) .'" target="_blank">' . $row["nomor_rap_alat"] . "</a>";
				$row['batching_plant'] = number_format($row['batching_plant'],0,',','.');
				$row['truck_mixer'] = number_format($row['truck_mixer'],0,',','.');
				$row['wheel_loader'] = number_format($row['wheel_loader'],0,',','.');
				$row['bbm_solar'] = number_format($row['bbm_solar'],0,',','.');
				$row['lampiran'] = '<a href="' . base_url('uploads/rap_alat/' . $row['lampiran']) .'" target="_blank">' . $row['lampiran'] . '</a>';  
				//$row['actions'] = "<a href=" . base_url('rap/hapus_rap_alat/' . $row["id"]) . ">" . "<button class='btn btn-danger'>Hapus</button>" . "</a>";
				$row['actions'] = '<a href="javascript:void(0);" onclick="DeleteData('.$row['id'].')" class="btn btn-danger"><i class="fa fa-close"></i> </a>';
				
                $data[] = $row;
            }

        }
        echo json_encode(array('data'=>$data));
    }

	public function delete_rap_alat()
	{
		$output['output'] = false;
		$id = $this->input->post('id');
		if(!empty($id)){
			$this->db->delete('rap_alat',array('id'=>$id));
			{
				$output['output'] = true;
			}
		}
		echo json_encode($output);
	}
	
	public function cetak_rap_alat($id){

		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
		$pdf->setPrintFooter(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		$pdf->AddPage('P');

		$data['rap_alat'] = $this->db->get_where('rap_alat',array('id'=>$id))->row_array();
        $html = $this->load->view('rap/cetak_rap_alat',$data,TRUE);
        $rap_alat = $this->db->get_where('rap_alat',array('id'=>$id))->row_array();


        
        $pdf->SetTitle($rap_alat['nomor_rap_alat']);
        $pdf->nsi_html($html);
        $pdf->Output($rap_alat['nomor_rap_alat'].'.pdf', 'I');
	}

	public function form_bua()
	{
		$check = $this->m_admin->check_login();
		if ($check == true) {
			$data['coa'] =  $this->db->select('*')
			->from('pmm_coa c')
			->where("c.status = 'PUBLISH'")
			->where("c.coa_category = 15 ")
			->order_by('c.coa_number','asc')
			->get()->result_array();
			$data['satuan'] = $this->db->order_by('measure_name', 'asc')->select('*')->get_where('pmm_measures', array('status' => 'PUBLISH'))->result_array();
			$this->load->view('rap/form_bua', $data);
		} else {
			redirect('admin');
		}
	}

	public function add_coa()
    {
        $no = $this->input->post('no');
        $coa = $this->db->select('*')
		->from('pmm_coa c')
		->where("c.status = 'PUBLISH'")
		->where("c.coa_category = 15 ")
		->order_by('c.coa_number','asc')
		->get()->result_array();
		$satuan = $this->db->order_by('measure_name', 'asc')->select('*')->get_where('pmm_measures', array('status' => 'PUBLISH'))->result_array();
	?>
        <tr>
            <td><?php echo $no; ?>.</td>
            <td>
				<select id="coa-<?php echo $no; ?>" onchange="changeData(<?php echo $no; ?>)" class="form-control form-select2" name="coa_<?php echo $no; ?>">
					<option value="">Pilih Akun</option>
					<?php
					if(!empty($coa)){
						foreach ($coa as $row) {
							?>
							<option value="<?php echo $row['id'];?>"><?php echo $row['coa'];?></option>
							<?php
						}
					}
					?>
				</select>
			</td>
            <td>
				<input type="number" min="0" name="qty_<?php echo $no; ?>" id="qty-<?php echo $no; ?>" onchange="changeData(<?php echo $no; ?>)" class="form-control input-sm text-center" />
			</td>
			<td>
				<select id="satuan-<?php echo $no; ?>" class="form-control form-select2" name="satuan_<?php echo $no; ?>" required="">
						<option value="">Pilih Satuan</option>
						<?php
						if(!empty($satuan)){
							foreach ($satuan as $sat) {
								?>
								<option value="<?php echo $sat['measure_name'];?>"><?php echo $sat['measure_name'];?></option>
								<?php
							}
						}
						?>
					</select>
				</td>
			<td>
				<input type="text" name="harga_satuan_<?php echo $no; ?>" id="harga_satuan-<?php echo $no; ?>" class="form-control numberformat tex-left input-sm text-right" onchange="changeData(<?php echo $no; ?>)" />
			</td>
			<td>
				<input type="text" name="jumlah_<?php echo $no; ?>" id="jumlah-<?php echo $no; ?>" class="form-control numberformat tex-left input-sm text-right" readonly="" />
			</td>
		</tr>

        <script type="text/javascript">
            $('.form-select2').select2();
            $('input.numberformat').number(true, 0, ',', '.');
        </script>
    <?php
    }

	public function submit_rap_bua()
    {
		$tanggal_rap_bua = $this->input->post('tanggal_rap_bua');
		$nomor_rap_bua = $this->input->post('nomor_rap_bua');
        $total_product = $this->input->post('total_product');
        $total = $this->input->post('total');

        $arr_insert = array(
            'tanggal_rap_bua' => date('Y-m-d', strtotime($tanggal_rap_bua)),
			'nomor_rap_bua' => $nomor_rap_bua,
            'total' => $total,
            'created_by' => $this->session->userdata('admin_id'),
            'created_on' => date('Y-m-d H:i:s'),
            'status' => 'PUBLISH'
        );


        if ($this->db->insert('rap_bua', $arr_insert)) {
            $rap_bua_id = $this->db->insert_id();

            for ($i = 1; $i <= $total_product; $i++) {
				$coa = $this->input->post('coa_' . $i);
				$qty = $this->input->post('qty_' . $i);
				$satuan = $this->input->post('satuan_' . $i);


				$harga_satuan = $this->input->post('harga_satuan_' . $i);
				$harga_satuan = str_replace('.', '', $harga_satuan);
				$harga_satuan = str_replace(',', '.', $harga_satuan);

				$jumlah_pro = $this->input->post('jumlah_' . $i);
				$jumlah_pro = str_replace('.', '', $jumlah_pro);
				$jumlah_pro = str_replace(',', '.', $jumlah_pro);

				if (!empty($coa)) {

                    $arr_detail = array(
                        'rap_bua_id' => $rap_bua_id,
                        'coa' => $coa,
                        'qty' => $qty,
                        'satuan' => $satuan,
                        'harga_satuan' => $harga_satuan,
                        'jumlah' => $jumlah_pro
                    );

                    $this->db->insert('rap_bua_detail', $arr_detail);
                } else {
                    redirect('rap/rap');
                    exit();
                }
            }

			if (!file_exists('uploads/rap_bua')) {
			    mkdir('uploads/rap_bu', 0777, true);
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

					$config['upload_path'] = 'uploads/rap_alat';
					$config['allowed_types'] = 'jpg|jpeg|png|pdf';
					$config['file_name'] = $_FILES['files']['name'][$i];

					$this->load->library('upload', $config);

					if ($this->upload->do_upload('file')) {
						$uploadData = $this->upload->data();
						$filename = $uploadData['file_name'];

						$data['totalFiles'][] = $filename;


						$data[$i] = array(
							'rap_bua_id' => $rap_bua_id,
							'lampiran'  => $data['totalFiles'][$i]
						);

						$this->db->insert('lampiran_rap_bua', $data[$i]);
					}
				}
			}


        }

        if ($this->db->trans_status() === FALSE) {
            # Something went wrong.
            $this->db->trans_rollback();
            $this->session->set_flashdata('notif_error', 'Gagal Menambahkan RAP BUA !!');
            redirect('rap/rap');
        } else {
            # Everything is Perfect. 
            # Committing data to the database.
            $this->db->trans_commit();
            $this->session->set_flashdata('notif_success', 'Berhasil Menambahkan RAP BUA !!');
            redirect('admin/rap');
        }
    }

	public function table_rap_bua()
	{   
        $data = array();

        $this->db->select('rap.*, lk.lampiran, rap.status');		
		$this->db->join('lampiran_rap_bua lk', 'rap.id = lk.rap_bua_id','left');
		$this->db->where('rap.status','PUBLISH');
		$this->db->group_by('rap.id');
		$this->db->order_by('rap.nomor_rap_bua','desc');			
		$query = $this->db->get('rap_bua rap');
		
       if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
                $row['no'] = $key+1;
				$row['tanggal_rap_bua'] =  date('d F Y',strtotime($row['tanggal_rap_bua']));
				$row['nomor_rap_bua'] = "<a href=" . base_url('rap/cetak_rap_bua/' . $row["id"]) .' target="_blank">' . $row["nomor_rap_bua"] . "</a>";
				$row['lampiran'] = '<a href="' . base_url('uploads/rap_bua/' . $row['lampiran']) .'" target="_blank">' . $row['lampiran'] . '</a>';
				$row['actions'] = '<a href="javascript:void(0);" onclick="DeleteDataBUA('.$row['id'].')" class="btn btn-danger"><i class="fa fa-close"></i> </a>';
				
                $data[] = $row;
            }

        }
        echo json_encode(array('data'=>$data));
    }

	public function delete_rap_bua()
	{
		$output['output'] = false;
		$id = $this->input->post('id');
		if(!empty($id)){
			$this->db->delete('rap_bua',array('id'=>$id));
			$this->db->delete('rap_bua_detail',array('rap_bua_id'=>$id));
			{
				$output['output'] = true;
			}
		}
		echo json_encode($output);
	}

	public function cetak_rap_bua($id){

		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		$pdf->AddPage('P');

		$data['id'] = $id;
        $html = $this->load->view('rap/cetak_rap_bua',$data,TRUE);
        
        $pdf->SetTitle('RAP BUA');
        $pdf->nsi_html($html);
		$pdf->Output('rap_bua.pdf', 'I');
	}
	

}
?>