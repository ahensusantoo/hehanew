<?php
    $query = "SELECT * FROM supplier WHERE status_rmv_supplier='N'";
    $data_supplier = $db->query($query)->fetch_all(MYSQLI_ASSOC);
?>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <!-- <h1 class="m-0 text-dark">Kelola Akun</h1> -->
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
            <a href="?page=supplier&action=tambah" class="btn btn-sm btn-success float-right">Tambah Supplier</a>
            <h5 class="">Daftar Super Admin & General Cashier Ticketing</h5>
          </div>
          <div class="card-body">
            
            <table id="example" class="table table-bordered table-striped table-sm tet-sm">
                <thead>
                    <tr>
                        <th width="3%">No</th>
                        <th>Kode Supplier</th>
                        <th>Nama Supplier</th>
                        <th>Alamat</th>
                        <th>Telepon</th>
                        <th>Status</th>
                        <th data-searchable="false" data-orderable="false">Kelola</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; foreach($data_supplier as $key => $value) { ?>
                        <?php $eid = enkripsiDekripsi($value['id_supplier'],'enkripsi'); ?>
                        <tr>
                            <td><?=$no++?></td>
                            <td><?=$value['kode_supplier']?></td>
                            <td><?=$value['nama_supplier']?></td>
                            <td><?=$value['alamat_supplier']?></td>
                            <td><?=$value['telp_supplier']?></td>
                            <td><?php 
                                    if($value['status_aktif_supplier'] == "Y"){
                                        echo "Aktif";
                                    }else{
                                        echo "Non Aktif";
                                    }
                                ?>
                            </td>
                            <td align="center"> 
                                <a href="?page=supplier&action=edit&eid=<?= $eid ?>" data-toggle="tooltip" title="Edit">
                                    <button class ="btn btn-info btn-xm" style="margin: 3px"><i class="fa fa-fw fa-edit"></i></button>
                                </a>
                                <a onclick="return confirm('Yakin data ingin dihapus ?')" href="view/admin/proses_data.php?hapus_supplier=on&eid=<?= $eid ?>" data-toggle="tooltip" title="Hapus">
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
</script>

<script>
    $(function () {
      $('#example2').DataTable({
        "paging": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        // "responsive": true,
      });
    });
  </script>
