<?php
// file ini berfungsi untuk logout (keluar dari sistem)

// memulai 
session_start();
if ( @$_GET['action'] == "admin-ns" ) {
	if(isset($_SESSION['session_admin_byox'])){
		session_destroy();
		if (isset($_SERVER['HTTP_COOKIE'])) {
		    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
		    foreach($cookies as $cookie) {
		        $parts = explode('=', $cookie);
		        $name = trim($parts[0]);
		        setcookie($name, '', time()-1000);
		        setcookie($name, '', time()-1000, '/');
		    }
		}
		echo "<script> alert('Berhasil Logout');
		window.location.href='../admin-tf-ns' </script>";
	} else {
		session_destroy();
		if (isset($_SERVER['HTTP_COOKIE'])) {
		    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
		    foreach($cookies as $cookie) {
		        $parts = explode('=', $cookie);
		        $name = trim($parts[0]);
		        setcookie($name, '', time()-1000);
		        setcookie($name, '', time()-1000, '/');
		    }
		}
		echo "<script> alert('Berhasil Logout');
		window.location.href='../admin-tf-ns' </script>";
	}
}else{
	if(isset($_SESSION['id_admin'])){
		session_destroy();
		if (isset($_SERVER['HTTP_COOKIE'])) {
		    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
		    foreach($cookies as $cookie) {
		        $parts = explode('=', $cookie);
		        $name = trim($parts[0]);
		        setcookie($name, '', time()-1000);
		        setcookie($name, '', time()-1000, '/');
		    }
		}
		echo "<script> alert('Berhasil Logout');
		window.location.href='../index.php' </script>";
	} else {
		session_destroy();
		if (isset($_SERVER['HTTP_COOKIE'])) {
		    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
		    foreach($cookies as $cookie) {
		        $parts = explode('=', $cookie);
		        $name = trim($parts[0]);
		        setcookie($name, '', time()-1000);
		        setcookie($name, '', time()-1000, '/');
		    }
		}
		echo "<script> alert('Berhasil Logout');
		window.location.href='../index.php' </script>";
	}
}



?>