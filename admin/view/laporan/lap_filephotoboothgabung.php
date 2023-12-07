<?php
if(empty($_POST['tgl_awal']) || empty($_POST['tgl_akhir'])){
  $tgl_awal = date('Y-m-d');
  $tgl_akhir = date('Y-m-d');
} else {
  $tgl_awal = date('Y-m-d', strtotime($_POST['tgl_awal']));
  $tgl_akhir = date('Y-m-d', strtotime($_POST['tgl_akhir']));
}
$array_data_shift=[];
$cek_shift = mysqli_query($db,"SELECT nama_shift, id_shift FROM shift WHERE status_aktif_shift = 'Y' ORDER BY nama_shift");
while($data_shift = mysqli_fetch_array($cek_shift)) {
  array_push($array_data_shift, (object)[
    'id' => $data_shift['id_shift'],
    'nama' => $data_shift['nama_shift'],
  ]);
}
$array_data_shift_length = count($array_data_shift);
$colspan_bawah = 3+($array_data_shift_length*2);

$total_diskon = $db->query("SELECT SUM(diskon) as jml FROM photoboothambil_transaksi WHERE status_transaksi!='2' AND DATE(tanggal_photoboothambil_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir'")->fetch_assoc()['jml'];

?>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Laporan File Gabung Photobooth</h1>
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
                  <th rowspan="2" style="vertical-align: middle;">No</th>
                  <th rowspan="2" style="vertical-align: middle;">Tiket</th>
                  <th rowspan="2" style="vertical-align: middle;">Harga</th>
                  <?php
                  foreach($array_data_shift as $value){
                    ?>
                    <th colspan="2">Shift <?= $value->nama?></th>
                    <?php
                  }
                  ?>
                  <th rowspan="2" style="vertical-align: middle;">Sub Qty</th>
                  <th rowspan="2" style="vertical-align: middle;">Omzet Bruto</th>
                </tr>
                <tr>
                  <?php
                  foreach($array_data_shift as $value){
                    ?>
                    <th>Qty</th>
                    <th>Jumlah</th>
                    <?php
                  }
                  ?>
                </tr>
              </thead>
              <tbody>
                <?php
                $nomor=1;
                $grand_total_qty = 0;
                $grand_total = 0;

                $sql = mysqli_query($db,"SELECT 
                  C.nama_photoboothambil_stan AS nama_jenis_tiket, A.kd_photoboothambil_stan,

                  (SELECT D.harga_photoboothambil_stan FROM photoboothambil_stan D WHERE D.id_photoboothambil_stan=A.kd_photoboothambil_stan) AS harga_satuan, 

                  (SELECT SUM(E.jumlah_tiket) FROM photoboothambil_tiket E JOIN photoboothambil_transaksi F ON E.kd_photoboothambil_transaksi=F.id_photoboothambil_transaksi WHERE  F.status_transaksi!='2' AND E.kd_photoboothambil_stan=A.kd_photoboothambil_stan AND DATE(F.tanggal_photoboothambil_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS jumlah_tiket,

                  (SELECT SUM(H.diskon) FROM photoboothambil_tiket G JOIN photoboothambil_transaksi H ON G.kd_photoboothambil_transaksi=H.id_photoboothambil_transaksi WHERE  H.status_transaksi!='2' AND G.kd_photoboothambil_stan=A.kd_photoboothambil_stan AND DATE(H.tanggal_photoboothambil_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS total_diskon,

                  (SELECT harga_photoboothambil_stan FROM photoboothambil_stan
                  WHERE id_photoboothambil_stan=A.kd_photoboothambil_stan) AS subtotal_transaksi

                  FROM photoboothambil_tiket A JOIN photoboothambil_transaksi B ON A.kd_photoboothambil_transaksi=B.id_photoboothambil_transaksi JOIN photoboothambil_stan C ON A.kd_photoboothambil_stan=C.id_photoboothambil_stan WHERE B.status_transaksi='1' AND DATE(B.tanggal_photoboothambil_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' GROUP BY A.kd_photoboothambil_stan");

                while($query = mysqli_fetch_array($sql)) {
                  $kd_photoboothambil_stan_temp = $query['kd_photoboothambil_stan'];
                  $grand_total = $grand_total + $query['jumlah_tiket']*$query['subtotal_transaksi'];
                  $grand_total_qty = $grand_total_qty + $query['jumlah_tiket'];
                  ?>
                  <tr>
                    <td> <?php echo $nomor++; ?> </td>
                    <td> <?php echo $query['nama_jenis_tiket']; ?> </td>
                    <td> <?php echo number_format($query['harga_satuan']); ?> </td>
                    <?php
                    foreach($array_data_shift as $value){
                      $ambil_pershift = mysqli_query($db,"SELECT 
                        (SELECT SUM(A.jumlah_tiket) FROM photoboothambil_tiket A 
                        JOIN photoboothambil_transaksi B ON A.kd_photoboothambil_transaksi=B.id_photoboothambil_transaksi 
                        WHERE  B.status_transaksi!='2' 
                        AND A.kd_photoboothambil_stan='$kd_photoboothambil_stan_temp'
                        AND B.kd_shift='$value->id'
                        AND DATE(B.tanggal_photoboothambil_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS jumlah_tiket,

                        (SELECT harga_photoboothambil_stan FROM photoboothambil_stan
                        WHERE id_photoboothambil_stan='$kd_photoboothambil_stan_temp') AS subtotal_transaksi");
                      $data_pershift = mysqli_fetch_array($ambil_pershift);
                      ?>
                      <td><?= number_format($data_pershift['jumlah_tiket'])?></td>
                      <td><?= number_format($data_pershift['jumlah_tiket']*$data_pershift['subtotal_transaksi'])?></td>
                      <?php
                    }
                    ?>
                    <td> <?php echo number_format($query['jumlah_tiket']); ?> </td>
                    <td> <?php echo number_format($query['jumlah_tiket']*$query['subtotal_transaksi']); ?> </td>
                  </tr>
                <?php } ?>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="3"></td>
                  <?php
                  foreach($array_data_shift as $value){
                    $ambil_pershift_total = mysqli_query($db,"SELECT 
                      (SELECT SUM(A.jumlah_tiket) FROM photoboothambil_tiket A 
                      JOIN photoboothambil_transaksi B ON A.kd_photoboothambil_transaksi=B.id_photoboothambil_transaksi 
                      WHERE  B.status_transaksi!='2' 
                      AND B.kd_shift='$value->id'
                      AND DATE(B.tanggal_photoboothambil_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS jumlah_tiket,
                      (SELECT SUM(nominal_sebelum_diskon) FROM photoboothambil_transaksi
                      WHERE  status_transaksi!='2' 
                      AND kd_shift='$value->id'
                      AND DATE(tanggal_photoboothambil_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS total_transaksi");
                    $data_pershift_total = mysqli_fetch_array($ambil_pershift_total);
                    ?>
                    <td><?= number_format($data_pershift_total['jumlah_tiket'])?></td>
                    <td><?= number_format($data_pershift_total['total_transaksi'])?></td>
                    <?php
                  }
                  ?>
                  <td> <?php echo number_format($grand_total_qty); ?> </td>
                  <td> <?php echo number_format($grand_total); ?> </td>
                </tr>
                <tr>
                  <td colspan="<?= $colspan_bawah?>"></td>
                  <td> Diskon </td>
                  <td> <?php echo number_format($total_diskon); ?> </td>
                </tr>
                <tr>
                  <td colspan="<?= $colspan_bawah?>"></td>
                  <td> Omzet Bersih </td>
                  <td> <?php echo number_format($grand_total - $total_diskon); ?> </td>
                </tr>
              </tfoot>
            </table>
          </div>
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
                FROM photoboothambil_transaksi A 
                LEFT JOIN jenis_pembayaran B ON A.kd_jenis_pembayaran=B.id_jenis_pembayaran 
                WHERE A.status_transaksi='1' 
                AND DATE(A.tanggal_photoboothambil_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' 
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

<script>
  $(function () {
    $('#example2').DataTable({
      "paging": false,
      "ordering": false,
      "info": false,
      "autoWidth": false,
      "filter": false,
      // "responsive": true,
    });
  });
</script>

<script>
  $(function () {
    $('#example1').DataTable({
      "paging": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
    });
  });
</script>

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
      var form = $('<form action="?page=lap-filephotoboothgabung" method="post">' +
        '<input type="hidden" name="tgl_awal" value="' + start.format('YYYY-MM-D') + '" />' +
        '<input type="hidden" name="tgl_akhir" value="' + end.format('YYYY-MM-D') + '" />' +
        '</form>');
      $('body').append(form);
      form.submit();
    }
    )
  })

  function exportexcel(awal, akhir){
    var form = $('<form target="_blank" action="view/laporan/lap_filephotoboothgabung_export_excel.php" method="post">' +
      '<input type="hidden" name="awal" value="' + awal + '" />' +
      '<input type="hidden" name="akhir" value="' + akhir + '" />' +
      '</form>');
    $('body').append(form);
    form.submit();
    // alert(awal+'    '+akhir);
  }
</script>