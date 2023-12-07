<?php
include("../koneksi.php");
$eid = enkripsiDekripsi(@$_GET['eid'], 'dekripsi');
$sql = mysqli_query($koneksi,"SELECT asis.*, ar.name_role, asis2.nama_anggota AS nama_ref FROM anggota_sistem AS asis
  JOIN anggota_role AS ar ON ar.id_role = asis.id_role
  LEFT JOIN anggota_sistem AS asis2 ON asis2.id_anggota = asis.id_anggota
  WHERE asis.id_anggota = '$eid'");
$data = mysqli_fetch_array($sql);
?>

<div class="modal-header">
  <h4 class="modal-title">Detail Akun</h4>
</div>

<div class="modal-body">
  <?php
  if(!empty($data['id_anggota'])){
    ?>
    <div class="table-responsive">
      <table class="table table-bordered">
        <tbody>
          <tr>
            <td>Nama</td>
            <td><?php echo $data['nama_anggota'] ?></td>
          </tr>
          <tr>
            <td>Username</td>
            <td><?php echo $data['username_anggota'] ?></td>
          </tr>
          <tr>
            <td>Jabatan</td>
            <td><?php echo $data['name_role'] ?></td>
          </tr>
          <tr>
            <td>Telepon</td>
            <td><?php echo $data['telepon_anggota'] ?></td>
          </tr>
          <tr>
            <td>Email</td>
            <td><?php echo $data['email_anggota'] ?></td>
          </tr>
          <tr>
            <td>Foto</td>
            <td><img width="100" height="auto" src="<?= base_url().'images/users/'.$data['foto_anggota'] ?>"></td>
          </tr>
          <tr>
            <td>Poin</td>
            <td><?php echo $data['poin_anggota'] ?></td>
          </tr>
          <tr>
            <td>Fee</td>
            <td><?php echo $data['fee_anggota'] ?></td>
          </tr>
          <tr>
            <td>Referral</td>
            <td><?php echo $data['nama_ref'] ?></td>
          </tr>
          <tr>
            <td>Provinsi</td>
            <td><?php echo $data['id_wilayah_provinsi'] ?></td>
          </tr>
          <tr>
            <td>Kelurahan</td>
            <td><?php echo $data['id_wilayah_district'] ?></td>
          </tr>
          <tr>
            <td>Kecamatan</td>
            <td><?php echo $data['id_wilayah_subdistrict'] ?></td>
          </tr>
          <tr>
            <td>Referensi Oleh</td>
            <td><?php echo $data['direferensikan_oleh'] ?></td>
          </tr>
          <tr>
            <td>Terakhir Login</td>
            <td><?php echo tanggal_jam_indo($data['terakhir_login']) ?></td>
          </tr>
          <tr>
            <td>Tanggal Buat</td>
            <td><?php echo tanggal_jam_indo($data['dibuat_tgl']) ?></td>
          </tr>
          <tr>
            <td>Dibuat Oleh</td>
            <td><?php echo $data['dibuat_oleh'] ?></td>
          </tr>
          <tr>
            <td>Tanggal Update</td>
            <td><?php echo tanggal_jam_indo($data['diedit_tgl']) ?></td>
          </tr>
          <tr>
            <td>Diupdate Oleh</td>
            <td><?php echo $data['diedit_oleh'] ?></td>
          </tr>
          <tr>
            <td>Tanggal Hapus</td>
            <td><?php echo tanggal_jam_indo($data['dihapus_tgl']) ?></td>
          </tr>
          <tr>
            <td>Dihapus Oleh</td>
            <td><?php echo $data['dihapus_oleh'] ?></td>
          </tr>
          <tr>
            <td>Status Aktif</td>
            <td><?php if($data['status_aktif'] == 'Y'){echo 'Aktif';} elseif($data['status_aktif'] == 'N'){echo 'Nonaktif';} else {echo "Error";}  ?></td>
          </tr>
          <tr>
            <td>Status Keagenan</td>
            <td><?php echo $data['status_keagenan'] ?></td>
          </tr>
          <tr>
            <td>Status Keanggotaan Dropshipper</td>
            <td><?php echo $data['status_keanggotaan_dropshipper'] ?></td>
          </tr>
          <tr>
            <td>Status Konfirmasi Email</td>
            <td><?php echo $data['status_konfirmasi_email'] ?></td>
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