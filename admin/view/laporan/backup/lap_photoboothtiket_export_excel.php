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
		$total_diskon = $db->query("SELECT SUM(diskon) as jml FROM photobooth_transaksi WHERE status_transaksi!='2' AND DATE(tanggal_photobooth_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir'")->fetch_assoc()['jml'];

		$sql = mysqli_query($db,"

			SELECT 

			C.nama_photobooth_stan AS nama_jenis_tiket, 

			(SELECT D.harga_photobooth_stan FROM photobooth_stan D WHERE D.id_photobooth_stan=A.kd_photobooth_stan) AS harga_satuan, 

			(SELECT SUM(E.jumlah_tiket) FROM photobooth_tiket E JOIN photobooth_transaksi F ON E.kd_photobooth_transaksi=F.id_photobooth_transaksi WHERE  F.status_transaksi!='3' AND E.kd_photobooth_stan=A.kd_photobooth_stan AND DATE(F.tanggal_photobooth_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir') AS jumlah_tiket

			FROM photobooth_tiket A JOIN photobooth_transaksi B ON A.kd_photobooth_transaksi=B.id_photobooth_transaksi JOIN photobooth_stan C ON A.kd_photobooth_stan=C.id_photobooth_stan WHERE B.status_transaksi!='3' AND DATE(B.tanggal_photobooth_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir' GROUP BY A.kd_photobooth_stan

			");


		while($query = mysqli_fetch_array($sql)) {
			$grand_total = $grand_total + ($query['harga_satuan'] * $query['jumlah_tiket']);
			?>
			<tr>
				<td> <?php echo $nomor++; ?> </td>
				<td> <?php echo $query['nama_jenis_tiket']; ?> </td>
				<td> <?php echo number_format($query['harga_satuan']); ?> </td>
				<td> <?php echo number_format($query['jumlah_tiket']); ?> </td>
				<td> <?php echo number_format($query['harga_satuan'] * $query['jumlah_tiket']); ?> </td>
				<!--<td> <?php echo number_format($query['total_diskon']); ?> </td>-->
				<!--<td> <?php echo number_format($query['total_transaksi']); ?> </td>-->
                    <!-- <td align="center"> 
                      <a href="javascript:void(0);" data-href="view/akun_kelola_detail.php?eid=<?php echo $eid; ?>" class="openPopup" data-toggle="tooltip" title="Detail">
                        <button class ="btn btn-success btn-xm" style="margin: 3px"><i class="fa fa-fw fa-file-alt"></i></button>
                      </a>
                  </td> -->
              </tr>
          <?php } ?>
      </tbody>
      <tfoot>
      	<tr>
      		<td colspan="4" align="right">Diskon</td>
      		<td> <?php echo number_format($total_diskon); ?> </td>
      	</tr>
      	<tr>
      		<td colspan="4" align="right">Total</td>
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
