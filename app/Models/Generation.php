<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Generation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'template_id', 'type', 'model_used', 'prompt',
        'result', 'image_url', 'image_path', 'tokens_used',
        'word_count', 'cost', 'metadata', 'is_favorite',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_favorite' => 'boolean',
        'cost' => 'decimal:6',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }
}
