<?php
function nomor_ke_hari($nomor_hari){
  if($nomor_hari == '1'){
    return 'Senin';
  } elseif($nomor_hari == '2'){
    return 'Selasa';
  } elseif($nomor_hari == '3'){
    return 'Rabu';
  } elseif($nomor_hari == '4'){
    return 'Kamis';
  } elseif($nomor_hari == '5'){
    return 'Jumat';
  } elseif($nomor_hari == '6'){
    return 'Sabtu';
  } elseif($nomor_hari == '7'){
    return 'Minggu';
  }
}
?>
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Kelola Tiket</h1>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <?php if (isset($_SESSION['notifikasi'])) { notifikasi($_SESSION['notifikasi']); } ?>
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Daftar Tiket</h3>
          </div>
          <div class="card-body">
            <table id="example" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama</th>
                  <th>Mulai Hari</th>
                  <th>Selesai Hari</th>
                  <th>Mulai Jam</th>
                  <th>Selesai Jam</th>
                  <th>Harga</th>
                  <th>Tampil</th>
                  <th>Jenis</th>
                  <th data-searchable="false" data-orderable="false">Kelola</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $nomor=1;
                $sql = mysqli_query($db,"SELECT * FROM jenis_tiket WHERE status_remove_tiket='N'");
                while($query = mysqli_fetch_array($sql)) {
                  $eid = enkripsiDekripsi($query['id_jenis_tiket'], 'enkripsi');
                  ?>
                  <tr>
                    <td> <?php echo $nomor++; ?> </td>
                    <td> <?php echo $query['nama_jenis_tiket']; ?> </td>
                    <td> <?php echo nomor_ke_hari($query['start_hari']);?> </td>
                    <td> <?php echo nomor_ke_hari($query['end_hari']);?> </td>
                    <td> <?php echo $query['start_jam']; ?> </td>
                    <td> <?php echo $query['end_jam']; ?> </td>
                    <td> <?php echo number_format($query['harga_tiket']); ?> </td>
                    <td align="center"> 
                      <div class="form-group">
                        <div class="custom-control custom-switch">
                          <input type="checkbox" class="custom-control-input btn_saklar_tiket" id="customSwitch<?=$nomor?>" data-id="<?= $eid ?>" <?=($query['status_display_tiket']=="Y")?'checked':'';?> > 
                          <label class="custom-control-label" for="customSwitch<?=$nomor?>"></label>
                        </div>
                      </div>
                    </td>
                    <td>
                        <?php if($query['status_hari_libur'] == "1"):?>
                            Hari Biasa
                        <?php elseif($query['status_hari_libur'] == "2") : ?>
                            Weekend
                        <?php elseif($query['status_hari_libur'] == "3") : ?>
                            Hari Libur
                        <?php else : ?>
                            -
                        <?php endif; ?>
                    
                    </td>
                    <td align="center"> 
                      <!-- <a href="javascript:void(0);" data-href="view/akun_kelola_detail.php?eid=<?php echo $eid; ?>" class="openPopup" data-toggle="tooltip" title="Detail">
                        <button class ="btn btn-success btn-xm" style="margin: 3px"><i class="fa fa-fw fa-file-alt"></i></button>
                      </a> -->
                      <a href="?page=tiket&action=edit&eid=<?php echo $eid ?>" data-toggle="tooltip" title="Edit">
                        <button class ="btn btn-info btn-xm" style="margin: 3px"><i class="fa fa-fw fa-edit"></i></button>
                      </a>
                      <a onclick="return confirm('Yakin data ingin dihapus ?')" href="view/tiket/proses_data.php?hapus_jenis_tiket=on&eid=<?php echo $eid ?>" data-toggle="tooltip" title="Hapus">
                        <button class ="btn btn-danger btn-xm" style="margin: 3px"><i class="fa fa-fw fa-trash"></i></button>
                      </a>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="myModal" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $('.openPopup').on('click',function(){
      var dataURL = $(this).attr('data-href');
      $('.modal-content').load(dataURL,function(){
        $('#myModal').modal({show:true});
      });
    }); 
  });
  
  $(".btn_saklar_tiket").click(function(){
        var id = $(this).attr("data-id");
        var elemen = this;
        if ($(this).is(':checked')) {
            var action = "aktifkan_jenis_tiket";
        }else{
            var action = "nonaktifkan_jenis_tiket";
        }
        
        $.ajax({
          url: 'view/tiket/proses_data.php?'+action+'='+id,
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