<?php 
    require_once('../../templates/koneksi.php');
    
	
	$kd_merchant_mutasi_stok                        = $_POST['id_merchant_mutasi_stok'];
	$kd_merchant_produk                             = $_POST['id_merchant_produk'];
	$jumlah_mutasi                                  = $_POST['jumlah_mutasi'];
	$id_merchant                                    = $_POST['id_merchant'];
	$id_merchant_employee                           = $_POST['id_merchant_employee'];
	$harga_beli                                     = $_POST['harga_beli'];
	$harga_jual                                     = $_POST['harga_jual'];
	$keterangan                                     = $_POST['keterangan'];
	$jenis_mutasi                                   = $_POST['jenis_mutasi'];
	$id_referensi                                   = id_ke_struk($kd_merchant_mutasi_stok);
	
	if(empty($kd_merchant_mutasi_stok)){
        http_response_code(400);
        $respon['pesan'] = "id mutasi hilang!Klik `Mengerti` untuk menutup pesan ini";
    	die(json_encode($respon)); 
        exit;
    }else if(empty($kd_merchant_produk)){
        http_response_code(400);
        $respon['pesan'] = "id produk hilang!Klik `Mengerti` untuk menutup pesan ini";
    	die(json_encode($respon)); 
        exit;
    }else if(empty($id_merchant)){
        http_response_code(400);
        $respon['pesan'] = "id merchant hilang!Klik `Mengerti` untuk menutup pesan ini";
    	die(json_encode($respon)); 
        exit;
    }else if(empty($id_merchant_employee)){
        http_response_code(400);
        $respon['pesan'] = "id merchant employee hilang!Klik `Mengerti` untuk menutup pesan ini";
    	die(json_encode($respon)); 
        exit;
    }else if(is_null($harga_beli)){
        http_response_code(400);
        $respon['pesan'] = "Harga beli tidak boleh kosong!Klik `Mengerti` untuk menutup pesan ini";
    	die(json_encode($respon)); 
        exit;
    }else if(empty($jenis_mutasi)){
        http_response_code(400);
        $respon['pesan'] = "Jenis mutasi hilang!Klik `Mengerti` untuk menutup pesan ini";
    	die(json_encode($respon)); 
        exit;
    }else if(empty($harga_jual)){
        http_response_code(400);
        $respon['pesan'] = "Harga jual tidak boleh kosong!\nKlik `Mengerti` untuk menutup pesan ini";
    	die(json_encode($respon)); 
        exit;
    }else{
	
        $sql	= mysqli_query($db,"Call tambah_mutasi('$kd_merchant_mutasi_stok', '$kd_merchant_produk', '$jumlah_mutasi', '$id_merchant', '$id_merchant_employee', '$harga_beli', '$keterangan', '$jenis_mutasi', '$harga_jual', '$id_referensi')"); 
        
        if($sql) {
                $respon['pesan'] = "berhasil menambah stok!\nKlik `Mengerti` untuk menutup pesan ini"; 
                echo json_encode($respon);
        	
        }  else{
                http_response_code(400);
                $respon['pesan'] = "Gagal menambah stok!\nKlik `Mengerti` untuk menutup pesan ini";
                echo json_encode($respon);
        }
    }

        
	mysqli_close($db);

 ?>