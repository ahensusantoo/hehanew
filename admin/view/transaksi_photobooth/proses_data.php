<?php
require __DIR__ . '/../../plugins/escpos/vendor/autoload.php';
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;

include "../../plugins/phpqrcode/qrlib.php"; 

require_once '../../templates/koneksi.php';

cek_login_role_admin("1/3/5");


// ======================================================================
// ======================== BUAT TRANSAKSI BARU =========================
// ======================================================================

// echo "<pre>";
// echo print_r($_POST);
// exit();

if (isset($_POST['tambah_transaksi_photobooth_baru'])){

    if (!isset($_POST['photobooth_dibeli'])) {
        $_SESSION['notifikasi']['fail'] = "Tidak ada photobooth yang dipilih";
        header('Location: '.base_url().'photobooth.php?page=transaksi_photobooth&action=kelola');
        exit();
    }

    $nama_cust          = antiSQLi(@$_POST['nama_cust']);
    $telp_cust          = antiSQLi(@$_POST['telp_cust']);
    $jumlah_dibayar     = preg_replace("/[^0-9]/", "", antiSQLi($_POST['jumlah_dibayar']) );
    $isi_diskon         = preg_replace("/[^0-9]/", "", antiSQLi($_POST['isi_diskon']) );
    $jenis_diskon       = antiSQLi($_POST['jenis_diskon']);
    $keterangan         = antiSQLi($_POST['keterangan']);
    $jenis_pembayaran   = enkripsiDekripsi(antiSQLi(@$_POST['jenis_pembayaran']), 'dekripsi');
    $tanggal_sekarang   = date("Y-m-d H:i:s");
    
    
    $get_jenis_pembayaran = $db->query("SELECT nama_jenis_pembayaran FROM jenis_pembayaran WHERE id_jenis_pembayaran='$jenis_pembayaran'")->fetch_assoc();
    
    if(isset($get_jenis_pembayaran)){
        $nama_jenis_pembayaran = $get_jenis_pembayaran['nama_jenis_pembayaran'];
    }else{
        $_SESSION['notifikasi']['fail'] = "Kegagalan Server";
        header('Location: '.base_url().'ticketing.php?page=transaksi&action=kelola');
        exit();
    }
    
    
    //  GET data Profile
    $profile = $db->query("SELECT * FROM profile WHERE id='1'")->fetch_assoc();


    $total_transaksi = 0;
    $total_tiket = 0;
    foreach ($_POST['photobooth_dibeli'] as $key => $jml) {
        $id_stan = enkripsiDekripsi(antiSQLi($key), 'dekripsi');

        $data_stan = $db->query("SELECT * FROM photobooth_stan WHERE id_photobooth_stan='$id_stan' AND status_display_photobooth='Y'")->fetch_assoc();

        if (!isset($data_stan)) {
            $_SESSION['notifikasi']['fail'] = "Photobooth tidak terdaftar";
            header('Location: '.base_url().'photobooth.php?page=transaksi_photobooth&action=kelola');
            exit();
        }

        if ($data_stan['status_paket'] == "Y") {
            
            $data_paket = $db->query("SELECT * FROM photobooth_stan_paket A JOIN photobooth_stan B ON A.id_photobooth_stan=B.id_photobooth_stan WHERE A.id_photobooth_stan_paket='$data_stan[id_photobooth_stan]' ")->fetch_all(MYSQLI_ASSOC);

            if (isset($photobooth_dibeli[$id_stan])) {
                $_SESSION['notifikasi']['fail'] = "Gagal. Tidak bisa memilih 2 spot foto yang sama";
                header('Location: '.base_url().'photobooth.php?page=transaksi_photobooth&action=kelola');
                exit();
            }


            $photobooth_dibeli[$id_stan]['jumlah'] = $jml;
            $photobooth_dibeli[$id_stan]['nama'] = $data_stan['nama_photobooth_stan'];
            $photobooth_dibeli[$id_stan]['harga']  = $data_stan['harga_photobooth_stan'];
            $photobooth_dibeli[$id_stan]['cetak_kepala']  = true;
            $photobooth_dibeli[$id_stan]['cetak_badan']  = false;
            $photobooth_dibeli[$id_stan]['antrian']  = false;
            $total_transaksi += $jml * (int)$data_stan['harga_photobooth_stan'];
            $total_tiket += $jml;

            foreach ($data_paket as $key_paket => $value_paket) {

                if (isset($photobooth_dibeli[$value_paket['id_photobooth_stan']])) {
                    $_SESSION['notifikasi']['fail'] = "Gagal. Tidak bisa memilih 2 spot foto yang sama.";
                    header('Location: '.base_url().'photobooth.php?page=transaksi_photobooth&action=kelola');
                    exit();
                }

                $photobooth_dibeli[$value_paket['id_photobooth_stan']]['jumlah'] = $jml;
                $photobooth_dibeli[$value_paket['id_photobooth_stan']]['nama'] = $value_paket['nama_photobooth_stan'];
                $photobooth_dibeli[$value_paket['id_photobooth_stan']]['harga']  = 0;
                $photobooth_dibeli[$value_paket['id_photobooth_stan']]['cetak_kepala']  = false;
                $photobooth_dibeli[$value_paket['id_photobooth_stan']]['cetak_badan']  = true;
                $photobooth_dibeli[$value_paket['id_photobooth_stan']]['antrian']  = true;
                $total_transaksi += $jml * 0;
                $total_tiket += $jml;
            }

        }else{

            if (isset($photobooth_dibeli[$id_stan])) {
                $_SESSION['notifikasi']['fail'] = "Gagal. Tidak bisa memilih 2 spot foto yang sama .";
                header('Location: '.base_url().'photobooth.php?page=transaksi_photobooth&action=kelola');
                exit();
            }
            
            $photobooth_dibeli[$id_stan]['jumlah'] = $jml;
            $photobooth_dibeli[$id_stan]['nama'] = $data_stan['nama_photobooth_stan'];
            $photobooth_dibeli[$id_stan]['harga']  = $data_stan['harga_photobooth_stan'];
            $photobooth_dibeli[$id_stan]['cetak_kepala']  = true;
            $photobooth_dibeli[$id_stan]['cetak_badan']  = true;
            $photobooth_dibeli[$id_stan]['antrian']  = true;
            $total_transaksi += $jml * (int)$data_stan['harga_photobooth_stan'];
            $total_tiket += $jml;
        }

    }

    // echo "<pre>";
    // print_r($data_paket);
    // exit;


    //  CEK DISKON
    $kd_voucher = "";
    $diskon = 0;
    if($jenis_diskon == ""){
        $diskon = 0;
    }else{
        if($jenis_diskon == "persen"){
            if($isi_diskon > 100){
               $isi_diskon = 100; 
            }
            $diskon = ($total_transaksi * (int)$isi_diskon / 100);
        }elseif($jenis_diskon == "harga"){
            $diskon =  (int)$isi_diskon;
            if($diskon > $total_transaksi){
                $_SESSION['notifikasi']['fail'] = "Voucher tidak dapat digunakan, diskon melebihi harga penjualan";
                header('Location: '.base_url().'photobooth.php?page=transaksi_photobooth&action=kelola');
                exit();
            }
        }elseif($jenis_diskon == "voucher"){
            
            $isi_diskon   = antiSQLi($_POST['isi_diskon']);
            $tgl_sekarang = date("Y-m-d");
            $jam_sekarang = date("H:i");
            $query = "SELECT * FROM voucher_photobooth WHERE kode_voucher='$isi_diskon' AND start_tgl<='$tgl_sekarang' AND end_tgl>='$tgl_sekarang' AND status_rmv_voucher='N' AND start_jam<'$jam_sekarang' AND end_jam>'$jam_sekarang' AND max_pengguna>0 ";
            $data_voucher = $db->query($query)->fetch_assoc();

            if(!isset($data_voucher)){
                $_SESSION['notifikasi']['fail'] = "Voucher tidak dapat digunakan - ER1 ";
                header('Location: '.base_url().'photobooth.php?page=transaksi_photobooth&action=kelola');
                exit();
            }

            if ($data_voucher['status_potongan'] == "1") { //Potongan Harga
                $diskon =  (int)$data_voucher['potongan_voucher'];
                if($diskon > $total_transaksi){
                    $_SESSION['notifikasi']['fail'] = "Voucher tidak dapat digunakan, diskon melebihi harga penjualan";
                    header('Location: '.base_url().'photobooth.php?page=transaksi_photobooth&action=kelola');
                    exit();
                }
                $kd_voucher = $data_voucher['id_voucher'];
                $db->query("UPDATE voucher_photobooth SET max_pengguna=max_pengguna-1 WHERE id_voucher='$kd_voucher' ");

            }elseif($data_voucher['status_potongan'] == "2"){ // Potongan Persen
                if($data_voucher['potongan_voucher'] > 100){
                   $data_voucher['potongan_voucher'] = 100; 
                }
                $diskon = ((int)$total_transaksi * (int)$data_voucher['potongan_voucher'] / 100);
                $kd_voucher = $data_voucher['id_voucher'];
                $db->query("UPDATE voucher_photobooth SET max_pengguna=max_pengguna-1 WHERE id_voucher='$kd_voucher' ");
            }else{
                $_SESSION['notifikasi']['fail'] = "Voucher tidak dapat digunakan - ER2";
                header('Location: '.base_url().'photobooth.php?page=transaksi_photobooth&action=kelola');
                exit();
            }

        }else{
            $_SESSION['notifikasi']['fail'] = "Voucher tidak dapat digunakan - ER3";
            header('Location: '.base_url().'photobooth.php?page=transaksi_photobooth&action=kelola');
            exit();
        }
    }
    

    $total_transaksi_setelah_diskon = $total_transaksi - $diskon;

    $db->begin_transaction();

    $id_transaksi = createID('id_photobooth_transaksi', 'photobooth_transaksi', 'TP');
    $no_struk = id_ke_struk($id_transaksi);
    $query = "INSERT INTO photobooth_transaksi (id_photobooth_transaksi, no_nota, kd_admin, kd_shift, kd_jenis_pembayaran, nama_cust, telp_cust, no_urut, jumlah_tiket, nominal_sebelum_diskon, kd_voucher, diskon, total_transaksi, bayar, status_transaksi, keterangan_photobooth) VALUES ('$id_transaksi', '$no_struk', '$_SESSION[id_admin]', '$_SESSION[shift]', '$jenis_pembayaran', '$nama_cust', '$telp_cust', '', '$total_tiket', '$total_transaksi', '$kd_voucher', '$diskon', '$total_transaksi_setelah_diskon', '$jumlah_dibayar', '1', '$keterangan')";
    $sql = $db->query($query);

    if (!$sql) {
        $db->rollback();
        $_SESSION['notifikasi']['fail'] = "Gagal diproses, mohon coba lagii ";
        header('Location: '.base_url().'photobooth.php?page=transaksi_photobooth&action=kelola');
        exit();
    }


    foreach ($photobooth_dibeli as $id_stan => $value) {
        $jumlah = $value['jumlah'];
        $harga = $value['harga'];
        $nama_stan = $value['nama'];
        
        $tgl_sekarang = date("Y-m-d");

        // $photobooth_dibeli[$id_stan]['cetak_kepala']  = true;
        // $photobooth_dibeli[$id_stan]['cetak_badan']  = true;
        // $photobooth_dibeli[$id_stan]['antrian']  = true;
            
        if ($value['antrian']) {
            $no_urut = $db->query("SELECT MAX(no_urut) AS hasil FROM photobooth_tiket WHERE kd_photobooth_stan='$id_stan' AND status_tiket!='2' AND DATE(tanggal_transaksi)='$tgl_sekarang' ")->fetch_assoc()['hasil'];
            if ($no_urut == NULL) {
                $no_urut = 1;
            }else{
                $no_urut += 1;
            }
        }else{
            $no_urut = 0;
        }

        $data_id_tiket = createID30Urut('id_photobooth_tiket', 'photobooth_tiket', 'DP');
    
        $id_tiket = $data_id_tiket["id"];
        $id_tiket_urutan = $data_id_tiket["urutan"];    

        // GENERATE ID UNIK
        $id_unik = bulanKeHuruf(date('m')).date('d').$id_tiket_urutan.date('y');

        $query = "INSERT INTO photobooth_tiket SET id_photobooth_tiket='$id_tiket', kode_tiket='$id_unik', kd_photobooth_transaksi='$id_transaksi', kd_photobooth_stan='$id_stan', harga_satuan='$harga', jumlah_tiket='$jumlah', no_urut='$no_urut' ";
        $sql = $db->query($query);
        
        if (!$sql) {
            $db->rollback();
            $_SESSION['notifikasi']['fail'] = "Gagal diproses, mohon coba lagii ";
            header('Location: '.base_url().'photobooth.php?page=transaksi_photobooth&action=kelola');
            exit();
        }
        
        $id_tiket_enkripsi = enkripsiDekripsi($id_tiket, 'enkripsi');
        // $tiket_dibeli[$id_tiket_enkripsi]['nama_tiket'] = $data_tiket['nama_jenis_tiket'];
        $tiket_dibeli[$id_tiket_enkripsi]['id_unik'] = $id_unik;
        $tiket_dibeli[$id_tiket_enkripsi]['no_urut'] = $no_urut;
        $tiket_dibeli[$id_tiket_enkripsi]['nama_cust'] = $nama_cust;
        $tiket_dibeli[$id_tiket_enkripsi]['nama'] = $nama_stan;
        $tiket_dibeli[$id_tiket_enkripsi]['cetak_badan'] = $value['cetak_badan'];
        $tiket_dibeli[$id_tiket_enkripsi]['info'] = "Tiket berlaku untuk ".$jumlah." orang";

    }


    $db->commit();


    foreach ($tiket_dibeli as $key => $value) {
        if (!$value['cetak_badan']) {
            unset($tiket_dibeli[$key]);
        }
    }


    foreach ($tiket_dibeli as $id_tiket => $value) {
            
        $data['id_tiket'] = $id_tiket;
        $data['nama_photobooth'] = $value['nama'];
        $data['tanggal'] = tanggal_jam_indo($tanggal_sekarang);
        $data['no_antrean'] = $value['no_urut'];
        $data['berlaku_untuk'] = $value['info'];
        $data['diberikan_kepada'] = "Untuk Pengunjung";
        $data['nama_cust'] = $nama_cust;
        $data_json = json_encode($data);
        printClient($_SESSION['printer'], 'photobooth', $data_json, $printer_tiket_photobooth);
        
    }
    
    
    foreach ($tiket_dibeli as $id_tiket => $value) {

        $data['id_tiket'] = $id_tiket;
        $data['nama_photobooth'] = $value['nama'];
        $data['tanggal'] = tanggal_jam_indo($tanggal_sekarang);
        $data['no_antrean'] = $value['no_urut'];
        $data['berlaku_untuk'] = $value['info'];
        $data['diberikan_kepada'] = "Untuk Petugas";
        $data['nama_cust'] = $nama_cust;
        $data_json = json_encode($data);
        printClient($_SESSION['printer'], 'photobooth', $data_json, $printer_tiket_photobooth);
        
    }
    
    tidakprint:

    foreach ($photobooth_dibeli as $key_cetak => $value_cetak) {
        if (!$value_cetak['cetak_kepala']) {
            unset($photobooth_dibeli[$key_cetak]);
        }
    }

    // echo "<pre>";
    // print_r($photobooth_dibeli);
    // exit;

    $data['no_struk'] = $no_struk;
    $data['tanggal'] = $tanggal_sekarang;
    $data['kasir'] = $_SESSION['username'];
    $data['detail'] = $photobooth_dibeli;
    $data['total_transaksi'] = $total_transaksi;
    $data['diskon'] = $diskon;
    $data['total_setalah_diskon'] = $total_transaksi_setelah_diskon;
    $data['nama_jenis_pembayaran'] = $nama_jenis_pembayaran;
    $data['bayar'] = $jumlah_dibayar;
    $data['telp_profile'] = $profile["telp_profile"];
    $data['keterangan'] = $keterangan;
    
    $data_json = json_encode($data);
    printClient($_SESSION['printer'], 'photobooth_bill', $data_json, $printer_tiket_photobooth);


    $_SESSION['notifikasi']['success'] = "Transaksi atas nama '".$nama_cust."' telah ditambahkan ";
    header('Location: '.base_url().'photobooth.php?page=transaksi_photobooth&action=kelola');
    exit();

    
    

}






