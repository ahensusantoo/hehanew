<?php
if(empty($_POST['tgl_awal']) || empty($_POST['tgl_akhir'])){
  $tgl_awal = date('Y-m-d');
  $tgl_akhir = date('Y-m-d');
} else {
  $tgl_awal = date('Y-m-d', strtotime($_POST['tgl_awal']));
  $tgl_akhir = date('Y-m-d', strtotime($_POST['tgl_akhir']));
}
?>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Laporan Shift Penjualan Photobooth</h1>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <a onclick="exportexcel('<?= $tgl_awal ?>', '<?= $tgl_akhir ?>')">
              <button type="button" class="btn btn-default float-right" style="margin-left: 7px"><i class="fa fa-fw fa-file-download"></i>Eksport Excel</button>
            </a>
            <button type="button" class="btn btn-default float-right" id="daterange-btn">
              <i class="far fa-calendar-alt"></i> <span id="reportrange"><?= tanggal_indo($tgl_awal).' - '.tanggal_indo($tgl_akhir); ?></span>
              <i class="fas fa-caret-down"></i>
            </button>
          </div>
          <div class="card-body">
            <table id="example" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Shift</th>
                  <th>Tiket</th>
                  <th>Omzet Bruto</th>
                  <th>Diskon</th>
                  <th>Omzet Bersih</th>
                  <!-- <th data-searchable="false" data-orderable="false">Kelola</th> -->
                </tr>
              </thead>
              <tbody>
                <?php
                $nomor=1;
                $grand_total = 0;
                  

                $sql = mysqli_query($db,"SELECT B.nama_shift, 

                (SELECT SUM(C.jumlah_tiket) FROM photobooth_transaksi C WHERE C.status_transaksi='1' AND DATE(C.tanggal_photobooth_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND C.kd_shift=A.kd_shift) AS jumlah_tiket ,
                
                (SELECT SUM(C.nominal_sebelum_diskon) FROM photobooth_transaksi C WHERE C.status_transaksi='1' AND DATE(C.tanggal_photobooth_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND C.kd_shift=A.kd_shift) AS total_harga,
                
                (SELECT SUM(C.diskon) FROM photobooth_transaksi C WHERE C.status_transaksi='1' AND DATE(C.tanggal_photobooth_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND C.kd_shift=A.kd_shift) AS diskon,
                
                (SELECT SUM(C.total_transaksi) FROM photobooth_transaksi C WHERE DATE(C.tanggal_photobooth_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND C.status_transaksi='1' AND C.kd_shift=A.kd_shift) AS total_transaksi
                
                FROM photobooth_transaksi A JOIN shift B ON A.kd_shift=B.id_shift WHERE A.status_transaksi='1' AND DATE(A.tanggal_photobooth_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' GROUP BY A.kd_shift");

                while($query = mysqli_fetch_array($sql)) {
                  $grand_total = $grand_total + $query['total_transaksi'];
                  ?>
                  <tr>
                    <td> <?php echo $nomor++; ?> </td>
                    <td> <?php echo $query['nama_shift']; ?> </td>
                    <td> <?php echo number_format($query['jumlah_tiket']); ?> </td>
                    <td> <?php echo number_format($query['total_harga']); ?> </td>
                    <td> <?php echo number_format($query['diskon']); ?> </td>
                    <td> <?php echo number_format($query['total_transaksi']); ?> </td>
                    <!-- <td align="center"> 
                      <a href="javascript:void(0);" data-href="view/akun_kelola_detail.php?eid=<?php echo $eid; ?>" class="openPopup" data-toggle="tooltip" title="Detail">
                        <button class ="btn btn-success btn-xm" style="margin: 3px"><i class="fa fa-fw fa-file-alt"></i></button>
                      </a>
                    </td> -->
                  </tr>
                <?php } ?>
              </tbody>
              <tr>
                <td colspan="5"></td>
                <td> <?php echo number_format($grand_total); ?> </td>
              </tr>
            </table>
          </div>
          <div class="card-body">
          <h5>Rincian Pembayaran</h5>
          <table id="example2" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Jenis</th>
                <th>Nominal</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $nomor=1;
              $grand_total_jenpem = 0;
              $sql = mysqli_query($db,"SELECT SUM(A.total_transaksi) AS total_transaksi, B.nama_jenis_pembayaran
                FROM photobooth_transaksi A 
                LEFT JOIN jenis_pembayaran B ON A.kd_jenis_pembayaran=B.id_jenis_pembayaran 
                WHERE A.status_transaksi='1' 
                AND DATE(A.tanggal_photobooth_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' 
                GROUP BY A.kd_jenis_pembayaran");
              while($query = mysqli_fetch_array($sql)) {
                $grand_total_jenpem = $grand_total_jenpem + $query['total_transaksi'];
                ?>
                <tr>
                  <td> <?php echo $query['nama_jenis_pembayaran']; ?> </td>
                  <td> <?php echo number_format($query['total_transaksi']); ?> </td>
                </tr>
              <?php } ?>
            </tbody>
            <tfoot>
              <tr>
                <td>Total</td>
                <td> <?php echo number_format($grand_total_jenpem); ?> </td>
              </tr>
            </tfoot>
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

<script type="text/javascript">
  $(function () {
    $('#daterange-btn').daterangepicker(
    {
      ranges   : {
        'Hari Ini' : [moment(), moment()],
        'Bulan Ini' : [moment().startOf('month'), moment().endOf('month')],
        'Tahun Ini' : [moment().startOf('year'), moment().endOf('year')]
      },
      startDate : '<?= date("m/d/Y", strtotime($tgl_awal)) ?>',
      endDate : '<?= date("m/d/Y", strtotime($tgl_akhir)) ?>'
    },
    function (start, end) {
      // $('#reportrange').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
      var form = $('<form action="?page=lap-photoboothshift" method="post">' +
        '<input type="hidden" name="tgl_awal" value="' + start.format('YYYY-MM-D') + '" />' +
        '<input type="hidden" name="tgl_akhir" value="' + end.format('YYYY-MM-D') + '" />' +
        '</form>');
      $('body').append(form);
      form.submit();
    }
    )
  })

  function exportexcel(awal, akhir){
    var form = $('<form target="_blank" action="view/laporan/lap_photoboothshift_export_excel.php" method="post">' +
      '<input type="hidden" name="awal" value="' + awal + '" />' +
      '<input type="hidden" name="akhir" value="' + akhir + '" />' +
      '</form>');
    $('body').append(form);
    form.submit();
    // alert(awal+'    '+akhir);
  }
</script>