<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, HasUuids;

    // Actual Supabase column is 'cake_name'
    protected $fillable = [
        'cake_name',
        'description',
        'price',
        'weight_grams',
        'image_url',
        'is_active',
    ];

    // Supabase table has no updated_at
    const UPDATED_AT = null;

    // Map created_at to timestamptz
    protected $dates = ['created_at'];

    protected function casts(): array
    {
        return [
            'price'        => 'integer',
            'weight_grams' => 'integer',
            'is_active'    => 'boolean',
        ];
    }

    /** Scope: only active/published products */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
