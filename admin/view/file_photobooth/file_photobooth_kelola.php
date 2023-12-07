<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Kelola File Photobooth</h1>
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
            <h3 class="card-title">Daftar File Photobooth</h3>
          </div>
          <div class="card-body">
            <table id="example" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama</th>
                  <th>Harga</th>
                  <th>Status Aktif</th>
                  <th data-searchable="false" data-orderable="false">Kelola</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $nomor=1;
                $sql = mysqli_query($db,"SELECT * FROM photoboothambil_stan WHERE status_remove_photoboothambil='N' ");
                while($query = mysqli_fetch_array($sql)) {
                  $eid = enkripsiDekripsi($query['id_photoboothambil_stan'], 'enkripsi');
                  ?>
                  <tr>
                    <td> <?php echo $nomor++; ?> </td>
                    <td> <?php echo $query['nama_photoboothambil_stan']; ?> </td>
                    <td> <?php echo number_format($query['harga_photoboothambil_stan']); ?> </td>
                    <td>
                        <div class="custom-control custom-switch">
                          <input type="checkbox" class="custom-control-input btn_saklar_photobooth" id="r<?= $nomor ?>"  data-id="<?= $eid ?>"  name="status_aktif" value="on" <?php if($query['status_display_photoboothambil'] == 'Y'){echo "checked";} ?> >
                          <label class="custom-control-label" for="r<?= $nomor ?>"></label>
                        </div>
                    </td>
                    <td align="center">
                      <a href="?page=file_photobooth&action=edit&eid=<?php echo $eid ?>" data-toggle="tooltip" title="Edit">
                        <button class ="btn btn-info btn-xm" style="margin: 3px"><i class="fa fa-fw fa-edit"></i></button>
                      </a>
                      <a onclick="return confirm('Yakin data ingin dihapus ?')" href="view/file_photobooth/proses_data.php?hapus_photobooth=on&eid=<?php echo $eid ?>" data-toggle="tooltip" title="Hapus">
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
  
  $(".btn_saklar_photobooth").click(function(){
        var id = $(this).attr("data-id");
        var elemen = this;
        if ($(this).is(':checked')) {
            var action = "aktifkan_stan_photobooth";
        }else{
            var action = "nonaktifkan_stan_photobooth";
        }
        
        $.ajax({
          url: 'view/file_photobooth/proses_data.php?'+action+'='+id,
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