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

        $customPlaceHolders = config('laradium-documents.custom_placeholders');

        if (!is_array($customPlaceHolders) && is_callable($customPlaceHolders)) {
            $customPlaceHolders = $customPlaceHolders();
        }

        foreach ($customPlaceHolders as $placeholder => $value) {
            $placeholders['custom'][] = $placeholder;
        }

        return $placeholders;
    }

    /**
     * @param DocumentableInterface $documentable
     * @param bool $useOriginal
     * @return string
     * @throws MissingRelationException
     */
    public function render(DocumentableInterface $documentable, $useOriginal = false): string
    {
        if (!isset($documentable->document)) {
            throw new MissingRelationException('Missing document relationship');
        }

        $template = $documentable->document->content;
        if ($documentable->content && !$useOriginal) {
            $template = $documentable->content;
        }

        $values = $this->getPlaceholderValues($documentable);

        $content = $this->replacePlaceholders($template, $values);

        $content = $this->parseMathOperations($content, $values);

        $content = $this->parseDefaultValues($content, $values);

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
    private function getPlaceholderValues(DocumentableInterface $documentable): array
    {
        $values = [];

        foreach ($this->getPlaceholders() as $nameSpace => $placeHolders) {
            foreach ($placeHolders as $placeHolder) {
                $values[$placeHolder] = $this->getPlaceholderValue($documentable, $placeHolder);
            }
        }

        return $values;
    }

    /**
     * @param DocumentableInterface $documentable
     * @param $placeHolder
     * @return mixed|string
     */
    private function getPlaceholderValue(DocumentableInterface $documentable, $placeHolder)
    {
        $customPlaceHolders = config('laradium-documents.custom_placeholders');

        if (!is_array($customPlaceHolders) && is_callable($customPlaceHolders)) {
            $customPlaceHolders = $customPlaceHolders();
        }

        [$nameSpace, $property] = explode('.', $placeHolder, 2);

        if ($nameSpace === 'function') {
            return $this->runFunction($property);
        }

        if (isset($customPlaceHolders[$placeHolder])) {
            $customPlaceholder = $customPlaceHolders[$placeHolder];

            return is_callable($customPlaceholder) ? $customPlaceholder($documentable) : $customPlaceholder;
        }

        if ($nameSpace === strtolower(class_basename($documentable))) {
            return $this->getDocumentableValue($documentable, $property);
        }

        return '';
    }

    /**
     * @param DocumentableInterface $documentable
     * @param $property
     * @return string
     */
    private function getDocumentableValue(DocumentableInterface $documentable, $property): string
    {
        if (str_contains($property, '.')) {
            [$relation, $subProperty] = explode('.', $property);

            if (method_exists($documentable->$relation, $subProperty)) {
                return $documentable->$relation->$subProperty($documentable);
            }

            return $documentable->$relation->$subProperty ?? '';
        }

        if (method_exists($documentable, $property)) {
            return $documentable->$property($documentable);
        }

        return $documentable->$property ?? '';
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

    /**
     * @param $template
     * @param $values
     * @return string
     */
    private function replacePlaceholders($template, $values): string
    {
        return str_replace(array_map(static function ($placeHolder) {
            return '{' . $placeHolder . '}';
        }, array_keys($values)), array_values($values), $template);
    }

    /**
     * @param $template
     * @param $values
     * @return string
     *
     * @TODO Modify this so the method could handle more complex mathematical operations
     */
    private function parseMathOperations($template, $values): string
    {
        return preg_replace_callback('/\\{(\S+)\s?(\+|\-|\\|\*)\s?(\S+)\}/', static function ($matches) use ($values) {
            $firstValue = $values[$matches[1]] ?? $matches[1];
            $operator = $matches[2];
            $secondValue = $values[$matches[3]] ?? $matches[3];

            if (is_numeric($firstValue) && is_numeric($secondValue)) {
                if ($operator === '+') {
                    return $firstValue + $secondValue;
                }

                if ($operator === '-') {
                    return $firstValue - $secondValue;
                }

                if ($operator === '*') {
                    return $firstValue * $secondValue;
                }

                if ($operator === '/') {
                    return $firstValue / $secondValue;
                }
            }

            return $firstValue . $secondValue;
        }, $template);
    }

    /**
     * @param $template
     * @param $values
     * @return string
     */
    private function parseDefaultValues($template, $values): string
    {
        return preg_replace_callback('/\{(\S+)\s?\|\s?\"(.+)\"\}/', static function ($matches) use ($values) {
            return isset($values[$matches[1]]) && $matches[1] ? $values[$matches[1]] : $matches[2];
        }, $template);
    }
}
