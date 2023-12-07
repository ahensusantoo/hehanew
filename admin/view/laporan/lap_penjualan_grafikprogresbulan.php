<?php 
if(empty($_POST['tgl_awal']) || empty($_POST['tgl_akhir'])){
  $tgl_awal =  date('Y-m-d', strtotime('-1 months'));
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


// $start = $tgl_awal = strtotime('2009-02-01');
// $tgl_akhir = strtotime('2011-01-01');
// while($tgl_awal < $tgl_akhir)
// {
//     //echo $month."<br>";
//      $tahun_loop = date('Y', $tgl_awal), PHP_EOL;
//      $tgl_awal = strtotime("+1 month", $tgl_awal);
// }


$list_stall = $db->query("SELECT * FROM merchant WHERE status_aktif_merchant='Y' ")->fetch_all(MYSQLI_ASSOC);


if($filer1 == 'all'){
  $stall_dipilih = $list_stall;
}else{
  $id_merchant = enkripsiDekripsi($filer1, 'dekripsi');
  $stall_dipilih = $db->query("SELECT * FROM merchant WHERE status_aktif_merchant='Y' AND id_merchant='$id_merchant'")->fetch_all(MYSQLI_ASSOC);
  // $stall_dipilih[0]['id_merchant'] = $filer1;  
}


foreach ($list_stall as $key => $value) {
  $id_merchant = $value['id_merchant'];

  $start = $month = strtotime($tgl_awal);
  $end = strtotime($tgl_akhir);
  while($month <= $end)
  { 

    $data[$key]['nama_merchant'] = $value['nama_merchant'];
    $tahun_loop =  date('Y', $month);
    $bulan_loop =  date('m', $month);
    $result_omset = @$db->query("SELECT SUM(B.tagihan_nota) AS jml FROM merchant_transaksi B WHERE YEAR(B.tgl_input_transaksi)='$tahun_loop' AND MONTH(B.tgl_input_transaksi)='$bulan_loop' AND B.status_transaksi='2' AND B.kd_jenis_pembayaran!='' AND B.kd_merchant='$value[id_merchant]' ")->fetch_assoc()['jml'];
    if ($result_omset == "") {
      $result_omset = 0;
    }
    $data[$key]['omset'][$bulan_loop."/".$tahun_loop] = $result_omset;


    $result_qty = @$db->query("SELECT SUM(B.jumlah_item) AS jml FROM merchant_transaksi B WHERE YEAR(B.tgl_input_transaksi)='$tahun_loop' AND MONTH(B.tgl_input_transaksi)='$bulan_loop' AND B.status_transaksi='2' AND B.kd_jenis_pembayaran!='' AND B.kd_merchant='$value[id_merchant]' ")->fetch_assoc()['jml'];
    if ($result_qty == "") {
      $result_qty = 0;
    }
    $data[$key]['qty'][$bulan_loop."/".$tahun_loop] = $result_qty;


    $month = strtotime("+1 month", $month);

  }



}




$tgl_awal = $db->query("SELECT tanggal_transaksi AS tgl FROM transaksi WHERE status_transaksi!='3' LIMIT 1")->fetch_assoc()['tgl'];
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
        <h1 class="m-0 text-dark">Grafik Progres Penjualan Per Bulan Per Stall</h1>
      </div>
    </div>
  </div>
</div>
<link rel="stylesheet" href="plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <div class="form-group row">
              <label class="col-sm-1 col-form-label">Periode :</label>
              <div class="col-sm-11">
                <div class="selectMonths" style="width: 100% !important">
                  <input type="text" class="btn btn-default float-left" value="" readonly style="width: 100% !important" />
                  <i>&#128197;</i>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-1 col-form-label">Stall :</label>
              <div class="col-sm-11">
                <select name="filer1" class="select2bs4 form-control" id="filer1" style="width: 100%" required>
                  <option value="all">SEMUA STALL</option>
                    <?php foreach ($list_stall as $bulan => $value): ?>
                        <?php $id_stall_encryp = enkripsiDekripsi($value['id_merchant'],'enkripsi'); ?>
                        <option value="<?= $id_stall_encryp ?>" <?php if($filer1 == $id_stall_encryp){echo 'selected';} ?>> <?= strtoupper($value['nama_merchant']) ?></option>
                    <?php endforeach ?>
                </select>
              </div>
            </div>
            <a onclick="terapkan()">
              <button type="button" class="btn btn-primary" style="width: 100%">Terapkan</button>
            </a>
          </div>
        </div>
      </div>
    </div>


    <?php foreach ($data as $key => $value): ?>

      <div class="card">
        <div class="card-header">
          <b><?= strtoupper($value['nama_merchant']) ?></b>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  PENJUALAN OMSET PER STALL
                </div>
                <div class="card-body pr-5" align="center">
                  <div id="chart_<?= $key ?>_omset" style="width: 100%;">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  PENJUALAN OMSET PER STALL
                </div>
                <div class="card-body pr-5" align="center">
                  <div id="chart_<?= $key ?>_qty" style="width: 100%;">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    <?php endforeach ?>


    

  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script src="plugins/select2/js/select2.full.min.js"></script>


<?php foreach ($data as $key => $value): ?>


<script type="text/javascript">
  var options = {
    chart: {
      height: '300px',
      type: 'bar',
      toolbar: {
        show: false
      }
    },
    dataLabels: {
      formatter: function (val) {
        return parseInt(val).toLocaleString();
      },
    },
    series: [{
      name: 'Jumlah',
      data: [
      <?php foreach($value['omset'] as $periode => $val) : ?>
        <?= $val ?>,
      <?php endforeach; ?>
      ]
    }],
    xaxis: {
      categories: [
      <?php foreach($value['omset'] as $periode => $val) : ?>
        "<?= $periode ?>",
      <?php endforeach; ?>
      ]
    }
  }
  var chart2 = new ApexCharts(document.querySelector("#chart_<?= $key ?>_omset"), options);
  chart2.render();


  var options = {
    chart: {
      height: '300px',
      type: 'bar',
      toolbar: {
        show: false
      }
    },
    dataLabels: {
      formatter: function (val) {
        return parseInt(val).toLocaleString();
      },
    },
    series: [{
      name: 'Jumlah',
      data: [
      <?php foreach($value['qty'] as $periode => $val) : ?>
        <?= $val ?>,
      <?php endforeach; ?>
      ]
    }],
    xaxis: {
      categories: [
      <?php foreach($value['qty'] as $periode => $val) : ?>
        "<?= $periode ?>",
      <?php endforeach; ?>
      ]
    }
  }
  var chart3 = new ApexCharts(document.querySelector("#chart_<?= $key ?>_qty"), options);
  chart3.render();


</script>
  
<?php endforeach ?>


<script src="plugins/month-range2/tether.min.js"></script>
<script src="plugins/month-range2/datePicker.js"></script>
<script>
  window.global_date_start = <?= date('Y', strtotime($tgl_awal))?>+'-'+<?=date('m', strtotime($tgl_awal))?>+'-'+<?=date('d', strtotime($tgl_awal)) ?>;
  window.global_date_end = <?= date('Y', strtotime($tgl_akhir))?>+'-'+<?=date('m', strtotime($tgl_akhir))?>+'-'+<?=date('d', strtotime($tgl_akhir)) ?>;

  $('.selectMonths:first input')
  .rangePicker({ RTL:false, setDate:[[<?= date('m', strtotime($tgl_awal)) ?>,<?= date('Y', strtotime($tgl_awal)) ?>],[<?= date('m', strtotime($tgl_akhir)) ?>,<?= date('Y', strtotime($tgl_akhir)) ?>]], closeOnSelect:true })
  .on('datePicker.done', function(e, result){
    // if( result instanceof Array )
    //   console.log(new Date(result[0][1], result[0][0] - 1), new Date(result[1][1], result[1][0] - 1));
    // else
    //   console.log(result);

    var start_master = new Date(result[0][1], result[0][0] - 1);
    var start = start_master.getFullYear() + "-" + String(start_master.getMonth() + 1).padStart(1, '0') + "-1";
    window.global_date_start = start;

    var end_master = new Date(result[1][1], result[1][0] - 1);
    var end = end_master.getFullYear() + "-" + String(end_master.getMonth() + 1).padStart(1, '0') + "-1";
    window.global_date_end = end;

    // alert(start + end);
    // var form = $('<form action="?page=penjualan&action=grafikprogresbulan" method="post">' +
    //   '<input type="hidden" name="tgl_awal" value="' + start + '" />' +
    //   '<input type="hidden" name="tgl_akhir" value="' + end + '" />' +
    //   '</form>');
    // $('body').append(form);
    // form.submit();
  });

  function terapkan(){
    var filer1 = document.getElementById('filer1').value;
    
    var form = $('<form action="?page=penjualan&action=grafikprogresbulan" method="post">' +
      '<input type="hidden" name="tgl_awal" value="' + window.global_date_start + '" />' +
      '<input type="hidden" name="tgl_akhir" value="' + window.global_date_end + '" />' +
      '<input type="hidden" name="filer1" value="' + filer1 + '" />' +
      '</form>');
    $('body').append(form);
    form.submit();
  }

</script>
