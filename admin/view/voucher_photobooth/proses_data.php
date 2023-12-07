<?php

require_once '../../templates/koneksi.php';


// ======================================================================
// ======================= TAMBAH VOUCHER TIKET =========================
// ======================================================================

if (isset($_POST['tambah_voucher_tiket'])){
    
    $nama_voucher = antiSQLi($_POST['nama_voucher']);
    $kode_voucher = antiSQLi($_POST['kode_voucher']);
    $deskripsi_voucher = antiSQLi($_POST['deskripsi_voucher']);
    $status_potongan = antiSQLi($_POST['status_potongan']);
    $potongan_voucher = preg_replace("/[^0-9]/", "", antiSQLi($_POST['potongan_voucher']) );
    $daterange = antiSQLi($_POST['daterange']);
    
    $start_tgl = date_format(date_create(substr($daterange,0,10)),"Y-m-d");
    $end_tgl = date_format(date_create(substr($daterange,13,10)),"Y-m-d");
    
    $start_jam = antiSQLi($_POST['start_jam']);
    $end_jam = antiSQLi($_POST['end_jam']);
    
    $max_pengguna = preg_replace("/[^0-9]/", "", antiSQLi($_POST['max_pengguna']) );
    
    $id_voucher = createID('id_voucher', 'voucher_photobooth', 'VT');
    $query = "INSERT INTO voucher_photobooth SET id_voucher='$id_voucher', kode_voucher='$kode_voucher', nama_voucher='$nama_voucher', deskripsi_voucher='$deskripsi_voucher', status_potongan='$status_potongan', potongan_voucher='$potongan_voucher', start_tgl='$start_tgl', end_tgl='$end_tgl', start_jam='$start_jam', end_jam='$end_jam', max_pengguna='$max_pengguna'";
    $sql = $db->query($query);
    
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Voucher '".$nama_voucher."' berhasil ditambahkan";
        header('Location: '.base_url().'admin-super.php?page=voucherphotobooth&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'admin-super.php?page=voucherphotobooth&action=kelola');
        exit();
    }
    
    
}



// ======================================================================
// ======================== EDIT VOUCHER TIKET ==========================
// ======================================================================

if (isset($_POST['edit_voucher_tiket'])){
    
    $id = enkripsiDekripsi(antiSQLi($_POST['id']), 'dekripsi');
    $nama_voucher = antiSQLi($_POST['nama_voucher']);
    $kode_voucher = antiSQLi($_POST['kode_voucher']);
    $deskripsi_voucher = antiSQLi($_POST['deskripsi_voucher']);
    $status_potongan = antiSQLi($_POST['status_potongan']);
    $potongan_voucher = preg_replace("/[^0-9]/", "", antiSQLi($_POST['potongan_voucher']) );
    $daterange = antiSQLi($_POST['daterange']);
    
    $start_tgl = date_format(date_create(substr($daterange,0,10)),"Y-m-d");
    $end_tgl = date_format(date_create(substr($daterange,13,10)),"Y-m-d");
    
    $start_jam = antiSQLi($_POST['start_jam']);
    $end_jam = antiSQLi($_POST['end_jam']);
    
    $max_pengguna = preg_replace("/[^0-9]/", "", antiSQLi($_POST['max_pengguna']) );
    
    $query = "UPDATE voucher_photobooth SET kode_voucher='$kode_voucher', nama_voucher='$nama_voucher', deskripsi_voucher='$deskripsi_voucher', status_potongan='$status_potongan', potongan_voucher='$potongan_voucher', start_tgl='$start_tgl', end_tgl='$end_tgl', start_jam='$start_jam', end_jam='$end_jam', max_pengguna='$max_pengguna' WHERE id_voucher='$id'";
    $sql = $db->query($query);
    
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Voucher '".$nama_voucher."' berhasil ditambahkan";
        header('Location: '.base_url().'admin-super.php?page=voucherphotobooth&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'admin-super.php?page=voucherphotobooth&action=kelola');
        exit();
    }
    
    
}



// ======================================================================
// ======================== EDIT VOUCHER TIKET ==========================
// ======================================================================

if (isset($_GET['hapus_voucher'])){
    
    $id = enkripsiDekripsi(antiSQLi($_GET['id']), 'dekripsi');
    
    $query = "UPDATE voucher_photobooth SET status_rmv_voucher='Y' WHERE id_voucher='$id'";
    $sql = $db->query($query);
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Voucher berhasil dihapus";
        header('Location: '.base_url().'admin-super.php?page=voucherphotobooth&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'admin-super.php?page=voucherphotobooth&action=kelola');
        exit();
    }
    
    
}