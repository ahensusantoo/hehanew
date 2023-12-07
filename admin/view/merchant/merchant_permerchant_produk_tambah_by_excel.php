<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Tambah Product By Excel</h1>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?php if (isset($_SESSION['notifikasi'])) { notifikasi($_SESSION['notifikasi']); } ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-10">
                                Syarat & Ketentuan : 
                                <ol>
                                    <li>Hanya Support File Excel .xls, .xlsx</li>
                                    <li>hanya proses menambahkan item baru</li>
                                    <li>Format penulisan harus sesuai dengan template excel yang telah di sediakan</li>
                                    <li>untuk kode kategori tidak boleh kosong dan harus sesuai dengan kode yang sudah ada sesuai dengan merchant/toko dari masing masing stall</li>
                                    <li>untuk kode Supplier harus sesuai dengan kode yang sudah ada sesuai dengan merchant/toko dari masing masing stall</li>
                                    <li>Jenis stock :</li>
                                        <ul>
                                            <li>1 : Barang bukan stock</li>
                                            <li>2 : Barang stock</li>
                                            note : hanya boleh diisi dengan angka 1 atau 2 saja.
                                        </ul>
                                    <li>Discount hanya bisa dalam bentuk persen</li>
                                    <li>Status Konsi :</li>
                                        <ul>
                                            <li>Y : Barang Titipan</li>
                                            <li>N : Barang Non Titipan</li>
                                            note  : hanya boleh diisi dengan angka Y atau N saja.
                                        </ul>
                                    <li>Status Tampil :</li>
                                        <ul>
                                            <li>Y : Barang Tampilin/Display</li>
                                            <li>N : Barang Di Hide/Sembunyikan</li>
                                            note  : hanya boleh diisi dengan angka Y atau N saja.
                                        </ul>
                                </ol>
                            </div>
                            <div class="col-md-2">
                                <a href="proses/template_import_produk_stall.xlsx" class="btn btn-success mb-4 float-right" title="Download Template" download="">
                                    <i class="fa fa-file-download"> </i> templete Excel
                                </a>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-lg-12">    
                                <form role="form" method="post" action="view/merchant/proses_data.php" enctype="multipart/form-data">
                                    <input type="hidden" name="merchant" value="<?= $_GET['merchant'] ?>">
                                    <div class="form-group">
                                        <label for="input_excel_produk">Import File Excel</label>
                                        <input type="file" name="input_excel_produk" class="form-control" accept=".xls, .xlsx">
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" name="import_produk_excel" class="btn btn-primary float-right">
                                            <i class="fa fa-paper-plane"></i> Save
                                        </button>
                                    </div>
                                </form> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>