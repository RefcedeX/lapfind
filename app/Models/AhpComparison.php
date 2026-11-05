<?php

namespace App\Models;

use Database\Factories\AhpComparisonFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'criteria_a_id', 'criteria_b_id', 'value'])]
class AhpComparison extends Model
{
    /** @use HasFactory<AhpComparisonFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'float',
        ];
    }

    /**
     * Get the user who made the pairwise comparison, if any.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get first criterion in the comparison pair.
     *
     * @return BelongsTo<Criterion, $this>
     */
    public function criteriaA(): BelongsTo
    {
        return $this->belongsTo(Criterion::class, 'criteria_a_id');
    }

    /**
     * Get second criterion in the comparison pair.
     *
     * @return BelongsTo<Criterion, $this>
     */
    public function criteriaB(): BelongsTo
    {
        return $this->belongsTo(Criterion::class, 'criteria_b_id');
    }
}
