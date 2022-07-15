<!doctype html>
<html lang="en" class="fixed">
    
<?php include 'lib.php'; ?>

<head>
    <?php echo $this->Templates->Header();?>
    

    <style type="text/css">
        .form-check {
            display: inline-block;
        }

    </style>
</head>

<body>
<div class="wrap">

    <?php echo $this->Templates->PageHeader();?>
    
    


    <div class="page-body">
        <?php echo $this->Templates->LeftBar();?>
        <div class="content">
            <div class="content-header">
                <div class="leftside-content-header">
                    <ul class="breadcrumbs">
                        <li><i class="fa fa-sitemap" aria-hidden="true"></i><a href="<?php echo base_url();?>">Dashboard</a></li>
                        <li><a href="<?php echo site_url('admin/penjualan');?>"> Penjualan</a></li>
                        <li><a href="<?php echo site_url('admin/penjualan');?>"> Penawaran Penjualan</a></li>
                        <li><a>Detail Penawaran Penjualan</a></li>
                    </ul>
                </div>
            </div>
            <div class="row animated fadeInUp">
                <div class="col-sm-12 col-lg-12">
                    <div class="panel">
                        <div class="panel-header">
                                <div class="">
                                    <h3 class="">Detail Penawaran Penjualan <?php echo $this->pmm_model->GetStatus2($penawaran['status']);?></h3>
                                </div>
                        </div>
                        <div class="panel-content">
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <th width="200px">Nomor</th>
                                    <td>: <?= $penawaran["nomor"] ?></td>
                                </tr>
                                <tr>
                                    <th width="200px">Nama Pelanggan </th>
                                    <td>: <?= $penawaran["nama"] ?></td>
                                </tr>
                                <tr>
                                    <th >Alamat </th>
                                    <td>: <?= $penawaran["client_address"] ?></td>
                                </tr>
                                <tr>
                                    <th >Tanggal</th>
                                    <td>: <?= convertDateDBtoIndo($penawaran["tanggal"]); ?></td>
                                </tr>
								<tr>
                                    <th >Perihal </th>
                                    <td>: <?= $penawaran["syarat_pembayaran"]; ?> Hari</td>
                                </tr>
                                <tr>
                                    <th >Perihal </th>
                                    <td>: <?= $penawaran["perihal"]; ?></td>
                                </tr>
                                <tr>
                                    <th >Total</th>
                                    <td>: <?= $this->filter->Rupiah($penawaran["sub_total"]); ?></td>
                                </tr>
                                <tr>
                                    <th width="100px">Lampiran</th>
                                    <td>:
                                        <?php foreach($lampiran as $l) : ?>
                                        <a href="<?= base_url("uploads/penawaran_penjualan/".$l["lampiran"]) ?>" target="_blank">Lihat bukti  <?= $l["lampiran"] ?> <br></a></td>
                                    <?php endforeach; ?>
                                </tr>
                                <tr>
                                    <th>Persyaratan Harga</th>
                                    <td>: <?= $penawaran["persyaratan_harga"] ?></td>
                                </tr>
                            </table>

                            <table class="table table-bordered table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="5%">No</th>
                                        <th class="text-center" width="20%">Produk</th>
                                        <th class="text-center" width="15%">Volume</th>
                                        <th class="text-center" width="15%">Satuan</th>
                                        <th class="text-center" width="15%">Harga Satuan</th>
                                        <th class="text-center" width="20%">Nilai</th>
                                        <th class="text-center" width="10%">Pajak</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $subtotal = 0;
                                    $tax_pph = 0;
                                    $tax_ppn = 0;
                                    $tax_0 = false;
                                    $total = 0;

                                    ?>
                                    <?php foreach($details as $no => $d) : ?>
                                        <?php
                                        $tax = $this->crud_global->GetField('pmm_taxs',array('id'=>$d['tax_id']),'tax_name');
                                        $measure = $this->crud_global->GetField('pmm_measures',array('id'=>$d['measure']),'measure_name');
                                        ?>
                                        <tr>
                                            <td class="text-center"><?= $no + 1;?></td>
                                            <td class="text-center"><?= $d["nama_produk"] ?></td>
                                            <td class="text-center"><?= $d["qty"]; ?></td>
                                            <td class="text-center"><?= $measure; ?></td>
                                            <td class="text-right"><?= number_format($d['price'],0,',','.'); ?></td>
                                            <td class="text-right"><?= number_format($d['total'],0,',','.'); ?></td>
                                            <td class="text-center"><?= $tax; ?></td>
                                        </tr>
										<?php
                                        $subtotal += $d['total'];
                                        if($d['tax_id'] == 4){
                                            $tax_0 = true;
                                        }
                                        if($d['tax_id'] == 3){
                                            $tax_ppn += $d['tax'];
                                        }
                                        if($d['tax_id'] == 5){
                                            $tax_pph += $d['tax'];
                                        }
                                        ?>
										<?php endforeach; ?>
									</tbody>
                                <!--<tfoot>
                                    <tr>
                                        <th colspan="6" class="text-right">SUBTOTAL</th>
                                        <th  class="text-right"><?= $this->filter->Rupiah($subtotal);?></th>
                                    </tr>
                                    <?php
                                    if($tax_ppn > 0){
                                        ?>
                                        <tr>
                                            <th colspan="6" class="text-right">PPN</th>
                                            <th  class="text-right"><?= $this->filter->Rupiah($tax_ppn);?></th>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                    if($tax_0){
                                        ?>
                                        <tr>
                                            <th colspan="6" class="text-right">Pajak 0%</th>
                                            <th  class="text-right"><?= $this->filter->Rupiah(0);?></th>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                    if($tax_pph > 0){
                                        ?>
                                        <tr>
                                            <th colspan="6" class="text-right">PPh 23</th>
                                            <th  class="text-right"><?= $this->filter->Rupiah($tax_pph);?></th>
                                        </tr>
                                        <?php
                                    }

                                    $total = $subtotal + $tax_ppn - $tax_pph;
                                    ?>

                                    <tr>
                                        <th colspan="6" class="text-right">TOTAL</th>
                                        <th  class="text-right"><?= $this->filter->Rupiah($total);?></th>
                                    </tr>
                                </tfoot>-->
                            </table>
                                       
                            <div class="text-right">  
                                <a href="<?php echo site_url('admin/penjualan');?>" class="btn btn-info" style="margin-top: 10px;"><i class="fa fa-mail-reply"></i> Kembali</a>

                                <?php if($penawaran["status"] === "DRAFT") : ?>
                                    <form class="form-check" action="<?= base_url("penjualan/approvalPenawaran/".$penawaran["id"]) ?>">
                                        <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Setujui</button>        
                                    </form>
                                    <form class="form-check" action="<?= base_url("penjualan/rejectedPenawaran/".$penawaran["id"]) ?>">
                                        <button type="submit" class="btn btn-danger"><i class="fa fa-close"></i> Tolak</button>        
                                    </form>
                                 <?php endif; ?>

                                 <?php if($penawaran["status"] === "OPEN") : ?>
                                    <a href="<?= base_url("penjualan/cetak_penawaran_penjualan/".$penawaran["id"]) ?>" target="_blank" class="btn btn-info" style="margin-top: 10px;"><i class="fa fa-print"></i> Cetak PDF</a>
                                    <?php
                                    if($this->session->userdata('admin_group_id') == 1 || $this->session->userdata('admin_group_id') == 11){
                                        ?>
                                        <a href="<?= base_url("penjualan/closed_penawaran_penjualan/".$penawaran["id"]) ?>" class="btn btn-danger" style="margin-top: 10px;"><i class="fa fa-close"></i> Closed</a>			
                                        <?php
                                    }
                                    ?>
                                <?php endif; ?>
						
							    <!--<a href="<?php echo site_url('penjualan/sunting_penawaran/' . $penawaran["id"] );?>" class="btn btn-success" style="margin-top: 10px;"><i class="fa fa-success"></i> Edit</a>-->

                                <?php if($penawaran["status"] === "CLOSED") : ?>
                                    <a href="<?= base_url("penjualan/cetak_penawaran_penjualan/".$penawaran["id"]) ?>" target="_blank" class="btn btn-info" style="margin-top: 10px;"><i class="fa fa-print"></i> Cetak PDF</a>
                                    <?php
                                    if($this->session->userdata('admin_group_id') == 1){
                                        ?>
                                        <a class="btn btn-danger" style="margin-top: 10px;" onclick="DeleteData('<?= site_url('penjualan/hapusPenawaranPenjualan/' . $penawaran['id']); ?>')"><i class="fa fa-close"></i> Hapus</a>		
                                        <?php
                                    }
                                    ?>
                                <?php endif; ?>
                            </div>                         
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>



    <script type="text/javascript">
        var form_control = '';
    </script>

	<?php echo $this->Templates->Footer();?>

	<script src="<?php echo base_url();?>assets/back/theme/vendor/bootbox.min.js"></script>
    <script src="<?php echo base_url();?>assets/back/theme/vendor/jquery.number.min.js"></script>

    <script src="<?php echo base_url();?>assets/back/theme/vendor/bootbox.min.js"></script>

    <script type="text/javascript">
        $('.form-check').click(function(e){
            e.preventDefault();
            var currentForm = this;
            bootbox.confirm({
                message: "Apakah anda yakin untuk proses data ini ?",
                buttons: {
                    confirm: {
                        label: 'Yes',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: 'No',
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                    if(result){
                        currentForm.submit();
                    }

                }
            });

        }); 
    </script>


</body>
</html>