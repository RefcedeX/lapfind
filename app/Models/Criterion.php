<?php

namespace App\Models;

use Database\Factories\CriterionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'name', 'type', 'unit', 'description'])]
class Criterion extends Model
{
    /** @use HasFactory<CriterionFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'criteria';

    /**
     * Determine if this criterion is a benefit type.
     */
    public function isBenefit(): bool
    {
        return $this->type === 'benefit';
    }

    /**
     * Determine if this criterion is a cost type.
     */
    public function isCost(): bool
    {
        return $this->type === 'cost';
    }

    /**
     * Get all alternative values for this criterion.
     *
     * @return HasMany<AlternativeValue, $this>
     */
    public function alternativeValues(): HasMany
    {
        return $this->hasMany(AlternativeValue::class, 'criteria_id');
    }

    /**
     * Get all laptop alternatives associated with this criterion.
     *
     * @return BelongsToMany<Alternative, $this>
     */
    public function alternatives(): BelongsToMany
    {
        return $this->belongsToMany(Alternative::class, 'alternative_values', 'criteria_id', 'alternative_id')
            ->withPivot('value')
            ->withTimestamps();
    }
}
