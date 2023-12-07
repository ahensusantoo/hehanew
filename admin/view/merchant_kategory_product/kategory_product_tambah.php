<?php  
  function setValue($value){
    $val = @$_SESSION['setvalue'][$value];
    unset($_SESSION['setvalue'][$value]);
    echo $val;
  }

  //selasi dengan tabel mercahnt
  $sess_kd_merchant = $_SESSION['kd_merchant'];
  $query = "SELECT * 
            FROM merchant 
            WHERE status_aktif_merchant='Y'
                AND id_merchant = '$sess_kd_merchant'
            ORDER BY id_merchant DESC";
  $data_merchant = $db->query($query)->fetch_all(MYSQLI_ASSOC);
  // echo "<pre>";
  // echo print_r($data_merchant);
  // echo "</prev>";
?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Tambah Kategory Product</h1>
      </div>
    </div>
  </div>
</section>

<section class="content">
  <div class="container-fluid">
        <?php if (isset($_SESSION['notifikasi'])) { notifikasi($_SESSION['notifikasi']); } ?>
    <div class="row">
      <div class="col-md-12">
        <div class="card card-primary">
          <!-- <div class="card-header">
            <h3 class="card-title">Quick Example <small>jQuery Validation</small></h3>
          </div> -->
          <form role="form" method="post" action="view/merchant_kategory_product/proses_data.php" enctype="multipart/form-data">
            <div class="card-body">
                <div class="form-group">
                    <label for="kode_kategori">Kode Kategory</label>
                    <input type="text" name="kode_kategori" value="<?php setValue("kode_kategori") ?>" class="form-control" id="kode_kategori" placeholder="Kode Kategory" maxlength="10" required>
                    <small class="text-red">Maximum 10 karakter</small>
                </div>
                
                <div class="form-group">
                    <label for="nama_kategori">Nama Kategory</label>
                    <input type="text" name="nama_kategori" value="<?php setValue("nama_kategori") ?>" class="form-control" id="nama_kategori" placeholder="Nama Kategory" maxlength="300" required>
                </div>
                
                <div class="form-group">
                    <label for="status_tampil"> Status Kategori Tampil</label>
                    <select id="status_tampil" name="status_tampil" class="form-control">
                      <option value="Y">Aktif</option>;
                      <option value="N">Non Aktif</option>;
                    </select>
                </div>
              
            </div>
            <div class="card-footer">
              <input type="submit" name="tambah_merchant_kategory_product" class="btn btn-primary" id="btnSubmit" value="Submit">
            </div>
          </form>
        </div>
      </div>
      <div class="col-md-6">
      </div>
    </div>
  </div>
</section>

