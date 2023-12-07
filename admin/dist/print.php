<?php

require __DIR__ . '/../plugins/escpos/vendor/autoload.php';
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;

include "../plugins/phpqrcode/qrlib.php"; 
require_once '../templates/koneksi.php';


$connector = new FilePrintConnector("//localhost/TM-T82");
$printer = new Printer($connector);
$printer -> setJustification(Printer::JUSTIFY_CENTER);
$logo    = EscposImage::load("5fbca0746db64.png");
$printer -> graphics($logo);
$printer -> text("\n");
$printer -> text("\n");
$printer -> text("\n");
$printer -> cut();
$printer -> close();

