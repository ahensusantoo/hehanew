<?php 
require_once('../../templates/koneksi.php');

	$idmerchant            = $_POST['idmerchant'];
	$kd_merchant_employee  = $_POST['id_merchant_employee'];
	$tagihan_nota          = $_POST['tagihan_nota'];
	$jumlahbayar           = $_POST['jumlahbayar'];
	$jumlah_item           = $_POST['jumlah_item'];
	$status_print          = $_POST['status_print'];
	$status_transaksi      = $_POST['status_transaksi'];
	$id_jenis_pembayaran   = $_POST['metode_pembayaran'];
    $catatan_pesanan       = $_POST['catatan_pesanan'];
    $status_pembayaran     = $_POST['status_pembayaran'];
    $kembalian             = $tagihan_nota-$jumlahbayar;
    $kd_shift              = $_POST['kd_shift'];
	$tagihan_nota_sebelum  = $_POST['tagihan_nota_sebelum'];
    $nomeja                = $_POST['nomeja'];
	
	if (isset($_POST['diskon'])){
	    $diskon = $_POST['diskon'];
	} else {
	    $diskon = '0';
	}
	    
	    
	    
	$sql	= "SELECT * FROM `merchant_transaksi_detail` WHERE `kd_merchant_transaksi` = '' AND `kd_merchant` = '$idmerchant' AND `kd_merchant_employee` = '$kd_merchant_employee'"; 
	$result = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
	
	if(isset($result[0])) {
	    
        $sql	= mysqli_query($db,"SELECT createID('merchant_transaksi') AS idku")->fetch_assoc();
        $idku   = $sql['idku'];
        $id_referensi  = id_ke_struk($idku);
        
        $query = mysqli_query($db, "CALL tambah_transaksi('$kd_merchant_employee', '$tagihan_nota', '$jumlahbayar', '$jumlah_item', '$status_print', '$status_transaksi',
        '$id_jenis_pembayaran', '$catatan_pesanan', '$idmerchant', '$status_pembayaran', '$idku', '$kd_shift', '$tagihan_nota_sebelum', '$diskon', '$id_referensi', '$nomeja')");
            
            if ($query){
            	$respon['id_transaksi'] = $idku;
            	$respon['tagihan_nota'] = "Rp " . number_format((double)$tagihan_nota,0,',','.');
            	$respon['jumlah_bayar'] = "Rp " . number_format((double)$jumlahbayar,0,',','.');
            	$respon['kembalian']    = "Rp " . number_format((double)$kembalian,0,',','.');
            	$respon['jumlah_item']  = $jumlah_item;
            	die(json_encode($respon));
            } else{ 
                http_response_code(400);
                $respon['pesan'] = "Gagal menambahkan Transaksi baru!\nKlik `Mengerti` untuk menutup pesan ini";
            	die(json_encode($respon)); 
            }
	}else{
	   
        http_response_code(400);
        $respon['pesan'] = "Keranjang sudah kosong!\nKlik `Mengerti` untuk menutup pesan ini";
        die(json_encode($respon));
	    
            
	}

mysqli_close($db);
?>