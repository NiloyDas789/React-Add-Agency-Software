<?php

namespace App\Exports;

use App\Models\OfferTollFreeNumber;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OfferTfnExport implements FromCollection, WithHeadings, WithMapping
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
            'Offer',
            'Creative',
            'TFN',
            'Station',
            'State',
            'Length',
            'Master',
            'Ad_id',
            'Source Type',
            'Website',
            'Terminating Number',
            'Data Type',
            'Assigned Date',
            'Start Date',
            'End Date',
            'Test Date',
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $offset = ($this->pageNum - 1) * $this->perPage;

        return OfferTollFreeNumber::query()
                ->latest()
                ->with(['offer.client:id,name', 'tollFreeNumber:id,number', 'station:id,title'])
                // ->offset($offset)
                // ->limit($this->perPage)
                ->get();
    }

    public function map($data): array
    {
        return [
            $data->offer?->client->name,
            $data->offer?->offer,
            $data->offer?->creative,
            $data->tollFreeNumber?->number,
            $data->station?->title,
            $data->state,
            $data->length,
            $data->master,
            $data->ad_id,
            $data->source_type === 1 ? 'Exclusive' : 'Shared',
            $data->website,
            $data->terminating_number,
            $data->data_type === 1 ? 'TFN' : ($data->data_type === 2 ? 'WEB' : 'TFN and WEB'),
            !empty($data->assigned_at) ? date('m/d/Y', strtotime($data->assigned_at)) : '',
            !empty($data->start_at) ? date('m/d/Y', strtotime($data->start_at)) : '',
            !empty($data->end_at) ? date('m/d/Y', strtotime($data->end_at)) : '',
            !empty($data->end_at) ? date('m/d/Y', strtotime($data->end_at)) : '',
        ];
    }
}
