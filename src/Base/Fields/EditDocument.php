<?php

namespace Laradium\Laradium\Documents\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\Field;
use Laradium\Laradium\Documents\Exceptions\NotDocumentableException;
use Laradium\Laradium\Documents\Interfaces\DocumentableInterface;

class EditDocument extends Field
{
    /**
     * @var string|null
     */
    protected $label;

    /**
     * EditDocument constructor.
     *
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        parent::__construct($parameters, $model);

        $this->fieldName($model->getContentKey());
        $this->value($model->getContent() ?? '');
    }

    /**
     * @return array
     * @throws NotDocumentableException
     */
    public function formattedResponse(): array
    {
        if (!in_array(DocumentableInterface::class, class_implements($this->getModel()), true)) {
            throw new NotDocumentableException('The model isn\'t documentable');
        }

        $response = parent::formattedResponse();

        $response['config']['exists'] = $this->getModel()->exists;

        return $response;
    }

    /**
     * @param $label
     * @return $this|Field
     */
    public function label($label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }
}
