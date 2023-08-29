<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmploymentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $all_employment_status_names = ['Intern','Probation','Permanent','PartTime'];
        $employment_status_names = DB::select('SELECT * FROM tbl_employement_status');
        if(empty($employment_status_names)){
            foreach($all_employment_status_names as $all_employment_status_name){
                DB::table('tbl_employement_status')->insert([
                    'employement_status_name' => $all_employment_status_name,
                    'employement_status_created_by'=> 1,
                    'employement_status_created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}
