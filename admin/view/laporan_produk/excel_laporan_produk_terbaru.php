<?php 

	header("Content-type: application/vnd-ms-excel");
	header("Content-Disposition: attachment; filename=laporan_kasir.xls");

	require_once '../../templates/koneksi.php';
	sessionLoginMerchantEmployee();


	 $query = "	SELECT * FROM merchant_produk
	 			LEFT JOIN merchant_kategori_produk ON merchant_produk.kd_merchant_kategori = 			merchant_kategori_produk.id_merchant_kategori_produk
	 			WHERE status_display_produk='Y' 
	 			ORDER BY id_merchant_produk DESC";
    $data_produk = $db->query($query)->fetch_all(MYSQLI_ASSOC);
	
 ?>


 <div class="table-responsive">
	<table class="table table-bordered table-striped" id="example" border="1">
		<thead>
			<tr>
				<th>No</th>
				<th>Kategory</th>
				<th>Nama Product</th>
				<th>Harga</th>
				<!-- <th>Gambar</th> -->
			</tr>
		</thead>
		<tbody>
			<?php $nomor=1; ?>
        <?php foreach($data_produk as $key => $val) : ?>
				<tr>
					<td><?= $nomor++ ?></td>
					<td><?= $val['nama_kategori'] ?></td>
					<td><?= $val['nama_produk'] ?></td>
					<td><?= number_format($val['harga_produk']) ?></td>
					<!-- <td>
						<?php if($val['gambar_produk'] != null  ) { ?>
                      	<img src="<?= base_url().'/assets/product/'.$val['gambar_produk'] ?>" style="width: 30px">
                    <?php } ?>
					</td> -->
				</tr>
        <?php $nomor++; endforeach; ?>
		</tbody>
	</table>
</div>

