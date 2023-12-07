<?php 
    require_once('../../templates/koneksi.php');
    
	$id_merchant                     = $_GET['id_merchant'];
	$id_merchant_kategori_produk     = $_GET['id_merchant_employee'];
    $kd_merchant_transaksi           = $_GET['id_merchant_transaksi'];
	
    	
    	    
    	if(!empty(str_replace("&#039;","'",$_GET['q']))){
    	    
    	    $q     = str_replace("&#039;","'",$_GET['q']);
       
            $sql	= mysqli_query($db,"SELECT a.catatan_pesanan, a.id_merchant_transaksi_detail, a.kd_merchant, a.kd_merchant_produk, b.status_konsi, b.nama_produk, a.jumlah_produk, b.gambar_produk, a.harga_produk, a.diskon, a.harga_setelah_diskon, a.tgl_input_detail
            FROM merchant_transaksi_detail a
            JOIN merchant_produk b ON a.kd_merchant_produk = b.id_merchant_produk
            WHERE a.kd_merchant = '$id_merchant' AND a.kd_merchant_employee = '$id_merchant_kategori_produk' AND a.kd_merchant_transaksi = '$kd_merchant_transaksi' AND a.status_transaksi_detail != '3' AND b.nama_produk LIKE '%$q%'"); 
            $result = array();
            	while($row = mysqli_fetch_array($sql)){
            	array_push($result,array(
            		'id_merchant_transaksi_detail'	    => $row['id_merchant_transaksi_detail'],
            		'kd_merchant'	                    => $row['kd_merchant'],
            		'kd_merchant_produk'	            => $row['kd_merchant_produk'],
            		'status_konsi'	                    => $row['status_konsi'],
            		'nama_produk'      	                => str_replace("&#039;","'",$row['nama_produk']),
            		'jumlah_beli'      	                => $row['jumlah_produk'],
            		'gambar_produk'	                    => base_url()."dist/img/barang/".$row['gambar_produk'],
            		'tanggal_input_keranjang'	    	=> date_format(date_create($row['tgl_input_detail']), 'd M y, H:i A'),
            		'diskon'	                        => $row['diskon'],
            		'catatan_pesanan'	                => str_replace("&#039;","'",$row['catatan_pesanan']),
            		'harga_setelah_diskon'	            => "Rp " . number_format((double)$row['harga_setelah_diskon'],2,',','.'),
            		'harga_produk'      	            => "Rp " . number_format((double)$row['harga_produk'],2,',','.')
            	));
                }
            
             if(isset($result[0])) {
    				    
    			echo json_encode($result);
    			
    		}  else{
                    http_response_code(400);
                    $respon['pesan'] = "Tidak ada data yang ditampilkan!\nKlik `Mengerti` untuk menutup pesan ini";
                    echo json_encode($respon);
    		}
    	}else{
    	    
            $sql	= mysqli_query($db,"SELECT a.catatan_pesanan, a.id_merchant_transaksi_detail, a.kd_merchant, a.kd_merchant_produk, b.status_konsi, b.nama_produk, a.jumlah_produk, b.gambar_produk, a.harga_produk, a.diskon, a.harga_setelah_diskon, a.tgl_input_detail FROM merchant_transaksi_detail a
            JOIN merchant_produk b ON a.kd_merchant_produk = b.id_merchant_produk
            WHERE a.kd_merchant = '$id_merchant' AND a.kd_merchant_employee = '$id_merchant_kategori_produk' AND a.kd_merchant_transaksi = '$kd_merchant_transaksi' AND a.status_transaksi_detail != '3'"); 
            $result = array();
            	while($row = mysqli_fetch_array($sql)){
            	array_push($result,array(
            		'id_merchant_transaksi_detail'	    => $row['id_merchant_transaksi_detail'],
            		'kd_merchant'	                    => $row['kd_merchant'],
            		'kd_merchant_produk'	            => $row['kd_merchant_produk'],
            		'status_konsi'	                    => $row['status_konsi'],
            		'nama_produk'      	                => str_replace("&#039;","'",$row['nama_produk']),
            		'jumlah_beli'      	                => $row['jumlah_produk'],
            		'catatan_pesanan'	                => $row['catatan_pesanan'],
            		'gambar_produk'	                    => base_url()."dist/img/barang/".$row['gambar_produk'],
            		'tanggal_input_keranjang'	    	=> date_format(date_create($row['tgl_input_detail']), 'd M y, H:i A'),
            		'diskon'	                        => $row['diskon'],
            		'harga_setelah_diskon'	            => "Rp " . number_format((double)$row['harga_setelah_diskon'],2,',','.'),
            		'harga_produk'      	            => "Rp " . number_format((double)$row['harga_produk'],2,',','.')
            	));
                }
            
             if(isset($result[0])) {
    			echo json_encode($result);
    		}  else{
                $sql	= mysqli_query($db,"DELETE FROM `merchant_transaksi` WHERE id_merchant_transaksi ='$kd_merchant_transaksi'");
                http_response_code(400);
                $respon['pesan'] = "Tidak ada data yang ditampilkan!\nKlik `Mengerti` untuk menutup pesan ini";
                echo json_encode($respon);
    		}
    	}

        
	mysqli_close($db);

 ?>