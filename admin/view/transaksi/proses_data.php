<?php

require __DIR__ . '/../../plugins/escpos/vendor/autoload.php';
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;

include "../../plugins/phpqrcode/qrlib.php"; 

require_once '../../templates/koneksi.php';

cek_login_role_admin("1/2");


// ======================================================================
// ======================== BUAT TRANSAKSI BARU =========================
// ======================================================================

// echo "<pre>";
// echo print_r($_POST);
// exit();

if (isset($_POST['tambah_transaksi_baru'])){
    
    
    if($_POST['jenis_pembayaran'] == ""){
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: '.base_url().'ticketing.php?page=transaksi&action=kelola');
        exit();
    }

    $kd_admin           = $_SESSION['id_admin'];
    $kd_shift           = $_SESSION['shift'];
    $nama_cust          = antiSQLi(trim($_POST['nama_cust']));
    $telp_cust          = antiSQLi(trim($_POST['telp_cust']));
    $keterangan         = antiSQLi(trim($_POST['keterangan']));
    $jenis_pembayaran   = enkripsiDekripsi(antiSQLi(trim($_POST['jenis_pembayaran'])), 'dekripsi');
    $id_jenis_tiket     = enkripsiDekripsi(antiSQLi(trim($_POST['id_jenis_tiket'])), 'dekripsi');
    $jumlah_tiket       = antiSQLi(trim($_POST['jumlah_tiket']));
    $tanggal_sekarang   = date("Y-m-d H:i:s");
    $bayar              = preg_replace("/[^0-9]/", "", antiSQLi(trim($_POST['bayar'])) );
    $isi_diskon         = preg_replace("/[^0-9]/", "", antiSQLi(trim($_POST['isi_diskon'])) );
    
    $get_jenis_pembayaran = $db->query("SELECT nama_jenis_pembayaran FROM jenis_pembayaran WHERE id_jenis_pembayaran='$jenis_pembayaran'")->fetch_assoc();
    
    if(isset($get_jenis_pembayaran)){
        $nama_jenis_pembayaran = $get_jenis_pembayaran['nama_jenis_pembayaran'];
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Server";
        header('Location: '.base_url().'ticketing.php?page=transaksi&action=kelola');
        exit();
    }
    

    $data_tiket = $db->query("SELECT * FROM jenis_tiket WHERE id_jenis_tiket='$id_jenis_tiket'")->fetch_assoc();
    
    //  GET data Profile
    $profile = $db->query("SELECT * FROM profile WHERE id='1'")->fetch_assoc();

    $total_transaksi = (int)$data_tiket['harga_tiket'] * (int)$jumlah_tiket;
    
    //  CEK DISKON
    if($_POST['jenis_diskon'] == ""){
        $diskon = 0;
    }else{
        if($_POST['jenis_diskon'] == "persen"){
            if($isi_diskon > 100){
               $isi_diskon = 100; 
            }
            $diskon = ($total_transaksi * $isi_diskon / 100);
        }elseif($_POST['jenis_diskon'] == "rupiah"){
            $diskon =  (int)$isi_diskon;
            if($diskon > $total_transaksi){
                $_SESSION['notifikasi']['fail'] = "Voucher tidak dapat digunakan, diskon melebihi harga penjualan";
                header('Location: '.base_url().'ticketing.php?page=transaksi&action=kelola');
                exit();
            }
        }elseif($_POST['jenis_diskon'] == "voucher"){
            
            $isi_diskon   = antiSQLi($_POST['isi_diskon']);
            $tgl_sekarang = date("Y-m-d");
            $jam_sekarang = date("H:i");
            
            $query = "SELECT * FROM voucher_tiket WHERE kode_voucher='$isi_diskon' AND start_tgl<='$tgl_sekarang' AND end_tgl>='$tgl_sekarang' AND status_rmv_voucher='N' AND start_jam<'$jam_sekarang' AND end_jam>'$jam_sekarang' AND max_pengguna>0 ";
            $data_voucher = $db->query($query)->fetch_assoc();

            if(!isset($data_voucher)){
                $_SESSION['notifikasi']['fail'] = "Voucher tidak dapat digunakan - ER1 ";
                header('Location: '.base_url().'ticketing.php?page=transaksi&action=kelola');
                exit();
            }

            if ($data_voucher['status_potongan'] == "1") { //Potongan Harga
                $diskon =  (int)$data_voucher['potongan_voucher'];
                if($diskon > $total_transaksi){
                    $_SESSION['notifikasi']['fail'] = "Voucher tidak dapat digunakan, diskon melebihi harga penjualan";
                    header('Location: '.base_url().'ticketing.php?page=transaksi&action=kelola');
                    exit();
                }
                $kd_voucher = $data_voucher['id_voucher'];
                $db->query("UPDATE voucher_tiket SET max_pengguna=max_pengguna-1 WHERE id_voucher='$kd_voucher' ");

            }elseif($data_voucher['status_potongan'] == "2"){ // Potongan Persen
                if($data_voucher['potongan_voucher'] > 100){
                   $data_voucher['potongan_voucher'] = 100; 
                }
                $diskon = ((int)$total_transaksi * (int)$data_voucher['potongan_voucher'] / 100);
                $kd_voucher = $data_voucher['id_voucher'];
                $db->query("UPDATE voucher_tiket SET max_pengguna=max_pengguna-1 WHERE id_voucher='$kd_voucher' ");
            }else{
                $_SESSION['notifikasi']['fail'] = "Voucher tidak dapat digunakan - ER2";
                header('Location: '.base_url().'ticketing.php?page=transaksi&action=kelola');
                exit();
            }
            
        }
    }
    
    
    $total_transaksi_setelah_diskon = $total_transaksi - $diskon;
    // END CEK DISKON
    
    
//  echo "<pre>";
//  echo print_r($_POST);
//  echo "<br><br><br>".$diskon;
    
    $db->begin_transaction();

    $id_transaksi = createID('id_transaksi', 'transaksi', 'TR');
    $no_struk = id_ke_struk($id_transaksi);
    // $kepala_tran = "INSERT INTO transaksi (id_transaksi, no_nota, kd_admin, kd_shift, nama_cust, telp_cust, jumlah_tiket, nominal_sebelum_diskon, diskon, bayar, kd_jenis_pembayaran, kd_voucher, total_transaksi, keterangan_tiket, status_transaksi, stt_insrt_tf_tiket, stt_updt_tf_tiket) VALUES ('$id_transaksi', '$no_struk', '$kd_admin', '$kd_shift', '$nama_cust' ,'$telp_cust' ,'$jumlah_tiket' ,'$total_transaksi', '$diskon', '$bayar', '$jenis_pembayaran', '', '$total_transaksi_setelah_diskon', '$keterangan', '1', '0', '0')";
    $kepala_tran = "INSERT INTO transaksi (id_transaksi, no_nota, kd_admin, kd_shift, nama_cust, telp_cust, jumlah_tiket, nominal_sebelum_diskon, diskon, bayar, kd_jenis_pembayaran, kd_voucher, total_transaksi, keterangan_tiket, status_transaksi) VALUES ('$id_transaksi', '$no_struk', '$kd_admin', '$kd_shift', '$nama_cust' ,'$telp_cust' ,'$jumlah_tiket' ,'$total_transaksi', '$diskon', '$bayar', '$jenis_pembayaran', '', '$total_transaksi_setelah_diskon', '$keterangan', '1')";
    $stmt[] = $db->query($kepala_tran);


    if (@$_POST['id_agen'] != "") {
        $id_agen   = enkripsiDekripsi($_POST['id_agen'], 'dekripsi');
        $query_agen = "UPDATE transaksi SET id_agen='$id_agen' WHERE id_transaksi='$id_transaksi' ";
        $stmt[] = $db->query($query_agen);
    }
    
    $kum_qur_tiket = [];
    // $kum_id_tiket = "";
    for ($x = 1; $x <= $jumlah_tiket; $x++) {

        $data_id_tiket = createID30Urut('id_tiket', 'tiket', 'TK');
        $id_tiket = $data_id_tiket["id"];
        $id_tiket_urutan = $data_id_tiket["urutan"];    

        // GENERATE ID UNIK
        $id_unik = bulanKeHuruf(date('m')).date('d').$id_tiket_urutan.date('y');

        $query_tiket = "INSERT INTO tiket (id_tiket, kode_tiket, kd_transaksi, kd_jenis_tiket, harga_satuan) VALUES ('$id_tiket', '$id_unik', '$id_transaksi', '$id_jenis_tiket', '$data_tiket[harga_tiket]')";
        $stmt[] = $db->query($query_tiket);

        array_push($kum_qur_tiket, $query_tiket);
        // $kum_id_tiket = $kum_id_tiket.$id_tiket.",";


        $id_tiket_enkripsi = enkripsiDekripsi($id_tiket, 'enkripsi');
        $tiket_dibeli[$id_tiket_enkripsi]['nama_tiket'] = $data_tiket['nama_jenis_tiket'];
        $tiket_dibeli[$id_tiket_enkripsi]['id_unik'] = $id_unik;
        $tiket_dibeli[$id_tiket_enkripsi]['info'] = "Tiket hanya berlaku untuk 1 orang";
        
    }

    // check table stt_insrt_tf_tiket lihat udh ada belum pada hari ini
    
    // di non aktifkan sementara
        // $stt_tf_tiket_count =  $db->query("SELECT COUNT(*) as jml FROM stt_tf_tiket WHERE DATE(tanggal_tf) = CURDATE()")->fetch_assoc()['jml'];

        // if($stt_tf_tiket_count < 1 ){
        //     $id_stt_tf = createID('id_stt_tf', 'stt_tf_tiket', 'TF');
        //     $tbl_stt_tf_insert = "INSERT INTO stt_tf_tiket SET id_stt_tf = '$id_stt_tf', tanggal_tf = now(), status_tf = '0', kd_user_sncy_tf = null, tgl_update_tf= null ";
        //     $stmt[] = $db->query($tbl_stt_tf_insert);
        // }
    // di non aktifkan sementara

    if (in_array(false, $stmt) OR in_array(0, $stmt)) {
        $db->rollback();
        $_SESSION['notifikasi']['fail'] = "Transaksi gagal diproses, harap coba lagi";
        header('Location: '.base_url().'ticketing.php?page=transaksi_tambah&action=');
        exit();
    }else{
        $db->commit();
        //proses insert ke new server

        
        //ambil semua data yg belum di insert ke sana
            // $transaksi_failed_tf = $db->query("SELECT *, b.tanggal_transaksi as tanggal_transaksi_tiket
            //         FROM transaksi a 
            //         JOIN tiket b ON a.id_transaksi = b.kd_transaksi
            //         WHERE stt_insrt_tf_tiket = '0' AND DATE(a.tanggal_transaksi) >= '2022-05-23' ")->fetch_all(MYSQLI_ASSOC);



            // $response_newserver = @CRUD_API(base_url_newserve()."api/tiket_masuk" ,json_encode([
            //     'act'                   => "tambah",
            //     // 'transaksi'             => [$kepala_tran, @$query_agen],
            //     // 'tiket'                 => $kum_qur_tiket,
            //     'transaksi_failed_tf'   => $transaksi_failed_tf,
            // ]));

            // if($response_newserver['status'] == true){
            //     $list_id_update = substr($response_newserver['id_transaksi_resend_back'], 0, -1);
                
            //     // proses update status.
            //     $db->query("UPDATE transaksi a, (SELECT id_transaksi FROM transaksi WHERE id_transaksi IN ($list_id_update)) AS b SET a.stt_insrt_tf_tiket = '1' WHERE a.id_transaksi = b.id_transaksi; ");
            // }
    }

    
    // goto lanjutaja;

    if (isset($_POST['cetak_barcode'])) {
        foreach ($tiket_dibeli as $id_tiket => $value) {        
            $data['id_tiket'] = $id_tiket;
            $data['nama_tiket'] = $value['nama_tiket'];
            $data['tgl'] = tanggal_jam_indo($tanggal_sekarang);
            $data['info'] = $value['info'];
            $data['id_unik'] = $value['id_unik'];
            $data['username'] = $_SESSION['username'];
            $data_json = json_encode($data);
            printClient($_SESSION['printer'], 'tiket_masuk', $data_json, $printer_tiket_masuk);
        }
    }
    
    
    $data['no_struk'] = $no_struk;
    $data['nama_kasir'] = $_SESSION['username'];
    $data['tanggal'] = $tanggal_sekarang;
    $data['nama_jenis_tiket'] = $data_tiket['nama_jenis_tiket'];
    $data['harga_tiket'] = $data_tiket['harga_tiket'];
    $data['jumlah_tiket'] = $jumlah_tiket;
    $data['total_transaksi'] = $total_transaksi;
    $data['diskon'] = $diskon;
    $data['total_transaksi_setelah_diskon'] = $total_transaksi_setelah_diskon;
    $data['nama_jenis_pembayaran'] = $nama_jenis_pembayaran;
    $data['bayar'] = $bayar;
    $data['telp_profile'] = $profile["telp_profile"];
    $data['keterangan'] = $keterangan;
    $data_json = json_encode($data);
    printClient($_SESSION['printer'], 'tiket_masuk_bill', $data_json, $printer_tiket_masuk);
    // https://github.com/mike42/escpos-php
    
    
    // lanjutaja:
    $_SESSION['notifikasi']['success'] = "Transaksi atas nama '".$nama_cust."' telah ditambahkan ";
    header('Location: '.base_url().'ticketing.php?page=transaksi_tambah&action=');
    exit();

}





