<?php 
require_once('../../templates/koneksi.php');

    $id_merchant_employee   = $_POST['id_merchant_employee'];
    $nama_employee          = str_replace("&#039;","'",$_POST['nama_employee']);
    $username_employee      = str_replace("&#039;","'",$_POST['username_employee']);
    $telp_employee          = $_POST['telp_employee'];
    $email_employee         = $_POST['email_employee'];
    $level                  = $_POST['level'];
    $status                 = $_POST['status'];
	
	if(!empty($_POST['password_employee'])){
	    
	    $password               = enkripsiDekripsi($_POST['password_employee'] ,'enkripsi');
	    
    	$query = mysqli_query($db, "UPDATE `merchant_employee` SET `level_employee` = '$level', `nama_employee`= '$nama_employee', `username_employee`='$username_employee',`password_employee`='$password',
    	`telp_employee`='$telp_employee',`email_employee`='$email_employee',`tgl_input_employee`='',`status_aktif_employee`='$status' WHERE id_merchant_employee = '$id_merchant_employee'");
    	
    	$cek_kode_anggota =$db->query(" SELECT * FROM merchant_employee 
                                    WHERE username_employee ='$username_employee'
                                    AND kd_merchant = '$kd_merchant' AND status_remove_employee = 'N'")->num_rows;
                                    
        if ($cek_kode_anggota > 0){
    	    http_response_code(400);
            $respon['pesan'] = "Username Anggota ".$username_employee." Sudah ada harap diganti!\nKlik `Mengerti` untuk menutup pesan ini";
            die(json_encode($respon));
	    } else {
	        if ($query){
        		$respon['pesan'] = "Anggota ".$nama_employee." berhasil diupdate";
        		die(json_encode($respon));
    	    } else{ 
        	    http_response_code(400);
                $respon['pesan'] = "Gagal diupdate Anggota baru!\nKlik `Mengerti` untuk menutup pesan ini";
        		die(json_encode($respon)); 
    	    }   
	    }
	
	}else{
	    $query = mysqli_query($db, "UPDATE `merchant_employee` SET `level_employee` = '$level', `nama_employee`= '$nama_employee', `username_employee`='$username_employee',
    	`telp_employee`='$telp_employee',`email_employee`='$email_employee',`tgl_input_employee`='',`status_aktif_employee`='$status' WHERE id_merchant_employee = '$id_merchant_employee'");
    	
    	$cek_kode_anggota =$db->query(" SELECT * FROM merchant_employee 
                                    WHERE username_employee ='$username_employee'
                                    AND kd_merchant = '$kd_merchant' AND status_remove_employee = 'N'")->num_rows;
        
        if ($cek_kode_anggota > 0){
    	    http_response_code(400);
            $respon['pesan'] = "Username Anggota ".$username_employee." Sudah ada harap diganti!\nKlik `Mengerti` untuk menutup pesan ini";
            die(json_encode($respon));
	    } else {
	        if ($query){
        		$respon['pesan'] = "Anggota ".$nama_employee." berhasil diupdate";
        		die(json_encode($respon));
    	    } else{ 
        	    http_response_code(400);
                $respon['pesan'] = "Gagal diupdate Anggota baru!\nKlik `Mengerti` untuk menutup pesan ini";
        		die(json_encode($respon)); 
    	    }      
	    }
	}

mysqli_close($db);
?>