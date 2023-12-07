<?php 
	//die('sedang proses maintenance/perbaikan mohon bersabar');
    $list_merchant = $db->query("SELECT * FROM merchant WHERE status_aktif_merchant='Y' ")->fetch_all(MYSQLI_ASSOC);
	
	if(isset($_GET['tanggal'])){
    	$tanggal = date('Y-m-d', strtotime($_GET['tanggal']));
    }else{
      	$tanggal = date('Y-m-d');
    }


	if(isset($_GET['NmPro'])){
    	$like = "AND a.nama_produk LIKE '%$_GET[NmPro]%'";
    }else{
    	$like = "";
    }
	if(isset($_GET['merchant'])){
    	$id_merchant_dekrip = enkripsiDekripsi($_GET['merchant'], 'dekripsi');
    	
      	$list_produk = $db->query("
            SELECT a.*, b.nama_supplier, c.harga_beli, d.stok_saat_ini
            FROM merchant_produk a
            LEFT JOIN supplier b ON a.id_supplier = b.id_supplier
            JOIN merchant_history_stok c ON a.id_merchant_produk = c.kd_merchant_produk
            JOIN merchant_stok d ON a.id_merchant_produk = d.kd_merchant_produk
            WHERE a.kd_merchant = '$id_merchant_dekrip'
                AND a.jenis_produk = '2'
                AND a.status_remove_produk = 'N'
                AND a.status_display_produk = 'Y'
                $like
                AND c.jenis_history = '0'
           	ORDER BY c.tanggal_history DESC
            ")->fetch_all(MYSQLI_ASSOC);
      	
    //print_r("<pre>"); print_r($list_produk); die();
    }
	
?>

<link rel="stylesheet" href="plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Laporan Posisi Sisa Persediaan Per Stall</h1>
      </div>
    </div>
  </div>
</div>

<section class="content">
  	<div class="container-fluid">
		<div class="card">
  	        <div class="card-body">
                <form action="" method="get" accept-charset="utf-8">
                    <input type="hidden" name="page" value="<?=$_GET['page']?>">
                  	<input type="hidden" name="action" value="<?=$_GET['action']?>">
                  	<div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <!--<input type="text" name="tanggal" class="form-control date">-->
                              	<input type="text" name="NmPro" class="form-control" value="<?= @$_GET['NmPro']?>" placeholder="Nama Produk">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <select name="merchant" class="select2bs4 form-control" style="width: 100%" required>
                                    <?php foreach($list_merchant as $key => $value){ ?>
                                        <?php $id_enkrip = enkripsiDekripsi($value['id_merchant'], 'enkripsi'); ?>
                                        <option value="<?= $id_enkrip ?>" <?= $id_enkrip == @$_GET['merchant'] ? 'selected' : null ?> ><?=$value['nama_merchant']?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                      	<div class="col-12">
                          <button type="submit" class="btn btn-primary btn-block">
                            <i class="fa fa-search"></i> Filter                          
                          </button>
                      	</div>
        			</div>
                </form>   
  			</div>
        </div>
    </div>
</section>

<section class="content">
  	<div class="container-fluid">
		<div class="card">
          	<div class="card-header">
          		<button id="btn_download" class="float-right mb-3"><i class="fas fa-download"></i></button>
          	</div>
  	        <div class="card-body" id="cetak">
              	<div class="table-responsive">
              		<table border="1" class="table table-bordered table-striped table2excel">
                      	<thead>
                        	<tr>
                              	<th>No</th>
                          		<th>Kode</th>
                          		<th>Nama Barang</th>
                                <th>Supplier</th>
                                <th>Harga Beli</th>
                                <th>Sisa Stok</th>
                                <th>Nilai Stok</th>
                                <th>Harga Jual</th>
                            </tr>
                        </thead>
                        <tbody>
                          <?php $no = 1; foreach($list_produk as $key => $value){ ?>
                          	<tr>
                              	<td> <?= $no++ ?></td>
                              	<td> <?= $value['kode_produk'] ?></td>
                              	<td> <?= $value['nama_produk'] ?></td>
                              	<td> <?= $value['nama_supplier'] == "" ? '-' : $value['nama_supplier'] ?> </td>
                                <td> <?= $value['harga_beli'] ?></td>	
                                <td> <?= $value['stok_saat_ini'] ?></td>	
                                <td> <?= $value['harga_produk'] ?></td>	
                                <td> <?= $value['harga_produk'] * $value['stok_saat_ini'] ?></td>
                          	</tr>
                            <?php } ?>
                      	</tbody>
                  	</table>
              	</div>
  			</div>
        </div>
    </div>
</section>

<script src="plugins/select2/js/select2.full.min.js"></script>

<script type="text/javascript">
  	//Initialize Select2 Elements
  	$(function () {
  		$('.select2bs4').select2({
    		theme: 'bootstrap4'
  		})
        
        $('.date').daterangepicker({
    		locale: {
          		format: 'D-MM-YYYY',
    		},
    		singleDatePicker: true,
    		showDropdowns: true
		});
      
      	$("#btn_download").click(function(e) {
          // window.open('data:application/vnd.ms-excel,' + encodeURIComponent( $('#cetak').html()));
          // e.preventDefault();
          var result = 'data:application/vnd.ms-excel,' + encodeURIComponent($('#cetak').html());
          var link = document.createElement("a");
          document.body.appendChild(link);
          link.download = "Laporan Pendapatan Harian Tiket Masuk (<?= date_format(date_create($tanggal_mulai), 'd-M-Y') ?> Sampai <?= date_format(date_create($tanggal_akhir), 'd-M-Y') ?>).xls"; //You need to change file_name here.
          link.href = result;
          link.click();

      });
  	})
  
</script>
