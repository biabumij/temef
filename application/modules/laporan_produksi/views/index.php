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
														<p><h5>Laporan Pemakaian Komposisi Bahan Baku</h5></p>
														<p>Menampilkan laporan pemakaian komposisi bahan baku yang dicatat dalam suatu periode.</p>
                                                        <a href="#laporan_pemakaian_komposisi" aria-controls="laporan_pemakaian_komposisi" role="tab" data-toggle="tab" class="btn btn-primary">Lihat Laporan</a>										
                                                    </div>
													<div class="col-sm-5">
														<p><h5>Pergerakan Bahan Baku</h5></p>
														<p>Menampilkan pergerakan bahan baku yang dicatat dalam suatu periode.</p>
                                                        <a href="#pergerakan_bahan_baku" aria-controls="pergerakan_bahan_baku" role="tab" data-toggle="tab" class="btn btn-primary">Lihat Laporan</a>
													</div>
													<div class="col-sm-5">
														<p><h5>Pergerakan Bahan Baku (Solar)</h5></p>
														<p>Menampilkan pergerakan bahan baku solar yang dicatat dalam suatu periode.</p>
                                                        <a href="#pergerakan_bahan_baku_solar" aria-controls="pergerakan_bahan_baku_solar" role="tab" data-toggle="tab" class="btn btn-primary">Lihat Laporan</a>
													</div>
													<div class="col-sm-5">
														<p><h5>Nilai Persediaan Barang</h5></p>
														<p>Menampilkan nilai persediaan barang yang dicatat dalam suatu periode.</p>
                                                        <a href="#nilai_persediaan_barang" aria-controls="nilai_persediaan_barang" role="tab" data-toggle="tab" class="btn btn-primary">Lihat Laporan</a>
													</div>
													<div class="col-sm-5">
														<p><h5>Laporan Pemakaian Bahan Baku</h5></p>
														<p>Menampilkan laporan pemakaian bahan baku yang dicatat dalam suatu periode.</p>
                                                        <a href="#laporan_pemakaian_bahan_baku" aria-controls="laporan_pemakaian_bahan_baku" role="tab" data-toggle="tab" class="btn btn-primary">Lihat Laporan</a>										
                                                    </div>
													<div class="col-sm-5">
														<p><h5>Laporan Evaluasi Pemakaian Bahan Baku</h5></p>
														<p>Menampilkan laporan evaluasi pemakaian bahan baku yang dicatat dalam suatu periode.</p>
                                                        <a href="#laporan_evaluasi" aria-controls="laporan_evaluasi" role="tab" data-toggle="tab" class="btn btn-primary">Lihat Laporan</a>										
                                                    </div>                                 													
                                                </div>
                                            </div>
                                        </div>
                                    </div>

									<!-- Laporan Pemakaian Komposisi -->
                                    
									<div role="tabpanel" class="tab-pane" id="laporan_pemakaian_komposisi">
                                        <div class="col-sm-15">
											<div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title">Laporan Pemakaian Komposisi</h3>
													<a href="laporan_produksi">Kembali</a>
                                                </div>
												<div style="margin: 20px">
													<div class="row">
														<form action="<?php echo site_url('laporan/laporan_pemakaian_komposisi_print');?>" target="_blank">
															<div class="col-sm-3">
																<input type="text" id="filter_date_komposisi" name="filter_date" class="form-control dtpicker"  autocomplete="off" placeholder="Filter By Date">
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
													<div class="table-responsive" id="box-ajax-komposisi">													
													
                    
													</div>
												</div>
											</div>
										</div>
									</div>
									
									<!-- Pergerakan Bahan Baku -->
									
                                    <div role="tabpanel" class="tab-pane" id="pergerakan_bahan_baku">
                                        <div class="col-sm-15">
										<div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title">Pergerakan Bahan Baku</h3>
													<a href="laporan_produksi">Kembali</a>
                                                </div>
												<div style="margin: 20px">
													<div class="row">
														<form action="<?php echo site_url('laporan/pergerakan_bahan_baku_print');?>" target="_blank">
															<div class="col-sm-3">
																<input type="text" id="filter_date_bahan_baku" name="filter_date" class="form-control dtpicker"  autocomplete="off" placeholder="Filter By Date">
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
													<div class="table-responsive" id="box-ajax-5">													
													
                    
													</div>
												</div>
										</div>
										
										</div>
                                    </div>

									<!-- Pergerakan Bahan Baku Solar -->
									
                                    <div role="tabpanel" class="tab-pane" id="pergerakan_bahan_baku_solar">
                                        <div class="col-sm-15">
										<div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title">Pergerakan Bahan Baku (Solar)</h3>
													<a href="laporan_produksi">Kembali</a>
                                                </div>
												<div style="margin: 20px">
													<div class="row">
														<form action="<?php echo site_url('laporan/pergerakan_bahan_baku_solar_print');?>" target="_blank">
															<div class="col-sm-3">
																<input type="text" id="filter_date_bahan_baku_solar" name="filter_date" class="form-control dtpicker"  autocomplete="off" placeholder="Filter By Date">
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
													<div class="table-responsive" id="box-ajax-solar">													
													
                    
													</div>
												</div>
										</div>
										
										</div>
                                    </div>

									<!-- Nilai Persediaan Barang -->
									
                                    <div role="tabpanel" class="tab-pane" id="nilai_persediaan_barang">
                                        <div class="col-sm-15">
										<div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title">Nilai Persediaan Barang</h3>
													<a href="laporan_produksi">Kembali</a>
                                                </div>
												<div style="margin: 20px">
													<div class="row">
														<form action="<?php echo site_url('laporan/nilai_persediaan_barang_print');?>" target="_blank">
															<div class="col-sm-3">
																<input type="text" id="filter_date_nilai" name="filter_date" class="form-control dtpicker"  autocomplete="off" placeholder="Filter By Date">
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
													<div class="table-responsive" id="box-ajax-3">													
													
                    
													</div>
												</div>
										</div>
										
										</div>
                                    </div>

                                    <!-- Laporan Pemakaian Bahan Baku -->
                                    
									<div role="tabpanel" class="tab-pane" id="laporan_pemakaian_bahan_baku">
                                        <div class="col-sm-15">
										<div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title">Laporan Pemakaian Bahan Baku</h3>
													<a href="laporan_produksi">Kembali</a>
                                                </div>
												<div style="margin: 20px">
													<div class="row">
														<form action="<?php echo site_url('laporan/laporan_pemakaian_bahan_print');?>" target="_blank">
															<div class="col-sm-3">
																<input type="text" id="filter_date_pemakaian_bahan" name="filter_date" class="form-control dtpicker"  autocomplete="off" placeholder="Filter By Date">
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
													<div class="table-responsive" id="box-ajax-pemakaian">													
													
                    
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
                                                    <h3 class="panel-title">Laporan Evaluasi Pemakaian Bahan Baku</h3>
													<a href="laporan_produksi">Kembali</a>
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

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="#" class="scroll-to-top"><i class="fa fa-angle-double-up"></i></a>
            </div>
        </div>

        <?php echo $this->Templates->Footer(); ?>

        <script src="<?php echo base_url(); ?>assets/back/theme/vendor/daterangepicker/moment.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/back/theme/vendor/daterangepicker/daterangepicker.js"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/back/theme/vendor/daterangepicker/daterangepicker.css">
        <script src="<?php echo base_url(); ?>assets/back/theme/vendor/bootbox.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/back/theme/vendor/jquery.number.min.js"></script>
        <script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>

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
			
			<!-- Script Pergerakan Bahan Baku -->

			<script type="text/javascript">
			$('#filter_date_bahan_baku').daterangepicker({
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

			$('#filter_date_bahan_baku').on('apply.daterangepicker', function(ev, picker) {
				  $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
				  TablePergerakanBahanBaku();
			});


			function TablePergerakanBahanBaku()
			{
				$('#wait').fadeIn('fast');   
				$.ajax({
					type    : "POST",
					url     : "<?php echo site_url('pmm/reports/pergerakan_bahan_baku'); ?>/"+Math.random(),
					dataType : 'html',
					data: {
						filter_date : $('#filter_date_bahan_baku').val(),
					},
					success : function(result){
						$('#box-ajax-5').html(result);
						$('#wait').fadeOut('fast');
					}
				});
			}

			//TablePergerakanBahanBaku();

            </script>

			<!-- Script Pergerakan Bahan Baku Solar -->

			<script type="text/javascript">
			$('#filter_date_bahan_baku_solar').daterangepicker({
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

			$('#filter_date_bahan_baku_solar').on('apply.daterangepicker', function(ev, picker) {
				  $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
				  TablePergerakanBahanBakuSolar();
			});


			function TablePergerakanBahanBakuSolar()
			{
				$('#wait').fadeIn('fast');   
				$.ajax({
					type    : "POST",
					url     : "<?php echo site_url('pmm/reports/pergerakan_bahan_baku_solar'); ?>/"+Math.random(),
					dataType : 'html',
					data: {
						filter_date : $('#filter_date_bahan_baku_solar').val(),
					},
					success : function(result){
						$('#box-ajax-solar').html(result);
						$('#wait').fadeOut('fast');
					}
				});
			}

			//TablePergerakanBahanBakuSolar();

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

			<!-- Script Evaluasi -->

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

</body>

</html>