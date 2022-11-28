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
                                                <th width="35%">URAIAN</th>
                                                <th width="30%">VOLUME</th>
                                                <th width="30%">SATUAN</th>   
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center">1.</td>
                                                <td class="text-left">Beton K 125 (10±2)</td>
                                                <td class="text-right"><input type="text" id="vol_produk_a" name="vol_produk_a" class="form-control numberformat text-right" value="<?php echo number_format($rak["vol_produk_a"],2,',','.');?>" onchange="changeData(1)" required="" autocomplete="off"></td>
                                                <td class="text-center">M3</td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">2.</td>
                                                <td class="text-left">Beton K 225 (10±2)</td>
                                                <td class="text-right"><input type="text" id="vol_produk_b" name="vol_produk_b" class="form-control numberformat text-right" value="<?php echo number_format($rak["vol_produk_b"],2,',','.');?>" onchange="changeData(1)" required="" autocomplete="off"></td>
                                                <td class="text-center">M3</td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">3.</td>
                                                <td class="text-left">Beton K 250 (10±2)</td>
                                                <td class="text-right"><input type="text" id="vol_produk_c" name="vol_produk_c" class="form-control numberformat text-right" value="<?php echo number_format($rak["vol_produk_c"],2,',','.');?>" onchange="changeData(1)" required="" autocomplete="off"></td>
                                                <td class="text-center">M3</td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">4.</td>
                                                <td class="text-left">Beton K 250 (18±2)</td>
                                                <td class="text-right"><input type="text" id="vol_produk_d" name="vol_produk_d" class="form-control numberformat text-right" value="<?php echo number_format($rak["vol_produk_d"],2,',','.');?>" onchange="changeData(1)"required="" autocomplete="off"></td>
                                                <td class="text-center">M3</td>
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

    </script>


</body>
</html>
