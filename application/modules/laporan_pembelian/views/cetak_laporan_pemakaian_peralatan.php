<!DOCTYPE html>
<html>
	<head>
	  <title>LAPORAN PEMAKAIAN PERALATAN PRODUKSI</title>
	  <?= include 'lib.php'; ?>
	  
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
		table tr.table-judul{
			background-color: #e69500;
			font-weight: bold;
			font-size: 7px;
			color: black;
		}
			
		table tr.table-baris1{
			background-color: #F0F0F0;
			font-size: 7px;
		}

		table tr.table-baris1-bold{
			background-color: #F0F0F0;
			font-size: 7px;
			font-weight: bold;
		}
			
		table tr.table-baris2{
			font-size: 7px;
			background-color: #E8E8E8;
		}

		table tr.table-baris2-bold{
			font-size: 7px;
			background-color: #E8E8E8;
			font-weight: bold;
		}
			
		table tr.table-total{
			background-color: #cccccc;
			font-weight: bold;
			font-size: 7px;
			color: black;
		}
	  </style>

	</head>
	<body>
		<br />
		<br />
		<table width="98%" cellpadding="3">
			<tr>
				<td align="center"  width="100%">
					<div style="display: block;font-weight: bold;font-size: 11px;">LAPORAN PEMAKAIAN PERALATAN PRODUKSI</div>
					<div style="display: block;font-weight: bold;font-size: 11px;">DIVISI BETON  PROYEK BENDUNGAN TEMEF</div>
				    <div style="display: block;font-weight: bold;font-size: 11px;">PT. BIA BUMI JAYENDRA</div>
					<div style="display: block;font-weight: bold;font-size: 11px; text-transform: uppercase;">PERIODE : <?php echo str_replace($search, $replace, $subject);?></div>
				</td>
			</tr>
		</table>
		<br />
		<br />
		<br />
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

		$pembelian = $this->db->select('
		pn.nama, po.no_po, po.subject, prm.measure, SUM(prm.volume) as volume, SUM(prm.price) / SUM(prm.volume) as harga_satuan, SUM(prm.price) as price')
		->from('pmm_receipt_material prm')
		->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
		->join('penerima pn', 'po.supplier_id = pn.id','left')
		->where("prm.date_receipt between '$date1' and '$date2'")
		->where("prm.material_id in (8,12,13,14,15,16,23,24,25)")
		->where("po.status in ('PUBLISH','CLOSED')")
		->order_by('pn.nama','asc')
		->group_by('prm.harga_satuan')
		->get()->result_array();

		$total_nilai = 0;

		foreach ($pembelian as $x){
			$total_nilai += $x['price'];
		}

		$akumulasi_bbm = $this->db->select('pp.date_akumulasi, SUM(pp.total_nilai_keluar_2) as total_nilai_keluar_2')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi between '$date1' and '$date2')")
			->get()->row_array();

		$total_nilai_bbm = 0;
		$total_nilai_bbm = $akumulasi_bbm['total_nilai_keluar_2'];

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
			
			<tr class="table-judul">
				<th width="35%" align="center"><br>REKANAN</th>
				<th width="20%" align="center"><br>URAIAN</th>
				<th width="7%" align="center"><br>SATUAN</th>
				<th width="13%" align="center"><br>VOLUME</th>
				<th width="15%" align="center">HARGA SATUAN</th>
				<th width="10%" align="center">NILAI</th>
	        </tr>
			<?php foreach ($pembelian as $x): ?>
			<tr class="table-baris1">
				<th align="left">&bull; <?= $x['nama'] ?></th>
				<th align="left"><?= $x['subject'] ?></th>
				<th align="center"><?= $x['measure'] ?></th>
				<th align="right"><?php echo number_format($x['volume'],2,',','.');?></th>
				<th align="right"><?php echo number_format($x['harga_satuan'],0,',','.');?></th>
				<th align="right"><?php echo number_format($x['price'],0,',','.');?></th>
			</tr>
			<?php endforeach; ?>
			<tr class="table-baris1">
				<th align="left">&bull; BBM Solar</th>
				<th align="left"></th>
				<th align="center"></th>
				<th align="right"></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($total_nilai_bbm,0,',','.');?></th>
			</tr>
			<?php foreach ($insentif_tm as $y): ?>
			<tr class="table-baris1">
				<th align="left" colspan="5">&bull; <?= $y['memo'] ?></th>
				<th align="right"><?php echo number_format($y['total'],0,',','.');?></th>
			</tr>
			<?php endforeach; ?>
			<tr class="table-total">
				<th align="right" colspan="5">TOTAL</th>
				<th align="right"><?php echo number_format($total_nilai,0,',','.');?></th>
			</tr>
	    </table>
		<br />
		<br />
		<br />
		<table width="98%" border="0" cellpadding="0">
			<tr >
				<td width="5%"></td>
				<td width="90%">
					<table width="100%" border="0" cellpadding="2">
						<tr>
							<td align="center" colspan="2">
								Disetujui Oleh
							</td>
							<td align="center" colspan="2">
								Diperiksa Oleh
							</td>
							<td align="center">
								Dibuat Oleh
							</td>
						</tr>
						<tr class="">
							<td align="center" height="55px">
								
							</td>
							<td align="center">
								
							</td>
							<td align="center">
								
							</td>
							<td align="center">
								
							</td>
							<td align="center">
								
							</td>
						</tr>
						<tr>
							<td align="center" >
								<b><u>Gervasius K. Limahekin</u><br />
								Ka. Plant</b>
							</td>
							<td align="center" >
								<b><br />
								Ass. Ka. Plant</b>
							</td>
							<td align="center">
								<b><br />
								M. Keu & SDM</b>
							</td>
							<td align="center">
								<b><br />
								M. Teknik</b>
							</td>
							<td align="center">
								<b><u>Agustinus Pakaenoni</u><br />
								Pj. Logistik</b>
							</td>
						</tr>
					</table>
				</td>
				<td width="5%"></td>
			</tr>
		</table>
		
	</body>
</html>