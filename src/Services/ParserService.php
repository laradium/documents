<?php

namespace Laradium\Laradium\Documents\Services;

use Illuminate\Support\Facades\File;
use Laradium\Laradium\Documents\Events\DocumentGenerated;
use Laradium\Laradium\Documents\Interfaces\DocumentableInterface;

class ParserService
{
    /**
     * @var array
     */
    protected $basePlaceholders = [
        'function.date_time',
        'function.date',
        'function.date_long',
        'function.time',
    ];

    /**
     * @return array
     */
    public function getDocumentableModels(): array
    {
        $models = [];

        $modelPath = config('laradium-documents.model_path');
        $modelNamespace = config('laradium-documents.model_namespace');
        $classes = config('laradium-documents.models');

        foreach (File::files($modelPath) as $file) {
            $classes[] = $modelNamespace . '\\' . basename($file, '.php');
        }

        foreach ($classes as $class) {
            if (in_array(DocumentableInterface::class, class_implements($class), true)) {
                $models[] = $class;
            }
        }

        return $models;
    }

    /**
     * @return array
     */
    public function getPlaceholders(): array
    {
        $placeholders = $this->basePlaceholders;

        $models = $this->getDocumentableModels();

        foreach ($models as $model) {
            $model = app($model);

            if (!method_exists($model, 'getPlaceholders')) {
                continue;
            }

            foreach ($model->getPlaceholders() as $placeholder) {
                $placeholders[] = snake_case(class_basename($model)) . '.' . $placeholder;
            }
        }

        foreach (config('laradium-documents.custom_placeholders') as $placeholder => $value) {
            $placeholders[] = $placeholder;
        }

        return $placeholders;
    }

    /**
     * @param DocumentableInterface $documentable
     * @return string
     */
    public function render(DocumentableInterface $documentable): string
    {
        $template = $documentable->document->content;

        $replace = $this->buildPlaceholderValues($documentable);

        $content = str_replace($replace['placeholders'], $replace['values'], $template);

        $documentable->update([
            'content' => $content
        ]);

        event(new DocumentGenerated($documentable));

        return $content;
    }

    /**
     * @param DocumentableInterface $documentable
     * @return array
     */
    private function buildPlaceholderValues(DocumentableInterface $documentable): array
    {
        $values = [
            'placeholders' => [],
            'values'       => []
        ];

        foreach ($this->getPlaceholders() as $index => $placeHolder) {
            $values['placeholders'][$index] = '{' . $placeHolder . '}';

            [$nameSpace, $property] = explode('.', $placeHolder, 2);

            if ($nameSpace === 'function') {
                $values['values'][$index] = $this->runFunction($property);
            } elseif (isset(config('laradium-documents.custom_placeholders')[$placeHolder])) {
                $customPlaceholder = config('laradium-documents.custom_placeholders')[$placeHolder];

                $values['values'][$index] = is_callable($customPlaceholder) ? $customPlaceholder() : $customPlaceholder;
            } elseif ($nameSpace === strtolower(class_basename($documentable))) {
                if (str_contains($property, '.')) {
                    [$relation, $subProperty] = explode('.', $property);

                    $values['values'][$index] = $documentable->$relation->$subProperty ?? '';
                } else {
                    $values['values'][$index] = $documentable->$property ?? '';
                }
            } else {
                $values['values'][$index] = '';
            }
        }

        return $values;
    }

    /**
     * @param $name
     * @return string
     */
    private function runFunction($name): string
    {
        if ($name === 'date_time') {
            return now()->toDateTimeString();
        }

        if ($name === 'date') {
            return now()->toDateString();
        }

        if ($name === 'date_long') {
            return now()->toFormattedDateString();
        }

        if ($name === 'time') {
            return now()->toTimeString();
        }

        if (str_contains($name, 'date_format')) {
            [, $format] = explode('=', $name);

            return now()->format($format);
        }

        return '';
    }
}
