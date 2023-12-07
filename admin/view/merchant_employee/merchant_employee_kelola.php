<?php
    $sess_kd_merchant =$_SESSION['kd_merchant']; 

    $query = "SELECT * 
                FROM merchant_employee 
                WHERE status_remove_employee='N'
                    AND kd_merchant = '$sess_kd_merchant'
                ORDER BY id_merchant_employee DESC";
    $data_employee = $db->query($query)->fetch_all(MYSQLI_ASSOC);
?>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-12">
        <h1 class="m-0 text-dark">Kelola Akun Merchant Employee</h1>
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
            <h3 class="card-title">Daftar Merchant Employee</h3>
          </div>
          <div class="card-body">
            <table id="example" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Kode Merchant</th>
                  <th>Nama Employee</th>
                  <th>Username Employee</th>
                  <th>Level Employee</th>
                  <th>Nomor Employee</th>
                  <th>Email Employee</th>
                  <th>Status Employee</th>
                  <th data-searchable="false" data-orderable="false">Kelola</th>
                </tr>
              </thead>
              <tbody>
                <?php $nomor=1; ?>
                <?php foreach($data_employee as $key => $val) : ?>
                <?php $id_enkripsi = enkripsiDekripsi($val['id_merchant_employee'],'enkripsi'); ?>
                    <tr>
                        <td> <?= $nomor; ?> </td>
                        <td> <?= $val['kd_merchant']; ?> </td>
                        <td> <?= $val['nama_employee']; ?> </td>
                        <td> <?= $val['username_employee']; ?> </td>
                        <td> 
                            <?php 
                                if($val['level_employee'] == "0"){
                                    echo "Super Admin Merchant";
                                }else if($val['level_employee'] == "0"){
                                    echo "Admin Merchant";
                                }else{
                                    echo "Kasir";
                                }
                            ?> 
                        </td>
                        <td> <?= $val['telp_employee']; ?> </td>
                        <td> <?= $val['email_employee']; ?> </td>
                        <td> 
                            <?php 
                                if($val['status_aktif_employee'] == "Y"){
                                    echo "AKTIF";
                                }else{
                                    echo "NON AKTIF";
                                }
                            ?> 
                        </td>
                        <td align="center"> 
                          <a href="?page=merchant_employee&action=edit&id=<?php echo $id_enkripsi ?>" data-toggle="tooltip" title="Edit">
                            <button class ="btn btn-info btn-xm" style="margin: 3px"><i class="fa fa-fw fa-edit"></i></button>
                          </a>
                          <a onclick="return confirm('Yakin data ingin dihapus ?')" href="view/merchant_employee/proses_data.php?merchant_employee_hapus=<?= $val['nama_employee']; ?>&id=<?php echo $id_enkripsi ?>" data-toggle="tooltip" title="Hapus">
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