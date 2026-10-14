<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailConfiguration extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $email_config = DB::select('SELECT * FROM tbl_email_configuration');
        if(empty($email_config)){
            DB::table('tbl_email_configuration')->insert([
                'email_config_mailer' => "smtp",
                'email_config_host' => "smtp.office365.com",
                'email_config_email' => "goms@ismailglobal.com",
                'email_config_pass' => env('SEED_EMAIL_CONFIG_PASSWORD', ''),
                'email_config_port' => "587",
                'email_config_encryption'=>"tls",
                'email_config_from_address'=>"goms@ismailglobal.com",
                'email_config_from_name'=>"Nexora HCM",
                'email_config_created_at'=>date('d-m-Y H:i:s'),
            ]);
        }
    }
}
