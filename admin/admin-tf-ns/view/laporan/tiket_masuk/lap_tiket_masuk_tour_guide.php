<?php

  // CEK FILTER
    if (@$_GET['tgl_awal'] != "" AND @$_GET['tgl_akhir'] != "") {
        $query_tgl = " AND DATE(tanggal_transaksi) BETWEEN '$_GET[tgl_awal]' AND '$_GET[tgl_akhir]'";
    }else{
        $query_tgl = "";
    }

    if (@$_GET['pembeli'] != "") {
        $query_pembeli = " AND nama_cust LIKE '%".$_GET['pembeli']."%' ";
    }else{
        $query_pembeli = "";
    }

    if (@$_GET['jenis_pembayaran'] != "") {
        $query_jenis_pembayaran = " AND kd_jenis_pembayaran = '".enkripsiDekripsi($_GET['jenis_pembayaran'], 'dekripsi')."' ";
    }else{
        $query_jenis_pembayaran = "";
    }

    if (@$_GET['kasir'] != "") {
        $query_kasir = " AND kd_admin = '".enkripsiDekripsi($_GET['kasir'], 'dekripsi')."' ";
    }else{
        $query_kasir = "";
    }

    if (@$_GET['agen_tour'] != "") {
        $query_agen = " AND A.id_agen = '".enkripsiDekripsi($_GET['agen_tour'], 'dekripsi')."' ";
    }else{
        $query_agen = "";
    }
  // END CEK FILTER
  
  $mindate = date('Y-m-d', strtotime('-1 months'));

  $list_transaksi_string = "
    FROM transaksi A 
    JOIN admin B ON A.kd_admin=B.id_admin 
    JOIN shift C ON A.kd_shift=C.id_shift 
    JOIN jenis_pembayaran D ON A.kd_jenis_pembayaran=D.id_jenis_pembayaran 
    WHERE status_transaksi!='3' AND A.id_agen != '' 
    $query_tgl $query_pembeli $query_kasir $query_agen $query_jenis_pembayaran";


  

  // PAGINATION
  $perpage = 10;
  $count_transaksi = "SELECT COUNT(*) AS jml $list_transaksi_string";

  $halamanaktif = ( isset($_GET['p'])) ? $_GET['p'] : 1;
  $awal = ( $halamanaktif - 1 ) * $perpage;

  // END PAGINATION


  // $total_transaksi = $db->query("SELECT COUNT(*) AS jml FROM transaksi WHERE status_transaksi!='3'")->fetch_assoc()['jml'];

  

    $list_transaksi = "
        SELECT *
        $list_transaksi_string
        ORDER BY A.id_transaksi DESC LIMIT $awal, $perpage
    ";

    $response_newserver = @CRUD_API(base_url_newserve()."api/lap_tiket_masuk/tiket_masuk_user_guide" ,json_encode([
        'act'               => "list_head",
        'count_record'     => $count_transaksi,
        'record'           => $list_transaksi,
    ]));
    // print_r("<pre>"); print_r($response_newserver); die();

    $jmlhalaman = ceil($response_newserver['count_record'] / $perpage);
    


  
    $jenis_tiket = $db->query("SELECT * FROM jenis_tiket WHERE status_display_tiket='Y' AND status_remove_tiket='N'")->fetch_all(MYSQLI_ASSOC);

    $list_pembayaran = $db->query("SELECT * FROM jenis_pembayaran WHERE status_aktif='Y' ");
    $list_kasir = $db->query("SELECT * FROM admin WHERE jabatan_admin='2' AND status_rmv_admin='N' ");
    $agen_tour = $db->query("SELECT * FROM agen WHERE status_hapus_agen='N' ")->fetch_all(MYSQLI_ASSOC);
    
  // ===========================================

  
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
              
              <button class="btn btn-sm btn-info btn_show_filter" data-show='hide'><i class="fas fa-filter"></i> | Filter</button>
            </div>
          </div>
          <div class="card-body">
            <div id="tab_filter" style="display: none;">
              <form action="?page=lap_tour_guide&action=kelola" method="GET" enctype="multipart/form-data">
                <div class="row mb-3">
                  <div class="col-md-6 pb-3">
                    <input type="text" name="" class="form-control form-control-sm" id="daterange-btn" readonly>
                    <input type="hidden" name="page" value="<?= @$_GET['page'] ?>">
                    <input type="hidden" name="action" value="<?= @$_GET['action'] ?>">
                    <input type="text" name="tgl_awal" id="tgl_awal" value="<?= date("Y-m-d") ?>" hidden="">
                    <input type="text" name="tgl_akhir" id="tgl_akhir" value="<?= date("Y-m-d") ?>" hidden="">
                  </div>
                  <div class="col-md-6 pb-3">
                    <select class="form-control form-control-sm" name="agen">
                        <option value="" selected hidden>AGEN</option>
                        <option value="">SEMUA AGEN</option>
                        <?php foreach ($agen_tour as $key => $value): ?>
                            <?php
                                $id_agen = enkripsiDekripsi($value['id_agen'],'enkripsi');
                            ?>
                            <option value="<?= $id_agen ?>"><?= strtoupper($value['nama_agen']) ?></option>
                        <?php endforeach ?>
                    </select>
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
                  <th>Tour Guide</th>
                  <th>Tiket</th>
                  <th>Total</th>
                  <th>Pembayaran</th>
                  <th data-searchable="false" data-orderable="false" style="text-align: center;">Detail</th>
                </tr>
              </thead>
              <tbody>
                <?php $count_transaksi = ($response_newserver['count_record'] > 10 )? $response_newserver['count_record']-1: $response_newserver['count_record'];  ?>
                <?php $awal += 1 ?>
                <?php $nomor = $awal ?>
                <?php foreach ($response_newserver['record'] as $key => $value): ?>
                  <tr>
                    <th><?= $nomor ?></th>
                    <td><?= date("d-m-y / H:i", strtotime($value['tanggal_transaksi'])) ?> WIB</td>
                    <td><?= $value['nama_admin'] ?></td>
                    <td><?= $value['nama_cust'] ?></td>
                    <td><?= $value['jumlah_tiket'] ?></td>
                    <td>Rp <?= number_format($value['total_transaksi']) ?></td>
                    <td><?= $value['nama_jenis_pembayaran'] ?></td>
                    <td align="center">
                      <a href="?page=lap_tiket_masuk_tour_guide&action=detail&id=<?= enkripsiDekripsi($value['id_transaksi'],'enkripsi') ?>" data-toggle="tooltip" title="Detail">
                        <button class ="btn btn-success btn-sm" style="margin: 0px; padding: 0px 4px 0px 4px"><i class="fas fa-file-invoice"></i></button>
                      </a>
                    </td>
                  </tr>  
                <?php $nomor++; endforeach ?>
              </tbody>
            </table>
            <hr>
              <span class="float-left">Menampilkan <?= $awal ?> - <?= $nomor-1 ?> dari <?= $count_transaksi ?> transaksi</span>


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
  
  

  $(function () {
    $('#daterange-btn').daterangepicker(
        {
          //minDate : '<?= date("m/d/Y", strtotime($mindate)) ?>'
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

  
  
  
  
  

  $(".btn_show_filter").click(function(){
      $("#tab_filter").toggle(200);
  })

  $("#btn_ganti_tiket").click(function(){
      $("#tab_daftar_tiket").toggle(200);
  })



 
  


</script>