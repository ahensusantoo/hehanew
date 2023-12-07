<?php 
        $mulai      = antiSQLi(@$_GET['mulai']);
        $akhir      = antiSQLi(@$_GET['akhir']);
    if( $mulai != "" AND $akhir != ""){
        
        $query = "  SELECT A.harga_produk, A.harga_setelah_diskon, A.jumlah_produk , B.nama_produk, A.diskon,
                                    
                                SUM(jumlah_produk) jumlah_produk,
                                        (SELECT SUM(C.jumlah_produk)
                                          FROM merchant_transaksi_detail C
                                          LEFT JOIN merchant_transaksi E ON C.kd_merchant_transaksi = E.id_merchant_transaksi
                                          WHERE C.status_transaksi_detail='2'
                                            AND DATE(C.tgl_input_detail) BETWEEN '$mulai' AND '$akhir'
                                            AND E.kd_jenis_pembayaran !=''
                                            AND C.kd_merchant = '$_SESSION[kd_merchant]') as total_produk_terjual
                              FROM merchant_transaksi_detail A 
                                LEFT JOIN merchant_produk B ON A.kd_merchant_produk=B.id_merchant_produk
                                LEFT JOIN merchant_transaksi D ON A.kd_merchant_transaksi = D.id_merchant_transaksi
                                    WHERE A.kd_merchant = '$_SESSION[kd_merchant]'
                                        AND A.status_transaksi_detail ='2'
                                        AND D.kd_jenis_pembayaran != ''
                                        AND DATE(A.tgl_input_detail) BETWEEN '$mulai' AND '$akhir'
                                GROUP BY A.kd_merchant_produk, A.harga_produk ";
    $data_produk = $db->query($query)->fetch_all(MYSQLI_ASSOC);
                                        
    }else{
        $mulai = date("Y-m-d");
        $akhir = date("Y-m-d");
    }
    
?>

<?php 
    // echo "<pre>";
    // echo print_r($cek_jenis_bayar);
    // echo die();
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
              <span style="font-size: 130%;">Laporan Transaksi Kasir</span>   
              <button type="button" id="export_excel" class="btn btn-sm btn-outline-secondary btn_cetak float-right" style="height: 81%"><i class="fas fa-download"></i> Export</button>
          </div>
          <div class="card-body">
            <?php if(!isset($data_produk[0] )) : ?>
                <center>
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
                        <div class="table-responsive">
          					<table class="table table-bordered" id="example">
          						<thead>
          							<tr>
          								<th>No</th>
          								<th>Nama Product</th>
          								<th>Harga Satuan</th>
          								<th>Jumlah Terjual</th>
          								<th>Persen(%)</th>
          							</tr>
          						</thead>
          						<tbody>
          						    <?php $total_persen = 0; ?>
                                    <?php $no=1; foreach ($data_produk as $key => $value) { ?>
                                    <?php $total_persen += round( ($value['jumlah_produk']/$value['total_produk_terjual'] ) *100, 2 ) ?>
                                      <tr>
                                      <td><?=$no++ ?></td>       
                                      <td><?=$value['nama_produk'] ?></td>       
                                      <td>Rp <?=number_format($value['harga_produk']) ?></td>       
                                      <td class="text-right"><?=$value['jumlah_produk'] ?></td>       
                                      <td class="text-right"><?=round( ($value['jumlah_produk']/$value['total_produk_terjual'] ) *100, 2 ) ?> %</td>       
                                    </tr>
                                  <?php } ?>
          						</tbody>
              						<tr>
                                        <th colspan="3" class="text-right">Total</th>
                                        <th class="text-right"><?=$data_produk[0]['total_produk_terjual']?></th>
                                        <th class="text-right"><?= round($total_persen) ?> %</th>
                                    </tr>
          					</table>
          				</div>
                        <hr>
                        
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

    $(function() { 
      $("#export_excel").click(function() {
         var mulai = "<?= @$mulai ?>"
         var akhir = "<?= @$akhir ?>"
         var nama_kasir = "<?= @$nama_kasir ?>"
         var id_kasir = "<?= @$id_kasir ?>"

         window.open('view/laporan_transaksi/excel_laporan_kasir.php?mulai='+mulai+ '&akhir=' +akhir+'&nama_kasir=' +nama_kasir+'&id_kasir='+id_kasir, '__blank');

      }); 
    });

</script>