<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\DoPrintDapurRevisiService;
use App\Http\Services\DoPrintDapurService;
use App\Http\Services\DoPrintDeleteOrderService;
use App\Http\Services\DoPrintNoteService;
use App\Http\Services\DoPrintPCService;
use App\Http\Services\DoPrintResiService;
use App\Http\Services\GetAdditionalTextService;
use App\Http\Services\GetDataPembayaranService;
use App\Http\Services\SetEditOrderOpsiMenu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;

class PembayaranController extends Controller
{
    public function load_detail_paket($id_paket)
    {
        $detailPaket = DB::table('pos_product_paket_detail')
            ->where('id_paket', $id_paket)
            ->get();

            

?>
            
        <div class="card-body">
            <ul class="nav nav-tabs" id="myTab5" role="tablist">
                <?php foreach($detailPaket as $detail){ ?>
                    <li class="nav-item">
                        <a class="nav-link {{$start==$detail['id_paket_detail'] ? 'active' : ''}}" id="tab-tab{{$detail['id_paket_detail']}}" data-toggle="tab" href="#tab{{$detail['id_paket_detail']}}" role="tab" aria-controls="tab-label{{$detail['id_paket_detail']}}" aria-selected="true">
                            <text>{{$detail['nama_paket_detail']}}</text>
                        </a>
                    </li>
                <?php } ?>
            </ul>
            <div class="tab-content" id="myTabContent5">
                
            <?php foreach($detailPaket as $detail){ ?>
            
                <div class="tab-pane fade show {{$start==$detail['id_paket_detail'] ? 'active' : ''}}" id="tab{{$detail['id_paket_detail']}}" role="tabpanel" aria-labelledby="tab-tab{{$detail['id_paket_detail']}}">
                        <div class="card">
                            <div class="card-body" id="jenis-ticket1">
                                
                            <table id="table_id" class="display">
                                <?php
                                    $no=1;
                                ?>
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>ID</th>
                                                <th>Nama</th>
                                                <th>Harga</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                @foreach ($item as $produk)
                                    @if ($produk->id_kategori == $kategori['id_kategori'])
                                            <tr>
                                                <td>{{ $no++}}</td>
                                                <td>{{$produk->id_item}}</td>
                                                <td>{{$produk->nama_item}}</td>
                                                <td>Rp. {{number_format($produk->harga_jual,0,',','.')}}</td>
                                                <td>
                                        <button type="button" class="btn-style datatiket btn-info" id="tiket-id-{{$produk->id_item}}" data-dataid="{{$produk->id_item}}" value="{{$produk->nama_item}}">Add</button>

                                                </td>
                                            </tr> 
                                    @endif
                                @endforeach
                                
                                </tbody>
                                    </table>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php
        
    }

    public function data()
    {
        $no = 1;
        $id_store = session('id_store');
        $pembayaran = DB::table('pos_front_payment')
            ->where('pos_front_payment.id_store', $id_store)
            ->where('pos_front_payment.id_kasir', session('id'))
            ->orderBy('pos_front_payment.id', 'desc')
            ->get();
        $data_pembayaran = new GetDataPembayaranService($pembayaran);
        $data_pembayaran->setData();
        $sub_total = $data_pembayaran->sub_total;
        $sub_total_thirdparty = $data_pembayaran->sub_total_thirdparty;
        $total = $data_pembayaran->total;
        $thirdparty = $data_pembayaran->thirdparty;
        $pajak = $data_pembayaran->pajak;
        $pajak_thirdparty = $data_pembayaran->pajak_thirdparty;
        $pembayaran = $data_pembayaran->data;

        $data = view('_partials.invoice', compact('pembayaran', 'sub_total', 'thirdparty', 'total', 'pajak_thirdparty', 'pajak', 'sub_total_thirdparty'));
        return $data;
    }

    public function old_data()
    {

        $no = 1;
        $id_store = session('id_store');
        $pembayaran = DB::table('pos_front_payment')
            ->where('id_store', $id_store)
            ->where('id_kasir', session('id'))
            ->orderBy('id', 'desc')
            ->get();
        foreach ($pembayaran as $key => $value) {
        ?>
            <li value="<?= $no ?>" id="sidemenu-<?= $value->id ?>" onclick="clicked(<?= $value->id ?>)" class="sidemenu">
                <table class="table-pembayaran">
                    <tr>
                        <td class="total-pesan">
                            <div class="jumlah-tiket"><span><?= ($value->qty == 0) ? 0 : $value->qty ?></span></div>
                        </td>
                        <td class="nama-barang"><b><?= $value->nama_tiket ?></b></td>
                        <td class="harga"><b>Rp <span><?= number_format(($value->total == 0) ? 0 : $value->total, 0, ",", ".") ?></span></b></td>
                    </tr>
                </table>
            </li>
        <?php
        }
    }

    public function belum_bayar()
    {

        $no = 1;
        $id_store = session('id_store');
        $pembayaran = DB::table('pos_belum')
            ->join('pos_kasir', 'pos_belum.id_kasir', '=', 'pos_kasir.id')
            ->select('pos_belum.*', 'pos_kasir.username')
            ->where('pos_belum.id_store', $id_store)
            ->get();

        foreach ($pembayaran as $key => $value) {
            $type_or = "";
            if ($value->type_order == 1) {
                $type_or = "Third Party";
            } elseif ($value->type_order == 2) {
                $type_or = "Take Away";
            } elseif ($value->type_order == 3) {
                $type_or = "Dine In";
            }
        ?>
            <tr>
                <td id="kode_temp<?= $value->id ?>"><?= $value->kode_temp ?></td>
                <td><?= $type_or ?></td>
                <td id="keterangan_order<?= $value->id ?>"><?= $value->keterangan_order ?></td>
                <td><?= $value->username ?></td>
                <td id="total_bayar<?= $value->id ?>"><?= number_format($value->total, 0, ',', '.') ?></td>
                <input type="hidden" id="id_waiters<?= $value->id ?>" value="<?= $value->total ?>">
                <input type="hidden" id="type_order<?= $value->id ?>" value="<?= $value->type_order ?>">
                <input type="hidden" id="subtotal<?= $value->id ?>" value="<?= $value->subtotal ?>">
                <input type="hidden" id="id_discount<?= $value->id ?>" value="<?= $value->id_discount ?>">
                <td class="text-center">
                    <?php if (session('role') == "cashier") { ?>
                        <button class="btn btn-info btn-sm col-2" onclick="open_note('<?= $value->id ?>')"><i class="far fa-sticky-note"></i></button>
                        <button class="btn btn-warning btn-sm col-2" onclick="edit_order('<?= $value->id ?>')"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-danger btn-sm col-2" onclick="del_order('<?= $value->kode_temp ?>')"><i class="fas fa-trash"></i></button>
                        <button class="btn btn-warning btn-sm col-2" onclick="pay_order(<?= $value->id ?>)"><i class="fas fa-money-bill-wave-alt"></i></button>
                    <?php
                    } else {
                        echo "#";
                    } ?>
                </td>
            </tr>
        <?php
        }
    }

    public function detail_belum_bayar($kode_temp)
    {

        $no = 1;
        $id_store = session('id_store');
        $pembayaran = DB::table('pos_belum')
            ->join('pos_kasir', 'pos_belum.id_kasir', '=', 'pos_kasir.id')
            ->select('pos_belum.*', 'pos_kasir.username')
            ->where('pos_belum.kode_temp', $kode_temp)
            ->first();

        $item_bayar = DB::table('pos_belum_bayar')
            ->join('pos_product_kategori', 'pos_product_kategori.id_kategori', '=', 'pos_belum_bayar.id_kategori')
            ->select('pos_belum_bayar.*', 'pos_product_kategori.is_paket')
            ->where('pos_belum_bayar.kode_temp', $kode_temp)
            ->get();

        $item_bayar = new GetDataPembayaranService($item_bayar);
        $item_bayar->setDataOrder();
        $sub_total = $item_bayar->sub_total;
        $qty_item = $item_bayar->qty;
        $type_or = "";
        if ($pembayaran->type_order == 1) {
            $type_or = "Third Party";
        } elseif ($pembayaran->type_order == 2) {
            $type_or = "Take Away";
        } elseif ($pembayaran->type_order == 3) {
            $type_or = "Dine In";
        }
        $view = view('_partials.detail_belum_bayar', compact('pembayaran', 'sub_total', 'qty_item', 'item_bayar', 'type_or', 'kode_temp'));
        return $view;
    }

    public function add_revisi_order($kode_temp)
    {
        $item_bayar = DB::table('pos_revisi_bayar')
            ->where('pos_revisi_bayar.id_store', session('id_store'))
            ->delete();

        $item_bayar = DB::table('pos_belum_bayar')
            ->where('pos_belum_bayar.kode_temp', $kode_temp)
            ->get();

        $data_pos_revisi_bayar = [];
        foreach ($item_bayar as $key => $value) {
            array_push($data_pos_revisi_bayar, [
                'id' => null,
                'id_pos_belum_bayar' => $value->id,
                'nama_item' => $value->nama_item,
                'id_item' => $value->id_item,
                'id_kategori' => $value->id_kategori,
                'id_store' => $value->id_store,
                'id_kasir' => $value->id_kasir,
                'harga' => $value->harga,
                'qty' => $value->qty,
                'total' => $value->total,
                'kode_temp' => $kode_temp,
                'additional_menu' => $value->additional_menu,
                'opsi_menu' => $value->opsi_menu,
                'item_type' => $value->item_type,
                'item_size' => $value->item_size,
                'pajak' => $value->pajak
            ]);
        }
        DB::table('pos_revisi_bayar')->insert($data_pos_revisi_bayar);
    }

    public function get_note($id)
    {
        $note = DB::table('pos_belum')
            ->where('id', $id)
            ->first()
            ->note;

        return json_encode($note);
    }

    public function del_revisi_order($id)
    {
        $item_bayar = DB::table('pos_revisi_bayar')
            ->where('pos_revisi_bayar.id', $id)
            ->first();
        if ($item_bayar) {
            DB::table('pos_revisi_bayar')
                ->where('pos_revisi_bayar.id', $id)
                ->delete();
            $item_bayar = new GetDataPembayaranService([$item_bayar]);
            $item_bayar->setDataOrder();
            $item_bayar = $item_bayar->data[0];
            return response()->json($item_bayar);
        }
    }

    public function detail_belum_bayar_edit($kode_temp)
    {

        $no = 1;
        $id_store = session('id_store');

        $pembayaran = DB::table('pos_belum')
            ->join('pos_kasir', 'pos_belum.id_kasir', '=', 'pos_kasir.id')
            ->select('pos_belum.*', 'pos_kasir.username')
            ->where('pos_belum.kode_temp', $kode_temp)
            ->first();

        $item_bayar = DB::table('pos_revisi_bayar')
            ->where('pos_revisi_bayar.kode_temp', $kode_temp)
            ->get();
        $item_bayar = new GetDataPembayaranService($item_bayar);
        $item_bayar->setDataOrder();
        $type_or = "";
        if ($pembayaran->type_order == 1) {
            $type_or = "Third Party";
        } elseif ($pembayaran->type_order == 2) {
            $type_or = "Take Away";
        } elseif ($pembayaran->type_order == 3) {
            $type_or = "Dine In";
        }
        $qty_item = 0;
        $view = view('_partials.detail_belum_bayar_edit', compact('kode_temp', 'type_or', 'pembayaran', 'item_bayar', 'qty_item'));
        return $view;
    }

    public function detail_belum_bayar_editItem($id)
    {
        $id_paket = request()->get('id_paket') ?: 0;
        $id_kategori = request()->get('id_kategori') ?: 0;

        $no = 1;
        $id_store = session('id_store');

        $item_revisi = DB::table('pos_revisi_bayar')
            ->where('pos_revisi_bayar.id', $id)
            ->first();

        $item_revisi = new GetDataPembayaranService([$item_revisi]);
        $item_revisi->setDataOrder();
        $item_revisi = $item_revisi->data[0];
        // return response()->json($item_revisi);
        $item_product = DB::table('pos_product_item')
            ->join('pos_product_kategori', 'pos_product_kategori.id_kategori', '=', 'pos_product_item.id_kategori')
            ->select('pos_product_item.*', 'pos_product_kategori.is_paket')
            ->where('pos_product_item.id_store', $id_store)
            ->where('pos_product_item.id_kategori', $item_revisi->id_kategori)
            ->get();

        $additional_menu = DB::table('pos_product_kategori_additional')->where('id_store', $id_store)->get();
        $view = view('_partials.detail_belum_bayar_editItem', compact('id', 'item_revisi', 'item_product', 'id_kategori'));
        return $view;
    }

    public function edit_item_order(Request $request)
    {
        $pos_belum = DB::table('pos_belum')
            ->where('kode_temp', $request->kode_temp)
            ->first();

        $item_to = DB::table('pos_product_item')
            ->where('id_item', $request->id)
            ->first();
            
        $pos_revisi_bayar = DB::table('pos_revisi_bayar')->where('id', $request->id_pos_revisi_bayar)->first();
        if (!$pos_revisi_bayar) return response()->json('error', 404);
        $qty_pos_revisi_bayar = $pos_revisi_bayar->qty;
        $qty_item = $request->qty_item;
        $opsi_menu = $request->input('opsi_menu') ?: [];
        $item_type = $request->input('item_type') ?: null;
        $item_size = $request->input('item_size') ?: null;
        $additional_menu = $request->input('additional_menu') ?: [];

        if ($item_type) $item_type = DB::table('pos_product_item_type')->where('id_type', $item_type['id'])->where('id_item', $request->id)->first();

        $pajak_thirdparty = isset($item_to->pajak_thirdparty) ? $item_to->pajak_thirdparty : 0;
        $thirdparty = $item_to->thirdparty - $pajak_thirdparty;
        
        $type_order = $pos_belum->type_order;

        $harga = $type_order == 1 ? $thirdparty : $item_to->harga;

        $pajak = $type_order == 1 ? $pajak_thirdparty : $item_to->pajak;

        if ($item_type) {
            $harga = $type_order == 1 ? $item_type->harga_thirdparty : $item_type->harga;
            $pajak = $type_order == 1 ? $item_type->pajak_thirdparty : $item_type->pajak;
        }
        if ($item_size) {
            // $harga += $item_size['harga'];
        }

        foreach ($additional_menu as $additional) {
            $harga += $additional['harga'];
        }

        foreach ($opsi_menu as $key => $menu) {
            if ($menu['item_type']) {
                // $harga += $menu['item_type']['harga'];
            }
            if ($menu['item_size']) {
                // $harga += $menu['item_size']['harga'];
            }
            if (isset($menu['additional_menu'])) {
                foreach ($menu['additional_menu'] as $additional) {
                    $harga += $additional['harga'];
                }
            }
        }
        $additional_menu = collect($additional_menu)->sortBy('id')->values();

        $opsi_menu = collect($opsi_menu)->sortBy('id')->sortBy('item_type')->values();
        $opsi_menu = $opsi_menu->map( function ($item, $key) use ($request) {
            $item['qty'] = $request->qty_item;
            $item['nama_item_lama'] = $item['nama_item'];
            $nama_item_service = new GetAdditionalTextService($item['nama_item'], $item['item_type'], $item['item_size']);
            $item['nama_item'] = "$nama_item_service";
            if (isset($item['additional_menu'])) {
                $item_additional_menu = collect($item['additional_menu'])->sortBy('id')->values();
                $item['additional_menu'] = $item_additional_menu;
            }
            return $item;
        });

        $total = $harga + $pajak;

        $opsi_menu = count($opsi_menu) > 0 ? json_encode($opsi_menu) : null;
        $additional_menu = count($additional_menu) > 0 ? json_encode($additional_menu) : null;
        $item_type = !is_null($item_type) ? json_encode($item_type) : null;
        $item_size = !is_null($item_size) ? json_encode($item_size) : null;
        $response = [
            'create' => null,
            'update' => null
        ];
        if ($pos_revisi_bayar->id_item == $item_to->id_item) {
            DB::table('pos_revisi_bayar')
                ->where('id', $request->id_pos_revisi_bayar)
                ->update([
                    'nama_item' => $item_to->nama_item,
                    'id_item' => $item_to->id_item,
                    'id_kategori' => $item_to->id_kategori,
                    'id_store' => $item_to->id_store,
                    'id_kasir' => $pos_belum->id_kasir,
                    'harga' => $harga,
                    'qty' => $request->qty_item,
                    'total' => ($total * $request->qty_item),
                    'pajak' => $pajak,
                    'kode_temp' => $request->kode_temp,
                    'item_size' => $item_size,
                    'item_type' => $item_type,
                    'opsi_menu' => $opsi_menu,
                    'additional_menu' => $additional_menu
                ]);
            $response['update'] = $request->all();
        } else {
            if ($qty_pos_revisi_bayar > $qty_item) {
                $qty_revisi = $pos_revisi_bayar->qty - $request->qty_item;
                $pajak_revisi = $pos_revisi_bayar->pajak * $qty_revisi;
                $total_revisi = ($pos_revisi_bayar->harga * $qty_revisi) + $pajak_revisi;
                DB::table('pos_revisi_bayar')
                    ->where('id', $request->id_pos_revisi_bayar)
                    ->update([
                        'qty' => $qty_revisi,
                        'total' => $total_revisi
                    ]);
                DB::table('pos_revisi_bayar')->insert([
                    'nama_item' => $item_to->nama_item,
                    'id_item' => $item_to->id_item,
                    'id_kategori' => $item_to->id_kategori,
                    'id_store' => $item_to->id_store,
                    'id_kasir' => $pos_belum->id_kasir,
                    'harga' => $harga,
                    'qty' => $request->qty_item,
                    'total' => ($total * $request->qty_item),
                    'pajak' => $pajak,
                    'kode_temp' => $request->kode_temp,
                    'item_size' => $item_size,
                    'item_type' => $item_type,
                    'opsi_menu' => $opsi_menu,
                    'additional_menu' => $additional_menu
                ]);
                $response['update'] = (array)DB::table('pos_revisi_bayar')
                    ->where('id', $request->id_pos_revisi_bayar)->first();
                $response['create'] = $request->all();
            } else {
                DB::table('pos_revisi_bayar')
                    ->where('id', $request->id_pos_revisi_bayar)
                    ->update([
                        'nama_item' => $item_to->nama_item,
                        'id_item' => $item_to->id_item,
                        'id_kategori' => $item_to->id_kategori,
                        'id_store' => $item_to->id_store,
                        'id_kasir' => $pos_belum->id_kasir,
                        'harga' => $harga,
                        'qty' => $request->qty_item,
                        'total' => ($total * $request->qty_item),
                        'pajak' => $pajak,
                        'kode_temp' => $request->kode_temp,
                        'item_size' => $item_size,
                        'item_type' => $item_type,
                        'opsi_menu' => $opsi_menu,
                        'additional_menu' => $additional_menu
                    ]);
                $response['update'] = $request->all();
            }
        }
            
        return response()->json($response);
    }

    public function add_edit_item_order(Request $request)
    {
        DB::beginTransaction();
        $opsi_menu = $request->input('opsi_menu') ?: [];
        $item_type = $request->input('item_type') ?: null;
        $item_size = $request->input('item_size') ?: null;
        $additional_menu = $request->input('additional_menu') ?: [];
        if ($item_type) $item_type = DB::table('pos_product_item_type')->where('id_type', $item_type['id'])->where('id_item', $request->id)->first();
        $item_from = DB::table('pos_belum')
            ->where('kode_temp', $request->kode_temp)
            ->first();

        $item_to = DB::table('pos_product_item')
            ->where('id_item', $request->id)
            ->first();

        $additional_menu = collect($additional_menu)->sortBy('id')->values();

        $opsi_menu = collect($opsi_menu)->sortBy('id')->sortBy('item_type')->values();
        $getDataOpsiMenu = new SetEditOrderOpsiMenu($opsi_menu);
        $opsi_menu = $getDataOpsiMenu->getOpsiMenu2();
        $checkitem = DB::table('pos_revisi_bayar')
            ->where('nama_item', $item_to->nama_item)
            ->where('id_kategori', $item_to->id_kategori)
            ->where('id_kasir', $item_from->id_kasir)
            ->where('opsi_menu', count($opsi_menu) > 0 ? json_encode($opsi_menu) : null)
            ->where('additional_menu', count($additional_menu) > 0 ? json_encode($additional_menu) : null)
            ->where('item_type', !is_null($item_type) ? json_encode($item_type) : null)
            ->where('item_size', !is_null($item_size) ? json_encode($item_size) : null)
            ->first();
        $response = $request->all();
        if ($checkitem != null) {
            $qty = $request->qty_item + $checkitem->qty;
            $total = $checkitem->total * $qty;
            DB::table('pos_revisi_bayar')
                ->where('id', $checkitem->id)
                ->update(
                    [
                        'qty' => $qty,
                        'total' => $total
                    ]
                );
            DB::commit();
            $response['id_pos_revisi_bayar'] = $checkitem->id;
        } else {
            $pajak_thirdparty = isset($item_to->pajak_thirdparty) ? $item_to->pajak_thirdparty : 0;
            $thirdparty = $item_to->thirdparty - $pajak_thirdparty;
            
            $type_order = $item_from->type_order;

            $harga = $type_order == 1 ? $thirdparty : $item_to->harga;

            $pajak = $type_order == 1 ? $pajak_thirdparty : $item_to->pajak;
            
            if ($item_type) {
                $harga = $type_order == 1 ? $item_type->harga_thirdparty : $item_type->harga;
                $pajak = $type_order == 1 ? $item_type->pajak_thirdparty : $item_type->pajak;
            }
            if ($item_size) {
                // $harga += $item_size['harga'];
            }
            
            foreach ($additional_menu as $additional) {
                $harga += $additional['harga'];
            }
    
            foreach ($opsi_menu as $key => $menu) {
                if (is_object($menu)) $menu = (array)$menu;
                if ($menu['item_type']) {
                    // $harga += $menu['item_type']['harga'];
                }
                if ($menu['item_size']) {
                    // $harga += $menu['item_size']['harga'];
                }
                if (isset($menu['additional_menu'])) {
                    foreach ($menu['additional_menu'] as $additional) {
                        $harga += $additional['harga'];
                    }
                }
            }

            $total = $harga + $pajak;
            $opsi_menu = count($opsi_menu) > 0 ? json_encode($opsi_menu) : null;
            $additional_menu = count($additional_menu) > 0 ? json_encode($additional_menu) : null;
            $item_type = !is_null($item_type) ? json_encode($item_type) : null;
            $item_size = !is_null($item_size) ? json_encode($item_size) : null;
            $input = [
                'id' => null,
                'nama_item' => $item_to->nama_item,
                'id_kategori' => $item_to->id_kategori,
                'id_store' => $item_to->id_store,
                'id_kasir' => $item_from->id_kasir,
                'harga' => $harga,
                'qty' => $request->qty_item,
                'total' => ($total * $request->qty_item),
                'pajak' => $pajak,
                'kode_temp' => $request->kode_temp,
                'item_size' => $item_size,
                'item_type' => $item_type,
                'opsi_menu' => $opsi_menu,
                'additional_menu' => $additional_menu
            ];

            $id_pos_revisi_bayar = DB::table('pos_revisi_bayar')->insertGetId($input);
            $response['id_pos_revisi_bayar'] = $id_pos_revisi_bayar;
            DB::commit();
        }
        return json_encode($response);
    }

    public function edit_diskon(Request $request)
    {
        session(['id_diskon' => $request->id, 'nama_diskon' => "Diskon Paket", 'jumlah_diskon' => $request->total_diskon]);
    }

    public function total_pembayaran()
    {
        $id_store = session('id_store');
        $id_kasir = session('id');
        $pembayaran = DB::table('pos_front_payment')->where('id_store', $id_store)->where('id_kasir', $id_kasir)->pluck('total');
        $sum = 0;
        foreach ($pembayaran as $key => $value) {
            $sum += $value;
        }
        $jum = number_format(($sum - session('jumlah_diskon')), 0, ",", ".");
        return $jum;
    }

    public function subtotal_pembayaran()
    {
        $id_store = session('id_store');
        $id_kasir = session('id');
        $pembayaran = DB::table('pos_front_payment')->where('id_store', $id_store)->where('id_kasir', $id_kasir)->pluck('total');
        $sum = 0;
        foreach ($pembayaran as $key => $value) {
            $sum += $value;
        }
        $n = number_format(($sum), 0, ",", ".");
        // $jum = number_format(($sum-session('jumlah_diskon')),0,",",".");
        return $n;
    }

    public function thirdparty_pembayaran()
    {
        $id_store = session('id_store');
        $id_kasir = session('id');
        $pembayaran = DB::table('pos_front_payment')->where('id_store', $id_store)->where('id_kasir', $id_kasir)->pluck('subthirdparty');
        $sum = 0;
        foreach ($pembayaran as $key => $value) {
            $sum += $value;
        }
        $n = number_format(($sum), 0, ",", ".");
        // $jum = number_format(($sum-session('jumlah_diskon')),0,",",".");
        return $n;
    }

    public function index()
    {
        //
    }

    public function create(Request $request)
    {
        $additional_menu = $request->input('additional_menu') ?: [];
        $opsi_menu = $request->input('opsi_menu') ?: [];
        $item_type = $request->input('item_type') ?: null;
        $item_size = $request->input('item_size') ?: null;
        $type = $request->input('type');
        $id = $request->input('id');
        DB::beginTransaction();
        $tiket = DB::table('pos_product_item')->where('id_item', $request->id)->first();

        $harga = $tiket->harga;
        $pajak = $tiket->pajak;
        $total = $tiket->harga + $tiket->pajak;
        $pajak_thirdparty = isset($tiket->pajak_thirdparty) ? $tiket->pajak_thirdparty : 0;
        $total_thirdparty = $tiket->thirdparty;
        $thirdparty = $tiket->thirdparty - $pajak_thirdparty;
        $nama_tiket = $tiket->nama_item;
        if ($item_type) {
            $item_type = DB::table('pos_product_item_type')->where('id_type', $item_type['id'])->first();
            if ($item_type) {
                $harga = $item_type->harga;
                $pajak = $item_type->pajak;
                $total = $item_type->harga_jual;
                $thirdparty = $item_type->harga_thirdparty;
                $pajak_thirdparty = $item_type->pajak_thirdparty;
                $total_thirdparty = $item_type->thirdparty;
            }
        }
        if ($item_size) {
            // $total += $item_size['harga'];
            // $total_thirdparty += $item_size['harga'];
        }
        foreach ($additional_menu as $additional) {
            $total += $additional['harga'];
            $total_thirdparty += $additional['harga'];
        }
        
        foreach ($opsi_menu as $key => $menu) {
            if (isset($menu['additional_menu'])) {
                foreach ($menu['additional_menu'] as $additional) {
                    $total += $additional['harga'];
                    $total_thirdparty += $additional['harga'];
                }
            }
        }

        $opsi_menu = collect($opsi_menu)->sortBy('id')->sortBy('item_type')->values();
        $opsi_menu = $opsi_menu->map( function ($item, $key) {
            if (isset($item['additional_menu'])) {
                $item_additional_menu = collect($item['additional_menu'])->sortBy('id')->values();
                $item['additional_menu'] = $item_additional_menu;
            }
            return $item;
        });
        
        $additional_menu = collect($additional_menu)->sortBy('id')->values();
        $pembayaran = DB::table('pos_front_payment')
        ->where('nama_tiket', $nama_tiket)
        ->where('id_store', $tiket->id_store)
        ->where('id_kasir', session('id'))
        ->where('opsi_menu', count($opsi_menu) > 0 ? json_encode($opsi_menu) : null)
        ->where('additional_menu', count($additional_menu) > 0 ? json_encode($additional_menu) : null)
        ->where('item_type', !is_null($item_type) ? json_encode($item_type) : null)
        ->where('item_size', !is_null($item_size) ? json_encode($item_size) : null)
        ->first();
        
        if ($pembayaran) {
            $qty = ($pembayaran->qty + 1);
            $total = ($pembayaran->qty + 1) * ($pembayaran->total / $pembayaran->qty);
            $thirdparty = ($pembayaran->qty + 1) * ($pembayaran->subthirdparty / $pembayaran->qty);
            $pajak = ($pembayaran->qty + 1) * $pajak;
            $pajak_thirdparty = ($pembayaran->qty + 1) * $pajak_thirdparty;

            DB::table('pos_front_payment')
                ->where('id', $pembayaran->id)
                ->where('id_store', $tiket->id_store)
                ->where('id_kasir', session('id'))
                ->update([
                    'qty' => $qty,
                    'total' => $total,
                    'subthirdparty' => $thirdparty,
                    'subpajak' => $pajak,
                    'subpajak_thirdparty' => $pajak_thirdparty
                ]);
            DB::commit();
            return json_encode("update");
        } else {
            $input = [
                'nama_tiket' => $nama_tiket,
                'id_item' => $tiket->id_item,
                'id_kategori' => $tiket->id_kategori,
                'id_store' => $tiket->id_store,
                'qty' => 1,
                'harga' => $harga,
                'subpajak' => $pajak,
                'total' => $total,
                'subpajak_thirdparty' => $pajak_thirdparty,
                'thirdparty' => $thirdparty,
                'subthirdparty' => $total_thirdparty,
                'id_kasir' => session('id'),
                'opsi_menu' => count($opsi_menu) > 0 ? json_encode($opsi_menu) : null,
                'additional_menu' => count($additional_menu) > 0 ? json_encode($additional_menu) : null,
                'item_type' => !is_null($item_type) ? json_encode($item_type) : null,
                'item_size' => !is_null($item_size) ? json_encode($item_size) : null,
            ];
            DB::table('pos_front_payment')->insert($input);
            DB::commit();
            return json_encode("Add");
        }
    }

    public function diskon(Request $request)
    {
        $id_store = session('id_store');
        $pembayaran = DB::table('pos_front_payment')
            ->where('id_store', $id_store)
            ->where('id_kasir', session('id'))
            ->pluck('total');
        $sum = 0;
        foreach ($pembayaran as $key => $value) {
            $sum += $value;
        }

        $diskon = DB::table('pos_diskon')->where('id_voucher', $request->id)->get();
        $diskon = $diskon[0];
        $jumlah_diskon = 0;
        if ($diskon->tipe_voucher == 2) {
            if ($diskon->maks_persen > (($sum * $diskon->persen) / 100)) {
                $jumlah_diskon = ($sum * $diskon->persen) / 100;
            } else {
                $jumlah_diskon = $diskon->maks_persen;
            }
        } else {
            if ($sum < $diskon->nominal) {
                $jumlah_diskon = $sum;
            } else {
                $jumlah_diskon = $diskon->nominal;
            }
        }
        session(['id_diskon' => $request->id, 'nama_diskon' => $diskon->nama_voucher, 'jumlah_diskon' => $jumlah_diskon]);

        return json_encode(session()->all());
    }

    public function load_diskon()
    {
        ?>
        <td class="nama-barang" id="nama-diskon"><i class="fas fa-cut"></i> Diskon <?= session('nama_diskon') ?>
        <?php
            if (session('id_diskon')) {
                ?>
                &nbsp; &nbsp; &nbsp; <i class="fas fa-times-circle" id="hapus-diskon"></i></td>
                <?php
            }
            ?>
    <td class="harga" id="total-diskon">Rp <span>- <?= number_format(session('jumlah_diskon'), 0, ",", ".") ?></span></td>
    <?php
    }

    public function del_diskon(Request $request)
    {
        $request->session()->forget(['id_diskon', 'jumlah_diskon', 'nama_diskon']);
        $data = $request->session()->all();
        return json_encode("Deleted");
    }

    public function del_order($kode_temp)
    {
        DB::beginTransaction();
        $belum_bayar = DB::table('pos_belum')
            ->where('kode_temp', $kode_temp)
            ->first();
        if ($belum_bayar->type_order == 3) {
            DB::table('pos_meja')
                ->where('nama_meja', $belum_bayar->keterangan_order)
                ->where('id_store', $belum_bayar->id_store)
                ->update([
                    'status' => 0
                ]);
        }

        $type_order = $belum_bayar->type_order;
        $keterangan_order = $belum_bayar->keterangan_order;

        $type_or = "";
        if ($type_order == 1) {
            $type_or = "Third Party";
        } elseif ($type_order == 2) {
            $type_or = "Take Away";
        } elseif ($type_order == 3) {
            $type_or = "Dine In";
        }

        $print_items = [];

        $dataitem = DB::table('pos_belum_bayar')
            ->where('kode_temp', $kode_temp)
            ->get();
        $dataitem = new GetDataPembayaranService($dataitem);
        $dataitem->setDataOrder();
        $dataitem = $dataitem->data;
        foreach ($dataitem as $item) {
            if (count($item->opsi_menu) > 0) {
                foreach ($item->opsi_menu as $menu_item) {
                    array_push($print_items, $menu_item);
                }
            } else {
                array_push($print_items, $item);
            }
        }
        $printers = DB::table('pos_product_kategori')->get();
        foreach ($printers as $printer) {
            $data = collect($print_items)->where('id_kategori', $printer->id_kategori)->values();
            if (count($data) > 0) {
                $printer_service = new DoPrintDeleteOrderService($data, $printer, $kode_temp, $type_or, $keterangan_order);
                if ($printer->ip_printer1 != null) {
                    $printer_service->print($printer->ip_printer1);
                }
                if ($printer->ip_printer2 != null) {
                    $printer_service->print($printer->ip_printer2);
                }
                if ($printer->ip_printer3 != null) {
                    $printer_service->print($printer->ip_printer3);
                }
            }
        }
        DB::table('pos_belum_bayar')
            ->where('kode_temp', $kode_temp)
            ->delete();
        DB::table('pos_belum')
            ->where('kode_temp', $kode_temp)
            ->delete();
        DB::commit();
        return response()->json($print_items);
    }

    public function activity(Request $request)
    {
        DB::beginTransaction();
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('Y-m-d');
        $times = date('H:i:s');
        $id_store = session('id_store');
        $id_kasir = session('id');

        // $angka_inc = DB::table('pos_activity')
        //         ->where('tanggal', $tanggal)
        //         ->where('id_store', $id_store)
        //         ->count()+1;
        // $no_invoice = 'INV'.date('Ymd').$id_store.$angka_inc;
        $no_invoice = $request->no_invoice;
        $dataitem = DB::table('pos_belum_bayar')
            ->where('kode_temp', $request->kode_order)
            ->get();
        $rowitem = DB::table('pos_belum')
            ->where('kode_temp', $request->kode_order)
            ->first();

        $id_diskon = $request->voucher_id;
        $jumlah_diskon = 0;
        if ($id_diskon) {
            $diskon = DB::table('pos_diskon')->where('id_voucher', $id_diskon)->first();
            if ($diskon) $jumlah_diskon = $diskon->nominal;
        }
        $subtotal = $rowitem->subtotal;
        $total = $rowitem->total - $jumlah_diskon;
        $type_order = $rowitem->type_order;
        $keterangan_order = $rowitem->keterangan_order;
        $tipe_pembayaran = $request->tipe_pembayaran;
        $is_split = isset($request->is_split) ? $request->is_split : 0;
        $no_rek = $request->no_rek;
        $debit_cash = $request->debit_cash;
        $cash = $request->cash;
        $cash_split = $request->cash_split;
        $kembalian = $request->kembalian;

        $type_or = "";
        if ($type_order == 1) {
            $type_or = "Third Party";
        } elseif ($type_order == 2) {
            $type_or = "Take Away";
        } elseif ($type_order == 3) {
            $type_or = "Dine In";
        }

        if ($tipe_pembayaran != 'Tunai') {
            $cash = $cash_split;
        }
        $data_activity = [
            'id_activity' => null, 'no_invoice' => $no_invoice, 'id_store' => $id_store,
            'id_employee' => $id_kasir, 'id_discount' => $id_diskon, 'subtotal' => $subtotal,
            'total' => $total, 'tipe_payment' => $tipe_pembayaran,
            'is_split' => $is_split, 'no_rek' => $no_rek, 'debit_cash' => $debit_cash, 'cash' => $cash,
            'kembalian' => $kembalian, 'tanggal' => $tanggal, 'time' => $times, 'status' => 'success',
            'type_order' => $type_order, 'keterangan_order' => $keterangan_order, 'pajak' => $rowitem->pajak
        ];
        DB::table('pos_activity')->insert($data_activity);
        $data_activity_item = [];
        foreach ($dataitem as $key => $value) {
            array_push($data_activity_item, [
                'id' => null, 'no_invoice' => $no_invoice, 'nama_item' => $value->nama_item,
                'id_kategori' => $value->id_kategori, 'id_store' => $value->id_store, 'id_kasir' => $id_kasir, 'harga' => $value->harga,
                'qty' => $value->qty, 'total' => $value->total, 'created_at' => $tanggal . ' ' . $times, 'status' => 'success',
                'opsi_menu' => $value->opsi_menu, 'additional_menu' => $value->additional_menu,
                'item_type' => $value->item_type, 'item_size' => $value->item_size, 'pajak' => $value->pajak
            ]);
        }
        DB::table('pos_activity_item')->insert($data_activity_item);

        $data_pembayaran = new GetDataPembayaranService($dataitem);
        $data_pembayaran->setDataOrder();
        // // Print langsung dari PC
        $print_service = new DoPrintPCService($data_pembayaran);
        $print_service->print($no_invoice, $cash, $tipe_pembayaran, $debit_cash, $kembalian, $is_split, $type_or, $keterangan_order, $request->kode_order, $id_diskon);

        session()->forget(['id_diskon', 'jumlah_diskon', 'nama_diskon']);
        DB::table('pos_belum_bayar')
            ->where('kode_temp', $request->kode_order)
            ->delete();
        DB::table('pos_belum')
            ->where('kode_temp', $request->kode_order)
            ->delete();
        if ($type_order == 3) {
            DB::table('pos_meja')
                ->where('nama_meja', $keterangan_order)
                ->where('id_store', $id_store)
                ->update([
                    'status' => 0
                ]);
        }
        DB::commit();
        return true;
    }

    public function order(Request $request)
    {
        DB::beginTransaction();
        date_default_timezone_set('Asia/Jakarta');
        // echo json_encode($request->all());  
        $id_store = session('id_store');
        $id_kasir = session('id');
        $id_diskon = session('id_diskon');

        $angka_inc = DB::table('pos_activity')
            ->where('tanggal', date('Y-m-d'))
            ->where('id_store', $id_store)
            ->orderBy('no_invoice', 'desc')
            ->first();

        $angka_inc_belum = DB::table('pos_belum')
            ->where('tanggal', date('Y-m-d'))
            ->where('id_store', $id_store)
            ->orderBy('kode_temp', 'desc')
            ->first();

        if ($angka_inc_belum == null) {
            if ($angka_inc == null) {
                $angka_inc = 1;
            } else {
                $num = substr($angka_inc->no_invoice, -4);
                $angka_inc = $num + 1;
            }
        } else {
            if ($angka_inc == null) {
                $num = substr($angka_inc_belum->kode_temp, -4);
                $angka_inc = $num + 1;
            } else {
                if ($angka_inc->no_invoice > $angka_inc_belum->kode_temp) {
                    $num = substr($angka_inc->no_invoice, -4);
                    $angka_inc = $num + 1;
                } else {
                    $num = substr($angka_inc_belum->kode_temp, -4);
                    $angka_inc = $num + 1;
                }
            }
        }


        $angka_inc = sprintf('%04d', $angka_inc);
        $kode_temp = 'MP' . date('ymd') . $angka_inc;

        $dataitem = DB::table('pos_front_payment')
            ->where('id_store', $id_store)
            ->where('id_kasir', $id_kasir)
            ->get();
        
        $data_pembayaran = new GetDataPembayaranService($dataitem);
        $data_pembayaran->setData();
        $sub_total = $data_pembayaran->sub_total;
        $sub_total_thirdparty = $data_pembayaran->sub_total_thirdparty;
        $total = $data_pembayaran->total;
        $thirdparty = $data_pembayaran->thirdparty;
        $pajak = $data_pembayaran->pajak;
        $pajak_thirdparty = $data_pembayaran->pajak_thirdparty;
        $pembayaran = $data_pembayaran->data;
        $type_order = $request->type_order;
        $meja_choose = $request->meja_choose;
        $keterangan_order = $request->keterangan_order;

        $type_or = "";
        if ($type_order == 1) {
            $type_or = "Third Party";
        } elseif ($type_order == 2) {
            $type_or = "Take Away";
        } elseif ($type_order == 3) {
            $type_or = "Dine In";
        }

        if ($meja_choose != null) {
            DB::table('pos_meja')->where('id', $meja_choose)
                ->update([
                    'status' => 1
                ]);
        }
        $data_pos_belum = [
            'id' => null,
            'kode_temp' => $kode_temp,
            'id_store' => $id_store,
            'id_kasir' => $id_kasir,
            'id_discount' => $id_diskon,
            'subtotal' => $type_order == 1 ? $sub_total_thirdparty : $sub_total,
            'total' => $type_order == 1 ? $thirdparty : $total,
            'pajak' => $type_order == 1 ? $pajak_thirdparty : $pajak,
            'tanggal' => date('Y-m-d'),
            'type_order' => $type_order,
            'keterangan_order' => $keterangan_order,
            'note' => $request->note
        ];
        DB::table('pos_belum')->insert($data_pos_belum);
        $data_pos_belum_bayar = [];
        $index = 0;
        while ($index < count($pembayaran)) {
            $item = $pembayaran[$index];
            $harga_item = 0;
            $total_harga_item = 0;
            foreach ($item->additional_menu as $additional_menu) {
                $harga_item += $additional_menu->harga;
            }
            foreach ($item->opsi_menu as $opsi_menu) {
                // $opsi_menu->qty = $item->qty;
                if (isset($opsi_menu->additional_menu)) {
                    foreach ($opsi_menu->additional_menu as $additional_menu) {
                        $harga_item += $additional_menu->harga;
                    }
                }
            }
            if ($type_order == 1) {
                $harga_item += $item->thirdparty;
                $total_harga_item += (($harga_item * $item->qty) + $item->subpajak_thirdparty);
            } else {
                $harga_item += $item->harga;
                $total_harga_item += (($harga_item * $item->qty) + $item->subpajak);
            }
            $pos_belum_bayar = [
                'id' => null,
                'kode_temp' => $kode_temp,
                'nama_item' => $item->nama_item_lama,
                'id_item' => $item->id_item,
                'id_kategori' => $item->id_kategori,
                'id_store' => $item->id_store,
                'id_kasir' => $id_kasir,
                'harga' => $harga_item,
                'qty' => $item->qty,
                'total' => $total_harga_item,
                'additional_menu' => count($item->additional_menu) > 0 ? json_encode($item->additional_menu) : null,
                'opsi_menu' => count($item->opsi_menu) > 0 ? json_encode($item->opsi_menu) : null,
                'id_paket' => $item->id_paket,
                'item_type' => is_null($item->item_type) ? null : json_encode($item->item_type),
                'item_size' => is_null($item->item_size) ? null : json_encode($item->item_size),
                'pajak' => ($type_order == 1 ? $item->subpajak_thirdparty : $item->subpajak) / $item->qty
            ];
            array_push($data_pos_belum_bayar, $pos_belum_bayar);
            $index += 1;
        }
        DB::table('pos_belum_bayar')->insert($data_pos_belum_bayar);

        foreach ($dataitem as $key => $value) {
            foreach ($value->opsi_menu as $opsi_menu) {
                $opsi_menu->qty = $value->qty;
                $dataitem->push($opsi_menu);
            }
        }
        $dataitem = $dataitem->filter( function ( $item, $key ) {
            return $item->is_paket == 0;
        })->values();
        
        $duplicates_item = collect($dataitem)->duplicates('order_id');
        foreach ($duplicates_item as $key => $duplicate) {
            $qty = $dataitem[$key]->qty;
            $dataitem = $dataitem->filter( function ($item, $index) use ($key, $duplicate, $qty) {
                if ($item->order_id == $duplicate) {
                    $item->qty += $qty;
                }
                return $index != $key;
            });
        }
        $id_kategori = $dataitem->pluck('id_kategori')->unique()->values();
        $printers = DB::table('pos_product_kategori')->whereIn('id_kategori', $id_kategori)->get();
        foreach ($printers as $printer) {
            $printer_service = new DoPrintDapurService($dataitem, $printer, $kode_temp, $type_or, $keterangan_order, $request->note);
            if ($printer->ip_printer1 != null) {
                $printer_service->print($printer->ip_printer1);
            }
            if ($printer->ip_printer2 != null) {
                $printer_service->print($printer->ip_printer2);
            }
            if ($printer->ip_printer3 != null) {
                $printer_service->print($printer->ip_printer3);
            }
        }

        DB::table('pos_front_payment')
            ->where('id_store', $id_store)
            ->where('id_kasir', $id_kasir)
            ->delete();

        DB::table('pos_diskon')->where('id_voucher', 1)
            ->update([
                'nominal' => null
            ]);
        DB::commit();
        session()->forget(['id_diskon', 'jumlah_diskon', 'nama_diskon']);
        return true;
    }

    public function edit_order(Request $request)
    {
        DB::beginTransaction();
        date_default_timezone_set('Asia/Jakarta');

        $kode_temp = $request->kode_temp;
        $create_data = isset($request->data['createData']) ? $request->data['createData'] : [];
        $update_data = isset($request->data['updateData']) ? $request->data['updateData'] : [];
        $delete_data = isset($request->data['deleteData']) ? $request->data['deleteData'] : [];

        $rowitem = DB::table('pos_belum')
            ->where('kode_temp', $kode_temp)
            ->first();

        $type_order = $rowitem->type_order;
        $keterangan_order = $rowitem->keterangan_order;

        $type_or = "";
        if ($type_order == 1) {
            $type_or = "Third Party";
        } elseif ($type_order == 2) {
            $type_or = "Take Away";
        } elseif ($type_order == 3) {
            $type_or = "Dine In";
        }

        $datarevisi = DB::table('pos_revisi_bayar')
            ->where('kode_temp', $kode_temp)
            ->get();

        $data_need_to_update = [];
        $data_need_to_delete = [];
        $data_need_to_add = [];
        foreach ($delete_data as $index => $data) {
            if (!isset($data['additional_menu'])) $data['additional_menu'] = [];
            $data['additional_menu'] = collect($data['additional_menu'])->sortBy('id')->values();
            if (!isset($data['opsi_menu'])) $data['opsi_menu'] = [];
            $data['nama_item_lama'] = $data['nama_item'];
            $nama_item = new GetAdditionalTextService($data['nama_item'], $data['item_type'], $data['item_size']);
            $data['nama_item'] = "$nama_item";
            $opsi_menu = new SetEditOrderOpsiMenu($data['opsi_menu'], $data);
            $data['opsi_menu'] = $opsi_menu->getOpsiMenu();
            if (count($data['opsi_menu']) > 0) {
                foreach ($data['opsi_menu'] as $opsi_menu) {
                    array_push($data_need_to_delete, (object)$opsi_menu);
                }
            } else {
                array_push($data_need_to_delete, (object)$data);
            }
        }
        foreach ($create_data as $index => $data) {
            if (!isset($data['additional_menu'])) $data['additional_menu'] = [];
            $data['additional_menu'] = collect($data['additional_menu'])->sortBy('id')->values();
            if (!isset($data['item_type'])) $data['item_type'] = null;
            if (!isset($data['item_size'])) $data['item_size'] = null;
            if (!isset($data['opsi_menu'])) $data['opsi_menu'] = [];
            if (is_string($data['opsi_menu'])) $data['opsi_menu'] = json_decode($data['opsi_menu']);
            $data['nama_item_lama'] = isset($data['text']) ? $data['text'] : $data['nama_item'];
            $nama_item = new GetAdditionalTextService($data['nama_item_lama'], $data['item_type'], $data['item_size']);
            $data['nama_item'] = "$nama_item";
            $opsi_menu = new SetEditOrderOpsiMenu($data['opsi_menu'], $data);
            $data['opsi_menu'] = $opsi_menu->getOpsiMenu();
            if (count($data['opsi_menu']) > 0) {
                foreach ($data['opsi_menu'] as $opsi_menu) {
                    array_push($data_need_to_add, (object)$opsi_menu);
                }
            } else {
                array_push($data_need_to_add, (object)$data);
            }
        }
        foreach ($update_data as $key => $data) {
            if (!isset($data['text'])) $data['text'] = $data['nama_item'];
            $data['nama_item'] = $data['text'];
            $data['nama_item_lama'] = $data['nama_item'];
            $nama_item = new GetAdditionalTextService($data['nama_item'], $data['item_type'], $data['item_size']);
            $data['nama_item'] = "$nama_item";
            if (!isset($data['qty_item'])) $data['qty_item'] = $data['qty'];
            $data['qty'] = $data['qty_item'];
            if (!isset($data['additional_menu'])) $data['additional_menu'] = [];
            $data['additional_menu'] = collect($data['additional_menu'])->sortBy('id')->values();
            if (!isset($data['opsi_menu']) || !$data['opsi_menu']) $data['opsi_menu'] = [];
            if (is_string($data['opsi_menu'])) $data['opsi_menu'] = json_decode($data['opsi_menu']);
            $opsi_menu = new SetEditOrderOpsiMenu($data['opsi_menu'], $data);
            $data['opsi_menu'] = $opsi_menu->getOpsiMenu();
            $data = (object)$data;
            $data_awal = DB::table('pos_belum_bayar')->where('id', $data->id_pos_belum_bayar)->first();
            if ($data_awal) {
                $data_awal = new GetDataPembayaranService([$data_awal]);
                $data_awal->setDataOrder();
                $data_awal = $data_awal->data[0];
                $data = new GetDataPembayaranService([$data]);
                $data->setDataOrder();
                $data = $data->data[0];
                $data->nama_item = $data->nama_item_lama;
                $data_awal->additional_menu = collect($data_awal->additional_menu)->sortBy('id')->values();
                $data_awal->opsi_menu = collect($data_awal->opsi_menu)->map( function ($opsi) use ($data_awal) {
                    if (!isset($opsi->additional_menu)) $opsi->additional_menu = [];
                    $opsi->additional_menu = collect($opsi->additional_menu)->sortBy('id')->values();
                    return $opsi;
                })->sortBy('id')->values();
                if ($data->id_kategori == $data_awal->id_kategori) {
                    if ((count($data->opsi_menu) == 0) && (count($data_awal->opsi_menu) == 0)) {
                        $data->data_awal = $data_awal;
                        if ($data->order_id != $data_awal->order_id) array_push($data_need_to_update, $data);
                    } else {
                        $data_awal_opsi_menu = $data_awal->opsi_menu;
                        $data_opsi_menu = $data->opsi_menu;
                        if (count($data_opsi_menu) == 0 && count($data_awal_opsi_menu) != 0) {
                            foreach ($data_awal_opsi_menu as $opsi_menu) {
                                array_push($data_need_to_delete, $opsi_menu);
                            }
                        } else if (count($data_opsi_menu) != 0 && count($data_awal_opsi_menu) == 0) {
                            foreach ($data_opsi_menu as $opsi_menu) {
                                array_push($data_need_to_add, (object)$opsi_menu);
                            }
                        } else {
                            $data_opsi_menu = collect($data_opsi_menu)->map(function ($option) {
                                if (is_array($option)) $option = (object)$option;
                                if (!$option->id_item) $option->id_item = $option->id;
                                return $option;
                            })->values();
                            // return response()->json([$data_awal_opsi_menu, $data_opsi_menu]);
                            if ($data->id == $data_awal->id_item) {
                                foreach ($data_opsi_menu as $index => $opsi_menu) {
                                    if (is_object($opsi_menu)) $opsi_menu = (array)$opsi_menu;
                                    $opsi_awal = $data_awal_opsi_menu[$index];
                                    $item_type = $opsi_awal->item_type != $opsi_menu['item_type'];
                                    $additional = $opsi_awal->additional_menu != $opsi_menu['additional_menu'];
                                    $qty = $opsi_awal->qty != $opsi_menu['qty'];
                                    $condition = $item_type || $additional || $qty;
                                    if ($condition) {
                                        if (is_array($opsi_menu)) $opsi_menu = (object)$opsi_menu;
                                            $opsi_menu->data_awal = $opsi_awal;
                                        array_push($data_need_to_update, $opsi_menu);
                                    }
                                }
                            } else {
                                $selected_ids = [];
                                foreach ($data_awal_opsi_menu as $opsi_menu) {
                                    $menu_exist = collect($data_opsi_menu)->where('id_item', $opsi_menu->id)->values()->first();
                                    if ($menu_exist) {
                                        if (is_array($menu_exist)) $menu_exist = (object)$menu_exist;
                                        $item_type = $menu_exist->item_type 
                                        != $opsi_menu->item_type;
                                        $additional = $menu_exist->additional_menu != $opsi_menu->additional_menu;
                                        $qty = $menu_exist->qty != $opsi_menu->qty;
                                        $condition = $item_type || $additional || $qty;
                                        if ($condition) {
                                            $menu_exist->data_awal = $opsi_menu;
                                            array_push($data_need_to_update, $menu_exist);
                                            array_push($selected_ids, $menu_exist->id_item);
                                        }
                                    } else {
                                        array_push($data_need_to_delete, $opsi_menu);
                                    }
                                }
                                $data_opsi_menu = collect($data_opsi_menu)->whereNotIn('id_item', $selected_ids)->values();
                                foreach($data_opsi_menu as $opsi_menu) {
                                    array_push($data_need_to_add, $opsi_menu);
                                }
                            }
                        }
                    }
                }
            }
        }
        $printers = DB::table('pos_product_kategori')->where('is_paket', 0)->get();
        foreach ($printers as $printer) {
            $update = collect($data_need_to_update)->where('id_kategori', $printer->id_kategori)->values();
            $delete = collect($data_need_to_delete)->where('id_kategori', $printer->id_kategori)->values()->toArray();
            $add = collect($data_need_to_add)->where('id_kategori', $printer->id_kategori)->values();
            if (count($update) > 0 || count($delete) > 0 || count($add) > 0) {
                $printer_service = new DoPrintDapurRevisiService($update, $add, $delete, $printer, $kode_temp, $type_or, $keterangan_order);
                if ($printer->ip_printer1 != null) {
                    $printer_service->print($printer->ip_printer1);
                }
                if ($printer->ip_printer2 != null) {
                    $printer_service->print($printer->ip_printer2);
                }
                if ($printer->ip_printer3 != null) {
                    $printer_service->print($printer->ip_printer3);
                }
            }
        }
        $data_pembayaran = new GetDataPembayaranService($datarevisi);
        $data_pembayaran->setDataOrder();
        $subtotal = $type_order == 1 ? $data_pembayaran->sub_total_thirdparty : $data_pembayaran->sub_total;
        $total = $data_pembayaran->total;
        $pajak = $type_order == 1 ? $data_pembayaran->pajak_thirdparty : $data_pembayaran->pajak;
        $update = [
            'subtotal' => $subtotal,
            'total' => $total,
            'pajak' => $pajak
        ];
        // return response()->json($update);
        DB::table('pos_belum')->where('kode_temp', $kode_temp)->update($update);
        DB::table('pos_belum_bayar')->where('kode_temp', $kode_temp)
            ->delete();
        $data_pos_belum_bayar = [];
        foreach ($datarevisi as $key => $value) {
            $harga_item = $value->harga;
            foreach ($value->additional_menu as $additional_menu) {
                $harga_item += $additional_menu->harga;
            }
            foreach ($value->opsi_menu as $opsi_menu) {
                if (isset($opsi_menu->additional_menu)) {
                    foreach ($opsi_menu->additional_menu as $additional_menu) {
                        $harga_item += $additional_menu->harga;
                    }
                }
            }
            array_push($data_pos_belum_bayar, [
                'id' => null,
                'nama_item' => $value->nama_item_lama,
                'id_item' => $value->id_item,
                'id_kategori' => $value->id_kategori,
                'id_store' => $value->id_store,
                'id_kasir' => $value->id_kasir,
                'harga' => $harga_item,
                'qty' => $value->qty,
                'total' => $value->total,
                'kode_temp' => $kode_temp,
                'additional_menu' => count($value->additional_menu) > 0 ? json_encode($value->additional_menu) : null,
                'opsi_menu' => count($value->opsi_menu) > 0 ? json_encode($value->opsi_menu) : null,
                'item_type' => is_null($value->item_type) ? null : json_encode($value->item_type),
                'item_size' => is_null($value->item_size) ? null : json_encode($value->item_size),
                'pajak' => $value->pajak
            ]);
        }
        DB::table('pos_belum_bayar')->insert($data_pos_belum_bayar);
        DB::commit();
        return response()->json([$data_pos_belum_bayar, [
            'subtotal' => $subtotal,
            'total' => $total,
            'pajak' => $pajak
        ]]);
    }

    public function edit_note(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        // echo json_encode($request->all());  

        $rowitem = DB::table('pos_belum')
            ->where('id', $request->id)
            ->first();

        $id_store = $rowitem->id_store;
        $id_kasir = $rowitem->id_kasir;
        $id_diskon = $rowitem->id_discount;

        $kode_temp = $rowitem->kode_temp;


        $dataitem = DB::table('pos_belum_bayar')
            ->where('kode_temp', $kode_temp)
            ->get();

        $data_pembayaran = new GetDataPembayaranService($dataitem);
        $data_pembayaran->setDataOrder();
        foreach ($dataitem as $key => $value) {
            foreach ($value->opsi_menu as $opsi_menu) {
                $dataitem->push($opsi_menu);
            }
        }
        $dataitem = $dataitem->filter( function ( $item, $key ) {
            return $item->is_paket == 0;
        })->values();
        $subtotal = $rowitem->subtotal;
        $total = $rowitem->total;

        $type_order = $rowitem->type_order;
        $keterangan_order = $rowitem->keterangan_order;

        $type_or = "";
        if ($type_order == 1) {
            $type_or = "Third Party";
        } elseif ($type_order == 2) {
            $type_or = "Take Away";
        } elseif ($type_order == 3) {
            $type_or = "Dine In";
        }

        DB::table('pos_belum')->where('id', $request->id)
            ->update([
                'note' => $request->note
            ]);

        $duplicates_item = collect($dataitem)->duplicates('order_id');
        foreach ($duplicates_item as $key => $duplicate) {
            $dataitem = $dataitem->filter( function ($item, $index) use ($key) {
                return $index != $key;
            });
        }
        $id_kategori = $dataitem->pluck('id_kategori')->unique()->values();
        $printers = DB::table('pos_product_kategori')->whereIn('id_kategori', $id_kategori)->get();
        foreach ($printers as $printer) {
            $printer_service = new DoPrintNoteService($printer, $kode_temp, $type_or, $keterangan_order, $request->note);
            if ($printer->ip_printer1 != null) {
                $printer_service->print($printer->ip_printer1);
            }
            if ($printer->ip_printer2 != null) {
                $printer_service->print($printer->ip_printer2);
            }
            if ($printer->ip_printer3 != null) {
                $printer_service->print($printer->ip_printer3);
            }
        }

        return true;
        // return json_encode($array);



    }

    public function add_edit_order()
    {
        $item_product = DB::table('pos_product_item')
            ->join('pos_product_kategori', 'pos_product_kategori.id_kategori', '=', 'pos_product_item.id_kategori')
            ->select('pos_product_item.*', 'pos_product_kategori.is_paket')
            ->where('id_store', session('id_store'))
            ->get();
            
        ?>
            <div class="form-group">
                <label for="">Add Item</label>
                <div class="d-flex justify-content-between">
                    <div class="container">
                        <div class="row">
                            <div class="col-8">
                                <select id="sel_order_add" class="form-control">
                                    <option></option>
                                    <?php foreach ($item_product as $key => $value) { ?>
                                        <option
                                            value="<?= $value->id_item ?>"
                                            data-dataid="<?= $value->id_item?>"
                                            data-type="<?= $value->is_paket ? 'paket' : 'item'?>"
                                            data-kategori="<?= $value->id_kategori ?>"
                                            data-text="<?= $value->nama_item ?>"
                                        >
                                            <?= $value->nama_item ?> @<?= $value->harga_jual ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-4">
                                <input type="number" min="1" value="1" id="qty_order_add" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col" id="add_item_additional_menu">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function close()
    {
        date_default_timezone_set('Asia/Jakarta');
        $id_store = session('id_store');
        $id_kasir = session('id');
        $tanggal = date("Y-m-d");
        $dataitem = DB::select("SELECT pos_activity_item.nama_item, SUM(qty) as total_qty, SUM(total) as total_bayar FROM pos_activity_item WHERE id_store=$id_store AND id_kasir=$id_kasir AND date(created_at)='$tanggal' AND status='success' GROUP BY nama_item");
        $deposit = DB::table('pos_deposit')->where('id_store', $id_store)->where('id_kasir', $id_kasir)->where('tanggal', $tanggal)->where('status', 1)->select('deposit')->get();

        $tunai = DB::select("SELECT SUM(cash-kembalian) as total_cash FROM pos_activity WHERE id_store=$id_store AND id_employee=$id_kasir AND tanggal='$tanggal' AND status='success'");
        $debit = DB::select("SELECT tipe_payment, SUM(debit_cash) as total_debit FROM pos_activity WHERE id_store=$id_store AND id_employee=$id_kasir AND tanggal='$tanggal' AND status='success' AND tipe_payment!='Tunai' GROUP BY tipe_payment");
        $diskon = DB::select("SELECT SUM(subtotal-total) as total_diskon FROM pos_activity WHERE id_store=$id_store AND id_employee=$id_kasir AND tanggal='$tanggal' AND status='success'");
        // dd($deposit);
        return view('close', compact('dataitem', 'tunai', 'debit', 'diskon', 'deposit'));
        // return $diskon;
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $pos_front_payment = DB::table('pos_front_payment')->where('id', $id)->first();
        if ($pos_front_payment) {
            $total = $pos_front_payment->total / $pos_front_payment->qty;
            $subthirdparty = $pos_front_payment->subthirdparty / $pos_front_payment->qty;
            $subpajak = $pos_front_payment->subpajak / $pos_front_payment->qty;
            $subpajak_thirdparty = $pos_front_payment->subpajak_thirdparty / $pos_front_payment->qty;
            $data = [
                'qty' => $request->qty,
                'total' => $total * $request->qty,
                'subthirdparty' => $subthirdparty * $request->qty,
                'subpajak' => $subpajak * $request->qty,
                'subpajak_thirdparty' => $subpajak_thirdparty * $request->qty
            ];
            // 214, 244
            DB::table('pos_front_payment')->where('id', $id)
                ->update($data);
        }
        return json_encode("Update Sukses");
    }

    public function destroy($id)
    {
        // var_dump($id);
        $id_store = session('id_store');
        DB::table('pos_front_payment')
            ->where('id', $id)
            ->where('id_store', $id_store)
            ->delete();
        return json_encode("Delete");
    }

    // Print Untuk PC
    public function print_con($inv, $c_bayar, $nama_debit, $d_bayar, $kembali, $is_split, $type_order, $keterangan_order, $kode_temp, $id_diskon)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('d M Y, H:i');
        $nama_kasir = session('nama');
        $nama_store = session('nama_store');
        $nama_printer = DB::table('pos_store')->where('id_store', session('id_store'))->first()->print_kasir;
        $connector = new WindowsPrintConnector($nama_printer);
        // $connector = new NetworkPrintConnector("10.154.30.208");
        /* Information for the receipt */

        $items = array();
        $totalQty = 0;
        $totalHarga = 0;
        $pembayaran = DB::table('pos_belum_bayar')
            ->where('kode_temp', $kode_temp)
            ->get();

        $data_pembayaran = new GetDataPembayaranService($pembayaran);
        $data_pembayaran->setDataOrder();

        foreach ($data_pembayaran->data as $key => $value) {
            $totalQty += $value->qty;
            $totalHarga += $value->total;
            if (!($value->id_paket != null && $value->id_kategori != 0)) {
                array_push(
                    $items,
                    new item(
                        $value->nama_item . "\n(" . $value->qty . " x " . number_format($value->harga, 0, ",", ".") . ")",
                        number_format($value->qty * $value->harga, 0, ",", ".")
                    )
                );
            } else {
                array_push($items, "- $value->nama_item");
            }
            if ($value->id_kategori != 0) {
                foreach ($value->additional_menu as $additional_menu) {
                    array_push(
                        $items,
                        new item(
                            "  + $additional_menu->nama_additional_menu",
                            number_format($additional_menu->harga * $value->qty, 0, ",", ".")
                        )
                    );
                    $totalHarga += $additional_menu->harga;
                }
            } else {
                $totalQty -= 1;
            }
        }

        $desc = new item2('Item (Qty x Price)', 'Total');
        $tax = new item2('Total Item', $totalQty);
        $total = new item2('Subtotal (Rp)', number_format($totalHarga, 0, ",", "."));
        $cash = new item('Cash (Rp)', number_format($c_bayar, 0, ",", "."));
        $debit = new item($nama_debit . ' (Rp)', number_format($d_bayar, 0, ",", "."));
        $kembalian = new item('Kembalian', number_format($kembali, 0, ",", "."));
        /* Date is kept the same for testing */
        // $date = date('l jS \of F Y h:i:s A');
        // $date = "Monday 6th of April 2015 02:56:25 PM";

        /* Start the printer */
        // $logo = EscposImage::load("resources/escpos-php.png", false);
        $printer = new Printer($connector);

        /* Print top logo */
        // $printer -> setJustification(Printer::JUSTIFY_CENTER);
        // $printer -> graphics($logo);

        /* Name of shop */
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $logo = EscposImage::load("images/icon/mediumLogo.png", false);
        // $printer->bitImageColumnFormat($logo, Printer::IMG_DOUBLE_WIDTH | Printer::IMG_DOUBLE_HEIGHT);
        $printer->selectPrintMode();
        $printer->text("Jln.Raja Isa Komp.Ruko KDA Junction\nBlok.D No.05");
        $printer->feed();

        /* Title of receipt */
        $printer->text("_________________________________\n");
        $printer->feed();


        /* Title of receipt */
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("No. Nota : " . $inv . "\nType Order : " . $type_order . " - " . $keterangan_order . "\nTanggal  : " . $tanggal . "\nKasir    : " . $nama_kasir . "\n");

        $printer->text("_________________________________\n");
        $printer->feed();


        // Ket nama

        $printer->text($desc);
        $printer->text("_________________________________\n");


        /* Items */
        $printer->feed();
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        foreach ($items as $item) {
            $printer->text($item);
        }
        $printer->text("_________________________________\n");
        $printer->feed();

        /* Tax and total */
        $printer->text($tax);
        // $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer->text($total);
        if ($id_diskon != null) {
            $diskon = DB::table('pos_belum')->where('kode_temp', $kode_temp)->first();
            $totaldiskon = $diskon->subtotal - $diskon->total;
            $rowdiskon = DB::table('pos_diskon')->where('id_voucher', $id_diskon)->first();
            $totaldiskon = $rowdiskon->nominal;

            $txtdiskon = new item2($rowdiskon->nama_voucher, "-" . number_format($totaldiskon, 0, ",", "."));
            $totalHarga -= $totaldiskon;
            $printer->text($txtdiskon);
        }
        $txttotal = new item('Total (Rp)', number_format($totalHarga, 0, ",", "."));
        $printer->text($txttotal);
        $printer->text("_________________________________\n");
        $printer->feed();
        if ($nama_debit == "Tunai") {
            $printer->text($cash);
        } else if ($is_split == 1) {
            $printer->text($cash);
            $printer->text($debit);
        } else {
            $printer->text($debit);
        }
        $printer->text($kembalian);
        $printer->selectPrintMode();
        $printer->feed(2);

        /* Cut the receipt and open the cash drawer */
        $printer->cut();
        return Log::info([$printer->getPrintConnector()]);
        // $printer -> pulse();

        $printer->close();
    }

    // Print Untuk PC RESI
    public function print_resi(Request $request, $inv)
    {
        date_default_timezone_set('Asia/Jakarta');
        $row_bayar = DB::table('pos_belum')
            ->where('kode_temp', $inv)
            ->first();
        $type_order = $row_bayar->type_order;
        $keterangan_order = $row_bayar->keterangan_order;
        $kode_temp = $inv;
        $id_diskon = $request->get('id_diskon');
        $type_or = "";
        if ($type_order == 1) {
            $type_or = "Third Party";
        } elseif ($type_order == 2) {
            $type_or = "Take Away";
        } elseif ($type_order == 3) {
            $type_or = "Dine In";
        }
        
        $pembayaran = DB::table('pos_belum_bayar')
            ->join('pos_product_kategori', 'pos_product_kategori.id_kategori', '=', 'pos_belum_bayar.id_kategori')
            ->select('pos_belum_bayar.*', 'pos_product_kategori.is_paket')
            ->where('kode_temp', $kode_temp)
            ->get();
        $nama_printer = DB::table('pos_store')->where('id_store', session('id_store'))->first()->print_kasir;
        $data_pembayaran = new GetDataPembayaranService($pembayaran);
        $data_pembayaran->setDataOrder();
        $printer_service = new DoPrintResiService($nama_printer, $inv, $data_pembayaran, $type_or, $keterangan_order, $id_diskon);
        $printer_service->print();
    }

    // // Print Kategori
    // public function print($id, $kode_temp, $type_order, $keterangan_order){
    //     // $connector =  new WindowsPrintConnector("POS-80C-Network");
    //     date_default_timezone_set('Asia/Jakarta');
    //     $tanggal = date('d-m-Y H:i');
    //     $nama_store = session('nama_store');
    //     $ip_printer = DB::table('pos_product_kategori')
    //             ->where('id_kategori', $id)
    //             ->first();

    //     if($ip_printer->print_by == "nama"){
    //         $connector = new WindowsPrintConnector($ip_printer->ip_printer1);
    //     }else{
    //         $connector = new NetworkPrintConnector($ip_printer->ip_printer1);
    //     }

    //     /* Information for the receipt */

    //     $items = array();
    //     $totalQty=0;
    //     $totalHarga=0;
    //     $pembayaran = DB::table('pos_belum_bayar')
    //             ->where('id_kategori', $id)
    //             ->where('kode_temp', $kode_temp)
    //             ->get();

    //     foreach ($pembayaran as $key => $value) {
    //         $totalQty+= $value->qty;
    //         $totalHarga+= $value->total;
    //         array_push($items, new item($value->nama_item, $value->qty));
    //     }

    //     $desc = new item('Nama Item', 'Qty');

    //     $printer = new Printer($connector);

    //     /* Name of shop */
    //     $printer -> setJustification(Printer::JUSTIFY_CENTER);
    //     $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
    //     $printer -> text($nama_store."\n");
    //     $printer -> selectPrintMode();
    //     $printer -> feed();

    //     /* Title of receipt */
    //     $printer -> text("_________________________________\n");
    //     $printer -> feed();


    //     /* Title of receipt */
    //     $printer -> setJustification(Printer::JUSTIFY_LEFT);
    //     $printer -> text("Kode Order : ".$kode_temp."\nType Order : ".$type_order." - ".$keterangan_order."\nTanggal  : ".$tanggal."\n");

    //     $printer -> text("_________________________________\n");
    //     $printer -> feed();


    //     // Ket nama

    //     $printer -> text($desc);
    //     $printer -> text("_________________________________\n");


    //     /* Items */
    //     $printer -> feed();
    //     $printer -> setJustification(Printer::JUSTIFY_LEFT);
    //     foreach ($items as $item) {
    //         $printer -> text($item);
    //     }
    //     $printer -> text("_________________________________\n");
    //     $printer -> feed();

    //     $row_belum = DB::table('pos_belum')
    //             ->where('kode_temp', $kode_temp)
    //             ->first();

    //     if($row_belum->note != null){
    //         $printer -> text("NOTE\n");
    //         $printer -> text($row_belum->note."\n");
    //         $printer -> text("_________________________________\n");
    //         $printer -> feed();
    //     }


    //     /* Cut the receipt and open the cash drawer */
    //     $printer -> cut();
    //     // $printer -> pulse();

    //     $printer -> close();

    // }

    // public function print_2($id, $kode_temp, $type_order, $keterangan_order){
    //     // $connector =  new WindowsPrintConnector("POS-80C-Network");
    //     date_default_timezone_set('Asia/Jakarta');
    //     $tanggal = date('d-m-Y H:i');
    //     $nama_store = session('nama_store');
    //     $ip_printer = DB::table('pos_product_kategori')
    //             ->where('id_kategori', $id)
    //             ->first();

    //     if($ip_printer->print_by == "nama"){
    //         $connector = new WindowsPrintConnector($ip_printer->ip_printer2);
    //     }else{
    //         $connector = new NetworkPrintConnector($ip_printer->ip_printer2);
    //     }

    //     /* Information for the receipt */

    //     $items = array();
    //     $totalQty=0;
    //     $totalHarga=0;
    //     $pembayaran = DB::table('pos_belum_bayar')
    //             ->where('id_kategori', $id)
    //             ->where('kode_temp', $kode_temp)
    //             ->get();
    //     foreach ($pembayaran as $key => $value) {
    //         $totalQty+= $value->qty;
    //         $totalHarga+= $value->total;
    //         array_push($items, new item($value->nama_item, $value->qty));
    //     }

    //     $desc = new item('Nama Item', 'Qty');

    //     $printer = new Printer($connector);

    //     /* Name of shop */
    //     $printer -> setJustification(Printer::JUSTIFY_CENTER);
    //     $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
    //     $printer -> text($nama_store."\n");
    //     $printer -> selectPrintMode();
    //     $printer -> feed();

    //     /* Title of receipt */
    //     $printer -> text("_________________________________\n");
    //     $printer -> feed();


    //     /* Title of receipt */
    //     $printer -> setJustification(Printer::JUSTIFY_LEFT);
    //     $printer -> text("Kode Order : ".$kode_temp."\nType Order : ".$type_order." - ".$keterangan_order."\nTanggal  : ".$tanggal."\n");

    //     $printer -> text("_________________________________\n");
    //     $printer -> feed();


    //     // Ket nama

    //     $printer -> text($desc);
    //     $printer -> text("_________________________________\n");


    //     /* Items */
    //     $printer -> feed();
    //     $printer -> setJustification(Printer::JUSTIFY_LEFT);
    //     foreach ($items as $item) {
    //         $printer -> text($item);
    //     }
    //     $printer -> text("_________________________________\n");
    //     $printer -> feed();

    //     $row_belum = DB::table('pos_belum')
    //             ->where('kode_temp', $kode_temp)
    //             ->first();

    //     if($row_belum->note != null){
    //         $printer -> text("NOTE\n");
    //         $printer -> text($row_belum->note."\n");
    //         $printer -> text("_________________________________\n");
    //         $printer -> feed();
    //     }

    //     /* Cut the receipt and open the cash drawer */
    //     $printer -> cut();
    //     // $printer -> pulse();

    //     $printer -> close();

    // }

    // public function print3($id, $kode_temp, $type_order, $keterangan_order){
    //     date_default_timezone_set('Asia/Jakarta');
    //     $tanggal = date('d-m-Y H:i');
    //     $nama_store = session('nama_store');
    //     $ip_printer = DB::table('pos_product_kategori')
    //             ->where('id_kategori', $id)
    //             ->first();

    //     if($ip_printer->print_by == "nama"){
    //         $connector = new WindowsPrintConnector($ip_printer->ip_printer3);
    //     }else{
    //         $connector = new NetworkPrintConnector($ip_printer->ip_printer3);
    //     }

    //     /* Information for the receipt */

    //     $items = array();
    //     $totalQty=0;
    //     $totalHarga=0;
    //     $pembayaran = DB::table('pos_front_payment')
    //             ->where('id_kategori', $id)
    //             ->where('kode_temp', $kode_temp)
    //             ->get();
    //     foreach ($pembayaran as $key => $value) {
    //         $totalQty+= $value->qty;
    //         $totalHarga+= $value->total;
    //         array_push($items, new item($value->nama_tiket, $value->qty));
    //     }

    //     $desc = new item('Nama Item', 'Qty');

    //     $printer = new Printer($connector);

    //     /* Name of shop */
    //     $printer -> setJustification(Printer::JUSTIFY_CENTER);
    //     $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
    //     $printer -> text($nama_store."\n");
    //     $printer -> selectPrintMode();
    //     $printer -> feed();

    //     /* Title of receipt */
    //     $printer -> text("_________________________________\n");
    //     $printer -> feed();


    //     /* Title of receipt */
    //     $printer -> setJustification(Printer::JUSTIFY_LEFT);
    //     $printer -> text("Kode Order : ".$kode_temp."\nType Order : ".$type_order." - ".$keterangan_order."\nTanggal  : ".$tanggal."\n");

    //     $printer -> text("_________________________________\n");
    //     $printer -> feed();


    //     // Ket nama

    //     $printer -> text($desc);
    //     $printer -> text("_________________________________\n");


    //     /* Items */
    //     $printer -> feed();
    //     $printer -> setJustification(Printer::JUSTIFY_LEFT);
    //     foreach ($items as $item) {
    //         $printer -> text($item);
    //     }
    //     $printer -> text("_________________________________\n");
    //     $printer -> feed();

    //     $row_belum = DB::table('pos_belum')
    //             ->where('kode_temp', $kode_temp)
    //             ->first();

    //     if($row_belum->note != null){
    //         $printer -> text("NOTE\n");
    //         $printer -> text($row_belum->note."\n");
    //         $printer -> text("_________________________________\n");
    //         $printer -> feed();
    //     }


    //     /* Cut the receipt and open the cash drawer */
    //     $printer -> cut();
    //     // $printer -> pulse();

    //     $printer -> close();

    // }

    // Print Kategori
    public function print($id, $kode_temp, $type_order, $keterangan_order)
    {
        // $connector =  new WindowsPrintConnector("POS-80C-Network");
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('d-m-Y H:i');
        $nama_store = session('nama_store');
        $ip_printer = DB::table('pos_product_kategori')
            ->where('id_kategori', $id)
            ->first();

        if ($ip_printer->print_by == "nama") {
            $connector = new WindowsPrintConnector($ip_printer->ip_printer1);
        } else {
            $connector = new NetworkPrintConnector($ip_printer->ip_printer1);
        }

        /* Information for the receipt */

        $items = array();
        $totalQty = 0;
        $totalHarga = 0;
        $pembayaran = DB::table('pos_belum_bayar')
            ->where('id_kategori', $id)
            ->where('kode_temp', $kode_temp)
            ->get();

        foreach ($pembayaran as $key => $value) {
            $totalQty += $value->qty;
            $totalHarga += $value->total;
            array_push($items, new item($value->nama_item, $value->qty));
        }

        $desc = new item('Nama Item', 'Qty');

        $printer = new Printer($connector);

        /* Name of shop */
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer->setFont(Printer::FONT_B);
        $printer->setTextSize(2, 2);
        $printer->text($nama_store . "\n");
        $printer->selectPrintMode();
        $printer->setFont(Printer::FONT_B);
        $printer->setTextSize(2, 2);
        $printer->feed();

        /* Title of receipt */
        $printer->text("___________\n");
        $printer->feed();


        /* Title of receipt */
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Kode Order : " . $kode_temp . "\nType Order : " . $type_order . " - " . $keterangan_order . "\nTanggal  : " . $tanggal . "\n");

        $printer->text("____________\n");
        $printer->feed();


        // Ket nama

        $printer->text($desc);
        $printer->text("____________\n");


        /* Items */
        $printer->feed();
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        foreach ($pembayaran as $key => $value) {
            $printer->text($value->nama_item . " - (" . $value->qty . ")\n");
            $additional_menu = json_decode($value->additional_menu);
            foreach ($additional_menu as $item) {
                if (!empty($item->additional_menu)) {
                    $nama_additional_menu = $item->additional_menu->nama_additional_menu;
                } else {
                    $nama_additional_menu = $item->nama_additional_menu;
                }
                $printer->text(" + $nama_additional_menu \n");
            }
            $printer->feed();
        }
        $printer->text("____________\n");
        $printer->feed();

        $row_belum = DB::table('pos_belum')
            ->where('kode_temp', $kode_temp)
            ->first();

        if ($row_belum->note != null) {
            $printer->text("NOTE\n");
            $printer->text($row_belum->note . "\n");
            $printer->text("____________\n");
            $printer->feed();
        }

        return Log::info([$printer->getPrintConnector()]);
        /* Cut the receipt and open the cash drawer */
        $printer->cut();
        // $printer -> pulse();

        $printer->close();
    }

    public function print_2($id, $kode_temp, $type_order, $keterangan_order)
    {
        // $connector =  new WindowsPrintConnector("POS-80C-Network");
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('d-m-Y H:i');
        $nama_store = session('nama_store');
        $ip_printer = DB::table('pos_product_kategori')
            ->where('id_kategori', $id)
            ->first();

        if ($ip_printer->print_by == "nama") {
            $connector = new WindowsPrintConnector($ip_printer->ip_printer2);
        } else {
            $connector = new NetworkPrintConnector($ip_printer->ip_printer2);
        }

        /* Information for the receipt */

        $items = array();
        $totalQty = 0;
        $totalHarga = 0;
        $pembayaran = DB::table('pos_belum_bayar')
            ->where('id_kategori', $id)
            ->where('kode_temp', $kode_temp)
            ->get();
        foreach ($pembayaran as $key => $value) {
            $totalQty += $value->qty;
            $totalHarga += $value->total;
            array_push($items, new item($value->nama_item, $value->qty));
        }

        $desc = new item('Nama Item', 'Qty');

        $printer = new Printer($connector);

        /* Name of shop */
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);

        $printer->setFont(Printer::FONT_B);
        $printer->setTextSize(2, 2);
        $printer->text($nama_store . "\n");
        $printer->selectPrintMode();

        $printer->setFont(Printer::FONT_B);
        $printer->setTextSize(2, 2);
        $printer->feed();

        /* Title of receipt */
        $printer->text("____________\n");
        $printer->feed();


        /* Title of receipt */
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Kode Order : " . $kode_temp . "\nType Order : " . $type_order . " - " . $keterangan_order . "\nTanggal  : " . $tanggal . "\n");

        $printer->text("____________\n");
        $printer->feed();


        // Ket nama

        $printer->text($desc);
        $printer->text("____________\n");


        /* Items */
        $printer->feed();
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        foreach ($pembayaran as $key => $value) {
            $printer->text($value->nama_item . " - (" . $value->qty . ")\n");
            $printer->feed();
        }
        $printer->text("____________\n");
        $printer->feed();

        $row_belum = DB::table('pos_belum')
            ->where('kode_temp', $kode_temp)
            ->first();

        if ($row_belum->note != null) {
            $printer->text("NOTE\n");
            $printer->text($row_belum->note . "\n");
            $printer->text("____________\n");
            $printer->feed();
        }
        return Log::info([$printer->getPrintConnector()]);
        /* Cut the receipt and open the cash drawer */
        $printer->cut();
        // $printer -> pulse();

        $printer->close();
    }

    public function print3($id, $kode_temp, $type_order, $keterangan_order)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('d-m-Y H:i');
        $nama_store = session('nama_store');
        $ip_printer = DB::table('pos_product_kategori')
            ->where('id_kategori', $id)
            ->first();

        if ($ip_printer->print_by == "nama") {
            $connector = new WindowsPrintConnector($ip_printer->ip_printer3);
        } else {
            $connector = new NetworkPrintConnector($ip_printer->ip_printer3);
        }

        /* Information for the receipt */

        $items = array();
        $totalQty = 0;
        $totalHarga = 0;
        $pembayaran = DB::table('pos_front_payment')
            ->where('id_kategori', $id)
            ->where('kode_temp', $kode_temp)
            ->get();
        foreach ($pembayaran as $key => $value) {
            $totalQty += $value->qty;
            $totalHarga += $value->total;
            array_push($items, new item($value->nama_tiket, $value->qty));
        }

        $desc = new item('Nama Item', 'Qty');

        $printer = new Printer($connector);

        /* Name of shop */
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);

