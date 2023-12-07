<?php 
require_once('../../templates/koneksi.php');
		    
	$id_merchant_employee 		= $_GET['id_merchant_employee'];
	
    	       
	$sql	= mysqli_query($db,"SELECT * FROM `merchant_employee` AS m1 JOIN `merchant` AS m2 ON m1.kd_merchant = m2.id_merchant WHERE id_merchant_employee =  '$id_merchant_employee'"); 
        $result = array();
        	while($row = mysqli_fetch_array($sql)){
        	array_push($result,array(
        		'id_merchant_employee'	            => $row['id_merchant_employee'],
        		'nama_employee'      	            => str_replace("&#039;","'",$row['nama_employee']),
        		'username_employee'	                => str_replace("&#039;","'",$row['username_employee']),
        		'level_employee'	                => $row['level_employee'],
        		'telp_employee'	                    => $row['telp_employee'],
        		'email_employee'	                => $row['email_employee'],
        		'kode_merchant'                     => $row['kode_merchant'],
        		'tanggal_sekarang'	                => date("Y-m-d"),
        		'tgl_input_employee'	    	    => date_format(date_create($row['tgl_input_employee']), 'd M y, H:i A'),
        		'status_aktif_employee'      	    => $row['status_aktif_employee'],
        		'nama_merchant'                     => $row['nama_merchant'],
        	));
            }
	
	if(isset($result[0])) {
	    
          
                echo json_encode($result);
            
	}else{
                http_response_code(400);
                $respon['pesan'] = "Tidak ada id merchant employee  yang terkait!\nKlik `Mengerti` untuk menutup pesan ini";
                echo json_encode($respon);
	}

	mysqli_close($db);

 ?>