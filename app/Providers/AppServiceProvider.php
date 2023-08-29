<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\EmailConfiguration;
use Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // if(Schema::hasTable('tbl_email_configuration')){
        //     $email_config = EmailConfiguration::get()->toArray();
        //     if(!empty($email_config)){
        //         $data = [
        //             'driver' => 'smtp',
        //             'host' => $email_config[0]['email_config_host'],
        //             'port' => $email_config[0]['email_config_port'],
        //             'encryption' => $email_config[0]['email_config_encryption'],
        //             'username' => $email_config[0]['email_config_email'],
        //             'password' => $email_config[0]['email_config_pass'],
        //             'from' => [
        //                 'address' => $email_config[0]['email_config_email'],
        //                 'name' => $email_config[0]['email_config_from_name'],
        //             ],
        //         ];
        //         Config::set('mail',$data);
        //     }
        // }
    }
}
