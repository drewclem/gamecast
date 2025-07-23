<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Question extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuids;

    // Question Types
    public const TYPE_OPEN = 'open';
    public const TYPE_LIVE = 'live';

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function toBroadcast(): array
    {
        $this->load('votes');

        $voteCounts = $this->getVoteCounts();
        $winners = $this->getWinningHosts();

        return [
            'id' => $this->id,
            'game_id' => $this->game_id,
            'question' => $this->question,
            'status' => $this->status,
            'is_active' => $this->is_active,
            'type' => $this->getType(),
            'winners' => $winners,
            'vote_counts' => $voteCounts,
            'votes_count' => $this->votes->count()
        ];
    }

    public function getType(): string
    {
        return $this->is_active ? self::TYPE_LIVE : self::TYPE_OPEN;
    }

    public function isOpenQuestion(): bool
    {
        return !$this->is_active;
    }

    public function isLiveQuestion(): bool
    {
        return $this->is_active;
    }

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

    public function scopeVotesForHost($query, $hostId)
    {
        return $query->votes()->where('host_id', $hostId);
    }

    public function getVoteCounts(): array
    {
        // Get all hosts from the associated game
        $gameHosts = $this->game->show->hosts()->pluck('id')->map(function ($id) {
            return (string)$id; // Ensure consistent string formatting
        })->toArray();

        // Initialize counts for all hosts to zero
        $voteCounts = array_fill_keys($gameHosts, 0);

        // Get actual vote counts
        $actualCounts = $this->votes()
            ->select('host_id', DB::raw('count(*) as count'))
            ->groupBy('host_id')
            ->get()
            ->pluck('count', 'host_id')
            ->toArray();

        // Merge with initialized counts (overwrite zeros with actual counts)
        foreach ($actualCounts as $hostId => $count) {
            $voteCounts[(string)$hostId] = (int)$count;
        }

        return [
            'byHost' => $voteCounts,
            'total' => array_sum($voteCounts)
        ];
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

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeOpen($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeLive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVotable($query)
    {
        return $query->where(function ($q) {
            $q->where('is_active', false)
                ->orWhere('status', 'active');
        });
    }

    public function canBeVotedOn(): bool
    {
        return $this->isOpenQuestion() || $this->status === 'active';
    }

    public function getWinningHost()
    {
        $voteCounts = $this->getVoteCounts();
        $winningHost = array_keys($voteCounts['byHost'], max($voteCounts['byHost']));
        return Host::find($winningHost)->first();
    }

    public function getWinningHosts()
    {
        $voteCounts = $this->getVoteCounts();
        $winningHosts = array_keys($voteCounts['byHost'], max($voteCounts['byHost']));
        return Host::whereIn('id', $winningHosts)->get();
    }

    public function votesByHost(): array
    {
        $gameHosts = $this->game->votableHosts->pluck('id')->map(function ($id) {
            return (string)$id;
        })->toArray();

        // Initialize counts for all hosts to zero
        $voteCounts = array_fill_keys($gameHosts, 0);

        // Get actual vote counts
        $actualCounts = $this->votes()
            ->select('host_id', DB::raw('count(*) as count'))
            ->groupBy('host_id')
            ->get()
            ->pluck('count', 'host_id')
            ->toArray();

        // Merge with initialized counts (overwrite zeros with actual counts)
        foreach ($actualCounts as $hostId => $count) {
            $voteCounts[(string)$hostId] = (int)$count;
        }

        return $voteCounts;
    }
}
