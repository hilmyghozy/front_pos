<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TiketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function data($id = null){
        $tiket = DB::table('pos_product_item')->where('id_kategori', 1)->get();
        // return $tiket;
        foreach ($tiket as $key => $value) {
            ?>
                <button type="button" class="btn-style btn-produk datatiket" id="tiket-id-<?=$value->id_item?>" data-dataid="<?=$value->id_item?>" value="<?=$value->nama_item?>"><?=$value->nama_item?><br><?=$value->harga_jual?><br><?=$value->thirdparty?><br><?=$value->pajak?></button>
            <?php
        }
    }

    public function data_meja(){
        $meja = DB::table('pos_meja')->where('id_store', session('id_store'))->get();
        // return $tiket;
        foreach ($meja as $key => $value) {
            ?>
                <div class="col-md-4">
                    <button class="btn-style <?=$value->status!=1?'btn-green':'btn-red'?>" onclick="set_meja(<?=$value->id?>)" id="meja<?=$value->id?>" <?=$value->status!=1?'':'disabled'?> ><?=$value->nama_meja?></button>
                </div>
            <?php
        }
    }

    public function harga(Request $request){
        $harga = DB::table('pos_product_item')->where('nama_item',$request->nama)->first()->harga_jual;
        return json_encode($harga);
    }

    public function index()
    {
        $id_store = session('id_store');
        // return $id_store;
        // dd(@session('id_store'));
        $metode = DB::table('pos_payment')->get();

        $bar = 0;
        $pizza = 100;
        $pasta = 200;
        $paket = 300;

        $item = DB::table('pos_product_item')
            ->join('pos_product_kategori', 'pos_product_item.id_kategori', '=', 'pos_product_kategori.id_kategori')
            ->select('pos_product_item.*', 'pos_product_kategori.nama_kategori', 'pos_product_kategori.is_paket')    
            ->where('id_store', $id_store)
            ->orderBy('id_kategori', 'asc')
            ->orderBy('nama_item', 'asc')
            ->get();
        foreach ($item as $key => $map) {
            switch ($map->id_kategori) {
                case 18:
                    $item[$key]->nomor = $bar;
                    $bar += 1;
                    break;
                case 19:
                    $item[$key]->nomor = $pizza;
                    $pizza += 1;
                    break;
                case 20:
                    $item[$key]->nomor = $pasta;
                    $pasta += 1;
                    break;
                case 21:
                    $item[$key]->nomor = $paket;
                    $paket += 1;
                    break;
            }
        }
        $n = 0;
        $start = 0;
        // $array=[];
        $array[$n] = array('id_kategori' => null, 'nama_kategori'=>null);
        foreach($item as $key=>$value){
            // return $value->nama_kategori;
            if($array[$n]['id_kategori']==null){
                $start = $value->id_kategori;
                $array[$n] = array('id_kategori'=>$value->id_kategori, 'nama_kategori'=>$value->nama_kategori);
            }elseif($value->id_kategori != $array[$n]['id_kategori']){
                array_push($array, array('id_kategori'=>$value->id_kategori, 'nama_kategori'=>$value->nama_kategori));
                $n++;
            }
        }

        $voucher = DB::table('pos_diskon')
                ->where('id_store', $id_store)
                ->where('nominal', '>', 0)
                ->get();

        $paket = DB::table('pos_product_paket')
                ->where('id_store', $id_store)
                ->get();
        // dd(session()->all());
        return view('welcome', compact ('item', 'array', 'start','metode', 'voucher','paket'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
