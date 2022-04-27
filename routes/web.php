<?php

use App\Http\Controllers\LokasiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Login
Route::get('/','Auth\AuthController@index');
Route::post('login','Auth\AuthController@login');


Route::group(['middleware'=>'is_login_middleware'],function(){
    Route::get('dashboard', 'TiketController@index');
    Route::get('depo', 'LokasiController@depo');
    Route::get('close', 'PembayaranController@close');
    Route::get('lokasi',function() {return view('lokasi');});
    Route::get('pembayaran','PembayaranController@data');
    Route::post('pembayaran/set_deposit','PembayaranController@post_pos_depo');
    Route::post('pembayaran/print_deposit','PembayaranController@print_deposit');
    Route::get('pembayaran/print_close_pos','PembayaranController@print_close_pos');
    Route::get('pembayaran/print_close_kitchen','PembayaranController@print_close_kitchen');
    Route::post('pembayaran/create','PembayaranController@create');
    Route::post('pembayaran/diskon','PembayaranController@diskon');
    Route::post('pembayaran/edit_diskon','PembayaranController@edit_diskon');
    Route::get('pembayaran/test_print','PembayaranController@test_print');
    Route::get('pembayaran/load_diskon','PembayaranController@load_diskon');
    Route::get('pembayaran/load_detail_paket','PembayaranController@load_detail_paket');

    
    Route::get('pembayaran/del_diskon','PembayaranController@del_diskon');
    Route::post('pembayaran/activity','PembayaranController@activity');
    Route::post('pembayaran/order','PembayaranController@order');
    Route::post('pembayaran/edit_order','PembayaranController@edit_order');
    Route::post('pembayaran/edit_note','PembayaranController@edit_note');
    Route::get('pembayaran/get_note/{id}','PembayaranController@get_note');
    Route::post('pembayaran/edit_item_order','PembayaranController@edit_item_order');
    Route::post('pembayaran/add_edit_item_order','PembayaranController@add_edit_item_order');
    Route::get('pembayaran/belum_bayar','PembayaranController@belum_bayar');
    Route::get('pembayaran/detail_belum_bayar/{kode_temp}','PembayaranController@detail_belum_bayar');
    Route::get('pembayaran/detail_belum_bayar_edit/{kode_temp}','PembayaranController@detail_belum_bayar_edit');
    Route::get('pembayaran/detail_belum_bayar_editItem/{kode_temp}','PembayaranController@detail_belum_bayar_editItem');
    Route::get('pembayaran/add_revisi_order/{kode_temp}','PembayaranController@add_revisi_order');
    Route::get('pembayaran/del_revisi_order/{id}','PembayaranController@del_revisi_order');
    Route::get('pembayaran/del_order/{kode_temp}','PembayaranController@del_order');
    Route::get('pembayaran/add_edit_order/','PembayaranController@add_edit_order');
    Route::get('tiket/{id}','TiketController@data');
    Route::get('meja/data_meja','TiketController@data_meja');
    Route::post('tiket/harga','TiketController@harga');
    Route::delete('pembayaran/{id}', 'PembayaranController@destroy');
    Route::patch('pembayaran/edit/{id}', 'PembayaranController@update');
    Route::get('pembayaran/total','PembayaranController@total_pembayaran');   
    Route::get('pembayaran/subtotal','PembayaranController@subtotal_pembayaran');
    Route::get('pembayaran/thirdparty','PembayaranController@thirdparty_pembayaran');
    Route::get('pembayaran/print', 'PembayaranController@print');  
    Route::get('pembayaran/print_con', 'PembayaranController@print_con');  
    Route::get('pembayaran/print_resi/{no}', 'PembayaranController@print_resi');  
    Route::get('lokasidata','LokasiController@data');
    Route::get('logout','Auth\AuthController@logout');
    Route::get('lock','Auth\AuthController@lock');
    Route::post('open_lock','Auth\AuthController@open_lock');
    Route::get('set_session','LokasiController@set_session');

    Route::get('pembayaran/test_printer1','PembayaranController@test_printer1');
    Route::get('pembayaran/test_print','PembayaranController@test_print');

    Route::get('additional-menu', 'AdditionalMenuController@getAdditionalMenu');
});




