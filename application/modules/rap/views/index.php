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
											<li><a href="<?= site_url('rap/form_rap'); ?>">RAP</a></li>
                                        </ul>
                                    </div>
                                </h3>

                            </div>
                            <div class="panel-content">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#jmd" aria-controls="jmd" role="tab" data-toggle="tab">RAP</a></li>
                                </ul>

                                <div class="tab-content">			
								
                                    <div role="tabpanel" class="tab-pane active" id="rap">									
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover" id="table_rap" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" width="5%">No.</th>
														<th class="text-center">Tanggal RAP</th>
														<th class="text-center">No. RAP</th>
														<th class="text-center">Total Bahan</th>
														<th class="text-center">Total Alat</th>
                                                        <th class="text-center">Total Overhead</th>
                                                        <th class="text-center">Total Biaya Admin</th>
                                                        <th class="text-center">Total Diskonto</th>
                                                        <th class="text-center">Lapmpiran</th>
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
		
		var table_rap = $('#table_rap').DataTable({
            ajax: {
                processing: true,
                serverSide: true,
                url: '<?php echo site_url('rap/table_rap'); ?>',
                type: 'POST',
                data: function(d) {
					d.filter_product = $('#filter_product').val();
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
                    "data": "tanggal_rap"
                },
				{
                    "data": "nomor_rap"
                },
				{
                    "data": "total_bahan"
                },
				{
                    "data": "total_alat"
                },
                {
                    "data": "total_overhead"
                },
                {
                    "data": "total_biaya_admin"
                },
                {
                    "data": "total_diskonto"
                },
                {
                    "data": "lampiran"
                },
				{
					"data": "actions"
				},
            ],
            "columnDefs": [{
                    "targets": [0, 1, 9],
                    "className": 'text-center',
                },
                {
                    "targets": [3, 4, 5, 6, 7],
                    "className": 'text-right',
                }
            ],
        });

		$('#filter_product').change(function() {
                table_rap.ajax.reload();
        });
	
		function DeleteData(id) {
        bootbox.confirm("Are you sure to delete this data ?", function(result) {
            // console.log('This was logged in the callback: ' + result); 
            if (result) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('rap/delete_rap'); ?>",
                    dataType: 'json',
                    data: {
                        id: id
                    },
                    success: function(result) {
                        if (result.output) {
                            table_rap.ajax.reload();
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