<?php 


header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=laporan_kasir.xls");

require_once '../../templates/koneksi.php';


$date      = antiSQLi(@$_GET['date']);

 $sess_kd_merchant = $_SESSION['kd_merchant'];
  if ($date != "" ){
      $query = "SELECT mp.nama_produk, mp.status_display_produk, mp.id_merchant_produk, mhs.stok_setelah, me.nama_employee, mhs.harga_beli, mhs.jenis_history, mhs.stok_sebelum, mhs.stok_setelah, mhs.masuk, mhs.keluar, mhs.keterangan,
             SUM(mhs.masuk) masuk, SUM(mhs.keluar) keluar
             FROM merchant_history_stok AS mhs
             LEFT JOIN merchant_produk AS mp ON mhs.kd_merchant_produk = mp.id_merchant_produk 
             LEFT JOIN merchant_employee AS me ON mhs.kd_merchant_employee = me.id_merchant_employee 
             WHERE mhs.kd_merchant = '$sess_kd_merchant' 
                AND date(mhs.tanggal_history) = '$date'
             GROUP BY mhs.kd_merchant_produk
             ORDER BY mhs.id_merchant_history_stok DESC";
      $data_stock = $db->query($query)->fetch_all(MYSQLI_ASSOC);

  }
  else{
    $date = date("Y-m-d");
  }

?>

<table style=" width: 100%">
    <tbody>
        <tr>
            <td>Priode</td>
            <td> : </td>
            <td><?= tanggal_indo($date) ?></td>
        </tr>
    </tbody>
</table>
<hr>

<table class="table table-bodered table-striped" id="example" border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Product</th>
            <th>Harga</th>
            <th>Stock Sebelum</th>
            <th>Masuk</th>
            <th>Keluar</th>
            <th>Stock Akhir</th>
            <th>Nilai Stock Akhir</th>
        </tr>
    </thead>
    <tbody>
        <?php
             $no = 1;
            foreach ($data_stock as $o) {
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $o['nama_produk'] ?></td>
            <td>Rp <?= number_format($o['harga_beli']) ?></td>
            <td><?= $o['stok_sebelum'] ?></td>
            <td><?= $o['masuk'] ?></td>
            <td><?= $o['keluar'] ?></td>
            <td><?= $o['stok_sebelum']+$o['masuk']-$o['keluar'] ?></td>
            <td>Rp <?=  number_format(($o['stok_sebelum']+$o['masuk']-$o['keluar'])*$o['harga_beli']) ?></td>
        </tr>
        <?php
            }
        ?>
    </tbody>
</table>