<?php 
    require_once('../../templates/koneksi.php');
    
	$barcode = $_GET['barcode'];
	$id_merchant                     = $_GET['id_merchant'];
	
        	    
        $sql	= mysqli_query($db,"SELECT * FROM produk_merchant
                    WHERE status_display_produk = 'Y' AND status_remove_produk = 'N' AND kd_merchant = '$id_merchant' AND barcode_produk = '$barcode' ORDER BY tgl_input_produk DESC"); 
        $result = array();
        	while($row = mysqli_fetch_array($sql)){
        	 $diskon = $row['diskon'];
                    	    $harga  = $row['harga_produk'];
                    	    if($diskon == '0'){
                    	        $harga_diskon = $row['harga_produk'];
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
                $respon['pesan'] = "Tidak ada data yang ditampilkan!\nKlik `Mengerti` untuk menutup pesan ini";
                echo json_encode($respon);
		}
         	
        
	mysqli_close($db);

 ?>