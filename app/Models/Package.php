<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'services',
        'price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => \App\Enums\PackageType::class,
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'services' => 'array',
        ];
    }
}
