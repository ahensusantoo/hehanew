<?php
include("../../koneksi.php");
$tahun = $_POST["tahun"];
$bulan = $_POST["bulan"];

$sql = mysqli_query($db,"SELECT hari_libur FROM hari_libur WHERE tahun_bulan = '$tahun-$bulan'");
$query = mysqli_fetch_array($sql);

if(!empty($query['hari_libur'])){
  echo $query['hari_libur'];
} else {
  echo '0';
}

?>