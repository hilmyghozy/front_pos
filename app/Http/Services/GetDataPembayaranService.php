<?php

namespace App\Http\Services;

class GetDataPembayaranService
{
    /**
     * Subtotal pembayaran biasa
     * 
     * @var int
     */
    var $sub_total = 0;

    /**
     * Subtotal pembayaran dengan thirdparty (gojek, grab, dkk)
     * 
     * @var int
     */
    var $sub_total_thirdparty = 0;

    /**
     * Total pembayaran
     * 
     * @var int
     */
    var $total = 0;

    /**
     * Total pembayaran dengan thirdparty (gojek, grab, dkk)
     * 
     * @var int 
     */
    var $thirdparty = 0;

    /**
     * Total pajak (sebelum melakukan order)
     * 
     * @var int
     */
    var $pajak = 0;

    /**
     * Total pajak pembayaran dengan thirdparty
     * 
     * @var int
     */
    var $pajak_thirdparty = 0;

    /**
     * Jumlah item / menu dalam data
     * 
     * @var int
     */
    var $qty = 0;

    /**
     * A collection of data pembayaran
     * 
     * @var array
     */
    var $data = [];

    /**
     * Initial data
     * 
     * @param array $pembayaran
     * @return void
     */
    public function __construct($pembayaran = [])
    {
        $this->data = $pembayaran;
    }

    /**
     * Set data sub total, total dan pajak
     * data ditampilkan pada invoice dan untuk membuat order baru
     * 
     * @return void
     */
    public function setData(): void
    {
        foreach ($this->data as $key => $item) {
            if (is_array($item)) $item = (object)$item;
            if (!isset($item->qty)) $item->qty = 0;
            $this->qty += $item->qty;
            $item->nama_tiket = isset($item->nama_tiket) ? $item->nama_tiket : $item->nama_item;
            $item->nama_item = isset($item->nama_item) ? $item->nama_item : $item->nama_tiket;
            $order_id = $item->nama_item;
            $item->is_paket = 0;
            if (!$item->opsi_menu) {
                $item->opsi_menu = [];
            } else {
                $item->opsi_menu = json_decode($item->opsi_menu);
            }
            if (count($item->opsi_menu) > 0) {
                $item->is_paket = 1;
                $this->qty -= $item->qty;
            }
            if (!$item->additional_menu) {
                $item->additional_menu = [];
            } else {
                $item->additional_menu = json_decode($item->additional_menu);
            }
            $item->nama_tiket_lama = $item->nama_tiket;
            $item->nama_item_lama = $item->nama_tiket;
            if ($item->item_type) {
                $item->item_type = json_decode($item->item_type);
                $item->harga = $item->item_type->harga;
                $item->thirdparty = $item->item_type->thirdparty;
                $text = $item->item_type->nama_type;
                $item_type_id = $item->item_type->id_type;
                $order_id = "$order_id.$item_type_id";
                $item->nama_tiket = "$item->nama_tiket ($text)";
                $item->nama_item = $item->nama_tiket;
            }
            $this->total += $item->total;
            $this->pajak += $item->subpajak;

            $this->thirdparty += $item->subthirdparty;
            $this->pajak_thirdparty += $item->subpajak_thirdparty;

            $item->total -= $item->subpajak;
            $item->subthirdparty -= $item->subpajak_thirdparty;

            foreach ($item->additional_menu as $menu) {
                $item->total -= ($menu->harga * $item->qty);
                $item->subthirdparty -= ($menu->harga * $item->qty);
                $order_id = "$order_id.$menu->id";
            }
            $item->order_id = $order_id;
            $setEditOrderOpsiMenu = new SetEditOrderOpsiMenu($item->opsi_menu);
            $item->opsi_menu = $setEditOrderOpsiMenu->getOpsiMenu2();
            foreach ($item->opsi_menu as $opsi_menu) {
                if (isset($opsi_menu->additional_menu)) {
                    foreach ($opsi_menu->additional_menu as $menu) {
                        $item->total -= ($menu->harga * $item->qty);
                        $item->subthirdparty -= ($menu->harga * $item->qty);
                    }
                }
            }
        }
        $this->sub_total = $this->total - $this->pajak;
        $this->sub_total_thirdparty = $this->thirdparty - $this->pajak_thirdparty;
    }

