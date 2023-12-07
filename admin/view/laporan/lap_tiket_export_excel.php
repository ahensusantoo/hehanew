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
?>

<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script> -->
<script src="../../plugins/export-excel/src/jquery.table2excel.js"></script>
<input type="hidden" name="namafile" id="namafile" value="<?= 'Laporan Penjualan Tiket '.tanggal_indo($tgl_awal).' - '.tanggal_indo($tgl_akhir); ?>">

<table class="table2excel" data-tableName="Test Table 1">
	<tr>
		<th colspan="7"><?= 'Laporan Penjualan Tiket '.tanggal_indo($tgl_awal).' - '.tanggal_indo($tgl_akhir); ?></th>
	</tr>
	<thead>
		<tr>
			<th>No</th>
			<th>Tiket</th>
			<th>Harga</th>
			<th>Jumlah</th>
			<th>Sub Total</th>
			<th>Diskon</th>
			<th>Sub Total Final</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$nomor=1;
		$grand_total = 0;
		$sql = mysqli_query($db,"SELECT B.kd_jenis_tiket, C.nama_jenis_tiket, B.harga_satuan, 
			(SELECT SUM(jumlah_tiket) FROM view_transaksi_join_tiket_orderby_id WHERE status_transaksi!='3' AND kd_jenis_tiket=B.kd_jenis_tiket AND DATE(tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS jumlah_tiket, 
			(SELECT SUM(nominal_sebelum_diskon) FROM view_transaksi_join_tiket_orderby_id WHERE status_transaksi!='3' AND kd_jenis_tiket=B.kd_jenis_tiket AND DATE(tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS total_harga_satuan,
			(SELECT SUM(diskon) FROM view_transaksi_join_tiket_orderby_id WHERE status_transaksi!='3' AND kd_jenis_tiket=B.kd_jenis_tiket AND DATE(tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS total_diskon,
			(SELECT SUM(total_transaksi) FROM view_transaksi_join_tiket_orderby_id WHERE status_transaksi!='3' AND kd_jenis_tiket=B.kd_jenis_tiket AND DATE(tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS total_transaksi 
			FROM transaksi A 
			JOIN tiket B ON A.id_transaksi=B.kd_transaksi 
			JOIN jenis_tiket C ON B.kd_jenis_tiket=C.id_jenis_tiket 
			WHERE A.status_transaksi!='3' 
			AND DATE(A.tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' 
			GROUP BY B.kd_jenis_tiket");
		while($query = mysqli_fetch_array($sql)) {
			$grand_total = $grand_total + $query['total_transaksi'];
			?>
			<tr>
				<td> <?php echo $nomor++; ?> </td>
				<td> <?php echo $query['nama_jenis_tiket']; ?> </td>
				<td> <?php echo $query['harga_satuan']; ?> </td>
				<td> <?php echo $query['jumlah_tiket']; ?> </td>
				<td> <?php echo $query['total_harga_satuan']; ?> </td>
				<td> <?php echo $query['total_diskon']; ?> </td>
				<td> <?php echo $query['total_transaksi']; ?> </td>
			</tr>
		<?php } ?>
		<tr>
			<td colspan="6"></td>
			<td> <?php echo $grand_total; ?> </td>
		</tr>
	</tbody>

	<tr>
		<th>Rincian Pembayaran</th>
	</tr>
	<thead>
		<tr>
			<th>Jenis</th>
			<th>Nominal</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$nomor=1;
		$grand_total_jenpem = 0;
		$sql = mysqli_query($db,"SELECT SUM(A.total_transaksi) AS total_transaksi, B.nama_jenis_pembayaran
			FROM transaksi A 
			LEFT JOIN jenis_pembayaran B ON A.kd_jenis_pembayaran=B.id_jenis_pembayaran 
			WHERE A.status_transaksi!='3' 
			AND DATE(A.tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' 
			GROUP BY A.kd_jenis_pembayaran");
		while($query = mysqli_fetch_array($sql)) {
			$grand_total_jenpem = $grand_total_jenpem + $query['total_transaksi'];
			?>
			<tr>
				<td> <?php echo $query['nama_jenis_pembayaran']; ?> </td>
				<td> <?php echo $query['total_transaksi']; ?> </td>
			</tr>
		<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<td>Total</td>
			<td> <?php echo $grand_total_jenpem; ?> </td>
		</tr>
	</tfoot>
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
