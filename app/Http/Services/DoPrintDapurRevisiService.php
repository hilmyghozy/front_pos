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
        
        if ($this->printer->print_by == "nama") {
            $connector = new WindowsPrintConnector($ip_printer);
        } else {
            $connector = new NetworkPrintConnector($ip_printer);
        }
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
            array_push($data_tambahan, (object)[
                'name' => $nama_item,
                'qty' => $qty,
                'additional_menu' => $additional_menu
            ]);
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
        if (count($this->data_revisi) > 0) {
            $printer->text($desc);
            $printer->text("____________\n");
            $printer->feed();
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("FROM\n");
            $printer->feed();
            foreach ($data_lama as $key => $value) {
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
            foreach ($data_revisi as $key => $value) {
                $printer->text($value->name . " - (" . $value->qty . ")\n");
                foreach ($value->additional_menu as $item) {
                    $text = isset($item->text) ? $item->text : $item['text'];
                    $printer->text(" + $text \n");
                }
                $printer->feed();
            }
            $printer->text("___________\n");
        }

        if (count($this->data_dihapus) > 0) {
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

        if (count($this->data_tambahan) > 0) {
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

        return Log::info([$printer->getPrintConnector()]);
        /* Cut the receipt and open the cash drawer */
        $printer->cut();
        // $printer -> pulse();

        $printer->close();
    }
}