// ======================================================================
// ======================== CETAK TIKET SATUAN ==========================
// ======================================================================

if(isset($_POST['cetak_tiket_satuan'])){
    
    $id_tiket = enkripsiDekripsi(antiSQLi($_POST['id_tiket']), 'dekripsi');

    $data_tiket = $db->query("SELECT * FROM tiket A JOIN transaksi B ON A.kd_transaksi=B.id_transaksi JOIN jenis_tiket C ON A.kd_jenis_tiket=C.id_jenis_tiket JOIN admin D ON B.kd_admin=D.id_admin WHERE id_tiket='$id_tiket'")->fetch_assoc();
    
    if(!isset($data_tiket)){
        $_SESSION['notifikasi']['fail'] = "Kegagalan Sistem";
        header('Location: ../../ticketing.php?page=transaksi&action=kelola');
        exit();
    }

    

    $info = "Tiket hanya berlaku untuk 1 Orang";
    
    
    $id_transaksi = $data_tiket['id_transaksi'];
    
    // $codeContents = antiSQLi($_POST['id_tiket']);

    // QRcode::png($codeContents,"temp_qrcode/".$codeContents.".png");
    
    // $connector = new FilePrintConnector("//localhost/TM-T82");
    // $printer = new Printer($connector);
    // $printer -> setJustification(Printer::JUSTIFY_CENTER);
    // $logo    = EscposImage::load("../../dist/img/hehaocen.png");
    // $printer -> graphics($logo);
    // $printer -> setTextSize(2, 1);
    // $printer -> text(strtoupper('( SALINAN )'));
    // $printer -> setTextSize(1, 1);
    // $printer -> text("\n");
    // $printer -> text("\n");
    // $printer -> text('Tiket '.htmlspecialchars_decode("".$data_tiket['nama_jenis_tiket'], ENT_QUOTES));
    // $printer -> text("\n");
    // $printer -> text("".tanggal_jam_indo($data_tiket['tanggal_transaksi'])."");
    // $printer -> text("\n");
    // $printer -> text("".$info);
    // $printer -> text("\n");
    // $QrCetak = EscposImage::load("temp_qrcode/".$codeContents.".png");
    // $printer -> graphics($QrCetak, Printer::IMG_DOUBLE_WIDTH | Printer::IMG_DOUBLE_HEIGHT);
    // $printer -> text("".$data_tiket['kode_tiket']);
    // $printer -> text("\n");
    // $printer -> text("Kasir : ".$data_tiket['username_admin']);
    // $printer -> text("\n");
    // $printer -> text("\n");
    // $printer -> text("\n");
    // $printer -> cut();
    // $printer -> close();
    // unlink("temp_qrcode/".$codeContents.".png");


    $data['id_tiket'] = $_POST['id_tiket'];
    $data['nama_tiket'] = $data_tiket['nama_jenis_tiket'];
    $data['tgl'] = tanggal_jam_indo($data_tiket['tanggal_transaksi']);
    $data['info'] = $info;
    $data['id_unik'] = $data_tiket['kode_tiket'];
    $data['username'] = $data_tiket['username_admin'];
    $data_json = json_encode($data);
    printClient($_SESSION['printer'], 'tiket_masuk_salinan', $data_json, $printer_tiket_masuk);

    
    $_SESSION['notifikasi']['success'] = "Tiket ".$data_tiket['kode_tiket']." telah dicetak";
    header('Location: ../../ticketing.php?page=transaksi&action=detail&id='.enkripsiDekripsi($data_tiket['id_transaksi'], 'enkripsi'));
    exit();
    
    
}


