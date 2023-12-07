<?php 
require_once('../../templates/koneksi.php');

$id_merchant          = $_GET['id_merchant'];
$id_merchant_employee = $_GET['id_merchant_employee'];
$kode_promo           = $_GET['kode_promo'];
$jumlahsubtotal       = $_GET['jumlahsubtotal'];

$sql    = mysqli_query($db,"SELECT a.catatan_pesanan, a.id_merchant_transaksi_detail, a.kd_merchant, a.kd_merchant_produk, b.status_konsi, b.nama_produk, a.jumlah_produk, b.gambar_produk, a.harga_produk, a.diskon, a.harga_setelah_diskon, a.tgl_input_detail
    FROM merchant_transaksi_detail a
    JOIN merchant_produk b ON a.kd_merchant_produk = b.id_merchant_produk
    WHERE a.kd_merchant = '$id_merchant' AND a.kd_merchant_employee = '$id_merchant_employee' AND a.kd_merchant_transaksi = '' AND a.status_transaksi_detail != '3'"); 
$result = array();
while($row = mysqli_fetch_array($sql)){
    $getProduk = mysqli_query($db,"SELECT * FROM promo a JOIN promo_setting_klaim b ON a.kd_promo_setting = b.kd_promo_setting WHERE DATE(a.expired_promo) = CURDATE() AND a.kode_promo = '$kode_promo' AND b.kd_merchant_produk = '$row[kd_merchant_produk]'
     AND a.status_klaim_promo = 'N'")->fetch_assoc();
    $jumlahsubtotal2 = $jumlahsubtotal - $getProduk['nominal_promo'];
    if (!empty($getProduk)){
      array_push($result,array(
        'nama_produk'              => $row['nama_produk'],
        'kd_merchant_produk'       => $row['kd_merchant_produk'],
        'potongan'                 => (double)$getProduk['nominal_promo'],
        'jumlahsubtotal'           => (string)$jumlahsubtotal2,
        'jumlahsubtotal_format'    => "Rp" . number_format((double)$jumlahsubtotal2,0,',','.'),
    ));
  } 
}

if(isset($result[0])) {

   echo json_encode($result);
   $query = mysqli_query($db, "UPDATE promo SET status_klaim_promo = 'Y', promo_diklaim_oleh = '$id_merchant_employee', nominal_ditukar_promo = '$getProduk[nominal_promo]' WHERE kode_promo = '$kode_promo'");

}  else{
    http_response_code(400);
    $respon['pesan'] = "Tidak ada promo yang ditampilkan!\nKlik `Mengerti` untuk menutup pesan ini";
    $respon['kode']  = "0";
    echo json_encode($respon);
}

mysqli_close($db);

?>