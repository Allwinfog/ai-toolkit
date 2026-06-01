<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Billable;

    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'plan_id', 'role',
        'text_used', 'image_used', 'code_used', 'total_words_generated',
        'usage_reset_at', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token', 'stripe_id'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'usage_reset_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    // ── Relationships ──

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function generations()
    {
        return $this->hasMany(Generation::class);
    }

    // ── Usage Helpers ──

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function getPlanLimits(): array
    {
        if ($this->plan) {
            return [
                'text_generations' => $this->plan->text_generations,
                'image_generations' => $this->plan->image_generations,
                'code_generations' => $this->plan->code_generations,
                'words_per_generation' => $this->plan->words_per_generation,
            ];
        }

        return config('ai-toolkit.plans.free');
    }

    public function canGenerate(string $type): bool
    {
        $limits = $this->getPlanLimits();
        $limitKey = "{$type}_generations";
        $usedKey = "{$type}_used";

        if ($limits[$limitKey] === -1) return true;

        return $this->{$usedKey} < $limits[$limitKey];
    }

    public function getRemainingGenerations(string $type): int
    {
        $limits = $this->getPlanLimits();
        $limitKey = "{$type}_generations";
        $usedKey = "{$type}_used";

        if ($limits[$limitKey] === -1) return PHP_INT_MAX;

        return max(0, $limits[$limitKey] - $this->{$usedKey});
    }

    public function incrementUsage(string $type, int $words = 0): void
    {
        $this->increment("{$type}_used");
        if ($words > 0) {
            $this->increment('total_words_generated', $words);
        }
    }

    public function resetUsage(): void
    {
        $this->update([
            'text_used' => 0,
            'image_used' => 0,
            'code_used' => 0,
            'usage_reset_at' => now(),
        ]);
    }

    public function getUsageSummary(): array
    {
        $limits = $this->getPlanLimits();
        return [
            'text' => ['used' => $this->text_used, 'limit' => $limits['text_generations']],
            'image' => ['used' => $this->image_used, 'limit' => $limits['image_generations']],
            'code' => ['used' => $this->code_used, 'limit' => $limits['code_generations']],
            'total_words' => $this->total_words_generated,
        ];
    }
}
