<?php 
    for($i=8; $i<24; $i++){
        
        $jam_mulai = sprintf('%02s', $i).":00";
        $jam_akhir = sprintf('%02s', $i).":59";
        
        $jam = $jam_mulai." - ".$jam_akhir;
        
        $list_jam[$jam] = $db->query("SELECT SUM(jumlah_tiket) AS jml FROM transaksi WHERE HOUR(tanggal_transaksi) BETWEEN '$jam_mulai' AND '$jam_akhir'")->fetch_assoc()['jml'];
        $list_jam[$jam] = round($list_jam[$jam]);
        
        if($list_jam[$jam] == ""){
            $list_jam[$jam] = 0;
        }
        
    }
    $tgl_awal = $db->query("SELECT tanggal_transaksi AS tgl FROM transaksi WHERE status_transaksi!='3' LIMIT 1")->fetch_assoc()['tgl'];
?>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Laporan Pengunjung</h1>
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
              Rata-rata Jumlah Pengujung Setiap Jam Dari Periode <?= tanggal_indo($tgl_awal) ?> - <?= tanggal_indo(date('Y-m-d')) ?>
          </div>
          <div class="card-body pr-5" align="center">
            <div id="chart" style="width: 100%;">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script type="text/javascript">

	var options = {
		chart: {
		    height: '400px',
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

	var chart = new ApexCharts(document.querySelector("#chart"), options);
	chart.render();

</script>