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
      <th colspan="5" style="text-align: center;">Laporan Penjualan Harian Per Produk</th>
    </tr>
    <tr><th colspan="5" style="text-align: center;">Periode <?= $tgl_awal ?> s/d <?= $tgl_akhir ?> </th></tr>
    <tr><th></th></tr>
  </thead>
</table>



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
    
    
  <table border="1" class="table table-bordered table-striped mb-4 table-sm">
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
          <td> <?php echo ($value['harga_produk']); ?> </td>
          <td> <?php echo ($value['jumlah']); ?> </td>
          <td> <?php echo ($value['jumlah'] * $value['harga_produk']); ?> </td>
          <td> <?= round($persen, 2) ?>%</td>
        </tr>
      <?php endforeach ?>
      <tr>
        <td colspan="3" align="right">DISKON</td>
        <td></td>
        <td><?= ($diskon) ?></td>
        <td></td>
      </tr>
      <tr>
        <td colspan="3" align="right">TOTAL</td>
        <td><?= ($total_jumlah) ?></td>
        <td><?= ($total_perstall[$key_awal] - $diskon) ?></td>
        <td><?= ($total_persen) ?>%</td>
      </tr>
    </tbody>
  </table>

  <table>
      <tbody>
          <tr>
              <td colspan="5" align="center"></td>
          </tr>
      </tbody>
  </table>


<?php endforeach ?>

