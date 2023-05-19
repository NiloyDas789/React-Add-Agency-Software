<?php

namespace App\Exports;

use App\Models\Provider;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProviderExport implements FromCollection, WithHeadings, WithMapping
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
            'Name',
            'Delivery Method',
            'Response Type',
            'Timezone',
            'Delivery Days',
            'Auto Delivery',
            'File Naming Convention',
            'Contact Name',
            'Contact Email',
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $offset = ($this->pageNum - 1) * $this->perPage;

        return Provider::query()
                ->latest()
                // ->offset($offset)
                // ->limit($this->perPage)
                ->get();
    }

    public function map($data): array
    {
        return [
            $data->name,
            $data->delivery_method,
            $data->response_type,
            $data->timezone,
            $data->delivery_days,
            $data->auto_delivery,
            $data->file_naming_convention,
            $data->contact_name,
            $data->contact_email,
        ];
    }
}
