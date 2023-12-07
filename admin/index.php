<?php 
include("templates/koneksi.php");

session_destroy();

setcookie('id_admin', '', time() + -100, '/');
setcookie('username', '', time() + -100, '/');
setcookie('role_admin', '', time() + -100, '/');
setcookie('shift', '', time() + -100, '/');
setcookie('printer', '', time() + -100, '/');
setcookie('base_php', '', time() + -100, '/');
setcookie('id_merchant_employee', '', time() + -100, '/');

$list_shift = $db->query("SELECT * FROM `shift` WHERE `status_aktif_shift` = 'Y'")->fetch_all(MYSQLI_ASSOC);

if(isset($_POST['login'])){
  $username = $_POST['username'];
  $pass = $_POST['pass'];
  
  
  // CEK GENERAL CASHIER STALL
  $data_gc_stall = $db->query("SELECT * FROM merchant_employee WHERE username_employee='$username' AND status_aktif_employee='Y' AND level_employee='0' AND status_remove_employee='N'")->fetch_assoc();
  if(isset($data_gc_stall)){
      if(enkripsiDekripsi($data_gc_stall['password_employee'], 'dekripsi') == $pass){
          $_SESSION['id_merchant_employee'] = $data_gc_stall['id_merchant_employee'];
          $_SESSION['username'] = $data_gc_stall['username_employee'];
          $_SESSION['base_php'] = 'general-cashier-stall.php';
          $_SESSION['shift'] = enkripsiDekripsi($_POST['shift'],'dekripsi');
          $_SESSION['printer'] = antiSQLi($_POST['printer']);

          setcookie('id_merchant_employee', enkripsiDekripsi($data_gc_stall['id_merchant_employee'], 'enkripsi'), time() + (60 * 60 * 16), '/');
          setcookie('username', $data_gc_stall['username_employee'], time() + (60 * 60 * 17), '/');
          setcookie('shift', antiSQLi($_POST['shift']), time() + (60 * 60 * 17), '/');
          setcookie('printer', antiSQLi($_POST['printer']), time() + (60 * 60 * 17), '/');
          setcookie('base_php', 'general-cashier-stall.php', time() + (60 * 60 * 17), '/');
          
          header('Location: general-cashier-stall.php?page=dashboard');
          exit();
      }
  }


   // CEK MERCHANT EMPLOYEE
  $query = "SELECT * FROM merchant_employee WHERE username_employee='$username' AND status_aktif_employee='Y' AND status_remove_employee='N'";
  $data_merchant_employee = $db->query($query)->fetch_assoc();
  if (isset($data_merchant_employee)) {
    if(enkripsiDekripsi($data_merchant_employee['password_employee'], 'dekripsi') == $pass){
      if($data_merchant_employee['level_employee'] == "1"){
        $_SESSION['id_merchant_employee'] = $data_merchant_employee['id_merchant_employee'];
        $_SESSION['kd_merchant'] = $data_merchant_employee['kd_merchant'];
        $_SESSION['username'] = $data_merchant_employee['username'];
        $_SESSION['nama_employee'] = $data_merchant_employee['nama_employee'];
        $_SESSION['level_employee'] = $data_merchant_employee['level_employee'];
        $_SESSION['printer'] = $data_merchant_employee['printer'];


         setcookie('id_merchant_employee', enkripsiDekripsi($data_gc_stall['id_merchant_employee'], 'enkripsi'), time() + (60 * 60 * 16), '/');
          setcookie('username', $data_gc_stall['username_employee'], time() + (60 * 60 * 17), '/');
          setcookie('shift', antiSQLi($_POST['shift']), time() + (60 * 60 * 17), '/');
          setcookie('printer', antiSQLi($_POST['printer']), time() + (60 * 60 * 17), '/');
          setcookie('base_php', 'general-cashier-stall.php', time() + (60 * 60 * 17), '/');



        $token = random_string(8);
        $sess_kd_merchant = $_SESSION['kd_merchant'];
        $sess_id_employee = $_SESSION['id_merchant_employee'];
        $queryToken = "UPDATE merchant_employee 
        SET token_login='$token'
            WHERE kd_merchant = '$sess_kd_merchant'
                AND id_merchant_employee = '$sess_id_employee' ";

        $sql = $db->query($queryToken);
        $_SESSION['token_login'] = $token;
        header('Location: merchant.php?page=dashboard');
        exit();
      }else{
        $_SESSION['notifikasi']['fail'] = "Anda Tidak Punya Hak Akses";  
        goto gagal_login;
      }
      
    }
  }



  // CEK ADMIN
  $query = "SELECT * FROM admin WHERE username_admin='$username' AND status_rmv_admin='N'";
  $data_admin = $db->query($query)->fetch_assoc();

  if(isset($data_admin)){
    if(enkripsiDekripsi(@$data_admin['password_admin'], 'dekripsi') == $pass){

      $id_shift = enkripsiDekripsi($_POST['shift'],'dekripsi');
      $nama_shift = @$db->query("SELECT nama_shift FROM shift WHERE id_shift='$id_shift' ")->fetch_assoc()['nama_shift'];

      $_SESSION['id_admin'] = $data_admin['id_admin'];
      $_SESSION['username'] = $data_admin['username_admin'];
      $_SESSION['role_admin'] = $data_admin['jabatan_admin'];
      $_SESSION['shift'] = $id_shift;
      $_SESSION['shift_nama'] = $nama_shift;
      $_SESSION['printer'] = antiSQLi($_POST['printer']);
      $_SESSION['base_php'] = 'admin-super.php';

      setcookie('id_admin', enkripsiDekripsi($data_admin['id_admin'], 'enkripsi'), time() + (60 * 60 * 16), '/');
      setcookie('shift', antiSQLi($_POST['shift']), time() + (60 * 60 * 17), '/');
      setcookie('printer', antiSQLi($_POST['printer']), time() + (60 * 60 * 17), '/');
      setcookie('base_php', 'admin-super.php', time() + (60 * 60 * 17), '/');
      
  
      if ($data_admin['jabatan_admin'] == "1") {
        header('Location: admin-super.php');
        exit();
      } elseif($data_admin['jabatan_admin'] == "2"){
        header('Location: ticketing.php');
        exit();
      } elseif($data_admin['jabatan_admin'] == "3"){
        header('Location: photobooth.php');
        exit();
      } elseif($data_admin['jabatan_admin'] == "5"){
        header('Location: photobooth-ambil.php');
        exit();
      } elseif($data_admin['jabatan_admin'] == "6"){
        $_SESSION['base_php'] = 'general-cashier-ticketing.php';
        header('Location: general-cashier-ticketing.php');
        exit();
      }
    }
  }


  $_SESSION['notifikasi']['fail'] = "Username atau password anda salah";
  gagal_login:
      
}



