<?php
    require_once('../../templates/koneksi.php');
    
    if($_REQUEST['tag'] == "all"){
        if (!empty($_REQUEST['q'])){
            $q          = $_REQUEST['q'];
            $limit      = $_REQUEST['limit'];
            $offset     = $_REQUEST['offset'];
            $result = array();
            $data   = mysqli_query($db, "SELECT * FROM supplier WHERE nama_supplier LIKE '%$q%' AND status_rmv_supplier = 'N' ORDER BY status_aktif_supplier ASC LIMIT $offset, $limit");
            if ($data){
                foreach($data as $i => $row){
                    array_push($result, array(
                        'id_supplier'       => $row['id_supplier'],
                        'kode_supplier'     => $row['kode_supplier'],
                        'nama_supplier'     => $row['nama_supplier'],
                        'alamat_supplier'   => $row['alamat_supplier'],
                        'telp_supplier'      => $row['telp_supplier'],
                        'status_aktif_supplier' => $row['status_aktif_supplier'],
                        'status_rmv_supplier' => $row['status_rmv_supplier'],
                        'tanggal_input_supplier' => $row['tanggal_input_supplier']
                        ));
                }
                echo json_encode($result);
                die();
            }else{
                http_response_code(400);
                $respon['pesan'] = "Terjadi kesalahan pada sistem!";
                echo json_encode($respon);
                die();
            }
        }else{
            $limit      = $_REQUEST['limit'];
            $offset     = $_REQUEST['offset'];
            $result = array();
            $data   = mysqli_query($db, "SELECT * FROM supplier WHERE status_rmv_supplier = 'N' ORDER BY status_aktif_supplier ASC LIMIT $offset, $limit");
            if ($data){
                foreach($data as $i => $row){
                    array_push($result, array(
                        'id_supplier'       => $row['id_supplier'],
                        'kode_supplier'     => $row['kode_supplier'],
                        'nama_supplier'     => $row['nama_supplier'],
                        'alamat_supplier'   => $row['alamat_supplier'],
                        'telp_supplier'      => $row['telp_supplier'],
                        'status_aktif_supplier' => $row['status_aktif_supplier'],
                        'status_rmv_supplier' => $row['status_rmv_supplier'],
                        'tanggal_input_supplier' => $row['tanggal_input_supplier']
                        ));
                }
                echo json_encode($result);
                die();
            }else{
                http_response_code(400);
                $respon['pesan'] = "Terjadi kesalahan pada sistem!";
                echo json_encode($respon);
                die();
            }   
        }
    }elseif($_REQUEST['tag'] == "detail"){
        $id         = $_REQUEST['id_supplier'];
        $data       =  mysqli_query($db, "SELECT * FROM supplier WHERE id_supplier = '$id'")->fetch_assoc();
        if ($data){
                    $result['id_supplier']              = $data['id_supplier'];
                    $result['kode_supplier']            = $data['kode_supplier'];
                    $result['nama_supplier']            = $data['nama_supplier'];
                    $result['alamat_supplier']          = $data['alamat_supplier'];
                    $result['telp_supplier']            = $data['telp_supplier'];
                    $result['status_aktif_supplier']    = $data['status_aktif_supplier'];
                    $result['status_rmv_supplier']      = $data['status_rmv_supplier'];
                    $result['tanggal_input_supplier']   = $data['tanggal_input_supplier'];
                    echo json_encode($result);
                    die();
        }else{
            http_response_code(400);
            $respon['pesan'] = "Terjadi kesalahan pada sistem!";
            echo json_encode($respon);
            die();
        }   
    }else{
        $result = array();
        $limit      = $_REQUEST['limit'];
        $offset     = $_REQUEST['offset'];
        $data   = mysqli_query($db, "SELECT * FROM supplier WHERE status_aktif_supplier = 'Y' AND status_rmv_supplier = 'N' ORDER BY id_supplier ASC LIMIT $offset, $limit");
        if ($data){
            foreach($data as $i => $row){
                array_push($result, array(
                    'id_supplier'       => $row['id_supplier'],
                    'kode_supplier'     => $row['kode_supplier'],
                    'nama_supplier'     => $row['nama_supplier'],
                    'alamat_supplier'   => $row['alamat_supplier'],
                    'telp_supplier'      => $row['telp_supplier'],
                    'status_aktif_supplier' => $row['status_aktif_supplier'],
                    'status_rmv_supplier' => $row['status_rmv_supplier'],
                    'tanggal_input_supplier' => $row['tanggal_input_supplier']
                    ));
            }
            echo json_encode($result);
            die();
        }else{
            http_response_code(400);
            $respon['pesan'] = "Terjadi kesalahan pada sistem!";
            echo json_encode($respon);
            die();
        }
    }
?>