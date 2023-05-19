<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\RestrictedAni;
use App\Http\Requests\StoreRestrictedAniRequest;
use App\Http\Requests\UpdateRestrictedAniRequest;

class RestrictedAniController extends Controller
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
        $restrictedAnis = RestrictedAni::query()
        ->when($orderBy != null, function ($query) use ($orderBy, $orderByType) {
            $query->orderBy($orderBy, $orderByType);
        })
        ->when($orderBy == null, function ($query) {
            $query->latest();
        })
        ->paginate($perPage)->onEachSide(1)->withQueryString();

        if ($restrictedAnis->currentPage() > $restrictedAnis->lastPage()) {
            $page = $restrictedAnis->lastPage();
            return redirect()->route('restricted-ani.index', compact('page', 'perPage'));
        }

        return Inertia::render('RestrictedAni/Index', compact('restrictedAnis', 'orderBy', 'orderByType'));
    }

     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('RestrictedAni/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreRestrictedAniRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRestrictedAniRequest $request)
    {
        RestrictedAni::create($request->validated());

        return redirect()->route('restricted-ani.index')->with('success', 'Restricted Ani created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RestrictedAni  $restrictedAni
     * @return \Illuminate\Http\Response
     */
    public function edit(RestrictedAni $restrictedAni)
    {
        if ($this->previous_route() !== 'restricted-ani.edit') {
            session()->put('prevUrl', url()->previous());
        }
        return Inertia::render('RestrictedAni/Edit', compact('restrictedAni'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRestrictedAniRequest  $request
     * @param  \App\Models\RestrictedAni  $restrictedAni
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRestrictedAniRequest $request, RestrictedAni $restrictedAni)
    {
        $restrictedAni->update($request->validated());

        $prevUrl = session('prevUrl');
        session()->pull('prevUrl');
        return redirect($prevUrl)->with('success', 'Restricted Ani updated successfully.');
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
            'ids.*' => ['required', 'exists:restricted_anis,id']
        ]);

        $restrictedAni = RestrictedAni::whereIn('id', $request->ids)->delete();

        activity('RestrictedAni')
            ->performedOn(new RestrictedAni())
            ->withProperties(['ids' => $request->ids])
            ->event('deleted')
            ->log($restrictedAni . ' items has been deleted.');

        return back()->with('message', 'Restricted Ani deleted successfully.');
    }
}
