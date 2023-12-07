<?php
    if(empty($_POST['tgl_awal']) || empty($_POST['tgl_akhir'])){
        $tgl_awal = date('Y-m-d');
        $tgl_akhir = date('Y-m-d');
    } else {
        $tgl_awal = date('Y-m-d', strtotime($_POST['tgl_awal']));
        $tgl_akhir = date('Y-m-d', strtotime($_POST['tgl_akhir']));
    }
    
    $tahun_awal = substr($tgl_awal,0,4);
    $tahun_akhir = substr($tgl_akhir,0,4);
    
    $bulan_awal = substr($tgl_awal,5,2);
    $bulan_akhir = substr($tgl_akhir,5,2);
    
    $tanggal_awal = substr($tgl_awal,8,2);
    $tanggal_akhir = substr($tgl_akhir,8,2); 

    if( $tgl_awal != "" AND $tgl_akhir != ""){
        
        $list_pendapatan_pershift = $db->query(" SELECT a.id_shift, a.nama_shift
                                                FROM shift a ")->fetch_all(MYSQLI_ASSOC);
        $num_row = COUNT($list_pendapatan_pershift)+1;
            // var_dump($num_row);
            
        $list_merchant = $db->query(" SELECT a.id_merchant, a.nama_merchant
                                        FROM merchant a ")->fetch_all(MYSQLI_ASSOC); 
        // var_dump($list_merchant);die();
        $start = $month = strtotime($tgl_awal);
        $end = strtotime($tgl_akhir);
        
        while($month <= $end) { 
            // $date_loop =  date('Y-m-d', $month);
            $bulan_loop =  date('m', $month);
            $tahun_loop =  date('Y', $month);
            
            // var_dump($list_pendapatan_pershift);die();
            
            foreach ($list_merchant as $key => $merchant) {
                
                foreach ($list_pendapatan_pershift as $key2 => $value_shift) {
                  
                    $hitung_omset_bruto = $db->query("SELECT  COALESCE(SUM(a.harga_produk * a.jumlah_produk),0) AS jml  
                                                    FROM merchant_transaksi_detail a
                                                    JOIN merchant_transaksi b on a.kd_merchant_transaksi = b.id_merchant_transaksi
                                                    WHERE b.kd_merchant='$merchant[id_merchant]'
                                                        AND b.status_transaksi ='2'
                                                        AND b.kd_jenis_pembayaran !=''
                                                        AND b.kd_shift ='$value_shift[id_shift]'
                                                        AND YEAR(b.tgl_input_transaksi) = '$tahun_loop'
                                                        AND MONTH(b.tgl_input_transaksi) = '$bulan_loop'
                                                         ")->fetch_assoc()['jml'];
                    
                    $diskon = $db->query("SELECT COALESCE(SUM((a.harga_produk-a.harga_setelah_diskon)*a.jumlah_produk),0) AS jml 
                                            FROM merchant_transaksi_detail a 
                                            JOIN merchant_transaksi b ON a.kd_merchant_transaksi=b.id_merchant_transaksi 
                                            WHERE b.kd_jenis_pembayaran!='' 
                                                AND a.kd_merchant='$merchant[id_merchant]'
                                                AND b.kd_shift ='$value_shift[id_shift]' 
                                                AND a.status_transaksi_detail='2'
                                                AND YEAR(b.tgl_input_transaksi) = '$tahun_loop'
                                                AND MONTH(b.tgl_input_transaksi) = '$bulan_loop'
                                                ")->fetch_assoc()['jml'];
                                                         
                    $diskon_pertransaksi = $db->query("SELECT COALESCE(SUM(a.diskon),0) AS jml 
                                                    FROM merchant_transaksi a    
                                                    WHERE a.kd_merchant='$merchant[id_merchant]'
                                                    	AND a.status_transaksi ='2'
                                                        AND a.kd_jenis_pembayaran !=''
                                                        AND a.kd_shift ='$value_shift[id_shift]'
                                                        AND YEAR(a.tgl_input_transaksi) = '$tahun_loop'
                                                        AND MONTH(a.tgl_input_transaksi) = '$bulan_loop'
                                                         ")->fetch_assoc()['jml'];
                                                         
                    $total_bersih = $db->query("SELECT COALESCE(SUM(a.tagihan_nota),0) AS jml 
                                                    FROM merchant_transaksi a    
                                                    WHERE a.kd_merchant='$merchant[id_merchant]'
                                                    	AND a.status_transaksi ='2'
                                                        AND a.kd_jenis_pembayaran !=''
                                                        AND a.kd_shift ='$value_shift[id_shift]'
                                                        AND YEAR(a.tgl_input_transaksi) = '$tahun_loop'
                                                        AND MONTH(a.tgl_input_transaksi) = '$bulan_loop'
                                                         ")->fetch_assoc()['jml'];
                //   var_dump($result_pershit);
                    $data['shift'] = $list_pendapatan_pershift;
                
                  $subdata['nama_shift'] = $value_shift['nama_shift'];
                  $subdata['omset_bruto'] = $hitung_omset_bruto;
                  $subdata['diskon'] = $diskon;
                  $subdata['diskon_pertransaksi'] = $diskon_pertransaksi;
                  $subdata['total_bersih'] = $total_bersih;
                
                //   $data['periode'][$date_loop][$value_shift['id_shift']."-".$value_shift['nama_shift']] =  $subdata;
                  $data['periode'][$bulan_loop." ".$tahun_loop][$merchant['nama_merchant']][$value_shift['id_shift']] =  $subdata;
            
                }
            }
        
            $month = strtotime("+1 month", $month);
        
          }
          
        //   echo "<pre>";
        //   echo print_r($data);
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
        <h1 class="m-0 text-dark">Ringkasan Laporan Penjualan Bulanan Semua Stall Makanan</h1>
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
                    <div class="table-responsive">
                        <table id="example" class="table table-bordered table-striped text-sm">
                            <thead>
                                <tr>
                                    <th class="text-center" rowspan="2">Bulan</th>
                                    <th class="text-center" rowspan="2">Nama Stall</th>
                                    <?php foreach($data['shift'] as $key => $shift) { ?>
                                        <th class="text-center" colspan="5"><?=$shift['nama_shift']?></th>
                                    <?php } ?>
                                    <th class="text-center" colspan="5">Total</th>
                                </tr>
                                <tr>
                                    <?php foreach($data['shift'] as $key => $shift) { ?>
                                        <th>Omset Bruto</th>
                                        <th>Diskon Produk</th>
                                        <th>Diskon Transaksi</th>
                                        <th>Total Diskon</th>
                                        <th>Omset Bersih</th>
                                    <?php } ?>
                                        <th>Omset Bruto</th>
                                        <th>Diskon Produk</th>
                                        <th>Diskon Transaksi</th>
                                        <th>Total Diskon</th>
                                        <th>Omset Bersih</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    foreach($data['shift'] as $key3 => $shift) { 
                                        $final_shift_omset_bruto[$shift['id_shift']] = 0;
                                        $final_shift_diskon_produk[$shift['id_shift']] = 0;
                                        $final_shift_diskon_transaksi[$shift['id_shift']] = 0;
                                        $final_shift_total_diskon[$shift['id_shift']] = 0;
                                        $final_shift_total_bersih[$shift['id_shift']] = 0;
                                    } 
                                    $final_omset_bruto[$shift['id_shift']] = 0;
                                    $final_diskon_produk[$shift['id_shift']] = 0;
                                    $final_diskon_transaksi[$shift['id_shift']] = 0;
                                    $final_total_diskon[$shift['id_shift']] = 0;
                                    $final_total_bersih[$shift['id_shift']] = 0;
                                ?>
                                <?php foreach($data['periode'] as $tanggal => $key) { ?>
                                    <?php $jml_rowspan_pertgl = count($key) ?>
                                    <?php $set_rowspan = false ?>
                                    <?php foreach($data['shift'] as $key2 => $shift) { 
                                        $all_tgl_shift_omset_bruto[$tanggal][$shift['id_shift']] = 0;
                                        $all_tgl_shift_diskon_produk[$tanggal][$shift['id_shift']] = 0;
                                        $all_tgl_shift_diskon_transaksi[$tanggal][$shift['id_shift']] = 0;
                                        $all_tgl_shift_total_diskon[$tanggal][$shift['id_shift']] = 0;
                                        $all_tgl_shift_total_bersih[$tanggal][$shift['id_shift']] = 0;
                                        $all_tgl_total_omset_bruto[$tanggal][$shift['id_shift']] = 0;
                                        $all_tgl_total_diskon_produk[$tanggal][$shift['id_shift']] = 0;
                                        $all_tgl_total_diskon_transaksi[$tanggal][$shift['id_shift']] = 0;
                                        $all_tgl_total_diskon_gabungan[$tanggal][$shift['id_shift']] = 0;
                                        $all_tgl_total_omset_bersih[$tanggal][$shift['id_shift']] = 0;
                                        //$final_omset_bruto[$shift['id_shift']] = 0;
                                    } ?>
                                    <?php foreach($key as $id_merchant => $value_shift) { ?>
                                        <?php
                                            $omset_bruto_perstall = 0;
                                            $diskon_produk_perstall = 0;
                                            $diskon_transaksi_perstall = 0;
                                            $diskon_total_diskon_perstall = 0;
                                            $diskon_total_bersih_perstall = 0;
                                        ?>
                                        <tr>
                                            <?php if(!$set_rowspan): ?>
                                                <?php $pecah_tanggal = explode(" ",$tanggal) ?>
                                                <td rowspan="<?= $jml_rowspan_pertgl ?>"><?= bulanAbjad($pecah_tanggal['0']) ?> <?= $pecah_tanggal['1'] ?></td>
                                                <?php $set_rowspan = true; ?>
                                            <?php endif; ?>
                                            <td><?= $id_merchant ?></td>
                                            <?php foreach($data['shift'] as $key_shift => $shift ) { ?>
                                                <?php
                                                    $omset_bruto_perstall += $value_shift[$shift['id_shift']]['omset_bruto'];
                                                    $diskon_produk_perstall += $value_shift[$shift['id_shift']]['diskon'];
                                                    $diskon_transaksi_perstall += $value_shift[$shift['id_shift']]['diskon_pertransaksi'];
                                                    $diskon_total_diskon_perstall += $value_shift[$shift['id_shift']]['diskon_pertransaksi'] + $value_shift[$shift['id_shift']]['diskon'] ;
                                                    $diskon_total_bersih_perstall += $value_shift[$shift['id_shift']]['total_bersih'] ;
                                                    $all_tgl_shift_omset_bruto[$tanggal][$shift['id_shift']] += $value_shift[$shift['id_shift']]['omset_bruto'];
                                                    $all_tgl_shift_diskon_produk[$tanggal][$shift['id_shift']] += $value_shift[$shift['id_shift']]['diskon'];
                                                    $all_tgl_shift_diskon_transaksi[$tanggal][$shift['id_shift']] += $value_shift[$shift['id_shift']]['diskon_pertransaksi'];
                                                    $all_tgl_shift_total_diskon[$tanggal][$shift['id_shift']] += $value_shift[$shift['id_shift']]['diskon_pertransaksi'] + $value_shift[$shift['id_shift']]['diskon'];
                                                    $all_tgl_shift_total_bersih[$tanggal][$shift['id_shift']] += $value_shift[$shift['id_shift']]['total_bersih'];
                                                ?>
                                                <td><?= number_format($value_shift[$shift['id_shift']]['omset_bruto']) ?></td>
                                                <td><?= number_format($value_shift[$shift['id_shift']]['diskon']) ?></td>
                                                <td><?= number_format($value_shift[$shift['id_shift']]['diskon_pertransaksi']) ?></td>
                                                <td><?= number_format($value_shift[$shift['id_shift']]['diskon_pertransaksi'] + $value_shift[$shift['id_shift']]['diskon']) ?></td>
                                                <td><?= number_format($value_shift[$shift['id_shift']]['total_bersih']) ?></td>
                                            <?php } ?>
                                            <?php
                                                $all_tgl_total_omset_bruto[$tanggal][$shift['id_shift']] += $omset_bruto_perstall;
                                                $all_tgl_total_diskon_produk[$tanggal][$shift['id_shift']] += $diskon_produk_perstall;
                                                $all_tgl_total_diskon_transaksi[$tanggal][$shift['id_shift']] += $diskon_transaksi_perstall;
                                                $all_tgl_total_diskon_gabungan[$tanggal][$shift['id_shift']] += $diskon_total_diskon_perstall;
                                                $all_tgl_total_omset_bersih[$tanggal][$shift['id_shift']] += $diskon_total_bersih_perstall;
                                            ?>
                                            <td><?= number_format($omset_bruto_perstall) ?></td>
                                            <td><?= number_format($diskon_produk_perstall) ?></td>
                                            <td><?= number_format($diskon_transaksi_perstall) ?></td>
                                            <td><?= number_format($diskon_total_diskon_perstall) ?></td>
                                            <td><?= number_format($diskon_total_bersih_perstall) ?></td>
                                        </tr>
                                    <?php } ?>
                                        <tr>
                                            <th class="text-center" colspan="2">Total</th>
                                            <?php foreach($data['shift'] as $key_shift => $shift ) { ?>
                                                <?php
                                                    $final_shift_omset_bruto[$shift['id_shift']] += $all_tgl_shift_omset_bruto[$tanggal][$shift['id_shift']];
                                                    $final_shift_diskon_produk[$shift['id_shift']] += $all_tgl_shift_diskon_produk[$tanggal][$shift['id_shift']];
                                                    $final_shift_diskon_transaksi[$shift['id_shift']] += $all_tgl_shift_diskon_transaksi[$tanggal][$shift['id_shift']];
                                                    $final_shift_total_diskon[$shift['id_shift']] += $all_tgl_shift_total_diskon[$tanggal][$shift['id_shift']];
                                                    $final_shift_total_bersih[$shift['id_shift']] += $all_tgl_shift_total_bersih[$tanggal][$shift['id_shift']];
                                                ?>
                                                <th><?= number_format($all_tgl_shift_omset_bruto[$tanggal][$shift['id_shift']]) ?></th>
                                                <th><?= number_format($all_tgl_shift_diskon_produk[$tanggal][$shift['id_shift']]) ?></th>
                                                <th><?= number_format($all_tgl_shift_diskon_transaksi[$tanggal][$shift['id_shift']]) ?></th>
                                                <th><?= number_format($all_tgl_shift_total_diskon[$tanggal][$shift['id_shift']]) ?></th>
                                                <th><?= number_format($all_tgl_shift_total_bersih[$tanggal][$shift['id_shift']]) ?></th>
                                            <?php } 
                                                $final_omset_bruto[$shift['id_shift']] += $all_tgl_total_omset_bruto[$tanggal][$shift['id_shift']];
                                                $final_diskon_produk[$shift['id_shift']] += $all_tgl_total_diskon_produk[$tanggal][$shift['id_shift']];
                                                $final_diskon_transaksi[$shift['id_shift']] += $all_tgl_total_diskon_transaksi[$tanggal][$shift['id_shift']];
                                                $final_total_diskon[$shift['id_shift']] += $all_tgl_total_diskon_gabungan[$tanggal][$shift['id_shift']];
                                                $final_total_bersih[$shift['id_shift']] += $all_tgl_total_omset_bersih[$tanggal][$shift['id_shift']];
                                            ?>
                                            <th><?= number_format($all_tgl_total_omset_bruto[$tanggal][$shift['id_shift']]) ?></th>
                                            <th><?= number_format($all_tgl_total_diskon_produk[$tanggal][$shift['id_shift']]) ?></th>
                                            <th><?= number_format($all_tgl_total_diskon_transaksi[$tanggal][$shift['id_shift']]) ?></th>
                                            <th><?= number_format($all_tgl_total_diskon_gabungan[$tanggal][$shift['id_shift']]) ?></th>
                                            <th><?= number_format($all_tgl_total_omset_bersih[$tanggal][$shift['id_shift']]) ?></th>
                                        </tr>
                                        <tr>
                                            <td colspan="20"></td>
                                        </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-center" colspan="2">Total Dari Seluruh</th>
                                    <?php foreach($data['shift'] as $key_shift => $shift ) { ?>
                                        <th><?= number_format($final_shift_omset_bruto[$shift['id_shift']]) ?></th>
                                        <th><?= number_format($final_shift_diskon_produk[$shift['id_shift']]) ?></th>
                                        <th><?= number_format($final_shift_diskon_transaksi[$shift['id_shift']]) ?></th>
                                        <th><?= number_format($final_shift_total_diskon[$shift['id_shift']]) ?></th>
                                        <th><?= number_format($final_shift_total_bersih[$shift['id_shift']]) ?></th>
                                    <?php } ?>
                                    <th><?= number_format($final_omset_bruto[$shift['id_shift']]) ?></th>
                                    <th><?= number_format($final_diskon_produk[$shift['id_shift']]) ?></th>
                                    <th><?= number_format($final_diskon_transaksi[$shift['id_shift']]) ?></th>
                                    <th><?= number_format($final_total_diskon[$shift['id_shift']]) ?></th>
                                    <th><?= number_format($final_total_bersih[$shift['id_shift']]) ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
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
    var form = $('<form target="_blank" action="view/laporan/lap_penjualan_laporanbulanan_excel.php" method="post">' +
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
    // var form = $('<form action="?page=penjualan&action=ringkasanbulanan" method="post">' +
    //   '<input type="hidden" name="tgl_awal" value="' + start + '" />' +
    //   '<input type="hidden" name="tgl_akhir" value="' + end + '" />' +
    //   '</form>');
    // $('body').append(form);
    // form.submit();
  });

  function terapkan(){
    var form = $('<form action="?page=penjualan&action=laporanbulanan" method="post">' +
      '<input type="hidden" name="tgl_awal" value="' + window.global_date_start + '" />' +
      '<input type="hidden" name="tgl_akhir" value="' + window.global_date_end + '" />' +
      '</form>');
    $('body').append(form);
    form.submit();
  }

</script>
