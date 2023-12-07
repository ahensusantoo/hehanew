<?php
    require_once('../../templates/koneksi.php');
    $id_supplier        = $_REQUEST['id_supplier'];
    $kode_supplier      = $_REQUEST['kode_supplier'];
    $nama_supplier      = $_REQUEST['nama_supplier'];
    $alamat_supplier    = $_REQUEST['alamat_supplier'];
    $telp_supplier      = $_REQUEST['telp_supplier'];
    $status_aktif_supplier = $_REQUEST['status_aktif_supplier'];
    $status_rmv_supplier = $_REQUEST['status_rmv_supplier'];
    
    $update     = mysqli_query($db, "UPDATE supplier SET kode_supplier = '$kode_supplier', nama_supplier = '$nama_supplier', alamat_supplier = '$alamat_supplier', telp_supplier = '$telp_supplier', status_aktif_supplier = '$status_aktif_supplier', status_rmv_supplier = '$status_rmv_supplier' WHERE id_supplier = '$id_supplier'");
    if ($update){
        $respon['pesan']  = "Berhasil mengedit supplier";
        echo json_encode($respon);
        die();
    }else{
        http_response_code(400);
        $respon  = "Gagal mengedit supplier";
        echo json_encode($respon);
        die();
    }
?>