<?php
$sess_kd_merchant = enkripsiDekripsi($_GET['merchant'], 'dekripsi');
?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Mutasi Masuk</h1>
      </div>
    </div>
  </div>
</section>

<section class="content">
  <div class="container-fluid">
    <form role="form" method="post" action="view/merchant/proses_data.php" enctype="multipart/form-data">
      <div class="row">
        <div class="col-md-12">
          <div class="card card-primary">
            <div class="card-body">
              <div class="form-group">
                <label>Keterangan Mutasi</label>
                <textarea rows="3" class="form-control" name="keterangan_kepala"></textarea>
                <input type="number" name="temp_jml" value="0" id="temp_jml" style="display: none">
              </div>
              <hr>
              <label>Data Produk Dimutasi</label>
              <button type="button" class="btn btn-sm btn-outline-info float-right mb-2"  data-toggle="modal" data-target="#modal-product">Tambah Produk</button>
              <input type="hidden" name="merchant" value="<?= $_GET['merchant'] ?>">
              <table class="table table-bordered text-sm">
                <thead>
                  <tr>
                    <th nowrap="">Kode Produk</th>
                    <th nowrap="">Nama Produk</th>
                    <th>Jumlah Mutasi</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Keterangan</th>
                    <th>Hapus</th>
                  </tr>
                  <tbody id="tab_produk_dimutasi">
                    <tr id="td_tanpa_data">
                      <td colspan="7" class="text-center">Belum Ada Produk Terpilih</td>
                    </tr>
                  </tbody>
                </thead>
              </table>
            </div>
            <div class="card-footer">
              <input type="submit" name="mutasi_keluar_gc" class="btn btn-primary" id="btnSubmit" value="Submit" style="width: 100%">
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</section>




