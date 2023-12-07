<?php 
require_once('../../templates/koneksi.php');

	$idmerchant                 = $_GET['idmerchant'];
	$kd_merchant_employee       = $_GET['id_merchant_employee'];
	$id_merchant_transaksi       = $_GET['id_merchant_transaksi'];
	$total_pembayaran_sebelum   = 0;
    $total_pembayaran           = 0;
	$total_item                 = 0;
	
    $sql	= mysqli_query($db,"SELECT a.jumlah_produk, a.harga_produk, a.diskon, a.harga_setelah_diskon, b.nomeja FROM merchant_transaksi_detail a
    JOIN merchant_transaksi b ON b.id_merchant_transaksi = a.kd_merchant_transaksi
    WHERE a.status_transaksi_detail != '3' AND a.kd_merchant_transaksi = '$id_merchant_transaksi' AND a.kd_merchant = '$idmerchant' AND a.kd_merchant_employee = '$kd_merchant_employee'"); 

    if($sql->num_rows == 0){
        http_response_code(400);
        $respon['pesan'] = "Tidak ada data dikeranjang!\nKlik `Mengerti` untuk menutup pesan ini";
    	die(json_encode($respon)); 
        exit;
    }
    
    while($row = mysqli_fetch_array($sql)){
        $nomeja = $row['nomeja'];
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
    $result['nomeja']                         = $nomeja;
    
    echo json_encode($result);
    
            
    

mysqli_close($db);
?>