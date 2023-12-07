<?php
// include("../../koneksi.php");
include("../../templates/koneksi.php");

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
    
    if(!empty($_POST['supplier'])){
        $supplier = $_POST['supplier'];
    } else {
        $supplier = '';
    }

    $list_stall         = $db->query("SELECT * FROM merchant WHERE status_aktif_merchant='Y' ")->fetch_all(MYSQLI_ASSOC);
    $list_supplier      = $db->query("SELECT * FROM supplier WHERE status_rmv_supplier='N' ")->fetch_all(MYSQLI_ASSOC);

   if($filer1 == 'all'){
        $stall_dipilih = $list_stall;
    }else{
        $id_merchant = enkripsiDekripsi($filer1, 'dekripsi');
        $stall_dipilih = $db->query("SELECT * FROM merchant WHERE status_aktif_merchant='Y' AND id_merchant='$id_merchant'")->fetch_all(MYSQLI_ASSOC);
    }
    
    if($supplier == 'all'){
        $supplier_dipilih = $list_supplier;
    }else{
        $id_supplier = enkripsiDekripsi($supplier, 'dekripsi');
        $supplier_dipilih = $db->query("SELECT * FROM supplier WHERE status_rmv_supplier='N' AND id_supplier ='$id_supplier' ")->fetch_all(MYSQLI_ASSOC);
    }

    
    foreach($stall_dipilih as $key_stall => $value_stall){
        foreach($supplier_dipilih as $key_supplier => $value_supplier){
            $sql = $db->query("SELECT c.nama_produk, c.harga_produk as harga_jual,  d.nama_supplier,
                                COALESCE(SUM(a.jumlah_produk),0) as jumlah_terjual,
                                (SELECT e.harga_beli FROM merchant_history_stok e
                                    WHERE e.kd_merchant_produk = c.id_merchant_produk
                                    GROUP BY e.kd_merchant_produk
                                    ORDER BY e.kd_merchant_produk DESC
                                    LIMIT 1) as harga_beli
                            FROM merchant_transaksi_detail a 
                            JOIN merchant_transaksi b ON a.kd_merchant_transaksi=b.id_merchant_transaksi
                            JOIN merchant_produk c ON a.kd_merchant_produk=c.id_merchant_produk
                            JOIN supplier d ON c.id_supplier=d.id_supplier
                            WHERE b.kd_jenis_pembayaran != ''
                              AND a.kd_merchant = '$value_stall[id_merchant]'
                                AND a.status_transaksi_detail = '2'
                                AND b.status_transaksi = '2'
                                AND c.id_supplier = '$value_supplier[id_supplier]'
                                AND DATE(a.tgl_input_detail) BETWEEN '$tgl_awal' AND '$tgl_akhir'
                                GROUP BY a.kd_merchant_produk
                          ")->fetch_all(MYSQLI_ASSOC);
            $result[$value_stall['nama_merchant']][$value_supplier['nama_supplier']] = $sql;
            
        } 
    }
    
    
    // echo "<pre>"; echo print_r($result); die();
   
    
?>
<!DOCTYPE html>
<html>
<head>
    <!-- Tempusdominus Bbootstrap 4 -->
<link rel="stylesheet" href="../../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
<!-- <input type="hidden" name="namafile" id="namafile" value="<?= 'Laporan Closing Stall '.tanggal_indo(@$_GET['tanggal']) ?>"> -->
    <title></title>
</head>
<body>
<input type="hidden" name="namafile" id="namafile" value="<?= 'Laporan Penjualan Supplier  '.tanggal_indo($tgl_awal).' - '.tanggal_indo($tgl_akhir); ?>">



<?php foreach($result as $key_merchant => $value_merchant ) { ?>
    <?php foreach($value_merchant as $key_supplier => $value_supplier ) { ?>
        <table class="table table-bordered table2excel">
            <thead>
                 <tr>
                    <td><b>Nama Supplier</b></td>
                    <td colspan="5"><b><?=$key_supplier?></b></td>
                </tr>
                <tr>
                    <td><b>Priode</b></td>
                    <td colspan="5"><b><?=tanggal_indo($tgl_awal)?> - <?=tanggal_indo($tgl_akhir)?></b></td>
                </tr>
                <tr>
                    <th colspan="6"><br><br></th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th>Jumlah Terjual</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Omset</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($value_supplier)): ?>
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data</td>
                    </tr>
                <?php else: ?>
                
                    <?php $total_jumlah_terjual = 0; $total_harga_beli = 0; $total_harga_jual = 0; $total_omset = 0; ?>
                    <?php $no = 1; foreach($value_supplier as $key_detail => $value_detail ) { ?>
                        <?php 
                            $omset = ($value_detail['harga_jual'] - $value_detail['harga_beli']) * $value_detail['jumlah_terjual']; 
                            $total_jumlah_terjual += $value_detail['jumlah_terjual'];
                            $total_harga_beli += $value_detail['harga_beli'];
                            $total_harga_jual += $value_detail['harga_jual'];
                            $total_omset += $omset;
                        ?>
                        <tr>
                            <td><?=$no ++ ?></td>
                            <td><?=$value_detail['nama_produk'] ?></td>
                            <td><?=number_format($value_detail['jumlah_terjual'],0,'.',',') ?></td>
                            <td><?=number_format($value_detail['harga_beli'],0,'.',',') ?></td>
                            <td><?=number_format($value_detail['harga_jual'],0,'.',',') ?></td>
                            <td><?=number_format($omset,0,'.',',') ?></td>
                        </tr>
                    <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-center" colspan="2">Total</th>
                    <th><?=number_format($total_jumlah_terjual,0,'.',',') ?></th>
                    <th><?=number_format($total_harga_beli,0,'.',',') ?></th>
                    <th><?=number_format($total_harga_jual,0,'.',',') ?></th>
                    <th><?=number_format($total_omset,0,'.',',') ?></th>
                </tr>
            </tfoot>
                <?php endif; ?>
        </table>
    <?php } ?>
<?php } ?>

<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script> -->
<script src="<?=base_url()?>plugins/export-excel/src/jquery.table2excel.js"></script>

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


</body>
</html>
