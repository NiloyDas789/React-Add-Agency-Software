<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreZipcodeByStationRequest;
use App\Http\Requests\UpdateZipcodeByStationRequest;
use App\Imports\ZipcodeByStationImport;
use App\Models\ZipcodeByStation;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use function GuzzleHttp\Promise\all;

class ZipcodeByStationController extends Controller
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

        $perPage = $request->perPage ?: $this->itemPerPage;

        $search = $request->search;

        $zipcodeByStations = ZipcodeByStation::query()
        ->when($search != null, function ($query) use ($search) {
            $query->where('state', 'LIKE', "%{$search}%")
                  ->orWhere('area_code', 'LIKE', "%{$search}%")
                  ->orWhere('zip_code', 'LIKE', "%{$search}%");
        })
        ->when($orderBy != null, function ($query) use ($orderBy, $orderByType) {
            $query->orderBy($orderBy, $orderByType);
        })
        ->when($orderBy == null, function ($query) {
            $query->latest('created_at');
        })
        ->paginate($perPage)->onEachSide(1)->withQueryString();

        if ($zipcodeByStations->currentPage() > $zipcodeByStations->lastPage()) {
            $page = $zipcodeByStations->lastPage();

            return redirect()->route('zipcodeByStations.index', compact('page', 'perPage'));
        }

        return Inertia::render('ZipcodeByStation/Index', compact('zipcodeByStations', 'orderByType', 'orderBy', 'search'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreZipcodeByStationRequest $request)
    {
        $zipCodeByStateExist = DB::table('zipcode_by_stations')->get(['state', 'area_code', 'zip_code'])->where('state', strtoupper($request->state))->where('area_code', $request->area_code)->where('zip_code', $request->zip_code)->count();

        if ($zipCodeByStateExist > 0) {
            return back()->withErrors('This row already exists!');
        }

        ZipcodeByStation::create($request->validated());

        return back()->with('success', 'Row created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ZipcodeByStation  $zipcodeByStation
     * @return \Illuminate\Http\Response
     */
    public function edit(ZipcodeByStation $zipcodeByStation)
    {
        if ($this->previous_route() !== 'zipcodeByStations.edit') {
            session()->put('prevUrl', url()->previous());
        }

        return Inertia::render('ZipcodeByStation/Edit', compact('zipcodeByStation'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ZipcodeByStation  $zipcodeByStation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateZipcodeByStationRequest $request, ZipcodeByStation $zipcodeByStation)
    {
        $zipCodeByStateExist = DB::table('zipcode_by_stations')->get(['state', 'area_code', 'zip_code'])->where('state', strtoupper($request->state))->where('area_code', $request->area_code)->where('zip_code', $request->zip_code)->count();

        if ($zipCodeByStateExist > 1) {
            return back()->withErrors('This row already exists!');
        }

        $zipcodeByStation->update($request->validated());

        $prevUrl = session('prevUrl');
        session()->pull('prevUrl');

        return redirect($prevUrl)->with('success', 'Row updated successfully.');
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
            'ids.*' => ['required', 'exists:zipcode_by_stations,id']
        ]);

        ZipcodeByStation::whereIn('id', $request->ids)->delete();

        return back()->with('message', 'Rows deleted successfully.');
    }

    public function zipcodeByStationImport(Request $request)
    {
        set_time_limit(0);
        Excel::import(new ZipcodeByStationImport(), $request->file);
        return back()->with('message', 'Zipcode File imported successfully!');
    }
}
