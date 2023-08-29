<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $location = DB::select('SELECT * FROM tbl_office_locations');
        if(empty($location)){
            DB::table('tbl_office_locations')->insert([
                'location_title' => "Official Site",
                'location_address' => "Suite # 603, 6th Floor, Progressive Center PECHS Block-6, Sharah-e-Faisal, Karachi, Sindh, PK",
                'location_latitude' => "24.863193462420856",
                'location_longitude' => "67.0743938469367",
                'location_added_at' => date('d-m-Y H:i:s'),
            ]);
        }
    }
}
