<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Watcher extends Model
{
    use HasFactory;
    use HasUuids;

    protected $guarded = ['id'];

    protected $casts = [
        'last_active_at' => 'datetime',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function hasVotedOnQuestion(Question $question): bool
    {
        return $this->votes()->where('question_id', $question->id)->exists();
    }

    public function updateActivity(): void
    {
        $this->update([
            'last_active_at' => now(),
        ]);
    }
}
