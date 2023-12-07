<?php
    // include("../../koneksi.php");
    include("../../templates/koneksi.php");
    if(empty($_POST['awal']) || empty($_POST['akhir'])){
     $tgl_awal = date('Y-m-d');
     $tgl_akhir = date('Y-m-d');
    } else {
     $tgl_awal = date('Y-m-d', strtotime($_POST['awal']));
     $tgl_akhir = date('Y-m-d', strtotime($_POST['akhir']));
    }


    $data = $db->query("
        SELECT D.id_photobooth_stan, D.nama_photobooth_stan, C.id_shift, C.nama_shift, A.harga_satuan, 
            COALESCE(SUM(A.jumlah_tiket),0) AS jumlah, 
            COALESCE(SUM(A.jumlah_tiket*A.harga_satuan),0) AS subtotal 
        FROM photobooth_tiket A 
        JOIN photobooth_transaksi B ON A.kd_photobooth_transaksi=B.id_photobooth_transaksi 
        JOIN shift C ON B.kd_shift=C.id_shift
        JOIN photobooth_stan D ON A.kd_photobooth_stan=D.id_photobooth_stan 
        WHERE DATE(B.tanggal_photobooth_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' 
            AND B.status_transaksi='1' 
            AND A.status_tiket!='2'
            AND A.harga_satuan > 0
        GROUP BY A.kd_photobooth_stan, A.harga_satuan, B.kd_shift
    ")->fetch_all(MYSQLI_ASSOC);

    $pembayaran = $db->query("SELECT B.id_jenis_pembayaran, B.nama_jenis_pembayaran, COALESCE(SUM(A.total_transaksi),0) AS jumlah, COALESCE(SUM(A.diskon),0) AS diskon FROM photobooth_transaksi A JOIN jenis_pembayaran B ON A.kd_jenis_pembayaran=B.id_jenis_pembayaran WHERE A.status_transaksi='1' AND DATE(A.tanggal_photobooth_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' GROUP BY B.id_jenis_pembayaran")->fetch_all(MYSQLI_ASSOC);


    $data_final = [];
    $data_shift = [];
    $data_pershift = [];
    $total_tiket = [];
    $total_tiket_jml = [];
    $total_bawah = [];
    foreach ($data as $key => $value) {
        $data_tiket[$value['id_photobooth_stan']] = $value['nama_photobooth_stan'];
        $data_shift[$value['id_shift']] = $value['nama_shift'];

        $data_pershift[$value['id_photobooth_stan']][$value['id_shift']]['nama'] = $value['nama_photobooth_stan'];
        $data_pershift[$value['id_photobooth_stan']][$value['id_shift']]['jumlah'] = $value['jumlah'];
        $data_pershift[$value['id_photobooth_stan']][$value['id_shift']]['harga_satuan'] = $value['harga_satuan'];
        $data_pershift[$value['id_photobooth_stan']][$value['id_shift']]['subtotal'] = $value['subtotal'];

        if (isset($total_tiket[$value['id_photobooth_stan']])) {
            $total_tiket[$value['id_photobooth_stan']]['total'] += $data_pershift[$value['id_photobooth_stan']][$value['id_shift']]['subtotal'];
            $total_tiket[$value['id_photobooth_stan']]['jumlah'] +=  $value['jumlah'];
        }else{
            $total_tiket[$value['id_photobooth_stan']]['total'] = $data_pershift[$value['id_photobooth_stan']][$value['id_shift']]['subtotal'];
            $total_tiket[$value['id_photobooth_stan']]['jumlah'] =  $value['jumlah'];
        }

        if (isset($total_bawah[$value['id_shift']]['jumlah'])) {
            $total_bawah[$value['id_shift']]['jumlah'] += $value['jumlah'];
            $total_bawah[$value['id_shift']]['subtotal'] += $data_pershift[$value['id_photobooth_stan']][$value['id_shift']]['subtotal'];
        }else{
            $total_bawah[$value['id_shift']]['jumlah'] = $value['jumlah'];
            $total_bawah[$value['id_shift']]['subtotal'] = $data_pershift[$value['id_photobooth_stan']][$value['id_shift']]['subtotal'];
        }

    }

    $kolom_shift = count($data_shift)*3;

    $total_diskon = 0;
    foreach ($pembayaran as $key => $value){
        $total_diskon += $value['diskon'];
    }



?>

<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script> -->
<script src="../../plugins/export-excel/src/jquery.table2excel.js"></script>
<input type="hidden" name="namafile" id="namafile" value="<?= 'Laporan Penjualan Tiket '.tanggal_indo($tgl_awal).' - '.tanggal_indo($tgl_akhir); ?>">

<table class="table2excel" data-tableName="Test Table 1" border="1">
  <tr>
    <th colspan="7"><?= 'Laporan Penjualan Tiket '.tanggal_indo($tgl_awal).' - '.tanggal_indo($tgl_akhir); ?></th>
  </tr>

    <thead>
        <tr>
            <th rowspan="2">Spot Foto</th>
            <?php foreach ($data_shift as $key => $value): ?>
                <th colspan="3" style="text-align: center;"><?= $value ?></th>
            <?php endforeach ?>
            <th colspan="2" style="text-align: center;">Total</th>
        </tr>
        <tr>
            <?php foreach ($data_shift as $key => $value): ?>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
            <?php endforeach ?>
            <th>Jumlah</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data_tiket as $id_tiket => $nama_tiket): ?>
            <tr>
                <td><?= $nama_tiket ?></td>
                <?php foreach ($data_shift as $key => $value): ?>
                    <?php if (!empty($data_pershift[$id_tiket][$key]['jumlah'])): ?>
                        <td><?= number_format(@$data_pershift[$id_tiket][$key]['harga_satuan'],0,',','.') ?></td>
                        <td><?= number_format(@$data_pershift[$id_tiket][$key]['jumlah'],0,',','.') ?></td>
                        <td><?= number_format(@$data_pershift[$id_tiket][$key]['subtotal'],0,',','.') ?></td>
                    <?php else: ?>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    <?php endif ?>
                <?php endforeach ?>
                <td><?= number_format(@$total_tiket[$id_tiket]['jumlah'],0,',','.') ?></td>
                <td><?= number_format(@$total_tiket[$id_tiket]['total'],0,',','.') ?></td>
            </tr>
        <?php endforeach ?>

        <tr>
            <td align="right">DISKON</td>
            <td colspan="<?= $kolom_shift+1 ?>"></td>
            <td nowrap="">- <?= number_format($total_diskon,0,',','.') ?></td>
        </tr>
        <tr>
            <td align="right"><b>TOTAL</b></td>
            <?php $total_all = 0; ?>
            <?php $jml_pertiket = 0; ?>
            <?php foreach ($data_shift as $key => $value): ?>
                <td>-</td>
                <td><?= number_format($total_bawah[$key]['jumlah'],0,',','.') ?></td>
                <td><?= number_format($total_bawah[$key]['subtotal'],0,',','.') ?></td>
                <?php $total_all += $total_bawah[$key]['subtotal']; ?>
                <?php $jml_pertiket += $total_bawah[$key]['jumlah']; ?>
            <?php endforeach ?>
            <td><?= number_format($jml_pertiket,0,',','.') ?></td>
            <td><?= number_format($total_all-$total_diskon,0,',','.') ?></td>
        </tr>

        <tr>
            <td colspan="<?= $kolom_shift+3 ?>">&nbsp;</td>
        </tr>
        <?php $total_bayar = 0; ?>
        <?php foreach ($pembayaran as $key => $value): ?>
            <?php $total_bayar += $value['jumlah'] ?>
            <tr>
                <td align="right"><?= $value['nama_jenis_pembayaran'] ?></td>
                <td colspan="<?= $kolom_shift+1 ?>"></td>
                <td><?= number_format($value['jumlah'],0,',','.') ?></td>
            </tr>
        <?php endforeach ?>
        <tr>
            <td align="right">DISKON</td>
            <td colspan="<?= $kolom_shift+1 ?>"></td>
            <td nowrap="">- <?= number_format($total_diskon,0,',','.') ?></td>
        </tr>
        <tr>
            <td align="right"><b>TOTAL AKHIR</b></td>
            <td colspan="<?= $kolom_shift+1 ?>"></td>
            <td><?= number_format($total_bayar,0,',','.') ?></td>
        </tr>
    </tbody>
  
</table>


<script type="text/javascript">
  $(function() {
    var name_element = document.getElementById('namafile').value;
    $(".table2excel").table2excel({
      exclude: ".noExl",
      name: "Excel Document Name",
      filename: name_element,
      fileext: ".xls",
      exclude_img: true,
      exclude_links: true,
      exclude_inputs: true
    });
    // window.onfocus=function(){ setTimeout(function () { window.close(); }, 500); }
  });
</script>
