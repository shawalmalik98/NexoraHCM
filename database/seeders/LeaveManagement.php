<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeaveManagement extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $all_leave_management_names = ['Casual Leave','Sick Leave','Annual Leave','Maternity Leave','Paternity Leave','Bereavement Leave','Compensatory Leave'];
        $leave_management = DB::select('SELECT * FROM tbl_leave_management');
        if(empty($leave_management)){
            foreach($all_leave_management_names as $all_leave_management_name){
                DB::table('tbl_leave_management')->insert([
                    'leave_name' => $all_leave_management_name,
                    'leave_manag_created_by'=> 1,
                    'leave_visibilty' => 1,
                    'leave_manag_created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}
