<?php
    include("../templates/koneksi.php");
    
    $id_merchant = enkripsiDekripsi($_POST['id_merchant'], 'dekripsi');

    $data = $db->query("SELECT * FROM merchant_produk A WHERE A.status_remove_produk='N' AND A.kd_merchant='$id_merchant'")->fetch_all(MYSQLI_ASSOC);
    
?>
<option value="">Pilih Produk</option>
<?php foreach ($data as $key => $value): ?>
	<option value="<?= enkripsiDekripsi($value['id_merchant_produk'], 'enkripsi') ?>"><?= $value['nama_produk'] ?></option>
<?php endforeach ?>

    
    
    
?>