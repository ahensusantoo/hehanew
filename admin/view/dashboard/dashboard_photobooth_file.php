<?php 

$bulan_ini = date("m");
$tanggal_ini = date("Y-m-d");

$list_produk = $db->query("SELECT * FROM photoboothambil_stan WHERE status_display_photoboothambil='Y'")->fetch_all(MYSQLI_ASSOC);

foreach ($list_produk as $key => $value) {

  $penjualan[$value['nama_photoboothambil_stan']] = @$db->query("SELECT SUM(jumlah_tiket) AS jml FROM photoboothambil_tiket WHERE DATE(tanggal_transaksi)='$tanggal_ini' AND kd_photoboothambil_stan='$value[id_photoboothambil_stan]' AND status_tiket IN ('0', '1')")->fetch_assoc()['jml'];

  if ($penjualan[$value['nama_photoboothambil_stan']] == "") {
    $penjualan[$value['nama_photoboothambil_stan']] = 0;
  }

}


// OMSET BULANAN PERADMIN
$omset_bulan = @$db->query("SELECT CASE WHEN SUM(total_transaksi) IS NULL THEN 0 ELSE SUM(total_transaksi) END AS jml_rupiah, CASE WHEN SUM(jumlah_tiket) IS NULL THEN 0 ELSE SUM(jumlah_tiket) END AS jml_tiket FROM photoboothambil_transaksi WHERE status_transaksi='1' AND MONTH(tanggal_photoboothambil_transaksi)='$bulan_ini' AND kd_admin='$_SESSION[id_admin]'")->fetch_assoc();

// OMSET HARIAN PERADMIN
$omset_harian = @$db->query("SELECT CASE WHEN SUM(total_transaksi) IS NULL THEN 0 ELSE SUM(total_transaksi) END AS jml_rupiah, CASE WHEN SUM(jumlah_tiket) IS NULL THEN 0 ELSE SUM(jumlah_tiket) END AS jml_tiket FROM photoboothambil_transaksi WHERE status_transaksi='1' AND DATE(tanggal_photoboothambil_transaksi)='$tanggal_ini' AND kd_admin='$_SESSION[id_admin]'")->fetch_assoc();


// OMSET BULANAN SEMUA
// $omset_bulan_semua = @$db->query("SELECT CASE WHEN SUM(total_transaksi) IS NULL THEN 0 ELSE SUM(total_transaksi) END AS jml_rupiah, CASE WHEN SUM(jumlah_tiket) IS NULL THEN 0 ELSE SUM(jumlah_tiket) END AS jml_tiket FROM photoboothambil_transaksi WHERE status_transaksi='1' AND MONTH(tanggal_photoboothambil_transaksi)='$bulan_ini'")->fetch_assoc();

// OMSET HARIAN SEMUA
// $omset_harian_semua = @$db->query("SELECT CASE WHEN SUM(total_transaksi) IS NULL THEN 0 ELSE SUM(total_transaksi) END AS jml_rupiah, CASE WHEN SUM(jumlah_tiket) IS NULL THEN 0 ELSE SUM(jumlah_tiket) END AS jml_tiket FROM photoboothambil_transaksi WHERE status_transaksi='1' AND DATE(tanggal_photoboothambil_transaksi)='$tanggal_ini'")->fetch_assoc();

?>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Dashboard Penjualan File Photobooth</h1>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12 col-sm-6 col-md-6">
        <div class="info-box">
          <span class="info-box-icon bg-info elevation-1"><i class="fas fa-money-bill-wave-alt"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Omset "<?= $_SESSION['username'] ?>" <br>Bulan Ini</span>
            <span class="info-box-number">
                <table>
                    <tbody>
                        <tr>
                            <td>Rp</td>
                            <td>:</td>
                            <td><?= number_format($omset_bulan['jml_rupiah']) ?></td>
                        </tr>
                        <tr>
                            <td>Tiket</td>
                            <td>:</td>
                            <td><?= number_format($omset_bulan['jml_tiket']) ?></td>
                        </tr>
                    </tbody>
                </table>
            </span>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-md-6">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-success elevation-1"><i class="fas fa-money-bill-wave-alt"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Omset "<?= $_SESSION['username'] ?>" <br>Hari Ini</span>
            <span class="info-box-number">
                <table>
                    <tbody>
                        <tr>
                            <td>Rp</td>
                            <td>:</td>
                            <td><?= number_format($omset_harian['jml_rupiah']) ?></td>
                        </tr>
                        <tr>
                            <td>Tiket</td>
                            <td>:</td>
                            <td><?= number_format($omset_harian['jml_tiket']) ?></td>
                        </tr>
                    </tbody>
                </table>
            </span>
          </div>
        </div>
      </div>

      <!-- <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
          <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Semua Omset Gabungan<br> File Spot Foto Bulan Ini</span>
            <span class="info-box-number">
                <table>
                    <tbody>
                        <tr>
                            <td>Rp</td>
                            <td>:</td>
                            <td><?= number_format($omset_bulan_semua['jml_rupiah']) ?></td>
                        </tr>
                        <tr>
                            <td>Tiket</td>
                            <td>:</td>
                            <td><?= number_format($omset_bulan_semua['jml_tiket']) ?></td>
                        </tr>
                    </tbody>
                </table>
            </span>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-success elevation-1"><i class="fas fa-users"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Semua Omset Gabungan<br> File Spot Foto Hari Ini</span>
            <span class="info-box-number">
                <table>
                    <tbody>
                        <tr>
                            <td>Rp</td>
                            <td>:</td>
                            <td><?= number_format($omset_harian_semua['jml_rupiah']) ?></td>
                        </tr>
                        <tr>
                            <td>Tiket</td>
                            <td>:</td>
                            <td><?= number_format($omset_harian_semua['jml_tiket']) ?></td>
                        </tr>
                    </tbody>
                </table>
            </span>
          </div>
        </div>
      </div> -->

    </div>

    <div class="row">
      <section class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-chart-line mr-1"></i>
              Penjualan Photobooth Hari Ini
            </h3>
          </div>
          <div class="card-body">
            <div id="chart2" style="width: 100%;">
            </div>
          </div>
        </div>
      </section>
    </div>


  </div>
</section>

<script src="plugins/apexcharts/apexcharts.js"></script>

<script type="text/javascript">
  var options = {
    chart: {
      height: '300px',
      type: 'bar',
      toolbar: {
        show: false
      }
    },
    series: [{
      name: 'Jumlah',
      data: [
      <?php foreach($penjualan as $nama => $jml) : ?>
        <?= $jml ?>,
      <?php endforeach; ?>
      ]
    }],
    xaxis: {
      categories: [
      <?php foreach($penjualan as $nama => $jml) : ?>
        "<?= $nama ?>",
      <?php endforeach; ?>
      ]
    }
  }
  var chart2 = new ApexCharts(document.querySelector("#chart2"), options);
  chart2.render();

</script>
