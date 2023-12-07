<?php

require_once '../../templates/koneksi.php';


// ======================================================================
// ======================= TAMBAH VOUCHER MERCHANT =========================
// ======================================================================

if (isset($_POST['tambah_voucher_merchant'])){
    $id_merchant = antiSQLi($_POST['id_merchant'])==''?'null': $_POST['id_merchant'] ;
    $nama_voucher = antiSQLi($_POST['nama_voucher']);
    $kode_voucher = antiSQLi($_POST['kode_voucher']);
    $deskripsi_voucher = antiSQLi($_POST['deskripsi_voucher']);
    $status_potongan = antiSQLi($_POST['status_potongan']);
    $potongan_voucher = preg_replace("/[^0-9]/", "", antiSQLi($_POST['potongan_voucher']) );
    $daterange = antiSQLi($_POST['daterange']);
    $min_transaksi = preg_replace("/[^0-9]/", "", antiSQLi($_POST['min_transaksi']) );
    $max_potongan = preg_replace("/[^0-9]/", "", antiSQLi($_POST['max_potongan']) );
    $start_tgl = date_format(date_create(substr($daterange,0,10)),"Y-m-d");
    $end_tgl = date_format(date_create(substr($daterange,13,10)),"Y-m-d");
    
    $start_jam = antiSQLi($_POST['start_jam']);
    $end_jam = antiSQLi($_POST['end_jam']);
    
    $max_pengguna = preg_replace("/[^0-9]/", "", antiSQLi($_POST['max_pengguna']) );
    
    // var_dump($_POST);die();
    $id_merchant = enkripsiDekripsi($id_merchant, 'dekripsi');
    
    $cek_kode_voucher=$db->query("SELECT COUNT(*) as jml
                                FROM voucher_merchant 
                                WHERE kode_voucher = '$kode_voucher'
				                    AND status_rmv_voucher ='N' ")->fetch_assoc();
// 	var_dump($cek_kode_voucher);die();
	
	if ($cek_kode_voucher['jml'] > 0 ) {
	    $_SESSION['notifikasi']['fail'] = "Gagal, Voucher '".$kode_voucher."' Sudah Digunakan";
        header('Location: '.base_url().'admin-super.php?page=vouchermerchant&action=tambah');
        exit();
	}else if($min_transaksi < $max_potongan){
	    $_SESSION['notifikasi']['fail'] = "Minamal Transaksi Harus Lebih Besar Dari Maximal Potongan!!!";
        header('Location: '.base_url().'admin-super.php?page=vouchermerchant&action=tambah');
        exit();
	}else{
        $id_voucher = createID('id_voucher', 'voucher_merchant', 'VM');
        $query = "INSERT INTO voucher_merchant SET id_voucher='$id_voucher', kode_voucher='$kode_voucher', min_transaksi='$min_transaksi', max_potongan='$max_potongan', kd_merchant = '$id_merchant', nama_voucher='$nama_voucher', deskripsi_voucher='$deskripsi_voucher', status_potongan='$status_potongan', potongan_voucher='$potongan_voucher', start_tgl='$start_tgl', end_tgl='$end_tgl', start_jam='$start_jam', end_jam='$end_jam', max_pengguna='$max_pengguna'";
        $sql = $db->query($query);
        
        
        if($sql){
            $_SESSION['notifikasi']['success'] = "Voucher '".$nama_voucher."' berhasil ditambahkan";
            header('Location: '.base_url().'admin-super.php?page=vouchermerchant&action=kelola');
            exit();
        }else{
            $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
            header('Location: '.base_url().'admin-super.php?page=vouchermerchant&action=tambah');
            exit();
        }
	}
    
    
}



// ======================================================================
// ======================== EDIT VOUCHER TIKET ==========================
// ======================================================================

if (isset($_POST['edit_voucher'])){
    // var_dump($_POST);die();
    $id = enkripsiDekripsi(antiSQLi($_POST['id']), 'dekripsi');
    $id_merchant = enkripsiDekripsi(antiSQLi($_POST['id_merchant']), 'dekripsi');
    $nama_voucher = antiSQLi($_POST['nama_voucher']);
    $kode_voucher = antiSQLi($_POST['kode_voucher']);
    $min_transaksi = preg_replace("/[^0-9]/", "", antiSQLi($_POST['min_transaksi']) );
    $max_potongan = preg_replace("/[^0-9]/", "", antiSQLi($_POST['max_potongan']) );
    $deskripsi_voucher = antiSQLi($_POST['deskripsi_voucher']);
    $status_potongan = antiSQLi($_POST['status_potongan']);
    $potongan_voucher = preg_replace("/[^0-9]/", "", antiSQLi($_POST['potongan_voucher']) );
    $daterange = antiSQLi($_POST['daterange']);
    
    $start_tgl = date_format(date_create(substr($daterange,0,10)),"Y-m-d");
    $end_tgl = date_format(date_create(substr($daterange,13,10)),"Y-m-d");
    
    $start_jam = antiSQLi($_POST['start_jam']);
    $end_jam = antiSQLi($_POST['end_jam']);
    
    $max_pengguna = preg_replace("/[^0-9]/", "", antiSQLi($_POST['max_pengguna']) );
    // var_dump($cek_kode_voucher);die();
    $cek_kode_voucher=$db->query("SELECT COUNT(*) as jml
                                FROM voucher_merchant 
                                WHERE kode_voucher = '$kode_voucher'
				                    AND status_rmv_voucher ='N'
				                    AND id_voucher != '$id' ")->fetch_assoc();
	
	if ($cek_kode_voucher['jml'] > 0 ) {
	    $_SESSION['notifikasi']['fail'] = "Gagal, Voucher '".$kode_voucher."' Sudah Digunakan";
        header('Location: '.base_url().'admin-super.php?page=vouchermerchant&action=edit&eid='.$_POST['id']);
        exit();
	}else if($min_transaksi < $max_potongan){
	    $_SESSION['notifikasi']['fail'] = "Minamal Transaksi Harus Lebih Besar Dari Maximal Potongan!!!";
        header('Location: '.base_url().'admin-super.php?page=vouchermerchant&action=edit&eid='.$_POST['id']);
        exit();
	}else{
        $query = "UPDATE voucher_merchant SET kode_voucher='$kode_voucher', kd_merchant='$id_merchant', nama_voucher='$nama_voucher', min_transaksi='$min_transaksi', max_potongan='$max_potongan', deskripsi_voucher='$deskripsi_voucher', status_potongan='$status_potongan', potongan_voucher='$potongan_voucher', start_tgl='$start_tgl', end_tgl='$end_tgl', start_jam='$start_jam', end_jam='$end_jam', max_pengguna='$max_pengguna' WHERE id_voucher='$id'";
        $sql = $db->query($query);
        
        
        if($sql){
            $_SESSION['notifikasi']['success'] = "Voucher '".$nama_voucher."' berhasil Diubah";
            header('Location: '.base_url().'admin-super.php?page=vouchermerchant&action=kelola');
            exit();
        }else{
            $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
            header('Location: '.base_url().'admin-super.php?page=vouchermerchant&action=edit&eid='.$_POST['id']);
            exit();
        }
	}
    
    
}



// ======================================================================
// ======================== EDIT VOUCHER TIKET ==========================
// ======================================================================

if (isset($_GET['hapus_voucher'])){
    
    $id = enkripsiDekripsi(antiSQLi($_GET['id']), 'dekripsi');
    
    $query = "UPDATE voucher_merchant SET status_rmv_voucher='Y' WHERE id_voucher='$id' ";
    $sql = $db->query($query);
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Voucher berhasil dihapus";
        header('Location: '.base_url().'admin-super.php?page=vouchermerchant&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'admin-super.php?page=vouchermerchant&action=kelola');
        exit();
    }
    
    
}