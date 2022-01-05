<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('vouchers')->insert([
            [
                'kode_voucher' => Str::random(6),
                'potongan' => 15000,
                'status' => 0,
            ],
            [
                'kode_voucher' => Str::random(6),
                'potongan' => 10000,
                'status' => 0,
            ],
            
            ]);
    }
}
