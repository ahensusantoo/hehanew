<?php 
require_once('../../templates/koneksi.php');

	$id_merchant_kategori_produk     = $_POST['id_merchant_kategori_produk'];
	$nama_kategori                   = $_POST['nama_kategori'];
	
	$cek_kode_kategori =$db->query(" SELECT * FROM merchant_kategori_produk 
                                WHERE kode_kategori ='$kode_kategori'
                                AND kd_merchant = '$idmerchant' AND status_remove_kategori = 'N'")->num_rows;

	$query = mysqli_query($db, "UPDATE merchant_kategori_produk SET nama_kategori = '$nama_kategori' WHERE id_merchant_kategori_produk = '$id_merchant_kategori_produk'");
	
	if ($cek_kode_kategori > 0){
	    http_response_code(400);
        $respon['pesan'] = "Kode kategori ".$kode_kategori." Sudah ada harap diganti!\nKlik `Mengerti` untuk menutup pesan ini";
        die(json_encode($respon));
	} else {
	    if ($query){
    		$respon['pesan'] = "Kategori ".$nama_kategori." berhasil dirubah";
    		die(json_encode($respon));
	    } else{ 
    	    http_response_code(400);
            $respon['pesan'] = "Gagal merubah kategori baru!\nKlik `Mengerti` untuk menutup pesan ini";
    		die(json_encode($respon)); 
	    }   
	}

mysqli_close($db);
?>