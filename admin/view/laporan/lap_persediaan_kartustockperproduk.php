<?php

$list_merchant = $db->query(" SELECT * FROM merchant ")->fetch_all(MYSQLI_ASSOC); 

?>

<link rel="stylesheet" href="plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark">Kartu Stock Per Produk Semua Stall</h1>
			</div>
		</div>
	</div>
</div>

<section class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="container p-3">
							<div class="form-group">
								<div class="row">
									<div class="col-md-6">
										<label>Tanggal Awal</label>
										<input type="date" class="form-control" id="tgl_awal" name="tgl_awal" value="<?=date('Y-m-d')?>" required="">
									</div>
									<div class="col-md-6">
										<label>Tanggal Akhir</label>
										<input type="date" class="form-control" id="tgl_akhir" name="tgl_akhir" value="<?=date('Y-m-d')?>" required="">
									</div>
								</div>
								<div class="form-group mt-2">
									<label>Pilih Merchant</label>
									<select class="form-control select2bs4" name="id_merchant" id="id_merchant" required="" style="width: 100%">
										<option value="">Pilih Merchant</option>
										<?php foreach ($list_merchant as $key => $value): ?>
											<option value="<?= enkripsiDekripsi($value['id_merchant'], 'enkripsi') ?>"><?= $value['nama_merchant'] ?></option>
										<?php endforeach ?>
									</select>
								</div>
								<div class="form-group mt-2">
									<label>Pilih Produk</label>
									<div class="spinner-border spinner-border-sm" role="status" id="spinner-produk" style="display: none"></div>
									<select class="form-control select2bs4" name="produk" id="id_produk" required="" style="width: 100%">
										<option value="">Pilih Produk</option>
									</select>
								</div>
								<button type="submit" name="filter" id="filter" class="btn btn-primary btn-block">Terapkan</button>
							</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<!-- <a onclick="exportexcel('<?= $tgl_awal ?>', '<?= $tgl_akhir ?>')">
							<button type="button" class="btn btn-default" style="width: 100%"><i class="fa fa-fw fa-file-download"></i>Eksport Excel</button>
						</a> -->
					</div>
					<div class="card-body">
						<table id="example" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>Tanggal</th>
									<th>Keterangan</th>
									<th>Bukti</th>
									<th>Masuk</th>
									<th>Keluar</th>
									<th>Saldo</th>
								</tr>
							</thead>
							<tbody id="list_kartu_order">
							</tbody>
							<tfoot id="total_list">
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<script src="plugins/select2/js/select2.full.min.js"></script>
<script type="text/javascript">
  //Initialize Select2 Elements
  $('.select2bs4').select2({
  	theme: 'bootstrap4'
  })
</script>

<script type="text/javascript">
	//$("#id_merchant").change(function(){
		//var val = $(this).val()
		//$.ajax({
		//	url: '<?= base_url() ?>view/ajax_get_barang_html.php',
			//type: 'POST',
			//data: {id_merchant: val},
			//success:function(result){
				// alert(result);
			//	$("#id_produk").html(result)
			//}
		//})
	//})
	
	$('#filter').click(function(){
	   // alert();z
	   var tgl_awal = $('#tgl_awal').val()
	   var tgl_akhir = $('#tgl_akhir').val()
	   var id_merchant = $('#id_merchant').val()
	   var id_produk = $('#id_produk').val()
	   alert(id_merchant)
	   if(id_merchant == ""){
	       alert('pilih Merchant Terlebih Dahulu')
	   }else if(id_produk == ""){
	       alert('pilih Merchant Terlebih Dahulu')
	   }else{
    	   $.ajax({
    	        url: '<?= base_url() ?>view/ajax_kartu_stock.php',
    			type: 'POST',
    			data: {
    			    'filter'        : true,
    			    'tgl_awal'      : tgl_awal,
    			    'tgl_akhir'     : tgl_akhir,
    			    'id_merchant'   : id_merchant,
    			    'id_produk'     : id_produk
    			},
    			dataType : 'json',
    		    success : function(result){
    			 //   console.log(result)
    			    var html= "";
    			 //   var tfoot= "";
    			    var total_masuk =0;
    			    var total_keluar =0;
    			    var total_saldo =0;
                    result.forEach(function(rowData) {
                        total_masuk     += parseInt(rowData.masuk)
                        total_keluar    += parseInt(rowData.keluar)
                        total_saldo     = parseInt(rowData.stok_setelah)
                        html += `
                                <tr>
                                    <td>`+rowData.tanggal_history+`</td>
                                    <td>`+rowData.keterangan+`</td>
                                    <td>`+rowData.id_referensi+`</td>
                                    <td>`+rowData.masuk+`</td>
                                    <td>`+rowData.keluar+`</td>
                                    <td>`+rowData.stok_setelah+`</td>
                                </tr>
                                `
                    });
                    tfoot = `
                                <tr>
                                    <th class="text-center" colspan="3">Total</th>
                                    <th>`+total_masuk+`</th>
                                    <th>`+total_keluar+`</th>
                                    <th>`+ (total_saldo) +`</th>
                                </tr>
                                `
                    $('#list_kartu_order').html(html);
                    $('#total_list').html(tfoot);
    			}
    	   })
	   }
	})

  function exportexcel(awal, akhir){
  	var form = $('<form target="_blank" action="view/laporan/lap_tiket_export_excel.php" method="post">' +
  		'<input type="hidden" name="awal" value="' + awal + '" />' +
  		'<input type="hidden" name="akhir" value="' + akhir + '" />' +
  		'</form>');
  	$('body').append(form);
  	form.submit();
	// alert(awal+'    '+akhir);
  }
</script>