// ======================================================================
// ========================= VERIFIKASI VOUCHER =========================
// ======================================================================

if(isset($_GET['cek_voucher'])){
    
    $id_voucher = antiSQLi($_GET['cek_voucher']);
    $tgl_sekarang = date("Y-m-d");
    $jam_sekarang = date("H:i");
    
    $query = "SELECT * FROM voucher_photobooth WHERE kode_voucher='$id_voucher' AND start_tgl<='$tgl_sekarang' AND end_tgl>='$tgl_sekarang' AND status_rmv_voucher='N' AND start_jam<'$jam_sekarang' AND end_jam>'$jam_sekarang' AND max_pengguna>0 ";
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
    
    $data_transaksi = $db->query("SELECT A.id_photobooth_transaksi, A.no_nota, A.tanggal_photobooth_transaksi, A.nama_cust, A.telp_cust, A.jumlah_tiket, A.nominal_sebelum_diskon, A.diskon, A.total_transaksi, A.bayar, A.status_transaksi, A.keterangan_photobooth, B.nama_admin, C.nama_shift, D.nama_jenis_pembayaran FROM photobooth_transaksi A JOIN admin B ON A.kd_admin=B.id_admin JOIN shift C ON A.kd_shift=C.id_shift JOIN jenis_pembayaran D ON A.kd_jenis_pembayaran=D.id_jenis_pembayaran WHERE A.id_photobooth_transaksi='$id_transaksi'")->fetch_assoc();
    
    $data_detail = $db->query("SELECT A.kd_photobooth_stan, A.harga_satuan, B.nama_photobooth_stan, (SELECT SUM(PT.jumlah_tiket) FROM photobooth_tiket PT WHERE PT.kd_photobooth_transaksi='$id_transaksi' AND PT.kd_photobooth_stan=A.kd_photobooth_stan) AS jumlah FROM photobooth_tiket A JOIN photobooth_stan B ON A.kd_photobooth_stan=B.id_photobooth_stan WHERE kd_photobooth_transaksi='$id_transaksi' GROUP BY A.kd_photobooth_stan")->fetch_all(MYSQLI_ASSOC);
    
    $telp_profile = $db->query("SELECT telp_profile FROM profile WHERE id='1' LIMIT 1")->fetch_assoc()['telp_profile'];
    
    
    
    // CETAK INVOICE
    // $connector = new FilePrintConnector("//localhost/TM-T82");
    // $printer = new Printer($connector);
    // $printer -> setJustification(Printer::JUSTIFY_CENTER);
    // $printer -> graphics(EscposImage::load('../../dist/img/hehaocen.png'), Printer::IMG_DEFAULT | Printer::IMG_DEFAULT);
    // $printer -> setTextSize(2, 1);
    // $printer -> text(strtoupper('( SALINAN )'));
    // $printer -> setTextSize(1, 1);
    // $printer -> text("\n");
    // $printer -> text("\n");
    // $printer -> setTextSize(2, 1);
    // $printer -> text('SPOT FOTO');
    // $printer -> setTextSize(1, 1);
    // $printer -> setJustification(Printer::JUSTIFY_LEFT);
    // $printer -> text("\n");
    // $printer -> text("\n");
    // $printer -> text($data_transaksi['tanggal_photobooth_transaksi']." ".sprintf('%28s'," Kasir :".$data_transaksi['nama_admin']));
    // $printer -> text("\n");
    // $printer -> text("------------------------------------------------");
    // $printer -> text("                     RINCIAN                    ");
    // $printer -> text("------------------------------------------------");
    // $printer -> setJustification(Printer::JUSTIFY_LEFT);
    
    // foreach($data_detail as $key => $value){
        
    //     $jumlah = $value['jumlah'];
    //     $harga = $value['harga_satuan'];
    //     $nama_stan = $value['nama_photobooth_stan'];
    //     $harga_plus_jml = number_format($harga)." x ".$jumlah;
    //     $subtotal = $harga * $jumlah;
        
    //     $printer -> text("\n");
    //     $printer -> text(sprintf('%-48s',$nama_stan));
    //     $printer -> text("\n");
    //     $printer -> text(sprintf('%-24s', $harga_plus_jml).sprintf('%24s',number_format($subtotal)));
    //     $printer -> text("\n");
        
    // }
    
    // $printer -> text("------------------------------------------------");
    // $printer -> setJustification(Printer::JUSTIFY_LEFT);
    // $printer -> text("\n");
    // $printer -> text(sprintf('%-24s',' ').sprintf('%13s','Total: ').sprintf('%11s',number_format($data_transaksi['nominal_sebelum_diskon'])));
    // $printer -> text("\n");
    // $printer -> text(sprintf('%-24s',' ').sprintf('%13s','Diskon: ').sprintf('%11s',number_format($data_transaksi['diskon'])));
    // $printer -> text("\n");
    // $printer -> text(sprintf('%-24s',' ').sprintf('%13s','Harga Akhir: ').sprintf('%11s',number_format($data_transaksi['total_transaksi'])));
    // $printer -> text("\n");
    // $printer -> text(sprintf('%-30s',$data_transaksi['nama_jenis_pembayaran'])."Bayar: ".sprintf('%11s',number_format($data_transaksi['bayar'])));
    // $printer -> text("\n");
    // $printer -> text(sprintf('%-24s','CS: '. $telp_profile).sprintf('%13s','Kembalian: ').sprintf('%11s',number_format($data_transaksi['bayar'] - $data_transaksi['total_transaksi'])));
    // $printer -> text("\n");
    // $printer -> text("\n");
    // $printer -> cut();
    // $printer -> close();
    
    
    $data['no_struk'] = $data_transaksi['no_nota'];
    $data['tanggal'] = $data_transaksi['tanggal_photobooth_transaksi'];
    $data['kasir'] = $data_transaksi['nama_admin'];
    $data['detail'] = $data_detail;
    $data['total_transaksi'] = $data_transaksi['nominal_sebelum_diskon'];
    $data['diskon'] = $data_transaksi['diskon'];
    $data['total_setalah_diskon'] = $data_transaksi['total_transaksi'];
    $data['nama_jenis_pembayaran'] = $data_transaksi['nama_jenis_pembayaran'];
    $data['bayar'] = $data_transaksi['bayar'];
    $data['telp_profile'] = $telp_profile;
    $data['keterangan'] = $data_transaksi['keterangan_photobooth'];
    $data_json = json_encode($data);
    printClient($_SESSION['printer'], 'photobooth_bill_salinan', $data_json, $printer_tiket_photobooth);
    
    echo "<script>window.history.back();</script>";
    
}







