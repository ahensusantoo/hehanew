<?php 
    require_once('../../templates/koneksi.php');
    
	$id_merchant_produk     = $_GET['id_merchant_produk'];
	
        	    
        $sql	= mysqli_query($db,"SELECT * FROM `view_merchant_produk` WHERE id_merchant_produk = '$id_merchant_produk'"); 
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
                    		'harga_beli'                        => $row['harga_beli'],
                    		'harga_beli_format'                 => "Rp " . number_format((double)$row['harga_beli'],0,',','.'),
                    		'harga_asli'      	                => $harga,
                    		'harga_diskon'      	            => $harga_diskon,
                    		'harga_diskon_format' 	            => "Rp " . number_format((double)$harga_diskon,0,',','.'),
                    		'kd_merchant_kategori'	            => $row['kd_merchant_kategori'],
                    		'jenis_produk'	                    => $row['jenis_produk'],
                    		'kode_produk'	                    => $row['kode_produk'],
                    		'barcode'	                        => $row['barcode_produk'],
                    		'id_supplier'                       => $row['id_supplier'],
                    		'nama_supplier'                     => $row['nama_supplier']
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