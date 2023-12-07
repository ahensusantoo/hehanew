<?php 
require_once('../../templates/koneksi.php');

	$id_merchant_employee     = $_POST['id_merchant_employee'];
	
	$sql	= "SELECT * FROM merchant_employee WHERE id_merchant_employee  = '$id_merchant_employee'"; 
	$result = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
	
	    
        if($result[0]['status_aktif_employee'] == 'Y') {

        	$query = mysqli_query($db, "UPDATE merchant_employee SET status_aktif_employee = 'N' WHERE id_merchant_employee = '$id_merchant_employee'");
        	
        	if ($query){
        		$respon['pesan'] = "Anggota ".$result[0]['nama_employee']." berhasil dinonaktifkan";
        		die(json_encode($respon));
        	} else{ 
        	    http_response_code(400);
                $respon['pesan'] = "Gagal menonaktifkan Anggota!\nKlik `Mengerti` untuk menutup pesan ini";
        		die(json_encode($respon)); 
        	}
        }else{
            $query = mysqli_query($db, "UPDATE merchant_employee SET status_aktif_employee = 'Y' WHERE id_merchant_employee = '$id_merchant_employee'");
        	
        	if ($query){
        		$respon['pesan'] = "Anggota ".$result[0]['nama_employee']." berhasil diaktifkan";
        		die(json_encode($respon));
        	} else{ 
        	    http_response_code(400);
                $respon['pesan'] = "Gagal menonaktifkan Anggota!\nKlik `Mengerti` untuk menutup pesan ini";
        		die(json_encode($respon)); 
        	}
        }

mysqli_close($db);
?>