<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeDupeDaysRequest;
use App\Http\Requests\UpdateDeDupeDaysRequest;
use App\Models\DeDupeDay;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DeDupeDayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $deDupeDays = DeDupeDay::latest()->paginate($this->itemPerPage)->onEachSide(1);

        return Inertia::render('DeDupeDays/Index', compact('deDupeDays'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreDeDupeDaysRequest $request)
    {
        DeDupeDay::create($request->validated());

        return back()->with('success', 'De Dupe Days created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DeDupeDay  $deDupeDay
     * @return \Illuminate\Http\Response
     */
    public function edit(DeDupeDay $deDupeDay)
    {
        return Inertia::render('DeDupeDays/Edit', compact('deDupeDay'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DeDupeDay  $deDupeDay
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateDeDupeDaysRequest $request, DeDupeDay $deDupeDay)
    {
        $deDupeDay->update($request->validated());

        return back()->with('success', 'De dupe days updated successfully.');
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
            'ids.*' => ['required', 'exists:de_dupe_days,id']
        ]);

        $deDupeDay = DeDupeDay::whereIn('id', $request->ids)->delete();

        activity('DeDupeDay')
            ->performedOn(new DeDupeDay())
            ->withProperties(['ids' => $request->ids])
            ->event('deleted')
            ->log($deDupeDay . ' items has been deleted.');

        return back()->with('message', 'De dupe days deleted successfully.');
    }
}
