<?php 
    require_once('../../templates/koneksi.php');
	      	
    $sql	= mysqli_query($db,"SELECT * FROM `jenis_pembayaran` WHERE `status_aktif` = 'Y'"); 
    $result = array();
    	while($row = mysqli_fetch_array($sql)){
    	array_push($result,array(
    		'id_jenis_pembayaran'	            => $row['id_jenis_pembayaran'],
    		'nama_jenis_pembayaran'      	    => str_replace("&#039;","'",$row['nama_jenis_pembayaran']),
    		'tgl_input_jenis_pembayaran'	    => date_format(date_create($row['tgl_input_jenis_pembayaran']), 'd M y, H:i A')
    	));
        }
    
     if(isset($result[0])) {
		echo json_encode($result);
	}  else{
        http_response_code(400);
        $respon['pesan'] = "Tidak ada data yang ditampilkan!\nKlik `Mengerti` untuk menutup pesan ini";
        echo json_encode($respon);
	}
            	

        
	mysqli_close($db);

 ?>