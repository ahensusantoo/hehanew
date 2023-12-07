<?php
// include("proses/ceklogin.php");
if(isset($id_admin_temp_fix)){
  $id_admin = enkripsiDekripsi($_GET['eid'], 'dekripsi');
  $data_admin = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM admin WHERE id_admin = '$id_admin'"));

  $data_uname = array();
  $ambil_uname = mysqli_query($db,"SELECT username_admin FROM admin WHERE id_admin != '$id_admin_temp'");
  while ($row_uname= mysqli_fetch_assoc($ambil_uname)){
    $data_uname[] = $row_uname['username_admin'];
  }

  $btnSubmit = @$_POST['btnSubmit'];
  if ($btnSubmit) {
    $nama_anggota = @$_POST['nama_anggota'];
    $email_anggota = @$_POST['email_anggota'];
    $username_anggota = @$_POST['username_anggota'];
    $password_anggota = enkripsiDekripsi(@$_POST['password_anggota'], "enkripsi");

    $insert = mysqli_query($db,"UPDATE `admin` SET 
      `nama_anggota` = '$nama_anggota',
      `email_anggota` = '$email_anggota',
      `username_anggota` = '$username_anggota',
      `password_anggota` = '$password_anggota' 
      WHERE id_admin = '$id_admin_temp'");

    if ($insert) {
      echo '<script type="text/javascript">
      alert("Data berhasil diedit"); 
      window.location.href="?page=editProfil";
      </script>';
    } else {
      $iki_error = str_replace("'", "`", mysqli_error($db));
      echo '<script type="text/javascript">
      alert("Gagal mengedit data\\n'.$iki_error.'"); 
      </script>';
    }
  }
} else {
  // header("location:index.php");
}
?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Tambah Shift</h1>
      </div>
    </div>
  </div>
</section>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-primary">
          <!-- <div class="card-header">
            <h3 class="card-title">Quick Example <small>jQuery Validation</small></h3>
          </div> -->
          <form role="form" method="post" action="view/shift/proses_data.php" enctype="multipart/form-data">
            <div class="card-body">
              <div class="form-group">
                <label for="nama_shift">Nama Shift</label>
                <input type="text" name="nama_shift" class="form-control" id="nama_shift" placeholder="Nama" maxlength="300" required>
              </div>
            </div>
            <div class="card-footer">
              <input type="submit" name="tambah_shift" class="btn btn-primary" id="btnSubmit" value="Submit">
            </div>
          </form>
        </div>
      </div>
      <div class="col-md-6">
      </div>
    </div>
  </div>
</section>
