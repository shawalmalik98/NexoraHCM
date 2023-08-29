<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IpAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ip_addresses = DB::select('SELECT * FROM tbl_mac_address');
        if(empty($ip_addresses)){
            DB::table('tbl_mac_address')->insert([
                'mac_address' => '127.0.0.1',
                'mac_address_added_date' => date('Y-m-d'),
            ]);
        }
    }
}
