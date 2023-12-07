<?php
// include("proses/ceklogin.php");
if(isset($id_admin_temp_fix)){
  $id_anggota = enkripsiDekripsi($_GET['eid'], 'dekripsi');
  $data_legalitas = mysqli_fetch_array(mysqli_query($koneksi,"SELECT id_anggota FROM anggota_sistem WHERE id_anggota = '$id_anggota'"));

  if (!empty($data_legalitas['id_anggota'])) {
    $query = mysqli_query($koneksi,"UPDATE `anggota_sistem` SET 
      `status_hapus` = 'Y',
      `dihapus_oleh` = '$id_admin_temp_fix',
      `dihapus_tgl` = CURRENT_TIME
      WHERE id_anggota = '$id_anggota'");
    if ($query) {
      echo '<script type="text/javascript">
      alert("Data berhasil diedit"); 
      window.location.href="?page=akun&action=kelola";
      </script>';
    } else {
      $iki_error = str_replace("'", "`", mysqli_error($koneksi));
      echo '<script type="text/javascript">
      alert("Gagal mengedit data\\n'.$iki_error.'"); 
      window.location.href="?page=akun&action=kelola";
      </script>';
    }
  } else {
    echo '<script type="text/javascript"> 
    alert("ID data tidak ditemukan"); 
    window.location.href="?page=akun&action=kelola";
    </script>';
  }
} else {
  header("location:index.php");
}
?>