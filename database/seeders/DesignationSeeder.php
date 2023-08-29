<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $get_designations = DB::select('SELECT * FROM tbl_designations');
        if(empty($get_designations)){
            DB::table('tbl_designations')->insert([
                'designation_name' => "CEO",
                'department_id' => 1,
            ]);
            DB::table('tbl_designations')->insert([
                'designation_name' => "Head Of HR",
                'department_id' => 2,
            ]);
            DB::table('tbl_designations')->insert([
                'designation_name' => "Executive",
                'department_id' => 3,
            ]);
        
        }
    }
}
