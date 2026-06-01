<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'icon', 'category', 'type',
        'system_prompt', 'user_prompt_template', 'fields', 'model',
        'max_tokens', 'is_active', 'is_premium', 'usage_count', 'sort_order',
    ];

    protected $casts = [
        'fields' => 'array',
        'is_active' => 'boolean',
        'is_premium' => 'boolean',
    ];

    public function generations()
    {
        return $this->hasMany(Generation::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function buildPrompt(array $inputs): string
    {
        $prompt = $this->user_prompt_template;
        foreach ($inputs as $key => $value) {
            $prompt = str_replace("{{{$key}}}", $value, $prompt);
        }
        return $prompt;
    }
}
