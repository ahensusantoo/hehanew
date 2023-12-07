<?php 
   
    $mulai      = antiSQLi(@$_GET['mulai']);
    $akhir      = antiSQLi(@$_GET['akhir']);
    if( $mulai != "" AND $akhir != ""){
        
        // $list_pendapatan_pershift = $db->query(" SELECT c.nama_shift, c.id_shift
        //                                         FROM merchant_transaksi b 
        //                                         JOIN shift c ON b.kd_shift = c.id_shift
        //                                         WHERE b.kd_merchant='$_SESSION[kd_merchant]'
        //                                         GROUP BY b.kd_shift ")->fetch_all(MYSQLI_ASSOC);

        $list_pendapatan_pershift = $db->query(" SELECT *
                                                    FROM shift
                                                    WHERE status_aktif_shift = 'Y' ")->fetch_all(MYSQLI_ASSOC);

          $start = $month = strtotime($mulai);
          $end = strtotime($akhir);
          while($month <= $end)
          { 
        
            $date_loop =  date('Y-m-d', $month);
            // $bulan_loop =  date('m', $month);
            // $tahun_loop =  date('Y', $month);
            
            // var_dump($list_pendapatan_pershift);die();
            
            foreach ($list_pendapatan_pershift as $key2 => $value_shift) {
              
              $result_pershit = @$db->query("SELECT SUM(a.tagihan_nota) AS jml 
                                                FROM merchant_transaksi a    
                                                WHERE a.kd_merchant='$_SESSION[kd_merchant]'
                                                	AND a.status_transaksi ='2'
                                                    AND a.kd_jenis_pembayaran !=''
                                                    AND a.kd_shift ='$value_shift[id_shift]'
                                                    AND DATE(a.tgl_input_transaksi) = '$date_loop'
                                                     ")->fetch_assoc()['jml'];
            //   var_dump($result_pershit);
              if ($result_pershit == "") {
                $result_pershit =0;
              }
                $data['shift'] = $list_pendapatan_pershift;
                
              $subdata['nama_shift'] = $value_shift['nama_shift'];
              $subdata['terjual'] = $result_pershit;
        
            //   $data['periode'][$date_loop][$value_shift['id_shift']."-".$value_shift['nama_shift']] =  $subdata;
              $data['periode'][$date_loop][$value_shift['id_shift']] =  $subdata;
        
            }
        
            $month = strtotime("+1 days", $month);
        
          }
          
        //   echo "<pre>";
        //   echo print_r($data);
    }else{
        $mulai = date("Y-m-d");
        $akhir = date("Y-m-d");
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
<style>
    .tabel_border, .tabel_border td, .tabel_border th {
      border: 1px solid black;
      padding : 2px 10px;
    }
    
    .tabel_border {
      width: 500px;
      border-collapse: collapse;
    }
    
</style>
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
            <?php if(!isset($data )) : ?>
                <center>
                    
                    <p class="text-red"></p>
                    Tidak ada data yang tersedia pada periode<br>
                    <?= tanggal_indo($mulai) ?> - <?= tanggal_indo($akhir) ?><br><br>
                    <i class="fa-2x fas fa-times-circle"></i>
                    <hr>
                </center>
                <?php goto tanpaData; ?>

            <?php endif; ?>
            <div class="table-responsive">
                <div style="min-width: 100px;"> 
                    <div id="tab_tabel_closing" style="width: 850px;">
                        <!-- <center><img src="dist/img/hehaocen.png" width="200px"></center> -->
                        <table style=" width: 100%">
                            <tbody>
                                <tr>
                                    <td>Priode</td>
                                    <td> : </td>
                                    <td><?= tanggal_indo($mulai) ?> - <?= tanggal_indo($akhir) ?></td>
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
                                        <td><?= tanggal_indo($tanggal)?></td>
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
            
            <form action="" method="GET">
                <input type="hidden" name="page" value="<?= @$_GET['page'] ?>">
                <input type="hidden" name="action" value="<?= @$_GET['action'] ?>">
                <div class="row">
                    <div class="col-md-4">
                        <label class="sr-only" for="inlineFormInputGroupUsername2">Username</label>
                          <div class="input-group mb-2 mr-sm-2">
                            <div class="input-group-prepend">
                              <div class="input-group-text">Mulai</div>
                            </div>
                            <input type="date" class="form-control" name="mulai" value="<?= date("Y-m-d") ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="sr-only" for="inlineFormInputGroupUsername2">Username</label>
                          <div class="input-group mb-2 mr-sm-2">
                            <div class="input-group-prepend">
                              <div class="input-group-text">Akhir</div>
                            </div>
                            <input type="date" class="form-control" name="akhir" value="<?= date("Y-m-d") ?>">
                        </div>
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
  </div>
</section>


<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.js"></script> 

<script type="text/javascript">

  $(document).on('click', '#pilih_kasir', function(){
      // nama_kasir =[];
      // id_kasir =[];
      $('#input_nama_kasir').val($(this).data('nama_kasir'))
      $('#input_id_kasir').val($(this).data('id_kasir'))
      // var tangkap_nama_kasir = $(this).data('nama_kasir')
      // var tangkap_id_kasir = $(this).data('id_kasir')
      // nama_kasir.push(tangkap_nama_kasir);
      // id_kasir.push(tangkap_id_kasir);
      $('#modal-kasir').modal('hide')
  });//end tangkap filter kasir


    $(function() { 
      $("#export_excel").click(function() {
         var mulai = "<?= @$mulai ?>"
         var akhir = "<?= @$akhir ?>"

         window.open('view/laporan_bagi_hasil_merchant/excel_laporan_bagi_hasil_harian.php?mulai='+mulai+ '&akhir=' +akhir, '__blank');

      }); 
    });

</script>