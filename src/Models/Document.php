<?php

namespace Laradium\Laradium\Documents\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    public const TYPE_CURRENT = 'current';
    public const TYPE_REVISION = 'revision';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'type',
        'revision',
        'key'
    ];

    /** Scopes */

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_CURRENT);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeRevision(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_REVISION);
    }

    /** Relationships */

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    /** Helpers */

    /**
     * @return Builder
     */
    public function revisions(): Builder
    {
        return self::where('key', $this->key)->revision();
    }
}
