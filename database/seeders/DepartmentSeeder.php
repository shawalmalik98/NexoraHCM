<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $get_departments = DB::select('SELECT * FROM tbl_departments');
        if(empty($get_departments)){
            $depart_array = ['Admin','Human Resources','Account & Finance'];
            foreach($depart_array as $department){
                DB::table('tbl_departments')->insert([
                    'department_name' => $department,
                    'department_create_time' => date('d-m-Y H:i:s'),
                ]);
            }
        }
    }
}
