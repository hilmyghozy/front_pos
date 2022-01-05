<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class LokasiController extends Controller
{
    public function data(){
        $lokasi = DB::table('pos_store')->get();
        // return $lokasi;
        foreach ($lokasi as $key => $value) {
            ?>
                <button type="button" class="btn-style btn-produk datalokasi" value="<?=$value->id_store?>"><?=$value->nama_store?></button>
            <?php
        }
    }

    public function set_session(Request $request){
        session(['id_store' => $request->id_store]);
        session(['nama_store' => $request->nama_store]);
        $data = $request->session()->all();
        return($data);
        // return view('depo');
    }

    public function depo(){
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('Y-m-d');
        $id = session('id');
        $id_store = session('id_store');
        $data_depo = DB::table('pos_deposit')->where('id_kasir',$id)->where('id_store',$id_store)->where('tanggal',$tanggal)->where('status',1)->count();
        
        if($data_depo>0){
            return redirect('dashboard');
        }else{
            if(session('role')=="waiters"){
                DB::table('pos_deposit')->insert([
                    'id_store' => session('id_store'), 
                    'id_kasir' => session('id'), 
                    'tanggal' => date('Y-m-d'), 
                    'deposit' => 0,
                    'status' => 1
                ]);
                
                return redirect('dashboard');
            }else{
                return view('depo');
            }
        }
    }
}
