<?php 
require_once('../../templates/koneksi.php');
		    
	$id_admin		= $_GET['id_admin'];
	
    	       
	$sql	= "SELECT * FROM admin WHERE id_admin ='$id_admin' AND status_rmv_admin ='N'"; 
	$result = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
	
	if(isset($result[0])) {
	    
          
                echo json_encode($result);
            
	}else{
                http_response_code(400);
                $respon['pesan'] = "Tidak ada id admin yang terkait!\nKlik `Mengerti` untuk menutup pesan ini";
                echo json_encode($respon);
	}

	mysqli_close($db);

 ?>