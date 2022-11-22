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
                                <form method="POST" action="<?php echo site_url('rak/submit_rencana_kerja_biaya');?>" id="form-po" enctype="multipart/form-data" autocomplete="off">
                                    <div class="row">
										<div class="col-sm-3">
                                            <label>Tanggal</label>
                                            <input type="text" class="form-control dtpicker" name="tanggal_rencana_kerja" required="" value="" />
                                        </div>
									</div>
                                    <br />
                                    <div class="table-responsive">
                                        <table id="table-product" class="table table-bordered table-striped table-condensed table-center">
                                            <thead>
                                                <tr class="text-center">
                                                    <th width="40%">URAIAN</th>
                                                    <th width="30%">NILAI</th>
                                                    <th width="30%"></th>                                  
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Biaya Overhead</td>
													<td>
                                                    <input type="text" id="biaya_overhead" name="biaya_overhead" class="form-control rupiahformat text-right" value="" required="" autocomplete="off">
                                                    </td>
                                                    <td class="text-center"></td>
                                                </tr>
                                                <tr>
                                                    <td>Biaya Bank</td>
													<td>
                                                    <input type="text" id="biaya_bank" name="biaya_bank" class="form-control rupiahformat text-right" value="" required="" autocomplete="off">
                                                    </td>
                                                    <td class="text-center"></td>
                                                </tr>
                                                <tr>
                                                    <td>Persiapan</td>
													<td>
                                                    <input type="text" id="biaya_persiapan" name="biaya_persiapan" class="form-control rupiahformat text-right" value="" required="" autocomplete="off">
                                                    </td>
                                                    <td class="text-center"></td>
                                                </tr>
                                                <tr>
                                                    <td>Termin</td>
													<td>
                                                    <input type="text" id="termin" name="termin" class="form-control rupiahformat text-right" value="" required="" autocomplete="off">
                                                    </td>
                                                    <td class="text-center"></td>
                                                </tr>		
                                            </tbody>
                                
                                        </table>    
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Lampiran</label>
                                                <input type="file" class="form-control" name="files[]"  multiple="" />
                                            </div>
                                        </div>
                                    </div>
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