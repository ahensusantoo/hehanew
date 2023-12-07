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
  <title>Photobooth Admin</title>
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

  <!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>
  
  <!-- flatpickr -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

  <!-- day picker -->
  <script src="plugins/daypicker/bootstrap-datepicker.js"></script>
  <link href="plugins/daypicker/bootstrap-datepicker.css" rel="stylesheet"/>

  <!-- month-range-picker -->
  <!-- <link rel="stylesheet" href="plugins/month-range/responsive-month-range-picker.css"> -->

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
            <i class="far fa-user mr-2"></i> <?= $_SESSION['username'] ?>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <a href="?page=edit-profil" class="dropdown-item">
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
        <span class="brand-text font-weight-light">Photobooth Admin</span>
      </a>
      <div class="sidebar">
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

            <li class="nav-header">PHOTOBOOTH</li>
            <li class="nav-item has-treeview" id="photoboothH">
              <a href="#" class="nav-link" id="photobooth">
                <i class="nav-icon fas fa-camera-retro"></i>
                <p>
                  Spot Photobooth
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="?page=photobooth&action=tambah" class="nav-link" id="photoboothtambah">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Tambah</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="?page=photobooth&action=kelola" class="nav-link" id="photoboothkelola">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Kelola</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item has-treeview" id="adminphotoboothH">
              <a href="#" class="nav-link" id="adminphotobooth">
                <i class="nav-icon fas fa-users"></i>
                <p>
                  Kasir Photobooth
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="?page=adminphotobooth&action=tambah" class="nav-link" id="adminphotoboothtambah">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Tambah</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="?page=adminphotobooth&action=kelola" class="nav-link" id="adminphotoboothkelola">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Kelola</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item has-treeview" id="voucherphotoboothH">
              <a href="#" class="nav-link" id="voucherphotobooth">
                <i class="nav-icon fas fa-tags"></i>
                <p>
                  Voucher Photobooth
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="?page=voucherphotobooth&action=tambah" class="nav-link" id="voucherphotoboothtambah">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Tambah</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="?page=voucherphotobooth&action=kelola" class="nav-link" id="voucherphotoboothkelola">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Kelola</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item">
              <a href="?page=transaksi_photobooth&action=kelola" class="nav-link" id="transaksi_photobooth">
                <i class="nav-icon fas fa-file"></i>
                <p>
                  Transaksi Photobooth
                </p>
              </a>
            </li>
            <li class="nav-item has-treeview" id="file_photoboothH">
              <a href="#" class="nav-link" id="file_photobooth">
                <i class="nav-icon fas fa-camera-retro"></i>
                <p>
                  File Photobooth
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="?page=file_photobooth&action=tambah" class="nav-link" id="file_photoboothtambah">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Tambah</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="?page=file_photobooth&action=kelola" class="nav-link" id="file_photoboothkelola">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Kelola</p>
                  </a>
                </li>
              </ul>
            </li>
            <!-- <li class="nav-item has-treeview" id="file_voucherphotoboothH">
              <a href="#" class="nav-link" id="file_voucherphotobooth">
                <i class="nav-icon fas fa-tags"></i>
                <p>
                  File Voucher Photobooth
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="?page=file_voucherphotobooth&action=tambah" class="nav-link" id="file_voucherphotoboothtambah">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Tambah</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="?page=file_voucherphotobooth&action=kelola" class="nav-link" id="file_voucherphotoboothkelola">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Kelola</p>
                  </a>
                </li>
              </ul>
            </li> -->
            <li class="nav-item">
              <a href="?page=file_transaksi_photobooth&action=kelola" class="nav-link" id="file_transaksi_photobooth">
                <i class="nav-icon fas fa-file"></i>
                <p>
                  File Transaksi Photobooth
                </p>
              </a>
            </li>

            <li class="nav-header">LAPORAN PHOTOBOOTH</li>
            <li class="nav-item">
              <a href="?page=lap-photoboothtiket" class="nav-link" id="lap-photoboothtiket">
                <i class="nav-icon fas fa-file-invoice"></i>
                <p>
                  Laporan Photobooth
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="?page=lap-photoboothshift" class="nav-link" id="lap-photoboothshift">
                <i class="nav-icon fas fa-file-invoice"></i>
                <p>
                  Laporan Shift
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="?page=lap-photoboothgabung" class="nav-link" id="lap-photoboothgabung">
                <i class="nav-icon fas fa-file-invoice"></i>
                <p>
                  Laporan Gabung
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="?page=lap-filephotobooth" class="nav-link" id="lap-filephotobooth">
                <i class="nav-icon fas fa-file-invoice"></i>
                <p>
                  Laporan File Photobooth
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="?page=lap-filephotoboothshift" class="nav-link" id="lap-filephotoboothshift">
                <i class="nav-icon fas fa-file-invoice"></i>
                <p>
                  Laporan File Shift
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="?page=lap-filephotoboothgabung" class="nav-link" id="lap-filephotoboothgabung">
                <i class="nav-icon fas fa-file-invoice"></i>
                <p>
                  Laporan File Gabung
                </p>
              </a>
            </li>
            <!-- <li class="nav-item">
              <a href="?page=lap-photoboothperjam" class="nav-link" id="lap-photoboothperjam">
                <i class="nav-icon fas fa-file-invoice"></i>
                <p>
                  Pengunjung Perjam
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
          // include "view/pengembangan.php";
          include "view/dashboard/dashboard.php";
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
      } elseif ($page == "pembayaran") {
        if ($action == "kelola") {
          include "view/pembayaran/pembayaran_kelola.php";
        } elseif ($action == "tambah") {
          include "view/pembayaran/pembayaran_tambah.php";
        } elseif ($action == "edit") {
          include "view/pembayaran/pembayaran_edit.php";
        } elseif ($action == "hapus") {
          include "view/pembayaran/pembayaran_hapus.php";
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
      } elseif ($page == "edit-profil") {
        if ($action == "") {
          include "view/profil_edit.php";
        }
      } elseif ($page == "transaksi") {
        if ($action == "kelola") {
          include "view/transaksi_superadmin/transaksi_kelola.php";
        } elseif ($action == "detail") {
          include "view/transaksi_superadmin/transaksi_detail.php";
        } elseif ($action == "revisi") {
          include "view/transaksi_superadmin/transaksi_revisi.php";
        }
      } elseif ($page == "transaksi_photobooth") {
        if ($action == "kelola") {
          include "view/transaksi_superadmin_photobooth/transaksi_kelola.php";
        } elseif ($action == "detail") {
          include "view/transaksi_superadmin_photobooth/transaksi_detail.php";
        } elseif ($action == "revisi") {
          include "view/transaksi_superadmin_photobooth/transaksi_revisi.php";
        }
      } elseif ($page == "shift") {
        if ($action == "kelola") {
          include "view/shift/shift_kelola.php";
        } elseif ($action == "tambah") {
          include "view/shift/shift_tambah.php";
        } elseif ($action == "edit") {
          include "view/shift/shift_edit.php";
        } elseif ($action == "hapus") {
          include "view/shift/shift_hapus.php";
        } 
      } elseif ($page == "photobooth") {
        if ($action == "kelola") {
          include "view/photobooth/photobooth_kelola.php";
        } elseif ($action == "tambah") {
          include "view/photobooth/photobooth_tambah.php";
        } elseif ($action == "edit") {
          include "view/photobooth/photobooth_edit.php";
        } elseif ($action == "hapus") {
          include "view/photobooth/photobooth_hapus.php";
        } 
      } elseif ($page == "lap-tiket") {
        include "view/laporan/lap_tiket.php";
      } elseif ($page == "lap-shift") {
        include "view/laporan/lap_shift.php";
      } elseif ($page == "lap-gabung") {
        include "view/laporan/lap_gabung.php";
      } elseif ($page == "lap-perjam") {
        include "view/laporan/lap-perjam.php";
      } elseif ($page == "vouchertiket") {
        if ($action == "kelola") {
          include "view/voucher_tiket/voucher_tiket_kelola.php";
        } elseif ($action == "tambah") {
          include "view/voucher_tiket/voucher_tiket_tambah.php";
        } elseif ($action == "edit") {
          include "view/voucher_tiket/voucher_tiket_edit.php";
        } elseif ($action == "hapus") {
          include "view/voucher_tiket/voucher_tiket_hapus.php";
        } 
      } elseif ($page == "voucherphotobooth") {
        if ($action == "kelola") {
          include "view/voucher_photobooth/voucher_photobooth_kelola.php";
        } elseif ($action == "tambah") {
          include "view/voucher_photobooth/voucher_photobooth_tambah.php";
        } elseif ($action == "edit") {
          include "view/voucher_photobooth/voucher_photobooth_edit.php";
        } elseif ($action == "hapus") {
          include "view/voucher_photobooth/voucher_photobooth_hapus.php";
        } 
      } elseif ($page == "merchant") {
        if ($action == "kelola") {
          include "view/merchant/merchant_kelola.php";
        } elseif ($action == "tambah") {
          include "view/merchant/merchant_tambah.php";
        } elseif ($action == "edit") {
          include "view/merchant/merchant_edit.php";
        } elseif ($action == "hapus") {
          include "view/merchant/merchant_hapus.php";
        } elseif ($action == "tambah-admin") {
          include "view/merchant/merchant_admin_tambah.php";
        } elseif ($action == "edit-admin") {
          include "view/merchant/merchant_admin_edit.php";
        }
      } elseif ($page == "merchantadmin") {
        if ($action == "kelola") {
          include "view/merchant_admin/merchant_admin_kelola.php";
        } elseif ($action == "tambah") {
          include "view/merchant_admin/merchant_admin_tambah.php";
        } elseif ($action == "edit") {
          include "view/merchant_admin/merchant_admin_edit.php";
        } elseif ($action == "hapus") {
          include "view/merchant_admin/merchant_admin_hapus.php";
        } 
      } elseif ($page == "vouchermerchant") {
        if ($action == "kelola") {
          include "view/voucher_merchant/voucher_merchant_kelola.php";
        } elseif ($action == "tambah") {
          include "view/voucher_merchant/voucher_merchant_tambah.php";
        } elseif ($action == "edit") {
          include "view/voucher_merchant/voucher_merchant_edit.php";
        } elseif ($action == "hapus") {
          include "view/voucher_merchant/voucher_merchant_hapus.php";
        } 
      } elseif ($page == "lap-photoboothtiket") {
        include "view/laporan/lap_photoboothtiket.php";
      } elseif ($page == "lap-photoboothshift") {
        include "view/laporan/lap_photoboothshift.php";
      } elseif ($page == "lap-photoboothgabung") {
        include "view/laporan/lap_photoboothgabung.php";
      } elseif ($page == "lap-photoboothperjam") {
        include "view/laporan/lap-photoboothperjam.php";
      } elseif ($page == "lap-merchant") {
        include "view/laporan/merchant.php";
      } elseif ($page == "lap-merchantgabung") {
        include "view/laporan/lap_merchantgabung.php";
      } elseif ($page == "admintiket") {
        if ($action == "kelola") {
          include "view/admin/admintiket_kelola.php";
        } elseif ($action == "tambah") {
          include "view/admin/admintiket_tambah.php";
        } elseif ($action == "edit") {
          include "view/admin/admintiket_edit.php";
        } elseif ($action == "hapus") {
          include "view/admin/admintiket_hapus.php";
        } 
      } elseif ($page == "adminphotobooth") {
        if ($action == "kelola") {
          include "view/admin/adminphotobooth_kelola.php";
        } elseif ($action == "tambah") {
          include "view/admin/adminphotobooth_tambah.php";
        } elseif ($action == "edit") {
          include "view/admin/adminphotobooth_edit.php";
        } elseif ($action == "hapus") {
          include "view/admin/adminphotobooth_hapus.php";
        } 
      } elseif ($page == "file_photobooth") {
        if ($action == "kelola") {
          include "view/file_photobooth/file_photobooth_kelola.php";
        } elseif ($action == "tambah") {
          include "view/file_photobooth/file_photobooth_tambah.php";
        } elseif ($action == "edit") {
          include "view/file_photobooth/file_photobooth_edit.php";
        } elseif ($action == "hapus") {
          include "view/file_photobooth/file_photobooth_hapus.php";
        } 
      } elseif ($page == "file_voucherphotobooth") {
        if ($action == "kelola") {
          include "view/file_voucher_photobooth/file_voucher_photobooth_kelola.php";
        } elseif ($action == "tambah") {
          include "view/file_voucher_photobooth/file_voucher_photobooth_tambah.php";
        } elseif ($action == "edit") {
          include "view/file_voucher_photobooth/file_voucher_photobooth_edit.php";
        } elseif ($action == "hapus") {
          include "view/file_voucher_photobooth/file_voucher_photobooth_hapus.php";
        } 
      } elseif ($page == "file_transaksi_photobooth") {
        if ($action == "kelola") {
          include "view/file_transaksi_superadmin_photobooth/file_transaksi_kelola.php";
        } elseif ($action == "detail") {
          include "view/file_transaksi_superadmin_photobooth/file_transaksi_detail.php";
        } elseif ($action == "revisi") {
          include "view/file_transaksi_superadmin_photobooth/file_transaksi_revisi.php";
        }
      } elseif ($page == "lap-filephotobooth") {
        include "view/laporan/lap_filephotobooth.php";
      } elseif ($page == "lap-filephotoboothshift") {
        include "view/laporan/lap_filephotoboothshift.php";
      } elseif ($page == "lap-filephotoboothgabung") {
        include "view/laporan/lap_filephotoboothgabung.php";
      } elseif ($page == "pengunjung") {
        if ($action == "perjamhari") {
          include "view/laporan_pengunjung/laporan_pengunjung_perjamhari.php";
        } elseif ($action == "perjambulan") {
          include "view/laporan_pengunjung/laporan_pengunjung_perjambulan.php";
        } elseif ($action == "perbulan") {
          include "view/laporan_pengunjung/laporan_pengunjung_perbulan.php";
        }
      } else {
        include "view/dashboard/dashboard.php";
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
      All rights reserved. Developed by <a href="http://andiglobalsoft.com">Andiglobalsoft</a>
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
  <script src="plugins/timepicker/timepicker.js"></script>

  <!-- month-range-picker -->
  <!-- <script src="plugins/month-range/plugins.js"></script> -->
  <!-- <script src="plugins/month-range/responsive-month-range-picker.js"></script> -->

</body>
</html>