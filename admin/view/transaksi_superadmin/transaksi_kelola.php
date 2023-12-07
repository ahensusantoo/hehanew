<?php
  
  $date_now = date("Y-m-d");
  $date_now_min_2 = date('Y-m-d', strtotime( date('Y-m-d') . " -2 days"));

  $list_transaksi = $db->query("SELECT * FROM transaksi A JOIN admin B ON A.kd_admin=B.id_admin JOIN shift C ON A.kd_shift=C.id_shift WHERE DATE(A.tanggal_transaksi) BETWEEN '$date_now_min_2' AND '$date_now' ORDER BY A.tanggal_transaksi DESC")->fetch_all(MYSQLI_ASSOC);

  // $list_transaksi = $db->query("SELECT * FROM transaksi A JOIN admin B ON A.kd_admin=B.id_admin JOIN shift C ON A.kd_shift=C.id_shift ORDER BY A.tanggal_transaksi DESC")->fetch_all(MYSQLI_ASSOC);

?>
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Transaksi Tiket Masuk</h1>
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
            <table id="example" class="table table-bordered text-sm table-sm">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Tanggal</th>
                  <th>No.Nota</th>
                  <th>Kasir</th>
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
                    <td><?= date("d-m-y / H:i", strtotime($value['tanggal_transaksi'])) ?> WIB</td>
                    <td><?= $value['nama_admin'] ?></td>
                    <td><?= $value['nama_shift'] ?></td>
                    <td><?= $value['nama_cust'] ?></td>
                    <td><?= $value['jumlah_tiket'] ?></td>
                    <td>
                        <?php if($value['status_transaksi'] == "1"): ?>
                            Sukses
                        <?php elseif($value['status_transaksi'] == "2"): ?>
                            Direvisi
                        <?php elseif($value['status_transaksi'] == "3"): ?>
                            Dibatalkan
                        <?php else : ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td>
                      <a href="?page=transaksi&action=detail&id=<?= enkripsiDekripsi($value['id_transaksi'],'enkripsi') ?>" data-toggle="tooltip" title="Detail">
                        <button class ="btn btn-info btn-xm" style="margin: 3px"><i class="fa fa-fw fa-edit"></i></button>
                      </a>
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