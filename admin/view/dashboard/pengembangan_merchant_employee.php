<?php 
$sess_kd_merchant = $_SESSION['kd_merchant'];

	$query = "	SELECT mp.nama_produk as np, mtd.id_merchant_transaksi_detail, mtd.kd_merchant, mtd.kd_merchant_employee, mtd.jumlah_produk, mtd.status_transaksi_detail, m.id_merchant, m.nama_merchant, mtd.status_transaksi_detail AS std ,
				SUM(mtd.jumlah_produk) jumlah_produk
				FROM merchant_transaksi_detail AS mtd
	 			LEFT JOIN merchant_produk AS mp ON mtd.kd_merchant_produk = mp.id_merchant_produk
	 			LEFT JOIN merchant AS m ON mtd.kd_merchant = m.id_merchant
	 			LEFT JOIN merchant_transaksi B ON mtd.kd_merchant_transaksi = B.id_merchant_transaksi
	 			WHERE mtd.kd_merchant = '$sess_kd_merchant' AND mtd.status_transaksi_detail='2'
	 			    AND B.kd_jenis_pembayaran != ''
	 			    AND DATE(mtd.tgl_input_detail) = DATE(NOW())
	 			GROUP BY mtd.kd_merchant_produk
	 			ORDER BY jumlah_produk DESC
	 			LIMIT 10";
    $data_produk = $db->query($query)->fetch_all(MYSQLI_ASSOC);
    
    $jmlh_produk = "SELECT *,
                SUM(A.jumlah_produk) AS jumlah_produk_terjual
                FROM merchant_transaksi_detail A
                LEFT JOIN merchant_transaksi B ON A.kd_merchant_transaksi = B.id_merchant_transaksi
                WHERE A.kd_merchant = '$sess_kd_merchant'
                    AND A.status_transaksi_detail= '2'
                    AND B.kd_jenis_pembayaran != ''
                    AND DATE(A.tgl_input_detail) = DATE(NOW())
    ";
    $jumlah_produk_terjual = $db->query($jmlh_produk)->fetch_assoc()['jumlah_produk_terjual'];
    
    $jmlh_admin = "	SELECT *
                FROM merchant_employee
                WHERE kd_merchant = '$sess_kd_merchant'
    ";
    $jumlah_admin = $db->query($jmlh_admin);
    $admin = $jumlah_admin->num_rows;
    
    $queryPendapatan = "SELECT SUM(A.harga_setelah_diskon * A.jumlah_produk) AS jumlah_harian
                        FROM merchant_transaksi_detail A
                        LEFT JOIN merchant_transaksi B ON A.kd_merchant_transaksi = B.id_merchant_transaksi
                            WHERE A.status_transaksi_detail = '2'
                            AND DATE(A.tgl_input_detail) = DATE(NOW())
                            AND B.kd_jenis_pembayaran != ''
                            AND A.kd_merchant = '$sess_kd_merchant'
                            GROUP BY DATE(A.tgl_input_detail) = DATE(NOW())";
    $data_pendapatan = @$db->query($queryPendapatan)->fetch_assoc()['jumlah_harian'];
    if ($data_pendapatan == ""){
        $data_pendapatan = 0;
    }
    
    $queryPendapatanBulan = "SELECT tgl_input_detail, SUM(A.harga_setelah_diskon * A.jumlah_produk) AS jumlah_bulanan
                        FROM merchant_transaksi_detail A
                        LEFT JOIN merchant_transaksi B ON A.kd_merchant_transaksi = B.id_merchant_transaksi
                            WHERE A.status_transaksi_detail = '2'
                            AND MONTH(A.tgl_input_detail) = MONTH(NOW())
                            AND YEAR(A.tgl_input_detail) = Year(NOW())
                            AND B.kd_jenis_pembayaran != ''
                            AND A.kd_merchant = '$sess_kd_merchant'
                            GROUP BY MONTH(A.tgl_input_detail) = MONTH(NOW())";
    $data_pendapatan_bulan = $db->query($queryPendapatanBulan)->fetch_assoc()['jumlah_bulanan'];
     if ($data_pendapatan_bulan == ""){
        $data_pendapatan_bulan = 0;
    }

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
      
      <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
          <span class="info-box-icon bg-success elevation-1"><i class="fas fa-money-bill-wave-alt"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Omset Hari Ini</span>
            <span class="info-box-number">
                Rp. <?= number_format($data_pendapatan) ?>
          </div>
        </div>
      </div>
      
      <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
          <span class="info-box-icon bg-success elevation-1"><i class="fas fa-money-bill-wave-alt"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Omset Bulan Ini</span>
            <span class="info-box-number">
                Rp. <?= number_format($data_pendapatan_bulan) ?>
          </div>
        </div>
      </div>
      
      <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
          <span class="info-box-icon bg-info elevation-1"><i class="fas fa-box"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Jumlah Product Terjual</span>
            <span class="info-box-number">
            <?php echo number_format($jumlah_produk_terjual) ?>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-success elevation-1"><i class="fas fa-users"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Jumlah Anggota</span>
            <span class="info-box-number">
                <?php echo $admin ?>
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
              Product Product Terlaris Hari ini
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
      <?php foreach($data_produk as $key => $val) : ?>
			        "<?= $val['jumlah_produk'] ?>",
			    <?php endforeach; ?>
      ]
    }],
    xaxis: {
      categories: [
      <?php foreach($data_produk as $key => $val) : ?>
			        "<?= $val['np'] ?>",
			    <?php endforeach; ?>
      ]
    }
  }
  var chart2 = new ApexCharts(document.querySelector("#chart2"), options);
  chart2.render();

</script>