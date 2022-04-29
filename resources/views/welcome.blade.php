@extends('layout')

@section('page title','Pengajuan Cuti')

@section('style')
<link rel="stylesheet" href="css/invoice.css">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
{{-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/dt-1.11.5/datatables.min.css"/> --}}
@endsection

@section('content')
<div class="card">
        <div class="card-body">
            <div class="container">
                <div class="row">
                    <div class="col d-flex justify-content-end">
                        <button type="button" class="w-auto mr-3 btn btn-primary" data-toggle="modal" data-target="#menuModal">Menu</button>
                        <button type="button" class="w-auto mr-3 btn btn-success" onclick="open_modal_note()">Order</button>
                    </div>
                </div>
            </div>
            <div id="invoice">
                @include('_partials.invoice', ['pembayaran' => [], 'sub_total' => 0, 'thirdparty' => 0, 'total' => 0, 'pajak_thirdparty' => 0, 'pajak' => 0, 'sub_total_thirdparty' => 0])
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
    {{-- <script type="text/javascript" src="https://cdn.datatables.net/v/bs/dt-1.11.5/datatables.min.js"></script> --}}
    <script>
        $(document).ready( function () {
            $('.display').DataTable();
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
        function loadInvoice () {
            // $('#bagan-diskon').load('{{ url('pembayaran/load_diskon') }}');
            // $('#tab-sidemenu').load('{{ url('pembayaran') }}');
            $('#invoice').load('{{ url('pembayaran') }}');
            // $('.table-pembayaran tr td #total-all-harga').load('{{ url('pembayaran/total') }}');
            // $('.table-pembayaran tr td #n_subtotal').load('{{ url('pembayaran/subtotal') }}');
            // $('.table-pembayaran tr td #n_thirdparty').load('{{ url('pembayaran/thirdparty') }}');
            // $('tr td.total-harga').load('{{ url('pembayaran/total') }}');
        }
        $(document).ready(function (){ 
            loadInvoice();
            // $('#tab-sidemenu').load('{{ url('pembayaran') }}');
            // $('#invoice').load('{{ url('pembayaran') }}');
            // $('.table-pembayaran tr td #total-all-harga').load('{{ url('pembayaran/total') }}');
            // $('.table-pembayaran tr td #n_subtotal').load('{{ url('pembayaran/subtotal') }}');
            // $('.table-pembayaran tr td #n_thirdparty').load('{{ url('pembayaran/thirdparty') }}');
            // $('tr td.total-harga').load('{{ url('pembayaran/total') }}');
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
        
        /** The Add Button **/
        var dataid;
        $(document).on("click", "#jenis-ticket1 button", function(){
            dataid = $(this).data("dataid");
            // alert(dataid);
            add(dataid)
            // $.ajax({
            //     url: '{{ url("pembayaran/create") }}',
            //     dataType : 'json',
            //     type: 'post',
            //     data: {
            //     "_token": "{{ csrf_token() }}",
            //     "id": dataid
            //     },
            //     success:function(response) {
            //         console.log(response);
            //     }
            // });
            // $('#bagan-diskon').load('{{ url('pembayaran/load_diskon') }}');
            // $('#tab-sidemenu').load('{{ url('pembayaran') }}');
            // $('.table-pembayaran tr td #total-all-harga').load('{{ url('pembayaran/total') }}');
            // $('.table-pembayaran tr td #n_subtotal').load('{{ url('pembayaran/subtotal') }}');
            // $('tr td.total-harga').load('{{ url('pembayaran/total') }}');
            
        });

        function add(dataid, type = null, additionalMenuItem = []) {
            $.ajax({
                url: '{{ url("pembayaran/create") }}',
                dataType : 'json',
                type: 'post',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": dataid,
                    type: type,
                    additional_menu: additionalMenuItem
                },
                success:function(response) {
                    loadInvoice();
                }
            });
            // $('#bagan-diskon').load('{{ url('pembayaran/load_diskon') }}');
            // $('#tab-sidemenu').load('{{ url('pembayaran') }}');
            // $('.table-pembayaran tr td #total-all-harga').load('{{ url('pembayaran/total') }}');
            // $('.table-pembayaran tr td #n_subtotal').load('{{ url('pembayaran/subtotal') }}');
            // $('tr td.total-harga').load('{{ url('pembayaran/total') }}');
            
        }

        
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
                        loadInvoice();
                    }
                });
                // $('#bagan-diskon').load('{{ url('pembayaran/load_diskon') }}');
                // $('#tab-sidemenu').load('{{ url('pembayaran') }}');
                // $('#invoice').load('{{ url('pembayaran') }}');
                // $('.table-pembayaran tr td #total-all-harga').load('{{ url('pembayaran/total') }}');
                // $('.table-pembayaran tr td #n_subtotal').load('{{ url('pembayaran/subtotal') }}');
                // $('tr td.total-harga').load('{{ url('pembayaran/total') }}');
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
                        loadInvoice();
                    }
                });


                // $('#bagan-diskon').load('{{ url('pembayaran/load_diskon') }}');
                // $('#tab-sidemenu').load('{{ url('pembayaran') }}');
                // $('#invoice').load('{{ url('pembayaran') }}');
                // $('.table-pembayaran tr td #total-all-harga').load('{{ url('pembayaran/total') }}');
                // $('.table-pembayaran tr td #n_subtotal').load('{{ url('pembayaran/subtotal') }}');
                // $('tr td.total-harga').load('{{ url('pembayaran/total') }}');

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
        // $(document).on("click",".btn-trash",function(button){
        //     var id = button.target.dataset.id
        //     x = "pembayaran/"+id;

        //     if(id>0){
                // $.ajax({
                //     url: '{{ url("pembayaran") }}'+"/"+id,
                //     method: 'DELETE',
                //     data: {
                //         "_token": "{{ csrf_token() }}",
                //         },
                //     success: function (response) {
                //         console.log("Deleted");
                //         id = 0
                //         loadInvoice();
                //     }
                // });
                // $('#bagan-diskon').load('{{ url('pembayaran/load_diskon') }}');
                // $('#tab-sidemenu').load('{{ url('pembayaran') }}');
                // $('#invoice').load('{{ url('pembayaran') }}');
                // $('.table-pembayaran tr td #total-all-harga').load('{{ url('pembayaran/total') }}');
                // $('.table-pembayaran tr td #n_subtotal').load('{{ url('pembayaran/subtotal') }}');
                // $('tr td.total-harga').load('{{ url('pembayaran/total') }}');

                // removeClassSidemenu();
        //     }
        // });

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
                        loadInvoice();
                    }
                });
                // $('#bagan-diskon').load('{{ url('pembayaran/load_diskon') }}');
                // $('#tab-sidemenu').load('{{ url('pembayaran') }}');
                // $('#invoice').load('{{ url('pembayaran') }}');
                // $('.table-pembayaran tr td #total-all-harga').load('{{ url('pembayaran/total') }}');
                // $('.table-pembayaran tr td #n_subtotal').load('{{ url('pembayaran/subtotal') }}');
                // $('tr td.total-harga').load('{{ url('pembayaran/total') }}');

                
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
                        loadInvoice();
                    }
                });
                // $('#bagan-diskon').load('{{ url('pembayaran/load_diskon') }}');
                // $('#tab-sidemenu').load('{{ url('pembayaran') }}');
                // $('#invoice').load('{{ url('pembayaran') }}');
                // $('.table-pembayaran tr td #total-all-harga').load('{{ url('pembayaran/total') }}');
                // $('.table-pembayaran tr td #n_subtotal').load('{{ url('pembayaran/subtotal') }}');
                // $('tr td.total-harga').load('{{ url('pembayaran/total') }}');

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
                                loadInvoice();
                                // $('#bagan-diskon').load('{{ url('pembayaran/load_diskon') }}');
                                // $('#tab-sidemenu').load('{{ url('pembayaran') }}');
                                // $('#invoice').load('{{ url('pembayaran') }}');
                                // $('.table-pembayaran tr td #total-all-harga').load('{{ url('pembayaran/total') }}');
                                // $('.table-pembayaran tr td #n_subtotal').load('{{ url('pembayaran/subtotal') }}');
                                // $('tr td.total-harga').load('{{ url('pembayaran/total') }}');

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
        $(document).on("click","#btn-validasi-pembayaran",function(){
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
            var voucher_id = $('input[name="diskon_value"]').val();
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
                        'kembalian' : kembalian,
                        'voucher_id': voucher_id
                        },
                        success:function(response) {
                            console.log(response);
                        }
                    });
                    $('#pembayaranModal').modal('hide');
                    // removeClassSidemenu();  
                    // $('#bagan-diskon').load('{{ url('pembayaran/load_diskon') }}');
                    // $('#tab-sidemenu').load('{{ url('pembayaran') }}');
                    $('#invoice').load('{{ url('pembayaran') }}');
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
        var buttonPembayaran
        $(document).on("click",".col-md-12.metode-pembayaran button.btn-pay",function(){
            buttonPembayaran = $(this)
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
                $('#bayar-input').focus()
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
                        loadInvoice();
                    }
                });
            // $('#bagan-diskon').load('{{ url('pembayaran/load_diskon') }}');
            // $('#tab-sidemenu').load('{{ url('pembayaran') }}');
            // $('#invoice').load('{{ url('pembayaran') }}');
            // $('.table-pembayaran tr td #total-all-harga').load('{{ url('pembayaran/total') }}');
            // $('.table-pembayaran tr td #n_subtotal').load('{{ url('pembayaran/subtotal') }}');
            // $('tr td.total-harga').load('{{ url('pembayaran/total') }}');

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
                    loadInvoice();
                }
            });
            
            removeClassSidemenu();  
            // $('#bagan-diskon').load('{{ url('pembayaran/load_diskon') }}');
            // $('#tab-sidemenu').load('{{ url('pembayaran') }}');
            // $('#invoice').load('{{ url('pembayaran') }}');
            // $('.table-pembayaran tr td #total-all-harga').load('{{ url('pembayaran/total') }}');
            // $('.table-pembayaran tr td #n_subtotal').load('{{ url('pembayaran/subtotal') }}');
            // $('tr td.total-harga').load('{{ url('pembayaran/total') }}');

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
            var subtotal_h = $('#sub_total_order').text();
            var pajak_h = $('#pajak_order').text();
            $('#modalOrderDetail').modal('hide');
            $('tr td.total-harga').text(total_h);
            $('tr td.subtotal_penjualan').text(subtotal_h);
            $('tr td.pajak_penjualan').text(pajak_h);
            $('#pembayaranModal').modal('show');
            $('#pembayaranModal').on('hidden.bs.modal', function () {
                removeDiskon();
            })
            var paymentMethod = $('#tipe-payment-method');
            console.log(paymentMethod[0].value == 1)
            splitUangKembali();
            uangKembalian();
            if (paymentMethod[0].value == 1) $('#bayar-input').focus()
        });

        function print_resi(event){
            var id_diskon = $(event).data('id-diskon')
            var no_inv = $('#order_no_invoice').text();
            $.get((`{{ url('pembayaran/print_resi') }}/${no_inv}?id_diskon=${id_diskon}`), function(data, status){
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
                            console.log(response)
                            $('#body_belum_bayar').load('{{ url('pembayaran/belum_bayar') }}');
                            sweetAlert(
                                '',
                                'Delete Order Berhasil',
                                'success'
                            )
                        }
                    });

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

        var data_edit_order = {
            createData: [],
            updateData: [],
            deleteData: []
        }

        function edit_order(id){
            $('#body_modalOrderEdit').html(loadingAdditionalMenu)
            var kode_temp = $('#kode_temp'+id).text();
            $.get(('{{ url('pembayaran/add_revisi_order') }}/'+kode_temp), function(data, status){
                console.log("Data: " + data + "\nStatus: " + status);
                data_edit_order = {
                    createData: [],
                    updateData: [],
                    deleteData: []
                }
                $('#body_modalOrderEdit').load('{{ url('pembayaran/detail_belum_bayar_edit') }}/'+kode_temp);
            });
            // $('#kode-order-temp').val(kode_temp);
            // $('#bayar-input').val(0);
            // $('#kartu-input').val(0);
            // $('#split-bill tr td input').val(0);
            // $('#split-bill-tunai tr td input').val(0);
            $('#modalOrderEdit').modal('show');
        }

        function edit_orderEdit(id, event){
            prevMenuPaket = 0
            setCreateDataToNull()
            var idpaket = $(event).data('idpaket')
            var idkategori = $(event).data('idkategori')
            $('#body_modalOrderEditItem').html(loadingAdditionalMenu)
            $.ajax({
                url: '{{ url('pembayaran/detail_belum_bayar_editItem') }}/'+id+`?id_paket=${idpaket}&id_kategori=${idkategori}`,
                success: function (response) {
                    $('#body_modalOrderEditItem').html(response)
                    $('#sel_order_revisi').select2({
                        placeholder: "Pilih Item",
                        dropdownParent: '#modalOrderEditItem'
                    })
                    var selected_item = $('#sel_order_revisi').select2('data')
                    if (selected_item.length > 0) {
                        setCreateData(selected_item[0].element, createData, '#sel_order_revisi_additional_option')
                    }
                    $('#sel_order_revisi').on('select2:select', function (select) {
                        setCreateDataToNull()
                        setCreateData(select.target.selectedOptions, createData, '#sel_order_revisi_additional_option')
                    })
                    validasiModalOrderDetailEditItem()
                },
                error: function (error) {

                }
            })
            $('#modalOrderEditItem').modal('show');
            $('#modalOrderEditItem').on('hidden.bs.modal', function () {
                setCreateDataToNull()
                $('#validasi_modalOrderDetailEditItem').off('click')
                $('#body_modalOrderEditItem').html('')
            })
        }

        function del_orderEdit(id){
            $.get(('{{ url('pembayaran/del_revisi_order') }}/'+id), function(data, status){
                // console.log("Data: " + data + "\nStatus: " + status);
                var dataCreateData = data_edit_order.createData.filter(function (create) {
                    return create.id_pos_revisi_bayar == data.id
                })
                if (dataCreateData.length == 0) data_edit_order.deleteData.push(data)
                data_edit_order.createData = data_edit_order.createData.filter(function (create) {
                    return create.id_pos_revisi_bayar != data.id
                })
                data_edit_order.updateData = data_edit_order.updateData.filter(function (update) {
                    return update.id_pos_belum_bayar != data.id_pos_belum_bayar
                })
                console.log(data_edit_order);
                var kode_temp = $('#id_orderEdit').text();
                $('#body_modalOrderEdit').load('{{ url('pembayaran/detail_belum_bayar_edit') }}/'+kode_temp);
            });
        }

        function validasiModalOrderDetailEditItem () {
            $('#validasi_modalOrderDetailEditItem').on('click', function (button) {
                createData.opsi_menu = []
                $('.menu_terpilih').each(function (key, val) {
                    var menuTerpilih = $(val).data('opsimenu')
                    createData.opsi_menu.push(menuTerpilih)
                })
                if (createData.type === 'paket' && createData.opsi_menu.length === 0) {
                    sweetAlert(
                            '',
                            `Silahkan pilih opsi menu terlebih dahulu`,
                            'warning'
                        )
                    return;
                }
                var buttonTypes = $('button.btn-item-type')
                if (buttonTypes.length > 0) {
                    if (!createData.item_type) {
                        sweetAlert(
                            '',
                            `Silahkan pilih menu type terlebih dahulu`,
                            'warning'
                        )
                        return;
                    }
                }
                var unselected_menu_type = 0
                $('button.opsi_menu_text').each(function(index, val) {
                    var menu_type = Number($(val).data('menu_type'))
                    var selected_menu_type = Number($(val).data('selected_menu_type'))
                    if (menu_type > 0 && selected_menu_type == 0) unselected_menu_type += 1;
                })
                if (unselected_menu_type > 0) {
                    sweetAlert(
                        '',
                        `Silahkan pilih menu type terlebih dahulu`,
                        'warning'
                    )
                    return;
                }
                button.preventDefault()
                var id_item = $('#id_item_edit_order').val();
                var id_pos_belum_bayar = $('#id_pos_belum_bayar').val();
                var id_item_sel = $('#sel_order_revisi').val();
                var qty_item = $('#qty_order_revisi').val();
                var kode_temp = $('#id_orderEdit').text();
                var updateDataEdit = createData
                updateDataEdit.kode_temp = kode_temp
                updateDataEdit.qty_item = qty_item
                updateDataEdit.id_pos_revisi_bayar = id_item
                updateDataEdit.id_pos_belum_bayar = id_pos_belum_bayar
                $.ajax({
                    url: '{{ url("pembayaran/edit_item_order") }}',
                    dataType : 'json',
                    type: 'post',
                    data: updateDataEdit,
                    success:function(response) {
                        var createResponse = response['create']
                        var updateResponse = response['update']
                        var createDataEdit = data_edit_order.createData.filter (function (data) {
                            return data.id == updateResponse.id
                        })
                        if (createDataEdit.length == 0) {
                            data_edit_order.updateData = data_edit_order.updateData.filter( function (data) {
                                return data.id_pos_revisi_bayar != updateDataEdit.id_pos_revisi_bayar
                            })
                            data_edit_order.updateData.push(updateResponse)
                        } else {
                            data_edit_order.createData = data_edit_order.createData.filter ( function (data) {
                                return data.id != updateResponse.id
                            })
                            data_edit_order.createData.push(updateResponse)
                        }
                        if (createResponse != null) data_edit_order.createData.push(createResponse)
                        $.ajax({
                            url: '{{ url('pembayaran/detail_belum_bayar_edit') }}/'+kode_temp,
                            success: function (response) {
                                $('#body_modalOrderEdit').html(response)
                                $('#modalOrderEditItem').modal('hide');
                            }
                        })
                    }
                });
            })
        }

        $('#validasi_modalOrderEdit').on('click', function () {
            var kode_temp = $('#id_orderEdit').text();
            $.ajax({
                url: '{{ url("pembayaran/edit_order") }}',
                dataType : 'json',
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    kode_temp: kode_temp,
                    data: data_edit_order
                },
                success:function(response) {
                    console.log(response)
                    $('#body_belum_bayar').load('{{ url('pembayaran/belum_bayar') }}');
                    $('#modalOrderEdit').modal('hide');
                }
            });
        })

        function open_AddItem(){
            prevMenuPaket = 0
            $('#body_modalOrderAddItem').html(loadingAdditionalMenu)
            setCreateDataToNull()
            $.ajax({
                url: '{{ url('pembayaran/add_edit_order') }}'
            }).done( function (response) {
                $('#body_modalOrderAddItem').html(response)
                $('#sel_order_add').select2({
                    placeholder: "Pilih Item",
                    dropdownParent: $("#modalOrderAddItem")
                })
                $('#sel_order_add').on('select2:select', function (event) {
                    setCreateData(event.target.selectedOptions, createData, '#add_item_additional_menu')
                })
            })
            $('#modalOrderAddItem').modal('show');
            $('#modalOrderAddItem').on('hidden.bs.modal', function () {
                $('#saveMenu').off('click')
                $('#body_modalOrderAddItem').html('')
                setCreateDataToNull()
            })
        }

        $(document).on("click","#validasi_modalOrderDetailAddItem",function(){
            var html = $('#body_modalOrderEdit').html()
            var kode_temp = $('#id_orderEdit').text();
            createData.opsi_menu = []
            $('.menu_terpilih').each(function (key, val) {
                var menuTerpilih = $(val).data('opsimenu')
                createData.opsi_menu.push(menuTerpilih)
            })
            if (createData.type === 'paket' && createData.opsi_menu.length === 0) {
                sweetAlert(
                        '',
                        `Silahkan pilih opsi menu terlebih dahulu`,
                        'warning'
                    )
                return;
            } else {
                var types = $('button.btn-item-type')
                console.log(types)
                if (types.length > 0) {
                    if (!createData.item_type) {
                        sweetAlert(
                            '',
                            `Silahkan pilih menu type terlebih dahulu`,
                            'warning'
                        )
                        return;
                    }
                }
            }
            var id_item = $('#sel_order_add').val();
            var qty_item = $('#qty_order_add').val();
            var createDataEdit = createData
            createDataEdit.kode_temp = kode_temp
            createDataEdit.qty_item = qty_item
            $('#body_modalOrderEdit').html(loadingAdditionalMenu)

            $.ajax({
                url: '{{ url("pembayaran/add_edit_item_order") }}',
                dataType : 'json',
                type: 'post',
                data: createDataEdit,
                success:function(response) {
                    setCreateDataToNull()
                    $('#body_modalOrderEdit').load('{{ url('pembayaran/detail_belum_bayar_edit') }}/'+kode_temp);
                    if (response !== 0) {
                        data_edit_order.createData.push(response);
                        $('#additionalMenuBody').html('')
                    }
                    $('#modalOrderAddItem').modal('hide');
                }, error: function (error) {
                    $('#body_modalOrderEdit').html(html)
                }
            });

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
            // $('#body_modalOrderEdit').load('{{ url('pembayaran/detail_belum_bayar_edit') }}/'+kode_temp);
            $('#modalNoteEdit').modal('hide');
        }



  </script>

    <script>
        var loadingAdditionalMenu = `<div class="d-flex justify-content-center" id="loadingAdditionalMenu">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>`
        var createData = {
            "_token": "{{ csrf_token() }}",
            id: null,
            id_kategori: null,
            type: null,
            additional_menu: [],
            item_type: null,
            item_size: null,
            opsi_menu: []
        }
        function setCreateDataToNull() {
            createData.id = null
            createData.id_kategori = null
            createData.item_type = null
            createData.item_size = null
            createData.additional_menu = []
            createData.opsi_menu = []
        }
        function toggleClassActive(element) {
            $(element).toggleClass('active')
            $(element).siblings().removeClass('active')
            $(element).siblings().children('.item-check').html('')
        }
        function setAdditionalItem(element) {
            if (createData.type == 'item') {
                var checked = $(element)[0].checked
                var value = $(element).val()
                if (checked) {
                    createData.additional_menu.push(value)
                } else {
                    var additionalMenu = createData.additional_menu.filter(function (additionalItem) {
                        return additionalItem != value
                    })
                    createData.additional_menu = additionalMenu
                }
            } else {
                
            }
        }
        async function ajax(dataType, dataKategori, dataId, paket = 0) {
            return $.ajax({
                url: '{{ url("additional-menu") }}',
                data: {
                    type: dataType,
                    id_kategori: dataKategori,
                    id: dataId,
                    paket: paket,
                    id_paket: createData.id
                }
            })
        }
        function getItem (item, element) {
            toggleClassActive(element)
            if (item == null) {
                $(element).children('.item-check').html('&check;')
                return {
                    id: $(element).data('id'),
                    text: $(element).data('text'),
                    harga: $(element).data('harga'),
                }
            } else if ($(element).data('id') != item.id) {
                $(element).children('.item-check').html('&check;')
                return {
                    id: $(element).data('id'),
                    text: $(element).data('text'),
                    harga: $(element).data('harga'),
                }
            } else {
                $(element).children('.item-check').html('')
                return null
            }
        }
        function getAdditionalMenu (additionalMenu, element) {
            var checked = element.target.checked
            var value = element.target.value
            if (checked) {
                additionalMenu.push({
                    id: value,
                    text: element.target.dataset.text,
                    harga: element.target.dataset.harga,
                })
            } else {
                additionalMenu = additionalMenu.filter( function (detail) {
                    return detail.id != value
                })
            }
            return additionalMenu;
        }
        function selectAdditional (data) {
            $('.btn-item-type').on('click', function () {
                data.item_type = getItem(data.item_type, this)
            })
            $('.btn-item-size').on('click', function () {
                data.item_size = getItem(data.item_size, this)
            })
            $('.additional_menu_item').on('change', function (additional) {
                data.additional_menu = getAdditionalMenu(data.additional_menu, additional)
            })
            return data;
        }
        async function setCreateData(event, createData, body = null) {
            var dataId = $(event).data('dataid')
            var dataType = $(event).data('type')
            var dataKategori = $(event).data('kategori')
            var dataText = $(event).data('text')
            setCreateDataToNull()
            $(body).html(loadingAdditionalMenu)
            createData.type = dataType
            createData.id = dataId
            createData.id_kategori = dataKategori
            if (dataText) createData.text = dataText
            return ajax(dataType, dataKategori, dataId).then(function (response) {
                $(body).html(response)
                if (dataType == 'item') {
                    createData = selectAdditional(createData)
                } else {
                    var datatables = $('table.table_opsi_menu').DataTable()
                    createData.opsi_menu = []
                    // $('.opsi_menu_text').each(function (key, value) {
                    //     createData.opsi_menu.push({
                    //         id: $(value).data('dataid'),
                    //         menuid: $(value).data('menuid'),
                    //         nama_item: $(value).data('text'),
                    //         id_item: null,
                    //         id_kategori: $(value).data('kategori'),
                    //         item_type: null,
                    //         item_size: null,
                    //         additional_menu: []
                    //     })
                    // })
                }
                return response
            })
        }
        function openAdditionalMenu (event) {
            $('#additionalMenu').modal()
            setCreateData(event, createData, '#additionalMenuBody').then( function (response) {
                $('#additionalMenu').on('hidden.bs.modal', function () {
                    $('#saveMenu').off('click')
                    $('#additionalMenuBody').html('')
                    turnOffSelectAdditional()
                })
            })
            $('#saveMenu').on('click', function () {
                var buttonTypes = $('button.btn-item-type')
                if (buttonTypes.length > 0) {
                    if (!createData.item_type) {
                        sweetAlert(
                            '',
                            `Silahkan pilih menu type terlebih dahulu`,
                            'warning'
                        )
                        return;
                    }
                }
                var unselected_menu_type = 0
                $('button.opsi_menu_text').each(function(index, val) {
                    var menu_type = Number($(val).data('menu_type'))
                    var selected_menu_type = Number($(val).data('selected_menu_type'))
                    if (menu_type > 0 && selected_menu_type == 0) unselected_menu_type += 1;
                })
                if (unselected_menu_type > 0) {
                    sweetAlert(
                            '',
                            `Silahkan pilih menu type terlebih dahulu`,
                            'warning'
                        )
                    return;
                }
                createData.opsi_menu = []
                $('.menu_terpilih').each(function (key, val) {
                    var menuTerpilih = $(val).data('opsimenu')
                    createData.opsi_menu.push(menuTerpilih)
                })
                var textError = []
                if (createData.type === 'paket') {
                    if (createData.opsi_menu.length === 0) {
                        sweetAlert(
                                '',
                                `Silahkan pilih opsi menu terlebih dahulu`,
                                'warning'
                            )
                        return;
                    }
                    $('tbody[class*="daftar_menu_terpilih_"]').each(function(key, val) {
                        var jumlahMenuKategori = $(val).data('jumlah')
                        var namaKategori = $(val).data('kategori')
                        var jumlahMenuTerpilih = $(val).children('.menu_terpilih')
                        if (jumlahMenuTerpilih.length < jumlahMenuKategori) {
                            textError.push(`${jumlahMenuKategori - jumlahMenuTerpilih.length} menu ${namaKategori}`);
                        }
                    })
                }
                if (textError.length > 0) {
                    textError = textError.join(', ')
                    sweetAlert(
                        '',
                        `Silahkan pilih ${textError} terlebih dahulu`,
                        'warning'
                    )
                    return
                }
                $.ajax({
                    url: '{{ url("pembayaran/create") }}',
                    dataType : 'json',
                    type: 'post',
                    data: createData,
                    success:function(response) {
                        $('#additionalMenu, #menuModal').modal('hide')
                        $('#additionalMenuBody').html('')
                        loadInvoice();
                    }
                });
            })
        }
        function selectedAdditionalMenu (availableOpsiMenu) {
            $('.btn-item-type').each( function (key, element) {
                var condition = availableOpsiMenu.item_type && ($(element).data('id') == availableOpsiMenu.item_type.id)
                if (condition) {
                    $(element).children('.item-check').html('&check;')
                }
            })
            $('.btn-item-size').each( function (key, element) {
                var condition = availableOpsiMenu.item_size && ($(element).data('id') == availableOpsiMenu.item_size.id)
                if (condition) {
                    $(element).children('.item-check').html('&check;')
                }
            })
            $('.additional_menu_item').each( function (key, element) {
                availableOpsiMenu.additional_menu.forEach( function (value) {
                    if (value.id == element.value) {
                        $(element).attr('checked', true)
                    }
                })
            })
            return availableOpsiMenu
        }
        function turnOffSelectAdditional () {
            $('.btn-item-type, .btn-item-size').off('click')
            $('.additional_menu_item').off('change')
        }
        
        function additionalMenuTemplate(text, harga) {
            var additionalMenuTemplate = `
                <button type="button" class="btn btn-outline-primary w-auto mb-2" disabled>
                    ${text} <span class="badge badge-info">+ Rp. ${harga}</span>
                </button>
            `
            return additionalMenuTemplate
        }
        function saveMenuPaket (createData, opsi_menu, event) {
            const menu_exists = createData.opsi_menu.filter(function (item) {
                return item.menuid == opsi_menu.menuid
            })
            if (menu_exists.length > 0) opsi_menu.menuid = `${opsi_menu.id}_${menu_exists.length}`
            $(event).next().children('.additional_menu_list').html('')
            var additionalText = ''
            opsi_menu.additional_menu.forEach( function (value) {
                $(event).next().children('.additional_menu_list').append(additionalMenuTemplate(value.text, value.harga))
                additionalText = `${additionalText} <span class="badge badge-info">${value.text}</span>`
            })
            $('#additionalMenuPaket').modal('hide')
            $('#additionalMenuPaketBody').html('')
            var text = $(event).data('text')
            // $(event).html(text)
            // if (opsi_menu.item_type) $(event).html(opsi_menu.item_type.text)
            if (opsi_menu.item_type) text = opsi_menu.item_type.text
            var menu_terpilih = `
                <tr class="menu_terpilih" data-opsimenu='${JSON.stringify(opsi_menu)}'>
                    <td>
                        <span class="badge badge-secondary">
                            <button type="button" class="close remove-diskon btn-xs" aria-label="Close" onClick="removeMenu(this, ${opsi_menu.menuid}, ${opsi_menu.id_kategori})">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </span>
                        ${text}
                    </td>
                    <td>${additionalText}</td>
                </tr>
            `
            $(`tr.tr_daftar_menu_terpilih_${opsi_menu.id_kategori}`).remove()
            $(`.daftar_menu_terpilih_${opsi_menu.id_kategori}`).append(menu_terpilih)
            createData.opsi_menu.push(opsi_menu)
            return createData
        }
        function removeMenu(event, menuid, id_kategori) {
            $(event).parents('.menu_terpilih').remove()
            var menu_terpilih = $(`.daftar_menu_terpilih_${id_kategori}`).children('.menu_terpilih')
            if (menu_terpilih.length == 0) {
                $(`.daftar_menu_terpilih_${id_kategori}`).append(`
                <tr class="tr_daftar_menu_terpilih_${ id_kategori }">
                        <td colspan="2">Belum ada menu yang dipilih</td>
                    </tr>
                `)
            }
        }
        var prevMenuPaket = 0
        function openAdditionalMenuPaket (event) {
            var dataType = $(event).data('type')
            var dataKategori = $(event).data('kategori')
            var jumlahMenu = $(event).data('jumlah_menu')
            var jumlahMenuTerpilih = $(`.daftar_menu_terpilih_${dataKategori}`).children('.menu_terpilih')
            console.log(jumlahMenuTerpilih.length, jumlahMenu)
            if (jumlahMenuTerpilih.length >= jumlahMenu) {
                sweetAlert(
                    '',
                    `Jumlah Maksimal Menu Yang Dapat Dipilih ${jumlahMenu}`,
                    'warning'
                )
                return
            }
            var dataId = $(event).data('dataid')
            var menuid = $(event).data('menuid')
            var availableOpsiMenu = createData.opsi_menu.filter(function (opsi_menu) {
                return opsi_menu.menuid == menuid
            })
            var condition = false
            if (prevMenuPaket == dataId) condition = true
            prevMenuPaket = dataId
            var opsi_menu = {
                id: dataId,
                menuid: menuid,
                nama_item: $(event).data('text'),
                id_item: dataId,
                id_kategori: $(event).data('kategori'),
                item_type: null,
                item_size: null,
                additional_menu: []
            }
            $('#additionalMenuPaketBody').html(loadingAdditionalMenu)
            ajax(dataType, dataKategori, dataId, 1).then(function (response) {
                $('#additionalMenuPaketBody').html(response)
                if (availableOpsiMenu.length > 0) opsi_menu = selectedAdditionalMenu(availableOpsiMenu[0])
                opsi_menu = selectAdditional(opsi_menu)
            })
            $('#saveMenuPaket').on('click', function (element) {
                var buttonTypes = $('button.btn-item-type')
                if (buttonTypes.length > 0) {
                    if (!opsi_menu.item_type) {
                        sweetAlert(
                            '',
                            `Silahkan pilih menu type terlebih dahulu`,
                            'warning'
                        )
                        return;
                    }
                }
                $(event).data('selected_menu_type', 0)
                if (opsi_menu.item_type) $(event).data('selected_menu_type', 1)
                createData = saveMenuPaket(createData, opsi_menu, event)
            })
            $('#additionalMenuPaket').on('hidden.bs.modal', function (event) {
                $('#additionalMenuPaketBody').html('')
                $('#saveMenuPaket').off('click')
                turnOffSelectAdditional()
            })
            $('#additionalMenuPaket').modal()
        }

        function saveMenu(event) {
            var dataId = $(event).data('dataid')
            var type = $(event).data('type')
            var inputAdditionalMenu = []
            var additionalMenuItem = $('.additional-menu-form').serializeArray()
            if (type === 'item') {
                for (let index = 0; index < additionalMenuItem.length; index++) {
                    const element = additionalMenuItem[index];
                    inputAdditionalMenu.push(element.value)
                }
            } else {
                for (let index = 0; index < additionalMenuItem.length; index++) {
                    const element = additionalMenuItem[index];
                }
            }
            add(dataId, type, additionalMenuItem)
            $('#additionalMenu, #menuModal').modal('hide')
        }
        var selectedDiskonValue = 0
        $('.btn-add-diskon').on('click', function (event) {
            var voucherId = $(this).data('dataid')
            var voucherValue = $(this).data('value')
            if (selectedDiskonValue != voucherValue) {
                selectedDiskonValue = voucherValue
                var diskonValue = new Intl.NumberFormat('id-ID').format(voucherValue)
                $('td.diskon').html(`
                    <input type="hidden" name="diskon_value" value="${voucherId}" />
                    <h6>
                      <span class="diskon_value">${diskonValue}</span>
                      <span class="badge badge-secondary">
                        <button type="button" class="close remove-diskon" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </span>
                    </h6>
                `)
                var subtotal = Number(tanpaTitik($('td.total-harga').html()))
                var total = subtotal - voucherValue
                $('td.total-harga').html(new Intl.NumberFormat('id-ID').format(total))
                uangKembalian()
                splitUangKembali()
                $(buttonPembayaran).trigger('click')
                $('.btn-print-resi').data('id-diskon', voucherId)
                $('button.remove-diskon').on('click', function (event) {
                    removeDiskon()
                })
            }
        })
        function removeDiskon () {
            selectedDiskonValue = 0
            var diskon = Number(tanpaTitik($('span.diskon_value').html()));
            var total = Number(tanpaTitik($('td.total-harga').html())) + diskon
            $('td.diskon').html('-');
            $('td.total-harga').html(new Intl.NumberFormat('id-ID').format(total))
            uangKembalian()
            splitUangKembali()
            $(buttonPembayaran).trigger('click')
            $('.btn-print-resi').removeData('id-diskon')
        }
        var xhr
        function reduceQty(event, id) {
            var qty = Number($(event).siblings('.invoice-qty').text());
            if (qty > 1) {
                if (xhr) xhr.abort()
                qty -= 1
                $(event).siblings('.invoice-qty').html(qty)
                updateQty(qty, id)
            }
        }
        function addQty(event, id) {
            if (xhr) xhr.abort()
            var qty = Number($(event).siblings('.invoice-qty').text());
            qty += 1
            $(event).siblings('.invoice-qty').html(qty)
            updateQty(qty, id)
        }
        function updateQty(qty, id) {
            xhr = $.ajax({
                url: '{{ url("pembayaran/edit/") }}' + `/${id}`,
                method: 'PATCH',
                data: {
                    _token: "{{ csrf_token() }}",
                    qty: qty
                },
                success: function (success) {
                    loadInvoice()
                },
                error: function (error) {

                }
            })
        }
        function deleteMenu(event) {
            var id = $(event).data('id')
            $.ajax({
                url: '{{ url("pembayaran") }}'+"/"+id,
                method: 'DELETE',
                data: {
                    "_token": "{{ csrf_token() }}",
                    },
                success: function (response) {
                    console.log("Deleted");
                    id = 0
                    loadInvoice();
                }
            });
        }
    </script>
@endsection
