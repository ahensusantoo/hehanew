<?php 
    require_once('../../templates/koneksi.php');
    
    $id_transaksi     = $_GET['id_transaksi'];
    $tot_diskon = 0;
    $tot_normal = 0;
    $datadiskon = 0;
    $sql    = mysqli_query($db,"SELECT a.jumlah_bayar-a.tagihan_nota AS kembalian, a.tagihan_nota, a.diskon, a.jumlah_bayar, a.jumlah_item, a.status_print, a.keterangan,
    a.status_transaksi, a.tgl_input_transaksi, a.no_nota, a.no_antrian, b.nama_merchant, b.telp_merchant, c.nama_employee, d.nama_jenis_pembayaran, a.nomeja, a.biaya_service, a.biaya_pajak
    FROM merchant_transaksi a
    JOIN merchant b ON a.kd_merchant = b.id_merchant
    JOIN jenis_pembayaran d ON a.kd_jenis_pembayaran = d.id_jenis_pembayaran
    JOIN merchant_employee c ON a.kd_merchant_employee = c.id_merchant_employee
    WHERE id_merchant_transaksi ='$id_transaksi'")->fetch_assoc(); 
        
        $data['no_nota']         = $sql['no_nota'];
        $data['keterangan']         = $sql['keterangan'];
        $data['nama_merchant']       = $sql['nama_merchant'];
        $data['telp_merchant']       = $sql['telp_merchant'];
        $data['nama_employee']       = $sql['nama_employee'];
        $data['nama_jenis_pembayaran']       = $sql['nama_jenis_pembayaran'];
        $data['no_antrian']     = $sql['no_antrian'];
        $data['jumlah_item']     = $sql['jumlah_item'];
        $data['status_print']    = $sql['status_print'];
        $data['status_transaksi']    = $sql['status_transaksi'];
        $data['tgl_input_transaksi'] = date_format(date_create($sql['tgl_input_transaksi']), 'd M y, H:i A');
        $data['jumlah_bayar']    = "Rp " . number_format((double)$sql['jumlah_bayar'],0,',','.');
        $data['tagihan_nota']    = "Rp " . number_format((double)$sql['tagihan_nota'],0,',','.');
        $data['kembalian']       = "Rp " . number_format((double)$sql['kembalian'],0,',','.');
        $data['diskon_voucher']  = "Rp " . number_format((double)$sql['diskon'],0,',','.');
        $data['nomeja']          = $sql['nomeja'];
        $data['servis']          = "Rp " . number_format((double)$sql['biaya_service'],0,',','.');
        $data['pajak']           = "Rp " . number_format((double)$sql['biaya_pajak'],0,',','.');
            
        $sql    = mysqli_query($db,"SELECT a.id_merchant_transaksi_detail, c.nama_produk,  a.jumlah_produk, a.harga_produk, a.diskon, a.harga_setelah_diskon, a.jumlah_produk*a.harga_produk AS jumlah, c.gambar_produk, c.status_konsi, a.catatan_pesanan FROM merchant_transaksi_detail a
        LEFT JOIN merchant_produk c ON c.id_merchant_produk = a.kd_merchant_produk
        WHERE a.status_transaksi_detail != 3 AND a.kd_merchant_transaksi ='$id_transaksi'"); 
        $result = array();
            while($row = mysqli_fetch_array($sql)){
                $tot_normal = $tot_normal + ($row['harga_produk']*$row['jumlah_produk']);
                $data['sub_total']       = "Rp " . number_format((double)$tot_normal,0,',','.');
                $data['sub_total_asli']    = (double)$tot_normal;
                
                if ($row['diskon'] != '0') {
                    $datadiskon = $row['harga_produk'] - $row['harga_setelah_diskon'];
                    $tot_diskon = $tot_diskon + ($datadiskon*$row['jumlah_produk']);
                    
                }
                $data['total_diskon']       = "Rp " . number_format((double)$tot_diskon,0,',','.');
            array_push($result,array(
                'id_merchant_transaksi_detail'      => $row['id_merchant_transaksi_detail'],
                'nama_produk'                       => str_replace("&#039;","'",$row['nama_produk']),
                'qty'                               => $row['jumlah_produk'],
                'diskon'                            => $row['diskon'],
                'harga_diskon'                      => "Rp " . number_format((double)$datadiskon,0,',','.'),
                'harga_produk'                      => "Rp " . number_format((double)$row['harga_produk'],0,',','.'),
                'harga_produk_diskon'               => "Rp " . number_format((double)$row['harga_setelah_diskon'],0,',','.'),
                'total_harga'                       => "Rp " . number_format((double)$row['jumlah'],0,',','.'),
                'status_konsi'                      => $row['status_konsi'],
                'gambar_produk'                     => base_url()."dist/img/barang/".$row['gambar_produk'],
              	'catatan_pesanan'					=> $row['catatan_pesanan'],
            ));
            }
        
         if(isset($result[0])) {
            $result1['data_transaksi'] = $data;
            $result1['result'] = $result;
                    
            echo json_encode($result1);
            
        }  else{
                http_response_code(400);
                $respon['pesan'] = "Tidak ada transaksi terkait!\nKlik `Mengerti` untuk menutup pesan ini";
                echo json_encode($respon);
        }
    
    

        
    mysqli_close($db);

 ?>