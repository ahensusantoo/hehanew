<?php
require_once '../../templates/koneksi.php';


// ======================================================================
// ========================= TAMBAH PHOTOBOOTH ==========================
// ======================================================================

if(isset($_POST['tambah_photobooth'])){
    
    $nama_photobooth_stan = antiSQLi($_POST['nama_photobooth_stan']);
    $harga_photobooth_stan = preg_replace("/[^0-9]/", "", antiSQLi($_POST['harga_photobooth_stan']) );
    $status_display_photobooth = antiSQLi($_POST['status_display_photobooth']);
    
    $id_photobooth_stan = createID('id_photoboothambil_stan','photoboothambil_stan','PF');
    $query = "INSERT INTO photoboothambil_stan (id_photoboothambil_stan, nama_photoboothambil_stan, harga_photoboothambil_stan, status_display_photoboothambil ) VALUES ('$id_photobooth_stan', '$nama_photobooth_stan', '$harga_photobooth_stan', '$status_display_photobooth')";
    $sql = $db->query($query);
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Photobooth '".$nama_photobooth_stan."' berhasil ditambahkan";
        header('Location: '.base_url().'admin-super.php?page=file_photobooth&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'admin-super.php?page=file_photobooth&action=kelola');
        exit();
    }
    
}




// ======================================================================
// ========================== UBAH PHOTOBOOTH ===========================
// ======================================================================

if(isset($_POST['ubah_photobooth'])){
    
    $id = enkripsiDekripsi(antiSQLi($_POST['id']), 'dekripsi');
    $nama_photoboothambil_stan = antiSQLi($_POST['nama_photoboothambil_stan']);
    $status_display_photoboothambil = antiSQLi($_POST['status_display_photoboothambil']);
    
    $query = "UPDATE photoboothambil_stan SET nama_photoboothambil_stan='$nama_photoboothambil_stan', status_display_photoboothambil='$status_display_photoboothambil' WHERE id_photoboothambil_stan ='$id' ";
    $sql = $db->query($query);
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "File Photobooth '".$nama_photoboothambil_stan."' berhasil diubah";
        header('Location: '.base_url().'admin-super.php?page=file_photobooth&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'admin-super.php?page=file_photobooth&action=kelola');
        exit();
    }
    
}





// ======================================================================
// ======================== HAPUS FILE PHOTOBOOTH =======================
// ======================================================================

if (isset($_GET['hapus_photobooth'])){
    
    $id_photoboothambil_stan  = enkripsiDekripsi(antiSQLi($_GET['eid']), 'dekripsi');
    
    $query = "UPDATE photoboothambil_stan SET status_remove_photoboothambil='Y' WHERE id_photoboothambil_stan ='$id_photoboothambil_stan' ";
    $sql = $db->query($query);
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Tiket berhasil dihapus";
        header('Location: '.base_url().'admin-super.php?page=file_photobooth&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'admin-super.php?page=file_photobooth&action=kelola');
        exit();
    }
    
}






// ======================================================================
// ====================== AKTIFKAN STAN PHOTOBOOTH ======================
// ======================================================================

if(isset($_GET['aktifkan_stan_photobooth'])){
    
    $id_photobooth_stan = enkripsiDekripsi(antiSQLi($_GET['aktifkan_stan_photobooth']), 'dekripsi');
    $sql = $db->query("UPDATE photoboothambil_stan SET status_display_photoboothambil='Y' WHERE id_photoboothambil_stan='$id_photobooth_stan'");
    
    if($sql){
        echo "berhasil";
        exit();
    }else{
        echo "gagal";
        exit();
    }
    
}



// ======================================================================
// ==================== NONAKTIFKAN STAN PHOTOBOOTH =====================
// ======================================================================

if(isset($_GET['nonaktifkan_stan_photobooth'])){
    
    $id_photobooth_stan = enkripsiDekripsi(antiSQLi($_GET['nonaktifkan_stan_photobooth']), 'dekripsi');
    $sql = $db->query("UPDATE photoboothambil_stan SET status_display_photoboothambil='N' WHERE id_photoboothambil_stan='$id_photobooth_stan'");
    
    if($sql){
        echo "berhasil";
        exit();
    }else{
        echo "gagal";
        exit();
    }
    
}