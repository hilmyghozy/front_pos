<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\EscposImage;

class DoPrintResiService
{
    /**
     * @var $printer
     */
    var $printer;

    /**
     * @var string $inv
     */
    var $inv;

    /**
     * Data Pembayaran
     * 
     * @var GetDataPembayaranService $data_pembayaran
     */
    var $data_pembayaran;

    /**
     * @var string $type_order
     */
    var $type_order;

    /**
     * @var string $keterangan_order
     */
    var $keterangan_order;

    /**
     * @var null|int $id_diskon
     */
    var $id_diskon;

    /**
     * Initiate DoPrintResiService class
     * 
     * @param mixed $printer
     * @param GetDataPembayaranService $data_pembayaran
     * @param string $inv
     * @param string $type_order
     * @param string $keterangan_order
     * @param null|int $id_diskon
     */
    public function __construct($printer, $inv, GetDataPembayaranService $data_pembayaran, $type_order, $keterangan_order, $id_diskon = null)
    {
        $this->printer = $printer;
        $this->inv = $inv;
        $this->data_pembayaran = $data_pembayaran;
        $this->type_order = $type_order;
        $this->keterangan_order = $keterangan_order;
        $this->id_diskon = $id_diskon;
    }

    public function print()
    {
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('d M Y, H:i');
        $nama_kasir = session('nama');
        $nama_store = session('nama_store');

        $connector = new WindowsPrintConnector($this->printer);
        // $connector = new NetworkPrintConnector("10.154.30.208");
        /* Information for the receipt */
        $data_pembayaran = $this->data_pembayaran;
        $totalQty = $data_pembayaran->qty;
        $totalHarga = $data_pembayaran->total;
        $pajak = $data_pembayaran->pajak;
        $subtotal = $data_pembayaran->sub_total;

        $items = array();
        $pembayaran = $data_pembayaran->data;
        foreach ($pembayaran as $key => $value) {
            array_push(
                $items,
                new Item(
                    $value->nama_item . "\n(" . $value->qty . " x " . number_format($value->harga, 0, ",", ".") . ")",
                    number_format($value->harga * $value->qty, 0, ",", ".")
                )
            );
            $additional_menu = $value->additional_menu;
            foreach ($value->additional_menu as $additional_menu) {
                array_push(
                    $items,
                    new Item(
                        "+ $additional_menu->text",
                        number_format($additional_menu->harga * $value->qty, 0, ",", ".")
                    )
                );
            }
            foreach ($value->opsi_menu as $opsi_menu) {
                array_push(
                    $items,
                    new Item(
                        "- $opsi_menu->nama_item",
                        ''
                    )
                );
                if (isset($opsi_menu->additional_menu)) {
                    foreach ($opsi_menu->additional_menu as $additional_menu) {
                        array_push(
                            $items,
                            new Item(
                                "  + $additional_menu->text",
                                number_format($additional_menu->harga * $value->qty, 0, ",", ".")
                            )
                        );
                    }
                }
            }
        }
        $desc = new Item2('Item (Qty x Price)', 'Total');
        $tax = new Item2('Total Item', $totalQty);
        $pajak_penjualan = new Item2('Tax (Rp)', number_format($pajak, 0, ",", "."));
        $subtotal_penjualan = new Item2('Subtotal (Rp)', number_format($subtotal, 0, ",", "."));
        // $date = date('l jS \of F Y h:i:s A');
        // $date = "Monday 6th of April 2015 02:56:25 PM";

        /* Start the printer */
        $logo = EscposImage::load("resources/escpos-php.png", false);
        $printer = new Printer($connector);

        /* Print top logo */
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> graphics($logo);

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
        $printer->text("No. Nota : " . $this->inv . "\nType Order : " . $this->type_order . " - " . $this->keterangan_order . "\nTanggal  : " . $tanggal . "\nKasir    : " . $nama_kasir . "\n");

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
        $printer->text($subtotal_penjualan);
        if ($this->id_diskon != null) {
            $diskon = DB::table('pos_belum')->where('kode_temp', $this->inv)->first();
            $totaldiskon = $diskon->subtotal - $diskon->total;
            $rowdiskon = DB::table('pos_diskon')->where('id_voucher', $this->id_diskon)->first();
            if ($rowdiskon) {
                $totaldiskon = $rowdiskon->nominal;
                $txtdiskon = new Item2($rowdiskon->nama_voucher, "-" . number_format($totaldiskon, 0, ",", "."));
                $totalHarga -= $totaldiskon;
                $printer->text($txtdiskon);
            }

        }
        $printer->text($pajak_penjualan);
        $txttotal = new item('Total (Rp)', number_format($totalHarga, 0, ",", "."));
        $printer->text($txttotal);
        $printer->text("_________________________________\n");
        $printer->feed();
        $printer->selectPrintMode();
        $printer->feed(2);
        // return Log::info([$printer->getPrintConnector()]);
        /* Cut the receipt and open the cash drawer */
        $printer->cut();
        // $printer -> pulse();

        $printer->close();
    }
}