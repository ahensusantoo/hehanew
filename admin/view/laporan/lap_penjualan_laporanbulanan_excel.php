<?php
  include("../../templates/koneksi.php");

  
  header("Content-type: application/vnd-ms-excel");
  header("Content-Disposition: attachment; filename=Laporan Bulananan.xls");

  
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
    
    $tanggal_awal = substr($tgl_awal,8,2);
    $tanggal_akhir = substr($tgl_akhir,8,2); 

    if( $tgl_awal != "" AND $tgl_akhir != ""){
        
        $list_pendapatan_pershift = $db->query(" SELECT a.id_shift, a.nama_shift
                                                FROM shift a ")->fetch_all(MYSQLI_ASSOC);
        $num_row = COUNT($list_pendapatan_pershift)+1;
            // var_dump($num_row);
            
        $list_merchant = $db->query(" SELECT a.id_merchant, a.nama_merchant
                                        FROM merchant a ")->fetch_all(MYSQLI_ASSOC); 
        // var_dump($list_merchant);die();
        $start = $month = strtotime($tgl_awal);
        $end = strtotime($tgl_akhir);
        
        while($month <= $end) { 
            // $date_loop =  date('Y-m-d', $month);
            $bulan_loop =  date('m', $month);
            $tahun_loop =  date('Y', $month);
            
            // var_dump($list_pendapatan_pershift);die();
            
            foreach ($list_merchant as $key => $merchant) {
                
                foreach ($list_pendapatan_pershift as $key2 => $value_shift) {
                  
                    $hitung_omset_bruto = $db->query("SELECT  COALESCE(SUM(a.harga_produk * a.jumlah_produk),0) AS jml  
                                                    FROM merchant_transaksi_detail a
                                                    JOIN merchant_transaksi b on a.kd_merchant_transaksi = b.id_merchant_transaksi
                                                    WHERE b.kd_merchant='$merchant[id_merchant]'
                                                        AND b.status_transaksi ='2'
                                                        AND b.kd_jenis_pembayaran !=''
                                                        AND b.kd_shift ='$value_shift[id_shift]'
                                                        AND YEAR(b.tgl_input_transaksi) = '$tahun_loop'
                                                        AND MONTH(b.tgl_input_transaksi) = '$bulan_loop'
                                                         ")->fetch_assoc()['jml'];
                    
                    $diskon = $db->query("SELECT COALESCE(SUM((a.harga_produk-a.harga_setelah_diskon)*a.jumlah_produk),0) AS jml 
                                            FROM merchant_transaksi_detail a 
                                            JOIN merchant_transaksi b ON a.kd_merchant_transaksi=b.id_merchant_transaksi 
                                            WHERE b.kd_jenis_pembayaran!='' 
                                                AND a.kd_merchant='$merchant[id_merchant]'
                                                AND b.kd_shift ='$value_shift[id_shift]' 
                                                AND a.status_transaksi_detail='2'
                                                AND YEAR(b.tgl_input_transaksi) = '$tahun_loop'
                                                AND MONTH(b.tgl_input_transaksi) = '$bulan_loop'
                                                ")->fetch_assoc()['jml'];
                                                         
                    $diskon_pertransaksi = $db->query("SELECT COALESCE(SUM(a.diskon),0) AS jml 
                                                    FROM merchant_transaksi a    
                                                    WHERE a.kd_merchant='$merchant[id_merchant]'
                                                        AND a.status_transaksi ='2'
                                                        AND a.kd_jenis_pembayaran !=''
                                                        AND a.kd_shift ='$value_shift[id_shift]'
                                                        AND YEAR(a.tgl_input_transaksi) = '$tahun_loop'
                                                        AND MONTH(a.tgl_input_transaksi) = '$bulan_loop'
                                                         ")->fetch_assoc()['jml'];
                                                         
                    $total_bersih = $db->query("SELECT COALESCE(SUM(a.tagihan_nota),0) AS jml 
                                                    FROM merchant_transaksi a    
                                                    WHERE a.kd_merchant='$merchant[id_merchant]'
                                                        AND a.status_transaksi ='2'
                                                        AND a.kd_jenis_pembayaran !=''
                                                        AND a.kd_shift ='$value_shift[id_shift]'
                                                        AND YEAR(a.tgl_input_transaksi) = '$tahun_loop'
                                                        AND MONTH(a.tgl_input_transaksi) = '$bulan_loop'
                                                         ")->fetch_assoc()['jml'];
                //   var_dump($result_pershit);
                    $data['shift'] = $list_pendapatan_pershift;
                
                  $subdata['nama_shift'] = $value_shift['nama_shift'];
                  $subdata['omset_bruto'] = $hitung_omset_bruto;
                  $subdata['diskon'] = $diskon;
                  $subdata['diskon_pertransaksi'] = $diskon_pertransaksi;
                  $subdata['total_bersih'] = $total_bersih;
                
                //   $data['periode'][$date_loop][$value_shift['id_shift']."-".$value_shift['nama_shift']] =  $subdata;
                  $data['periode'][$bulan_loop." ".$tahun_loop][$merchant['nama_merchant']][$value_shift['id_shift']] =  $subdata;
            
                }
            }
        
            $month = strtotime("+1 month", $month);
        
          }
          
        //   echo "<pre>";
        //   echo print_r($data);
    }


?>
    

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
      <th colspan="4" style="text-align: center;">Laporan Bulanan</th>
    </tr>
    <tr><th colspan="4" style="text-align: center;">Periode <?= $tgl_awal ?> s/d <?= $tgl_akhir ?> </th></tr>
    <tr><th></th></tr>
  </thead>
</table>


<table border="1" id="example" class="table table-bordered table-striped text-sm">
    <thead>
        <tr>
            <th class="text-center" rowspan="2">Bulan</th>
            <th class="text-center" rowspan="2">Nama Stall</th>
            <?php foreach($data['shift'] as $key => $shift) { ?>
                <th class="text-center" colspan="5"><?=$shift['nama_shift']?></th>
            <?php } ?>
            <th class="text-center" colspan="5">Total</th>
        </tr>
        <tr>
            <?php foreach($data['shift'] as $key => $shift) { ?>
                <th>Omset Bruto</th>
                <th>Diskon Produk</th>
                <th>Diskon Transaksi</th>
                <th>Total Diskon</th>
                <th>Omset Bersih</th>
            <?php } ?>
                <th>Omset Bruto</th>
                <th>Diskon Produk</th>
                <th>Diskon Transaksi</th>
                <th>Total Diskon</th>
                <th>Omset Bersih</th>
        </tr>
    </thead>
    <tbody>
        <?php 
            foreach($data['shift'] as $key3 => $shift) { 
                $final_shift_omset_bruto[$shift['id_shift']] = 0;
                $final_shift_diskon_produk[$shift['id_shift']] = 0;
                $final_shift_diskon_transaksi[$shift['id_shift']] = 0;
                $final_shift_total_diskon[$shift['id_shift']] = 0;
                $final_shift_total_bersih[$shift['id_shift']] = 0;
            } 
            $final_omset_bruto[$shift['id_shift']] = 0;
            $final_diskon_produk[$shift['id_shift']] = 0;
            $final_diskon_transaksi[$shift['id_shift']] = 0;
            $final_total_diskon[$shift['id_shift']] = 0;
            $final_total_bersih[$shift['id_shift']] = 0;
        ?>
        <?php foreach($data['periode'] as $tanggal => $key) { ?>
            <?php $jml_rowspan_pertgl = count($key) ?>
            <?php $set_rowspan = false ?>
            <?php foreach($data['shift'] as $key2 => $shift) { 
                $all_tgl_shift_omset_bruto[$tanggal][$shift['id_shift']] = 0;
                $all_tgl_shift_diskon_produk[$tanggal][$shift['id_shift']] = 0;
                $all_tgl_shift_diskon_transaksi[$tanggal][$shift['id_shift']] = 0;
                $all_tgl_shift_total_diskon[$tanggal][$shift['id_shift']] = 0;
                $all_tgl_shift_total_bersih[$tanggal][$shift['id_shift']] = 0;
                $all_tgl_total_omset_bruto[$tanggal][$shift['id_shift']] = 0;
                $all_tgl_total_diskon_produk[$tanggal][$shift['id_shift']] = 0;
                $all_tgl_total_diskon_transaksi[$tanggal][$shift['id_shift']] = 0;
                $all_tgl_total_diskon_gabungan[$tanggal][$shift['id_shift']] = 0;
                $all_tgl_total_omset_bersih[$tanggal][$shift['id_shift']] = 0;
                //$final_omset_bruto[$shift['id_shift']] = 0;
            } ?>
            <?php foreach($key as $id_merchant => $value_shift) { ?>
                <?php
                    $omset_bruto_perstall = 0;
                    $diskon_produk_perstall = 0;
                    $diskon_transaksi_perstall = 0;
                    $diskon_total_diskon_perstall = 0;
                    $diskon_total_bersih_perstall = 0;
                ?>
                <tr>
                    <?php if(!$set_rowspan): ?>
                        <?php $pecah_tanggal = explode(" ",$tanggal) ?>
                        <td rowspan="<?= $jml_rowspan_pertgl ?>"><?= bulanAbjad($pecah_tanggal['0']) ?> <?= $pecah_tanggal['1'] ?></td>
                        <?php $set_rowspan = true; ?>
                    <?php endif; ?>
                    <td><?= $id_merchant ?></td>
                    <?php foreach($data['shift'] as $key_shift => $shift ) { ?>
                        <?php
                            $omset_bruto_perstall += $value_shift[$shift['id_shift']]['omset_bruto'];
                            $diskon_produk_perstall += $value_shift[$shift['id_shift']]['diskon'];
                            $diskon_transaksi_perstall += $value_shift[$shift['id_shift']]['diskon_pertransaksi'];
                            $diskon_total_diskon_perstall += $value_shift[$shift['id_shift']]['diskon_pertransaksi'] + $value_shift[$shift['id_shift']]['diskon'] ;
                            $diskon_total_bersih_perstall += $value_shift[$shift['id_shift']]['total_bersih'] ;
                            $all_tgl_shift_omset_bruto[$tanggal][$shift['id_shift']] += $value_shift[$shift['id_shift']]['omset_bruto'];
                            $all_tgl_shift_diskon_produk[$tanggal][$shift['id_shift']] += $value_shift[$shift['id_shift']]['diskon'];
                            $all_tgl_shift_diskon_transaksi[$tanggal][$shift['id_shift']] += $value_shift[$shift['id_shift']]['diskon_pertransaksi'];
                            $all_tgl_shift_total_diskon[$tanggal][$shift['id_shift']] += $value_shift[$shift['id_shift']]['diskon_pertransaksi'] + $value_shift[$shift['id_shift']]['diskon'];
                            $all_tgl_shift_total_bersih[$tanggal][$shift['id_shift']] += $value_shift[$shift['id_shift']]['total_bersih'];
                        ?>
                        <td><?= number_format($value_shift[$shift['id_shift']]['omset_bruto']) ?></td>
                        <td><?= number_format($value_shift[$shift['id_shift']]['diskon']) ?></td>
                        <td><?= number_format($value_shift[$shift['id_shift']]['diskon_pertransaksi']) ?></td>
                        <td><?= number_format($value_shift[$shift['id_shift']]['diskon_pertransaksi'] + $value_shift[$shift['id_shift']]['diskon']) ?></td>
                        <td><?= number_format($value_shift[$shift['id_shift']]['total_bersih']) ?></td>
                    <?php } ?>
                    <?php
                        $all_tgl_total_omset_bruto[$tanggal][$shift['id_shift']] += $omset_bruto_perstall;
                        $all_tgl_total_diskon_produk[$tanggal][$shift['id_shift']] += $diskon_produk_perstall;
                        $all_tgl_total_diskon_transaksi[$tanggal][$shift['id_shift']] += $diskon_transaksi_perstall;
                        $all_tgl_total_diskon_gabungan[$tanggal][$shift['id_shift']] += $diskon_total_diskon_perstall;
                        $all_tgl_total_omset_bersih[$tanggal][$shift['id_shift']] += $diskon_total_bersih_perstall;
                    ?>
                    <td><?= number_format($omset_bruto_perstall) ?></td>
                    <td><?= number_format($diskon_produk_perstall) ?></td>
                    <td><?= number_format($diskon_transaksi_perstall) ?></td>
                    <td><?= number_format($diskon_total_diskon_perstall) ?></td>
                    <td><?= number_format($diskon_total_bersih_perstall) ?></td>
                </tr>
            <?php } ?>
                <tr>
                    <th class="text-center" colspan="2">Total</th>
                    <?php foreach($data['shift'] as $key_shift => $shift ) { ?>
                        <?php
                            $final_shift_omset_bruto[$shift['id_shift']] += $all_tgl_shift_omset_bruto[$tanggal][$shift['id_shift']];
                            $final_shift_diskon_produk[$shift['id_shift']] += $all_tgl_shift_diskon_produk[$tanggal][$shift['id_shift']];
                            $final_shift_diskon_transaksi[$shift['id_shift']] += $all_tgl_shift_diskon_transaksi[$tanggal][$shift['id_shift']];
                            $final_shift_total_diskon[$shift['id_shift']] += $all_tgl_shift_total_diskon[$tanggal][$shift['id_shift']];
                            $final_shift_total_bersih[$shift['id_shift']] += $all_tgl_shift_total_bersih[$tanggal][$shift['id_shift']];
                        ?>
                        <th><?= number_format($all_tgl_shift_omset_bruto[$tanggal][$shift['id_shift']]) ?></th>
                        <th><?= number_format($all_tgl_shift_diskon_produk[$tanggal][$shift['id_shift']]) ?></th>
                        <th><?= number_format($all_tgl_shift_diskon_transaksi[$tanggal][$shift['id_shift']]) ?></th>
                        <th><?= number_format($all_tgl_shift_total_diskon[$tanggal][$shift['id_shift']]) ?></th>
                        <th><?= number_format($all_tgl_shift_total_bersih[$tanggal][$shift['id_shift']]) ?></th>
                    <?php } 
                        $final_omset_bruto[$shift['id_shift']] += $all_tgl_total_omset_bruto[$tanggal][$shift['id_shift']];
                        $final_diskon_produk[$shift['id_shift']] += $all_tgl_total_diskon_produk[$tanggal][$shift['id_shift']];
                        $final_diskon_transaksi[$shift['id_shift']] += $all_tgl_total_diskon_transaksi[$tanggal][$shift['id_shift']];
                        $final_total_diskon[$shift['id_shift']] += $all_tgl_total_diskon_gabungan[$tanggal][$shift['id_shift']];
                        $final_total_bersih[$shift['id_shift']] += $all_tgl_total_omset_bersih[$tanggal][$shift['id_shift']];
                    ?>
                    <th><?= number_format($all_tgl_total_omset_bruto[$tanggal][$shift['id_shift']]) ?></th>
                    <th><?= number_format($all_tgl_total_diskon_produk[$tanggal][$shift['id_shift']]) ?></th>
                    <th><?= number_format($all_tgl_total_diskon_transaksi[$tanggal][$shift['id_shift']]) ?></th>
                    <th><?= number_format($all_tgl_total_diskon_gabungan[$tanggal][$shift['id_shift']]) ?></th>
                    <th><?= number_format($all_tgl_total_omset_bersih[$tanggal][$shift['id_shift']]) ?></th>
                </tr>
                <tr>
                    <td colspan="20"></td>
                </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <th class="text-center" colspan="2">Total Dari Seluruh</th>
            <?php foreach($data['shift'] as $key_shift => $shift ) { ?>
                <th><?= number_format($final_shift_omset_bruto[$shift['id_shift']]) ?></th>
                <th><?= number_format($final_shift_diskon_produk[$shift['id_shift']]) ?></th>
                <th><?= number_format($final_shift_diskon_transaksi[$shift['id_shift']]) ?></th>
                <th><?= number_format($final_shift_total_diskon[$shift['id_shift']]) ?></th>
                <th><?= number_format($final_shift_total_bersih[$shift['id_shift']]) ?></th>
            <?php } ?>
            <th><?= number_format($final_omset_bruto[$shift['id_shift']]) ?></th>
            <th><?= number_format($final_diskon_produk[$shift['id_shift']]) ?></th>
            <th><?= number_format($final_diskon_transaksi[$shift['id_shift']]) ?></th>
            <th><?= number_format($final_total_diskon[$shift['id_shift']]) ?></th>
            <th><?= number_format($final_total_bersih[$shift['id_shift']]) ?></th>
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




