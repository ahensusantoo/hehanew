<?php
require_once '../../templates/koneksi.php';


// ======================================================================
// ========================= TAMBAH PHOTOBOOTH ==========================
// ======================================================================

if(isset($_POST['tambah_photobooth'])){
    
    $nama_photobooth_stan = antiSQLi($_POST['nama_photobooth_stan']);
    $harga_photobooth_stan = preg_replace("/[^0-9]/", "", antiSQLi($_POST['harga_photobooth_stan']) );
    $status_display_photobooth = antiSQLi($_POST['status_display_photobooth']);
    $status_paket = antiSQLi($_POST['status_paket']);
    

    $db->begin_transaction();

    $id_photobooth_stan = createID('id_photobooth_stan','photobooth_stan','PB');
    $query = "INSERT INTO photobooth_stan (id_photobooth_stan, nama_photobooth_stan, harga_photobooth_stan, status_display_photobooth) VALUES ('$id_photobooth_stan', '$nama_photobooth_stan', '$harga_photobooth_stan', '$status_display_photobooth')";
    $sql[] = $db->query($query);

    if ($_POST['status_paket'] == "Y") {

        foreach ($_POST['paket'] as $key => $value) {
            $id_detail_paket = enkripsiDekripsi($key, 'dekripsi');
            $sql[] = $db->query("INSERT INTO photobooth_stan_paket SET id_photobooth_stan_paket='$id_photobooth_stan', id_photobooth_stan='$id_detail_paket' ");
        }

    }


    if(in_array(false, $sql)){
        $db->rollback();
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'admin-super.php?page=photobooth&action=kelola');
        exit();
    }else{
        $db->commit();
        $_SESSION['notifikasi']['success'] = "Photobooth '".$nama_photobooth_stan."' berhasil ditambahkan";
        header('Location: '.base_url().'admin-super.php?page=photobooth&action=kelola');
        exit();
    }
    
}



// ======================================================================
// ========================== UBAH PHOTOBOOTH ===========================
// ======================================================================

if(isset($_POST['ubah_photobooth'])){


    $id = enkripsiDekripsi(antiSQLi($_POST['id']), 'dekripsi');
    $nama_photobooth_stan = antiSQLi($_POST['nama_photobooth_stan']);
    $status_display_photobooth = antiSQLi($_POST['status_display_photobooth']);
    $status_paket = antiSQLi($_POST['status_paket']);


    $db->begin_transaction();
    
    $query = "UPDATE photobooth_stan SET nama_photobooth_stan='$nama_photobooth_stan', status_display_photobooth='$status_display_photobooth', status_paket='$status_paket' WHERE id_photobooth_stan='$id' ";
    $sql[] = $db->query($query);

    $sql[] = $db->query("DELETE FROM photobooth_stan_paket WHERE id_photobooth_stan_paket='$id' ");

    if ($_POST['status_paket'] == "Y") {

        foreach ($_POST['paket'] as $key => $value) {
            $id_detail_paket = enkripsiDekripsi($key, 'dekripsi');
            $sql[] = $db->query("INSERT INTO photobooth_stan_paket SET id_photobooth_stan_paket='$id', id_photobooth_stan='$id_detail_paket' ");
        }

    }

    if(in_array(false, $sql)){
        $db->rollback();
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'admin-super.php?page=photobooth&action=kelola');
        exit();
    }else{
        $db->commit();
        $_SESSION['notifikasi']['success'] = "Photobooth '".$nama_photobooth_stan."' berhasil diubah";
        header('Location: '.base_url().'admin-super.php?page=photobooth&action=kelola');
        exit();
    }

    
}





// ======================================================================
// ======================== HAPUS STAN PHOTOBOOTH =======================
// ======================================================================

if (isset($_GET['hapus_photobooth'])){
    
    $id_photobooth_stan  = enkripsiDekripsi(antiSQLi($_GET['eid']), 'dekripsi');
    
    $db->begin_transaction();

    $query = "UPDATE photobooth_stan SET status_remove_photobooth='Y' WHERE id_photobooth_stan ='$id_photobooth_stan' ";
    $sql[] = $db->query($query);

    $sql[] = $db->query("DELETE FROM photobooth_stan_paket WHERE id_photobooth_stan_paket='$id_photobooth_stan' ");
    

    if(in_array(false, $sql)){
        $db->rollback();
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'admin-super.php?page=photobooth&action=kelola');
        exit();
    }else{
        $db->commit();
        $_SESSION['notifikasi']['success'] = "Tiket berhasil dihapus";
        header('Location: '.base_url().'admin-super.php?page=photobooth&action=kelola');
        exit();
    }
    
}





// ======================================================================
// ====================== AKTIFKAN STAN PHOTOBOOTH ======================
// ======================================================================

if(isset($_GET['aktifkan_stan_photobooth'])){
    
    $id_photobooth_stan = enkripsiDekripsi(antiSQLi($_GET['aktifkan_stan_photobooth']), 'dekripsi');
    $sql = $db->query("UPDATE photobooth_stan SET status_display_photobooth='Y' WHERE id_photobooth_stan='$id_photobooth_stan'");
    
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
    $sql = $db->query("UPDATE photobooth_stan SET status_display_photobooth='N' WHERE id_photobooth_stan='$id_photobooth_stan'");
    
    if($sql){
        echo "berhasil";
        exit();
    }else{
        echo "gagal";
        exit();
    }
    
}