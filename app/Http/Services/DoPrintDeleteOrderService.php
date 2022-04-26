<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class DoPrintDeleteOrderService
{
    /**
     * Data pembayaran yang akan di cetak ke dapur sesuai kategori
     * 
     * @var array $data
     */
    var $data = [];

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
     * Initiate DoPrintDeleteOrderService class
     * 
     * @param array $data
     * @param object $printer
     * @param string $kode_temp
     * @param string $type_order
     * @param string $keterangan_order
     */
    public function __construct($data, $printer, $kode_temp, $type_order, $keterangan_order)
    {
        $this->data = $data;
        $this->printer = $printer;
        $this->kode_temp = $kode_temp;
        $this->type_order = $type_order;
        $this->keterangan_order = $keterangan_order;
    }

    /**
     * Print Data Pesanan ke Dapur
     * 
     * @param string $ip_printer
     */
    public function print($ip_printer)
    {
        // $connector =  new WindowsPrintConnector("POS-80C-Network");
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('d-m-Y H:i');
        $nama_store = session('nama_store');

        if ($this->printer->print_by == "nama") {
            $connector = new WindowsPrintConnector($ip_printer);
        } else {
            $connector = new NetworkPrintConnector($ip_printer);
        }


        /* Information for the receipt */

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
        $printer->text("Kode Order : " . $this->kode_temp . "\nType Order : " . $this->type_order . " - " . $this->keterangan_order . "\nTanggal  : " . $tanggal . "\n");

        $printer->text("____________\n");
        $printer->feed();


        // Ket nama

        $printer->text($desc);
        $printer->text("____________\n");


        /* Items */
        $printer->feed();
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        foreach ($this->data as $key => $value) {
            $printer->text($value->nama_item . " - (" . $value->qty . ")\n");
            $printer->feed();
        }
        $printer->text("____________\n");
        $printer->feed();
        return Log::info([$printer->getPrintConnector()]);


        /* Cut the receipt and open the cash drawer */
        $printer->cut();
        // $printer -> pulse();

        $printer->close();
    }
}