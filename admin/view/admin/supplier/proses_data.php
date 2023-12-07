<?php

require_once '../../templates/koneksi.php';
//  echo "<pre>"; echo print_r($_POST);die();

// ======================================================================
// ======================= TAMBAH ADMINISTRATOR =========================
// ======================================================================

if (isset($_POST['tambah_admin'])){
    
    $nama_admin = antiSQLi($_POST['nama_admin']);
    $username_admin = antiSQLi($_POST['username_admin']);
    $password_admin = antiSQLi($_POST['password_admin']);
    $password_admin_cnf = antiSQLi($_POST['password_admin_cnf']);
    $jabatan_admin = antiSQLi($_POST['jabatan_admin']);
    
    if($password_admin != $password_admin_cnf){
        $_SESSION['notifikasi']['fail'] = "Password anda tidak sama, mohon coba lagi";
        goto gagal_tambah_admin;
    }

    $cek_username = $db->query("SELECT COUNT(*) AS jml FROM admin WHERE username_admin='$username_admin' AND status_rmv_admin='N'")->fetch_assoc()['jml'];
    if ($cek_username > 0) {
        $_SESSION['notifikasi']['fail'] = "Akun gagal dibuat, username telah digunakan. Coba username lain";
        goto gagal_tambah_admin;
    }
    
    $password_enkripsi = enkripsiDekripsi($password_admin,'enkripsi');
    $id_admin = createID('id_admin', 'admin', 'AD');
    $query = "INSERT INTO admin (id_admin, nama_admin, username_admin, password_admin, jabatan_admin) VALUES ('$id_admin', '$nama_admin', '$username_admin', '$password_enkripsi', '$jabatan_admin') ";
    $sql = $db->query($query);
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Admin ".$username_admin." berhasil ditambahkan";
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
    }

    gagal_tambah_admin:
    if ($jabatan_admin == '1' OR $jabatan_admin == '6') { //super-admin
        header('Location: '.base_url().$_SESSION['base_php'].'?page=admin&action=kelola');
        exit();
    }elseif($jabatan_admin == '2'){ //admin tiket
        header('Location: '.base_url().$_SESSION['base_php'].'?page=admintiket&action=kelola');
        exit();
    }elseif($jabatan_admin == '3' OR $jabatan_admin == '4' OR $jabatan_admin == '5'){//admin photobooth
        header('Location: '.base_url().$_SESSION['base_php'].'?page=adminphotobooth&action=kelola');
        exit();
    }
    
    
}



// ======================================================================
// ======================== EDIT ADMINISTRATOR ==========================
// ======================================================================

if(isset($_POST['edit_admin'])){
    
    $id_admin = enkripsiDekripsi($_POST['id_admin'],'dekripsi');
    $nama_admin = antiSQLi($_POST['nama_anggota']);
    $username_admin = antiSQLi($_POST['username_anggota']);
    $password_admin = antiSQLi($_POST['password_anggota']);
    $password_admin_cnf = antiSQLi($_POST['password_anggota_cnf']);
    $jabatan_admin = antiSQLi($_POST['jabatan_admin']);
    $password_enkripsi = enkripsiDekripsi($password_admin,'enkripsi');
    
    if($password_admin !== ""){
        if($password_admin != $password_admin_cnf){
            $_SESSION['notifikasi']['fail'] = "Password anda tidak sama, mohon coba lagi";
            goto gagal_edit_admin;
        }else{
            $query_ubah_password = ", password_admin='".$password_enkripsi."' ";
        }
    }else{
        $query_ubah_password = "";
    }
  
  
    $cek_username = $db->query("SELECT COUNT(*) AS jml FROM admin WHERE username_admin='$username_admin' AND status_rmv_admin='N' AND id_admin!='$id_admin' ")->fetch_assoc()['jml'];
    if ($cek_username > 0) {
        $_SESSION['notifikasi']['fail'] = "Akun gagal dibuat, username telah digunakan. Coba username lain";
        goto gagal_tambah_admin;
    }
    
    
    $query = "UPDATE admin SET nama_admin='$nama_admin', username_admin='$username_admin', jabatan_admin='$jabatan_admin' $query_ubah_password WHERE id_admin='$id_admin'";
    $sql = $db->query($query);
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Admin ".$username_admin." berhasil diubah";
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
    }

    gagal_edit_admin:
    if ($jabatan_admin == '1' OR $jabatan_admin == '6') { //super-admin
        header('Location: '.base_url().$_SESSION['base_php'].'?page=admin&action=kelola');
        exit();
    }elseif($jabatan_admin == '2'){ //admin tiket
        header('Location: '.base_url().$_SESSION['base_php'].'?page=admintiket&action=kelola');
        exit();
    }elseif($jabatan_admin == '3' OR $jabatan_admin == '4' OR $jabatan_admin == '5'){//admin photobooth
        header('Location: '.base_url().$_SESSION['base_php'].'?page=adminphotobooth&action=kelola');
        exit();
    }
    
}


