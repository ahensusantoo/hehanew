<?php 
    require_once('../../templates/koneksi.php');
    
	$id_merchant                     = $_GET['id_merchant'];
	$id_merchant_kategori_produk     = $_GET['id_merchant_employee'];
	$limit                           = $_GET['limit'];
	$offset                          = $_GET['offset'];
	
    	
    	    
            	if(!empty($_GET['tanggal_awal'])){
            	    
            	    $tanggal_awal     = $_GET['tanggal_awal']." 00:00:00";
            	    $tanggal_akhir     = $_GET['tanggal_akhir']." 23:59:59";
               
                    $sql	= mysqli_query($db,"SELECT * FROM merchant_mutasi_stok a
                    JOIN merchant_employee b ON a.kd_merchant_employee = b.id_merchant_employee 
                    WHERE a.kd_merchant = '$id_merchant'  AND a.tanggal_mutasi BETWEEN '$tanggal_awal' AND '$tanggal_akhir' 
                    LIMIT $limit, $offset"); 
                    $result = array();
                    	while($row = mysqli_fetch_array($sql)){
                    	    if($row['jenis_mutasi'] == '2'){
                    	        $mutasi = 'Keluar';
                    	    }else{
                    	        $mutasi = 'Masuk';
                    	    }
                    	array_push($result,array(
                    		'id_merchant_mutasi_stok'	        => $row['id_merchant_mutasi_stok'],
                    		'id_pretty'	                        => id_ke_struk($row['id_merchant_mutasi_stok']),
                    		'nama_employee'	    	            => str_replace("&#039;","'",$row['nama_employee']),
                    		'tanggal_mutasi'	    	        => date_format(date_create($row['tanggal_mutasi']), 'd M y, H:i A'),
                    		'jenis_mutasi'	                    => $row['jenis_mutasi'],
                    		'jenis_format'	                    => $mutasi,
                    		'keterangan_mutasi'      	        => str_replace("&#039;","'",$row['keterangan_mutasi']),
                    		'status_rmv_mutasi'      	        => $row['status_rmv_mutasi']
                    	));
                        }
                    
                     if(isset($result[0])) {
            				    
            			echo json_encode($result);
            			
            		}  else{
                            http_response_code(400);
                            if($limit == 0){
                                $respon['pesan'] = "Tidak ada data yang ditampilkan!!\nKlik `Mengerti` untuk menutup pesan ini";
                                $respon['kode']  = "1";
                            }else{
                                $respon['pesan'] = "Ini merupakan halaman terakhir!\nKlik `Mengerti` untuk menutup pesan ini";
                                $respon['kode']  = "2";
                            }
                            echo json_encode($respon);
            		}
            	}else{
            	    
            	    //AND a.kd_merchant_employee = '$id_merchant_kategori_produk'
               
                    $sql	= mysqli_query($db,"SELECT * FROM merchant_mutasi_stok a
                    JOIN merchant_employee b ON a.kd_merchant_employee = b.id_merchant_employee 
                    WHERE a.kd_merchant = '$id_merchant'
                    ORDER BY tanggal_mutasi DESC LIMIT $limit, $offset"); 
                    $result = array();
                    	while($row = mysqli_fetch_array($sql)){
                    	if($row['jenis_mutasi'] == '2'){
                    	        $mutasi = 'Keluar';
                    	    }else{
                    	        $mutasi = 'Masuk';
                    	    }
                    	array_push($result,array(
                    		'id_merchant_mutasi_stok'	        => $row['id_merchant_mutasi_stok'],
                    		'id_pretty'	                        => id_ke_struk($row['id_merchant_mutasi_stok']),
                    		'nama_employee'	    	            => str_replace("&#039;","'",$row['nama_employee']),
                    		'tanggal_mutasi'	    	        => date_format(date_create($row['tanggal_mutasi']), 'd M y, H:i A'),
                    		'jenis_mutasi'	                    => $row['jenis_mutasi'],
                    		'jenis_format'	                    => $mutasi,
                    		'keterangan_mutasi'      	        => str_replace("&#039;","'",$row['keterangan_mutasi']),
                    		'status_rmv_mutasi'      	        => $row['status_rmv_mutasi']
                    	));
                        }
                    
                     if(isset($result[0])) {
            				    
            			echo json_encode($result);
            			
            		}  else{
                           http_response_code(400);
                            if($limit == 0){
                                $respon['pesan'] = "Tidak ada data yang ditampilkan!!\nKlik `Mengerti` untuk menutup pesan ini";
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