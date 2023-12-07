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

                                

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <!--<h1 class="m-0 text-dark">LAPORAN BAGI HASIL HARIAN</h1>-->
      </div>
    </div>
  </div>
</div>

<style type="text/css">
  .selectMonths{ position:relative; display:inline-block; }
  .selectMonthsselect {height: 30px; }
  .selectMonths > i{ position:absolute; right:5px; top:5px; opacity:0.35; font-style:normal; font-size:18px; transition:0.2s; pointer-events:none; }
  .selectMonths > input{ text-transform:capitalize; padding-left:10px; cursor:default; cursor:pointer; }
  .selectMonths:hover > i{ opacity:.7; }
  .selectMonths + .selectMonths{ float:none; }
</style>
<link rel="stylesheet" href="plugins/month-range2/picker.css">


<section class="content">
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-12">
        <div class="card">
          <div class="card-header text-bold">
              <span style="font-size: 130%;">Laporan Bagi Hasil Harian</span>   
              <button type="button" id="export_excel" class="btn btn-sm btn-outline-secondary btn_cetak float-right" style="height: 81%"><i class="fas fa-download"></i> Export</button>
          </div>
          <div class="card-body">
          
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
          
            <?php if(!isset($data )) : ?>
                <center>
                    
                    <p class="text-red"></p>
                    Tidak ada data yang tersedia pada periode<br>
                    PERIODE <?= bulanAbjad($bulan_awal) ?> <?= $tahun_awal ?> sd <?= bulanAbjad($bulan_akhir) ?> <?= $tahun_akhir ?><br><br>
                    <i class="fa-2x fas fa-times-circle"></i>
                    <hr>
                </center>
                <?php goto tanpaData; ?>

            <?php endif; ?>
            <div class="table-responsive">
                <div style="min-width: 100px;"> 
                    <div id="tab_tabel_closing" style="width: 100%;">
                        <!-- <center><img src="dist/img/hehaocen.png" width="200px"></center> -->
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
                        <table class="table table-bordered table-striped table-sm">
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
            </div>
            
            <hr class="mt-5">
            
            <?php tanpaData: ?>
            
            
            
            


<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.js"></script> 
<script src="plugins/month-range2/tether.min.js"></script>
<script src="plugins/month-range2/datePicker.js"></script>

<script type="text/javascript">
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
    var form = $('<form action="?page=laporan_bagi_hasil&action=bulanan" method="post">' +
      '<input type="hidden" name="tgl_awal" value="' + start + '" />' +
      '<input type="hidden" name="tgl_akhir" value="' + end + '" />' +
      '</form>');
    $('body').append(form);
    form.submit();
  });

  $(document).on('click', '#pilih_kasir', function(){
        $(function() { 
          $("#export_excel").click(function() {
             var mulai = "<?= @$mulai ?>"
             var akhir = "<?= @$akhir ?>"
    
             window.open('view/laporan_bagi_hasil_merchant/excel_laporan_bagi_hasil_harian.php?mulai='+mulai+ '&akhir=' +akhir, '__blank');
    
          }); 
        });
  
  });
</script>