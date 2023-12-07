<?php 
require_once('../../templates/koneksi.php');

	$id_merchant_kategori_produk     = $_POST['id_merchant_kategori_produk'];
	
	$sql	= "SELECT * FROM merchant_kategori_produk WHERE id_merchant_kategori_produk = '$id_merchant_kategori_produk'"; 
	$result = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
	
	    
        if($result[0]['status_aktif_kategori'] == 'Y') {

        	$query = mysqli_query($db, "UPDATE merchant_kategori_produk SET status_aktif_kategori = 'N' WHERE id_merchant_kategori_produk = '$id_merchant_kategori_produk'");
        	
        	if ($query){
        		$respon['pesan'] = "Kategori ".$result[0]['nama_kategori']." berhasil dinonaktifkan";
        		die(json_encode($respon));
        	} else{ 
        	    http_response_code(400);
                $respon['pesan'] = "Gagal merubah kategori baru!\nKlik `Mengerti` untuk menutup pesan ini";
        		die(json_encode($respon)); 
        	}
        }else{
            $query = mysqli_query($db, "UPDATE merchant_kategori_produk SET status_aktif_kategori = 'Y' WHERE id_merchant_kategori_produk = '$id_merchant_kategori_produk'");
        	
        	if ($query){
        		$respon['pesan'] = "Kategori ".$result[0]['nama_kategori']." berhasil diaktifkan";
        		die(json_encode($respon));
        	} else{ 
        	    http_response_code(400);
                $respon['pesan'] = "Gagal merubah kategori baru!\nKlik `Mengerti` untuk menutup pesan ini";
        		die(json_encode($respon)); 
        	}
        }

mysqli_close($db);
?>