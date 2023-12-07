<?php 

    if(isset($_GET['mulai']) AND isset($_GET['akhir'])){
        $mulai = antiSQLi($_GET['mulai']);
        $akhir = antiSQLi($_GET['akhir']);
    }else{
        $mulai = date("Y-m-d");
        $akhir = date("Y-m-d"); 
    }
    
    $nama_kasir = $db->query("SELECT nama_admin FROM admin WHERE id_admin='$_SESSION[id_admin]'")->fetch_assoc()['nama_admin'];
    
    
    $shift = $db->query("SELECT B.nama_shift FROM transaksi A JOIN shift B ON A.kd_shift=B.id_shift WHERE A.kd_admin='$_SESSION[id_admin]' AND A.status_transaksi!='3' AND DATE(A.tanggal_transaksi) BETWEEN '$mulai' AND '$akhir' GROUP BY B.id_shift")->fetch_all(MYSQLI_ASSOC);
    

    $data = $db->query("SELECT D.id_jenis_tiket, D.nama_jenis_tiket, COUNT(A.id_tiket) AS jumlah_tiket, A.harga_satuan FROM tiket A JOIN transaksi B ON A.kd_transaksi=B.id_transaksi  JOIN jenis_tiket D ON A.kd_jenis_tiket=D.id_jenis_tiket WHERE DATE(B.tanggal_transaksi) BETWEEN '$mulai' AND '$akhir' AND A.status_tiket!='2' AND B.kd_admin='$_SESSION[id_admin]' GROUP BY A.kd_jenis_tiket, A.harga_satuan ")->fetch_all(MYSQLI_ASSOC);

    $pembayaran = $db->query("SELECT B.nama_jenis_pembayaran, COALESCE(SUM(A.total_transaksi),0) AS jumlah, COALESCE(SUM(A.diskon),0) AS diskon FROM transaksi A JOIN jenis_pembayaran B ON A.kd_jenis_pembayaran=B.id_jenis_pembayaran WHERE DATE(A.tanggal_transaksi) BETWEEN '$mulai' AND '$akhir' AND A.status_transaksi!='3' AND A.kd_admin='$_SESSION[id_admin]' GROUP BY A.kd_jenis_pembayaran")->fetch_all(MYSQLI_ASSOC);

    $total_diskon = 0;
    foreach ($pembayaran as $key => $value){
        $total_diskon += $value['diskon'];
    }
    
    
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
            <?php if(!isset($data[0])) : ?>
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
                                    <td>Tiket Masuk</td>
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
                                <?php foreach($data as $key => $value): ?>
                                    <?php $subtotal = $value['harga_satuan']*$value['jumlah_tiket'] ?>
                                    <?php $total_penjualan_tiket_rupiah += (int)$subtotal; ?>
                                    <?php $total_penjualan_tiket_jumlah += (int)$value['jumlah_tiket']; ?>
                                    <tr>
                                        <td><?= $value['nama_jenis_tiket'] ?></td>
                                        <td align="right">Rp <?= number_format(@$value['harga_satuan']) ?></td>
                                        <td align="right"><?= number_format($value['jumlah_tiket']) ?></td>
                                        <td align="right">Rp <?= number_format(@$subtotal) ?></td>
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
                                <?php foreach ($pembayaran as $key => $value) : ?>
                                    <?php $total_rincian_pembayaran += $value['jumlah']; ?>
                                    <tr>
                                        <td><?= $value['nama_jenis_pembayaran'] ?></td>
                                        <td align="right">Rp <?= number_format($value['jumlah']) ?></td>
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
    						printer: '<?= $printer_tiket_masuk ?>',
    						ip: '<?= $_SESSION["printer"] ?>',
    						nama_file: 'closing_tiket_masuk_<?= date('YmdHis') ?>',
    					} 
    				}).done(function(o) { 
    					alert(o); 
    					$("#tab_tabel_closing").css("font-size","100%");
    				}); 
    			} 
    		}); 
    	}); 
    });

</script>