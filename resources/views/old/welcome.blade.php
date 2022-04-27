@extends('layout')

@section('page title','Pengajuan Cuti')

@section('content')
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs" id="myTab5" role="tablist">
                @foreach ($array as $kategori)
                    <li class="nav-item">
                        <a class="nav-link {{$start==$kategori['id_kategori'] ? 'active' : ''}}" id="tab-tab{{$kategori['id_kategori']}}" data-toggle="tab" href="#tab{{$kategori['id_kategori']}}" role="tab" aria-controls="tab-label{{$kategori['id_kategori']}}" aria-selected="true">
                            <text>{{$kategori['nama_kategori']}}</text>
                        </a>
                    </li>
                @endforeach
                <li class="nav-item">
                    <a class="nav-link" id="tab-tabpaket" data-toggle="tab" href="#tab-paket" role="tab" aria-controls="tab-labelpaket" aria-selected="true">
                        <text>Paket</text>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-tabvoucher" data-toggle="tab" href="#tab-voucher" role="tab" aria-controls="tab-labelvoucher" aria-selected="true">
                        <text>Voucher</text>
                    </a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent5">
                @foreach ($array as $kategori)
                    <div class="tab-pane fade show {{$start==$kategori['id_kategori'] ? 'active' : ''}}" id="tab{{$kategori['id_kategori']}}" role="tabpanel" aria-labelledby="tab-tab{{$kategori['id_kategori']}}">
                        <div class="card">
                            <div class="card-body" id="jenis-ticket1">
                                
                            <table id="table_id" class="display">
                                <?php
                                    $no=1;
                                ?>
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>ID</th>
                                                <th>Nama</th>
                                                <th>Harga</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                @foreach ($item as $produk)
                                    @if ($produk->id_kategori == $kategori['id_kategori'])
                                            <tr>
                                                <td>{{ $no++}}</td>
                                                <td>{{$produk->id_item}}</td>
                                                <td>{{$produk->nama_item}}</td>
                                                <td>Rp. {{number_format($produk->harga_jual,0,',','.')}}</td>
                                                <td>
                                        <button type="button" class="btn-style datatiket btn-info" id="tiket-id-{{$produk->id_item}}" data-dataid="{{$produk->id_item}}" value="{{$produk->nama_item}}">Add</button>

                                                </td>
                                            </tr> 
                                    @endif
                                @endforeach
                                
                                </tbody>
                                    </table>
                            </div>
                        </div>
                    </div>
                @endforeach
                
                <div class="tab-pane fade show" id="tab-paket" role="tabpanel" aria-labelledby="tab-tabpaket">

                    <div class="card-body" id="jenis-ticket2-paket">
                    <table id="table_id" class="display">
                                <?php
                                    $no=1;
                                ?>
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>ID</th>
                                                <th>Nama</th>
                                                <th>Harga</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                @foreach ($paket as $paketitem)
                                            <tr>
                                                <td>{{ $no++}}</td>
                                                <td>{{$paketitem->id_paket}}</td>
                                                <td>{{$paketitem->nama_paket}}</td>
                                                <td>Rp. {{number_format($paketitem->harga_jual,0,',','.')}}</td>
                                                <td>
                                        <button type="button" class="btn-style datatiket btn-info" id="tiket-id-{{$paketitem->id_paket}}" data-dataid="{{$paketitem->id_paket}}" value="{{$paketitem->nama_paket}}">Add</button>

                                                </td>
                                            </tr>  
                                @endforeach
                                
                                </tbody>
                                    </table>
                    </div>
                </div>
                <div class="tab-pane fade show" id="tab-voucher" role="tabpanel" aria-labelledby="tab-tabvoucher">
                    <div class="card">
                        <div class="card-body" id="jenis-voucher">
                            @foreach ($voucher as $produk)
                                <button type="button" class="btn-style btn-produk datatiket" id="diskon-id-{{$produk->id_voucher}}" data-dataid="{{$produk->id_voucher}}" data-tipe="{{$produk->id_voucher}}" data-nominal="{{$produk->nominal}}" data-persen="{{$produk->persen}}" data-makspersen="{{$produk->maks_persen}}" value="{{$produk->nama_voucher}}">{{$produk->nama_voucher}}<br>{{$produk->tipe_voucher==1 ? $produk->nominal : 'Max '.$produk->maks_persen}}</button>
                            @endforeach    
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="id-click">
            <input type="hidden" id="nama-click">
            <input type="hidden" id="qty-click">
            <input type="hidden" id="harga-click">
            <input type="hidden" id="idkategori-click">
            <input type="hidden" id="idstore-click">
        </div>
    </div>
    <button class="btn btn-info position-fixed rounded-circle" style="right:24px;bottom:24px; width:75px; height:75px;" onclick="pesanan_order()">
        <i class="far fa-check-circle" style="font-size:48px;"></i>
    </button>
@endsection

