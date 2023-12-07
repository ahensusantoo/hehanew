<?php 
    $list_anggota = $db->query("SELECT * FROM merchant_employee WHERE status_remove_employee='N' ")->fetch_all(MYSQLI_ASSOC);
    $list_shift = $db->query("SELECT * FROM shift WHERE status_aktif_shift='Y' ")->fetch_all(MYSQLI_ASSOC);


    if (isset($_GET['tanggal']) AND isset($_GET['shift'])) { 
      
      $tanggal = $_GET['tanggal'];
      $shift = enkripsiDekripsi($_GET['shift'], 'dekripsi');

      if (!empty($_GET['employee']) OR $_GET['employee'] != "") {
        // echo "MASUK 1";exit();
        $id_merchant_employee = enkripsiDekripsi($_GET['employee'], 'dekripsi');
        $employee = $db->query("SELECT * FROM merchant_employee WHERE id_merchant_employee='$id_merchant_employee' ")->fetch_all(MYSQLI_ASSOC);
        $employee_dipilih = $employee;
      }else{
        // echo "MASUK 2";exit();
        $employee_dipilih = $list_anggota;
      }

      $data = [];
      foreach ($employee_dipilih as $key_employee => $value_employee) {
        $list_produk = $db->query("SELECT A.kd_merchant_produk, A.harga_produk, C.nama_produk, C.kode_produk FROM merchant_transaksi_detail A JOIN merchant_transaksi B ON A.kd_merchant_transaksi=B.id_merchant_transaksi JOIN merchant_produk C ON A.kd_merchant_produk=C.id_merchant_produk WHERE A.kd_merchant_employee='$value_employee[id_merchant_employee]' AND DATE(A.tgl_input_detail)='$tanggal' AND B.kd_shift='$shift' AND B.kd_jenis_pembayaran!='' AND A.status_transaksi_detail='2' GROUP BY A.kd_merchant_produk,A.harga_produk ")->fetch_all(MYSQLI_ASSOC);

        foreach ($list_produk as $key_produk => $value_produk) {

          $data_perproduk = $db->query("SELECT SUM(A.harga_produk) AS harga_produk, SUM((A.harga_setelah_diskon-A.harga_produk)*A.jumlah_produk) AS diskon_perbarang, A.jumlah_produk, SUM(A.harga_setelah_diskon*A.jumlah_produk) AS subtotal FROM merchant_transaksi_detail A JOIN merchant_transaksi B ON A.kd_merchant_transaksi=B.id_merchant_transaksi WHERE A.kd_merchant_produk='$value_produk[kd_merchant_produk]' AND DATE(A.tgl_input_detail)='$tanggal' AND A.kd_merchant_employee='$value_employee[id_merchant_employee]' AND B.kd_shift='$shift' AND B.kd_jenis_pembayaran!='' AND A.harga_produk='$value_produk[harga_produk]' AND A.status_transaksi_detail='2' ")->fetch_assoc();

          $data[$key_employee]['nama_employee'] = $value_employee['nama_employee'];
          $data[$key_employee]['penjualan'][$key_produk]['kode_produk'] = $value_produk['kode_produk'];
          $data[$key_employee]['penjualan'][$key_produk]['nama_produk'] = $value_produk['nama_produk'];
          $data[$key_employee]['penjualan'][$key_produk]['harga_produk'] = $data_perproduk['harga_produk'];
          $data[$key_employee]['penjualan'][$key_produk]['diskon_perbarang'] = $data_perproduk['diskon_perbarang'];
          $data[$key_employee]['penjualan'][$key_produk]['jumlah_produk'] = $data_perproduk['jumlah_produk'];
          $data[$key_employee]['penjualan'][$key_produk]['subtotal'] = $data_perproduk['subtotal'];
        }

        $list_pembayaran = $db->query("SELECT DISTINCT(A.kd_jenis_pembayaran) AS id_jenis_pembayaran, B.nama_jenis_pembayaran FROM merchant_transaksi A JOIN jenis_pembayaran B ON A.kd_jenis_pembayaran=B.id_jenis_pembayaran WHERE A.kd_merchant_employee='$value_employee[id_merchant_employee]' AND A.kd_shift='$shift' AND A.kd_jenis_pembayaran!='' AND DATE(A.tgl_input_transaksi)='$tanggal' AND A.status_transaksi='2'")->fetch_all(MYSQLI_ASSOC);

        foreach ($list_pembayaran as $key_bayar => $value_bayar) {
          $jenis_bayar = $db->query("SELECT SUM(A.nominal_sebelum_diskon) AS total, SUM(A.diskon) AS diskon, SUM(A.tagihan_nota) AS tagihan_nota FROM merchant_transaksi A WHERE A.kd_jenis_pembayaran='$value_bayar[id_jenis_pembayaran]' AND  A.kd_shift='$shift' AND DATE(A.tgl_input_transaksi)='$tanggal' AND A.kd_merchant_employee='$value_employee[id_merchant_employee]' AND A.status_transaksi='2'")->fetch_assoc();
          $data[$key_employee]['pembayaran'][$key_bayar]['nama'] = $value_bayar['nama_jenis_pembayaran'];
          $data[$key_employee]['pembayaran'][$key_bayar]['total'] = $jenis_bayar['total'];
          $data[$key_employee]['pembayaran'][$key_bayar]['diskon'] = $jenis_bayar['diskon'];
          $data[$key_employee]['pembayaran'][$key_bayar]['tagihan_nota'] = $jenis_bayar['tagihan_nota'];
        }

        
      }


    }


?>


