<?php 
require_once('../../templates/koneksi.php');

    $kd_merchant            = $_POST['id_merchant'];
    $nama_employee          = str_replace("&#039;","'",$_POST['nama_employee']);
    $username_employee      = str_replace("&#039;","'",$_POST['username_employee']);
    $telp_employee          = $_POST['telp_employee'];
    $email_employee         = $_POST['email_employee'];
    $level                  = $_POST['level'];
    $status                 = $_POST['status'];
    $password               = enkripsiDekripsi($_POST['password_employee'] ,'enkripsi');
    
    $cek_kode_anggota =$db->query(" SELECT * FROM merchant_employee 
                                    WHERE username_employee ='$username_employee'
                                    AND kd_merchant = '$kd_merchant' AND status_remove_employee = 'N'")->num_rows;

	$id_merchant_employee = createID('id_merchant_employee', 'merchant_employee', 'IP');
	
	$query = mysqli_query($db, "INSERT INTO `merchant_employee`(`id_merchant_employee`, `kd_merchant`, `nama_employee`, `username_employee`, `password_employee`, `level_employee`, `telp_employee`, `email_employee`, `status_aktif_employee`)
	VALUES ('$id_merchant_employee', '$kd_merchant', '$nama_employee', '$username_employee', '$password', '$level', '$telp_employee', '$email_employee', '$status')");
	
	if ($cek_kode_anggota > 0){
	    http_response_code(400);
        $respon['pesan'] = "Username Anggota ".$username_employee." Sudah ada harap diganti!\nKlik `Mengerti` untuk menutup pesan ini";
        die(json_encode($respon));
	} else {
        if ($query){
    		$respon['pesan'] = "Anggota ".$nama_employee." berhasil ditambahkan";
    		die(json_encode($respon));
    	} else{ 
    	    http_response_code(400);
            $respon['pesan'] = "Gagal menambahkan Anggota baru!\nKlik `Mengerti` untuk menutup pesan ini";
    		die(json_encode($respon)); 
    	}
	}

mysqli_close($db);
?>