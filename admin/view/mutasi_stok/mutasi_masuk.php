<?php
//$sess_kd_merchant = enkripsiDekripsi($_GET['merchant'], 'dekripsi');
$query = "SELECT * 
                FROM view_merchant_produk mp
                WHERE mp.kd_merchant = '$_SESSION[kd_merchant]'
                    AND mp.jenis_produk ='2'
                    AND mp.status_remove_produk ='N'
                    AND mp.status_display_produk = 'Y'
                ORDER BY id_merchant_produk DESC";
$data_produk = $db->query($query)->fetch_all(MYSQLI_ASSOC);

?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Mutasi Keluar</h1>
      </div>
    </div>
  </div>
</section>

<section class="content">
  <div class="container-fluid">
    <form role="form" method="post" action="view/mutasi_stok/proses_data.php" enctype="multipart/form-data">
      <div class="row">
        <div class="col-md-12">
          <div class="card card-primary">
            <div class="card-body">
            <?php if (isset($_SESSION['notifikasi'])) { notifikasi($_SESSION['notifikasi']); } ?>
              <div class="form-group">
                <label>Keterangan Mutasi</label>
                <textarea rows="3" class="form-control" name="keterangan_kepala"></textarea>
                <input type="hidden" name="temp_jml" value="0" id="temp_jml">
              </div>
              <hr>
              <label>Data Produk Dimutasi</label>
              
              <button type="button" class="btn btn-sm btn-outline-info float-right mb-2"  data-toggle="modal" data-target="#modal-product">Tambah Produk</button>
              <!--<input type="hidden" name="merchant" value="<?= $_GET['merchant'] ?>">-->
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Jumlah Mutasi</th>
                    <th>Keterangan</th>
                    <th>Hapus</th>
                  </tr>
                  <tbody id="tab_produk_dimutasi">
                    <tr id="td_tanpa_data">
                      <td colspan="12" class="text-center">Belum Ada Produk Terpilih</td>
                    </tr>
                  </tbody>
                </thead>
              </table>
            </div>
            <div class="card-footer">
              <input type="submit" name="mutasi_masuk_gc" class="btn btn-primary" id="btnSubmit" value="Submit" style="width: 100%">
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</section>


<!-- modal -->
<div class="modal fade" id="modal-product">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Pilih Kasir</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hodden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <table class="table table-sm table-bodered table-striped" id="example" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Product</th>
                            <th>Nama Product</th>
                            <!-- <td>jenis</td> -->
                            <th>Pilih</th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php
                        $no = 1;
                        foreach ($data_produk as $o) {
                      ?>
                      <tr>  
                        <td><?= $no++ ?></td>
                        <td><?= $o['kode_produk'] ?></td>
                        <td><?= $o['nama_produk'] ?></td>
                        <!-- <td style="text-align:center"></td> -->
                        <td>
                          <button class="btn btn-xs btn-info pilih_produk" 
                            data-nama_produk="<?=$o['nama_produk']?>"
                            data-id_produk="<?=enkripsiDekripsi($o['id_merchant_produk'],'enkripsi')?>"
                            data-harga_beli_produk="<?=$o['harga_beli']?>"
                            data-kode_produk="<?=$o['kode_produk']?>"
                            data-harga_jual_produk="<?=$o['harga_produk']?>"
                            >
                            <i class="fa fa-check"></i>Select
                          </button>
                        </td>    
                      </tr> 
                      <?php
                          }
                      ?>
                    </tbody>
                </table>             
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
  function cekPass(){
    if ($('#password_employee').val() == $('#password_employee_cnf').val()) {
      $('#message').html('');
      document.getElementById("btnSubmit").disabled = false; 
    } else {
      $('#message').html('Tidak Cocok').css('color', 'red');
      document.getElementById("btnSubmit").disabled = true; 
    }
  };

  $(".pilih_produk").click(function(){
    var temp_jml = parseInt($("#temp_jml").val());
    var temp_jml_after = temp_jml + 1;
    var temp_jml = $("#temp_jml").val(temp_jml_after);

    var id_produk = $(this).data('id_produk');
    var kode_produk = $(this).data('kode_produk');
    var nama_produk = $(this).data('nama_produk');
    var harga_beli_produk = $(this).data('harga_beli_produk');
    var harga_jual_produk = $(this).data('harga_jual_produk');

    // alert(kode_produk)

    $("#tab_produk_dimutasi").append(`

      <tr>
        <td>
          <input type="hidden" name="produk_dimutasi[`+temp_jml_after+`][id]" value="`+id_produk+`">
          <input type="hidden" name="produk_dimutasi[`+temp_jml_after+`][kode_produk]" value="`+kode_produk+`">
          `+kode_produk+`
        </td>
        <td>`+nama_produk+`</td>
        <td><input type="number" class="form-control" name="produk_dimutasi[`+temp_jml_after+`][harga_beli]" value="`+harga_beli_produk+`" ></td>
        <td><input type="number" class="form-control" name="produk_dimutasi[`+temp_jml_after+`][harga_jual]" value="`+harga_jual_produk+`"></td>
        <td><input type="number" class="form-control" name="produk_dimutasi[`+temp_jml_after+`][jml]" value="1" min='1'></td>
        <td><input type="text"  class="form-control" name="produk_dimutasi[`+temp_jml_after+`][ket]"></td>
        <td><button type="button" class="badge badge-danger hapus_persatuan">Hapus</button></td>
      </tr>

    `)

    $("#td_tanpa_data").css('display','none');
    $("#modal-product .close").trigger('click');

  })

  $("#tab_produk_dimutasi").on('click', '.hapus_persatuan', function(){
    $(this).closest('tr').remove();
  })

  
</script>