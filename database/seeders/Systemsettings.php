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
                'company_logo' => "fav-icons.png",
                'company_name' => "SourceCode",
                'company_email' => "abc@sourcecode.gmail.com",
                'company_address' => "Progressive Center, Office no.603 6th Floor Shahrah-e-Faisal, Block 6 PECHS",
                'company_email_permission' => 0,
                'push_notification_permission' => 0,
                'company_favicon' => "fav-icons.png",
                'check_in_flexibility_time' => 15,
                'check_out_flexibility_time' => 15,
                'no_of_employees' => "unlimited",
                'web_attendance' => 1,
                'signature_on_payslip' =>'default-signature.PNG',
                'system_currency'   => 'PKR'
            ]);
        }
    }
}
