<?php
    include("../templates/koneksi.php");
    if(isset($_POST['filter'])){
        $tgl_awal    = $_POST['tgl_awal'];
        $tgl_akhir   = $_POST['tgl_akhir'];
        $id_merchant = enkripsiDekripsi($_POST['id_merchant'], 'dekripsi');
        $id_produk = enkripsiDekripsi($_POST['id_produk'], 'dekripsi');
       
        $tgl_sebelum = date('Y-m-d', strtotime('-1 days', strtotime($tgl_awal)));
        
        $query = $db->query("SELECT a.keterangan, a.id_referensi, a.masuk, a.keluar, a.stok_setelah, a.kd_merchant_produk, a.tanggal_history, a.id_referensi
                                FROM merchant_history_stok a
                                JOIN merchant_produk b ON a.kd_merchant_produk = b.id_merchant_produk
                                WHERE a.kd_merchant_produk = '$id_produk'
                                    AND DATE(a.tanggal_history) BETWEEN '$tgl_awal' AND '$tgl_akhir'
                                    AND b.jenis_produk = '2'
                                    AND a.kd_merchant = '$id_merchant'")->fetch_all(MYSQLI_ASSOC);
                                    
        // $last_jumlah_stock = $db->query("SELECT a.stok_setelah
        //                                     FROM merchant_history_stok a  
        //                                     JOIN merchant_produk b ON a.kd_merchant_produk = b.id_merchant_produk
        //                                     WHERE a.kd_merchant_produk = '$id_produk'
        //                                         AND DATE(a.tanggal_history) = '$tgl_sebelum'
        //                                         AND b.jenis_produk = '2'
        //                                         AND a.kd_merchant = '$id_merchant'
        //                                         AND a.status_keranjang IN ('2','4', null)
        //                                     ORDER BY DATE(a.tanggal_history) DESC
        //                                     LIMIT 1
        //                                 ")->fetch_assoc();
        
        echo json_encode($query);
    }
?>