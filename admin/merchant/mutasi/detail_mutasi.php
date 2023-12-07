<?php 
    require_once('../../templates/koneksi.php');
    
	$id_merchant_mutasi_stok      = $_GET['id_merchant_mutasi_stok'];
	$q                        = $_GET['q'];
	$limit                        = $_GET['limit'];
	$offset                       = $_GET['offset'];
	

	
    $sql1	= mysqli_query($db,"SELECT * FROM merchant_mutasi_stok a
    JOIN merchant_employee b ON a.kd_merchant_employee = b.id_merchant_employee
    WHERE a.id_merchant_mutasi_stok = '$id_merchant_mutasi_stok'")->fetch_assoc(); 
       if($sql1['jenis_mutasi'] == '2'){
           $mutasi = 'Keluar';
       }else{
           $mutasi = 'Masuk';
       } 
        $data['id_merchant_mutasi_stok']	     = $sql1['id_merchant_mutasi_stok'];
        $data['id_pretty']	                     = id_ke_struk($sql1['id_merchant_mutasi_stok']);
        $data['tanggal_mutasi']	                 = $sql1['tanggal_mutasi'];
        $data['nama_employee']	    	         = str_replace("&#039;","'",$sql1['nama_employee']);
        $data['keterangan_mutasi']      	     = str_replace("&#039;","'",$sql1['keterangan_mutasi']);
        $data['jenis_mutasi']	                 = $sql1['jenis_mutasi'];
        $data['jenis_format']	                 = $mutasi;
        $data['status_rmv_mutasi']	             = $sql1['status_rmv_mutasi'];
        $data['tanggal_mutasi'] = date_format(date_create($sql1['tanggal_mutasi']), 'd M y, H:i A');
        
    $data_transaksi = $data;
    if(empty($q)){        
        $sql	= mysqli_query($db,"SELECT * FROM merchant_mutasi_detail a
        JOIN merchant_produk b ON a.kd_merchant_produk = b.id_merchant_produk
        WHERE a.kd_merchant_mutasi_stok='$id_merchant_mutasi_stok' ORDER BY id_merchant_mutasi_detail DESC LIMIT $limit, $offset"); 
            $result = array();
            	while($row = mysqli_fetch_array($sql)){
            	array_push($result,array(
            		'id_merchant_mutasi_detail'	        => $row['id_merchant_mutasi_detail'],
            		'jumlah_mutasi'                     => $row['jumlah_mutasi'],
            		'nama_produk'      	                => str_replace("&#039;","'",$row['nama_produk']),
            		'harga_produk'	                    => "Rp " . number_format((double)$row['harga_produk'],2,',','.'),
            		'status_konsi'	                    => $row['status_konsi'],
            		'gambar_produk'	                    => base_url()."dist/img/barang/".$row['gambar_produk'],
            	));
                }
    }else{
        $sql	= mysqli_query($db,"SELECT * FROM merchant_mutasi_detail a
        JOIN merchant_produk b ON a.kd_merchant_produk = b.id_merchant_produk
        WHERE a.kd_merchant_mutasi_stok='$id_merchant_mutasi_stok' AND b.nama_produk LIKE '%$q%' ORDER BY id_merchant_mutasi_detail DESC LIMIT $limit, $offset"); 
            $result = array();
            	while($row = mysqli_fetch_array($sql)){
            	array_push($result,array(
            		'id_merchant_mutasi_detail'	        => $row['id_merchant_mutasi_detail'],
            		'jumlah_mutasi'                     => $row['jumlah_mutasi'],
            		'nama_produk'      	                => str_replace("&#039;","'",$row['nama_produk']),
            		'harga_produk'	                    => "Rp " . number_format((double)$row['harga_produk'],2,',','.'),
            		'status_konsi'	                    => $row['status_konsi'],
            		'gambar_produk'	                    => base_url()."dist/img/barang/".$row['gambar_produk'],
            	));
                }
    }            
            
    if(isset($sql1)){        
        
        if(isset($result[0])) {
            $result1['status'] = 0;
            $result1['result'] = $result;
            $result1['data_mutasi'] = $data_transaksi;
            echo json_encode($result1);			
		}  else{
		    
		    if($limit == 0){
		        $result1['status'] = 1;
                $result1['data_mutasi'] = $data_transaksi;
                
		    }else{
		        $result1['status'] = 2;
                $result1['data_mutasi'] = $data_transaksi;
		    }
		    echo json_encode($result1);
		}
	
    }else{
        http_response_code(400);
        $respon['pesan'] = "Tidak ada barang yang dimutasi!\nKlik `Mengerti` untuk menutup pesan ini";
        echo json_encode($respon);
    }

        
	mysqli_close($db);

 ?>