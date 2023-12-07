<?php
    $sess_kd_merchant = $_SESSION['kd_merchant'];

    $query = "SELECT * 
                FROM merchant_transaksi mt
                LEFT JOIN merchant_employee me ON mt.kd_merchant_employee = me.id_merchant_employee
                LEFT JOIN jenis_pembayaran jp ON mt.kd_jenis_pembayaran = jp.id_jenis_pembayaran
                WHERE mt.status_transaksi !='1'
                    AND mt.kd_merchant = '$sess_kd_merchant'
                    AND mt.kd_jenis_pembayaran != ''
                ORDER BY DATE(mt.tgl_input_transaksi) DESC";
    $data_transaksi = $db->query($query)->fetch_all(MYSQLI_ASSOC);

?>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-12">
        <h1 class="m-0 text-dark">Kelola Transaksi</h1>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <?php if (isset($_SESSION['notifikasi'])) { notifikasi($_SESSION['notifikasi']); } ?>
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Daftar Transaksi</h3>
            
          
          </div>
          <div class="card-body">

                <table id="example" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>No Nota</th>
                      <th>Nama Kasir</th>
                      <th>Jenis Pembayaran</th>
                      <th>Tanggal Transaksi</th>
                      <th>Diskon</th>
                      <th>Jumlah Product</th>
                      <th>Total</th>
                      <th>Status Transaksi</th>
                      <th>Keterangan</th>
                      <th>Kelola</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $nomor=1; ?>
                    <?php foreach($data_transaksi as $key => $val) : ?>
                    <?php $id_enkripsi = enkripsiDekripsi($val['id_merchant_transaksi'],'enkripsi'); ?>
                        <tr>
                            <td> <?= $nomor++; ?> </td>
                            <td> <?= $val['no_nota']; ?> </td>
                            <td> <?= $val['nama_employee']; ?> </td>
                            <td> <?= $val['nama_jenis_pembayaran']; ?> </td>
                            <td> <?= $val['tgl_input_transaksi']; ?> </td>
                            <td> 
                                <?php 
                                    if($val['diskon'] == ""){
                                      echo "0";
                                    } else{
                                      echo  $val['diskon']." %";
                                    }
                                ?>
                            </td>
                            <td> <?= $val['jumlah_item']; ?> </td>
                            <td> <?= number_format($val['tagihan_nota']); ?> </td>
                            <td> 
                                <?php 
                                    if($val['status_transaksi'] == "2"){
                                        echo "Sukses";
                                    }else if ($val['status_transaksi'] == "3"){
                                        echo "transaksi Di Batalkan!!!";
                                    }
                                ?> 
                            </td>
                            <td> <?= $val['keterangan']; ?> </td>
                            <td align="center"> 
                              <!--<a href="?page=transaksi_merchant&action=edit&id=<?php echo $id_enkripsi ?>" title="Edit Transaksi">-->
                              <!--  <button class ="btn btn-danger">-->
                              <!--      <i class="fa fa-fw fa-edit">-->
                              <!--      </i>-->
                              <!--  </button>-->
                              <!--</a>-->
                              
                                <a href="#modalRincian" data-toggle="modal" data-id_merchant_transaksi="<?= $id_enkripsi ?>" data-kd_merchant="<?=$val['kd_merchant']?>" class="rincian_transaksi" title="Rincian Transaksi">
									<button class ="btn btn-info">
                                        <i class="fa fa-fw fa-eye"></i>
                                    </button>
								</a>
                                
                                <?php if ($val['status_transaksi'] != "3" ){ ?>
                                    <a href="#modalDelete" data-toggle="modal" onclick="$('#modalDelete #formDelete').attr('action', 'view/transaksi_merchant/proses_data.php?transaksi_merchant_hapus_all=<?= $val['no_nota']; ?>&id_merchant_transaksi=<?= $id_enkripsi ?>')" title="Batal Transaksi">
    									<button class ="btn btn-danger">
                                            <i class="fa fa-fw fa-times"></i>
                                        </button>
    								</a>
    							<?php } ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>

            
          </div> 
        </div>
      </div>
    </div>
  </div>
</section>

<!--modal untuk delete-->
<div class="modal fade" id="modalDelete">
	<div class="modal-dialog">
		<div class="modal-content">
		    <form id="formDelete" action="" method="post">
    			<div class="modal-header">
    			    <h4 class="modal-title">Yakin Ingin menghapus Data ini ?</h4>
    				<button type="button" class="close" data-dismiss="modal" aria-label="close">
    					<span aria-hodden="true">&times;</span>
    				</button>
    			</div>
    			<div class="modal-body">
    			    <div class="form-group">
                        <label for="keterangan_revisi">Keterangan Cancel</label>
                        <textarea name="keterangan_revisi" class="form-control" id="keterangan_revisi" placeholder="Keterangan Cancel" maxlength="300" autocomplete="off" required></textarea>
                    </div>
    			</div>
    			<div class="modal-footer table-responsive">
    			    
    					<button class="btn btn-default" data-dismiss="modal">Tidak</button>
        				<button class="btn btn-danger" type="submit">Cancel Transaksi</button>
        			
			    </div>
			</form>
		</div>
	</div>
</div>

<!--modal untuk info-->
<div class="modal fade" id="modalRincian" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail Transaksi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">

$(document).ready(function(){
    $('.openPopup').on('click',function(){
      var dataURL = $(this).attr('data-href');
      $('.modal-content').load(dataURL,function(){
        $('#modalRincian').modal({show:true});
      });
    }); 
  });

    $(document).on('click', '.rincian_transaksi', function(){
        var id_merchant_transaksi = $(this).data('id_merchant_transaksi')
        var kd_merchant           = $(this).data('kd_merchant')
        //alert(id_merchant_transaksi)
        $.ajax({
            url     : 'view/transaksi_merchant/detail_merchant_transaksi.php',
            type    : 'post',
            data    : {
                'rincian_transaksi' : true,
                'id_merchant_transaksi' : id_merchant_transaksi,
                'kd_merchant'   : kd_merchant
            },
            //dataType    : 'json',
            success : function(response){
                $("#modalRincian .modal-body").html(response);
            },
            error: function(){
                alert('Kegagalan Sistem');
            }
        })
        
    })
</script>