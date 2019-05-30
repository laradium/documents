<?php

namespace Laradium\Laradium\Documents\Base\Fields;

use Laradium\Laradium\Base\Fields\Wysiwyg;
use Laradium\Laradium\Documents\Services\DocumentService;

class DocumentEditor extends Wysiwyg
{
    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        $response = parent::formattedResponse();
        $response['config']['placeholders'] = (new DocumentService())->getParser()->getFlatPlaceholders();

        return $response;
    }
}
