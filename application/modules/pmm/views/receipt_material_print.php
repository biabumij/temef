<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $row['no_po'];?></title>
    <?= include 'lib.php'; ?>
    
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
      <tr>
        <td align="center">
          <div style="display: block;font-weight: bold;font-size: 16px;">Surat Jalan Penerimaan Pembelian</div>
        </td>
      </tr>
      <?php
      if(!empty($supplier_name)){
        ?>
        <tr>
          <td align="center">
            <div style="display: block;font-weight: bold;font-size: 14px;"><?php echo $supplier_name;?></div>
          </td>
        </tr>
        <?php
      }
      ?>
      
    </table>
    <br />
    <table class="minimalistBlack" cellpadding="3" width="98%">
      <tr>
                <th width="5%">No</th>
                <th width="5%">Tanggal</th>
                <th width="10%">Rekanan</th>
			        	<th width="10%">No. Pesanan Pembelian</th>
                <th width="10%">No. Surat Jalan</th>
                <th width="5%">Memo</th>
                <th width="10%">No. Kendaraan</th>
                <th width="5%">Supir</th>
                <th width="10%">Produk</th>
                <th width="5%">Volume</th>
			        	<th width="5%">Satuan</th>
                <th width="10%">Harga Satuan</th>
                <th width="10%">Nilai</th>
            </tr>
            <?php
            $total = 0;
            $total_con = 0;
            $total_biaya =0;
            if(!empty($data)){
              $date = false;
              $total_by_date = 0;
            $total_con_by_date = 0;
            $total_biaya_by_date = 0;
              foreach ($data as $key => $row) {

                $biaya = $row['biaya'];
                if($date !== false && $row['date_receipt'] != $date){
                  ?>
                  <tr>
                    <th colspan="9" style="text-align:right">TOTAL <?php echo date('d F Y',strtotime($date));?></th>
                      <th style="text-align:center;"><?php echo number_format($total_by_date,2,',','.');?></th>
                      <th>-</th>
                      <th>-</th>
                      <th style="text-align:right;"><?php echo number_format($total_biaya_by_date,2,',','.');?></th>
                  </tr>
                  <?php
                  $total_by_date = 0;
                  $total_con_by_date = 0;
                  $total_biaya_by_date = 0;
                }
                $total_by_date += $row['volume'];
                $total_con_by_date += $row['volume'];
                $total_biaya_by_date += $row['price'];
                ?>
                <tr>
                  <td><?php echo $key + 1 ;?></td>
                  <td><?php echo date('d-m-Y',strtotime($row['date_receipt']));?></td>
                  <td><?php echo $this->crud_global->GetField('penerima',array('id'=>$row['supplier_id']),'nama');?></td>
				          <td><?php echo $row['no_po'];?></td>
                  <td><?php echo $row['surat_jalan'];?></td>
                  <td><?php echo $row['memo'];?></td>
                  <td><?php echo $row['no_kendaraan'];?></td>
                  <td><?php echo $row['driver'];?></td>
                  <td><?php echo $this->crud_global->GetField('produk',array('id'=>$row['material_id']),'nama_produk');?></td>
				          <td><?php echo number_format($row['volume'],2,',','.');?></td>
                  <td><?php echo $row['measure'];?></td>
                  <td style="text-align:right;"><?php echo number_format($row['harga_satuan'],2,',','.');?></td>
                  <td style="text-align:right;"><?php echo number_format($row['price'],2,',','.');?></td>
                </tr>
                <?php

                if($key == count($data) - 1){
                  ?>
                  <tr>
                    <th colspan="9" style="text-align:right">TOTAL <?= convertDateDBtoIndo($row["date_receipt"]); ?></th>
                      <th style="text-align:center;"><?php echo number_format($total_by_date,2,',','.');?></th>
                      <th>-</th>
                      <th>-</th>
                      <th style="text-align:right;"><?php echo number_format($total_biaya_by_date,2,',','.');?></th>
                  </tr>
                  <?php
                  $total_by_date = 0;
                  $total_by_date = 0;
                  $total_con_by_date = 0;
                  $total_biaya_by_date = 0;
                }
                
                $date = $row['date_receipt'];
                
                $total += $row['volume'];
                $total_con += $row['volume'];
                $total_biaya += $row['price'];
              }
            }
            ?>  
            <tr>
               <th colspan="9" style="text-align:right">TOTAL</th>
               <th style="text-align:center;"><?php echo number_format($total,2,',','.');?></th>
               <th>-</th>
               <th>-</th>
               <th style="text-align:right;"><?php echo number_format($total_biaya,2,',','.');?></th>
           </tr>
    </table>

      
    

  </body>
</html>