        $printer->setFont(Printer::FONT_B);
        $printer->setTextSize(2, 2);
        $printer->text($nama_store . "\n");
        $printer->selectPrintMode();

        $printer->setFont(Printer::FONT_B);
        $printer->setTextSize(2, 2);
        $printer->feed();

        /* Title of receipt */
        $printer->text("____________\n");
        $printer->feed();


        /* Title of receipt */
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Kode Order : " . $kode_temp . "\nType Order : " . $type_order . " - " . $keterangan_order . "\nTanggal  : " . $tanggal . "\n");

        $printer->text("____________\n");
        $printer->feed();


        // Ket nama

        $printer->text($desc);
        $printer->text("____________\n");


        /* Items */
        $printer->feed();
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        foreach ($items as $item) {
            $printer->text($item);
        }
        $printer->text("____________\n");
        $printer->feed();

        $row_belum = DB::table('pos_belum')
            ->where('kode_temp', $kode_temp)
            ->first();

        if ($row_belum->note != null) {
            $printer->text("NOTE\n");
            $printer->text($row_belum->note . "\n");
            $printer->text("____________\n");
            $printer->feed();
        }

        return Log::info([$printer->getPrintConnector()]);
        /* Cut the receipt and open the cash drawer */
        $printer->cut();
        // $printer -> pulse();

