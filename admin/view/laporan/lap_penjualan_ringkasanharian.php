<?php
if(empty($_POST['tgl_awal']) || empty($_POST['tgl_akhir'])){
  $tgl_awal = date('Y-m-d');
  $tgl_akhir = date('Y-m-d');
} else {
  $tgl_awal = date('Y-m-d', strtotime($_POST['tgl_awal']));
  $tgl_akhir = date('Y-m-d', strtotime($_POST['tgl_akhir']));
}


// $query = "
//     SELECT A.tgl_input_detail, 

//     FROM merchant_transaksi_detail A WHERE DATE(A.tgl_input_detail) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND A.status_transaksi_detail='2' GROUP BY DATE(A.tgl_input_detail)

// ";
// $data = $db->query($query)->fetch_all(MYSQLI_ASSOC);

// foreach ($data as $key => $value) {
  
//   $data[$key]['jml_merchant'] = $db->query("SELECT COUNT(DISTINCT B.kd_merchant)  AS jml_merchant FROM merchant_transaksi_detail B WHERE DATE(B.tgl_input_detail)=DATE(A.tgl_input_detail) AND B.status_transaksi_detail='2'")->fetch_assoc()['jml_merchant'];

//   $data[$key]['jml_terjual'] = $db->query("SELECT SUM(C.jumlah_produk) AS jml_terjual FROM merchant_transaksi_detail C WHERE DATE(C.tgl_input_detail)=DATE(A.tgl_input_detail) AND C.status_transaksi_detail='2'")->fetch_assoc()['jml_terjual'];

//   $data[$key]['omset_bruto'] = $db->query("SELECT SUM(D.harga_produk) AS omset_bruto FROM merchant_transaksi_detail D WHERE DATE(D.tgl_input_detail)=DATE(A.tgl_input_detail) AND D.status_transaksi_detail='2'")->fetch_assoc()['omset_bruto'];

//   $data[$key]['potongan'] = $db->query("

//     SELECT (SELECT SUM(X.harga_produk) FROM merchant_transaksi_detail X WHERE DATE(X.tgl_input_detail)=DATE(A.tgl_input_detail) AND X.status_transaksi_detail='2') - (SELECT SUM(Y.harga_produk) FROM merchant_transaksi_detail Y WHERE DATE(Y.tgl_input_detail)=DATE(A.tgl_input_detail) AND Y.status_transaksi_detail='2') AS potongan

//   ")->fetch_assoc()['potongan'];

//   $data[$key]['omset_bersih'] = $db->query("SELECT SUM(E.harga_produk) AS omset_bersih FROM merchant_transaksi_detail E WHERE DATE(E.tgl_input_detail)=DATE(A.tgl_input_detail) AND E.status_transaksi_detail='2'")->fetch_assoc()['omset_bersih'];

// }



$start = $month = strtotime($tgl_awal);
$end = strtotime($tgl_akhir);
$key = 0;
while($month <= $end){

    $tgl_loop =  date('Y-m-d', $month);

    $data[$key]['tgl_input_detail'] = $tgl_loop;

    $data[$key]['jml_merchant'] = $db->query("SELECT COUNT(DISTINCT B.kd_merchant)  AS jml_merchant FROM merchant_transaksi_detail B WHERE DATE(B.tgl_input_detail)='$tgl_loop' AND B.status_transaksi_detail='2'")->fetch_assoc()['jml_merchant'];

    $data[$key]['jml_terjual'] = $db->query("SELECT SUM(C.jumlah_produk) AS jml_terjual FROM merchant_transaksi_detail C WHERE DATE(C.tgl_input_detail)='$tgl_loop' AND C.status_transaksi_detail='2'")->fetch_assoc()['jml_terjual'];

    $data[$key]['omset_bruto'] = $db->query("SELECT COALESCE(SUM(A.harga_produk*A.jumlah_produk), 0) AS omset_bruto FROM merchant_transaksi_detail A JOIN merchant_transaksi B ON A.kd_merchant_transaksi=B.id_merchant_transaksi WHERE DATE(A.tgl_input_detail)='$tgl_loop' AND A.status_transaksi_detail='2' AND A.kd_merchant!='' AND B.kd_jenis_pembayaran!='' AND B.status_transaksi='2' AND B.kd_shift!=''")->fetch_assoc()['omset_bruto'];

    $diskon_perbarang = $db->query("SELECT COALESCE(SUM((A.harga_produk-A.harga_setelah_diskon)*A.jumlah_produk), 0) AS diskon_perbarang FROM merchant_transaksi_detail A JOIN merchant_transaksi B ON A.kd_merchant_transaksi=B.id_merchant_transaksi WHERE DATE(A.tgl_input_detail)='$tgl_loop' AND A.status_transaksi_detail='2' AND A.kd_merchant!='' AND B.kd_jenis_pembayaran!='' AND B.status_transaksi='2' AND B.kd_shift!=''")->fetch_assoc()['diskon_perbarang'];
    $data[$key]['diskon_perbarang'] = $diskon_perbarang;
                
    $diskon_transaksi = $db->query("SELECT COALESCE(SUM(A.diskon),0) AS diskon_transaksi FROM merchant_transaksi A WHERE A.kd_merchant!='' AND A.status_transaksi='2' AND DATE(A.tgl_input_transaksi)='$tgl_loop' AND A.kd_jenis_pembayaran!='' AND A.kd_shift!=''")->fetch_assoc()['diskon_transaksi'];
    $data[$key]['diskon_transaksi'] = $diskon_transaksi;

    $total_diskon = $diskon_perbarang + $diskon_transaksi;
    $data[$key]['total_diskon'] = $total_diskon;

    $data[$key]['omset_bersih'] = $data[$key]['omset_bruto'] - $total_diskon;

    
    $key++;
    $month = strtotime("+1 day", $month);
    
}

