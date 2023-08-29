<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmailEndPoint;

class EmailEndPointsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        // don't change the postion of endpoints primary key use for logic
        $end_points = [
            ['endpoint_desc'=>'Apply for loan method'],
            ['endpoint_desc'=>'Apply for shift time changing request'],
            ['endpoint_desc'=>'Create new requisition'],
            ['endpoint_desc'=>'Resignation placement'],
            ['endpoint_desc'=>'Remote work request']];

        $get_end_points = EmailEndPoint::get()->toArray();

        if(empty($get_end_points)){

            EmailEndPoint::insert($end_points); 
        }
    }
}
