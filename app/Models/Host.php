<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Host extends Model
{
    use HasFactory;
    use HasUuids;

    protected $guarded = ['id'];


    protected static function booted(): void
    {
        static::deleting(function (Host $host) {
            if ($host->user->current_host_id === $host->id) {
                $host->user->update(['current_host_id' => null]);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function show(): BelongsTo
    {
        return $this->belongsTo(Show::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }
}
