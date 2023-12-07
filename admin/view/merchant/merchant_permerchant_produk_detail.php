<?php
include("../../templates/koneksi.php");
$eid = enkripsiDekripsi(@$_GET['eid'], 'dekripsi');
$sql = mysqli_query($db,"SELECT * FROM view_merchant_produk mp
  LEFT JOIN merchant_kategori_produk mkp ON mp.kd_merchant_kategori = mkp.id_merchant_kategori_produk
  LEFT JOIN merchant_stok ms ON mp.id_merchant_produk = ms.kd_merchant_produk 
  WHERE mkp.id_merchant_kategori_produk = '$eid'");
$data = mysqli_fetch_array($sql);

function jenis_merchant($nomor){
  if($nomor == '1'){
    return 'Souvenir';
  } elseif($nomor == '2'){
    return 'Makanan';
  } 
}
?>

<div class="modal-header">
  <h4 class="modal-title">Detail Produk</h4>
</div>

<div class="modal-body">
  <?php
  if(!empty($data['id_merchant_kategori_produk'])){
    ?>
    <div class="table-responsive">
      <table class="table table-bordered">
        <tbody>
          <tr>
            <td>Nama</td>
            <td><?php echo $data['nama_merchant'] ?></td>
          </tr>
          <tr>
            <td>Kode</td>
            <td><?php echo $data['kode_merchant'] ?></td>
          </tr>
          <tr>
            <td>Telepon</td>
            <td><?php echo $data['telp_merchant'] ?></td>
          </tr>
          <tr>
            <td>Email</td>
            <td><?php echo $data['email_merchant'] ?></td>
          </tr>
          <tr>
            <td>Panjang x Lebar</td>
            <td><?php echo $data['panjang_merchant'].' x '.$data['lebar_merchant'] ?></td>
          </tr>
          <tr>
            <td>Gambar</td>
            <td><img width="100" height="auto" src="<?= base_url().'/dist/img/barang/'.$query['gambar_produk'] ?>"></td>
          </tr>
          <tr>
            <td>Jenis</td>
            <td><?php echo jenis_merchant($data['status_merchant']) ?></td>
          </tr>
          <tr>
            <td>Tanggal Tambah</td>
            <td><?php echo tanggal_jam_indo($data['tgl_input_merchant']); ?></td>
          </tr>
        </tbody>
      </table>
    </div>
    <?php
  }else{
    echo 'Terjadi kesalahan sistem, data tidak ditemukan';
  }
  ?>
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
</div>