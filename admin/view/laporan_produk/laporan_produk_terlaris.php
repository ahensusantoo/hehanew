<?php 
    $mulai      = antiSQLi(@$_GET['mulai']);
    $akhir      = antiSQLi(@$_GET['akhir']);

	$sess_kd_merchant = $_SESSION['kd_merchant'];

    if( $mulai != "" && $akhir != "" ){
    	$query = "	SELECT mp.nama_produk as np, mtd.id_merchant_transaksi_detail, mtd.harga_produk, mtd.kd_merchant, mtd.kd_merchant_employee, mtd.jumlah_produk, mtd.status_transaksi_detail, m.id_merchant, m.nama_merchant, mtd.status_transaksi_detail AS std ,
    				SUM(mtd.jumlah_produk) jumlah_produk
    				FROM merchant_transaksi_detail AS mtd
    	 			LEFT JOIN merchant_produk AS mp ON mtd.kd_merchant_produk = mp.id_merchant_produk
    	 			LEFT JOIN merchant AS m ON mtd.kd_merchant = m.id_merchant
    	 			LEFT JOIN merchant_transaksi mt ON mtd.kd_merchant_transaksi = mt.id_merchant_transaksi
    	 			WHERE mtd.kd_merchant = '$sess_kd_merchant' 
    	 			    AND mtd.status_transaksi_detail='2'
    	 			    AND DATE(mtd.tgl_input_detail) BETWEEN '$mulai' AND '$akhir'
    	 			    AND mt.kd_jenis_pembayaran != ''
    	 			GROUP BY mtd.kd_merchant_produk, mtd.harga_produk
    	 			ORDER BY jumlah_produk DESC
    	 			LIMIT 10";
        $data_produk = $db->query($query)->fetch_all(MYSQLI_ASSOC);
    }else{
        $query = "	SELECT mp.nama_produk as np, mtd.id_merchant_transaksi_detail, mtd.harga_produk, mtd.kd_merchant, mtd.kd_merchant_employee, mtd.jumlah_produk, mtd.status_transaksi_detail, m.id_merchant, m.nama_merchant, mtd.status_transaksi_detail AS std ,
        				SUM(mtd.jumlah_produk) jumlah_produk
        				FROM merchant_transaksi_detail AS mtd
        	 			LEFT JOIN merchant_produk AS mp ON mtd.kd_merchant_produk = mp.id_merchant_produk
        	 			LEFT JOIN merchant AS m ON mtd.kd_merchant = m.id_merchant
        	 			LEFT JOIN merchant_transaksi mt ON mtd.kd_merchant_transaksi = mt.id_merchant_transaksi
        	 			WHERE mtd.kd_merchant = '$sess_kd_merchant' 
        	 			    AND mtd.status_transaksi_detail='2'
        	 			    AND DATE(mtd.tgl_input_detail) = DATE(NOW())
        	 			    AND mt.kd_jenis_pembayaran != ''
        	 			GROUP BY mtd.kd_merchant_produk, mtd.harga_produk
        	 			ORDER BY jumlah_produk DESC
        	 			LIMIT 10";
            $data_produk = $db->query($query)->fetch_all(MYSQLI_ASSOC);
    }
    //  echo "<pre>";
    // echo print_r($data_produk);
    // echo die();

?>
<section class="mt-4 mb-3 content" >
    <form action="" method="GET">
        <input type="hidden" name="page" value="<?= @$_GET['page'] ?>">
        <input type="hidden" name="action" value="<?= @$_GET['action'] ?>">
        <div class="row">
            <div class="col-md-3">
                <label class="sr-only" for="inlineFormInputGroupUsername2">Username</label>
                  <div class="input-group mb-2 mr-sm-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Mulai</div>
                    </div>
                    <input type="date" class="form-control" name="mulai" value="<?= date("Y-m-d") ?>">
                </div>
            </div>
            <div class="col-md-3">
                <label class="sr-only" for="inlineFormInputGroupUsername2">Username</label>
                  <div class="input-group mb-2 mr-sm-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Akhir</div>
                    </div>
                    <input type="date" class="form-control" name="akhir" value="<?= date("Y-m-d") ?>">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" name="filter" class="btn btn-block btn-outline-secondary" style="height: 81%">Proses</button>
            </div>
        </div>
    </form>
</section>

<section>
    <div class="row">
      <section class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-chart-line mr-1"></i>
              Product Product Terlaris
            </h3>
          </div>
          <div class="card-body">
            <div id="chart2" style="width: 100%;">
            </div>
          </div>
        </div>
      </section>
    </div>
</section>

<section class="content">
  	<div class="container-fluid">
    	<div class="row">
      		<div class="col-12">
        		<div class="card">
          			<div class="card-header">
          				<div class="table-responsive">
          					<table class="table table-bordered table-striped" id="example" >
          						<thead>
          							<tr>
          								<th>Ranking</th>
          								<th>Nama Produk</th>
          								<th>Harga Satuan</th>
          								<th>Jumlah Terjual</th>
          							</tr>
          						</thead>
          						<tbody>
          						    <?php $no=1; foreach ($data_produk as $val ) { ?>
              							<tr>
              							    <td><?=$no++ ?></td>
              							    <td><?=$val['np']?></td>
              							    <td><?=number_format($val['harga_produk'])?></td>
              							    <td><?=$val['jumlah_produk']?></td>
              							</tr>
          							<?php } ?>
          						</tbody>
          					</table>
          				</div>
          			</div>
        		</div>
        	</div>
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