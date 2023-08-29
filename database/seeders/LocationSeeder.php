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
                'location_title' => "Head Office",
                'location_address' => "17, Banglore Town, Main Sharah-e-Faisal, Karachi-75350, Pakistan.",
                'location_latitude' => "24.87004059989487",
                'location_longitude' => "67.08699856124093",
                'location_added_at' => date('d-m-Y H:i:s'),
            ]);
        }
    }
}
