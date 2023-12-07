<?php 
require_once('../../templates/koneksi.php');
		    
	$username		= $_POST['username'];
	$pass_login		= $_POST['password'];
	
	$text_dekripsi = enkripsiDekripsi( $pass_login ,'enkripsi');

    	       
	$sql	= "SELECT * FROM admin WHERE username_admin ='$username' AND status_rmv_admin ='N'"; 
	$result = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
	
	if(isset($result[0])) {
	    
        if($text_dekripsi == $result[0]['password_admin']) {
          
                echo json_encode($result);
            
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