<?php
    $hari_tanggal_satu = date_format(date_create($_GET['id']."-1"),"N");
    
    $x = $_GET['id'];
    $tahun = explode('-',$x)[0];
    $bulan = explode('-',$x)[1];
    
    
    $hari_libur = $db->query("SELECT hari_libur FROM hari_libur WHERE tahun_bulan='$_GET[id]'")->fetch_assoc()['hari_libur'];
    $hari_libur = explode('|', $hari_libur);
    
    
    switch($bulan){
      case 2:
          if($tahun%4==0){
              if($tahun%100==0){
                  if($tahun%400==0){
                      $maxtanggal = 29;
                  }else{
                      $maxtanggal = 28;
                  }
              }else{
                  $maxtanggal = 29;
              }
          }else{
              $maxtanggal = 28;
          }
          break;
      case 4:
          $maxtanggal = 30;
          break;
      case 6:
          $maxtanggal = 30;
          break;
      case 9:
          $maxtanggal = 30;
          break;
      case 11:
          $maxtanggal = 30;
          break;
      default:
          $maxtanggal = 31;
    }
    
    if($hari_tanggal_satu == "1"){
        $start_tanggal = 0;
    }elseif($hari_tanggal_satu == "2"){
        $start_tanggal = -1;
    }elseif($hari_tanggal_satu == "3"){
        $start_tanggal = -2;
    }elseif($hari_tanggal_satu == "4"){
        $start_tanggal = -3;
    }elseif($hari_tanggal_satu == "5"){
        $start_tanggal = -4;
    }elseif($hari_tanggal_satu == "6"){
        $start_tanggal = -5;
    }elseif($hari_tanggal_satu == "7"){
        $start_tanggal = -6;
    }
    
?>
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
          <form role="form" method="post" action="view/libur/proses_data.php" enctype="multipart/form-data">
            <div class="card-body">   
              <div class="row">
                  <div class="col-md-6">
                      <div class="form-group">
                        <label for="tahun_libur">Tahun</label>
                        <input type="text" name="tahun_libur" class="form-control" id="tahun_libur" maxlength="4" value="<?= $tahun ?>" readonly required>
                      </div>
                  </div>
                  <div class="col-md-6">
                      <div class="form-group">
                        <label for="tahun_libur">Bulan</label>
                        <input type="text" name="bulan_libur" class="form-control" id="bulan_libur" maxlength="4" value="<?= $bulan ?>" readonly required>
                      </div>
                  </div>
              </div>

              <div class="form-group" id="kalender_tabel">
                <label for="kalender">Kalender</label>
                <table id="kalender" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th align="center">Senin</th>
                      <th align="center">Selasa</th>
                      <th align="center">Rabu</th>
                      <th align="center">Kamis</th>
                      <th align="center">Jumat</th>
                      <th align="center">Sabtu</th>
                      <th align="center">Minggu</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php for ($x = 1; $x <= 5; $x++): ?>
                        <tr>
                            <?php for ($y = 1; $y <= 7; $y++): ?>
                            <?php $start_tanggal++ ?>
                            <td>
                                <?php if($start_tanggal > 0 AND $start_tanggal <= $maxtanggal): ?>
                                    
                                    <?= $start_tanggal ?>
                                    <br>
                                    <?php if(in_array($start_tanggal, $hari_libur)): ?>
                                        <input type="checkbox" name="hari_libur[]" value="<?= $start_tanggal ?>" checked>
                                    <?php else : ?>
                                        <input type="checkbox" name="hari_libur[]" value="<?= $start_tanggal ?>">
                                    <?php endif; ?>
                                    
                                <?php endif; ?>
                            </td>
                            <?php endfor; ?>
                        </tr>
                    <?php endfor; ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer">
              <input type="submit" name="edit_hari_libur" class="btn btn-primary" id="btnSubmit" value="Submit">
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