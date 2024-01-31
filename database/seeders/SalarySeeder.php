<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class SalarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $get_salarys = DB::select('SELECT * FROM tbl_salary');
        $get_salarys = json_decode(json_encode($get_salarys), true);
        if(empty($get_salarys)){
            DB::table('tbl_salary')->insert([
                'salary_employee_id' => 1,
                'salary_employee_code' => 1000000,
                'basic_salary' => Crypt::encryptString('50000'),
                'house_rent' => Crypt::encryptString('5000'),
                'utilities' => Crypt::encryptString('4000'),
                'gross_adjustment' => Crypt::encryptString('41000'),
                'created_at' => date('Y-m-d h:i:s'),
                
            ]);
        }

        $get_salary_earning = DB::select('SELECT * FROM tbl_earning');
        if(empty($get_salary_earning)){
            DB::table('tbl_earning')->insert([
                'earning_salary_id' => 1,
                'earning_title' => 'fuel',
                'earning_amount' => Crypt::encryptString('5000'),
            ]);
        }

        $get_salary_deduction = DB::select('SELECT * FROM tbl_deduction');
        if(empty($get_salary_deduction)){
            DB::table('tbl_deduction')->insert([
                'deduction_salary_id' => 1,
                'deduction_title' => 'medical insurance',
                'deduction_amount' => Crypt::encryptString('5000'),
            ]);
        }
    }
}
