<?php
  include("../../templates/koneksi.php");

  
  header("Content-type: application/vnd-ms-excel");
  header("Content-Disposition: attachment; filename=Laporan Harian.xls");

  

  if(empty($_POST['tgl_awal']) || empty($_POST['tgl_akhir'])){
    $tgl_awal = date('Y-m-d');
    $tgl_akhir = date('Y-m-d');
  } else {
    $tgl_awal = date('Y-m-d', strtotime($_POST['tgl_awal']));
    $tgl_akhir = date('Y-m-d', strtotime($_POST['tgl_akhir']));
  }

    $list_shift = $db->query("SELECT * FROM shift")->fetch_all(MYSQLI_ASSOC);
    $list_merchant = $db->query("SELECT * FROM merchant");
    
    foreach($list_shift as $key_on => $value_shift){
        $total_semua['shift'][$value_shift['id_shift']]['omset_bruto'] = 0;
        $total_semua['shift'][$value_shift['id_shift']]['diskon_perbarang'] = 0;
        $total_semua['shift'][$value_shift['id_shift']]['diskon_transaksi'] = 0;
        $total_semua['shift'][$value_shift['id_shift']]['diskon_total'] = 0;
        $total_semua['shift'][$value_shift['id_shift']]['omset_bersih'] = 0;
    } 
    
    $total_semua['kanan']['omset_bruto'] = 0;
    $total_semua['kanan']['diskon_perbarang'] = 0;
    $total_semua['kanan']['diskon_transaksi'] = 0;
    $total_semua['kanan']['diskon_total'] = 0;
    $total_semua['kanan']['omset_bersih'] = 0;
    
    $start = $month = strtotime($tgl_awal);
    $end = strtotime($tgl_akhir);
    while($month <= $end){
        $tgl_loop =  date('Y-m-d', $month);
        
        // INISIALISASI
        foreach($list_shift as $key_on => $value_shift){
            $total_pertgl['shift'][$value_shift['id_shift']]['omset_bruto'] = 0;
            $total_pertgl['shift'][$value_shift['id_shift']]['diskon_perbarang'] = 0;
            $total_pertgl['shift'][$value_shift['id_shift']]['diskon_transaksi'] = 0;
            $total_pertgl['shift'][$value_shift['id_shift']]['diskon_total'] = 0;
            $total_pertgl['shift'][$value_shift['id_shift']]['omset_bersih'] = 0;
        }
        
        foreach($list_merchant as $key_on => $value_merchant){
            
            // INISIALISASI
            $data[$tgl_loop]['merchant'][$value_merchant['id_merchant']]['nama_merchant'] = $value_merchant['nama_merchant'];
            foreach($list_shift as $key_on => $value_shift){
                $data[$tgl_loop]['merchant'][$value_merchant['id_merchant']]['shift'][$value_shift['id_shift']]['omset_bruto'] = 0;
                $data[$tgl_loop]['merchant'][$value_merchant['id_merchant']]['shift'][$value_shift['id_shift']]['diskon_perbarang'] = 0;
                $data[$tgl_loop]['merchant'][$value_merchant['id_merchant']]['shift'][$value_shift['id_shift']]['diskon_transaksi'] = 0;
                $data[$tgl_loop]['merchant'][$value_merchant['id_merchant']]['shift'][$value_shift['id_shift']]['diskon_total'] = 0;
                $data[$tgl_loop]['merchant'][$value_merchant['id_merchant']]['shift'][$value_shift['id_shift']]['omset_bersih'] = 0;
            }
            
            $data[$tgl_loop]['merchant'][$value_merchant['id_merchant']]['total_pertgl_merchant']['omset_bruto'] = 0;
            $data[$tgl_loop]['merchant'][$value_merchant['id_merchant']]['total_pertgl_merchant']['diskon_perbarang'] = 0;
            $data[$tgl_loop]['merchant'][$value_merchant['id_merchant']]['total_pertgl_merchant']['diskon_transaksi'] = 0;
            $data[$tgl_loop]['merchant'][$value_merchant['id_merchant']]['total_pertgl_merchant']['diskon_total'] = 0;
            $data[$tgl_loop]['merchant'][$value_merchant['id_merchant']]['total_pertgl_merchant']['omset_bersih'] = 0;
            
            $data[$tgl_loop]['total_pertgl']['omset_bruto'] = 0;
            $omset_bruto_tgl_stall_kanan = 0;
            $diskon_perbarang_tgl_stall_kanan = 0;
            $diskon_transaksi_tgl_stall_kanan = 0;
            $total_diskon_tgl_stall_kanan = 0;
            $total_tgl_stall_kanan = 0;
                   
            
            // GET DATA
            foreach($list_shift as $key_on => $value_shift){
                    
                $omset_bruto = $db->query("SELECT COALESCE(SUM(A.harga_produk*A.jumlah_produk), 0) AS omset_bruto FROM merchant_transaksi_detail A JOIN merchant_transaksi B ON A.kd_merchant_transaksi=B.id_merchant_transaksi WHERE DATE(A.tgl_input_detail)='$tgl_loop' AND A.status_transaksi_detail='2' AND A.kd_merchant='$value_merchant[id_merchant]' AND B.kd_jenis_pembayaran!='' AND B.status_transaksi='2' AND B.kd_shift='$value_shift[id_shift]'")->fetch_assoc()['omset_bruto'];
                
                $diskon_perbarang = $db->query("SELECT COALESCE(SUM((A.harga_produk-A.harga_setelah_diskon)*A.jumlah_produk), 0) AS diskon_perbarang FROM merchant_transaksi_detail A JOIN merchant_transaksi B ON A.kd_merchant_transaksi=B.id_merchant_transaksi WHERE DATE(A.tgl_input_detail)='$tgl_loop' AND A.status_transaksi_detail='2' AND A.kd_merchant='$value_merchant[id_merchant]' AND B.kd_jenis_pembayaran!='' AND B.status_transaksi='2' AND B.kd_shift='$value_shift[id_shift]'")->fetch_assoc()['diskon_perbarang'];
                
                $diskon_transaksi = $db->query("SELECT COALESCE(SUM(A.diskon),0) AS diskon_transaksi FROM merchant_transaksi A WHERE A.kd_merchant='$value_merchant[id_merchant]' AND A.status_transaksi='2' AND DATE(A.tgl_input_transaksi)='$tgl_loop' AND A.kd_jenis_pembayaran!='' AND A.kd_shift='$value_shift[id_shift]'")->fetch_assoc()['diskon_transaksi'];
                
                $total_diskon = $diskon_perbarang + $diskon_transaksi;
                
                // echo "<br> $omset_bruto";
                $data[$tgl_loop]['merchant'][$value_merchant['id_merchant']]['shift'][$value_shift['id_shift']]['omset_bruto'] = $omset_bruto;
                $data[$tgl_loop]['merchant'][$value_merchant['id_merchant']]['shift'][$value_shift['id_shift']]['diskon_perbarang'] = $diskon_perbarang;
                $data[$tgl_loop]['merchant'][$value_merchant['id_merchant']]['shift'][$value_shift['id_shift']]['diskon_transaksi'] = $diskon_transaksi;
                $data[$tgl_loop]['merchant'][$value_merchant['id_merchant']]['shift'][$value_shift['id_shift']]['diskon_total'] = $total_diskon;
                $data[$tgl_loop]['merchant'][$value_merchant['id_merchant']]['shift'][$value_shift['id_shift']]['omset_bersih'] = ($omset_bruto - $total_diskon);
                
                $omset_bruto_tgl_stall_kanan += $omset_bruto;
                $diskon_perbarang_tgl_stall_kanan += $diskon_perbarang;
                $diskon_transaksi_tgl_stall_kanan += $diskon_transaksi;
                $total_diskon_tgl_stall_kanan += $total_diskon;
                $total_tgl_stall_kanan += ($omset_bruto - $total_diskon);
                
                
                $total_pertgl['shift'][$value_shift['id_shift']]['omset_bruto'] += $omset_bruto;
                $total_pertgl['shift'][$value_shift['id_shift']]['diskon_perbarang'] += $diskon_perbarang;
                $total_pertgl['shift'][$value_shift['id_shift']]['diskon_transaksi'] += $diskon_transaksi;
                $total_pertgl['shift'][$value_shift['id_shift']]['diskon_total'] += $total_diskon;
                $total_pertgl['shift'][$value_shift['id_shift']]['omset_bersih'] += ($omset_bruto - $total_diskon);
                
                
                // $total_semua['shift'][$value_shift['id_shift']]['omset_bruto'] += $omset_bruto;
                // $total_semua['shift'][$value_shift['id_shift']]['diskon_perbarang'] += $diskon_perbarang;
                // $total_semua['shift'][$value_shift['id_shift']]['diskon_transaksi'] += $diskon_transaksi;
                // $total_semua['shift'][$value_shift['id_shift']]['diskon_total'] += $total_diskon;
                // $total_semua['shift'][$value_shift['id_shift']]['omset_bersih'] += ($omset_bruto - $total_diskon);
                
                // $total_semua['kanan']['omset_bruto'] += $omset_bruto;
                // $total_semua['kanan']['diskon_perbarang'] += $diskon_perbarang;
                // $total_semua['kanan']['diskon_transaksi'] += $diskon_transaksi;
                // $total_semua['kanan']['diskon_total'] += $total_diskon;
                // $total_semua['kanan']['omset_bersih'] += ($omset_bruto - $total_diskon);
                
                
            }
            
            $data[$tgl_loop]['merchant'][$value_merchant['id_merchant']]['total_pertgl_merchant']['omset_bruto'] += $omset_bruto_tgl_stall_kanan;
            $data[$tgl_loop]['merchant'][$value_merchant['id_merchant']]['total_pertgl_merchant']['diskon_perbarang'] += $diskon_perbarang_tgl_stall_kanan;
            $data[$tgl_loop]['merchant'][$value_merchant['id_merchant']]['total_pertgl_merchant']['diskon_transaksi'] += $diskon_transaksi_tgl_stall_kanan;
            $data[$tgl_loop]['merchant'][$value_merchant['id_merchant']]['total_pertgl_merchant']['diskon_total'] += $total_diskon_tgl_stall_kanan;
            $data[$tgl_loop]['merchant'][$value_merchant['id_merchant']]['total_pertgl_merchant']['omset_bersih'] += $total_tgl_stall_kanan;
            
            
            
        }
        
        
        $data[$tgl_loop]['total_pertgl']['omset_bruto'] = 0;
        $data[$tgl_loop]['total_pertgl']['diskon_perbarang'] = 0;
        $data[$tgl_loop]['total_pertgl']['diskon_transaksi'] = 0;
        $data[$tgl_loop]['total_pertgl']['diskon_total'] = 0;
        $data[$tgl_loop]['total_pertgl']['omset_bersih'] = 0;
        
        foreach($list_shift as $key_on => $value_shift){
            $data[$tgl_loop]['total_pertgl']['shift'][$value_shift['id_shift']]['omset_bruto'] = $total_pertgl['shift'][$value_shift['id_shift']]['omset_bruto'];
            $data[$tgl_loop]['total_pertgl']['shift'][$value_shift['id_shift']]['diskon_perbarang'] = $total_pertgl['shift'][$value_shift['id_shift']]['diskon_perbarang'];
            $data[$tgl_loop]['total_pertgl']['shift'][$value_shift['id_shift']]['diskon_transaksi'] = $total_pertgl['shift'][$value_shift['id_shift']]['diskon_transaksi'];
            $data[$tgl_loop]['total_pertgl']['shift'][$value_shift['id_shift']]['diskon_total'] =  $total_pertgl['shift'][$value_shift['id_shift']]['diskon_total'];
            $data[$tgl_loop]['total_pertgl']['shift'][$value_shift['id_shift']]['omset_bersih'] = $total_pertgl['shift'][$value_shift['id_shift']]['omset_bersih'];
            
            $data[$tgl_loop]['total_pertgl']['omset_bruto'] += $total_pertgl['shift'][$value_shift['id_shift']]['omset_bruto'];
            $data[$tgl_loop]['total_pertgl']['diskon_perbarang'] += $total_pertgl['shift'][$value_shift['id_shift']]['diskon_perbarang'];
            $data[$tgl_loop]['total_pertgl']['diskon_transaksi'] += $total_pertgl['shift'][$value_shift['id_shift']]['diskon_transaksi'];
            $data[$tgl_loop]['total_pertgl']['diskon_total'] += $total_pertgl['shift'][$value_shift['id_shift']]['diskon_total'];
            $data[$tgl_loop]['total_pertgl']['omset_bersih'] += $total_pertgl['shift'][$value_shift['id_shift']]['omset_bersih'];
        }
        
        // $data[$tgl_loop]['total_pertgl']['omset_bruto'] += $total_pertgl['shift'][$value_shift['id_shift']]['omset_bruto'];
        // $data[$tgl_loop]['total_pertgl']['diskon_perbarang'] += $total_pertgl['shift'][$value_shift['id_shift']]['diskon_perbarang'];
        // $data[$tgl_loop]['total_pertgl']['diskon_transaksi'] += $total_pertgl['shift'][$value_shift['id_shift']]['diskon_transaksi'];
        // $data[$tgl_loop]['total_pertgl']['diskon_total'] +=  $total_pertgl['shift'][$value_shift['id_shift']]['diskon_total'];
        // $data[$tgl_loop]['total_pertgl']['omset_bersih'] += $total_pertgl['shift'][$value_shift['id_shift']]['omset_bersih'];
        
        
        
        foreach($list_shift as $key_on => $value_shift){
            $total_semua['shift'][$value_shift['id_shift']]['omset_bruto'] += $data[$tgl_loop]['total_pertgl']['shift'][$value_shift['id_shift']]['omset_bruto'];
            $total_semua['shift'][$value_shift['id_shift']]['diskon_perbarang'] += $data[$tgl_loop]['total_pertgl']['shift'][$value_shift['id_shift']]['diskon_perbarang'];
            $total_semua['shift'][$value_shift['id_shift']]['diskon_transaksi'] += $data[$tgl_loop]['total_pertgl']['shift'][$value_shift['id_shift']]['diskon_transaksi'];
            $total_semua['shift'][$value_shift['id_shift']]['diskon_total'] += $data[$tgl_loop]['total_pertgl']['shift'][$value_shift['id_shift']]['diskon_total'];
            $total_semua['shift'][$value_shift['id_shift']]['omset_bersih'] += $data[$tgl_loop]['total_pertgl']['shift'][$value_shift['id_shift']]['omset_bersih'];
            
            $total_semua['kanan']['omset_bruto'] += $data[$tgl_loop]['total_pertgl']['shift'][$value_shift['id_shift']]['omset_bruto'];
            $total_semua['kanan']['diskon_perbarang'] += $data[$tgl_loop]['total_pertgl']['shift'][$value_shift['id_shift']]['diskon_perbarang'];
            $total_semua['kanan']['diskon_transaksi'] += $data[$tgl_loop]['total_pertgl']['shift'][$value_shift['id_shift']]['diskon_transaksi'];
            $total_semua['kanan']['diskon_total'] += $data[$tgl_loop]['total_pertgl']['shift'][$value_shift['id_shift']]['diskon_total'];
            $total_semua['kanan']['omset_bersih'] += $data[$tgl_loop]['total_pertgl']['shift'][$value_shift['id_shift']]['omset_bersih'];
        } 
        
        // $total_pertgl['shift'][$value_shift['id_shift']]['omset_bruto'] = 0;
        // $total_pertgl['shift'][$value_shift['id_shift']]['diskon_perbarang'] = 0;
        // $total_pertgl['shift'][$value_shift['id_shift']]['diskon_transaksi'] = 0;
        // $total_pertgl['shift'][$value_shift['id_shift']]['diskon_total'] = 0;
        // $total_pertgl['shift'][$value_shift['id_shift']]['omset_bersih'] = 0;
        
        
        $month = strtotime("+1 day", $month);
        
        
    }
    

