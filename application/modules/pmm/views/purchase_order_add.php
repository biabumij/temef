<!doctype html>
<html lang="en" class="fixed">
<head>
    <?php echo $this->Templates->Header();?>

    <style type="text/css">
        .form-check{
            display: inline-block;;
        }
        .form-detail a{
            margin-top: 10px;
        }
        #form-po .control-label{
            text-align: left;
        }
    </style>
</head>

<body>
<div class="wrap">
    
    <?php echo $this->Templates->PageHeader();?>

    <div class="page-body">
        <?php echo $this->Templates->LeftBar();?>
        <div class="content">
            <div class="content-header">
                <div class="leftside-content-header">
                    <ul class="breadcrumbs">
                        <li><i class="fa fa-sitemap" aria-hidden="true"></i><a href="<?php echo site_url('admin');?>">Dashboard</a></li>
                        <li><a href="<?php echo site_url('admin/pembelian');?>"> Pesanan Pembelian</a></li>
                        <li><a>Detail Pemesanan Pembelian</a></li>
                    </ul>
                </div>
            </div>
            <div class="row animated fadeInUp">
                <div class="col-sm-12 col-lg-12">
                    <div class="panel">
                        <div class="panel-header">
                            
                            <div class="">
                                <h3 class="">Detail Pesanan Pembelian <?php echo $this->pmm_model->GetStatus($data['status']);?></h3>
                                
                            </div>
                        </div>
                        <div class="panel-content">
                            <div class="row">
                                <div class="col-sm-6">
                                    <form id="form-po" class="form-horizontal">
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-sm-3 control-label text-align:right">No PO : </label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="no_po" value="<?php echo $data['no_po'];?>" readonly="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Tanggal PO : </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="date_po" class="form-control dtpicker" value="<?php echo date('d-m-Y',strtotime($data['date_po']));?>" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Perihal : </label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="subject" value="<?php echo $data['subject'];?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Lampiran : </label>
                                            <div class="col-sm-8">
                                                <a href="<?= base_url("uploads/purchase_order/".$data["document_po"]) ?>" target="_blank"><?php echo $data['document_po'];?></a>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Dibuat Oleh : </label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" value="<?php echo $this->crud_global->GetField('tbl_admin',array('admin_id'=>$data['created_by']),'admin_name');?>" readonly="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Dibuat Tanggal : </label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" value="<?= date('d/m/Y H:i:s',strtotime($data['created_on']));?>" readonly="">
                                            </div>
                                        </div>
									
                                                <input type="hidden" class="form-control" id="date_pkp" value="<?php echo date('d-m-Y',strtotime('10-02-2021'));?>" readonly="">
                                         
                                    </form>
                                </div>
                                <div class="col-sm-6">
                                    <form class="form-horizontal">
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Rekanan : </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="supplier" name="supplier" class="form-control" value="<?php echo $supplier_name;?>" readonly="">
                                                <input type="hidden" id="supplier_id" name="supplier_id" class="form-control" value="<?php echo $data['supplier_id'];?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Alamat : </label>
                                            <div class="col-sm-8">
                                                <textarea id="address_supplier"  class="form-control" rows="5" readonly=""><?php echo $address_supplier;?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-sm-3 control-label">NPWP : </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="npwp_supplier" class="form-control" value="<?php echo $npwp_supplier;?>" readonly="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-sm-3 control-label">No. Penawaran : </label>
                                            <div class="col-sm-8">
                                            <?php
                                            foreach ($details as $dt) {
                                            ?>
                                                <b><?php echo $this->crud_global->GetField('produk',array('id'=>$dt['material_id']),'nama_produk');?></b><span class="form-control" readonly=""><a target="_blank" href="<?= base_url("pembelian/penawaran_pembelian_detail/".$dt['penawaran_id'])?>"><?php echo $this->crud_global->GetField('pmm_penawaran_pembelian',array('id'=>$dt['penawaran_id']),'nomor_penawaran');?></a></span>
                                            <?php
                                            }
                                            ?>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            
                            <br />
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover table-center" id="guest-table">
                                    <thead>
                                        <tr>
                                            <th width="50px" class="text-center">No</th>
                                            <th class="text-center">Produk</th>
                                            <th class="text-center">Satuan</th>
                                            <th class="text-center">Volume</th>
                                            <th class="text-center">Harga Satuan</th>
                                            <th class="text-center">Nilai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       <?php
                                       $subtotal = 0;
                                       $total = 0;
                                       $tax_0 = 0;
									   $tax_ppn = 0;
									   $tax_pph = 0;
                                       $tax_ppn11 = 0;
                                       foreach ($details as $no => $dt) {
                                            $nilai = $dt['total'] * $dt['price'];
                                           ?>  
                                           <tr>
                                               <td class="text-center"><?= $no + 1;?></td>
                                               <td class="text-left"><?php echo $this->crud_global->GetField('produk',array('id'=>$dt['material_id']),'nama_produk');?></td>
                                               <td class="text-center"><?php echo $dt['measure'];?></td>
                                               <td class="text-center"><?php echo number_format($dt['total'],2,',','.');?></td>
                                               <td class="text-right"><?php echo number_format($dt['price'],0,',','.');?></td>
                                               <td class="text-right"><?php echo number_format($nilai,0,',','.');?></td>
                                               <input type="hidden" id="total-<?php echo $no;?>" value="<?php echo $subtotal;?>" >
                                           </tr>

                                           <?php
                                           $subtotal += $dt['total'] * $dt['price'];
                                           if($dt['tax_id'] == 4){
                                               $tax_0 = true;
                                           }
                                           if($dt['tax_id'] == 3){
                                               $tax_ppn += $dt['tax'];
                                           }
                                           if($dt['tax_id'] == 5){
                                               $tax_pph += $dt['tax'];
                                           }
                                           if($dt['tax_id'] == 6){
                                               $tax_ppn11 += $dt['tax'];
                                           }
                                           
                                       }
                                       ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-right">Sub Total</th>
                                        <th  class="text-right"><?= number_format($subtotal,0,',','.'); ?></th>
                                    </tr>
                                    <?php
                                    if($tax_ppn > 0){
                                        ?>
                                        <tr>
                                            <th colspan="5" class="text-right">Pajak (PPN 10%)</th>
                                            <th  class="text-right"><?= number_format($tax_ppn,0,',','.'); ?></th>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                    if($tax_0){
                                        ?>
                                        <tr>
                                            <th colspan="5" class="text-right">Pajak (PPN 0%)</th>
                                            <th  class="text-right"><?= number_format(0,0,',','.'); ?></th>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                    if($tax_pph > 0){
                                        ?>
                                        <tr>
                                            <th colspan="5" class="text-right">Pajak (PPh 23)</th>
                                            <th  class="text-right"><?= number_format($tax_pph,0,',','.'); ?></th>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                    if($tax_ppn11 > 0){
                                        ?>
                                        <tr>
                                            <th colspan="5" class="text-right">Pajak (PPN 11%)</th>
                                            <th  class="text-right"><?= number_format($tax_ppn11,0,',','.'); ?></th>
                                        </tr>
                                        <?php
                                    }
                                    $total = $subtotal + $tax_ppn - $tax_pph + $tax_ppn11;
                                    ?>
                                    
                                    <tr>
                                        <th colspan="5" class="text-right">TOTAL</th>
                                        <th  class="text-right"><?= number_format($total,0,',','.'); ?></th>
                                    </tr>
                                </tfoot>
                                </table>
                            </div>

                            <br />
                            <div class="text-right">

                                <?php
                                if($data['status'] == 'PUBLISH'){
                                    ?>
                                    <a href="<?= site_url('pmm/purchase_order/get_pdf/'.$id);?>" target="_blank" class="btn btn-info"><i class="fa fa-print"></i> Cetak</a><br />
                                    <a href="<?= site_url('pmm/receipt_material/manage/'.$id);?>" class="btn btn-success"><i class="fa fa-truck"></i> Terima Produk</a>
                                    <br />
                                    <?php
                                    if($this->session->userdata('admin_group_id') == 1 || $this->session->userdata('admin_group_id') == 4 ||  $this->session->userdata('admin_group_id') == 5){
                                        ?>
                                        <form class="form-approval" action="<?= base_url("pembelian/closed_po/".$id) ?>">
                                            <button type="submit" class="btn btn-danger"><i class="fa fa-close"></i> Closed Pesanan Pembelian</button>        
                                        </form>	
                                        <?php
                                    }
                                }
                                ?>
                                <input type="hidden" id="purchase_order_id" value="<?php echo $id;?>">
                                <?php
                                if($data['status'] == 'DRAFT'){
                                    ?>
                                    <a onclick="ProcessForm('<?php echo site_url('pmm/purchase_order/process/'.$id.'/3');?>')" class="btn btn-warning check-btn" id="btn-po"><i class="fa fa-send"></i> Buat PO</a>
                                    <?php
                                }else if($data['status'] == 'WAITING'){
                                    if($this->session->userdata('admin_group_id') == 1 || $this->session->userdata('admin_group_id') == 4 || $this->session->userdata('admin_group_id') == 5 || $this->session->userdata('admin_group_id') == 6 || $this->session->userdata('admin_group_id') == 10){
                                        ?>
                                        <a onclick="CreatePO()" class="btn btn-success"><i class="fa fa-check"></i> Setujui</a>
                                        <a onclick="ProcessForm('<?php echo site_url('pmm/purchase_order/process/'.$id.'/2');?>')" class="btn btn-danger check-btn"><i class="fa fa-close"></i> Tolak</a>
                                        <?php
                                    }
                                }
                                ?>
                            
                                <?php if($data["status"] === "CLOSED") : ?>
                                    <a href="<?= site_url('pmm/purchase_order/get_pdf/'.$id);?>" target="_blank" class="btn btn-info"><i class="fa fa-print"></i> Cetak</a><br />
                                    <?php
                                    if($this->session->userdata('admin_group_id') == 1){
                                        ?>
                                        <form class="form-check" action="<?= site_url("pmm/purchase_order/delete/".$id);?>">
                                            <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Hapus</button>        
                                        </form>	
                                        <?php
                                    }
                                    ?>
                                <?php endif; ?>

                                <?php if($data["status"] === "REJECTED") : ?>                             
                                    <?php
                                    if($this->session->userdata('admin_group_id') == 1){
                                        ?>
                                        <form class="form-check" action="<?= site_url("pmm/purchase_order/delete/".$id);?>">
                                            <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Hapus</button>        
                                        </form>	
                                        <?php
                                    }
                                    ?>
                                <?php endif; ?>

                                <form>
                                    <br />
                                    <a href="<?php echo site_url('admin/pembelian');?>" class="btn btn-info"><i class="fa fa-mail-reply"></i> Kembali</a>
                                </form>
                            
                            </div>
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
    <script src="<?php echo base_url();?>assets/back/theme/vendor/bootbox.min.js"></script>
    <script src="<?php echo base_url();?>assets/back/theme/vendor/daterangepicker/moment.min.js"></script>
    <script src="<?php echo base_url();?>assets/back/theme/vendor/daterangepicker/daterangepicker.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/back/theme/vendor/daterangepicker/daterangepicker.css">
    <script src="<?php echo base_url();?>assets/back/theme/vendor/jquery.number.min.js"></script>

    <script type="text/javascript">
        

        $('#pph_val').number(true,0,',','.');
        $('.dtpicker').daterangepicker({
                singleDatePicker: true,
                locale: {
              format: 'DD-MM-YYYY'
            }
            });
        $('.dtpicker').on('apply.daterangepicker', function(ev, picker) {
              $(this).val(picker.startDate.format('DD-MM-YYYY'));
              // table.ajax.reload();
        });

        function ProcessForm(url){
            bootbox.confirm("Apakah anda yakin untuk proses data ini ?", function(result){ 
                if(result){
                    window.location.href = url;
                }
            });
        }

        function CreatePO(){
            bootbox.confirm("Apakah anda yakin untuk proses data ini ?", function(result){ 
                if(result){
                    $('#btn-po').button('loading');
                    var arr = {
                        date_po : $('#date_po').val(),
                        subject : $('#subject').val(),
                        date_pkp : $('#date_pkp').val(),
                        supplier_id : $('#supplier_id').val(),
                        total : $('#total').val(),
                        ppn : $('#ppn').val(),
                        pph : $('#pph').val(),
                        pph : $('#ppn11').val(),
                        id : $('#purchase_order_id').val()
                    }
                    if($('#date_po').val() == '' || $('#subject').val() == '' || $('#date_pkp').val() == '' || $('#supplier').val() == ''){
                        bootbox.alert('Opps !! Please fill the field first !!');
                    }else {

                        $.ajax({
                            type    : "POST",
                            url     : "<?php echo site_url('pmm/purchase_order/approve_po'); ?>",
                            dataType : 'json',
                            data: arr,
                            success : function(result){
                                if(result.output){
                                    // table.ajax.reload();
                                    // bootbox.alert('Berhasil menghapus!!');
                                    window.location.href = result.url;
                                }else if(result.err){
                                    bootbox.alert(result.err);
                                }
                                $('#btn-po').button('reset');
                            }
                        });
                    }

                }
            });
        }

        $('#supplier').change(function(){
            var npwp = $('option:selected', this).attr('data-npwp');
            var address = $('option:selected', this).attr('data-address');
            $('#npwp_supplier').val(npwp);
            $('#address_supplier').val(address);
            console.log(npwp);
        });

        $('.ppn-get').change(function(){
            
            
            var ppn_val = $('#ppn_val').val();
            var ppn = ppn_val;
            var pph = $('#pph_val').val();
            var val = $(this).val();
            var total = $('#total-'+val).val();
            var subtotal = $('#total').val();
            var total_fix = $('#total-fix').val();
            if ($(this).is(':checked')) {

                if(total > 0){
                    ppn = (total * 10) / 100;
                    ppn = parseInt(ppn_val) + parseInt(ppn);
                }
                $('#ppn_val').val(ppn);
                $('#ppn').number(ppn, 2,',','.' );
                total_all = parseInt(total_fix) + parseInt(ppn) - parseInt(pph);
                $('#total').val(total_all);
                $('#total-text').number(total_all, 2,',','.' );

            }else {
                if(total > 0){
                    ppn = (total * 10) / 100;
                    total_all = parseInt(subtotal) - parseInt(ppn) - parseInt(pph);
                    ppn = parseInt(ppn_val) - parseInt(ppn);

                }

                
                $('#ppn_val').val(ppn);
                $('#ppn').number(ppn, 2,',','.' );
                
                $('#total').val(total_all);
                $('#total-text').number(total_all, 2,',','.' );


            }
        });
        $('.pph-get').keyup(function(){
            
            var val = $(this).val();
            var data_val = $(this).attr('data-val');
            var pph_val = $('#pph_val').val();
            var total = $('#total-'+data_val).val();
            var ppn = $('#ppn_val').val();
            var total_fix = $('#total-fix').val();
            var subtotal = $('#total').val();
            // console.log(val);
            countPPH();
        });


        function countPPH()
        {   
            var total_pph = 0;
            $('.pph-get').each(function(key,val){
                var no = key + 1;
                var subtotal = $('#total-'+no).val();
                var val = $(val).val();
                var pph = (subtotal * val) / 100;
                total_pph += pph;
                
            });
            var total = $('#total-fix').val();
            var ppn = $('#ppn_val').val();
            $('#pph_val').val(total_pph);
            $('#pph').number(total_pph, 2,',','.' );
            total_all = (parseInt(total)  - parseInt(total_pph)) + parseInt(ppn);
            
            $('#total').val(total_all);
            $('#total-text').number(total_all, 2,',','.' );
        }

        $('.form-check').submit(function(e){
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

        $('.form-approval').submit(function(e){
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
