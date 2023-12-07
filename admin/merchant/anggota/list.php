<?php 
    require_once('../../templates/koneksi.php');
    
    $limit                           = $_GET['limit'];
    $offset                          = $_GET['offset'];
    $id_merchant                     = $_GET['id_merchant'];
	$id_merchant_employee            = $_GET['id_merchant_employee'];
	
	if(!empty($_GET['q'])){
	    
	    $q     = str_replace("&#039;","'",$_GET['q']);
   
        $sql	= mysqli_query($db,"SELECT * FROM `merchant_employee` WHERE id_merchant_employee != '$id_merchant_employee' AND kd_merchant =  '$id_merchant' AND status_remove_employee = 'N' AND (username_employee LIKE '%$q%' OR nama_employee LIKE '%$q%') ORDER BY tgl_input_employee DESC LIMIT $limit, $offset"); 
        $result = array();
        	while($row = mysqli_fetch_array($sql)){
        	array_push($result,array(
        		'id_merchant_employee'	            => $row['id_merchant_employee'],
        		'nama_employee'      	            => str_replace("&#039;","'",$row['nama_employee']),
        		'username_employee'	                => str_replace("&#039;","'",$row['username_employee']),
        		'level_employee'	                => $row['level_employee'],
        		'telp_employee'	                    => $row['telp_employee'],
        		'email_employee'	                => $row['email_employee'],
        		'tgl_input_employee'	    	    => date_format(date_create($row['tgl_input_employee']), 'd M y, H:i A'),
        		'status_aktif_employee'      	    => $row['status_aktif_employee']
        	));
            }
        
         if(isset($result[0])) {
				    
			echo json_encode($result);
			
		}  else{
                http_response_code(400);
                if($limit == 0){
                    $respon['pesan'] = "Tidak ada data yang ditampilkan!\nKlik `Mengerti` untuk menutup pesan ini";
                    $respon['kode']  = "1";
                }else{
                    $respon['pesan'] = "Ini merupakan halaman terakhir!\nKlik `Mengerti` untuk menutup pesan ini";
                    $respon['kode']  = "2";
                }
                echo json_encode($respon);
		}
	}else{
	    
       $sql	= mysqli_query($db,"SELECT * FROM `merchant_employee` WHERE id_merchant_employee != '$id_merchant_employee' AND kd_merchant =  '$id_merchant' AND status_remove_employee = 'N' ORDER BY tgl_input_employee DESC LIMIT $limit, $offset"); 
        $result = array();
        	while($row = mysqli_fetch_array($sql)){
        	array_push($result,array(
        		'id_merchant_employee'	            => $row['id_merchant_employee'],
        		'nama_employee'      	            => str_replace("&#039;","'",$row['nama_employee']),
        		'username_employee'	                => str_replace("&#039;","'",$row['username_employee']),
        		'level_employee'	                => $row['level_employee'],
        		'telp_employee'	                    => $row['telp_employee'],
        		'email_employee'	                => $row['email_employee'],
        		'tgl_input_employee'	    	    => date_format(date_create($row['tgl_input_employee']), 'd M y, H:i A'),
        		'status_aktif_employee'      	    => $row['status_aktif_employee']
        	));
            }
        
         if(isset($result[0])) {
				    
			echo json_encode($result);
			
		}  else{
                http_response_code(400);
                if($limit == 0){
                    $respon['pesan'] = "Tidak ada data yang ditampilkan!\nKlik `Mengerti` untuk menutup pesan ini";
                    $respon['kode']  = "1";
                }else{
                    $respon['pesan'] = "Ini merupakan halaman terakhir!\nKlik `Mengerti` untuk menutup pesan ini";
                    $respon['kode']  = "2";
                }
                echo json_encode($respon);
		}
	}
        
	mysqli_close($db);

 ?>