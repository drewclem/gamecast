<?php

namespace App\Models;

use App\Enums\GameStatus;
use App\Services\QrCodeService;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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

    protected static function booted(): void
    {
        static::created(function (Game $game) {
            $qrCodeService = app(QrCodeService::class);
            $qrCodeService->generateGameJoinQrCode($game);
        });
    }

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
        return $this->hasMany(Question::class);
    }

    public function openQuestions(): HasMany
    {
        return $this->questions()->where('is_active', false);
    }

    public function liveQuestions(): HasMany
    {
        return $this->questions()->where('is_active', true);
    }

    public function watchers(): HasMany
    {
        return $this->hasMany(Watcher::class);
    }

    public function activeQuestion(): HasOne
    {
        return $this->hasOne(Question::class, 'id', 'current_question_id');
    }

    public function isLive(): bool
    {
        return $this->status === GameStatus::LIVE;
    }

    public function votes(): HasManyThrough
    {
        return $this->hasManyThrough(Vote::class, Question::class);
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

    public function getQrCode(): string
    {
        return '/storage/games/' . $this->id . '/join-qr.png';
    }

    public function getQrCodeUrlAttribute(): string
    {
        return asset('storage/games/' . $this->id . '/join-qr.png');
    }

    public function regenerateQrCode(): string
    {
        $qrCodeService = app(QrCodeService::class);

        // First delete existing QR code if it exists
        $path = "qrcodes/game-{$this->slug}.png";
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        return $qrCodeService->generateGameJoinQrCode($this);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('title', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%");
    }

    public function votableHost1(): BelongsTo
    {
        return $this->belongsTo(Host::class, 'votable_host_1_id');
    }

    public function votableHost2(): BelongsTo
    {
        return $this->belongsTo(Host::class, 'votable_host_2_id');
    }

    public function votableHosts(): Attribute
    {
        return Attribute::make(
            get: function () {
                $hosts = collect();

                if ($this->votableHost1) {
                    $hosts->push($this->votableHost1);
                }

                if ($this->votableHost2) {
                    $hosts->push($this->votableHost2);
                }

                return $hosts;
            }
        );
    }
}
