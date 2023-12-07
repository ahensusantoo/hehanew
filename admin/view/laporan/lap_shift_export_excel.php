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
<input type="hidden" name="namafile" id="namafile" value="<?= 'Laporan Shift Penjualan '.tanggal_indo($tgl_awal).' - '.tanggal_indo($tgl_akhir); ?>">

<table class="table2excel" data-tableName="Test Table 1">
	<tr>
		<th colspan="4"><?= 'Laporan Shift Penjualan '.tanggal_indo($tgl_awal).' - '.tanggal_indo($tgl_akhir); ?></th>
	</tr>
	<thead>
		<tr>
			<th>No</th>
			<th>Shift</th>
			<th>Tiket</th>
			<th>Bruto</th>
			<th>Diskon</th>
			<th>Sub Total</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$nomor=1;
		$grand_total = 0;
		$sql = mysqli_query($db,"SELECT s.nama_shift, SUM(t.jumlah_tiket) AS jumlah_tiket, SUM(t.nominal_sebelum_diskon) AS bruto, SUM(t.diskon) AS diskon, SUM(t.total_transaksi) AS total_transaksi FROM `shift` AS s
			JOIN transaksi AS t ON s.id_shift = t.kd_shift
			WHERE (DATE(t.tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir')
			AND t.status_transaksi != '3'
			GROUP BY t.kd_shift");
		while($query = mysqli_fetch_array($sql)) {
			$grand_total = $grand_total + $query['total_transaksi'];
			?>
			<tr>
				<td> <?php echo $nomor++; ?> </td>
				<td> <?php echo $query['nama_shift']; ?> </td>
				<td> <?php echo $query['jumlah_tiket']; ?> </td>
				<td> <?php echo $query['bruto']; ?> </td>
				<td> <?php echo $query['diskon']; ?> </td>
				<td> <?php echo $query['total_transaksi']; ?> </td>
			</tr>
		<?php } ?>
		<tr>
			<td colspan="5"></td>
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
				<td> <?php echo number_format($query['total_transaksi']); ?> </td>
			</tr>
		<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<td>Total</td>
			<td> <?php echo number_format($grand_total_jenpem); ?> </td>
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
