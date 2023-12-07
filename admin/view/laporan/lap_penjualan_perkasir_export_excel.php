<?php
    include("../../templates/koneksi.php");

    
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=Laporan Penjualan Harian Per Kasir.xls");

    
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
    
    
    $id_employee = enkripsiDekripsi($_POST['filer1'] , 'dekripsi');
    
    $kasir = $db->query("SELECT A.nama_employee, B.nama_merchant FROM merchant_employee A JOIN merchant B ON A.kd_merchant=B.id_merchant WHERE A.id_merchant_employee='$id_employee'")->fetch_assoc();
    
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
    <tbody>
        <tr>
            <td colspan="5" align="center">Laporan Penjualan Harian Per Kasir</td>
        </tr>
        <tr>
            <td colspan="5" align="center">Periode : <?= tanggal_indo($tgl_awal) ?> sd <?= tanggal_indo($tgl_akhir) ?></td>
        </tr>
        
        <?php if ($_POST['filer1'] == "all"): ?>
            <tr>
                <td colspan="5" align="center">Kasir : Semua Kasir</td>
            </tr>
        <?php else: ?>
            <tr>
                <td colspan="5" align="center">Kasir : <?= @$kasir['nama_employee'] ?> ( <?= @$kasir['nama_merchant'] ?> )</td>
            </tr>
        <?php endif ?>
        
        
        <tr>
            <td colspan="5" align="center"></td>
        </tr>
    </tbody>
</table>

