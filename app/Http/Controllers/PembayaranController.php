<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

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
            ->where('pos_belum_bayar.kode_temp', $kode_temp)
            ->get();

        $type_or = "";
        if ($pembayaran->type_order == 1) {
            $type_or = "Third Party";
        } elseif ($pembayaran->type_order == 2) {
            $type_or = "Take Away";
        } elseif ($pembayaran->type_order == 3) {
            $type_or = "Dine In";
        }

        ?>
        <tr>
            <th colspan="2" class="text-left">Kode</th>
            <td class="text-right" id="order_no_invoice"><?= $kode_temp ?></td>
        </tr>
        <tr>
            <th colspan="2" class="text-left">Type Order</th>
            <td class="text-right"><?= $type_or ?> - <?= $pembayaran->keterangan_order ?></td>
        </tr>
        <?php
        $qty_item = 0;
        if($pembayaran->type_order == 1){
             foreach ($item_bayar as $key => $value) {
                $third = $value->harga * $value->qty;
        ?>
            <tr>
                <th class="text-left"><?= $value->nama_item ?></th>
                <td class="text-center"><?= $value->qty ?></td>
                <td class="text-right"><?= number_format($third, 0, ',', '.') ?></td>
            </tr>
        <?php
            $qty_item += $value->qty;
        }

        ?>
        <tr>
            <th colspan="2" class="text-left">Subtotal</th>
            <td class="text-right"><?= number_format($pembayaran->subtotal, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <th colspan="2" class="text-left">Diskon</th>
            <td class="text-right" id="diskon_order"><?= number_format($pembayaran->total - $pembayaran->subtotal, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <th class="text-left">Total</th>
            <td class="text-left"><?= $qty_item ?></td>
            <td class="text-right" id="total_order"><?= number_format($pembayaran->total, 0, ',', '.') ?></td>
        </tr>
    <?php
        }else{
        foreach ($item_bayar as $key => $value) {

        ?>
            <tr>
                <th class="text-left"><?= $value->nama_item ?></th>
                <td class="text-center"><?= $value->qty ?></td>
                <td class="text-right"><?= number_format($value->total, 0, ',', '.') ?></td>
            </tr>
        <?php
            $qty_item += $value->qty;
        }

        ?>
        <tr>
            <th colspan="2" class="text-left">Subtotal</th>
            <td class="text-right"><?= number_format($pembayaran->subtotal, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <th colspan="2" class="text-left">Diskon</th>
            <td class="text-right" id="diskon_order"><?= number_format($pembayaran->total - $pembayaran->subtotal, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <th class="text-left">Total</th>
            <td class="text-left"><?= $qty_item ?></td>
            <td class="text-right" id="total_order"><?= number_format($pembayaran->total, 0, ',', '.') ?></td>
        </tr>
    <?php
        }
    }

    public function add_revisi_order($kode_temp)
    {
        $item_bayar = DB::table('pos_revisi_bayar')
            ->where('pos_revisi_bayar.id_store', session('id_store'))
            ->delete();

        $item_bayar = DB::table('pos_belum_bayar')
            ->where('pos_belum_bayar.kode_temp', $kode_temp)
            ->get();

        foreach ($item_bayar as $key => $value) {
            DB::table('pos_revisi_bayar')->insert([
                [
                    'id' => null, 'nama_item' => $value->nama_item,
                    'id_kategori' => $value->id_kategori, 'id_store' => $value->id_store, 'id_kasir' => $value->id_kasir,
                    'harga' => $value->harga, 'qty' => $value->qty, 'total' => $value->total,
                    'kode_temp' => $kode_temp
                ]
            ]);
        }
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
            ->delete();
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

        $type_or = "";
        if ($pembayaran->type_order == 1) {
            $type_or = "Third Party";
        } elseif ($pembayaran->type_order == 2) {
            $type_or = "Take Away";
        } elseif ($pembayaran->type_order == 3) {
            $type_or = "Dine In";
        }

         if ($pembayaran->type_order == 1) {
            ?>
            <tr>
                <th colspan="2" class="text-left">Kode</th>
                <td colspan="2" class="text-right" id="id_orderEdit"><?= $kode_temp ?></td>
            </tr>
            <tr>
                <th colspan="2" class="text-left">Type Order</th>
                <td colspan="2" class="text-right"><?= $type_or ?> - <?= $pembayaran->keterangan_order ?></td>
            </tr>
            <?php
            $qty_item = 0;
            $tot = 0;
            foreach ($item_bayar as $key => $value) {
                $third = $value->harga * $value->qty;
            ?>
            <tr>
                <th class="text-left"><?= $value->nama_item ?></th>
                <td class="text-center"><?= $value->qty ?></td>
                <td class="text-right"><?= number_format($third, 0, ',', '.') ?></td>
                <td class="text-right">
                    <button class="btn btn-warning btn-sm col-5" onclick="edit_orderEdit('<?= $value->id ?>')"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-danger btn-sm col-5" onclick="del_orderEdit('<?= $value->id ?>')"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        <?php
            $qty_item += $value->qty;
            $tot += $value->total;
        }
        $tot_diskon = $pembayaran->subtotal - $pembayaran->total;

        ?>
        <tr>
            <th colspan="2" class="text-left">Subtotal</th>
            <td colspan="2" class="text-right" id="subtotalOrderEdit"><?= number_format($pembayaran->total, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <th colspan="2" class="text-left">Diskon</th>
            <td colspan="2" class="text-right" id="diskon_order">0</td>
        </tr>
        <tr>
            <th class="text-left">Total</th>
            <td class="text-left"><?= $qty_item ?></td>
            <td colspan="2" class="text-right" id="totalOrderEdit"><?= number_format($pembayaran->total, 0, ',', '.') ?></td>
        </tr>
    <?php
        }else{
    ?>
        <tr>
            <th colspan="2" class="text-left">Kode</th>
            <td colspan="2" class="text-right" id="id_orderEdit"><?= $kode_temp ?></td>
        </tr>
        <tr>
            <th colspan="2" class="text-left">Type Order</th>
            <td colspan="2" class="text-right"><?= $type_or ?> - <?= $pembayaran->keterangan_order ?></td>
        </tr>
        <?php
        $qty_item = 0;
        $tot = 0;
        foreach ($item_bayar as $key => $value) {

        ?>
            <tr>
                <th class="text-left"><?= $value->nama_item ?></th>
                <td class="text-center"><?= $value->qty ?></td>
                <td class="text-right"><?= number_format($value->total, 0, ',', '.') ?></td>
                <td class="text-right">
                    <button class="btn btn-warning btn-sm col-5" onclick="edit_orderEdit('<?= $value->id ?>')"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-danger btn-sm col-5" onclick="del_orderEdit('<?= $value->id ?>')"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        <?php
            $qty_item += $value->qty;
            $tot += $value->total;
        }
        $tot_diskon = $pembayaran->subtotal - $pembayaran->total;

        ?>
        <tr>
            <th colspan="2" class="text-left">Subtotal</th>
            <td colspan="2" class="text-right" id="subtotalOrderEdit"><?= number_format($tot, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <th colspan="2" class="text-left">Diskon</th>
            <td colspan="2" class="text-right" id="diskon_order"><?= number_format($tot_diskon, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <th class="text-left">Total</th>
            <td class="text-left"><?= $qty_item ?></td>
            <td colspan="2" class="text-right" id="totalOrderEdit"><?= number_format($tot - $tot_diskon, 0, ',', '.') ?></td>
        </tr>
    <?php
        }
    }

    public function detail_belum_bayar_editItem($id)
    {

        $no = 1;
        $id_store = session('id_store');

        $item_revisi = DB::table('pos_revisi_bayar')
            ->where('pos_revisi_bayar.id', $id)
            ->first();

        $item_product = DB::table('pos_product_item')
            ->where('id_store', session('id_store'))
            ->where('id_kategori', $item_revisi->id_kategori)
            ->get();

    ?>
        <div class="form-group">
            <label for="from">From</label>
            <div class="d-flex justify-content-between">
                <input type="hidden" id="id_item_edit_order" value="<?= $id ?>">
                <input type="text" value="<?= $item_revisi->nama_item ?>" class="form-control col-8" readonly>
                <input type="text" value="<?= $item_revisi->qty ?>" class="form-control col-3" readonly>
            </div>
        </div>

        <div class="form-group">
            <label for="from">To</label>
            <div class="d-flex justify-content-between">
                <select id="sel_order_revisi" class="form-control col-8">
                    <?php foreach ($item_product as $key => $value) { ?>
                        <option value="<?= $value->id_item ?>" <?= $value->nama_item == $item_revisi->nama_item ? 'selected' : null ?>><?= $value->nama_item ?> @<?= $value->harga_jual ?></option>
                    <?php } ?>
                </select>
                <input type="number" min="1" value="1" id="qty_order_revisi" class="form-control col-3">
            </div>
        </div>
    <?php

    }

    public function edit_item_order(Request $request)
    {
        $item_from = DB::table('pos_revisi_bayar')
            ->where('id', $request->id_item)
            ->first();

        $item_to = DB::table('pos_product_item')
            ->where('id_item', $request->id_item_sel)
            ->first();

        $checkitem = DB::table('pos_revisi_bayar')
            ->where('nama_item', $item_to->nama_item)
            ->where('id_kategori', $item_to->id_kategori)
            ->where('id_kasir', $item_from->id_kasir)
            ->first();

        // return json_encode($item_to);
        if ($checkitem != null && $checkitem->id != $request->id_item) {
            DB::table('pos_revisi_bayar')
                ->where('id', $checkitem->id)
                ->update(
                    [
                        'qty' => $request->qty_item + $checkitem->qty,
                        'total' => ($item_to->harga_jual * $request->qty_item) + $checkitem->total
                    ]
                );

            DB::table('pos_revisi_bayar')
                ->where('id', $request->id_item)
                ->delete();
        } else {
            DB::table('pos_revisi_bayar')
                ->where('id', $request->id_item)
                ->update(
                    [
                        'nama_item' => $item_to->nama_item, 'harga' => $item_to->harga_jual,
                        'qty' => $request->qty_item, 'total' => ($item_to->harga_jual * $request->qty_item)
                    ]
                );
        }
    }

    public function add_edit_item_order(Request $request)
    {
        $item_from = DB::table('pos_belum')
            ->where('kode_temp', $request->kode_temp)
            ->first();

        $item_to = DB::table('pos_product_item')
            ->where('id_item', $request->id_item)
            ->first();

        $checkitem = DB::table('pos_revisi_bayar')
            ->where('nama_item', $item_to->nama_item)
            ->where('id_kategori', $item_to->id_kategori)
            ->where('id_kasir', $item_from->id_kasir)
            ->first();

        // return json_encode($request->all());
        if ($checkitem != null) {
            DB::table('pos_revisi_bayar')
                ->where('id', $checkitem->id)
                ->update(
                    [
                        'qty' => $request->qty_item + $checkitem->qty,
                        'total' => ($item_to->harga_jual * $request->qty_item) + $checkitem->total
                    ]
                );
        } else {
            // return json_encode("add");
            DB::table('pos_revisi_bayar')
                ->insert([
                    [
                        'id' => null,
                        'nama_item' => $item_to->nama_item,
                        'id_kategori' => $item_to->id_kategori,
                        'id_store' => $item_to->id_store,
                        'id_kasir' => $item_from->id_kasir,
                        'harga' => $item_to->harga_jual,
                        'qty' => $request->qty_item,
                        'total' => ($item_to->harga_jual * $request->qty_item),
                        'kode_temp' => $request->kode_temp
                    ]
                ]);
        }
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
        $tiket = DB::table('pos_product_item')->where('id_item', $request->id)->first();

        $pembayaran = DB::table('pos_front_payment')
            ->where('nama_tiket', $tiket->nama_item)
            ->where('id_store', $tiket->id_store)
            ->where('id_kasir', session('id'))
            ->get();

        if ($pembayaran->count() > 0) {

            DB::table('pos_front_payment')
                ->where('nama_tiket', $tiket->nama_item)
                ->where('id_store', $tiket->id_store)
                ->where('id_kasir', session('id'))
                ->update(['qty' => ($pembayaran->first()->qty + 1), 'total' => ($pembayaran->first()->qty + 1) * $tiket->harga_jual,'subthirdparty' => ($pembayaran->first()->qty + 1) * $tiket->thirdparty,'subpajak' => ($pembayaran->first()->qty + 1) * $tiket->thirdparty]);

            return json_encode("update");
        } else {
            DB::table('pos_front_payment')->insert([
                [
                    'nama_tiket' => $tiket->nama_item,
                    'id_kategori' => $tiket->id_kategori,
                    'id_store' => $tiket->id_store,
                    'harga' => $tiket->harga_jual,
                    'subthirdparty' => $tiket->thirdparty,
                    'subpajak' => $tiket->pajak,
                    'qty' => 1,
                    'total' => $tiket->harga_jual,
                    'id_kasir' => session('id'),
                ]
            ]);
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

        $dataitem = DB::table('pos_belum_bayar')
            ->where('kode_temp', $kode_temp)
            ->get();

        // // Mencari kategori yang ada IP nya untuk di print
        $n = 0;
        $array[$n] = array('id_kategori' => null);
        foreach ($dataitem as $key => $value) {
            // return $value->nama_kategori;
            if ($array[$n]['id_kategori'] == null) {
                $start = $value->id_kategori;
                $array[$n] = array('id_kategori' => $value->id_kategori);
            } elseif ($value->id_kategori != $array[$n]['id_kategori']) {
                array_push($array, array('id_kategori' => $value->id_kategori));
                $n++;
            }
        }

        // Print By Kategori
        foreach ($array as $ar) {
            $ip_printer = DB::table('pos_product_kategori')
                ->where('id_kategori', $ar['id_kategori'])
                ->get();

            if ($ip_printer[0]->ip_printer1 != null) {
                // self::print($ar['id_kategori'], $kode_temp, $type_or, $keterangan_order);
                self::no_test_print_dapur($ar['id_kategori'], $kode_temp, $type_or, $keterangan_order);
            }
            if ($ip_printer[0]->ip_printer2 != null) {
                // self::print_2($ar['id_kategori'], $kode_temp, $type_or, $keterangan_order);
                self::no_test_print_dapur($ar['id_kategori'], $kode_temp, $type_or, $keterangan_order);
            }
            if ($ip_printer[0]->ip_printer3 != null) {
                // self::print3($ar['id_kategori'], $kode_temp, $type_or, $keterangan_order);
                self::no_test_print_dapur($ar['id_kategori'], $kode_temp, $type_or, $keterangan_order);
            }
        }

        DB::table('pos_belum_bayar')
            ->where('kode_temp', $kode_temp)
            ->delete();
        DB::table('pos_belum')
            ->where('kode_temp', $kode_temp)
            ->delete();
    }

    public function activity(Request $request)
    {
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

        $id_diskon = $rowitem->id_discount;
        $subtotal = $rowitem->subtotal;
        $total = $rowitem->total;
        $type_order = $rowitem->type_order;
        $keterangan_order = $rowitem->keterangan_order;
        $tipe_pembayaran = $request->tipe_pembayaran;
        $is_split = $request->is_split;
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

        DB::table('pos_activity')->insert([
            [
                'id_activity' => null, 'no_invoice' => $no_invoice, 'id_store' => $id_store,
                'id_employee' => $id_kasir, 'id_discount' => $id_diskon, 'subtotal' => $subtotal,
                'total' => $total, 'tipe_payment' => $tipe_pembayaran,
                'is_split' => $is_split, 'no_rek' => $no_rek, 'debit_cash' => $debit_cash, 'cash' => $cash,
                'kembalian' => $kembalian, 'tanggal' => $tanggal, 'time' => $times, 'status' => 'success',
                'type_order' => $type_order, 'keterangan_order' => $keterangan_order
            ]
        ]);
        foreach ($dataitem as $key => $value) {
            DB::table('pos_activity_item')->insert([
                [
                    'id' => null, 'no_invoice' => $no_invoice, 'nama_item' => $value->nama_item,
                    'id_kategori' => $value->id_kategori, 'id_store' => $value->id_store, 'id_kasir' => $id_kasir, 'harga' => $value->harga,
                    'qty' => $value->qty, 'total' => $value->total, 'created_at' => $tanggal . ' ' . $times, 'status' => 'success'
                ]
            ]);
        }

        // Mencari kategori yang ada IP nya untuk di print
        // $n = 0;
        // $array[$n] = array('id_kategori' => null);
        // foreach($dataitem as $key=>$value){
        //     // return $value->nama_kategori;
        //     if($array[$n]['id_kategori']==null){
        //         $start = $value->id_kategori;
        //         $array[$n] = array('id_kategori'=>$value->id_kategori);
        //     }elseif($value->id_kategori != $array[$n]['id_kategori']){
        //         array_push($array, array('id_kategori'=>$value->id_kategori));
        //         $n++;
        //     }
        // }

        // Print By Kategori
        // foreach($array as $ar){
        //     $ip_printer = DB::table('pos_product_kategori')
        //             ->where('id_kategori', $ar['id_kategori'])
        //             ->get();

        //     if($ip_printer[0]->ip_printer1 != null){
        //         // self::print($ar['id_kategori'], $no_invoice);
        //         self::test_printer_dapur($ar['id_kategori'], $no_invoice, $request->kode_order);
        //     }
        //     if($ip_printer[0]->ip_printer2 != null){
        //         // self::print_2($ar['id_kategori'], $no_invoice);
        //         self::test_printer_dapur($ar['id_kategori'], $no_invoice, $request->kode_order);
        //     }
        //     if($ip_printer[0]->ip_printer3 != null){
        //         // self::print3($ar['id_kategori'], $no_invoice);
        //         self::test_printer_dapur($ar['id_kategori'], $no_invoice, $request->kode_order);
        //     }
        // }

        // // Print langsung dari PC
        self::print_con($no_invoice, $cash, $tipe_pembayaran, $debit_cash, $kembalian, $is_split, $type_or, $keterangan_order, $request->kode_order, $id_diskon);

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
    }

    public function order(Request $request)
    {
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

        $subtotal = $request->subtotal;
        $thirdparty = $request->thirdparty;
        $total = $request->total_bayar;
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

        if($type_order == 1){
            DB::table('pos_belum')->insert([
                [
                    'id' => null, 'kode_temp' => $kode_temp, 'id_store' => $id_store,
                    'id_kasir' => $id_kasir, 'id_discount' => $id_diskon, 'subtotal' => $thirdparty,
                    'total' => $thirdparty, 'tanggal' => date('Y-m-d'), 'type_order' => $type_order,
                    'keterangan_order' => $keterangan_order, 'note' => $request->note
                ]
            ]);
        }else{
            DB::table('pos_belum')->insert([
                [
                    'id' => null, 'kode_temp' => $kode_temp, 'id_store' => $id_store,
                    'id_kasir' => $id_kasir, 'id_discount' => $id_diskon, 'subtotal' => $subtotal,
                    'total' => $total, 'tanggal' => date('Y-m-d'), 'type_order' => $type_order,
                    'keterangan_order' => $keterangan_order, 'note' => $request->note
                ]
            ]);
        }


        foreach ($dataitem as $key => $value) {
             if($type_order == 1){
                $third = $value->subthirdparty/$value->qty;
                $subthird = $third * $value->qty;
                 DB::table('pos_belum_bayar')->insert([
                [
                    'id' => null, 'kode_temp' => $kode_temp, 'nama_item' => $value->nama_tiket,
                    'id_kategori' => $value->id_kategori, 'id_store' => $value->id_store, 'id_kasir' => $id_kasir, 'harga' => $third,
                    'qty' => $value->qty, 'total' => $subthird
                ]
            ]);
             }else{
                DB::table('pos_belum_bayar')->insert([
                    [
                        'id' => null, 'kode_temp' => $kode_temp, 'nama_item' => $value->nama_tiket,
                        'id_kategori' => $value->id_kategori, 'id_store' => $value->id_store, 'id_kasir' => $id_kasir, 'harga' => $value->harga,
                        'qty' => $value->qty, 'total' => $value->total
                    ]
                ]);
            }
        }

        // // Mencari kategori yang ada IP nya untuk di print
        $n = 0;
        $array[$n] = array('id_kategori' => null);
        foreach ($dataitem as $key => $value) {
            // return $value->nama_kategori;
            if ($array[$n]['id_kategori'] == null) {
                $start = $value->id_kategori;
                $array[$n] = array('id_kategori' => $value->id_kategori);
            } elseif ($value->id_kategori != $array[$n]['id_kategori']) {
                array_push($array, array('id_kategori' => $value->id_kategori));
                $n++;
            }
        }

        // Print By Kategori
        foreach ($array as $ar) {
            $ip_printer = DB::table('pos_product_kategori')
                ->where('id_kategori', $ar['id_kategori'])
                ->get();

            if ($ip_printer[0]->ip_printer1 != null) {
                self::print($ar['id_kategori'], $kode_temp, $type_or, $keterangan_order);
                // self::test_print_dapur($ar['id_kategori'], $kode_temp, $type_or, $keterangan_order);
            }
            if ($ip_printer[0]->ip_printer2 != null) {
                self::print_2($ar['id_kategori'], $kode_temp, $type_or, $keterangan_order);
                // self::test_print_dapur($ar['id_kategori'], $kode_temp, $type_or, $keterangan_order);
            }
            if ($ip_printer[0]->ip_printer3 != null) {
                self::print3($ar['id_kategori'], $kode_temp, $type_or, $keterangan_order);
                // self::test_print_dapur($ar['id_kategori'], $kode_temp, $type_or, $keterangan_order);
            }
        }

        // // Print langsung dari PC
        // // self::print_con($no_invoice, $cash, $tipe_pembayaran, $debit_cash, $kembalian, $is_split);
        DB::table('pos_front_payment')
            ->where('id_store', $id_store)
            ->where('id_kasir', $id_kasir)
            ->delete();

        DB::table('pos_diskon')->where('id_voucher', 1)
            ->update([
                'nominal' => null
            ]);
        session()->forget(['id_diskon', 'jumlah_diskon', 'nama_diskon']);
    }

    public function edit_order(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        // echo json_encode($request->all());  


        $kode_temp = $request->kode_temp;

        $rowitem = DB::table('pos_belum')
            ->where('kode_temp', $kode_temp)
            ->first();

        $dataitem = DB::table('pos_belum_bayar')
            ->where('kode_temp', $kode_temp)
            ->get();

        $datarevisi = DB::table('pos_revisi_bayar')
            ->where('kode_temp', $kode_temp)
            ->get();

        $id_store = $rowitem->id_store;
        $id_kasir = $rowitem->id_kasir;
        $id_diskon = $rowitem->id_discount;

        $jum_dataitem = DB::select("SELECT id_kategori FROM `pos_belum_bayar` WHERE kode_temp ='" . $kode_temp . "' GROUP BY id_kategori");
        $jum_datarevisi = DB::select("SELECT id_kategori FROM `pos_revisi_bayar` WHERE kode_temp ='" . $kode_temp . "' GROUP BY id_kategori");
        $num_di = 0;
        foreach ($jum_dataitem as $key => $value) {
            $num_di++;
        }

        $num_dr = 0;
        foreach ($jum_datarevisi as $key => $value) {
            $num_dr++;
        }

        $subtotal = $request->subtotal;
        $total = $request->total;

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

        // // Mencari kategori yang ada IP nya untuk di print
        $n = 0;
        if ($num_di >= $num_dr) {
            $array[$n] = array('id_kategori' => null);
            foreach ($dataitem as $key => $value) {
                // return $value->nama_kategori;
                if ($array[$n]['id_kategori'] == null) {
                    $start = $value->id_kategori;
                    $array[$n] = array('id_kategori' => $value->id_kategori);
                } elseif ($value->id_kategori != $array[$n]['id_kategori']) {
                    array_push($array, array('id_kategori' => $value->id_kategori));
                    $n++;
                }
            }
        } else {
            $array[$n] = array('id_kategori' => null);
            foreach ($datarevisi as $key => $value) {
                // return $value->nama_kategori;
                if ($array[$n]['id_kategori'] == null) {
                    $start = $value->id_kategori;
                    $array[$n] = array('id_kategori' => $value->id_kategori);
                } elseif ($value->id_kategori != $array[$n]['id_kategori']) {
                    array_push($array, array('id_kategori' => $value->id_kategori));
                    $n++;
                }
            }
        }

        // Print By Kategori
        foreach ($array as $ar) {
            $ip_printer = DB::table('pos_product_kategori')
                ->where('id_kategori', $ar['id_kategori'])
                ->get();

            if ($ip_printer[0]->ip_printer1 != null) {
                // self::print($ar['id_kategori'], $kode_temp, $type_or, $keterangan_order);
                self::test_print_dapur_revisi($ar['id_kategori'], $kode_temp, $type_or, $keterangan_order);
            }
            if ($ip_printer[0]->ip_printer2 != null) {
                // self::print_2($ar['id_kategori'], $kode_temp, $type_or, $keterangan_order);
                self::test_print_dapur_revisi($ar['id_kategori'], $kode_temp, $type_or, $keterangan_order);
            }
            if ($ip_printer[0]->ip_printer3 != null) {
                // self::print3($ar['id_kategori'], $kode_temp, $type_or, $keterangan_order);
                self::test_print_dapur_revisi($ar['id_kategori'], $kode_temp, $type_or, $keterangan_order);
            }
        }

        // return json_encode($array);

        DB::table('pos_belum')->where('kode_temp', $kode_temp)
            ->update([
                'subtotal' => $subtotal,
                'total' => $total
            ]);

        DB::table('pos_belum_bayar')->where('kode_temp', $kode_temp)
            ->delete();

        foreach ($datarevisi as $key => $value) {
            DB::table('pos_belum_bayar')->insert([
                [
                    'id' => null, 'kode_temp' => $kode_temp, 'nama_item' => $value->nama_item,
                    'id_kategori' => $value->id_kategori, 'id_store' => $value->id_store, 'id_kasir' => $value->id_kasir, 'harga' => $value->harga,
                    'qty' => $value->qty, 'total' => $value->total
                ]
            ]);
        }


        // // Print langsung dari PC
        // // self::print_con($no_invoice, $cash, $tipe_pembayaran, $debit_cash, $kembalian, $is_split);

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

        // // Mencari kategori yang ada IP nya untuk di print
        $n = 0;
        $array[$n] = array('id_kategori' => null);
        foreach ($dataitem as $key => $value) {
            // return $value->nama_kategori;
            if ($array[$n]['id_kategori'] == null) {
                $start = $value->id_kategori;
                $array[$n] = array('id_kategori' => $value->id_kategori);
            } elseif ($value->id_kategori != $array[$n]['id_kategori']) {
                array_push($array, array('id_kategori' => $value->id_kategori));
                $n++;
            }
        }

        // Print By Kategori
        foreach ($array as $ar) {
            $ip_printer = DB::table('pos_product_kategori')
                ->where('id_kategori', $ar['id_kategori'])
                ->get();

            if ($ip_printer[0]->ip_printer1 != null) {
                // self::print($ar['id_kategori'], $kode_temp, $type_or, $keterangan_order);
                self::notetest_print_dapur_revisi($ar['id_kategori'], $kode_temp, $type_or, $keterangan_order, $request->note);
            }
            if ($ip_printer[0]->ip_printer2 != null) {
                // self::print_2($ar['id_kategori'], $kode_temp, $type_or, $keterangan_order);
                self::notetest_print_dapur_revisi($ar['id_kategori'], $kode_temp, $type_or, $keterangan_order, $request->note);
            }
            if ($ip_printer[0]->ip_printer3 != null) {
                // self::print3($ar['id_kategori'], $kode_temp, $type_or, $keterangan_order);
                self::notetest_print_dapur_revisi($ar['id_kategori'], $kode_temp, $type_or, $keterangan_order, $request->note);
            }
        }

        // return json_encode($array);



    }

    public function add_edit_order()
    {
        $item_product = DB::table('pos_product_item')
            ->where('id_store', session('id_store'))
            ->get();

?>
    <div class="form-group">
        <label for="">Add Item</label>
        <div class="d-flex justify-content-between">
            <select id="sel_order_add" class="form-control col-8">
                <?php foreach ($item_product as $key => $value) { ?>
                    <option value="<?= $value->id_item ?>"><?= $value->nama_item ?> @<?= $value->harga_jual ?></option>
                <?php } ?>
            </select>
            <input type="number" min="1" value="1" id="qty_order_add" class="form-control col-3">
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
        DB::table('pos_front_payment')->where('id', $id)
            ->update([
                'qty' => $request->qty,
                'total' => $request->total
            ]);
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

        foreach ($pembayaran as $key => $value) {
            $totalQty += $value->qty;
            $totalHarga += $value->total;
            array_push($items, new item($value->nama_item . "\n(" . $value->qty . " x " . number_format($value->harga, 0, ",", ".") . ")", number_format($value->total, 0, ",", ".")));
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
        $printer->bitImageColumnFormat($logo, Printer::IMG_DOUBLE_WIDTH | Printer::IMG_DOUBLE_HEIGHT);
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
        // $printer -> pulse();

        $printer->close();
    }

    // Print Untuk PC RESI
    public function print_resi($inv)
    {
        date_default_timezone_set('Asia/Jakarta');
        $row_bayar = DB::table('pos_belum')
            ->where('kode_temp', $inv)
            ->first();
        $type_order = $row_bayar->type_order;
        $keterangan_order = $row_bayar->keterangan_order;
        $kode_temp = $inv;
        $id_diskon = $row_bayar->id_discount;

        $type_or = "";
        if ($type_order == 1) {
            $type_or = "Third Party";
        } elseif ($type_order == 2) {
            $type_or = "Take Away";
        } elseif ($type_order == 3) {
            $type_or = "Dine In";
        }

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

        foreach ($pembayaran as $key => $value) {
            $totalQty += $value->qty;
            $totalHarga += $value->total;
            array_push($items, new item($value->nama_item . "\n(" . $value->qty . " x " . number_format($value->harga, 0, ",", ".") . ")", number_format($value->total, 0, ",", ".")));
        }

        $desc = new item2('Item (Qty x Price)', 'Total');
        $tax = new item2('Total Item', $totalQty);
        $total = new item2('Subtotal (Rp)', number_format($totalHarga, 0, ",", "."));
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
        $printer->bitImageColumnFormat($logo, Printer::IMG_DOUBLE_WIDTH | Printer::IMG_DOUBLE_HEIGHT);
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

            $txtdiskon = new item2($rowdiskon->nama_voucher, "-" . number_format($totaldiskon, 0, ",", "."));
            $totalHarga -= $totaldiskon;
            $printer->text($txtdiskon);
        }
        $txttotal = new item('Total (Rp)', number_format($totalHarga, 0, ",", "."));
        $printer->text($txttotal);
        $printer->text("_________________________________\n");
        $printer->feed();
        $printer->selectPrintMode();
        $printer->feed(2);

        /* Cut the receipt and open the cash drawer */
        $printer->cut();
        // $printer -> pulse();

        $printer->close();
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
            'deposit' => $request->total * 1000,
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
