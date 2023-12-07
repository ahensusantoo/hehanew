<?php
    $eid = enkripsiDekripsi($_GET['eid'],'dekripsi');

    $data_supplier = $db->query("SELECT * FROM supplier WHERE id_supplier = '$eid' ")->fetch_assoc();
    
    // echo "<pre>"; print_r($data_supplier);die();
?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Supplier</h1>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <form role="form" method="post" action="view/admin/proses_data.php" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="kode_supplier">Kode Supplier</label>
                                <input type="hidden" name="id" value="<?= $_GET['eid'] ?>">
                                <input type="text" name="kode_supplier" value="<?= $data_supplier['kode_supplier']?>" class="form-control" id="kode_supplier" placeholder="Kode Supplier" maxlength="20" required>
                            </div>
                            <div class="form-group">
                                <label for="nama_supplier">Nama Supplier</label>
                                <input type="text" name="nama_supplier" value="<?= $data_supplier['nama_supplier']?>" class="form-control" id="nama_supplier" placeholder="Nama Supplier" maxlength="100" required>
                            </div>
                            <div class="form-group">
                                <label for="telp_supplier">Nomor Telepon</label>
                                <input type="number" name="telp_supplier" value="<?= $data_supplier['telp_supplier']?>" class="form-control" id="telp_supplier" placeholder="Telepon Supplier" maxlength="15" autocomplete="off" required>
                            </div>
                            <div class="form-group">
                                <label for="alamat">Alamat Supplier</label>
                                <textarea name="alamat" id="alamat" class="form-control" placeholder="Alamat Supplier"><?=$data_supplier['alamat_supplier']?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="status_aktif_supplier">Status Aktif Employee</label></span>
                                <select name="status_aktif_supplier" class="form-control" id="status_aktif_supplier" required>
                                    <option value="Y" <?php if($data_supplier['status_aktif_supplier'] == "Y") { echo "selected"; } ?> >AKTIF</option>
                                    <option value="N" <?php if($data_supplier['status_aktif_supplier'] == "N") { echo "selected"; } ?> >NON AKTIF</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-footer">
                            <input type="submit" name="edit_supplier" class="btn btn-primary" id="btnSubmit" value="Submit" style="width: 100%">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script type="text/javascript">
    function cekPass(){
        if ($('#password_employee').val() == $('#password_employee_cnf').val()) {
            $('#message').html('');
            document.getElementById("btnSubmit").disabled = false; 
        } else {
            $('#message').html('Tidak Cocok').css('color', 'red');
            document.getElementById("btnSubmit").disabled = true; 
        }
    };
</script>