<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQualificationRequest;
use App\Http\Requests\UpdateQualificationRequest;
use App\Models\Qualification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QualificationController extends Controller
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

        $qualifications = Qualification::query()
        ->when($orderBy != null, function ($query) use ($orderBy, $orderByType) {
            $query->orderBy($orderBy, $orderByType);
        })
        ->when($orderBy == null, function ($query) {
            $query->latest();
        })
        ->paginate($perPage)->onEachSide(1)->withQueryString();

        if ($qualifications->currentPage() > $qualifications->lastPage()) {
            $page = $qualifications->lastPage();
            return redirect()->route('qualifications.index', compact('page', 'perPage'));
        }
        return Inertia::render('Qualifications/Index', compact('qualifications', 'orderBy','orderByType'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreQualificationRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreQualificationRequest $request)
    {
        Qualification::create($request->validated());

        return back()->with('success', 'Qualification created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Qualification  $qualification
     * @return \Illuminate\Http\Response
     */
    public function edit(Qualification $qualification)
    {
        if ($this->previous_route() !== 'qualifications.edit') {
            session()->put('prevUrl', url()->previous());
        }
        return Inertia::render('Qualifications/Edit', compact('qualification'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateQualificationRequest  $request
     * @param  \App\Models\Qualification  $qualification
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateQualificationRequest $request, Qualification $qualification)
    {
        $qualification->update($request->validated());

        $prevUrl = session('prevUrl');
        session()->pull('prevUrl');
        return redirect($prevUrl)->with('success', 'Qualification updated successfully.');
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
            'ids.*' => ['required', 'exists:qualifications,id']
        ]);

        $qualification = Qualification::whereIn('id', $request->ids)->delete();

        activity('Qualification')
            ->performedOn(new Qualification())
            ->withProperties(['ids' => $request->ids])
            ->event('deleted')
            ->log($qualification . ' items has been deleted.');

        return back()->with('message', 'Qualifications deleted successfully.');
    }
}
