<?php

namespace Laradium\Laradium\Documents\Interfaces;

interface DocumentableInterface
{
    /**
     * @return array
     */
    public function getPlaceholders(): array;
}
