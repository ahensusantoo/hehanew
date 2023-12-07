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



$tahun_awal = substr($tgl_awal,0,4);
$tahun_akhir = substr($tgl_akhir,0,4);

$bulan_awal = substr($tgl_awal,5,2);
$bulan_akhir = substr($tgl_akhir,5,2);

$tanggal_awal = substr($tgl_awal,8,2);
$tanggal_akhir = substr($tgl_akhir,8,2);

$full_date_awal = $tahun_awal."-".$bulan_awal."-01";
$full_date_akhir = $tahun_akhir."-".$bulan_akhir."-30";




$list_stall = $db->query("SELECT * FROM merchant WHERE status_aktif_merchant='Y' ")->fetch_all(MYSQLI_ASSOC);


if($filer1 == 'all'){
  $stall_dipilih = $list_stall;
}else{
  $id_merchant = enkripsiDekripsi($filer1, 'dekripsi');
  $stall_dipilih = $db->query("SELECT * FROM merchant WHERE status_aktif_merchant='Y' AND id_merchant='$id_merchant'")->fetch_all(MYSQLI_ASSOC);
  // $stall_dipilih[0]['id_merchant'] = $filer1;  
}


foreach ($stall_dipilih as $key => $value) {
  $id_merchant = $value['id_merchant'];

  $list_produk = $db->query("SELECT A.kd_merchant_produk, B.nama_produk, A.harga_produk FROM merchant_transaksi_detail A JOIN merchant_produk B ON A.kd_merchant_produk=B.id_merchant_produk JOIN merchant_transaksi C ON A.kd_merchant_transaksi=C.id_merchant_transaksi WHERE A.status_transaksi_detail='2' AND C.kd_jenis_pembayaran!='' AND B.kd_merchant='$id_merchant' AND DATE(A.tgl_input_detail) BETWEEN '$full_date_awal' AND '$full_date_akhir' GROUP BY A.kd_merchant_produk,A.harga_produk")->fetch_all(MYSQLI_ASSOC);

  $start = $month = strtotime($tgl_awal);
  $end = strtotime($tgl_akhir);
  while($month <= $end)
  { 

    $tahun_loop =  date('Y', $month);
    $bulan_loop =  date('m', $month);

    foreach ($list_produk as $key2 => $value_perproduk) {
      
      $result_perproduk = @$db->query("SELECT SUM(A.jumlah_produk) AS jml FROM merchant_transaksi_detail A JOIN merchant_transaksi B ON A.kd_merchant_transaksi=B.id_merchant_transaksi WHERE B.kd_jenis_pembayaran!='' AND A.kd_merchant='$id_merchant' AND A.kd_merchant_produk='$value_perproduk[kd_merchant_produk]' AND A.harga_produk='$value_perproduk[harga_produk]' AND A.status_transaksi_detail='2' AND YEAR(A.tgl_input_detail)='$tahun_loop' AND MONTH(A.tgl_input_detail)='$bulan_loop'")->fetch_assoc()['jml'];
      if ($result_perproduk == "") {
        $result_perproduk =0;
      }

      $data[$id_merchant]['id_merchant'] = $id_merchant;
      $data[$id_merchant]['nama_merchant'] = $value['nama_merchant'];
      $data[$id_merchant]['produk'] = $list_produk;

      $subdata['nama_produk'] = $value_perproduk['nama_produk'];
      $subdata['harga'] = $value_perproduk['harga_produk'];
      $subdata['terjual'] = $result_perproduk;

      $data[$id_merchant]['periode'][$bulan_loop."/".$tahun_loop][$value_perproduk['kd_merchant_produk']."".$value_perproduk['harga_produk']] = $subdata;

    }

    $month = strtotime("+1 month", $month);

  }
}


