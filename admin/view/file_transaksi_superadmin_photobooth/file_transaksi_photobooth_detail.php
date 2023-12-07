<?php 
    include "../../templates/koneksi.php";
    
    if(isset($_GET['id'])){
        $id = enkripsiDekripsi(antiSQLi($_GET['id']), 'dekripsi');
    }else{
        echo "<center></center>";
        exit();
    }
    
    $data_transaksi = $db->query("SELECT A.id_photoboothambil_transaksi, A.tanggal_photoboothambil_transaksi, A.nama_cust, A.telp_cust, A.jumlah_tiket, A.nominal_sebelum_diskon, A.diskon, A.total_transaksi, A.bayar, A.status_transaksi, B.nama_admin, C.nama_shift, D.nama_jenis_pembayaran FROM photoboothambil_transaksi A JOIN admin B ON A.kd_admin=B.id_admin JOIN shift C ON A.kd_shift=C.id_shift JOIN jenis_pembayaran D ON A.kd_jenis_pembayaran=D.id_jenis_pembayaran WHERE A.id_photoboothambil_transaksi='$id'")->fetch_assoc();
    
    $data_detail = $db->query("SELECT A.id_photoboothambil_tiket, A.no_urut, A.jumlah_tiket, A.harga_satuan, B.nama_photoboothambil_stan FROM photoboothambil_tiket A JOIN photoboothambil_stan B ON A.kd_photoboothambil_stan=B.id_photoboothambil_stan WHERE kd_photoboothambil_transaksi='$id'")->fetch_all(MYSQLI_ASSOC);
    
?>

<?php if($data_transaksi['status_transaksi'] == "2") : ?>
    <?php $data_revisi = $db->query("SELECT * FROM photoboothambil_revisi_transaksi A JOIN admin B ON A.kd_admin=B.id_admin WHERE kd_transaksi_ambil='$id'")->fetch_assoc(); ?>
    <center><span class="text-red">Transaksi Dibatalkan Oleh <?= @$data_revisi['nama_admin'] ?>, karena <?= @$data_revisi['keterangan_revisi_ambil'] ?></span></center>
    <hr>
<?php endif; ?>

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
			<td><?= tanggal_jam_indo($data_transaksi['tanggal_photoboothambil_transaksi']) ?></td>
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
		<?php if($data_transaksi['status_transaksi'] == "1") : ?>
		<tr>
			<td>Batalkan Transaksi</td>
			<td> : </td>
			<td>
			    <button class="btn btn-sm btn-danger p-0 pl-1 pr-1 btn_batalkan">Batalkan Transaksi</button>
			</td>
		</tr>
		<?php endif; ?>
	</thead>
</table>

<form id="frm_pembatalan" action="view/file_transaksi_superadmin_photobooth/proses_data.php" class="mb-5 pb-3" method="POST" style="display:none;">
    <hr>
    <div class="form-group">
        <label>Alasan Pembatalan</label>
        <input type="hidden" name="batalkan_transaksi" value="<?= enkripsiDekripsi($data_transaksi['id_photoboothambil_transaksi'],'enkripsi') ?>">
        <textarea row="3" class="form-control" name="alasan" required></textarea>
    </div>
    <button type="submit" class="btn btn-danger float-right btn-sm">Batalkan</button>
</form>

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
    			    </td>
    				<td colspan="2" class="pl-2"><?= $value['nama_photoboothambil_stan'] ?></td>
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

<script>
    $(".btn_batalkan").click(function(){
        $("#frm_pembatalan").toggle(200);
    })
</script>
