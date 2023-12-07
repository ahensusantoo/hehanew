<?php

// echo "<pre>";
// echo print_r($_POST);
// die();

require_once '../../templates/koneksi.php';
sessionLoginMerchantEmployee();


// ======================================================================
// ======================= TAMBAH =======================================
// ======================================================================

if (isset($_POST['tambah_produk'])){

    $nama_produk	 	= addslashes($_POST['nama_produk']);
    $kategori 			= addslashes($_POST['kategori']);
    $harga_produk 		= preg_replace("/[^0-9]/", "", antiSQLi($_POST['harga_beli_produk']) );
    $harga_jual 		= preg_replace("/[^0-9]/", "", antiSQLi($_POST['harga_jual_produk']) );
    $diskon_produk 		= preg_replace("/[^0-9]/", "", antiSQLi($_POST['diskon_produk']) );
    $konsi 				= addslashes($_POST['konsi']);
    $status_tampil 	    = addslashes($_POST['status_tampil']);
    $stok_produk 		= addslashes($_POST['stok_produk']);
    $jenis_stock 		= addslashes($_POST['jenis_stock']);
    $kode_produk 		= addslashes($_POST['kode_produk']);
   
    if($diskon_produk == ""){
    	$diskon_produk = 0;
    }
    if($jenis_stock != "2"){
        $stok_produk = 0;
    }
    
    // var_dump($harga_jual);die();
    
    $cek_kode_produk =$db->query(" SELECT * FROM merchant_produk 
                                    WHERE kode_produk ='".strtoupper($kode_produk)."'
                                    AND kd_merchant = '$_SESSION[kd_merchant]' ")->num_rows;

   
    if($cek_kode_produk > 0 ){ 
        $_SESSION['notifikasi']['fail'] = "Nama Product ".$kode_produk." Sudah Digunakan";
        header('Location: '.base_url().'merchant.php?page=product&action=tambah');
        exit();
    }else if($harga_jual < $harga_produk){
        $_SESSION['notifikasi']['fail'] = "Harga Jual Product Lebih Kecil Dari Harga Jual ";
        header('Location: '.base_url().'merchant.php?page=product&action=tambah');
        exit();
    }else{
        if (isset($_FILES['gambar_produk'])) {
    
    		// FORMAT DIIZINKAN
    	    $format_diizinkan["image/jpeg"] 		= "";
    	    $format_diizinkan["image/jpg"] 			= "";
    	    $format_diizinkan["image/png"] 			= "";
    	    // END FORMAT DIIZINKAN
    	    	
    		if (isset($format_diizinkan[$_FILES['gambar_produk']['type']])){
    
    			$nama_file 	= $_FILES['gambar_produk']['name'];
    			$lokasi 	= $_FILES['gambar_produk']['tmp_name'];
    			$nama_file 	= substr(md5(rand()), 0, 10).$nama_file;
    			
    	    
    			
    			if(move_uploaded_file($lokasi, "../../dist/img/barang/".$nama_file)){
    				//$id_merchant_produk = createID('id_merchant_produk', 'merchant_produk', 'MP');
    				$sess_kd_merchant = $_SESSION['kd_merchant'];
    				$sess_id_merchant_employee = $_SESSION['id_merchant_employee'];
    	        	$query = " CALL new_barang('$sess_kd_merchant', '$sess_id_merchant_employee', '$kategori', '$nama_produk', 
    	        	                '$harga_jual', '$diskon_produk', '$nama_file', '$konsi', '$status_tampil', '$stok_produk', '$harga_produk', '".strtoupper($kode_produk)."','$jenis_stock')";
    	        	$sql = $db->query($query);
    
    	        	if($sql){
    	        		//Jika Berhasil
    	        		$_SESSION['notifikasi']['success'] = "Product ".$nama_produk." berhasil ditambahkan";
    	        		header('Location: '.base_url().'merchant.php?page=product&action=kelola');
    	        		exit();
    	    		}else{
    		        	//Jika Gagal
    		        	$_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
    		        	header('Location: '.base_url().'merchant.php?page=product&action=tambah');
    		        	exit();
    		    	}    
    			}else{
    				//Jika Gagal
    	        	$_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
    	        	header('Location: '.base_url().'merchant.php?page=product&action=tambah');
    	        	exit();
    			}
    		}else{
    		    $nama_file = null;
    		    $sess_kd_merchant = $_SESSION['kd_merchant'];
    		    $sess_id_merchant_employee = $_SESSION['id_merchant_employee'];
    		    $query = " CALL new_barang('$sess_kd_merchant', '$sess_id_merchant_employee', '$kategori', '$nama_produk', 
            	                '$harga_jual', '$diskon_produk', '$nama_file', '$konsi', '$status_tampil', '$stok_produk', '$harga_produk', '".strtoupper($kode_produk)."','$jenis_stock')";
            	$sql = $db->query($query);
    
            	if($sql){
            		//Jika Berhasil
            		$_SESSION['notifikasi']['success'] = "Product ".$nama_produk." berhasil ditambahkan";
            		header('Location: '.base_url().'merchant.php?page=product&action=kelola');
            		exit();
        		}else{
    	        	//Jika Gagal
    	        	$_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
    	        	header('Location: '.base_url().'merchant.php?page=product&action=tambah');
    	        	exit();
    	    	}    
    		}
    	}
    }//tutup else cheack harga
}


