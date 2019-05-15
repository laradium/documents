<?php

namespace Laradium\Laradium\Documents\Services;

use Illuminate\Support\Facades\File;
use Laradium\Laradium\Documents\Events\DocumentGenerated;
use Laradium\Laradium\Documents\Exceptions\MissingRelationException;
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
        $placeholders = [
            'global' => $this->basePlaceholders
        ];

        $models = $this->getDocumentableModels();

        foreach ($models as $model) {
            $model = app($model);

            if (!method_exists($model, 'getPlaceholders')) {
                continue;
            }

            foreach ($model->getPlaceholders() as $placeholder) {
                $nameSpace = snake_case(class_basename($model));

                $placeholders[$nameSpace][] = $nameSpace . '.' . $placeholder;
            }
        }

        foreach (config('laradium-documents.custom_placeholders') as $placeholder => $value) {
            $placeholders['custom'][] = $placeholder;
        }

        return $placeholders;
    }

    /**
     * @param DocumentableInterface $documentable
     * @return string
     * @throws MissingRelationException
     */
    public function render(DocumentableInterface $documentable): string
    {
        if (!isset($documentable->document)) {
            throw new MissingRelationException('Missing document relationship');
        }

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
     *
     * @TODO Refactor this, in something more readable
     */
    private function buildPlaceholderValues(DocumentableInterface $documentable): array
    {
        $values = [
            'placeholders' => [],
            'values'       => []
        ];

        foreach ($this->getPlaceholders() as $nameSpace => $placeHolders) {
            foreach ($placeHolders as $placeHolder) {
                $values['placeholders'][] = '{' . $placeHolder . '}';

                [$nameSpace, $property] = explode('.', $placeHolder, 2);

                if ($nameSpace === 'function') {
                    $values['values'][] = $this->runFunction($property);
                } elseif (isset(config('laradium-documents.custom_placeholders')[$placeHolder])) {
                    $customPlaceholder = config('laradium-documents.custom_placeholders')[$placeHolder];

                    $values['values'][] = is_callable($customPlaceholder) ? $customPlaceholder($documentable) : $customPlaceholder;
                } elseif ($nameSpace === strtolower(class_basename($documentable))) {
                    if (str_contains($property, '.')) {
                        [$relation, $subProperty] = explode('.', $property);

                        if (method_exists($documentable->$relation, $subProperty)) {
                            $values['values'][] = $documentable->$relation->$subProperty($documentable);
                        } else {
                            $values['values'][] = $documentable->$relation->$subProperty ?? '';
                        }
                    } else if (method_exists($documentable, $property)) {
                        $values['values'][] = $documentable->$property($documentable);
                    } else {
                        $values['values'][] = $documentable->$property ?? '';
                    }
                } else {
                    $values['values'][] = '';
                }
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
