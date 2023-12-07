<?php 
		    
	$jabatan		= $_GET['jabatan'];
	$result 		= array();
	
	if($jabatan == 0) {
	    
	    array_push($result,array(
				'judul'					=> 'Transaksi',
				'keterangan_singkat'	=> 'Tambah Transaksi',
				'keterangan_komplit'	=> 'Fitur untuk melakukan tambah Transaksi ',
				'tag_menu'				=> 'transaksi',
				'gambar'				=> 'data_file/transaksi.png',
				'icon'				    => 'data_file/transaksi.png'				
				));
				
				
				
		array_push($result,array(
				'judul'					=> 'Bayar tagihan',
				'gambar'				=> 'data_file/bayartagihan.png',
				'icon'				    => 'data_file/bayartagihan.png',
				'keterangan_singkat'	=> 'Bayar tagihan',
				'keterangan_komplit'	=> 'Fitur untuk melakukan tambah Transaksi ',
				'tag_menu'				=> 'tagihan'
				));	
				
		array_push($result,array(
				'judul'					=> 'Keranjang',
				'keterangan_singkat'	=> 'Keranjang',
				'keterangan_komplit'	=> 'Fitur untuk melakukan tambah Transaksi ',
				'tag_menu'				=> 'keranjang',
				'gambar'				=> 'data_file/keranjang.png',
				'icon'				    => 'data_file/keranjang.png'				
				));		
		
				
		array_push($result,array(
				'judul'					=> 'Barang',
				'keterangan_singkat'	=> 'Tambah barang dan lihat barang',
				'keterangan_komplit'	=> 'Fitur untuk melakukan tambah barang dan lihat barang oleh superadmin',
				'tag_menu'				=> 'barang',
				'gambar'				=> 'data_file/barang.png',
				'icon'				    => 'data_file/barang.png'				
				));
		
		array_push($result,array(
				'judul'					=> 'Supplier',
				'keterangan_singkat'	=> 'Kelola Supplier',
				'keterangan_komplit'	=> 'Fitur untuk mengelola Supplier ',
				'tag_menu'				=> 'supplier',
				'gambar'				=> 'data_file/transaksi.png',
				'icon'				    => 'data_file/transaksi.png'				
				));
				
		array_push($result,array(
				'judul'					=> 'Kategori',
				'keterangan_singkat'	=> 'Tambah kategori dan lihat kategori',
				'keterangan_komplit'	=> 'Fitur untuk melakukan tambah barang dan lihat barang oleh superadmin',
				'tag_menu'				=> 'kategori',
				'gambar'				=> 'data_file/kategori.png',
				'icon'				    => 'data_file/kategori.png'				
				));		
				
	  
		
		array_push($result,array(
				'judul'					=> 'Anggota',
				'keterangan_singkat'	=> 'Tambah Anggota dan lihat anggota',
				'keterangan_komplit'	=> 'Fitur untuk melakukan tambah Anggota dan lihat anggota ',
				'tag_menu'				=> 'anggota',
				'gambar'				=> 'data_file/anggota.png',
				'icon'				    => 'data_file/anggota.png'				
				));
				
		array_push($result,array(
				'judul'					=> 'Laporan',
				'keterangan_singkat'	=> 'Laporan transaksi',
				'keterangan_komplit'	=> 'Rekapan transaksi yang bisa anda akses dan filer baik menit jam hari bahkan tahun',
				'tag_menu'				=> 'laporan',
				'gambar'				=> 'data_file/laporan.png',
				'icon'				    => 'data_file/laporan.png'
				));
				
		array_push($result,array(
				'judul'					=> 'Mutasi',
				'keterangan_singkat'	=> 'Mutasi keluar masuk',
				'keterangan_komplit'	=> 'Mutasi keluar masuk barang kasir',
				'tag_menu'				=> 'mutasi',
				'gambar'				=> 'data_file/mutasi.png',
				'icon'				    => 'data_file/mutasi.png'
				));
				
		array_push($result,array(
				'judul'					=> 'Riwayat Transaksi',
				'keterangan_singkat'	=> 'Panduan aplikasi',
				'keterangan_komplit'	=> 'panduan aplikasi',
				'tag_menu'				=> 'riwayat',
				'gambar'				=> 'data_file/history.png',
				'icon'				    => 'data_file/history.png'
				));		
        
	echo json_encode(array('result'	=> $result));
            
	}elseif($jabatan == 1) {
	    
	    array_push($result,array(
				'judul'					=> 'Transaksi',
				'keterangan_singkat'	=> 'Tambah Transaksi',
				'keterangan_komplit'	=> 'Fitur untuk melakukan tambah Transaksi ',
				'tag_menu'				=> 'transaksi',
				'gambar'				=> 'data_file/transaksi.png',
				'icon'				    => 'data_file/transaksi.png'				
				));
				
				
				
		array_push($result,array(
				'judul'					=> 'Bayar tagihan',
				'gambar'				=> 'data_file/bayartagihan.png',
				'icon'				    => 'data_file/bayartagihan.png',
				'keterangan_singkat'	=> 'Bayar tagihan',
				'keterangan_komplit'	=> 'Fitur untuk melakukan tambah Transaksi ',
				'tag_menu'				=> 'tagihan'
				));	
				
		array_push($result,array(
				'judul'					=> 'Keranjang',
				'keterangan_singkat'	=> 'Keranjang',
				'keterangan_komplit'	=> 'Fitur untuk melakukan tambah Transaksi ',
				'tag_menu'				=> 'keranjang',
				'gambar'				=> 'data_file/keranjang.png',
				'icon'				    => 'data_file/keranjang.png'				
				));		
		
				
		array_push($result,array(
				'judul'					=> 'Barang',
				'keterangan_singkat'	=> 'Tambah barang dan lihat barang',
				'keterangan_komplit'	=> 'Fitur untuk melakukan tambah barang dan lihat barang oleh superadmin',
				'tag_menu'				=> 'barang',
				'gambar'				=> 'data_file/barang.png',
				'icon'				    => 'data_file/barang.png'				
				));
				
		array_push($result,array(
				'judul'					=> 'Supplier',
				'keterangan_singkat'	=> 'Kelola Supplier',
				'keterangan_komplit'	=> 'Fitur untuk mengelola Supplier ',
				'tag_menu'				=> 'supplier',
				'gambar'				=> 'data_file/transaksi.png',
				'icon'				    => 'data_file/transaksi.png'				
				));
				
		array_push($result,array(
				'judul'					=> 'Kategori',
				'keterangan_singkat'	=> 'Tambah kategori dan lihat kategori',
				'keterangan_komplit'	=> 'Fitur untuk melakukan tambah barang dan lihat barang oleh superadmin',
				'tag_menu'				=> 'kategori',
				'gambar'				=> 'data_file/kategori.png',
				'icon'				    => 'data_file/kategori.png'				
				));		
				
	  
		
		array_push($result,array(
				'judul'					=> 'Anggota',
				'keterangan_singkat'	=> 'Tambah Anggota dan lihat anggota',
				'keterangan_komplit'	=> 'Fitur untuk melakukan tambah Anggota dan lihat anggota ',
				'tag_menu'				=> 'anggota',
				'gambar'				=> 'data_file/anggota.png',
				'icon'				    => 'data_file/anggota.png'				
				));
				
		array_push($result,array(
				'judul'					=> 'Laporan',
				'keterangan_singkat'	=> 'Laporan transaksi',
				'keterangan_komplit'	=> 'Rekapan transaksi yang bisa anda akses dan filer baik menit jam hari bahkan tahun',
				'tag_menu'				=> 'laporan',
				'gambar'				=> 'data_file/laporan.png',
				'icon'				    => 'data_file/laporan.png'
				));
				
		array_push($result,array(
				'judul'					=> 'Mutasi',
				'keterangan_singkat'	=> 'Mutasi keluar masuk',
				'keterangan_komplit'	=> 'Mutasi keluar masuk barang kasir',
				'tag_menu'				=> 'mutasi',
				'gambar'				=> 'data_file/mutasi.png',
				'icon'				    => 'data_file/mutasi.png'
				));
				
		array_push($result,array(
				'judul'					=> 'Riwayat Transaksi',
				'keterangan_singkat'	=> 'Panduan aplikasi',
				'keterangan_komplit'	=> 'panduan aplikasi',
				'tag_menu'				=> 'riwayat',
				'gambar'				=> 'data_file/history.png',
				'icon'				    => 'data_file/history.png'
				));		
        
	echo json_encode(array('result'	=> $result));
            
	}else{
	    
	    array_push($result,array(
				'judul'					=> 'Transaksi',
				'keterangan_singkat'	=> 'Tambah Transaksi',
				'keterangan_komplit'	=> 'Fitur untuk melakukan tambah Transaksi ',
				'tag_menu'				=> 'transaksi',
				'gambar'				=> 'data_file/transaksi.png',
				'icon'				    => 'data_file/transaksi.png'				
				));	
			
		array_push($result,array(
				'judul'					=> 'Bayar tagihan',
				'keterangan_singkat'	=> 'Bayar tagihan',
				'keterangan_komplit'	=> 'Fitur untuk melakukan tambah Transaksi ',
				'tag_menu'				=> 'tagihan',
				'gambar'				=> 'data_file/bayartagihan.png',
				'icon'				    => 'data_file/bayartagihan.png'				
				));	
				
		array_push($result,array(
				'judul'					=> 'Keranjang',
				'keterangan_singkat'	=> 'Keranjang',
				'keterangan_komplit'	=> 'Fitur untuk melakukan tambah Transaksi ',
				'tag_menu'				=> 'keranjang',
				'gambar'				=> 'data_file/keranjang.png',
				'icon'				    => 'data_file/keranjang.png'				
				));		
				
        array_push($result,array(
				'judul'					=> 'Laporan',
				'keterangan_singkat'	=> 'Laporan transaksi',
				'keterangan_komplit'	=> 'Rekapan transaksi yang bisa anda akses dan filer baik menit jam hari bahkan tahun',
				'tag_menu'				=> 'laporan',
				'gambar'				=> 'data_file/laporan.png',
				'icon'				    => 'data_file/laporan.png'
				));
				
				
		array_push($result,array(
				'judul'					=> 'Riwayat Transaksi',
				'keterangan_singkat'	=> 'Panduan aplikasi',
				'keterangan_komplit'	=> 'panduan aplikasi',
				'tag_menu'				=> 'riwayat',
				'gambar'				=> 'data_file/history.png',
				'icon'				    => 'data_file/history.png'
				));			
				
        
	echo json_encode(array('result'	=> $result));
	}

 ?>