<table border="1" id="" class="table table-sm table-bordered table-striped">
  <thead>
    <tr>
      <th>No</th>
      <th>Nama Barang</th>
      <th>Harga</th>
      <th>Jumlah</th>
      <th>Total</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $nomor=1;
    $grand_total = 0;
    $id_employee = enkripsiDekripsi($_POST['filer1'] , 'dekripsi');
    
    
    if($_POST['filer1'] == "all"){
        $data = $db->query("
            SELECT 

            B.nama_produk, A.harga_produk, A.harga_setelah_diskon,
            
            (SELECT SUM(C.jumlah_produk) FROM merchant_transaksi_detail C WHERE C.kd_merchant_produk=A.kd_merchant_produk AND DATE(C.tgl_input_detail) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND C.status_transaksi_detail='2' AND C.harga_produk=A.harga_produk ) AS jumlah
            
            FROM merchant_transaksi_detail A JOIN merchant_produk B ON A.kd_merchant_produk=B.id_merchant_produk JOIN merchant_transaksi D ON A.kd_merchant_transaksi=D.id_merchant_transaksi WHERE A.status_transaksi_detail='2' AND D.kd_jenis_pembayaran!='' GROUP BY A.kd_merchant_produk , A.harga_produk  
            
        ")->fetch_all(MYSQLI_ASSOC);
        
        $diskon = $db->query("SELECT SUM(A.harga_produk-A.harga_setelah_diskon) AS jml FROM merchant_transaksi_detail A WHERE DATE(A.tgl_input_detail) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND A.status_transaksi_detail='2'")->fetch_assoc()['jml'];
    }else{
        $data = $db->query("
            SELECT 

            B.nama_produk, A.harga_produk, A.harga_setelah_diskon,
            
            (SELECT SUM(C.jumlah_produk) FROM merchant_transaksi_detail C WHERE C.kd_merchant_employee='$id_employee' AND C.kd_merchant_produk=A.kd_merchant_produk AND DATE(C.tgl_input_detail) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND C.status_transaksi_detail='2' AND C.harga_produk=A.harga_produk ) AS jumlah
            
            FROM merchant_transaksi_detail A JOIN merchant_produk B ON A.kd_merchant_produk=B.id_merchant_produk JOIN merchant_transaksi D ON A.kd_merchant_transaksi=D.id_merchant_transaksi WHERE A.kd_merchant_employee='$id_employee' AND A.status_transaksi_detail='2' AND D.kd_jenis_pembayaran!='' GROUP BY A.kd_merchant_produk , A.harga_produk  
            
        ")->fetch_all(MYSQLI_ASSOC);
        
        $diskon = $db->query("SELECT SUM(A.harga_produk-A.harga_setelah_diskon) AS jml FROM merchant_transaksi_detail A WHERE A.kd_merchant_employee='$id_employee' AND DATE(A.tgl_input_detail) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND A.status_transaksi_detail='2'")->fetch_assoc()['jml'];
    }
    
    
    ?>
    
    <?php $total_omzet_kotor = 0 ?>
    <?php $total_produk = 0 ?>
    <?php foreach($data as $key => $value): ?>
        <?php $subtotal = $value['jumlah'] * $value['harga_produk'] ?>
        <?php $total_omzet_kotor += $subtotal ?>
        <?php $total_produk += $value['jumlah'] ?>
        <tr>
          <td> <?= $key+1 ?></td>
          <td> <?= $value['nama_produk'] ?></td>
          <td>Rp <?= number_format($value['harga_produk']) ?></td>
          <td> <?= number_format($value['jumlah']) ?></td>
          <td>Rp <?= number_format($subtotal) ?></td>
        </tr>
    <?php endforeach; ?>
  </tbody>
  <tfoot>
    <tr>
      <td colspan="3">Total</td>
      <td> <?php echo number_format($total_produk); ?> </td>
      <td>Rp <?php echo number_format($total_omzet_kotor); ?> </td>
    </tr>
    <tr>
      <td colspan="3">Diskon</td>
      <td></td>
      <td>Rp <?php echo number_format($diskon); ?> </td>
    </tr>
    <tr>
      <td colspan="3">Omset Bersih</td>
      <td></td>
      <td>Rp <?php echo number_format($total_omzet_kotor - $diskon); ?> </td>
    </tr>
  </tfoot>
</table>



<table>
    <tbody>
        <tr>
            <td colspan="5" align="center"></td>
        </tr>
    </tbody>
</table>



<?php
            
    if($_POST['filer1'] == "all"){
        $rincian = $db->query("
            SELECT 

            CASE WHEN A.kd_jenis_pembayaran = '' THEN 'BAYAR NANTI' ELSE B.nama_jenis_pembayaran END AS nama_jenis,
            
            (SELECT SUM(C.tagihan_nota) FROM merchant_transaksi C WHERE DATE(C.tgl_input_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND C.kd_jenis_pembayaran=A.kd_jenis_pembayaran AND C.status_transaksi='2') AS jumlah,
            
            (SELECT COUNT(*) FROM merchant_transaksi C WHERE DATE(C.tgl_input_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND C.kd_jenis_pembayaran=A.kd_jenis_pembayaran AND C.status_transaksi='2') AS jumlah_struk
            
            FROM merchant_transaksi A JOIN jenis_pembayaran B ON A.kd_jenis_pembayaran=B.id_jenis_pembayaran  AND A.status_transaksi='2' GROUP BY A.kd_jenis_pembayaran
        ")->fetch_all(MYSQLI_ASSOC);
    }else{
        $rincian = $db->query("
            SELECT 

            CASE WHEN A.kd_jenis_pembayaran = '' THEN 'BAYAR NANTI' ELSE B.nama_jenis_pembayaran END AS nama_jenis,
            
            (SELECT SUM(C.tagihan_nota) FROM merchant_transaksi C WHERE C.kd_merchant_employee='$id_employee' AND DATE(C.tgl_input_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND C.kd_jenis_pembayaran=A.kd_jenis_pembayaran AND C.status_transaksi='2') AS jumlah,
            
            (SELECT COUNT(*) FROM merchant_transaksi C WHERE C.kd_merchant_employee='$id_employee' AND DATE(C.tgl_input_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND C.kd_jenis_pembayaran=A.kd_jenis_pembayaran AND C.status_transaksi='2') AS jumlah_struk
            
            FROM merchant_transaksi A JOIN jenis_pembayaran B ON A.kd_jenis_pembayaran=B.id_jenis_pembayaran  AND A.status_transaksi='2' GROUP BY A.kd_jenis_pembayaran
        ")->fetch_all(MYSQLI_ASSOC);
    }
?>

<table border="1" id="" class="table table-sm table-bordered table-striped">
  <thead>
    <tr>
      <th>No</th>
      <th>Jenis Pembayaran</th>
      <th>Jumlah Struk</th>
      <th colspan="2">Total</th>
    </tr>
  </thead>
  <tbody>
    <?php $total_rincian = 0 ?>
    <?php $total_struk = 0 ?>
    <?php foreach($rincian as $key => $value): ?>
        <?php $total_rincian += $value['jumlah'] ?>
        <?php $total_struk += $value['jumlah_struk'] ?>
        <tr>
          <td> <?= $key+1 ?></td>
          <td> <?= $value['nama_jenis'] ?></td>
          <td><?= ($value['jumlah_struk']) ?></td>
          <td colspan="2">Rp <?= ($value['jumlah']) ?></td>
        </tr>
    <?php endforeach; ?>
  </tbody>
  <tfoot>
    <tr>
      <td></td>
      <td><b>TOTAL</b></td>
      <td><?php echo ($total_struk); ?> </td>
      <td colspan="2">Rp <?php echo ($total_rincian); ?> </td>
    </tr>
  </tfoot>
</table>