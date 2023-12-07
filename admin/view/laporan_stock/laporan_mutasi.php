<?php 

  $date      = antiSQLi(@$_GET['date']);

  $sess_kd_merchant = $_SESSION['kd_merchant'];
  if ($date != "" ){
        $query = "SELECT mp.nama_produk, mp.status_display_produk, mp.id_merchant_produk, mhs.stok_setelah, me.nama_employee, mhs.harga_beli, mhs.jenis_history, mhs.stok_sebelum, mhs.stok_setelah, mhs.masuk, mhs.keluar, mhs.keterangan,
             SUM(mhs.masuk) masuk, SUM(mhs.keluar) keluar
             FROM merchant_history_stok AS mhs
             LEFT JOIN merchant_produk AS mp ON mhs.kd_merchant_produk = mp.id_merchant_produk 
             LEFT JOIN merchant_employee AS me ON mhs.kd_merchant_employee = me.id_merchant_employee 
             WHERE mhs.kd_merchant = '$sess_kd_merchant'
                AND date(mhs.tanggal_history) = '$date'
             GROUP BY mhs.kd_merchant_produk
             ORDER BY mhs.id_merchant_history_stok DESC";
        $data_stock = $db->query($query)->fetch_all(MYSQLI_ASSOC);

  }
  else{
    $date = date("Y-m-d");
  }


  // $query = "SELECT mp.nama_produk, mp.status_display_produk, mp.id_merchant_produk, mhs.stok_setelah,
  //     SUM(mhs.stok_setelah) stok_setelah
  //           FROM merchant_history_stok AS mhs
  //           LEFT JOIN merchant_produk AS mp ON mhs.kd_merchant_produk = mp.id_merchant_produk 
  //           WHERE mhs.kd_merchant = '$sess_kd_merchant'
  //                 GROUP BY mhs.kd_merchant_produk
  //           ORDER BY mp.nama_produk ASC";
  // $data_stock = $db->query($query)->fetch_all(MYSQLI_ASSOC);

    // echo "<pre>";
    // echo print_r($data_stock);
    // echo die();
	
 ?>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <!--<h1 class="m-0 text-dark">LAPORAN</h1>-->
      </div>
    </div>
  </div>
</div>
<!-- <style>
    .tabel_border, .tabel_border td, .tabel_border th {
      border: 1px solid black;
      padding : 2px 10px;
    }
    
    .tabel_border {
      width: 500px;
      border-collapse: collapse;
    }  
