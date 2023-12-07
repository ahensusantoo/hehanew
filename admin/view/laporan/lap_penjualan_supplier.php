<?php

    if(empty($_POST['tgl_awal']) || empty($_POST['tgl_akhir'])){
        $tgl_awal = date('Y-m-d');
        $tgl_akhir = date('Y-m-d');
    } else {
        $tgl_awal = date('Y-m-d', strtotime($_POST['tgl_awal']));
        $tgl_akhir = date('Y-m-d', strtotime($_POST['tgl_akhir']));
    }

    if(!empty($_POST['filer1'])){
        $filer1 = $_POST['filer1'];
    } else {
        $filer1 = '';
    }
    
    if(!empty($_POST['supplier'])){
        $supplier = $_POST['supplier'];
    } else {
        $supplier = '';
    }

    $list_stall         = $db->query("SELECT * FROM merchant WHERE status_aktif_merchant='Y' ")->fetch_all(MYSQLI_ASSOC);
    $list_supplier      = $db->query("SELECT * FROM supplier WHERE status_rmv_supplier='N' ")->fetch_all(MYSQLI_ASSOC);

   if($filer1 == 'all'){
        $stall_dipilih = $list_stall;
    }else{
        $id_merchant = enkripsiDekripsi($filer1, 'dekripsi');
        $stall_dipilih = $db->query("SELECT * FROM merchant WHERE status_aktif_merchant='Y' AND id_merchant='$id_merchant'")->fetch_all(MYSQLI_ASSOC);
    }
    
    if($supplier == 'all'){
        $supplier_dipilih = $list_supplier;
    }else{
        $id_supplier = enkripsiDekripsi($supplier, 'dekripsi');
        $supplier_dipilih = $db->query("SELECT * FROM supplier WHERE status_rmv_supplier='N' AND id_supplier ='$id_supplier' ")->fetch_all(MYSQLI_ASSOC);
    }

    
    foreach($stall_dipilih as $key_stall => $value_stall){
        foreach($supplier_dipilih as $key_supplier => $value_supplier){
            $sql = $db->query("SELECT c.nama_produk, c.harga_produk as harga_jual,  d.nama_supplier,
                                COALESCE(SUM(a.jumlah_produk),0) as jumlah_terjual,
                                (SELECT e.harga_beli FROM merchant_history_stok e
                                    WHERE e.kd_merchant_produk = c.id_merchant_produk
                                    GROUP BY e.kd_merchant_produk
                                    ORDER BY e.kd_merchant_produk DESC
                                    LIMIT 1) as harga_beli
                            FROM merchant_transaksi_detail a 
                            JOIN merchant_transaksi b ON a.kd_merchant_transaksi=b.id_merchant_transaksi
                            JOIN merchant_produk c ON a.kd_merchant_produk=c.id_merchant_produk
                            JOIN supplier d ON c.id_supplier=d.id_supplier
                            WHERE b.kd_jenis_pembayaran != ''
                            	AND a.kd_merchant = '$value_stall[id_merchant]'
                                AND a.status_transaksi_detail = '2'
                                AND b.status_transaksi = '2'
                                AND c.id_supplier = '$value_supplier[id_supplier]'
                                AND DATE(a.tgl_input_detail) BETWEEN '$tgl_awal' AND '$tgl_akhir'
                                GROUP BY a.kd_merchant_produk
                        	")->fetch_all(MYSQLI_ASSOC);
            $result[$value_stall['nama_merchant']][$value_supplier['nama_supplier']] = $sql;
            
        } 
    }
    
    
    // echo "<pre>"; echo print_r($result);
   
    
?>

<link rel="stylesheet" href="plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1 class="m-0 text-dark">Laporan Penjualan Harian Per Produk</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Periode</label>
                            <div class="col-sm-10">
                                <button type="button" class="btn btn-default" id="daterange-btn" style="width: 100%">
                                    <i class="far fa-calendar-alt"></i> <span id="reportrange"><?= date('j F Y', strtotime($tgl_awal)).' - '.date('j F Y', strtotime($tgl_akhir)); ?></span>
                                    <i class="fas fa-caret-down"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Stall </label>
                            <div class="col-sm-10">
                                <select name="filer1" class="select2bs4 form-control" id="filer1" style="width: 100%" required>
                                    <!-- <option value="all"> ALL STALL</option> -->
                                    <?php foreach ($list_stall as $bulan => $value): ?>
                                        <?php $id_stall_encryp = enkripsiDekripsi($value['id_merchant'],'enkripsi'); ?>
                                        <option value="<?= $id_stall_encryp ?>" <?php if($filer1 == $id_stall_encryp){echo 'selected';} ?>> <?= strtoupper($value['nama_merchant']) ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Supplier </label>
                            <div class="col-sm-10">
                                <select name="supplier" class="select2bs4 form-control" id="id_supplier" style="width: 100%" required>
                                    <?php foreach ($list_supplier as $key => $value): ?>
                                        <?php $id_supplier_encryp = enkripsiDekripsi($value['id_supplier'],'enkripsi'); ?>
                                        <option value="<?= $id_supplier_encryp ?>" <?php if($supplier == $id_supplier_encryp){echo 'selected';} ?> ><?= strtoupper($value['kode_supplier']) ?> - <?= strtoupper($value['nama_supplier']) ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <a onclick="terapkan()">
                          <button type="button" class="btn btn-primary" style="width: 100%">Terapkan</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php if(!isset($result)) : ?>
                <center>
                    Silahkan pilih Stall Dan Supplier Terlebih Dahulu<br>
                    <?= tanggal_indo($tgl_awal) ?> - <?= tanggal_indo($tgl_akhir) ?><br><br>
                    <i class="fa-2x fas fa-times-circle"></i>
                    <hr>
                </center>
                <?php goto tanpaData; ?>

            <?php endif; ?>
        
        <?php foreach($result as $key_merchant => $value_merchant ) { ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-header">
                                <a onclick="exportexcel('<?= $tgl_awal ?>', '<?= $tgl_akhir ?>', '<?=$_POST['filer1']?>', '<?=$_POST['supplier']?>')">
                                  <button type="button" class="btn btn-default" style="width: 100%"><i class="fa fa-fw fa-file-download"></i>Eksport Excel</button>
                                </a>
                              </div>
                        </div>
                        <?php foreach($value_merchant as $key_supplier => $value_supplier ) { ?>
                            <div class="card-body">
                                <b>Nama Stall : <?=$key_merchant?></b>
                                <table width="50%">
                                    <tbody>
                                        <tr>
                                            <td><b>Nama Supplier</b></td>
                                            <td> : </td>
                                            <td><b><?=$key_supplier?></b></td>
                                        </tr>
                                        <tr>
                                            <td><b>Priode</b></td>
                                            <td> : </td>
                                            <td><b><?=tanggal_indo($tgl_awal)?> - <?=tanggal_indo($tgl_akhir)?></b></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <hr>

                                <table class="table table-bordered table2excel">
                                    

                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Produk</th>
                                            <th>Jumlah Terjual</th>
                                            <th>Harga Beli</th>
                                            <th>Harga Jual</th>
                                            <th>Omset</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($value_supplier)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center">Tidak ada data</td>
                                            </tr>
                                        <?php else: ?>
                                        
                                            <?php $total_jumlah_terjual = 0; $total_harga_beli = 0; $total_harga_jual = 0; $total_omset = 0; ?>
                                            <?php $no = 1; foreach($value_supplier as $key_detail => $value_detail ) { ?>
                                                <?php 
                                                    $omset = ($value_detail['harga_jual'] - $value_detail['harga_beli']) * $value_detail['jumlah_terjual']; 
                                                    $total_jumlah_terjual += $value_detail['jumlah_terjual'];
                                                    $total_harga_beli += $value_detail['harga_beli'];
                                                    $total_harga_jual += $value_detail['harga_jual'];
                                                    $total_omset += $omset;
                                                ?>
                                                <tr>
                                                    <td><?=$no ++ ?></td>
                                                    <td><?=$value_detail['nama_produk'] ?></td>
                                                    <td><?=number_format($value_detail['jumlah_terjual']) ?></td>
                                                    <td><?=number_format($value_detail['harga_beli']) ?></td>
                                                    <td><?=number_format($value_detail['harga_jual']) ?></td>
                                                    <td><?=number_format($omset) ?></td>
                                                </tr>
                                            <?php } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="text-center" colspan="2">Total</th>
                                            <th><?=number_format($total_jumlah_terjual) ?></th>
                                            <th><?=number_format($total_harga_beli) ?></th>
                                            <th><?=number_format($total_harga_jual) ?></th>
                                            <th><?=number_format($total_omset) ?></th>
                                        </tr>
                                    </tfoot>
                                        <?php endif; ?>
                                </table>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php tanpaData: ?>
    </div>
</section>

 <!-- sweet alert -->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<!--<script src="//cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>-->
<script src="plugins/select2/js/select2.full.min.js"></script>

<script type="text/javascript">
  //Initialize Select2 Elements
  $('.select2bs4').select2({
    theme: 'bootstrap4'
  })
</script>

<script src="<?=base_url()?>plugins/export-excel/src/jquery.table2excel.js"></script>

<script type="text/javascript">
    $('#btn_excpot').click(function(){
        var name_element = document.getElementById('namafile').value;
        $(".table2excel").table2excel({
            exclude: ".noExl",
            name: "Excel Document Name",
            filename: name_element,
            fileext: ".xls",
            exclude_img: true,
            exclude_links: true,
            exclude_inputs: true
        });
        // window.onfocus=function(){ setTimeout(function () { window.close(); }, 500); }
    });
</script>


<script type="text/javascript">

  $(function () {
    $('#daterange-btn').daterangepicker(
    {
      ranges   : {
        'Hari Ini'  : [moment(), moment()],
        'Bulan Ini' : [moment().startOf('month'), moment().endOf('month')],
        'Tahun Ini' : [moment().startOf('year'), moment().endOf('year')]
      },
      startDate     : '<?= date("m/d/Y", strtotime($tgl_awal)) ?>',
      endDate       : '<?= date("m/d/Y", strtotime($tgl_akhir)) ?>'
    },
    function (start, end) {
      $('#reportrange').html(start.format('D MMMM YYYY') + ' - ' + end.format('D MMMM YYYY'));
      // var form = $('<form action="?page=penjualan&action=perproduk" method="post">' +
      //   '<input type="hidden" name="tgl_awal" value="' + start.format('YYYY-MM-D') + '" />' +
      //   '<input type="hidden" name="tgl_akhir" value="' + end.format('YYYY-MM-D') + '" />' +
      //   '</form>');
      // $('body').append(form);
      // form.submit();
    }
    )
  })

  function terapkan(){
    var tanggal     = document.getElementById('reportrange').innerHTML.split(" - ");
    var filer1      = document.getElementById('filer1').value;
    var id_supplier = document.getElementById('id_supplier').value;
    
    var start_master= new Date(tanggal[0]);
    var start       = start_master.getFullYear() + "-" + String(start_master.getMonth() + 1) + "-" + String(start_master.getDate());

    var end_master = new Date(tanggal[1]);
    var end        = end_master.getFullYear() + "-" + String(end_master.getMonth() + 1) + "-" + String(end_master.getDate());

    // alert(start + ' aaaa ' + end);

    var form = $('<form action="?page=penjualan&action=supplier" method="post">' +
      '<input type="hidden" name="tgl_awal" value="' + start + '" />' +
      '<input type="hidden" name="tgl_akhir" value="' + end + '" />' +
      '<input type="hidden" name="filer1" value="' + filer1 + '" />' +
      '<input type="hidden" name="supplier" value="' + id_supplier + '" />' +
      '</form>');
    $('body').append(form);
    form.submit();
  }

  function exportexcel(awal, akhir, filer1, supplier){
    var form = $('<form target="_blank" action="view/laporan/lap_penjualan_supplier_export_excel.php" method="post">' +
      '<input type="hidden" name="tgl_awal" value="' + awal + '" />' +
      '<input type="hidden" name="tgl_akhir" value="' + akhir + '" />' +
      '<input type="hidden" name="filer1" value="' + filer1 + '" />' +
      '<input type="hidden" name="supplier" value="' + supplier + '" />' +
      '</form>');
    $('body').append(form);
    // alert(awal+' - '+akhir+' '+filer+' '+supplier+'')
    form.submit();
    // alert(awal+'    '+akhir);
  }
</script>
                            


 

<!-- <input type="hidden" name="namafile" id="namafile" value="<?= 'Laporan Closing Stall '.tanggal_indo(@$_GET['tanggal']) ?>"> -->

<!-- <script type="text/javascript">
  $('#btn_excel').click(function(){ 

    var name_element = document.getElementById('namafile').value;
    $(".table2excel").table2excel({
      exclude: ".noExl",
      name: "Excel Document Name",
      filename: name_element,
      fileext: ".xls",
      exclude_img: true,
      exclude_links: true,
      exclude_inputs: true
    });
    // window.onfocus=function(){ setTimeout(function () { window.close(); }, 500); }
  });
</script> -->
                         
                            