// ======================================================================
// ================== CETAK ULANG TIKET PHOTOBOOTH ======================
// ======================================================================

if(isset($_GET['cetak_ulang_tiket_satuan'])){
    
    $id_tiket = enkripsiDekripsi(antiSQLi($_GET['cetak_ulang_tiket_satuan']), 'dekripsi');
    
    $data_transaksi = $db->query("SELECT PT.no_urut, PT.jumlah_tiket, P.nama_cust, PS.nama_photobooth_stan, P.tanggal_photobooth_transaksi, A.nama_admin FROM photobooth_tiket PT JOIN photobooth_stan PS ON PT.kd_photobooth_stan=PS.id_photobooth_stan JOIN photobooth_transaksi P ON PT.kd_photobooth_transaksi=P.id_photobooth_transaksi JOIN admin A ON P.kd_admin=A.id_admin WHERE PT.id_photobooth_tiket='$id_tiket'")->fetch_assoc();
    
    if(!isset($data_transaksi)){
        echo "<script>window.history.back();</script>";
    }
    
    // $codeContents = $id_tiket;
    
    // QRcode::png($codeContents,"temp_qrcode/".$codeContents.".png");
    
    // $connector = new FilePrintConnector("//localhost/TM-T82");
    // $printer = new Printer($connector);
    // $printer -> setJustification(Printer::JUSTIFY_CENTER);
    // $logo    = EscposImage::load("../../dist/img/hehaocen-150.png");
    // $printer -> graphics($logo);
    // $printer -> setTextSize(2, 1);
    // $printer -> text(strtoupper('( SALINAN )'));
    // $printer -> setTextSize(1, 1);
    // $printer -> text("\n");
    // $printer -> setTextSize(2, 1);
    // $printer -> text(strtoupper($data_transaksi['nama_photobooth_stan']));
    // $printer -> setTextSize(1, 1);
    // $printer -> text("\n");
    // $printer -> text("".tanggal_jam_indo($data_transaksi['tanggal_photobooth_transaksi'])."");
    // $printer -> text("\n");
    // $printer -> text("Nomor Antrean :");
    // $printer -> text("\n");
    // $printer -> text("\n");
    // $printer -> setTextSize(5, 4);
    // $printer -> text("".$data_transaksi['no_urut']);
    // $printer -> setTextSize(1, 1);
    // $printer -> text("\n");
    // $printer -> text("\n");
    // $printer -> text("Tiket berlaku utuk ".$data_transaksi['jumlah_tiket']." Orang");
    // $printer -> text("\n");
    // $printer -> text("\n");
    // $printer -> text("\n");
    // $printer -> cut();
    // $printer -> close();
    // // unlink("temp_qrcode/".$codeContents.".png");


    $data['id_tiket'] = '';
    $data['nama_photobooth'] = $data_transaksi['nama_photobooth_stan'];
    $data['tanggal'] = tanggal_jam_indo($data_transaksi['tanggal_photobooth_transaksi']);
    $data['no_antrean'] = $data_transaksi['no_urut'];
    $data['berlaku_untuk'] = "Tiket berlaku untuk ".$data_transaksi['jumlah_tiket']." orang";
    $data['diberikan_kepada'] = "Untuk Pengunjung";
    $data['nama_cust'] = $data_transaksi['nama_cust'];
    $data_json = json_encode($data);
    printClient($_SESSION['printer'], 'photobooth_salinan', $data_json, $printer_tiket_photobooth);


    $data['diberikan_kepada'] = "Untuk Patugas";
    $data_json = json_encode($data);
    printClient($_SESSION['printer'], 'photobooth_salinan', $data_json, $printer_tiket_photobooth);

    
    echo "<script>window.history.back();</script>";
}
        


?>

