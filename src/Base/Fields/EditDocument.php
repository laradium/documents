<?php

namespace Laradium\Laradium\Documents\Base\Fields;

use Laradium\Laradium\Base\Field;
use Laradium\Laradium\Documents\Exceptions\NotDocumentableException;
use Laradium\Laradium\Documents\Interfaces\DocumentableInterface;
use Laradium\Laradium\Documents\Services\DocumentService;

class EditDocument extends Field
{
    /**
     * @var string|null
     */
    protected $label;

    /**
     * @param $attributes
     * @return Field
     */
    public function build($attributes = []): Field
    {
        $this->fieldName($this->getModel()->getContentKey());
        $this->value($this->getModel()->getContent() ?? '');

        return parent::build($attributes);
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
        $response['config']['placeholders'] = (new DocumentService())->getParser()->getFlatPlaceholders();

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
