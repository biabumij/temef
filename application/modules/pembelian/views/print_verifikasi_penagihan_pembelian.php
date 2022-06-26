<!DOCTYPE html>
<html>
	<head>
	  <title>Verifikasi Dokumen Penagihan Pembelian</title>
	  
	  <style type="text/css">
	  	body{
	  		font-family: "Open Sans", Arial, sans-serif;
	  	}
	  </style>

	</head>
	<body>
		<table width="98%" border="0" cellpadding="3">
			<tr>
				<td align="center">
					<div style="display: block;font-weight: bold;font-size: 12px;">BUKTI PENERIMAAN DAN VERIFIKASI DOKUMEN TAGIHAN</div>
				</td>
			</tr>
		</table>
		<br /><br />
		<table width="98%" border="1" cellpadding="2">
			<tr>
				<td>
					<table width="100%" border="0" cellpadding="3">
						<tr>
							<td colspan="2" width="24%">
								<div style="display: block;font-weight: bold;"><u>DIISI OLEH VEFIKATOR</u></div>
							</td>
							<td width="2%">:</td>
							<td width="74%"></td>
						</tr>
						<tr>
							<td width="4%">1.</td>
							<td width="20%">Nama Rekanan</td>
							<td width="2%">:</td>
							<td width="74%"><?= $row['supplier_name'];?></td>
						</tr>
						<tr>
							<td>2.</td>
							<td>Nomor Kontrak / PO</td>
							<td>:</td>
							<td ><?= $row['nomor_po'].' - '.$row['tanggal_po'];?></td>
						</tr>
						<tr>
							<td>3.</td>
							<td>Nama Barang / Jasa</td>
							<td>:</td>
							<td ><?= $row['nama_barang_jasa'];?></td>
						</tr>
						<tr>
							<td>4.</td>
							<td>Nilai Kontrak / PO</td>
							<td>:</td>
							<td ><?= $row['nilai_kontrak'];?></td>
						</tr>
						<tr>
							<td>5.</td>
							<td>Nilai Tagihan (Net)</td>
							<td>:</td>
							<td ><?= $row['nilai_tagihan'];?></td>
						</tr>
						<tr>
							<td>6.</td>
							<td >PPN / PPh 23</td>
							<td>:</td>
							<td  ><?= $row['ppn'];?></td>
						</tr>
						<tr>
							<td>7.</td>
							<td >Tanggal Invoice</td>
							<td>:</td>
							<td  ><?= $row['tanggal_invoice'];?></td>
						</tr>
						<tr>
							<td>8.</td>
							<td >Tanggal Diterima Proyek</td>
							<td>:</td>
							<td  ><?= $row['tanggal_diterima_proyek'];?></td>
						</tr>
						<tr>
							<td>9.</td>
							<td >Tanggal Diterima Office</td>
							<td>:</td>
							<td  ></td>
						</tr>
						<tr>
							<td>10.</td>
							<td >Metode Pembayaran</td>
							<td>:</td>
							<td ><?= $row['metode_pembayaran'];?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<br />
		<br />
		<table width="98%" border="1" cellpadding="3">
			<tr style="font-weight: bold">
				<th width="30%">
					<table width="100%" border="0">
						<tr>
							<td width="10%" style="vertical-align: middle;">A.</td>
							<td width="90%">KELENGKAPAN DATA <br />(LENGKAP DAN BENAR)</td>
						</tr>
					</table>
				</th>
				<th width="20%" style="text-align: center">ADA / TIDAK (V/X)</th>
				<th width="50%" style="text-align: center">KETERANGAN</th>
			</tr>
			<tr>
				<th width="30%">
					<table width="100%" border="0">
						<tr>
							<td width="10%" style="vertical-align: middle;">1.</td>
							<td width="90%">Invoice</td>
						</tr>
					</table>
				</th>
				<th width="20%" style="text-align: center"><?= $this->pmm_finance->CheckorNo($row['invoice']);?></th>
				<th width="50%" ><?= $row['invoice_keterangan'];?></th>
			</tr>
			<tr>
				<th width="30%">
					<table width="100%" border="0">
						<tr>
							<td width="10%" style="vertical-align: middle;">2.</td>
							<td width="90%">Kwitansi</td>
						</tr>
					</table>
				</th>
				<th width="20%" style="text-align: center"><?= $this->pmm_finance->CheckorNo($row['kwitansi']);?></th>
				<th width="50%" ><?= $row['kwitansi_keterangan'];?></th>
			</tr>
			<tr>
				<th width="30%">
					<table width="100%" border="0">
						<tr>
							<td width="10%" style="vertical-align: middle;">3.</td>
							<td width="90%">Faktur Pajak</td>
						</tr>
					</table>
				</th>
				<th width="20%" style="text-align: center"><?= $this->pmm_finance->CheckorNo($row['faktur']);?></th>
				<th width="50%" ><?= $row['faktur_keterangan'];?></th>
			</tr>
			<tr>
				<th width="30%">
					<table width="100%" border="0">
						<tr>
							<td width="10%" style="vertical-align: middle;">4.</td>
							<td width="90%">Berita Acara Pembayaran (BAP)</td>
						</tr>
					</table>
				</th>
				<th width="20%" style="text-align: center"><?= $this->pmm_finance->CheckorNo($row['bap']);?></th>
				<th width="50%" ><?= $row['bap_keterangan'];?></th>
			</tr>
			<tr>
				<th width="30%">
					<table width="100%" border="0">
						<tr>
							<td width="10%" style="vertical-align: middle;">5.</td>
							<td width="90%">Berita Acara Serah Terima (BAST)</td>
						</tr>
					</table>
				</th>
				<th width="20%" style="text-align: center"><?= $this->pmm_finance->CheckorNo($row['bast']);?></th>
				<th width="50%" ><?= $row['bast_keterangan'];?></th>
			</tr>
			<tr>
				<th width="30%">
					<table width="100%" border="0">
						<tr>
							<td width="10%" style="vertical-align: middle;">6.</td>
							<td width="90%">Surat Jalan</td>
						</tr>
					</table>
				</th>
				<th width="20%" style="text-align: center"><?= $this->pmm_finance->CheckorNo($row['surat_jalan']);?></th>
				<th width="50%" ><?= $row['surat_jalan_keterangan'];?></th>
			</tr>
			<tr>
				<th width="30%">
					<table width="100%" border="0">
						<tr>
							<td width="10%" style="vertical-align: middle;">7.</td>
							<td width="90%">Copy Kontrak (P0)</td>
						</tr>
					</table>
				</th>
				<th width="20%" style="text-align: center"><?= $this->pmm_finance->CheckorNo($row['copy_po']);?></th>
				<th width="50%" ><?= $row['copy_po_keterangan'];?></th>
			</tr>
		</table>
		<br />
		<br />
		<table width="98%" border="1" cellpadding="3">
			<tr >
				<th width="100%">
					<table width="100%" border="0">
						<tr>
							<td width="20%" style="vertical-align: middle;">Catatan</td>
							<td width="80%" style="height:80px"><?= $row['catatan'];?></td>
						</tr>
					</table>
				</th>
			</tr>
		</table>
		<br />
		<br />
		<?php
        $kepala_divisi = $this->pmm_model->GetNameGroup(15);
		$kepala_logistik = $this->pmm_model->GetNameGroup(11);
        $m_keu = $this->pmm_model->GetNameGroup(10);
        ?>  
        <table width="98%" border="0" cellpadding="0">
            <tr>
                <td width="100%">
                     <table width="100%" border="1" cellpadding="2">
                        <tr class="table-active3">
                            <td align="center" colspan="2" width="60%">
                                Diperiksa Oleh (Proyek)
                            </td>
                            <td align="center" width="20%">
                                Disetujui Oleh
                            </td>
                            <td align="center" width="20%">
                                Mengetahui
                            </td>
                        </tr>
                        <tr class="">
                            <td align="center" height="75px">
                            </td>
                            <td align="center">
                            </td>
                            <td align="center">           
                            </td>       
                            <td align="center">   
                            </td>
                        </tr>
                        <tr class="table-active3">
                            <td align="center">
                                <?= $row['verifikator'];?>
                            </td>
                            <td align="center">
                                <?=  $kepala_logistik['admin_name'];?>
                            </td>
                            <td align="center">
                                 <?=  $kepala_divisi['admin_name'];?> 
                            </td>
                            <td align="center">
                                <?=  $m_keu['admin_name'];?>
                            </td>
                        </tr>
                        <tr class="table-active3">
                            <td align="center">
                                Pj. Keuangan Proyek 
                            </td>
                            <td align="center">
            					Pj. Logistik
                               </td>
                            <td align="center">
            					Kepala Proyek
                            </td>
                            <td align="center" > 
                                M. Keuangan & SDM
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

	</body>
</html>