@extends('layout_noside')

@section('page title','Lock')

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
    .lock{
        font-size: 1.5rem !important;
    }
</style>

@section('content')
    <div class="card">
        <div class="card-header text-center">
            <div class="col-sm-12"><h1><i class="fas fa-lock lock"></i> Locked</h1></div>
        </div>
    </div>
        <div class="card pb-0 mb-0">
            <div class="card-body">
                <div class="row  mt-4">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-6">
                        <div class="text-center lock pb-3">
                            {{-- <i class="fas fa-lock lock"></i> Locked --}}
                        </div>
                            <form action="{{url('open_lock')}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="form-group">
                                    <input type="password" class="form-control" name="password" placeholder="Password" required autofocus>
                                </div>

                                @if (session('message'))
                                    <div class="alert alert-danger alert-dismissible fade show d-flex" role="alert">
                                        <div class="col-md-10">
                                            {{session('message')}}
                                        </div>
                                        <div class="col-md-3 ">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-top:-15px">
                                            <span aria-hidden="true">&times;</span>
                                        </div>
                                        
                                    </div>
                                @endif

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-7"></div>
                                        <div class="col-sm-5">
                                            <button type="submit" class="btn btn-success">UNLOCK</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                    </div>
                    <div class="col-sm-3"></div>
                </div>
            </div>
        </div>
@endsection

@section('script')
<script type="text/javascript">
    


</script>
@endsection