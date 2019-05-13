<?php

use Laradium\Laradium\Documents\Services\DocumentService;

if (!function_exists('documentColumns')) {

    /**
     * @param $blueprint
     * @param bool $withForeign
     */
    function documentColumns($blueprint, $withForeign = false)
    {
        DocumentService::columns($blueprint, $withForeign);
    }
}
