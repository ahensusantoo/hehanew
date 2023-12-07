<?php  
  function setValue($value){
    $val = @$_SESSION['setvalue'][$value];
    unset($_SESSION['setvalue'][$value]);
    echo $val;
  }
    $sess_kd_merchant = $_SESSION['kd_merchant'];
  $id_produk = enkripsiDekripsi($_GET['id'],'dekripsi');
//   $sql = "SELECT * 
//             FROM merchant_produk A
//             LEFT JOIN merchant_stok B ON A.id_merchant_produk = B.kd_merchant_produk
//             WHERE id_merchant_produk = '$id_produk'
//                 AND A.kd_merchant = '$sess_kd_merchant' ";
//   $data_produk = $db->query($sql)->fetch_assoc();
  
  $sql = "SELECT * 
            FROM view_merchant_produk A
            LEFT JOIN merchant_stok B ON A.id_merchant_produk = B.kd_merchant_produk
            WHERE A.id_merchant_produk = '$id_produk'
                AND A.kd_merchant = '$sess_kd_merchant' ";
  $data_produk = $db->query($sql)->fetch_assoc();

  $sql = "SELECT * 
            FROM merchant_kategori_produk
            WHERE kd_merchant = '$sess_kd_merchant' ";
  $data_kategori = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
 
    // echo "<pre>";
    // echo print_r($data_kategori);
    // echo die();
