<?php

namespace App\Http\Controllers;

use App\Exports\OfferExport;
use App\Http\Requests\StoreOfferRequest;
use App\Http\Requests\UpdateOfferRequest;
use App\Models\Disposition;
use App\Models\Offer;
use App\Models\OfferLength;
use App\Models\Provider;
use App\Models\Qualification;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class OfferController extends Controller
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

        $offers = Offer::query()
            ->leftJoin('users', 'offers.client_id', '=', 'users.id')
            ->leftJoin('providers', 'offers.provider_id', '=', 'providers.id')
            ->when($search != null, function ($query) use ($search) {
                $query->whereHas('client', function ($query) use ($search) {
                    return $query->where('name', 'like', "%{$search}%");
                })
                    ->orWhereHas('provider', function ($query) use ($search) {
                        return $query->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('offerLengths', function ($query) use ($search) {
                        return $query->where('length', 'like', "%{$search}%");
                    })
                    ->orWhereHas('qualifications', function ($query) use ($search) {
                        return $query->where('title', 'like', "%{$search}%");
                    })
                    ->orWhereHas('states', function ($query) use ($search) {
                        return $query->where('name', 'like', "%{$search}%");
                    })
                    ->search($search);
            })
            ->with(['qualifications:id,title', 'states:id,name', 'offerLengths'])
            ->when($orderBy != null, function ($query) use ($orderBy, $orderByType) {
                $query
                ->when($orderBy == 'billable_payout', function ($query) use ($orderByType) {
                    $query->orderByRaw('CONVERT(billable_payout, SIGNED)' . $orderByType);
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
            ->select('offers.*', 'users.name as client_name', 'providers.name as provider_name')
            ->paginate($perPage)->onEachSide(1)->withQueryString();

        if ($offers->currentPage() > $offers->lastPage()) {
            $page = $offers->lastPage();
            return redirect()->route('offers.index', compact('page', 'perPage'));
        }

        return Inertia::render('Offers/Index', compact('offers', 'search', 'orderBy', 'orderByType'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $providers = Provider::orderBy('name')->get();
        $clients = User::clients()->orderBy('name')->get();
        $qualifications = Qualification::get();
        $states = State::orderBy('name')->get();
        $dispositions = Disposition::orderBy('title')->get();
        $offerLengths = OfferLength::get();

        return Inertia::render('Offers/Create', compact('clients', 'providers', 'qualifications', 'states', 'dispositions', 'offerLengths'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreOfferRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreOfferRequest $request)
    {
        $offerInfo = $request->validated() + ['margin' => $request->billable_payout - $request->media_payout];

        $offerExists = Offer::where('offer', $request->offer)->where('creative', $request->creative)->where('client_id', $request->client_id)->exists();

        $request->validate([
            'lengths' => 'required',
        ]);

        if ($offerExists) {
            return back()->withErrors('The offer is already created');
        }

        DB::beginTransaction();
        try {
            $offer = Offer::create($offerInfo);

            $offerLengthsData = [];
            foreach ($request->lengths as $length) {
                array_push($offerLengthsData, ['offer_id' => $offer->id, 'length' => $length]);
            }

            OfferLength::insert($offerLengthsData);

            $offer->qualifications()->attach($request->qualification_ids);
            $offer->states()->attach($request->state_ids);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Something went wrong, please try again!');
        }

        return redirect()->route('offers.index')->with('success', 'Offer created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Offer  $offer
     * @return \Illuminate\Http\Response
     */
    public function edit(Offer $offer)
    {
        $offer->load('offerLengths', 'qualifications:id,title', 'states:id,name');

        $providers = Provider::orderBy('name')->get();
        $clients = User::clients()->orderBy('name')->get();
        $qualifications = Qualification::orderBy('title')->get();
        $states = State::orderBy('name')->get();
        $dispositions = Disposition::orderBy('title')->get();
        if ($this->previous_route() !== 'offers.edit') {
            session()->put('prevUrl', url()->previous());
        }

        return Inertia::render('Offers/Edit', compact('offer', 'clients', 'providers', 'qualifications', 'states', 'dispositions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOfferRequest  $request
     * @param  \App\Models\Offer  $offer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateOfferRequest $request, Offer $offer)
    {
        $offerInfo = $request->validated() + ['margin' => $request->billable_payout - $request->media_payout];

        $request->validate([
            'lengths' => 'required',
        ]);

        $offerExists = Offer::where([
            'offer'     => $request->offer,
            'creative'  => $request->creative,
            'client_id' => $request->client_id
        ])->first();

        if ($offerExists && $offer->client_id !== $offerExists->client_id) {
            if ((int)$request->client_id === $offerExists->client_id && $request->offer === $offerExists->offer && $request->creative === $offerExists->creative) {
                return back()->withErrors('The offer is already created');
            }
        }

        DB::beginTransaction();
        try {
            $offer->update($offerInfo);

            OfferLength::where('offer_id', $request->offer_id)->delete();

            $offerLengthsData = [];
            foreach ($request->lengths as $length) {
                array_push($offerLengthsData, ['offer_id' => $offer->id, 'length' => $length]);
            }
            OfferLength::insert($offerLengthsData);

            $offer->qualifications()->sync($request->qualification_ids);
            $offer->states()->sync($request->state_ids);

            $offerCount = Offer::where([
                'offer'     => $request->offer,
                'creative'  => $request->creative,
                'client_id' => $request->client_id
            ])->count();

            if ($offerCount > 1) {
                return back()->withErrors('The offer is already created');
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Something went wrong, please try again..!');
        }

        $prevUrl = session('prevUrl');
        session()->pull('prevUrl');

        return  redirect($prevUrl)->with('success', 'Offer updated successfully.');
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
            'ids.*' => ['required', 'exists:offers,id']
        ]);

        $offer = Offer::whereIn('id', $request->ids)->delete();

        activity('Offer')
            ->performedOn(new Offer())
            ->withProperties(['ids' => $request->ids])
            ->event('deleted')
            ->log($offer . ' items has been deleted.');

        return back()->with('message', 'Offers deleted successfully.');
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
        $offers = new OfferExport($page, $perPage);
        return Excel::download($offers, 'offers.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
