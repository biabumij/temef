<!doctype html>
<html lang="en" class="fixed">

<head>
    <?php echo $this->Templates->Header(); ?>
	<style type="text/css">
		.mytable thead th {
		  background-color:	#e69500;
		  color: #000000;
		  text-align: center;
		  vertical-align: middle;
		  padding: 5px;
		}
		
		.mytable tbody td {
		  padding: 5px;
          background-color: #F0F0F0;
		}
		
		.mytable tfoot td {
		  background-color:	#E8E8E8;
		  color: #000000;
		  padding: 5px;
		}
    </style>
</head>

<body>
    <div class="wrap">

        <?php echo $this->Templates->PageHeader(); ?>

        <div class="page-body">
            <?php echo $this->Templates->LeftBar(); ?>
            <div class="content">
                <div class="content-header">
                    <div class="leftside-content-header">
                        <ul class="breadcrumbs">
                            <li><i class="fa fa-bar-chart" aria-hidden="true"></i>Laporan</li>
                            <li><a><?php echo $row[0]->menu_name; ?></a></li>
                        </ul>
                    </div>
                </div>
                <div class="row animated fadeInUp">
                    <div class="col-sm-12 col-lg-12">
                        <div class="panel">
                            <div class="panel-content">
								<div class="panel-header">
									<h3 class="section-subtitle"><?php echo $row[0]->menu_name; ?></h3>
								</div>
                                <div class="tab-content">
									
									<!-- Laporan Laba Rugi -->
                                    <div role="tabpanel" class="tab-pane active" id="laba_rugi">
                                        <br />
                                        <div class="row">
                                            <div width="100%">
                                                <div class="panel panel-default">                                            
													<div class="col-sm-5">
														<p><h5>Evaluasi Bahan</h5></p>
                                                        <a href="#laporan_evaluasi" aria-controls="laporan_evaluasi" role="tab" data-toggle="tab" class="btn btn-primary">Lihat Laporan</a>										
                                                    </div>
													<div class="col-sm-5">
														<p><h5>Evaluasi Alat</h5></p>
                                                        <a href="#evaluasi_alat" aria-controls="evaluasi_alat" role="tab" data-toggle="tab" class="btn btn-primary">Lihat Laporan</a>										
                                                    </div>
													<div class="col-sm-5">
														<p><h5>Evaluasi BUA, Diskonto, Persiapan (Coming Soon)</h5></p>
                                                        <a href="#evaluasi_alat" aria-controls="evaluasi_alat" role="tab" data-toggle="tab" class="btn btn-primary">Lihat Laporan</a>										
                                                    </div>
													<div class="col-sm-5">
														<p><h5>Evaluasi Target Produksi (Coming Soon)</h5></p>
                                                        <a href="#evaluasi_target_produksi" aria-controls="evaluasi_target_produksi" role="tab" data-toggle="tab" class="btn btn-primary">Lihat Laporan</a>										
                                                    </div>
													<div class="col-sm-5">
														<p><h5>Evaluasi Laba Rugi (Coming Soon)</h5></p>
                                                        <a href="#evaluasi_laba_rugi" aria-controls="evaluasi_laba_rugi" role="tab" data-toggle="tab" class="btn btn-primary">Lihat Laporan</a>										
                                                    </div>                                 													
                                                </div>
                                            </div>
                                        </div>
                                    </div>

									<!-- Laporan Evaluasi -->
                                    
									<div role="tabpanel" class="tab-pane" id="laporan_evaluasi">
                                        <div class="col-sm-15">
										<div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title">Evaluasi Bahan</h3>
													<a href="laporan_ev._produksi">Kembali</a>
                                                </div>
												<div style="margin: 20px">
													<div class="row">
														<form action="<?php echo site_url('laporan/laporan_evaluasi_print');?>" target="_blank">
															<div class="col-sm-3">
																<input type="text" id="filter_date_evaluasi" name="filter_date" class="form-control dtpicker"  autocomplete="off" placeholder="Filter By Date">
															</div>
															<div class="col-sm-3">
																<button type="submit" class="btn btn-info"><i class="fa fa-print"></i>  Print</button>
															</div>
														</form>
														
													</div>
													<br />
													<div id="wait" style=" text-align: center; align-content: center; display: none;">	
														<div>Please Wait</div>
														<div class="fa-3x">
														  <i class="fa fa-spinner fa-spin"></i>
														</div>
													</div>				
													<div class="table-responsive" id="box-ajax-evaluasi">													
													
                    
													</div>
												</div>
										</div>
										
										</div>
                                    </div>

									<div role="tabpanel" class="tab-pane" id="evaluasi_alat">
                                        <div class="col-sm-15">
										<div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title">Evaluasi Alat</h3>
													<a href="laporan_ev._produksi">Kembali</a>
                                                </div>
												<div style="margin: 20px">
													<div class="row">
														<form action="<?php echo site_url('laporan/cetak_laporan_evaluasi_alat');?>" target="_blank">
															<div class="col-sm-3">
																<input type="text" id="filter_date_evaluasi_alat" name="filter_date" class="form-control dtpicker"  autocomplete="off" placeholder="Filter By Date">
															</div>
															<div class="col-sm-3">
																<button type="submit" class="btn btn-info"><i class="fa fa-print"></i>  Print</button>
															</div>
														</form>
														
													</div>
													<br />
													<div id="wait" style=" text-align: center; align-content: center; display: none;">	
														<div>Please Wait</div>
														<div class="fa-3x">
														  <i class="fa fa-spinner fa-spin"></i>
														</div>
													</div>				
													<div class="table-responsive" id="evaluasi-alat">													
													
                    
													</div>
												</div>
										</div>
										
										</div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>

        <?php echo $this->Templates->Footer(); ?>

        <script src="<?php echo base_url(); ?>assets/back/theme/vendor/daterangepicker/moment.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/back/theme/vendor/daterangepicker/daterangepicker.js"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/back/theme/vendor/daterangepicker/daterangepicker.css">
        <script src="<?php echo base_url(); ?>assets/back/theme/vendor/bootbox.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/back/theme/vendor/jquery.number.min.js"></script>
        <script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>

		<!-- Script Evaluasi Bahan -->

		<script type="text/javascript">
			$('#filter_date_evaluasi').daterangepicker({
            autoUpdateInput : false,
			showDropdowns: true,
            locale: {
              format: 'DD-MM-YYYY'
            },
            ranges: {
               'Today': [moment(), moment()],
               'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               'Last 7 Days': [moment().subtract(6, 'days'), moment()],
               'Last 30 Days': [moment().subtract(30, 'days'), moment()],
               'This Month': [moment().startOf('month'), moment().endOf('month')],
               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
			});

			$('#filter_date_evaluasi').on('apply.daterangepicker', function(ev, picker) {
				  $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
				  TableEvaluasi();
			});


			function TableEvaluasi()
			{
				$('#wait').fadeIn('fast');   
				$.ajax({
					type    : "POST",
					url     : "<?php echo site_url('pmm/productions/table_evaluasi'); ?>/"+Math.random(),
					dataType : 'html',
					data: {
						filter_date : $('#filter_date_evaluasi').val(),
					},
					success : function(result){
						$('#box-ajax-evaluasi').html(result);
						$('#wait').fadeOut('fast');
					}
				});
			}

			//TableEvaluasi();

            </script>
		
		<!-- Script Evaluasi Alat -->

		<script type="text/javascript">
			$('#filter_date_evaluasi_alat').daterangepicker({
            autoUpdateInput : false,
			showDropdowns: true,
            locale: {
              format: 'DD-MM-YYYY'
            },
            ranges: {
               'Today': [moment(), moment()],
               'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               'Last 7 Days': [moment().subtract(6, 'days'), moment()],
               'Last 30 Days': [moment().subtract(30, 'days'), moment()],
               'This Month': [moment().startOf('month'), moment().endOf('month')],
               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
			});

			$('#filter_date_evaluasi_alat').on('apply.daterangepicker', function(ev, picker) {
				  $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
				  TableEvaluasiAlat();
			});


			function TableEvaluasiAlat()
			{
				$('#wait').fadeIn('fast');   
				$.ajax({
					type    : "POST",
					url     : "<?php echo site_url('pmm/reports/table_evaluasi_alat'); ?>/"+Math.random(),
					dataType : 'html',
					data: {
						filter_date : $('#filter_date_evaluasi_alat').val(),
					},
					success : function(result){
						$('#evaluasi-alat').html(result);
						$('#wait').fadeOut('fast');
					}
				});
			}

			//TableEvaluasiAlat();

            </script>

		<!-- Script Pemakaian Komposisi -->

		<script type="text/javascript">
			$('#filter_date_komposisi').daterangepicker({
            autoUpdateInput : false,
			showDropdowns: true,
            locale: {
              format: 'DD-MM-YYYY'
            },
            ranges: {
               'Today': [moment(), moment()],
               'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               'Last 7 Days': [moment().subtract(6, 'days'), moment()],
               'Last 30 Days': [moment().subtract(30, 'days'), moment()],
               'This Month': [moment().startOf('month'), moment().endOf('month')],
               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
			});

			$('#filter_date_komposisi').on('apply.daterangepicker', function(ev, picker) {
				  $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
				  TableKomposisi();
			});


			function TableKomposisi()
			{
				$('#wait').fadeIn('fast');   
				$.ajax({
					type    : "POST",
					url     : "<?php echo site_url('pmm/productions/table_komposisi'); ?>/"+Math.random(),
					dataType : 'html',
					data: {
						filter_date : $('#filter_date_komposisi').val(),
					},
					success : function(result){
						$('#box-ajax-komposisi').html(result);
						$('#wait').fadeOut('fast');
					}
				});
			}

			//TableKomposisi();

            </script>

			<!-- Script Nilai Persediaan Barang -->

			<script type="text/javascript">
			$('#filter_date_nilai').daterangepicker({
            autoUpdateInput : false,
			showDropdowns: true,
            locale: {
              format: 'DD-MM-YYYY'
            },
            ranges: {
               'Today': [moment(), moment()],
               'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               'Last 7 Days': [moment().subtract(6, 'days'), moment()],
               'Last 30 Days': [moment().subtract(30, 'days'), moment()],
               'This Month': [moment().startOf('month'), moment().endOf('month')],
               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
					}
				});

				$('#filter_date_nilai').on('apply.daterangepicker', function(ev, picker) {
					  $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
					  TableNilaiPersediaanBarang();
				});


				function TableNilaiPersediaanBarang()
				{
					$('#wait').fadeIn('fast');   
					$.ajax({
						type    : "POST",
						url     : "<?php echo site_url('pmm/reports/nilai_persediaan_barang'); ?>/"+Math.random(),
						dataType : 'html',
						data: {
							filter_date : $('#filter_date_nilai').val(),
						},
						success : function(result){
							$('#box-ajax-3').html(result);
							$('#wait').fadeOut('fast');
						}
					});
				}

				//TableNilaiPersediaanBarang();
			
            </script>

			<!-- Script Pemakaian Bahan Baku -->

			<script type="text/javascript">
			$('#filter_date_pemakaian_bahan').daterangepicker({
            autoUpdateInput : false,
			showDropdowns: true,
            locale: {
              format: 'DD-MM-YYYY'
            },
            ranges: {
               'Today': [moment(), moment()],
               'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               'Last 7 Days': [moment().subtract(6, 'days'), moment()],
               'Last 30 Days': [moment().subtract(30, 'days'), moment()],
               'This Month': [moment().startOf('month'), moment().endOf('month')],
               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
			});

			$('#filter_date_pemakaian_bahan').on('apply.daterangepicker', function(ev, picker) {
				  $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
				  TablePemakaianBahan();
			});


			function TablePemakaianBahan()
			{
				$('#wait').fadeIn('fast');   
				$.ajax({
					type    : "POST",
					url     : "<?php echo site_url('pmm/productions/table_pemakaian_bahan'); ?>/"+Math.random(),
					dataType : 'html',
					data: {
						filter_date : $('#filter_date_pemakaian_bahan').val(),
					},
					success : function(result){
						$('#box-ajax-pemakaian').html(result);
						$('#wait').fadeOut('fast');
					}
				});
			}

			//TablePemakaianBahan();

            </script>

			<!-- Script Evaluasi RAP -->

			<script type="text/javascript">
			$('#filter_date_evaluasi_rap').daterangepicker({
            autoUpdateInput : false,
			showDropdowns: true,
            locale: {
              format: 'DD-MM-YYYY'
            },
            ranges: {
               'Today': [moment(), moment()],
               'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               'Last 7 Days': [moment().subtract(6, 'days'), moment()],
               'Last 30 Days': [moment().subtract(30, 'days'), moment()],
               'This Month': [moment().startOf('month'), moment().endOf('month')],
               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
			});

			$('#filter_date_evaluasi_rap').on('apply.daterangepicker', function(ev, picker) {
				  $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
				  TableEvaluasiRAP();
			});


			function TableEvaluasiRAP()
			{
				$('#wait').fadeIn('fast');   
				$.ajax({
					type    : "POST",
					url     : "<?php echo site_url('pmm/reports/table_evaluasi_rap'); ?>/"+Math.random(),
					dataType : 'html',
					data: {
						filter_date : $('#filter_date_evaluasi_rap').val(),
					},
					success : function(result){
						$('#box-ajax-evaluasi-rap').html(result);
						$('#wait').fadeOut('fast');
					}
				});
			}

			//TableEvaluasiRAP();

            </script>

			<!-- Script Beban Pokok Produksi -->

			<script type="text/javascript">
			$('#filter_date_bpp').daterangepicker({
            autoUpdateInput : false,
			showDropdowns: true,
            locale: {
              format: 'DD-MM-YYYY'
            },
            ranges: {
               'Today': [moment(), moment()],
               'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               'Last 7 Days': [moment().subtract(6, 'days'), moment()],
               'Last 30 Days': [moment().subtract(30, 'days'), moment()],
               'This Month': [moment().startOf('month'), moment().endOf('month')],
               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
			});

			$('#filter_date_bpp').on('apply.daterangepicker', function(ev, picker) {
				  $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
				  TableBPP();
			});


			function TableBPP()
			{
				$('#wait').fadeIn('fast');   
				$.ajax({
					type    : "POST",
					url     : "<?php echo site_url('pmm/reports/beban_pokok_produksi'); ?>/"+Math.random(),
					dataType : 'html',
					data: {
						filter_date : $('#filter_date_bpp').val(),
					},
					success : function(result){
						$('#beban-pokok-produksi').html(result);
						$('#wait').fadeOut('fast');
					}
				});
			}

			//TableBPP();

            </script>

</body>

</html>