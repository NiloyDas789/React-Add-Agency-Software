<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    private $reportData;

    public function __construct($reportData)
    {
        $this->reportData = $reportData;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Time',
            'Toll Free number',
            'Terminating Number',
            'ANI',
            'Duration',
            'Disposition',
            'Client',
            'Offer',
            'Creative',
            'Length',
            'Provider',
            'Call Status',
            'Station',
            'Billable Payout',
            'Media Payout',
            'Margin ($)',
            'Margin (%)',
            'State',
            'Qualified',
            'Zip Code',
            'Duplicate',
            'Restricted',
            'Call Recording',
            'Credit',
            'Credit Reason',
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->reportData;
    }

    public function map($data): array
    {
        return [
            $data->date,
            $data->time,
            $data->toll_free_number,
            $data->terminating_number !== null ? $data->terminating_number : (!empty($data->reportTerminatingNumber) ? $data->reportTerminatingNumber : ''),
            $data->ani,
            $data->duration == 0 ? '0' : $data->duration,
            $data->reportDisposition,
            $data->clientName,
            $data->offer,
            $data->creative,
            !empty($data->creativeLength) ? ':' . $data->creativeLength : '',
            $data->providerName,
            $data->call_status,
            $data->Station,
            $data->call_status === 'Billable' ? (!empty($data->billable_payout) ? '$' . $data->billable_payout : '') : '',
            $data->call_status === 'Billable' ? (!empty($data->media_payout) ? '$' . $data->media_payout : '') : '',
            $this->margin($data->call_status, $data->billable_payout, $data->media_payout, 'dollar'),
            $this->margin($data->call_status, $data->billable_payout, $data->media_payout, 'percent'),
            $data->stateName,
            $data->zip_code,
            $data->call_status === 'Billable' ? 'Yes' : 'NO',
            $data->call_status === 'Duplicate' ? 'Yes' : 'No',
            $data->call_status === 'Restricted' ? 'Yes' : 'No',
            $data->call_recording,
            $data->credit,
            $data->credit_reason,
        ];
    }

    public function margin($callStatus, $billablePayout, $mediaPayout, $type)
    {
        if ($callStatus === 'Billable') {
            if ($type === 'dollar') {
                return '$' . number_format($billablePayout - $mediaPayout, 2);
            } else {
                return !empty($billablePayout) ? number_format(((($billablePayout - $mediaPayout) / $billablePayout) * 100), 2) . '%' : '';
            }
        }

        return '';
    }
}
