<?php

require_once '../../templates/koneksi.php';


// ======================================================================
// ===================== REVISI TRANSAKSI SATUAN ========================
// ======================================================================

if(isset($_POST['revisi_transaksi_satuan'])){

    $id_transaksi       = enkripsiDekripsi(antiSQLi(@$_POST['id_transaksi']),'dekripsi');
    $nama_cust 			= antiSQLi(@$_POST['nama_cust']);
	$telp_cust 			= antiSQLi(@$_POST['telp_cust']);
	$keterangan_revisi 	= antiSQLi(@$_POST['keterangan_revisi']);
	
	$total_transaksi_lama = $db->query("SELECT total_transaksi FROM transaksi WHERE id_transaksi='$id_transaksi'")->fetch_assoc()['total_transaksi'];
	
	$pengurangan_tiket = 0;
	$pengurangan_harga = 0;
	if(isset($_POST['tiket_dihapus'])){
	    $pengurangan_tiket = count($_POST['tiket_dihapus']);
	    foreach($_POST['tiket_dihapus'] as $key => $id_tiket_enkrip){
	        $id_tiket = enkripsiDekripsi($id_tiket_enkrip,'dekripsi');
	        $harga_satuan = $db->query("SELECT harga_satuan FROM tiket WHERE id_tiket='$id_tiket'")->fetch_assoc()['harga_satuan'];
	        $pengurangan_harga += (int)$harga_satuan;
	        $sql = $db->query("UPDATE tiket SET status_tiket='2' WHERE id_tiket='$id_tiket'");
	    }  
	    
	}
	
	$total_transaksi_baru = $total_transaksi_lama - $pengurangan_harga;
	$id_revisi = createID('id_revisi', 'revisi_transaksi', 'RT');
    $query = "INSERT INTO revisi_transaksi (id_revisi, keterangan_revisi, kd_admin, kd_transaksi, jenis_revisi, nominal_awal, nominal_akhir	) VALUES ('$id_revisi', '$keterangan_revisi', '$_SESSION[id_admin]', '$id_transaksi', '1', '$total_transaksi_lama', '$total_transaksi_baru')";
    $sql = $db->query($query);
    
    $query = "UPDATE transaksi SET status_transaksi='2', total_transaksi=total_transaksi-$pengurangan_harga, jumlah_tiket=jumlah_tiket-$pengurangan_tiket WHERE id_transaksi='$id_transaksi'";
    $sql = $db->query($query);
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Transaksi atas nama '".$nama_cust."' berhasil revisi";
        header('Location: '.base_url().'admin-super.php?page=transaksi&action=detail&id='.enkripsiDekripsi($id_transaksi,'enkripsi'));
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem - ER ";
        header('Location: '.base_url().'admin-super.php?page=transaksi&action=detail&id='.enkripsiDekripsi($id_transaksi,'enkripsi'));
        exit();
    }
}


// ======================================================================
// ======================= TRANSAKSI DIBATALKAN =========================
// ======================================================================

if(isset($_POST['batalkan_transaksi'])){
    
    $id = enkripsiDekripsi(antiSQLi($_POST['batalkan_transaksi']), 'dekripsi');
    $alasan = antiSQLi($_POST['alasan']);
    
    $data = $db->query("SELECT * FROM photobooth_transaksi WHERE id_photobooth_transaksi='$id'")->fetch_assoc();
    $nominal_awal = $data['total_transaksi'];

    $db->query("UPDATE photobooth_transaksi SET status_transaksi='2' WHERE id_photobooth_transaksi='$id'");

    $db->query("UPDATE photobooth_tiket SET status_tiket='2' WHERE kd_photobooth_transaksi='$id'");
    
    $db->query("INSERT INTO photobooth_revisi_transaksi SET id_revisi=createID('photobooth_revisi_transaksi'), keterangan_revisi='$alasan', kd_admin='$_SESSION[id_admin]', kd_transaksi='$id', jenis_revisi='2', nominal_awal='$nominal_awal', nominal_akhir='0'");


    $_SESSION['notifikasi']['success'] = "Transaksi Telah Dibatalkan";
    header('Location: '.base_url().'admin-super.php?page=transaksi_photobooth&action=kelola');
    exit();

}
