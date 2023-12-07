<?php
  include("../../templates/koneksi.php");

  
  header("Content-type: application/vnd-ms-excel");
  header("Content-Disposition: attachment; filename=Laporan Bagi Hasil Harian.xls");

  

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
      <th colspan="4" style="text-align: center;">Laporan Penjualan Bagi Hasil Harian</th>
    </tr>
    <tr><th colspan="4" style="text-align: center;">Periode <?= $tgl_awal ?> s/d <?= $tgl_akhir ?> </th></tr>
    <tr><th></th></tr>
  </thead>
</table>


<table border="1" id="example" class="table table-bordered table-striped">
  <thead>
    <tr>
      <th rowspan="2">Tanggal</th>
      <th rowspan="2">Nama Stall</th>
      <th colspan="3">Omset Bersih</th>
      <th colspan="2">Bagi Hasil</th>
  </tr>
  <tr>
      <th>Shift Pagi</th>
      <th>Shift Siang</th>
      <th>Total</th>
      <th>HEHA (22%)</th>
      <th>OKY (78%)</th>
  </tr>
</thead>
<tbody>
    <?php
    $nomor=1;
    $grand_total = 0;
    $sql = mysqli_query($db,"SELECT B.kd_jenis_tiket, C.nama_jenis_tiket, B.harga_satuan, 
      (SELECT SUM(jumlah_tiket) FROM view_transaksi_join_tiket_orderby_id WHERE status_transaksi!='3' AND kd_jenis_tiket=B.kd_jenis_tiket AND DATE(tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS jumlah_tiket, 
      (SELECT SUM(nominal_sebelum_diskon) FROM view_transaksi_join_tiket_orderby_id WHERE status_transaksi!='3' AND kd_jenis_tiket=B.kd_jenis_tiket AND DATE(tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS total_harga_satuan,
      (SELECT SUM(diskon) FROM view_transaksi_join_tiket_orderby_id WHERE status_transaksi!='3' AND kd_jenis_tiket=B.kd_jenis_tiket AND DATE(tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS total_diskon,
      (SELECT SUM(total_transaksi) FROM view_transaksi_join_tiket_orderby_id WHERE status_transaksi!='3' AND kd_jenis_tiket=B.kd_jenis_tiket AND DATE(tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS total_transaksi 
      FROM transaksi A 
      JOIN tiket B ON A.id_transaksi=B.kd_transaksi 
      JOIN jenis_tiket C ON B.kd_jenis_tiket=C.id_jenis_tiket 
      WHERE A.status_transaksi!='3' 
      AND DATE(A.tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' 
      GROUP BY B.kd_jenis_tiket");
    while($query = mysqli_fetch_array($sql)) {
      $grand_total = $grand_total + $query['total_transaksi'];
      ?>
      <tr>
        <td> <?php echo $nomor++; ?> </td>
        <td> <?php echo $query['nama_jenis_tiket']; ?> </td>
        <td> <?php echo number_format($query['harga_satuan']); ?> </td>
        <td> <?php echo number_format($query['jumlah_tiket']); ?> </td>
        <td> <?php echo number_format($query['total_harga_satuan']); ?> </td>
    </tr>
<?php } ?>
</tbody>
<tfoot>
    <tr>
      <td colspan="2">Total</td>
      <td> <?php echo number_format($grand_total); ?> </td>
      <td> <?php echo number_format($grand_total); ?> </td>
      <td> <?php echo number_format($grand_total); ?> </td>
      <td> <?php echo number_format($grand_total); ?> </td>
      <td> <?php echo number_format($grand_total); ?> </td>
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




