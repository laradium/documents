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
        $url = '#';

        if ($model->exists) {
            $url = route('admin.documents.download', [
                'id'   => $model->id,
                'type' => strtolower(class_basename($model))
            ]);
        }

        parent::__construct([
            'Download',
            $url,
        ], $model);

        $this->attributes([
            'target' => '_blank',
            'class'  => 'btn btn-primary ' . (!$model->exists ? 'hidden' : ''),
        ]);
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        $response = parent::formattedResponse();

        $response['type'] = 'link';

        return $response;
    }
}
