<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Kelola Shift</h1>
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
            <h3 class="card-title">Daftar Shift</h3>
          </div>
          <div class="card-body">
            <table id="example" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama</th>
                  <th>Status Aktif</th>
                  <!-- <th>Tanggal Input</th> -->
                  <th data-searchable="false" data-orderable="false">Kelola</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $nomor=1;
                $sql = mysqli_query($db,"SELECT * FROM `shift`");
                while($query = mysqli_fetch_array($sql)) {
                  $eid = enkripsiDekripsi($query['id_shift'], 'enkripsi');
                  ?>
                  <tr>
                    <td> <?php echo $nomor++; ?> </td>
                    <td> <?php echo $query['nama_shift']; ?> </td>
                    <td>
                      <?php if ($query['status_aktif_shift'] == "Y"): ?>
                        Aktif
                      <?php else: ?>
                        Nonaktif
                      <?php endif ?>
                    </td>
                    <!-- <td data-order="<?php echo tanggal_order($query['tanggal_input_shift']) ?>"> <?php echo tanggal_jam_indo($query['tanggal_input_shift']); ?> </td> -->
                    <td align="center">
                      <a href="?page=shift&action=edit&eid=<?php echo $eid ?>" data-toggle="tooltip" title="Edit">
                        <button class ="btn btn-info btn-xm" style="margin: 3px"><i class="fa fa-fw fa-edit"></i></button>
                      </a>
                      <!-- <a onclick="return confirm('Yakin data ingin dihapus ?')" href="?page=shift&action=hapus&eid=<?php echo $eid ?>" data-toggle="tooltip" title="Hapus">
                        <button class ="btn btn-danger btn-xm" style="margin: 3px"><i class="fa fa-fw fa-trash"></i></button>
                      </a> -->
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