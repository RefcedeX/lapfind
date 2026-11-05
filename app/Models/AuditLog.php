<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    /**
     * Audit logs only store creation timestamps (immutable records).
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'user_role',
        'action',
        'category',
        'description',
        'ip_address',
        'user_agent',
        'method',
        'url',
        'properties',
        'created_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * The user that performed the action (if authenticated).
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Filter query by category.
     *
     * @param  Builder<static>  $query
     */
    public function scopeCategory(Builder $query, ?string $category): Builder
    {
        if ($category && $category !== 'all') {
            return $query->where('category', $category);
        }

        return $query;
    }

    /**
     * Filter query by search term.
     *
     * @param  Builder<static>  $query
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! empty($term)) {
            return $query->where(function (Builder $q) use ($term) {
                $q->where('description', 'like', "%{$term}%")
                    ->orWhere('user_name', 'like', "%{$term}%")
                    ->orWhere('user_email', 'like', "%{$term}%")
                    ->orWhere('ip_address', 'like', "%{$term}%")
                    ->orWhere('action', 'like', "%{$term}%");
            });
        }

        return $query;
    }

    /**
     * Get the badge color style according to action severity.
     */
    public function getSeverityColorAttribute(): string
    {
        if (str_contains($this->action, 'failed') || str_contains($this->action, 'delete') || str_contains($this->action, 'destroy')) {
            return 'rose';
        }

        if (str_contains($this->action, 'update') || str_contains($this->action, 'edit')) {
            return 'amber';
        }

        if (str_contains($this->action, 'create') || str_contains($this->action, 'store') || str_contains($this->action, 'login')) {
            return 'emerald';
        }

        return 'blue';
    }
}
