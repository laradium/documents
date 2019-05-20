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
     * @return array
     * @throws NotDocumentableException
     */
    public function formattedResponse(): array
    {
        if (!in_array(DocumentableInterface::class, class_implements($this->getModel()), true)) {
            throw new NotDocumentableException('The model isn\'t documentable');
        }

        $this->fieldName($this->getModel()->getContentKey());
        $this->value($this->getModel()->getContent() ?? '');

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
