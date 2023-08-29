<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $get_banks = DB::select('SELECT * FROM tbl_banks');
        if(empty($get_banks)){
            DB::table('tbl_banks')->insert([
                ['bank_name' => "Al Baraka Bank (Pakistan) Limited"],
                ['bank_name' => "Allied Bank Limited"],
                ['bank_name' => "Askari Bank"],
                ['bank_name' => "Bank Alfalah Limited"],
                ['bank_name' => "Bank Al-Habib Limited"],
                ['bank_name' => "BankIslami Pakistan Limited"],
                ['bank_name' => "Citi Bank"],
                ['bank_name' => "Dubai Islamic Bank Pakistan Limited"],
                ['bank_name' => "Faysal Bank Limited"],
                ['bank_name' => "First Women Bank Limited"],
                ['bank_name' => "Habib Bank Limited"],
                ['bank_name' => "Standard Chartered Bank (Pakistan) Limited"],
                ['bank_name' => "Habib Metropolitan Bank Limited"],
                ['bank_name' => "JS Bank Limited"],
                ['bank_name' => "MCB Bank Limited"],
                ['bank_name' => "MCB Islamic Bank Limited"],
                ['bank_name' => "Meezan Bank Limited"],
                ['bank_name' => "National Bank of Pakistan"],
                ['bank_name' => "Bank of Punjab"],
                ['bank_name' => "Sindh Bank"],
                ['bank_name' => "Bank of Khyber"],
                ['bank_name' => "First Women Bank"],
                ['bank_name' => "Samba Bank Limited"],
                ['bank_name' => "Silkbank Limited"],
                ['bank_name' => "Soneri Bank"],
                ['bank_name' => "Summit Bank"],
                ['bank_name' => "United Bank Limited"]
            ]);
        }
    }
}
