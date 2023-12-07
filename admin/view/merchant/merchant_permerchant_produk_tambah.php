<?php  

  //selasi dengan tabel mercahnt
    $id_merchant = enkripsiDekripsi($_GET['merchant'], 'dekripsi');
    $query = "SELECT * FROM merchant_kategori_produk 
              WHERE status_aktif_kategori='Y' AND status_remove_kategori='N' 
                AND kd_merchant = '$id_merchant'
              ORDER BY id_merchant_kategori_produk DESC";
    $data_kategori = $db->query($query)->fetch_all(MYSQLI_ASSOC);

    $list_supplier = $db->query("SELECT * FROM supplier WHERE status_rmv_supplier='N'")->fetch_all(MYSQLI_ASSOC);
 
?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Tambah Product</h1>
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
          <form role="form" method="post" action="view/merchant/proses_data.php" enctype="multipart/form-data">
            <input type="hidden" name="merchant" value="<?= $_GET['merchant'] ?>">
            <div class="card-body">
              <div class="form-group">
                <label for="nama_produk">Nama Product</label>
                <input type="text" name="nama_produk" value="" class="form-control" id="nama_produk"  placeholder="Nama Product" maxlength="300" required>
              </div>
              
              <div class="form-group">
                <label for="kode_produk">Kode Product</label>
                <input type="text" name="kode_produk" value="" class="form-control" id="kode_produk"  placeholder="Kode Product" maxlength="10" required>
                <small class="text-red">maximal 10 karakter</small>
              </div>

              <div class="form-group">
                <label for="kategori"> Pilih Kategory</label>
                <select id="kategori" name="kategori" class="form-control">
                  <?php foreach ($data_kategori as $value) { ?>
                      <option value="<?=$value['id_merchant_kategori_produk'] ?>"><?= $value['nama_kategori'] ?></option>;
                  <?php } ?>
                </select>
              </div>
 
              <div class="form-group">
                <label for="harga_produk">Harga Beli Product</label>
                <input type="text" name="harga_beli_produk" class="form-control" placeholder="Harga Beli Product" maxlength="300" onkeyup="format_nominal(this)" required/>
              </div>

              <div class="form-group">
                <label for="harga_produk">Barcoode</label>
                <input type="text" name="barcode" class="form-control" placeholder="Barcode Produk" maxlength="300" />
              </div>

              <div class="form-group">
                <label for="supp">Supplier</label>
                <select id="supp" name="supplier" class="form-control" required="">
                    <?php foreach ($list_supplier as $key => $value): ?>
                      <option value="<?= enkripsiDekripsi($value['id_supplier'], 'enkripsi') ?>"><?= $value['nama_supplier'] ?></option>
                    <?php endforeach ?>
                </select>
              </div>
              
              <div class="form-group">
                <label for="harga_produk">Harga Jual Product</label>
                <input type="text" name="harga_jual_produk" class="form-control" placeholder="Harga Jual Product" maxlength="300" onkeyup="format_nominal(this)" required/>
              </div>
              
              <div class="form-group">
                <label for="jenis_stock"> Jenis Stock</label>
                <select id="jenis_stock" name="jenis_stock" class="form-control ">
                    <option value="">Pilih</option>;
                    <option value="1">Bukan Barang Stock</option>;
                    <option value="2">Stock</option>;
                </select>
              </div>
              
              <div class="form-group" id="tampil_stock" style="display:none">
                <label for="stok">Stock Product</label>
                <input type="number" name="stok_produk" value="" class="form-control" id="stok_produk" placeholder="Stock Product" maxlength="300">
              </div>

              <div class="form-group">
                <label for="diskon_produk">Discount Product</label>
                <input type="number" name="diskon_produk"  value="" class="form-control" id="diskon_produk" placeholder="Discount Product" maxlength="300">
                <small style="color:red">Dalam bentuk persen(%)</small>
              </div>

              <div class="form-group">
                <label for="konsi"> Status Konsi</label>
                <select id="konsi" name="konsi" class="form-control">
                  <option value="Y">Titipan</option>;
                  <option value="N">Non Titipan</option>;
                </select>
              </div>
              
              <div class="form-group">
                <label for="status_tampil"> Status Produk Tampil</label>
                <select id="status_tampil" name="status_tampil" class="form-control">
                  <option value="Y">Tampil</option>;
                  <option value="N">Tidak Ditampilin</option>;
                </select>
              </div>

              <div class="form-group">
                <label for="diskon_produk">Gambar Product</label>
                <input type="file" name="gambar_produk" value="" class="form-control" id="gambar_produk" placeholder="Gambar Product" maxlength="300">
                <small style="color:red">Ekstensi yang di perbolehkan .jpg | .jpeg |.png</small>
              </div>
            
            </div>
            <div class="card-footer">
              <input type="submit" name="tambah_merchant_produk" class="btn btn-primary" id="btnSubmit" value="Submit">
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
		
	function format_nominal(arg){
        var bayar = $(arg).val().replace(/[^0-9]/g, '');
            if (bayar == 0) {bayar = 0}
            $(arg).val(parseInt(bayar).toLocaleString());
	}
		
    $('#jenis_stock').change(function(){
        var jenis_stock = $('#jenis_stock').val();
        if (jenis_stock == ""){
            alert('Anda Belum Memilih Jenis Stock');
        }else if(jenis_stock == "2"){
            $('#tampil_stock').css("display", "inline")
            $('#stok_produk').attr("required", "")
        }else{
            $('#tampil_stock').css("display", "none")
            $('#stok_produk').removeAttr("required")
        }
    })
    
</script>
