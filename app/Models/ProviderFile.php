<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ProviderFile extends Model
{
    use HasFactory;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->useLogName('ProviderFile')
        ->setDescriptionForEvent(fn (string $eventName) => "An item  has been {$eventName} .");
    }

    public const STATUS = [
        'complete'        => 1,
        'bad_file_format' => 2,
        'empty'           => 3,
        'pending'         => 4,
    ];

    protected $fillable = [
        'provider_id',
        'file_name',
        'status',
        'received_at',
    ];

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ucfirst(str_replace('_', ' ', array_search($value, self::STATUS))),
        )->shouldCache();
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    protected function receivedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('m/d/Y', strtotime($value)),
        )->shouldCache();
    }
}
