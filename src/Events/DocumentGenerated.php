<?php

namespace Laradium\Laradium\Documents\Events;

use Illuminate\Queue\SerializesModels;
use Laradium\Laradium\Documents\Interfaces\DocumentableInterface;

class DocumentGenerated
{
    use SerializesModels;

    /**
     * @var DocumentableInterface
     */
    public $document;

    /**
     * DocumentGenerated constructor.
     *
     * @param DocumentableInterface $document
     */
    public function __construct(DocumentableInterface $document)
    {
        $this->document = $document;
    }
}
