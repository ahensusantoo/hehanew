<?php  
  
    $query = "SELECT * FROM jenis_tiket WHERE status_display_tiket='Y' AND status_remove_tiket='N' ";
    $jenis_tiket = $db->query($query)->fetch_all(MYSQLI_ASSOC);


    // CEK TIKET UNTUK HARI INI
    $hari_sekarang = date('N');
    $tanggal_sekarang = date('d');
    $jam_sekarang = date("H:i");
    $tahun_bulan_sekarang = date('Y-m');

    $hari_libur = @$db->query("SELECT COUNT(hari_libur) AS jml FROM hari_libur WHERE tahun_bulan='$tahun_bulan_sekarang' AND hari_libur LIKE '%$tanggal_sekarang%'")->fetch_assoc()['jml'];
    if ($hari_libur == "") {
        $hari_libur = 0;
    }
    if ((int)$hari_libur > 0) { //KETIKA HARI INI HARI LIBUR
        $tiket_sekarang = $db->query("SELECT * FROM jenis_tiket WHERE status_hari_libur='3' AND status_display_tiket='Y' AND start_jam<='$jam_sekarang' AND end_jam>='$jam_sekarang'  AND status_remove_tiket='N'")->fetch_assoc();
        if ($tiket_sekarang == "") {
            $tiket_sekarang = $db->query("SELECT * FROM jenis_tiket WHERE start_hari<='$hari_sekarang' AND end_hari>='$hari_sekarang' AND status_display_tiket='Y' AND start_jam<='$jam_sekarang' AND end_jam>='$jam_sekarang'  AND status_remove_tiket='N'")->fetch_assoc();
        }
    }else{ //KETIKA HARI INI BUKAN HARI LIBUR
        $tiket_sekarang = $db->query("SELECT * FROM jenis_tiket WHERE start_hari<='$hari_sekarang' AND end_hari>='$hari_sekarang' AND status_display_tiket='Y' AND start_jam<='$jam_sekarang' AND end_jam>='$jam_sekarang'  AND status_remove_tiket='N'")->fetch_assoc();
    }



    $list_pembayaran = $db->query("SELECT * FROM jenis_pembayaran WHERE status_aktif='Y' ");


    $get_daftar_kota = $db->query("SELECT * FROM indo_cities")->fetch_all(MYSQLI_ASSOC);

    // print_r("<pre>"); print_r($get_daftar_kota); die();

?>
<style>
    input:focus, select:focus, button:focus{
        box-shadow: 0px 0px 5px blue !important;
    }
</style>

<link rel="stylesheet" href="plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

