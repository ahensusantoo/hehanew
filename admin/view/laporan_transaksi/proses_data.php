<?php

// echo "<pre>";
// echo print_r($_POST);
// die();

require_once '../../templates/koneksi.php';
sessionLoginMerchantEmployee();

$sess_kd_merchant = $_SESSION['kd_merchant'];
$sess_id_employee = $_SESSION['id_merchant_employee'];

	// ======================================================================
	// ======================= TAMBAH =======================================
	// ======================================================================

if (isset($_POST['button_filter'])){
	$mulai      = antiSQLi($_POST['mulai']);
    $akhir      = antiSQLi($_POST['akhir']);
    $nama_kasir = antiSQLi($_POST['nama_kasir']);
    $id_kasir   = antiSQLi($_POST['id_kasir']);
    if($mulai && $akhir && $nama_kasir && $id_kasir){
    	$nama_kasir = " SELECT nama_employee FROM merchant_employee WHERE id_merchant_employee = '$id_kasir' ";
    	$sql_nama_kaisr = $db->query($nama_kasir)->fetch_assoc()['nama_employee'];

    	$check_data = "
    					SELECT *   
						FROM merchant_transaksi_detail A 
					   	LEFT JOIN merchant_produk B ON A.kd_merchant_produk=B.id_merchant_produk 
					        WHERE A.kd_merchant = 'IM201711000000000000'
					            AND A.kd_merchant_employee='IP201711000000000000'
					            AND A.status_transaksi_detail ='2'
					            AND DATE(A.tgl_input_detail) BETWEEN '2020-11-03' AND '2020-11-04'
    	";

    	$check_data = $db->query($nama_kasir)->fetch_all(MYSQLI_ASSOC);

    	
    }
}


























	// if (isset($_POST['filter'])){
	// 	$filter_tanggal = $_POST['filter_tanggal'];
	// 	$filter_nama = $_POST['filter_nama'];
	// 	$filter_id = $_POST['filter_id'];

	// 	//filter nama tanpa tanggal
	// 	if ( $filter_nama && $filter_tanggal == ""){
	// 		//kondisi filter cuma per nama kasir tanpa tanggal
	// 		$query = "  SELECT m.id_merchant, m.nama_merchant, me.id_merchant_employee, me.nama_employee, me.username_employee, me.status_aktif_employee, mp.nama_produk, mtd.id_merchant_transaksi_detail, mtd.kd_merchant, mtd.kd_merchant_employee, mtd.kd_merchant_produk, mtd.jumlah_produk, mtd.harga_produk, mtd.diskon, mtd.harga_setelah_diskon, mtd.status_transaksi_detail, mtd.tgl_input_detail,
	// 		    SUM(mtd.jumlah_produk) jumlah_produk, SUM(mtd.harga_setelah_diskon) harga_setelah_diskon
	// 		            FROM merchant_transaksi_detail AS mtd
	// 		            LEFT JOIN merchant AS m ON mtd.kd_merchant = m.id_merchant
	// 		            LEFT JOIN merchant_produk AS mp ON mtd.kd_merchant_produk = mp.id_merchant_produk
	// 		            LEFT JOIN merchant_employee AS me ON mtd.kd_merchant_employee = me.id_merchant_employee
	// 		            WHERE mtd.kd_merchant = '$sess_kd_merchant' 
	// 		            		AND mtd.status_transaksi_detail='2' 
	// 		            		AND mtd.kd_merchant_employee = '$filter_id'
	// 		            GROUP BY mtd.kd_merchant_produk
	// 		        ";
	// 		$data_transaksi = $db->query($query)->fetch_all(MYSQLI_ASSOC);

	// 		// echo "<pre>";
	// 		// echo print_r($data_transaksi);
	// 		// die();			
	// 		echo json_encode($data_transaksi);
	// 	}
	// 	//filter nama dan tanggal
	// 	else if ( $filter_nama && $filter_tanggal ){
	// 		//jika ada filter tanggal dan nama kasir jalankan script ini
	// 		$filter_tanggal = $_POST['filter_tanggal'];
	// 		$filter_nama = $_POST['filter_nama'];
	// 		$filter_id = $_POST['filter_id'];

	// 		$pecah_tanggal = explode("_", $filter_tanggal);

	// 		// echo "<pre>";
	// 		// echo ($pecah_tanggal[0]);
	// 		// die();

	// 		$query = "  SELECT m.id_merchant, m.nama_merchant, me.id_merchant_employee, me.nama_employee, me.username_employee, me.status_aktif_employee, mp.nama_produk, mtd.id_merchant_transaksi_detail, mtd.kd_merchant, mtd.kd_merchant_employee, mtd.kd_merchant_produk, mtd.jumlah_produk, mtd.harga_produk, mtd.diskon, mtd.harga_setelah_diskon, mtd.status_transaksi_detail, mtd.tgl_input_detail,
	// 		    SUM(mtd.jumlah_produk) jumlah_produk, SUM(mtd.harga_produk) harga_produk
	// 		            FROM merchant_transaksi_detail AS mtd
	// 		            LEFT JOIN merchant AS m ON mtd.kd_merchant = m.id_merchant
	// 		            LEFT JOIN merchant_produk AS mp ON mtd.kd_merchant_produk = mp.id_merchant_produk
	// 		            LEFT JOIN merchant_employee AS me ON mtd.kd_merchant_employee = me.id_merchant_employee
	// 		            WHERE mtd.kd_merchant = '$sess_kd_merchant' 
	// 		            		AND mtd.status_transaksi_detail='2' 
	// 		            		AND mtd.kd_merchant_employee = '$filter_id'
	// 		            		AND DATE(tgl_input_detail) BETWEEN '$pecah_tanggal[0]' AND '$pecah_tanggal[1]'
	// 		            GROUP BY mtd.kd_merchant_produk
	// 		        ";
	// 		$data_transaksi = $db->query($query)->fetch_all(MYSQLI_ASSOC);

	// 		echo json_encode($data_transaksi);
	// 	}

	// 	//filter tanggal tanpa nama kasir
	// 	else if ( $filter_nama == "" && $filter_tanggal ){
	// 		$filter_tanggal = $_POST['filter_tanggal'];
	// 		$filter_nama = $_POST['filter_nama'];
	// 		$filter_id = $_POST['filter_id'];

	// 		$pecah_tanggal = explode("_", $filter_tanggal);

	// 		// echo "<pre>";
	// 		// echo ($pecah_tanggal[0]);
	// 		// die();

	// 		$query = "  SELECT m.id_merchant, m.nama_merchant, me.id_merchant_employee, me.nama_employee, me.username_employee, me.status_aktif_employee, mp.nama_produk, mtd.id_merchant_transaksi_detail, mtd.kd_merchant, mtd.kd_merchant_employee, mtd.kd_merchant_produk, mtd.jumlah_produk, mtd.harga_produk, mtd.diskon, mtd.harga_setelah_diskon, mtd.status_transaksi_detail, mtd.tgl_input_detail,
	// 		    SUM(mtd.jumlah_produk) jumlah_produk, SUM(mtd.harga_produk) harga_produk
	// 		            FROM merchant_transaksi_detail AS mtd
	// 		            LEFT JOIN merchant AS m ON mtd.kd_merchant = m.id_merchant
	// 		            LEFT JOIN merchant_produk AS mp ON mtd.kd_merchant_produk = mp.id_merchant_produk
	// 		            LEFT JOIN merchant_employee AS me ON mtd.kd_merchant_employee = me.id_merchant_employee
	// 		            WHERE mtd.kd_merchant = '$sess_kd_merchant' 
	// 		            		AND mtd.status_transaksi_detail='2'
	// 		            		AND DATE(tgl_input_detail) BETWEEN '$pecah_tanggal[0]' AND '$pecah_tanggal[1]'
	// 		            GROUP BY mtd.kd_merchant_produk
	// 		        ";
	// 		$data_transaksi = $db->query($query)->fetch_all(MYSQLI_ASSOC);

	// 		echo json_encode($data_transaksi);
	// 	}
	
	// }//end tutup check button filter

?>