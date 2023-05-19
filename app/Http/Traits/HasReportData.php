<?php
namespace App\Http\Traits;

use DB;
use App\Models\Report;
use Carbon\Carbon;

trait HasReportData
{
    public function getReportData()
    {
        $this->reportData($this->joinReportTableData());
    }

    public function joinReportTableData()
    {
        $joinWithOfferTfn = DB::raw('(SELECT `offer_toll_free_numbers`.`id`
        FROM `offer_toll_free_numbers`
        WHERE `offer_toll_free_numbers`.`toll_free_number_id` = `toll_free_numbers`.`id` AND `offer_toll_free_numbers`.`state` = `reports`.`state` AND CURDATE() BETWEEN `offer_toll_free_numbers`.`start_at` AND `offer_toll_free_numbers`.`end_at` AND `offer_toll_free_numbers`.`end_at` IS NOT NULL
        UNION
        SELECT `offer_toll_free_numbers`.`id`
        FROM `offer_toll_free_numbers`
        WHERE `offer_toll_free_numbers`.`toll_free_number_id` = `toll_free_numbers`.`id` AND `offer_toll_free_numbers`.`state` = `reports`.`state` AND `offer_toll_free_numbers`.`end_at` IS NULL
        UNION
        SELECT `offer_toll_free_numbers`.`id`
        FROM `offer_toll_free_numbers`
        WHERE `offer_toll_free_numbers`.`toll_free_number_id` = `toll_free_numbers`.`id` AND CURDATE() BETWEEN `offer_toll_free_numbers`.`start_at` AND `offer_toll_free_numbers`.`end_at` AND `offer_toll_free_numbers`.`end_at` IS NOT NULL
        UNION
        SELECT `offer_toll_free_numbers`.`id`
        FROM `offer_toll_free_numbers`
        WHERE `offer_toll_free_numbers`.`toll_free_number_id` = `toll_free_numbers`.`id` AND `offer_toll_free_numbers`.`end_at` IS NULL
        LIMIT 1)');

        $reports = DB::table('reports')
            ->select(
                'reports.id',
                DB::raw("(DATE_FORMAT(reports.called_at,'%m/%d/%Y %H:%i:%s')) as called_at"),
                DB::raw("(DATE_FORMAT(reports.called_at,'%m/%d/%Y')) as date"),
                DB::raw("(DATE_FORMAT(reports.called_at,'%H:%i:%s')) as time"),
                'reports.toll_free_number as toll_free_number',
                'reports.state as stateName',
                'reports.zip_code',
                'reports.duration',
                'reports.area_code',
                'reports.disposition as reportDisposition',
                'reports.ani',
                'reports.call_status',
                'offer_toll_free_numbers.source_type',
                'offer_toll_free_numbers.id as offerTfnId',
                'offer_toll_free_numbers.end_at',
                'offers.id as offerId',
                'offers.de_dupe',
                'offers.end_at as offerEndDate',
                $this->calculateBillablePayout(),
                'reports.revenue',
                'offers.billable_call_duration',
                // 'offers.media_payout',
                $this->stationPayable(),
                'offers.dispositions as dispositionTitle',
                'offers.start_at as offerStartingDate',
                'users.name as clientName',
                'providers.name as providerName',
                $this->findStation()
            )
            ->join('toll_free_numbers', function ($join) {
                $join->on('reports.toll_free_number', '=', 'toll_free_numbers.number')
                    ->where(function ($q) {
                        $q->where('reports.call_status', null)->orWhere('reports.call_status', 'Billable')
                          ->on('reports.toll_free_number', '=', 'toll_free_numbers.number');
                    });
            })
            ->leftJoin('offer_toll_free_numbers', 'offer_toll_free_numbers.id', $joinWithOfferTfn)
            ->leftJoin('stations', 'stations.id', '=', 'offer_toll_free_numbers.station_id')
            ->leftJoin('offers', 'offers.id', '=', 'offer_toll_free_numbers.offer_id')
            ->leftJoin('providers', 'providers.id', '=', 'offers.provider_id')
            ->leftJoin('users', 'users.id', '=', 'offers.client_id')
            ->orderBy('reports.called_at')
            // ->latest('reports.created_at')
            ->get();

        return $reports;
    }

