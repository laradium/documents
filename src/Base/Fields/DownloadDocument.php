<?php

namespace Laradium\Laradium\Documents\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\Fields\Link;

class DownloadDocument extends Link
{
    /**
     * DownloadDocument constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        parent::__construct([
            array_get($parameters, 0, 'Download'),
            '#',
        ], $model);

        $this->attr([
            'target' => '_blank',
            'class'  => 'btn btn-primary mb-2',
        ]);
    }

    public function build($attributes = [])
    {
        $url = '#';

        if ($this->getModel()->exists) {
            $url = route('admin.documents.download', [
                'id'   => $this->getModel()->id,
                'type' => strtolower(class_basename($this->getModel()))
            ]);
        }

        $this->value($url);

        return parent::build($attributes);
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        $response = parent::formattedResponse();

        $response['type'] = 'link';
        $response['attr'] = $this->getAttr();

        if ($response['value'] === '#') {
            $response['attr']['class'] .= ' hidden';
        }

        return $response;
    }
}
