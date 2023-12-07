<?php 
require_once('../../templates/koneksi.php');

	$id_merchant_produk             = $_POST['id_merchant_produk'];
	$id_merchant_kategori           = $_POST['id_merchant_kategori'];
	$nama_produk                    = $_POST['nama_produk'];
	$harga_produk                   = $_POST['harga_produk'];
	$status_konsi                   = $_POST['status_konsi'];
	$diskon                         = $_POST['diskon'];
	$status_display_produk          = $_POST['status_display_produk'];
	$jenis_produk                   = $_POST['jenis_produk'];
	$kode_produk                    = $_POST['kode_produk'];
	$id_merchant                    = $_POST['id_merchant'];
	$barcode                        = $_POST['barcode'];
	$supplier                       = $_POST['id_supplier'];

    $sql	= "SELECT * FROM merchant_produk WHERE id_merchant_produk  = '$id_merchant_produk'"; 
    $data = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
    
    $cek_kode_produk =$db->query(" SELECT * FROM merchant_produk 
                                    WHERE kode_produk ='".strtoupper($kode_produk)."'
                                    AND kd_merchant = '$id_merchant' 
                                    AND id_merchant_produk != '$id_merchant_produk' AND status_remove_produk = 'N'")->num_rows;
    
    if (isset($data[0])) {
        
        if ($cek_kode_produk > 0){
            http_response_code(400);
            $respon['pesan'] = "Kode barang ".$kode_produk." Sudah digunakan harap diganti!\nKlik `Mengerti` untuk menutup pesan ini";
            die(json_encode($respon));
        } else {
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
            		    
                    	$query = mysqli_query($db, "UPDATE merchant_produk SET kd_merchant_kategori = '$id_merchant_kategori', diskon = '$diskon',nama_produk = '$nama_produk',
                    	harga_produk = '$harga_produk', status_konsi = '$status_konsi', status_display_produk = '$status_display_produk',  gambar_produk = '$nama_file', jenis_produk = '$jenis_produk'
                    	, barcode_produk = '$barcode' WHERE id_merchant_produk = '$id_merchant_produk', id_supplier = '$supplier '");
                    	
                    	
                    	if ($query){
                    	    unlink("../../dist/img/barang/".$data[0]['gambar_produk']);
                    		$respon['pesan'] = "Detail barang ".$data[0]['nama_produk']." berhasil dirubah";
                    		die(json_encode($respon));
                    	} else{ 
                    	    http_response_code(400);
                    	    unlink("../../dist/img/barang/".$nama_file);
                            $respon['pesan'] = "Gagal merubah barang!\nKlik `Mengerti` untuk menutup pesan ini";
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
        	    
                   	$query = mysqli_query($db, "UPDATE merchant_produk SET kd_merchant_kategori = '$id_merchant_kategori', diskon = '$diskon', nama_produk = '$nama_produk',
                    	harga_produk = '$harga_produk', status_konsi = '$status_konsi', status_display_produk = '$status_display_produk', jenis_produk = '$jenis_produk',
                    	barcode_produk = '$barcode' , id_supplier = '$supplier ' WHERE id_merchant_produk = '$id_merchant_produk'");
                    	
                    	
                    	if ($query){
                    		$respon['pesan'] = "Foto barang ".$data[0]['nama_produk']." berhasil dirubah";
                    		die(json_encode($respon));
                    	} else{ 
                    	    http_response_code(400);
                            $respon['pesan'] = "Gagal merubah barang!\nKlik `Mengerti` untuk menutup pesan ini";
                    		die(json_encode($respon)); 
                    	}
            
        	}      
        }
	
    }else{
        http_response_code(400);
        $respon['pesan'] = "Tidak ada barang terkait!\nKlik `Mengerti` untuk menutup pesan ini";
        die(json_encode($respon));
    }	

mysqli_close($db);
?>