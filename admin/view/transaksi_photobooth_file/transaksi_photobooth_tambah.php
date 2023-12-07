<?php
    include("../../templates/koneksi.php");
    
    $list_stall = $db->query("SELECT * FROM merchant WHERE status_aktif_merchant='Y'")->fetch_all(MYSQLI_ASSOC);
    
?>

<style type="text/css">
	table{
		margin: 20px auto;
		border-collapse: collapse;
	}
	table th,
	table td{
		border: 1px solid #3c3c3c;
		padding: 3px 8px;
		font-size: 80%;
	}
</style>


<table>
    <thead>
        <tr>
            <th colspan="3"></th>
            <?php foreach ($list_stall as $key => $value): ?>
                <?php $jml_kasir = $db->query("SELECT COUNT(id_merchant_employee) AS jml FROM merchant_employee WHERE kd_merchant='$value[id_merchant]' AND status_aktif_employee='Y'")->fetch_assoc()['jml'] ?>
                <th colspan="<?= ($jml_kasir*3)+3 ?>"><?= $value['nama_merchant'] ?></th>
            <?php endforeach ?>
        </tr>
        <tr>
            <th colspan="3"></th>
            <?php foreach ($list_stall as $key => $value): ?>
                <?php $list_kasir = $db->query("SELECT * FROM merchant_employee WHERE kd_merchant='$value[id_merchant]' AND status_aktif_employee='Y'")->fetch_all(MYSQLI_ASSOC) ?>
                <?php foreach ($list_kasir as $key_list_kasir => $value_list_kasir): ?>
                    <th colspan="3"><?= $value_list_kasir['username_employee'] ?></th>
                <?php endforeach ?>
                    <th colspan="3">TOTAL</th>
            <?php endforeach ?>
        </tr>
        <tr>
            <th nowrap="">No</th>
            <th nowrap="">Nama Barang</th>
            <th nowrap="">Harga Jual</th>
            <?php foreach ($list_stall as $key => $value): ?>
                <?php $list_kasir = $db->query("SELECT * FROM merchant_employee WHERE kd_merchant='$value[id_merchant]' AND status_aktif_employee='Y'")->fetch_all(MYSQLI_ASSOC) ?>
                <?php foreach ($list_kasir as $key_list_kasir => $value_list_kasir): ?>
                    <th>Jml</th>
                    <th>Diskon</th>
                    <th>Rupiah</th>
                <?php endforeach ?>
                    <th>Jml</th>
                    <th>Diskon</th>
                    <th>Rupiah</th>
            <?php endforeach ?>
        </tr>
    </thead>
    <tbody>
        <?php $no_urut = 0; ?>
        <?php foreach ($list_stall as $key => $value): ?>
            <?php $list_barang = $db->query("SELECT * FROM merchant_produk WHERE kd_merchant='$value[id_merchant]' AND status_remove_produk='N'")->fetch_all(MYSQLI_ASSOC); ?>
            <?php foreach ($list_barang as $key_list_barang => $value_list_barang): ?>
                <?php $no_urut ++ ?>
                <tr>
                    <td><?=  $no_urut  ?></td>
                    <td><?= $value_list_barang['nama_produk'] ?></td>
                    <td align="right"><?= number_format($value_list_barang['harga_produk']) ?></td>

                    <?php foreach ($list_stall as $key_list_stall_row => $value_list_stall_row): ?>

                        <?php $list_kasir = $db->query("SELECT * FROM merchant_employee WHERE kd_merchant='$value_list_stall_row[id_merchant]' AND status_aktif_employee='Y'")->fetch_all(MYSQLI_ASSOC) ?>

                        <?php foreach ($list_kasir as $key_list_kasir => $value_list_kasir): ?>
                                <?php $total_semua[$value_list_kasir['id_merchant_employee']]['jml'] = 0 ?>
                                <?php $total_semua[$value_list_kasir['id_merchant_employee']]['diskon'] = 0 ?>
                                <?php $total_semua[$value_list_kasir['id_merchant_employee']]['subtotal'] = 0 ?>
                            <?php endforeach ?>
                        

                        <?php if ($value_list_stall_row['id_merchant'] == $value['id_merchant']): ?>

                            <?php $jml = 0 ?>
                            <?php $diskon = 0 ?>
                            <?php $total = 0 ?>



                            <?php foreach ($list_kasir as $key_list_kasir => $value_list_kasir): ?>
                                <?php $penjualan = $db->query("SELECT SUM(A.jumlah_produk) AS jml, SUM(A.diskon) AS diskon, SUM(A.harga_setelah_diskon) AS subtotal FROM merchant_transaksi_detail A WHERE A.kd_merchant_employee='$value_list_kasir[id_merchant_employee]' AND A.kd_merchant='$value[id_merchant]' AND A.kd_merchant_produk='$value_list_barang[id_merchant_produk]' AND A.status_transaksi_detail='2'")->fetch_assoc(); ?>

                                <td align="right" nowrap=""><?= number_format($penjualan['jml']) ?>,-</td>
                                <td align="right" nowrap=""><?= number_format($penjualan['diskon']) ?>,-</td>
                                <td align="right" nowrap=""><?= number_format($penjualan['subtotal']) ?>,-</td>

                                <?php $jml += $penjualan['jml'] ?>
                                <?php $diskon += $penjualan['diskon'] ?>
                                <?php $total += $penjualan['subtotal'] ?>


                            <?php endforeach ?>

                                <?php $total_semua[$value_list_kasir['id_merchant_employee']]['jml'] += $jml ?>
                                <?php $total_semua[$value_list_kasir['id_merchant_employee']]['diskon'] += $diskon ?>
                                <?php $total_semua[$value_list_kasir['id_merchant_employee']]['subtotal'] += $total ?>

                                <td align="right" nowrap=""><?= number_format($jml) ?>,-</td>
                                <td align="right" nowrap=""><?= number_format($diskon) ?>,-</td>
                                <td align="right" nowrap=""><?= number_format($total) ?>,-</td>

                        <?php else: ?>
                            <?php foreach ($list_kasir as $key_list_kasir => $value_list_kasir): ?>
                                <td></td>
                                <td></td>
                                <td></td>
                            <?php endforeach ?>
                                <td></td>
                                <td></td>
                                <td></td>
                        <?php endif ?>


                        

                    <?php endforeach ?>

                </tr>
            <?php endforeach ?>
            <!-- <tr>
                <td></td>
                <td></td>
                <td></td>
            </tr> -->
        <?php endforeach ?>

        <!-- TOTAL -->
        <tr>
            <td></td>
            <td></td>
            <td></td>


            <?php foreach ($list_stall as $key_list_stall_row => $value_list_stall_row): ?>

                <?php if ($value_list_stall_row['id_merchant'] == $value['id_merchant']): ?>

                    <?php foreach ($list_kasir as $key_list_kasir => $value_list_kasir): ?>
                        <td>x</td>
                        <td>x</td>
                        <td>x</td>
                    <?php endforeach ?>
                        <td>x</td>
                        <td>x</td>
                        <td>x</td>

                <?php else: ?>
                    <?php foreach ($list_kasir as $key_list_kasir => $value_list_kasir): ?>
                        <td>x</td>
                        <td>x</td>
                        <td>x</td>
                    <?php endforeach ?>
                        <td>x</td>
                        <td>x</td>
                        <td>x</td>
                <?php endif ?>


                

            <?php endforeach ?>


        </tr>
    </tbody>
</table>

<pre>
    <?= print_r($total_semua) ?>
</pre>