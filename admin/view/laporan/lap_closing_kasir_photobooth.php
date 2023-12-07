<?php 

    if(empty($_POST['tgl_awal']) || empty($_POST['tgl_akhir'])){
        $tgl_awal = date('Y-m-d');
        $tgl_akhir = date('Y-m-d');
    } else {
        $tgl_awal = date('Y-m-d', strtotime($_POST['tgl_awal']));
        $tgl_akhir = date('Y-m-d', strtotime($_POST['tgl_akhir']));
    }

    $nama_kasir = $db->query("SELECT nama_admin FROM admin WHERE id_admin='$_SESSION[id_admin]'")->fetch_assoc()['nama_admin'];

    $data = $db->query("
        SELECT D.id_photobooth_stan, D.nama_photobooth_stan, C.id_shift, C.nama_shift, A.harga_satuan, 
            COALESCE(SUM(A.jumlah_tiket),0) AS jumlah, 
            COALESCE(SUM(A.jumlah_tiket*A.harga_satuan),0) AS subtotal 
        FROM photobooth_tiket A 
        JOIN photobooth_transaksi B ON A.kd_photobooth_transaksi=B.id_photobooth_transaksi 
        JOIN shift C ON B.kd_shift=C.id_shift
        JOIN photobooth_stan D ON A.kd_photobooth_stan=D.id_photobooth_stan 
        WHERE DATE(B.tanggal_photobooth_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' 
            AND B.status_transaksi='1' 
            AND A.status_tiket!='2'
            AND ( A.harga_satuan > 0 OR B.status_paket_spot = 1)
            AND B.kd_admin='$_SESSION[id_admin]'
        GROUP BY A.kd_photobooth_stan, A.harga_satuan, B.kd_shift
    ")->fetch_all(MYSQLI_ASSOC);

    $shift = $db->query("SELECT B.nama_shift FROM photobooth_transaksi A JOIN shift B ON A.kd_shift=B.id_shift WHERE A.kd_admin='$_SESSION[id_admin]' AND A.status_transaksi='1' AND DATE(A.tanggal_photobooth_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' GROUP BY B.id_shift")->fetch_all(MYSQLI_ASSOC);

    $pembayaran = $db->query("
        SELECT A.id_jenis_pembayaran, A.nama_jenis_pembayaran, 
            (SELECT SUM(T.nominal_sebelum_diskon) 
                FROM photobooth_transaksi T 
                WHERE T.kd_admin='$_SESSION[id_admin]' 
                    AND T.kd_jenis_pembayaran=A.id_jenis_pembayaran 
                    AND T.status_transaksi='1' 
                    AND DATE(T.tanggal_photobooth_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') - 
            (SELECT SUM(T2.diskon) 
                FROM photobooth_transaksi T2 
                WHERE T2.kd_admin='$_SESSION[id_admin]'  
                    AND T2.kd_jenis_pembayaran=A.id_jenis_pembayaran 
                    AND T2.status_transaksi='1' 
                    AND DATE(T2.tanggal_photobooth_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS total,

            (SELECT SUM(T3.diskon) 
                FROM photobooth_transaksi T3 
                WHERE T3.kd_admin='$_SESSION[id_admin]'  
                    AND T3.kd_jenis_pembayaran=A.id_jenis_pembayaran 
                    AND T3.status_transaksi='1' 
                    AND DATE(T3.tanggal_photobooth_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS total_diskon

        FROM jenis_pembayaran A WHERE A.status_aktif='Y' ")->fetch_all(MYSQLI_ASSOC);


    $data_final = [];
    $data_shift = [];
    $data_pershift = [];
    $total_tiket = [];
    $total_tiket_jml = [];
    $total_bawah = [];
    foreach ($data as $key => $value) {
        $data_tiket[$value['id_photobooth_stan']] = $value['nama_photobooth_stan'];
        $data_shift[$value['id_shift']] = $value['nama_shift'];

        $data_pershift[$value['id_photobooth_stan']][$value['id_shift']]['nama'] = $value['nama_photobooth_stan'];
        $data_pershift[$value['id_photobooth_stan']][$value['id_shift']]['jumlah'] = $value['jumlah'];
        $data_pershift[$value['id_photobooth_stan']][$value['id_shift']]['harga_satuan'] = $value['harga_satuan'];
        $data_pershift[$value['id_photobooth_stan']][$value['id_shift']]['subtotal'] = $value['subtotal'];

        if (isset($total_tiket[$value['id_photobooth_stan']])) {
            $total_tiket[$value['id_photobooth_stan']]['total'] += $data_pershift[$value['id_photobooth_stan']][$value['id_shift']]['subtotal'];
            $total_tiket[$value['id_photobooth_stan']]['jumlah'] +=  $value['jumlah'];
        }else{
            $total_tiket[$value['id_photobooth_stan']]['total'] = $data_pershift[$value['id_photobooth_stan']][$value['id_shift']]['subtotal'];
            $total_tiket[$value['id_photobooth_stan']]['jumlah'] =  $value['jumlah'];
        }

        if (isset($total_bawah[$value['id_shift']]['jumlah'])) {
            $total_bawah[$value['id_shift']]['jumlah'] += $value['jumlah'];
            $total_bawah[$value['id_shift']]['subtotal'] += $data_pershift[$value['id_photobooth_stan']][$value['id_shift']]['subtotal'];
        }else{
            $total_bawah[$value['id_shift']]['jumlah'] = $value['jumlah'];
            $total_bawah[$value['id_shift']]['subtotal'] = $data_pershift[$value['id_photobooth_stan']][$value['id_shift']]['subtotal'];
        }

    }

    $kolom_shift = count($data_shift)*3;

    $total_diskon = 0;
    foreach ($pembayaran as $key => $value){
        $total_diskon += $value['total_diskon'];
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
            	<button type="button" class="btn btn-sm btn-outline-secondary btn_sync float-right" style="height: 81%; margin-right:5px;"><i class="fas fa-sync"></i> SYNC</button>
          </div>
          <div class="card-body">
            <?php if (empty($data)): ?>
                <center>
                    Tidak ada data yang tersedia pada periode<br>
                    <?= tanggal_indo($tgl_awal) ?> - <?= tanggal_indo($tgl_akhir) ?><br><br>
                    <i class="fa-2x fas fa-times-circle"></i>
                    <hr>
                </center>
                <?php goto tanpaData; ?>
            <?php endif; ?>
            <div class="table-responsive">
                <div style="min-width: 100px;"> 
                    <div id="tab_tabel_closing" style="width: 570px; margin-left:auto; margin-right:auto;">
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
                                    <td>Tiket Photobooth</td>
                                </tr>
                                <tr>
                                    <td>Priode</td>
                                    <td> : </td>
                                    <td><?= tanggal_indo($tgl_awal) ?> - <?= tanggal_indo($tgl_akhir) ?></td>
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
                        <table border="1" class="table table-bordered table-sm mt-3">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">Spot Foto</th>
                                            <?php foreach ($data_shift as $key => $value): ?>
                                                <th colspan="3" style="text-align: center;"><?= $value ?></th>
                                            <?php endforeach ?>
                                            <th colspan="2" style="text-align: center;">Total</th>
                                        </tr>
                                        <tr>
                                            <?php foreach ($data_shift as $key => $value): ?>
                                                <th>Harga</th>
                                                <th>Jumlah</th>
                                                <th>Subtotal</th>
                                            <?php endforeach ?>
                                            <th>Jumlah</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data_tiket as $id_tiket => $nama_tiket): ?>
                                            <tr>
                                                <td><?= $nama_tiket ?></td>
                                                <?php foreach ($data_shift as $key => $value): ?>
                                                    <?php if (!empty($data_pershift[$id_tiket][$key]['jumlah'])): ?>
                                                        <td><?= number_format(@$data_pershift[$id_tiket][$key]['harga_satuan'],0,',','.') ?></td>
                                                        <td><?= number_format(@$data_pershift[$id_tiket][$key]['jumlah'],0,',','.') ?></td>
                                                        <td><?= number_format(@$data_pershift[$id_tiket][$key]['subtotal'],0,',','.') ?></td>
                                                    <?php else: ?>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                    <?php endif ?>
                                                <?php endforeach ?>
                                                <td><?= number_format(@$total_tiket[$id_tiket]['jumlah'],0,',','.') ?></td>
                                                <td><?= number_format(@$total_tiket[$id_tiket]['total'],0,',','.') ?></td>
                                            </tr>
                                        <?php endforeach ?>

                                        <tr>
                                            <td align="right">DISKON</td>
                                            <td colspan="<?= $kolom_shift+1 ?>"></td>
                                            <td nowrap="">- <?= number_format($total_diskon,0,',','.') ?></td>
                                        </tr>
                                        <tr>
                                            <td align="right"><b>TOTAL</b></td>
                                            <?php $total_all = 0; ?>
                                            <?php $jml_pertiket = 0; ?>
                                            <?php foreach ($data_shift as $key => $value): ?>
                                                <td>-</td>
                                                <td><?= number_format($total_bawah[$key]['jumlah'],0,',','.') ?></td>
                                                <td><?= number_format($total_bawah[$key]['subtotal'],0,',','.') ?></td>
                                                <?php $total_all += $total_bawah[$key]['subtotal']; ?>
                                                <?php $jml_pertiket += $total_bawah[$key]['jumlah']; ?>
                                            <?php endforeach ?>
                                            <td><?= number_format($jml_pertiket,0,',','.') ?></td>
                                            <td><?= number_format($total_all-$total_diskon,0,',','.') ?></td>
                                        </tr>

                                        <!-- <tr>
                                            <td colspan="<?= $kolom_shift+3 ?>">&nbsp;</td>
                                        </tr>
                                        <?php $total_bayar = 0; ?>
                                        <?php foreach ($pembayaran as $key => $value): ?>
                                            <?php $total_bayar += $value['jumlah'] ?>
                                            <tr>
                                                <td align="right"><?= $value['nama_jenis_pembayaran'] ?></td>
                                                <td colspan="<?= $kolom_shift+1 ?>"></td>
                                                <td><?= number_format($value['jumlah'],0,',','.') ?></td>
                                            </tr>
                                        <?php endforeach ?>
                                        <tr>
                                            <td align="right">DISKON</td>
                                            <td colspan="<?= $kolom_shift+1 ?>"></td>
                                            <td nowrap="">- <?= number_format($total_diskon,0,',','.') ?></td>
                                        </tr>
                                        <tr>
                                            <td align="right"><b>TOTAL AKHIR</b></td>
                                            <td colspan="<?= $kolom_shift+1 ?>"></td>
                                            <td><?= number_format($total_bayar,0,',','.') ?></td>
                                        </tr> -->
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
                                    <?php $total_rincian_pembayaran += $value['total']; ?>
                                    <tr>
                                        <td><?= $value['nama_jenis_pembayaran'] ?></td>
                                        <td align="right"><?= number_format($value['total']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td><b>TOTAL</b></td>
                                    <td align="right"><b><?= number_format($total_rincian_pembayaran) ?></b></td>
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
    						printer: '<?= $printer_tiket_photobooth ?>',
    						ip: '<?= $_SESSION["printer"] ?>',
    						nama_file: 'closing_tiket_photobooth_<?= date('YmdHis') ?>',
    					} 
    				}).done(function(o) { 
    					console.log('saved'); 
    					$("#tab_tabel_closing").css("font-size","100%");
    				}); 
    			} 
    		}); 
    	});
      
      	$(".btn_sync").click(function() {
            $.ajax({ 
                url: "view/laporan/proses_data.php?sync=",
                type: "POST",
              	dataType : "json",
                data: { 
                	sync:"sync"
                },
              	success	: function(data){
                    if(data.status == true){
                    	alert('data berhasil di perbarui');
                      	location.reload();
                    }else{
                      alert('gagal memperbarui data');
                      	location.reload();
                    }
                }
            })
    	});
      
    });
</script>