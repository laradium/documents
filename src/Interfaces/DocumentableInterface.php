<?php

namespace Laradium\Laradium\Documents\Interfaces;

interface DocumentableInterface
{
    /**
     * @return array
     */
    public function getPlaceholders(): array;

    /**
     * @return string
     */
    public function getContent(): string;

    /**
     * @return string
     */
    public function getContentKey(): string;
}
