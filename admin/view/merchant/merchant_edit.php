<?php
    $id = enkripsiDekripsi(antiSQLi($_GET['eid']),'dekripsi');
    $data = $db->query("SELECT * FROM merchant WHERE id_merchant='$id' ")->fetch_assoc();
?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Edit Merchant</h1>
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
                <label for="nama_merchant">Nama</label>
                <input type="hidden" name="id" value="<?= $_GET['eid'] ?>">
                <input type="text" name="nama_merchant" class="form-control" id="nama_merchant" placeholder="Nama" value="<?= $data['nama_merchant'] ?>" maxlength="300" required>
              </div>
              <div class="form-group">
                <label for="kode_merchant">Kode</label>
                <input type="text" name="kode_merchant" class="form-control" id="kode_merchant" placeholder="Kode" value="<?= $data['kode_merchant'] ?>" maxlength="3" required>
              </div>
              <div class="form-group">
                <label for="telp_merchant">Telepon</label>
                <input type="text" name="telp_merchant" class="form-control" id="telp_merchant" placeholder="Telepon" value="<?= $data['telp_merchant'] ?>" maxlength="20" required>
              </div>
              <div class="form-group">
                <label for="telp_merchant">Email</label>
                <input type="email" name="email_merchant" class="form-control" id="email_merchant" placeholder="Email" value="<?= $data['email_merchant'] ?>" maxlength="100" required>
              </div>
              <div class="form-group">
                <label for="panjang_merchant">Panjang</label>
                <input type="number" name="panjang_merchant" class="form-control" id="panjang_merchant" placeholder="Panjang" value="<?= $data['panjang_merchant'] ?>" maxlength="10" required>
              </div>
              <div class="form-group">
                <label for="lebar_merchant">Lebar</label>
                <input type="number" name="lebar_merchant" class="form-control" id="lebar_merchant" placeholder="Lebar" value="<?= $data['lebar_merchant'] ?>" maxlength="10" required>
              </div>
              <!--<div class="form-group">
                <label for="file_logo">Logo</label>
                <input type="file" name="file_logo" class="form-control" id="file_logo" required>
              </div>-->
              <div class="form-group">
                <label for="status_merchant">Jenis</label>
                <select name="status_merchant" class="form-control" id="status_merchant" required>
                  <option value="1" <?php if($data['status_merchant'] == "1"){echo'selected';} ?> >Souvenir</option>
                  <option value="2" <?php if($data['status_merchant'] == "2"){echo'selected';} ?> >Makanan</option>
                  <option value="3" <?php if($data['status_merchant'] == "3"){echo'selected';} ?> >Refleksi</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-12">
          <div class="card card-primary">
            <div class="card-footer">
              <input type="submit" name="edit_merchant" class="btn btn-primary" id="btnSubmit" value="Submit" style="width: 100%">
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</section>