// ======================================================================
// ======================= HAPUS ADMINISTRATOR ==========================
// ======================================================================

if(isset($_GET['hapus_admin'])){
    $id_admin = enkripsiDekripsi($_GET['id'],'dekripsi');

    $query = "SELECT * FROM admin WHERE id_admin='$id_admin'";
    $admin = @$db->query($query)->fetch_assoc();

    if ($admin != "") {
        $jabatan_admin = $admin['jabatan_admin'];
    }else{
        $jabatan_admin = '1';
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        goto gagal_hapus_admin;
    }

    $query = "UPDATE admin SET status_rmv_admin='Y' WHERE id_admin='$id_admin'";
    $sql = $db->query($query);
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "Admin ".$_GET['hapus_admin']." berhasil dihapus";
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
    }

    gagal_hapus_admin:

    if ($jabatan_admin == '1' OR $jabatan_admin == '6') { //super-admin
        header('Location: '.base_url().$_SESSION['base_php'].'?page=admin&action=kelola');
        exit();
    }elseif($jabatan_admin == '2'){ //admin tiket
        header('Location: '.base_url().$_SESSION['base_php'].'?page=admintiket&action=kelola');
        exit();
    }elseif($jabatan_admin == '3' OR $jabatan_admin == '4' OR $jabatan_admin == '5'){//admin photobooth
        header('Location: '.base_url().$_SESSION['base_php'].'?page=adminphotobooth&action=kelola');
        exit();
    }
    
}




// ======================================================================
// ===================== TAMBAH MERCHENT EMPLOYEE =======================
// ======================================================================

if (isset($_POST['tambah_general_cashier_stall'])){
    $id_merchant = "";
    $nama_merchant = antiSQLi($_POST['nama_employee']);
    $username_merchant = antiSQLi($_POST['username_employee']);
    $password_merchant = antiSQLi($_POST['password_employee']);
    $password_merchant_cnf = antiSQLi($_POST['password_employee_cnf']);
    $level_merchant = "0";
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
					AND status_remove_employee ='N' ")->num_rows;

    if($password_merchant != $password_merchant_cnf){
        
        $_SESSION['notifikasi']['fail'] = "Password anda tidak sama, mohon coba lagi";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=generalcashier_stall&action=kelola');
        exit();
    }
    elseif($cek_username > 0 ){
     $_SESSION['notifikasi']['fail'] = "Username ".$username_merchant." Sudah Digunakan";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=generalcashier_stall&action=kelola');
        exit();   
    }
    
    $password_enkripsi = enkripsiDekripsi($password_merchant,'enkripsi');
    $id_merchant = createID('id_merchant_employee', 'merchant_employee', 'IP');

    $query = "INSERT INTO merchant_employee (id_merchant_employee,kd_merchant, nama_employee, username_employee, password_employee, level_employee,telp_employee,email_employee, status_aktif_employee) 
                VALUES ('$id_merchant', '$sess_kd_merchant', '$nama_merchant', '$username_merchant', '$password_enkripsi','$level_merchant', '$telp_merchant', '$email_merchant', '$status_aktif_employee')";
    $sql = $db->query($query);
    
    if($sql){
        $_SESSION['notifikasi']['success'] = "merchant employee ".$username_merchant." berhasil ditambahkan";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=generalcashier_stall&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=generalcashier_stall&action=kelola');
        exit();
    }    
    
}







