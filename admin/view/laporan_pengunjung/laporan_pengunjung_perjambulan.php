<?php
if(empty($_POST['tgl_awal']) || empty($_POST['tgl_akhir'])){
  $tgl_awal = date('Y-m-d');
  $tgl_akhir = date('Y-m-d');
} else {
  $tgl_awal = date('Y-m-d', strtotime($_POST['tgl_awal']));
  $tgl_akhir = date('Y-m-d', strtotime($_POST['tgl_akhir']));
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
        <h1 class="m-0 text-dark">Laporan Pengunjung Perjam Setiap Bulan</h1>
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
            <a onclick="exportexcel('<?= $tgl_awal ?>', '<?= $tgl_akhir ?>')">
              <button type="button" class="btn btn-default float-left" style="margin-right: 7px"><i class="fa fa-fw fa-file-download"></i>Eksport Excel</button>
            </a>
            <div class="selectMonths">
              <input type="text" class="btn btn-default float-left" value="" readonly />
              <i>&#128197;</i>
            </div>
          </div>
          <div class="card-body">


<?php  
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


          $jml_total = 0;
          for($i=8; $i<24; $i++){
              
              $jam_mulai = sprintf('%02s', $i).":00";
              $jam_akhir = sprintf('%02s', $i).":59";
              
              $jam = $jam_mulai." - ".$jam_akhir;
              
              $list_jam[$jam] = $db->query("SELECT SUM(jumlah_tiket) AS jml FROM transaksi WHERE HOUR(tanggal_transaksi) BETWEEN '$jam_mulai' AND '$jam_akhir' AND MONTH(tanggal_transaksi)='$x' AND YEAR(tanggal_transaksi)='$key' ")->fetch_assoc()['jml'];
              $list_jam[$jam] = round($list_jam[$jam]);
              
              if($list_jam[$jam] == ""){
                  $list_jam[$jam] = 0;
              }
              
              $jml_total += $list_jam[$jam];
              
          }
          $data[$key][$x] = $list_jam;


        }
      }elseif ($key == $tahun_akhir) {
        for ($x = 1; $x <= $bulan_akhir; $x++) {
          

          $jml_total = 0;
          for($i=8; $i<24; $i++){
              
              $jam_mulai = sprintf('%02s', $i).":00";
              $jam_akhir = sprintf('%02s', $i).":59";
              
              $jam = $jam_mulai." - ".$jam_akhir;
              
              $list_jam[$jam] = $db->query("SELECT SUM(jumlah_tiket) AS jml FROM transaksi WHERE HOUR(tanggal_transaksi) BETWEEN '$jam_mulai' AND '$jam_akhir' AND MONTH(tanggal_transaksi)='$x' AND YEAR(tanggal_transaksi)='$key' ")->fetch_assoc()['jml'];
              $list_jam[$jam] = round($list_jam[$jam]);
              
              if($list_jam[$jam] == ""){
                  $list_jam[$jam] = 0;
              }
              
              $jml_total += $list_jam[$jam];
              
          }
          $data[$key][$x] = $list_jam;


        }
      }else{
        for ($x = 1; $x <= 12; $x++) {
          

          $jml_total = 0;
          for($i=8; $i<24; $i++){
              
              $jam_mulai = sprintf('%02s', $i).":00";
              $jam_akhir = sprintf('%02s', $i).":59";
              
              $jam = $jam_mulai." - ".$jam_akhir;
              
              $list_jam[$jam] = $db->query("SELECT SUM(jumlah_tiket) AS jml FROM transaksi WHERE HOUR(tanggal_transaksi) BETWEEN '$jam_mulai' AND '$jam_akhir' AND MONTH(tanggal_transaksi)='$x' AND YEAR(tanggal_transaksi)='$key' ")->fetch_assoc()['jml'];
              $list_jam[$jam] = round($list_jam[$jam]);
              
              if($list_jam[$jam] == ""){
                  $list_jam[$jam] = 0;
              }
              
              $jml_total += $list_jam[$jam];
              
          }
          $data[$key][$x] = $list_jam;


        }
      }

    }

  }else{

    for ($x = $bulan_awal; $x <= $bulan_akhir; $x++) {


      $jml_total = 0;
      for($i=8; $i<24; $i++){
          
          $jam_mulai = sprintf('%02s', $i).":00";
          $jam_akhir = sprintf('%02s', $i).":59";
          
          $jam = $jam_mulai." - ".$jam_akhir;
          
          $list_jam[$jam] = $db->query("SELECT SUM(jumlah_tiket) AS jml FROM transaksi WHERE HOUR(tanggal_transaksi) BETWEEN '$jam_mulai' AND '$jam_akhir' AND MONTH(tanggal_transaksi)='$x' AND YEAR(tanggal_transaksi)='$tahun_awal' ")->fetch_assoc()['jml'];
          $list_jam[$jam] = round($list_jam[$jam]);
          
          if($list_jam[$jam] == ""){
              $list_jam[$jam] = 0;
          }
          
          $jml_total += $list_jam[$jam];
          
      }

      $data[$tahun_awal][$x] = $list_jam;
    }

  }

  

