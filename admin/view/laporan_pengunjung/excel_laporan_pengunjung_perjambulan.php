<?php

    include("../../templates/koneksi.php");

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=Laporan Perjam.xls");
    
?>


<?php
if(empty($_POST['tgl_awal']) || empty($_POST['tgl_akhir'])){
  $tgl_awal = date('Y-m-d');
  $tgl_akhir = date('Y-m-d');
} else {
  $tgl_awal = date('Y-m-d', strtotime($_POST['tgl_awal']));
  $tgl_akhir = date('Y-m-d', strtotime($_POST['tgl_akhir']));
}
?>



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

  <table border="1" class="table table-sm text-sm table-bordered">
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

