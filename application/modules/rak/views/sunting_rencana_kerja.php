<!doctype html>
<html lang="en" class="fixed">
<head>
    <?php echo $this->Templates->Header();?>

    <style type="text/css">
        .table-center th{
            text-align:center;
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
                            <li><i class="fa fa-money" aria-hidden="true"></i>RAP</li>
                            
                            <li><a>Rencana Kerja</a></li>
                        </ul>
                    </div>
                </div>
                <div class="row animated fadeInUp">
                    <div class="col-sm-12 col-lg-12">
                        <div class="panel">
                            <div class="panel-header"> 
                                <div class="">
                                    <h3 class="">Rencana Kerja</h3>
                                    
                                </div>
                            </div>
                            <div class="panel-content">
                                <form method="POST" action="<?php echo site_url('rak/submit_sunting_rencana_kerja');?>" id="form-po" enctype="multipart/form-data" autocomplete="off">
                                    <input type="hidden" name="id" value="<?= $rak["id"] ?>">
                                    <table class="table table-bordered table-striped">
                                        <?php
                                        $tanggal = $rak['tanggal_rencana_kerja'];
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
                                        <tr>
                                            <th width="200px">Tanggal</th>
                                            <td>: <?= tgl_indo(date($date)); ?></td>
                                        </tr>
                                        <tr>
                                            <th width="100px">Lampiran</th>
                                            <td>:  
                                                <?php foreach($lampiran as $l) : ?>                                    
                                                <a href="<?= base_url("uploads/rak_biaya/".$l["lampiran"]) ?>" target="_blank">Lihat bukti  <?= $l["lampiran"] ?> <br></a></td>
                                                <?php endforeach; ?>
                                        </tr>
                                    </table>
                                    <table id="table-product" class="table table-bordered table-striped table-condensed table-center">
                                            <tr>
                                                <th width="5%">NO.</th>
                                                <th width="25%">URAIAN</th>
                                                <th width="25%">VOLUME</th>
                                                <th width="10%">SATUAN</th>
                                                <th width="40%">KOMPOSISI</th>     
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center">1.</td>
                                                <td class="text-left">Beton K 125 (10±2)</td>
                                                <td class="text-right"><input type="text" id="vol_produk_a" name="vol_produk_a" class="form-control numberformat text-right" value="<?php echo number_format($rak["vol_produk_a"],2,',','.');?>" onchange="changeData(1)" required="" autocomplete="off"></td>
                                                <td class="text-center">M3</td>
                                                <td class="text-center">
                                                    <select id="komposisi_125" name="komposisi_125" class="form-control input-sm">
                                                        <option value="">Pilih Komposisi</option>
                                                        <?php
                                                        if (!empty($komposisi)) {
                                                            foreach ($komposisi as $kom) {
                                                        ?>
                                                                <option value="<?php echo $kom['id']; ?>"><?php echo $kom['jobs_type']; ?> - (<?= date('d/F/Y',strtotime($kom['date_agregat']));?>)</option>
                                                            <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">2.</td>
                                                <td class="text-left">Beton K 225 (10±2)</td>
                                                <td class="text-right"><input type="text" id="vol_produk_b" name="vol_produk_b" class="form-control numberformat text-right" value="<?php echo number_format($rak["vol_produk_b"],2,',','.');?>" onchange="changeData(1)" required="" autocomplete="off"></td>
                                                <td class="text-center">M3</td>
                                                <td class="text-center">
                                                    <select id="komposisi_225" name="komposisi_225" class="form-control input-sm">
                                                        <option value="">Pilih Komposisi</option>
                                                        <?php
                                                        if (!empty($komposisi)) {
                                                            foreach ($komposisi as $kom) {
                                                        ?>
                                                                <option value="<?php echo $kom['id']; ?>"><?php echo $kom['jobs_type']; ?> - (<?= date('d/F/Y',strtotime($kom['date_agregat']));?>)</option>
                                                            <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">3.</td>
                                                <td class="text-left">Beton K 250 (10±2)</td>
                                                <td class="text-right"><input type="text" id="vol_produk_c" name="vol_produk_c" class="form-control numberformat text-right" value="<?php echo number_format($rak["vol_produk_c"],2,',','.');?>" onchange="changeData(1)" required="" autocomplete="off"></td>
                                                <td class="text-center">M3</td>
                                                <td class="text-center">
                                                    <select id="komposisi_250" name="komposisi_250" class="form-control input-sm">
                                                        <option value="">Pilih Komposisi</option>
                                                        <?php
                                                        if (!empty($komposisi)) {
                                                            foreach ($komposisi as $kom) {
                                                        ?>
                                                                <option value="<?php echo $kom['id']; ?>"><?php echo $kom['jobs_type']; ?> - (<?= date('d/F/Y',strtotime($kom['date_agregat']));?>)</option>
                                                            <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">4.</td>
                                                <td class="text-left">Beton K 250 (18±2)</td>
                                                <td class="text-right"><input type="text" id="vol_produk_d" name="vol_produk_d" class="form-control numberformat text-right" value="<?php echo number_format($rak["vol_produk_d"],2,',','.');?>" onchange="changeData(1)"required="" autocomplete="off"></td>
                                                <td class="text-center">M3</td>
                                                <td class="text-center">
                                                    <select id="komposisi_250_2" name="komposisi_250_2" class="form-control input-sm">
                                                        <option value="">Pilih Komposisi</option>
                                                        <?php
                                                        if (!empty($komposisi)) {
                                                            foreach ($komposisi as $kom) {
                                                        ?>
                                                                <option value="<?php echo $kom['id']; ?>"><?php echo $kom['jobs_type']; ?> - (<?= date('d/F/Y',strtotime($kom['date_agregat']));?>)</option>
                                                            <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2" class="text-right">GRAND TOTAL</td>
                                                <td>
                                                <input type="text" id="sub-total-val" name="sub_total" value="0" class="form-control numberformat tex-left text-right" readonly="">
                                                </td>
                                                <td></td>
                                            </tr> 
                                        </tfoot>
                                    </table>
                                    <table id="table-product" class="table table-bordered table-striped table-condensed table-center">
                                        <thead>
                                            <tr class="text-center">
                                                <th width="5%">NO.</th>
                                                <th width="15%">KEBUTUHAN BAHAN</th>
                                                <th width="50%">PENAWARAN</th>
                                                <th width="30%">HARGA SATUAN</th>                                 
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center">1.</td>
                                                <td>Semen</td>
                                                <td class="text-center"><select id="penawaran_id_semen" name="penawaran_id_semen" class="form-control">
                                                    <option value="">Pilih Penawaran</option>
                                                    <?php

                                                    foreach ($semen as $key => $sm) {
                                                        ?>
                                                        <option value="<?php echo $sm['penawaran_id'];?>" data-supplier_id="<?php echo $sm['supplier_id'];?>" data-measure="<?php echo $sm['measure'];?>" data-price="<?php echo $sm['price'];?>" data-tax_id="<?php echo $sm['tax_id'];?>" data-tax="<?php echo $sm['tax'];?>" data-penawaran_id="<?php echo $sm['penawaran_id'];?>"><?php echo $sm['nama'];?> - <?php echo $sm['nomor_penawaran'];?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                                </td>
                                                <td>
                                                    <input type="text" id="price_semen" name="price_semen" class="form-control rupiahformat text-right" value="" required="" readonly="" autocomplete="off">
                                                    <input type="hidden" id="measure_semen" name="measure_semen" class="form-control text-right" value="" required="" readonly="" autocomplete="off">
                                                    <input type="hidden" id="tax_id_semen" name="tax_id_semen" class="form-control text-right" value="" required="" readonly="" autocomplete="off">
                                                    <input type="hidden" id="supplier_id_semen" name="supplier_id_semen" class="form-control text-right" value="" required="" readonly="" autocomplete="off">
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="text-center">2.</td>
                                                <td>Pasir</td>
                                                <td class="text-center"><select id="penawaran_id_pasir" name="penawaran_id_pasir" class="form-control">
                                                    <option value="">Pilih Penawaran</option>
                                                    <?php

                                                    foreach ($pasir as $key => $sm) {
                                                        ?>
                                                        <option value="<?php echo $sm['penawaran_id'];?>" data-supplier_id="<?php echo $sm['supplier_id'];?>" data-measure="<?php echo $sm['measure'];?>" data-price="<?php echo $sm['price'];?>" data-tax_id="<?php echo $sm['tax_id'];?>" data-tax="<?php echo $sm['tax'];?>" data-penawaran_id="<?php echo $sm['penawaran_id'];?>"><?php echo $sm['nama'];?> - <?php echo $sm['nomor_penawaran'];?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                                </td>
                                                <td>
                                                    <input type="text" id="price_pasir" name="price_pasir" class="form-control rupiahformat text-right" value="" required="" readonly="" autocomplete="off">
                                                    <input type="hidden" id="measure_pasir" name="measure_pasir" class="form-control text-right" value="" required="" readonly="" autocomplete="off">
                                                    <input type="hidden" id="tax_id_pasir" name="tax_id_pasir" class="form-control text-right" value="" required="" readonly="" autocomplete="off">
                                                    <input type="hidden" id="supplier_id_pasir" name="supplier_id_pasir" class="form-control text-right" value="" required="" readonly="" autocomplete="off">
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="text-center">3.</td>
                                                <td>Batu Split 10-20</td>
                                                <td class="text-center"><select id="penawaran_id_batu1020" name="penawaran_id_batu1020" class="form-control">
                                                    <option value="">Pilih Penawaran</option>
                                                    <?php

                                                    foreach ($batu1020 as $key => $sm) {
                                                        ?>
                                                        <option value="<?php echo $sm['penawaran_id'];?>" data-supplier_id="<?php echo $sm['supplier_id'];?>" data-measure="<?php echo $sm['measure'];?>" data-price="<?php echo $sm['price'];?>" data-tax_id="<?php echo $sm['tax_id'];?>" data-tax="<?php echo $sm['tax'];?>" data-penawaran_id="<?php echo $sm['penawaran_id'];?>"><?php echo $sm['nama'];?> - <?php echo $sm['nomor_penawaran'];?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                                </td>
                                                <td>
                                                    <input type="text" id="price_batu1020" name="price_batu1020" class="form-control rupiahformat text-right" value="" required="" readonly="" autocomplete="off">
                                                    <input type="hidden" id="measure_batu1020" name="measure_batu1020" class="form-control text-right" value="" required="" readonly="" autocomplete="off">
                                                    <input type="hidden" id="tax_id_batu1020" name="tax_id_batu1020" class="form-control text-right" value="" required="" readonly="" autocomplete="off">
                                                    <input type="hidden" id="supplier_id_batu1020" name="supplier_id_batu1020" class="form-control text-right" value="" required="" readonly="" autocomplete="off">
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="text-center">4.</td>
                                                <td>Batu Split 20-30</td>
                                                <td class="text-center"><select id="penawaran_id_batu2030" name="penawaran_id_batu2030" class="form-control">
                                                    <option value="">Pilih Penawaran</option>
                                                    <?php

                                                    foreach ($batu2030 as $key => $sm) {
                                                        ?>
                                                        <option value="<?php echo $sm['penawaran_id'];?>" data-supplier_id="<?php echo $sm['supplier_id'];?>" data-measure="<?php echo $sm['measure'];?>" data-price="<?php echo $sm['price'];?>" data-tax_id="<?php echo $sm['tax_id'];?>" data-tax="<?php echo $sm['tax'];?>" data-penawaran_id="<?php echo $sm['penawaran_id'];?>"><?php echo $sm['nama'];?> - <?php echo $sm['nomor_penawaran'];?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                                </td>
                                                <td>
                                                    <input type="text" id="price_batu2030" name="price_batu2030" class="form-control rupiahformat text-right" value="" required="" readonly="" autocomplete="off">
                                                    <input type="hidden" id="measure_batu2030" name="measure_batu2030" class="form-control text-right" value="" required="" readonly="" autocomplete="off">
                                                    <input type="hidden" id="tax_id_batu2030" name="tax_id_batu2030" class="form-control text-right" value="" required="" readonly="" autocomplete="off">
                                                    <input type="hidden" id="supplier_id_batu2030" name="supplier_id_batu2030" class="form-control text-right" value="" required="" readonly="" autocomplete="off">
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="text-center">5.</td>
                                                <td>BBM Solar</td>
                                                <td class="text-center"><select id="penawaran_id_solar" name="penawaran_id_solar" class="form-control">
                                                    <option value="">Pilih Penawaran</option>
                                                    <?php

                                                    foreach ($solar as $key => $sm) {
                                                        ?>
                                                        <option value="<?php echo $sm['penawaran_id'];?>" data-supplier_id="<?php echo $sm['supplier_id'];?>" data-measure="<?php echo $sm['measure'];?>" data-price="<?php echo $sm['price'];?>" data-tax_id="<?php echo $sm['tax_id'];?>" data-tax="<?php echo $sm['tax'];?>" data-penawaran_id="<?php echo $sm['penawaran_id'];?>"><?php echo $sm['nama'];?> - <?php echo $sm['nomor_penawaran'];?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                                </td>
                                                <td>
                                                    <input type="text" id="price_solar" name="price_solar" class="form-control rupiahformat text-right" value="" required="" readonly="" autocomplete="off">
                                                    <input type="hidden" id="measure_solar" name="measure_solar" class="form-control text-right" value="" required="" readonly="" autocomplete="off">
                                                    <input type="hidden" id="tax_id_solar" name="tax_id_solar" class="form-control text-right" value="" required="" readonly="" autocomplete="off">
                                                    <input type="hidden" id="supplier_id_solar" name="supplier_id_solar" class="form-control text-right" value="" required="" readonly="" autocomplete="off">
                                                </td>
                                            </tr>
                                                        
                                        </tbody>
                                    </table>    
									<br />
                                    <div class="row">
                                        <div class="col-sm-12 text-right">
                                            <a href="<?= site_url('admin/rencana_kerja');?>" class="btn btn-danger" style="margin-bottom:0;"><i class="fa fa-close"></i> Batal</a>
                                            <button type="submit" class="btn btn-success"><i class="fa fa-send"></i> Kirim</button>
                                        </div>
                                    </div>
                                </form>
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
        
        $('.form-select2').select2();

        $('input.numberformat').number(true, 2,',','.' );
        $('input.rupiahformat').number(true, 0,',','.' );

        $('.dtpicker').daterangepicker({
            singleDatePicker: true,
            showDropdowns : true,
            locale: {
              format: 'DD-MM-YYYY'
            }
        });
        $('.dtpicker').on('apply.daterangepicker', function(ev, picker) {
              $(this).val(picker.startDate.format('DD-MM-YYYY'));
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
			var vol_produk_a = $('#vol_produk_a').val();
            var vol_produk_b = $('#vol_produk_b').val();
            var vol_produk_c = $('#vol_produk_c').val();
            var vol_produk_d = $('#vol_produk_d').val();
            				
			vol_produk_a = ( vol_produk_a);
            $('#vol_produk_a').val(vol_produk_a);
            vol_produk_b = ( vol_produk_b);
            $('#vol_produk_b').val(vol_produk_b);
            vol_produk_c = ( vol_produk_c);
            $('#vol_produk_c').val(vol_produk_c);
            vol_produk_d = ( vol_produk_d);
            $('#vol_produk_d').val(vol_produk_d);
            getTotal();
        }

        function getTotal()
        {
            var sub_total = $('#sub-total-val').val();

            sub_total = parseFloat($('#vol_produk_a').val()) + parseFloat($('#vol_produk_b').val()) + parseFloat($('#vol_produk_c').val()) + parseFloat($('#vol_produk_d').val());
            
            $('#sub-total-val').val(sub_total);
            $('#sub-total').text($.number( sub_total, 2,',','.' ));

            total_total = parseFloat(sub_total);
            $('#total-val').val(total_total);
            $('#total').text($.number( total_total, 2,',','.' ));
        }

        $(document).ready(function(){
            $('#komposisi_125').val(<?= $rak['komposisi_125'];?>).trigger('change');
            $('#komposisi_225').val(<?= $rak['komposisi_225'];?>).trigger('change');
            $('#komposisi_250').val(<?= $rak['komposisi_250'];?>).trigger('change');
            $('#komposisi_250_2').val(<?= $rak['komposisi_250_2'];?>).trigger('change');

            $('#penawaran_id_semen').val(<?= $rak['penawaran_id_semen'];?>).trigger('change');
            $('#penawaran_id_pasir').val(<?= $rak['penawaran_id_pasir'];?>).trigger('change');
            $('#penawaran_id_batu1020').val(<?= $rak['penawaran_id_batu1020'];?>).trigger('change');
            $('#penawaran_id_batu2030').val(<?= $rak['penawaran_id_batu2030'];?>).trigger('change');
            $('#penawaran_id_solar').val(<?= $rak['penawaran_id_solar'];?>).trigger('change');
        });

        $('#penawaran_id_semen').change(function(){
            var penawaran_id = $(this).find(':selected').data('penawaran_id');
            $('#penawaran_id_semen').val(penawaran_id);
			var price = $(this).find(':selected').data('price');
            $('#price_semen').val(price);
            var supplier_id = $(this).find(':selected').data('supplier_id');
            $('#supplier_id_semen').val(supplier_id);
            var measure = $(this).find(':selected').data('measure');
            $('#measure_semen').val(measure);
            var tax_id = $(this).find(':selected').data('tax_id');
            $('#tax_id_semen').val(tax_id);
        });

        $('#penawaran_id_pasir').change(function(){
            var penawaran_id = $(this).find(':selected').data('penawaran_id');
            $('#penawaran_id_pasir').val(penawaran_id);
			var price = $(this).find(':selected').data('price');
            $('#price_pasir').val(price);
            var supplier_id = $(this).find(':selected').data('supplier_id');
            $('#supplier_id_pasir').val(supplier_id);
            var measure = $(this).find(':selected').data('measure');
            $('#measure_pasir').val(measure);
            var tax_id = $(this).find(':selected').data('tax_id');
            $('#tax_id_pasir').val(tax_id);
        });

        $('#penawaran_id_batu1020').change(function(){
            var penawaran_id = $(this).find(':selected').data('penawaran_id');
            $('#penawaran_id_batu1020').val(penawaran_id);
			var price = $(this).find(':selected').data('price');
            $('#price_batu1020').val(price);
            var supplier_id = $(this).find(':selected').data('supplier_id');
            $('#supplier_id_batu1020').val(supplier_id);
            var measure = $(this).find(':selected').data('measure');
            $('#measure_batu1020').val(measure);
            var tax_id = $(this).find(':selected').data('tax_id');
            $('#tax_id_batu1020').val(tax_id);
        });

        $('#penawaran_id_batu2030').change(function(){
            var penawaran_id = $(this).find(':selected').data('penawaran_id');
            $('#penawaran_id_batu2030').val(penawaran_id);
			var price = $(this).find(':selected').data('price');
            $('#price_batu2030').val(price);
            var supplier_id = $(this).find(':selected').data('supplier_id');
            $('#supplier_id_batu2030').val(supplier_id);
            var measure = $(this).find(':selected').data('measure');
            $('#measure_batu2030').val(measure);
            var tax_id = $(this).find(':selected').data('tax_id');
            $('#tax_id_batu2030').val(tax_id);
        });

        $('#penawaran_id_solar').change(function(){
            var penawaran_id = $(this).find(':selected').data('penawaran_id');
            $('#penawaran_id_solar').val(penawaran_id);
			var price = $(this).find(':selected').data('price');
            $('#price_solar').val(price);
            var supplier_id = $(this).find(':selected').data('supplier_id');
            $('#supplier_id_solar').val(supplier_id);
            var measure = $(this).find(':selected').data('measure');
            $('#measure_solar').val(measure);
            var tax_id = $(this).find(':selected').data('tax_id');
            $('#tax_id_solar').val(tax_id);
        });

    </script>


</body>
</html>