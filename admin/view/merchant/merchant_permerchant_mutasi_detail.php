<?php

    $id_mutasi_stock = enkripsiDekripsi($_GET['eid'], 'dekripsi');

    $mutasi = $db->query("SELECT * FROM merchant_mutasi_stok A LEFT JOIN merchant_employee B ON A.kd_merchant_employee=B.id_merchant_employee WHERE A.id_merchant_mutasi_stok='$id_mutasi_stock' ")->fetch_assoc(); 

    $detail_mutasi = $db->query("SELECT * FROM merchant_mutasi_detail A JOIN merchant_produk B ON A.kd_merchant_produk=B.id_merchant_produk WHERE A.kd_merchant_mutasi_stok='$id_mutasi_stock'")->fetch_all(MYSQLI_ASSOC);


?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <!-- <h1>Detail Transaksi</h1> -->
      </div>
    </div>
  </div>
</section>

<section class="content">
  <div class="container-fluid">
      <div class="row">
        

        <div class="col-md-12">

          <div class="card">
            <div class="card-body">
              <h5>Detail Mutasi</h5>
              <table class="table table-sm">
                <tbody>
                  <tr>
                    <td>No. Mutasi</td>
                    <td>:</td>
                    <td><?= id_ke_struk($mutasi['id_merchant_mutasi_stok']) ?></td>
                  </tr>
                  <tr>
                    <td>Tanggal</td>
                    <td>:</td>
                    <td><?= date_format(date_create($mutasi['tanggal_mutasi']), 'd M y, H:i A') ?></td>
                  </tr>
                  <tr>
                    <td>Jenis Mutasi</td>
                    <td>:</td>
                    <td>
                      <?php if ($mutasi['jenis_mutasi'] == "1"): ?>
                        Mutasi Masuk
                      <?php elseif($mutasi['jenis_mutasi'] == "2"): ?>
                        Mutasi Keluar
                      <?php endif ?>
                    </td>
                  </tr>
                  <tr>
                    <td>Pembuat</td>
                    <td>:</td>
                    <td><?= (@$mutasi['nama_employee']) ?></td>
                  </tr>
                  <tr>
                    <td>Keterangan</td>
                    <td>:</td>
                    <td><?= (@$mutasi['keterangan_mutasi']) ?></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>  
        </div>

        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <h5>Rincian Mutasi</h5>
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Produk</th>
                    <th>Jumlah</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $total_semua = 0 ?>
                  <?php foreach ($detail_mutasi as $key => $value): ?>
                    <tr>
                      <td><?= $key+1 ?></td>
                      <td><?= ($value['nama_produk']) ?></td>
                      <td><?= number_format($value['jumlah_mutasi']) ?></td>
                    </tr>
                  <?php endforeach ?>
                </tbody>
              </table>
            </div>
          </div>  
        </div>

      </div>
  </div>
</section>


<!-- Modal -->
<form action="view/merchant/proses_data.php" method="post">
  <div class="modal fade" id="modal_batalkan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Batalkan Transaksi</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Alasan Pembatalan</label>
            <textarea class="form-control" rows="3" name="alasan" required=""></textarea>
            <input type="hidden" name="merchant" value="<?= @$_GET['merchant'] ?>" required="">
            <input type="hidden" name="id_transaksi" value="<?= @$_GET['eid'] ?>" required="">
            <input type="hidden" name="batalkan_transaksi">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Kembali</button>
          <button type="submit" class="btn btn-danger">Batalkan</button>
        </div>
      </div>
    </div>
  </div>
</form>
