@extends('layout_noside')

@section('page title','Depo')

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
        color: #69be47 !important;
    }
    td{
        padding-top: 8px;
    }
</style>

@section('content')
<div class="card">
    <div class="card-header text-center">
        <div class="col-sm-12"><h1>Close</h1></div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="card-body" id="input-depo">
            <div class="form-group row">
                <div class="col-sm-12">
                    <table class="col-md-8 mx-auto h5">
                        <tbody>
                            <tr><td><label for="">Item</label></td></tr>
                            @foreach ($dataitem as $item) 
                            <tr>
                                <td>{{$item->nama_item}} X {{$item->total_qty}}</td>
                                <td class="text-right">{{number_format($item->total_bayar,0,",",".")}}</td>
                            </tr>
                            @endforeach
                            <tr><td><label for=""></label></td></tr>
                            @foreach ($deposit as $item)   
                            <tr>
                                <td>Deposit</td>
                                <td class="text-right">{{number_format($item->deposit,0,",",".")}}</td>
                                @php
                                    $depo = $item->deposit;
                                @endphp
                            </tr>
                            @endforeach
                            @foreach ($tunai as $item)   
                            <tr>
                                <td>Tunai</td>
                                <td class="text-right">{{number_format($item->total_cash,0,",",".")}}</td>
                                @php
                                    $tot_cash = $item->total_cash;
                                @endphp
                            </tr>
                            @endforeach
                            <tr>
                                <td>Total Cash</td>
                                <td class="text-right">{{number_format(@$depo+$tot_cash,0,",",".")}}</td>
                            </tr>
                            @foreach ($debit as $item)   
                            <tr>
                                <td>{{$item->tipe_payment}}</td>
                                <td class="text-right">{{number_format($item->total_debit,0,",",".")}}</td>
                            </tr>
                            @endforeach
                            @foreach ($diskon as $item)   
                            <tr>
                                <td>Diskon</td>
                                <td class="text-right">{{number_format($item->total_diskon,0,",",".")}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>   
            </div>
            <div class="col-md-8 mx-auto row">
                <button type="button" class="btn btn-success btn-lg col-md-5 mr-5 lb-lg" id="close-print" data-href="{{URL::to('pembayaran/print_close_pos')}}">Print &nbsp;&nbsp;<i class="fas fa-print lb-lg"></i></button>
                <button type="button" class="btn btn-danger btn-lg col-md-5 ml-3 lb-lg" id="close-logout" data-href="{{URL::to('/logout')}}">LogOut &nbsp;&nbsp;<i class="fas fa-sign-out-alt lb-lg"></i></button>           
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    $(document).on("click","#close-print",function(){
        window.location.href = $(this).data('href');
    })

    $('#close-logout').on('click',function(e){
    // $(document).on("click","#close-logout",function(e){
        e.preventDefault();
        // alert('asd');
        swal({
            title: 'Are you sure?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Logout'
        }).then((result) => {
            if (result.dismiss !== 'cancel') {
                window.location.href = $(this).data('href');
            }
        })
    })

</script>
@endsection