<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStationRequest;
use App\Http\Requests\UpdateStationRequest;
use App\Models\Station;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StationController extends Controller
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

        $stations = Station::query()
        ->when($orderBy != null, function ($query) use ($orderBy, $orderByType) {
            $query->orderBy($orderBy, $orderByType);
        })
        ->when($orderBy == null, function ($query) {
            $query->latest();
        })
        ->paginate($perPage)->onEachSide(1)->withQueryString();

        if ($stations->currentPage() > $stations->lastPage()) {
            $page = $stations->lastPage();
            return redirect()->route('stations.index', compact('page', 'perPage'));
        }

        return Inertia::render('Stations/Index', compact('stations', 'orderBy','orderByType'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreStationRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreStationRequest $request)
    {
        Station::create($request->validated());

        return back()->with('success', 'Station created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Station  $station
     * @return \Illuminate\Http\Response
     */
    public function edit(Station $station)
    {
        if ($this->previous_route() !== 'stations.edit') {
            session()->put('prevUrl', url()->previous());
        }
        return Inertia::render('Stations/Edit', compact('station'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateStationRequest  $request
     * @param  \App\Models\Station  $station
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateStationRequest $request, Station $station)
    {
        $station->update($request->validated());

        $prevUrl = session('prevUrl');
        session()->pull('prevUrl');
        return redirect($prevUrl)->with('success', 'Station updated successfully.');
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
            'ids.*' => ['required', 'exists:stations,id']
        ]);

        $station = Station::whereIn('id', $request->ids)->delete();

        activity('Station')
            ->performedOn(new Station())
            ->withProperties(['ids' => $request->ids])
            ->event('deleted')
            ->log($station . ' items has been deleted.');

        return back()->with('message', 'Stations deleted successfully.');
    }
}
