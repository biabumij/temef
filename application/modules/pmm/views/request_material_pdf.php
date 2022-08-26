<!DOCTYPE html>
<html>
	<head>
	  <title><?php echo $row['request_no'];?></title>
	  <?= include 'lib.php'; ?>
	  
	  <style type="text/css">
	  	body{
	  		font-family: "Open Sans", Arial, sans-serif;
	  	}
	  	table.minimalistBlack {
		  border: 0px solid #000000;
		  width: 98%;
		  text-align: left;
		}
		table.minimalistBlack td, table.minimalistBlack th {
		  border: 1px solid #000000;
		  padding: 5px 4px;
		}
		table.minimalistBlack tr td {
		  /*font-size: 13px;*/
		  text-align:center;
		}
		table.minimalistBlack tr th {
		  /*font-size: 14px;*/
		  font-weight: bold;
		  color: #000000;
		  text-align: center;
		  padding: 10px;
		}
		hr{
			margin-top:0;
			margin-bottom:30px;
		}
		h3{
			margin-top:0;
		}
		table tr.table-head{
            background-color: #e69500;
			color: #ffffff;
			font-size: 9px;
        }
	  </style>

	</head>
	<body>
		<table width="98%" border="0" cellpadding="3">
			<tr>
				<td align="center">
					<div style="display: block;font-weight: bold;font-size: 12px;">PERMINTAAN BAHAN & ALAT <br />
					DIVISI BETON PROYEK LANJUTAN BENDUNGAN TEMEF (PAKET 3) (MYC) <br />PT BIA BUMI JAYENDRA</div>
				</td>
			</tr>
		</table>
		<br /><br />
		<table width="98%" border="0" cellpadding="3">
			<tr>
				<th>No. Permintaan</th>
				<th width="10px">:</th>
				<th align="left"><?php echo $row['request_no'];?></th>
			</tr>
			<tr>
				<th>Tanggal Permintaan</th>
				<th width="10px">:</th>
				<th align="left"><?= convertDateDBtoIndo($row["request_date"]); ?></th>
			</tr>
			<tr>
				<th>Rekanan</th>
				<th width="10px">:</th>
				<th align="left"><?php echo $row['supplier_id'] = $this->crud_global->GetField('penerima',array('id'=>$row['supplier_id']),'nama');;?></th>
			</tr>
			<tr>
				<th>Memo</th>
				<th width="10px">:</th>
				<th align="left"><?php echo $row['memo'];?></th>
			</tr>
		</table>
		<br />
		<br />
		<table class="minimalistBlack" cellpadding="5" width="98%">
			<tr  class="table-head">
				<th width="5%">No</th>
                <th width="30%">Produk</th>
                <th width="10%">Satuan</th>
                <th width="15%">Volume</th>
				<th width="20%">Harga Satuan</th>
                <th width="20%">Nilai</th>
			</tr>
			<?php
			if(!empty($data)){
				$total =0;
				foreach ($data as $dw_key => $dw_val) {
					$total = $total + $dw_val['total'];
					?>
            		<tr>
            			<td><?php echo $dw_key + 1;?></td>
            			<td align="left"><?php echo $dw_val['material_name'];?></td>
            			<td><?php echo $dw_val['measure'];?></td>
						<td><?php echo $dw_val['volume'];?></td>
            			<td align="right"><?php echo $dw_val['price'];?></td>
            			<td align="right"><?php echo number_format($total,0,',','.');?></td>
            		</tr>
            		<?php
				}
			}
			?>

		</table>
		<br />
		<br />
		<table width="98%" border="0" cellpadding="30">
			<tr >
				<td width="20%"></td>
				<td width="75%">
					<table width="100%" border="0" cellpadding="2">
						<tr>
							<td align="center">
								<b>Disetujui Oleh</b>
							</td>
							<td align="center" >
								<b>Dibuat Oleh</b>
							</td>	
						</tr>
						<tr class="">
							<td align="center" height="55px">
								
							</td>
							<td align="center">
								
							</td>
						</tr>
						<tr>
							<td align="center" >
								<b><u>Gervasius K. Limahekin</u></b><br />
								<b>Jabatan : Ka. Plant</b>
							</td>
							<td align="center">
								<b><u>Agustinus Pakaenoni</u></b><br />
								<b>Jabatan : Pj. Logistik</b>
							</td>
						</tr>
					</table>
				</td>
				<td width="5%"></td>
			</tr>
		</table>
	</body>
</html>