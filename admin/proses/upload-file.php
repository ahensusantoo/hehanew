<?php

function validateFileUploadGambar($fileUpload, $jenis, $allowNull){
	$allowedFormat = array("png", "jpg", "jpeg", "PNG", "JPG", "JPEG");
	$maxFileSize = 1000000;

	$fileUploadName 	= $_FILES[$fileUpload]['name'];
	$fileUploadSize 	= $_FILES[$fileUpload]['size'];
	$fileUploadFormat 	= pathinfo($fileUploadName, PATHINFO_EXTENSION);
	if($allowNull == true && $fileUploadName == null){
		return;
	}
	if(!in_array($fileUploadFormat, $allowedFormat)){
		$_SESSION['error'][] = "Format File ".$jenis." Harus png atau jpeg atau jpg";
	}
	if ($fileUploadSize > $maxFileSize){
		$_SESSION['error'][] = "Ukuran File ".$jenis." Melebihi 500 kb";
	}
	return;
}

function validateFileUploadGambarJPG($fileUpload, $jenis, $allowNull){
	$allowedFormat = array("jpg", "JPG");
	$maxFileSize = 513000;

	$fileUploadName 	= $_FILES[$fileUpload]['name'];
	$fileUploadSize 	= $_FILES[$fileUpload]['size'];
	$fileUploadFormat 	= pathinfo($fileUploadName, PATHINFO_EXTENSION);
	if($allowNull == true && $fileUploadName == null){
		return;
	}
	if(!in_array($fileUploadFormat, $allowedFormat)){
		$_SESSION['error'][] = "Format File ".$jenis." Harus .jpg";
	}
	if ($fileUploadSize > $maxFileSize){
		$_SESSION['error'][] = "Ukuran File ".$jenis." Melebihi 500 kb";
	}
	return;
}

function validateFileUpload($fileUpload, $jenis, $allowNull){
	$allowedFormat = array("png", "jpg", "jpeg", "pdf");
	$maxFileSize = 513000;

	$fileUploadName 	= $_FILES[$fileUpload]['name'];
	$fileUploadSize 	= $_FILES[$fileUpload]['size'];
	$fileUploadFormat 	= pathinfo($fileUploadName, PATHINFO_EXTENSION);
	if($allowNull == true && $fileUploadName == null){
		return;
	}
	if(!in_array($fileUploadFormat, $allowedFormat)){
		$_SESSION['error'][] = "Format File <strong>".$jenis."</strong> Harus <strong>pdf</strong> atau <strong>jpg</strong>";
	}
	if ($fileUploadSize > $maxFileSize){
		$_SESSION['error'][] = "Ukuran File <strong>".$jenis."</strong> Melebihi 500 kb";
	}
	return;
}

function uploadFile($fileUpload, $folder){
	$path = "../images/".$folder;
	if (!file_exists($path)){mkdir($path);}
	$fileUploadName = date('zHis').substr(round(microtime(true) * 1000), 10, 1).'-'.$_FILES[$fileUpload]['name'];
	if(move_uploaded_file($_FILES[$fileUpload]['tmp_name'], $path.'/'.$fileUploadName)){
		return $fileUploadName;
	} else{
		$_SESSION['error'][] = "Gagal Upload ".$folder.". Ada Kesalahan pada server, Silahkan Coba Lagi";			
	}
}

?>