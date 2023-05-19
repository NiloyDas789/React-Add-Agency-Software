<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class ZipcodeByStation extends Model
{
    use HasFactory;

    protected $fillable = [
        'state',
        'area_code',
        'zip_code',
    ];

    protected function state(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => strtoupper($value),
        )->shouldCache();
    }
}
