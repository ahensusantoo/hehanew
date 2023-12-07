<?php 
	// for($i=1; $i<12; $i++){
	$tahun      = antiSQLi(@$_GET['tahun']);

	$sess_kd_merchant = $_SESSION['kd_merchant'];

    if( $tahun != ""){
        
        for ($i=1; $i<=12 ; $i++) { 
            
            $query = "SELECT SUM(a.harga_setelah_diskon * a.jumlah_produk) AS jml 
                        FROM merchant_transaksi_detail a
                        LEFT JOIN merchant_transaksi b ON a.kd_merchant_transaksi = b.id_merchant_transaksi
                        WHERE MONTH(a.tgl_input_detail)='$pecahBulan' 
                            AND YEAR(a.tgl_input_detail)='$tahun' 
                            AND a.kd_merchant = '$sess_kd_merchant'
                            AND b.kd_jenis_pembayaran != ''
                            AND a.status_transaksi_detail='2'";
            
            $produk_terlaris = @$db->query($query)->fetch_assoc()['jml'];
    		if($produk_terlaris == NULL){
    		    $produk_terlaris = 0;
    		}
    	
    		$list_produk[$i]['tgl_input_transaksi'] = $pecahBulan;
            $list_produk[$i]['jumlah'] = @$produk_terlaris;
        }    
        for ($i=1; $i<=12 ; $i++) { 
            $pecahBulan = sprintf('%2s', $i);
            //untuk untuk jumlah transaksi
            $query_qty = "SELECT 
                            SUM(a.jumlah_produk) AS jml 
                            FROM merchant_transaksi_detail a
                            LEFT JOIN merchant_transaksi b ON a.kd_merchant_transaksi = b.id_merchant_transaksi
                            WHERE MONTH(a.tgl_input_detail)='$pecahBulan' 
                            AND YEAR(a.tgl_input_detail)='$tahun' 
                            AND a.kd_merchant = '$sess_kd_merchant'
                            AND b.kd_jenis_pembayaran != ''
                            AND a.status_transaksi_detail='2'";
            
            $produk_qty = @$db->query($query_qty)->fetch_assoc()['jml'];
    		if($produk_qty == NULL){
    		    $produk_qty = 0;
    		}
    	
    		$list_qty[$i]['tgl_input_transaksi'] = $pecahBulan;
            $list_qty[$i]['jumlah'] = @$produk_qty;
    
        }
        
    }else{

    	$tahun = date('Y');
    
    	for ($i=1; $i<=12 ; $i++) { 
    		
    		$pecahBulan = sprintf('%2s', $i);
    		$year = date('Y');

            $query = "SELECT SUM(a.harga_setelah_diskon * a.jumlah_produk) AS jml 
                            FROM merchant_transaksi_detail a 
                            LEFT JOIN merchant_transaksi b ON a.kd_merchant_transaksi = b.id_merchant_transaksi
                            WHERE MONTH(a.tgl_input_detail)='$pecahBulan' 
                                AND YEAR(a.tgl_input_detail)='$year' 
                                AND a.kd_merchant = '$sess_kd_merchant' 
                                AND b.kd_jenis_pembayaran != ''
                                AND a.status_transaksi_detail='2' ";
    
    		$produk_terlaris = @$db->query($query)->fetch_assoc()['jml'];
    		if($produk_terlaris == NULL){
    		    $produk_terlaris = 0;
    		}
    	
    		$list_produk[$i]['tgl_input_transaksi'] = $pecahBulan;
            $list_produk[$i]['jumlah'] = @$produk_terlaris;
            
    	}
		for ($i=1; $i<=12 ; $i++) { 
		    $pecahBulan = sprintf('%2s', $i);
    		 //untuk untuk jumlah transaksi
            $query_qty = "SELECT 
                            SUM(a.jumlah_produk) AS jml 
                            FROM merchant_transaksi_detail a
                            LEFT JOIN merchant_transaksi b ON a.kd_merchant_transaksi = b.id_merchant_transaksi
                            WHERE MONTH(a.tgl_input_detail)='$pecahBulan' 
                            AND YEAR(a.tgl_input_detail)='$tahun' 
                            AND a.kd_merchant = '$sess_kd_merchant' 
                            AND b.kd_jenis_pembayaran != ''
                            AND a.status_transaksi_detail='2'";
            
            $produk_qty = @$db->query($query_qty)->fetch_assoc()['jml'];
    		if($produk_qty == NULL){
    		    $produk_qty = 0;
    		}
    	
    		$list_qty[$i]['tgl_input_transaksi'] = $pecahBulan;
            $list_qty[$i]['jumlah'] = @$produk_qty;
  
        }
    }




