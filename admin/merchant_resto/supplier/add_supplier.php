<?php
    require_once('../../templates/koneksi.php');
    
    $kd_supplier        = $_REQUEST['kode_supplier'];
    $nama_supplier      = $_REQUEST['nama_supplier'];
    $alamat_supplier    = $_REQUEST['alamat_supplier'];
    $telp_supplier      = $_REQUEST['telp_supplier'];
    
    $id         = mysqli_query($db, "SELECT createID('supplier') AS id")->fetch_assoc();
    $idku       = $id['id'];
    $insert     = mysqli_query($db, "INSERT INTO supplier SET id_supplier = '$idku', kode_supplier = '$kd_supplier', nama_supplier = '$nama_supplier', alamat_supplier = '$alamat_supplier', telp_supplier = '$telp_supplier', status_aktif_supplier = 'Y', status_rmv_supplier = 'N'");
    if ($insert){
        $respon['pesan'] = "Berhasil menambah supplier";
        echo json_encode($respon);
        die();
    }else{
        http_response_code(400);
        $respon = "Gagal menambah supplier!";
        echo json_encode($respon);
        die();
    }
?>