<!-- modal -->
<div class="modal fade" id="modal-product">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Pilih Kasir</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hodden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <div class="row">
                    <div class="col-lg-8"></div>
                    <div class="col-lg-4">
                        <input type="text" name="search" id="pencarian" class="form-control mb-2" placeholder="pencarian">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bodered table-striped" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Product</th>
                                <th>Nama Product</th>
                                <!-- <td>jenis</td> -->
                                <th>Pilih</th>
                            </tr>
                        </thead>
                        <tbody id="tbl_list">
                        </tbody>
                    </table> 
                </div>
                <div class="row">
                    <div class="col-6">
                        <nav class="">
                            Record : <span id="total_data"></span>
                        </nav>
                    </div>
                    <div class="col-6">
                        <nav aria-label="Page navigation" id="pagination"></nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function(){
        $("#tbl_list").empty();
        load_record();
    })

    function load_record(page = 1, search = null, sendData=null){
        if(sendData != null){
            var sendData = convertSerialize(sendData);
        }
        $.ajax({
            url : "view/merchant/proses_data.php",
            type : "post",
            data: {
                page : page,
                search : search,
                meth : 'get_record_product',
                kd_merchant : '<?=$sess_kd_merchant?>',
                sendData: sendData
            },
            dataType:"json",
            beforeSend:function(){
                $('.preloader').show()
            },
            complete:function(){
                $('.preloader').hide()
            },
            success:function(data){
                var html = ''
                if(data.record.length > 0 ){
                    var no = ((parseInt(data.page-1))*parseInt(data.perpage)+1);
                    $.each(data.record, function(key, value) {
                        html += `
                            <tr>  
                                <td>${no++}</td>
                                <td>${value.kode_produk}</td>
                                <td>${value.nama_produk}</td>
                                <td>
                                    <button class="btn btn-xs btn-info pilih_produk" 
                                        data-nama_produk="${value.nama_produk}"
                                        data-id_produk="${value.id_merchant_produk}"
                                        data-harga_beli_produk="${value.harga_beli}"
                                        data-kode_produk="${value.kode_produk}"
                                        data-harga_jual_produk="${value.harga_produk}"
                                        >
                                        <i class="fa fa-check"></i>Select
                                    </button>
                                </td>    
                            </tr> 
                        `;
                    });


                    $('#total_data').html(data.total_rows)
                    var pagination = pagination_hal(data.total_rows, data.perpage, data.page, '')
                    $('#pagination').html(pagination);
                }else{
                    html += `
                        <tr>
                            <td colspan="4" class="text-center">Belum ada data</td>
                        </tr>
                    `
                }
                $("#tbl_list").html(html)
            }
        })
    }

    var searchTerm = "";
    $("#pencarian").on("keydown", function(event) {
        if (event.keyCode === 13) {
            searchTerm = $(this).val();
            if (document.getElementById('filter_form')) {
                // Elemen dengan ID "filter_form" ditemukan
                var formData = $('#filter_form').serialize();
            } else {
                // Elemen dengan ID "filter_form" tidak ditemukan
                var formData = null
            }

            load_record(1, searchTerm, formData);
        }
    });

    $("#pagination").on("click", ".page-link[data-halaman]", function() {
        load_record($(this).data("halaman"), searchTerm);
    });


    function pagination_hal(total_rows, perpage, hal_aktif, url) {
        var pagination = '<ul class="pagination justify-content-end">'; // Tambahkan ul tag untuk daftar halaman
        var paging = Math.ceil(total_rows / perpage);

        // PREV BUTTON
        if (hal_aktif == "1"){
            pagination += `<li class="page-item"><a disabled class="page-link">&laquo</a></li>`
        }else{
            pagination += `<li class="page-item"><a class="page-link spinner_aktif" href="javascript:void(0)" data-halaman="${parseInt(hal_aktif) - 1}" >&laquo</a></li>`
        }

        // PAGE PREV
        if (hal_aktif > 1){
            for (i=(parseInt(hal_aktif)-2); i < hal_aktif; i++) { 
                if (i < hal_aktif && i > 0) {
                   pagination += `<li class="page-item"><a class="page-link spinner_aktif"href="javascript:void(0)" data-halaman="${parseInt(i)}">`+i+`</a></li>`;
                }
            }
        }

        // PAGE ACTIVE
        pagination += `<li class="page-item active"><a disabled class="page-link">`+hal_aktif+`</a></li>`

        // PAGE NEXT
        if (hal_aktif < paging){
            for (i=(parseInt(hal_aktif)+1); i < (parseInt(hal_aktif)+3); i++) { 
                if (i > hal_aktif && i <= paging) {
                    pagination += `<li class="page-item"><a class="page-link spinner_aktif" href="javascript:void(0)" data-halaman="${parseInt(i)}">${i}</a></li>`;
                }
            }
        }

        //NEXT BUTTON
        if (hal_aktif == paging){
            pagination += `<li class="page-item"><a disabled class="page-link">&raquo</a></li>`
        }else{
            pagination += `<li class="page-item"><a class="page-link spinner_aktif" href="javascript:void(0)" data-halaman="${parseInt(hal_aktif) + 1}">&raquo</a></li>`
        }

        pagination += '</ul>'; // Tutup ul tag untuk daftar halaman
        return pagination;
    }



    $("#tbl_list").on('click', ".pilih_produk", function(){
        var temp_jml = parseInt($("#temp_jml").val());
        var temp_jml_after = temp_jml + 1;
        var temp_jml = $("#temp_jml").val(temp_jml_after);

        var id_produk = $(this).data('id_produk');
        var kode_produk = $(this).data('kode_produk');
        var nama_produk = $(this).data('nama_produk');
        var harga_beli_produk = $(this).data('harga_beli_produk');
        var harga_jual_produk = $(this).data('harga_jual_produk');

        // alert(kode_produk)

        $("#tab_produk_dimutasi").append(`

          <tr>
            <td>
              <input type="hidden" name="produk_dimutasi[`+temp_jml_after+`][id]" value="`+id_produk+`">
              `+kode_produk+`
            </td>
            <td>`+nama_produk+`</td>
            <td><input type="number" class="form-control" name="produk_dimutasi[`+temp_jml_after+`][jml]" value="1" min='1' required></td>
            <td><input type="number"  class="form-control" name="produk_dimutasi[`+temp_jml_after+`][beli]" min='1' value="`+harga_beli_produk+`" required></td>
            <td><input type="number"  class="form-control" name="produk_dimutasi[`+temp_jml_after+`][jual]" min='1' value="`+harga_jual_produk+`" required></td>
            <td><input type="text"  class="form-control" name="produk_dimutasi[`+temp_jml_after+`][ket]"></td>
            <td><button type="button" class="badge badge-danger hapus_persatuan">Hapus</button></td>
          </tr>

        `)

        $("#td_tanpa_data").css('display','none');
        $("#modal-product .close").trigger('click');

      })

      $("#tab_produk_dimutasi").on('click', '.hapus_persatuan', function(){
        $(this).closest('tr').remove();
      })
</script>