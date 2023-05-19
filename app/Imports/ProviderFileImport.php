<?php

namespace App\Imports;

use App\Models\Report;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class ProviderFileImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, SkipsEmptyRows
{
    protected $states;
    protected $anis;
    protected $areaCodes;
    protected $zipCodes;
    protected $durations;
    protected $dispositions;
    protected $providerIds;
    protected $tollFreeNumbers;
    protected $leadSKUs;
    protected $called_at;
    protected $salesCount;
    protected $alreadyExist;

    protected $allStateAreaCodes;
    protected $allStateZipCodes;
    protected $allLeadSKU;

    public function __construct(protected $fieldMap, protected $reqProviderId, $oldData, $allStateAreaCodes, $allStateZipCodes, $allLeadSKU)
    {
        $this->salesCount = 0;
        $this->alreadyExist = [];

        $this->states = $oldData->pluck('state', 'id')->toArray();
        $this->anis = $oldData->pluck('ani', 'id')->toArray();
        $this->areaCodes = $oldData->pluck('area_code', 'id')->toArray();
        $this->zipCodes = $oldData->pluck('zip_code', 'id')->toArray();
        $this->durations = $oldData->pluck('duration', 'id')->toArray();
        $this->dispositions = $oldData->pluck('disposition', 'id')->toArray();
        $this->providerIds = $oldData->pluck('provider_id', 'id')->toArray();
        $this->tollFreeNumbers = $oldData->pluck('toll_free_number', 'id')->toArray();
        $this->leadSKUs = $oldData->pluck('lead_sku', 'id')->toArray();
        $this->called_at = $oldData->pluck('called_at', 'id')->toArray();

        $this->allStateAreaCodes = $allStateAreaCodes;
        $this->allStateZipCodes = $allStateZipCodes;
        $this->allLeadSKU = $allLeadSKU;
    }

    public function getAlreadyExist(): array
    {
        return $this->alreadyExist;
    }

    public function getTotalSales(): int
    {
        return $this->salesCount;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->salesCount += 1;

        $calledAt = $this->getDateTime($row);

        if (!is_null($calledAt)) {
            if (gettype($calledAt) !== 'string') {
                $calledAt = Date::excelToTimestamp($calledAt, config('app.timezone'));
            }
            $calledAt = Carbon::parse($calledAt)->format('Y-m-d H:i:s');
        }

        $keys = array_keys($this->called_at, $calledAt);

        if (!empty($keys)) {
            foreach ($keys as $key) {
                if (
                    ($this->getNumber($row, !empty($this->getValue($row, 'toll_free_number')) ? $this->getValue($row, 'toll_free_number') . ' tfn_ani_terminating_number' : $this->getValue($row, 'lead_sku') . ' lead_sku') == $this->tollFreeNumbers[$key] || $this->getValue($row, 'lead_sku') == $this->leadSKUs[$key])
                &&
                      ($this->getNumber($row, !empty($this->getValue($row, 'ani')) ?? $this->getValue($row, 'ani') . ' tfn_ani_terminating_number') == $this->anis[$key] || $this->getZipCode($this->getValue($row, 'zip_code')) == $this->zipCodes[$key] || $this->getAreaCode($row) == $this->areaCodes[$key])
                &&
                      $this->reqProviderId == $this->providerIds[$key]
                &&
                      (!empty($this->getValue($row, 'disposition')) ? $this->getValue($row, 'disposition') : null == $this->dispositions[$key]) || (!empty($this->getDuration($row)) ? $this->getDuration($row) : null == $this->durations[$key])
                &&
                      $this->getState($row) == $this->states[$key]
                ) {
                    array_push($this->alreadyExist, $row);
                    return;
                }
            }
        }

        $importData = [
            'provider_id'          => $this->reqProviderId,
            'toll_free_number'     => $this->getNumber($row, !empty($this->getValue($row, 'toll_free_number')) ?
                                      $this->getValue($row, 'toll_free_number') . ' tfn_ani_terminating_number' : $this->getValue($row, 'lead_sku') . ' lead_sku'),

            'lead_sku'             => $this->getLeadSku($this->getValue($row, 'lead_sku')),

            'terminating_number'   => $this->getNumber($row, !empty($this->getValue($row, 'terminating_number')) ?
                                      $this->getValue($row, 'terminating_number') . ' tfn_ani_terminating_number' : null),

            'ani'                  => $this->getNumber($row, !empty($this->getValue($row, 'ani')) ?
                                      $this->getValue($row, 'ani') . ' tfn_ani_terminating_number' : null),

            'duration'             => $this->getDuration($row),
            'disposition'          => $this->getValue($row, 'disposition'),
            'state'                => $this->getState($row),
            'area_code'            => $this->getAreaCode($row),
            'zip_code'             => $this->getZipCode($this->getValue($row, 'zip_code')),
            'revenue'              => $this->getValue($row, 'revenue'),
            'call_recording'       => $this->getValue($row, 'call_recording'),
            'called_at'            => Carbon::createFromFormat('Y-m-d H:i:s', $calledAt, 'EST')->setTimezone(config('app.timezone')),
        ];

        return new Report($importData);
    }

    protected function getNumber($row, $phoneNumberOrSKU)
    {
        if (!empty($phoneNumberOrSKU) && str_contains($phoneNumberOrSKU, ' tfn_ani_terminating_number')) {
            $number = substr(preg_replace('/[^\d]/', '', $phoneNumberOrSKU), -10);

            return $number;
        } elseif (!empty($phoneNumberOrSKU)) {
            $leadSkuString = str_replace(' lead_sku', '', $phoneNumberOrSKU);
            $leadSKU = $this->getLeadSku($leadSkuString);
            $tollFreeNumber = !empty($this->allLeadSKU[$leadSKU]) ? $this->allLeadSKU[$leadSKU] : null;

            return $tollFreeNumber;
        }

        return !empty($phoneNumberOrSKU) ? $phoneNumberOrSKU : null;
    }

    protected function getLeadSku($leadSku)
    {
        if (strlen($leadSku) > 10) {
            $leadSkuString = preg_replace('/[^A-Za-z0-9]/', '', $leadSku);
            return (ctype_digit($leadSkuString) && strlen($leadSkuString) >= 10) ? substr($leadSkuString, -10) : $leadSku;
        }

        return $leadSku;
    }

    protected function getState($row)
    {
        $state = $this->getValue($row, 'state');

        $areaCode = $this->getAreaCode($row);

        $zipCode = $this->getZipCode($this->getValue($row, 'zip_code'));

        if (empty($state)) {
            if (!empty($zipCode)) {
                return !empty($this->allStateZipCodes[$zipCode]) ? $this->allStateZipCodes[$zipCode] : null;
            }

            if (!empty($areaCode)) {
                return !empty($this->allStateAreaCodes[$areaCode]) ? $this->allStateAreaCodes[$areaCode] : null;
            }
        }

        return $state;
    }

    protected function getZipCode($value)
    {
        if (!empty($value) && str_contains($value, '-')) {
            $trimValue = str_replace(['(', ')', ' '], '', $value);
            $arr = explode('-', $trimValue);

            $zip_code = (strlen($arr[0]) < 5) ? str_pad($arr[0], 5, '0', STR_PAD_LEFT) : $arr[0];

            return $zip_code;
        }

        $zip_code = ((!empty($value) && strlen($value) < 5)) ? str_pad($value, 5, '0', STR_PAD_LEFT) : $value;

        return !empty($zip_code) ? $zip_code : null;
    }

    protected function getAreaCode($row)
    {
        $areaCode = $this->getValue($row, 'area_code');

        if (!empty($areaCode)) {
            $number = preg_replace('/[^\d]/', '', $areaCode);
            return strlen($number) > 10 ? substr($number, 1, 3) : substr($number, 0, 3);
        }

        $ani = $this->getValue($row, 'ani');

        if (!empty($ani)) {
            $areaCodeFromAni = preg_replace('/[^\d]/', '', $ani);
            return strlen($areaCodeFromAni) > 10 ? substr($areaCodeFromAni, 1, 3) : substr($areaCodeFromAni, 0, 3);
        }

        return !empty($areaCode) ? $areaCode : null;
    }

    protected function getDuration($row)
    {
        $durationValue = $this->getValue($row, 'duration');

        if (!empty($durationValue) && gettype($durationValue) === 'double') {
            $duration = Date::excelToTimestamp($durationValue, config('app.timezone'));
            $durationTime = Carbon::parse(date('H:i:s', $duration))->toTimeString();
            $arr = explode(':', $durationTime);

            if ($durationValue >= 1) {
                return (int)$durationValue * 24 * 60 + (int)($arr[0] * 60 + (int)$arr[1]);
            }

            if (count($arr) === 3 && $arr[2] === '00') {
                return (int)($arr[0] * 60 + (int)$arr[1]);
            }

            if (count($arr) === 3 && $arr[2] !== '00') {
                return (int)($arr[0] * 3600 + (int)$arr[1] * 60 + (int)$arr[2]);
            }

            if (count($arr) === 2) {
                return (int)($arr[0] * 60 + (int)$arr[1]);
            }
        }

        if (!empty($durationValue) && gettype($durationValue) === 'string') {
            $arr = explode(':', $durationValue);

            if (count($arr) === 3 && $arr[2] === '00') {
                return (int)($arr[0] * 60 + (int)$arr[1]);
            }

            if (count($arr) === 3 && $arr[2] !== '00') {
                return (int)($arr[0] * 3600 + (int)$arr[1] * 60 + (int)$arr[2]);
            }

            if (count($arr) === 2) {
                return (int)($arr[0] * 60 + (int)$arr[1]);
            }
        }

        $callStartTimeValue = $this->getValue($row, 'call_start_time');
        $callEndTimeValue = $this->getValue($row, 'call_end_time');

        if (!empty($callStartTimeValue) && !empty($callEndTimeValue)) {
            $callStartTime = Date::excelToTimestamp($this->getValue($row, 'call_start_time'), config('app.timezone'));

            $callEndTime = Date::excelToTimestamp($this->getValue($row, 'call_end_time'), config('app.timezone'));

            return $callEndTime - $callStartTime;
        }

        return $durationValue;
    }

    protected function getValue($row, $key)
    {
        if (isset($this->fieldMap[$key])) {
            return $row[$this->fieldMap[$key]];
        }

        return null;
    }

    protected function getDateTime($row)
    {
        if (array_key_exists('call_date_time', $this->fieldMap)) {
            return $this->getValue($row, 'call_date_time');
        } elseif (array_key_exists('call_date', $this->fieldMap) && !array_key_exists('call_time', $this->fieldMap)) {
            return $this->getValue($row, 'call_date');
        } elseif (array_key_exists('call_date', $this->fieldMap) && array_key_exists('call_time', $this->fieldMap)) {
            return $this->mergeDateTime($row, ['call_date', 'call_time']);
        }

        return null;
    }

    protected function mergeDateTime($row, $dateTime)
    {
        $date = $this->getValue($row, $dateTime[0]);
        $time = $this->getValue($row, $dateTime[1]);

        if (!empty($date)) {
            if (gettype($date) !== 'string') {
                $date = Date::excelToTimestamp($date, config('app.timezone'));
                $time = Date::excelToTimestamp($time, config('app.timezone'));
            } else {
                $date = strtotime($date);
                $time = strtotime($time);
            }

            $called_at = !empty($time)
            ? Carbon::parse(date('d-m-Y', $date))->toDateString() . ' ' . Carbon::parse(date('H:i:s', $time))->toTimeString()
            : Carbon::parse(date('d-m-Y', $date))->toDateString();

            return $called_at;
        }

        return null;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
