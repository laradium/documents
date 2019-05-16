<?php

namespace Laradium\Laradium\Documents\Events;

use Illuminate\Queue\SerializesModels;
use Laradium\Laradium\Documents\Models\Document;

class DocumentUpdated
{
    use SerializesModels;

    /**
     * @var Document
     */
    public $document;

    /**
     * DocumentUpdated constructor.
     *
     * @param Document $document
     */
    public function __construct(Document $document)
    {
        $this->document = $document;
    }
}
