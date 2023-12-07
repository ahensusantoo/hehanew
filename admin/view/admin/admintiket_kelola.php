<?php
    $query = "SELECT * FROM admin WHERE status_rmv_admin='N' AND jabatan_admin = '2'";
    $data_admin = $db->query($query)->fetch_all(MYSQLI_ASSOC);
?>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Kelola Akun</h1>
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
            <h3 class="card-title">Daftar Akun</h3>
          </div>
          <div class="card-body">
            <table id="example" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama</th>
                  <th>Username</th>
                  <th>Jabatan</th>
                  <th data-searchable="false" data-orderable="false">Kelola</th>
                </tr>
              </thead>
              <tbody>
                <?php $nomor=1; ?>
                <?php foreach($data_admin as $key => $val) : ?>
                <?php $id_enkripsi = enkripsiDekripsi($val['id_admin'],'enkripsi'); ?>
                    <tr>
                        <td> <?= $nomor++; ?> </td>
                        <td> <?= $val['nama_admin']; ?> </td>
                        <td> <?= $val['username_admin']; ?> </td>
                        <td> <?= statusJabatanAdmin($val['jabatan_admin']) ?> </td>
                        <td align="center"> 
                          <a href="?page=admintiket&action=edit&id=<?php echo $id_enkripsi ?>" data-toggle="tooltip" title="Edit">
                            <button class ="btn btn-info btn-xm" style="margin: 3px"><i class="fa fa-fw fa-edit"></i></button>
                          </a>
                          <a onclick="return confirm('Yakin data ingin dihapus ?')" href="view/admin/proses_data.php?hapus_admin=<?= $val['nama_admin']; ?>&id=<?php echo $id_enkripsi ?>" data-toggle="tooltip" title="Hapus">
                            <button class ="btn btn-danger btn-xm" style="margin: 3px"><i class="fa fa-fw fa-trash"></i></button>
                          </a>
                        </td>
                    </tr>
                <?php $nomor++; endforeach; ?>
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
</script>