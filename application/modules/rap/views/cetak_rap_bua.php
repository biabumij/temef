<!DOCTYPE html>
<html>
	<head>
	  <?= include 'lib.php'; ?>
	  <title>RAP BUA</title>
	  
	  <style type="text/css">
	  	body{
	  		font-family: "Open Sans", Arial, sans-serif;
	  	}
	  	table.minimalistBlack {
		  border: 0px solid #000000;
		  width: 100%;
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
		table.head tr th {
		  /*font-size: 14px;*/
		  font-weight: bold;
		  color: #000000;
		  text-align: left;
		  padding: 10px;
		}
		table tr.table-active{
            background-color: #b5b5b5;
        }
        table tr.table-active2{
            background-color: #cac8c8;
        }
		table tr.table-active3{
            background-color: #eee;
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
		<table width="100%" border="0" cellpadding="3">
			<tr>
				<td align="center">
					<div style="display: block;font-weight: bold;font-size: 12px;">RAP BUA</div>
					<?php
					$rap_bua = $this->db->select('rap.*')
					->from('rap_bua rap')
					->where('rap.id',$id)
					->get()->row_array();

					$tanggal = $rap_bua['tanggal_rap_bua'];
					$date = date('Y-m-d',strtotime($tanggal));
					?>
					<?php
					function tgl_indo($date){
						$bulan = array (
							1 =>   'Januari',
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
						$pecahkan = explode('-', $date);
						
						// variabel pecahkan 0 = tanggal
						// variabel pecahkan 1 = bulan
						// variabel pecahkan 2 = tahun
					
						return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
						
					}
					?>

					<div style="display: block;font-weight: bold;font-size: 12px; text-transform: uppercase;">(<?= tgl_indo(date($date)); ?>)</div>
				</td>
			</tr>
		</table>
		<br />
		<br />
		<table class="minimalistBlack" cellpadding="5" width="98%">
			<tr class="table-active">
				<th align="center" width="15%">NO.</th>
				<th align="center" width="25%">AKUN</th>
				<th align="center" width="15%">VOLUME</th>
				<th align="center" width="15%">SATUAN</th>
				<th align="center" width="15%">HARGA SATUAN</th>
				<th align="center" width="15%">TOTAL</th>
            </tr>
			<?php
			$rap_bua_detail = $this->db->select('rpd.*, c.coa')
			->from('rap_bua rap')
			->join('rap_bua_detail rpd','rap.id = rpd.rap_bua_id','left')
			->join('pmm_coa c','rpd.coa = c.id','left')
			->where('rpd.rap_bua_id',$id)
			->get()->result_array();

			file_put_contents("D:\\rap_bua_detail.txt", $this->db->last_query());

           	$no = 0 ;

            $total = 0;
			
           	foreach ($rap_bua_detail as $row) : ?>  
               <tr>
                   <td align="center"><?php echo $no+1;?></td>
                   <td align="left"><?= $row["coa"] ?></td>
				   <td align="center"><?= number_format($row['qty'],0,',','.'); ?></td>
	               <td align="center"><?= $row["satuan"]; ?></td>
	               <td align="right"><?= number_format($row['harga_satuan'],0,',','.'); ?></td>
	               <td align="right"><?= number_format($row['jumlah'],0,',','.'); ?></td>
               </tr>

			<?php
			$no++;
			$total += $row['jumlah'];
			endforeach; ?>

            
            <tr>
                <th colspan="5" align="right">TOTAL</th>
				<th align="right"><?= number_format($total,0,',','.'); ?></th>
            </tr>
           	
		</table>
	</body>
</html>