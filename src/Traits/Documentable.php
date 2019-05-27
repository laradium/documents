<?php

namespace Laradium\Laradium\Documents\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laradium\Laradium\Documents\Models\Document;
use Laradium\Laradium\Documents\Services\DocumentService;

trait Documentable
{
    /**
     * Register the observers
     */
    public static function bootDocumentable(): void
    {
        static::saved(static function (self $model) {
            if (!$model->autoRender()) {
                return false;
            }

            (new DocumentService())->render($model);
        });
    }

    /**
     * @return array
     */
    public function getPlaceholders(): array
    {
        return $this->placeholders ?? array_merge($this->getFillable(), $this->getArrayableAppends());
    }

    /**
     * @return string
     */
    public function getContentKey(): string
    {
        return $this->contentKey ?? 'content';
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->{$this->getContentKey()} ?? '';
    }

    /**
     * @return bool
     */
    public function autoRender(): bool
    {
        $globalAutoRender = config('laradium-documents.auto_render', true);

        if (!property_exists($this, 'autoRender')) {
            return $globalAutoRender;
        }

        return $globalAutoRender && $this->autoRender;
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
