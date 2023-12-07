<?php

  $id_transaksi = enkripsiDekripsi($_GET['id'],'dekripsi');
  $data_transaksi = $db->query("SELECT * FROM transaksi A JOIN admin B ON A.kd_admin=B.id_admin JOIN shift C ON A.kd_shift=C.id_shift WHERE A.id_transaksi='$id_transaksi'")->fetch_assoc();
  $detail_transaksi = $db->query("SELECT * FROM tiket A JOIN jenis_tiket B ON A.kd_jenis_tiket=B.id_jenis_tiket WHERE kd_transaksi='$id_transaksi'")->fetch_all(MYSQLI_ASSOC);

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
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <table class="" style="width: 100%">
                  <tbody>
                    <tr>
                      <td>Tanggal</td>
                      <td> : </td>
                      <td><?= tanggal_jam_indo($data_transaksi['tanggal_transaksi']) ?></td>
                    </tr>
                    <tr>
                      <td>Pembeli</td>
                      <td> : </td>
                      <td>
                        <?= $data_transaksi['nama_cust'] != "" ? $data_transaksi['nama_cust']."( ".$data_transaksi['telp_cust']." )" : "-"  ?>
                      </td>
                    </tr>
                    <tr>
                      <td>Admin</td>
                      <td> : </td>
                      <td><?= $data_transaksi['nama_admin'] ?></td>
                    </tr>
                    <tr>
                      <td>Shift</td>
                      <td> : </td>
                      <td><?= $data_transaksi['nama_shift'] ?></td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="col-md-6">
                <table style="width: 100%">
                  <tbody>
                    <tr>
                      <td>Total Pembelian</td>
                      <td> : </td>
                      <td>Rp <?= number_format($data_transaksi['nominal_sebelum_diskon']) ?></td>
                    </tr>
                    <tr>
                      <td>Diskon</td>
                      <td> : </td>
                      <td>Rp <?= number_format($data_transaksi['diskon']) ?></td>
                    </tr>
                    <tr>
                      <td>Total Transaksi</td>
                      <td> : </td>
                      <td>Rp <?= number_format($data_transaksi['total_transaksi']) ?></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="col-md-12">
              <hr>
              <center><label>RINCIAN</label></center>
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
                      <td>
                    </tr>
                  <?php $nomor++; endforeach ?>
                </tbody>
              </table>
              <hr>
              <!--<button class="btn btn-block btn-info">Cetak Semua Tiket Yang Belum Dipakai</button>-->
            </div>
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