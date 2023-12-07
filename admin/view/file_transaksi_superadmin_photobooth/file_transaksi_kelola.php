<?php

	$limit = 10;

    if(@$_GET['hal'] != "" || !empty($_GET['hal'])) {
        $offset = ($_GET['hal'] - 1) * $limit;
        $hal_aktif = $_GET['hal'];
    }else{
        $offset     = 0;
        $hal_aktif = 1;
    }

	if(!empty(@$_GET['pencarian'])){
        $string_where = "AND A.no_nota = '$_GET[pencarian]' ";
    }else{
      $string_where = "";
    }

  	$list_transaksi = $db->query("
  		SELECT * FROM photoboothambil_transaksi A 
        JOIN admin B ON A.kd_admin=B.id_admin 
        JOIN shift C ON A.kd_shift=C.id_shift
        $string_where
        ORDER BY A.tanggal_photoboothambil_transaksi DESC
    	LIMIT $offset, $limit
    ")->fetch_all(MYSQLI_ASSOC);

	$count_data = $db->query("
          SELECT COUNT(*) as jml
         	FROM photoboothambil_transaksi A 
        	JOIN admin B ON A.kd_admin=B.id_admin 
        	JOIN shift C ON A.kd_shift=C.id_shift
          	$string_where
      ")->fetch_assoc()['jml'];

    $jumlah_hal = ceil($count_data/$limit);

	//print_r("<pre>"); print_r($jumlah_hal); die();

?>
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Transaksi Penjualan File Spot Foto</h1>
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
            <h3 class="card-title">Daftar File Transaksi</h3>
          </div>
          <div class="card-body">
            <form action="" method="GET" class=" pb-0">
                <div class="row pb-0">
                    <div class="col-lg-6 col-md-6 col-12 mb-2">
						<input type="hidden" name="page" value="<?=@$_GET['page']?>">
                      	<input type="hidden" name="action" value="<?=@$_GET['action']?>">
                    </div>
                    <div class="col-lg-4 col-md-8 col-12 mb-2">
                        <input type="text" name="pencarian" id="pencarian" value="<?=@$_GET['pencarian']?>" class="form-control" placeholder="Nomor Nota">
                    </div>
                    <div class="col-lg-2 col-md-4 col-12">
                        <button type="submit" class="btn btn-primary btn-block">Filter</button>
                    </div>
                </div>
            </form>
            <table class="table table-bordered table-sm text-sm">
              <thead>
                <tr>
                  <th>No</th>
                  <th>No.Nota</th>
                  <th>Tanggal</th>
                  <th>Kasir Photobooth</th>
                  <th>Shift</th>
                  <th>Customer</th>
                  <th>Tiket</th>
                  <th>Status</th>
                  <th data-searchable="false" data-orderable="false">Detail</th>
                </tr>
              </thead>
              <tbody>
                <?php if(!empty($list_transaksi)) : ?>
                    <?php $nomor = (((int)$hal_aktif-1)*(int)$limit)+1; ?>
                    <?php foreach ($list_transaksi as $key => $value): ?>
                      <tr>
                        <td><?= number_format($nomor) ?></td>
                        <td><?= $value['no_nota'] ?></td>
                        <td><?= date("d-m-y / H:i", strtotime($value['tanggal_photoboothambil_transaksi'])) ?> WIB</td>
                        <td><?= $value['nama_admin'] ?></td>
                        <td><?= $value['nama_shift'] ?></td>
                        <td><?= $value['nama_cust'] ?></td>
                        <td><?= $value['jumlah_tiket'] ?></td>
                        <td>
                            <?php if($value['status_transaksi'] == "1"): ?>
                                Sukses
                            <?php elseif($value['status_transaksi'] == "2"): ?>
                                Dibatalkan
                            <?php elseif($value['status_transaksi'] == "3"): ?>
                                Dibatalkan
                            <?php else : ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                          <button class ="btn btn-success btn-sm btn_detail_transaksi" title="Detail" data-id="<?= enkripsiDekripsi($value['id_photoboothambil_transaksi'],'enkripsi') ?>" data-toggle="modal" data-target="#modal_detail" style="margin: 0px; padding: 0px 4px 0px 4px"><i class="fas fa-file-invoice"></i></button>
                        </td>
                      </tr>  
                    <?php $nomor++; endforeach ?>
                <?php else: ?>
                	<tr>
                		<td colspan="9" class="text-center">Data tidak ditemukan</td>
                    </tr> 
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="row mt-3 mb-3">
            	<div class="col-12">
                  <nav aria-label="Page navigation example">
                      <ul class="pagination " style="float: right !important;">
                          <?= pagination($jumlah_hal, $hal_aktif, 'http://'.$_SERVER['HTTP_HOST'].'/hehanew/admin/admin-super.php?page=file_transaksi_photobooth&action=kelola&pencarian='.@$_GET['pencarian']); ?>
                      </ul>
                  </nav>
              </div>
            </div>
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
  $(document).ready(function(){
    $('.openPopup').on('click',function(){
      var dataURL = $(this).attr('data-href');
      $('.modal-content').load(dataURL,function(){
        $('#myModal').modal({show:true});
      });
    }); 
  });

  $(".btn_detail_transaksi").click(function(){
        var id = $(this).attr("data-id");
        $("#modal_detail .modal-body").html(`<?= spinnerMemuat() ?>`);
        $.ajax({
            url: 'view/file_transaksi_superadmin_photobooth/file_transaksi_photobooth_detail.php?id='+id,
            success:function(result){
                $("#modal_detail .modal-body").html(result);
            }
        })
    })


</script>