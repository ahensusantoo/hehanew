<?php
require_once '../../templates/koneksi.php';


// ======================================================================
// =========================== TAMBAH SHIFT =============================
// ======================================================================

if(isset($_POST['tambah_shift'])){
    
    $nama_shift = antiSQLi($_POST['nama_shift']); 
    $id_shift = createID('id_shift', 'shift', 'SH');
   
    $query = "INSERT INTO shift (id_shift, nama_shift) VALUES ('$id_shift', '$nama_shift')";
    $sql = $db->query($query);
   
    if($sql){
        $_SESSION['notifikasi']['success'] = "Shift berhasil ditambahkan";
        header('Location: '.base_url().'admin-super.php?page=shift&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'admin-super.php?page=shift&action=kelola');
        exit();
    }
    
}


// ======================================================================
// ============================ UBAH SHIFT ==============================
// ======================================================================

if(isset($_POST['ubah_shift'])){
    
    $id_shift = enkripsiDekripsi(antiSQLi($_POST['id_shift']), 'dekripsi');
    $nama_shift = antiSQLi($_POST['nama_shift']); 

    if(isset($_POST['status_aktif'])){
        $status_aktif_shift = "Y";
    }else{
        $status_aktif_shift = "N";
    }
   
    $query = "UPDATE shift SET nama_shift='$nama_shift', status_aktif_shift='$status_aktif_shift' WHERE id_shift='$id_shift' ";
    $sql = $db->query($query);
   
    if($sql){
        $_SESSION['notifikasi']['success'] = "Shift berhasil diubah";
        header('Location: '.base_url().'admin-super.php?page=shift&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'admin-super.php?page=shift&action=kelola');
        exit();
    }
    
}