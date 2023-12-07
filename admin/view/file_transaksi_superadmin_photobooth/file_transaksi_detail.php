<?php

  $id_transaksi = enkripsiDekripsi($_GET['id'],'dekripsi');
  $data_transaksi = $db->query("SELECT * FROM transaksi A JOIN admin B ON A.kd_admin=B.id_admin JOIN shift C ON A.kd_shift=C.id_shift WHERE A.id_transaksi='$id_transaksi'")->fetch_assoc();
  $detail_transaksi = $db->query("SELECT * FROM tiket A JOIN jenis_tiket B ON A.kd_jenis_tiket=B.id_jenis_tiket WHERE kd_transaksi='$id_transaksi'")->fetch_all(MYSQLI_ASSOC);
  $data_revisi = $db->query("SELECT * FROM revisi_transaksi A JOIN admin B ON A.kd_admin=B.id_admin WHERE kd_transaksi='$id_transaksi'")->fetch_all(MYSQLI_ASSOC);

?>
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Detail Transaksi</h1>
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
            <!-- <a href="?page=transaksi&action=revisi&id=<?= $_GET['id'] ?>" class="btn btn-sm btn-info float-right">Revisi Transaksi</a> -->
            <a href="view/transaksi_superadmin/proses_data.php?batalkan_transaksi=<?= $_GET['id'] ?>" onclick="return confirm('Yakin ingin membatalkan?')" class="btn btn-sm btn-danger float-right">Batalkan Transaksi</a>
          </div>
          <div class="card-body">
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
                      <td>Pembeli</td>
                      <td> : </td>
                      <td><?= $data_transaksi['nama_cust'] ?> (<?= $data_transaksi['telp_cust'] ?>)</td>
                    </tr>
                    <tr>
                      <td>Total Transaksi</td>
                      <td> : </td>
                      <td>Rp <?= number_format($data_transaksi['total_transaksi']) ?></td>
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
                    </tr>
                  <?php $nomor++; endforeach ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Revisi Transaksi</h3>
          </div>
          <div class="card-body">
                <table class="table table-sm">
                  <thead>
                      <tr>
                          <th>Tanggal</th>
                          <th>Keterangan</th>
                          <th>Admin</th>
                          <th>Jenis Revisi</th>
                          <th>Nominal Awal</th>
                          <th>Nominal Akhir</th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php if (!isset($data_revisi[0])): ?>
                        <tr><td colspan="6" align="center">Tidak ada revisi pada transaksi ini</td></tr>
                      <?php endif ?>

                      <?php foreach($data_revisi as $key => $value) : ?>
                        <tr>
                            <td><?= $value['tanggal_revisi'] ?></td>
                            <td><?= $value['keterangan_revisi'] ?></td>
                            <td><?= $value['nama_admin'] ?></td>
                            <td>
                                <?php if($value['jenis_revisi'] == "1"): ?>
                                    Dibatalkan Sebagian
                                <?php else: ?>
                                    Dibatalkan Seluruhnya
                                <?php endif; ?>
                            </td>
                            <td>Rp <?= number_format($value['nominal_awal']) ?></td>
                            <td>Rp <?= number_format($value['nominal_akhir']) ?></td>
                        </tr>
                      <?php  endforeach; ?>
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