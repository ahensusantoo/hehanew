<?php 
    include "../../templates/koneksi.php";
    
    if(isset($_GET['id'])){
        $id = enkripsiDekripsi(antiSQLi($_GET['id']), 'dekripsi');
    }else{
        echo "<center></center>";
        exit();
    }
    
    $data_transaksi = $db->query("SELECT A.id_photobooth_transaksi, A.tanggal_photobooth_transaksi, A.nama_cust, A.telp_cust, A.jumlah_tiket, A.nominal_sebelum_diskon, A.diskon, A.total_transaksi, A.bayar, A.status_transaksi, B.nama_admin, C.nama_shift, D.nama_jenis_pembayaran FROM photobooth_transaksi A JOIN admin B ON A.kd_admin=B.id_admin JOIN shift C ON A.kd_shift=C.id_shift JOIN jenis_pembayaran D ON A.kd_jenis_pembayaran=D.id_jenis_pembayaran WHERE A.id_photobooth_transaksi='$id'")->fetch_assoc();
    
    $data_detail = $db->query("SELECT A.id_photobooth_tiket, A.no_urut, A.jumlah_tiket, A.harga_satuan, B.nama_photobooth_stan FROM photobooth_tiket A JOIN photobooth_stan B ON A.kd_photobooth_stan=B.id_photobooth_stan WHERE kd_photobooth_transaksi='$id'")->fetch_all(MYSQLI_ASSOC);
    
?>


<table style="border-collapse: collapse; width: 100%">
	<thead>
		<tr>
			<td>Kasir</td>
			<td> : </td>
			<td><?= $data_transaksi['nama_admin'] ?></td>
		</tr>
		<tr>
			<td>Tanggal</td>
			<td> : </td>
			<td><?= tanggal_jam_indo($data_transaksi['tanggal_photobooth_transaksi']) ?></td>
		</tr>
		<tr>
			<td>Pembeli</td>
			<td> : </td>
			<td><?= $data_transaksi['nama_cust'] ?></td>
		</tr>
		<tr>
			<td>Telp. Pembeli</td>
			<td> : </td>
			<td><?= $data_transaksi['telp_cust'] ?></td>
		</tr>
		<tr>
			<td>Total Tiket</td>
			<td> : </td>
			<td><?= number_format($data_transaksi['jumlah_tiket']) ?></td>
		</tr>
		<tr>
			<td>Total</td>
			<td> : </td>
			<td>Rp <?= number_format($data_transaksi['nominal_sebelum_diskon']) ?></td>
		</tr>
		<tr>
			<td>Diskon</td>
			<td> : </td>
			<td>Rp <?= number_format($data_transaksi['diskon']) ?></td>
		</tr>
		<tr>
			<td>Total Akhir</td>
			<td> : </td>
			<td>Rp <?= number_format($data_transaksi['total_transaksi']) ?></td>
		</tr>
		<tr>
			<td>Cetak Bill</td>
			<td> : </td>
			<td>
			    <a href="view/transaksi_photobooth/proses_data.php?cetak_ulang_bill=<?= enkripsiDekripsi($data_transaksi['id_photobooth_transaksi'],'enkripsi') ?>" class="btn btn-sm btn-outline-info  p-0 pl-1 pr-1"><i class="fas fa-print"></i> Cetak Bill</a>
			</td>
		</tr>
	</thead>
</table>

<hr>
<center>RINCIAN</center>
<hr>

<?php $no = 1; ?>
<?php foreach($data_detail as $key => $value): ?>
    <div class="mb-2" style="padding: 1px 5px; border: 1px solid #ebebeb; border-radius: 8px">
    	<table style="width: 100%">
    		<tbody>
    			<tr>
    			    <td align="center" rowspan="4" style="border-right:1px solid #ebebeb;">
    			        <b><?= $no ?></b><br>
    			        <?php if ($value['no_urut'] > 0): ?>
	    			        <a href="view/transaksi_photobooth/proses_data.php?cetak_ulang_tiket_satuan=<?= enkripsiDekripsi($value['id_photobooth_tiket'],'enkripsi') ?>" class="btn btn-sm btn-outline-info p-0 pl-1 pr-1"><i class="fas fa-print"></i></a>
	    			    <?php else: ?>
	    			        <a class="btn btn-sm btn-outline-secondary p-0 pl-1 pr-1"><i class="fas fa-print"></i></a>
						<?php endif ?>
    			    </td>
    				<td colspan="2" class="pl-2"><?= $value['nama_photobooth_stan'] ?></td>
    			</tr>
    			<tr>
    				<td colspan="2" class="pl-2">Nomor Urut : <b><?= $value['no_urut'] ?></b></td>
    			</tr>
    			<tr>
    				<td colspan="2" class="pl-2">Harga Satuan : <?= number_format($value['harga_satuan']) ?></td>
    			</tr>
    			<tr>
    				<td colspan="2" class="pl-2">Dapat digunakan untuk <?= $value['jumlah_tiket'] ?> Orang</td>
    			</tr>
    		</tbody>
    	</table>
    </div>
<?php $no++; ?>
<?php endforeach; ?>