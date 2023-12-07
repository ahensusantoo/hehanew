<?php 
require_once('../../templates/koneksi.php');

	$idmerchant             = $_POST['idmerchant'];
	$kd_merchant_employee   = $_POST['id_merchant_employee'];
	$kd_merchant_produk     = $_POST['id_merchant_produk'];
	$id_merchant_transaksi  = $_POST['id_merchant_transaksi'];
	$catatan_pesanan        = $_POST['catatan_pesanan'];
	$jumlah_beli            = $_POST['jumlah_beli'];
	$diskon                 = $_POST['diskon'];
	$harga_diskon           = $_POST['harga_diskon'];
	    
	    if(empty($id_merchant_transaksi)){
        
        	$sql	= "SELECT * FROM merchant_transaksi_detail
            WHERE kd_merchant_employee ='$kd_merchant_employee' AND kd_merchant ='$idmerchant' AND kd_merchant_produk ='$kd_merchant_produk' AND kd_merchant_transaksi = '' AND status_transaksi_detail ='1'"; 
        	$result = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
        	
        	if(empty($result[0])) {
        
                	
                $query = mysqli_query($db, "CALL tambah_keranjang_nanti('$idmerchant', '$kd_merchant_employee', '$kd_merchant_produk', '$id_merchant_transaksi', '$catatan_pesanan', '$jumlah_beli', '$diskon', '$harga_diskon')");
                
                if ($query){
                	$respon['pesan'] = "Barang berhasil ditambahkan";
                	die(json_encode($respon));
                } else{ 
                    http_response_code(400);
                    $respon['kode'] = "1";
                    $respon['pesan'] = "Gagal menambahkan barang baru!\nKlik `Mengerti` untuk menutup pesan ini";
                	die(json_encode($respon)); 
                }
        	}else{
                http_response_code(400);
                $respon['kode'] = "2";
                $respon['pesan'] = "Barang sudah masuk keranjang, lakukan perubahan data di keranjang bila terjadi perubahan";
                die(json_encode($respon)); 
                    
        	}
	    }else{
	        
	        $sql	= "SELECT * FROM merchant_transaksi_detail
            WHERE kd_merchant_employee ='$kd_merchant_employee' AND kd_merchant ='$idmerchant' AND kd_merchant_produk ='$kd_merchant_produk' AND kd_merchant_transaksi = '$id_merchant_transaksi' AND status_transaksi_detail ='1'"; 
        	$result = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
        	
        	if(empty($result[0])) {
        
                	
                $query = mysqli_query($db, "CALL tambah_keranjang_nanti('$idmerchant', '$kd_merchant_employee', '$kd_merchant_produk', '$id_merchant_transaksi', '$catatan_pesanan', '$jumlah_beli', '$diskon', '$harga_diskon')");
                
                if ($query){
                	$respon['pesan'] = "Barang berhasil ditambahkan";
                	die(json_encode($respon));
                } else{ 
                    http_response_code(400);
                    $respon['kode'] = "1";
                    $respon['pesan'] = "Barang sudah masuk transaksi, lakukan perubahan data di detail transaksi bila terjadi perubahan";
                	die(json_encode($respon)); 
                }
        	}else{
                http_response_code(400);
                $respon['kode'] = "2";
                $respon['pesan'] = "Barang sudah masuk keranjang, lakukan perubahan data di keranjang bila terjadi perubahan";
                die(json_encode($respon)); 
                    
        	}
	    }
        	
	

mysqli_close($db);
?>