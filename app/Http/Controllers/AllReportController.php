<?php

namespace App\Http\Controllers;

use App\Exports\CreativeByStationReportExport;
use App\Exports\DataProviderReportExport;
use App\Exports\OffersPayoutExport;
use App\Exports\StationPayableReportExport;
use App\Exports\StationReportExport;
use App\Exports\TopLineCallReportExport;
use App\Exports\WeeklyPerformanceReportExport;
use App\Http\Traits\HasReportData;
use App\Models\Offer;
use App\Models\Provider;
use App\Models\Station;
use App\Models\TollFreeNumber;
use App\Models\User;
use Arr;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class AllReportController extends Controller
{
    use HasReportData;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clients = User::whereHas('offers')->orderBy('name')->get(['id', 'name']);
        $stations = Station::orderBy('title')->get(['id', 'title']);
        $tollFreeNumbers = TollFreeNumber::get(['id', 'number']);
        $offers = Offer::orderBy('offer')->get(['id', 'offer']);
        $providers = Provider::orderBy('name')->get(['id', 'name']);

        return Inertia::render('AllReport/Index', compact('clients', 'stations', 'tollFreeNumbers', 'offers', 'providers'));
    }

    public function generateReport(Request $request)
    {
        set_time_limit(0);

        $stationId = Arr::exists($request->data, 'station_id') ? array_map('intval', $request->data['station_id']) : [];
        $offerId = Arr::exists($request->data, 'offer_id') ? array_map('intval', $request->data['offer_id']) : [];
        $providerId = Arr::exists($request->data, 'provider_id') ? array_map('intval', $request->data['provider_id']) : [];
        $clientId = Arr::exists($request->data, 'client_id') ? array_map('intval', $request->data['client_id']) : [];
        $startDate = $request->data['start_date'] ?: '';
        $endDate = $request->data['end_date'] ?: '';
        $year = Arr::exists($request->data, 'year') ? array_map('intval', $request->data['year']) : [];

        $this->getReportData();

        switch($request->data['report_type_id']) {
            case('1'):
                return $this->topLineCallReport($stationId, $offerId, $providerId, $clientId, $startDate, $endDate, $year, $request->data['report_type_id']);

                break;
            case('2'):
                return Excel::download((
                    new StationPayableReportExport(
                        $this->joinTableData($stationId, $offerId, $providerId, $clientId, $startDate, $endDate, $year, $this->selectStationPayableColumn(), $request->data['report_type_id'])
                    )), 'station_payable_report.csv', \Maatwebsite\Excel\Excel::CSV);
                break;
            case('3'):
                return $this->stationsReport($stationId, $offerId, $providerId, $clientId, $startDate, $endDate, $year, $request->data['report_type_id']);

                break;
            case('4'):
                return Excel::download((
                    new CreativeByStationReportExport(
                        $this->joinTableData($stationId, $offerId, $providerId, $clientId, $startDate, $endDate, $year, $this->selectCreativeByStationColumn(), $request->data['report_type_id'])
                    )), 'creative_by_station_report.csv', \Maatwebsite\Excel\Excel::CSV);
                break;
            case('5'):
                return $this->offersPayoutReport($stationId, $offerId, $providerId, $clientId, $startDate, $endDate, $year, $request->data['report_type_id']);
                break;
            case('6'):
                return Excel::download((
                    new WeeklyPerformanceReportExport(
                        $this->joinTableData($stationId, $offerId, $providerId, $clientId, $startDate, $endDate, $year, $this->selectWeeklyPerformanceReportColumn(), $request->data['report_type_id']),
                        $startDate
                    )), 'weekly_performance_report.csv', \Maatwebsite\Excel\Excel::CSV);
                break;
            case('7'):
                return Excel::download((
                    new DataProviderReportExport($stationId, $offerId, $providerId, $clientId, $startDate, $endDate, $year)), 'data_provider_report.csv', \Maatwebsite\Excel\Excel::CSV);
                break;
        }
    }

    /**
    * @return \Illuminate\Support\Collection
    *
    */
    public function joinTableData($stationId, $offerId, $providerId, $clientId, $startDate, $endDate, $year, $selectedColumn, $reportType)
    {
        $reports = DB::table('reports')
        ->leftJoin('joinreports', 'joinreports.report_id', '=', 'reports.id')
        ->leftJoin('offer_toll_free_numbers', 'offer_toll_free_numbers.id', '=', 'joinreports.offer_toll_free_number_id')
        ->leftJoin('toll_free_numbers', 'toll_free_numbers.id', '=', 'offer_toll_free_numbers.toll_free_number_id')
        ->leftJoin('stations', 'stations.id', '=', 'offer_toll_free_numbers.station_id')
        ->leftJoin('offers', 'offers.id', '=', 'offer_toll_free_numbers.offer_id')
        ->leftJoin('providers', 'providers.id', '=', 'offers.provider_id')
        ->leftJoin('users', 'users.id', '=', 'offers.client_id')
        ->select($selectedColumn)
        ->when(!empty($stationId), function ($q) use ($stationId) {
            $q->whereIn('offer_toll_free_numbers.station_id', $stationId);
        })
        ->when(!empty($offerId), function ($q) use ($offerId) {
            $q->whereIn('offer_toll_free_numbers.offer_id', $offerId);
        })
        ->when(!empty($providerId), function ($q) use ($providerId) {
            $q->whereIn('reports.provider_id', $providerId);
        })
        ->when(!empty($clientId), function ($q) use ($clientId) {
            $q->whereIn('offers.client_id', $clientId);
        })
        ->when(!empty($startDate), function ($q) use ($startDate) {
            $q->whereDate('reports.called_at', '>=', $startDate);
        })
        ->when(!empty($endDate), function ($q) use ($endDate) {
            $q->whereDate('reports.called_at', '<=', $endDate);
        })
        ->when(!empty($year), function ($q) use ($year) {
            $q->whereIn(\DB::raw('year(reports.called_at)'), $year);
        })
        ->when(((int)($reportType) === 1), function ($q) {
            $q->where('reports.call_status', '!=', null);
        })
        ->when(((int)($reportType) === 2), function ($q) {
            $q->where('offer_toll_free_numbers.id', '!=', null)->groupBy('Station', 'source_type')->orderBy('total_billable_payout', 'DESC');
        })
        ->when(((int)($reportType) === 3), function ($q) {
            $q->where('offer_toll_free_numbers.id', '!=', null)->groupBy('reports.id')->orderBy('Station');
        })
        ->when(((int)($reportType) === 4), function ($q) {
            $q->where('offer_toll_free_numbers.id', '!=', null)->groupBy('year', 'offers.creative', 'Station')->orderBy('year');
        })
        ->when(((int)($reportType) === 6), function ($q) {
            $q->where('offer_toll_free_numbers.id', '!=', null)->where('reports.call_status', 'Billable')->groupBy('offers.client_id', 'offers.offer');
        })
        ->get();

        return $reports;
    }

    public function selectTopLineReportColumn()
    {
        return [
            'reports.id',
            DB::raw("(DATE_FORMAT(reports.called_at,'%m/%d/%Y')) as date"),
            DB::raw("(DATE_FORMAT(reports.called_at,'%H:%i:%s')) as time"),
            'reports.toll_free_number',
            'reports.state as stateName',
            'reports.zip_code',
            'reports.call_recording',
            'reports.credit',
            'reports.duration',
            'reports.credit_reason',
            'reports.area_code',
            'reports.disposition as reportDisposition',
            'reports.ani',
            'reports.call_status',
            'toll_free_numbers.number as assignedTfn',
            'offer_toll_free_numbers.toll_free_number_id',
            'offer_toll_free_numbers.station_id',
            'offer_toll_free_numbers.terminating_number',
            'offer_toll_free_numbers.length as creativeLength',
            'offer_toll_free_numbers.source_type',
            'offer_toll_free_numbers.state as tfnState',
            'offers.id as offerId',
            'offers.offer',
            'offers.creative',
            'offers.dispositions as dispositionTitle',
            'offers.de_dupe',
            'offers.billable_payout as offer_billable_payout',
            $this->calculateBillablePayout(),
            'offers.billable_call_duration',
            // 'offers.media_payout',
            $this->stationPayable(),
            'offers.start_at as offerStartingDate',
            'users.name as clientName',
            'providers.name as providerName',
            'stations.title as stationTitle',
            $this->findStation()
        ];
    }

    public function selectStationPayableColumn()
    {
        return [
            $this->findStation(),
            'offer_toll_free_numbers.source_type',
            DB::raw('COUNT(`reports`.`id`) AS leads'),
            $this->getTotalBillableOrMediaPayout('billable_payout'),
            $this->getTotalBillableOrMediaPayout('media_payout'),
        ];
    }

    public function selectStationReportColumn()
    {
        return [
            $this->findStation(),
            'offers.id as offerId',
            'offer_toll_free_numbers.toll_free_number_id',
            'offer_toll_free_numbers.station_id',
            'offers.offer',
            'offer_toll_free_numbers.ad_id',
            'offer_toll_free_numbers.length',
            'reports.toll_free_number',
            DB::raw("(DATE_FORMAT(reports.called_at,'%m/%d/%Y')) as date"),
            DB::raw("(DATE_FORMAT(reports.called_at,'%H:%i:%s')) as time"),
            'reports.state',
            'reports.zip_code',
            DB::raw('COUNT(`reports`.`id`) AS leads'),
            $this->getTotalBillableOrMediaPayout('media_payout'),
        ];
    }

    public function selectCreativeByStationColumn()
    {
        return [
            'users.name as clientName',
            'offers.offer as offerName',
            'offers.creative as creativeName',
            'reports.call_status as callStatus',
            $this->findStation(),
            $this->getTotalBillableOrMediaPayout('billable_payout'),
            DB::raw('year(reports.called_at) as year'),
        ];
    }

    public function selectOffersPayoutColumn()
    {
        return [
            'offers.id as offerId',
            'users.name as clientName',
            'offers.offer as offerName',
            'offers.creative as creativeName',
            'offer_toll_free_numbers.length',
            'offers.billable_payout',
            'offers.media_payout',
        ];
    }

    public function selectWeeklyPerformanceReportColumn()
    {
        return [
            DB::raw('year(reports.called_at) AS `year`'),
            DB::raw("(DATE_FORMAT(reports.called_at,'%m/%d/%Y')) as weekStartDate"),
            'users.name as client_name',
            'offers.offer as offer_name',
            $this->getTotalBillableOrMediaPayout('billable_payout'),
            $this->getTotalBillableOrMediaPayout('media_payout'),
        ];
    }

    public function topLineCallReport($stationId, $offerId, $providerId, $clientId, $startDate, $endDate, $year, $reportType)
    {
        $topLineCallReports = $this->joinTableData($stationId, $offerId, $providerId, $clientId, $startDate, $endDate, $year, $this->selectTopLineReportColumn(), $reportType);

        $allOfferTfnLengthsAdId = DB::table('offer_toll_free_numbers')->get(['offer_id', 'toll_free_number_id', 'station_id', 'length', 'ad_id']);

        return Excel::download((new TopLineCallReportExport($topLineCallReports, $allOfferTfnLengthsAdId)), 'top_line_call_report.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function stationsReport($stationId, $offerId, $providerId, $clientId, $startDate, $endDate, $year, $reportType)
    {
        $stationReports = $this->joinTableData($stationId, $offerId, $providerId, $clientId, $startDate, $endDate, $year, $this->selectStationReportColumn(), $reportType);

        $allOfferTfnLengthsAdId = DB::table('offer_toll_free_numbers')->get(['offer_id', 'toll_free_number_id', 'station_id', 'length', 'ad_id']);

        return Excel::download((new StationReportExport($stationReports, $allOfferTfnLengthsAdId)), 'station_report.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function offersPayoutReport($stationId, $offerId, $providerId, $clientId, $startDate, $endDate, $year)
    {
        $reports = DB::table('offer_toll_free_numbers')
                        ->join('toll_free_numbers', 'toll_free_numbers.id', 'offer_toll_free_numbers.toll_free_number_id')
                        ->join('stations', 'stations.id', '=', 'offer_toll_free_numbers.station_id')
                        ->join('offers', 'offers.id', '=', 'offer_toll_free_numbers.offer_id')
                        ->join('providers', 'providers.id', '=', 'offers.provider_id')
                        ->join('users', 'users.id', '=', 'offers.client_id')
                        ->select($this->selectOffersPayoutColumn())
                        ->when(!empty($stationId), fn ($q) => $q->whereIn('offer_toll_free_numbers.station_id', $stationId))
                        ->when(!empty($offerId), fn ($q) => $q->whereIn('offer_toll_free_numbers.offer_id', $offerId))
                        ->when(!empty($providerId), fn ($q) => $q->whereIn('offers.provider_id', $providerId))
                        ->when(!empty($clientId), fn ($q) => $q->whereIn('offers.client_id', $clientId))
                        ->when(!empty($startDate), fn ($q) => $q->whereDate('reports.start_at', $startDate))
                        ->when(!empty($endDate), fn ($q) => $q->whereDate('reports.end_at', $endDate))
                        ->when(!empty($year), fn ($q) => $q->whereIn(\DB::raw('year(offers.start_at)'), $year))
                        ->get();

        $offerIds = $reports->pluck('offerId')->toArray();

        $qualificationsOfAllOffer = DB::table('offers')
            ->join('offer_qualification', 'offer_qualification.offer_id', 'offers.id')
            ->join('qualifications', 'qualifications.id', 'offer_qualification.qualification_id')
            ->whereIn('offers.id', $offerIds)
            ->select('offers.id as offerId', 'qualifications.title  as qualificationTitle')
            ->get(['qualificationTitle', 'offerId']);

        $allIndexOfOfferQualificationIds = $qualificationsOfAllOffer->pluck('offerId')->toArray();
        $allIndexOfOfferQualificationTitle = $qualificationsOfAllOffer->pluck('qualificationTitle')->toArray();

        $statesOfAllOffer = DB::table('offers')
            ->join('offer_state', 'offer_state.offer_id', 'offers.id')
            ->join('states', 'states.id', 'offer_state.state_id')
            ->whereIn('offers.id', $offerIds)
            ->select('offers.id as offerId', 'states.name as restrictedState')
            ->get(['restrictedState', 'offerId']);

        $allIndexOfOfferIds = $statesOfAllOffer->pluck('offerId')->toArray();
        $allIndexOfOfferRestrictedState = $statesOfAllOffer->pluck('restrictedState')->toArray();

        $allOfferLengths = DB::table('offer_lengths')
        ->whereIn('offer_id', $offerIds)
        ->get(['offer_id', 'length']);

        return Excel::download((new OffersPayoutExport($reports, $allIndexOfOfferIds, $allIndexOfOfferRestrictedState, $allIndexOfOfferQualificationIds, $allIndexOfOfferQualificationTitle, $allOfferLengths)), 'offers_and_payout_report.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function getTotalBillableOrMediaPayout($value)
    {
        return DB::raw("SUM(CASE
            WHEN (`reports`.`call_status` = 'Billable') AND (`offers`.`$value` IS NOT NULL OR `offers`.`$value` != '') AND (`reports`.`revenue` IS NOT NULL OR  `reports`.`revenue` != '') THEN `offers`.`$value`

            WHEN (`reports`.`call_status` = 'Billable') AND (`offers`.`$value` IS NOT NULL OR `offers`.`$value` != '') AND (`reports`.`revenue` IS NULL OR `reports`.`revenue` = '') THEN `offers`.`$value`

            WHEN (`reports`.`call_status` = 'Billable') AND (`offers`.`$value` IS NULL OR `offers`.`$value` = '') AND (`reports`.`revenue` IS NULL OR  `reports`.`revenue` = '') THEN NULL

            ELSE CASE WHEN (`reports`.`call_status` = 'Billable') AND (`reports`.`revenue` IS NOT NULL OR `reports`.`revenue` != '') THEN Cast(`reports`.`revenue`/2 AS DECIMAL(16,2))
                ELSE 0
            END
        END) AS total_$value");
    }
}
