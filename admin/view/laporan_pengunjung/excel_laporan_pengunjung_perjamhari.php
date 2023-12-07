<?php
    include("../../templates/koneksi.php");

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=Laporan Perjam.xls");
    
?>

<?php
if(empty($_POST['tgl_awal']) || empty($_POST['tgl_akhir'])){
  $tgl_awal = date('Y-m-d');
  $tgl_akhir = date('Y-m-d');
} else {
  $tgl_awal = date('Y-m-d', strtotime($_POST['tgl_awal']));
  $tgl_akhir = date('Y-m-d', strtotime($_POST['tgl_akhir']));
}
?>


<?php 
    $jml_total = 0;
    for($i=8; $i<24; $i++){
        
        $jam_mulai = sprintf('%02s', $i).":00";
        $jam_akhir = sprintf('%02s', $i).":59";
        
        $jam = $jam_mulai." - ".$jam_akhir;
        
        $list_jam[$jam] = $db->query("SELECT SUM(jumlah_tiket) AS jml FROM transaksi WHERE HOUR(tanggal_transaksi) BETWEEN '$jam_mulai' AND '$jam_akhir' AND DATE(tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir'")->fetch_assoc()['jml'];
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


<style type="text/css">
	table{
		margin: 20px auto;
		border-collapse: collapse;
	}
	table th,
	table td{
		border: 1px solid #3c3c3c;
		padding: 3px 8px;
		font-size: 80%;
	}
</style>



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

<table border="1" class="table table-bordered table-hover" >
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
            <?php $persen_satuan = round(($value / $jml_total) * 100) ?>
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
