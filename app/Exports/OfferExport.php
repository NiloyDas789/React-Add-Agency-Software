<?php

namespace App\Exports;

use App\Models\Offer;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OfferExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    private $pageNum = 1;
    private $perPage = 10;

    public function __construct($page, $perPage)
    {
        $this->pageNum = $page;
        $this->perPage = $perPage;
    }

    public function headings(): array
    {
        return [
            'Client',
            'Provider',
            'Offer',
            'Creative',
            'Billable Payout',
            'Media Payout',
            'Margin ($)',
            'Margin (%)',
            'Qualifications',
            'Disposition',
            'Billable Call Duration',
            'De_Dupe',
            'Restricted States',
            'Start At',
            'End At',
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $offset = ($this->pageNum - 1) * $this->perPage;

        return Offer::query()
                ->latest()
                ->with(['client:id,name', 'provider:id,name', 'qualifications:id,title', 'states:id,name'])
                // ->offset($offset)
                // ->limit($this->perPage)
                ->get();
    }

    public function map($data): array
    {
        return [
            $data->client->name ?: '',
            $data->provider->name ?: '',
            $data->offer ?: '',
            $data->creative ?: '',
            '$' . number_format($data->billable_payout, 2),
            '$' . number_format($data->media_payout, 2),
            '$' . number_format(($data->billable_payout - $data->media_payout), 2),
            ($data->billable_payout - $data->media_payout) > 0 ? number_format((($data->billable_payout - $data->media_payout) / $data->billable_payout) * 100, 2) . '%' : '',
            $this->getQualifications($data->qualifications),
            $data->dispositions ?: '',
            $data->billable_call_duration ? $data->billable_call_duration . ' sec' : '',
            $data->de_dupe ?: '',
            $this->getRestrictedStates($data->states),
            date('m/d/Y', strtotime($data->start_at)),
            date('m/d/Y', strtotime($data->end_at)),
        ];
    }

    public function getQualifications($qualifications)
    {
        $allQualifications = [];

        foreach ($qualifications as $qualification) {
            $allQualifications[] = $qualification->title;
        }

        return implode(', ', $allQualifications);
    }

    public function getRestrictedStates($states)
    {
        $allStates = [];

        foreach ($states as $state) {
            $allStates[] = $state->name;
        }

        return implode(', ', $allStates);
    }
}
