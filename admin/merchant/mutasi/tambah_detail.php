<?php 
require_once('../../templates/koneksi.php');

	$idmerchant            = $_POST['idmerchant'];
	$id_merchant_employee  = $_POST['id_merchant_employee'];
	$jenis_mutasi          = $_POST['jenis_mutasi'];
	$keterangan_mutasi     = str_replace("&#039;","'",$_POST['keterangan_mutasi']);
	
	if(empty($idmerchant)){
        http_response_code(400);
        $respon['pesan'] = "id merchant hilang!Klik `Mengerti` untuk menutup pesan ini";
    	die(json_encode($respon)); 
        exit;
    }else if(empty($id_merchant_employee)){
        http_response_code(400);
        $respon['pesan'] = "id merchant employe hilang!Klik `Mengerti` untuk menutup pesan ini";
    	die(json_encode($respon)); 
        exit;
    }else if(empty($keterangan_mutasi)){
        http_response_code(400);
        $respon['pesan'] = "Beri keterangan untuk mempermudah data mutasi !Klik `Mengerti` untuk menutup pesan ini";
    	die(json_encode($respon)); 
        exit;
    }else{
	
        $query = mysqli_query($db, "INSERT INTO `merchant_mutasi_stok`(`id_merchant_mutasi_stok`, `jenis_mutasi`, `kd_merchant`, `kd_merchant_employee`, `keterangan_mutasi`) 
        VALUES (createID('merchant_mutasi_stok'), '$jenis_mutasi', '$idmerchant', '$id_merchant_employee', '$keterangan_mutasi')");
    	
    	if ($query){
    		$respon['pesan'] = "Detail Mutasi berhasil dibuat";
    		die(json_encode($respon));
    	} else{ 
    	    http_response_code(400);
            $respon['pesan'] = "Gagal menambahkan barang baru!\nKlik `Mengerti` untuk menutup pesan ini";
    		die(json_encode($respon)); 
    	}

    }

mysqli_close($db);
?>