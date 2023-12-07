<?php
include("../templates/koneksi.php");
session_admin_tf_ns();
// include("proses/ceklogin.php");
// include("proses/upload-file.php");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Tiket TF</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <!-- Tempusdominus Bbootstrap 4 -->
    <link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="../plugins/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="../plugins/summernote/summernote-bs4.css">

    <!-- jQuery -->
    <script src="../plugins/jquery/jquery.min.js"></script>
  
    <!-- flatpickr -->
    <link rel="stylesheet" href="../plugins//flatpickr/flatpickr.min.css">
    <script src="../plugins//flatpickr/flatpickr.js"></script>

    <!-- day picker -->
    <script src="../plugins/daypicker/bootstrap-datepicker.js"></script>
    <link href="../plugins/daypicker/bootstrap-datepicker.css" rel="stylesheet"/>

</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-user mr-2"></i> <?= $_SESSION['username'] ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <!-- <a href="?page=edit-profil" class="dropdown-item">
                            <i class="fas fa-file mr-2"></i> Edit Profil
                        </a> -->
                        <div class="dropdown-divider"></div>
                        <a href="../proses/logout.php?action=admin-ns" class="dropdown-item">
                            <i class="fas fa-copy mr-2"></i> Keluar/Logout
                        </a>
                    </div>
                </li>
            </ul>
        </nav>

        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="#" class="brand-link">
                <img src="../dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                    <span class="brand-text font-weight-light">Admin Tiket TF</span>
            </a>
            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column text-sm" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="?page=dashboard" class="nav-link" id="dashboard">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>
                                    Dashboard
                                </p>
                            </a>
                        </li>

                        <li class="nav-header"></li>
                        <li class="nav-item has-treeview" id="akunH">
                            <a href="#" class="nav-link" id="akun">
                                <i class="nav-icon fas fa-users"></i>
                                <p>
                                    Akun
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="?page=akun&action=tambah" class="nav-link" id="akuntambah">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Tambah</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="?page=akun&action=kelola" class="nav-link" id="akunkelola">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Kelola</p>
                                    </a>
                                </li>
                            </ul>
                        </li>


                        <li class="nav-header">TIKET MASUK</li>
                        <li class="nav-item">
                            <a href="?page=sync_tiket_masuk" class="nav-link" id="sync_tiket_masuk">
                                <i class="nav-icon fa fa-sync-alt"></i>
                                <p>
                                    SYNC Tiket Masuk
                                </p>
                            </a>
                        </li>

                        <li class="nav-header">LAPORAN TIKET MASUK</li>
                        <li class="nav-item">
                            <a href="?page=lap_tiket_masuk" class="nav-link" id="lap_tiket_masuk">
                                <i class="nav-icon fa fa-file-invoice"></i>
                                <p>
                                    Laporan Tiket Masuk
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="?page=lap_tiket_masuk_pershift" class="nav-link" id="lap_tiket_masuk_pershift">
                                <i class="nav-icon fa fa-file-invoice"></i>
                                <p>
                                    Laporan Shift
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="?page=lap_tiket_masuk_tour_guide" class="nav-link" id="lap_tiket_masuk_tour_guide">
                                <i class="nav-icon fa fa-file-invoice"></i>
                                <p>
                                    Laporan Tour Guide
                                </p>
                            </a>
                        </li>
                        <!-- <li class="nav-item">
                            <a href="?page=lap_tiket_masuk_gabungan" class="nav-link" id="lap_tiket_masuk_gabungan">
                                <i class="nav-icon fa fa-file-invoice"></i>
                                <p>
                                    Laporan Gabungan
                                </p>
                            </a>
                        </li> -->
                        <!-- <li class="nav-item has-treeview" id="akunH">
                            <a href="#" class="nav-link" id="akun">
                                <i class="nav-icon fas fa-file-invoice"></i>
                                <p>
                                    Laporan Pengunjung
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="?page=lap_tiket_masuk&action=harian" class="nav-link" id="lap_tiket_masukharian">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Lap Perjam Harian</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="?page=akun&action=kelola" class="nav-link" id="akunkelola">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Kelola</p>
                                    </a>
                                </li>
                            </ul>
                        </li> -->

                    </ul>
                </li>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
    <?php
        $page   = @$_GET['page'];
        $action = @$_GET['action'];
        $action2 = @$_GET['action2'];
        if ($page == "dashboard") {
            if ($action == "") {
                // include "view/pengembangan.php";
                include "../admin-tf-ns/view/dashboard/index.php";
            }
        }else if ($page == "sync_tiket_masuk") {
            if ($action == "") {
                // include "view/pengembangan.php";
                include "../admin-tf-ns/view/tiket_masuk/sync_tiket_masuk.php";
            }
        }else if ($page == "lap_tiket_masuk") {
            if ($action == "") {
                // include "view/pengembangan.php";
                include "../admin-tf-ns/view/laporan/tiket_masuk/lap_tiket_masuk.php";
            }
        }else if ($page == "lap_tiket_masuk_pershift") {
            if ($action == "") {
                // include "view/pengembangan.php";
                include "../admin-tf-ns/view/laporan/tiket_masuk/lap_tiket_masuk_pershift.php";
            }
        }else if ($page == "lap_tiket_masuk_gabungan") {
            if ($action == "") {
                // include "view/pengembangan.php";
                include "../admin-tf-ns/view/laporan/tiket_masuk/lap_tiket_masuk_gabungan.php";
            }
        }else if ($page == "lap_tiket_masuk_tour_guide") {
            if ($action == "") {
                // include "view/pengembangan.php";
                include "../admin-tf-ns/view/laporan/tiket_masuk/lap_tiket_masuk_tour_guide.php";
            }elseif($action == "detail"){
                include "../admin-tf-ns/view/laporan/tiket_masuk/lap_tiket_masuk_tour_guide_detail.php";
            }
        } elseif ($page == "akun") {
            if ($action == "kelola") {
                include "../admin-tf-ns/view/akun/index.php";
            } elseif ($action == "tambah") {
                include "../admin-tf-ns/view/akun/tambah.php";
            } elseif ($action == "edit") {
                include "../admin-tf-ns/view/akun/edit.php";
            } 
        } elseif ($page == "lap_tiket_masuk") {
            if ($action == "harian") {
                include "../admin-tf-ns/view/laporan/tiket_masuk/harian.php";
            } elseif ($action == "tambah") {
                include "../admin-tf-ns/view/akun/tambah.php";
            } elseif ($action == "edit") {
                include "../admin-tf-ns/view/akun/edit.php";
            } 
        } else {
            include "../admin-tf-ns/view/dashboard/index.php";
        }
      
        if(!empty($page)){
            echo "<script>if(document.getElementById('".$page."')){document.getElementById('".$page."').classList.add('active')}</script>";
            echo "<script>$(document).ready(function(){document.getElementById('".$page."').scrollIntoView();});</script>";
            echo "<script>if(document.getElementById('".$page.'H'."')){document.getElementById('".$page.'H'."').classList.add('menu-open')}</script>";
            echo "<script>$(document).ready(function(){document.getElementById('".$page.'H'."').scrollIntoView();});</script>";
            if(!empty($action)){
                echo "<script>if(document.getElementById('".$page.$action."')){document.getElementById('".$page.$action."').classList.add('active')}</script>";
            }
        } else {
            echo "<script>document.getElementById('dashboard').classList.add('active')</script>";
        }
    ?>
    </div>


    <footer class="main-footer">
      <strong>Copyright &copy; 2014-2019 <a href="http://adminlte.io">AdminLTE.io</a>.</strong>
      All rights reserved. Developed by <a href="http://andiglobalsoft.com">Andiglobalsoft</a>
      <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 3.0.5
      </div>
    </footer>


    <aside class="control-sidebar control-sidebar-dark">

    </aside>

  </div>


  <!-- jQuery UI 1.11.4 -->
  <script src="../plugins/jquery-ui/jquery-ui.min.js"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
    $.widget.bridge('uibutton', $.ui.button)
  </script>
  <!-- Bootstrap 4 -->
  <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- ChartJS -->
  <script src="../plugins/chart.js/Chart.min.js"></script>
  <!-- Sparkline -->
  <script src="../plugins/sparklines/sparkline.js"></script>
  <!-- JQVMap -->
  <script src="../plugins/jqvmap/jquery.vmap.min.js"></script>
  <script src="../plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
  <!-- jQuery Knob Chart -->
  <script src="../plugins/jquery-knob/jquery.knob.min.js"></script>
  <!-- daterangepicker -->
  <script src="../plugins/moment/moment.min.js"></script>
  <script src="../plugins/daterangepicker/daterangepicker.js"></script>
  <!-- Tempusdominus Bootstrap 4 -->
  <script src="../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
  <!-- Summernote -->
  <script src="../plugins/summernote/summernote-bs4.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="../plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../dist/js/adminlte.js"></script>
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <script src="../dist/js/pages/dashboard.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="../dist/js/demo.js"></script>
  <!-- DataTables -->
  <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

  <script type="text/javascript">
    $(".datetime_picker").flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        defaultDate: "12:00",
        time_24hr: true
    });
  </script>
  
  <script>
    $(function () {
      $('#example').DataTable({
        "paging": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        // "responsive": true,
      });
    });
  </script>

  <!-- tooltips -->
  <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
    });
  </script>
  
  <!-- timepicker -->
  <script src="../plugins/timepicker/timepicker.js"></script>

</body>
</html>