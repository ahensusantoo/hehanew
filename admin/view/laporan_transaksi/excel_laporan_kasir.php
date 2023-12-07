<?php 

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=laporan_kasir.xls");

require_once '../../templates/koneksi.php';

	$mulai      = antiSQLi(@$_GET['mulai']);
    $akhir      = antiSQLi(@$_GET['akhir']);
    $nama_kasir = antiSQLi(@$_GET['nama_kasir']);
    $id_kasir   = antiSQLi(@$_GET['id_kasir']);


     if( $mulai != "" AND $akhir != "" AND $nama_kasir != "" AND $id_kasir !=""){
        $mulai      = antiSQLi($_GET['mulai']);
        $akhir      = antiSQLi($_GET['akhir']);
        $nama_kasir = antiSQLi($_GET['nama_kasir']);
        $id_kasir   = antiSQLi($_GET['id_kasir']);

        $nama_kasir = $db->query("SELECT nama_employee FROM merchant_employee WHERE id_merchant_employee='$id_kasir'")->fetch_assoc()['nama_employee'];

        $check_data = $db->query("SELECT *,
                                SUM(jumlah_produk) jumlah_produk  
                              FROM merchant_transaksi_detail A 
                                LEFT JOIN merchant_produk B ON A.kd_merchant_produk=B.id_merchant_produk 
                                    WHERE A.kd_merchant = '$_SESSION[kd_merchant]'
                                        AND A.kd_merchant_employee='$id_kasir'
                                        AND A.status_transaksi_detail ='2'
                                        AND DATE(A.tgl_input_detail) BETWEEN '$mulai' AND '$akhir'
                                    GROUP BY kd_merchant_produk")->fetch_all(MYSQLI_ASSOC);
        
        $cek_jenis_bayar = $db->query("SELECT *,
                                        SUM(C.harga_setelah_diskon * C.jumlah_produk) tagihan_nota,
                                            (SELECT COUNT(D.kd_jenis_pembayaran) 
                                                FROM merchant_transaksi D 
                                                WHERE D.kd_merchant = '$_SESSION[kd_merchant]'
                                                    AND D.status_transaksi ='2'
                                                    AND D.kd_merchant_employee='$id_kasir'
                                                    AND DATE(D.tgl_input_transaksi) BETWEEN '$mulai' AND '$akhir'
                                                    AND D.kd_jenis_pembayaran = A.kd_jenis_pembayaran) as jumlah_struck
                                        FROM merchant_transaksi A 
                                        LEFT JOIN jenis_pembayaran B ON A.kd_jenis_pembayaran = B.id_jenis_pembayaran
                                        LEFT JOIN merchant_transaksi_detail C ON A.id_merchant_transaksi = C.kd_merchant_transaksi
                                        WHERE A.kd_merchant = '$_SESSION[kd_merchant]'
                                            AND C.kd_merchant_employee='$id_kasir'
                                            AND A.status_transaksi ='2'
                                            AND DATE(A.tgl_input_transaksi) BETWEEN '$mulai' AND '$akhir'
                                        GROUP BY A.kd_jenis_pembayaran   
                                    ")->fetch_all(MYSQLI_ASSOC);
                                        
        
    }else if( $mulai!= "" AND $akhir != ""){
        $check_data = $db->query("SELECT *,
                                SUM(jumlah_produk) jumlah_produk
                              FROM merchant_transaksi_detail A 
                                LEFT JOIN merchant_produk B ON A.kd_merchant_produk=B.id_merchant_produk 
                                    WHERE A.kd_merchant = '$_SESSION[kd_merchant]'
                                        AND A.status_transaksi_detail ='2'
                                        AND DATE(A.tgl_input_detail) BETWEEN '$mulai' AND '$akhir'
                                GROUP BY kd_merchant_produk")->fetch_all(MYSQLI_ASSOC);
                                
        $cek_jenis_bayar = $db->query("SELECT *,
                                        SUM(tagihan_nota) tagihan_nota, 
                                                (SELECT COUNT(C.kd_jenis_pembayaran) 
                                                FROM merchant_transaksi C 
                                                WHERE C.kd_merchant = '$_SESSION[kd_merchant]'
                                                    AND C.status_transaksi ='2'
                                                    AND DATE(C.tgl_input_transaksi) BETWEEN '$mulai' AND '$akhir'
                                                    AND C.kd_jenis_pembayaran = A.kd_jenis_pembayaran) as jumlah_struck
                                        FROM merchant_transaksi A 
                                        LEFT JOIN jenis_pembayaran B ON A.kd_jenis_pembayaran = B.id_jenis_pembayaran
                                        WHERE A.kd_merchant = '$_SESSION[kd_merchant]'
                                            AND A.status_transaksi ='2'
                                            AND DATE(A.tgl_input_transaksi) BETWEEN '$mulai' AND '$akhir'
                                        GROUP BY A.kd_jenis_pembayaran   
                                    ")->fetch_all(MYSQLI_ASSOC);
    }else{
        $mulai = date("Y-m-d");
        $akhir = date("Y-m-d");
    }

 ?>
<style type="text/css" media="screen">
	table{
		margin: 20px auto;
		border-collapse: collapse;
	}

	table th,
	table td{
		border: 1px solid #3c3c3c;
		padding: 3px 8px;
		font-size: 80%;
	}
</style>

<div style="min-width: 100px;"> 
    <div id="tab_tabel_closing" style="width: 850px;">
		<table style=" width: 100%">
		    <tbody>
		        <tr>
		            <td>Nama</td>
		            <td> : </td>
		            <td>
		              <?php 
		                if( $nama_kasir != ""){
		                  echo $nama_kasir;
		                }else{
		                  echo "-";
		                } 
		              ?>
		                  
		            </td>
		        </tr>
		        <tr>
		            <td>Priode</td>
		            <td> : </td>
		            <td><?= tanggal_indo($mulai) ?> - <?= tanggal_indo($akhir) ?></td>
		        </tr>
		    </tbody>
		</table>
		<hr>

		 <table class="tabel_border" border="1">
		     <thead>
		        <tr>
                    <th>Nama Barang</th>
                    <th>Tanggal Transaksi</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
		     </thead>
		     <tbody>
                <?php $harga_produk = 0; ?>
                <?php $diskon = 0; $jumlah_produk = 0; ?>
                <?php foreach($check_data as $key => $value): ?>
                    <?php 
                        $harga_produk += (int)$value['harga_produk']*$value['jumlah_produk'];
                        $jumlah_produk += (int)$value['jumlah_produk'];
                    ?>
                    <?php if ($value['diskon'] != "0"){
                        $diskon += (int)(($value['diskon'] * $value['harga_produk'] / 100)* $value['jumlah_produk'] );    
                    }else{
                        $diskon += (int)($value['diskon']) ;
                    } ?> 
                    
                    
                    <tr>
                        <td align="right"><?=$value['nama_produk'] ?></td>
                        <td align="right"><?=$value['tgl_input_detail'] ?></td>
                        <td align="right">Rp <?= number_format($value['harga_produk']) ?></td>
                        <td align="right"><?=$value['jumlah_produk'] ?></td>
                        <td align="right">Rp <?=number_format($value['harga_produk'] * $value['jumlah_produk'] ) ?> </td>
                    </tr>
                <?php endforeach; ?>
                
                <tr>
                    <td align="right" colspan="3">Total</td>
                    <td align="right"><?= number_format($jumlah_produk)  ?></td>
                    <td align="right">Rp <?= number_format($harga_produk)  ?></td>
                </tr>
                
                <tr>
                    <td align="right" colspan="4">Discount</td>
                    <td align="right">Rp <?= number_format($diskon)  ?></td>
                </tr>
              
                <tr>
                    <td align="right" colspan="4">Grand Total</td>
                    <td align="right">Rp <?= number_format($harga_produk - $diskon)  ?></td>
                </tr>
            </tbody>
        </table>
        <hr>
        <center><b>RINCIAN</b></center>
        <hr>
        <table class="tabel_border" border="1" style="margin-top: 10px; margin-left: auto; margin-right: auto; width: 100%">
            <thead>
                <tr>
                    <th style="text-align: center;">Jenis Pembayaran</th>
                    <th style="text-align: center;">Nominal</th>
                    <th style="text-align: center;">Jumlah Struck</th>
                </tr>
            </thead>
            <tbody>
                <?php $total_rincian_pembayaran = 0; $total_struck = 0; ?>
                <?php foreach ($cek_jenis_bayar as $key => $value) : ?>
                    <?php $total_rincian_pembayaran += $value['tagihan_nota']; $total_struck += $value['jumlah_struck']; ?>
                    <tr>
                        <td><?= $value['nama_jenis_pembayaran'] ?></td>
                        <td align="right">Rp <?= number_format($value['tagihan_nota']) ?></td>
                        <td align="right"><?= number_format($value['jumlah_struck']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td ><b>TOTAL</b></td>
                    <td align="right"><b>Rp <?= number_format($total_rincian_pembayaran) ?></b></td>
                    <td align="right"><b><?= number_format($total_struck) ?></b></td>
                </tr>
            </tbody>
        </table>
	</div>
</div>