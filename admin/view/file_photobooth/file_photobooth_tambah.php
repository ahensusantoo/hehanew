<?php
?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Tambah File Photobooth</h1>
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
          <form role="form" method="post" action="view/file_photobooth/proses_data.php" enctype="multipart/form-data">
            <div class="card-body">
              <div class="form-group">
                <label for="nama_photobooth_stan">Nama</label>
                <input type="text" name="nama_photobooth_stan" class="form-control" id="nama_photobooth_stan" placeholder="Nama" maxlength="300" required>
              </div>
              <div class="form-group">
                <label for="harga_photobooth_stan">Harga</label>
                <input class="form-control" type="text" name="harga_photobooth_stan" id="harga_photobooth_stan" maxlength="20" onkeyup="this.value = this.value.replace(/[^0-9]/g, '').replace(/([0-9])(?=([0-9][0-9][0-9])+(?![0-9]))/g, '$1.')" onchange="this.value = this.value.replace(/[^0-9]/g, '').replace(/([0-9])(?=([0-9][0-9][0-9])+(?![0-9]))/g, '$1.')">
              </div>    
              <div class="form-group">
                <label for="status_display_photobooth">Status Display</label>
                <select name="status_display_photobooth" class="form-control" id="status_display_photobooth" required>
                  <option value="Y">Tampil</option>
                  <option value="N">Disembunyikan</option>
                </select>
              </div>                    

            </div>
            <div class="card-footer">
              <input type="submit" name="tambah_photobooth" class="btn btn-primary" id="btnSubmit" value="Submit">
            </div>
          </form>
        </div>
      </div>
      <div class="col-md-6">
      </div>
    </div>
  </div>
</section>