<?php

namespace App\Imports;

use App\Models\ZipcodeByStation;
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ZipcodeByStationImport implements ToModel, WithBatchInserts, WithChunkReading, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnError, SkipsOnFailure
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new ZipcodeByStation([
            'state'     => $row['state'],
            'area_code' => $row['area_code'],
            'zip_code'  => $row['zip_code'],
        ]);
    }

    public function prepareForValidation($data)
    {
        $data['state'] = (string)$data[(array_keys($data)[0])];
        $data['area_code'] = (string)$data[(array_keys($data)[1])];
        $data['zip_code'] = (strlen((string)$data[(array_keys($data)[2])]) < 5) ? str_pad((string)$data[(array_keys($data)[2])], 5, '0', STR_PAD_LEFT) : (string)$data[(array_keys($data)[2])];

        return $data;
    }

    public function rules(): array
    {
        return [
            'state'                  => ['required', 'min:2', 'max:2'],
            'area_code'              => ['required', 'min:3', 'max:3'],
            'zip_code'               => ['required', 'min:5', 'max:5'],
        ];
    }

    public function onFailure(Failure ...$failures)
    {
    }

    public function onError(\Throwable $e)
    {
        // Handle the exception how you'd like.
    }

    public function batchSize(): int
    {
        return 500;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
