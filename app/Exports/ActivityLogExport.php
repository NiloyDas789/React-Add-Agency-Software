<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Spatie\Activitylog\Models\Activity;

class ActivityLogExport implements FromCollection, WithHeadings, WithMapping
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
            'User Name',
            'User Email',
            'Module',
            'Description',
            'Effected Ids',
            'Event',
            'Activity Time',
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $offset = ($this->pageNum - 1) * $this->perPage;

        return Activity::query()
                ->latest()
                ->with(['causer'])
                ->withCasts(['updated_at' => 'datetime:F j, Y, g:i a'])
                ->offset($offset)
                ->limit($this->perPage)
                ->get();
    }

    public function map($data): array
    {
        return [
            $data->causer?->name,
            $data->causer?->email,
            $data->log_name,
            $data->description,
            $data->subject_id ?? implode(', ', $data?->properties['ids']),
            $data->event,
            $data->updated_at,
        ];
    }
}
