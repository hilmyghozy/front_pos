<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pembayaran')->insert([
            [
                'nama_tiket' => 'Normal Ticket',
                'harga' => 150000,
                'qty' => 2,
                'total' => 300000
            ],
            [
                'nama_tiket' => 'Aparat',
                'harga' => 120000,
                'qty' => 1,
                'total' => 120000
            ]
            
        ]);
    }
}
