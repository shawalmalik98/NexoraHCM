<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    // starter office locations; set the real addresses, coordinates and attendance radius from All Locations
    const LOCATIONS = ['Head Office', 'Branch Office', 'Warehouse'];

    /**
     * @return void
     */
    public function run()
    {
        if (DB::table('tbl_office_locations')->exists()) {
            return;
        }

        foreach (self::LOCATIONS as $location) {
            DB::table('tbl_office_locations')->insert([
                'location_title' => $location,
                'location_address' => '',
                'location_latitude' => '0',
                'location_longitude' => '0',
                'location_attendance_radius' => 100,
                'location_added_at' => date('d-m-Y H:i:s'),
            ]);
        }
    }
}
