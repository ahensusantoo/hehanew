<?php 
require_once('../../templates/koneksi.php');

	$kd_merchant_transaksi = $_POST['id_merchant_transaksi'];
	$idmerchant            = $_POST['idmerchant'];
	$kd_merchant_employee  = $_POST['id_merchant_employee'];
	$tagihan_nota          = $_POST['tagihan_nota'];
	$jumlahbayar           = $_POST['jumlahbayar'];
	$jumlah_item           = $_POST['jumlah_item'];
	$status_print          = $_POST['status_print'];
	$id_jenis_pembayaran   = $_POST['metode_pembayaran'];
    $kembalian             = $tagihan_nota-$jumlahbayar;
	$tagihan_nota_sebelum  = $_POST['tagihan_nota_sebelum'];
	$id_referensi          = id_ke_struk($kd_merchant_transaksi);
	$nomeja                = $_POST['nomeja'];
	    
	$sql	= "SELECT * FROM `merchant_transaksi_detail` WHERE `kd_merchant_transaksi` = '$kd_merchant_transaksi' AND `kd_merchant` = '$idmerchant' AND `kd_merchant_employee` = '$kd_merchant_employee'"; 
	$result = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
	
	if(isset($result[0])) {
        $query = mysqli_query($db, "UPDATE `merchant_transaksi` SET `kd_jenis_pembayaran`='$id_jenis_pembayaran', `tagihan_nota`='$tagihan_nota', `nominal_sebelum_diskon` = '$tagihan_nota_sebelum',`jumlah_bayar`='$jumlahbayar',`jumlah_item`='$jumlah_item',`status_print`='$status_print',`status_transaksi`='2',`status_pembayaran`='1',`nomeja`='$nomeja'
        WHERE `id_merchant_transaksi` = '$kd_merchant_transaksi' AND `kd_merchant` = '$idmerchant' AND `kd_merchant_employee` = '$kd_merchant_employee'");
        
         $query2 = mysqli_query($db, "UPDATE `merchant_history_stok` SET `status_keranjang` = '2', `id_referensi`='$id_referensi'
            WHERE `status_keranjang` = '5' AND `kd_merchant_employee` = '$kd_merchant_employee' AND `kd_bayar_nanti` = '$kd_merchant_transaksi'");
            
            if ($query){
                    if ($query2){
                        $query_detail = mysqli_query($db, "UPDATE `merchant_transaksi_detail` SET `status_transaksi_detail`='2'
                        WHERE `id_merchant_transaksi` = '' AND `kd_merchant` = '$idmerchant' AND `kd_merchant_employee` = '$kd_merchant_employee'");
      
                    	$respon['id_transaksi'] = $kd_merchant_transaksi;
                    	$respon['tagihan_nota'] = "Rp " . number_format((double)$tagihan_nota,0,',','.');
                    	$respon['jumlah_bayar'] = "Rp " . number_format((double)$jumlahbayar,0,',','.');
                    	$respon['kembalian']    = "Rp " . number_format((double)$kembalian,0,',','.');
                    	$respon['jumlah_item']  = $jumlah_item;
                    	die(json_encode($respon));   
                    } else {
                        http_response_code(400);
                        $respon['pesan'] = "Gagal 1 Transaksi baru!\nKlik `Mengerti` untuk menutup pesan ini";
                    	die(json_encode($respon)); 
                    }
            } else{ 
                http_response_code(400);
                $respon['pesan'] = "Gagal 3 Transaksi baru!\nKlik `Mengerti` untuk menutup pesan ini";
            	die(json_encode($respon)); 
            }
	}else{
	   
        http_response_code(400);
        $respon['pesan'] = "Keranjang sudah kosong!\nKlik `Mengerti` untuk menutup pesan ini";
        die(json_encode($respon));
	    
            
	}

mysqli_close($db);
?>