<?php 
$ip_server = $_SERVER['SERVER_ADDR']; 

if($ip_server) {
    $respon['ip_address']  = $ip_server;
    $respon['pesan'] = "Berhasil menyimpan IP jaringan ".$ip_server."";
    echo json_encode($respon);
}
	
exit;
 ?>