<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class   Provider extends Model
{
    use HasFactory;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Provider')
            ->setDescriptionForEvent(fn (string $eventName) => "An item  has been {$eventName} .");
    }

    public const AUTO_DELIVERY = [
        'YES' => 1,
        'NO'  => 0,
    ];

    protected $fillable = [
        'name',
        'delivery_method',
        'response_type',
        'timezone',
        'delivery_days',
        'auto_delivery',
        'file_naming_convention',
        'contact_name',
        'contact_email',
        'status',
    ];

    public function scopeActive($query)
    {
        $query->whereStatus(1);
    }

    public function files(): HasMany
    {
        return $this->hasMany(ProviderFile::class);
    }
}
