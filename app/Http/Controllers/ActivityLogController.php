<?php

namespace App\Http\Controllers;

use App\Exports\ActivityLogExport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->perPage ?: $this->itemPerPage;
        $activityLogs = Activity::with(['causer'])
        ->latest()
        ->withCasts(['updated_at' => 'datetime:F j, Y, g:i a'])
        ->paginate($perPage)
        ->withQueryString()
        ->onEachSide(1);

        if ($activityLogs->currentPage() > $activityLogs->lastPage()) {
            $page = $activityLogs->lastPage();
            return redirect()->route('activity-log', compact('page', 'perPage'));
        }

        return Inertia::render('ActivityLogs/Index', compact('activityLogs'));
    }

    public function export($page, $perPage)
    {
        $activityLogs = new ActivityLogExport($page, $perPage);
        return Excel::download($activityLogs, 'activityLogs.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
