<style type="text/css">
  td {
    text-align: center; 
    vertical-align: middle;
  }
</style>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Tambah Libur</h1>
      </div>
    </div>
  </div>
</section>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-primary">
          <form role="form" method="post" action="" enctype="multipart/form-data">
            <div class="card-body">              
              <div class="form-group">
                <label for="tahun_libur">Tahun</label>
                <input type="text" name="tahun_libur" class="form-control" id="tahun_libur" maxlength="4" value="<?= date('Y') ?>" required>
              </div>
              <div class="form-group">
                <label for="bulan_libur">Bulan</label>
                <select name="bulan_libur" class="form-control" id="bulan_libur" required>
                  <option value="" selected>-- Bulan --</option>
                  <option value="01">Januari</option>
                  <option value="02">Februari</option>
                  <option value="03">Maret</option>
                  <option value="04">April</option>
                  <option value="05">Mei</option>
                  <option value="06">Juni</option>
                  <option value="07">Juli</option>
                  <option value="08">Agustus</option>
                  <option value="09">September</option>
                  <option value="10">Oktober</option>
                  <option value="11">November</option>
                  <option value="12">Desember</option>
                </select>
              </div>

              <div class="form-group">
                <button type="button" onclick="lanjut_kalender()" class ="btn btn-info btn-xm" style="margin: 3px; width: 100%">Lanjut</button>
              </div>

              <div class="form-group" id="kalender_tabel" style="display: none">
                <label for="kalender">Kalender</label>
                <table id="kalender" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th align="center">Minggu</th>
                      <th align="center">Senin</th>
                      <th align="center">Selasa</th>
                      <th align="center">Rabu</th>
                      <th align="center">Kamis</th>
                      <th align="center">Jumat</th>
                      <th align="center">Sabtu</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- <?php
                    for ($i=1; $i < 33; $i++) { 
                      $tahun_bulan_now = date('Y-m-');
                      $number_hari_now = date('N', strtotime($tahun_bulan_now.sprintf('%02s',$i)));
                      $tanggal_now = date('d', strtotime($tahun_bulan_now.sprintf('%02s',$i)));
                      if ($i == 1) {
                        echo '<tr>';
                        if ($number_hari_now == '7') {
                          ?><?php
                        } elseif ($number_hari_now == '1') {
                          ?><td></td><?php
                        } elseif ($number_hari_now == '2') {
                          ?><td></td><td></td><?php
                        } elseif ($number_hari_now == '3') {
                          ?><td></td><td></td><td></td><?php
                        } elseif ($number_hari_now == '4') {
                          ?><td></td><td></td><td></td><td></td><?php
                        } elseif ($number_hari_now == '5') {
                          ?><td></td><td></td><td></td><td></td><td></td><?php
                        } elseif ($number_hari_now == '6') {
                          ?><td></td><td></td><td></td><td></td><td></td><td></td><?php
                        }
                        ?>
                        <td align="center">
                          <input type="radio" class="form-control" name="tanggal<?php echo $i ?>" id="tanggal<?php echo $i ?>" value="<?php echo $i ?>">
                          <label for="tanggal<?php echo $i ?>"><?php echo $i; ?></label>
                        </td>
                        <?php
                      } elseif ($i == 32) {
                        ?></tr><?php
                      } elseif ($i > 1 && $i <32) {
                        if($tanggal_now != '01'){
                          if ($number_hari_now == '7') {
                            echo '</tr><tr>'
                            ?>
                            <td align="center">
                              <input type="radio" class="form-control" name="tanggal<?php echo $i ?>" id="tanggal<?php echo $i ?>" value="<?php echo $i ?>">
                              <label for="tanggal<?php echo $i ?>"><?php echo $i; ?></label>
                            </td>
                            <?php
                          } elseif ($number_hari_now != '7') {
                            ?>
                            <td align="center">
                              <input type="radio" class="form-control" name="tanggal<?php echo $i ?>" id="tanggal<?php echo $i ?>" value="<?php echo $i ?>">
                              <label for="tanggal<?php echo $i ?>"><?php echo $i; ?></label>
                            </td>
                            <?php
                          }
                        } else {
                          $i = 31;
                        }
                      }
                    }
                    ?> -->
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer">
              <input type="submit" name="btnSubmit" class="btn btn-primary" id="btnSubmit" value="Submit">
            </div>
          </form>
        </div>
      </div>
      <div class="col-md-6">
      </div>
    </div>
  </div>
