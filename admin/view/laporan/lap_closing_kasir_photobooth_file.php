<?php 

    if(isset($_GET['mulai']) AND isset($_GET['akhir'])){
        $mulai = antiSQLi($_GET['mulai']);
        $akhir = antiSQLi($_GET['akhir']);
    }else{
        $mulai = date("Y-m-d");
        $akhir = date("Y-m-d");
    }
    
    $nama_kasir = $db->query("SELECT nama_admin FROM admin WHERE id_admin='$_SESSION[id_admin]'")->fetch_assoc()['nama_admin'];

    $query = "
        SELECT 

        C.nama_photoboothambil_stan, 

        (SELECT D.harga_photoboothambil_stan FROM photoboothambil_stan D WHERE D.id_photoboothambil_stan=A.kd_photoboothambil_stan) AS harga, 

        (SELECT SUM(E.jumlah_tiket) FROM photoboothambil_tiket E JOIN photoboothambil_transaksi F ON E.kd_photoboothambil_transaksi=F.id_photoboothambil_transaksi WHERE F.kd_admin='$_SESSION[id_admin]' AND F.status_transaksi='1' AND E.kd_photoboothambil_stan=A.kd_photoboothambil_stan AND DATE(F.tanggal_photoboothambil_transaksi) BETWEEN '$mulai' AND '$akhir') AS jml 

        FROM photoboothambil_tiket A JOIN photoboothambil_transaksi B ON A.kd_photoboothambil_transaksi=B.id_photoboothambil_transaksi JOIN photoboothambil_stan C ON A.kd_photoboothambil_stan=C.id_photoboothambil_stan WHERE B.kd_admin='$_SESSION[id_admin]' AND B.status_transaksi='1' AND DATE(B.tanggal_photoboothambil_transaksi) BETWEEN '$mulai' AND '$akhir' GROUP BY A.kd_photoboothambil_stan

    ";
    
    $data_tiket = $db->query($query)->fetch_all(MYSQLI_ASSOC);
    
    $total_diskon = $db->query("SELECT SUM(diskon) as jml FROM photoboothambil_transaksi WHERE kd_admin='$_SESSION[id_admin]' AND status_transaksi='1' AND DATE(tanggal_photoboothambil_transaksi) BETWEEN '$mulai' AND '$akhir'")->fetch_assoc()['jml'];
    
    $shift = $db->query("SELECT B.nama_shift FROM photoboothambil_transaksi A JOIN shift B ON A.kd_shift=B.id_shift WHERE A.kd_admin='$_SESSION[id_admin]' AND A.status_transaksi='1' AND DATE(A.tanggal_photoboothambil_transaksi) BETWEEN '$mulai' AND '$akhir' GROUP BY B.id_shift")->fetch_all(MYSQLI_ASSOC);
    
    $query = "SELECT A.id_jenis_pembayaran, A.nama_jenis_pembayaran, (SELECT SUM(T.nominal_sebelum_diskon) FROM photoboothambil_transaksi T WHERE T.kd_admin='$_SESSION[id_admin]' AND T.kd_jenis_pembayaran=A.id_jenis_pembayaran AND T.status_transaksi='1' AND DATE(T.tanggal_photoboothambil_transaksi) BETWEEN '$mulai' AND '$akhir') - (SELECT SUM(T2.diskon) FROM photoboothambil_transaksi T2 WHERE T2.kd_admin='$_SESSION[id_admin]'  AND T2.kd_jenis_pembayaran=A.id_jenis_pembayaran AND T2.status_transaksi='1' AND DATE(T2.tanggal_photoboothambil_transaksi) BETWEEN '$mulai' AND '$akhir') AS total FROM jenis_pembayaran A WHERE A.status_aktif='Y'";
    $rincian_pembayaran = $db->query($query)->fetch_all(MYSQLI_ASSOC);
    
    
    
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
      <div class="col-8">
        <div class="card">
          <div class="card-header text-bold">
              <span style="font-size: 130%;">Laporan Transaksi Kasir</span>   
              <button type="button" class="btn btn-sm btn-outline-secondary btn_cetak float-right" style="height: 81%"><i class="fas fa-print"></i> Cetak</button>
          </div>
          <div class="card-body">
            <?php if(!isset($data_tiket[0])) : ?>
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
                    <div id="tab_tabel_closing" style="width: 550px; margin-left:auto; margin-right:auto;">
                        <center><img src="dist/img/hehaocen.png" width="200px"></center>
                        <table style="margin-left: auto; margin-right: auto; width: 100%">
                            <tbody>
                                <tr>
                                    <td>Nama</td>
                                    <td> : </td>
                                    <td><?= $nama_kasir ?></td>
                                </tr>
                                <tr>
                                    <td>Unit</td>
                                    <td> : </td>
                                    <td>Pembelian File Photobooth</td>
                                </tr>
                                <tr>
                                    <td>Priode</td>
                                    <td> : </td>
                                    <td><?= tanggal_indo($mulai) ?> - <?= tanggal_indo($akhir) ?></td>
                                </tr>
                                <tr>
                                    <td>Shift</td>
                                    <td> : </td>
                                    <td>
                                        <?php foreach($shift as $key =>$value) : ?>
                                            <?= $value['nama_shift'] ?>,
                                        <?php endforeach; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <hr>
                        <table class="tabel_border" style="margin-top: 10px; margin-left: auto; margin-right: auto; width: 100%">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">Tiket</th>
                                    <th style="text-align: center;">Harga</th>
                                    <th style="text-align: center;">Jml</th>
                                    <th style="text-align: center;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $total_penjualan_tiket_rupiah = 0; ?>
                                <?php $total_penjualan_tiket_jumlah = 0; ?>
                                <?php foreach($data_tiket as $key => $value): ?>
                                    <?php $subtotal = $value['harga'] * $value['jml'] ?>
                                    <?php $total_penjualan_tiket_rupiah += (int)$subtotal; ?>
                                    <?php $total_penjualan_tiket_jumlah += (int)$value['jml']; ?>
                                    <tr>
                                        <td><?= $value['nama_photoboothambil_stan'] ?></td>
                                        <td align="right">Rp <?= number_format(@$value['harga']) ?></td>
                                        <td align="right"><?= $value['jml'] ?></td>
                                        <td align="right">Rp <?= number_format($subtotal) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                              
                                <tr>
                                    <td align="right" colspan="2">Total</td>
                                    <td align="right"><?= number_format($total_penjualan_tiket_jumlah) ?></td>
                                    <td align="right">Rp <?= number_format($total_penjualan_tiket_rupiah) ?></td>
                                </tr>
                                <tr>
                                    <td align="right" colspan="2">Diskon</td>
                                    <td align="right"></td>
                                    <td align="right">Rp <?= number_format(@$total_diskon) ?></td>
                                </tr>
                                <tr>
                                    <td align="right" colspan="2">Total Omzet</td>
                                    <td align="right"><?= number_format($total_penjualan_tiket_jumlah) ?></td>
                                    <td align="right">Rp <?= number_format($total_penjualan_tiket_rupiah - @$total_diskon) ?></td>
                                </tr>
                            </tbody>
                        </table>
                        <hr>
                        <center><b>RINCIAN</b></center>
                        <hr>
                        <table class="tabel_border" style="margin-top: 10px; margin-left: auto; margin-right: auto; width: 100%">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">Jenis Pembayaran</th>
                                    <th style="text-align: center;">Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $total_rincian_pembayaran = 0; ?>
                                <?php foreach ($rincian_pembayaran as $key => $value) : ?>
                                    <?php $total_rincian_pembayaran += $value['total']; ?>
                                    <tr>
                                        <td><?= $value['nama_jenis_pembayaran'] ?></td>
                                        <td align="right">Rp <?= number_format($value['total']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td><b>TOTAL</b></td>
                                    <td align="right"><b>Rp <?= number_format($total_rincian_pembayaran) ?></b></td>
                                </tr>
                            </tbody>
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
                    <div class="col-md-5">
                        <label class="sr-only" for="inlineFormInputGroupUsername2">Username</label>
                          <div class="input-group mb-2 mr-sm-2">
                            <div class="input-group-prepend">
                              <div class="input-group-text">Mulai</div>
                            </div>
                            <input type="date" class="form-control" name="mulai" value="<?= date("Y-m-d") ?>">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <label class="sr-only" for="inlineFormInputGroupUsername2">Username</label>
                          <div class="input-group mb-2 mr-sm-2">
                            <div class="input-group-prepend">
                              <div class="input-group-text">Akhir</div>
                            </div>
                            <input type="date" class="form-control" name="akhir" value="<?= date("Y-m-d") ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-block btn-outline-secondary" style="height: 81%">Proses</button>
                    </div>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


<script src="plugins/html2canvas/html2canvas.js"></script> 

<script type="text/javascript">
    $(function() { 
    	$(".btn_cetak").click(function() {
    	    $("#tab_tabel_closing").css("font-size","150%");
    		html2canvas($("#tab_tabel_closing"), { 
    			onrendered: function(canvas) { 
    				var imgsrc = canvas.toDataURL("image/png"); 
    				console.log(imgsrc); 
    				$("#newimg").attr('src', imgsrc); 
    				$("#img").show(); 
    				var dataURL = canvas.toDataURL(); 
    				$.ajax({ 
    				    url: "view/laporan/proses_data.php?cetak_laporan_closing=",
    					type: "POST", 
    					data: { 
    						imgBase64: dataURL ,
    						printer: '<?= $printer_file_photobooth ?>',
    						ip: '<?= $_SESSION["printer"] ?>',
    						nama_file: 'closing_file_photobooth_<?= date('YmdHis') ?>',
    					} 
    				}).done(function(o) { 
    					console.log('saved'); 
    					$("#tab_tabel_closing").css("font-size","100%");
    				}); 
    			} 
    		}); 
    	}); 
    });
</script>