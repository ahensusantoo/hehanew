<?php

require_once '../../templates/koneksi.php';


// ======================================================================
// =========================== TAMBAH MERCHANT ==========================
// ======================================================================

if (isset($_POST['tambah_merchant'])) {
    
    if(antiSQLi($_POST['password_employee_cnf']) != antiSQLi($_POST['password_employee_cnf'])){
        $_SESSION['notifikasi']['fail'] = "Merchant '".$nama_merchant."'' gagal ditambahkan, password yang anda masukkan tidak sama";
		header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=kelola');
        exit();
    }
	
	// DATA MERCHANT
	$nama_merchant = antiSQLi($_POST['nama_merchant']);
	$kode_merchant = antiSQLi($_POST['kode_merchant']);
	$telp_merchant = antiSQLi($_POST['telp_merchant']);
	$email_merchant = antiSQLi($_POST['email_merchant']);
	$panjang_merchant = preg_replace("/[^0-9]/", "", antiSQLi($_POST['panjang_merchant']) );
	$lebar_merchant = preg_replace("/[^0-9]/", "", antiSQLi($_POST['lebar_merchant']) );
	$status_merchant = antiSQLi($_POST['status_merchant']);


	$id_merchant = createID('id_merchant', 'merchant', 'IM');
	$query = "INSERT INTO merchant (id_merchant, kode_merchant, nama_merchant, telp_merchant, email_merchant, panjang_merchant, lebar_merchant, file_logo, status_merchant) VALUES ('$id_merchant', '$kode_merchant', '$nama_merchant', '$telp_merchant', '$email_merchant', '$panjang_merchant', '$lebar_merchant', '', '$status_merchant')";


	// if (!$db->query($query)) {
	// 	$_SESSION['notifikasi']['fail'] = "Merchant '".$nama_merchant."'' gagal ditambahkan, mohon coba lagi";
	// 	header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=kelola');
 //        exit();
	// }


	//INPUT THUMBNAIL
// 	if (isset($_FILES['logo_merchant'])) {

// 		// FORMAT DIIZINKAN
// 	    $format_diizinkan["image/jpeg"] 		= "";
// 	    $format_diizinkan["image/jpg"] 			= "";
// 	    $format_diizinkan["image/png"] 			= "";
// 	    // END FORMAT DIIZINKAN

// 		if (isset($format_diizinkan[$_FILES['logo_merchant']['type']])){
// 			$nama_file 	= $_FILES['logo_merchant']['name'];
// 			$lokasi 	= $_FILES['logo_merchant']['tmp_name'];
// 			$nama_file 	= substr(md5(rand()), 0, 10).$nama_file;
// 			if(move_uploaded_file($lokasi, "../../assets/pengumuman/".$nama_file)){
// 				$db->query("UPDATE merchant SET file_logo='$nama_file' WHERE id_merchant='$id_merchant'");
// 			}
// 		}else{
// 			// Format tidak sesuai
// 		}

// 	}
    
    
	// DATA ADMIN
	// $nama_employee = antiSQLi($_POST['nama_employee']);
	// $username_employee = antiSQLi($_POST['username_employee']);
	// $password_employee = antiSQLi($_POST['password_employee']);
	// $password_employee_cnf = antiSQLi($_POST['password_employee_cnf']);
	// $telp_employee = antiSQLi($_POST['telp_employee']);
	// $email_employee = antiSQLi($_POST['email_employee']);

	// $password_employee = enkripsiDekripsi($password_employee,'enkripsi');
	// $id_merchant_employee = createID('id_merchant_employee', 'merchant_employee', 'IP');
	// $query = "INSERT INTO merchant_employee SET id_merchant_employee='$id_merchant_employee', kd_merchant='$id_merchant', nama_employee='$nama_employee', username_employee='$username_employee', password_employee='$password_employee', level_employee='1', telp_employee='$telp_employee', email_employee='$email_employee'";

	if($db->query($query)){
        $_SESSION['notifikasi']['success'] = "Data merchant berhasil ditambahkan";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=kelola');
        exit();
    }else{
    	$db->query("DELETE FROM merchant WHERE id_merchant='$id_merchant'");
    	unlink("../../assets/pengumuman/".$nama_file);
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=kelola');
        exit();
    }

}


// ======================================================================
// ========================= AKTIFKAN MERCHANT ==========================
// ======================================================================
if(isset($_GET['aktifkan_merchant'])){
    
    $id = enkripsiDekripsi(antiSQLi($_GET['aktifkan_merchant']), 'dekripsi');
    $sql = $db->query("UPDATE merchant SET status_aktif_merchant='Y' WHERE id_merchant='$id'");
    
    if($sql){
        echo "berhasil";
        exit();
    }else{
        echo "gagal";
        exit();
    }
    
}




// ======================================================================
// ======================= NONAKTIFKAN MERCHANT =========================
// ======================================================================

if(isset($_GET['nonaktifkan_merchant'])){
    
    $id = enkripsiDekripsi(antiSQLi($_GET['nonaktifkan_merchant']), 'dekripsi');
    $sql = $db->query("UPDATE merchant SET status_aktif_merchant='N' WHERE id_merchant='$id'");
    
    if($sql){
        echo "berhasil";
        exit();
    }else{
        echo "gagal";
        exit();
    }
    
}




// ======================================================================
// ====================== TAMBAH ADMIN MERCHANT =========================
// ======================================================================

