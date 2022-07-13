<!DOCTYPE html>
<html>
    
    <head>
      <title>JURNAL UMUM</title>
      <?= include 'lib.php'; ?>
      <style type="text/css">
        body{
            font-family: "Open Sans", Arial, sans-serif;
        }
        table.minimalistBlack {
          /*border: 1px solid #ededed;*/
          width: 98%;
          text-align: left;
        }
        table.minimalistBlack td, table.minimalistBlack th {
          border: 0.5px solid #ededed;
          padding: 5px 4px;
        }
        table.minimalistBlack tr td {
          /*font-size: 13px;*/
          /*text-align:center;*/
        }
        table.minimalistBlack tr th {
          /*font-size: 14px;*/
          font-weight: bold;
          padding: 10px;
        }
        table.minimalistBlack .table-akun{
            background-color: #e69500;
            color: #fff;
        }
        table.minimalistBlack .table-akun th{
            color: #fff;
        }

        table tr.table-active{
            background-color: #b5b5b5;
        }
        table tr.table-active2{
            background-color: #cac8c8;
        }
        table tr.table-active3{
            back
            ground-color: #eee;
        }
        hr{
            margin-top:0;
            margin-bottom:30px;
        }
        h3{
            margin-top:0;
        }
      </style>

    </head>
    <body>
        <table width="98%" border="0" cellpadding="3">
            <tr >
                <td align="center">
                    <div style=";font-weight: bold;font-size: 14px;border-bottom: 1px solid #000;border-top: 1px solid #000;">JURNAL UMUM</div>
                </td>
            </tr>
        </table>
        <br /><br />
        <table width="98%" border="0" cellpadding="3">
            <tr>
                <th width="25%">Nomor Transaksi</th>
                <th width="2%">:</th>
                <th width="73%" align="left"><?= $biaya["nomor_transaksi"] ?></th>
            </tr>
            <tr>
                <th>Tanggal Transaksi</th>
                <th >:</th>
                <th align="left"><?= convertDateDBtoIndo($biaya["tanggal_transaksi"]); ?></th>
            </tr>
        </table>
        <br /><br />
        <table class="minimalistBlack" cellpadding="4" width="98%">
            <tr class="table-akun">
                <th width="10%">KODE AKUN</th>
                <th width="30%">NAMA AKUN</th>
                <th width="30%" align="right">DEBIT</th>
                <th width="30%"  align="right">KREDIT</th>
            </tr>
            <?php foreach($detail as $d) : ?>
            <tr>
                <td><?= $d["coa_number"] ?> </td>
                <td><?= $d["coa"]; ?></td>
                <td align="right"><?= number_format($d["debit"],2,',','.'); ?></td>
                <td align="right"><?= number_format($d['kredit'],2,',','.') ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <th></th>
                <th align="right">TOTAL</th>
                <th align="right">Rp.<?= number_format($biaya['total_debit'],2,',','.') ?></th>
                <th align="right">Rp.<?= number_format($biaya['total_kredit'],2,',','.') ?></th>
            </tr>
        </table>
        <br />
        <br />
        <table width="98%" border="0" cellpadding="3">
            <tr>
                <th width="25%">Memo</th>
                <th width="2%">:</th>
                <th width="73%" align="left"><?= $biaya["memo"] ?></th>
            </tr>
        </table>
        <br />
        <br />
        <table width="98%" border="0" cellpadding="15">
			<tr >
				<td width="5%"></td>
				<td width="90%">
					<table width="100%" border="0" cellpadding="2">
						<tr>
							<td align="center" >
								Disetujui oleh
							</td>
							<td align="center" >
								Diperiksa Oleh
							</td>
							<td align="center" >
								Dibuat Oleh
							</td>
						</tr>
						<tr>
							<td align="center" height="40px">
								
							</td>
							<td align="center">
								
							</td>
							<td align="center">
								
							</td>
						</tr>
						<tr>
							<td align="center" >
								<b><u>Gervasius K Hekin</u><br />
								Ka. Plant</b>
							</td>
							<td align="center" >
								<b><u>Debi Khania</u><br />
								Pj. Keuangan & SDM</b>
							</td>
							<td align="center" >
								<b><u>Debi Khania</u><br />
								Kasir</b>
							</td>
						</tr>
					</table>
				</td>
				<td width="5%"></td>
			</tr>
		</table>
    </body>
</html>