<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStateRequest;
use App\Http\Requests\UpdateStateRequest;
use App\Models\State;
use Inertia\Inertia;
use Illuminate\Http\Request;

class StateController extends Controller
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

        $states = State::query()
        ->when($orderBy != null, function ($query) use ($orderBy, $orderByType) {
            $query->orderBy($orderBy, $orderByType);
        })
        ->when($orderBy == null, function ($query) {
            $query->latest();
        })
        ->paginate($perPage)->onEachSide(1)->withQueryString();

        if ($states->currentPage() > $states->lastPage()) {
            $page = $states->lastPage();
            return redirect()->route('states.index', compact('page', 'perPage'));
        }
        return Inertia::render('States/Index', compact('states', 'orderBy', 'orderByType'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreStateRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreStateRequest $request)
    {
        State::create($request->validated());

        return back()->with('success', 'State created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function edit(State $state)
    {
        if ($this->previous_route() !== 'states.edit') {
            session()->put('prevUrl', url()->previous());
        }
        return Inertia::render('States/Edit', compact('state'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateStateRequest  $request
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateStateRequest $request, State $state)
    {
        $state->update($request->validated());

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
            'ids.*' => ['required', 'exists:states,id']
        ]);

        $state = State::whereIn('id', $request->ids)->delete();

        activity('State')
            ->performedOn(new State())
            ->withProperties(['ids' => $request->ids])
            ->event('deleted')
            ->log($state . ' items has been deleted.');

        return back()->with('message', 'States deleted successfully.');
    }
}
