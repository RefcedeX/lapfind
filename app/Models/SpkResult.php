<?php

namespace App\Models;

use Database\Factories\SpkResultFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'title', 'weights_snapshot', 'ranking_snapshot', 'steps_snapshot'])]
class SpkResult extends Model
{
    /** @use HasFactory<SpkResultFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'weights_snapshot' => 'array',
            'ranking_snapshot' => 'array',
            'steps_snapshot' => 'array',
        ];
    }

    /**
     * Get the user that owns the SPK evaluation result.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
