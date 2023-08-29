<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InterviewLevelStatus extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $get_interview_status_level = DB::select('SELECT * FROM tbl_interview_status_level');
        if(empty($get_interview_status_level)){
            DB::table('tbl_interview_status_level')->insert([
                ['interview_status_level' => "Interview Scheduled"],
                ['interview_status_level' => "Hold for more interviews"],
                ['interview_status_level' => "Candidate Shortlisted"],
                ['interview_status_level' => "Candidate Rejected"],
                ['interview_status_level' => "Offer Placed"],
                ['interview_status_level' => "Offer Rejected"],
                ['interview_status_level' => "Candidate didn't Appeared"],
                ['interview_status_level' => "Candidate Hired"],
                ['interview_status_level' => "Interview Cancelled"],
            ]);
        }
    }
}
