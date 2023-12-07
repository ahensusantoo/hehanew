<?php
	$mindate = date('Y-m-d', strtotime('-3 days'));
  $list_transaksi = $db->query("SELECT * FROM photobooth_transaksi A JOIN admin B ON A.kd_admin=B.id_admin JOIN shift C ON A.kd_shift=C.id_shift WHERE DATE(A.tanggal_photobooth_transaksi)>'$mindate' ORDER BY A.tanggal_photobooth_transaksi DESC")->fetch_all(MYSQLI_ASSOC);

?>
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Transaksi Tiket Masuk Spot Foto</h1>
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
            <h3 class="card-title">Daftar Transaksi</h3>
          </div>
          <div class="card-body">
            <table id="example" class="table table-bordered table-sm text-sm">
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
                <?php $nomor = 1; ?>
                <?php foreach ($list_transaksi as $key => $value): ?>
                  <tr>
                    <td><?= number_format($nomor) ?></td>
                    <td><?= $value['no_nota'] ?></td>
                    <td><?= date("d-m-y / H:i", strtotime($value['tanggal_photobooth_transaksi'])) ?> WIB</td>
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
                      <button class ="btn btn-success btn-sm btn_detail_transaksi" title="Detail" data-id="<?= enkripsiDekripsi($value['id_photobooth_transaksi'],'enkripsi') ?>" data-toggle="modal" data-target="#modal_detail" style="margin: 0px; padding: 0px 4px 0px 4px"><i class="fas fa-file-invoice"></i></button>
                    </td>
                  </tr>  
                <?php $nomor++; endforeach ?>
              </tbody>
            </table>
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
            url: 'view/transaksi_superadmin_photobooth/transaksi_photobooth_detail.php?id='+id,
            success:function(result){
                $("#modal_detail .modal-body").html(result);
            }
        })
    })


</script>