<section class="content pt-2">
    <div class="container-fluid">
        <?php if (isset($_SESSION['notifikasi'])) { notifikasi($_SESSION['notifikasi']); } ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <form role="form" id="frm_transaksi" method="post" action="view/transaksi/proses_data.php" enctype="multipart/form-data" autocomplete="off">
                        <div class="card-body">
                            <div class="form-group">
                                <button type="button" id="btn_ganti_tiket" class="badge badge-primary" data-toggle="modal" data-target="#modal_jenis_tiket">Ganti</button>
                                <label for="nama_tiket">JENIS TIKET : <span id="nama_tiket"><?= @$tiket_sekarang['nama_jenis_tiket'] ?></span></label>
                                <br>
                                <button type="button" class="badge badge-primary" id="btn_ganti_agen" data-toggle="modal" data-target="#modal_agen">Ganti</button>
                                <label for="nama_tiket">SALES/MARKETING/JASA WISATA : <span id="txt_nama_agen">Tidak Ada</span></label>

                                <input type="hidden" name="id_jenis_tiket" id="id_jenis_tiket" value="<?= enkripsiDekripsi($tiket_sekarang['id_jenis_tiket'],'enkripsi') ?>">
                            </div>
                            <label>Ringkasan :</label>
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <td>Harga</td>
                                        <td>:</td>
                                        <td id="harga_tiket_satuan">Rp <?= number_format(@$tiket_sekarang['harga_tiket']) ?></td>
                                        <td>Total Tagihan</td>
                                        <td>:</td>
                                        <td>Rp <span id="txt_tagihan">0</span></td>
                                    </tr>
                                    <tr>
                                        <td>Sub total</td>
                                        <td>:</td>
                                        <td id="txt_sub_total">Rp 0</td>
                                        <td>Jumlah Bayar</td>
                                        <td>:</td>
                                        <td id="txt_jml_bayar">Rp 0</td>
                                    <tr>
                                        <td>Diskon</td>
                                        <td>:</td>
                                        <td>Rp <span id="txt_diskon_rincian">0</span></td>
                                        <td>Kembalian</td>
                                        <td>:</td>
                                        <td id="txt_kembalian">Rp 0</td>
                                    </tr>
                                </tbody>
                            </table>

                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="nama_cust">Nama Pembeli</label>
                                        <input type="text" name="nama_cust" class="form-control" id="nama_cust" placeholder="Nama Pembeli" maxlength="300">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="telp_cust">No. Telepon Pembeli</label>
                                        <input type="text" name="telp_cust" class="form-control" id="telp_cust" placeholder="Telepon Pembeli" maxlength="300">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="jumlah_tiket">Jumlah Beli</label>
                                        <input type="text" name="jumlah_tiket" id="jumlah_tiket" class="form-control" placeholder="Jumlah tiket dibeli" maxlength="300" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-4 pt-2">
                                        <button type="button" data-toggle="modal" id="btn_voucher" data-target="#modal_diskon" class="btn btn-sm btn-outline-secondary">Diskon</button>
                                        <button style="display: none;" type="button" id="btn_batalkan_voucher" class="btn btn-sm btn-outline-danger">Hapus Diskon</button>
                                        : <span id="txt_diskon">Tanpa Diskon</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Jenis Pembayaran</label>
                                        <select class="form-control select2" name="jenis_pembayaran">
                                        <?php $pembayaran_filter = "Samua Jenis Pembayaran" ?>
                                        <?php foreach ($list_pembayaran as $key => $value): ?>
                                            <?php if(enkripsiDekripsi($value['id_jenis_pembayaran'],'enkripsi') == @$_GET['jenis_pembayaran'] ){ $pembayaran_filter = $value['nama_jenis_pembayaran']; } ?>
                                            <option value="<?= enkripsiDekripsi($value['id_jenis_pembayaran'],'enkripsi') ?>"><?= $value['nama_jenis_pembayaran'] ?></option>
                                        <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bayar">Bayar</label>
                                        <input type="text" id="fld_jml_bayar" name="bayar" onkeyup="format_rupiah(this)" name="bayar" class="form-control" placeholder="Jumlah Pembayaran" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="ket">Keterangan</label>
                                        <input type="text" class="form-control" name="keterangan">
                                    </div>
                                </div>
                            </div>
                            <input type="submit" name="tambah_transaksi_baru" class="btn btn-primary btn-block mt-0" id="btnSubmit" value="SIMPAN">
                        </div>
                        <input type="hidden" name="tambah_transaksi_baru">
                        <input type="hidden" name="nominal_diskon" id="fld_diskon">
                        <input type="hidden" name="jenis_diskon" id="fld_diskon_jenis">
                        <input type="hidden" name="isi_diskon" id="fld_diskon_kode">
                        <input type="hidden" name="harga_sebelum_diskon" id="fld_harga_sebelum_diskon">
                        <input type="hidden" name="harga_tiket_satuan" id="fld_harga_tiket" value="<?= @$tiket_sekarang['harga_tiket'] ?>">
                        <input type="hidden" name="harga_setelah_diskon" id="fld_harga_setelah_diskon" value="0">
                        <input type="hidden" name="kembalian" id="fld_kembalian" value="0">
                        <input type="hidden" name="id_agen" id="fld_id_agen" value="0">
                        <div class="card-footer">
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
            </div>
        </div>
    </div>
</section>


