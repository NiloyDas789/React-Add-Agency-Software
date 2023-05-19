<?php

namespace App\Http\Controllers;

use App\Exports\ProviderExport;
use App\Http\Requests\StoreProviderFileRequest;
use App\Imports\ProviderFileImport;
use App\Models\Provider;
use App\Models\ProviderFile;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Storage;

class ProviderFileController extends Controller
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

        $providerFiles = ProviderFile::query()
        ->when($orderBy != null, function ($query) use ($orderBy, $orderByType) {
            $query->orderBy($orderBy, $orderByType);
        })
        ->when($orderBy == null, function ($query) {
            $query->latest();
        })
        ->with('provider')->paginate($perPage)->onEachSide(1)->withQueryString();

        if ($providerFiles->currentPage() > $providerFiles->lastPage()) {
            $page = $providerFiles->lastPage();
            return redirect()->route('provider-files.index', compact('page', 'perPage'));
        }

        return Inertia::render('ProviderFiles/Index', compact('providerFiles', 'orderBy', 'orderByType'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $providers = Provider::get();

        return Inertia::render('ProviderFiles/Create', compact('providers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProviderFileRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProviderFileRequest $request)
    {
        set_time_limit(0);

        $filterFields = [];
        foreach (json_decode($request->fieldMaps) as $value) {
            if (!empty($value->applicationField) && !empty($value->reportField)) {
                $filterFields[$value->applicationField] = Str::slug($value->reportField, '_');
            }
        }

        $oldData = Report::get([
            'id',
            'ani',
            'state',
            'area_code',
            'zip_code',
            'duration',
            'disposition',
            'provider_id',
            'toll_free_number',
            'lead_sku',
            DB::raw("DATE_FORMAT(called_at, '%Y-%m-%d %H:%i:%s') as called_at")
        ]);

        $allStateAreaCodes = DB::table('zipcode_by_stations')
            ->where('state', '!=', 'NULL')
            ->distinct()
            ->pluck('state', 'area_code');

        $allStateZipCodes = DB::table('zipcode_by_stations')
            ->where('state', '!=', 'NULL')
            ->distinct()
            ->pluck('state', 'zip_code');

        $allLeadSKU = DB::table('offer_toll_free_numbers')->join('toll_free_numbers', 'toll_free_numbers.id', '=', 'offer_toll_free_numbers.toll_free_number_id')->where('lead_sku', '!=', null)->pluck('number', 'lead_sku');

        $providerFileImport = new ProviderFileImport($filterFields, $request->provider_id, $oldData, $allStateAreaCodes, $allStateZipCodes, $allLeadSKU);

        DB::beginTransaction();
        try {
            ProviderFile::create([
                'provider_id' => $request->provider_id,
                'received_at' => $request->received_at,
                'file_name'   => Storage::putFileAs('provider_files/' . $request->provider_id, $request->file, time() . '_' . $request->file->getClientOriginalName()),
            ]);

            Excel::import($providerFileImport, $request->file('file'));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Something went wrong, please check your file and try again.'], 422);
        }

        $existSales = $providerFileImport->getAlreadyExist();
        $importedCount = $providerFileImport->getTotalSales() - count($existSales);

        if ($importedCount < 1) {
            return response()->json(['message' => 'All Data Already Exist.'], 422);
        }

        $data = false;
        $message = $importedCount . ' Rows Imported.';

        if (count($existSales) > 0) {
            $message .= "\n" . count($existSales) . ' Rows Already Exist.';
            $data = $existSales;
        }

        return response()->json(['message' => $message, 'alreadyExists' => $data], 201);
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Models\ProviderFile  $providerFile
    * @return \Illuminate\Http\RedirectResponse
    */
    public function destroy(ProviderFile $providerFile)
    {
        if (Storage::exists($providerFile->file_name)) {
            Storage::delete($providerFile->file_name);
        }

        $providerFile->delete();

        return back()->with('message', 'Provider File deleted successfully.');
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
            'ids.*' => ['required', 'exists:provider_files,id']
        ]);

        $allIds = $request->ids;
        $providers = ProviderFile::findOrFail($allIds)->pluck('file_name');

        foreach ($providers as $provider) {
            if (Storage::exists($provider)) {
                Storage::delete($provider);
            }
        }

        $providerFile = ProviderFile::whereIn('id', $request->ids)->delete();

        activity('ProviderFile')
            ->performedOn(new ProviderFile())
            ->withProperties(['ids' => $request->ids])
            ->event('deleted')
            ->log($providerFile . ' items has been deleted.');

        return back()->with('message', 'Provider File deleted successfully.');
    }

    public function providerFileDownload(ProviderFile $providerFile)
    {
        return Storage::download($providerFile->file_name);
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
        $providerFiles = new ProviderExport($page, $perPage);
        return Excel::download($providerFiles, 'providerFiles.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
