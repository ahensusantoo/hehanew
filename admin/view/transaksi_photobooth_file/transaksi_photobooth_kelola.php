<?php

  // CEK FILTER
  if (isset($_GET['tgl_awal']) AND isset($_GET['tgl_akhir']) AND isset($_GET['pembeli']) AND isset($_GET['jenis_pembayaran']) AND isset($_GET['kasir']) ) {

    if ($_GET['tgl_awal'] != "" AND $_GET['tgl_akhir'] != "") {
      $query_tgl = " AND tanggal_photoboothambil_transaksi BETWEEN '".$_GET['tgl_awal']." 00:00:00' AND '".$_GET['tgl_akhir']." 23:59:59' ";
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
    
  $mindate = date('Y-m-d', strtotime('-3 months'));

  // PAGINATION
  $perpage = 10;
  $total_transaksi = $db->query("SELECT COUNT(*) AS jml FROM photoboothambil_transaksi A JOIN admin B ON A.kd_admin=B.id_admin JOIN shift C ON A.kd_shift=C.id_shift JOIN jenis_pembayaran D ON A.kd_jenis_pembayaran=D.id_jenis_pembayaran WHERE status_transaksi!='3' AND DATE(A.tanggal_photoboothambil_transaksi)>'$mindate' $query_tgl $query_pembeli $query_kasir $query_jenis_pembayaran")->fetch_assoc()['jml'];
  $jmlhalaman = ceil($total_transaksi / $perpage);
  $halamanaktif = ( isset($_GET['p'])) ? $_GET['p'] : 1;
  $awal = ( $halamanaktif - 1 ) * $perpage;
  // END PAGINATION

  // $total_transaksi = $db->query("SELECT COUNT(*) AS jml FROM transaksi WHERE status_transaksi!='3'")->fetch_assoc()['jml'];

  $list_transaksi = $db->query("SELECT * FROM photoboothambil_transaksi A JOIN admin B ON A.kd_admin=B.id_admin JOIN shift C ON A.kd_shift=C.id_shift JOIN jenis_pembayaran D ON A.kd_jenis_pembayaran=D.id_jenis_pembayaran WHERE status_transaksi!='3' AND DATE(A.tanggal_photoboothambil_transaksi)>'$mindate' $query_tgl $query_pembeli $query_kasir $query_jenis_pembayaran ORDER BY A.tanggal_photoboothambil_transaksi DESC LIMIT $awal, $perpage")->fetch_all(MYSQLI_ASSOC);


  // ===========================================================================================================

  $query = "SELECT * FROM photoboothambil_stan WHERE status_display_photoboothambil='Y' AND status_remove_photoboothambil='N' ";
  $daftar_photobooth = $db->query($query)->fetch_all(MYSQLI_ASSOC);
  
  $list_pembayaran = $db->query("SELECT * FROM jenis_pembayaran WHERE status_aktif='Y' ");
  $list_kasir = $db->query("SELECT * FROM admin WHERE jabatan_admin='3' AND status_rmv_admin='N' ");

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
  .table-riwayat tbody tr td{
    /*padding: 0px 10px;*/
    vertical-align: middle;
  }
  .table-riwayat thead tr th{
    height: 45px;
    /*padding: 5px 10px;*/
    vertical-align: middle;
    background-color: #f0f0f0;
  }

  .table-sm thead tr th{
    height: 30px;
  }

  .table-photobooth tr td, .table-photobooth tr th{
    padding: 5px 10px;
  }

  .btn-less-padding{
    padding: 0px 3px
  }

  .tab_ada_photophooth_dipilih{
    display: none;
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


<section class="content" id="tab_transaksi_baru" style="display: none;">
  <div class="container-fluid">
     
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col">
                <h5>Transaksi Baru</h5>
              </div>
              <div class="col">
                <div>
                  <button type="button" class="float-right btn btn-sm btn-info" style="padding: 0px 5px" data-toggle="modal" data-target="#modal_daftar_photobooth" >Tambah Item</button>
                </div>
              </div>
            </div>
            <hr>
            <form id="frm_transaksi" action="view/transaksi_photobooth_file/proses_data.php" method="POST">
              <div class="row">
                <div class="col-md-6">
                  <table>
                    <tbody>
                      <tr>
                        <td>Pembeli</td>
                        <td> : </td>
                        <td><input type="" class="" name="nama_cust" value=""></td>
                      </tr>
                      <tr>
                        <td>Telepon</td>
                        <td> : </td>
                        <td><input type="" class="" name="telp_cust" value=""></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="col-md-6">
                  <table class="float-right">
                    <tbody>
                      <tr>
                        <td>Kasir</td>
                        <td> : </td>
                        <td><input type="" name="" value="<?= $_SESSION['username'] ?>" readonly></td>
                      </tr>
                      <tr>
                        <td>Tanggal</td>
                        <td> : </td>
                        <td><input type="" name="" value="<?= date("Y-m-d, H:i") ?>" readonly></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <table id="" class="table table-bordered table-hover mt-2 table-sm">
                <thead>
                  <tr>
                    <th>Photobooth</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th data-searchable="false" data-orderable="false" style="text-align: center;">Aksi</th>
                  </tr>
                </thead>
                <tbody id="tab_keranjang_photobooth">
                  <tr class="tab_blm_ada_photophooth_dipilih">
                    <td colspan="5" align="center">Belum ada spot foto dipilih</td>
                  </tr>
                  <tr class="tab_ada_photophooth_dipilih">
                    <td colspan="3" align="right">Total</td>
                    <td><span id="total_sebelum_diskon"></span></td>
                    <td></td>
                  </tr>
                  <tr class="tab_ada_photophooth_dipilih">
                    <td colspan="3" align="right"><span id="info_diskon"></span>Diskon</td>
                    <td>
                        <span id="harga_diskon">Rp 0</span>
                    </td>
                    <td align="center">
                        <button type="button" class="btn btn-sm btn-outline-info btn-less-padding" data-toggle="modal" data-target="#modal_diskon_transaksi" style="width: 77%;"><i class="fas fa-plus"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-less-padding btn_hapus_diskon" style="width: 20%;"><i class="far fa-trash-alt"></i></button>
                    </td>
                  </tr>
                  <tr class="tab_ada_photophooth_dipilih">
                    <td colspan="3" align="right">Total Final</td>
                    <td><span id="total_setelah_diskon">Rp 0</span></td>
                    <td></td>
                  </tr>
                  <tr class="tab_ada_photophooth_dipilih">
                    <td colspan="3" align="right">Bayar</td>
                    <td><input type="text" name="jumlah_dibayar" class="form-control form-control-sm" id="jumlah_dibayar" class="" value="Rp 0" onkeyup="format_rupiah(this)" style="height: 20px; width: 100%; border: none; padding: 0px" readonly="" ></td>
                    <td>
                      <select class="form-control form-control-sm" style="height: 23px; font-size: 90%; padding: 0px" name="jenis_pembayaran" required="">
                        <option value="" >Pilih</option>
                        <?php $list_pembayaran = $db->query("SELECT * FROM jenis_pembayaran WHERE status_aktif='Y' "); ?>
                        <?php while($value = $list_pembayaran->fetch_assoc()) : ?>
                            <option value="<?= enkripsiDekripsi($value['id_jenis_pembayaran'],'enkripsi') ?>"><?= $value['nama_jenis_pembayaran'] ?></option>
                        <?php endwhile; ?>
                      </select>
                    </td>
                  </tr>
                  <tr class="tab_ada_photophooth_dipilih">
                    <td colspan="3" align="right">Kembalian</td>
                    <td><span id="jumlah_kembalian">Rp 0</span></td>
                    <td></td>
                  </tr>
                </tbody>
                <tbody>
                  <tr>
                    
                  </tr>
                </tbody>
              </table>
              <div class="form-group">
                  <label>Keterangan Transaksi</label>
                  <textarea class="form-control" name="keterangan" rows="2"></textarea>
              </div>
              <input type="text" name="jenis_diskon" value="" hidden="">
              <input type="text" name="isi_diskon" value="" hidden="">
              <input type="text" name="tambah_transaksi_photobooth_baru" value="" hidden="">
              <button type="submit" id="btn_submit_transaksi_baru" class="btn btn-block btn-info tab_ada_photophooth_dipilih">CETAK TIKET</button>

            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>



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
              <button class="btn btn-sm btn-info btn_transaksi_baru" data-show="hide"><i class="fas fa-plus"></i> | Transaksi Baru</button>
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
              </form>
            </div>
            <?php if (isset($_GET['pembeli'])): ?>
              <div class="mb-2 pl-2" style="border: 1px solid #cecece">
                <a href="?page=transaksi_photobooth&action=kelola"><button class="badge badge-danger float-right mr-2">Reset Filter</button></a>
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
            <table id="" class="table table-bordered table-hover table-riwayat">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Tanggal / Jam</th>
                  <th>Admin</th>
                  <th>Customer</th>
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
                    <td><?= date("d-m-y / H:i", strtotime($value['tanggal_photoboothambil_transaksi'])) ?> WIB</td>
                    <td><?= $value['nama_admin'] ?></td>
                    <td><?= $value['nama_cust'] ?></td>
                    <td><?= $value['jumlah_tiket'] ?></td>
                    <td>Rp <?= number_format($value['total_transaksi']) ?></td>
                    <td><?= $value['nama_jenis_pembayaran'] ?></td>
                    <td align="center">
                        <button class ="btn btn-success btn-sm btn_detail_transaksi" title="Detail" data-id="<?= enkripsiDekripsi($value['id_photoboothambil_transaksi'],'enkripsi') ?>" data-toggle="modal" data-target="#modal_detail" style="margin: 0px; padding: 0px 4px 0px 4px"><i class="fas fa-file-invoice"></i></button>
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
                      <a class="page-link" href="?page=transaksi_photobooth&action=kelola<?php if(isset($_GET['tgl_awal'])) {echo "&tgl_awal=".$_GET['tgl_awal'];} ?><?php if(isset($_GET['tgl_akhir'])) {echo "&tgl_akhir=".$_GET['tgl_akhir'];} ?><?php if(isset($_GET['pembeli'])) {echo "&pembeli=".$_GET['pembeli'];} ?><?php if(isset($_GET['jenis_pembayaran'])) {echo "&jenis_pembayaran=".$_GET['jenis_pembayaran'];} ?><?php if(isset($_GET['kasir'])) {echo "&kasir=".$_GET['kasir'];} ?>&p=<?= $halamanaktif-1 ?>">Sebelumnya</a>
                    </li>
                  <?php else: ?>
                    <li class="page-item disabled">
                      <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Sebelumnya</a>
                    </li>
                  <?php endif ?>


                  <!-- <li class="page-item"><a class="page-link" href="#">1</a></li> -->
                  <li class="page-item disabled" aria-current="page" disabled>
                    <select class="form-control btn_ubah_halaman" data-page="?page=transaksi_photobooth&action=kelola<?php if(isset($_GET['tgl_awal'])) {echo "&tgl_awal=".$_GET['tgl_awal'];} ?><?php if(isset($_GET['tgl_akhir'])) {echo "&tgl_akhir=".$_GET['tgl_akhir'];} ?><?php if(isset($_GET['pembeli'])) {echo "&pembeli=".$_GET['pembeli'];} ?><?php if(isset($_GET['jenis_pembayaran'])) {echo "&jenis_pembayaran=".$_GET['jenis_pembayaran'];} ?><?php if(isset($_GET['kasir'])) {echo "&kasir=".$_GET['kasir'];} ?>&p=" style="height: 94%">
                        <?php for ($i=1; $i <= $jmlhalaman ; $i++) : ?>
                            <option value="<?= $i ?>" <?php if($halamanaktif == $i) {echo "selected"; }?> ><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                    <!--<a class="page-link" href="#" disabled>$halamanaktif <span class="sr-only">(current)</span></a>-->
                  </li>
                  <!-- <li class="page-item"><a class="page-link" href="#">3</a></li> -->


                  <!-- Halaman Selanjutnya -->
                  <?php if ($halamanaktif < $jmlhalaman): ?>
                    <li class="page-item">
                      <a class="page-link" href="?page=transaksi_photobooth&action=kelola<?php if(isset($_GET['tgl_awal'])) {echo "&tgl_awal=".$_GET['tgl_awal'];} ?><?php if(isset($_GET['tgl_akhir'])) {echo "&tgl_akhir=".$_GET['tgl_akhir'];} ?><?php if(isset($_GET['pembeli'])) {echo "&pembeli=".$_GET['pembeli'];} ?><?php if(isset($_GET['jenis_pembayaran'])) {echo "&jenis_pembayaran=".$_GET['jenis_pembayaran'];} ?><?php if(isset($_GET['kasir'])) {echo "&kasir=".$_GET['kasir'];} ?>&p=<?= $halamanaktif+1 ?>">Selanjutnya</a>
                    </li>
                  <?php else: ?>
                    <li class="page-item disabled">
                      <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Selanjutnya</a>
                    </li>
                  <?php endif ?>

                </ul>
                *Kasir hanya dapat melihat riwayat transaksi 3 bulan terakhir
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- Modal -->


<div class="modal fade" id="modal_daftar_photobooth" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Daftar Photobooth</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <table class="table-bordered table-photobooth" width="100%">
            <thead>
              <tr>
                <th>Nama</th>
                <th>Harga</th>
                <th>Pilih</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($daftar_photobooth as $key => $value): ?>
                <tr>
                  <td><?= $value['nama_photoboothambil_stan'] ?></td>
                  <td nowrap="">Rp <?= number_format($value['harga_photoboothambil_stan']) ?></td>
                  <td nowrap=""><button type="button" class="btn btn-sm btn-outline-info btn-block btn-less-padding btn_pilih_photobooth" data-id="<?= enkripsiDekripsi($value['id_photoboothambil_stan'],'enkripsi') ?>" data-nama="<?= $value['nama_photoboothambil_stan'] ?>" data-harga="<?= $value['harga_photoboothambil_stan'] ?>">Pilih</button></td>
                </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
    </div>
  </div>
</div>


<div class="modal fade" id="modal_diskon_transaksi" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Diskon</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body text-center">
          <form id="frm_diskon">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="radio_voucher" id="rv1" value="harga">
              <label class="form-check-label" for="rv1">Potongan Harga</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="radio_voucher" id="rv2" value="persen" checked>
              <label class="form-check-label" for="rv2">Persen</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="radio_voucher" id="rv3" value="voucher">
              <label class="form-check-label" for="rv3">Voucher</label>
            </div>

            <div class="form-group mt-2">
              <input type="text" class="form-control" name="isi_diskon" id="isi_diskon" placeholder="Masukkan jumlah diskon dalam satuan persen (%)">
              <small class="info_voucher_kosong text-red" style="display: none;">KODE VOUCHER TIDAK TERSEDIA</small>
            </div>

            <button type="button" class="btn btn-info btn-block btn_gunakan_diskon">Gunakan</button>
          </form>

        </div>
    </div>
  </div>
</div>



<!-- Modal -->
<div class="modal fade" id="modal_detail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail Transaksi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">

    $(".btn_detail_transaksi").click(function(){
        var id = $(this).attr("data-id");
        $("#modal_detail .modal-body").html(`<?= spinnerMemuat() ?>`);
        $.ajax({
            url: 'view/transaksi_photobooth_file/transaksi_photobooth_detail.php?id='+id,
            success:function(result){
                $("#modal_detail .modal-body").html(result);
            }
        })
    })


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

  $(".btn_transaksi_baru").click(function(){
      $("#tab_transaksi_baru").toggle(300);
  })



  function hitungBiaya(){
    var total_rupiah = 0;
    $('#frm_transaksi .list_barang').each(function(index){
      var harga  = parseInt($(this).attr("data-harga"));
      var jumlah = parseInt($(this).val());
      var class_sub_total = "."+$(this).attr("data-class-subtotal");
      var subtotal_harga = jumlah * harga;
      total_rupiah = total_rupiah + subtotal_harga;

      $(class_sub_total).html(parseInt(subtotal_harga).toLocaleString());
      $("#total_sebelum_diskon").html("Rp "+parseInt(total_rupiah).toLocaleString());
      // $("#harga_total").val(total_rupiah);
    });

    var harga_diskon = parseInt($("#harga_diskon").html().replace(/[^0-9]/g, ''));
    var total_setelah_diskon = total_rupiah - harga_diskon;
    var jumlah_dibayar = parseInt($("#jumlah_dibayar").val().replace(/[^0-9]/g, ''));
    var jumlah_kembalian = jumlah_dibayar - total_setelah_diskon;
    if(jumlah_kembalian < 0){
      jumlah_kembalian = 0;
    }

    $("#total_setelah_diskon").html("Rp "+parseInt(total_setelah_diskon).toLocaleString());
    $("#jumlah_kembalian").html("Rp "+parseInt(jumlah_kembalian).toLocaleString());

  }


  var no_list = 0;
  $(".btn_pilih_photobooth").click(function(){

    $(".tab_ada_photophooth_dipilih").show();
    $(".tab_blm_ada_photophooth_dipilih").hide();

    no_list = no_list+1;
    var id = $(this).attr("data-id");
    var nama = $(this).attr("data-nama");
    var harga = parseInt($(this).attr("data-harga"));

    var id_replace = id.replace(/=/g, "");

    $("#modal_daftar_photobooth .close").trigger("click");

    if($('.produk-ke-'+id_replace+'').val()) {
      var cek_keranjang = 1;
    }else{
      var cek_keranjang = 0;
    }

    if (cek_keranjang == 0) {
      $("#tab_keranjang_photobooth").prepend(`
        <tr>
          <td>`+nama+`</td>
          <td>Rp `+parseInt(harga).toLocaleString()+`</td>
          <td>
          <input type="number" min="1" name="photobooth_dibeli[`+id+`]" value="1" class="form-control list_barang produk-ke-`+id_replace+`" style="width: 100px; height: 30px;"
          data-id="`+id+`" 
          data-harga="`+harga+`" 
          data-class-subtotal="st-`+no_list+`"
          >
          </td>
          <td>Rp <span class="st-`+no_list+`"></span></td>
          <td><button type="button" class="btn btn-sm btn-outline-danger btn-block btn-less-padding btn_delete_keranjang"><i class='far fa-trash-alt'></i></button></td>
        </tr>
        `);
    }

    if (cek_keranjang == 1) {
      $('.produk-ke-'+id_replace+'').val(parseInt($('.produk-ke-'+id_replace+'').val()) + 1);
    }

    hitungBiaya()

  })


  $("#frm_transaksi").on('click', '.btn_delete_keranjang', function(){
    $(this).closest( "tr" ).remove();

    if(!$('.list_barang').val()) {
      $(".tab_blm_ada_photophooth_dipilih").fadeIn();
      $(".tab_ada_photophooth_dipilih").hide();
    }

    hitungBiaya();
  })


  $("#frm_transaksi").on('change', '.list_barang', function(){
    hitungBiaya();
  })

  $("#jumlah_dibayar").keyup(function(){
    hitungBiaya();
  })

  $("#frm_diskon input[name=radio_voucher]").change(function(){
    var jenis = $(this).val();

    if(jenis == "harga"){
      $('#isi_diskon').attr("placeholder", "Masukkan jumlah diskon dalam satuan rupiah (Rp)");
    }else if(jenis == "persen"){
      $('#isi_diskon').attr("placeholder", "Masukkan jumlah diskon dalam satuan persen (%)");
    }else if(jenis == "voucher"){
      $('#isi_diskon').attr("placeholder", "Masukkan Kode Voucher");
    }else{
      alert("Kegagalan Sistem")
    }
  })

  $("#frm_diskon input[name=isi_diskon]").keyup(function(){
    $(".info_voucher_kosong").hide();
  })
  
  $("#frm_diskon").submit(function(e){

      e.preventDefault();
      $(".btn_gunakan_diskon").trigger("click");
      
  })

  $(".btn_gunakan_diskon").click(function(){
    var jenis = $("#frm_diskon input[name=radio_voucher]:checked").val();
    var value = parseInt($("#frm_diskon input[name=isi_diskon]").val().replace(/[^0-9]/g, ''));
    var harga_sebelum_diskon = parseInt($("#total_sebelum_diskon").html().replace(/[^0-9]/g, ''));


    if(jenis == "harga"){
      var total = harga_sebelum_diskon - value;
      if(total < 0){
          total = 0;      
      }else{
        total = value;
      }


      document.getElementById("frm_diskon").reset();
      var info = "<span class='text-blue'>( Potongan Harga ) </span>";
      $("#info_diskon").html(info);
      $("#harga_diskon").html("Rp "+parseInt(value).toLocaleString());
      $("#modal_diskon_transaksi .close").trigger("click");
      $("#frm_transaksi input[name=isi_diskon]").val(value);
      $("#frm_transaksi input[name=jenis_diskon]").val(jenis);
      hitungBiaya()

    }else if(jenis == "persen"){
      if(value > 100){
          value = 100;
      }
      var total = harga_sebelum_diskon * value / 100;

      document.getElementById("frm_diskon").reset();
      var info = "<span class='text-blue'>( Potongan sebesar "+value+"% ) </span>";
      $("#info_diskon").html(info);
      $("#harga_diskon").html("Rp "+parseInt(total).toLocaleString());
      $("#modal_diskon_transaksi .close").trigger("click");
      $("#frm_transaksi input[name=isi_diskon]").val(value);
      $("#frm_transaksi input[name=jenis_diskon]").val(jenis);
      hitungBiaya()

    }else if(jenis == "voucher"){
      var kode_voucher = $("#frm_diskon input[name=isi_diskon]").val();
      $.ajax({
          url : 'view/transaksi_photobooth_file/proses_data.php?cek_voucher='+kode_voucher,
          success:function(result){

            var data = result;
            var obj = JSON.parse(data);
            // alert(obj.kode + ", " + obj.value + ", " + obj.tipe);
            
            if(obj.kode == "N"){
                var info = "<span class='text-red'>KODE VOUCHER TIDAK TERSEDIA</span>";
                $(".info_voucher_kosong").show();
                // $("#modal_diskon_transaksi .close").trigger("click");
                hitungBiaya()
            }else{

                var tipe = obj.tipe;
                var nominal = parseInt(obj.value);
                
                if(tipe == "2"){ // PERSEN

                    if(nominal > 100){
                        nominal = 100;
                    }
                    var total = harga_sebelum_diskon * nominal / 100;

                    document.getElementById("frm_diskon").reset();
                    var info = "<span class='text-blue'>( Potongan sebesar "+nominal+"% ) </span>";
                    $("#info_diskon").html(info);
                    $("#harga_diskon").html("Rp "+parseInt(total).toLocaleString());
                    $("#modal_diskon_transaksi .close").trigger("click");
                    $("#frm_transaksi input[name=isi_diskon]").val(kode_voucher);
                    $("#frm_transaksi input[name=jenis_diskon]").val(jenis);
                    hitungBiaya();

                }else if(tipe == "1"){ //NOMINAL

                    var total = harga_sebelum_diskon - nominal;
                    if(total < 0){
                        total = 0;      
                    }else{
                      total = nominal;
                    }

                    document.getElementById("frm_diskon").reset();
                    var info = "<span class='text-blue'>( Potongan Harga ) </span>";
                    $("#info_diskon").html(info);
                    $("#harga_diskon").html("Rp "+parseInt(total).toLocaleString());
                    $("#modal_diskon_transaksi .close").trigger("click");
                    $("#frm_transaksi input[name=isi_diskon]").val(kode_voucher);
                    $("#frm_transaksi input[name=jenis_diskon]").val(jenis);
                    hitungBiaya()

                }else{
                    alert("Kegagalan Sistem");
                }
            }
      
            $(".spinner_validasi_voucher").hide();
              
          }
          
      })
    }else{
      alert("Kegagalan Sistem")
    }

    hitungBiaya();

  })


  $("#frm_transaksi select[name=jenis_pembayaran]").change(function(){
      var id = $('#frm_transaksi select[name=jenis_pembayaran] option').filter(':selected').val()
      
      if(id == ""){
          $('#frm_transaksi input[name=jumlah_dibayar]').val("");
          $('#frm_transaksi input[name=jumlah_dibayar]').prop('readonly', true);
      }else{
          $("#jumlah_dibayar").val($("#total_setelah_diskon").html());
          $('#frm_transaksi input[name=jumlah_dibayar]').prop('readonly', false);
      }

      hitungBiaya();
      
  })
  
  $(".btn_hapus_diskon").click(function(){
      $("#info_diskon").html("");
      $("#harga_diskon").html("Rp 0");
      $("#frm_transaksi input[name=isi_diskon]").val("");
      $("#frm_transaksi input[name=jenis_diskon]").val("");
      
      hitungBiaya();
  })
  
  $(".btn_show_filter").click(function(){
      $("#tab_filter").fadeToggle();
  })
  
  $(".btn_ubah_halaman").change(function(){
      var url = $(this).attr("data-page");
      var hal = $('.btn_ubah_halaman').find('option:selected').val();
      
      window.location.href = url+hal;
  })

  // ==============================================================================

  $(function () {
    
    $('#daterange-btn').daterangepicker(
        {
      minDate : '<?= date("m/d/Y", strtotime($mindate)) ?>'
    },
      function (start, end) {
        var mulai = start.format('YYYY-MM-D');
        var akhir = end.format('YYYY-MM-D');
        $("#tgl_awal").val(mulai);
        $("#tgl_akhir").val(akhir);
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


  $("#btn_submit_transaksi_baru").click(function(e){
    e.preventDefault();
    var jumlah_produk = 0;
    $('#frm_transaksi .list_barang').each(function(index){
      jumlah_produk += parseInt($(this).val());
    });

    var total_transaksi = parseInt($("#total_setelah_diskon").html().replace(/[^0-9]/g, ''))  || 0;
    var dibayar = parseInt($("#jumlah_dibayar").val().replace(/[^0-9]/g, ''))  || 0;

    if (jumlah_produk == 0) {alert('Harap isi jumlah tiket')}

    if (dibayar+1 < total_transaksi) {alert('Jumlah yang dibayar kurang dari total transaksi')}

    if (jumlah_produk > 0 && dibayar+1 > total_transaksi) {
      $(this).html(`<?= spinnerMohonTunggu() ?>`);
      $(this).attr("disabled","");
      $("#frm_transaksi").submit();
    }

  })
  



</script>