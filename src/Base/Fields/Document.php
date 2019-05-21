<?php

namespace Laradium\Laradium\Documents\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\Fields\Select;
use Laradium\Laradium\Documents\Models\Document as DocumentModel;

class Document extends Select
{
    /**
     * Document constructor.
     *
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        parent::__construct($parameters, $model);

        $this->label($this->getLabel() ?? 'Document');
        $this->options(DocumentModel::current()->pluck('title', 'id')->toArray());

        $this->rules('required|exists:documents,id');
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        $data = parent::formattedResponse();
        $data['type'] = 'select';

        return $data;
    }
}
