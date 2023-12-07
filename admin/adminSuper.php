<?php
include("templates/koneksi.php");
sessionLogin();
// include("proses/ceklogin.php");
// include("proses/upload-file.php");
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Super Admin</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.css">
  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

  <!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>

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
        <!-- <li class="nav-item">
          <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
            <i class="fas fa-th-large"></i>
          </a>
        </li> -->
        <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="far fa-user mr-2"></i> <?php echo "nama admin"; ?>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <a href="?page=editProfil" class="dropdown-item">
              <i class="fas fa-file mr-2"></i> Edit Profil
            </a>
            <div class="dropdown-divider"></div>
            <a href="proses/logout.php" class="dropdown-item">
              <i class="fas fa-copy mr-2"></i> Keluar/Logout
            </a>
          </div>
        </li>
      </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <a href="#" class="brand-link">
        <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
        style="opacity: .8">
        <span class="brand-text font-weight-light">Super Admin</span>
      </a>
      <div class="sidebar">
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item">
              <a href="?page=dashboard" class="nav-link" id="dashboard">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>
                  Dashboard
                </p>
              </a>
            </li>
            <li class="nav-item has-treeview" id="adminH">
              <a href="#" class="nav-link" id="admin">
                <i class="nav-icon fas fa-users"></i>
                <p>
                  Admin
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="?page=admin&action=tambah" class="nav-link" id="admintambah">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Tambah</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="?page=admin&action=kelola" class="nav-link" id="adminkelola">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Kelola</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item has-treeview" id="tiketH">
              <a href="#" class="nav-link" id="tiket">
                <i class="nav-icon fas fa-file-invoice"></i>
                <p>
                  Tiket
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="?page=tiket&action=tambah" class="nav-link" id="tikettambah">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Tambah</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="?page=tiket&action=kelola" class="nav-link" id="tiketkelola">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Kelola</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item has-treeview" id="liburH">
              <a href="#" class="nav-link" id="libur">
                <i class="nav-icon fas fa-file"></i>
                <p>
                  Hari Libur
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="?page=libur&action=tambah" class="nav-link" id="liburtambah">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Tambah</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="?page=libur&action=kelola" class="nav-link" id="liburkelola">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Kelola</p>
                  </a>
                </li>
              </ul>
            </li>
            <!-- <li class="nav-item">
              <a href="?page=laporan" class="nav-link" id="laporan">
                <i class="nav-icon fas fa-file"></i>
                <p>
                  Laporan
                </p>
              </a>
            </li> -->
          </ul>
        </nav>

      </div>
    </aside>

    <div class="content-wrapper">
      <?php
      $page   = @$_GET['page'];
      $action = @$_GET['action'];
      if ($page == "dashboard") {
        if ($action == "") {
          include "view/pengembangan.php";
        }
      } elseif ($page == "admin") {
        if ($action == "kelola") {
          include "view/admin/admin_kelola.php";
        } elseif ($action == "tambah") {
          include "view/admin/admin_tambah.php";
        } elseif ($action == "edit") {
          include "view/admin/admin_edit.php";
        } elseif ($action == "hapus") {
          include "view/admin/admin_hapus.php";
        } 
      } elseif ($page == "tiket") {
        if ($action == "kelola") {
          include "view/tiket/tiket_kelola.php";
        } elseif ($action == "tambah") {
          include "view/tiket/tiket_tambah.php";
        } elseif ($action == "edit") {
          include "view/tiket/tiket_edit.php";
        } elseif ($action == "hapus") {
          include "view/tiket/tiket_hapus.php";
        } 
      } elseif ($page == "libur") {
        if ($action == "kelola") {
          include "view/libur/libur_kelola.php";
        } elseif ($action == "tambah") {
          include "view/libur/libur_tambah.php";
        } elseif ($action == "edit") {
          include "view/libur/libur_edit.php";
        } elseif ($action == "hapus") {
          include "view/libur/libur_hapus.php";
        } 
      } elseif ($page == "editProfil") {
        if ($action == "") {
          include "view/profil_edit.php";
        }
      } else {
        // include "view/agen_dashboard.php";
      }
      
      if(!empty($page)){
        echo "<script>if(document.getElementById('".$page."')){document.getElementById('".$page."').classList.add('active')}</script>";
        echo "<script>if(document.getElementById('".$page.'H'."')){document.getElementById('".$page.'H'."').classList.add('menu-open')}</script>";
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
      All rights reserved.
      <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 3.0.5
      </div>
    </footer>


    <aside class="control-sidebar control-sidebar-dark">

    </aside>

  </div>


  <!-- jQuery UI 1.11.4 -->
  <script src="plugins/jquery-ui/jquery-ui.min.js"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
    $.widget.bridge('uibutton', $.ui.button)
  </script>
  <!-- Bootstrap 4 -->
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- ChartJS -->
  <script src="plugins/chart.js/Chart.min.js"></script>
  <!-- Sparkline -->
  <script src="plugins/sparklines/sparkline.js"></script>
  <!-- JQVMap -->
  <script src="plugins/jqvmap/jquery.vmap.min.js"></script>
  <script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
  <!-- jQuery Knob Chart -->
  <script src="plugins/jquery-knob/jquery.knob.min.js"></script>
  <!-- daterangepicker -->
  <script src="plugins/moment/moment.min.js"></script>
  <script src="plugins/daterangepicker/daterangepicker.js"></script>
  <!-- Tempusdominus Bootstrap 4 -->
  <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
  <!-- Summernote -->
  <script src="plugins/summernote/summernote-bs4.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="dist/js/adminlte.js"></script>
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <script src="dist/js/pages/dashboard.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="dist/js/demo.js"></script>
  <!-- DataTables -->
  <script src="plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
  
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  
  <script type="text/javascript">
    $(".datetime_picker").flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        defaultDate: "12:00"
    });
  </script>
  
  <script>
    $(function () {
      $('#example').DataTable({
        "paging": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
      });
    });
  </script>
  <!-- tooltips -->
  <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
    });
  </script>
</body>
</html>