if(isset($_POST['tambah_admin_merchant'])){
    
    $id_merchant = enkripsiDekripsi(antiSQLi($_POST['id_merchant']), 'dekripsi');
    $nama_employee = antiSQLi($_POST['nama_employee']);
    $username_employee = antiSQLi($_POST['username_employee']);
    $password_employee = antiSQLi($_POST['password_employee']);
    $password_employee_cnf = antiSQLi($_POST['password_employee_cnf']);
    $telp_employee = antiSQLi($_POST['telp_employee']);
    $email_employee = antiSQLi($_POST['email_employee']);
    
    
    if($password_admin != $password_admin_cnf){
        $_SESSION['notifikasi']['fail'] = "Password anda tidak sama, mohon coba lagi";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=kelola');
        exit();
    }

    $cek_username = $db->query("SELECT COUNT(*) AS jml FROM merchant_employee WHERE username_employee='$username_employee'")->fetch_assoc()['jml'];
    if ($cek_username > 0) {
        $_SESSION['notifikasi']['fail'] = "Akun gagal dibuat, username telah digunakan. Coba username lain";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=kelola');
        exit();
    }
    
    $password_enkripsi = enkripsiDekripsi($password_employee,'enkripsi');
    $id_admin = createID('id_merchant_employee', 'merchant_employee', 'IP');
    $query = "INSERT INTO merchant_employee SET id_merchant_employee='$id_admin', kd_merchant='$id_merchant', nama_employee='$nama_employee', username_employee='$username_employee', password_employee='$password_enkripsi', level_employee='1', telp_employee='$telp_employee', email_employee='$email_employee' ";
    $sql = $db->query($query);
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Admin berhasil ditambahkan";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=kelola');
        exit();
    }
    
}




// ======================================================================
// ==================== AKTIFKAN MERCHANT EMPLOYEE ======================
// ======================================================================
if(isset($_GET['aktifkan_merchant_employee'])){
    
    $id = enkripsiDekripsi(antiSQLi($_GET['aktifkan_merchant_employee']), 'dekripsi');
    $sql = $db->query("UPDATE merchant_employee SET status_aktif_employee='Y' WHERE id_merchant_employee ='$id'");
    
    if($sql){
        echo "berhasil";
        exit();
    }else{
        echo "gagal";
        exit();
    }
    
}




// ======================================================================
// ================== NONAKTIFKAN MERCHANT EMPLOYEE =====================
// ======================================================================

if(isset($_GET['nonaktifkan_merchant_employee'])){
    
    $id = enkripsiDekripsi(antiSQLi($_GET['nonaktifkan_merchant_employee']), 'dekripsi');
    $sql = $db->query("UPDATE merchant_employee SET status_aktif_employee='N' WHERE id_merchant_employee ='$id'");
    
    if($sql){
        echo "berhasil";
        exit();
    }else{
        echo "gagal";
        exit();
    }
    
}



// ======================================================================
// ======================= NONAKTIFKAN MERCHANT =========================
// ======================================================================

if(isset($_POST['edit_merchant_employee'])){
    
    $id = enkripsiDekripsi(antiSQLi($_POST['nama_employee']), 'dekripsi');
    $nama_employee = antiSQLi($_POST['nama_employee']);
    $username_employee = antiSQLi($_POST['username_employee']);
    $password_employee = antiSQLi($_POST['password_employee']);
    $password_employee_cnf = antiSQLi($_POST['password_employee_cnf']);
    $telp_employee = antiSQLi($_POST['telp_employee']);
    $email_employee = antiSQLi($_POST['email_employee']);
    
    if($password_employee != ""){
        if($password_admin != $password_admin_cnf){
            $_SESSION['notifikasi']['fail'] = "Password anda tidak sama, mohon coba lagi";
            header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=kelola');
            exit();
        }else{
            $password_enkripsi = enkripsiDekripsi($password_employee,'enkripsi');
            $query_password = " password_employee='".$password_enkripsi."' ";           
        }
    }else{
        $query_password = " ,password_employee='' ";
    }

    $cek_username = $db->query("SELECT COUNT(*) AS jml FROM merchant_employee WHERE username_employee='$username_employee' AND id_merchant_employee='$id' ")->fetch_assoc()['jml'];
    if ($cek_username > 0) {
        $_SESSION['notifikasi']['fail'] = "Akun gagal dibuat, username telah digunakan. Coba username lain";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=kelola');
        exit();
    }
    
    $query = "UPDATE merchant_employee SET kd_merchant='$id_merchant', nama_employee='$nama_employee', username_employee='$username_employee', telp_employee='$telp_employee', email_employee='$email_employee' WHERE id_merchant_employee='$id'";
    $sql = $db->query($query);
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Admin berhasil diubah";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=kelola');
        exit();
    }
    
    
    
}




// ======================================================================
// ============================ EDIT MERCHANT ===========================
// ======================================================================

if (isset($_POST['edit_merchant'])) {
    
    // DATA MERCHANT
    $id = enkripsiDekripsi(antiSQLi($_POST['id']), 'dekripsi');
	$nama_merchant = antiSQLi($_POST['nama_merchant']);
	$kode_merchant = antiSQLi($_POST['kode_merchant']);
	$telp_merchant = antiSQLi($_POST['telp_merchant']);
	$email_merchant = antiSQLi($_POST['email_merchant']);
	$panjang_merchant = preg_replace("/[^0-9]/", "", antiSQLi($_POST['panjang_merchant']) );
	$lebar_merchant = preg_replace("/[^0-9]/", "", antiSQLi($_POST['lebar_merchant']) );
	$status_merchant = antiSQLi($_POST['status_merchant']);


	$query = "UPDATE merchant SET kode_merchant='$kode_merchant', nama_merchant='$nama_merchant', telp_merchant='$telp_merchant', email_merchant='$email_merchant', panjang_merchant='$panjang_merchant', lebar_merchant='$lebar_merchant', file_logo='', status_merchant='$status_merchant' WHERE id_merchant='$id'";
    $sql = $db->query($query);
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Merchant berhasil diubah";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=kelola');
        exit();
    }
    
    
}



// ======================================================================
// ======================== TAMBAH KATEGORI PRODUK ======================
// ======================================================================

