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
        <h1>Tambah Tiket</h1>
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
          <form role="form" method="post" action="view/tiket/proses_data.php" enctype="multipart/form-data">
            <div class="card-body">
              <div class="form-group">
                <label for="nama_tiket">Nama</label>
                <input type="text" name="nama_tiket" class="form-control" id="nama_tiket" placeholder="Nama" maxlength="300" required>
              </div>
              <div class="form-group">
                <label for="start_hari">Hari Mulai</label>
                <select name="start_hari" class="form-control" id="start_hari" required>
                  <option value="1">Senin</option>
                  <option value="2">Selasa</option>
                  <option value="3">Rabu</option>
                  <option value="4">Kamis</option>
                  <option value="5">Jumat</option>
                  <option value="6">Sabtu</option>
                  <option value="7">Minggu</option>
                </select>
              </div>
              <div class="form-group">
                <label for="end_hari">Hari Selesai</label>
                <select name="end_hari" class="form-control" id="end_hari" required>
                  <option value="1">Senin</option>
                  <option value="2">Selasa</option>
                  <option value="3">Rabu</option>
                  <option value="4">Kamis</option>
                  <option value="5">Jumat</option>
                  <option value="6">Sabtu</option>
                  <option value="7">Minggu</option>
                </select>
              </div>
              <div class="form-group">
                <label for="start_jam">Jam Mulai</label>
                <input type="text" class="form-control datetime_picker" name="start_jam" id="start_jam" placeholder="12:00" required>
              </div>
              <div class="form-group">
                <label for="end_jam">Jam Selesai</label>
                <input type="text" name="end_jam" id="end_jam" class="form-control datetime_picker" placeholder="12:00" required>
              </div>
              <div class="form-group">
                <label for="harga_tiket">Harga Tiket</label>
                <input class="form-control" type="text" name="harga_tiket" id="harga_tiket" maxlength="20" onkeyup="this.value = this.value.replace(/[^0-9]/g, '').replace(/([0-9])(?=([0-9][0-9][0-9])+(?![0-9]))/g, '$1.')" onchange="this.value = this.value.replace(/[^0-9]/g, '').replace(/([0-9])(?=([0-9][0-9][0-9])+(?![0-9]))/g, '$1.')">
              </div>    
              <div class="form-group">
                <label for="status_hari_linur">Status Hari Libur</label>
                <select name="status_hari_linur" class="form-control" id="status_hari_linur" required>
                  <option value="1">Hari Biasa</option>
                  <option value="2">Weekend & Sebelum Libur</option>
                  <option value="3">Hari Libur</option>
                </select>
              </div>                    

            </div>
            <div class="card-footer">
              <input type="submit" name="tambah_jenis_tiket" class="btn btn-primary" id="btnSubmit" value="Submit">
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
    $(".datetime_picker").flatpickr({
      enableTime: true,
      noCalendar: true,
      dateFormat: "H:i",
      defaultDate: "12:00",
      time_24hr: true
    });
  </script>