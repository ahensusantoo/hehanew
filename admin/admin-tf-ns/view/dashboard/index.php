<?php 

$bulan_ini = date("m");
$tanggal_ini = date("Y-m-d");

for($i=8; $i<24; $i++){

  $jam_mulai = sprintf('%02s', $i).":00";
  $jam_akhir = sprintf('%02s', $i).":59";

  $jam = $jam_mulai." - ".$jam_akhir;

  $list_jam[$jam] = $db->query("SELECT SUM(jumlah_tiket) AS jml FROM transaksi WHERE HOUR(tanggal_transaksi) BETWEEN '$jam_mulai' AND '$jam_akhir' AND DATE(tanggal_transaksi)='$tanggal_ini' AND status_transaksi!='3'")->fetch_assoc()['jml'];
  $list_jam[$jam] = round($list_jam[$jam]);

  if($list_jam[$jam] == ""){
    $list_jam[$jam] = 0;
  }

}



$list_produk = $db->query("SELECT * FROM photobooth_stan WHERE status_display_photobooth='Y'")->fetch_all(MYSQLI_ASSOC);

foreach ($list_produk as $key => $value) {

  $penjualan[$value['nama_photobooth_stan']] = @$db->query("SELECT SUM(jumlah_tiket) AS jml FROM photobooth_tiket WHERE DATE(tanggal_transaksi)='$tanggal_ini' AND kd_photobooth_stan='$value[id_photobooth_stan]' AND status_tiket IN ('0', '1')")->fetch_assoc()['jml'];

  if ($penjualan[$value['nama_photobooth_stan']] == "") {
    $penjualan[$value['nama_photobooth_stan']] = 0;
  }

}


$tgl_awal = @$db->query("SELECT tanggal_transaksi AS tgl FROM transaksi WHERE status_transaksi!='3' LIMIT 1")->fetch_assoc()['tgl'];


// OMSET TIKET BULANAN SEMUA
$omset_tiket_bulanan = @$db->query("SELECT CASE WHEN SUM(total_transaksi) IS NULL THEN 0 ELSE SUM(total_transaksi) END AS jml_rupiah, CASE WHEN SUM(jumlah_tiket) IS NULL THEN 0 ELSE SUM(jumlah_tiket) END AS jml_tiket FROM transaksi WHERE status_transaksi='1' AND MONTH(tanggal_transaksi)='$bulan_ini'")->fetch_assoc();

// OMSET TIKET HARIAN SEMUA
$onset_tiket_harian = @$db->query("SELECT CASE WHEN SUM(total_transaksi) IS NULL THEN 0 ELSE SUM(total_transaksi) END AS jml_rupiah, CASE WHEN SUM(jumlah_tiket) IS NULL THEN 0 ELSE SUM(jumlah_tiket) END AS jml_tiket FROM transaksi WHERE status_transaksi='1' AND DATE(tanggal_transaksi)='$tanggal_ini'")->fetch_assoc();


?>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Dashboard</h1>
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
            <span class="info-box-text">Omset Tiket Masuk <br>Bulan Ini</span>
            <span class="info-box-number">
                <table>
                    <tbody>
                        <tr>
                            <td>Rp</td>
                            <td>:</td>
                            <td><?= number_format($omset_tiket_bulanan['jml_rupiah']) ?></td>
                        </tr>
                        <tr>
                            <td>Tiket</td>
                            <td>:</td>
                            <td><?= number_format($omset_tiket_bulanan['jml_tiket']) ?></td>
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
            <span class="info-box-text">Omset Tiket Masuk <br>Hari Ini</span>
            <span class="info-box-number">
                <table>
                    <tbody>
                        <tr>
                            <td>Rp</td>
                            <td>:</td>
                            <td><?= number_format($onset_tiket_harian['jml_rupiah']) ?></td>
                        </tr>
                        <tr>
                            <td>Tiket</td>
                            <td>:</td>
                            <td><?= number_format($onset_tiket_harian['jml_tiket']) ?></td>
                        </tr>
                    </tbody>
                </table>
            </span>
          </div>
        </div>
      </div>


    </div>

    <div class="row">
      <section class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-chart-line mr-1"></i>
              Jumlah Pengunjung Hari Ini
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

<script src="../plugins/apexcharts/apexcharts.js"></script>

<script type="text/javascript">
  var options = {
    chart: {
      height: '300px',
      type: 'area',
      toolbar: {
        show: false
      }
    },
    series: [{
      name: 'pengunjung',
      data: [
      <?php foreach($list_jam as $jam => $jml) : ?>
        <?= $jml ?>,
      <?php endforeach; ?>
      ]
    }],
    xaxis: {
      categories: [
      <?php foreach($list_jam as $jam => $jml) : ?>
        "<?= $jam ?>",
      <?php endforeach; ?>
      ]
    }
  }
  var chart2 = new ApexCharts(document.querySelector("#chart2"), options);
  chart2.render();

</script>


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
  var chart3 = new ApexCharts(document.querySelector("#chart3"), options);
  chart3.render();

</script>