<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $all_employee_groups = ['Regular Employee Group'];
        $employee_groups = DB::select('SELECT * FROM tbl_employee_groups');
        if(empty($employee_groups)){
            foreach($all_employee_groups as $all_employee_group){
                DB::table('tbl_employee_groups')->insert([
                    'group_name' => $all_employee_group,
                    'created_by'=> 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}
