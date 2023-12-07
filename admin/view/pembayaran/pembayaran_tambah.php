<?php
?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Tambah Pembayaran</h1>
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
          <form role="form" method="post" action="view/pembayaran/proses_data.php" enctype="multipart/form-data">
            <div class="card-body">
              <div class="form-group">
                <label for="nama_jenis_pembayaran">Nama</label>
                <input type="text" name="nama_jenis_pembayaran" class="form-control" id="nama_jenis_pembayaran" placeholder="Nama" maxlength="300" required>
              </div>                 

            </div>
            <div class="card-footer">
              <input type="submit" name="tambah_jenis_pembayaran" class="btn btn-primary" id="btnSubmit" value="Submit">
            </div>
          </form>
        </div>
      </div>
      <div class="col-md-6">
      </div>
    </div>
  </div>
</section>