?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Edit Product</h1>
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
          <form role="form" method="post" action="view/product/proses_data.php" enctype="multipart/form-data">
            <div class="card-body">
              
              <div class="form-group">
                <label for="kode_produk">Kode Product</label>
                <input type="hidden" name="id_merchant_produk" value="<?= $_GET['id'] ?>">
                <input type="text" name="kode_produk" value="<?= $data_produk['kode_produk'] ?>" class="form-control" id="kode_produk" placeholder="kode Product" maxlength="10" required>
              </div>
              
              <div class="form-group">
                <label for="nama_produk">Nama Product</label>
                <input type="text" name="nama_produk" value="<?= $data_produk['nama_produk'] ?>" class="form-control" id="nama_produk" placeholder="Nama Product" maxlength="300" required>
              </div>

              <div class="form-group">
                <label for="kategori"> Pilih Kategory</label>
                <select id="kategori" name="kategori" class="form-control">
                <?php foreach ($data_kategori as $data_kategori => $value) { ?>
                  <option value="<?= $value['id_merchant_kategori_produk'] ?>"<?=$value['id_merchant_kategori_produk'] == $data_produk['kd_merchant_kategori']  ? 'selected' : null ?> > <?=$value['nama_kategori'] ?></option>;
                  <?php } ?>
                </select>
              </div>

              <div class="form-group">
                <label for="harga_produk">Harga Beli Product</label>
                <input type="text" name="harga_beli_produk" value="<?= number_format($data_produk['harga_beli']) ?>" class="form-control" placeholder="Harga Product" maxlength="300" onkeyup="format_nominal(this)" readonly required/>
              </div>
              
              <div class="form-group">
                <label for="harga_produk">Harga Jual Product</label>
                <input type="text" name="harga_jual_produk" value="<?= number_format($data_produk['harga_produk']) ?>" class="form-control" placeholder="Harga Product" maxlength="300" onkeyup="format_nominal(this)" readonly required/>
              </div>
              
              <!--<div class="form-group">
                <label for="jenis_stock"> Jenis Stock</label>
                <select id="jenis_stock" name="jenis_stock" class="form-control" readonly disable>
                    <option value="">Pilih</option>;
                    <option value="1" <?=$data_produk['jenis_produk'] == "1" ? 'selected' : null ?> readonly>Bukan Barang Stock</option>;
                    <option value="2" <?=$data_produk['jenis_produk'] == "2" ? 'selected' : null ?> readonly>Stock</option>;
                </select>
              </div>-->
            
               <div class="form-group">
                <label for="stok_produk">Jenis Stock</label>
                <?php if ($data_produk['jenis_produk'] == "1"): ?>
                    <input type="text" name="jenis_stock" value="Bukan Barang Stock" class="form-control" id="jenis_stock" maxlength="300" readonly>
                <?php elseif($data_produk['jenis_produk'] == "2") : ?>
                    <input type="text" name="jenis_stock" value="Stock" class="form-control" id="jenis_stock" maxlength="300" readonly>
                <?php endif; ?>
              </div> 
              
              <div class="form-group" id="tampil_stock">
                <label for="stok_produk">Stock Product</label>
                <input type="number" name="stok_produk" value="<?= $data_produk['stok_saat_ini'] ?>" class="form-control" id="stok_produk" placeholder="Stock Product" maxlength="300" readonly>
              </div>

              <div class="form-group">
                <label for="diskon_produk">Discount Product</label>
                <input type="number" name="diskon_produk" value="<?= $data_produk['diskon'] ?>" class="form-control" id="diskon_produk" placeholder="Discount Product" maxlength="300">
                  <small style="color:red">Dalam bentuk persen(%)</small>
              </div>

              <div class="form-group">
                <label for="konsi"> Status Konsi</label>
                <select id="konsi" name="konsi" class="form-control">
                  <option value="Y" <?=$data_produk['status_konsi'] == "Y" ? 'selected' : null ?> >Titipan</option>;
                  <option value="N" <?=$data_produk['status_konsi'] == "N" ? 'selected' : null ?> >Non Titipan</option>;
                </select>
              </div>
              
               <div class="form-group">
                <label for="status_tampil"> Status Produk Tampil</label>
                <select id="status_tampil" name="status_tampil" class="form-control">
                  <option value="Y" <?=$data_produk['status_display_produk'] == "Y" ? 'selected' : null ?> >Tampil</option>;
                  <option value="N" <?=$data_produk['status_display_produk'] == "N" ? 'selected' : null ?> >Tidak Ditampilin</option>;
                </select>
              </div>

              <div class="form-group">
                <label for="diskon_produk">Gambat Product</label>
                <?php if ($data_produk['gambar_produk'] != null ) { ?>
                  <div style="margin-bottom:5px">
                    <img src="<?= base_url().'/dist/img/barang/'.$data_produk['gambar_produk'] ?>" style="width: 100px">
                  </div>
                <?php }  ?>
                <input type="file" name="gambar_produk" value="" class="form-control" id="gambar_produk" placeholder="Gambar Product" maxlength="300">
                <small style="color:red">Biarkan kosong jika tidak ingin merubah gambar!!!</small>
              </div>
            
            </div>
            <div class="card-footer">
              <input type="submit" name="edit_produk" class="btn btn-primary" id="btnSubmit" value="Submit">
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
		
		$(document).ready(function(){
		    var jenis_stock = $('#jenis_stock').val();
		  //  alert(jenis_stock)
		    if (jenis_stock == ""){
                alert('Anda Belum Memilih Jenis Stock');
            }else if(jenis_stock == "Stock"){
                $('#tampil_stock').css("display", "inline")
                //$('#stok_produk').attr("required", "")
            }else{
                $('#tampil_stock').css("display", "none")
                //$('#stok_produk').removeAttr("required")
            }
		})
		
// 		$('#jenis_stock').change(function(){
// 		    var jenis_stock = $('#jenis_stock').val();
// 		  //  alert()
// 		    if (jenis_stock == ""){
//                 alert('Anda Belum Memilih Jenis Stock');
//             }else if(jenis_stock == "2"){
//                 $('#tampil_stock').css("display", "inline")
//                 //$('#stok_produk').attr("required", "")
//             }else{
//                 $('#tampil_stock').css("display", "none")
//                 //$('#stok_produk').removeAttr("required")
//             }
// 		})
	</script>

