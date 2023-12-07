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
        <h1 class="m-0 text-dark">Laporan Pengunjung Perjam Setiap Hari</h1>
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
              
              <?php 
    $jml_total = 0;
    for($i=8; $i<24; $i++){
        
        $jam_mulai = sprintf('%02s', $i).":00";
        $jam_akhir = sprintf('%02s', $i).":59";
        
        $jam = $jam_mulai." - ".$jam_akhir;
        
        $list_jam[$jam] = $db->query("SELECT SUM(jumlah_tiket) AS jml FROM transaksi WHERE HOUR(tanggal_transaksi) BETWEEN '$jam_mulai' AND '$jam_akhir' AND DATE(tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND status_transaksi!='3' ")->fetch_assoc()['jml'];
        $list_jam[$jam] = round($list_jam[$jam]);
        
        if($list_jam[$jam] == ""){
            $list_jam[$jam] = 0;
        }
        
        $jml_total += $list_jam[$jam];
        
    }
    if($jml_total == 0){
        $jml_total = -1;
    }
    
?>



<table class="table">
    <tbody>
        <tr>
            <td colspan="4" align="center">ANALISA JUMLAH PENGUNJUNG BERDASARKAN JAM</td>
        </tr>
        <tr>
            <td colspan="4" align="center">PERIODE <?= tanggal_indo($tgl_awal) ?> sd <?= tanggal_indo($tgl_akhir) ?></td>
        </tr>
        <tr>
            <td colspan="4" align="center"></td>
        </tr>
    </tbody>
</table>

<table class="table table-bordered table-hover table-sm" >
    <thead>
        <tr>
            <th rowspan='2'>No</th>
            <th rowspan='2'>Jam</th>
            <th colspan="2">Jumlah Pengunjung</th>
        </tr>
        <tr>
            <th>Jumlah Orang</th>
            <th>%</th>
        </tr>
    </thead>
    <tbody>
        
        <?php $jml_persen = 0; ?>
        <?php $no = 1; ?>
        <?php foreach ($list_jam as $key => $value): ?>
            <?php $persen_satuan = round(($value / $jml_total) * 100, 3) ?>
            <?php $jml_persen += $persen_satuan ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $key ?></td>
                <td align='right'><?= $value ?></td>
                <td align='right'><?= $persen_satuan ?> % </td>
            </tr>
        <?php endforeach ?>
        <tr>
            <td colspan='2' align='right'>TOTAL</td>
            <td align='right'><?= ($jml_total < 0)? '0' : $jml_total ?></td>
            <td align='right'><?= $jml_persen ?> % </td>
        </tr>
        
        
        
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
    $('#example3').DataTable({
      "paging": false,
      "ordering": false,
      "info": false,
      "autoWidth": false,
      "filter": false,
      // "responsive": true,
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
      var form = $('<form action="?page=pengunjung&action=perjamhari" method="post">' +
        '<input type="hidden" name="tgl_awal" value="' + start.format('YYYY-MM-D') + '" />' +
        '<input type="hidden" name="tgl_akhir" value="' + end.format('YYYY-MM-D') + '" />' +
        '</form>');
      $('body').append(form);
      form.submit();
    }
    )
  })

  function exportexcel(awal, akhir){
    var form = $('<form target="_blank" action="view/laporan_pengunjung/excel_laporan_pengunjung_perjamhari.php" method="post">' +
      '<input type="hidden" name="tgl_awal" value="' + awal + '" />' +
      '<input type="hidden" name="tgl_akhir" value="' + akhir + '" />' +
      '</form>');
    $('body').append(form);
    form.submit();
    // alert(awal+'    '+akhir);
  }
</script>