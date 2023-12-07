<?php

  // CEK FILTER
  if (isset($_GET['tgl_awal']) AND isset($_GET['tgl_akhir']) AND isset($_GET['pembeli']) AND isset($_GET['jenis_pembayaran']) AND isset($_GET['kasir']) ) {

    if ($_GET['tgl_awal'] != "" AND $_GET['tgl_akhir'] != "") {
      $query_tgl = " AND tanggal_transaksi BETWEEN '".$_GET['tgl_awal']." 00:00:00' AND '".$_GET['tgl_akhir']." 23:59:59' ";
    }else{
      $query_tgl = "";
    }

    if ($_GET['pembeli'] != "") {
      $query_pembeli = " AND nama_cust LIKE '%".$_GET['pembeli']."%' ";
    }else{
      $query_pembeli = "";
    }
    
    if ($_GET['jenis_pembayaran'] != "") {
      $query_jenis_pembayaran = " AND kd_jenis_pembayaran = '".enkripsiDekripsi($_GET['jenis_pembayaran'], 'dekripsi')."' ";
    }else{
      $query_jenis_pembayaran = "";
    }
    
    if ($_GET['kasir'] != "") {
      $query_kasir = " AND kd_admin = '".enkripsiDekripsi($_GET['kasir'], 'dekripsi')."' ";
    }else{
      $query_kasir = "";
    }

  }else{
    $query_tgl = "";
    $query_pembeli = "";
    $query_jenis_pembayaran = "";
    $query_kasir = "";
  }
  // END CEK FILTER
  
  
  $mindate = date('Y-m-d', strtotime('-1 months'));

  // PAGINATION
  $perpage = 10;
  $total_transaksi = $db->query("SELECT COUNT(*) AS jml FROM transaksi WHERE status_transaksi!='3' AND DATE(tanggal_transaksi)>'$mindate' $query_tgl $query_pembeli $query_kasir $query_jenis_pembayaran")->fetch_assoc()['jml'];
  $jmlhalaman = ceil($total_transaksi / $perpage);
  $halamanaktif = ( isset($_GET['p'])) ? $_GET['p'] : 1;
  $awal = ( $halamanaktif - 1 ) * $perpage;
  // END PAGINATION


  // $total_transaksi = $db->query("SELECT COUNT(*) AS jml FROM transaksi WHERE status_transaksi!='3'")->fetch_assoc()['jml'];

  $list_transaksi = $db->query("SELECT * FROM transaksi A JOIN admin B ON A.kd_admin=B.id_admin JOIN shift C ON A.kd_shift=C.id_shift JOIN jenis_pembayaran D ON A.kd_jenis_pembayaran=D.id_jenis_pembayaran WHERE status_transaksi!='3' AND DATE(A.tanggal_transaksi)>'$mindate' $query_tgl $query_pembeli $query_kasir $query_jenis_pembayaran ORDER BY A.id_transaksi DESC LIMIT $awal, $perpage")->fetch_all(MYSQLI_ASSOC);
  
  $query = "SELECT * FROM jenis_tiket WHERE status_display_tiket='Y' AND status_remove_tiket='N'";
  $jenis_tiket = $db->query($query)->fetch_all(MYSQLI_ASSOC);


  // CEK TIKET UNTUK HARI INI
  $hari_sekarang = date('N');
  $tanggal_sekarang = date('d');
  $jam_sekarang = date("H:i");
  $tahun_bulan_sekarang = date('Y-m');

  $hari_libur = @$db->query("SELECT COUNT(hari_libur) AS jml FROM hari_libur WHERE tahun_bulan='$tahun_bulan_sekarang' AND hari_libur LIKE '%$tanggal_sekarang%'")->fetch_assoc()['jml'];
  if ($hari_libur == "") {
    $hari_libur = 0;
  }
  if ((int)$hari_libur > 0) { //KETIKA HARI INI HARI LIBUR
    $tiket_sekarang = $db->query("SELECT * FROM jenis_tiket WHERE status_hari_libur='3' AND status_display_tiket='Y' AND start_jam<='$jam_sekarang' AND end_jam>='$jam_sekarang'  AND status_remove_tiket='N'")->fetch_assoc();
    if ($tiket_sekarang == "") {
      $tiket_sekarang = $db->query("SELECT * FROM jenis_tiket WHERE start_hari<='$hari_sekarang' AND end_hari>='$hari_sekarang' AND status_display_tiket='Y' AND start_jam<='$jam_sekarang' AND end_jam>='$jam_sekarang'  AND status_remove_tiket='N'")->fetch_assoc();
    }
  }else{ //KETIKA HARI INI BUKAN HARI LIBUR
    $tiket_sekarang = $db->query("SELECT * FROM jenis_tiket WHERE start_hari<='$hari_sekarang' AND end_hari>='$hari_sekarang' AND status_display_tiket='Y' AND start_jam<='$jam_sekarang' AND end_jam>='$jam_sekarang'  AND status_remove_tiket='N'")->fetch_assoc();
  }



    $list_pembayaran = $db->query("SELECT * FROM jenis_pembayaran WHERE status_aktif='Y' ");
    $list_kasir = $db->query("SELECT * FROM admin WHERE jabatan_admin='2' AND status_rmv_admin='N' ");
    
    
  // ===========================================

  function nomor_ke_hari($nomor_hari){
    if($nomor_hari == '1'){
      return 'Senin';
    } elseif($nomor_hari == '2'){
      return 'Selasa';
    } elseif($nomor_hari == '3'){
      return 'Rabu';
    } elseif($nomor_hari == '4'){
      return 'Kamis';
    } elseif($nomor_hari == '5'){
      return 'Jumat';
    } elseif($nomor_hari == '6'){
      return 'Sabtu';
    } elseif($nomor_hari == '7'){
      return 'Minggu';
    }
  }
