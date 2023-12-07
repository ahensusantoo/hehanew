<?php

require_once '../../templates/koneksi.php';


// ======================================================================
// ======================= TAMBAH ADMINISTRATOR =========================
// ======================================================================

if (isset($_POST['tambah_admin'])){
    
    $nama_admin = antiSQLi($_POST['nama_admin']);
    $username_admin = antiSQLi($_POST['username_admin']);
    $password_admin = antiSQLi($_POST['password_admin']);
    $password_admin_cnf = antiSQLi($_POST['password_admin_cnf']);
    $jabatan_admin = antiSQLi($_POST['jabatan_admin']);
    
    if($password_admin != $password_admin_cnf){
        
        $_SESSION['notifikasi']['fail'] = "Password anda tidak sama, mohon coba lagi";
        header('Location: ?page=admin&action=kelola');
        exit();
    
    }
    
    $password_enkripsi = enkripsiDekripsi($password_admin,'enkripsi');
    $id_admin = createID('id_admin', 'admin', 'AD');
    $query = "INSERT INTO admin (id_admin, nama_admin, username_admin, password_admin, jabatan_admin) VALUES ('$id_admin', '$nama_admin', '$username_admin', '', '$jabatan_admin') ";
    $sql = $db->query($query);
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Admin ".$username_admin." berhasil ditambahkan";
        header('Location: '.base_url().'admin-super.php?page=admin&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'admin-super.php?page=admin&action=kelola');
        exit();
    }
    
    
}



// ======================================================================
// ======================== EDIT ADMINISTRATOR ==========================
// ======================================================================

if(isset($_POST['edit_admin'])){
    
    $id_admin = enkripsiDekripsi($_POST['id_admin'],'dekripsi');
    $nama_admin = antiSQLi($_POST['nama_anggota']);
    $username_admin = antiSQLi($_POST['username_anggota']);
    $password_admin = antiSQLi($_POST['password_anggota']);
    $password_admin_cnf = antiSQLi($_POST['password_anggota_cnf']);
    $jabatan_admin = antiSQLi($_POST['jabatan_admin']);
    $password_enkripsi = enkripsiDekripsi($password_admin,'enkripsi');
    
    if($password_admin !== ""){
        if($password_admin != $password_admin_cnf){
            $_SESSION['notifikasi']['fail'] = "Password anda tidak sama, mohon coba lagi";
            header('Location: ?page=admin&action=kelola');
            exit();
        }else{
            $query_ubah_password = ", password_admin='".$password_enkripsi."' ";
        }
    }else{
        $query_ubah_password = "";
    }
    
    
    
    
    $query = "UPDATE admin SET nama_admin='$nama_admin', username_admin='$username_admin', jabatan_admin='$jabatan_admin' $query_ubah_password WHERE id_admin='$id_admin'";
    $sql = $db->query($query);
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Admin ".$username_admin." berhasil diubah";
        header('Location: '.base_url().'admin-super.php?page=admin&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'admin-super.php?page=admin&action=kelola');
        exit();
    }
    
}


// ======================================================================
// ======================= HAPUS ADMINISTRATOR ==========================
// ======================================================================

if(isset($_GET['hapus_admin'])){
    $id_admin = enkripsiDekripsi($_GET['id'],'dekripsi');
    
    $query = "UPDATE admin SET status_rmv_admin='Y' WHERE id_admin='$id_admin'";
    $sql = $db->query($query);
    
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Admin ".$_GET['hapus_admin']." berhasil diubah";
        header('Location: '.base_url().'admin-super.php?page=admin&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'admin-super.php?page=admin&action=kelola');
        exit();
    }
    
}
