<?php

require __DIR__ . '/../../plugins/escpos/vendor/autoload.php';
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;


require_once '../../templates/koneksi.php';


// ======================================================================
// ======================= CETAK LAPORAN CLOSING ========================
// ======================================================================

if(isset($_GET['cetak_laporan_closing'])){
    
    define('UPLOAD_DIR', 'temp_img/');   
	$img = $_POST['imgBase64'];   
	$img = str_replace('data:image/png;base64,', '', $img);   
	$img = str_replace(' ', '+', $img);   
	$data = base64_decode($img);   
	$nama_foto = "foto-laporan.png";
	$file = UPLOAD_DIR . $nama_foto;   
	$success = file_put_contents($file, $data);   
	print $success ? $file : 'Unable to save the file.';  
	
    // 	CETAK
	$connector = new FilePrintConnector("//localhost/TM-T82");
    $printer = new Printer($connector);
    $printer -> setJustification(Printer::JUSTIFY_CENTER);
    $logo    = EscposImage::load("temp_img/".$nama_foto);
    $printer -> graphics($logo);
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> cut();
    $printer -> close();

	exit();
}