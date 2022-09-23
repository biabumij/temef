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
		}
		
		.mytable tfoot td {
		  background-color:	#e69500;
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
                                    <div role="tabpanel" class="tab-pane active" id="pembelian">
                                        <br />
                                        <div class="row">
                                            <div width="100%">
                                                <div class="panel panel-default">                                            
													<div class="col-sm-5">
														<p><h5>Daftar Tagihan</h5></p>
														<p>Menampilkan jumlah nilai tagihan pada setiap rekanan yang dicatat dalam suatu periode.</p>
                                                        <a href="#laporan_daftar_tagihan" aria-controls="laporan_daftar_tagihan" role="tab" data-toggle="tab" class="btn btn-primary">Lihat Laporan</a>
                                                    </div>
													<div class="col-sm-5">
														<p><h5>Hutang</h5></p>
														<p>Menampilkan jumlah nilai hutang pada setiap rekanan yang dicatat dalam suatu periode.</p>
                                                        <a href="#laporan_hutang" aria-controls="laporan_hutang" role="tab" data-toggle="tab" class="btn btn-primary">Lihat Laporan</a>
                                                    </div>
													<div class="col-sm-5">
														<p><h5>Umur Hutang</h5></p>
														<p>Menampilkan umur hutang pada setiap rekanan  yang dicatat dalam suatu periode.</p>
                                                        <a href="#laporan_umur_hutang" aria-controls="laporan_umur_hutang" role="tab" data-toggle="tab" class="btn btn-primary">Lihat Laporan</a>
                                                    </div>
													<div class="col-sm-5">
														<p><h5>Daftar Pembayaran</h5></p>
														<p>Menampilkan jumlah pembayaran pada setiap setiap rekanan yang dicatat dalam suatu periode.</p>
                                                        <a href="#laporan_daftar_pembayaran" aria-controls="laporan_daftar_pembayaran" role="tab" data-toggle="tab" class="btn btn-primary">Lihat Laporan</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
									
									<!-- Laporan Daftar Tagihan -->

                                    <?php             
                                    $kategori = $this->db->order_by('nama_kategori_produk', 'asc')->get_where('kategori_produk', array('status' => 'PUBLISH'))->result_array();
                                    ?>

                                    <div role="tabpanel" class="tab-pane" id="laporan_daftar_tagihan">
                                        <div class="col-sm-15">
                                            <div class="panel panel-default">
												<div class="panel-heading">
                                                    <h3 class="panel-title">Daftar Tagihan</h3>
													<a href="laporan_pembelian">Kembali</a>
                                                </div>
                                                <div style="margin: 20px">
                                                    <div class="row">
                                                        <form action="<?php echo site_url('laporan/cetak_daftar_tagihan_pembelian'); ?>" target="_blank">
                                                            <div class="col-sm-3">
                                                                <input type="text" id="filter_date_f" name="filter_date" class="form-control dtpicker" autocomplete="off" placeholder="Filter by Date">
                                                            </div>                                                         
                                                            <div class="col-sm-3">
                                                                <button class="btn btn-info" type="submit" id="btn-print"><i class="fa fa-print"></i> Print</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <br />
                                                    <div id="box-print" class="table-responsive">
                                                        <div id="loader-table" class="text-center" style="display:none">
                                                            <img src="<?php echo base_url(); ?>assets/back/theme/images/loader.gif">
                                                            <div>
                                                                Please Wait
                                                            </div>
                                                        </div>
                                                        <table class="mytable table table-striped table-hover table-center table-bordered table-condensed" id="table-date4" style="display:none" width="100%";>
                                                            <thead>
                                                            <tr>
																<th align="center" rowspan="2" style="vertical-align:middle;">NO.</th>
																<th align="center">REKANAN</th>
																<th align="center" rowspan="2" style="vertical-align:middle;">NO. INVOICE</th>
																<th align="center" rowspan="2" style="vertical-align:middle;">MEMO</th>
																<th align="center" rowspan="2" style="vertical-align:middle;">VOLUME</th>
																<th align="center" rowspan="2" style="vertical-align:middle;">SATUAN</th>
																<th align="center" rowspan="2" style="vertical-align:middle;">DPP</th>
                                                                <th align="center" rowspan="2" style="vertical-align:middle;">PPN</th>
                                                                <th align="center" rowspan="2" style="vertical-align:middle;">TOTAL</th>
                                                            </tr>
                                                            <tr>
                                                                <th class="text-center">TGL. INVOICE</th>
                                                            </tr>
															</thead>
                                                            <tbody></tbody>
															<tfoot class="mytable table-hover table-center table-bordered table-condensed"></tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
									</div>
									
									<!-- Laporan Hutang -->

                                    <div role="tabpanel" class="tab-pane" id="laporan_hutang">
                                        <div class="col-sm-15">
                                            <div class="panel panel-default">  
												<div class="panel-heading">												
                                                    <h3 class="panel-title">Laporan Hutang</h3>
													<a href="laporan_pembelian">Kembali</a>
                                                </div>
                                                <div style="margin: 20px">
                                                    <div class="row">
                                                        <form action="<?php echo site_url('laporan/cetak_hutang'); ?>" target="_blank">
                                                            <div class="col-sm-3">
                                                                <input type="text" id="filter_date_g" name="filter_date" class="form-control dtpicker" autocomplete="off" placeholder="Filter by Date">
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <select id="filter_kategori_g" name="filter_kategori" class="form-control select2">
                                                                    <option value="">Pilih Kategori</option>
                                                                    <?php
                                                                    foreach ($kategori as $key => $kat) {
                                                                    ?>
                                                                        <option value="<?php echo $kat['id']; ?>"><?php echo $kat['nama_kategori_produk']; ?></option>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>                                                       
                                                            <div class="col-sm-3">
                                                                <button class="btn btn-info" type="submit" id="btn-print"><i class="fa fa-print"></i> Print</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <br />
                                                    <div id="box-print" class="table-responsive">
                                                        <div id="loader-table" class="text-center" style="display:none">
                                                            <img src="<?php echo base_url(); ?>assets/back/theme/images/loader.gif">
                                                            <div>
                                                                Please Wait
                                                            </div>
                                                        </div>
                                                        <table class="mytable table table-striped table-hover table-center table-bordered table-condensed" id="table-date5" style="display:none" width="100%";>
                                                            <thead>
                                                            <tr>
																<th align="center" rowspan="2" style="vertical-align:middle;">NO.</th>
																<th align="center">REKANAN</th>
																<th align="center" rowspan="2" style="vertical-align:middle;">PRODUK</th>
                                                                <th align="center" rowspan="2" style="vertical-align:middle;">PENERIMAAN</th>
																<th align="center" rowspan="2" style="vertical-align:middle;">TAGIHAN</th>
																<th align="center" rowspan="2" style="vertical-align:middle;">PEMBAYARAN</th>
																<th align="center" rowspan="2" style="vertical-align:middle;">HUTANG</th>
                                                            </tr>
                                                            <tr>
																<th align="center">KATEGORI</th>
                                                            </tr>
															</thead>
                                                            <tbody></tbody>
															<tfoot class="mytable table-hover table-center table-bordered table-condensed"></tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
									</div>
									
									<!-- Laporan Umur Hutang -->

                                    <div role="tabpanel" class="tab-pane" id="laporan_umur_hutang">
                                        <div class="col-sm-15">
										<div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title">Laporan Umur Hutang</h3>
													<a href="laporan_pembelian">Kembali</a>
                                                </div>
												<div style="margin: 20px">
													<div class="row">
														<form action="<?php echo site_url('laporan/cetak_umur_hutang');?>" target="_blank">
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
													<div class="table-responsive" id="table-date6">													
													
                    
													</div>
												</div>
										</div>
										
										</div>
                                    </div>
									
									<!-- Laporan Daftar Pembayaran -->
									
									<div role="tabpanel" class="tab-pane" id="laporan_daftar_pembayaran">
                                        <div class="col-sm-15">
                                            <div class="panel panel-default">  
												<div class="panel-heading">												
                                                    <h3 class="panel-title">Laporan Daftar Pembayaran</h3>
													<a href="laporan_pembelian">Kembali</a>
                                                </div>
                                                <div style="margin: 20px">
                                                    <div class="row">
                                                        <form action="<?php echo site_url('laporan/cetak_daftar_pembayaran'); ?>" target="_blank">
                                                            <div class="col-sm-3">
                                                                <input type="text" id="filter_date_i" name="filter_date" class="form-control dtpicker" autocomplete="off" placeholder="Filter by Date">
                                                            </div>                                                           
                                                            <div class="col-sm-3">
                                                                <button class="btn btn-info" type="submit" id="btn-print"><i class="fa fa-print"></i> Print</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <br />
                                                    <div id="box-print" class="table-responsive">
                                                        <div id="loader-table" class="text-center" style="display:none">
                                                            <img src="<?php echo base_url(); ?>assets/back/theme/images/loader.gif">
                                                            <div>
                                                                Please Wait
                                                            </div>
                                                        </div>
                                                        <table class="mytable table table-striped table-hover table-center table-bordered table-condensed" id="table-date7" style="display:none" width="100%";>
                                                            <thead>
                                                            <tr>
																<th align="center" rowspan="2" style="vertical-align:middle;">NO.</th>
																<th align="center">REKANAN</th>
																<th align="center" rowspan="2" style="vertical-align:middle;">NO. PEMBAYARAN</th>
																<th align="center" rowspan="2" style="vertical-align:middle;">TANGGAL TAGIHAN</th>
																<th align="center" rowspan="2" style="vertical-align:middle;">NO. TAGIHAN</th>
																<th align="center" rowspan="2" style="vertical-align:middle;">PEMBAYARAN</th>
                                                            </tr>
                                                            <tr>
                                                                <th class="text-center">TGL. BAYAR</th>
                                                            </tr>
															</thead>
                                                            <tbody></tbody>
															<tfoot class="mytable table-hover table-center table-bordered table-condensed"></tfoot>
                                                        </table>
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
		
		
		
		<!-- Script Daftar Tagihan -->
		
		<script type="text/javascript">
            $('input.numberformat').number(true, 4, ',', '.');
            $('#filter_date_f').daterangepicker({
                autoUpdateInput: false,
				showDropdowns : true,
                locale: {
                    format: 'DD-MM-YYYY'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });

            $('#filter_date_f').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
                TableDate4();
            });

            function TableDate4() {
                $('#table-date4').show();
                $('#loader-table').fadeIn('fast');
                $('#table-date4 tbody').html('');
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('pmm/receipt_material/table_date4'); ?>/" + Math.random(),
                    dataType: 'json',
                    data: {
                        filter_date: $('#filter_date_f').val(),
                    },
                    success: function(result) {
                        if (result.data) {
                            $('#table-date4 tbody').html('');

                            if (result.data.length > 0) {
                                $.each(result.data, function(i, val) {
                                    $('#table-date4 tbody').append('<tr onclick="NextShowDaftarTagihan(' + val.no + ')" class="active" style="font-weight:bold;cursor:pointer;"><td class="text-center">' + val.no + '</td><td class="text-left" colspan="9">' + val.nama + '</td></tr>');
                                    $.each(val.mats, function(a, row) {
                                        var a_no = a + 1;
                                        $('#table-date4 tbody').append('<tr style="display:none;" class="mats-' + val.no + '"><td class="text-center"></td><td class="text-center">' + row.tanggal_invoice + '</td><td class="text-left">' + row.nomor_invoice + '</td><td class="text-left">' + row.memo + '</td><td class="text-right">' + row.volume + '</td><td class="text-center">' + row.measure + '</td><td class="text-right">' + row.jumlah + '</td><td class="text-right">' + row.ppn + '</td><td class="text-right">' + row.total_price + '</td></tr>');
                                    });
									$('#table-date4 tbody').append('<tr style="display:none;" class="mats-' + val.no + '"><td class="text-right" colspan="8"><b>JUMLAH</b></td><td class="text-right"><b>' + val.jumlah + '</b></td></tr>');
                                });
                                $('#table-date4 tbody').append('<tr><td class="text-right" colspan="8"><b>TOTAL</b></td><td class="text-right" ><b>' + result.total + '</b></td></tr>');
                            } else {
                                $('#table-date4 tbody').append('<tr><td class="text-center" colspan="9"><b>NO DATA</b></td></tr>');
                            }
                            $('#loader-table').fadeOut('fast');
                        } else if (result.err) {
                            bootbox.alert(result.err);
                        }
                    }
                });
            }

            function NextShowDaftarTagihan(id) {
                console.log('.mats-' + id);
                $('.mats-' + id).slideToggle();
            }

        </script>
		
		<!-- Script Hutang -->
		
		<script type="text/javascript">
            $('input.numberformat').number(true, 4, ',', '.');
            $('#filter_date_g').daterangepicker({
                autoUpdateInput: false,
				showDropdowns : true,
                locale: {
                    format: 'DD-MM-YYYY'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });

            $('#filter_date_g').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
                TableDate5();
            });

            function TableDate5() {
                $('#table-date5').show();
                $('#loader-table').fadeIn('fast');
                $('#table-date5 tbody').html('');
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('pmm/receipt_material/table_date5'); ?>/" + Math.random(),
                    dataType: 'json',
                    data: {
                        filter_date: $('#filter_date_g').val(),
                        filter_kategori: $('#filter_kategori_g').val(),
                    },
                    success: function(result) {
                        if (result.data) {
                            $('#table-date5 tbody').html('');

                            if (result.data.length > 0) {
                                $.each(result.data, function(i, val) {
                                    window.penerimaan = 0;
                                    window.tagihan = 0;
                                    window.pembayaran = 0;
                                    window.hutang = 0;
                                    $('#table-date5 tbody').append('<tr onclick="NextShowHutang(' + val.no + ')" class="active" style="font-weight:bold;cursor:pointer;"><td class="text-center">' + val.no + '</td><td class="text-left" colspan="6">' + val.nama + '</td></tr>');
                                    $.each(val.mats, function(a, row) {
                                        var a_no = a + 1;
                                        $('#table-date5 tbody').append('<tr style="display:none;" class="mats-' + val.no + '"><td class="text-center"></td><td class="text-left">' + row.kategori_id + '</td><td class="text-left">' + row.nama_produk + '</td><td class="text-right">' + row.penerimaan + '</td><td class="text-right">' + row.tagihan + '</td><td class="text-right">' + row.pembayaran + '</td><td class="text-right">' + row.hutang + '</td></tr>');
                                        
                                        window.penerimaan += parseFloat(row.penerimaan.replace(/\./g,'').replace(',', '.'));
                                        window.tagihan += parseFloat(row.tagihan.replace(/\./g,'').replace(',', '.'));
                                        window.pembayaran += parseFloat(row.pembayaran.replace(/\./g,'').replace(',', '.'));
                                        window.hutang += parseFloat(row.hutang.replace(/\./g,'').replace(',', '.'));
                                        

                                    });
                                    $('#table-date5 tbody').append('<tr class="active"><td class="text-right" colspan="3"><b>Jumlah</b></td><td class="text-right"><b>' + formatter3.format(window.penerimaan) + '</b></td><td class="text-right"><b>' + formatter3.format(window.tagihan) + '</b></td><td class="text-right"><b>' + formatter3.format(window.pembayaran) + '</b></td><td class="text-right"><b>' + formatter3.format(window.hutang) + '</b></td></tr>');
                                });
                                $('#table-date5 tbody').append('<tr style="background-color:#cccccc;"><td class="text-right" colspan="3"><b>Total</b></td><td class="text-right" ><b>' + result.total_jumlah_penerimaan + '</b></td><td class="text-right" ><b>' + result.total_jumlah_tagihan + '</b></td><td class="text-right" ><b>' + result.total_jumlah_pembayaran + '</b></td><td class="text-right" ><b>' + result.total_jumlah_hutang + '</b></td></tr>');
                            } else {
                                $('#table-date5 tbody').append('<tr><td class="text-center" colspan="7"><b>NO DATA</b></td></tr>');
                            }
                            $('#loader-table').fadeOut('fast');
                        } else if (result.err) {
                            bootbox.alert(result.err);
                        }
                    }
                });
            }

            function NextShowHutang(id) {
                console.log('.mats-' + id);
                $('.mats-' + id).slideToggle();
            }

            $('#filter_kategori_g').change(function() {
                TableDate5();
            });

            window.formatter3 = new Intl.NumberFormat('id-ID', {
                style: 'decimal',
                currency: 'IDR',
                symbol: 'none',
				minimumFractionDigits : '0'
            });

        </script>
		
		<!-- Script Umur Hutang -->

        <script type="text/javascript">
			$('#filter_date_h').daterangepicker({
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

				$('#filter_date_h').on('apply.daterangepicker', function(ev, picker) {
					  $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
					  TableDate6();
				});


				function TableDate6()
				{
					$('#wait').fadeIn('fast');   
					$.ajax({
						type    : "POST",
						url     : "<?php echo site_url('pmm/receipt_material/umur_hutang'); ?>/"+Math.random(),
						dataType : 'html',
						data: {
							filter_date : $('#filter_date_h').val(),
						},
						success : function(result){
							$('#table-date6').html(result);
							$('#wait').fadeOut('fast');
						}
					});
				}

				TableDate6();
			
            </script>
		
		<!-- Script Daftar Pembayaran -->
		
		<script type="text/javascript">
            $('input.numberformat').number(true, 4, ',', '.');
            $('#filter_date_i').daterangepicker({
                autoUpdateInput: false,
				showDropdowns : true,
                locale: {
                    format: 'DD-MM-YYYY'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });

            $('#filter_date_i').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
                TableDate7();
            });

            function TableDate7() {
                $('#table-date7').show();
                $('#loader-table').fadeIn('fast');
                $('#table-date7 tbody').html('');
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('pmm/receipt_material/table_date7'); ?>/" + Math.random(),
                    dataType: 'json',
                    data: {
                        filter_date: $('#filter_date_i').val(),
                    },
                    success: function(result) {
                        if (result.data) {
                            $('#table-date7 tbody').html('');

                            if (result.data.length > 0) {
                                $.each(result.data, function(i, val) {
                                    $('#table-date7 tbody').append('<tr onclick="NextShowDaftarPembayaran(' + val.no + ')" class="active" style="font-weight:bold;cursor:pointer;"><td class="text-center">' + val.no + '</td><td class="text-left" colspan="5">' + val.supplier_name + '</td></tr>');
                                    $.each(val.mats, function(a, row) {
                                        var a_no = a + 1;
                                        $('#table-date7 tbody').append('<tr style="display:none;" class="mats-' + val.no + '"><td class="text-center"></td><td class="text-center">' + row.tanggal_pembayaran + '</td><td class="text-left">' + row.nomor_transaksi + '</td><td class="text-center">' + row.tanggal_invoice + '</td><td class="text-left">' + row.nomor_invoice + '</td><td class="text-right">' + row.pembayaran + '</td></tr>');
                                    });
									$('#table-date7 tbody').append('<tr style="display:none;" class="mats-' + val.no + '"><td class="text-right" colspan="5"><b>JUMLAH</b></td><td class="text-right">' + val.total_bayar + '</td></tr>');
                                });
                                $('#table-date7 tbody').append('<tr><td class="text-right" colspan="5"><b>TOTAL</b></td><td class="text-right" ><b>' + result.total + '</b></td></tr>');
                            } else {
                                $('#table-date7 tbody').append('<tr><td class="text-center" colspan="6"><b>NO DATA</b></td></tr>');
                            }
                            $('#loader-table').fadeOut('fast');
                        } else if (result.err) {
                            bootbox.alert(result.err);
                        }
                    }
                });
            }

            function NextShowDaftarPembayaran(id) {
                console.log('.mats-' + id);
                $('.mats-' + id).slideToggle();
            }
			

        </script>
		
        
</body>

</html>