?>
<link rel="stylesheet" href="plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<style type="text/css">
  .selectMonths{ position:relative; display:inline-block; }
  .selectMonthsselect {height: 30px; }
  .selectMonths > i{ position:absolute; right:5px; top:5px; opacity:0.35; font-style:normal; font-size:18px; transition:0.2s; pointer-events:none; }
  .selectMonths > input{ text-transform:capitalize; padding-left:10px; cursor:default; cursor:pointer; }
  .selectMonths:hover > i{ opacity:.7; }
  .selectMonths + .selectMonths{ float:none; }
</style>
<link rel="stylesheet" href="plugins/month-range2/picker.css">

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-12">
        <h1 class="m-0 text-dark">Perbandingan Produk Bulan</h1>
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
              <label class="col-sm-1 col-form-label">Periode :</label>
              <div class="col-sm-11">
                <div class="selectMonths" style="width: 100% !important">
                  <input type="text" class="btn btn-default float-left" value="" readonly style="width: 100% !important" />
                  <i>&#128197;</i>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-1 col-form-label">Stall :</label>
              <div class="col-sm-11">
                <select name="filer1" class="select2bs4 form-control" id="filer1" style="width: 100%" required>
                  <!-- <option value="all">SEMUA STALL</option> -->
                    <?php foreach ($list_stall as $bulan => $value): ?>
                        <?php $id_stall_encryp = enkripsiDekripsi($value['id_merchant'],'enkripsi'); ?>
                        <option value="<?= $id_stall_encryp ?>" <?php if($filer1 == $id_stall_encryp){echo 'selected';} ?>> <?= strtoupper($value['nama_merchant']) ?></option>
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


    <div class="row">
      <div class="col-12">
        <div class="card">
          <!-- <div class="card-header">
            <a onclick="exportexcel('<?= $tgl_awal ?>', '<?= $tgl_akhir ?>')">
              <button type="button" class="btn btn-default" style="width: 100%"><i class="fa fa-fw fa-file-download"></i>Eksport Excel</button>
            </a>
          </div> -->
          <div class="card-header">
            <!-- <a onclick="exportexcel('<?= $tgl_awal ?>', '<?= $tgl_akhir ?>')"> -->
              <button type="button" id="btn_excel" class="btn btn-default" style="width: 100%"><i class="fa fa-fw fa-file-download"></i>Eksport Excel</button>
            <!-- </a> -->
          </div>

          <div class="card-body">

            <?php if (empty($data)): ?>
              <p class="mt-3 text-center">Tidak ada data, harap pilih stall dan periode</p>
              <?php goto tanpa_data; ?>
            <?php endif ?>




            <?php foreach ($data as $key_merchant => $value_permerchant): ?>
              <input type="hidden" name="namafile" id="namafile" value="<?= 'Laporan Perbadingan 3 Bulan Produk'.$value_permerchant['nama_merchant'].' '.tanggal_indo($tgl_awal).' - '.tanggal_indo($tgl_akhir); ?>">
              <label><?= $value_permerchant['nama_merchant'] ?></label>
              <table id="" class="table table-bordered table-striped table-sm table2excel">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <?php foreach ($data[$key_merchant]['periode'] as $periode => $value_periode): ?>
                      <th><?= $periode ?></th>
                    <?php endforeach ?>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($data[$key_merchant]['produk'] as $key => $value): ?>
                    <tr>
                      <td><?= $key+1 ?></td>
                      <td><?= $value['nama_produk'] ?></td>
                      <td><?= number_format($value['harga_produk']) ?></td>
                      <?php foreach ($data[$key_merchant]['periode'] as $periode => $value_periode): ?>
                        <td><?= $value_periode[$value['kd_merchant_produk'].$value['harga_produk']]['terjual'] ?></td>
                        <?php  
                          if (isset($total_jml[$key_merchant][$periode])) {
                            $total_jml[$key_merchant][$periode] += $value_periode[$value['kd_merchant_produk'].$value['harga_produk']]['terjual']; 
                          }else{
                            $total_jml[$key_merchant][$periode] = $value_periode[$value['kd_merchant_produk'].$value['harga_produk']]['terjual'];
                          }
                        ?>
                      <?php endforeach ?>
                    </tr>
                  <?php endforeach ?>
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="3">TOTAL</td>
                    <?php foreach ($data[$key_merchant]['periode'] as $periode => $value_periode): ?>
                      <th><?= $total_jml[$key_merchant][$periode] ?></th>
                    <?php endforeach ?>
                  </tr>
                </tfoot>
              </table>
            <?php endforeach ?>

            <?php tanpa_data: ?>
          </div>
        </div>
      </div>
    </div>



    
  </div>
