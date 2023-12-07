File Manager
Search
 Upload
 New Item
 Andi
File "php upload gambar.php"

Full path: C:/xampp/htdocs/php upload gambar.php
File size: 2.87 B
MIME-type: text/x-php
Charset: utf-8
 Download    Open    Edit    Advanced Editor    Back

<?php

// INPUT THUMBNAIL
	if (isset($_FILES['thumbnail'])) {

		// FORMAT DIIZINKAN
	    $format_diizinkan["image/jpeg"] 		= "";
	    $format_diizinkan["image/jpg"] 			= "";
	    // END FORMAT DIIZINKAN

		if (isset($format_diizinkan[$_FILES['thumbnail']['type']])){

			$nama_file 	= $_FILES['thumbnail']['name'];
			$lokasi 	= $_FILES['thumbnail']['tmp_name'];
			$nama_file 	= substr(md5(rand()), 0, 10).$nama_file;
			
			if(move_uploaded_file($lokasi, "../../assets/pengumuman/".$nama_file)){
			    // berhasil upload
			}else{
			    // gagal upload
			}


		}else{
    		// Format tidak diperbolehkan
		}

	}








// BULAN ABJAD

<?php
$tgl = "2020-12-07 22:15:15";
$bulan = date_format(date_create($tgl),"m");


function bulanAbjad($bulan){
  switch ($bulan) {
    case 1:
      return 'Januari';
      break;
    case 2:
      return 'Februari';
      break;
    case 12:
      return 'Desember';
      break;


    default:
      return 'Bulan tidak terdaftar';
  }
}

echo bulanAbjad($bulan);

?>  


// SELECT BLABLA
SELECT D.nama_produk, (SELECT SUM(B.jumlah_produk) FROM merchant_transaksi_detail B JOIN merchant_produk C ON B.kd_merchant_produk=C.id_merchant_produk WHERE B.kd_merchant_produk=A.kd_merchant_produk) AS jumlah FROM merchant_transaksi_detail A JOIN merchant_produk D ON A.kd_merchant_produk=D.id_merchant_produk GROUP BY A.kd_merchant_produk ORDER BY (SELECT SUM(B.jumlah_produk) FROM merchant_transaksi_detail B JOIN merchant_produk C ON B.kd_merchant_produk=C.id_merchant_produk WHERE B.kd_merchant_produk=A.kd_merchant_produk) DESC 




SELECT D.nama_produk, (SELECT SUM(B.jumlah_produk) FROM merchant_transaksi_detail B JOIN merchant_produk C ON B.kd_merchant_produk=C.id_merchant_produk WHERE B.kd_merchant_produk=A.kd_merchant_produk) AS jumlah FROM merchant_transaksi_detail A JOIN merchant_produk D ON A.kd_merchant_produk=D.id_merchant_produk WHERE YEAR(A.tgl_input_detail)='2020' AND MONTH(A.tgl_input_detail) GROUP BY A.kd_merchant_produk ORDER BY (SELECT SUM(B.jumlah_produk) FROM merchant_transaksi_detail B JOIN merchant_produk C ON B.kd_merchant_produk=C.id_merchant_produk WHERE B.kd_merchant_produk=A.kd_merchant_produk) DESC 



<?php  

	for ($i=1; $i<=12 ; $i++) { 
		
		$bulan = sprintf('%02s', $i);

		$query = "SELECT D.nama_produk, (SELECT SUM(B.jumlah_produk) FROM merchant_transaksi_detail B JOIN merchant_produk C ON B.kd_merchant_produk=C.id_merchant_produk WHERE B.kd_merchant_produk=A.kd_merchant_produk) AS jumlah FROM merchant_transaksi_detail A JOIN merchant_produk D ON A.kd_merchant_produk=D.id_merchant_produk WHERE YEAR(A.tgl_input_detail)='2020' AND MONTH(A.tgl_input_detail)='$bulan' GROUP BY A.kd_merchant_produk ORDER BY (SELECT SUM(B.jumlah_produk) FROM merchant_transaksi_detail B JOIN merchant_produk C ON B.kd_merchant_produk=C.id_merchant_produk WHERE B.kd_merchant_produk=A.kd_merchant_produk) DESC ";

		$bulan[$i] = $db->query($query)->fetch_assoc();
	}


?>


<!-- data laporan perkasir -->
<?php

$sess_kd_merchant = $_SESSION['kd_merchant'];

  // echo "<pre>";
  // echo ($data_transaksi);
  // echo die();



