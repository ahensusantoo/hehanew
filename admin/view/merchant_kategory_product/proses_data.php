<?php

// echo "<pre>";
// echo print_r($_POST);
// die();

require_once '../../templates/koneksi.php';
sessionLoginMerchantEmployee();

// ======================================================================
// ======================= TAMBAH =======================================
// ======================================================================
$sess_kd_merchant = $_SESSION['kd_merchant'];
if (isset($_POST['tambah_merchant_kategory_product'])){

    $nama_kategori = addslashes($_POST['nama_kategori']);
    $kode_kategori = addslashes($_POST['kode_kategori']);
    $status_tampil = addslashes($_POST['status_tampil']);
    
    $cek_kode_kategori = $db->query("SELECT * 
                                    FROM merchant_kategori_produk 
                                    WHERE kode_kategori = '".strtoupper($kode_kategori)."'
                                        AND kd_merchant = '$sess_kd_merchant' ")->num_rows;

    $_SESSION['setvalue']['kd_merchant'] = $kd_merchant;
    $_SESSION['setvalue']['nama_merchant'] = $nama_merchant;
    $_SESSION['setvalue']['kode_merchant'] = $kode_merchant;
       
    if($cek_kode_kategori > 0){ 
        $_SESSION['notifikasi']['fail'] = "Kode ".$kode_kategori." Sudah Digunakan";
        header('Location: '.base_url().'merchant.php?page=kategory_product&action=tambah');
        exit();
    }
    else {
        $id_merchant_kategori_produk = createID('id_merchant_kategori_produk', 'merchant_kategori_produk', 'KG');
        $sess_kd_merchant = $_SESSION['kd_merchant'];
        $query = "INSERT 
                    INTO merchant_kategori_produk (id_merchant_kategori_produk,kd_merchant, kode_kategori, nama_kategori, status_aktif_kategori) 
                    VALUES ('$id_merchant_kategori_produk', '$sess_kd_merchant', '".strtoupper($kode_kategori)."', '$nama_kategori', '$status_tampil')";
        $sql = $db->query($query);
    // var_dump($sql);die();
    // echo $query;
    }
    
    if($sql){
        //Jika Berhasil
        $_SESSION['notifikasi']['success'] = "Nama Kategory ".$nama_kategori." berhasil ditambahkan";
        header('Location: '.base_url().'merchant.php?page=kategory_product&action=kelola');
        exit();
    }else{
        //Jika Gagal
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'merchant.php?page=kategory_product&action=tambah');
        exit();
    }    
}



// ======================================================================
// ======================== EDIT ========================================
// ======================================================================

if(isset($_POST['edit_merchant_kategory_product'])){

    $id_merchant_kategori_produk = addslashes(enkripsiDekripsi($_POST['id_merchant_kategori_produk'],'dekripsi'));
    $nama_kategori = addslashes($_POST['nama_kategori']);
    $kode_kategori = addslashes($_POST['kode_kategori']);
    $status_tampil = addslashes($_POST['status_tampil']);
    
    $sess_kd_merchant = $_SESSION['kd_merchant'];
    $cek_kode_kategori=$db->query("SELECT * FROM merchant_kategori_produk 
                                    WHERE kode_kategori ='".strtoupper($kode_kategori)."' 
                                        AND id_merchant_kategori_produk!='$id_merchant_kategori_produk'
                                            AND kd_merchant = '$sess_kd_merchant' ")->num_rows;
    if($cek_kode_kategori > 0 ){
        $_SESSION['notifikasi']['fail'] = "Kode Kategory ".$kode_kategori." Sudah Digunakan";
        header('Location: '.base_url().'merchant.php?page=kategory_product&action=tambah');
        exit();   
    }else{
        $sess_kd_merchant = $_SESSION['kd_merchant'];
        $query = "  UPDATE merchant_kategori_produk 
                    SET kd_merchant= '$sess_kd_merchant' ,nama_kategori='$nama_kategori', kode_kategori='".strtoupper($kode_kategori)."', status_aktif_kategori='$status_tampil'  
                    WHERE id_merchant_kategori_produk='$id_merchant_kategori_produk'";
        $sql = $db->query($query);
    }

    if($sql){
        $_SESSION['notifikasi']['success'] = "Nama Kategory ".$nama_kategori." Di Update";
        header('Location: '.base_url().'merchant.php?page=kategory_product&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'merchant.php?page=kategory_product&action=edit');
        exit();
    }
}


// ======================================================================
// =======================  HAPUS  ======================================
// ======================================================================


if(isset($_GET['kategory_product_hapus'])){

    $id = enkripsiDekripsi($_GET['id'],'dekripsi');
    $nama_kategori = addslashes($_GET['nama_kategori']);
   
    $query = "  UPDATE merchant_kategori_produk 
                SET status_remove_kategori='Y',
                status_aktif_kategori='N'
                WHERE id_merchant_kategori_produk='$id'
                    AND kd_merchant = '$sess_kd_merchant' ";
    // echo $query;
    $sql = $db->query($query);

    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Kategory".$nama_kategori ."Berhasil Hapus";
        header('Location: '.base_url().'merchant.php?page=kategory_product&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'merchant.php?page=kategory_product&action=kelola');
        exit();
    }
}