</section>

<script type="text/javascript">
  $('#kalender').dataTable({
    "paging": false
  });
</script>

<script type="text/javascript">
  function lanjut_kalender(){
    while (document.getElementById('kalender').rows.length > 1) {
      document.getElementById('kalender').deleteRow(1);
    }
    var myKeyVals = { tahun : document.getElementById('tahun_libur').value, bulan : document.getElementById('bulan_libur').value }

    var saveData = $.ajax({
      type: 'POST',
      url: "view/libur/libur_ajax_data.php",
      data: myKeyVals,
      dataType: "text",
      success: function(resultData) { 
        // alert(resultData);
        document.getElementById('kalender_tabel').style.display = "block";

        function tambah_input(increment, cellke, terpilih) {
          var y = document.createElement("label");
          y.setAttribute("for", "tanggal"+increment);
          y.innerHTML = increment+' ';
          cellke.appendChild(y);

          var z = document.createElement("BR"); 
          cellke.appendChild(z);

          var x = document.createElement("INPUT");
          x.setAttribute("type", "checkbox");
          x.setAttribute("style", "form-control");
          x.setAttribute("name", "tanggal"+increment);
          x.setAttribute("id", "tanggal"+increment);
          if(terpilih == true){
            x.checked = true;
          }
          cellke.appendChild(x);
        }

        var table = document.getElementById('kalender').getElementsByTagName('tbody')[0];
        var tr = 0;
        var pecah_hari_terpilih = resultData.split('|');
        for (var i=1; i < 33; i++) {
          var d = new Date(document.getElementById('tahun_libur').value+"-"+document.getElementById('bulan_libur').value+"-"+i);

          var tgl = d.getDate();
          var hari = d.getDay();

          var def_terpilih = pecah_hari_terpilih.includes(tgl.toString());

          if(!isNaN(tgl)){
            if(i == 1){
              var row = table.insertRow(tr);
              if(hari == 0){
                var cell1 = row.insertCell(0);
                tambah_input(i, cell1, def_terpilih);
              } else if(hari == 1){
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                tambah_input(i, cell2, def_terpilih);
              } else if(hari == 2){
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                tambah_input(i, cell3, def_terpilih);
              } else if(hari == 3){
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                var cell4 = row.insertCell(3);
                tambah_input(i, cell4, def_terpilih);
              } else if(hari == 4){
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                var cell4 = row.insertCell(3);
                var cell5 = row.insertCell(4);
                tambah_input(i, cell5, def_terpilih);
              } else if(hari == 5){
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                var cell4 = row.insertCell(3);
                var cell5 = row.insertCell(4);
                var cell6 = row.insertCell(5);
                tambah_input(i, cell6, def_terpilih);
              } else if(hari == 6){
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                var cell4 = row.insertCell(3);
                var cell5 = row.insertCell(4);
                var cell6 = row.insertCell(5);
                var cell7 = row.insertCell(6);
                tambah_input(i, cell7, def_terpilih);
              }
            } else if (i > 1 && i <32) {
              if(hari == 0){
                tr++;
                var row = table.insertRow(tr);
                var cell1 = row.insertCell(0);
                tambah_input(i, cell1, def_terpilih);
              } else if(hari == 1){
                var cell2 = row.insertCell(1);
                tambah_input(i, cell2, def_terpilih);
              } else if(hari == 2){
                var cell3 = row.insertCell(2);
                tambah_input(i, cell3, def_terpilih);
              } else if(hari == 3){
                var cell4 = row.insertCell(3);
                tambah_input(i, cell4, def_terpilih);
              } else if(hari == 4){
                var cell5 = row.insertCell(4);
                tambah_input(i, cell5, def_terpilih);
              } else if(hari == 5){
                var cell6 = row.insertCell(5);
                tambah_input(i, cell6, def_terpilih);
              } else if(hari == 6){
                var cell7 = row.insertCell(6);
                tambah_input(i, cell7, def_terpilih);
              }
            }
          }
        }
      }
    });
saveData.error(function() { alert("Something went wrong"); });

}
</script>