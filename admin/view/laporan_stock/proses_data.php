<?php

// echo "<pre>";
// echo print_r($_POST);
// die();

require_once '../../templates/koneksi.php';
sessionLoginMerchantEmployee();

$sess_kd_merchant = $_SESSION['kd_merchant'];
$sess_id_employee = $_SESSION['id_merchant_employee'];


    // if (isset($_POST['filter'])){
    //     $tangkap_date = $_POST['tangkap_date'];

    //     //filter nama tanpa tanggal
    //     if (isset($tangkap_date)){
    //         // $query = "  SELECT * 
    //         //             FROM view_merchant_history_stok AS A
    //         //             JOIN merchant_employee AS B ON A.kd_merchant_employee = B.id_merchant_employee 
    //         //             WHERE A.kd_merchant = '$sess_kd_merchant'
    //         //                 AND DATE(A.tanggal_history) = '$tangkap_date'
    //         //          ";

    //         $query = "  SELECT * 
    //                         FROM merchant_history_stok AS A
    //                         JOIN merchant_employee AS B ON A.kd_merchant_employee = B.id_merchant_employee
    //                         JOIN merchant_produk AS C ON A.kd_merchant_produk = C.id_merchant_produk
    //                         WHERE A.kd_merchant = '$sess_kd_merchant'
    //                             AND DATE(A.tanggal_history) = '$tangkap_date'
    //                  ";
    //         $data_stock = $db->query($query)->fetch_all(MYSQLI_ASSOC);

    //         echo json_encode($data_stock);
    //     }
    // }
     //filter nama dan tanggal
?>