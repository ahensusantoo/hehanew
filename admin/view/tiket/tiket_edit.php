<?php
    
    $id_tiket = enkripsiDekripsi(antiSQLi($_GET['eid']), 'dekripsi');
    $query = "SELECT * FROM jenis_tiket WHERE id_jenis_tiket='$id_tiket'";
    $data_tiket = $db->query($query)->fetch_assoc();

?>
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Tambah Tiket</h1>
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
          <form role="form" method="post" action="view/tiket/proses_data.php" enctype="multipart/form-data">
            <div class="card-body">
              <div class="form-group">
                <label for="nama_tiket">Nama</label>
                <input type="hidden" name="id_jenis_tiket" value="<?= $_GET['eid'] ?>">
                <input type="text" name="nama_tiket" class="form-control" id="nama_tiket" placeholder="Nama" maxlength="300" value="<?= $data_tiket['nama_jenis_tiket'] ?>" required>
              </div>
              <div class="form-group">
                <label for="start_hari">Hari Mulai</label>
                <select name="start_hari" class="form-control" id="start_hari" required>
                  <option value="1" <?=($data_tiket['start_hari']=="1")?'selected':'';?> >Senin</option>
                  <option value="2" <?=($data_tiket['start_hari']=="2")?'selected':'';?> >Selasa</option>
                  <option value="3" <?=($data_tiket['start_hari']=="3")?'selected':'';?> >Rabu</option>
                  <option value="4" <?=($data_tiket['start_hari']=="4")?'selected':'';?> >Kamis</option>
                  <option value="5" <?=($data_tiket['start_hari']=="5")?'selected':'';?> >Jumat</option>
                  <option value="6" <?=($data_tiket['start_hari']=="6")?'selected':'';?> >Sabtu</option>
                  <option value="7" <?=($data_tiket['start_hari']=="7")?'selected':'';?> >Minggu</option>
                </select>
              </div>
              <div class="form-group">
                <label for="end_hari">Hari Selesai</label>
                <select name="end_hari" class="form-control" id="end_hari" required>
                  <option value="1" <?=($data_tiket['end_hari']=="7")?'selected':'';?> >Senin</option>
                  <option value="2" <?=($data_tiket['end_hari']=="7")?'selected':'';?> >Selasa</option>
                  <option value="3" <?=($data_tiket['end_hari']=="7")?'selected':'';?> >Rabu</option>
                  <option value="4" <?=($data_tiket['end_hari']=="7")?'selected':'';?> >Kamis</option>
                  <option value="5" <?=($data_tiket['end_hari']=="7")?'selected':'';?> >Jumat</option>
                  <option value="6" <?=($data_tiket['end_hari']=="7")?'selected':'';?> >Sabtu</option>
                  <option value="7" <?=($data_tiket['end_hari']=="7")?'selected':'';?> >Minggu</option>
                </select>
              </div>
              <div class="form-group">
                <label for="start_jam">Jam Mulai</label>
                <input type="text" class="form-control datetime_picker" name="start_jam" id="start_jam" placeholder="12:00" data-timepicker value="<?= $data_tiket['start_jam'] ?>" required>
              </div>
              <div class="form-group">
                <label for="end_jam">Jam Selesai</label>
                <input type="text" name="end_jam" id="end_jam" class="form-control datetime_picker" placeholder="12:00" data-timepicker value="<?= $data_tiket['end_jam'] ?>" required>
              </div>
              <div class="form-group">
                <label for="harga_tiket">Harga Tiket</label>
                <input class="form-control" type="text" name="harga_tiket" id="harga_tiket" maxlength="20" value="<?= $data_tiket['harga_tiket'] ?>" onkeyup="this.value = this.value.replace(/[^0-9]/g, '').replace(/([0-9])(?=([0-9][0-9][0-9])+(?![0-9]))/g, '$1.')" onchange="this.value = this.value.replace(/[^0-9]/g, '').replace(/([0-9])(?=([0-9][0-9][0-9])+(?![0-9]))/g, '$1.')" readonly>
              </div>    
              <div class="form-group">
                <label for="status_hari_linur">Status Hari Libur</label>
                <select name="status_hari_libur" class="form-control" id="status_hari_libur" required>
                  <option value="1" <?=($data_tiket['status_hari_libur']=="1")?'selected':'';?> >Hari Biasa</option>
                  <option value="2" <?=($data_tiket['status_hari_libur']=="2")?'selected':'';?> >Weekend & Sebelum Libur</option>
                  <option value="3" <?=($data_tiket['status_hari_libur']=="3")?'selected':'';?> >Hari Libur</option>
                </select>
              </div>       
              <hr>
              <div class="form-group">
                <div class="custom-control custom-switch">
                  <input type="checkbox" class="custom-control-input" id="customSwitch1" name="status_aktif" value="on" <?=($data_tiket['status_display_tiket']=="Y")?'checked':'';?> >
                  <label class="custom-control-label" for="customSwitch1">Status Aktif Tiket</label>
                </div>
              </div>

            </div>
            <div class="card-footer">
              <input type="submit" name="ubah_jenis_tiket" class="btn btn-primary" id="btnSubmit" value="Submit">
            </div>
          </form>
        </div>
      </div>
      <div class="col-md-6">
      </div>
    </div>
  </div>
</section>