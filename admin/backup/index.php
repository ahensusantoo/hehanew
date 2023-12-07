<?php 
include("koneksi.php");
if(isset($_SESSION['session_admin_byox'])){
  $id_temp = enkripsiDekripsi($_SESSION['session_admin_byox'], "dekripsi");
  $cek_username = mysqli_query($koneksi,"SELECT asi.id_role FROM anggota_sistem AS asi WHERE asi.id_anggota = '$id_temp'");
  $data_username = mysqli_fetch_array($cek_username);
  if ($data_username['id_role'] == 'AGN') {
    header("location:adminSuper.php");
  }
} 

if(!empty(enkripsiDekripsi(@$_GET['art'], "dekripsi"))) {
  echo "<script>alert('Gagal login\\n\\n".enkripsiDekripsi($_GET['art'], "dekripsi")."');</script>";
  // header("location:index.php");
}

if(isset($_POST['login'])){
  $username = $_POST['username'];
  $pass = $_POST['pass'];

  $cek_username = mysqli_query($koneksi,"SELECT asi.id_anggota, asi.id_role, asi.nama_anggota, asi.password_anggota, asi.status_aktif, asi.status_hapus FROM anggota_sistem AS asi WHERE asi.username_anggota = '$username'");
  $data_username = mysqli_fetch_array($cek_username);
  
  if(!empty($data_username['id_anggota'])){
    if($data_username['status_hapus'] == 'N'){
      if($data_username['status_aktif'] == 'Y'){
        if(enkripsiDekripsi($pass, "enkripsi") == $data_username['password_anggota']){
          $ec_admin = enkripsiDekripsi($data_username['id_anggota'], "enkripsi");
          session_start();
          $_SESSION['session_admin_byox'] = $ec_admin;

          if ($data_username['id_role'] == 'AGN') {
            header("location:adminSuper.php");
          }

        } else {
          header("location:index.php?art=".enkripsiDekripsi("Password anda salah.", "enkripsi"));
        }
      } else {
        header("location:index.php?art=".enkripsiDekripsi("Username ".$username." dinonaktifkan Super Admin.", "enkripsi"));
      }
    } else {
      header("location:index.php?art=".enkripsiDekripsi("Username ".$username." dihapus Super Admin.", "enkripsi"));
    }
  } else {
    header("location:index.php?art=".enkripsiDekripsi("Username ".$username." tidak ditemukan.", "enkripsi"));
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Admin Byox | Log in</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <a ><b>Admin</b> Byox</a>
    </div>
    <!-- /.login-logo -->
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
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

</body>
</html>
