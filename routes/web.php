<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AllReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeDupeDayController;
use App\Http\Controllers\DispositionController;
use App\Http\Controllers\ProviderFileController;
use App\Http\Controllers\QualificationController;
use App\Http\Controllers\RestrictedAniController;
use App\Http\Controllers\TollFreeNumberController;
use App\Http\Controllers\OfferTollFreeNumberController;
use App\Http\Controllers\ZipcodeByStationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProviderFileFieldController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::get('activity-log', [ActivityLogController::class, 'index'])->name('activity-log');
    Route::resource('users', UserController::class)->except('show', 'create', 'destroy');
    Route::resource('providers', ProviderController::class)->except('show', 'create', 'destroy');
    Route::resource('restricted-ani', RestrictedAniController::class)->except('show', 'destroy');
    Route::resource('provider-files', ProviderFileController::class)->except('show', 'edit', 'update');
    Route::resource('qualifications', QualificationController::class)->except('show', 'create', 'destroy');
    Route::resource('stations', StationController::class)->except('show', 'create', 'destroy');
    Route::resource('states', StateController::class)->except('show', 'create', 'destroy');
    Route::resource('offers', OfferController::class)->except('show', 'destroy');
    Route::resource('offerTollFreeNumbers', OfferTollFreeNumberController::class)->except('show', 'destroy');
    Route::resource('tollFreeNumbers', TollFreeNumberController::class)->except('show', 'create', 'destroy');
    Route::resource('reports', ReportController::class)->only('index', 'edit', 'update');
    Route::resource('de-dupe-days', DeDupeDayController::class)->except('show', 'create', 'destroy');
    Route::resource('dispositions', DispositionController::class)->except('show', 'create', 'destroy');
    Route::resource('zipcodeByStations', ZipcodeByStationController::class)->except('show', 'create', 'destroy');

    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile/update/{user}', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('provider-file-fields/{id}', [ProviderFileFieldController::class, 'index'])->name('provider_file_fields.index');
    Route::post('provider-file-fields', [ProviderFileFieldController::class, 'store'])->name('provider_file_fields.store');

    Route::get('all-reports', [AllReportController::class, 'index'])->name('all_reports.report_form');
    Route::get('all-reports-generate', [AllReportController::class, 'generateReport'])->name('all_reports.generate_report');

    Route::post('users-delete', [UserController::class, 'selectedDelete'])->name('users.delete');
    Route::post('providers-delete', [ProviderController::class, 'selectedDelete'])->name('providers.delete');
    Route::post('states-delete', [StateController::class, 'selectedDelete'])->name('states.delete');
    Route::post('qualifications-delete', [QualificationController::class, 'selectedDelete'])->name('qualifications.delete');
    Route::post('stations-delete', [StationController::class, 'selectedDelete'])->name('stations.delete');
    Route::post('offers-delete', [OfferController::class, 'selectedDelete'])->name('offers.delete');
    Route::post('offers-tfn-delete', [OfferTollFreeNumberController::class, 'selectedDelete'])->name('offers_TFN.delete');
    Route::post('toll-free-numbers-delete', [TollFreeNumberController::class, 'selectedDelete'])->name('toll_free_numbers.delete');
    Route::post('reports-delete', [ReportController::class, 'selectedDelete'])->name('reports.delete');
    Route::post('provider-file-delete', [ProviderFileController::class, 'selectedDelete'])->name('provider_files.delete');
    Route::post('de-dupe-days-delete', [DeDupeDayController::class, 'selectedDelete'])->name('de_dupe_days.delete');
    Route::post('disposition-delete', [DispositionController::class, 'selectedDelete'])->name('dispositions.delete');
    Route::post('restrictedAni-delete', [RestrictedAniController::class, 'selectedDelete'])->name('restricted_ani.delete');
    Route::post('zipcode-by-station-delete', [ZipcodeByStationController::class, 'selectedDelete'])->name('zipcode_by_station.delete');
    Route::post('provider-file-fields-delete', [ProviderFileFieldController::class, 'selectedDelete'])->name('provider_file_fields.delete');

    Route::get('providers/export/{page},{perPage}', [ProviderController::class, 'export'])->name('providers.export');
    Route::get('offers/export/{page},{perPage}', [OfferController::class, 'export'])->name('offers.export');
    Route::get('tfn/export/{page},{perPage}', [OfferTollFreeNumberController::class, 'export'])->name('tfn.export');
    Route::get('provider-file/export/{page},{perPage}', [ProviderFileController::class, 'export'])->name('provider_files.export');
    Route::get('reports/export/{page}/{perPage}/{search?}', [ReportController::class, 'export'])->name('report.export');
    Route::get('activity-log/export/{page},{perPage}', [ActivityLogController::class, 'export'])->name('activity_log.export');
    Route::post('toll-free-numbers-import', [TollFreeNumberController::class, 'tollFreeNumbersImport'])->name('tfn.import');
    Route::post('zipcode-by-station-import', [ZipcodeByStationController::class, 'zipcodeByStationImport'])->name('zipcode_by_station.import');
    Route::get('provider-file-download/{providerFile}', [ProviderFileController::class, 'providerFileDownload'])->name('provider_file.download');
});

require __DIR__ . '/auth.php';
