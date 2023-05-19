<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDispositionRequest;
use App\Http\Requests\UpdateDispositionRequest;
use App\Models\Disposition;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DispositionController extends Controller
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

        $dispositions = Disposition::query()
        ->when($orderBy != null, function ($query) use ($orderBy, $orderByType) {
            $query->orderBy($orderBy, $orderByType);
        })
        ->when($orderBy == null, function ($query) {
            $query->latest();
        })
        ->paginate($perPage)->onEachSide(1)->withQueryString();

        if ($dispositions->currentPage() > $dispositions->lastPage()) {
            $page = $dispositions->lastPage();
            return redirect()->route('dispositions.index', compact('page', 'perPage'));
        }
        return Inertia::render('Disposition/Index', compact('dispositions', 'orderBy','orderByType'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreDispositionRequest $request)
    {
        Disposition::create($request->validated());

        return back()->with('success', 'Data disposition created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Disposition  $disposition
     * @return \Illuminate\Http\Response
     */
    public function edit(Disposition $disposition)
    {
        if ($this->previous_route() !== 'dispositions.edit') {
            session()->put('prevUrl', url()->previous());
        }
        return Inertia::render('Disposition/Edit', compact('disposition'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Disposition  $disposition
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateDispositionRequest $request, Disposition $disposition)
    {
        $disposition->update($request->validated());

        $prevUrl = session('prevUrl');
        session()->pull('prevUrl');
        return redirect($prevUrl)->with('success', 'Data disposition updated successfully.');
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
            'ids.*' => ['required', 'exists:dispositions,id']
        ]);

        $disposition = Disposition::whereIn('id', $request->ids)->delete();

        activity('Disposition')
            ->performedOn(new Disposition())
            ->withProperties(['ids' => $request->ids])
            ->event('deleted')
            ->log($disposition . ' items has been deleted.');

        return back()->with('message', 'Data disposition deleted successfully.');
    }
}