@section('script')
    <script>
        $(document).ready( function () {
            $('.display').DataTable({
                "paging": false,
            });
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const btnHapus = $('#btnHapus');
        const btnEdit = $('#btnEdit');
        const btnBaru = $('#btnBaru');
        const btnClose = $('#btnClose');

        const cardComponent = $('#cardComponent');
        const group = new SlimSelect({
            select: '#group'
        });
        let groupData = [];

        const dataForm = $('#dataForm');
        const inputType = $('#inputType');
        const iID = $('#idMenu');
        const iGroup = $('#group');
        const iNama = $('#nama');
        const iUrl = $('#url');
        const iSegment = $('#segment_name');
        const iOrder = $('#ord');

        let selectedData;

        function resetForm() {
            iID.val('');
            iNama.val('');
            iUrl.val('');
            iSegment.val('');
            iOrder.val('');
        }

        const tableIndex = $('#tableIndex').DataTable({
            "ajax": {
                "method": "POST",
                "url": "{{ url('admin/system-utility/menu/list') }}",
                "header": {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
                },
                "complete": function (xhr,responseText) {
                    if (responseText == 'error') {
                        console.log(xhr);
                        console.log(responseText);
                    }
                }
            },
            "columns": [
                { "data": "group" },
                { "data": "name" },
                { "data": "url" },
                { "data": "segment_name" },
                { "data": "ord" },
            ],
            order: [
                [0,'asc'],
                [4,'asc'],
            ]
        });
        $('#tableIndex tbody').on( 'click', 'tr', function () {
            let data = tableIndex.row( this ).data();
            iID.val(data.id);
            iGroup.val(data.id_group);
            iNama.val(data.name);
            iUrl.val(data.url);
            iSegment.val(data.segment_name);
            iOrder.val(data.ord);
            // console.log(data);
            if ( $(this).hasClass('selected') ) {
                $(this).removeClass('selected');
                btnEdit.attr('disabled','true');
                btnHapus.attr('disabled','true');
            } else {
                tableIndex.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
                btnEdit.removeAttr('disabled');
                btnHapus.removeAttr('disabled');
            }
        });

        $(document).ready(function () {
            /*
            Menu Group List
             */
            $.ajax({
                url: "{{ url('admin/system-utility/menu/group') }}",
                method: "post",
                success: function (response) {
                    // console.log(response);
                    let data = JSON.parse(response);
                    data.forEach(function(v,i) {
                        groupData.push(
                            {text: v.name, value: v.id}
                        )
                    });
                    group.setData(groupData);
                }
            });

            /*
            Button Action
             */
            btnBaru.click(function (e) {
                e.preventDefault();
                inputType.val('new');
                resetForm();
                cardComponent.removeClass('d-none');
                $('html, body').animate({
                    scrollTop: cardComponent.offset().top
                }, 500);
            });
            btnEdit.click(function (e) {
                e.preventDefault();
                inputType.val('edit');
                cardComponent.removeClass('d-none');
                $('html, body').animate({
                    scrollTop: cardComponent.offset().top
                }, 500);
            });
            btnHapus.click(function (e) {
                e.preventDefault();
                Swal.fire({
                    title: iNama.val()+" akan dihapus",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Hapus Data'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: '{{ url('admin/system-utility/menu/delete') }}',
                            method: 'post',
                            data: {id: iID.val()},
                            success: function (response) {
                                console.log(response);
                                if (response === 'success') {
                                    Swal.fire({
                                        title: 'Data terhapus!',
                                        type: 'success',
                                        onClose: function () {
                                            tableIndex.ajax.reload();
                                        }
                                    })
                                } else {
                                    Swal.fire({
                                        title: 'Gagal',
                                        text: 'Silahkan coba lagi',
                                        type: 'error',
                                    })
                                }
                            }
                        });
                    }
                });

            });
            btnClose.click(function (e) {
                e.preventDefault();
                $("html, body").animate({ scrollTop: 0 }, 500, function () {
                    resetForm();
                    cardComponent.addClass('d-none');
                    tableIndex.ajax.reload();
                    btnEdit.attr('disabled','true');
                    btnHapus.attr('disabled','true');
                });
            });

            /*
            SUBMIT DATA
            First: Check new or edit data
             */
            dataForm.submit(function (e) {
                e.preventDefault();
                let url;
                if (inputType.val() === 'new') {
                    url = "{{ url('admin/system-utility/menu/add') }}";
                } else {
                    url = "{{ url('admin/system-utility/menu/edit') }}";
                }
                $.ajax({
                    url: url,
                    method: 'post',
                    data: $(this).serialize(),
                    success: function (response) {
                        console.log(response);
                        if (response === 'success') {
                            Swal.fire({
                                type: 'success',
                                title: 'Data Tersimpan',
                                onClose: function () {
                                    $("html, body").animate({ scrollTop: 0 }, 500, function () {
                                        cardComponent.addClass('d-none');
                                        tableIndex.ajax.reload();
                                    });
                                }
                            })
                        } else {
                            Swal.fire(
                                'Gagal!',
                                'Username atau Password Salah',
                                'warning'
                            )
                        }
                    }
                })
            })
        });


    </script>

    <script type="text/javascript">
        
        $(document).ready(function (){ 
            $('#tab-sidemenu').load('{{ url('pembayaran') }}');
            $('.table-pembayaran tr td #total-all-harga').load('{{ url('pembayaran/total') }}');
            $('.table-pembayaran tr td #n_subtotal').load('{{ url('pembayaran/subtotal') }}');
            $('.table-pembayaran tr td #n_thirdparty').load('{{ url('pembayaran/thirdparty') }}');
            $('tr td.total-harga').load('{{ url('pembayaran/total') }}');
        });

        var id_click;
        var nama_click;
        var qty_click;
        var harga_click;
        var total_click;
        var jumlah_click=0;
        var id_payment;
        var nama_payment;
        var attribut_input = '';

        function removeClassSidemenu(){
            $('.sidebar-menu .sidemenu').removeClass("active");
            remove_hidden();
        }
        
        function clicked(id){
            removeClassSidemenu();
            $('#sidemenu-'+id).addClass("active");
            id_click = id;
            nama_click = $('#sidemenu-'+id+' table .nama-barang').text();
            qty_click = $('#sidemenu-'+id+' table .jumlah-tiket').text();
            total_click = tanpaTitik($('#sidemenu-'+id+' table .harga span').text());
            
            harga_click = total_click/qty_click;
            console.log(tanpaTitik(total_click));
            fill_hidden();
        }

        function tanpaTitik(m){
            var temp = m.split(".");
            return temp.join('');
        }

        function fill_hidden(){
            $('#id-click').val(id_click);
            $('#nama-click').val(nama_click);
            $('#qty-click').val(qty_click);
            $('#harga-click').val(total_click);
        }

        function remove_hidden(){
            $('#id-click').val('');
            $('#nama-click').val('');
            $('#qty-click').val('');
            $('#harga-click').val('');

            id_click = 0;
            nama_click = 0;
            qty_click = 0;
            total_click = 0;
            harga_click = 0;
            jumlah_click=0;
        }
        
        var dataid;
        $(document).on("click", "#jenis-ticket1 button", function(){
            dataid = $(this).data("dataid");
            // alert(dataid);
            $.ajax({
                url: '{{ url("pembayaran/create") }}',
                dataType : 'json',
                type: 'post',
                data: {
                "_token": "{{ csrf_token() }}",
                "id": dataid
                },
                success:function(response) {
                    console.log(response);
                }
            });
            $('#bagan-diskon').load('{{ url('pembayaran/load_diskon') }}');
            $('#tab-sidemenu').load('{{ url('pembayaran') }}');
            $('.table-pembayaran tr td #total-all-harga').load('{{ url('pembayaran/total') }}');
            $('.table-pembayaran tr td #n_subtotal').load('{{ url('pembayaran/subtotal') }}');
            $('tr td.total-harga').load('{{ url('pembayaran/total') }}');
            
        });

        
        $(document).on("click", "#jenis-ticket2-paket button", function(){
            var paketId;
            paketId = $(this).data("dataid");

            alert(paketId);
            
            $('#modalPaketDetail').modal('show');
            
            $('#paket-detail-data').load('{{ url('pembayaran/load_detail_paket') }}'+'/'+paketId);
        });

        $(document).on("click", "#jenis-voucher button", function(){
            dataid = $(this).data("dataid");
            valuee = $(this).val();
            // alert(valuee);
            // alert('wowoo');
            if(dataid == 1){
                $('#jumlah_paket').val('');
                jum_diskon_input();
                $('#modalPaket').modal('show');
            }else{
                $.ajax({
                    url: '{{ url("pembayaran/diskon") }}',
                    dataType : 'json',
                    type: 'post',
                    data: {
                    "_token": "{{ csrf_token() }}",
                    "id": dataid
                    },
                    success:function(response) {
                        console.log(response);
                    }
                });
                $('#bagan-diskon').load('{{ url('pembayaran/load_diskon') }}');
                $('#tab-sidemenu').load('{{ url('pembayaran') }}');
                $('.table-pembayaran tr td #total-all-harga').load('{{ url('pembayaran/total') }}');
                $('.table-pembayaran tr td #n_subtotal').load('{{ url('pembayaran/subtotal') }}');
                $('tr td.total-harga').load('{{ url('pembayaran/total') }}');
            }

            
        });

        function jum_diskon_input(){
            var isi = '';
            var jum_dis = $('#jumlah_paket').val();
            if(jum_dis==''){
                jum_dis = 0;
            }

            for (var i = 1; i <= jum_dis; i++) {
                isi+= '<div class="col-12 mb-2 pr-0" >'+
                        '<input type="number" class="form-control form-control-sm" aria-label="" id="input_paket_'+i+'" style="width:220px" placeholder="Diskon Paket Ke '+i+'" oninput="totalan_diskon()">'+
                      '</div>';
            }

            $('#input_diskon').html(isi);
            $('#total_diskon_paket').text(0);
        }

        function totalan_diskon(){
            var tot_diskon = 0;
            var jum_dis = $('#jumlah_paket').val();

            for (var i = 1; i <= jum_dis; i++) {
                var paket_in = $('#input_paket_'+i).val();
                if(paket_in==''){
                    paket_in=0;
                }
                paket_in = parseInt(paket_in);
                tot_diskon+=paket_in;
            }
            console.log(tot_diskon);
            $('#total_diskon_paket').text(new Intl.NumberFormat("id-ID").format(tot_diskon));
        }

        $(document).on("click", "#validasi_modalPaket", function(){
            var jum_dis = $('#jumlah_paket').val();
            var total_diskon = $('#total_diskon_paket').text();
            total_diskon = tanpaTitik(total_diskon);

            if(jum_dis==0 || jum_dis==''){
                sweetAlert(
                    '',
                    'Jumlah Paket Masih Kosong',
                    'warning'
                )
            }else{
                $.ajax({
                    url: '{{ url("pembayaran/edit_diskon") }}',
                    dataType : 'json',
                    type: 'post',
                    data: {
                    "_token": "{{ csrf_token() }}",
                    "id": dataid,
                    "total_diskon": total_diskon
                    },
                    success:function(response) {
                        console.log(response);
                    }
                });


                $('#bagan-diskon').load('{{ url('pembayaran/load_diskon') }}');
                $('#tab-sidemenu').load('{{ url('pembayaran') }}');
                $('.table-pembayaran tr td #total-all-harga').load('{{ url('pembayaran/total') }}');
                $('.table-pembayaran tr td #n_subtotal').load('{{ url('pembayaran/subtotal') }}');
                $('tr td.total-harga').load('{{ url('pembayaran/total') }}');

                $('#modalPaket').modal('hide');

            }
        });

        $(document).on("click", ".sidebar-brand", function(){
            removeClassSidemenu();
        });

        $(document).on("click", ".card-body", function(){
            removeClassSidemenu();
        });

        $(document).on("click", ".navbar-bg", function(){
            removeClassSidemenu();
        });

        $(document).on("click", ".navbar", function(){
            removeClassSidemenu();
        });

        //delete_item
        $(document).on("click","#btn-trash",function(){
            var id = id_click;
            x = "pembayaran/"+id;

            if(id>0){
                    $.ajax({
                    url: '{{ url("pembayaran") }}'+"/"+id,
                    method: 'DELETE',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        },
                    success: function (response) {
                        console.log("Deleted");
                    }
                });
                $('#bagan-diskon').load('{{ url('pembayaran/load_diskon') }}');
                $('#tab-sidemenu').load('{{ url('pembayaran') }}');
                $('.table-pembayaran tr td #total-all-harga').load('{{ url('pembayaran/total') }}');
                $('.table-pembayaran tr td #n_subtotal').load('{{ url('pembayaran/subtotal') }}');
                $('tr td.total-harga').load('{{ url('pembayaran/total') }}');

                removeClassSidemenu();
            }
        });

        //hapus_qty
        $(document).on("click","#btn-next",function(){
            var id = id_click;
            var value = $('#qty-click').val();
            var n_string;
            var n;
            var totHarga;
            
            if(id>0){
                n=value.substring(0,value.length - 1);
                if(value.length>1){
                    $('#qty-click').val(n);
                    totHarga = harga_click*n;
                    // alert(totHarga);

                }else{
                    $('#qty-click').val(0);
                    totHarga = "";
                    // alert(totHarga);
                    $('#btn-trash').click();
                }
                
                $.ajax({
                    url: '{{ url("pembayaran/edit") }}/'+id_click,
                    dataType : 'json',
                    type: 'patch',
                    data: {
                    "_token": "{{ csrf_token() }}",
                    "qty": n,
                    "total": totHarga
                    },
                    success:function(response) {
                        console.log(response);
                    }
                });
                $('#bagan-diskon').load('{{ url('pembayaran/load_diskon') }}');
                $('#tab-sidemenu').load('{{ url('pembayaran') }}');
                $('.table-pembayaran tr td #total-all-harga').load('{{ url('pembayaran/total') }}');
                $('.table-pembayaran tr td #n_subtotal').load('{{ url('pembayaran/subtotal') }}');
                $('tr td.total-harga').load('{{ url('pembayaran/total') }}');

                
            }
        });

        //update_qty
        $(document).on("click", ".container .row .col-sm-4 button", function(){
            // $('#sidemenu-'+id).addClass("active");
            var x = $(this).text();
            var y = $('#qty-click').val();
            var n; 
            var totHarga;
            if(id_click > 0 && x != "" && total_click != 0 ){
                if(jumlah_click == 0){
                    n=x;
                    totHarga = harga_click*n;
                    $('#qty-click').val(n);
                    $('#harga-click').val(totHarga);
                    // alert(totHarga);
                }else{
                    n=y+x;
                    totHarga = harga_click*n;
                    // alert(totHarga);
                    $('#qty-click').val(n);
                    $('#harga-click').val(totHarga);
                }
                jumlah_click++;


                $.ajax({
                    url: '{{ url("pembayaran/edit") }}/'+id_click,
                    dataType : 'json',
                    type: 'patch',
                    data: {
                    "_token": "{{ csrf_token() }}",
                    "qty": n,
                    "total": totHarga
                    },
                    success:function(response) {
                        console.log(response);
                    }
                });
                $('#bagan-diskon').load('{{ url('pembayaran/load_diskon') }}');
                $('#tab-sidemenu').load('{{ url('pembayaran') }}');
                $('.table-pembayaran tr td #total-all-harga').load('{{ url('pembayaran/total') }}');
                $('.table-pembayaran tr td #n_subtotal').load('{{ url('pembayaran/subtotal') }}');
                $('tr td.total-harga').load('{{ url('pembayaran/total') }}');

            }else if(id_click > 0 && x != "" ){
                n=x;
                $.ajax({
                    url: '{{ url("tiket/harga") }}',
                    dataType : 'json',
                    type: 'post',
                    data: {
                    "_token": "{{ csrf_token() }}",
                    "nama": nama_click,
                    },
                    success:function(response) {
                        console.log(response);
                        // harga_click = response;
                        totHarga = n*response;
                        $('#qty-click').val(n);
                        $('#harga-click').val(totHarga);
                        
                        $.ajax({
                            url: '{{ url("pembayaran/edit") }}/'+id_click,
                            dataType : 'json',
                            type: 'patch',
                            data: {
                            "_token": "{{ csrf_token() }}",
                            "qty": n,
                            "total": totHarga
                            },
                            success:function(response_2) {
                                console.log(response_2);
                                $('#bagan-diskon').load('{{ url('pembayaran/load_diskon') }}');
                                $('#tab-sidemenu').load('{{ url('pembayaran') }}');
                                $('.table-pembayaran tr td #total-all-harga').load('{{ url('pembayaran/total') }}');
                                $('.table-pembayaran tr td #n_subtotal').load('{{ url('pembayaran/subtotal') }}');
                                $('tr td.total-harga').load('{{ url('pembayaran/total') }}');

                            }
                        });
                    }
                });
            }
        });

        //btn-angka modal pembayaran
        $(document).on("click", ".col-sm-7 .row .col-sm-4 button", function(){
            var x = $(this).text();
            var y = $('#'+attribut_input+' tr td input').val();

            if(x!=''){
                if(y.length == 1 && y == 0)
                    $('#'+attribut_input+' tr td input').val(x);
                else
                    $('#'+attribut_input+' tr td input').val(y+x);
            }
            kembalian_uang();
        });

        //btn-nominal modal pembayaran
        $(document).on("click", ".btn-nominal", function(){
            var x = tanpaTitik($(this).text());
            var y = $('#'+attribut_input+' tr td input').val();
            let n = 0;

            if(x!=''){
                if(y==0){
                    $('#'+attribut_input+' tr td input').val(x);
                    // $('#metodePembayaran b').text(new Intl.NumberFormat("id-ID").format(x));
                }else{
                    n= parseInt(y) + parseInt(x) ;
                    $('#'+attribut_input+' tr td input').val(n);
                }
            }

            kembalian_uang();
        });

        function uangKembalian(){
            var bayar = $('#bayar-input').val();
            var totalBayar =  tanpaTitik($('tr td.total-harga').text());
            var kembalian = $('.table-pembayaran tr td.harga b span.kembalian');
            kembalian.text(bayar-totalBayar);
            $('#metodePembayaran b').text(new Intl.NumberFormat("id-ID").format(bayar));
            $('tr td.kembalian').text(new Intl.NumberFormat("id-ID").format(bayar-totalBayar));
        }

        //btn-red modal pembayaran
        $(document).on("click", ".col-sm-7 .row .col-sm-4 button.btn-style.btn-red", function(){
            $('#'+attribut_input+' tr td input').val('0');
            kembalian_uang();
        });

        //btn-back modal pembayaran
        $(document).on("click", ".col-sm-7 .row .col-sm-4 button.btn-style.btn-back", function(){
            var y = $('#'+attribut_input+' tr td input').val();
            var n=y.substring(0,y.length - 1);
            if(y.length>1){
                $('#'+attribut_input+' tr td input').val(n);

            }else{
                $('#'+attribut_input+' tr td input').val(0);
            }
            kembalian_uang();
        });

        //Modal_pembayaran_button
        $(document).on("click",".modal-header .container-fluid .row .col-sm-4 .btn-primary",function(){

            var total_bayar = tanpaTitik($('tr td.total-harga').text());
            var tipe_bayar = $('#nama-payment-method').val();
            var is_splited = $('#split-bill-clicked').val();
            var no_rekening = $('#text-nokartu tr td input').val();
            var debit_cash = $('#split-bill tr td input').val();
            var cash_split = $('#split-bill-tunai tr td input').val();
            var cash = $('#text-bayar tr td input#bayar-input').val();
            var kode_order = $('#kode-order-temp').val();
            var no_invoice = $('#order_no_invoice').text();
            var kembalian = tanpaTitik($('#bar_kembalian').text());
            var change = parseInt(kembalian);
            if(tipe_bayar==''){
                sweetAlert(
                    '',
                    'Mohon Pilih Tipe Bayar',
                    'warning'
                )
            }else{
                if(change<0){
                    sweetAlert(
                        '',
                        'Mohon masukkan nominal yang benar',
                        'warning'
                    )
                }else{
                    $.ajax({
                        url: '{{ url("pembayaran/activity") }}',
                        dataType : 'json',
                        type: 'post',
                        data: {
                        "_token": "{{ csrf_token() }}",
                        'kode_order' : kode_order,
                        'total_bayar' : total_bayar,
                        'tipe_pembayaran' : tipe_bayar,
                        'is_split' : is_splited,
                        'no_rek' : no_rekening,
                        'debit_cash' : debit_cash,
                        'cash' : cash,
                        'cash_split' : cash_split,
                        'no_invoice' : no_invoice,
                        'kembalian' : kembalian
                        },
                        success:function(response) {
                            console.log(response);
                        }
                    });
                    $('#pembayaranModal').modal('hide');
                    // removeClassSidemenu();  
                    // $('#bagan-diskon').load('{{ url('pembayaran/load_diskon') }}');
                    // $('#tab-sidemenu').load('{{ url('pembayaran') }}');
                    // $('.table-pembayaran tr td #total-all-harga').load('{{ url('pembayaran/total') }}');
                    // $('.table-pembayaran tr td #n_subtotal').load('{{ url('pembayaran/subtotal') }}');
                    // $('tr td.total-harga').load('{{ url('pembayaran/total') }}');
                    $('#body_belum_bayar').load('{{ url('pembayaran/belum_bayar') }}');
                    sweetAlert(
                        '',
                        'Pembayaran Berhasil',
                        'success'
                    )
                }
            }
        });

        $(document).on("click",".modal-body .container-fluid .row .col-sm-6 button",function(){
            var voucher_id =  $(".modal-body .container-fluid .row .col-md-12.mb-2 input").val();
            // alert(voucher_id);
            if(voucher_id==''){
                $('#voucherModal').modal('hide');
            }else{
                $.ajax({
                    url: '{{ url("voucher") }}',
                    dataType : 'json',
                    type: 'post',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "kode_voucher": voucher_id,
                    },
                    success:function(response) { 
                        console.log(response);
                    }
                });
                $(".modal-body .container-fluid .row .col-md-12.mb-2 input").val('');
                $('#voucherModal').modal('hide');
            }
        });

        //Nutup modal pembayaran
        $(document).on('click','.modal .modal-dialog .modal-content .modal-header .container-fluid .row .col-sm-4 .btn-red',function(){
            $('#pembayaranModal').modal('hide');
            $("#pembayaranModal input").val('');
            uangKembalian();
        });

        //button tipe pembayaran
        $(document).on("click",".col-md-12.metode-pembayaran button.btn-pay",function(){
            $('#id-payment-method').val($(this).data('dataid'));
            $('#nama-payment-method').val($(this).text());
            $('#is-split-method').val($(this).data('value'));
            $('#tipe-payment-method').val($(this).data('tipe'));

            if($(this).data('tipe')==1){
                $('#bayar-input').val(0);
                $('#metodePembayaran b').text('');
                $('#split-bill tr td input').val(0)
                $('#split-bill-tunai tr td input').val(0)
                $('#text-nokartu tr td input').val();
                $('#metodePembayaran b.plus-split').addClass("hidden");
                $('#split-bill-clicked').val('0');
                uangKembalian();
                
            }else if($(this).data('tipe')==2){
                var t_bayar = $('.total-harga').text();
                $('#metodePembayaran b').text(t_bayar);

                t_bayar = tanpaTitik(t_bayar);
                $('#split-bill tr td input').val(t_bayar)
                $('#split-bill-tunai tr td input').val(0)
                $('#text-nokartu tr td input').val();
                splitUangKembali();
                $('#bayar-input').val(0);
            }
            
        });

        $(document).on("click",".container-fluid.mt-3 .row .col-sm-5 .table-pembayaran",function(){
            attribut_input = $(this).attr('id');
        });

        function kembalian_uang(){
            if(attribut_input == 'split-bill-tunai'){
                splitUangKembali();
            }else if(attribut_input == 'text-bayar'){
                uangKembalian();
            }else if(attribut_input == 'split-bill' || $('#split-bill-clicked').val() == 1){
                splitUangKembali();
            }
        }

        function splitUangKembali(){
            var inp = $('#split-bill tr td input').val()
            if(isNaN(inp)){
                $('#split-bill tr td input').val(0);
                inp = 0
            }else if(inp == 0){
                $('#split-bill tr td input').val(0);
                inp = 0
            }

            var inp = $('#split-bill-tunai tr td input').val()
            if(isNaN(inp)){
                $('#split-bill-tunai tr td input').val(0);
                inp = 0
            }else if(inp == 0){
                $('#split-bill-tunai tr td input').val(0);
                inp = 0
            }

            var uang_debit = parseInt($('#split-bill tr td input').val());
            var uang_tunai = parseInt($('#split-bill-tunai tr td input').val());
            $('#split-bill-tunai tr td input').val(uang_tunai)
            var totalBayar =  tanpaTitik($('tr td.total-harga').text());
            var kembalian = $('.table-pembayaran tr td.harga b span.kembalian');

            $('#metodePembayaran b').text(new Intl.NumberFormat("id-ID").format(uang_debit));
            $('#metodePembayaran b.plus-split').text(" + "+new Intl.NumberFormat("id-ID").format(uang_tunai));
            kembalian.text((uang_debit+uang_tunai)-totalBayar);
            $('tr td.kembalian').text(new Intl.NumberFormat("id-ID").format((uang_debit+uang_tunai)-totalBayar));

        }

        $(document).on("click",".split-bill",function(){
            if($('#is-split-method').val()  !=0){
                if($('#split-bill-clicked').val()==0){
                    $('#split-bill-tunai').removeClass("hidden");
                    $('#kembalian-bill').removeClass("hidden");
                    $('#metodePembayaran b.plus-split').removeClass("hidden");
                    $('#split-bill-clicked').val('1');
                    splitUangKembali();
                }else{
                    $('#split-bill-tunai').addClass("hidden");
                    $('#kembalian-bill').addClass("hidden");
                    $('#metodePembayaran b.plus-split').addClass("hidden");
                    $('#split-bill-clicked').val('0');
                    $('#split-bill-tunai tr td input').val(0)
                }
            }
        });

        $(document).on("click","#hapus-diskon",function(){

            $.ajax({
                    url: '{{ url("pembayaran/del_diskon") }}',
                    dataType : 'json',
                    type: 'get',
                    data: {
                    "_token": "aE1qWnsJQ8Odh11nsfLjAwk3hKoeXj8G2QXpKqSZ",
                    
                    },
                    success:function(response) {
                        console.log(response);
                    }
                });
            $('#bagan-diskon').load('{{ url('pembayaran/load_diskon') }}');
            $('#tab-sidemenu').load('{{ url('pembayaran') }}');
            $('.table-pembayaran tr td #total-all-harga').load('{{ url('pembayaran/total') }}');
            $('.table-pembayaran tr td #n_subtotal').load('{{ url('pembayaran/subtotal') }}');
            $('tr td.total-harga').load('{{ url('pembayaran/total') }}');

        });

        function open_modal_note(){
            $('#textarea_note').val(null);
            $('#modalNote').modal('show');
        }

        function open_modal_order(){

            $('#modalNote').modal('hide');
            $('#modalOrder').modal('show');
            $('#keterangan_order').val('')
            $('#meja_choose').val('')
            $('#type_order').val('')
        }

        function type_or(no){
            $('#type_order').val(no);
        }

        $(document).on("click","#next_type",function(){
            var t_o =  $('#type_order').val()
            $('#keterangan_order').val('')
            console.log(t_o)
            if(t_o == 1){
                $('#modalOrder').modal('hide');
                $('#modalThird').modal('show');
                $('.table-pembayaran tr td #n_thirdparty').load('{{ url('pembayaran/thirdparty') }}');
            }else if(t_o == 2){
                $('#keterangan_order').val('')
                $('#meja_choose').val('')
                order_makan()
                $('#modalOrder').modal('hide');
            }else if(t_o == 3){
                $('#modalOrder').modal('hide');
                $('#daftar_meja').load('{{ url('meja/data_meja') }}');
                $('#meja_choose').val('')
                $('#modalDine').modal('show');
            }else{
                sweetAlert(
                    '',
                    'Mohon Anda Belum Memilih Type Order',
                    'warning'
                )
            }
        });

        function set_3rd(no){
            var tx = $('#third_'+no).text()
            $('#keterangan_order').val(tx)
            // alert(tx)
        }

        function set_meja(no){
            $('#meja_choose').val(no)
            var no_meja = $('#meja'+no).text()
            $('#keterangan_order').val(no_meja)
            // alert(no_meja)
        }

        $(document).on("click","#validasi_dine_in",function(){
            var t_o =  $('#type_order').val()
            var m_c =  $('#meja_choose').val()
            if(m_c != ''){
                order_makan();
                $('#modalDine').modal('hide');
            }else{
                sweetAlert(
                    '',
                    'Mohon Anda Belum Memilih Meja',
                    'warning'
                )
            }
        });

        $(document).on("click","#btn_validasi_third",function(){
            var t_o =  $('#type_order').val()
            var k_o =  $('#keterangan_order').val()
            if(k_o != ''){
                order_makan();
                $('#modalThird').modal('hide');
            }else{
                sweetAlert(
                    '',
                    'Mohon Pilih Third Party',
                    'warning'
                )
            }
        });

        function order_makan(){

            var total_bayar = tanpaTitik($('#total-all-harga').text());
            var subtotal = tanpaTitik($('#n_subtotal').text());
            var thirdparty = tanpaTitik($('#n_thirdparty').text());
            var t_o =  $('#type_order').val()
            var k_o =  $('#keterangan_order').val()
            var m_o = $('#meja_choose').val()
            var note = $('#textarea_note').val();
            console.log(note)
            console.log(total_bayar);
            console.log(subtotal);
            console.log(m_o);

            $.ajax({
                url: '{{ url("pembayaran/order") }}',
                dataType : 'json',
                type: 'post',
                data: {
                "_token": "{{ csrf_token() }}",
                'subtotal' : subtotal,
                'thirdparty' : thirdparty,
                'total_bayar' : total_bayar,
                'type_order' : t_o,
                'keterangan_order' : k_o,
                'meja_choose' : m_o,
                'note' : note,
                },
                success:function(response) {
                }
            });
            
            removeClassSidemenu();  
            $('#bagan-diskon').load('{{ url('pembayaran/load_diskon') }}');
            $('#tab-sidemenu').load('{{ url('pembayaran') }}');
            $('.table-pembayaran tr td #total-all-harga').load('{{ url('pembayaran/total') }}');
            $('.table-pembayaran tr td #n_subtotal').load('{{ url('pembayaran/subtotal') }}');
            $('tr td.total-harga').load('{{ url('pembayaran/total') }}');

            sweetAlert(
                '',
                'Order Berhasil',
                'success'
            );

            
            
            
        }

        function pesanan_order(){
            $('#body_belum_bayar').load('{{ url('pembayaran/belum_bayar') }}');
            $('#modalOrderPay').modal('show');
        }

        function pay_order(id){
            var kode_temp = $('#kode_temp'+id).text();
            $('#kode-order-temp').val(kode_temp);
            $('#bayar-input').val(0);
            $('#kartu-input').val(0);
            $('#split-bill tr td input').val(0);
            $('#split-bill-tunai tr td input').val(0);
            $('#body_modalOrderDetail').load('{{ url('pembayaran/detail_belum_bayar') }}/'+kode_temp);
            $('#modalOrderDetail').modal('show');
        }

        $(document).on("click","#validasi_modalOrderDetail",function(){
            var total_h = $('#total_order').text();
            $('#modalOrderDetail').modal('hide');
            $('tr td.total-harga').text(total_h);
            $('#pembayaranModal').modal('show');
            splitUangKembali();
            uangKembalian();
        });

        function print_resi(){
            var no_inv = $('#order_no_invoice').text();
            $.get(('{{ url('pembayaran/print_resi') }}/'+no_inv), function(data, status){
                console.log("Data: " + data + "\nStatus: " + status);
            });
        }

        function del_order(kode_temp){
            // e.preventDefault();
            // alert('asd');
            swal({
                title: 'Are you sure?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Delete'
            }).then((result) => {
                if (result.dismiss !== 'cancel') {
                    $.ajax({
                        url: "{{ url('pembayaran/del_order') }}/"+kode_temp,
                        dataType : 'json',
                        type: 'get',
                        data: {
                            "_token": "aE1qWnsJQ8Odh11nsfLjAwk3hKoeXj8G2QXpKqSZ",
                        },
                        success:function(response) {
                            console.log('del_order')
                        }
                    });

                    $('#body_belum_bayar').load('{{ url('pembayaran/belum_bayar') }}');
                    sweetAlert(
                        '',
                        'Delete Order Berhasil',
                        'success'
                    )
                }
            });


        }
        
        function input_tunai(){
            var inp = $('#bayar-input').val()
            if(isNaN(inp)){
                $('#bayar-input').val(0);
                inp = 0
            }else if(inp == 0){
                $('#bayar-input').val(0);
                inp = 0
            }

            $('#bayar-input').val(parseInt(inp))
            var totalBayar =  tanpaTitik($('tr td.total-harga').text());
            var kembalian = $('.table-pembayaran tr td.harga b span.kembalian');

            $('#metodePembayaran b').text(new Intl.NumberFormat("id-ID").format(inp));
            kembalian.text(inp-totalBayar);
            $('tr td.kembalian').text(new Intl.NumberFormat("id-ID").format(inp-totalBayar));
            // alert(inp)
        }

        function edit_order(id){
            var kode_temp = $('#kode_temp'+id).text();
            $.get(('{{ url('pembayaran/add_revisi_order') }}/'+kode_temp), function(data, status){
                console.log("Data: " + data + "\nStatus: " + status);
            });
            // $('#kode-order-temp').val(kode_temp);
            // $('#bayar-input').val(0);
            // $('#kartu-input').val(0);
            // $('#split-bill tr td input').val(0);
            // $('#split-bill-tunai tr td input').val(0);
            $('#body_modalOrderEdit').load('{{ url('pembayaran/detail_belum_bayar_edit') }}/'+kode_temp);
            $('#modalOrderEdit').modal('show');
        }

        function edit_orderEdit(id){
            $('#body_modalOrderEditItem').load('{{ url('pembayaran/detail_belum_bayar_editItem') }}/'+id);
            $('#modalOrderEditItem').modal('show');
            // $('#id_item_edit_order').val(id);
        }

        function del_orderEdit(id){
            $.get(('{{ url('pembayaran/del_revisi_order') }}/'+id), function(data, status){
                console.log("Data: " + data + "\nStatus: " + status);
            });
            var kode_temp = $('#id_orderEdit').text();
            $('#body_modalOrderEdit').load('{{ url('pembayaran/detail_belum_bayar_edit') }}/'+kode_temp);
        }

        $(document).on("click","#validasi_modalOrderDetailEditItem",function(){
            var id_item = $('#id_item_edit_order').val();
            var id_item_sel = $('#sel_order_revisi').val();
            var qty_item = $('#qty_order_revisi').val();

            // console.log(qty_item)

            $.ajax({
                url: '{{ url("pembayaran/edit_item_order") }}',
                dataType : 'json',
                type: 'post',
                data: {
                "_token": "{{ csrf_token() }}",
                'id_item' : id_item,
                'id_item_sel' : id_item_sel,
                'qty_item' : qty_item
                },
                success:function(response) {
                    console.log(response)
                }
            });

            var kode_temp = $('#id_orderEdit').text();
            $('#body_modalOrderEdit').load('{{ url('pembayaran/detail_belum_bayar_edit') }}/'+kode_temp);
            $('#modalOrderEditItem').modal('hide');
        });

        $(document).on("click","#validasi_modalOrderEdit",function(){
            var kode_temp = $('#id_orderEdit').text();
            var subtotal = $('#subtotalOrderEdit').text();
            subtotal = tanpaTitik(subtotal)
            var total = $('#totalOrderEdit').text();
            total = tanpaTitik(total)

            $.ajax({
                url: '{{ url("pembayaran/edit_order") }}',
                dataType : 'json',
                type: 'post',
                data: {
                "_token": "{{ csrf_token() }}",
                'kode_temp' : kode_temp,
                'subtotal' : subtotal,
                'total' : total
                },
                success:function(response) {
                    console.log(response)
                }
            });

            $('#body_modalOrderEdit').load('{{ url('pembayaran/detail_belum_bayar_edit') }}/'+kode_temp);
            $('#body_belum_bayar').load('{{ url('pembayaran/belum_bayar') }}');
            $('#modalOrderEdit').modal('hide');
        });

        function open_AddItem(){
            $('#body_modalOrderAddItem').load('{{ url('pembayaran/add_edit_order') }}');
            $('#modalOrderAddItem').modal('show');
        }

        $(document).on("click","#validasi_modalOrderDetailAddItem",function(){
            var kode_temp = $('#id_orderEdit').text();

            var id_item = $('#sel_order_add').val();
            var qty_item = $('#qty_order_add').val();
            // console.log(id_item);

            $.ajax({
                url: '{{ url("pembayaran/add_edit_item_order") }}',
                dataType : 'json',
                type: 'post',
                data: {
                "_token": "{{ csrf_token() }}",
                'kode_temp' : kode_temp,
                'id_item' : id_item,
                'qty_item' : qty_item
                },
                success:function(response) {
                    console.log(response)
                }
            });


            $('#body_modalOrderEdit').load('{{ url('pembayaran/detail_belum_bayar_edit') }}/'+kode_temp);
            $('#modalOrderAddItem').modal('hide');
        });

        function open_note(id){
            $.get(('{{ url('pembayaran/get_note') }}/'+id), function(data, status){
                console.log("Data: " + data + "\nStatus: " + status);
                data = JSON.parse(data)
                $('#textarea_note_edit').val(data)
            });

            $('#textarea_note_edit_id').val(id);
            $('#modalNoteEdit').modal('show');
        }

        function edit_note(){
            var id_belum = $('#textarea_note_edit_id').val();
            var note = $('#textarea_note_edit').val();

            $.ajax({
                url: '{{ url("pembayaran/edit_note") }}',
                dataType : 'json',
                type: 'post',
                data: {
                "_token": "{{ csrf_token() }}",
                'id' : id_belum,
                'note' : note
                },
                success:function(response) {
                    console.log(response)
                }
            });
            var kode_temp = $('#kode_temp'+id_belum).text()
            $('#body_modalOrderEdit').load('{{ url('pembayaran/detail_belum_bayar_edit') }}/'+kode_temp);
            $('#modalNoteEdit').modal('hide');
        }



  </script>
@endsection
