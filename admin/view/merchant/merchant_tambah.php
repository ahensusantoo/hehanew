<?php

?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Tambah Merchant</h1>
      </div>
    </div>
  </div>
</section>

<section class="content">
  <div class="container-fluid">
    <form role="form" method="post" action="view/merchant/proses_data.php" enctype="multipart/form-data">
      <div class="row">
        <div class="col-md-12">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Data Merchant</h3>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="nama_merchant">Nama</label>
                <input type="text" name="nama_merchant" class="form-control" id="nama_merchant" placeholder="Nama" maxlength="300" required>
              </div>
              <div class="form-group">
                <label for="kode_merchant">Kode</label>
                <input type="text" name="kode_merchant" class="form-control" id="kode_merchant" placeholder="Kode" maxlength="3" required>
              </div>
              <div class="form-group">
                <label for="telp_merchant">Telepon</label>
                <input type="text" name="telp_merchant" class="form-control" id="telp_merchant" placeholder="Telepon" maxlength="20" required>
              </div>
              <div class="form-group">
                <label for="telp_merchant">Email</label>
                <input type="email" name="email_merchant" class="form-control" id="email_merchant" placeholder="Email" maxlength="100" required>
              </div>
              <div class="form-group">
                <label for="panjang_merchant">Panjang</label>
                <input type="number" name="panjang_merchant" class="form-control" id="panjang_merchant" placeholder="Panjang" maxlength="10" required>
              </div>
              <div class="form-group">
                <label for="lebar_merchant">Lebar</label>
                <input type="number" name="lebar_merchant" class="form-control" id="lebar_merchant" placeholder="Lebar" maxlength="10" required>
              </div>
              <!--<div class="form-group">
                <label for="file_logo">Logo</label>
                <input type="file" name="file_logo" class="form-control" id="file_logo" required>
              </div>-->
              <div class="form-group">
                <label for="status_merchant">Jenis</label>
                <select name="status_merchant" class="form-control" id="status_merchant" required>
                  <option value="1">Souvenir</option>
                  <option value="2">Makanan</option>
                  <option value="3">Refleksi</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <!-- <div class="col-md-6">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Data Admin Merchant</h3>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="nama_employee">Nama Admin</label>
                <input type="text" name="nama_employee" class="form-control" id="nama_employee" placeholder="Nama" maxlength="300" required>
              </div>
              <div class="form-group">
                <label for="username_employee">Username Admin</label>
                <input type="text" name="username_employee" class="form-control" id="username_employee" placeholder="Nama" maxlength="100" required>
              </div>
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="password_employee">Password</label>
                    <input type="password" name="password_employee" class="form-control" id="password_employee" placeholder="Password" maxlength="500" autocomplete="off" onkeyup="cekPass()" required>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="password_employee_cnf">Konfirmasi Password</label> <span id='message'></span>
                    <input type="password" name="password_employee_cnf" class="form-control" id="password_employee_cnf" placeholder="Konfirmasi Password" maxlength="500" autocomplete="off" onkeyup="cekPass()" required>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="telp_employee">Telepon</label>
                <input type="text" name="telp_employee" class="form-control" id="telp_employee" placeholder="Telepon" maxlength="20" required>
              </div>
              <div class="form-group">
                <label for="email_employee">Email</label>
                <input type="email" name="email_employee" class="form-control" id="email_employee" placeholder="Email" maxlength="100" required>
              </div>
            </div>
          </div>
        </div> -->
        <div class="col-md-12">
          <div class="card card-primary">
            <div class="card-footer">
              <input type="submit" name="tambah_merchant" class="btn btn-primary" id="btnSubmit" value="Submit" style="width: 100%">
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</section>

<script type="text/javascript">
  function cekPass(){
    if ($('#password_employee').val() == $('#password_employee_cnf').val()) {
      $('#message').html('');
      document.getElementById("btnSubmit").disabled = false; 
    } else {
      $('#message').html('Tidak Cocok').css('color', 'red');
      document.getElementById("btnSubmit").disabled = true; 
    }
  };
</script>