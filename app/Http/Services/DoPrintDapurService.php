<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class DoPrintDapurService
{
    /**
     * Data pembayaran yang akan di cetak ke dapur sesuai kategori
     * 
     * @var array $data_pembayaran
     */
    var $data_pembayaran = [];

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
     * Notes
     * 
     * @var string|null $notes
     */
    var $notes = null;

    /**
     * Initiate DoPrintDapurService
     * 
     * @param array $data_pembayaran
     * @param object $printer
     * @param string $kode_temp
     * @param string $type_order
     * @param string $keterangan_order
     * @param string|null $notes
     */
    public function __construct($data_pembayaran, $printer, $kode_temp, $type_order, $keterangan_order, $notes = null)
    {
        $this->data_pembayaran = $data_pembayaran;
        $this->printer = $printer;
        $this->kode_temp = $kode_temp;
        $this->type_order = $type_order;
        $this->keterangan_order = $keterangan_order;
        $this->notes = $notes;
    }

    /**
     * Print Data Pesanan ke Dapur
     * 
     * @param string $ip_printer
     */
    public function print ($ip_printer)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('d-m-Y H:i');
        $nama_store = session('nama_store');

        if ($this->printer->print_by == "nama") {
            $connector = new WindowsPrintConnector($ip_printer);
        } else {
            $connector = new NetworkPrintConnector($ip_printer);
        }

        /* Information for the receipt */

        $items = array();
        $totalQty = 0;

        $pembayaran = collect($this->data_pembayaran)->where('id_kategori', $this->printer->id_kategori)->values();
        foreach ($pembayaran as $key => $value) {
            $totalQty += $value->qty;
            array_push($items, new Item($value->nama_item, $value->qty));
        }

        $desc = new Item('Nama Item', 'Qty');

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
        $printer->text("Kode Order : " . $this->kode_temp . "\nType Order : " . $this->type_order . " - " . $this->keterangan_order . "\nTanggal  : " . $tanggal . "\n");

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
            $additional_menu = isset($value->additional_menu) ? $value->additional_menu : [];
            foreach ($additional_menu as $item) {
                $printer->text(" + $item->text \n");
            }
            $printer->feed();
        }
        $printer->text("____________\n");
        $printer->feed();

        // $row_belum = DB::table('pos_belum')
        //     ->where('kode_temp', $this->kode_temp)
        //     ->first();

        if ($this->notes != null) {
            $printer->text("NOTE\n");
            $printer->text($this->notes . "\n");
            $printer->text("____________\n");
            $printer->feed();
        }

        // return Log::info([$printer->getPrintConnector()]);
        /* Cut the receipt and open the cash drawer */
        $printer->cut();
        // $printer -> pulse();

        $printer->close();
    }
}