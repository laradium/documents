<?php

use Laradium\Laradium\Documents\Services\DocumentService;

if (!function_exists('documentColumns')) {

    /**
     * @param $blueprint
     * @param bool $withForeign
     * @return void
     */
    function documentColumns($blueprint, $withForeign = false): void
    {
        DocumentService::columns($blueprint, $withForeign);
    }
}
