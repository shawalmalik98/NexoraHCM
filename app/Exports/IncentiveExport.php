<?php

namespace App\Exports;

use DB; // Import the DB facade
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Color;

class IncentiveExport implements FromCollection, WithHeadings, WithStyles
{
    protected $incentivePeriod;

    public function __construct($incentivePeriod)
    {
        $this->incentivePeriod = $incentivePeriod;
    }

    public function collection()
    {
        return collect(DB::table('tbl_incentive') // Use DB facade to query the table
            ->where('incentive_period', $this->incentivePeriod)
            ->get(['incentive_period', 'employee_type AS designation', 'brand', 'productive_criteria AS target_type', 'target_value', 'incentive']));
    }

    public function headings(): array
    {
        return [
            'Incentive Period',
            'Designation',
            'Brand',
            'Target Type',
            'Target Value',
            'Incentive',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Apply styles to the header row
        $sheet->getStyle('A1:F1')->getFont()->setBold(true); // Bold header row

        // Apply styles to the Designation column (B column)
        $designationColumn = 'B'; // Designation is in the B column
        $highestRow = $sheet->getHighestRow(); // Get the last row with data

        // Set the fill color for the Designation column to light blue
        $sheet->getStyle($designationColumn . '2:' . $designationColumn . $highestRow)
            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFADD8E6'); // Light Blue (ARGB)

        return [];
    }
}
