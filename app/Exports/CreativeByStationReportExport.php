<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class CreativeByStationReportExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    private $reportData;

    public function __construct($reportData)
    {
        $this->reportData = $reportData;
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'Client',
            'Offer',
            'Creative',
            'Station',
            'Total Billable',
            'Year'
        ];
    }

    public function collection()
    {
        return $this->reportData;
    }

    public function map($data): array
    {
        return [
            $data->clientName ?: '',
            $data->offerName ?: '',
            $data->creativeName ?: '',
            $data->Station,
            $data->total_billable_payout ? ('$' . $data->total_billable_payout) : '$0.00',
            $data->year ?: '',
        ];
    }
}
