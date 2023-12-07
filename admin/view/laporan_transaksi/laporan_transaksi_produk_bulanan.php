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

$full_date_awal = $tahun_awal."-".$bulan_awal."-01";
$full_date_akhir = $tahun_akhir."-".$bulan_akhir."-30";

// var_dump($full_date_awal);


  $list_produk = $db->query("SELECT A.kd_merchant_produk, B.nama_produk, A.harga_produk 
                            FROM merchant_transaksi_detail A 
                            JOIN merchant_produk B ON A.kd_merchant_produk=B.id_merchant_produk 
                            JOIN merchant_transaksi C ON A.kd_merchant_transaksi=C.id_merchant_transaksi 
                            WHERE C.kd_jenis_pembayaran!=''
                                AND A.status_transaksi_detail='2'
                                AND B.kd_merchant='$_SESSION[kd_merchant]' 
                                AND DATE(A.tgl_input_detail) BETWEEN '$full_date_awal' AND '$full_date_akhir' 
                            GROUP BY A.kd_merchant_produk,A.harga_produk")->fetch_all(MYSQLI_ASSOC);

  $start = $month = strtotime($tgl_awal);
  $end = strtotime($tgl_akhir);
  while($month <= $end)
  { 

    $tahun_loop =  date('Y', $month);
    $bulan_loop =  date('m', $month);

    foreach ($list_produk as $key2 => $value_perproduk) {
      
      $result_perproduk = @$db->query("SELECT SUM(A.jumlah_produk) AS jml 
                                        FROM merchant_transaksi_detail A 
                                        JOIN merchant_transaksi B ON A.kd_merchant_transaksi = B.id_merchant_transaksi
                                        WHERE A.kd_merchant='$_SESSION[kd_merchant]'
                                            AND A.kd_merchant_produk='$value_perproduk[kd_merchant_produk]' 
                                            AND A.harga_produk='$value_perproduk[harga_produk]' 
                                            AND A.status_transaksi_detail='2'
                                            AND B.kd_jenis_pembayaran!=''
                                            AND YEAR(A.tgl_input_detail)='$tahun_loop' 
                                            AND MONTH(A.tgl_input_detail)='$bulan_loop'")->fetch_assoc()['jml'];
    //   var_dump( $result_perproduk);
      if ($result_perproduk == "") {
        $result_perproduk =0;
      }
        
        $data['produk'] = $list_produk;
    
      $subdata['nama_produk'] = $value_perproduk['nama_produk'];
      $subdata['harga'] = $value_perproduk['harga_produk'];
      $subdata['terjual'] = $result_perproduk;

      $data['periode'][$bulan_loop."/".$tahun_loop][$value_perproduk['kd_merchant_produk']."".$value_perproduk['harga_produk']] = $subdata;

    }

    $month = strtotime("+1 month", $month);

  }

//   echo "<pre>";
//   echo print_r($data);

 ?>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-12">
        <h1 class="m-0 text-dark">Laporan Product Terbaru</h1>
        <button type="button" id="export_excel" class="btn btn-sm btn-success float-right"><i class="fas fa-print"></i> Export</button>
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
        
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-body pr-5" align="center">
                <table class="table">
                    <tbody>
                        <tr>
                            <td colspan="4" align="center">GRAFIK PENGUNJUNG SETIAP BULAN</td>
                        </tr>
                        <tr>
                            <td colspan="4" align="center">PERIODE <?= bulanAbjad($bulan_awal) ?> <?= $tahun_awal ?> sd <?= bulanAbjad($bulan_akhir) ?> <?= $tahun_akhir ?></td>
                        </tr>
                    </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
        
      <div class="row">
          <div class="col-12">
            <div class="card">
                <div class="card-header">
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="example" >
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Nama Product</th>
                          <th>Harga Satuan</th>
                          <?php foreach($data['periode'] as $bulan =>$val) { ?>
                                    <th><?= $bulan ?></th>
                          <?php } ?>
                        </tr>
                      </thead>
                      <tbody>
                         <?php $no=1; foreach($data['produk'] as $key => $value) { ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $value['nama_produk'] ?></td>
                                <td><?= $value['harga_produk'] ?></td>
                                <?php foreach($data['periode'] as $bulan =>$val) { ?>
                                    <td><?= $val[$value['kd_merchant_produk'].$value['harga_produk']]['terjual'] ?></td>
                                    <?php  
                                        if (isset($total_jml[$bulan])) {
                                            $total_jml[$bulan] += $val[$value['kd_merchant_produk'].$value['harga_produk']]['terjual']; 
                                        }else{
                                            $total_jml[$bulan] = $val[$value['kd_merchant_produk'].$value['harga_produk']]['terjual'];
                                        }
                                    ?>
                                <?php } ?>
                            </tr>
                          <?php } ?>
                      </tbody>
                       <tfoot>
                          <tr>
                            <td colspan="3">TOTAL</td>
                            <?php foreach($data['periode'] as $bulan =>$val) { ?>
                                <th><?= $total_jml[$bulan] ?></th>
                            <?php } ?>
                          </tr>
                        </tfoot>
                    </table>
                  </div>
                </div>
            </div>
          </div>
        </div>
    </div>
</section>

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
    var form = $('<form action="?page=laporan_transaksi&action=perproduk_bulanan" method="post">' +
      '<input type="hidden" name="tgl_awal" value="' + start + '" />' +
      '<input type="hidden" name="tgl_akhir" value="' + end + '" />' +
      '</form>');
    $('body').append(form);
    form.submit();
  });

</script>

<script>
  $(function() { 
    $("#export_excel").click(function() {
      // var mulai = "<?= @$mulai ?>"
      // var akhir = "<?= @$akhir ?>"
      // var nama_kasir = "<?= @$nama_kasir ?>"
      // var id_kasir = "<?= @$id_kasir ?>"

      window.open('view/laporan_produk/excel_laporan_produk_terbaru.php', '__blank');

    }); 
  });
</script>