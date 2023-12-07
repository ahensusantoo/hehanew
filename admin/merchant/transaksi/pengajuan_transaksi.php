<?php 
require_once('../../templates/koneksi.php');

	$id_merchant_transaksi            = $_POST['id_merchant_transaksi'];
	    
	
        
        $query = mysqli_query($db, "UPDATE merchant_transaksi SET pengajuan_pembatalan = 'Y' WHERE id_merchant_transaksi = '$id_merchant_transaksi'");
            
        if ($query){
        	$respon['pesan'] = "Berhasil menghapus transaksi! Klik `Mengerti` untuk menutup pesan ini";
        	die(json_encode($respon));
        } else{ 
            http_response_code(400);
            $respon['pesan'] = "Gagal menghapus transaksi! Klik `Mengerti` untuk menutup pesan ini";
        	die(json_encode($respon)); 
        }

mysqli_close($db);
?>