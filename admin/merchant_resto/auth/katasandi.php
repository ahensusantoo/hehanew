<?php 
require_once('../../templates/koneksi.php');
		    
	$id_merchant_employee 		= $_POST['id_merchant_employee'];
	$pass_lama		            = $_POST['password_lama'];
	$pass_baru		            = $_POST['password_baru'];
	$pass_ulangi	            = $_POST['password_ulangi'];

if($pass_baru == $pass_ulangi){
    	       
	$sql	= "SELECT * FROM merchant_employee WHERE id_merchant_employee='$id_merchant_employee'"; 
	$result = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
	
	if(isset($result[0])) {
	    
	    $password = enkripsiDekripsi($pass_baru ,'enkripsi');
	    $passlama = enkripsiDekripsi($result[0]['password_employee'], 'dekripsi');
	    
	    
       if($passlama == $pass_lama) {
        
                $sql2   = "UPDATE merchant_employee SET password_employee = '$password' WHERE id_merchant_employee = '$id_merchant_employee'";
                $db->query($sql2);
                $respon['pesan'] = "Kata sandi telah berubah!\nKlik `Selesai` untuk menutup pesan ini";
                echo json_encode($respon);
            
        }else {
                http_response_code(400);
                $respon['pesan'] = "Kata sandi yang anda masukan salah!\nKlik `Ok` untuk menutup pesan ini";
                echo json_encode($respon);
        }
            
	}else{
                http_response_code(400);
                $respon['pesan'] = "Tidak id anggota terkait!\nKlik `OK` untuk menutup pesan ini";
                echo json_encode($respon);
	}
	
}else{
    
    http_response_code(400);
    $respon['pesan'] = "Pastikan kata sandi yang anda masukan sama !\nKlik `OK` untuk menutup pesan ini";
    echo json_encode($respon);
    
}	

	mysqli_close($db);

 ?>