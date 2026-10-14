<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $all_companies = ['Nexora HCM','CandyLand','Bisconni','Snackcity','Hudson Pharma','Astro Films'];
        $companies = DB::select('SELECT * FROM tbl_company');
        if(empty($companies)){
            foreach($all_companies as $company){
                DB::table('tbl_company')->insert([
                    'company_name' => $company,
                    'created_by'=> 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}
