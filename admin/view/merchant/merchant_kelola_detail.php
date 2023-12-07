<?php
include("../../templates/koneksi.php");
$eid = enkripsiDekripsi(@$_GET['eid'], 'dekripsi');
$sql = mysqli_query($db,"SELECT * FROM `merchant` WHERE id_merchant = '$eid'");
$data = mysqli_fetch_array($sql);

function jenis_merchant($nomor){
  if($nomor == '1'){
    return 'Souvenir';
  } elseif($nomor == '2'){
    return 'Makanan';
  } 
}

function level_employee($nomor){
  if($nomor == '0'){
    return 'Superadmin';
  } elseif($nomor == '1'){
    return 'Admin Kasir';
  } elseif($nomor == '2'){
    return 'Kasir';
  } 
}
?>

<div class="modal-header">
  <h4 class="modal-title">Detail Akun</h4>
</div>

<div class="modal-body">
  <?php
  if(!empty($data['id_merchant'])){
    ?>
    <div class="table-responsive">
      <table class="table table-bordered">
        <tbody>
          <tr>
            <td>Nama</td>
            <td><?php echo $data['nama_merchant'] ?></td>
          </tr>
          <tr>
            <td>Kode</td>
            <td><?php echo $data['kode_merchant'] ?></td>
          </tr>
          <tr>
            <td>Telepon</td>
            <td><?php echo $data['telp_merchant'] ?></td>
          </tr>
          <tr>
            <td>Email</td>
            <td><?php echo $data['email_merchant'] ?></td>
          </tr>
          <tr>
            <td>Panjang x Lebar</td>
            <td><?php echo $data['panjang_merchant'].' x '.$data['lebar_merchant'] ?></td>
          </tr>
          <!--<tr>
            <td>Logo</td>
            <td><img width="100" height="auto" src="<?= base_url().'images/users/'.$data['file_logo'] ?>"></td>
          </tr>-->
          <tr>
            <td>Jenis</td>
            <td><?php echo jenis_merchant($data['status_merchant']) ?></td>
          </tr>
          <tr>
            <td>Tanggal Tambah</td>
            <td><?php echo tanggal_jam_indo($data['tgl_input_merchant']); ?></td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Admin Merchant</h3>
        <a href="?page=merchant&action=tambah-admin&id=<?= enkripsiDekripsi($data['id_merchant'], 'enkripsi') ?>" data-toggle="tooltip" title="Edit">
          <button type="button" class="btn btn-info float-right" style="margin-left: 7px"><i class="fa fa-fw fa-user"></i>Tambah Admin</button>
        </a>
      </div>
      <div class="card-body"> 
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <!-- <th>No</th> -->
                <th>Nama</th>
                <th>Username</th>
                <th>Level</th>
                <th>Aktif</th>
                <th>Kelola</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $nomor=1;
              $sql = mysqli_query($db,"SELECT nama_employee, username_employee, status_aktif_employee, id_merchant_employee, level_employee FROM `merchant_employee` WHERE kd_merchant = '$eid'");
              while($query = mysqli_fetch_array($sql)) {
                $eidd = enkripsiDekripsi($query['id_merchant_employee'], 'enkripsi');
                ?>
                <tr>
                  <!-- <td> <?php echo $nomor++; ?> </td> -->
                  <td> <?php echo $query['nama_employee']; ?> </td>
                  <td> <?php echo $query['username_employee']; ?> </td>
                  <td> <?php echo level_employee($query['level_employee']); ?> </td>
                  <td align="center"> 
                    <div class="form-group">
                      <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input btn_saklar_employee" id="er<?=$nomor?>" name="status_aktif_employee" value="on" <?=($query['status_aktif_employee']=="Y")?'checked':'';?> data-id="<?= $eidd ?>"> 
                        <label class="custom-control-label" for="er<?=$nomor?>"></label>
                      </div>
                    </div>
                  </td>
                  <td align="center"> 
                    <a href="?page=merchant&action=edit-admin&eid=<?php echo $eidd ?>" data-toggle="tooltip" title="Edit">
                      <button class ="btn btn-info btn-xm" style="margin: 3px"><i class="fa fa-fw fa-edit"></i></button>
                    </a>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
    <?php
  }else{
    echo 'Terjadi kesalahan sistem, data tidak ditemukan';
  }
  ?>
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
</div>


<script>
    $(".btn_saklar_employee").click(function(){
        var id = $(this).attr("data-id");
        var elemen = this;
        if ($(this).is(':checked')) {
            var action = "aktifkan_merchant_employee";
        }else{
            var action = "nonaktifkan_merchant_employee";
        }
        
        
        $.ajax({
          url: 'view/merchant/proses_data.php?'+action+'='+id,
          success:function(result){
            if (result.indexOf("gagal") != -1) {
              alert('Terjadi Kegagalan Sistem');
              $(elemen).trigger("change");
            }
          },
          error:function(){
            alert('Terjadi Kegagalan Sistem');
            $(elemen).trigger("change");
          }
        })
  })
</script>