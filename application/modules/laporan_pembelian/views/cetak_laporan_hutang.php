<!DOCTYPE html>
<html>
	<head>
	  <title>LAPORAN HUTANG</title>
	  
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
			font-size: 9px;
			color: black;
		}
			
		table tr.table-baris1{
			background-color: #F0F0F0;
			font-size: 8px;
		}

		table tr.table-baris1-bold{
			background-color: #F0F0F0;
			font-size: 9px;
			font-weight: bold;
		}
			
		table tr.table-baris2{
			font-size: 9px;
			background-color: #E8E8E8;
		}
			
		table tr.table-total{
			background-color: #cccccc;
			font-weight: bold;
			font-size: 9px;
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
					<div style="display: block;font-weight: bold;font-size: 12px;">LAPORAN HUTANG</div>
					<div style="display: block;font-weight: bold;font-size: 11px;">DIVISI BETON  PROYEK BENDUNGAN TEMEF</div>
				    <div style="display: block;font-weight: bold;font-size: 11px;">PT. BIA BUMI JAYENDRA</div>
					<div style="display: block;font-weight: bold;font-size: 12px; text-transform: uppercase;">PERIODE : <?php echo str_replace($search, $replace, $subject);?></div>
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
		
		<table class="table table-bordered" width="98%"  cellpadding="2">
			<style type="text/css">
				table tr.table-judul{
					background-color: #e69500;
					font-weight: bold;
					font-size: 9px;
					color: black;
				}
					
				table tr.table-baris1{
					background-color: #F0F0F0;
					font-size: 9px;
				}

				table tr.table-baris1-bold{
					background-color: #F0F0F0;
					font-size: 9px;
					font-weight: bold;
				}
					
				table tr.table-baris2{
					font-size: 9px;
					background-color: #E8E8E8;
				}
					
				table tr.table-total{
					background-color: #cccccc;
					font-weight: bold;
					font-size: 9px;
					color: black;
				}
			</style>
			<tr class="table-judul">
				<th width="5%" align="center" rowspan="2" style="vertical-align:middle;">NO.</th>
				<th width="25%" align="center" rowspan="2" style="vertical-align:middle;">URAIAN</th>
				<th width="13%" align="center" rowspan="2" style="vertical-align:middle;">PENERIMAAN</th>
				<th width="13%" align="center" rowspan="2" style="vertical-align:middle;">TAGIHAN</th>
				<th width="13%" align="center" rowspan="2" style="vertical-align:middle;">PEMBAYARAN</th>
				<th width="26%" align="center" colspan="2">HUTANG</th>
				<th width="5%" align="center" rowspan="2" style="vertical-align:middle;">KET.</th>
	        </tr>
			<tr class="table-judul">
				<th align="center">PENERIMAAN</th>
				<th align="center">TAGIHAN</th>
	        </tr>
			<tr class="table-baris1-bold">
				<th align="center">1</th>			
				<th align="left" colspan="7">MATERIAL / BAHAN</th>
	        </tr>
			<?php
			$penerima_1 = $this->db->select('nama')
			->from('penerima')
			->where("id = 3")
			->get()->row_array();
			?>
			<tr class="table-baris1-bold">
				<th align="center"></th>			
				<th align="left" colspan="7">1.1 <?= $penerima_1['nama'];?></th>
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
			<tr class="table-baris1">
				<th align="center"></th>			
				<th align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Batu Split 10-20</th>
				<th align="right"><?php echo number_format($penerimaan_batu1020_alamindah['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($tagihan_batu1020_alamindah['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pembayaran_batu1020_alamindah['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_penerimaan_batu1020_alamindah,0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_tagihan_batu1020_alamindah,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center"></th>			
				<th align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Batu Split 20-30</th>
				<th align="right"><?php echo number_format($penerimaan_batu2030_alamindah['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($tagihan_batu2030_alamindah['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pembayaran_batu2030_alamindah['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_penerimaan_batu2030_alamindah,0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_tagihan_batu2030_alamindah,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center"></th>			
				<th align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pasir</th>
				<th align="right"><?php echo number_format($penerimaan_pasir_alamindah['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($tagihan_pasir_alamindah['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pembayaran_pasir_alamindah['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_penerimaan_pasir_alamindah,0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_tagihan_pasir_alamindah,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center"></th>			
				<th align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jasa Angkut</th>
				<th align="right"><?php echo number_format($penerimaan_jasa_angkut_alamindah['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($tagihan_jasa_angkut_alamindah['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pembayaran_jasa_angkut_alamindah['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_penerimaan_jasa_angkut_alamindah,0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_tagihan_jasa_angkut_alamindah,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			<tr class="table-baris2">
				<th align="center"></th>			
				<th align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jumlah</th>
				<th align="right"><?php echo number_format($jumlah_penerimaan_alamindah,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_tagihan_alamindah,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_pembayaran_alamindah,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_hutang_penerimaan_alamindah,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_hutang_tagihan_alamindah,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			<?php
			$penerima_2 = $this->db->select('nama')
			->from('penerima')
			->where("id = 4")
			->get()->row_array();
			?>
			<tr class="table-baris1-bold">
				<th align="center"></th>			
				<th align="left" colspan="7">1.2 <?= $penerima_2['nama'];?></th>
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
			$hutang_penerimaan_semen_cons_kupang = $penerimaan_semen_cons_kupang['total'] - $pembayaran_semen_cons_kupang['total'];

			$jumlah_hutang_penerimaan_kupang = $hutang_penerimaan_semen_cons_kupang;
			?>

			<?php
			$hutang_tagihan_semen_cons_kupang = $tagihan_semen_cons_kupang['total'] - $pembayaran_semen_cons_kupang['total'];

			$jumlah_hutang_tagihan_kupang = $hutang_tagihan_semen_cons_kupang;
			?>
			<tr class="table-baris1">
				<th align="center"></th>			
				<th align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Semen Cons</th>
				<th align="right"><?php echo number_format($penerimaan_semen_cons_kupang['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($tagihan_semen_cons_kupang['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pembayaran_semen_cons_kupang['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_penerimaan_semen_cons_kupang,0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_tagihan_semen_cons_kupang,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			<tr class="table-baris2">
				<th align="center"></th>			
				<th align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jumlah</th>
				<th align="right"><?php echo number_format($jumlah_penerimaan_kupang,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_tagihan_kupang,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_pembayaran_kupang,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_hutang_penerimaan_kupang,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_hutang_tagihan_kupang,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			<?php
			$penerima_3 = $this->db->select('nama')
			->from('penerima')
			->where("id = 7")
			->get()->row_array();
			?>
			<tr class="table-baris1-bold">
				<th align="center"></th>			
				<th align="left" colspan="7">1.3 <?= $penerima_3['nama'];?></th>
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
			$hutang_penerimaan_solar_langit = $penerimaan_solar_langit['total'] - $pembayaran_solar_langit['total'];

			$jumlah_hutang_penerimaan_langit = $hutang_penerimaan_solar_langit;
			?>

			<?php
			$hutang_tagihan_solar_langit = $tagihan_solar_langit['total'] - $pembayaran_solar_langit['total'];

			$jumlah_hutang_tagihan_langit = $hutang_tagihan_solar_langit;
			?>
			<tr class="table-baris1">
				<th align="center"></th>			
				<th align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Solar</th>
				<th align="right"><?php echo number_format($penerimaan_solar_langit['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($tagihan_solar_langit['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pembayaran_solar_langit['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_penerimaan_solar_langit,0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_tagihan_solar_langit,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			<tr class="table-baris2">
				<th align="center"></th>			
				<th align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jumlah</th>
				<th align="right"><?php echo number_format($jumlah_penerimaan_langit,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_tagihan_langit,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_pembayaran_langit,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_hutang_penerimaan_langit,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_hutang_tagihan_langit,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			<?php
			$penerima_4 = $this->db->select('nama')
			->from('penerima')
			->where("id = 2")
			->get()->row_array();
			?>
			<tr class="table-baris1-bold">
				<th align="center"></th>			
				<th align="left" colspan="7">1.4 <?= $penerima_4['nama'];?></th>
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
			$hutang_penerimaan_semen_pcc_sli = $penerimaan_semen_pcc_sli['total'] - $pembayaran_semen_pcc_sli['total'];
			$hutang_penerimaan_semen_opc_sli = $penerimaan_semen_opc_sli['total'] - $pembayaran_semen_opc_sli['total'];

			$jumlah_hutang_penerimaan_sli = $hutang_penerimaan_semen_pcc_sli - $hutang_penerimaan_semen_opc_sli;
			?>

			<?php
			$hutang_tagihan_semen_pcc_sli = $tagihan_semen_pcc_sli['total'] - $pembayaran_semen_pcc_sli['total'];
			$hutang_tagihan_semen_opc_sli = $tagihan_semen_opc_sli['total'] - $pembayaran_semen_opc_sli['total'];

			$jumlah_hutang_tagihan_sli = $hutang_tagihan_semen_pcc_sli - $hutang_tagihan_semen_opc_sli;
			?>
			<tr class="table-baris1">
				<th align="center"></th>			
				<th align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Semen (PCC)</th>
				<th align="right"><?php echo number_format($penerimaan_semen_pcc_sli['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($tagihan_semen_pcc_sli['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pembayaran_semen_pcc_sli['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_penerimaan_semen_pcc_sli,0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_tagihan_semen_pcc_sli,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center"></th>			
				<th align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Semen (OPC)</th>
				<th align="right"><?php echo number_format($penerimaan_semen_opc_sli['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($tagihan_semen_opc_sli['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pembayaran_semen_opc_sli['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_penerimaan_semen_opc_sli,0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_tagihan_semen_opc_sli,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			<tr class="table-baris2">
				<th align="center"></th>			
				<th align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jumlah</th>
				<th align="right"><?php echo number_format($jumlah_penerimaan_sli,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_tagihan_sli,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_pembayaran_sli,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_hutang_penerimaan_sli,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_hutang_tagihan_sli,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			<?php
			$penerima_5 = $this->db->select('nama')
			->from('penerima')
			->where("id = 24")
			->get()->row_array();
			?>
			<tr class="table-baris1-bold">
				<th align="center"></th>			
				<th align="left" colspan="7">1.5 <?= $penerima_5['nama'];?></th>
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
			$hutang_penerimaan_solar_teleindo = $penerimaan_solar_teleindo['total'] - $pembayaran_solar_teleindo['total'];

			$jumlah_hutang_penerimaan_teleindo = $hutang_penerimaan_solar_teleindo;
			?>

			<?php
			$hutang_tagihan_solar_teleindo = $tagihan_solar_teleindo['total'] - $pembayaran_solar_teleindo['total'];

			$jumlah_hutang_tagihan_teleindo = $hutang_tagihan_solar_teleindo;
			?>
			<tr class="table-baris1">
				<th align="center"></th>			
				<th align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Solar</th>
				<th align="right"><?php echo number_format($penerimaan_solar_teleindo['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($tagihan_solar_teleindo['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pembayaran_solar_teleindo['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_penerimaan_solar_teleindo,0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_tagihan_solar_teleindo,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			<tr class="table-baris2">
				<th align="center"></th>			
				<th align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jumlah</th>
				<th align="right"><?php echo number_format($jumlah_penerimaan_teleindo,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_tagihan_teleindo,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_pembayaran_teleindo,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_hutang_penerimaan_teleindo,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_hutang_tagihan_teleindo,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			<?php
			$jumlah_penerimaan_bahan = $jumlah_penerimaan_alamindah + $jumlah_penerimaan_kupang + $jumlah_penerimaan_langit + $jumlah_penerimaan_sli + $jumlah_penerimaan_teleindo;
			$jumlah_tagihan_bahan = $jumlah_tagihan_alamindah + $jumlah_tagihan_kupang + $jumlah_tagihan_langit + $jumlah_tagihan_sli + $jumlah_tagihan_teleindo;
			$jumlah_pembayaran_bahan = $jumlah_pembayaran_alamindah + $jumlah_pembayaran_kupang + $jumlah_pembayaran_langit + $jumlah_pembayaran_sli + $jumlah_pembayaran_teleindo;
			$jumlah_hutang_penerimaan_bahan = $jumlah_hutang_penerimaan_alamindah + $jumlah_hutang_penerimaan_kupang + $jumlah_hutang_penerimaan_langit + $jumlah_hutang_penerimaan_sli + $jumlah_hutang_penerimaan_teleindo;
			$jumlah_hutang_tagihan_bahan = $jumlah_hutang_tagihan_alamindah + $jumlah_hutang_tagihan_kupang + $jumlah_hutang_tagihan_langit + $jumlah_hutang_tagihan_sli + $jumlah_hutang_tagihan_teleindo;
			?>
			<tr class="table-total">
				<th align="center"></th>			
				<th align="center">Jumlah Bahan</th>
				<th align="right"><?php echo number_format($jumlah_penerimaan_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_tagihan_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_pembayaran_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_hutang_penerimaan_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_hutang_tagihan_bahan,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			
			<!-- PERALATAN -->
			<tr class="table-baris1-bold">
				<th align="center">2</th>			
				<th align="left" colspan="7">PERALATAN</th>
	        </tr>
			<?php
			$penerima_6 = $this->db->select('nama')
			->from('penerima')
			->where("id = 5")
			->get()->row_array();
			?>
			<tr class="table-baris1-bold">
				<th align="center"></th>			
				<th align="left" colspan="7">2.1 <?= $penerima_6['nama'];?></th>
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

			$hutang_penerimaan_truck_mixer_nindya = $penerimaan_truck_mixer_nindya['total'] - $pembayaran_truck_mixer_nindya['total'];

			$jumlah_hutang_penerimaan_nindya = $hutang_penerimaan_truck_mixer_nindya;
			?>

			<?php

			$hutang_tagihan_truck_mixer_nindya = $tagihan_truck_mixer_nindya['total'] - $pembayaran_truck_mixer_nindya['total'];

			$jumlah_hutang_tagihan_nindya = $hutang_tagihan_truck_mixer_nindya;
			?>
			<tr class="table-baris1">
				<th align="center"></th>			
				<th align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Truck Mixer</th>
				<th align="right"><?php echo number_format($penerimaan_truck_mixer_nindya['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($tagihan_truck_mixer_nindya['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pembayaran_truck_mixer_nindya['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_penerimaan_truck_mixer_nindya,0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_tagihan_truck_mixer_nindya,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			<tr class="table-baris2">
				<th align="center"></th>			
				<th align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jumlah</th>
				<th align="right"><?php echo number_format($jumlah_penerimaan_nindya,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_tagihan_nindya,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_pembayaran_nindya,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_hutang_penerimaan_nindya,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_hutang_tagihan_nindya,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			<?php
			$penerima_7 = $this->db->select('nama')
			->from('penerima')
			->where("id = 6")
			->get()->row_array();
			?>
			<tr class="table-baris1-bold">
				<th align="center"></th>			
				<th align="left" colspan="7">2.2 <?= $penerima_7['nama'];?></th>
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
			$hutang_penerimaan_truck_mixer_sbm = $penerimaan_truck_mixer_sbm['total'] - $pembayaran_truck_mixer_sbm['total'];

			$jumlah_hutang_penerimaan_sbm = $hutang_penerimaan_truck_mixer_sbm;
			?>

			<?php
			$hutang_tagihan_truck_mixer_sbm = $tagihan_truck_mixer_sbm['total'] - $pembayaran_truck_mixer_sbm['total'];

			$jumlah_hutang_tagihan_sbm = $hutang_tagihan_truck_mixer_sbm;
			?>
			<tr class="table-baris1">
				<th align="center"></th>			
				<th align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Truck Mixer</th>
				<th align="right"><?php echo number_format($penerimaan_truck_mixer_sbm['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($tagihan_truck_mixer_sbm['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pembayaran_truck_mixer_sbm['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_penerimaan_truck_mixer_sbm,0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_tagihan_truck_mixer_sbm,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			<tr class="table-baris2">
				<th align="center"></th>			
				<th align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jumlah</th>
				<th align="right"><?php echo number_format($jumlah_penerimaan_sbm,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_tagihan_sbm,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_pembayaran_sbm,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_hutang_penerimaan_sbm,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_hutang_tagihan_sbm,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			<?php
			$penerima_8 = $this->db->select('nama')
			->from('penerima')
			->where("id = 3")
			->get()->row_array();
			?>
			<tr class="table-baris1-bold">
				<th align="center"></th>			
				<th align="left" colspan="7">2.3 <?= $penerima_8['nama'];?></th>
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
			$hutang_penerimaan_batching_plant_alamindah_alat = $penerimaan_batching_plant_alamindah_alat['total'] - $pembayaran_batching_plant_alamindah_alat['total'];
			$hutang_penerimaan_wheel_loader_alamindah_alat = $penerimaan_wheel_loader_alamindah_alat['total'] - $pembayaran_wheel_loader_alamindah_alat['total'];

			$jumlah_hutang_penerimaan_alamindah_alat = $hutang_penerimaan_batching_plant_alamindah_alat + $hutang_penerimaan_wheel_loader_alamindah_alat;
			?>

			<?php
			$hutang_tagihan_batching_plant_alamindah_alat = $tagihan_batching_plant_alamindah_alat['total'] - $pembayaran_batching_plant_alamindah_alat['total'];
			$hutang_tagihan_wheel_loader_alamindah_alat = $tagihan_wheel_loader_alamindah_alat['total'] - $pembayaran_wheel_loader_alamindah_alat['total'];

			$jumlah_hutang_tagihan_alamindah_alat = $hutang_tagihan_batching_plant_alamindah_alat + $hutang_tagihan_wheel_loader_alamindah_alat;
			?>
			<tr class="table-baris1">
				<th align="center"></th>			
				<th align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Batching Plant</th>
				<th align="right"><?php echo number_format($penerimaan_batching_plant_alamindah_alat['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($tagihan_batching_plant_alamindah_alat['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pembayaran_batching_plant_alamindah_alat['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_penerimaan_batching_plant_alamindah_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_tagihan_batching_plant_alamindah_alat,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center"></th>			
				<th align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Wheel Loader</th>
				<th align="right"><?php echo number_format($penerimaan_wheel_loader_alamindah_alat['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($tagihan_wheel_loader_alamindah_alat['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pembayaran_wheel_loader_alamindah_alat['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_penerimaan_wheel_loader_alamindah_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_tagihan_wheel_loader_alamindah_alat,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			<tr class="table-baris2">
				<th align="center"></th>			
				<th align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jumlah</th>
				<th align="right"><?php echo number_format($jumlah_penerimaan_alamindah_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_tagihan_alamindah_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_pembayaran_alamindah_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_hutang_penerimaan_alamindah_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_hutang_tagihan_alamindah_alat,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			<?php
			$penerima_9 = $this->db->select('nama')
			->from('penerima')
			->where("id = 25")
			->get()->row_array();
			?>
			<tr class="table-baris1-bold">
				<th align="center"></th>			
				<th align="left" colspan="7">2.4 <?= $penerima_9['nama'];?></th>
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
			$hutang_penerimaan_truck_mixer_waskita = $penerimaan_truck_mixer_waskita['total'] - $pembayaran_truck_mixer_waskita['total'];

			$jumlah_hutang_penerimaan_waskita = $hutang_penerimaan_truck_mixer_waskita - $hutang_penerimaan_truck_mixer_waskita;
			?>

			<?php
			$hutang_tagihan_truck_mixer_waskita = $tagihan_truck_mixer_waskita['total'] - $pembayaran_truck_mixer_waskita['total'];

			$jumlah_hutang_tagihan_waskita = $hutang_tagihan_truck_mixer_waskita - $hutang_tagihan_truck_mixer_waskita;
			?>
			<tr class="table-baris1">
				<th align="center"></th>			
				<th align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Truck Mixer</th>
				<th align="right"><?php echo number_format($penerimaan_truck_mixer_waskita['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($tagihan_truck_mixer_waskita['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pembayaran_truck_mixer_waskita['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_penerimaan_truck_mixer_waskita,0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_tagihan_truck_mixer_waskita,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			<tr class="table-baris2">
				<th align="center"></th>			
				<th align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jumlah</th>
				<th align="right"><?php echo number_format($jumlah_penerimaan_waskita,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_tagihan_waskita,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_pembayaran_waskita,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_hutang_penerimaan_waskita,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_hutang_tagihan_waskita,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			<?php
			$penerima_10 = $this->db->select('nama')
			->from('penerima')
			->where("id = 2")
			->get()->row_array();
			?>
			<tr class="table-baris1-bold">
				<th align="center"></th>			
				<th align="left" colspan="7">2.5 <?= $penerima_10['nama'];?></th>
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
			$hutang_penerimaan_truck_mixer_sli_alat = $penerimaan_truck_mixer_sli_alat['total'] - $pembayaran_truck_mixer_sli_alat['total'];

			$jumlah_hutang_penerimaan_sli_alat = $hutang_penerimaan_truck_mixer_sli_alat;
			?>

			<?php
			$hutang_tagihan_truck_mixer_sli_alat = $tagihan_truck_mixer_sli_alat['total'] - $pembayaran_truck_mixer_sli_alat['total'];

			$jumlah_hutang_tagihan_sli_alat = $hutang_tagihan_truck_mixer_sli_alat;
			?>
			<tr class="table-baris1">
				<th align="center"></th>			
				<th align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Solar</th>
				<th align="right"><?php echo number_format($penerimaan_truck_mixer_sli_alat['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($tagihan_truck_mixer_sli_alat['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($pembayaran_truck_mixer_sli_alat['total'],0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_penerimaan_truck_mixer_sli_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($hutang_tagihan_truck_mixer_sli_alat,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			<tr class="table-baris2">
				<th align="center"></th>			
				<th align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jumlah</th>
				<th align="right"><?php echo number_format($jumlah_penerimaan_sli_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_tagihan_sli_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_pembayaran_sli_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_hutang_penerimaan_sli_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_hutang_tagihan_sli_alat,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			<?php
			$jumlah_penerimaan_alat= $jumlah_penerimaan_nindya + $jumlah_penerimaan_sbm + $jumlah_penerimaan_alamindah_alat + $jumlah_penerimaan_waskita + $jumlah_penerimaan_sli_alat;
			$jumlah_tagihan_alat= $jumlah_tagihan_nindya + $jumlah_tagihan_sbm + $jumlah_tagihan_alamindah_alat + $jumlah_tagihan_waskita + $jumlah_tagihan_sli_alat;
			$jumlah_pembayaran_alat= $jumlah_pembayaran_nindya + $jumlah_pembayaran_sbm + $jumlah_pembayaran_alamindah_alat + $jumlah_pembayaran_waskita + $jumlah_pembayaran_sli_alat;
			$jumlah_hutang_penerimaan_alat= $jumlah_hutang_penerimaan_nindya + $jumlah_hutang_penerimaan_sbm + $jumlah_hutang_penerimaan_alamindah_alat + $jumlah_hutang_penerimaan_waskita + $jumlah_hutang_penerimaan_sli_alat;
			$jumlah_hutang_tagihan_alat= $jumlah_hutang_tagihan_nindya + $jumlah_hutang_tagihan_sbm + $jumlah_hutang_tagihan_alamindah_alat + $jumlah_hutang_tagihan_waskita + $jumlah_hutang_tagihan_sli_alat;
			?>
			<tr class="table-total">
				<th align="center"></th>			
				<th align="center">Jumlah Alat</th>
				<th align="right"><?php echo number_format($jumlah_penerimaan_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_tagihan_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_pembayaran_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_hutang_penerimaan_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_hutang_tagihan_alat,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
			<?php
			$total_hutang_penerimaan = $jumlah_penerimaan_bahan + $jumlah_penerimaan_alat;
			$total_hutang_tagihan = $jumlah_tagihan_bahan + $jumlah_tagihan_alat;
			$total_hutang_pembayaran = $jumlah_pembayaran_bahan + $jumlah_pembayaran_alat;
			$total_hutang_penerimaan_all = $jumlah_hutang_penerimaan_bahan + $jumlah_hutang_penerimaan_alat;
			$total_hutang_tagihan_all = $jumlah_hutang_tagihan_bahan + $jumlah_hutang_tagihan_alat;
			?>
			<tr class="table-total">
				<th align="center"></th>			
				<th align="center">Total Hutang</th>
				<th align="right"><?php echo number_format($total_hutang_penerimaan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_hutang_tagihan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_hutang_pembayaran,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_hutang_penerimaan_all,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_hutang_tagihan_all,0,',','.');?></th>
				<th align="right"></th>
	        </tr>
		</table>
		
	</body>
</html>