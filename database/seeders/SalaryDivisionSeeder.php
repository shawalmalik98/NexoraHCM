<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalaryDivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $get_salary_division = DB::select('SELECT * FROM tbl_salary_division');
        if(empty($get_salary_division)){
            DB::table('tbl_salary_division')->insert([
                ['salary_division_title' => "Basic",
                'salary_division_percentage' => 50],
                ['salary_division_title' => "Utilities",
                'salary_division_percentage' => 25],
                ['salary_division_title' => "Fuel Allowance",
                'salary_division_percentage' => 25]
            ]);
        }
    }
}