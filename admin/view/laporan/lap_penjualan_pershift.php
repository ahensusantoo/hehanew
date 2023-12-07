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

function bulan_jml($bulan){
  switch($bulan){
    case 2:
      if($tahun%4==0){
        if($tahun%100==0){
          if($tahun%400==0){
            $maxtanggal = 29;
          }else{
            $maxtanggal = 28;
          }
        }else{
          $maxtanggal = 29;
        }
      }else{
        $maxtanggal = 28;
      }
      break;
    case 4:
      $maxtanggal = 30;
      break;
    case 6:
      $maxtanggal = 30;
      break;
    case 9:
      $maxtanggal = 30;
      break;
    case 11:
      $maxtanggal = 30;
      break;
    default:
      $maxtanggal = 31;
  }
  return $maxtanggal;
}

$tahun_awal = substr($tgl_awal,0,4);
$tahun_akhir = substr($tgl_akhir,0,4);

$bulan_awal = substr($tgl_awal,5,2);
$bulan_akhir = substr($tgl_akhir,5,2);

$tanggal_awal = substr($tgl_awal,8,2);
$tanggal_akhir = substr($tgl_akhir,8,2);


for ($i=$tahun_awal; $i <= $tahun_akhir; $i++) { 
  $tahun[$i] = $bulan_awal;
}


if ($tahun_awal !== $tahun_akhir) {

  echo "<script>alert('tahun tidak sama')</script>";
  foreach ($tahun as $thn => $value) {

    echo "<script>alert('perulanagan tahun')</script>";
    if ($bulan_awal !== $bulan_akhir) {


    
      for ($bulan=$bulan_awal; $bulan <= $bulan_akhir ; $bulan++) { 

          if ($bulan == $bulan_awal) {
            
            $bulan_jml = bulan_jml($bulan);
            for ($tgl=$tanggal_awal; $tgl <= $bulan_jml; $tgl++) { 
              $data[$thn][$bulan][$tgl] = "";
            }

          }elseif($bulan == $bulan_akhir){

            $bulan_jml = bulan_jml($bulan);
            for ($tgl=1; $tgl <= $bulan_jml; $tgl++) { 
              $data[$thn][$bulan][$tgl] = "";
            }

          }else{

            $bulan_jml = bulan_jml($bulan);
            for ($tgl=1; $tgl <= $bulan_akhir; $tgl++) { 
              $data[$thn][$bulan][$tgl] = "";
            }

          }

      }

      echo "<script>alert('masuk bulan 1')</script>";

    }else{

      echo "<script>alert('masuk bulan 2')</script>";

      for ($bulan=$bulan_akhir; $bulan <= $bulan_akhir; $bulan++) { 
        $bulan_jml = bulan_jml($bulan_akhir);
        for ($tgl=$tanggal_awal; $tgl <= $tanggal_akhir; $tgl++) { 
          $data[$tahun_awal][$bulan][$tgl] = "";
        }
      }

    }
  }


}else{

  echo "<script>alert('tahun sama')</script>";

  if ($bulan_awal !== $bulan_akhir) {

    echo "<script>alert('bulan tidak sama')</script>";
    
    for ($bulan=$bulan_awal; $bulan <= $bulan_akhir ; $bulan++) { 


        if ($bulan == $bulan_awal) {
          
          $bulan_jml = bulan_jml($bulan);
          for ($tgl=$tanggal_awal; $tgl <= $bulan_jml; $tgl++) { 
            $data[$tahun_awal][$bulan][$tgl] = "";
          }

        }elseif($bulan == $bulan_akhir){

          $bulan_jml = bulan_jml($bulan);
          for ($tgl=1; $tgl <= $bulan_jml; $tgl++) { 
            $data[$tahun_awal][$bulan][$tgl] = "";
          }

        }else{

          $bulan_jml = bulan_jml($bulan);
          for ($tgl=1; $tgl <= $bulan_akhir; $tgl++) { 
            $data[$tahun_awal][$bulan][$tgl] = "";
          }

        }

    }

  }

  else{

    echo "<script>alert('bulan tidak sama')</script>";

    for ($bulan=$bulan_akhir; $bulan <= $bulan_akhir; $bulan++) { 
      $bulan_jml = bulan_jml($bulan_akhir);
      for ($tgl=$tanggal_awal; $tgl <= $tanggal_akhir; $tgl++) { 
        $data[$tahun_awal][$bulan][$tgl] = "";
      }
    }

  }

}



?>

<link rel="stylesheet" href="plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Laporan Penjualan Harian Per Shift</h1>
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
                <button type="button" class="btn btn-default" id="daterange-btn" style="width: 100%">
                  <i class="far fa-calendar-alt"></i> <span id="reportrange"><?= date('j F Y', strtotime($tgl_awal)).' - '.date('j F Y', strtotime($tgl_akhir)); ?></span>
                  <i class="fas fa-caret-down"></i>
                </button>
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
            <pre><?php print_r($data) ?></pre>
            <table id="example" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Barang</th>
                  <th>Harga</th>
                  <th>Jumlah</th>
                  <th>Total</th>
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
                  <td colspan="3">Total</td>
                  <td> <?php echo number_format($grand_total); ?> </td>
                  <td> <?php echo number_format($grand_total); ?> </td>
                </tr>
                <tr>
                  <td colspan="3">Diskon</td>
                  <td> <?php echo number_format($grand_total); ?> </td>
                  <td> <?php echo number_format($grand_total); ?> </td>
                </tr>
                <tr>
                  <td colspan="3">Omset Bersih</td>
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

<script src="plugins/select2/js/select2.full.min.js"></script>
<script type="text/javascript">
  //Initialize Select2 Elements
  $('.select2bs4').select2({
    theme: 'bootstrap4'
  })
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
      $('#reportrange').html(start.format('D MMMM YYYY') + ' - ' + end.format('D MMMM YYYY'));
      // var form = $('<form action="?page=penjualan&action=pershift" method="post">' +
      //   '<input type="hidden" name="tgl_awal" value="' + start.format('YYYY-MM-D') + '" />' +
      //   '<input type="hidden" name="tgl_akhir" value="' + end.format('YYYY-MM-D') + '" />' +
      //   '</form>');
      // $('body').append(form);
      // form.submit();
    }
    )
  })

  function terapkan(){
    var tanggal = document.getElementById('reportrange').innerHTML.split(" - ");

    var start_master = new Date(tanggal[0]);
    var start = start_master.getFullYear() + "-" + String(start_master.getMonth() + 1) + "-" + String(start_master.getDate());

    var end_master = new Date(tanggal[1]);
    var end = end_master.getFullYear() + "-" + String(end_master.getMonth() + 1) + "-" + String(end_master.getDate());

    // alert(start + ' aaaa ' + end);

    var form = $('<form action="?page=penjualan&action=pershift" method="post">' +
      '<input type="hidden" name="tgl_awal" value="' + start + '" />' +
      '<input type="hidden" name="tgl_akhir" value="' + end + '" />' +
      '</form>');
    $('body').append(form);
    form.submit();
  }

  function exportexcel(awal, akhir){
    var form = $('<form target="_blank" action="view/laporan/lap_tiket_export_excel.php" method="post">' +
      '<input type="hidden" name="awal" value="' + awal + '" />' +
      '<input type="hidden" name="akhir" value="' + akhir + '" />' +
      '</form>');
    $('body').append(form);
    form.submit();
    // alert(awal+'    '+akhir);
  }
</script>