if(empty($_POST['tgl_awal']) || empty($_POST['tgl_akhir'])){
  $tgl_awal = date('Y-m-d');
  $tgl_akhir = date('Y-m-d');
} else {
  $tgl_awal = date('Y-m-d', strtotime($_POST['tgl_awal']));
  $tgl_akhir = date('Y-m-d', strtotime($_POST['tgl_akhir']));
}
?>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Laporan Transaksi Kasir</h1>
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

            <div class="col-md-2 float-right">
              <div class="form-group">
                <button type="button" class="btn btn-primary btn-block" id="filter">filter</button>
              </div>
            </div>

            <div  class="col-md-4 float-right">
              <div class="form-group input-group">
                <!-- <input type="text" id="nama_toko"> -->
                <input type="hidden" id="input_id_kasir" name="id_kasir">
                <input type="text" id="input_nama_kasir" name="inpit_nama_kasir" class="form-control" placeholder="Pilih Nama Kasir">
                  <span class="input-group-btn"> 
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-kasir">
                    <i class="fa fa-search"></i>
                    </button>
                  </span>
              </div>
            </div>

            <div class="col-md-5 float-right">
              <div class="form-group">
                <input type="text" value="" class="form-control" name="dates" id="dates" placeholder="Pilih Tanggal" />
              </div>
            </div>

              <div class="form-group" style="padding-right: 9px">
                <a onclick="exportexcel('<?= $tgl_awal ?>', '<?= $tgl_akhir ?>')">
                  <button type="button" class="btn btn-success float-right" style="padding-right: 30px"><i class="fa fa-fw fa-file-download"></i>Eksport Excel</button>
                </a>
            </div>          

          <div class="card-body">
            
            <div class="bungkus-table d-none">
              <div  class="table-responsive">
                <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Nama Kasir</th>
                          <th>Nama Product</th>
                          <th>Tanggal Transaksi</th>
                          <th>Harga Peritem</th>
                          <th>Jumlah Terjual</th>
                          <th>Total</th>
                        </tr>
                      </thead>
                      <tbody id="filter_data">
                      </tbody>
                    </table>    
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- modal perpemesan -->
<div class="modal fade" id="modal-kasir">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Pilih Kasir</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hodden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <table class="table table-bodered table-striped" id="example" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kasir</th>
                            <th>Username Kasir</th>
                            <!-- <th>No Telp</th> -->
                            <!-- <td>jenis</td> -->
                            <th>pilih</th>
                        </tr>
                    </thead>
                    <tbody>
                      <!-- looping data Kasir dari masing masing mercahnt -->
                      <?php
                          $query = "SELECT * 
                                      FROM merchant_employee 
                                      WHERE status_aktif_employee='Y' AND kd_merchant = '$sess_kd_merchant' 
                                      ORDER BY id_merchant_employee DESC";
                          $data_employee = $db->query($query)->fetch_all(MYSQLI_ASSOC);
                      ?>
                      <?php
                        $no = 1;
                        foreach ($data_employee as $o) {
                      ?>
                      <tr>  
                        <td><?= $no++ ?></td>
                        <td><?= $o['nama_employee'] ?></td>
                        <td><?= $o['username_employee'] ?></td>
                        
                        <td>
                          <button class="btn btn-xs btn-info" id="pilih_kasir" 
                            data-nama_kasir="<?=$o['nama_employee']?>"
                            data-id_kasir="<?=$o['id_merchant_employee']?>"
                            >
                            <i class="fa fa-check"></i>Select
                          </button>
                        </td>    
                      </tr> 
                      <?php
                          }
                      ?>
                    </tbody>
                </table>             
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
  
    //tangkap inputan filter kasir
    var id_kasir = [];
    id_kasir.push("");
    
    var nama_kasir = [];
    nama_kasir.push("");
    
    var tanggal = [];
    tanggal.push("");
    // tanggal.push('<?= date("Y-m-d")  ?>_<?= date("Y-m-d")  ?>');

    $(document).on('click', '#pilih_kasir', function(){
        nama_kasir =[];
        id_kasir =[];
        $('#input_nama_kasir').val($(this).data('nama_kasir'))
        $('#input_id_kasir').val($(this).data('id_kasir'))
        var tangkap_nama_kasir = $(this).data('nama_kasir')
        var tangkap_id_kasir = $(this).data('id_kasir')
        nama_kasir.push(tangkap_nama_kasir);
        id_kasir.push(tangkap_id_kasir);
        $('#modal-kasir').modal('hide')
    });//end tangkap filter kasir
 
$(function () {
     $('input[name="dates"]').daterangepicker({
        opens: 'left'
      }, function (start, end) {
          // $("#dates").change(function() {
              tanggal = [];
              var hasil_tanggal = start.format('YYYY-MM-DD')+'_'+end.format('YYYY-MM-DD')
              // console.log(hasil_tanggal)
              tanggal.push(hasil_tanggal);
          // })
        }
    )
})


$(document).on('click', '#filter', function(){
    var filter_tanggal = tanggal[0]
    var filter_nama = nama_kasir[0]
    var filter_id = id_kasir[0]

    // alert(filter_nama)
    // alert(filter_tanggal)
    // alert(filter_id)
  
    $.ajax({
      type    : 'POST',
      url     : '<?=base_url().'/view/laporan_transaksi/proses_data.php' ?>',
      // async   : true,
      data   : { filter : true, 'filter_tanggal' : filter_tanggal , 'filter_nama' : filter_nama, 'filter_id' : filter_id },
      dataType: 'json',
      success : function(data){//data dari controller di simpan pada (data)
        // console.log(data)
        var html = '';
        var i;
        var no=0;

        if(data.length >0){
          for (i=0; i<data.length; i++) {
            no++;
            html += '<tr>'+
                        '<td>'+ no + '</td>'+
                        '<td>'+ data[i].nama_employee + '</td>'+
                        '<td>'+ data[i].nama_produk + '</td>'+
                        '<td>'+ data[i].tgl_input_detail + '</td>'+
                        '<td>'+ data[i].harga_setelah_diskon + '</td>'+
                        '<td>'+ data[i].jumlah_produk + '</td>'+
                        '<td>'+ parseInt(data[i].harga_setelah_diskon).toLocaleString() + '</td>'+
                '</tr>';

                $('#filter_data').html(html);
          }
        }else{
          html += '<tr>'+
                    '<td colspan=12 class="text-center" >' +'Tidak ada data'+ '</td>'+
                  '</tr>'
                $('#filter_data').html(html);
        }
        $('.bungkus-table').removeClass('d-none');
      }
    })    

});//end tangkap filter kasir


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