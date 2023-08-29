<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AttendanceRule;
use Illuminate\Support\Facades\DB;

class AttendanceRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   

        // default rule of late (late=>deduction)
        $rule_lates = [
            '0'=>'0',
            '1'=>'0',
            '2'=>'0',
            '3'=>'1',
            '4'=>'1',
            '5'=>'2',
            '6'=>'3',
            '7'=>'4',
            '8'=>'0',
            '9'=>'4',
            '10'=>'4',
            '11'=>'4',
            '12'=>'4',
            '13'=>'4',
            '14'=>'4',
            '15'=>'4',
            '16'=>'4',
            '17'=>'4',
            '18'=>'4',
            '19'=>'4',
            '20'=>'4',
            '21'=>'4',
            '22'=>'4',
            '23'=>'4',
            '24'=>'4',
            '25'=>'4',
            '26'=>'4',
            '27'=>'4',
            '28'=>'4',
            '29'=>'4',
            '30'=>'4',
        ];

        // default rule of half (half=>deduction)
        $rule_halfs = [
            '0'=>'0',
            '1'=>'0',
            '2'=>'0',
            '3'=>'1',
            '4'=>'1',
            '5'=>'2',
            '6'=>'3',
            '7'=>'4',
            '8'=>'0',
            '9'=>'4',
            '10'=>'4',
            '11'=>'4',
            '12'=>'4',
            '13'=>'4',
            '14'=>'4',
            '15'=>'4',
            '16'=>'4',
            '17'=>'4',
            '18'=>'4',
            '19'=>'4',
            '20'=>'4',
            '21'=>'4',
            '22'=>'4',
            '23'=>'4',
            '24'=>'4',
            '25'=>'4',
            '26'=>'4',
            '27'=>'4',
            '28'=>'4',
            '29'=>'4',
            '30'=>'4',
        ];
        
        AttendanceRule::truncate();
        $deduction_rule = AttendanceRule::get()->toArray();

        if(empty($deduction_rule)){

            foreach($rule_lates as $days=>$deduct){

                $insert_seeder = new AttendanceRule();
                $insert_seeder->attendance_deduction_rule_type = 1 ;
                $insert_seeder->attendance_deduction_rule_day_count = $days ;
                $insert_seeder->attendance_deduction_rule_deduction_count = $deduct ;
                $insert_seeder->save();

            }

            foreach($rule_halfs as $days=>$deduct){

                $insert_seeder = new AttendanceRule();
                $insert_seeder->attendance_deduction_rule_type = 2 ;
                $insert_seeder->attendance_deduction_rule_day_count = $days ;
                $insert_seeder->attendance_deduction_rule_deduction_count = $deduct ;
                $insert_seeder->save();

            }

        }
    }
}
