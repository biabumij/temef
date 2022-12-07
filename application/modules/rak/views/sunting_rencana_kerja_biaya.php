<!doctype html>
<html lang="en" class="fixed">
<head>
    <?php echo $this->Templates->Header();?>

    <style type="text/css">
        .form-approval {
            display: inline-block;
        }
		
		.mytable thead th {
		  /*background-color: #D3D3D3;
		  border: solid 1px #000000;*/
		  color: #000000;
		  text-align: center;
		  vertical-align: middle;
		  padding : 10px;
		}
		
		.mytable tbody td {
		  padding: 5px;
		}
		
		.mytable tfoot th {
		  padding: 5px;
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
                                <form method="POST" action="<?php echo site_url('rak/submit_sunting_rencana_biaya');?>" id="form-po" enctype="multipart/form-data" autocomplete="off">
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
                                    <table class="mytable table-bordered table-hover table-striped" width="100%">
                                        <thead>
                                            <tr>
                                            <th class="text-center" width="50%">URAIAN</th>
                                            <th class="text-center" width="50%">NILAI</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            </tr>
                                                <td colspan="3" class="text-center" style="background-color:#EEEEEE;"><b>PRODUKSI</b></td>
                                            <tr>
                                            <tr>
                                                <td class="text-left">Biaya Bahan</td>
                                                <td class="text-right"><input type="text" id="biaya_bahan" name="biaya_bahan" class="form-control rupiahformat text-right" value="<?= $rak['biaya_bahan'] ?>" required="" autocomplete="off"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-left">Biaya Alat</td>
                                                <td class="text-right"><input type="text" id="biaya_alat" name="biaya_alat" class="form-control rupiahformat text-right" value="<?= $rak['biaya_alat'] ?>" required="" autocomplete="off"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-left">Biaya Overhead</td>
                                                <td class="text-right"><input type="text" id="biaya_overhead" name="biaya_overhead" class="form-control rupiahformat text-right" value="<?= $rak['biaya_overhead'] ?>" required="" autocomplete="off"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-left">Biaya Bank</td>
                                                <td class="text-right"><input type="text" id="biaya_bank" name="biaya_bank" class="form-control rupiahformat text-right" value="<?= $rak['biaya_bank'] ?>" required="" autocomplete="off"></td>
                                            </tr>
                                            <!--<tr>
                                                <td class="text-left">Termin</td>
                                                <td class="text-right"><input type="text" id="termin" name="termin" class="form-control rupiahformat text-right" value="<?= $rak['termin'] ?>" required="" autocomplete="off"></td>
                                            </tr>-->
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

    </script>


</body>
</html>
