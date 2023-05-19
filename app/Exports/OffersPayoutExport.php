<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OffersPayoutExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    private $offersPayoutReportData;

    private $allIndexOfOfferQualificationIds;
    private $allIndexOfOfferQualificationTitle;

    private $allIndexOfOfferIds;
    private $allIndexOfOfferRestrictedStates;

    private $allOfferLengths;

    public function __construct($offersPayoutReportData, $allIndexOfOfferIds, $allIndexOfOfferRestrictedStates, $allIndexOfOfferQualificationIds, $allIndexOfOfferQualificationTitle, $allOfferLengths)
    {
        $this->offersPayoutReportData = $offersPayoutReportData;

        $this->allIndexOfOfferQualificationIds = $allIndexOfOfferQualificationIds;
        $this->allIndexOfOfferQualificationTitle = $allIndexOfOfferQualificationTitle;

        $this->allIndexOfOfferIds = $allIndexOfOfferIds;
        $this->allIndexOfOfferRestrictedStates = $allIndexOfOfferRestrictedStates;

        $this->allOfferLengths = $allOfferLengths;
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
            'Length',
            'Billable',
            'Station Pay',
            'Qualifications',
            'Restrictions',
        ];
    }

    public function collection()
    {
        return $this->offersPayoutReportData;
    }

    public function map($data): array
    {
        return [
            $data->clientName ?: '',
            $data->offerName ?: '',
            $data->creativeName ?: '',
            $this->getLength($data->offerId),
            $data->billable_payout ? '$' . $data->billable_payout : '',
            $data->media_payout ? '$' . $data->media_payout : '',
            $this->getQualifications($data->offerId),
            $this->getRestrictedStates($data->offerId),
        ];
    }

    public function getRestrictedStates($offerId)
    {
        $restrictedStates = [];

        $indexesOfOfferId = array_keys($this->allIndexOfOfferIds, $offerId);

        for ($i = 0; $i < sizeof($indexesOfOfferId); $i++) {
            $restrictedStates[] = $this->allIndexOfOfferRestrictedStates[$indexesOfOfferId[$i]];
        }

        return implode(', ', $restrictedStates);
    }

    public function getQualifications($offerId)
    {
        $qualifications = [];

        $indexesOfOfferId = array_keys($this->allIndexOfOfferQualificationIds, $offerId);

        for ($i = 0; $i < sizeof($indexesOfOfferId); $i++) {
            $qualifications[] = $this->allIndexOfOfferQualificationTitle[$indexesOfOfferId[$i]];
        }

        return implode(', ', $qualifications);
    }

    public function getLength($offerId)
    {
        $offerLengths = [];

        $lengths = $this->allOfferLengths->where('offer_id', $offerId);

        foreach ($lengths as $length) {
            $offerLengths[] = ':' . $length->length;
        }

        return implode(', ', $offerLengths);
    }
}
