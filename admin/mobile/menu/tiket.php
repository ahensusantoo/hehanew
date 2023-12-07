<?php 
		    
	$jabatan		= $_GET['jabatan'];
	$result 		= array();
	
	if($jabatan == 1) {
		
		array_push($result,array(
				'judul'					=> 'Tiket',
				'keterangan_singkat'	=> 'Scan tiket heha sky view',
				'keterangan_komplit'	=> 'Petugas akan memindai tiket heha yang dibawa oleh pembeli setelah dicetak dari print tiket',
				'tag_menu'				=> 'tiket',
				'gambar'				=> 'data_file/scantiket.png',
				'icon'				    => 'data_file/scan1.png'
				));
				
		array_push($result,array(
				'judul'					=> 'Photobooth',
				'keterangan_singkat'	=> 'Scan tiket photobooth heha sky view',
				'keterangan_komplit'	=> 'Petugas akan memindai tiket heha yang dibawa oleh pembeli setelah dicetak dari print tiket',
				'tag_menu'				=> 'photobooth',
				'gambar'				=> 'data_file/scanphoto.png',
				'icon'				    => 'data_file/scan1.png'				
				));		
	    
        
	echo json_encode(array('result'	=> $result,
	'background' => 'data_file/banner.jpg'));
            
	}else{
                http_response_code(400);
                $respon['pesan'] = "Tidak id anggota terkait!\nKlik `Mengerti` untuk menutup pesan ini";
                echo json_encode($respon);
	}

 ?>