?>

<style type="text/css">
  .table tbody tr td{
    padding: 0px 10px;
    vertical-align: middle;
  }
  .table thead tr th{
    height: 45px;
    padding: 5px 10px;
    vertical-align: middle;
    background-color: #f0f0f0;
  }
</style>
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <!-- <h1 class="m-0 text-dark">Kelola Transaksi</h1> -->
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
      <?php if (isset($_SESSION['notifikasi'])) { notifikasi($_SESSION['notifikasi']); } ?>
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <div class="float-left">
              <h5 class="mt-1">Data Transaksi</h5>
            </div>
            <div class="text-right">
              <button class="btn btn-sm btn-info btn_modal_transaksi_baru" data-toggle="modal" data-target="#modal_transaksi_baru"><i class="fas fa-plus"></i> | Transaksi Baru</button>
              <button class="btn btn-sm btn-info btn_show_filter" data-show='hide'><i class="fas fa-filter"></i> | Filter</button>
            </div>
          </div>
          <div class="card-body">
            <div id="tab_filter" style="display: none;">
              <form action="" method="GET" enctype="multipart/form-data">
                <div class="row mb-3">
                  <div class="col-md-6 pb-3">
                    <input type="text" name="" class="form-control form-control-sm" id="daterange-btn" readonly>
                    <input type="hidden" name="page" value="<?= @$_GET['page'] ?>">
                    <input type="hidden" name="action" value="<?= @$_GET['action'] ?>">
                    <input type="text" name="tgl_awal" id="tgl_awal" value="<?= date("Y-m-d") ?>" hidden="">
                    <input type="text" name="tgl_akhir" id="tgl_akhir" value="<?= date("Y-m-d") ?>" hidden="">
                  </div>
                  <div class="col-md-6 pb-3">
                    <input type="text" name="pembeli" placeholder="Nama Pembeli" class="form-control form-control-sm">
                  </div>
                  <div class="col-md-6 pb-3">
                    <select class="form-control form-control-sm" name="jenis_pembayaran">
                        <option value="" selected hidden>JENIS PEMBAYARAN</option>
                        <option value="">SEMUA JENIS PEMBAYARAN</option>
                        <?php $pembayaran_filter = "Samua Jenis Pembayaran" ?>
                        <?php foreach ($list_pembayaran as $key => $value): ?>
                            <?php
                                if(enkripsiDekripsi($value['id_jenis_pembayaran'],'enkripsi') == @$_GET['jenis_pembayaran'] ){
                                    $pembayaran_filter = $value['nama_jenis_pembayaran'];
                                }
                            ?>
                            <option value="<?= enkripsiDekripsi($value['id_jenis_pembayaran'],'enkripsi') ?>"><?= $value['nama_jenis_pembayaran'] ?></option>
                        <?php endforeach ?>
                    </select>
                  </div>
                  <div class="col-md-6 pb-3">
                    <select class="form-control form-control-sm" name="kasir">
                        <option value="" selected hidden>KASIR</option>
                        <option value="">SEMUA KASIR</option>
                        <?php $kasir_filter = "Samua Kasir" ?>
                        <?php foreach ($list_kasir as $key => $value): ?>
                            <?php
                                if(enkripsiDekripsi($value['id_admin'],'enkripsi') == @$_GET['kasir'] ){
                                    $kasir_filter = $value['nama_admin'];
                                }
                            ?>
                            <option value="<?= enkripsiDekripsi($value['id_admin'],'enkripsi') ?>"><?= strtoupper($value['nama_admin']) ?></option>
                        <?php endforeach ?>
                    </select>
                  </div>
                  <div class="col-md-12">
                    <button type="submit" class="btn btn-sm btn-block btn-outline-info">Cari</button>
                  </div>
                </div>
                <hr>
              </form>
            </div>
            <?php if (isset($_GET['pembeli'])): ?>
              <div class="mb-2 pl-2" style="border: 1px solid #cecece">
                <a href="?page=transaksi&action=kelola"><button class="badge badge-danger float-right mr-2">Reset Filter</button></a>
                <b>Filter</b>
                <hr style="margin: 0px">
                <table>
                  <tbody>
                    <tr><td>Tanggal</td><td> : </td><td><?= tanggal_indo($_GET['tgl_awal']) ?> - <?= tanggal_indo($_GET['tgl_akhir']) ?></td></tr>
                    <tr><td>Jenis Pembayaran</td><td> : </td><td><?= $pembayaran_filter ?></td></tr>
                    <tr><td>Kasir</td><td> : </td><td><?= $kasir_filter ?></td></tr>
                    <tr><td>Pembeli</td><td> : </td><td><?= (@$_GET['pembeli'] == "")? 'Semua Pembeli' : $_GET['pembeli'];  ?></td></tr>
                  </tbody>
                </table>
              </div>
            <?php endif ?>
            <table id="" class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Tanggal / Jam</th>
                  <th>Admin</th>
                  <th>Pelanggan</th>
                  <th>Tiket</th>
                  <th>Total</th>
                  <th>Pembayaran</th>
                  <th data-searchable="false" data-orderable="false" style="text-align: center;">Detail</th>
                </tr>
              </thead>
              <tbody>
                <?php $total_transaksi = ($total_transaksi > 10 )? $total_transaksi-1: $total_transaksi;  ?>
                <?php $awal += 1 ?>
                <?php $nomor = $awal ?>
                <?php foreach ($list_transaksi as $key => $value): ?>
                  <tr>
                    <th><?= $nomor ?></th>
                    <td><?= date("d-m-y / H:i", strtotime($value['tanggal_transaksi'])) ?> WIB</td>
                    <td><?= $value['nama_admin'] ?></td>
                    <td><?= $value['nama_cust'] ?></td>
                    <td><?= $value['jumlah_tiket'] ?></td>
                    <td>Rp <?= number_format($value['total_transaksi']) ?></td>
                    <td><?= $value['nama_jenis_pembayaran'] ?></td>
                    <td align="center">
                      <a href="?page=transaksi&action=detail&id=<?= enkripsiDekripsi($value['id_transaksi'],'enkripsi') ?>" data-toggle="tooltip" title="Detail">
                        <button class ="btn btn-success btn-sm" style="margin: 0px; padding: 0px 4px 0px 4px"><i class="fas fa-file-invoice"></i></button>
                      </a>
                    </td>
                  </tr>  
                <?php $nomor++; endforeach ?>
              </tbody>
            </table>
            <hr>
              <span class="float-left">Menampilkan <?= $awal ?> - <?= $nomor-1 ?> dari <?= $total_transaksi ?> transaksi</span>


              <nav aria-label="...">
                <ul class="pagination justify-content-end">

                  <!-- Halaman Sebelumnya -->
                  <?php if ($halamanaktif > 1): ?>
                    <li class="page-item">
                      <a class="page-link" href="?page=transaksi&action=kelola<?php if(isset($_GET['tgl_awal'])) {echo "&tgl_awal=".$_GET['tgl_awal'];} ?><?php if(isset($_GET['tgl_akhir'])) {echo "&tgl_akhir=".$_GET['tgl_akhir'];} ?><?php if(isset($_GET['pembeli'])) {echo "&pembeli=".$_GET['pembeli'];} ?><?php if(isset($_GET['jenis_pembayaran'])) {echo "&jenis_pembayaran=".$_GET['jenis_pembayaran'];} ?><?php if(isset($_GET['kasir'])) {echo "&kasir=".$_GET['kasir'];} ?>&p=<?= $halamanaktif-1 ?>">Sebelumnya</a>
                    </li>
                  <?php else: ?>
                    <li class="page-item disabled">
                      <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Sebelumnya</a>
                    </li>
                  <?php endif ?>


                  <!-- <li class="page-item"><a class="page-link" href="#">1</a></li> -->
                  <li class="page-item disabled" aria-current="page" disabled>

                    <input type="text" class="btn_ubah_halaman form-control" data-page="?page=transaksi&action=kelola<?php if(isset($_GET['tgl_awal'])) {echo "&tgl_awal=".$_GET['tgl_awal'];} ?><?php if(isset($_GET['tgl_akhir'])) {echo "&tgl_akhir=".$_GET['tgl_akhir'];} ?><?php if(isset($_GET['pembeli'])) {echo "&pembeli=".$_GET['pembeli'];} ?><?php if(isset($_GET['jenis_pembayaran'])) {echo "&jenis_pembayaran=".$_GET['jenis_pembayaran'];} ?><?php if(isset($_GET['kasir'])) {echo "&kasir=".$_GET['kasir'];} ?>&p=" style="height: 99%; width: 70px; text-align: center;" value="<?= $halamanaktif ?>" >
                    <!--<a class="page-link" href="#" disabled>$halamanaktif <span class="sr-only">(current)</span></a>-->
                  </li>
                  <!-- <li class="page-item"><a class="page-link" href="#">3</a></li> -->


                  <!-- Halaman Selanjutnya -->
                  <?php if ($halamanaktif < $jmlhalaman): ?>
                    <li class="page-item">
                      <a class="page-link" href="?page=transaksi&action=kelola<?php if(isset($_GET['tgl_awal'])) {echo "&tgl_awal=".$_GET['tgl_awal'];} ?><?php if(isset($_GET['tgl_akhir'])) {echo "&tgl_akhir=".$_GET['tgl_akhir'];} ?><?php if(isset($_GET['pembeli'])) {echo "&pembeli=".$_GET['pembeli'];} ?><?php if(isset($_GET['jenis_pembayaran'])) {echo "&jenis_pembayaran=".$_GET['jenis_pembayaran'];} ?><?php if(isset($_GET['kasir'])) {echo "&kasir=".$_GET['kasir'];} ?>&p=<?= $halamanaktif+1 ?>">Selanjutnya</a>
                    </li>
                  <?php else: ?>
                    <li class="page-item disabled">
                      <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Selanjutnya</a>
                    </li>
                  <?php endif ?>

                </ul>
                
                *Kasir hanya dapat melihat riwayat transaksi 1 bulan terakhir

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- Modal -->


