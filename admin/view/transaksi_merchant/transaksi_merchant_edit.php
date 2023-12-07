<?php  
  function setValue($value){
    $val = @$_SESSION['setvalue'][$value];
    unset($_SESSION['setvalue'][$value]);
    echo $val;
  }
    $sess_kd_merchant = $_SESSION['kd_merchant'];
    
    
    $id_transaksi_merchant = enkripsiDekripsi($_GET['id'],'dekripsi');
//   $sql = "SELECT * 
//             FROM merchant_produk A
//             LEFT JOIN merchant_stok B ON A.id_merchant_produk = B.kd_merchant_produk
//             WHERE id_merchant_produk = '$id_produk'
//                 AND A.kd_merchant = '$sess_kd_merchant' ";
//   $data_produk = $db->query($sql)->fetch_assoc();
  
  $sql = "SELECT * 
            FROM view_merchant_transaksi A
            WHERE A.id_merchant_transaksi = '$id_transaksi_merchant'
                AND A.kd_merchant = '$sess_kd_merchant'
                AND status_transaksi = '2'
                AND status_transaksi_detail = '2' ";
  $data_transaksi = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
  
//   $sql1 = "SELECT * 
//             FROM view_merchant_transaksi A
//             WHERE A.id_merchant_transaksi = '$id_transaksi_merchant'
//                 AND A.kd_merchant = '$sess_kd_merchant'
//                 AND status_transaksi_detail = '2'
//                 AND status_transaksi = '2' ";
//   $data_transaksi_sum = $db->query($sql1)->num_rows;

  $sql = "SELECT * 
            FROM merchant_kategori_produk
            WHERE kd_merchant = '$sess_kd_merchant' ";
  $data_kategori = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
  
  
  $count_transaksi = COUNT($data_transaksi); 
 
 
    // echo "<pre>";
    // echo print_r($data_kategori);
    // echo die();
?>
<script src="plugins/bootbox/dist/bootbox.all.min.js"></script>
<script src="plugins/bootbox/dist/bootbox.locales.min.js"></script>
<script src="plugins/bootbox/dist/bootbox.min.js"></script>


<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Pilih Product Yang Dicancel</h1>
            <small class="text-red">Note : Hapus Data Satu Persatu </small>
        
         </div>
    </div>
  </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?php if (isset($_SESSION['notifikasi'])) { notifikasi($_SESSION['notifikasi']); } ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    
                    <?php if($count_transaksi > 0 ) { ?>
                
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                          <thead>
                            <tr>
                              <th>No</th>
                              <th>Kode Produt</th>
                              <th>Nama Produt</th>
                              <th>Tanggal Transaksi</th>
                              <th>Harga Product</th>
                              <th>Discount</th>
                              <th>QTY</th>
                              <th>Total</th>
                              <th>Status Pembayaran</th>
                              <th data-searchable="false" data-orderable="false">Kelola</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php $nomor=1; ?>
                            <?php foreach($data_transaksi as $key => $val) : ?>
                                <?php $id_enkripsi = enkripsiDekripsi($val['id_merchant_produk'],'enkripsi'); ?>
                                <tr>
                                    <td> <?= $nomor++; ?> </td>
                                    <td> <?= $val['id_merchant_produk']; ?> </td>
                                    <td> <?= $val['nama_produk']; ?> </td>
                                    <td> <?= $val['tgl_input_transaksi']; ?> </td>
                                    <td> <?= number_format($val['harga_produk']); ?> </td>
                                    <td> 
                                        <?php 
                                            if($val['diskon_barang'] == ""){
                                              echo "0";
                                            } else{
                                              echo  $val['diskon_barang']." %";
                                            }
                                        ?> 
                                    </td>
                                    <td> <?= $val['jumlah_produk']; ?> </td>
                                    <td> <?= number_format($val['harga_setelah_diskon'] * $val['jumlah_produk']); ?> </td>
                                    <td> 
                                        <?php  if($val['status_transaksi_detail']=="2")
                                            echo "Success";
                                        ?> 
                                    </td>
                                    <td align="center"> 
                                        <a onclick="return confirm('Yakin data ingin dihapus ?')" href="view/transaksi_merchant/proses_data.php?transaksi_merchant_hapus=<?= $val['nama_produk']; ?>&id=<?php echo $id_enkripsi ?>&id_transaksi_detail=<?= $val['id_merchant_transaksi_detail']?>&id_merchant_transaksi=<?= enkripsiDekripsi($val['id_merchant_transaksi'], 'enkripsi')?>" data-toggle="tooltip" title="Hapus">
                                            <button class ="btn btn-danger btn-xm" style="margin: 3px"><i class="fa fa-fw fa-trash"></i></button>
                                        </a>
                                </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                    </div>
                    
                    <?php } ?>
                    
                    <?php if($count_transaksi == 0 ) { ?>
                        <?php
                            $merchant_transaksi = " UPDATE merchant_transaksi SET status_transaksi ='3'
                                                    WHERE kd_merchant = '$sess_kd_merchant'
                                                    AND id_merchant_transaksi = '$id_transaksi_merchant'
                                                     ";
                            $sql2 = $db->query($merchant_transaksi);
                        ?>
                        
                        <script>
                        $(document).ready(function(){
                            bootbox.alert("Data Transaksi Kosong, Status Transaksi Sudah Dibatalkan Semua!!!", function(){ 
                                window.location.replace('merchant.php?page=transaksi_merchant&action=cancel');
                            });
                        })
        				</script>
                        
                    <?php } ?>
                    
                </div>
            </div>
        <div class="col-md-6">
        
    </div>
</section>



<script type="text/javascript">
		
		function format_nominal(arg){
            var bayar = $(arg).val().replace(/[^0-9]/g, '');
                if (bayar == 0) {bayar = 0}
                $(arg).val(parseInt(bayar).toLocaleString());
		}
	</script>