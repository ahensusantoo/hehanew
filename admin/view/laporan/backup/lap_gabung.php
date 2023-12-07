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
$colspan_bawah = 5+($array_data_shift_length*2);
?>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Laporan Gabung</h1>
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
            <table id="example1" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th rowspan="2" style="vertical-align: middle;">No</th>
                  <th rowspan="2" style="vertical-align: middle;">Tiket</th>
                  <th rowspan="2" style="vertical-align: middle;">Harga</th>
                  <?php
                  foreach($array_data_shift as $value){
                    ?>
                    <th colspan="3">Shift <?= $value->nama?></th>
                    <?php
                  }
                  ?>
                  <th rowspan="2" style="vertical-align: middle;">Sub Qty</th>
                  <th rowspan="2" style="vertical-align: middle;">Sub Total</th>
                </tr>
                <tr>
                  <?php
                  foreach($array_data_shift as $value){
                    ?>
                    <th>Qty</th>
                    <th>Diskon</th>
                    <th>Jumlah</th>
                    <?php
                  }
                  ?>
                  <!-- <th data-searchable="false" data-orderable="false">Kelola</th> -->
                </tr>
              </thead>
              <tbody>
                <?php
                $nomor=1;
                $grand_total_qty = 0;
                $grand_total = 0;
                // $sql = mysqli_query($db,"SELECT 
                //   SUM(tr.jumlah_tiket) AS jumlah_tiket, 
                //   SUM(tr.total_transaksi) AS total_harga_satuan, 
                //   SUM(tr.total_transaksi)/SUM(tr.jumlah_tiket) AS harga_satuan,
                //   jt.nama_jenis_tiket, 
                //   DATE(tr.tanggal_transaksi) AS tgl_temp, 
                //   s.nama_shift 
                //   FROM `transaksi` AS tr 
                //   JOIN tiket AS t ON t.kd_transaksi = tr.id_transaksi 
                //   JOIN jenis_tiket AS jt ON t.kd_jenis_tiket = jt.id_jenis_tiket 
                //   JOIN shift AS s ON tr.kd_shift = s.id_shift
                //   WHERE (DATE(tr.tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir')
                //   AND tr.status_transaksi != '3' 
                //   GROUP BY t.kd_jenis_tiket, DATE(tr.tanggal_transaksi), tr.kd_shift 
                //   ORDER BY DATE(tr.tanggal_transaksi), jt.nama_jenis_tiket");

                // $sql = mysqli_query($db,"SELECT 
                //   SUM(tr.jumlah_tiket) AS jumlah_tiket, 
                //   SUM(tr.total_transaksi) AS total_harga_satuan, 
                //   SUM(tr.total_transaksi)/SUM(tr.jumlah_tiket) AS harga_satuan,
                //   jt.nama_jenis_tiket,
                //   t.kd_jenis_tiket
                //   FROM `transaksi` AS tr 
                //   JOIN tiket AS t ON t.kd_transaksi = tr.id_transaksi 
                //   JOIN jenis_tiket AS jt ON t.kd_jenis_tiket = jt.id_jenis_tiket 
                //   WHERE (DATE(tr.tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir')
                //   AND tr.status_transaksi != '3' 
                //   GROUP BY t.kd_jenis_tiket
                //   ORDER BY jt.nama_jenis_tiket");

                // $sql = mysqli_query($db,"SELECT B.kd_jenis_tiket, C.nama_jenis_tiket, B.harga_satuan, 
                //   (SELECT SUM(DISTINCT D.jumlah_tiket) FROM transaksi D JOIN tiket E ON D.id_transaksi=E.kd_transaksi WHERE D.status_transaksi!='3' AND E.kd_jenis_tiket=B.kd_jenis_tiket AND DATE(D.tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS jumlah_tiket, 
                //   (SELECT SUM(DISTINCT F.total_transaksi) FROM transaksi F JOIN tiket G ON F.id_transaksi=G.kd_transaksi WHERE F.status_transaksi!='3' AND G.kd_jenis_tiket=B.kd_jenis_tiket AND DATE(F.tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS total_harga_satuan 
                //   FROM transaksi A 
                //   JOIN tiket B ON A.id_transaksi=B.kd_transaksi 
                //   JOIN jenis_tiket C ON B.kd_jenis_tiket=C.id_jenis_tiket 
                //   WHERE A.status_transaksi!='3' 
                //   AND DATE(A.tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' 
                //   GROUP BY B.kd_jenis_tiket");

                $sql = mysqli_query($db,"SELECT B.kd_jenis_tiket, C.nama_jenis_tiket, B.harga_satuan, 
                  (SELECT SUM(jumlah_tiket) FROM view_transaksi_join_tiket_orderby_id WHERE status_transaksi!='3' AND kd_jenis_tiket=B.kd_jenis_tiket AND DATE(tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS jumlah_tiket, 
                  (SELECT SUM(nominal_sebelum_diskon) FROM view_transaksi_join_tiket_orderby_id WHERE status_transaksi!='3' AND kd_jenis_tiket=B.kd_jenis_tiket AND DATE(tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS total_harga_satuan,
                  (SELECT SUM(diskon) FROM view_transaksi_join_tiket_orderby_id WHERE status_transaksi!='3' AND kd_jenis_tiket=B.kd_jenis_tiket AND DATE(tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS total_diskon,
                  (SELECT SUM(total_transaksi) FROM view_transaksi_join_tiket_orderby_id WHERE status_transaksi!='3' AND kd_jenis_tiket=B.kd_jenis_tiket AND DATE(tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS total_transaksi 
                  FROM transaksi A 
                  JOIN tiket B ON A.id_transaksi=B.kd_transaksi 
                  JOIN jenis_tiket C ON B.kd_jenis_tiket=C.id_jenis_tiket 
                  WHERE A.status_transaksi!='3' 
                  AND DATE(A.tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' 
                  GROUP BY B.kd_jenis_tiket");

                while($query = mysqli_fetch_array($sql)) {
                  $kd_jenis_tiket_temp = $query['kd_jenis_tiket'];
                  $grand_total = $grand_total + $query['total_transaksi'];
                  $grand_total_qty = $grand_total_qty + $query['jumlah_tiket'];
                  ?>
                  <tr>
                    <td> <?php echo $nomor++; ?> </td>
                    <td> <?php echo $query['nama_jenis_tiket']; ?> </td>
                    <td> <?php echo number_format($query['harga_satuan']); ?> </td>
                    <?php
                    foreach($array_data_shift as $value){
                      $ambil_pershift = mysqli_query($db,"SELECT SUM(DISTINCT a.jumlah_tiket) AS dummy,
                        (SELECT SUM(jumlah_tiket) FROM view_transaksi_join_tiket_orderby_id WHERE status_transaksi!='3' AND kd_jenis_tiket='$kd_jenis_tiket_temp' AND DATE(tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND kd_shift = '$value->id') AS jumlah_tiket, 
                        (SELECT SUM(nominal_sebelum_diskon) FROM view_transaksi_join_tiket_orderby_id WHERE status_transaksi!='3' AND kd_jenis_tiket='$kd_jenis_tiket_temp' AND DATE(tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND kd_shift = '$value->id') AS sub_total,
                        (SELECT SUM(diskon) FROM view_transaksi_join_tiket_orderby_id WHERE status_transaksi!='3' AND kd_jenis_tiket='$kd_jenis_tiket_temp' AND DATE(tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND kd_shift = '$value->id') AS total_diskon,
                        (SELECT SUM(total_transaksi) FROM view_transaksi_join_tiket_orderby_id WHERE status_transaksi!='3' AND kd_jenis_tiket='$kd_jenis_tiket_temp' AND DATE(tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND kd_shift = '$value->id') AS total_transaksi 
                        FROM transaksi A 
                        JOIN tiket B ON A.id_transaksi=B.kd_transaksi 
                        WHERE A.status_transaksi!='3' 
                        AND DATE(A.tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir'
                        AND b.kd_jenis_tiket = '$kd_jenis_tiket_temp' 
                        AND a.kd_shift = '$value->id'");
                      $data_pershift = mysqli_fetch_array($ambil_pershift);
                      ?>
                      <td><?= number_format($data_pershift['jumlah_tiket'])?></td>
                      <td><?= number_format($data_pershift['total_diskon'])?></td>
                      <td><?= number_format($data_pershift['total_transaksi'])?></td>
                      <?php
                    }
                    ?>
                    <td> <?php echo number_format($query['jumlah_tiket']); ?> </td>
                    <td> <?php echo number_format($query['total_transaksi']); ?> </td>
                    <!-- <td align="center"> 
                      <a href="javascript:void(0);" data-href="view/akun_kelola_detail.php?eid=<?php echo $eid; ?>" class="openPopup" data-toggle="tooltip" title="Detail">
                        <button class ="btn btn-success btn-xm" style="margin: 3px"><i class="fa fa-fw fa-file-alt"></i></button>
                      </a>
                    </td> -->
                  </tr>
                <?php } ?>
              </tbody>
              <tfoot>
                <td colspan="<?= $colspan_bawah ?>"></td>
                <td> <?php echo number_format($grand_total_qty); ?> </td>
                <td> <?php echo number_format($grand_total); ?> </td>
              </tfoot>
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
                  FROM transaksi A 
                  LEFT JOIN jenis_pembayaran B ON A.kd_jenis_pembayaran=B.id_jenis_pembayaran 
                  WHERE A.status_transaksi!='3' 
                  AND DATE(A.tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' 
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
      var form = $('<form action="?page=lap-gabung" method="post">' +
        '<input type="hidden" name="tgl_awal" value="' + start.format('YYYY-MM-D') + '" />' +
        '<input type="hidden" name="tgl_akhir" value="' + end.format('YYYY-MM-D') + '" />' +
        '</form>');
      $('body').append(form);
      form.submit();
    }
    )
  })

  function exportexcel(awal, akhir){
    var form = $('<form target="_blank" action="view/laporan/lap_gabung_export_excel.php" method="post">' +
      '<input type="hidden" name="awal" value="' + awal + '" />' +
      '<input type="hidden" name="akhir" value="' + akhir + '" />' +
      '</form>');
    $('body').append(form);
    form.submit();
    // alert(awal+'    '+akhir);
  }
</script>