// ======================================================================
// ======================= EDIT MERCHANT EMPLOYEE =======================
// ======================================================================

if(isset($_POST['edit_merchant_employee_gc'])){
    
    $id_employee = antiSQLi(enkripsiDekripsi($_POST['employee'],'dekripsi'));
    $id_merchant = "";
    $nama_merchant = antiSQLi($_POST['nama_employee']);
    $username_merchant = antiSQLi($_POST['username_employee']);
    $password_merchant = antiSQLi($_POST['password_employee']);
    $password_merchant_cnf = antiSQLi($_POST['password_employee_cnf']);
    $level_merchant = "0";
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
				AND status_remove_employee ='N' 
                                AND id_merchant_employee!='$id_employee'")->num_rows;
    if($cek_username > 0 ){
        $_SESSION['notifikasi']['fail'] = "Username ".$username_merchant." Sudah Digunakan";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=generalcashier_stall&action=kelola');
        exit();   
    }

    elseif($password_merchant !== ""){
        if($password_merchant != $password_merchant_cnf){
            $_SESSION['notifikasi']['fail'] = "Password anda tidak sama, mohon coba lagi";
            header('Location: '.base_url().$_SESSION['base_php'].'?page=generalcashier_stall&action=kelola');
        exit();
        }else{
            $password_enkripsi = enkripsiDekripsi($password_merchant,'enkripsi');
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
        header('Location: '.base_url().$_SESSION['base_php'].'?page=generalcashier_stall&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=generalcashier_stall&action=kelola');
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
        $_SESSION['notifikasi']['success'] = "General Cashier berhasil dihapus";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=generalcashier_stall&action=kelola');
        exit();
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().$_SESSION['base_php'].'?page=generalcashier_stall&action=kelola');
        exit();
    }
}
    
    
// ======================================================================
// ======================= TAMBAH SUPPLIER ==============================
// ======================================================================
    
    if (isset($_POST['tambah_supplier'])){
        // echo "<pre>"; echo print_r($_POST);die();
        $kode_supplier          = antiSQLi($_POST['kode_supplier']);
        $nama_supplier          = antiSQLi($_POST['nama_supplier']);
        $telp_supplier          = antiSQLi($_POST['telp_supplier']);
        $alamat                 = antiSQLi($_POST['alamat']);
        $status_aktif_supplier  = antiSQLi($_POST['status_aktif_supplier']);
        
        $cek_kode = $db->query("SELECT COUNT(*) AS jml FROM supplier WHERE kode_supplier='$kode_supplier' AND status_rmv_supplier='N'")->fetch_assoc()['jml'];
        if ($cek_kode > 0) {
            $_SESSION['notifikasi']['fail'] = "Supplier gagal dibuat, Kode Supplier telah digunakan. Coba Kode lain";
            // goto gagal_tambah_admin;
            header('Location: '.base_url().$_SESSION['base_php'].'?page=supplier&action=tambah');
            exit();
        }
        
        $query = "INSERT INTO supplier SET id_supplier=createID('supplier'), kode_supplier= '$kode_supplier', nama_supplier='$nama_supplier', telp_supplier='$telp_supplier', alamat_supplier='$alamat', status_aktif_supplier='$status_aktif_supplier', tanggal_input_supplier=NOW() ";
        $sql = $db->query($query);
        
        if($sql){
            $_SESSION['notifikasi']['success'] = "Admin ".$nama_supplier." berhasil ditambahkan";
            header('Location: '.base_url().$_SESSION['base_php'].'?page=supplier&action=kelola');
            exit();
        }else{
            $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
            header('Location: '.base_url().$_SESSION['base_php'].'?page=supplier&action=tambah');
            exit();    
        }
    
        // gagal_tambah_admin:
        // if ($jabatan_admin == '1' OR $jabatan_admin == '6') { //super-admin
        //     header('Location: '.base_url().$_SESSION['base_php'].'?page=admin&action=kelola');
        //     exit();
        // }elseif($jabatan_admin == '2'){ //admin tiket
        //     header('Location: '.base_url().$_SESSION['base_php'].'?page=admintiket&action=kelola');
        //     exit();
        // }elseif($jabatan_admin == '3' OR $jabatan_admin == '4' OR $jabatan_admin == '5'){//admin photobooth
        //     header('Location: '.base_url().$_SESSION['base_php'].'?page=adminphotobooth&action=kelola');
        //     exit();
        // }
        
        
    }
    
        
