<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class RestrictedAni extends Model
{
    use HasFactory;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->useLogName('RestrictedAni')
        ->setDescriptionForEvent(fn (string $eventName) => "An item  has been {$eventName} .");
    }

    protected $fillable = [
        'restricted_ani',
        'date',
        'reason',
        'status',
    ];
}
