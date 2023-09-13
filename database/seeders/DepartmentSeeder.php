<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $get_departments = DB::select('SELECT * FROM tbl_departments');
        if(empty($get_departments)){
            // $depart_array = ['Executive','HR & Admin','Accounts & Finance'];
            $depart_array = [
                'Strategic Operations',
                'Marketing',
                'Supply Chain',
                'Sales Operations',
                'Accounts & Finance',
                'Sales & Marketing',
                'Operations',
                'Export',
                'Human Resources',
                'Legal Affairs',
                'Project',
                'Production',
                'Accounts',
                'Field Force',
                'Finance',
                'Film Line',
                'Maintenance Mechanical',
                'Quality',
                'SAP',
                'Power House',
                'Logistics',
                'Trade Marketing',
                'Research & Development',
                'Electrical',
                'Planing',
                'Internal Audit',
                'Utilities',
                'Sales & Distrubution',
                'Planning',
                'Metalizing',
                'Admin Operations',
                'Brand Management',
                'Import',
                'HR Operations/C&B',
                'Electrical Maintenance',
                'Slitting',
                'Warehousing',
                'BTL',
                'Development',
                'Quality Assurance',
                'IT Infrastructure',
                'Taxation',
                'Plant-1',
                'Plant-5',
                'FGS',
                'Secretarial',
                'Health, Safety & Environment',
                'Production Planning',
                'Plant-3 ',
                'R&P Material Store',
                'Talent Acquisition',
                'Plant-3',
                'Agro Operations',
                'Mixing',
                'Hamid Ismail Foundation ',
                'Store',
                'Procurement',
                'Admin Ops',
                'Technical Store',
                'Plant-2 ',
                'Baking',
                'Admin Purchase',
                'Lab',
                'Cutting',
                'Microbiology Lab',
                'Quality Control',
                'General',
                'Time Office',
                'Power House II',
                'Wooden Workshop',
                'Plant-4 ',
                'Packing',
                'Mechanical',
                'Plant-6',
                'Ball Mill',
                'Dispatch',
                'Disposal',
                'Security & Vigilance',
                'Administration',
                'Administration & Security',
                'Information Technology',
                'Management',
                'WH Contractual',
                'Sales',
            ];
            foreach($depart_array as $department){
                DB::table('tbl_departments')->insert([
                    'department_name' => $department,
                    'department_create_time' => date('d-m-Y H:i:s'),
                ]);
            }
        }
    }
}