    public function reportData($reports)
    {
        $offerStates = DB::table('offer_state')->get();

        $statesIds = $offerStates->pluck('state_id', 'id')->toArray(); //[ 2=>67 3=>68 4=>69 5=>68 ]
        $offerIds = $offerStates->pluck('offer_id', 'id')->toArray(); //[ 2=>1 3=>2 4=>2 5=>1 ]

        $restrictedStates = DB::table('states')->whereIn('id', array_values($statesIds))->pluck('id', 'name')->toArray(); // [ "CA" => 14, "VA" => 6 ]

        $restrictedAni = DB::table('restricted_anis')->pluck('restricted_ani')->toArray();

        $allJoinedReportId = DB::table('joinreports')->pluck('report_id')->toArray();

        $nullOrBillableCallStatus = $reports->where('offerTfnId', '!=', null)->pluck('call_status', 'id');

        $joinReportArray = [];
        $duplicateAni = [];
        $firstCallDateUnixVal = [];
        $deDupe = '';
        $keyValueCollection = collect();

        foreach ($reports as $item) {
            $pivotIdsFromOffer = array_keys($offerIds, $item->offerId); // [ 0 => 2, 1 => 5 ]

            if (!empty($item->offerTfnId)) {
                //////////        Restricted for State          //////////

                if (!empty($item->offerId) && !empty($item->stateName) && in_array($item->stateName, array_keys($restrictedStates))) {
                    $restrictedStateIdOfItemState = !empty($restrictedStates[$item->stateName]) ? $restrictedStates[$item->stateName] : '';

                    $isStateRestricted = $this->findItemStateIsRestricted($restrictedStateIdOfItemState, $pivotIdsFromOffer, $statesIds);

                    if ($isStateRestricted) {
                        $item->call_status = 'Restricted';
                    }
                }

                //////////        Restricted for Ani          //////////

                if (!empty($item->ani) && in_array($item->ani, $restrictedAni)) {
                    $item->call_status = 'Restricted';
                }

                /////////         Billable and Not Qualified          /////////

                $reportDisposition = explode(',', $item->reportDisposition);
                $dispositionTitle = explode(',', $item->dispositionTitle);

                if (!empty($item->dispositionTitle) && (!empty($item->reportDisposition) || (string)$item->reportDisposition == (string)0) && (in_array($item->dispositionTitle, $reportDisposition) || in_array($item->reportDisposition, $dispositionTitle)) && $item->call_status != 'Restricted' && $item->call_status != 'Duplicate') {
                    $item->call_status = 'Billable';
                } elseif (!empty($item->dispositionTitle) && $item->call_status != 'Restricted' && $item->call_status != 'Duplicate') {
                    if (empty($item->reportDisposition)) {
                        $item->call_status = 'Not Qualified';
                    }
                    if ((string)$item->reportDisposition == (string)0 && (!in_array($item->dispositionTitle, $reportDisposition) || !in_array($item->reportDisposition, $dispositionTitle))) {
                        $item->call_status = 'Not Qualified';
                    }
                    if (!empty($item->reportDisposition) && (!in_array($item->dispositionTitle, $reportDisposition) || !in_array($item->reportDisposition, $dispositionTitle))) {
                        $item->call_status = 'Not Qualified';
                    }
                }
                if ((!empty($item->billable_call_duration) && !empty($item->duration)) && ($item->call_status != 'Restricted' && $item->call_status != 'Duplicate' && $item->call_status != 'Billable') || ((int)($item->duration) === 0 && !empty($item->billable_call_duration))) {
                    if ($item->duration < $item->billable_call_duration) {
                        $item->call_status = 'Not Qualified';
                    }
                    if ($item->duration >= $item->billable_call_duration) {
                        $item->call_status = 'Billable';
                    }
                }

                $currentDate = Carbon::now();
                $givenDate = Carbon::parse($item->offerEndDate);

                if ($currentDate > $givenDate) {
                    $item->call_status = 'Not Qualified';
                }

                /////////           Duplicate Call Detect         //////////

                if (!empty($item->ani) && $item->call_status == 'Billable') {
                    $calledDate = date('Y-m-d', strtotime($item->called_at));

                    if ($keyValueCollection->count() == 0) {
                        $keyValueCollection->push(['ani' => $item->ani, 'offer' => $item->offerId]);
                    }

                    if ($keyValueCollection->where('ani', $item->ani)->where('offer', $item->offerId)->count() > 0) {
                        if (array_key_exists($item->ani, $firstCallDateUnixVal)) {
                            $deDupe = strtotime(\Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d', $firstCallDateUnixVal[$item->ani]))->addDays($item->de_dupe == null ? $item->de_dupe = 0 : (int)$item->de_dupe)->toDateString());

                            if (strtotime($calledDate) > $deDupe) {
                                $firstCallDateUnixVal[$item->ani] = strtotime($calledDate);
                                $duplicateAni[] = $item->ani;
                            }

                            if (strtotime($calledDate) >= $firstCallDateUnixVal[$item->ani] && strtotime($calledDate) <= $deDupe) {
                                in_array($item->ani, $duplicateAni) ? $item->call_status = 'Duplicate' : $item->call_status = $item->call_status;
                            }
                        } else {
                            $firstCallDateUnixVal[$item->ani] = strtotime($calledDate);
                            $duplicateAni[] = $item->ani;
                        }
                    } else {
                        $keyValueCollection->push(['ani' => $item->ani, 'offer' => $item->offerId]);
                    }
                }
            } else {
                $item->call_status = null;
            }

            if (!empty($item->call_status)) {
                // insertable data to joinreports table

                $duplicateReportCount = in_array($item->id, $allJoinedReportId);

                if ($duplicateReportCount === false) {
                    $joinReportArray[] = [
                        'report_id'                    => $item->id,
                        'offer_toll_free_number_id'    => $item->offerTfnId,
                    ];
                }

                if ($nullOrBillableCallStatus[$item->id] !== $item->call_status) {
                    Report::where('id', $item->id)->update(['call_status' => $item->call_status]);
                }
            }
        }

        $joinReportArray = collect($joinReportArray);
        $chunks = $joinReportArray->chunk(100);

        foreach ($chunks as $chunk) {
            DB::table('joinreports')->insert($chunk->toArray());
        }

        return $reports;
    }

    public function calculateBillablePayout()
    {
        return DB::raw("(CASE
        WHEN (`offers`.`billable_payout` IS NOT NULL OR `offers`.`billable_payout` != '') AND (`reports`.`revenue` IS NOT NULL OR  `reports`.`revenue` != '') THEN `offers`.`billable_payout`

        WHEN (`offers`.`billable_payout` IS NOT NULL OR `offers`.`billable_payout` != '') AND (`reports`.`revenue` IS NULL OR `reports`.`revenue` = '') THEN `offers`.`billable_payout`

        WHEN (`offers`.`billable_payout` IS NULL OR `offers`.`billable_payout` = '') AND (`reports`.`revenue` IS NULL OR  `reports`.`revenue` = '') THEN NULL

        ELSE CASE WHEN (`reports`.`revenue` IS NOT NULL OR `reports`.`revenue` != '') THEN Cast(`reports`.`revenue`/2 AS DECIMAL(16,2))
              ELSE NULL
              END
        END) AS billable_payout");
    }

    public function findStation()
    {
        return DB::raw("(CASE
        WHEN `offer_toll_free_numbers`.`source_type` = 1 THEN `stations`.`title`
        WHEN `reports`.`state` <> `offer_toll_free_numbers`.`state` AND `offer_toll_free_numbers`.`source_type` = 2 THEN 'Unsourceable'
        WHEN `offer_toll_free_numbers`.`state` IS NULL AND `offer_toll_free_numbers`.`source_type` = 2 THEN 'Unsourceable'
        WHEN `reports`.`state` IS NULL AND `offer_toll_free_numbers`.`source_type` = 2 THEN 'Unsourceable'
          ELSE `stations`.`title`
          END) AS Station");
    }

    public function stationPayable()
    {
        return DB::raw("(CASE
        WHEN `offer_toll_free_numbers`.`end_at` IS NULL THEN `offers`.`media_payout`
        WHEN DATE_FORMAT(`reports`.`called_at`, '%Y-%m-%d') <= DATE_FORMAT(`offer_toll_free_numbers`.`end_at`, '%Y-%m-%d') THEN `offers`.`media_payout`
        ELSE 0
        END) AS media_payout");
    }

    /**
     * @param mixed $restrictedStateIdOfItemState
     *
     * @param mixed $pivotIds
     * @param mixed $statesIds
     * @return bool
     */
    public function findItemStateIsRestricted($restrictedStateIdOfItemState, $pivotIds, $statesIds)
    {
        $statesIdsOfItemsOffer[] = '';
        for ($i = 0; $i < sizeof($pivotIds); $i++) {
            $statesIdsOfItemsOffer[] = $statesIds[$pivotIds[$i]];
        }

        return in_array($restrictedStateIdOfItemState, $statesIdsOfItemsOffer);
    }
}
