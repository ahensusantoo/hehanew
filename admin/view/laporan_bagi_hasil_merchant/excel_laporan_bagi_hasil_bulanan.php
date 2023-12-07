<?php 

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Bagi_Hasil_Bulanan.xls");

require_once '../../templates/koneksi.php';

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
        
        // $list_pendapatan_pershift = $db->query(" SELECT c.nama_shift, c.id_shift
        //                                         FROM merchant_transaksi b 
        //                                         JOIN shift c ON b.kd_shift = c.id_shift
        //                                         WHERE b.kd_merchant='$_SESSION[kd_merchant]'
        //                                         GROUP BY b.kd_shift ")->fetch_all(MYSQLI_ASSOC);

        $list_pendapatan_pershift = $db->query(" SELECT *
                                                    FROM shift
                                                    WHERE status_aktif_shift = 'Y' ")->fetch_all(MYSQLI_ASSOC);

          $start = $month = strtotime($tgl_awal);
          $end = strtotime($tgl_akhir);
          while($month <= $end)
          { 
        
            // $date_loop =  date('Y-m-d', $month);
            $bulan_loop =  date('m', $month);
            $tahun_loop =  date('Y', $month);
            
            // var_dump($list_pendapatan_pershift);die();
            
            foreach ($list_pendapatan_pershift as $key2 => $value_shift) {
              
              $result_pershit = @$db->query("SELECT SUM(a.tagihan_nota) AS jml 
                                                FROM merchant_transaksi a    
                                                WHERE a.kd_merchant='$_SESSION[kd_merchant]'
                                                	AND a.status_transaksi ='2'
                                                    AND a.kd_jenis_pembayaran !=''
                                                    AND a.kd_shift ='$value_shift[id_shift]'
                                                    AND YEAR(a.tgl_input_transaksi) = '$tahun_loop'
                                                    AND MONTH(a.tgl_input_transaksi) = '$bulan_loop'
                                                     ")->fetch_assoc()['jml'];
            //   var_dump($result_pershit);
              if ($result_pershit == "") {
                $result_pershit =0;
              }
                $data['shift'] = $list_pendapatan_pershift;
                
              $subdata['nama_shift'] = $value_shift['nama_shift'];
              $subdata['terjual'] = $result_pershit;
        
            //   $data['periode'][$date_loop][$value_shift['id_shift']."-".$value_shift['nama_shift']] =  $subdata;
              $data['periode'][$bulan_loop." ".$tahun_loop][$value_shift['id_shift']] =  $subdata;
        
            }
        
            $month = strtotime("+1 days", $month);
        
          }
          
        //   echo "<pre>";
        //   echo print_r($data);
    }


?>
<style type="text/css" media="screen">
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

<div style="min-width: 100px;"> 
    <div id="tab_tabel_closing" style="width: 850px;">
		<table style=" width: 100%">
            <tbody>
                <tr>
                    <td>Priode</td>
                    <td> : </td>
                    <td>PERIODE <?= bulanAbjad($bulan_awal) ?> <?= $tahun_awal ?> sd <?= bulanAbjad($bulan_akhir) ?> <?= $tahun_akhir ?></td>
                </tr>
            </tbody>
        </table>
        <hr>
        <table class="tabel_border" border="1" style="margin-top: 10px; margin-left: auto; margin-right: auto; width: 100%">
            <thead>
            	<tr>
            	    <th class="text-center" rowspan="2">Tanggal</th>
            	    <th class="text-center" colspan="<?=COUNT($list_pendapatan_pershift)+1 ?>">Omset</th>
            	    <th class="text-center" colspan="2">Bagi Hasil</th>
            	</tr>
            	<tr>
            	    <?php foreach($data['shift'] as $key => $shift ) { ?>
                	    <th><?=$shift['nama_shift'] ?></th>
            	    <?php } ?>
            	    <th>Omset</th>
            	    <th>HEHA (22%)</th>
            	    <th>OKY (78%)</th>
            	</tr>
            </thead>
            <tbody>
                <?php $total_all_omset = 0; $all_heha=0; $all_oky=0; ?>
                <?php foreach($data['periode'] as $tanggal => $key ) { ?>
                    <tr>
                        <?php $pecah_tanggal = explode(" ",$tanggal) ?>
                        <td><?= bulanAbjad($pecah_tanggal['0']) ?> <?= $pecah_tanggal['1'] ?> </td>
                        <?php foreach($data['shift'] as $key_shift => $shift ) { ?>
                	        <td><?= number_format($data['periode'][$tanggal][$shift['id_shift']]['terjual']) ?></td>
                            <?php  
                                if (isset($omset_pertanggal[$tanggal]) ) {
                                    $omset_pertanggal[$tanggal] += $data['periode'][$tanggal][$shift['id_shift']]['terjual']; 
                                    
                                }else{
                                    $omset_pertanggal[$tanggal] = $data['periode'][$tanggal][$shift['id_shift']]['terjual'];
                                    
                                }
                                
                                if (isset($omset_pershift[$key_shift]) ) {
                                    $omset_pershift[$key_shift] += $data['periode'][$tanggal][$shift['id_shift']]['terjual']; 
                                    
                                }else{
                                    $omset_pershift[$key_shift] = $data['periode'][$tanggal][$shift['id_shift']]['terjual'];
                                    
                                }
                            ?>   
                	    <?php } ?>
                	    <?php $total_all_omset += $omset_pertanggal[$tanggal]; $all_heha +=($omset_pertanggal[$tanggal]*0.22); $all_oky +=($omset_pertanggal[$tanggal]*0.78);  ?>
                        <td><?= number_format($omset_pertanggal[$tanggal]) ?></td>
                        <td><?= number_format($omset_pertanggal[$tanggal]*0.22) ?></td>
                        <td><?= number_format($omset_pertanggal[$tanggal]*0.78) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <?php foreach($data['shift'] as $id_shift => $shift ) { ?>
                        <th><?= number_format($omset_pershift[$id_shift]) ?></th>
                    <?php } ?>
                    <th><?=number_format($total_all_omset) ?></th>
                    <th><?=number_format($all_heha) ?></th>
                    <th><?=number_format($all_oky) ?></th>
                </tr>
            </tfoot>
        </table>
	</div>
</div>

<?php
	// $tgl = $list_bln['tgl_input_detail'];
	// $bulan = date_format(date_create($tgl),"m");
	   // echo "<pre>";
	   // echo print_r($list_produk);
	   // echo die();

// 	 function bulanAbjad($bulan){
// 	   switch ($bulan) {
// 	     case "1":
// 	       return 'Januari';
// 	       break;
// 	     case "2":
// 	       return 'Februari';
// 	       break;
// 	     case "3":
// 	       return 'Maret';
// 	       break;
// 	     case "4":
// 	       return 'April';
// 	       break;
// 	     case "5":
// 	       return 'Mei';
// 	       break;
// 	     case "6":
// 	       return 'Juni';
// 	       break;
// 	     case "7":
// 	       return 'Juli';
// 	       break;
// 	     case "8":
// 	       return 'Agustus';
// 	       break;
// 	     case "9":
// 	       return 'September';
// 	       break;
// 	     case "10":
// 	       return 'Oktober';
// 	       break;
// 	     case "11":
// 	       return 'November';
// 	       break;
// 	     case "12":
// 	       return 'Desember';
// 	       break;


// 	     default:
// 	       return 'Bulan tidak terdaftar';
// 	   }
// 	 }

	 // echo bulanAbjad($bulan);  

?>

