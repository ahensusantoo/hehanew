<?php 
    require_once('../../templates/koneksi.php');
    
    $id_merchant                     = $_GET['id_merchant'];
    $id_merchant_employee            = $_GET['id_merchant_employee'];
    $tanggal_awal                    = $_GET['tanggal_awal'];
    $tanggal_akhir                   = $_GET['tanggal_akhir'];
    $totaljml                        = 0;
    $diskon_total                    = 0;
    $level                           = $_GET['level'];
	$subjml                          = 0;
	$totalpembayaran                 = 0;
    
    if($level == 2){
        $query	= mysqli_query($db,"SELECT 
        A.nama_jenis_pembayaran, 
        (SELECT SUM(MT.tagihan_nota) FROM merchant_transaksi MT WHERE MT.kd_jenis_pembayaran=A.id_jenis_pembayaran AND MT.kd_merchant = '$id_merchant' AND MT.kd_merchant_employee = '$id_merchant_employee' AND MT.status_transaksi = '2' AND DATE(MT.tgl_input_transaksi) BETWEEN '$tanggal_awal' AND '$tanggal_akhir') AS total
        FROM jenis_pembayaran A WHERE A.status_aktif='Y'"); 
        $result2 = array();
        	while($row2 = mysqli_fetch_array($query)){
        	$totalpembayaran = $totalpembayaran + $row2['total'];   
        	array_push($result2,array(
        		'nama_jenis_pembayaran'      	    => str_replace("&#039;","'",$row2['nama_jenis_pembayaran']),
        		'total'	                            => "Rp " . number_format((double)$row2['total'],0,',','.'),
        	));
            }
	
	    $diskon = $db->query("SELECT SUM((A.harga_produk-A.harga_setelah_diskon)*A.jumlah_produk) AS jml FROM merchant_transaksi_detail A JOIN merchant_transaksi B ON A.kd_merchant_transaksi=B.id_merchant_transaksi WHERE 
        B.kd_jenis_pembayaran!='' AND A.kd_merchant='$id_merchant' AND A.kd_merchant_employee='$id_merchant_employee' 
        AND DATE(A.tgl_input_detail) BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND A.status_transaksi_detail='2'")->fetch_assoc()['jml'];
        
        
        $sql	= mysqli_query($db,"SELECT toko, file_logo, nama_employee, telp_employee, gambar_produk, nama_produk, harga_setelah_diskon, SUM(jumlah_produk) AS jml, (harga_produk*SUM(jumlah_produk)) AS subtotal, diskon_total, harga_produk, kode_produk
        FROM view_merchant_transaksi WHERE `kd_merchant` = '$id_merchant' AND kd_merchant_employee = '$id_merchant_employee' AND status_transaksi = '2' AND status_transaksi_detail = '2' AND DATE(`tgl_input_transaksi`) BETWEEN '$tanggal_awal' AND '$tanggal_akhir' GROUP BY id_merchant_produk, harga_produk"); 
        $result = array();
        	while($row = mysqli_fetch_array($sql)){
        	$subjml                   = $subjml + $row['jml'];
        	$diskon_total             = $diskon_total + $row['diskon_total']; 
        	$diskon_total2            = $row['diskon_total'];
        	$subdiskon                = $diskon;
        	$totaljml                 = $totaljml + $row['subtotal'];
        	$total_omset_bersih       = $totaljml - $diskon - $diskon_total2;
        	$data['toko']             = $row['toko'];
        	$data['file_logo']        = $row['file_logo'];
        	$data['nama_employee']    = $row['nama_employee'];
        	$data['telp_employee']    = $row['telp_employee'];
        	$data['jml']              = $subjml;
        	$data['jml_pembayaran']   = "Rp " . number_format((double)$totalpembayaran,0,',','.');
        	$data['total']            = "Rp " . number_format((double)$total_omset_bersih,0,',','.');
        	$data['diskon_transaksi'] = "Rp " . number_format((double)$diskon_total2,0,',','.');
        	$data['diskon']           = "Rp " . number_format((double)$subdiskon,0,',','.');
        	$data['periode']	      = date('d M Y', strtotime($tanggal_awal))."-".date('d M Y', strtotime($tanggal_akhir));
        	array_push($result,array(
                'kode_produk'                 	    => $row['kode_produk'],
        		'nama_produk'      	                => str_replace("&#039;","'",$row['nama_produk']),
                'harga_produk_awal'               => "Rp " . number_format((double)$row['harga_produk'],0,',','.'),
                'jml'                 	            => $row['jml'],
        		'harga_produk'	                    => "Rp " . number_format((double)$row['harga_setelah_diskon'],0,',','.'),
        		'total_harga'	                    => "Rp " . number_format((double)$row['subtotal'],0,',','.'),
        		'gambar_produk'	                    => base_url()."dist/img/barang/".$row['gambar_produk'],
        	));
            }

        
         if(isset($result[0])) {
            $result1['data_transaksi'] = $data;
            $result1['jenisbayar'] = $result2;
            $result1['databarang'] = $result;
            echo json_encode($result1);
			
		}  else{
            http_response_code(400);
            $respon['pesan'] = "Tidak ada transaksi terkait!\nKlik `Mengerti` untuk menutup pesan ini";
            echo json_encode($respon);
		}
	
    }else{
        $query	= mysqli_query($db,"SELECT 
        A.nama_jenis_pembayaran, 
        (SELECT SUM(MT.tagihan_nota) FROM merchant_transaksi MT WHERE MT.kd_jenis_pembayaran=A.id_jenis_pembayaran AND MT.kd_merchant = '$id_merchant' AND MT.status_transaksi = '2' AND DATE(MT.tgl_input_transaksi) BETWEEN '$tanggal_awal' AND '$tanggal_akhir') AS total
        FROM jenis_pembayaran A WHERE A.status_aktif='Y'"); 
        $result2 = array();
        	while($row2 = mysqli_fetch_array($query)){
        	$totalpembayaran = $totalpembayaran + $row2['total'];   
        	array_push($result2,array(
        		'nama_jenis_pembayaran'      	    => str_replace("&#039;","'",$row2['nama_jenis_pembayaran']),
        		'total'	                            => "Rp " . number_format((double)$row2['total'],0,',','.')
        	));
            }
            
        $diskon = $db->query("SELECT SUM((A.harga_produk-A.harga_setelah_diskon)*A.jumlah_produk) AS jml FROM merchant_transaksi_detail A JOIN merchant_transaksi B ON A.kd_merchant_transaksi=B.id_merchant_transaksi WHERE 
        B.kd_jenis_pembayaran!='' AND A.kd_merchant='$id_merchant' AND A.kd_merchant_employee='$id_merchant_employee' 
        AND DATE(A.tgl_input_detail) BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND A.status_transaksi_detail='2'")->fetch_assoc()['jml'];
        
        $sql	= mysqli_query($db,"SELECT toko, file_logo, nama_employee, telp_employee, gambar_produk, nama_produk, harga_setelah_diskon, SUM(jumlah_produk) AS jml, (harga_produk*SUM(jumlah_produk)) AS subtotal, SUM(diskon_total) AS dtotal, harga_produk, kode_produk
        FROM view_merchant_transaksi WHERE `kd_merchant` = '$id_merchant' AND kd_merchant_employee = '$id_merchant_employee' AND status_transaksi = '2' AND DATE(`tgl_input_transaksi`) BETWEEN '$tanggal_awal' AND '$tanggal_akhir' GROUP BY id_merchant_produk, harga_produk"); 
        $result = array();
        	while($row = mysqli_fetch_array($sql)){
        	$totaljml                 = $totaljml + $row['subtotal'];
        	$subjml                   = $subjml + $row['jml'];
        	$subdiskon                = $diskon;
        	$data['toko']             = $row['toko'];
        	$data['file_logo']        = $row['file_logo'];
        	$data['nama_employee']    = $row['nama_employee'];
        	$data['telp_employee']    = $row['telp_employee'];
        	$data['jml']              = $subjml;
        	$data['jml_pembayaran']   = "Rp " . number_format((double)$totalpembayaran,0,',','.');
        	$data['total']            = "Rp " . number_format((double)$totaljml,0,',','.');
        	$data['diskon']           = "Rp " . number_format((double)$subdiskon,0,',','.');
        	$data['diskon_transaksi'] = "Rp " . number_format((double)$row['dtotal'],0,',','.');
        	$data['periode']	      = date('d M Y', strtotime($tanggal_awal))."-".date('d M Y', strtotime($tanggal_akhir));
        	array_push($result,array(
        	    'kode_produk'                 	    => $row['kode_produk'],
        		'nama_produk'      	                => str_replace("&#039;","'",$row['nama_produk']),
                'harga_produk_awal'               => "Rp " . number_format((double)$row['harga_produk'],0,',','.'),
                'jml'                 	            => $row['jml'],
        		'harga_produk'	                    => "Rp " . number_format((double)$row['harga_setelah_diskon'],0,',','.'),
        		'total_harga'	                    => "Rp " . number_format((double)$row['subtotal'],0,',','.'),
        		'gambar_produk'	                    => base_url()."dist/img/barang/".$row['gambar_produk'],
        	));
            }

        
         if(isset($result[0])) {
            $result1['data_transaksi'] = $data;
            $result1['jenisbayar'] = $result2;
            $result1['databarang'] = $result;
            echo json_encode($result1);
			
		}  else{
                http_response_code(400);
                $respon['pesan'] = "Tidak ada transaksi terkait!\nKlik `Mengerti` untuk menutup pesan ini";
                echo json_encode($respon);
		}
    }
        
	mysqli_close($db);

 ?>