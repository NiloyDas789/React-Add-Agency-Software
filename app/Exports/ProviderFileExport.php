<!-- <?php -->

namespace App\Exports;

use App\Models\ProviderFile;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProviderFileExport implements FromCollection, WithHeadings, WithMapping
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
            'Data Provider',
            'File Name',
            'Received At',
            'Last Updated At',
            'Process Status',
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $offset = ($this->pageNum - 1) * $this->perPage;

        return ProviderFile::query()
                ->latest()
                ->with(['provider:id,name'])
                // ->offset($offset)
                // ->limit($this->perPage)
                ->get();
    }
    public function map($data): array
    {
        return [
            $data->provider->name,
            $data->file_name,
            $data->received_at,
            $data->updated_at,
            $data->status,
        ];
    }
}
