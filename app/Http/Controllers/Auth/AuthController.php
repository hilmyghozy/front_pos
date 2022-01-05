<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function index(){
        if(session('nama')){
            return redirect('/dashboard'); 
        }
        return view('login');
    }

    public function login(Request $request){
        $kasir = DB::table('pos_kasir')
                ->where('username', $request->username)
                ->where('password', $request->password)
                ->count();

        if($kasir>0){
            $datakasir = DB::table('pos_kasir')
                ->where('username', $request->username)
                ->where('password', $request->password)
                ->get();
            // return redirect('/lokasi');
            // session('id_kasir', );
            foreach($datakasir as $value){
                session(['username' => $value->username, 'id'=> $value->id, 
                'nama'=> $value->nama, 'role'=>$value->role,'is_login'=>true]);
            }
            return redirect('/lokasi');
        }else{
            return redirect('/')->with('message','Username atau Password Salah !');
        }
        // dd ($request->all());
        // $data = User::where('username',$request->username)->firstOrFail();
        // if($data){
        //     if($request->password==$data->password){
        //         return redirect('/lokasi');
        //     }
        //     else{
        //         return redirect('/')->with('message','Username atau Password Salah !');
        //     }
        // }
    }

    public function logout(Request $request){
        DB::table('pos_deposit')->where('id_store', session('id_store'))->where('id_kasir', session('id'))->update(['status' => 0]);
        $request->session()->flush();
        DB::table('pos_front_payment')->where('id_store', session('id_store'))->where('id_kasir', session('id'))->delete();
        return redirect('/');
    }

    public function lock(Request $request){
        $username = session('username');
        $id_store = session('id_store');
        $nama_store = session('nama_store');
        $role = session('role');
        // DB::table('pos_front_payment')->truncate();
        // $request->session()->flush();
        session(['username' => $username, 'id_store'=> $id_store, 'nama_store'=> $nama_store, 'role'=> $role,'is_login'=>true]);
        return view('/lockscreen', compact('username','id_store','nama_store','role'));
    }

    public function open_lock(Request $request){
        // dd ($request->all());

        $kasir = DB::table('pos_kasir')
                ->where('username', session('username'))
                ->where('password', $request->password)
                ->count();

        // dd($request->all());

        if($kasir>0){
            $datakasir = DB::table('pos_kasir')
                ->where('username', session('username'))
                ->where('password', $request->password)
                ->get();
            // return redirect('/lokasi');
            // session('id_kasir', );
            foreach($datakasir as $value){
                if(session('id_store')){
                    session(['username' => $value->username, 'id'=> $value->id, 
                    'nama'=> $value->nama, 'role'=> $value->role, 'id_store'=>session('id_store'), 
                    'nama_store'=>session('nama_store'), 'is_login'=>true]);
                    return redirect('/dashboard');
                }else{
                    session(['username' => $value->username, 'id'=> $value->id, 
                    'nama'=> $value->nama, 'role'=> $value->role, 'is_login'=>true]);
                    return redirect('/lokasi');
                }
                    
            }
        }else{
            return redirect('/lock')->with('message','Password Salah !');
        }
    }
}
