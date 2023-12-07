<?php 

  $date      = antiSQLi(@$_GET['date']);

  //$date      = antiSQLi(@$_GET['date']);

  $sess_kd_merchant = $_SESSION['kd_merchant'];
  //if ($date != "" ){
      $query = "SELECT a.kd_merchant_produk, a.jenis_history, a.nama_produk, a.harga_produk, a.harga_beli, 
                a.gambar_produk, a.status_konsi, a.nama_employee, a.telp_employee, 
                a.nama_merchant, a.telp_merchant, a.file_logo,
                
                (SELECT d.stok_terakhir 
                    FROM view_merchant_history_stok d 
                    WHERE d.kd_merchant = '$sess_kd_merchant' 
                        AND d.kd_merchant_produk=a.kd_merchant_produk 
                        ORDER BY d.tanggal_history DESC LIMIT 1) AS stok_setelah, 
                
                (SELECT b.tanggal_history 
                    FROM view_merchant_history_stok b 
                    WHERE b.kd_merchant = '$sess_kd_merchant' 
                        AND b.kd_merchant_produk=a.kd_merchant_produk 
                         
                        ORDER BY b.tanggal_history DESC LIMIT 1) AS tanggal_history 
                
                FROM `view_merchant_history_stok` a 
                    WHERE a.kd_merchant = '$sess_kd_merchant' 
                    GROUP BY a.kd_merchant_produk";
      $data_stock = $db->query($query)->fetch_all(MYSQLI_ASSOC);

 // }
  //else{
   // $date = date("Y-m-d");
  //}
	
 ?>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <!--<h1 class="m-0 text-dark">LAPORAN TRANSAKSI</h1>-->
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
              <span style="font-size: 130%;">Laporan Stock</span>   
              <button type="button" id="export_excel" class="btn btn-sm btn-outline-secondary btn_cetak float-right" style="height: 81%"><i class="fas fa-download"></i> Eksport</button>
          </div>
          <div class="card-body">
            <div class="table-responsive">  
                <table class="table table-bodered table-striped" id="example" >
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>ID Product</th>
                        <th>Nama Product</th>
                        <th>Harga Beli</th>
                        <th>Sisa Stock</th>
                        <th>Nilai Stock </th>
                        <th>Harga Jual </th>
                      </tr>
                    </thead>
                    <tbody>
                        <?php $total_sisa_stock = 0; $total_nilai_stock = 0; ?>
                       <?php $no = 1; foreach ($data_stock as $o) { ?>
                       <?php $total_sisa_stock += $o['stok_setelah'];  $total_nilai_stock += $o['stok_setelah']*$o['harga_beli'];  ?>
                      <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $o['kd_merchant_produk'] ?></td>
                        <td><?= $o['nama_produk'] ?></td>
                        <td>Rp <?= number_format($o['harga_beli']) ?></td>
                        <td><?= $o['stok_setelah'] ?></td>
                        <td>Rp <?= number_format($o['stok_setelah']*$o['harga_beli']) ?></td>
                        <td>Rp <?= number_format($o['harga_produk']) ?></td>
                      </tr>
                      <?php } ?>
                    </tbody>
                        <tr>
                            <th colspan="4" class="text-right">Total</th>
                            <th><?=$total_sisa_stock ?></th>
                            <th>Rp <?=number_format($total_nilai_stock) ?></th>
                        </tr>
                  </table>
            </div>
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