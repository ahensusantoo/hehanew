<?php 
require_once('../../templates/koneksi.php');

    $idmerchant            = $_GET['id_merchant'];
    $kd_merchant_employee  = $_GET['id_merchant_employee'];
    $tanggal_awal          = $_GET['tanggal_awal'];
    $tanggal_akhir         = $_GET['tanggal_akhir'];
    $date1 =date_format(date_create($_GET['tanggal_awal']), 'd M y');
    $date2 =date_format(date_create($_GET['tanggal_akhir']), 'd M y');
    $level                 = $_GET['level'];
    $posisi                = 0;
    
    if($level == 2){
            	    

    $sql	= mysqli_query($db,"SELECT b.nama_merchant AS nama_toko, b.telp_merchant AS telp_kantor, b.file_logo, c.nama_employee AS nama_kasir, c.telp_employee AS telp_kasir, SUM(a.tagihan_nota) AS tagihan_nota, COUNT(a.id_merchant_transaksi) AS total_transaksi, SUM(a.tagihan_nota)/COUNT(a.id_merchant_transaksi) AS rata_rata FROM merchant_transaksi a
    JOIN merchant b ON b.id_merchant = a.kd_merchant
    JOIN merchant_employee c ON c.id_merchant_employee = a.kd_merchant_employee
    WHERE a.kd_merchant = '$idmerchant' AND a.status_transaksi = '2' AND a.status_pembayaran = '1' AND a.kd_merchant_employee = '$kd_merchant_employee' AND DATE(a.tgl_input_transaksi) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'")->fetch_assoc(); 
        $data['nama_toko']      = $sql['nama_toko'];
        $data['telp_kantor']    = $sql['telp_kantor'];
        $data['file_logo']      = $sql['file_logo'];
        $data['nama_kasir']     = $sql['nama_kasir'];
        $data['tanggal']        = $date1." - ".$date2;
        $data['telp_kasir']     = $sql['telp_kasir'];
        $data['total_transaksi']     = $sql['total_transaksi'];
        $data['tagihan_nota']       = "Rp " . number_format((double)$sql['tagihan_nota'],0,',','.');
        $data['rata_rata']          = "Rp " . number_format((double)$sql['rata_rata'],0,',','.');
    
       $data_transaksi = $data;
            
        $sql	= mysqli_query($db,"SELECT SUM(a.jumlah_produk) AS jumlah_produk, b.nama_produk, b.status_konsi, b.gambar_produk, b.id_merchant_produk FROM merchant_transaksi_detail a 
        JOIN merchant_produk b ON a.kd_merchant_produk = b.id_merchant_produk
        WHERE a.status_transaksi_detail = '2' AND a.kd_merchant = '$idmerchant' AND a.kd_merchant_employee = '$kd_merchant_employee' AND DATE(a.tgl_input_detail) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
        GROUP BY b.id_merchant_produk ORDER BY a.jumlah_produk DESC LIMIT 3"); 
        $result = array();
        	while($row = mysqli_fetch_array($sql)){
        	    
        	
        	 if($posisi == 0){
        	     $icon = "data_file/gold.png";
        	 }else if($posisi == 1){
        	     $icon = "data_file/silver.png";
        	 }else{
        	     $icon = "data_file/broze.png";
        	 }
        	 
            	array_push($result,array(
            		'id_merchant_produk'	            => $row['id_merchant_produk'],
            		'nama_produk'      	                => str_replace("&#039;","'",$row['nama_produk']),
                    'qty'                 	            => $row['jumlah_produk'],
            		'status_konsi'	                    => $row['status_konsi'],
            		'icon'	                            => $icon,
            		'gambar_produk'	                    => base_url()."dist/img/barang/".$row['gambar_produk'],
            	));
        	$posisi++;
            }
        
         if(isset($result[0])) {
				    $result1['result'] = $result;
				    $result1['data_transaksi'] = $data_transaksi;
				    
			echo json_encode($result1);
			
		}  else{
                http_response_code(400);
                $respon['pesan'] = "Tidak ada transaksi terkait!\nKlik `Mengerti` untuk menutup pesan ini";
                echo json_encode($respon);
		}
		
    }else{
        
        $sql	= mysqli_query($db,"SELECT b.nama_merchant AS nama_toko, b.telp_merchant AS telp_kantor, b.file_logo, c.nama_employee AS nama_kasir, c.telp_employee AS telp_kasir, SUM(a.tagihan_nota) AS tagihan_nota, COUNT(a.id_merchant_transaksi) AS total_transaksi, SUM(a.tagihan_nota)/COUNT(a.id_merchant_transaksi) AS rata_rata FROM merchant_transaksi a
    JOIN merchant b ON b.id_merchant = a.kd_merchant
    JOIN merchant_employee c ON c.id_merchant_employee = a.kd_merchant_employee
    WHERE a.kd_merchant = '$idmerchant' AND a.status_transaksi = '2' AND a.status_pembayaran = '1' AND DATE(a.tgl_input_transaksi) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'")->fetch_assoc(); 
        $data['nama_toko']      = $sql['nama_toko'];
        $data['telp_kantor']    = $sql['telp_kantor'];
        $data['file_logo']      = $sql['file_logo'];
        $data['nama_kasir']     = $sql['nama_kasir'];
        $data['tanggal']        = $date1." - ".$date2;
        $data['telp_kasir']     = $sql['telp_kasir'];
        $data['total_transaksi']     = $sql['total_transaksi'];
        $data['tagihan_nota']       = "Rp " . number_format((double)$sql['tagihan_nota'],0,',','.');
        $data['rata_rata']          = "Rp " . number_format((double)$sql['rata_rata'],0,',','.');
    
       $data_transaksi = $data;
            
        $sql	= mysqli_query($db,"SELECT SUM(a.jumlah_produk) AS jumlah_produk, b.nama_produk, b.status_konsi, b.gambar_produk, b.id_merchant_produk FROM merchant_transaksi_detail a 
        JOIN merchant_produk b ON a.kd_merchant_produk = b.id_merchant_produk
        WHERE a.status_transaksi_detail = '2' AND a.kd_merchant = '$idmerchant' AND DATE(a.tgl_input_detail) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
        GROUP BY b.id_merchant_produk ORDER BY a.jumlah_produk DESC LIMIT 3"); 
        $result = array();
        	while($row = mysqli_fetch_array($sql)){
        	    
        	
        	 if($posisi == 0){
        	     $icon = "data_file/gold.png";
        	 }else if($posisi == 1){
        	     $icon = "data_file/silver.png";
        	 }else{
        	     $icon = "data_file/broze.png";
        	 }
        	 
            	array_push($result,array(
            		'id_merchant_produk'	            => $row['id_merchant_produk'],
            		'nama_produk'      	                => str_replace("&#039;","'",$row['nama_produk']),
                    'qty'                 	            => $row['jumlah_produk'],
            		'status_konsi'	                    => $row['status_konsi'],
            		'icon'	                            => $icon,
            		'gambar_produk'	                    => base_url()."dist/img/barang/".$row['gambar_produk'],
            	));
        	$posisi++;
            }
        
         if(isset($result[0])) {
             
		    $result1['result'] = $result;
		    $result1['data_transaksi'] = $data_transaksi;
			echo json_encode($result1);
			
		}  else{
                http_response_code(400);
                $respon['pesan'] = "Tidak ada transaksi terkait!\nKlik `Mengerti` untuk menutup pesan ini";
                echo json_encode($respon);
		}
        
    }

mysqli_close($db);
?>