<!DOCTYPE html>
<html>
	<head>
	  <title>LAPORAN UMUR PIUTANG</title>
	  
	  <style type="text/css">
		table tr.table-judul{
			background-color: #e69500;
			font-weight: bold;
			font-size: 8px;
			color: black;
		}
			
		table tr.table-baris1{
			background-color: #F0F0F0;
			font-size: 8px;
		}

		table tr.table-baris1-bold{
			background-color: #F0F0F0;
			font-size: 8px;
			font-weight: bold;
		}
			
		table tr.table-baris2{
			font-size: 8px;
			background-color: #E8E8E8;
		}

		table tr.table-baris2-bold{
			font-size: 8px;
			background-color: #E8E8E8;
			font-weight: bold;
		}
			
		table tr.table-total{
			background-color: #cccccc;
			font-weight: bold;
			font-size: 8px;
			color: black;
		}
	  </style>

	</head>
	<body>
		<table width="98%">
			<tr>
				<td width="100%" align="center">
					<div style="display: block;font-weight: bold;font-size: 11px;">LAPORAN UMUR PIUTANG</div>
					<div style="display: block;font-weight: bold;font-size: 11px;">DIVISI BETON  PROYEK BENDUNGAN TEMEF</div>
				    <div style="display: block;font-weight: bold;font-size: 11px;">PT. BIA BUMI JAYENDRA</div>
				</td>
			</tr>
		</table>
		<br />
		<br />
		<table cellpadding="3" width="98%">

		<?php

		$date_now = date('Y-m-d');

		$penagihan_penjualan = $this->db->select('ppp.*, p.nama, ppp.total - (select COALESCE(sum(total),0) from pmm_pembayaran pm where pm.penagihan_id = ppp.id) as total_pembayaran')
		->from('pmm_penagihan_penjualan ppp')
		->join('penerima p','ppp.client_id = p.id','left')
		->where("ppp.status = 'OPEN'")
		->order_by('ppp.tanggal_invoice','desc')
		->get()->result_array();

		?>
			<tr class="table-judul">
				<th align="center" width="5%">NO.</th>
				<th align="center" width="17%">NO. INVOICE</th>
				<th align="center" width="8%">TGL. INVOICE</th>
				<th align="center" width="20%">REKANAN</th>
				<th align="center" width="10%">TOTAL</th>
				<th align="center" width="10%">1-30 HARI</th>
				<th align="center" width="10%">31-60 HARI</th>
				<th align="center" width="10%">61-90 HARI</th>
				<th align="center" width="10%">>90 HARI</th>
				
            </tr>
            <?php   
			if(!empty($penagihan_penjualan)){
			foreach ($penagihan_penjualan as $key => $x) {
			$dateOne30 = new DateTime($x['tanggal_invoice']);
			$dateTwo30 = new DateTime($date_now);
			$diff30 = $dateTwo30->diff($dateOne30)->format("%a");

			$dateOne60 = new DateTime($x['tanggal_invoice']);
			$dateTwo60 = new DateTime($date_now);
			$diff60 = $dateTwo60->diff($dateOne60)->format("%a");

			$dateOne90 = new DateTime($x['tanggal_invoice']);
			$dateTwo90 = new DateTime($date_now);
			$diff90 = $dateTwo90->diff($dateOne90)->format("%a");

			$dateOne120 = new DateTime($x['tanggal_invoice']);
			$dateTwo120 = new DateTime($date_now);
			$diff120 = $dateTwo120->diff($dateOne120)->format("%a");
			?>
            <tr class="table-baris1">
			<th align="center"><?php echo $key + 1;?></th>
			<th align="left"><?= $x['nomor_invoice'] ?></th>
			<th align="center"><?= date('d-m-Y',strtotime($x['tanggal_invoice'])); ?></th>
			<th align="left"><?= $x['nama'] ?></th>
			<th align="right"><?php echo number_format($x['total_pembayaran'],0,',','.');?></th>
			<th align="right"><?php echo ($diff30 >= 0 && $diff30 <= 30) ? number_format($x['total_pembayaran'],0,',','.') : '';?></th>
			<th align="right"><?php echo ($diff60 >= 31 && $diff60 <= 60) ? number_format($x['total_pembayaran'],0,',','.') : '';?></th>
			<th align="right"><?php echo ($diff90 >= 61 && $diff90 <= 90) ? number_format($x['total_pembayaran'],0,',','.') : '';?></th>
			<th align="right"><?php echo ($diff120 >= 91 && $diff120 <= 999) ? number_format($x['total_pembayaran'],0,',','.') : '';?></th>
		</tr>
		<?php
        }
        }
        ?>
	</table>
	</body>
</html>