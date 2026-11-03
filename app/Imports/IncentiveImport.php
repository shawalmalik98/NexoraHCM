<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class IncentiveImport implements ToCollection
{
    protected $period;

    public function __construct($period)
    {
        $this->period = $period;
    }

    public function collection(Collection $rows): void
    {
        $brandsRow = $rows[0];      // First row with brand names
        $criteriaRow = $rows[1];    // Second row with productive criteria
        $incentiveRow = $rows[2];   // Third row with incentives

        // Start from column 1 since column 0 is the 'Brands' label
        for ($i = 1; $i < $brandsRow->count(); $i++) {
            // Insert data into the 'incentives' table
            Incentive::create([
                'brands' => $brandsRow[$i],                // Brand name (e.g., 'Brand 1')
                'productive_criteria' => $criteriaRow[$i], // Productive Criteria (e.g., 'Criteria 1')
                'incentive' => $incentiveRow[$i],          // Incentive (e.g., 'Incentive 1')
                'period' => $this->period,                  // Store the period
            ]);
        }
    }
}