        $printer->close();
    }

    public function print_deposit($request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('d-m-Y H:i');
        $nama_store = session('nama_store');
        $connector = new WindowsPrintConnector('KasirMain');

        /* Information for the receipt */

        $items = array();
        $totalQty = 0;
        $totalHarga = 0;


        $desc = new item('Total Deposit', $request->total);
        $k100 = new item('100.000 X ', $request->total);

        $printer = new Printer($connector);

        /* Name of shop */
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer->text("MARINARA'S PIZZA\n");
        $printer->selectPrintMode();
        $printer->text("Jln.Raja Isa Komp.Ruko KDA Junction\nBlok.D No.05");
        $printer->feed();

        /* Title of receipt */
        $printer->text("_________________________________\n");
        $printer->feed();


        /* Title of receipt */
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Deposit Store : " . $tanggal . "\n");
        $printer->text("Store         : " . $nama_store . "\n");
        $printer->text("Kasir         : " . session('nama') . "\n");
        $printer->text("_________________________________\n");
        $printer->feed();


        // Ket nama
        $printer->text($desc);
        $printer->text("_________________________________\n");


        /* Items */
        $printer->feed();
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("100.000 X " . $request->k100 . " = " . number_format((100000 * $request->k100), 0, ",", ".") . "\n");
        $printer->text("50.000 X " . $request->k50 . " = " . number_format((50000 * $request->k50), 0, ",", ".") . "\n");
        $printer->text("20.000 X " . $request->k20 . " = " . number_format((20000 * $request->k20), 0, ",", ".") . "\n");
        $printer->text("10.000 X " . $request->k10 . " = " . number_format((10000 * $request->k10), 0, ",", ".") . "\n");
        $printer->text("5.000 X " . $request->k5 . " = " . number_format((5000 * $request->k5), 0, ",", ".") . "\n");
        $printer->text("2.000 X " . $request->k2 . " = " . number_format((2000 * $request->k2), 0, ",", ".") . "\n");
        $printer->text("1.000 X " . $request->k1 . " = " . number_format((1000 * $request->k1), 0, ",", ".") . "\n");
        $printer->feed();


        /* Cut the receipt and open the cash drawer */
        $printer->cut();
        // $printer -> pulse();

        $printer->close();
    }

    // Print Closing PC
    public function print_close_pos()
    {
        date_default_timezone_set('Asia/Jakarta');
        $id_store = session('id_store');
        $id_kasir = session('id');
        $tanggal = date("Y-m-d");
        $dataitem = DB::select("SELECT pos_activity_item.nama_item, harga, SUM(qty) as total_qty, SUM(total) as total_bayar FROM pos_activity_item WHERE id_store=$id_store AND id_kasir=$id_kasir AND date(created_at)='$tanggal' AND status='success' GROUP BY nama_item, harga");
        $deposit = DB::table('pos_deposit')->where('id_store', $id_store)->where('id_kasir', $id_kasir)->where('tanggal', $tanggal)->where('status', 1)->select('deposit')->get();
        $dataip = DB::select("SELECT pos_activity_item.id_kategori, pos_product_kategori.ip_printer1 FROM pos_activity_item INNER JOIN pos_product_kategori ON pos_activity_item.id_kategori=pos_product_kategori.id_kategori WHERE id_store=$id_store AND date(created_at)='$tanggal' AND status='success' GROUP BY pos_activity_item.id_kategori, pos_product_kategori.ip_printer1");
        $tunai = DB::select("SELECT SUM(cash-kembalian) as total_cash FROM pos_activity WHERE id_store=$id_store AND id_employee=$id_kasir AND tanggal='$tanggal' AND status='success'");
        $debit = DB::select("SELECT tipe_payment, SUM(debit_cash) as total_debit FROM pos_activity WHERE id_store=$id_store AND id_employee=$id_kasir AND tanggal='$tanggal' AND status='success' AND tipe_payment!='Tunai' GROUP BY tipe_payment");
        $diskon = DB::select("SELECT SUM(subtotal-total) as total_diskon FROM pos_activity WHERE id_store=$id_store AND id_employee=$id_kasir AND tanggal='$tanggal' AND status='success'");

        $tanggal = date('d-m-Y H:i');
        $nama_store = session('nama_store');
        $nama_printer = DB::table('pos_store')->where('id_store', session('id_store'))->first()->print_kasir;
        $connector = new WindowsPrintConnector($nama_printer);

        /* Information for the receipt */

        $items = array();
        $depo = array();
        $tun = array();
        $deb = array();
        $total_cash = 0;
        $dis = 0;

        foreach ($dataitem as $key => $value) {
            array_push($items, new item($value->nama_item . ' (' . $value->total_qty . ' X ' . number_format($value->harga, 0, ",", ".") . ')', number_format($value->total_bayar, 0, ",", ".")));
        }

        foreach ($deposit as $key => $value) {
            array_push($depo, new item('Deposit', number_format($value->deposit, 0, ",", ".")));
            $total_cash += $value->deposit;
        }

        foreach ($tunai as $key => $value) {
            array_push($tun, new item('Tunai', number_format($value->total_cash, 0, ",", ".")));
            $total_cash += $value->total_cash;
        }

        foreach ($debit as $key => $value) {
            array_push($deb, new item($value->tipe_payment, number_format($value->total_debit, 0, ",", ".")));
        }

        foreach ($diskon as $key => $value) {
            $dis += $value->total_diskon;
        }

        $printer = new Printer($connector);

        /* Name of shop */
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer->text("MARINARA'S PIZZA\n");
        $printer->selectPrintMode();
        $printer->text("Jln.Raja Isa Komp.Ruko KDA Junction\nBlok.D No.05");
        $printer->feed();

        /* Title of receipt */
        $printer->text("_________________________________\n");
        $printer->feed();


        /* Title of receipt */
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Tanggal : " . $tanggal . "\n");
        $printer->text("Store   : " . $nama_store . "\n");
        $printer->text("Kasir   : " . session('nama') . "\n");
        $printer->text("_________________________________\n");
        $printer->feed();


        /* Items */
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        foreach ($items as $item) {
            $printer->text($item);
        }
        $printer->text("_________________________________\n");
        $printer->feed();
        foreach ($depo as $item) {
            $printer->text($item);
        }
        foreach ($tun as $item) {
            $printer->text($item);
        }
        $printer->text(new Item('Diskon', number_format($dis, 0, ",", ".")));
        $printer->text(new Item('Total Cash', number_format($total_cash, 0, ",", ".")));
        $printer->feed();
        foreach ($deb as $item) {
            $printer->text($item);
        }
        $printer->feed(2);


        /* Cut the receipt and open the cash drawer */
        $printer->cut();
        // $printer -> pulse();

        $printer->close();

        // Print Kitchen
        // foreach ($dataip as $key => $value) {
        //     if($value->ip_printer1 != null){
        //         self::print_close_kitchen();
        //     }
        // }
        return redirect('close');
    }


    public function print_close_kitchen()
    {
        date_default_timezone_set('Asia/Jakarta');
        $id_store = session('id_store');
        $id_kasir = session('id');
        $tanggal = date("Y-m-d");
        $dataitem = DB::select("SELECT pos_activity_item.nama_item, harga, SUM(qty) as total_qty, SUM(total) as total_bayar FROM pos_activity_item WHERE id_store=$id_store AND id_kasir=$id_kasir AND date(created_at)='$tanggal' AND status='success' GROUP BY nama_item, harga");
        $dataip = DB::select("SELECT pos_activity_item.id_kategori, pos_product_kategori.ip_printer1 FROM pos_activity_item INNER JOIN pos_product_kategori ON pos_activity_item.id_kategori=pos_product_kategori.id_kategori WHERE id_store=$id_store AND date(created_at)='$tanggal' AND status='success' GROUP BY pos_activity_item.id_kategori, pos_product_kategori.ip_printer1");

        $tanggal = date('d-m-Y H:i');
        $nama_store = session('nama_store');
        $ip = 0;
        foreach ($dataip as $key => $value) {
            $ip = $value->ip_printer1;
        }
        if ($ip == 0) {
            return redirect('close');
        }
        $connector = new NetworkPrintConnector($ip);
        // return $ip;
        /* Information for the receipt */

        $items = array();
        $depo = array();
        $tun = array();
        $deb = array();
        $total_cash = 0;
        $dis = 0;

        foreach ($dataitem as $key => $value) {
            array_push($items, new item($value->nama_item, $value->total_qty));
        }


        $printer = new Printer($connector);

        /* Name of shop */
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer->text("MARINARA'S PIZZA\n");
        $printer->selectPrintMode();
        $printer->text("Jln.Raja Isa Komp.Ruko KDA Junction\nBlok.D No.05");
        $printer->feed();

        /* Title of receipt */

        $printer->text("_________________________________\n");
        $printer->feed();


        /* Title of receipt */
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Tanggal : " . $tanggal . "\n");
        $printer->text("Store   : " . $nama_store . "\n");
        $printer->text("_________________________________\n");
        $printer->feed();
        $printer->text(new item('Nama Item', 'Qty'));
        $printer->text("_________________________________\n");;
        $printer->feed();

        /* Items */
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        foreach ($items as $item) {
            $printer->text($item);
        }
        $printer->text("_________________________________\n");
        $printer->feed(2);


        /* Cut the receipt and open the cash drawer */
        $printer->cut();
        // $printer -> pulse();

        $printer->close();
    }

    public function test_print()
    {
        date_default_timezone_set('Asia/Jakarta');

        $connector = new NetworkPrintConnector('10.154.30.208');

        $printer = new Printer($connector);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer->text("Marinaras\n");
        $printer->selectPrintMode();
        $printer->text("Jln.Raja Isa Komp.Ruko KDA Junction\nBlok.D No.05");
        $printer->feed();

        /* Title of receipt */

        $printer->text("_________________________________\n");

        $printer->text("TESTING\n");
        $printer->text("_________________________________\n");
        $printer->feed(2);


        /* Cut the receipt and open the cash drawer */
        $printer->cut();
        // $printer -> pulse();

        $printer->close();
    }

    public function post_pos_depo(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('Y-m-d');
        DB::table('pos_deposit')->insert([
            'id_store' => session('id_store'),
            'id_kasir' => session('id'),
            'tanggal' => $tanggal,
            'deposit' => 2000000,
            'status' => 1
        ]);

        // Print Deposit
        // self::print_deposit($request);

        return redirect('dashboard');
    }

    public function test_printer1()
    {
        // phpinfo();
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date("Y-m-d");
        $tanggal = date('d-m-Y H:i');
        $nama_store = session('nama_store');
        $nama_printer = DB::table('pos_store')->where('id_store', session('id_store'))->first()->print_kasir;
        $connector = new WindowsPrintConnector($nama_printer);

        // dd(is_readable("images/icon/logoM.png"));
        $printer = new Printer($connector);
        $logo = EscposImage::load("images/icon/mediumLogo.png", false);

        /* Name of shop */


        /* Print top logo */
        $printer->setJustification(Printer::JUSTIFY_CENTER);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer->bitImageColumnFormat($logo, Printer::IMG_DOUBLE_WIDTH | Printer::IMG_DOUBLE_HEIGHT);
        // $printer -> text("MARINARA'S PIZZA\n");
        $printer->selectPrintMode();
        $printer->text("Jln.Raja Isa Komp.Ruko KDA Junction\nBlok.D No.05");
        $printer->feed();

        /* Title of receipt */
        $printer->text("_________________________________\n");
        $printer->feed();


        /* Title of receipt */
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Tanggal : " . $tanggal . "\n");
        $printer->text("Store   : " . $nama_store . "\n");
        $printer->text("Kasir   : " . session('nama') . "\n");
        $printer->text("_________________________________\n");
        $printer->feed();


        /* Items */
        $printer->setJustification(Printer::JUSTIFY_LEFT);

        $printer->text("_________________________________\n");
        $printer->feed();
        $printer->setFont(Printer::FONT_A);
        $printer->setTextSize(1, 1);
        $printer->text("The quick brown fox jumps over the lazy dog.\n");
        $printer->feed(2);


        /* Cut the receipt and open the cash drawer */
        $printer->cut();
        // $printer -> pulse();

        $printer->close();
    }

    public function test_print_dapur($id, $kode_temp, $type_order, $keterangan_order)
    {
        // $connector =  new WindowsPrintConnector("POS-80C-Network");
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('d-m-Y H:i');
        $nama_store = session('nama_store');
        $ip_printer = DB::table('pos_product_kategori')
            ->where('id_kategori', $id)
            ->get();

        // $connector = new NetworkPrintConnector($ip_printer[0]->ip_printer1);
        $connector = new WindowsPrintConnector("KasirMain");


        /* Information for the receipt */

        $items = array();
        $totalQty = 0;
        $totalHarga = 0;
        $pembayaran = DB::table('pos_belum_bayar')
            ->where('id_kategori', $id)
            ->where('kode_temp', $kode_temp)
            ->get();

        foreach ($pembayaran as $key => $value) {
            $totalQty += $value->qty;
            $totalHarga += $value->total;
            array_push($items, new item($value->nama_item, $value->qty));
        }

        $desc = new item('Nama Item', 'Qty');

        $printer = new Printer($connector);

        /* Name of shop */
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer->text($nama_store . "\n");
        $printer->selectPrintMode();
        $printer->feed();

        /* Title of receipt */
        $printer->text("_________________________________\n");
        $printer->feed();


        /* Title of receipt */
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Kode Order : " . $kode_temp . "\nType Order : " . $type_order . " - " . $keterangan_order . "\nTanggal  : " . $tanggal . "\n");

        $printer->text("_________________________________\n");
        $printer->feed();


        // Ket nama

        $printer->text($desc);
        $printer->text("_________________________________\n");


        /* Items */
        $printer->feed();
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        foreach ($items as $item) {
            $printer->text($item);
        }
        $printer->text("_________________________________\n");
        $printer->feed();


        /* Cut the receipt and open the cash drawer */
        $printer->cut();
        // $printer -> pulse();

        $printer->close();
    }

    // public function test_print_dapur_revisi($id, $kode_temp, $type_order, $keterangan_order){
    //     // $connector =  new WindowsPrintConnector("POS-80C-Network");
    //     date_default_timezone_set('Asia/Jakarta');
    //     $tanggal = date('d-m-Y H:i');
    //     $nama_store = session('nama_store');
    //     $ip_printer = DB::table('pos_product_kategori')
    //             ->where('id_kategori', $id)
    //             ->first();

    //     if($ip_printer->print_by == "nama"){
    //         $connector = new WindowsPrintConnector($ip_printer->ip_printer1);
    //     }else{
    //         $connector = new NetworkPrintConnector($ip_printer->ip_printer1);
    //     }


    //     /* Information for the receipt */

    //     $items = array();
    //     $items_revisi = array();
    //     $totalQty=0;
    //     $totalHarga=0;
    //     $pembayaran = DB::table('pos_belum_bayar')
    //             ->where('id_kategori', $id)
    //             ->where('kode_temp', $kode_temp)
    //             ->get();

    //     $p_revisi = DB::table('pos_revisi_bayar')
    //             ->where('id_kategori', $id)
    //             ->where('kode_temp', $kode_temp)
    //             ->get();

    //     foreach ($pembayaran as $key => $value) {
    //         $totalQty+= $value->qty;
    //         $totalHarga+= $value->total;
    //         array_push($items, new item($value->nama_item, $value->qty));
    //     }

    //     foreach ($p_revisi as $key => $value) {
    //         $totalQty+= $value->qty;
    //         $totalHarga+= $value->total;
    //         array_push($items_revisi, new item($value->nama_item, $value->qty));
    //     }

    //     $desc = new item('Nama Item', 'Qty');

    //     $printer = new Printer($connector);

    //     /* Name of shop */
    //     $printer -> setJustification(Printer::JUSTIFY_CENTER);
    //     $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
    //     $printer -> text($nama_store."\n");
    //     $printer -> text("REVISI ORDER\n");
    //     $printer -> selectPrintMode();
    //     $printer -> feed();

    //     /* Title of receipt */
    //     $printer -> text("_________________________________\n");
    //     $printer -> feed();


    //     /* Title of receipt */
    //     $printer -> setJustification(Printer::JUSTIFY_LEFT);
    //     $printer -> text("Kode Order : ".$kode_temp."\nType Order : ".$type_order." - ".$keterangan_order."\nTanggal  : ".$tanggal."\n");

    //     $printer -> text("_________________________________\n");
    //     $printer -> feed();


    //     // Ket nama

    //     $printer -> text($desc);
    //     $printer -> text("_________________________________\n");


    //     /* Items */
    //     $printer -> feed();
    //     $printer -> setJustification(Printer::JUSTIFY_LEFT);
    //     $printer -> text("FROM\n");
    //     foreach ($items as $item) {
    //         $printer -> text($item);
    //     }
    //     $printer -> text("_________________________________\n");
    //     $printer -> feed();
    //     $printer -> feed();
    //     $printer -> setJustification(Printer::JUSTIFY_LEFT);
    //     $printer -> text("TO\n");
    //     foreach ($items_revisi as $item) {
    //         $printer -> text($item);    
    //     }
    //     $printer -> text("_________________________________\n");


    //     /* Cut the receipt and open the cash drawer */
    //     $printer -> cut();
    //     // $printer -> pulse();

    //     $printer -> close();

    // }

    public function test_print_dapur_revisi($id, $kode_temp, $type_order, $keterangan_order)
    {
        // $connector =  new WindowsPrintConnector("POS-80C-Network");
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('d-m-Y H:i');
        $nama_store = session('nama_store');
        $ip_printer = DB::table('pos_product_kategori')
            ->where('id_kategori', $id)
            ->first();

        if ($ip_printer->print_by == "nama") {
            $connector = new WindowsPrintConnector($ip_printer->ip_printer1);
        } else {
            $connector = new NetworkPrintConnector($ip_printer->ip_printer1);
        }


        /* Information for the receipt */

        $items = array();
        $items_revisi = array();
        $totalQty = 0;
        $totalHarga = 0;
        $pembayaran = DB::table('pos_belum_bayar')
            ->where('id_kategori', $id)
            ->where('kode_temp', $kode_temp)
            ->get();

        $p_revisi = DB::table('pos_revisi_bayar')
            ->where('id_kategori', $id)
            ->where('kode_temp', $kode_temp)
            ->get();

        foreach ($pembayaran as $key => $value) {
            $totalQty += $value->qty;
            $totalHarga += $value->total;
            array_push($items, new item($value->nama_item, $value->qty));
        }

        foreach ($p_revisi as $key => $value) {
            $totalQty += $value->qty;
            $totalHarga += $value->total;
            array_push($items_revisi, new item($value->nama_item, $value->qty));
        }

        $desc = new item('Nama Item', 'Qty');

        $printer = new Printer($connector);

        /* Name of shop */
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer->setFont(Printer::FONT_B);
        $printer->setTextSize(2, 2);
        $printer->text($nama_store . "\n");
        $printer->text("REVISI ORDER\n");
        $printer->selectPrintMode();
        $printer->setFont(Printer::FONT_B);
        $printer->setTextSize(2, 2);
        $printer->feed();

        /* Title of receipt */
        $printer->text("____________\n");
        $printer->feed();


        /* Title of receipt */
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Kode Order : " . $kode_temp . "\nType Order : " . $type_order . " - " . $keterangan_order . "\nTanggal  : " . $tanggal . "\n");

        $printer->text("____________\n");
        $printer->feed();


        // Ket nama

        $printer->text($desc);
        $printer->text("____________\n");


        /* Items */
        $printer->feed();
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("FROM\n");
        $printer->feed();
        foreach ($pembayaran as $key => $value) {
            $printer->text($value->nama_item . " - (" . $value->qty . ")\n");
            $printer->feed();
        }
        $printer->text("____________\n");
        $printer->feed();
        $printer->feed();
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("TO\n");
        $printer->feed();
        foreach ($p_revisi as $key => $value) {
            $printer->text($value->nama_item . " - (" . $value->qty . ")\n");
            $printer->feed();
        }
        $printer->text("___________\n");

        return $printer->getPrintConnector();
        /* Cut the receipt and open the cash drawer */
        $printer->cut();
        // $printer -> pulse();

        $printer->close();
    }

    // public function notetest_print_dapur_revisi($id, $kode_temp, $type_order, $keterangan_order, $note){
    //     // $connector =  new WindowsPrintConnector("POS-80C-Network");
    //     date_default_timezone_set('Asia/Jakarta');
    //     $tanggal = date('d-m-Y H:i');
    //     $nama_store = session('nama_store');
    //     $ip_printer = DB::table('pos_product_kategori')
    //             ->where('id_kategori', $id)
    //             ->first();

    //     if($ip_printer->print_by == "nama"){
    //         $connector = new WindowsPrintConnector($ip_printer->ip_printer1);
    //     }else{
    //         $connector = new NetworkPrintConnector($ip_printer->ip_printer1);
    //     }

    //     $printer = new Printer($connector);

    //     /* Name of shop */
    //     $printer -> setJustification(Printer::JUSTIFY_CENTER);
    //     $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
    //     $printer -> text($nama_store."\n");
    //     $printer -> text("REVISI NOTE\n");
    //     $printer -> selectPrintMode();
    //     $printer -> feed();

    //     /* Title of receipt */
    //     $printer -> text("_________________________________\n");
    //     $printer -> feed();


    //     /* Title of receipt */
    //     $printer -> setJustification(Printer::JUSTIFY_LEFT);
    //     $printer -> text("No Order : ".$kode_temp."\nType Order : ".$type_order." - ".$keterangan_order."\nTanggal  : ".$tanggal."\n");

    //     $printer -> text("_________________________________\n");
    //     $printer -> feed();

    //     $printer -> setJustification(Printer::JUSTIFY_LEFT);
    //     $printer -> text("NOTE\n");
    //     $printer -> text($note."\n");
    //     $printer -> text("_________________________________\n");
    //     $printer -> feed();

    //     $printer -> cut();

    //     $printer -> close();

    // }

    // public function no_test_print_dapur($id, $kode_temp, $type_order, $keterangan_order){
    //     // $connector =  new WindowsPrintConnector("POS-80C-Network");
    //     date_default_timezone_set('Asia/Jakarta');
    //     $tanggal = date('d-m-Y H:i');
    //     $nama_store = session('nama_store');
    //     $ip_printer = DB::table('pos_product_kategori')
    //             ->where('id_kategori', $id)
    //             ->first();

    //     if($ip_printer->print_by == "nama"){
    //         $connector = new WindowsPrintConnector($ip_printer->ip_printer1);
    //     }else{
    //         $connector = new NetworkPrintConnector($ip_printer->ip_printer1);
    //     }


    //     /* Information for the receipt */

    //     $items = array();
    //     $totalQty=0;
    //     $totalHarga=0;
    //     $pembayaran = DB::table('pos_belum_bayar')
    //             ->where('id_kategori', $id)
    //             ->where('kode_temp', $kode_temp)
    //             ->get();

    //     foreach ($pembayaran as $key => $value) {
    //         $totalQty+= $value->qty;
    //         $totalHarga+= $value->total;
    //         array_push($items, new item($value->nama_item, $value->qty));
    //     }

    //     $desc = new item('Nama Item', 'Qty');

    //     $printer = new Printer($connector);

    //     /* Name of shop */
    //     $printer -> setJustification(Printer::JUSTIFY_CENTER);
    //     $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
    //     $printer -> text($nama_store."\n");
    //     $printer -> text("ORDER CANCEL\n");
    //     $printer -> selectPrintMode();
    //     $printer -> feed();

    //     /* Title of receipt */
    //     $printer -> text("_________________________________\n");
    //     $printer -> feed();

    //     /* Title of receipt */
    //     $printer -> setJustification(Printer::JUSTIFY_LEFT);
    //     $printer -> text("Kode Order : ".$kode_temp."\nType Order : ".$type_order." - ".$keterangan_order."\nTanggal  : ".$tanggal."\n");

    //     $printer -> text("_________________________________\n");
    //     $printer -> feed();


    //     // Ket nama

    //     $printer -> text($desc);
    //     $printer -> text("_________________________________\n");


    //     /* Items */
    //     $printer -> feed();
    //     $printer -> setJustification(Printer::JUSTIFY_LEFT);
    //     foreach ($items as $item) {
    //         $printer -> text($item);
    //     }
    //     $printer -> text("_________________________________\n");
    //     $printer -> feed();


    //     /* Cut the receipt and open the cash drawer */
    //     $printer -> cut();
    //     // $printer -> pulse();

    //     $printer -> close();

    // }

    public function notetest_print_dapur_revisi($id, $kode_temp, $type_order, $keterangan_order, $note)
    {
        // $connector =  new WindowsPrintConnector("POS-80C-Network");
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('d-m-Y H:i');
        $nama_store = session('nama_store');
        $ip_printer = DB::table('pos_product_kategori')
            ->where('id_kategori', $id)
            ->first();

        if ($ip_printer->print_by == "nama") {
            $connector = new WindowsPrintConnector($ip_printer->ip_printer1);
        } else {
            $connector = new NetworkPrintConnector($ip_printer->ip_printer1);
        }

        $printer = new Printer($connector);

        /* Name of shop */
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer->setFont(Printer::FONT_B);
        $printer->setTextSize(2, 2);
        $printer->text($nama_store . "\n");
        $printer->text("REVISI NOTE\n");
        $printer->selectPrintMode();
        $printer->setFont(Printer::FONT_B);
        $printer->setTextSize(2, 2);
        $printer->feed();

        /* Title of receipt */
        $printer->text("____________\n");
        $printer->feed();


        /* Title of receipt */
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("No Order : " . $kode_temp . "\nType Order : " . $type_order . " - " . $keterangan_order . "\nTanggal  : " . $tanggal . "\n");

        $printer->text("____________\n");
        $printer->feed();

        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("NOTE\n");
        $printer->text($note . "\n");
        $printer->text("____________\n");
        $printer->feed();
        return $printer->getPrintConnector();
        $printer->cut();

        $printer->close();
    }

    public function no_test_print_dapur($id, $kode_temp, $type_order, $keterangan_order)
    {
        // $connector =  new WindowsPrintConnector("POS-80C-Network");
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('d-m-Y H:i');
        $nama_store = session('nama_store');
        $ip_printer = DB::table('pos_product_kategori')
            ->where('id_kategori', $id)
            ->first();

        if ($ip_printer->print_by == "nama") {
            $connector = new WindowsPrintConnector($ip_printer->ip_printer1);
        } else {
            $connector = new NetworkPrintConnector($ip_printer->ip_printer1);
        }


        /* Information for the receipt */

        $items = array();
        $totalQty = 0;
        $totalHarga = 0;
        $pembayaran = DB::table('pos_belum_bayar')
            ->where('id_kategori', $id)
            ->where('kode_temp', $kode_temp)
            ->get();

        foreach ($pembayaran as $key => $value) {
            $totalQty += $value->qty;
            $totalHarga += $value->total;
            array_push($items, new item($value->nama_item, $value->qty));
        }

        $desc = new item('Nama Item', 'Qty');

        $printer = new Printer($connector);

        /* Name of shop */
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer->setFont(Printer::FONT_B);
        $printer->setTextSize(2, 2);
        $printer->text($nama_store . "\n");
        $printer->text("ORDER CANCEL\n");
        $printer->selectPrintMode();
        $printer->setFont(Printer::FONT_B);
        $printer->setTextSize(2, 2);
        $printer->feed();

        /* Title of receipt */
        $printer->text("____________\n");
        $printer->feed();

        /* Title of receipt */
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Kode Order : " . $kode_temp . "\nType Order : " . $type_order . " - " . $keterangan_order . "\nTanggal  : " . $tanggal . "\n");

        $printer->text("____________\n");
        $printer->feed();


        // Ket nama

        $printer->text($desc);
        $printer->text("____________\n");


        /* Items */
        $printer->feed();
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        foreach ($pembayaran as $key => $value) {
            $printer->text($value->nama_item . " - (" . $value->qty . ")\n");
            $printer->feed();
        }
        $printer->text("____________\n");
        $printer->feed();
        return $printer->getPrintConnector();


        /* Cut the receipt and open the cash drawer */
        $printer->cut();
        // $printer -> pulse();

        $printer->close();
    }
}



