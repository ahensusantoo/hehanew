<?php 
    require_once('../../templates/koneksi.php');
    
    $limit      = $_GET['limit'];
    $offset     = $_GET['offset'];
    $tag_menu   = $_GET['tag'];
	
	if($tag_menu == 'tiket'){
   
        $sql	= "SELECT*FROM transaksi
        INNER JOIN tiket ON transaksi.id_transaksi = tiket.kd_transaksi 
        INNER JOIN jenis_tiket ON tiket.kd_jenis_tiket = jenis_tiket.id_jenis_tiket
        WHERE tiket.status_tiket = '1' ORDER BY transaksi.tanggal_transaksi DESC LIMIT $limit, $offset"; 
        $result = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
        
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
            http_response_code(400);
            $respon['pesan'] = "Sedang dalam pengembangan!\nKlik `Mengerti` untuk menutup pesan ini";
            $respon['kode']  = "0";
            echo json_encode($respon);
	}
        
	mysqli_close($db);

 ?>