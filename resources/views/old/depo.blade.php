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
</style>

@section('content')
<div class="card">
    <div class="card-header text-center">
        <div class="col-sm-12"><h1>Set Deposit</h1></div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{url('pembayaran/set_deposit')}}" >
            @csrf
            <div class="card-body" id="input-depo">
                <div class="form-group row">
                    <label for="100k" class="col-sm-5 col-form-label-sm lb-lg">100.000 x </label>
                    <div class="col-sm-7">
                        <div class="input-group">
                            <input type="number" class="form-control jml-depo text-right" id="100k" name="k100" min="0" placeholder="0" value="0" >
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary " id="btn_100_min" type="button">-</button>
                                <button class="btn btn-outline-secondary " id="btn_100_plus" type="button">+</button>
                            </div>
                        </div>
                    </div>   
                </div>
                <div class="form-group row">
                    <label for="50k" class="col-sm-5 col-form-label-sm lb-lg">50.000 x </label>
                    <div class="col-sm-7">
                        <div class="input-group">
                            <input type="number" class="form-control jml-depo text-right" id="50k" name="k50" min="0" placeholder="0" value="0" >
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary " id="btn_50_min" type="button">-</button>
                                <button class="btn btn-outline-secondary " id="btn_50_plus" type="button">+</button>
                                </div>
                        </div>
                    </div>   
                </div>
                <div class="form-group row">
                    <label for="20k" class="col-sm-5 col-form-label-sm lb-lg">20.000 x </label>
                    <div class="col-sm-7">
                        <div class="input-group">
                            <input type="number" class="form-control jml-depo text-right" id="20k" name="k20" min="0" placeholder="0" value="0"  >
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary " id="btn_20_min" type="button">-</button>
                                <button class="btn btn-outline-secondary " id="btn_20_plus" type="button">+</button>
                                </div>
                        </div>
                    </div>   
                </div>
                <div class="form-group row">
                    <label for="10k" class="col-sm-5 col-form-label-sm lb-lg">10.000 x </label>
                    <div class="col-sm-7">
                        <div class="input-group">
                            <input type="number" class="form-control jml-depo text-right" id="10k" name="k10" min="0"  placeholder="0" value="0"  >
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary " id="btn_10_min" type="button">-</button>
                                <button class="btn btn-outline-secondary " id="btn_10_plus" type="button">+</button>
                                </div>
                        </div>
                    </div>   
                </div>
                <div class="form-group row">
                    <label for="5k" class="col-sm-5 col-form-label-sm lb-lg">5.000 x </label>
                    <div class="col-sm-7">
                        <div class="input-group">
                            <input type="number" class="form-control jml-depo text-right" id="5k" name="k5" min="0"  placeholder="0" value="0"  >
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary " id="btn_5_min" type="button">-</button>
                                <button class="btn btn-outline-secondary " id="btn_5_plus" type="button">+</button>
                                </div>
                        </div>
                    </div>   
                </div>
                <div class="form-group row">
                    <label for="2k" class="col-sm-5 col-form-label-sm lb-lg">2.000 x </label>
                    <div class="col-sm-7">
                        <div class="input-group">
                            <input type="number" class="form-control jml-depo text-right" id="2k" name="k2" min="0" placeholder="0" value="0"  >
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary " id="btn_2_min" type="button">-</button>
                                <button class="btn btn-outline-secondary " id="btn_2_plus" type="button">+</button>
                                </div>
                        </div>
                    </div>   
                </div>
                <div class="form-group row">
                    <label for="1k" class="col-sm-5 col-form-label-sm lb-lg">1.000 x </label>
                    <div class="col-sm-7">
                        <div class="input-group">
                            <input type="number" class="form-control jml-depo text-right" id="1k" name="k1" min="0"  placeholder="0" value="0"  >
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary " id="btn_1_min" type="button">-</button>
                                <button class="btn btn-outline-secondary " id="btn_1_plus" type="button">+</button>
                                </div>
                        </div>
                    </div>   
                </div>
                <div class="form-group row">
                    <label for="total" class="col-sm-5 col-form-label-lg lb-lg">Total (Rp.)</label>
                    <div class="col-sm-7 ">
                        <input type="text" readonly class="form-control-plaintext text-right lb-lg" id="depo-total" name="total" placeholder="0" value="0">
                    </div>
                </div>
                <button type="submit" class="btn btn-success btn-lg my-1  text-uppercase" id="depo-validasi">Validasi</button>
                <button type="button" class="btn btn-secondary btn-lg mt-1  mb-3 text-uppercase" id="depo-cancel" data-href="{{URL::to('/lokasi')}}">Batal</button>
            </div>              
        </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    //btn plus minus 100k
    
    $(document).on("click","#btn_100_plus",function(){
        var now = parseInt($('#100k').val());
        var num = now+1;
        $('#100k').val(num);
    });
    $(document).on("click","#btn_100_min",function(){
        var now = parseInt($('#100k').val());
        if(now=='0'){
            $('#100k').val(0);
        }else{
            var num = now-1;
            $('#100k').val(num);
        }
    });
    $(document).on("click","#btn_50_plus",function(){
        var now = parseInt($('#50k').val());
        var num = now+1;
        $('#50k').val(num);
    });
    $(document).on("click","#btn_50_min",function(){
        var now = parseInt($('#50k').val());
        if(now=='0'){
            $('#50k').val(0);
        }else{
            var num = now-1;
            $('#50k').val(num);
        }
    });
    $(document).on("click","#btn_20_plus",function(){
        var now = parseInt($('#20k').val());
        var num = now+1;
        $('#20k').val(num);
    });
    $(document).on("click","#btn_20_min",function(){
        var now = parseInt($('#20k').val());
        if(now=='0'){
            $('#20k').val(0);
        }else{
            var num = now-1;
            $('#20k').val(num);
        }
    });
    
    $(document).on("click","#btn_10_plus",function(){
        var now = parseInt($('#10k').val());
        var num = now+1;
        $('#10k').val(num);
    });
    $(document).on("click","#btn_10_min",function(){
        var now = parseInt($('#10k').val());
        if(now=='0'){
            $('#10k').val(0);
        }else{
            var num = now-1;
            $('#10k').val(num);
        }
    });
    $(document).on("click","#btn_5_plus",function(){
        var now = parseInt($('#5k').val());
        var num = now+1;
        $('#5k').val(num);
    });
    $(document).on("click","#btn_5_min",function(){
        var now = parseInt($('#5k').val());
        if(now=='0'){
            $('#5k').val(0);
        }else{
            var num = now-1;
            $('#5k').val(num);
        }
    });
    $(document).on("click","#btn_2_plus",function(){
        var now = parseInt($('#2k').val());
        var num = now+1;
        $('#2k').val(num);
    });
    $(document).on("click","#btn_2_min",function(){
        var now = parseInt($('#2k').val());
        if(now=='0'){
            $('#2k').val(0);
        }else{
            var num = now-1;
            $('#2k').val(num);
        }
    });
    $(document).on("click","#btn_1_plus",function(){
        var now = parseInt($('#1k').val());
        var num = now+1;
        $('#1k').val(num);
    });
    $(document).on("click","#btn_1_min",function(){
        var now = parseInt($('#1k').val());
        if(now=='0'){
            $('#1k').val(0);
        }else{
            var num = now-1;
            $('#1k').val(num);
        }
    });

    $('.input-group').on('input','.jml-depo',function(){
        hitung_total();
    });

    $(document).on("click",".btn-outline-secondary",function(){
        hitung_total();
    });

    function hitung_total(){
        var total_depo = 0;
        var total_depo_float = 0;
        $('.form-group .jml-depo').each(function(){
            var n100 = $('#100k').val();
            var n50 = $('#50k').val();
            var n20 = $('#20k').val();
            var n10 = $('#10k').val();
            var n5 = $('#5k').val();
            var n2 = $('#2k').val();
            var n1 = $('#1k').val();
            total_depo = (n100*100000)+(n50*50000)+(n20*20000)+(n10*10000)+(n5*5000)+(n2*2000)+(n1*1000);
        });
        total_depo_float = parseFloat(total_depo);
        $('#depo-total').val(format(total_depo));
    }

    // $(document).on("click","#depo-validasi",function(){
    //     window.location.href = $(this).data('href');
    // })

    $(document).on("click","#depo-cancel",function(){
        window.location.href = $(this).data('href');
    })

    function format(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }


</script>
@endsection