?>


<style type="text/css">
  table{
    margin: 20px auto;
    border-collapse: collapse;
  }
  table th,
  table td{
    border: 1px solid #3c3c3c;
    padding: 3px 8px;
    font-size: 80%;
  }
</style>


<table>
  <thead>
    <tr>
      <th colspan="4" style="text-align: center;">Laporan Penjualan Harian</th>
    </tr>
    <tr><th colspan="4" style="text-align: center;">Periode <?= $tgl_awal ?> s/d <?= $tgl_akhir ?> </th></tr>
    <tr><th></th></tr>
  </thead>
</table>


<table border="1" class="table table-bordered table-striped text-sm">
  <thead>
    <tr>
      <th rowspan="2">Tanggal</th>
      <th rowspan="2" nowrap >Nama Stall</th>
      <?php foreach($list_shift as $key_shift => $value_shift): ?>
        <th colspan="5"><?= $value_shift['nama_shift'] ?></th>
      <?php endforeach ?>
      <th colspan="5">TOTAL</th>
    </tr>
    <tr>
      <?php foreach($list_shift as $key_shift => $value_shift): ?>
        <th>Omset Bruto</th>
        <th>Diskon Perbarang</th>
        <th>Diskon Transaksi</th>
        <th>Total Diskon</th>
        <th>Omset Bersih</th>
      <?php endforeach ?>
      <th>Omset Bruto</th>
      <th>Diskon Perbarang</th>
      <th>Diskon Transaksi</th>
      <th>Total Diskon</th>
      <th>Omset Bersih</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($data as $key_tgl => $value_pertgl): ?>
      <?php if(isset($value_pertgl['merchant'])) : ?>
        <?php foreach($value_pertgl['merchant'] as $key_stall => $value_stall): ?>
          <tr>
            <?php if(!isset($jml_rowspan_pertgl[$key_tgl])): ?>
              <?php $jml_rowspan_pertgl[$key_tgl] = count($value_pertgl['merchant']) ?>
              <td rowspan="<?= $jml_rowspan_pertgl[$key_tgl] ?>" nowrap><?= $key_tgl ?></td>
            <?php endif; ?>
            <td><?= $value_stall['nama_merchant'] ?></td>
            <?php foreach($list_shift as $key_shift => $value_shift): ?>
              <td><?= (@$data[$key_tgl]['merchant'][$key_stall]['shift'][$value_shift['id_shift']]['omset_bruto']) ?></td>
              <td><?= (@$data[$key_tgl]['merchant'][$key_stall]['shift'][$value_shift['id_shift']]['diskon_perbarang']) ?></td>
              <td><?= (@$data[$key_tgl]['merchant'][$key_stall]['shift'][$value_shift['id_shift']]['diskon_transaksi']) ?></td>
              <td><?= (@$data[$key_tgl]['merchant'][$key_stall]['shift'][$value_shift['id_shift']]['diskon_total']) ?></td>
              <td><?= (@$data[$key_tgl]['merchant'][$key_stall]['shift'][$value_shift['id_shift']]['omset_bersih']) ?></td>
            <?php endforeach ?>
            <td><?= (@$data[$key_tgl]['merchant'][$key_stall]['total_pertgl_merchant']['omset_bruto'])?></td>
            <td><?= (@$data[$key_tgl]['merchant'][$key_stall]['total_pertgl_merchant']['diskon_perbarang']) ?></td>
            <td><?= (@$data[$key_tgl]['merchant'][$key_stall]['total_pertgl_merchant']['diskon_transaksi']) ?></td>
            <td><?= (@$data[$key_tgl]['merchant'][$key_stall]['total_pertgl_merchant']['diskon_total']) ?></td>
            <td><?= (@$data[$key_tgl]['merchant'][$key_stall]['total_pertgl_merchant']['omset_bersih']) ?></td>
          </tr>
        <?php endforeach ?>
      <?php endif; ?>
      <tr style="background-color:#fdfdbd;">
        <td><?= $key_tgl ?></td>
        <td>-</td>
        <?php foreach($list_shift as $key_shift => $value_shift): ?>
          <td><?= (@$data[$key_tgl]['total_pertgl']['shift'][$value_shift['id_shift']]['omset_bruto']) ?></td>
          <td><?= (@$data[$key_tgl]['total_pertgl']['shift'][$value_shift['id_shift']]['diskon_perbarang']) ?></td>
          <td><?= (@$data[$key_tgl]['total_pertgl']['shift'][$value_shift['id_shift']]['diskon_transaksi']) ?></td>
          <td><?= (@$data[$key_tgl]['total_pertgl']['shift'][$value_shift['id_shift']]['diskon_total']) ?></td>
          <td><?= (@$data[$key_tgl]['total_pertgl']['shift'][$value_shift['id_shift']]['omset_bersih']) ?></td>
        <?php endforeach ?>
        <td><?= (@$data[$key_tgl]['total_pertgl']['omset_bruto']) ?></td>
        <td><?= (@$data[$key_tgl]['total_pertgl']['diskon_perbarang']) ?></td>
        <td><?= (@$data[$key_tgl]['total_pertgl']['diskon_transaksi']) ?></td>
        <td><?= (@$data[$key_tgl]['total_pertgl']['diskon_total']) ?></td>
        <td><?= (@$data[$key_tgl]['total_pertgl']['omset_bersih']) ?></td>
      </tr>
    <?php endforeach ?>
  </tbody>
  <tfoot>
    <tr>
      <td colspan="2">TOTAL</td>
      <?php foreach($list_shift as $key_shift => $value_shift): ?>
        <td style="font-weight: bold;"><?= (@$total_semua['shift'][$value_shift['id_shift']]['omset_bruto']) ?></td>
        <td style="font-weight: bold;"><?= (@$total_semua['shift'][$value_shift['id_shift']]['diskon_perbarang']) ?></td>
        <td style="font-weight: bold;"><?= (@$total_semua['shift'][$value_shift['id_shift']]['diskon_transaksi']) ?></td>
        <td style="font-weight: bold;"><?= (@$total_semua['shift'][$value_shift['id_shift']]['diskon_total']) ?></td>
        <td style="font-weight: bold;"><?= (@$total_semua['shift'][$value_shift['id_shift']]['omset_bersih']) ?></td>
      <?php endforeach ?>
      <td style="font-weight: bold;"><?= (@$total_semua['kanan']['omset_bruto']) ?></td>
      <td style="font-weight: bold;"><?= (@$total_semua['kanan']['diskon_perbarang']) ?></td>
      <td style="font-weight: bold;"><?= (@$total_semua['kanan']['diskon_transaksi']) ?></td>
      <td style="font-weight: bold;"><?= (@$total_semua['kanan']['diskon_total']) ?></td>
      <td style="font-weight: bold;"><?= (@$total_semua['kanan']['omset_bersih']) ?></td>
    </tr>
  </tfoot>
</table>
  <table>
      <tbody>
          <tr>
              <td colspan="4" align="center"></td>
          </tr>
      </tbody>
  </table>




