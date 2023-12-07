<?php

require_once '../../templates/koneksi.php';


// ======================================================================
// ========================= TAMBAH JENIS TIKET =========================
// ======================================================================

if (isset($_POST['tambah_jenis_tiket'])){
    
    $nama_tiket = antiSQLi($_POST['nama_tiket']);
    $start_hari = antiSQLi($_POST['start_hari']);
    $end_hari = antiSQLi($_POST['end_hari']);
    $start_jam = antiSQLi($_POST['start_jam']);
    $end_jam = antiSQLi($_POST['end_jam']);
    $harga_tiket = preg_replace("/[^0-9]/", "", antiSQLi($_POST['harga_tiket']) );
    $status_hari_linur = antiSQLi($_POST['status_hari_linur']);
    
    $id_jenis_tiket = createID('id_jenis_tiket', 'jenis_tiket', 'JT');
    $query = "INSERT INTO jenis_tiket (id_jenis_tiket, nama_jenis_tiket, start_hari, end_hari, start_jam, end_jam, harga_tiket, status_hari_libur) VALUES ('$id_jenis_tiket', '$nama_tiket', '$start_hari', '$end_hari', '$start_jam', '$end_jam', '$harga_tiket', '$status_hari_linur')";
    $sql = $db->query($query);
    
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Tiket '".$nama_tiket."' berhasil ditambahkan";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=tiket&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=tiket&action=kelola');
        exit();
    }
    
    
}


// ======================================================================
// ========================== UBAH JENIS TIKET ==========================
// ======================================================================

if (isset($_POST['ubah_jenis_tiket'])){
    
    $id_jenis_tiket = enkripsiDekripsi(antiSQLi($_POST['id_jenis_tiket']), 'dekripsi');
    $nama_tiket = antiSQLi($_POST['nama_tiket']);
    $start_hari = antiSQLi($_POST['start_hari']);
    $end_hari = antiSQLi($_POST['end_hari']);
    $start_jam = antiSQLi($_POST['start_jam']);
    $end_jam = antiSQLi($_POST['end_jam']);
    $status_hari_linur = antiSQLi($_POST['status_hari_libur']);
    if(isset($_POST['status_aktif'])){
        $status_display_tiket = "Y";
    }else{
        $status_display_tiket = "N";
    }
    
    $query = "UPDATE jenis_tiket SET nama_jenis_tiket='$nama_tiket', start_hari='$start_hari', end_hari='$end_hari', start_jam='$start_jam', end_jam='$end_jam', status_hari_libur='$status_hari_linur', status_display_tiket='$status_display_tiket' WHERE id_jenis_tiket='$id_jenis_tiket'";
    $sql = $db->query($query);
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Tiket '".$nama_tiket."' berhasil diubah";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=tiket&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem".$query;
        header('Location: '.base_url().$_SESSION['base_php'].'?page=tiket&action=kelola');
        exit();
    }
    
}




// ======================================================================
// ========================== HAPUS JENIS TIKET =========================
// ======================================================================

if (isset($_GET['hapus_jenis_tiket'])){
    
    $id_jenis_tiket = enkripsiDekripsi(antiSQLi($_GET['eid']), 'dekripsi');
    
    $query = "UPDATE jenis_tiket SET status_remove_tiket='Y' WHERE id_jenis_tiket='$id_jenis_tiket' ";
    $sql = $db->query($query);
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Tiket berhasil dihapus";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=tiket&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem".$query;
        header('Location: '.base_url().$_SESSION['base_php'].'?page=tiket&action=kelola');
        exit();
    }
    
}






// ======================================================================
// ========================= TAMBAH HARI LIBUR ==========================
// ======================================================================

if (isset($_POST['tambah_hari_libur'])){
    
    $tahun = antiSQLi($_POST['tahun']);
    $bulan = antiSQLi($_POST['bulan']);
    $hari_libur = implode("|", $_POST['hari_libur']);
    $tahun_bulan = $tahun."-".$bulan;
    
    
    $query = "INSERT INTO hari_libur (tahun_bulan, hari_libur) VALUES ('$tahun_bulan', '$hari_libur')";
    $sql = $db->query($query);
    
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Hari Libur pada bulan ke-".$bulan." berhasil ditambahkan";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=tiket&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=tiket&action=kelola');
        exit();
    }
    
    
}


// ======================================================================
// =========================== UBAH HARI LIBUR ==========================
// ======================================================================

if (isset($_POST['ubah_hari_libur'])){
    
    $tahun_bulan = antiSQLi($_POST['tahun_bulan']);
    $hari_libur = implode("|", $_POST['hari_libur']);
    
    
    $query = "UPDATE hari_libur SET hari_libur='$hari_libur' WHERE tahun_bulan='$tahun_bulan'";
    $sql = $db->query($query);
    
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Hari Libur pada bulan ke-".$tahun_bulan." berhasil diubah";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=tiket&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=tiket&action=kelola');
        exit();
    }
    
    
}



// ======================================================================
// ===================== AKTIFKAN JENIS PEMBAYARAN ======================
// ======================================================================

if(isset($_GET['aktifkan_jenis_tiket'])){
    
    $id_jenis_tiket = enkripsiDekripsi(antiSQLi($_GET['aktifkan_jenis_tiket']), 'dekripsi');
    $sql = $db->query("UPDATE jenis_tiket SET status_display_tiket='Y' WHERE id_jenis_tiket='$id_jenis_tiket'");
    
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

if(isset($_GET['nonaktifkan_jenis_tiket'])){
    
    $id_jenis_tiket = enkripsiDekripsi(antiSQLi($_GET['nonaktifkan_jenis_tiket']), 'dekripsi');
    $sql = $db->query("UPDATE jenis_tiket SET status_display_tiket='N' WHERE id_jenis_tiket='$id_jenis_tiket'");
    
    if($sql){
        echo "berhasil";
        exit();
    }else{
        echo "gagal";
        exit();
    }
    
}