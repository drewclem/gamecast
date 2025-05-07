<?php

namespace App\Models;

use App\Enums\GameStatus;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Game extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuids;

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'status' => GameStatus::class,
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function show(): BelongsTo
    {
        return $this->belongsTo(Show::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order_index');
    }

    public function watchers(): HasMany
    {
        return $this->hasMany(Watcher::class);
    }

    public function activeQuestion()
    {
        return $this->questions()->where('status', 'active')->first();
    }

    public function isLive(): bool
    {
        return $this->status === GameStatus::LIVE;
    }

    public function hasEnded(): bool
    {
        return $this->status === GameStatus::ENDED;
    }

    public function getTotalVotesForHost1(): int
    {
        return Vote::whereIn('question_id', $this->questions()->pluck('id'))
            ->where('choice', 1)
            ->count();
    }

    public function getTotalVotesForHost2(): int
    {
        return Vote::whereIn('question_id', $this->questions()->pluck('id'))
            ->where('choice', 2)
            ->count();
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('title', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%");
    }
}
