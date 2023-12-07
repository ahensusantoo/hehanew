<?php 
    require_once('../../templates/koneksi.php');
    
    $limit                           = $_GET['limit'];
    $offset                          = $_GET['offset'];
	$id_merchant                     = $_GET['id_merchant'];
	$id_merchant_kategori_produk     = $_GET['id_merchant_kategori_produk'];
	
	if(isset($_GET['status_display'])) {
	
    	if($id_merchant_kategori_produk == 'semua'){
    	    
            	if(!empty(str_replace("&#039;","'",$_GET['q']))){
            	    
            	    $q     = str_replace("&#039;","'",$_GET['q']);
               
                    $sql	= mysqli_query($db,"SELECT * FROM produk_merchant
                    WHERE status_display_produk = 'Y' AND status_remove_produk = 'N' AND jenis_produk = '2' AND kd_merchant = '$id_merchant' AND nama_produk LIKE '%$q%' ORDER BY tgl_input_produk DESC LIMIT $limit, $offset"); 
                    $result = array();
                    	while($row = mysqli_fetch_array($sql)){
                    	    $diskon = $row['diskon'];
                    	    $harga  = $row['harga_produk'];
                    	    if($diskon == '0'){
                    	        $harga_diskon = $harga;
                    	    }else{
                    	        $harga_diskon = $diskon/100*$harga;
                    	        $harga_diskon = $harga - $harga_diskon;
                    	    }
                    	array_push($result,array(
                    		'id_merchant_produk'	            => $row['id_merchant_produk'],
                    		'diskon'	                        => $diskon,
                    		'kd_merchant'	                    => $row['kd_merchant'],
                    		'status_konsi'	                    => $row['status_konsi'],
                    		'nama_kategori'      	            => str_replace("&#039;","'",$row['nama_kategori']),
                    		'nama_produk'      	                => str_replace("&#039;","'",$row['nama_produk']),
                    		'gambar_produk'	                    => base_url()."dist/img/barang/".$row['gambar_produk'],
                    		'stok'	                            => (int)$row['stok_saat_ini'],
                    		'status_display_produk'	            => $row['status_display_produk'],
                    		'tgl_input_produk'	    	        => date_format(date_create($row['tgl_input_produk']), 'd M y, H:i A'),
                    		'harga_format'      	            => "Rp " . number_format((double)$harga,0,',','.'),
                    		'harga_asli'      	                => $harga,
                    		'harga_diskon'      	            => $harga_diskon,
                    		'harga_diskon_format' 	            => "Rp " . number_format((double)$harga_diskon,0,',','.'),
                    		'jenis_produk'	                    => $row['jenis_produk'],
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
            	    
                    $sql	= mysqli_query($db,"SELECT * FROM produk_merchant
                    WHERE status_display_produk = 'Y' AND status_remove_produk = 'N' AND jenis_produk = '2' AND kd_merchant = '$id_merchant' ORDER BY tgl_input_produk DESC LIMIT $limit, $offset"); 
                    $result = array();
                    	while($row = mysqli_fetch_array($sql)){
                    	$diskon = $row['diskon'];
                    	    $harga  = $row['harga_produk'];
                    	    if($diskon == '0'){
                    	        $harga_diskon = $harga;
                    	    }else{
                    	       $harga_diskon = $diskon/100*$harga;
                       	       $harga_diskon = $harga - $harga_diskon; 
                    	    }
                    	array_push($result,array(
                    		'id_merchant_produk'	            => $row['id_merchant_produk'],
                    		'diskon'	                        => $diskon,
                    		'kd_merchant'	                    => $row['kd_merchant'],
                    		'status_konsi'	                    => $row['status_konsi'],
                    		'nama_kategori'      	            => str_replace("&#039;","'",$row['nama_kategori']),
                    		'nama_produk'      	                => str_replace("&#039;","'",$row['nama_produk']),
                    		'gambar_produk'	                    => base_url()."dist/img/barang/".$row['gambar_produk'],
                    		'stok'	                            => (int)$row['stok_saat_ini'],
                    		'status_display_produk'	            => $row['status_display_produk'],
                    		'tgl_input_produk'	    	        => date_format(date_create($row['tgl_input_produk']), 'd M y, H:i A'),
                    		'harga_format'      	            => "Rp " . number_format((double)$harga,0,',','.'),
                    		'harga_asli'      	                => $harga,
                    		'harga_diskon'      	            => $harga_diskon,
                    		'harga_diskon_format' 	            => "Rp " . number_format((double)$harga_diskon,0,',','.'),
                    		'jenis_produk'	                    => $row['jenis_produk'],
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
    	}else{
    	    
    	    if(!empty(str_replace("&#039;","'",$_GET['q']))){
            	    
            	    $q     = str_replace("&#039;","'",$_GET['q']);
               
                    $sql	= mysqli_query($db,"SELECT * FROM produk_merchant 
                    WHERE status_display_produk = 'Y' AND status_remove_produk = 'N' AND jenis_produk = '2' AND kd_merchant = '$id_merchant' AND kd_merchant_kategori = '$id_merchant_kategori_produk' AND nama_produk LIKE '%$q%' ORDER BY tgl_input_produk DESC LIMIT $limit, $offset"); 
                    $result = array();
                    	while($row = mysqli_fetch_array($sql)){
                    	$diskon = $row['diskon'];
                    	    $harga  = $row['harga_produk'];
                    	    if($diskon == '0'){
                    	        $harga_diskon = $harga;
                    	    }else{
                    	       $harga_diskon = $diskon/100*$harga;
                       	       $harga_diskon = $harga - $harga_diskon;
                    	    }
                    	array_push($result,array(
                    		'id_merchant_produk'	            => $row['id_merchant_produk'],
                    		'diskon'	                        => $diskon,
                    		'kd_merchant'	                    => $row['kd_merchant'],
                    		'status_konsi'	                    => $row['status_konsi'],
                    		'nama_kategori'      	            => str_replace("&#039;","'",$row['nama_kategori']),
                    		'nama_produk'      	                => str_replace("&#039;","'",$row['nama_produk']),
                    		'gambar_produk'	                    => base_url()."dist/img/barang/".$row['gambar_produk'],
                    		'stok'	                            => (int)$row['stok_saat_ini'],
                    		'status_display_produk'	            => $row['status_display_produk'],
                    		'tgl_input_produk'	    	        => date_format(date_create($row['tgl_input_produk']), 'd M y, H:i A'),
                    		'harga_format'      	            => "Rp " . number_format((double)$harga,0,',','.'),
                    		'harga_asli'      	                => $harga,
                    		'harga_diskon'      	            => $harga_diskon,
                    		'harga_diskon_format' 	            => "Rp " . number_format((double)$harga_diskon,0,',','.'),
                    		'jenis_produk'	                    => $row['jenis_produk'],
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
            	    
                    $sql	= mysqli_query($db,"SELECT * FROM produk_merchant
                    WHERE status_display_produk = 'Y' AND status_remove_produk = 'N' AND jenis_produk = '2' AND kd_merchant = '$id_merchant' AND kd_merchant_kategori = '$id_merchant_kategori_produk' ORDER BY tgl_input_produk DESC LIMIT $limit, $offset"); 
                    $result = array();
                    	while($row = mysqli_fetch_array($sql)){
                    	$diskon = $row['diskon'];
                    	    $harga  = $row['harga_produk'];
                    	    if($diskon == '0'){
                    	        $harga_diskon = $harga;
                    	    }else{
                    	       $harga_diskon = $diskon/100*$harga;
                       	       $harga_diskon = $harga - $harga_diskon;
                    	    }
                    	array_push($result,array(
                    		'id_merchant_produk'	            => $row['id_merchant_produk'],
                    		'diskon'	                        => $diskon,
                    		'kd_merchant'	                    => $row['kd_merchant'],
                    		'status_konsi'	                    => $row['status_konsi'],
                    		'nama_kategori'      	            => str_replace("&#039;","'",$row['nama_kategori']),
                    		'nama_produk'      	                => str_replace("&#039;","'",$row['nama_produk']),
                    		'gambar_produk'	                    => base_url()."dist/img/barang/".$row['gambar_produk'],
                    		'stok'	                            => (int)$row['stok_saat_ini'],
                    		'status_display_produk'	            => $row['status_display_produk'],
                    		'tgl_input_produk'	    	        => date_format(date_create($row['tgl_input_produk']), 'd M y, H:i A'),
                    		'harga_format'      	            => "Rp " . number_format((double)$harga,0,',','.'),
                    		'harga_asli'      	                => $harga,
                    		'harga_diskon'      	            => $harga_diskon,
                    		'harga_diskon_format' 	            => "Rp " . number_format((double)$harga_diskon,0,',','.'),
                    		'jenis_produk'	                    => $row['jenis_produk'],
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
    	    
    	}
	}else{
	    if($id_merchant_kategori_produk == 'semua'){
    	    
            	if(!empty(str_replace("&#039;","'",$_GET['q']))){
            	    
            	    $q     = str_replace("&#039;","'",$_GET['q']);
               
                    $sql	= mysqli_query($db,"SELECT * FROM produk_merchant
                    WHERE status_remove_produk = 'N' AND kd_merchant = '$id_merchant' AND jenis_produk = '2' AND nama_produk LIKE '%$q%' ORDER BY tgl_input_produk DESC LIMIT $limit, $offset"); 
                    $result = array();
                    	while($row = mysqli_fetch_array($sql)){
                    	$diskon = $row['diskon'];
                    	    $harga  = $row['harga_produk'];
                    	    if($diskon == '0'){
                    	        $harga_diskon = $harga;
                    	    }else{
                    	      $harga_diskon = $diskon/100*$harga;
                       	       $harga_diskon = $harga - $harga_diskon;
                    	    }
                    	array_push($result,array(
                    		'id_merchant_produk'	            => $row['id_merchant_produk'],
                    		'diskon'	                        => $diskon,
                    		'kd_merchant'	                    => $row['kd_merchant'],
                    		'status_konsi'	                    => $row['status_konsi'],
                    		'nama_kategori'      	            => str_replace("&#039;","'",$row['nama_kategori']),
                    		'nama_produk'      	                => str_replace("&#039;","'",$row['nama_produk']),
                    		'gambar_produk'	                    => base_url()."dist/img/barang/".$row['gambar_produk'],
                    		'stok'	                            => (int)$row['stok_saat_ini'],
                    		'status_display_produk'	            => $row['status_display_produk'],
                    		'tgl_input_produk'	    	        => date_format(date_create($row['tgl_input_produk']), 'd M y, H:i A'),
                    		'harga_format'      	            => "Rp " . number_format((double)$harga,0,',','.'),
                    		'harga_asli'      	                => $harga,
                    		'harga_diskon'      	            => $harga_diskon,
                    		'harga_diskon_format' 	            => "Rp " . number_format((double)$harga_diskon,0,',','.'),
                    		'jenis_produk'	                    => $row['jenis_produk'],
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
            	    
                    $sql	= mysqli_query($db,"SELECT * FROM produk_merchant
                    WHERE status_remove_produk = 'N' AND kd_merchant = '$id_merchant' AND jenis_produk = '2' ORDER BY tgl_input_produk DESC LIMIT $limit, $offset"); 
                    $result = array();
                    	while($row = mysqli_fetch_array($sql)){
                    	$diskon = $row['diskon'];
                    	    $harga  = $row['harga_produk'];
                    	    if($diskon == '0'){
                    	        $harga_diskon = $harga;
                    	    }else{
                    	      $harga_diskon = $diskon/100*$harga;
                       	       $harga_diskon = $harga - $harga_diskon;
                    	    }
                    	array_push($result,array(
                    		'id_merchant_produk'	            => $row['id_merchant_produk'],
                    		'diskon'	                        => $diskon,
                    		'kd_merchant'	                    => $row['kd_merchant'],
                    		'status_konsi'	                    => $row['status_konsi'],
                    		'nama_kategori'      	            => str_replace("&#039;","'",$row['nama_kategori']),
                    		'nama_produk'      	                => str_replace("&#039;","'",$row['nama_produk']),
                    		'gambar_produk'	                    => base_url()."dist/img/barang/".$row['gambar_produk'],
                    		'stok'	                            => (int)$row['stok_saat_ini'],
                    		'status_display_produk'	            => $row['status_display_produk'],
                    		'tgl_input_produk'	    	        => date_format(date_create($row['tgl_input_produk']), 'd M y, H:i A'),
                    		'harga_format'      	            => "Rp " . number_format((double)$harga,0,',','.'),
                    		'harga_asli'      	                => $harga,
                    		'harga_diskon'      	            => $harga_diskon,
                    		'harga_diskon_format' 	            => "Rp " . number_format((double)$harga_diskon,0,',','.'),
                    		'jenis_produk'	                    => $row['jenis_produk'],
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
    	}else{
    	    
    	    if(!empty(str_replace("&#039;","'",$_GET['q']))){
            	    
            	    $q     = str_replace("&#039;","'",$_GET['q']);
               
                    $sql	= mysqli_query($db,"SELECT * FROM produk_merchant 
                    WHERE status_remove_produk = 'N' AND kd_merchant = '$id_merchant' AND jenis_produk = '2' AND kd_merchant_kategori = '$id_merchant_kategori_produk' AND nama_produk LIKE '%$q%' ORDER BY tgl_input_produk DESC LIMIT $limit, $offset"); 
                    $result = array();
                    	while($row = mysqli_fetch_array($sql)){
                    	$diskon = $row['diskon'];
                    	    $harga  = $row['harga_produk'];
                    	    if($diskon == '0'){
                    	        $harga_diskon = $harga;
                    	    }else{
                    	       $harga_diskon = $diskon/100*$harga;
                       	       $harga_diskon = $harga - $harga_diskon;
                    	    }
                    	array_push($result,array(
                    		'id_merchant_produk'	            => $row['id_merchant_produk'],
                    		'diskon'	                        => $diskon,
                    		'kd_merchant'	                    => $row['kd_merchant'],
                    		'status_konsi'	                    => $row['status_konsi'],
                    		'nama_kategori'      	            => str_replace("&#039;","'",$row['nama_kategori']),
                    		'nama_produk'      	                => str_replace("&#039;","'",$row['nama_produk']),
                    		'gambar_produk'	                    => base_url()."dist/img/barang/".$row['gambar_produk'],
                    		'stok'	                            => (int)$row['stok_saat_ini'],
                    		'status_display_produk'	            => $row['status_display_produk'],
                    		'tgl_input_produk'	    	        => date_format(date_create($row['tgl_input_produk']), 'd M y, H:i A'),
                    		'harga_format'      	            => "Rp " . number_format((double)$harga,0,',','.'),
                    		'harga_asli'      	                => $harga,
                    		'harga_diskon'      	            => $harga_diskon,
                    		'harga_diskon_format' 	            => "Rp " . number_format((double)$harga_diskon,0,',','.'),
                    		'jenis_produk'	                    => $row['jenis_produk'],
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
            	    
                    $sql	= mysqli_query($db,"SELECT * FROM produk_merchant
                    WHERE status_remove_produk = 'N' AND kd_merchant = '$id_merchant' AND jenis_produk = '2' AND kd_merchant_kategori = '$id_merchant_kategori_produk' ORDER BY tgl_input_produk DESC LIMIT $limit, $offset"); 
                    $result = array();
                    	while($row = mysqli_fetch_array($sql)){
                    	$diskon = $row['diskon'];
                    	    $harga  = $row['harga_produk'];
                    	    if($diskon == '0'){
                    	        $harga_diskon = $harga;
                    	    }else{
                    	        $harga_diskon = $diskon/100*$harga;
                       	       $harga_diskon = $harga - $harga_diskon;
                    	    }
                    	array_push($result,array(
                    		'id_merchant_produk'	            => $row['id_merchant_produk'],
                    		'diskon'	                        => $diskon,
                    		'kd_merchant'	                    => $row['kd_merchant'],
                    		'status_konsi'	                    => $row['status_konsi'],
                    		'nama_kategori'      	            => str_replace("&#039;","'",$row['nama_kategori']),
                    		'nama_produk'      	                => str_replace("&#039;","'",$row['nama_produk']),
                    		'gambar_produk'	                    => base_url()."dist/img/barang/".$row['gambar_produk'],
                    		'stok'	                            => (int)$row['stok_saat_ini'],
                    		'status_display_produk'	            => $row['status_display_produk'],
                    		'tgl_input_produk'	    	        => date_format(date_create($row['tgl_input_produk']), 'd M y, H:i A'),
                    		'harga_format'      	            => "Rp " . number_format((double)$harga,0,',','.'),
                    		'harga_asli'      	                => $harga,
                    		'harga_diskon'      	            => $harga_diskon,
                    		'harga_diskon_format' 	            => "Rp " . number_format((double)$harga_diskon,0,',','.'),
                    		'jenis_produk'	                    => $row['jenis_produk'],
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
    	    
    	}
	}
        
	mysqli_close($db);

 ?>