<?php
    $id_shift = enkripsiDekripsi(antiSQLi($_GET['eid']),'dekripsi');
    
    $data_shift = $db->query("SELECT * FROM shift WHERE id_shift='$id_shift'")->fetch_assoc();
?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Edit Shift</h1>
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
          <form role="form" method="post" action="view/shift/proses_data.php" enctype="multipart/form-data">
            <input type="hidden" name="id_shift" value="<?= $_GET['eid'] ?>">
            <div class="card-body">
              <div class="form-group">
                <label for="nama_shift">Nama Shift</label>
                <input type="text" name="nama_shift" class="form-control" id="nama_shift" placeholder="Nama" maxlength="300"  value="<?= $data_shift['nama_shift'] ?>"required>
              </div>
              <hr>
              <div class="form-group">
                <div class="custom-control custom-switch">
                  <input type="checkbox" class="custom-control-input" id="customSwitch1" name="status_aktif" value="on" <?=($data_shift['status_aktif_shift']=="Y")?'checked':'';?> >
                  <label class="custom-control-label" for="customSwitch1">Status Aktif Shift</label>
                </div>
              </div>
            </div>
            <div class="card-footer">
              <input type="submit" name="ubah_shift" class="btn btn-primary" id="btnSubmit" value="Submit">
            </div>
          </form>
        </div>
      </div>
      <div class="col-md-6">
      </div>
    </div>
  </div>
</section>
