<?php

  $id_transaksi = enkripsiDekripsi($_GET['id'],'dekripsi');
  $data_transaksi = $db->query("SELECT * FROM transaksi A JOIN admin B ON A.kd_admin=B.id_admin JOIN shift C ON A.kd_shift=C.id_shift WHERE A.id_transaksi='$id_transaksi'")->fetch_assoc();
  $detail_transaksi = $db->query("SELECT * FROM tiket A JOIN jenis_tiket B ON A.kd_jenis_tiket=B.id_jenis_tiket WHERE kd_transaksi='$id_transaksi'")->fetch_all(MYSQLI_ASSOC);

?>
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Revisi Transaksi</h1>
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
            <h3 class="card-title">Data Transaksi</h3>
            <button class="btn btn-sm btn-danger float-right">Batalkan Transaksi</button>
          </div>
          <div class="card-body">
            <form action="view/transaksi_superadmin/proses_data.php" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="id_transaksi" value="<?= $_GET['id'] ?>">
              <div class="row">
                <div class="col-md-6">
                  <table class="table table-sm">
                    <tbody>
                      <tr>
                        <td>Tanggal</td>
                        <td> : </td>
                        <td><?= tanggal_jam_indo($data_transaksi['tanggal_transaksi']) ?></td>
                      </tr>
                      <tr>
                        <td>Nama Pembeli</td>
                        <td> : </td>
                        <td><input name="nama_cust" value="<?= $data_transaksi['nama_cust'] ?>" style="height:100%; width:100%;"></td>
                      </tr>
                      <tr>
                        <td>Telepon Pembeli</td>
                        <td> : </td>
                        <td><input name="telp_cust" value="<?= $data_transaksi['telp_cust'] ?>" style="height:100%; width:100%;"></td>
                      </tr>
                      <tr>
                        <td>Total Transaksi</td>
                        <td> : </td>
                        <td>
                          <input id="total_transaksi" value="Rp <?= number_format($data_transaksi['total_transaksi']) ?>" style="height:100%; width:100%;" readonly>
                          <input id="total_transaksi_hide" value="<?= $data_transaksi['total_transaksi'] ?>" hidden>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="col-md-6">
                  <table class="table table-sm">
                    <tbody>
                      <tr>
                        <td>Admin</td>
                        <td> : </td>
                        <td><?= $data_transaksi['nama_admin'] ?></td>
                      </tr>
                      <tr>
                        <td>Pembeli</td>
                        <td> : </td>
                        <td><?= $data_transaksi['nama_shift'] ?></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              
                <div class="col-md-12">
                  <hr>
                  <table class="table table-sm">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Jenis Tiket</th>
                        <th>Kode</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th style="text-align:end">Hapus</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $nomor = 1; ?>
                      <?php foreach ($detail_transaksi as $key => $value): ?>
                        <tr>
                          <td><?= number_format($nomor) ?></td>
                          <td><?= $value['nama_jenis_tiket'] ?></td>
                          <td><?= $value['kode_tiket'] ?></td>
                          <td><?= number_format($value['harga_satuan']) ?></td>
                          <td>
                            <?php if ($value['status_tiket'] == "0"): ?>
                              Belum Dipakai
                            <?php elseif ($value['status_tiket'] == "1"): ?>
                              Dipakai
                            <?php elseif ($value['status_tiket'] == "2"): ?>
                              Dibatalkan
                            <?php endif ?>
                          </td>
                          <td align="end">
                            <?php if ($value['status_tiket'] == "0"): ?>
                              <input style="transform: scale(1.5);" data-harga="<?= $value['harga_satuan'] ?>" class="form-check-input tiket_dihapus" type="checkbox" name="tiket_dihapus[]" value="<?= enkripsiDekripsi($value['id_tiket'],'enkripsi') ?>">
                            <?php endif ?>
                          </td>
                        </tr>
                      <?php $nomor++; endforeach ?>
                    </tbody>
                  </table>
                  <hr>
                  <button type="submit" name="revisi_transaksi_satuan" value="" class="btn btn-block btn-info">SIMPAN REVISI</button>
                </div>
              </div>
            </form>
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
  
  $(".tiket_dihapus").click(function(){
        var dikurangi = 0;
        $('.tiket_dihapus:checked').each(function () {
            var harga = parseInt($(this).attr("data-harga").replace(/[^0-9]/g, ''));
            dikurangi = dikurangi+harga;
        });
        
        var total = parseInt($("#total_transaksi_hide").val().replace(/[^0-9]/g, ''));
        $("#total_transaksi").val("Rp "+parseInt(total-dikurangi).toLocaleString());
  })
</script>