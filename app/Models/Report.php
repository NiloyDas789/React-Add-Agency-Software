<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Report extends Model
{
    use HasFactory;
    use LogsActivity;

    public $timestamp = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->useLogName('Report')
        ->setDescriptionForEvent(fn (string $eventName) => "An item  has been {$eventName} .");
    }

    public const CREDIT = [
        'yes' => 1,
        'no'  => 0,
    ];

    public const CREDIT_REASON = [
        'courtesy_credit' => 1,
        'prank_caller'    => 2,
        'robot_dialer'    => 3,
        'spam'            => 4,
    ];

    protected $fillable = [
        'provider_id',
        'toll_free_number',
        'terminating_number',
        'ani',
        'lead_sku',
        'revenue',
        'duration',
        'disposition',
        'call_status',
        'state',
        'area_code',
        'zip_code',
        'call_recording',
        'credit',
        'credit_reason',
        'called_at',
    ];

    protected function creditReason(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ucfirst(str_replace('_', ' ', array_search($value, self::CREDIT_REASON))),
        )->shouldCache();
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    protected function disposition(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => ucwords($value),
        )->shouldCache();
    }
}
