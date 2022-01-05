<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TiketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tiket')->insert([
            [
                'nama_tiket' => 'Normal Ticket',
                'harga' => 150000,
                'tipe' => 1
            ],
            [
                'nama_tiket' => 'Manula & difabel',
                'harga' => 75000,
                'tipe' => 1
            ],
            [
                'nama_tiket' => 'Aparat',
                'harga' => 120000,
                'tipe' => 1
            ],
            [
                'nama_tiket' => 'Traveloka',
                'harga' => 127500,
                'tipe' => 1
            ],
            [
                'nama_tiket' => 'Check IT',
                'harga' => 1,
                'tipe' => 1
            ],
            [
                'nama_tiket' => 'Web Saloka',
                'harga' => 130000,
                'tipe' => 2
            ],
            [
                'nama_tiket' => 'Grup Saloka',
                'harga' => 130000,
                'tipe' => 3
            ]
            
        ]);
    }
}
