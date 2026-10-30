<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignationSeeder extends Seeder
{
    // starter departments and their designations; "Administrator" (Administration) is the Administrator Account's designation
    const DESIGNATIONS = [
        'Administration' => ['Administrator', 'Director', 'Manager', 'Executive'],
        'Human Resources' => ['HR Manager', 'HR Executive', 'Recruiter'],
        'Finance & Accounts' => ['Finance Manager', 'Accountant', 'Accounts Executive'],
        'Sales' => ['Sales Manager', 'Sales Executive'],
        'Marketing' => ['Marketing Manager', 'Marketing Executive'],
        'Operations' => ['Operations Manager', 'Operations Executive'],
        'Information Technology' => ['IT Manager', 'Software Engineer', 'IT Support Executive'],
        'Customer Support' => ['Support Manager', 'Support Executive'],
    ];

    /**
     * @return void
     */
    public function run()
    {
        if (DB::table('tbl_designations')->exists()) {
            return;
        }

        foreach (self::DESIGNATIONS as $department => $designations) {
            $department_id = DB::table('tbl_departments')->where('department_name', $department)->value('department_id');
            if (!$department_id) {
                continue;
            }
            foreach ($designations as $designation) {
                DB::table('tbl_designations')->insert([
                    'designation_name' => $designation,
                    'department_id' => $department_id,
                ]);
            }
        }
    }
}
