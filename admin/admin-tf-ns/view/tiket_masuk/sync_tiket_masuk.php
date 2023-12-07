<?php 
    if( empty($_POST['tgl_filter']) ){
        $tgl_filter = date('Y-m-d');
    } else {
        $tgl_filter = date('Y-m-d', strtotime($_POST['tgl_filter']));
    }

    // print_r($tgl_filter); die();

    // server asli heha
    $count_ori_serve = $db->query("
        SELECT 
            COUNT(DISTINCT(A.id_transaksi)) as jml_transaksi, 
            COUNT(B.kd_transaksi) as jml_tiket, 
            (SELECT COALESCE(SUM(A.total_transaksi), 0)
                FROM transaksi A
                WHERE DATE(A.tanggal_transaksi) = '$tgl_filter'
                AND A.status_transaksi != '3'
            ) as total_transaksi 
        FROM transaksi A
        JOIN tiket B ON A.id_transaksi=B.kd_transaksi
        WHERE DATE(A.tanggal_transaksi) = '$tgl_filter'
            AND A.status_transaksi != '3'
    ")->fetch_assoc();


    // data duplicate server
    $response_newserver = @CRUD_API(base_url_newserve()."api/tiket_masuk/count_pendapatan" ,json_encode([
        'act'           => "single_row",
        'tgl_filter'    => $tgl_filter,
    ]));

    //status SYNC
    $status_sync = $db->query("
        SELECT * 
        FROM stt_tf_tiket A 
        LEFT JOIN admin B ON A.kd_user_sncy_tf = B.id_admin
        WHERE DATE(A.tanggal_tf) = '$tgl_filter' 
        ORDER BY id_stt_tf DESC
    ")->fetch_assoc();

    // print_r("<pre>"); print_r($status_sync); die();

?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">SYNC Tiket Masuk</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="form-group row">
                            <label class="col-sm-1 col-form-label">Periode :</label>
                            <div class="col-sm-11">
                                <button type="button" class="btn btn-default" id="daterange-btn" style="width: 100%">
                                    <i class="far fa-calendar-alt"></i> <span id="reportrange"><?= date('j F Y', strtotime($tgl_filter)); ?></span>
                                    <i class="fas fa-caret-down"></i>
                                </button>
                            </div>
                        </div>
            
                        <a onclick="terapkan('<?= $tgl_filter ?>')">
                            <button type="button" class="btn btn-primary" style="width: 100%">Terapkan</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <style type="text/css" media="screen">
            .card.status-sync{
                text-align: center; color: white; font-size: 20px; font-weight: bold;
            }

            .card.status-sync .danger{
                background-color: red;
            }

            .card.status-sync .warning{
                background-color: yellow;
                color: black !important;
            }

            .card.status-sync .success{
                background-color: green;
                /*opacity: 0.8;*/
            }

        </style>
        <div class="row">
            <div class="col-12">
                <div class="card status-sync">
                    <?php 
                        if(empty($status_sync) ) {
                            $sync_color = "danger";
                            $sync_pesan = " Tidak dapat melakukan cutting original server pada periode ".tanggal_indo($tgl_filter)." ini";
                            $disabled = "disabled";
                        }else{
                            if($status_sync['status_tf'] == "0"){
                                $disabled   = "";
                                $sync_color = "warning";
                                $sync_pesan = " Belum melakukan cutting original server pada periode ".tanggal_indo($tgl_filter)." ini";
                            }else{
                                $disabled   = "disabled";
                                $sync_color = "success";
                                $sync_pesan = " Anda sudah melakukan cutting original server pada periode ".tanggal_indo($tgl_filter)." ini";
                            }
                        }
                    ?>

                    <div class="card-body <?= $sync_color ?>">
                        <?= $sync_pesan ?>
                        <?php if(!empty($status_sync)) { ?>
                            <?php if( $status_sync['kd_user_sncy_tf'] != null ) { ?>
                                <div class="biodata-admin" style="text-align: left !important; margin-top: 35px; font-size: 12px !important;">
                                    <table class="table" width="50%" style="width: 30% !important;">
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th><?= $status_sync['username_admin'] ?></th>
                                            </tr>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th><?= tanggal_jam_indo($status_sync['tgl_update_tf']) ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-md-12 col-12 mb-2">
                <div class="card">
                    <div class="card-header">
                        SERVER ASLI
                    </div>
                    <div class="card-body">
                        <div class="table-responisve">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Jumlah Transaksi</th>
                                        <th>Jumlah Tiket</th>
                                        <th>Nominal TIket</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if( $count_ori_serve['jml_transaksi'] > 0 ) : ?>
                                        <tr>
                                            <td><?= number_format($count_ori_serve['jml_transaksi']) ?></td>
                                            <td><?= number_format($count_ori_serve['jml_tiket']) ?></td>
                                            <td><?= number_format($count_ori_serve['total_transaksi']) ?></td>
                                        </tr>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="3" class="text-center">Tidak Ada Data</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-12 mb-2">
                <div class="card">
                    <div class="card-header">
                        SERVER DUPLICATE
                    </div>
                    <div class="card-body">
                        <div class="table-responisve">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Jumlah Transaksi</th>
                                        <th>Jumlah Tiket</th>
                                        <th>Nominal TIket</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if( $response_newserver['query']['jml_transaksi'] > 0 ) : ?>
                                        <tr>
                                            <td><?= number_format($response_newserver['query']['jml_transaksi']) ?></td>
                                            <td><?= number_format($response_newserver['query']['jml_tiket']) ?></td>
                                            <td><?= number_format($response_newserver['query']['total_transaksi']) ?></td>
                                        </tr>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="3" class="text-center">Tidak Ada Data</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body" style="text-align: center;">
                        
                        <?php if($count_ori_serve['total_transaksi'] > 0 ) : ?>
                            <button type="button" id="sync_two_server" class="btn btn-primary btn-flat" <?= $disabled ?> >
                                <i class="fa fa-sync-alt"></i>
                                Sinkronisasi
                            </button>
                            
                            <!-- check status sync -->
                            <?php if(!empty($status_sync)) { ?>
                                <?php if($status_sync['status_tf'] == "0") { ?>
                                    <button type="button" id="proses_two_server" class="btn btn-success btn-flat" <?= $response_newserver['query']['total_transaksi'] == $count_ori_serve['total_transaksi'] ? null : 'disabled' ?> <?= $disabled ?> >
                                        <i class="fa fa-paper-plane"></i>
                                        Proses Cut
                                    </button>
                                <?php } ?>
                            <?php } ?>
                        <?php else : ?>
                            <?= "Harus ada transaksi dulu" ?>    
                        <?php endif; ?>    

                    </div>
                </div>
            </div>
        </div>


    </div>
</section>

<script>
    $(document).ready(function(){
        // proses sync
        $('#sync_two_server').on('click',function(){
            // alert()
            var tgl_filter = "<?= $tgl_filter ?>"
            $.ajax({
                url         : "view/tiket_masuk/proses_data.php",
                type        : "post",
                dataType    : "json",
                data        : {
                    sync_two_server : true, 
                    tgl_filter : tgl_filter
                },
                success: function (response) {
                    // console.log(response)
                    if(response.status){
                        alert(response.pesan);
                        location.reload();
                    }else{
                        alert(response.pesan);
                        location.reload();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert("Terjadi kesalahan, silahkan coba kembali");
                    location.reload();
                }
            });
        }); 

        // proses cut
        $('#proses_two_server').on('click',function(){
            // alert()
            var tgl_filter = "<?= $tgl_filter ?>"
            $.ajax({
                url         : "view/tiket_masuk/proses_data.php",
                type        : "post",
                dataType    : "json",
                data        : {
                    proses_two_server : true, 
                    tgl_filter : tgl_filter
                },
                success: function (response) {
                    // console.log(response)
                    if(response.status){
                        alert(response.pesan);
                        location.reload();
                    }else{
                        alert(response.pesan);
                        location.reload();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert("Terjadi kesalahan, silahkan coba kembali");
                    location.reload();
                }
            });
        }); 
    });
</script>

<script type="text/javascript">
    $(function () {
        $('#daterange-btn').daterangepicker(
        {
                singleDatePicker: true,
                showDropdowns: true,
                // minYear: 1901,
                maxYear: parseInt(moment().format('YYYY'),10),
                startDate : '<?= date("m/d/Y", strtotime($tgl_filter)) ?>',
                endDate : '<?= date("m/d/Y", strtotime($tgl_filter)) ?>'
            },
            function (start, end, label) {
                // var years = moment().diff(start, 'years');
                // alert("You are " + end + " years old!");
                $('#reportrange').html(start.format('D MMMM YYYY'));
                // var form = $('<form action="?page=penjualan&action=perkasir" method="post">' +
                //   '<input type="hidden" name="tgl_awal" value="' + start.format('YYYY-MM-D') + '" />' +
                //   '<input type="hidden" name="tgl_akhir" value="' + end.format('YYYY-MM-D') + '" />' +
                //   '</form>');
                // $('body').append(form);
                // form.submit();
            }
        )
    })

    function terapkan(){
        // var tgl_filter = document.getElementById('reportrange');
        var tanggal = document.getElementById('reportrange').innerHTML.split(" - ");
        // var filer1 = document.getElementById('filer1').value;

         var start_master = new Date(tanggal[0]);
        var tgl_filter = start_master.getFullYear() + "-" + String(start_master.getMonth() + 1) + "-" + String(start_master.getDate());

        // var end_master = new Date(tanggal[1]);
        // var end = end_master.getFullYear() + "-" + String(end_master.getMonth() + 1) + "-" + String(end_master.getDate());


        // alert(tgl_filter);

        var form = $('<form action="?page=sync_tiket_masuk" method="post">' +
            '<input type="hidden" name="tgl_filter" value="' + tgl_filter + '" />' +
            // '<input type="hidden" name="tgl_akhir" value="' + end + '" />' +
            // '<input type="hidden" name="filer1" value="' + filer1 + '" />' +
            '</form>');
            $('body').append(form);
        form.submit();
    }
</script>