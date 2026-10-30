<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Starter departments for a new company; rename or add more from Manage Departments.
     *
     * @return void
     */
    public function run()
    {
        if (DB::table('tbl_departments')->exists()) {
            return;
        }

        foreach (array_keys(DesignationSeeder::DESIGNATIONS) as $department) {
            DB::table('tbl_departments')->insert([
                'department_name' => $department,
                'department_create_time' => date('d-m-Y H:i:s'),
            ]);
        }
    }
}
