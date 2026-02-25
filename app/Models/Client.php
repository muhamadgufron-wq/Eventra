<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'bride_name',
        'groom_name',
        'bride_phone',
        'groom_phone',
        'email',
        'event_date',
        'event_location',
        'package_id',
        'notes',
    ];

    public function package(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
        ];
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