?>
  
  

<table class="table">
    <tbody>
        <tr>
            <td colspan="4" align="center">ANALISA JUMLAH PENGUNJUNG BERDASARKAN JAM SETIAP BULANNYA</td>
        </tr>
        <tr>
            <td colspan="4" align="center">PERIODE <?= $bulan_awal ?>/<?= $tahun_awal ?> sd <?= $bulan_akhir ?>/<?= $tahun_akhir ?></td>
        </tr>
        <tr>
            <td colspan="4" align="center"></td>
        </tr>
    </tbody>
</table>

<div class="table-responsive">
  <table class="table table-sm text-sm table-bordered">
    <thead>
      <tr>
        <th rowspan="2" class="text-center">No</th>
        <th rowspan="2" class="text-center">Jam</th>
        <?php foreach ($data as $tahun => $data_val): ?>
          <?php foreach ($data_val as $bulan => $value): ?>
            <th colspan="3" class="text-center"><?= $bulan ?>/<?= $tahun ?></th>
          <?php endforeach ?>
        <?php endforeach ?>
        <th colspan="3">TOTAL</th>
      </tr>
      <tr>
        <?php foreach ($data as $tahun => $data_val): ?>
          <?php foreach ($data_val as $bulan => $value): ?>
            <th class="text-center">Jml</th>
            <th class="text-center">Rata2</th>
            <th class="text-center">%</th>
            <?php  
              $subtotal_bawah[$tahun."".$bulan] = array_sum($value);
              $rata2_bawah[$tahun."".$bulan] = $subtotal_bawah[$tahun."".$bulan] / 30;
              $persen_bawah[$tahun."".$bulan] = 0;
            ?>
          <?php endforeach ?>
        <?php endforeach ?>
        <th class="text-center">Jml</th>
        <th class="text-center">Rata2</th>
        <th class="text-center">%</th>
      </tr>
    </thead>
    <tbody>
      <?php 
        $total_kanan_bawah = array_sum($subtotal_bawah);
        $total_persen_kanan_bawah = 0;
        $nomor = 1;
        for($i=8; $i<24; $i++){ 
            $jam_mulai = sprintf('%02s', $i).":00";
            $jam_akhir = sprintf('%02s', $i).":59";?>
           
              <tr>
                <td><?= $nomor++ ?></td>
                <td nowrap><?= $jam_mulai ?> - <?= $jam_akhir ?></td>

                <?php $subtotal_kanan = 0; ?>
                <?php foreach ($data as $tahun => $data_val): ?>
                  <?php foreach ($data_val as $bulan => $value): ?>
                    <td><?= $value[$jam_mulai." - ".$jam_akhir] ?></td>
                    <td><?= round($value[$jam_mulai." - ".$jam_akhir] / 30, 2) ?></td>
                    <td>
                      <?php  
                        if ($subtotal_bawah[$tahun."".$bulan] == 0) {
                          $pembagi = 1;
                        }else{
                          $pembagi = $subtotal_bawah[$tahun."".$bulan];
                        }

                        $persen = round(($value[$jam_mulai." - ".$jam_akhir] / $pembagi) * 100, 2);
                        $subtotal_kanan += $value[$jam_mulai." - ".$jam_akhir] ;

                        $persen_bawah[$tahun."".$bulan] += $persen;
                      ?>
                      <?= ($persen) ?>%
                    </td>
                  <?php endforeach ?>
                <?php endforeach ?>

                <td><?= $subtotal_kanan ?></td>
                <td><?= round($subtotal_kanan/30, 2) ?></td>
                <td>
                  <?php if ($total_kanan_bawah == 0): ?>
                    0%
                  <?php else: ?>
                    <?= round(($subtotal_kanan / $total_kanan_bawah)*100,2) ?>%
                  <?php endif ?>
                </td>
                <?php if ($total_kanan_bawah == 0): ?>
                  <?php $total_persen_kanan_bawah += 0 ?>
                <?php else: ?>
                  <?php $total_persen_kanan_bawah += (($subtotal_kanan / $total_kanan_bawah)*100) ?>
                <?php endif ?>

              </tr>

        <?php }
      ?>
      <tr>
        <td>-</td>
        <td nowrap>TOTAL</td>

        <?php foreach ($data as $tahun => $data_val): ?>
          <?php foreach ($data_val as $bulan => $value): ?>
            <td><?= $subtotal_bawah[$tahun."".$bulan] ?></td>
            <td><?= round($rata2_bawah[$tahun."".$bulan],2) ?></td>
            <td><?= $persen_bawah[$tahun."".$bulan] ?>%</td>
          <?php endforeach ?>
        <?php endforeach ?>

        <td><?= $total_kanan_bawah ?></td>
        <td><?= round($total_kanan_bawah / 30, 2) ?></td>
        <td><?= $total_persen_kanan_bawah ?>%</td>

      </tr>
    </tbody>

  </table>
