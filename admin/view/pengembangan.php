<?php
    
    $tgl_hari_ini = date('Y-m-d');
    $tiket_terjual = $db->query("SELECT SUM(jumlah_tiket) AS jml FROM transaksi WHERE status_transaksi!='3' AND DATE(tanggal_transaksi)='$tgl_hari_ini'")->fetch_assoc()['jml'];
    $pendapatan_hari_ini = $db->query("SELECT SUM(total_transaksi) AS jml FROM transaksi WHERE status_transaksi!='3' AND DATE(tanggal_transaksi)='$tgl_hari_ini'")->fetch_assoc()['jml'];
    
?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Rangkuman</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Rangkuman</li>
        </ol>
      </div>
    </div>
  </div>
</section>

<section class="content">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-info">
          <div class="inner text-center">
            <hr>
            <h5><?= number_format($tiket_terjual) ?></h5>
            <p>Tiket Terjual Hari Ini</p>
            <hr>
          </div>
        </div>
      </div>
      <!-- ./col -->
      <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-success">
          <div class="inner text-center">
            <hr>
            <h5>Rp <?= number_format($pendapatan_hari_ini) ?>-</h5>
            <p>Pendapatan Hari Ini</p>
            <hr>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>