<?php 
    require_once('../../templates/koneksi.php');
    
    $limit                           = $_GET['limit'];
    $offset                          = $_GET['offset'];
	$id_merchant                     = $_GET['id_merchant'];
    $tanggal_awal                    = $_GET['tanggal_awal'];
        
        	    
        // $sql	= mysqli_query($db,"SELECT a.status_display_produk, a.id_merchant_history_stok, a.kd_merchant_produk, a.jenis_history, a.nama_produk, a.harga_produk, a.harga_beli, a.gambar_produk, a.status_konsi, a.nama_employee, a.telp_employee, a.nama_merchant, a.telp_merchant,a.file_logo,
        //	(SELECT d.stok_terakhir FROM view_merchant_history_stok d WHERE d.kd_merchant = '$id_merchant' AND d.kd_merchant_produk=a.kd_merchant_produk ORDER BY d.tanggal_history DESC LIMIT 1) AS stok_terakhir, 
        //	(SELECT b.tanggal_history FROM view_merchant_history_stok b WHERE b.kd_merchant = '$id_merchant' AND b.kd_merchant_produk=a.kd_merchant_produk AND DATE(b.tanggal_history) = '$tanggal_awal' ORDER BY b.tanggal_history DESC LIMIT 1) AS tanggal_history 
        //	FROM `view_merchant_history_stok` a WHERE a.kd_merchant = '$id_merchant' AND jenis_produk = '2' GROUP BY a.kd_merchant_produk LIMIT $limit, $offset");
		
        
        $sql	= mysqli_query($db,"SELECT a.status_display_produk, a.id_merchant_history_stok, a.kd_merchant_produk, a.jenis_history, a.nama_produk, a.harga_produk, a.harga_beli, a.gambar_produk, a.status_konsi, a.nama_employee, a.telp_employee, a.nama_merchant, a.telp_merchant,a.file_logo, 
        		gethistoristokakhir(a.kd_merchant_produk,'$id_merchant') as stok_terakhir,current_date() AS tanggal_history 
        		FROM `view_merchant_history_stok` a WHERE a.kd_merchant = '$id_merchant' AND jenis_produk = '2' GROUP BY a.kd_merchant_produk LIMIT $limit, $offset");

                $result = array();
                	while($row = mysqli_fetch_array($sql)){
                	array_push($result,array(
                		'id_merchant_history_stok'	        => $row['id_merchant_history_stok'],
                		'jenis_history'	                    => $row['jenis_history'],
                		'stok_setelah'	                    => $row['stok_terakhir'],
                		'status_konsi'	                    => $row['status_konsi'],
                		'nama_produk'      	                => str_replace("&#039;","'",$row['nama_produk']),
                		'gambar_produk'	                    => base_url()."dist/img/barang/".$row['gambar_produk'],
                		'status_display_produk'	            => $row['status_display_produk'],
                		'tanggal_history'	    	        => date_format(date_create($row['tanggal_history']), 'd M y, H:i A'),
                		'harga_beli_format'      	        => "Rp " . number_format((double)$row['harga_beli'],0,',','.'),
                		'harga_beli' 	                    => $row['harga_beli'],
                	));
                    }
                
                 if(isset($result[0])) {
        				    
        			echo json_encode($result);
        			
        		} else{
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
        	
        
	mysqli_close($db);

 ?>