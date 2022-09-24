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
                            
                            <li><a>RAP Alat</a></li>
                        </ul>
                    </div>
                </div>
                <div class="row animated fadeInUp">
                    <div class="col-sm-12 col-lg-12">
                        <div class="panel">
                            <div class="panel-header"> 
                                <div class="">
                                    <h3 class="">RAP ALAT</h3>
                                    
                                </div>
                            </div>
                            <div class="panel-content">
                                <form method="POST" action="<?php echo site_url('rap/submit_rap_alat');?>" id="form-po" enctype="multipart/form-data" autocomplete="off">
                                    <div class="row">
										<div class="col-sm-3">
                                            <label>Tanggal</label>
                                            <input type="text" class="form-control dtpicker" name="tanggal_rap_alat" required="" value="" />
                                        </div>
										<div class="col-sm-6">
                                            <label>Nomor RAP</label>
                                            <input type="text" class="form-control" name="nomor_rap_alat" required="" value="<?= $this->pmm_model->GetNoRapAlat();?>">
                                        </div> 
									</div>
                                    <br />
                                    <div class="table-responsive">
                                        <table id="table-product" class="table table-bordered table-striped table-condensed table-center">
                                            <thead>
                                                <tr class="text-center">
                                                    <th width="5%">NO.</th>
                                                    <th width="30%">URAIAN</th>
													<th width="65%">TOTAL</th>                                    
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center">1.</td>
                                                    <td>BATCHING PLANT</td>
													<td>
                                                    <input type="text" name="batching_plant" id="batching_plant" onkeyup="sum();" class="form-control input-sm text-right numberformat" required="" />
                                                    </td>
                                                </tr>	
                                                <tr>
                                                    <td class="text-center">2.</td>
                                                    <td>TRUCK MIXER</td>
													<td>
                                                    <input type="text" name="truck_mixer" id="truck_mixer" onkeyup="sum();" class="form-control input-sm text-right numberformat" required="" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">3.</td>
                                                    <td>WHEEL LOADER</td>
													<td>
                                                    <input type="text" name="wheel_loader" id="wheel_loader" onkeyup="sum();" class="form-control input-sm text-right numberformat" required="" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">4.</td>
                                                    <td>BBM SOLAR</td>
													<td>
                                                    <input type="text" name="bbm_solar" id="bbm_solar" onkeyup="sum();" class="form-control input-sm text-right numberformat" required="" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td></td> 
                                                    <td>TOTAL</td>   
                                                    <td>
                                                        <input type="text" id="total_all" name="total_all" class="form-control input-sm text-right numberformat" value="" readonly="">
                                                    </td>
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
                                            <a href="<?= site_url('admin/rap');?>" class="btn btn-danger" style="margin-bottom:0;"><i class="fa fa-close"></i> Batal</a>
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

        $('input.numberformat').number( true, 0,',','.' );

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
		
		function sum() {
    
		var txtFirstNumberValue = document.getElementById('batching_plant').value;
		var txtSecondNumberValue = document.getElementById('truck_mixer').value;
		var txtThirdNumberValue = document.getElementById('wheel_loader').value;
		var txtFourthNumberValue = document.getElementById('bbm_solar').value;

        txtFirstNumberValue = txtFirstNumberValue.replace(/[^0-9]/g, '');
        txtSecondNumberValue = txtSecondNumberValue.replace(/[^0-9]/g, '');
        txtThirdNumberValue = txtThirdNumberValue.replace(/[^0-9]/g, '');
        txtFourthNumberValue = txtFourthNumberValue.replace(/[^0-9]/g, '');
        

		var result = parseInt(txtFourthNumberValue) + parseInt(txtThirdNumberValue) + parseInt(txtSecondNumberValue) + parseInt(txtFirstNumberValue);
		if (!isNaN(result)) {

		 document.getElementById('total_all').value = formatter.format(result);
			}
		}

        window.formatter = new Intl.NumberFormat('id-ID', {
        style: 'decimal',
        currency: 'IDR',
        symbol: 'none',
        minimumFractionDigits : '0'
    });

        
		
		
    </script>


</body>
</html>
