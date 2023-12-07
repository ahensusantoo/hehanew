<?php
  include("../../templates/koneksi.php");

  
  header("Content-type: application/vnd-ms-excel");
  header("Content-Disposition: attachment; filename=Laporan Ringkasan Harian.xls");

  

  if(empty($_POST['tgl_awal']) || empty($_POST['tgl_akhir'])){
    $tgl_awal = date('Y-m-d');
    $tgl_akhir = date('Y-m-d');
  } else {
    $tgl_awal = date('Y-m-d', strtotime($_POST['tgl_awal']));
    $tgl_akhir = date('Y-m-d', strtotime($_POST['tgl_akhir']));
  }

$start = $month = strtotime($tgl_awal);
$end = strtotime($tgl_akhir);
$key = 0;
while($month <= $end){

    $tgl_loop =  date('Y-m-d', $month);

    $data[$key]['tgl_input_detail'] = $tgl_loop;

    $data[$key]['jml_merchant'] = $db->query("SELECT COUNT(DISTINCT B.kd_merchant)  AS jml_merchant FROM merchant_transaksi_detail B WHERE DATE(B.tgl_input_detail)='$tgl_loop' AND B.status_transaksi_detail='2'")->fetch_assoc()['jml_merchant'];

    $data[$key]['jml_terjual'] = $db->query("SELECT SUM(C.jumlah_produk) AS jml_terjual FROM merchant_transaksi_detail C WHERE DATE(C.tgl_input_detail)='$tgl_loop' AND C.status_transaksi_detail='2'")->fetch_assoc()['jml_terjual'];

    $data[$key]['omset_bruto'] = $db->query("SELECT COALESCE(SUM(A.harga_produk*A.jumlah_produk), 0) AS omset_bruto FROM merchant_transaksi_detail A JOIN merchant_transaksi B ON A.kd_merchant_transaksi=B.id_merchant_transaksi WHERE DATE(A.tgl_input_detail)='$tgl_loop' AND A.status_transaksi_detail='2' AND A.kd_merchant!='' AND B.kd_jenis_pembayaran!='' AND B.status_transaksi='2' AND B.kd_shift!=''")->fetch_assoc()['omset_bruto'];

    $diskon_perbarang = $db->query("SELECT COALESCE(SUM((A.harga_produk-A.harga_setelah_diskon)*A.jumlah_produk), 0) AS diskon_perbarang FROM merchant_transaksi_detail A JOIN merchant_transaksi B ON A.kd_merchant_transaksi=B.id_merchant_transaksi WHERE DATE(A.tgl_input_detail)='$tgl_loop' AND A.status_transaksi_detail='2' AND A.kd_merchant!='' AND B.kd_jenis_pembayaran!='' AND B.status_transaksi='2' AND B.kd_shift!=''")->fetch_assoc()['diskon_perbarang'];
    $data[$key]['diskon_perbarang'] = $diskon_perbarang;
                
    $diskon_transaksi = $db->query("SELECT COALESCE(SUM(A.diskon),0) AS diskon_transaksi FROM merchant_transaksi A WHERE A.kd_merchant!='' AND A.status_transaksi='2' AND DATE(A.tgl_input_transaksi)='$tgl_loop' AND A.kd_jenis_pembayaran!='' AND A.kd_shift!=''")->fetch_assoc()['diskon_transaksi'];
    $data[$key]['diskon_transaksi'] = $diskon_transaksi;

    $total_diskon = $diskon_perbarang + $diskon_transaksi;
    $data[$key]['total_diskon'] = $total_diskon;

    $data[$key]['omset_bersih'] = $data[$key]['omset_bruto'] - $total_diskon;

    
    $key++;
    $month = strtotime("+1 day", $month);
    
}

?>


<style type="text/css">
  table{
    margin: 20px auto;
    border-collapse: collapse;
  }
  table th,
  table td{
    border: 1px solid #3c3c3c;
    padding: 3px 8px;
    font-size: 80%;
  }
</style>


<table>
  <thead>
    <tr>
      <th colspan="4" style="text-align: center;">Laporan Penjualan Ringkasan Harian</th>
    </tr>
    <tr><th colspan="4" style="text-align: center;">Periode <?= $tgl_awal ?> s/d <?= $tgl_akhir ?> </th></tr>
    <tr><th></th></tr>
  </thead>
</table>


<table class="table table-bordered table-striped" border="1">
  <thead>
    <tr>
      <th>Tanggal</th>
      <th>Jumlah Stall</th>
      <th>Jumlah Terjual</th>
      <th>Omset Bruto</th>
      <th>Diskon Produk</th>
      <th>Diskon Transaksi</th>
      <th>Omset Bersih</th>
    </tr>
  </thead>
  <tbody>
    <?php $jml_terjual  = 0 ?>
    <?php $omset_bruto  = 0 ?>
    <?php $diskon_perbarang= 0 ?>
    <?php $diskon_transaksi= 0 ?>
    <?php $omset_bersih = 0 ?>
    <?php foreach ($data as $key => $value): ?>
      <?php $jml_terjual          += $value['jml_terjual'] ?>
      <?php $omset_bruto          += $value['omset_bruto'] ?>
      <?php $diskon_perbarang     += $value['diskon_perbarang'] ?>
      <?php $diskon_transaksi     += $value['diskon_transaksi'] ?>
      <?php $omset_bersih         += $value['omset_bersih'] ?>
      <tr>
        <td><?= tanggal_indo($value['tgl_input_detail']) ?></td>
        <td><?= $value['jml_merchant'] ?></td>
        <td><?= $value['jml_terjual'] ?></td>
        <td><?= $value['omset_bruto'] ?></td>
        <td><?= $value['diskon_perbarang'] ?></td>
        <td><?= $value['diskon_transaksi'] ?></td>
        <td><?= $value['omset_bersih'] ?></td>
      </tr>
    <?php endforeach ?>
  </tbody>
  <tfoot>
    <tr>
      <td>TOTAL</td>
      <td> </td>
      <td> <?php echo ($jml_terjual); ?> </td>
      <td> <?php echo ($omset_bruto); ?> </td>
      <td> <?php echo ($diskon_perbarang); ?> </td>
      <td> <?php echo ($diskon_transaksi); ?> </td>
      <td> <?php echo ($omset_bersih); ?> </td>
    </tr>
  </tfoot>
</table>
  <table>
      <tbody>
          <tr>
              <td colspan="4" align="center"></td>
          </tr>
      </tbody>
  </table>




