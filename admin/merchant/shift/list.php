<?php 
    require_once('../../templates/koneksi.php');
	    
    $sql	= mysqli_query($db,"SELECT * FROM shift WHERE status_aktif_shift = 'Y'"); 
    $result = array();
    	while($row = mysqli_fetch_array($sql)){
    	array_push($result,array(
    		'id_shift'	    => $row['id_shift'],
    		'nama_shift'	=> $row['nama_shift'],
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
        
	mysqli_close($db);

 ?>