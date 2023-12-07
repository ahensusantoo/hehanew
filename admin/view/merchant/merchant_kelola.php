<?php
function jenis_merchant($nomor){
  if($nomor == '1'){
    return 'Souvenir';
  } elseif($nomor == '2'){
    return 'Makanan';
  } elseif($nomor == '3'){
    return 'Refleksi';
  } 
}
?>
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Kelola Merchant</h1>
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
            <h3 class="card-title">Daftar Merchant</h3>
          </div>
          <div class="card-body">
            <table id="example" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama</th>
                  <th>Kode</th>
                  <th>Jenis</th>
                  <th>Tanggal Tambah</th>
                  <th>Aktif</th>
                  <th data-searchable="false" data-orderable="false">Kelola</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $nomor=1;
                $sql = mysqli_query($db,"SELECT * FROM `merchant`");
                while($query = mysqli_fetch_array($sql)) {
                  $eid = enkripsiDekripsi($query['id_merchant'], 'enkripsi');
                  ?>
                  <tr>
                    <td> <?php echo $nomor++; ?> </td>
                    <td> <?php echo $query['nama_merchant']; ?> </td>
                    <td> <?php echo $query['kode_merchant']; ?> </td>
                    <td> <?php echo jenis_merchant($query['status_merchant']); ?> </td>
                    <td data-order="<?php echo tanggal_order($query['tgl_input_merchant']) ?>"> <?php echo tanggal_jam_indo($query['tgl_input_merchant']); ?> </td>
                    <td align="center"> 
                      <div class="form-group">
                        <div class="custom-control custom-switch">
                          <input type="checkbox" class="custom-control-input btn_saklar_merchant" data-id="<?= enkripsiDekripsi($query['id_merchant'],'enkripsi') ?>" id="r<?=$nomor?>" name="status_aktif_merchant" value="on" <?=($query['status_aktif_merchant']=="Y")?'checked':'';?> > 
                          <label class="custom-control-label" for="r<?=$nomor?>"></label>
                        </div>
                      </div>
                    </td>
                    <td align="center"> 
                      <!-- <a href="javascript:void(0);" data-href="view/merchant/merchant_kelola_detail.php?eid=<?php echo $eid; ?>" class="openPopup" data-toggle="tooltip" title="Detail">
                        <button class ="btn btn-success btn-xm" style="margin: 3px"><i class="fa fa-fw fa-file-alt"></i></button>
                      </a> -->
                      <a href="?page=merchant&action=edit&eid=<?php echo $eid ?>" data-toggle="tooltip" title="Edit">
                        <button class ="btn btn-info btn-xm" style="margin: 3px"><i class="fa fa-fw fa-edit"></i></button>
                      </a>
                      <!--<a onclick="return confirm('Yakin data ingin dihapus ?')" href="?page=merchant&action=hapus&eid=<?php echo $eid ?>" data-toggle="tooltip" title="Hapus">-->
                        <!--<button class ="btn btn-danger btn-xm" style="margin: 3px"><i class="fa fa-fw fa-trash"></i></button>-->
                      <!--</a>-->
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
  
  
  
  $(".btn_saklar_merchant").click(function(){
        var id = $(this).attr("data-id");
        var elemen = this;
        if ($(this).is(':checked')) {
            var action = "aktifkan_merchant";
        }else{
            var action = "nonaktifkan_merchant";
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