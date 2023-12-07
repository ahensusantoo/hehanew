<?php 
require_once('../../templates/koneksi.php');

	$kd_merchant            = $_POST['id_merchant'];
	$kd_merchant_employee   = $_POST['id_merchant_employee'];
	$kd_merchant_produk     = $_POST['id_merchant_produk'];
	$id_merchant_transaksi  = $_POST['id_merchant_transaksi'];
	$id_transaksi           = $_POST['id_transaksi'];
	    
	
        
        	$sql	= "SELECT * FROM merchant_transaksi_detail
            WHERE kd_merchant_employee ='$kd_merchant_employee' AND kd_merchant ='$kd_merchant' AND kd_merchant_produk ='$kd_merchant_produk' AND id_merchant_transaksi_detail = '$id_merchant_transaksi'"; 
        	$result = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
        	
        
        	
        	if(isset($result[0])) {
        
                	
                $query = mysqli_query($db, "CALL hapus_keranjang_nanti('$kd_merchant', '$kd_merchant_employee', '$kd_merchant_produk', '$id_merchant_transaksi', '$id_transaksi')");
                
                if ($query){
                	$respon['pesan'] = "Barang berhasil dihapus";
                	die(json_encode($respon));
                } else{ 
                    http_response_code(400);
                    $respon['status'] = "1";
                    $respon['pesan'] = "Gagal menghapus barang baru!\nKlik `Mengerti` untuk menutup pesan ini";
                	die(json_encode($respon)); 
                }
        	}else{
                http_response_code(400);
                $respon['status'] = "2";
                $respon['pesan'] = "Barang sudah terhapus! Klik mengerti untuk menutup pesan ini";
                die(json_encode($respon)); 
                    
        	}
	    
        	
	

mysqli_close($db);
?>