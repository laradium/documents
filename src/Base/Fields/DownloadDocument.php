<?php

namespace Laradium\Laradium\Documents\Base\Fields;

use Laradium\Laradium\Base\Field;
use Laradium\Laradium\Documents\Exceptions\NotDocumentableException;
use Laradium\Laradium\Documents\Interfaces\DocumentableInterface;

class DownloadDocument extends Field
{
    /**
     * @var string|null
     */
    protected $label;

    /**
     * @var bool
     */
    protected $withEdit = false;

    /**
     * @return array
     * @throws NotDocumentableException
     */
    public function formattedResponse(): array
    {
        if (!in_array(DocumentableInterface::class, class_implements($this->getModel()), true)) {
            throw new NotDocumentableException('The model isn\'t documentable');
        }

        $attributes = $this->getAttributes();
        $response = parent::formattedResponse();
        $response['config']['exists'] = $this->getModel()->exists;
        $response['config']['with_edit'] = $this->withEdit;
        $response['value'] = '#';

        if ($this->getModel()->exists) {
            $response['value'] = route('admin.documents.download', [
                'id'   => $this->getModel()->id,
                'type' => strtolower(class_basename($this->getModel()))
            ]);
        }

        $response['edit_field'] = null;
        if ($this->withEdit) {
            $response['edit_field'] = (new EditDocument([], $this->getModel()))->build($attributes[0] ? $attributes : [])->formattedResponse();
        }

        return $response;
    }

    /**
     * @param $label
     * @return DownloadDocument
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

    /**
     * @param bool $value
     * @return DownloadDocument
     */
    public function withEdit($value = true): self
    {
        $this->withEdit = $value;

        return $this;
    }
}