    /**
     * Set data setiap item pembayaran
     * data ditampilkan pada menu edit order, digunakan saat melakukan perubahan order
     * dan ....
     * 
     * @return void
     */
    public function setDataOrder () : void
    {
        foreach ($this->data as $key => $item) {
            if (is_array($item)) $item = (object)$item;
            if (!isset($item->qty)) $item->qty = 0;
            $this->qty += $item->qty;
            if (!isset($item->total)) $item->total = 0;
            if (!isset($item->pajak)) $item->pajak = 0;
            if (!isset($item->harga)) $item->harga = 0;
            $item->nama_item_lama = $item->nama_item;
            $order_id = $item->nama_item;
            if (isset($item->_token)) $order_id = $item->text;
            $item->is_paket = 0;
            if (!isset($item->opsi_menu) || !$item->opsi_menu) {
                $item->opsi_menu = [];
            } else {
                if (is_string($item->opsi_menu)) $item->opsi_menu = json_decode($item->opsi_menu);
            }
            if (count($item->opsi_menu) > 0) {
                $item->is_paket = 1;
                $this->qty -= $item->qty;
            }
            if (!$item->additional_menu) {
                $item->additional_menu = [];
            } else {
                $item->additional_menu = json_decode($item->additional_menu);
            }
            $additional_text = '';
            if ($item->item_type) {
                if (is_string($item->item_type)) $item->item_type = json_decode($item->item_type);
                if (is_array($item->item_type)) $item->item_type = (object)$item->item_type;
                $text = isset($item->item_type->nama_type) ? $item->item_type->nama_type : $item->item_type->text;
                $item->nama_item = "$item->nama_item ($text)";
                $item_type_id = isset($item->item_type->id_type) ? $item->item_type->id_type : $item->item_type->id;
                $order_id = "$order_id.$item_type_id";
            }
            // if ($item->item_type) {
            //     $item->item_type = json_decode($item->item_type);
            //     $text = $item->item_type->text;
            //     $additional_text = "$additional_text $text ";
            //     $item_type_id = $item->item_type->id;
            //     $order_id = "$order_id.$item_type_id";
            // }
            // if ($item->item_size) {
            //     $item->item_size = json_decode($item->item_size);
            //     $text = $item->item_size->text;
            //     $additional_text = "$additional_text $text ";
            //     $item_size_id = $item->item_size->id;
            //     $order_id = "$order_id.$item_size_id";
            // }
            // if ($item->item_size || $item->item_type) {
            //     $item->nama_item = "$item->nama_item ($additional_text)";
            // }
            $this->total += $item->total;
            $this->pajak += ($item->pajak * $item->qty);
            $this->sub_total += ($item->harga * $item->qty);

            foreach ($item->additional_menu as $menu) {
                $item->harga -= $menu->harga;
                $order_id = "$order_id.$menu->id";
            }
            $item->order_id = "$order_id";
            foreach ($item->opsi_menu as $opsi_menu) {
                if (is_array($opsi_menu)) $opsi_menu = (object)$opsi_menu;
                if (!isset($item->qty)) $item->qty = 0;
                $opsi_menu->qty = $item->qty;
                $this->qty += $item->qty;
                $opsi_menu->is_paket = 0;
                $opsi_menu->harga = 0;
                $additional_text = '';
                $order_id = "";
                if (isset($opsi_menu->item_type)) {
                    if (is_array($opsi_menu->item_type)) $opsi_menu->item_type = (object)$opsi_menu->item_type;
                    $text = $opsi_menu->item_type->text;
                    $additional_text = "$additional_text $text ";
                    $item_type_id = $opsi_menu->item_type->id;
                    $order_id = "$order_id.$item_type_id";
                }
                if ($opsi_menu->item_size) {
                    // $text = $opsi_menu->item_size->text;
                    // $additional_text = "$additional_text $text ";
                    // $item_size_id = $opsi_menu->item_size->id;
                    // $order_id = "$order_id.$item_size_id";
                }
                $opsi_menu->nama_item_lama = $opsi_menu->nama_item;
                if ($opsi_menu->item_size || $opsi_menu->item_type) {
                    $opsi_menu->nama_item_lama = trim(explode("($additional_text)", $opsi_menu->nama_item)[0]);
                }
                if (isset($opsi_menu->additional_menu)) {
                    foreach ($opsi_menu->additional_menu as $menu) {
                        if (is_array($menu)) $menu = (object)$menu;
                        $item->harga -= $menu->harga;
                        $order_id = "$order_id.$menu->id";
                    }
                }
                $order_id = "$order_id";
                $nama_item_lama = $opsi_menu->nama_item_lama;
                $opsi_menu->order_id = "$nama_item_lama$order_id";
            }
        }
    }
}