<?php
require_once('../../templates/koneksi.php');

$kode_voucher       = $_GET['kode_voucher'];
$total_transaksi    = $_GET['total_transaksi'];
$tanggal_sekarang   = date("Y-m-d");
$jam_sekarang       = date("H:i");

$sql    = "SELECT * FROM voucher_merchant WHERE kode_voucher='$kode_voucher' AND start_tgl<='$tanggal_sekarang' AND end_tgl>='$tanggal_sekarang' 
AND status_rmv_voucher='N' AND start_jam<'$jam_sekarang' AND end_jam>'$jam_sekarang' AND max_pengguna>0"; 
$data_voucher = $db->query($sql)->fetch_assoc();

//cek ada gak kode voucher nya
if(isset($data_voucher)) {
        //cek status potongan atau bukan
        if ($data_voucher['status_potongan'] == "1") { //Potongan Harga
            $diskon =  (int)$data_voucher['potongan_voucher'];
            if($diskon > $total_transaksi){
                // cek total transaksi, melebihi harga pembelian tidak
                http_response_code(400);
                $respon['pesan'] = "Gagal, melebihi harga pembelian!\nKlik `Mengerti` untuk menutup pesan ini";
                echo json_encode($respon);
                exit();
                die();
            }

            //cek minimal transaksi pengguna
            $minimal_transaksi = $data_voucher['min_transaksi'];
            if ($minimal_transaksi >= $total_transaksi){
                http_response_code(400);
                $respon['pesan'] = "Gagal, minimal transaksi anda kurang!\nKlik `Mengerti` untuk menutup pesan ini";
                echo json_encode($respon);
                exit();
                die();
            }

            //cek maksimal pengguna
            $max_pengguna = $data_voucher['max_pengguna'];
            if ($max_pengguna < 0){
                http_response_code(400);
                $respon['pesan'] = "Gagal, maksimal pengguna sudah terpenuhi!\nKlik `Mengerti` untuk menutup pesan ini";
                echo json_encode($respon);
                exit();
                die();
            }

            $db->query("UPDATE voucher_merchant SET max_pengguna=max_pengguna-1 WHERE kode_voucher='$kode_voucher' ");
            $respon['hasil_potongan'] = $data_voucher['potongan_voucher'];
            $respon['pesan'] = "Berhasil klaim voucher";
            echo json_encode($respon);

        }elseif($data_voucher['status_potongan'] == "2"){ 
        // Potongan Persen
            if($data_voucher['potongan_voucher'] > 100){
               $data_voucher['potongan_voucher'] = 100; 
           }

        //cek minimal transaksi pengguna
           $minimal_transaksi = $data_voucher['min_transaksi'];
           if ($minimal_transaksi >= $total_transaksi){
            http_response_code(400);
            $respon['pesan'] = "Gagal, minimal transaksi anda kurang!\nKlik `Mengerti` untuk menutup pesan ini";
            echo json_encode($respon);
        }

        //cek potongan maksimal transaksi pengguna
        $diskon = ((double)$total_transaksi * (double)$data_voucher['potongan_voucher'] / 100);
        $maksimal_potongan =  (double)$data_voucher['max_potongan'];
        if($diskon >= $maksimal_potongan){
                // GAGAL, melebihi harga pembelian
            $hasil_potongan = $data_voucher['max_potongan'];
        } else {
            $hasil_potongan = $diskon;
        }

        //cek maksimal pengguna
        $max_pengguna = $data_voucher['max_pengguna'];
        if ($max_pengguna < 0){
            http_response_code(400);
            $respon['pesan'] = "Gagal, maksimal pengguna sudah terpenuhi!\nKlik `Mengerti` untuk menutup pesan ini";
            echo json_encode($respon);
        }

        
        $db->query("UPDATE voucher_merchant SET max_pengguna=max_pengguna-1 WHERE kode_voucher='$kode_voucher' ");
        $respon['hasil_potongan'] = (string)$hasil_potongan;
        $respon['pesan'] = "Berhasil klaim voucher";
        echo json_encode($respon);
        exit();
        die();
    }else{
            // GAGAL, Voucher tidak dapat digunkan
        http_response_code(400);
        $respon['pesan'] = "Gagal, voucher tidak dapat digunakan!\nKlik `Mengerti` untuk menutup pesan ini";
        echo json_encode($respon);
        exit();
        die();
    }
} else {
    http_response_code(400);
    $respon['pesan'] = "Tidak ada data yang ditampilkan!\nKlik `Mengerti` untuk menutup pesan ini";
    echo json_encode($respon);
    exit();
    die();
}

mysqli_close($db);


?>