<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdditionalMenuController extends Controller
{
    public function getAdditionalMenu(Request $request)
    {
        $id_store = session('id_store');
        $type = $request->input('type');
        $id_kategori = $request->input('id_kategori');
        $id = $request->input('id');
        $paket = $request->input('paket');
        $id_paket = $request->input('id_paket');
        if ($type === 'item') {
            if ($paket) {
                $item_types = DB::table('pos_product_item_menu_type')
                    ->join('pos_product_item', 'pos_product_item.id_item', '=', 'pos_product_item_menu_type.id_item_paket')
                    ->join('pos_product_item_type', 'pos_product_item_type.id_type', '=', 'pos_product_item_menu_type.id_item_type')
                    ->select('pos_product_item_menu_type.*', 'pos_product_item.nama_item', 'pos_product_item_type.*')
                    ->where('pos_product_item_menu_type.id_item', $id_paket)
                    ->where('pos_product_item_menu_type.id_item_paket', $id)
                    ->get();
            } else {
                $item_types = DB::table('pos_product_item_type')
                    ->join('pos_product_item', 'pos_product_item.id_item', '=', 'pos_product_item_type.id_item')
                    ->where('pos_product_item_type.id_item', $id)
                    ->where('pos_product_item_type.id_item_type', '!=', NULL)
                    ->select('pos_product_item.nama_item', 'pos_product_item_type.*')
                    ->get();
            }
            $item_types = $item_types->map(function ($item) {
                    $item->nama_type = $item->nama_item . ' (' . $item->nama_type . ')';
                    return $item;
                });
            // $item_sizes = DB::table('pos_product_item_size')->where('id_item', $id)->get();
            $item_sizes = [];
            $additional_menu = $this->additionalMenuItem($id_store, $id_kategori);
            $view = view('_partials/additional-menu-item', compact('additional_menu', 'item_types', 'item_sizes', 'paket'));
        } else {
            $opsi_menu = [];
            $item = DB::table('pos_product_item')->where('id_item', $id)->first();
            if ($item) {
                $product_opsi_menu = DB::table('pos_product_item_menu')
                    ->join('pos_product_item', 'pos_product_item.id_item', '=', 'pos_product_item_menu.id_item_paket')
                    // ->select('pos_product_item_menu.jumlah', 'pos_product_item_menu.id_item_paket as id_item', 'pos_product_item.nama_item')
                    ->where('pos_product_item_menu.id_item', $id)
                    ->get();
                $item = 0;
                while ($item < count($product_opsi_menu)) {
                    $menu = $product_opsi_menu[$item];
                    $product_item = DB::table('pos_product_item')->where('id_item', $menu->id_item)->first();
                    if ($product_item) {
                        $additional_menu = $this->additionalMenuItem($id_store, $product_item->id_kategori);
                        $menu->additional_menu = $additional_menu;
                        $menu_type = DB::table('pos_product_item_menu_type')->where('id_pos_product_item_opsi_menu', $menu->id)->count();
                        $menu->menu_type = $menu_type;
                        array_push($opsi_menu, $menu);
                    }
                    $item += 1;
                }
            }
            $view = view('_partials/additional-menu-packet', compact('opsi_menu'));
        }
        return $view;
    }

    public function additionalMenuItem($id_store, $id_kategori)
    {
        $additional_menu = DB::table('pos_product_kategori_additional')
            ->where('id_kategori', $id_kategori)
            ->where('id_store', $id_store)->get();
        return $additional_menu;
    }
}