if(isset($_POST['tambah_merchant_kategori_produk'])){
    
    $id_merchant = enkripsiDekripsi($_POST['id_merchant'], 'dekripsi');
    $nama_kategori = antiSQLi($_POST['nama_kategori']);
    $kode_kategori = antiSQLi($_POST['kode_kategori']);
    $status_tampil = antiSQLi($_POST['status_tampil']);
    
    
    $cek_kode_kategori = $db->query("SELECT * 
                                    FROM merchant_kategori_produk 
                                    WHERE kode_kategori = '".strtoupper($kode_kategori)."'
                                        AND kd_merchant = '$id_merchant' ")->num_rows;

    if($cek_kode_kategori > 0){ 
        $_SESSION['notifikasi']['fail'] = "Kode ".$kode_kategori." Sudah Digunakan";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=1&filter1='.antiSQLi($_POST['id_merchant']));
        exit();
    }
    else {
        $id_merchant_kategori_produk = createID('id_merchant_kategori_produk', 'merchant_kategori_produk', 'KG');
        $sess_kd_merchant = $_SESSION['kd_merchant'];
        $query = "INSERT 
                    INTO merchant_kategori_produk (id_merchant_kategori_produk,kd_merchant, kode_kategori, nama_kategori, status_aktif_kategori) 
                    VALUES ('$id_merchant_kategori_produk', '$id_merchant', '".strtoupper($kode_kategori)."', '$nama_kategori', '$status_tampil')";
        $sql = $db->query($query);
    }
    
    if($sql){
        //Jika Berhasil
        $_SESSION['notifikasi']['success'] = "Nama Kategory ".$nama_kategori." berhasil ditambahkan";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=1&filter1='.antiSQLi($_POST['id_merchant']));
        exit();
    }else{
        //Jika Gagal
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=1&filter1='.antiSQLi($_POST['id_merchant']));
        exit();
    }    
    
}



// ======================================================================
// ======================== EDIT KATEGORI PRODUK ========================
// ======================================================================

if(isset($_POST['edit_merchant_kategori_produk'])){
    
    $id_merchant_kategori_produk = antiSQLi(enkripsiDekripsi($_POST['id_kategori'],'dekripsi'));
    $id_merchant = enkripsiDekripsi($_POST['merchant'],'dekripsi');
    $nama_kategori = antiSQLi($_POST['nama_kategori']);
    $kode_kategori = antiSQLi($_POST['kode_kategori']);
    $status_tampil = antiSQLi($_POST['status_tampil']);
    
    $sess_kd_merchant = $id_merchant;
    $cek_kode_kategori=$db->query("SELECT * FROM merchant_kategori_produk 
                                    WHERE kode_kategori ='".strtoupper($kode_kategori)."' 
                                        AND id_merchant_kategori_produk!='$id_merchant_kategori_produk'
                                            AND kd_merchant = '$id_merchant' ")->num_rows;
    if($cek_kode_kategori > 0 ){
        $_SESSION['notifikasi']['fail'] = "Kode Kategory ".$kode_kategori." Sudah Digunakan";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=1&filter1='.antiSQLi($_POST['merchant']));
        exit();   
    }else{
        $query = "  UPDATE merchant_kategori_produk 
                    SET kd_merchant= '$id_merchant' ,nama_kategori='$nama_kategori', kode_kategori='".strtoupper($kode_kategori)."', status_aktif_kategori='$status_tampil'  
                    WHERE id_merchant_kategori_produk='$id_merchant_kategori_produk'";
        $sql = $db->query($query);
    }

    if($sql){
        $_SESSION['notifikasi']['success'] = "Nama Kategory ".$nama_kategori." Di Update";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=1&filter1='.antiSQLi($_POST['merchant']));
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=1&filter1='.antiSQLi($_POST['merchant']));
        exit();
    }
    
}


// ======================================================================
// ======================== HAPUS KATEGORI PRODUK =======================
// ======================================================================

if(isset($_GET['hapus_merchant_kategori_produk'])){
    
    $id = enkripsiDekripsi($_GET['eid'],'dekripsi');
   
    $query = "  UPDATE merchant_kategori_produk 
                SET status_remove_kategori='Y' , status_aktif_kategori='N'
                WHERE id_merchant_kategori_produk='$id' ";
    $sql = $db->query($query);

    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Kategory Berhasil Hapus";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
}




// ======================================================================
// =========================== TAMBAH PRODUK ============================
// ======================================================================

if(isset($_POST['tambah_merchant_produk'])){
    
    $id_merchant	 	= enkripsiDekripsi($_POST['merchant'],'dekripsi');
    $id_supplier        = enkripsiDekripsi($_POST['supplier'],'dekripsi');
    $nama_produk	 	= antiSQLi($_POST['nama_produk']);
    $kategori 			= antiSQLi($_POST['kategori']);
    $harga_produk 		= preg_replace("/[^0-9]/", "", antiSQLi($_POST['harga_beli_produk']) );
    $harga_jual 		= preg_replace("/[^0-9]/", "", antiSQLi($_POST['harga_jual_produk']) );
    $diskon_produk 		= preg_replace("/[^0-9]/", "", antiSQLi($_POST['diskon_produk']) );
    $konsi 				= antiSQLi($_POST['konsi']);
    $status_tampil 	    = antiSQLi($_POST['status_tampil']);
    $stok_produk 		= antiSQLi($_POST['stok_produk']);
    $jenis_stock 		= antiSQLi($_POST['jenis_stock']);
    $kode_produk 		= antiSQLi($_POST['kode_produk']);
    $barcode            = antiSQLi($_POST['barcode']);
   
    if($diskon_produk == ""){
    	$diskon_produk = 0;
    }
    if($jenis_stock != "2"){
        $stok_produk = 0;
    }
    
    // var_dump($harga_jual);die();
    
    $cek_kode_produk =$db->query(" SELECT * FROM merchant_produk 
                                    WHERE kode_produk ='".strtoupper($kode_produk)."'
                                    AND kd_merchant = '$id_merchant' ")->num_rows;

   
    if($cek_kode_produk > 0 ){ 
        $_SESSION['notifikasi']['fail'] = "Nama Product ".$kode_produk." Sudah Digunakan";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=2&filter1='.antiSQLi($_POST['merchant']));
        exit();
    }else if($harga_jual < $harga_produk){
        $_SESSION['notifikasi']['fail'] = "Harga Jual Product Lebih Kecil Dari Harga Jual ";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=2&filter1='.antiSQLi($_POST['merchant']));
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
    				$sess_id_merchant_employee = $_SESSION['id_merchant_employee'];
    	        	$query = " CALL new_barang('$id_merchant', '$sess_id_merchant_employee', '$kategori', '$nama_produk', 
    	        	                '$harga_jual', '$diskon_produk', '$nama_file', '$konsi', '$status_tampil', '$stok_produk', '$harga_produk', '".strtoupper($kode_produk)."','$jenis_stock')";
    	        	$sql = $db->query($query);
    
    	        	if($sql){
    	        		//Jika Berhasil
    	        		$_SESSION['notifikasi']['success'] = "Product ".$nama_produk." berhasil ditambahkan";
    	        		header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=2&filter1='.antiSQLi($_POST['merchant']));
    	        		exit();
    	    		}else{
    		        	//Jika Gagal
    		        	$_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
    		        	header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=2&filter1='.antiSQLi($_POST['merchant']));
    		        	exit();
    		    	}    
    			}else{
    				//Jika Gagal
    	        	$_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
    	        	header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=2&filter1='.antiSQLi($_POST['merchant']));
    	        	exit();
    			}
    		}else{
    		    $nama_file = null;
    		    $sess_id_merchant_employee = $_SESSION['id_merchant_employee'];
    		    $query = " CALL new_barang('$id_merchant', '$sess_id_merchant_employee', '$kategori', '$nama_produk', 
            	                '$harga_jual', '$diskon_produk', '$nama_file', '$konsi', '$status_tampil', '$stok_produk', '$harga_produk', '".strtoupper($kode_produk)."','$jenis_stock', '$barcode', '$id_supplier')";
            	$sql = $db->query($query);
    
            	if($sql){
            		//Jika Berhasil
            		$_SESSION['notifikasi']['success'] = "Product ".$nama_produk." berhasil ditambahkan";
            		header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=2&filter1='.antiSQLi($_POST['merchant']));
            		exit();
        		}else{
    	        	//Jika Gagal
    	        	$_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
    	        	header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=2&filter1='.antiSQLi($_POST['merchant']));
    	        	exit();
    	    	}    
    		}
    	}
    }//tutup else cheack harga
    
}


