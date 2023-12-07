<?php 
require_once('../../templates/koneksi.php');
		    
	$username	    	= $_POST['username'];
	$token_login		= $_POST['token_login'];
	

    	       
    $sql	= "SELECT * FROM merchant a
    INNER JOIN merchant_employee b ON a.id_merchant = b.kd_merchant 
    WHERE b.username_employee ='$username' AND b.status_aktif_employee ='Y'"; 
	$result = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
	
	if(isset($result[0])) {
	    
        if($token_login == $result[0]['token_login']) {
        
                $respon['pesan'] = "Token sama!\nKlik `Mengerti` untuk menutup pesan ini";
                echo json_encode($respon);
            
        }else {
                http_response_code(400);
                $respon['pesan'] = "Token ini tidak sama!\nKlik `Mengerti` untuk menutup pesan ini";
                echo json_encode($respon);
        }
            
	}else{
                http_response_code(400);
                $respon['pesan'] = "Tidak ada id admin yang terkait!\nKlik `Mengerti` untuk menutup pesan ini";
                echo json_encode($respon);
	}

	mysqli_close($db);

 ?>