<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Offer extends Model
{
    use HasFactory;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->useLogName('Offer')
        ->setDescriptionForEvent(fn (string $eventName) => "An item  has been {$eventName} .");
    }

    protected $fillable = [
        'client_id',
        'provider_id',
        'offer',
        'dispositions',
        'creative',
        'billable_payout',
        'media_payout',
        'margin',
        'billable_call_duration',
        'de_dupe',
        'status',
        'start_at',
        'end_at',
    ];

    public function scopeActive($query)
    {
        $query->whereStatus(1);
    }

    public $searchFields = [
        'offer',
        'creative',
        'dispositions',
        'billable_payout',
        'media_payout',
        'margin',
        'billable_call_duration',
        'de_dupe',
    ];

    public function scopeSearch($query, $search)
    {
        foreach ($this->searchFields as $searchField) {
            $query->orWhere($searchField, 'like', '%' . $search . '%');
        }
        $query->orWhere('start_at', 'like', '%' . date('Y-m-d', strtotime($search)) . '%');
        $query->orWhere('end_at', 'like', '%' . date('Y-m-d', strtotime($search)) . '%');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function states(): BelongsToMany
    {
        return $this->belongsToMany(State::class);
    }

    public function qualifications(): BelongsToMany
    {
        return $this->belongsToMany(Qualification::class);
    }

    public function offerTollFreeNumbers(): HasMany
    {
        return $this->hasMany(OfferTollFreeNumber::class);
    }

    public function offerLengths(): HasMany
    {
        return $this->hasMany(OfferLength::class);
    }
}
