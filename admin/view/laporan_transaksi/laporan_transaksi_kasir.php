<?php 
        $mulai      = antiSQLi(@$_GET['mulai']);
        $akhir      = antiSQLi(@$_GET['akhir']);
        $nama_kasir = antiSQLi(@$_GET['nama_kasir']);
        $id_kasir   = antiSQLi(@$_GET['id_kasir']);
    if( $mulai != "" AND $akhir != "" AND $nama_kasir != "" AND $id_kasir !=""){

        $nama_kasir = $db->query("SELECT nama_employee FROM merchant_employee WHERE id_merchant_employee='$id_kasir'")->fetch_assoc()['nama_employee'];

        $check_data = $db->query("SELECT A.harga_produk, A.harga_setelah_diskon, A.jumlah_produk , B.nama_produk, A.diskon,
                                    
                                SUM(A.jumlah_produk) jumlah_produk
                                FROM merchant_transaksi_detail A 
                                LEFT JOIN merchant_produk B ON A.kd_merchant_produk=B.id_merchant_produk 
                                LEFT JOIN merchant_transaksi C ON A.kd_merchant_transaksi=C.id_merchant_transaksi 
                                    WHERE A.kd_merchant = '$_SESSION[kd_merchant]'
                                        AND A.status_transaksi_detail ='2'
                                        AND C.kd_jenis_pembayaran !=''
                                        AND A.kd_merchant_employee='$id_kasir'
                                        AND DATE(A.tgl_input_detail) BETWEEN '$mulai' AND '$akhir'
                                GROUP BY A.kd_merchant_produk, A.harga_produk ")->fetch_all(MYSQLI_ASSOC);
        
        $diskon = $db->query("SELECT SUM((A.harga_produk-A.harga_setelah_diskon)*A.jumlah_produk) AS jml FROM merchant_transaksi_detail A JOIN merchant_transaksi B ON A.kd_merchant_transaksi=B.id_merchant_transaksi WHERE B.kd_jenis_pembayaran!='' AND A.kd_merchant='$_SESSION[kd_merchant]' AND A.kd_merchant_employee='$id_kasir' AND DATE(A.tgl_input_detail) BETWEEN '$mulai' AND '$akhir' AND A.status_transaksi_detail='2'")->fetch_assoc()['jml'];

        
        $cek_jenis_bayar = $db->query("SELECT *,
                                        SUM(C.harga_setelah_diskon * C.jumlah_produk) tagihan_nota,
                                            (SELECT COUNT(D.kd_jenis_pembayaran) 
                                                FROM merchant_transaksi D 
                                                WHERE D.kd_merchant = '$_SESSION[kd_merchant]'
                                                    AND D.status_transaksi ='2'
                                                    AND D.kd_merchant_employee='$id_kasir'
                                                    AND D.kd_jenis_pembayaran !=''
                                                    AND DATE(D.tgl_input_transaksi) BETWEEN '$mulai' AND '$akhir'
                                                    AND D.kd_jenis_pembayaran = A.kd_jenis_pembayaran) as jumlah_struck
                                        FROM merchant_transaksi A 
                                        LEFT JOIN jenis_pembayaran B ON A.kd_jenis_pembayaran = B.id_jenis_pembayaran
                                        LEFT JOIN merchant_transaksi_detail C ON A.id_merchant_transaksi = C.kd_merchant_transaksi
                                        WHERE A.kd_merchant = '$_SESSION[kd_merchant]'
                                            AND C.kd_merchant_employee='$id_kasir'
                                            AND A.status_transaksi ='2'
                                            AND A.kd_jenis_pembayaran != ''
                                            AND DATE(A.tgl_input_transaksi) BETWEEN '$mulai' AND '$akhir'
                                        GROUP BY A.kd_jenis_pembayaran 
                                    ")->fetch_all(MYSQLI_ASSOC);
                                        
        
    }else if( $mulai!= "" AND $akhir != ""){
        $check_data = $db->query("SELECT A.harga_produk, A.harga_setelah_diskon, A.jumlah_produk , B.nama_produk, A.diskon,
                                    
                                SUM(A.jumlah_produk) jumlah_produk
                                FROM merchant_transaksi_detail A 
                                LEFT JOIN merchant_produk B ON A.kd_merchant_produk=B.id_merchant_produk 
                                LEFT JOIN merchant_transaksi C ON A.kd_merchant_transaksi=C.id_merchant_transaksi 
                                    WHERE A.kd_merchant = '$_SESSION[kd_merchant]'
                                        AND A.status_transaksi_detail ='2'
                                        AND C.kd_jenis_pembayaran !=''
                                        AND DATE(A.tgl_input_detail) BETWEEN '$mulai' AND '$akhir'
                                GROUP BY A.kd_merchant_produk, A.harga_produk ")->fetch_all(MYSQLI_ASSOC);
                                
                                $diskon = $db->query("SELECT SUM((A.harga_produk-A.harga_setelah_diskon)*A.jumlah_produk) AS jml FROM merchant_transaksi_detail A JOIN merchant_transaksi B ON A.kd_merchant_transaksi=B.id_merchant_transaksi WHERE B.kd_jenis_pembayaran!='' AND A.kd_merchant='$_SESSION[kd_merchant]' AND DATE(A.tgl_input_detail) BETWEEN '$mulai' AND '$akhir' AND A.status_transaksi_detail='2'")->fetch_assoc()['jml'];


                    
        // echo "<pre>";
        // echo print_r($check_data);
                                
        $cek_jenis_bayar = $db->query("SELECT *,
                                        SUM(A.tagihan_nota) tagihan_nota, 
                                                (SELECT COUNT(C.kd_jenis_pembayaran) 
                                                FROM merchant_transaksi C 
                                                WHERE C.kd_merchant = '$_SESSION[kd_merchant]'
                                                    AND C.status_transaksi ='2'
                                                    AND C.kd_jenis_pembayaran !=''
                                                    AND DATE(C.tgl_input_transaksi) BETWEEN '$mulai' AND '$akhir'
                                                    AND C.kd_jenis_pembayaran = A.kd_jenis_pembayaran) as jumlah_struck
                                        FROM merchant_transaksi A 
                                        JOIN jenis_pembayaran B ON A.kd_jenis_pembayaran = B.id_jenis_pembayaran
                                        WHERE A.kd_merchant = '$_SESSION[kd_merchant]'
                                            AND A.status_transaksi ='2'
                                            AND DATE(A.tgl_input_transaksi) BETWEEN '$mulai' AND '$akhir'
                                            AND A.kd_jenis_pembayaran !=''
                                        GROUP BY A.kd_jenis_pembayaran  
                                    ")->fetch_all(MYSQLI_ASSOC);
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
            <?php if(!isset($check_data[0] )) : ?>
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
                                    <td>Nama</td>
                                    <td> : </td>
                                    <td>
                                      <?php 
                                        if( $nama_kasir != ""){
                                          echo $nama_kasir;
                                        }else{
                                          echo "Semua Kasir";
                                        } 
                                      ?>
                                          
                                    </td>
                                </tr>
                                <tr>
                                    <td>Priode</td>
                                    <td> : </td>
                                    <td><?= tanggal_indo($mulai) ?> - <?= tanggal_indo($akhir) ?></td>
                                </tr>
                            </tbody>
                        </table>
                        <hr>
                        <table class="tabel_border" style="margin-top: 10px; margin-left: auto; margin-right: auto; width: 100%">
                            <thead>
                                <tr>
                                    <th>Nama Barang</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $harga_produk = 0; ?>
                                <?php  $jumlah_produk = 0; ?>
                                <?php foreach($check_data as $key => $value): ?>
                                    <?php 
                                        $harga_produk += (int)$value['harga_produk']*$value['jumlah_produk'];
                                        $jumlah_produk += (int)$value['jumlah_produk'];
                                    ?>
                                    
                                    <tr>
                                        <td align="left"><?=$value['nama_produk'] ?></td>
                                        <td align="right">Rp <?= number_format($value['harga_produk']) ?></td>
                                        <td align="right"><?=$value['jumlah_produk'] ?></td>
                                        <td align="right">Rp <?=number_format($value['harga_produk'] * $value['jumlah_produk'] ) ?> </td>
                                    </tr>
                                <?php endforeach; ?>
                                
                                <tr>
                                    <td align="right" colspan="2">Total</td>
                                    <td align="right"><?= number_format($jumlah_produk)  ?></td>
                                    <td align="right">Rp <?= number_format($harga_produk)  ?></td>
                                </tr>
                                
                                <tr>
                                    <td align="right" colspan="3">Discount</td>
                                    <td align="right">Rp <?= number_format($diskon)  ?></td>
                                </tr>
                              
                                <tr>
                                    <td align="right" colspan="3">Grand Total</td>
                                    <td align="right">Rp <?= number_format($harga_produk - $diskon)  ?></td>
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
                                    <th style="text-align: center;">Jumlah Struck</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $total_rincian_pembayaran = 0; $total_struck = 0; ?>
                                <?php foreach ($cek_jenis_bayar as $key => $value) : ?>
                                    <?php $total_rincian_pembayaran += $value['tagihan_nota']; $total_struck += $value['jumlah_struck']; ?>
                                    <tr>
                                        <td><?= $value['nama_jenis_pembayaran'] ?></td>
                                        <td align="right">Rp <?= number_format($value['tagihan_nota']) ?></td>
                                        <td align="right"><?= number_format($value['jumlah_struck']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td ><b>TOTAL</b></td>
                                    <td align="right"><b>Rp <?= number_format($total_rincian_pembayaran) ?></b></td>
                                    <td align="right"><b><?= number_format($total_struck) ?></b></td>
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
                    <div class="col-md-3">
                        <label class="sr-only" for="inlineFormInputGroupUsername2">Username</label>
                          <div class="input-group mb-2 mr-sm-2">
                            <div class="input-group-prepend">
                              <div class="input-group-text">Mulai</div>
                            </div>
                            <input type="date" class="form-control" name="mulai" value="<?= date("Y-m-d") ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="sr-only" for="inlineFormInputGroupUsername2">Username</label>
                          <div class="input-group mb-2 mr-sm-2">
                            <div class="input-group-prepend">
                              <div class="input-group-text">Akhir</div>
                            </div>
                            <input type="date" class="form-control" name="akhir" value="<?= date("Y-m-d") ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="sr-only" for="inlineFormInputGroupUsername2">Username</label>
                          <div class="input-group mb-2 mr-sm-2">
                            <div class="input-group-prepend">
                              <div class="input-group-text">Kasir</div>
                            </div>
                            <input type="hidden" name="id_kasir" id="input_id_kasir">
                            <input type="text" class="form-control" id="input_nama_kasir" name="nama_kasir" placeholder="Nama Kasir">
                            <span class="input-group-btn"> 
                              <!-- Button trigger modal -->
                              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-kasir">
                              <i class="fa fa-search"></i>
                              </button>
                            </span>
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


<!-- Modal Pilih Kasir -->
<div class="modal fade" id="modal-kasir">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Pilih Kasir</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hodden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <table class="table table-bodered table-striped" id="example" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kasir</th>
                            <th>Username Kasir</th>
                            <!-- <th>No Telp</th> -->
                            <!-- <td>jenis</td> -->
                            <th>pilih</th>
                        </tr>
                    </thead>
                    <tbody>
                      <!-- looping data Kasir dari masing masing mercahnt -->
                      <?php
                          $sess_kd_merchant = $_SESSION['kd_merchant'];
                          $query = "SELECT * 
                                      FROM merchant_employee 
                                      WHERE status_aktif_employee='Y' AND status_remove_employee = 'N' AND kd_merchant = '$sess_kd_merchant' 
                                      ORDER BY nama_employee ASC";
                          $data_employee = $db->query($query)->fetch_all(MYSQLI_ASSOC);
                      ?>
                      <?php
                        $no = 1;
                        foreach ($data_employee as $o) {
                      ?>
                      <tr>  
                        <td><?= $no++ ?></td>
                        <td><?= $o['nama_employee'] ?></td>
                        <td><?= $o['username_employee'] ?></td>
                        
                        <td>
                          <button class="btn btn-xs btn-info" id="pilih_kasir" 
                            data-nama_kasir="<?=$o['nama_employee']?>"
                            data-id_kasir="<?=$o['id_merchant_employee']?>"
                            >
                            <i class="fa fa-check"></i>Select
                          </button>
                        </td>    
                      </tr> 
                      <?php
                          }
                      ?>
                    </tbody>
                </table>             
            </div>
        </div>
    </div>
</div>


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
         var nama_kasir = "<?= @$nama_kasir ?>"
         var id_kasir = "<?= @$id_kasir ?>"

         window.open('view/laporan_transaksi/excel_laporan_kasir.php?mulai='+mulai+ '&akhir=' +akhir+'&nama_kasir=' +nama_kasir+'&id_kasir='+id_kasir, '__blank');

      }); 
    });

</script>