<?php 
require_once('../../templates/koneksi.php');

	$idmerchant            = $_POST['id_merchant'];
	$id_merchant_transaksi_detail      = $_POST['id_merchant_transaksi_detail'];
	$kd_merchant_employee  = $_POST['id_merchant_employee'];
	$id_merchant_produk    = $_POST['id_merchant_produk'];
    $catatan_pesanan       = $_POST['catatan_pesanan'];
    $stok_setelah          = $_POST['jumlah_data'];
	    
	   
        	
            $query = mysqli_query($db, "CALL edit_keranjang_nanti('$idmerchant', '$kd_merchant_employee', '$id_merchant_produk', '$catatan_pesanan', '$stok_setelah', '$id_merchant_transaksi_detail')");
            
            if ($query){
            	$respon['pesan'] = "Berhasil mengedit keranjang!\nKlik `Selesai` untuk menutup pesan ini";
            	die(json_encode($respon));
            } else{ 
                http_response_code(400);
                $respon['pesan'] = "Gagal mengedit keranjang!\nKlik `Selesai` untuk menutup pesan ini";
            	die(json_encode($respon)); 
            }
	

mysqli_close($db);
?>