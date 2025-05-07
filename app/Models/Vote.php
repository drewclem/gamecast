<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vote extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuids;

    protected $guarded = ['id'];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function watcher(): BelongsTo
    {
        return $this->belongsTo(Watcher::class);
    }
}
