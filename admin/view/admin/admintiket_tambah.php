<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Tambah Akun</h1>
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
              <div class="form-group">
                <label for="nama_admin">Nama</label>
                <input type="text" name="nama_admin" class="form-control" id="nama_admin" placeholder="Nama" maxlength="300" required>
              </div>
              <div class="form-group">
                <label for="username_admin">Username</label> <span id='messageUname'></span>
                <input type="text" name="username_admin" class="form-control" id="username_admin" placeholder="Username" maxlength="100" autocomplete="off" onkeyup="cekUsername()" required>
              </div>
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="password_admin">Password</label>
                    <input type="password" name="password_admin" class="form-control" id="password_admin" placeholder="Password" maxlength="500" autocomplete="off" onkeyup="cekPass()" required>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="password_admin_cnf">Konfirmasi Password</label> <span id='message'></span>
                    <input type="password" name="password_admin_cnf" class="form-control" id="password_admin_cnf" placeholder="Konfirmasi Password" maxlength="500" autocomplete="off" onkeyup="cekPass()" required>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="jabatan_admin">Jabatan</label></span>
                <select name="jabatan_admin" class="form-control" id="jabatan_admin" readonly required>
                  <option value="2">Admin Tiket</option>
                </select>
              </div>
            </div>
            <div class="card-footer">
              <input type="submit" name="tambah_admin" class="btn btn-primary" id="btnSubmit" value="Submit">
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
  var array_kode_uname = <?php echo json_encode($data_uname)?>;
  function cekUsername(){
    if (array_kode_uname.indexOf($('#username_admin').val()) > -1) {
      $('#messageUname').html(' (Username sudah dipakai)').css('color', 'red');
      document.getElementById("btnSubmit").disabled = true; 
    } else {
      $('#messageUname').html('').css('color', 'red');
      // document.getElementById("btnSubmit").disabled = false;
      cekGabungan(); 
    }
  };
  function cekPass(){
    if ($('#password_admin').val() == $('#password_admin_cnf').val()) {
      $('#message').html('');
      // document.getElementById("btnSubmit").disabled = false; 
      cekGabungan(); 
    } else {
      $('#message').html('Tidak Cocok').css('color', 'red');
      //document.getElementById("btnSubmit").disabled = true; 
    }
  };
  function cekGabungan(){
    if(array_kode_uname.indexOf($('#username_admin').val()) < 0 && $('#password_admin').val() == $('#password_admin_cnf').val()){
      //document.getElementById("btnSubmit").disabled = false;
    }
  }
</script>