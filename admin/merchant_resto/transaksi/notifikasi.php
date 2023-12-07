<?php 
require_once('../../templates/koneksi.php');

	$idmerchant            = $_GET['idmerchant'];
	$kd_merchant_employee  = $_GET['id_merchant_employee'];
	$total_pembayaran_sebelum       = 0;
    $total_pembayaran   = 0;
	$total_item         = 0;
	
    $sql	= mysqli_query($db,"SELECT jumlah_produk, harga_produk, diskon, harga_setelah_diskon FROM merchant_transaksi_detail
    WHERE status_transaksi_detail != '3' AND kd_merchant_transaksi = '' AND kd_merchant = '$idmerchant' AND kd_merchant_employee = '$kd_merchant_employee'"); 

    if($sql->num_rows == 0){
        http_response_code(400);
        $respon['pesan'] = "Tidak ada data dikeranjang!\nKlik `Mengerti` untuk menutup pesan ini";
    	die(json_encode($respon)); 
        exit;
    }
    
    while($row = mysqli_fetch_array($sql)){
        $total_pembayaran_sebelum = $total_pembayaran_sebelum + ($row['harga_produk']*$row['jumlah_produk']);
        if($row['diskon'] == '0'){
        	$total_pembayaran   = $total_pembayaran + ($row['harga_produk']*$row['jumlah_produk']);
        	$total_item         = $total_item + $row['jumlah_produk'];
    	}else{
            $total_pembayaran   = $total_pembayaran + ($row['harga_setelah_diskon']*$row['jumlah_produk']);
            $total_item         = $total_item + $row['jumlah_produk'];
    	}
                	
    }
    
    $hitung                                   = $total_pembayaran_sebelum-$total_pembayaran;       
    
    $result['total_item']                     = $total_item;
    $result['total_belanja_sebelum']          = $total_pembayaran_sebelum;
    $result['total_belanja']                  = $total_pembayaran;
    $result['total_belanja_sebelum_format']   = "Rp " . number_format((double)$total_pembayaran_sebelum,0,',','.');
    $result['hemat']                          = "Rp " . number_format((double)$hitung,0,',','.');
    $result['total_format']                   = "Rp " . number_format((double)$total_pembayaran,0,',','.');
    
    echo json_encode($result);
    
            
    

mysqli_close($db);
?>