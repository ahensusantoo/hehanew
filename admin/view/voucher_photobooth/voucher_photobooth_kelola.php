<?php
function jenis_voucher($nomor){
  if($nomor == '1'){
    return 'Nominal';
  } elseif($nomor == '2'){
    return 'Persen';
  } 
}
?>
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Kelola Voucher Photobooth</h1>
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
            <h3 class="card-title">Daftar Voucher Photobooth</h3>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="example" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Kode</th>
                    <th>Deskripsi</th>
                    <th>Jenis</th>
                    <th>Potongan</th>
                    <th>Mulai</th>
                    <th>Selesai</th>
                    <th>Batas Pengguna</th>
                    <th>Tanggal Tambah</th>
                    <th data-searchable="false" data-orderable="false">Kelola</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $nomor=1;
                  $sql = mysqli_query($db,"SELECT * FROM `voucher_photobooth` WHERE status_rmv_voucher='N'");
                  while($query = mysqli_fetch_array($sql)) {
                    $eid = enkripsiDekripsi($query['id_voucher'], 'enkripsi');
                    ?>
                    <tr>
                      <td> <?php echo $nomor++; ?> </td>
                      <td> <?php echo $query['nama_voucher']; ?> </td>
                      <td> <?php echo $query['kode_voucher']; ?> </td>
                      <td> <?php echo $query['deskripsi_voucher']; ?> </td>
                      <td> <?php echo jenis_voucher($query['status_potongan']); ?> </td>
                      <td align="right"> <?php echo number_format($query['potongan_voucher']); ?> </td>
                      <td data-order="<?php echo tanggal_order($query['start_tgl']) ?>"> <?php echo tanggal_jam_indo($query['start_tgl']); ?> </td>
                      <td data-order="<?php echo tanggal_order($query['end_tgl']) ?>"> <?php echo tanggal_jam_indo($query['end_tgl']); ?> </td>
                      <td align="right"> <?php echo number_format($query['max_pengguna']); ?> </td>
                      <td data-order="<?php echo tanggal_order($query['tgl_input']) ?>"> <?php echo tanggal_jam_indo($query['tgl_input']); ?> </td>
                      <td align="center"> 
                        <!-- <a href="javascript:void(0);" data-href="view/akun_kelola_detail.php?eid=<?php echo $eid; ?>" class="openPopup" data-toggle="tooltip" title="Detail">
                          <button class ="btn btn-success btn-xm" style="margin: 3px"><i class="fa fa-fw fa-file-alt"></i></button>
                        </a> -->
                        <a href="?page=voucherphotobooth&action=edit&eid=<?php echo $eid ?>" data-toggle="tooltip" title="Edit">
                          <button class ="btn btn-info btn-xm" style="margin: 3px"><i class="fa fa-fw fa-edit"></i></button>
                        </a>
                        <a onclick="return confirm('Yakin data ingin dihapus ?')" href="view/voucher_photobooth/proses_data.php?hapus_voucher=on&id=<?= $eid ?> " data-toggle="tooltip" title="Hapus">
                          <button class ="btn btn-danger btn-xm" style="margin: 3px"><i class="fa fa-fw fa-trash"></i></button>
                        </a>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div
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
</script>