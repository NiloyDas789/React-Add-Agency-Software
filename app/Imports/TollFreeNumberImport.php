<?php

namespace App\Imports;

use App\Models\TollFreeNumber;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class TollFreeNumberImport implements ToModel, WithBatchInserts, WithChunkReading, WithValidation, SkipsEmptyRows, SkipsOnError, SkipsOnFailure
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new TollFreeNumber([
            'number' => $row['toll_free_number'],
        ]);
    }

    public function prepareForValidation($data)
    {
        $data['toll_free_number'] = substr(preg_replace('/[^\d]/', '', (string)($data[(array_keys($data)[0])])), -10);

        return $data;
    }

    public function rules(): array
    {
        return [
            'toll_free_number' => ['required', 'unique:toll_free_numbers,number']
        ];
    }

    public function onError(\Throwable $e)
    {
        // Handle the exception how you'd like.
    }

    public function onFailure(Failure ...$failures)
    {
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
