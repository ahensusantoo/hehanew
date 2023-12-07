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
$colspan_bawah = 3+($array_data_shift_length*2);

$total_diskon = $db->query("SELECT SUM(diskon) as jml FROM photobooth_transaksi WHERE status_transaksi!='2' AND DATE(tanggal_photobooth_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir'")->fetch_assoc()['jml'];
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
				<th colspan="2">Shift <?= $value->nama?></th>
				<?php
			}
			?>
			<th rowspan="2" style="vertical-align: middle;">Sub Qty</th>
			<th rowspan="2" style="vertical-align: middle;">Sub Total</th>
		</tr>
		<tr>
			<?php
			foreach($array_data_shift as $value){
				?>
				<th>Qty</th>
				<th>Jumlah</th>
				<?php
			}
			?>
		</tr>
	</thead>
	<tbody>
		<?php
		$nomor=1;
		$grand_total_qty = 0;
		$grand_total = 0;

		$sql = mysqli_query($db,"SELECT 
			C.nama_photobooth_stan AS nama_jenis_tiket, A.kd_photobooth_stan,

			(SELECT D.harga_photobooth_stan FROM photobooth_stan D WHERE D.id_photobooth_stan=A.kd_photobooth_stan) AS harga_satuan, 

			(SELECT SUM(E.jumlah_tiket) FROM photobooth_tiket E JOIN photobooth_transaksi F ON E.kd_photobooth_transaksi=F.id_photobooth_transaksi WHERE  F.status_transaksi!='3' AND E.kd_photobooth_stan=A.kd_photobooth_stan AND DATE(F.tanggal_photobooth_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS jumlah_tiket,

			(SELECT SUM(H.diskon) FROM photobooth_tiket G JOIN photobooth_transaksi H ON G.kd_photobooth_transaksi=H.id_photobooth_transaksi WHERE  H.status_transaksi!='3' AND G.kd_photobooth_stan=A.kd_photobooth_stan AND DATE(H.tanggal_photobooth_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS total_diskon,

			(SELECT SUM(I.harga_satuan) FROM photobooth_tiket I JOIN photobooth_transaksi J ON I.kd_photobooth_transaksi=J.id_photobooth_transaksi WHERE  J.status_transaksi!='3' AND I.kd_photobooth_stan=A.kd_photobooth_stan AND DATE(J.tanggal_photobooth_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS total_transaksi

			FROM photobooth_tiket A JOIN photobooth_transaksi B ON A.kd_photobooth_transaksi=B.id_photobooth_transaksi JOIN photobooth_stan C ON A.kd_photobooth_stan=C.id_photobooth_stan WHERE B.status_transaksi!='3' AND DATE(B.tanggal_photobooth_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' GROUP BY A.kd_photobooth_stan
			");

		while($query = mysqli_fetch_array($sql)) {
			$kd_photobooth_stan_temp = $query['kd_photobooth_stan'];
			$grand_total = $grand_total + $query['total_transaksi'];
			$grand_total_qty = $grand_total_qty + $query['jumlah_tiket'];
			?>
			<tr>
				<td> <?php echo $nomor++; ?> </td>
				<td> <?php echo $query['nama_jenis_tiket']; ?> </td>
				<td> <?php echo number_format($query['harga_satuan']); ?> </td>
				<?php
				foreach($array_data_shift as $value){
					$ambil_pershift = mysqli_query($db,"SELECT 

						(SELECT SUM(A.jumlah_tiket) FROM photobooth_tiket A 
						JOIN photobooth_transaksi B ON A.kd_photobooth_transaksi=B.id_photobooth_transaksi 
						WHERE  B.status_transaksi!='3' 
						AND A.kd_photobooth_stan='$kd_photobooth_stan_temp'
						AND B.kd_shift='$value->id'
						AND DATE(B.tanggal_photobooth_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS jumlah_tiket,

						(SELECT SUM(C.harga_satuan) FROM photobooth_tiket C 
						JOIN photobooth_transaksi D ON C.kd_photobooth_transaksi=D.id_photobooth_transaksi 
						WHERE  D.status_transaksi!='3' 
						AND C.kd_photobooth_stan='$kd_photobooth_stan_temp'
						AND D.kd_shift='$value->id'
						AND DATE(D.tanggal_photobooth_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS total_transaksi
						FROM photobooth_transaksi 
						LIMIT 1");
					$data_pershift = mysqli_fetch_array($ambil_pershift);
					?>
					<td><?= number_format($data_pershift['jumlah_tiket'])?></td>
					<td><?= number_format($data_pershift['total_transaksi'])?></td>
					<?php
				}
				?>
				<td> <?php echo number_format($query['jumlah_tiket']); ?> </td>
				<td> <?php echo number_format($query['total_transaksi']); ?> </td>
			</tr>
		<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="<?= $colspan_bawah ?>"></td>
			<td> <?php echo number_format($grand_total_qty); ?> </td>
			<td> <?php echo number_format($grand_total); ?> </td>
		</tr>
		<tr>
			<td colspan="<?= $colspan_bawah?>"></td>
			<td> Diskon </td>
			<td> <?php echo number_format($total_diskon); ?> </td>
		</tr>
		<tr>
			<td colspan="<?= $colspan_bawah?>"></td>
			<td> Total Akhir </td>
			<td> <?php echo number_format($grand_total - $total_diskon); ?> </td>
		</tr>
	</tfoot>

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
			FROM photobooth_transaksi A 
			LEFT JOIN jenis_pembayaran B ON A.kd_jenis_pembayaran=B.id_jenis_pembayaran 
			WHERE A.status_transaksi!='3' 
			AND DATE(A.tanggal_photobooth_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' 
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
	<tr>
		<td>Total</td>
		<td> <?php echo number_format($grand_total_jenpem); ?> </td>
	</tr>
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
