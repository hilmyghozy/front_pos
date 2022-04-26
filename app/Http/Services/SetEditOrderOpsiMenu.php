<?php

namespace App\Http\Services;

class SetEditOrderOpsiMenu
{
    /**
     * @var array $opsi_menu
     */
    var $opsi_menu = [];

    /**
     * @var array $data
     */
    var $data = [];

    /**
     * @param array $opsi_menu
     * @param array $data
     * 
     */
    public function __construct($opsi_menu = [], $data = [])
    {
        $this->opsi_menu = $opsi_menu;
        $this->data = $data;
    }

    public function getOpsiMenu()
    {
        $data = $this->data;
        $opsi_menu = $this->opsi_menu;
        return collect($opsi_menu)->map(function ($item, $key) use ($data) {
            if (is_object($item)) $item = (array)$item;
            $item['nama_item_lama'] = $item['nama_item'];
            if (!isset($item['item_type'])) $item['item_type'] = null;
            if (!isset($item['item_size'])) $item['item_size'] = null;
            $nama_item = new GetAdditionalTextService($item['nama_item'], $item['item_type'], $item['item_size']);
            $item['nama_item'] = "$nama_item";
            $item['qty'] = isset($data['qty_item']) ? $data['qty_item'] : $data['qty'];
            if (!isset($item['additional_menu'])) $item['additional_menu'] = [];
            $item['additional_menu'] = collect($item['additional_menu'])->sortBy('id')->values();
            return $item;
        })->sortBy('id')->values();
    }

    public function getOpsiMenu2()
    {
        $this->opsi_menu = collect($this->opsi_menu)->map(function ($opsi_menu) {
            if (is_array($opsi_menu)) $opsi_menu = (object)$opsi_menu;
            $opsi_menu->is_paket = 0;
            $opsi_menu->harga = 0;
            $order_id = $opsi_menu->nama_item;
            $opsi_menu->nama_item_lama = $opsi_menu->nama_item;
            if ($opsi_menu->item_type) {
                if (is_array($opsi_menu->item_type)) $opsi_menu->item_type = (object)$opsi_menu->item_type;
                $opsi_menu->nama_item = $opsi_menu->item_type->text;
                $item_type_id = $opsi_menu->item_type->id;
                $order_id = "$order_id.$item_type_id";
            }
            if (isset($opsi_menu->additional_menu)) {
                foreach ($opsi_menu->additional_menu as $menu) {
                    if (is_array($menu)) $menu = (object)$menu;
                    $order_id = "$order_id.$menu->id";
                }
            }
            $opsi_menu->order_id = $order_id;
            $opsi_menu->menuid = null;
            $opsi_menu->id_item = null;
            return $opsi_menu;
        });
        return $this->opsi_menu;
    }
}