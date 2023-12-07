<?php

  // CEK FILTER
  if (isset($_GET['tgl_awal']) AND isset($_GET['tgl_akhir']) AND isset($_GET['pembeli'])) {

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

  }else{
    $query_tgl = "";
    $query_pembeli = "";
  }
  // END CEK FILTER


  // PAGINATION
  $perpage = 10;
  $total_transaksi = $db->query("SELECT COUNT(*) AS jml FROM transaksi WHERE status_transaksi!='3' $query_tgl $query_pembeli")->fetch_assoc()['jml'];
  $jmlhalaman = ceil($total_transaksi / $perpage);
  $halamanaktif = ( isset($_GET['p'])) ? $_GET['p'] : 1;
  $awal = ( $halamanaktif - 1 ) * $perpage;
  // END PAGINATION

  // $total_transaksi = $db->query("SELECT COUNT(*) AS jml FROM transaksi WHERE status_transaksi!='3'")->fetch_assoc()['jml'];

  $list_transaksi = $db->query("SELECT * FROM transaksi A JOIN admin B ON A.kd_admin=B.id_admin JOIN shift C ON A.kd_shift=C.id_shift WHERE status_transaksi!='3' $query_tgl $query_pembeli ORDER BY A.id_transaksi DESC LIMIT $awal, $perpage")->fetch_all(MYSQLI_ASSOC);


  $query = "SELECT * FROM jenis_tiket WHERE status_display_tiket='Y'";
  $jenis_tiket = $db->query($query)->fetch_all(MYSQLI_ASSOC);


  // CEK TIKET UNTUK HARI INI
  $hari_sekarang = date('N');
  $tanggal_sekarang = date('d');
  $jam_sekarang = date("H:i");
  $tahun_bulan_sekarang = date('Y-m');

  $hari_libur = $db->query("SELECT COUNT(hari_libur) AS jml FROM hari_libur WHERE tahun_bulan='$tahun_bulan_sekarang' AND hari_libur LIKE '%$tanggal_sekarang%'")->fetch_assoc()['jml'];
  if ((int)$hari_libur > 0) { //KETIKA HARI INI HARI LIBUR
    $tiket_sekarang = $db->query("SELECT * FROM jenis_tiket WHERE status_hari_libur='3' AND status_display_tiket='Y' AND start_jam<='$jam_sekarang' AND end_jam>='$jam_sekarang'")->fetch_assoc();
  }else{ //KETIKA HARI INI BUKAN HARI LIBUR
    $tiket_sekarang = $db->query("SELECT * FROM jenis_tiket WHERE start_hari<='$hari_sekarang' AND end_hari>='$hari_sekarang' AND status_display_tiket='Y' AND start_jam<='$jam_sekarang' AND end_jam>='$jam_sekarang'")->fetch_assoc();
  }

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
                  <div class="col-md-5">
                    <input type="text" name="" class="form-control form-control-sm" id="daterange-btn">
                    <input type="hidden" name="page" value="<?= @$_GET['page'] ?>">
                    <input type="hidden" name="action" value="<?= @$_GET['action'] ?>">
                    <input type="date" name="tgl_awal" id="tgl_awal" value="<?= date("Y-m-d") ?>" hidden="">
                    <input type="date" name="tgl_akhir" id="tgl_akhir" value="<?= date("Y-m-d") ?>" hidden="">
                  </div>
                  <div class="col-md-5">
                    <input type="text" name="pembeli" placeholder="Nama Pembeli" class="form-control form-control-sm">
                  </div>
                  <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-block btn-outline-info">Cari</button>
                  </div>
                </div>
              </form>
            </div>
            <?php if (isset($_GET['pembeli'])): ?>
              <div class="mb-2 pl-2" style="border: 1px solid #cecece">
                <a href="?page=transaksi&action=kelola"><button class="badge badge-danger float-right mr-2">Reset Filter</button></a>
                <b>Filter</b>
                <hr style="margin: 0px">
                <table>
                  <tbody>
                    <tr><td>Pembeli</td><td> : </td><td><?= $_GET['pembeli'] ?></td></tr>
                    <tr><td>Tanggal</td><td> : </td><td><?= tanggal_indo($_GET['tgl_awal']) ?> - <?= tanggal_indo($_GET['tgl_akhir']) ?></td></tr>
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
                  <th>Customer</th>
                  <th>Tiket</th>
                  <th>Total</th>
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
                      <a class="page-link" href="?page=transaksi&action=kelola&p=<?= $halamanaktif-1 ?>">Sebelumnya</a>
                    </li>
                  <?php else: ?>
                    <li class="page-item disabled">
                      <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Sebelumnya</a>
                    </li>
                  <?php endif ?>


                  <!-- <li class="page-item"><a class="page-link" href="#">1</a></li> -->
                  <li class="page-item disabled" aria-current="page" disabled>
                    <select class="form-control" style="height: 96%">
                        <option value="<?= $halamanaktif ?>"><?= $halamanaktif ?></option>
                    </select>
                    <!--<a class="page-link" href="#" disabled>$halamanaktif <span class="sr-only">(current)</span></a>-->
                  </li>
                  <!-- <li class="page-item"><a class="page-link" href="#">3</a></li> -->


                  <!-- Halaman Selanjutnya -->
                  <?php if ($halamanaktif < $jmlhalaman): ?>
                    <li class="page-item">
                      <a class="page-link" href="?page=transaksi&action=kelola&p=<?= $halamanaktif+1 ?>">Selanjutnya</a>
                    </li>
                  <?php else: ?>
                    <li class="page-item disabled">
                      <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Selanjutnya</a>
                    </li>
                  <?php endif ?>

                </ul>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- Modal -->


