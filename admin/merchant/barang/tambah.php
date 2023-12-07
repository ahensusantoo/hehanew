<?php 
require_once('../../templates/koneksi.php');

	$idmerchant            = $_POST['idmerchant'];
	$id_merchant_employee  = $_POST['id_merchant_employee'];
	$id_merchant_kategori  = $_POST['id_merchant_kategori'];
	$nama_produk           = str_replace("&#039;","'",$_POST['nama_produk']);
	$harga_produk          = $_POST['harga_produk'];
	$diskon                = $_POST['diskon'];
	$status_konsi          = $_POST['status_konsi'];
	$status_display_produk = $_POST['status_display_produk'];
	$harga_beli            = $_POST['harga_beli'];
	$stok                  = $_POST['stok'];
	$kode_produk           = $_POST['kode_produk'];
	$jenis_produk          = $_POST['jenis_produk'];
	$barcode               = $_POST['barcode'];
	$supplier              = $_POST['id_supplier'];
	
	$cek_kode_produk =$db->query(" SELECT * FROM merchant_produk 
                                    WHERE kode_produk ='".strtoupper($kode_produk)."'
                                    AND kd_merchant = '$idmerchant' AND status_remove_produk = 'N'")->num_rows;
	
	
	 if($harga_produk <= $harga_beli){
        http_response_code(400);
        $respon['pesan'] = "Harga jual tidak boleh kurang dari harga beli! Klik `Mengerti` untuk menutup pesan ini";
    	die(json_encode($respon)); 
        exit;
    }
    	
    	if ($cek_kode_produk > 0){
            http_response_code(400);
            $respon['pesan'] = "Kode barang ".$kode_produk." Sudah digunakan harap diganti!\nKlik `Mengerti` untuk menutup pesan ini";
            die(json_encode($respon));
        } else {
            if (!empty($_FILES['uploadedfile'])) {
    
    		// FORMAT DIIZINKAN
    	    $format_diizinkan["image/jpeg"] 		= "";
    	    $format_diizinkan["image/jpg"] 			= "";
    	    $format_diizinkan["image/png"] 		    = "";
    	    // END FORMAT DIIZINKAN
    
        	if (isset($format_diizinkan[$_FILES['uploadedfile']['type']])){
        		    
                $nama_file  = random_word(10).".png";
    			$lokasi 	= $_FILES['uploadedfile']['tmp_name'];
        			
        		if(move_uploaded_file($lokasi, "../../dist/img/barang/".$nama_file)){
        		    
                	$query = mysqli_query($db, "CALL new_barang('$idmerchant', '$id_merchant_employee', '$id_merchant_kategori', '$nama_produk',
                	'$harga_produk', '$diskon', '$nama_file', '$status_konsi', '$status_display_produk', '$stok', '$harga_beli', '$kode_produk', '$jenis_produk', '$barcode', '$supplier')");
                	
                	if ($query){
                		$respon['pesan'] = "Barang ".$nama_produk." berhasil ditambahkan";
                		die(json_encode($respon));
                	} else{ 
                	    http_response_code(400);
                	    unlink("../../dist/img/barang/".$nama_file);
                        $respon['pesan'] = "Gagal menambahkan barang baru!\nKlik `Mengerti` untuk menutup pesan ini";
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
    	    
                $query = mysqli_query($db, "CALL new_barang('$idmerchant', '$id_merchant_employee', '$id_merchant_kategori', '$nama_produk',
                	'$harga_produk', '$diskon', '', '$status_konsi', '$status_display_produk', '$stok', '$harga_beli', '$kode_produk', '$jenis_produk', '$barcode', '$supplier')");
            	
            	if ($query){
            		$respon['pesan'] = "Barang ".$nama_produk." berhasil ditambahkan";
            		die(json_encode($respon));
            	} else{ 
            	    http_response_code(400);
                    $respon['pesan'] = "Gagal menambahkan barang baru!\nKlik `Mengerti` untuk menutup pesan ini";
            		die(json_encode($respon)); 
            	}
        
    	}         
    }
	    
mysqli_close($db);
?>