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


if(isset($_POST['tgl_dipilih'])){
    $tanggal = $_POST['tgl_dipilih'];
}else{
    $tanggal = $tgl_akhir;
}

$list_stall = $db->query("SELECT * FROM merchant WHERE status_aktif_merchant='Y' ")->fetch_all(MYSQLI_ASSOC);

if($filer1 == 'all'){
  $stall_dipilih = $list_stall;
}else{
  $id_merchant = enkripsiDekripsi($filer1, 'dekripsi');
  $stall_dipilih = $db->query("SELECT * FROM merchant WHERE status_aktif_merchant='Y' AND id_merchant='$id_merchant'")->fetch_all(MYSQLI_ASSOC);
  // $stall_dipilih[0]['id_merchant'] = $filer1;  
}

$data = [];

foreach ($stall_dipilih as $key_stall => $value_stall) {
        
    $list_produk = $db->query("SELECT A.id_merchant_produk, A.kd_merchant, A.kode_produk, A.jenis_produk, A.nama_produk, A.harga_produk AS harga_jual FROM merchant_produk A LEFT JOIN merchant_transaksi_detail B ON A.id_merchant_produk=B.kd_merchant_produk WHERE A.kd_merchant='$value_stall[id_merchant]' AND A.jenis_produk='2' AND (DATE(B.tgl_input_detail)='$tanggal' OR DATE(B.tgl_input_detail) IS NULL) AND ( B.status_transaksi_detail IS NOT NULL OR (B.status_transaksi_detail IS NULL AND A.status_remove_produk='N') ) GROUP BY A.id_merchant_produk")->fetch_all(MYSQLI_ASSOC);;
    
    $data[$value_stall['id_merchant']]['nama_stall'] = $value_stall['nama_merchant'];
    
    foreach($list_produk as $key_produk => $value_produk){
        
        $keluar_masuk = $db->query("SELECT COALESCE(SUM(A.masuk),0) AS stok_masuk, COALESCE(SUM(A.keluar),0) AS stok_keluar FROM merchant_history_stok A WHERE DATE(A.tanggal_history)='$tanggal' AND A.kd_merchant_produk='$value_produk[id_merchant_produk]'")->fetch_assoc();
        
        $stok_akhir = $db->query("SELECT A.stok_setelah, A.harga_beli FROM merchant_history_stok A WHERE DATE(A.tanggal_history)<='2021-02-26' AND A.kd_merchant_produk='$value_produk[id_merchant_produk]' ORDER BY A.tanggal_history DESC LIMIT 1")->fetch_assoc();
        
        $stok_awal = $db->query("SELECT A.stok_sebelum FROM merchant_history_stok A WHERE DATE(A.tanggal_history)='2021-02-26' AND A.kd_merchant_produk='$value_produk[id_merchant_produk]' ORDER BY A.tanggal_history ASC LIMIT 1")->fetch_assoc();
        if(empty($stok_awal)){
            $stok_awal = $db->query("SELECT A.stok_sebelum FROM merchant_history_stok A WHERE DATE(A.tanggal_history)<'2021-02-26' AND A.kd_merchant_produk='$value_produk[id_merchant_produk]' ORDER BY A.tanggal_history DESC LIMIT 1")->fetch_assoc();
        }
        
        $perproduk = $db->query("SELECT A.stok_sebelum, A.masuk, A.keluar, A.stok_setelah, A.harga_beli FROM merchant_history_stok A WHERE DATE(A.tanggal_history)='$tanggal' AND A.kd_merchant_produk='$value_produk[id_merchant_produk]' ORDER BY A.id_merchant_history_stok DESC LIMIT 1 ")->fetch_assoc();
        
        if(empty($perproduk)){
            $perproduk = $db->query("SELECT A.stok_sebelum, A.masuk, A.keluar, A.stok_setelah, A.harga_beli FROM merchant_history_stok A WHERE DATE(A.tanggal_history)<'$tanggal' AND A.kd_merchant_produk='$value_produk[id_merchant_produk]' ORDER BY A.id_merchant_history_stok DESC LIMIT 1 ")->fetch_assoc();
        }
        
        $data[$value_stall['id_merchant']]['produk'][$key_produk]['kode_produk'] = $value_produk['kode_produk'];
        $data[$value_stall['id_merchant']]['produk'][$key_produk]['nama_produk'] = $value_produk['nama_produk'];
        $data[$value_stall['id_merchant']]['produk'][$key_produk]['harga_beli'] = $stok_akhir['harga_beli'];
        $data[$value_stall['id_merchant']]['produk'][$key_produk]['stok_awal'] = $stok_awal['stok_sebelum'];
        $data[$value_stall['id_merchant']]['produk'][$key_produk]['masuk'] = $keluar_masuk['stok_masuk'];
        $data[$value_stall['id_merchant']]['produk'][$key_produk]['keluar'] = $keluar_masuk['stok_keluar'];
        $data[$value_stall['id_merchant']]['produk'][$key_produk]['stok_setelah'] = $stok_akhir['stok_setelah'];
        $data[$value_stall['id_merchant']]['produk'][$key_produk]['nilai_stok_akhir'] = $stok_akhir['stok_setelah']*$stok_akhir['harga_beli'];
        
    }
    
}



