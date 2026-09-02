<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'description', 'area', 'due_at', 'source'])]
class Task extends Model
{
    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<Activity, $this> */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function complete(): void
    {
        $this->forceFill([
            'status' => 'done',
            'completed_at' => now(),
        ])->save();
    }

    public function reopen(): void
    {
        $this->forceFill([
            'status' => 'open',
            'completed_at' => null,
        ])->save();
    }
}
