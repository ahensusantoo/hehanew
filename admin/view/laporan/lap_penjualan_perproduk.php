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

$list_stall = $db->query("SELECT * FROM merchant WHERE status_aktif_merchant='Y' ")->fetch_all(MYSQLI_ASSOC);

if($filer1 == 'all'){
  $stall_dipilih = $list_stall;
}else{
  $id_merchant = enkripsiDekripsi($filer1, 'dekripsi');
  $stall_dipilih = $db->query("SELECT * FROM merchant WHERE status_aktif_merchant='Y' AND id_merchant='$id_merchant'")->fetch_all(MYSQLI_ASSOC);
  // $stall_dipilih[0]['id_merchant'] = $filer1;  
}




?>

<link rel="stylesheet" href="plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
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
              <label class="col-sm-1 col-form-label">Periode :</label>
              <div class="col-sm-11">
                <button type="button" class="btn btn-default" id="daterange-btn" style="width: 100%">
                  <i class="far fa-calendar-alt"></i> <span id="reportrange"><?= date('j F Y', strtotime($tgl_awal)).' - '.date('j F Y', strtotime($tgl_akhir)); ?></span>
                  <i class="fas fa-caret-down"></i>
                </button>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-1 col-form-label">Stall :</label>
              <div class="col-sm-11">
                <select name="filer1" class="select2bs4 form-control" id="filer1" style="width: 100%" required>
                    <option value="all">SEMUA STALL</option>
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
          <div class="card-header">
            <a onclick="exportexcel('<?= $tgl_awal ?>', '<?= $tgl_akhir ?>', '<?= $filer1 ?>')">
              <button type="button" class="btn btn-default" style="width: 100%"><i class="fa fa-fw fa-file-download"></i>Eksport Excel</button>
            </a>
          </div>
          <div class="card-body">

            <?php if ($filer1 == ""): ?>
              <br><br><center>Harap Pilih Stall dan Periode</center>
              <?php goto tanpa_data; ?> 
            <?php endif ?>
            
            <?php foreach ($stall_dipilih as $key_awal => $value): ?>

              <?php  
                $query = "SELECT 
    
                        B.nama_produk, A.harga_produk, A.harga_setelah_diskon,
                        
                        (SELECT SUM(C.jumlah_produk) FROM merchant_transaksi_detail C WHERE C.kd_merchant_produk=A.kd_merchant_produk AND DATE(C.tgl_input_detail) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND C.status_transaksi_detail='2' AND C.harga_produk=A.harga_produk ) AS jumlah
                        
                        FROM merchant_transaksi_detail A JOIN merchant_produk B ON A.kd_merchant_produk=B.id_merchant_produk JOIN merchant_transaksi D ON A.kd_merchant_transaksi=D.id_merchant_transaksi WHERE A.status_transaksi_detail='2' AND D.kd_jenis_pembayaran!='' AND DATE(A.tgl_input_detail) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND A.kd_merchant='$value[id_merchant]' GROUP BY A.kd_merchant_produk , A.harga_produk  
                        ";
                $list_produk = $db->query($query)->fetch_all(MYSQLI_ASSOC);

                $total_jumlah = 0;
                foreach ($list_produk as $a => $b) {
                  $total_jumlah += $b['jumlah'];
                }
                
                $diskon = @$db->query("SELECT SUM((A.harga_produk-A.harga_setelah_diskon)*A.jumlah_produk) AS jml FROM merchant_transaksi_detail A JOIN merchant_transaksi B ON A.kd_merchant_transaksi=B.id_merchant_transaksi WHERE 
                    B.kd_jenis_pembayaran!='' AND A.kd_merchant='$value[id_merchant]'
                    AND DATE(A.tgl_input_detail) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND A.status_transaksi_detail='2'")->fetch_assoc()['jml'];
                    
                if($diskon == ""){
                    $diskon = 0;
                }

              ?>
                
                
              <table id="" class="table table-bordered table-striped mb-4 table-sm">
                <thead>
                  <tr>
                    <th colspan="6"><?= $value['nama_merchant'] ?></th>
                  </tr>
                  <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th>%</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $total_persen = 0 ?>
                  <?php $total_perstall[$key_awal] = 0 ?>
                  <?php foreach ($list_produk as $key => $value): ?>
                    <tr>
                      <?php  
                        $persen = ($value['jumlah']/$total_jumlah)*100;
                        $total_persen += $persen;
                        $subtotal = $value['jumlah'] * $value['harga_produk'];
                        $total_perstall[$key_awal] += $subtotal
                      ?>
                      <td> <?php echo $key+1; ?> </td>
                      <td> <?php echo $value['nama_produk']; ?> </td>
                      <td> <?php echo number_format($value['harga_produk']); ?> </td>
                      <td> <?php echo number_format($value['jumlah']); ?> </td>
                      <td> <?php echo number_format($value['jumlah'] * $value['harga_produk']); ?> </td>
                      <td> <?= round($persen, 2) ?>%</td>
                    </tr>
                  <?php endforeach ?>
                  <tr>
                    <td colspan="3" align="right">DISKON</td>
                    <td></td>
                    <td><?= number_format($diskon) ?></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td colspan="3" align="right">TOTAL</td>
                    <td><?= number_format($total_jumlah) ?></td>
                    <td><?= number_format($total_perstall[$key_awal] - $diskon) ?></td>
                    <td><?= ($total_persen) ?>%</td>
                  </tr>
                </tbody>
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
  $(function () {
    $('#daterange-btn').daterangepicker(
    {
      ranges   : {
        'Hari Ini' : [moment(), moment()],
        'Bulan Ini' : [moment().startOf('month'), moment().endOf('month')],
        'Tahun Ini' : [moment().startOf('year'), moment().endOf('year')]
      },
      startDate : '<?= date("m/d/Y", strtotime($tgl_awal)) ?>',
      endDate : '<?= date("m/d/Y", strtotime($tgl_akhir)) ?>'
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
    var tanggal = document.getElementById('reportrange').innerHTML.split(" - ");
    var filer1 = document.getElementById('filer1').value;

    var start_master = new Date(tanggal[0]);
    var start = start_master.getFullYear() + "-" + String(start_master.getMonth() + 1) + "-" + String(start_master.getDate());

    var end_master = new Date(tanggal[1]);
    var end = end_master.getFullYear() + "-" + String(end_master.getMonth() + 1) + "-" + String(end_master.getDate());

    // alert(start + ' aaaa ' + end);

    var form = $('<form action="?page=penjualan&action=perproduk" method="post">' +
      '<input type="hidden" name="tgl_awal" value="' + start + '" />' +
      '<input type="hidden" name="tgl_akhir" value="' + end + '" />' +
      '<input type="hidden" name="filer1" value="' + filer1 + '" />' +
      '</form>');
    $('body').append(form);
    form.submit();
  }

  function exportexcel(awal, akhir, filer1){
    var form = $('<form target="_blank" action="view/laporan/lap_penjualan_perproduk_export_excel.php" method="post">' +
      '<input type="hidden" name="tgl_awal" value="' + awal + '" />' +
      '<input type="hidden" name="tgl_akhir" value="' + akhir + '" />' +
      '<input type="hidden" name="filer1" value="' + filer1 + '" />' +
      '</form>');
    $('body').append(form);
    alert(awal+' - '+akhir+''+filer1+'')
    form.submit();
    // alert(awal+'    '+akhir);
  }
</script>