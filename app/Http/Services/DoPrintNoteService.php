<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Log;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class DoPrintNoteService
{
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
     * @var string $notes
     */
    var $notes = '';

    /**
     * Initiate DoPrintNoteService class
     * 
     * @param object $printer
     * @param string $kode_temp
     * @param string $type_or
     * @param string $keterangan_order
     * @param string $notes
     */
    public function __construct($printer, $kode_temp, $type_order, $keterangan_order, $notes)
    {
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
        $printer->text("No Order : " . $this->kode_temp . "\nType Order : " . $this->type_order . " - " . $this->keterangan_order . "\nTanggal  : " . $tanggal . "\n");

        $printer->text("____________\n");
        $printer->feed();

        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("NOTE\n");
        $printer->text($this->notes . "\n");
        $printer->text("____________\n");
        $printer->feed();
        return Log::info([$printer->getPrintConnector()]);
        $printer->cut();

        $printer->close();
    }
}