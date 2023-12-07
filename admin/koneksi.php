<?php
ob_start();
if(file_exists('templates/koneksi.php')){
	require_once('templates/koneksi.php');
} elseif(file_exists('../templates/koneksi.php')){
	require_once('../templates/koneksi.php');
} elseif(file_exists('../../templates/koneksi.php')){
	require_once('../../templates/koneksi.php');
} elseif(file_exists('../../../templates/koneksi.php')){
	require_once('../../../templates/koneksi.php');
} elseif(file_exists('../../../../templates/koneksi.php')){
	require_once('../../../../templates/koneksi.php');
}

// $urlmain = "https://localhost/byox.co.id/";
// $urlgambar = $urlmain."images/";

function tanggal_indo($tanggal_sblm){
	$tanggal = date("Y-m-j", strtotime($tanggal_sblm));

	$bulan = array (1 =>   'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
	$split = explode('-', $tanggal);
	return $split[2] . ' ' . $bulan[ (int)$split[1] ] . ' ' . $split[0];
}

function tanggal_jam_indo($tanggal_sblm){
	$tanggal = date("Y-m-j", strtotime($tanggal_sblm));

	$bulan = array (1 =>   'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
	$split = explode('-', $tanggal);
	return $split[2] . ' ' . $bulan[ (int)$split[1] ] . ' ' . $split[0] . '<br>' . date("G:i:s", strtotime($tanggal_sblm));
}

function tanggal_order($tanggal_sblm){
	return date("YmdHis", strtotime($tanggal_sblm));
}

function jam_indo($tanggal_sblm){
	return date("G:i", strtotime($tanggal_sblm));
}

$hari_indo = array ( 1 => 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu');
?>