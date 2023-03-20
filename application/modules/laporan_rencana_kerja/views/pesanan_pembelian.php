<!doctype html>
<html lang="en" class="fixed">
<head>
    <?php echo $this->Templates->Header();?>

    <style type="text/css">
        .table-center th, .table-center td{
            text-align:center;
        }
        .table-warning tr, .table-warning th, .table-warning td{
            border: 1px solid white;
            font-weight:bold;
        }
    </style>
</head>

<body>
    <div class="wrap">
        
        <?php echo $this->Templates->PageHeader();?>

        <div class="page-body">
            <?php echo $this->Templates->LeftBar();?>
            <div class="content" style="padding:0;">
                <div class="content-header">
                    <div class="leftside-content-header">
                        <ul class="breadcrumbs">
                            <li><i class="fa fa-sitemap" aria-hidden="true"></i><a href="<?php echo site_url('admin');?>">Dashboard</a></li>
                            <li>
                                <a href="<?php echo site_url('admin/laporan_rencana_kerja');?>"> <i class="fa fa-calendar" aria-hidden="true"></i> Pesanan Pembelian</a></li>
                            <li><a>Buat Pesanan Pembelian</a></li>
                        </ul>
                    </div>
                </div>
                <div class="row animated fadeInUp">
                    <div class="col-sm-12 col-lg-12">
                        <div class="panel">
                            <div class="panel-header"> 
                                <h3 >Buat Pesanan Pembelian</h3>
                            </div>
                            <div class="panel-content">
                                <div class="table-responsive">
                                    

                                    <?php
                                    $kategori  = $this->db->order_by('nama_kategori_produk', 'asc')->select('*')->get_where('kategori_produk', array('status' => 'PUBLISH'))->result_array();
                                    ?>
                                    <form method="POST" action="<?php echo site_url('pembelian/submit_pesanan_pembelian');?>" id="form-po" enctype="multipart/form-data" autocomplete="off">
                                        <table id="table-product" class="table table-bordered table-striped table-condensed table-center">
                                            <tbody>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <label>Subyek</label>
                                                        <input type="text" name="subject" class="form-control" required="" autocomplete="off"/>
                                                    </div>
                                                    <br />
                                                    <br />
                                                    <br />
                                                    <div class="col-sm-3">
                                                        <label>Tanggal Pesanan Pembelian</label>
                                                        <input type="date" name="date_po" class="form-control text-left" required="">
                                                    </div>
                                                    <br />
                                                    <br />
                                                    <br />
                                                    <div class="col-sm-6">
                                                        <label>No. Pesanan Pembelian</label>
                                                        <input type="text" name="no_po" class="form-control text-left" value="<?= $no_po;?>" required="">
                                                    </div>
                                                    <br />
                                                    <br />
                                                    <br />
                                                    <div class="col-sm-6">
                                                        <label>Kategori</label>
                                                        <select name="kategori_id" class="form-control select2" required="" autocomplete="off">
                                                            <option value="">Pilih Kategori</option>
                                                            <?php
                                                            foreach ($kategori as $key => $kat) {
                                                                ?>
                                                                <option value="<?php echo $kat['id'];?>"><?php echo $kat['nama_kategori_produk'];?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <br />
                                                    <br />
                                                    <br />
                                                </div>
                                            </tbody>
                                        </table>
                                        <table class="table-warning table-center" width="50%">
                                            <tr>
                                                <th colspan="5" style="background-color:#404040;color:white;">VOLUME (M3)</th>
                                            </tr>
                                            <tr>
                                                <th width="10%" style="background-color:#d63232; color:white;" rowspan="2">KEBUTUHAN</th>
                                                <th width="10%" style="background-color:#c1e266;" rowspan="2">SISA STOK</th>
                                                <th width="20%" style="background-color:#539ed6; color:white;" colspan="2">PROSES PO</th>
                                                <th width="10%" style="background-color:#cbcbcb;" rowspan="2">SISA KEBUTUHAN</th>
                                            </tr>         
                                            <tr>
                                                <th style="background-color:#539ed6;color:white;">TOTAL PO</th>
                                                <th style="background-color:#539ed6;color:white;">TERIMA PO</th>
                                            </tr>
                                            <tr>
                                                <td class="text-center">
                                                    <input style="background-color:#d63232; color:white;" class="form-control input-sm text-center" value="<?php echo number_format($kebutuhan,2,',','.');?>" readonly=""/>
                                                </td>
                                                <td class="text-center">
                                                    <input style="background-color:#c1e266;" class="form-control input-sm text-center" value="<?php echo number_format($stock_opname['display_volume'],2,',','.');?>" readonly=""/>
                                                </td>
                                                <td class="text-center">
                                                    <input style="background-color:#539ed6; color:white;" class="form-control input-sm text-center" value="<?php echo number_format($purchase_order,2,',','.');?>" readonly=""/>
                                                </td>
                                                <td class="text-center">
                                                    <input style="background-color:#539ed6; color:white;" class="form-control input-sm text-center" value="<?php echo number_format($total_receipt,2,',','.');?>" readonly=""/>
                                                </td>
                                                <td class="text-center">
                                                    <input style="background-color:#cbcbcb;" class="form-control input-sm text-center" value="<?php echo number_format($kebutuhan - $stock_opname['display_volume'] - $purchase_order,2,',','.');?>" readonly=""/>
                                                </td>
                                            </tr>
                                        </table>
                                        <br />
                                        <table id="table-product" class="table table-bordered table-striped table-condensed table-center">
                                            <thead>
                                                <tr>
                                                    <th width="5%" rowspan="2">NO.</th>
                                                    <th>REKANAN</th>
                                                    <th>PRODUK</th>
                                                    <th>VOLUME</th>
                                                    <th>SATUAN</th>
                                                    <th>HARGA</th>
                                                    <th>NILAI</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <input type="hidden" name="request_no" class="form-control input-sm text-left" value="<?= $request_no;?>"/>
                                                    <input type="hidden" name="measure_id" class="form-control input-sm text-left" value="<?= $details['measure'];?>"/>

                                                    <input type="hidden" name="supplier_id" class="form-control input-sm text-left" value="<?= $row['supplier_id'];?>"/>
                                                    <input type="hidden" name="date_pkp" class="form-control input-sm text-left" value="2021-02-10"/>
                                                    <input type="hidden" name="rekanan" class="form-control input-sm text-left" value="<?= $row['syarat_pembayaran'];?>"/>
                                                    <input type="hidden" name="penawaran_pembelian_id" class="form-control input-sm text-left" value="<?= $row['id'];?>"/>
                                                    <input type="hidden" name="produk" class="form-control input-sm text-left" value="<?= $details['material_id'];?>"/>
                                                    <input type="hidden" name="tax_id" class="form-control input-sm text-left" value="<?= $details['tax_id'];?>"/>
                                                    <input type="hidden" name="pajak_id" class="form-control input-sm text-left" value="<?= $details['pajak_id'];?>"/>
                                                   
                                                    <td class="text-center">1.</td>
                                                    <td class="text-left">
                                                        <input class="form-control input-sm text-center" value="<?= $row['supplier'];?>" readonly=""/>
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-control input-sm text-center" value="<?= $details['material_id'] = $this->crud_global->GetField('produk',array('id'=>$details['material_id']),'nama_produk');?>" readonly=""/>
                                                    </td>
                                                    <td class="text-center">
                                                        <input name="volume" id="volume" onchange="changeData(1)" class="form-control numberformat text-center" value="<?php echo number_format($kebutuhan - $stock_opname['display_volume'] - $purchase_order,2,',','.');?>"/>
                                                    </td>
                                                    <td class="text-center">
                                                        <input name="satuan" class="form-control input-sm text-center" value="<?= $details['measure'] = $this->crud_global->GetField('pmm_measures',array('id'=>$details['measure']),'measure_name');?>" readonly=""/>
                                                    </td>
                                                    <td class="text-center">
                                                        <input name="harsat" id="harsat" onchange="changeData(1)" class="form-control rupiahformat input-sm text-center" value="<?php echo number_format($details['price'],0,',','.');?>" readonly=""/>
                                                    </td>
                                                    <?php
                                                    $a = round($kebutuhan - $stock_opname['display_volume'] - $purchase_order,2);
                                                    $b = $details['price'];
                                                    $nilai = $a * $b
                                                    ?>
                                                    <td class="text-center">
                                                        <input name="nilai" id="nilai" class="form-control rupiahformat input-sm text-center" value="<?php echo number_format($nilai,0,',','.');?>" readonly=""/>
                                                    </td>
                                                </tr>
                                                <?php
                                                if ($details['tax_id'] == 4) {
                                                    $tax_0_1 = true;
                                                }
                                                if ($details['tax_id'] == 3) {
                                                    $tax_ppn_1 = ($nilai * 10) / 100;
                                                }
                                                if ($details['tax_id'] == 5) {
                                                    $tax_pph_1 = ($nilai * 2) / 100;
                                                }
                                                if ($details['tax_id'] == 6) {
                                                    $tax_ppn11_1 = ($nilai * 11) / 100;
                                                }
                                                if ($details['pajak_id'] == 4) {
                                                    $tax_0_2 = true;
                                                }
                                                if ($details['pajak_id'] == 3) {
                                                    $tax_ppn_2 = ($nilai * 10) / 100;
                                                }
                                                if ($details['pajak_id'] == 5) {
                                                    $tax_pph_2 = ($nilai * 2) / 100;
                                                }
                                                if ($details['pajak_id'] == 6) {
                                                    $tax_ppn11_2 = ($nilai * 11) / 100;
                                                }

                                                $ppn_1 = ($tax_ppn_1 - $tax_pph_1 + $tax_ppn11_1);
                                                $ppn_2 = ($tax_ppn_2 - $tax_pph_2 + $tax_ppn11_2);
                                                ?>
                                                <input type="hidden" name="tax" class="form-control input-sm text-left" value="<?= number_format($ppn_1,0,',','.');?>"/>
                                                <input type="hidden" name="pajak" class="form-control input-sm text-left" value="<?= number_format($ppn_2,0,',','.');?>"/>
                                                <input type="hidden" name="total" class="form-control input-sm text-left" value="<?= number_format($nilai,0,',','.');?>"/>
                                            <div>
                                            </tbody>
                                        </table>
                                        <table id="table-product" class="table table-bordered table-striped table-condensed table-center">
                                            <tbody>
                                                <div class="col-sm-8">
                                                    <label>Memo</label>
                                                    <textarea id="about_text" name="memo" class="form-control" data-required="false" rows="20">
<p style="font-size:6;"><b>Syarat &amp; Ketentuan :</b></p>
<p style="font-size:6;">1.&nbsp;Waktu Penyerahan : 2 Februari 2023 s/d 8 Februari 2023</p>
<p style="font-size:6;">2.&nbsp;Tempat Penyerahan : Proyek Bendungan Temef, Desa Konbaki, Kecamatan Polen, KAB. TTS</p>
<p style="font-size:6;">3.&nbsp;Cara Pembayaran : 30 (tiga puluh) hari kerja setelah berkas tagihan dinyatakan lolos verifikasi keuangan PT. Bia Bumi Jayendra, dengan melampirkan</p>
<p style="font-size:6;">&nbsp;&nbsp;&nbsp; dokumen sebagai berikut :</p>
<p style="font-size:6;">&nbsp;&nbsp;&nbsp;3.1 Tagihan</p>
<p style="font-size:6;">&nbsp;&nbsp;&nbsp;3.2 Kwitansi</p>
<p style="font-size:6;">&nbsp;&nbsp;&nbsp;3.3 BAP (Berita Acara Pembayaran)</p>
<p style="font-size:6;">&nbsp;&nbsp;&nbsp;3.4 BAST (Berita Acara Serah Terima) &amp; rekap surat jalan yang ditandatangani oleh pihak pemberi order dan penerima order</p>
<p style="font-size:6;">&nbsp;&nbsp;&nbsp;3.5 Surat Jalan Asli (Nomor PO harus tercantum pada setiap surat jalan)</p>
<p style="font-size:6;">&nbsp;&nbsp;&nbsp;3.6 PO</p>
<p style="font-size:6;">&nbsp;&nbsp;&nbsp;3.7 Faktur Pajak</p>
<p style="font-size:6;">4. Lain-lain :</p>
<p style="font-size:6;">&nbsp;&nbsp;&nbsp;4.1 Barang harus dalam kondisi 100% baik</p>
<p style="font-size:6;">&nbsp;&nbsp;&nbsp;4.2 Barang dikembalikan apabila tidak sesuai dengan pesanan</p>
                                                    </textarea>
                                                </div>
                                                </div>
                                            </tbody>
                                        </table>
                                        <div class="row">
                                        <div class="col-sm-12 text-right">
                                            <a href="<?php echo site_url('admin/pembelian');?>" class="btn btn-danger" style="margin-bottom:0;"><i class="fa fa-close"></i> Batal</a>
                                            <button type="submit" class="btn btn-success"><i class="fa fa-send"></i>  Kirim</button>
                                        </div>
                                        <br />
                                        <br />
                                        <br />
                                    </div>
                                    </form>
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
    <script src="<?php echo base_url();?>assets/back/theme/vendor/jquery.number.min.js"></script>
    <script src="<?php echo base_url();?>assets/back/theme/vendor/daterangepicker/moment.min.js"></script>
    <script src="<?php echo base_url();?>assets/back/theme/vendor/daterangepicker/daterangepicker.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/back/theme/vendor/daterangepicker/daterangepicker.css">
    <script src="<?php echo base_url();?>assets/back/theme/vendor/bootbox.min.js"></script>

    <script type="text/javascript">
        
    $('input.numberformat').number(true, 2,',','.' );
    $('input.rupiahformat').number(true, 0,',','.' );

    tinymce.init({
    selector: 'textarea#about_text',
    height: 200,
    menubar: false,
    });

    $('#form-po').submit(function(e){
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

    function changeData(id)
        {

            var volume = $('#volume').val();
            var harsat = $('#harsat').val();
            var nilai = $('#nilai').val();

            volume = (volume);
            $('#nilai').text($.number(volume, 2,',','.' ));
            harsat = (harsat);
            $('#harsat').val(harsat);
            nilai = (volume * harsat);
            $('#nilai').val(nilai);
            $('#nilai').text($.number(nilai, 0,',','.' ));

        }
    function getTotal()
    {
        var nilai = $('nilai').val();

        nilai = parseFloat($('#volume').val()) * parseFloat($('#harsat').val());
        
        $('#nilai').val(nilai);
        $('#nilai').text($.number( nilai, 0,',','.' ));

        nilai = parseFloat(nilai);
        $('#nilai').val(nilai);
        $('#nilai').text($.number( nilai, 0,',','.' ));
    }
    </script>
    
</body>
</html>
