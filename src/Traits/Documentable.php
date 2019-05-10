<?php

namespace Laradium\Laradium\Documents\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laradium\Laradium\Documents\Models\Document;

trait Documentable
{
    /**
     * @return array
     */
    public function getPlaceholders(): array
    {
        return $this->placeholders ?? $this->getFillable();
    }

    /** Relationships */

    /**
     * @return BelongsTo
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