?>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-12">
        <h1 class="mb-4 text-dark">Laporan Pendapatan Perbulan Pada Tahun (<?= SUBSTR($tahun,0,4) ?>)</h1>
            <form action="" method="GET">
                <input type="hidden" name="page" value="<?= @$_GET['page'] ?>">
                <input type="hidden" name="action" value="<?= @$_GET['action'] ?>">
                <div class="row">
                   
                    <div class="col-md-3">
                        <?php 
                        	$queryTahun = "SELECT *
                        	            FROM merchant_transaksi
                        	            WHERE status_transaksi ='2' 
                            	            AND kd_merchant = '$sess_kd_merchant'
                                        GROUP BY YEAR(tgl_input_transaksi)
                                        ORDER BY YEAR(tgl_input_transaksi) DESC";
                            
                            // $produk_terlaris[$i] = $db->query($query)->fetch_all(MYSQLI_ASSOC);
                            $tahun = $db->query($queryTahun)->fetch_all(MYSQLI_ASSOC);
                        ?>
                        <select id="tahun" name="tahun" class="form-control">
                            
                              <?php foreach ($tahun as $value) { ?>
                                  <option value="<?=$value['tgl_input_transaksi']?>"><?= SUBSTR($value['tgl_input_transaksi'],0,4) ?></option>
                              <?php } ?>
                        </select>
                    </div>
                   
                    <div class="col-md-2">
                        <button type="submit" name="filter" class="btn btn-block btn-outline-secondary" style="height: 81%">Proses</button>
                    </div>
                </div>
            </form>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
              <h2> PENJUALAN OMSET</h2>
          </div>
          <div class="card-body pr-5" align="center">
            <div class="chart_omset" style="width: 100%;">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h2>PENJUALAN QUANTITAS</h2>
          </div>
          <div class="card-body pr-5" align="center">
            <div class="chart_qty" style="width: 100%;">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="dist/apex/dist/apexcharts.min.js"></script>
<script src="dist/apex/dist/apexcharts.js"></script>

<script type="text/javascript">

	var options = {
		chart: {
		    height: '400px',
			type: 'area',
			toolbar: {
		      show: false
		    }
		},
		dataLabels:{
		    formatter: function (val){
		        return parseInt(val).toLocaleString();
		    },
		},
		series: [{
			name: 'Pendapatan',
			data : [
				<?php foreach($list_produk as $key => $val) : ?>
			        <?= "'".$val['jumlah']."'"?>,
			    <?php endforeach; ?>
			]
		}],
		xaxis: {
		    
			categories: [
					<?php foreach($list_produk as $key => $val) : ?>
				        <?= "'".bulanAbjad($val['tgl_input_transaksi'])."'" ?>,
				    <?php endforeach; ?>
			    ]
		}
	}

	var chart = new ApexCharts(document.querySelector(".chart_omset"), options);
	chart.render();
	
	
	var options = {
		chart: {
		    height: '400px',
			type: 'area',
			toolbar: {
		      show: false
		    }
		},
		dataLabels:{
		    formatter: function (val){
		        return parseInt(val).toLocaleString();
		    },
		},
		series: [{
			name: 'Pendapatan',
			data : [
				<?php foreach($list_qty as $key => $val) : ?>
			        <?= "'".$val['jumlah']."'"?>,
			    <?php endforeach; ?>
			]
		}],
		xaxis: {
		    
			categories: [
					<?php foreach($list_qty as $key => $val) : ?>
				        <?= "'".bulanAbjad($val['tgl_input_transaksi'])."'" ?>,
				    <?php endforeach; ?>
			    ]
		}
	}

	var chart = new ApexCharts(document.querySelector(".chart_qty"), options);
	chart.render();


</script>