<?php
    $id_admin = enkripsiDekripsi($_GET['id'],'dekripsi');
    $sql = "SELECT * FROM admin WHERE id_admin='$id_admin'";
    $data_admin = $db->query($sql)->fetch_assoc();
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
    <div class="row">
      <div class="col-md-12">
        <div class="card card-primary">
          <!-- <div class="card-header">
            <h3 class="card-title">Quick Example <small>jQuery Validation</small></h3>
          </div> -->
          <form role="form" method="post" action="view/admin/proses_data.php" enctype="multipart/form-data">
            <div class="card-body">              
              <input type="hidden" name="id_admin" value="<?= $_GET['id'] ?>">
              <div class="form-group">
                <label for="nama_anggota">Nama</label>
                <input type="text" name="nama_anggota" class="form-control" id="nama_anggota" placeholder="Nama" maxlength="255" value="<?php echo $data_admin['nama_admin'] ?>" required>
              </div>
              <div class="form-group">
                <label for="username_anggota">Username</label> <span id='messageUname'></span>
                <input type="text" name="username_anggota" class="form-control" id="username_anggota" placeholder="Username" maxlength="100" autocomplete="off" onkeyup="cekUsername()" value="<?php echo $data_admin['username_admin'] ?>" required>
              </div>
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="password_anggota">Password</label>
                    <input type="password" name="password_anggota" class="form-control" id="password_anggota" placeholder="Password" maxlength="100" autocomplete="off" onkeyup="cekPass()" >
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="password_anggota_cnf">Konfirmasi Password</label> <span id='message'></span>
                    <input type="password" name="password_anggota_cnf" class="form-control" id="password_anggota_cnf" placeholder="Konfirmasi Password" maxlength="100" autocomplete="off" onkeyup="cekPass()">
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="jabatan_admin">Jabatan</label>
                <select name="jabatan_admin" class="form-control" id="jabatan_admin" required>
                  <option value="1" <?php if($data_admin['jabatan_admin'] == "1"){ echo "checked"; }?>>Superadmin</option>
                  <option value="6" <?php if($data_admin['jabatan_admin'] == "6"){ echo "selected"; }?>>General Cashier</option>
                </select>
              </div>
            </div>
            <div class="card-footer">
              <input type="submit" name="edit_admin" class="btn btn-primary" id="btnSubmit" value="Submit">
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
      alert
    if ($('#password_anggota').val() == $('#password_anggota_cnf').val()) {
      $('#message').html('');
      // document.getElementById("btnSubmit").disabled = false; 
      cekGabungan(); 
    } else {
      $('#message').html('Tidak Cocok').css('color', 'red');
      document.getElementById("btnSubmit").disabled = true; 
    }
  };
  function cekGabungan(){
    if($('#password_anggota').val() == $('#password_anggota_cnf').val()){
      document.getElementById("btnSubmit").disabled = false;
    }
  }

  function setInputFilter(textbox, inputFilter) {
    ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function(event) {
      textbox.addEventListener(event, function() {
        if (inputFilter(this.value)) {
          this.oldValue = this.value;
          this.oldSelectionStart = this.selectionStart;
          this.oldSelectionEnd = this.selectionEnd;
        } else if (this.hasOwnProperty("oldValue")) {
          this.value = this.oldValue;
          this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
        }
      });
    });
  }

  setInputFilter(document.getElementById("telepon_anggota"), function(value) {
    return /^\d*$/.test(value);
  });

  setInputFilter(document.getElementById("username_anggota"), function(value) {
    return /^[a-zA-Z0-9]*$/.test(value);
  });

  setInputFilter(document.getElementById("password_anggota"), function(value) {
    return /^[a-zA-Z0-9]*$/.test(value);
  });
  setInputFilter(document.getElementById("password_anggota_cnf"), function(value) {
    return /^[a-zA-Z0-9]*$/.test(value);
  });
</script>