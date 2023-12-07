<?php

// echo "<pre>";
// echo print_r($_POST);
// die();

require_once '../../templates/koneksi.php';
sessionLoginMerchantEmployee();

// ==============================================================
// ======================= TAMBAH ===============================
// ==============================================================

if(isset($_POST['mutasi_masuk_gc'])){
    
    
    //$id_merchant = enkripsiDekripsi($_POST['merchant'],'dekripsi');
    $id_employee = $_SESSION['id_merchant_employee'];
    $keterangan_kepala = antiSQLi($_POST['keterangan_kepala']);
    
    $db->begin_transaction();
    
        $id_kepala = createID('id_merchant_mutasi_stok', 'merchant_mutasi_stok', 'MS');
        $sql[] = $db->query("INSERT INTO `merchant_mutasi_stok`(`id_merchant_mutasi_stok`, `jenis_mutasi`, `kd_merchant`, `kd_merchant_employee`, `keterangan_mutasi`) 
            VALUES ('$id_kepala', '1', '$_SESSION[kd_merchant]', '$id_employee', '$keterangan_kepala')");
        
        $gagal = 0;
    	$notif_gagal = "";
    	
    // 	var_dump($_POST['produk_dimutasi']); die();
    // 	var_dump($value['kode_produk']); die();
          
        foreach ($_POST['produk_dimutasi'] as $key => $value) {
            // var_dump($value['kode_produk']); die();
            $id_produk = enkripsiDekripsi($value['id'], 'dekripsi');
            if($value['harga_jual'] < $value['harga_beli'] ){
    		    $db->rollback();
        // 		$notif_gagal = $notif_gagal."- Harga Beli Produk Harga Jual Lebih Kecil Dari Harga Beli !!!<br>";
    		  //  $gagal = 1;
        		$_SESSION['notifikasi']['fail'] = "Gagal Mutasi Semua Produk, pastikan harga jual lebih tinggi dari harga beli";
                header('Location: '.base_url().'merchant.php?page=mutasi_stok&action=masuk');
    		    exit();
            }
            
            $query = " CALL tambah_mutasi('$id_kepala', '$id_produk', '$value[jml]', '$_SESSION[kd_merchant]', '$id_employee', '$value[harga_beli]', '$value[ket]', '1', '$value[harga_jual]') ";
    
            $sql[] = $db->query($query);
            
            // tambah_mutasi('$id_mutasi', '$id_produk', '$value[jml]', '$id_merchant', '$id_employee', '$data_lama['harga_beli']', '$value[ket]', '1', '$data_lama['harga_produk']')
            
        }
        
        if($gagal == 1){
            $db->rollback();
            $_SESSION['notifikasi']['fail'] = $notif_gagal;
            header('Location: '.base_url().'merchant.php?page=mutasi_stok&action=masuk');
            exit();
        }
    
    
    if(in_array(false, $sql) OR in_array(0, $sql)){
		$db->rollback();
		$_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'merchant.php?page=mutasi_stok&action=masuk');
        exit();
	}else{
		$db->commit();
		$_SESSION['notifikasi']['success'] = "Data Mutasi Berhasil Disimpan";
        header('Location: '.base_url().'merchant.php?page=mutasi_stok&action=masuk');
        exit();
	}

    
}




// ======================================================================
// =============================  MUTASI KELUAR  ========================
// ======================================================================

if(isset($_POST['mutasi_keluar_gc'])){
    
    
    $id_merchant = enkripsiDekripsi($_POST['merchant'],'dekripsi');
    $id_employee = $_SESSION['id_merchant_employee'];
    $keterangan_kepala = antiSQLi($_POST['keterangan_kepala']);
    
    $db->begin_transaction();
    
    $id_kepala = createID('id_merchant_mutasi_stok', 'merchant_mutasi_stok', 'MS');
    $sql[] = $db->query("INSERT INTO `merchant_mutasi_stok`(`id_merchant_mutasi_stok`, `jenis_mutasi`, `kd_merchant`, `kd_merchant_employee`, `keterangan_mutasi`) 
        VALUES ('$id_kepala', '2', '$_SESSION[kd_merchant]', '$id_employee', '$keterangan_kepala')");
        
    foreach ($_POST['produk_dimutasi'] as $key => $value) {
        
        $id_produk = enkripsiDekripsi($value['id'], 'dekripsi');
        $data_lama = $db->query("SELECT * FROM view_merchant_produk WHERE id_merchant_produk='$id_produk' AND status_remove_produk='N' ")->fetch_assoc();
        
        if(!isset($data_lama)){
            $db->rollback();
    		$_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
            header('Location: '.base_url().'merchant.php?page=mutasi_stok&action=keluar');
            exit();
        }
        
        $query = " CALL tambah_mutasi('$id_kepala', '$id_produk', '$value[jml]', '$_SESSION[kd_merchant]', '$id_employee', '$data_lama[harga_beli]', '$value[ket]', '2', '$data_lama[harga_produk]') ";

        $sql[] = $db->query($query);
        
        // tambah_mutasi('$id_mutasi', '$id_produk', '$value[jml]', '$id_merchant', '$id_employee', '$data_lama['harga_beli']', '$value[ket]', '2', '$data_lama['harga_produk']')
        
    }
    
    
    if(in_array(false, $sql) OR in_array(0, $sql)){
		$db->rollback();
		$_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'merchant.php?page=mutasi_stok&action=keluar');
        exit();
	}else{
		$db->commit();
		$_SESSION['notifikasi']['success'] = "Data Mutasi Berhasil Disimpan";
        header('Location: '.base_url().'merchant.php?page=mutasi_stok&action=keluar');
        exit();
	}

    
}

?>