<!-- Modal Tiket-->
<div class="modal fade" id="modal_jenis_tiket" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Jenis Tiket</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table text-center table-bordered table-sm" style="font-size: 80%;">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Keterangan</th>
                            <th>Harga</th>
                            <th>Pilih</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jenis_tiket as $key => $value): ?>
                        <tr>
                            <td style="vertical-align: middle;">
                                <?= $value['nama_jenis_tiket'] ?>
                            </td>
                            <td>
                                <?= angkaKeHari($value['start_hari']) ?> - <?= angkaKeHari($value['end_hari']) ?><br><?= $value['start_jam'] ?> WIB - <?= $value['end_jam'] ?> WIB
                            </td>
                            <td style="vertical-align: middle;">
                                Rp <?= number_format($value['harga_tiket']) ?>
                            </td>
                            <td style="vertical-align: middle;">
                                <button type="button" class="btn btn-sm btn-block btn-info btn_pilih_jenis"
                                    data-id="<?= enkripsiDekripsi($value['id_jenis_tiket'],'enkripsi') ?>" 
                                    data-nama="<?= $value['nama_jenis_tiket'] ?>" 
                                    data-harga="<?= $value['harga_tiket'] ?>">PILIH
                                </button>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Diskon -->
<div class="modal fade" id="modal_diskon" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Input Diskon</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frm_diskon">
                    <div class="form-group">
                        <label>Jenis Diskon</label>
                        <select class="form-control" id="select_jenis_diskon">  
                            <option value="persen">Persen</option>
                            <option value="rupiah">Rupiah</option>
                            <option value="voucher">Voucher</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Diskon / Kode Voucher</label>
                        <input type="text" id="input_diskon" class="form-control" name="">
                    </div>
                    <hr>
                    <button id="btn_terapkan_diskon" class="btn btn-block btn-outline-secondary">Terapkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- modal cari agen -->
<div class="modal fade" id="modal_agen" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Agen / Jasa Wisata</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="text" autocomplete="off" class="form-control" id="fld_cari_agen">
                <hr>
                <div id="tab_cari_agen">
                    <center>Hasil Cari ...</center>
                </div>
            </div>
            <div class="modal-footer" style="display: block">
                <div class="row">
                    <div class="col-6">
                        <button id="btn_batal_agen" class="btn btn-outline-secondary btn-block">Batalkan Sales</button>
                    </div>
                    <div class="col-6">
                        <button id="btn_tambah_agen" class="btn btn-primary btn-block">Tambah Data</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- modal add agen -->
<div class="modal fade" id="modal_tambah_agen" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Agen / Jasa Wisata</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frm_tambah_agen">
                    <div class="form-group">
                        <label>Nama Agen</label>
                        <input type="hidden" class="form-control" name="tambah_agen_json">
                        <input type="text" class="form-control" name="nama" required autocomplete="off" style="text-transform: uppercase !important;">
                    </div>
                    <div class="form-group">
                        <label>No Identitas Agen</label>
                        <input type="text" class="form-control" name="no_identitas" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label>No Telepon Agen</label>
                        <input type="text" class="form-control" name="telp" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label>Kota Agen</label>
                        <select name="kota" class="form-control select2bs4">
                            <option value="" selected hidden>PILIH KOTA AGEN</option>
                            <?php foreach($get_daftar_kota as $key => $value) { ?>
                            <option value="<?= enkripsiDekripsi($value['city_id'], 'enkripsi') ?>"><?= $value['city_name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Alamat Agen</label>
                        <textarea class="form-control" name="alamat" required autocomplete="off"></textarea>
                    </div>
                    <div class="form-group">
                        <hr>
                        <button type="submit" class="btn btn-block btn-primary">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="plugins/select2/js/select2.full.min.js"></script>


<script type="text/javascript">

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })

</script>

