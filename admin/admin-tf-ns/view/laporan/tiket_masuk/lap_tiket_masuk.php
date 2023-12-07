<?php
    if(empty($_POST['tgl_awal']) || empty($_POST['tgl_akhir'])){
        $tgl_awal = date('Y-m-d');
        $tgl_akhir = date('Y-m-d');
    } else {
        $tgl_awal = date('Y-m-d', strtotime($_POST['tgl_awal']));
        $tgl_akhir = date('Y-m-d', strtotime($_POST['tgl_akhir']));
    }


    $response_newserver = @CRUD_API(base_url_newserve()."api/lap_tiket_masuk/tiket_masuk_harian" ,json_encode([
        // 'act'               => "l",
        'tgl_awal'          => $tgl_awal,
        'tgl_akhir'         => $tgl_akhir,
    ]));

    // $response_newserver =json_encode([
    //     // 'act'               => "l",
    //     'tgl_awal'          => $tgl_awal,
    //     'tgl_akhir'         => $tgl_akhir,
    // ]);


    // print_r("<pre>"); print_R($response_newserver); die();

?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Laporan Penjualan Tiket Masuk</h1>
            </div>
        </div>
    </div>
</div>

<div id="cetak">
    <div class="container pt-3">
        <div class="card" style="height: 100%; background-color: #658fcac7">
            <div class="card-body ml-1 mr-1 pl-0 pr-0" style="height: 100%">
                <div class="text-white" style="width:100%">
                    <center>
                        ~ PENDAPATAN TIKET HARIAN ~<br>

                        <?= date_format(date_create($tgl_awal), 'd-M-Y') ?> Sampai <?= date_format(date_create($tgl_akhir), 'd-M-Y') ?><br>
                        (Update : <?= date("d F Y, H:i"); ?> WIB)
                    </center>
                </div>
            </div>
        </div>
    </div>

    <style type="text/css">
        .card{
            box-shadow: 0px 0px 7px rgba(0,0,0,0.2);
            border-radius: 10px;
        }
    </style>

    <section class="">
        <div class="container pt-4">
            <div class="card" style="height: 100%;">
                <div class="card-header">
                    <button type="button" class="btn btn-default float-right" id="daterange-btn">
                        <i class="far fa-calendar-alt"></i> <span id="reportrange"><?= tanggal_indo($tgl_awal).' - '.tanggal_indo($tgl_akhir); ?></span>
                        <i class="fas fa-caret-down"></i>
                    </button>
                </div>
                <div class="card-body" style="height: 100%">
                    <table><tbody><tr><td></td></tr></tbody></table>
                        <?php if (empty($response_newserver['record'])): ?>
                            <hr>
                            <center>Data tidak tersedia</center>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table border="1" class="table table-bordered table-sm mt-3">
                                    <thead>
                                        <tr>
                                            <th colspan="3"><center>RINGKASAN</center></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $total_omset = 0 ?>
                                        <?php foreach ($response_newserver['record'] as $key => $value): ?>
                                            <?php $total_omset += $value['jml'] ?>
                                            <tr>
                                                <td><?= date_format(date_create($value['tanggal']), 'd-M-Y') ?></td>
                                                <td> : </td>
                                                <td><?= number_format($value['jml'],0,',','.') ?></td>
                                            </tr>

                                        <?php endforeach ?>

                                        <tr>
                                            <td><b>TOTAL</b></td>
                                            <td> : </td>
                                            <td><b><?= number_format($total_omset,0,',','.') ?></b></td>
                                        </tr>

                                        <tr>
                                            <td colspan="3">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" align="center"><b>JENIS PEMBAYARAN</b></td>
                                        </tr>

                                        <?php $total_diskon = 0 ?>
                                        <?php $total_pembayaran = 0 ?>
                                        <?php foreach ($response_newserver['pembayaran'] as $key => $value): ?>
                                            <?php $total_diskon += $value['diskon'] ?>
                                            <?php $total_pembayaran += $value['jumlah'] ?>
                                            <tr>
                                                <td><?= strtoupper($value['nama_jenis_pembayaran']) ?></td>
                                                <td>:</td>
                                                <td><?= number_format($value['jumlah'],0,',','.') ?></td>
                                            </tr>
                                        <?php endforeach ?>
                                        <tr>
                                            <td>DISKON</td>
                                            <td>:</td>
                                            <td>- <?= number_format($total_diskon,0,',','.') ?></td>
                                        </tr>
                                        <tr>
                                            <td><b>TOTAL AKHIR</b></td>
                                            <td>:</td>
                                            <td><b><?= number_format($total_pembayaran,0,',','.') ?></b></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button id="btn_download" class="float-right"><i class="fas fa-download"></i></button>
                        <?php endif ?>

                </div>
            </div>
        </div>
    </section>
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
      var form = $('<form action="?page=lap_tiket_masuk" method="post">' +
        '<input type="hidden" name="tgl_awal" value="' + start.format('YYYY-MM-D') + '" />' +
        '<input type="hidden" name="tgl_akhir" value="' + end.format('YYYY-MM-D') + '" />' +
        '</form>');
      $('body').append(form);
      form.submit();
    }
    )
  })

  function exportexcel(awal, akhir){
    var form = $('<form target="_blank" action="view/laporan/lap_photoboothtiket_export_excel.php" method="post">' +
      '<input type="hidden" name="awal" value="' + awal + '" />' +
      '<input type="hidden" name="akhir" value="' + akhir + '" />' +
      '</form>');
    $('body').append(form);
    form.submit();
    // alert(awal+'    '+akhir);
  }

  $("#btn_download").click(function(e) {
        // window.open('data:application/vnd.ms-excel,' + encodeURIComponent( $('#cetak').html()));
        // e.preventDefault();
        var result = 'data:application/vnd.ms-excel,' + encodeURIComponent($('#cetak').html());
        var link = document.createElement("a");
        document.body.appendChild(link);
        link.download = "Laporan Pendapatan Harian Tiket Masuk (<?= date_format(date_create($tgl_awal), 'd-M-Y') ?> Sampai <?= date_format(date_create($tgl_akhir), 'd-M-Y') ?>).xls"; //You need to change file_name here.
        link.href = result;
        link.click();

    });
</script>