?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Admin Heha | Log in</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <a ><b>Admin</b> Heha</a>
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
          <div class="input-group mb-3">
            <select class="form-control" name="shift">
              <?php foreach ($list_shift as $key => $value): ?>
                <option value="<?= enkripsiDekripsi($value['id_shift'], 'enkripsi') ?>"><?= $value['nama_shift'] ?></option>
              <?php endforeach ?>
            </select>
          </div>
          <label class="ml-1">Printer</label>
          <div class="input-group mb-3">
            <select class="form-control" name="printer">
                <option value="192.168.72.4"  <?= (get_client_ip() == "192.168.72.4" )?'selected':'' ?> >192.168.72.4</option>
                <option value="192.168.72.6"  <?= (get_client_ip() == "192.168.72.6" )?'selected':'' ?> >192.168.72.6</option>
                <option value="192.168.72.7"  <?= (get_client_ip() == "192.168.72.7" )?'selected':'' ?> >192.168.72.7</option>
                <option value="192.168.72.8"  <?= (get_client_ip() == "192.168.72.8" )?'selected':'' ?> >192.168.72.8</option>
                <option value="192.168.72.9"  <?= (get_client_ip() == "192.168.72.9" )?'selected':'' ?> >192.168.72.9</option>
                <option value="192.168.72.10" <?= (get_client_ip() == "192.168.72.10" )?'selected':'' ?> >192.168.72.10</option>
                <option value="192.168.72.11" <?= (get_client_ip() == "192.168.72.11" )?'selected':'' ?> >192.168.72.11</option>
                <option value="192.168.72.12" <?= (get_client_ip() == "192.168.72.12" )?'selected':'' ?> >192.168.72.12</option>
                <option value="192.168.72.13" <?= (get_client_ip() == "192.168.72.13" )?'selected':'' ?> >192.168.72.13</option>
                <option value="192.168.72.14" <?= (get_client_ip() == "192.168.72.14" )?'selected':'' ?> >192.168.72.14</option>
                <option value="192.168.72.15" <?= (get_client_ip() == "192.168.72.15" )?'selected':'' ?> >192.168.72.15</option>
                <option value="192.168.72.16" <?= (get_client_ip() == "192.168.72.16" )?'selected':'' ?> >192.168.72.16</option>
                <option value="192.168.72.17" <?= (get_client_ip() == "192.168.72.17" )?'selected':'' ?> >192.168.72.17</option>
                <option value="192.168.72.18" <?= (get_client_ip() == "192.168.72.18" )?'selected':'' ?> >192.168.72.18</option>
                <option value="192.168.72.19" <?= (get_client_ip() == "192.168.72.19" )?'selected':'' ?> >192.168.72.19</option>
                <option value="192.168.72.20" <?= (get_client_ip() == "192.168.72.20" )?'selected':'' ?> >192.168.72.20</option>
                <option value="192.168.6.47" <?= (get_client_ip() == "192.168.6.47" )?'selected':'' ?> >192.168.6.47</option>
            </select>
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
