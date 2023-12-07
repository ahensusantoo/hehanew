<?php 
    require_once('../../templates/koneksi.php');
    
    $id_merchant_transaksi           = $_GET['id_merchant_transaksi']; 
    $kd_merchant                     = $_GET['kd_merchant']; 
    
    
    $merchant_transaksi_detail = "SELECT * 
                                    FROM merchant_transaksi_detail A 
                                    LEFT JOIN merchant_transaksi B ON A.kd_merchant_transaksi = B.id_merchant_transaksi
                                    WHERE kd_merchant_transaksi = '$id_merchant_transaksi'
                                    	AND status_transaksi_detail = '2'";
    $sql = $db->query($merchant_transaksi_detail)->fetch_all(MYSQLI_ASSOC);
    
    foreach($sql as $key => $val) {
        $query = " CALL hapus_keranjang('$val[kd_merchant]', '$val[kd_merchant_employee]', '$val[kd_merchant_produk]', '$val[id_merchant_transaksi_detail]', '$val[kd_merchant_transaksi]') ";
        $sql = $db->query($query);
    }
    
    $update_merchant_transaksi = "UPDATE merchant_transaksi SET status_transaksi ='3'
                                    WHERE kd_merchant = '$kd_merchant'
                                        AND id_merchant_transaksi = '$id_merchant_transaksi'";
                                        
    $sql2 = $db->query($update_merchant_transaksi);
    
    if($sql2){
                    $respon['pesan'] = "Berhasil!\nKlik `Mengerti` untuk menutup pesan ini";
                    echo json_encode($respon);
    }else{
       http_response_code(400);
                    $respon['pesan'] = "gagal!\nKlik `Mengerti` untuk menutup pesan ini";
                    echo json_encode($respon);
    }
    
mysqli_close($db);

 ?>    