<form method="post" action="view/transaksi/proses_data.php" enctype="multipart/form-data" autocomplete="off">
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
                    echo $tiket_sekarang['nama_jenis_tiket'].' ( Rp '.number_format($tiket_sekarang['harga_tiket'] ); } ?> "
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
                          >PILIH</button>
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
                  <input type="number" min="1" class="form-control" placeholder="Masukkan jumlah tiket yang dibeli" name="jumlah_tiket">
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
                                
                                <div class="float-right spinner_validasi_voucher" style="display:none; margin-top: -30px; margin-right: 10px;"><div class="spinner-border spinner-border-sm" role="status" ></div></div>
                                <small class="text-red notif_voucher_tidak_ada" style="display:none;">*Kode voucher tidak tersedia</small>
                                <small class="text-blue notif_voucher_ada" style="display:none;">*Kode voucher berhasil digunakan</small>
                                
                            </div>
                            <div class="col-md-3">
                                <select class="form-control" name="jenis_diskon">
                                    <option value="">Pilih Jenis</option>
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
                                <?php $list_pembayaran = $db->query("SELECT * FROM jenis_pembayaran WHERE status_aktif='Y' "); ?>
                                <?php while($value = $list_pembayaran->fetch_assoc()) : ?>
                                    <option value="<?= enkripsiDekripsi($value['id_jenis_pembayaran'],'enkripsi') ?>"><?= $value['nama_jenis_pembayaran'] ?></option>
                                <?php endwhile; ?>
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
            </div>
            <div class="modal-footer">
              <button type="submit" name="tambah_transaksi_baru" class="btn btn-success btn-block">PROSES TRANSAKSI</button>
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

  $(".btn_show_filter").click(function(){
      $("#tab_filter").fadeToggle();
  })

  $("#btn_ganti_tiket").click(function(){
      $("#tab_daftar_tiket").fadeToggle();
  })
  
   $(".btn_diskon").click(function(){
      $(".tab_diskon").fadeToggle();
   })

  $(function () {
    $('#daterange-btn').daterangepicker({},
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
  });

  $("input[name=jumlah_tiket]").keyup(function(){
    var harga_satuan = $("input[name=harga_tiket_satuan]").val().replace(/[^0-9]/g, '');
    var jumlah_tiket = $("input[name=jumlah_tiket]").val().replace(/[^0-9]/g, '');;
    var harga_sebelum_diskon = harga_satuan * jumlah_tiket ;

    $("input[name=harga_sebelum_diskon]").val("Rp "+parseInt(harga_sebelum_diskon).toLocaleString());
    $("input[name=harga_setelah_diskon]").val("Rp "+parseInt(harga_sebelum_diskon).toLocaleString());
  })

  $("input[name=bayar]").keyup(function(){
    var harga_sebelum_diskon = $("input[name=harga_sebelum_diskon]").val().replace(/[^0-9]/g, '');
    var harga_setelah_diskon = $("input[name=harga_setelah_diskon]").val().replace(/[^0-9]/g, '');
    var bayar = $("input[name=bayar]").val().replace(/[^0-9]/g, '');
    var total_kembalian = bayar - harga_setelah_diskon;

    if(total_kembalian < 0){
      total_kembalian = 0;      
    }
    $("input[name=kembalian]").val("Rp "+parseInt(total_kembalian).toLocaleString());
  })
  
  
  $("#modal_transaksi_baru select[name=jenis_pembayaran]").change(function(){
      var id = $('#modal_transaksi_baru select[name=jenis_pembayaran] option').filter(':selected').val()
      
      if(id == ""){
          $('#modal_transaksi_baru input[name=bayar]').val("");
          $('#modal_transaksi_baru input[name=bayar]').prop('readonly', true);
      }else{
          $('#modal_transaksi_baru input[name=bayar]').prop('readonly', false);
      }
      
  })
  
  
  $("#modal_transaksi_baru select[name=jenis_diskon]").change(function(){
      
      var id = $('#modal_transaksi_baru select[name=jenis_diskon] option').filter(':selected').val()
      
      if(id == ""){
          $('#modal_transaksi_baru input[name=isi_diskon]').attr("placeholder", "Harap Pilih Jenis Diskon");
          $('#modal_transaksi_baru input[name=isi_diskon]').val("");
          $('#modal_transaksi_baru input[name=isi_diskon]').prop('readonly', true);
      }else{
          
          if(id == "rupiah"){
              $('#modal_transaksi_baru input[name=isi_diskon]').val("");
              $('#modal_transaksi_baru input[name=isi_diskon]').prop('readonly', false);
              $('#modal_transaksi_baru input[name=isi_diskon]').attr("placeholder", "Masukkan Total Diskon Dalam Satuan Rupiah");
          }else if(id == "persen"){
              $('#modal_transaksi_baru input[name=isi_diskon]').val("");
              $('#modal_transaksi_baru input[name=isi_diskon]').prop('readonly', false);
              $('#modal_transaksi_baru input[name=isi_diskon]').attr("placeholder", "Masukkan Total Diskon Dalam Satuan Persen");
          }else if(id == "voucher"){
              $('#modal_transaksi_baru input[name=isi_diskon]').val("");
              $('#modal_transaksi_baru input[name=isi_diskon]').prop('readonly', false);
              $('#modal_transaksi_baru input[name=isi_diskon]').attr("placeholder", "Masukkan Kode Voucher");
          }else{
              alert("Kegagalan Sistem");
          }
      }
      
  })
  
  
  $("input[name=isi_diskon]").keyup(function(){
      
      $(".notif_voucher_tidak_ada").hide();
      $(".notif_voucher_ada").hide();
      
      if(){
          
      }
      
      var id = $('#modal_transaksi_baru select[name=jenis_diskon] option').filter(':selected').val();
      var harga_sebelum_diskon = parseInt($("input[name=harga_sebelum_diskon]").val().replace(/[^0-9]/g, ''));
      var value = parseInt($(this).val().replace(/[^0-9]/g, ''));


      if(id == "rupiah"){
          var total = harga_sebelum_diskon - value;
          if(total < 0){
              total = 0;      
          }
          $("#modal_transaksi_baru input[name=harga_setelah_diskon]").val("Rp "+parseInt(total).toLocaleString());
      }else if(id == "persen"){
          if(value > 100){
              value = 100;
          }
          var total = harga_sebelum_diskon - (harga_sebelum_diskon * value / 100);
          if(total < 0){
              total = 0;      
          }
          $('#modal_transaksi_baru input[name=isi_diskon]').val(value+" %");
          $("#modal_transaksi_baru input[name=harga_setelah_diskon]").val("Rp "+parseInt(total).toLocaleString());
          
      }else if(id == "voucher"){
          $(".spinner_validasi_voucher").show();
          
          var kode = $(this).val();
          
          $.ajax({
              url : 'view/transaksi/proses_data.php?cek_voucher='+kode,
              success:function(result){
                  
                var data = result;
                var obj = JSON.parse(data);
                // alert(obj.kode + ", " + obj.value + ", " + obj.tipe);
                
                if(obj.kode == "N"){
                    $(".notif_voucher_tidak_ada").show();
                }else{
                    
                    
                    
                    var tipe = obj.tipe;
                    var nominal = obj.value;
                    
                    if(tipe == "2"){
                        
                          if(nominal > 100){
                              nominal = 100;
                          }
                          var harga_setelah_diskon = harga_sebelum_diskon - (harga_sebelum_diskon * nominal / 100);
                          
                          if(harga_setelah_diskon < 0){
                              harga_setelah_diskon = 0;      
                          }
                          
                          var info = "*Voucher berhasil digunakan. ( Potongan "+nominal+"% )";
                          $(".notif_voucher_ada").html(info);
                          $(".notif_voucher_ada").show();
                          $("#modal_transaksi_baru input[name=harga_setelah_diskon]").val("Rp "+parseInt(harga_setelah_diskon).toLocaleString());
                          
                    }else if(tipe == "1"){
                        
                    }else{
                        // alert("Kegagalan Sistem");
                    }
                }
				  
                $(".spinner_validasi_voucher").hide();
                  
              }
              
          })
      }else{
          
          alert("Kegagalan Sistem");
      }
      
      
    // var harga_satuan = $("input[name=harga_tiket_satuan]").val().replace(/[^0-9]/g, '');
    // var jumlah_tiket = $("input[name=jumlah_tiket]").val().replace(/[^0-9]/g, '');
    // var harga_sebelum_diskon = harga_satuan * jumlah_tiket ;
    
    // $("input[name=harga_sebelum_diskon]").val("Rp "+parseInt(harga_sebelum_diskon).toLocaleString());
    
  })


</script>