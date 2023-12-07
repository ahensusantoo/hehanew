<?php 
require_once('../../templates/koneksi.php');

	$id_merchant_produk             = $_POST['id_merchant_produk'];

    $sql	= "SELECT * FROM merchant_produk WHERE id_merchant_produk  = '$id_merchant_produk'"; 
    $data = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
    
    if (isset($data[0])) {
            
	// INPUT THUMBNAIL
        	if (isset($_FILES['uploadedfile'])) {
        
        		// FORMAT DIIZINKAN
        	    $format_diizinkan["image/jpeg"] 		= "";
        	    $format_diizinkan["image/jpg"] 			= "";
        	    $format_diizinkan["image/png"] 		    = "";
        	    // END FORMAT DIIZINKAN
        
            	if (isset($format_diizinkan[$_FILES['uploadedfile']['type']])){
            		    
    		            $nama_file  = random_word(10).".png";
            			$lokasi 	= $_FILES['uploadedfile']['tmp_name'];
            			
            		if(move_uploaded_file($lokasi, "../../dist/img/barang/".$nama_file)){
            		    
                    	$query = mysqli_query($db, "UPDATE merchant_produk SET gambar_produk = '$nama_file' WHERE id_merchant_produk = '$id_merchant_produk'");
                    	
                    	
                    	if ($query){
                    	    unlink("../../dist/img/barang/".$data[0]['gambar_produk']);
                    		$respon['pesan'] = "Foto barang ".$data[0]['nama_produk']." berhasil ditambahkan";
                    		die(json_encode($respon));
                    	} else{ 
                    	    http_response_code(400);
                    	    unlink("../../dist/img/barang/".$nama_file);
                            $respon['pesan'] = "Gagal merubah foto barang!\nKlik `Mengerti` untuk menutup pesan ini";
                    		die(json_encode($respon)); 
                    	}
                    	
        			}else{
                            http_response_code(400);
                            $respon['pesan'] = "Upload file mengalami kegagalan!\nKlik `Mengerti` untuk menutup pesan ini";
                            die(json_encode($respon)); 
        			}	
                }else{
                    http_response_code(400);
                    $respon['pesan'] = "Format tidak diperbolehkan!\nKlik `Mengerti` untuk menutup pesan ini";
                    die(json_encode($respon));
        		}
        
        	}else{
        	    
                    http_response_code(400);
                    $respon['pesan'] = "File gambar tidak ditemukan!\nKlik `Mengerti` untuk menutup pesan ini";
                    die(json_encode($respon));
            
        	}    	
	
    }else{
        http_response_code(400);
        $respon['pesan'] = "Tidak ada barang terkait!\nKlik `Mengerti` untuk menutup pesan ini";
        die(json_encode($respon));
    }	

mysqli_close($db);
?>