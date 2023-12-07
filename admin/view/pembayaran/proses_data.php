<?php
require_once '../../templates/koneksi.php';


// ======================================================================
// ====================== TAMBAH JENIS PEMBAYARAN =======================
// ======================================================================

if(isset($_POST['tambah_jenis_pembayaran'])){
    
    $nama_pembayaran = antiSQLi($_POST['nama_jenis_pembayaran']);
    $id_jenis_pembayaran = createID('id_jenis_pembayaran', 'jenis_pembayaran', 'JP');
    
    $query = "INSERT INTO jenis_pembayaran (id_jenis_pembayaran , nama_jenis_pembayaran) VALUES ('$id_jenis_pembayaran', '$nama_pembayaran')";
    $sql = $db->query($query);
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Jenis pembayaran '".$nama_pembayaran."' berhasil ditambahkan";
        header('Location: '.base_url().'admin-super.php?page=pembayaran&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'admin-super.php?page=pembayaran&action=kelola');
        exit();
    }
    
}


// ======================================================================
// ===================== AKTIFKAN JENIS PEMBAYARAN ======================
// ======================================================================

if(isset($_GET['aktifkan_jenis_pembayaran'])){
    
    $id_jenis_pembayaran = enkripsiDekripsi(antiSQLi($_GET['aktifkan_jenis_pembayaran']), 'dekripsi');
    $sql = $db->query("UPDATE jenis_pembayaran SET status_aktif='Y' WHERE id_jenis_pembayaran='$id_jenis_pembayaran'");
    
    if($sql){
        echo "berhasil";
        exit();
    }else{
        echo "gagal";
        exit();
    }
    
}



// ======================================================================
// =================== NONAKTIFKAN JENIS PEMBAYARAN =====================
// ======================================================================

if(isset($_GET['nonaktifkan_jenis_pembayaran'])){
    
    $id_jenis_pembayaran = enkripsiDekripsi(antiSQLi($_GET['nonaktifkan_jenis_pembayaran']), 'dekripsi');
    $sql = $db->query("UPDATE jenis_pembayaran SET status_aktif='N' WHERE id_jenis_pembayaran='$id_jenis_pembayaran'");
    
    if($sql){
        echo "berhasil";
        exit();
    }else{
        echo "gagal";
        exit();
    }
    
}
