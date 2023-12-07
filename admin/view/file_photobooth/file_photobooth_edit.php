<?php
$id_photobooth_stan = enkripsiDekripsi(antiSQLi($_GET['eid']), 'dekripsi');
$query = "SELECT * FROM photoboothambil_stan WHERE id_photoboothambil_stan='$id_photobooth_stan'";
$data_photobooth_stan = $db->query($query)->fetch_assoc();
?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Edit File Photobooth</h1>
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
                <label for="nama_photoboothambil_stan">Nama</label>
                <input type="text" name="nama_photoboothambil_stan" class="form-control" id="nama_photoboothambil_stan" placeholder="Nama" maxlength="500" value="<?= $data_photobooth_stan['nama_photoboothambil_stan'] ?>" required>
              </div>
              <div class="form-group">
                <label for="harga_photoboothambil_stan">Harga</label>
                <input class="form-control" type="text" name="harga_photoboothambil_stan" id="harga_photoboothambil_stan" maxlength="20" value="<?= str_replace(',', '.', number_format($data_photobooth_stan['harga_photoboothambil_stan'])) ?>" onkeyup="this.value = this.value.replace(/[^0-9]/g, '').replace(/([0-9])(?=([0-9][0-9][0-9])+(?![0-9]))/g, '$1.')" onchange="this.value = this.value.replace(/[^0-9]/g, '').replace(/([0-9])(?=([0-9][0-9][0-9])+(?![0-9]))/g, '$1.')" readonly>
              </div>    
              <div class="form-group">
                <label for="status_display_photoboothambil">Status Display</label>
                <select name="status_display_photoboothambil" class="form-control" id="status_display_photoboothambil" required>
                  <option value="Y" <?=($data_photobooth_stan['status_display_photoboothambil']=="Y")?'selected':'';?>>Tampil</option>
                  <option value="N" <?=($data_photobooth_stan['status_display_photoboothambil']=="N")?'selected':'';?>>Disembunyikan</option>
                </select>
              </div>                    

            </div>
            <div class="card-footer">
              <input type="submit" name="ubah_photobooth" class="btn btn-primary" id="btnSubmit" value="Submit">
            </div>
          </form>
        </div>
      </div>
      <div class="col-md-6">
      </div>
    </div>
  </div>
</section>