</section>

<script src="plugins/select2/js/select2.full.min.js"></script>

<script type="text/javascript">
  //Initialize Select2 Elements
  $('.select2bs4').select2({
    theme: 'bootstrap4'
  })
</script>

<script type="text/javascript">
  function exportexcel(awal, akhir){
    var form = $('<form target="_blank" action="view/laporan/lap_tiket_export_excel.php" method="post">' +
      '<input type="hidden" name="awal" value="' + awal + '" />' +
      '<input type="hidden" name="akhir" value="' + akhir + '" />' +
      '</form>');
    $('body').append(form);
    form.submit();
    // alert(awal+'    '+akhir);
  }
</script>

<script src="plugins/month-range2/tether.min.js"></script>
<script src="plugins/month-range2/datePicker.js"></script>
<script>
  window.global_date_start = <?= date('Y', strtotime($tgl_awal))?>+'-'+<?=date('m', strtotime($tgl_awal))?>+'-'+<?=date('d', strtotime($tgl_awal)) ?>;
  window.global_date_end = <?= date('Y', strtotime($tgl_akhir))?>+'-'+<?=date('m', strtotime($tgl_akhir))?>+'-'+<?=date('d', strtotime($tgl_akhir)) ?>;

  $('.selectMonths:first input')
  .rangePicker({ RTL:false, setDate:[[<?= date('m', strtotime($tgl_awal)) ?>,<?= date('Y', strtotime($tgl_awal)) ?>],[<?= date('m', strtotime($tgl_akhir)) ?>,<?= date('Y', strtotime($tgl_akhir)) ?>]], closeOnSelect:true })
  .on('datePicker.done', function(e, result){
    // if( result instanceof Array )
    //   console.log(new Date(result[0][1], result[0][0] - 1), new Date(result[1][1], result[1][0] - 1));
    // else
    //   console.log(result);

    var start_master = new Date(result[0][1], result[0][0] - 1);
    var start = start_master.getFullYear() + "-" + String(start_master.getMonth() + 1).padStart(1, '0') + "-1";
    window.global_date_start = start;

    var end_master = new Date(result[1][1], result[1][0] - 1);
    var end = end_master.getFullYear() + "-" + String(end_master.getMonth() + 1).padStart(1, '0') + "-1";
    window.global_date_end = end;

    // alert(start + end);
    // var form = $('<form action="?page=penjualan&action=banding3bulan" method="post">' +
    //   '<input type="hidden" name="tgl_awal" value="' + start + '" />' +
    //   '<input type="hidden" name="tgl_akhir" value="' + end + '" />' +
    //   '</form>');
    // $('body').append(form);
    // form.submit();
  });

  function terapkan(){  
    var filer1 = document.getElementById('filer1').value;
    
    var form = $('<form action="?page=penjualan&action=banding3bulan" method="post">' +
      '<input type="hidden" name="tgl_awal" value="' + window.global_date_start + '" />' +
      '<input type="hidden" name="tgl_akhir" value="' + window.global_date_end + '" />' +
      '<input type="hidden" name="filer1" value="' + filer1 + '" />' +
      '</form>');
    $('body').append(form);
    form.submit();
  }

</script>


<script src="<?=base_url()?>plugins/export-excel/src/jquery.table2excel.js"></script>

<!-- <input type="hidden" name="namafile" id="namafile" value="<?= 'Laporan Closing Stall '.tanggal_indo(@$_GET['tanggal']) ?>"> -->

<script type="text/javascript">
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
</script>