// ======================================================================
// ======================= UPDATE =======================================
// ======================================================================

if (isset($_POST['edit_produk'])){
    
    $sess_kd_merchant = $_SESSION['kd_merchant'];

    $id_merchant_produk = addslashes(enkripsiDekripsi($_POST['id_merchant_produk'],'dekripsi'));
    $nama_produk	 	= addslashes($_POST['nama_produk']);
    $kategori 			= addslashes($_POST['kategori']);
    $harga_produk 		= preg_replace("/[^0-9]/", "", antiSQLi($_POST['harga_beli_produk']) );
    $harga_jual 		= preg_replace("/[^0-9]/", "", antiSQLi($_POST['harga_jual_produk']) );
    $diskon_produk 		= preg_replace("/[^0-9]/", "", antiSQLi($_POST['diskon_produk']) );
    $konsi 				= addslashes($_POST['konsi']);
    $status_tampil 	    = addslashes($_POST['status_tampil']);
    $stok_produk 		= addslashes($_POST['stok_produk']);
    $jenis_stock 		= addslashes($_POST['jenis_stock']);
    $kode_produk 		= addslashes($_POST['kode_produk']);

    if($diskon_produk == ""){
    	$diskon_produk = 0;
    }
    if($jenis_stock != "2"){
        $stok_produk = 0;
    }
    
    
    $cek_kode_produk =$db->query(" SELECT * FROM merchant_produk 
                                    WHERE kode_produk ='".strtoupper($kode_produk)."'
                                    AND kd_merchant = '$_SESSION[kd_merchant]'
                                    AND id_merchant_produk != '$id_merchant_produk' ")->num_rows;
    
    if($cek_kode_produk > 0 ){ 
        $_SESSION['notifikasi']['fail'] = "Nama Product ".$kode_produk." Sudah Digunakan";
        header('Location: '.base_url().'merchant.php?page=product&action=edit');
        exit();
    }
    $query = "UPDATE merchant_produk 
				SET kd_merchant= '$sess_kd_merchant', kd_merchant_kategori='$kategori', nama_produk='$nama_produk', harga_produk='$harga_jual', diskon='$diskon_produk', status_konsi='$konsi', status_display_produk='$status_tampil' 
				WHERE id_merchant_produk='$id_merchant_produk' ";
    $sqltanpa = $db->query($query);

    if (!$sqltanpa){
    	$_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
		header('Location: '.base_url().'merchant.php?page=product&action=edit&id='.$_POST['id_merchant_produk']);
		exit();
	}
	else{
		if (empty($_FILES['gambar_produk']['tmp_name'])){
			$_SESSION['notifikasi']['success'] = "Data Berhasil Di Update";
			header('Location: '.base_url().'merchant.php?page=product&action=kelola');
			exit();
		}
	}

	// FORMAT DIIZINKAN
    $format_diizinkan["image/jpeg"] 		= "";
    $format_diizinkan["image/jpg"] 			= "";
    // END FORMAT DIIZINKAN
    $nama_file 	= $_FILES['gambar_produk']['name'];
	$lokasi 	= $_FILES['gambar_produk']['tmp_name'];
	$nama_file 	= substr(md5(rand()), 0, 10).$nama_file;
	if(move_uploaded_file($lokasi, "../../dist/img/barang/".$nama_file)){
		//proses hapus foto lama
		$qry = "SELECT * FROM merchant_produk WHERE id_merchant_produk='$id_merchant_produk'";
		$data_produk = $db->query($qry)->fetch_assoc();
		unlink("../../dist/img/barang/".$data_produk['gambar_produk']);

		//proses update.
		$sess_kd_merchant = $_SESSION['kd_merchant'];
		$query_update = "UPDATE merchant_produk 
       					SET  gambar_produk='$nama_file'
       					WHERE id_merchant_produk='$id_merchant_produk'";
    	$sql = $db->query($query_update);

    	
	}else{
		$_SESSION['notifikasi']['fail'] = "Foto Gagal di Update";
		header('Location: '.base_url().'merchant.php?page=product&action=edit&id='.$_POST['id_merchant_produk']);
	}

    if($sql){
    	$_SESSION['notifikasi']['success'] = "Data Berhasil Di Update";
		header('Location: '.base_url().'merchant.php?page=product&action=kelola');
	}else{
		$_SESSION['notifikasi']['fail'] = "Foto Gagal di Update";
		header('Location: '.base_url().'merchant.php?page=product&action=edit&id='.$_POST['id_merchant_produk']);
	}
    
    // $cek_nama_produk =$db->query("SELECT * FROM merchant_produk WHERE nama_produk ='$nama_produk' AND id_merchant_produk!='$id_merchant_produk' ")->num_rows;
    
    // if($cek_nama_produk > 0){ 
    //     $_SESSION['notifikasi']['fail'] = "Nama Product ".$nama_produk." Sudah Digunakan";
    //     header('Location: '.base_url().'merchant.php?page=product&action=edit');
    //     exit();
    // } 
 //    if (isset($_FILES['gambar_produk'])) {
	// 	// FORMAT DIIZINKAN
	//     $format_diizinkan["image/jpeg"] 		= "";
	//     $format_diizinkan["image/jpg"] 			= "";
	//     // END FORMAT DIIZINKAN
	// 	if (isset($format_diizinkan[$_FILES['gambar_produk']['type']])){
	// 		$nama_file 	= $_FILES['gambar_produk']['name'];
	// 		$lokasi 	= $_FILES['gambar_produk']['tmp_name'];
	// 		$nama_file 	= substr(md5(rand()), 0, 10).$nama_file;

	// 		if (!empty($_FILES['gambar_produk']['tmp_name'])){
			    
	// 			if(move_uploaded_file($lokasi, "../../assets/product/".$nama_file)){
	// 				//proses hapus foto lama
	// 				$qry = "SELECT * FROM merchant_produk WHERE id_merchant_produk='$id_merchant_produk'";
	// 				$data_produk = $db->query($qry)->fetch_assoc();
	// 				unlink("../../assets/product/".$data_produk['gambar_produk']);

	// 				//proses update.
	// 				$sess_kd_merchant = $_SESSION['kd_merchant'];
	// 				$query_update = "UPDATE merchant_produk 
 //                   					SET kd_merchant= '$sess_kd_merchant', kd_merchant_kategori='$kategori', nama_produk='$nama_produk', harga_produk='$harga_produk', diskon='$diskon_produk',  gambar_produk='$nama_file', status_konsis='$konsi', status_display_produk='Y' 
 //                   					WHERE id_merchant_produk='$id_merchant_produk'";
	// 	        	$sql = $db->query($query_update);

	// 	        	if($sql){
	// 	        		//Jika Berhasil
	// 	        		$_SESSION['notifikasi']['success'] = "Nama Kategory ".$nama_kategori." berhasil ditambahkan";
	// 	        		header('Location: '.base_url().'merchant.php?page=product&action=kelola');
	// 	        		exit();
	// 	    		}else{
	// 		        	//Jika Gagal
	// 		        	$_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
	// 		        	header('Location: '.base_url().'merchant.php?page=product&action=edit&id='.$_POST['id_merchant_produk']);
	// 		        	exit();
	// 		    	}    
	// 			}else{
	// 				//Jika Gagal
	// 	        	$_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
	// 	        	// header('Location: '.base_url().'merchant.php?page=product&action=edit'.$id_merchant_produk);
	// 	        	exit();
	// 			}
	// 		}else{
	// 			//update tanpa foto
	// 		}
	// 	}
	// }
}



// ======================================================================
// =======================  HAPUS  ======================================
// ======================================================================

if(isset($_GET['product_hapus'])){
    $id_produk = enkripsiDekripsi($_GET['id'],'dekripsi');
    $nama_produk = addslashes($_GET['nama_produk']);
    
    $query = "UPDATE merchant_produk SET status_remove_produk='Y', status_display_produk='N' WHERE id_merchant_produk='$id_produk' AND kd_merchant = '$_SESSION[kd_merchant]' ";
    // echo $query;
    $sql = $db->query($query);

    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Produk ".$nama_produk." Berhasil Di Hapus";
        header('Location: '.base_url().'merchant.php?page=product&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'merchant.php?page=product&action=kelola');
        exit();
    }
}

