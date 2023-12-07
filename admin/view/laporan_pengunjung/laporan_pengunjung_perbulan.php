<?php 
if(empty($_POST['tgl_awal']) || empty($_POST['tgl_akhir'])){
  $tgl_awal = date('Y-m-d');
  $tgl_akhir = date('Y-m-d');
} else {
  $tgl_awal = date('Y-m-d', strtotime($_POST['tgl_awal']));
  $tgl_akhir = date('Y-m-d', strtotime($_POST['tgl_akhir']));
}

$tahun_awal = substr($tgl_awal,0,4);
$tahun_akhir = substr($tgl_akhir,0,4);

$bulan_awal = substr($tgl_awal,5,2);
$bulan_akhir = substr($tgl_akhir,5,2);



  
  for ($i=$tahun_awal; $i <= $tahun_akhir; $i++) { 
    $tahun[$i] = $bulan_awal;
  }


  if ($tahun_akhir > $tahun_awal) {

    foreach ($tahun as $key => $bulan_awal) {

      if ($key == $tahun_awal) {

        for ($x = $bulan_awal; $x <= 12; $x++) {

          $list_bulan[$x] = $db->query("SELECT SUM(jumlah_tiket) AS jml FROM transaksi WHERE MONTH(tanggal_transaksi)='$x' AND YEAR(tanggal_transaksi)='$key' ")->fetch_assoc()['jml'];
          $list_bulan[$x] = round($list_bulan[$x]);
          
          if($list_bulan[$x] == ""){
              $list_bulan[$x] = 0;
          }
          $data[$key][$x] = $list_bulan[$x];

        }


      }elseif ($key == $tahun_akhir) {

        for ($x = 1; $x <= $bulan_akhir; $x++) {
          

          $list_bulan[$x] = $db->query("SELECT SUM(jumlah_tiket) AS jml FROM transaksi WHERE MONTH(tanggal_transaksi)='$x' AND YEAR(tanggal_transaksi)='$key' ")->fetch_assoc()['jml'];
          $list_bulan[$x] = round($list_bulan[$x]);
          
          if($list_bulan[$x] == ""){
              $list_bulan[$x] = 0;
          }
          $data[$key][$x] = $list_bulan[$x];


        }

      }else{
        for ($x = 1; $x <= 12; $x++) {
          

          $list_bulan[$x] = $db->query("SELECT SUM(jumlah_tiket) AS jml FROM transaksi WHERE MONTH(tanggal_transaksi)='$x' AND YEAR(tanggal_transaksi)='$key' ")->fetch_assoc()['jml'];
          $list_bulan[$x] = round($list_bulan[$x]);
          
          if($list_bulan[$x] == ""){
              $list_bulan[$x] = 0;
          }
          $data[$key][$x] = $list_bulan[$x];


        }
      }

    }

  }else{

    for ($x = $bulan_awal; $x <= $bulan_akhir; $x++) {

      $list_bulan[$x] = $db->query("SELECT SUM(jumlah_tiket) AS jml FROM transaksi WHERE MONTH(tanggal_transaksi)='$x' AND YEAR(tanggal_transaksi)='$tahun_awal' ")->fetch_assoc()['jml'];
      $list_bulan[$x] = round($list_bulan[$x]);
      
      if($list_bulan[$x] == ""){
          $list_bulan[$x] = 0;
      }
      $data[$tahun_awal][$x] = $list_bulan[$x];

    }

  }





?>
<style type="text/css">
  .selectMonths{ position:relative; display:inline-block; }
  .selectMonthsselect {height: 30px; }
  .selectMonths > i{ position:absolute; right:5px; top:5px; opacity:0.35; font-style:normal; font-size:18px; transition:0.2s; pointer-events:none; }
  .selectMonths > input{ text-transform:capitalize; padding-left:10px; cursor:default; cursor:pointer; }
  .selectMonths:hover > i{ opacity:.7; }
  .selectMonths + .selectMonths{ float:none; }
</style>
<link rel="stylesheet" href="plugins/month-range2/picker.css">

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
            <div class="selectMonths">
              <input type="text" class="btn btn-default float-left" value="" readonly />
              <i>&#128197;</i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body pr-5" align="center">
            <table class="table">
                <tbody>
                    <tr>
                        <td colspan="4" align="center">GRAFIK PENGUNJUNG SETIAP BULAN</td>
                    </tr>
                    <tr>
                        <td colspan="4" align="center">PERIODE <?= $bulan_awal ?>/<?= $tahun_awal ?> sd <?= $bulan_akhir ?>/<?= $tahun_akhir ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" align="center"></td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <div id="chart2" style="width: 100%;">
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
      <?php foreach($data as $tahun => $val) : ?>
        <?php foreach($val as $bulan => $val) : ?>
          <?= $val ?>,
        <?php endforeach; ?>
      <?php endforeach; ?>
      ]
    }],
    xaxis: {
      categories: [
      <?php foreach($data as $tahun => $val) : ?>
        <?php foreach($val as $bulan => $val) : ?>
          "<?= $bulan ?>/<?= $tahun ?>",
        <?php endforeach; ?>
      <?php endforeach; ?>
      ]
    }
  }
  var chart2 = new ApexCharts(document.querySelector("#chart2"), options);
  chart2.render();

</script>


<script src="plugins/month-range2/tether.min.js"></script>
<script src="plugins/month-range2/datePicker.js"></script>
<script>
  $('.selectMonths:first input')
  .rangePicker({ RTL:false, setDate:[[<?= date('m', strtotime($tgl_awal)) ?>,<?= date('Y', strtotime($tgl_awal)) ?>],[<?= date('m', strtotime($tgl_akhir)) ?>,<?= date('Y', strtotime($tgl_akhir)) ?>]], closeOnSelect:true })
  .on('datePicker.done', function(e, result){
    // if( result instanceof Array )
    //   console.log(new Date(result[0][1], result[0][0] - 1), new Date(result[1][1], result[1][0] - 1));
    // else
    //   console.log(result);

    var start_master = new Date(result[0][1], result[0][0] - 1);
    var start = start_master.getFullYear() + "-" + String(start_master.getMonth() + 1).padStart(1, '0') + "-1";

    var end_master = new Date(result[1][1], result[1][0] - 1);
    var end = end_master.getFullYear() + "-" + String(end_master.getMonth() + 1).padStart(1, '0') + "-1";

    // alert(start + end);
    var form = $('<form action="?page=pengunjung&action=perbulan" method="post">' +
      '<input type="hidden" name="tgl_awal" value="' + start + '" />' +
      '<input type="hidden" name="tgl_akhir" value="' + end + '" />' +
      '</form>');
    $('body').append(form);
    form.submit();
  });

</script>
