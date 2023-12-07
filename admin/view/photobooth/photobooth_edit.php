<?php
$id_photobooth_stan = enkripsiDekripsi(antiSQLi($_GET['eid']), 'dekripsi');
$query = "SELECT * FROM photobooth_stan WHERE id_photobooth_stan='$id_photobooth_stan'";
$data_photobooth_stan = $db->query($query)->fetch_assoc();

$query = "SELECT * FROM photobooth_stan WHERE status_display_photobooth='Y' AND status_paket='N' AND status_remove_photobooth='N' AND id_photobooth_stan!='$id_photobooth_stan' ";
$daftar_photobooth = $db->query($query)->fetch_all(MYSQLI_ASSOC);

$list_paket = $db->query("SELECT * FROM photobooth_stan_paket WHERE id_photobooth_stan_paket='$id_photobooth_stan' ")->fetch_all(MYSQLI_ASSOC);


?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Edit Spot Photobooth</h1>
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
          <form role="form" method="post" action="view/photobooth/proses_data.php" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $_GET['eid'] ?>">
            <div class="card-body">
              <div class="form-group">
                <label for="nama_photobooth_stan">Nama</label>
                <input type="text" name="nama_photobooth_stan" class="form-control" id="nama_photobooth_stan" placeholder="Nama" maxlength="500" value="<?= $data_photobooth_stan['nama_photobooth_stan'] ?>" required>
              </div>
              <div class="form-group">
                <label for="harga_photobooth_stan">Harga</label>
                <input class="form-control" type="text" name="harga_photobooth_stan" id="harga_photobooth_stan" maxlength="20" value="<?= str_replace(',', '.', number_format($data_photobooth_stan['harga_photobooth_stan'])) ?>" onkeyup="this.value = this.value.replace(/[^0-9]/g, '').replace(/([0-9])(?=([0-9][0-9][0-9])+(?![0-9]))/g, '$1.')" onchange="this.value = this.value.replace(/[^0-9]/g, '').replace(/([0-9])(?=([0-9][0-9][0-9])+(?![0-9]))/g, '$1.')" readonly>
              </div>    
              <div class="form-group">
                <label for="status_display_photobooth">Status Display</label>
                <select name="status_display_photobooth" class="form-control" id="status_display_photobooth" required>
                  <option value="Y" <?=($data_photobooth_stan['status_display_photobooth']=="Y")?'selected':'';?>>Tampil</option>
                  <option value="N" <?=($data_photobooth_stan['status_display_photobooth']=="N")?'selected':'';?>>Disembunyikan</option>
                </select>
              </div>
              <div class="form-group">
                <label for="status_paket">Apakah Ini Paket Spot Foto?</label>
                <select name="status_paket" class="form-control" id="status_paket" required>
                  <option value="Y" <?=($data_photobooth_stan['status_paket']=="Y")?'selected':'';?>>Ya</option>
                  <option value="N" <?=($data_photobooth_stan['status_paket']=="N")?'selected':'';?>>Bukan</option>
                </select>
              </div>                    

              <?php if ($data_photobooth_stan['status_paket'] == "Y"): ?>
                <div id="tab_paket" >
              <?php else: ?>
                <div id="tab_paket" style="display: none;">
              <?php endif ?>
                <hr>
                <button class="float-right btn btn-sm btn-info" type="button" data-toggle="modal" data-target="#modal_daftar_photobooth">Pilih Spot Foto</button>
                <table class="table table-bordered mt-5">
                  <thead>
                    <tr>
                      <th>Spot Foto</th>
                      <th>Hapus</th>
                    </tr>
                  </thead>
                  <tbody id="tab_isi_detail_paket">
                    <?php foreach ($daftar_photobooth as $key => $value): ?>
                      <?php foreach ($list_paket as $key_paket => $value_paket): ?>
                        <?php if ($value_paket['id_photobooth_stan'] == $value['id_photobooth_stan']): ?>
                          <tr>
                            <td>
                              <input type="hidden" name="paket[<?= enkripsiDekripsi($value['id_photobooth_stan'], 'enkripsi') ?>]">
                              <?= $value['nama_photobooth_stan'] ?>
                            </td>
                            <td>
                              <button type="button" class="btn btn-sm btn-danger hapus_paket">Hapus</button>
                            </td>
                          </tr>
                        <?php endif ?>
                      <?php endforeach ?>
                    <?php endforeach ?>
                  </tbody>
                </table>
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




<div class="modal fade" id="modal_daftar_photobooth" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Daftar Photobooth</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <table class="table-bordered table-photobooth" width="100%">
            <thead>
              <tr>
                <th>Nama</th>
                <th>Harga</th>
                <th>Pilih</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($daftar_photobooth as $key => $value): ?>
                <tr>
                  <td><?= $value['nama_photobooth_stan'] ?></td>
                  <td nowrap="">Rp <?= number_format($value['harga_photobooth_stan']) ?></td>
                  <td nowrap=""><button type="button" class="btn btn-sm btn-outline-info btn-block btn-less-padding btn_pilih_photobooth" data-id="<?= enkripsiDekripsi($value['id_photobooth_stan'],'enkripsi') ?>" data-nama="<?= $value['nama_photobooth_stan'] ?>" data-harga="<?= $value['harga_photobooth_stan'] ?>">Pilih</button></td>
                </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
    </div>
  </div>
</div>



<script>
  $('#status_paket').change(function(){
    var isi = $(this).val();
    if (isi == "Y") {
      $('#tab_paket').show();
    }else{
      $('#tab_paket').hide();
    }
  })

  $('.btn_pilih_photobooth').click(function(){
    var nama = $(this).data('nama');
    var id = $(this).data('id');
    $('#tab_isi_detail_paket').append(`
      <tr>
        <td>
          <input type="hidden" name="paket[`+id+`]">
          `+nama+`
        </td>
        <td>
          <button type="button" class="btn btn-sm btn-danger hapus_paket">Hapus</button>
        </td>
      </tr>
    `);
    $('#modal_daftar_photobooth .close').trigger('click');
  })

  $('#tab_isi_detail_paket').on('click', '.hapus_paket', function(){
    $(this).closest('tr').remove();
  })
</script>

