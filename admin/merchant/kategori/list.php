<?php 
    require_once('../../templates/koneksi.php');
    
    $limit           = $_GET['limit'];
    $offset          = $_GET['offset'];
    $kd_merchant     = $_GET['id_merchant'];
	
	if(!empty($_GET['q'])){
	    
	    $q     = $_GET['q'];
        
        $sql	= mysqli_query($db,"SELECT*FROM merchant_kategori_produk
        WHERE kd_merchant = '$kd_merchant' AND nama_kategori LIKE '%$q%' ORDER BY tgl_input_kategori DESC LIMIT $limit, $offset"); 
                $result = array();
            
                	while($row = mysqli_fetch_array($sql)){
                	array_push($result,array(
                		'id_merchant_kategori_produk'	    => $row['id_merchant_kategori_produk'],
                		'kd_merchant'	                    => $row['kd_merchant'],
                		'nama_kategori'      	            => str_replace("&#039;","'",$row['nama_kategori']),
                		'kode_kategori'      	            => str_replace("&#039;","'",$row['kode_kategori']),
                		'status_aktif_kategori'	            => $row['status_aktif_kategori'],
                		'tgl_input_kategori'	    	    => date_format(date_create($row['tgl_input_kategori']), 'd M y, H:i A')
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
	    
        $sql	= mysqli_query($db,"SELECT*FROM merchant_kategori_produk WHERE kd_merchant = '$kd_merchant' ORDER BY tgl_input_kategori DESC LIMIT $limit, $offset"); 
        $result = array();
        	while($row = mysqli_fetch_array($sql)){
        	array_push($result,array(
        		'id_merchant_kategori_produk'	    => $row['id_merchant_kategori_produk'],
        		'kd_merchant'	                    => $row['kd_merchant'],
        		'nama_kategori'      	            => str_replace("&#039;","'",$row['nama_kategori']),
        		'kode_kategori'      	            => str_replace("&#039;","'",$row['kode_kategori']),
        		'status_aktif_kategori'	            => $row['status_aktif_kategori'],
        		'tgl_input_kategori'	    	    => date_format(date_create($row['tgl_input_kategori']), 'd M y, H:i A')
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