<?php

    $id_transaksi = enkripsiDekripsi($_GET['eid'], 'dekripsi');

    $transaksi = $db->query("SELECT * FROM merchant_transaksi A LEFT JOIN merchant B ON A.kd_merchant=B.id_merchant LEFT JOIN shift C ON A.kd_shift=C.id_shift LEFT JOIN jenis_pembayaran D ON A.kd_jenis_pembayaran=D.id_jenis_pembayaran WHERE A.id_merchant_transaksi='$id_transaksi'
")->fetch_assoc(); 

    $detail_mutasi = $db->query("SELECT *, A.harga_produk AS harga_produk_detail FROM merchant_transaksi_detail A LEFT JOIN merchant_produk B ON A.kd_merchant_produk=B.id_merchant_produk WHERE A.kd_merchant_transaksi='$id_transaksi' ")->fetch_all(MYSQLI_ASSOC);


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

          <?php if ($transaksi['status_transaksi'] == "3" ): ?>
            <div class="alert alert-danger" role="alert">
              <?php  
                $pembatalan = $db->query("SELECT * FROM merchant_transaksi_revisi A LEFT JOIN merchant_employee B ON A.kd_admin=B.id_merchant_employee WHERE A.kd_transaksi='$id_transaksi' ORDER BY A.id_merchant_transaksi_revisi  DESC LIMIT 1")->fetch_assoc();
              ?>
              Transaksi Dibatalkan Oleh <?= $pembatalan['nama_employee'] ?>, Dengan keterangan ( <?= $pembatalan['keterangan_revisi'] ?> )
            </div>
          <?php endif ?>

          <div class="card">
            <div class="card-body">
              <?php if ($transaksi['status_transaksi'] !== "3" ): ?>
                <button type="button" class="badge badge-danger float-right" data-toggle="modal" data-target="#modal_batalkan">Batalkan Transaksi</button>
              <?php endif ?>
              <h5>Detail Transaksi</h5>
              <table class="table table-sm">
                <tbody>
                  <tr>
                    <td>No. Nota</td>
                    <td>:</td>
                    <td><?= strtoupper($transaksi['no_nota']) ?></td>
                  </tr>
                  <tr>
                    <td>Tanggal</td>
                    <td>:</td>
                    <td><?= date_format(date_create($transaksi['tgl_input_transaksi']), 'd M y, H:i A') ?></td>
                  </tr>
                  <tr>
                    <td>Merchant</td>
                    <td>:</td>
                    <td><?= (@$transaksi['nama_merchant']) ?></td>
                  </tr>
                  <tr>
                    <td>Shift</td>
                    <td>:</td>
                    <td><?= (@$transaksi['nama_shift']) ?></td>
                  </tr>
                  <tr>
                    <td>Jenis Pembayaran</td>
                    <td>:</td>
                    <td><?= (@$transaksi['nama_jenis_pembayaran']) ?></td>
                  </tr>
                  <tr>
                    <td>No. Antrean</td>
                    <td>:</td>
                    <td><?= ($transaksi['no_antrian']) ?></td>
                  </tr>
                  <tr>
                    <td>Total Transaksi</td>
                    <td>:</td>
                    <td>Rp <?= number_format($transaksi['tagihan_nota']) ?></td>
                  </tr>
                  <tr>
                    <td>Status Transaksi</td>
                    <td>:</td>
                    <td>
                      <?php if ($transaksi['status_transaksi'] == "1"): ?>
                        Sedang Diproses
                      <?php elseif($transaksi['status_transaksi'] == "2"): ?>
                        Sukses
                      <?php elseif($transaksi['status_transaksi'] == "3" ) : ?>
                        Dibatalkan
                      <?php endif ?>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>  
        </div>

        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <h5>Rincian Transaksi</h5>
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
                      <td>Rp <?= number_format($value['harga_produk_detail']) ?></td>
                      <td><?= number_format($value['jumlah_produk']) ?></td>
                      <td>Rp <?= number_format($value['harga_setelah_diskon']) ?></td>
                      <td>Rp <?= number_format($value['harga_setelah_diskon'] * $value['jumlah_produk']) ?></td>
                      <?php $total_semua += $value['harga_setelah_diskon'] * $value['jumlah_produk'] ?>
                    </tr>
                  <?php endforeach ?>
                  <tr>
                    <td colspan="4"></td>
                    <td><b>TOTAL</b></td>
                    <td><b>Rp <?= number_format($total_semua) ?></b></td>
                  </tr>
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
