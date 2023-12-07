<?php 
    require_once('../../templates/koneksi.php');
    
    $id_tiket = $_GET['id_tiket'];
    $tag_menu = $_GET['tag'];
	
	if($tag_menu == 'tiket'){
   
        $sql	= "SELECT*FROM transaksi
                    INNER JOIN tiket ON transaksi.id_transaksi = tiket.kd_transaksi
                    INNER JOIN jenis_tiket ON tiket.kd_jenis_tiket = jenis_tiket.id_jenis_tiket
                    where tiket.kode_tiket = '$id_tiket'"; 
        $result = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
        
        
        if(!isset($result[0])) {
            
                http_response_code(400);
                $respon['pesan'] = "Tidak ada data terkait!\nKlik `Mengerti` untuk menutup pesan ini";
                $respon['kode']  = "0";
                echo json_encode($respon);
                mysqli_close($db);
                exit();
            
        }
        
         if($result[0]['status_transaksi'] == '1' || $result[0]['status_transaksi'] == '2') {

				if ($result[0]['status_tiket'] == '0'){
				    
					echo json_encode($result);
				
				    
				}else if ($result[0]['status_tiket'] == '1' ){
                    http_response_code(400);
                    $respon['kode']  = "2";
                    $respon['pesan'] = "Tiket Telah dipakai!\nKlik `Mengerti` untuk menutup pesan ini";
                    echo json_encode($respon);
				}else{
				    http_response_code(400);
				    $respon['kode']  = "1";
                    $respon['pesan'] = "Tiket Telah dibatalkan!\nKlik `Mengerti` untuk menutup pesan ini";
                    echo json_encode($respon);
				}

		}  else{
                http_response_code(400);
                $respon['pesan'] = "Transaksi Telah dibatalkan!\nKlik `Mengerti` untuk menutup pesan ini";
                $respon['kode']  = "1";
                echo json_encode($respon);
		}
	}else{
            http_response_code(400);
            $respon['pesan'] = "Sedang dalam pengembangan!\nKlik `Mengerti` untuk menutup pesan ini";
            $respon['kode']  = "1";
            echo json_encode($respon);
	}
        
	mysqli_close($db);

 ?>