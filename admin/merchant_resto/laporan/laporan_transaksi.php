<?php 
    require_once('../../templates/koneksi.php');
    
	$id_merchant                     = $_GET['id_merchant'];
	$id_merchant_employee            = $_GET['id_merchant_employee'];
	$level                           = $_GET['level'];
	$limit                           = $_GET['limit'];
	$offset                          = $_GET['offset'];
	
    	
    if($level == 2){	    
            	if(!empty($_GET['tanggal_awal'])){
            	    
            	    $tanggal_awal     = $_GET['tanggal_awal'];
            	    $tanggal_akhir     = $_GET['tanggal_akhir'];
               
                    $sql	= mysqli_query($db,"SELECT * FROM view_merchant_transaksi 
                    WHERE status_transaksi != 3 AND `kd_merchant` = '$id_merchant' AND `kd_merchant_employee` = '$id_merchant_employee' AND DATE(tgl_input_transaksi) BETWEEN '$tanggal_awal' AND '$tanggal_akhir' 
                    GROUP BY id_merchant_transaksi ORDER BY tgl_input_transaksi DESC LIMIT $limit, $offset"); 
                    $result = array();
                    	while($row = mysqli_fetch_array($sql)){
                    	    if($row['status_transaksi'] == 1){
                    	        $statustransaksi = "Sedang diproses";
                    	    }else if($row['status_transaksi'] == 2){
                    	        $statustransaksi = "Lunas";
                    	    }else{
                    	        $statustransaksi = "Dibatalkan";
                    	    }
                    	array_push($result,array(
                    		'id_merchant_transaksi'	            => $row['id_merchant_transaksi'],
                    		'nama_employee'      	            => str_replace("&#039;","'",$row['nama_employee']),
                    		'toko'               	            => str_replace("&#039;","'",$row['toko']),
                    		'telp_merchant'               	    => $row['telp_merchant'],
                    		'file_logo'               	        => $row['file_logo'],
                    		'no_nota'	    	                => $row['no_nota'],
                    		'kd_jenis_pembayaran'	    	    => $row['kd_jenis_pembayaran'],
                    		'no_antrian'	                    => $row['no_antrian'],
                    		'jumlah_item'      	                => $row['jumlah_item'],
                    		'status_print'      	            => $row['status_print'],
                    		'nama_produk'      	                => str_replace("&#039;","'",$row['nama_produk']),
                    		'keterangan_transaksi'      	    => $row['keterangan'],
                    		'catatan_barang'      	            => $row['catatan_pesanan'],
                    		'status_transaksi_ket'      	    => $statustransaksi,
                    		'status_transaksi'      	        => $row['status_transaksi'],
                    		'nama_jenis_pembayaran'	    	    => $row['nama_jenis_pembayaran'],
                    		'diskon_total'	    	            => $row['diskon_total'],
                    		'diskon_barang'      	            => $row['diskon_barang'],
                    		'nominal_sebelum_diskon'	        => "Rp " . number_format((double)$row['nominal_sebelum_diskon'],0,',','.'),
                    		'tagihan_nota'      	            => "Rp " . number_format((double)$row['tagihan_nota'],0,',','.'),
                    		'jumlah_bayar'      	            => "Rp " . number_format((double)$row['jumlah_bayar'],0,',','.'),
                    		'tgl_input_transaksi'	    	    => date_format(date_create($row['tgl_input_transaksi']), 'd M y, H:i A'),
                    		
                    		
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
            	    
               
                    $sql	= mysqli_query($db,"SELECT * FROM view_merchant_transaksi 
                    WHERE status_transaksi != 3 AND `kd_merchant` = '$id_merchant' AND `kd_merchant_employee` = '$id_merchant_employee' 
                    GROUP BY id_merchant_transaksi ORDER BY tgl_input_transaksi DESC LIMIT $limit, $offset"); 
                    $result = array();
                    	while($row = mysqli_fetch_array($sql)){
                    	     if($row['status_transaksi'] == 1){
                    	        $statustransaksi = "Sedang diproses";
                    	    }else if($row['status_transaksi'] == 2){
                    	        $statustransaksi = "Lunas";
                    	    }else{
                    	        $statustransaksi = "Dibatalkan";
                    	    }
                    	array_push($result,array(
                    		'id_merchant_transaksi'	            => $row['id_merchant_transaksi'],
                    		'nama_employee'      	            => str_replace("&#039;","'",$row['nama_employee']),
                    		'toko'               	            => str_replace("&#039;","'",$row['toko']),
                    		'telp_merchant'               	    => $row['telp_merchant'],
                    		'file_logo'               	        => $row['file_logo'],
                    		'no_nota'	    	                => $row['no_nota'],
                    		'kd_jenis_pembayaran'	    	    => $row['kd_jenis_pembayaran'],
                    		'no_antrian'	                    => $row['no_antrian'],
                    		'jumlah_item'      	                => $row['jumlah_item'],
                    		'status_print'      	            => $row['status_print'],
                    		'nama_produk'      	                => str_replace("&#039;","'",$row['nama_produk']),
                    		'keterangan_transaksi'      	    => $row['keterangan'],
                    		'catatan_barang'      	            => $row['catatan_pesanan'],
                    		'status_transaksi_ket'      	    => $statustransaksi,
                    		'status_transaksi'      	        => $row['status_transaksi'],
                    		'nama_jenis_pembayaran'	    	    => $row['nama_jenis_pembayaran'],
                    		'diskon_total'	    	            => $row['diskon_total'],
                    		'diskon_barang'      	            => $row['diskon_barang'],
                    		'nominal_sebelum_diskon'	        => "Rp " . number_format((double)$row['nominal_sebelum_diskon'],0,',','.'),
                    		'tagihan_nota'      	            => "Rp " . number_format((double)$row['tagihan_nota'],0,',','.'),
                    		'jumlah_bayar'      	            => "Rp " . number_format((double)$row['jumlah_bayar'],0,',','.'),
                    		'tgl_input_transaksi'	    	    => date_format(date_create($row['tgl_input_transaksi']), 'd M y, H:i A'),
                    		
                    		
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
        
        
        if(!empty($_GET['tanggal_awal'])){
            	    
            	    $tanggal_awal     = $_GET['tanggal_awal'];
            	    $tanggal_akhir     = $_GET['tanggal_akhir'];
               
                    $sql	= mysqli_query($db,"SELECT * FROM view_merchant_transaksi 
                    WHERE `kd_merchant` = '$id_merchant' AND DATE(tgl_input_transaksi) BETWEEN '$tanggal_awal' AND '$tanggal_akhir' 
                    GROUP BY id_merchant_transaksi ORDER BY tgl_input_transaksi DESC LIMIT $limit, $offset"); 
                    $result = array();
                    	while($row = mysqli_fetch_array($sql)){
                    	    if($row['status_transaksi'] == 1){
                    	        $statustransaksi = "Sedang diproses";
                    	    }else if($row['status_transaksi'] == 2){
                    	        $statustransaksi = "Lunas";
                    	    }else{
                    	        $statustransaksi = "Dibatalkan";
                    	    }
                    	array_push($result,array(
                    		'id_merchant_transaksi'	            => $row['id_merchant_transaksi'],
                    		'nama_employee'      	            => str_replace("&#039;","'",$row['nama_employee']),
                    		'toko'               	            => str_replace("&#039;","'",$row['toko']),
                    		'telp_merchant'               	    => $row['telp_merchant'],
                    		'file_logo'               	        => $row['file_logo'],
                    		'no_nota'	    	                => $row['no_nota'],
                    		'kd_jenis_pembayaran'	    	    => $row['kd_jenis_pembayaran'],
                    		'no_antrian'	                    => $row['no_antrian'],
                    		'jumlah_item'      	                => $row['jumlah_item'],
                    		'status_print'      	            => $row['status_print'],
                    		'nama_produk'      	                => str_replace("&#039;","'",$row['nama_produk']),
                    		'keterangan_transaksi'      	    => $row['keterangan'],
                    		'catatan_barang'      	            => $row['catatan_pesanan'],
                    		'status_transaksi_ket'      	    => $statustransaksi,
                    		'status_transaksi'      	        => $row['status_transaksi'],
                    		'nama_jenis_pembayaran'	    	    => $row['nama_jenis_pembayaran'],
                    		'diskon_total'	    	            => $row['diskon_total'],
                    		'diskon_barang'      	            => $row['diskon_barang'],
                    		'nominal_sebelum_diskon'	        => "Rp " . number_format((double)$row['nominal_sebelum_diskon'],0,',','.'),
                    		'tagihan_nota'      	            => "Rp " . number_format((double)$row['tagihan_nota'],0,',','.'),
                    		'jumlah_bayar'      	            => "Rp " . number_format((double)$row['jumlah_bayar'],0,',','.'),
                    		'tgl_input_transaksi'	    	    => date_format(date_create($row['tgl_input_transaksi']), 'd M y, H:i A'),
                    		
                    		
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
            	    
               
                    $sql	= mysqli_query($db,"SELECT * FROM view_merchant_transaksi 
                    WHERE status_transaksi != 3 AND `kd_merchant` = '$id_merchant' 
                    GROUP BY id_merchant_transaksi ORDER BY tgl_input_transaksi DESC LIMIT $limit, $offset"); 
                    $result = array();
                    	while($row = mysqli_fetch_array($sql)){
                    	     if($row['status_transaksi'] == 1){
                    	        $statustransaksi = "Sedang diproses";
                    	    }else if($row['status_transaksi'] == 2){
                    	        $statustransaksi = "Lunas";
                    	    }else{
                    	        $statustransaksi = "Dibatalkan";
                    	    }
                    	array_push($result,array(
                    		'id_merchant_transaksi'	            => $row['id_merchant_transaksi'],
                    		'nama_employee'      	            => str_replace("&#039;","'",$row['nama_employee']),
                    		'toko'               	            => str_replace("&#039;","'",$row['toko']),
                    		'telp_merchant'               	    => $row['telp_merchant'],
                    		'file_logo'               	        => $row['file_logo'],
                    		'no_nota'	    	                => $row['no_nota'],
                    		'kd_jenis_pembayaran'	    	    => $row['kd_jenis_pembayaran'],
                    		'no_antrian'	                    => $row['no_antrian'],
                    		'jumlah_item'      	                => $row['jumlah_item'],
                    		'status_print'      	            => $row['status_print'],
                    		'nama_produk'      	                => str_replace("&#039;","'",$row['nama_produk']),
                    		'keterangan_transaksi'      	    => $row['keterangan'],
                    		'catatan_barang'      	            => $row['catatan_pesanan'],
                    		'status_transaksi_ket'      	    => $statustransaksi,
                    		'status_transaksi'      	        => $row['status_transaksi'],
                    		'nama_jenis_pembayaran'	    	    => $row['nama_jenis_pembayaran'],
                    		'diskon_total'	    	            => $row['diskon_total'],
                    		'diskon_barang'      	            => $row['diskon_barang'],
                    		'nominal_sebelum_diskon'	        => "Rp " . number_format((double)$row['nominal_sebelum_diskon'],0,',','.'),
                    		'tagihan_nota'      	            => "Rp " . number_format((double)$row['tagihan_nota'],0,',','.'),
                    		'jumlah_bayar'      	            => "Rp " . number_format((double)$row['jumlah_bayar'],0,',','.'),
                    		'tgl_input_transaksi'	    	    => date_format(date_create($row['tgl_input_transaksi']), 'd M y, H:i A'),
                    		
                    		
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
	mysqli_close($db);

 ?>