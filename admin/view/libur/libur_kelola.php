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
                  <th>Tahun</th>
                  <th>Bulan</th>
                  <th>Tanggal</th>
                  <th data-searchable="false" data-orderable="false">Kelola</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $nomor=1;
                $sql = mysqli_query($db,"SELECT * FROM hari_libur");
                while($query = mysqli_fetch_array($sql)) {
                  ?>
                  <tr>
                    <td> <?php echo $nomor++; ?> </td>
                    <td> <?php echo date('Y', strtotime($query['tahun_bulan'])); ?> </td>
                    <td> <?php echo date('m', strtotime($query['tahun_bulan'])).' / '.date('M', strtotime($query['tahun_bulan'])); ?> </td>
                    <td>
                      <?php
                      $hari_libur = explode("|", $query['hari_libur']);
                      $jumlah_array = count($hari_libur);

                      for ($x = 0; $x < $jumlah_array-1; $x++) {
                        echo date('l', strtotime($query['tahun_bulan'].'-'.sprintf('%02s',$hari_libur[$x]))).' / '.$hari_libur[$x].'<br>';
                      }
                      ?>
                    </td>
                    <td align="center">
                      <a href="?page=libur&action=edit&id=<?= $query['tahun_bulan'] ?>" data-toggle="tooltip" title="Edit">
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