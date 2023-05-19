<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TollFreeNumber extends Model
{
    use HasFactory;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->useLogName('TollFreeNumber')
        ->setDescriptionForEvent(fn (string $eventName) => "An item  has been {$eventName} .");
    }

    protected $fillable = [
        'number',
        'status',
    ];

    public function offerTollFreeNumbers(): HasMany
    {
        return $this->hasMany(OfferTollFreeNumber::class);
    }
}