</div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="myModal" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

    </div>
  </div>
</div>

<script>
  $(function () {
    $('#example2').DataTable({
      "paging": false,
      "ordering": false,
      "info": false,
      "autoWidth": false,
      "filter": false,
      // "responsive": true,
    });
  });
</script>

<script>
  $(function () {
    $('#example3').DataTable({
      "paging": false,
      "ordering": false,
      "info": false,
      "autoWidth": false,
      "filter": false,
      // "responsive": true,
    });
  });
</script>

<script type="text/javascript">
  $(document).ready(function(){
    $('.openPopup').on('click',function(){
      var dataURL = $(this).attr('data-href');
      $('.modal-content').load(dataURL,function(){
        $('#myModal').modal({show:true});
      });
    }); 
  });
</script>

<script type="text/javascript">
  function exportexcel(awal, akhir){
    var form = $('<form target="_blank" action="view/laporan_pengunjung/excel_laporan_pengunjung_perjambulan.php" method="post">' +
      '<input type="hidden" name="tgl_awal" value="' + awal + '" />' +
      '<input type="hidden" name="tgl_akhir" value="' + akhir + '" />' +
      '</form>');
    $('body').append(form);
    form.submit();
    // alert(awal+'    '+akhir);
  }
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
    var form = $('<form action="?page=pengunjung&action=perjambulan" method="post">' +
      '<input type="hidden" name="tgl_awal" value="' + start + '" />' +
      '<input type="hidden" name="tgl_akhir" value="' + end + '" />' +
      '</form>');
    $('body').append(form);
    form.submit();
    
  });

</script>
