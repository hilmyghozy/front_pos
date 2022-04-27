<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\EscposImage;

class DoPrintPCService
{
    /**
     * @var GetDataPembayaranService $data
     */
    var $data;

    /**
     * @param GetDataPembayaranService $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function print($inv, $c_bayar, $nama_debit, $d_bayar, $kembali, $is_split, $type_order, $keterangan_order, $kode_temp, $id_diskon)
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
        $data_pembayaran = $this->data;
        $totalQty = $data_pembayaran->qty;
        $totalHarga = $data_pembayaran->total;
        $pajak = $data_pembayaran->pajak;
        $subtotal = $data_pembayaran->sub_total;

        foreach ($data_pembayaran->data as $key => $value) {
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
        $total = new Item2('Subtotal (Rp)', number_format($totalHarga, 0, ",", "."));
        $subtotal = new Item2('Subtotal (Rp)', number_format($subtotal, 0, ",", "."));
        $pajak = new Item2('Tax (Rp)', number_format($pajak, 0, ",", "."));
        $cash = new Item('Cash (Rp)', number_format($c_bayar, 0, ",", "."));
        $debit = new Item($nama_debit . ' (Rp)', number_format($d_bayar, 0, ",", "."));
        $kembalian = new Item('Kembalian', number_format($kembali, 0, ",", "."));
        /* Date is kept the same for testing */
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
        $printer->text($subtotal);
        $printer->text($pajak);
        if ($id_diskon != null) {
            $diskon = DB::table('pos_belum')->where('kode_temp', $kode_temp)->first();
            $totaldiskon = $diskon->subtotal - $diskon->total;
            $rowdiskon = DB::table('pos_diskon')->where('id_voucher', $id_diskon)->first();
            $totaldiskon = $rowdiskon->nominal;

            $txtdiskon = new Item2($rowdiskon->nama_voucher, "-" . number_format($totaldiskon, 0, ",", "."));
            $totalHarga -= $totaldiskon;
            $printer->text($txtdiskon);
        }
        $txttotal = new Item('Total (Rp)', number_format($totalHarga, 0, ",", "."));
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
        // return Log::info([$printer->getPrintConnector()]);
        // $printer -> pulse();

        $printer->close();
    }
}