</style> -->
<section class="content">
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-12">
        <div class="card">
          <div class="card-header text-bold">
              <span style="font-size: 130%;">Laporan Mutasi</span>   
              <button type="button" id="export_excel" class="btn btn-sm btn-outline-secondary btn_cetak float-right" style="height: 81%"><i class="fas fa-print"></i> Cetak</button>
          </div>
          <div class="card-body">
            <?php if(!isset($data_stock[0] )) : ?>
                <center>
                    Tidak ada data yang tersedia pada periode<br>
                    <?= tanggal_indo($date) ?><br><br>
                    <i class="fa-2x fas fa-times-circle"></i>
                    <hr>
                </center>
                <?php goto tanpaData; ?>

            <?php endif; ?>
            <div class="table-responsive">
                        <table>
                            <tbody>
                                <!-- <tr>
                                    <td>Nama</td>
                                    <td> : </td>
                                    <td>
                                      <?php 
                                        if( $nama_kasir != ""){
                                          echo $nama_kasir;
                                        }else{
                                          echo "-";
                                        } 
                                      ?>
                                          
                                    </td>
                                </tr> -->
                                <tr>
                                    <td style="padding-right: 20px">Priode</td>
                                    <td style="padding-right: 10px"> : </td>
                                    <td><?= tanggal_indo($date) ?></td>
                                </tr>
                            </tbody>
                        </table>
                        <hr>
                          <table class="table table-bodered table-striped" id="example" >
                            <thead>
                              <tr>
                                <th>No</th>
                                <th>Nama Product</th>
                                <th>Harga</th>
                                <th>Stock Sebelum</th>
                                <th>Masuk</th>
                                <th>Keluar</th>
                                <th>Stock Akhir</th>
                                <th>Nilai Stock Akhir</th>
                              </tr>
                            </thead>
                            <tbody>
                                <?php $total_stock_awal =0; $total_masuk = 0; $total_keluar =0; $total_akhir=0; $harga_akhir=0; ?>
                               <?php $no = 1; foreach ($data_stock as $o) { ?>
                               <?php $total_stock_awal += $o['stok_sebelum']; $total_masuk += $o['masuk']; $total_keluar += $o['keluar'];  $total_akhir += $o['stok_sebelum']+$o['masuk']-$o['keluar']; $harga_akhir += ($o['stok_sebelum']+$o['masuk']-$o['keluar'])*$o['harga_beli']; ?>
                              <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $o['nama_produk'] ?></td>
                                <td>Rp <?= number_format($o['harga_beli']) ?></td>
                                <td class="text-right"><?= $o['stok_sebelum'] ?></td>
                                <td class="text-right"><?= $o['masuk'] ?></td>
                                <td class="text-right"><?= $o['keluar'] ?></td>
                                <td class="text-right"><?= $o['stok_sebelum']+$o['masuk']-$o['keluar'] ?></td>
                                <td class="text-right">Rp <?=  number_format(($o['stok_sebelum']+$o['masuk']-$o['keluar'])*$o['harga_beli']) ?></td>
                              </tr>
                              <?php } ?>
                            </tbody>
                            <tr>
                                <th class="text-right" colspan="3">Total</th>
                                <th class="text-right"><?=$total_stock_awal?></th>
                                <th class="text-right"><?=$total_masuk?></th>
                                <th class="text-right"><?=$total_keluar?></th>
                                <th class="text-right"><?=$total_akhir?></th>
                                <th class="text-right">Rp <?=number_format($harga_akhir)?></th>
                            </tr>
                          </table>

                   <!--  </div> 
                </div>  -->
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
                              <div class="input-group-text">Tanggal</div>
                            </div>
                            <input type="date" class="form-control" name="date" value="<?= date("Y-m-d") ?>">
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


<script>

  $(function() { 
    $("#export_excel").click(function() {
       var date = "<?= @$date ?>"

       window.open('view/laporan_stock/excel_laporan_stock.php?date='+date, '__blank');

    }); 
  });

  
  // $("#dates").change(function() {
  //   let tangkap_date = $('#dates').val()
  //   // alert(tangkap_date)
  //   $.ajax({
  //     type    : 'POST',
  //     url     : '<?=base_url().'/view/laporan_stock/proses_data.php' ?>',
  //     // async   : true,
  //     data   : { filter : true, 'tangkap_date' : tangkap_date },
  //     dataType: 'json',
  //     success : function(data){//data dari controller di simpan pada (data)
  //       //console.log(data)
  //       var html = '';
  //       var i;
  //         for (i=0; i<data.length; i++) {
  //           // if ( data[i].status_display_produk == "Y"){
  //           //   var status_display_produk = "Display"
  //           // }else{
  //           //   var status_display_produk = "Non Display"
  //           // }
  //           no++;
  //           html += '<tr>'+
  //                       '<td>'+ data[i].nama_employee + '</td>'+
  //                       '<td>'+ data[i].nama_produk + '</td>'+
  //                       '<td>'+ data[i].tanggal_history + '</td>'+
  //                       '<td>'+ data[i].masuk + '</td>'+
  //                       '<td>'+ data[i].keluar + '</td>'+
  //                       '<td>'+ parseInt(data[i].harga_beli).toLocaleString() + '</td>'+
  //                       '<td>'+ data[i].stok_setelah + '</td>'+
  //                       '<td>'+ data[i].keterangan + '</td>'+
  //                       '<td>'+ data[i].status_display_produk + '</td>'+
  //               '</tr>';
  //         }
  //       $('#filter_data').html(html);
  //     }
  //   })
  // })



//  $(function () {
//    $('input[name="dates"]').daterangepicker({
      // opens: 'left'
      // }, function (start, end) {
      // // $("#dates").change(function() {
      //   tanggal = [];
      //   var hasil_tanggal = start.format('YYYY-MM-DD')+'_'+end.format('YYYY-MM-DD')
      // // console.log(hasil_tanggal)
      //       tanggal.push(hasil_tanggal);
      //       // })
      //     }
      // )
//  })

</script>