<script>
    var allow_submit = false;

    $("#frm_transaksi").submit(function(e){
        if (allow_submit == false) {
            e.preventDefault();
            var gagal = false;
            var jml_tiket = parseInt($("#jumlah_tiket").val().replace(/[^0-9]/g, '')) || 0;
            var tiket = $("#fld_harga_tiket").val();
            var jml_bayar = parseInt($("#fld_jml_bayar").val().replace(/[^0-9]/g, '')) || 0;
            var harga_setelah_diskon = parseInt($("#fld_harga_setelah_diskon").val().replace(/[^0-9]/g, '')) || 0;
           
            if (tiket == "") {
                alert("Jenis tiket belum dipilih");
            }

            if (jml_tiket <= 0) {
                alert("Jumlah tiket belum dimasukkan");
            }

            if (jml_bayar < harga_setelah_diskon) {
                alert("Jumlah pembayaran kurang dari nominal tagihan");
            }

            // console.log(jml_bayar);
            // console.log(harga_setelah_diskon);

            if (gagal == false) {
                allow_submit = true;
                $("#frm_transaksi").submit();
                // console.log('submit');
            }else{ 
                // console.log('non submit');
            }
        }
    });

    $("#btn_ganti_tiket").focus();

    function hitung_harga(){
        var jml_tiket = parseInt($("#jumlah_tiket").val().replace(/[^0-9]/g, '')) || 0;
        var harga = parseInt($("#fld_harga_tiket").val().replace(/[^0-9]/g, '')) || 0;
        var diskon = parseInt($("#fld_diskon").val().replace(/[^0-9]/g, '')) || 0;
        var diskon_jenis = $("#fld_diskon_jenis").val();
        
        // alert($("#fld_jml_bayar").val())
        var sub_total = jml_tiket * harga;
        var tagihan = jml_tiket * harga;
        $("#fld_harga_sebelum_diskon").val(tagihan);
        
          // alert(diskon_jenis)
        if (diskon_jenis == 'rupiah') {
            tagihan = tagihan - diskon; 

        }else if(diskon_jenis == "persen"){
            diskon = tagihan*diskon/100;
            tagihan = tagihan - diskon; 
        }

        var bayar =  tagihan

        var kembalian = tagihan - bayar;
        if (kembalian > 0) {
            kembalian = 0;
        }
        kembalian = Math.abs(kembalian);

        $("#txt_jml_bayar").html("Rp "+parseInt(tagihan).toLocaleString());
        $("#txt_kembalian").html("Rp "+parseInt(kembalian).toLocaleString());
        $("#txt_diskon_rincian").html(parseInt(diskon).toLocaleString());
        $("#txt_sub_total").html(parseInt(sub_total).toLocaleString());
        $("#txt_tagihan").html(parseInt(tagihan).toLocaleString());
        $("#fld_jml_bayar").val(parseInt(tagihan).toLocaleString());
        $("#fld_harga_setelah_diskon").val(parseInt(tagihan).toLocaleString());
        $("#fld_kembalian").val(kembalian);
    }

    function hitung_kembalian(){
        var jml_tiket = parseInt($("#jumlah_tiket").val().replace(/[^0-9]/g, '')) || 0;
        var harga = parseInt($("#fld_harga_tiket").val().replace(/[^0-9]/g, '')) || 0;
        var diskon = parseInt($("#fld_diskon").val().replace(/[^0-9]/g, '')) || 0;
        var diskon_jenis = $("#fld_diskon_jenis").val();
        var bayar = parseInt($("#fld_jml_bayar").val().replace(/[^0-9]/g, '')) || 0;
        // alert($("#fld_jml_bayar").val())
        
        var tagihan = jml_tiket * harga;
        $("#fld_harga_sebelum_diskon").val(tagihan);
        
          // alert(diskon_jenis)
        if (diskon_jenis == 'rupiah') {
            tagihan = tagihan - diskon; 

        }else if(diskon_jenis == "persen"){
            diskon = tagihan*diskon/100;
            tagihan = tagihan - diskon; 
        }

        var kembalian =  parseInt(bayar) - parseInt(tagihan);
        // console.log(tagihan)
        if (parseInt(kembalian) < 0) {
            $('#txt_kembalian').addClass("text-danger")
        }else{
            $('#txt_kembalian').removeClass("text-danger")
        }
        // kembalian = Math.abs(kembalian);
        // kembalian = Math.abs(kembalian);

        $("#txt_jml_bayar").html("Rp "+$('#fld_jml_bayar').val().toLocaleString());
        $("#txt_kembalian").html("Rp "+kembalian.toLocaleString());
    }

    $("#jumlah_tiket").keyup(function(){
        hitung_harga();
    })

    $("#fld_jml_bayar").keyup(function(){
        hitung_kembalian();
    })

    function format_angka(arg){
        var bayar = $(arg).val().replace(/[^0-9]/g, '');
        if (bayar == 0) {bayar = 0}
        $(arg).val("Rp "+parseInt(bayar).toLocaleString());
    }

    function format_rupiah(arg){
        var bayar = $(arg).val().replace(/[^0-9]/g, '');;
        if (bayar == 0) {bayar = 0}
        $(arg).val("Rp "+parseInt(bayar).toLocaleString());
    }

    $(".btn_pilih_jenis").click(function(){
        var id = $(this).attr("data-id");
        var nama = $(this).attr("data-nama");
        var harga = $(this).attr("data-harga");

        $("#nama_tiket").html(nama);
        $("#id_jenis_tiket").val(id); 
        $("#fld_harga_tiket").val(harga); 
        $("#harga_tiket_satuan").html("Rp "+parseInt(harga).toLocaleString());
        $("#modal_jenis_tiket .close").trigger("click");
    });


    $("#fld_cari_agen").keyup(function(){
        var key = $("#fld_cari_agen").val();
        $("#tab_cari_agen").empty();
        if (key != "") {
            $.ajax({
                url: 'view/transaksi/proses_data.php?cari_agen='+key,
                dataType: 'json',
                success:function(respon){
                    // console.log(respon)
                    $.each( respon.data, function( key, value ) {
                        $("#tab_cari_agen").append(`
                            <button data-id="`+value.id_agen+`" data-nama="`+value.nama_agen+`" data-telp="`+value.no_telp_agen+`" class="btn-block btn btn-outline-secondary text-left btn_pilih_agen">
                                `+value.nama_agen+` <br>
                                `+value.no_identitas_agen+` <br>
                                Alamat :`+value.alamat_agen+` <br>
                                Telp : `+value.no_telp_agen+` <br>
                            </button>
                        `)
                    });
                }
            })
        }
    })

    function agen_terpilih(id, nama, telp){
        $("#txt_nama_agen").html(nama);
        $("#nama_cust").val(nama);
        $("#telp_cust").val(telp);
        $("#fld_id_agen").val(id);
    }

    $("#tab_cari_agen").on('click', '.btn_pilih_agen', function(){
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var telp = $(this).data('telp');
        agen_terpilih(id, nama, telp)
        
        $("#modal_agen .close").trigger("click");
    })

    $("#btn_batal_agen").click(function(){
        $("#txt_nama_agen").html('Tidak Ada');
        $("#nama_cust").val('');
        $("#telp_cust").val('');
        $("#fld_id_agen").html('');
        $("#modal_agen .close").trigger("click");
    })

    $("#btn_tambah_agen").click(function(){
        $('#modal_agen').modal('hide');
        $('#modal_tambah_agen').modal('show');
    })

    $('#modal_tambah_agen').on('hidden.bs.modal', function () {
        $("#btn_ganti_agen").focus();
    })

    $("#frm_tambah_agen").submit(function(e){
    e.preventDefault();
        $.ajax({
            url: 'view/transaksi/proses_data.php',
            dataType: 'json',
            type: 'POST',
            data: $('#frm_tambah_agen').serialize(),
            success:function(respon){
                // console.log(respon);
                if (respon.status == "200") {
                    alert("Berhasil Menambahkan Data");
                    $("#frm_tambah_agen").trigger("reset");
                    $('#modal_tambah_agen .close').trigger('click');

                    //hbs nambah agen langsung agen ini yg terpilih di transaksi
                    agen_terpilih(respon.record.id, respon.record.nama, respon.record.telp)

                }else if(respon.status == "500"){
                    alert(respon.data);
                }
            }
        })
    })


    $("#input_diskon").keyup(function(){
        var jenis = $("#select_jenis_diskon").val();
        var kode = $("#input_diskon").val();
        if (jenis == "voucher") {
            // console.log('Y')
            $.ajax({
                url : 'view/transaksi/proses_data.php?cek_voucher='+kode,
                success:function(result){
                    var data = result;
                    var obj = JSON.parse(data);
                    if(obj.kode == "Y"){
                        alert('Y')
                    }else if(obj.kode == "N"){
                        // alert('N')
                    }
                }

            })
        }
    })

    $("#frm_diskon").submit(function(e){
        e.preventDefault();

        var jenis = $("#select_jenis_diskon").val();
        var kode = $("#input_diskon").val();
        var nominal_akhir = 0;

        if (jenis == "voucher") {
          // console.log('Y')
            $.ajax({
                url : 'view/transaksi/proses_data.php?cek_voucher='+kode,
                success:function(result){
                    var data = result;
                    var obj = JSON.parse(data);

                    if (obj.tipe == "2") { // PERSEN
                        $("#fld_diskon_jenis").val('persen');
                        $("#txt_diskon").html("Diskon "+obj.value+"%");
                    }else if(obj.tipe == "1"){ // NOMINAL
                        $("#fld_diskon_jenis").val('rupiah');
                        $("#txt_diskon").html("Diskon Rp "+obj.value+"");
                    }

                    $("#fld_diskon_kode").val(kode);
                    $("#fld_diskon").val(obj.value);
                    nominal_akhir = obj.value;

                    // hitungbiaya2()
                    // $(".spinner_validasi_voucher").hide();
                }
            })
        }else{
            if (jenis == "rupiah") {
                $("#fld_diskon_jenis").val('rupiah');
                $("#txt_diskon").html("Diskon Rp "+kode+"");
            }else if(jenis == "persen"){
                $("#fld_diskon_jenis").val('persen');
                $("#txt_diskon").html("Diskon "+kode+"%");
            }

            nominal_akhir = kode;
            $("#fld_diskon").val(kode);
            $("#fld_diskon_kode").val(kode);
        }

        if(nominal_akhir > 0){
            $("#btn_batalkan_voucher").show(function(){
                $("#btn_batalkan_voucher").focus();
            });
            $("#btn_voucher").hide();
        }else{
            $("#btn_batalkan_voucher").hide();
            $("#btn_voucher").show(function(){
                $("#btn_voucher").focus();
            });
        }

        hitung_harga();
        $("#modal_diskon .close").trigger("click");
    })

    $("#btn_batalkan_voucher").click(function(){
        $("#fld_diskon_jenis").val('');
        $("#txt_diskon").html("Tanpa Diskon");
        $("#fld_diskon_kode").val('');
        $("#fld_diskon").val('');
        $("#btn_batalkan_voucher").hide();
        
        $("#btn_voucher").show(function(){
            $("#btn_voucher").focus();
        });
        hitung_harga();
    })


  // $("input[name=jumlah_tiket]").keyup(function(){
  //   var harga_satuan = $("#harga_tiket_satuan").html().replace(/[^0-9]/g, '');
  //   var jumlah_tiket = $("input[name=jumlah_tiket]").val().replace(/[^0-9]/g, '');;
  //   var total_harga = harga_satuan * jumlah_tiket ;

  //   $("input[name=total_harga]").val("Rp "+parseInt(total_harga).toLocaleString());
  // })

  // $("input[name=bayar]").keyup(function(){
  //   var total_harga = $("input[name=total_harga]").val().replace(/[^0-9]/g, '');;
  //   var bayar = $("input[name=bayar]").val().replace(/[^0-9]/g, '');;
  //   var total_kembalian = bayar - total_harga;

  //   if(total_kembalian < 0){
  //     total_kembalian = 0;      
  //   }

  //   $("input[name=kembalian]").val("Rp "+parseInt(total_kembalian).toLocaleString());

  // })
</script>