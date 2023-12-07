<?php 
require_once('../../templates/koneksi.php');

	$idmerchant     = $_POST['idmerchant'];
	$nama_kategori  = $_POST['nama_kategori'];
	$kode_kategori  = $_POST['kode_kategori'];
	
	$cek_kode_kategori =$db->query(" SELECT * FROM merchant_kategori_produk 
                                WHERE kode_kategori ='$kode_kategori'
                                AND kd_merchant = '$idmerchant' AND status_remove_kategori = 'N'")->num_rows;

	$idkategori = createID('id_merchant_kategori_produk', 'merchant_kategori_produk', 'KG');
	$query = mysqli_query($db, "INSERT INTO merchant_kategori_produk (id_merchant_kategori_produk , kd_merchant, nama_kategori, kode_kategori, tgl_input_kategori) 
	VALUES ('$idkategori', '$idmerchant', '$nama_kategori', '$kode_kategori', CURRENT_TIMESTAMP)");
               
    if ($cek_kode_kategori > 0){
	    http_response_code(400);
        $respon['pesan'] = "Kode kategori ".$kode_kategori." Sudah ada harap diganti!\nKlik `Mengerti` untuk menutup pesan ini";
        die(json_encode($respon));
	} else {
	    if ($query){
    		$respon['pesan'] = "Kategori ".$nama_kategori." berhasil ditambahkan";
    		die(json_encode($respon));
	    } else{ 
    	    http_response_code(400);
            $respon['pesan'] = "Gagal menambahkan kategori baru!\nKlik `Mengerti` untuk menutup pesan ini";
    		die(json_encode($respon)); 
	    }
	}                     

mysqli_close($db);
?>