// ======================================================================
// ========================= VERIFIKASI VOUCHER =========================
// ======================================================================

if(isset($_GET['cek_voucher'])){
    
    $id_voucher = antiSQLi($_GET['cek_voucher']);
    $tgl_sekarang = date("Y-m-d");
    $jam_sekarang = date("H:i");
    
    $query = "SELECT * FROM voucher_tiket WHERE kode_voucher='$id_voucher' AND start_tgl<='$tgl_sekarang' AND end_tgl>='$tgl_sekarang' AND status_rmv_voucher='N' AND start_jam<'$jam_sekarang' AND end_jam>'$jam_sekarang' AND max_pengguna>0 ";
    $data_voucher = $db->query($query)->fetch_assoc();
    
    if(!isset($data_voucher)){
        $data['kode'] = "N";
        echo json_encode($data);
    }else{
        $data['kode']  = "Y";
        $data['value'] = $data_voucher['potongan_voucher'];
        $data['tipe']  = $data_voucher['status_potongan'];
        echo json_encode($data);
    }

    
}




// ======================================================================
// ========================= CETAK ULANG BILL ===========================
// ======================================================================


if(isset($_GET['cetak_ulang_bill'])){
    
    $id_transaksi = enkripsiDekripsi(antiSQLi($_GET['cetak_ulang_bill']), 'dekripsi');
    
    $telp_profile = $db->query("SELECT telp_profile FROM profile WHERE id='1' LIMIT 1")->fetch_assoc()['telp_profile'];
    
    $data_tiket = $db->query("SELECT A.tanggal_transaksi, A.no_nota, C.nama_admin, D.nama_jenis_tiket, A.jumlah_tiket, B.harga_satuan, (B.harga_satuan*A.jumlah_tiket) AS subtotal, A.diskon, A.bayar, A.total_transaksi, A.nominal_sebelum_diskon, E.nama_jenis_pembayaran, A.keterangan_tiket FROM transaksi A JOIN tiket B ON A.id_transaksi=B.kd_transaksi JOIN admin C ON A.kd_admin=C.id_admin JOIN jenis_tiket D ON B.kd_jenis_tiket=D.id_jenis_tiket JOIN jenis_pembayaran E ON A.kd_jenis_pembayaran=E.id_jenis_pembayaran  WHERE A.id_transaksi='$id_transaksi' LIMIT 1")->fetch_assoc();
    
    
    // CETAK INVOICE
    // $connector = new FilePrintConnector("//localhost/TM-T82");
 //    $printer = new Printer($connector);
 //    $printer -> setJustification(Printer::JUSTIFY_CENTER);
 //    $printer -> graphics(EscposImage::load('../../dist/img/hehaocen.png'), Printer::IMG_DEFAULT | Printer::IMG_DEFAULT);
    
 //    // $printer -> text('Nota Transaksi Tiket Masuk');
 //    // $printer -> text("\n");
 //    $printer -> setTextSize(2, 1);
 //    $printer -> text(strtoupper('( SALINAN )'));
 //    $printer -> setTextSize(1, 1);
 //    $printer -> text("\n");
 //    $printer -> text("\n");
 //    $printer -> setJustification(Printer::JUSTIFY_LEFT);
 //    $printer -> text($data_tiket['tanggal_transaksi']." ".sprintf('%28s'," Kasir :".$data_tiket['nama_admin']));
 //    $printer -> text("\n");
 //    $printer -> text("------------------------------------------------");
 //    $printer -> text("                     RINCIAN                    ");
 //    $printer -> text("------------------------------------------------");
    
    
 //    $harga_plus_jml = number_format($data_tiket['harga_satuan'])." x ".$data_tiket['jumlah_tiket'];
 //    $subtotal = (int)$data_tiket['jumlah_tiket'] * $data_tiket['harga_satuan'];
    
 //    $printer -> text("\n");
 //    $printer -> text(sprintf('%-48s','Tiket Masuk '.$data_tiket['nama_jenis_tiket']));
 //    $printer -> text("\n");
 //    $printer -> text(sprintf('%-24s', $harga_plus_jml).sprintf('%24s',number_format($subtotal)));
 //    $printer -> text("\n");
    
    
 //    $printer -> text("------------------------------------------------");
 //    $printer -> setJustification(Printer::JUSTIFY_LEFT);
 //    $printer -> text("\n");
 //    $printer -> text(sprintf('%-24s',' ').sprintf('%13s','Total: ').sprintf('%11s',number_format($data_tiket['nominal_sebelum_diskon'])));
 //    $printer -> text("\n");
 //    $printer -> text(sprintf('%-24s',' ').sprintf('%13s','Diskon: ').sprintf('%11s',number_format($data_tiket['diskon'])));
 //    $printer -> text("\n");
 //    $printer -> text(sprintf('%-24s',' ').sprintf('%13s','Harga Akhir: ').sprintf('%11s',number_format($data_tiket['total_transaksi'])));
 //    $printer -> text("\n");
 //    $printer -> text(sprintf('%-30s',$data_tiket['nama_jenis_pembayaran'])."Bayar: ".sprintf('%11s',number_format($data_tiket['bayar'])));
 //    $printer -> text("\n");
 //    $printer -> text(sprintf('%-24s','CS: '. $telp_profile).sprintf('%13s','Kembalian: ').sprintf('%11s',number_format($data_tiket['bayar'] - $data_tiket['total_transaksi'])));
 //    $printer -> text("\n");
 //    $printer -> text("\n");
 //    $printer -> cut();
 //    $printer -> close();
    
    
    $data['no_struk'] = $data_tiket['no_nota'];
    $data['nama_kasir'] = $data_tiket['nama_admin'];
    $data['tanggal'] = $data_tiket['tanggal_transaksi'];
    $data['nama_jenis_tiket'] = $data_tiket['nama_jenis_tiket'];
    $data['harga_tiket'] = $data_tiket['harga_satuan'];
    $data['jumlah_tiket'] = $data_tiket['jumlah_tiket'];
    $data['total_transaksi'] = $data_tiket['nominal_sebelum_diskon'];
    $data['diskon'] = $data_tiket['diskon'];
    $data['total_transaksi_setelah_diskon'] = $data_tiket['total_transaksi'];
    $data['nama_jenis_pembayaran'] = $data_tiket['nama_jenis_pembayaran'];
    $data['bayar'] = $data_tiket['bayar'];
    $data['telp_profile'] = $telp_profile;
    $data['keterangan'] = $data_tiket['keterangan_tiket'];
    $data_json = json_encode($data);
    printClient($_SESSION['printer'], 'tiket_masuk_bill_salinan', $data_json, $printer_tiket_masuk);
    
    
    
    
    //  =========================================
    
    echo "<script>window.history.back();</script>";
    
}


