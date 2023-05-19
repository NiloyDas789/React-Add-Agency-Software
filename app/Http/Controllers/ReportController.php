<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Http\Traits\HasReportData;
use App\Models\Report;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    use HasReportData;

    public $searchFields = [
        'toll_free_number',
        'offer_toll_free_numbers.terminating_number',
        'ani',
        'duration',
        'reports.disposition',
        'users.name',
        'offers.offer',
        'offers.creative',
        'providers.name',
        'call_status',
        'stations.title',
        'offers.billable_payout',
        'offers.media_payout',
        'reports.state',
        'zip_code',
        'call_recording',
        'credit',
        'credit_reason',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        set_time_limit(0);

        $orderByType = $request->orderByType;
        $orderBy = $request->orderBy;
        $search = $request->search;
        $pageNum = $request->page ? $request->page : 1;
        $perPage = $request->perPage ?: $this->itemPerPage;
        if (empty($search)) {
            $this->getReportData();
        }

        $reports = $this->showReportData($perPage, $pageNum, $search, $orderBy, $orderByType);

        if ($reports->currentPage() > $reports->lastPage()) {
            $page = $reports->lastPage();
            return redirect()->route('reports.index', compact('page', 'perPage'));
        }

        return Inertia::render('Report/Index', compact('reports', 'search', 'orderBy', 'orderByType'));
    }

    public function showReportData($perPage, $pageNum = 1, $search, $orderBy, $orderByType, $type = 'index')
    {
        $reports = DB::table('reports')
            ->select(
                'providers.name as providerName',
                'reports.id',
                DB::raw("(DATE_FORMAT(reports.called_at,'%m/%d/%Y %H:%i:%s')) as called_at"),
                DB::raw("(DATE_FORMAT(reports.called_at,'%m/%d/%Y')) as date"),
                DB::raw("(DATE_FORMAT(reports.called_at,'%H:%i:%s')) as time"),
                'reports.toll_free_number as toll_free_number',
                'reports.lead_sku as reportLeadSKU',
                'reports.state as stateName',
                'reports.zip_code',
                'reports.call_recording',
                'reports.credit',
                'reports.duration',
                'reports.credit_reason',
                'reports.area_code',
                'reports.disposition as reportDisposition',
                'reports.ani',
                'reports.terminating_number as reportTerminatingNumber',
                'reports.call_status',
                'toll_free_numbers.number as assignedTfn',
                'offer_toll_free_numbers.lead_sku as assignedLeadSKU',
                'offer_toll_free_numbers.terminating_number',
                'offer_toll_free_numbers.length as creativeLength',
                'offer_toll_free_numbers.source_type',
                'offer_toll_free_numbers.state as tfnState',
                'offer_toll_free_numbers.id as offerTfnId',
                'offers.id as offerId',
                'offers.offer',
                'offers.creative',
                'offers.de_dupe',
                'offers.billable_payout as offer_billable_payout',
                $this->calculateBillablePayout(),
                'reports.revenue',
                'offers.billable_call_duration',
                // 'offers.media_payout',
                $this->stationPayable(),
                'offers.dispositions as dispositionTitle',
                'offers.start_at as offerStartingDate',
                'stations.title as stationTitle',
                'users.name as clientName',
                $this->findStation()
            )
            ->leftJoin('joinreports', 'joinreports.report_id', '=', 'reports.id')
            ->leftJoin('offer_toll_free_numbers', 'offer_toll_free_numbers.id', '=', 'joinreports.offer_toll_free_number_id')
            ->leftJoin('toll_free_numbers', 'toll_free_numbers.id', '=', 'offer_toll_free_numbers.toll_free_number_id')
            ->leftJoin('stations', 'stations.id', '=', 'offer_toll_free_numbers.station_id')
            ->leftJoin('offers', 'offers.id', '=', 'offer_toll_free_numbers.offer_id')
            ->leftJoin('providers', 'providers.id', '=', 'offers.provider_id')
            ->leftJoin('users', 'users.id', '=', 'offers.client_id')
            ->when($search != null, function ($query) use ($search) {
                $query->where('called_at', 'LIKE', '%' . date('Y-m-d H:i:s', strtotime($search)) . '%');
                foreach ($this->searchFields as $searchField) {
                    $query->orWhere($searchField, 'like', '%' . $search . '%');
                }
            })
            ->when($orderBy != null, function ($query) use ($orderBy, $orderByType) {
                $query
                ->when($orderBy == 'toll_free_number', function ($query) use ($orderByType) {
                    $query->orderByRaw('CONVERT(toll_free_number, SIGNED)' . $orderByType);
                })
                ->when($orderBy == 'offer_toll_free_numbers.terminating_number', function ($query) use ($orderByType) {
                    $query->orderByRaw('CONVERT(offer_toll_free_numbers.terminating_number, SIGNED)' . $orderByType);
                })
                ->when($orderBy == 'ani', function ($query) use ($orderByType) {
                    $query->orderByRaw('CONVERT(ani, SIGNED)' . $orderByType);
                })
                ->when($orderBy == 'billable_payout', function ($query) use ($orderByType) {
                    $query->orderByRaw('CONVERT(billable_payout, SIGNED)' . $orderByType);
                })
                ->when($orderBy == 'media_payout', function ($query) use ($orderByType) {
                    $query->orderByRaw('CONVERT(media_payout, SIGNED)' . $orderByType);
                })
                ->when($orderBy == 'zip_code', function ($query) use ($orderByType) {
                    $query->orderByRaw('CONVERT(zip_code, SIGNED)' . $orderByType);
                })
                ->orderBy($orderBy, $orderByType);
            })
            ->when($orderBy == null, function ($query) {
                $query->latest('reports.created_at');
            })
            ->when($type === 'report', fn ($q) => $q->get())
            ->when($type !== 'report', fn ($q) => $q->paginate($perPage)->onEachSide(1)->withQueryString());

        return $reports;
    }

    /**
         * Show the form for editing the specified resource.
         *
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
    public function edit(Report $report)
    {
        if ($this->previous_route() !== 'reports.edit') {
            session()->put('prevUrl', url()->previous());
        }
        return Inertia::render('Report/Edit', compact('report'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Report $report)
    {
        $report->update($request->all());

        $prevUrl = session('prevUrl');
        session()->pull('prevUrl');
        return redirect($prevUrl)->with('success', 'Report updated successfully.');
    }

    /**
     * Remove selected resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function selectedDelete(Request $request)
    {
        $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['required', 'exists:reports,id']
        ]);

        $report = Report::whereIn('id', $request->ids)->delete();

        activity('Report')
            ->performedOn(new Report())
            ->withProperties(['ids' => $request->ids])
            ->event('deleted')
            ->log($report . ' items has been deleted.');

        return back()->with('message', 'Reports deleted successfully.');
    }

    /**
     * Download Csv file.
     *
     *@param mixed $page
     *@param mixed $perPage
     *@return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export($page, $perPage, $search = null)
    {
        $joinTable = $this->showReportData($perPage, $page, $search, $orderBy = null, $orderByType = null, 'report');

        $reports = new ReportExport($joinTable);

        return Excel::download($reports, 'reports.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
