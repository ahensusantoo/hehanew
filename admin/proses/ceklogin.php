<?php
if(!isset($_SESSION['session_admin_byox'])){
	header("location:index.php?art=".enkripsiDekripsi("Akun terlogout oleh sistem", "enkripsi"));
} else {
	$id_admin_temp = enkripsiDekripsi($_SESSION['session_admin_byox'], "dekripsi");
	$data_admin = mysqli_fetch_array(mysqli_query($koneksi,"SELECT asi.id_role, asi.id_anggota, asi.status_aktif, asi.status_hapus FROM anggota_sistem AS asi WHERE asi.id_anggota = '$id_admin_temp'"));

	if($data_admin['status_hapus'] == 'Y'){
		session_destroy();
		header("location:index.php?art=".enkripsiDekripsi("Akun anda dihapus Super Admin.", "enkripsi"));
	}
	if($data_admin['status_aktif'] == 'N'){
		session_destroy();
		header("location:index.php?art=".enkripsiDekripsi("Akun anda dinonaktifkan Super Admin.", "enkripsi"));
	}
	if(empty($data_admin['id_anggota'])) {
		session_destroy();
		header("location:index.php?art=".enkripsiDekripsi("Akun anda tidak ditemukan.", "enkripsi"));
	}
	if (empty($data_admin['id_anggota']) || !$data_admin['id_role'] == 'AGN' || !$data_admin['id_role'] == 'HQ') {
		session_destroy();
		header("location:index.php?art=".enkripsiDekripsi("Terjadi Kesalahan Sistem, Role tidak tersedia.", "enkripsi"));
	}
	$id_admin_temp_fix = $data_admin['id_anggota'];
}
?>