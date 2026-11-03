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
            // common split in Pakistan: house rent 45% of basic, utilities 10% of basic (both inside the gross salary);
            // HR changes it in HRM Settings
            DB::table('tbl_salary_division')->insert([
                ['salary_division_title' => "Basic Salary",
                'salary_division_percentage' => 64.5],
                ['salary_division_title' => "House Rent Allowance",
                'salary_division_percentage' => 29],
                ['salary_division_title' => "Utilities Allowance",
                'salary_division_percentage' => 6.5]
            ]);
        }
    }
}