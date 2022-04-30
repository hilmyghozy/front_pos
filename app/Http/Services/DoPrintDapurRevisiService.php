<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class DoPrintDapurRevisiService
{
    /**
     * Data pembayaran baru yang akan di cetak ke dapur sesuai kategori
     * 
     * @var array $data_revisi
     */
    var $data_revisi = [];

    /**
     * Data pembayaran tambahan yang akan di cetak ke dapur sesuai kategori
     * 
     * @var array $data_tambahan
     */
    var $data_tambahan = [];

    /**
     * Data pembayaran lama yang akan di cetak ke dapur sesuai kategori
     * 
     * @var array $data_dihapus
     */
    var $data_dihapus = [];

    /**
     * Data kategori denga ip printer
     * 
     * @var object|null $printer
     */
    var $printer = null;

    /**
     * Kode Temp
     * 
     * @var string $kode_temp
     */
    var $kode_temp = '';

    /**
     * Tipe order 
     * 
     * @var string $type_or
     */
    var $type_order = '';

    /**
     * Keterangan order
     * 
     * @var string $keterangan_order
     */
    var $keterangan_order = '';

    /**
     * Initiate DoPrintDapurService
     * 
     * @param array $data_revisi
     * @param array $data_tambahan
     * @param array $data_dihapus
     * @param object $printer
     * @param string $kode_temp
     * @param string $type_order
     * @param string $keterangan_order
     */
    public function __construct($data_revisi = [], $data_tambahan = [], $data_dihapus = [], $printer, $kode_temp, $type_order, $keterangan_order)
    {
        $this->data_revisi = $data_revisi;
        $this->data_tambahan = $data_tambahan;
        $this->data_dihapus = $data_dihapus;
        $this->printer = $printer;
        $this->kode_temp = $kode_temp;
        $this->type_order = $type_order;
        $this->keterangan_order = $keterangan_order;
    }

    /**
     * Print Data Revisi Pesanan ke Dapur
     * 
     * @param string $ip_printer
     */
    public function print ($ip_printer)
    {
        Log::info("Print to $ip_printer " . $this->printer->nama_kategori);
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('d-m-Y H:i');
        $nama_store = session('nama_store');
        
        /* Information for the receipt */
        $data_lama = [];
        $data_dihapus = [];
        $data_revisi = [];
        $data_tambahan = [];
        $items_revisi = $this->data_revisi;
        Log::info('$this->data_revisi');
        foreach ($items_revisi as $item) {
            if (is_array($item)) $item = (object)$item;
            array_push($data_revisi, (object)[
                'name' => isset($item->nama_item) ? $item->nama_item : $item->text,
                'qty' => isset($item->qty_item) ? $item->qty_item : $item->qty,
                'additional_menu' => $item->additional_menu
            ]);
            array_push($data_lama, (object)[
                'name' => $item->data_awal->nama_item,
                'qty' => $item->data_awal->qty,
                'additional_menu' => $item->data_awal->additional_menu
            ]);
        }
        Log::info('$this->data_dihapus');
        foreach ($this->data_dihapus as $item) {
            if (is_array($item)) $item = (object)$item;
            $nama_item = isset($item->nama_item) ? $item->nama_item : $item['nama_item'];
            $qty = isset($item->qty) ? $item->qty : $item['qty_item'];
            $additional_menu = isset($item->additional_menu) ? $item->additional_menu : $item['additional_menu'];
            array_push($data_dihapus, (object)[
                'name' => $nama_item,
                'qty' => $qty,
                'additional_menu' => $additional_menu]);
        }
        Log::info('$this->data_tambahan');
        foreach ($this->data_tambahan as $item) {
            if (is_array($item)) $item = (object)$item;
            $nama_item = isset($item->nama_item) ? $item->nama_item : $item['nama_item'];
            $qty = isset($item->qty) ? $item->qty : $item->qty_item;
            $additional_menu = isset($item->additional_menu) ? $item->additional_menu : $item['additional_menu'];
            $item_lama_exists = $this->filterExists($data_lama, $nama_item, $additional_menu);
            $item_revisi_exists = $this->filterExists($data_revisi, $nama_item, $additional_menu);
            $condition = false;
            $condition_2 = false;
            if ($item_revisi_exists) {
                $condition = $item_revisi_exists->qty == $qty;
            } if ($item_lama_exists && $item_revisi_exists) {
                $condition_2 = $item_lama_exists->qty == ($qty + $item_revisi_exists->qty);
            }
            if ($condition) {
                $data_revisi = $this->mapAddQty($data_revisi, $item_revisi_exists, $qty);
            } else if ($condition_2) {
                $data_lama = $this->filterExcept($data_lama, $item_lama_exists);
                $data_revisi = $this->filterExcept($data_revisi, $item_revisi_exists);
            } else {
                array_push($data_tambahan, (object)[
                    'name' => $nama_item,
                    'qty' => $qty,
                    'additional_menu' => $additional_menu
                ]);
            }
        }
        $new_data_revisi = $this->filterDataRevisi($data_revisi, $data_lama);
        $new_data_lama = $this->filterDataRevisi($data_lama, $data_revisi);
        $condition = (count($data_dihapus) == 0) && (count($data_tambahan) == 0) && (count($new_data_lama) == 0) && (count($new_data_revisi) == 0);
        if ($condition) {
            return;
        }

        if ($this->printer->print_by == "nama") {
            $connector = new WindowsPrintConnector($ip_printer);
        } else {
            $connector = new NetworkPrintConnector($ip_printer);
        }

        $desc = new Item('Nama Item', 'Qty');
        
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
        $printer->text("Kode Order : " . $this->kode_temp . "\nType Order : " . $this->type_order . " - " . $this->keterangan_order . "\nTanggal  : " . $tanggal . "\n");

        $printer->text("____________\n");
        $printer->feed();


        // Ket nama

        
        
        /* Items */
        if (count($new_data_lama) > 0 && count($new_data_revisi) > 0) {
            $printer->text($desc);
            $printer->text("____________\n");
            $printer->feed();
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("FROM\n");
            $printer->feed();
            foreach ($new_data_lama as $key => $value) {
                $printer->text($value->name . " - (" . $value->qty . ")\n");
                foreach ($value->additional_menu as $item) {
                    $text = isset($item->text) ? $item->text : $item['text'];
                    $printer->text(" + $text \n");
                }
                $printer->feed();
            }
            $printer->text("____________\n");
            $printer->feed();
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("TO\n");
            $printer->feed();
            foreach ($new_data_revisi as $key => $value) {
                $printer->text($value->name . " - (" . $value->qty . ")\n");
                foreach ($value->additional_menu as $item) {
                    $text = isset($item->text) ? $item->text : $item['text'];
                    $printer->text(" + $text \n");
                }
                $printer->feed();
            }
            $printer->text("___________\n");
        }

        if (count($data_dihapus) > 0) {
            $printer->feed();
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("CANCELED MENU\n");
            $printer->feed();
            $printer->text($desc);
            $printer->text("____________\n");
            foreach ($data_dihapus as $key => $value) {
                $printer->text($value->name . " - (" . $value->qty . ")\n");
                foreach ($value->additional_menu as $item) {
                    $text = isset($item->text) ? $item->text : $item['text'];
                    $printer->text(" + $text \n");
                }
                // $printer->feed();
            }
            $printer->text("___________\n");
        }

        if (count($data_tambahan) > 0) {
            $printer->feed();
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("NEW MENU\n");
            $printer->text($desc);
            $printer->text("____________\n");
            $printer->feed();
            foreach ($data_tambahan as $key => $value) {
                $printer->text($value->name . " - (" . $value->qty . ")\n");
                foreach ($value->additional_menu as $item) {
                    $text = isset($item->text) ? $item->text : $item['text'];
                    $printer->text(" + $text \n");
                }
                // $printer->feed();
            }
            $printer->text("___________\n");
        }

        $printer->feed();

        // return Log::info([$printer->getPrintConnector()]);
        /* Cut the receipt and open the cash drawer */
        $printer->cut();
        // $printer -> pulse();

        $printer->close();
    }

    private function filterExists($data = [], $nama_item = '', $additional_menu = [])
    {
        return collect($data)->filter(function($value, $key) use($nama_item, $additional_menu) {
            $similar_name = $nama_item == $value->name;
            $similar_additional_menu = $additional_menu == $value->additional_menu;
            $diff_additional_menu = strcmp(json_encode($additional_menu), json_encode($value->additional_menu));
            $similar_additional_menu = $diff_additional_menu == 0;
            $condition = $similar_additional_menu && $similar_name;
            if ($condition) return $condition;
        })->first();
    }

    private function filterExcept($data_lama, $item)
    {
        return collect($data_lama)->filter(function($value) use ($item) {
            $similar_name = $item->name == $value->name;
            $similar_additional_menu = $item->additional_menu == $value->additional_menu;
            $diff_additional_menu = strcmp(json_encode($item->additional_menu), json_encode($value->additional_menu));
            $similar_additional_menu = $diff_additional_menu == 0;
            $similar_qty = $item->qty == $value->qty;
            $condition = $similar_additional_menu && $similar_name && $similar_qty;
            if (!$condition) return true;
        })->values();
    }

    private function mapAddQty($data = [], $item, $qty = 0)
    {
        return collect($data)->map(function($value) use ($item, $qty) {
            $similar_name = $item->name == $value->name;
            $similar_additional_menu = $item->additional_menu == $value->additional_menu;
            $similar_qty = $item->qty == $value->qty;
            $condition = $similar_additional_menu && $similar_name && $similar_qty;
            if ($condition) $value->qty += $qty;
            return $value;
        })->values();
    }

    private function filterDataRevisi($data_lama, $data_revisi)
    {
        return collect($data_lama)->filter(function($value, $key) use ($data_revisi) {
            $similar_name = $data_revisi[$key]->name == $value->name;
            $similar_additional_menu = $data_revisi[$key]->additional_menu == $value->additional_menu;
            $data_revisi_additional_menu = $data_revisi[$key]->additional_menu;
            $value_additional_menu = $value->additional_menu;
            $diff_additional_menu = strcmp(json_encode($data_revisi_additional_menu), json_encode($value_additional_menu));
            $similar_additional_menu = $diff_additional_menu == 0;
            $similar_qty = $data_revisi[$key]->qty == $value->qty;
            $condition = $similar_additional_menu && $similar_name && $similar_qty;
            if (!$condition) return true;
        })->values();
    }
}