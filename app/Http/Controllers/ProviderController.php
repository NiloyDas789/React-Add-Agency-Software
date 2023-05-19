<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Provider;
use Illuminate\Http\Request;
use App\Exports\ProviderExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\StoreProviderRequest;
use App\Http\Requests\UpdateProviderRequest;

class ProviderController extends Controller
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

        $providers = Provider::query()
            ->when($orderBy != null, function ($query) use ($orderBy, $orderByType) {
                $query->orderBy($orderBy, $orderByType);
            })
            ->when($orderBy == null, function ($query) {
                $query->latest();
            })
            ->paginate($perPage)->onEachSide(1)->withQueryString();

        if ($providers->currentPage() > $providers->lastPage()) {
            $page = $providers->lastPage();
            return redirect()->route('providers.index', compact('page', 'perPage'));
        }

        return Inertia::render('Providers/Index', compact('providers', 'orderBy','orderByType'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProviderRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreProviderRequest $request)
    {
        Provider::create($request->validated());

        return back()->with('success', 'Provider created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function edit(Provider $provider)
    {
        if ($this->previous_route() !== 'providers.edit') {
            session()->put('prevUrl', url()->previous());
        }
        return Inertia::render('Providers/Edit', compact('provider'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProviderRequest  $request
     * @param  \App\Models\Provider  $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateProviderRequest $request, Provider $provider)
    {
        $provider->update($request->validated());

        $prevUrl = session('prevUrl');
        session()->pull('prevUrl');
        return redirect($prevUrl)->with('success', 'Provider updated successfully.');
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
            'ids.*' => ['required', 'exists:providers,id']
        ]);

        $provider = Provider::whereIn('id', $request->ids)->delete();

        activity('Provider')
            ->performedOn(new Provider())
            ->withProperties(['ids' => $request->ids])
            ->event('deleted')
            ->log($provider . ' items has been deleted.');

        return back()->with('message', 'Providers deleted successfully.');
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
        $providers = new ProviderExport($page, $perPage);
        return Excel::download($providers, 'providers.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
