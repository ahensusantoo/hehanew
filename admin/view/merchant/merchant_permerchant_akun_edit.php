<?php
$eid = enkripsiDekripsi($_GET['eid'],'dekripsi');

$data_asli = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM `merchant_employee`  WHERE id_merchant_employee = '$eid'"));
?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Edit Akun</h1>
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
                <label for="nama_employee">Nama</label>
                <input type="hidden" name="merchant" value="<?= $_GET['merchant'] ?>">
                <input type="hidden" name="employee" value="<?= $_GET['eid'] ?>">
                <input type="text" name="nama_employee" value="<?= $data_asli['nama_employee']?>" class="form-control" id="nama_employee" placeholder="Nama" maxlength="300" required>
              </div>
              <div class="form-group">
                <label for="username_employee">Username</label>
                <input type="text" name="username_employee" value="<?= $data_asli['username_employee']?>" class="form-control" id="username_employee" placeholder="Nama" maxlength="100" required>
              </div>
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="password_employee">Password</label>
                    <input type="password" name="password_employee" value="<?= enkripsiDekripsi($data_asli['password_employee'], 'dekripsi')?>" class="form-control" id="password_employee" placeholder="Password" maxlength="500" autocomplete="off" onkeyup="cekPass()">
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="password_employee_cnf">Konfirmasi Password</label> <span id='message'></span>
                    <input type="password" name="password_employee_cnf" value="<?= enkripsiDekripsi($data_asli['password_employee'], 'dekripsi')?>" class="form-control" id="password_employee_cnf" placeholder="Konfirmasi Password" maxlength="500" autocomplete="off" onkeyup="cekPass()">
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="telp_employee">Telepon</label>
                <input type="text" name="telp_employee" value="<?= $data_asli['telp_employee']?>" class="form-control" id="telp_employee" placeholder="Telepon" maxlength="20" required>
              </div>
              <div class="form-group">
                <label for="email_employee">Email</label>
                <input type="email" name="email_employee" value="<?= $data_asli['email_employee']?>" class="form-control" id="email_employee" placeholder="Email" maxlength="100" required>
              </div>
              <div class="form-group">
                <label for="level_employee">Level Akun</label>
                <select name="level_employee" class="form-control" id="level_employee" required>
                  <option value="1" <?php if($data_asli['level_employee'] == '1'){echo'selected';}; ?>>Admin Merchant</option>
                  <option value="2" <?php if($data_asli['level_employee'] == '2'){echo'selected';}; ?>>Kasir Merchant</option>
                </select>
              </div>
              <div class="form-group">
                <label for="status_aktif_employee">Status Aktif Employee</label></span>
                <select name="status_aktif_employee" class="form-control" id="status_aktif_employee" required>
                    <option value="Y"  <?php if($data_asli['status_aktif_employee'] == '2'){echo'selected';}; ?>>AKTIF</option>
                    <option value="N"  <?php if($data_asli['status_aktif_employee'] == '2'){echo'selected';}; ?>>NON AKTIF</option>
                </select>
              </div>
            </div>
            <div class="card-footer">
              <input type="submit" name="edit_merchant_employee_gc" class="btn btn-primary" id="btnSubmit" value="Submit" style="width: 100%">
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