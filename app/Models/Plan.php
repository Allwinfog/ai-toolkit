<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'price', 'billing_cycle',
        'stripe_price_id', 'text_generations', 'image_generations',
        'code_generations', 'words_per_generation', 'allowed_models',
        'features', 'is_active', 'is_featured', 'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'allowed_models' => 'array',
        'features' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function isUnlimited(string $type): bool
    {
        return $this->{"{$type}_generations"} === -1;
    }
}
