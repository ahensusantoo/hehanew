<?php 
require_once('../../templates/koneksi.php');

	$id_merchant_produk     = $_POST['id_merchant_produk'];
	
	$sql	= "SELECT * FROM merchant_produk WHERE id_merchant_produk  = '$id_merchant_produk'"; 
	$result = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
	
	    
        if($result[0]['status_remove_produk'] == 'N') {

        	$query = mysqli_query($db, "UPDATE merchant_produk SET status_remove_produk = 'Y' WHERE id_merchant_produk = '$id_merchant_produk'");
        	
        	if ($query){
        		$respon['pesan'] = "Barang ".$result[0]['nama_produk']." berhasil dinonaktifkan";
        		die(json_encode($respon));
        	} else{ 
        	    http_response_code(400);
                $respon['pesan'] = "Gagal menonaktifkan barang!\nKlik `Mengerti` untuk menutup pesan ini";
        		die(json_encode($respon)); 
        	}
        }else{
            $query = mysqli_query($db, "UPDATE merchant_produk SET status_remove_produk = 'N' WHERE id_merchant_produk = '$id_merchant_produk'");
        	
        	if ($query){
        		$respon['pesan'] = "Barang ".$result[0]['nama_produk']." berhasil diaktifkan";
        		die(json_encode($respon));
        	} else{ 
        	    http_response_code(400);
                $respon['pesan'] = "Gagal menonaktifkan barang!\nKlik `Mengerti` untuk menutup pesan ini";
        		die(json_encode($respon)); 
        	}
        }

mysqli_close($db);
?>