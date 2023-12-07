<?php
    $query = "SELECT * FROM admin WHERE status_rmv_admin='N' AND jabatan_admin IN ('1','6')";
    $data_admin = $db->query($query)->fetch_all(MYSQLI_ASSOC);

    $query = "SELECT * FROM merchant_employee WHERE status_remove_employee='N' AND level_employee='0' ";
    $admin_stall = $db->query($query)->fetch_all(MYSQLI_ASSOC);
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
            <a href="?page=generalcashier_stall&action=tambah" class="btn btn-sm btn-success float-right">Tambah Akun</a>
            <h5 class="">Daftar Super Admin & General Cashier Ticketing</h5>
          </div>
          <div class="card-body">
            
            <table id="example" class="table table-bordered table-striped table-sm tet-sm">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama</th>
                  <th>Username</th>
                  <th>Nomor</th>
                  <th>Email</th>
                  <th>Status</th>
                  <th data-searchable="false" data-orderable="false">Kelola</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $nomor=1;
                $grand_total = 0;
                $sql = mysqli_query($db,"SELECT * 
                  FROM merchant_employee 
                  WHERE status_remove_employee='N'
                  AND level_employee='0'
                  ORDER BY id_merchant_employee DESC");
                while($query = mysqli_fetch_array($sql)) {
                  $eid = enkripsiDekripsi($query['id_merchant_employee'],'enkripsi');
                  ?>
                  <tr>
                    <td> <?= $nomor++; ?> </td>
                    <td> <?= $query['nama_employee']; ?> </td>
                    <td> <?= $query['username_employee']; ?> </td>
                    <td> <?= $query['telp_employee']; ?> </td>
                    <td> <?= $query['email_employee']; ?> </td>
                    <td> 
                      <?php 
                      if($query['status_aktif_employee'] == "Y"){
                        echo "AKTIF";
                      }else{
                        echo "NON AKTIF";
                      }
                      ?> 
                    </td>
                    <td align="center"> 
                      <a href="?page=generalcashier_stall&action=edit&eid=<?= $eid ?>" data-toggle="tooltip" title="Edit">
                        <button class ="btn btn-info btn-xm" style="margin: 3px"><i class="fa fa-fw fa-edit"></i></button>
                      </a>
                      <a onclick="return confirm('Yakin data ingin dihapus ?')" href="view/admin/proses_data.php?hapus_merchant_empoyee_gc=on&eid=<?= $eid ?>" data-toggle="tooltip" title="Hapus">
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




























            