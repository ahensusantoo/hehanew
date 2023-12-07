<?php
require_once '../../templates/koneksi.php';


// ======================================================================
// ========================= TAMBAH HARI LIBUR ==========================
// ======================================================================


if(isset($_POST['tambah_hari_libur'])){

    $tahun          = antiSQLi($_POST['tahun_libur']);
    $bulan          = antiSQLi($_POST['bulan_libur']);
    $tahun_bulan    = $tahun."-".$bulan;
    
    $cek_data = $db->query("SELECT COUNT(tahun_bulan) AS jml FROM hari_libur WHERE tahun_bulan='$tahun_bulan'")->fetch_assoc()['jml'];
    if($cek_data > 0){
        $_SESSION['notifikasi']['fail'] = "Bulan '".$bulan."/".$tahun."' telah ada didatabase, anda bisa mengubah melalui menu kelola hari libur.";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=libur&action=kelola');
        exit();
    }
    
    $hari_libur =  implode("|",$_POST['hari_libur']);
    $query = "INSERT INTO hari_libur (tahun_bulan, hari_libur) VALUES ('$tahun_bulan', '$hari_libur')";
    $sql = $db->query($query);
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Libur bulan '".$bulan."/".$tahun."' berhasil ditambahkan";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=libur&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=libur&action=kelola');
        exit();
    }
    
}

// ======================================================================
// ========================== UBAH HARI LIBUR ===========================
// ======================================================================

if(isset($_POST['edit_hari_libur'])){
    
    $tahun          = antiSQLi($_POST['tahun_libur']);
    $bulan          = antiSQLi($_POST['bulan_libur']);
    $tahun_bulan    = $tahun."-".$bulan;
    
    $hari_libur =  implode("|",$_POST['hari_libur']);
    
    $query = "UPDATE hari_libur SET hari_libur='$hari_libur' WHERE tahun_bulan='$tahun_bulan'";
    $sql = $db->query($query);
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Libur bulan '".$bulan."/".$tahun."' berhasil diubah";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=libur&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=libur&action=kelola');
        exit();
    }
    
    
}

