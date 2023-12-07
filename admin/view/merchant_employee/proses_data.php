<?php

// echo "<pre>";
// echo print_r($_POST);
// die();

require_once '../../templates/koneksi.php';
sessionLoginMerchantEmployee();


// ======================================================================
// ======================= TAMBAH =======================================
// ======================================================================

if (isset($_POST['tambah_merchant'])){
    $nama_merchant = addslashes($_POST['nama_merchant']);
    $username_merchant = addslashes($_POST['username_merchant']);
	$password_merchant = $_POST['password_merchant'];
    $password_merchant_cnf = addslashes($_POST['password_merchant_cnf']);
    $level_merchant = addslashes($_POST['level_merchant']);
    $telp_merchant = addslashes($_POST['telp_merchant']);
    $email_merchant = addslashes($_POST['email_merchant']);
    $status_aktif_employee = addslashes($_POST['status_aktif_employee']);
    
    if($telp_merchant == ""){
        $telp_merchant = null;
    }else if($email_merchant == ""){
        $email_merchant = null;        
    }
    
    $sess_kd_merchant = $_SESSION['kd_merchant'];
    
    $cek_username=$db->query("SELECT * 
                                FROM merchant_employee 
                                WHERE username_employee = '$username_merchant'
				AND status_remove_employee='N' ")->num_rows;

    if($password_merchant != $password_merchant_cnf){

        $_SESSION['setvalue']['kd_merchant'] = $kd_merchant;
        $_SESSION['setvalue']['nama_merchant'] = $nama_merchant;
        $_SESSION['setvalue']['username_merchant'] = $username_merchant;
        $_SESSION['setvalue']['level_merchant'] = $level_merchant;
        $_SESSION['setvalue']['telp_merchant'] = $telp_merchant;
        $_SESSION['setvalue']['email_merchant'] = $email_merchant;
        $_SESSION['setvalue']['status_aktif_employee'] = $status_aktif_employee;
        
        $_SESSION['notifikasi']['fail'] = "Password anda tidak sama, mohon coba lagi";
        header('Location: '.base_url().'merchant.php?page=merchant_employee&action=tambah');
        exit();
    }
    elseif($cek_username > 0 ){
     $_SESSION['notifikasi']['fail'] = "Username ".$username_merchant." Sudah Digunakan";
        header('Location: '.base_url().'merchant.php?page=merchant_employee&action=tambah');
        exit();   
    }
    
	$id_merchant = createID('id_merchant_employee', 'merchant_employee', 'IP');
    $password_enkripsi = enkripsiDekripsi($password_merchant,'enkripsi');
    $sess_kd_merchant = $_SESSION['kd_merchant'];
    $query = "INSERT INTO merchant_employee (id_merchant_employee, kd_merchant, nama_employee, username_employee, password_employee, level_employee, telp_employee, email_employee, status_aktif_employee) 
                                    VALUES ('$id_merchant', '$sess_kd_merchant', '$nama_merchant', '$username_merchant', '$password_enkripsi','$level_merchant', '$telp_merchant', '$email_merchant', '$status_aktif_employee')";
    $sql = $db->query($query);
    // var_dump($sql);die();
    // echo $query;

    if($sql){
        $_SESSION['notifikasi']['success'] = "merchant employee ".$username_merchant." berhasil ditambahkan";
        header('Location: '.base_url().'merchant.php?page=merchant_employee&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'merchant.php?page=merchant_employee&action=tambah');
        exit();
    }    
}

// ======================================================================
// ======================== EDIT ========================================
// ======================================================================

if(isset($_POST['edit_merchant'])){
    
    // print_r($_POST);die();
    
    $id_merchant = enkripsiDekripsi($_POST['id_merchant'],'dekripsi');
    $nama_merchant = addslashes($_POST['nama_merchant']);
    $username_merchant = addslashes($_POST['username_merchant']);
    $password_merchant = $_POST['password_merchant'];
    $password_merchant_cnf = addslashes($_POST['password_merchant_cnf']);
    $level_merchant = addslashes($_POST['level_merchant']);
    $telp_merchant = addslashes($_POST['telp_merchant']);
    $email_merchant = addslashes($_POST['email_merchant']);
    $status_aktif_employee = addslashes($_POST['status_aktif_employee']);
    
     
    
    if($telp_merchant == ""){
        $telp_merchant = null;
    }else if($email_merchant == ""){
        $email_merchant = null;        
    }
    
    $cek_username=$db->query("SELECT * 
                            FROM merchant_employee 
                            WHERE username_employee ='$username_merchant' 
				AND status_remove_employee = 'N'
                                AND id_merchant_employee !='$id_merchant' ")->num_rows;
    if($cek_username > 0 ){
        $_SESSION['notifikasi']['fail'] = "Username ".$username_merchant." Sudah Digunakan";
        header('Location: '.base_url().'merchant.php?page=merchant_employee&action=edit&id='.$_POST['id_merchant']);
        exit();   
    }

    elseif($password_merchant !== ""){
        if($password_merchant != $password_merchant_cnf){
            $_SESSION['notifikasi']['fail'] = "Password anda tidak sama, mohon coba lagi";
            header('Location: '.base_url().'merchant.php?page=merchant_employee&action=edit&id='.$_POST['id_merchant']);
        exit();
        }else{
            $password_enkripsi = enkripsiDekripsi($password_merchant ,'enkripsi');
            $query_ubah_password = ", password_employee='".$password_enkripsi."' ";
        }
    }else{
        $query_ubah_password = "";
    }
    $sess_kd_merchant = $_SESSION['kd_merchant'];        
    $query = "UPDATE merchant_employee 
             SET kd_merchant='$sess_kd_merchant', nama_employee='$nama_merchant', username_employee='$username_merchant', level_employee='$level_merchant', telp_employee='$telp_merchant', email_employee='$email_merchant', status_aktif_employee='$status_aktif_employee' $query_ubah_password 
             WHERE id_merchant_employee='$id_merchant'";
    $sql = $db->query($query);
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Admin ".$username_merchant." berhasil diubah";
        header('Location: '.base_url().'merchant.php?page=merchant_employee&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'merchant.php?page=merchant_employee&action=edit&id='.$_POST['id_merchant']);
        exit();
    }
}


// ======================================================================
// =======================  HAPUS  ======================================
// ======================================================================

if(isset($_GET['merchant_employee_hapus'])){
    $id_merchant = enkripsiDekripsi($_GET['id'],'dekripsi');
    
    $query = "UPDATE merchant_employee 
                SET status_remove_employee='Y', status_aktif_employee ='N'
                WHERE id_merchant_employee='$id_merchant'
                    AND kd_merchant = '$_SESSION[kd_merchant]' ";
    // echo $query;
    $sql = $db->query($query);

    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Admin ".$_GET['merchant_employee_hapus']." berhasil dihapus";
        header('Location: '.base_url().'merchant.php?page=merchant_employee&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'merchant.php?page=merchant_employee&action=kelola');
        exit();
    }
}