<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Laporan Closing Stall</h1>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <form method="GET" action="">
              <input type="hidden" name="page" value="penjualan">
              <input type="hidden" name="action" value="closingstall">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <input type="date" class="form-control" name="tanggal" value="<?= @$_GET['tanggal'] ?>" required="">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <select class="form-control" name="employee" required=""> 
                      <option value="">Semua Anggota</option>
                      <?php foreach ($list_anggota as $key => $value): ?>
                        <?php $id_enc = enkripsiDekripsi($value['id_merchant_employee'], 'enkripsi') ?>
                        <option value="<?= $id_enc ?>"
                          <?= ($id_enc == @$_GET['employee'])? 'selected': NULL ?>
                          ><?= $value['nama_employee'] ?></option>
                      <?php endforeach ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <select class="form-control" name="shift" required="">
                      <?php foreach ($list_shift as $key => $value): ?>
                        <?php $id_enc = enkripsiDekripsi($value['id_shift'], 'enkripsi') ?>
                        <option value="<?= $id_enc ?>"
                          <?= ($id_enc == @$_GET['shift'])? 'selected': NULL ?>
                          ><?= $value['nama_shift'] ?></option>
                      <?php endforeach ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <button class="btn btn-primary btn-block">Filter</button>
                  </div>
                </div>
              </div>
            </form>

            <hr>

            <?php if (empty($data)): ?>
              <center>Filter terlebih dahulu</center>
            <?php else: ?>
               <div class="float-right">
                  <button type="button" id="btn_excel" class="bnt btn-info mb-4">
                    <i class="fa fa-download"> Export Excel</i>
                  </button>
                </div>
               

              <?php foreach ($data as $key => $value): ?>
                <label>Nama : <?= $value['nama_employee'] ?></label>

                <div class="table-responsive">
                  <table class="table table-sm table-bordered table-striped table2excel">
                    <thead>
                      <tr>
                        <th>Kode</th>
                        <th>Produk</th>
                        <th>Harga Jual</th>
                        <th>Jumlah Terjual</th>
                        <th>Total Diskon</th>
                        <th>Subtotal</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $total_jumlah = 0 ?>
                      <?php $total_diskon = 0 ?>
                      <?php $total_final = 0 ?>
                      <?php foreach ($value['penjualan'] as $key_penjualan => $value_penjualan): ?>
                        <?php $total_jumlah += $value_penjualan['jumlah_produk'] ?>
                        <?php $total_diskon += $value_penjualan['diskon_perbarang'] ?>
                        <?php $total_final += $value_penjualan['subtotal'] ?>
                        <tr>
                          <td><?= $value_penjualan['kode_produk'] ?></td>
                          <td><?= $value_penjualan['nama_produk'] ?></td>
                          <td align="right"><?= $value_penjualan['harga_produk'] ?></td>
                          <td align="right"><?= $value_penjualan['jumlah_produk'] ?></td>
                          <td align="right"><?= $value_penjualan['diskon_perbarang'] ?></td>
                          <td align="right"><?= $value_penjualan['subtotal'] ?></td>
                        </tr>
                      <?php endforeach ?>
                        <tr>
                        <td colspan="3" align="right">TOTAL</td>
                        <td align="right"><?= $total_jumlah ?></td>
                        <td align="right">Rp <?= $total_diskon ?></td>
                        <td align="right">Rp <?= $total_final ?></td>
                      </tr>
                      <tr>
                        <td colspan="6"><br><br></td>
                      </tr>
                    </tbody>
                    <!-- <tfoot> -->
                      
                    <!-- </tfoot> -->


                    <thead>
                      <tr>
                        <th>No</th>
                        <th colspan="4">Jenis Pembayaran</th>
                        <th>Total</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $total_diskon = 0 ?>
                      <?php $total_final = 0 ?>
                      <?php foreach ($value['pembayaran'] as $key_pembayaran => $value_pembayaran): ?>
                        <?php $total_diskon += $value_pembayaran['diskon'] ?>
                        <?php $total_final += $value_pembayaran['tagihan_nota'] ?>
                        <tr>
                          <td ><?= $key_pembayaran+1 ?></td>
                          <td colspan="4"><?= $value_pembayaran['nama'] ?></td>
                          <td align="right"><?= $value_pembayaran['total'] ?></td>
                        </tr>
                      <?php endforeach ?>
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="5" align="right">DISKON TRANSAKSI</td>
                        <td align="right">Rp <?= $total_diskon ?></td>
                      </tr>
                      <tr>
                        <td colspan="5" align="right">TOTAL</td>
                        <td align="right">Rp <?= $total_final ?></td>
                      </tr>
                    </tfoot>

                  </table>
                </div>
                <br>

                <!-- <div class="table-responsive">
                  <table class="table table-sm table-bordered table-striped table2excel">
                    
                  </table>
                </div> -->
              <?php endforeach ?>


            <?php endif ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>


<script src="<?=base_url()?>plugins/export-excel/src/jquery.table2excel.js"></script>
<!-- <input type="hidden" name="namafile" id="namafile" value="<?= 'Laporan Gabungan Tiket Shift '.tanggal_indo($tgl_awal).' - '.tanggal_indo($tgl_akhir); ?>"> -->
<input type="hidden" name="namafile" id="namafile" value="<?= 'Laporan Closing Stall '.tanggal_indo(@$_GET['tanggal']) ?>">

<script type="text/javascript">
  $('#btn_excel').click(function(){ 

    var name_element = document.getElementById('namafile').value;
    $(".table2excel").table2excel({
      exclude: ".noExl",
      name: "Excel Document Name",
      filename: name_element,
      fileext: ".xls",
      exclude_img: true,
      exclude_links: true,
      exclude_inputs: true
    });
    // window.onfocus=function(){ setTimeout(function () { window.close(); }, 500); }
  });
</script>
