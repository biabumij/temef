<!DOCTYPE html>
<html>
	<head>
	  <title>ALAT</title>
	  
	  <?php
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
		
		$subject = "$filter_date";

		echo str_replace($search, $replace, $subject);

	  ?>
	  
	  <style type="text/css">
		 body {
			font-family: helvetica;
		}

		table.table-border-pojok-kiri, th.table-border-pojok-kiri, td.table-border-pojok-kiri {
			border-top: 1px solid black;
			border-bottom: 1px solid black;
			border-right: 1px solid #cccccc;
			border-left: 1px solid black;
		}

		table.table-border-pojok-tengah, th.table-border-pojok-tengah, td.table-border-pojok-tengah {
			border-top: 1px solid black;
			border-bottom: 1px solid black;
			border-right: 1px solid #cccccc;
		}

		table.table-border-pojok-kanan, th.table-border-pojok-kanan, td.table-border-pojok-kanan {
			border-top: 1px solid black;
			border-bottom: 1px solid black;
			border-right: 1px solid black;
		}

		table.table-border-spesial, th.table-border-spesial, td.table-border-spesial {
			border-left: 1px solid black;
			border-right: 1px solid black;
		}

		table.table-border-spesial-kiri, th.table-border-spesial-kiri, td.table-border-spesial-kiri {
			border-left: 1px solid black;
			border-top: 2px solid black;
			border-bottom: 2px solid black;
		}

		table.table-border-spesial-tengah, th.table-border-spesial-tengah, td.table-border-spesial-tengah {
			border-left: 1px solid #cccccc;
			border-right: 1px solid #cccccc;
			border-top: 2px solid black;
			border-bottom: 2px solid black;
		}

		table.table-border-spesial-kanan, th.table-border-spesial-kanan, td.table-border-spesial-kanan {
			border-left: 1px solid #cccccc;
			border-right: 1px solid black;
			border-top: 2px solid black;
			border-bottom: 2px solid black;
		}

		table tr.table-judul{
			border: 1px solid;
			background-color: #e69500;
			font-weight: bold;
			font-size: 7px;
			color: black;
		}
			
		table tr.table-baris1{
			background-color: none;
			font-size: 7px;
		}

		table tr.table-baris1-bold{
			background-color: none;
			font-size: 7px;
			font-weight: bold;
		}
			
		table tr.table-total{
			background-color: #FFFF00;
			font-weight: bold;
			font-size: 7px;
			color: black;
		}

		table tr.table-total2{
			background-color: #eeeeee;
			font-weight: bold;
			font-size: 7px;
			color: black;
		}
	  </style>

	</head>
	<body>
		<div align="center" style="display: block;font-weight: bold;font-size: 12px;">BIAYA PEMAKAIAN ALAT</div>
		<div align="center" style="display: block;font-weight: bold;font-size: 11px;">DIVISI BETON  PROYEK BENDUNGAN TEMEF</div>
		<div align="center" style="display: block;font-weight: bold;font-size: 11px;">PT. BIA BUMI JAYENDRA</div>
		<div align="center" style="display: block;font-weight: bold;font-size: 12px; text-transform: uppercase;">PERIODE : <?php echo str_replace($search, $replace, $subject);?></div>
		<br /><br /><br />
		<?php
		$data = array();
		
		$arr_date = $this->input->get('filter_date');
		$arr_filter_date = explode(' - ', $arr_date);
		$date1 = '';
		$date2 = '';

		if(count($arr_filter_date) == 2){
			$date1 	= date('Y-m-d',strtotime($arr_filter_date[0]));
			$date2 	= date('Y-m-d',strtotime($arr_filter_date[1]));
			$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]));
		}
		
		?>
		
		<table width="98%" border="0" cellpadding="3" border="0">
		
			<?php
			
			$pembelian_bp = $this->db->select('
			pn.nama, po.no_po, p.nama_produk, prm.measure, SUM(prm.volume) as volume, prm.harga_satuan, SUM(prm.price) as price')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->join('penerima pn', 'po.supplier_id = pn.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("p.kategori_alat = '1'")
			->where("po.status in ('PUBLISH','CLOSED')")
			->group_by('prm.material_id')
			->order_by('p.nama_produk','asc')
			->get()->result_array();

			$total_pembelian_bp = 0;
			$total_vol_pembelian_bp = 0;

			foreach ($pembelian_bp as $x){
				$total_pembelian_bp += $x['price'];
				$total_vol_pembelian_bp += $x['volume'];
			}

			$pembelian_tm = $this->db->select('
			pn.nama, po.no_po, p.nama_produk, prm.measure, SUM(prm.volume) as volume, prm.harga_satuan, SUM(prm.price) as price')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->join('penerima pn', 'po.supplier_id = pn.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("p.kategori_alat = '2'")
			->where("po.status in ('PUBLISH','CLOSED')")
			->group_by('prm.material_id')
			->order_by('p.nama_produk','asc')
			->get()->result_array();

			$total_pembelian_tm = 0;
			$total_vol_pembelian_tm = 0;

			foreach ($pembelian_tm as $x){
				$total_pembelian_tm += $x['price'];
				$total_vol_pembelian_tm += $x['volume'];
			}

			$pembelian_wl = $this->db->select('
			pn.nama, po.no_po, p.nama_produk, prm.measure, SUM(prm.volume) as volume, prm.harga_satuan, SUM(prm.price) as price')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->join('penerima pn', 'po.supplier_id = pn.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("p.kategori_alat = '3'")
			->where("po.status in ('PUBLISH','CLOSED')")
			->group_by('prm.material_id')
			->order_by('p.nama_produk','asc')
			->get()->result_array();

			$total_pembelian_wl = 0;
			$total_vol_pembelian_wl = 0;

			foreach ($pembelian_wl as $x){
				$total_pembelian_wl += $x['price'];
				$total_vol_pembelian_wl += $x['volume'];
			}

			$pembelian_tf = $this->db->select('
			pn.nama, po.no_po, p.nama_produk, prm.measure, SUM(prm.volume) as volume, prm.harga_satuan, SUM(prm.price) as price')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->join('penerima pn', 'po.supplier_id = pn.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("p.kategori_alat = '4'")
			->where("po.status in ('PUBLISH','CLOSED')")
			->group_by('prm.material_id')
			->order_by('p.nama_produk','asc')
			->get()->result_array();

			$total_pembelian_tf = 0;
			$total_vol_pembelian_tf = 0;

			foreach ($pembelian_tf as $x){
				$total_pembelian_tf += $x['price'];
				$total_vol_pembelian_tf += $x['volume'];
			}

			$akumulasi_bbm = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar_2) as total_nilai_keluar_2')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi between '$date1' and '$date2')")
			->get()->row_array();

			$total_nilai_bbm = 0;
			$total_nilai_bbm = $akumulasi_bbm['total_nilai_keluar_2'];

			$insentif_tm = $this->db->select('pb.memo as memo, sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->where("pdb.akun = 220")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->group_by('pdb.id')
			->get()->result_array();

			$total_insentif_tm = 0;

			foreach ($insentif_tm as $y){
				$total_insentif_tm += $y['total'];
			}

			$total_insentif_tm_all = 0;
			$total_insentif_tm_all = $total_insentif_tm;

			$produk_exc = $this->db->select('
			pn.nama, po.no_po, p.nama_produk, prm.measure, SUM(prm.volume) as volume, prm.harga_satuan, SUM(prm.price) as price')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->join('penerima pn', 'po.supplier_id = pn.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("p.kategori_alat = '5'")
			->where("po.status in ('PUBLISH','CLOSED')")
			->group_by('prm.material_id')
			->order_by('p.nama_produk','asc')
			->get()->result_array();

			$total_price_exc = 0;
			foreach ($produk_exc as $x){
				$total_price_exc += $x['qty'] * $x['price'];
			}

			$produk_dmp_4m3 = $this->db->select('
			pn.nama, po.no_po, p.nama_produk, prm.measure, SUM(prm.volume) as volume, prm.harga_satuan, SUM(prm.price) as price')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->join('penerima pn', 'po.supplier_id = pn.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("p.kategori_alat = '6'")
			->where("po.status in ('PUBLISH','CLOSED')")
			->group_by('prm.material_id')
			->order_by('p.nama_produk','asc')
			->get()->result_array();

			$total_price_dmp_4m3 = 0;
			foreach ($produk_dmp_4m3 as $x){
				$total_price_dmp_4m3 += $x['qty'] * $x['price'];
			}

			$produk_dmp_10m3 = $this->db->select('
			pn.nama, po.no_po, p.nama_produk, prm.measure, SUM(prm.volume) as volume, prm.harga_satuan, SUM(prm.price) as price')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->join('penerima pn', 'po.supplier_id = pn.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("p.kategori_alat = '7'")
			->where("po.status in ('PUBLISH','CLOSED')")
			->group_by('prm.material_id')
			->order_by('p.nama_produk','asc')
			->get()->result_array();

			$total_price_dmp_10m3 = 0;
			foreach ($produk_dmp_10m3 as $x){
				$total_price_dmp_10m3 += $x['qty'] * $x['price'];
			}

			$produk_sc = $this->db->select('
			pn.nama, po.no_po, p.nama_produk, prm.measure, SUM(prm.volume) as volume, prm.harga_satuan, SUM(prm.price) as price')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->join('penerima pn', 'po.supplier_id = pn.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("p.kategori_alat = '8'")
			->where("po.status in ('PUBLISH','CLOSED')")
			->group_by('prm.material_id')
			->order_by('p.nama_produk','asc')
			->get()->result_array();

			$total_price_sc = 0;
			foreach ($produk_sc as $x){
				$total_price_sc += $x['qty'] * $x['price'];
			}

			$produk_gns = $this->db->select('
			pn.nama, po.no_po, p.nama_produk, prm.measure, SUM(prm.volume) as volume, prm.harga_satuan, SUM(prm.price) as price')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->join('penerima pn', 'po.supplier_id = pn.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("p.kategori_alat = '9'")
			->where("po.status in ('PUBLISH','CLOSED')")
			->group_by('prm.material_id')
			->order_by('p.nama_produk','asc')
			->get()->result_array();

			$total_price_gns = 0;
			foreach ($produk_gns as $x){
				$total_price_gns += $x['qty'] * $x['price'];
			}

			$produk_wl_sc = $this->db->select('
			pn.nama, po.no_po, p.nama_produk, prm.measure, SUM(prm.volume) as volume, prm.harga_satuan, SUM(prm.price) as price')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->join('penerima pn', 'po.supplier_id = pn.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("p.kategori_alat = '10'")
			->where("po.status in ('PUBLISH','CLOSED')")
			->group_by('prm.material_id')
			->order_by('p.nama_produk','asc')
			->get()->result_array();

			$total_price_wl_sc = 0;
			foreach ($produk_wl_sc as $x){
				$total_price_wl_sc += $x['qty'] * $x['price'];
			}

			$total_nilai_all = $total_pembelian_bp + $total_pembelian_tm + $total_pembelian_wl + $total_nilai_bbm + $total_insentif_tm_all + ($total_price_exc + $total_price_dmp_4m3 + $total_price_dmp_10m3 + $total_price_sc + $total_price_gns + $total_price_wl_sc);
			?>
			
			<tr class="table-judul">
				<th width="5%" align="center" class="table-border-pojok-kiri">NO.</th>
				<th width="20%" align="center" class="table-border-pojok-tengah">URAIAN</th>
				<th width="32%" align="center" class="table-border-pojok-tengah">REKANAN</th>
				<th width="10%" align="center" class="table-border-pojok-tengah">VOLUME</th>
				<th width="8%" align="center" class="table-border-pojok-tengah">SATUAN</th>
				<th width="10%" align="center" class="table-border-pojok-tengah">HARGA SATUAN</th>
				<th width="15%" align="center" class="table-border-pojok-kanan">TOTAL</th>
	        </tr>
			<tr class="table-baris1">
				<th align="center" class="table-border-pojok-kiri">1.</th>	
				<th align="left" class="table-border-pojok-tengah">Batching Plant</th>
				<th align="right" class="table-border-pojok-tengah"></th>
				<th align="center" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-kanan"></th>
	        </tr>
			<?php foreach ($pembelian_bp as $x): ?>
			<tr class="table-baris1">
				<th align="center" class="table-border-pojok-kiri"></th>
				<th align="left" class="table-border-pojok-tengah"></th>
				<th align="left" class="table-border-pojok-tengah"><?= $x['nama'] ?></th>
				<th align="right" class="table-border-pojok-tengah"><?php echo number_format($x['volume'],2,',','.');?></th>
				<th align="center" class="table-border-pojok-tengah"><?= $x['measure'] ?></th>
				<th align="right" class="table-border-pojok-tengah"><?php echo number_format($x['harga_satuan'],0,',','.');?></th>
				<th align="right" class="table-border-pojok-kanan"><?php echo number_format($x['price'],0,',','.');?></th>
			</tr>
			<?php endforeach; ?>
			<tr class="table-baris1-bold">
				<th align="center" class="table-border-pojok-kiri"></th>
				<th align="right" class="table-border-pojok-tengah">Total Batching Plant</th>
				<th align="left" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-tengah"><?php echo number_format($total_vol_pembelian_bp,2,',','.');?></th>
				<th align="center" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-kanan"><?php echo number_format($total_pembelian_bp,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center" class="table-border-pojok-kiri">2.</th>	
				<th align="left" class="table-border-pojok-tengah">Truck Mixer</th>
				<th align="right" class="table-border-pojok-tengah"></th>
				<th align="center" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-kanan"></th>
	        </tr>
			<?php foreach ($pembelian_tm as $x): ?>
			<tr class="table-baris1">
				<th align="center" class="table-border-pojok-kiri"></th>
				<th align="left" class="table-border-pojok-tengah"></th>
				<th align="left" class="table-border-pojok-tengah"><?= $x['nama'] ?></th>
				<th align="right" class="table-border-pojok-tengah"><?php echo number_format($x['volume'],2,',','.');?></th>
				<th align="center" class="table-border-pojok-tengah"><?= $x['measure'] ?></th>
				<th align="right" class="table-border-pojok-tengah"><?php echo number_format($x['harga_satuan'],0,',','.');?></th>
				<th align="right" class="table-border-pojok-kanan"><?php echo number_format($x['price'],0,',','.');?></th>
			</tr>
			<?php endforeach; ?>
			<tr class="table-baris1-bold">
				<th align="center" class="table-border-pojok-kiri"></th>
				<th align="right" class="table-border-pojok-tengah">Total Truck Mixer</th>
				<th align="left" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-tengah"><?php echo number_format($total_vol_pembelian_tm,2,',','.');?></th>
				<th align="center" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-kanan"><?php echo number_format($total_pembelian_tm,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center" class="table-border-pojok-kiri">3.</th>	
				<th align="left" class="table-border-pojok-tengah">Wheel Loader</th>
				<th align="right" class="table-border-pojok-tengah"></th>
				<th align="center" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-kanan"></th>
	        </tr>
			<?php foreach ($pembelian_wl as $x): ?>
			<tr class="table-baris1">
				<th align="center" class="table-border-pojok-kiri"></th>
				<th align="left" class="table-border-pojok-tengah"></th>
				<th align="left" class="table-border-pojok-tengah"><?= $x['nama'] ?></th>
				<th align="right" class="table-border-pojok-tengah"><?php echo number_format($x['volume'],2,',','.');?></th>
				<th align="center" class="table-border-pojok-tengah"><?= $x['measure'] ?></th>
				<th align="right" class="table-border-pojok-tengah"><?php echo number_format($x['harga_satuan'],0,',','.');?></th>
				<th align="right" class="table-border-pojok-kanan"><?php echo number_format($x['price'],0,',','.');?></th>
			</tr>
			<?php endforeach; ?>
			<tr class="table-baris1-bold">
				<th align="center" class="table-border-pojok-kiri"></th>
				<th align="right" class="table-border-pojok-tengah">Total Wheel Loader</th>
				<th align="left" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-tengah"><?php echo number_format($total_vol_pembelian_wl,2,',','.');?></th>
				<th align="center" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-kanan"><?php echo number_format($total_pembelian_wl,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center" class="table-border-pojok-kiri">4.</th>	
				<th align="left" class="table-border-pojok-tengah">Transfer Semen</th>
				<th align="right" class="table-border-pojok-tengah"></th>
				<th align="center" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-kanan"></th>
	        </tr>
			<?php foreach ($pembelian_tf as $x): ?>
			<tr class="table-baris1">
				<th align="center" class="table-border-pojok-kiri"></th>
				<th align="left" class="table-border-pojok-tengah"></th>
				<th align="left" class="table-border-pojok-tengah"><?= $x['nama'] ?></th>
				<th align="right" class="table-border-pojok-tengah"><?php echo number_format($x['volume'],2,',','.');?></th>
				<th align="center" class="table-border-pojok-tengah"><?= $x['measure'] ?></th>
				<th align="right" class="table-border-pojok-tengah"><?php echo number_format($x['harga_satuan'],0,',','.');?></th>
				<th align="right" class="table-border-pojok-kanan"><?php echo number_format($x['price'],0,',','.');?></th>
			</tr>
			<?php endforeach; ?>
			<tr class="table-baris1-bold">
				<th align="center" class="table-border-pojok-kiri"></th>
				<th align="right" class="table-border-pojok-tengah">Total Transfer Semen</th>
				<th align="left" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-tengah"><?php echo number_format($total_vol_pembelian_tf,2,',','.');?></th>
				<th align="center" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-kanan"><?php echo number_format($total_pembelian_tf,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center" class="table-border-pojok-kiri">5.</th>
				<th align="left" class="table-border-pojok-tengah">BBM Solar</th>
				<th align="center" class="table-border-pojok-tengah"></th>
				<th align="center" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-kanan"><?php echo number_format($total_nilai_bbm,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center" class="table-border-pojok-kiri">6.</th>
				<th align="left" class="table-border-pojok-tengah">Insentif Operator</th>
				<th align="center" class="table-border-pojok-tengah"></th>
				<th align="center" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-kanan"><?php echo number_format($total_insentif_tm_all,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center" class="table-border-pojok-kiri">7.</th>	
				<th align="left" class="table-border-pojok-tengah">Sewa Alat (SC)</th>
				<th align="right" class="table-border-pojok-tengah"></th>
				<th align="center" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-tengah"></th>
				<th align="right" class="table-border-pojok-kanan"></th>
	        </tr>
			<?php foreach ($produk_exc as $x): ?>
			<tr class="table-baris1">
				<th align="center" class="table-border-pojok-kiri"></th>
				<th align="left" class="table-border-pojok-tengah"></th>
				<th align="left" class="table-border-pojok-tengah"><?= $x['nama'] ?></th>
				<th align="right" class="table-border-pojok-tengah"><?php echo number_format($x['volume'],2,',','.');?></th>
				<th align="center" class="table-border-pojok-tengah"><?= $x['measure'] ?></th>
				<th align="right" class="table-border-pojok-tengah"><?php echo number_format($x['harga_satuan'],0,',','.');?></th>
				<th align="right" class="table-border-pojok-kanan"><?php echo number_format($x['price'],0,',','.');?></th>
			</tr>
			<?php endforeach; ?>
			<?php foreach ($produk_dmp_4m3 as $x): ?>
			<tr class="table-baris1">
				<th align="center" class="table-border-pojok-kiri"></th>
				<th align="left" class="table-border-pojok-tengah"></th>
				<th align="left" class="table-border-pojok-tengah"><?= $x['nama'] ?></th>
				<th align="right" class="table-border-pojok-tengah"><?php echo number_format($x['volume'],2,',','.');?></th>
				<th align="center" class="table-border-pojok-tengah"><?= $x['measure'] ?></th>
				<th align="right" class="table-border-pojok-tengah"><?php echo number_format($x['harga_satuan'],0,',','.');?></th>
				<th align="right" class="table-border-pojok-kanan"><?php echo number_format($x['price'],0,',','.');?></th>
			</tr>
			<?php endforeach; ?>
			<?php foreach ($produk_dmp_10m3 as $x): ?>
			<tr class="table-baris1">
				<th align="center" class="table-border-pojok-kiri"></th>
				<th align="left" class="table-border-pojok-tengah"></th>
				<th align="left" class="table-border-pojok-tengah"><?= $x['nama'] ?></th>
				<th align="right" class="table-border-pojok-tengah"><?php echo number_format($x['volume'],2,',','.');?></th>
				<th align="center" class="table-border-pojok-tengah"><?= $x['measure'] ?></th>
				<th align="right" class="table-border-pojok-tengah"><?php echo number_format($x['harga_satuan'],0,',','.');?></th>
				<th align="right" class="table-border-pojok-kanan"><?php echo number_format($x['price'],0,',','.');?></th>
			</tr>
			<?php endforeach; ?>
			<?php foreach ($produk_sc as $x): ?>
			<tr class="table-baris1">
				<th align="center" class="table-border-pojok-kiri"></th>
				<th align="left" class="table-border-pojok-tengah"></th>
				<th align="left" class="table-border-pojok-tengah"><?= $x['nama'] ?></th>
				<th align="right" class="table-border-pojok-tengah"><?php echo number_format($x['volume'],2,',','.');?></th>
				<th align="center" class="table-border-pojok-tengah"><?= $x['measure'] ?></th>
				<th align="right" class="table-border-pojok-tengah"><?php echo number_format($x['harga_satuan'],0,',','.');?></th>
				<th align="right" class="table-border-pojok-kanan"><?php echo number_format($x['price'],0,',','.');?></th>
			</tr>
			<?php endforeach; ?>
			<?php foreach ($produk_gns as $x): ?>
			<tr class="table-baris1">
				<th align="center" class="table-border-pojok-kiri"></th>
				<th align="left" class="table-border-pojok-tengah"></th>
				<th align="left" class="table-border-pojok-tengah"><?= $x['nama'] ?></th>
				<th align="right" class="table-border-pojok-tengah"><?php echo number_format($x['volume'],2,',','.');?></th>
				<th align="center" class="table-border-pojok-tengah"><?= $x['measure'] ?></th>
				<th align="right" class="table-border-pojok-tengah"><?php echo number_format($x['harga_satuan'],0,',','.');?></th>
				<th align="right" class="table-border-pojok-kanan"><?php echo number_format($x['price'],0,',','.');?></th>
			</tr>
			<?php endforeach; ?>
			<?php foreach ($produk_wl_sc as $x): ?>
			<tr class="table-baris1">
				<th align="center" class="table-border-pojok-kiri"></th>
				<th align="left" class="table-border-pojok-tengah"></th>
				<th align="left" class="table-border-pojok-tengah"><?= $x['nama'] ?></th>
				<th align="right" class="table-border-pojok-tengah"><?php echo number_format($x['volume'],2,',','.');?></th>
				<th align="center" class="table-border-pojok-tengah"><?= $x['measure'] ?></th>
				<th align="right" class="table-border-pojok-tengah"><?php echo number_format($x['harga_satuan'],0,',','.');?></th>
				<th align="right" class="table-border-pojok-kanan"><?php echo number_format($x['price'],0,',','.');?></th>
			</tr>
			<?php endforeach; ?>
			<tr class="table-total2">	
				<th align="right" colspan="6" class="table-border-spesial-kiri">TOTAL BIAYA PEMAKAIAN ALAT</th>
				<th align="right" class="table-border-spesial-kanan"><?php echo number_format($total_nilai_all,0,',','.');?></th>
	        </tr>
	    </table>
		
	</body>
</html>