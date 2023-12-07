<?php 
    require_once('../../templates/koneksi.php');
    
	$id_merchant                     = $_GET['id_merchant'];
	$id_merchant_employee            = $_GET['id_merchant_employee'];
	$level                           = $_GET['level'];
	$limit                           = $_GET['limit'];
	$offset                          = $_GET['offset'];
	
    if($level == 2){           
            $sql	= mysqli_query($db,"SELECT * FROM `merchant_transaksi` 
            WHERE status_transaksi ='3' AND `kd_merchant` = '$id_merchant' AND kd_merchant_employee = '$id_merchant_employee'
            ORDER BY tgl_input_transaksi DESC LIMIT $limit, $offset"); 
            $result = array();
            	while($row = mysqli_fetch_array($sql)){
            	     if($row['status_transaksi'] == 1){
            	        $statustransaksi = "Sudah jadi";
            	    }else if($row['status_transaksi'] == 2){
            	        $statustransaksi = "Sedang diproses";
            	    }else{
            	        $statustransaksi = "Dibatalkan";
            	    }
            	array_push($result,array(
            		'id_merchant_transaksi'	            => $row['id_merchant_transaksi'],
            		'no_nota'	    	                => $row['no_nota'],
            		'tgl_input_transaksi'	    	    => date_format(date_create($row['tgl_input_transaksi']), 'd M y, H:i A'),
            		'no_antrian'	                    => $row['no_antrian'],
            		'nominal_sebelum_diskon'	        => "Rp " . number_format((double)$row['nominal_sebelum_diskon'],2,',','.'),
            		'diskon'      	                    => $row['diskon'],
            		'tagihan_nota'      	            => "Rp " . number_format((double)$row['tagihan_nota'],2,',','.'),
            		'jumlah_bayar'      	            => "Rp " . number_format((double)$row['jumlah_bayar'],2,',','.'),
            		'jumlah_item'      	                => $row['jumlah_item'],
            		'status_print'      	            => $row['status_print'],
            		'keterangan'      	                => str_replace("&#039;","'",$row['keterangan']),
            		'status_transaksi_ket'      	    => $statustransaksi,
            		'status_transaksi'      	        => $row['status_transaksi'],
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
    
    $sql	= mysqli_query($db,"SELECT * FROM `merchant_transaksi` 
            WHERE status_transaksi ='3' AND `kd_merchant` = '$id_merchant' AND kd_merchant_employee = '$id_merchant_employee'
            ORDER BY tgl_input_transaksi DESC LIMIT $limit, $offset"); 
            $result = array();
            	while($row = mysqli_fetch_array($sql)){
            	     if($row['status_transaksi'] == 1){
            	        $statustransaksi = "Sudah jadi";
            	    }else if($row['status_transaksi'] == 2){
            	        $statustransaksi = "Sedang diproses";
            	    }else{
            	        $statustransaksi = "Dibatalkan";
            	    }
            	array_push($result,array(
            		'id_merchant_transaksi'	            => $row['id_merchant_transaksi'],
            		'no_nota'	    	                => $row['no_nota'],
            		'tgl_input_transaksi'	    	    => date_format(date_create($row['tgl_input_transaksi']), 'd M y, H:i A'),
            		'no_antrian'	                    => $row['no_antrian'],
            		'nominal_sebelum_diskon'	        => "Rp " . number_format((double)$row['nominal_sebelum_diskon'],2,',','.'),
            		'diskon'      	                    => $row['diskon'],
            		'tagihan_nota'      	            => "Rp " . number_format((double)$row['tagihan_nota'],2,',','.'),
            		'jumlah_bayar'      	            => "Rp " . number_format((double)$row['jumlah_bayar'],2,',','.'),
            		'jumlah_item'      	                => $row['jumlah_item'],
            		'status_print'      	            => $row['status_print'],
            		'keterangan'      	                => str_replace("&#039;","'",$row['keterangan']),
            		'status_transaksi_ket'      	    => $statustransaksi,
            		'status_transaksi'      	        => $row['status_transaksi'],
            	));
                }
            
             if(isset($result[0])) {
    				    
    			echo json_encode($result);
    			
    		}  else{
                    http_response_code(400);
                    $respon['pesan'] = "Tidak ada data yang ditampilkan!\nKlik `Mengerti` untuk menutup pesan ini";
                    echo json_encode($respon);
    		}
    
}
        
	mysqli_close($db);

 ?>