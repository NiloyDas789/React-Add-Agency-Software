<?php

namespace App\Exports;

use App\Models\Offer;
use DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DataProviderReportExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    /**
    * @return \Illuminate\Support\Collection
    */
    private $stationId;
    private $offerId;
    private $providerId;
    private $clientId;
    private $startDate;
    private $endDate;
    private $year;

    public function __construct($stationId, $offerId, $providerId, $clientId, $startDate, $endDate, $year)
    {
        $this->stationId = $stationId;
        $this->offerId = $offerId;
        $this->providerId = $providerId;
        $this->clientId = $clientId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->year = $year;
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'Client',
            'Offer',
            'Data Type',
            'Data Provider',
            'Disposition/Qualifier',
            'Start Dates',
        ];
    }

    public function collection()
    {
        $dataProvider = DB::table('offer_toll_free_numbers')
                        ->join('toll_free_numbers', 'toll_free_numbers.id', 'offer_toll_free_numbers.toll_free_number_id')
                        ->join('stations', 'stations.id', '=', 'offer_toll_free_numbers.station_id')
                        ->join('offers', 'offers.id', '=', 'offer_toll_free_numbers.offer_id')
                        ->join('providers', 'providers.id', '=', 'offers.provider_id')
                        ->join('users', 'users.id', '=', 'offers.client_id')
                        ->select(
                            'users.name as clientName',
                            'offers.offer as offerName',
                            'offer_toll_free_numbers.data_type as dataType',
                            'providers.name as providerName',
                            'offers.dispositions as dispositionTitle',
                            'offers.billable_call_duration as billableCalDuration',
                            DB::raw("(DATE_FORMAT(offers.start_at,'%m/%d/%Y')) as offerStartingDate"),
                        )
                        ->when(!empty($this->stationId), fn ($q) => $q->whereIn('offer_toll_free_numbers.station_id', $this->stationId))
                        ->when(!empty($this->offerId), fn ($q) => $q->whereIn('offer_toll_free_numbers.offer_id', $this->offerId))
                        ->when(!empty($this->providerId), fn ($q) => $q->whereIn('offers.provider_id', $this->providerId))
                        ->when(!empty($this->clientId), fn ($q) => $q->whereIn('offers.client_id', $this->clientId))
                        ->when(!empty($this->startDate), fn ($q) => $q->whereDate('reports.start_at', $this->startDate))
                        ->when(!empty($this->endDate), fn ($q) => $q->whereDate('reports.end_at', $this->endDate))
                        ->when(!empty($this->year), fn ($q) => $q->whereIn(\DB::raw('year(offers.start_at)'), $this->year))
                        ->get();

        return $dataProvider;
    }

    public function map($data): array
    {
        return [
            $data->clientName ?: '',
            $data->offerName ?: '',
            (int)($data->dataType) === 1 ? 'TFN' : ((int)($data->dataType) === 2 ? 'WEB' : 'TFN and WEB'),
            $data->providerName ?: '',
            $data->dispositionTitle ?: '',
            $data->offerStartingDate ?: '',
        ];
    }
}