class item
{
    private $name;
    private $price;
    private $dollarSign;

    public function __construct($name = '', $price = '', $dollarSign = false)
    {
        $this->name = $name;
        $this->price = $price;
        $this->dollarSign = $dollarSign;
    }

    public function __toString()
    {
        $rightCols = 20;
        $leftCols = 10;
        if ($this->dollarSign) {
            $leftCols = $leftCols / 2 - $rightCols / 2;
        }
        $left = str_pad($this->name, $leftCols);

        $sign = ($this->dollarSign ? '$ ' : '');
        $right = str_pad($sign . $this->price, $rightCols, ' ', STR_PAD_LEFT);
        return "$left$right\n";
    }
}


class item2
{
    private $name;
    private $price;
    private $dollarSign;

    public function __construct($name = '', $price = '', $dollarSign = false)
    {
        $this->name = $name;
        $this->price = $price;
        $this->dollarSign = $dollarSign;
    }

    public function __toString()
    {
        $rightCols = 10;
        $leftCols = 20;
        if ($this->dollarSign) {
            $leftCols = $leftCols / 2 - $rightCols / 2;
        }
        $left = str_pad($this->name, $leftCols);

        $sign = ($this->dollarSign ? '$ ' : '');
        $right = str_pad($sign . $this->price, $rightCols, ' ', STR_PAD_LEFT);
        return "$left$right\n";
    }
}

class item3
{
    private $name;
    private $price;
    private $dollarSign;

    public function __construct($name = '', $price = '', $dollarSign = false)
    {
        $this->name = $name;
        $this->price = $price;
        $this->dollarSign = $dollarSign;
    }

    public function __toString()
    {
        $rightCols = 13;
        $leftCols = 20;
        if ($this->dollarSign) {
            $leftCols = $leftCols / 2 - $rightCols / 2;
        }
        $left = str_pad($this->name, $leftCols);

        $sign = ($this->dollarSign ? '$ ' : '');
        $right = str_pad($sign . $this->price, $rightCols, ' ', STR_PAD_LEFT);
        return "$left$right\n";
    }
}
