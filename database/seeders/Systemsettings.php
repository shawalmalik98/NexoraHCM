<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Systemsettings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $hrm_setting = DB::select('SELECT * FROM tbl_hrm_settings');
        if(empty($hrm_setting)){
            DB::table('tbl_hrm_settings')->insert([
                'company_logo' => "hcm-img.png",
                'company_name' => "Nexora HCM",
                'company_email' => "admin@example.com",
                'company_address' => "",
                'company_email_permission' => 0,
                'push_notification_permission' => 0,
                'company_favicon' => "hcm-img.png",
                'check_in_flexibility_time' => 20,
                'check_out_flexibility_time' => 20,
                'no_of_employees' => "unlimited",
                'web_attendance' => 1,
                'mobile_attendance' => 1,
                'signature_on_payslip' =>'default-signature.png'
            ]);
        }
    }
}
