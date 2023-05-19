<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTollFreeNumberRequest;
use App\Http\Requests\UpdateTollFreeNumberRequest;
use App\Imports\TollFreeNumberImport;
use App\Models\TollFreeNumber;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class TollFreeNumberController extends Controller
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

        $tollFreeNumbers = TollFreeNumber::query()
        ->when($orderBy != null, function ($query) use ($orderBy, $orderByType) {
            $query->orderBy($orderBy, $orderByType);
        })
        ->when($orderBy == null, function ($query) {
            $query->latest();
        })
        ->paginate($perPage)->onEachSide(1)->withQueryString();

        if ($tollFreeNumbers->currentPage() > $tollFreeNumbers->lastPage()) {
            $page = $tollFreeNumbers->lastPage();
            return redirect()->route('tollFreeNumbers.index', compact('page', 'perPage'));
        }
        return Inertia::render('TFNs/Index', compact('tollFreeNumbers', 'orderByType', 'orderBy'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreTollFreeNumberRequest $request)
    {
        TollFreeNumber::create($request->validated());

        return back()->with('success', 'Tax file number created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TollFreeNumber  $tollFreeNumber
     * @return \Illuminate\Http\Response
     */
    public function edit(TollFreeNumber $tollFreeNumber)
    {
        if ($this->previous_route() !== 'tollFreeNumbers.edit') {
            session()->put('prevUrl', url()->previous());
        }
        return Inertia::render('TFNs/Edit', compact('tollFreeNumber'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TollFreeNumber  $tollFreeNumber
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateTollFreeNumberRequest $request, TollFreeNumber $tollFreeNumber)
    {
        $tollFreeNumber->update($request->validated());

        $prevUrl = session('prevUrl');
        session()->pull('prevUrl');
        return redirect($prevUrl)->with('success', 'State updated successfully.');
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
            'ids.*' => ['required', 'exists:toll_free_numbers,id']
        ]);

        $tollFreeNumber = TollFreeNumber::whereIn('id', $request->ids)->delete();

        activity('TollFreeNumber')
            ->performedOn(new TollFreeNumber())
            ->withProperties(['ids' => $request->ids])
            ->event('deleted')
            ->log($tollFreeNumber . ' items has been deleted.');

        return back()->with('message', 'Tax file numbers deleted successfully.');
    }

    public function tollFreeNumbersImport(Request $request)
    {
        Excel::import(new TollFreeNumberImport(), $request->file);
        return back()->with('message', 'Tax file numbers imported successfully.');
    }
}
