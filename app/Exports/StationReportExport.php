<?php

namespace App\Exports;

use DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StationReportExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    private $stationReportData;
    private $allOfferTfnLengthsAdId;

    public function __construct($stationReportData, $allOfferTfnLengthsAdId)
    {
        $this->stationReportData = $stationReportData;
        $this->allOfferTfnLengthsAdId = $allOfferTfnLengthsAdId;
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'STATION',
            'OFFER',
            'AD-ID',
            'LENGTH',
            'TFN',
            'DATE',
            'TIME',
            'STATE',
            'ZIP',
            'LEADS',
            'PAYOUT',
        ];
    }

    public function collection()
    {
        return $this->stationReportData;
    }

    public function map($data): array
    {
        return [
            $data->Station,
            $data->offer ?: '',
            $data->Station != 'Unsourceable' ? $this->lengthOrAdId($data->offerId, $data->toll_free_number_id, $data->station_id, 'ad_id') : '',
            $data->Station != 'Unsourceable' ? $this->lengthOrAdId($data->offerId, $data->toll_free_number_id, $data->station_id, 'length') : '',
            $data->toll_free_number ?: '',
            $data->date ?: '',
            $data->time ?: '',
            $data->state ?: '',
            $data->zip_code ?: '',
            $data->leads ?: '',
            $data->total_media_payout ? ('$' . number_format($data->total_media_payout, 2)) : '$0.00',
        ];
    }

    public function lengthOrAdId($offerId, $tfnId, $stationId, $type)
    {
        $offerTfnLengths = [];
        $offerTfnAdId = [];

        $offerTfnLengthsAdId = $this->allOfferTfnLengthsAdId
                    ->where('offer_id', $offerId)
                    ->where('toll_free_number_id', $tfnId)
                    ->where('station_id', $stationId);

        foreach ($offerTfnLengthsAdId as $item) {
            if ($type === 'length') {
                $offerTfnLengths[] = ':' . $item->length;
            } else {
                $offerTfnAdId[] = $item->ad_id;
            }
        }

        $offerTfnLengthsOrAdId = $type === 'length' ? implode(', ', $offerTfnLengths) : implode(', ', $offerTfnAdId);

        return $offerTfnLengthsOrAdId;
    }
}
