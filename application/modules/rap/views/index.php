<!doctype html>
<html lang="en" class="fixed">

<head>
    <?php echo $this->Templates->Header(); ?>
    <style type="text/css">
        .tab-pane {
            padding-top: 20px;
        }

        .select2-container--default .select2-results__option[aria-disabled=true] {
            display: none;
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
                            <li><i class="fa fa-sitemap" aria-hidden="true"></i><a href="<?php echo site_url('admin'); ?>">Dashboard</a></li>
                            <li><a><?php echo $row[0]->menu_name; ?></a></li>
                        </ul>
                    </div>
                </div>
                <div class="row animated fadeInUp">
                    <div class="col-sm-12 col-lg-12">
                        <div class="panel">
                            <div class="panel-header">
                                <h3 class="section-subtitle">
                                    <?php echo $row[0]->menu_name; ?>
                                    <div class="pull-right">
                                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-plus"></i> Buat Baru <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a href="<?= site_url('komposisi/form_komposisi'); ?>">Bahan</a></li>
											<li><a href="<?= site_url('rap/form_alat'); ?>">Alat</a></li>
                                            <li><a href="<?= site_url('rap/form_bua'); ?>">BUA</a></li>
                                        </ul>
                                    </div>
                                </h3>

                            </div>
                            <div class="panel-content">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#komposisi_agregat" aria-controls="komposisi_agregat" role="tab" data-toggle="tab">Bahan</a></li>
                                    <li role="presentation"><a href="#alat" aria-controls="alat" role="tab" data-toggle="tab">Alat</a></li>
                                    <li role="presentation"><a href="#bua" aria-controls="bua" role="tab" data-toggle="tab">BUA</a></li>
                                </ul>

                                <div class="tab-content">
                                    
                                <!-- Table Bahan -->
									
                                <div role="tabpanel" class="tab-pane active" id="komposisi_agregat">
										<div class="col-sm-4">
											<input type="text" id="filter_date_agregat" name="filter_date" class="form-control dtpickerange" autocomplete="off" placeholder="Filter By Date">
										</div>
										<br />
										<br />										
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover" id="table_agregat" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th width="5%">No</th>
														<th width="20%">Tanggal</th>
														<th width="20%">Mutu Beton</th>
                                                        <th width="35%">Judul</th>
														<th width="20%">Lampiran</th>
														
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                                <tfoot>
                                                   
                                                </tfoot>
                                            </table>
                                        </div>
									</div>
										
									<!-- Table Alat -->
								
                                    <div role="tabpanel" class="tab-pane" id="alat">									
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover" id="table_rap_alat" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" width="5%">No.</th>
														<th class="text-center">Tanggal</th>
														<th class="text-center">Nomor</th>
														<th class="text-center">Batching Plant</th>
														<th class="text-center">Truck Mixer</th>
                                                        <th class="text-center">Wheel Loader</th>
                                                        <th class="text-center">BBM Solar</th>
                                                        <th class="text-center">Lampiran</th>
														<th class="text-center">Tindakan</th>
													</tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                                <tfoot>
                                                   
                                                </tfoot>
                                            </table>
                                        </div>
									</div>

                                    <!-- Table bua -->
								
                                    <div role="tabpanel" class="tab-pane" id="bua">									
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover" id="table_rap_bua" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" width="5%">No.</th>
														<th class="text-center">Tanggal</th>
														<th class="text-center">Nomor</th>
                                                        <th class="text-center">Lampiran</th>
														<th class="text-center">Tindakan</th>
													</tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                                <tfoot>
                                                   
                                                </tfoot>
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

    <?php echo $this->Templates->Footer(); ?>

    <script src="<?php echo base_url(); ?>assets/back/theme/vendor/jquery.number.min.js"></script>
    
    <script src="<?php echo base_url(); ?>assets/back/theme/vendor/bootbox.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.3.1/css/select.dataTables.min.css">
    <script type="text/javascript" src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/back/theme/vendor/daterangepicker/moment.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/back/theme/vendor/daterangepicker/daterangepicker.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/back/theme/vendor/daterangepicker/daterangepicker.css">
    
    <script type="text/javascript">
	$('#dtpickerange').daterangepicker({
        autoUpdateInput: false,
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
        },
        showDropdowns: true,
		});
		
		var table_agregat = $('#table_agregat').DataTable({
            ajax: {
                processing: true,
                serverSide: true,
                url: '<?php echo site_url('komposisi/table_agregat'); ?>',
                type: 'POST',
                data: function(d) {
                    d.filter_date = $('#filter_date_agregat').val();
                }
            },
            responsive: true,
            "deferRender": true,
            "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
            },
            columns: [
				{
                    "data": "no"
                },
				{
                    "data": "date_agregat"
                },
				{
                    "data": "mutu_beton"
                },
				{
                    "data": "jobs_type"
                },
				{
                    "data": "lampiran"
                }
            ],
            "columnDefs": [{
                    "targets": [0, 1, 2, 3],
                    "className": 'text-center',
                }
            ],
        });
		
		$('#filter_date_agregat').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
        table_agregat.ajax.reload();
		});
	
    </script>

    <script type="text/javascript">
		
		var table_rap_alat = $('#table_rap_alat').DataTable({
            ajax: {
                processing: true,
                serverSide: true,
                url: '<?php echo site_url('rap/table_rap_alat'); ?>',
                type: 'POST',
                data: function(d) {
					//d.filter_product = $('#filter_product').val();
                }
            },
            responsive: true,
            "deferRender": true,
            "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
            },
            columns: [
				{
                    "data": "no"
                },
				{
                    "data": "tanggal_rap_alat"
                },
				{
                    "data": "nomor_rap_alat"
                },
				{
                    "data": "batching_plant"
                },
				{
                    "data": "truck_mixer"
                },
                {
                    "data": "wheel_loader"
                },
                {
                    "data": "bbm_solar"
                },
                {
                    "data": "lampiran"
                },
				{
					"data": "actions"
				},
            ],
            "columnDefs": [{
                    "targets": [0, 1, 8],
                    "className": 'text-center',
                },
                {
                    "targets": [3, 4, 5, 6],
                    "className": 'text-right',
                }
            ],
        });
	
		function DeleteData(id) {
        bootbox.confirm("Are you sure to delete this data ?", function(result) {
            // console.log('This was logged in the callback: ' + result); 
            if (result) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('rap/delete_rap_alat'); ?>",
                    dataType: 'json',
                    data: {
                        id: id
                    },
                    success: function(result) {
                        if (result.output) {
                            table_rap_alat.ajax.reload();
                            bootbox.alert('Berhasil menghapus!!');
                        } else if (result.err) {
                            bootbox.alert(result.err);
                        }
                    }
                });
            }
        });
    }
    </script>

    <script type="text/javascript">
		
		var table_rap_bua = $('#table_rap_bua').DataTable({
            ajax: {
                processing: true,
                serverSide: true,
                url: '<?php echo site_url('rap/table_rap_bua'); ?>',
                type: 'POST',
                data: function(d) {
                }
            },
            responsive: true,
            "deferRender": true,
            "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
            },
            columns: [
                {
                    "data": "no"
                },
                {
                    "data": "tanggal_rap_bua"
                },
                {
                    "data": "nomor_rap_bua"
                },
                {
                    "data": "lampiran"
                },
                {
                    "data": "actions"
                },
            ],
            "columnDefs": [{
                    "targets": [0, 1, 4],
                    "className": 'text-center',
                }
            ],
        });
	
		function DeleteDataBUA(id) {
        bootbox.confirm("Are you sure to delete this data ?", function(result) {
            // console.log('This was logged in the callback: ' + result); 
            if (result) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('rap/delete_rap_bua'); ?>",
                    dataType: 'json',
                    data: {
                        id: id
                    },
                    success: function(result) {
                        if (result.output) {
                            table_rap_alat.ajax.reload();
                            bootbox.alert('Berhasil menghapus!!');
                        } else if (result.err) {
                            bootbox.alert(result.err);
                        }
                    }
                });
            }
        });
    }
    </script>

</body>

</html>