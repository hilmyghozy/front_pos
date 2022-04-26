@extends('layout_noside')

@section('page title','Lokasi')

<style>
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
    -webkit-appearance: none; 
    margin: 0; 
    }

    .lb-lg{
        font-size: 20px !important;
    }
    .card-header{
        text-align: center !important;
        color: #16986E !important;
    }
</style>

@section('content')
<div class="card">
    <div class="card-header text-center">
        <div class="col-sm-12"><h1>Pilih Store</h1></div>
    </div>
</div>
<div class="card" >
       
                    {{-- {{session()->get('id')}} --}}
    <div class="card-body row lokasi" id="lokasi">
        {{-- <button type="button" class="btn-style btn-produk" value="Normal Ticket">Normal Ticket <br> 150.000 </button>
        <button type="button" class="btn-style btn-produk" value="Manula & Difable">Manula & difable <br> 75.000 </button>
        <button type="button" class="btn-style btn-produk" value="Aparat">Aparat <br> 120.000 </button> --}}
        
    </div>
    <div class="card-footer lokasi">
        <button class="btn-style btn-produk h-25 btn-next-lokasi">Next &nbsp; <i class="fas fa-chevron-right"></i></button>
        <input type="hidden" id="id_store">
        <input type="hidden" id="nama_store">
    </div>
              
</div>
@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function (){ 
            $('#lokasi').load('{{ url("lokasidata") }}');
    });

    $(document).on("click", "button.datalokasi", function(){
        var id_store = $(this).attr("value");
        var nama_store = $(this).text();
        $('#id_store').val(id_store);
        $('#nama_store').val(nama_store);
        // alert(nama_store)
           
    });
    $(document).on("click", ".lokasi .btn-next-lokasi", function(){
        // var id_store = $(this).attr("value");
        // var nama_store = $(this).text();
        // alert('lokasi');

        var id_store = $('#id_store').val();
        var nama_store = $('#nama_store').val();

        $.ajax({
            url: '{{ url("set_session") }}',
            dataType : 'json',
            type: 'get',
            data: { 
                "_token": "{{ csrf_token() }}",
                "id_store": id_store,
                "nama_store": nama_store
            },
                success:function(response) {
                    if($('#nama_store').val() == ''){
                        alert('Silahkan Pilih Store');
                    }else{
                        // alert('ada')
                        location.href = "{{url('depo')}}";
                    }
                    console.log(response);
                }
        });

        
    });
</script>
@endsection