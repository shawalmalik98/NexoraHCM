<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RequestModulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $all_request_modules = ['Attendance Request','Leave Request','Remote Work Request','Advance Salary Request','Loan Request','Resignation Request','Employee Expense Request','RPL Leave Request'];
        $modules_requests = DB::select('SELECT * FROM tbl_modules_requests');
        if(empty($modules_requests)){
            foreach($all_request_modules as $request_module){
                DB::table('tbl_modules_requests')->insert([
                    'request_module_name' => $request_module,
                ]);
            }
        }
    }
}
