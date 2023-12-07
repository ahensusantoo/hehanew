<?php

// echo "<pre>";
// echo print_r($_POST);
// die();

require_once '../../templates/koneksi.php';
sessionLoginMerchantEmployee();


// ======================================================================
// ======================= Cancel Transaksi =============================
// ======================================================================

if (isset($_GET['transaksi_merchant_hapus'])){
    $id_produk = enkripsiDekripsi($_GET['id'],'dekripsi');
    $id_merchant_transaksi_detail = $_GET['id_transaksi_detail'];
    $id_merchant_transaksi = enkripsiDekripsi($_GET['id_merchant_transaksi'],'dekripsi');
    
    $sess_kd_merchant = $_SESSION['kd_merchant'];
    $sess_id_merchant_employee = $_SESSION['id_merchant_employee'];
    
    $hasil_tagihan_nota = $harga_perproduk * $qty ;
    
    $sql = "SELECT * 
             FROM merchant_transaksi_detail
             WHERE kd_merchant_produk = '$id_produk'
                 AND kd_merchant = '$sess_kd_merchant'
                 AND id_merchant_transaksi_detail = '$id_merchant_transaksi_detail'
                 ";
   $data_transaksi = $db->query($sql)->fetch_assoc();
    $harga_perproduk = $data_transaksi['harga_setelah_diskon'];
    $qty = $data_transaksi['jumlah_produk'];
    
    
    
    $hasil_tagihan_nota = $harga_perproduk * $qty ;
    
    $query = " CALL hapus_keranjang('$sess_kd_merchant', '$sess_id_merchant_employee', '$id_produk', '$id_merchant_transaksi_detail', '$id_merchant_transaksi') ";
    $sql = $db->query($query);
    
    //update table merchant_transaksi
    $merchant_transaksi = " UPDATE merchant_transaksi SET jumlah_item = jumlah_item - '$qty', tagihan_nota = tagihan_nota - '$hasil_tagihan_nota'
                WHERE kd_merchant = '$sess_kd_merchant'
                    AND id_merchant_transaksi = '$id_merchant_transaksi'
                ";
    $sql2 = $db->query($merchant_transaksi);

    if($sql){
        $_SESSION['notifikasi']['success'] = "Data Berhasil Dihapus";
        header('Location: '.base_url().'merchant.php?page=transaksi_merchant&action=edit&id='.$_GET['id_merchant_transaksi']);
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'merchant.php?page=transaksi_merchant&action=edit&id='.$_GET['id_merchant_transaksi']);
        exit();
    } 

}


// ======================================================================
// ======================= Cancel ALL Transaksi =========================
// ======================================================================

if (isset($_GET['transaksi_merchant_hapus_all'])){
    $id_merchant_transaksi = enkripsiDekripsi($_GET['id_merchant_transaksi'],'dekripsi');
    $sess_kd_merchant = $_SESSION['kd_merchant']; 
    
    $merchant_transaksi_detail = " SELECT *,
                                    SUM(A.jumlah_produk) as jml_produk, SUM(A.harga_setelah_diskon * jumlah_produk) as total
                                    FROM merchant_transaksi_detail A 
                                    LEFT JOIN merchant_transaksi B ON A.kd_merchant_transaksi = B.id_merchant_transaksi
                                    WHERE A.kd_merchant_transaksi = '$id_merchant_transaksi'
                                    	AND A.status_transaksi_detail = '2'
                                ";
    $sql_transaksi_detail = $db->query($merchant_transaksi_detail)->fetch_all(MYSQLI_ASSOC);
    
    $total_item_awal = $sql_transaksi_detail['0']['jml_produk'];
    $total_nominal_awal = $sql_transaksi_detail['0']['total'];
    
    //$sess = $sql[]['kd_merchant'];
    
    // echo "<pre>";
    // echo print_r( $sql_transaksi_detail['0']['total']);
    // echo die();
    
    $db->begin_transaction();
    
        foreach($sql_transaksi_detail as $key => $val) {
            $query = " CALL hapus_keranjang('$val[kd_merchant]', '$val[kd_merchant_employee]', '$val[kd_merchant_produk]', '$val[id_merchant_transaksi_detail]', '$val[kd_merchant_transaksi]') ";
            $sql = $db->query($query);
        }
        
        $update_merchant_transaksi = " UPDATE merchant_transaksi SET status_transaksi ='3'
                                        WHERE kd_merchant = '$sess_kd_merchant'
                                            AND id_merchant_transaksi = '$id_merchant_transaksi'
                                     ";
        $sql[] = $db->query($update_merchant_transaksi);
        
        $insert_revesi_transaksi = " INSERT INTO merchant_transaksi_revisi SET id_merchant_transaksi_revisi = createID('merchant_transaksi_revisi'), tanggal_revisi =  CURRENT_TIMESTAMP(), keterangan_revisi = '$_POST[keterangan_revisi]', kd_admin='$_SESSION[id_merchant_employee]', kd_transaksi='$id_merchant_transaksi', jenis_revisi='2', jumlah_item_awal='$total_item_awal', jumlah_item_akhir='0', jumlah_nominal_awal='$total_nominal_awal', jumlah_nominal_akhir='0' ";
        $sql[] = $db->query($insert_revesi_transaksi);
        
    if(in_array(false, $sql) OR in_array(0, $sql)){
        $db->rollback();
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'merchant.php?page=transaksi_merchant&action=cancel');
        exit();
    }else{
        $db->commit();
         $_SESSION['notifikasi']['success'] = "Data Berhasil Dihapus Semua";
        header('Location: '.base_url().'merchant.php?page=transaksi_merchant&action=cancel');
        exit();
    }
    
}



// ======================================================================
// ======================= Cancel ALL Transaksi =========================
// ======================================================================
if (isset($_POST['rincian_transaksi'])){
    $id_merchant_transaksi  = enkripsiDekripsi($_POST['id_merchant_transaksi'],'dekripsi');
    $kd_merchant            = $_POST['kd_merchant'];
    
    $transaksi_detail = " SELECT B.nama_produk, A.harga_produk, A.diskon, A.harga_setelah_diskon, C.nama_employee, D.tanggal_revisi, D.tanggal_revisi, D.keterangan_revisi, D.kd_admin, D.jenis_revisi                                  
                            FROM merchant_transaksi_detail A
                            LEFT JOIN merchant_produk B ON A.kd_merchant_produk = B.id_merchant_produk
                            LEFT JOIN merchant_employee C ON A.kd_merchant_employee = C.id_merchant_employee
                            LEFT JOIN merchant_transaksi_revisi D ON A.kd_merchant_transaksi = D.kd_transaksi
                            WHERE A.kd_merchant_transaksi = '$id_merchant_transaksi'
                            	AND A.kd_merchant = '$kd_merchant'
                                ";
    $sql_transaksi_detail = $db->query($transaksi_detail)->fetch_all(MYSQLI_ASSOC);

    echo json_encode($sql_transaksi_detail);
}






