<?php 
    $sess_kd_merchant = $_SESSION['kd_merchant'];
	 $query = "	SELECT * FROM merchant_produk A
	 			JOIN merchant_kategori_produk ON A.kd_merchant_kategori = merchant_kategori_produk.id_merchant_kategori_produk
	 			WHERE status_display_produk='Y'
	 			    AND status_remove_produk='N'
	 			    AND A.kd_merchant = '$sess_kd_merchant'
	 			ORDER BY id_merchant_produk DESC";
    $data_produk = $db->query($query)->fetch_all(MYSQLI_ASSOC);
	
 ?>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-12">
        <h1 class="m-0 text-dark">Laporan Product Terbaru</h1>
        <button type="button" id="export_excel" class="btn btn-sm btn-success float-right"><i class="fas fa-print"></i> Export</button>
      </div>
    </div>
  </div>
</div>

<section class="content">
  	<div class="container-fluid">
    	<div class="row">
      		<div class="col-12">
        		<div class="card">
          			<div class="card-header">
          				<div class="table-responsive">
          					<table class="table table-bordered table-striped" id="example" >
          						<thead>
          							<tr>
          								<th>No</th>
          								<th>Kategory</th>
          								<th>Nama Product</th>
          								<th>Harga</th>
          								<th>Gambar</th>
          							</tr>
          						</thead>
          						<tbody>
          							<?php $nomor=1; ?>
					                <?php foreach($data_produk as $key => $val) : ?>
	          							<tr>
	          								<td><?= $nomor ?></td>
	          								<td><?= $val['nama_kategori'] ?></td>
	          								<td><?= $val['nama_produk'] ?></td>
	          								<td><?= number_format($val['harga_produk']) ?></td>
	          								<td>
	          									<?php if($val['gambar_produk'] != null  ) { ?>
                                                  <img src="<?= base_url().'/dist/img/barang/'.$val['gambar_produk'] ?>" style="width: 100px">
                                                <?php } ?>
                                                
                                                <?php if($val['gambar_produk'] == null  ) { ?>
                                                    <img src="<?= base_url().'/dist/img/barang/no_picture.jpg' ?>" style="width: 100px">
                                                <?php } ?>
	          								</td>
	          							</tr>
					                <?php $nomor++; endforeach; ?>
          						</tbody>
          					</table>
          				</div>
          			</div>
        		</div>
        	</div>
      	</div>
    </div>
</section>

<script>
  $(function() { 
    $("#export_excel").click(function() {
      // var mulai = "<?= @$mulai ?>"
      // var akhir = "<?= @$akhir ?>"
      // var nama_kasir = "<?= @$nama_kasir ?>"
      // var id_kasir = "<?= @$id_kasir ?>"

      window.open('view/laporan_produk/excel_laporan_produk_terbaru.php', '__blank');

    }); 
  });
</script>