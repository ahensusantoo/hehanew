<?php
    if (isset($_POST['rincian_transaksi'])){
        include "../../templates/koneksi.php";
        $id_merchant_transaksi  = enkripsiDekripsi($_POST['id_merchant_transaksi'],'dekripsi');
        $kd_merchant            = $_POST['kd_merchant'];
        
        $transaksi_detail_revisi = " SELECT a.keterangan_revisi, c.nama_employee, b.status_transaksi, a.tanggal_revisi
                                        FROM merchant_transaksi_revisi a
                                        LEFT JOIN merchant_transaksi b ON a.kd_transaksi = b.id_merchant_transaksi 
                                        LEFT JOIN merchant_employee c ON a.kd_admin = c.id_merchant_employee 
                                        WHERE b.id_merchant_transaksi = '$id_merchant_transaksi'
                                    ";
        $sql_transaksi_detail_revisi = $db->query($transaksi_detail_revisi)->fetch_assoc();
        
        $query_rincian_transaksi = " SELECT * 
                                    FROM merchant_transaksi_detail a
                                    LEFT JOIN merchant_transaksi b ON a.kd_merchant_transaksi = b.id_merchant_transaksi
                                    LEFT JOIN merchant_employee c ON a.kd_merchant_employee = c.id_merchant_employee 
                                    LEFT JOIN merchant_produk d ON a.kd_merchant_produk = d.id_merchant_produk 
                                    WHERE a.kd_merchant_transaksi = '$id_merchant_transaksi'
                                    ";
        
        $sql_transaksi_rincian = $db->query($query_rincian_transaksi)->fetch_all(MYSQLI_ASSOC);
        
        $cek_jenis_bayar = $db->query("SELECT *,
                                        SUM(A.tagihan_nota) tagihan_nota, 
                                                (SELECT COUNT(C.kd_jenis_pembayaran) 
                                                FROM merchant_transaksi C 
                                                WHERE C.id_merchant_transaksi = '$id_merchant_transaksi'
                                                    AND C.kd_jenis_pembayaran !=''
                                                    AND C.kd_jenis_pembayaran = A.kd_jenis_pembayaran) as jumlah_struck
                                        FROM merchant_transaksi A 
                                        JOIN jenis_pembayaran B ON A.kd_jenis_pembayaran = B.id_jenis_pembayaran
                                        WHERE A.id_merchant_transaksi = '$id_merchant_transaksi'
                                            AND A.kd_jenis_pembayaran !=''
                                        GROUP BY A.kd_jenis_pembayaran  
                                    ")->fetch_all(MYSQLI_ASSOC);
    
        //echo json_encode($sql_transaksi_detail);
    }
?>

<?php if($sql_transaksi_detail_revisi['status_transaksi'] == "3") : ?>
    <table style="border-collapse: collapse; width: 100%" class="text-red">
	<thead>
		<tr>
			<td>Di Batalkan Oleh</td>
			<td> : </td>
			<td><?= @$sql_transaksi_detail_revisi['nama_employee'] ?>.</td>
		</tr>
		<tr>
			<td>Alasan </td>
			<td> : </td>
			<td><?= @$sql_transaksi_detail_revisi['keterangan_revisi'] ?></td>
		</tr>
		<tr>
			<td>Tanggal Cancel</td>
			<td> : </td>
			<td><?= tanggal_jam_indo(@$sql_transaksi_detail_revisi['tanggal_revisi']) ?></td>
		</tr>
	</thead>
</table>
    <hr>
<?php endif; ?>

<table style="border-collapse: collapse; width: 100%">
	<thead>
		<tr>
			<td>Kasir</td>
			<td> : </td>
			<td><?= $sql_transaksi_rincian['0']['nama_employee'] ?></td>
		</tr>
		<tr>
			<td>Tanggal Transaksi</td>
			<td> : </td>
			<td><?= tanggal_jam_indo($sql_transaksi_rincian['0']['tgl_input_transaksi']) ?></td>
		</tr>
	</thead>
</table>

<hr>
<center>RINCIAN</center>
<hr>

<table class="table table-bordered table-striped">
	<thead>
	    <tr>
	        <th>No</th>
	        <th>Nama Produk</th>
	        <th>Harga Produk</th>
	        <th>Jumlah Produk</th>
	        <th>Sub total</th>
	    </tr>
	</thead>
	<tbody>
	    <?php $total_jumlah = 0; $sub_total = 0; $diskon = 0; ?>
	    <?php $no=1; foreach($sql_transaksi_rincian as $key => $value) { ?>
	    <?php 
	        $sub_total += (int)$value['harga_produk']*$value['jumlah_produk'];
            $total_jumlah += (int)$value['jumlah_produk'];
            if ($value['diskon'] != "0"){
                $diskon += (int)(($value['diskon'] * $value['harga_produk'] / 100)* $value['jumlah_produk'] );    
            }else{
                $diskon += (int)($value['diskon']) ;
            }
	    ?>
    		<tr>
    	        <td><?= $no++ ?></td>
    	        <td><?= $value['nama_produk'] ?></td>
    	        <td><?= number_format($value['harga_produk']) ?></td>
    	        <td><?= $value['jumlah_produk'] ?></td>
    	        <td><?= number_format($value['harga_produk'] * $value['jumlah_produk']) ?></td>
    	    </tr>
    	<?php } ?>
	</tbody>
	<tfoot>
	    <tr>
	        <th colspan="3">Total</th>
	        <th><?= $total_jumlah ?></th>
	        <th><?= number_format($sub_total) ?></th>
	    </tr>
	    <tr>
	        <th colspan="4">Diskon</th>
	        <th><?= $diskon ?></th>
	    </tr>
	    <tr>
	        <th colspan="4">Diskon</th>
	        <th><?= number_format($sub_total - $diskon)  ?></th>
	    </tr>
	</tfoot>
</table>

<hr>
<center><b>RINCIAN</b></center>
<hr>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th style="text-align: center;">Jenis Pembayaran</th>
            <th style="text-align: center;">Nominal</th>
        </tr>
    </thead>
    <tbody>
        <?php $total_rincian_pembayaran = 0; $total_struck = 0; ?>
        <?php foreach ($cek_jenis_bayar as $key => $value) : ?>
            <?php $total_rincian_pembayaran += $value['tagihan_nota']; $total_struck += $value['jumlah_struck']; ?>
            <tr>
                <td><?= $value['nama_jenis_pembayaran'] ?></td>
                <td align="right">Rp <?= number_format($value['tagihan_nota']) ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td ><b>TOTAL</b></td>
            <td align="right"><b>Rp <?= number_format($total_rincian_pembayaran) ?></b></td>
        </tr>
    </tbody>
</table>
