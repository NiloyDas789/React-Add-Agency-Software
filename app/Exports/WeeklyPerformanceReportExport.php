<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;

class WeeklyPerformanceReportExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    /**
    * @return \Illuminate\Support\Collection
    */

    private $weeklyPerformanceReportData;
    private $startDate;

    public function __construct($weeklyPerformanceReportData, $startDate)
    {
        $this->weeklyPerformanceReportData = $weeklyPerformanceReportData;
        $this->startDate = $startDate;
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'Year',
            'Week',
            'Client',
            'Offer',
            'Billable',
            'Payable',
            'Gross Profit',
            'Margin',
        ];
    }

    public function collection()
    {
        return $this->weeklyPerformanceReportData;
    }

    public function map($data): array
    {
        return [
            $data->year,
            !empty($this->startDate) ? $this->startDate : $data->weekStartDate,
            $data->client_name,
            $data->offer_name,
            $data->total_billable_payout ? '$' . $data->total_billable_payout : '$0.00',
            $data->total_billable_payout ? '$' . $data->total_media_payout : '$0.00',
            $this->grossProfit($data->total_billable_payout, $data->total_media_payout),
            $this->margin($data->total_billable_payout, $data->total_media_payout),
        ];
    }

    public function grossProfit($billablePayout, $mediaPayout)
    {
        return $billablePayout != 0 ? '$' . $billablePayout - $mediaPayout : '$0.00';
    }

    public function margin($billablePayout, $mediaPayout)
    {
        return $billablePayout != 0 ? number_format(((($billablePayout - $mediaPayout) / $billablePayout) * 100), 2) . '%' : '$0.00%';
    }
}
