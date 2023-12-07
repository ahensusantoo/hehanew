<?php
if(!empty($_GET['filter1'])){
  $filter1 = $_GET['filter1'];
  $id_merchant = enkripsiDekripsi($_GET['filter1'],'dekripsi');
} else {
  $filter1 = '';
  $id_merchant = '';
}

if(!empty($_GET['menu'])){
  $menu = $_GET['menu'];
} else {
  $menu = '1';
}

$query = "SELECT id_merchant, nama_merchant FROM merchant 
WHERE status_aktif_merchant='Y'";
$list_merchant = $db->query($query)->fetch_all(MYSQLI_ASSOC);
?>

<link rel="stylesheet" href="plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Kelola Per Merchant</h1>
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
            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Merchant :</label>
              <div class="col-sm-10">
                <select name="filter1" class="select2bs4 form-control" style="width: 100%" id="filter1" onchange="terapkan(<?= $menu ?>)" required>
                  <option disabled selected>PILIH MERCHANT</option>
                  <?php foreach ($list_merchant as $bulan => $value): ?>
                    <?php $id_mrc_encryp = enkripsiDekripsi($value['id_merchant'],'enkripsi'); ?>
                    <option value="<?= $id_mrc_encryp ?>" <?php if($filter1 == $id_mrc_encryp){echo 'selected';} ?>> <?= strtoupper($value['nama_merchant']) ?></option>
                  <?php endforeach ?>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">

        <?php if (!isset($_GET['filter1'])): ?>
          <p class="text-center mt-3">Harap Pilih Stall Merchant Terlebih Dahulu</p>
          <?php goto tanpa_stall; ?>
        <?php endif ?>

        <div class="card">
          <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
              <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link <?php if($menu == 1){echo 'active';} ?>" href="#" id="1" onclick="terapkan('1')">Kategori Produk</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link <?php if($menu == 2){echo 'active';} ?>" href="#" id="2" onclick="terapkan('2')">Produk</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link <?php if($menu == 5){echo 'active';} ?>" href="#" id="5" onclick="terapkan('5')">Transaksi</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link <?php if($menu == 6){echo 'active';} ?>" href="#" id="6" onclick="terapkan('6')">Akun</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link <?php if($menu == 7){echo 'active';} ?>" href="#" id="7" onclick="terapkan('7')">Mutasi</a>
                </li>
              </ul>
            </div>
          </div>

          <?php
          if($menu == 1){
            ?>
            <div class="card-header">
              <a href="?page=merchant&action=permerchant&action2=kategoritambah&merchant=<?= $_GET['filter1'] ?>">
                <button type="button" class="btn btn-default" style="width: 100%"><i class="fa fa-fw fa-plus"></i>Tambah Data</button>
              </a>
            </div>
            <div class="card-body">

              <table id="example" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Kode Kategori</th>
                    <th>Nama Kategori</th>
                    <th>Status Aktif</th>
                    <th data-searchable="false" data-orderable="false">Kelola</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $nomor=1;
                  $grand_total = 0;
                  $sql = mysqli_query($db,"SELECT * 
                    FROM merchant_kategori_produk 
                    WHERE status_remove_kategori='N' 
                    AND kd_merchant = '$id_merchant'
                    ORDER BY id_merchant_kategori_produk DESC");
                  while($query = mysqli_fetch_array($sql)) {
                    $eid = enkripsiDekripsi($query['id_merchant_kategori_produk'],'enkripsi');
                    ?>
                    <tr>
                      <td> <?= $nomor++; ?> </td>
                      <td> <?= $query['kode_kategori']; ?> </td>
                      <td> <?= $query['nama_kategori']; ?> </td>
                      <td>
                        <?php if ($query['status_aktif_kategori'] == 'Y'): ?>
                          Aktif
                        <?php else: ?>
                          Nonaktif
                        <?php endif ?>
                      </td>
                      <td align="center"> 
                        <a href="?page=merchant&action=permerchant&action2=kategoriedit&eid=<?= $eid ?>&merchant=<?= $_GET['filter1'] ?>" data-toggle="tooltip" title="Edit">
                          <button class ="btn btn-info btn-xm" style="margin: 3px"><i class="fa fa-fw fa-edit"></i></button>
                        </a>
                        <a onclick="return confirm('Yakin data ingin dihapus ?')" href="view/merchant/proses_data.php?hapus_merchant_kategori_produk=on&eid=<?= $eid ?>" data-toggle="tooltip" title="Hapus">
                          <button class ="btn btn-danger btn-xm" style="margin: 3px"><i class="fa fa-fw fa-trash"></i></button>
                        </a>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
            <?php
          } elseif($menu == 2){
            ?>
            <div class="card-header">
              <a href="?page=merchant&action=permerchant&action2=produktambah&merchant=<?= $_GET['filter1'] ?>">
                <button type="button" class="btn btn-default" style="width: 100%"><i class="fa fa-fw fa-plus"></i>Tambah Data</button>
              </a>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="float-left">
                   <a href="?page=merchant&action=permerchant&action2=ProdukTambahByExcel&merchant=<?= $_GET['filter1'] ?>">
                        <button type="button" class="btn btn-success mb-4">
                            <i class="fa fa-plus"> </i> Import By Excel
                        </button>
                    </a>
                </div>
              </div>

              <table id="example" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama Product</th>
                    <th>Merchant Kategory</th>
                    <th>Status Tampil</th>
                    <th data-searchable="false" data-orderable="false">Kelola</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $nomor=1;
                  $grand_total = 0;
                  $sql = mysqli_query($db,"SELECT * FROM merchant_produk A JOIN merchant_kategori_produk B ON A.kd_merchant_kategori=B.id_merchant_kategori_produk WHERE A.kd_merchant='$id_merchant' AND A.status_remove_produk='N' ORDER BY A.id_merchant_produk DESC");
                  while($query = mysqli_fetch_array($sql)) {
                    $eid = enkripsiDekripsi($query['id_merchant_produk'],'enkripsi');
                    ?>
                    <tr>
                      <td> <?= $nomor++; ?> </td>
                      <td> <?= $query['nama_produk']; ?> </td>
                      <td> <?= $query['nama_kategori']; ?> </td>
                      <td>
                        <?php  
                        if($query['status_display_produk'] == "Y") {
                          echo "Tampil";     
                        }else{
                          echo "Disembunyikan";
                        }
                        ?>
                      </td>
                      <td align="center"> 
                        <a href="?page=merchant&action=permerchant&action2=produkedit&eid=<?= $eid ?>&merchant=<?= $_GET['filter1'] ?>" data-toggle="tooltip" title="Edit">
                          <button class ="btn btn-info btn-xm" style="margin: 3px"><i class="fa fa-fw fa-edit"></i></button>
                        </a>
                        <a onclick="return confirm('Yakin data ingin dihapus ?')" href="view/merchant/proses_data.php?hapus_produk=on&eid=<?= $eid ?>&merchant=<?= $_GET['filter1'] ?>" data-toggle="tooltip" title="Hapus">
                          <button class ="btn btn-danger btn-xm" style="margin: 3px"><i class="fa fa-fw fa-trash"></i></button>
                        </a>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
            <?php
          } elseif($menu == 3){
            ?>
            
          <?php
        } elseif($menu == 4){
          ?>
     
          <?php
        } elseif($menu == 5){
          ?>
          <?php
            	//die($_SERVER['HTTP_HOST']);
            	$limit = 10;

                if(@$_GET['hal'] != "" || !empty($_GET['hal'])) {
                    $offset = ($_GET['hal'] - 1) * $limit;
                    $hal_aktif = $_GET['hal'];
                }else{
                    $offset     = 0;
                    $hal_aktif = 1;
                }
            
            	if(!empty(@$_GET['pencarian'])){
                    $string_where = "AND mt.no_nota = '$_GET[pencarian]' ";
                }else{
                  $string_where = "";
                }
            
          		$record = $db->query("SELECT * 
                  FROM merchant_transaksi mt
                  LEFT JOIN merchant_employee me ON mt.kd_merchant_employee = me.id_merchant_employee
                  LEFT JOIN jenis_pembayaran jp ON mt.kd_jenis_pembayaran = jp.id_jenis_pembayaran
                  WHERE mt.status_transaksi !='1'
                  AND mt.kd_merchant = '$id_merchant'
                  $string_where
                  ORDER BY id_merchant_transaksi DESC
                  LIMIT $offset, $limit
                ")->fetch_all(MYSQLI_ASSOC);
            
            	$count_data = $db->query("
                  SELECT COUNT(*) as jml
                  FROM merchant_transaksi mt
                  LEFT JOIN merchant_employee me ON mt.kd_merchant_employee = me.id_merchant_employee
                  LEFT JOIN jenis_pembayaran jp ON mt.kd_jenis_pembayaran = jp.id_jenis_pembayaran
                  WHERE mt.status_transaksi !='1'
                  AND mt.kd_merchant = '$id_merchant'
                  $string_where
              ")->fetch_assoc()['jml'];
            
            $jumlah_hal = ceil($count_data/$limit);
            
          ?>
          
          <div class="card-body">
            <div class="row mt-3 mb-3">
            	<div class="col-12">
              		<form action="" method="GET" class=" pb-0">
                        <input type="hidden" name="page" value="merchant">
                      	<input type="hidden" name="action" value="permerchant">
                      	<input type="hidden" name="filter1" value="<?=@$_GET['filter1']?>">
                      	<input type="hidden" name="menu" value="<?=@$_GET['menu']?>">
                        <div class="row pb-0">
                            <div class="col-lg-6 col-md-6 col-12 mb-2">
                                
                            </div>
                            <div class="col-lg-4 col-md-8 col-12 mb-2">
                                <input type="text" name="pencarian" id="pencarian" value="<?=@$_GET['pencarian']?>" class="form-control" placeholder="Nomor Nota">
                            </div>
                            <div class="col-lg-2 col-md-4 col-12">
                                <button type="submit" class="btn btn-primary btn-block">Filter</button>
                            </div>
                        </div>
                    </form>
              	</div>
            </div>
            <table class="table table-bordered table-striped ">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Tanggal Transaksi</th>
                  <th>No Nota</th>
                  <th>Nama Kasir</th>
                  <!-- <th>Jenis Pembayaran</th> -->
                  <th>Total</th>
                  <th>Detail</th>
                  <th data-searchable="false" data-orderable="false" data-priority="1">Kelola</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $nomor=(((int)$hal_aktif-1)*(int)$limit)+1;
                $grand_total = 0;
                foreach($record as $key =>$value) {
                  ?>
                	<?php $eid = enkripsiDekripsi($value['id_merchant_transaksi'],'enkripsi'); ?>
                  <tr>
                    <td> <?= $nomor++; ?> </td>
                    <td> <?= date_format(date_create($value['tgl_input_transaksi']), 'd M y, H:i A'); ?></td>
                    <td> <?= $value['no_nota']; ?> </td>
                    <td> <?= $value['nama_employee']; ?> </td>
                    <!-- <td> <?php //echo $value['nama_jenis_pembayaran']; ?> </td> -->
                    <td> <?= number_format($value['tagihan_nota']); ?> </td>
                    <td> 
                      <?php 
                      if($value['status_transaksi'] == "2"){
                        echo "Sukses";
                      }else if ($value['status_transaksi'] == "3"){
                        echo "Dibatalkan";
                      }
                      ?> 
                    </td>
                    <td align="center"> 
                      <a href="?page=merchant&action=permerchant&action2=detailtransaksi&eid=<?= $eid ?>&merchant=<?= $_GET['filter1'] ?>" class="badge badge-success">
                        Detail
                      </a>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
            <div class="row mt-3 mb-3">
            	<div class="col-12">
                  <nav aria-label="Page navigation example">
                      <ul class="pagination " style="float: right !important;">
                          <?= pagination($jumlah_hal, $hal_aktif, 'http://'.$_SERVER['HTTP_HOST'].'/hehanew/admin/general-cashier-stall.php?page=merchant&action=permerchant&filter1='.@$_GET['filter1'].'&menu='.@$_GET['menu'].'&pencarian='.@$_GET['pencarian']); ?>
                      </ul>
                  </nav>
              </div>
            </div>
          </div>
          <?php
        } elseif($menu == 6){
          ?>
          <div class="card-header">
            <a href="?page=merchant&action=permerchant&action2=akuntambah&merchant=<?= $_GET['filter1'] ?>">
              <button type="button" class="btn btn-default" style="width: 100%"><i class="fa fa-fw fa-plus"></i>Tambah Data</button>
            </a>
          </div>
          <div class="card-body">
            <table id="example" class="table table-bordered table-striped table-sm tet-sm">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama</th>
                  <th>Username</th>
                  <th>Level</th>
                  <th>Nomor</th>
                  <th>Email</th>
                  <th>Status</th>
                  <th data-searchable="false" data-orderable="false">Kelola</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $nomor=1;
                $grand_total = 0;
                $sql = mysqli_query($db,"SELECT * 
                  FROM merchant_employee 
                  WHERE status_remove_employee='N'
                  AND level_employee!='0'
                  AND kd_merchant = '$id_merchant'
                  AND status_remove_employee='N'
                  ORDER BY id_merchant_employee DESC");
                while($query = mysqli_fetch_array($sql)) {
                  $eid = enkripsiDekripsi($query['id_merchant_employee'],'enkripsi');
                  ?>
                  <tr>
                    <td> <?= $nomor++; ?> </td>
                    <td> <?= $query['nama_employee']; ?> </td>
                    <td> <?= $query['username_employee']; ?> </td>
                    <td> 
                      <?php 
                      if($query['level_employee'] == "0"){
                        echo "Super Admin Merchant";
                      }else if($query['level_employee'] == "1"){
                        echo "Admin Merchant";
                      }else{
                        echo "Kasir";
                      }
                      ?> 
                    </td>
                    <td> <?= $query['telp_employee']; ?> </td>
                    <td> <?= $query['email_employee']; ?> </td>
                    <td> 
                      <?php 
                      if($query['status_aktif_employee'] == "Y"){
                        echo "AKTIF";
                      }else{
                        echo "NON AKTIF";
                      }
                      ?> 
                    </td>
                    <td align="center"> 
                      <a href="?page=merchant&action=permerchant&action2=akunedit&eid=<?= $eid ?>&merchant=<?= $_GET['filter1'] ?>" data-toggle="tooltip" title="Edit">
                        <button class ="btn btn-info btn-xm" style="margin: 3px"><i class="fa fa-fw fa-edit"></i></button>
                      </a>
                      <a onclick="return confirm('Yakin data ingin dihapus ?')" href="view/merchant/proses_data.php?hapus_merchant_empoyee_gc=on&eid=<?= $eid ?>&merchant=<?= $_GET['filter1'] ?>" data-toggle="tooltip" title="Hapus">
                        <button class ="btn btn-danger btn-xm" style="margin: 3px"><i class="fa fa-fw fa-trash"></i></button>
                      </a>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
          <?php
        } elseif($menu == 7){
          ?>

          <?php
              //die($_SERVER['HTTP_HOST']);
              $limit = 10;

                if(@$_GET['hal'] != "" || !empty($_GET['hal'])) {
                    $offset = ($_GET['hal'] - 1) * $limit;
                    $hal_aktif = $_GET['hal'];
                }else{
                    $offset     = 0;
                    $hal_aktif = 1;
                }
            
              if(!empty(@$_GET['pencarian'])){
                    $string_where = "AND mt.no_nota = '$_GET[pencarian]' ";
                }else{
                  $string_where = "";
                }
            
              $record = $db->query("
                SELECT A.id_merchant_mutasi_stok, A.tanggal_mutasi, A.jenis_mutasi, A.kd_merchant, B.nama_employee 
                  FROM merchant_mutasi_stok A JOIN merchant_employee B ON A.kd_merchant_employee=B.id_merchant_employee
                  WHERE A.kd_merchant='".enkripsiDekripsi($_GET['filter1'],'dekripsi')."' 
                  AND A.status_rmv_mutasi='N' $string_where
                  ORDER BY A.id_merchant_mutasi_stok DESC
                  LIMIT $offset, $limit
                ")->fetch_all(MYSQLI_ASSOC);
            
              $count_data = $db->query("
                  SELECT COUNT(*) as jml 
                  FROM merchant_mutasi_stok A JOIN merchant_employee B ON A.kd_merchant_employee=B.id_merchant_employee
                  WHERE A.kd_merchant='".enkripsiDekripsi($_GET['filter1'],'dekripsi')."' 
                  AND A.status_rmv_mutasi='N' $string_where
              ")->fetch_assoc()['jml'];
            
            $jumlah_hal = ceil($count_data/$limit);
            
          ?>


          <div class="card-header">
            <a href="?page=merchant&action=permerchant&action2=mutasimasuk&merchant=<?= $_GET['filter1'] ?>">
              <button type="button" class="btn btn-default mb-2" style="width: 100%"><i class="fa fa-fw fa-plus"></i>Mutasi Masuk</button>
            </a>
            <a href="?page=merchant&action=permerchant&action2=mutasikeluar&merchant=<?= $_GET['filter1'] ?>">
              <button type="button" class="btn btn-default" style="width: 100%"><i class="fa fa-fw fa-minus"></i>Mutasi Keluar</button>
            </a>
          </div>
          <div class="card-body">
            <div class="row mt-3 mb-3">
              <div class="col-12">
                  <form action="" method="GET" class=" pb-0">
                        <input type="hidden" name="page" value="merchant">
                        <input type="hidden" name="action" value="permerchant">
                        <input type="hidden" name="filter1" value="<?=@$_GET['filter1']?>">
                        <input type="hidden" name="menu" value="<?=@$_GET['menu']?>">
                        <div class="row pb-0">
                            <div class="col-lg-6 col-md-6 col-12 mb-2">
                                
                            </div>
                            <div class="col-lg-4 col-md-8 col-12 mb-2">
                                <input type="text" name="pencarian" id="pencarian" value="<?=@$_GET['pencarian']?>" class="form-control" placeholder="Nomor Mutasi">
                            </div>
                            <div class="col-lg-2 col-md-4 col-12">
                                <button type="submit" class="btn btn-primary btn-block">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <table class="table table-bordered table-striped text-sm">
              <thead>
                <tr>
                  <th>No</th>
                  <th>No. Mutasi</th>
                  <th>Pembuat</th>
                  <th>Jenis Mutasi</th>
                  <th>Tanggal</th>
                  <th data-searchable="false" data-orderable="false">Detail</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $nomor=(((int)$hal_aktif-1)*(int)$limit)+1;
                $grand_total = 0;
                foreach($record as $key =>$value) {
                  ?>
                  <?php $eid = enkripsiDekripsi($value['id_merchant_mutasi_stok'],'enkripsi'); ?>
                  <tr>
                    <td> <?= $nomor++; ?> </td>
                    <td> <?= id_ke_struk($query['id_merchant_mutasi_stok']) ?></td>
                    <td> <?= $value['nama_employee']; ?> </td>
                    <td> 
                      <?php 
                      if($value['jenis_mutasi'] == "1"){
                        echo "Masuk";
                      }else if($value['jenis_mutasi'] == "2"){
                        echo "Keluar";
                      }else{
                        echo "-";
                      }
                      ?> 
                    </td>
                    <td> <?= date_format(date_create($value['tanggal_mutasi']), 'd M y, H:i A'); ?> </td>
                    <td align="center"> 
                      <a href="?page=merchant&action=permerchant&action2=detailmutasi&eid=<?= $eid ?>&merchant=<?= $_GET['filter1'] ?>" class="badge badge-success">Detail</a>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
            <div class="row mt-3 mb-3">
              <div class="col-12">
                  <nav aria-label="Page navigation example">
                      <ul class="pagination " style="float: right !important;">
                          <?= pagination($jumlah_hal, $hal_aktif, 'http://'.$_SERVER['HTTP_HOST'].'/hehanew/admin/general-cashier-stall.php?page=merchant&action=permerchant&filter1='.@$_GET['filter1'].'&menu='.@$_GET['menu'].'&pencarian='.@$_GET['pencarian']); ?>
                      </ul>
                  </nav>
              </div>
            </div>
          </div>
          <?php
        }
        ?>
      </div>
      <?php tanpa_stall: ?>
    </div>
  </div>
</div>
</section>


<div class="modal fade" id="myModal" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

    </div>
  </div>
</div>

<script src="plugins/select2/js/select2.full.min.js"></script>
<script type="text/javascript">
  $('.select2bs4').select2({
    theme: 'bootstrap4'
  })
</script>

<script type="text/javascript">
  $(document).ready(function(){
    $('.openPopup').on('click',function(){
      var dataURL = $(this).attr('data-href');
      $('.modal-content').load(dataURL,function(){
        $('#myModal').modal({show:true});
      });
    }); 
  });
</script>

<script>
  $(function () {
    $('#example2').DataTable({
      "paging": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>

<script type="text/javascript">
  function terapkan(var_menu){
    var filter1 = document.getElementById('filter1').value;

    // var form = $('<form action="?page=merchant&action=permerchant" method="get">' +
    //   '<input type="hidden" name="filter1" value="' + filter1 + '" />' +
    //   '<input type="hidden" name="menu" value="' + var_menu + '" />' +
    //   '</form>');
    // $('body').append(form);
    // form.submit();

    // alert(var_menu)

    window.location.href="?page=merchant&action=permerchant&filter1=" + filter1 + "&menu=" + var_menu;
  }
</script>

<script type="text/javascript">

  $(document).on('click', '#pilih_produk', function(){
    $('#input_nama_product').val($(this).data('nama_produk'))
    $('#input_id_product').val($(this).data('id_produk'))
    $('#input_harga_beli_produk').val(parseInt($(this).data('harga_beli_produk')).toLocaleString())
    $('#input_harga_jual_produk').val(parseInt($(this).data('harga_jual_produk')).toLocaleString())

    $('#modal-product').modal('hide')
      });//end tangkap filter kasir

  function format_nominal(arg){
    var bayar = $(arg).val().replace(/[^0-9]/g, '');
    if (bayar == 0) {bayar = 0}
      $(arg).val(parseInt(bayar).toLocaleString());
  }


</script>