// ======================================================================
// ========================= EDIT SUPPLIER ==============================
// ======================================================================
    
    if (isset($_POST['edit_supplier'])){
        // echo "<pre>"; echo print_r($_POST);die();
        $kode_supplier          = antiSQLi($_POST['kode_supplier']);
        $nama_supplier          = antiSQLi($_POST['nama_supplier']);
        $telp_supplier          = antiSQLi($_POST['telp_supplier']);
        $alamat                 = antiSQLi($_POST['alamat']);
        $status_aktif_supplier  = antiSQLi($_POST['status_aktif_supplier']);
        
        $id = antiSQLi(enkripsiDekripsi($_POST['id'],'dekripsi'));
        
        $cek_kode = $db->query("SELECT COUNT(*) AS jml FROM supplier WHERE kode_supplier='$kode_supplier' AND status_rmv_supplier='N' AND id_supplier !='$id' ")->fetch_assoc()['jml'];
        if ($cek_kode > 0) {
            $_SESSION['notifikasi']['fail'] = "Supplier gagal dibuat, Kode Supplier telah digunakan. Coba Kode lain";
            // goto gagal_tambah_admin;
            header('Location: '.base_url().$_SESSION['base_php'].'?page=supplier&action=edit&eid='.$_POST['id']);
            exit();
        }
        
        $query = "UPDATE supplier SET kode_supplier= '$kode_supplier', nama_supplier='$nama_supplier', telp_supplier='$telp_supplier', alamat_supplier='$alamat', status_aktif_supplier='$status_aktif_supplier' WHERE id_supplier = '$id' ";
        $sql = $db->query($query);
        
        if($sql){
            $_SESSION['notifikasi']['success'] = "Admin ".$nama_supplier." berhasil diedit";
            header('Location: '.base_url().$_SESSION['base_php'].'?page=supplier&action=kelola');
            exit();
        }else{
            $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
            header('Location: '.base_url().$_SESSION['base_php'].'?page=supplier&action=tambah');
            exit();    
        }
    
        // gagal_tambah_admin:
        // if ($jabatan_admin == '1' OR $jabatan_admin == '6') { //super-admin
        //     header('Location: '.base_url().$_SESSION['base_php'].'?page=admin&action=kelola');
        //     exit();
        // }elseif($jabatan_admin == '2'){ //admin tiket
        //     header('Location: '.base_url().$_SESSION['base_php'].'?page=admintiket&action=kelola');
        //     exit();
        // }elseif($jabatan_admin == '3' OR $jabatan_admin == '4' OR $jabatan_admin == '5'){//admin photobooth
        //     header('Location: '.base_url().$_SESSION['base_php'].'?page=adminphotobooth&action=kelola');
        //     exit();
        // }
        
        
    }
    
    
// ======================================================================
// ======================  HAPUS MERCHANT EMPLOYEE  =====================
// ======================================================================

    if(isset($_GET['hapus_supplier'])){
        $id = antiSQLi(enkripsiDekripsi($_GET['eid'],'dekripsi'));
        
        $query = "UPDATE supplier 
                    SET status_aktif_supplier ='N', status_rmv_supplier ='Y'
                    WHERE id_supplier='$id' ";
        // echo "<pre>"; echo print_r($query);die();        
        $sql = $db->query($query);
    
    
        if($sql){
            $_SESSION['notifikasi']['success'] = "Supplier berhasil dihapus";
            header('Location: '.base_url().$_SESSION['base_php'].'?page=supplier&action=kelola');
            exit();
        }else{
            $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
            header('Location: '.base_url().$_SESSION['base_php'].'?page=supplier&action=kelola');
            exit();
        }
    }