?>




<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-12">
        <h1 class="m-0 text-dark">Ringkasan Laporan Penjualan Harian Semua Stall Makanan</h1>
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
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Jumlah Stall</th>
                  <th>Jumlah Terjual</th>
                  <th>Omset Bruto</th>
                  <th>Diskon Produk</th>
                  <th>Diskon Transaksi</th>
                  <th>Omset Bersih</th>
                </tr>
              </thead>
              <tbody>
                <?php $jml_terjual  = 0 ?>
                <?php $omset_bruto  = 0 ?>
                <?php $diskon_perbarang= 0 ?>
                <?php $diskon_transaksi= 0 ?>
                <?php $omset_bersih = 0 ?>
                <?php foreach ($data as $key => $value): ?>
                  <?php $jml_terjual          += $value['jml_terjual'] ?>
                  <?php $omset_bruto          += $value['omset_bruto'] ?>
                  <?php $diskon_perbarang     += $value['diskon_perbarang'] ?>
                  <?php $diskon_transaksi     += $value['diskon_transaksi'] ?>
                  <?php $omset_bersih         += $value['omset_bersih'] ?>
                  <tr>
                    <td><?= tanggal_indo($value['tgl_input_detail']) ?></td>
                    <td><?= $value['jml_merchant'] ?></td>
                    <td><?= $value['jml_terjual'] ?></td>
                    <td><?= $value['omset_bruto'] ?></td>
                    <td><?= $value['diskon_perbarang'] ?></td>
                    <td><?= $value['diskon_transaksi'] ?></td>
                    <td><?= $value['omset_bersih'] ?></td>
                  </tr>
                <?php endforeach ?>
              </tbody>
              <tfoot>
                <tr>
                  <td>TOTAL</td>
                  <td> </td>
                  <td> <?php echo number_format($jml_terjual); ?> </td>
                  <td> <?php echo number_format($omset_bruto); ?> </td>
                  <td> <?php echo number_format($diskon_perbarang); ?> </td>
                  <td> <?php echo number_format($diskon_transaksi); ?> </td>
                  <td> <?php echo number_format($omset_bersih); ?> </td>
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
      $('#reportrange').html(start.format('D MMMM YYYY') + ' - ' + end.format('D MMMM YYYY'));
      // var form = $('<form action="?page=penjualan&action=ringkasanharian" method="post">' +
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

    var form = $('<form action="?page=penjualan&action=ringkasanharian" method="post">' +
      '<input type="hidden" name="tgl_awal" value="' + start + '" />' +
      '<input type="hidden" name="tgl_akhir" value="' + end + '" />' +
      '</form>');
    $('body').append(form);
    form.submit();
  }

  function exportexcel(awal, akhir){
    var form = $('<form target="_blank" action="view/laporan/lap_penjualan_ringkasanharian_excel.php" method="post">' +
      '<input type="hidden" name="tgl_awal" value="' + awal + '" />' +
      '<input type="hidden" name="tgl_akhir" value="' + akhir + '" />' +
      '</form>');
    $('body').append(form);
    form.submit();
    // alert(awal+'    '+akhir);
  }
</script>