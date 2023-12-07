<?php

require __DIR__ . '/../../plugins/escpos/vendor/autoload.php';
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;


require_once '../../templates/koneksi.php';

// echo "<pre>";
// print_r($_POST);
// $base_link = base_url();
// $nama_foto = $_POST['nama_file'].".png";
// $x = array('printer' => $_POST['printer'],'nama_file' => $nama_foto ,'base_url' => $base_link);
// print_r($x);
// echo 'http://'.$_POST['ip'].'/print/cetak_laporan_closing.php <br>';
// exit();

// ======================================================================
// ======================= CETAK LAPORAN CLOSING ========================
// ======================================================================

if(isset($_GET['cetak_laporan_closing'])){
    
    define('UPLOAD_DIR', 'temp_img/');   
    $img = $_POST['imgBase64'];   
    $img = str_replace('data:image/png;base64,', '', $img);   
    $img = str_replace(' ', '+', $img);   
    $data = base64_decode($img);   
    $nama_foto = $_POST['nama_file'].".png";
    $file = UPLOAD_DIR . $nama_foto;   
    $success = file_put_contents($file, $data);    
    // print $success ? $file : 'Unable to save the file.';
  //print_r("<pre>"); print_r($file); die(); 
    
    //  CETAK
//  $connector = new FilePrintConnector("//localhost/TM-T82");
//     $printer = new Printer($connector);
//     $printer -> setJustification(Printer::JUSTIFY_CENTER);
//     $logo    = EscposImage::load("temp_img/".$nama_foto);
//     $printer -> graphics($logo);
//     $printer -> text("\n");
//     $printer -> text("\n");
//     $printer -> text("\n");
//     $printer -> cut();
//     $printer -> close();
    
    // ===================================

    
    // $connector = new FilePrintConnector("//localhost/TM-T82");
    // $printer = new Printer($connector);
    // $printer -> setJustification(Printer::JUSTIFY_CENTER);
    // $tux = EscposImage::load("temp_img/".$nama_foto, false);
    // $printer -> graphics($tux);
    // $printer -> text("\n");
    // $printer -> text("\n");
    // $printer -> cut();
    // $printer -> close();
    
    
    // ==========================================
    
    // $connector = new FilePrintConnector("//localhost/TM-T82");
    // $printer = new Printer($connector);
    
    // try {
    //     $printer -> setJustification(Printer::JUSTIFY_CENTER);
    //     $tux = EscposImage::load("temp_img/".$nama_foto, false);
    //     $printer -> bitImage($tux);
    //     $printer -> feed();
    // } catch (Exception $e) {
    //     /* Images not supported on your PHP, or image file not found */
    //     $printer -> text($e -> getMessage() . "\n");
    // }
    
    // $printer -> cut();
    // $printer -> close();

    $base_link = base_url();

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'http://'.$_POST['ip'].'/print/cetak_laporan_closing.php',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => array('printer' => $_POST['printer'],'nama_file' => $nama_foto ,'base_url' => $base_link),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    echo $response;

    unlink(UPLOAD_DIR . $nama_foto);


    exit();
}

if(isset($_GET['sync'])){
  //die('a');
  $date = date('Y-m-d');
  $sql[] = $db->query("
  	UPDATE photobooth_transaksi a SET a.status_paket_spot = '1' WHERE DATE(a.tanggal_photobooth_transaksi) >= '$date' AND a.total_transaksi = 0
  ");
  if(in_array(false, $sql)){
        $db->rollback();
    	$msg = [
        	'status' => false,
        ];
        //$_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        //header('Location: '.base_url().'photobooth.php?page=laporan&action=kelola');
        //exit();
    }else{
        $db->commit();
        //$_SESSION['notifikasi']['success'] = "Photobooth berhasil perbarui";
        //header('Location: '.base_url().'photobooth.php?page=laporan&action=kelola');
        //exit();
        $msg = [
        	'status' => true,
        ];
    }
  	echo json_encode($msg);
}