<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Tambah Supplier</h1>
      </div>
    </div>
  </div>
</section>

<section class="content">
  <div class="container-fluid">
    <form role="form" method="post" action="view/admin/proses_data.php" enctype="multipart/form-data">
      <div class="row">
        <div class="col-md-12">
          <div class="card card-primary">
            <div>
                <a href="?page=supplier&action=kelola" class="btn btn-sm btn-warning float-right" style="margin-right:20px; margin-top:20px;">Kembali</a>
            </div>
            <div class="card-body">
            
            <?php if (isset($_SESSION['notifikasi'])) { notifikasi($_SESSION['notifikasi']); } ?>
              <div class="form-group">
                <label for="kode_supplier">Kode Supplier</label>
                <input type="text" name="kode_supplier" class="form-control" id="kode_supplier" placeholder="Kode Supplier" maxlength="20" required>
              </div>
              <div class="form-group">
                <label for="nama_supplier">Nama Supplier</label>
                <input type="text" name="nama_supplier" class="form-control" id="nama_supplier" placeholder="Nama Supplier" maxlength="100" required>
              </div>
              <div class="form-group">
                <label for="telp_supplier">Nomor Telepon</label>
                <input type="number" name="telp_supplier" class="form-control" id="telp_supplier" placeholder="Telepon Supplier" maxlength="15" autocomplete="off" required>
              </div>
              <div class="form-group">
                <label for="alamat">Alamat Supplier</label>
                <textarea name="alamat" id="alamat" class="form-control" placeholder="Alamat Supplier"></textarea>
              </div>
              <div class="form-group">
                <label for="status_aktif_supplier">Status Aktif Employee</label></span>
                <select name="status_aktif_supplier" class="form-control" id="status_aktif_supplier" required>
                    <option value="Y" <?php if(@$_SESSION['setvalue']["status_aktif_supplier"]== "Y") { echo "selected"; } ?> >AKTIF</option>
                    <option value="N" <?php if(@$_SESSION['setvalue']["status_aktif_supplier"]== "N") { echo "selected"; } ?> >NON AKTIF</option>
                </select>
              </div>
            </div>
            <div class="card-footer">
              <input type="submit" name="tambah_supplier" class="btn btn-primary" id="btnSubmit" value="Submit" style="width: 100%">
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</section>