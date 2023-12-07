<?php 
require_once('../../templates/koneksi.php');
		    
	$username		= $_POST['username'];
	$pass_login		= $_POST['password'];
	
	$text_dekripsi = enkripsiDekripsi( $pass_login ,'enkripsi');

    	       
    $sql	= "SELECT * FROM merchant a
    INNER JOIN merchant_employee b ON a.id_merchant = b.kd_merchant 
    WHERE b.username_employee ='$username' AND b.status_aktif_employee ='Y' AND b.level_employee != '0'"; 
	$result = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
	
	if(isset($result[0])) {
	    
        if($text_dekripsi == $result[0]['password_employee']) {
        
            $sql2	= "UPDATE merchant_employee SET token_login = LEFT(UUID(), 8) WHERE username_employee ='$username'"; 
            $db->query($sql2);
            
            $sql	= "SELECT * FROM merchant a
            INNER JOIN merchant_employee b ON a.id_merchant = b.kd_merchant 
            WHERE b.username_employee ='$username' AND b.status_aktif_employee ='Y'"; 
            $data = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
        
            echo json_encode($data);
            
        }else {
                http_response_code(400);
                $respon['pesan'] = "Kata sandi yang anda masukan salah!\nKlik `Mengerti` untuk menutup pesan ini";
                echo json_encode($respon);
        }
            
	}else{
                http_response_code(400);
                $respon['pesan'] = "Tidak ada id admin yang terkait!\nKlik `Mengerti` untuk menutup pesan ini";
                echo json_encode($respon);
	}

	mysqli_close($db);

 ?>