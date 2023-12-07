<?php
  include("../../templates/koneksi.php");

  
  header("Content-type: application/vnd-ms-excel");
  header("Content-Disposition: attachment; filename=Laporan Penjualan Harian Per Produk.xls");

  

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
    $filer1 = 'all';
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
      <th colspan="4" style="text-align: center;">Laporan Penjualan 10 Produk Terlaris Per Stall</th>
    </tr>
    <tr><th colspan="4" style="text-align: center;">Periode <?= $tgl_awal ?> s/d <?= $tgl_akhir ?> </th></tr>
    <tr><th></th></tr>
  </thead>
</table>


<?php foreach ($stall_dipilih as $key => $value): ?>

  <?php  
    $query = "SELECT A.kd_merchant_produk, B.nama_produk, A.harga_produk, SUM(A.jumlah_produk) AS jml FROM merchant_transaksi_detail A JOIN merchant_produk B ON A.kd_merchant_produk=B.id_merchant_produk WHERE A.status_transaksi_detail='2' AND  A.kd_merchant='$value[id_merchant]'  AND DATE(A.tgl_input_detail) BETWEEN '$tgl_awal' AND '$tgl_akhir' GROUP BY A.kd_merchant_produk, A.harga_produk ORDER BY SUM(A.jumlah_produk) DESC
            ";
    $list_produk = $db->query($query)->fetch_all(MYSQLI_ASSOC);
  ?>

  <table border="1" class="table table-bordered table-striped mb-4 table-sm">
    <thead>
      <tr>
        <th colspan="4"><?= $value['nama_merchant'] ?></th>
      </tr>
      <tr>
        <th>No</th>
        <th>Nama Barang</th>
        <th>Harga</th>
        <th>Jumlah Terjual</th>
      </tr>
    </thead>
    <tbody>
      <?php $total_jumlah = 0 ?>
      <?php foreach ($list_produk as $key => $value): ?>
        <tr>
          <?php  
            $total_jumlah += $value['jml'];
          ?>
          <td> <?php echo $key+1; ?> </td>
          <td> <?php echo $value['nama_produk']; ?> </td>
          <td> <?php echo number_format($value['harga_produk']); ?> </td>
          <td> <?php echo number_format($value['jml']); ?> </td>
        </tr>
      <?php endforeach ?>
      <tr>
        <td colspan="3" align="right">TOTAL</td>
        <td><?= ($total_jumlah) ?></td>
      </tr>
    </tbody>
  </table>

  <table>
      <tbody>
          <tr>
              <td colspan="4" align="center"></td>
          </tr>
      </tbody>
  </table>

<?php endforeach ?>