if (isset($_GET['cari_agen'])) {
    $key = antiSQLi($_GET['cari_agen']);
    $data = $db->query("SELECT * FROM agen WHERE status_hapus_agen='N' AND ( nama_agen LIKE '%$key%' OR no_identitas_agen LIKE '%$key%' OR no_telp_agen LIKE '%$key%' ) ORDER BY nama_agen ASC ")->fetch_all(MYSQLI_ASSOC);


    foreach ($data as $key => $value) {
        $data[$key]['id_agen'] = enkripsiDekripsi($data[$key]['id_agen'], 'enkripsi');
    }
    // print_r("<pre>"); print_r($data); die();

    $respon['status'] = "200";
    $respon['data'] = $data;
    echo json_encode($respon);exit();

}


if (isset($_POST['tambah_agen_json'])) {
    $nama = antiSQLi(strtoupper($_POST['nama']));
    $no_identitas = antiSQLi($_POST['no_identitas']);
    $telp = antiSQLi($_POST['telp']);
    $kota = antiSQLi(enkripsiDekripsi($_POST['kota'], 'dekripsi'));
    $alamat = antiSQLi($_POST['alamat']);
    $kd_admin = $_SESSION['id_admin'];
    $id = createID('id_agen','agen','AN');

    $cek_telp = $db->query("SELECT * FROM agen WHERE no_telp_agen='$telp' AND status_hapus_agen='N' ")->fetch_assoc();
    if (!empty($cek_telp)) {
        $respon['status'] = "500";
        $respon['data'] = "Nomor telepon telah digunakan, harap masukkan nomor lain";
        echo json_encode($respon);exit();
    }

    $cek_no_identitas = $db->query("SELECT * FROM agen WHERE no_identitas_agen='$no_identitas' AND status_hapus_agen='N' ")->fetch_assoc();
    if (!empty($cek_no_identitas)) {
        $respon['status'] = "500";
        $respon['data'] = "Nomor identitas telah digunakan, harap masukkan nomor lain";
        echo json_encode($respon);exit();
    }
    
    $stmt = $db->query("INSERT INTO agen SET id_agen='$id', nama_agen='$nama', no_telp_agen='$telp', alamat_agen='$alamat', kota_agen= '$kota', no_identitas_agen='$no_identitas', dibuat_oleh_agen='$kd_admin' ");

    if ($stmt) {
        $respon['status'] = "200";
        $respon['data'] = "Berhasil Memasukkan Data";
        $respon['record'] = [
            'id' => enkripsiDekripsi($id, 'enkripsi'),
            'nama' => $nama,
            'telp' => $telp
        ];
        echo json_encode($respon);exit();
    }else{
        $respon['status'] = "500";
        $respon['data'] = "Gagal Server";
        echo json_encode($respon);exit();
    }
}

?>