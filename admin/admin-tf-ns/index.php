<?php 
    include("../templates/koneksi.php");

    session_destroy();

    setcookie('id_admin', '', time() + -100, '/');
    setcookie('nama_admin', '', time() + -100, '/');
    setcookie('username_admin', '', time() + -100, '/');
    setcookie('stts_login_adm_ns', '', time() + -100, '/');
    setcookie('base_php', '', time() + -100, '/');

    // $list_shift = $db->query("SELECT * FROM `shift` WHERE `status_aktif_shift` = 'Y'")->fetch_all(MYSQLI_ASSOC);

    if(isset($_POST['login'])){
        $username = $_POST['username'];
        $pass = $_POST['pass'];
      
      
        // CEK GENERAL CASHIER STALL
        $data_admin = $db->query(" SELECT * FROM admin WHERE username_admin='$username' AND status_rmv_admin='N' ")->fetch_assoc();
        
        if(empty($data_admin)){
            $_SESSION['notifikasi']['fail'] = "Username atau Password anda salah!!!";  
            goto gagal_login;
        }

        if(enkripsiDekripsi($data_admin['password_admin'], 'dekripsi') != $pass){
            $_SESSION['notifikasi']['fail'] = "Username atau Password anda salah!!!";  
            goto gagal_login;
        }

        if($data_admin['stts_login_adm_ns'] == '0'){
            $_SESSION['notifikasi']['fail'] = "Anda tidak memiliki hak akses!!!";  
            goto gagal_login;
        }

        $_SESSION['id_admin'] = $data_admin['id_admin'];
        $_SESSION['username'] = $data_admin['username_admin'];
        $_SESSION['role_admin'] = $data_admin['jabatan_admin'];
        $_SESSION['stts_login_adm_ns'] = $data_admin['stts_login_adm_ns'];
        $_SESSION['base_php'] = 'admin-tf-ns/admin-super.php';

        setcookie('id_admin', enkripsiDekripsi($data_admin['id_admin'], 'enkripsi'), time() + (60 * 60 * 16), '/');
        setcookie('base_php', 'admin-tf-ns/admin-super.php', time() + (60 * 60 * 17), '/');
      
        header('Location: admin-super.php');
        exit();
    }

    // $_SESSION['notifikasi']['fail'] = "Username atau password anda salah";
    gagal_login:

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Admin Tiket | Log in</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <a ><b>Admin</b> Tiket</a>
    </div>
    <!-- /.login-logo -->
    <?php if (isset($_SESSION['notifikasi'])) { notifikasi($_SESSION['notifikasi']); } ?>
    
    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg">Masukkan Username dan Password</p>

        <form role="form" method="post" action="" enctype="multipart/form-data">
          <div class="input-group mb-3">
            <input type="text" class="form-control" name="username" placeholder="Username" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" name="pass" placeholder="Password" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-8">
              <div class="icheck-primary">
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" name="login" class="btn btn-primary btn-block">Masuk</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>

</body>
</html>
