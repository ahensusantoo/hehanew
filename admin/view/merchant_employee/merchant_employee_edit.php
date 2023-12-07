<?php
    $sess_kd_merchant = $_SESSION['kd_merchant'];
    $id_merchant_employee = enkripsiDekripsi($_GET['id'],'dekripsi');
    $sql = "SELECT * 
            FROM merchant_employee 
            WHERE id_merchant_employee='$id_merchant_employee'
                AND kd_merchant = '$sess_kd_merchant' ";
    $data_merchant = $db->query($sql)->fetch_assoc();
?>


<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-12">
        <h1>Edit Merchant Employee</h1>
        
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
                <input type="hidden" name="id_merchant" value="<?= $_GET['id'] ?>">
                <label for="nama_merchant">Nama Merchant Employee</label>
                <input type="text" name="nama_merchant" value="<?= $data_merchant['nama_employee'] ?>" class="form-control" id="nama_merchant" placeholder="Nama Merchant Employee" maxlength="300" required>
              </div>
              <div class="form-group">
                <label for="username_merchant">Username Merchant Employee</label> <span id='messageUname'></span>
                <input type="text" name="username_merchant" value="<?= $data_merchant['username_employee'] ?>" class="form-control" id="username_merchant" placeholder="Username Merchant Employee" maxlength="100" autocomplete="off" onkeyup="cekUsername()" required>
              </div>
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="password_merchant">Password Merchant Employee</label>
                    <input type="password" name="password_merchant" class="form-control" id="password_merchant" placeholder="Password Merchant Employee" maxlength="500" autocomplete="off" onkeyup="cekPass()">
                    <small class="text-red">*Biarkan kosong jika tidak ingin mengubah password</small>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="password_admin_cnf">Konfirmasi Password</label> <span id='message'></span>
                    <input type="password" name="password_merchant_cnf" class="form-control" id="password_merchant_cnf" placeholder="Konfirmasi Password" maxlength="500" autocomplete="off" onkeyup="cekPass()">
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="level_employee">Level Merchant Employee</label></span>
                <select name="level_merchant" class="form-control" id="level_merchant" required>
                  <option disabled selected>--PILIH--</option>
                    <?php if($_SESSION['level_employee'] == "0" ) { ?>
                        <option value="1" <?php if($data_merchant['level_employee']== "1") { echo "selected"; } ?> >Admin</option>}
                        <option value="2" <?php if($data_merchant['level_employee']== "2") { echo "selected"; } ?> >Kasir</option>    
                    <?php } ?>
                    <?php if($_SESSION['level_employee'] == "1" ) { ?>
                        <option value="2" <?php if($data_merchant['level_employee']== "2") { echo "selected"; } ?> >Kasir</option>
                    <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label for="telp_merchant">Telpon Merchant Employee</label>
                <input type="number" name="telp_merchant" value="<?= $data_merchant['telp_employee'] ?>" class="form-control" id="telp_merchant" placeholder="Telpon Merchant Employee" maxlength="20">
              </div>
              <div class="form-group">
                <label for="email_merchant">Email Merchant Employee</label>
                <input type="email" name="email_merchant" value="<?= $data_merchant['email_employee'] ?>" class="form-control" id="email_merchant" placeholder="Email Merchant Employee" maxlength="200">
              </div>
              <div class="form-group">
                <label for="status_aktif_employee">Status Aktif Employee</label></span>
                <select name="status_aktif_employee" class="form-control" id="level_merchant" required>
                  <option disabled selected>--PILIH--</option>
                        <option value="Y" <?php if($data_merchant['status_aktif_employee'] == "Y") { echo "selected"; } ?> >AKTIF</option>}
                        <option value="N" <?php if($data_merchant['status_aktif_employee']== "N") { echo "selected"; } ?> >NON AKTIF</option>}
                </select>
              </div>
              
            </div>
            <div class="card-footer">
              <input type="submit" name="edit_merchant" class="btn btn-primary" id="btnSubmit" value="Submit">
            </div>
          </form>
        </div>
      </div>
      <div class="col-md-6">
      </div>
    </div>
  </div>
</section>
