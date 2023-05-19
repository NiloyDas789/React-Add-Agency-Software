<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class DeDupeDay extends Model
{
    use HasFactory;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->useLogName('DeDupeDay')
        ->setDescriptionForEvent(fn (string $eventName) => "An item  has been {$eventName} .");
    }

    protected $fillable = [
        'days',
        'status',
    ];
}