<form method="post" id="frm_transaksi_baru" action="view/transaksi/proses_data.php" enctype="multipart/form-data" autocomplete="off" onkeydown="return event.key != 'Enter';">
    <div class="modal fade" id="modal_transaksi_baru" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Tambah Transaksi</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Tiket</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="nama_tiket" value="<?php if(isset($tiket_sekarang['nama_jenis_tiket'])){ 
                    echo $tiket_sekarang['nama_jenis_tiket'].' ( Rp '.number_format($tiket_sekarang['harga_tiket']).' ) '; } ?> "
                  readonly="" required="">
                  <input type="hidden" name="id_jenis_tiket" id="id_jenis_tiket" value="<?= enkripsiDekripsi($tiket_sekarang['id_jenis_tiket'],'enkripsi') ?>">
                  <input type="text" name="harga_tiket_satuan" class="form-control" value="<?= $tiket_sekarang['harga_tiket'] ?>" required readonly hidden>
                </div>
                <div class="col-sm-2">
                  <button type="button" class="btn btn-block btn-outline-info" id="btn_ganti_tiket" data-show='hide'>Ganti</button>
                </div>
              </div>
              <div id="tab_daftar_tiket" style="display: none;">
                <hr>
                <table class="table text-center table-bordered table-sm" style="font-size: 80%;">
                  <thead>
                    <tr>
                      <th>Nama</th>
                      <th>Keterangan</th>
                      <th>Harga</th>
                      <th>Pilih</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($jenis_tiket as $key => $value): ?>
                      <tr>
                        <td style="vertical-align: middle;">
                          <?= $value['nama_jenis_tiket'] ?>
                        </td>
                        <td>
                          <?= angkaKeHari($value['start_hari']) ?> - <?= angkaKeHari($value['end_hari']) ?><br><?= $value['start_jam'] ?> WIB - <?= $value['end_jam'] ?> WIB
                        </td>
                        <td style="vertical-align: middle;">
                          Rp <?= number_format($value['harga_tiket']) ?>
                        </td>
                        <td style="vertical-align: middle;">
                          <button type="button" class="btn btn-sm btn-block btn-outline-info btn_pilih_jenis"
                          data-id="<?= enkripsiDekripsi($value['id_jenis_tiket'],'enkripsi') ?>" 
                          data-nama="<?= $value['nama_jenis_tiket'] ?>" 
                          data-harga="<?= $value['harga_tiket'] ?>"
                           style="padding: 0px 2px;">PILIH</button>
                        </td>
                      </tr>
                    <?php endforeach ?>
                  </tbody>
                </table>
                <hr>
              </div>
              <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Nama Pembeli</label>
                <div class="col-sm-10">
                  <input type="text" name="nama_cust" class="form-control" id="nama_cust" placeholder="Masukkan nama Pembeli">
                </div>
              </div>
              <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Telepon Pembeli</label>
                <div class="col-sm-10">
                  <input type="text" name="telp_cust" class="form-control" id="telp_cust" placeholder="Masukkan telepon Pembeli">
                </div>
              </div>
              <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Jumlah Beli</label>
                <div class="col-sm-10">
                  <input type="text" min="1" class="form-control" placeholder="Masukkan jumlah tiket yang dibeli" name="jumlah_tiket">
                </div>
              </div>
              <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Harga</label>
                <div class="col-sm-10">
                    <div class="row">
                        <div class="col-md-9">
                            <input type="text" class="form-control" placeholder="Total Harga" id="harga_sebelum_diskon" name="harga_sebelum_diskon" readonly>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-secondary btn-block btn_diskon" data-show='hide'>Diskon</button>
                        </div>
                    </div>
                </div>
              </div>
              
              
              <div class="tab_diskon" style="display: none">
                  <div class="form-group row">
                    <label for="inputPassword" class="col-sm-2 col-form-label">Diskon</label>
                    
                    <div class="col-sm-10">
                        <div class="row">
                            <div class="col-md-9">
                                <input type="text" class="form-control" placeholder="Harap Pilih Jenis Diskon" name="isi_diskon" readonly>
                                <input type="number" name="nominal_diskon" value="0" hidden="">
                                
                                <div class="float-right spinner_validasi_voucher" style="display:none; margin-top: -30px; margin-right: 10px;"><div class="spinner-border spinner-border-sm" role="status" ></div></div>
                                <small class="notif_diskon" style="display:none;"></small>
                                
                            </div>
                            <div class="col-md-3">
                              <select class="form-control" name="jenis_diskon">
                                <option value="">Tanpa Diskon</option>
                                <option value="persen">Persen</option>
                                <option value="rupiah">Rupiah</option>
                                <option value="voucher">Voucher</option>
                              </select>
                            </div>
                        </div>
                    </div>
                  </div>
                  
                  <div class="form-group row">
                    <label for="inputPassword" class="col-sm-2 col-form-label">Total Harga</label>
                    <div class="col-sm-10">
                      <input type="text" name="harga_setelah_diskon" class="form-control" id="harga_setelah_diskon"value="Rp 0" readonly style="border: 1px solid green">
                    </div>
                  </div>
              </div>
              
              <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Bayar</label>
                <div class="col-sm-10">
                    <div class="row">
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="bayar" onkeyup="format_rupiah(this)" placeholder="Jumlah Pembayaran" readonly required>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" name="jenis_pembayaran" required>
                                <option value="" >Pilih</option>
                                <?php foreach ($list_pembayaran as $key => $value): ?>
                                    <option value="<?= enkripsiDekripsi($value['id_jenis_pembayaran'],'enkripsi') ?>"><?= $value['nama_jenis_pembayaran'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>
              </div>
              <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Kembalian</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="kembalian" placeholder="Kembalian" readonly required>
                </div>
              </div>
              <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Keterangan</label>
                <div class="col-sm-10">
                  <textarea class="form-control" name="keterangan" rows="2"></textarea>
                </div>
              </div>
              <div class="custom-control custom-switch text-right">
                  <label class="mr-5">Cetak Tiket Satuan</label>
                  <input type="checkbox" class="custom-control-input btn_saklar_tiket" id="cetak_barcode" name="cetak_barcode" value='on'> 
                  <label class="custom-control-label" for="cetak_barcode"></label>
              </div>
            </div>
            <div class="modal-footer">
              <input type="hidden" name="tambah_transaksi_baru">
              <button type="submit" id="btn_submit_transaksi_baru" class="btn btn-success btn-block">PROSES TRANSAKSI</button>
            </div>
        </div>
      </div>
    </div>
</form>


<script type="text/javascript">
  $(document).ready(function(){
    $('.openPopup').on('click',function(){
      var dataURL = $(this).attr('data-href');
      $('.modal-content').load(dataURL,function(){
        $('#myModal').modal({show:true});
      });
    }); 
  });

  $(document).on('keypress',function(e) {
      if(e.which == 13) {
        if(!$('.modal.show').length){
          $(".btn_modal_transaksi_baru").trigger("click");
          setTimeout( function(){ 
            $("#modal_transaksi_baru #nama_cust").focus();
          }  , 500 );
        }
      }
  });
  
  
  
  function hitungbiaya(){

  var harga_produk = parseInt($("#modal_transaksi_baru input[name=harga_tiket_satuan]").val().replace(/[^0-9]/g, ''))  || 0;
  var jumlah_produk = parseInt($("#modal_transaksi_baru input[name=jumlah_tiket]").val().replace(/[^0-9]/g, ''))  || 0;
  var diskon_rupiah = parseInt($("#modal_transaksi_baru input[name=nominal_diskon]").val().replace(/[^0-9]/g, ''))  || 0;
  var dibayar = parseInt($("#modal_transaksi_baru input[name=bayar]").val().replace(/[^0-9]/g, ''))  || 0;
  
//  alert(harga_produk+" - "+jumlah_produk+" - "+diskon_rupiah+" - "+dibayar);

  var harga_sebelum_diskon = harga_produk * jumlah_produk;
  var harga_setelah_diskon = harga_sebelum_diskon - diskon_rupiah;
  var kembalian = dibayar - harga_setelah_diskon;
  if(kembalian < 0){
    kembalian = 0;
  }

  $("#modal_transaksi_baru input[name=harga_sebelum_diskon]").val("Rp "+parseInt(harga_sebelum_diskon).toLocaleString());
  $("#modal_transaksi_baru input[name=harga_setelah_diskon]").val("Rp "+parseInt(harga_setelah_diskon).toLocaleString());
  $("#modal_transaksi_baru input[name=kembalian]").val("Rp "+parseInt(kembalian).toLocaleString());

  }
  

  $(function () {
    $('#daterange-btn').daterangepicker(
        {
          minDate : '<?= date("m/d/Y", strtotime($mindate)) ?>'
        },
      function (start, end) {
        $("#tgl_awal").val(start.format('YYYY-MM-D'));
        $("#tgl_akhir").val(end.format('YYYY-MM-D'));
      }
    )
  })

  function format_rupiah(arg){
    var bayar = $(arg).val().replace(/[^0-9]/g, '');;
    if (bayar == 0) {bayar = 0}
    $(arg).val("Rp "+parseInt(bayar).toLocaleString());
  }
  
  function format_nominal(arg){
    var bayar = $(arg).val().replace(/[^0-9]/g, '');
    if (bayar == 0) {bayar = 0}
    $(arg).val(parseInt(bayar).toLocaleString());
  }

  $(".btn_pilih_jenis").click(function(){
    var id = $(this).attr("data-id");
    var nama = $(this).attr("data-nama");
    var harga = $(this).attr("data-harga");

    $("#nama_tiket").val(nama+" ( "+"Rp "+parseInt(harga).toLocaleString()+" )");
    $("#id_jenis_tiket").val(id);
    $("input[name=harga_tiket_satuan]").val("Rp "+parseInt(harga).toLocaleString());
    $("#btn_ganti_tiket").trigger("click");
    $("#modal_transaksi_baru input[name=jumlah_tiket]").val("")
    $("#modal_transaksi_baru select[name=jenis_pembayaran]").val("").change();
    hitungbiaya()
  });
  
  
  function hitungbiaya(){

  var jenis_diskon = $('#modal_transaksi_baru select[name=jenis_diskon] option').filter(':selected').val();
  var isi_diskon = parseInt($("#modal_transaksi_baru input[name=isi_diskon]").val().replace(/[^0-9]/g, '')) || 0;
  if(jenis_diskon == "rupiah"){
         
    $("#modal_transaksi_baru input[name=isi_diskon]").val("Rp "+parseInt(isi_diskon).toLocaleString());
    $('#modal_transaksi_baru input[name=nominal_diskon]').val(isi_diskon);
    
    hitungbiaya2()

  }else if(jenis_diskon == "persen"){
        
        var harga_satuan= parseInt($("#modal_transaksi_baru input[name=harga_tiket_satuan]").val().replace(/[^0-9]/g, '')) || 0;
      var jumlah_dibeli = parseInt($("#modal_transaksi_baru input[name=jumlah_tiket]").val().replace(/[^0-9]/g, '')) || 0;
        var harga_non_diskon = harga_satuan * jumlah_dibeli;
        
    if(isi_diskon > 100){
            isi_diskon = 100;
        }
        
        var total = (harga_non_diskon * isi_diskon / 100);

        $('#modal_transaksi_baru input[name=isi_diskon]').val(isi_diskon+" %");
    $('#modal_transaksi_baru input[name=nominal_diskon]').val(total);
    
    hitungbiaya2()
    
  }else if(jenis_diskon == "voucher"){
      
      $(".spinner_validasi_voucher").show();
        var kode = $("#modal_transaksi_baru input[name=isi_diskon]").val();
        
        $.ajax({
            url : 'view/transaksi/proses_data.php?cek_voucher='+kode,
            success:function(result){
                
              var data = result;
              var obj = JSON.parse(data);
              // alert(obj.kode + ", " + obj.value + ", " + obj.tipe);
              
              
              
              if(obj.kode == "N"){
                  var info = "<span class='text-red'>*Kode voucher tidak tersedia</span>";
                  $(".notif_diskon").html(info);
                  $(".notif_diskon").show();
              }else{
                  
                  var harga_satuan= parseInt($("#modal_transaksi_baru input[name=harga_tiket_satuan]").val().replace(/[^0-9]/g, '')) || 0;
                var jumlah_dibeli = parseInt($("#modal_transaksi_baru input[name=jumlah_tiket]").val().replace(/[^0-9]/g, '')) || 0;
                  var harga_non_diskon = harga_satuan * jumlah_dibeli;

                  var tipe = obj.tipe;
                  var nominal = parseInt(obj.value);
                  
                  if(tipe == "2"){ // PERSEN
                      
                      if(nominal > 100){
                          nominal = 100;
                      }
                      
                      var harga_diskon = (harga_non_diskon * nominal / 100);
                      
                      var info = "<span class='text-green'>**Voucher berhasil digunakan. ( Potongan "+nominal+"% )</span>";
                      $(".notif_diskon").html(info);
                      $(".notif_diskon").show();
                      $('#modal_transaksi_baru input[name=nominal_diskon]').val(harga_diskon);
                        
                  }else if(tipe == "1"){ //NOMINAL
                  
                      var harga_diskon = nominal;
                      
                      var info = "<span class='text-green'>**Voucher berhasil digunakan. ( Potongan harga Rp"+parseInt(harga_diskon).toLocaleString()+",- )</span>";
                      $(".notif_diskon").html(info);
                      $(".notif_diskon").show();
                      $('#modal_transaksi_baru input[name=nominal_diskon]').val(harga_diskon);
                     
                  }else{
                       alert("Kegagalan Sistem");
                  }
              }
          
          hitungbiaya2()
              $(".spinner_validasi_voucher").hide();
                
            }
            
        })
        
        
    $('#modal_transaksi_baru input[name=nominal_diskon]').val("");

  }else{
        hitungbiaya2()
  }

  }
  
  function hitungbiaya2(){
    var harga_produk = parseInt($("#modal_transaksi_baru input[name=harga_tiket_satuan]").val().replace(/[^0-9]/g, '')) || 0;
    var jumlah_produk = parseInt($("#modal_transaksi_baru input[name=jumlah_tiket]").val().replace(/[^0-9]/g, '')) || 0;
    var diskon_rupiah = parseInt($("#modal_transaksi_baru input[name=nominal_diskon]").val().replace(/[^0-9]/g, '')) || 0;
    var dibayar = parseInt($("#modal_transaksi_baru input[name=bayar]").val().replace(/[^0-9]/g, '')) || 0;
    
    var harga_sebelum_diskon = harga_produk * jumlah_produk;
    var harga_setelah_diskon = harga_sebelum_diskon - diskon_rupiah;
    var kembalian = dibayar - harga_setelah_diskon;
    if(kembalian < 0){
      kembalian = 0;
    }
    
    $("#modal_transaksi_baru input[name=harga_sebelum_diskon]").val("Rp "+parseInt(harga_sebelum_diskon).toLocaleString());
    $("#modal_transaksi_baru input[name=harga_setelah_diskon]").val("Rp "+parseInt(harga_setelah_diskon).toLocaleString());
    $("#modal_transaksi_baru input[name=kembalian]").val("Rp "+parseInt(kembalian).toLocaleString());
    
    $(".spinner_validasi_voucher").hide();
  }
  
  

  $(".btn_show_filter").click(function(){
      $("#tab_filter").toggle(200);
  })

  $("#btn_ganti_tiket").click(function(){
      $("#tab_daftar_tiket").toggle(200);
  })
  
   $(".btn_diskon").click(function(){
      $(".tab_diskon").toggle(200);
      $('#modal_transaksi_baru input[name=isi_diskon]').val("");
      hitungbiaya()
   })


  $("input[name=jumlah_tiket]").keyup(function(){
      $("#modal_transaksi_baru select[name=jenis_pembayaran]").val("").change();
      hitungbiaya();
    // var harga_satuan = $("input[name=harga_tiket_satuan]").val().replace(/[^0-9]/g, '');
    // var jumlah_tiket = $("input[name=jumlah_tiket]").val().replace(/[^0-9]/g, '');;
    // var harga_sebelum_diskon = harga_satuan * jumlah_tiket ;

    // $("input[name=harga_sebelum_diskon]").val("Rp "+parseInt(harga_sebelum_diskon).toLocaleString());
    // $("input[name=harga_setelah_diskon]").val("Rp "+parseInt(harga_sebelum_diskon).toLocaleString());
  })

  $("input[name=bayar]").keyup(function(){
      hitungbiaya()
    // var harga_sebelum_diskon = $("input[name=harga_sebelum_diskon]").val().replace(/[^0-9]/g, '');
    // var harga_setelah_diskon = $("input[name=harga_setelah_diskon]").val().replace(/[^0-9]/g, '');
    // var bayar = $("input[name=bayar]").val().replace(/[^0-9]/g, '');
    // var total_kembalian = bayar - harga_setelah_diskon;

    // if(total_kembalian < 0){
    //   total_kembalian = 0;      
    // }
    // $("input[name=kembalian]").val("Rp "+parseInt(total_kembalian).toLocaleString());
  })
  
  
  $("#modal_transaksi_baru select[name=jenis_pembayaran]").change(function(){
      var id = $('#modal_transaksi_baru select[name=jenis_pembayaran] option').filter(':selected').val()
      
      if(id == ""){
          $('#modal_transaksi_baru input[name=bayar]').val("");
          $('#modal_transaksi_baru input[name=bayar]').prop('readonly', true);
      }else{
          $('#modal_transaksi_baru input[name=bayar]').val($('#modal_transaksi_baru #harga_setelah_diskon').val());
          $('#modal_transaksi_baru input[name=bayar]').prop('readonly', false);
      }
      
      
      hitungbiaya()
      
  })
  
  
  $("#modal_transaksi_baru select[name=jenis_diskon]").change(function(){
      
      var id = $('#modal_transaksi_baru select[name=jenis_diskon] option').filter(':selected').val()
      
      if(id == ""){
          $('#modal_transaksi_baru input[name=isi_diskon]').attr("placeholder", "Harap Pilih Jenis Diskon");
          $('#modal_transaksi_baru input[name=isi_diskon]').val("");
          $('#modal_transaksi_baru input[name=nominal_diskon]').val("");
          $('#modal_transaksi_baru input[name=isi_diskon]').prop('readonly', true);
      }else{
          
          if(id == "rupiah"){
              $('#modal_transaksi_baru input[name=isi_diskon]').val("");
              $('#modal_transaksi_baru input[name=nominal_diskon]').val("");
              $('#modal_transaksi_baru input[name=isi_diskon]').prop('readonly', false);
              $('#modal_transaksi_baru input[name=isi_diskon]').attr("placeholder", "Masukkan Total Diskon Dalam Satuan Rupiah");
          }else if(id == "persen"){
              $('#modal_transaksi_baru input[name=isi_diskon]').val("");
              $('#modal_transaksi_baru input[name=nominal_diskon]').val("");
              $('#modal_transaksi_baru input[name=isi_diskon]').prop('readonly', false);
              $('#modal_transaksi_baru input[name=isi_diskon]').attr("placeholder", "Masukkan Total Diskon Dalam Satuan Persen");
          }else if(id == "voucher"){
              $('#modal_transaksi_baru input[name=isi_diskon]').val("");
              var id = $('#modal_transaksi_baru select[name=jenis_diskon] option').filter(':selected').val();
              $('#modal_transaksi_baru input[name=isi_diskon]').prop('readonly', false);
              $('#modal_transaksi_baru input[name=isi_diskon]').attr("placeholder", "Masukkan Kode Voucher");
          }else{
              alert("Kegagalan Sistem");
          }
      }
      
      $(".notif_diskon").hide();
      hitungbiaya()
      
  })
  
  $("input[name=isi_diskon]").keyup(function(){
      
      hitungbiaya();
      
  })
  
  
  $(".btn_ubah_halaman").change(function(){
      var url = $(this).attr("data-page");
      var hal = parseInt($('.btn_ubah_halaman').val().replace(/[^0-9]/g, ''))  || 1
      
      window.location.href = url+hal;
  })


  $("#btn_submit_transaksi_baru").click(function(e){
    e.preventDefault();
    var jumlah_produk = parseInt($("#modal_transaksi_baru input[name=jumlah_tiket]").val().replace(/[^0-9]/g, ''))  || 0;
    var total_transaksi = parseInt($("#modal_transaksi_baru input[name=harga_setelah_diskon]").val().replace(/[^0-9]/g, ''))  || 0;
    var dibayar = parseInt($("#modal_transaksi_baru input[name=bayar]").val().replace(/[^0-9]/g, ''))  || 0;

    if (jumlah_produk == 0) {alert('Harap isi jumlah tiket')}

    if (dibayar+1 < total_transaksi) {alert('Jumlah yang dibayar kurang dari total transaksi')}

    if (jumlah_produk > 0 && dibayar+1 > total_transaksi) {
      $(this).html(`<?= spinnerMohonTunggu() ?>`);
      $(this).attr("disabled","");
      $("#frm_transaksi_baru").submit();
    }

  })
  


</script>