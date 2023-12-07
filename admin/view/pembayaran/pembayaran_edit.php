<?php
$id_jenis_pembayaran = enkripsiDekripsi(antiSQLi($_GET['eid']), 'dekripsi');
$query = "SELECT * FROM jenis_pembayaran WHERE id_jenis_pembayaran='$id_jenis_pembayaran'";
$data_jenis_pembayaran = $db->query($query)->fetch_assoc();
?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Edit Pembayaran</h1>
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
          <form role="form" method="post" action="" enctype="multipart/form-data">
            <div class="card-body">
              <div class="form-group">
                <label for="nama_jenis_pembayaran">Nama</label>
                <input type="text" name="nama_jenis_pembayaran" class="form-control" id="nama_jenis_pembayaran" placeholder="Nama" maxlength="300" value="<?= $data_jenis_pembayaran['nama_jenis_pembayaran'] ?>" required>
              </div>
            </div>
            <div class="card-footer">
              <input type="submit" name="btnSubmit" class="btn btn-primary" id="btnSubmit" value="Submit">
            </div>
          </form>
        </div>
      </div>
      <div class="col-md-6">
      </div>
    </div>
  </div>
</section>