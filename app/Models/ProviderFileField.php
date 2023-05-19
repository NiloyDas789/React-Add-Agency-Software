<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProviderFileField extends Model
{
    use HasFactory;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('ProviderFileField')
            ->setDescriptionForEvent(fn (string $eventName) => "An item  has been {$eventName} .");
    }

    protected $fillable = [
        'provider_id',
        'field_maps',
    ];
}
