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

$starttime = microtime(true); // Top of page

$query = "SELECT A.id_merchant_employee, A.nama_employee, B.nama_merchant FROM merchant_employee A JOIN merchant B ON A.kd_merchant=B.id_merchant WHERE A.status_remove_employee='N'";
$list_kasir = $db->query($query)->fetch_all(MYSQLI_ASSOC);


if($filer1 == 'all'){
  $kasir_dipilih = $list_kasir;
}else{
  $id_merchant = enkripsiDekripsi($filer1, 'dekripsi');
  $kasir_dipilih = $db->query("SELECT A.id_merchant_employee, A.nama_employee, B.nama_merchant FROM merchant_employee A JOIN merchant B ON A.kd_merchant=B.id_merchant WHERE A.status_remove_employee='N' AND A.id_merchant_employee='$id_merchant'")->fetch_all(MYSQLI_ASSOC);
  // $stall_dipilih[0]['id_merchant'] = $filer1;  
}

?>



<link rel="stylesheet" href="plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Laporan Penjualan Harian Per Kasir</h1>
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
            <div class="form-group row">
              <label class="col-sm-1 col-form-label">Kasir :</label>
              <div class="col-sm-11">
                <select name="filer1" class="select2bs4 form-control" style="width: 100%" id="filer1" required>
                    <!-- <option value="all">SEMUA KASIR</option> -->
                    <?php foreach ($list_kasir as $bulan => $value): ?>
                        <?php $id_emp_encryp = enkripsiDekripsi($value['id_merchant_employee'],'enkripsi'); ?>
                        <option value="<?= $id_emp_encryp ?>" <?php if($filer1 == $id_emp_encryp){echo 'selected';} ?>> <?= strtoupper($value['nama_employee']) ?> ( <?= strtoupper($value['nama_merchant']) ?> )</option>
                    <?php endforeach ?>
                </select>
              </div>
            </div>
            <a onclick="terapkan('<?= $tgl_awal ?>', '<?= $tgl_akhir ?>')">
              <button type="button" class="btn btn-primary" style="width: 100%">Terapkan</button>
            </a>
          </div>
        </div>
      </div>
    </div>
    
    
          <div class="card-header">
            <a onclick="exportexcel('<?= $tgl_awal ?>', '<?= $tgl_akhir ?>', '<?= $_POST["filer1"] ?>')">
              <button type="button" class="btn btn-default" style="width: 100%"><i class="fa fa-fw fa-file-download"></i>Eksport Excel</button>
            </a>
          </div>
          

            <?php if (!isset($_POST['filer1'])): ?>
              <br><br>
              <center>Harap Pilih Periode dan Kasir</center>
              <?php goto no_filter; ?>
            <?php endif ?>


            <?php foreach ($kasir_dipilih as $key => $value_kasir): ?>

              <?php
                $nomor=1;
                $grand_total = 0;
                $id_employee = enkripsiDekripsi($_POST['filer1'] , 'dekripsi');
                

                $data = @$db->query("
                    SELECT 

                    B.nama_produk, A.harga_produk, A.harga_setelah_diskon, A.kd_merchant_produk, SUM(A.jumlah_produk) AS jumlah
                    
                    FROM merchant_transaksi_detail A JOIN merchant_produk B ON A.kd_merchant_produk=B.id_merchant_produk JOIN merchant_transaksi D ON A.kd_merchant_transaksi=D.id_merchant_transaksi WHERE A.kd_merchant_employee='$value_kasir[id_merchant_employee]' AND A.status_transaksi_detail='2' AND D.kd_jenis_pembayaran!='' AND DATE(A.tgl_input_detail) BETWEEN '$tgl_awal' AND '$tgl_akhir' GROUP BY A.kd_merchant_produk , A.harga_produk  
                    
                ")->fetch_all(MYSQLI_ASSOC);

                $query_extra = "";
                foreach ($data as $key_for => $value_for) {

                  $query_extra = $query_extra." ( C.kd_merchant_produk='$value_for[kd_merchant_produk]' AND C.harga_produk='$value_for[harga_produk]' ) OR "; 


                  // $jml = $db->query("SELECT SUM(C.jumlah_produk) AS jumlah FROM merchant_transaksi_detail C JOIN merchant_transaksi E ON C.kd_merchant_transaksi=E.id_merchant_transaksi WHERE E.kd_jenis_pembayaran!='' AND C.kd_merchant_employee='$value_kasir[id_merchant_employee]' AND C.kd_merchant_produk='$value_for[kd_merchant_produk]' AND DATE(C.tgl_input_detail) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND C.status_transaksi_detail='2' AND C.harga_produk='$value_for[harga_produk]'")->fetch_assoc()['jumlah'];
                  // $data[$key_for]['jumlah'] = $jml;

                }

                $query_extra = rtrim($query_extra, 'OR ');
                if ($query_extra != '') {
                  $query_extra = " AND (".$query_extra.")";
                }


                // $data_extra_jumlah = $db->query("SELECT SUM(C.jumlah_produk) AS jumlah FROM merchant_transaksi_detail C JOIN merchant_transaksi E ON C.kd_merchant_transaksi=E.id_merchant_transaksi WHERE E.kd_jenis_pembayaran!='' AND C.kd_merchant_employee='$value_kasir[id_merchant_employee]' AND DATE(C.tgl_input_detail) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND C.status_transaksi_detail='2' $query_extra GROUP BY C.kd_merchant_produk, C.harga_produk")->fetch_all(MYSQLI_ASSOC);

                // var_dump($data_extra_jumlah);exit();

                
                $diskon = $db->query("SELECT SUM((A.harga_produk-A.harga_setelah_diskon)*A.jumlah_produk) AS jml FROM merchant_transaksi_detail A JOIN merchant_transaksi B ON A.kd_merchant_transaksi=B.id_merchant_transaksi WHERE B.kd_jenis_pembayaran!='' AND A.kd_merchant_employee='$value_kasir[id_merchant_employee]' AND DATE(A.tgl_input_detail) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND A.status_transaksi_detail='2'")->fetch_assoc()['jml'];

              ?>

              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">


                      <label><?= $value_kasir['nama_employee'] ?></label>

                      <?php if (!isset($data[0])): ?>
                        <span class="text-center"> : Tidak ada data transaksi</span>
                      <?php else: ?>
                      

                        <table id="" class="table table-sm table-bordered table-striped">
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
                            
                            
                            <?php $total_omzet_kotor = 0 ?>
                            <?php $total_produk = 0 ?>
                            <?php foreach($data as $key => $value): ?>
                                <?php $subtotal = $value['jumlah'] * $value['harga_produk'] ?>
                                <?php $total_omzet_kotor += $subtotal ?>
                                <?php $total_produk += $value['jumlah'] ?>
                                <tr>
                                  <td> <?= $key+1 ?></td>
                                  <td> <?= $value['nama_produk'] ?></td>
                                  <td>Rp <?= number_format($value['harga_produk']) ?></td>
                                  <td> <?= number_format($value['jumlah']) ?></td>
                                  <td>Rp <?= number_format($subtotal) ?></td>
                                </tr>
                            <?php endforeach; ?>
                          </tbody>
                          <tfoot>
                            <tr>
                              <td colspan="3">Total</td>
                              <td> <?php echo number_format($total_produk); ?> </td>
                              <td>Rp <?php echo number_format($total_omzet_kotor); ?> </td>
                            </tr>
                            <tr>
                              <td colspan="3">Diskon</td>
                              <td></td>
                              <td>Rp <?php echo number_format($diskon); ?> </td>
                            </tr>
                            <tr>
                              <td colspan="3">Omset Bersih</td>
                              <td></td>
                              <td>Rp <?php echo number_format($total_omzet_kotor - $diskon); ?> </td>
                            </tr>
                          </tfoot>
                        </table>
                        <hr>
                      
                        <?php
                        
                          $rincian = $db->query("
                              SELECT 

                              CASE WHEN A.kd_jenis_pembayaran = '' THEN 'BAYAR NANTI' ELSE B.nama_jenis_pembayaran END AS nama_jenis, A.kd_jenis_pembayaran
                              
                              FROM merchant_transaksi A JOIN jenis_pembayaran B ON A.kd_jenis_pembayaran=B.id_jenis_pembayaran  AND A.status_transaksi='2' GROUP BY A.kd_jenis_pembayaran
                          ")->fetch_all(MYSQLI_ASSOC);

                          foreach ($rincian as $key_rincian => $value_rincian) {
                            
                            $jumlah = $db->query("SELECT SUM(C.tagihan_nota) AS jumlah FROM merchant_transaksi C WHERE C.kd_merchant_employee='$value_kasir[id_merchant_employee]' AND DATE(C.tgl_input_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND C.kd_jenis_pembayaran='$value_rincian[kd_jenis_pembayaran]' AND C.status_transaksi='2'")->fetch_assoc()['jumlah'];

                            $jumlah_struk = $db->query("SELECT COUNT(*) AS jumlah_struk FROM merchant_transaksi C WHERE C.kd_merchant_employee='$value_kasir[id_merchant_employee]' AND DATE(C.tgl_input_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND C.kd_jenis_pembayaran='$value_rincian[kd_jenis_pembayaran]' AND C.status_transaksi='2' ")->fetch_assoc()['jumlah_struk'];

                            $rincian[$key_rincian]['jumlah'] = $jumlah;
                            $rincian[$key_rincian]['jumlah_struk'] = $jumlah_struk;

                          }


                          


                           // $rincian = $db->query("
                           //    SELECT 

                           //    CASE WHEN A.kd_jenis_pembayaran = '' THEN 'BAYAR NANTI' ELSE B.nama_jenis_pembayaran END AS nama_jenis,
                              
                           //    (SELECT jml_penjualan_perkasir('$value_kasir[id_merchant_employee]', '$tgl_awal', '$tgl_akhir', A.kd_jenis_pembayaran) ) AS jumlah,

                           //    (SELECT jml_struk_penjualan_perkasir('$value_kasir[id_merchant_employee]', '$tgl_awal', '$tgl_akhir', A.kd_jenis_pembayaran) ) AS jumlah_struk
                              
                           //    FROM merchant_transaksi A JOIN jenis_pembayaran B ON A.kd_jenis_pembayaran=B.id_jenis_pembayaran  AND A.status_transaksi='2' GROUP BY A.kd_jenis_pembayaran")->fetch_all(MYSQLI_ASSOC);
                            
                        ?>

                        <table id="" class="table table-sm table-bordered table-striped">
                          <thead>
                            <tr>
                              <th>No</th>
                              <th>Jenis Pembayaran</th>
                              <th>Jumlah Struk</th>
                              <th>Total</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php $total_rincian = 0 ?>
                            <?php $total_struk = 0 ?>
                            <?php foreach($rincian as $key => $value): ?>
                                <?php $total_rincian += $value['jumlah'] ?>
                                <?php $total_struk += $value['jumlah_struk'] ?>
                                <tr>
                                  <td> <?= $key+1 ?></td>
                                  <td> <?= $value['nama_jenis'] ?></td>
                                  <td><?= number_format($value['jumlah_struk']) ?></td>
                                  <td>Rp <?= number_format($value['jumlah']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                          </tbody>
                          <tfoot>
                            <tr>
                              <td></td>
                              <td><b>TOTAL</b></td>
                              <td><?php echo number_format($total_struk); ?> </td>
                              <td>Rp <?php echo number_format($total_rincian); ?> </td>
                            </tr>
                          </tfoot>
                        </table>
                        
                        <hr class="mb-5">

                      <?php endif ?>

                    </div>
                  </div>
                </div>
              </div>

            <?php endforeach ?>
            
            
            <?php no_filter: ?>

          
  </div>
</section>

<div class="modal fade" id="myModal" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

    </div>
  </div>
</div>


<script src="plugins/select2/js/select2.full.min.js"></script>


<script type="text/javascript">

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })
    
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
      // var form = $('<form action="?page=penjualan&action=perkasir" method="post">' +
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
    var filer1 = document.getElementById('filer1').value;

    var start_master = new Date(tanggal[0]);
    var start = start_master.getFullYear() + "-" + String(start_master.getMonth() + 1) + "-" + String(start_master.getDate());

    var end_master = new Date(tanggal[1]);
    var end = end_master.getFullYear() + "-" + String(end_master.getMonth() + 1) + "-" + String(end_master.getDate());

    // alert(start + ' aaaa ' + end);

    var form = $('<form action="?page=penjualan&action=perkasir" method="post">' +
      '<input type="hidden" name="tgl_awal" value="' + start + '" />' +
      '<input type="hidden" name="tgl_akhir" value="' + end + '" />' +
      '<input type="hidden" name="filer1" value="' + filer1 + '" />' +
      '</form>');
    $('body').append(form);
    form.submit();
  }

  function exportexcel(awal, akhir, kasir){
    var form = $('<form target="_blank" action="view/laporan/lap_penjualan_perkasir_export_excel.php" method="post">' +
      '<input type="hidden" name="tgl_awal" value="' + awal + '" />' +
      '<input type="hidden" name="tgl_akhir" value="' + akhir + '" />' +
      '<input type="hidden" name="filer1" value="' + kasir + '" />' +
      '</form>');
    $('body').append(form);
    form.submit();
    // alert(awal+'    '+akhir);
  }
</script>


<?php  

  $endtime = microtime(true); // Bottom of page

  printf("Page loaded in %f seconds", $endtime - $starttime );
?>