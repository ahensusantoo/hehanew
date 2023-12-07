<?php
session_start();
// $db = new mysqli("localhost","ags","Adm!n495","hov");
$db = new mysqli("localhost","root","","hehanew");
date_default_timezone_set("Asia/Jakarta"); 


// PRINTER
$ip_printer_tiket_masuk = '192.168.72.7'; 
$printer_tiket_masuk = 'TM-T82';

$ip_printer_tiket_photobooth = '192.168.72.9'; 
$printer_tiket_photobooth = 'TM-T82';

$ip_printer_file_photobooth = '192.168.72.9'; 
$printer_file_photobooth = 'TM-T82';
// END PRINTER


	function base_url(){
		return "http://localhost/hehanew/admin/";
	}

	function base_url_newserve(){
		//return "http://localhost/hov_newserve/";
      	return "http://192.168.72.5/hov_newserve//";
	}

	function getApi_record($url, $action){
        global $SConfig;
        $curl = curl_init();

        curl_setopt_array($curl, array(
          	CURLOPT_URL => $SConfig->_link_api,
          	CURLOPT_RETURNTRANSFER => true,
          	CURLOPT_ENCODING => '',
          	CURLOPT_MAXREDIRS => 10,
          	CURLOPT_TIMEOUT => 0,
          	CURLOPT_FOLLOWLOCATION => true,
          	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          	CURLOPT_CUSTOMREQUEST => 'POST',
          	CURLOPT_POSTFIELDS =>'{
            	"act" : "'.$action.'",
            	"token" : "'.$SConfig->_token_api.'"
        	}',
          	CURLOPT_HTTPHEADER => array(
    			'Content-Type: application/json',
    			'Authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImFwaV9uZXdzZXJ2ZWhvdkBnbWFpbC5jb20ifQ.zmaxDEgA23Wzh_Qg86NYLQGjECX9l9OkwSy9keiSQ64',
    			'Cookie: ci_session=oh2qnsd0v9j1ptl8127iam83ged1u45f'
  			),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }

	// proses insert, update, detail, delete data api
  	function CRUD_API($url, $data){
  		$curl = curl_init();

      	curl_setopt_array($curl, array(
        	CURLOPT_URL => $url,
        	CURLOPT_RETURNTRANSFER => true,
        	CURLOPT_ENCODING => '',
        	CURLOPT_MAXREDIRS => 10,
        	CURLOPT_TIMEOUT => 0,
        	CURLOPT_FOLLOWLOCATION => true,
        	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        	CURLOPT_CUSTOMREQUEST => 'POST',
        	CURLOPT_POSTFIELDS => $data,
        	CURLOPT_HTTPHEADER => array(
    			'Content-Type: application/json',
    			'Authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImFwaV9uZXdzZXJ2ZWhvdkBnbWFpbC5jb20ifQ.zmaxDEgA23Wzh_Qg86NYLQGjECX9l9OkwSy9keiSQ64',
    			'Cookie: ci_session=oh2qnsd0v9j1ptl8127iam83ged1u45f'
  			),
      	));

     	$response = curl_exec($curl);

      	curl_close($curl);
      	return json_decode($response, true);
  	}
	
	function random_word($id = 20){
		$pool = '1234567890abcdefghijkmnpqrstuvwxyz';
	
		$word = '';
		for ($i = 0; $i < $id; $i++){
		$word .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
		}
		return $word; 
	}
	
	function random_string($kata = 8){
		$pool = '1234567890abcdefghijkmnpqrstuvwxyz';
	
		$token = '';
		for ($i = 0; $i < $kata; $i++){
		$token .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
		}
		return $token; 
	}
	
    function createSlug($str){
        $delimiter = '-';
        $slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));
        return $slug;
        
        } 

	function get_cookie_check($string){
    	if (isset($_COOKIE[$string])) {
    		return $_COOKIE[$string];
    	}else{
    		$_SESSION['notifikasi']['fail'] = "Harap melakukan login ulang";
            header('Location: '.base_url().'');
			exit();
    	}
    }

	function sessionLogin(){


		if (!isset($_SESSION['id_admin']) AND !isset($_SESSION['username']) AND !isset($_SESSION['role_admin']) AND !isset($_SESSION['shift']) AND !isset($_SESSION['printer']) AND !isset($_SESSION['base_php']) ) {

			if (isset($_COOKIE['id_admin'])) {
				$id_admin = enkripsiDekripsi(get_cookie_check('id_admin'), 'dekripsi');
				$query = "SELECT * FROM admin WHERE id_admin='$id_admin' AND status_rmv_admin='N'";
				$data_admin = $GLOBALS['db']->query($query)->fetch_assoc();

				if (!isset($data_admin)) {
					$_SESSION['notifikasi']['fail'] = "Harap melakukan login ulang";
			        header('Location: '.base_url().'');
	    			exit();
				}

				$id_shift = enkripsiDekripsi(get_cookie_check('shift'), 'dekripsi');
				$nama_shift = @$GLOBALS['db']->query("SELECT nama_shift FROM shift WHERE id_shift='$id_shift' ")->fetch_assoc()['nama_shift'];

				$_SESSION['id_admin'] = $data_admin['id_admin'];
				$_SESSION['username'] = $data_admin['username_admin'];
				$_SESSION['role_admin'] = $data_admin['jabatan_admin'];
				$_SESSION['shift_nama'] = $nama_shift;
				$_SESSION['shift'] = enkripsiDekripsi(get_cookie_check('shift'),'dekripsi');
				$_SESSION['printer'] = antiSQLi(get_cookie_check('printer'));
				$_SESSION['base_php'] = antiSQLi(get_cookie_check('base_php'));
			}else{
				$_SESSION['notifikasi']['fail'] = "Sesi login anda telah berakhir, mohon login kembali";
				header('Location: '.base_url().'');
				exit();
			}
			
		}
		// elseif( !isset($_SESSION['id_merchant_employee']) AND !isset($_SESSION['kd_merchant']) AND !isset($_SESSION['username']) AND !isset($_SESSION['nama_employee']) AND !isset($_SESSION['level_employee']) AND !isset($_SESSION['printer']) ){
		// 	if (isset($_COOKIE['id_merchant_employee'])) {
		// 		$id_merchant_employee = enkripsiDekripsi(get_cookie_check('id_merchant_employee'), 'dekripsi');
		// 		$query = "SELECT * FROM admin WHERE id_admin='$id_admin' AND status_rmv_admin='N'";
		// 		$data_admin = $GLOBALS['db']->query($query)->fetch_assoc();

		// 		if (!isset($data_admin)) {
		// 			$_SESSION['notifikasi']['fail'] = "Harap melakukan login ulang";
		// 	        header('Location: '.base_url().'');
	 //    			exit();
		// 		}

		// 		$id_shift = enkripsiDekripsi(get_cookie_check('shift'), 'dekripsi');
		// 		$nama_shift = @$GLOBALS['db']->query("SELECT nama_shift FROM shift WHERE id_shift='$id_shift' ")->fetch_assoc()['nama_shift'];

		// 		$_SESSION['id_admin'] = $data_admin['id_admin'];
		// 		$_SESSION['username'] = $data_admin['username_admin'];
		// 		$_SESSION['role_admin'] = $data_admin['jabatan_admin'];
		// 		$_SESSION['shift_nama'] = $nama_shift;
		// 		$_SESSION['shift'] = enkripsiDekripsi(get_cookie_check('shift'),'dekripsi');
		// 		$_SESSION['printer'] = antiSQLi(get_cookie_check('printer'));
		// 		$_SESSION['base_php'] = antiSQLi(get_cookie_check('base_php'));
		// 	}else{
		// 		$_SESSION['notifikasi']['fail'] = "Sesi login anda telah berakhir, mohon login kembali";
		// 		header('Location: '.base_url().'');
		// 		exit();
		// 	}
		// }
		else{
		    $role = $_SESSION['role_admin'];
		    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		    
		    if($_SESSION['role_admin'] == "1"){
		        $page_diijinkan = "admin-super.php";
		    }elseif($_SESSION['role_admin'] == "2"){
		        $page_diijinkan = "ticketing.php";
		    }elseif($_SESSION['role_admin'] == "3"){
		        $page_diijinkan = "photobooth.php";
		    }elseif($_SESSION['role_admin'] == "5"){
		        $page_diijinkan = "photobooth-ambil.php";
		    }elseif($_SESSION['role_admin'] == "6"){
		        $page_diijinkan = "general-cashier-ticketing.php";
		    }else{
		    	$_SESSION['notifikasi']['fail'] = "Harap melakukan login ulang";
		        header('Location: '.base_url().'');
    			exit();
		    }
		    
		    if (strpos($actual_link, $page_diijinkan) == false) {
		    	$_SESSION['notifikasi']['fail'] = "Harap melakukan login ulang";
                header('Location: '.base_url().'');
    			exit();
            }
		}

	}
	
	
	function session_gc_stall(){
	    
	    if( !isset($_SESSION['id_merchant_employee']) OR !isset($_SESSION['username']) OR !isset($_SESSION['base_php']) OR !isset($_SESSION['shift']) OR !isset($_SESSION['printer']) ){

	    	if (isset($_COOKIE['id_merchant_employee'])) {
				$id_merchant_employee = enkripsiDekripsi(get_cookie_check('id_merchant_employee'), 'dekripsi');
				$data_gc_stall = $GLOBALS['db']->query("SELECT * FROM merchant_employee WHERE id_merchant_employee='$id_merchant_employee' AND status_aktif_employee='Y' AND level_employee='0' AND status_remove_employee='N'")->fetch_assoc();

				if (!isset($data_gc_stall)) {
					$_SESSION['notifikasi']['fail'] = "Harap melakukan login ulang";
			        header('Location: '.base_url().'');
	    			exit();
				}
				$_SESSION['id_merchant_employee'] = $data_gc_stall['id_merchant_employee'];
				$_SESSION['username'] = $data_gc_stall['username_employee'];
				$_SESSION['shift'] = enkripsiDekripsi(get_cookie_check('shift'),'dekripsi');
				$_SESSION['base_php'] = antiSQLi(get_cookie_check('base_php'));
				$_SESSION['printer'] = antiSQLi(get_cookie_check('printer'));
			}else{
				$_SESSION['notifikasi']['fail'] = "Sesi login anda telah berakhir, mohon login kembali";
				header('Location: '.base_url().'');
				exit();
			}
	    }else{
	        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	        if (strpos($actual_link, 'general-cashier-stall.php') == false) {
                $_SESSION['notifikasi']['fail'] = "Harap melakukan login ulang";
                header('Location: '.base_url().'');
    			exit();
            }
	    }
	    
	}

	function session_admin_tf_ns(){
	    
	    if( !isset($_SESSION['id_admin']) OR !isset($_SESSION['username']) OR !isset($_SESSION['base_php']) OR !isset($_SESSION['role_admin']) OR !isset($_SESSION['stts_login_adm_ns']) ){

	    	if (isset($_COOKIE['id_admin'])) {
				$id_admin = enkripsiDekripsi(get_cookie_check('id_admin'), 'dekripsi');
				$data_admin = $GLOBALS['db']->query("SELECT * FROM admin WHERE id_admin = '$id_admin' AND stts_login_adm_ns = '1' AND status_rmv_admin = 'N' ")->fetch_assoc();
				// print_r($data_admin); die();
				if (empty($data_admin)) {
					$_SESSION['notifikasi']['fail'] = "Tidak ada akses login!!!";
			        header('Location: '.base_url().'admin-tf-ns');
	    			exit();
				}

				$_SESSION['id_admin'] = $data_admin['id_admin'];
		        $_SESSION['username'] = $data_admin['username_admin'];
		        $_SESSION['role_admin'] = $data_admin['jabatan_admin'];
		        $_SESSION['stts_login_adm_ns'] = $data_admin['stts_login_adm_ns'];
		        $_SESSION['base_php'] = 'admin-tf-ns/admin-super.php';
			}else{
				$_SESSION['notifikasi']['fail'] = "Sesi login anda telah berakhir, mohon login kembali";
				header('Location: '.base_url().'admin-tf-ns');
				exit();
			}
	    }else{
	        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	        if (strpos($actual_link, 'admin-super.php') == false) {
                $_SESSION['notifikasi']['fail'] = "Harap melakukan login ulang";
                header('Location: '.base_url().'admin-tf-ns');
    			exit();
            }else{
            	if($_SESSION['stts_login_adm_ns'] != 1 ){
            		$_SESSION['notifikasi']['fail'] = "Anda tidak memiliki hak akses!!!";
	                header('Location: '.base_url().'admin-tf-ns');
	    			exit();
            	}
            }
	    }
	    
	}
	
	
	
    function sessionLoginMerchantEmployee(){
		if (!isset($_SESSION['id_merchant_employee']) OR !isset($_SESSION['kd_merchant']) OR !isset($_SESSION['nama_employee']) OR !isset($_SESSION['level_employee']) ) {
			 $_SESSION['notifikasi']['fail'] = "Sesi login anda telah berakhir, mohon login kembali";
			header('Location: '.base_url().'');
			exit();
		}else{
		    $queryToken = "SELECT token_login
		                    FROM merchant_employee
		                    WHERE kd_merchant = '$_SESSION[kd_merchant]'
		                        AND id_merchant_employee = '$_SESSION[id_merchant_employee]'
		                    ";
                    // echo $queryToken;
            $sql = $GLOBALS['db']->query($queryToken)->fetch_assoc();
            // echo "<script>alert('".$sql['token_login']."')</script>";
            if ($sql['token_login'] != $_SESSION['token_login']){
                session_destroy();
                session_start();
                $_SESSION['notifikasi']['fail'] = "Akun Anda Telah Di Gunakan Pada Device Lain!!";
                
                // echo "<script>alert('Akun Anda Telah Di Gunakan Pada Device Lain!!')</script>";
                header('Location: '.base_url().'');
    			exit();
            }
		}
	}


	function cek_login_role_admin($list_allowed_role){
		if (!isset($_SESSION['id_admin']) OR !isset($_SESSION['username']) OR !isset($_SESSION['role_admin']) OR !isset($_SESSION['shift']) OR !isset($_SESSION['printer']) OR !isset($_SESSION['base_php']) ) {

			if (isset($_COOKIE['id_admin'])) {
				$id_admin = enkripsiDekripsi(get_cookie_check('id_admin'), 'dekripsi');
				$query = "SELECT * FROM admin WHERE id_admin='$id_admin' AND status_rmv_admin='N'";
				$data_admin = $GLOBALS['db']->query($query)->fetch_assoc();

				if (!isset($data_admin)) {
					$_SESSION['notifikasi']['fail'] = "Harap melakukan login ulang";
			        header('Location: '.base_url().'');
	    			exit();
				}

				$id_shift = enkripsiDekripsi(get_cookie_check('shift'), 'dekripsi');
				$nama_shift = @$GLOBALS['db']->query("SELECT nama_shift FROM shift WHERE id_shift='$id_shift' ")->fetch_assoc()['nama_shift'];

				$_SESSION['id_admin'] = $data_admin['id_admin'];
				$_SESSION['username'] = $data_admin['username_admin'];
				$_SESSION['role_admin'] = $data_admin['jabatan_admin'];
				$_SESSION['shift_nama'] = $nama_shift;
				$_SESSION['shift'] = enkripsiDekripsi(get_cookie_check('shift'),'dekripsi');
				$_SESSION['printer'] = antiSQLi(get_cookie_check('printer'));
				$_SESSION['base_php'] = antiSQLi(get_cookie_check('base_php'));
			}else{
				$_SESSION['notifikasi']['fail'] = "Sesi login anda telah berakhir, mohon login kembali";
				header('Location: '.base_url().'');
				exit();
			}
		}

		$role_allowed = explode("/", $list_allowed_role);
		if (!in_array($_SESSION['role_admin'], $role_allowed)) {
		 	$_SESSION['notifikasi']['fail'] = "Harap melakukan login ulang";
	        header('Location: '.base_url().'');
			exit();
		} 
	}

	function antiSQLi($string){
		return mysqli_real_escape_string($GLOBALS['db'], $string);
	}

	function enkripsiDekripsi( $string, $action ) {
        // you may change these values to your own
        $secret_key = '15saf fsFed5&sda6v Pkfasbdu asUK@';
        $secret_iv = '1597864002563154';
    
        $output = false;
        $encrypt_method = 'AES-256-CBC';
        $key = hash( 'sha256', $secret_key );
        $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
    
        if( $action == 'enkripsi' ) {
            $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
        }
        else if( $action == 'dekripsi' ){
            $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
        }
    
        return $output;
    }
    
    function bulanAbjad($bulan){
	   switch ($bulan) {
	     case "1":
	       return 'Januari';
	       break;
	     case "2":
	       return 'Februari';
	       break;
	     case "3":
	       return 'Maret';
	       break;
	     case "4":
	       return 'April';
	       break;
	     case "5":
	       return 'Mei';
	       break;
	     case "6":
	       return 'Juni';
	       break;
	     case "7":
	       return 'Juli';
	       break;
	     case "8":
	       return 'Agustus';
	       break;
	     case "9":
	       return 'September';
	       break;
	     case "10":
	       return 'Oktober';
	       break;
	     case "11":
	       return 'November';
	       break;
	     case "12":
	       return 'Desember';
	       break;


	     default:
	       return 'Bulan tidak terdaftar';
	   }
	 }


	function createID($search, $table, $kode){
		  // CREATE ID
		  $id_primary = $GLOBALS['db']->query("SELECT max($search) as maxKode FROM $table");
		  $id_primary = $id_primary->fetch_assoc();
		  $id_primary = $id_primary['maxKode'];

		  if(substr($id_primary, 2, 8) != date('Ymd')){
		    $noUrut = 0;
		  } else {
		    $noUrut = (int) substr($id_primary, 10, 10);
		    if($noUrut == 9999999999){ $noUrut = 0; } 
		    else { $noUrut++; }
		  }
		  $id_primary = $kode . date('Ymd') . sprintf("%010s", $noUrut);
		  return $id_primary;
		  // END CREATE ID
	}
	
	function createKode($kode, $idmerchant, $kd_merchant_employee){
		  // CREATE ID
		  $id_primary = $GLOBALS['db']->query("SELECT max(id_merchant_transaksi) as maxKode FROM merchant_transaksi WHERE kd_merchant ='$idmerchant' AND kd_merchant_employee = '$kd_merchant_employee'");
		  $id_primary = $id_primary->fetch_assoc();
		  $id_primary = $id_primary['maxKode'];

		  if(substr($id_primary, 2, 8) != date('Ymd')){
		    $noUrut = 0;
		  } else {
		    $noUrut = (int) substr($id_primary, 10, 10);
		    if($noUrut == 9999999999){ $noUrut = 0; } 
		    else { $noUrut++; }
		  }
		  $id_primary = $kode . date('Ymd') . sprintf("%010s", $noUrut);
		  return $id_primary;
		  // END CREATE ID
	}
	
	function createAntrian($idmerchant, $kd_merchant_employee){
		  // CREATE ID
		  $id_primary = $GLOBALS['db']->query("SELECT max(no_antrian) as maxKode, DATE(tgl_input_transaksi) AS tgl
		  FROM merchant_transaksi WHERE kd_merchant ='$idmerchant' AND kd_merchant_employee = '$kd_merchant_employee'");
		  IF(!isset($id_primary)){
		      $nourut = 1;
		  }else{
    		  $id_primary = $id_primary->fetch_assoc();
    		  $nourut     = $id_primary['maxKode'];
    		  $tgl_input  = $id_primary['tgl'];
    
    		  if($tgl_input != date('Y-m-d')){
    		    $nourut = 1;
    		  } else { 
    		    $nourut++; 
    		  }
		  }
		  return $nourut;
		  // END CREATE ID
	}

	function createIDUrut($search, $table, $kode){
		  // CREATE ID
		  $id_primary = $GLOBALS['db']->query("SELECT max($search) as maxKode FROM $table");
		  $id_primary = $id_primary->fetch_assoc();
		  $id_primary = $id_primary['maxKode'];

		  if(substr($id_primary, 2, 8) != date('Ymd')){
		    $noUrut = 0;
		  } else {
		    $noUrut = (int) substr($id_primary, 10, 10);
		    if($noUrut == 9999999999){ $noUrut = 0; } 
		    else { $noUrut++; }
		  }
		  $id_primary = $kode . date('Ymd') . sprintf("%010s", $noUrut);
		  $data['id'] = $id_primary;
		  $data['urutan'] = $noUrut;
		  return $data;
		  // END CREATE ID
	}




	function createID30($search, $table, $kode){
		  // CREATE ID
		  $id_primary = $GLOBALS['db']->query("SELECT max($search) as maxKode FROM $table");
		  $id_primary = $id_primary->fetch_assoc();
		  $id_primary = $id_primary['maxKode'];

		  if(substr($id_primary, 2, 8) != date('Ymd')){
		    $noUrut = 0;
		  } else {
		    $noUrut = (int) substr($id_primary, 10, 30);
		    if($noUrut == 9999999999){ $noUrut = 0; } 
		    else { $noUrut++; }
		  }
		  $id_primary = $kode . date('Ymd') . sprintf("%020s", $noUrut);
		  return $id_primary;
		  // END CREATE ID
	}


	function createID30Urut($search, $table, $kode){
		  // CREATE ID
		  $id_primary = $GLOBALS['db']->query("SELECT max($search) as maxKode FROM $table");
		  $id_primary = $id_primary->fetch_assoc();
		  $id_primary = $id_primary['maxKode'];

		  if(substr($id_primary, 2, 8) != date('Ymd')){
		    $noUrut = 0;
		  } else {
		    $noUrut = (int) substr($id_primary, 10, 30);
		    if($noUrut == 9999999999){ $noUrut = 0; } 
		    else { $noUrut++; }
		  }
		  $id_primary = $kode . date('Ymd') . sprintf("%020s", $noUrut);
		  $data['id'] = $id_primary;
		  $data['urutan'] = $noUrut;
		  return $data;
		  // END CREATE ID
	}

	function spinnerMemuat(){
		return '
			<center><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Memuat</center>
		';
	}

	function spinnerMohonTunggu(){
		return '
			<center><div class="spinner-border spinner-border-sm text-info" role="status"></div> Mohon Tunggu</center>
		';
	}

	function notifikasi($notif){
		if (isset($notif['success'])) {
			echo '
				<div class="alert alert-success alert-dismissible fade show" role="alert">
		          '.$notif["success"].'
		          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
		            <span aria-hidden="true">&times;</span>
		          </button>
		        </div>
			';
		}elseif (isset($notif['fail'])) {
			echo '
				<div class="alert alert-danger alert-dismissible fade show" role="alert">
		          '.$notif["fail"].'
		          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
		            <span aria-hidden="true">&times;</span>
		          </button>
		        </div>
			';
		}

		unset($_SESSION['notifikasi']);
	}
	
	
	function statusJabatanAdmin($kode){
	    switch ($kode) {
            case "1":
                return "Super Admin";
            break;
            case "2":
                return "Kasir Tiket Masuk";
            break;
            case "3":
                return "Kasir Tiket Photobooth";
            break;
            case "4":
                return "Kasir Upload Photobooth";
            break;
            case "5":
                return "Kasir Pembelian File Photobooth";
            break;
            case "6":
                return "General Cashier Ticketing";
            break;
            
            default:
            return "Jabatan tidak terdaftar dalam sistem";
        }
	}

	function bulanKeHuruf($kode){
	    switch ($kode) {
            case "01":
                return "A";
            break;
            case "02":
                return "S";
            break;
            case "03":
                return "D";
            break;
            case "04":
                return "F";
            break;
            case "05":
                return "G";
            break;
            case "06":
                return "H";
            break;
            case "07":
                return "J";
            break;
            case "08":
                return "K";
            break;
            case "09":
                return "L";
            break;
            case "10":
                return "Z";
            break;
            case "11":
                return "X";
            break;
            case "12":
                return "C";
            break;
            
            default:
            return "N";
        }
	}

	function angkaKeHari($kode){
	    switch ($kode) {
            case "1":
                return "Senin";
            break;
            case "2":
                return "Selasa";
            break;
            case "3":
                return "Rabu";
            break;
            case "4":
                return "Kamis";
            break;
            case "5":
                return "Jum'at";
            break;
            case "6":
                return "Sabtu";
            break;
            case "7":
                return "Minggu";
            break;
            
            default:
            return "Hari tidak diketahui";
        }
	}


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
		return $split[2] . ' ' . $bulan[ (int)$split[1] ] . ' ' . $split[0] . ', ' . date("G:i:s", strtotime($tanggal_sblm)). " WIB";
	}

    function tanggal_order($tanggal_sblm){
    	return date("YmdHis", strtotime($tanggal_sblm));
    }
    
    function jam_indo($tanggal_sblm){
    	return date("G:i", strtotime($tanggal_sblm));
    }
    
    function id_ke_struk($string){
        $inisial = substr($string, 0,2);
        $tgl = substr($string, 4,6);
        $tgl = date_format(date_create($tgl),"dmy");
        $num = round(substr($string, 10));
        $no_nota = $inisial = $inisial."-".$tgl."-".$num;
        return $no_nota;
    }
    
    
    function printClient($ip_client, $file, $data, $printer){
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => ''.$ip_client.'/print/'.$file.'.php',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => array('data' => $data ,'printer' => $printer),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
    }

	function get_client_ip() {
	    $ipaddress = '';
	    if (getenv('HTTP_CLIENT_IP'))
	        $ipaddress = getenv('HTTP_CLIENT_IP');
	    else if(getenv('HTTP_X_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
	    else if(getenv('HTTP_X_FORWARDED'))
	        $ipaddress = getenv('HTTP_X_FORWARDED');
	    else if(getenv('HTTP_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_FORWARDED_FOR');
	    else if(getenv('HTTP_FORWARDED'))
	       $ipaddress = getenv('HTTP_FORWARDED');
	    else if(getenv('REMOTE_ADDR'))
	        $ipaddress = getenv('REMOTE_ADDR');
	    else
	        $ipaddress = 'UNKNOWN';
	    return $ipaddress;
	}

	function get_client_ip2() {
	    $ipaddress = '';
	    if (isset($_SERVER['HTTP_CLIENT_IP']))
	        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED']))
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
	        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_FORWARDED']))
	        $ipaddress = $_SERVER['HTTP_FORWARDED'];
	    else if(isset($_SERVER['REMOTE_ADDR']))
	        $ipaddress = $_SERVER['REMOTE_ADDR'];
	    else
	        $ipaddress = 'UNKNOWN';
	    return $ipaddress;
	}

	function pagination($jml_halaman, $hal_aktif, $link_paginaton){
		//PREV BUTTON
   
	   	if ((int)$hal_aktif == "1") {
		   	$html = '<li class="page-item"><a disabled class="page-link" data-abc="true"><i class="fa fa-angle-left"></i></a></li>';
	   	}else{
		   	$html = '<li class="page-item"><a class="page-link" href="'.$link_paginaton.'&hal='. ($hal_aktif-1).'" data-abc="true"><i class="fa fa-angle-left"></i></a></li>';
	   	}
		   
	   	//PAGE PREV 
	   	if ((int)$hal_aktif > 1){
		   	for ($i=(int)$hal_aktif-2; $i < (int)$hal_aktif; $i++) { 
			   	if ((int)$i < (int)$hal_aktif && (int)$i > 0) {
					$html = @$html.'<li class="page-item"><a class="page-link" href="'.$link_paginaton.'&hal='.$i.'" data-abc="true">'.$i.'</a></li>';
			   	}
		   	}
	   	} 
		   
	   	//PAGE ACTIVE
	   	$html = @$html.'<li class="page-item active"><a href="javascript:void(0)" disabled class="page-link">'.$hal_aktif.'</a></li>';

	   	//PAGE NEXT    
	   	if ((int)$hal_aktif < (int)$jml_halaman){
		   	for ($i= (int)$hal_aktif+1; $i < (int)$hal_aktif+3; $i++) { 
			   	if ($i > (int)$hal_aktif && $i <= (int)$jml_halaman) {
				   	$html = @$html.'<li class="page-item"><a class="page-link" href="'.$link_paginaton.'&hal='.$i.'" data-abc="true">'.$i.'</a></li>';
			   	}
		   	}
	   	} 
	   	
	   	//NEXT BUTTON
	   	if ( (int)$hal_aktif == (int)$jml_halaman){
		   	$html = @$html.'<li class="page-item"><a disabled class="page-link" data-abc="true"><i class="fa fa-angle-right"></i></a></li>';
	   	}else{
		   	$html = @$html.'<li class="page-item"><a class="page-link" href="'.$link_paginaton.'&hal='.($hal_aktif+1).'" data-abc="true"><i class="fa fa-angle-right"></i></a></li>';
	   	}

	   	return $html;
   	}
?>