<?php

	require_once '../../../templates/koneksi.php';

	if (isset($_POST['sync_two_server'])){
		$tgl_filter = $_POST['tgl_filter'];
		
		//pertama check sudah melakukan SYNC belum pada tgl tertentu
		$status_sync = $db->query("
	        SELECT * 
	        FROM stt_tf_tiket A 
	        LEFT JOIN admin B ON A.kd_user_sncy_tf = B.id_admin
	        WHERE DATE(A.tanggal_tf) = '$tgl_filter' 
	        ORDER BY id_stt_tf DESC
	    ")->fetch_assoc();

	    if( !empty($status_sync) ){
	    	if($status_sync['status_tf'] == '1' ){
		    	$respon = [
	    			'status' => false,
	    			'pesan'  => "Periode ".tanggal_indo($tgl_filter)." Anda sudah melakukan sinkronisasi",
	    			'status_code' => 400,
	    		];
	    		echo json_encode($respon);
	        	die();
	    	}
	    }

		//ambil semua data yg belum di insert ke sana dan data yg pernah di update
    	$sync_tiket_transaksi = $db->query("
    		SELECT a.*, b.*, b.tanggal_transaksi as tanggal_transaksi_tiket, c.id_revisi, c.tanggal_revisi, c.keterangan_revisi, c.kd_admin as kd_admin_revisi, c.kd_transaksi as kd_transaksi_revisi, c.jenis_revisi, c.nominal_awal, c.nominal_akhir 
    		FROM transaksi a
    		JOIN tiket b ON a.id_transaksi = b.kd_transaksi
    		LEFT JOIN revisi_transaksi c ON a.id_transaksi = c.kd_transaksi
    		WHERE DATE(a.tanggal_transaksi) = '$tgl_filter'
    			AND (a.stt_insrt_tf_tiket = '0' 
    			OR a.stt_updt_tf_tiket = '1')
        ")->fetch_all(MYSQLI_ASSOC);

        // print_r("<pre>"); print_r($sync_tiket_transaksi); die();

    	if( empty($sync_tiket_transaksi) ){
    		$respon = [
    			'status' => false,
    			'pesan'  => "Tidak terdapat data pada periode ".tanggal_indo($tgl_filter)." ini",
    			'status_code' => 400,
    		];
    		echo json_encode($respon);
        	die();
    	}

    	$response_newserver = @CRUD_API(base_url_newserve()."api/tiket_masuk/sync_tiket_transaksi" ,json_encode([
            'act'                   => "sync_tiket_transaksi",
            'sync_tiket_transaksi'  => $sync_tiket_transaksi,
        ]));

        if($response_newserver['status'] == true){
            
            // proses update status.
            $finish_query = $db->query("UPDATE transaksi a, ( SELECT c.id_transaksi FROM transaksi c WHERE DATE(c.tanggal_transaksi) = '$tgl_filter' AND (c.stt_insrt_tf_tiket = '0' OR c.stt_updt_tf_tiket = '1') ) AS b SET a.stt_insrt_tf_tiket = '1', a.stt_updt_tf_tiket = '0' WHERE a.id_transaksi = b.id_transaksi; ");

        	if($finish_query){
        		$respon = [
	    			'status' => true,
	    			'pesan'  => "Berhasil melakukan sinkronisasi",
	    			'status_code' => 200,
	    		];
        	}else{
        		$respon = [
	    			'status' => false,
	    			'pesan'  => "Kesalahann System silahkan coba kembali",
	    			'status_code' => 400,
	    		];
        	}

        	echo json_encode($respon);
        	die();

        }else{
        	$respon = [
    			'status' => false,
    			'pesan'  => "Terjadi kesalahan pada server duplicate, silahkan coba kembali",
    			'status_code' => 400,
    		];
    		echo json_encode($respon);
        	die();
        }

	}

	if(isset($_POST['proses_two_server'])){
		$tgl_filter = $_POST['tgl_filter'];
		
		//pertama check sudah melakukan SYNC belum pada tgl tertentu
		$status_sync = $db->query("
	        SELECT * 
	        FROM stt_tf_tiket A
	        WHERE DATE(A.tanggal_tf) = '$tgl_filter' 
	        ORDER BY id_stt_tf DESC
	    ")->fetch_assoc();

	    if( !empty($status_sync) ){
	    	if($status_sync['status_tf'] == '1' ){
		    	$respon = [
	    			'status' => false,
	    			'pesan'  => "Periode ".tanggal_indo($tgl_filter)." Anda sudah melakukan sinkronisasi",
	    			'status_code' => 400,
	    		];
	    		echo json_encode($respon);
	        	die();
	    	}
	    }


	    $count_pendapatan_tiket = $db->query("SELECT COALESCE(SUM(A.total_transaksi), 0) as total_pendapatan_bersih
                FROM transaksi A
                WHERE DATE(A.tanggal_transaksi) = '$tgl_filter'
                AND A.status_transaksi != '3' ")->fetch_assoc()['total_pendapatan_bersih'];

	    // perhitungan 30 persen
	    $nominal_cut = round( (int)$count_pendapatan_tiket * 0.3, 2);


	    $record_transaksi_tiket =  $db->query("SELECT *
                FROM transaksi A
                WHERE DATE(A.tanggal_transaksi) = '$tgl_filter'
                -- AND A.status_transaksi != '3'
               	ORDER BY id_transaksi DESC ")->fetch_all(MYSQLI_ASSOC);

	    $id_to_delete = "";
	    foreach( $record_transaksi_tiket as $key => $value ){
	    	if( $value['total_transaksi'] > $nominal_cut  ){
	    		if($value['status_transaksi'] != '3'){
		    		$id_to_delete = $id_to_delete."'".$value['id_transaksi']."'".",";
		    		break;
	    		}else{
	    			$id_to_delete = $id_to_delete."'".$value['id_transaksi']."'".",";
	    		}
	    	}else{
	    		if($value['status_transaksi'] != '3'){
		    		$nominal_cut -=   $value['total_transaksi'];
		    		$id_to_delete = $id_to_delete."'".$value['id_transaksi']."'".",";
	    		}else{
		    		$id_to_delete = $id_to_delete."'".$value['id_transaksi']."'".",";
	    		}

	    	}
	    	// $cut_plus += $value['total_transaksi'];
	    }


	    $db->begin_transaction();

		    $list_id_delete = substr($id_to_delete, 0, -1);

		    //table transaksi kepala
		    $stmt[] = $db->query("
		    	DELETE FROM transaksi WHERE id_transaksi IN ($list_id_delete)
		    	-- UPDATE transaksi SET test_delete='1' WHERE id_transaksi IN ($list_id_delete)
		    ");

		    // table badan
		    $stmt[] = $db->query("
		    	DELETE FROM tiket WHERE id_transaksi IN ($list_id_delete)
		    	-- UPDATE tiket SET status_delete='1' WHERE kd_transaksi IN ($list_id_delete)
		    ");
			
			// jika ada pembatalan
			$stmt[] = $db->query("
		    	DELETE FROM revisi_transaksi WHERE id_transaksi IN ($list_id_delete)
		    	-- UPDATE revisi_transaksi SET test_delete='1' WHERE kd_transaksi IN ($list_id_delete)
		    ");

			// ubah status cutting
		    $stmt[] = $db->query("
		    	UPDATE stt_tf_tiket SET status_tf='1', kd_user_sncy_tf = '$_SESSION[id_admin]', tgl_update_tf = NOW() WHERE DATE(tanggal_tf) = '$tgl_filter'
		    ");
		    

		if (in_array(false, $stmt) OR in_array(0, $stmt)) {
	        $db->rollback();
	       	$respon = [
    			'status' => false,
    			'pesan'  => "Kesalahann System silahkan coba kembali",
    			'status_code' => 400,
    		];
    		echo json_encode($respon);
	    	die();
	    }else{
	        $db->commit();
	        $respon = [
    			'status' => true,
    			'pesan'  => "Berhasil melakukan proses cutting transaksi tiket masuk",
    			'status_code' => 200,
    		];
    		echo json_encode($respon);
	    	die();
	    }

	}


?>