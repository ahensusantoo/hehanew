<?php
if(empty($_POST['tgl_awal']) || empty($_POST['tgl_akhir'])){
  $tgl_awal = date('Y-m-d');
  $tgl_akhir = date('Y-m-d');
} else {
  $tgl_awal = date('Y-m-d', strtotime($_POST['tgl_awal']));
  $tgl_akhir = date('Y-m-d', strtotime($_POST['tgl_akhir']));
}

if(!empty($_POST['filer1'])){
  $filer1 = $_POST['filer1'];
} else {
  $filer1 = '';
}
?>
<style type="text/css">
  .selectMonths{ position:relative; display:inline-block; }
  .selectMonthsselect {height: 30px; }
  .selectMonths > i{ position:absolute; right:5px; top:5px; opacity:0.35; font-style:normal; font-size:18px; transition:0.2s; pointer-events:none; }
  .selectMonths > input{ text-transform:capitalize; padding-left:10px; cursor:default; cursor:pointer; }
  .selectMonths:hover > i{ opacity:.7; }
  .selectMonths + .selectMonths{ float:none; }
</style>
<link rel="stylesheet" href="plugins/month-range2/picker.css">

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-12">
        <h1 class="m-0 text-dark">Laporan Bagi Hasil Penjualan Bulanan Semua Stall</h1>
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
            <div class="form-group row">
              <label class="col-sm-1 col-form-label">Periode :</label>
              <div class="col-sm-11">
                <div class="selectMonths" style="width: 100% !important">
                  <input type="text" class="btn btn-default float-left" value="" readonly style="width: 100% !important" />
                  <i>&#128197;</i>
                </div>
              </div>
            </div>
            <a onclick="terapkan()">
              <button type="button" class="btn btn-primary" style="width: 100%">Terapkan</button>
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <a onclick="exportexcel('<?= $tgl_awal ?>', '<?= $tgl_akhir ?>')">
              <button type="button" class="btn btn-default" style="width: 100%"><i class="fa fa-fw fa-file-download"></i>Eksport Excel</button>
            </a>
          </div>
          <div class="card-body">
            <table id="example" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th rowspan="2">Tanggal</th>
                  <th rowspan="2">Nama Stall</th>
                  <th colspan="3">Omset Bersih</th>
                  <th colspan="2">Bagi Hasil</th>
                </tr>
                <tr>
                  <th>Shift Pagi</th>
                  <th>Shift Siang</th>
                  <th>Total</th>
                  <th>HEHA (22%)</th>
                  <th>OKY (78%)</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $nomor=1;
                $grand_total = 0;
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
                  $grand_total = $grand_total + $query['total_transaksi'];
                  ?>
                  <tr>
                    <td> <?php echo $nomor++; ?> </td>
                    <td> <?php echo $query['nama_jenis_tiket']; ?> </td>
                    <td> <?php echo number_format($query['harga_satuan']); ?> </td>
                    <td> <?php echo number_format($query['jumlah_tiket']); ?> </td>
                    <td> <?php echo number_format($query['total_harga_satuan']); ?> </td>
                  </tr>
                <?php } ?>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="2">Total</td>
                  <td> <?php echo number_format($grand_total); ?> </td>
                  <td> <?php echo number_format($grand_total); ?> </td>
                  <td> <?php echo number_format($grand_total); ?> </td>
                  <td> <?php echo number_format($grand_total); ?> </td>
                  <td> <?php echo number_format($grand_total); ?> </td>
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
  function exportexcel(awal, akhir){
    var form = $('<form target="_blank" action="view/laporan/lap_penjualan_bagihasilbulanan_excel.php" method="post">' +
      '<input type="hidden" name="tgl_awal" value="' + awal + '" />' +
      '<input type="hidden" name="tgl_akhir" value="' + akhir + '" />' +
      '</form>');
    $('body').append(form);
    form.submit();
    // alert(awal+'    '+akhir);
  }
</script>

<script src="plugins/month-range2/tether.min.js"></script>
<script src="plugins/month-range2/datePicker.js"></script>
<script>
  window.global_date_start = <?= date('Y', strtotime($tgl_awal))?>+'-'+<?=date('m', strtotime($tgl_awal))?>+'-'+<?=date('d', strtotime($tgl_awal)) ?>;
  window.global_date_end = <?= date('Y', strtotime($tgl_akhir))?>+'-'+<?=date('m', strtotime($tgl_akhir))?>+'-'+<?=date('d', strtotime($tgl_akhir)) ?>;

  $('.selectMonths:first input')
  .rangePicker({ RTL:false, setDate:[[<?= date('m', strtotime($tgl_awal)) ?>,<?= date('Y', strtotime($tgl_awal)) ?>],[<?= date('m', strtotime($tgl_akhir)) ?>,<?= date('Y', strtotime($tgl_akhir)) ?>]], closeOnSelect:true })
  .on('datePicker.done', function(e, result){
    // if( result instanceof Array )
    //   console.log(new Date(result[0][1], result[0][0] - 1), new Date(result[1][1], result[1][0] - 1));
    // else
    //   console.log(result);

    var start_master = new Date(result[0][1], result[0][0] - 1);
    var start = start_master.getFullYear() + "-" + String(start_master.getMonth() + 1).padStart(1, '0') + "-1";
    window.global_date_start = start;

    var end_master = new Date(result[1][1], result[1][0] - 1);
    var end = end_master.getFullYear() + "-" + String(end_master.getMonth() + 1).padStart(1, '0') + "-1";
    window.global_date_end = end;

    // alert(start + end);
    // var form = $('<form action="?page=penjualan&action=bagihasilbulanan" method="post">' +
    //   '<input type="hidden" name="tgl_awal" value="' + start + '" />' +
    //   '<input type="hidden" name="tgl_akhir" value="' + end + '" />' +
    //   '</form>');
    // $('body').append(form);
    // form.submit();
  });

  function terapkan(){
    var form = $('<form action="?page=penjualan&action=bagihasilbulanan" method="post">' +
      '<input type="hidden" name="tgl_awal" value="' + window.global_date_start + '" />' +
      '<input type="hidden" name="tgl_akhir" value="' + window.global_date_end + '" />' +
      '</form>');
    $('body').append(form);
    form.submit();
  }

</script>
