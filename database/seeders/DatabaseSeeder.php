<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            Systemsettings::class,
            IpAddressSeeder::class,
            AttendanceRuleSeeder::class,
            SystemModuleSeeder::class,
            LeaveManagement::class,
            EmployeeGroupSeeder::class,
            EmployeeCompanySeeder::class,
            EmployeeSeeder::class,
            DepartmentSeeder::class,
            DesignationSeeder::class,
            BankSeeder::class,
            InterviewLevelStatus::class,
            RequestModulesSeeder::class,
            EmploymentStatusSeeder::class,
            SalaryDivisionSeeder::class,
            EmailEndPointsSeeder::class,
            LocationSeeder::class,
            EmailConfiguration::class,
          
        ]);
    }
}
