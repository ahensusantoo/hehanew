<?php  
  function setValue($value){
    $val = @$_SESSION['setvalue'][$value];
    unset($_SESSION['setvalue'][$value]);
    echo $val;
  }
?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Tambah Akun Merchant Employee</h1>
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
          <form role="form" method="post" action="view/merchant_employee/proses_data.php" enctype="multipart/form-data">
            <div class="card-body">              
              <div class="form-group">
                <label for="nama_merchant">Nama Merchant Employee</label>
                <input type="text" name="nama_merchant" value="<?php setValue("nama_merchant") ?>" class="form-control" id="nama_merchant" placeholder="Nama Merchant Employee" maxlength="300" required>
              </div>
              <div class="form-group">
                <label for="username_merchant">Username Merchant Employee</label> <span id='messageUname'></span>
                <input type="text" name="username_merchant" value="<?php setValue("username_merchant") ?>" class="form-control" id="username_merchant" placeholder="Username Merchant Employee" maxlength="100" autocomplete="off" onkeyup="cekUsername()" required>
              </div>
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="password_merchant">Password Merchant Employee</label>
                    <input type="password" name="password_merchant" class="form-control" id="password_merchant" placeholder="Password Merchant Employee" maxlength="500" autocomplete="off" onkeyup="cekPass()" required>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="password_admin_cnf">Konfirmasi Password</label> <span id='message'></span>
                    <input type="password" name="password_merchant_cnf" class="form-control" id="password_merchant_cnf" placeholder="Konfirmasi Password" maxlength="500" autocomplete="off" onkeyup="cekPass()" required>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="level_employee">Level Merchant Employee</label></span>
                <!-- <pre>
                  <?php echo print_r($_SESSION) ?>  
                </pre> -->
                <select name="level_merchant" class="form-control" id="level_merchant" required>
                    <?php if($_SESSION['level_employee'] == "0" ) { ?>
                        <option value="1" <?php if(@$_SESSION['setvalue']["level_merchant"]== "1") { echo "selected"; } ?> >Admin</option>}
                        <option value="2" <?php if(@$_SESSION['setvalue']["level_merchant"]== "2") { echo "selected"; } ?> >Kasir</option>    
                    <?php } ?>
                    <?php if($_SESSION['level_employee'] == "1" ) { ?>
                        <option value="2" <?php if(@$_SESSION['setvalue']["level_merchant"]== "2") { echo "selected"; } ?> >Kasir</option>
                    <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label for="telp_merchant">Telpon Merchant Employee</label>
                <input type="number" name="telp_merchant" value="<?php setValue("telp_merchant") ?>" class="form-control" id="telp_merchant" placeholder="Telpon Merchant Employee" maxlength="20">
              </div>
              <div class="form-group">
                <label for="email_merchant">Email Merchant Employee</label>
                <input type="email" name="email_merchant" value="<?php setValue("email_merchant") ?>" class="form-control" id="email_merchant" placeholder="Email Merchant Employee" maxlength="200">
              </div>
              <div class="form-group">
                <label for="status_aktif_employee">Status Aktif Employee</label></span>
                <select name="status_aktif_employee" class="form-control" id="status_aktif_employee" required>
                    <option value="Y" <?php if(@$_SESSION['setvalue']["status_aktif_employee"]== "Y") { echo "selected"; } ?> >AKTIF</option>
                    <option value="N" <?php if(@$_SESSION['setvalue']["status_aktif_employee"]== "N") { echo "selected"; } ?> >NON AKTIF</option>
                </select>
              </div>
            </div>
            <div class="card-footer">
              <input type="submit" name="tambah_merchant" class="btn btn-primary" id="btnSubmit" value="Submit">
            </div>
          </form>
        </div>
      </div>
      <div class="col-md-6">
      </div>
    </div>
  </div>
</section>

