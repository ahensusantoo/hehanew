<?php
$eid = enkripsiDekripsi($_GET['eid'],'dekripsi');

$data_asli = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM merchant_kategori_produk WHERE id_merchant_kategori_produk = '$eid'"));
?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Edit Kategori</h1>
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
                <input type='hidden' name='merchant' value="<?= $_GET['merchant'] ?>">
              <input type='hidden' name='id_kategori' value="<?= $_GET['eid'] ?>">
              <div class="form-group">
                <label for="kode_kategori">Kode Kategori</label>
                <input type="text" name="kode_kategori" value="<?= $data_asli['kode_kategori'] ?>" class="form-control" id="kode_kategori" placeholder="Kode Kategori" maxlength="300" required>
              </div>
              <div class="form-group">
                <label for="nama_kategori">Nama Kategori</label>
                <input type="text" name="nama_kategori" class="form-control" id="nama_kategori" placeholder="Nama Kategori" maxlength="300" value="<?= $data_asli['nama_kategori']?>" required>
              </div>
              <div class="form-group">
                <label>Status Aktif Kategori</label>
                <select class="form-control" name="status_tampil">
                    <option value="Y" <?php if($data_asli['status_aktif_kategori'] == 'Y'){echo'selected';}; ?>>Aktif</option>
                    <option value="N" <?php if($data_asli['status_aktif_kategori'] == 'N'){echo'selected';}; ?>>Nonaktif</option>
                </select>
              </div>
            </div>
            <div class="card-footer">
              <input type="submit" name="edit_merchant_kategori_produk" class="btn btn-primary" id="btnSubmit" value="Submit" style="width: 100%">
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</section>