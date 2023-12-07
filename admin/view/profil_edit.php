<?php

    if(isset($_POST['ubah_profile'])){
        $nama_admin = antiSQLi($_POST['nama_admin']);
        $username_admin = antiSQLi($_POST['username_admin']);
        $password_admin = antiSQLi($_POST['password_admin']);
        $password_admin_cnf = antiSQLi($_POST['password_admin_cnf']);
        $password_enkripsi = enkripsiDekripsi($password_admin,'enkripsi');
        $url_sekarang = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        
        $cek_username = $db->query("SELECT COUNT(*) AS jml FROM admin WHERE username_admin='$username_admin' AND id_admin!='$_SESSION[id_admin]' ")->fetch_assoc()['jml'];
        if($cek_username > 0){
            $_SESSION['notifikasi']['fail'] = "Username tidak tersedia, coba dengan username lain";
            echo "<script>window.location = window.location.href;</script>";
            exit();
        }
        
        
        if($password_admin !== ""){
            if($password_admin != $password_admin_cnf){
                $_SESSION['notifikasi']['fail'] = "Password anda tidak sama, mohon coba lagi";
                echo "<script>window.location = window.location.href;</script>";
                exit();
            }else{
                $query_ubah_password = ", password_admin='".$password_enkripsi."' ";
            }
        }else{
            $query_ubah_password = "";
        }
        
        $query = "UPDATE admin SET nama_admin='$nama_admin', username_admin='$username_admin' $query_ubah_password WHERE id_admin='$_SESSION[id_admin]'";
        $sql = $db->query($query);
        
        if($sql){
            $_SESSION['notifikasi']['success'] = "Admin ".$username_admin." berhasil diubah";
            echo "<script>window.location = window.location.href;</script>";
            exit();
        }else{
            $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
            echo "<script>window.location = window.location.href;</script>";
            exit();
        }
        
    }

    $data_profile = $db->query("SELECT * FROM admin WHERE id_admin='$_SESSION[id_admin]'")->fetch_assoc();
?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Edit Profil</h1>
      </div>
    </div>
  </div>
</section>

<section class="content">
  <div class="container-fluid">
      <?php if (isset($_SESSION['notifikasi'])) { notifikasi($_SESSION['notifikasi']); } ?>
    <div class="row">
      <div class="col-md-12">
        <div class="card card-primary">
          <!-- <div class="card-header">
            <h3 class="card-title">Quick Example <small>jQuery Validation</small></h3>
          </div> -->
          <form role="form" method="post" action="" enctype="multipart/form-data">
            <div class="card-body">              
              <div class="form-group">
                <label for="nama_admin">Nama</label>
                <input type="text" name="nama_admin" class="form-control" id="nama_admin" placeholder="Nama" maxlength="255" value="<?php echo $data_profile['nama_admin'] ?>" required>
              </div>
              <div class="form-group">
                <label for="username_admin">Username</label> <span id='messageUname'></span>
                <input type="text" name="username_admin" class="form-control" id="username_admin" placeholder="Username" maxlength="100" autocomplete="off" onkeyup="cekUsername()" value="<?php echo $data_profile['username_admin'] ?>" required>
              </div>
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="password_admin">Password</label>
                    <input type="password" name="password_admin" class="form-control" id="password_admin" placeholder="Password" maxlength="100" autocomplete="off" onkeyup="cekPass()" value="">
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="password_admin_cnf">Konfirmasi Password</label> <span id='message'></span>
                    <input type="password" name="password_admin_cnf" class="form-control" id="password_admin_cnf" placeholder="Konfirmasi Password" maxlength="100" autocomplete="off" onkeyup="cekPass()" value="">
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer">
              <input type="submit" name="ubah_profile" class="btn btn-primary" id="btnSubmit" value="Submit">
            </div>
          </form>
        </div>
      </div>
      <div class="col-md-6">
      </div>
    </div>
  </div>
</section>

<script type="text/javascript">
  function cekPass(){
    if ($('#password_admin').val() == $('#password_admin_cnf').val()) {
      $('#message').html('');
      // document.getElementById("btnSubmit").disabled = false; 
      cekGabungan(); 
    } else {
      $('#message').html('Tidak Cocok').css('color', 'red');
    //   document.getElementById("btnSubmit").disabled = true; 
    }
  };
  function cekGabungan(){
    if(array_kode_uname.indexOf($('#username_anggota').val()) < 0 && $('#password_admin').val() == $('#password_admin_cnf').val()){
    //   document.getElementById("btnSubmit").disabled = false;
    }
  }
</script>