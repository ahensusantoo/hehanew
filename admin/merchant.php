<?php 
include("templates/koneksi.php");
//sessionLogin();
sessionLoginMerchantEmployee();
// include("proses/ceklogin.php");
// include("proses/upload-file.php");

$sess_kd_merchant =$_SESSION['kd_merchant'];

$query_nama_title = "	SELECT *
                        FROM merchant
                        WHERE id_merchant = '$sess_kd_merchant'
                            AND status_aktif_merchant = 'Y' ";
$nama_title = $db->query($query_nama_title)->fetch_assoc();


?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>
        <?= $nama_title['nama_merchant'] ?>
  </title>
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
  <script src="plugins/mask/jquery.mask.min.js"></script>

  
  <!-- flatpickr -->
  <link rel="stylesheet" href="plugins/flatpickr/flatpickr.min.css">

  <!-- day picker -->
  <script src="plugins/daypicker/bootstrap-datepicker.js"></script>
  <link href="plugins/daypicker/bootstrap-datepicker.css" rel="stylesheet"/>
  
  <!-- select2 -->
  <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">


  <!-- alert bootbox -->
    <script src="plugins/bootbox/dist/bootbox.all.min.js"></script>
    <script src="plugins/bootbox/dist/bootbox.locales.min.js"></script>
    <script src="plugins/bootbox/dist/bootbox.min.js"></script>


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
          <?php if($_SESSION['level_employee'] == "0"){
              echo "SuperAdmin ";
          }else if($_SESSION['level_employee'] == "1"){
              echo "Admin ";
          }else{
              echo "Kasir " ;
          }
          
          ?>
            <i class="far fa-user mr-2"></i> <?php echo $_SESSION['nama_employee']; ?>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <div class="dropdown-divider"></div>
            <a href="proses/logout.php" onclick="return confirm('Anda yakin Logout?')" class="dropdown-item">
              <i class="fas fa-sign-out-alt"></i> Keluar/Logout
            </a>
          </div>
        </li>
      </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <a href="#" class="brand-link">
        <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
        style="opacity: .8">
        <span class="brand-text font-weight-light">
			<?= $nama_title['nama_merchant'] ?> 
	    </span>
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

            <!-- Bagian Kasir/ Mercahnt Employee -->
            <li class="nav-item has-treeview" id="merchant_employeeH">
              <a href="#" class="nav-link" id="merchant_employee">
                <i class="nav-icon fas fa-users"></i>
                <p>
                  Kasir
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="?page=merchant_employee&action=tambah" class="nav-link" id="merchant_employeetambah">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Tambah</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="?page=merchant_employee&action=kelola" class="nav-link" id="merchant_employeekelola">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Kelola</p>
                  </a>
                </li>
              </ul>
            </li>

            <!-- Bagian Mercahnt Kategory -->
            <li class="nav-item has-treeview" id="kategory_productH">
              <a href="#" class="nav-link" id="kategory_product">
                <i class="nav-icon fas fa-tags"></i>
                <p>
                  Kategory Product
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="?page=kategory_product&action=tambah" class="nav-link" id="kategory_producttambah">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Tambah</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="?page=kategory_product&action=kelola" class="nav-link" id="kategory_productkelola">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Kelola</p>
                  </a>
                </li>
              </ul>
            </li>

            <!-- Bagian Product Merchant -->
            <li class="nav-item has-treeview" id="productH">
              <a href="#" class="nav-link" id="product">
                <i class="nav-icon fas fa-box-open"></i>
                <p>
                  Product
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="?page=product&action=tambah" class="nav-link" id="producttambah">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Tambah</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="?page=product&action=kelola" class="nav-link" id="productkelola">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Kelola</p>
                  </a>
                </li>
              </ul>
            </li>

            <!-- mutasi stok -->
            <li class="nav-item has-treeview" id="mutasi_stokH">
              <a href="#" class="nav-link" id="mutasi_stok">
                <i class="nav-icon fas fa-truck"></i>
                <p>
                  Mutasi Stock
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="?page=mutasi_stok&action=masuk" class="nav-link" id="mutasi_stokmasuk">
                    <i class="far fa-circle nav-icon"></i>
                    <p>mutasi Masuk</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="?page=mutasi_stok&action=keluar" class="nav-link" id="mutasi_stokkeluar">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Mutasi Keluar</p>
                  </a>
                </li>
              </ul>
            </li>
            
         <li class="header text-white">Laporan</li>
         
            <?php if($_SESSION['level_employee']=="1") { ?>
                
                <li class="nav-item has-treeview" id="laporan_bagi_hasilH">
                    <a href="#" class="nav-link" id="laporan_bagi_hasil">
                        <i class="nav-icon fas fa-file"></i>
                        <p> Laporan Bagi Hasil
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                          <a href="?page=laporan_bagi_hasil&action=harian" class="nav-link" id="laporan_bagi_hasilharian">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Harian</p>
                          </a>
                        </li>
                        <li class="nav-item">
                          <a href="?page=laporan_bagi_hasil&action=bulanan" class="nav-link" id="laporan_bagi_hasilbulanan">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Bulanan</p>
                          </a>
                        </li>
                    </ul>
                </li>
            <?php } ?>

            <!-- Bagian Product Merchant -->
            <li class="nav-item has-treeview" id="laporan_produkH">
              <a href="#" class="nav-link" id="laporan_produk">
                <i class="nav-icon fas fa-file"></i>
                <p>
                  Laporan Product
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="?page=laporan_produk&action=terlaris" class="nav-link" id="laporan_produkterlaris">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Laporan Product Terlaris</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="?page=laporan_produk&action=terbaru" class="nav-link" id="laporan_produkterbaru">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Laporan Product Terbaru</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="?page=laporan_produk&action=penjualan_harian_perproduk" class="nav-link" id="laporan_produkpenjualan_harian_perproduk">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Penjualan Harian Perproduk</p>
                  </a>
                </li>
              </ul>
            </li>

            <!-- Bagian Laporan -->
            <li class="nav-item has-treeview" id="laporan_transaksiH">
              <a href="#" class="nav-link" id="laporan_transaksi">
                <i class="nav-icon fas fa-file"></i>
                <p>
                  Laporan Transaksi
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="?page=laporan_transaksi&action=kasir" class="nav-link" id="laporan_transaksikasir">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Penjualan Harian Perkasir</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="?page=laporan_transaksi&action=perbulan" class="nav-link" id="laporan_transaksiperbulan">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Laporan Grafik Perbulan</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="?page=laporan_transaksi&action=harian_shift" class="nav-link" id="laporan_transaksiharian_shift">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Penjualan Harian Pershift</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="?page=laporan_transaksi&action=harian_shift_gabungan" class="nav-link" id="laporan_transaksiharian_shift_gabungan">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Penjualan Harian(Gab Shift)</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="?page=laporan_transaksi&action=perproduk_bulanan" class="nav-link" id="laporan_transaksiperproduk_bulanan">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Penjualan Produk Bulanan</p>
                  </a>
                </li> 
              </ul>
            </li>

            <!-- Bagian Laporan -->
            <li class="nav-item has-treeview" id="laporan_stockH">
              <a href="#" class="nav-link" id="laporan_stock">
                <i class="nav-icon fas fa-file"></i>
                <p>
                  Laporan Stock
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="?page=laporan_stock&action=stock" class="nav-link" id="laporan_stockstock">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Laporan Stock</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="?page=laporan_stock&action=mutasi" class="nav-link" id="laporan_stockmutasi">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Laporan Mutasi</p>
                  </a>
                </li>
              </ul>
            </li>
            
            <?php if($_SESSION['level_employee']=="0") { ?>
                <li class="header text-white">Transaksi</li>
    			    <li class="nav-item has-treeview" id="transaksi_merchantH">
                      <a href="#" class="nav-link" id="laporan_stock">
                    <i class="nav-icon fas fa-shopping-cart"></i>
                    <p>
                      Transaksi
                      <i class="right fas fa-angle-left"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="?page=transaksi_merchant&action=cancel" class="nav-link" id="transaksi_merchantcancel">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Cancel Transaksi</p>
                      </a>
                    </li>
                  </ul>
                </li>
                
                
            <?php } ?>

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
          include "view/dashboard/pengembangan_merchant_employee.php";
        }
      } elseif ($page == "merchant_employee") {
        if ($action == "kelola") {
          include "view/merchant_employee/merchant_employee_kelola.php";
        } elseif ($action == "tambah") {
          include "view/merchant_employee/merchant_employee_tambah.php";
        } elseif ($action == "edit") {
          include "view/merchant_employee/merchant_employee_edit.php";
        } elseif ($action == "hapus") {
          include "view/libur/libur_hapus.php";
        }
      } elseif ($page == "kategory_product") {
        if ($action == "kelola") {
          include "view/merchant_kategory_product/kategory_product_kelola.php";
        } elseif ($action == "tambah") {
          include "view/merchant_kategory_product/kategory_product_tambah.php";
        } elseif ($action == "edit") {
          include "view/merchant_kategory_product/kategory_product_edit.php";
        } elseif ($action == "hapus") {
          include "view/merchant_kategory_product/kategory_product_hapus.php";
        }
      } elseif ($page == "product") {
        if ($action == "kelola") {
          include "view/product/product_kelola.php";
        } elseif ($action == "tambah") {
          include "view/product/product_tambah.php";
        } elseif ($action == "edit") {
          include "view/product/product_edit.php";
        } elseif ($action == "hapus") {
          include "view/product/product_hapus.php";
        } 
      } elseif ($page == "mutasi_stok") {
        if ($action == "masuk") {
          include "view/mutasi_stok/mutasi_masuk.php";
        } elseif ($action == "keluar") {
          include "view/mutasi_stok/mutasi_keluar.php";
        } 
      }elseif ($page == "laporan_produk") {
        if ($action == "terlaris") {
          include "view/laporan_produk/laporan_produk_terlaris.php";
        } elseif ($action == "terbaru") {
          include "view/laporan_produk/laporan_produk_terbaru.php";
        }elseif ($action == "penjualan_harian_perproduk") {
          include "view/laporan_produk/laporan_penjualan_harian_perproduk.php";
        } 
      }elseif ($page == "laporan_transaksi") {
        if ($action == "kasir") {
          include "view/laporan_transaksi/laporan_transaksi_kasir.php";
        } elseif ($action == "perbulan") {
          include "view/laporan_transaksi/laporan_transaksi_perbulan.php";
        }elseif ($action == "harian_shift") {
          include "view/laporan_transaksi/laporan_transaksi_harian_pershift.php";
        }elseif ($action == "harian_shift_gabungan") {
          include "view/laporan_transaksi/laporan_transaksi_harian_gabungan.php";
        } elseif ($action == "perproduk_bulanan") {
          include "view/laporan_transaksi/laporan_transaksi_produk_bulanan.php";
        } 
    //   }
    //   elseif ($action == "stock") {
    //       include "view/laporan_stock/laporan_stock.php"; 
    //   }
      } elseif ($page == "laporan_stock") {
        if ($action == "stock") {
          include "view/laporan_stock/laporan_stock.php";
        } elseif ($action == "mutasi") {
          include "view/laporan_stock/laporan_mutasi.php";
        } 
      }elseif ($page == "laporan_bagi_hasil") {
        if ($action == "harian") {
          include "view/laporan_bagi_hasil_merchant/laporan_bagi_hasil_harian.php";
        } elseif ($action == "bulanan") {
          include "view/laporan_bagi_hasil_merchant/laporan_bagi_hasil_bulanan.php";
        } 
      }elseif ($page == "transaksi_merchant") {
        if ($action == "cancel") {
          include "view/transaksi_merchant/cancel_transaksi.php";
        } elseif ($action == "edit") {
          include "view/transaksi_merchant/transaksi_merchant_edit.php";
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
  
  <!-- timepicker -->
  <script src="plugins/timepicker/timepicker.js"></script>

  <!-- DataTables -->
  <script src="plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
  <script src="plugins/select2/js/select2.full.min.js"></script>

  <script>
    $(function () {
      $('.select2').select2()
      $('.select2bs4').select2({
        theme: 'bootstrap4'
      })

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
