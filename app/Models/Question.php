<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuids;

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function host(): BelongsTo
    {
        return $this->belongsTo(Host::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function getVotesForHost1(): int
    {
        return $this->votes()->where('choice', 1)->count();
    }

    public function getVotesForHost2(): int
    {
        return $this->votes()->where('choice', 2)->count();
    }

    public function isVotingOpen(): bool
    {
        return $this->status === 'active';
    }

    public function isRevealed(): bool
    {
        return $this->status === 'revealed';
    }

    public function isVotingClosed(): bool
    {
        return $this->status === 'closed' || $this->status === 'revealed';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
