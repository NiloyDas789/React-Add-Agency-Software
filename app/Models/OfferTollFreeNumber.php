<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class OfferTollFreeNumber extends Model
{
    use HasFactory;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->useLogName('OfferTollFreeNumber')
        ->setDescriptionForEvent(fn (string $eventName) => "1 Offer Tfn has been {$eventName} .");
    }

    public const SOURCE_TYPE = [
        'exclusive' => 1,
        'shared'    => 2,
    ];

    public const DATA_TYPE = [
        'tfn'     => 1,
        'web'     => 2,
        'tfn_web' => 3,
    ];

    public $searchFields = [
        'lead_sku',
        'state',
        'length',
        'master',
        'ad_id',
        'website',
        'terminating_number'
    ];

    public $searchDateFields = [
        'assigned_at',
        'offer_toll_free_numbers.start_at',
        'offer_toll_free_numbers.end_at',
        'test_call_at'
    ];

    public function scopeSearch($query, $search)
    {
        foreach ($this->searchFields as $searchField) {
            $query->orWhere($searchField, 'like', '%' . $search . '%');
        }
        foreach ($this->searchDateFields as $searchDateField) {
            $query->orWhere($searchDateField, 'like', '%' . date('Y-m-d', strtotime($search)) . '%');
        }
    }

        protected $fillable = [
            'offer_id',
            'toll_free_number_id',
            'station_id',
            'lead_sku',
            'state',
            'length',
            'master',
            'ad_id',
            'source_type',
            'website',
            'terminating_number',
            'data_type',
            'assigned_at',
            'start_at',
            'end_at',
            'test_call_at',
        ];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function tollFreeNumber(): BelongsTo
    {
        return $this->belongsTo(TollFreeNumber::class);
    }

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
