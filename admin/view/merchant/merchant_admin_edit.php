<?php
    $id_merchant_employee = enkripsiDekripsi(antiSQLi($_GET['eid']), 'dekripsi');
    $data = $db->query("SELECT * FROM merchant_employee WHERE id_merchant_employee='$id_merchant_employee'")->fetch_assoc();
?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Edit Admin Merchant</h1>
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
            <div class="card-body">
              <div class="form-group">
                <label for="nama_employee">Nama Admin</label>
                <input type="hidden" name="id" value="<?= $_GET['eid'] ?>">
                <input type="text" name="nama_employee" class="form-control" id="nama_employee" placeholder="Nama" value="<?= $data['nama_employee'] ?>" maxlength="300" required>
              </div>
              <div class="form-group">
                <label for="username_employee">Username Admin</label>
                <input type="text" name="username_employee" class="form-control" id="username_employee" placeholder="Nama" value="<?= $data['username_employee'] ?>" maxlength="100" required>
              </div>
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="password_employee">Password</label>
                    <input type="password" name="password_employee" class="form-control" id="password_employee" placeholder="Password" maxlength="500" autocomplete="off" onkeyup="cekPass()" >
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="password_employee_cnf">Konfirmasi Password</label> <span id='message'></span>
                    <input type="password" name="password_employee_cnf" class="form-control" id="password_employee_cnf" placeholder="Konfirmasi Password" maxlength="500" autocomplete="off" onkeyup="cekPass()" >
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="telp_employee">Telepon</label>
                <input type="text" name="telp_employee" class="form-control" id="telp_employee" placeholder="Telepon" value="<?= $data['telp_employee'] ?>" maxlength="20" required>
              </div>
              <div class="form-group">
                <label for="email_employee">Email</label>
                <input type="email" name="email_employee" class="form-control" id="email_employee" placeholder="Email" value="<?= $data['email_employee'] ?>" maxlength="100" required>
              </div>
              <div class="form-group">
                <label for="level_employee">Level Akun</label>
                <select name="level_employee" class="form-control" id="level_employee" required>
                  <option value="0" <?php if($data['level_employee'] == '0'){echo 'selected';} ?>>Superadmin</option>
                  <option value="1" <?php if($data['level_employee'] == '1'){echo 'selected';} ?>>Admin Merchant</option>
                  <option value="2" <?php if($data['level_employee'] == '2'){echo 'selected';} ?>>Kasir Merchant</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-12">
          <div class="card card-primary">
            <div class="card-footer">
              <input type="submit" name="edit_merchant_employee" class="btn btn-primary" id="btnSubmit" value="Submit" style="width: 100%">
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