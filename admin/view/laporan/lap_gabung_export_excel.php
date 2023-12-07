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
$array_data_shift=[];
$cek_shift = mysqli_query($db,"SELECT nama_shift, id_shift FROM shift WHERE status_aktif_shift = 'Y' ORDER BY nama_shift");
while($data_shift = mysqli_fetch_array($cek_shift)) {
	array_push($array_data_shift, (object)[
		'id' => $data_shift['id_shift'],
		'nama' => $data_shift['nama_shift'],
	]);
}
$array_data_shift_length = count($array_data_shift);
$colspan_bawah = 5+($array_data_shift_length*2);
?>

<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script> -->
<script src="../../plugins/export-excel/src/jquery.table2excel.js"></script>
<input type="hidden" name="namafile" id="namafile" value="<?= 'Laporan Gabungan Tiket Shift '.tanggal_indo($tgl_awal).' - '.tanggal_indo($tgl_akhir); ?>">

<table class="table2excel" data-tableName="Test Table 1">
	<tr>
		<th colspan="<?= $colspan_bawah+2 ?>"><?= 'Laporan Gabungan Tiket Shift '.tanggal_indo($tgl_awal).' - '.tanggal_indo($tgl_akhir); ?></th>
	</tr>
	<thead>
		<tr>
			<th rowspan="2" style="vertical-align: middle;">No</th>
			<th rowspan="2" style="vertical-align: middle;">Tiket</th>
			<th rowspan="2" style="vertical-align: middle;">Harga</th>
			<?php
			foreach($array_data_shift as $value){
				?>
				<th colspan="3">Shift <?= $value->nama?></th>
				<?php
			}
			?>
			<th rowspan="2" style="vertical-align: middle;">Sub Qty</th>
			<th rowspan="2" style="vertical-align: middle;">Omzet Bersih</th>
		</tr>
		<tr>
			<?php
			foreach($array_data_shift as $value){
				?>
				<th>Qty</th>
				<th>Diskon</th>
				<th>Jumlah</th>
				<?php
			}
			?>
			<!-- <th data-searchable="false" data-orderable="false">Kelola</th> -->
		</tr>
	</thead>
	<tbody>
		<?php
		$nomor=1;
		$grand_total_qty = 0;
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
			$kd_jenis_tiket_temp = $query['kd_jenis_tiket'];
			$grand_total = $grand_total + $query['total_transaksi'];
			$grand_total_qty = $grand_total_qty + $query['jumlah_tiket'];
			?>
			<tr>
				<td> <?php echo $nomor++; ?> </td>
				<td> <?php echo $query['nama_jenis_tiket']; ?> </td>
				<td> <?php echo $query['harga_satuan']; ?> </td>
				<?php
				foreach($array_data_shift as $value){
					$ambil_pershift = mysqli_query($db,"SELECT 
						(SELECT SUM(jumlah_tiket) FROM view_transaksi_join_tiket_orderby_id WHERE status_transaksi!='3' AND kd_jenis_tiket='$kd_jenis_tiket_temp' AND DATE(tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND kd_shift = '$value->id') AS jumlah_tiket, 
						(SELECT SUM(diskon) FROM view_transaksi_join_tiket_orderby_id WHERE status_transaksi!='3' AND kd_jenis_tiket='$kd_jenis_tiket_temp' AND DATE(tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND kd_shift = '$value->id') AS total_diskon,
						(SELECT SUM(total_transaksi) FROM view_transaksi_join_tiket_orderby_id WHERE status_transaksi!='3' AND kd_jenis_tiket='$kd_jenis_tiket_temp' AND DATE(tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND kd_shift = '$value->id') AS total_transaksi");
					$data_pershift = mysqli_fetch_array($ambil_pershift);
					?>
					<td><?= $data_pershift['jumlah_tiket']?></td>
					<td><?= $data_pershift['total_diskon']?></td>
					<td><?= $data_pershift['total_transaksi']?></td>
					<?php
				}
				?>
				<td> <?php echo $query['jumlah_tiket']; ?> </td>
				<td> <?php echo $query['total_transaksi']; ?> </td>
			</tr>
		<?php } ?>
	</tbody>
	<tr>
		<td colspan="3"></td>
		<?php
		foreach($array_data_shift as $value){
			$ambil_pershift_total = mysqli_query($db,"SELECT 
				(SELECT SUM(jumlah_tiket) FROM view_transaksi_join_tiket_orderby_id WHERE status_transaksi!='3' AND DATE(tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND kd_shift = '$value->id') AS jumlah_tiket, 
				(SELECT SUM(diskon) FROM view_transaksi_join_tiket_orderby_id WHERE status_transaksi!='3' AND DATE(tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND kd_shift = '$value->id') AS total_diskon,
				(SELECT SUM(total_transaksi) FROM view_transaksi_join_tiket_orderby_id WHERE status_transaksi!='3' AND DATE(tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' AND kd_shift = '$value->id') AS total_transaksi ");
			$data_pershift_total = mysqli_fetch_array($ambil_pershift_total);
			?>
			<td><?= $data_pershift_total['jumlah_tiket']?></td>
			<td><?= $data_pershift_total['total_diskon']?></td>
			<td><?= $data_pershift_total['total_transaksi']?></td>
			<?php
		}
		?>
		<td> <?php echo $grand_total_qty; ?></td>
		<td> <?php echo $grand_total; ?></td>
	</tr>

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