// ======================================================================
// ============================ Import Excel Produk =====================
// ======================================================================

if(isset($_POST['import_produk_excel'])){

    require('../../plugins/input_excel/spreadsheet-reader/php-excel-reader/excel_reader2.php');
    require('../../plugins/input_excel/spreadsheet-reader/SpreadsheetReader.php');

    $kd_merchant = enkripsiDekripsi($_POST['merchant'], 'dekripsi');
    // var_dump($_POST);die();
    $nama = $_FILES['input_excel_produk']['name'];
    $target_dir = "../../plugins/input_excel/temp_file/".basename($_FILES['input_excel_produk']['name']);

    if(!move_uploaded_file($_FILES['input_excel_produk']['tmp_name'],$target_dir)){
        $_SESSION['notifikasi']['fail']= "lokasi penyimpanan excel salah !!!";
        header('Location: '.base_url().'general-cashier-stall.php?page=merchant&action=permerchant&action2=ProdukTambahByExcel&merchant='.$post['merchant']);
    }

    $Reader = new SpreadsheetReader($target_dir);

    $db->begin_transaction();

        $no = 1;
        $gagal = 0;
        $notif_gagal = "";


        foreach ($Reader as $Key => $kolom){
            // import data excel mulai baris ke-2 (karena ada header pada baris 1)
            if ($Key < 1) continue;         
            if ($kolom[0] == '') continue;

            $nama_produk            = antiSQLi(trim($kolom[0]));
            $kode_produk            = antiSQLi(trim($kolom[1]));
            $kode_kategory          = antiSQLi(trim($kolom[2]));
            $harga_beli_produk      = preg_replace("/[^0-9]/", "", antiSQLi(trim($kolom[3])) );
            $barcode                = antiSQLi(trim($kolom[4]));
            $kode_supplier          = antiSQLi(trim($kolom[5]));
            $harga_jual_produk      = preg_replace("/[^0-9]/", "", antiSQLi(trim($kolom[6])) );
            $jenis_stock            = antiSQLi(trim($kolom[7]));
            $stok_produk            = preg_replace("/[^0-9]/", "", antiSQLi(trim($kolom[8])) );
            $diskon_produk          = preg_replace("/[^0-9]/", "", antiSQLi(trim($kolom[9])) );
            $status_konsi           = antiSQLi(trim($kolom[10]));
            $status_tampil_prdouk   = antiSQLi(trim($kolom[11]));
            $nama_file = null;
    
            if($diskon_produk == ""){
                $diskon_produk = 0;
            }
            if($jenis_stock != "2"){
                $stok_produk = 0;
            }
            if($kode_supplier== ""){
                $kode_supplier = null;
            }


            $produk_kode_count = $db->query("SELECT  count(kode_produk) as jml FROM merchant_produk WHERE kode_produk = '".strtoupper($kode_produk)."' AND kd_merchant = '$kd_merchant' ")->fetch_assoc()['jml'];

            $id_kategory_produk = $db->query("SELECT  id_merchant_kategori_produk, kode_kategori  FROM merchant_kategori_produk WHERE kode_kategori = '".strtoupper($kode_kategory)."' AND kd_merchant = '$kd_merchant' ")->fetch_assoc();

            $id_supplier = $db->query("SELECT * FROM supplier WHERE kode_supplier = '".strtoupper($kode_supplier)."' ")->fetch_assoc();
            
            if($produk_kode_count > 0 ){
                $notif_gagal = $notif_gagal."- Kode produk ". $kode_produk." yang sudah digunakan!!!<br>";
                $gagal = 1;
            }else if($id_kategory_produk == ""){
                $notif_gagal = $notif_gagal."- Kode Kategori Produk ". $kode_kategory." yang anda masukkan tidak ada di database!!!<br>";
                $gagal = 1;
            }else if($id_supplier == ""){
                $notif_gagal = $notif_gagal."- Kode Supplier ". $kode_supplier." yang anda masukkan tidak ada di database!!!<br>";
                $gagal = 1;
            }else if($harga_beli_produk > $harga_jual_produk ){
                $notif_gagal = $notif_gagal."- Harga Beli '".$nama_produk."' Tidak boleh Lebih Besar (>) dari Harga jual produk!!!<br>";
                $gagal = 1;
            }else if($jenis_stock != "1" && $jenis_stock != "2"){
                $notif_gagal = $notif_gagal."- Jenis Produk ". $nama_produk." yang anda masukkan tidak sesuai dengan syarat!!!<br>";
                $gagal = 1;
            }else if($status_konsi != "Y" && $status_konsi != "N"){
                $notif_gagal = $notif_gagal."- Status Konsi ". $nama_produk." yang anda masukkan tidak sesuai dengan syarat!!!<br>";
                $gagal = 1;
            }else if($status_tampil_prdouk != "Y" && $status_tampil_prdouk != "N"){
                $notif_gagal = $notif_gagal."- Status Tampil produk ". $nama_produk." yang anda masukkan tidak sesuai dengan syarat!!!<br>";
                $gagal = 1;
            }

                $id_produk = $db->query("SELECT createID('merchant_produk') as id_merchant_produk")->fetch_assoc()['id_merchant_produk'];
                $id_merchant_stok = $db->query("SELECT createID('merchant_stok') as id_merchant_stok")->fetch_assoc()['id_merchant_stok'];
                $id_merchant_history_stok = $db->query("SELECT createID('merchant_history_stok') as id_merchant_history_stok")->fetch_assoc()['id_merchant_history_stok'];
                // print_r("<pre>"); print_r($id_produk); die();

            $excel[] = $db->query("
                INSERT INTO merchant_produk SET id_merchant_produk = '$id_produk', kd_merchant = '$kd_merchant', kd_merchant_kategori = '$id_kategory_produk[id_merchant_kategori_produk]', nama_produk = '$nama_produk', harga_produk = '$harga_jual_produk', diskon = '$diskon_produk', gambar_produk = '$nama_file', status_konsi = '$status_konsi', status_display_produk = '$status_tampil_prdouk', kode_produk = '$kode_produk', jenis_produk = '$jenis_stock', barcode_produk = '$barcode', id_supplier = '$id_supplier[id_supplier]' 
            ");

            $excel[] = $db->query("
                INSERT INTO merchant_stok (`id_merchant_stok` , `kd_merchant`, `kd_merchant_produk`, `stok_saat_ini`) VALUES ('$id_merchant_stok', '$kd_merchant', '$id_produk', '$stok_produk');
            ");

            $excel[] = $db->query("
                INSERT INTO merchant_history_stok (`id_merchant_history_stok`, `kd_merchant`, `kd_merchant_produk`, `kd_merchant_employee`, `stok_sebelum`, `masuk`, `keluar`, `stok_setelah`, `harga_beli`, `keterangan`, `jenis_history`) VALUES ('$id_merchant_history_stok', '$kd_merchant', '$id_produk', '$_SESSION[id_merchant_employee]', '0', '0', '0', '$stok_produk', '$harga_beli_produk', 'Barang baru', '0');
            ");

            // print_r("<pre>"); print_r($excel); die();

        }//tutup foreach
    
    if($gagal > 0){
        $db->rollback();
        $_SESSION['notifikasi']['fail'] = $notif_gagal;
        unlink($target_dir);
        header('Location: '.base_url().'general-cashier-stall.php?page=merchant&action=permerchant&action2=ProdukTambahByExcel&merchant='.$_POST['merchant']);
        exit;
    }
    
    if(in_array(false, $excel) OR in_array(0, $excel)){
        $db->rollback();
        $_SESSION['notifikasi']['fail'] = "Gagal Upload Data !!!";
        unlink($target_dir);
        header('Location: '.base_url().'general-cashier-stall.php?page=merchant&action=permerchant&action2=ProdukTambahByExcel&merchant='.$_POST['merchant']);
    }else{
        $db->commit();
        unlink($target_dir);
        $_SESSION['notifikasi']['success'] = "Product berhasil ditambahkan";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=2&filter1='.antiSQLi($_POST['merchant']));
    }
    exit;
}



// ======================================================================
// ============================ EDIT PRODUK =============================
// ======================================================================

if(isset($_POST['edit_merchant_produk'])){
    
    // $sess_kd_merchant = $_SESSION['kd_merchant'];
    $sess_kd_merchant	= enkripsiDekripsi($_POST['merchant'],'dekripsi');
    $id_merchant_produk = antiSQLi(enkripsiDekripsi($_POST['id_merchant_produk'],'dekripsi'));
    $nama_produk	 	= antiSQLi($_POST['nama_produk']);
    $kategori 			= antiSQLi($_POST['kategori']);
    $harga_produk 		= preg_replace("/[^0-9]/", "", antiSQLi($_POST['harga_beli_produk']) );
    $harga_jual 		= preg_replace("/[^0-9]/", "", antiSQLi($_POST['harga_jual_produk']) );
    $diskon_produk 		= preg_replace("/[^0-9]/", "", antiSQLi($_POST['diskon_produk']) );
    $konsi 				= antiSQLi($_POST['konsi']);
    $status_tampil 	    = antiSQLi($_POST['status_tampil']);
    $stok_produk 		= antiSQLi($_POST['stok_produk']);
    $kode_produk 		= antiSQLi($_POST['kode_produk']);

    if($diskon_produk == ""){
    	$diskon_produk = 0;
    }
    if($jenis_stock != "2"){
        $stok_produk = 0;
    }
    
    
    $cek_kode_produk =$db->query(" SELECT * FROM merchant_produk 
                                    WHERE kode_produk ='".strtoupper($kode_produk)."'
                                    AND kd_merchant = '$sess_kd_merchant'
                                    AND id_merchant_produk != '$id_merchant_produk' ")->num_rows;
    
    if($cek_kode_produk > 0 ){ 
        $_SESSION['notifikasi']['fail'] = "Nama Product ".$kode_produk." Sudah Digunakan";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=2&filter1='.antiSQLi($_POST['merchant']));
        exit();
    }
    $query = "UPDATE merchant_produk 
				SET kd_merchant= '$sess_kd_merchant', kd_merchant_kategori='$kategori', nama_produk='$nama_produk', harga_produk='$harga_jual', diskon='$diskon_produk', status_konsi='$konsi', status_display_produk='$status_tampil'
				WHERE id_merchant_produk='$id_merchant_produk' ";
    $sqltanpa = $db->query($query);

    if (!$sqltanpa){
    	$_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
		header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=2&filter1='.antiSQLi($_POST['merchant']));
		exit();
	}
	else{
		if (empty($_FILES['gambar_produk']['tmp_name'])){
			$_SESSION['notifikasi']['success'] = "Data Berhasil Di Update";
			header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=2&filter1='.antiSQLi($_POST['merchant']));
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
		$query_update = "UPDATE merchant_produk 
       					SET  gambar_produk='$nama_file'
       					WHERE id_merchant_produk='$id_merchant_produk'";
    	$sql = $db->query($query_update);

    	
	}else{
		$_SESSION['notifikasi']['fail'] = "Foto Gagal di Update";
		header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=2&filter1='.antiSQLi($_POST['merchant']));
	}

    if($sql){
    	$_SESSION['notifikasi']['success'] = "Data Berhasil Di Update";
		header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=2&filter1='.antiSQLi($_POST['merchant']));
	}else{
		$_SESSION['notifikasi']['fail'] = "Foto Gagal di Update";
		header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=2&filter1='.antiSQLi($_POST['merchant']));
	}
    
}





// ======================================================================
// =======================  HAPUS  ======================================
// ======================================================================

if(isset($_GET['hapus_produk'])){
    
    $id_produk = enkripsiDekripsi($_GET['eid'],'dekripsi');
    $id_merchant = enkripsiDekripsi($_GET['merchant'],'dekripsi');
    
    $query = "UPDATE merchant_produk SET status_remove_produk='Y', status_display_produk='N' WHERE id_merchant_produk='$id_produk' AND kd_merchant = '$id_merchant' ";
    // echo $query;
    $sql = $db->query($query);

    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Produk Berhasil Di Hapus";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=2&filter1='.antiSQLi($_GET['merchant']));
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=2&filter1='.antiSQLi($_GET['merchant']));
        exit();
    }
    
}




// ======================================================================
// ===================== TAMBAH MERCHENT EMPLOYEE =======================
// ======================================================================

if (isset($_POST['tambah_merchant_employee_gc'])){
    $id_merchant = enkripsiDekripsi($_POST['merchant'], 'dekripsi');
    $nama_merchant = antiSQLi($_POST['nama_employee']);
    $username_merchant = antiSQLi($_POST['username_employee']);
    $password_merchant = antiSQLi($_POST['password_employee']);
    $password_merchant_cnf = antiSQLi($_POST['password_employee_cnf']);
    $level_merchant = antiSQLi($_POST['level_employee']);
    $telp_merchant = antiSQLi($_POST['telp_employee']);
    $email_merchant = antiSQLi($_POST['email_employee']);
    $status_aktif_employee = antiSQLi($_POST['status_aktif_employee']);
    
    if($telp_merchant == ""){
        $telp_merchant = null;
    }else if($email_merchant == ""){
        $email_merchant = null;        
    }
    
    $sess_kd_merchant = $id_merchant;
    
    $cek_username=$db->query("SELECT * 
                                FROM merchant_employee 
                                WHERE username_employee = '$username_merchant'
				AND status_remove_employee='N' ")->num_rows;

    if($password_merchant != $password_merchant_cnf){
        
        $_SESSION['notifikasi']['fail'] = "Password anda tidak sama, mohon coba lagi";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=6&filter1='.antiSQLi($_POST['merchant']));
        exit();
    }
    elseif($cek_username > 0 ){
     $_SESSION['notifikasi']['fail'] = "Username ".$username_merchant." Sudah Digunakan";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=6&filter1='.antiSQLi($_POST['merchant']));
        exit();   
    }
    
    $password_enkripsi = enkripsiDekripsi($password_merchant ,'enkripsi');
    $id_merchant = createID('id_merchant_employee', 'merchant_employee', 'IP');

    $query = "INSERT INTO merchant_employee (id_merchant_employee,kd_merchant, nama_employee, username_employee, password_employee, level_employee,telp_employee,email_employee, status_aktif_employee) 
                VALUES ('$id_merchant', '$sess_kd_merchant', '$nama_merchant', '$username_merchant', '$password_enkripsi','$level_merchant', '$telp_merchant', '$email_merchant', '$status_aktif_employee')";
    $sql = $db->query($query);
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "merchant employee ".$username_merchant." berhasil ditambahkan";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=6&filter1='.antiSQLi($_POST['merchant']));
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=6&filter1='.antiSQLi($_POST['merchant']));
        exit();
    }    
    
}





// ======================================================================
// ======================= EDIT MERCHANT EMPLOYEE =======================
// ======================================================================

if(isset($_POST['edit_merchant_employee_gc'])){
    
    $id_employee = antiSQLi(enkripsiDekripsi($_POST['employee'],'dekripsi'));
    $id_merchant = antiSQLi(enkripsiDekripsi($_POST['merchant'],'dekripsi'));
    $nama_merchant = antiSQLi($_POST['nama_employee']);
    $username_merchant = antiSQLi($_POST['username_employee']);
    $password_merchant = antiSQLi($_POST['password_employee']);
    $password_merchant_cnf = antiSQLi($_POST['password_employee_cnf']);
    $level_merchant = antiSQLi($_POST['level_employee']);
    $telp_merchant = antiSQLi($_POST['telp_employee']);
    $email_merchant = antiSQLi($_POST['email_employee']);
    $status_aktif_employee = antiSQLi($_POST['status_aktif_employee']);
    
    
    if($telp_merchant == ""){
        $telp_merchant = null;
    }else if($email_merchant == ""){
        $email_merchant = null;        
    }
    
    $cek_username=$db->query("SELECT * 
                            FROM merchant_employee 
                            WHERE username_employee ='$username_merchant'
				AND status_remove_employee = 'N' 
                                AND id_merchant_employee!='$id_employee'")->num_rows;
    if($cek_username > 0 ){
        $_SESSION['notifikasi']['fail'] = "Username ".$username_merchant." Sudah Digunakan";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=6&filter1='.antiSQLi($_POST['merchant']));
        exit();   
    }

    elseif($password_merchant !== ""){
        if($password_merchant != $password_merchant_cnf){
            $_SESSION['notifikasi']['fail'] = "Password anda tidak sama, mohon coba lagi";
            header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=6&filter1='.antiSQLi($_POST['merchant']));
        exit();
        }else{
            $password_enkripsi = enkripsiDekripsi($password_merchant ,'enkripsi');
            $query_ubah_password = ", password_employee='".$password_enkripsi."' ";
        }
    }else{
        $query_ubah_password = "";
    }
    $sess_kd_merchant = $id_merchant;        
    $query = "UPDATE merchant_employee 
             SET kd_merchant='$sess_kd_merchant', nama_employee='$nama_merchant', username_employee='$username_merchant', level_employee='$level_merchant', telp_employee='$telp_merchant', email_employee='$email_merchant', status_aktif_employee='$status_aktif_employee' $query_ubah_password 
             WHERE id_merchant_employee='$id_employee'";
    $sql = $db->query($query);
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Admin ".$username_merchant." berhasil diubah";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=6&filter1='.antiSQLi($_POST['merchant']));
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=6&filter1='.antiSQLi($_POST['merchant']));
        exit();
    }
}





// ======================================================================
// ======================  HAPUS MERCHANT EMPLOYEE  =====================
// ======================================================================

if(isset($_GET['hapus_merchant_empoyee_gc'])){
    $id_employee = enkripsiDekripsi($_GET['eid'],'dekripsi');
    $id_merchant = enkripsiDekripsi($_GET['merchant'],'dekripsi');
    
    $query = "UPDATE merchant_employee 
                SET status_remove_employee='Y', status_aktif_employee ='N'
                WHERE id_merchant_employee='$id_employee' ";
                
    $sql = $db->query($query);


    if($sql){
        $_SESSION['notifikasi']['success'] = "Admin berhasil dihapus";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=6&filter1='.antiSQLi($_GET['merchant']));
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=6&filter1='.antiSQLi($_GET['merchant']));
        exit();
    }
}





// ======================================================================
// =============================  MUTASI KELUAR  ========================
// ======================================================================

if(isset($_POST['mutasi_keluar_gc'])){
    
    // echo "<pre>";
    // print_r($_POST);
    // exit();
    

    // [merchant] => eFNzYVdkYmpMLzBWTHYvWXgrRUdHUGJEaW8zd3NJclF4Z2JZVjlZL3pKWT0=
    // [keterangan_kepala] => afas;
    // [produk_dimutasi] => Array
    //     (
    //         [1] => Array
    //             (
    //                 [id] => WTZHeXJianVaTkVLQWR6bERXZ1VSN3FrZXUrTkMxOUJqRmFvMmFzNjVWcz0=
    //                 [jml] => 1
    //                 [ket] => rgre
    //             )

    //         [2] => Array
    //             (
    //                 [id] => RE1JMnZWdlN5b2M2WURKOXNjZFdZZG1rQXZrUUlicmNUSlQ2aStOV2lVQT0=
    //                 [jml] => 1
    //                 [ket] => hnre
    //             )

    //     )
    
    $id_merchant = enkripsiDekripsi($_POST['merchant'],'dekripsi');
    $id_employee = $_SESSION['id_merchant_employee'];
    $keterangan_kepala = antiSQLi($_POST['keterangan_kepala']);
    
    $db->begin_transaction();
    
    $id_kepala = createID('id_merchant_mutasi_stok', 'merchant_mutasi_stok', 'MS');
    $sql[] = $db->query("INSERT INTO `merchant_mutasi_stok`(`id_merchant_mutasi_stok`, `jenis_mutasi`, `kd_merchant`, `kd_merchant_employee`, `keterangan_mutasi`) 
        VALUES ('$id_kepala', '2', '$id_merchant', '$id_employee', '$keterangan_kepala')");
    $no_struk_kepala = id_ke_struk($id_kepala); 
        
    foreach ($_POST['produk_dimutasi'] as $key => $value) {
        
        // $id_produk = enkripsiDekripsi($value['id'], 'dekripsi');
        $id_produk = $value['id'];
        $data_lama = $db->query("SELECT * FROM view_merchant_produk WHERE id_merchant_produk='$id_produk' AND status_remove_produk='N' ")->fetch_assoc();
        
        if(!isset($data_lama)){
            $db->rollback();
    		$_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
            header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=7&filter1='.antiSQLi($_POST['merchant']));
            exit();
        }
        
        $query = " CALL tambah_mutasi('$id_kepala', '$id_produk', '$value[jml]', '$id_merchant', '$id_employee', '$data_lama[harga_beli]', '$value[ket]', '2', '$data_lama[harga_produk]', '$no_struk_kepala') ";

        $sql[] = $db->query($query);
        
    }
    
    
    if(in_array(false, $sql) OR in_array(0, $sql)){
		$db->rollback();
		$_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=7&filter1='.antiSQLi($_POST['merchant']));
        exit();
	}else{
		$db->commit();
		$_SESSION['notifikasi']['success'] = "Data Mutasi Berhasil Disimpan";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=7&filter1='.antiSQLi($_POST['merchant']));
        exit();
	}

    
}



// ======================================================================
// =========================== MUTASI MASUK =============================
// ======================================================================

if(isset($_POST['mutasi_masuk_gc'])){
    // echo "<pre>";
    // print_r($_POST);
    // exit();

    // [keterangan_kepala] => dasd
    // [temp_jml] => 1
    // [merchant] => eFNzYVdkYmpMLzBWTHYvWXgrRUdHUGJEaW8zd3NJclF4Z2JZVjlZL3pKWT0=
    // [produk_dimutasi] => Array
    //     (
    //         [1] => Array
    //             (
    //                 [id] => cnZqb1B3TmlJci94WEttMGJCNHN0Ly90UC83cFFYMVVNZzVaNjZnTlBhOD0=
    //                 [jml] => 1
    //                 [beli] => 12
    //                 [jual] => 12
    //                 [ket] => 
    //             )

    //     )

    $id_merchant = enkripsiDekripsi($_POST['merchant'],'dekripsi');
    $id_employee = $_SESSION['id_merchant_employee'];
    $keterangan_kepala = antiSQLi($_POST['keterangan_kepala']);



    foreach ($_POST['produk_dimutasi'] as $key => $value) {
        if ($value['jual'] < $value['beli']) {
            $_SESSION['notifikasi']['fail'] = "Gagal, pastikan harga jual lebih tinggi dari harga beli";
            header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=7&filter1='.antiSQLi($_POST['merchant']));
            exit();
        }
    }

    
    $db->begin_transaction();
    
    $id_kepala = createID('id_merchant_mutasi_stok', 'merchant_mutasi_stok', 'MS');
    $sql[] = $db->query("INSERT INTO `merchant_mutasi_stok`(`id_merchant_mutasi_stok`, `jenis_mutasi`, `kd_merchant`, `kd_merchant_employee`, `keterangan_mutasi`) 
        VALUES ('$id_kepala', '1', '$id_merchant', '$id_employee', '$keterangan_kepala')");
    $no_struk_kepala = id_ke_struk($id_kepala); 
        
    foreach ($_POST['produk_dimutasi'] as $key => $value) {
        
        // $id_produk = enkripsiDekripsi($value['id'], 'dekripsi');
        $id_produk = $value['id'];
        $data_lama = $db->query("SELECT * FROM view_merchant_produk WHERE id_merchant_produk='$id_produk' AND status_remove_produk='N' ")->fetch_assoc();
        
        if(!isset($data_lama)){
            $db->rollback();
            $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
            header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=7&filter1='.antiSQLi($_POST['merchant']));
            exit();
        }
        
        $query = " CALL tambah_mutasi('$id_kepala', '$id_produk', '$value[jml]', '$id_merchant', '$id_employee', '$value[beli]', '$value[ket]', '1', '$value[jual]', '$no_struk_kepala') ";

        $sql[] = $db->query($query);
        
        // tambah_mutasi('$id_mutasi', '$id_produk', '$value[jml]', '$id_merchant', '$id_employee', '$data_lama['harga_beli']', '$value[ket]', '2', '$data_lama['harga_produk']')
        
    }
    
    
    if(in_array(false, $sql) OR in_array(0, $sql)){
        $db->rollback();
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=7&filter1='.antiSQLi($_POST['merchant']));
        exit();
    }else{
        $db->commit();
        $_SESSION['notifikasi']['success'] = "Data Mutasi Berhasil Disimpan";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=7&filter1='.antiSQLi($_POST['merchant']));
        exit();
    }

}



// ======================================================================
// ======================== BATALKAN TRANSAKSI ==========================
// ======================================================================

if(isset($_POST['batalkan_transaksi'])){
    

    $id_merchant = enkripsiDekripsi($_POST['merchant'],'dekripsi');
    $id_transaksi = enkripsiDekripsi($_POST['id_transaksi'],'dekripsi');
    $alasan = $_POST['alasan'];

    $data_transaksi = $db->query("SELECT * FROM merchant_transaksi WHERE id_merchant_transaksi='$id_transaksi'")->fetch_assoc();

    if (!isset($data_transaksi)) {
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=5&filter1='.antiSQLi($_POST['merchant']));
        exit();
    }

    $db->begin_transaction();

    $sql[] = $db->query("UPDATE merchant_transaksi SET status_transaksi ='3' WHERE id_merchant_transaksi = '$id_transaksi'");
    
    $sql[] = $db->query("UPDATE merchant_transaksi_detail SET status_transaksi_detail ='3' WHERE kd_merchant_transaksi = '$id_transaksi'");

    $sql[] = $db->query("INSERT INTO merchant_transaksi_revisi SET id_merchant_transaksi_revisi =createID('merchant_transaksi_revisi'), keterangan_revisi='$alasan', kd_admin='$_SESSION[id_merchant_employee]', kd_transaksi='$id_transaksi', jenis_revisi='2', jumlah_item_awal='$data_transaksi[jumlah_item]', jumlah_item_akhir='0', jumlah_nominal_awal='$data_transaksi[tagihan_nota]', jumlah_nominal_akhir='0' ");

     if(in_array(false, $sql) OR in_array(0, $sql)){
        $db->rollback();
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=5&filter1='.antiSQLi($_POST['merchant']));
        exit();
    }else{
        $db->commit();
         $_SESSION['notifikasi']['success'] = "Transaksi Berhasil Dibatalkan";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=merchant&action=permerchant&menu=5&filter1='.antiSQLi($_POST['merchant']));
        exit();
    }
}


if (isset($_POST['meth'])) {
    $limit =10;
    $sess_kd_merchant = $_POST['kd_merchant'];
  if($_POST['meth'] == "get_record_product"){
    if(!empty($_POST['page']) && $_POST['page'] > 1 ){
        $offset = ($_POST['page'] - 1) * $limit;
        $page = $_POST['page'];
    }else{
        $offset 	= 0;
        $page = 1;
    }

    $cari = '';
    if(@$post['search'] != "" || !empty($_POST['search'])) { 
        $search = $_POST['search'];
        $cari = "AND (nama_produk LIKE '%$search%' OR kode_produk LIKE '%$search%') ";
    }

    $total_row 	= $db->query("
                SELECT COUNT(*) as jml 
                FROM view_merchant_produk mp
                WHERE mp.kd_merchant = '$sess_kd_merchant'
                    AND mp.status_remove_produk ='N'
                    AND mp.status_display_produk = 'Y'
                    AND mp.jenis_produk = '2'
                    $cari
    ")->fetch_assoc()['jml'];

    $record = $db->query("
                SELECT * 
                 FROM view_merchant_produk mp
                    WHERE mp.kd_merchant = '$sess_kd_merchant'
                    AND mp.status_remove_produk ='N'
                    AND mp.status_display_produk = 'Y'
                    AND mp.jenis_produk = '2'
                    $cari
                 ORDER BY id_merchant_produk DESC
                 LIMIT $offset, $limit
    ")->fetch_all(MYSQLI_ASSOC);

    $data =[
        // 'title' 		=> 'Manajemen Divisi',
        'total_rows' 	=> $total_row,
        'perpage' 		=> $limit,
        'record' 		=> $record,
        'page' 			=> $page,
        'jumlah_hal' 	=> ceil($total_row/$limit),
    ];

    echo json_encode($data);
    die();
  }
}