?>
<link rel="stylesheet" href="plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Rekap Keluar Masuk Persediaan Per Stall</h1>
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
            <form action="?page=persediaan&action=keluarmasukpersediaan" method="POST">
                <div class="form-group row">
                  <label class="col-sm-1 col-form-label">Periode :</label>
                  <div class="col-sm-11">
                    <input type="date" name="tgl_dipilih" class="form-control" value="<?= $tanggal ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-1 col-form-label">Stall :</label>
                  <div class="col-sm-11">
                    <?php  $list_merchant = $db->query(" SELECT a.id_merchant, a.nama_merchant
                                            FROM merchant a ")->fetch_all(MYSQLI_ASSOC) ?>
                    <select name="filer1" class="select2bs4 form-control" id="filer1" style="width: 100%" required>
                            <option value="all">SEMUA STALL</option>
                        <?php foreach($list_merchant as $key => $val ) { ?>
                            <option value="<?= enkripsiDekripsi($val['id_merchant'], 'enkripsi'); ?>"> <?=$val['nama_merchant']?></option>
                        <?php } ?>
                    </select>
                  </div>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%">Terapkan</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <!-- <a onclick="exportexcel('<?= $tgl_awal ?>', '<?= $tgl_akhir ?>')">
              <button type="button" class="btn btn-default" style="width: 100%"><i class="fa fa-fw fa-file-download"></i>Eksport Excel</button>
            </a> -->
          </div>
          
            <?php if(empty($stall_dipilih)): ?>
                <div class="card-body text-center">Harap pilih stall dan tanggal terlebih dahulu</div>
            <?php endif; ?>
          
            <?php foreach($data as $key_stall => $value_stall): ?>
            
              <div class="card-body">
                <h5><?= $value_stall['nama_stall'] ?></h5>
                <?php if(empty($value_stall['produk'])) : ?>
                    <br>Tidak ada data<br>
                <?php else : ?>
                    
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>Kode</th>
                          <th>Nama Barang</th>
                          <th>Harga Beli</th>
                          <th>Stok Awal</th>
                          <th>Masuk</th>
                          <th>Keluar</th>
                          <th>Stok Akhir</th>
                          <th>Nilai Stok Akhir</th>
                        </tr>
                      </thead>
                      <tbody>
        
                            <?php $total_stok_awal = 0 ?>
                            <?php $total_masuk = 0 ?>
                            <?php $total_keluar = 0 ?>
                            <?php $total_stok_akhir = 0 ?>
                            <?php $total_nilai_stok_akhir = 0 ?>
                            <?php foreach($value_stall['produk'] as $key_produk => $value_produk): ?>
                                <?php $total_stok_awal += $value_produk['stok_awal'] ?>
                                <?php $total_masuk += $value_produk['masuk'] ?>
                                <?php $total_keluar += $value_produk['keluar'] ?>
                                <?php $total_stok_akhir += $value_produk['stok_setelah'] ?>
                                <?php $total_nilai_stok_akhir += $value_produk['nilai_stok_akhir'] ?>
                              <tr>
                                <td> <?php echo $value_produk['kode_produk']; ?> </td>
                                <td> <?php echo $value_produk['nama_produk']; ?> </td>
                                <td> <?php echo number_format($value_produk['harga_beli']); ?> </td>
                                <td> <?php echo number_format($value_produk['stok_awal']); ?> </td>
                                <td> <?php echo number_format($value_produk['masuk']); ?> </td>
                                <td> <?php echo number_format($value_produk['keluar']); ?> </td>
                                <td> <?php echo number_format($value_produk['stok_setelah']); ?> </td>
                                <td> <?php echo number_format($value_produk['nilai_stok_akhir']); ?> </td>
                              </tr>
                            <?php endforeach; ?>
                      </tbody>
                      <tfoot>
                        <tr>
                          <td colspan="3" style='font-weight: bold;'>Total</td>
                          <td style='font-weight: bold;'> <?php echo number_format($total_stok_awal); ?> </td>
                          <td style='font-weight: bold;'> <?php echo number_format($total_masuk); ?> </td>
                          <td style='font-weight: bold;'> <?php echo number_format($total_keluar); ?> </td>
                          <td style='font-weight: bold;'> <?php echo number_format($total_stok_akhir); ?> </td>
                          <td style='font-weight: bold;'> <?php echo number_format($total_nilai_stok_akhir); ?> </td>
                          <td> </td>
                        </tr>
                      </tfoot>
                    </table>
                <?php endif; ?>
              </div>
              
            <?php endforeach; ?>
          
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
      // var form = $('<form action="?page=penjualan&action=perkasir" method="post">' +
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

    var form = $('<form action="?page=persediaan&action=keluarmasukpersediaan" method="post">' +
      '<input type="hidden" name="tgl_awal" value="' + start + '" />' +
      '<input type="hidden" name="tgl_akhir" value="' + end + '" />' +
      '<input type="hidden" name="filer1" value="' + filer1 + '" />' +
      '</form>');
    $('body').append(form);
    form.submit();
  }

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