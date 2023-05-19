<?php

namespace App\Http\Controllers;

use App\Exports\OfferTfnExport;
use App\Http\Requests\StoreOfferTollFreeNumberRequest;
use App\Http\Requests\UpdateOfferTollFreeNumberRequest;
use App\Models\Station;
use App\Models\Offer;
use App\Models\OfferTollFreeNumber;
use App\Models\Report;
use App\Models\TollFreeNumber;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class OfferTollFreeNumberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orderByType = $request->orderByType;
        $orderBy = $request->orderBy;
        $search = $request->search;
        $perPage = $request->perPage ?: $this->itemPerPage;

        $offerTFNs = OfferTollFreeNumber::query()
            ->leftJoin('toll_free_numbers', 'offer_toll_free_numbers.toll_free_number_id', 'toll_free_numbers.id')
            ->leftJoin('stations', 'offer_toll_free_numbers.station_id', 'stations.id')
            ->leftJoin('offers', 'offer_toll_free_numbers.offer_id', 'offers.id')
            ->leftJoin('users', 'offers.client_id', 'users.id')
            ->when($search != null, function ($query) use ($search) {
                $query
                ->whereHas('offer', function ($query) use ($search) {
                    return $query->where('offer', 'like', "%{$search}%")
                    ->orWhere('creative', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($query) use ($search) {
                        return $query->where('name', 'like', "%{$search}%");
                    });
                })
                ->orWhereHas('tollFreeNumber', function ($query) use ($search) {
                    $query->where('number', 'like', "%{$search}%");
                })
                ->orWhereHas('station', function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%");
                })
                ->orWhereHas('station', function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%");
                })
                ->orWhere(function ($query) use ($search) {
                    switch (Str::lower($search)) {
                        case 'exclusive':
                            $searchSourceType = 1;
                            break;
                        case 'shared':
                            $searchSourceType = 2;
                            break;
                        default:
                            $searchSourceType = null;
                            break;
                    }
                    $query->where('source_type', $searchSourceType);
                })
                ->orWhere(function ($query) use ($search) {
                    switch (Str::lower($search)) {
                        case 'tfn':
                            $searchDataType = [1, 3];
                            break;
                        case 'web':
                            $searchDataType = [2, 3];
                            break;
                        case 'tfn and web':
                            $searchDataType = [3];
                            break;
                        default:
                            $searchDataType = [];
                            break;
                    }
                    $query->whereIn('data_type', $searchDataType);
                })
                ->search($search);
            })
            ->when($orderBy != null, function ($query) use ($orderBy, $orderByType) {
                $query
                ->when($orderBy == 'toll_free_number', function ($query) use ($orderByType) {
                    $query->orderByRaw('CONVERT(toll_free_number, SIGNED)' . $orderByType);
                })
                ->when($orderBy == 'terminating_number', function ($query) use ($orderByType) {
                    $query->orderByRaw('CONVERT(terminating_number, SIGNED)' . $orderByType);
                })
                ->when($orderBy == 'media_payout', function ($query) use ($orderByType) {
                    $query->orderByRaw('CONVERT(media_payout, SIGNED)' . $orderByType);
                })
                ->when($orderBy == 'margin', function ($query) use ($orderByType) {
                    $query->orderByRaw('(CONVERT(margin, SIGNED)/CONVERT(billable_payout, SIGNED))*100 ' . $orderByType);
                })
                ->orderBy($orderBy, $orderByType);
            })
            ->when($orderBy == null, function ($query) {
                $query->latest();
            })
            ->select('offer_toll_free_numbers.*', 'offers.offer', 'offers.creative as offer_creative', 'users.name as client_name', 'toll_free_numbers.number as toll_free_number', 'stations.title as station_name')
            ->paginate($perPage)->onEachSide(1)->withQueryString();

        if ($offerTFNs->currentPage() > $offerTFNs->lastPage()) {
            $page = $offerTFNs->lastPage();
            return redirect()->route('offerTollFreeNumbers.index', compact('page', 'perPage'));
        }

        return Inertia::render('OfferTFNs/Index', compact('offerTFNs', 'search', 'orderBy', 'orderByType'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $clients = User::whereHas('offers')->orderBy('name')->get(['id', 'name']);
        $offers = Offer::with('offerLengths')->get(['id', 'client_id', 'offer', 'creative']);
        $stations = Station::get(['id', 'title']);
        $tollFreeNumbers = TollFreeNumber::get(['id', 'number']);
        $states = DB::table('zipcode_by_stations')->where('state', '!=', 'NULL')->orderBy('state')->distinct()->pluck('state')->toArray();

        return Inertia::render('OfferTFNs/Create', compact('clients', 'offers', 'stations', 'tollFreeNumbers', 'states'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreOfferTollFreeNumberRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOfferTollFreeNumberRequest $request)
    {
        $data = $request->validated();
        $data['lead_sku'] = $this->getLeadSku($data['lead_sku']);

        $request->validate(
            [
                'state' => ['nullable', 'string', 'max:255', 'required_if:source_type,2'],
            ],
            ['state.required_if' => 'The state field is required when source type is Shared']
        );

        $data['offer_id'] = Offer::where([
            'offer'      => $data['offer'],
            'creative'   => $data['creative'],
            'client_id'  => $data['client_id'],
        ])->firstOrFail()->id;

        $allOfferTfnState = explode(',', $request->state);

        $tfnExistForDate = OfferTollFreeNumber::where('toll_free_number_id', $data['toll_free_number_id'])
        ->whereBetween('end_at', [$data['start_at'], $data['end_at']])->first();

        DB::beginTransaction();

        try {
            for ($i = 0; $i < count($allOfferTfnState); $i++) {
                $offerTfn = OfferTollFreeNumber::where([
                    'toll_free_number_id'   => $data['toll_free_number_id']
                ])->first();

                $tfnExistForDate = OfferTollFreeNumber::query()
                ->when($data['source_type'] == 1, function ($q) use ($data) {
                    $q->where('toll_free_number_id', $data['toll_free_number_id'])
                    ->where(function ($q) use ($data) {
                        $q->whereBetween('start_at', [$data['start_at'], $data['end_at']])
                        ->orWhereBetween('end_at', [$data['start_at'], $data['end_at']]);
                    });
                })
                ->when($data['source_type'] == 2, function ($q) use ($data, $allOfferTfnState, $i) {
                    $q->where([
                        'toll_free_number_id'   => $data['toll_free_number_id'],
                        'offer_id'              => $data['offer_id'],
                        'station_id'            => $data['station_id'],
                        'state'                 => $allOfferTfnState[$i],
                    ])->where(function ($q) use ($data) {
                        $q->whereBetween('start_at', [$data['start_at'], $data['end_at']])
                        ->orWhereBetween('end_at', [$data['start_at'], $data['end_at']]);
                    });
                })->get();

                $latestEndDateDataOfExistingOTFN = OfferTollFreeNumber::where([
                    'toll_free_number_id'   => $data['toll_free_number_id']
                ])->orderBy('end_at', 'desc')->first();

                if (isset($offerTfn)) {
                    if ($data['offer_id'] != $offerTfn->offer_id) {
                        return back()->withErrors('One TFN can not be assigned to multiple offer');
                    }

                    if (!empty($data['lead_sku']) && !empty($offerTfn->lead_sku) && $data['lead_sku'] != $offerTfn->lead_sku) {
                        return back()->withErrors('This TFN already assigned for another LeadSKU');
                    }

                    if ($offerTfn->source_type == 1) {
                        if ((int)$data['source_type'] == 2) {
                            return back()->withErrors('This TFN already assigned as exclusive!');
                        }

                        $tfnWithoutEndDate = OfferTollFreeNumber::with(['tollFreeNumber:id,number', 'station:id,title'])->where([
                            'toll_free_number_id'   => $data['toll_free_number_id'],
                            'end_at'                => null
                        ])->first();

                        if (isset($tfnWithoutEndDate)) {
                            return back()->withErrors('Another station is assigned already, please set the end date for the TFN: ' . $tfnWithoutEndDate->tollFreeNumber->number . ' and station: ' . $tfnWithoutEndDate->station->title . ' then try again');
                        }

                        if ($tfnExistForDate->count() > 0) {
                            return back()->withErrors('You can not assign another station for this TFN from ' . $tfnExistForDate->first()->start_at . ' to ' . $tfnExistForDate->first()->end_at);
                        }
                    }

                    if ($offerTfn->source_type == 2) {
                        $stateForSharedTfn = $offerTfn->where([
                            'state'                    => $allOfferTfnState[$i],
                        ])->first();

                        if ((int)$data['source_type'] == 1 && $latestEndDateDataOfExistingOTFN->end_at > $data['start_at']) {
                            return back()->withErrors('This TFN already assigned as shared!');
                        }

                        $tfnWithoutEndDate = OfferTollFreeNumber::with(['tollFreeNumber:id,number', 'station:id,title'])->where([
                            'toll_free_number_id'   => $data['toll_free_number_id'],
                            'offer_id'              => $data['offer_id'],
                            'state'                 => $allOfferTfnState[$i],
                            'end_at'                => null
                        ])->first();

                        if (isset($tfnWithoutEndDate)) {
                            return back()->withErrors('Another station is assigned already, please set the end date for the TFN: ' . $tfnWithoutEndDate->tollFreeNumber->number . ' and station: ' . $tfnWithoutEndDate->station->title . ' then try again');
                        }

                        if ($tfnExistForDate->count() > 0) {
                            return back()->withErrors('You can not assign another station for this TFN from ' . $tfnExistForDate->first()->start_at . ' to ' . $tfnExistForDate->first()->end_at);
                        }

                        if (isset($stateForSharedTfn) && $stateForSharedTfn->station_id != (int)($data['station_id'])) {
                            if (!empty($stateForSharedTfn->end_at)) {
                                $count = $stateForSharedTfn->whereBetween('start_at', [$data['start_at'], $data['end_at']])->orWhereBetween('end_at', [$data['start_at'], $data['end_at']])->count();

                                if ($count > 0) {
                                    return back()->withErrors('The State already assigned for another shared station for that date range');
                                }
                            }
                        }
                    }
                }

                $leadSKU = OfferTollFreeNumber::where([
                    'lead_sku'              => $data['lead_sku'],
                    'toll_free_number_id'   => $data['toll_free_number_id'],
                    'offer_id'              => $data['offer_id'],
                    'station_id'            => $data['station_id'],
                    'state'                 => $allOfferTfnState[$i],
                    'length'                => $data['length']
                ])
                ->where('lead_sku', '!=', null)
                ->first();

                if (!empty($leadSKU)) {
                    return back()->withErrors('This LeadSKU already assigned');
                }

                $leadSkuTfn = OfferTollFreeNumber::where([
                    'lead_sku'              => $data['lead_sku'],
                ])
                ->where('lead_sku', '!=', null)
                ->first();

                if (!empty($leadSkuTfn) && $data['toll_free_number_id'] != $leadSkuTfn->toll_free_number_id) {
                    return back()->withErrors('This LeadSKU already assigned for another TFN');
                }

                $finalData = $data + ['state' => $allOfferTfnState[$i]];

                OfferTollFreeNumber::create($finalData);

                if (!empty($data['lead_sku'])) {
                    $leadSkuReports = Report::where('toll_free_number', null)->where('lead_sku', $data['lead_sku'])->get(['id', 'toll_free_number', 'lead_sku']);

                    if ($leadSkuReports->count() > 0) {
                        $reportId = [];

                        $tfnForLeadSku = OfferTollFreeNumber::with('tollFreeNumber:id,number')->where('lead_sku', $data['lead_sku'])->first('toll_free_number_id')->tollFreeNumber->number;

                        foreach ($leadSkuReports as $report) {
                            $reportId[] = $report->id;
                        }

                        DB::table('reports')->whereIn('id', $reportId)->update(
                            [
                                'toll_free_number'     => $tfnForLeadSku,
                                'updated_at'           => now()
                            ]
                        );
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Something went wrong, please try again!');
        }
        return redirect()->route('offerTollFreeNumbers.index')->with('success', 'OfferTollFreeNumber created successfully.');
    }

    protected function getLeadSku($leadSku)
    {
        if (strlen($leadSku) > 10) {
            $leadSkuString = preg_replace('/[^A-Za-z0-9]/', '', $leadSku);
            return (ctype_digit($leadSkuString) && strlen($leadSkuString) >= 10) ? substr($leadSkuString, -10) : $leadSku;
        }

        return $leadSku;
    }

    /**
         * Show the form for editing the specified resource.
         *
         * @param  \App\Models\OfferTollFreeNumber  $offerTollFreeNumber
         * @return \Illuminate\Http\Response
         */
    public function edit(OfferTollFreeNumber $offerTollFreeNumber)
    {
        $offerTollFreeNumber->load([
            'station:id,title',
            'tollFreeNumber:id,number',
            'offer:id,client_id,offer,creative' => ['client:id,name'],
        ]);

        $clients = User::whereHas('offers')->orderBy('name')->get(['id', 'name']);
        $offers = Offer::get(['id', 'client_id', 'offer', 'creative']);
        $offers = Offer::with('offerLengths')->get(['id', 'client_id', 'offer', 'creative']);
        $stations = Station::get(['id', 'title']);
        $tollFreeNumbers = TollFreeNumber::get(['id', 'number']);
        $states = DB::table('zipcode_by_stations')->where('state', '!=', 'NULL')->orderBy('state')->distinct()->pluck('state')->toArray();

        if ($this->previous_route() != 'offerTollFreeNumbers.edit') {
            session()->put('prevUrl', url()->previous());
        }
        return Inertia::render('OfferTFNs/Edit', compact('offerTollFreeNumber', 'clients', 'offers', 'stations', 'tollFreeNumbers', 'states'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOfferTollFreeNumberRequest  $request
     * @param  \App\Models\OfferTollFreeNumber  $offerTollFreeNumber
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateOfferTollFreeNumberRequest $request, OfferTollFreeNumber $offerTollFreeNumber)
    {
        $data = $request->validated() + ['state' => $request->state];
        $data['lead_sku'] = !empty($data['lead_sku']) ? $this->getLeadSku($data['lead_sku']) : $data['lead_sku'];

        DB::beginTransaction();

        try {
            $data['offer_id'] = Offer::where([
                'offer'      => $data['offer'],
                'creative'   => $data['creative'],
                'client_id'  => $data['client_id'],
            ])->firstOrFail()->id;

            $offerTfnExists = OfferTollFreeNumber::where([
                'toll_free_number_id'   => $data['toll_free_number_id']
            ])->first();

            $offerTfnStationExists = OfferTollFreeNumber::where([
                'toll_free_number_id'   => $data['toll_free_number_id'],
                'offer_id'              => $data['offer_id'],
                'station_id'            => $data['station_id'],
            ])->first();

            $stateForSharedTfn = OfferTollFreeNumber::where([
                'toll_free_number_id'      => $data['toll_free_number_id'],
                'source_type'              => $data['source_type'],
                'state'                    => $data['state'],
            ])->first();

            if (($offerTollFreeNumber->toll_free_number_id != (int)$data['toll_free_number_id']) || ($data['offer_id'] != $offerTollFreeNumber->offer_id)) {
                if (isset($offerTfnExists)) {
                    if ($offerTfnExists->source_type == 1) {
                        return back()->withErrors('This TFN already assigned as exclusive!');
                    }
                    if ((int)$data['source_type'] == 1) {
                        return back()->withErrors('This TFN already assigned as shared!');
                    }
                    if ($data['offer_id'] != $offerTfnExists->offer_id) {
                        return back()->withErrors('One TFN can not be assigned to multiple offer');
                    }
                    if (isset($offerTfnStationExists)) {
                        return back()->withErrors('The TFN already assigned as shared for this station!');
                    }
                }

                if (!empty($data['lead_sku'])) {
                    $leadSkuTfn = OfferTollFreeNumber::where([
                        'lead_sku'              => $data['lead_sku'],
                    ])
                    ->where('lead_sku', '!=', null)
                    ->first();

                    if ($leadSkuTfn->toll_free_number_id != $data['toll_free_number_id']) {
                        return back()->withErrors('This LeadSKU already assigned for another TFN!');
                    }
                }

                $offerTollFreeNumber->update($data);
            }

            if (!empty($data['lead_sku'])) {
                $offerTollFreeNumber->update($data);

                $existsLeadSKU = OfferTollFreeNumber::where('lead_sku', $data['lead_sku'])->get()->unique('toll_free_number_id');
                $existsLeadSKUTfn = OfferTollFreeNumber::where('toll_free_number_id', $data['toll_free_number_id'])->get()->unique('lead_sku');

                if ($existsLeadSKUTfn->count() > 1 || $existsLeadSKU->count() > 1) {
                    return back()->withErrors('This TFN and LeadSKU already assigned!');
                }
            }

            if ($offerTollFreeNumber->source_type == 1) {
                $offerTollFreeNumber->update($data);
            }

            if ($offerTollFreeNumber->source_type == 2) {
                if ((int)$data['source_type'] == 1) {
                    $offerTfnCount = OfferTollFreeNumber::where([
                        'toll_free_number_id'     => $data['toll_free_number_id'],
                    ])->count();
                    if ($offerTfnCount > 1) {
                        return back()->withErrors('This TFN already assigned more than once as shared!');
                    }
                    $offerTollFreeNumber->update($data);
                }

                if ($offerTfnStationExists || $stateForSharedTfn) {
                    $offerTollFreeNumber->update($data);

                    $offerTfnStationCount = OfferTollFreeNumber::where([
                        'toll_free_number_id'         => $data['toll_free_number_id'],
                        'offer_id'                    => $data['offer_id'],
                        'station_id'                  => $data['station_id'],
                        'state'                       => $data['state'],
                        'length'                      => $data['length'],
                    ])->count();

                    $stateForSharedTfnCount = OfferTollFreeNumber::where([
                        'toll_free_number_id'       => $data['toll_free_number_id'],
                        'source_type'               => $data['source_type'],
                        'state'                     => $data['state'],
                    ])->get();

                    if ($offerTfnStationCount > 1) {
                        return back()->withErrors('The TFN already assigned as shared for this station!');
                    }

                    if ($stateForSharedTfnCount->count() > 1 && $offerTollFreeNumber->station_id != (int)($data['station_id'])) {
                        return back()->withErrors('The State already assigned for another shared station!');
                    }
                }
            }

            if (!empty($data['lead_sku'])) {
                $leadSkuReports = Report::where('toll_free_number', null)->where('lead_sku', $data['lead_sku'])->get(['id', 'toll_free_number', 'lead_sku']);

                if ($leadSkuReports->count() > 0) {
                    $reportId = [];

                    $tfnForLeadSku = OfferTollFreeNumber::with('tollFreeNumber:id,number')->where('lead_sku', $data['lead_sku'])->first('toll_free_number_id')->tollFreeNumber->number;

                    foreach ($leadSkuReports as $report) {
                        $reportId[] = $report->id;
                    }

                    DB::table('reports')->whereIn('id', $reportId)->update(
                        [
                            'toll_free_number'     => $tfnForLeadSku,
                            'updated_at'           => now()
                        ]
                    );
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Something went wrong, please try again!');
        }

        $prevUrl = session('prevUrl');
        session()->pull('prevUrl');
        return redirect($prevUrl)->with('success', 'Offer TFN updated successfully!');
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
            'ids.*' => ['required', 'exists:offer_toll_free_numbers,id']
        ]);

        $offerTollFreeNumber = OfferTollFreeNumber::whereIn('id', $request->ids)->delete();

        activity('OfferTollFreeNumber')
            ->performedOn(new OfferTollFreeNumber())
            ->withProperties(['ids' => $request->ids])
            ->event('deleted')
            ->log($offerTollFreeNumber . ' items has been deleted.');

        return back()->with('message', 'Offers TFN deleted successfully.');
    }

    /**
     * Download Csv file.
     *
     *@param mixed $page
     *@param mixed $perPage
     *@return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export($page, $perPage)
    {
        $offerTfn = new OfferTfnExport($page, $perPage);
        return Excel::download($offerTfn, 'offerTfn.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
