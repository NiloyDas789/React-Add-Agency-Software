<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StationPayableReportExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    private $stationPayableReportData;

    public function __construct($stationPayableReportData)
    {
        $this->stationPayableReportData = $stationPayableReportData;
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'Station Name',
            'Source Type',
            'Leads',
            'Total Billable',
            'Total Station Payable',
            'Gross Profit',
        ];
    }

    public function collection()
    {
        return $this->stationPayableReportData;
    }

    public function map($data): array
    {
        return [
            $data->Station,
            $data->source_type === 1 ? 'Exclusive' : 'Shared',
            $data->leads ?: '',
            $data->total_billable_payout ? ('$' . number_format($data->total_billable_payout, 2)) : '',
            $data->total_media_payout ? ('$' . number_format($data->total_media_payout, 2)) : '',
            $data->total_billable_payout ? ('$' . number_format(($data->total_billable_payout - $data->total_media_payout), 2)) : '',
        ];
    }
}
