<?php
    $id_voucher = enkripsiDekripsi($_GET['eid'],'dekripsi');
    $data = $db->query("SELECT * FROM voucher_merchant
                        LEFT JOIN merchant ON voucher_merchant.kd_merchant = merchant.id_merchant 
                        WHERE id_voucher='$id_voucher'")->fetch_assoc();
                        
                        
    $list_merchant = mysqli_query($db,"SELECT *
                                    FROM merchant
                                    WHERE status_aktif_merchant = 'Y' ")->fetch_all(MYSQLI_ASSOC);
?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Edit Voucher Tiket</h1>
      </div>
    </div>
  </div>
</section>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-primary">
            <?php if (isset($_SESSION['notifikasi'])) { notifikasi($_SESSION['notifikasi']); } ?>
          <form role="form" method="post" action="view/voucher_merchant/proses_data.php" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $_GET['eid'] ?>">
            <div class="card-body">
              <div class="form-group">
                <label for="nama_voucher">Nama Voucher</label>
                <input type="text" name="nama_voucher" class="form-control" id="nama_voucher" placeholder="Nama" maxlength="300" value="<?= $data['nama_voucher'] ?>" required>
              </div>
              <div class="form-group">
                <label for="kode_voucher">Kode Voucher</label>
                <input type="text" name="kode_voucher" class="form-control" id="kode_voucher" placeholder="Kode" maxlength="20" value="<?= $data['kode_voucher'] ?>" required>
              </div>
              <div class="form-group">
                <label for="potongan_voucher">Minamal Transaksi</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Rp</span>
                  </div>
                  <input type="text" class="form-control" name="min_transaksi" id="min_transaksi" value="<?= $data['min_transaksi'] ?>"  maxlength="20" onkeyup="this.value = this.value.replace(/[^0-9]/g, '').replace(/([0-9])(?=([0-9][0-9][0-9])+(?![0-9]))/g, '$1.')" onchange="this.value = this.value.replace(/[^0-9]/g, '').replace(/([0-9])(?=([0-9][0-9][0-9])+(?![0-9]))/g, '$1.')" required>
                </div>
              </div>
              <div class="form-group">
                <label for="potongan_voucher">Maximal Potongan</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Rp</span>
                  </div>
                  <input type="text" class="form-control" name="max_potongan" id="max_potongan" value="<?= $data['max_potongan'] ?>"  maxlength="20" onkeyup="this.value = this.value.replace(/[^0-9]/g, '').replace(/([0-9])(?=([0-9][0-9][0-9])+(?![0-9]))/g, '$1.')" onchange="this.value = this.value.replace(/[^0-9]/g, '').replace(/([0-9])(?=([0-9][0-9][0-9])+(?![0-9]))/g, '$1.')" required>
                </div>
              </div>
              <div class="form-group">
                <label for="deskripsi_voucher">Deskripsi</label>
                <textarea name="deskripsi_voucher" class="form-control" id="deskripsi_voucher" placeholder="Deskripsi" maxlength="1000" required><?= $data['deskripsi_voucher'] ?></textarea>
              </div>
              <div class="form-group">
                <label for="status_potongan">Jenis</label>
                <select name="status_potongan" class="form-control" id="status_potongan" onchange="ubahjenis()" required>
                  <option value="1" <?php if($data['status_potongan'] == "") {echo "selected";} ?> >Nominal</option>
                  <option value="2" <?php if($data['status_potongan'] == "") {echo "selected";} ?>  >Persen</option>
                </select>
              </div>
              <div class="form-group">
                <label for="potongan_voucher">Potongan</label>
                <div class="input-group">
                  <div class="input-group-prepend" id="potonganrupiah">
                    <span class="input-group-text">Rp</span>
                  </div>
                  <input type="text" class="form-control" name="potongan_voucher" value="<?= $data['potongan_voucher'] ?>" id="potongan_voucher"  maxlength="20" onkeyup="this.value = this.value.replace(/[^0-9]/g, '').replace(/([0-9])(?=([0-9][0-9][0-9])+(?![0-9]))/g, '$1.')" onchange="this.value = this.value.replace(/[^0-9]/g, '').replace(/([0-9])(?=([0-9][0-9][0-9])+(?![0-9]))/g, '$1.')" required>
                  <div class="input-group-append" id="potonganpersen" style="display: none">
                    <span class="input-group-text">%</span>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label>Tanggal Berlaku Voucher</label>
                <div class="input-group">
                  <input type="text" class="form-control" name="daterange" id="daterange" value="" required>
                </div>
              </div>
              <div class="form-group">
                <label for="start_jam">Jam Mulai Berlaku</label>
                <input type="text" name="start_jam" id="start_jam" class="form-control datetime_picker_start" required>
              </div>
              <div class="form-group">
                <label for="end_jam">Jam Selesai Berlaku</label>
                <input type="text" name="end_jam" id="end_jam" class="form-control datetime_picker_end" required>
              </div>
              <div class="form-group">
                <label for="max_pengguna">Batas Pengguna</label>
                <input type="number" name="max_pengguna" class="form-control" id="max_pengguna" placeholder="50" value="<?= $data['max_pengguna'] ?>" required>
              </div>
            </div>
            <div class="card-footer">
              <input type="submit" name="edit_voucher" class="btn btn-primary" id="btnSubmit" value="Submit">
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
  $(".datetime_picker_start").flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    defaultDate: "<?= $data['start_jam'] ?>",
    time_24hr: true
  });
</script>

<script type="text/javascript">
  $(".datetime_picker_end").flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    defaultDate: "<?= $data['end_jam'] ?>",
    time_24hr: true
  });
</script>

<script type="text/javascript">
  $(function () {
    $('#daterange').daterangepicker(
    {
      locale:{
        format: 'MM/DD/YYYY'
      },
      startDate : "<?= date_format(date_create($data['start_tgl']),"m/d/Y") ?>",
      endDate : "<?= date_format(date_create($data['end_tgl']),"m/d/Y") ?>"
    },
    function (start, end) {
      // $('#reportrange').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }
    )
  })

  function ubahjenis(){
    if(document.getElementById("status_potongan").value == 1){
      document.getElementById("potonganpersen").style.display = "none";
      document.getElementById("potonganrupiah").style.display = "block";
      document.getElementById("potongan_voucher").maxLength = "20";
      document.getElementById("potongan_voucher").value = "";
    } else if(document.getElementById("status_potongan").value == 2){
      document.getElementById("potonganpersen").style.display = "block";
      document.getElementById("potonganrupiah").style.display = "none";
      document.getElementById("potongan_voucher").maxLength = "3";
      document.getElementById("potongan_voucher").value = "";
    }
  }
</script>